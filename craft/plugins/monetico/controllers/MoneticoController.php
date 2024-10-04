<?php
namespace Craft;

/**
 * Class Commerce_PaymentsController
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @copyright Copyright (c) 2015, Pixel & Tonic, Inc.
 * @license   http://craftcommerce.com/license Craft Commerce License Agreement
 * @see       http://craftcommerce.com
 * @package   craft.plugins.commerce.controllers
 * @since     1.0
 */

use Omnipay\Monetico\Message\MoneticoPaiementEpt;
use Omnipay\Monetico\Message\MoneticoPaiementHmac;

class MoneticoController extends Commerce_BaseFrontEndController
{

	/** @var BaseGatewayAdapter */
	private $_gatewayAdapter;


	/**
	 * This is the Return interface the bank server will POST to
	 *
	 * It should:
	 * - Validate the MAC seal
	 * - If ok, update the status of our copy of the transaction and return ok
	 * - If not, return the error response they want
	 *
	 * The response should be plain text and in the following formats.
	 *
	 * OK:
	 * version=2
	 * cdr=0
	 *
	 * NOT OK:
	 * version=2
	 * cdr=1
	 *
	 *
	 * This is what we can expect in POST or GET
	 * TPE=0000001&date=07%2f10%2f2016_a_14%3a07%3a42&montant=59%2e99EUR&reference=8c974587824a22c12e4edb64b899916e&MAC=83286B59F07D59DBCD62746C18D18B9A6CE1038B&texte-libre=Class%2ftour+booking&code-retour=Annulation&cvx=oui&vld=1219&brand=AM&status3ds=4&motifrefus=Refus&modepaiement=CB
	 *
	 * @throws Exception
	 * @throws HttpException
	 */
	public function actionHandleReturn()
	{

		/**
		 * First up, check the MAC seal
		 */
		$mac = craft()->request->getParam('MAC');

		$transactionId = craft()->request->getParam('reference');

		// Try and get the transaction
		$transaction = craft()->commerce_transactions->getTransactionById($transactionId);

		/**
		 * If we can’t get the transaction, then we can’t get the gateway which we
		 * need to build the MAC data as well as to update the transaction record
		 * and to make the payment in Commerce. I think that qualifies as enough of
		 * a failure to tell the gateway something went wrong.
		 */
		if (!$transaction)
		{
			MoneticoPlugin::log("Couldn’t get transaction.", LogLevel::Error);
			$this->_returnFailure();
		}

		MoneticoPlugin::log("handleReturn called for order ID $transaction->orderId.", LogLevel::Info, true);

		// Get the gateway off that
		$gateway = $transaction->paymentMethod->getGateway();

		// Make a big blob of data combining all the stuff we need to sign the MAC
		// 'endPoint' => 'https://p.monetico-services.com/test/paiement.cgi',
		$data = array(
			'TPE' => craft()->request->getParam('TPE'),
			'date' => craft()->request->getParam('date'),
			'montant' => craft()->request->getParam('montant'),
			'reference' => $transactionId,
			'texte-libre' => craft()->request->getParam('texte-libre'),
			'version' => $gateway->getVersion(),
			'endPoint' => 'https://p.monetico-services.com/paiement.cgi',
			'lgue' => 'EN',
			'societe' => $gateway->getSociete(),
			'url_retour_ok' => $gateway->getReturnUrlAfterPayment(),
			'url_retour_err' => $gateway->getReturnUrlError(),
			'code-retour' => craft()->request->getParam('code-retour'),
			'cvx' => craft()->request->getParam('cvx'),
			'vld' => craft()->request->getParam('vld'),
			'brand' => craft()->request->getParam('brand'),
			'status3ds' => craft()->request->getParam('status3ds'),
			'numauto' => craft()->request->getParam('numauto'),
			'motifrefus' => craft()->request->getParam('motifrefus'),
			'originecb' => craft()->request->getParam('originecb'),
			'bincb' => craft()->request->getParam('bincb'),
			'hpancb' => craft()->request->getParam('hpancb'),
			'ipclient' => craft()->request->getParam('ipclient'),
			'originetr' => craft()->request->getParam('originetr'),
			'veres' => craft()->request->getParam('veres'),
			'pares' => craft()->request->getParam('pares'),
		);

		$oEpt = new MoneticoPaiementEpt($data, $gateway->getSecretKey());
		$oHmac = new MoneticoPaiementHmac($oEpt);

		$phase2back_fields = sprintf(_MONETICOPAIEMENT_PHASE2BACK_FIELDS,
			$data['TPE'],
			$data['date'],
			$data['montant'],
			$data['reference'],
			$data['texte-libre'],
			$data['version'],
			$data['code-retour'],
			$data['cvx'],
			$data['vld'],
			$data['brand'],
			$data['status3ds'],
			$data['numauto'],
			$data['motifrefus'],
			$data['originecb'],
			$data['bincb'],
			$data['hpancb'],
			$data['ipclient'],
			$data['originetr'],
			$data['veres'],
			$data['pares']
		);

		// Create the MAC
		$macToCompare = $oHmac->computeHmac($phase2back_fields);

		// Compare the MAC we got in POST and the one we just made
		if (strtolower($macToCompare) !== strtolower($mac))
		{
			// If they don’t match, then the integrtiy of the data
			// cannot be guaranteed, so we bail and log it.
			MoneticoPlugin::log("MAC didn’t match. Possible fraud attempt.", LogLevel::Info);
			$this->_returnFailure();
		}
		else
		{

			/**
			 * It worked!
			 *
			 * Process the payment after setting the transaction status using
			 * the payment return code (code-retour).
			 *
			 * Whether it was successful or not, we have to return the plaintext
			 * success message because the MAC code matched OK, but here is where
			 * we either mark the transaction as failed, or complete the payment.
			 */

			// Get the return code
			$returnCode = $data['code-retour'];

			// It was successful
			if ($returnCode === 'payetest' || $returnCode === 'paiement')
			{

				// Update the transaction model’s status
				$transaction->status = Commerce_TransactionRecord::STATUS_REDIRECT;

				// Try and complete the order
				$success = craft()->commerce_payments->completePayment($transaction, $customError);

				// If we couldn’t then log an error
				if (!$success)
				{
					MoneticoPlugin::log("Transaction was successful but couldn’t complete the order for order #".$transaction->order->number, LogLevel::Error);
				}
			}
			// Payment failed
			elseif ($returnCode === 'Annulation')
			{
				// Update the transaction model’s status
				$transaction->status = Commerce_TransactionRecord::STATUS_FAILED;

				// Try and update the transaction
				$success = craft()->commerce_transactions->saveTransaction($transaction);

				// If we couldn’t then log an error
				if (!$success)
				{
					MoneticoPlugin::log("Transaction failed but we couldn’t update the transaction for id #".$transaction->id." on order #".$transaction->order->number, LogLevel::Error);
				}
			}

			// Return OK to the gateway regardless of all the above becuase the MAC matched
			$this->_returnSuccess();
		}

	}


	/**
	 * Returns the correct failure message to the gateway.
	 */
	private function _returnFailure()
	{
		HeaderHelper::setHeader(array('Content-Type' => 'text/plain'));
		ob_start();
		echo sprintf(_MONETICOPAIEMENT_PHASE2BACK_RECEIPT, _MONETICOPAIEMENT_PHASE2BACK_MACNOTOK);
		craft()->end();
	}


	/**
	 * Returns the correct success message to the gateway.
	 */
	private function _returnSuccess()
	{
		HeaderHelper::setHeader(array('Content-Type' => 'text/plain'));
		ob_start();
		echo sprintf(_MONETICOPAIEMENT_PHASE2BACK_RECEIPT, _MONETICOPAIEMENT_PHASE2BACK_MACOK);
		craft()->end();
	}

}
