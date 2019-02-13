<?php

class CommerceExtensions_BusinessDirectory_ListingController extends Mage_Core_Controller_Front_Action
{
  public function listAction()
  {
    $directoryId = $this->getRequest()->getParam('directory_id', $this->getRequest()->getParam('directory_id', false));

    $_customerSession = Mage::getSingleton('customer/session');
    $_customerSession->setDirectoryIdViewed($directoryId);
    $_customerSession->setLastDirectoryUrlViewed($this->getRequest()->getPathInfo());
    if(!Mage::helper('businessdirectory/directory')->renderPage($this, $directoryId)) {
      $this->_forward('noRoute');
      return;
    }
  }

  public function searchAction()
  {
    $directoryId  = $this->getRequest()->getParam('directory_id');
    $directoryUrl = Mage::getModel('businessdirectory/directory')->getUrl($directoryId);

    $_customerSession = Mage::getSingleton('customer/session');
    $_customerSession->setDirectoryIdViewed($directoryId);
    $_customerSession->setLastDirectoryUrlViewed($this->getRequest()->getPathInfo());

    $searchedName     = $this->getRequest()->getParam('directory_name');
    $searchedLocation = $this->getRequest()->getParam('directory_location');

    $params = array();
    if($searchedName) {
      $params['directory_name'] = $searchedName;
    }

    if($searchedLocation) {
      $params['directory_location'] = $searchedLocation;
    }

    $query = http_build_query($params);

    if($query) {
      $directoryUrl = $directoryUrl . '?' . $query;
    }
    $this->_redirectUrl($directoryUrl);
    return;
  }

  public function resetAction()
  {
    $directoryId  = $this->getRequest()->getParam('directory_id');
    $directoryUrl = Mage::getModel('businessdirectory/directory')->getUrl($directoryId);

    $_customerSession = Mage::getSingleton('customer/session');
    $_customerSession->setDirectoryIdViewed($directoryId);
    $_customerSession->setLastDirectoryUrlViewed($this->getRequest()->getPathInfo());

    $this->_redirectUrl($directoryUrl);
    return;
  }

  public function redirectAction()
  {
    $this->loadLayout();
    $this->renderLayout();
    if(!$listingId = $this->getRequest()->getParam('listing_id')) {
      $this->_forward('noRoute');
      return;
    } else {
      $link = $this->getRequest()->getParam('link');
      $url  = Mage::getModel('businessdirectory/directory_listing')
                  ->load($listingId)
                  ->getData($link);
      $this->_redirectUrl($url);
      return;
    }
  }

  public function ajaxNamesAction()
  {
    $term        = $this->getRequest()->getParam('term');
    $directoryId = $this->getRequest()->getParam('directory_id');
    $listings    = Mage::getModel('businessdirectory/directory_listing')->getListings($directoryId);
    $listings->getSelect()->where("REPLACE(listing_name,' ','') LIKE ?", "%" . str_replace(' ', '', $term) . "%");
    $listings->getSelect()->order('listing_name', 'ASC');
    $listings->getSelect()->limit(20);
    $result = array();
    foreach($listings as $listing) {
      $data          = array();
      $data['name']  = $listing->getListingName();
      $data['city']  = $listing->getListingCity() ? $listing->getListingCity() : null;
      $data['state'] = $listing->getListingState() ? $listing->getListingState() : null;
      $data['url']   = $listing->getIdentifier() ? Mage::helper('businessdirectory/listing')->getUrl($listing->getListingId()) : null;
      $result[]      = $data;
    }
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function profileAction()
  {
    $listingId = $this->getRequest()->getParam('listing_id', $this->getRequest()->getParam('listing_id', false));

    $_customerSession = Mage::getSingleton('customer/session');
    $_customerSession->setDirectoryIdViewed($this->getRequest()->getParam('directory_id'));
    $_customerSession->setListingIdViewed($listingId);
    $_customerSession->setLastDirectoryUrlViewed($this->getRequest()->getPathInfo());

    if(!Mage::helper('businessdirectory/listing')->renderPage($this, $listingId)) {
      $this->_forward('noRoute');
      return;
    }
  }

  public function buttonAction()
  {
    $this->loadLayout();
    $_data        = $this->getRequest()->getParams();
    $_directoryId = $_data['directoryId'];
    unset($_data['directoryId']);
    $_listingIds = array_map(array($this, 'extractId'), $_data);
    $_listings   = Mage::getModel('businessdirectory/directory_listing')->getCollection()
                       ->addFieldToFilter('directory_id', array('in' => $_directoryId))
                       ->addFieldToFilter('listing_id', array('in' => $_listingIds))
                       ->load();

    $_directory = Mage::getModel('businessdirectory/directory')->getDirectory($_directoryId);
    $block      = $this->getLayout()->createBlock('businessdirectory/directory_listing_button');
    $block->setCanClaimProfile($_directory->getCanClaimProfile());

    $_response = array();
    foreach($_listings as $_listing) {
      $_response["#button-" . $_listing->getListingId()] = $block->getButtonHtml($_listing);
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_response));
    return;
  }

  public function extractId($string)
  {
    return str_ireplace(array('button-', 'item-'), '', $string);
  }

}