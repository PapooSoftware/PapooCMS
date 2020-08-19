var $text_diashow_stop = "";
var $text_diashow_continue = "";

var $leiste_hoehe = 160;
var $leiste_offset_left = 0;
var $leiste_max_offset = 0;
var $leiste_speed = 0;
var $leiste_show = 1;
var $leiste_toggle_auto = false;
var $leiste_hidden = 0;
var $leiste_toggle_auto_init = false;
var $leiste_toggle_timeout = false;
var $leiste_standzeit = 3000;

var $slide_start_timeout = 100;
var $aktu_image_number = 1;
var $slide_active = true;
var $slide_timeout = false;
var $fade_action = false;

var $image_array = new Array();
var $image_leiste_content_width = 25;


jQuery(document).ready(function()
{
	$text_diashow_stop = $("span#start_stop a").text();
	$("div#scroll_right").click(function() {my_move(1);});
	$("div#scroll_left").click(function() {my_move(-1);});
	$leiste_speed = Math.round($(window).width() * 0.7);
	
	$("div#image_text").animate({opacity: 0.75}, 10);
	
	if ($leiste_toggle_auto)
	{
		window.setTimeout("leiste_toggle_auto_init()", $leiste_standzeit);
		$().mousemove(function(e) {leiste_toggle_auto(e);});
	}
	else
	{
		$("div#leiste_toggle").show();
	}
	
	image_show(1);
});

function my_move($dir)
{
	$("div#scroll_left").show();
	$("div#scroll_right").show();
	
	$leiste_max_offset = - ($image_leiste_content_width - $("div#image_leiste").width());
	
	if ($dir == 0) $leiste_offset_left = 0;
	else $leiste_offset_left = $leiste_offset_left + (-$dir*$leiste_speed);
	
	if ($leiste_offset_left >= 0)
	{
		$leiste_offset_left = 0;
		$("div#scroll_left").hide();
	}
	if ($leiste_offset_left <= $leiste_max_offset)
	{
		$leiste_offset_left = $leiste_max_offset;
		$("div#scroll_right").hide();
	}
	
	$("div#image_leiste_content").animate({left: $leiste_offset_left+"px"}, 1000, "easeOutBounce");
}

function leiste_init($init_leiste_hoehe, $text_continue)
{
	$leiste_hoehe = $init_leiste_hoehe;
	$leiste_max_offset = - ($image_leiste_content_width - $("div#image_leiste").width());
	if ($leiste_max_offset < 0) $("div#scroll_right").show();
	
	$("div#image_leiste_content").animate({width: $image_leiste_content_width+"px"}, 0);
	
	$text_diashow_continue = $text_continue;
}

function add_image($id, $src, $width, $height, $name, $text, $timeout, $left_offset)
{
	$temp_array = new Array();
	$temp_array['src'] = $src;
	$temp_array['width'] = $width;
	$temp_array['height'] = $height;
	$temp_array['name'] = $name;
	$temp_array['text'] = $text;
	$temp_array['timeout'] = $timeout * 1000;
	$temp_array['pos_left'] = $left_offset;
	$image_leiste_content_width = $left_offset;
	
	$image_array[$id] = $temp_array;
}

function slide_show()
{
	if ($slide_active)
	{
		$aktu_image_number ++;
		if ($aktu_image_number >= $image_array.length) $aktu_image_number = 1;
		image_show_start();
	}
}

function slide_show_toggle()
{
	if ($slide_active)
	{
		if ($slide_timeout) window.clearTimeout($slide_timeout);
		$slide_active = false;
		$("span#start_stop a").text($text_diashow_continue);
	}
	else
	{
		$slide_active = true;
		$("span#start_stop a").text($text_diashow_stop);
		slide_show();
	}
	//$("div#controll").hide();
}

function image_show_start()
{
	$fade_action = true;
	// altes Bild ausblenden
	$("div#image_box").animate({opacity: 0}, 2000, 0, function(){$fade_action = false; image_show($aktu_image_number);});
}

function image_show($id)
{
	// Bild laden
	$bild = document.createElement('img');
	$bild.src = $image_array[$id]['src'];
	
	$left_offset = Math.round($(window).width()/2 - $image_array[$id]['width']/2);
	if ($left_offset < 0) $left_offset = 0;
	$top_offset = Math.round($(window).height()/2 - $image_array[$id]['height']*0.55);
	if ($top_offset < 0) $top_offset = 0;
	
	$("div#image_box").animate({left: $left_offset+"px", top: $top_offset+"px"}, {duration: 10});
	
	$("div#image_box").html($bild);
	$("div#image_box").animate({opacity: 1}, 2000, 0, function(){$fade_action = false; if ($slide_active) {$slide_timeout = window.setTimeout("slide_show()", $image_array[$aktu_image_number]['timeout']);}});
	$("div#image_box img").load(function(){Reflection.add($bild, {"height": 0.2, "opacity": 0.25 });});
	
	// Texte laden
	$temp_name = $image_array[$id]['name']+" <small>("+$id+" / "+($image_array.length-1)+")";
	$("div#image_text h2").html($temp_name);
	$temp_text = $image_array[$id]['text'];
	$temp_text = $temp_text.replace(/_NEWLINE_/g, "<br />");
	$("div#image_text p").html($temp_text);
	
	// Vorschau-Bild in Leiste markieren
	$("div.image_box_leiste").css({borderTopColor: "#000000"});
	$("div#image_box_leiste_id_"+$id).css({borderTopColor: "#DDDDDD"});
	
	// Vorschau-Leiste nachrücken
	if (($image_array[$id]['pos_left'] + $leiste_offset_left - $(window).width()/5) > $leiste_speed) my_move(1);
	if (($id == 1) && ($leiste_offset_left != 0)) my_move(0);
}

function image_leiste_click($id)
{
	if (!$fade_action && ($leiste_show == 1))
	{
		if ($slide_active) slide_show_toggle();
		$aktu_image_number = $id;
		image_show_start();
	}
}

function leiste_toggle_auto_init()
{
	$leiste_toggle_auto_init = true;
	leiste_toggle_do();
	//$(window).blur();
	//$(window).focus();
}

function leiste_toggle_auto(e)
{
	if ($leiste_toggle_auto_init)
	{
		if ($leiste_show == 1)
		{
			if ($leiste_toggle_timeout) window.clearTimeout($leiste_toggle_timeout);
		}
		else
		{
			leiste_toggle_do();
		}
		$leiste_toggle_timeout = window.setTimeout("leiste_toggle_do()", $leiste_standzeit);
	}
}

function leiste_toggle_auto_BACKUP(e)
{
	//$("div#debug").text("e: "+e+"; Y: "+e.pageY);
	if ($leiste_toggle_auto_init)
	{
		if (($leiste_show == 1) && (e.clientY > $leiste_hoehe)) leiste_toggle_do();
		if (($leiste_show == $leiste_hidden) && (e.clientY < $leiste_hoehe)) leiste_toggle_do();
	}
}

function leiste_toggle_do()
{
	if ($leiste_show == 1)
	{
		$("div#leiste_toggle a").text(".. Leiste einblenden");
		$leiste_show = $leiste_hidden;
		$leiste_zindex = -100;
	}
	else
	{
		$("div#leiste_toggle a").text(".. Leiste ausblenden");
		$("div#image_leiste img").show();
		$leiste_show = 1;
		$leiste_zindex = 300;
	}
	$("div#image_leiste").animate({opacity: $leiste_show}, "normal", "", function(){leiste_imgs_hide();}).css({zIndex: $leiste_zindex});
	$("div#scroll_left").css({zIndex: $leiste_zindex+10});
	$("div#scroll_right").css({zIndex: $leiste_zindex+20});
}

function leiste_imgs_hide()
{
	if ($leiste_show == $leiste_hidden && !$leiste_toggle_auto) $("div#image_leiste img").hide();
}

function diashow_close()
{
	if (history.length > 1) history.back();
	else window.close();
}

function test()
{
	leiste_toggle_do();
}