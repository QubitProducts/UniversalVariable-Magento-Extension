<?php
class QuBit_UniversalVariable_Helper_Customer extends QuBit_UniversalVariable_Helper_Data
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::helper('customer')->getCustomer();
    }
    /**
     * get universal variable customer array
     * @return array
     */
    public function getUvArray()
    {
        $customer = $this->_getCustomer();
        //fill in with information we'll always know
        $info = array(
            'language'       => $customer->getStore()->getConfig('general/locale/code'),
            'returning'      => false,
        );
        if ($customer->getId()) {
            $info['name']           = $customer->getName();
            $info['email']          = $customer->getEmail();;
            $info['user_id']        = (string) $customer->getId();
            $info['returning']      = true;
        } else if ($this->isConfirmation()) {
            $orderId = $this->_getCheckoutSession()->getLastOrderId();
            if ($orderId) {
                $order = $this->_getSalesOrder()->load($orderId);
                $info['email'] = $order->getCustomerEmail();
                $info['name']  = $order->getCustomerName();
            }
        }
        return $info;
    }
}