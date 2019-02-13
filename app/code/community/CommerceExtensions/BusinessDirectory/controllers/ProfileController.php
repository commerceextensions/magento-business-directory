<?php

class CommerceExtensions_BusinessDirectory_ProfileController extends Mage_Core_Controller_Front_Action
{
  protected function _getSession()
  {
    return Mage::getSingleton('customer/session');
  }

  public function loginAction()
  {
    if($errorMessage = $this->_checkParamErrors()) {
      $this->_getSession()->addError($errorMessage);
      $this->_redirectUrl(Mage::getModel('businessdirectory/directory')->_getRedirectBackUrl());
      return;
    }

    $this->loadLayout();
    $this->_initLayoutMessages('customer/session');
    $this->getLayout()->getBlock('head')->setTitle($this->__('User Login'));
    $this->renderLayout();
  }

  public function registerAction()
  {
    if($errorMessage = $this->_checkParamErrors()) {
      $this->_getSession()->addError($errorMessage);
      $this->_redirectUrl(Mage::getModel('businessdirectory/directory')->_getRedirectBackUrl());
      return;
    }

    $this->loadLayout();
    $this->_initLayoutMessages('customer/session');
    $this->getLayout()->getBlock('head')->setTitle($this->__('Register Your Account'));
    $this->renderLayout();
  }

  public function newAction()
  {
    // set data so the observer knows what page to set the url for
    $this->_getSession()->setData('directory_action', 'businessdirectory/profile/new');
    $this->_getSession()->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());

    $this->loadLayout();
    $this->_setBreadcrumbs();
    $this->_initLayoutMessages('customer/session');

    $_directoryModel = Mage::getModel('businessdirectory/directory');

    if(!Mage::helper('customer')->isLoggedIn()) {
      // force login to submit new listing
      $this->_redirect('*/*/login', array('_current' => true));
      return;
    }

    if($errorMessage = $this->_checkParamErrors()) {
      $this->_getSession()->addError($errorMessage);
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    if($directoryId = $this->getRequest()->getParam('directory_id')) {
      $directory = $_directoryModel->load($directoryId);

      if(!$directory->getCanSubmitNewListing()) {
        // if directory does not allow new listings to be created
        $this->_getSession()->addError('This directory does not permit users to create new listings.');
        $this->_redirectUrl($directory->getUrl());
        return;
      }

      $this->getLayout()->getBlock('head')
           ->setTitle($this->__('%s: New Company', $directory->getContentHeading()));
    } else {
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    $this->renderLayout();
  }

  public function claimAction()
  {
    // set data so the observer knows what page to set the url for
    $this->_getSession()->setData('directory_action', 'businessdirectory/profile/claim');
    $this->_getSession()->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());

    $this->loadLayout();
    $this->_setBreadcrumbs();
    $this->_initLayoutMessages('customer/session');

    $_directoryModel = Mage::getModel('businessdirectory/directory');

    if(!Mage::helper('customer')->isLoggedIn()) {
      // force login to claim listing
      $this->_redirect('*/*/login', array('_current' => true));
      return;
    }

    if($errorMessage = $this->_checkParamErrors()) {
      $this->_getSession()->addError($errorMessage);
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    if($listingId = $this->getRequest()->getParam('listing_id')) {
      $listing = Mage::getModel('businessdirectory/directory_listing')
                     ->load($listingId);

      $directoryId = $listing->getDirectoryId();
      $directory   = $_directoryModel->load($directoryId);
      if(!$directory->getCanClaimProfile()) {
        // if directory does not allow profiles to be claimed
        $this->_getSession()->addError('This directory does not permit profiles to be claimed.');
        $this->_redirectUrl($directory->getUrl());
        return;
      }

      if((int)$listing->getCustomerId() > 0) {
        $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
        return;
      }

      $this->getLayout()->getBlock('head')
           ->setTitle($this->__('%s: Claim Your Profile', $listing->getListingName()));
    } else {
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    $this->renderLayout();
  }

  public function updateAction()
  {
    // set data so the observer knows what page to set the url for
    $this->_getSession()->setData('directory_action', 'businessdirectory/profile/update');
    $this->_getSession()->setBeforeAuthUrl(Mage::helper('core/url')->getCurrentUrl());

    $this->loadLayout();
    $this->_setBreadcrumbs();
    $this->_initLayoutMessages('customer/session');

    $_directoryModel = Mage::getModel('businessdirectory/directory');

    if(!Mage::helper('customer')->isLoggedIn()) {
      // force login to claim listing
      $this->_redirect('*/*/login', array('_current' => true));
      return;
    }

    if($errorMessage = $this->_checkParamErrors()) {
      $this->_getSession()->addError($errorMessage);
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    if($listingId = $this->getRequest()->getParam('listing_id')) {
      $listing = Mage::getModel('businessdirectory/directory_listing')
                     ->load($listingId);

      // if listing does not belong to customer, do not allow them to update the listing
      if(!(int)$listing->getCustomerId() ||
         (int)$listing->getCustomerId() < 1 ||
         (int)$listing->getCustomerId() !== (int)Mage::helper('customer')->getCustomer()->getId()
      ) {
        $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
        return;
      }

      $this->getLayout()->getBlock('head')
           ->setTitle($this->__('%s: Update Your Profile', $listing->getListingName()));

    } else {
      $this->_redirectUrl($_directoryModel->_getRedirectBackUrl());
      return;
    }

    $this->renderLayout();
  }

  public function submitAction()
  {
    if($data = $this->getRequest()->getPost()) {

      $formName = $this->_getFormName($data);
      $this->_getSession()->setData($formName, new Varien_Object($data));

      $url = '*/*/' . $data['action'];

      $directory = Mage::getModel('businessdirectory/directory')
                       ->load($data['directory_id']);

      $listing = null;
      if(array_key_exists('listing_id', $data)) {
        $listing = Mage::getModel('businessdirectory/directory_listing')->load($data['listing_id']);
      }

      try {
        if($listing) {
          $data['identifier'] = $listing->getIdentifier();
        } else {
          $urlModel           = Mage::getModel('businessdirectory/directory_listing_url');
          $data['identifier'] = $urlModel->createUrl($data, $directory);
        }

        $imageFieldExists = array_key_exists('listing_image', $data) ? true : false;

        // add uploaded image to the data array
        $imageModel = Mage::getModel('businessdirectory/image');
        if(!$_FILES['listing_image']['name']) {
          if(array_key_exists('current_listing_image', $data)) {
            $data['listing_image'] = $data['current_listing_image'];
          } else {
            $data['listing_image'] = null;
          }
        } else {
          if(!empty($_FILES) && $_FILES['listing_image']['name'] && !$imageModel->checkFileType($_FILES['listing_image']['name'])) {

            $permittedImageTypes = implode(', ', CommerceExtensions_BusinessDirectory_Model_Image::$ALLOWED_FILE_TYPES);
            $this->_getSession()->addError($this->__('Unable to save. Image files must be one of the following types: %s', $permittedImageTypes));

            $this->_redirect($url, array('_current' => true));
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
        }

        $this->_getSession()->setData($formName, new Varien_Object($data));

        $submitModel = Mage::getModel('businessdirectory/directory_listing_submit');
        $submitModel->setData($data);
        $submissionId = $submitModel->save()->getId();

        $submission = $submitModel->load($submissionId);

        switch($data['action']) {
          case 'new':
            Mage::dispatchEvent('businessdirectory_profile_new_submit_after', array('listing' => $submission));
            $message = $this->__('Thank you for submitting your company to be listed in the %s directory. It should appear in the directory within 24-48 hours.', $directory->getContentHeading());
            $this->_getSession()->addSuccess($message);
            $this->_redirect('*/*/success', array('_current' => true));
            return;
            break;
          case 'claim':
            Mage::dispatchEvent('businessdirectory_profile_claim_submit_after', array('listing' => $submission));
            $message = $this->__('Thank you for claiming and completing your business profile. It should appear in the %s directory with any changes you have made within 24-48 hours.', $directory->getContentHeading());
            $this->_getSession()->addSuccess($message);
            $this->_redirect('*/*/success', array('_current' => true));
            return;
            break;
          case 'update':
            Mage::dispatchEvent('businessdirectory_profile_update_submit_after', array('listing' => $submission));
            $message = $this->__('Thank you for updating your business profile. Any changes you have made should appear within 24-48 hours.', $directory->getContentHeading());;
            $this->_getSession()->addSuccess($message);
            break;
        }

      } catch(Mage_Core_Exception $e) {
        $this->_getSession()->addError($e->getMessage());
        $this->_redirect($url, array('_current' => true));
        return;
      } catch(Exception $e) {
        $this->_getSession()->addError($e->getMessage());
        $this->_redirect($url, array('_current' => true));
        return;
      }
      $this->_getSession()->unsetData($formName);

      if($url = $directory->getUrl()) {
        $this->_redirectUrl($url);
      } else {
        $this->_redirect('*/*/success', array('_current' => true));
      }
      return;
    }
  }

  public function successAction()
  {
    $directoryId = null;
    $listingId   = null;

    if($this->getRequest()->getParam('directory_id')) {
      $directoryId = $this->getRequest()->getParam('directory_id');
    }
    if($this->getRequest()->getParam('listing_id')) {
      $listingId = $this->getRequest()->getParam('listing_id');
    }

    if(is_null($directoryId) && is_null($listingId)) {
      $url = rtrim(Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL, Mage::app()->getStore()->getStoreId()), '/');
      $this->_redirectUrl($url);
      return;
    }

    $this->loadLayout();
    $this->renderLayout();

    if(!$directoryId && $listingId) {
      $listing     = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
      $directoryId = $listing->getDirectoryId();
    }
    $directory = Mage::getModel('businessdirectory/directory')
                     ->load($directoryId);
    $url       = $directory->getUrl();
    $this->_redirectUrl($url);
    return;
  }

  protected function _checkParamErrors()
  {
    if(!$this->getRequest()->getParam('directory_id') && !$this->getRequest()->getParam('listing_id')) {
      // if there no directory or listing params in the url redirect the user back to their last location
      return $this->__('There was an error accessing the listing profile or business directory.');
    }
    if($directoryId = $this->getRequest()->getParam('directory_id')) {
      $directory = Mage::getModel('businessdirectory/directory')->load($directoryId);
      if(!$directory->getDirectoryId()) {
        return $this->__('The directory that you are trying to access does not exist.');
      }
    }
    if($listingId = $this->getRequest()->getParam('listing_id')) {
      $listing = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
      if(!$listing->getListingId()) {
        return $this->__('The listing profile that you are trying to claim does not exist.');
      }
    }
    return null;
  }

  protected function _getFormName($data)
  {
    if($data['listing_id']) {
      $identifier = 'listing_id_' . $data['listing_id'];
    } elseif($data['directory_id']) {
      $identifier = 'directory_id_' . $data['directory_id'];
    }
    return $data['action'] . '_' . $identifier;
  }

  protected function _setBreadcrumbs()
  {
    if(Mage::getStoreConfig('businessdirectory/frontend/show_breadcrumbs') && $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {

      if($directoryId = $this->getRequest()->getParam('directory_id')) {

        $directory = Mage::getModel('businessdirectory/directory')->load($directoryId);
        $breadcrumbs->addCrumb('home',
                               array(
                                 'label' => $this->__('Home'),
                                 'title' => $this->__('Go to Home Page'),
                                 'link'  => Mage::getBaseUrl()
                               )
        );
        if($directory->getContentHeading()) {
          $breadcrumbs->addCrumb('directory',
                                 array(
                                   'label' => $this->__($directory->getContentHeading()),
                                   'title' => $this->__($directory->getContentHeading()),
                                   'link'  => $directory->getUrl()
                                 )
          );
        }
        $breadcrumbs->addCrumb('action',
                               array(
                                 'label' => $this->__('Submit New Listing'),
                                 'title' => $this->__('Submit New Listing')
                               )
        );

      } elseif($listingId = $this->getRequest()->getParam('listing_id')) {

        $listing   = Mage::getModel('businessdirectory/directory_listing')->load($listingId);
        $directory = Mage::getModel('businessdirectory/directory')->load($listing->getDirectoryId());
        $breadcrumbs->addCrumb('home',
                               array(
                                 'label' => $this->__('Home'),
                                 'title' => $this->__('Go to Home Page'),
                                 'link'  => Mage::getBaseUrl()
                               )
        );
        if($directory->getContentHeading()) {
          $breadcrumbs->addCrumb('directory',
                                 array(
                                   'label' => $this->__($directory->getContentHeading()),
                                   'title' => $this->__($directory->getContentHeading()),
                                   'link'  => $directory->getUrl()
                                 )
          );
        }
        $breadcrumbs->addCrumb('listing',
                               array(
                                 'label' => $this->__($listing->getListingName()),
                                 'title' => $this->__($listing->getListingName()),
                                 'link'  => $listing->getUrl()
                               )
        );

        $action = $this->getRequest()->getActionName();
        $label  = ucfirst($action) . " Your Business Profile";

        $breadcrumbs->addCrumb('action',
                               array(
                                 'label' => $this->__($label),
                                 'title' => $this->__($label)
                               )
        );
      }
    }
  }

  public function buttonAction()
  {
    $this->loadLayout();
    $_divId     = $this->getRequest()->getParam('listingId');
    $_listingId = str_ireplace(array('button-', 'item-'), '', $_divId);
    $_listing   = Mage::getModel('businessdirectory/directory_listing')->getListing($_listingId);

    $_directory = Mage::getModel('businessdirectory/directory')->getDirectory($_listing->getDirectoryId());
    $block      = $this->getLayout()->createBlock('businessdirectory/directory_listing_button');
    $block->setCanClaimProfile($_directory->getCanClaimProfile());
    $_response                                         = array();
    $_response["#button-" . $_listing->getListingId()] = $block->getButtonHtml($_listing);
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($_response));
    return;
  }

  public function extractId($string)
  {
    return str_ireplace(array('button-', 'item-'), '', $string);
  }

}