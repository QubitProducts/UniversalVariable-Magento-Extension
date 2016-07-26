<?php

class QuBit_UniversalVariable_Model_Observer
{
    /**
     * Observes http_response_send_before
     * Replaces placeholder with actual listing information
     *
     * @param Varien_Event_Observer $observer
     */
    public function replaceListingPlaceholder(Varien_Event_Observer $observer)
    {
        /** @var QuBit_UniversalVariable_Helper_Data $helper */
        $helper = Mage::helper('qubituv');

        $layout = Mage::app()->getLayout();

        /** @var Mage_Catalog_Block_Product_List $block */
        $block = $layout->getBlock($helper->getCategoryProductListBlockName());
        if (!$block instanceof Mage_Catalog_Block_Product_List) {
            $block = $layout->getBlock($helper->getSearchProductListBlockName());
            if (!$block instanceof Mage_Catalog_Block_Product_List) {
                return;
            }
        }

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $block->getLoadedProductCollection();

        // add an extra check if for any reason the collection wasn't loaded yet
        // better not show uv data then mess up the collection order & pagination
        if (!$collection->isLoaded()) {
            return;
        }

        $uv = $helper->getUv();

        $listing = array(
            'result_count' => $collection->getSize(),
            'items' => array()
        );

        foreach($collection as $product) {
            $listing['items'][] = $uv->getProductData($product);
        }

        $toolbar = $block->getToolbarBlock();
        $listing['sort_by'] = $toolbar->getCurrentOrder() . '_' . $toolbar->getCurrentDirection();
        $listing['layout'] = $toolbar->getCurrentMode();

        $listing = Zend_Json::encode($listing);
        $replace = 'uvTemp.listing = $H(uvTemp.listing || {}).merge(' . $listing . ').toObject();' . PHP_EOL;

        $body = str_replace(
            $helper->getListingReplacementString(),
            $replace,
            $observer->getEvent()->getResponse()->getBody()
        );

        $observer->getEvent()->getResponse()->setBody($body);
    }
}