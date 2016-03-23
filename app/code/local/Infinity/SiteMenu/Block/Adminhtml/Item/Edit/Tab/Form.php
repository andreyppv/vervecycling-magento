<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();

        $this->setForm( $form );

        $fieldset = $form->addFieldset( 'topmenu_form', array( 'legend' => Mage::helper('sitemenu')->__('Item information') ) );

       $fieldset->addField( 'title', 'text', array(
            'label'    => Mage::helper('sitemenu')->__('Title'),
            'name'     => 'title',
            'class'    => 'required-entry',
            'required' => true
        ));
        $fieldset->addField( 'key', 'text', array(
            'label'    => Mage::helper('sitemenu')->__('Identifier'),
            'name'     => 'key'
        ));

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'title'     => Mage::helper('catalogrule')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
        ));

        $fieldset->addField( 'category', 'select', array(
            'label'    => Mage::helper('sitemenu')->__('Category'),
            'name'     => 'category',
            'class'    => 'required-entry',
            'values'   => Mage::getSingleton('sitemenu/category')->getCategoryValuesForForm(),
            'onchange' => 'updateParentItems()'
        ));

        if ( Mage::app()->isSingleStoreMode() ) {
            $fieldset->addField( 'store_id', 'multiselect', array(
                'label'    => Mage::helper('cms')->__('Store View'),
                'name'     => 'store_id[]',
                'onclick'  => 'updateStore()',
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm( false, true ),
                'required' => true
            ));
        } else {
            $storeId = Mage::app()->getStore()->getId();
            $fieldset->addField( 'store_id', 'hidden', array(
                'name'  => 'store_id[]',
                'value' => $storeId,
            ));
        }

        $fieldset->addField( 'fid', 'select', array(
            'label'  => Mage::helper('sitemenu')->__('Parent'),
            'name'   => 'fid',
            'values' => Infinity_SiteMenu_Model_Item::getParentList()
        ));
        $fieldset->addField( 'is_catalog', 'select', array(
            'label'    => Mage::helper('sitemenu')->__('Menu Type'),
            'name'     => 'is_catalog',
            'values'   => Infinity_SiteMenu_Model_Type::getOptionArrayForForm(),
            'onchange' => 'updateItemType( this.value )'
        ));

        $fieldset->addField( 'mg_cat_id', 'select', array(
            'label'    => Mage::helper('sitemenu')->__('Product Category'),
            'name'     => 'mg_cat_id',
            'required' => true
        ));

        $fieldset->addField( 'category_url', 'select', array(
            'label'    => Mage::helper('sitemenu')->__('Choose Category Page to Url'),
            'name'     => 'category_url',
            'onchange' => 'updateCatalogUrl()',
            'note'     => '<span><input id="use_search_url" name="use_search_url" type="checkbox" value="1" onclick="updateCatalogUrl()"/> <label for="use_search_url">'. Mage::helper('sitemenu')->__('use search url') .'</label></span>'
        ));

        $fieldset->addField( 'cat_id','hidden',array(
            'label'    => Mage::helper('sitemenu')->__('ID for catalog'),
            'name'     => 'cat_id',
        ));

        $fieldset->addField( 'cmspage','select',array(
            'label'    => Mage::helper('sitemenu')->__('Choose CMS Page to Url'),
            'name'     => 'cmspage',
            'onchange' => 'updateUrl( this.value )'
        ));

        $fieldset->addField( 'url', 'text', array(
            'label'    => Mage::helper('sitemenu')->__('Url'),
            'name'     => 'url',
        ));

        $fieldset->addField( 'url_rewrite', 'text', array(
            'label'    => Mage::helper('sitemenu')->__('Real Url'),
            'name'     => 'url_rewrite'
        ));

        $fieldset->addField( 'target', 'select', array(
            'label'    => Mage::helper('sitemenu')->__('Target'),
            'name'     => 'target',
            'values'   => Infinity_SiteMenu_Model_Targets::getOptionArrayForForm(),
        ));

        $fieldset->addField( 'is_default', 'select', array(
            'label'  => Mage::helper('sitemenu')->__('Is Default'),
            'name'   => 'is_default',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        $fieldset->addField( 'status', 'select', array(
            'label'  => Mage::helper('sitemenu')->__('Status'),
            'name'   => 'status',
            'values' => Infinity_SiteMenu_Model_Status::getOptionArrayForForm(),
        ));

        $fieldset->addField( 'sort', 'text', array(
            'label'    => Mage::helper('sitemenu')->__('Sort Order'),
            'required' => false,
            'name'     => 'sort',
        ));

        //$this->_formScripts[] = $this->getLayout()->createBlock('sitemenu/adminhtml_item_edit_tab_form_js')->toHtml();

        if ( Mage::getSingleton('adminhtml/session')->getSiteMenuData() ) {
            $form->setValues( Mage::getSingleton('adminhtml/session')->getSiteMenuData() );
            Mage::getSingleton('adminhtml/session')->setSiteMenuData(null);
        }
        elseif ( Mage::registry('sitemenu_data') ) {
            $form->setValues( Mage::registry('sitemenu_data')->getData() );
        }

        return parent::_prepareForm();

    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param String $html
     *
     * @return String
     */
    protected function _afterToHtml( $html ) {

        $javascript = $this->getLayout()->createBlock('sitemenu/adminhtml_item_edit_tab_form_js')->toHtml();

        return $html . $javascript;

    }

}
