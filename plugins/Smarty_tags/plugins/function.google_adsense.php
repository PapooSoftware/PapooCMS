<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.google_adsense.php
 * Type:     function
 * Name:     google_adsense
 * Purpose:  inserts Google Ads code
 * -------------------------------------------------------------
 */

function smarty_function_google_adsense($params, &$smarty)
{
	if (($params['client']) == "")
	{
		$smarty->trigger_error("google_adsense: missing parameter 'client'", E_USER_WARNING);
		$fehler = 1;
	}
	if (($params['width']) == "")
	{
		$smarty->trigger_error("google_adsense: missing parameter 'width'", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (!ctype_digit((string)($params['width'])))
	{
		$smarty->trigger_error("google_adsense: parameter 'width' not numeric", E_USER_WARNING);
		$fehler = 1;
	}
	if (($params['height']) == "")
	{
		$smarty->trigger_error("google_adsense: missing parameter 'height'", E_USER_WARNING);
		$fehler = 1;
	}
	elseif (!ctype_digit((string)($params['height'])))
	{
		$smarty->trigger_error("google_adsense: parameter 'height' not numeric", E_USER_WARNING);
		$fehler = 1;
	}
	if (($params['format'] == ""))
	{
		$smarty->trigger_error("google_adsense: missing parameter 'format'", E_USER_WARNING);
		$fehler = 1;
	}
	if ($params['type'] == "")
	{
		$smarty->trigger_error("google_adsense: missing parameter 'type'", E_USER_WARNING);
		$fehler = 1;
	}
	if (!$fehler)
	{
		$return = '<script type="text/javascript">' . chr(13);
		$return .= '<!-- ' . chr(13);;
		$return .= 'google_ad_client = "' . $params['client'] . '";';
		
		$return .= 'google_ad_width = "' . $params['width'] . '";';
		$return .= 'google_ad_height = "' . $params['height'] . '";';
		$return .= 'google_ad_format = "' . $params['format'] . '";';
		$return .= 'google_ad_type = "' . $params['type'] . '";';
		if (isset($params['alternate_color'])) $return .= 'google_alternate_color = "' . $params['alternate_color'] . '";';
		if (isset($params['channel'])) $return .= 'google_ad_channel = "' . $params['channel'] . '";';
		if (isset($params['color_border'])) $return .= 'google_color_border = "' . $params['color_border'] . '";';
		if (isset($params['color_bg'])) $return .= 'google_color_bg = "' . $params['color_bg'] . '";';
		if (isset($params['color_link'])) $return .= 'google_color_link = "' . $params['color_link'] . '";';
		if (isset($params['color_url'])) $return .= 'google_color_url = "' . $params['color_url'] . '";';
		if (isset($params['color_text'])) $return .= 'google_color_text = "' . $params['color_text'] . '";';
		if (isset($params['any'])) $return .= $params['any'] . ';';
		$return .= '//--></script>' . chr(13);
		$return .= '<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . chr(13);
		return $return;
	}
}
?>
