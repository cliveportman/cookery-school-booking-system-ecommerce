<?php

namespace Omnipay\Monetico;

use Omnipay\Common\AbstractGateway;
use Omnipay\Monetico\Message\AuthorizeRequest;


/**
 * OGone Class
 * http://payment-services.ingenico.com/ogone/support/guides/integration%20guides/e-commerce
 */
class EcommerceGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Monetico Paiement Nov 2019';
    }

    public function getDefaultParameters()
    {
        // adding default parameters here adds them to the settings form
        return array(
            'secretKey' => '',
            'version' => '',
            'TPE' => '',
            'societe' => '',
            'returnUrlWhatIsThis' => '',
            'returnUrlAfterPayment' => '',
            'returnUrlError' => '',
            'testMode' => false,
        );
    }

    // not sure why these exist
    // removing them, the payment form seems ok
    // and they're duplicated (both get and set) in EcommerecPurchaseRequest.php
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

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Monetico\Message\EcommercePurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Monetico\Message\EcommerceCompletePurchaseRequest', $parameters);
    }
}
