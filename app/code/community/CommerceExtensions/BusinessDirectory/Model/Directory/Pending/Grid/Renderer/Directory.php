<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Directory extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $value = $row->getData($this->getColumn()->getIndex());
    if(is_null($value) || $value == 0) {
      return null;
    } else
      $directoryTitle = Mage::getSingleton('businessdirectory/directory')->load($value)->getTitle();
    return $value . ' / ' . $directoryTitle;
  }
}