<?php
/**
 * Infinity Support Swithcer
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 */

class Infinity_SupportSwitcher_Block_Switcher extends Mage_Core_Block_Template
{
    public function parseLocationsOption()
    {
        return Mage::helper('supportswitcher/data')->parseLocationsOption();
    }
}