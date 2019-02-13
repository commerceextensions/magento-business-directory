<?php

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Staticfilter extends Mage_Core_Model_Resource_Db_Abstract
{
  protected function _construct()
  {
    $this->_init('businessdirectory/directory_staticfilter', 'id');
  }
}