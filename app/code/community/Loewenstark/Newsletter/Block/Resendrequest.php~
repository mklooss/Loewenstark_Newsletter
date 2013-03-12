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
class Loewenstark_Newsletter_Block_Resendrequest
extends Mage_Customer_Block_Account_Dashboard
{

    /**
     * get resend url
     *
     * @return string
     */
    public function getResendUrl()
    {
        return $this->getUrl('*/*/resend', array('_secure' => true));
    }
    
    /**
     * Type is Enabled
     * 
     * @return type
     */
    public function isEnabled()
    {
        return !Mage::getStoreConfigFlag(Loewenstark_Newsletter_Model_Subscriber::XML_PATH_ADVANCED_DOUBELOPTIN);
    }
    
    /**
     * get Current 
     * @return type
     */
    public function getStatus()
    {
        return (int) $this->getSubscriptionObject()->getStatus();
    }
    
    /**
     * Subscriber Model
     * 
     * @return Loewenstark_Newsletter_SubscriberController
     */
    public function getSubscription()
    {
        return $this->getSubscriptionObject();
    }
    
    /**
     * Simple Mapping
     * 
     * @param type $string
     * @return type
     */
    public function getStatusByName($string)
    {
        $return = array(
            "STATUS_SUBSCRIBED"     => Loewenstark_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED,
            "STATUS_NOT_ACTIVE"     => Loewenstark_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE,
            "STATUS_UNSUBSCRIBED"   => Loewenstark_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED,
            "STATUS_UNCONFIRMED"    => Loewenstark_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED,
        );
        return isset($return[$string]) ? $return[$string] : null;
    }
}
