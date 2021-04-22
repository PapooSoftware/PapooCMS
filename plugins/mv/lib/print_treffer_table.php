<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $result, $hit, $back_or_front $result ist �berfl�ssig !!!)
/**
* Baut die Mitglieder-/Objektliste als table auf (BE)
* called by show_user_list() mv.php (userlist.html)
*/

//Meta ID festlegen
$meta_id = $this->meta_gruppe;
if (!defined("admin")) if (is_numeric($this->checked->extern_meta)) $meta_id = $this->checked->extern_meta;
$this->intervall = $back_or_front == "mvcform_list" ? $this->intervall_back : $this->intervall_front; // Wozu? Hier nur BE!
$this->weiter->make_limit($this->intervall);

#$time_start = microtime(true);
$orderby = $this->mv_art == 2 ? 'mv_content_userid' : 'mv_content_id';
$sql = sprintf("SELECT *
						FROM %s T1
						INNER JOIN %s T2 ON (T1.mvcform_group_id = T2.mvcform_group_id)
						WHERE mvcform_form_id = '%s' 
						AND mvcform_meta_id = '%d'
						GROUP BY mvcform_id
						ORDER BY T2.mvcform_group_order_id, mvcform_order_id DESC",
						
						$this->cms->tbname['papoo_mvcform'],
						
						$this->cms->tbname['papoo_mvcform_group'],
						
						$this->db->escape($this->checked->mv_id),
						$this->db->escape($meta_id)
				);
$mvcform_type = $this->db->get_results($sql, ARRAY_A);

$fields = [
	'mv_content_id',
	'mv_content_owner',
	'mv_content_userid',
	'mv_content_search',
	'mv_content_sperre',
	'mv_content_teaser',
	'mv_content_create_date',
	'mv_content_edit_date',
	'mv_content_create_owner',
	'mv_content_edit_user',
];
foreach ($mvcform_type AS $key => $value) {
	$fields[] = "{$value['mvcform_name']}_{$value['mvcform_id']}";
}
// Holt alle S�tze inkl. Satzanzahl, die zur Metaebene und mv_id passen
// die sonst. Metaebenen enthalten immer alle zugeordneten Metaebenen, auch die Hauptmetaebene
// von daher wird die Hauptmetaebene hier nicht ben�tigt
$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS %s
						FROM %s
  						INNER JOIN %s ON (mv_meta_lp_user_id = mv_content_id)
						WHERE mv_meta_lp_meta_id = '%d'
						AND mv_meta_lp_mv_id = '%d'
						ORDER BY %s DESC
						%s",
						implode(', ', $fields),
  						$this->cms->tbname['papoo_mv_meta_lp'],
						
 						$this->cms->tbname['papoo_mv']
						. "_content_"
						. $this->db->escape($this->checked->mv_id)
						. "_search_"
						. $this->db->escape($this->cms->lang_back_content_id),
						
						$this->db->escape($meta_id),
						$this->db->escape($this->checked->mv_id),
						$orderby,
						$this->db->escape($this->weiter->sqllimit)
				);
$result = $this->db->get_results($sql);
$this->weiter->result_anzahl = $this->content->template['anzahl'] = $anzahl = $this->db->get_var("SELECT FOUND_ROWS()");
#$time_end = microtime(true);
#$totaltime = $time_end - $time_start;
#echo "Die Abfrage dauerte " . $totaltime . " Sekunden";

$loop = 0;
// alle CR raus
$this->content->template['mv_liste_keys'] = $this->content->template['mv_liste'] = "nobr:";
$this->weiter->weiter_link =  "plugin.php"
								. "?menuid="
								. $this->checked->menuid
								. "&template="
								. $this->checked->template
								. "&amp;mv_id="
								. $this->checked->mv_id;
$this->weiter->modlink = "no";
$this->weiter->do_weiter("teaser");

if (count($mvcform_type)) foreach($mvcform_type as $dtp) { $dtp_ar[$dtp['mvcform_id']] = $dtp['mvcform_type']; }
$this->finde_die_art_der_verwaltung_heraus();

if (!empty($result))
{
	// dann die Eintr�ge durchloopen
	if ($this->mv_art != 2) $this->content->template['mv_liste_keys'] .= "<th>ID</th>";
	foreach($result as $row)
	{
		$mv_liste = "";
		foreach($row as $key => $value)
		{
			// die id zwischenspeichern f�r multiselect look up tabellen
			if ($key == "mv_content_id") $mv_content_id = $value;
			
			if ($key == "mv_content_sperre"
				&& $value == 0) $is_aktiver_eintrag = 1;
			// die ersten 7 key/value Eintr�ge ignorieren
			if (!($key == "mv_content_id"
				|| $key == "mv_content_owner"
				|| $key == "mv_content_sperre"
				|| $key == "mv_content_teaser"
				|| $key == "mv_content_create_date"
				|| $key == "mv_content_create_owner"
				|| $key == "mv_content_edit_date"
				|| $key == "mv_content_edit_user"))
			{
				list($find_name, $find_id) = $this->get_feld_name($key);
				// die Eintr�ge notieren
				if ($this->feld_is_listed($find_id, $back_or_front))
				{
					// beim ersten Durchgang sich die Spaltennamen(keys) notieren
					if ($loop < 1)
					{
						// Sonderfall userid
						if ($find_name == ""
							&& $find_id == "userid") $find_name_label = $find_id;
						// ansonsten aus der Lang Tabelle das Label f�r das Feld holen
						else
						{
							$sql = sprintf("SELECT mvcform_label
													FROM %s 
													WHERE mvcform_lang_id = '%d' 
													AND mvcform_lang_lang = '%d' 
													AND mvcform_lang_meta_id = '%d'",
													$this->cms->tbname['papoo_mvcform_lang'],
													$this->db->escape($find_id),
													$this->db->escape($this->cms->lang_back_content_id),
													$this->db->escape($meta_id)
											);
							$find_name_label = $this->db->get_var($sql);
						}
						if ($this->mv_art != 2)
						{
							if (!empty($find_name_label)) $this->content->template['mv_liste_keys'] .= "<th>" . $find_name_label . "</th>";
						}
						elseif (!empty($find_name_label)) $this->content->template['mv_liste_keys'] .= "<th>" . $find_name_label . "</th>";
							else $this->content->template['mv_liste_keys'] .= "<th>Content-Id</th><th>User-ID</th>";
					}
					#if ($this->mv_art != 2) // Bei MV nicht die content_id anzeigen, f�r MV wird sp�ter die content_userid angezeigt
					#{
						if (empty($mv_liste))
						{
							// "bearbeiten" Link am Ende der Reihe einbauen
			/*$this->content->template['mv_liste'] .= "<td><a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "&amp;userid="
													. $row->mv_content_userid
													. "\">"
													. $this->content->template['plugin']['mv']['mv_list_bearbeiten']
													. "</a></td>";*/
							// Link-Backend auf Mitglied bearbeiten Template
							$link = $_SERVER['PHP_SELF']
														. "?menuid="
														. $this->checked->menuid
														. "&template=mv/templates/change_user.html";
							$mv_liste .= "<td><a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "&amp;userid="
													. $row->mv_content_userid
													. "\" title=\"Edit Content-Id $mv_content_id\">"
													. $mv_content_id
													. "</a></td>";
							if ($this->mv_art != 2) continue;
						}
					#}
					// was f�r eine Art von feld ist es?
					$mvcform_type = $dtp_ar[$find_id];

					if ($find_id != "userid")
					{
						// Markierung f�r neue Feldtypen
						switch($mvcform_type)
						{
							//f�r das Multiselect Feld die Werte aus der entsprechenden Look Up Tabelle holen
							case "multiselect":
								$value = $this->get_multiselect_werte($find_id, $find_name, $mv_content_id);
								break;
							// Wenn ein Password Feld dann nur *** ausgeben
							case "password":
								$value = "******";
								break;
							// Wenn ein Timestamp Feld dann entsprechend Formatieren
							case "timestamp":
								if (!empty($value))
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
							// Wenn es ein Upload Bild ist, dann Thumbnail anzeigen, bei der Galerie nur das 1. Bild
							case "galerie":
								if ($value) $value = $this->make_galerie_first_images($value);
							case "picture":
								if ($value)
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
							// Wenn es ein File Upload ist, dann schneide den Zeitstempel/feldname/FeldID vorne weg
							case "file":
								$dateilink = $this->cms->webverzeichnis . "/files/" . $value;
								#$dateiname = substr($value, 19 + strlen($find_id) + strlen($find_name), strlen($value));
								#$dateiname = substr($value, 11, strlen($value));
								$dateiname = $value;
								$value = '<a href="' . $dateilink . '" target="_blank">' . $dateiname . '</a>';
								break;
							// wenn select, radio oder check Feld dann Werte aus der Lookup Tabelle holen
							case "select":
								$value = $this->get_lp_wert($find_id, $find_name, $mv_content_id);
								break;
							// pre_select added khmweb
							case "pre_select":
								$value = $this->get_lp_wert($find_id, $find_name, $mv_content_id);
								$val_pre_array = explode("+++", $value);
								if (!empty($val_pre_array['0'])) $value = $val_pre_array['0'];
								break;
							case "radio":
								$value = $this->get_lp_wert($find_id, $find_name, $mv_content_id);
								break;
							case "check":
								$value = !empty($value) ?  $this->get_lp_wert($find_id, $find_name, $mv_content_id) : 0;
								$value = empty($value) ? $this->content->template['plugin']['mv']['istnein'] : $value;
								break;
							// Preisintervall
							case "preisintervall":
								$value = $value . $this->get_feld_waehrung($find_id, $this->checked->mv_id);
								break;
							default: break;
							// und was ist mit pre_select?
						}
					}
					// ?? $hit ist immer "" !!! khmweb
					// Falls die Tabelle das Ergebnis einer Suche ist, dann auch den Suchbegriff jeweils mit bold markieren
					if ($hit != ""
						&& $mvcform_type != "picture")
					{
						$teile = explode($hit, $value, 2); // markiert nur den ersten Treffer!!!
						if ($teile[0] != $value) $value = $teile[0] . "<b>" . $hit . "</b>" . $teile[1];
					}
					// die Eintr�ge notieren
					$mv_liste .= "<td>$value</td>";
				}
			}
		}
		if ($is_aktiver_eintrag == 1)
		{
			$this->content->template['mv_liste'] .= "<tr class=\"aktiver_eintrag\">";
			$is_aktiver_eintrag = 0;
		}
		else $this->content->template['mv_liste'] .= "<tr class=\"nicht_aktiver_eintrag\">";
		$this->content->template['mv_liste'] .= "$mv_liste";
		if ($back_or_front == "mvcform_list")
		{
			// Link-Backend auf Mitglied bearbeiten Template
			$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=mv/templates/change_user.html";
			// "l�schen" Link am Ende der Reihe einbauen
			if ($this->content->template['is_admin'] == "ok")
			{
				$this->content->template['mv_liste'] .= "<td><a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "&amp;userid="
													. $row->mv_content_userid
													. "&amp;fertig=del&amp;submitdel=ja\">"
													. $this->content->template['plugin']['mv']['mv_list_del']
													. "</a></td>";
			}
			else $this->content->template['mv_liste'] .= "<td></td>";
			// "bearbeiten" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= "<td><a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "&amp;userid="
													. $row->mv_content_userid
													. "\">"
													. $this->content->template['plugin']['mv']['mv_list_bearbeiten']
													. "</a></td>";
													
				// chgd. by khmweb f�r die Aktivierung mehrerer S�tze �ber Checkboxen 12.11.09/09.02.11
				// "aktivieren" Link am Ende der Reihe einbauen
			$this->content->template['mv_liste'] .= '<td>'.
				'<input type="checkbox" name="mv_content_active[]" value="'.$row->mv_content_id.'"'.($row->mv_content_sperre ? '' : ' checked="checked"').' />'.
				'<input type="hidden" name="mv_content_userid['.$row->mv_content_id.']" value="'.$row->mv_content_userid.'" />'.
				'</td><td>'.
				'<input type="checkbox" name="mv_content_teaser[]" value="'.$row->mv_content_id.'"'.($row->mv_content_teaser ? ' checked="checked"' : '').' />'.
				'</td>';
		}
		else
		{
			// Link-Frontend auf Mitglied bearbeiten Template
			$link = $_SERVER['PHP_SELF']
					. "?menuid="
					. $this->checked->menuid
					. "&template=mv/templates/mv_show_front.html";
			$this->content->template['mv_liste'] .= "<td><a href=\"$link&amp;mv_id="
													. $this->checked->mv_id
													. "&amp;mv_content_id="
													. $row->mv_content_id
													. "\">"
													. $this->content->template['plugin']['mv']['more_info']
													. "</a></td>";
		}
		$this->content->template['mv_liste'] .= "</tr>";
		$loop++;
	}
}
if ($back_or_front == "mvcform_list")
{
	// noch ein <td> Tag f�r den in der Kopfreihe fehlenden bearbeiten <td> Tag
	$this->content->template['mv_liste_keys'] .= "<th>Mitglied</th>";
}
$this->content->template['mv_liste_keys'] .= "<th>Mitglied</th><th>aktiv</th><th>anteasern</th>";
// Ausgeben das es Treffer gab
$this->content->template['message'] = "treffer";
?>