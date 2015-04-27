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
class Loewenstark_Newsletter_SubscriberController extends Mage_Core_Controller_Front_Action
{
    /**
      * New subscription action
      */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $helper             = Mage::helper("lws_newsletter");
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($helper->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($helper->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                if (Mage::getStoreConfigFlag(Loewenstark_Newsletter_Model_Subscriber::XML_PATH_ADVANCED_DOUBELOPTIN)) {
                    $ownerId = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email)
                            ->getId();
                    if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                        Mage::throwException($helper->__('This email address is already assigned to another user.'));
                    }
                }

                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($helper->__('Confirmation request has been sent.'));
                } else {
                    $session->addSuccess($helper->__('Thank you for your subscription.'));
                }
            } catch (Mage_Core_Exception $e) {
                $session->addException($e, $helper->__('There was a problem with the subscription: %s', $e->getMessage()));
            } catch (Exception $e) {
                $session->addException($e, $helper->__('There was a problem with the subscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
