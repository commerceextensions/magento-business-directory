<?php

class CommerceExtensions_BusinessDirectory_Model_Api_Geocode extends Mage_Core_Model_Abstract
{
  public function getCoordinates(array $listing = array())
  {
    $geocode = Mage::getModel('businessdirectory/api_geocode_googlemaps')->getCoordinates($listing);
    if(!empty($geocode)) {
      return $geocode;
    } else {
      // if googlemaps doesnt product a result, try bing maps
      return Mage::getModel('businessdirectory/api_geocode_bingmaps')->getCoordinates($listing);
    }
  }
}