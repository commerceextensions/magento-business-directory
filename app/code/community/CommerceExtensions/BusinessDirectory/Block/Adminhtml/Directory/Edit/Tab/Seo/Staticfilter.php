<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Seo_Staticfilter extends Mage_Adminhtml_Block_Template
{
  public    $fieldValueArray;
  protected $_staticFilterModel;

  public function getDirectoryId()
  {
    return $this->getRequest()->getParam('directory_id');
  }

  public function getStaticFilterModel()
  {
    if(!$this->_staticFilterModel) {
      return $this->_staticFilterModel = Mage::getModel('businessdirectory/directory_staticfilter');
    }
    return $this->_staticFilterModel;
  }

  public function getStaticFilters()
  {
    return $this->getStaticFilterModel()->getFilters();
  }

  public function getSortedFilterFieldArray()
  {
    $filterArray      = $this->getStaticfilters()->toArray();
    $allowableFilters = $this->getStaticFilterModel()->ALLOWABLE_FILTERS;

    $filterFields = array();
    // first we get the saved filters fields and set the sort order as the array key
    foreach($filterArray['items'] as $filter) {
      $filterFields[$filter['filter_field']] = $allowableFilters[$filter['filter_field']];
    }

    // get any missing filter fields from the allowable fields array
    foreach($allowableFilters as $field => $label) {
      if(!in_array($field, $filterFields)) {
        $filterFields[$field] = $label;
      }
    }
    return $filterFields;
  }

  public function getFilters()
  {
    return $this->getSortedFilterFieldArray();
  }

  public function fieldValueHelper($filtername, $dbfieldname)
  {
    if(!$this->fieldValueArray) {
      $filterArray = $this->getStaticfilters()->toArray();
      $newArray    = array();
      foreach($filterArray['items'] as $filter) {
        $newArray[$filter['filter_field']] = $filter;
        unset($newArray[$filter['filter_field']]['filter_field']);
      }
      $this->fieldValueArray = $newArray;
    }

    if(array_key_exists($filtername, $this->fieldValueArray) &&
       array_key_exists($dbfieldname, $this->fieldValueArray[$filtername])
    ) {
      return $this->fieldValueArray[$filtername][$dbfieldname];
    }
    return null;
  }

}