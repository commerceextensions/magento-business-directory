<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Listing extends Mage_Core_Model_Abstract
{
  public $REQUIRED_CSV_COLUMNS = array('is_active', 'listing_name', 'listing_country');

  protected function _construct()
  {
    $this->_init('businessdirectory/directory_listing');
  }

  public function checkIdentifier($identifier, $directoryId)
  {
    return $this->_getResource()->checkIdentifier($identifier, $directoryId);
  }

  public function getUrl($listingId = null, $storeId = null)
  {
    $storeId   = is_null($storeId) ? Mage::app()->getStore()->getStoreId() : $storeId;
    $baseUrl   = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $storeId);
    $suffix    = Mage::getStoreConfig('businessdirectory/frontend/url_suffix');
    $listing   = $this->getListing($listingId);
    $directory = Mage::getModel('businessdirectory/directory')->load($listing->getDirectoryId());
    return $baseUrl . $directory->getIdentifier() . '/' . $listing->getIdentifier() . $suffix;
  }

  public function getListing($listingId = null)
  {
    if($listingId) {
      $listing = $this->load($listingId);
      return $listing;
    }

    if(!Mage::registry('listing')) {
      if($listingId = Mage::app()->getRequest()->getParam('listing_id')) {
        $listing = $this->load($listingId);
        Mage::register('listing', $listing);
        return $listing;
      }
    } else {
      return Mage::registry('listing');
    }
  }

  public function getListings($directoryId = null)
  {
    if($directoryId) {
      $collection = $this->getCollection()
                         ->addFieldToFilter('directory_id', $directoryId)
                         ->addFieldToFilter('is_active', 1);
      return $collection;
    }

    if($directoryId = Mage::app()->getRequest()->getParam('directory_id')) {
      $collection = $this->getCollection()
                         ->addFieldToFilter('directory_id', $directoryId)
                         ->addFieldToFilter('is_active', 1);
      return $collection;
    }
  }
}