<?php
class Ecomwise_MshopB2B_Model_Collections_RuleCollection extends Mage_CatalogRule_Model_Resource_Rule_Collection{
	public function getSelectCountSql()
	{
		$this->_renderFilters();
		$countSelect = clone $this->getSelect();
		$countSelect->reset(Zend_Db_Select::ORDER);
		$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
		$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
		$countSelect->reset(Zend_Db_Select::COLUMNS);
		if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
			$countSelect->reset(Zend_Db_Select::GROUP);
			$countSelect->distinct(true);
			$group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
			$countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
		} else {
			$countSelect->columns('COUNT(*)');
		}
		return $countSelect;
	}
} 