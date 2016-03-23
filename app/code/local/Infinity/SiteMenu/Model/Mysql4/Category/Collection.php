<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {

        parent::_construct();

        $this->_init('sitemenu/category');
        
    }
    
}
