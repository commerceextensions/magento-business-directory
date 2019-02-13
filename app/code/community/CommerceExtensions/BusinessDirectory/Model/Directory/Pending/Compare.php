<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Compare extends CommerceExtensions_BusinessDirectory_Model_Directory_Listing
{
  protected $_pendingDataId;
  protected $_pendingData;
  protected $_listingId;
  protected $_listingData = null;

  public function __construct($pendingId = null)
  {
    if($pendingId) {
      $this->_setData($pendingId);
    }
    parent::__construct();
  }

  protected function _setData($pendingId)
  {
    if(!$this->_pendingData) {
      $this->_pendingData = Mage::getModel('businessdirectory/directory_listing_submit')->load($pendingId);
    }
    $this->_pendingDataId = $this->_pendingData->getId();
    $this->_listingId     = $this->_pendingData->getListingId();
  }

  public function getCurrentData()
  {
    if(!$this->_listingData) {
      $_listing = $this->getListing($this->_listingId);
      if($_listing) {
        return $this->_listingData = $_listing->toArray();
      }
    }
    return $this->_listingData;
  }

  public function getPendingData()
  {
    return $this->_pendingData->toArray();
  }

  public function getComparisonArray()
  {
    $currentData   = $this->getCurrentData();
    $pendingData   = $this->getPendingData();
    $booleanFields = array('is_featured');
    $array         = array();

    $helper    = Mage::helper('cms');
    $processor = $helper->getPageTemplateProcessor();

    foreach($this->getComparisonFieldsArray() as $field => $label) {
      if(in_array($field, $booleanFields)) {
        $array['current'][$field] = ($currentData[$field] == 0) ? 'No' : 'Yes';
        $array['pending'][$field] = ($pendingData[$field] == 0) ? 'No' : 'Yes';
      } elseif($field == 'content') {
        $array['current'][$field] = $processor->filter($currentData[$field]);
        $array['pending'][$field] = $processor->filter($pendingData[$field]);
      } else {
        $array['current'][$field] = $currentData[$field];
        $array['pending'][$field] = $pendingData[$field];
      }
    }
    return $array;
  }

  public function getComparisonFieldsArray()
  {
    return array(
      'identifier'           => 'Listing Url Key',
      'listing_name'         => 'Listing Name',
      'address_line_one'     => 'Address Line 1',
      'address_line_two'     => 'Address Line 2',
      'listing_city'         => 'City',
      'listing_state'        => 'State/Province',
      'listing_zip_code'     => 'Postal Code',
      'listing_country'      => 'Country',
      'listing_email'        => 'Email',
      'listing_website'      => 'Website',
      'listing_contact_name' => 'Contact Name',
      'listing_phone'        => 'Phone Number',
      'listing_fax'          => 'Fax Number',
      'is_featured'          => 'Featured Listing',
      'backlink'             => 'Verification Backlink',
      'facebook_page'        => 'Facebook Page',
      'twitter_page'         => 'Twitter Page',
      'google_plus_page'     => 'Google+ Page',
      'listing_image'        => 'Image',
      'listing_comments'     => 'Additional Info',
      'content'              => 'Profile Content',
    );
  }

}