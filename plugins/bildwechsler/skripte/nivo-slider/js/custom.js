jQuery(document).ready(function(){
													 
// ---------------------------------------------------------
// Tabs
// ---------------------------------------------------------
jQuery(".tabs").each(function(){

		jQuery(this).find(".tab").hide();
		jQuery(this).find(".tab-menu li:first a").addClass("active").show();
		jQuery(this).find(".tab:first").show();

});

jQuery(".tabs").each(function(){

		jQuery(this).find(".tab-menu a").click(function() {

				jQuery(this).parent().parent().find("a").removeClass("active");
				jQuery(this).addClass("active");
				jQuery(this).parent().parent().parent().parent().find(".tab").hide();
				var activeTab = jQuery(this).attr("href");
				jQuery(activeTab).fadeIn();
				return false;

		});

});


// ---------------------------------------------------------
// Toggle
// ---------------------------------------------------------

jQuery(".toggle").each(function(){

		jQuery(this).find(".box").hide();

});

jQuery(".toggle").each(function(){

		jQuery(this).find(".trigger").click(function() {

				jQuery(this).toggleClass("active").next().stop(true, true).slideToggle("normal");

				return false;

		});

});



jQuery(".recent-posts.team li:nth-child(3n)").addClass("nomargin");



// prettyphoto init
jQuery("a[rel^='prettyPhoto']").prettyPhoto({
	animation_speed:'normal',
	slideshow:5000,
	autoplay_slideshow: false,
	overlay_gallery: true
});



// ---------------------------------------------------------
// Social Icons
// ---------------------------------------------------------

jQuery(".social-networks li a").tooltip({ effect: 'slide', position: 'top center', opacity: .5 });



// ---------------------------------------------------------
// Back to Top
// ---------------------------------------------------------

// fade in #back-top
jQuery(function () {
	jQuery(window).scroll(function () {
		if (jQuery(this).scrollTop() > 100) {
			jQuery('#back-top').fadeIn();
		} else {
			jQuery('#back-top').fadeOut();
		}
	});

	// scroll body to 0px on click
	jQuery('#back-top a').click(function () {
		jQuery('body,html').stop(false, false).animate({
			scrollTop: 0
		}, 800);
		return false;
	});
});


});