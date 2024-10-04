<?php

class MoneticoPaiement_Ept {

	public $sVersion;	// Version du TPE - EPT Version (Ex : 3.0)
	public $sNumero;	// Numero du TPE - EPT Number (Ex : 1234567)
	public $sCodeSociete;	// Code Societe - Company code (Ex : companyname)
	public $sLangue;	// Langue - Language (Ex : FR, DE, EN, ..)
	public $sUrlOK;		// Url de retour OK - Return URL OK
	public $sUrlKO;		// Url de retour KO - Return URL KO
	public $sUrlPaiement;	// Payment Server URL (eg https://p.monetico-services.com/paiement.cgi)

	private $_sCle;		// La cle - The Key	
	
	function __construct($sLangue = "FR") {

		$aRequiredConstants = array('MONETICOPAIEMENT_KEY', 'MONETICOPAIEMENT_VERSION', 'MONETICOPAIEMENT_EPTNUMBER', 'MONETICOPAIEMENT_COMPANYCODE');
		$this->_checkEptParams($aRequiredConstants);

		$this->sVersion = MONETICOPAIEMENT_VERSION;
		$this->_sCle = MONETICOPAIEMENT_KEY;
		$this->sNumero = MONETICOPAIEMENT_EPTNUMBER;
		$this->sUrlPaiement = MONETICOPAIEMENT_URLSERVER . MONETICOPAIEMENT_URLPAYMENT;

		$this->sCodeSociete = MONETICOPAIEMENT_COMPANYCODE;
		$this->sLangue = $sLangue;

		$this->sUrlOK = MONETICOPAIEMENT_URLOK;
		$this->sUrlKO = MONETICOPAIEMENT_URLKO;

	}

	public function getCle() {

		return $this->_sCle;
	}

	private function _checkEptParams($aConstants) {

		for ($i = 0; $i < count($aConstants); $i++)
			if (!defined($aConstants[$i])) die ("Error: " . $aConstants[$i]);
	}

}

class MoneticoPaiement_Hmac {

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

function getMethode()
{
    if ($_SERVER["REQUEST_METHOD"] == "GET")  
        return $_GET; 

    if ($_SERVER["REQUEST_METHOD"] == "POST")
	return $_POST;

    die ('Invalid REQUEST_METHOD (not GET, not POST).');
}

// ----------------------------------------------------------------------------
// function HtmlEncode
//
// IN:  chaine a encoder / String to encode
// OUT: Chaine encod�e / Encoded string
//
// Description: Encode special characters under HTML format
//                           ********************
//              Encodage des caract�res sp�ciaux au format HTML
// ----------------------------------------------------------------------------
function HtmlEncode ($data)
{
    $SAFE_OUT_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890._-";
    $encoded_data = "";
    $result = "";
    for ($i=0; $i<strlen($data); $i++)
    {
        if (strchr($SAFE_OUT_CHARS, $data{$i})) {
            $result .= $data{$i};
        }
        else if (($var = bin2hex(substr($data,$i,1))) <= "7F"){
            $result .= "&#x" . $var . ";";
        }
        else
            $result .= $data{$i};
            
    }
    return $result;
}
?>
