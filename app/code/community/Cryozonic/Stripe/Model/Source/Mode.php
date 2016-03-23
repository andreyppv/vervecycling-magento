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

class Cryozonic_Stripe_Model_Source_Mode
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Cryozonic_Stripe_Model_Standard::TEST,
                'label' => Mage::helper('cryozonic_stripe')->__('Test')
            ),
            array(
                'value' => Cryozonic_Stripe_Model_Standard::LIVE,
                'label' => Mage::helper('cryozonic_stripe')->__('Live')
            ),
        );
    }
}
