<?php

namespace Craft;

class QuickNotifierVariable
{

    public function sendNotification($options)
    {
    	
        $emailSent = craft()->quickNotifier->sendNotification($options);
        return "Email sent";

    }

}