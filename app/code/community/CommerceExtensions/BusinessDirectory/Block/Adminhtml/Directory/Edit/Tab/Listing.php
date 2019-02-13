<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $model = Mage::registry('directory_data');

    $form     = new Varien_Data_Form(array(
                                       'id'      => 'upload_form',
                                       'action'  => $this->getUrl('*/businessdirectory_listing/uploadCsv', array('directory_id' => $model->getDirectoryId())),
                                       'method'  => 'post',
                                       'enctype' => 'multipart/form-data',
                                     )
    );
    $fieldset = $form->addFieldset('listing_upload', array('legend' => $this->__('Upload Directory Listings via CSV')));
    $upload   = $fieldset->addField('upload', 'select', array(
      'name'     => 'upload',
      'label'    => $this->__('Upload New CSV?'),
      'options'  => array(0 => 'No', 1 => 'Yes'),
      'required' => false
    ));
    $geocode  = $fieldset->addField('geocode', 'select', array(
      'name'     => 'geocode',
      'label'    => $this->__('Geocode Addresses on Import?'),
      'options'  => array(1 => 'Yes', 0 => 'No'),
      'required' => false
    ));
    $file     = $fieldset->addField('listing_csv', 'file', array(
      'name'               => 'listing_csv',
      'label'              => $this->__('Listings CSV'),
      'title'              => $this->__('Listings CSV'),
      'required'           => true,
      'class'              => 'required-file',
      'after_element_html' => '<div style="font-size:11px; margin-top:5px;width:600px;"> <strong>Helpful Hints for Upload</strong>
			  <ul style="list-style:disc;font-size:11px; margin-left:15px;">
			    <li style="margin:0px;">If you need to Geocode more than 5,000 address, it is recommended that you pre-geocode your list prior to uploading with a service like Smarty Streets. You can visit their site by clicking <a href="http://smartystreets.com/" target="_blank">here</a>. It is not free but they are USPS certified and are among the least expensive geocoding services available.</li>
			    <li style="margin:0px;"><strong>The CSV that you upload will not replace your current listings, it will add to them.</strong></li>
				<li style="margin:0px;">Make sure all fields in your CSV are enclosed in "quotes".</li>
				<li style="margin:0px;">Don\'t use Excel to edit your CSV, Instead, use OpenOffice CALC program. Excel tends to strip quotes from fields when saving a CSV. You can download OpenOffice for free by clicking <a href="http://www.openoffice.org/download/" target="_blank">here</a>.</li>				
				<li style="margin:0px;">Don\'t include the listing_id or directory_id columns in your CSV, they will be automatically assigned.</li>
				<li style="margin:0px;"><strong>The ' . implode(', ', Mage::getModel('businessdirectory/directory_listing')->REQUIRED_CSV_COLUMNS) . ' columns are required for import</strong>. In the "listing_country" column, make sure to use the 2-letter ISO-3166 country code. To view these codes, please click <a href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements" target="_blank">here</a>.</li>
				<li style="margin:0px;">If you are importing a large list and find that the operation is timing out, break it up into multiple lists. When using the Geocoding tool for your import, it can take some time to fully import.</li>
			  </ul>
			  </div>
			'
    ));

    Mage::dispatchEvent('businessdirectory_directory_csv_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);
    $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                                       ->addFieldMap($upload->getHtmlId(), $upload->getName())
                                       ->addFieldMap($file->getHtmlId(), $file->getName())
                                       ->addFieldMap($geocode->getHtmlId(), $geocode->getName())
                                       ->addFieldDependence($file->getName(), $upload->getName(), 1)
                                       ->addFieldDependence($geocode->getName(), $upload->getName(), 1)
    );
    return parent::_prepareForm();
  }
}