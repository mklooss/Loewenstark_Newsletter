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
class Loewenstark_Newsletter_Model_Subscriber extends Mage_Newsletter_Model_Subscriber
{
    // configuration
    const XML_PATH_ADVANCED_DOUBELOPTIN = "newsletter/advanced/doubleoptinmagedefault";

    /** @var bool $_sendConfirmationSuccessEmail check if email already send **/
    protected $_sendConfirmationSuccessEmail = true;
    /** @var bool $_sendConfirmationRequestEmail check if email already send **/
    protected $_sendConfirmationRequestEmail = true;

    /**
     * Load subscriber info by customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Newsletter_Model_Subscriber
     */
    public function loadByCustomer(Mage_Customer_Model_Customer $customer)
    {
        $data = $this->getResource()->loadByCustomer($customer);
        $this->addData($data);
        if (!empty($data) && $customer->getId() && !$this->getCustomerId()) {
            $this->setCustomerId($customer->getId());
            // if code exists use the current code
            $code = $this->getSubscriberConfirmCode();
            if(!$code)
            {
                $code = $this->randomSequence();
            }
            $this->setSubscriberConfirmCode($code);
            if ($this->getStatus()==self::STATUS_NOT_ACTIVE) {
                $this->setStatus($customer->getIsSubscribed() ? self::STATUS_SUBSCRIBED : self::STATUS_UNSUBSCRIBED);
            }
            $this->save();
        }
        return $this;
    }


    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws Exception
     * @return int
     */
    public function subscribe($email)
    {
        // fall back to default
        if (Mage::getStoreConfigFlag(self::XML_PATH_ADVANCED_DOUBELOPTIN)) {
            return parent::subscribe($email);
        }

        $this->loadByEmail($email);
        $customerSession = Mage::getSingleton('customer/session');

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        $isConfirmNeed   = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $isOwnSubscribes = false;
        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email)
            ->getId();
        $isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // BOF: changed for force double opt in
                $this->setStatus(self::STATUS_UNCONFIRMED);
                // EOF: changed for force double opt in
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setSubscriberEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            $this->setStoreId($customerSession->getCustomer()->getStoreId());
            $this->setCustomerId($customerSession->getCustomerId());
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
     * Saving customer subscription status
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Newsletter_Model_Subscriber
     */
    public function subscribeCustomer($customer)
    {
        // fall back to default
        if (Mage::getStoreConfigFlag(self::XML_PATH_ADVANCED_DOUBELOPTIN)) {
            return parent::subscribeCustomer($customer);
        }

        $this->loadByCustomer($customer);

        if ($customer->getImportMode()) {
            $this->setImportMode(true);
        }

        if (!$customer->getIsSubscribed() && !$this->getId()) {
            // If subscription flag not set or customer is not a subscriber
            // and no subscribe below
            return $this;
        }

        if (!$this->getId()) {
            $this->setSubscriberConfirmCode($this->randomSequence());
        }

        /*
         * Logical mismatch between customer registration confirmation code and customer password confirmation
         */
        $confirmation = null;
        if ($customer->isConfirmationRequired() && ($customer->getConfirmation() != $customer->getPassword())) {
            $confirmation = $customer->getConfirmation();
        }

        $sendInformationEmail = false;
        if ($customer->hasIsSubscribed()) {
            $status = $customer->getIsSubscribed() ? self::STATUS_UNCONFIRMED : self::STATUS_UNSUBSCRIBED;
            /**
             * If subscription status has been changed then send email to the customer
             */
            if ($status == self::STATUS_UNCONFIRMED || $this->getStatus() == self::STATUS_UNCONFIRMED) {
                $status = self::STATUS_UNCONFIRMED;
                $sendInformationEmail = true;
            }
            /**
             * reset with data from subscription
             */
            if ($status == self::STATUS_UNCONFIRMED && $this->getStatus() == self::STATUS_SUBSCRIBED)
            {
                $status = self::STATUS_SUBSCRIBED;
                $sendInformationEmail = false;
            }
        } elseif (($this->getStatus() == self::STATUS_UNCONFIRMED) && (is_null($confirmation))) {
            $status = self::STATUS_UNCONFIRMED;
            $sendInformationEmail = true;
        } else {
            $status = ($this->getStatus() == self::STATUS_NOT_ACTIVE ? self::STATUS_UNSUBSCRIBED : $this->getStatus());
        }

        if ($status != $this->getStatus()) {
            $this->setIsStatusChanged(true);
        }

        $this->setStatus($status);

        if (!$this->getId()) {
            $storeId = $customer->getStoreId();
            if ($customer->getStoreId() == 0) {
                $storeId = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customer->getId())
                ->setEmail($customer->getEmail());
        } else {
            $this->setStoreId($customer->getStoreId())
                ->setEmail($customer->getEmail());
        }

        $this->save();
        if ($this->getIsStatusChanged()) {
            $sendSubscription = $customer->getData('sendSubscription') || $sendInformationEmail;
            if (is_null($sendSubscription) xor $sendSubscription) {
                if ($this->getIsStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                    $this->sendUnsubscriptionEmail();
                } elseif ($this->getIsStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                    $this->sendConfirmationSuccessEmail();
                } elseif ($this->getIsStatusChanged() && $status == self::STATUS_UNCONFIRMED) {
                    $this->sendConfirmationRequestEmail();
                }
            }
        }
        return $this;
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
        if ($parent) {
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
    public function sendConfirmationSuccessEmail()
    {
        // do not send two E-Mails, may Magento will be implements this line in methode self::confirm($code)
        if ($this->_sendConfirmationSuccessEmail) {
            parent::sendConfirmationSuccessEmail();
            $this->_sendConfirmationSuccessEmail = !$this->_sendConfirmationSuccessEmail;
        }
        return $this;
    }

    public function sendConfirmationRequestEmail()
    {
        if ($this->_sendConfirmationRequestEmail) {
            parent::sendConfirmationRequestEmail();
            $this->_sendConfirmationRequestEmail = !$this->_sendConfirmationRequestEmail;
        }
        return $this;
    }
}
