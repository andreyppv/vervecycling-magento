<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Model_Item extends Mage_Core_Model_Abstract {

    public function _construct() {

        parent::_construct();
        
        $this->_init('sitemenu/item');
        
    }

    static protected function _space( $number = 0 ) {

        $_strResult = '';
        if ( $number )
            for ( $i = 0; $i < $number; $i++ )
                $_strResult .= '　|-- ';

        return $_strResult;

    }

    static protected function _arrangeMenu( $menus, $current = '', $level = 0 ) {

        $arr_result = array();
        if ( $menus ) {
            foreach ( $menus as $menu ) {
                if ( !$current && $menu['fid'] == 0 ) {
                    $arr_result[] = array(
                        'value' => $menu['id'],
                        'label' => $menu['title'],
                    );
                    $temp = self::_arrangeMenu( $menus, $menu, $level + 1 );
                    if ( $temp ) {
                        foreach ( $temp as $item ) {
                            $arr_result[] = array(
                                'value' => $item['value'],
                                'label' => self::_space( $level + 1 ) . $item['label']
                            );
                        }
                    }
                }
                elseif ( $current && $menu['fid'] && $menu['fid'] == $current['id'] ) {
                    $arr_result[] = array(
                        'value' => $menu['id'],
                        'label' => $menu['title'],
                    );
                }
            }
        }

        return $arr_result;

    }

    static public function getParentList( $storeIds = '', $catalogId = '' ) {

        $menus_rc = Mage::getModel('sitemenu/item')->getResourceCollection();

        $wheres = "`status`=1";
        if ( $storeIds ) {
            $wheres .= " AND ( FIND_IN_SET( 0, `store_id` ) > 0";
            foreach ( $storeIds as $storeId )
                if ( $storeId )
                    $wheres .= " OR FIND_IN_SET( {$storeId}, `store_id` ) > 0";
            $wheres .= ")";
        }

        if ( $catalogId )
             $wheres .= " AND `category`={$catalogId}";

        $menus = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll( $menus_rc->getSelect()->where( $wheres ) );

        $arrResult = self::_arrangeMenu( $menus );

        return $arrResult;

    }
    
    public function getOptionArray( $parent = 0, $level = 0 ) {

        $childen = Mage::getModel('sitemenu/item')->getCollection()->addFieldToFilter( 'fid', $parent );

        $arr = ( $parent == 0 ) ? array( 0 => Mage::helper( 'adminhtml' )->__('[ root ]') ) : array();
        foreach ( $childen as $child ) {
            $arr[ $child->getId() ] = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level ) . $child->getTitle() .' (ID:'. $child->getId() .')';
            $arr = $arr + self::getOptionArray( $child->getId(), $level + 1 );
        }

        return $arr;

    }

    public function getItemValuesForForm( $store_ids, $category_id = 1, $parent = 0, $level = 0 ) {
        
        $items = array();

        $collection = $this->getCollection()
                           ->addFilter( 'category', array( 'eq' => $category_id ) )
                           ->addFilter( 'fid', array( 'eq' => $parent ) )
                           ->addStoreFilter( $store_ids );

        foreach ( $collection as $child )
            $items = array_merge( $items, 
                                  array( array( 'label' => str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level ) . $child->getTitle() .' (ID:'. $child->getId() .')', 'value' => $child->getId() ) ),
                                  $this->getItemValuesForForm( $store_ids, $category_id, $child->getId(), $level + 1 ) );

        return $items;

    }

    public function getProductCategoryValuesForForm( $store_ids ) {

        $store_id = 0;
        if ( count( $store_ids ) == 1 ){
            $allowed_store_ids = array_keys( Mage::app()->getStores() );
            if ( in_array( $store_ids[0], $allowed_store_ids ) )
                $store_id = $store_ids[0];
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
                                                        ->addAttributeToSelect( 'name' )
                                                        ->addFieldToFilter( 'level', array( 'neq' => 0 ) )
                                                        ->setProductStoreId( $store_id )
                                                        ->setStoreId( $store_id );
        
        $base_url = Mage::getBaseUrl();
        $categories = array();
        foreach ( $collection as $category ) {
            $path = explode( '/', $category->getPath() );
            $levels = count( $path );
            $str_cat = '$categories';
            for ( $lv = 0; $lv < $levels; $lv ++ ) {
                if ( $path[ $lv ] ) {
                    $str_cat .= '["children"]['. $path[ $lv ] .']';
                    eval( 'if ( !isset( '. $str_cat .') ) '. $str_cat .' = array( "children" => array() );' );
                }
            }
            eval( $str_cat .'["id"] = "'. str_replace( '"', '\"', $category->getId() ) .'";' );
            eval( $str_cat .'["name"] = "'. str_replace( '"', '\"', $category->getName() ) .'";' );
            eval( $str_cat .'["url"] = "'. str_replace( '"', '\"', substr( $category->getUrl(), strlen( $base_url ) ) ) .'";' );
        }

        return $categories['children'];

    }

}
