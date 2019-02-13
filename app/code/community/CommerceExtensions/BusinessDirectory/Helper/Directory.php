<?php

class CommerceExtensions_BusinessDirectory_Helper_Directory extends Mage_Core_Helper_Abstract
{
  const XML_PATH_NO_ROUTE_PAGE   = 'web/default/cms_no_route';
  const XML_PATH_NO_COOKIES_PAGE = 'web/default/cms_no_cookies';
  const XML_PATH_HOME_PAGE       = 'web/default/cms_home_page';

  public function renderPage(Mage_Core_Controller_Front_Action $action, $directoryId = null)
  {
    return $this->_renderPage($action, $directoryId);
  }

  protected function _renderPage(Mage_Core_Controller_Varien_Action $action, $directoryId = null, $renderLayout = true)
  {
    $directory = Mage::getSingleton('businessdirectory/directory');
    if(!is_null($directoryId) && $directoryId !== $directory->getDirectoryId()) {
      $delimeterPosition = strrpos($directoryId, '|');
      if($delimeterPosition) {
        $directoryId = substr($directoryId, 0, $delimeterPosition);
      }

      $directory->setStoreId(Mage::app()->getStore()->getId());
      if(!$directory->load($directoryId)) {
        return false;
      }
    }

    if(!$directory->getDirectoryId()) {
      return false;
    }

    $inRange = Mage::app()->getLocale()
                   ->isStoreDateInInterval(null, $directory->getCustomThemeFrom(), $directory->getCustomThemeTo());

    if($directory->getCustomTheme()) {
      if($inRange) {
        list($package, $theme) = explode('/', $directory->getCustomTheme());
        Mage::getSingleton('core/design_package')
            ->setPackageName($package)
            ->setTheme($theme);
      }
    }

    $action->getLayout()->getUpdate()
           ->addHandle('default')
           ->addHandle('BUSINESSDIRECTORY_DIRECTORY_' . $directory->getDirectoryId())
           ->addHandle('businessdirectory_directory');

    $action->addActionLayoutHandles();
    if($directory->getRootTemplate()) {
      $handle = ($directory->getCustomRootTemplate()
                 && $directory->getCustomRootTemplate() != 'empty'
                 && $inRange) ? $directory->getCustomRootTemplate() : $directory->getRootTemplate();
      $action->getLayout()->helper('page/layout')->applyHandle($handle);
    }

    Mage::dispatchEvent('businessdirectory_directory_page_render', array('directory' => $directory, 'controller_action' => $action));

    $action->loadLayoutUpdates();
    $layoutUpdate = ($directory->getCustomLayoutUpdateXml() && $inRange)
      ? $directory->getCustomLayoutUpdateXml() : $directory->getLayoutUpdateXml();
    $action->getLayout()->getUpdate()->addUpdate($layoutUpdate);
    $action->generateLayoutXml()->generateLayoutBlocks();

    $contentHeadingBlock = $action->getLayout()->getBlock('page_content_heading');
    if($contentHeadingBlock) {
      $contentHeading = $this->escapeHtml($directory->getContentHeading());
      $contentHeadingBlock->setContentHeading($contentHeading);
    }

    if($directory->getRootTemplate()) {
      $action->getLayout()->helper('page/layout')
             ->applyTemplate($directory->getRootTemplate());
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

  public function getPageUrl($directoryId = null)
  {
    $directory = Mage::getModel('businessdirectory/directory');
    if(!is_null($directoryId) && $directoryId !== $directory->getDirectoryId()) {
      $directory->setStoreId(Mage::app()->getStore()->getId());
      if(!$directory->load($directoryId)) {
        return null;
      }
    }

    if(!$directory->getDirectoryId()) {
      return null;
    }

    return Mage::getUrl(null, array('_direct' => $directory->getIdentifier()));
  }
}
