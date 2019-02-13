<?php
/**
 * Config.php
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
class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Config extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
    $model = Mage::registry('directory_data');

    $form = new Varien_Data_Form(array(
                                   'id'      => 'config_form',
                                   'action'  => $this->getUrl('*/*/save', array('directory_id' => $model->getDirectoryId())),
                                   'method'  => 'post',
                                   'enctype' => 'multipart/form-data',
                                 )
    );

    $fieldset = $form->addFieldset('location_config', array('legend' => $this->__('Maps & Search Settings')));
    $fieldset->addField('search_name_placeholder', 'text', array(
      'name'               => 'search_name_placeholder',
      'label'              => $this->__('Placeholder Text for Name'),
      'title'              => $this->__('Placeholder Text for Name'),
      'required'           => false,
      'after_element_html' => '<p class="note">Set the placeholder text for the "name" search box on the frontend directory page.</p>'
    ));
    $fieldset->addField('search_location_placeholder', 'text', array(
      'name'               => 'search_location_placeholder',
      'label'              => $this->__('Placeholder Text for Location'),
      'title'              => $this->__('Placeholder Text for Location'),
      'required'           => false,
      'after_element_html' => '<p class="note">Set the placeholder text for the "location" search box on the frontend directory page.</p>'
    ));
    $fieldset->addField('distance_units', 'select', array(
      'name'     => 'distance_units',
      'label'    => $this->__('Distance Units'),
      'title'    => $this->__('Distance Units'),
      'required' => true,
      'options'  => array('miles' => 'Miles', 'kilometers' => 'Kilometers'),
    ));
    $displayMap = $fieldset->addField('show_map', 'select', array(
      'name'     => 'show_map',
      'label'    => $this->__('Display Map'),
      'title'    => $this->__('Display Map'),
      'required' => true,
      'options'  => array(0 => 'No', 1 => 'Yes'),
    ));
    $mapWidth   = $fieldset->addField('map_width', 'text', array(
      'name'     => 'map_width',
      'label'    => $this->__('Map Width(px)'),
      'title'    => $this->__('Map Width(px)'),
      'required' => true,
      'class'    => 'validate-greater-than-zero validate-digits'
    ));
    $mapHeight  = $fieldset->addField('map_height', 'text', array(
      'name'     => 'map_height',
      'label'    => $this->__('Map Height(px)'),
      'title'    => $this->__('Map Height(px)'),
      'required' => true,
      'class'    => 'validate-greater-than-zero validate-digits'
    ));

    $fieldset        = $form->addFieldset('featured_config', array('legend' => $this->__('Featured Listing Settings')));
    $displayFeatured = $fieldset->addField('display_featured_listings', 'select', array(
      'name'     => 'display_featured_listings',
      'label'    => $this->__('Show Featured Listings Block'),
      'title'    => $this->__('Show Featured Listings Block'),
      'required' => true,
      'options'  => array(0 => 'No', 1 => 'Yes'),
    ));
    $featuredCount   = $fieldset->addField('featured_listing_display_count', 'text', array(
      'name'               => 'featured_listing_display_count',
      'label'              => $this->__('Number of Featured Listings to Display'),
      'title'              => $this->__('Number of Featured Listings to Display'),
      'required'           => true,
      'class'              => 'validate-greater-than-zero validate-digits',
      'after_element_html' => '<p class="note">You can set as many listings to "Featured" as you want. The value that you enter above will limit the number of featured listings that show in the block at any one time. The featured listings will display in random order so they all get a chance to be shown. However, if a user conducts a search by location, the featured listings will be display in ascending order by distance.</p>'
    ));
    $featuredButton  = $fieldset->addField('featured_listing_button_text', 'text', array(
      'name'               => 'featured_listing_button_text',
      'label'              => $this->__('Featured Listing Button Text'),
      'title'              => $this->__('Featured Listing Button Text'),
      'required'           => true,
      'class'              => 'validate-text',
      'after_element_html' => '<p class="note">Ex. "Featured Listing!"</p>'
    ));

    $fieldset = $form->addFieldset('other_config', array('legend' => $this->__('Other Directory Settings')));
    $fieldset->addField('is_us_only', 'select', array(
      'name'               => 'is_us_only',
      'label'              => $this->__('United States Only'),
      'title'              => $this->__('United States Only'),
      'required'           => true,
      'options'            => array(0 => 'No', 1 => 'Yes'),
      'after_element_html' => '<p class="note">For US based directories, this will activate helper functions that format things like phone numbers, fax numbers and more automatically to save you time in doing it manually.</p>'
    ));
    $fieldset->addField('show_country', 'select', array(
      'name'     => 'show_country',
      'label'    => $this->__('Show Country In Address'),
      'title'    => $this->__('Show Country In Address'),
      'required' => true,
      'options'  => array(0 => 'No', 1 => 'Yes')
    ));
    $fieldset->addField('can_claim_profile', 'select', array(
      'name'     => 'can_claim_profile',
      'label'    => $this->__('Allow Users to Claim Profiles'),
      'title'    => $this->__('Allow Users to Claim Profiles'),
      'required' => false,
      'options'  => array(0 => 'No', 1 => 'Yes')
    ));
    $fieldset->addField('can_submit_new_listing', 'select', array(
      'name'               => 'can_submit_new_listing',
      'label'              => $this->__('Allow Users to Submit New Listings'),
      'title'              => $this->__('Allow Users to Submit New Listings'),
      'required'           => false,
      'options'            => array(0 => 'No', 1 => 'Yes'),
      'after_element_html' => '<p class="note">Allows users create and submit new listings. You would still need to review and approve the listings before they go live on the directory.</p>'
    ));
    $fieldset->addField('append_location_to_url', 'select', array(
      'name'               => 'append_location_to_url',
      'label'              => $this->__('Append Location to URL'),
      'title'              => $this->__('Append Location to URL'),
      'required'           => false,
      'options'            => array(0 => 'No', 1 => 'Yes(recommended)'),
      'after_element_html' => '<p class="note">When allowing the system to automatically convert the business\'s name into the URL for the profile, you can allow the system to append location information to the url like this: \'shawns-sneaker-shop-chicago-il\'</p>'
    ));

    Mage::dispatchEvent('businessdirectory_directory_config_form_prepare', array('form' => $form));
    $form->setValues($model->getData());
    $this->setForm($form);

    $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                                       ->addFieldMap($displayFeatured->getHtmlId(), $displayFeatured->getName())
                                       ->addFieldMap($featuredCount->getHtmlId(), $featuredCount->getName())
                                       ->addFieldMap($displayMap->getHtmlId(), $displayMap->getName())
                                       ->addFieldMap($mapWidth->getHtmlId(), $mapWidth->getName())
                                       ->addFieldMap($mapHeight->getHtmlId(), $mapHeight->getName())
                                       ->addFieldDependence($featuredCount->getName(), $displayFeatured->getName(), 1)
                                       ->addFieldDependence($mapWidth->getName(), $displayMap->getName(), 1)
                                       ->addFieldDependence($mapHeight->getName(), $displayMap->getName(), 1)
    );

    return parent::_prepareForm();
  }
}