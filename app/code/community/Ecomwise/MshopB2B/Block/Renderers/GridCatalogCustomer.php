<?php
class Ecomwise_MshopB2B_Block_Renderers_GridCatalogCustomer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    
	public function render(Varien_Object $row){
    	
    	$customers = Mage::registry('customerpromotions_catalog_rule_customers');
    	$rule_id = $row->getData('rule_id');
    	if ($rule_id && $customers && isset($customers[$rule_id]) ){
            $number_of_customers = 10;
            
            $more_than_limit = false;
            
            if (sizeof($customers[$rule_id]) > $number_of_customers){
                $rule_customers = array_slice($customers[$rule_id], 0, $number_of_customers);
                $more_than_limit = true;
            }
            else {
                $rule_customers = $customers[$rule_id];
            }
            
            $html = implode('<br/>', $rule_customers);
            
            if ($more_than_limit){
                $html .= '<br/>...';
            }
            
            return $html;
            
    	}else{
            return '&nbsp;';
        }
       
    }

} 