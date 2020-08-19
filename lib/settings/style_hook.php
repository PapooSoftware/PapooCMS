<?php
//Spezielle Stylehooks initialisieren
$temp_style_dir = "";
if (!empty($cms->style_dir)) {
	$smarty->compile_id = $cms->style_dir;
	$temp_style_dir = "/styles/".$cms->style_dir."/templates/";

	/**
	 * Falls eine stylespezifische PHP-Klasse existiert, diese einbinden und Objekt erzeugen
	 */
	$style_hooks_class = PAPOO_ABS_PFAD . "/styles/" . $cms->style_dir . "/lib/style_hooks.php";
	if (file_exists($style_hooks_class)) {
		require_once($style_hooks_class);
		$style_hooks = new style_hooks;
	}
}

/**
 * Führt eine style-spezifische Methode aus, falls diese vorhanden ist
 *
 * @param $hook_method
 * @param array $args
 */
function run_style_hook($hook_method, $args = array())
{
	global $style_hooks;
	if (is_callable(array($style_hooks, $hook_method))) {
		call_user_func_array(array($style_hooks, $hook_method), $args);
	}
}
