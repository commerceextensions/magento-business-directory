<?php
/**
 * Image.php
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
 
class CommerceExtensions_BusinessDirectory_Model_Image extends Mage_Core_Model_Abstract
{
  public static $ALLOWED_FILE_TYPES = array('jpg', 'jpeg', 'gif', 'png');

  public function checkFileType($filename)
  {
    $filename = explode('.', $filename);
    $filetype = strtolower(end($filename));
    if(!in_array($filetype, self::$ALLOWED_FILE_TYPES)) {
      return false;
    }
    return true;
  }

  public function uploadFiles($files)
  {
    if(!empty($files) && is_array($files)) {
      $result = array();
      foreach($files as $file => $info) {
        $result[] = $this->uploadFile($file);
      }
      return $result;
    }
  }

  public function importImage($move = false, $imageFile, $fileStoredInImportFolder = false)
  {
    if($fileStoredInImportFolder) {
      $file = Mage::getBaseDir('var') . DS . 'import' . DS . $imageFile;
    } else {
      $file = Mage::getBaseDir('media') . DS . $imageFile;
    }

    $baseMediaPath  = Mage::getBaseDir('media') . DS . 'businessdirectory' . DS . 'image';
    $dynamicScmsURL = 'businessdirectory' . DS . 'image';

    $pathinfo            = pathinfo($file);
    $fileName            = Varien_File_Uploader::getCorrectFileName($pathinfo['basename']);
    $dispretionPath      = Varien_File_Uploader::getDispretionPath($fileName);
    $fileName            = $dispretionPath . DS . $fileName;
    $destinationFilePath = $baseMediaPath;

    $fileName       = $dispretionPath . DS . Varien_File_Uploader::getNewFileName($file);
    $destinationDIR = $baseMediaPath . $fileName;

    $ioAdapter = new Varien_Io_File();
    $ioAdapter->setAllowCreateFolders(true);
    $distanationDirectory = dirname($destinationDIR);

    try {
      $ioAdapter->open(array(
                         'path' => $distanationDirectory
                       ));

      if($move) {
        $ioAdapter->mv($file, $destinationDIR);
      } else {
        $ioAdapter->cp($file, $destinationDIR);
        $ioAdapter->chmod($destinationDIR, 0777);
      }
    } catch(Exception $e) {
      Mage::throwException(Mage::helper('businessdirectory')->__('Failed to move file: %s', $e->getMessage()));
    }

    return $dynamicScmsURL . $fileName;
  }

  public function uploadFile($file_name)
  {
    if(!empty($_FILES[$file_name]['name'])) {
      $result            = array();
      $dynamicScmsURL    = 'businessdirectory' . DS . 'image';
      $baseScmsMediaURL  = Mage::getBaseUrl('media') . DS . 'businessdirectory' . DS . 'image';
      $baseScmsMediaPath = Mage::getBaseDir('media') . DS . 'businessdirectory' . DS . 'image';

      $uploader = new Varien_File_Uploader($file_name);
      $uploader->setAllowedExtensions(self::$ALLOWED_FILE_TYPES);
      $uploader->setAllowRenameFiles(true);
      $uploader->setFilesDispersion(true);
      $result = $uploader->save($baseScmsMediaPath);

      $file = str_replace(DS, '/', $result['file']);
      if(substr($baseScmsMediaURL, strlen($baseScmsMediaURL) - 1) == '/' && substr($file, 0, 1) == '/') $file = substr($file, 1);

      $ScmsMediaUrl = $dynamicScmsURL . $file;

      $result['fieldname'] = $file_name;
      $result['url']       = $ScmsMediaUrl;
      $result['file']      = $result['file'];
      return $result;
    } else {
      return false;
    }
  }
}