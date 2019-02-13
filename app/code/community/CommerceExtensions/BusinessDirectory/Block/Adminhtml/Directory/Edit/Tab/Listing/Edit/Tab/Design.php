<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tab_Design extends CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Design
{
  protected function _prepareForm()
  {
    Mage::unregister('directory_data');
    $model = Mage::registry('listing_data');
    Mage::register('directory_data', $model);
    $form = parent::_prepareForm();
    Mage::dispatchEvent('businessdirectory_directory_listing_design_form_prepare', array('form' => $form));
    return $form;
  }
}