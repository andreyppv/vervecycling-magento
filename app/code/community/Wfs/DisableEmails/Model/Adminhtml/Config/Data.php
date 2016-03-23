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
 * @copyright  Copyright (c) 2013 WebFlakeStudio (http://webflakestudio.com)
 * @license    http://webflakestudio.com/WFS-LICENSE-COMMUNITY.txt
 */
class Wfs_DisableEmails_Model_Adminhtml_Config_Data extends Mage_Adminhtml_Model_Config_Data
{
    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    public function save()
    {
        $this->_validate();
        $this->_getScope();

        Mage::dispatchEvent('model_config_data_save_before', array('object' => $this));

        $section = $this->getSection();
        $website = $this->getWebsite();
        $store   = $this->getStore();
        $groups  = $this->getGroups();
        $scope   = $this->getScope();
        $scopeId = $this->getScopeId();

        if (empty($groups)) {
            return $this;
        }

        $sections = Mage::getModel('adminhtml/config')->getSections();
        /* @var $sections Mage_Core_Model_Config_Element */

        $oldConfig = $this->_getConfig(true);

        $deleteTransaction = Mage::getModel('core/resource_transaction');
        /* @var $deleteTransaction Mage_Core_Model_Resource_Transaction */
        $saveTransaction = Mage::getModel('core/resource_transaction');
        /* @var $saveTransaction Mage_Core_Model_Resource_Transaction */

        // Extends for old config data
        $oldConfigAdditionalGroups = array();

        foreach ($groups as $group => $groupData) {
            /**
             * Map field names if they were cloned
             */
            $groupConfig = $sections->descend($section.'/groups/'.$group);

            if ($clonedFields = !empty($groupConfig->clone_fields)) {
                if ($groupConfig->clone_model) {
                    $cloneModel = Mage::getModel((string)$groupConfig->clone_model);
                } else {
                    Mage::throwException('Config form fieldset clone model required to be able to clone fields');
                }
                $mappedFields = array();
                $fieldsConfig = $sections->descend($section.'/groups/'.$group.'/fields');

                if ($fieldsConfig->hasChildren()) {
                    foreach ($fieldsConfig->children() as $field => $node) {
                        foreach ($cloneModel->getPrefixes() as $prefix) {
                            $mappedFields[$prefix['field'].(string)$field] = (string)$field;
                        }
                    }
                }
            }
            // set value for group field entry by fieldname
            // use extra memory
            $fieldsetData = array();
            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldsetData[$field] = (is_array($fieldData) && isset($fieldData['value']))
                    ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $field => $fieldData) {
                $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/' . $field);
                if (!$fieldConfig && $clonedFields && isset($mappedFields[$field])) {
                    $fieldConfig = $sections->descend($section . '/groups/' . $group . '/fields/'
                        . $mappedFields[$field]);
                }
                if (!$fieldConfig) {
                    $node = $sections->xpath($section .'//' . $group . '[@type="group"]/fields/' . $field);
                    if ($node) {
                        $fieldConfig = $node[0];
                    }
                }

                /**
                 * Get field backend model
                 * Fix core issue here, because not all fields has 'backend_model' property
                 */
                if (!empty($fieldConfig->backend_model)) {
                    $backendClass = $fieldConfig->backend_model;
                } else {
                    $backendClass = 'core/config_data';
                }

                /** @var $dataObject Mage_Core_Model_Config_Data */
                $dataObject = Mage::getModel($backendClass);
                if (!$dataObject instanceof Mage_Core_Model_Config_Data) {
                    Mage::throwException('Invalid config field backend model: '.$backendClass);
                }

                $dataObject
                    ->setField($field)
                    ->setGroups($groups)
                    ->setGroupId($group)
                    ->setStoreCode($store)
                    ->setWebsiteCode($website)
                    ->setScope($scope)
                    ->setScopeId($scopeId)
                    ->setFieldConfig($fieldConfig)
                    ->setFieldsetData($fieldsetData)
                ;

                if (!isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                $path = $section . '/' . $group . '/' . $field;

                /**
                 * Look for custom defined field path
                 */
                if (is_object($fieldConfig)) {
                    $configPath = (string)$fieldConfig->config_path;
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $groupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if (!isset($oldConfigAdditionalGroups[$groupPath])) {
                            $oldConfig = $this->extendConfig($groupPath, true, $oldConfig);
                            $oldConfigAdditionalGroups[$groupPath] = true;
                        }
                        $path = $configPath;
                    }
                }

                $inherit = !empty($fieldData['inherit']);

                $dataObject->setPath($path)
                    ->setValue($fieldData['value']);

                if (isset($oldConfig[$path])) {
                    $dataObject->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($dataObject);
                    }
                    else {
                        $deleteTransaction->addObject($dataObject);
                    }
                }
                elseif (!$inherit) {
                    $dataObject->unsConfigId();
                    $saveTransaction->addObject($dataObject);
                }
            }
        }

        $deleteTransaction->delete();
        $saveTransaction->save();

        return $this;
    }
}
