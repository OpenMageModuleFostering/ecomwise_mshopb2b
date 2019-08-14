<?php
/*
 * Upgrade script for transfering extension from Experius to Ecomwise namesapce
 * 
 */

$installer = $this;
$installer->startSetup();

//ecomwise_catalogpromotions_mapping table moving

if (!$installer->tableExists($installer->getTable('ecomwise_catalogpromotions_mapping'))){
	
	$installer->run("
		CREATE TABLE {$this->getTable('ecomwise_catalogpromotions_mapping')} (
		`rule_id` int(11) NOT NULL default '0',
  		`mamut_id` int(11) NOT NULL default '0',
  		`mamut_rule_id` int(11) NOT NULL default '0',
  		UNIQUE KEY `ecomwise_rule_mamut_catalog` (`rule_id`,`mamut_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
}


if ($installer->tableExists($installer->getTable('experius_catalogpromotions_mapping')) && $installer->tableExists($installer->getTable('ecomwise_catalogpromotions_mapping'))) {
 
	$installer->run("INSERT INTO {$this->getTable('ecomwise_catalogpromotions_mapping')} (`rule_id`, `mamut_id`, `mamut_rule_id` ) SELECT `rule_id`, `mamut_id`, `mamut_rule_id` FROM {$this->getTable('experius_catalogpromotions_mapping')};");

}



//ecomwise_customermamut_mapping table moving

if (!$installer->tableExists($installer->getTable('ecomwise_customermamut_mapping'))){

	$installer->run("
			CREATE TABLE {$this->getTable('ecomwise_customermamut_mapping')} (
  			`customer_id` int(11) NOT NULL default '0',
  			`mamut_id` int(11) NOT NULL default '0',
  			UNIQUE KEY `ecomwise_mamut_customer` (`mamut_id`,`customer_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			");
}


if ($installer->tableExists($installer->getTable('experius_customermamut_mapping')) && $installer->tableExists($installer->getTable('ecomwise_customermamut_mapping'))) {

	$installer->run("INSERT INTO {$this->getTable('ecomwise_customermamut_mapping')} (`customer_id`, `mamut_id`) SELECT `customer_id`, `mamut_id` FROM {$this->getTable('experius_customermamut_mapping')};");

}


//ecomwise_mamut_contact_groups mamut_contact_group_name table moving

if (!$installer->tableExists($installer->getTable('ecomwise_mamut_contact_groups'))){

	$installer->run("
		CREATE TABLE {$this->getTable('ecomwise_mamut_contact_groups')} (
  		`group_id` int(11) NOT NULL AUTO_INCREMENT,
  		`mamut_contact_group_name` varchar(255) NOT NULL default '0',
  		PRIMARY KEY(group_id)
  		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
}


if ($installer->tableExists($installer->getTable('experius_mamut_contact_groups')) && $installer->tableExists($installer->getTable('ecomwise_mamut_contact_groups'))) {

		$installer->run("INSERT INTO {$this->getTable('ecomwise_mamut_contact_groups')} (`group_id`, `mamut_contact_group_name`) SELECT `group_id`, `mamut_contact_group_name` FROM {$this->getTable('experius_mamut_contact_groups')};");

}





$installer->endSetup();