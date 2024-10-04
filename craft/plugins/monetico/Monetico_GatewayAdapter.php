<?php
namespace Commerce\Gateways\Omnipay;

use Commerce\Gateways\OffsiteGatewayAdapter;

class Monetico_GatewayAdapter extends OffsiteGatewayAdapter
{
    public function handle()
    {
        return "Monetico_Ecommerce";
    }
}
