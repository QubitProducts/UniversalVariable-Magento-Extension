<?php
class QuBit_UniversalVariable_Model_Container_Uv extends Enterprise_PageCache_Model_Container_Advanced_Quote
{
    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        return $block->toHtml();
    }
}