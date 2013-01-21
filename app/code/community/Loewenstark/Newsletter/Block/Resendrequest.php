<?php
/**
 * Loewenstark_Newsletter
 *
 * @category    Block
 * @package     Loewenstark_Newsletter
 * @copyright   Copyright (c) 2012 Mathis Klooss (http://www.loewenstark.de/)
 * @license     https://github.com/mklooss/Loewenstark_Newsletter/blob/master/README.md
 */
class Loewenstark_Newsletter_Block_Resendrequest
extends Mage_Customer_Block_Account_Dashboard
{

    /**
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