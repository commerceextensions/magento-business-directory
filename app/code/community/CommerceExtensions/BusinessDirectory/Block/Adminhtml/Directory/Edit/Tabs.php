<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('directory_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle($this->__('Manage Directory'));
  }

  protected function _beforeToHtml()
  {
    $this->addTab('directory_main', array(
      'label'   => $this->__('Directory Information'),
      'title'   => $this->__('Directory Information'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_main')->toHtml(),
    ));
    $this->addTab('directory_config', array(
      'label'   => $this->__('Settings'),
      'title'   => $this->__('Settings'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_config')->toHtml(),
    ));
    $this->addTab('directory_content', array(
      'label'   => $this->__('Content'),
      'title'   => $this->__('Content'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_content')->toHtml(),
    ));
    if($this->getRequest()->getParam('directory_id')) {
      $this->addTab('directory_listings', array(
        'label'   => $this->__('Listings'),
        'title'   => $this->__('Listings'),
        'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing')->toHtml() .
                     $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_container')->toHtml()
      ));
    }
    $this->addTab('directory_design', array(
      'label'   => $this->__('Design'),
      'title'   => $this->__('Design'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_design')->toHtml(),
    ));
    $this->addTab('directory_meta', array(
      'label'   => $this->__('Meta Data'),
      'title'   => $this->__('Meta Data'),
      'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_meta')->toHtml(),
    ));
    if($this->getRequest()->getParam('directory_id')) {
      $this->addTab('directory_seo', array(
        'label'   => $this->__('SEO'),
        'title'   => $this->__('SEO'),
        'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_seo')->toHtml()
      ));
    }
    if($this->getRequest()->getParam('directory_id')) {
      $this->addTab('directory_seoextended', array(
        'label'   => $this->__('SEO Extended'),
        'title'   => $this->__('SEO Extended'),
        'content' => $this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_seoextended')->toHtml()
      ));
    }
    return parent::_beforeToHtml();
  }
}