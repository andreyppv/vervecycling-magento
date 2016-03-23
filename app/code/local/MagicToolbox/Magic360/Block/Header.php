<?php

class MagicToolbox_Magic360_Block_Header extends Mage_Core_Block_Template {

    protected $pageType = '';

    public function _construct() {
        $this->setTemplate('magic360/header.phtml');
    }

    public function setPageType($pageType = '') {
        $this->pageType = $pageType;
    }

    public function getPageType() {
        return $this->pageType;
    }

}
