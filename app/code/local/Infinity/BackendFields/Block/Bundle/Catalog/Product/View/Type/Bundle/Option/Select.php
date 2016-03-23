<?php

class Infinity_BackendFields_Block_Bundle_Catalog_Product_View_Type_Bundle_Option_Select extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Select
{
     public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        if($price > 0)
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return $priceTitle;
    }
}
?>
