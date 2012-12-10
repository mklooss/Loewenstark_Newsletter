<?php

class Loewenstark_Newsletter_Model_Observer
extends Mage_Newsletter_Model_Observer
{
    
    public function subscribeCustomer($observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            // fall back to default
            if(Mage::getStoreConfigFlag(Loewenstark_Newsletter_Model_Subscriber::XML_PATH_ADVANCED_DOUBELOPTIN))
            {
                Mage::getModel("newsletter/subscriber")->subscribeCustomer($customer);
            }
            else
            {
                if (!$customer->getIsSubscribed() && !$this->getId()) {
                    return $this;
                }
                Mage::getModel("newsletter/subscriber")->subscribe($customer->getEmail());
            }
        }
        return $this;
    }
}
