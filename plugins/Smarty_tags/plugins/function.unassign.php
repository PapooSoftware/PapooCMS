<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.unassign.php
 * Type:     function
 * Name:     unassign
 * Purpose:  unassign smarty variable, array
 * -------------------------------------------------------------
 */

function smarty_function_unassign($params, &$smarty) {
    #$params = $smarty->_parse_attrs($params);
    if (!isset($params['var'])) {
        $smarty->trigger_error("unassign: missing 'var' parameter", E_USER_WARNING);
    }
	$smarty->clear_assign($params['var']);
}
?>