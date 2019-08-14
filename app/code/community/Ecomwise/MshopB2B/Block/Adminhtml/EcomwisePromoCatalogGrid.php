<?php
/**
 *Catolog promotions grid override. 
 * mamuit id, mamut rule id, customer ids filds are added
 * 
 */

class Ecomwise_MshopB2B_Block_Adminhtml_EcomwisePromoCatalogGrid extends Mage_Adminhtml_Block_Widget_Grid{
	
	 public function __construct(){
        parent::__construct();
        $this->setId('promo_catalog_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        
    	$resurce = Mage::getSingleton('core/resource');
		$table_name = $resurce->getTableName('ecomwise_catalogpromotions_mapping'); 
		$table_customermapping = $resurce->getTableName('ecomwise_customermamut_mapping');
		$customer_table = $resurce->getTableName('customer_entity');
		$cutomerGroups = $resurce->getTableName('catalogrule_customer_group');
        $collection = Mage::getResourceModel('catalogrule/rule_collection');
        $collection->getSelect()->joinLeft(array('mapping' => $table_name), 'main_table.rule_id = mapping.rule_id', array('mapping.mamut_id', 'mapping.mamut_rule_id'));
        $collection->getSelect()->joinLeft(array('mappingCustomer' => $table_customermapping), 'mapping.mamut_id = mappingCustomer.mamut_id', array('mappingCustomer.customer_id'));
        $collection->getSelect()->joinLeft(array('customer' => $customer_table), 'mappingCustomer.customer_id = customer.entity_id', array('customer.email'));
        
        if(version_compare(Mage::getVersion(),'1.7.0.0','>=')){
        	$collection->getSelect()->joinLeft(array('cgroups' => $cutomerGroups),'main_table.rule_id = cgroups.rule_id',array('*'));
        }
        $collection->getSelect()->group('main_table.rule_id');
       
        $this->setCollection($collection);
        return parent::_prepareCollection();
    	
    	
    }

    protected function _prepareColumns(){
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('catalogrule')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        	'filter_index'=>'main_table.rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalogrule')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));
        
        $this->addColumn('from_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('catalogrule')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('catalogrule')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));
        
         $this->addColumn('mamut_id', array(
            'header'    => Mage::helper('catalogrule')->__('Mamut Relation ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'mamut_id',
        ));
        
        $this->addColumn('mamut_rule_id', array(
            'header'    => Mage::helper('catalogrule')->__('Mamut Rule ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'mamut_rule_id',
        ));
        
        $resurce_model = Mage::getModel('mshopb2b/catalogpromotions');
        $customers = $resurce_model->get_customers();
       
        $customer_groups = $resurce_model->get_customer_groups();
        if(version_compare(Mage::getVersion(),'1.7.0.0','>=')){
	        $this->addColumn('customer_group_id', array(
	            'header'    => Mage::helper('salesrule')->__('Customer Groups'),
	            'align'     => 'left',
	            'width'     => '120px',
	            'index'     => 'customer_group_id',
	            'renderer'  => 'mshopb2b/renderers_gridCatalogRenderer',
	            'filter'    => 'mshopb2b/renderers_gridCatalogFilterCustomerGroup',
	            'options'   => $customer_groups,
	        ));
        }else{
        	$this->addColumn('customer_group_ids', array(
        			'header'    => Mage::helper('salesrule')->__('Customer Groups'),
        			'align'     => 'left',
        			'width'     => '120px',
        			'index'     => 'customer_group_ids',
        			'renderer'  => 'mshopb2b/renderers_gridCatalogRenderer',
        			'filter'    => 'mshopb2b/renderers_gridCatalogFilterCustomerGroup',
        			'options'   => $customer_groups,
        	));
        }
        
       /*$this->addColumn('customer_ids', array(
            'header'    => Mage::helper('salesrule')->__('Individual Customers'),
            'align'     => 'left',
            'width'     => '260px',
            'index'     => 'mapping.mamut_id',
            'renderer'  => 'mshopb2b/renderers_gridCatalogCustomer',
            'filter'    => 'adminhtml/widget_grid_column_filter_select',
            'options'   => $customers,
        ));*/
        $this->addColumn('email', array(
        		'header'    => Mage::helper('salesrule')->__('Individual Customers'),
        		'align'     => 'left',
        		'width'     => '260px',
        		//'index'     => 'mapping.mamut_id',
        		'index'     => 'email',
        		'renderer'  => 'mshopb2b/renderers_gridCatalogCustomer',
        		//'filter'    => 'adminhtml/widget_grid_column_filter_select',
        		//'options'   => $customers,
        ));
        
         $this->addColumn('sort_order', array(
            'header'    => Mage::helper('salesrule')->__('Priority'),
            'width'     => '100px',
            'align'     => 'right',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }
    
    protected function _afterLoadCollection(){
    	 $collection = $this->getCollection();
    	  
    	 $rules_ids = array();
    	 
      	foreach ($collection->load() as $rule){
      	 	if ($rule_id = $rule->getId()){
                $rules_ids[] = $rule_id;
            } 
        }
        
        $customers_to_rule = array();
      	if ($rules_ids){
       
       		//$customers_to_rule = array();
       
       		$resurce_customers = Mage::getModel('mshopb2b/catalogpromotions');
       		$customers = $resurce_customers->get_customers();

       		$resurce = Mage::getSingleton('core/resource');
       		$mapping_table = $resurce->getTableName('ecomwise_customermamut_mapping');        
       		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
       
        	foreach($rules_ids as $rule_id){
       			$mamut_id = $resurce_customers->getMamutId($rule_id);
       	
       			if($mamut_id){
       				$result = $connection->query('SELECT * FROM '.$mapping_table.'  
						                       WHERE mamut_id = '.$mamut_id.' 
						            ');	
            		$rows = $result->fetchAll(PDO::FETCH_ASSOC);
            
           		 	if($rows){
            		 	foreach($rows as $row){
            	
            				if (isset($customers[$row['customer_id']])){
                       			 $customers_to_rule[$rule_id][$row['customer_id']] = $customers[$row['customer_id']];
                    		}
            			}  
            		}
       			}   
      		} 
       	}
		Mage::register('customerpromotions_catalog_rule_customers', $customers_to_rule);
    	return parent::_afterLoadCollection();
    }
	
	private function getMamutId($rule_id){
	    $resurce = Mage::getSingleton('core/resource');
        $mapping_table = $resurce->getTableName('ecomwise_catalogpromotions_mapping');        
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
	
		$result = $connection->query('SELECT * FROM '.$mapping_table.'  
						                       WHERE rule_id = '.$rule_id.' 
						             LIMIT 1');	
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            if($row){
            	return  $row['mamut_id'];
            }else{
            	return false;
            }		
	}
}