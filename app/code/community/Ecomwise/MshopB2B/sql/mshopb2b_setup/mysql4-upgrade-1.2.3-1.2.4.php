<?php
 
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE {$this->getTable('ecomwise_customermamut_mapping')} 
	CHANGE `customer_id`  `customer_id` INT( 10 ) UNSIGNED NOT NULL ;

	ALTER TABLE {$this->getTable('ecomwise_customermamut_mapping')} 
	ADD CONSTRAINT `CUSTOMER_MAMUT_MAPPING_CO_CUSTOM` FOREIGN KEY (`customer_id`) 
	REFERENCES {$this->getTable('customer_entity')} (`entity_id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;

		
	");

$installer->endSetup(); 
