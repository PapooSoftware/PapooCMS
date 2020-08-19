<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.content_shop.php
 * Type:     function
 * Name:     content_shop
 * Purpose:  displays the content of the Papoo shop
 * -------------------------------------------------------------
 */

function smarty_function_content_shop($params, &$smarty)
{
	global $user;
	global $db;
	global $cms;
	global $produkt_daten;
	global $arrkey;
	// Tabelle produkt_daten holen
	$produkt_daten = $smarty->get_template_vars(produkt_daten);
	if (!count($produkt_daten)) return "";
	foreach ($produkt_daten AS $key => $value) { $arrkey = $key; }
	$sql = sprintf("SELECT gruppenid
					FROM %s
					WHERE userid = '%s'",
			$cms->papoo_lookup_ug,
			$user->userid
			);
	$result = $db->get_var($sql);
	if ($result != 10) $smarty->assign("loggedin_and_not_jeder", "1");
	// callbacks ausführen, wenn nicht gecached. Eindeutige ID
	if (!$smarty->is_cached("tpl_content_shop:" . $produkt_daten[$arrkey][0]['produkt_id']))
	{
		// Ressourcen-Typ in Smarty registrieren
		$smarty->register_resource("tpl_content_shop",
				array("db_get_template",
						"db_get_timestamp",
						"db_get_secure",
						"db_get_trusted"));
	}
	// Template-Output speichern, dabei die callbacks ausführen und enthaltene Tags ausführen
	$myoutput = $smarty->fetch("tpl_content_shop:" . $produkt_daten[$arrkey][0]['produkt_id']);
	// produkt_daten Elemente komplett sichern
	$produkt_daten2 = $produkt_daten;
	// die im content enthaltenen tags sind durch den fetch mit callbacks ausgeführt worden
	$produkt_daten2[$arrkey][0]['daten'] = $myoutput;
	// zurückstellen array produkt_daten komplett. produkt_daten wird komplett überschrieben
	$smarty->assign("produkt_daten", $produkt_daten2);
	$return = "";
	return $return;
}

// callbacks
function db_get_template ($tpl_name, &$tpl_source, &$smarty_obj)
{
	global $produkt_daten;
	global $arrkey;
	$tpl_source = $produkt_daten[$arrkey][0]['daten']; // den aufzulösenden string (Tags-Auflösung) zurückgeben
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
