<?php

class Loewenstark_Newsletter_Block_Unsubscribe
extends Mage_Newsletter_Model_Subscriber
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
