<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Category_Grid_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    var $fields;

    public function render( Varien_Object $row ) {

        $options = $this->getColumn()->getOptions();
        $params  = $this->getColumn()->getParams();
        $field   = $this->getColumn()->getIndex();
        $value   = $row->getData( $field );

        // get fields to post
        $this->fields = is_string( $params['fields'] ) ? preg_split( '/\s*,\s*/Ui', $params['fields'] ) : array( 'id' );

        // set default value array
        foreach ( $this->fields as $f )
            $arr_v[ $f ] = $row->getData( $f );

        // set selector HTML
        $html = '<select class="action-select" onchange="varienGridAction.execute(this);">';
        foreach ( $options as $v => $label ){

            // add `selected` attr to selected option
            $selected = ( $v == $value ) ? ' selected="selected"' : '';

            // update option value
            $arr_v[ $field ] = $v;

            // set option HTML
            $html .= '<option '. $this->_toOptionAttibutes( $arr_v, $params ) . $selected .'>'. $label .'</option>';
        }
        $html .= '</select>';

        return $html;

    }

    protected function _toOptionAttibutes( $value, $params ) {

        $this->_transformActionData( $value, $params );

        $attibutes = array( 'value' => $this->htmlEscape( Mage::helper('core')->jsonEncode( $params ) ) );

        $html_attributes = new Varien_Object();
        $html_attributes->setData( $attibutes );

        return $html_attributes->serialize();

    }

    protected function _transformActionData( $value, &$params ) {

        // get base url & unset unnecessary params
        foreach ( $params as $k => $v ) {
            if ( $k == 'url' )
                if ( isset( $params['url']['base'] ) )
                    $base = $params['url']['base'];
            unset( $params[ $k ] );
        }

        // get data to post for each option
        $attrs = array();
        foreach ( $this->fields as $field ) {
            if ( isset( $value[ $field ] ) )
                $attrs[ $field ] = $value[ $field ];
        }

        // process the link with base url & data just got
        $params['href'] = $this->getUrl( $base, $attrs );

    }

}
