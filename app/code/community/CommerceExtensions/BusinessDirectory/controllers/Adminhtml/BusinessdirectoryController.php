<?php

class CommerceExtensions_BusinessDirectory_Adminhtml_BusinessdirectoryController extends Mage_Adminhtml_Controller_action
{
  protected function _initAction()
  {
    $this->loadLayout()
         ->_setActiveMenu('cms');
    $this->_title($this->__('Business Directories'));
    return $this;
  }

  public function indexAction()
  {
    $this->_initAction();
    $this->renderLayout();
  }

  public function newAction()
  {
    $this->_forward('edit');
  }

  public function editAction()
  {
    $id    = $this->getRequest()->getParam('directory_id');
    $model = Mage::getModel('businessdirectory/directory');

    if($id) {
      $model->load($id);
      if(!$model->getId()) {
        $this->_getSession()->addError($this->__('This directory no longer exists.'));
        $this->_redirect('*/*/');
        return;
      }
    }

    $data = $this->_getSession()->getFormData(true);
    if(!empty($data)) {
      $model->setData($data);
    }

    // 4. Register model to use later in blocks
    Mage::register('directory_data', $model);

    // 5. Build edit form
    $this->_initAction()
         ->_title($model->getId() ? $model->getTitle() : $this->__('New Directory'))
         ->_addBreadcrumb(
           $id ? $this->__('Edit Directory')
             : $this->__('New Directory'),
           $id ? $this->__('Edit Directory')
             : $this->__('New Directory'));

    $this->_addContent($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit'))
         ->_addLeft($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_edit_tabs'));

    $this->renderLayout();

  }

  public function saveAction()
  {
    if($data = $this->getRequest()->getPost()) {
      $data = $this->_filterPostData($data);

      $this->_processStaticFilterContent($data);
      $this->_processStaticFilterData($data);

      if($data['identifier']) {
        $data['identifier'] = rtrim($data['identifier'], '/');
      }

      $model = Mage::getModel('businessdirectory/directory');
      if($id = $this->getRequest()->getParam('directory_id')) {
        $model->load($id);
      }
      $model->setData($data);

      Mage::dispatchEvent('businessdirectory_controller_directory_save_prepare', array('directory' => $model, 'request' => $this->getRequest()));

      if(!$this->_validatePostData($data)) {
        $this->_redirect('*/*/edit', array('directory_id' => $model->getDirectoryId(), '_current' => true));
        return;
      }

      try {

        // begin process uploaded csv
        if(isset($_FILES['listing_csv'])) {

          $csv = $_FILES['listing_csv'];

          $csvHelper = Mage::helper('cecore/csv');

          $filetype = $csvHelper->getExtension($csv);
          if($filetype != 'csv') {
            $this->_getSession()->addError('Only CSV files can be uploaded. No uploads or data changes have been saved.');
            $this->_redirect('*/*/edit', array('directory_id' => $model->getDirectoryId(), '_current' => true));
            return;
          }

          $errors = $csvHelper->getErrors($csv);
          if((int)$errors > 0) {
            $this->_getSession()->addError('Your CSV file contains errors. No uploads or data changes have been saved.');
            $this->_redirect('*/*/edit', array('directory_id' => $model->getDirectoryId(), '_current' => true));
            return;
          }

          $listingData = $csvHelper->getDataColumnsAsKey($csv);

          $listingModel = Mage::getModel('businessdirectory/directory_listing');
          if($model->getDirectoryId()) {
            $requiredColumns = $listingModel->REQUIRED_CSV_COLUMNS;
            $skipped         = 0;
            $imported        = 0;

            $urlModel = Mage::getModel('businessdirectory/directory_listing_url');

            foreach($listingData as $listing) {

              // remove empty fields from csv row
              $listing = array_filter($listing, 'strlen');

              // if user's csv contains listing_id field, remove it as it will be automatically generated
              if(array_key_exists('listing_id', $listing)) {
                unset($listing['listing_id']);
              }

              // if user's csv contains directory_id field, remove it as we will use the id of the current directory
              if(array_key_exists('directory_id', $listing)) {
                unset($listing['directory_id']);
              }

              // must have directory id
              $listing['directory_id'] = $model->getDirectoryId();

              $rowColumns = array_keys($listing);

              $hasAllRequiredData = $csvHelper->hasRequiredColumns($rowColumns, $requiredColumns);
              if($hasAllRequiredData) {

                if(!$listing['identifier']) {
                  $listing['identifier'] = $urlModel->createUrl($listing, $model);
                } else {
                  // if csv does contain url key, run formatUrlKey function on it to ensure that it is acceptable for import
                  $urlKey                = str_ireplace("'s", "s", $listing['identifier']);
                  $listing['identifier'] = Mage::getModel('catalog/product_url')->formatUrlKey($urlKey);
                }

                $imageModel = Mage::getModel('businessdirectory/image');
                if(array_key_exists('listing_image', $listing)) {
                  if($imageModel->checkFileType($listing['listing_image'])) {
                    $listing['listing_image'] = $imageModel->importImage(false, $listing['listing_image'], true);
                  } else {
                    $this->_getSession()
                         ->addError($this->__('%s was not uploaded because it is the wrong filetpye. Image files must be one of the following types: ' . implode(', ', CommerceExtensions_BusinessDirectory_Model_Image::$ALLOWED_FILE_TYPES), $listing['listing_image']));
                    unset($listing['listing_image']);
                  }
                }

                if($data['geocode']) {
                  $geocode = Mage::getModel('businessdirectory/api_geocode')->getCoordinates($listing);
                  if(!empty($geocode)) {
                    $listing['latitude']  = $geocode['Latitude'];
                    $listing['longitude'] = $geocode['Longitude'];
                  }
                }

                Mage::dispatchEvent('businessdirectory_controller_listing_csv_row_import_prepare', array('csv_row' => $listing));
                $listingModel->setData($listing);

                if(!Mage::getResourceModel('businessdirectory/directory_listing')->getIsUniqueListing($listingModel)) {
                  $this->_getSession()
                       ->addError($this->__('%s was skipped because the URL is not unique. Please note that if you did not include the \'identifier\' column in your CSV, that the system converted the \'listing_name\' field into a URL and it was not unique.', $listingModel->getListingName()));
                  continue;
                }

                $listingModel->save();
                $imported++;
              } else {
                $skipped++;
              }
            }
          } else {
            $this->_getSession()->addError('There was an internal problem determining the destination directory of your upload. No uploads or data changes have been saved.');
            $this->_redirect('*/*/edit', array('directory_id' => $model->getDirectoryId(), '_current' => true));
            return;
          }

          if($skipped > 0) {
            $this->_getSession()
                 ->addError($this->__($skipped . ' rows in your CSV were not imported. These rows were missing required data. Please remember that for each row in your CSV, that data is required for the ' . implode(', ', $requiredColumns) . ' columns. Do not reimport the other rows as they have already been added to the database.'));
          }
          if($imported > 0) {
            $this->_getSession()->addSuccess($this->__($imported . ' listings have been imported and added to the directory.'));
          }
        }
        // end process uploaded csv

        $model->save();
        $this->_getSession()->addSuccess($this->__('The directory has been saved.'));
        // clear previously saved data from session
        $this->_getSession()->setFormData(false);
        if($this->getRequest()->getParam('back')) {
          $this->_redirect('*/*/edit', array('directory_id' => $model->getDirectoryId(), '_current' => true));
          return;
        }

        $this->_redirect('*/*/');
        return;

      } catch(Mage_Core_Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      } catch(Exception $e) {
        $this->_getSession()->addException($e, $this->__('An error occurred while saving the directory.'));
      }

      $this->_getSession()->setFormData($data);
      $this->_redirect('*/*/edit', array('directory_id' => $this->getRequest()->getParam('directory_id')));
      return;
    }
    $this->_redirect('*/*/');
  }

  public function deleteAction()
  {
    $directoryId = $this->getRequest()->getParam('directory_id');
    if(!$directoryId) {
      $this->_getSession()->addError($this->__('This directory no longer exists.'));
    } else {
      try {
        $directory = Mage::getSingleton('businessdirectory/directory')->load($directoryId);
        Mage::dispatchEvent('businessdirectory_controller_directory_delete', array('directory' => $directory));
        $directory->delete();

        $this->_getSession()->addSuccess('The directory has been deleted.');
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
      }
    }
    $this->_redirect('*/*/index');
  }

  protected function _processStaticFilterData($data)
  {
    $model = Mage::getModel('businessdirectory/directory_staticfilter');

    if(array_key_exists('staticfilter', $data)) {
      // assemble filter data array
      $filterArray = $data['staticfilter'];
      foreach($filterArray as $fieldname => $dataArray) {
        $itemid = $model->getCollection()
                        ->addFieldToFilter('directory_id', $dataArray['directory_id'])
                        ->addFieldToFilter('filter_field', $fieldname)
                        ->getFirstItem()
                        ->getId();

        $dataArray['is_active']    = array_key_exists('is_active', $dataArray) ? $dataArray['is_active'] : false;
        $dataArray['filter_field'] = $fieldname;

        if($itemid) {
          $model->load($itemid);
          $model->addData($dataArray);
          $model->save();
        } else {
          $model->setData($dataArray);
          $model->save();
        }
      }
    }
  }

  protected function _processStaticFilterContent($data)
  {
    $model = Mage::getModel('businessdirectory/directory_staticfilter_content');

    if(array_key_exists('filtertext', $data)) {
      $filterArray = $data['filtertext'];
      foreach($filterArray as $fieldname => $parentArray) {
        foreach($parentArray as $filtervalue => $contentArray) {
          foreach($contentArray as $fieldtype => $content) {

            $id = $model->getCollection()
                        ->addFieldToFilter('filter_field', $fieldname)
                        ->addFieldToFilter('filter_value', $filtervalue)
                        ->addFieldToFilter('field_type', $fieldtype)
                        ->addFieldToFilter('directory_id', $data['directory_id'])
                        ->getFirstItem()
                        ->getId();

            $dataArray['directory_id'] = $data['directory_id'];
            $dataArray['filter_value'] = $filtervalue;
            $dataArray['filter_field'] = $fieldname;
            $dataArray['field_type']   = $fieldtype;
            $dataArray['content']      = $content;

            if($id) {
              $model->load($id);
              $model->addData($dataArray);
              $model->save();
            } else {
              $model->setData($dataArray);
              $model->save();
            }
          }
        }
      }
    }
    return $this;
  }

  protected function _filterPostData($data)
  {
    $data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
    return $data;
  }

  protected function _validatePostData($data)
  {
    $errorNo = true;
    if(!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
      /** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
      $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
      if(!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
        $errorNo = false;
      }
      if(!empty($data['custom_layout_update_xml'])
         && !$validatorCustomLayout->isValid($data['custom_layout_update_xml'])
      ) {
        $errorNo = false;
      }
      foreach($validatorCustomLayout->getMessages() as $message) {
        $this->_getSession()->addError($message);
      }
    }
    return $errorNo;
  }

  public function massDeleteAction()
  {
    $directoryIds = $this->getRequest()->getParam('directory_id');
    $deleted      = 0;
    $notDeleted   = 0;
    if(!is_array($directoryIds)) {
      $this->_getSession()->addError($this->__('Please select directories to delete.'));
    } else {
      if(!empty($directoryIds)) {
        try {
          foreach($directoryIds as $directoryId) {
            $directory = Mage::getSingleton('businessdirectory/directory')->load($directoryId);
            Mage::dispatchEvent('businessdirectory_controller_directory_delete', array('directory' => $directory));
            $directory->delete();
            $deleted++;
          }
        } catch(Exception $e) {
          $notDeleted++;
          $this->_getSession()->addError($e->getMessage());
        }

        if($deleted == 1) {
          $message = $this->__('%d directory has been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        } elseif($deleted > 1) {
          $message = $this->__('%d directories have been deleted.', $deleted);
          $this->_getSession()->addSuccess($message);
        }

        if($notDeleted == 1) {
          $message = $this->__('%d directory was not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        } elseif($notDeleted > 1) {
          $message = $this->__('%d directories were not deleted.', $notDeleted);
          $this->_getSession()->addError($message);
        }

      }
    }
    $this->_redirect('*/*/index');
    return;
  }

  public function massStatusAction()
  {
    $directoryIds = $this->getRequest()->getParam('directory_id');
    $status       = $this->getRequest()->getParam('status');
    if(!is_array($directoryIds)) {
      $this->_getSession()->addError($this->__('Please select directories to update.'));
    } else {
      if(!empty($directoryIds)) {
        try {
          foreach($directoryIds as $directoryId) {
            $directory = Mage::getSingleton('businessdirectory/directory')->load($directoryId);
            Mage::dispatchEvent('businessdirectory_controller_directory_status_update', array('directory' => $directory));
            $directory->setIsActive($status);
            $directory->save();
          }
          $count       = count($directoryIds);
          $statusValue = $status == 1 ? 'enabled.' : 'disabled.';
          if($count == 1) {
            $message = $this->__('%d directory has been ' . $statusValue, $count);
          } else {
            $message = $this->__('%d directories have been ' . $statusValue, $count);
          }
          $this->_getSession()->addSuccess($message);
        } catch(Exception $e) {
          $this->_getSession()->addError($e->getMessage());
        }
      }
    }
    $this->_redirect('*/*/index');
  }

  public function gridAction()
  {
    $this->loadLayout();
    $this->getResponse()->setBody($this->getLayout()->createBlock('businessdirectory/adminhtml_directory_grid')->toHtml());
  }

  protected function _isAllowed()
  {
    #return true;
    return Mage::getSingleton('admin/session')->isAllowed('businessdirectory');
  }
}