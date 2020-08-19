<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_menue_top.php
 * Type:     function
 * Name:     mod_menue_top
 * Purpose:  displays the top menu
 * -------------------------------------------------------------
 */

function smarty_function_mod_menue_top($params, &$smarty)
{
	$module_aktiv = $smarty->get_template_vars(module_aktiv);
	if ($module_aktiv['mod_menue_top']) // if the module 'top menu' is activated
	{
		$mod_menutop = $smarty->get_template_vars(menutop); // top menu array
		if (count($mod_menutop)) // if any items of the top menu are present
		{
			$return = '<div class="modul" id="mod_menue_top">' . chr(13);
			$return .= '<ul class="topul">' . chr(13);
			$nl = $smarty->get_template_vars(nl);
			$i = 0;
			foreach ($mod_menutop AS $menutop)
			{
				$i++;
				$return .= $nl;
				$return .= '<li>' . chr(13);
				$return .= '<a ' . $menutop['mtlang_class'] . ' href="';
				if ($menutop['mtlang_link']) $return .= $menutop['mtlang_link'];
				else $return .= $menutop['menutop_link'];
				$return .= '" title="' . htmlspecialchars($menutop['mtlang_title'], ENT_QUOTES, 'UTF-8') . '" ';
				$return .= 'id="mod_menue_top_link_' . $menutop['menutop_id'] . '"';
				if ($menutop['menutop_extern']) $return .= 'target="_blank"';
				$return .= '>';
				$return .= '<span class="ignore">' . $i . '.: </span>';
				$return .= htmlspecialchars($menutop['mtlang_name'], ENT_QUOTES, 'UTF-8');
				$return .= '<span class="ignore">.</span>';
				$return .= '</a>' . chr(13);
				if (count($mod_menutop) != $i) $return .= '<span class="mod_menue_top_separator">|</span>';
				$return .= $nl;
				$return .= '</li>' . chr(13);
			}
			$return .= $nl;
			$return .= '</ul>' . chr(13);
			$return .= '</div>' . chr(13);
			return $return;
		}
	}
}
?>
