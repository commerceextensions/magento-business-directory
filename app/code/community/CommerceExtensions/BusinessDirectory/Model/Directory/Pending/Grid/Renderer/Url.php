<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $value = $row->getData($this->getColumn()->getIndex());
    if(is_null($value)) {
      return null;
    } else
      return '<a href="' . $value . '" target="_blank">' . $value . '</a>';
  }
}