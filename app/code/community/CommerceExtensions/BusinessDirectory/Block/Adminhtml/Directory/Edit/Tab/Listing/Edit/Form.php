<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $directoryId = $this->getRequest()->getParam('directory_id');
    $model       = Mage::registry('listing_data');

    $params                 = array();
    $params['directory_id'] = $directoryId;
    if($model->getListingId()) {
      $params['listing_id'] = $model->getListingId();
    }
    $form = new Varien_Data_Form(array(
                                   'id'      => 'edit_form',
                                   'action'  => $this->getUrl('*/businessdirectory_listing/save', $params),
                                   'method'  => 'post',
                                   'enctype' => 'multipart/form-data'
                                 )
    );
    $form->setUseContainer(true);
    $this->setForm($form);
    return parent::_prepareForm();
  }
}