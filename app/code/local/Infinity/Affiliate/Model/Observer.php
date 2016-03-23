<?php

class Infinity_Affiliate_Model_Observer
{

    public function saveAffiliateData(Varien_Event_Observer $observer)
    {
        //Mage::dispatchEvent('admin_session_user_login_success', array('user'=>$user));
        //$user = $observer->getEvent()->getUser();
        //$user->doSomething();
    }
    
    public function saveAffiliateQuoteData($observer)
    {
        
        $affiliatePartnerName = Mage::app()->getFrontController()->getRequest()->getParam('affiliate_partner_name');
        
        if($affiliatePartnerName)
        {
            $quote = $observer->getEvent()->getOrder()->getQuote();
            $quote->setData('affiliate_partner_name', $affiliatePartnerName);
            
            $order = $observer->getEvent()->getOrder();
            $order->setData('affiliate_partner_name', $affiliatePartnerName);
        }    
        
        return $this;
    }

}
