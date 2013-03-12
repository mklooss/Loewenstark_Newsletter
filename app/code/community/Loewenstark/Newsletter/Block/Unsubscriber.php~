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
