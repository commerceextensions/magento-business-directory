<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Profile_Claim_Login extends Mage_Customer_Block_Form_Login
{
  public $listing;
  public $directory;

  public function getListing()
  {
    if($listingId = $this->getRequest()->getParam('listing_id')) {
      if(!$this->listing) {
        $this->listing = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
        return $this->listing;
      }
      return $this->listing;
    }
    return null;
  }

  public function getDirectory()
  {
    if($directoryId = $this->getRequest()->getParam('directory_id')) {
      if(!$this->listing) {
        $this->directory = Mage::getModel('businessdirectory/directory')->load($directoryId);
        return $this->directory;
      }
      return $this->directory;
    }
    return null;
  }

  public function getCreateAccountUrl()
  {
    return $this->getUrl('*/*/register', array('_secure' => true, '_current' => true));
  }

}