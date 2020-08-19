/*

  Quelle: http://www.einfach-fuer-alle.de/artikel/fontsize/
	To implement this script in your Web page, configure this file as
	shown below, then put this file on your Web server.
	
	Next, insert the following at the beginning of the <head> section
	of your Web page:

		<script type="text/javascript" language="JavaScript1.2" src="[path]efa_fontsize.js"></script>

	where [path] is the path to this file on your server.
	
	Insert the following right after the <body> tag:

		<script type="text/javascript" language="JavaScript1.2">
			if (efa_fontSize) efa_fontSize.efaInit();
		</script>
	
	Finally, insert the following where you wish the links to change the
	text size to appear: 

		<script type="text/javascript" language="JavaScript1.2">
			if (efa_fontSize) document.write(efa_fontSize.allLinks);
		</script>
*/

/*
	efa_increment = percentage by which each click increases/decreases size
	efa_bigger = array of properties for 'increase font size' link
	efa_reset = array of properties for 'reset font size' link
	efa_smaller = array of properties for 'decrease font size' link

	properties array format:
		['before HTML',
		 'inside HTML',
		 'title text',
		 'class text',
		 'id text',
		 'name text',
		 'accesskey text',
		 'onmouseover JavaScript',
		 'onmouseout JavaScript',
		 'on focus JavaScript',
		 'after HTML'
		 ]
*/



var efa_bigger = ['',											//HTML to go before 'bigger' link
				  'A+',											//HTML to go inside 'bigger' anchor tag
				  'Schrift gr&ouml;sser stellen',				//title attribute
				  'bigger',										//class attribute
				  '',											//id attribute
				  '',											//name attribute
				  '',											//accesskey attribute
				  '',											//onmouseover attribute
				  '',											//onmouseout attribute
				  '',											//onfocus attribute
				  ' '											//HTML to go after 'bigger' link
				  ]

var efa_reset = ['',
				 'A',											//HTML to go before 'reset' link
				 'Schrift normal',								//HTML to go inside 'reset' anchor tag
				  'normal',										//class attribute
				  '',											//id attribute
				  '',											//name attribute
				  '',											//accesskey attribute
				  '',											//onmouseover attribute
				  '',											//onmouseout attribute
				  '',											//onfocus attribute
				  ' '											//HTML to go after 'reset' link
				  ]

var efa_smaller = ['',
				   'A-',										//HTML to go before 'smaller' link
				   'Schrift kleiner stellen',					//HTML to go inside 'smaller' anchor tag
				   'smaller',									//class attribute
				   '',											//id attribute
				   '',											//name attribute
				   '',											//accesskey attribute
				   '',											//onmouseover attribute
				   '',											//onmouseout attribute
				   '',											//onfocus attribute
				   ''											//HTML to go after 'smaller' link
				   ]

function Efa_Fontsize(increment,bigger,reset,smaller,def) {
	// check for the W3C DOM
	this.w3c = (document.getElementById);
	// check for the MS DOM
	this.ms = (document.all);
	// get the userAgent string and normalize case
	this.userAgent = navigator.userAgent.toLowerCase();
	// check for Opera and that the version is 7 or higher; note that because of Opera's spoofing we need to
	// resort to some fancy string trickery to extract the version from the userAgent string rather than
	// just using appVersion
	this.isOldOp = ((this.userAgent.indexOf('opera') != -1)&&(parseFloat(this.userAgent.substr(this.userAgent.indexOf('opera')+5)) <= 7));
	// check for Mac IE; this has been commented out because there is a simple fix for Mac IE's 'no resizing
	// text in table cells' bug--namely, make sure there is at least one tag (a <p>, <span>, <div>, whatever)
	// containing any content in the table cell; that is, use <td><p>text</p></td> or <th><span>text</span></th>
	// instead of <td>text</td> or <th>text</th>; if you'd prefer not to use the workaround, then uncomment
	// the following line:
	// this.isMacIE = ((this.userAgent.indexOf('msie') != -1) && (this.userAgent.indexOf('mac') != -1) && (this.userAgent.indexOf('opera') == -1));
	// check whether the W3C DOM or the MS DOM is present and that the browser isn't Mac IE (if above line is
	// uncommented) or an old version of Opera
	if ((this.w3c || this.ms) && !this.isOldOp && !this.isMacIE) {
		// set the name of the function so we can create event handlers later
		this.name = "efa_fontSize";
		// set the cookie name to get/save preferences
		this.cookieName = 'efaSize';
		// set the increment value to the appropriate parameter
		this.increment = increment;
		//default text size as percentage of user default
		this.def = def;
		//intended default text size in pixels as a percentage of the assumed 16px
		this.defPx = Math.round(16*(def/100));
		//base multiplier to correct for small user defaults
		this.base = 1;
		// call the getPrefs function to get preferences saved as a cookie, if any
		this.pref = this.getPref();
		// stuff the HTML for the test <div> into the testHTML property
		this.testHTML = '<div id="efaTest" style="position:absolute;visibility:hidden;line-height:1em;">&nbsp;</div>';
		// get the HTML for the 'bigger' link
		this.biggerLink = this.getLinkHtml(1,bigger);
		// get the HTML for the 'reset' link
		this.resetLink = this.getLinkHtml(0,reset);
		// get the HTML for the 'smaller' link
		this.smallerLink = this.getLinkHtml(-1,smaller);
		// set up an onlunload handler to save the user's font size preferences
	} else {
		// set the link html properties to an empty string so the links don't show up
		// in unsupported browsers
		this.biggerLink = '';
		this.resetLink = '';
		this.smallerLink = '';
		// set the efaInit method to a function that only returns true so
		//we don't get errors in unsupported browsers
		this.efaInit = new Function('return true;');
	}
	// concatenate the individual links into a single property to write all the HTML
	// for them in one shot
	this.allLinks = this.biggerLink + this.resetLink + this.smallerLink;
}
// check the user's current base text size and adjust as necessary
Efa_Fontsize.prototype.efaInit = function(efa_default) {
	this.efa_default=efa_default;
		// write the test <div> into the document
		document.writeln(this.testHTML);
		// get a reference to the body tag
		this.body = (this.w3c)?document.getElementsByTagName('body')[0].style:document.all.tags('body')[0].style;
		this.html = (this.w3c)?document.getElementsByTagName('html')[0].style:document.all.tags('html')[0].style;
		
		// get a reference to the test element
		this.efaTest = (this.w3c)?document.getElementById('efaTest'):document.all['efaTest'];
		// get the height of the test element
		var h = (this.efaTest.clientHeight)?parseInt(this.efaTest.clientHeight):(this.efaTest.offsetHeight)?parseInt(this.efaTest.offsetHeight):999;
		// check that the current base size is at least as large as the browser default (16px) adjusted
		// by our base percentage; if not, divide 16 by the base size and multiply our base multiplier
		//  by the result to compensate
		if (h < this.defPx) this.base = this.defPx/h;
		// now we set the body font size to the appropriate percentage so the user gets the 
		// font size they selected or our default if they haven't chosen one
		var fontSize = Math.round(this.pref*this.base);
		
	if (fontSize>efa_max_size)
		{
			fontSize=efa_max_size;
		}
		if (fontSize<efa_min_size)
		{
			fontSize=efa_min_size;
	}
	this.body.fontSize=fontSize + '%';
	this.html.fontSize=fontSize + '%';
};
// construct the HTML for the links; we expect -1, 1 or 0 for the direction, an array
// of properties to add to the <a> tag and HTML to go before, after and inside the tag
Efa_Fontsize.prototype.getLinkHtml = function(direction,properties) {
	// declare the HTML variable and add the HTML to go before the link, the start of the link
	// and the onclick handler; we insert the direction argument as a parameter passed to the
	// setSize method of this object
	var html = properties[0] + '<a href="#" onclick="efa_fontSize.setSize(' + direction + '); return false;"';
	// concatenate the title attribute and value
	html += (properties[2])?' title="' + properties[2] + '"':'';
	// concatenate the class attribute and value
	html += (properties[3])?' class="' + properties[3] + '"':'';
	// concatenate the id attribute and value
	html += (properties[4])?' id="' + properties[4] + '"':'';
	// concatenate the name attribute and value
	html += (properties[5])?' name="' + properties[5] + '"':'';
	// concatenate the accesskey attribute and value
	html += (properties[6])?' accesskey="' + properties[6] + '"':'';
	// concatenate the onmouseover attribute and value
	html += (properties[7])?' onmouseover="' + properties[7] + '"':'';
	// concatenate the onmouseout attribute and value
	html += (properties[8])?' onmouseout="' + properties[8] + '"':'';
	// concatenate the title onfocus and value
	html += (properties[9])?' onfocus="' + properties[9] + '"':'';
	// concatenate the link contents, closing tag and any HTML to go after the link and return the
	// entire string
	return html += '>'+ properties[1] + '<' + '/a>' + properties[10];
};
// get the saved preferences out of the cookie, if any
Efa_Fontsize.prototype.getPref = function() {
	// get the value of the cookie for this object
	var pref = this.getCookie(this.cookieName);
	// if there was a cookie value return it as a number
	if (pref) return parseInt(pref);
	// if no cookie value, return the default
	else return this.def;
};
// change the text size; expects a direction parameter of 1 (increase size), -1 (decrease size)
// or 0 (reset to default)
Efa_Fontsize.prototype.setSize = function(direction) {

	// see if we were passed a nonzero direction parameter;
	// if so, multiply it by the increment and add it to the current percentage size;
	// if the direction was negative, it will reduce the size; if the direction was positive,
	// it will increase the size; if the direction parameter is undefined or zero, reset
	// current percentage to the default
	this.pref = (direction)?this.pref+(direction*this.increment):this.def;
	this.setCookie(this.cookieName,this.pref);
	// set the text size
	var fontSize = Math.round(this.pref*this.base);
	
	if (fontSize>efa_max_size)
		{
			fontSize=efa_max_size;
		}
		if (fontSize<efa_min_size)
		{
			fontSize=efa_min_size;
	}
	
	this.body.fontSize=fontSize + '%';
	this.html.fontSize=fontSize + '%';
};
// get the value of the cookie with the name equal to a string passed as an argument
Efa_Fontsize.prototype.getCookie = function(cookieName) {
	var cookie = cookieManager.getCookie(cookieName);
	return (cookie)?cookie:false;
};
// set a cookie with a supplied name and value
Efa_Fontsize.prototype.setCookie = function(cookieName,cookieValue) {
	return cookieManager.setCookie(cookieName,cookieValue);
};


