<?php
/**
 * Cryozonic
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Single Domain License
 * that is available through the world-wide-web at this URL:
 * http://cryozonic.com/licenses/stripe.html
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to info@cryozonic.com so we can send
 * you a copy immediately.
 *
 * @category   Cryozonic
 * @package    Cryozonic_Stripe
 * @copyright  Copyright (c) Cryozonic Ltd (http://cryozonic.com)
 */

class Cryozonic_Stripe_Block_Form_Standard extends Mage_Payment_Block_Form_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cryozonic/stripe/form/standard.phtml');

        // We need to check if Stripe Subscriptions is installed, this is the
        // cross-magento version compatible way.
        $path = dirname(__FILE__).DS.'..'.DS.'..'.DS.'Model'.DS.'Cryozonic_StripeSubscriptions.xml';
        if (file_exists($path))
            $this->stripe = Mage::getModel('cryozonic_stripe/subscriptions');
        else
            $this->stripe = Mage::getModel('cryozonic_stripe/standard');
    }
}
