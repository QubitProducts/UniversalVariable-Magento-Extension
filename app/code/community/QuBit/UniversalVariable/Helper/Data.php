<?php

class QuBit_UniversalVariable_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * check whether current request is the confirmation page
     * @return boolean
     */
    public function isConfirmation()
    {
        // default controllerName is "onepage"
        // relax the check, only check if contains checkout
        // somecheckout systems has different prefix/postfix,
        // but all contains checkout
        if (strpos($this->_getModuleName(), 'checkout') !== false && $this->_getActionName() == 'success') {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    /**
     * @return Mage_Sales_Model_Order
     */
    protected function _getSalesOrder()
    {
        return Mage::getModel('sales/order');
    }
    /**
     * get request model
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getFrontController()->getRequest();
    }
    /**
     * get request module name 
     * @return str
     */
    protected function _getModuleName()
    {
        return $this->_getRequest()->getModuleName();
    }
    /**
     * get request action name
     * @return str
     */
    protected function _getActionName()
    {
        return $this->_getRequest()->getActionName();
    }
}
