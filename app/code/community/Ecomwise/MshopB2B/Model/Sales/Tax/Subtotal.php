<?php 
class Ecomwise_MshopB2B_Model_Sales_Tax_Subtotal extends Mage_Tax_Model_Sales_Total_Quote_Subtotal{

 public function collect(Mage_Sales_Model_Quote_Address $address)
    {

    	
    	 $items = $this->_getAddressItems($address);
   		 foreach ($items as $item) {
        	 if($item->getApiCreated()){
         		return $this;
        	 }
    	}
    	
    	$this->_store   = $address->getQuote()->getStore();
        $this->_address = $address;

        $this->_subtotalInclTax     = 0;
        $this->_baseSubtotalInclTax = 0;
        $this->_subtotal            = 0;
        $this->_baseSubtotal        = 0;
        $this->_roundingDeltas      = array();

        $address->setSubtotalInclTax(0);
        $address->setBaseSubtotalInclTax(0);
        $address->setTotalAmount('subtotal', 0);
        $address->setBaseTotalAmount('subtotal', 0);

       
        if (!$items) {
            return $this;
        }

        $addressRequest = $this->_getAddressTaxRequest($address);
        $storeRequest   = $this->_getStoreTaxRequest($address);
        $this->_calculator->setCustomer($address->getQuote()->getCustomer());
        if ($this->_config->priceIncludesTax($this->_store)) {
            $classIds = array();
            foreach ($items as $item) {
                $classIds[] = $item->getProduct()->getTaxClassId();
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $classIds[] = $child->getProduct()->getTaxClassId();
                    }
                }
            }
            $classIds = array_unique($classIds);
            $storeRequest->setProductClassId($classIds);
            $addressRequest->setProductClassId($classIds);
            $this->_areTaxRequestsSimilar = $this->_calculator->compareRequests($storeRequest, $addressRequest);
        }

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            //if($item->getApiCreated()== true){
            	//return $this;
            //}
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_processItem($child, $addressRequest);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_processItem($item, $addressRequest);
            }
            $this->_addSubtotalAmount($address, $item);
        }
        $address->setRoundingDeltas($this->_roundingDeltas);
        return $this;
    }


}