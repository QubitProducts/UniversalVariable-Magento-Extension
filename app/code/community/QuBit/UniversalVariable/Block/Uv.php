<?php 
class QuBit_UniversalVariable_Block_Uv extends Mage_Core_Block_Template
{
    /**
     * @return QuBit_UniversalVariable_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('universal_variable_main');
    }
    /**
     * @todo implement
     * @return array
     */
    public function getEvents()
    {
        return array();
    }
}
