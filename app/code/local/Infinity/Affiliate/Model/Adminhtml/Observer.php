<?php

class Infinity_Affiliate_Model_Adminhtml_Observer
{
	//Event: adminhtml_controller_action_predispatch_start
	public function overrideTheme()
	{
            Mage::getDesign()->setArea('adminhtml')->setTheme((string)Mage::getStoreConfig('design/admin/theme'));
	}
}