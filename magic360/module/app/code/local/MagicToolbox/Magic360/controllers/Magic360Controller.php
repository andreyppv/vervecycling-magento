<?php

class MagicToolbox_Magic360_Magic360Controller extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {

        $this->_title($this->__('Magic 360&#8482; Settings'));
        $this->loadLayout()->_setActiveMenu('magictoolbox/magic360')->renderLayout();

    }

    public function addAction() {

        if($data = $this->getRequest()->getPost()) {

            if(empty($data['store_views']) && empty($data['design'])) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magic360')->__('You already have default settings!'));
                $this->_redirect('*/*/');
                return;
            }

            if(empty($data['store_views'])) $data['store_views'] = '';
            list($website_id, $group_id, $store_id) = explode('/', $data['store_views']);
            if(empty($data['design'])) $data['design'] = '';
            list($package, $theme) = explode('/', $data['design']);

            $model = Mage::getModel('magic360/settings');
            $collection = $model->getCollection();


            $collection->getSelect()->/*columns('custom_settings_title')->*/
                where(empty($website_id) ? 'website_id IS NULL' : 'website_id = ?', empty($website_id) ? null : (int)$website_id)->
                where(empty($group_id)   ? 'group_id IS NULL'   : 'group_id = ?',   empty($group_id)   ? null : (int)$group_id)->
                where(empty($store_id)   ? 'store_id IS NULL'   : 'store_id = ?',   empty($store_id)   ? null : (int)$store_id)->
                where('package = ?', empty($package) ? '' : $package)->
                where('theme = ?', empty($theme) ? '' : $theme);

            if($collection->getSize()) {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magic360')->__('The settings already exists!'));
                $this->_redirect('*/*/');
                return;
            }

            $custom_settings_title = array();

            if(empty($data['store_views'])) {
                $custom_settings_title[] = 'All Store Views';
            } else {
                if(!empty($website_id)) {
                    $model->setWebsite_id($website_id);
                    $website = Mage::app()->getWebsite($website_id);
                    $custom_settings_title[] = $website->getName();
                }
                if(!empty($group_id)) {
                    $model->setGroup_id($group_id);
                    $group = $website->getGroups();
                    $group = $group[$group_id];
                    if(!$group instanceof Mage_Core_Model_Store_Group) {
                        $group = Mage::app()->getGroup($group);
                    }
                    $custom_settings_title[] = $group->getName();
                }
                if(!empty($store_id)) {
                    $model->setStore_id($store_id);
                    $store = $group->getStores();
                    $store = $store[$store_id];
                    $custom_settings_title[] = $store->getName();
                }
            }

            if(empty($data['design'])) {
                $custom_settings_title[] = 'All Designs';
            } else {
                if(empty($theme)) {
                    $model->setPackage($package);
                    $custom_settings_title[] = $package.' package';
                } else {
                    $model->setPackage($package);
                    $model->setTheme($theme);
                    $custom_settings_title[] = $package.'/'.$theme.' theme';
                }
            }

            $custom_settings_title = 'Settings for '.implode(' => ', $custom_settings_title);
            $model->setCustom_settings_title($custom_settings_title);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magic360')->__('Settings was successfully added'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
            } catch(Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
            }

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magic360')->__('Unable to add settings'));
        }
        $this->_redirect('*/*/');

    }

    public function deleteAction() {

        $id = $this->getRequest()->getParam('id');
        if($id > 0) {
            try {
                $model = Mage::getModel('magic360/settings')->load($id);
                $isDefaultSettings =
                    $model->getWebsite_id() == NULL &&
                    $model->getGroup_id() == NULL &&
                    $model->getStore_id() == NULL &&
                    $model->getPackage() == '' &&
                    $model->getTheme() == '';
                if($isDefaultSettings) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('You can not delete default settings!'));
                } else {
                    $model->delete();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Settings was successfully deleted'));
                }
            } catch(Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');

    }

    public function massDeleteAction() {

        $ids = $this->getRequest()->getParam('massactionId');
        $alert = 0;
        if(is_array($ids)) {
            try {
                foreach($ids as $id) {
                    $model = Mage::getModel('magic360/settings')->load($id);
                    $isDefaultSettings =
                        $model->getWebsite_id() == NULL &&
                        $model->getGroup_id() == NULL &&
                        $model->getStore_id() == NULL &&
                        $model->getPackage() == '' &&
                        $model->getTheme() == '';
                    if($isDefaultSettings) {
                        $alert = 1;
                    } else {
                        $model->delete();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d row(s) were successfully deleted', count($ids)-$alert)
                );
                if($alert) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('You can not delete default settings!')
                    );
                }
            } catch(Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select rows'));
        }
        $this->_redirect('*/*/');

    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('magic360/settings')->load($id);
        if($model->getId()) {
            Mage::register('magic360_model_data', $model);
            $this->_title($this->__('Magic 360&#8482; Settings'));
            $this->loadLayout();
            $this->_setActiveMenu('magictoolbox/magic360');
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magic360')->__('Settings does not exist'));
            $this->_redirect('*/*/');
        }

    }

    public function saveAction() {

        if($params = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('magic360/settings');


            $data = array();
            $data['value'] = serialize($params['magic360']);
            $data['last_edit_time'] = now();
            $model->setData($data)->setId($id);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('magic360')->__('Settings was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id'        => $id,
                        '_current'  => true,
                        'back'      => null
                    ));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch(Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($params);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magic360')->__('Unable to find settings to save'));
        $this->_redirect('*/*/');

    }

    public function validateAction() {

        $response = new Varien_Object();
        $response->setError(false);
        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             */
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage($e->getMessage());
        }
        catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());

    }

}
