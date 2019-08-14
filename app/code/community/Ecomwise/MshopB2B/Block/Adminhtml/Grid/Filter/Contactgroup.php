<?php
class Ecomwise_MshopB2B_Block_Adminhtml_Grid_Filter_Contactgroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    public function getCondition()
    {
    	$resurce = Mage::getSingleton('core/resource');
	    $customer_entity_text = $resurce->getTableName('customer_entity_text');
	    $mamut_contact_groups = $resurce->getTableName('ecomwise_mamut_contact_groups');
    	$cw = $resurce->getConnection('core_write');
    	
	    $collection = Mage::registry('customer_c');
    	
	    $result = $cw->fetchRow("SELECT mamut_contact_group_name FROM ".$mamut_contact_groups."
	    	WHERE group_id =".$this->getValue()."");
	    
	    $collection->addAttributeToFilter('mamut_contact_groups', array('like' => '%'.$result['mamut_contact_group_name'].'%'));
	    
    	return null;
    }
}