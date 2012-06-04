<?php

class Loewenstark_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    /** @var bool $_sendConfirmationSuccessEmail check if email already send **/
    private $_sendConfirmationSuccessEmail = true;

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
    
    /**
     * Confirms subscriber newsletter
     *
     * @param string $code
     * @return boolean
     */
    public function confirm($code)
    {
        $parent = (bool) parent::confirm($code);
        if( $parent ) {
            $this->sendConfirmationSuccessEmail();
            return true;
        }
        return false;
    }

    /**
     * Sends out confirmation success email
     * 
     * @see Mage_Newsletter_Model_Subscriber::sendConfirmationSuccessEmail
     *
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function sendConfirmationSuccessEmail() {
        // do not send two E-Mails, may Magento will be implements this line in methode self::confirm($code)
        if( $this->_sendConfirmationSuccessEmail ) {
            parent::sendConfirmationSuccessEmail();
            $this->_sendConfirmationSuccessEmail = !$this->_sendConfirmationSuccessEmail;
        }
        return $this;
    }
}
