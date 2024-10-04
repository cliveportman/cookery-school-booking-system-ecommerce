<?php
namespace Craft;

class Registration_ParticipantsController extends BaseController
{

	protected $allowAnonymous = true;

    public function actionUpdateParticipants()
    {
        $this->requirePostRequest();
        
        $vars = [];
        $vars['orderId'] = craft()->request->getParam('orderId');
        $vars['products'] = craft()->request->getParam('products');
        if( craft()->registration->updateParticipants($vars) ) {
            $this->redirectToPostedUrl();
        }
    }

    public function actionUpdateAttendance()
    {
        $this->requirePostRequest();
        
        $vars = [];
        $vars['product'] = craft()->request->getParam('product');
        if( craft()->registration->updateAttendance($vars) ) {
            $this->redirectToPostedUrl();
        }
    }

}