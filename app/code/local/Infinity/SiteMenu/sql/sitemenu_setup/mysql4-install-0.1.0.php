<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('infinity_sitemenu_items')};
CREATE TABLE {$this->getTable('infinity_sitemenu_items')} (
    `id` int(11) NOT NULL auto_increment,
    `fid` int(11) NOT NULL default '0',
    `store_id` varchar(100) NOT NULL default '0',
    `title` varchar(100) NULL,
    `key` varchar(50) NULL,
    `url` varchar(100) NULL,
    `sort` int(11) NOT NULL default '0',
    `target` enum('_self','_blank','_parent','_top') default '_self',
    `status` tinyint(2) NOT NULL default '0',
    `is_catalog` tinyint(1) NOT NULL default '0',
    `category` int(11) NOT NULL default '0',
    `is_default` tinyint(1) NOT NULL default '0',
    PRIMARY KEY  (`id`),
    KEY `sort` (`sort`),
    KEY `fid` (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('infinity_sitemenu_items')} (`id`, `fid`, `store_id`, `title`, `key`, `url`, `sort`, `target`, `status`, `category`, `is_default`, `is_catalog`) VALUES
(1, 0, '0', 'Home', 'home', 'home', 1, '_self', 1, 1, 1, 0),
(2, 0, '0', 'Catalog', 'catalog', '#', 2, '_self', 1, 1, 0, 1),
(3, 0, '0', 'About Us', 'about-us', 'about-magento-demo-store', 1, '_self', 1, 3, 0, 0),
(4, 0, '0', 'Customer Service', 'customer-service', 'customer-service', 2, '_self', 1, 3, 0, 0);

DROP TABLE IF EXISTS {$this->getTable('infinity_sitemenu_categories')};
CREATE TABLE {$this->getTable('infinity_sitemenu_categories')} (
    `id` int(11) NOT NULL auto_increment,
    `name` varchar(100) NULL,
    `identify` varchar(100) NULL,
    `status` tinyint(2) NOT NULL default '0',
    PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('infinity_sitemenu_categories')} (`id`, `name`, `identify`, `status`) VALUES
(1, 'Main', 'main', 1),
(2, 'Top', 'top', 1),
(3, 'Footer', 'footer', 1);

");

$installer->endSetup(); 