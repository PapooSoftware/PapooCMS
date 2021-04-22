<?php
/**
* Felddefinitions-Angaben checken bei Neueingabe eines Feldes
* 
* called by create_new_field() (mv.php)
*/
$this->checked->mvcform_name = trim($this->checked->mvcform_name);
$this->checked->mvcform_label = trim($this->checked->mvcform_label);
$this->checked->mvcform_name = preg_replace("/[^a-zA-Z0-9]/", "", $this->checked->mvcform_name);

// Name, Gruppierung und Label m�ssen f�r alle Feldtypen vorhanden sein & d�rfen nicht identisch mit Systemnamen sein
if (empty($this->checked->mvcform_name)
	OR $this->checked->mvcform_name == "Benutzername"
	OR $this->checked->mvcform_name == "passwort"
	OR $this->checked->mvcform_name == "email"
	OR $this->checked->mvcform_name == "antwortmail"
	OR $this->checked->mvcform_name == "newsletter"
	OR $this->checked->mvcform_name == "board"
	OR $this->checked->mvcform_name == "active"
	OR $this->checked->mvcform_name == "signatur") $this->content->template['fehler1'] = $fehler = 1;
if (empty($this->checked->mvcform_group_id)) $this->content->template['fehler13'] = $fehler = 1;
if (empty($this->checked->mvcform_label)) $this->content->template['fehler2'] = $fehler = 1;

// Nur diese Feldtypen sind erlaubt
if (!($mvcform_type == "check"
	OR $mvcform_type == "email"
	OR $mvcform_type == "file"
	OR $mvcform_type == "galerie"
	OR $mvcform_type == "link"
	OR $mvcform_type == "multiselect"
	OR $mvcform_type == "password"
	OR $mvcform_type == "picture"
	OR $mvcform_type == "pre_select"
	OR $mvcform_type == "preisintervall"
	OR $mvcform_type == "radio"
	OR $mvcform_type == "select"
	OR $mvcform_type == "text"
	OR $mvcform_type == "textarea"
	OR $mvcform_type == "textarea_tiny"
	OR $mvcform_type == "timestamp"
	OR $mvcform_type == "zeitintervall"
	OR $mvcform_type == "artikel"
	OR $mvcform_type == "flex_verbindung"
	OR $mvcform_type == "flex_tree"
	OR $mvcform_type == "hidden"
	OR $mvcform_type == "sprechende_url")) $this->content->template['fehler15'] = $fehler = 1; // ung�ltiger mvcform_type
else
{
	switch ($mvcform_type)
	 {
	 	case "check":
		case "multiselect":
		case "pre_select":
		case "radio":
		case "select":
			$check_fields = array('content_type' => 0, 'minlen' => 0, 'maxlen' => 0, 'fieldlen' => 0, 'select_values' => 1);
			break;
		case "email":
		case "link":
			$check_fields = array('content_type' => 0, 'minlen' => 0, 'maxlen' => 0, 'fieldlen' => 1, 'select_values' => 0);
			break;
		case "password":
			$check_fields = array('content_type' => 0, 'minlen' => 1, 'maxlen' => 1, 'fieldlen' => 1, 'select_values' => 0);
			break;
		case "text":
			$check_fields = array('content_type' => 1, 'minlen' => 1, 'maxlen' => 1, 'fieldlen' => 1, 'select_values' => 0);
			break;
		case "textarea":
		case "textarea_tiny":
			$check_fields = array('content_type' => 0, 'minlen' => 1, 'maxlen' => 0, 'fieldlen' => 0, 'select_values' => 0);
			break;
	 }
}

// Alle max. L�ngen werden durch den Feldtyp text in den angelegten Feldern der Tabellen bestimmt! (nicht mehr durch varchar(255))
// Check Inhaltstyp bei Feldtyp text
if ($check_fields['content_type']
	AND !($this->checked->mvcform_content_type == "alpha"
			OR $this->checked->mvcform_content_type == "num")) $this->content->template['fehler16'] = $fehler = 1; // Inhaltstyp fehlt

// Check Min. L�nge numerische Pr�fung, wenn vorgegeben
if ($check_fields['minlen']
	AND !empty($this->checked->mvcform_minlaeng)
	AND !ctype_digit($this->checked->mvcform_minlaeng)) $this->content->template['fehler5'] = $fehler = 1; // Min. L�nge nicht numerisch

//  Check Max. L�nge numerische Pr�fung, wenn vorgegeben
if ($check_fields['maxlen']
	AND !empty($this->checked->mvcform_maxlaeng)
	AND !ctype_digit($this->checked->mvcform_maxlaeng)) $this->content->template['fehler6'] = $fehler = 1; // Max. L�nge nicht numerisch

// Bei password und text muss Min. L�nge >= Max. L�nge sein, wenn angegeben
if ($check_fields['minlen'] AND $check_fields['maxlen']
	AND !empty($this->checked->mvcform_maxlaeng)
	AND $this->checked->mvcform_minlaeng > $this->checked->mvcform_maxlaeng) $this->content->template['fehler7'] = $fehler = 1; // Min. < Max.

// Feldl�nge in der Anzeige: numerische Pr�fung, wenn vorgegeben
if ($check_fields['fieldlen']
	AND !empty($this->checked->mvcform_size)
	AND !ctype_digit($this->checked->mvcform_size)) $this->content->template['fehler8'] = $fehler = 1; // Max. L�nge nicht numerisch

//  Check Min./Max. L�ngenangaben
//  Check Min. L�nge text, password
if ($check_fields['minlen']
	AND ctype_digit($this->checked->mvcform_minlaeng)
	AND ($mvcform_type == "text" OR $mvcform_type == "password")
	AND $this->checked->mvcform_minlaeng > 255) $this->content->template['fehler17'] = $fehler = 1; // Min. L�nge > 255
	
//  Check Min. L�nge textarea, textarea_tiny
if ($check_fields['minlen']
	AND ctype_digit($this->checked->mvcform_minlaeng)
	AND ($mvcform_type == "textarea" OR $mvcform_type == "textarea_tiny") 
	AND $this->checked->mvcform_minlaeng > 65535) $this->content->template['fehler23'] = $fehler = 1; // Min. L�nge > 65535
	
//  Check Max.L�nge text, password
if ($check_fields['maxlen']
	AND ctype_digit($this->checked->mvcform_maxlaeng)
	AND ($mvcform_type == "text" OR $mvcform_type == "password")
	AND $this->checked->mvcform_maxlaeng > 255) $this->content->template['fehler18'] = $fehler = 1; // Min. L�nge > 255
	
//  Check Max. L�nge textarea, textarea_tiny
if ($check_fields['maxlen']
	AND ctype_digit($this->checked->mvcform_maxlaeng)
	AND ($mvcform_type == "textarea" OR $mvcform_type == "textarea_tiny") 
	AND $this->checked->mvcform_maxlaeng > 65535) $this->content->template['fehler24'] = $fehler = 1; // Min. L�nge > 65535
	
//  Check anzuzeigende Eingabe-Feld-L�nge
if ($check_fields['fieldlen']
	AND ctype_digit($this->checked->mvcform_size)
	AND $this->checked->mvcform_size > 255) $this->content->template['fehler19'] = $fehler = 1; // Min. L�nge > 255
	
// Plausib f�r check, multiselect, pre_select, radio, select
if ($mvcform_type == "check"
	OR $mvcform_type == "multiselect"
	OR $mvcform_type == "pre_select"
	OR $mvcform_type == "radio"
	OR $mvcform_type == "select")
{
	$content_neu_array = explode("\r\n", trim($this->checked->mvcform_content_list));
	// Wenn Eingaben vorliegen, Eingaben pr�fen
	if (strlen($this->checked->mvcform_content_list))
	{
		// Checkbox max. 1 Wert
		if ($mvcform_type == "check"
			AND count($content_neu_array) > 1) $this->content->template['fehler3'] = $fehler = 1;

		// multiselect, pre_select, select mind. 2 Werte
		elseif (($mvcform_type == "multiselect" OR $mvcform_type == "pre_select" OR $mvcform_type == "select")
				AND count($content_neu_array) < 2) $this->content->template['fehler21'] = $fehler = 1;

		// Check das pre_select-Format (ausgabe+++eingabe)
		// Check max. L�ngen f�r Feldtypen check, radio, select, multiselect 
		foreach ($content_neu_array AS $key => $value)
		{
			$value = trim($value);
			if ($mvcform_type == "pre_select")
			{
				$presel = explode("+++", $value);
				if (count($presel) != 2) $this->content->template['fehler12'] = $fehler = 1; // Preselect falsches Format
				elseif (empty($presel[0])
						OR empty($presel[1])) $this->content->template['fehler12'] = $fehler = 1; // Preselect hat mind. einen empty value -> wrong format
				if (strlen($value) > 65535)
				{
					$this->content->template['presel_length'] = strlen($presel[0]) + strlen($presel[1]) + 3;
					$this->content->template['fehler22'] = $fehler = 1; // Zu viele Zeichen
				}
			}
			elseif (empty($value)) $this->content->template['fehler10'] = $fehler = 1; // einer der Werte ist empty
			// Feldtypen check, multiselect, radio, select
			elseif (($mvcform_type == "check" OR $mvcform_type == "radio" OR $mvcform_type == "select")
					AND strlen($value) > 255)
			{
				$this->content->template['crs_length'] = strlen($value);
				$this->content->template['fehler20'] = $fehler = 1; // Zu viele Zeichen
			}
			elseif ($mvcform_type == "multiselect"
					AND strlen($value) > 65535)
			{
				$this->content->template['multiselect_length'] = strlen($value);
				$this->content->template['fehler22'] = $fehler = 1; // Zu viele Zeichen
			}
		}
	}
	else $this->content->template['fehler9'] = $fehler = 1; // Kein Auswahlwert angegeben. Ist erforderlich.
}
?>
