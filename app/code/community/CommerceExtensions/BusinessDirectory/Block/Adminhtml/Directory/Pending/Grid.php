<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Pending_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultSort('id');
    $this->setId('pendingSubmissionGrid');
    $this->setDefaultDir('asc');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getModel('businessdirectory/directory_listing_submit')->getCollection();
    $this->setCollection($collection);
    parent::_prepareCollection();
    return $this;
  }

  protected function _prepareColumns()
  {
    $this->addColumn('id',
                     array(
                       'header' => $this->__('ID'),
                       'align'  => 'left',
                       'width'  => '80px',
                       'index'  => 'id',
                       'type'   => 'number'
                     )
    );
    $this->addColumn('title',
                     array(
                       'header' => $this->__('Assigned Directory'),
                       'align'  => 'left',
                       'index'  => 'title',
                     )
    );
    $this->addColumn('customer_name',
                     array(
                       'header'       => $this->__('Business Owner Name'),
                       'align'        => 'left',
                       'index'        => 'customer_name',
                       'filter_index' => "TRIM(CONCAT(ce1.value,' ',ce2.value))",
                     )
    );
    $this->addColumn('listing_name',
                     array(
                       'header' => $this->__('Business Name'),
                       'align'  => 'left',
                       'index'  => 'listing_name',
                     )
    );
    $this->addColumn('listing_website',
                     array(
                       'header'   => $this->__('Website'),
                       'align'    => 'left',
                       'width'    => '150px',
                       'index'    => 'listing_website',
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Url'
                     )
    );
    $this->addColumn('backlink',
                     array(
                       'header'   => $this->__('Backlink'),
                       'align'    => 'left',
                       'width'    => '150px',
                       'index'    => 'backlink',
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Url'
                     )
    );
    $this->addColumn('listing_city',
                     array(
                       'header' => $this->__('City'),
                       'align'  => 'left',
                       'width'  => '150px',
                       'index'  => 'listing_city',
                     )
    );
    $this->addColumn('listing_state',
                     array(
                       'header' => $this->__('State/Province'),
                       'align'  => 'left',
                       'width'  => '80px',
                       'index'  => 'listing_state',
                     )
    );
    $this->addColumn('listing_country',
                     array(
                       'header' => $this->__('Country'),
                       'align'  => 'left',
                       'width'  => '80px',
                       'index'  => 'listing_country',
                     )
    );
    $this->addColumn('is_featured', array(
      'header'  => $this->__('Featured'),
      'index'   => 'is_featured',
      'type'    => 'options',
      'width'   => '80px',
      'options' => array(
        0 => $this->__('No'),
        1 => $this->__('Yes')
      ),
    ));
    $this->addColumn('creation_time', array(
      'header' => $this->__('Submission Time'),
      'index'  => 'creation_time',
      'type'   => 'datetime',
      'align'  => 'center',
      'width'  => 200
    ));
    $this->addColumn('action', array(
      'header'   => $this->__('Action'),
      'index'    => 'action',
      'type'     => 'options',
      'width'    => '100px',
      'align'    => 'center',
      'options'  => array(
        'new'    => $this->__('New'),
        'claim'  => $this->__('Claimed'),
        'update' => $this->__('Updated')
      ),
      'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Pending_Grid_Renderer_Action'
    ));
    return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('id');
    $this->getMassactionBlock()->setFormFieldName('id');
    $this->getMassactionBlock()->addItem('delete', array(
      'label'   => $this->__('Delete'),
      'url'     => $this->getUrl('*/*/massDelete'),
      'confirm' => $this->__('Are you sure you want to delete the selected submissions?')
    ));

    Mage::dispatchEvent('businessdirectory_directory_pending_grid_massaction_prepare', array('block' => $this));
    return $this;
  }

  public function getGridUrl()
  {
    return $this->getUrl('*/*/grid', array('_current' => true));
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
}