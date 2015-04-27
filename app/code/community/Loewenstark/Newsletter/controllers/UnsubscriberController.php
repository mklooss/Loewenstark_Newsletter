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
class Loewenstark_Newsletter_UnsubscriberController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    public function indexAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                $status = Mage::getModel('newsletter/unsubscriber')->unsubscribeByEmail($email);
                $session->addSuccess($this->__('You have been unsubscribed.'));
            } catch (Mage_Core_Exception $e) {
                $session->addException($e, $this->__('There was a problem with the unsubscription: %s', $e->getMessage()));
            } catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the unsubscription.'));
            }
        }
        $this->_redirectReferer();
    }
}
