<?php

class CommerceExtensions_BusinessDirectory_Helper_Listing extends Mage_Core_Helper_Abstract
{
  const XML_PATH_NO_ROUTE_PAGE   = 'web/default/cms_no_route';
  const XML_PATH_NO_COOKIES_PAGE = 'web/default/cms_no_cookies';
  const XML_PATH_HOME_PAGE       = 'web/default/cms_home_page';

  public function renderPage(Mage_Core_Controller_Front_Action $action, $listingId = null)
  {
    return $this->_renderPage($action, $listingId);
  }

  protected function _renderPage(Mage_Core_Controller_Varien_Action $action, $listingId = null, $renderLayout = true)
  {
    $listing = Mage::getSingleton('businessdirectory/directory_listing')->load($listingId);
    if(!is_null($listingId) && $listingId !== $listing->getListingId()) {
      $delimeterPosition = strrpos($listingId, '|');
      if($delimeterPosition) {
        $listingId = substr($listingId, 0, $delimeterPosition);
      }

      $directory = Mage::getSingleton('businessdirectory/directory');
      $directory->setStoreId(Mage::app()->getStore()->getId());
      if(!$directory->load($listing->getDirectoryId())) {
        return false;
      }
    }

    $directory = Mage::getSingleton('businessdirectory/directory');
    $directory->setStoreId(Mage::app()->getStore()->getId());
    if(!$directory->load($listing->getDirectoryId())) {
      return false;
    }

    if(!$listing->getListingId()) {
      return false;
    }

    $inRange = Mage::app()->getLocale()
                   ->isStoreDateInInterval(null, $listing->getCustomThemeFrom(), $listing->getCustomThemeTo());

    if($listing->getCustomTheme()) {
      if($inRange) {
        list($package, $theme) = explode('/', $listing->getCustomTheme());
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
      }
    }

    $action->getLayout()->getUpdate()
           ->addHandle('default')
           ->addHandle('BUSINESSDIRECTORY_LISTING_' . $listing->getListingId())
           ->addHandle('businessdirectory_listing');
    if($listing->getIsFeatured()) {
      $action->getLayout()->getUpdate()
             ->addHandle('businessdirectory_featured_listing');
    }
    $action->addActionLayoutHandles();

    if($listing->getRootTemplate()) {
      $handle = ($listing->getCustomRootTemplate()
                 && $listing->getCustomRootTemplate() != 'empty'
                 && $inRange) ? $listing->getCustomRootTemplate() : $listing->getRootTemplate();
      $action->getLayout()->helper('page/layout')->applyHandle($handle);
    }

    Mage::dispatchEvent('businessdirectory_directory_listing_page_render', array('listing' => $listing, 'controller_action' => $action));

    $action->loadLayoutUpdates();
    $layoutUpdate = ($listing->getCustomLayoutUpdateXml() && $inRange) ? $listing->getCustomLayoutUpdateXml() : $listing->getLayoutUpdateXml();
    $action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
    $action->generateLayoutXml()->generateLayoutBlocks();

    if($listing->getRootTemplate()) {
      $action->getLayout()->helper('page/layout')
             ->applyTemplate($listing->getRootTemplate());
    }

    if($renderLayout) {
      $action->renderLayout();
    }
    return true;
  }

  public function renderPageExtended(Mage_Core_Controller_Varien_Action $action, $directoryId = null, $renderLayout = true)
  {
    return $this->_renderPage($action, $directoryId, $renderLayout);
  }

  public function getPageUrl($listingId = null)
  {
    $listing = Mage::getModel('businessdirectory/directory_listing');
    if(!is_null($listingId) && $listingId !== $listing->getListingId()) {
      $directory = Mage::getModel('businessdirectory/directory')->load($listing->getDirectoryId());
      $directory->setStoreId(Mage::app()->getStore()->getId());
      if(!$directory->load($directoryId)) {
        return null;
      }
    }

    if(!$listing->getListingId()) {
      return null;
    }

    return Mage::getUrl(null, array('_direct' => $listing->getIdentifier()));
  }

  public function getUrl($listingId)
  {
    return Mage::getModel('businessdirectory/directory_listing')->getUrl($listingId);
  }

  public function canShowUpdateButton($listing)
  {
    if(Mage::getSingleton('customer/session')->isLoggedIn()) {
      $customer = Mage::getSingleton('customer/session')->getCustomer();
      if($customer->getId() == $listing->getCustomerId()) {
        return true;
      }
      return false;
    }
    return false;
  }

}