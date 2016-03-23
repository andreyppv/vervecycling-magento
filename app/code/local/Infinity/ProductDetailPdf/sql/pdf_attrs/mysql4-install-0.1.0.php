    <?php

//Installer
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'infinity_pdf_name', array(
    'label' => 'PDF Name',
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'text',
    'visible' => true,
    'required' => false,
    'is_user_defined' => true,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default' => 0,
    'used_in_product_listing' => 1,
    'unique' => false,
));

$installer->addAttribute('catalog_product', 'infinity_pdf_file', array(
    'label' => 'PDF File',
    'group' => 'General',
    'type' => 'varchar',
    'input' => 'text',
    'visible' => true,
    'required' => false,
    'is_user_defined' => true,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'default' => 0,
    'used_in_product_listing' => 1,
    'unique' => false,
));

$mediaPath = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA);
Mage::getConfig()->createDirIfNotExists($mediaPath.'/easyinstallpdf');

$installer->endSetup();
