<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_blockGroup = 'businessdirectory';
    $this->_controller = 'adminhtml_directory_edit_tab_listing_container';
    $this->_headerText = $this->__('Manage Directory Listings');
    parent::__construct();
    $this->_removeButton('add');
    $this->_addButton('add', array(
      'label'   => $this->__('Add New Listing'),
      'onclick' => "setLocation('{$this->getUrl('*/businessdirectory_listing/new',array('directory_id' => $this->getRequest()->getParam('directory_id')))}')",
    ));
  }
}