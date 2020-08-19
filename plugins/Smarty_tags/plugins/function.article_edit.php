<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.article_edit.php
 * Type:     function
 * Name:     article_edit
 * Purpose:  displays a link for "edit"
 * -------------------------------------------------------------
 */

function smarty_function_article_edit($params, &$smarty)
{		
	$editlink = $smarty->get_template_vars(editlink);
	if ($editlink)
	{
		$message_2195 = $smarty->get_template_vars(message_2195);
		$return = '<a rel="nofollow" href="' . $editlink . '" title="' . $message_2195 . '" target="_blank">';
		$return .= $message_2195;
		$return .= '</a>';
		return $return;
	}
}
?>
