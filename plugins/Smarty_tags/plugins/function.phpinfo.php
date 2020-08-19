<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.phpinfo.php
 * Type:     function
 * Name:     phpinfo
 * Purpose:  displays informations about php
 * -------------------------------------------------------------
 */

function smarty_function_phpinfo($params, &$smarty)
{
	ob_start () ;
	if (!empty($params['what']))
	{
		switch ($params['what'])
		{
			 case "INFO_GENERAL": $rc = phpinfo(1); break;
			 case "INFO_CREDITS": $rc = phpinfo(2); break;
			 case "INFO_CONFIGURATION": $rc = phpinfo(4); break;
			 case "INFO_MODULES": $rc = phpinfo(8); break;
			 case "INFO_ENVIRONMENT": $rc = phpinfo(16); break;
			 case "INFO_VARIABLES": $rc = phpinfo(32); break;
			 case "INFO_LICENSE": $rc = phpinfo(64); break;
			 case "INFO_ALL": $rc = phpinfo(-1); break;
		}
	}
	else $rc = phpinfo();
	$pinfo = ob_get_contents();
	ob_end_clean ();
	if (!$rc) echo "PHPINFO nicht erfolgreich. rc = " . $rc;
	else
	{
		if (!empty($params['popup']))
		{
			$pinfo = str_replace("\x0a", "", $pinfo);
			$pinfo = str_replace("'", "&#039;", $pinfo);
			echo "<SCRIPT language=javascript> if( self.name == '' ) var title = 'Console'; else var title = 'Console_' + self.name;";
			echo '_smarty_tags_phpinfo = window.open("",title.value,"width=980,height=600,resizable,scrollbars=yes");_smarty_tags_phpinfo.document.write("<HTML><HEAD><TITLE>Smarty Tags phpinfo ' . $params['what'] . '</TITLE></HEAD><BODY bgcolor=#ffffff>");';
			echo "_smarty_tags_phpinfo.document.write('" . $pinfo . "');";
			echo '_smarty_tags_phpinfo.document.write("</BODY></HTML>");_smarty_tags_phpinfo.document.close();</SCRIPT>';
		}
		else echo $pinfo;
	}
}
?>