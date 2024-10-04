<?php
namespace Craft;

/**
 * Product Dates fieldtype
 *
 * @author    Clive Portman <clive@cliveportman.co.uk>
 * @copyright Copyright (c) 2016, Clive Portman.
 * @license   MIT
 * @version   0.1
 */

class ProductsCalendar_ProductDatesFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Product Dates field');
    }

    // we may store in json with more then default 255 chars, so lets use MySQL text column
    public function defineContentAttribute()
    {
      return array(AttributeType::String, 'column' => ColumnType::Text);
    }

    protected function defineSettings()
    {
        return array(
            'minDates' => array(AttributeType::Number, 'default' => '0'),
            'maxDates' => array(AttributeType::Number, 'default' => null),
        );
    }

    public function getSettingsHtml()
    {
      return craft()->templates->render('productscalendar/settings', array(
          'settings' => $this->getSettings()
      ));
    }

    public function prepValueFromPost($value)
    {
      // Modify $value here before saving to db, keep json as its send
      if (isset($value['date'])) {
        $value = $value['date'];
      }

      return $value;
    }

    public function prepValue($value)
    {
      $dates = array();
      @$dates = json_decode($value);
      $dd = array();
      if (is_array($dates)) {
        foreach ($dates as $date) {
          $dd[] = ($date == null) ? null : DateTime::createFromString($date, craft()->getTimeZone());
        }
      }

      $out_value = array('json' => $value, 'dates' => $dd);
      return $out_value;
    }

    public function getInputHtml($name, $value)
    {
      // Reformat the input name into something that looks more like an ID
      $id = craft()->templates->formatInputId($name);

      // Figure out what that ID is going to look like once it has been namespaced
      $namespacedId = craft()->templates->namespaceInputId($id);
      $html = craft()->templates->render('productscalendar/input', array(
            'name'  => $name,
            'value' => $value,
            'settings' => $this->getSettings(),
            'id'    => $id
      ));

        return $html;
    }

}
