<?php 
class Ecomwise_MshopB2B_Model_Api_Credit_Api extends Mage_Api_Model_Resource_Abstract{
	
	public function block($email, $website_id = null){
		if($email == '' || $email == null){
			$this->_fault("email_not_sent");
		
		}
		
		//return true;
		if($website_id == null ){
			 foreach (Mage::app()->getWebsites() as $website) {
			 	$website_id  = $website->getId(); 
			 	break;
			 }
		}
		
		//return $email;
		
		try{
			$customer = Mage::getModel("customer/customer")->setWebsiteId($website_id)->loadByEmail($email);
		}catch(Exception $ex){
			return  $this->_fault('invalid_data');
		}
		
		if($customer->getEntityId() == null){
			
			$this->_fault("customer_not_exists");
			
		}
		
		
		try{
			$customer->setMshopBloked("1");
			$customer->save();
			
		}catch(Extension $ex){
			$this->_fault("invalid_data");
		
		}	
		
		return true;
	}
	
	public function unblock($email, $website_id = null){
		if($email == '' || $email == null){
			$this->_fault("email_not_sent");
		
		}
		
	   if($website_id == null ){
			 foreach (Mage::app()->getWebsites() as $website) {
			 	$website_id  = $website->getId(); 
			 	break;
			 }
		}
		
		try{
			$customer = Mage::getModel("customer/customer")->setWebsiteId($website_id)->loadByEmail($email);
		}catch(Exception $ex){
			return  $this->_fault('invalid_data');
		}
		
		if($customer->getEntityId() == null){
			$this->_fault("customer_not_exists");
		}
		try{
			$customer->setMshopBloked(0);
			$customer->save();
			return true;
		}catch(Extension $ex){
			$this->_fault($ex->getMessage());
		
		}
	
	}


} 