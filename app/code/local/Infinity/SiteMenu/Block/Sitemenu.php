<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Sitemenu extends Mage_Core_Block_Template {

    /**
     * Base URL of the site
     *
     * @var String
     */
    protected $_base_url;

    /**
     * URL of current page
     *
     * @var String
     */
    protected $_cur_url;

    /**
     * Directory path of catalog image
     *
     * @var String
     */
    protected $_c_img_dir;

    /**
     * Store ID of current page
     *
     * @var int
     */
    protected $_store_id;

    /**
     * Data of catalog category
     *
     * @var Object
     */
    protected $_catalog_category_data;

    /**
     * URLs to skip on checking highlight item
     *
     * @var Array
     */
    protected $_skip_urls = array();

    /**
     * Count items in each column
     *
     * @var int
     */
    protected $countItemLimit;

    public function __construct() {
        // init
        $this->_base_url  = $this->getBaseUrl();
        $this->_cur_url   = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->_store_id  = Mage::app()->getStore()->getId();
        $this->_c_img_dir = Mage::getBaseUrl('media') . 'catalog/category/';
        $this->countItemLimit = $this->getColumnItemLimit();

        parent::__construct();

    }

    protected function getColumnItemLimit()
    {
        $columnItemLimit = Mage::getStoreConfig('sitemenu/settings/columnItemLimit', Mage::app()->getStore()->getId());
        // @INFO: In line below we need minus, because in foreach var $key has started count from 0.
//        $columnItemLimit--;
        return $columnItemLimit;
    }

    /**
     * Get menu data
     *
     * @return Object
     */
    protected function _getMenuData() {

        $identify  = $this->getMenuCategory();
        $menu_data = Mage::registry( 'infinity_menu_'. $identify );

        if ( !$menu_data ) {

            // get category ID
            $category = Mage::getModel('sitemenu/category')->getCollection()->addFieldToFilter( 'status', 1 )->addFieldToFilter( 'identify', $identify );
            $category->getSelect()->limit(1);
            foreach ( $category as $cat )
                $cat_id = $cat->getId();

            // get menu collention
            $collection = Mage::getModel('sitemenu/item')->getCollection()
                            ->addVisibleFilter()
                            ->addFieldToFilter( 'category', $cat_id );

            // build menu data as an array from collention
            $items = array( 'children' => array() );
            foreach ( $collection as $item ) {
                $path    = explode( '/', $item->getPath() );
                $levels  = count( $path );
                $str_item = '$items';
                for ( $lv = 0; $lv < $levels; $lv++ ) {
                    if ( $path[ $lv ] ) {
                        $str_item .= '["children"]['. $path[ $lv ] .']';
                        eval( 'if ( !isset( '. $str_item .') ) '. $str_item .' = array( "children" => array() );' );
                    }
                }

                // set attributes
                $attributes = $item->toArray();
                foreach ( $attributes as $key => $value )
                    eval( $str_item . '["' . str_replace( '"', '\"', $key ) . '"] = "' . str_replace( '"', '\"', $value ) . '";' );

                // set catalog data
                eval( 'switch ( '. $str_item .'["is_catalog"] ) { '.
                      'case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_CAT : '. $str_item .'["catalog_children"] = self::_getProductCategoryData( '. $str_item .'["mg_cat_id"] ); break; '.
                      'case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_PRO : '. $str_item .'["catalog_children"] = self::_getProductData( '. $str_item .'["mg_cat_id"] ); break; '.
                      '}' );
            }

            // register menu data for further usage
            Mage::register( 'infinity_menu_'. $identify, $items['children'] );

            return $items['children'];
        }
        else {
            return $menu_data;
        }
        
    }

    /**
     * Get menu HTML of selected category and parent
     *
     * @param int $category_id
     * @param int $parent_id
     * @param int $level
     *
     * @return String
     */
    protected function _getMenuHtml( $items, $level = 1, $show = false, $parent_identifier = NULL ) {

        if ( count( $items ) ) {

            // sort items
            if ( !function_exists('_sortMenuData') ) {
                function _sortMenuData( $a, $b ) { return ( isset( $a['sort'] ) && isset( $b['sort'] ) ) ? ( $a['sort'] == $b['sort'] ? 0 : ( $a['sort'] < $b['sort'] ? -1 : 1 ) ) : 0 ; }
            }
            usort( $items, '_sortMenuData' );

            // check whether show content of the item
            $show = $show || $this->getRootIdentifier() == $parent_identifier;

            $block_items = array();
            $num = count( $items );
            foreach ( $items as $i => $item ) {

                // skip no access items even if children of which are exists
                if ( !isset( $item['id'] ) )
                    continue;

                // get & remove children data
                switch ( $item['is_catalog'] ) {
                    case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_CAT :
                    case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_PRO : $children = $item['catalog_children']; break;
                    default                                             : $children = $item['children']; break;
                }
                unset( $item['children'] );

                // create a record to store datas
                $tmp_item = new Varien_Object;
                $tmp_item->setData( $item );

                // set children
                switch ( $tmp_item->getIsCatalog() ) {
                    case Infinity_SiteMenu_Model_Type::TYPE_NORMAL      : if ( count( $children ) ) $tmp_item->setData( 'children', self::_getMenuHtml( $children, $level + 1, $show, $item['key'] ) ); break;
                    case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_CAT : $tmp_item->setData( 'children', self::_getProductCategoryHtml( $children, $level + 1 ) ); break;
                    case Infinity_SiteMenu_Model_Type::TYPE_CATALOG_PRO : $tmp_item->setData( 'children', self::_getProductHtml( $children, $level + 1 ) ); break;
                }
                
                // add some menu status
                $tmp_item->setData( 'level', $level );
                $tmp_item->setData( 'is_first', ( $i == 0 ) );
                $tmp_item->setData( 'is_last', ( $i == $num - 1 ) );
                $tmp_item->setData( 'is_current', self::_getIsCurrent( $tmp_item ) );
                $tmp_item->setData( 'url', self::_resetUrl( $tmp_item->getUrl() ) );
                $tmp_item->setData( 'has_child', count( $children ) ? true : false );

                // add the record to collection
                $block_items[] = $tmp_item;
            }

            // add record collection to block
            $block = $this->getLayout()->createBlock('sitemenu/item');
            $block->setData( 'level', $level )->setData( 'items', $block_items )->setData( 'show', ( !$this->getRootIdentifier() || $show  ) );

            return $block->toHtml();
        }
        else {
            return '';
        }

    }

    /**
     * Get catalog category data
     *
     * @param int $level
     *
     * @return Array
     */
    protected function _getProductCategoryData( $catid ) {

        // get category collection
        $model = Mage::getModel('catalog/category');
        $selected_path = $model->load( $catid )->getPath();
        $collection = $model->getCollection()
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('image')
                            ->addAttributeToSelect('thumbnail')
                            ->addAttributeToSelect('description')
                            ->addAttributeToSelect('is_active')
                            ->addFieldToFilter( 'is_active', 1 )
                            ->addPathFilter( '^'. $selected_path .'/' )
                            ->setProductStoreId( $this->_store_id )
                            ->setStoreId( $this->_store_id );

        $version = Mage::getVersionInfo();
        if ( $version['minor'] >= 5 )
            $collection->addAttributeToSelect('include_in_menu')->addFieldToFilter( 'include_in_menu', 1 );

        $categories = array( 'children' => array() );

        if ( $collection->getSize() ) {
            $start_lv = count( explode( '/', $selected_path ) );
            // switch collection to array tree with the `path` attribute
            foreach ( $collection as $category ) {
                $tmp_url = substr( $category->getUrl(), strlen($this->_base_url ) );
                $path    = explode( '/', $category->getPath() );
                $levels  = count( $path );
                $str_cat = '$categories';
                for ( $lv = $start_lv; $lv < $levels; $lv++ ) {
                    if ( $path[ $lv ] ) {
                        $str_cat .= '["children"]['. $path[$lv] .']';
                        eval( 'if ( !isset( '. $str_cat .') ) '. $str_cat .' = array( "children" => array() );' );
                    }
                }
                $attributes = $category->toArray();
                foreach ( $attributes as $key => $value )
                    eval( $str_cat .'["'. str_replace( '"', '\"', $key ) .'"] = "'. str_replace( '"', '\"', $value ) .'";' );
                eval( $str_cat .'["url"] = "'. str_replace( '"', '\"', $tmp_url ) .'";' );
                $this->_skip_urls[] = $tmp_url; // Add URL to the `To Skip Array`
            }
        }

        // return category data
        return $categories['children'];
        
    }

    /**
     * Get catalog category HTML
     *
     * @param Array $categories
     * @param int $level
     *
     * @return String
     */
    protected function _getProductCategoryHtml( $categories, $level ) {

        if ( count( $categories ) ) {

            // sort items
            if ( !function_exists('_sortCategoryData') ) {
                function _sortCategoryData( $a, $b ) { return $a['position'] == $b['position'] ? 0 : ( $a['position'] < $b['position'] ? -1 : 1 ); }
            }
            usort( $categories, '_sortCategoryData' );

            $block_items = array();
            $num = count( $categories );
            foreach ( $categories as $i => $item ) {

                // get & remove children data
                $children = $item['children'];
                unset( $item['children'] );

                // create a record to store datas
                $tmp_item = new Varien_Object;
                $tmp_item->setData( $item );

                // add some menu status
                $tmp_item->setData( 'level', $level );
                $tmp_item->setData( 'is_first', ( $i == 0 ) );
                $tmp_item->setData( 'is_last', ( $i == $num - 1 ) );
                $tmp_item->setData( 'is_current', self::_getIsCurrent( $tmp_item ) );
                $tmp_item->setData( 'url', self::_resetUrl( $tmp_item->getUrl() ) );
                $tmp_item->setData( 'has_child', count( $children ) ? true : false );

                // set child html
                if ( count( $children ) )
                    $tmp_item->setData( 'children', self::_getProductCategoryHtml( $children, $level + 1 ) );

                // add the record to collection
                $block_items[] = $tmp_item;
            }

            // add record collection to block
            $block = $this->getLayout()->createBlock('sitemenu/item_catalog_category');
            $block->setData( 'level', $level )->setData( 'items', $block_items );
            if ( $this->getProCatTpl() )
                $block->setTemplate( $this->getProCatTpl() );

            return $block->toHtml();
        }
        else {
            return '';
        }

    }

    /**
     * Get product data
     *
     * @param int $catid
     *
     * @return Array
     */
    protected function _getProductData( $catid ) {

        $products = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('name')
                        ->addUrlRewrite( $catid )
                        ->addCategoryFilter( Mage::getModel('catalog/category')->load( $catid ) )
                        ->setOrder( 'name', 'asc' );

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection( $products );

        return $products;

    }

    /**
     * Get product HTML
     *
     * @param Array $products
     * @param int $level
     *
     * @return String
     */
    protected function _getProductHtml( $products, $level ) {

        $num = count( $products );
        if ( $num > 0 ) {
            $i = 0;
            $block_items = array();
            foreach ( $products as $product ) {

                // create object to contain data
                $tmp_item = new Varien_Object;
                $tmp_item->setData( $product->toArray() );

                // add some menu status
                $tmp_item->setData( 'level', $level );
                $tmp_item->setData( 'url', $product->getRequestPath() );
                $tmp_item->setData( 'product_url', $product->getProductUrl() );
                $tmp_item->setData( 'is_first', ( $i == 0 ) );
                $tmp_item->setData( 'is_last', ( $i == $num - 1 ) );
                $tmp_item->setData( 'is_current', self::_getIsCurrent( $tmp_item ) );

                $block_items[] = $tmp_item;

                $i ++;
            }

            // add record collection to block
            $block = $this->getLayout()->createBlock('sitemenu/item_catalog_product');
            $block->setData( 'level', $level )->setData( 'items', $block_items );
            if ( $this->getProTpl() )
                $block->setTemplate( $this->getProTpl() );

            return $block->toHtml();
        }
        else {
            return '';
        }

    }

    /**
     * Check if the given item is link to current page
     *
     * @param Object $item
     *
     * @return Boolean
     */
    protected function _getIsCurrent( $item ) {

        $cur_params = preg_replace( '/^index\.php/Ui', '', substr( $this->_cur_url, strlen( preg_replace( '/http(?:s)?:\/\//Ui', '', $this->_base_url ) ) ) );
        $cur_params = trim( preg_replace( '/(.*)(?:\?.*)$/Ui', '$1', $cur_params ), '/' );
		
		$item_url = trim( preg_replace( '/(.*)(?:\?.*)$/Ui', '$1', $item->getUrl() ), '/' );

        // item URL is same with the current one
        if ( $cur_params == $item_url ) {
            return true;
        }
        // current URL is extended to the item URL, and the item is not a catalog item
        elseif ( @preg_match( '/^'. $item_url .'\/.+/', $cur_params ) && !in_array( $item_url, $this->_skip_urls ) ) {
            return true;
        }
        // default item on home page
        elseif ( $cur_params == '' && $item->getIsDefault() ) {
            return true;
        }
        else {
            // on product detail page
            $item_url = str_replace( array( '.html', '.htm' ), '', $item_url );
            if ( @preg_match( '/^'. $item_url .'\/.+/', $cur_params ) )
                return true;

            // others
            return false;
        }
        
    }

    /**
     * Return a well format URL
     *
     * @param String $url
     *
     * @return String
     */
    protected function _resetUrl($url) {

        //return preg_match('/^http(?:s)?:\/\//Ui', $url) ? $url : ( preg_match('/^#\S+/Ui', $url) ? $url : ( $url == '#' ? 'javascript:;' : $this->_base_url . trim( $url, '/' ) ) );
        return preg_match('/^http(?:s)?:\/\//Ui', $url) ? $url : ( preg_match('/^#\S+/Ui', $url) ? $url : ( $url == '#' ? 'javascript:;' : str_replace('store/','',$this->_base_url) . trim( $url, '/' ).'/' ) );
        
    }

    /**
     * Return the whole menu HTML
     *
     * @return String
     */
    public function getMenu() {

        return self::_getMenuHtml( $this->_getMenuData() );

    }

    public function getCountItemLimit()
    {
        return $this->countItemLimit;
    }
}
