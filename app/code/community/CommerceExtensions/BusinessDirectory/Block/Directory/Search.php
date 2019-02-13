<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Search extends Mage_Core_Block_Template
{
  public function getDirectory()
  {
    $directory = Mage::getSingleton('businessdirectory/directory')->getDirectory();
    if($this->helper('businessdirectory')->isVersionEqualOrHigher('1.8')) {
      $this->addModelTags($directory);
    }
    return $directory;
  }

  public function getSearchUrl()
  {
    return $this->getUrl('*/*/search', array('directory_id' => $this->getDirectory()->getDirectoryId()));
  }

  public function getResetUrl()
  {
    return $this->getUrl('*/*/reset', array('directory_id' => $this->getDirectory()->getDirectoryId()));
  }

  public function getNameQueryText()
  {
    if($text = $this->escapeHtml($this->getRequest()->getParam('directory_name'))) {
      return $text;
    }
    return null;
  }

  public function getLocationQueryText()
  {
    if($text = $this->escapeHtml($this->getRequest()->getParam('directory_location'))) {
      return $text;
    }
    return null;
  }

  public function getNamePlaceholderText()
  {
    if($text = $this->escapeHtml($this->getDirectory()->getSearchNamePlaceholder())) {
      return $text;
    }
    return $this->escapeHtml('Search by Name / Company');
  }

  public function getLocationPlaceholderText()
  {
    if($text = $this->escapeHtml($this->getDirectory()->getSearchLocationPlaceholder())) {
      return $text;
    }
    return $this->escapeHtml('Search by Address, City, State, Zip or Point of Interest');
  }

}