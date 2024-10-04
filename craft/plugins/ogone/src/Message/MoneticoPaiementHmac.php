<?php

namespace Omnipay\Ogone\Message;

class MoneticoPaiementHmac {

    private $_sUsableKey; //The usable TPE key

    function __construct($oEpt) {       
        $this->_sUsableKey = $this->_getUsableKey($oEpt);
    }

    private function _getUsableKey($oEpt){

        $hexStrKey  = substr($oEpt->getCle(), 0, 38);
        $hexFinal   = "" . substr($oEpt->getCle(), 38, 2) . "00";
    
        $cca0=ord($hexFinal); 

        if ($cca0>70 && $cca0<97) 
            $hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
        else { 
            if (substr($hexFinal, 1, 1)=="M") 
                $hexStrKey .= substr($hexFinal, 0, 1) . "0"; 
            else 
                $hexStrKey .= substr($hexFinal, 0, 2);
        }

        return pack("H*", $hexStrKey);
    }

    public function computeHmac($sData) {

        return strtolower(hash_hmac("sha1", $sData, $this->_sUsableKey));
    }

}
