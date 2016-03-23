<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE `{$this->getTable('infinity_sitemenu_items')}` ADD `customer_group_ids` VARCHAR( 255 ) NULL;

");

$installer->endSetup();