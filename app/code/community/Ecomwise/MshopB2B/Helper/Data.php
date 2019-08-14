<?php 
class Ecomwise_MshopB2B_Helper_Data extends Mage_Adminhtml_Helper_Data{


	public function log($method, $varname, $data){
	
		Mage::log("---------------------------------------------------------------------------------", null, "mshopb2b.log");
		
		Mage::log("Method name: ", null, "mshopb2b.log");
		Mage::log($method, null, "mshopb2b.log");
		Mage::log($varname, null, "mshopb2b.log");
		Mage::log($data, null, "mshopb2b.log");
		
		Mage::log("---------------------------------------------------------------------------------", null, "mshopb2b.log");
	
	}
	
	public function getRulesForCustomer($customer_id){
		
		$rule_ids = array();
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$resurce = Mage::getSingleton('core/resource');
		$mapping_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');
		
		$result_mamut_id = $connection->fetchRow("select mamut_id from ".$resurce->getTableName('ecomwise_customermamut_mapping')."
															where customer_id=?", $customer_id);
		$result_rule_id = $connection->fetchAll("select rule_id from ".$resurce->getTableName('ecomwise_catalogpromotions_mapping')."
															where mamut_id=?", $result_mamut_id['mamut_id']);
			
		foreach($result_rule_id as $row){
			$rule_ids[] = $row['rule_id'];
		}
		
		
		return $rule_ids;
	}
}