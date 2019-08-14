<?php 
class Ecomwise_MshopB2B_Block_Renderers_companyRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	
	public function render(Varien_Object $row){
		$customer_id = $row->getData('entity_id');
		
		$customer = Mage::getModel('customer/customer')->load($customer_id);
		$customerAddressId = $customer->getDefaultBilling();
		if ($customerAddressId){
       		$address = Mage::getModel('customer/address')->load($customerAddressId);
       		return $address->getData('company');
      	}else{
      		return null;
      	}
		
		             
		
	}
}

