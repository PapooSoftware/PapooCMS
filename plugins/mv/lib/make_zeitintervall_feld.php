<?php
/**
* Zeitintervall
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
// Select Feld für den AnfangsTag
$select_anfang_tag = $this->content->template['plugin']['mv']['tag']
					. ' <select name="anfang_tag_'
					. $feld['mvcform_name']
					. '" id="anfang_tag_'
					. $feld['mvcform_name']
					. '" size="1" >';
$select_anfang_tag .= '<option value="">TT</option>';
$count = 1;
while($count < 32)
{
	$select_anfang_tag .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$tag = 'anfang_tag_' . $feld['mvcform_name'];
	if ($this->checked->$tag == $count) $select_anfang_tag .= '" selected="selected">';
	else $select_anfang_tag .= '">';
	$select_anfang_tag .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_anfang_tag .= '</select>';
// Select Feld für den EndeTag
$select_ende_tag = $this->content->template['plugin']['mv']['tag']
					. ' <select name="ende_tag_'
					. $feld['mvcform_name']
					. '" id="ende_tag_'
					. $feld['mvcform_name']
					. '" size="1" >';
$select_ende_tag .= '<option value="">TT</option>';
$count = 1;
while($count < 32)
{
	$select_ende_tag .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$tag = 'ende_tag_' . $feld['mvcform_name'];
	if ($this->checked->$tag == $count) $select_ende_tag .= '" selected="selected">';
	else $select_ende_tag .= '">';
	$select_ende_tag .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_ende_tag .= '</select>';
// Select Feld für den AnfangMonat
$select_anfang_monat = $this->content->template['plugin']['mv']['monat']
						. ' <select name="anfang_monat_'
						. $feld['mvcform_name']
						. '" id="anfang_monat_'
						. $feld['mvcform_name']
						. '" size="1" >';
$select_anfang_monat .= '<option value="">MM</option>';
$count = 1;
while($count < 13)
{
	$select_anfang_monat .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$monat = 'anfang_monat_' . $feld['mvcform_name'];
	if ($this->checked->$monat == $count) $select_anfang_monat .= '" selected="selected">';
	else $select_anfang_monat .= '">';
	$select_anfang_monat .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_anfang_monat .= '</select>';
// Select Feld für den EndeMonat
$select_ende_monat = $this->content->template['plugin']['mv']['monat']
						. ' <select name="ende_monat_'
						. $feld['mvcform_name']
						. '" id="ende_monat_'
						. $feld['mvcform_name']
						. '" size="1" >';
$select_ende_monat .= '<option value="">MM</option>';
$count = 1;
while($count < 13)
{
	$select_ende_monat .= '<option value="' . sprintf("%02d", $count);
	// Wenn ausgewählt aus redo
	$monat = 'ende_monat_' . $feld['mvcform_name'];
	if ($this->checked->$monat == $count) $select_ende_monat .= '" selected="selected">';
	else $select_ende_monat .= '">';
	$select_ende_monat .= sprintf("%02d", $count) . '</option>';
	$count++;
}
$select_ende_monat .= '</select>';
// Select Feld für das AnfangJahr
$select_anfang_jahr = $this->content->template['plugin']['mv']['jahr']
						. ' <select name="anfang_jahr_'
						. $feld['mvcform_name']
						. '" id="anfang_jahr_'
						. $feld['mvcform_name']
						. '" size="1" >';
$select_anfang_jahr .= '<option value="">JJJJ</option>';
$count = 1910;
while($count < 2037)
{
	$select_anfang_jahr .= '<option value="' . sprintf("%04d", $count);
	// Wenn ausgewählt aus redo
	$jahr = 'anfang_jahr_' . $feld['mvcform_name'];
	if ($this->checked->$jahr == $count) $select_anfang_jahr .= '" selected="selected">';
	else $select_anfang_jahr .= '">';
	$select_anfang_jahr .= sprintf("%04d", $count) . '</option>';
	$count++;
}
$select_anfang_jahr .= '</select>';
// Select Feld für das EndeJahr
$select_ende_jahr = $this->content->template['plugin']['mv']['jahr']
					. ' <select name="ende_jahr_'
					. $feld['mvcform_name']
					. '" id="ende_jahr_'
					. $feld['mvcform_name']
					. '" size="1" >';
$select_ende_jahr .= '<option value="">JJJJ</option>';
$count = 1910;
while($count < 2037)
{
	$select_ende_jahr .= '<option value="' . sprintf("%04d", $count);
	// Wenn ausgewählt aus redo
	$jahr = 'ende_jahr_' . $feld['mvcform_name'];
	if ($this->checked->$jahr == $count) $select_ende_jahr .= '" selected="selected">';
	else $select_ende_jahr .= '">';
	$select_ende_jahr .= sprintf("%04d", $count) . '</option>';
	$count++;
}
$select_ende_jahr .= '</select>';
$cfeld .= '<div class="mv_zeitintervall">';
$cfeld .= $select_anfang_tag . " " . $select_anfang_monat . " " . $select_anfang_jahr;
#$cfeld .= " " . $this->content->template['plugin']['mv']['bis'] . "<br />";
$cfeld .= $select_ende_tag . " " . $select_ende_monat . " " . $select_ende_jahr;
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '</div>';
$this->feldarray[] = $cfeld; // (fürs BE)
?>