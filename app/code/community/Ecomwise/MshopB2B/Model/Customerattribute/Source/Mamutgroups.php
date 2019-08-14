<?php 
class Ecomwise_MshopB2B_Model_Customerattribute_Source_Mamutgroups extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
    {
        if (!$this->_options) {
        	
        	$resource = Mage::getSingleton('core/resource');
			$reader  = $resource->getConnection('core_read');	
			$tablename =  $resource->gettableName('ecomwise_mamut_contact_groups');
        	
			$rows = $reader->fetchAll("SELECT * FROM ".$tablename." WHERE mamut_contact_group_name !='MAMUT NOT LOGGED IN'");
        	if($rows){
				foreach ($rows as $row){
					$this->_options[] = array(
						 'label' => $row['mamut_contact_group_name'],
	            	     'value' => $row['mamut_contact_group_name'],
						 'selected' => true,
					 );
				}
        	}else{
        		$this->_options[] = array(
						 'label' => "There are no mamut contact groups uploaded",
	            	     'value' => "0",
						 'selected' => true,
					 );
        	}
			
        }
        return $this->_options;
    }
}