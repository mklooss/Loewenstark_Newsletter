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
