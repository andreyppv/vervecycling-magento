jcf.lib.domReady(function(){
	jcf.customForms.destroyAll();
	jcf.customForms.replaceAll();
});

function isMobile() {
    var isiPad = /ipad/i.test(navigator.userAgent.toLowerCase());
    return jQuery.browser.mobile || isiPad;
}

jQuery('.sidebar .block .block-title').live('click', function(event){
	event.preventDefault();
	jQuery(this).toggleClass('active');
	jQuery(this).next('.block-content').toggleClass('active').slideToggle();
	return false;
});


function styleProductTabs(){
	var $tabs = jQuery('#collateral-tabs');
	if($tabs.children('.tab').css('float') == 'none'){
		if(!$tabs.hasClass('mobile')){
			$tabs.addClass('mobile');
			$tabs.children().removeClass('active');
		}
	}else{
		$tabs.removeClass('mobile');
		if(!$tabs.children('.active').size()){
			$tabs.children('dt.first').addClass('active');
			$tabs.children('dt.first').next('dd').addClass('active');
		}
		$tabs.children().attr('style','');
	}
}

jQuery(document).ready(function(){
    var duration = 500;
	jQuery('.go-to').live('click', function(event){
		event.preventDefault();
		targetId = jQuery(this).attr('href');
		if(jQuery(targetId)){
			targetY = jQuery(targetId).offset().top;
			if(targetY){
				targetY -= jQuery('.top-navigation').outerHeight() + 30;
			}else{
				 targetY = 0;
			}
			jQuery('html, body').animate({scrollTop: targetY}, duration);
		}
        return false;
	});
});
jQuery(window).load(function(){
	jQuery('.products-grid .description').each(function(){
		li = jQuery(this).find('li');
		if(!li.length){
			jQuery(this).dotdotdot({
				watch: 'window',
			});
		}else{
			var h_max = jQuery(this).height(),
				h = 0;
			li.each(function(){
				h += jQuery(this).height();
				if(h >= h_max){
					h = h_max - (h - jQuery(this).height());
					
					lineHeight = parseInt(jQuery(this).css('line-height'));
					h = (h <= 0)? lineHeight : h;
					
					lineCount = Math.round(h / lineHeight);
					
					if(lineCount == 1){
						if(jQuery(this).next().length){
							jQuery(this).html(jQuery(this).html() + '...');
						}
						jQuery(this).css({
							'white-space': 'nowrap',
							'overflow': 'hidden',
							'text-overflow': 'ellipsis'
						});
					}else{
						h = Math.round(h / lineHeight) * lineHeight;
						
						jQuery(this).css('max-height', h);
						jQuery(this).dotdotdot({
							watch: 'window',
						});
					}
					jQuery(this).nextAll().remove();
					return false;
				}
			});
		}
	});
});

jQuery(window).resize(function(){
	jQuery('.sidebar .block .block-title').each(function(){
		if(jQuery(this).parents('.sidebar').css('float') == 'none'){
			jQuery(this).parents('.sidebar').addClass('mobile');
		}else{
			jQuery(this).parents('.sidebar').removeClass('mobile');
		}
		if(!jQuery(this).parents('.sidebar').hasClass('mobile')){
			jQuery(this).removeClass('active');
			jQuery(this).next('.block-content').removeClass('active').attr('style','');
		}
	});
	styleProductTabs();
});