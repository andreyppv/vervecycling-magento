<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        // Note that the sitemenu_id refers to the key field in your database table.
        $this->_init('sitemenu/item', 'id');
        
    }

    /**
     * implode stores id with ',' before save to database
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function _beforeSave( Mage_Core_Model_Abstract $object ) {

        $model = Mage::getModel('sitemenu/item');

        // reset idenifier
        $object->setKey( !$object->getKey() ? strtolower ( preg_replace( '/\s+/', '-', preg_replace( '/(?![_ \-])\W+/', '', $object->getTitle() ) ) )
                                            : strtolower ( preg_replace( '/\s+/', '-', preg_replace( '/(?![_ \-])\W+/', '', $object->getKey() ) ) ) );

        // check identifier duplication
        $items = $model->getCollection()->addFieldToFilter( '`category`', array( 'eq' => $object->getCategory() ) )->addFieldToFilter( '`key`', array( 'eq' => $object->getKey() ) );$store_ids = $object->getStoreId();
        // store filter
        if ( !is_array( $store_ids ) )
            $store_ids = explode( ',', $store_ids );
        $items->getSelect()->where( "FIND_IN_SET('" . implode( "',`store_id`) OR FIND_IN_SET('", $store_ids ) . "',`store_id`)" );
        // id filter
        if ( $object->getId() )
            $items->addFieldToFilter( '`id`', array( 'neq' => $object->getId() ) );
        if ( $items->getSize() )
            Mage::throwException( Mage::helper('sitemenu')->__('Identifier is used by another item.') );

        // reset store ID
        if ( is_array( $object->getData('store_id' ) ) )
            $object->setData( 'store_id', implode( ',', $object->getData('store_id') ) );

        // reset customer group IDs
        if ( is_array( $object->getData('customer_group_ids' ) ) )
            $object->setData( 'customer_group_ids', implode( ',', $object->getData('customer_group_ids') ) );

        // reset is default
        if ( $object->getIsDefault() ) {
            $items = $model->getCollection()->addFieldToFilter( 'category', array( 'eq' => $object->getCategory() ) );
            foreach ( $items as $item )
                $item->setIsDefault( 0 )->save();
        }

        // reset empty URL
        $object->setUrl( !$object->getUrl() ? '#' : $object->getUrl() );

        // process URL rewrite
        if ( $object->getUrlRewrite() ) {

            $resource  = Mage::getSingleton('core/resource');
            $read      = $resource->getConnection('core_read');
            $write     = $resource->getConnection('core_write');
            $tempTable = $resource->getTableName('core/url_rewrite');
            $_storeid  = $object->getStoreId();

            $_stores = array();

            if ( $_storeid == 0 ) {
                foreach ( Mage::app()->getStores() as $_store )
                    if ( $_store->getId() )
                        $_stores[] = $_store->getId();
            }
            else {
                is_array( $_storeid ) ? '' : $_stores = explode( ',', $_storeid );
            }

            $wheres = "";
            $i = 0;
            foreach ( $_stores as $_store ) {
                if ( $i )
                    $wheres .= " OR `store_id`=$_store";
                else
                    $wheres .= " `store_id`=$_store";
                $i ++;
            }
            $wheres .= "";

            $select = $read->select()
                    ->from($tempTable, array('count(*) as num'))
                    ->where("`target_path` = ?", $object->getUrlRewrite())
                    ->where($wheres)
                    ->order("store_id");
            $temp = $read->fetchAll($select);

            if ($temp[0]['num']) {
                $deleteWhere = "`target_path` = '" . $object->getUrlRewrite() . "' AND (" . $wheres . ")";
                $write->delete($tempTable, $deleteWhere);
            }

            $bind['target_path'] = $object->getUrlRewrite();
            $bind['request_path'] = $object->getUrl();
            $bind['id_path'] = $object->getUrlRewrite();
            $bind['is_system'] = 0;
            
            foreach ( $_stores as $_store ) {
                $bind['store_id'] = $_store;
                $write->insert($tempTable, $bind);
            }
        }

        return parent::_beforeSave( $object );
        
    }

    /**
     * explode stores to array fomat before show in form
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad( Mage_Core_Model_Abstract $object ) {

        $object->setData( 'store_id', explode( ',', $object->getData('store_id') ) );
        $object->setData( 'customer_group_ids', explode( ',', $object->getData('customer_group_ids') ) );

        return parent::_afterLoad( $object );
        
    }

}
