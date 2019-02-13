<?php

class CommerceExtensions_BusinessDirectory_Block_Varien_Data_Form_Element_Staticfilter extends Varien_Data_Form_Element_Abstract
{
  public function __construct($data)
  {
    parent::__construct($data);
  }

  public function getElementHtml()
  {
    return Mage::app()->getLayout()
               ->createBlock('businessdirectory/adminhtml_directory_edit_tab_seo_staticfilter')
               ->setTemplate('businessdirectory/directory/edit/tab/seo/staticfilter.phtml')->toHtml();
  }
}
