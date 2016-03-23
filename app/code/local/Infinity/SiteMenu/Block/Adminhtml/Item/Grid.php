<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();

        $this->setId( 'SiteMenuItemGrid' );
        $this->setDefaultSort( 'id' );
        $this->setDefaultDir( 'ASC' );
        $this->setSaveParametersInSession( true );

    }

    protected function _prepareCollection() {

        $this->setCollection( Mage::getModel('sitemenu/item')->getCollection() );

        return parent::_prepareCollection();

    }

    protected function _prepareColumns() {

        // ID
        $this->addColumn( 'id', array(
            'header' => Mage::helper('sitemenu')->__('ID'),
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'id',
        ) );

        // Title
        $this->addColumn( 'title', array(
            'header' => Mage::helper('sitemenu')->__('Title'),
            'align'  => 'left',
            'index'  => 'title',
        ) );

        // Identifier
        $this->addColumn( 'key', array(
            'header'       => Mage::helper('sitemenu')->__('Identifier'),
            'width'        => '150px',
            'align'        => 'left',
            'index'        => 'key',
            'filter_index' => '`key`'
        ) );

        // URL
        $this->addColumn( 'url', array(
            'header' => Mage::helper('sitemenu')->__('URL'),
            'width'  => '150px',
            'align'  => 'left',
            'index'  => 'url',
        ) );

        // Parent
        $this->addColumn( 'parent', array(
            'header'  => Mage::helper('sitemenu')->__('Parent'),
            'width'   => '150px',
            'index'   => 'fid',
            'type'    => 'options',
            'filter'  => 'sitemenu/adminhtml_item_grid_select',
            'options' => Mage::getModel('sitemenu/item')->getOptionArray()
        ) );

        // Category
        $this->addColumn( 'category', array(
            'header'  => Mage::helper('sitemenu')->__('Category'),
            'width'   => '150px',
            'index'   => 'category',
            'type'    => 'options',
            'options' => Mage::getModel('sitemenu/category')->getOptionArray()
        ) );

        // Store View
        if ( !Mage::app()->isSingleStoreMode() ) {
            $this->addColumn( 'store_id', array(
                'header'                    => Mage::helper('cms')->__('Store View'),
                'align'                     => 'center',
                'width'                     => '150px',
                'index'                     => 'store_id',
                'type'                      => 'store',
                'store_all'                 => true,
                'store_view'                => true,
                'sortable'                  => false,
                'filter_condition_callback' => array( $this, '_filterStoreCondition' ),
            ) );
        }

        // Sort Order
        $this->addColumn( 'sort', array(
            'header'   => Mage::helper('sitemenu')->__('Order'),
            'width'    => '50px',
            'align'    => 'center',
            'index'    => 'sort',
            'renderer' => 'sitemenu/adminhtml_item_grid_input'
        ) );

        // Status
        $this->addColumn( 'status', array(
            'header'   => Mage::helper('sitemenu')->__('Status'),
            'align'    => 'center',
            'width'    => '90px',
            'index'    => 'status',
            'filter'   => 'sitemenu/adminhtml_item_grid_select',
            'renderer' => 'sitemenu/adminhtml_item_grid_status',
            'options'  => array(
                1 => Mage::helper('sitemenu')->__('Enabled'),
                0 => Mage::helper('sitemenu')->__('Disabled')
            ),
            'params'   => array(
                'url'    => array( 'base' => '*/*/switchstatus' ),
                'fields' => 'id,status'
            )
        ) );

        //$this->addExportType( '*/*/exportCsv', Mage::helper('sitemenu')->__('CSV') );
        //$this->addExportType( '*/*/exportXml', Mage::helper('sitemenu')->__('XML') );

        return parent::_prepareColumns();

    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('sitemenu');

        $this->getMassactionBlock()->addItem( 'delete', array(
            'label'   => Mage::helper('sitemenu')->__('Delete'),
            'url'     => $this->getUrl( '*/*/massDelete' ),
            'confirm' => Mage::helper('sitemenu')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem( 'publish', array(
            'label' => Mage::helper('sitemenu')->__('Publish'),
            'url'   => $this->getUrl( '*/*/massStatus/status/1' )
        ));

        $this->getMassactionBlock()->addItem( 'unpublish', array(
            'label' => Mage::helper('sitemenu')->__('Unpublish'),
            'url'   => $this->getUrl( '*/*/massStatus/status/0' )
        ));

        return $this;

    }

    protected function _afterLoadCollection() {

        $this->getCollection()->walk('afterLoad');

        parent::_afterLoadCollection();

    }

    protected function _filterStoreCondition( $collection, $column ) {

        if ( !$value = $column->getFilter()->getValue() ) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);

    }

    public function getRowUrl($row ) {

        return $this->getUrl( '*/*/edit', array( 'id' => $row->getId() ) );

    }

}
