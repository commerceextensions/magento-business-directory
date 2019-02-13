<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Profile_Claim_Setup extends Mage_Core_Block_Template
{
  public $listing;
  public $directory;

  public function getSubmittedData()
  {
    // if error with submission, put the posted data into a varien data object
    // so that the form fields remain filled and the user doesn't have to
    // redo the whole form
    if($data = Mage::getSingleton('core/session')->getData($this->_getFormName())) {
      return $data;
    }
    return null;
  }

  public function getListing()
  {
    if($this->getSubmittedData()) {
      return $this->getSubmittedData();
    }

    if(!$this->listing) {
      if($listingId = $this->getRequest()->getParam('listing_id')) {
        $this->listing = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
        return $this->listing;
      } else
        return null;
    }
    return $this->listing;
  }

  public function getCustomer()
  {
    if(Mage::getSingleton('customer/session')->isLoggedIn()) {
      return Mage::getSingleton('customer/session')->getCustomer();
    }
    return null;
  }

  public function getCustomerId()
  {
    if($this->getCustomer()) {
      return $this->getCustomer()->getId();
    }
    return null;
  }

  public function getDirectory()
  {
    if(!$this->directory) {
      if($directoryId = $this->getRequest()->getParam('directory_id')) {
        $this->directory = Mage::getModel('businessdirectory/directory')->load($directoryId);
        return $this->directory;
      } else
        return null;
    }
    return $this->directory;
  }

  public function getDirectoryId()
  {
    if($this->getDirectory()) {
      return $this->getDirectory()->getDirectoryId();
    } elseif($listingId = $this->getRequest()->getParam('listing_id')) {
      return Mage::getModel('businessdirectory/directory_listing')
                 ->load($listingId)
                 ->getDirectoryId();
    }
  }

  public function getCountries()
  {
    return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
  }

  public function getListingId()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingId();
    }
    return null;
  }

  public function getListingName()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingName();
    }
    return null;
  }

  public function getAddressLineOne()
  {
    if($this->getListing()) {
      return $this->getListing()->getAddressLineOne();
    }
    return null;
  }

  public function getAddressLineTwo()
  {
    if($this->getListing()) {
      return $this->getListing()->getAddressLineTwo();
    }
    return null;
  }

  public function getListingCity()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingCity();
    }
    return null;
  }

  public function getListingState()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingState();
    }
    return null;
  }

  public function getListingCountry()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingCountry();
    }
    return null;
  }

  public function getListingZipCode()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingZipCode();
    }
    return null;
  }

  public function getListingContactName()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingContactName();
    }
    return null;
  }

  public function getListingEmail()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingEmail();
    }
    return null;
  }

  public function getListingWebsite()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingWebsite();
    }
    return null;
  }

  public function getListingPhone()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingPhone();
    }
    return null;
  }

  public function getListingFax()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingFax();
    }
    return null;
  }

  public function getFacebookPage()
  {
    if($this->getListing()) {
      return $this->getListing()->getFacebookPage();
    }
    return null;
  }

  public function getTwitterPage()
  {
    if($this->getListing()) {
      return $this->getListing()->getTwitterPage();
    }
    return null;
  }

  public function getGooglePlusPage()
  {
    if($this->getListing()) {
      return $this->getListing()->getGooglePlusPage();
    }
    return null;
  }

  public function isFeatured()
  {
    if($this->getListing()) {
      if(is_null($this->getListing()->getIsFeatured())) {
        return 1;
      }
      return $this->getListing()->getIsFeatured();
    }
    return 1;
  }

  public function getBacklink()
  {
    if($this->getListing()) {
      return $this->getListing()->getBacklink();
    }
    return null;
  }

  public function getAction()
  {
    return $this->getRequest()->getActionName();
  }

  public function getContent()
  {
    if($this->getListing()) {
      return $this->getListing()->getContent();
    }
    return null;
  }

  public function getListingComments()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingComments();
    }
    return null;
  }

  public function getListingImage()
  {
    if($this->getListing()) {
      return $this->getListing()->getListingImage();
    }
    return null;
  }

  public function getPostActionUrl()
  {
    return $this->getUrl('*/*/submit', array('_current' => true, '_secure' => true));
  }

  protected function _getFormName()
  {
    $params = $this->getRequest()->getParams();
    $values = array();
    foreach($params as $key => $value) {
      $values[] = $key . '_' . $value;
    }
    return $this->getAction() . '_' . implode('_', $values);
  }

  public function getBackUrl()
  {
    $baseUrl = parent::getBackUrl();
    if($listingId = $this->getRequest()->getParam('listing_id')) {
      $directoryId = Mage::getModel('businessdirectory/directory_listing')
                         ->load($listingId)
                         ->getDirectoryId();
      $url         = Mage::getModel('businessdirectory/directory')
                         ->load($directoryId)
                         ->getUrl();
      return $url;
    } elseif($directoryId = $this->getRequest()->getParam('directory_id')) {
      $url = Mage::getModel('businessdirectory/directory')
                 ->load($directoryId)
                 ->getUrl();
      return $url;
    } else {
      return parent::getPostActionUrl();
    }
  }

}