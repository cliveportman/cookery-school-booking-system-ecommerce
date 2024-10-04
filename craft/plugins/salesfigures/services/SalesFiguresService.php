<?php

namespace Craft;

class SalesFiguresService extends BaseApplicationComponent
{

    public function getSalesByProductIds($options)
    {

    	// REQUIRES startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];
        $productIds = $options['productIds'];

        // START BY GETTING THE PRODUCTS LINKED TO THE CLASS
        $variantIds = craft()->salesFigures->getVariants($productIds);

        // GET THE ORDERS FOR THE PERIOD IN QUESTION
        $orders = craft()->salesFigures->getOrdersWithinRange($startDate, $endDate, $variantIds);

        $numberOfSales = 0;
        $valueOfSales = 0;
        $unsoldSpaces = 0;

        // WHEN A LINEITEM HAS THE QTY REDUCED USING THE STOCKREPLENISHER PLUGIN
        // WE ADD A NOTE 'ITEM CANCELLED' BUT WE HAVE NO RECORD OF THE ORIGINAL QTY.
        // THE LINEITEM TOTAL IS UPDATED SO CAN BE COMPARED WITH THE
        // ORDER TOTAL (THAT ISN'T UPDATED) TO FIND THE VALUE OF THE CANCELLATIONS
        // BUT FINDING THE NUMBER OF CANCELLATIONS WILL BE UNRELIABLE WHEN MORE THAN
        // ONE LINE ITEM IS UPDATE
        // RECOMMEND CREATING A CUSTOM ORDER FIELD CALLED 'CANCELLATIONS' THAT STORES
        // A JSON OBJECT OR TABLE CONTAINING DETAILS OF A CANCELLATION, E.G.
        // ONE ROW PER CANCELLATION REQUEST WITH LINEITEM ID, PURCHASABLE ID, NUMBER CANCELLED, VALUE CANCELLED
        // THIS COULD BE MANUALLY UPDATED FOR PREVIOUS CANCELLATIONS

        // LOOP THROUGH EACH LINEITEM IN EACH ORDER
        foreach ($orders as $order) {
        foreach ($order->getLineItems() as $lineItem) {
            if (in_array($lineItem->purchasableId, $variantIds)) {
                $numberOfSales = $numberOfSales + $lineItem->qty;
                $unsoldSpaces = $unsoldSpaces + $lineItem->purchasable->stock;
                $valueOfSales = $valueOfSales + $lineItem->total;
            }
        }
        }

        // PREPARE THE RETURN OBJECT
        $returnObject = new \stdClass;
        $returnObject->totalSalesNumber = $numberOfSales;
        $returnObject->totalSalesValue = $valueOfSales;
        $returnObject->unsoldSpaces = $unsoldSpaces;
        $returnObject->variantIds = $variantIds;
        $returnObject->orders = $orders;

        return $returnObject;
                
    }

    public function getVariants($productIds) {

        // GET THE PRODUCTS
        $criteria = craft()->elements->getCriteria('Commerce_Product');
        $criteria->id = $productIds;
        $criteria->limit = NULL;
        $products = $criteria->find();

        $variantIds = [];
        foreach ($products as $product) {
            array_push($variantIds, $product->defaultVariant->id);
        }

        return $variantIds;  

    }

    public function getOrdersWithinRange($startDate, $endDate, $variantIds) {
        

        $criteria = craft()->elements->getCriteria('Commerce_Order');
        //$criteria->dateOrdered = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $criteria->hasPurchasables = $variantIds;
        $criteria->orderStatus = 1;
        $criteria->limit = NULL;
        $orders =  $criteria->find();
        //throw new Exception(count($orders));
        return $orders;  

    }

    public function getTotalSales($options)
    {

        // REQUIRES startDate AND endDate, FORMATTED AS YYYY-MM-DD
        $startDate = $options['startDate'];
        $endDate = $options['endDate'];

        // GET ALL THE ORDERS WITHIN THE TWO DATES
        $criteria = craft()->elements->getCriteria('Commerce_Order');
        $criteria->dateOrdered = array('and', '>= ' . $startDate, '<= ' . $endDate);
        $criteria->orderStatus = 1;
        $criteria->limit = NULL;
        $orders =  $criteria->find();

        $salesTotal = 0;
        foreach ($orders as $order) {
            $salesTotal = $salesTotal + $order->totalPrice;
        }

        return $salesTotal;

    }

}