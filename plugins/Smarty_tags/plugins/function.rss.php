<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.rss.php
 * Type:     function
 * Name:     rss
 * Purpose:  displays a rss feed
 * References/Specifications
 * Atom 1.0 http://www.atomenabled.org/developers/syndication/atom-format-spec.php
 * RSS 0.90 http://www.rssboard.org/rss-0-9-0
 * RSS 0.91 http://www.rssboard.org/rss-0-9-1-netscape (June 2000)
 * RSS 0.91 http://backend.userland.com/rss091
 * RSS 0.92 http://backend.userland.com/rss092 (December 2000)
 * RSS 1.0  http://feedvalidator.org/docs/rss1.html
 * RSS 1.0  http://web.resource.org/rss/1.0/spec
 * RSS 1.1  
 * RSS 2.0  http://feedvalidator.org/docs/rss2.html (August 2002)
 * RSS 2.0.1 http://www.rssboard.org/rss-2-0-1 (July 2003)
 * Profile of elements http://www.rssboard.org/rss-profile
 * -------------------------------------------------------------
 */

function smarty_function_rss($params, &$smarty)
{
	if ($params['url'] == "")
	{
		$smarty->trigger_error("rss: missing parameter 'url'", E_USER_WARNING);
		$fehler = 1;
	}
	if (isset($params['count']) AND !ctype_digit((string)($params['count'])))
	{
		$smarty->trigger_error("rss: parameter 'count' is not numeric", E_USER_WARNING);
		$fehler = 1;
	}
	// kein Link, wenn das Feed ebenfalls Link(s) liefert (valides HTML sicherstellen)
	$link = $params['link'] == "" ? true : $params['link']; // defaults to true
	$smarty->assign("link", $link); // to template
	if (!$fehler)
	{
		$url = $params['url'];
		#define('MAGPIE_OUTPUT_ENCODING', 'UTF-8'); // UTF-8
		define('MAGPIE_DIR', PAPOO_ABS_PFAD . "/plugins/Smarty_tags/includes/");
		define('MAGPIE_DEBUG', 0);
		define('MAGPIE_CACHE_ON', 0); // 0 = no caching
		#define('MAGPIE_CACHE_DIR', dirname(dirname(__FILE__)) . "/rsscache"); // default is ./cache; will be created if possible
		#define('MAGPIE_CACHE_AGE', 10*60); // default is 3600 = 1 hour (60*60)
		define('MAGPIE_FETCH_TIME_OUT', 12); // default is 5 second timeout - too low for some servers
		// e. g. http://medinfo.netbib.de/feed
		require_once(MAGPIE_DIR . 'rss_fetch.inc');
		
		$rss = fetch_rss($url);#print_r($rss);
		if (!$rss) $smarty->trigger_error("rss: " . magpie_error(), E_USER_WARNING);
		else
		{
			$table_data2 = (array)$rss; // convert object to array

			$count = $params['count'] ? $params['count'] : 15;
			$count = $count <= count($table_data2) ? $count : count($table_data2);

			global $db;

			// Titel des Feeds
			if ($table_data2['channel']['title'] != "")
			{
				$channel_title = strip_tags($db->escape($table_data2['channel']['title'])); // XSS
				#$channel_title = preg_replace("/([\\x7f-\\x9f]*)/e", chr(32), $channel_title); // non-displayable Chars
				$channel_title = Literals_To_HTMLEntities($channel_title);
				if (strtolower($table_data2['encoding']) != "utf-8") $channel_title = utf8_encode($channel_title);
				$table_data2['channel']['title'] = $channel_title;
			}

			// Kurze Beschreibung des Feeds
			if ($channel_description != "")
			{
				$channel_description = strip_tags($db->escape($table_data2['channel']['description'])); // XSS
				#$channel_description = preg_replace("/([\\x7f-\\x9f]*)/e", chr(32), $channel_description); // non-displayable Chars
				$channel_description = Literals_To_HTMLEntities($channel_description);
				if (strtolower($table_data2['encoding']) != "utf-8") $channel_description = utf8_encode($channel_description);		
				$table_data2['channel']['description'] = $channel_description;
			}

			// favicon.ico angefordert?
			if ($params['favicon'])
			{
				if ($table_data2['channel']['link'] != "")
				{
					$pmatch = preg_match("/^(http:\/\/)?([^\/]+)/i", $table_data2['channel']['link'], $pmatch_res);
					$host = $pmatch_res[2];
					if (substr($host, -1) != "/") $host .= "/";
					$favicon_uri = "http://" . $host . "favicon.ico";
					if ($pmatch && getFaviconContentType($favicon_uri, $contentType))
					{
						if (preg_match("/image\/x-icon/", $contentType)) $table_data2['channel']['favicon'] = $favicon_uri;
					}
				}
			}
			$item_descr = $params['item_descr'] == "" ? true : $params['item_descr']; // defaults to true
			$smarty->assign("item_descr", $item_descr); // to template

// einige Korrekturen/Anpassungen
			// base URL für items in diesem feed.
			// URL der Webpräsenz
			if (array_key_exists('link', $table_data2['channel'])) $base_Url = $table_data2['channel']['link'];
			else $base_Url = $params['url']; // invalid feed

			foreach ($table_data2['items'] as $key => $item)
			{
				if ($i < $count)
				{
					// Titel des Eintrags
					if ($item['title'] != "")
					{
						$item_titel = htmlentities($table_data2['items'][$key]['title']);
						#$item_titel = preg_replace("/([\\x7f-\\x9f]*)/e", chr(32), $item_titel); // non-displayable Chars
						$item_titel = Literals_To_HTMLEntities($item_titel);
						if (strtolower($table_data2['encoding']) != "utf-8") $item_titel = utf8_encode($item_titel);
						$table_data2['items'][$key]['title'] = $item_titel;
					}
					
					// Kurze Zusammenfassung des Eintrags. set description lt. RFC Regeln RSS/Atom
					$item_description = "";
					if (is_array($item['content'])
						&& array_key_exists('encoded', $item['content']))
							$item_description = $item['content']['encoded'];
					elseif (array_key_exists('description', $item)) $item_description = $item['description'];
					elseif (array_key_exists('atom_content', $item)) $item_description = $item['atom_content'];
					elseif (array_key_exists('summary', $item)) $item_description = $item['summary'];
	
					if ($item_description != "")
					{
						#$item_description = preg_replace("/([\\x7f-\\x9f]*)/e", chr(32), $item_description); // non-displayable Chars
						$item_description = Literals_To_HTMLEntities($item_description);
						if (strtolower($table_data2['encoding']) != "utf-8") $item_description = utf8_encode($item_description);
						if ($base_Url != "") $item_description = relUrl_to_absUrl($item_description, $base_Url);
						$table_data2['items'][$key]['description'] = $item_description;
					}
					
	
					// Eindeutige Identifikation des Eintrages (RSS/Atom)
					$guid = "";
					if(array_key_exists('guid', $item) && $item['guid'] != "") $guid = $item['guid'];
					elseif(array_key_exists('id', $item) && $item['id'] != "") $guid = $item['id'];
					$table_data2['items'][$key]['guid'] = trim($db->escape($guid));
					#// skip in-feed-dupe
					#if ($guid && isset($guids[$guid])) continue;
					#elseif($guid) $guids[$guid] = true;
	
					// Link zum vollständigen Eintrag
					if (array_key_exists('link', $item) && $item['link'] != "") $url = $item['link'];
					elseif (array_key_exists('guid', $item) && $item['guid'] != "") $url = $item['guid'];
					elseif (array_key_exists('link_', $item) && $item['link_'] != "") $url = $item['link_'];
					// make sure the url is properly escaped
					$url = htmlentities($url, ENT_QUOTES );
					$table_data2['items'][$key]['url'] = $db->escape($url);
				
					// Autor des Artikels, E-Mail-Adresse
					if (array_key_exists('dc', $item) && array_key_exists('creator', $item['dc']))
						$author = $item['dc']['creator']; // RSS 1.0
					elseif (array_key_exists('author_name', $item)) $author = $item['author_name']; // Atom 0.3
					if ($author != "") $table_data2['items'][$key]['author'] = trim(strip_tags($author));
				
					// Datum des Items
					$cDate = -1;
					if (array_key_exists('dc', $item) && array_key_exists('date', $item['dc']))
						$cDate = parse_w3cdtf($item['dc']['date']); // RSS 1.0
					elseif (array_key_exists('pubdate', $item)) {
						#$cDate = strtotime($rss_item['pubdate'], 0);
						$cDate = $item['pubdate']; // RSS 2.0
					}
					elseif (array_key_exists('published', $item)) $cDate = parse_w3cdtf($item['published']); // Atom 1.0
					elseif (array_key_exists('issued', $item)) $cDate = parse_w3cdtf($item['issued']); // Atom alternativ
					elseif (array_key_exists('updated', $item)) $cDate = parse_w3cdtf($item['updated']); // Atom alternativ
					elseif (array_key_exists('created', $item)) $cDate = parse_w3cdtf($item['created']); // Atom 0.3
					if ($cDate != "") $table_data2['items'][$key]['cDate'] = $cDate;
					$i++;
				}
				else
				{
					if (count($table_data2) > $count) array_pop($table_data2['items']); // restliche items entfernen
				}
			}

			if ($params['debug'])
			{
				// Array mit print_r-Format im neuen Fenster anzeigen
				$rss_debug = "<pre>" . htmlentities(print_r($table_data2, 1), ENT_QUOTES) . "</pre>";
				$rss_debug = str_replace("\x0a", "<br />", $rss_debug);
				$rss_debug = str_replace("\x0d", "", $rss_debug);
				echo "<script language=javascript> if( self.name == '' )";
				echo " var title = 'Console'; else var title = 'Console_' + self.name;";
				echo '_smarty_tags_rss = window.open("",title.value,"scrollbars=1,';
				echo 'menubar=1,status=1,location=1,toolbar=1,directories=1,resizable");';
				echo "_smarty_tags_rss.document.write('<html><head><title>Smarty Tags rss debug</title>";
				echo "</head><body bgcolor=#ffffff>');";
				echo "_smarty_tags_rss.document.write('" . $rss_debug . "');";
				echo '_smarty_tags_rss.document.write("</body></html>");_smarty_tags_rss.document.close();</script>';
			}
		}
		$smarty->assign("table_data2", $table_data2);
	}
	return;
}

function getFaviconContentType($link, &$contentType)
{
	$return = false;
    $url_parts = @parse_url($link);
    if (!empty ($url_parts["host"]))
	{
		if (!empty ($url_parts["path"])) $documentpath = $url_parts["path"];
		else $documentpath = "/";

		if (!empty ($url_parts["query"])) $documentpath .= "?" . $url_parts["query"];
	
		$port = (array_key_exists('port', $url_parts) ? $url_parts["port"] : "80");
		$host = $url_parts["host"];
		$fp = @fsockopen($host, $port, $errno, $errstr, 30);
		if ($fp)
		{
			$out = "GET " . $documentpath . " HTTP/1.0\r\nHost: " . $host . "\r\n\r\n";
			fwrite($fp, $out);
			while (!feof($fp)) {
				$line = fgets($fp, 100);
				if (preg_match("/Content-Type: (.*)/i", $line, $matches))
				{
					$contentType = $matches[1];
					$return = true;
					break;
				}
			}
		}
		#else // errno, errstr
	}
    return $return;
}

/**
 * Ersetzt relative urls durch absolute urls für anchors, images
 */
function relUrl_to_absUrl($orig_content, $feed_url)
{
    preg_match('/(http|https|ftp):\/\//', $feed_url, $protocol); // -> array $protocol
    $serverUrl = preg_replace("/(http|https|ftp|news):\/\//", "", $feed_url); // ohne http:// etc.
    $serverUrl = preg_replace("/\/.*/", "", $serverUrl); // nur domain, subdomain
    if ($serverUrl == '') return $orig_content;
    if (isset($protocol[0]))
	{
        $new_content = preg_replace('/href="\//', 'href="' . $protocol[0] . $serverUrl . '/', $orig_content); // <a
        $new_content = preg_replace('/src="\//', 'src="' . $protocol[0] . $serverUrl . '/', $new_content); // <img
    }
	else $new_content = $orig_content;
    return $new_content;
}

// x7F - x9F
function Literals_To_HTMLEntities($char)
{
		$replace = array(
		"&#127;", "&#8364;", "&#129;", "&#8218;", "&#402;", "&#8222;", "&#8230;", "&#8224;", "&#8225;", "&#710;",
		"&#8240;", "&#352;", "&#8249;", "&#338;", "&#141;", "&#381;", "&#143;", "&#144;", "&#8216;", "&#8217;",
		"&#8220;", "&#8221;", "&#8226;", "&#8211;", "&#8212;", "&#732;", "&#8482;", "&#353;", "&#8250;", "&#339;",
		"&#157;", "&#382;", "&#376;");
		
		$search = array(
	    chr(127), chr(128), chr(129), chr(130), chr(131), chr(132), chr(133), chr(134), chr(135),
		chr(136), chr(137), chr(138), chr(139), chr(140), chr(141), chr(142), chr(143), chr(144), chr(145),
		chr(146), chr(147), chr(148), chr(149), chr(150), chr(151), chr(152), chr(153), chr(154), chr(155),
		chr(156), chr(157), chr(158), chr(159));
		return $umw_inhalt = str_replace($search, $replace, $char);
}
?>