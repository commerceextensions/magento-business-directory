<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $model = Mage::registry('directory_data');

    $form = new Varien_Data_Form(array(
                                   'id'     => 'edit_form',
                                   'action' => $this->getUrl('*/*/save', array('directory_id' => $this->getRequest()->getParam('directory_id'))),
                                   'method' => 'post'
                                 ));

    $fieldset = $form->addFieldset('base_fieldset', array(
      'legend' => $this->__('Business Directory Information'),
    ));

    if($model->getDirectoryId()) {
      $fieldset->addField('directory_id', 'hidden', array(
        'name' => 'directory_id',
      ));
    }

    $fieldset->addField('title', 'text', array(
      'name'               => 'title',
      'label'              => $this->__('Directory Name'),
      'title'              => $this->__('Directory Name'),
      'required'           => true,
      'after_element_html' => '<p class="note">This will serve as the default title tag for the directory. You can use the <strong>[[store_name]]</strong> tag in this field.</p>'
    ));

    $fieldset->addField('identifier', 'text', array(
      'name'     => 'identifier',
      'label'    => $this->__('Directory Url Key'),
      'title'    => $this->__('Directory Url Key'),
      'class'    => 'validate-identifier',
      'required' => true,
    ));

    if(!Mage::app()->isSingleStoreMode()) {
      $field    = $fieldset->addField('store_id', 'multiselect', array(
        'name'     => 'stores[]',
        'label'    => $this->__('Store View'),
        'title'    => $this->__('Store View'),
        'required' => true,
        'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
      ));
      $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
      $field->setRenderer($renderer);
    } else {
      $fieldset->addField('store_id', 'hidden', array(
        'name'  => 'stores[]',
        'value' => Mage::app()->getStore(true)->getId()
      ));
      $model->setStoreId(Mage::app()->getStore(true)->getId());
    }

    $fieldset->addField('is_active', 'select', array(
      'label'    => $this->__('Status'),
      'title'    => $this->__('Status'),
      'name'     => 'is_active',
      'required' => true,
      'options'  => array(1 => 'Enabled', 0 => 'Disabled'),
    ));

    Mage::dispatchEvent('businessdirectory_directory_main_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);
    return parent::_prepareForm();
  }

}