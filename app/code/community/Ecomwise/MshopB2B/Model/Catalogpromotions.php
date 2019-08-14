<?php 

class Ecomwise_MshopB2B_Model_Catalogpromotions extends Mage_Eav_Model_Entity_Attribute{
	
	public function get_customer_groups(){
		
		$notlogged = false;
		
	    $customer_groups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        
        foreach ($customer_groups as $group) {
            if ($group['value']==0) {
                $notlogged = true;
            }
        }
        if (!$notlogged) {
            array_unshift($customer_groups, array('value'=>0, 'label'=>Mage::helper('salesrule')->__('NOT LOGGED IN')));
        }
        
        
        $customer_groups_array = array();
            
        if ($customer_groups){
             foreach ($customer_groups as $group){
                    $customer_groups_array[$group['value']] = $group['label'];
             }
        }
            
        return $customer_groups_array;
    }
	
	public function get_customers(){
		
		$customers = array();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$resurce = Mage::getSingleton('core/resource');
		$customer_to_rule_table = $resurce->getTableName('ecomwise_customermamut_mapping');        

      
      
        $query    = $connection  ->select()
                                 ->from($customer_to_rule_table, array('customer_id'))
                                 ->group('customer_id');
        
        $customer_rows = $connection->fetchCol($query);       
      
        if ($customer_rows) {
            $customer_collection = Mage::getResourceModel('customer/customer_collection')
                ->addNameToSelect()
                ->addAttributeToFilter('entity_id', $customer_rows)
                ->load();
                                    
            foreach ($customer_collection as $customer){
                $name = $customer->getName() . ' - ' . $customer->getEmail();
                $customers[$customer->getId()] = $name;
            }
        }
        
        return $customers;
    
    }
    
    public function getMamutId($rule_id = null){
  		if($rule_id != null){
	   		$resurce = Mage::getSingleton('core/resource');
        	$mapping_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');        
        	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	
			$result = $connection->query('SELECT * FROM '.$mapping_table.'  
						                       WHERE rule_id = '.$rule_id.' 
						             LIMIT 1');	
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            if($row){
            	return  $row['mamut_id'];
            }else{
            	return false;
            }
            
  	   }else{
  			return false;
  	   }
			
	}
	
	
}