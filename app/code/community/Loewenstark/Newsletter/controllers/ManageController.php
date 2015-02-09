<?php
/**
  * Loewenstark_Newsletter
  *
  * @category  Loewenstark
  * @package   Loewenstark_Newsletter
  * @author    Mathis Klooss <m.klooss@loewenstark.de>
  * @copyright 2013 Loewenstark Web-Solution GmbH (http://www.loewenstark.de). All rights served.
  * @license     https://github.com/mklooss/Loewenstark_Newsletter/blob/master/README.md
  */
class Loewenstark_Newsletter_ManageController extends Mage_Core_Controller_Front_Action
{
    /**
      * New subscription action
      */
    public function resendAction()
    {
        if (Mage::helper('customer')->isLoggedIn()) {
            try {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->sendConfirmationRequestEmail();
            } catch (Exception $e) {
                // Log exception
                Mage::logException($e);
            }
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('lws_newsletter')->__('Newsletter confirmation E-Mail has been send!'));
        }
        $this->_redirectReferer();
    }
}
