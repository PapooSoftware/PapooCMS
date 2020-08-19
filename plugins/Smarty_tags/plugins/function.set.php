<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.set.php
 * Type:     function
 * Name:     set
 * Purpose:  set a variable 
 * -------------------------------------------------------------
 */
 
function smarty_function_set($params, &$smarty)
{
	// if an array is to create
	if (substr($params['value'], 0, 6) == "array(") eval("\$params['value'] = " . str_replace('"', '', $params['value']) . ";");
	
	if (!isset($params['var']))
	{
		$smarty->trigger_error("set: missing 'var' parameter", E_USER_WARNING);
		return;
	}

	// Functions permitted in "if" parameter
	$functionsPermitted = array('empty', '!empty', 'is_null', '!is_null', 'isset', '!isset', 'is_void');
	if (!isset($params['value']))
	{
		$smarty->assign($params['var'], null); // clean setting
		return;
	}
	elseif (isset($params['if']))
	{ // Setting with "if" parameter
		if (in_array($params['if'], $functionsPermitted))
		{
			$var = $smarty->get_template_vars($params['var']); echo "aa=".$var."<br>";
			switch ($params['if'])
			{
				case "is_void":  if (empty($var) and ($var !== 0) and ($var !== '0'))
														$smarty->assign($params['var'], $params['value']); break;
				case "empty": if (empty($params['var'])) $smarty->assign($params['var'], $params['value']); break;
				case "!empty": if (!empty($params['var'])) $smarty->assign($params['var'], $params['value']); break;
				case "is_null": if (is_null($params['var'])) $smarty->assign($params['var'], $params['value']); break;
				case "!is_null": if (!is_null($params['var'])) $smarty->assign($params['var'], $params['value']); break;
				case "isset": if (isset($params['var'])) $smarty->assign($params['var'], $params['value']); break;
				case "!isset": if (!isset($params['var'])) $smarty->assign($params['var'], $params['value']); break;
			}
		}
		else
		{
			$smarty->trigger_error("set: 'if' parameter not valid", E_USER_WARNING);
		}
	}
	else
	{ // normal setting
		$smarty->assign($params['var'], $params['value']);
	}
}

?>
