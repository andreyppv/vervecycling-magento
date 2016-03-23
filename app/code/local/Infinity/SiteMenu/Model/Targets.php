<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Targets extends Varien_Object {

    const TARGET_SELF   = '_self';
    const TARGET_BLANK  = '_blank';
    const TARGET_PARENT = '_parent';
    const TARGET_TOP    = '_top';

    static public function getOptionArray() {

        return array(
            self::TARGET_SELF   => Mage::helper('sitemenu')->__('Self'),
            self::TARGET_BLANK  => Mage::helper('sitemenu')->__('Blank'),
            self::TARGET_PARENT => Mage::helper('sitemenu')->__('Parent'),
            self::TARGET_TOP    => Mage::helper('sitemenu')->__('Top'),
        );

    }

    static public function getOptionArrayForForm() {

        return array(
            array(
                'value' => self::TARGET_SELF,
                'label' => Mage::helper('sitemenu')->__('Self'),
            ),
            array(
                'value' => self::TARGET_BLANK,
                'label' => Mage::helper('sitemenu')->__('Blank'),
            ),
            array(
                'value' => self::TARGET_PARENT,
                'label' => Mage::helper('sitemenu')->__('Parent'),
            ),
            array(
                'value' => self::TARGET_TOP,
                'label' => Mage::helper('sitemenu')->__('Top'),
            )
        );
        
    }

}
