// Wird am Ende jeder Papoo-Seite eingebunden

if ( typeof $(document).foundation === 'function' ) {
	$(document).foundation();
}

$(function() {
	$('map').imageMapResize(); 
});
