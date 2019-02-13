<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId   = 'id';
    $this->_blockGroup = 'businessdirectory';
    $this->_controller = 'adminhtml_directory_edit_tab_listing';

    $directoryId = $this->getRequest()->getParam('directory_id');

    if($listingId = $this->getRequest()->getParam('listing_id')) {
      $this->_removeButton('reset');
      $message = $this->__('Are you sure you want to delete this listing?');
      $this->_addButton('delete', array(
        'label'   => $this->__('Delete Listing'),
        'onclick' => "deleteConfirm('{$message}','{$this->_getDeleteUrl()}')",
        'class'   => 'scalable delete',
        'level'   => -1
      ));
    }
    $this->_updateButton('back', 'onclick', "setLocation('{$this->_getBackUrl()}')");
    $this->_addButton('saveandcontinue', array(
      'label'   => $this->__('Save and Continue Edit'),
      'onclick' => 'saveAndContinueEdit()',
      'class'   => 'save',
    ), -100);
    $this->_formScripts[] = "
		function saveAndContinueEdit(){
			editForm.submit($('edit_form').action+'back/edit/');
		}
	";
  }

  public function getHeaderText()
  {
    if(Mage::registry('listing_data')->getListingId()) {
      return $this->__("Edit '%s'", $this->escapeHtml(Mage::registry('listing_data')->getListingName()));
    } else {
      return $this->__('New Listing');
    }
  }

  protected function _getSaveAndContinueUrl()
  {
    return $this->getUrl('*/businessdirectory_listing/save', array('_current' => true, 'back' => 'edit'));
  }

  protected function _getBackUrl()
  {
    $directoryId = $this->getRequest()->getParam('directory_id');
    return $this->getUrl('*/businessdirectory/edit', array('directory_id' => $directoryId, 'active_tab' => 'directory_listings'));
  }

  protected function _getDeleteUrl()
  {
    return $this->getUrl('*/*/delete', array('_current' => true, 'active_tab' => 'directory_listings'));
  }

  protected function _prepareLayout()
  {
    $tabsBlock = $this->getLayout()->getBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tabs');
    if($tabsBlock) {
      $tabsBlockJsObject = $tabsBlock->getJsObjectName();
      $tabsBlockPrefix   = $tabsBlock->getId() . '_';
    } else {
      $tabsBlockJsObject = 'listing_tabsJsTabs';
      $tabsBlockPrefix   = 'listing_tabs_';
    }

    $this->_formScripts[] = "
		function toggleEditor() {
			if (tinyMCE.getInstanceById('listing_content') == null) {
				tinyMCE.execCommand('mceAddControl', false, 'listing_content');
			} else {
				tinyMCE.execCommand('mceRemoveControl', false, 'listing_content');
			}
		}
	";
    return parent::_prepareLayout();
  }

  protected function _isAllowedAction($action)
  {
    return true;
  }
}