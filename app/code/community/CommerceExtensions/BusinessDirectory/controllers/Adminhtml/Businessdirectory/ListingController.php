<?php

class CommerceExtensions_BusinessDirectory_Adminhtml_Businessdirectory_ListingController extends Mage_Adminhtml_Controller_action
{
  protected function _initAction()
  {
    $this->loadLayout()
         ->_setActiveMenu('cms');
    return $this;
  }

  public function newAction()
  {
    $this->_forward('edit');
  }

  public function editAction()
  {
    $id    = $this->getRequest()->getParam('listing_id');
    $model = Mage::getModel('businessdirectory/directory_listing');

    if($id) {
      $model->load($id);
      if(!$model->getListingId()) {
        $this->_getSession()->addError($this->__('This listing entry no longer exists.'));
        $this->_redirect('*/businessdirectory/edit', array('_current' => true));
        return;
      }
    }

    $data = $this->_getSession()->getFormData(true);

    if(!empty($data)) {
      $model->setData($data);
    }

    // we get the collection model so that the other tables are joined to the model
    $model = $model->getCollection()
                   ->addFieldToFilter('listing_id', $model->getListingId())
                   ->getFirstItem();
    Mage::register('listing_data', $model);

    $this->_initAction()
         ->_title($model->getListingId() ? $model->getListingName() : $this->__('New Listing'))
         ->_addBreadcrumb(
           $id ? $this->__('Edit Listng')
             : $this->__('New Listng'),
           $id ? $this->__('Edit Listng')
             : $this->__('New Listng')
         );

    $this->_addContent($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit'))
         ->_addContent($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_customer_grid'))
         ->_addLeft($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_tabs'));
    $this->renderLayout();
  }

  public function saveAction()
  {
    if($data = $this->getRequest()->getParams()) {

      $model       = Mage::getSingleton('businessdirectory/directory_listing');
      $directoryId = $this->getRequest()->getParam('directory_id');

      if($listingId = $this->getRequest()->getParam('listing_id')) {
        $model->load($listingId);
      }

      $imageFieldExists = array_key_exists('listing_image', $data) ? true : false;

      // add uploaded image to the data array
      $imageModel = Mage::getModel('businessdirectory/image');
      if(!empty($_FILES)
         && $_FILES['listing_image']['name']
         && !$imageModel->checkFileType($_FILES['listing_image']['name'])
      ) {

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

      if(!$data['identifier']) {
        $directory          = Mage::getModel('businessdirectory/directory')->load($directoryId);
        $urlModel           = Mage::getModel('businessdirectory/directory_listing_url');
        $data['identifier'] = $urlModel->createUrl($data, $directory);
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

      Mage::dispatchEvent('businessdirectory_controller_listing_save_prepare', array('listing' => $model, 'request' => $this->getRequest()));

      try {

        $listingId = $model->save()->getId();

        $this->_getSession()->addSuccess($this->__('The listing has been saved.'));
        $this->_getSession()->setFormData(false);

        if($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('_current' => true, 'listing_id' => $listingId));
          return;
        }
        $this->_redirect('*/businessdirectory/edit', array('directory_id' => $directoryId, 'active_tab' => 'directory_listings'));
        return;

      } catch(Mage_Core_Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
        $this->_getSession()->addException($e, $this->__('An error occurred while saving the listing.'));
      }

      $this->_getSession()->setFormData($data);
      $this->_redirect('*/*/edit', array('listing_id' => $listingId));
      return;
    }
  }

  public function deleteAction()
  {
    $listingId   = $this->getRequest()->getParam('listing_id');
    $directoryId = $this->getRequest()->getParam('directory_id');
    if(!$listingId) {
      $this->_getSession()->addError($this->__('This listing no longer exists.'));
    } else {
      try {
        $listing = Mage::getSingleton('businessdirectory/directory_listing')->load($listingId);
        Mage::dispatchEvent('businessdirectory_controller_directory_listing_delete', array('listing' => $listing));
        $listing->delete();

        $this->_getSession()->addSuccess('The listing has been deleted.');
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/businessdirectory/edit', array('directory_id' => $directoryId, 'active_tab' => 'directory_listings'));
    return;
  }

  public function massDeleteAction()
  {
    $listingIds = $this->getRequest()->getParam('listing_id');
    $deleted    = 0;
    $notDeleted = 0;
    if(!is_array($listingIds)) {
      $this->_getSession()->addError($this->__('Please select listings to delete.'));
    } else {
      if(!empty($listingIds)) {
        try {
          foreach($listingIds as $listingId) {
            $listing = Mage::getSingleton('businessdirectory/directory_listing')->load($listingId);
            Mage::dispatchEvent('businessdirectory_controller_directory_listing_delete', array('listing' => $listing));
            $listing->delete();
            $deleted++;
          }
        } catch(Exception $e) {
          $notDeleted++;
          $this->_getSession()->addError($e->getMessage());
        }
        if($deleted == 1) {
          $message = $this->__('%d listing has been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        } elseif($deleted > 1) {
          $message = $this->__('%d listings have been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        }

        if($notDeleted == 1) {
          $message = $this->__('%d listing was not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        } elseif($notDeleted > 1) {
          $message = $this->__('%d listings were not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        }
      }
    }
    $this->_redirect('*/businessdirectory/edit', array('_current' => true));
    return;
  }

  public function massGeocodeAction()
  {
    $listingIds  = $this->getRequest()->getParam('listing_id');
    $geocoded    = 0;
    $notGeocoded = 0;
    if(!is_array($listingIds)) {
      $this->_getSession()->addError($this->__('Please select listings to geocode.'));
    } else {
      if(!empty($listingIds)) {
        try {
          foreach($listingIds as $listingId) {
            $listing = Mage::getSingleton('businessdirectory/directory_listing')->load($listingId);
            $geocode = Mage::getModel('businessdirectory/api_geocode')->getCoordinates($listing->toArray());
            if(!empty($geocode)) {
              $listing->setLatitude($geocode['Latitude']);
              $listing->setLongitude($geocode['Longitude']);
              $listing->save();
              $geocoded++;
            } else {
              $notGeocoded++;
            }
          }
        } catch(Exception $e) {
          $notGeocoded++;
          $this->_getSession()->addError($e->getMessage());
        }

        if($geocoded == 1) {
          $message = $this->__('%d listing has been geocoded.', $geocoded);
          $this->_getSession()->addSuccess($message);
        } elseif($geocoded > 1) {
          $message = $this->__('%d listings have been geocoded.', $geocoded);
          $this->_getSession()->addSuccess($message);
        }

        if($notGeocoded == 1) {
          $message = $this->__('%d listing was not geocoded.', $notGeocoded);
          $this->_getSession()->addError($message);
        } elseif($notGeocoded > 1) {
          $message = $this->__('%d listings were not geocoded. Either the address/location info wasn\'t recognized by the API or there was some other error.', $notGeocoded);
          $this->_getSession()->addError($message);
        }
      }
    }
    $this->_redirect('*/businessdirectory/edit', array('_current' => true));
    return;
  }

  public function massStatusAction()
  {
    $listingIds  = $this->getRequest()->getParam('listing_id');
    $directoryId = $this->getRequest()->getParam('directory_id');
    $status      = $this->getRequest()->getParam('status');
    if(!is_array($listingIds)) {
      $this->_getSession()->addError($this->__('Please select listings to update.'));
    } else {
      if(!empty($listingIds)) {
        try {
          foreach($listingIds as $listingId) {
            $listing = Mage::getSingleton('businessdirectory/directory_listing')->load($listingId);
            Mage::dispatchEvent('businessdirectory_controller_directory_listing_status_update', array('listing' => $listing));
            $listing->setIsActive($status);
            $listing->save();
          }
          $count       = count($listingIds);
          $statusValue = $status == 1 ? 'enabled.' : 'disabled.';
          if($count == 1) {
            $message = $this->__('%d listing has been ' . $statusValue, $count);
          } else {
            $message = $this->__('%d listings have been ' . $statusValue, $count);
          }
          $this->_getSession()->addSuccess($message);
        } catch(Exception $e) {
          $this->_getSession()->addError($e->getMessage());
        }
      }
    }
    $this->_redirect('*/businessdirectory/edit', array('directory_id' => $directoryId, 'active_tab' => 'directory_listings'));
    return;
  }

  public function exportCsvAction()
  {
    $tablename  = Mage::getSingleton('core/resource')->getTableName('businessdirectory/directory_listing');
    $collection = Mage::getModel('businessdirectory/directory_listing')->getCollection()
                      ->addFieldToFilter('directory_id', $this->getRequest()->getParam('directory_id'));
    $content    = Mage::helper('cecore/csv')->createCsv($tablename, $collection);
    $fileName   = 'businessdirectory.csv';
    $this->_prepareDownloadResponse($fileName, $content);
  }

  public function gridAction()
  {
    $this->loadLayout();
    $this->getResponse()->setBody($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_container_grid')->toHtml());
  }

  public function customerGridAction()
  {
    $this->loadLayout();
    $this->getResponse()->setBody($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tab_listing_edit_customer_grid')->toHtml());
  }

  protected function _isAllowed()
  {
    return true;
  }
}