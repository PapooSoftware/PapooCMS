<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.reporeid.php
 * Type:     function
 * Name:     reporeid
 * Purpose:  displays the reporeid of the actual page
 * -------------------------------------------------------------
 */

function smarty_function_reporeid($params, &$smarty)
{
	$table_data = $smarty->get_template_vars(table_data);
	return $reporeid = $table_data[0]['reporeid'];
}
?>
