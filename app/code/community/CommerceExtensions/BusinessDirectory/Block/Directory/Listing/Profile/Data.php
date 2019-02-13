<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Profile_Data extends Mage_Core_Block_Template
{
  public $directory;
  public $listing;

  public function getDirectory()
  {
    if(!$this->directory) {
      $this->directory = Mage::getModel('businessdirectory/directory')
                             ->getDirectory($this->getRequest()->getParam('directory_id'));
      return $this->directory;
    }
    return $this->directory;
  }

  public function getListing()
  {
    if(!$this->listing) {
      $this->listing = Mage::getModel('businessdirectory/directory_listing')
                           ->load($this->getRequest()->getParam('listing_id'));
      return $this->listing;
    }
    return $this->listing;
  }

  protected function getContent()
  {
    $helper    = Mage::helper('cms');
    $processor = $helper->getPageTemplateProcessor();
    $html      = $processor->filter($this->getListing()->getContent());
    $html      = $this->getMessagesBlock()->toHtml() . $html;
    if($html && $html != '')
      return $html;
    else
      return null;
  }

  public function canShowMap()
  {
    $_listing = $this->getListing();
    if($this->getDirectory()->getShowMap()) {
      if($_listing->getLatitude() && $_listing->getLatitude() != 0 && $_listing->getLongitude() && $_listing->getLongitude() != 0) {
        return true;
      }
    }
    return false;
  }

  public function getImageHeight()
  {
    return Mage::getStoreConfig('businessdirectory/frontend/profile_image_height');
  }

  public function getImageWidth()
  {
    return Mage::getStoreConfig('businessdirectory/frontend/profile_image_width');
  }

  public function getShowCountry()
  {
    return $this->getDirectory()->getShowCountry();
  }

  public function getUseSchemaTags()
  {
    return $this->getDirectory()->getUseSchemaOnDirectory();
  }

  public function getSchemaTypeUrl()
  {
    if($this->getUseSchemaTags()) {
      return trim($this->getDirectory()->getSchemaTypeUrlForDirectory());
    }
    return null;
  }

  public function getParentSchemaTag()
  {
    if($this->getUseSchemaTags()) {
      return trim('itemscope itemtype="' . $this->getSchemaTypeUrl() . '"');
    }
    return null;
  }

  public function addSchemaTag($tagType, $content = null)
  {
    if($this->getUseSchemaTags()) {
      return $this->helper('businessdirectory/tags')->addSchemaTag($tagType, $content);
    }
    return null;
  }

  public function wrapWithSchemaTag($string, $tagType, $content = null)
  {
    if($this->getUseSchemaTags()) {
      return $this->helper('businessdirectory/tags')->wrapWithSchemaTag($string, $tagType);
    }
    return $string;
  }

  public function addSchemaTagMeta($content, $tagType)
  {
    if($this->getUseSchemaTags()) {
      return $this->helper('businessdirectory/tags')->addSchemaTagMeta($content, $tagType);
    }
    return null;
  }

  public function getSocialHtml($_listing)
  {
    $block = $this->getLayout()->createBlock('businessdirectory/directory_listing_social');
    return $block->getSocialHtml($_listing);
  }

  public function getButtonHtml($_listing)
  {
    $block = $this->getLayout()->createBlock('businessdirectory/directory_listing_button', false);
    $block->setCanClaimProfile($this->getDirectory()->getCanClaimProfile());
    return $block->getButtonHtml($_listing);
  }

}