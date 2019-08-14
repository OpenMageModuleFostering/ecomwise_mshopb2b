<?php 
class Ecomwise_MshopB2B_Block_Renderers_mamutIDRenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
	
	public function render(Varien_Object $row){
		$customer_id = $row->getData('entity_id');
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		$resurce = Mage::getSingleton('core/resource');
		$mapping_table = $resurce->getTableName('ecomwise_customermamut_mapping');
		$query    = $connection  ->select()
                                 ->from($mapping_table, array('mamut_id'))
                                 ->where('customer_id = ?', $customer_id)
                                 ->limit(1);
        $row_mamut = $connection->fetchRow($query);
        if($row_mamut){
        	return $row_mamut['mamut_id'];
        
        }else{
        	return 'NULL';
        }                        
		
	}
}

