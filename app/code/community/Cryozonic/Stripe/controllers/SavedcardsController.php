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

class Cryozonic_Stripe_SavedcardsController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $stripe = Mage::getModel('cryozonic_stripe/standard');

        $deleteCards = $this->getRequest()->getParam('card', null);
        if (!empty($deleteCards))
        {
            $stripe->deleteCards($deleteCards);
            $this->_redirect('customer/savedcards');
        }

        $newcard = $this->getRequest()->getParam('newcard', null);
        if (!empty($newcard))
        {
            if ($newcard)
            {
                if (isset($newcard['cc_stripejs_token']))
                {
                    // This case is when AVS is enabled
                    if (strpos($newcard['cc_stripejs_token'], ':'))
                    {
                        $card = explode(':', $newcard['cc_stripejs_token']);
                        $params = $card[0];
                    }
                    else
                        $params = $newcard['cc_stripejs_token'];
                }
                else
                    $params = array(
                        "name" => $newcard['cc_owner'],
                        "number" => $newcard['cc_number'],
                        "cvc" => $newcard['cc_cid'],
                        "exp_month" => $newcard['cc_exp_month'],
                        "exp_year" => $newcard['cc_exp_year']
                    );

                try
                {
                    $stripe->addCardToCustomer($params);
                    $this->_redirect('customer/savedcards');
                }
                catch (Stripe_Error $e)
                {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
                catch (Exception $e)
                {
                    Mage::log($e->getMessage());
                    Mage::getSingleton('core/session')->addError("Sorry, the card could not be added!");
                }
            }
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}