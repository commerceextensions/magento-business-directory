<?php

class CommerceExtensions_BusinessDirectory_Block_Directory_Listing_Button extends Mage_Core_Block_Template
{
  public function canClaimProfile()
  {
    return $this->getCanClaimProfile();
  }

  public function getButtonHtml($_listing)
  {
    $this->getLayout()
         ->createBlock($this, 'businessdirectory.button')
         ->setListing($_listing)
         ->setTemplate('businessdirectory/helper/button.phtml');
    return $this->toHtml();
  }

  protected function _beforeToHtml()
  {
    parent::_beforeToHtml();
  }
}