<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('infinity_sitemenu_categories')}` ADD `weight` INT NOT NULL ;
    
    UPDATE {$this->getTable('infinity_sitemenu_categories')} SET `weight` = '1' WHERE `id` =1 ;
    UPDATE {$this->getTable('infinity_sitemenu_categories')} SET `weight` = '2' WHERE `id` =2 ;
    UPDATE {$this->getTable('infinity_sitemenu_categories')} SET `weight` = '3' WHERE `id` =3 ;

");

$installer->endSetup();