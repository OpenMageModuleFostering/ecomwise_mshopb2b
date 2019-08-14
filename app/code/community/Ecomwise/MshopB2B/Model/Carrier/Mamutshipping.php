<?php 
class Ecomwise_Mshopb2b_Model_Carrier_Mamutshipping
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'mamut_shipping';
    protected $_isFixed = false;
 
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
 
    	if (!(Mage::app()->getStore()->isAdmin())) {
			return;
		} 

    	
    	$result = Mage::getModel('shipping/rate_result');
 
        $method = Mage::getModel('shipping/rate_result_method');
        $method->setCarrier('mamut_shipping');
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('mamut_shipping');
        $method->setMethodTitle($this->getConfigData('name'));
        
    	
        
        $method->setPrice(0);
        $method->setCost(0);
 
        $result->append($method);
 
        return $result;
    }
 
    public function getAllowedMethods()
    {
        return array('mamut_shipping' => $this->getConfigData('name'));
    }
}