<?php

class CommerceExtensions_BusinessDirectory_Controller_DirectoryRouter extends Mage_Core_Controller_Varien_Router_Abstract
{

  public function initControllerRouters($observer)
  {
    /* @var $front Mage_Core_Controller_Varien_Front */
    $front = $observer->getEvent()->getFront();
    $front->addRouter('directory', $this);
  }

  public function match(Zend_Controller_Request_Http $request)
  {
    if(!Mage::isInstalled()) {
      Mage::app()->getFrontController()->getResponse()
          ->setRedirect(Mage::getUrl('install'))
          ->sendResponse();
      exit;
    }

    $suffix     = Mage::getStoreConfig('businessdirectory/frontend/url_suffix');
    $identifier = trim($request->getPathInfo(), '/');
    $identifier = rtrim($identifier, $suffix);

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

    $directory = Mage::getModel('businessdirectory/directory');

    $directoryId = $directory->checkIdentifier($identifier, Mage::app()->getStore()->getId());
    if(!$directoryId) {
      return false;
    }

    $identifier = $identifier . $suffix;
    $request->setModuleName('businessdirectory')
            ->setControllerName('listing')
            ->setActionName('list')
            ->setParam('directory_id', $directoryId);
    $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier);

    return true;
  }
}
