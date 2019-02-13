<?php
/**
 * Compare.php
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
class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Pending_Compare extends Mage_Core_Block_Template
{
  public function getComparisonArray()
  {
    if($id = $this->getRequest()->getParam('id')) {
      $model = Mage::getModel('businessdirectory/directory_pending_compare', $id);
      return $model->getComparisonArray();
    }
    return array();
  }

  public function getComparisonFields()
  {
    $data          = $this->getComparisonArray();
    $currentFields = array_keys($data['current']);
    $pendingFields = array_keys($data['pending']);
    return array_unique(array_merge($currentFields, $pendingFields));
  }

  public function getComparisonLabel($field)
  {
    $model = Mage::getModel('businessdirectory/directory_pending_compare');
    $array = $model->getComparisonFieldsArray();
    return $array[$field];
  }

}