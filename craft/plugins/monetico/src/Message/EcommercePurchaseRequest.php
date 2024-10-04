<?php

namespace Omnipay\Monetico\Message;

use Omnipay\Common\Message\AbstractRequest;

// adding this removed 'class InvalidRequestException missing'
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Monetico Authorize Request
 */
class EcommercePurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://p.monetico-services.com/paiement.cgi';
    protected $testEndpoint = 'https://p.monetico-services.com/test/paiement.cgi';

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getVersion()
    {
        return $this->getParameter('version');
    }
    public function setVersion($value)
    {
        return $this->setParameter('version', $value);
    }

    public function getTPE()
    {
        return $this->getParameter('TPE');
    }
    public function setTPE($value)
    {
        return $this->setParameter('TPE', $value);
    }

    public function getSociete()
    {
        return $this->getParameter('societe');
    }
    public function setSociete($value)
    {
        return $this->setParameter('societe', $value);
    }

    public function getReturnUrlWhatIsThis()
    {
        return $this->getParameter('returnUrlWhatIsThis');
    }
    public function setReturnUrlWhatIsThis($value)
    {
        return $this->setParameter('returnUrlWhatIsThis', $value);
    }

    public function getReturnUrlAfterPayment()
    {
        return $this->getParameter('returnUrlAfterPayment');
    }
    public function setReturnUrlAfterPayment($value)
    {
        return $this->setParameter('returnUrlAfterPayment', $value);
    }

    public function getReturnUrlError()
    {
        return $this->getParameter('returnUrlError');
    }
    public function setReturnUrlError($value)
    {
        return $this->setParameter('returnUrlError', $value);
    }

    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'returnUrl');

        $data = array();

        // not using this but could do
        $sOptions = "";

        // Used only with installments
        $sNbrEch = "";
        $sDateEcheance1 = "";
        $sMontantEcheance1 = "";
        $sDateEcheance2 = "";
        $sMontantEcheance2 = "";
        $sDateEcheance3 = "";
        $sMontantEcheance3 = "";
        $sDateEcheance4 = "";
        $sMontantEcheance4 = "";

        $data['TPE'] = $this->getTPE();

        $data['date'] = date("d/m/Y:H:i:s");
        $data['date_commande'] = date("d/m/Y");

        $data['cartTotal'] = (int) $this->getAmount();

        // ISO 4217 currency
        $data['currency'] = $this->getCurrency();

        // Amount
        $data['montant'] = $data['cartTotal'] . $data['currency'];
        $data['￼￼￼montant_a_capturer'] = $data['montant'];
        $data['montant_deja_capture'] = '0' . $data['currency'];
        $data['montant_restant'] = '0' . $data['currency'];

        // Use transaction hash but could replace with transaction ID
        // when live as it is displayed on the payment page
        // No good using transaction ID in development because
        // cannot use same reference more than once
        $data['reference'] = substr($this->getTransactionId(), 0, 12);

        // Some free text can go in here
        $data['texte-libre'] = $this->getDescription();

        $data['version'] = $this->getVersion();
        $data['lgue'] = 'EN';
        $data['societe'] = $this->getSociete();
        $data['mail'] = $this->getCard()->getEmail();

        $data['endPoint'] = $this->getEndpoint();

        $data['url_retour'] = $this->getReturnUrlWhatIsThis();

				/**
				 * This is the link that the user can click on the payment screen once
				 * its gone through - this should be the same as $transaction->order->returnUrl
				 *
				 * So, we can use the transactionId to get the transaction and voila.
				 */
				$transactionId = $this->getTransactionId();

				// Get the transaction
				$service = new \Craft\Commerce_TransactionsService;
				$transaction = $service->getTransactionById($transactionId);
				if ($transaction)
				{
					// We have a transaction, get the returnUrl off it
					$data['url_retour_ok'] = $this->getReturnUrlAfterPayment() . $transaction->order->number;
				}
				// If for some reason we can’t get the transaction fall back to using
				// the same as the supplied error url
				else
				{
					$data['url_retour_ok'] = $this->getReturnUrlError();
				}

        $data['url_retour_err'] = $this->getReturnUrlError();


				// Set up the Ept and Hmac
				$oEpt = new MoneticoPaiementEpt($data, $this->getSecretKey());
				$oHmac = new MoneticoPaiementHmac($oEpt);


        // Data for creating MAC
        $phase1go_fields = sprintf(_MONETICOPAIEMENT_PHASE1GO_FIELDS,
            $data['TPE'],
            $data['date'],
            $data['cartTotal'],
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

        // Create the MAC
        $mac = $oHmac->computeHmac($phase1go_fields);

        // Return the data in the format they want it
        return array(
          "version"        => $data['version'],
          "TPE"            => $data['TPE'],
          "date"           => $data['date'],
          "montant"        => $data['cartTotal'] . $data['currency'],
          "reference"      => $data['reference'],
          "MAC"            => $mac,
          "url_retour"     => $data['url_retour'],
          "url_retour_ok"  => $data['url_retour_ok'],
          "url_retour_err" => $data['url_retour_err'],
          "lgue"           => $data['lgue'],
          "societe"        => $data['societe'],
          "texte-libre"    => htmlentities($data['texte-libre']),
          "mail"           => $data['mail'],
          "nbrech"         => $sNbrEch,
          "dateech1"       => $sDateEcheance1,
          "montantech1"    => $sMontantEcheance1,
          "dateech2"       => $sDateEcheance2,
          "montantech2"    => $sMontantEcheance2,
          "dateech3"       => $sDateEcheance3,
          "montantech3"    => $sMontantEcheance3,
          "dateech4"       => $sDateEcheance4,
          "montantech4"    => $sMontantEcheance4,
        );

    }

    public function sendData($data)
    {
        return $this->response = new EcommercePurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }
}
