<?php
/**
 * Observer.php
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

class CommerceExtensions_BusinessDirectory_Model_Observer extends Varien_Event_Observer
{
  public function listingFilter($observer)
  {
    $collection = $observer->getEvent()->getCollection();

    $allowableFilters = array_keys(Mage::getModel('businessdirectory/directory_staticfilter')->ALLOWABLE_FILTERS);
    $currentParams    = Mage::app()->getRequest()->getParams();
    foreach($currentParams as $paramname => $paramdata) {
      if(in_array($paramname, $allowableFilters)) {
        $collection->addFieldToFilter($paramname, $paramdata);
      }
    }

    // if the directory is geocoded & we can obtain a starting point, this will do some distance figuring for us
    $coordinates = $this->_getGeocodingStartPoint();
    if(!empty($coordinates)) {
      $units = Mage::getModel('businessdirectory/directory')->getDirectory()->getDistanceUnits();
      $units = ($units == 'miles') ? 3959 : 6371;

      $latitude  = $coordinates['Latitude'];
      $longitude = $coordinates['Longitude'];

      $collection->getSelect()
                 ->columns(array('distance' => new Zend_Db_Expr("({$units} * ACOS(COS(RADIANS({$latitude})) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS({$longitude})) + SIN(RADIANS({$latitude})) * SIN(RADIANS(latitude))))")));
      $collection->getSelect()
                 ->order('distance', Varien_Db_Select::SQL_ASC);
    }

    if($this->_directoryHasGeocoding() == 'false') {
      // search location like this if it is not a geocoded directory
      if($locationSearchText = Mage::app()->getRequest()->getParam('directory_location')) {
        $locationSearchText = strtoupper(preg_replace("/[^A-Za-z0-9 ]/", '', $locationSearchText));
        $collection->getSelect()
                   ->where(new Zend_Db_Expr("UPPER(REPLACE(CONCAT_WS('',address_line_one,address_line_two,listing_city,listing_state,listing_zip_code,listing_country),'[0-9]+','')) LIKE '%{$locationSearchText}%'"));
      }
    }

    $collection->getSelect()->order('listing_country', Varien_Db_Select::SQL_ASC)
               ->order('listing_state', Varien_Db_Select::SQL_ASC)
               ->order('listing_city', Varien_Db_Select::SQL_ASC)
               ->order('listing_name', Varien_Db_Select::SQL_ASC);
    // if the user uses the "name" search box
    if($name = Mage::app()->getRequest()->getParam('directory_name')) {
      $collection->addFieldToFilter('listing_name', array("like" => "%{$name}%"));
    }
  }

  public function featuredListingFilter($observer)
  {
    $collection = $observer->getEvent()->getCollection();
    $collection->addFieldToFilter('is_featured', 1);

    $allowableFilters = array_keys(Mage::getModel('businessdirectory/directory_staticfilter')->ALLOWABLE_FILTERS);
    $currentParams    = array_keys(Mage::app()->getRequest()->getParams());
    if(in_array('listing_state', $currentParams)) {
      $collection->addFieldToFilter('listing_state', Mage::app()->getRequest()->getParam('listing_state'));
    }

    // if the directory is geocoded & we can obtain a starting point, this will do some distance figuring for us
    $coordinates = $this->_getGeocodingStartPoint();
    if(!empty($coordinates)) {
      $units = Mage::getModel('businessdirectory/directory')->getDirectory()->getDistanceUnits();
      $units = ($units == 'miles') ? 3959 : 6371;

      $latitude  = $coordinates['Latitude'];
      $longitude = $coordinates['Longitude'];

      $collection->getSelect()
                 ->columns(array('distance' => new Zend_Db_Expr("({$units} * ACOS(COS(RADIANS({$latitude})) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS({$longitude})) + SIN(RADIANS({$latitude})) * SIN(RADIANS(latitude))))")));
      $collection->getSelect()->order('distance', Varien_Db_Select::SQL_ASC);
    } else {
      $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
    }
  }

  protected function _directoryHasGeocoding()
  {
    if(!Mage::registry('directory_has_geocoding')) {
      $directoryId = Mage::app()->getRequest()->getParam('directory_id');

      $resource  = Mage::getSingleton('core/resource');
      $adapter   = $resource->getConnection('core_read');
      $statement = $adapter->prepare("SELECT latitude FROM {$resource->getTableName('businessdirectory/directory_listing')} WHERE latitude IS NOT NULL AND latitude != '0.000000' AND directory_id = {$directoryId} AND is_active = 1");
      $statement->execute();
      $geocoded = $statement->fetchAll(PDO::FETCH_COLUMN);

      $statement = $adapter->prepare("SELECT COUNT(*) FROM {$resource->getTableName('businessdirectory/directory_listing')} WHERE directory_id = {$directoryId} AND is_active = 1");
      $statement->execute();
      $count            = $statement->fetchAll(PDO::FETCH_COLUMN);
      $countAllListings = (int)$count[0];

      $percentageGeocoded = (count($geocoded) / $countAllListings) * 100;
      $percentageGeocoded = number_format((float)$percentageGeocoded, 2, '.', '');

      if($percentageGeocoded < 25) { // if directory is not at least 25% geocoded, it will be treated as a non-geocoded directory
        Mage::register('directory_has_geocoding', 'false');
      } else {
        Mage::register('directory_has_geocoding', 'true');
      }
      return Mage::registry('directory_has_geocoding');
    } else {
      return Mage::registry('directory_has_geocoding');
    }
  }

  protected function _getGeocodingStartPoint()
  {
    $coordinates = Mage::registry('geocode_start_point');
    if(!empty($coordinates)) {
      return $coordinates;
    } else {
      Mage::unregister('geocode_start_point');
    }

    if(array_key_exists('directory_location', Mage::app()->getRequest()->getParams()) && $this->_directoryHasGeocoding() == 'true') {
      // if user types in a search location, get the coordinates of the searched location to use as a start point
      $coordinates = Mage::getModel('businessdirectory/api_geocode')->getCoordinates(Mage::app()->getRequest()->getParams());
    } elseif(Mage::getStoreConfig('businessdirectory/geocode/ipinfodb_api_key') && $this->_directoryHasGeocoding() == 'true') {
      // otherwise, is they are not searching by location, use the user's actual location as a start point
      $coordinates = Mage::getModel('businessdirectory/api_geocode_ipinfodb')->getCoordinates();
    }
    Mage::register('geocode_start_point', $coordinates);
    return $coordinates;
  }

  public function test($observer)
  {
    $event = $observer->getEvent();
    echo '<pre>', print_r($event), '</pre>';
    die;
  }

}