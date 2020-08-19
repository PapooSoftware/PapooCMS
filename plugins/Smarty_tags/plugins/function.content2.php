<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.content2.php
 * Type:     function
 * Name:     content
 * Purpose:  displays the content of the actual article
 * -------------------------------------------------------------
 */

function smarty_function_content2($params, &$smarty)
{
	global $lan_article;

	// Variable lan_article holen
	$lan_article = $smarty->get_template_vars(lan_article);
	// callbacks ausführen, wenn nicht gecached. Eindeutige ID
	if (!$smarty->is_cached("tpl_content:" . $params['reporeid'] ))
	{
		// Ressourcen-Typ in Smarty registrieren
		$smarty->register_resource("tpl_content",
				array("db_get_template",
						"db_get_timestamp",
						"db_get_secure",
						"db_get_trusted"));
	}
	// Template-Output speichern, dabei die callbacks ausführen und enthaltene Tags ausführen
	$myoutput = $smarty->fetch("tpl_content:" . $params['reporeid'] );
	$smarty->assign("lan_article", $myoutput);
}

// callbacks
function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
	global $lan_article;
	$tpl_source = $lan_article; // den aufzulösenden string (Tags-Auflösung) zurückgeben
	return true;
}

function db_get_timestamp($tpl_name, &$tpl_timestamp, &$smarty_obj)
{
	$tpl_timestamp = time();
	return true;
}

function db_get_secure($tpl_name, &$smarty_obj)
{
	// Annahme: alle Templates sind sicher
	return true;
}

function db_get_trusted($tpl_name, &$smarty_obj)
{
	// wird nicht für Templates verwendet
}
?>
