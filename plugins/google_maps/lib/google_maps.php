<?php

/**
 * Class google_maps
 */
class google_maps
{
	/**
	 * google_maps constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $checked, $user, $db, $cms;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->db = &$db;
		$this->cms = &$cms;
		//Admin Einstellungen durchf�hren
		$this->google_maps_do_admin();

		if(!isset($this->content->template['pluginload'])) {
			$this->content->template['pluginload'] = NULL;
		}
	}

	/**
	 * google_maps::google_maps_do_admin()
	 * Admin Weiche
	 * @return void
	 */
	function google_maps_do_admin()
	{
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			#$template=basename($template);

			if (strpos("XXX" . $template, "google_maps_back.html")) {
				//Einstellungen �berabeiten
				$this->google_maps_make_einstellungen();
			}
		}
	}

	/**
	 * google_maps::google_maps_make_einstellungen()
	 * Einstellungen f�r das Google Maps Plugin speichern
	 *
	 * @return void
	 */
	function google_maps_make_einstellungen()
	{
		//Daten Updaten
		if (!empty($this->checked->submit)) {
			$sql = sprintf("UPDATE %s SET 
								gmap_apikey='%s',
                                gmap_apikey_2='%s',
								gmap_typ='%s',
								gmap_breite='%d',
								gmap_hoehe='%d',
								gmap_zoom='%d' WHERE 
								gmap_id='1'
								",

				$this->cms->tbname['papoo_google_maps_tabelle'],
				$this->db->escape($this->checked->gmap_apikey),
				$this->db->escape($this->checked->gmap_apikey_2),
				$this->db->escape($this->checked->gmap_typ),
				$this->db->escape($this->checked->gmap_breite),
				$this->db->escape($this->checked->gmap_hoehe),
				$this->db->escape($this->checked->gmap_zoom));
			$this->db->query($sql);
		}

		//Daten rausholen
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_google_maps_tabelle']);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['gmap'] = $result;

		if(!isset($this->content->template['pluginload'])) {
			$this->content->template['pluginload'] = NULL;
		}
	}

	function post_papoo()
	{
		if (!defined("admin")) {
			//Ausgabe filtern
			#$this->gmap_filter_ausgabe_inhalt();

			//3. Spalte filtern
			#$this->gmap_filter_ausgabe_spalte();
		}
	}

	function output_filter()
	{
		if (!defined("admin")) {
			//Ausgabe filtern
			$this->gmap_filter_output();

			//3. Spalte filtern
			#$this->gmap_filter_ausgabe_spalte();
		}
	}

	/**
	 * google_maps::filter_ausgabe_inhalt()
	 * Diese Funktion filter die Ausgabe im Inhaltsbereich
	 * und ersetzt die Vorgaben mit den Inhalten der Google Map
	 *
	 * @return void
	 */
	function gmap_filter_ausgabe_inhalt()
	{
		//Ausgabe rausholen
		$ausgabe = $this->content->template['table_data'];

		$i = 0;
		//Ausgabe durchgehen
		if (is_array($ausgabe)) {
			foreach ($ausgabe as $data) {
				//Inhalte �bergeben
				$this->inhalt = $inhalt = $data['text'];
				//Link rausholen
				$link = $this->gmap_get_link_data($inhalt);

				if (!empty($link)) {
					//Link aufsplitten
					$check_ar = $this->gmap_get_link_content($link);
					//An checked Objekt �bergeben
					foreach ($check_ar as $check) {
						$this->gmap_make_map($check);
						//Ergebnis eintragen
						$this->gmap_make_text();
					}
					//An Template �bergeben
					$this->content->template['table_data'][$i]['text'] = "" . $this->inhalt;
					$i++;
				}
			}
		}
	}

	/**
	 * google_maps::filter_ausgabe_inhalt()
	 * Diese Funktion filter die Ausgabe im Inhaltsbereich
	 * und ersetzt die Vorgaben mit den Inhalten der Google Map
	 *
	 * @return void
	 */
	function gmap_filter_output()
	{
		//Ausgabe rausholen
		global $output;

		$ausgabe = $output;

		//Inhalte �bergeben
		$this->inhalt = $inhalt = $ausgabe;
		//Link rausholen
		$link = $this->gmap_get_link_data($inhalt);

		if (!empty($link)) {
			//Link aufsplitten
			$check_ar = $this->gmap_get_link_content($link);
			$this->content->template['plugin_header'] ='nodecode:
           					<link href="https://code.google.com/apis/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />';

			$i = 1;

			//An checked Objekt �bergeben
			foreach ($check_ar as $check) {
				if(count($check_ar) == 1){
					$this->gmap_make_map($check, 'no');
				}
				else {
					$this->gmap_make_map($check, $i);
				}
				//Ergebnis eintragen
				$this->gmap_make_text();
				$i++;
			}
			//An Template �bergeben
			$ausgabe = "" . $this->inhalt;

			$ausgabe=str_ireplace("</head>",$this->js_skript_ausgabe."</head>",$ausgabe);
			//$this->content->template['pluginload']
			//$ausgabe=str_ireplace("onload=\"","onload=\" initialize();",$ausgabe);
			//$ausgabe=str_ireplace("visilexit(visi_text);"," ",$ausgabe);
		}

		$output = $ausgabe;
	}

	/**
	 * google_maps::gmap_make_map()
	 * Karte erstellen und einbinden
	 *
	 * @param array $addr
	 * @param string $anzahl
	 * @return void
	 */
	function gmap_make_map($addr=array(), $anzahl='')
	{
		//Daten rausholen
		$map_can = $anzahl == 'no' ? 'map_canvas' : 'map_canvas' . $anzahl;

		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_google_maps_tabelle']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		//$adar=explode(",",$addr['link']);

		$ad="<address>";

		$ad.=$addr['link'];

		$ad.="</address>";

		//Map zusammensetzen
		$this->map=$ad.'<div id="'.$map_can.'" style="width: '.$result['0']['gmap_breite'].'px; height: '.$result['0']['gmap_hoehe'].'px"></div><br />';

		#$addr['link']="Bonner Talweg 26, 53113 Bonn";
		$result['0']['gmap_apikey']=trim($result['0']['gmap_apikey']);
		$result['0']['gmap_apikey_2']=trim($result['0']['gmap_apikey_2']);

		//{$pluginload}
		$this->content->template['pluginload']="initialize(); ";

		//Skript zusammensetzen
		$this->content->template['plugin_header'] .=
			'<script type="text/javascript">
            let geocoder;
            let map;

            function initialize()  {

                let myOptions = {
                  zoom: '.$result['0']['gmap_zoom'].',
                  mapTypeId: google.maps.MapTypeId.'.$result['0']['gmap_typ'].'
                }

                let map = new google.maps.Map(document.getElementById("'.$map_can.'"), myOptions);

                let contentString = \''.$addr['link'].'<br /><a href="https://maps.google.de/maps?daddr='.strip_tags($addr['link']).'&geocode=&dirflg=&saddr=&f=d&sspn=0.00846,0.017273&ie=UTF8&z=16" target="_blank"><u>Route</u></a>\';
				
            	let infowindow = new google.maps.InfoWindow({
                    content: contentString
                });

                geocoder = new google.maps.Geocoder();
                let address = "'.strip_tags($addr['link']).'";
                geocoder.geocode( { \'address\': address}, function(results, status) {
        
                  if (status == google.maps.GeocoderStatus.OK) {
                	let center = results[0].geometry.location;
                    map.setCenter(center);
                    let marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title:"'.$addr['link'].'"
                    });
                    marker.addListener("click", function() {
                    	infowindow.open(map, marker);
					});
                  } else {
                    alert("Geocode was not successful for the following reason: " + status);
                  }
                });

            }
            google.maps.event.addDomListener(window, \'load\', initialize);
            </script>
            ';

		//Zweiter API-Key
		if (!empty($result['0']['gmap_apikey'])) {
			$this->content->template['plugin_header_2'] = 'nodecode:
			<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key='.$result['0']['gmap_apikey'].'" type="text/javascript"></script>';
		}
		else {
			$this->content->template['plugin_header_2'] = 'nodecode:
			<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>';
		}

		// Wenn keine Api Keys gegeben sind wird die Map auch nicht ausgegeben.
		if (empty($result['0']['gmap_apikey'])) {
			$this->content->template['plugin_header'] = "";
		}

		$this->js_skript_ausgabe=str_ireplace("nodecode:", "", ($this->content->template['plugin_header_2'] ?? '') . ($this->content->template['plugin_header'] ?? ''));

		IfNotSetNull($this->content->template['pluginload']);
	}

	/**
	 * google_maps::make_text()
	 * Inhalte zusammenführen
	 *
	 * @param string $insert
	 * @param string $address
	 * @return void
	 */
	function gmap_make_text($insert = "", $address = "")
	{
		$insert = str_ireplace("nobr:", "", $insert);
		$text = $this->inhalt;

		//$text=str_ireplace("</address><address>",", ",$text);
		$text_insert = $this->map;

		#$text= preg_replace('/<span class="insert_flex">(.*?)<\\/span>/i', $text_insert, $text);
		$text=preg_replace('/<address[^>]+>/i','<address>',$text);

		#$text = preg_replace('/<address(.*?)>/ie', '<address>', $text, 1);
		//$text = preg_replace('/<address>(.*?)<\\/address>/i', $text_insert, $text, 1);
		preg_match('/<address>(.*?)<\/address>/ism', $text_insert, $address);

		$address = str_replace('/','\/',$address[0]);

		$text = preg_replace('/'.$address.'/ism', $text_insert, $text, 1);

		$this->inhalt = $text;
	}

	/**
	 * google_maps::get_link_content()
	 * Var Inhalte des Links rausholen
	 *
	 * @param array $link_a
	 * @return mixed|null
	 */
	function gmap_get_link_content($link_a = array())
	{
		if (is_array($link_a)) {
			$i = 0;
			foreach ($link_a as $link) {
				$check[$i]['link'] = $link;
				$i++;
			}
		}
		IfNotSetNull($check);
		return $check;
	}

	/**
	 * google_maps::get_link_data()
	 * Filter den Link aus dem Content raus
	 *
	 * @param mixed $inhalt
	 * @return mixed
	 */
	function gmap_get_link_data($inhalt = array())
	{
		//$inhalt=str_ireplace("</address><address>",", ",$inhalt);

		//Inhalte aufsplitten
		$ausgabe = array();
		//preg_match_all("|<address>(.*)</address>|U", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$inhalt=preg_replace('/<address[^>]+>/i','<address>',$inhalt);
		preg_match_all("/<address>(.*?)<\/address>/is", $inhalt, $ausgabe, PREG_PATTERN_ORDER);

		$causgabe =$ausgabe['1'];

		//R�ckgabe ARRAY mit den Links
		return $causgabe;
	}
}

$google_maps = new google_maps();
