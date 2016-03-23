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

class Cryozonic_Stripe_Model_Source_ExpiredAuthorizations
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('cryozonic_stripe')->__('Warn admin and don\'t capture')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('cryozonic_stripe')->__('Try to re-create the charge with a saved card')
            ),
        );
    }
}
