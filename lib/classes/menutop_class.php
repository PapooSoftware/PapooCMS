<?php
/**
 * #####################################
 * # papoo Version 3.0                 #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # (p) S.Bergmann@papoo.de           #
 * #####################################
 */

/**
 * Class menutop_class
 */
#[AllowDynamicProperties]
class menutop_class
{
	/**
	 * menutop_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		global $db_praefix;
		$this->db_praefix = $db_praefix;
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;
		// checked Klasse einbinde
		global $checked;
		$this->checked = &$checked;
		// Module-Klasse
		global $module;
		$this->module = &$module;
		// Sitemap-Klasse
		global $sitemap;
		$this->sitemap = &$sitemap;

		IfNotSetNull($this->checked->menutop_link_extern);
		IfNotSetNull($this->checked->menutop_action);

		$this->make_menutop();
	}

	/**
	 *
	 */
	function make_menutop()
	{

		// FRONTEND
		if (!defined("admin")) {
			#if ($this->module->module_aktiv['mod_menue_top'])
			#	{
			$this->content->template['menutop'] = $this->menutop_liste($this->cms->lang_id);

			if (empty($this->content->template['menutop'])) $this->module->module_aktiv['mod_menue_top'] = false;
			#}
		}
		// BACKEND
		else {
			if ($this->checked->menuid == 58 && $this->user->check_intern()) {
				$this->switch_backend($this->checked->menutop_action);
			}
		}
	}

	/**
	 * @param string $action
	 */
	function switch_backend ($action = "")
	{
		$this->user->check_access();

		switch ($action) {
		case "do_delete":
			$this->menutop_delete($this->checked->menutop_id);
			$this->menutop_reorder();
			$this->switch_backend();
			break;

		case "delete":
			$this->content->template['templateweiche'] = "LOESCHEN";
			$this->content->template['menutop'] = $this->menutop_data($this->checked->menutop_id, $this->cms->lang_id);
			break;

		case "save":
			$this->menutop_save($this->checked->menutop_modus, $this->checked->menutop_id, $this->checked->menutop_order_id);
			$this->switch_backend();
			break;

		case "add":
			$this->content->template['templateweiche'] = "NEU_EDIT";
			$this->content->template['menutop_modus'] = "NEU";
			$this->content->template['menutop'] = $this->menutop_data();
			$this->content->template['menutop']['menutop_order_id'] = $this->checked->menutop_order_id + 2;
			$this->menutop_make_sitemap();
			break;

		case "edit":
			$this->content->template['templateweiche'] = "NEU_EDIT";
			$this->content->template['menutop_modus'] = "EDIT";
			$this->content->template['menutop'] = $this->menutop_data($this->checked->menutop_id);
			$this->menutop_make_sitemap();
			break;

		case "order_hoch":
			$this->menutop_move("hoch", $this->checked->menutop_id, $this->checked->menutop_order_id);
			$this->menutop_reorder();
			$this->switch_backend();
			break;

		case "order_runter":
			$this->menutop_move("runter", $this->checked->menutop_id, $this->checked->menutop_order_id);
			$this->menutop_reorder();
			$this->switch_backend();
			break;

		case "":
		default:
			if(!empty($_SESSION['langid_front'])  ) {
				$lang_id = $_SESSION['langid_front'];
			}
			else {
				$lang_id = $this->cms->lang_id;
			}
			$this->content->template['templateweiche'] = "LISTE";
			$this->content->template['menutop'] = $this->menutop_liste($lang_id);
			break;
		}
	}

	/**
	 * @param $lang_id
	 * @return array|bool|void
	 */
	function menutop_liste($lang_id)
	{
		$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2
						WHERE t1.menutop_id=t2.mtlang_menutop_id AND t2.mtlang_lang_id='%d'
						ORDER BY t1.menutop_order_id",

			$this->db_praefix."papoo_menutop",
			$this->db_praefix."papoo_menutop_language",
			$this->db->escape($lang_id)
		);

		$temp_return = $this->db->get_results($sql, ARRAY_A);

		$uri=$this->get_uri();
		$i=0;
		if (is_array($temp_return)) {
			foreach ($temp_return as $temp) {
				$temp_return[$i]['mtlang_name']="nodecode:".str_ireplace("&","&amp;",$temp['mtlang_name']);
				$temp_return[$i]['mtlang_name']="nodecode:".$temp['mtlang_name'];
				$temp_return[$i]['mtlang_title']="nodecode:".$temp['mtlang_title'];
				$temp_return[$i]['mtlang_class']="nodecode:class=\"top_link\"";
				if ($uri==$temp['menutop_link']) {
					$temp_return[$i]['mtlang_class']="nodecode:class=\"aktiv_top_link\"";
				}
				$i++;
			}
		}

		if (!empty($temp_return)) {
			return $temp_return;
		}
		else {
			return false;
		}
	}

	/**
	 * @return bool|mixed|string|string[]|null
	 */
	function get_uri()
	{
		$uri=$_SERVER['REQUEST_URI'];
		$uri=str_replace("http://","",$uri);
		$uri_a=explode("\/",$uri,1);
		$uri=str_replace(PAPOO_WEB_PFAD,"",$uri_a['0']);

		if ($this->cms->mod_rewrite!=2) {
			$uri=substr($uri,1,9999);
		}

		$uri = str_ireplace('&',"&amp;",$uri);
		return $uri;
	}

	/**
	 * @param int $menutop_id
	 * @param int $lang_id
	 * @return array
	 */
	function menutop_data($menutop_id = 0, $lang_id = 0)
	{
		$temp_return = array();

		// Daten eines Menü-Punktes mit Sprach-Informationen einer Sprache laden
		if ($menutop_id && $lang_id) {
			$sql = sprintf("SELECT * FROM %s AS t1, %s AS t2
							WHERE t1.menutop_id=t2.mtlang_menutop_id
							AND t1.menutop_id='%d' AND t2.mtlang_lang_id='%d'",

				$this->db_praefix."papoo_menutop",
				$this->db_praefix."papoo_menutop_language",
				$this->db->escape($menutop_id),
				$this->db->escape($lang_id)
			);

			$temp_result = $this->db->get_results($sql, ARRAY_A);
			$i=0;
			foreach ($temp_return as $temp) {
				$temp_return[$i]['mtlang_name']="nodecode:".$temp['mtlang_name'];
				$temp_return[$i]['mtlang_title']="nodecode:".$temp['mtlang_title'];
				$i++;
			}
			$temp_return = $temp_result[0];
		}
		// Daten eines Menü-Punktes mit einem Array "sprachen" laden welches die Informationen aller Sprachen enthält
		else {
			$sql = sprintf("SELECT * FROM %s WHERE menutop_id=%d",

				$this->db_praefix."papoo_menutop",
				$this->db->escape($menutop_id)
			);

			$temp_result = $this->db->get_results($sql, ARRAY_A);
			if (!empty($temp_result)) {
				$temp_return = $temp_result[0];
			}

			$temp_sprachen = array();
			if (is_array($this->content->template['languageget'])) {
				foreach ($this->content->template['languageget'] as $sprache) {
					$sql = sprintf("SELECT * FROM %s
									WHERE mtlang_menutop_id='%d' AND mtlang_lang_id='%d'",

						$this->db_praefix."papoo_menutop_language",
						$this->db->escape($menutop_id),
						$this->db->escape($sprache['lang_id'])
					);
					$texte = $this->db->get_results($sql, ARRAY_A);
					$i=0;
					if (is_array($texte)) {
						foreach ($texte as $temp) {
							$texte[$i]['mtlang_name']="nodecode:".$temp['mtlang_name'];
							$texte[$i]['mtlang_title']="nodecode:".$temp['mtlang_title'];
							$i++;
						}
					}

					if (empty($texte)) {
						$temp_sprachen[$sprache['lang_id']] = $sprache;
					}
					else {
						$temp_sprachen[$sprache['lang_id']] = array_merge($sprache, $texte[0]);
					}
				}
			}
			$temp_return['sprachen'] = $temp_sprachen;
		}
		return $temp_return;
	}

	/**
	 * @param string $modus
	 * @param int $menutop_id
	 * @param int $order_id
	 * @return bool|int|mixed
	 */
	function menutop_save($modus = "", $menutop_id = 0, $order_id = 0)
	{
		// 1. Menü-Link ermitteln
		switch ($this->checked->menutop_link_selector)
		{
		case "SELECTION":
			$temp_link = $this->checked->menutop_link_select;
			break;

		case "TEXT":
			$temp_link = $this->checked->menutop_link_text;
			break;

		case "":
		default:
			return false;
			break;
		}

		// 2. Modus ermitteln und Eintrag in Tabelle papoo_menutop vornehmen
		switch ($modus) {
		case "EDIT":
			$sql = sprintf("UPDATE %s SET menutop_link='%s', menutop_extern='%d' WHERE menutop_id='%d'",
				$this->db_praefix."papoo_menutop",
				$this->db->escape($temp_link),
				$this->db->escape($this->checked->menutop_link_extern),
				$this->db->escape($menutop_id)
			);
			$this->db->query($sql);
			break;

		case "NEU":
			$sql = sprintf("INSERT INTO %s SET menutop_link='%s', menutop_extern='%d', menutop_order_id='%d'",
				$this->db_praefix."papoo_menutop",
				$this->db->escape($temp_link),
				$this->db->escape($this->checked->menutop_link_extern),
				$this->db->escape($order_id)
			);
			$this->db->query($sql);
			$menutop_id = $this->db->insert_id;
			$this->menutop_reorder();
			break;

		case "":
		default:
			return false;
			break;
		}

		// 3. Sprach-Daten speichern
		// 3.a bestehende Daten löschen
		$sql = sprintf("DELETE FROM %s WHERE mtlang_menutop_id='%d'",
			$this->db_praefix."papoo_menutop_language",
			$this->db->escape($menutop_id)
		);
		$this->db->query($sql);

		// 3.b neue Daten speichern
		if (!empty($this->checked->sprachen)) {
			foreach($this->checked->sprachen as $sprache) {
				$sql = sprintf("INSERT INTO %s
								SET mtlang_menutop_id='%d', mtlang_lang_id='%d',
								mtlang_name='%s', mtlang_title='%s', mtlang_link='%s'",
					$this->db_praefix."papoo_menutop_language",
					$this->db->escape($menutop_id),
					$this->db->escape($sprache['lang_id']),
					$this->db->escape($sprache['name']),
					$this->db->escape($sprache['title']),
					$this->db->escape($sprache['link'])
				);
				$this->db->query($sql);
			}
		}
		return $menutop_id;
	}

	/**
	 * @param int $menutop_id
	 */
	function menutop_delete($menutop_id = 0)
	{
		if ($menutop_id) {
			// 1. Eintrag in Tabelle "papoo_menutop" löschen
			$sql = sprintf("DELETE FROM %s WHERE menutop_id='%d'",
				$this->db_praefix."papoo_menutop",
				$this->db->escape($menutop_id)
			);
			$this->db->query($sql);

			// 2. Einträge in Sprach-Tabelle "papoo_menutop_language" löschen
			$sql = sprintf("DELETE FROM %s WHERE mtlang_menutop_id='%d'",
				$this->db_praefix."papoo_menutop_language",
				$this->db->escape($menutop_id)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Funktionen zum Verschieben
	 *
	 * @param $modus
	 * @param $menutop_id
	 * @param $order_id
	 */
	function menutop_move($modus, $menutop_id, $order_id)
	{
		if ($modus == "hoch") {
			$order_id_new = $order_id - 15;
		}
		else {
			$order_id_new = $order_id + 15;
		}

		$sql = sprintf("UPDATE %s SET menutop_order_id='%d' WHERE menutop_id='%d'",
			$this->db_praefix."papoo_menutop",
			$this->db->escape($order_id_new),
			$this->db->escape($menutop_id)
		);
		$this->db->query($sql);
	}

	/**
	 *
	 */
	function menutop_reorder()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY menutop_order_id", $this->db_praefix."papoo_menutop");
		$temp_result = $this->db->get_results($sql, ARRAY_A);

		if (!empty($temp_result)) {
			$temp_nummer = 10;
			foreach ($temp_result as $menutop) {
				$sql = sprintf("UPDATE %s SET menutop_order_id='%d' WHERE menutop_id='%d'",
					$this->db_praefix."papoo_menutop",
					$this->db->escape($temp_nummer),
					$this->db->escape($menutop['menutop_id'])
				);
				$this->db->query($sql);
				$temp_nummer += 10;
			}
		}
	}

	/**
	 * Hilfsfunktionen
	 */
	function menutop_make_sitemap()
	{
		$this->sitemap->make_sitemap();
		$this->content->template['table_data_site']=$this->content->template['table_data'];
		$this->content->template['table_data']=[];
		$this->content->template['plugin']=[];
	}
}

$menutop = new menutop_class();