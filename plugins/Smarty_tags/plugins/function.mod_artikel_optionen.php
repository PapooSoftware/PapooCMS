<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.mod_artikel_optionen.php
 * Type:     function
 * Name:     mod_artikel_optionen
 * Purpose:  displays links for "tell a friend", print, edit
 * -------------------------------------------------------------
 */

function smarty_function_mod_artikel_optionen($params, &$smarty)
{		
	$anzeig_versende = $smarty->get_template_vars(anzeig_versende);
	$reporeid_print = $smarty->get_template_vars(reporeid_print);
	$anzeig_drucken = $smarty->get_template_vars(anzeig_drucken);
	$urldatself = $smarty->get_template_vars(urldatself);
	$plugin = $smarty->get_template_vars(plugin);
	$editlink = $smarty->get_template_vars(editlink);

    if(!isset($plugin['mehrstufige_freigabe'])) {
        $plugin['mehrstufige_freigabe'] = NULL;
    }

	if (($anzeig_versende AND $reporeid_print)
		OR ($anzeig_drucken AND $urldatself)
		OR ($plugin['mehrstufige_freigabe']['handbuchdruck_aktiv'])
		OR ($editlink))
	{
		$return = '<div class="modul" id="mod_artikel_optionen">' . chr(13);
		// Optionen zu diesem Artikel:
		// $return .= '<h5>{$message_2014}</h5>';
		$return .= '<span class="ignore">.</span>' . chr(13);
		$return .= '<ul class="option">' . chr(13);
		$return .= '<li class="ignore"></li>' . chr(13);
		
		$slash = $smarty->get_template_vars(slash);
		$menuid_aktuell = $smarty->get_template_vars(menuid_aktuell);
		if ($anzeig_versende AND $reporeid_print)
		{
			$return .= '<li>';
			// Sie k�nnen diese Seite versenden/empfehlen
			$return .= '<a rel="nofollow" href="' . $slash . 'index.php?menuid=' . $menuid_aktuell . '&amp;reporeid_send=1&amp;';
			$reporeid_print = $smarty->get_template_vars(reporeid_print);
			$message_2015 = $smarty->get_template_vars(message_2015);
			$return .= 'reporeid_print=' . $reporeid_print . '" title="' . $message_2015 . '">';
			$return .= $message_2015;
			$return .= '</a>' . chr(13);
			$return .= '<span class="ignore">.</span>' . chr(13);
			$return .= '</li>' . chr(13);
		}
		
		if ($anzeig_drucken AND $urldatself)
		{
			$return .= '<li>' . chr(13);
			// Druckversion dieses Artikels
			$urldatprint = $smarty->get_template_vars(urldatprint);
			$message_2016 = $smarty->get_template_vars(message_2016);
			$return .= '<a rel="nofollow" href="' . $slash . $urldatself . '?' . $urldatprint . 'print=ok" title="' . $message_2016;
			$return .= '">';
			$return .= $message_2016;
			$return .= '</a>' . chr(13);
			$return .= '<span class="ignore">.</span>' . chr(13);
			$return .= '</li>' . chr(13);
		}
		
		if ($plugin['mehrstufige_freigabe']['handbuchdruck_aktiv'])
		{
			$return .= '<li>' . chr(13);
			$return .= '<a rel="nofollow" href="' . $slash . 'plugin.php?menuid=' . $menuid_aktuell;
			$return .= '&amp;template=mehrstufige_freigabe/templates/front_handbuchdruck.html&amp;print=ok" title=".. Handbuchdruck">';
			$return .= '.. Handbuchdruck';
			$return .= '</a>' . chr(13);
			$return .= '<span class="ignore">.</span>' . chr(13);
			$return .= '</li>' . chr(13);
		}
	
		if ($editlink)
		{
			$return .= '<li>' . chr(13);
			$message_2195 = $smarty->get_template_vars(message_2195);
			$return .= '<a rel="nofollow" href="' . $editlink . '" title="' . $message_2195 . '">';
			$return .= $message_2195;
			$return .= '</a>' . chr(13);
			$return .= '<span class="ignore">.</span>' . chr(13);
			$return .= '</li>' . chr(13);
		}
		$return .= '</ul>' . chr(13);
		$return .= '</div>' . chr(13);

        if(!isset($plugin['mehrstufige_freigabe'])) {
            $plugin['mehrstufige_freigabe'] = NULL;
        }

		return $return;
	}
}
?>
