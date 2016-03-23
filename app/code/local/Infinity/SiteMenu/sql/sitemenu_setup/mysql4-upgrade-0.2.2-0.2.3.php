<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('infinity_sitemenu_items')}` ADD `path` VARCHAR( 255 ) NULL;
    ALTER TABLE `{$this->getTable('infinity_sitemenu_items')}` ADD INDEX `sitemenu_path` ( `path` );

    UPDATE `{$this->getTable('infinity_sitemenu_items')}` SET `path` = '1' WHERE `id` =1 ;
    UPDATE `{$this->getTable('infinity_sitemenu_items')}` SET `path` = '2' WHERE `id` =2 ;
    UPDATE `{$this->getTable('infinity_sitemenu_items')}` SET `path` = '3' WHERE `id` =3 ;
    UPDATE `{$this->getTable('infinity_sitemenu_items')}` SET `path` = '4' WHERE `id` =4 ;

");

$installer->endSetup();