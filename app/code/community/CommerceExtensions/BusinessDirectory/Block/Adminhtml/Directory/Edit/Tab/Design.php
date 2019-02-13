<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form();
    $form->setHtmlIdPrefix('page_');
    $model = Mage::registry('directory_data');

    $layoutFieldset = $form->addFieldset('layout_fieldset', array(
      'legend' => $this->__('Page Layout'),
      'class'  => 'fieldset-wide',
    ));

    $layoutOptions = Mage::getSingleton('page/source_layout')->toOptionArray();
    array_unshift($layoutOptions, array('label' => '-- Please Select --', 'value' => ''));
    $layoutFieldset->addField('root_template', 'select', array(
      'name'     => 'root_template',
      'label'    => $this->__('Layout'),
      'required' => false,
      'values'   => $layoutOptions,
    ));
    if(!$model->getId()) {
      $model->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
    }

    $layoutFieldset->addField('layout_update_xml', 'textarea', array(
      'name'  => 'layout_update_xml',
      'label' => $this->__('Layout Update XML'),
      'style' => 'height:24em;',
    ));

    $designFieldset = $form->addFieldset('design_fieldset', array(
      'legend' => $this->__('Custom Design'),
      'class'  => 'fieldset-wide',
    ));

    $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
      Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
    );

    $designFieldset->addField('custom_theme_from', 'date', array(
      'name'   => 'custom_theme_from',
      'label'  => $this->__('Custom Design From'),
      'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      'format' => $dateFormatIso,
      'class'  => 'validate-date validate-date-range date-range-custom_theme-from'
    ));

    $designFieldset->addField('custom_theme_to', 'date', array(
      'name'   => 'custom_theme_to',
      'label'  => $this->__('Custom Design To'),
      'image'  => $this->getSkinUrl('images/grid-cal.gif'),
      'format' => $dateFormatIso,
      'class'  => 'validate-date validate-date-range date-range-custom_theme-to'
    ));

    $designFieldset->addField('custom_theme', 'select', array(
      'name'   => 'custom_theme',
      'label'  => $this->__('Custom Theme'),
      'values' => Mage::getModel('core/design_source_design')->getAllOptions(),
    ));

    $designFieldset->addField('custom_root_template', 'select', array(
      'name'   => 'custom_root_template',
      'label'  => $this->__('Custom Layout'),
      'values' => Mage::getSingleton('page/source_layout')->toOptionArray(true),
    ));

    $designFieldset->addField('custom_layout_update_xml', 'textarea', array(
      'name'  => 'custom_layout_update_xml',
      'label' => $this->__('Custom Layout Update XML'),
      'style' => 'height:24em;',
    ));

    Mage::dispatchEvent('businessdirectory_directory_design_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);
    return parent::_prepareForm();
  }
}