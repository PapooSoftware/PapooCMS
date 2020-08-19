<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_efa_fontsize.php
 * Type:     function
 * Name:     mod_efa_fontsize
 * Purpose:  displays the efa font size selectors
 * -------------------------------------------------------------
 */

function smarty_function_mod_efa_fontsize($params, &$smarty)
{
	$module_aktiv = $smarty->get_template_vars(module_aktiv);
	if ($module_aktiv['mod_efa_fontsize'])
	{
		$return = '<div class="modul" id="mod_efa_fontsize">' . chr(13);
		$mod_efa_fontsize = $smarty->get_template_vars(mod_efa_fontsize);
		$return .= '<span class="mod_efa_fontsize_text">' . $mod_efa_fontsize['text'] . '</span>' . chr(13);
		$return .= '<script type="text/javascript">' . chr(13);
		$return .= 'if (efa_fontSize)' . chr(13);
		$return .= '{' . chr(13);
		$return .= 'efa_bigger[2] = "' . $mod_efa_fontsize['bigger'] . '";' . chr(13);
		$return .= 'efa_reset[2] = "' . $mod_efa_fontsize['normal'] . '";' . chr(13);
		$return .= 'efa_smaller[2] = "' . $mod_efa_fontsize['smaller'] . '";' . chr(13);
		$return .= 'var efa_fontSize_lokalisiert = new ';
		$return .= 'Efa_Fontsize(efa_increment,efa_bigger,efa_reset,efa_smaller,efa_default);' . chr(13);
		$return .= 'document.write(efa_fontSize_lokalisiert.allLinks);' . chr(13);
		$return .= '}' . chr(13);
		$return .= '</script>' . chr(13);
		$return .= '</div>' . chr(13);
		return $return;
	}
}
?>