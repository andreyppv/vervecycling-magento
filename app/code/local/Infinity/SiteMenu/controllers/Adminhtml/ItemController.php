<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Adminhtml_ItemController extends Mage_Adminhtml_Controller_Action {

    protected $_path = array();

    protected function _initAction() {

        $this->loadLayout()
             ->_setActiveMenu( 'infinity/sitemenu/item' )
             ->_addBreadcrumb( Mage::helper('adminhtml')->__( 'Items Manager' ), Mage::helper('adminhtml')->__( 'Item Manager' ) );

        return $this;

    }

    protected function _sendUploadResponse( $fileName, $content, $contentType='application/octet-stream' ) {

        $response = $this->getResponse();

        $response->setHeader( 'HTTP/1.1 200 OK', '' );
        $response->setHeader( 'Pragma', 'public', true );
        $response->setHeader( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
        $response->setHeader( 'Content-Disposition', 'attachment; filename=' . $fileName );
        $response->setHeader( 'Last-Modified', date( 'r' ) );
        $response->setHeader( 'Accept-Ranges', 'bytes' );
        $response->setHeader( 'Content-Length', strlen( $content ) );
        $response->setHeader( 'Content-type', $contentType );

        $response->setBody( $content );

        $response->sendResponse();

        die;

    }

    protected function _pagesToOptionIdArray( $resource ) {

        $res = array();
        $existingIdentifiers = array();
        foreach ( $resource as $item ) {
            $identifier = $item->getData('identifier');
            $res[] = array(
                'value' => $identifier,
                'label' => $item->getData('title')
            );
        }
        return $res;

    }

    protected function _setPath( $id ) {

        $item = Mage::getModel('sitemenu/item')->load( $id );
        array_unshift( $this->_path, $item->getId() );

        if ( $item->getFid() )
            self::_setPath( $item->getFid() );

    }

    public function indexAction() {
        $this->_initAction()->renderLayout();

    }

    public function editAction() {

        $id    = $this->getRequest()->getParam( 'id' );
        $model = Mage::getModel( 'sitemenu/item' )->load( $id );

        if ( $model->getId() || $id == 0 ) {
            $data = Mage::getSingleton( 'adminhtml/session' )->getFormData( true );

            if ( !empty( $data ) ) {
                $model->setData( $data );
            }

            Mage::register( 'sitemenu_data', $model );

            $this->loadLayout();
            $this->_setActiveMenu( 'infinity/sitemenu/items' );

            $this->_addBreadcrumb( Mage::helper( 'adminhtml' )->__( 'Item Manager' ), Mage::helper( 'adminhtml' )->__( 'Item Manager' ) );
            $this->_addBreadcrumb( Mage::helper( 'adminhtml' )->__( 'Item News' ), Mage::helper( 'adminhtml' )->__( 'Item News' ) );

            $this->getLayout()->getBlock( 'head' )->setCanLoadExtJs( true );

            $this->_addContent( $this->getLayout()->createBlock( 'sitemenu/adminhtml_item_edit' ) )
                    ->_addLeft( $this->getLayout()->createBlock( 'sitemenu/adminhtml_item_edit_tabs' ) );

            $this->renderLayout();
        }
        else {
            Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'core' )->__( 'Item does not exist' ) );
            $this->_redirect( '*/*/' );
        }

    }

    public function newAction() {

        $this->_forward( 'edit' );

    }

    public function switchstatusAction() {

        $id     = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');

        try {
            $model = Mage::getModel( 'sitemenu/item' );
            $model->load( $id )->setStatus( $status )->setIsMassupdate( true )->save();

            Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'core' )->__( 'Status changed.' ) );
        }
        catch ( Exception $e ) {
            Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'core' )->__( 'Unable to switch status.' ) );
        }

        $this->_redirect( '*/*/' );

    }

    public function saveAction() {

        if ( !$this->_validateFormKey() )
            return $this->_redirect( '*/*/edit' );

        if ( $data = $this->getRequest()->getPost() ) {
            
            $model = Mage::getModel('sitemenu/item');
            $id    = $this->getRequest()->getParam('id');

            // set default value of some attributes which are empty
            $data['store_id'] = empty( $data['store_id'][0] ) ? array( 0 ) : $data['store_id'];

            /*if ( isset( $_FILES['filename']['name'] ) && $_FILES['filename']['name'] != '' ) {
                try {
                    $uploader = new Varien_File_Uploader( 'filename' );

                    // Any extention would work
                    $uploader->setAllowedExtensions( array( 'jpg', 'jpeg', 'gif', 'png' ) );
                    $uploader->setAllowRenameFiles( false );
                    $uploader->setFilesDispersion( false );

                    // We set media as the upload dir
                    $path = Mage::getBaseDir( 'media' ) . DS;
                    $uploader->save( $path, $_FILES['filename']['name'] );
                }
                catch ( Exception $e ) { }

                // this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }*/

            $model->setData( $data )->setId( $id );

            try {
                // save first in order to get ID of which is a new item
                if ( !$id )
                    $model->save();

                // set path and store data
                array_unshift( $this->_path, $model->getId() );
                if ( $model->getFid() )
                    self::_setPath( $model->getFid() );
                $model->setPath( implode( '/', $this->_path ) )->save();

                Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'core' )->__( 'Item was saved successfully.' ) );
                Mage::getSingleton( 'adminhtml/session' )->setFormData( false );

                if ( $this->getRequest()->getParam( 'back' ) )
                    return $this->_redirect( '*/*/edit', array( 'id' => $model->getId() ) );

                return $this->_redirect( '*/*/' );
            }
            catch ( Exception $e ) {
                Mage::getSingleton( 'adminhtml/session' )->addError( $e->getMessage() );
                Mage::getSingleton( 'adminhtml/session' )->setFormData( $data );

                return $this->_redirect( '*/*/edit', array( 'id' => $id ) );
            }
        }

        Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'sitemenu' )->__( 'Unable to find item.' ) );
        $this->_redirect( '*/*/' );

    }

    public function deleteAction() {

        $id = $this->getRequest()->getParam( 'id' );
        if ( $id > 0 ) {
            try {
                // check if has child
                $collection = Mage::getModel( 'sitemenu/item' )->getCollection()->addFieldToFilter( 'fid', $id );
                if ( $collection->getSize() > 0 ) {
                    Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper('core')->__( 'Child exists, could not delete the item.' ) );
                    return $this->_redirect( '*/*/edit', array( 'id' => $id ) );
                }

                $model = Mage::getModel( 'sitemenu/item' )->setId( $id )->delete();
                Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper( 'adminhtml' )->__( 'Item was successfully deleted' ) );
            }
            catch ( Exception $e ) {
                Mage::getSingleton( 'adminhtml/session' )->addError( $e->getMessage() );
                $this->_redirect( '*/*/edit', array( 'id' => $id ) );
            }
        }

        $this->_redirect( '*/*/' );

    }

    public function massDeleteAction() {

        $sitemenuIds = $this->getRequest()->getParam( 'sitemenu' );

        if ( !is_array( $sitemenuIds ) ) {
            Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper( 'adminhtml' )->__( 'Please select item(s)' ) );
        }
        else {
            try {
                // check if has child
                $collection = Mage::getModel( 'sitemenu/item' )->getCollection()->addFieldToFilter( 'fid', $sitemenuIds );
                if ( $collection->getSize() > 0 ) {
                    Mage::getSingleton( 'adminhtml/session' )->addError( Mage::helper('core')->__( 'Child exists, could not delete the item(s).' ) );
                    return $this->_redirect( '*/*/index' );
                }

                foreach ( $sitemenuIds as $sitemenuId ) {
                    $sitemenu = Mage::getModel( 'sitemenu/item' )->load( $sitemenuId );
                    $sitemenu->delete();
                }
                Mage::getSingleton( 'adminhtml/session' )->addSuccess(
                        Mage::helper( 'adminhtml' )->__(
                                'Total of %d record(s) were successfully deleted', count( $sitemenuIds )
                        )
                );
            }
            catch ( Exception $e ) {
                Mage::getSingleton( 'adminhtml/session' )->addError( $e->getMessage() );
            }
        }

        $this->_redirect( '*/*/index' );

    }

    public function massStatusAction() {

        $sitemenuIds = $this->getRequest()->getParam( 'sitemenu' );

        if ( !is_array( $sitemenuIds ) ) {
            Mage::getSingleton( 'adminhtml/session' )->addError( $this->__( 'Please select item(s)' ) );
        }
        else {
            try {
                foreach ( $sitemenuIds as $sitemenuId ) {
                    $sitemenu = Mage::getSingleton( 'sitemenu/item' )
                            ->load( $sitemenuId )
                            ->setStatus( $this->getRequest()->getParam( 'status' ) )
                            ->setIsMassupdate( true )
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__( 'Total of %d record(s) were successfully updated', count( $sitemenuIds ) )
                );
            } catch ( Exception $e ) {
                $this->_getSession()->addError( $e->getMessage() );
            }
        }

        $this->_redirect( '*/*/index' );

    }

    public function massSortAction() {

        $sort = $this->getRequest()->getParam( 'sort_order' );

        if ( is_array( $sort ) ) {
            try {
                foreach ( $sort as $id => $sort )
                    Mage::getSingleton( 'sitemenu/item' )->load( $id )->setSort( $sort )->setIsMassupdate( true )->save();

                Mage::getSingleton( 'adminhtml/session' )->addSuccess( Mage::helper('core')->__('Order sorted.') );
            }
            catch ( Exception $e ) {
                Mage::getSingleton( 'adminhtml/session' )->addError( $e->getMessage() );
            }
        }

        return $this->_redirect( '*/*/index' );

    }

    public function getItemsAction() {

        $categoryId = $this->getRequest()->getParam('category_id');
        
        $store_ids = $this->getRequest()->getParam('store_id');
        if ( $store_ids )
            $store_ids = explode( ',', $store_ids );

        $items = Infinity_SiteMenu_Model_Item::getParentList( $store_ids, $categoryId );

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode( $items ) );

    }

    public function getProductCategoriesAction() {

        $store_ids = explode( ',', $this->getRequest()->getParam( 'store_id' ) );

        $categories = Mage::getSingleton('sitemenu/item')->getProductCategoryValuesForForm( $store_ids );

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode( $categories ) );

    }

    public function getCmsPagesAction() {

        $store_ids = explode( ',', $this->getRequest()->getParam( 'store_id' ) );

        $pages = Mage::getResourceModel('cms/page_collection');
        if ( count( $store_ids ) > 1 || ( count( $store_ids ) == 1 && $store_ids[0] != 0 ) )
            $pages->addStoreFilter( $store_ids );

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode( self::_pagesToOptionIdArray( $pages ) ) );

    }

    public function exportCsvAction() {

        $fileName = 'sitemenu.csv';
        $content  = $this->getLayout()->createBlock( 'sitemenu/adminhtml_sitemenu_grid' )->getCsv();

        $this->_sendUploadResponse( $fileName, $content );

    }

    public function exportXmlAction() {

        $fileName = 'sitemenu.xml';
        $content  = $this->getLayout()->createBlock( 'sitemenu/adminhtml_sitemenu_grid' )->getXml();

        $this->_sendUploadResponse( $fileName, $content );

    }

    protected function _isAllowed() {

        return Mage::getSingleton('admin/session')->isAllowed('infinity/sitemenu/item');

    }

    public function createForLanAction() {

        $website_id = $this->getRequest()->getParam('website_id');
        $website = Mage::getModel('core/website')->load( $website_id );
        if ( !$website->getId() )
            return false;

        $stores = $website->getStores();
        $def_store_id = $website->getDefaultStore()->getId();

        $store_ids = array();
        foreach ( $stores as $store )
            if ( $store->getId() != $def_store_id )
                $store_ids[] = $store->getId();

        $relations = array();
        $reset_item_ids = array();
        $collection = Mage::getResourceModel('sitemenu/item_collection')->addFieldToFilter( 'store_id', '0' );
        foreach ( $collection as $item ) {
            $data = $item->getData();
            foreach ( $store_ids as $store_id ) {
                // create new item
                $my_data = $data;
                $my_data['store_id'] = $store_id;
                unset( $my_data['id'] );
                try {
                    $my_item = Mage::getModel('sitemenu/item')->setData( $my_data )->save();

                    // get relationship
                    if ( !isset( $relations[ $store_id ] ) )
                        $relations[ $store_id ] = array();
                    $relations[ $store_id ][ $data['id'] ] = $my_item->getId();

                    // store IDs of items which have parent
                    if ( $data['fid'] )
                        $reset_item_ids[] = $my_item->getId();
                }
                catch ( Exception $e ) {
                    Mage::log( $e->getMessages() );
                }
            }
        }

        // reset parent ID of new items
        if ( count( $reset_item_ids ) ) {
            $reset_items = Mage::getResourceModel('sitemenu/item_collection')->addFieldToFilter( 'id', $reset_item_ids );
            foreach ( $reset_items as $reset_item )
                $reset_item->setFid( $relations[ $reset_item->getStoreId() ][ $reset_item->getFid() ] )->save();
        }

        Mage::getSingleton( 'adminhtml/session' )->addSuccess(
            Mage::helper( 'adminhtml' )->__( 'Items for multiple store views created successfully' )
        );

        return $this->_redirect( '*/*/index' );

    }
    
}