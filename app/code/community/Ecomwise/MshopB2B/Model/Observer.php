<?php 
class Ecomwise_MshopB2B_Model_Observer extends Varien_Object{
	
	/**
	 *
	 * Event listener for "catalog_product_collection_load_after"
	 * It applies individual price rules to a product colection calculating them on run instead taking from database
	 * 
	 */
	public function prepareCollection($observer){
		$data = $observer->getCollection();
		$customer_group = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$storeId = Mage::app()->getStore()->getId();
		$webid =   Mage::getModel('core/store')->load($storeId)->getWebsiteId();
		
		foreach ($data as $product) {
			
			if($product->getTypeId() == "bundle"){
				
				$product->setMinPrice(null);
				$product->setMaxPrice(null);
					
			}else if($product->getTypeId() == "grouped"){
				
				$assoc_products = $product->getTypeInstance(true)->getAssociatedProductIds($product);
				$assoc_prices = Mage::getResourceModel('catalogrule/rule')->getRulePrices(date("Y-m-d"), $webid, $customer_group, $assoc_products);
				$product->setMinimalPrice(min($assoc_prices));
			}else{
				
				$final_price = Mage::getResourceModel('catalogrule/rule')
				->getRulePrice(date("Y-m-d"),$webid,$customer_group,$product->getId());
				
				
				if($final_price){
					$minimalPrice = $product->getMinimalPrice();
					$price = $product->getPrice();
					
					if($final_price < $price){
						$product->setFinalPrice($final_price);
					}
					
					if($final_price < $minimalPrice){
						$product->setMinimalPrice($final_price);
					}
					//$product->setFinalPrice($final_price);
					//$product->setMinimalPrice($final_price);
					//$product->setMaximalPrice($final_price);
				}
						
				
			}
			
		}
	
		return;
	}
	
	/**
	 * 
	 * Event listener for "catalogrule_rule_delete_after". Delete individula customer mappings for rule
	 * @ Object $observer
	 */
	public function afterDeleteRule($observer){
		
	
		$data = $observer->getData();
		$rule = $data['data_object'];
		$rule_id = $rule -> getId();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	
		$resurce = Mage::getSingleton('core/resource');
		$customer_to_rule_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');
	
		$connection->delete($customer_to_rule_table, 'rule_id = ' . $rule_id);
	
		return; 
	
	}
	
} 