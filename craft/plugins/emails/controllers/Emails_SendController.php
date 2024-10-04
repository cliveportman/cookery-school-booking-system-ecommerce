<?php
namespace Craft;

class Emails_SendController extends BaseController
{

    //protected $allowAnonymous = true;

    // SENDS EMAILS TO ALL CUSTOMERS ATTENDING A CLASS
    public function actionSendAll(array $vars = [])
    {

        $this->requirePostRequest();

        
        $vars['subject'] = craft()->request->getParam('subject');
        //$vars['greeting'] = craft()->request->getParam('greeting');
        $vars['message'] = craft()->request->getParam('message');
        //$vars['signature'] = craft()->request->getParam('signature');
        $vars['customers'] = craft()->request->getParam('customers');
        $vars['entryIds'] = craft()->request->getParam('entryIds');
        $vars['user'] = craft()->userSession->getUser();
        EmailsPlugin::log($vars['entryIds'], LogLevel::Info, true);

        if (craft()->emails->sendAll($vars, craft()->userSession->getUser())) {

            $this->redirectToPostedUrl();

        } else {      

            $error = Craft::t('The email could not be sent: ') . print_r($vars);
            EmailsPlugin::log($error, LogLevel::Error);
            throw new Exception($error);

        }

    }

    // SENDS EMAILS TO ONE CUSTOMER ATTENDING A CLASS
    public function actionSendSingle(array $vars = [])
    {

    }

}