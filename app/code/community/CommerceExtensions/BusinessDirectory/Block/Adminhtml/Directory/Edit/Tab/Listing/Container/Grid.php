<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setDefaultSort('grid_listing_id');
    $this->setId('directoryListingsGrid');
    $this->setDefaultDir('asc');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
  }

  protected function _prepareCollection()
  {
    $collection  = Mage::getModel('businessdirectory/directory_listing')->getCollection();
    $directoryId = $this->getRequest()->getParam('directory_id');
    if($directoryId) {
      $collection->addFieldToFilter('directory_id', $directoryId);
    }
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $fieldPrefix = 'grid_';
    $this->addColumn($fieldPrefix . 'listing_id',
                     array(
                       'header' => $this->__('ID'),
                       'align'  => 'left',
                       'width'  => '50px',
                       'index'  => 'listing_id',
                       'type'   => 'number'
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_image',
                     array(
                       'header'   => $this->__('Image'),
                       'align'    => 'center',
                       'width'    => '60px',
                       'index'    => 'listing_image',
                       'filter'   => false,
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Grid_Renderer_Image'
                     )
    );
    $this->addColumn($fieldPrefix . 'customer_name',
                     array(
                       'header'       => $this->__('Business Owner Name'),
                       'align'        => 'left',
                       'index'        => 'customer_name',
                       'filter_index' => "TRIM(CONCAT(ce1.value,' ',ce2.value))",
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_name',
                     array(
                       'header' => $this->__('Business Name'),
                       'align'  => 'left',
                       'index'  => 'listing_name',
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_city',
                     array(
                       'header' => $this->__('City'),
                       'align'  => 'left',
                       'width'  => '100px',
                       'index'  => 'listing_city',
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_state',
                     array(
                       'header' => $this->__('State/Province'),
                       'align'  => 'left',
                       'width'  => '100px',
                       'index'  => 'listing_state',
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_zip_code',
                     array(
                       'header' => $this->__('ZIP'),
                       'width'  => '90px',
                       'index'  => 'listing_zip_code',
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_country',
                     array(
                       'header' => $this->__('Country'),
                       'width'  => '100px',
                       'type'   => 'country',
                       'index'  => 'listing_country',
                     )
    );
    $this->addColumn($fieldPrefix . 'listing_website',
                     array(
                       'header'   => $this->__('Website'),
                       'width'    => '150px',
                       'index'    => 'listing_website',
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Grid_Renderer_Url'
                     )
    );
    $this->addColumn($fieldPrefix . 'latitude',
                     array(
                       'header'   => $this->__('Latitude'),
                       'width'    => '100px',
                       'index'    => 'latitude',
                       'type'     => 'number',
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Grid_Renderer_Coordinates'
                     )
    );
    $this->addColumn($fieldPrefix . 'longitude',
                     array(
                       'header'   => $this->__('Longitude'),
                       'width'    => '100px',
                       'index'    => 'longitude',
                       'type'     => 'number',
                       'renderer' => 'CommerceExtensions_BusinessDirectory_Model_Directory_Listing_Grid_Renderer_Coordinates'
                     )
    );
    $this->addColumn($fieldPrefix . 'is_featured', array(
      'header'  => $this->__('Featured'),
      'index'   => 'is_featured',
      'type'    => 'options',
      'width'   => '80px',
      'options' => array(
        0 => $this->__('No'),
        1 => $this->__('Yes')
      ),
    ));
    $this->addColumn($fieldPrefix . 'is_active', array(
      'header'  => $this->__('Status'),
      'index'   => 'is_active',
      'type'    => 'options',
      'width'   => '100px',
      'options' => array(
        0 => $this->__('Disabled'),
        1 => $this->__('Enabled')
      ),
    ));

    $this->addExportType('*/businessdirectory_listing/exportCsv', $this->__('CSV'));
    return parent::_prepareColumns();
  }

  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('id');
    $this->getMassactionBlock()->setFormFieldName('listing_id');

    $params = array('_current' => true, 'active_tab' => 'directory_listings');
    $this->getMassactionBlock()->addItem('delete', array(
      'label'   => $this->__('Delete'),
      'url'     => $this->getUrl('*/businessdirectory_listing/massDelete', $params),
      'confirm' => $this->__('Are you sure you want to delete the selected listing(s)?')
    ));

    $statuses = array(
      array('label' => '', 'value' => ''),
      array('label' => $this->__('Disabled'), 'value' => 0),
      array('label' => $this->__('Enabled'), 'value' => 1)
    );
    $this->getMassactionBlock()->addItem('status', array(
      'label'      => $this->__('Change status'),
      'url'        => $this->getUrl('*/businessdirectory_listing/massStatus', $params),
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
    $this->getMassactionBlock()->addItem('massgeocode', array(
      'label'   => $this->__('Bulk Geocode'),
      'url'     => $this->getUrl('*/businessdirectory_listing/massGeocode', $params),
      'confirm' => $this->__('Are you sure you want to get Latitude & Longitude coordinates for the selected listings? If you have selected a large number of listings, the process could take some time and/or you could exceed the limits of the Google Maps and Bing Maps APIs that are used to obtain the coordinates.')
    ));

    Mage::dispatchEvent('businessdirectory_directory_listing_grid_massaction_prepare', array('block' => $this));
    return $this;
  }

  public function getGridUrl()
  {
    return $this->getUrl('*/businessdirectory_listing/grid', array('_current' => true));
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/businessdirectory_listing/edit', array('listing_id' => $row->getId(), 'directory_id' => $this->getRequest()->getParam('directory_id')));
  }
}