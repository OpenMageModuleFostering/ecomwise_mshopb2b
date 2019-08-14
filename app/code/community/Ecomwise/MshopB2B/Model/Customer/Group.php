<?php 
class Ecomwise_MshopB2B_Model_Customer_Group extends Mage_Customer_Model_Group{

	 protected function _prepareData()
    {
        $this->setCode(
            substr($this->getCode(), 0, 100)
        );
        return $this;
    }
	

}