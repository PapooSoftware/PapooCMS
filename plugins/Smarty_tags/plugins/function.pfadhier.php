<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.pfadhier.php
 * Type:     function
 * address:  pfadhier
 * Purpose:  displays the Papoo variable $pfadhier
 * -------------------------------------------------------------
 */

function smarty_function_pfadhier($params, &$smarty)
{
	return $smarty->get_template_vars(pfadhier);
}
?>
