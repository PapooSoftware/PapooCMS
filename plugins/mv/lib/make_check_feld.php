<?php
/**
* Check Boxen erzeugen
*/
$cfeld = "";

$cfeld .= '<input type="checkbox" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" value="';
$cdaten = $feld['mvcform_lang_lookup']; // set by get_lang_werte_lookup() mv.php
if ((is_array($cdaten)))
{
	if ($cdaten[0]->lookup_id != 0
		&& $cdaten[0]->content != ""
		&& $cdaten[0]->content != "0") $cfeld .= $cdaten[0]->lookup_id . '" ';
	else $cfeld .= '1" '; // default, ok?
}
else $cfeld .= '1" '; // default
if ($this->checked->{$feld['mvcform_name']} == $cdaten[0]->lookup_id) $cfeld .= ' checked="checked"';
$cfeld .= '/>';

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

$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>