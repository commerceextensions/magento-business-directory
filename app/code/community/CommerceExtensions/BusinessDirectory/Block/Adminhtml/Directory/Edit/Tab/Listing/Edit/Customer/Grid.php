<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultLimit(50);
  }

  protected function _prepareMassaction()
  {
    return null;
  }

  protected function _prepareColumns()
  {
    parent::_prepareColumns();
    unset($this->_columns['website_id']);
    unset($this->_columns['action']);
    unset($this->_columns['group']);
    unset($this->_columns['customer_since']);
    unset($this->_exportTypes);
  }

  public function getRowUrl($row)
  {
    return null;
  }

  public function getGridUrl()
  {
    return $this->getUrl('*/businessdirectory_listing/customerGrid', array('_current' => true));
  }

}