<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory'))
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'identity' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ), 'Directory ID')
                   ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true
                   ), 'Directory Title')
                   ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                     'default'  => null,
                   ), 'Directory String Identifier')
                   ->addColumn('distance_units', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
                     'nullable' => true,
                     'default'  => 'miles'
                   ), 'Display Distance in Miles or Kilometers')
                   ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => '1',
                   ), 'Is Directory Active')
                   ->addColumn('search_name_placeholder', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Name Searchbox Placeholder Text')
                   ->addColumn('search_location_placeholder', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Location Searchbox Placeholder Text')
                   ->addColumn('display_featured_listings', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                     'nullable' => false,
                     'default'  => 1,
                   ), 'Number of Featured Listings to Display')
                   ->addColumn('featured_listing_display_count', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => 10,
                   ), 'Number of Featured Listings to Display')
                   ->addColumn('featured_listing_button_text', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
                     'nullable' => true,
                     'default'  => 'Featured Listing!',
                   ), 'Featured Listings Button Text')
                   ->addColumn('show_map', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                     'nullable' => false,
                     'default'  => 1,
                   ), 'Can Display Map?')
                   ->addColumn('map_width', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => 193,
                   ), 'Map Width')
                   ->addColumn('map_height', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => 300,
                   ), 'Map Height')
                   ->addColumn('show_country', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                     'nullable' => false,
                     'default'  => 0
                   ), 'Show Country In Address On Frontend')
                   ->addColumn('use_schema_on_directory', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                     'nullable' => false,
                   ), 'Use Schema.org on Directory?')
                   ->addColumn('schema_type_url_for_directory', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Schema Type URL for Directory')
                   ->addColumn('use_schema_on_profile', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                     'nullable' => false,
                   ), 'Use Schema.org on Profile?')
                   ->addColumn('schema_type_url_for_profile', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Schema Type URL for Profile')
                   ->addColumn('profile_title_tag_structure', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Profile Page Default Title Tag Structure')
                   ->addColumn('profile_default_meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Profile Page Default Meta Keywords')
                   ->addColumn('profile_default_meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Profile Page Default Meta Description')
                   ->addColumn('root_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true
                   ), 'Directory Template')
                   ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Directory Meta Keywords')
                   ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Directory Meta Description')
                   ->addColumn('content_heading', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true,
                   ), 'Directory Content Heading')
                   ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
                     'nullable' => true,
                   ), 'Directory Content')
                   ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Directory Creation Time')
                   ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Directory Modification Time')
                   ->addColumn('layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                     'nullable' => true,
                   ), 'Directory Layout Update Content')
                   ->addColumn('custom_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Directory Custom Theme')
                   ->addColumn('custom_root_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true,
                   ), 'Directory Custom Template')
                   ->addColumn('custom_layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                     'nullable' => true,
                   ), 'Directory Custom Layout Update Content')
                   ->addColumn('custom_theme_from', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
                     'nullable' => true,
                   ), 'Directory Custom Theme Active From Date')
                   ->addColumn('custom_theme_to', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
                     'nullable' => true,
                   ), 'Directory Custom Theme Active To Date')
                   ->addIndex($installer->getIdxName('businessdirectory/directory', array('identifier')),
                              array('identifier'))
                   ->setComment('Business Directory Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory_store'))
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'nullable' => false,
                     'primary'  => true,
                   ), 'Directory ID')
                   ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ), 'Store ID')
                   ->addIndex($installer->getIdxName('businessdirectory/directory_store', array('store_id')),
                              array('store_id'))
                   ->addForeignKey($installer->getFkName('businessdirectory/directory_store', 'directory_id', 'businessdirectory/directory', 'directory_id'),
                                   'directory_id', $installer->getTable('businessdirectory/directory'), 'directory_id',
                                   Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                   ->addForeignKey($installer->getFkName('businessdirectory/directory_store', 'directory_id', 'core/store', 'store_id'),
                                   'store_id', $installer->getTable('core/store'), 'store_id',
                                   Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                   ->setComment('Business Directory To Store Linkage Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory_listing'))
                   ->addColumn('listing_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'identity' => true,
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ), 'Listing Entry ID')
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ), 'Directory ID')
                   ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
                     'unsigned' => true,
                     'nullable' => false,
                   ), 'Customer ID')
                   ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                     'default'  => null,
                   ), 'Listing String Identifier')
                   ->addColumn('listing_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Name')
                   ->addColumn('address_line_one', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Address Line 1')
                   ->addColumn('address_line_two', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Address Line 2')
                   ->addColumn('listing_city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business City')
                   ->addColumn('listing_state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business State')
                   ->addColumn('listing_zip_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Zip Code')
                   ->addColumn('listing_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Country')
                   ->addColumn('listing_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business E-Mail')
                   ->addColumn('listing_website', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Website')
                   ->addColumn('listing_contact_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Contact Name')
                   ->addColumn('listing_phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Phone')
                   ->addColumn('listing_fax', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Fax')
                   ->addColumn('listing_comments', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Additional Info')
                   ->addColumn('latitude', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,6', array(
                     'nullable' => false,
                     'default'  => 0
                   ), 'Listing Address Latitude')
                   ->addColumn('longitude', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,6', array(
                     'nullable' => false,
                     'default'  => 0
                   ), 'Listing Address Longitude')
                   ->addColumn('is_featured', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                     'nullable' => false,
                     'default'  => 0
                   ), 'Is Featured Listing')
                   ->addColumn('backlink', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Backlink Verification Site')
                   ->addColumn('facebook_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Facebook Page')
                   ->addColumn('twitter_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Twitter Page')
                   ->addColumn('google_plus_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Google Plus Page')
                   ->addColumn('listing_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Image')
                   ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                     'nullable' => false,
                     'default'  => 1
                   ), 'Is Enabled Listing')
                   ->addColumn('root_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true
                   ), 'Listing Template')
                   ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Listing Meta Keywords')
                   ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Listing Meta Description')
                   ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
                     'nullable' => true,
                   ), 'Listing Content')
                   ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Listing Creation Time')
                   ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Listing Modification Time')
                   ->addColumn('layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                     'nullable' => true,
                   ), 'Listing Layout Update Content')
                   ->addColumn('custom_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                   ), 'Listing Custom Theme')
                   ->addColumn('custom_root_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                     'nullable' => true,
                   ), 'Listing Custom Template')
                   ->addColumn('custom_layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                     'nullable' => true,
                   ), 'Listing Custom Layout Update Content')
                   ->addColumn('custom_theme_from', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
                     'nullable' => true,
                   ), 'Listing Custom Theme Active From Date')
                   ->addColumn('custom_theme_to', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
                     'nullable' => true,
                   ), 'Listing Custom Theme Active To Date')
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('identifier')),
                              array('identifier'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('directory_id')),
                              array('directory_id'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_name')),
                              array('listing_name'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_city')),
                              array('listing_city'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_state')),
                              array('listing_state'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_zip_code')),
                              array('listing_zip_code'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_country')),
                              array('listing_country'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('latitude')),
                              array('latitude', 'longitude'))
                   ->addForeignKey($installer->getFkName('businessdirectory/directory_listing', 'directory_id', 'businessdirectory/directory', 'directory_id'),
                                   'directory_id', $installer->getTable('businessdirectory/directory'), 'directory_id',
                                   Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                   ->setComment('Business Directory To Listings Linkage Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory_listing_submit'))
                   ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'identity' => true,
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ))
                   ->addColumn('listing_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => true,
                   ), 'Listing Entry ID')
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                   ), 'Directory ID')
                   ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
                     'unsigned' => true,
                     'nullable' => false,
                   ), 'Customer ID')
                   ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                     'nullable' => true,
                     'default'  => null,
                   ), 'Listing String Identifier')
                   ->addColumn('listing_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Name')
                   ->addColumn('address_line_one', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Address Line 1')
                   ->addColumn('address_line_two', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Address Line 2')
                   ->addColumn('listing_city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business City')
                   ->addColumn('listing_state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business State')
                   ->addColumn('listing_zip_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Zip Code')
                   ->addColumn('listing_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Country')
                   ->addColumn('listing_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business E-Mail')
                   ->addColumn('listing_website', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Website')
                   ->addColumn('listing_contact_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Contact Name')
                   ->addColumn('listing_phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Phone')
                   ->addColumn('listing_fax', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                     'nullable' => true
                   ), 'Business Fax')
                   ->addColumn('listing_comments', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Additional Info')
                   ->addColumn('is_featured', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                     'nullable' => false,
                     'default'  => 0
                   ), 'Is Featured Listing')
                   ->addColumn('backlink', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Backlink Verification Site')
                   ->addColumn('facebook_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Facebook Page')
                   ->addColumn('twitter_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Twitter Page')
                   ->addColumn('google_plus_page', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Google Plus Page')
                   ->addColumn('listing_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true
                   ), 'Business Image')
                   ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
                     'nullable' => true,
                   ), 'Listing Content')
                   ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Claim / Update Request Time')
                   ->addColumn('action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array(
                     'nullable' => true
                   ), 'Listing Submission Type')
                   ->addColumn('action', Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array(
                     'nullable' => true
                   ), 'New / Update / Claim')
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('directory_id')),
                              array('directory_id'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_listing', array('listing_id')),
                              array('listing_id'))
                   ->setComment('Business Directory Listing Submission Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory_staticfilter'))
                   ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'identity' => true,
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ))
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                   ), 'Directory ID')
                   ->addColumn('filter_field', Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array(
                     'nullable' => false,
                   ), 'Filter Name')
                   ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => '1',
                   ), 'Is Filter Active')
                   ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                     'nullable' => false,
                     'default'  => '1',
                   ), 'Is Filter Active')
                   ->addColumn('title_tag', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                     'nullable' => true,
                   ), 'Title Tag Setup')
                   ->addColumn('h1_tag', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                     'nullable' => true,
                   ), 'Title Tag Setup')
                   ->addColumn('filter_label', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                     'nullable' => true,
                   ), 'Filter Container Label')
                   ->addColumn('filter_link_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                     'nullable' => true,
                   ), 'Filter Link Title Tag Setup')
                   ->addColumn('num_cols', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                     'default'  => 5
                   ), 'How many filter columns on frontend')
                   ->addColumn('num_rows', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                     'default'  => 12
                   ), 'How many filter rows on frontend')
                   ->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Directory Meta Description')
                   ->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
                     'nullable' => true,
                   ), 'Directory Meta Keywords')
                   ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
                     'nullable' => true,
                   ), 'Directory Content')
                   ->addIndex($installer->getIdxName('businessdirectory/directory_staticfilter', array('id')),
                              array('id'))
                   ->addIndex($installer->getIdxName('businessdirectory/directory_staticfilter', array('filter_field')),
                              array('directory_id', 'filter_field', 'is_active'))
                   ->addForeignKey($installer->getFkName('businessdirectory/directory_staticfilter', 'directory_id', 'businessdirectory/directory', 'directory_id'),
                                   'directory_id', $installer->getTable('businessdirectory/directory'), 'directory_id',
                                   Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                   ->setComment('Business Directory To Static Filter Linkage Table');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
                   ->newTable($installer->getTable('businessdirectory/directory_staticfilter_content'))
                   ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'identity' => true,
                     'unsigned' => true,
                     'nullable' => false,
                     'primary'  => true,
                   ))
                   ->addColumn('directory_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                     'unsigned' => true,
                     'nullable' => false,
                   ), 'Directory ID')
                   ->addColumn('filter_field', Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array(
                     'nullable' => false,
                   ), 'Filter Name')
                   ->addColumn('filter_value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 128, array(
                     'nullable' => false,
                   ), 'Filter Value')
                   ->addColumn('field_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 24, array(
                     'nullable' => false,
                   ), 'Filter Type')
                   ->addColumn('content', Varien_Db_Ddl_Table::TYPE_VARCHAR, '1M', array(
                     'nullable' => false,
                   ), 'Content')
                   ->addIndex($installer->getIdxName('businessdirectory/directory_staticfilter_content', array('filter_field')),
                              array('directory_id', 'filter_field', 'filter_value', 'field_type'))
                   ->addForeignKey($installer->getFkName('businessdirectory/directory_staticfilter_content', 'directory_id', 'businessdirectory/directory', 'directory_id'),
                                   'directory_id', $installer->getTable('businessdirectory/directory'), 'directory_id',
                                   Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                   ->setComment('Business Directory To Static Filter Content Linkage Table');
$installer->getConnection()->createTable($table);

$installer->setConfigData('businessdirectory/frontend/show_breadcrumbs', 1);
$installer->setConfigData('businessdirectory/frontend/url_suffix', '/');
$installer->setConfigData('businessdirectory/frontend/list_image_width', 60);
$installer->setConfigData('businessdirectory/frontend/list_image_height', 60);
$installer->setConfigData('businessdirectory/frontend/profile_image_width', 60);
$installer->setConfigData('businessdirectory/frontend/profile_image_height', 60);
$installer->setConfigData('businessdirectory/frontend/disclaimer_text', 'In an attempt to provide increased value to our visitors, {{config path="general/store_information/name"}} may provide links, phone numbers, and other methods of contact to websites / businesses operated by third parties. However, even if the third party is affiliated with {{config path="general/store_information/name"}}, {{config path="general/store_information/name"}} has no control over these linked websites / businesses, all of which have separate privacy policies and business practices, independent of {{config path="general/store_information/name"}}. These linked websites / businesses are only for your convenience and therefore you access them at your own risk. {{config path="general/store_information/name"}} assumes no responsibility whatsoever for any damage or negative consequences that may occur as a result of contacting or utilizing these websites / businesses.');
$installer->setConfigData('businessdirectory/pending/geocode_on_approve', 1);

$installer->endSetup();
