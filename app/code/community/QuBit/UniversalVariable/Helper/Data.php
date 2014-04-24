<?php

class QuBit_UniversalVariable_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_version = '1.2';
    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('qubit/qubit_universal_variable_enabled');
    }
    /**
     * @return boolean
     */
    public function opentagEnabled()
    {
        return Mage::getStoreConfigFlag('qubit/qubit_opentag_enabled');
    }
    /**
     * @return str
     */
    public function getOpentagContainerId()
    {
        return Mage::getStoreConfig('qubit/qubit_opentag_container_id');
    }
    /**
     * @return boolean
     */
    public function isOpentagAsync()
    {
        return Mage::getStoreConfigFlag('qubit/qubit_opentag_async');
    }
    /**
     * @todo refactor to try and get path from breadcrumb block, if it exists instead 
     */
    protected function _getBreadcrumb()
    {
        return Mage::helper('catalog')->getBreadcrumbPath();
    }
    /**
     * @return array
     */
    public function getPageBreadcrumb()
    {
        $arr        = $this->_getBreadcrumb();
        $breadcrumb = array();
        foreach ($arr as $category) {
            $breadcrumb[] = $category['label'];
        }
        return $breadcrumb;
    }
    /**
     * get page type array
     * @return array
     */
    public function getPageUvArray()
    {
        $info = array();
        $info['type'] = $this->getPageType();
        // WARNING: `page.category` will be deprecated in the next release
        //          We will follow the specification that uses `page.type`
        //          Please migrate any frontend JavaScripts using this `universal_variable.page.category` variable
        $info['category']   = $info['type'];
        $info['breadcrumb'] = $this->getPageBreadcrumb();
        return $info;
    }
    /**
     * @return string
     */
    public function getPageType()
    {
        if ($this->isHome()) {
            return 'home';
        } elseif ($this->isContent()) {
            return 'content';
        } elseif ($this->isCategory()) {
            return 'category';
        } elseif ($this->isSearch()) {
            return 'search';
        } elseif ($this->isProduct()) {
            return 'product';
        } elseif ($this->isBasket()) {
            return 'basket';
        } elseif ($this->isCheckout()) {
            return 'checkout';
        } elseif ($this->isConfirmation()) {
            return 'confirmation';
        } else {
            return $this->_getModuleName();
        }
    }
    /**
     * @return boolean
     */
    public function isHome()
    {
        $urlModel = Mage::getModel('core/url');
        return $urlModel->getUrl('') == $urlModel->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
    /**
     * @return boolean
     */
    public function isContent()
    {
        if ($this->_getModuleName() == 'cms') {
            return true;
        }
        return false;
    }
    /**
     * 
     * @return boolean
     */
    public function isCategory()
    {
        if ($this->_getControllerName() == 'category') {
            return true;
        }
        return false;
    }
    /**
     * @return boolean
     */
    public function isSearch()
    {
        if ($this->_getModuleName() == 'catalogsearch') {
            return true;
        }
        return false;
    }
    /**
     * @return boolean
     */
    public function isProduct()
    {
        if (Mage::registry('current_product')) {
            return true;
        }
        return false;
    }
    /**
     * @return boolean
     */
    public function isBasket()
    {
        $request    = $this->_getRequest();
        $module     = $request->getModuleName();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        if ($module == 'checkout' && $controller == 'cart' && $action == 'index'){
            return true;
        }
        return false;
    }
    /**
     * @return boolean
     */
    public function isCheckout()
    {
        if (strpos($this->_getModuleName(), 'checkout') !== false && $this->_getActionName() != 'success') {
            return true;
        }
        return false;
    }
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
     * get request controller name
     * @return str
     */
    protected function _getControllerName()
    {
        return $this->_getRequest()->getControllerName();
    }
    /**
     * get request action name
     * @return str
     */
    protected function _getActionName()
    {
        return $this->_getRequest()->getActionName();
    }
    /**
     * get store currency code
     * @return str
     */
    protected function _getStoreCurrency() {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }
}
