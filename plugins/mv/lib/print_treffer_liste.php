<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $result, $hit, $back_or_front
/**
* Baut die Mitgliederliste als Liste auf
*/
$meta_id = $this->meta_gruppe;
if (!defined("admin")) if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
$loop = 0;
if (empty($this->content->template['buffer_liste'])) $this->content->template['mv_liste'] = "nobr:";
else $this->content->template['mv_liste'] = "";
$this->content->template['mv_liste_keys'] = "nobr:";
// holt das Template f�rs Frontend aus der Datenbank
$sql = sprintf("SELECT template_content_all FROM %s 
											WHERE lang_id = '%d' 
											AND meta_id = '%d'",
											$this->cms->tbname['papoo_mv'] 
											. "_template_" 
											. $this->db->escape($this->checked->mv_id),
											$this->db->escape($this->cms->lang_back_content_id), 
											$this->db->escape($meta_id)
				);
$this->content->template['mv_template_vorlage'] = "nobr:" . $this->db->get_var($sql);
if ($this->content->template['mv_template_vorlage'] == "nobr:") $this->content->template['no_template'] = 1; // Fehler kein Template
// dann die Eintr�ge durchloopen
$this->content->template['mv_template_all'] = array();
$temp_counter = 0;
// $result aus search_user.php
if (!empty($result) AND !$this->content->template['no_template'])
{
	foreach($result as $row)
	{
		$this->content->template['mv_liste'] .= "<li>";
		$this->content->template['mv_template_all'][$temp_counter] = $this->content->template['mv_template_vorlage'];
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
				// beim ersten Durchgang die Spaltennamen(keys) notieren
				if ($loop < 1) if ($find_name == "" 
									&& $find_id == "userid") $find_name = $find_id; // Sonderfall userid
				// was f�r eine Art von feld ist es?
				switch($this->felder_typen[$find_name . "_" . $find_id])
				{
					// Markierung f�r neue Feldtypen
					// Standardfall: nimm den $value Wert
					default: break;
					// wenn Select oder radio Feld dann Werte aus der Lookup Tabelle holen
					case "select":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						break;
					case "pre_select":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						$val_pre_array = explode("+++", $value);
						if (!empty($val_pre_array['0'])) $value = $val_pre_array['0'];
						break;
					//f�r das Multiselect Feld die Werte aus der entsprechenden Look Up Tabelle holen
					case "multiselect":
						$value = $this->get_multiselect_werte($find_id, $find_name, $mv_content_id);
						break;
					// radio Buttons haben ebenfalls Lookup Werte
					case "radio":
						$value = $this->get_lp_wert_front($find_id, $find_name, $mv_content_id);
						break;
					// Wenn ein Timestamp Feld dann entsprechend formatieren
					case "timestamp":
						if ($value != "")
						{
							list($tag, $monat, $jahr) = $this->get_day_month_year($value);
							$value = $tag . "." . $monat . "." . $jahr;
						}
						break;
					// Wenn Zeitintervall Feld dann entsprechend Formatieren
					case "zeitintervall":
						list($anfang_datum, $ende_datum) = explode(",", $value);
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
						if (!empty($value)) $value = '<img src="' 
														. $this->image_core->pfad_thumbs_web
														. $value 
														. '" title="' 
														. $find_name 
														. '" alt="' 
														. $find_name . '" />';
						break;
					// Wenn es ein File Upload ist, dann schneide den Zeitstempel/feldname/FeldID vorne weg
					case "file":
						$dateilink = $this->cms->webverzeichnis . "/files/" . $value;
						$dateiname = substr($value, 19 + strlen($find_id) + strlen($find_name), strlen($value));
						$value = '<a href="' . $dateilink . '" target="_blank">' . $dateiname . '</a>';
						break;

					// Preisintervall
					case "preisintervall":
						$value = $value . $this->get_feld_waehrung($find_id, $this->checked->mv_id);
						break;
				}
				// Falls die Tabelle das Ergebnis einer Suche ist, dann auch den Suchbegriff jeweils mit bold makieren
				if ($hit != "" 
					&& $mvcform_type != "picture")
				{
					$teile = explode($hit, $value, 2); // makiert nur den ersten Treffer!!!
					if ($teile[0] != $value) $value = $teile[0] . "<strong>" . $hit . "</strong>" . $teile[1];
				}
				// die Eintr�ge notieren
				$this->content->template['mv_liste'] .= "\n\t$value";
				// F�rs Frontend
				$this->content->template['mv_template_all'][$temp_counter] = preg_replace('/#' . $find_name . '_' . $find_id . '#/', 
																							$value, 
																							$this->content->template['mv_template_all'][$temp_counter]);
			}
		}
		if ($back_or_front == "mvcform_list")
		{
			// Link-Backend auf Mitglied bearbeiten Template
			$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/change_user.html";
			// "l�schen" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id=" 
													. $this->checked->mv_id
													. "&amp;mv_content_id=" 
													. $row->mv_content_id 
													. "&amp;fertig=del&amp;submitdel=ja\">"
													. $this->content->template['plugin']['mv']['mv_list_del'] 
													. "</a>";
			// "bearbeiten" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id=" 
													. $this->checked->mv_id
													. "&amp;mv_content_id=" 
													. $row->mv_content_id 
													. "\">"
													. $this->content->template['plugin']['mv']['mv_list_bearbeiten'] 
													. "</a>";
		}
		else
		{
			// Link-Frontend auf Mitglied bearbeiten Template
			$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_show_front.html";
			$this->content->template['mv_liste'] .= "<a href=\"$link&amp;mv_id=" 
													. $this->checked->mv_id
													. "&amp;mv_content_id=" . $row->mv_content_id 
													. "\">"
													. $this->content->template['plugin']['mv']['more_info'] 
													. "</a>";
		}
		// Link-Frontend auf Mitglied bearbeiten Template
		//$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_show_front.html";
		//$this->content->template['mv_liste'] .= "<a href=\"$link&mv_id=".$this->checked->mv_id."&mv_content_id=".$row->mv_content_id."\">".$this->content->
		//template['plugin']['mv']['more_info']."</a>";
		$this->content->template['mv_template_all'][$temp_counter] = preg_replace('/\$\$(.*?)\$\$/',
																					"<a href=\"$link&mv_id=" 
																					. $this->checked->mv_id 
																					. "&mv_content_id=" 
																					. $row->mv_content_id
																					. "\">\\1</a>", 
																					$this->content->template['mv_template_all'][$temp_counter]);
		$temp_counter++;
		#$this->content->template['mv_liste'] .= "\n";
		$loop++;
	}
}
// Ausgeben, das es Treffer gab
$this->content->template['message'] = "treffer";
?>
