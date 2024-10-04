<?php

namespace Craft;

define ("_MONETICOPAIEMENT_CTLHMAC","V4.0.sha1.php--[CtlHmac%s%s]-%s");
define ("_MONETICOPAIEMENT_CTLHMACSTR", "CtlHmac%s%s");
define ("_MONETICOPAIEMENT_PHASE2BACK_RECEIPT","version=2\ncdr=%s\n");
define ("_MONETICOPAIEMENT_PHASE2BACK_MACOK","0");
define ("_MONETICOPAIEMENT_PHASE2BACK_MACNOTOK","1");
define ("_MONETICOPAIEMENT_PHASE2BACK_FIELDS", "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*");
define ("_MONETICOPAIEMENT_PHASE1GO_FIELDS", "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s");

class MoneticoPlugin extends BasePlugin
{

    private $commerceInstalled = false;

    public function init()
    {
        $commerce = craft()->db->createCommand()
            ->select('id')
            ->from('plugins')
            ->where("class = 'Commerce'")
            ->queryScalar();
        if($commerce){
            $this->commerceInstalled = true;
        }
    }

    public function getName()
    {
        return "Monetico Paiement Commerce Gateway";
    }

    /**
     * Returns the plugin’s version number.
     *
     * @return string The plugin’s version number.
     */
    public function getVersion()
    {
        return "1.2";
    }

    /**
     * Returns the plugin developer’s name.
     *
     * @return string The plugin developer’s name.
     */
    public function getDeveloper()
    {
        return "Clive Portman";
    }

    /**
     * Returns the plugin developer’s URL.
     *
     * @return string The plugin developer’s URL.
     */
    public function getDeveloperUrl()
    {
        return "https://clive.theportman.co/";
    }

    public function commerce_registerGatewayAdapters(){
        if($this->commerceInstalled) {
            require __DIR__ . '/vendor/autoload.php';
            require_once __DIR__.'/Monetico_GatewayAdapter.php';
            return ['\Commerce\Gateways\Omnipay\Monetico_GatewayAdapter'];
        }
        return [];
    }
}
