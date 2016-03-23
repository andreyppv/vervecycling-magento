<?php

class Infinity_SupportSwitcher_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->norouteAction();
            return;
        }
        $ajaxRequest = $this->getRequest()->getParams('location');
        if(!($ajaxRequest['location'])){
            $locationsList = Mage::helper('supportswitcher/data')->parseLocationsOption();
            $ajaxRequest['location'] = reset(array_keys($locationsList));
        }
        $cookie = Mage::getSingleton('core/cookie');
        $cookie->set('location', $ajaxRequest['location'] , time()+86400, '/');
        die($ajaxRequest['location']);
    }
}