/*
	LightBox-Localisation:
*/


function localiceLightbox($text_1a, $text_1b, $text_2)
{
	if (document.getElementById('lightboxCaption'))
	{
		var $text_1 = "";
		$text_1 = $text_1a + ' <a href="#" onclick="hideLightbox(); return false;"><kbd>X</kbd></a> ' + $text_2;
		document.getElementById('keyboardMsg').innerHTML = $text_1;
		
		$text_2 = $text_1b + ' ' + $text_2;
		document.getElementById('lightboxImage').parentNode.title = $text_2;
	}
}
