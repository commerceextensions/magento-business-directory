<?php
/**
 * Featured.php
 * CommerceExtensions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Business Directory
 * @copyright  Copyright (c) 2003-2019 CommerceExtensions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
class CommerceExtensions_BusinessDirectory_Block_Directory_Featured extends Mage_Core_Block_Template
{
  protected $_listingCollection;

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
    if($this->getDirectory()->getDisplayFeaturedListings()) {

      $this->_listingCollection = Mage::getModel('businessdirectory/directory_listing')->getListings();
      Mage::dispatchEvent('businessdirectory_featured_collection_load_before', array('collection' => $this->_listingCollection));
      // here is where we can give a feature to allow the user to decide how many featured listings to display at once
      $this->_listingCollection->getSelect()->limit($this->getDirectory()->getFeaturedListingDisplayCount());
      $this->_listingCollection->load();
      return $this;
    } else {
      return array(); // if not displaying featured listings, return empty array
    }
  }

  protected function _beforeToHtml()
  {
    $this->_prepareData();
    return parent::_beforeToHtml();
  }

  public function getItems()
  {
    return $this->_listingCollection;
  }

  public function getShowCountry()
  {
    return $this->getDirectory()->getShowCountry();
  }

  public function canDisplayFeaturedListings()
  {
    return $this->getDirectory()->getDisplayFeaturedListings();
  }
}