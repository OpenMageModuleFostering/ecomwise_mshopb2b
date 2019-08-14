<?php 

class Ecomwise_MshopB2B_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form 
	extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
	{
	    public function getShippingRates()
	    {
	        if (empty($this->_rates)) {
	            $groups = $this->getAddress()->getGroupedAllShippingRates();
	            /*
	            if (!empty($groups)) {
	
	                $ratesFilter = new Varien_Filter_Object_Grid();
	                $ratesFilter->addFilter($this->getStore()->getPriceFilter(), 'price');
	
	                foreach ($groups as $code => $groupItems) {
	                    $groups[$code] = $ratesFilter->filter($groupItems);
	                }
	            }
	            */
	            $temp = array();
	            foreach($groups as $code => $_rates)
	            {
	            	if($code == "mamut_shipping") continue;
	            	$temp[$code] = $_rates;
	            }
	            return $this->_rates = $temp;
	        }
	        
	    	$temp = $this->_rates;
	        foreach($groups as $code => $_rates)
	        {
	        	if($code == "mamut_shipping") continue;
	            $temp[$code] = $_rates;
	        }
	        $this->_rates = $temp;
	        return $this->_rates;
	    }
	}