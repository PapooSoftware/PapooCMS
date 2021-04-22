<?php
/**
* Ein Textarea Feld erzeugen
*/

if (!isset($this->content->template['tinymce_lang_id']))
	$this->content->template['tinymce_lang_id'] = $this->cms->lang_id;
if (!isset($this->content->template['css_data_klassen']))
	$this->get_css_klassen();

$cfeld = "";
$cfeld .= '<label for="tinymvcform' . $feld['mvcform_name'] . '"';

// Wenn ein Fehler besteht
if ($this->error[$feld['mvcform_name']] == "error")
{
	if ((!empty($feld['mvcform_descrip'])))
	{
		$cfeld .= '  class="form_error" >' . $feld['mvcform_descrip'] . ' ';
	}
	else
	{
		$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['mv']['fehlermeldung'] . ' ';
	}
}
else
{
	$cfeld .= '>';
}
$cfeld .= $feld['mvcform_label'] . '';

// Fallunterscheidung bei den Pflichtfeldern ob man im Front- oder Backend ist
if ($this->mv_back_or_front == "front")
{
	if ($feld['mvcform_must'] == 1)
	{
		$cfeld .= ' * ';
	}
}
else
{
	if ($feld['mvcform_must_back'] == 1)
	{
		$cfeld .= ' * ';
	}
}
$cfeld .= '</label>';

if ($this->showbrs == 1)
{
	$cfeld .= '<br />';
}

$cfeld .= '<textarea cols="40" rows="16" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="tinymvcform' . $feld['mvcform_name'] . '" >';
// Options eintragen
$cfeld .= $this->diverse->encode_quote($this->checked->{$feld['mvcform_name']});
$cfeld .= '</textarea>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';
$cfeld = "nobr:" . $cfeld;
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
$this->show_tiny = 1;
$this->tiny_elements .= 'tinymvcform' . $feld['mvcform_name'] . ',';
?>