<?php
/**
 * Directory.php
 * CommerceExtensions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Product
 * @package    Business Directory
 * @copyright  Copyright (c) 2003-2019 CommerceExtensions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
 
class CommerceExtensions_BusinessDirectory_Model_Resource_Directory extends Mage_Core_Model_Resource_Db_Abstract
{
  protected $_store = null;

  protected function _construct()
  {
    $this->_init('businessdirectory/directory', 'directory_id');
  }

  protected function _beforeDelete(Mage_Core_Model_Abstract $object)
  {
    $condition = array(
      'directory_id = ?' => (int)$object->getDirectoryId(),
    );
    $this->_getWriteAdapter()->delete($this->getTable('businessdirectory/directory_store'), $condition);
    $this->_getWriteAdapter()->delete($this->getTable('businessdirectory/directory_listing_submit'), $condition);
    return parent::_beforeDelete($object);
  }

  protected function _beforeSave(Mage_Core_Model_Abstract $object)
  {
    if(!$this->getIsUniqueDirectoryToStores($object)) {
      Mage::throwException(Mage::helper('businessdirectory')->__('A Directory URL key for specified store already exists.'));
    }

    if(!$this->isValidDirectoryIdentifier($object)) {
      Mage::throwException(Mage::helper('businessdirectory')->__('The Directory URL key contains capital letters or disallowed symbols.'));
    }

    if($this->isNumericDirectoryIdentifier($object)) {
      Mage::throwException(Mage::helper('businessdirectory')->__('The Directory URL key cannot consist only of numbers.'));
    }

    // modify create / update dates
    if($object->isObjectNew() && !$object->hasCreationTime()) {
      $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
    }

    $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

    return parent::_beforeSave($object);
  }

  /**
   * Assign page to store views
   *
   * @param Mage_Core_Model_Abstract $object
   */
  protected function _afterSave(Mage_Core_Model_Abstract $object)
  {
    $oldStores = $this->lookupStoreIds($object->getId());
    $newStores = (array)$object->getStores();
    if(empty($newStores)) {
      $newStores = (array)$object->getStoreId();
    }
    $table  = $this->getTable('businessdirectory/directory_store');
    $insert = array_diff($newStores, $oldStores);
    $delete = array_diff($oldStores, $newStores);

    if($delete) {
      $where = array(
        'directory_id = ?' => (int)$object->getId(),
        'store_id IN (?)'  => $delete
      );

      $this->_getWriteAdapter()->delete($table, $where);
    }

    if($insert) {
      $data = array();

      foreach($insert as $storeId) {
        $data[] = array(
          'directory_id' => (int)$object->getId(),
          'store_id'     => (int)$storeId
        );
      }

      $this->_getWriteAdapter()->insertMultiple($table, $data);
    }

    return parent::_afterSave($object);
  }

  /**
   * Load an object using 'identifier' field if there's no field specified and value is not numeric
   *
   * @param Mage_Core_Model_Abstract $object
   * @param mixed                    $value
   * @param string                   $field
   */
  public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
  {
    if(!is_numeric($value) && is_null($field)) {
      $field = 'identifier';
    }

    return parent::load($object, $value, $field);
  }

  protected function _afterLoad(Mage_Core_Model_Abstract $object)
  {
    if($object->getId()) {
      $stores = $this->lookupStoreIds($object->getId());
      $object->setData('store_id', $stores);
    }
    return parent::_afterLoad($object);
  }

  protected function _getLoadSelect($field, $value, $object)
  {
    $select = parent::_getLoadSelect($field, $value, $object);

    if($object->getStoreId()) {
      $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
      $select->join(
        array('businessdirectory_directory_store' => $this->getTable('businessdirectory/directory_store')),
        $this->getMainTable() . '.directory_id = businessdirectory_directory_store.directory_id',
        array())
             ->where('is_active = ?', 1)
             ->where('businessdirectory_directory_store.store_id IN (?)', $storeIds)
             ->order('businessdirectory_directory_store.store_id DESC')
             ->limit(1);
    }

    return $select;
  }

  protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
  {
    $select = $this->_getReadAdapter()->select()
                   ->from(array('cp' => $this->getMainTable()))
                   ->join(
                     array('cps' => $this->getTable('businessdirectory/directory_store')),
                     'cp.directory_id = cps.directory_id',
                     array())
                   ->where('cp.identifier = ?', $identifier)
                   ->where('cps.store_id IN (?)', $store);

    if(!is_null($isActive)) {
      $select->where('cp.is_active = ?', $isActive);
    }

    return $select;
  }

  public function getIsUniqueDirectoryToStores(Mage_Core_Model_Abstract $object)
  {
    if(Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
      $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
    } else {
      $stores = (array)$object->getData('stores');
    }

    $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

    if($object->getId()) {
      $select->where('cps.directory_id <> ?', $object->getId());
    }

    if($this->_getWriteAdapter()->fetchRow($select)) {
      return false;
    }

    return true;
  }

  protected function isValidDirectoryIdentifier(Mage_Core_Model_Abstract $object)
  {
    return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
  }

  protected function isNumericDirectoryIdentifier(Mage_Core_Model_Abstract $object)
  {
    return preg_match('/^[0-9]+$/', $object->getData('identifier'));
  }

  public function checkIdentifier($identifier, $storeId)
  {
    $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
    $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
    $select->reset(Zend_Db_Select::COLUMNS)
           ->columns('cp.directory_id')
           ->order('cps.store_id DESC')
           ->limit(1);

    return $this->_getReadAdapter()->fetchOne($select);
  }

  public function getDirectoryTitleByIdentifier($identifier)
  {
    $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
    if($this->_store) {
      $stores[] = (int)$this->getStore()->getId();
    }

    $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
    $select->reset(Zend_Db_Select::COLUMNS)
           ->columns('cp.title')
           ->order('cps.store_id DESC')
           ->limit(1);

    return $this->_getReadAdapter()->fetchOne($select);
  }

  public function getDirectoryTitleById($id)
  {
    $adapter = $this->_getReadAdapter();

    $select = $adapter->select()
                      ->from($this->getMainTable(), 'title')
                      ->where('directory_id = :directory_id');

    $binds = array(
      'directory_id' => (int)$id
    );

    return $adapter->fetchOne($select, $binds);
  }

  public function getDirectoryIdentifierById($id)
  {
    $adapter = $this->_getReadAdapter();

    $select = $adapter->select()
                      ->from($this->getMainTable(), 'identifier')
                      ->where('directory_id = :directory_id');

    $binds = array(
      'directory_id' => (int)$id
    );

    return $adapter->fetchOne($select, $binds);
  }

  public function lookupStoreIds($directoryId)
  {
    $adapter = $this->_getReadAdapter();

    $select = $adapter->select()
                      ->from($this->getTable('businessdirectory/directory_store'), 'store_id')
                      ->where('directory_id = ?', (int)$directoryId);

    return $adapter->fetchCol($select);
  }

  public function setStore($store)
  {
    $this->_store = $store;
    return $this;
  }

  public function getStore()
  {
    return Mage::app()->getStore($this->_store);
  }

}