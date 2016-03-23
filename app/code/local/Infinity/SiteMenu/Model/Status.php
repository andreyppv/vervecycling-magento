<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Status extends Varien_Object {

    const STATUS_ENABLED  = 1;
    const STATUS_DISABLED = 0;

    static public function getOptionArray() {

        return array(
            self::STATUS_ENABLED => Mage::helper('core')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('core')->__('Disabled')
        );

    }

    static public function getOptionArrayForForm() {

        return array(
            array(
                'value' => self::STATUS_ENABLED,
                'label' => Mage::helper('core')->__('Enabled'),
            ),
            array(
                'value' => self::STATUS_DISABLED,
                'label' => Mage::helper('core')->__('Disabled'),
            )
        );
        
    }

}
