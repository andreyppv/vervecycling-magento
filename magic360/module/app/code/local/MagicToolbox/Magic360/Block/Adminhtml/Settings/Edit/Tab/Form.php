<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $blockId = preg_replace('/^magic360_|_settings_block$/is', '', $this->getNameInLayout());

        $helper = Mage::helper('magic360/params');

        $tool = Mage::registry('magic360_core_class');

        if($tool === null) {

            //require_once(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..'.DS.'core'.DS.'magic360.module.core.class.php');
            require_once(BP . str_replace('/', DS, '/app/code/local/MagicToolbox/Magic360/core/magic360.module.core.class.php'));
            $tool = new Magic360ModuleCoreClass();

            foreach($helper->getDefaultValues() as $block => $params) {
                foreach($params as $id => $value) {
                    $tool->params->setValue($id, $value, $block);
                }
            }

            $model = Mage::registry('magic360_model_data');
            $data = $model->getData();
            if($data['value']) {
                $_params = unserialize($data['value']);
                foreach($_params as $block => $params) {
                    if(is_array($params))
                    foreach($params  as $id => $value) {
                        $tool->params->setValue($id, $value, $block);
                    }
                }
            }

            Mage::register('magic360_core_class', $tool);

        }

        $form = new Varien_Data_Form();
        //$form->setHtmlIdPrefix('_general');
        $this->setForm($form);

        $gId = 0;
        foreach($helper->getParamsMap($blockId) as $group => $ids) {
            $fieldset = $form->addFieldset($blockId.'_group_fieldset_'.$gId++, array('legend' => Mage::helper('magic360')->__($group), 'class' => 'magic360Fieldset'));
            foreach($ids as $id) {
                $config = array(
                    'label'     => Mage::helper('magic360')->__($tool->params->getLabel($id, $blockId)),
                    'name'      => 'magic360['.$blockId.']['.$id.']',
                    'note'      => '',
                    'value'     => $tool->params->getValue($id, $blockId),
                    //'class'     => 'required-entry',
                    //'required'  => true,
                );
                $description = $tool->params->getDescription($id, $blockId);
                if($description) {
                    $config['note'] = $description;
                }
                $type = $tool->params->getType($id, $blockId);
                $values = $tool->params->getValues($id, $blockId);
                if($type != 'array' && $tool->params->valuesExists($id, $blockId, false)) {
                    if(!empty($config['note'])) $config['note'] .= "<br />";
                    $config['note'] .= "(allowed values: ".implode(", ", $values).")";
                }
                switch($type) {
                    case 'num':
                        $type = 'text';
                    case 'text':
                        break;
                    case 'array':
                        //switch($tool->params->getSubType($id, $tool->params->generalProfile)) {
                        switch($tool->params->getSubType($id, $blockId)) {
                            case 'select':
                                if($id == 'template') {
                                    $type = 'select';
                                    break;
                                }
                            case 'radio':
                                $type = 'radios';
                                $config['style'] = 'margin-right: 5px;';
                                break;
                            default:
                                $type = 'text';
                        }
                        $config['values'] = array();
                        foreach($values as $v) {
                            $config['values'][] = array('value'=>$v, 'label'=>$v);
                        }
                        break;
                    default:
                        $type = 'text';
                }
                $fieldset->addField($blockId.'-'.$id, $type, $config);
            }
        }

        return parent::_prepareForm();

    }

}