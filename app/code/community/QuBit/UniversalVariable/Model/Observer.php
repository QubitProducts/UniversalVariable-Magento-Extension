<?php
class QuBit_UniversalVariable_Model_Observer
{
    /**
     * Is Enabled Full Page Cache
     *
     * @var bool
     */
    protected $_isEnabled;
    
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_isEnabled = Mage::app()->useCache('full_page');
    }
    
    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return $this->_isEnabled;
    }
    /**
     * Set cart hash in cookie on quote change
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_PageCache_Model_Observer
     */
    public function registerQuoteChange(Varien_Event_Observer $observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }
        $cacheId = QuBit_UniversalVariable_Model_Container_Uv::getCacheId();
        Enterprise_PageCache_Model_Cache::getCacheInstance()->remove($cacheId);
        return $this;
    }
}