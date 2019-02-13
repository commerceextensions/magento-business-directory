<?php
/**
 * Listing.php
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
class CommerceExtensions_BusinessDirectory_Block_Directory_Listing extends Mage_Core_Block_Template
{
  public function getDirectory()
  {
    $directory = Mage::getSingleton('businessdirectory/directory')->getDirectory();
    if($this->helper('businessdirectory')->isVersionEqualOrHigher('1.8')) {
      $this->addModelTags($directory);
    }
    return $directory;
  }

  protected function _prepareLayout()
  {
    parent::_prepareLayout();

    $collection = Mage::getModel('businessdirectory/directory_listing')->getListings();
    Mage::dispatchEvent('businessdirectory_listing_collection_load_before', array('collection' => $collection));
    $this->setCollection($collection);
    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
    $pager->setAvailableLimit(array(10 => 10, 25 => 25, 50 => 50));
    $pager->setCollection($this->getCollection());
    $this->setChild('pager', $pager);
    $this->getCollection()->load();
    Mage::dispatchEvent('businessdirectory_listing_collection_load_after', array('collection' => $this->getCollection()));
    return $this;
  }

  public function getPagerHtml()
  {
    return $this->getChildHtml('pager');
  }

  public function getImageHeight()
  {
    return Mage::getStoreConfig('businessdirectory/frontend/list_image_height');
  }

  public function getImageWidth()
  {
    return Mage::getStoreConfig('businessdirectory/frontend/list_image_width');
  }

  public function countGeocodedListings()
  {
    $hasCoordinates = 0;
    foreach($this->getCollection() as $_listing) {
      if($_listing->getLatitude() != 0 && $_listing->getLongitude() != 0) {
        $hasCoordinates++;
      }
    }
    return $hasCoordinates;
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

  public function canClaimProfile()
  {
    return $this->getDirectory()->getCanClaimProfile();
  }

  public function getSocialHtml($_listing)
  {
    $block = $this->getLayout()->createBlock('businessdirectory/directory_listing_social');
    return $block->getSocialHtml($_listing);
  }

  public function getButtonHtml($_listing)
  {
    $block = $this->getLayout()->createBlock('businessdirectory/directory_listing_button');
    $block->setCanClaimProfile($this->canClaimProfile());
    return $block->getButtonHtml($_listing);
  }

}