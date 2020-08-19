<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.tell_a_friend.php
 * Type:     function
 * Name:     tell_a_friend
 * Purpose:  displays the link for "tell a friend"
 * -------------------------------------------------------------
 */

function smarty_function_tell_a_friend($params, &$smarty)
{
	$anzeig_versende = $smarty->get_template_vars(anzeig_versende);
	$reporeid_print = $smarty->get_template_vars(reporeid_print);
	$slash = $smarty->get_template_vars(slash);
	$menuid_aktuell = $smarty->get_template_vars(menuid_aktuell);
	if ($anzeig_versende AND $reporeid_print)
	{
		// Sie können diese Seite versenden/empfehlen
		$return = '<a rel="nofollow" href="' . $slash . 'index.php?menuid=' . $menuid_aktuell . '&amp;reporeid_send=1&amp;';
		$reporeid_print = $smarty->get_template_vars(reporeid_print);
		$message_2015 = $smarty->get_template_vars(message_2015);
		$return .= 'reporeid_print=' . $reporeid_print . '" title="' . $message_2015 . '">';
		$return .= $message_2015;
		$return .= '</a>';
		return $return;
	}
}
?>
