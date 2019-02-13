<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $form = new Varien_Data_Form(array(
                                   'id'      => 'edit_form',
                                   'action'  => $this->getUrl('*/*/save', array('directory_id' => $this->getRequest()->getParam('directory_id'))),
                                   'method'  => 'post',
                                   'enctype' => 'multipart/form-data'
                                 )
    );
    $form->setUseContainer(true);
    $this->setForm($form);
    return parent::_prepareForm();
  }
}