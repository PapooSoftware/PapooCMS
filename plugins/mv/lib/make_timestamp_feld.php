<?php
/**
* Timestamp
*/
$cfeld = "";
$cfeld .= '<label for="' . $feld['mvcform_name'] . '"';

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
// Select Feld für den Tag
$select_tag =
	$this->content->template['plugin']['mv']['tag'] . ' <select name="mvcform_tag_' . $feld['mvcform_name']
		. '" id="mvcform_tag_' . $feld['mvcform_name'] . '" size="1" >';
$select_tag .= '<option value="">TT</option>';
$count = 1;

while($count < 32)
{
	$select_tag .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$tag = 'mvcform_tag_' . $feld['mvcform_name'];

	if ($this->checked->$tag == $count)
	{
		$select_tag .= '" selected="selected">';
	}
	else
	{
		$select_tag .= '">';
	}
	$select_tag .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_tag .= '</select>';
// Select Feld für den Monat
$select_monat =
	$this->content->template['plugin']['mv']['monat'] . ' <select name="mvcform_monat_' . $feld['mvcform_name']
		. '" id="mvcform_monat_' . $feld['mvcform_name'] . '" size="1" >';
$select_monat .= '<option value="">MM</option>';
$count = 1;

while($count < 13)
{
	$select_monat .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$monat = 'mvcform_monat_' . $feld['mvcform_name'];

	if ($this->checked->$monat == $count)
	{
		$select_monat .= '" selected="selected">';
	}
	else
	{
		$select_monat .= '">';
	}
	$select_monat .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_monat .= '</select>';
// Select Feld für das Jahr
$select_jahr =
	$this->content->template['plugin']['mv']['jahr'] . ' <select name="mvcform_jahr_' . $feld['mvcform_name']
		. '" id="mvcform_jahr_' . $feld['mvcform_name'] . '" size="1" >';
$select_jahr .= '<option value="">JJJJ</option>';
$count = 1910;

while($count < 2037)
{
	$select_jahr .= '<option value="' . sprintf("%04d", $count);
	// Wenn ausgewählt aus redo
	$jahr = 'mvcform_jahr_' . $feld['mvcform_name'];

	if ($this->checked->$jahr == $count)
	{
		$select_jahr .= '" selected="selected">';
	}
	else
	{
		$select_jahr .= '">';
	}
	$select_jahr .= sprintf("%04d", $count) . '</option>';
	$count++;
}
$select_jahr .= '</select>';
$cfeld .= $select_tag . " " . $select_monat . " " . $select_jahr;
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';
$this->feldarray[] = $cfeld; // (fürs BE)
?>