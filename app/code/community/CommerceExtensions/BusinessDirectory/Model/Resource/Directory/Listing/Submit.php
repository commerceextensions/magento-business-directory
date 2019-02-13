<?php

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Listing_Submit extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct()
  {
    $this->_init('businessdirectory/directory_listing_submit', 'id');
  }

  protected function _beforeSave(Mage_Core_Model_Abstract $object)
  {
    if($object->isObjectNew() && !$object->hasCreationTime()) {
      $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
    }

    $directory = Mage::getModel('businessdirectory/directory')->load($object->getDirectoryId());
    if($directory->getIsUsOnly()) {
      if($object->getListingPhone()) {
        $phone = Mage::helper('businessdirectory')->formatPhoneForUs($object->getListingPhone());
        $object->setListingPhone($phone);
      }
      if($object->getListingFax()) {
        $fax = Mage::helper('businessdirectory')->formatPhoneForUs($object->getListingFax());
        $object->setListingFax($fax);
      }
      if($object->getListingState()) {
        $state = Mage::helper('businessdirectory')->abbreviateStateName($object->getListingState());
        $object->setListingState($state);
      }
    }
    return parent::_beforeSave($object);
  }

}