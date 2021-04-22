<?php
/**
* Bildergalerie Feld ausgeben
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
$cfeld .= $feld['mvcform_label'] . '';
// Fallunterscheidung bei den Pflichtfeldern ob man im Front- oder Backend ist
if ($this->mv_back_or_front == "front")
{
	if ($feld['mvcform_must'] == 1) $cfeld .= ' * ';
}
elseif ($feld['mvcform_must_back'] == 1) $cfeld .= ' * ';
$cfeld .= '</label>';
if ($this->showbrs == 1) $cfeld .= '<br />';


// Flexeintrag Bearbeitungs-Modus
if (isset($this->checked->mv_content_id)) {
	// galerie info aus der session nehmen, damit bilder aus der "objekt eintrage"-maske, in der das objekt nicht
	// gespeichert wurde, nicht in der bearbeitungsmaske statt der eigentlichen bilder erscheinen
	unset($_SESSION["plugin_mv"][$feld['mvcform_name']]);
}


// Edit
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
	if (!$_SESSION['plugin_mv'][$feld['mvcform_name']])
	{
		if (count($result))
		{
			$galerie_name_arr = explode(";", $result[0]->{$feld['mvcform_name']});
			$this->checked->{$feld['mvcform_name']} = $result[0]->{$feld['mvcform_name']}; // Dem Feldnamen die Dateinamen zuweisen
		}
	}
	else
	{
		$galerie_name_arr = explode(";", $_SESSION['plugin_mv'][$feld['mvcform_name']]);
		$this->checked->{$feld['mvcform_name']} = $_SESSION['plugin_mv'][$feld['mvcform_name']]; // Dem Feldnamen die Dateinamen zuweise
	}
}
// Neueintrag
else
{
	// Falls schon etwas hochgeladen wurde
	if ($_SESSION['plugin_mv'][$feld['mvcform_name']])
	{
		$this->checked->{$feld['mvcform_name']} = $_SESSION['plugin_mv'][$feld['mvcform_name']];
		$galerie_name_arr = explode(";", $_SESSION['plugin_mv'][$feld['mvcform_name']]);
		$this->checked->{$feld['mvcform_name']} = $_SESSION['plugin_mv'][$feld['mvcform_name']]; // Dem Feldnamen die Dateinamen zuweise
	}
	else $this->checked->{$feld['mvcform_name']} = "";
}
// Feld und Button "Durchsuchen"
$cfeld .= '<input type="file" accept="image/*" class="mv_picture"';
$cfeld .= ' name="'
			. $feldname
			. '" '
			. 'id="'
			. $feldname
			. '" />';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
// Button Hinzuf�gen
$cfeld .= '<input type="submit" class="mv_picture" value="'
			. $this->content->template['plugin']['mv']['hinzufuegen']
			. '" name="'
			. $feldname
			. '_upload" />';

$imageUploaded = false;

// DELETE: Bild entfernen
if (!empty($this->checked->picdel['filename']) // wenn L�schen nicht aktiv ist
	AND $this->checked->picdel['fieldname'] == $feld['mvcform_name']) // sind wir beim richtigen Feld angekommen?
{
	$this->delete_picture($this->checked->picdel['filename'], 1); // Dateiname �bergeben beim richtigen Feld
	// auch aus der Liste raus, die in die DB geht
	$this->checked->{$feld['mvcform_name']} = str_replace($this->checked->picdel['filename'] . ";", "", $this->checked->{$feld['mvcform_name']});
	if (!empty($this->checked->{$feld['mvcform_name']})) $galerie_name_arr = explode(";", $this->checked->{$feld['mvcform_name']});
	else $galerie_name_arr = array();
	$_SESSION['plugin_mv'][$feld['mvcform_name']] = $this->checked->{$feld['mvcform_name']};
	if ($this->checked->template == "mv/templates/change_user.html"
		OR $this->checked->template == "mv/templates/mv_edit_front.html"
		OR $this->checked->template == "mv/templates/mv_edit_own_front.html") $this->update_file_pic_content_tables($this->checked->{$feld['mvcform_name']}, $feld['mvcform_name']);
}
// UPLOAD: Bild hochladen
elseif (is_array($this->checked->upload) &&
		in_array($feld['mvcform_name'], $this->checked->upload, true)
) {
	$this->upload_picture_mv($feldname, ""); // neues Bild hochladen, Feldname �bergeben (steht in $_FILES als Key)
	$this->checked->{$feld['mvcform_name']} .= $this->dateiname_upload . ";"; // neuen Dateinamen hinzuf�gen. Kommt in die DB
	$_SESSION['plugin_mv'][$feld['mvcform_name']] = $this->checked->{$feld['mvcform_name']}; // f�r die Thumbnail-Liste
	$galerie_name_arr[] = $this->dateiname_upload;
	$this->update_file_pic_content_tables($this->checked->{$feld['mvcform_name']}, $feld['mvcform_name']);
	$imageUploaded = true;
}
// Neueingabe speichern
elseif ($this->insert_ok == true
		&& !empty($this->checked->mv_submit)
		&& $this->checked->mv_submit == $this->content->template['plugin']['mv']['Eintragen'])
{
	if (isset($galerie_name_arr) && is_array($galerie_name_arr) && count($galerie_name_arr))
	{
		foreach ($galerie_name_arr AS $key => $value)
		{
			if (!empty($value))
			{
				$_SESSION['plugin_mv'][$feld['mvcform_name']] = $value;
				$this->upload_picture_mv($feldname, "new");
				// tempor�re Filenamen in echte f�r �bergabe an die DB umwandeln
				$this->checked->{$feld['mvcform_name']} = str_replace($value, $this->dateiname_upload, $this->checked->{$feld['mvcform_name']});
			}
		}
	}
}
$cfeld .= '<div class="mv_picture_edit mv_gallery_edit">';
// HTML-Ausgaben
// Meldung "Bild wurde gel�scht" ausgeben
if (isset($this->checked->picdel) // nur, wenn L�schen aktiv ist
	AND !empty($this->checked->picdel)
	AND $this->checked->picdel['fieldname'] == $feld['mvcform_name']) // und wir beim richtigen Feld angekommen sind
	$cfeld .= '<div>Datei "'
				. $this->checked->picdel['filename']
				. '" wurde gel&ouml;scht.</div>';
// Text: Bild "XXX" wurde hochgeladen
if ($imageUploaded) {
	$cfeld .= "<br /><div>"
			. $this->content->template['plugin']['mv']['bilddatei_teil_1']
			. "'"
			.  $_FILES[$feldname]['name']
			. "'"
			. $this->content->template['plugin']['mv']['datei_teil_2']
			. "</div>";
}
// Thumbnails-Ausgabe und Entfernen-Button
if (!empty($galerie_name_arr))
{
	$cfeld .= '<ul>';
	foreach($galerie_name_arr as $key => $value)
	{
		if (!empty($value))
		{
			$cfeld .= '<li style="list-style-type:none">';
			// Wegen Umsetzung von $this->checked->mvcformfeldname nach $this->checked->feldname s. change_user.php switch 235
			// Meldung ausgeben, wenn das Bild in der DB, aber nicht mehr im Verzeichnis vorhanden ist
			if ($this->checked->picdel['fieldname'] != $feld['mvcform_name']) // Falls L�schen aktiv ist: die gel�schte Datei nicht, aber alle anderen pr�fen
			{
				// Fehlermeldung, wenn nicht vorhanden
				if (!is_file($this->image_core->pfad_images . $value))
				{
					$cfeld .= '<div class="picture_error">Datei '
								.  "'images/"
								. $value
								. "' ist nicht vorhanden.</div>";
				}
			}
			// Thumbnail
			if (is_file($this->image_core->pfad_thumbs . $value))
			{
				$cfeld .= '<a href="'
							. $this->image_core->pfad_images_web
							. $value
							. '" title="" rel="lightbox">'
							. '<img src="'
							. $this->image_core->pfad_images_web
							. $this->diverse->encode_quote($value)
							. '" title="'
							. $feld['mvcform_label']
							. '" border="0" alt="'
							. $feld['mvcform_label']
							. '" />'
							. '</a>';
			}
			// Button entfernen, auch wenn Bild nicht existiert, Dann kann er dadurch aus der DB entfernt werden.
			$cfeld .= '<button type="submit" class="mv_picture" value="'
						. $this->content->template['plugin']['mv']['entfernen']
						. '" name="'
						. $feldname
						. '_picdel['
						. $value
						. ']" alt="'
						. $this->content->template['plugin']['mv']['entfernen']
						. '">'.$this->content->template['plugin']['mv']['entfernen'].'</button><br />';
			$cfeld .= "</li>";
		}
	}
	$cfeld .= "</ul></div>";
}
$this->feldarray[] = $cfeld; // (f�rs BE)
?>