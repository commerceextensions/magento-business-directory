<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Pending_Edit_Form extends CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tab_Main
{
  protected function _prepareForm()
  {
    parent::_prepareForm();
    $model = Mage::registry('listing_data');
    $form  = $this->getForm();
    $form->setAction($this->getUrl('*/*/save', array('id' => $model->getId())));

    $fieldset = $form->getElement('base_fieldset');
    $fieldset->removeField('is_active');
    $fieldset->removeField('geocode_on_save');

    $fieldset      = $form->addFieldset('content_fieldset', array('legend' => $this->__('Content'), 'class' => 'fieldset-wide'));
    $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
    $wysiwygConfig->addData(array(
                              'add_variables'            => false,
                              'plugins'                  => array(),
                              'widget_window_url'        => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
                              'directives_url'           => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
                              'directives_url_quoted'    => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
                              'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                            ));
    $contentField = $fieldset->addField('content', 'editor', array(
      'name'     => 'content',
      'style'    => 'height:36em;',
      'required' => false,
      'config'   => $wysiwygConfig
    ));

    // Setting custom renderer for content field to remove label column
    $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                     ->setTemplate('cms/page/edit/form/renderer/content.phtml');
    $contentField->setRenderer($renderer);

    Mage::dispatchEvent('businessdirectory_directory_pending_form_prepare', array('form' => $form));

    $form->setUseContainer(true);
    $form->setValues($model->getData());
    $this->setForm($form);
    return $this;
  }
}