<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Mysql4_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {

        parent::_construct();

        $this->_init('sitemenu/item');
        
    }

    public function addVisibleFilter() {

        $this->getSelect()->join( array( 'category' => $this->getTable('sitemenu/category') ), 'category.id = main_table.category', array( 'category_id' => 'category.id', 'category_name' => 'category.name', 'category_status' => 'category.status' ) );

        $this->distinct( true )
             ->addFieldToFilter( 'category.status', 1 )
             ->addFieldToFilter( 'main_table.status', 1 )
             ->addCustomerGroupFilter()
             ->addStoreFilter();

        return $this;

    }

    public function addCustomerGroupFilter() {

        $this->getSelect()->where( "`customer_group_ids` IS NULL OR FIND_IN_SET('" . Mage::getSingleton('customer/session')->getCustomerGroupId() . "',`customer_group_ids`)" );

        return $this;

    }

    public function addStoreFilter( $store = NULL, $admin = true ) {

        if ( is_null( $store ) )
            $store = Mage::app()->getStore();

        if ( $store instanceof Mage_Core_Model_Store )
            $store = $store->getId();

        $this->getSelect()->where("FIND_IN_SET('0',`store_id`) or FIND_IN_SET('" . $store . "',`store_id`)");

        return $this;

    }

}

