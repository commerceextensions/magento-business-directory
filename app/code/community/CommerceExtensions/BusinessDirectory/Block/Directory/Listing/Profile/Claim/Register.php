<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Profile_Claim_Register extends Mage_Customer_Block_Form_Register
{
  public function getPostActionUrl()
  {
    $baseUrl = parent::getPostActionUrl();
    if($listingId = $this->getRequest()->getParam('listing_id')) {
      return rtrim($baseUrl, '/') . '/listing_id/' . $listingId . '/';
    } elseif($directoryId = $this->getRequest()->getParam('directory_id')) {
      return rtrim($baseUrl, '/') . '/directory_id/' . $directoryId . '/';
    } else {
      return parent::getPostActionUrl();
    }
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