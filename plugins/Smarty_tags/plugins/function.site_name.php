<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.site_name.php
 * Type:     function
 * address:  site_name
 * Purpose:  displays the Papoo variable $site_name
 * -------------------------------------------------------------
 */

function smarty_function_site_name($params, &$smarty)
{
	return $smarty->get_template_vars(site_name);
}
?>
