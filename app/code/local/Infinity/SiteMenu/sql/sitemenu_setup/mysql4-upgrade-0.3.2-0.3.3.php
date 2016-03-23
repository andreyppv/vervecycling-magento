<?php
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$entityTypeId = $installer->getEntityTypeId('catalog_category');
 
$installer->removeAttribute($entityTypeId,'home_page_category_title');
$installer->removeAttribute($entityTypeId,'home_page_category_image');

$installer->addAttribute($entityTypeId, 'home_page_category_title', array(
    'type'              => 'varchar',
    'label'             => 'Home Page Category Title',
    'input'             => 'text',	
    'sort_order'        => 60,	
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'visible_on_front'  => true,
    'unique'            => false,
    'group'             => 'Display Settings'
));


$installer->addAttribute($entityTypeId, 'home_page_category_image', array(
    'type'              => 'varchar',
    'label'             => 'Home Page Category Image',
    'input'             => 'image',
    'backend'           => 'catalog/category_attribute_backend_image',	
    'sort_order'        => 70,	
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'visible_on_front'  => true,
    'unique'            => false,
    'group'             => 'Display Settings'
));