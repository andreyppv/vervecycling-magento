Object.extend(Product.OptionsPrice.prototype,{
    formatPrice: function(price) {
        this.priceFormat.precision = 0;
        this.priceFormat.requiredPrecision = 0;
        return (this.optionPrices.bundle > 0)
            ? formatCurrency(this.productPrice, this.priceFormat)+'<span class="add-price-bundle"> + '+formatCurrency(this.optionPrices.bundle, this.priceFormat)+'</span>'
            : formatCurrency(price, this.priceFormat)
    }
});