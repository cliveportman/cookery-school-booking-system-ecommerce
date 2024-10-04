<?php
namespace Craft;

class EmailsPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Emails for La Cuisine Paris');
    }

    public function getDescription()
    {
        return 'Handles emails sent from the booking system.';
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
    

}