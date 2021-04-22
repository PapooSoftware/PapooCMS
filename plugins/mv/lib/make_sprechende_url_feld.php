<?php
/**
* Ein Textinuput Feld erzeugen
*/
$cfeld = "";
$cfeld .= '<label for="' . $feld['mvcform_name'] . '"';
// Wenn ein Fehler besteht
if ($this->error[$feld['mvcform_name']] == "error")
{
	$cfeld .= ' class="form_error" >';
	#$cfeld .= empty($feld['mvcform_descrip']) ? $this->content->template['plugin']['mv']['fehlermeldung'] : $feld['mvcform_descrip'];
	$cfeld .= empty($feld['mvcform_descrip']) ? "" : $feld['mvcform_descrip'];
	$cfeld .= ' ';
}
else $cfeld .= '>';
$cfeld .= $feld['mvcform_label'] . '';

// Fallunterscheidung bei den Pflichtfeldern ob man im Front- oder Backend ist
if ($this->mv_back_or_front == "front")
{
	if ($feld['mvcform_must'] == 1) $cfeld .= ' * ';
}
else
{
	if ($feld['mvcform_must_back'] == 1) $cfeld .= ' * ';
}
$cfeld .= '</label>';

if ($this->showbrs == 1) $cfeld .= '<br />';
$cfeld .= '<input type="text" size="';

$cfeld .= empty($feld['mvcform_size']) ? "30" : $feld['mvcform_size'];

$cfeld .= '" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" value="';
// OPtions eintragen
$cfeld .= $this->diverse->encode_quote($this->checked->{$feld['mvcform_name']});
$cfeld .= '"/>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
#$cfeld .= '<br />';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>