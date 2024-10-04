<?php
namespace Craft;

class RegistrationPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Registration');
    }

    public function getDescription()
    {
        return 'Uses custom order fields to handle a register system.';
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
        return 'http://cliveportman.co.uk';
    }
    
    
    public function init()
    {
        parent::init();

        /*
        // ON SAVE ASSETS
        craft()->on('assets.saveAsset', function (Event $event) {

            // if it's a new asset, rename it for privacy
            if ($event->params['isNewAsset']) {
                $asset = $event->params['asset'];
                if ($asset->folderId == 3) craft()->academy_invoices->renameAsset($event); 
            }
        });
        */

        craft()->on('commerce_orders.onBeforeOrderComplete', function (Event $event) {
            static $recursionLevel = 0;
            if ($recursionLevel == 0) {
                $recursionLevel++;
                $order = $event->params['order'];
                craft()->registration->sendAutoNotification('Order complete');
                craft()->registration->createOrderRegisters($order);
            }
        });
    }
    

}