<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magic360/settings')};
CREATE TABLE {$this->getTable('magic360/settings')} (
    `setting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `website_id` smallint(5) unsigned default NULL,
    `group_id` smallint(5) unsigned default NULL,
    `store_id` smallint(5) unsigned default NULL,
    `package` varchar(255) NOT NULL default '',
    `theme` varchar(255) NOT NULL default '',
    `last_edit_time` datetime default NULL,
    `custom_settings_title` varchar(255) NOT NULL default '',
    `value` text default NULL,
    PRIMARY KEY (`setting_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

INSERT INTO {$this->getTable('magic360/settings')} (`setting_id`, `website_id`, `group_id`, `store_id`, `package`, `theme`, `last_edit_time`, `custom_settings_title`, `value`) VALUES (NULL, NULL, NULL, NULL, '', '', NULL, 'Edit Magic 360 default settings', NULL);

");

//TEXT    65,535 bytes    ~64kb
//MEDIUMTEXT   16,777,215 bytes   ~16MB
//LONGTEXT    4,294,967,295 bytes     ~4GB
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('magic360/gallery')} (
    `product_id` int( 11 ) unsigned NOT NULL AUTO_INCREMENT,
    `columns` tinyint (2) unsigned NOT NULL,
    `gallery` mediumtext default NULL,
    PRIMARY KEY (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

");

$installer->endSetup();

?>
