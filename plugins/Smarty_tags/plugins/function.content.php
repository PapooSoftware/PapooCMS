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

function smarty_function_content($params, &$smarty)
{
	global $table_data;

	// Tabelle table_data holen
	$table_data = $smarty->get_template_vars(table_data);
	// callbacks ausführen, wenn nicht gecached. Eindeutige ID
	if (!$smarty->is_cached("tpl_content:" . $table_data[0]['reporeid']))
	{
		// Ressourcen-Typ in Smarty registrieren
		$smarty->register_resource("tpl_content",
				array("db_get_template",
						"db_get_timestamp",
						"db_get_secure",
						"db_get_trusted"));
	}
	// Template-Output speichern, dabei die callbacks ausführen und enthaltene Tags ausführen
	$myoutput = $smarty->fetch("tpl_content:" . $table_data[0]['reporeid']);
	// Anzeigen, wenn der Parameter gesetzt ist, sonst table_data mofifizieren
	if (!($params['display']))
	{
		// tabla_data Elemente komplett sichern
		$table_data2 = $table_data;
		// die im content enthaltenen tags sind durch den fetch mit callbacks ausgeführt worden
		$table_data2[0]['text'] = $myoutput;
		// zurückstellen array table_data komplett. table_data wird komplett überschrieben
		$smarty->assign("table_data", $table_data2);
		$return = "";
	}
	else $return = $myoutput;
	return $return;
}

// callbacks
function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
	global $table_data;
	$tpl_source = $table_data[0]['text']; // den aufzulösenden string (Tags-Auflösung) zurückgeben
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
