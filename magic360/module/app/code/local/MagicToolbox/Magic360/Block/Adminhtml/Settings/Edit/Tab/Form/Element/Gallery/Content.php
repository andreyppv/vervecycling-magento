<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings_Edit_Tab_Form_Element_Gallery_Content extends Mage_Adminhtml_Block_Widget {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('magic360/gallery.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('uploader',
            $this->getLayout()->createBlock('adminhtml/media_uploader')
        );

        $this->getUploader()->getConfig()
            ->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/magic360_gallery/upload'))
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                )
            ));

        return parent::_prepareLayout();
    }

    public function getUploader() {
        return $this->getChild('uploader');
    }

    public function getUploaderHtml() {
        $html = $this->getChildHtml('uploader');
        //cut some script files and scripts that already included
        $html = preg_replace('/<script[^>]*?(flex|flexuploader|FABridge)\.js[^>]*><\/script>/', '', $html);
        $html = preg_replace('/<script[^>]*>[^<]*?Translator[^<]*<\/script>/', '', $html);
        return $html;
    }

    public function getJsObjectName() {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getImagesJson() {
        $id = Mage::registry('current_product')->getId();
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $table = $resource->getTableName('magic360/gallery');
        $result = $connection->query("SELECT gallery FROM {$table} WHERE product_id = {$id}");
        if($result) {
            $rows = $result->fetch(PDO::FETCH_ASSOC);
            if($rows) {
               return $rows['gallery'];
            }
        }
        return '[]';
    }

    public function getMagic360MediaUrl($file) {
        $file = str_replace(DS, '/', $file);
        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }
        return Mage::getBaseUrl('media').'magictoolbox/magic360/'.$file;
    }

}

