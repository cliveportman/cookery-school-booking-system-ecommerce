<?php

namespace Craft;

class EmailsVariable
{

    public function sendNotification($options)
    {
    	
        $emailSent = craft()->quickNotifier->sendNotification($options);
        return "Email sent";

    }

}