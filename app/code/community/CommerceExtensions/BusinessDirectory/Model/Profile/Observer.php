<?php

class CommerceExtensions_BusinessDirectory_Model_Profile_Observer extends Varien_Event_Observer
{
  public function redirectAfterRegister($observer)
  {
    if(Mage::app()->getRequest()->getParam('listing_id') || Mage::app()->getRequest()->getParam('directory_id')) {
      $_session = Mage::getSingleton('customer/session');
      $action   = $_session->getDirectoryAction();

      if(Mage::app()->getRequest()->getParam('listing_id')) {
        $listingId = Mage::app()->getRequest()->getParam('listing_id');
        Mage::app()->getRequest()->setParam('success_url', Mage::getUrl($action, array('listing_id' => $listingId)));
        Mage::app()->getRequest()->setParam('error_url', Mage::helper('core/url')->getCurrentUrl());
        $url = Mage::getUrl($action, array('listing_id' => $listingId));
        $_session->setBeforeAuthUrl($url);
      } elseif(Mage::app()->getRequest()->getParam('directory_id')) {
        $directoryId = Mage::app()->getRequest()->getParam('directory_id');
        Mage::app()->getRequest()->setParam('success_url', Mage::getUrl($action, array('directory_id' => $directoryId)));
        Mage::app()->getRequest()->setParam('error_url', Mage::helper('core/url')->getCurrentUrl());
        $url = Mage::getUrl($action, array('directory_id' => $directoryId));
        $_session->setBeforeAuthUrl($url);
      }
    }
  }

  public function redirectAfterLogin($observer)
  {
    if(Mage::app()->getRequest()->getParam('listing_id') || Mage::app()->getRequest()->getParam('directory_id')) {
      $_session  = Mage::getSingleton('customer/session');
      $action    = $_session->getDirectoryAction();
      $response1 = $observer->getResponse();
      echo '<pre>', print_r($response1), '</pre>';
      die;

      if(Mage::app()->getRequest()->getParam('listing_id')) {
        $listingId = Mage::app()->getRequest()->getParam('listing_id');
        $url       = Mage::getUrl($action, array('listing_id' => $listingId));
        $_session->setAfterAuthUrl($url);
      } elseif(Mage::app()->getRequest()->getParam('directory_id')) {
        $directoryId = Mage::app()->getRequest()->getParam('directory_id');
        $url         = Mage::getUrl($action, array('directory_id' => $directoryId));
        $_session->setAfterAuthUrl($url);
      }
    }
  }

}