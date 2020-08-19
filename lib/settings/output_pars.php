<?php
//$output=preg_replace('/\> (\.|\|\,|\;|\!|\?|:)/','>$1',$output);

//Outputfilter die man noch Über die Ausgabe jagen kann initialisieren
$pluginintegrator->output_filter();

$diverse->log_zugriff();
// und tidy machen
if (empty($notidy)) {
	$output = $html->make_tidy_front($output);
}
#$download = new download_class();
$output=$download->replace_downloadlinks($output);
$output=$diverse->do_videos($output);
$output=$diverse->do_pfadeanpassen($output);
$output = $diverse->placeholders($output);
$output=preg_replace("/<p([^>]*)><\\/p>/","<p\$1>&nbsp;</p>",$output);
$output = str_replace("classid=\"", "classid=\"clsid:", $output);

run_style_hook('pre_output', array(&$output));
$diverse->fix_menu_one_links();

// & in links durch $amp; ersetzen
$find    = "/((href|src)=\"[^\"]*?)&(?!amp;)([^\"]*\")/";
$replace = "\\1&amp;\\3";
do {
	$len = strlen($output);
	$output = preg_replace($find, $replace, $output);
} while($len < strlen($output));
$output = str_replace("&amp;amp;", "&amp;", $output);
// pkalender-Einbindung Cache-Klasse
$cache->cache_speichern();