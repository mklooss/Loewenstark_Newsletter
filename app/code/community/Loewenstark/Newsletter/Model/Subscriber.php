<?php

class Loewenstark_Newsletter_Model_Subscriber
extends Mage_Newsletter_Model_Subscriber
{
    
    CONST XML_PATH_ADVANCED_DOUBELOPTIN = "newsletter/advanced/doubleoptinmagedefault";
    
    /** @var bool $_sendConfirmationSuccessEmail check if email already send **/
    protected $_sendConfirmationSuccessEmail = true;
    
    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribe($email)
    {
        if(Mage::getStoreConfigFlag(self::XML_PATH_ADVANCED_DOUBELOPTIN)) {
            return parent::subscribe($email);
        }
        
        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if(!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed   = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $owner = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email);

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // BOF: changed for force double opt in
                $this->setStatus(self::STATUS_NOT_ACTIVE);
                // EOF: changed for force double opt in
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($owner->getId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId($owner->getId());
        } else {
            $this->setStoreId(Mage::app()->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setIsStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }

            return $this->getStatus();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
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
