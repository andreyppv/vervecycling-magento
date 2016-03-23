<?php

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$productEntity = Mage_Catalog_Model_Product::ENTITY;

/* Add Attributes */

/* Product Label */
$attributeCode = 'product_label';
if($installer->getAttribute($productEntity, $attributeCode)) {
    $installer->removeAttribute($productEntity, $attributeCode);
}
$installer->addAttribute($productEntity, $attributeCode, array(
    'type'              		=> 'varchar',
    'label'             		=> 'Product Label',
    'input'             		=> 'text',
    'global'            		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           		=> true,
    'required'          		=> false,
    'user_defined'      		=> true,
    'backend'       			=> '',
    'sort_order'				=> 1,
    'visible_on_front'  		=> true,
    'used_in_product_listing' 	=> true,
    'group'           			=> 'General'
));