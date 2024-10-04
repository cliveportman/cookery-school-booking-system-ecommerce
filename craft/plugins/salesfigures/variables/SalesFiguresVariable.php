<?php

namespace Craft;

class SalesFiguresVariable
{

    public function getSalesByProduct($options = array()) {

    	// REQUIRED OPTIONS ARE startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $sales = craft()->salesFigures->getSalesByProductIds($options);
        return $sales;

    }

    public function getTotalSales($options = array()) {

    	// REQUIRED OPTIONS ARE startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $salesTotal = craft()->salesFigures->getTotalSales($options);
        return $salesTotal;

    }

}