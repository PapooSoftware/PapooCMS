<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_breadcrumb.php
 * Type:     function
 * Name:     mod_breadcrumb
 * Purpose:  displays the breadcrumb
 * -------------------------------------------------------------
 */

function smarty_function_mod_breadcrumb($params, &$smarty)
{
	$module_aktiv = $smarty->get_template_vars(module_aktiv);
	if ($module_aktiv['mod_breadcrump']) // if the module 'breadcrumb' is activated
	{
		$menu_data_breadcrump = $smarty->get_template_vars(menu_data_breadcrump);
		if (count($menu_data_breadcrump)) // if any breadcrumb items are present
		{
			$slash = $smarty->get_template_vars(slash);
			$frag_connect = $smarty->get_template_vars(frag_connect);
			$gleich_connect = $smarty->get_template_vars(gleich_connect);
			$link = $smarty->get_template_vars(link);
			// start html
			$return = '<div class="modul" id="mod_breadcrump">' . chr(13);
			// text 'you are here:'
			$return .= '<span class="breadtext">' . $smarty->get_template_vars(message_2183) . ': </span>' . chr(13);
			// link tag
			$return .= '<a class="breadlink1" href="' . $slash . '" title="' . $smarty->get_template_vars(message_2000) . $link . '">';
			// link text
			$return .= $link;
			// end link tag
			$return .= '</a>' . chr(13);
			
			foreach ($menu_data_breadcrump as $menu)
			{
				if ($menu['menuid'] > 1) // not first page
				{
					if ($smarty->get_template_vars(sp_urls) == "ok") // pretty urls activated?
					{
						if (!$menu['extern'] && !$menu['extern_bread']) // external link
						{
							$return .= '<span class="breadslash"> / </span>' . chr(13);
							// link tag
							$rezurn .= '<a href="' . $slash . $menu['menuname_url'] . '" class="breadlink"';
							$return .= 'id="breadcrump_' . $menu['htmltag_id'] . '" title="' . $menu['lang_title'] . '">';
							
							$return .= '<dfn class="insert"></dfn>';
							$return .= '<dfn>' . $menu['nummer'] . ': </dfn>';
							// link text
							$return .= $menu['menuname'];
							$return .= '</a>' . chr(13);
						}
						else
						{
							if (!$menu['extern']) // internal
							{
								$return .= '<span class="breadslash"> / </span>' . chr(13);
								$return .= '<dfn class="insert"></dfn>' . chr(13);
								$return .= '<dfn>' . $menu['nummer'] . ': </dfn>' . chr(13);
								// menu text
								$return .= $menu['menuname'];
								// end link tag ??
								#$return .='</a>';
							}
							if (!$menu['extern_bread']) // internal link
							{
								$return .= '<span class="breadslash"> / </span>' . chr(13);
								// link tag
								$return .= '<a href="' . $menu['menulink'] . '" class="breadlink" ';
								$return .= 'id="breadcrump_' . $menu['htmltag_id'];
								$return .= '" title="' . $menu['lang_title'] . '">';
								
								$return .= '<dfn class="insert"></dfn>';
								$return .= '<dfn>' . $menu['nummer'] . ': </dfn>';
								// link text
								$return .= $menu['menuname'];
								// end link tag
								$return .= '</a>' . chr(13);
							}
						}
					}
					else // first page
					{
						$return .= '<span class="breadslash"> / </span>' . chr(13);
						if (!$menu['link_aktiv'] AND !1) // "AND NOT 1" deaktiviert die Deaktivierung des aktuellen Links *}
						{
							$return .= '<strong>';
							$return .= '<span class="breadlink_span" id="breadcrumpsp_' . $menu['htmltag_id'] . '">';
							// menu text
							$return .= $menu['menuname'];
							$return .= '</span>';
							$return .= '</strong>' . chr(13);
						}
						else
						{
							// link tag
							$return .= '<a class="breadlink"';
							$return .= ' href="' . $slash . $menu['menulink'] . $frag_connect . 'menuid' . $gleich_connect;
							$return .= $menu['menuid'] . $menu['template'] . '" id="breadcrump_' . $menu['htmltag_id'] . '" ';
							$return .= 'title="' . $menu['lang_title'] . '">';
							// link text
							$return .= $menu['menuname'];
							// end link tag
							$return .= '</a>' . chr(13);
						}
					}
				}
			}
			$return .= '</div>' . chr(13);
			return $return;
		}
	}
}
?>
