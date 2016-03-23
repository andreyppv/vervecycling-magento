jQuery(document).ready(function() {
	jQuery('#testimoni-carousel').owlCarousel({
		autoplay: 3000,
		items: 3,
		navigation: true,
		navigationText: [
			"<i class='icon-chevron-left icon-white'></i>",
			"<i class='icon-chevron-right icon-white'></i>"
				],
		pagination:false
	});
});