<?php

namespace Omnipay\Ogone\Message;

class MoneticoPaiementEpt {

    public $sVersion;   // Version du TPE - EPT Version (Ex : 3.0)
    public $sNumero;    // Numero du TPE - EPT Number (Ex : 1234567)
    public $sCodeSociete;   // Code Societe - Company code (Ex : companyname)
    public $sLangue;    // Langue - Language (Ex : FR, DE, EN, ..)
    public $sUrlOK;     // Url de retour OK - Return URL OK
    public $sUrlKO;     // Url de retour KO - Return URL KO
    public $sUrlPaiement;   // Payment Server URL (eg https://p.monetico-services.com/paiement.cgi)

    private $_sCle;     // La cle - The Key 
    
    function __construct($data, $secretKey) {

        //$aRequiredConstants = array();
        //$this->_checkEptParams($aRequiredConstants);

        $this->sVersion = $data['version'];
        $this->_sCle = $secretKey;
        $this->sNumero = $data['TPE'];
        $this->sUrlPaiement = $data['endPoint'];

        $this->sCodeSociete = $data['societe'];
        $this->sLangue = $data['lgue'];

        $this->sUrlOK = $data['url_retour_ok'];
        $this->sUrlKO = $data['url_retour_err'];

    }

    public function getCle() {

        return $this->_sCle;
    }

    private function _checkEptParams($aConstants) {

        for ($i = 0; $i < count($aConstants); $i++)
            if (!defined($aConstants[$i])) die ("Error: " . $aConstants[$i]);
    }

}
