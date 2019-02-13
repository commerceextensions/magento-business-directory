<?php
/**
 * Listing.php
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

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Listing extends Mage_Core_Model_Resource_Db_Abstract
{

  protected $_store = null;

  protected function _construct()
  {
    $this->_init('businessdirectory/directory_listing', 'listing_id');
  }

  protected function _beforeSave(Mage_Core_Model_Abstract $object)
  {
    $helper = Mage::helper('businessdirectory');

    if(!$object->getIdentifier()) {
      $urlKey = str_ireplace("'s", "s", $object->getListingName());
      $object->setIdentifier(Mage::getModel('catalog/product_url')->formatUrlKey($urlKey));
    }

    if(!$this->getIsUniqueListing($object)) {
      Mage::throwException($helper->__('A Listing URL key for this directory already exists.'));
    }

    if(!$this->isValidListingIdentifier($object)) {
      Mage::throwException($helper->__('The Listing URL key contains capital letters or disallowed symbols.'));
    }

    if($this->isNumericListingIdentifier($object)) {
      Mage::throwException($helper->__('The Listing URL key cannot consist only of numbers.'));
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

    // modify create / update dates
    if($object->isObjectNew() && !$object->hasCreationTime()) {
      $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
    }

    $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

    return parent::_beforeSave($object);
  }

  protected function isValidListingIdentifier(Mage_Core_Model_Abstract $object)
  {
    return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
  }

  protected function isNumericListingIdentifier(Mage_Core_Model_Abstract $object)
  {
    return preg_match('/^[0-9]+$/', $object->getData('identifier'));
  }

  public function getIsUniqueListing(Mage_Core_Model_Abstract $object)
  {
    $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $object->getData('directory_id'));

    if($object->getListingId()) {
      $select->where('cp.listing_id <> ?', $object->getListingId());
    }

    if($this->_getWriteAdapter()->fetchRow($select)) {
      return false;
    }
    return true;
  }

  protected function _getLoadByIdentifierSelect($identifier, $directoryId = null, $isActive = null)
  {
    $select = $this->_getReadAdapter()->select()
                   ->from(array('cp' => $this->getMainTable()))
                   ->join(
                     array('cps' => $this->getTable('businessdirectory/directory')),
                     'cp.directory_id = cps.directory_id',
                     array())
                   ->where('cp.identifier = ?', $identifier);

    if($directoryId) {
      $select->where('cp.directory_id = ?', $directoryId);
    }

    if(!is_null($isActive)) {
      $select->where('cp.is_active = ?', $isActive);
    }
    return $select;
  }

  public function checkIdentifier($identifier, $directoryId)
  {
    $select = $this->_getLoadByIdentifierSelect($identifier, $directoryId);

    $select->reset(Zend_Db_Select::COLUMNS)
           ->columns('cp.listing_id')
           ->order('cps.directory_id DESC')
           ->limit(1);
    return $this->_getReadAdapter()->fetchOne($select);
  }

  public function getListingNameByIdentifier($identifier)
  {
    $directoryId = array($directoryId);
    $select      = $this->_getLoadByIdentifierSelect($identifier);
    $select->reset(Zend_Db_Select::COLUMNS)
           ->columns('cp.listing_name')
           ->order('cps.directory_id DESC')
           ->limit(1);

    return $this->_getReadAdapter()->fetchOne($select);
  }

  public function getListingNameById($id)
  {
    $adapter = $this->_getReadAdapter();

    $select = $adapter->select()
                      ->from($this->getMainTable(), 'listing_name')
                      ->where('listing_id = :listing_id');

    $binds = array(
      'listing_id' => (int)$id
    );

    return $adapter->fetchOne($select, $binds);
  }

  public function getListingIdentifierById($id)
  {
    $adapter = $this->_getReadAdapter();

    $select = $adapter->select()
                      ->from($this->getMainTable(), 'identifier')
                      ->where('listing_id = :listing_id');

    $binds = array(
      'listing_id' => (int)$id
    );

    return $adapter->fetchOne($select, $binds);
  }

  protected function _beforeDelete(Mage_Core_Model_Abstract $object)
  {
    $condition = array(
      'listing_id = ?' => (int)$object->getListingId(),
    );
    $this->_getWriteAdapter()->delete($this->getTable('businessdirectory/directory_listing_submit'), $condition);
    return parent::_beforeDelete($object);
  }

}