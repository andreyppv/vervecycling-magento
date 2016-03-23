<?php

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup();
$categoryEntityId = $installer->getEntityTypeId('catalog_category');

$installer->addAttribute($categoryEntityId, 'home_page_category_image', array(
    'type'              => 'varchar',
    'label'             => 'Home Page Category Image',
    'input'             => 'image',
    'backend'           => 'catalog/category_attribute_backend_image',	
    //'sort_order'        => 30,	
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'visible_on_front'  => true,
    'unique'            => false,
    'group'             => 'General Information'
));

$installer->addAttribute($categoryEntityId, 'home_page_category_title', array(
    'type'              => 'varchar',
    'label'             => 'Home Page Category Title',
    'input'             => 'text',
    //'backend'           => 'catalog/category_attribute_backend_image',	
    //'sort_order'        => 40,	
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'visible_on_front'  => true,
    'unique'            => false,
    'group'             => 'General Information'
));