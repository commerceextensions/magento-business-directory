<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Social extends Mage_Core_Block_Template
{
  public function getSocialHtml($_listing)
  {
    $this->getLayout()
         ->createBlock($this, 'businessdirectory.social')
         ->setListing($_listing)
         ->setTemplate('businessdirectory/helper/social.phtml');
    return $this->toHtml();
  }

  protected function _beforeToHtml()
  {
    parent::_beforeToHtml();
  }
}