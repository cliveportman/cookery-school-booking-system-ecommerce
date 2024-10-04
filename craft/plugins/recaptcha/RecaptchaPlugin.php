<?php

/*
*
* reCaptcha for Craft Main Plugin File
* Author: Aaron Berkowitz (@asberk)
* https://github.com/aberkie/craft-recaptcha
*
*/

namespace Craft;

class RecaptchaPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('reCAPTCHA for Craft');
    }

    function getVersion()
    {
        return '1.0.1';
    }

    function getDeveloper()
    {
        return 'Aaron Berkowitz';
    }

    function getDeveloperUrl()
    {
        return 'https://github.com/aberkie';
    }

    protected function defineSettings()
    {
        return array(
            'siteKey' => array(AttributeType::Mixed, 'default' => ''),
            'secretKey' => array(AttributeType::Mixed, 'default' => '')
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('recaptcha/settings', array(
            'settings' => $this->getSettings()
        ));
    }

    public function init()
    {
        craft()->on('guestEntries.beforeSave', function (GuestEntriesEvent $event)
        {
            $entryModel = $event->params['entry'];
            $captcha = craft()->request->getPost('g-recaptcha-response');
            $verified = craft()->recaptcha_verify->verify($captcha);
            if (!$verified)
            {
                //Uh oh...its a robot. Don't process this form!
                $entryModel->addError('recaptcha', "recaptcha");
                $event->isValid = false;
                craft()->request->redirect(craft()->getSiteUrl() . 'recaptcha');
            }
            
        });
    }
}
