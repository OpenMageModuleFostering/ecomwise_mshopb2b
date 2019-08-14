<?php 
$installer = $this;
$installer->startSetup();


/**
 * creation of mamut contact groups table
 * 
 */

$installer->run("
	DROP TABLE IF EXISTS {$this->getTable('ecomwise_mamut_contact_groups')};
	CREATE TABLE {$this->getTable('ecomwise_mamut_contact_groups')} (
  	`group_id` int(11) NOT NULL AUTO_INCREMENT,
  	`mamut_contact_group_name` varchar(255) NOT NULL default '0',
  	PRIMARY KEY(group_id)
  	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  	INSERT INTO {$this->getTable('ecomwise_mamut_contact_groups')} (`mamut_contact_group_name`) VALUES('MAMUT NOT LOGGED IN')
");


/**
 * 
 * 
 * 
 * mamut contacts groups customer attribute
 * 
 */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

//$setup->removeAttribute( 'customer', 'mshop_bloked' );

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'mamut_contact_groups', array(
    'label'         => 'Mamut Contact Groups',
	'type'          => 'text',
   	'input'         => 'multiselect',
    'backend'           => 'mshopb2b/customerattribute_backend_mamutgroups',
    'input_renderer'    => 'mshopb2b/customerattribute_helper_mamutgroups',
    'source'            => 'mshopb2b/customerattribute_source_mamutgroups',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'visible_in_advanced_search' => false,
    'unique'            => false
    
));


$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'mamut_contact_groups',
 '999'  //sort_order
);


$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'mamut_contact_groups');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->setData('sort_order', 999);
$oAttribute->save();


$installer->endSetup(); 

