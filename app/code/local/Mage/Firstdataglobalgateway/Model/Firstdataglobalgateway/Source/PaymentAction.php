<?php
class Mage_Firstdataglobalgateway_Model_Firstdataglobalgateway_Source_PaymentAction { public function toOptionArray() { return array(array('value' => Mage_Firstdataglobalgateway_Model_Firstdataglobalgateway::ACTION_AUTHORIZE, 'label' => Mage::helper('paygate')->__('Authorize Only')), array('value' => Mage_Firstdataglobalgateway_Model_Firstdataglobalgateway::ACTION_AUTHORIZE_CAPTURE, 'label' => Mage::helper('paygate')->__('Authorize and Capture'))); } }