<?php 
/**
 * Class extends and overwrites Mage_CatalogRule_Model_Rule_Condition_Combine
 * constructor setting the default aggregator to ANY
 * 
 *
 * @author 
 *
 */
class Ecomwise_MshopB2B_Model_Rules_Combine extends Mage_CatalogRule_Model_Rule_Condition_Combine{
	
    public function __construct(){
        parent::__construct();
    	$this->setType('catalogrule/rule_condition_combine')
             ->setAggregator('any');
    }
}