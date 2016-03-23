<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    
    ALTER TABLE {$this->getTable('infinity_sitemenu_items')} ADD `mg_cat_id` INT( 11 ) NOT NULL AFTER `is_catalog` ;

    UPDATE {$this->getTable('infinity_sitemenu_items')} SET `mg_cat_id` = '2' WHERE `id` =2 LIMIT 1 ;

");

$installer->endSetup();