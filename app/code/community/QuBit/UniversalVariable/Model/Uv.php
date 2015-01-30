<?php

/**
 * Class QuBit_UniversalVariable_Model_Uv
 *
 * @method QuBit_UniversalVariable_Model_Uv setVersion() setVersion(mixed $version)
 * @method QuBit_UniversalVariable_Model_Uv setEvents() setEvents(array $events)
 * @method QuBit_UniversalVariable_Model_Uv setProduct() setProduct($data)
 * @method QuBit_UniversalVariable_Model_Uv setPage() setPage($data)
 * @method QuBit_UniversalVariable_Model_Uv setUser() setUser($data)
 * @method QuBit_UniversalVariable_Model_Uv setListing() setListing($data)
 * @method QuBit_UniversalVariable_Model_Uv setBasket() setBasket($data)
 * @method QuBit_UniversalVariable_Model_Uv setTransaction() setTransaction($data)
 * @method QuBit_UniversalVariable_Model_Uv setMagentoVersion() setMagentoVersion(mixed $version)
 */

class QuBit_UniversalVariable_Model_Uv extends Varien_Object
{

    /**
     * This is the UV specification Version
     * @var string
     * @url http://tools.qubitproducts.com/uv/developers/specification
     */
    protected $_version = "1.2.2";

    protected function _construct()
    {
        $this
            ->setVersion($this->_version)
            ->setEvents(array())
            ->_initUser()
            ->_initPage();

        if ($this->helper()->isProductPage()) {
            $this->_initProduct();
        }

        if ($this->helper()->shouldShowMagentoVersion()) {
            $this->setMagentoVersion(Mage::getVersion());
        }

        if ($this->helper()->isCategoryPage() || $this->helper()->isSearchPage()) {
            $this->_initListing();
        }

        //if ($this->helper()->isCartPage()) {
        $this->_initCart();
        //}

        if ($this->helper()->isConfirmationPage()) {
            $this->_initTransaction();
        }
    }

    /**
     * @return string json encoded UV data
     */
    public function getUvData()
    {
        $data = $this->toArray(array('version', 'magento_version', 'page', 'user', 'product', 'basket', 'listing', 'transaction', 'events'));
        $data = array_filter($data);

        $transport = new Varien_Object($data);
        Mage::dispatchEvent('qubituv_before_to_json', array('uv' => $transport));

        return Zend_Json::encode($transport->getData());
    }

    /**
     * @return QuBit_UniversalVariable_Helper_Data
     */
    protected function helper() {
        return Mage::helper('qubituv');
    }

    protected function _initPage()
    {
        $breadcrumb = array();
        foreach (Mage::helper('catalog')->getBreadcrumbPath() as $category) {
            $breadcrumb[] = $category['label'];
        }

        if ($this->helper()->isHomePage()) {
            $type = 'home';
        } elseif ($this->helper()->isContentPage()) {
            $type = 'content';
        } elseif ($this->helper()->isCategoryPage()) {
            $type = 'category';
        } elseif ($this->helper()->isSearchPage()) {
            $type = 'search';
        } elseif ($this->helper()->isProductPage()) {
            $type = 'product';
        } elseif ($this->helper()->isCartPage()) {
            $type = 'basket';
        } elseif ($this->helper()->isCheckoutPage()) {
            $type = 'checkout';
        } elseif ($this->helper()->isConfirmationPage()) {
            $type = 'confirmation';
        } else {
            $type = trim(Mage::app()->getRequest()->getRequestUri(), '/');
        }

        $this->setPage(array(
            'type' => $type,
            'breadcrumb' => $breadcrumb,
        ));

        return $this;
    }

    protected function _initUser()
    {
        /** @var Mage_Customer_Model_Customer $user */
        $user = Mage::helper('customer')->getCustomer();

        if ($this->helper()->isConfirmationPage()) {
            if ($orderId = Mage::getSingleton('checkout/session')->getLastOrderId()) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $email = $order->getCustomerEmail();
                $name = trim($order->getCustomerFirstname() . ' ' . $order->getCustomerLastname());
            }
        } else {
            $email = $user->getEmail();
            $name = trim($user->getFirstname() . ' ' . $user->getLastname());
        }

        $data = array();
        if (!empty($email)) {
            $data['email'] = $email;
        }

        if (!empty($name)) {
            $data['name'] = $name;
        }

        $data['has_transacted'] = false;
        if ($user->getId()) {
            $data['user_id'] = $this->helper()->shouldUseRealUserId() ?
                (string)$user->getId() :
                md5($user->getId() . $user->getEmail());

            $data['has_transacted'] = $this->helper()->hasCustomerTransacted($user);
        }

        $data['customer_group'] = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $data['returning'] = $user->getId() ? true : false;
        $data['language'] = Mage::getStoreConfig('general/locale/code');

        $this->setUser($data);
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract $address
     * @return array
     */
    protected function _getAddressData(Mage_Customer_Model_Address_Abstract $address)
    {
        $data = array();
        if ($address) {
            $data['name'] = $address->getName();
            $data['address'] = $address->getStreetFull();
            $data['city'] = $address->getCity();
            $data['postcode'] = $address->getPostcode();
            $data['country'] = $address->getCountry();
            $data['state'] = $address->getRegion() ? $address->getRegion() : '';
        }

        return $data;
    }

    /**
     * @return string
     */
    protected function _getCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductData(Mage_Catalog_Model_Product $product)
    {
        $id = $product->getId();
        if (!$this->helper()->shouldUseRealProductId()) {
            $id = $product->getSku() ? $product->getSku() : md5($id);
        }
        $data = array(
            'id' => $id,
            'url' => $product->getProductUrl(),
            'name' => $product->getName(),
            'unit_price' => (float)$product->getPrice(),
            'unit_sale_price' => (float)$product->getFinalPrice(),
            'currency' => $this->_getCurrency(),
            'description' => strip_tags($product->getShortDescription()),
            'sku_code' => $product->getSku()
        );

        if ($this->helper()->shouldShowProductStockInfo()) {
            $data['stock'] = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
        }

        $catIndex = $catNames = array();
        $limit = 2; $k = 0;
        foreach ($product->getCategoryIds() as $catId) {
            if (++$k > $limit) {
                break;
            }
            if (!isset($catIndex[$catId])) {
                $catIndex[$catId] = Mage::getModel('catalog/category')->load($catId);
            }
            $catNames[] = $catIndex[$catId]->getName();
        }

        if (isset($catNames[0])) {
            $data['category'] = $catNames[0];
        }
        if (isset($catNames[1])) {
            $data['subcategory'] = $catNames[1];
        }

        return $data;
    }

    protected function _getLineItems($items, $pageType)
    {
        $data = array();
        foreach ($items as $item) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            // product needs to be visible
            if (!$product->isVisibleInSiteVisibility()) {
                continue;
            }

            $line = array();
            $line['product'] = $this->getProductData($product);
            $line['subtotal'] = (float)$item->getRowTotalInclTax();
            $line['total_discount'] = (float)$item->getDiscountAmount();

            $line['quantity'] = $pageType == 'basket' ? (float)$item->getQty() : (float)$item->getQtyOrdered();

            // Recalculate unit_sale_price after voucher applied Github: #35
            // https://github.com/QubitProducts/UniversalVariable-Magento-Extension/issues/35
            $line['product']['unit_sale_price'] -= $line['total_discount'] / $line['quantity'];

            $data[] = $line;
        }

        return $data;
    }

    /**
     * None of the listing variables can be properly set here
     * since the product collection is being loaded
     * in the Mage_Catalog_Block_Product_List::_beforeToHtml method
     *
     * @see QuBit_UniversalVariable_Model_Observer::processListingData
     */
    protected function _initListing()
    {
        $data = array();
        if ($this->helper()->isSearchPage()) {
            $data['query'] = Mage::helper('catalogsearch')->getQueryText();
        }

        $this->setListing($data);
        return $this;
    }

    protected function _initProduct()
    {
        $product = Mage::registry('current_product');
        if (!$product instanceof Mage_Catalog_Model_Product || !$product->getId()) {
            return false;
        }

        $this->setProduct($this->getProductData($product));
        return $this;
    }

    protected function _initCart()
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if ($quote->getItemsCount() > 0) {

            $data = array();
            if ($quote->getId()) {
                $data['id'] = (string)$quote->getId();
            }

            $data['currency'] = $this->_getCurrency();
            $data['subtotal'] = (float)$quote->getSubtotal();
            $data['tax'] = (float)$quote->getShippingAddress()->getTaxAmount();
            $data['subtotal_include_tax'] = (boolean)$this->_doesSubtotalIncludeTax($quote, $data['tax']);
            $data['shipping_cost'] = (float)$quote->getShippingAmount();
            $data['shipping_method'] = $quote->getShippingMethod() ? $quote->getShippingMethod() : '';
            $data['total'] = (float)$quote->getGrandTotal();

            // Line items
            $data['line_items'] = $this->_getLineItems($quote->getAllVisibleItems(), 'basket');

            $this->setBasket($data);
            return $this;
        }
    }

    /**
     * @param $order
     * @param $tax
     * @return bool
     * @todo implement this Magento-style
     */
    protected function _doesSubtotalIncludeTax($order, $tax)
    {
        /* Conditions:
            - if tax is zero, then set to false
            - Assume that if grand total is bigger than total after subtracting shipping, then subtotal does NOT include tax
        */
        $grandTotalWithoutShipping = $order->getGrandTotal() - $order->getShippingAmount();
        return !($tax == 0 || $grandTotalWithoutShipping > $order->getSubtotal());
    }

    protected function _initTransaction()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if (!$orderId) {
            return $this;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        $transaction = array();

        $transaction['order_id'] = $order->getIncrementId();
        $transaction['currency'] = $this->_getCurrency();
        $transaction['subtotal'] = (float)$order->getSubtotal();
        $transaction['tax'] = (float)$order->getTaxAmount();
        $transaction['subtotal_include_tax'] = $this->_doesSubtotalIncludeTax($order, $transaction['tax']);
        $transaction['payment_type'] = $order->getPayment()->getMethodInstance()->getTitle();
        $transaction['total'] = (float)$order->getGrandTotal();

        if ($order->getCouponCode()) {
            $transaction['voucher'] = array($order->getCouponCode());
        }

        if ($order->getDiscountAmount() > 0) {
            $transaction['voucher_discount'] = -1 * $order->getDiscountAmount();
        }

        $transaction['shipping_cost'] = (float)$order->getShippingAmount();
        $transaction['shipping_method'] = $order->getShippingMethod() ? $order->getShippingMethod() : '';

        // Get addresses
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress instanceof Mage_Sales_Model_Order_Address) {
            $transaction['delivery'] = $this->_getAddressData($shippingAddress);
        }

        $transaction['billing'] = $this->_getAddressData($order->getBillingAddress());

        // Get items
        $transaction['line_items'] = $this->_getLineItems($order->getAllVisibleItems(), 'transaction');

        $this->setTransaction($transaction);
        return $this;
    }

}
