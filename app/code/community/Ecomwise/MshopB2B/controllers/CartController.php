<?php 
include_once('Mage/Checkout/controllers/CartController.php');

class Ecomwise_MshopB2B_CartController extends Mage_Checkout_CartController{

    
    public function addAction(){
    	
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    	if($customer){
    		$is_blocked = $customer->getMshopBloked();
    		if($customer->getMshopBloked() == 1){
	        	$this->_getSession()->addError($this->__("Your account is blocked from shopping in the webshop! Contact store owners for unblocking your account!"));
	        	$this->_goBack();
	        	return;
    	     }
    	}
    	
    	parent::addAction();
        
    }
    
     public function addgroupAction(){
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    		if($customer){
    			$is_blocked = $customer->getMshopBloked();
    			if($customer->getMshopBloked() == 1){
	        		$this->_getSession()->addError($this->__("Your account is blocked from shopping in the webshop! Contact store owners for unblocking your account!"));
	        		$this->_goBack();
	        		return;
    	     	}
    	}
    	
		parent::addgroupAction();    	
    	
    }
    
}