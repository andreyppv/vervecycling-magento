function countrySwitcherGo(_this){
    currency = jQuery(_this).val();
    var date = new Date(0);
    document.cookie="currency=; path=/; expires="+date.toUTCString();
    
    var date = new Date( new Date().getTime() + 1000*86400*365 );
    document.cookie="currency="+currency+"; path=/; expires="+date.toUTCString()+"domain="+Mage.Cookies.domain;

    setLocation(jQuery(_this).attr('data-url'));
};
jQuery(document).on('click', '.header-country-switcher li a, .product-currency li a' , function(event){
    event.preventDefault();
    event.stopPropagation();
    switchCurrency(jQuery(this));
    setLocation(jQuery(this).parents('.header-country-switcher, .product-currency').attr('data-current-url'));
    return false;
});
jQuery(document).on('click', '#language-chooser a', function(){
    store = jQuery(this).data('lang');
    Mage.Cookies.clear('store');
    var date = new Date( new Date().getTime() + 1000*86400*365 );
    document.cookie="store="+store+"; expires="+date.toUTCString()+"; path="+Mage.Cookies.path+"; domain="+Mage.Cookies.domain+";";
    setLocation(jQuery(this).attr('href'));
    return false;
});

function currencyLisShow(_this){
	jQuery(_this).parent('.sub-title').toggleClass('show');
	jQuery(_this).parents('.product-currency').find('.currency-list').toggleClass('show');
	return false;
}

jQuery(document).bind('click', function (e) {
	if (jQuery(e.target).closest('.product-currency').length == 0) {
		jQuery('.product-currency').children('.sub-title').removeClass('show');
		jQuery('.product-currency').children('.currency-list').removeClass('show');
	}
});

function currentCurrencyNameSet(){
	jQuery('.product-currency').each(function(){
		jQuery(this).children('.sub-title').find('.name').text(jQuery(this).children('.currency-list').find('li.current').find('.name').text());
	})
}
function switchCurrency(element){
    
    currency = element.data('currency');
    website = element.data('website');
    language = 'uk'; //jQuery('#language-chooser li.active a').data('lang').substr(-2);
    store = website+'_'+language;
    var date = new Date(0);
    Mage.Cookies.clear('currency');
    var date = new Date( new Date().getTime() + 1000*86400*365 );
    document.cookie="currency="+currency+"; expires="+date.toUTCString()+"; path=/; domain="+Mage.Cookies.domain;
    document.cookie="store="+store+"; expires="+date.toUTCString()+"; path="+Mage.Cookies.path+"; domain="+Mage.Cookies.domain+";";
}

jQuery(document).ready(function(){
	currentCurrencyNameSet();
});
	