<?php
namespace Craft;

class EmailsPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Emails');
    }

    public function getDescription()
    {
        return 'Quickly send an email within a template.';
    }

    function getVersion()
    {
        return '1.0';
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