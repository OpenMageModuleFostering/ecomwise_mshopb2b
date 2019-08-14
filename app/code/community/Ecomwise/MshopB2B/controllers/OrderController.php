<?php 

include_once('Mage/Sales/controllers/OrderController.php');

class Ecomwise_MshopB2B_OrderController extends Mage_Sales_OrderController{
	
 	protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
    
	public function reorderAction(){
    	$customer = Mage::getSingleton('customer/session')->getCustomer();
    	if($customer){
    		
    		$is_blocked = $customer->getMshopBloked();
    		if($customer->getMshopBloked() == 1){
	        	Mage::getSingleton('checkout/session')->addError($this->__("Your account is blocked from shopping in the webshop! Contact store owners for unblocking your account!"));
	        	$this->_goBack();
	        	return;
    	     }
    	 }
    		
        parent::reorderAction();	
    }
}

