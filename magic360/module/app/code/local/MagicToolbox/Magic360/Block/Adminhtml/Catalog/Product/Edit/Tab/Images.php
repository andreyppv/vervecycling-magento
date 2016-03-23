<?php

class MagicToolbox_Magic360_Block_Adminhtml_Catalog_Product_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('group_fields_magic360_images', array('legend' => Mage::helper('magic360')->__('Images'), 'class' => 'magic360Fieldset'));
        $multiRows = false;
        $columnsNumber = 0;
        $rowsNumber = 1;
        $imagesCount = 0;
        $id = Mage::registry('current_product')->getId();
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');
        $table = $resource->getTableName('magic360/gallery');
        $result = $connection->query("SELECT columns, gallery FROM {$table} WHERE product_id = {$id}");
        if($result) {
            $rows = $result->fetch(PDO::FETCH_ASSOC);
            if($rows) {
                $columnsNumber = $rows['columns'];
                $images = Mage::helper('core')->jsonDecode($rows['gallery']);
                foreach($images as $image) {
                    if($image['disabled']) continue;
                    $imagesCount++;
                }
            }
        }
        if($imagesCount != $columnsNumber) {
            $multiRows = true;
            $rowsNumber = floor($imagesCount/$columnsNumber);
        }
        $fieldset->addField('magic360_multi_rows', 'checkbox', array(
            'label'     => Mage::helper('magic360')->__('Multi-row spin'),
            'name'      => 'magic360[multi_rows]',
            'note'      => '',
            'value'     => $multiRows,
            'checked'   => $multiRows,
            'onclick'   => '$(\'magic360_columns\').disabled = $(\'magic360_rows\').disabled = !$(\'magic360_multi_rows\').checked;'
        ));
        $fieldset->addField('magic360_columns', 'text', array(
            'label'     => Mage::helper('magic360')->__('Number of images on X-axis'),
            'name'      => 'magic360[columns]',
            'note'      => '',
            'value'     => $columnsNumber,
            'disabled'  => !$multiRows
        ));
        $fieldset->addField('magic360_rows', 'text', array(
            'label'     => Mage::helper('magic360')->__('Number of images on Y-axis'),
            'name'      => 'magic360[rows]',
            'note'      => '',
            'value'     => $rowsNumber,
            'disabled'  => !$multiRows
        ));
        $fieldset->addType('magic360_gallery', 'MagicToolbox_Magic360_Block_Adminhtml_Settings_Edit_Tab_Form_Element_Gallery');
        $fieldset->addField('magic360_gallery', 'magic360_gallery', array(
            'label'     => Mage::helper('magic360')->__('${too.id} gallery'),
            'name'      => 'magic360[gallery]',
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel() {
        return $this->__('Magic 360&#8482; Images');
    }

    public function getTabTitle() {
        return $this->__('Magic 360&#8482; Images');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    public function getHtmlId() {
        return $this->getId();
    }

    public function getJsObjectName() {
        return $this->getHtmlId().'JsObject';
    }

}

