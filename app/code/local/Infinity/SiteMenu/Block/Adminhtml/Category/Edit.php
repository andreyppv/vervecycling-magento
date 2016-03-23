<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {

        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'sitemenu';
        $this->_controller = 'adminhtml_category';
        
        $this->_updateButton('save', 'label', Mage::helper('core')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('core')->__('Delete Category'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('sitemenu_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'sitemenu_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'sitemenu_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {

        $_data = Mage::registry('sitemenu_data');

        if ( $_data && $_data->getId() ) {
            return Mage::helper('core')->__( 'Edit Category `%s` (ID: %d)', $this->htmlEscape( $_data->getName() ), $_data->getId() );
        }
        else {
            return Mage::helper('core')->__('Add Category');
        }

    }
    
}