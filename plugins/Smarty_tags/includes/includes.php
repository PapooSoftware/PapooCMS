<?php
#$smarty->load_filter("pre", "form");
#$smarty->force_compile = true;
// concat the 2nd plugins dir
$smarty->plugins_dir = array(PAPOO_ABS_PFAD .'/lib/smarty/plugins', PAPOO_ABS_PFAD .'/plugins/Smarty_tags/plugins');
require_once(PAPOO_ABS_PFAD."/plugins/Smarty_tags/lib/Smarty_tags_class.php");
$Smarty_tags->Smarty_tags_listtags_user();
// exec and register user plugins if any exist
if (count($content->template['smartytags_user_plugins'])) {
	foreach ($content->template['smartytags_user_plugins'] AS $key =>$value) {
		$func_name = "function_" . $value['user_plugin_name'] . "_user_plugin";
		// execute and if no errors: register the user plugin 
		if (!(@eval('function ' . $func_name . '($params, &$smarty) {' . $content->template['smartytags_user_plugins'][$key]['php_code'] . '}') === FALSE)) {
			if ($content->template['smartytags_user_plugins'][$key]['tag_active'] == 'j') $smarty->register_function($value['user_plugin_name'], $func_name, false);
		}
	}
}
