<?php
/**
* Hidden fields erzeugen
*/
$cfeld = "";
$cfeld .= '<input type="hidden" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" value="';
$cfeld .= $this->checked->{$feld['mvcform_name']};
$cfeld .= '"/>';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>