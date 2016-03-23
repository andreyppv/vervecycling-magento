<?php
class Infinity_BackendFields_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getLogoSrcHover(){
    	$src = Mage::getStoreConfig('design/header/logo_src_hover');
	    return $src ? Mage::getDesign()->getSkinUrl($src) : '';
	}
	
    public function getMainBanner(){
    	$src = Mage::getStoreConfig('design/header/mainbanner');
	    return $src ? Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'upload/' . $src : '';
	}
	
    public function getWelcomeTitle(){
    	return Mage::getStoreConfig('design/header/welcome_title');
	}
}