<?php
require_once(Mage::getModuleDir('controllers','Mage_Checkout').DS.'OnepageController.php');

class Infinity_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
  
    // use previos version of progressAction(). After magento upgrade this method was changed and progress panel was broken on master branch
    public function progressAction()
    {
        // previous step should never be null. We always start with billing and go forward
        $prevStep = $this->getRequest()->getParam('toStep', false);

        if ($this->_expireAjax() || !$prevStep) {
            return null;
        }
        $this->loadLayout(false);
        $this->renderLayout();
    }

}