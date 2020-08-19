<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.redirect.php
 * Type:     function
 * Name:     redirect
 * Purpose:  redirects to a given URL
 * -------------------------------------------------------------
 */

function smarty_function_redirect($params, &$smarty)
{
	if (isset($params['url']))
	{
		$location_url = $params['url'];
		if ( $_SESSION['debug_stopallredirect'] )
		echo '<a href="' . $location_url . '">Weiter</a>';
		else header( "Location: $location_url" );
		exit;
	}
}
?>
