<?php

class CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
  public function render(Varien_Object $row)
  {
    $type = $row->getData($this->getColumn()->getIndex());
    $css  = 'display: block;height: 16px;font: bold 10px/16px Arial, Helvetica, sans-serif;text-transform: uppercase;text-align: center;padding:0;margin: 1px 0;white-space: nowrap;color: #fff;border-radius:8px;-moz-border-radius:8px;-webkit-border-radius:8px;';

    switch($type) {
      case 'new':
        return '<div style="background:#36B712;' . $css . '"><strong>' . $type . '</strong></div>';
        break;
      case 'claim':
        return '<div style="background:#0000FF;' . $css . '"><strong>' . $type . '</strong></div>';
        break;
      case 'update':
        return '<div style="background:#AAAAAA;' . $css . '"><strong>' . $type . '</strong></div>';
        break;
    }
  }
}