<?php

class CommerceExtensions_BusinessDirectory_Adminhtml_Businessdirectory_PendingController extends Mage_Adminhtml_Controller_action
{
  protected function _initAction()
  {
    $this->loadLayout()
         ->_setActiveMenu('cms');
    $this->_title($this->__('Business Directories'))->_title($this->__('Pending Submissions'));
    return $this;
  }

  public function indexAction()
  {
    $this->_initAction();
    $this->renderLayout();
  }

  public function editAction()
  {
    $id    = $this->getRequest()->getParam('id');
    $model = Mage::getModel('businessdirectory/directory_listing_submit');

    if($id) {
      $model->load($id);
      if(!$model->getId()) {
        $this->_getSession()->addError($this->__('This directory submission no longer exists.'));
        $this->_redirect('*/*/');
        return;
      }
    }

    $data = $this->_getSession()->getFormData(true);
    if(!empty($data)) {
      $model->setData($data);
    }

    // we register using listing data here so that we can simple extend
    // CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory_Edit_Tab_Listing_Edit_Tab_Main
    // instead of building a whole new form
    // we also get the collection model so that the other tables are joined to the model
    $model = $model->getCollection()
                   ->addFieldToFilter('id', $model->getId())
                   ->getFirstItem();
    Mage::register('listing_data', $model);

    $this->_initAction()
         ->_title($this->__($model->getListingName()));
    $this->_addContent($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_pending_edit'))
         ->_addContent($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_customer_grid'));
    $this->renderLayout();

  }

  public function saveAction()
  {
    if($data = $this->getRequest()->getParams()) {

      $model = Mage::getSingleton('businessdirectory/directory_listing_submit');

      if($id = $this->getRequest()->getParam('id')) {
        $model->load($id);
      }

      if(!$data['identifier']) {
        $directory          = Mage::getModel('businessdirectory/directory')->load($model->getDirectoryId());
        $urlModel           = Mage::getModel('businessdirectory/directory_listing_url');
        $data['identifier'] = $urlModel->createUrl($data, $directory);
      } else {
        // if csv does contain url key, run formatUrlKey function on it to ensure that it is acceptable for import
        $urlKey             = str_ireplace("'s", "s", $data['identifier']);
        $data['identifier'] = Mage::getModel('catalog/product_url')->formatUrlKey($urlKey);
      }

      $imageFieldExists = array_key_exists('listing_image', $data) ? true : false;

      // add uploaded image to the data array
      $imageModel = Mage::getModel('businessdirectory/image');
      if(!empty($_FILES) && $_FILES['listing_image']['name'] && !$imageModel->checkFileType($_FILES['listing_image']['name'])) {
        $this->_getSession()
             ->addError($this->__('Unable to save. Image files must be one of the following types: %s', implode(', ', CommerceExtensions_BusinessDirectory_Model_Image::$ALLOWED_FILE_TYPES)));
        $this->_redirect('*/*/edit', array('listing_id' => $listingId));
        return;
      } elseif(!empty($_FILES)
               && $_FILES['listing_image']['name']
               && $imageModel->checkFileType($_FILES['listing_image']['name'])
      ) {
        $files = $imageModel->uploadFiles($_FILES);
        if($files && is_array($files)) {
          for($i = 0; $i < count($files); $i++) {
            if($files[$i]) {
              $data['listing_image'] = str_replace('\\', '/', $files[$i]['url']);
            }
          }
        }
      } elseif($imageFieldExists && array_key_exists('delete', $data['listing_image'])) {
        if($data['listing_image']['delete']) {
          $data['listing_image'] = null;
        }
      } elseif($imageFieldExists) {
        $data['listing_image'] = $data['listing_image']['value'];
      } else {
        $data['listing_image'] = null;
      }

      if(array_key_exists('geocode_on_save', $data) && $data['geocode_on_save']) {
        $geocode = Mage::getModel('businessdirectory/api_geocode')->getCoordinates($data);
        if(!empty($geocode)) {
          $data['latitude']  = $geocode['Latitude'];
          $data['longitude'] = $geocode['Longitude'];
        }
      }

      $model->_hasDataChanges = true;
      $model->addData($data);

      Mage::dispatchEvent('businessdirectory_controller_pending_save_prepare', array('submission' => $model, 'request' => $this->getRequest()));

      try {
        $id = $model->save()->getId();

        $this->_getSession()->addSuccess($this->__('The directory submission has been saved. However, it is still pending your approval.'));
        $this->_getSession()->setFormData(false);

        if($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('_current' => true, 'id' => $id));
          return;
        }
        $this->_redirect('*/*');
        return;

      } catch(Mage_Core_Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
        $this->_getSession()->addException($e, $this->__('An error occurred while saving the directory submission.'));
      }

      $this->_getSession()->setFormData($data);
      $this->_redirect('*/*/edit', array('_current' => true, 'id' => $id));
      return;
    }
  }

  public function approveAction()
  {
    $model = Mage::getModel('businessdirectory/directory_listing_submit');

    if($id = $this->getRequest()->getParam('id')) {

      $model->load($id);
      $model->_hasDataChanges = true;

      if(Mage::getStoreConfig('businessdirectory/pending/geocode_on_approve')) {
        $geocode = Mage::getModel('businessdirectory/api_geocode')->getCoordinates($model->toArray());
        if(!empty($geocode)) {
          $model->setLatitude($geocode['Latitude']);
          $model->setLongitude($geocode['Longitude']);
        }
      }

      if(!$model->getId()) {
        $this->_getSession()->addError($e, $this->__('An error occurred and the listing submission was not approved.'));
        $this->_redirect('*/*/edit', array('_current' => true));
        return;
      }

      $directory = Mage::getModel('businessdirectory/directory')->load($model->getDirectoryId());

      if(!$model->getIdentifier()) {
        // if csv does not contain url key, convert listing_name into url key
        $urlKey     = str_ireplace("'s", "s", $model->getListingName());
        $identifier = Mage::getModel('catalog/product_url')->formatUrlKey($urlKey);
        $model->setIdentifier($identifier);
      }

      $listingModel = Mage::getSingleton('businessdirectory/directory_listing');

      if($model->getAction() == 'new') {

        $listingId = $listingModel->checkIdentifier($model->getIdentifier(), $model->getDirectoryId());
        if($listingId) {
          $this->_getSession()->addError($this->__('A listing with this URL key already exists in the directory. The URL key must be unique.'));
          $this->_redirect('*/*/edit', array('_current' => true));
          return;
        }

        $listingModel->addData($model->getData());

        try {

          $newListingId = $listingModel->save()->getId();
          if($newListingId) {
            $model->load($id);
            $model->delete();
          }
          $newListing = $listingModel->load($newListingId);
          if($newListing->getIsFeatured()) {
            Mage::dispatchEvent('businessdirectory_profile_new_featured_approve_after', array('listing' => $newListing, 'action' => $model->getAction()));
            Mage::dispatchEvent('businessdirectory_profile_new_approve_after', array('listing' => $newListing, 'action' => $model->getAction()));
          } else {
            Mage::dispatchEvent('businessdirectory_profile_new_standard_approve_after', array('listing' => $newListing, 'action' => $model->getAction()));
            Mage::dispatchEvent('businessdirectory_profile_new_approve_after', array('listing' => $newListing, 'action' => $model->getAction()));
          }
          $this->_getSession()->addSuccess($this->__('The listing has been approved and is now live in the %s directory.', $directory->getTitle()));
          $this->_redirect('*/*');
          return;

        } catch(Mage_Core_Exception $e) {
          $this->_getSession()->addError($e->getMessage());
          $this->_redirect('*/*/edit', array('_current' => true));
          return;
        } catch(Exception $e) {
          $this->_getSession()->addError($this->__('An error occurred and the listing submission was not approved.'));
          $this->_redirect('*/*/edit', array('_current' => true));
          return;
        }

      } elseif(in_array($model->getAction(), array('claim', 'update'))) {

        $listingModel->load($model->getListingId());
        $wasFeaturedBefore = $listingModel->getIsFeatured(); // we get this data to determine if the update resulted in an upgrade from standard listing to featured listing
        $listingModel->addData($model->getData());

        try {

          $listingId = $listingModel->save()->getListingId();
          $model->load($id);
          $model->delete();

          $listing = $listingModel->load($listingId);

          $isFeaturedAfter = $listing->getIsFeatured();

          if($model->getAction() == 'claim') {
            if($listing->getIsFeatured()) {
              Mage::dispatchEvent('businessdirectory_profile_claim_featured_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
              Mage::dispatchEvent('businessdirectory_profile_claim_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
            } else {
              Mage::dispatchEvent('businessdirectory_profile_claim_standard_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
              Mage::dispatchEvent('businessdirectory_profile_claim_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
            }
          }
          if($model->getAction() == 'update') {
            if(!$wasFeaturedBefore && $isFeaturedAfter) {
              Mage::dispatchEvent('businessdirectory_profile_update_upgrade_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
              Mage::dispatchEvent('businessdirectory_profile_update_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
            } elseif($listing->getIsFeatured()) {
              Mage::dispatchEvent('businessdirectory_profile_update_featured_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
              Mage::dispatchEvent('businessdirectory_profile_update_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
            } else {
              Mage::dispatchEvent('businessdirectory_profile_update_standard_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
              Mage::dispatchEvent('businessdirectory_profile_update_approve_after', array('listing' => $listing, 'action' => $model->getAction()));
            }
          }

          $this->_getSession()->addSuccess($this->__('The listing changes has been approved and are now live in the %s directory.', $directory->getTitle()));
          $this->_redirect('*/*');
          return;

        } catch(Mage_Core_Exception $e) {
          $this->_getSession()->addError($e->getMessage());
          $this->_redirect('*/*/edit', array('_current' => true));
          return;
        } catch(Exception $e) {
          $this->_getSession()->addError($this->__('An error occurred and the listing changes were not approved.'));
          $this->_redirect('*/*/edit', array('_current' => true));
          return;
        }

      }

    } else {
      $this->_getSession()->addError($this->__('An error occurred and the listing submission was not approved.'));
      $this->_redirect('*/*/edit', array('_current' => true));
      return;
    }
  }

  public function deleteAction()
  {
    $id = $this->getRequest()->getParam('id');
    if(!$id) {
      $this->_getSession()->addError($this->__('This directory submission no longer exists.'));
    } else {
      try {
        $submission = Mage::getSingleton('businessdirectory/directory_listing_submit')->load($id);
        Mage::dispatchEvent('businessdirectory_controller_pending_delete', array('submission' => $submission));
        $submission->delete();

        $this->_getSession()->addSuccess('The directory submission has been deleted.');
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*');
  }

  public function massDeleteAction()
  {
    $ids        = $this->getRequest()->getParam('id');
    $deleted    = 0;
    $notDeleted = 0;
    if(!is_array($ids)) {
      $this->_getSession()->addError($this->__('Please select directories to delete.'));
    } else {
      if(!empty($ids)) {
        try {
          foreach($ids as $id) {
            $submission = Mage::getSingleton('businessdirectory/directory_listing_submit')->load($id);
            Mage::dispatchEvent('businessdirectory_controller_pending_delete', array('submission' => $submission));
            $submission->delete();
            $deleted++;
          }
        } catch(Exception $e) {
          $notDeleted++;
          $this->_getSession()->addError($e->getMessage());
        }

        if($deleted == 1) {
          $message = $this->__('%d submission has been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        } elseif($deleted > 1) {
          $message = $this->__('%d submissions have been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        }

        if($notDeleted == 1) {
          $message = $this->__('%d submission was not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        } elseif($notDeleted > 1) {
          $message = $this->__('%d submissions were not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        }
      }
    }

    $this->_redirect('*/*/index');
    return;
  }

  public function gridAction()
  {
    $this->loadLayout();
    $this->getResponse()->setBody($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_pending_grid')->toHtml());
  }

  protected function _isAllowed()
  {
    return true;
  }
}