<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Item_Catalog_Category extends Mage_Core_Block_Template {

    public function __construct() {

        parent::__construct();

        $this->setTemplate('infinity/sitemenu/item/catalog/category.phtml');
        
    }

    public function getItems() {

        return $this->getData('items');

    }

}