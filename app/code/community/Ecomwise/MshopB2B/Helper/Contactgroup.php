<?php
class Ecomwise_MshopB2B_Helper_Contactgroup extends Mage_Core_Helper_Abstract
{   
    public function getOptionsForFilter()
    {
        $resource = Mage::getSingleton('core/resource');
		$reader  = $resource->getConnection('core_read');	
		$tablename =  $resource->gettableName('ecomwise_mamut_contact_groups');
        
		$rows = $reader->fetchAll("SELECT * FROM ".$tablename." WHERE mamut_contact_group_name !='MAMUT NOT LOGGED IN'");
        if($rows){
			foreach ($rows as $row){
				$options[$row['group_id']] = $row['mamut_contact_group_name'];
			}
			return $options;
        }
    }
    
}