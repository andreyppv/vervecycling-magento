<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {

        parent::__construct();

        $this->setId('sitemenu_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle( Mage::helper('core')->__('Item Information') );
        
    }

    protected function _beforeToHtml() {
        
        $this->addTab('form_section', array(
            'label'   => Mage::helper('core')->__('Item Information'),
            'title'   => Mage::helper('core')->__('Item Information'),
            'content' => $this->getLayout()->createBlock('sitemenu/adminhtml_item_edit_tab_form')->toHtml()
        ));

        return parent::_beforeToHtml();
        
    }

}
