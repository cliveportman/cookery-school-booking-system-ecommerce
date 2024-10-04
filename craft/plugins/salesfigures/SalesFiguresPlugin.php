<?php
namespace Craft;

/**
 * SalesFigures plugin
 *
 * @author    Clive Portman <clive@cliveportman.co.uk>
 * @copyright Copyright (c) forever, Clive Portman.
 * @license   MIT
 * @version   0.1
 */

class SalesFiguresPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('Sales Figures');
    }

    function getDescription()
    {
        return Craft::t('Returns total sales of purchasable within a date range.');
    }

    function getVersion()
    {
        return '0.1';
    }

    function getDeveloper()
    {
        return 'Clive Portman';
    }

    function getDeveloperUrl()
    {
        return 'https://cliveportman.co.uk';
    }

}
