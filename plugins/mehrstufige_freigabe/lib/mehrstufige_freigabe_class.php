<?php

/**
 * Class mehrstufige_freigabe_class
 */
class mehrstufige_freigabe_class
{
	/**
	 * mehrstufige_freigabe_class constructor.
	 */
	function __construct()
	{
		global $db, $db_praefix, $checked, $content, $user, $module;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->user = & $user;
		$this->module = & $module;

		$this->aktiv = true;

		$this->content->template['plugin']['mehrstufige_freigabe']['handbuchdruck_aktiv'] = true;

		$this->make_mehrstufige_freigabe();
	}

	function post_papoo()
	{
		if (!defined("admin")) {
			if(!isset($this->content->template['reporeid_print'])) {
				$this->content->template['reporeid_print'] = NULL;
			}

			$temp_aktu_repore_id = $this->content->template['reporeid_print'];

			$temp_informationen = $this->freigabe_data($temp_aktu_repore_id);

			if (!empty($temp_informationen)) {
				$this->content->template['plugin_mehrstufige_freigabe_informationen'] = $temp_informationen;
				$this->content->template['plugin_mehrstufige_freigabe_menu_top'] = $this->helper_menu_top_name($this->checked->menuid);
				$temp_menu_aktu = $this->helper_menu_data($this->checked->menuid);
				if ($temp_menu_aktu['untermenuzu']) {
					$this->content->template['plugin_mehrstufige_freigabe_menu_aktu'] = $temp_menu_aktu['nummer']." ".$temp_menu_aktu['menuname'];
				}
				else {
					$this->content->template['plugin_mehrstufige_freigabe_menu_aktu'] = "&nbsp;";
				}
			}
			else {
				$this->module->module_aktiv['mod_mehrstufige_freigabe_informationen'] = false;
			}
		}
	}

	function make_mehrstufige_freigabe()
	{
		global $template;

		if (defined("admin")) {
			if (strpos("XXX".$template, "mehrstufige_freigabe/templates/back.html")) {
				$this->user->check_access();
				$this->switch_back($this->checked->action);
			}

			if (strpos("XXX".$template, "mehrstufige_freigabe/templates/back_init.html")) {
				$this->user->check_access();
				$this->switch_back_init($this->checked->action);
			}
		}
		else {
			if (strpos("XXX".$template, "mehrstufige_freigabe/templates/front_handbuchdruck.html")) {
				$this->switch_front_handbuchdruck();
			}
		}
	}

	function switch_front_handbuchdruck()
	{
		// Liste der Men�punkte generieren
		$temp_menupunkte = array();
		$temp_menupunkte[] = $this->helper_menu_data($this->checked->menuid);

		$temp_sub_menupunkte = $this->helper_get_submenues($this->checked->menuid);
		if (!empty($temp_sub_menupunkte)) $temp_menupunkte = array_merge ($temp_menupunkte, $temp_sub_menupunkte);

		// Liste aller Artikel f�r alle oben gefundenen Men�punkte (inkl. Freigabe-Informationen) generieren
		$temp_artikelliste = array();
		global $artikel;
		global $download;
		global $diverse;

		if (!empty($temp_menupunkte)) {
			foreach ($temp_menupunkte as $temp_menupunkt) {
				$temp_artikel_dieses_menupunktes = $artikel->get_artikel($temp_menupunkt['menuid'], "ALL");

				if (!empty($temp_artikel_dieses_menupunktes)) {

					$temp_artikel_liste = array();
					foreach ($temp_artikel_dieses_menupunktes as $artikel_dieses_menupunktes) {
						if ($artikel_dieses_menupunktes->reporeID) {
							$temp_artikel = array();

							$temp_artikel['reporeid'] = $artikel_dieses_menupunktes->reporeID;

							if ($artikel_dieses_menupunktes->uberschrift_hyn == 1) {
								$temp_artikel['uberschrift'] = $artikel_dieses_menupunktes->header;
							}
							else {
								$temp_artikel['uberschrift'] = "";
							}

							// diverse "Korrekturen" ausf�hren (siehe Artikel-Klasse Funktion make_artikel()
							$temp_artikel['text'] = $artikel_dieses_menupunktes->lan_article;
							if ($artikel_dieses_menupunktes->teaser_atyn == 1) {
								// Teaser markieren
								if (empty($artikel_dieses_menupunktes->teaser_bild_html)) {
									$temp_artikel['text'] =
										"<div>".$artikel_dieses_menupunktes->lan_teaser_img_fertig."".$artikel_dieses_menupunktes->lan_teaser ."</div>".$temp_artikel['text'];
								}
								else {
									$temp_artikel['text'] =
										"<div>".$artikel_dieses_menupunktes->teaser_bild_html."".$artikel_dieses_menupunktes->lan_teaser."</div>".$temp_artikel['text'];
								}
							}
							$temp_artikel['text'] = $download->replace_downloadlinks($temp_artikel['text']);
							$temp_artikel['text'] = $diverse->do_pfadeanpassen($temp_artikel['text']);

							// Informationen der mehrstufigen Freigabe einf�gen
							$temp_artikel['freigabe'] = $this->freigabe_data($artikel_dieses_menupunktes->reporeID);

							$temp_artikel_liste[] = $temp_artikel;
						}
					}
					$temp_artikelliste[$temp_menupunkt['menuid']] = $temp_artikel_liste;
				}
			}
		}

		// die beiden obigen Listen zu einer Handbuch-Liste zusammenf�hren
		$temp_handbuch = array();
		if (!empty($temp_menupunkte)) {
			foreach ($temp_menupunkte as $temp_menupunkt) {
				$temp_eintrag = array();
				$temp_eintrag['menu'] = $temp_menupunkt;
				$temp_eintrag['artikel'] = $temp_artikelliste[$temp_menupunkt['menuid']];
				$temp_handbuch[$temp_menupunkt['menuid']] = $temp_eintrag;
			}
		}

		$this->content->template['plugin']['mehrstufige_freigabe']['handbuch'] = $temp_handbuch;

		$this->content->template['plugin_mehrstufige_freigabe_menu_top'] = $this->helper_menu_top_name($this->checked->menuid);
		$this->module->module_aktiv['mod_mehrstufige_freigabe_informationen'] = false;
	}

	/**
	 * @param $aktu_menuid
	 * @return array
	 */
	function helper_get_submenues($aktu_menuid)
	{
		$temp_ergebnis = array();
		global $menu;

		if (!empty($menu->data_front_complete)) {
			foreach($menu->data_front_complete as $menue) {
				if ($menue['untermenuzu'] == $aktu_menuid) {
					$temp_ergebnis[] = $menue;
					$temp_sub = $this->helper_get_submenues($menue['menuid']);
					if (!empty($temp_sub)) $temp_ergebnis = array_merge($temp_ergebnis, $temp_sub);
				}
			}
		}
		return $temp_ergebnis;
	}

	/**
	 * @return string
	 */
	function helper_menu_top_name()
	{
		$temp_return = "&nbsp;";

		global $menu;
		if (!empty($menu->data_front_breadcrump)) {
			foreach($menu->data_front_breadcrump as $menue) {
				if (!$menue['untermenuzu']) {
					$temp_return = $menue['menuname'];
				}
			}
		}
		return $temp_return;
	}

	/**
	 * @param int $menu_id
	 * @return array
	 */
	function helper_menu_data($menu_id = 0)
	{
		$temp_return = array();

		if ($menu_id) {
			global $menu;
			if (!empty($menu->data_front_complete)) {
				foreach($menu->data_front_complete as $menue) {
					if ($menue['menuid'] == $menu_id) {
						$temp_return = $menue;
					}
				}
			}
		}
		return $temp_return;
	}

	/**
	 * @param int $repore_id
	 * @return array
	 */
	function freigabe_data($repore_id = 0)
	{
		$temp_return = array();

		if ($repore_id) {
			$sql = sprintf("SELECT * FROM %s WHERE mf_repore_id='%d'",
				$this->db_praefix."mehrstufige_freigabe",
				$repore_id
			);
			$temp_informationen = $this->db->get_results($sql, ARRAY_A);

			if (!empty($temp_informationen)) {
				$temp_return = $temp_informationen[0];
			}
		}
		return $temp_return;
	}

	/**
	 * @param int $repore_id
	 */
	function freigabe_eintragen($repore_id = 0)
	{
		$temp_freigabe = $this->freigabe_data($repore_id);

		// Ermittlung Gruppen-IDs dieses Benutzers
		$sql = sprintf("SELECT gruppenid FROM %s WHERE userid='%d'",
			$this->db_praefix."papoo_lookup_ug",
			$this->user->userid);

		$temp_user_gruppen_ids = $this->db->get_col($sql);

		// Ermittlung Gruppen-IDs der mehrstufigen Freigabe
		$sql = sprintf("SELECT * FROM %s WHERE mfe_id='1'", $this->db_praefix."mehrstufige_freigabe_einstellungen");
		$temp_mf_gruppen_ids = $this->db->get_row($sql, ARRAY_A);

		$temp_user_is_freigeber = false;
		$temp_user_is_pruefer = false;
		$temp_user_is_autor = false;
		$temp_user_is_nothing = false;

		if (in_array($temp_mf_gruppen_ids['mfe_gruppenid_freigeber'], $temp_user_gruppen_ids)) {
			$temp_user_is_freigeber = true;
		}
		elseif (in_array($temp_mf_gruppen_ids['mfe_gruppenid_pruefer'], $temp_user_gruppen_ids)) {
			$temp_user_is_pruefer = true;
		}
		elseif (in_array($temp_mf_gruppen_ids['mfe_gruppenid_autor'], $temp_user_gruppen_ids)) {
			$temp_user_is_autor = true;
		}
		else {
			$temp_user_is_nothing = true;
		}

		if ($temp_user_is_freigeber) {
			$temp_freigabe['mf_freigeber_name'] = $this->user->username;
			$temp_freigabe['mf_freigeber_id'] = $this->user->userid;
			$temp_freigabe['mf_freigeber_date'] = "NOW()";
		}

		if ($temp_user_is_pruefer) {
			$temp_freigabe['mf_freigeber_name'] = "";
			$temp_freigabe['mf_freigeber_id'] = "";
			$temp_freigabe['mf_freigeber_date'] = "";

			$temp_freigabe['mf_pruefer_name'] = $this->user->username;
			$temp_freigabe['mf_pruefer_id'] = $this->user->userid;
			$temp_freigabe['mf_pruefer_date'] = "NOW()";
		}

		if ($temp_user_is_autor) {
			$temp_freigabe['mf_freigeber_name'] = "";
			$temp_freigabe['mf_freigeber_id'] = "";
			$temp_freigabe['mf_freigeber_date'] = "";

			$temp_freigabe['mf_pruefer_name'] = "";
			$temp_freigabe['mf_pruefer_id'] = "";
			$temp_freigabe['mf_pruefer_date'] = "";

			$temp_freigabe['mf_autor_name'] = $this->user->username;
			$temp_freigabe['mf_autor_id'] = $this->user->userid;
			$temp_freigabe['mf_autor_date'] = "NOW()";
		}

		$sql = sprintf("DELETE FROM %s WHERE mf_repore_id='%d'",
			$this->db_praefix."mehrstufige_freigabe",
			$repore_id
		);
		$this->db->query($sql);

		if (!$temp_user_is_nothing) {
			$sql = sprintf("INSERT INTO %s SET mf_repore_id='%d',
							mf_autor_name='%s', mf_autor_id='%d', mf_autor_date='%s',
							mf_pruefer_name='%s', mf_pruefer_id='%d', mf_pruefer_date='%s',
							mf_freigeber_name='%s', mf_freigeber_id='%d', mf_freigeber_date='%s'",

				$this->db_praefix."mehrstufige_freigabe",
				$repore_id,

				$this->db->escape($temp_freigabe['mf_autor_name']),
				$temp_freigabe['mf_autor_id'],
				$this->helper_date_or_now($temp_freigabe['mf_autor_date']),

				$this->db->escape($temp_freigabe['mf_pruefer_name']),
				$temp_freigabe['mf_pruefer_id'],
				$this->helper_date_or_now($temp_freigabe['mf_pruefer_date']),

				$this->db->escape($temp_freigabe['mf_freigeber_name']),
				$temp_freigabe['mf_freigeber_id'],
				$this->helper_date_or_now($temp_freigabe['mf_freigeber_date'])
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param string $date
	 * @return string
	 */
	function helper_date_or_now($date = "")
	{
		$temp_return = "NOW()";
		if ($date != "NOW()") {
			$temp_return = "'".$date."'";
		}

		return $temp_return;
	}


	// ***************************
	// ADMIN-FUNKTIONEN
	// ***************************
	/**
	 * @param string $action
	 */
	function switch_back ($action = "")
	{
		switch ($action) {
		case "mfe_save":
			$sql = sprintf("UPDATE %s 
								SET mfe_gruppenid_autor='%d', mfe_gruppenid_pruefer='%d', mfe_gruppenid_freigeber='%d'
								WHERE mfe_id='1'",
				$this->db_praefix."mehrstufige_freigabe_einstellungen",

				$this->checked->mfe_gruppenid_autor,
				$this->checked->mfe_gruppenid_pruefer,
				$this->checked->mfe_gruppenid_freigeber
			);
			$this->db->query($sql);

			$this->switch_back();
			break;

		case "":
		default:
			$sql = sprintf("SELECT * FROM %s WHERE mfe_id='1'", $this->db_praefix."mehrstufige_freigabe_einstellungen");

			$this->content->template['plugin']['mehrstufige_freigabe']['einstellungen'] = $this->db->get_row($sql, ARRAY_A);

			$sql = sprintf("SELECT * FROM %s", $this->db_praefix."papoo_gruppe");

			$this->content->template['plugin']['mehrstufige_freigabe']['gruppen'] = $this->db->get_results($sql, ARRAY_A);

			break;
		}
	}

	/**
	 * @param string $action
	 */
	function switch_back_init ($action = "")
	{
		switch ($action) {
		case "mfi_init_do":
			$temp_init_info = $this->helper_init_restore($this->checked);

			if (!$this->helper_init_test($temp_init_info)) {

				$this->content->template['plugin']['mehrstufige_freigabe']['init'] = $temp_init_info;
				$this->switch_back_init();
			}
			else {
				$temp_init_info = $this->helper_init_info_ergaenzen($temp_init_info);
				$this->freigabe_init($temp_init_info);

				$this->content->template['plugin']['mehrstufige_freigabe']['template_weiche'] = "fertig";
			}
			break;

		case "mfi_init":
			$temp_init_info = $this->helper_init_restore($this->checked);

			if (!$this->helper_init_test($temp_init_info)) {
				$this->content->template['plugin']['mehrstufige_freigabe']['init'] = $temp_init_info;
				$this->switch_back_init();
			}
			else {
				$temp_init_info = $this->helper_init_info_ergaenzen($temp_init_info);

				$this->content->template['plugin']['mehrstufige_freigabe']['init'] = $temp_init_info;
				$this->content->template['plugin']['mehrstufige_freigabe']['template_weiche'] = "pruefung";
			}
			break;

		case "":
		default:
			$sql = sprintf("SELECT * FROM %s", $this->db_praefix."papoo_user");

			$this->content->template['plugin']['mehrstufige_freigabe']['user'] = $this->db->get_results($sql, ARRAY_A);
			$this->content->template['plugin']['mehrstufige_freigabe']['template_weiche'] = "start";

			break;
		}
	}

	/**
	 * @param $params
	 */
	function freigabe_init($params)
	{
		// Liste aller Artikel-IDs erstellen
		// =====================================================
		$sql = sprintf("SELECT reporeID FROM %s", $this->db_praefix."papoo_repore");
		$artikel_ids = $this->db->get_col($sql);
		// .. zum einfacheren L�schen, array "flippen"
		$artikel_ids = array_flip($artikel_ids);


		// Liste der Freigabe-Informationen auslesen
		// =====================================================
		$sql = sprintf("SELECT * FROM %s", $this->db_praefix."mehrstufige_freigabe");
		$freigaben = $this->db->get_results($sql, ARRAY_A);

		if (!empty($freigaben)) {
			foreach ($freigaben as $freigabe) {
				// .. Artikel-ID aus Liste aller Artikel-IDs entfernen
				unset($artikel_ids[$freigabe['mf_repore_id']]);

				$temp_update_sql = "";

				if (!$freigabe['mf_autor_id']) {
					$temp_update_sql .= sprintf(" mf_autor_name='%s', mf_autor_id='%d', mf_autor_date='%s',",
						$this->db->escape($params['mfi_autor_username']),
						$params['mfi_autor_userid'],
						$this->db->escape($params['mfi_autor_datum'])
					);
				}

				if (!$freigabe['mf_pruefer_id']) {
					$temp_update_sql .= sprintf(" mf_pruefer_name='%s', mf_pruefer_id='%d', mf_pruefer_date='%s',",
						$this->db->escape($params['mfi_pruefer_username']),
						$params['mfi_pruefer_userid'],
						$this->db->escape($params['mfi_pruefer_datum'])
					);
				}

				if (!$freigabe['mf_freigeber_id']) {
					$temp_update_sql .= sprintf(" mf_freigeber_name='%s', mf_freigeber_id='%d', mf_freigeber_date='%s',",
						$this->db->escape($params['mfi_freigeber_username']),
						$params['mfi_freigeber_userid'],
						$this->db->escape($params['mfi_freigeber_datum'])
					);
				}

				if (!empty($temp_update_sql)) {
					// letztes Komma wieder entfernen !!!
					$temp_update_sql[strrpos($temp_update_sql, ",")] = " ";

					$sql = sprintf("UPDATE %s SET %s WHERE mf_repore_id='%d'",
						$this->db_praefix."mehrstufige_freigabe",
						$temp_update_sql,
						$freigabe['mf_repore_id']
					);
					$this->db->query($sql);
				}
			}
		}

		// Restliche, komplett leere Artikel mit Freigabe-Informationen versehen
		// =====================================================
		if (!empty($artikel_ids)) {
			// .. zum einfacheren Auslesen, array zur�ck-"flippen"
			$artikel_ids = array_flip($artikel_ids);

			foreach($artikel_ids as $artikel_id) {
				$sql = sprintf("INSERT INTO %s SET mf_repore_id='%d',
								mf_autor_name='%s', mf_autor_id='%d', mf_autor_date='%s',
								mf_pruefer_name='%s', mf_pruefer_id='%d', mf_pruefer_date='%s',
								mf_freigeber_name='%s', mf_freigeber_id='%d', mf_freigeber_date='%s'",

					$this->db_praefix."mehrstufige_freigabe",
					$artikel_id,

					$this->db->escape($params['mfi_autor_username']),
					$params['mfi_autor_userid'],
					$this->db->escape($params['mfi_autor_datum']),

					$this->db->escape($params['mfi_pruefer_username']),
					$params['mfi_pruefer_userid'],
					$this->db->escape($params['mfi_pruefer_datum']),

					$this->db->escape($params['mfi_freigeber_username']),
					$params['mfi_freigeber_userid'],
					$this->db->escape($params['mfi_freigeber_datum'])

				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	function helper_init_restore($params = array())
	{
		$temp_return = array();

		if (!empty($params)) {
			foreach ($params as $name => $wert) {
				if (strpos("XXX".$name, "mfi_")) {
					$temp_return[$name] = $wert;
				}
			}
		}
		return $temp_return;
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	function helper_init_test($params = array())
	{
		if(!isset($this->content->template['plugin']['mehrstufige_freigabe'])) {
			$this->content->template['plugin']['mehrstufige_freigabe'] = NULL;
		}

		$temp_return = true;

		if (empty($params['mfi_autor_userid'])) {
			$temp_return = false;

			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_autor_userid'] = true;
		}
		if (!preg_match("/^[0-9]{1,2}$/", $params['mfi_autor_tag']) OR !in_array($params['mfi_autor_tag'], range(1, 31))
			OR !preg_match("/^[0-9]{1,2}$/", $params['mfi_autor_monat']) OR !in_array($params['mfi_autor_monat'], range(1, 12))
			OR !preg_match("/^[0-9]{1,4}$/", $params['mfi_autor_jahr']) OR !in_array($params['mfi_autor_jahr'], range(1900, 2050))
		) {
			$temp_return = false;

			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_autor_datum'] = true;
		}

		if (empty($params['mfi_pruefer_userid'])) {
			$temp_return = false;

			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_pruefer_userid'] = true;
		}
		if (!preg_match("/^[0-9]{1,2}$/", $params['mfi_pruefer_tag']) OR !in_array($params['mfi_pruefer_tag'], range(1, 31))
			OR !preg_match("/^[0-9]{1,2}$/", $params['mfi_pruefer_monat']) OR !in_array($params['mfi_pruefer_monat'], range(1, 12))
			OR !preg_match("/^[0-9]{1,4}$/", $params['mfi_pruefer_jahr']) OR !in_array($params['mfi_pruefer_jahr'], range(1900, 2050))
		) {
			$temp_return = false;
			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_pruefer_datum'] = true;
		}

		if (empty($params['mfi_freigeber_userid'])) {
			$temp_return = false;

			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_freigeber_userid'] = true;
		}
		if (!preg_match("/^[0-9]{1,2}$/", $params['mfi_freigeber_tag']) OR !in_array($params['mfi_freigeber_tag'], range(1, 31))
			OR !preg_match("/^[0-9]{1,2}$/", $params['mfi_freigeber_monat']) OR !in_array($params['mfi_freigeber_monat'], range(1, 12))
			OR !preg_match("/^[0-9]{1,4}$/", $params['mfi_freigeber_jahr']) OR !in_array($params['mfi_freigeber_jahr'], range(1900, 2050))
		) {
			$temp_return = false;

			$this->content->template['plugin']['mehrstufige_freigabe']['error']['mfi_freigeber_datum'] = true;
		}

		return $temp_return;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	function helper_init_info_ergaenzen($params = array())
	{
		$params['mfi_autor_username'] = $this->helper_user_name($params['mfi_autor_userid']);
		$params['mfi_autor_datum'] =
			str_pad($params['mfi_autor_jahr'], 4, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_autor_monat'], 2, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_autor_tag'], 2, "0", STR_PAD_LEFT);

		$params['mfi_pruefer_username'] = $this->helper_user_name($params['mfi_pruefer_userid']);
		$params['mfi_pruefer_datum'] =
			str_pad($params['mfi_pruefer_jahr'], 4, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_pruefer_monat'], 2, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_pruefer_tag'], 2, "0", STR_PAD_LEFT);

		$params['mfi_freigeber_username'] = $this->helper_user_name($params['mfi_freigeber_userid']);
		$params['mfi_freigeber_datum'] =
			str_pad($params['mfi_freigeber_jahr'], 4, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_freigeber_monat'], 2, "0", STR_PAD_LEFT)."-".
			str_pad($params['mfi_freigeber_tag'], 2, "0", STR_PAD_LEFT);

		return $params;
	}

	/**
	 * @param int $user_id
	 * @return string|null
	 */
	function helper_user_name($user_id = 0)
	{
		$sql = sprintf("SELECT username FROM %s WHERE userid='%d'", $this->db_praefix."papoo_user", $user_id);
		$temp_return = $this->db->get_var($sql);

		return $temp_return;
	}
}

$mehrstufige_freigabe = new mehrstufige_freigabe_class();
