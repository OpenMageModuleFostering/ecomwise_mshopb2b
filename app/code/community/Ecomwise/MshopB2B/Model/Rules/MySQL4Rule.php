<?php 
class Ecomwise_MshopB2B_Model_Rules_MySQL4Rule extends Mage_CatalogRule_Model_Mysql4_Rule{
	
   public function getRulePrice($date, $wId, $gId, $pId){
    	
        $data = $this->getRulePrices($date, $wId, $gId, array($pId));
       
        if (isset($data[$pId])) {
            return $data[$pId];
        }

        return false; 
    }
    /**
     * 
     * Main function that calculates catalog rules prices for group of products
     * Selects all rules that are applied to that product for given date,
     * and applyies rules to the product price depending on the customer, customer group
     * @see Mage_CatalogRule_Model_Mysql4_Rule::getRulePrices()
     */
	public function getRulePrices($date, $websiteId, $customerGroupId, $productIds){
		
	 	$customer_id = Mage::getSingleton('customer/session')->getCustomerId();
	 	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		$resurce = Mage::getSingleton('core/resource');
		$mapping_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');

		$rule_prices = $resurce->getTableName('catalogrule_product_price');
		$rule_product = $resurce->getTableName('catalogrule_product');
		
	    if($mapping_table != null){
		 	if($customer_id == null){
		 	 	if ($_SESSION && isset($_SESSION['adminhtml_quote']) && isset($_SESSION['adminhtml_quote']['customer_id']) && $_SESSION['adminhtml_quote']['customer_id']){
                    	$customer_id = $_SESSION['adminhtml_quote']['customer_id'];
                }
		 
		 	}
		
		}
		
		$rule_ids = Mage::helper("mshopb2b") -> getRulesForCustomer($customer_id);
        
        $adapter = $this->_getReadAdapter();
        
		$product_prices = array();
		foreach ($productIds as $pid){
			
			$time = strtotime($date);
			
			$product_rule_data = $this->_getRuleProductsStmt($time, $time, $pid, $websiteId)->fetchAll();
			 
		    if($product_rule_data){
				$websiteId = $product_rule_data[0]['website_id'];
            	if (isset($product_rule_data[0]['website_'.$websiteId.'_price'])) {
                	$productPrice = $product_rule_data[0]['website_'.$websiteId.'_price'];
            	}else {
                	$productPrice = $product_rule_data[0]['default_price'];
            	}
			}else{
				$storeId = Mage::app()->getStore()->getId();
				$product = Mage::getModel('catalog/product')
				           ->setStoreId($storeId)
				           ->load($pid);
				$productPrice = $product->getPrice();
			}
			
			//filter hihest discount rule for customer
			$product_rule_data = $this->filterIndividual($product_rule_data, $rule_ids, $customer_id , $productPrice, $customerGroupId);
			
            $priceRules = $productPrice;
            $ind_rules = array();
            foreach ($product_rule_data as $data){
                $rule_id = $data['rule_id'];
		    	if(!in_array($rule_id, $ind_rules)){
            	        	$priceRules = Mage::helper('catalogrule')->calcPriceRule(
                        	$data['action_operator'],
                        	$data['action_amount'],
                        	$priceRules );
                        	$ind_rules[] = $rule_id;
                     		if ($data['action_stop']) {
                    	  		break;
            	     		}
            	      	}
             }
		 	$priceRules =  Mage::app()->getStore()->roundPrice($priceRules);
		 	$product_prices[$pid] = $priceRules;
		
		}
		
		return $product_prices;
    }
    /**
     * 
     * 
     * Checks if the rule is for individual customer
     * @param unknown_type $rule_id
     * @param unknown_type $customerId
     */
    protected function isInd($rule_id, $customerId){

    	$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

		$resurce = Mage::getSingleton('core/resource');
		$mapping_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');
		
		$query    = $connection  ->select()
                                 ->from($mapping_table)
                                 ->where('rule_id = ?', $rule_id);
                                 
        $rule_rows = $connection->fetchRow($query);

        if($rule_rows and $rule_rows['mamut_id'] != 0){

        	return true;  	
        }
        return false;
    }
	
    /**
     * 
     * Core magento function that selects all rules for product for given date.
     * 
     * @see Mage_CatalogRule_Model_Mysql4_Rule::_getRuleProductsStmt()
     */
    
	public function _getRuleProductsStmt($fromDate, $toDate, $productId=null, $websiteId = null)
    {
        $read = $this->_getReadAdapter();
        /**
         * Sort order is important
         * It used for check stop price rule condition.
         * website_id   customer_group_id   product_id  sort_order
         *  1           1                   1           0
         *  1           1                   1           1
         *  1           1                   1           2
         * if row with sort order 1 will have stop flag we should exclude
         * all next rows for same product id from price calculation
         */
        $select = $read->select()
            ->from(array('rp'=>$this->getTable('catalogrule/rule_product')))
            ->where($read->quoteInto('rp.from_time=0 or rp.from_time<=?', $toDate)
             ." or ".$read->quoteInto('rp.to_time=0 or rp.to_time>=?', $fromDate))
             
            ->order(array('rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order', 'rp.rule_id'));

        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }

        /**
         * Join default price and websites prices to result
         */
        $priceAttr  = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'price');
        $priceTable = $priceAttr->getBackend()->getTable();
        $attributeId= $priceAttr->getId();

        $joinCondition = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id='.$attributeId.') and %1$s.store_id=%2$s';

        $select->join(
            array('pp_default'=>$priceTable),
            sprintf($joinCondition, 'pp_default', Mage_Core_Model_App::ADMIN_STORE_ID),
            array('default_price'=>'pp_default.value')
        );

        if ($websiteId !== null) {
            $website  = Mage::app()->getWebsite($websiteId);
            $defaultGroup = $website->getDefaultGroup();
            if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                $storeId    = $defaultGroup->getDefaultStoreId();
            } else {
                $storeId    = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $select->joinInner(
                array('product_website'=>$this->getTable('catalog/product_website')),
                'product_website.product_id=rp.product_id AND rp.website_id=product_website.website_id AND product_website.website_id='.$websiteId,
                array()
            );

            $tableAlias = 'pp'.$websiteId;
            $fieldAlias = 'website_'.$websiteId.'_price';
            $select->joinLeft(
                array($tableAlias=>$priceTable),
                sprintf($joinCondition, $tableAlias, $storeId),
                array($fieldAlias=>$tableAlias.'.value')
            );
        } else {
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteId  = $website->getId();
                $defaultGroup = $website->getDefaultGroup();
                if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                    $storeId    = $defaultGroup->getDefaultStoreId();
                } else {
                    $storeId    = Mage_Core_Model_App::ADMIN_STORE_ID;
                }

                $storeId    = $defaultGroup->getDefaultStoreId();
                $tableAlias = 'pp'.$websiteId;
                $fieldAlias = 'website_'.$websiteId.'_price';
                $select->joinLeft(
                    array($tableAlias=>$priceTable),
                    sprintf($joinCondition, $tableAlias, $storeId),
                    array($fieldAlias=>$tableAlias.'.value')
                );
            }
        }
        return $read->query($select);
    }
    
    
    public function filterIndividual($product_rule_data, $rule_ids, $customer_id, $productPrice, $customerGroupId){
    	//containers for highest discount rule id and discout price
    	$highest = null;
    	$price_start = 100000000000;
    	
    	
    	$individual = array();
    	
    	foreach($product_rule_data as $key => $data ){
    		
    		//if rule is for other individual customer is removed
    		if($this->isInd($data['rule_id'], $customer_id)){
    			if(!in_array($data['rule_id'], $rule_ids)){
    				unset($product_rule_data[$key]);
    			}else{
    			//rule price is calculated for later filtering, and if highes, rule_id is stored in $hihgest.	
    				$rule_price = Mage::helper('catalogrule')->calcPriceRule(
                        	$data['action_operator'],
                        	$data['action_amount'],
                        	$productPrice);
    				if($rule_price < $price_start){
    					$price_start = $rule_price;
    					$highest = $key;
    				}
    				//used for later filtering of highest discount rule
    				$individual[] = $key;
    			}
    			
    		}else{
    			if($customerGroupId != $data['customer_group_id']){
    				unset($product_rule_data[$key]);
    			}else{
    				
    				//check if rule is created by mshop
     				$resurce = Mage::getSingleton('core/resource');
     				$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
     				$mshop_rule_ids = $connection->fetchAll("select rule_id from ".$resurce->getTableName('ecomwise_catalogpromotions_mapping')."");
     				$isMshop = false;
     				foreach($mshop_rule_ids as $rule){
     					if($rule['rule_id']==$data['rule_id']){
     						$isMshop = true;
     					}
     				}
     				
     				if($isMshop){
 	    				//rule price is calculated for later filtering, and if highes, rule_id is stored in $hihgest.
 	    				$rule_price = Mage::helper('catalogrule')->calcPriceRule(
	    						$data['action_operator'],
	    						$data['action_amount'],
	    						$productPrice);
	    				if($rule_price < $price_start){
	    					$price_start = $rule_price;
	    					$highest = $key;
	    				}
	    				//used for later filtering of highest discount rule
	    				$individual[] = $key;
     				}
     			}
    			
    		}
    	}
    	//filter highest discount rule if configuration is on only for individual rules
    	if(Mage::getStoreConfig("mshopb2b/parameters/applyhighestrule", 0)){
    		$diffrence = array_diff ($individual, array($highest));
    		foreach($diffrence as $array_key){
    			unset($product_rule_data[$array_key]);
    		}
    		
    	}
    	return $product_rule_data;
    }
    
}