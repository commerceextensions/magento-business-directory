<?php
/**
 * Grid.php
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
 
class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultSort('directory_id');
    $this->setId('businessDirectoryGrid');
    $this->setDefaultDir('asc');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getModel('businessdirectory/directory')->getCollection();
    $collection->setFirstStoreFlag(true);
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _filterStoreCondition($collection, $column)
  {
    if(!$value = $column->getFilter()->getValue()) {
      return;
    }
    $this->getCollection()->addStoreFilter($value);
  }

  protected function _prepareColumns()
  {
    $this->addColumn('directory_id',
                     array(
                       'header' => $this->__('Directory ID'),
                       'align'  => 'left',
                       'width'  => '100px',
                       'index'  => 'directory_id',
                       'type'   => 'number'
                     )
    );
    $this->addColumn('title',
                     array(
                       'header' => $this->__('Directory Title'),
                       'align'  => 'left',
                       'index'  => 'title',
                     )
    );
    $this->addColumn('identifier',
                     array(
                       'header' => $this->__('Directory URL'),
                       'align'  => 'left',
                       'index'  => 'identifier',
                       'width'  => '100px',
                     )
    );
    if(!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('store_id',
                       array(
                         'header'     => $this->__('Store View'),
                         'index'      => 'store_id',
                         'type'       => 'store',
                         'store_all'  => true,
                         'store_view' => true,
                         'sortable'   => false,
                         'filter_condition_callback'
                                      => array($this, '_filterStoreCondition'),
                       ));
    }
    $this->addColumn('is_active',
                     array(
                       'header'  => $this->__('Status'),
                       'index'   => 'is_active',
                       'type'    => 'options',
                       'width'   => '100px',
                       'options' => array(
                         0 => $this->__('Disabled'),
                         1 => $this->__('Enabled')
                       ),
                     ));
    return parent::_prepareColumns();
  }

  protected function _afterLoadCollection()
  {
    $this->getCollection()->walk('afterLoad');
    parent::_afterLoadCollection();
  }

  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('id');
    $this->getMassactionBlock()->setFormFieldName('directory_id');

    $this->getMassactionBlock()->addItem('delete', array(
      'label'   => $this->__('Delete'),
      'url'     => $this->getUrl('*/*/massDelete'),
      'confirm' => $this->__('Are you sure you want to delete the selected directories?')
    ));

    $statuses = array(
      array('label' => '', 'value' => ''),
      array('label' => $this->__('Disabled'), 'value' => 0),
      array('label' => $this->__('Enabled'), 'value' => 1)
    );
    $this->getMassactionBlock()->addItem('status', array(
      'label'      => $this->__('Change status'),
      'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
      'additional' => array(
        'visibility' => array(
          'name'   => 'status',
          'type'   => 'select',
          'class'  => 'required-entry',
          'label'  => $this->__('Status'),
          'values' => $statuses
        )
      )
    ));

    Mage::dispatchEvent('businessdirectory_directory_grid_massaction_prepare', array('block' => $this));
    return $this;
  }

  public function getGridUrl()
  {
    return $this->getUrl('*/*/grid', array('_current' => true));
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('directory_id' => $row->getId()));
  }
}