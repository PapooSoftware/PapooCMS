<?php
/**
* Ein Preisintervall Feld erzeugen
*/
// Holt f�r das Feld die W�hrung aus der Datenbank
$feld_waehrung = $this->get_feld_waehrung($feld['mvcform_id'], $this->checked->mv_id);
$cfeld = "";
$cfeld .= '<label for="' . $feld['mvcform_name'] . '"';
// Wenn ein Fehler besteht
if ($this->error[$feld['mvcform_name']] == "error")
{
	if (!empty($feld['mvcform_descrip'])) $cfeld .= '  class="form_error" >' . $feld['mvcform_descrip'] . ' ';
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
$cfeld .= '<input type="text" size="';
if ((!empty($feld['mvcform_size']))) $cfeld .= $feld['mvcform_size'];
else $cfeld .= "30";
$cfeld .= '" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" value="';
// OPtions eintragen
$cfeld .= $this->diverse->encode_quote($this->checked->{$feld['mvcform_name']});
$cfeld .= '"/><span class="waehrung">' . $feld_waehrung . '</span>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>