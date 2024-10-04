<?php

define ("MONETICOPAIEMENT_KEY", "");
define ("MONETICOPAIEMENT_EPTNUMBER", "");
define ("MONETICOPAIEMENT_VERSION", "3.0");
define ("MONETICOPAIEMENT_URLSERVER", "https://p.monetico-services.com/test/");
define ("MONETICOPAIEMENT_COMPANYCODE", "");
define ("MONETICOPAIEMENT_URLOK", "");
define ("MONETICOPAIEMENT_URLKO", "");

define ("MONETICOPAIEMENT_CTLHMAC","V4.0.sha1.php--[CtlHmac%s%s]-%s");
define ("MONETICOPAIEMENT_CTLHMACSTR", "CtlHmac%s%s");
define ("MONETICOPAIEMENT_PHASE2BACK_RECEIPT","version=2\ncdr=%s");
define ("MONETICOPAIEMENT_PHASE2BACK_MACOK","0");
define ("MONETICOPAIEMENT_PHASE2BACK_MACNOTOK","1\n");
define ("MONETICOPAIEMENT_PHASE2BACK_FIELDS", "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*");
define ("MONETICOPAIEMENT_PHASE1GO_FIELDS", "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s");
define ("MONETICOPAIEMENT_URLPAYMENT", "paiement.cgi");

class MoneticoPaiementEpt {

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

function getMethode()
{
    if ($_SERVER["REQUEST_METHOD"] == "GET")  
        return $_GET; 

    if ($_SERVER["REQUEST_METHOD"] == "POST")
	return $_POST;

    die ('Invalid REQUEST_METHOD (not GET, not POST).');
}

// Order reference, unique, alphaNum (A-Z a-z 0-9), 12 characters max
$sReference = "ref" . date("His");
// Cart total price
$sMontant = 1.01;
// ISO 4217 currency
$sDevise  = "EUR";
$sTexteLibre = "Texte Libre";
//$sDate = date("d/m/Y:H:i:s");
$sLangue = "FR";
$sEmail = "test@test.zz";

// not sure what this is
$sOptions = "";

// Use these if you're accepting payments in installments
$sNbrEch = "";
$sDateEcheance1 = "";
$sMontantEcheance1 = "";
$sDateEcheance2 = "";
$sMontantEcheance2 = "";
$sDateEcheance3 = "";
$sMontantEcheance3 = "";
$sDateEcheance4 = "";
$sMontantEcheance4 = "";

$oEpt = new MoneticoPaiementEpt($sLangue);     		
$oHmac = new MoneticoPaiementHmac($oEpt);      	        

// Control String for support
$CtlHmac = sprintf(MONETICOPAIEMENT_CTLHMAC, $oEpt->sVersion, $oEpt->sNumero, $oHmac->computeHmac(sprintf(MONETICOPAIEMENT_CTLHMACSTR, $oEpt->sVersion, $oEpt->sNumero)));

$data['TPE'] = '0000001';
$data['date'] = date("d/m/Y:H:i:s");
$data['montant'] = $sMontant;
$data['currency'] = $sDevise;
$data['reference'] = $sReference;
$data['texte-libre'] = $sTexteLibre;
$data['version'] = '3.0';
$data['lgue'] = $sLangue;
$data['societe'] = '1b1af5d81d11f1a02fcf';
$data['mail'] = $sEmail;

// Data to certify
$phase1go_fields = sprintf(MONETICOPAIEMENT_PHASE1GO_FIELDS,
	$data['TPE'],
    $data['date'],
    $data['montant'],
	$data['currency'],
    $data['reference'],
    $data['texte-libre'],
    $data['version'],
    $data['lgue'],
    $data['societe'],
    $data['mail'],
	$sNbrEch,
	$sDateEcheance1,
	$sMontantEcheance1,
	$sDateEcheance2,
	$sMontantEcheance2,
	$sDateEcheance3,
	$sMontantEcheance3,
	$sDateEcheance4,
	$sMontantEcheance4,
	$sOptions
);

// MAC computation
$sMac = $oHmac->computeHmac($phase1go_fields);



print_r($phase1go_fields);
echo "'" . $sMac . "'";



        $data = array();

        // MONETICO PAIEMENT
        $data['TPE'] = '3.0';
        $data['date'] ='29/07/2016:10:10:50';
        $data['montant'] = '1.01EUR';
        $data['reference'] = 'ref101050';
        $data['texte-libre'] = 'Texte Libre';
        $data['version'] = '0000001';
        $data['lgue'] = 'FR';
        $data['societe'] = '1b1af5d81d11f1a02fcf';
        $data['mail'] = 'test@test.zz';

        $string = '3.0*29/07/2016:10:10:50*1.01EUR*ref101050*Texte Libre*0000001*FR*1b1af5d81d11f1a02fcf*test@test.zz';

        //echo '089ab5c0c1d3ba97939c978ac580b8c2e3eb5ec0 - text mac<br>';
        //echo '089ab5c0c1d3ba97939c978ac580b8c2e3eb5ec0 - calc mac';
        $data['MAC'] = '089ab5c0c1d3ba97939c978ac580b8c2e3eb5ec0';
        $data['url_retour'] = 'http://dev.lacuisineparis/checkout/fail';
        $data['url_retour_ok'] = 'http://dev.lacuisineparis/checkout/ok';
        $data['url_retour_err'] = 'http://dev.lacuisineparis/checkout/fail';






?>