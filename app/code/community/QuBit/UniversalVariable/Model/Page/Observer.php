<?php

class QuBit_UniversalVariable_Model_Page_Observer {

  // This is the UV specification Version
  // http://tools.qubitproducts.com/uv/developers/specification
  protected $_version     = "1.2";
  protected $_user        = null;
  protected $_page        = null;
  protected $_basket      = null;
  protected $_product     = null;
  protected $_search      = null;
  protected $_transaction = null;
  protected $_listing     = null;
  protected $_events      = array();

  protected function _getRequest() {
    return Mage::app()->getFrontController()->getRequest();
  }

  /*
  * Returns Controller Name
  */
  protected function _getControllerName() {
    return $this->_getRequest()->getControllerName();
  }

  protected function _getActionName() {
    return $this->_getRequest()->getActionName();
  }

  protected function _getModuleName() {
    return $this->_getRequest()->getModuleName();
  }

  protected function _getRouteName() {
    return $this->_getRequest()->getRouteName();
  }

  protected function _getCustomer() {
    return Mage::helper('customer')->getCustomer();
  }

  protected function _getBreadcrumb() {
    return Mage::helper('catalog')->getBreadcrumbPath();
  }

  protected function _getCategory($category_id) {
    return Mage::getModel('catalog/category')->load($category_id);
  }

  protected function _getCurrentProduct() {
    return Mage::registry('current_product');
  }

  protected function _getProduct($productId) {
    return Mage::getModel('catalog/product')->load($productId);
  }

  protected function _getCurrentCategory() {
    return Mage::registry('current_category');
  }

  protected function _getCatalogSearch() {
    return Mage::getSingleton('catalogsearch/advanced');
  }

  protected function _getCheckoutSession() {
    return Mage::getSingleton('checkout/session');
  }

  protected function _getSalesOrder() {
    return Mage::getModel('sales/order');
  }

  protected function _getOrderAddress() {
    return Mage::getModel('sales/order_address');
  }

  /*
  * Determine which page type we're on
  */

  public function _isHome() {
    if (Mage::app()->getRequest()->getRequestString() == "/") {
      return true;
    } else {
      return false;
    }
  }

  public function _isContent() {
    if ($this->_getModuleName() == 'cms') {
      return true;
    } else {
      return false;
    }
  }

  public function _isCategory() {
    if ($this->_getControllerName() == 'category') {
      return true;
    } else {
      return false;
    }
  }

  public function _isSearch() {
    if ($this->_getModuleName() == 'catalogsearch') {
      return true;
    } else {
      return false;
    }
  }

  public function _isProduct() {
    $onCatalog = false;
    if(Mage::registry('current_product')) {
        $onCatalog = true;
    }
    return $onCatalog;
  }

  public function _isBasket() {
    $request = $this->_getRequest();
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == 'checkout' && $controller == 'cart' && $action == 'index'){
      return true;
    } else {
      return false;
    }
  }

  public function _isCheckout() {
    if (strpos($this->_getModuleName(), 'checkout') !== false && $this->_getActionName() != 'success') {
      return true;
    } else {
      return false;
    }
  }

  public function _isConfirmation() {
    // default controllerName is "onepage"
    // relax the check, only check if contains checkout
    // somecheckout systems has different prefix/postfix,
    // but all contains checkout
    if (strpos($this->_getModuleName(), 'checkout') !== false && $this->_getActionName() == "success") {
      return true;
    } else {
      return false;
    }
  }


/*
 * Get information on pages to pass to front end
 */

  public function getVersion() {
    return $this->_version;
  }

  public function getUser() {
    return $this->_user;
  }

  public function getPage() {
    return $this->_page;
  }

  public function getProduct() {
    return $this->_product;
  }

  public function getBasket() {
    return $this->_basket;
  }

  public function getTransaction() {
    return $this->_transaction;
  }

  public function getListing() {
    return $this->_listing;
  }

  public function getMageVersion() {
    return Mage::getVersion();
  }

  public function getEvents() {
    return array();
  }


/*
 * Set the model attributes to be passed front end
 */

  public function _getPageType() {
    if ($this->_isHome()) {
      return 'home';
    } elseif ($this->_isContent()) {
      return 'content';
    } elseif ($this->_isCategory()) {
      return 'category';
    } elseif ($this->_isSearch()) {
      return 'search';
    } elseif ($this->_isProduct()) {
      return 'product';
    } elseif ($this->_isBasket()) {
      return 'basket';
    } elseif ($this->_isCheckout()) {
      return 'checkout';
    } elseif ($this->_isConfirmation()) {
      return 'confirmation';
    } else {
      return $this->_getModuleName();
    }
  }

  public function _getPageBreadcrumb() {
    $arr = $this->_getBreadcrumb();
    $breadcrumb = array();
    foreach ($arr as $category) {
      $breadcrumb[] = $category['label'];
    }
    return $breadcrumb;
  }

  public function _setPage() {
    $this->_page = array();
    $this->_page['type'] = $this->_getPageType();
    // WARNING: `page.category` will be deprecated in the next release
    //          We will follow the specification that uses `page.type`
    //          Please migrate any frontend JavaScripts using this `universal_variable.page.category` variable
    $this->_page['category'] = $this->_page['type'];
    $this->_page['breadcrumb'] = $this->_getPageBreadcrumb();
  }

  // Set the user info
  public function _setUser() {
    $this->_user = array();
    $user    = $this->_getCustomer();
    $user_id = $user->getEntityId();

    if ($this->_isConfirmation()) {
      $orderId = $this->_getCheckoutSession()->getLastOrderId();
      if ($orderId) {
        $order = $this->_getSalesOrder()->load($orderId);
        $email = $order->getCustomerEmail();
      }
    } else {
      $email = $user->getEmail();
    }

    if ($email) {
      $this->_user['email'] = $email;
    }

    if ($user_id) {
      $this->_user['user_id'] = (string) $user_id;
    }
    $this->_user['returning'] = $user_id ? true : false;
    $this->_user['language']  = Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId());;

  }

  public function _getAddress($address) {
    $billing = array();
    if ($address) {
      $billing['name']     = $address->getName();
      $billing['address']  = $address->getStreetFull();
      $billing['city']     = $address->getCity();
      $billing['postcode'] = $address->getPostcode();
      $billing['country']  = $address->getCountry();
      $state = $address->getRegion();
      $billing['state']    = $state ? $state : '';
    }
    return $billing;
  }

  public function _getProductStock($product) {
    return (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
  }

  public function _getCurrency() {
    return Mage::app()->getStore()->getCurrentCurrencyCode();
  }

  public function _getProductModel($product) {
    $product_model = array();
    $product_model['id']       = $product->getId();
    $product_model['sku_code'] = $product->getSku();
    $product_model['url']      = $product->getProductUrl();
    $product_model['name']     = $product->getName();
    $product_model['unit_price']      = (float) $product->getPrice();
    $product_model['unit_sale_price'] = (float) $product->getFinalPrice();
    $product_model['currency']        = $this->_getCurrency();
    $product_model['description']     = strip_tags($product->getShortDescription());
    $product_model['stock']           = (int) $this->_getProductStock($product);

    $categories = $this->_getProductCategories($product);
    if (isset($categories[0])) {
      $product_model['category'] = $categories[0];
    }
    if (isset($categories[1])) {
      $product_model['subcategory'] = $categories[1];
    }

    return $product_model;
  }

  public function _getProductCategories($product) {
    $cats = $product->getCategoryIds();
    if ($cats) {
      $category_names = array();
      foreach ($cats as $category_id) {
        $_cat = $this->_getCategory($category_id);
        $category_names[] = $_cat->getName();
      }
      return $category_names;
    } else {
      return false;
    }
  }

  public function _getLineItems($items, $page_type) {
    $line_items = array();
    foreach($items as $item) {
      $productId = $item->getProductId();
      $product   = $this->_getProduct($productId);
      // product needs to be visible
      if ($product->isVisibleInSiteVisibility()) {
        $litem_model             = array();
        $litem_model['product']  = $this->_getProductModel($product);


        $litem_model['subtotal'] = (float) $item->getRowTotalInclTax();
        $litem_model['total_discount'] = (float) $item->getDiscountAmount();

        if ($page_type == 'basket') {
          $litem_model['quantity'] = (float) $item->getQty();
        } else {
          $litem_model['quantity'] = (float) $item->getQtyOrdered();
        }

        // Recalculate unit_sale_price after voucher applied Github: #35
        // https://github.com/QubitProducts/UniversalVariable-Magento-Extension/issues/35
        $unit_sale_price_after_discount = $litem_model['product']['unit_sale_price'];
        $unit_sale_price_after_discount = 
          $unit_sale_price_after_discount - ($litem_model['total_discount'] / $litem_model['quantity']);
        $litem_model['product']['unit_sale_price'] = $unit_sale_price_after_discount;

        array_push($line_items, $litem_model);
      }
    }
    return $line_items;
  }

  public function _setListing() {
    $this->_listing = array();
    if ($this->_isCategory()) {
      $category = $this->_getCurrentCategory();
    } elseif ($this->_isSearch()) {
      $category = $this->_getCatalogSearch();
      if (isset($_GET['q'])) {
        $this->_listing['query'] = $_GET['q'];
      }
    }
  }

  public function _setProduct() {
    $product  = $this->_getCurrentProduct();
    if (!$product) return false;
    $this->_product = $this->_getProductModel($product);
  }

  public function _setBasket() {
    $cart = $this->_getCheckoutSession();
    
    if (!isset($cart)) {
      return;
    }

    $basket = array();
    $quote = $cart->getQuote();

    // Set normal params
    $basket_id = $this->_getCheckoutSession()->getQuoteId();
    if ($basket_id) {
      $basket['id'] = (string) $basket_id;
    }
    $basket['currency']             = $this->_getCurrency();
    $basket['subtotal']             = (float) $quote->getSubtotal();
    $basket['tax']                  = (float) $quote->getShippingAddress()->getTaxAmount();
    $basket['subtotal_include_tax'] = (boolean) $this->_doesSubtotalIncludeTax($quote, $basket['tax']);
    $basket['shipping_cost']        = (float) $quote->getShippingAmount();
    $basket['shipping_method']      = $this->_extractShippingMethod($quote);
    $basket['total']                = (float) $quote->getGrandTotal();

    // Line items
    $items = $quote->getAllItems();
    $basket['line_items'] = $this->_getLineItems($items, 'basket');

    $this->_basket = $basket;
  }

  public function _doesSubtotalIncludeTax($order, $tax) {
    /* Conditions:
        - if tax is zero, then set to false
        - Assume that if grand total is bigger than total after subtracting shipping, then subtotal does NOT include tax
    */
    $grandTotalWithoutShipping = $order->getGrandTotal() - $order->getShippingAmount();
    if ($tax == 0 || $grandTotalWithoutShipping > $order->getSubtotal()) {
      return false;
    } else {
      return true;
    }
  }

  public function _extractShippingMethod($order) {
    $shipping_method = $order->getShippingMethod();
    return $shipping_method ? $shipping_method : '';
  }

  public function _setTranscation() {
    $orderId = $this->_getCheckoutSession()->getLastOrderId();
    if ($orderId) {
      $transaction = array();
      $order       = $this->_getSalesOrder()->load($orderId);

      // Get general details
      $transaction['order_id']             = $order->getIncrementId();
      $transaction['currency']             = $this->_getCurrency();
      $transaction['subtotal']             = (float) $order->getSubtotal();
      $transaction['tax']                  = (float) $order->getTaxAmount();
      $transaction['subtotal_include_tax'] = $this->_doesSubtotalIncludeTax($order, $transaction['tax']);
      $transaction['payment_type']         = $order->getPayment()->getMethodInstance()->getTitle();
      $transaction['total']                = (float) $order->getGrandTotal();

      $voucher                             = $order->getCouponCode();
      $transaction['voucher']              = $voucher ? $voucher : "";
      $voucher_discount                    = -1 * $order->getDiscountAmount();
      $transaction['voucher_discount']     = $voucher_discount ? $voucher_discount : 0;

      
      $transaction['shipping_cost']   = (float) $order->getShippingAmount();
      $transaction['shipping_method'] = $this->_extractShippingMethod($order);

      // Get addresses
      if (method_exists($order,'getShippingAddress')) {
        $shippingId        = $order->getShippingAddress()->getId();
        $shippingAddress   = $order->getShippingAddress();
        $transaction['delivery'] = $this->_getAddress($shippingAddress);
        $address           = $this->_getOrderAddress()->load($shippingId);
      }

      $billingAddress    = $order->getBillingAddress();
      $transaction['billing']  = $this->_getAddress($billingAddress);

      // Get items
      $items                     = $order->getAllItems();
      $line_items                = $this->_getLineItems($items, 'transaction');
      $transaction['line_items'] = $line_items;

      $this->_transaction = $transaction;
    }
  }

  public function setUniversalVariable($observer) {
    $this->_setUser();
    $this->_setPage();

    if ($this->_isProduct()) {
      $this->_setProduct();
    }

    if ($this->_isCategory()) {
      $this->_setListing();
    }

    if ($this->_isCategory() || $this->_isSearch()) {
      $this->_setListing();
    }

    if (!$this->_isConfirmation()) {
      $this->_setBasket();
    }

    if ($this->_isConfirmation()) {
      $this->_setTranscation();
    }

    return $this;
  }

}
?>