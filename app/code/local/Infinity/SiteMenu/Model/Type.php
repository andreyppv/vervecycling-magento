<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2013 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.Z
 */
class Infinity_SiteMenu_Model_Type extends Varien_Object {

    const TYPE_NORMAL      = 0;
    const TYPE_CATALOG_CAT = 1;
    const TYPE_CATALOG_PRO = 2;

    static public function getOptionArray() {

        return array(
            self::TYPE_NORMAL      => Mage::helper('sitemenu')->__('Normal Link'),
            self::TYPE_CATALOG_CAT => Mage::helper('sitemenu')->__('Product Category Tree'),
            self::TYPE_CATALOG_PRO => Mage::helper('sitemenu')->__('Product List')
        );

    }

    static public function getOptionArrayForForm() {

        return array(
            array(
                'value' => self::TYPE_NORMAL,
                'label' => Mage::helper('sitemenu')->__('Normal Link'),
            ),
            array(
                'value' => self::TYPE_CATALOG_CAT,
                'label' => Mage::helper('sitemenu')->__('Product Category Tree'),
            ),
            array(
                'value' => self::TYPE_CATALOG_PRO,
                'label' => Mage::helper('sitemenu')->__('Product List'),
            )
        );
        
    }

}
