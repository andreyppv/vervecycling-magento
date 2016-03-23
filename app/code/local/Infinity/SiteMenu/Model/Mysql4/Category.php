<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        // Note that the sitemenu_id refers to the key field in your database table.
        $this->_init( 'sitemenu/category', 'id' );
        
    }

}
