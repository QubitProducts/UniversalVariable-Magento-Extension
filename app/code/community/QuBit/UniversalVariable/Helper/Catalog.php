<?php
class QuBit_UniversalVariable_Helper_Catalog extends QuBit_UniversalVariable_Helper_Data
{
    /**
     * @return Mage_Catalog_Model_Product|null
     */
    protected function _getCurrentProduct()
    {
        return Mage::registry('current_product');
    }
    /**
     * get category by Id
     * @param int $categoryId
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory($categoryId)
    {
        return Mage::getModel('catalog/category')->load($categoryId);
    }
    /**
     * get product stock quantity
     * @param Mage_Catalog_Model_Product $product
     * @return number
     */
    protected function _getProuctStock(Mage_Catalog_Model_Product $product)
    {
        return (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
    }
    /**
     * @todo refactor to load multiple category names in one query
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getProductCategories(Mage_Catalog_Model_Product $product)
    {
        $cats = $product->getCategoryIds();
        $categoryNames = array();
        if ($cats) {
            foreach ($cats as $categoryId) {
                $_cat = $this->_getCategory($categoryId);
                $categoryNames[] = $_cat->getName();
                if (count($categoryNames) == 2) {
                    //current spec only supports category and subcategory
                    //avoid loading unnecessary entities
                    break;
                }
            }
        }
        return $categoryNames;
    }
    /**
     * @return array | false
     */
    public function getCurrentProductUvArray() {
        $product  = $this->_getCurrentProduct();
        if (!$product) {
            return false;
        }
        return $this->_getProductModel($product);
    }
    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductUVArray(Mage_Catalog_Model_Product $product)
    {
        return $this->_getProductModel($product);
    }
    /**
     * @return array
     */
    public function getListingUvArray()
    {
        $info = array();
        if ($this->isSearch()) {
            $query    = Mage::app()->getRequest()->getParam('q', false);
            if (isset($query)) {
                $info['query'] = $query;
            }
        }
        return $info;
    }
    /**
     * get product model
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getProductModel(Mage_Catalog_Model_Product $product)
    {
        $info = array(
            'id'             => $product->getId(),
            'sku_code'       => $product->getSku(),
            'url'            => $product->getProductUrl(),
            'name'           => $product->getName(),
            'unit_price'      => (float) $product->getPrice(),
            'unit_sale_price' => (float) $product->getFinalPrice(),
            'currency'        => $this->_getStoreCurrency(),
            'description'     => strip_tags($product->getShortDescription()),
            'stock'           => (int) $this->_getProuctStock($product),
        );
        
        $categories = $this->_getProductCategories($product);
        if (isset($categories[0])) {
            $info['category'] = $categories[0];
        }
        if (isset($categories[1])) {
            $info['subcategory'] = $categories[1];
        }
        
        return $info;
    }
}