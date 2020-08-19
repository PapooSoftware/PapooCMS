<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.get_template_vars.php
 * Type:     function
 * Name:     get_template_vars
 * Purpose:  displays template variables
 * -------------------------------------------------------------
 */

function smarty_function_get_template_vars($params, &$smarty)
{
	$templ_vars = $smarty->get_template_vars();
	ksort($templ_vars);
	foreach ($templ_vars as $key => $value)
	{
		if (empty($params['show']))
		{
	    	if (is_array($value)) echo "<tt><strong>" . $key . "</strong></tt> = Array (" . count($value) . ")<br />";
			elseif (is_object($value)) echo "<tt><strong>" . $key . "</strong></tt> = Object<br />";
		}
		else
		{
			if (is_array($value) OR is_object($value))
			{
				$result = print_r($value, true);
				$result = str_replace("\x20", "&nbsp;", $result);
				$result = str_replace("<","&lt;",$result);
				$result = str_replace(">","&gt;",$result);
				echo "<tt><strong>" . $key . "</strong> = " . str_replace("\x0a", "<br />", $result) . "</tt>";
			}
		}
		if (!is_array($value) AND !is_object($value)) echo "<tt><strong>" . $key . "</strong></tt> = " . trim($value) . "<br />";
	}
}
?>
