<?php
class Infinity_HomeProductsList_Block_Product_List extends Mage_Catalog_Block_Product_Abstract
{
    public function getLoadedProductCollection(){   	
        $_products = Mage::getResourceModel('catalog/product_collection');
		$_products->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addMinimalPrice()
				->addFinalPrice()
				->addTaxPercents()
                ;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_products);
        
        return $_products;
    }
    
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