<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('infinity_sitemenu_items')} ADD `url_rewrite` varchar(100) NULL AFTER `url` ;
");

$installer->endSetup();