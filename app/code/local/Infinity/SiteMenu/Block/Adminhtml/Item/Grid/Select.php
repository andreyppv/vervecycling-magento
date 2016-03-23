<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item_Grid_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select {

    protected function _renderOption($option, $value)
                {
        $selected = (($option['value'] == $value && (!is_null($value))) ? ' selected="selected"' : '' );
        return '<option value="'. $option['value'].'"'.$selected.'>'. $option['label'] .'</option>';
        
    }

    public function getHtml() {
    
        $html = '<select name="'.$this->_getHtmlName().'" id="'.$this->_getHtmlId().'" class="no-changes">';
        $value = $this->getValue();
        foreach ($this->_getOptions() as $option){
            if (is_array($option['value'])) {
                $html .= '<optgroup label="'. $option['label'] . '">';
                foreach ($option['value'] as $subOption) {
                    $html .= $this->_renderOption($subOption, $value);
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_renderOption($option, $value);
            }
        }
        $html.='</select>';
        return $html;
        
    }

}
