<?php

namespace Craft;

class RegistrationVariable
{

    public function sendNotification($options)
    {
    	
        $emailSent = craft()->registration->sendNotification($options);
        return "Email sent";

    }

}