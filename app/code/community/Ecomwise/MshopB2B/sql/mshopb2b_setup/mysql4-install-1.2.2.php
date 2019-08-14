<?php
$installer = $this;

$installer->startSetup();

/**
 * new Mamut status
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

/**
 * 
 * 
 * 
 * Mshop Blocked creation attribute
 * 
 */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

//$setup->removeAttribute( 'customer', 'mshop_bloked' );

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'mshop_bloked', array(
    'input'         => 'text',
    'type'          => 'int',
    'label'         => 'Mshop Blocked',
    'visible'       => 1,
    'required'      => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'mshop_bloked',
 '999'  //sort_order
);

/*
$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mshop_bloked');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();
*/

/* add attribute to order */
//$installer->addAttribute('order', 'reference_order', array('type'=>'varchar'));
 

/**
 * creation of two mshop tables
 * 
 */
$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('ecomwise_catalogpromotions_mapping')};
	CREATE TABLE {$this->getTable('ecomwise_catalogpromotions_mapping')} (
  	`rule_id` int(11) NOT NULL default '0',
  	`mamut_id` int(11) NOT NULL default '0',
  	`mamut_rule_id` int(11) NOT NULL default '0',
  	UNIQUE KEY `ecomwise_rule_mamut_catalog` (`rule_id`,`mamut_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS {$this->getTable('ecomwise_customermamut_mapping')};
	CREATE TABLE {$this->getTable('ecomwise_customermamut_mapping')} (
  	`customer_id` int(11) NOT NULL default '0',
  	`mamut_id` int(11) NOT NULL default '0',
  	UNIQUE KEY `ecomwise_mamut_customer` (`mamut_id`,`customer_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");





$installer->endSetup();




$installer1 = new Mage_Sales_Model_Mysql4_Setup('core_setup');
 
$installer1->startSetup();
 
$installer1->addAttribute('order', 'reference_order', array('type'=>'varchar'));
 
$installer1->endSetup();
