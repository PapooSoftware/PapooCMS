<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.iframe.php
 * Type:     function
 * Name:     iframe
 * Purpose:  displays a page within an iframe
 * -------------------------------------------------------------
 */

function smarty_function_iframe($params, &$smarty)
{
	if (!count($params))
	{
		$smarty->trigger_error("iframe: missing parameters name, src, width, height", E_USER_WARNING);
		$return = "";
	}
	elseif (count($params) > 4)
	{
		$smarty->trigger_error("iframe: too few parameters", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['src']) OR $params['src'] == "")
	{
		$smarty->trigger_error("iframe: missing parameter 'src'", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['name']) OR $params['name'] == "")
	{
		$smarty->trigger_error("iframe: missing parameter 'name'", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['width']))
	{
		$smarty->trigger_error("iframe: missing parameter 'width'", E_USER_WARNING);
		$return = "";
	}
	elseif (!isset($params['height']))
	{
		$smarty->trigger_error("iframe: missing parameter 'height'", E_USER_WARNING);
		$return = "";
	}
	elseif (!ctype_digit((string)($params['width'])))
	{
		$smarty->trigger_error("iframe: parameter 'width' not numeric", E_USER_WARNING);
		$return = "";
	}
	elseif (!ctype_digit((string)($params['height'])))
	{
		$smarty->trigger_error("iframe: parameter 'height' not numeric", E_USER_WARNING);
		$return = "";
	}
	else
	{
		$return = '<iframe scrolling="auto" frameborder="0" name="' . $params['name'];
		$return .= '" width="' . $params['width'] . '" height="' . $params['height'] . '" src="' . $params['src'] . '"><p><strong>Ihr Browser kann leider keine eingebetteten Frames anzeigen</strong></p></iframe>' . chr(13);
	}
	return $return;
}
?>
