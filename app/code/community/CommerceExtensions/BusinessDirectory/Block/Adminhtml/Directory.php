<?php
/**
 * Directory.php
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
class CommerceExtensions_BusinessDirectory_Block_Adminhtml_Directory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_blockGroup = 'businessdirectory';
    $this->_controller = 'adminhtml_directory';
    $this->_headerText = $this->__('Manage Business Directories');
    parent::__construct();
    $this->_updateButton('add', 'label', $this->__('Add New Business Directory'));
    $this->_updateButton('add', 'onclick', "setLocation('{$this->getUrl('*/*/new')}')");
  }
}