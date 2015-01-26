<?php

class QuBit_UniversalVariable_Model_Observer
{
    /**
     * Observes controller_action_layout_render_before
     * @param Varien_Event_Observer $observer
     */
    public function processListingData(Varien_Event_Observer $observer)
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

        $uv = $helper->getUv();
        $listing = $uv->getListing();

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $block->getLoadedProductCollection();

        $listing['result_count'] = $collection->getSize();
        $listing['items'] = array();
        foreach($collection as $product) {
            $listing['items'][] = $uv->getProductData($product);
        }

        $toolbar = $block->getToolbarBlock();
        $listing['sort_by'] = $toolbar->getCurrentOrder() . '_' . $toolbar->getCurrentDirection();
        $listing['layout'] = $toolbar->getCurrentMode();

        $uv->setListing($listing);
    }
}