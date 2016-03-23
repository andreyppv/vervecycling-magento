<?php
class Infinity_ProductsList_Block_Product_List extends Mage_Catalog_Block_Product_List
{   
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $type_id = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $realPriceHtml = $this->_preparePriceRenderer($type_id)
                ->setProduct($product)
                ->setDisplayMinimalPrice($displayMinimalPrice)
                ->setIdSuffix($idSuffix)
                ->toHtml();
            $product->setAddToCartUrl($this->getAddToCartUrl($product));
            $product->setRealPriceHtml($realPriceHtml);
            $type_id = $this->_mapRenderer;
        }

        $html = $this->_preparePriceRenderer($type_id)
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->toHtml();
		$html = str_replace('<em>', '', $html);
		$html = str_replace('</em>', '', $html);
        $currency = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $currency = preg_replace ("/[^a-zA-Z]/","",$currency);
        $html = str_replace($currency, '<em>' . $currency . '</em>', $html);
        
        return str_replace('.00','',$html);
    }
}