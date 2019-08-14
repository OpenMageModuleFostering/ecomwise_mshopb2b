<?php
class Ecomwise_MshopB2B_Block_Renderers_GridCatalogRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    
public function render(Varien_Object $row){
		if(version_compare(Mage::getVersion(),'1.7.0.0','>=')){
			return $this->render17($row);
		}else{
			return $this->renderBase($row);
		}
    }

	
	private function render17(Varien_Object $row){
		$customers = array();
		$resource = Mage::getSingleton('core/resource');
		$cr = Mage::getSingleton('core/resource')->getConnection('core_read');
		$cw = Mage::getSingleton('core/resource')->getConnection('core_write');
		$table = $resource->getTableName('catalogrule_customer_group');
		$tableGroups = $resource->getTableName('catalogrule_customer_group');
		
		$query = $cr ->select()
		->from($table, array('customer_group_id'))
		->where('rule_id = ?', $row->getData('rule_id'));
		
		$rows = $cr->fetchCol($query);
		
		$groups ="";
		foreach($rows as $row){
			$group = Mage::getModel('customer/group')->load($row);
			$groups.= $group->getCustomerGroupCode()."</br>";
		}
		
		return $groups;
		
	}
	
	private function renderBase(Varien_Object $row){
		$groups = $row->getData($this->getColumn()->getIndex());
		 
		if ($groups || $groups == '0'){
			 
			$customerpromotions_model = Mage::getModel('mshopb2b/catalogpromotions');
			$customer_groups = $customerpromotions_model->get_customer_groups();
		
			$group_names = array();
			foreach ($groups as $group_id){
				 
				if (isset($customer_groups[$group_id])){
					$group_names[] = $customer_groups[$group_id];
				}
			}
		
			$html = implode(', ', $group_names);
		
			return $html;
		}
		else {
			return ' ';
		}
	}

} 