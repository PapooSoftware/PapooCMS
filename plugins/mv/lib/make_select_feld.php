<?php
/**
* Select Boxen erzeugen
*/
$cfeld = "";
$cfeld .= '<label for="' . $feld['mvcform_name'] . '"';
// Wenn ein Fehler besteht
if ($this->error[$feld['mvcform_name']] == "error")
{
	if ((!empty($feld['mvcform_descrip']))) $cfeld .= '  class="form_error" >' . $feld['mvcform_descrip'] . ' ';
	else $cfeld .= '  class="form_error" >' . $this->content->template['plugin']['mv']['fehlermeldung'] . ' ';
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
$cfeld .= '<select name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" size="1" >';

// Options eintragen
if (!empty($feld['mvcform_lang_lookup']))
{
	$cdaten = $feld['mvcform_lang_lookup'];
	if ($feld['mvcform_default_wert'] != 1) $cfeld .= '<option value="0">' . $this->content->template['plugin']['mv']['bittewaehlen'] . '</option>';
	#if ($feld['mvcform_default_wert'] == 1
	#	&& $this->checked->$feld['mvcform_name'] != $cdaten[0])
	#{
		// ?????????????
	#}
	// Eintr�ge durchgehen
	if ((is_array($cdaten)))
	{
		foreach($cdaten as $daten)
		{
			if ($daten->lookup_id != 0
				&& $daten->content != "")
			{
				$cfeld .= '<option value="' . $daten->lookup_id;
				// Wenn ausgew�hlt aus redo
				if ($this->checked->{$feld['mvcform_name']} == $daten->lookup_id) $cfeld .= '" selected="selected"';
				else $cfeld .= '"';
				$cfeld .= '>';
				if ($daten->text) $cfeld .= $daten->text;
				else $cfeld .= $daten->content;
				$cfeld .= '</option>';
			}
		}
	}
}
$cfeld .= '</select>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';
$this->feldarray[] = $cfeld; // (f�rs BE)
?>