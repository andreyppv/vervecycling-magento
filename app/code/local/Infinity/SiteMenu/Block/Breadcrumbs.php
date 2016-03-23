<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2013 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Bruce.z
 */
class Infinity_SiteMenu_Block_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs {

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
     * Store ID of current page
     *
     * @var int
     */
    protected $_store_id;
    

    public function __construct() {

        // init
        $this->_base_url = $this->getBaseUrl();
        $this->_cur_url  = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $this->_store_id = Mage::app()->getStore()->getId();

        parent::__construct();
        
    }

    /**
     * Return a well format URL
     *
     * @param String $url
     *
     * @return String
     */
    protected function _resetUrl( $url ) {

        return preg_match( '/^http(?:s)?:\/\//Ui', $url) ? $url : ( preg_match('/^#\S+/Ui', $url) ? $url : ( $url == '#' ? 'javascript:;' : $this->_base_url . trim( $url, '/' ) ) );

    }

    protected function _addCrumbs( $item ) {

        $this->_crumbs = NULL;
        $this->addCrumb( 'home', array(
            'label' => Mage::helper('sitemenu')->__('Home'),
            'title' => Mage::helper('sitemenu')->__('Home'),
            'link'  => Mage::getBaseUrl()
        ) );
        $path = explode( '/', $item->getPath() );
        foreach ( $path as $k => $item_id ) {
            $item = Mage::getModel('sitemenu/item')->load( $item_id );
            if ( !$item->getIsDefault() ) {
                $this->addCrumb( $item->getKey(), array(
                    'label' => $item->getTitle(),
                    'title' => $item->getTitle(),
                    'link'  => self::_resetUrl( $item->getUrl() )
                ) );
            }
        }
        
    }

    protected function _clearLastLink() {

        $total = count( $this->_crumbs );
        if ( $total > 0 ) {
            $last_key = array_keys( $this->_crumbs, end( $this->_crumbs ) );
            $this->_crumbs[ $last_key[0] ]['link'] = NULL;
        }

    }

    protected function _toHtml() {

        $cur_params = preg_replace( '/^index\.php/Ui', '', substr( $this->_cur_url, strlen( preg_replace( '/http(?:s)?:\/\//Ui', '', $this->_base_url ) ) ) );
        $cur_params = trim( preg_replace( '/(.*)(?:\?.*)$/Ui', '$1', $cur_params ), '/' );

        $item = Mage::getResourceModel('sitemenu/item_collection')
                    ->addVisibleFilter()
                    ->addFieldToFilter( 'url', array( $cur_params, $cur_params.'/' ) )
                    ->setOrder( 'weight', 'asc' )
                    ->getFirstItem();

        if ( $item->getId() ) {
            // reset crumbs if current page URL is in site menu
            $this->_addCrumbs( $item );

            // clean link of last item
            $this->_clearLastLink();
        }
        else {
            $current_category = Mage::registry('current_category');
            $current_product  = Mage::registry('current_product');

            // get current category
            if ( $current_product instanceof Mage_Catalog_Model_Product )
                if ( !$current_category instanceof Mage_Catalog_Model_Category )
                    $current_category = $current_product->getCategoryCollection()->addAttributeToSelect('name')->addAttributeToSelect('description')->addFieldToFilter( 'level', array( 'gteq' => 2 ) )->getFirstItem();

            // check if is on catalog page with category
            if ( $current_category instanceof Mage_Catalog_Model_Category ) {
                // check if there is a catalog type menu with ID of which is part of current category path
                $category_ids = explode( '/', $current_category->getPath() );
                $item = Mage::getResourceModel('sitemenu/item_collection')->addVisibleFilter()->addFieldToFilter( 'is_catalog', array( 'eq' => '1' ) )->addFieldToFilter( 'mg_cat_id', array( 'in' => $category_ids ) )->setOrder( 'weight', 'asc' )->getFirstItem();
                if ( $item->getId() ) {
                    // add menu path to crumbs
                    $this->_addCrumbs( $item );

                    // add category path to crumbs
                    $add_to_breadcrumb = false;
                    foreach ( $category_ids as $category_id ) {
                        if ( $add_to_breadcrumb ) {
                            $category = Mage::getModel('catalog/category')->load( $category_id );
                            $this->addCrumb( $category->getUrlKey(), array(
                                'label' => $category->getName(),
                                'title' => $category->getName(),
                                'link'  => $category->getUrl()
                            ) );
                        }
                        if ( $category_id == $item->getMgCatId() )
                            $add_to_breadcrumb = true;
                    }
                    
                    // add product path to crumbs if is product page
                    if ( $current_product instanceof Mage_Catalog_Model_Product ) {
                        $this->addCrumb( $current_product->getSku(), array(
                            'label' => $current_product->getName(),
                            'title' => $current_product->getName(),
                            'link'  => $current_product->getProductUrl()
                        ) );
                    }
                    
                    // clean link of last item
                    $this->_clearLastLink();
                }
            }
        }

        return parent::_toHtml();

    }
    
}
