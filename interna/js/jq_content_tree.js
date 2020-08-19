"use strict";

var $content_tree_show_all = false;
var $toggle_ids_all = [];
var $toggle_ids_open = [];

$(function()
{
	/* Erzeugt das Toggle-Button-Span */
	function prepend_span()
	{
		var span_text = "";
		var bg_position = "0px -25px";
		var toggle_state = "open";
		
		var span_style = 'float: left; width: 25px; height: 25px; margin-left: -25px; background-image: url(./bilder/pfeile_ordner_offen_zu.gif); background-repeat: no-repeat; background-position:'+bg_position+';';
		
		var $span = $('<span class="lia_toggle" />');
		$span.attr('style', span_style);
		$span.attr('data-toggle-state', toggle_state);
		$span.text(span_text);

		return $span;
	}
	
	
	// Ausblenden Anzeige "Inhalte"
	//if (!$content_tree_show_all) $("#inhalt_sitemap ul ul").hide();
	// Ausblenden Anzeige "Menue bearbeiten"
	//if (!$content_tree_show_all) $("#inhalt_menu ul ul").hide();
	
	/* Alle Menüpunkte durchgehen */
	$("div#inhalt_sitemap ul li, div#inhalt_menu ul li").each(function(i) {
		
		// Erstes UL bestimmen
		var ul = $(this).children("ul");
		if (!ul.length) {
			ul = $(this).children("div").children("ul");
		}
		// Toggle-Button einbauen
		if (ul.length) {
			$(this).prepend(prepend_span());
		}
		
		/*
		// Wenn sowohl viele Artikel als auch Untermenüpunkte, Extratoggler für Artikel einbauen
		var artikel_ul = $(this).children('ul.ul_artikel');
		if (artikel_ul.length && artikel_ul.children('li').length > 5 && $(this).children('div').children('ul').length) {
			artikel_ul.wrap($('<ul class="artikel-wrapper"><li /></ul>'));
			
			var wrapper_li = artikel_ul.closest('.artikel-wrapper li');
			wrapper_li.prepend(prepend_span());
			wrapper_li.children('.lia_toggle').after('<div style="height: 2em"/>')
		}
		*/
	});
	
	// jedem lia_toggle eine eindeutige Nummer (=menuid) zuweisen. Damit wird fuer restore vermerkt welcher offen oder zu war.
	$("span.lia_toggle").each(function() {
		var menuid = 0;
		menuid = $($(this).parent("li").find("a")[0]).attr("menuid");
		if (menuid) {
			this.setAttribute('data-menuid', menuid);
			$toggle_ids_all.push(menuid);
		}
	});
	$("span.lia_toggle").click(function () {do_toggle($(this));}).css({"cursor": "pointer"});
	
	// bloede Zeilen-Hoehen-Mogelei, wegen IE
	$("span.lia_toggle").parent("li").css({"line-height": "0px"});
	$("span.lia_toggle").parent("li").children("div").css({"line-height": "120%"});
	$("span.lia_toggle").parent("li").children(".ul_artikel").css({"line-height": "120%"});
	
	toggle_restore();
	//$("div#inhalt_sitemap, div#inhalt_menu").css({"display": "block"});
	
	//alert(".. fin");
	
});

function toggle_restore()
{
	var $temp_saved_ids = new Array();
	var $temp_saved = $.jStorage.get("lia_toggle_ids_open");
	//alert(".. temp_saved: "+$temp_saved);
	
	if (!$temp_saved && $content_tree_show_all) $temp_saved_ids = $toggle_ids_all;
	else if($temp_saved) $temp_saved_ids = $temp_saved.split(",");
	//alert(".. lass offen: "+$temp_saved_ids.join(", "));
	
	for (var $i=0; $i < $toggle_ids_all.length; $i++)
	{
		//alert(".. restore: "+$temp_ids[$i]);
		if (!in_array($toggle_ids_all[$i], $temp_saved_ids))
		{
			//alert(".. schliesse: "+$toggle_ids_all[$i]);
			do_toggle($("span.lia_toggle[data-menuid="+$toggle_ids_all[$i]+"]"), "restore");
		}
		else
		{
			//alert(".. offen lassen: "+$toggle_ids_all[$i]);
			menuid_open_add($toggle_ids_all[$i]);
		}
	}
}

function do_toggle($element, $modus)
{
	
	$element.parent().children("ul").toggle();
	var $temp_element = $element.parent().children("div");
	$temp_element.children("ul").toggle();
	
	var menuid = $element.attr("data-menuid");
	
	if ($element.attr("data-toggle-state") != "closed")
	{
		$element.css({"background-position": "0px 0px"});
		$element.attr("data-toggle-state", "closed");

		
		if (menuid)
			menuid_open_delete(menuid);
	}
	else
	{
		$element.css({"background-position": "0px -25px"});
		$element.attr("data-toggle-state", "open");

		
		if (menuid)
			menuid_open_add(menuid);
	}
	if ($modus != "restore")
	{
		$.jStorage.set("lia_toggle_ids_open", $toggle_ids_open.join(","));
	}
	//_debug();
}

function jq_content_tree_show_all()
{
	$content_tree_show_all = true;
}


// get URl-parameter 
function get_url_parameter($name, $url)
{
	var i=1  //Suchposition in der URL
	var suche = $name+"="
	while (i < $url.length)
	{
		if ($url.substring(i, i+suche.length)==suche)
		{
			var ende = $url.indexOf("&", i+suche.length)
			ende = (ende>-1) ? ende : $url.length
			var loca = $url.substring(i+suche.length, ende)
			return unescape(loca)
		}
		i++
	}
	return ""
}

function menuid_open_add($menuid)
{
	if ($menuid)
	{
		$toggle_ids_open.push($menuid);
	}
}

function menuid_open_delete($menuid)
{
	if ($menuid)
	{
		var $temp_menu_ids = new Array();
		
		for (var $i = 0; $i < $toggle_ids_open.length; $i++)
		{
			if ($toggle_ids_open[$i] != $menuid)
			{
				$temp_menu_ids.push($toggle_ids_open[$i]);
			}
		}
		
		$toggle_ids_open = $temp_menu_ids;
	}
}

function in_array($item, $array)
{
	for(var $i=0; $i< $array.length; $i++) if ($item == $array[$i]) return true;
	return false;
}

function _debug()
{
	var $temp_text = "";
	$temp_text += "alle menuids: "+$toggle_ids_all.join(", ")+";\n";
	$temp_text += "offene menuids: "+$toggle_ids_open.join(", ")+";\n";
	alert("DEBUG: \n"+$temp_text);
}