<?php
class Verve_Cycling_Block_Template extends Mage_Core_Block_Template {
    public function getCategories(){
       return Mage::app()->getLayout()
           ->createBlock('cms/block')
           ->setBlockId('homepage')->toHtml();
    }
}