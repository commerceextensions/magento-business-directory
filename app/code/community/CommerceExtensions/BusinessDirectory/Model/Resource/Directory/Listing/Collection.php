<?php

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Listing_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
  protected function _construct()
  {
    $this->_init('businessdirectory/directory_listing');
  }

  protected function _beforeLoad()
  {
    $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
    $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');

    $this->getSelect()
         ->joinLeft(
           array('ce1' => Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar')),
           "ce1.entity_id=main_table.customer_id AND ce1.attribute_id=" . $fn->getAttributeId(),
           array('value'))
         ->joinLeft(
           array('ce2' => Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar')),
           "ce2.entity_id=main_table.customer_id AND ce2.attribute_id=" . $ln->getAttributeId(),
           array('value'))
         ->columns(new Zend_Db_Expr("CONCAT(ce1.value, ' ',ce2.value) AS customer_name"));
    return parent::_beforeLoad();
  }
}