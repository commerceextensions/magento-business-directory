<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Seoextended extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

    $model = Mage::registry('directory_data');

    $data = Mage::getModel('businessdirectory/directory_staticfilter_content')->getCollection()
                ->addFieldToFilter('directory_id', $model->getDirectoryId());

    $formDataArray                 = array();
    $formDataArray['directory_id'] = $model->getDirectoryId();
    foreach($data as $item) {
      $formDataArray['staticfilter_state_' . strtolower($item->getFilterValue()) . '_' . strtolower($item->getFieldType())] = $item->getContent();
    }

    $form = new Varien_Data_Form(array(
                                   'id'      => 'seoextended_form',
                                   'action'  => $this->getUrl('*/*/save', array('directory_id' => $model->getDirectoryId())),
                                   'method'  => 'post',
                                   'enctype' => 'multipart/form-data',
                                 )
    );

    $fieldset = $form->addFieldset('listing_state_text', array(
      'legend'  => $this->__('On Page Textual Content for Individual States/Provinces'),
      'comment' => $this->__('The fields here are designed to give you the ability to add more unique content when the "State" filter is applied. By default, any text entered in the SEO tab within the "State" filter in the box labeled "Filter Text Content" would be shown when the user selects a state. However, if you enter different content in the boxes below, it will override the default and allow you to display unique content for each state.'),
      'class'   => 'fieldset-wide'
    ));
    if($model->getDirectoryId()) {
      $fieldset->addField('directory_id', 'hidden', array(
        'name'  => 'directory_id',
        'value' => $model->getDirectoryId()
      ));
    }

    $states = $this->_getStateDataArray();
    foreach($states as $state) {
      $field     = 'staticfilter_state_' . strtolower($state['value']) . '_content';
      $name      = 'filtertext[listing_state][' . $state['value'] . '][content]';
      $stateName = Mage::helper('businessdirectory')->convertStateAbbreviation($state['value'], 'US');
      $fieldset->addField($field, 'textarea', array(
        'name'               => $name,
        'label'              => $this->__($state['label']),
        'title'              => $this->__($state['label']),
        'required'           => false,
        'style'              => 'height:150px;',
        'after_element_html' => '<p class="note">On-Page Textual Content for ' . $state['label'] . '. You can use the [[listing_state]] and [[store_name]] tags in this field. The text in this field will override the "Filter Text Content" for the State filter located in the SEO tab.</p>'
      ));
    }

    Mage::dispatchEvent('businessdirectory_directory_seoextended_form_prepare', array('form' => $form));
    $form->setValues($formDataArray);
    $this->setForm($form);
    return parent::_prepareForm();

  }

  protected function _getStateDataArray()
  {
    $model = Mage::registry('directory_data');

    $states = Mage::getModel('businessdirectory/directory_listing')->getCollection();
    $states->getSelect()->reset(Zend_Db_Select::COLUMNS);
    $states->addFieldToSelect('listing_state');
    $states->addFieldToFilter('directory_id', $model->getDirectoryId());
    $states->getSelect()->distinct(true);
    $states->getSelect()->order('listing_state', Varien_Db_Select::SQL_ASC);
    $array = $states->toArray();

    $states = array();
    foreach($array['items'] as $key => $item) {
      if(!in_array($item['listing_state'], $states) && !is_null($item['listing_state'])) {
        $key                   = Mage::helper('businessdirectory')->convertStateAbbreviation(strtoupper($item['listing_state']), 'US');
        $states[$key]['value'] = strtoupper($item['listing_state']);
        $states[$key]['label'] = $key;
      }
    }

    ksort($states, SORT_REGULAR);
    return $states;
  }
}