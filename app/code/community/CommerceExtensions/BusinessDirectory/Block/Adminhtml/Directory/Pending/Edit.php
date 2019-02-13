<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Pending_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    $this->_blockGroup = 'businessdirectory';
    $this->_controller = 'adminhtml_directory_pending';
    parent::__construct();
    $this->_removeButton('reset');
    $this->_updateButton('save', 'label', $this->__('Save Changes Without Approving'));
    $model       = Mage::registry('listing_data');
    $listingType = $model->getAction();
    switch($listingType) {
      case 'new':
        $message = $this->__('Are you sure? Approving this listing will set the listing live and remove the submission from the pending list.');
        break;
      case 'claim':
        $message = $this->__('Are you sure? Approving this listing will overwrite the current listing data with the data in this form and will remove this submission from the pending list.');
        break;
      case 'update':
        $message = $this->__('Are you sure? Approving this listing will overwrite the current listing data with the data in this form and will remove this submission from the pending list.');
        break;
      default:
        $message = 'Are you sure?';
    }

    $this->_addButton('approve', array(
      'label'   => $this->__('Approve'),
      'onclick' => "confirmSetLocation('{$message}','{$this->_getApprovalUrl()}')",
      'class'   => 'scalable save'
    ));
    if($id = $this->getRequest()->getParam('id')) {
      $model   = Mage::getModel('businessdirectory/directory_listing_submit')->load($id);
      $actions = array('claim', 'update');
      if(in_array($model->getAction(), $actions)) {
        $this->_addButton('compare', array(
          'label'   => $this->__('Compare Changes'),
          'onclick' => 'compareChanges()',
          'class'   => 'save',
        ));
      }
    }
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
    $data = Mage::registry('listing_data');

    if($data) {
      $action = $data->getAction();
      switch($action) {
        case 'new':
          return $this->__('New Directory Listing Awaiting Approval: %s', $data->getListingName());
          break;
        case 'claim':
          return $this->__('Profile Claimed & Awaiting Approval: %s', $data->getListingName());
          break;
        case 'update':
          return $this->__('Profile Updates Awaiting Approval: %s', $data->getListingName());
          break;
        default:
          return 'Manage Pending Listing';
      }
    }
  }

  protected function _getApprovalUrl()
  {
    return $this->getUrl('*/*/approve', array('id' => $this->getRequest()->getParam('id')));
  }

  protected function _getSaveAndContinueUrl()
  {
    return $this->getUrl('*/*/save', array(
      '_current' => true,
      'back'     => 'edit',
    ));
  }

  protected function _prepareLayout()
  {
    $this->_formScripts[] = "
		function toggleEditor() {
		  if (tinyMCE.getInstanceById('content') == null) {
			  tinyMCE.execCommand('mceAddControl', false, 'content');
		  } else {
			  tinyMCE.execCommand('mceRemoveControl', false, 'content');
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