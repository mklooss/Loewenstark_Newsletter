<?php
/**
 * Loewenstark_Newsletter
 *
 * @category    block
 * @package     Loewenstark_Newsletter
 * @copyright   Copyright (c) 2012 Mathis Klooss (http://www.loewenstark.de/)
 * @license     https://github.com/mklooss/Loewenstark_Newsletter/blob/master/README.md
 */
class Loewenstark_Newsletter_Block_Unsubscriber
extends Mage_Newsletter_Block_Subscribe
{
    
    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/unsubscriber', array('_secure' => true));
    }
}