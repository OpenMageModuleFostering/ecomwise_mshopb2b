<?php 
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE {$this->getTable('customer/customer_group')} MODIFY customer_group_code VARCHAR(100) ;
	

		");

$installer->endSetup(); 
