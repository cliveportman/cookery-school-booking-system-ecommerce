<?php
namespace Craft;

class QuickNotifierPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Quick Notifier');
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