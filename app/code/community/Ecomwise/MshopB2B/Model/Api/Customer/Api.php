<?php 

class Ecomwise_MshopB2B_Model_Api_Customer_Api extends Mage_Api_Model_Resource_Abstract{

	protected $resource;
	protected $reader;
	protected $writer;
	
	protected function construct(){
		$this->resource = Mage::getSingleton('core/resource');
		$this->reader  = $this->resource->getConnection('core_read');	
		$this->writer  = $this->resource->getConnection('core_write');
	}
	
	public function updatePreferedLocale($mamutId, $input)
	{
		$mapping = array(
			'NO' => 'nn_NO',
		    'SV' => 'sv_SE',
		    'DA' => 'da_DK',		
		    'FI' => 'fi_FI',
		    'EN' => 'en_US',
		    'FR' => 'fr_FR',
		    'DE' => 'de_DE',
		    'ES' => 'es_ES',
		    'IT' => 'it_IT',
		    'PT' => 'pt_PT',
		    'EL' => 'el_GR',
		    'ZH' => 'zh_CN',
		    'CS' => 'cs_CZ',
		    'ID' => 'id_ID',
		    'HE' => 'he_IL',
		    'JA' => 'ja_JP',
		    'MS' => 'ms_MY',
		    'NL' => 'nl_NL',
		    'RU' => 'ru_RU',
		    'AR' => 'ar_SA',
		    'KO' => 'ko_KR',
		    'TH' => 'th_TH',
		    'TR' => 'tr_TR',
		    'IS' => 'is_IS'
		);
		
		$preferedLocale = 'en_US';
		if($mapping[$input])
		{
			$preferedLocale = $mapping[$input];	
		}
		
		$this->construct();
		if($mamutId == 0 or $mamutId == null){
			$this->_fault("customer_not_exists_for_mamut_id");
		}
		$customer_row = $this->reader->fetchRow("SELECT customer_id FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." 
        							WHERE mamut_id=?", $mamutId );
		
		if($customer_row == null){
			$this->_fault("customer_not_exists_for_mamut_id");
		}
		$customerId = $customer_row['customer_id'];
		$customer = Mage::getModel('customer/customer')->load($customerId);
		
		$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'customer_language_locale');
		
		if($attribute)
		{
			if($customer)
			{
				$customer->setCustomerLanguageLocale($preferedLocale);
				try{
					$customer->save();
					return true;
				}catch(Exception $e){
					return false;	
				}
			}
			return false;
		}
		return false;
	}
	
	public function createCustomerGroup($groupName){  

		$customer_group = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_code', array('eq'=>$groupName));
		
		if($customer_group->count() == 0){
		
			Mage::getSingleton('customer/group')->setData(
			     array('customer_group_code' => $groupName,
			           'tax_class_id' => 3)
			)->save();
		
		}
		return true;
	}
	
	public function assignCustomerToGroup($customerId, $groupId){
		
		$customer = Mage::getModel('customer/customer')->load($customerId);
		if(!$customer->getId()){
			$this->_fault("unknown_mcustomer");
		}
		
		$group = Mage::getModel('customer/group')->load($groupId);
		if(!$group->getId()){
			$this->_fault("unknown_mgroup");
		}
		$customer->setGroupId($groupId);
		$customer->save();
		
		return true;
	}
	
	public function update($magentoCustomerId, $customerData, $addressBillingData, $addressShippingData){
		$this->construct();
		$customer_id = $magentoCustomerId;
		
		if($customer_id != '' && $customer_id != null && Mage::getModel('customer/customer')->load($customer_id)->getId()){
			$customerId = $customer_id;
		}else{
			$mamut_id = $customerData->mamut_id;
			if($mamut_id == '' or $mamut_id == null){
				$this->_fault("customer_not_exists_for_mamut_id");
			}
			$customer_row = $this->reader->fetchRow("SELECT customer_id FROM ".$this->resource->getTableName('ecomwise_customermamut_mapping')." 
	        							WHERE mamut_id=?", $mamut_id );
			
			if($customer_row == null){
				$this->_fault("customer_not_exists_for_mamut_id");
			}
			$customerId = $customer_row['customer_id'];
		}
		//$customerEntity = $customerData->customerEntity;
		
		$customer = Mage::getModel('customer/customer')->load($customerId);
		if(!$customer->getId()){
			$this->_fault("mapped_customer_not_exists_for_mamut_id");
		}
		
		$mamut_contact_groups = $customerData->mamut_contact_groups;
		if(is_array($mamut_contact_groups)){
			$this->updateMamutContactGroups($customer, $mamut_contact_groups );
		} 
		
		$response1 = Mage::getModel('customer/customer_api')->update($customerId, 
																	array(
																		email=>$customerData->email,
																		firstname=>$customerData->firstname,
																		lastname=>$customerData->lastname,
																		password=>$customerData->password,
																		website_id=>$customerData->website_id,
																		store_id=>$customerData->store_id,
																		group_id=>$customerData->group_id,
																		taxvat=>$customerData->taxvat
																	)					
		);
		if(!$response1){
			$this->_fault("customer_information_update_failed");
		}
		
		 //update default billing address
		$billingAdr = $customer->getDefaultBillingAddress();
	    if($billingAdr){
	       $addrId = $billingAdr->getId();
		   $response2 = Mage::getModel('customer/address_api')->update($addrId,
															array(
																city=>$addressBillingData->city,
																company=>$addressBillingData->company,
																country_id=>$addressBillingData->country_id,
																fax=>$addressBillingData->fax,
																firstname=>$addressBillingData->firstname,
																lastname=>$addressBillingData->lastname,
																middlename=>$addressBillingData->middlename,
																postcode=>$addressBillingData->postcode,
																prefix=>$addressBillingData->prefix,
																region_id=>$addressBillingData->region_id,
																region=>$addressBillingData->region,
																street=>$addressBillingData->street,
																suffix=>$addressBillingData->suffix,
																telephone=>$addressBillingData->telephone,
																is_default_billing=>1,
																//is_default_shipping=>1,
															)		
			);
			if(!$response2){
				$this->_fault("address_information_update_failed");
			}
        }else{
        	$response2 = Mage::getModel('customer/address_api')->create($customerId,
															array(
																city=>$addressBillingData->city,
																company=>$addressBillingData->company,
																country_id=>$addressBillingData->country_id,
																fax=>$addressBillingData->fax,
																firstname=>$addressBillingData->firstname,
																lastname=>$addressBillingData->lastname,
																middlename=>$addressBillingData->middlename,
																postcode=>$addressBillingData->postcode,
																prefix=>$addressBillingData->prefix,
																region_id=>$addressBillingData->region_id,
																region=>$addressBillingData->region,
																street=>$addressBillingData->street,
																suffix=>$addressBillingData->suffix,
																telephone=>$addressBillingData->telephone,
																is_default_billing=>1,
																//is_default_shipping=>1,
															)		
			);
			if(!$response2){
				$this->_fault("address_information_update_failed");
			}
        }
        
        //update default shipping address
        $shippingAdr = $customer->getDefaultShippingAddress();
	    if($shippingAdr){
	       $addrId = $shippingAdr->getId();
		   $response3 = Mage::getModel('customer/address_api')->update($addrId,
															array(
																city=>$addressShippingData->city,
																company=>$addressShippingData->company,
																country_id=>$addressShippingData->country_id,
																fax=>$addressShippingData->fax,
																firstname=>$addressShippingData->firstname,
																lastname=>$addressShippingData->lastname,
																middlename=>$addressShippingData->middlename,
																postcode=>$addressShippingData->postcode,
																prefix=>$addressShippingData->prefix,
																region_id=>$addressShippingData->region_id,
																region=>$addressShippingData->region,
																street=>$addressShippingData->street,
																suffix=>$addressShippingData->suffix,
																telephone=>$addressShippingData->telephone,
																//is_default_billing=>1,
																is_default_shipping=>1,
															)		
			);
			if(!$response3){
				$this->_fault("address_information_update_failed");
			}
        }else{
        	$response3 = Mage::getModel('customer/address_api')->create($customerId,
															array(
																city=>$addressShippingData->city,
																company=>$addressShippingData->company,
																country_id=>$addressShippingData->country_id,
																fax=>$addressShippingData->fax,
																firstname=>$addressShippingData->firstname,
																lastname=>$addressShippingData->lastname,
																middlename=>$addressShippingData->middlename,
																postcode=>$addressShippingData->postcode,
																prefix=>$addressShippingData->prefix,
																region_id=>$addressShippingData->region_id,
																region=>$addressShippingData->region,
																street=>$addressShippingData->street,
																suffix=>$addressShippingData->suffix,
																telephone=>$addressShippingData->telephone,
																//is_default_billing=>1,
																is_default_shipping=>1,
															)		
			);
			if(!$response3){
				$this->_fault("address_information_update_failed");
			}
        }
		return true;
	}
	
	
	public function create($customerData, $addressBillingData, $addressShippingData){
		$this->construct();
		$errors = $this->validateCustomerData($customerData, $addressBillingData, $addressShippingData);
		
		if(count($errors) > 0){
			$this->_fault("customer_create_incomplete_data", "Incomplete customer creation data. Missing parts: ".implode(', ',$errors));
			
		}
		$customer = Mage::getModel("customer/customer")->setWebsiteId($customerData->website_id)->loadByEmail($customerData->email);
		if($customer->getId()){
			$this->_fault("customer_create_customer_allready_exists");
		}
		$customerId  = Mage::getModel('customer/customer_api')->create(array(
																		'email'=>$customerData->email,
																		'firstname'=>$customerData->firstname,
																		'lastname'=>$customerData->lastname,
																		'password'=>$customerData->password,
																		'website_id'=>$customerData->website_id,
																		'store_id'=>$customerData->store_id,
																		'group_id'=>$customerData->group_id,
																		'taxvat'=>$customerData->taxvat
																	));
		if(!$customerId){
			$this->_fault("customer_creation_failed");
		}
		
		$customer = Mage::getModel('customer/customer')->load($customerId);

		
		$addressBillingData->is_default_billing = 1; 
		$response2 = Mage::getModel('customer/address_api_v2')->create($customerId, $addressBillingData);


		$addressShippingData->is_default_shipping = 1; 
		$response3 = Mage::getModel('customer/address_api_v2')->create($customerId, $addressShippingData);

		if(!$response2){
			$this->_fault("address_creation_failed");
		}
		if(!$response3){
			$this->_fault("address_creation_failed");
		}
		
		$mamut_contact_groups = $customerData->mamut_contact_groups;
		if(is_array($mamut_contact_groups)){
			
			$this->updateMamutContactGroups($customer, $mamut_contact_groups );
		}
		
		$setmamut = Mage::getModel('mshopb2b/api_mamut_api')->setMamutId($customerData->mamut_id, $customerId);
		
		if(!$setmamut){
			$this->_fault("mamut_id_not_set_for_customer");
		}
		
		return $customerId;
	}
	
	public function updatedSince($date){
		
		$allCustomers = Mage::getModel('customer/customer')
    		->getCollection()
    		->addAttributeToSelect('updated_at')
    		->addAttributeToFilter('email', array("neq"=>''))
    		->addAttributeToFilter('updated_at', array('gt' => $date))
    		->joinField('mamut_id_alias','mshopb2b/mshopb2b','mamut_id','customer_id=entity_id',null,'left')
    		;
    		
    	//return $allCustomers; 
    		
    	$cus_mamut_ids = array();
    	foreach ($allCustomers as $cus){
    		if($cus->getMamutIdAlias() != null){
    			$cus_mamut_ids[] = $cus->getMamutIdAlias() ;
    		}
    	}	
    	return $cus_mamut_ids;	
	
	}
	private function updateMamutContactGroups($customer, $mamut_contact_groups ){
		
		$tablename =  $this->resource->gettableName('ecomwise_mamut_contact_groups');
		
		for($i =0; $i<count($mamut_contact_groups); $i++){
			
			$group = trim($mamut_contact_groups[$i]);
			$query = "SELECT * FROM ".$tablename." WHERE mamut_contact_group_name = '".$group."' ";
			$row = $this->reader->fetchRow($query);
			if(!$row){
				
				$this->writer->query("INSERT INTO ".$tablename." (mamut_contact_group_name) values('".$group."')");
			}
			$mamut_contact_groups[$i] = $group;
		}
		$customer->setMamutContactGroups(implode(",", $mamut_contact_groups));
		$customer->save();
		
		return;
		
	}
	
	
	private function validateCustomerData($customerData, $billndAddress, $shippingAddress){
		$errors = array();
		
			
		if($customerData->email == null || $customerData->email == ''){
			$errors [] = 'customer data:email';
		}
		if($customerData->firstname == null || $customerData->firstname == ''){
				$errors [] = 'customer data:firstname';
			}
		if($customerData->lastname == null || $customerData->lastname == ''){
				$errors [] = 'customer data:lastname';
			}
		if($customerData->website_id == null || $customerData->website_id == ''){
				$errors [] = 'customer data:website_id';
			}
		if($customerData->group_id == null || $customerData->group_id == ''){
				$errors [] = 'customer data:group_id';
			}
		if(Mage::getStoreConfig('customer/address/taxvat_show') == 'req'){
			if($customerData->taxvat == null || $customerData->taxvat == ''){
				$errors [] = 'customer data:taxvat';
			}	
		}

	
		if($billndAddress->firstname == null || $billndAddress->firstname == ''){
				$errors [] = 'billing address:firstname';
			}
		if($billndAddress->lastname == null || $billndAddress->lastname == ''){
				$errors [] = 'billing address:lastname';
			}
		if($billndAddress->street == null || $billndAddress->street == '' || count($billndAddress->street) == 0){
				$errors [] = 'billing address:street';
			}
		if($billndAddress->city == null || $billndAddress->city == ''){
				$errors [] = 'billing address:city';
			}
		if($billndAddress->country_id == null || $billndAddress->country_id == ''){
				$errors [] = 'billing address:country_id';
			}	
		if($billndAddress->postcode == null || $billndAddress->postcode == ''){
				$errors [] = 'billing address:postcode';
			}	
			if($billndAddress->telephone == null || $billndAddress->telephone == ''){
				$errors [] = 'billing address:telephone';
			}
			
		if($shippingAddress->firstname == null || $shippingAddress->firstname == ''){
				$errors [] = 'shipping address:firstname';
			}
		if($shippingAddress->lastname == null || $shippingAddress->lastname == ''){
				$errors [] = 'shipping address:lastname';
			}
		if($shippingAddress->street == null || $shippingAddress->street == '' || count($shippingAddress->street) == 0){
				$errors [] = 'shipping address:street';
			}
		if($shippingAddress->city == null || $shippingAddress->city == ''){
				$errors [] = 'shipping address:city';
			}
		if($shippingAddress->country_id == null || $shippingAddress->country_id == ''){
				$errors [] = 'shipping address:country_id';
			}	
		if($shippingAddress->postcode == null || $shippingAddress->postcode == ''){
				$errors [] = 'shipping address:postcode';
			}	
		if($shippingAddress->telephone == null || $shippingAddress->telephone == ''){
				$errors [] = 'shipping address:telephone';
		}	
			
		
		return $errors;
		
	}
	
	
}

?>