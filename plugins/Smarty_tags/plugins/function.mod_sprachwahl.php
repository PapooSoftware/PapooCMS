<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_sprachwahl.php
 * Type:     function
 * Name:     mod_sprachwahl
 * Purpose:  displays the language selector
 * -------------------------------------------------------------
 */

function smarty_function_mod_sprachwahl($params, &$smarty)
{
	$module_aktiv = $smarty->get_template_vars(module_aktiv);
	if ($module_aktiv['mod_sprachwahl']) // if the module 'language select' is activated
	{
		$languageget = $smarty->get_template_vars(languageget); // language array
		if (count($languageget)) // if any languages are selected
		{
			// start html
			$return = '<div class="modul" id="mod_sprachwahl">' . chr(13);
			$return .= '<ul>' . chr(13);
			
			$aktulanglong = $smarty->get_template_vars(aktulanglong);
			$slash = $smarty->get_template_vars(slash);
			
			foreach ($languageget as $lang)
			{
				$return .= '<li class="languageli">' . chr(13);
				// link tag
				$return .= '<a class="toplink" href="' . $lang['lang_link'] . '"';
				if ($aktulanglong == $lang['language']) $return .= ' id="aktulang"';
				$return .= ' title="' . $lang['language'] . '">';
				// img tag
				$return .= '<img src="' . $slash . 'bilder/' . $lang['lang_bild'] . '" width="20" height="14" alt="';
				if ($lang['lang_title'] == "")
					$return .= $lang['language'];
				else
					$return .= $lang['lang_title'];
				$return .= '" /> ';
				// link text
				$return .= $lang['language'];
				// tails
				$return .= '</a>' . chr(13);
				$return .= '<span class="ignore">.</span>' . chr(13);
				$return .= '</li>' . chr(13);
			}
			$return .= '</ul>' . chr(13);
			$return .= '</div>' . chr(13);
			return $return;
		}
	}
}
?>
