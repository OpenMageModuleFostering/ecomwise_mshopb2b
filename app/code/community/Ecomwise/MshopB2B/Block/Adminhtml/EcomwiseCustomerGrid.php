<?php 
/**
 * Customers grid override.  
 * Mamut ID field is added to the customer grid
 **/
 
class Ecomwise_MshopB2B_Block_Adminhtml_EcomwiseCustomerGrid extends Mage_Adminhtml_Block_Customer_Grid{

 	protected function _prepareCollection(){
       $resurce = Mage::getSingleton('core/resource');
	   $table_name = $resurce->getTableName('ecomwise_customermamut_mapping');
	   $customer_entity_text = 	$resurce->getTableName('customer_entity_text');
		
	    /* get mamut_contact_groups attribute Id*/
	   	$entityTypeId = Mage::getModel('eav/config')->getEntityType('customer')->getEntityTypeId();
	    $attribute = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setCodeFilter('mamut_contact_groups')
                ->setEntityTypeFilter($entityTypeId)
                ->getFirstItem();
                
		$contactGroupAttrId = $attribute->getAttributeId();
		 
    	
    	$collection = Mage::getResourceModel('customer/customer_collection');
    	
    	
    	$collection->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('billing_company', 'customer_address/company', 'default_billing', null, 'left')
            ->joinField('mamut_contact_groups',
				$customer_entity_text,
				'value',
				'entity_id=entity_id',
				"{{table}}.attribute_id='".$contactGroupAttrId."'",
				'left');
            
        $collection->joinField('mamut_id_a',
                'mshopb2b/mshopb2b',
                'mamut_id',
                'customer_id=entity_id',
                null,
                'left');
            ;
         $collection->addAttributeToSelect('mamut_contact_groups');
            
         $this->setCollection($collection);

         Mage::register('customer_c',$collection);
         return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        
    }
    
    
	protected function _prepareColumns(){
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '140',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '90',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));

        $this->addColumn('Telephone', array(
            'header'    => Mage::helper('customer')->__('Telephone'),
            'width'     => '90',
            'index'     => 'billing_telephone'
        ));

        $this->addColumn('billing_postcode', array(
            'header'    => Mage::helper('customer')->__('ZIP'),
            'width'     => '70',
            'index'     => 'billing_postcode',
        ));
        
        $this->addColumn('mamut_id', array(
            'header'    => Mage::helper('customer')->__('Mamut Relation ID'),
            'width'     => '20px',
            'index'     => 'mamut_id_a',
            'type'  => 'text',
          // 'renderer' => 'mshopb2b/renderers_mamutIDRenderer',
        ));
        
        $contactOptions = array();
        $contactOptions = Mage::helper('mshopb2b/contactgroup')->getOptionsForFilter();
        $contactGroupFilter = 'mshopb2b/adminhtml_grid_filter_contactgroup';			

        $this->addColumn('mamut_contact_groups', array(
            'header'    => Mage::helper('customer')->__('Mamut Contact Group'),
            'width'     => '200px',
            'index'     => 'mamut_contact_groups',
            'type'      => 'options',
            'options'   => $contactOptions,
          	'filter'    => $contactGroupFilter,
          	'renderer' => 'mshopb2b/renderers_mamutContactRenderer',
        ));
        
        $this->addColumn('billing_company', array(
            'header'    => Mage::helper('customer')->__('Company'),
            'width'     => '70',
            'index'     => 'billing_company',
          
            
        ));

        $this->addColumn('billing_country_id', array(
            'header'    => Mage::helper('customer')->__('Country'),
            'width'     => '80',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ));

        $this->addColumn('billing_region', array(
            'header'    => Mage::helper('customer')->__('State/Province'),
            'width'     => '80',
            'index'     => 'billing_region',
        ));

        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '90',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }
}