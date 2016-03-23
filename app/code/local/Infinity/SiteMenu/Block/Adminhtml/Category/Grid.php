<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();

        $this->setId( 'SiteMenuCategoryGrid' );
        $this->setDefaultSort( 'id' );
        $this->setDefaultDir( 'ASC' );
        $this->setSaveParametersInSession( true );
        
    }

    protected function _prepareCollection() {

        $this->setCollection( Mage::getModel('sitemenu/category')->getCollection() );

        return parent::_prepareCollection();
        
    }

    protected function _prepareColumns() {

        $this->addColumn( 'name', array(
            'header' => Mage::helper('core')->__('Name'),
            'align'  => 'left',
            'index'  => 'name'
        ) );

        $this->addColumn( 'identify', array(
            'header' => Mage::helper('core')->__('Identify'),
            'align'  =>'left',
            'index'  => 'identify'
        ) );

        $this->addColumn( 'weight', array(
            'header' => Mage::helper('core')->__('Weight'),
            'align'  => 'left',
            'index'  => 'weight',
            'width'  => '90px'
        ) );

        $this->addColumn( 'status', array(
            'header'   => Mage::helper('core')->__('Status'),
            'align'    => 'center',
            'width'    => '90px',
            'index'    => 'status',
            'filter'   => 'adminhtml/widget_grid_column_filter_select',
            'options'  => array(
                1 => Mage::helper('core')->__('Enabled'),
                2 => Mage::helper('core')->__('Disabled')
            ),
            'params'   => array(
                'url'    => array( 'base' => '*/*/switchstatus' ),
                'fields' => 'id,status'
            ),
            'renderer' => new Infinity_SiteMenu_Block_Adminhtml_Category_Grid_Status()
        ) );

        return parent::_prepareColumns();
        
    }

    protected function _prepareMassaction() {
        
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('sitemenu_category');

        $this->getMassactionBlock()->addItem( 'delete', array(
            'label'   => Mage::helper('core')->__('Delete'),
            'url'     => $this->getUrl( '*/*/massDelete' ),
            'confirm' => Mage::helper('core')->__('Are you sure?')
        ));

        $this->getMassactionBlock()->addItem( 'publish', array(
            'label' => Mage::helper('core')->__('Publish'),
            'url'   => $this->getUrl( '*/*/massStatus/status/1' )
        ));

        $this->getMassactionBlock()->addItem( 'unpublish', array(
            'label' => Mage::helper('core')->__('Unpublish'),
            'url'   => $this->getUrl( '*/*/massStatus/status/2' )
        ));
        
        return $this;
        
    }

    public function getRowUrl( $row ) {
    
        return $this->getUrl( '*/*/edit', array( 'id' => $row->getId() ) );
        
    }

}