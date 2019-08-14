<?php

class Ecomwise_Mshopb2b_Model_Payment_Mamutbilling  extends Mage_Payment_Model_Method_Abstract
{

	protected $_code = 'mamut_billing';

	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;
	protected $_canUseCheckout          = false;

}