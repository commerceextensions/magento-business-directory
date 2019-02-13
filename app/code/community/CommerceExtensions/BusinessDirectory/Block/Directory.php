<?php
/**
 * Directory.php
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
class CommerceExtensions_BusinessDirectory_Block_Directory extends Mage_Core_Block_Abstract
{
  protected $_filterModel;

  public function getDirectory()
  {
    $directory = Mage::getSingleton('businessdirectory/directory')->getDirectory();
    if($this->helper('businessdirectory')->isVersionEqualOrHigher('1.8')) {
      $this->addModelTags($directory);
    }
    return $directory;
  }

  public function getFilterModel()
  {
    if(!$this->_filterModel) {
      return $this->_filterModel = Mage::getModel('businessdirectory/directory_staticfilter');
    }
    return $this->_filterModel;
  }

  protected function _prepareLayout()
  {
    $directory = $this->getDirectory();
    if(Mage::getStoreConfig('businessdirectory/frontend/show_breadcrumbs') && $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
      $breadcrumbs->addCrumb('home',
                             array(
                               'label' => $this->__('Home'),
                               'title' => $this->__('Go to Home Page'),
                               'link'  => Mage::getBaseUrl()
                             )
      );

      $allowableFilters = $this->getFilterModel()->ALLOWABLE_FILTERS;
      $allowableKeys    = array_keys($allowableFilters);
      $parameters       = $this->getRequest()->getParams();
      $parameterKeys    = array_keys($parameters);
      $crumbKeys        = array_intersect($allowableKeys, $parameterKeys);

      if(empty($crumbKeys)) {
        $breadcrumbs->addCrumb('businessdirectory_listing_crumb',
                               array(
                                 'label' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getContentHeading())),
                                 'title' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getContentHeading()))
                               )
        );
      } else {

        $breadcrumbs->addCrumb('businessdirectory_listing',
                               array(
                                 'label' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getContentHeading())),
                                 'title' => $this->__($this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getContentHeading())),
                                 'link'  => $directory->getUrl()
                               )
        );

        $total = count($crumbKeys);
        $path  = $directory->getUrl();
        $i     = 1;
        foreach($crumbKeys as $key) {
          $crumbTitle = $this->getFilterModel()->getFilterConfig($key)->getH1Tag();
          $crumbTitle = $this->helper('businessdirectory/tags')->processCustomTags($crumbTitle);
          $value      = $parameters[$key];
          $path .= ($i === 1) ? '?' : '&';
          $path .= $key . '=' . urlencode($value);
          $label = $key === 'listing_state' ? $this->helper('businessdirectory')->convertStateAbbreviation($value, 'US') : $value;
          if($i === $total) {
            $breadcrumbs->addCrumb($key . '_crumb',
                                   array(
                                     'label' => $label,
                                     'title' => $crumbTitle
                                   )
            );
          } else {
            $breadcrumbs->addCrumb($key . '_crumb',
                                   array(
                                     'label' => $label,
                                     'title' => $crumbTitle,
                                     'link'  => $path
                                   )
            );
          }
          $i++;
        }
      }
    }

    $root = $this->getLayout()->getBlock('root');
    if($root) {
      $root->addBodyClass('directory-' . $directory->getIdentifier());
    }

    $head = $this->getLayout()->getBlock('head');
    if($head) {
      $head->setTitle($this->_getTitle());
      $head->setKeywords($this->_getMetaKeywords());
      $head->setDescription($this->_getMetaDescription());
      $head->addLinkRel('canonical', $directory->getUrl());
    }
    return parent::_prepareLayout();
  }

  protected function _getTitle()
  {
    if($this->getFilterModel()->getCurrentFilterField()) {
      $title = $this->getFilterModel()->getFilterConfig($this->getFilterModel()->getCurrentFilterField())->getTitleTag();
      return $this->helper('businessdirectory/tags')->processCustomTags($title);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getTitle());
  }

  protected function _getMetaDescription()
  {
    if($this->getFilterModel()->getCurrentFilterField()) {
      $description = $this->getFilterModel()->getFilterConfig($this->getFilterModel()->getCurrentFilterField())->getMetaDescription();
      return $this->helper('businessdirectory/tags')->processCustomTags($description);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getMetaDescription());
  }

  protected function _getMetaKeywords()
  {
    if($this->getFilterModel()->getCurrentFilterField()) {
      $keywords = $this->getFilterModel()->getFilterConfig($this->getFilterModel()->getCurrentFilterField())->getMetaKeywords();
      return $this->helper('businessdirectory/tags')->processCustomTags($keywords);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getMetaKeywords());
  }

}
