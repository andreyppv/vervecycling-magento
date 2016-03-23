<?php

/**
 * Infinity Site Menu
 *
 * @category    Infinity
 * @package     Infinity_SiteMenu
 * @copyright   Copyright (c) 2011 Infinity Technologies (http://www.infinitytechnologies.com.au)
 * @author      Haydn.h, Bruce.z
 */
class Infinity_SiteMenu_Block_Adminhtml_Item extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        $this->_controller = 'adminhtml_item';
        $this->_blockGroup = 'sitemenu';

        $this->_addButtonLabel = Mage::helper('core')->__('Add an Item');
        $this->_headerText     = Mage::helper('core')->__('Site Menu Item Manager');
        
        parent::__construct();

        $this->_addButton( 'auto_add', array(
            'label'   => Mage::helper('core')->__('Create Items for Store Views'),
            'onclick' => 'createForLan(this);'
        ));

        $this->_addButton( 'sort', array(
            'label'   => Mage::helper('core')->__('Sort Items'),
            'onclick' => 'sortItems(this);'
        ));

    }

    protected function _toHtml() {

        ob_start();

        ?>

        <script type="text/javascript">
        //<![CDATA[
        var sortItems = function ( el ) {

            // created form if not exists
            if ( !document.getElementById( 'form_sortorder' ) )
                $(el).up().insert( new Element( 'form', { 'action' : '<?php echo $this->getUrl( '*/*/massSort' ) ?>', 'method' : 'post', 'id' : 'form_sortorder' } ).update() );
            
            // clear order form
            $('form_sortorder').update('').insert( new Element( 'input', { 'type' : 'hidden', 'name' : 'form_key', 'value' : '<?php echo Mage::getSingleton('core/session')->getFormKey() ?>' } ) );

            // update order form
            var id    = document.getElementsByName('sitemenu');
            var order = document.getElementsByName('sort[]');
            for ( var i = 0; i < id.length; i ++ )
                $('form_sortorder').insert( new Element( 'input', { 'type' : 'hidden', 'name' : 'sort_order[' + id[i].value + ']', 'value' : order[i].value } ) );
            
            // submit form
            $('form_sortorder').submit();
        }

        var createForLan = function () {

            window.location.href = '<?php echo $this->getUrl( '*/*/createforlan', array( 'website_id' => 1 ) ) ?>';

        }
        //]]>
        </script>

        <?php

        $js = ob_get_contents();

        ob_end_clean();

        return parent::_toHtml() . $js;

    }

}
