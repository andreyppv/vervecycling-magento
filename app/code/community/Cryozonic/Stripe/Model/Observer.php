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

class Cryozonic_Stripe_Model_Observer
{
    public function sales_order_payment_place_end($observer)
    {
        $customer = $observer->getPayment()->getOrder()->getCustomer();
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();

        if (!empty($customerId) && !empty($customerEmail))
        {
            try
            {
                $resource = Mage::getSingleton('core/resource');
                $connection = $resource->getConnection('core_write');
                $fields = array();
                $fields['customer_id'] = $customerId;
                $condition = array($connection->quoteInto('customer_email=?', $customerEmail));
                $result = $connection->update('cryozonic_stripesubscriptions_customers', $fields, $condition);
            }
            catch (Exception $e) {}
        }
    }
}