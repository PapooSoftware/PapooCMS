<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menu.php
 * Type:     function
 * Name:     menu
 * Purpose:  creates an array containing complete front-end menu data 
 * -------------------------------------------------------------
 */

function smarty_function_menu($params, &$smarty)
{
	global $menu;
	$menu_data = $menu->menu_data_read("FRONT");
	#$smarty->assign("smarty_menu_data", $menu_data);
	// Suche nach aktiven Parent-Elementen zur Kennzeichnung mit class="active"/"current"
	// wenn ein Untermenüpunkt aktiv ist
	if (is_array($menu_data))
	{
		foreach ($menu_data AS $key => $value) // alle Menüpunkte durchgehen
		{
			#if ($value['menuname'] == "Startseite") { unset($menu_data[$key]); continue; }
			if ($value['is_aktiv']) // letzter Menüpunkt in der Kette ist gesetzt bei Auswahl
			{
				// Kette von unten nach oben bis zum Ende (untermenuzu = 0) durchgehen
				for ($i = 0; $value['untermenuzu'] != 0; $i++)
				{
					if ($menu_data[$i]['menuid'] == $value['untermenuzu']) // parent gefunden
					{
						$menu_data[$i]['sub_menu_active'] = 1; // kennzeichnen
						$value['untermenuzu'] = $menu_data[$i]['untermenuzu']; // next parent 
						$i = 0; // von vorn
					}
				}
			}
		}
		// Menü-HTML zu den Menüdaten erzeugen/hinzufügen
		$ul_id = $li_id = $first = 1;
		foreach ($menu_data AS $key => $value) // alle Menüpunkte durchgehen
		{
			// 1. Satz
			if ($first)
			{
				$first = 0;
				$menu_data[$key]['menu_html'] = '<div id="menu"><ul class="level0" id="ul_1"><li class="';
				$old_level = $value['level'];
				$menu_data[$key]['ul_id'] = $ul_id;
			}
			// Keine Änderung des Levels
			elseif ($value['level'] == $old_level)
			{
				$menu_data[$key]['menu_html'] = '</li><li class="';
				$menu_data[$key]['ul_id'] = $ul_id;
			}
			// Neues Untermenü
			elseif ($value['level'] > $old_level)
			{
				$ul_id++;
				$old_level = $value['level'];
				$menu_data[$key]['menu_html'] = '<ul class="level' . ($value['level'] + 1) . '" id="ul_' . $ul_id . '"><li class="';
				$menu_data[$key]['ul_id'] = $ul_id;
				
			}
			// zu höheren Levels zurück
			elseif ($value['level'] < $old_level)
			{
				$menu_data[$key]['menu_html'] = '</li>';
				$loop = $old_level - $value['level'];
				for ($i = $loop; ($i > 0); $i--)
				{
					$menu_data[$key]['menu_html'] .= '</ul></li>';
				}
				$menu_data[$key]['ul_id'] = $ul_id;
				$old_level = $value['level'];
				$menu_data[$key]['menu_html'] .= '<li class="';
			}
		}
		/*foreach ($menu_data AS $key => $value)
		{
			echo "<br>x".$menu_data[$key]['menu_html'];
		}*/
		#print_r($menu_data);
		#exit;
		$sp_urls = $smarty->get_template_vars(sp_urls);
		$slash = $smarty->get_template_vars(slash);
		#$frag_connect = $smarty->get_template_vars(frag_connect);
		#$gleich_connect = $smarty->get_template_vars(gleich_connect);
#print_r($menu_data);exit;
		
		foreach ($menu_data AS $key => $value) // alle Menüpunkte durchgehen
		{
			// Inits
			$sub_follows = 0;
			$concat = $target = "";
			
			// CSS Steuerung
			$class = 'level' . ($value['level'] + 1); // Start mit 1
			
			// Klasse itemX, X = 1 bis x, ausser bei Level 0, dann 2 bis x
			// extrahieren aus Nummer, z.B. 3.1.2, ergibt item4 Level0, Level 1 item1, Level2 item2
			$item_arr = explode(".", $value['nummer']);
			$item = $item_arr[$value['level']]; // Start mit 1
			#if ($value['level'] == 0) $item++;
			$class .= " item" . $item;
			
			if ($value['erster'])
			{
				$class .= " first";
				$ul_active = 1;
			}
			if ($value['letzter']) $class .= " last";
			// class parent nur, wenn nicht erster oder letzter, aber wenn ein Untermenü folgt
			// wenn sich die Länge von nummer vergrössert, folgt ein Unternmenü (ginge auch mit check des levels)
			$dots1 = substr_count($value['nummer'], ".");
			$dots2 = substr_count($menu_data[$key + 1]['nummer'], ".");
			if ($dots1 < $dots2)
			{
				$class .= " parent"; // Untermenü folgt
				$sub_follows = 1;
			}
			// Aktive Menüpunkte mit einer class versehen
			// Level 1/Hauptmenüpunkt = active, alle anderen Untermenüs bekommen current
			if ($value['level'] == 0
				AND ($value['sub_menu_active']
				OR $value['is_aktiv'])) $class .= ' active'; // Item ist aktiver Link
			elseif (($value['level'] != 0
				AND $value['sub_menu_active'])
				OR $value['is_aktiv']) $class .= ' current'; // current nur für Submenüs und gewähltes Item
			$return .= $menu_data[$key]['menu_html'] . $class . '" id="li_' . $li_id . '">'; // Menü-HTML ausgeben
			//Link konstruieren
			$return .= '<a id="a_' . $li_id . '" href="';	// Start anchor
			$li_id++;
			// Plugins
			if (strpos($value['menulink'], "plugin:") !== FALSE)
						$value['menulink'] = str_replace("plugin:", 'plugin.php?template=', $value['menulink']) . "&";
			// externer Link
			elseif (substr($value['menulink'], 0, 7) == "http://"
				AND !strpos($value['menulink'], "webhappening")) $extern = 'target="_blank"';
			// Normaler Link "?menuid=", bei Plugin "#menuid="
			else $concat .= "?";
			if ($extern == ""
				AND !strpos($value['menulink'], "webhappening")) $return .= $slash; // Variable slash gesteuert durch Papoo
			// href= mit/ohne spurls
			if (!$sp_urls
				OR $extern
				OR strpos($value['menulink'], "webhappening")) $return .= $value['menulink'];
			else $return .= $value['menuname_url'];
			// Menü-ID für Plugin und normaler Link, aber nicht bei spurls
			if ($extern == ""
				AND !$sp_urls) $return .= $concat 
										. 'menuid='
										. $value['menuid'];
			$return .= '" ';
			// Rest: class, terget, title
			$return .=  'class="' . $class . '" title="' . $value['lang_title'] . '" ' . $extern . '>'; // Parameter class und title; anchor schliessen
			// Ausgabe Menüname
			$return .= '<span><span class="title">' . $value['menuname'] . '</span></span>';
			$return .= '</a>'; // Anchor-Ende
			if ($key == (count($menu_data) - 1)
				AND $ul_active) $return .= "</li></ul></li></ul></div>";
			elseif ($key == (count($menu_data) - 1)) $return .= "</li></ul></div>";
			if ($value['letzter']) $ul_active = 0;
		}
	}
	return $return;
}
?>
