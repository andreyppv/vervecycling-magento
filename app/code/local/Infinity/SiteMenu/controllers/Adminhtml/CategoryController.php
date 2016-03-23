<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {

        $this->loadLayout()
             ->_setActiveMenu( 'infinity/sitemenu/category' )
             ->_addBreadcrumb( Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));

        return $this;

    }

    public function indexAction() {
        
        $this->_initAction()->renderLayout();
        
    }

    public function editAction() {
        
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('sitemenu/category')->load($id);

        if ( $model->getId() || $id == 0 ) {

            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('sitemenu_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('infinity/sitemenu/category');

            $this->_addBreadcrumb( Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Item Manager') );
            $this->_addBreadcrumb( Mage::helper('adminhtml')->__('Category News'), Mage::helper('adminhtml')->__('Item News') );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent( $this->getLayout()->createBlock( 'sitemenu/adminhtml_category_edit' ) )
                    ->_addLeft( $this->getLayout()->createBlock( 'sitemenu/adminhtml_category_edit_tabs' ) );

            $this->renderLayout();
        }
        else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('core')->__('Category does not exist'));
            $this->_redirect('*/*/');
        }

    }

    public function newAction() {
        
        $this->_forward('edit');

    }

    public function saveAction() {

        if ( !$this->_validateFormKey() )
            return $this->_redirect( '*/*/edit' );

        if ( $data = $this->getRequest()->getPost() ) {

            $id = $this->getRequest()->getParam('id');

            $collection = Mage::getModel('sitemenu/category')->getCollection()
                                                             ->addFieldToFilter( 'identify', $data['identify'] );
            if ( $id )
                $collection->addFieldToFilter( 'id', array( 'neq' => $id ) );

            if ( $collection->getSize() > 0 ) {
                Mage::getSingleton('adminhtml/session')->addError( Mage::helper('core')->__('The identify has been used, please try another.') );

                if ( $id )
                    return $this->_redirect( '*/*/edit', array( 'id' => $id ) );
                else
                    return $this->_redirect( '*/*/edit' );
            }

            $model = Mage::getModel('sitemenu/category')->setData( $data )
                                                        ->setId( $id )
                                                        ->setUpdateTime( now() );
            
            if ( $model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL )
                $model->setCreatedTime( now() );

            try {

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper('core')->__('Category was successfully saved') );
                Mage::getSingleton('adminhtml/session')->setFormData( false );

                if ( $this->getRequest()->getParam('back') )
                    return $this->_redirect( '*/*/edit', array( 'id' => $model->getId() ) );
                else
                    return $this->_redirect( '*/*/' );

            }
            catch ( Exception $e ) {

                Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
                Mage::getSingleton('adminhtml/session')->setFormData( $data );

                return $this->_redirect( '*/*/edit', array( 'id' => $this->getRequest()->getParam('id') ) );

            }
        }

        Mage::getSingleton('adminhtml/session')->addError( Mage::helper('core')->__('Unable to find item to save') );
        
        $this->_redirect('*/*/');

    }

    public function deleteAction() {
        
        if ( $this->getRequest()->getParam('id') > 0 ) {
                try {
                        $model = Mage::getModel('sitemenu/category');

                        $model->setId($this->getRequest()->getParam('id'))
                                ->delete();

                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                        $this->_redirect('*/*/');
                } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
        }
        $this->_redirect('*/*/');

    }

    public function switchstatusAction() {

        $id     = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');

        try {
            $model = Mage::getModel( 'sitemenu/category' );
            $model->load( $id )->setStatus( $status )->setIsMassupdate( true )->save();

            Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'core' )->__( 'Status changed.' ) );
        }
        catch ( Exception $e ) {
            Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'core' )->__( 'Unable to switch status.' ) );
        }

        $this->_redirect( '*/*/' );

    }

    public function massDeleteAction() {
        
        $sitemenuIds = $this->getRequest()->getParam('sitemenu_category');

        if ( !is_array( $sitemenuIds ) ) {
            Mage::getSingleton('adminhtml/session')->addError( Mage::helper('adminhtml')->__('Please select item(s)') );
        }
        else {
            try {
                foreach ($sitemenuIds as $sitemenuId) {
                    $sitemenu = Mage::getModel('sitemenu/category')->load($sitemenuId);
                    $sitemenu->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__( 'Total of %d record(s) were successfully deleted', count($sitemenuIds) )
                );
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/index');

    }

    public function massStatusAction() {
        
        $sitemenuIds = $this->getRequest()->getParam('sitemenu_category');

        if ( !is_array( $sitemenuIds ) ) {
            Mage::getSingleton('adminhtml/session')->addError( $this->__('Please select item(s)') );
        }
        else {
            try {
                foreach ( $sitemenuIds as $sitemenuId ) {
                    $sitemenu = Mage::getSingleton('sitemenu/category')->load($sitemenuId)
                                                                       ->setStatus($this->getRequest()->getParam('status'))
                                                                       ->setIsMassupdate(true)
                                                                       ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($sitemenuIds))
                );
            }
            catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        
        $this->_redirect('*/*/index');
        
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        
        die;
        
    }

    protected function _isAllowed() {

        return Mage::getSingleton('admin/session')->isAllowed('infinity/sitemenu/category');

    }
    
}
