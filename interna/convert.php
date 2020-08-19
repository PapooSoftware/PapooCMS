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

$pfad = "..";
// Und eine Konstante setzen
define('admin', "admin");
// Alle benötigten Dateien einbinden
require_once "../lib/includes.inc.php";
// interne Plugin Klasse einbinden
require_once "../lib/classes/convertarticle_class.php";
global $convert;

$convert->convert_article();