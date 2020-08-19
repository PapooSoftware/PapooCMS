<?php
#####################################
# papoo Version 1.1                 #
# (c) Carsten Euwens 2003           #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version 4.2                   #
#####################################

// Klasse und URL-Überprüfungsfunktion einbinden
if (strstr( $_SERVER['PHP_SELF'],'function.php')) die('You are not allowed to see this page directly');

/**
 * @param $resource_type
 * @param $resource_name
 * @param $template_source
 * @param $template_timestamp
 * @param $smarty_obj
 * @return bool
 */
function make_template($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
	if( $resource_type == 'file' ) {
		$temp_template = PAPOO_ABS_PFAD."/styles_default/templates/".$resource_name;

		if (file_exists($temp_template)) {
			$smarty_obj->display($temp_template);
			return true;
		}
		else {
			//$smarty_obj->trigger_error('unable to read resource: "'.$resource_name.'"');
			return false;
		}
	}
	else {
		// keine Datei
		return false;
	}
}

/**
 * Funktion zum Überprüfen der URL-Syntax in BBCode
 *
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 * @return bool|string
 */
function do_bbcode_url($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
	// Tag hatte nicht das default-Attribut
	if ($openclose == 'all') {
		// invalid url
		if (check_url ($elem_contents, array ('http', 'ftp', 'mailto')) === false) {
			return false;
		}
		return '<a href="'.$elem_contents.'">'.$elem_contents.'</a>';
		// Tag hatte das default-Attribut und das hier ist der öffnende Tag
	}
	else if ($openclose == 'open') {
		// invalid url
		if (check_url ($attrs['default'], array ('http', 'ftp', 'mailto')) === false) {
			return false;
		}
		return '<a href="'.$attrs['default'].'">';
		// Tag hatte das default-Attribut und das hier ist der schließende Tag
	}
	else if ($openclose == 'close') {
		return '</a>';
		// Irgendwas seltsames geht vor sich
	}
	else {
		// Fehler
		return false;
	}
}

/**
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 * @return bool|string
 */
function do_bbcode_abk($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
	// Tag hatte nicht das default-Attribut
	if ($openclose == 'all') {
		return '<abbr title="'.$elem_contents.'">'.$elem_contents.'</abbr>';
		// Tag hatte das default-Attribut und das hier ist der öffnende Tag
	}
	else if ($openclose == 'open') {
		// invalid url
		return '<abbr title="'.$attrs['default'].'">';
		// Tag hatte das default-Attribut und das hier ist der schließende Tag
	}
	else if ($openclose == 'close') {
		return '</abbr>';
		// Irgendwas seltsames geht vor sich
	}
	else {
		// Fehler
		return false;
	}
}

/**
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 * @return bool|string
 */
function do_bbcode_acr($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
	// Tag hatte nicht das default-Attribut
	if ($openclose == 'all') {
		return '<acronym title="'.$elem_contents.'">'.$elem_contents.'</acronym>';
		// Tag hatte das default-Attribut und das hier ist der öffnende Tag
	}
	else if ($openclose == 'open') {
		// invalid url
		return '<acronym title="'.$attrs['default'].'">';
		// Tag hatte das default-Attribut und das hier ist der schließende Tag
	}
	else if ($openclose == 'close') {
		return '</acronym>';
		// Irgendwas seltsames geht vor sich
	}
	else {
		// Fehler
		return false;
	}
}

/**
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 * @return bool|string
 */
function do_bbcode_zit($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
	// Tag hatte nicht das default-Attribut
	if ($openclose == 'all') {
		return '<cite title="'.$elem_contents.'">'.$elem_contents.'</cite>';
		// Tag hatte das default-Attribut und das hier ist der öffnende Tag
	}
	else if ($openclose == 'open') {
		// invalid url
		return '<cite title="'.$attrs['default'].'">';
		// Tag hatte das default-Attribut und das hier ist der schließende Tag
	}
	else if ($openclose == 'close') {
		return '</cite>';
		// Irgendwas seltsames geht vor sich
	}
	else {
		// Fehler
		return false;
	}
}

/**
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 *
 * @deprecated Macht nichts
 */
function do_bb_alt($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
}

/**
 * Funktion zum Einbinden von Bildern in BBCode
 *
 * @param $tag_name
 * @param $attrs
 * @param $elem_contents
 * @param $func_param
 * @param $openclose
 * @return string
 */
function do_bbcode_img($tag_name, $attrs, $elem_contents, $func_param, $openclose)
{
	$temp_return = "";

	// Tag hatte nicht das default-Attribut
	if ($openclose == 'all') {
		$elem_contents = str_replace(array("'", '"'), "", $elem_contents);
		// invalid url
		if (strstr($elem_contents,"http:")) {
			$temp_return = '<img src="' . $elem_contents . '" alt="' . $image_alt . '" title="' . $image_title . '" />';
		}
	}
	return $temp_return;
}


/**
 * Alles bis auf Neuezeile-Zeichen entfernen
 *
 * @param $text
 * @return string|string[]|null
 */
function bbcode_stripcontents($text)
{
	return preg_replace ("/[^\n]/", '', $text);
}

/**
 * Den letzten Zeilenumbruch entfernen
 *
 * @param $text
 * @return string|string[]|null
 */
function bbcode_striplastlinebreak($text)
{
	$text = preg_replace ("/\n( +)?$/", '$1', $text);
	return $text;
}

/**
 * Zeilenumbrüche verschiedener Betriebsysteme vereinheitlichen
 *
 * @param $text
 * @return string|string[]|null
 */
function convertlinebreaks($text)
{
	return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

// wenn das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require_once(PAPOO_ABS_PFAD."/lib/bbcode.inc.php");
	$bbcode = new BBCode();
	// Objekt erzeugen
	global $bbcode;
	// Zeilenumbrüche vereinheitlichen für alle
	$bbcode->addParser ('convertlinebreaks', array ('block', 'inline', 'link', 'listitem', 'list'));
	// Nur für nicht-Listen
	$bbcode->addParser ('htmlspecialchars', array ('block', 'inline', 'link', 'listitem'));
	// nur für Listenelemente
	$bbcode->addParser ('bbcode_striplastlinebreak', array ('listitem'));
	// nur für Nicht-Listen
	$bbcode->addParser ('nl2br', array ('block', 'inline', 'link', 'listitem'));
	// nur für Listen
	$bbcode->addParser ('bbcode_stripcontents', array ('list'));
	// [b], [i]
	$bbcode->addCode ('engl', 'simple_replace', null, array ('<span lang="en" xml:lang="en">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('fran', 'simple_replace', null, array ('<span lang="fr" xml:lang="fr">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('span', 'simple_replace', null, array ('<span lang="es" xml:lang="es">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	/*
	$bbcode->addCode ('ha', 'simple_replace', null, array ('</p><h1>', '</h1><p>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hb', 'simple_replace', null, array ('</p><h2>', '</h2><p>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hc', 'simple_replace', null, array ('</p><h3>', '</h3><p>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hd', 'simple_replace', null, array ('</p><h4>', '</h4><p>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('he', 'simple_replace', null, array ('</p><h5>', '</h5><p>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	*/
	$bbcode->addCode ('ha', 'simple_replace', null, array ('<h1>', '</h1>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hb', 'simple_replace', null, array ('<h2>', '</h2>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hc', 'simple_replace', null, array ('<h3>', '</h3>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('hd', 'simple_replace', null, array ('<h4>', '</h4>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('he', 'simple_replace', null, array ('<h5>', '</h5>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('b', 'simple_replace', null, array ('<strong>', '</strong>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('i', 'simple_replace', null, array ('<span style="font-style:italic;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('verdana', 'simple_replace', null, array ('<span style="font-family:verdana;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('arial', 'simple_replace', null, array ('<span style="font-family:arial;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('times', 'simple_replace', null, array ('<span style="font-family:times;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('courier', 'simple_replace', null, array ('<span style="font-family:courier;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('sans', 'simple_replace', null, array ('<span style="font-family:sans-serif;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('blau', 'simple_replace', null, array ('<span style="color:blue;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('rot', 'simple_replace', null, array ('<span style="color:red;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('gruen', 'simple_replace', null, array ('<span style="color:green;">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	# Positionierung
	$bbcode->addCode ('links', 'simple_replace', null, array ('<span class="bildlinks">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	$bbcode->addCode ('rechts', 'simple_replace', null, array ('<span class="bildrechts">', '</span>'),
		'inline', array ('listitem', 'block', 'inline', 'link'), array ());
	// [url]http://...[/url], [url=http://...]Text[/url]
	$bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('default'),
		'link', array ('listitem', 'block', 'inline'), array ('link'));
	// [img]http://...[/img]
	$bbcode->addCode ('img', 'usecontent', 'do_bbcode_img', array (),
		'image', array ('listitem', 'block', 'inline', 'link'), array ());
	//[abk=][abk]
	$bbcode->addCode ('abk', 'usecontent?', 'do_bbcode_abk', array ('default'),
		'link', array ('listitem', 'block', 'inline'), array ('link'));
	//[acr=][acr]
	$bbcode->addCode ('acr', 'usecontent?', 'do_bbcode_acr', array ('default'),
		'link', array ('listitem', 'block', 'inline'), array ('link'));
	//[zit=][zit]
	$bbcode->addCode ('zit', 'usecontent?', 'do_bbcode_zit', array ('default'),
		'link', array ('listitem', 'block', 'inline'), array ('link'));
	// Nur zwei Bilder auf einmal
	$bbcode->setOccurrenceType ('img', 'image');
	$bbcode->setMaxOccurrences ('image', 20);
	// [ulist]
	//   [*]Eintrag
	// [/ulist]
	$bbcode->addCode ('uliste', 'simple_replace', null, array ('</p><ul>', '</ul><p>'),
		'list', array ('block', 'listitem'), array ());
	$bbcode->addCode ('*', 'simple_replace', null, array ('<li>', "</li>\n"),
		'inline', array ('list'), array ());
	// [olist]
	//   [*]Eintrag
	// [/olist]
	$bbcode->addCode ('oliste', 'simple_replace', null, array ('</p><ol>', '</ol><p>'),
		'list', array ('block', 'listitem'), array ());
	$bbcode->setCodeFlag ('*', 'no_close_tag', true);
}

/**
 * @param $text
 * @return string
 */
function convertum($text)
{
	$result = "";
	for($ico = 0; $ico < strlen($text); $ico++) {
		switch($text [$ico]) {
		case "ä":
			$result .= "&auml;";
			break;
		case "ü":
			$result .= "&uuml;";
			break;
		case "ö":
			$result .= "&ouml;";
			break;
		case "Ä":
			$result .= "&Auml;";
			break;
		case "Ü":
			$result .= "&Uuml;";
			break;
		case "Ö":
			$result .= "&Ouml;";
			break;
		case "ß":
			$result .= "&szlig;";
			break;
		case "é":
			$result .= "&eacute;";
			break;
		case "è":
			$result .= "&egrave;";
			break;
		case "ê":
			$result .= "&ecirc;";
			break;
		case "ë":
			$result .= "&euml";
			break;
		case "É":
			$result .= "&Eacute;";
			break;
		case "È":
			$result .= "&Egrave;";
			break;
		case "Ê":
			$result .= "&Ecirc;";
			break;
		case "Ë":
			$result .= "&Euml;";
			break;
		case "á":
			$result .= "&aacute;";
			break;
		case "à":
			$result .= "&agrave;";
			break;
		case "å":
			$result .= "&aring;";
			break;
		case "â":
			$result .= "&acirc;";
			break;
		case "Á":
			$result .= "&Aacute;";
			break;
		case "À":
			$result .= "&Agrave;";
			break;
		case "Â":
			$result .= "&Acirc;";
			break;
		case "Å":
			$result .= "&Aring;";
			break;
		case "Ã":
			$result .= "&Atilde;";
			break;
		case "í":
			$result .= "&iacute;";
			break;
		case "ì":
			$result .= "&igrave;";
			break;
		case "î":
			$result .= "&icirc;";
			break;
		case "ï":
			$result .= "&iuml;";
			break;
		case "Í":
			$result .= "&Iacute;";
			break;
		case "Ì":
			$result .= "&Igrave;";
			break;
		case "Î":
			$result .= "&Icirc;";
			break;
		case "Ï":
			$result .= "&Iuml;";
			break;
		case "ú":
			$result .= "&uacute;";
			break;
		case "ù":
			$result .= "&ugrave;";
			break;
		case "û":
			$result .= "&ucirc;";
			break;
		case "Ú":
			$result .= "&Uacute;";
			break;
		case "Ù":
			$result .= "&Ugrave;";
			break;
		case "Û":
			$result .= "&Ucirc;";
			break;
		case "ó":
			$result .= "&oacute;";
			break;
		case "ò":
			$result .= "&ograve;";
			break;
		case "ô":
			$result .= "&ocirc;";
			break;
		case "õ":
			$result .= "&otilde;";
			break;
		case "Ó":
			$result .= "&Oacute;";
			break;
		case "Ò":
			$result .= "&Ograve;";
			break;
		case "Ô":
			$result .= "&Ocirc;";
			break;
		case "Õ":
			$result .= "&Otilde";
			break;
		case "ñ":
			$result .= "&ntilde;";
			break;
		case "ý":
			$result .= "&yacute;";
			break;
		case "Ç":
			$result .= "&Ccedil;";
			break;
		case "ç":
			$result .= "&ccedil;";
			break;
		case "Ý":
			$result .= "&Yacute;";
			break;
		default:
			$result .= $text [
			$ico];
		}
	}
	return($result);
}

/**
 * Ersetzt Umlaute durch ASCII-Umschreibung (ä => ae) und normalisiert
 * die Unicode-Kodierung.
 *
 * @param string $str Eingabe (UTF-8)
 * @return string Ergebnis ohne Umlaute
 */
function replace_umlaute($str)
{
	# Umlaute / Sonderfälle für Diakritika
	$str = strtr($str, array(
		'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
		'Ä' => 'Ae', 'Ö' => 'oe', 'Ü' => 'Ue',
		'ñ' => 'ny', 'ÿ' => 'yu',
		'Ñ' => 'Ny', 'Ÿ' => 'Yu',
		'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ĩ' => 'i', 'ı' => 'i',
		'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ĩ' => 'I', 'İ' => 'I',
		'Đ'=>'Dj', 'đ'=>'dj', 'Ø'=>'O', 'Þ'=>'B',
		'Æ'=>'AE', 'Œ'=>'OE', 'æ'=>'ae', 'œ'=>'oe'));
	if (class_exists('Normalizer')) {
		// Mit Unicode-Normalisierung Buchstaben von Diakritika trennen
		$str = Normalizer::normalize($str, Normalizer::NFD);
		// Alle übrig gebliebenen Diakritika entfernen
		$str = preg_replace('/\pM/u' , '', $str);
		// Zurücknormalisieren, dabei mit NFKC auch Sonderformen von Zeichen in
		// Grundform umwandeln (z.B. ² => 2)
		$str = Normalizer::normalize($str, Normalizer::NFKC);
	}
	else {
		$str = strtr($str, array(
			'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
			'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
			'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
			'ç' => 'c', 'Ç' => 'C',
			'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
			'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
			'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
			'Ă' => 'A', 'Ș' => 'S', 'Ş' => 'S', 'Ț' => 'T', 'Ţ' => 'T',
			'ă' => 'a', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
			'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Ã'=>'A', 'Æ'=>'AE', 'Œ'=>'OE',
			'Ø'=>'O', 'Þ'=>'B', 'ÿ'=>'yu', 'æ'=>'ae', 'ñ'=>'ny', 'œ'=>'OE',
			'Ñ'=>'Ny'));
	}
	return $str;
}

/**
 * Entfernt Umbrüche und NUL-Bytes, und ersetzt dann alle nicht
 * nicht-alphanummerischen Zeichen durch $replacement.
 *
 * Sinnvoll zum Vorbereiten von Dateinamen und URLs.
 *
 * @param string $str Eingabe (UTF-8)
 * @param string $replacement Ersetzung für nicht-erlaubte Zeichen
 * @param string $allow Erlaubte Zeichen für Ausgabe (Prüfung in Regex)
 * @return string Bereinigtes Ergebnis
 */
function make_ascii_safe_string($str, $replacement='_', $allow='a-zA-Z0-9_-')
{
	# Zeilenumbrüche
	$str = str_replace("\n", "", $str);
	$str = str_replace("\r", "", $str);
	# Null-Bytes
	$str = str_replace("\0", "", $str);
	# Sonstiges
	$str = preg_replace('/[^'.str_replace('/', '\/', $allow).']/u', $replacement, $str);
	return $str;
}

/**
 * Wandelt "memory_limit"-Speicherangaben in Bytes um.
 * @param $val shorthand-Speicherangabe, wie z. B. 128M, 1G oder 1024K.
 * @return int Gibt den Wert von $val in Bytes umgerechnet zurueck.
 */
function return_bytes($val)
{
	if (preg_match("/^(?<value>[1-9][0-9]*)(?<unit>[a-z]+)$/i", trim($val), $match) == 1) {
		switch(strtolower(substr($match["unit"], 0, 1))) {
		case 'k':
			$val = $match["value"] * 1024;
			break;
		case 'm':
			$val = $match["value"] * pow(1024, 2);
			break;
		case 'g':
			$val = $match["value"] * pow(1024, 3);
			break;
		default:
			break;
		}
	}
	return (int)$val;
}

/**
 * @return bool|int Gibt die Erschoepfung des Speichers in Bytes zurueck, falls es der Fall ist, ansonsten false.
 */
function memory_exhausted()
{
	$mem = return_bytes(ini_get("memory_limit")) - memory_get_usage();
	return ($mem < 1) ? ($mem * 1048576) : false;
}

/**
 * Setzt das memory_limit neu, sofern der aktuelle Wert geringer als $limit ist.
 * @param string $limit Speicherbedarf, der mindestens gedeckt werden soll.
 */
function set_minimum_memory_limit($limit = "128M")
{
	if (return_bytes(ini_get("memory_limit")) < return_bytes($limit)) {
		ini_set("memory_limit", $limit);
	}
}

if (defined("MINIMUM_MEMORY_REQUIREMENT")) {
	set_minimum_memory_limit(MINIMUM_MEMORY_REQUIREMENT);
}
else {
	set_minimum_memory_limit();
}

/**
 * Diese Funktion lädt stylespezifische Spracheinträge aus dem Ordner styles/{}/messages/.
 *
 * @date 2018-10-16
 * @author Christoph Zimmer <cz@papoo.de>
 */
function loadStyleSpecificFrontendMessages()
{
	$fallback = ["en", "de"];
	$languages = array_unique(array_merge([$GLOBALS["cms"]->lang_short], $fallback));
	$messagesPath = rtrim(PAPOO_ABS_PFAD, "/")."/styles/{$GLOBALS["cms"]->style_dir}/messages/";

	foreach ($languages as $language) {
		$filename = "{$messagesPath}messages_frontend_{$language}.inc.php";
		if (is_file($filename)) {
			require_once $filename;
			break;
		}
	}
}
