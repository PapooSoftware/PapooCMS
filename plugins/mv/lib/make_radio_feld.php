<?php
/**
* Radio Buttons erzeugen
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
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '</label><br />';


if (!empty($feld['mvcform_lang_lookup']))
{
	$cdaten = $feld['mvcform_lang_lookup'];
	// Eintr�ge durchgehen
	if ((is_array($cdaten)))
	{
		foreach($cdaten as $daten)
		{
			if ($daten->lookup_id != 0 && $daten->content != "")
			{
				$cfeld .= "<input class=\"radio_flex_input\" type=\"radio\" name=\"{$feld["mvcform_name"]}\" ".
					"id=\"{$feld["mvcform_name"]}_{$daten->lookup_id}\" value=\"{$daten->lookup_id}\" ".
					($this->checked->{$feld["mvcform_name"]} == $daten->lookup_id ? "checkes=\"checked\" " : "").
					">";

				$cfeld .= "<label class=\"radio_flex_label\" for=\"{$feld["mvcform_name"]}_{$daten->lookup_id}\"";

				// Wenn ein Fehler besteht
				if ($this->error[$feld['mvcform_name']] == "error")
				{
					if ((!empty($feld['mvcform_descrip']))) $cfeld .= '  class="form_error" >' . $feld['mvcform_descrip'] . ' ';
					else $cfeld .= '  class="form_error" >' . $this->content->template['plugin']['mv']['fehlermeldung'] . ' ';
				}
				else $cfeld .= '>';
				$cfeld .= $daten->content . '';
				$cfeld .= '</label>';
				
				$cfeld .= '<br />';
			}
		}
	}
}
$cfeld .= '<br />';
$this->feldarray[] = $cfeld; // (f�rs BE)
?>