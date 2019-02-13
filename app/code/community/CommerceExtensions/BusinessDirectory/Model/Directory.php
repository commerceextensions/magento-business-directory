<?php
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
 
class CommerceExtensions_BusinessDirectory_Model_Directory extends Mage_Core_Model_Abstract
{
  protected function _construct()
  {
    $this->_init('businessdirectory/directory');
  }

  public function checkIdentifier($identifier, $storeId)
  {
    return $this->_getResource()->checkIdentifier($identifier, $storeId);
  }

  public function getUrl($directoryId = null, $storeId = null)
  {
    $storeId   = is_null($storeId) ? Mage::app()->getStore()->getStoreId() : $storeId;
    $baseUrl   = Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, $storeId);
    $suffix    = Mage::getStoreConfig('businessdirectory/frontend/url_suffix');
    $directory = $this->setStoreId(Mage::app()->getStore()->getId())->load($directoryId);
    return $baseUrl . $directory->getIdentifier() . $suffix;
  }

  public function getDirectory($directoryId = null)
  {
    if($directoryId) {
      $directory = $this->setStoreId(Mage::app()->getStore()->getId())
                        ->load($directoryId);
      return $directory;
    }

    if(!Mage::registry('directory')) {
      if($directoryId = Mage::app()->getRequest()->getParam('directory_id')) {
        $directory = $this->setStoreId(Mage::app()->getStore()->getId())
                          ->load($directoryId);
        Mage::register('directory', $directory);
        return $directory;
      }
    } else {
      return Mage::registry('directory');
    }
  }

  public function _getRedirectBackUrl()
  {
    $_customerSession = Mage::getSingleton('customer/session');
    if($_customerSession->getLastDirectoryUrlViewed()) {
      return Mage::getUrl() . ltrim($_customerSession->getLastDirectoryUrlViewed(), '/');
    }

    if($_customerSession->getDirectoryIdViewed()) {
      return $this->getUrl($_customerSession->getDirectoryIdViewed());
    }
  }

}