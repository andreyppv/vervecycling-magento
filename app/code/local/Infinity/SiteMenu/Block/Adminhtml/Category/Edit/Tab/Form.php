<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();

        $this->setForm( $form );
        
        $fieldset = $form->addFieldset( 'sitemenu_form', array( 'legend' => Mage::helper('core')->__('Category information') ) );
        

        $fieldset->addField( 'name', 'text', array(
            'label'    => Mage::helper('core')->__('Category Name'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'name',
        ));

        $fieldset->addField( 'identify', 'text', array(
            'label'    => Mage::helper('core')->__('Category Identify'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'identify',
        ));

        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('core')->__('Status'),
            'name'   => 'status',
            'values' => array(
                array( 'value' => 1, 'label' => Mage::helper('core')->__('Enabled') ),
                array( 'value' => 2, 'label' => Mage::helper('core')->__('Disabled') )
            )
        ));

        $fieldset->addField( 'weight', 'text', array(
            'label'    => Mage::helper('core')->__('Weight'),
            'name'     => 'weight',
        ));

        if ( Mage::getSingleton('adminhtml/session')->getsitemenuData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getsitemenuData());
            Mage::getSingleton('adminhtml/session')->setsitemenuData(null);
        }
        elseif ( Mage::registry('sitemenu_data') ) {
            $form->setValues(Mage::registry('sitemenu_data')->getData());
        }
        
        return parent::_prepareForm();
        
    }
    
}