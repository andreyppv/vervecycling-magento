<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Edit_Tab_Form_Js extends Mage_Adminhtml_Block_Template {

    public function __construct() {

        parent::__construct();
        
        $this->setTemplate('infinity/sitemenu/js.phtml');

    }
    
}
