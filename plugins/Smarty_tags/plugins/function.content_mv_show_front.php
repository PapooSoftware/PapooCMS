<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.content.php
 * Type:     function
 * Name:     content
 * Purpose:  displays the content for the template mv_show_front.html
 * -------------------------------------------------------------
 */

function smarty_function_content_mv_show_front($params, &$smarty)
{
	global $mv_template_all;

	// Tabelle table_data holen
	$mv_template_all = $smarty->get_template_vars(mv_template_all);
	$mv_content_id = $smarty->get_template_vars(mv_content_id);
	$mv_id_in_use = $smarty->get_template_vars(mv_id_in_use);
	
	// callbacks ausführen, wenn nicht gecached. Eindeutige ID
	if (!$smarty->is_cached("tpl_content:" . $mv_id_in_use . "_" . $mv_content_id))
	{
		// Ressourcen-Typ in Smarty registrieren
		$smarty->register_resource("tpl_content",
				array("db_get_template",
						"db_get_timestamp",
						"db_get_secure",
						"db_get_trusted"));
	}
	// Template-Output speichern, dabei die callbacks ausführen und enthaltene Tags ausführen
	$myoutput = $smarty->fetch("tpl_content:" . $mv_id_in_use . "_" . $mv_content_id);
	// Anzeigen, wenn der Parameter gesetzt ist, sonst table_data mofifizieren
	if (!($params['display']))
	{
		// tabla_data Elemente komplett sichern
		$mv_template_all2 = $mv_template_all;
		// die im content enthaltenen tags sind durch den fetch mit callbacks ausgeführt worden
		$mv_template_all2 = $myoutput;
		// zurückstellen array table_data komplett. table_data wird komplett überschrieben
		$smarty->assign("mv_template_all", $mv_template_all2);
		$return = "";
	}
	else $return = $myoutput;
	return $return;
}

// callbacks
function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
	global $mv_template_all;
	$tpl_source = $mv_template_all; // den aufzulösenden string (Tags-Auflösung) zurückgeben
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
