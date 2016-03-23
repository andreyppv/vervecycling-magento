<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'sitemenu';
        
        $this->_addButtonLabel = Mage::helper('core')->__('Add a Category');
        $this->_headerText     = Mage::helper('core')->__('Site Menu Category Manager');
        
        parent::__construct();
        
    }

}
