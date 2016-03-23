<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Category extends Mage_Core_Model_Abstract {
    
    public function _construct() {

        parent::_construct();
        
        $this->_init('sitemenu/category');

    }

    /**
     * get Catalog categories optionArray
     *
     * @return array :the select options of the store categories
     */
    public function getOptionArray() {

        $childen = Mage::getSingleton('sitemenu/category')->getCollection();

        $arr = array();
        foreach ( $childen as $child )
            $arr[ $child->getId() ] = $child->getName();
        
        return $arr;
        
    }

    /**
     * get Catalog categories optionArray
     * 
     * @return array :the select options of the store categories
     */
    public function getCategoryValuesForForm() {

        $childen = Mage::getModel('sitemenu/category')->getCollection()->getAllIds();

        $arr = array();
        foreach ( $childen as $child ) {
            $arr[] = array(
                'label' => Mage::getModel('sitemenu/category')->load( $child )->getName(),
                'value' => Mage::getModel('sitemenu/category')->load( $child )->getId(),
            );
        }
        
        return $arr;

    }

}