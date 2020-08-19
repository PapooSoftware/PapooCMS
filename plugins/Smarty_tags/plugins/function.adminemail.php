<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.adminemail.php
 * Type:     function
 * Name:     adminemail
 * Purpose:  displays the admin email address
 * -------------------------------------------------------------
 */

function smarty_function_adminemail($params, &$smarty)
{
	global $db;
	global $cms;
	$sql = sprintf("SELECT admin_email FROM %s",
			$cms->papoo_daten
			);
	$adminemail = $db->get_var($sql);
	return $adminemail;
}
?>
