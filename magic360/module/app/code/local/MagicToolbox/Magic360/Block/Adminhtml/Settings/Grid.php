<?php

class MagicToolbox_Magic360_Block_Adminhtml_Settings_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('settingsGrid');
        $this->setDefaultSort('setting_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);

    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('magic360/settings')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

    protected function _prepareColumns() {

        $this->addColumn('setting_id', array(
            'header'    => Mage::helper('magic360')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'setting_id',
        ));

        $this->addColumn('custom_settings_title', array(
            'header'    => Mage::helper('magic360')->__('List of custom settings'),
            'align'     =>'left',
            'index'     => 'custom_settings_title',
        ));

        /*$this->addColumn('package', array(
            'header'    => Mage::helper('magic360')->__('Package'),
            'align'     =>'left',
            'index'     => 'package',
        ));*/

        /*$this->addColumn('theme', array(
            'header'    => Mage::helper('magic360')->__('Theme'),
            'align'     =>'left',
            'index'     => 'theme',
        ));*/

        $this->addColumn('last_edit_time', array(
            'header'    => Mage::helper('magic360')->__('Last edit time'),
            'align'     =>'left',
            'index'     => 'last_edit_time',
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('magic360')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('magic360')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();

    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('setting_id');
        $this->getMassactionBlock()->setFormFieldName('massactionId');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('magic360')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('magic360')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row) {

        return $this->getUrl('*/*/edit', array('id' => $row->getId()));

    }

}
