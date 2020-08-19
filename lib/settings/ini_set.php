<?php
//INI Settings...
ini_set('url_rewriter.tags', '');
ini_set("register_globals", 0);
ini_set("display_errors", 1);
ini_set("arg_separator.output", "&amp;");
ini_set("url_rewriter.tags", "");
ini_set("error_reporting", E_ALL & ~E_NOTICE);

//Sonstige Settings
date_default_timezone_set("Europe/Berlin");

//Konstanten
define("MINIMUM_MEMORY_REQUIREMENT", "128M");

//Server Vars
if(!isset($_SERVER['HTTP_REFERER'])) {
	$_SERVER['HTTP_REFERER'] = NULL;
}
