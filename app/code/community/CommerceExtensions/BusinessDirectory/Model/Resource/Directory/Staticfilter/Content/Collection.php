<?php

class CommerceExtensions_BusinessDirectory_Model_Resource_Directory_Staticfilter_Content_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
  protected function _construct()
  {
    $this->_init('businessdirectory/directory_staticfilter_content');
  }
}