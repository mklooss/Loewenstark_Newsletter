<?php
/**
 * Loewenstark_Newsletter
 *
 * @category    Controller
 * @package     Loewenstark_Newsletter
 * @copyright   Copyright (c) 2012 Mathis Klooss (http://www.loewenstark.de/)
 * @license     https://github.com/mklooss/Loewenstark_Newsletter/blob/master/README.md
 */
class Loewenstark_Newsletter_ManageController
extends Mage_Core_Controller_Front_Action
{
    /**
      * New subscription action
      */
    public function resendAction()
    {
        if(Mage::helper('customer')->isLoggedIn()) {
            try {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->sendConfirmationRequestEmail();
            } catch(Exception $e) {}
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('lws_newsletter')->__('Newsletter confirmation E-Mail has been send!'));
        }
        $this->_redirectReferer();
    }
}