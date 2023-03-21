<?php
/**
* Mehrfach Select Feld ausgeben
*/
$cfeld = "";
$cfeld .= '<a name="anker_mvcform' . $feld['mvcform_name'] . '"></a>';
$cfeld .= '<label for="mvcform' . $feld['mvcform_name'] . '"';
if ($this->error[$feld['mvcform_name']] == "error")
{
	if ((!empty($feld['mvcform_descrip']))) $cfeld .= '  class="form_error" >' . $feld['mvcform_descrip'] . ' ';
	else $cfeld .= '  class="form_error" >' . $this->content->template['plugin']['mv']['fehlermeldung'] . ' ';
}
else $cfeld .= '>';
$cfeld .= $feld['mvcform_label'] . '';
// Fallunterscheidung bei den Pflichtfeldern, ob man im Front- oder Backend ist
if ($this->mv_back_or_front == "front"
	AND $feld['mvcform_must'] == 1) $cfeld .= ' * ';
elseif ($feld['mvcform_must_back'] == 1) $cfeld .= ' * ';
$cfeld .= '</label>';
if ($this->showbrs == 1) $cfeld .= '<br />';
$cfeld .= '<select name="mvcform' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="mvcform' . $feld['mvcform_name'] . '" size="1" >';
// Options eintragen
if (!empty($feld['mvcform_lang_lookup']))
{
	$cdaten = $feld['mvcform_lang_lookup'];
	$cfeld .= '<option value="0">' . $this->content->template['plugin']['mv']['bittewaehlen'] . '</option>';
	// Einträge durchgehen
	if ((is_array($cdaten)))
	{
		foreach($cdaten as $daten)
		{
			if ($daten->lookup_id != 0 && ($daten->content != "" OR $daten->text != ""))
			{
				$cfeld .= '<option value="' . $this->diverse->encode_quote($daten->lookup_id);
				$cfeld .= '">';
				if ($daten->text) $cfeld .= $daten->text;
				else $cfeld .= $daten->content;
				$cfeld .= '</option>';
			}
		}
	}
}
$cfeld .= '</select>';
$cfeld .= '<input onClick="addMultiSelectOption(\'mvcform'
			. $feld['mvcform_name']
			. '\');" type="button" value="'
			. $this->content->template['plugin']['mv']['hinzufuegen']
			. '" name="multiselect_'
			. $feld['mvcform_name']
			. '" />';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />'."\n";
$cfeld .= '<ul class="multilist">';
$hidden = "";

// Sortierklasse um die Reihenfolge bereits ausgewählter Optionen zu behalten
if (!class_exists('_make_multiselect_field_sorter')) {
#[AllowDynamicProperties]
class _make_multiselect_field_sorter {
		function __construct(&$sort_by_array) {
			$this->arr_field_werte = &$sort_by_array;
		}
		function sort(&$a_obj, &$b_obj) {
			$a = $a_obj->lookup_id;
			$b = $b_obj->lookup_id;
			$akey = array_search($a, $this->arr_field_werte);
			$bkey = array_search($b, $this->arr_field_werte);
			if ($akey === FALSE && $bkey === FALSE){
				if ($a == $b) return 0;
				return ($a < $b) ? -1 : 1;
			}
			elseif ($akey === FALSE) return -1;
			elseif ($bkey === FALSE) return 1;
			else {
				if ($akey == $bkey) return 0;
				return ($akey < $bkey) ? -1 : 1;
			}
		}
	}
}

if (!empty($cdaten))
{
	// Hole ausgewählte Optionen aus DB
	$sql = sprintf("SELECT %s FROM %s
					WHERE mv_content_id = '%d'",
				$feld['mvcform_name'],
				
				$this->cms->tbname['papoo_mv']
					. "_content_"
					. $this->db->escape($this->checked->mv_id)
					. "_search_"
					. $this->db->escape($this->cms->lang_back_content_id),
					
				$this->db->escape($this->checked->mv_content_id)
		);
	$arr_field_werte = explode("\n", trim($this->db->get_var($sql)));
	
	
	// Sortiere, damit ausgewählte Optionen ihre Reihenfolge behalten.
	if (empty($this->checked->zweiterunde)) {
		$sorter = new _make_multiselect_field_sorter($arr_field_werte);
	} else {
		// Baue Sortierarray für Reloads (hässlich aber es funktioniert...)
		$idarray = array();
		foreach ($this->checked as $rkey => $rvalue)
			if (strpos($rkey,  "hiddenmvcform" . $feld['mvcform_name'] . "_") === 0)
				$idarray[] = substr($rkey, strlen('hiddenmvcform' . $feld['mvcform_name'] . '_'));
		$sorter = new _make_multiselect_field_sorter($idarray);
	}
	usort($cdaten, array($sorter, 'sort'));
	unset($sorter);
	
	// Baue Liste
	foreach($cdaten as $daten)
	{
		// Standardwerte für nicht ausgewählte Optionen
		$style = 'style="display:none;"';
		$value = "0";
		$treffer = "";
		// ist es ein Reload, weil z.B. Fehler bei Eingabe gemacht wurden
		if ($this->checked->zweiterunde == "ja")
		{
			$subbi = "hiddenmvcform" . $feld['mvcform_name'] . "_" . $daten->lookup_id;
			if ($this->checked->$subbi == 1) $treffer = "ja";
		}
		// oder ist es die "Erste Anzeige"
		else
		{
			$treffer = array_search($daten->lookup_id, $arr_field_werte);
		}
		// wenn die Option dabei ist, dann auch anzeigen und Wert auf 1 setzen
		// Abfrage auf ja, damit im Fehlerfall auch erneute Anzeige erfolgt
		if (is_numeric($treffer)
			OR $treffer == "ja")
		{
			$style = 'style="display:block;"';
			$value = "1";
		}
		// fürs Template zusammensetzen
		$daten->content = trim($daten->content);
		$daten->text = trim($daten->text);
		$cfeld .= '<li id="mvcform'
					. $feld['mvcform_name']
					. '_'
					. $daten->lookup_id
					. '" '
					. $style
					. '> <input onClick="delMultiSelectOption(\'mvcform'
					. $feld['mvcform_name']
					. '\',\''
					. $daten->lookup_id
					. '\');" type="button" value="'
					. $this->content->template['plugin']['mv']['entfernen']
					. '" /><div class="multilistitem">';
					if ($daten->text) $cfeld .= $daten->text;
					else $cfeld .= $daten->content;
					$cfeld .= '</div></li>';
		$hidden .= '<input type="hidden" id="hiddenmvcform'
					. $feld['mvcform_name']
					. '_'
					. $daten->lookup_id
					. '" name="hiddenmvcform'
					. $feld['mvcform_name']
					. '_'
					. $daten->lookup_id
					. '" value="'
					. $value
					. '"/>';
	}
}
$cfeld .= $hidden;
$cfeld .= '</ul>';
// Daten übergeben
$this->feldarray[] = $cfeld; // (fürs BE)
?>