<?php
/**
 * Disclaimer.php
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
class CommerceExtensions_BusinessDirectory_Block_Disclaimer extends Mage_Core_Block_Template
{
  public function getDisclaimerText()
  {
    if($text = Mage::getStoreConfig('businessdirectory/frontend/disclaimer_text')) {
      $helper    = Mage::helper('cms');
      $processor = $helper->getPageTemplateProcessor();
      return $processor->filter($text);
    } else
      return null;
  }
}