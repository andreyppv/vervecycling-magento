<?php

class MagicToolbox_Magic360_Model_Mysql4_Settings extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {

        $this->_init('magic360/settings', 'setting_id');

    }

}
