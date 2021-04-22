$(document).ready(function()
{
	$('img[data-papoopopupimage]').wrap(function()
	{
		$temp_a = $(document.createElement('a'));
		$temp_href = $(this).attr("src");
		$temp_href = $temp_href.replace("/images/thumbs/", "/images/");
		//return '<a href="'+$temp_href+'" title="'+$(this).attr("src");+'" alt="'+$(this).attr("alt");+'" rel="lightbox"></a>';
		
		$temp_a.attr("href", $temp_href);
		$temp_a.attr("rel", "lightbox");
		$temp_a.attr("title", $(this).attr("alt"));
		
		return $temp_a;
	});
});


