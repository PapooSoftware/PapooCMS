<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.upload_url.php
 * Type:     function
 * address:  upload_url
 * Purpose:  displays the upload url of the given type
 * -------------------------------------------------------------
 */

function smarty_function_upload_url($params, &$smarty)
{
	global $image_core;
	global $intern_upload;

	if (!count($params))
	{
		$smarty->trigger_error("upload_url: missing parameter", E_USER_WARNING);
		$return = "";
	}
	elseif (count($params) > 1)
	{
		$smarty->trigger_error("upload_url: too few parameters", E_USER_WARNING);
		$return = "";
	}
	else
	{
		if ($params['images']) $return = $image_core->pfad_images;
		elseif ($params['thumbs']) $return = $image_core->pfad_thumbs;
		elseif ($params['files']) $return = PAPOO_ABS_PFAD . '/dokumente/upload';
		elseif ($params['videos']) $return = PAPOO_ABS_PFAD."/video/";
		else
		{
			$smarty->trigger_error("upload_url: unknown parameter", E_USER_WARNING);
			$return = "";
		}
	}
	return $return;
}
?>
