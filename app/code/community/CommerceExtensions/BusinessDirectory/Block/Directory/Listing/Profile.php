<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Profile extends Mage_Core_Block_Abstract
{
  public $directory;
  public $listing;

  public function getDirectory($directoryId = null)
  {
    if(!$this->directory) {
      $this->directory = Mage::getModel('businessdirectory/directory')->getDirectory($directoryId);
      return $this->directory;
    }
    return $this->directory;
  }

  public function getListing()
  {
    $listingId = $this->getRequest()->getParam('listing_id');
    if(!$this->listing) {
      $this->listing = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
      return $this->listing;
    }
    return $this->listing;
  }

  protected function _prepareLayout()
  {
    $listing   = $this->getListing();
    $directory = $this->getDirectory($listing->getDirectoryId());

    if(Mage::getStoreConfig('businessdirectory/frontend/show_breadcrumbs') && $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
      $breadcrumbs->addCrumb('home',
                             array(
                               'label' => $this->__('Home'),
                               'title' => $this->__('Go to Home Page'),
                               'link'  => Mage::getBaseUrl()
                             )
      );

      if($directory->getContentHeading()) {
        $breadcrumbs->addCrumb('businessdirectory_directory',
                               array(
                                 'label' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($directory->getContentHeading())),
                                 'title' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($directory->getContentHeading())),
                                 'link'  => $directory->getUrl()
                               )
        );
      }
      $breadcrumbs->addCrumb('businessdirectory_listing',
                             array(
                               'label' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($listing->getListingName())),
                               'title' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($listing->getListingName()))
                             )
      );
    }

    $root = $this->getLayout()->getBlock('root');
    if($root) {
      $root->addBodyClass('directory-listing-profile');
    }

    $head = $this->getLayout()->getBlock('head');
    if($head) {
      $head->setTitle($this->_getTitle());
      $head->setKeywords($this->_getMetaKeywords());
      $head->setDescription($this->_getMetaDescription());
      $head->addLinkRel('canonical', $listing->getUrl());
    }
    return parent::_prepareLayout();
  }

  protected function _getTitle()
  {
    $possibleFields = $this->getListing()->toArray();
    $title          = $this->getDirectory()->getProfileTitleTagStructure();
    if(is_null($title) || $title == '') {
      return $this->helper('businessdirectory/tags')->processCustomTags($this->getListing()->getListingName(), $possibleFields);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($title, $possibleFields);
  }

  protected function _getMetaDescription()
  {
    $listingDescription = $this->getListing()->getMetaDescription();
    $possibleFields     = $this->getListing()->toArray();
    if(is_null($listingDescription) || $listingDescription == '') {
      return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getProfileDefaultMetaDescription(), $possibleFields);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($listingDescription, $possibleFields);
  }

  protected function _getMetaKeywords()
  {
    $listingKeywords = $this->getListing()->getMetaKeywords();
    $possibleFields  = $this->getListing()->toArray();
    if(is_null($listingKeywords) || $listingKeywords == '') {
      return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getProfileDefaultMetaKeywords(), $possibleFields);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($listingKeywords, $possibleFields);
  }

}