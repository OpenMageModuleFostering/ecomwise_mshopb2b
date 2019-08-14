<?php 
class Ecomwise_MshopB2B_Model_Api_Promotions_Api extends Mage_Api_Model_Resource_Abstract
{
	
	public function checkCreateFixedPriceForCustomer($mamutRuleId, $mamutCustomerId, $productSku, $fixedPrice){
		
		$rule_id = 0;
		
		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' fixed Price '.$fixedPrice,
			  'is_active'        => '1',
              'simple_action'    => 'to_fixed',
              'discount_amount'  => $fixedPrice,
        );
        $conditions = $this->setConditions("sku", $productSku);
        
	 	$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
       
        $rule_id = $this->createRule($data, $cus_groups, null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;
	}
	
	public function checkCreateDiscountForCustomer($mamutRuleId, $mamutCustomerId, $productSku, $discountPercentage){
		$rule_id = 0;
		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        
        $conditions = $this->setConditions("sku", $productSku);
        
		$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
        
		$rule_id = $this->createRule($data, $cus_groups, null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;
	}
	
	public function checkCreateDefaultDiscountForCustomer($mamutRuleId, $mamutCustomerId,  $discountPercentage){
		
		$rule_id = 0;
		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' discount percentage '.$discountPercentage." on all products",
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        
        //$conditions = $this->setConditions("sku", $productSku);
        
		$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
        
		$rule_id = $this->createRule($data, $cus_groups, null, 1, null);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;
		
	}
	
	public function checkCreateDiscountCategoryForCustomer($mamutRuleId, $mamutCustomerId, $categoryId, $discountPercentage){
		/*$rule_id = 0;
		$session_parameter = $mamutRuleId."-".$mamutCustomerId."-".$categoryId."-".$discountPercentage;

		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' category discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        
        $conditions = $this->setConditions("category_ids", $categoryId);
        
		$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
        
		$rule_id = $this->createRule($data, $cus_groups , null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;*/
		
//		$ruleIds = "";
//		$category = Mage::getModel('catalog/category')->load($categoryId);
//		$productCollection = $category->getProductCollection();
//		foreach ($productCollection as $product){
//			$ruleIds.=",".$this->createDiscountForCustomer($mamutRuleId,$mamutCustomerId,$product->getSku(),$discountPercentage);
//		}
//		return substr($ruleIds,1);

		$rule_id = 0;
		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' category discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        
        $productSkus = array();
		$category = Mage::getModel('catalog/category')->load($categoryId);
		$productCollection = $category->getProductCollection();
		
		foreach ($productCollection as $product){
			$productSkus[] = $product->getSku();
		}
		if(count($productSkus)==0){
			$this->_fault('no_products_assigned_to_category');
		}
        $conditions = $this->setConditions("sku", $productSkus);
        
		$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
        
		$rule_id = $this->createRule($data, $cus_groups , null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;
	}
	
   public function checkCreateFixedPriceForCustomerGroup($mamutRuleId, $customerGroup, $productSku, $price){
   		$rule_id = 0;
		$data = array(
              'name'             => 'Rule for customer group: '.$customerGroup.' fixed Price '.$price,
			  'is_active'        => '1',
              'simple_action'    => 'to_fixed',
              'discount_amount'  => $price,
        );
        $conditions = $this->setConditions("sku", $productSku);
        
        $customerGroupId = $this->getCustomerGroups($customerGroup);
        
        if(!$customerGroupId){
        	$this->_fault('customer_group_not_exists');
        }
        $rule_id = $this->createRule($data, array($customerGroupId), null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, null);

		return $rule_id;	
	}
	
	public function checkCreateDiscountForCustomerGroup($mamutRuleId, $customerGroup, $productSku, $discountPercentage){
		$rule_id = 0;
		$data = array(
              'name'             => 'Rule for customer group: '.$customerGroup.' discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        $conditions = $this->setConditions("sku", $productSku);
        
        $customerGroupId = $this->getCustomerGroups($customerGroup);
        
        if(!$customerGroupId){
        	$this->_fault('customer_group_not_exists');
        }
        $rule_id = $this->createRule($data, array($customerGroupId), null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, null);

		return $rule_id;
	}
	
	public function checkCreateDiscountCategoryForCustomerGroup($mamutRuleId, $customerGroup, $categoryId, $discountPercentage){
		
		/*
		$rule_id = 0;
		$data = array(
              'name'             => 'Rule for customer group on category: '.$customerGroup.' discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
         $conditions = $this->setConditions("category_ids", $categoryId);
        
        $customerGroupId = $this->getCustomerGroups($customerGroup);
        
        if(!$customerGroupId){
        	$this->_fault('customer_group_not_exists');
        }
        $rule_id = $this->createRule($data, array($customerGroupId), null, 1, $conditions);
        $this->mapRuleToMamutRule($rule_id, $mamutRuleId, null);

		return $rule_id;*/
		
		$rule_id = 0;
		$data = array(
				'name'             => 'Rule for customer group on category: '.$customerGroup.' discount percentage '.$discountPercentage,
				'is_active'        => '1',
				'simple_action'    => 'by_percent',
				'discount_amount'  => $discountPercentage,
		);
		$productSkus = array();
		$category = Mage::getModel('catalog/category')->load($categoryId);
		$productCollection = $category->getProductCollection();
		
		foreach ($productCollection as $product){
			$productSkus[] = $product->getSku();
		}
		if(count($productSkus)==0){
			$this->_fault('no_products_assigned_to_category');
		}
		$conditions = $this->setConditions("sku", $productSkus);
		
		$customerGroupId = $this->getCustomerGroups($customerGroup);
		
		if(!$customerGroupId){
			$this->_fault('customer_group_not_exists');
		}
		$rule_id = $this->createRule($data, array($customerGroupId), null, 1, $conditions);
		$this->mapRuleToMamutRule($rule_id, $mamutRuleId, null);
		
		return $rule_id;
		
	}
	
	public function deleteDiscountRule($mamutRuleIds){
		
		if(count ($mamutRuleIds) == 0){
			$this->_fault('array_not_pased');
		}
		
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
	
		$mamut_to_rule_table = $resource->getTableName('ecomwise_catalogpromotions_mapping');
		
        $result_rules = $connection->query("select * from ".$mamut_to_rule_table." ");
		
		$rows = $result_rules->fetchAll(PDO::FETCH_ASSOC);
		
		$deleted = false;
		foreach($rows as $row){
			$rule_mamut_id = $row['mamut_rule_id'] ;
			if(!in_array($rule_mamut_id, $mamutRuleIds)){	
		
				$result_rule = $connection->fetchRow("select rule_id from ".$mamut_to_rule_table." 
															where mamut_rule_id=?", $rule_mamut_id);
		
				if($result_rule['rule_id'] != null){
					$rule = Mage::getModel('catalogrule/rule')->load($result_rule['rule_id']);
					$rule->delete();
					$connection->delete($mamut_to_rule_table, 'mamut_rule_id = ' . $rule_mamut_id);
					$deleted = true;
		    	}
			}
       }
	   return $deleted;
	}
	
  	public function createRule($ruleData = null, $customerGroupIds=null, $websitesIds=null, $applyRule = null, $conditions = null){
	  	$data = array(
                    'name'                  => 'api_rule',
                    'description'           => '',
                    'from_date'             => '',
                    'to_date'               => '',
                    'is_active'             => '1',
                    'conditions_serialized' => '',
                    'actions_serialized'    => '',
                    'stop_rules_processing' => '0',
                    'sort_order'            => '0',
                    'simple_action'         => 'by_fixed',
                    'discount_amount'      => '0',
        );
                 
       	if($ruleData === null){          	
			$ruleData = array();
        }    
                 
	    foreach($ruleData as $key => $newValue)
          	if(isset($data[$key]) == false){               		
              	$this->_fault('code_not_valid');
            }else{
                $data[$key] = $newValue;
            }
                 
        if($customerGroupIds){
           	if(is_array($customerGroupIds)){
               	foreach($customerGroupIds as $groupid){
                 	$group = Mage::getModel('customer/group')->load($groupid);
                    if(!($group->getId())){
                       	return $this->_fault('customer_group_not_exists');
                    }                      
                }
                $data['customer_group_ids'] = $customerGroupIds;
            }else{
                return $this->_fault('customerids_array_not_passed');
            }
        }else{
            $data['customer_group_ids']= array(1);
        }
                 
	            if($websitesIds){
	            	if(is_array($websitesIds)){
                      foreach($websitesIds as $webid){
                      	$web = Mage::app()->getWebsite($webid);
                        if(!$web){
                        	return $this->_fault('website_not_exists');
                        }                      
                      }
                      $data['website_ids'] = $websitesIds;
	            	}else{
	            		 return $this->_fault('websiteids_array_not_passed');
	            	}
                 }else{
                 	$wids = array();
                      foreach (Mage::app()->getWebsites() as $website) {
                             $websiteId  = $website->getId();
                             $wids[] = $websiteId;
                          }
                      $data['website_ids'] = $wids;
                 }
                 
               try{
                 
                 $rule = Mage::getModel('catalogrule/rule');
                 $rule->setData($data);

                 foreach($conditions as $condition){
	            	$rule->getConditions()->addCondition($condition);
	        	 }
                 $rule->save();

                 return $rule->getId();
               }catch(Mage_Core_Exception $e){
               		return $this->_fault('not_created',$e->getMessage());
               }
	  
	  }
	
	
   private function isIndInstalled(){
	  	$modules = Mage::getConfig()->getNode('modules')->children();
	    $modulesArray = (array)$modules;
 
        if(isset($modulesArray['Ecomwise_Customerpromotions'])) {
         		
        	    return true;
		} else {
    			return false;
		}
	  
	  }
	  
 	private function mapRuleToMamutRule($rule_id, $mamutRuleId, $mamutcustomerid = null){
	  	
	  	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$resurce = Mage::getSingleton('core/resource');
		$mamut_to_rule_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');
		
		$mamut_id = ($mamutcustomerid != null)? $mamutcustomerid : 0;
		
		
		$result_rule = $connection->fetchRow("select rule_id from ".$mamut_to_rule_table." 
															where mamut_rule_id=?", array($mamutRuleId));
		
		if($result_rule['rule_id'] != null){
				$rule = Mage::getModel('catalogrule/rule')->load($result_rule['rule_id']);
				$rule->delete();
				
		 }
		
		
		$connection->delete($mamut_to_rule_table, 'mamut_rule_id = ' . $mamutRuleId);

		
		$data = array('rule_id'=> $rule_id,'mamut_id'=> $mamut_id,'mamut_rule_id'=>$mamutRuleId);
        $connection->insert($mamut_to_rule_table , $data);
        return;
	  
	}
 	private function getCustomerGroups($customerGroup){
	  	$model = Mage::getModel('mshopb2b/catalogpromotions');
	  	$customer_groups = $model->get_customer_groups();
	  	foreach($customer_groups as $key=>$val){
	  		if($val == $customerGroup){
	  			return $key;
	  		}
	  	
	  	}
	  	return false;
	  }
	  
	private function getGroup($mamut_customer_id){
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		$resurce = Mage::getSingleton('core/resource');
		$mapping_table = $resurce->getTableName('ecomwise_customermamut_mapping');
		$query    = $connection  ->select()
                                 ->from($mapping_table, array('customer_id'))
                                 ->where('mamut_id = ?', $mamut_customer_id)
                                 ->limit(1);
        $row_mamut = $connection->fetchRow($query);
        if($row_mamut){
        	$cusomer_id = $row_mamut['customer_id'];
        	$customer = Mage::getModel('customer/customer')->load($cusomer_id);
        	if($customer->getId()!= null){
        		return $customer->getGroupId();
        	
        	}else{
        		return false;
        	}
        }else{
        
        	return false;
        }
	  
	  }
	
	private function setConditions($type, $params){
		
		$conditions = array();
		if(is_array($params)){
			foreach ($params as $param){
	        	$skuCondition = Mage::getModel('catalogrule/rule_condition_product')
	                    ->setType('catalogrule/rule_condition_product')
	                    ->setAttribute($type)
	                    ->setOperator('==')
	                    ->setValue($param);
	            $conditions[] = $skuCondition;
	        }
		}else{
        	$skuCondition = Mage::getModel('catalogrule/rule_condition_product')
                    ->setType('catalogrule/rule_condition_product')
                    ->setAttribute($type)
                    ->setOperator('==')
                    ->setValue($params);
            $conditions[] = $skuCondition;
		}
        return $conditions;
	}
	
	private function changeAggregationToAny($rule_id){
		$rule = Mage::getModel('catalogrule/rule')->load($rule_id);
		$cond = unserialize($rule->getConditionsSerialized() );
		$cond['aggregator'] = 'any';

		$resource = Mage::getSingleton('core/resource');
		$writer  = $resource->getConnection('core_write');
		
		$sql  = "UPDATE ".$resource->getTableName('catalogrule')." SET conditions_serialized=? WHERE rule_id=\"".$rule_id."\"";
    	$writer->query($sql, serialize($cond));
	}
	
	private function createDiscountForCustomer($mamutRuleId, $mamutCustomerId, $productSku, $discountPercentage){
		$rule_id = 0;
		$session_parameter = $mamutRuleId."-".$mamutCustomerId."-".$productSku."-".$discountPercentage;

		$data = array(
              'name'             => 'Rule: '.$mamutRuleId.' for mamut user: '.$mamutCustomerId.' discount percentage '.$discountPercentage,
			  'is_active'        => '1',
              'simple_action'    => 'by_percent',
              'discount_amount'  => $discountPercentage,
        );
        
        $conditions = $this->setConditions("sku", $productSku);
        
		$cus_groups = array();
        if($cus_group = $this->getGroup($mamutCustomerId)){
        	$cus_groups[] = $cus_group;
        }else{
        	$cus_groups[] = 1;
        }
        
		$rule_id = $this->createRule($data, $cus_groups, null, 1, $conditions);
        $this->mapMultipleMamutRuleForMamutCustomer($rule_id, $mamutRuleId, $mamutCustomerId);

		return $rule_id;
	}
	
	private function mapMultipleMamutRuleForMamutCustomer($rule_id, $mamutRuleId, $mamutcustomerid = null){
	  	
	  	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$resurce = Mage::getSingleton('core/resource');
		$mamut_to_rule_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');
		
		$mamut_id = ($mamutcustomerid != null)? $mamutcustomerid : 0;
		
		$data = array('rule_id'=> $rule_id,'mamut_id'=> $mamut_id,'mamut_rule_id'=>$mamutRuleId);
        $connection->insert($mamut_to_rule_table , $data);
        return;
	  
	}
	
	public function applyRules(){
		try{
			$resource = Mage::getResourceSingleton('catalogrule/rule');
			$collection = Mage::getModel("catalogrule/rule")->getCollection();
			$collection->walk(array($resource, 'updateRuleProductData'));
			$resource->applyAllRulesForDateRange();
			Mage::app()->removeCache('catalog_rules_dirty');
			return  true;
		}catch(Exception $ex){
			Mage::log($ex->getMessage());
			return false;
		} 
	}
	
}