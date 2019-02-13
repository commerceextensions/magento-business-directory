<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareLayout()
  {
    parent::_prepareLayout();
    if(Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
      $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
    }
  }

  protected function _prepareForm()
  {
    $model = Mage::registry('directory_data');
    $form  = new Varien_Data_Form();
    $form->setHtmlIdPrefix('page_');
    $fieldset = $form->addFieldset('content_fieldset', array('legend' => $this->__('Content'), 'class' => 'fieldset-wide'));

    $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
      array('tab_id' => $this->getTabId())
    );
    $wysiwygConfig->addData(array(
                              'add_variables'            => false,
                              'plugins'                  => array(),
                              'widget_window_url'        => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
                              'directives_url'           => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
                              'directives_url_quoted'    => preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
                              'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
                            ));

    $fieldset->addField('content_heading', 'text', array(
      'name'               => 'content_heading',
      'label'              => $this->__('Content Heading'),
      'title'              => $this->__('Content Heading'),
      'after_element_html' => '<p class="note">This will serve as the default H1 tag for the directory AND the name of the directory visible to customers. You can use the <strong>[[store_name]]</strong> tag in this field.</p>',
      'required'           => true
    ));

    $contentField = $fieldset->addField('content', 'editor', array(
      'name'               => 'content',
      'style'              => 'height:36em;',
      'required'           => false,
      'config'             => $wysiwygConfig,
      'after_element_html' => '<p class="note">This will serve as the default textual content for the directory. You can use the <strong>[[store_name]]</strong> tag in this field.</p>'
    ));

    // Setting custom renderer for content field to remove label column
    $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                     ->setTemplate('cms/page/edit/form/renderer/content.phtml');
    $contentField->setRenderer($renderer);

    $form->setValues($model->getData());
    $this->setForm($form);

    Mage::dispatchEvent('businessdirectory_directory_content_form_prepare', array('form' => $form));

    return parent::_prepareForm();
  }
}