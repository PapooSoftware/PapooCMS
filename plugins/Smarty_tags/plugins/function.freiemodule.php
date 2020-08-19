<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.content.php
 * Type:     function
 * Name:     content
 * Purpose:  displays the content of the actual article
 * -------------------------------------------------------------
 */

function smarty_function_freiemodule($params, &$smarty)
{
	#ini_set('display_errors', 1);
	#error_reporting(E_ALL ^ E_NOTICE);
	global $freiemodule_code;
	// Variable freiemodule_result holen
	$freiemodule_result = $smarty->get_template_vars(freiemodule_result);
	$freiemodule_code = $freiemodule_result[$params['val']]['freiemodule_code'];
	$id = $freiemodule_result[$params['val']]['freiemodule_modulid'];
	// callbacks ausführen, wenn nicht gecached. Eindeutige ID
	if (!$smarty->is_cached("tpl_content_fm:" . $id))
	{
		// Ressourcen-Typ in Smarty registrieren
		$smarty->register_resource("tpl_content_fm",
				array("db_get_template_freiemodule",
						"db_get_timestamp_freiemodule",
						"db_get_secure_freiemodule",
						"db_get_trusted_freiemodule"));
	}
	// Template-Output speichern, dabei die callbacks ausführen und enthaltene Tags ausführen. Compile-Id wg. mehrfachem Aufrufs nötig
	$myoutput = $smarty->fetch("tpl_content_fm:" . $id, "", $params['val']);
	// freiemodule_result Elemente komplett sichern
	$freiemodule_result2 = $freiemodule_result;
	// die in freiemodule_code enthaltenen tags sind durch den fetch mit callbacks ausgeführt worden
	$freiemodule_result2[$params['val']]['freiemodule_code'] = $myoutput;
	// zurückstellen array freiemodule_result komplett. freiemodule_result wird komplett überschrieben
	$smarty->assign("freiemodule_result", $freiemodule_result2);
	#$smarty->unregister_resource("tpl_content_fm");
	$return = "";
}

// callbacks
function db_get_template_freiemodule($tpl_name, &$tpl_source, &$smarty_obj)
{
	global $freiemodule_code;
	$tpl_source = $freiemodule_code; // den aufzulösenden string (Tags-Auflösung) zurückgeben
	return true;
}

function db_get_timestamp_freiemodule($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
	$tpl_timestamp = time();
	return true;
}

function db_get_secure_freiemodule($tpl_name, &$smarty_obj)
{
	// Annahme: alle Templates sind sicher
	return true;
}

function db_get_trusted_freiemodule($tpl_name, &$smarty_obj)
{
	// wird nicht für Templates verwendet
}
?>
