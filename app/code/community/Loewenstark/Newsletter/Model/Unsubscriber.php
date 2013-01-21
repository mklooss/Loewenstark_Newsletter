<?php
/**
 * Loewenstark_Newsletter
 *
 * @category    Model
 * @package     Loewenstark_Newsletter
 * @copyright   Copyright (c) 2012 Mathis Klooss (http://www.loewenstark.de/)
 * @license     https://github.com/mklooss/Loewenstark_Newsletter/blob/master/README.md
 */
class Loewenstark_Newsletter_Model_Unsubscriber
extends Loewenstark_Newsletter_Model_Subscriber
{

    /**
     * unsubscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function unsubscribeByEmail($email) {
        $this->setStoreId(Mage::app()->getStore()->getId());
        $this->loadByEmail($email);
        if( !$this->getId() ) {
            return false;
        }
        $this->setCheckCode($this->getCode());
        $this->unsubscribe();
        return $this->getStatus();
    }
}
