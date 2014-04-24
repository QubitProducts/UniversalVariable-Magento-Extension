<?php
class QuBit_UniversalVariable_Helper_Cart extends QuBit_UniversalVariable_Helper_Data
{
    public function getCartUvArray()
    {
        $cartSession  = $this->_getCheckoutSession();
        $basket       = array();
        $quote        = $cartSession->getQuote();
        
        // Set normal params
        $basketId = $cartSession->getQuoteId();
        if ($basketId) {
            $basket['id'] = (string) $basketId;
        }
        $basket['currency']             = $this->_getStoreCurrency();
        $basket['subtotal']             = (float) $quote->getSubtotal();
        $basket['tax']                  = (float) $quote->getShippingAddress()->getTaxAmount();
        $basket['subtotal_include_tax'] = (boolean) $this->_doesSubtotalIncludeTax($quote, $basket['tax']);
        $basket['shipping_cost']        = (float) $quote->getShippingAmount();
        $basket['shipping_method']      = $quote->getShippingMethod();
        $basket['total']                = (float) $quote->getGrandTotal();
        
        // Line items
        $items = $quote->getAllItems();
        $basket['line_items'] = $this->_getLineItems($items, 'basket');
        
        return $basket;
    }
    public function getTransactionUvArray()
    {
        $orderId = $this->_getCheckoutSession()->getLastOrderId();
        $transaction = array();
        if ($orderId) {
            $order       = $this->_getSalesOrder()->load($orderId);
        
            // Get general details
            $transaction['order_id']             = $order->getIncrementId();
            $transaction['currency']             = $this->_getStoreCurrency();
            $transaction['subtotal']             = (float) $order->getSubtotal();
            $transaction['tax']                  = (float) $order->getTaxAmount();
            $transaction['subtotal_include_tax'] = $this->_doesSubtotalIncludeTax($order, $transaction['tax']);
            $transaction['payment_type']         = $order->getPayment()->getMethodInstance()->getTitle();
            $transaction['total']                = (float) $order->getGrandTotal();
        
            $voucher                             = $order->getCouponCode();
            $transaction['voucher']              = $voucher ? $voucher : '';
            $voucher_discount                    = -1 * $order->getDiscountAmount();
            $transaction['voucher_discount']     = $voucher_discount ? $voucher_discount : 0;
        
        
            $transaction['shipping_cost']   = (float) $order->getShippingAmount();
            $transaction['shipping_method'] = $order->getShippingMethod();
        
            // Get addresses
            $billingAddress    = $order->getBillingAddress();
            $shippingAddress   = $order->getShippingAddress();
            $transaction['billing']  = $this->_getAddress($billingAddress);
            $transaction['delivery'] = $this->_getAddress($shippingAddress);
        
            // Get items
            $items                     = $order->getAllItems();
            $transaction['line_items'] = $this->_getLineItems($items, 'transaction');
        }
        return $transaction;
    }
    /**
     * @param Mage_Sales_Model_Quote | Mage_Sales_Model_Order $quote
     * @param float $tax
     * @return boolean
     */
    protected function _doesSubtotalIncludeTax($quote, $tax)
    {
        /* Conditions:
            - if tax is zero, then set to false
            - Assume that if grand total is bigger than total after subtracting shipping, then subtotal does NOT include tax
        */
        $grandTotalWithoutShipping = $quote->getGrandTotal() - $quote->getShippingAmount();
        if ($tax == 0 || $grandTotalWithoutShipping > $quote->getSubtotal()) {
          return false;
        }
      return true;
  }
  protected function _getLineItems($items, $pageType)
  {
      $line_items = array();
      foreach($items as $item) {
          $productId = $item->getProductId();
          $product   = $item->getProduct();
          // product needs to be visible
          if ($product->isVisibleInSiteVisibility()) {
              $litem_model             = array();
              $litem_model['product']  = Mage::helper('universal_variable_main/catalog')->getProductUVArray($product);
  
  
              $litem_model['subtotal'] = (float) $item->getRowTotalInclTax();
              $litem_model['total_discount'] = (float) $item->getDiscountAmount();
  
              if ($pageType == 'basket') {
                  $litem_model['quantity'] = (float) $item->getQty();
              } else {
                  $litem_model['quantity'] = (float) $item->getQtyOrdered();
              }
  
              array_push($line_items, $litem_model);
          }
      }
      return $line_items;
  }
  /**
   * @param unknown $address
   * @return array
   */
  protected function _getAddress($address)
  {
      $info = array();
      if ($address) {
          $info['name']     = $address->getName();
          $info['address']  = $address->getStreetFull();
          $info['city']     = $address->getCity();
          $info['postcode'] = $address->getPostcode();
          $info['country']  = $address->getCountry();
      }
      // TODO: $billing['state']
      return $info;
  }
}