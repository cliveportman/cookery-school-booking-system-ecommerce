<?php
namespace Craft;

class QuickNotifierService extends BaseApplicationComponent
{

	// SEND NOTIFICATIONS TO PEOPLE LISTED
	public function sendNotification($options = array()) 
	{

		// SEND THE EMAIL
        $email = new EmailModel();
        $email->toEmail = $options['email'];
        $email->subject = $options['subject'];
        $email->body    = $options['body'];
        craft()->email->sendEmail($email);

	}
}