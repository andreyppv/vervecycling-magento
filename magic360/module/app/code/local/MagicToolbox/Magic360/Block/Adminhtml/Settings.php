<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        $this->_blockGroup = 'magic360';//module name
        $this->_controller = 'adminhtml_settings';//the path to your block class
        $this->_headerText = Mage::helper('magic360')->__('Magic 360&#8482; settings');
        parent::__construct();
        $this->setTemplate('magic360/settings.phtml');

    }

    protected function _prepareLayout() {

        $this->setChild('settings_grid', $this->getLayout()->createBlock('magic360/adminhtml_settings_grid', 'magic360.grid'));
        $this->setChild('custom_design_settings_form', $this->getLayout()->createBlock('magic360/adminhtml_settings_form', 'magic360.form'));
        return parent::_prepareLayout();

    }

    public function getAddCustomSettingsFormHtml() {

        $html = $this->getChildHtml('custom_design_settings_form');
        if(Mage::registry('magic360_custom_design_settings_form')) {
            return $html;
        } else {
            return '';
        }

    }

    public function getSettingsGridHtml() {

        return $this->getChildHtml('settings_grid');

    }

}
