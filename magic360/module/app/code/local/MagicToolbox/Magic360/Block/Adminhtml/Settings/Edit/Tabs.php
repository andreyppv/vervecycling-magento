<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {

        parent::__construct();

        $this->setId('magic360_config_tabs');
        $this->setDestElementId('edit_form');//this should be same as the form id
        $this->setTitle('<span style="visibility: hidden">'.Mage::helper('magic360')->__('Supported blocks:').'</span>');

    }

    protected function _beforeToHtml() {

        $blocks = Mage::helper('magic360/params')->getBlocks();
        $activeTab = $this->getRequest()->getParam('tab', 'product');

        foreach($blocks as $id => $label) {
            $this->addTab($id, array(
                'label'     => Mage::helper('magic360')->__($label),
                'title'     => Mage::helper('magic360')->__($label.' settings'),
                'content'   => $this->getLayout()->createBlock('magic360/adminhtml_settings_edit_tab_form', 'magic360_'.$id.'_settings_block')->toHtml(),
                'active'    => ($id == $activeTab) ? true : false
            ));
        }

        return parent::_beforeToHtml();

    }

}