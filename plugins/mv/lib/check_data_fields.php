<?php
/**
* Daten der Felder checken, wenn Felder ge�ndert werden
*
* called by change_field.php
*/
$this->checked->mvcform_name = trim($this->checked->mvcform_name);
$this->checked->mvcform_label = trim($this->checked->mvcform_label);
$this->checked->mvcform_name = preg_replace("/[^a-zA-Z0-9]/", "", $this->checked->mvcform_name);

// Name, Gruppierung und Label m�ssen f�r alle Feldtypen vorhanden sein
if (empty($this->checked->mvcform_name)) $this->content->template['fehler1'] = $fehler = 1;
if (empty($this->checked->mvcform_group_id)) $this->content->template['fehler13'] = $fehler = 1;
if (empty($this->checked->mvcform_label)) $this->content->template['fehler2'] = $fehler = 1;

$mvcform_type = $this->checked->mvcform_type;
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
	AND ctype_digit($this->checked->mvcform_minlaeng)
	AND ctype_digit($this->checked->mvcform_maxlaeng)
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
	if ($mvcform_type == "pre_select")
	{
		// Daten werden ge�ndert (EditMode)
		// Gibt es neue Werte? (in der Textarea unter den alten Werten)
		if (!empty($this->checked->mvcform_content_list_new))
		{
			$content_neu_array = explode("\r\n", trim($this->checked->mvcform_content_list_new));
			foreach ($content_neu_array AS $key => $value)
			{
				$value = trim($value);
				$presel = explode("+++", $value);
				if (count($presel) != 2) $this->content->template['fehler12'] = $fehler = 1;
				elseif (empty($presel[0])
					OR empty($presel[1])) $this->content->template['fehler12'] = $fehler = 1;
				if (strlen($value) > 65535)
				{
					$this->content->template['presel_length'] = strlen($presel[0]) + strlen($presel[1]) + 3;
					$this->content->template['fehler22'] = $fehler = 1; // Zu viele Zeichen
				}
			}
		}
		if (count($this->checked->mvcform_content_list))
		{
			// Alte Werte pr�fen, k�nnten ge�ndert worden sein
			foreach ($this->checked->mvcform_content_list AS $key => $value)
			{
				$value = trim($value);
				if (empty($value)) // Eintrag l�schen?
				{
					$this->del_field_value_active = $this->delete_radio_value($key); 
					unset($this->checked->mvcform_content_list[$key]); // und aus dem Array
				}
				else
				{
					$presel = explode("+++", $value);
					if (count($presel) != 2) $this->content->template['fehler12'] = $fehler = 1;
					elseif (empty($presel[0])
						OR empty($presel[1])) $this->content->template['fehler12'] = $fehler = 1;
					if (strlen($value) > 65535)
					{
						$this->content->template['presel_length'] = strlen($presel[0]) + strlen($presel[1]) + 3;
						$this->content->template['fehler22'] = $fehler = 1; // Zu viele Zeichen
					}
				}
			}
		}
		if ((count($content_neu_array) + count($this->checked->mvcform_content_list)) < 2) $this->content->template['fehler21'] = $fehler = 1; // mind. 2 Eintr�ge
	}
	else
	{
		// Checkbox mit mehreren Werten: Zukunftsmusik ;-) daher so belassen, klappt auch mit einem Wert
		if ($mvcform_type == "check")
		{
			if (count($this->checked->mvcform_content_list))
			{
				// alle einzelnen Werte aus dem Array pr�fen
				foreach ($this->checked->mvcform_content_list AS $key => $value)
				{
					$value = trim($value);
					if ($value == ""
						AND count($this->checked->mvcform_content_list) > 1)
					{
						// Wert l�schen in der DB bei Eingabe von "" im Feld
						// eingebaut, weil es fehlerhafte Installs gibt und damit diese �berfl�ssige Werte loswerden k�nnen
						$this->del_field_value_active = $this->delete_radio_value($key); 
						unset($this->checked->mvcform_content_list[$key]); // und aus dem Array
					}
					elseif (empty($value)) $this->content->template['fehler10'] = $fehler = 1; // Null/leer nicht erlaubt, wenn nur ein Feld
					elseif (strlen($value) > 255) // Check length
					{
						$this->content->template['crs_length'] = strlen($value);
						$this->content->template['fehler20'] = $fehler = 1; // Zu viele Zeichen
					}
				}
			}
		}
		elseif ($mvcform_type == "radio"
			OR $mvcform_type == "select"
			OR $mvcform_type == "multiselect")
		{
			// L�ngen-Pr�fung
			if (count($this->checked->mvcform_content_list))
			{
				foreach ($this->checked->mvcform_content_list AS $key => $value)
				{
					$value = trim($value);
					if (($mvcform_type == "radio" OR $mvcform_type == "select")
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
			// Wenn g�ltige Eingaben vorhanden sind, weiterpr�fen
			if (!($this->content->template['fehler20']
				AND $this->content->template['fehler22']))
			{
				if (count($this->checked->mvcform_content_list))
				{
					// alle einzelnen Werte aus dem Array pr�fen
					foreach ($this->checked->mvcform_content_list AS $key => $value)
					{
						$value = trim($value);
						if ($value == "")
						{
							$this->del_field_value_active = $this->delete_radio_value($key); // Wert l�schen in der DB
							unset($this->checked->mvcform_content_list[$key]); // und aus dem Array
						}
						elseif ($value == "0") $this->content->template['fehler10'] = $fehler = 1; // Null nicht erlaubt
						else $count1++;
					}
				}
				// Falls auch neue hinzukommen, diese pr�fen
				if (strlen($this->checked->mvcform_content_list_new))
				{
					// Alle einzelnen Werte aus der Textarea pr�fen
					$content_neu_array = explode("\r\n", trim($this->checked->mvcform_content_list_new));
					foreach ($content_neu_array AS $key => $value)
					{
						$value = trim($value); // 1. und/oder letzte Leerzeile raus
						if (empty($value)) $this->content->template['fehler10'] = $fehler = 1; // Eine der Eingaben ist 0 / leer
						else
						{
							$count2++;
							if (($mvcform_type == "radio" OR $mvcform_type == "select")
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
				}
				if (($mvcform_type == "select"
					OR $mvcform_type == "multiselect")
					AND (($count1 + $count2) < 2)) $this->content->template['fehler21'] = $fehler = 1; // mind. 2 Eintr�ge
			}
		}
	}
}
$this->content->template['formeingabe_fehler'] = $fehler; // Generelle Meldung ans Template, dass irgendein Fehler vorliegt
?>
