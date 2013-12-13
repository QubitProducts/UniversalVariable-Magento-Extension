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
    $this->_page = Mage::helper('universal_variable_main')->getPageUvArray();
  }

  // Set the user info
  public function _setUser() {
    $this->_user = Mage::helper('universal_variable_main/customer')->getUvArray();
  }

  public function _getAddress($address) {
    $billing = array();
    if ($address) {
      $billing['name']     = $address->getName();
      $billing['address']  = $address->getStreetFull();
      $billing['city']     = $address->getCity();
      $billing['postcode'] = $address->getPostcode();
      $billing['country']  = $address->getCountry();
    }
    // TODO: $billing['state']
    return $billing;
  }

  public function _getProuctStock($product) {
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
    $product_model['stock']           = (int) $this->_getProuctStock($product);

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

        array_push($line_items, $litem_model);
      }
    }
    return $line_items;
  }

  public function _setListing() {
    $this->_listing = Mage::helper('universal_variable_main/catalog')->getListingUvArray();
  }

  public function _setProduct() {
    $this->_product = Mage::helper('universal_variable_main/catalog')->getCurrentProductUvArray();
  }

  public function _setBasket() {
    $this->_basket = Mage::helper('universal_variable_main/cart')->getCartUvArray();
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

  public function _setTranscation() {
    $orderId = $this->_getCheckoutSession()->getLastOrderId();
    if ($orderId) {
      $this->_transaction = Mage::helper('universal_variable_main/cart')->getTransactionUvArray();
    }
  }

  public function setUniversalVariable($observer) {
    $this->_setUser();
    $this->_setPage();

    if ($this->_isProduct()) {
      $this->_setProduct();
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