<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn($installer->getTable('businessdirectory/directory'), 'can_claim_profile', array(
  'type'    => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
  'comment' => 'Allow Users to Claim Profile',
));
$connection->addColumn($installer->getTable('businessdirectory/directory'), 'can_submit_new_listing', array(
  'type'    => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
  'comment' => 'Allow Users to Submit New Listings',
));
$connection->addColumn($installer->getTable('businessdirectory/directory'), 'append_location_to_url', array(
  'type'    => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
  'comment' => 'Append Location Data to Listing URL',
));
$installer->endSetup();
