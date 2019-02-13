<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tab_Meta extends Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
{
  protected function _prepareForm()
  {
    Mage::unregister('cms_page');
    $model = Mage::registry('listing_data');
    Mage::register('cms_page', $model);
    return parent::_prepareForm();
  }
}