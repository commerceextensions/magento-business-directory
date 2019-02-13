<?php

class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $model = Mage::registry('listing_data');

    $directoryId = $model->getDirectoryId() ? $model->getDirectoryId() : $this->getRequest()->getParam('directory_id');
    $directory   = Mage::getModel('businessdirectory/directory')->load($directoryId);

    $params                 = array();
    $params['directory_id'] = $directoryId;
    if($model->getListingId()) {
      $params['listing_id'] = $model->getListingId();
    }
    $form     = new Varien_Data_Form(array(
                                       'id'      => 'edit_form',
                                       'action'  => $this->getUrl('*/businessdirectory_listing/save', $params),
                                       'method'  => 'post',
                                       'enctype' => 'multipart/form-data'
                                     )
    );
    $fieldset = $form->addFieldset('owner_fieldset', array(
      'legend' => $this->__('Business Owner Information'),
    ));

    if($directoryId) {
      $fieldset->addField('directory_name', 'note', array(
        'name'  => 'directory_name',
        'label' => $this->__('Directory Name'),
        'text'  => $this->__($directory->getTitle())
      ));
    }
    if($model->getListingId()) {
      $fieldset->addField('listing_id', 'note', array(
        'name'  => 'listing_id',
        'label' => $this->__('Listing ID'),
        'text'  => $this->__($model->getListingId())
      ));
    }
    $fieldset->addField('customer_id', 'text', array(
      'name'               => 'customer_id',
      'label'              => $this->__('Customer ID'),
      'title'              => $this->__('Customer ID'),
      'class'              => 'validate-digits',
      'required'           => false,
      'after_element_html' => '<button title="Find Customer" type="button" class="scalable save" id="find-customer"><span><span><span>Find Customer</span></span></span></button>'
    ));
    $customerName = is_null($model->getCustomerId()) || $model->getCustomerId() == 0 ? 'Not yet assigned to customer' : $model->getCustomerName();
    $fieldset->addField('customer_name', 'note', array(
      'text'  => $this->__($customerName),
      'label' => $this->__('Customer Name'),
      'title' => $this->__('Customer Name'),
    ));
    $fieldset = $form->addFieldset('base_fieldset', array(
      'legend' => $this->__('Business Information'),
    ));
    $fieldset->addField('directory_id', 'hidden', array(
      'name'  => 'directory_id',
      'value' => $directoryId
    ));
    $fieldset->addField('is_active', 'select', array(
      'label'    => $this->__('Status'),
      'title'    => $this->__('Status'),
      'name'     => 'is_active',
      'required' => true,
      'options'  => array(1 => 'Enabled', 0 => 'Disabled'),
    ));
    $fieldset->addField('identifier', 'text', array(
      'name'     => 'identifier',
      'label'    => $this->__('Listing Url Key'),
      'title'    => $this->__('Listing Url Key'),
      'class'    => 'validate-identifier',
      'required' => false,
    ));
    $fieldset->addField('listing_name', 'text', array(
      'name'     => 'listing_name',
      'label'    => $this->__('Listing Name'),
      'title'    => $this->__('Listing Name'),
      'required' => true,
      'class'    => 'required-entry input-text'
    ));
    $fieldset->addField('address_line_one', 'text', array(
      'name'     => 'address_line_one',
      'label'    => $this->__('Address'),
      'title'    => $this->__('Address Line 1'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $fieldset->addField('address_line_two', 'text', array(
      'name'     => 'address_line_two',
      'title'    => $this->__('Address Line 2'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $fieldset->addField('listing_city', 'text', array(
      'name'     => 'listing_city',
      'label'    => $this->__('City'),
      'title'    => $this->__('City'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $fieldset->addField('listing_state', 'text', array(
      'name'     => 'listing_state',
      'label'    => $this->__('State/Province'),
      'title'    => $this->__('State/Province'),
      'required' => false,
      'class'    => 'validate-state'
    ));
    $fieldset->addField('listing_zip_code', 'text', array(
      'name'     => 'listing_zip_code',
      'label'    => $this->__('Zip/Postal Code'),
      'title'    => $this->__('Zip/Postal Code'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    $country   = $fieldset->addField('listing_country', 'select', array(
      'name'     => 'listing_country',
      'label'    => $this->__('Country'),
      'title'    => $this->__('Country'),
      'required' => true,
      'class'    => 'countries countriesselect',
      'values'   => $countries
    ));
    $fieldset->addField('geocode_on_save', 'checkbox', array(
      'name'               => 'geocode_on_save',
      'label'              => $this->__('Geocode On Save?'),
      'title'              => $this->__('Geocode On Save?'),
      'onclick'            => 'this.value = this.checked ? 1 : 0;',
      'required'           => false,
      'after_element_html' => '<label for="geocode_on_save">Get Latitude and Longitude coordinates on save.<br /> This will not work if you do not enter an address.</label>'
    ));
    $fieldset->addField('listing_comments', 'textarea', array(
      'name'               => 'listing_comments',
      'label'              => $this->__('Additional Info'),
      'title'              => $this->__('Additional Info'),
      'disabled'           => false,
      'style'              => 'height:50px;',
      'class'              => 'validate-length maximum-length-255 textarea',
      'onkeyup'            => 'checkMaxLength(this, 255)',
      'after_element_html' => '<p class="note">Maximum 255 chars. Additional info to display with listing on frontend.</p>'
    ));

    $fieldset = $form->addFieldset('contact_fieldset', array(
      'legend' => $this->__('Contact Information'),
    ));
    $fieldset->addField('listing_contact_name', 'text', array(
      'name'     => 'listing_contact_name',
      'label'    => $this->__('Contact Name'),
      'title'    => $this->__('Contact Name'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $fieldset->addField('listing_email', 'text', array(
      'name'     => 'listing_email',
      'label'    => $this->__('Email'),
      'title'    => $this->__('Email'),
      'required' => false,
      'class'    => 'validate-email input-text'
    ));
    $fieldset->addField('listing_website', 'text', array(
      'name'     => 'listing_website',
      'label'    => $this->__('Website'),
      'title'    => $this->__('Website'),
      'required' => false,
      'class'    => 'validate-url input-text'
    ));
    $fieldset->addField('listing_phone', 'text', array(
      'name'     => 'listing_phone',
      'label'    => $this->__('Telephone'),
      'title'    => $this->__('Telephone'),
      'required' => false,
      'class'    => 'input-text'
    ));
    $lastElement = $fieldset->addField('listing_fax', 'text', array(
      'name'     => 'listing_fax',
      'label'    => $this->__('Fax'),
      'title'    => $this->__('Fax'),
      'required' => false,
      'class'    => 'input-text'
    ));

    $fieldset   = $form->addFieldset('featured_fieldset', array(
      'legend' => $this->__('For Featured Listings Only'),
    ));
    $featured   = $fieldset->addField('is_featured', 'select', array(
      'name'               => 'is_featured',
      'label'              => $this->__('Featured Listing'),
      'title'              => $this->__('Featured Listing'),
      'required'           => false,
      'values'             => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
      'after_element_html' => '<p class="note">Add this listing to the featured section? If setting this listing as a "Featured Listing", make sure you have either added a link to the listing website or some other form of contact info to display or your users will have no way to reach out to the listing.</p>'
    ));
    $backlink   = $fieldset->addField('backlink', 'text', array(
      'name'     => 'backlink',
      'label'    => $this->__('Verification Backlink'),
      'title'    => $this->__('Verification Backlink'),
      'required' => true,
      'class'    => 'validate-url',
    ));
    $facebook   = $fieldset->addField('facebook_page', 'text', array(
      'name'     => 'facebook_page',
      'label'    => $this->__('Facebook Page'),
      'title'    => $this->__('Facebook Page'),
      'required' => false,
      'class'    => 'validate-url input-text'
    ));
    $twitter    = $fieldset->addField('twitter_page', 'text', array(
      'name'     => 'twitter_page',
      'label'    => $this->__('Twitter Page'),
      'title'    => $this->__('Twitter Page'),
      'required' => false,
      'class'    => 'validate-url input-text'
    ));
    $googleplus = $fieldset->addField('google_plus_page', 'text', array(
      'name'     => 'google_plus_page',
      'label'    => $this->__('Google+ Page'),
      'title'    => $this->__('Google+ Page'),
      'required' => false,
      'class'    => 'validate-url input-text'
    ));
    $image      = $fieldset->addField('listing_image', 'image', array(
      'name'     => 'listing_image',
      'label'    => $this->__('Image'),
      'title'    => $this->__('Image'),
      'required' => false
    ));

    $lastElement->setAfterElementHtml(
      "<script>
		function checkMaxLength(Object, MaxLen)
		{
			if (Object.value.length > MaxLen-1) {
				Object.value = Object.value.substr(0, MaxLen);
			}
			return 1;
		}	 
	 </script>"
    );

    Mage::dispatchEvent('businessdirectory_directory_listing_main_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);
    $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                                       ->addFieldMap($featured->getHtmlId(), $featured->getName())
                                       ->addFieldMap($facebook->getHtmlId(), $facebook->getName())
                                       ->addFieldMap($twitter->getHtmlId(), $twitter->getName())
                                       ->addFieldMap($googleplus->getHtmlId(), $googleplus->getName())
                                       ->addFieldMap($image->getHtmlId(), $image->getName())
                                       ->addFieldMap($backlink->getHtmlId(), $backlink->getName())
                                       ->addFieldDependence($facebook->getName(), $featured->getName(), 1)
                                       ->addFieldDependence($twitter->getName(), $featured->getName(), 1)
                                       ->addFieldDependence($googleplus->getName(), $featured->getName(), 1)
                                       ->addFieldDependence($image->getName(), $featured->getName(), 1)
                                       ->addFieldDependence($backlink->getName(), $featured->getName(), 1)
    );
    return parent::_prepareForm();
  }

}