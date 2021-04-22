<?php
/**
* Ein Password Feld erzeugen
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
$cfeld .= '<input type="password" size="';

if ((!empty($feld['mvcform_size'])))
{
	$cfeld .= $feld['mvcform_size'];
}
else
{
	$cfeld .= "30";
}
$cfeld .= '" name="' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="' . $feld['mvcform_name'] . '" value="';

// Options eintragen, aber nur, wenn ein Mitglied neu eingtragen wird,
// oder wenn das Passwortfeld beim Bearbeiten eines Mitglieds neu bef�llt wurde
if ($this->password_new == "ja"
	|| ($this->diverse->encode_quote($this->checked->{$feld['mvcform_name']}) != "" 
	&& $this->checked->zweiterunde == "ja")) $cfeld .= $this->diverse->encode_quote($this->checked->{$feld['mvcform_name']});
$cfeld .= '"/>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);

// wenn im Frontend, dann noch was dazwischen bauen, damit die div Bl�cke auch stimmen;)
if (!defined("admin"))
{
	//$cfeld .= '</div><div class="mv_feld">';
	$cfeld .= '<br />';
}
else
{
	$cfeld .= '<br />';
}
$cfeld .= '<label for="2_mvcform' . $feld['mvcform_name'] . '"';

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
$cfeld .= 'Best&auml;tigung';

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
$cfeld .= '<input type="password" size="';

if ((!empty($feld['mvcform_size'])))
{
	$cfeld .= $feld['mvcform_size'];
}
else
{
	$cfeld .= "30";
}
$cfeld .= '" name="2_' . $feld['mvcform_name'] . '" ';
$cfeld .= 'id="2_mvcform' . $feld['mvcform_name'] . '" value="';
$password_2 = "2_" . $feld['mvcform_name'];

// Options eintragen aber nur wenn ein Mitglied neu eingtragen wird, oder wenn das Passwortfeld beim bearbeiten eines Mitglieds neu bef�llt wurde
if ($this->password_new == "ja"
	|| ($this->diverse->encode_quote($this->checked->$password_2) != "" && $this->checked->zweiterunde
		== "ja"))$cfeld .= $this->diverse->encode_quote($this->checked->$password_2);
$cfeld .= '"/>';

$cfeld .= '<br />';
// Daten �bergeben
$this->feldarray[] = $cfeld; // (f�rs BE)
?>