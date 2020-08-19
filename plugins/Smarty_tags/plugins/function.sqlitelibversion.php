<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.sqlitelibversion.php
 * Type:     function
 * Name:     sqlitelibversion
 * Purpose:  displays the version of sqlitelib
 * -------------------------------------------------------------
 */

function smarty_function_sqlitelibversion($params, &$smarty)
{
	return sqlite_libversion();
}
?>
