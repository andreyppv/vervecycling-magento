<?php

class MagicToolbox_Magic360_Helper_Settings extends Mage_Core_Helper_Abstract {

    static private $_toolCoreClass = null;
    static private $_scrollCoreClass = null;
    static private $_templates = array();
    private $_default_templates = array();
    private $_interface;
    private $_theme;
    //private $_skin;

    public function __construct() {

        $designPackage = Mage::getSingleton('core/design_package');
        $this->_interface = $designPackage->getPackageName();
        $this->_theme = $designPackage->getTheme('template');
        //$this->_skin = $designPackage->getTheme('skin');
        $this->_default_templates = array(
            'product.info.media' => 'catalog'.DS.'product'.DS.'view'.DS.'media.phtml',
            'product_list' => 'catalog'.DS.'product'.DS.'list.phtml',
            'search_result_list' => 'catalog'.DS.'product'.DS.'list.phtml',
            'right.reports.product.viewed' => 'reports'.DS.'product_viewed.phtml',
            'left.reports.product.viewed' => 'reports'.DS.'product_viewed.phtml',
            'home.catalog.product.new' => 'catalog'.DS.'product'.DS.'new.phtml',
        );


    }

    public function getBlockTemplate($blockName, $template) {
        //NOTE: to save original template
        if(!isset(self::$_templates[$blockName])) {
            $block = Mage::app()->getLayout()->getBlock($blockName);
            if($block) {
                self::$_templates[$blockName] = $block->getTemplate();
            }
        }
        return $template;
    }

    public function getTemplateFilename($blockName, $defaultTemplate = '') {
        $template = isset(self::$_templates[$blockName]) ? self::$_templates[$blockName] :
                    (isset($this->_default_templates[$blockName]) ? $this->_default_templates[$blockName] :
                    $defaultTemplate);
        return Mage::getSingleton('core/design_package')->getTemplateFilename($template);
    }

    public function loadTool($profile = '') {

        if(null === self::$_toolCoreClass) {

            $helper = Mage::helper('magic360/params');

            require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/Magic360/core/magic360.module.core.class.php'));
            self::$_toolCoreClass = new Magic360ModuleCoreClass();

            foreach($helper->getDefaultValues() as $block => $params) {
                foreach($params as $id => $value) {
                    self::$_toolCoreClass->params->setValue($id, $value, $block);
                }
            }

            $store = Mage::app()->getStore();
            $website_id = $store->getWebsiteId();
            $group_id = $store->getGroupId();
            $store_id = $store->getId();

            $designPackage = Mage::getSingleton('core/design_package');
            $interface = $designPackage->getPackageName();
            $theme = $designPackage->getTheme('template');

            $where = array(
                0 => array(
                    'id' => 'website_id',
                    'params' => array(' = ?', $website_id),
                    'default' => array(' IS NULL', null),
                ),
                1 => array(
                    'id' => 'group_id',
                    'params' => array(' = ?', $group_id),
                    'default' => array(' IS NULL', null),
                ),
                2 => array(
                    'id' => 'store_id',
                    'params' => array(' = ?', $store_id),
                    'default' => array(' IS NULL', null),
                ),
                3 => array(
                    'id' => 'package',
                    'params' => array(' = ?', $interface),
                    'default' => array(' = ?', ''),
                ),
                4 => array(
                    'id' => 'theme',
                    'params' => array(' = ?', $theme),
                    'default' => array(' = ?', ''),
                ),
            );

            $count = count($where);

            $model = Mage::getModel('magic360/settings');

            do {
                $collection = $model->getCollection();
                $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('value');
                foreach($where as $id => $data) {
                    $collection->getSelect()->where($data['id'].$data['params'][0], $data['params'][1]);
                }
                if($collection->getSize()) {
                    break;
                }
                $collection->getSelect()->reset(Zend_Db_Select::WHERE);
                if(!$count) break;
                $count--;
                $where[$count]['params'] = $where[$count]['default'];
            } while($count >= 0);

            $_params = $collection->getFirstItem()->getValue();
            if(!empty($_params)) {
                $_params = unserialize($_params);
                foreach($_params as $block => $params) {
                    if(is_array($params))
                    foreach($params  as $id => $value) {
                        self::$_toolCoreClass->params->setValue($id, $value, $block);
                    }
                }
            }

            foreach($helper->getBlocks() as $id => $label) {

                /* load locale */
                $locale = $this->__('Magic360_Message');
                if($locale != 'Magic360_Message') {
                    self::$_toolCoreClass->params->setValue('message', $locale, $id);
                }
            }
            //NOTE: spike to save loading-text, fullscreen-loading-text, hint-text, mobile-hint-text params
            self::$_toolCoreClass->params->setValue('loading-text', self::$_toolCoreClass->params->getValue('loading-text', 'product'), self::$_toolCoreClass->params->generalProfile);
            self::$_toolCoreClass->params->setValue('fullscreen-loading-text', self::$_toolCoreClass->params->getValue('fullscreen-loading-text', 'product'), self::$_toolCoreClass->params->generalProfile);
            self::$_toolCoreClass->params->setValue('hint-text', self::$_toolCoreClass->params->getValue('hint-text', 'product'), self::$_toolCoreClass->params->generalProfile);
            self::$_toolCoreClass->params->setValue('mobile-hint-text', self::$_toolCoreClass->params->getValue('mobile-hint-text', 'product'), self::$_toolCoreClass->params->generalProfile);

            //NOTE: copy values to general profile because lang options can be setted in the headers only
            $locale = $this->__('Magic360_LoadingText');
            if($locale != 'Magic360_LoadingText') {
                self::$_toolCoreClass->params->setValue('loading-text', $locale, self::$_toolCoreClass->params->generalProfile);
            }
            $locale = $this->__('Magic360_FullscreenLoadingText');
            if($locale != 'Magic360_FullscreenLoadingText') {
                self::$_toolCoreClass->params->setValue('fullscreen-loading-text', $locale, self::$_toolCoreClass->params->generalProfile);
            }
            $locale = $this->__('Magic360_HintText');
            if($locale != 'Magic360_HintText') {
                self::$_toolCoreClass->params->setValue('hint-text', $locale, self::$_toolCoreClass->params->generalProfile);
            }
            $locale = $this->__('Magic360_MobileHintText');
            if($locale != 'Magic360_MobileHintText') {
                self::$_toolCoreClass->params->setValue('mobile-hint-text', $locale, self::$_toolCoreClass->params->generalProfile);
            }

        }

        if($profile) {
            self::$_toolCoreClass->params->setProfile($profile);
        }

        return self::$_toolCoreClass;
    }

    public function loadScroll() {
        return self::$_scrollCoreClass;
    }

    public function magicToolboxGetSizes($sizeType, $originalSizes = null) {

        $w = self::$_toolCoreClass->params->getValue($sizeType.'-max-width');
        $h = self::$_toolCoreClass->params->getValue($sizeType.'-max-height');
        if(empty($w)) $w = 0;
        if(empty($h)) $h = 0;
        if(self::$_toolCoreClass->params->checkValue('square-images', 'No')) {
            list($w, $h) = self::calculate_size($originalSizes[0], $originalSizes[1], $w, $h);
        } else {
            $h = $w = $h ? ($w ? min($w, $h) : $h) : $w;
        }
        return array($w, $h);
    }

    /*public function magicToolboxResizer($product = null, $watermark = 'image', $type = null, $imageFile = null) {
        if($product == null) return false;

        $subdir = 'image';
        $helper = Mage::helper('catalog/image')->init($product, $subdir, $imageFile);
        if($type !== null) {
            $helper->watermark(Mage::getStoreConfig('design/watermark/' . $watermark . '_image'),
                Mage::getStoreConfig('design/watermark/' . $watermark . '_position'),
                Mage::getStoreConfig('design/watermark/' . $watermark . '_size'),
                Mage::getStoreConfig('design/watermark/' . $watermark . '_imageOpacity'));
        }

        $model = Mage::getModel('catalog/product_image');
        $model->setValueDesctinationSubdir($subdir);
        try {
            if($imageFile == null) {
                $model->setValueBaseFile($product->getData($subdir));
            } else {
                $model->setValueBaseFile($imageFile);
            }
        } catch ( Exception $e ) {
            $img = Mage::getDesign()->getSkinUrl() . $helper->getPlaceholder();
            if($type == null) return $img;
            return array($img, $img);
        }

        $img = $helper->__toString();
        if($type == null) return $img;

        $squareImages = false;
        if(self::$_toolCoreClass) {
            if(self::$_toolCoreClass->params->checkValue('square-images', 'Yes')) {
                $squareImages = true;
            }
        }

        $w = self::$_toolCoreClass->params->getValue($type.'-max-width');
        $h = self::$_toolCoreClass->params->getValue($type.'-max-height');

        if(!$squareImages) {
            $size = getimagesize($model->getBaseFile());
            list($w, $h) = self::calculate_size($size[0], $size[1], $w, $h);
        } else {
            $h = $w = min($w, $h);
        }

        $helper->resize($w, $h);
        $thumb = $helper->__toString();
        return array($img, $thumb);
    }*/

    private function calculate_size($originalW, $originalH, $maxW = 0, $maxH = 0) {
        if(!$maxW && !$maxH) {
            return array($originalW, $originalH);
        } elseif(!$maxW) {
            $maxW = ($maxH * $originalW) / $originalH;
        } elseif(!$maxH) {
            $maxH = ($maxW * $originalH) / $originalW;
        }
        $sizeDepends = $originalW/$originalH;
        $placeHolderDepends = $maxW/$maxH;
        if($sizeDepends > $placeHolderDepends) {
            $newW = $maxW;
            $newH = $originalH * ($maxW / $originalW);
        } else {
            $newW = $originalW * ($maxH / $originalH);
            $newH = $maxH;
        }
        return array(round($newW), round($newH));
    }

    public function isModuleOutputEnabled($moduleName = null) {

        if($moduleName === null) {
            $moduleName = 'MagicToolbox_Magic360';//$this->_getModuleName();
        }
        if(method_exists('Mage_Core_Helper_Abstract', 'isModuleOutputEnabled')) {
            return parent::isModuleOutputEnabled($moduleName);
        }
        //if (!$this->isModuleEnabled($moduleName)) {
        //    return false;
        //}
        if(Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName)) {
            return false;
        }
        return true;
    }

}
