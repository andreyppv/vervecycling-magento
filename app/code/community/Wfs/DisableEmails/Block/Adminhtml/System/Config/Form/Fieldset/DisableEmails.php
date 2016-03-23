<?php
/**
 * WebFlakeStudio
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webflakestudio.com/WFS-LICENSE-COMMUNITY.txt
 *
 *
 * MAGENTO EDITION USAGE NOTICE
 *
 * This package designed for Magento COMMUNITY edition
 * WebFlakeStudio does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * WebFlakeStudio does not provide extension support in case of
 * incorrect edition usage.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   Wfs
 * @package    Wfs_DisableEmails
 * @copyright  Copyright (c) 2012 WebFlakeStudio (http://webflakestudio.com)
 * @license    http://webflakestudio.com/WFS-LICENSE-COMMUNITY.txt
 */
class Wfs_DisableEmails_Block_Adminhtml_System_Config_Form_Fieldset_DisableEmails
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    /**
     * Render form element
     *
     * @see Mage_Adminhtml_Block_System_Config_Form_Fieldset::render()
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $emails = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
        foreach ($emails as $notificationType) {
            if ($notificationType['value']) {
                $html.= $this->_getFieldHtml($element, $notificationType);
            }
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Get dummy element
     *
     * @return Varien_Object
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Varien_Object(
                array('show_in_default' => 1, 'show_in_website' => 1)
            );
        }
        return $this->_dummyElement;
    }

    /**
     * Get field renderer object
     *
     * @return Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    /**
     * Get form element values
     *
     * @return array
     */
    protected function _getValues()
    {
        if (empty($this->_values)) {
            $this->_values = array(
                array('label' => Mage::helper('wfs_disable_emails')->__('On'), 'value' => 0),
                array('label' => Mage::helper('wfs_disable_emails')->__('Off'), 'value' => 1)
            );
        }
        return $this->_values;
    }

    /**
     * Get field html
     *
     * @param Varien_Data_Form_Element_Abstract $fieldset
     * @param string $notificationType
     * @return string
     */
    protected function _getFieldHtml($fieldset, $notificationType)
    {
        $configData = $this->getConfigData();
        $path = Wfs_DisableEmails_Model_Email_Template::XML_PATH_PREFIX . $notificationType['value'];
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (int)(string)$this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }

        $e = $this->_getDummyElement();
        $field = $fieldset->addField('notify_' . $notificationType['value'], 'select',
            array(
                'name'          => 'groups[wfs_disable_emails][fields][' . $notificationType['value'] . '][value]',
                'label'         => $notificationType['label'],
                'value'         => $data,
                'values'        => $this->_getValues(),
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
