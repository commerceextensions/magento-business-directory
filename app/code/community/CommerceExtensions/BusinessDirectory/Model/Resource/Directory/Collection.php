<?php
/**
 * Collection.php
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

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
  protected $_previewFlag;

  protected function _construct()
  {
    $this->_init('businessdirectory/directory');
    $this->_map['fields']['directory_id'] = 'main_table.directory_id';
    $this->_map['fields']['store']        = 'store_table.store_id';
  }

  public function toOptionArray()
  {
    return $this->_toOptionArray('identifier', 'title');
  }

  /**
   * Returns pairs identifier - title for unique identifiers
   * and pairs identifier|page_id - title for non-unique after first
   *
   * @return array
   */
  public function toOptionIdArray()
  {
    $res                 = array();
    $existingIdentifiers = array();
    foreach($this as $item) {
      $identifier = $item->getData('identifier');

      $data['value'] = $identifier;
      $data['label'] = $item->getData('title');

      if(in_array($identifier, $existingIdentifiers)) {
        $data['value'] .= '|' . $item->getData('directory_id');
      } else {
        $existingIdentifiers[] = $identifier;
      }

      $res[] = $data;
    }

    return $res;
  }

  public function setFirstStoreFlag($flag = false)
  {
    $this->_previewFlag = $flag;
    return $this;
  }

  protected function _afterLoad()
  {
    if($this->_previewFlag) {
      $items      = $this->getColumnValues('directory_id');
      $connection = $this->getConnection();
      if(count($items)) {
        $select = $connection->select()
                             ->from(array('cps' => $this->getTable('businessdirectory/directory_store')))
                             ->where('cps.directory_id IN (?)', $items);
        if($result = $connection->fetchPairs($select)) {
          foreach($this as $item) {
            if(!isset($result[$item->getData('directory_id')])) {
              continue;
            }
            if($result[$item->getData('directory_id')] == 0) {
              $stores    = Mage::app()->getStores(false, true);
              $storeId   = current($stores)->getId();
              $storeCode = key($stores);
            } else {
              $storeId   = $result[$item->getData('directory_id')];
              $storeCode = Mage::app()->getStore($storeId)->getCode();
            }
            $item->setData('_first_store_id', $storeId);
            $item->setData('store_code', $storeCode);
          }
        }
      }
    }

    return parent::_afterLoad();
  }

  public function addStoreFilter($store, $withAdmin = true)
  {
    if(!$this->getFlag('store_filter_added')) {
      if($store instanceof Mage_Core_Model_Store) {
        $store = array($store->getId());
      }

      if(!is_array($store)) {
        $store = array($store);
      }

      if($withAdmin) {
        $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
      }

      $this->addFilter('store', array('in' => $store), 'public');
    }
    return $this;
  }

  protected function _renderFiltersBefore()
  {
    if($this->getFilter('store')) {
      $this->getSelect()->join(
        array('store_table' => $this->getTable('businessdirectory/directory_store')),
        'main_table.directory_id = store_table.directory_id',
        array()
      )->group('main_table.directory_id');

      /*
       * Allow analytic functions usage because of one field grouping
       */
      $this->_useAnalyticFunction = true;
    }
    return parent::_renderFiltersBefore();
  }

  public function getSelectCountSql()
  {
    $countSelect = parent::getSelectCountSql();

    $countSelect->reset(Zend_Db_Select::GROUP);

    return $countSelect;
  }
}