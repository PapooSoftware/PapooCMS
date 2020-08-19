<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.link.php
 * Type:     function
 * address:  link
 * Purpose:  displays the Papoo variable $link
 * -------------------------------------------------------------
 */

function smarty_function_link($params, &$smarty)
{
	return $smarty->get_template_vars(link);
}
?>
