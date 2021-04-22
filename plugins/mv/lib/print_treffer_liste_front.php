<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $result, $hit, $back_or_front
/**
* bereitet die Suchtreffer f�rs Frontend auf
*/
$meta_id = $this->meta_gruppe;
if (!defined("admin")
	AND is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
//Felder Rechte rausholen
$this->get_field_rights();
$loop = 0;
if (empty($this->content->template['buffer_liste'])) $this->content->template['mv_liste'] = "nobr:";
else $this->content->template['mv_liste'] = "";
$this->content->template['mv_liste_keys'] = "nobr:";
// holt das Template f�rs Frontend aus der Datenbank
$this->content->template['mv_template_vorlage'] = "nobr:";
$sql = sprintf("SELECT template_content_all
						FROM %s
						WHERE lang_id = '%d'
						AND meta_id = '%d'",

						$this->cms->tbname['papoo_mv']
						. "_template_"
						. $this->db->escape($this->checked->mv_id),

						$this->db->escape($this->cms->lang_id),
						$this->db->escape($meta_id)
				);
$this->content->template['mv_template_vorlage'] .= $this->db->get_var($sql);


// smarty templates include in ausgabe-formatierung ermoeglichen
$GLOBALS["smarty"]->assign($this->content->template);
$this->content->template["mv_template_vorlage"] = preg_replace_callback('/\{include file="([^"]+)"\}/',
	function ($match) {
		$template = PAPOO_ABS_PFAD."/styles/{$GLOBALS["cms"]->style_dir}/templates/".$match[1];
		if (file_exists($template)) {
			return $GLOBALS["smarty"]->fetch($template);
		}
		else {
			return "{Error: Template '$template' not found.}";
		}
	}, $this->content->template["mv_template_vorlage"]
);


// dann die Eintr�ge durchloopen
$this->content->template['mv_template_all'] = array();
$temp_counter = 0;
if (is_array($result) && count($result) > 0)
{
	$template_type = 'replace';
	if (strpos($this->content->template['mv_template_vorlage'], 'nobr:##SMARTY##') === 0) {
		$template_type = 'smarty';
		$smarty_file = PAPOO_ABS_PFAD."/templates_c/mv_template_vorlage_".$this->checked->mv_id."_".$this->cms->lang_id.".html";
		file_put_contents($smarty_file.'.tmp', substr($this->content->template['mv_template_vorlage'], strlen('nobr:##SMARTY##')), LOCK_EX);
		rename($smarty_file.'.tmp', $smarty_file);
	}

	$sprechendeUrlColName = $this->get_sprechende_url_col_name($this->checked->mv_id);
	foreach($result as $row)
	{
		$this->content->template['mv_liste'] .= "<li>";
		$this->content->template['mv_template_all'][$temp_counter] = $this->content->template['mv_template_vorlage'];

		$replacements = ['id' => $row->mv_content_id, 'mv_id' => $this->checked->mv_id];

		foreach($row as $key => $value)
		{
			// die id zwischenspeichern f�r multiselect look up tabellen
			if ($key == "mv_content_id") $mv_content_id = $value;
			// die ersten sechs key/value Eintr�ge ignorieren
			if ($key == "mv_content_id"
				|| $key == "mv_content_owner"
				|| $key == "mv_content_userid"
				|| $key == "mv_content_sperre"
				|| $key == "mv_content_teaser"
				|| $key == "mv_content_create_date"
				|| $key == "mv_content_create_owner"
				|| $key == "mv_content_edit_date"
				|| $key == "mv_content_edit_user")
            {
				$this->content->template['mv_liste'] .= "\n\t$value";
                $key_ohne_mv_content = str_replace('mv_content_', '', $key);
				$this->content->template['mv_template_all'][$temp_counter] = preg_replace("/#$key_ohne_mv_content#/", $value, $this->content->template['mv_template_all'][$temp_counter]);
            }
            else
			{
				list($find_name, $find_id) = $this->get_feld_name($key);
				//Hier checken ob an dem aktuellen Feld die Leserechte bestehen.
				$die_aktuelle_id_aray = explode("_", $key);
				$die_aktuelle_id = array_pop($die_aktuelle_id_aray);
				if (is_numeric($die_aktuelle_id))
				{
					if (!in_array($die_aktuelle_id, $this->felder_rechte_aktuelle_gruppe))
					{
						$value = "";
						$this->content->template['mv_liste'] .= "\n\t$value";
						$this->content->template['mv_template_all'][$temp_counter] =
							preg_replace('/#' . $find_name . '_' . $find_id . '#/', $value, $this->content->template['mv_template_all'][$temp_counter]);
						//Keine Leserechte - dann abbrechen
						continue;
					}
				}
				// beim ersten Durchgang sich die Spaltennamen(keys) notieren
				if ($loop < 1
					AND $find_name == ""
					AND $find_id == "userid")
				{
					$find_name = $find_id;
				}
				// was für eine Art von feld ist es?
				switch($this->felder_typen[$find_name . "_" . $find_id])
				{
					// Markierung f�r neue Feldtypen
					// Standartfall nimm den $value Wert
					default: break;
					// wenn Select oder radio Feld dann Werte aus der Lookup Tabelle holen
					case "select":
						if ($template_type == 'smarty') {
							$value = [
								'id' => (int)$value,
								'text' => $this->get_lp_wert_front($find_id, $find_name, $mv_content_id),
							];
							break;
						}
						// Bei Sortierung erscheint Klartext... (check: Was, wenn numer. Klartext?)
						if (is_numeric($value)) $value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						break;
					//f�r das Multiselect Feld die Werte aus der entsprechenden Look Up Tabelle holen
					case "multiselect":
						if ($template_type == 'smarty') {
							$value = $this->get_multiselect_werte_array($find_id, $find_name, $mv_content_id);
						} else {
							$value = $this->get_multiselect_werte($find_id, $find_name, $mv_content_id);
						}
						break;
						break;
					// radio Buttons haben ebenfalls Lookup Werte
					case "radio":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						break;
					// radio Buttons haben ebenfalls Lookup Werte
					case "check":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						break;
					case "pre_select":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						$val_pre_array = explode("+++", $value);
						if (!empty($val_pre_array['0'])) $value = $val_pre_array['0'];
						break;
					// Wenn ein Timestamp Feld dann entsprechend formatieren
					case "timestamp":
						if ($value != "")
						{
							if (is_numeric($this->checked->datum))
							{
								//Nicht das aktuelle Datum - dann �berspringen
								if ($this->checked->datum != $this->checked->datum)
								{
									$this->content->template['mv_template_all'][$temp_counter] = "";
									$this->weiter->result_anzahl = $this->weiter->result_anzahl - 1;
									if ($this->weiter->result_anzahl < 10) $this->content->template['weiter'] = "0";
									$what = "teaser";
									$this->weiter->do_weiter($what);
								}
							}
							list($tag, $monat, $jahr) = $this->get_day_month_year($value);
							$value = $tag . "." . $monat . "." . $jahr;
						}
						break;
					// Wenn Zeitintervall Feld dann entsprechend Formatieren
					case "zeitintervall":
						list($anfang_datum, $ende_datum) = explode(",", $value);
						if (is_numeric($this->checked->datum))
						{
							//Nicht das aktuelle Datum - dann �berspringen
							if ($this->checked->datum < $anfang_datum or $this->checked->datum > $ende_datum)
							{
								$this->content->template['mv_template_all'][$temp_counter] = "";
								$this->weiter->result_anzahl = $this->weiter->result_anzahl - 1;
								if ($this->weiter->result_anzahl < 10) $this->content->template['weiter'] = "0";
								$what = "teaser";
								$this->weiter->do_weiter($what);
							}
						}
						if (!empty($anfang_datum))
						{
							list($tag, $monat, $jahr) = $this->get_day_month_year($anfang_datum);
							$value = $tag . "." . $monat . "." . $jahr;
						}
						if ($anfang_datum != $ende_datum)
						{
							if (!empty($ende_datum))
							{
								list($tag, $monat, $jahr) = $this->get_day_month_year($ende_datum);
								$value .= $this->content->template['plugin']['mv']['bis'] . $tag . "." . $monat . "." . $jahr;
							}
						}
						break;
					// Wenn ein Password Feld dann nur *** ausgeben
					case "password":
						$value = "******";
						break;
					// Wenn es ein Upload Bild ist, dann Thumbnail anzeigen
					case "galerie":
						$value = $this->make_galerie_first_images($value);
					case "picture":
						if (!empty($value))
						{
							$value = '<img src="'
										. $this->image_core->pfad_thumbs_web
										. $value
										. '" title="'
										. $find_name
										. '" alt="'
										. $find_name
										. '" class="imagesize" />';
						}
						break;
					case "file":
						$dateilink = $this->cms->webverzeichnis . "/files/" . $value;
						$dateiname = substr($value, 0);
						if ($this->output_html_link_for_file_upload_field) $value = '<a href="' . $dateilink . '" target="_blank">' . $dateiname . '</a>';
						else $value = $dateilink;
						break;
					// Preisintervall
					case "preisintervall":
						$value = $value . " " . $this->get_feld_waehrung($find_id, $this->checked->mv_id); // chgd. khmweb 1.12.09 W�hrung auf Abstand gesetzt
						break;
				}
				// Falls die Tabelle das Ergebnis einer Suche ist, dann auch den Suchbegriff jeweils mit bold makieren
				if ($hit != "" && $mvcform_type != "picture" && is_string($value))
				{
					$teile = explode($hit, $value, 2); // makiert nur den ersten Treffer!!!
					if ($teile[0] != $value) $value = $teile[0] . "<strong>" . $hit . "</strong>" . $teile[1];
				}
				// die Eintr�ge notieren
				$this->content->template['mv_liste'] .= "\n\t$value";
				// dzvhae Sonderfall
				if ($this->dzvhae_system_id
					&& $find_name . '_' . $find_id == $this->dzvhae_feld_flex_systemid)
				{
					$sql = sprintf("SELECT mv_content_id,
											%s,
											%s
											FROM %s
											WHERE %s = '%d'",
											$this->dzvhae_feld_flex_vorname,
											$this->dzvhae_feld_flex_nachname,

											$this->cms->tbname['papoo_mv']
											. "_content_1"
											. "_search_"
											. $this->cms->lang_id,

											$this->dzvhae_feld_flex_systemid,
											$this->db->escape($value)
									);
					$mv_daten = $this->db->get_results($sql, ARRAY_A);
					$value = $mv_daten[0][$this->dzvhae_feld_flex_vorname] . ' ' . $mv_daten[0][$this->dzvhae_feld_flex_nachname];
				}
				// Hier sind nur die, für die auch Rechte eingestellt sind und frontlist=1 haben. Andere im Template können nicht replaced werden.
				// Diese erscheinen mit Rauten drumrum...
				// Fürs Frontend
				$replacements[$find_name.'_'.$find_id] = $value;
				//print_r($replacements);
				if ($template_type !== 'smarty') {
					$this->content->template['mv_template_all'][$temp_counter] =
						preg_replace('/#' . $find_name . '_' . $find_id . '#/', $value, $this->content->template['mv_template_all'][$temp_counter]);
				}
			}
		}

		if ($template_type == 'smarty') {
			$GLOBALS["smarty"]->assign('nl', "\n");
			$GLOBALS["smarty"]->assign('menuid', $this->checked->menuid);
			$GLOBALS["smarty"]->assign('mv_item', $replacements);
			$this->content->template['mv_template_all'][$temp_counter] = 'nobr:'.$GLOBALS["smarty"]->fetch('file:'.$smarty_file);
			$GLOBALS["smarty"]->assign('mv_item', null);
		}

		if ($back_or_front == "mvcform_list")
		{
			// Link-Backend auf Mitglied bearbeiten Template
			$link = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $this->checked->menuid . "&template=mv/templates/change_user.html";
			// "l�schen" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;extern_meta="
													. $this->checked->extern_meta
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "&amp;fertig=del&amp;submitdel=ja\">"
													. $this->content->template['plugin']['mv']['mv_list_del']
													. "</a>";
			// "bearbeiten" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;extern_meta="
													. $this->checked->extern_meta
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "\">"
													. $this->content->template['plugin']['mv']['mv_list_bearbeiten']
													. "</a>";
		}
		else
		{
			// Link-Frontend auf Mitglied bearbeiten Template
			$link = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_show_front.html";
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;extern_meta="
													. $this->checked->extern_meta
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "\">"
													. $this->content->template['plugin']['mv']['more_info']
													. "</a>";
		}
	// Link-Frontend auf Mitglied bearbeiten Template
	//$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_show_front.html";
	//$this->content->template['mv_liste'] .= "<a href=\"$link&mv_id=".$this->checked->mv_id."&mv_content_id=".$row->mv_content_id."\">".$this->content->template['plugin']['mv']['more_info']."</a>";
		// Hole Standard Sprache im Frontend
		$lang_frontend = $this->db->get_var(sprintf("SELECT `lang_frontend` FROM `%s` LIMIT 1;", $this->cms->tbname['papoo_daten']));
		// Sprechend URL zur Einzelansicht generieren
		if($this->cms->mod_free == '1' && $sprechendeUrlColName !== false &&
			isset($row->$sprechendeUrlColName) && strlen($row->$sprechendeUrlColName) > 0) {
			$this->content->template['mv_template_all'][$temp_counter] = preg_replace_callback('/\$\$(.*?)\$\$(?:\[(?<class_names>[^\]]+)\])?/', function ($match) use ($lang_frontend, $row, $sprechendeUrlColName) {
				$classNames = $match['class_names'] ?? '';
				return sprintf("<a href=\"%s/%sf-%d-%d-%d-%s.html\"%s>%s</a>",
                    PAPOO_WEB_PFAD,
                    (($this->content->template['lang_short'] !== $lang_frontend) ? $this->content->template['lang_short']."/" : ""),
                    $this->checked->mv_id,
                    $row->mv_content_id,
                    $this->checked->menuid,
					$row->$sprechendeUrlColName,
					($classNames ? " class=\"{$classNames}\"" : ''),
					$match[1]
				); },
				$this->content->template['mv_template_all'][$temp_counter]
			);
		}
		else {
			$this->content->template['mv_template_all'][$temp_counter] =
				preg_replace_callback('/\$\$(.*?)\$\$(?:\[(?<class_names>[^\]]+)\])?/', function ($match) use ($link, $row) {
					$classNames = $match['class_names'] ?? '';
					$classAttribute = $classNames ? " class=\"{$classNames}\"" : '';
					return "<a href=\"$link&mv_id="
					. $this->checked->mv_id
					. "&extern_meta="
					. $this->checked->extern_meta
					. "&mv_content_id="
					. $row->mv_content_id
					. "&getlang="
					. $this->content->template['lang_short']
					. "\"{$classAttribute}>{$match[1]}</a>";
					},
					$this->content->template['mv_template_all'][$temp_counter]);
		}
		$this->content->template['mv_template_all'] =
			preg_replace('/#ID#/', $row->mv_content_id, $this->content->template['mv_template_all']);
		if (is_numeric($this->checked->mv_id)) $this->content->template['mv_template_all'] =
			preg_replace('/#MVID#/', $this->checked->mv_id, $this->content->template['mv_template_all']);
		$temp_counter++;
		$this->content->template['mv_liste'] .= "\n";
		$loop++;
	}
}
if (stristr($template,"mv_search_front_onemv.html"))
{
    $this->content->template['mv_liste']=array();
}
if ($this->dzvhae_system_id && $this->checked->mv_id == "3") $this->content->template['mv_dz_funktion'] = "ja";
if ($this->dzvhae_system_id && $this->checked->mv_id == "2") $this->content->template['mv_dz_weiterbildung'] = "ja";
// Ausgeben das es Treffer gab
$this->content->template['message'] = "treffer";
?>
