<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $imageUrl = $row->getData($this->getColumn()->getIndex());
    if(!is_null($imageUrl) && $imageUrl != '') {
      $mediaUrl = Mage::getBaseUrl('media');
      $mediaUrl = rtrim($mediaUrl, DS) . DS;
      return '<img src="' . $mediaUrl . $imageUrl . '" height="50" style="max-width:50px;" />';
    } else {
      return null;
    }
  }
}