<?php
/**
 * Content.php
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
class CommerceExtensions_BusinessDirectory_Block_Directory_Content extends Mage_Core_Block_Template
{
  protected $_filterModel;
  protected $_filterContentModel;

  public function getDirectory()
  {
    $directory = Mage::getSingleton('businessdirectory/directory')->getDirectory();
    if($this->helper('businessdirectory')->isVersionEqualOrHigher('1.8')) {
      $this->addModelTags($directory);
    }
    return $directory;
  }

  public function getDirectoryId()
  {
    return $this->getDirectory()->getDirectoryId();
  }

  public function getFilterModel()
  {
    if(!$this->_filterModel) {
      return $this->_filterModel = Mage::getModel('businessdirectory/directory_staticfilter');
    }
    return $this->_filterModel;
  }

  public function getFilterContentModel()
  {
    if(!$this->_filterContentModel) {
      return $this->_filterContentModel = Mage::getModel('businessdirectory/directory_staticfilter_content');
    }
    return $this->_filterContentModel;
  }

  public function getFilterContent($fieldname, $filtervalue, $fieldtype)
  {
    $content = $this->getFilterContentModel()->getCollection()
                    ->addFieldToFilter('filter_field', $fieldname)
                    ->addFieldToFilter('filter_value', $filtervalue)
                    ->addFieldToFilter('field_type', $fieldtype)
                    ->addFieldToFilter('directory_id', $this->getDirectoryId())
                    ->getFirstItem()
                    ->getContent();
    return is_null($content) || $content == '' ? null : $content;
  }

  public function getContentHeading()
  {
    if($this->getFilterModel()->getCurrentFilterField()) {
      $h1tag = $this->getFilterModel()->getFilterConfig($this->getFilterModel()->getCurrentFilterField())->getH1Tag();
      return $this->helper('businessdirectory/tags')->processCustomTags($h1tag);
    }
    return $this->helper('businessdirectory/tags')->processCustomTags($this->getDirectory()->getContentHeading());
  }

  protected function getContent()
  {
    $helper    = Mage::helper('cms');
    $processor = $helper->getPageTemplateProcessor();

    $filterField = $this->getFilterModel()->getCurrentFilterField();
    if($filterField) {

      // if there is a filter applied, first check to see if there is filter specific content available
      $filterValue           = $this->getRequest()->getParam($filterField);
      $filterSpecificContent = $this->getFilterContent($filterField, $filterValue, 'content');
      if($filterSpecificContent) {
        $html = $filterSpecificContent;
        $html = $processor->filter($html);
      } else {
        // else grab the filter's templated content
        $html = $this->getFilterModel()->getFilterConfig($filterField)->getContent();
        $html = $processor->filter($html);
      }
    } else {
      $html = $processor->filter($this->getDirectory()->getContent());
    }
    $html = $this->getMessagesBlock()->toHtml() . $html;

    if($html && $html != '')
      return $this->helper('businessdirectory/tags')->processCustomTags($html);
    else
      return null;
  }

}