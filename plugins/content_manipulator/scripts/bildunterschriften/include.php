<?php

/**
 * Class bildunterschrift
 */
class bildunterschrift
{
	//Diesen Eintrag auf YES setzen um ihn zu aktivieren
	var $bildunterschrift = "NO";

	/**
	 * bildunterschrift constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if(!defined("admin") || ($_GET['is_lp'] == 1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if($this->bildunterschrift == "YES") {
				$output = $this->erzeuge_bild_unterschrift($output);
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Bildunterschriften im Content Bereich";
		$this->content->template['plugin_cm_body']['de'][] = 'Dieses Skript aktivieren Sie indem Sie in der Datei /plugins/content_manipulator/scripts/bildunterschriften/include.php den Eintrag var $bildunterschriften = "YES" setzen. Dann werden bei allen Bildern im Contentbereich die title Einträge zu Bildunterschriften.';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param $output
	 *
	 * @return mixed|string|string[]|null
	 */
	function erzeuge_bild_unterschrift( $output )
	{
		$ndat = explode("<!--Artikelbereich, hier kommt der Inhalt-->", $output);
		$xdat = explode("<!-- ### end_of_content -->", $ndat['1']);
		// Die alt Inhalte
		@mb_regex_encoding('UTF-8');

		preg_match_all('/(alt=.)([a-zA-Z0-9 äöü(),:!\-.&©]{1,})/', ($xdat['0']), $alt);

		// Die img tags komplett
		preg_match_all('/<img[^>]*>/Ui', $xdat['0'], $img);
		// Die Bilder selber
		preg_match_all("/img (.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $xdat['0'],
			$bilder );
		$bilderliste = $bilder['2'];
		// Das drumherum div
		$div1 = '<div style="';
		$i = 0;
		$pfad = PAPOO_ABS_PFAD;
		$pfad = str_replace(PAPOO_WEB_PFAD, "", $pfad);
		// Die Bilder durchgehen und ersetzen
		foreach($img['0'] as $key => $value) {
			// Dann die alts durchgehen
			if(is_array($alt['2'])) {
				foreach($alt['2'] as $akey => $avalue) {
					if(stristr($value, $avalue)) {
						// Bildgröße ermitteln
						if(is_array($bilderliste)) {
							foreach($bilderliste as $ikey => $ivalue) {
								if(stristr($value, $ivalue) && stristr($ivalue, ".")) {
									$size = @getimagesize($pfad . "" . $ivalue);
								}
							}
						}
						preg_match_all('/(width=.)([a-zA-Z0-9\s]{1,})/', $value, $width);
						if(empty($width['2']['0']) && isset($size['0'])) {
							$width1 = $size['0'];
						}
						else {
							$width1 = $width['2']['0'];
						}
						preg_match_all('/(float.)([a-zA-Z0-9\s]{1,})/', $value, $float);
						preg_match_all('/(margin.)([a-zA-Z0-9-:\s]{1,})/', $value, $margin);
						preg_match_all('/(padding-.)([a-zA-Z0-9\s]{1,})/', $value, $padding);
						if(!empty($margin['0']['0'])) {
							$value2 = @str_ireplace($margin['0']['0'], "", $value);
						}
						else {
							$value2 = $value;
						}
						if(!empty($margin['0']['1'])) {
							$value2 = @str_ireplace($margin['0']['1'], "", $value2);
						}
						if(!empty($margin['0']['2'])) {
							$value2 = @str_ireplace($margin['0']['2'], "", $value2);
						}
						if(!empty($margin['0']['3'])) {
							$value2 = @str_ireplace($margin['0']['3'], "", $value2);
						}
						$value2 = @str_ireplace("float", "", $value2);
						// teaserbild
						if(stristr($value, "teaserbildleft")) {
							$value2 = str_ireplace("teaserbildleft", "", $value2);
							$extramargin = "margin-right:10px;";
						}
						if(stristr($value, "teaserbildright")) {
							$value2 = str_ireplace("teaserbildright", "", $value2);
							$extramargin = "margin-left:10px;";
						}
						IfNotSetNull($margin['0']['0']);
						IfNotSetNull($margin['0']['1']);
						IfNotSetNull($margin['0']['2']);
						IfNotSetNull($margin['0']['3']);
						IfNotSetNull($padding['0']['0']);
						IfNotSetNull($float['0']['0']);
						IfNotSetNull($extramargin);

						$ersetz[$i][] = $div1 . "width:" . $width1 . "px;" . $float['0']['0'] . ";" . $margin['0']['0'] .
							";" . $margin['0']['1'] . ";" . $margin['0']['2'] . ";" . $margin['0']['3'] .
							";" . $padding['0']['0'] . $extramargin . "\"><div style=\"\">" . $value2 .
							"</div><div style=\"font-size:90%;color:#999;text-align:right;border:1px solid #999;border-top:0;word-wrap:break-word;padding-right:2px;\">" .
							$avalue . "</div></div>";
						$ersetz[$i][] = $value;
					}
				}
			}
			$i++;
		}
		if(isset($ersetz) && is_array($ersetz)) {
			foreach($ersetz as $key => $value) {
				$output = str_ireplace($value['1'], $value['0'], $output);
			}
		}
		return $output;
	}
}

$bildunterschrift = new bildunterschrift();
