<?php 
$installer = $this;
$installer->startSetup();

/**
 * 
 * 
 * Adding new order status
 *  
 */

$code = 'mshop_mamut'; 
$label = "Mamut";


$state = "complete";
$is_default = 1;

$status = Mage::getModel('sales/order_status')->load($code);
if(!$status->getStatus()){

	$data = array("is_new"=> 1, "status" => $code, "label" => $label, "store_labels" => Array ());
	$status->setData($data)->setStatus($code);
	try{
	  $status->save();
	}catch(Exception $ex){
	  Mage::log($ex->getMessage());
	
	}
	try{
		 $status->assignState($state, $isDefault);
	
	}catch(Exception $ex){
		Mage::log($ex->getMessage());
	
	}
}

$installer->endSetup(); 



