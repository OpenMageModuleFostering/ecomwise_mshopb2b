<?php 
class Ecomwise_MshopB2B_Block_Renderers_GridCatalogFilterCustomerGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select{
	
	public function getCondition(){
		if (is_null($this->getValue())) {
			return null;
		}
		return array('finset' => $this->getValue());
    }
    

} 


?>