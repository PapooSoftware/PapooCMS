 
jQuery(document).ready(function() {


/*----------------------------------------------------------------------------------*/
/*	Portfolio Custom Fields Hide/Show
/*----------------------------------------------------------------------------------*/

    var portfolioTypeTrigger = jQuery('#tz_portfolio_type'),
        portfolioImage = jQuery('#tz-meta-box-portfolio-image'),
        portfolioVideo = jQuery('#tz-meta-box-portfolio-video'),
        portfolioAudio = jQuery('#tz-meta-box-portfolio-audio');
        currentType = portfolioTypeTrigger.val();
        
    tzSwitchPortfolio(currentType);

    portfolioTypeTrigger.change( function() {
       currentType = jQuery(this).val();
       
       tzSwitchPortfolio(currentType);
    });
    
    function tzSwitchPortfolio(currentType) {
        if( currentType === 'Audio' ) {
            tzHideAllPortfolio(portfolioAudio);
        } else if( currentType === 'Video' ) {
            tzHideAllPortfolio(portfolioVideo);
        } else {
            tzHideAllPortfolio(portfolioImage);
        }
    }
    
    function tzHideAllPortfolio(notThisOne) {
		portfolioImage.css('display', 'none');
		portfolioVideo.css('display', 'none');
		portfolioAudio.css('display', 'none');
		notThisOne.css('display', 'block');
	}


// ---------------------------------------------------------
//  	Quote
// ---------------------------------------------------------
	var quoteOptions = jQuery('#tz-meta-box-quote');
	var quoteTrigger = jQuery('#post-format-quote');
	
	quoteOptions.css('display', 'none');

// ---------------------------------------------------------
//  	Image
// ---------------------------------------------------------
	var imageOptions = jQuery('#tz-meta-box-image');
	var imageTrigger = jQuery('#post-format-image');
	
	imageOptions.css('display', 'none');

// ---------------------------------------------------------
//  	Link
// ---------------------------------------------------------
	var linkOptions = jQuery('#tz-meta-box-link');
	var linkTrigger = jQuery('#post-format-link');
	
	linkOptions.css('display', 'none');
	
// ---------------------------------------------------------
//  	Audio
// ---------------------------------------------------------	
	var audioOptions = jQuery('#tz-meta-box-audio');
	var audioTrigger = jQuery('#post-format-audio');
	
	audioOptions.css('display', 'none');
	
// ---------------------------------------------------------
//  	Video
// ---------------------------------------------------------
	var videoOptions = jQuery('#tz-meta-box-video');
	var videoTrigger = jQuery('#post-format-video');
	
	videoOptions.css('display', 'none');


// ---------------------------------------------------------
//  	Core
// ---------------------------------------------------------
	var group = jQuery('#post-formats-select input');

	
	group.change( function() {
		
		if(jQuery(this).val() == 'quote') {
			quoteOptions.css('display', 'block');
			tzHideAll(quoteOptions);
			
		} else if(jQuery(this).val() == 'link') {
			linkOptions.css('display', 'block');
			tzHideAll(linkOptions);
			
		} else if(jQuery(this).val() == 'audio') {
			audioOptions.css('display', 'block');
			tzHideAll(audioOptions);
			
		} else if(jQuery(this).val() == 'video') {
			videoOptions.css('display', 'block');
			tzHideAll(videoOptions);
			
		} else if(jQuery(this).val() == 'image') {
			imageOptions.css('display', 'block');
			tzHideAll(imageOptions);
			
		} else {
			quoteOptions.css('display', 'none');
			videoOptions.css('display', 'none');
			linkOptions.css('display', 'none');
			audioOptions.css('display', 'none');
			imageOptions.css('display', 'none');
		}
		
	});
	
	if(quoteTrigger.is(':checked'))
		quoteOptions.css('display', 'block');
		
	if(linkTrigger.is(':checked'))
		linkOptions.css('display', 'block');
		
	if(audioTrigger.is(':checked'))
		audioOptions.css('display', 'block');
		
	if(videoTrigger.is(':checked'))
		videoOptions.css('display', 'block');
		
	if(imageTrigger.is(':checked'))
		imageOptions.css('display', 'block');
		
	function tzHideAll(notThisOne) {
		videoOptions.css('display', 'none');
		quoteOptions.css('display', 'none');
		linkOptions.css('display', 'none');
		audioOptions.css('display', 'none');
		imageOptions.css('display', 'none');
		notThisOne.css('display', 'block');
	}
	
	
});