<?php 
/**
 * 
 * 
 * Catalog rule edit tab form overide.
 * Mamut ID field is added
 * @author 
 *
 */
class Ecomwise_MshopB2B_Block_Adminhtml_EcomwisePromoCatalogEditTab extends Mage_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main{

	protected function _prepareForm(){
    	$parent = parent::_prepareForm();
    	 
    	$parent_form =  $parent->getForm();

        return $parent_form;
    }
    
    protected function _toHtml(){
        $parent_html = parent::_toHtml();
        return $parent_html;
    }
    
    public function getRuleCustomers(){
    	    
    	$customers_rows = array();
    	$current_rule = Mage::registry('current_promo_catalog_rule');        
        
       if ($current_rule->getId()){
            $main_resurce = Mage::getSingleton('core/resource');
    		$rules_table = $main_resurce->getTableName('ecomwise_catalogpromotions_mapping');        
    
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        
            $query = $connection ->select()
                                ->from($rules_table, array('customer_id'))
                                ->where('rule_id = '. $current_rule->getId());
            
            $customers_rows = $connection->fetchCol($query);       
        }
        
        $customers_list = array();
        
        if ($customers_rows){
           $customer_collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToFilter('entity_id', $customers_rows) 
                ->load();
                                    
            foreach ($customer_collection as $customer){
                $customer_name = $customer->getName() . ' - ' . $customer->getEmail();
                $customers_array = array('value'=>$customer->getId(),'label'=>$customer_name); 
                $customer_list[] = $customers_array;
            }
        }
        
        return $customer_list;
    }  
}