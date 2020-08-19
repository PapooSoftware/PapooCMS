<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

$template = "papoo_css.html";
// benötigte Dateien einbauen
require_once "./all_inc.php";
// Inhalt der aktuellen Standard CSS Datei einlesen
$inhalt = implode("", file("../" . $content->template['standard_style']));
// MimeType CSS setzen auskommentiert da der IE das nicht rafft
// header("content-type: text/css");
$inhalt = str_replace("../../", "/", $inhalt);
// Inhalt ausgeben
echo $inhalt;
// Hintergrund auf weiß und Schrift auf schwarz setzen
echo "body {" . "background:#fff;" . "color:#000;" . "}" . ".artikel {background:fff;}";
