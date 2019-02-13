<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Staticfilter extends Mage_Core_Block_Template
{
  public    $field;
  protected $_listingCollection;
  protected $_filterModel;

  public function getDirectory()
  {
    $directory = Mage::getSingleton('businessdirectory/directory')->getDirectory();
    if($this->helper('businessdirectory')->isVersionEqualOrHigher('1.8')) {
      $this->addModelTags($directory);
    }
    return $directory;
  }

  protected function _prepareData()
  {
    $this->_listingCollection = Mage::getModel('businessdirectory/directory_listing')->getListings();
    $this->_listingCollection->clear();
    return $this;
  }

  protected function _beforeToHtml()
  {
    $this->_prepareData();
    return parent::_beforeToHtml();
  }

  public function getResetUrl()
  {
    $url = $this->getDirectory()->getUrl();
    if($this->getFilterModel()->getCurrentFilterField()) {
      return "<div class='staticfilter-reset-link'>(<a href='{$url}'>show all</a>)</div>";
    }
    return null;
  }

  public function getFilterLabel()
  {
    $string = $this->getFilterModel()->getFilterConfig($this->field)->getFilterLabel();
    if(is_null($string)) {
      $string = $this->getFilterModel()->getFilterConfig($this->getDisplayableFilterField())->getFilterLabel();
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($string);
  }

  public function getFilterModel()
  {
    if(!$this->_filterModel) {
      return $this->_filterModel = Mage::getModel('businessdirectory/directory_staticfilter');
    }
    return $this->_filterModel;
  }

  public function getDisplayableFilterField()
  {
    return Mage::getModel('businessdirectory/directory_staticfilter')
               ->getDisplayableFilterField()
               ->getFilterField();
  }

  public function getCurrentFilterField()
  {
    return $this->getFilterModel()->getCurrentFilterField();
  }

  public function getFilterValues()
  {
    $currentParams = $this->getRequest()->getParams();
    $this->field   = $this->getDisplayableFilterField();

    if($this->field) {

      $filterModel  = $this->getFilterModel();
      $filterConfig = $filterModel->getFilterConfig($this->field);

      $this->_listingCollection
        ->addFieldToSelect($this->field);

      $allowableParamsArray = $filterModel->ALLOWABLE_FILTERS;
      unset($allowableParamsArray[$this->field]);
      $allowableParams = array_keys($allowableParamsArray);

      foreach($currentParams as $paramname => $paramdata) {
        if(in_array($paramname, $allowableParams)) {
          $this->_listingCollection->addFieldToFilter($paramname, $paramdata);
        }
      }
      if(!in_array('listing_country', $currentParams)) {
        $this->_listingCollection->addFieldToSelect('listing_country');
      }
      // if the number of results is greater than the max allowable filters,
      // this query will get the "most occuring" values
      $this->_listingCollection->getSelect()
                               ->distinct($this->field)
                               ->limit($filterConfig->getMaxValues())
                               ->columns(new Zend_Db_Expr("COUNT({$this->field}) AS num_occurrences"))
                               ->group($this->field)
                               ->order('num_occurrences DESC');

      $filterValues = array();
      foreach($this->_listingCollection->getItems() as $value) {
        if($value[$this->field]) {
          $filterValues[] = array('value' => trim($value[$this->field]), 'country' => $value['listing_country']);
        }
      }
      $filterValues = $this->resortFilters($filterValues, 'value');

      if(empty($filterValues)) {
        return array();
      }

      $columnCount                  = (int)$filterConfig->getNumCols();
      $chunkCount                   = ceil(count($filterValues) / $columnCount);
      $filterValuesChunks['values'] = array_chunk($filterValues, $chunkCount);
      $total                        = 0;

      // calculate a whole number column width
      for($i = 0; $i <= $columnCount - 1; $i++) {
        if($i != $columnCount - 1) {
          $width                                           = (int)floor(100 / $columnCount);
          $total                                           = (int)$total + $width;
          $filterValuesChunks['values'][$i]['columnWidth'] = (int)$width;
        } else {
          $width                                           = (int)floor(100 / $columnCount);
          $total                                           = (int)$total + $width;
          $extraPixels                                     = (int)$total - 100;
          $filterValuesChunks['values'][$i]['columnWidth'] = (int)$width - $extraPixels;
        }
      }

      $filterValuesChunks['columnCount'] = $columnCount;
      return $filterValuesChunks;
    }
    return array();

  }

  public function reSortFilters(&$array, $key)
  {
    $sorter = array();
    $ret    = array();
    reset($array);
    foreach($array as $ii => $va) {
      $sorter[$ii] = $va[$key];
    }
    asort($sorter);
    foreach($sorter as $ii => $va) {
      $ret[$ii] = $array[$ii];
    }
    return $array = $ret;
  }

  public function getFilterUrl($value)
  {
    if(!is_null($value)) {

      $allowableParams = array_keys($this->getFilterModel()->ALLOWABLE_FILTERS);
      $params          = array();
      foreach($this->getRequest()->getParams() as $key => $paramValue) {
        if(in_array($key, $allowableParams) && !array_key_exists($key, $params)) {
          $params[$key] = $paramValue;
        }
      }
      $params[$this->field] = $value;
      $query                = !empty($params) ? '?' . http_build_query($params) : null;
    } else {
      $query = null;
    }
    return $this->getDirectory()->getUrl() . $query;
  }

  public function getFilterLinkTitleTag($arguments = array())
  {
    $string = $this->getFilterModel()->getFilterConfig($this->field)->getFilterLinkTitle();
    return $this->helper('businessdirectory/tags')->processCustomTags($string, $arguments);
  }
}