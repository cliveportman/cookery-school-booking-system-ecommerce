<?php

namespace Omnipay\Ogone\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Ogone Complete Purchase Request
 */
class EcommerceCompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
      return true;
    }

    public function getTransactionReference()
    {
      // Not sure we can do this for an offsite gateway without modifying
      // the commerce_payments->completePayment method
      return null;
    }

}
