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

class Cryozonic_Stripe_Helper_Data extends Mage_Payment_Helper_Data
{
    public function getBillingAddress($quote = null)
    {
        $address = array();

        $checkout = Mage::getSingleton('checkout/session')->getQuote();
        if ($checkout->getItemsCount() > 0) // Are we at the checkout page?
        {
            $billAddress = $checkout->getBillingAddress();
            $address['address_line1'] = $billAddress->getData('street');
            $address['address_zip'] = $billAddress->getData('postcode');
        }

        // If there is no checkout session then we must be coming here from the back office.
        if (empty($address['address_line1']) && Mage::app()->getStore()->isAdmin())
        {
            $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();

            if (!empty($quote)) {
                $billAddress = $quote->getBillingAddress();
                $address['address_line1'] = $billAddress->getData('street');
                $address['address_zip'] = $billAddress->getData('postcode');
            }
        }
        else if (empty($address['address_line1']))
        {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerAddressId = $customer->getDefaultBilling();
            if ($customerAddressId)
            {
                $billAddress = Mage::getModel('customer/address')->load($customerAddressId);
                $address['address_line1'] = $billAddress->getData('street');
                $address['address_zip'] = $billAddress->getData('postcode');
            }
        }

        return $address;
    }
}
