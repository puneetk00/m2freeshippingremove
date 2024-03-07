<?php

namespace Pos\CustomShipping\Plugin\Magento\Shipping\Model;

class Shipping
{
    protected $product;
 
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $product
    ) {
        $this->product = $product; 
    }
 
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        $logger->info('Code : '.print_r($carrierCode,true));
        
        $noFreeShipping = false;
        $allItems = $request->getAllItems();
         
        foreach ($allItems as $item){    
            $_product = $this->product->create()->load($item->getProduct()->getId());
            //$logger->info('product atr set : '.print_r($_product->getAttributeSetId(),true));
            if ($_product->getAttributeSetId() == 31) { //here you can check your attribute value
                $noFreeShipping = true;
                break;
            }
        }
        if ($noFreeShipping && $carrierCode == 'freeshipping') {
            return false;
        }
        $result = $proceed($carrierCode, $request);
        return $result;
    }
}
