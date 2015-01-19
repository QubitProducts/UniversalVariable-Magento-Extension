<?php

class QuBit_UniversalVariable_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CONFIG_KEY_OPENTAG_ENABLED       = 'qubituv/opentag/enabled';
    const CONFIG_KEY_OPENTAG_CONTAINER_ID  = 'qubituv/opentag/container_id';
    const CONFIG_KEY_OPENTAG_USE_ASYNC     = 'qubituv/opentag/async';

    const CONFIG_KEY_UV_ENABLED            = 'qubituv/settings/enabled';

    const CONFIG_KEY_ADV_PROD_ID           = 'qubituv/advanced/show_product_id';
    const CONFIG_KEY_ADV_STOCK             = 'qubituv/advanced/show_stock_info';
    const CONFIG_KEY_ADV_USER_ID           = 'qubituv/advanced/show_user_id';
    const CONFIG_KEY_ADV_CAT_BLOCK_NAME    = 'qubituv/advanced/category_product_list_block';
    const CONFIG_KEY_ADV_SRCH_BLOCK_NAME   = 'qubituv/advanced/search_product_list_block';
    const CONFIG_KEY_ADV_MAGENTO_VERSION   = 'qubituv/advanced/show_magento_version';

    /**
     * @return mixed
     */
    public function getContainerId()
    {
        return Mage::getStoreConfig(self::CONFIG_KEY_OPENTAG_CONTAINER_ID);
    }

    /**
     * @return bool
     */
    public function opentagEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_OPENTAG_ENABLED) && $this->getContainerId();
    }

    /**
     * @return bool
     */
    public function uvEnabled()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_UV_ENABLED);
    }

    /**
     * @return bool
     */
    public function useAsync()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_OPENTAG_USE_ASYNC);
    }

    /**
     * @return bool
     */
    public function shouldUseRealProductId()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_ADV_PROD_ID);
    }

    /**
     * @return bool
     */
    public function shouldShowProductStockInfo()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_ADV_STOCK);
    }

    /**
     * @return bool
     */
    public function shouldUseRealUserId()
    {
        return Mage::getStoreConfigFlag(self::CONFIG_KEY_ADV_USER_ID);
    }

    /**
     * @return mixed
     */
    public function getCategoryProductListBlockName()
    {
        return Mage::getStoreConfig(self::CONFIG_KEY_ADV_CAT_BLOCK_NAME);
    }

    /**
     * @return mixed
     */
    public function getSearchProductListBlockName()
    {
        return Mage::getStoreConfig(self::CONFIG_KEY_ADV_SRCH_BLOCK_NAME);
    }

    /**
     * @return bool
     */
    public function shouldShowMagentoVersion()
    {
        return Mage::getStoreConfig(self::CONFIG_KEY_ADV_MAGENTO_VERSION);
    }

    /**
     * @return QuBit_UniversalVariable_Model_Uv
     */
    public function getUv()
    {
        return Mage::getSingleton('qubituv/uv');
    }

    /**
     * @return string
     */
    public function getRequestPath()
    {
        $r = Mage::app()->getRequest();
        $path = array($r->getRouteName(), $r->getControllerName(), $r->getActionName());
        return implode('_', $path);
    }

    /**
     * @return bool
     */
    public function isHomePage()
    {
        return 'cms_index_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isContentPage()
    {
        return 'cms_page_view' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isCategoryPage()
    {
        return 'catalog_category_view' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isSearchPage()
    {
        return 'catalogsearch_advanced_result' == $this->getRequestPath()
            || 'catalogsearch_result_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isProductPage()
    {
        $p = Mage::registry('current_product');
        return $p instanceof Mage_Catalog_Model_Product && $p->getId();
    }

    /**
     * @return bool
     */
    public function isCartPage()
    {
        return 'checkout_cart_index' == $this->getRequestPath();
    }

    /**
     * @return bool
     */
    public function isCheckoutPage()
    {
        $r = Mage::app()->getRequest();
        return false !== strpos($r->getRouteName(), 'checkout') && 'success' != $r->getActionName();
    }

    public function isConfirmationPage()
    {
        /*
         * default controllerName is "onepage"
         * relax the check, only check if contains checkout
         * somecheckout systems has different prefix/postfix,
         * but all contains checkout
         */
        $r = Mage::app()->getRequest();
        return false !== strpos($r->getRouteName(), 'checkout') && 'success' == $r->getActionName();
    }

    /**
     * @param Mage_Customer_Model_Customer $customer
     * @return bool
     */
    public function hasCustomerTransacted(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->getId()) {
            return false;
        }

        /** @var Mage_Core_Model_Resource $r */
        $r = Mage::getSingleton('core/resource');

        $read = $r->getConnection('core_read');
        $select = $read->select()
            ->from($r->getTableName('sales/order'), array('c' => new Zend_Db_Expr('COUNT(*)')))
            ->where('customer_id = ?', $customer->getId());

        return $read->fetchOne($select) > 0;
    }
}