<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Url extends Mage_Core_Model_Abstract
{
  public function createUrl($listingData, CommerceExtensions_BusinessDirectory_Model_Directory $directory)
  {
    // if listing does not contain url key, convert listing_name into url key
    $replacements = array("'s" => "s", "," => "", "." => "");
    $original     = array_keys($replacements);
    $new          = array_values($replacements);

    $urlKey = str_ireplace($original, $new, $listingData['listing_name']);

    if($directory->getAppendLocationToUrl()) {
      // if directory is configured to automatically append location to url
      $urlLocale = array();
      if(array_key_exists('listing_city', $listingData) && isset($listingData['listing_city'])) {
        $urlLocale['listing_city'] = $listingData['listing_city'];
      }
      if(array_key_exists('listing_state', $listingData) && isset($listingData['listing_state'])) {
        if($directory->getIsUsOnly()) {
          $listingData['listing_state'] = $state = Mage::helper('businessdirectory')->abbreviateStateName($listingData['listing_state']);
        }
        $urlLocale['listing_state'] = $listingData['listing_state'];
      }
      if(!empty($urlLocale)) {
        $urlKey = $urlKey . ' ' . implode(' ', $urlLocale);
      }
    }
    $urlKey = Mage::getModel('catalog/product_url')->formatUrlKey($urlKey);
    return $urlKey;
  }
}