<?php
namespace Craft;

/**
 * Recaptcha controller
 */
class Recaptcha_ServiceController extends BaseController
{
    protected $allowAnonymous = true;
    /**
     * Handle the save user form request.
     */
    public function actionSaveUser()
    {
        $this->requirePostRequest();
        $captcha = craft()->request->getPost('g-recaptcha-response');
        $verified = craft()->recaptcha_verify->verify($captcha);

        if ($verified) {
            $this->forward('users/saveUser', false);
        } else {
            $user = new UserModel();
            $user->addError('recaptcha', Craft::t("Failed reCAPTCHA validation."));

            // Store given values, so the user don't has to repeat them
            $user->username = craft()->request->getPost('username');
            $user->email = craft()->request->getPost('email');

            // Send the account back to the template
            craft()->urlManager->setRouteVariables(array(
                'account' => $user
            ));
        }
    }
    
    public function actionSendMessage()
    {
        $this->requirePostRequest();
        $captcha = craft()->request->getPost('g-recaptcha-response');
        $verified = craft()->recaptcha_verify->verify($captcha);

        if ($verified) {
            $this->forward('contactForm/sendMessage', false);
        } else {
            $email = new ContactFormModel();
            $email->addError('recaptcha', Craft::t("Failed reCAPTCHA validation."));

            // Store given values, so the user don't has to repeat them
            $email->fromName = craft()->request->getPost('fromName');
            $email->fromEmail = craft()->request->getPost('fromEmail');
            $email->message = craft()->request->getPost('message');
            $email->subject = craft()->request->getPost('subject');

            // Send the account back to the template
            craft()->urlManager->setRouteVariables(array(
                'message' => $email
            ));
        }
    }
}
