<?php
/**
 * File Upload Feld ausgeben
 */
$feldname = 'mvcform' . $feld['mvcform_name']; // spr. Kurzform
$cfeld = "";
$cfeld .= '<label for="'
	. $feldname
	. '"';
// Wenn ein Fehler besteht
if ($this->error[$feld['mvcform_name']] == "error")
{
	if ((!empty($feld['mvcform_descrip']))) $cfeld .= ' class="form_error" >'
		. $feld['mvcform_descrip']
		. ' ';
	else $cfeld .= ' class="form_error" >'
		. $this->content->template['plugin']['mv']['fehlermeldung']
		. ' ';
}
else $cfeld .= '>';
$cfeld .= $feld['mvcform_label'];

// Fallunterscheidung bei den Pflichtfeldern ob man im Front- oder Backend ist
if ($this->mv_back_or_front == "front")
{
	if ($feld['mvcform_must'] == 1) $cfeld .= ' * ';
}
elseif ($feld['mvcform_must_back'] == 1) $cfeld .= ' * ';
$cfeld .= '</label>';
if ($this->showbrs == 1) $cfeld .= '<br />';
if ($this->checked->template == "mv/templates/change_user.html"
	OR $this->checked->template == "mv/templates/mv_edit_front.html"
	OR $this->checked->template == "mv/templates/mv_edit_own_front.html")
{
	// Daten zur content_id einlesen, falls vorhanden
	$sql = sprintf("SELECT * FROM %s
		WHERE mv_content_id = '%s'
		LIMIT 1",

		$this->cms->tbname['papoo_mv']
		. "_content_"
		. $this->db->escape($this->checked->mv_id)
		. "_search_"
		. $this->db->escape($this->cms->lang_back_content_id),

			$this->db->escape($this->checked->mv_content_id)
		);
	$result = $this->db->get_results($sql);
	$this->checked->{$feld['mvcform_name']} = $result[0]->{$feld['mvcform_name']}; // alter Dateiname
	if ($_FILES[$feldname]['name']) $dateiname_neu = $_SESSION['plugin_mv'][$feld['mvcform_name']] = $_FILES[$feldname]['name']; // neuer Dateiname
	else $dateiname_neu = $_SESSION['plugin_mv'][$feld['mvcform_name']] = "";
}
// Neueintrag
else
{
	// Falls noch nichts hochgeladen wurde
	if ($_SESSION['plugin_mv'][$feld['mvcform_name']]) $this->checked->{$feld['mvcform_name']} = $_SESSION['plugin_mv'][$feld['mvcform_name']];
	else $this->checked->{$feld['mvcform_name']} = "";
	if ($_FILES[$feldname]['name']) $dateiname_neu = $_SESSION['plugin_mv'][$feld['mvcform_name']] = $_FILES[$feldname]['name']; // Name bei Neueingabe
}
// DELETE: File entfernen
if (!empty($this->checked->picdel) // wenn L�schen nicht aktiv ist
	AND $this->checked->picdel['fieldname'] == $feld['mvcform_name'])
{
	$this->delete_file($this->checked->picdel['filename']); // Dateiname �bergeben beim richtigen Feld
	$this->dateiname_upload = "";
	unset($_SESSION['plugin_mv'][$feld['mvcform_name']]);
	if ($this->checked->template == "mv/templates/change_user.html"
		OR $this->checked->template == "mv/templates/mv_edit_front.html"
		OR $this->checked->template == "mv/templates/mv_edit_own_front.html") $this->update_file_pic_content_tables("", $feld['mvcform_name']); 
}
else
{
	if (is_array($this->checked->upload) // wenn Hochladen aktiv ist
		AND $dateiname_neu // und nicht nur auf Hochladen ohne Fileauswahl gedr�ckt wurde
		AND in_array($feld['mvcform_name'], $this->checked->upload) // und wir beim richtigen Feld angekommen sind
	) {
		$this->delete_file($this->checked->{$feld['mvcform_name']}); // alten File l�schen. Dateiname �bergeben
		$this->upload_file_mv($feldname, ""); // neuen File hochladen,, Feldname �bergeben (steht in $_FILES als Key)
		$_SESSION['plugin_mv'][$feld['mvcform_name']] = $this->checked->{$feld['mvcform_name']} = $this->dateiname_upload; // auf neuen Dateinamen setzen f�r die weitere Verarbeitung
		$this->update_file_pic_content_tables($this->dateiname_upload, $feld['mvcform_name']);
	}
	// Speichern; Rename uploaded pics, auch thumb, nur bei Neueintrag
	// $this->content->template['plugin']['mv']['senden'] wird nur von fp_content.html und mv_create_front.html gesendet
	// Nur diese beiden dienen der Erstellung von Neueintr�gen
	if ($this->insert_ok == true
		&& !empty($this->checked->mv_submit)
		&& $this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen'])
	{
		if(empty($this->checked->{$feld['mvcform_name']}))
		{
			$this->delete_file($this->checked->{$feld['mvcform_name']}); // alten File l�schen. Dateiname �bergeben
			$this->upload_file_mv($feldname, ""); // neuen File hochladen,, Feldname �bergeben (steht in $_FILES als Key)
			$_SESSION['plugin_mv'][$feld['mvcform_name']] = $this->checked->{$feld['mvcform_name']} = $this->dateiname_upload; // auf neuen Dateinamen setzen f�r die weitere Verarbeitung
			$this->update_file_pic_content_tables($this->dateiname_upload, $feld['mvcform_name']);
		}

		$this->upload_file_mv($feldname, "new");
		$this->checked->{$feld['mvcform_name']} = $this->dateiname_upload; // f�r �bergabe an DB
	}
}
// Wegen Umsetzung von $this->checked->mvcformfeldname nach $this->checked->feldname s. change_user.php switch 235
// Meldung ausgeben, wenn das Bild in der DB, aber nicht mehr im Verzeichnis vorhanden ist
if (!empty($this->checked->{$feld['mvcform_name']}) // Name aus der DB
	AND $this->checked->picdel['fieldname'] != $feld['mvcform_name']) // Falls L�schen aktiv ist: die gel�schte Datei nicht, aber alle anderen pr�fen
{
	// Fehlermeldung, wenn nicht vorhanden
	if (!is_file($this->cms->pfadhier . "/files/" . $this->checked->{$feld['mvcform_name']}))
	{
		// Ist in der DB, aber nicht mehr im Verzeichnis -> Fehler ausl�sen
		$cfeld .= '<br /><div class="picture_error">Datei '
			.  "'files/"
			. $this->checked->{$feld['mvcform_name']}
			. "' ist nicht vorhanden.</div>";
	}
}

// Meldung "File wurde gel�scht" ausgeben
if (isset($this->checked->picdel) // nur, wenn L�schen aktiv ist
	AND !empty($this->checked->picdel)
	AND $this->checked->picdel['fieldname'] == $feld['mvcform_name']) // und wir beim richtigen Feld angekommen sind
{
	$cfeld .= '<div style="clear:left">Datei "'
		. $this->checked->{$feld['mvcform_name']}
		. '" wurde gel&ouml;scht.</div>';
}

// Text: File "XXX" wurde hochgeladen
if (is_array($this->checked->upload) // wenn Hochladen aktiv ist
	AND $dateiname_neu // und nicht nur auf Hochladen ohne Fileauswahl gedr�ckt wurde
	AND in_array($feld['mvcform_name'], $this->checked->upload)
	AND !empty($this->checked->{$feld['mvcform_name']})) // und wir beim richtigen Feld angekommen sind
{
	$cfeld .= '<div style="clear:left">' . $this->content->template['plugin']['mv']['datei_teil_1']
		. "'"
		. $this->checked->{$feld['mvcform_name']}
		. "'"
		. $this->content->template['plugin']['mv']['datei_teil_2']
		. "</div>";
}

// Feld und Button "Durchsuchen"
$cfeld .= '<input type="file" class="mv_picture"';
$cfeld .= ' name="'
	. $feldname
	. '" ';
$cfeld .= 'id="'
	. $feldname
	. '"';
$cfeld .= '/>';

// Button Hochladen
$cfeld .= '<input type="hidden" value="'
	. $this->content->template['plugin']['mv']['datei_hochladen']
	. '" name="'
	. $feldname
	. '_upload" />';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);

// Dateiname anzeigen und L�schfunktion
if (is_file($this->cms->pfadhier . "/files/" . $this->checked->{$feld['mvcform_name']}))
{
	// Dateiname anzeigen
	$cfeld .= '<div><br />'
		. $this->checked->{$feld['mvcform_name']}
		. '</div>';
}
if (!empty($this->checked->{$feld['mvcform_name']})
	AND $this->checked->picdel['fieldname'] != $feld['mvcform_name'])
{
	// Button "entfernen" anzeigen
	$cfeld .= '<div><br />'
		. ' <input type="submit" class="mv_picture" value="' 
		. $this->content->template['plugin']['mv']['entfernen']
		. '" name="'
		. $feldname 
		. '_picdel['
		. $this->checked->{$feld['mvcform_name']}
		. ']" alt="'
		. $this->content->template['plugin']['mv']['entfernen'] 
		. '" /></div>';

	$cfeld .= '<input type="hidden" value="1" name="'.$feldname.'_already_uploaded" />';
}
$cfeld .= '<br />';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>
