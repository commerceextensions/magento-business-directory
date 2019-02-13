<?php

class CommerceExtensions_BusinessDirectory_Model_Email_Observer extends Mage_Core_Model_Abstract
{
  protected $_vars;

  const XML_PATH_ADMIN_EMAIL_ADDRESS = 'businessdirectory/emails/admin_email';

  const XML_PATH_EMAIL_CUST_NEW_SUBMIT_ENABLED  = 'businessdirectory/emails/enable_profile_new_submit_email';
  const XML_PATH_EMAIL_CUST_NEW_SUBMIT_TEMPLATE = 'businessdirectory/emails/profile_new_submit_template';
  const XML_PATH_EMAIL_ADMIN_NEW_SUBMIT_ENABLED = 'businessdirectory/emails/cc_admin_profile_new_submit';

  const XML_PATH_EMAIL_CUST_NEW_APPROVE_ENABLED           = 'businessdirectory/emails/enable_profile_new_approve_email';
  const XML_PATH_EMAIL_CUST_NEW_STANDARD_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_new_standard_approve_template';
  const XML_PATH_EMAIL_CUST_NEW_FEATURED_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_new_featured_approve_template';
  const XML_PATH_EMAIL_ADMIN_NEW_APPROVE_ENABLED          = 'businessdirectory/emails/cc_admin_profile_new_approve';

  const XML_PATH_EMAIL_CUST_CLAIM_SUBMIT_ENABLED  = 'businessdirectory/emails/enable_profile_claim_submit_email';
  const XML_PATH_EMAIL_CUST_CLAIM_SUBMIT_TEMPLATE = 'businessdirectory/emails/profile_claim_submit_template';
  const XML_PATH_EMAIL_ADMIN_CLAIM_SUBMIT_ENABLED = 'businessdirectory/emails/cc_admin_profile_claim_submit';

  const XML_PATH_EMAIL_CUST_CLAIM_APPROVE_ENABLED           = 'businessdirectory/emails/enable_profile_claim_approve_email';
  const XML_PATH_EMAIL_CUST_CLAIM_STANDARD_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_claim_standard_approve_template';
  const XML_PATH_EMAIL_CUST_CLAIM_FEATURED_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_claim_featured_approve_template';
  const XML_PATH_EMAIL_ADMIN_CLAIM_APPROVE_ENABLED          = 'businessdirectory/emails/cc_admin_profile_claim_approve';

  const XML_PATH_EMAIL_CUST_UPDATE_SUBMIT_ENABLED  = 'businessdirectory/emails/enable_profile_update_submit_email';
  const XML_PATH_EMAIL_CUST_UPDATE_SUBMIT_TEMPLATE = 'businessdirectory/emails/profile_update_submit_template';
  const XML_PATH_EMAIL_ADMIN_UPDATE_SUBMIT_ENABLED = 'businessdirectory/emails/cc_admin_profile_update_submit';

  const XML_PATH_EMAIL_CUST_UPDATE_APPROVE_ENABLED           = 'businessdirectory/emails/enable_profile_update_approve_email';
  const XML_PATH_EMAIL_CUST_UPDATE_STANDARD_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_update_standard_approve_template';
  const XML_PATH_EMAIL_CUST_UPDATE_FEATURED_APPROVE_TEMPLATE = 'businessdirectory/emails/profile_update_featured_approve_template';
  const XML_PATH_EMAIL_CUST_UPDATE_UPGRADE_APPROVE_TEMPLATE  = 'businessdirectory/emails/profile_update_upgrade_approve_template';
  const XML_PATH_EMAIL_ADMIN_UPDATE_APPROVE_ENABLED          = 'businessdirectory/emails/cc_admin_profile_update_approve';

  public function sendEmail($observer)
  {
    $storeId = Mage::app()->getStore()->getStoreId();

    $event       = $observer->getEvent();
    $name        = $event->getName();
    $listing     = $event->getListing();
    $emailConfig = $this->getEmailConfig($name);

    if(empty($emailConfig)) return;
    if(empty($listing)) return;
    if(!$emailConfig['canEmailCustomer']) return;
    if(!$emailConfig['template']) return;

    $customerId = $listing->getCustomerId();
    if(!$customerId) return;

    $customer  = Mage::getModel('customer/customer')->load($customerId);
    $directory = Mage::getModel('businessdirectory/directory')->load($listing->getDirectoryId());

    $customerData  = array(
      'customer_id'    => $customerId,
      'customer_email' => $customer['email'],
      'firstname'      => $customer['firstname'],
      'lastname'       => $customer['lastname'],
    );
    $directoryData = array(
      'directory_url'     => $directory->getUrl(),
      'directory_name'    => Mage::helper('businessdirectory/tags')->processCustomTags($directory->getTitle()),
      'directory_heading' => $directory->getContentHeading()
    );

    if($name != 'businessdirectory_profile_new_submit_after') {
      // we dont get the listing url when observer businessdirectory_profile_new_submit_after is fired because it doesn't exist yet
      $listingUrl = Mage::getModel('businessdirectory/directory_listing')->getUrl($listing->getListingId());
      $listing->setListingUrl($listingUrl);
    }

    $data = array_merge($customerData, $listing->toArray(), $directoryData);

    $translate = Mage::getSingleton('core/translate');
    /* @var $translate Mage_Core_Model_Translate */
    $translate->setTranslateInline(false);

    $mailTemplate = Mage::getModel('core/email_template');
    /* @var $mailTemplate Mage_Core_Model_Email_Template */

    $to          = array();
    $to['email'] = $customer['email'];
    $to['name']  = $customer['firstname'];

    $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                 ->sendTransactional(
                   $emailConfig['template'],
                   Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
                   $to['email'],
                   $to['name'],
                   $data
                 );

    if($emailConfig['canCopyAdmin']) {
      $to['email'] = Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_ADDRESS, $storeId);
      $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                   ->sendTransactional(
                     $emailConfig['template'],
                     Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId),
                     $to['email'],
                     $to['name'],
                     $data
                   );
    }
//	echo $mailTemplate->getProcessedTemplate();die;
    $translate->setTranslateInline(true);
    return $this;

  }

  public function getEmailConfig($observerName)
  {
    $emailConfig = array();
    $storeId     = Mage::app()->getStore()->getStoreId();
    if($observerName == 'businessdirectory_profile_new_submit_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_SUBMIT_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_SUBMIT_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_NEW_SUBMIT_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_new_standard_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_STANDARD_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_NEW_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_new_featured_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_FEATURED_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_NEW_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_NEW_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_claim_submit_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_SUBMIT_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_SUBMIT_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_CLAIM_SUBMIT_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_claim_standard_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_STANDARD_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_CLAIM_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_claim_featured_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_FEATURED_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_CLAIM_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_CLAIM_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_update_submit_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_SUBMIT_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_SUBMIT_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_UPDATE_SUBMIT_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_update_standard_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_STANDARD_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_UPDATE_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_update_featured_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_FEATURED_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_UPDATE_APPROVE_ENABLED, $storeId);
    }
    if($observerName == 'businessdirectory_profile_update_upgrade_approve_after') {
      $emailConfig['template']         = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_UPGRADE_APPROVE_TEMPLATE, $storeId);
      $emailConfig['canEmailCustomer'] = Mage::getStoreConfig(self::XML_PATH_EMAIL_CUST_UPDATE_APPROVE_ENABLED, $storeId);
      $emailConfig['canCopyAdmin']     = Mage::getStoreConfig(self::XML_PATH_EMAIL_ADMIN_UPDATE_APPROVE_ENABLED, $storeId);
    }
    return $emailConfig;

  }
}