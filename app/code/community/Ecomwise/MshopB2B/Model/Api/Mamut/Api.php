<?php 
class Ecomwise_MshopB2B_Model_Api_Mamut_Api extends Mage_Api_Model_Resource_Abstract{
	
	protected $resource;
	protected $reader;
	protected $writer;
	
	protected function construct(){
		$this->resource = Mage::getSingleton('core/resource');
		$this->reader  = $this->resource->getConnection('core_read');	
		$this->writer  = $this->resource->getConnection('core_write');
	}
	
	public function setMamutId($mamutId, $customerId){
		$this->construct();

		if($mamutId == 0 or $mamutId == null or $customerId == 0 or $customerId == null){
			$this->_fault("invalid_data_provided");
		}
		
		
		
		$customer = Mage::getModel("customer/customer")->load($customerId);
		if(!$customer->getId()){
			
			$this->_fault("magento_customer_not_exists");
		}
		
		$customer = $this->reader->fetchRow("SELECT customer_id FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." 
        							WHERE customer_id=?", $customerId );
		if($customer != null){
			$this->_fault("customer_id_allready_exists");
		}
       
		//$customermamut_mapping_row = $this->reader->fetchRow("SELECT customer_id, mamut_id FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." 
       // 							WHERE customer_id=? and mamut_id=?", array($customerId, $mamutId) ); 
		
		//if($customermamut_mapping_row == null){ 
		
			try{
				$sql  = "INSERT INTO ".$this->resource->getTableName('ecomwise_customermamut_mapping')." values (?,?)";
    			$this->writer->query($sql, array($customerId,$mamutId));
				return true;
		
			}catch(Exception $ex){
				Mage::log($ex->getMessage());
				return false;
			}
	
	  //	}
	  
	 
	}
	
    public function removeMamutMapping($mamutId, $customerId){
		$this->construct();

		if($mamutId == 0 or $mamutId == null or $customerId == 0 or $customerId == null){
			$this->_fault("invalid_data_provided");
		}

		$customermamut_mapping_row = $this->reader->fetchRow("SELECT mamut_id, customer_id FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." 
        							WHERE mamut_id=? AND customer_id=?", array($mamutId, $customerId) );
		
		if($customermamut_mapping_row != null){
			$sql  = "DELETE FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." WHERE mamut_id=? AND customer_id=?";
    		$this->writer->query($sql, array($mamutId, $customerId));
			return true;
		}else{
			$this->_fault("mapping_not_found");
		}
		return false;
	}
	
	public function filterMamutMapping(){
		$this->construct();
		$rows = $this->reader->fetchAll("SELECT * FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." ");
		
		$count = 0;
		foreach($rows as $row){
			$cid = $row['customer_id'];
			$cid_entity_row = $this->reader->fetchRow("SELECT * FROM ".$this->resource->getTableName('customer_entity')." WHERE entity_id = ".$cid." ;");
			if(!$cid_entity_row){
				$count++;
				$this->writer->query("DELETE  FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." WHERE customer_id = ".$cid." ;");
			}
			
		}
		
		return $count;
	}
	
}

?>