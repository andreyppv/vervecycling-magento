<?php



/* @var $this Mage_Eav_Model_Entity_Setup */
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer = $this;
$installer->startSetup();

$regions = array(
    array('code' => 'ACT', 'name' => 'Australia Capital Territory'),
    array('code' => 'NSW', 'name' => 'New South Wales'),
    array('code' => 'NT', 'name' => 'Northern Territory'),
    array('code' => 'QLD', 'name' => 'Queensland'),
    array('code' => 'SA', 'name' => 'South Australia'),
    array('code' => 'TAS', 'name' => 'Tasmania'),
    array('code' => 'VIC', 'name' => 'Victoria'),
    array('code' => 'WA', 'name' => 'Western Australia')
);

$db = Mage::getSingleton('core/resource')->getConnection('core_read');

foreach ($regions as $region) {
    // Check if this region has already been added
    $result = $db->fetchOne("SELECT code FROM " . $this->getTable('directory_country_region') . " WHERE `country_id` = 'AU' AND `code` = '" . $region['code'] . "'");
    if ($result != $region['code']) {
        $installer->run(
                "INSERT INTO `{$this->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`) VALUES
            ('AU', '" . $region['code'] . "', '" . $region['name'] . "');
            INSERT INTO `{$this->getTable('directory_country_region_name')}` (`locale`, `region_id`, `name`) VALUES
            ('en_US', LAST_INSERT_ID(), '" . $region['name'] . "'), ('en_AU', LAST_INSERT_ID(), '" . $region['name'] . "');"
        );
    }
}

$installer->endSetup();
