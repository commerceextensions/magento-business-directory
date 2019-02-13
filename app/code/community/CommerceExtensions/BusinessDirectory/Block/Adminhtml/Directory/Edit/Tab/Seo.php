<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $model = Mage::registry('directory_data');

    $form = new Varien_Data_Form(array(
                                   'id'      => 'seo_form',
                                   'action'  => $this->getUrl('*/*/save', array('directory_id' => $model->getDirectoryId())),
                                   'method'  => 'post',
                                   'enctype' => 'multipart/form-data',
                                 )
    );

    $fieldset              = $form->addFieldset('schema_config', array(
      'legend' => $this->__('Schema.org Markup Settings'),
    ));
    $showSchemaOnDirectory = $fieldset->addField('use_schema_on_directory', 'select', array(
      'name'     => 'use_schema_on_directory',
      'label'    => $this->__('Use Schema.org on Directory'),
      'title'    => $this->__('Use Schema.org on Directory'),
      'required' => false,
      'options'  => array(0 => 'No', 1 => 'Yes'),
    ));
    $schemaUrlDirectory    = $fieldset->addField('schema_type_url_for_directory', 'text', array(
      'name'               => 'schema_type_url_for_directory',
      'label'              => $this->__('Schema Type URL for Directory'),
      'title'              => $this->__('Schema Type URL for Directory'),
      'required'           => true,
      'class'              => 'validate-url',
      'after_element_html' => '<p class="note">The main schema type. Ex. http://schema.org/LocalBusiness</p>'
    ));
    $showSchemaOnProfile   = $fieldset->addField('use_schema_on_profile', 'select', array(
      'name'     => 'use_schema_on_profile',
      'label'    => $this->__('Use Schema.org on Profile'),
      'title'    => $this->__('Use Schema.org on Profile'),
      'required' => false,
      'options'  => array(0 => 'No', 1 => 'Yes'),
    ));
    $schemaUrlProfile      = $fieldset->addField('schema_type_url_for_profile', 'text', array(
      'name'               => 'schema_type_url_for_profile',
      'label'              => $this->__('Schema Type URL for Profile'),
      'title'              => $this->__('Schema Type URL for Profile'),
      'required'           => true,
      'class'              => 'validate-url',
      'after_element_html' => '<p class="note">The main schema type. Ex. http://schema.org/LocalBusiness</p>'
    ));

    $fieldset = $form->addFieldset('profile_defaults_config', array(
      'legend' => $this->__('Profile Page Default Settings - Create Custom Templated Tags for Individual Profile Pages'),
      'class'  => 'fieldset-wide',
    ));
    $fieldset->addField('note', 'note', array(
      'text' => "<div class='ce_block_notice'>
						  <h3>A Little Clarification On These Options</h3>
						  <p>The options in this section pertain to the individual profile pages for the listings. If you do not want to enter or upload individual meta keywords and meta descriptions for the listings, you can create a template that will serve as the default value. You can also plug in any piece of data from the listing with double-bracketed variables. You can use the name of ANY column from the <strong>" . Mage::getSingleton('core/resource')
                                                                                                                                                                                                                                                                                                                                                                                                                                   ->getTableName('businessdirectory/directory_listing') . "</strong> table in your database. For instance, to get the listing name, you use the double-bracketed column name like this: <strong>[[listing_name]]</strong>. Additionally, you can get the current store name like this: <strong>[[store_name]]</strong>.</p>
						</div>",
    ));

    $fieldset->addField('profile_title_tag_structure', 'text', array(
      'name'               => 'profile_title_tag_structure',
      'label'              => $this->__('Title Tag Configuration'),
      'title'              => $this->__('Title Tag Configuration'),
      'required'           => false,
      'after_element_html' => '<p class="note">Ex. [[listing_name]]: Laywer in [[listing_city]], [[listing_state]] - [[store_name]]</p>'
    ));
    $fieldset->addField('profile_default_meta_description', 'textarea', array(
      'name'               => 'profile_default_meta_description',
      'label'              => $this->__('Meta Description'),
      'title'              => $this->__('Meta Description'),
      'disabled'           => false,
      'style'              => 'height:50px;',
      'class'              => 'validate-length maximum-length-255 textarea',
      'onkeyup'            => 'checkMaxLength(this, 255)',
      'after_element_html' => '<p class="note">Maximum 255 chars. Ex. Looking for a great lawyer in [[listing_state]]? Browse [[store_name]]\'s huge directory of lawyers today!</p>'
    ));
    $fieldset->addField('profile_default_meta_keywords', 'textarea', array(
      'name'               => 'profile_default_meta_keywords',
      'label'              => $this->__('Meta Keywords'),
      'title'              => $this->__('Meta Keywords'),
      'disabled'           => false,
      'style'              => 'height:50px;',
      'class'              => 'validate-length maximum-length-255 textarea',
      'onkeyup'            => 'checkMaxLength(this, 255)',
      'after_element_html' => '<p class="note">Maximum 255 chars.</p>'
    ));

    $fieldset = $form->addFieldset('static_filter_config', array(
      'legend' => $this->__('Static Filter Settings'),
      'class'  => 'fieldset-wide'
    ));
    $fieldset->addType('sortdrop', 'CommerceExtensions_BusinessDirectory_Block_Varien_Data_Form_Element_Staticfilter');
    $fieldset->addField('filter_fields', 'sortdrop', array(
      'label' => $this->__('Static Filter Setup'),
      'name'  => 'filter_fields',
    ));

    Mage::dispatchEvent('businessdirectory_directory_seo_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);

    $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                                       ->addFieldMap($showSchemaOnDirectory->getHtmlId(), $showSchemaOnDirectory->getName())
                                       ->addFieldMap($schemaUrlDirectory->getHtmlId(), $schemaUrlDirectory->getName())
                                       ->addFieldMap($showSchemaOnProfile->getHtmlId(), $showSchemaOnProfile->getName())
                                       ->addFieldMap($schemaUrlProfile->getHtmlId(), $schemaUrlProfile->getName())
                                       ->addFieldDependence($schemaUrlDirectory->getName(), $showSchemaOnDirectory->getName(), 1)
                                       ->addFieldDependence($schemaUrlProfile->getName(), $showSchemaOnProfile->getName(), 1)
    );

    return parent::_prepareForm();


  }
}