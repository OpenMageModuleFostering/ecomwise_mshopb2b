<?php 
$installer = $this;
$installer->startSetup();

/**
 * 
 * Adding  attribute to customer entity 
 * 
 * 
 * 
 */

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

// $setup->removeAttribute( 'customer', 'mshop_bloked' );

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




$installer->endSetup();
