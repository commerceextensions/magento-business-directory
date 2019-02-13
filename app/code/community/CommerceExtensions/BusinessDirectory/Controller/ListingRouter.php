<?php

class CommerceExtensions_BusinessDirectory_Controller_ListingRouter extends Mage_Core_Controller_Varien_Router_Abstract
{

  public function initControllerRouters($observer)
  {
    /* @var $front Mage_Core_Controller_Varien_Front */
    $front = $observer->getEvent()->getFront();
    $front->addRouter('listing', $this);
  }

  public function match(Zend_Controller_Request_Http $request)
  {
    if(!Mage::isInstalled()) {
      Mage::app()->getFrontController()->getResponse()
          ->setRedirect(Mage::getUrl('install'))
          ->sendResponse();
      exit;
    }

    $suffix              = Mage::getStoreConfig('businessdirectory/frontend/url_suffix');
    $identifier          = explode('/', trim($request->getPathInfo(), '/'));
    $directoryIdentifier = trim($identifier[0], '/');
    $identifier          = str_ireplace($suffix, '', end($identifier));

    $condition = new Varien_Object(array(
                                     'identifier' => $identifier,
                                     'continue'   => true
                                   ));
    Mage::dispatchEvent('businessdirectory_controller_router_match_before', array(
      'router'    => $this,
      'condition' => $condition
    ));
    $identifier = $condition->getIdentifier();

    if($condition->getRedirectUrl()) {
      Mage::app()->getFrontController()->getResponse()
          ->setRedirect($condition->getRedirectUrl())
          ->sendResponse();
      $request->setDispatched(true);
      return true;
    }

    if(!$condition->getContinue()) {
      return false;
    }

    $directoryId = Mage::getModel('businessdirectory/directory')
                       ->checkIdentifier($directoryIdentifier, Mage::app()->getStore()->getStoreId());
    if(!$directoryId) {
      return false;
    }

    $listing   = Mage::getModel('businessdirectory/directory_listing');
    $listingId = $listing->checkIdentifier($identifier, $directoryId);

    if(!$listingId) {
      return false;
    }

    $listing = $listing->load($listingId);
    if(!$listing->getIsActive()) {
      $directory = Mage::getModel('businessdirectory/directory')->load($directoryId);
      Mage::app()
          ->getResponse()
          ->setRedirect($directory->getUrl(), 301)
          ->sendResponse();
      exit;
    }

    $stores = Mage::getResourceModel('businessdirectory/directory')->lookupStoreIds($listing->getDirectoryId());
    if(!in_array(Mage_Core_Model_App::ADMIN_STORE_ID, $stores)) {
      if(!in_array(Mage::app()->getStore()->getId(), $stores)) {
        return false; // if not a directory enabled store, don't show the listing either
      }
    }

    $identifier = $identifier . $suffix;
    $request->setModuleName('businessdirectory')
            ->setControllerName('listing')
            ->setActionName('profile')
            ->setParam('listing_id', $listingId)
            ->setParam('directory_id', $directoryId);
    $request->setAlias(
      Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
      $identifier
    );

    return true;
  }
}
