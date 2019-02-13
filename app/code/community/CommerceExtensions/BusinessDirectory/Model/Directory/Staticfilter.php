<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Staticfilter extends Mage_Core_Model_Abstract
{
  public $ALLOWABLE_FILTERS = array(
    // in order to add more filter types, this is the only
    // place that you need to add it. key => value pairs like below
    'listing_country'  => 'Country',
    'listing_state'    => 'State',
    'listing_city'     => 'City',
    'listing_zip_code' => 'Zip/Postal Code',
  );

  protected $_currentFilterField = null;

  protected function _construct()
  {
    $this->_init('businessdirectory/directory_staticfilter');
  }

  public function getFilters($directoryId = null)
  {
    if($directoryId) {
      $filters = $this->getCollection()
                      ->addFieldToFilter('directory_id', $directoryId)
                      ->setOrder('sort_order', Varien_Db_Select::SQL_ASC);
      return $filters;
    }

    if(!Mage::registry('staticfilters')) {
      if($directoryId = Mage::app()->getRequest()->getParam('directory_id')) {
        $filters = $this->getCollection()
                        ->addFieldToFilter('directory_id', $directoryId)
                        ->setOrder('sort_order', Varien_Db_Select::SQL_ASC);
        Mage::unregister('staticfilters');
        Mage::register('staticfilters', $filters);
        return $filters;
      }
    } else {
      return Mage::registry('staticfilters');
    }
  }

  public function getActiveFilters($directoryId = null)
  {
    return $this->getFilters($directoryId)
                ->addFieldToFilter('is_active', 1);
  }

  public function getDisplayableFilterField()
  {
    $filters = $this->getActiveFilters();
    $params  = array_keys(Mage::app()->getRequest()->getParams());
    return $filters->addFieldToFilter('filter_field', array('nin' => $params))->getFirstItem();
  }

  public function getCurrentFilterField()
  {
    if(!$this->_currentFilterField) {
      $params          = array_keys(Mage::app()->getRequest()->getParams());
      $allowableFields = array_keys($this->ALLOWABLE_FILTERS);
      $orderedFields   = array_reverse($allowableFields);
      foreach($orderedFields as $field) {
        if(in_array($field, $params)) {
          $this->_currentFilterField = $field;
          break;
        }
      }
    }
    return $this->_currentFilterField;
  }

  public function getFilterConfig($filterfield, $directoryId = null)
  {
    if(!$directoryId) {
      $directoryId = Mage::app()->getRequest()->getParam('directory_id');
    }

    $filterConfig = $this->getCollection()
                         ->addFieldToFilter('directory_id', $directoryId)
                         ->addFieldToFilter('filter_field', $filterfield)
                         ->getFirstItem();
    $filterConfig->setMaxValues((int)$filterConfig->getNumRows() * (int)$filterConfig->getNumCols());
    return $filterConfig;
  }

}