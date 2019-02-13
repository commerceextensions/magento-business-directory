<?php
/**
 * Edit.php
 * CommerceExtensions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Business Directory
 * @copyright  Copyright (c) 2003-2019 CommerceExtensions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
 
class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {
    parent::__construct();

    $this->_objectId   = 'id';
    $this->_blockGroup = 'businessdirectory';
    $this->_controller = 'adminhtml_directory';

    $this->_updateButton('save', 'label', $this->__('Save Directory'));
    if($this->getRequest()->getParam('directory_id')) {
      $this->_removeButton('reset');
      $message = $this->__('Are you sure you want to delete this directory?');
      $this->_addButton('delete', array(
        'label'   => $this->__('Delete Directory'),
        'onclick' => "confirmSetLocation('{$message}','{$this->_getDeleteUrl()}')",
        'class'   => 'scalable delete',
        'level'   => -1
      ));
    }
    $this->_addButton('saveandcontinue', array(
      'label'   => $this->__('Save and Continue Edit'),
      'onclick' => "saveAndContinueEdit('{$this->_getSaveAndContinueUrl()}')",
      'class'   => 'save',
    ), -100);
  }

  public function getHeaderText()
  {
    if(Mage::registry('directory_data')->getDirectoryId()) {
      return $this->__("Edit Directory: '%s'", $this->escapeHtml(Mage::registry('directory_data')->getTitle()));
    } else {
      return $this->__('New Business Directory');
    }
  }

  protected function _isAllowedAction($action)
  {
    return true;
  }

  protected function _getSaveAndContinueUrl()
  {
    return $this->getUrl('*/*/save', array(
      '_current'   => true,
      'back'       => 'edit',
      'active_tab' => '{{tab_id}}'
    ));
  }

  protected function _getDeleteUrl()
  {
    return $this->getUrl('*/*/delete', array('directory_id' => Mage::registry('directory_data')->getDirectoryId()));
  }

  protected function _prepareLayout()
  {
    $tabsBlock = $this->getLayout()->getBlock('businessdirectory/adminhtml_directory_edit_tabs');
    if($tabsBlock) {
      $tabsBlockJsObject = $tabsBlock->getJsObjectName();
      $tabsBlockPrefix   = $tabsBlock->getId() . '_';
    } else {
      $tabsBlockJsObject = 'directory_tabsJsTabs';
      $tabsBlockPrefix   = 'directory_tabs_';
    }

    $this->_formScripts[] = "
		function toggleEditor() {
			if (tinyMCE.getInstanceById('directory_content') == null) {
				tinyMCE.execCommand('mceAddControl', false, 'directory_content');
			} else {
				tinyMCE.execCommand('mceRemoveControl', false, 'directory_content');
			}
		}

		function saveAndContinueEdit(urlTemplate) {
			var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
			var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
			if (tabsIdValue.startsWith(tabsBlockPrefix)) {
				tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
			}
			var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
			var url = template.evaluate({tab_id:tabsIdValue});
			editForm.submit(url);
		}
	";
    return parent::_prepareLayout();
  }

}