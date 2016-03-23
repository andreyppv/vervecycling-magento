<?php

class Infinity_BundleSave_Model_Selection extends Mage_Bundle_Model_Selection
{
    /**
    * Processing object before save data
    *
    * @return Mage_Bundle_Model_Selection
    */
    protected function _beforeSave()
    {
        // No code please
    }

    /**
    * Processing object after save data
    *
    * @return Mage_Bundle_Model_Selection
    */
    protected function _afterSave()
    {
        $storeId = Mage::registry('product')->getStoreId();
        if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
            $this->setWebsiteId(Mage::app()->getStore($storeId)->getWebsiteId());

            $this->getResource()->saveSelectionPrice($this);

            if (!$this->getDefaultPriceScope()) {
                $this->unsSelectionPriceValue();
                $this->unsSelectionPriceType();
            }
        }
        parent::_afterSave();
    }
}