<?php

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn($installer->getTable('businessdirectory/directory'), 'is_us_only', array(
  'type'    => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
  'comment' => 'Is United States Based Directory',
));
$installer->endSetup();
