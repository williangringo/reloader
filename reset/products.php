<?php

$q = <<<QUERY
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `catalog_product_bundle_option`;
TRUNCATE TABLE `catalog_product_bundle_option_value`;
TRUNCATE TABLE `catalog_product_bundle_selection`;
TRUNCATE TABLE `catalog_product_entity_datetime`;
TRUNCATE TABLE `catalog_product_entity_decimal`;
TRUNCATE TABLE `catalog_product_entity_gallery`;
TRUNCATE TABLE `catalog_product_entity_int`;
TRUNCATE TABLE `catalog_product_entity_media_gallery`;
TRUNCATE TABLE `catalog_product_entity_media_gallery_value`;
TRUNCATE TABLE `catalog_product_entity_text`;
TRUNCATE TABLE `catalog_product_entity_tier_price`;
TRUNCATE TABLE `catalog_product_entity_varchar`;
TRUNCATE TABLE `catalog_product_flat_1`;
TRUNCATE TABLE `catalog_product_link`;
TRUNCATE TABLE `catalog_product_link_attribute`;
TRUNCATE TABLE `catalog_product_link_attribute_decimal`;
TRUNCATE TABLE `catalog_product_link_attribute_int`;
TRUNCATE TABLE `catalog_product_link_attribute_varchar`;
TRUNCATE TABLE `catalog_product_link_type`;
TRUNCATE TABLE `catalog_product_option`;
TRUNCATE TABLE `catalog_product_option_price`;
TRUNCATE TABLE `catalog_product_option_title`;
TRUNCATE TABLE `catalog_product_option_type_price`;
TRUNCATE TABLE `catalog_product_option_type_title`;
TRUNCATE TABLE `catalog_product_option_type_value`;
TRUNCATE TABLE `catalog_product_super_attribute_label`;
TRUNCATE TABLE `catalog_product_super_attribute_pricing`;
TRUNCATE TABLE `catalog_product_super_attribute`;
TRUNCATE TABLE `catalog_product_super_link`;
TRUNCATE TABLE `catalog_product_enabled_index`;
TRUNCATE TABLE `catalog_product_website`;
TRUNCATE TABLE `catalog_product_relation`;
TRUNCATE TABLE `catalog_category_product_index`;
TRUNCATE TABLE `catalog_category_product`;
TRUNCATE TABLE `cataloginventory_stock_item`;
TRUNCATE TABLE `cataloginventory_stock_status`;
TRUNCATE TABLE `cataloginventory_stock_status_idx`;
TRUNCATE TABLE `cataloginventory_stock`;
TRUNCATE TABLE `core_url_rewrite`;
INSERT  INTO `catalog_product_link_type`(`link_type_id`,`code`) VALUES (1,'relation'),(2,'bundle'),(3,'super'),(4,'up_sell'),(5,'cross_sell');
INSERT  INTO `catalog_product_link_attribute`(`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) VALUES (1,2,'qty','decimal'),(2,1,'position','int'),(3,4,'position','int'),(4,5,'position','int'),(6,1,'qty','decimal'),(7,3,'position','int'),(8,3,'qty','decimal');
INSERT  INTO `cataloginventory_stock`(`stock_id`,`stock_name`) VALUES (1,'Default');
TRUNCATE TABLE `catalog_product_entity`;
SET FOREIGN_KEY_CHECKS = 1;
QUERY;

require_once __DIR__ . '/../../../app/Mage.php';
Mage::app('default');

$connection = Mage::getSingleton('core/resource')->getConnection('core_write');

try {
    $connection->beginTransaction();
    $connection->query($q);
    $connection->commit();
} catch (Exception $e) {
    $connection->rollBack();
}
