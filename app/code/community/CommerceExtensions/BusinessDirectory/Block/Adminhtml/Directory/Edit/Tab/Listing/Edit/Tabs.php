<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('listing_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle($this->__('Manage Listing'));
  }

  protected function _beforeToHtml()
  {
    $this->addTab('listing_main', array(
      'label'   => $this->__('Listing Information'),
      'title'   => $this->__('Listing Information'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tab_main')->toHtml(),
    ));
    $this->addTab('listing_content', array(
      'label'   => $this->__('Content'),
      'title'   => $this->__('Content'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tab_content')->toHtml(),
    ));
    $this->addTab('listing_design', array(
      'label'   => $this->__('Design'),
      'title'   => $this->__('Design'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tab_design')->toHtml(),
    ));
    $this->addTab('listing_meta', array(
      'label'   => $this->__('Meta Data'),
      'title'   => $this->__('Meta Data'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tab_meta')->toHtml(),
    ));
    return parent::_beforeToHtml();
  }
}