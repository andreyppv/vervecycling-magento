<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Grid_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render( Varien_Object $row ) {

        $field = $this->getColumn()->getIndex();
        $value = $row->getData( $field );
        $id    = $row->getData( 'id' );

        return '<input type="text" class="input-text " value="'. $this->htmlEscape( $value ) .'" name="'. $field .'[]" />';

    }

}
