<?php

/**
 * Class bildwechsler_class
 */
class bildwechsler_class
{
	// Der Bildtext aus dem Feld "Text" fest in das Bild eingebrannt.
	// Im Template kann das Bild mit dem eingebrannten Text Ã¼ber die
	// Smarty-Variable "bw_bild_mit_eingebranntem_text" abgerufen werden.
	private $text_einbrennen = FALSE;

	private $config;

	/**
	 * bildwechsler_class::bildwechsler_class()
	 *
	 * @return void
	 */
	function __construct()
	{
		// Einbindung der globalen Papoo-Objekte
		global $checked, $db, $db_praefix, $content, $cms, $db_abs;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->content = & $content;
		$this->cms = & $cms;
		$this->db_abs = & $db_abs;

		$this->updateConfig();
		$this->loadConfig();

		// Aufruf Aktionsweiche
		$this->make_bildwechsler();
	}

	/**
	 * @param string $name
	 */
	function save_active_template($name)
	{
		$sql = sprintf(
			"UPDATE %s SET bw_template_name='%s'",
			$this->db_praefix."bildwechsler_config",
			$this->db->escape($name)
		);
		$this->db->query($sql);
	}

	private function updateConfig()
	{
		if ($_SERVER["REQUEST_METHOD"] === "POST") {
			if (isset($_POST["menu_settings"])) {
				$this->db->query(
					"UPDATE {$this->cms->tbname["bildwechsler_config"]} ".
					"SET `use_ancestors_as_fallback` = ".(isset($_POST["use_ancestors_as_fallback"]) ? 1 : 0)." ".
					"WHERE id = 1"
				);
			}
		}
	}

	private function loadConfig()
	{
		$this->config = $this->db->get_row(
			"SELECT * FROM {$this->cms->tbname["bildwechsler_config"]} WHERE id = 1",
			ARRAY_A
		);
		$this->content->template["plugin_bildwechsler_config"] = [
			"use_ancestors_as_fallback" => (bool)$this->config["use_ancestors_as_fallback"]
		];
	}

	/**
	 * bildwechsler_class::get_bw_templates()
	 * liefert alle verfï¿½gbaren Bildwechsler-templates im Verzeichnis /plugins/bildwechsler/templates/module
	 * @return array
	 */
	function get_bw_templates()
	{
		$templates_pfad = PAPOO_ABS_PFAD . '/plugins/bildwechsler/skripte';
		$templates = array();

		$handle = opendir ($templates_pfad);
		while ($datei = readdir($handle)) {
			if (!is_dir($datei)) {
				$templates[] = $datei;
			}
		}
		closedir($handle);
		asort($templates);
		return $templates;
	}

	/**
	 * @return mixed
	 */
	function load_active_template()
	{
		$sql = sprintf(
			"SELECT %s FROM %s",
			"bw_template_name",
			$this->db_praefix."bildwechsler_config"
		);

		$temp_return = $this->db->get_row($sql, ARRAY_A);

		return $temp_return['bw_template_name'];
	}

	/**
	 * bildwechsler_class::make_bildwechsler()
	 *
	 * @return void
	 */
	function make_bildwechsler()
	{
		IfNotSetNull($this->checked->bw_action);

		// FRONTEND:
		if (!defined("admin")) {
			$this->switch_front();
		}

		$this->reset_sprachen();

		// BACKEND:
		if (defined("admin")) {
			global $template;

			if (strpos("XXX".$template, "bildwechsler/templates/back.html") !== false) {
				$this->content->template['bildwechsler_css_path'] = PAPOO_WEB_PFAD.'/plugins/bildwechsler/css';
				global $user;
				$user->check_intern();
				$this->switch_back($this->checked->bw_action);
			}
		}

	}

	private function reset_sprachen()
	{
		$sql = sprintf(
			"UPDATE %s SET bw_lang_id='1' WHERE bw_lang_id<'1'",
			$this->db_praefix . "bildwechsler");
		$this->db->query($sql);
	}

	/**
	 * bildwechsler_class::switch_front()
	 *
	 * @return void
	 */
	function switch_front()
	{
		$temp_wechselbilder = $this->bw_liste($this->checked->menuid);

		// FallBack Grundeinstellungen
		if (empty($temp_wechselbilder)) {
			$temp_wechselbilder = $this->bw_liste(0);
		}

		if (!empty($temp_wechselbilder)) {
			$index = 0;
			foreach ($temp_wechselbilder as $temp_bild) {
				if ($temp_bild['bw_extra_link'] && $temp_bild['bw_extra_link_text']) {
					$temp_wechselbilder[$index]['bw_extra_link'] =
						'<a href="' . $temp_bild['bw_extra_link'] . '">' . $temp_bild['bw_extra_link_text'] . '</a>';
					$temp_wechselbilder[$index]['bw_extra_link_url'] = $temp_bild['bw_extra_link'];
					// unset ($temp_wechselbilder[$index]['bw_extra_link_text']);
				}
				$index++;
			}

			if ($this->text_einbrennen) {
				foreach ($temp_wechselbilder as &$temp_wechselbild) {
					$temp_wechselbild['bw_bild_mit_eingebranntem_text'] =
						str_replace(
							"../images/",
							PAPOO_WEB_PFAD . "/images/text_", $temp_wechselbild['bw_bild']);
				}
				unset($temp_wechselbild);
			}

			$this->content->template['plugins']['bildwechsler'] = $temp_wechselbilder;
			$active_template = $this->load_active_template();
			$this->content->template['bildwechsler_active_template'] = $active_template;
			$this->content->template['plugin_header'][] = '<link rel="stylesheet" href="plugins/bildwechsler/skripte/'.$active_template.'/css/'.$active_template.'.css" />';
		}
		else {
			global $module;
			$module->module_aktiv['mod_bildwechsler'] = false;
		}
	}

	/**
	 * bildwechsler_class::switch_back()
	 *
	 * @param string $action
	 * @return void
	 */
	function switch_back($action="")
	{
		switch($action) {
		case "do_delete":
			$sql = sprintf(
				"DELETE FROM %s WHERE bw_id='%d' AND bw_lang_id='%d'",
				$this->db_praefix . "bildwechsler",
				$this->checked->bw_id,
				$this->cms->lang_id
			);
			$this->db->query($sql);
			$this->bw_reorder_auto($this->checked->bw_menu_id);

			$this->switch_back("bw_liste");
			break;

		case "delete":
			$this->content->template['plugins']['bildwechsler'] = $this->bw_data($this->checked->bw_id);
			$this->content->template['template_weiche'] = "BW_DELETE";
			break;

		case "edit":
			$temp_data = isset($this->checked->bw_id) ? $this->bw_data($this->checked->bw_id) : NULL;
			if (!stristr($temp_data['bw_bild'], "iframe")) {
				$temp_data['wechselbild'] = '<img '.$temp_data['bw_bild'].' />';
				if (isset($temp_data['bw_link']) && $temp_data['bw_link']) {
					$temp_data['wechselbild'] = '<a ' . $temp_data['bw_link'] . '>' . $temp_data['wechselbild'] . '</a>';
				}
			}
			else {
				$temp_data['wechselbild'] = $temp_data['bw_bild'];
			}

			$this->content->template['plugins']['bildwechsler'] = $temp_data;

			$this->content->template['template_weiche'] = "BW_EDIT";
			break;

		case "bw_order_runter":
		case "bw_order_hoch":
			$temp_count = 15;
			if ($action == "bw_order_hoch") $temp_count = -15;

			$sql = sprintf(
				"UPDATE %s SET bw_order_id=(bw_order_id+(%d)) WHERE bw_id='%d' AND bw_lang_id='%d' ",
				$this->db_praefix."bildwechsler",
				$temp_count,
				$this->checked->bw_id,
				$this->cms->lang_id
			);

			$this->db->query($sql);
			$this->bw_reorder_auto($this->checked->bw_menu_id);

			$this->switch_back("bw_liste");
			break;


		case "save":
			$this->bw_save();
			$this->switch_back("bw_liste");
			break;

		case "bw_liste":
			$this->content->template['plugins']['bildwechsler']['liste'] =
				$this->bw_liste($this->checked->bw_menu_id, true);
			$this->content->template['template_weiche'] = "BW_LISTE";
			break;

		case "":
		default:
			$this->menu_liste();
			$this->content->template['plugins']['bildwechsler']['menu_status'] = $this->menu_bw_status();
			$this->content->template['template_weiche'] = "MENU_LISTE";
			break;
		}
	}

	/**
	 * bildwechsler_class::menu_liste()
	 *
	 * @return void
	 */
	function menu_liste()
	{
		global $menu;
		global $intern_menu;

		IfNotSetNull($_POST['bw_template_name']);

		$menu->data_front_publish = $intern_menu->mak_menu_liste_zugriff();
		$this->content->template['templateweiche'] = "LISTE";

		if ($_POST['bw_template_name']) {
			$bw_template_name = $_POST['bw_template_name'];
			$this->save_active_template($bw_template_name);
		}

		$this->content->template['plugins']['bildwechsler']['templates'] = $this->get_bw_templates();
		$this->content->template['plugins']['bildwechsler']['active_template'] = $this->load_active_template();

		if ($this->cms->categories == 1) {
			// Kategorien rausholen
			$menu->preset_categories();
			$menu->get_menu_cats();
		}

		//Zusammenbauen und die ohne rechte entsprechend triggern
		$this->content->template['menulist_data'] =
			$menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

		IfNotSetNull($menu->ebenen_shift_ende);

		$this->content->template['menulist_ebenen_shift_ende'] = $menu->ebenen_shift_ende;
		if ($this->cms->categories == 1) {
			$menu->get_menu_cats();
			$menu->make_menucats();
			// Kategorien rausholen
			$catlist_data = $menu->preset_categories(true);
			$this->content->template['catlist_data'] = $catlist_data;
			$menu->get_menu_cats();
			$this->content->template['categories'] = 1;
		}
		else {
			$this->content->template['catlist_data'][0] = array("catid"=>-1);
			$this->content->template['no_categories'] = 1;
		}
		$this->content->template['menulist_data_bildwechsler']=$this->content->template['menulist_data'];
	}

	/**
	 * bildwechsler_class::menu_bw_status()
	 *
	 * @return mixed
	 */
	function menu_bw_status()
	{
		$temp_return = array();

		$sql = sprintf("SELECT bw_menu_id, count(bw_id) AS anzahl_bilder FROM %s WHERE bw_lang_id='%d' GROUP by bw_menu_id",
			$this->db_praefix."bildwechsler",
			$this->cms->lang_id
		);
		$temp_menu_anzahl = $this->db->get_results($sql, ARRAY_A);

		if (!empty($temp_menu_anzahl)) {
			foreach ($temp_menu_anzahl as $temp_anzahl) {
				$temp_return[$temp_anzahl['bw_menu_id']] = $temp_anzahl;
			}
		}

		return $temp_return;
	}


	/**
	 * bildwechsler_class::get_menuids_sub()
	 * Ruft sich rekursiv auf um alle Eltern-IDs zu holen.
	 * Speichert die ursprï¿½ngliche Menu-ID und die der Eltern-
	 * Menuepunkte in $this->menu_array.
	 * @param integer $menuid
	 * @return void
	 */
	private function get_menuids_sub($menuid = 0)
	{
		$sql = sprintf(
			"SELECT untermenuzu FROM %s WHERE menuid='%d' ",
			$this->db_praefix . "papoo_me_nu",
			$menuid
		);
		$subid = (int)($this->db->get_var($sql));

		$this->menu_array[] = $menuid;

		if ($subid >= 1) {
			$this->get_menuids_sub($subid);
		}
	}

	/**
	 * bildwechsler_class::bw_liste()
	 *
	 * @param integer $bw_menu_id
	 * @param bool $isAdmin
	 * @return array
	 */
	function bw_liste($bw_menu_id = 0, $isAdmin = false)
	{
		if (
			!$isAdmin
			&& isset($this->config["use_ancestors_as_fallback"])
			&& $this->config["use_ancestors_as_fallback"]
		) {
			$this->get_menuids_sub($bw_menu_id);
		}

		//Name des Menuepunktes
		$xsql = array();
		$xsql['dbname'] = "papoo_menu_language";
		$xsql['select_felder'] = array("menuname");
		$xsql['limit'] = "";
		$xsql['where_data'] = array("menuid_id" => $bw_menu_id, "lang_id" => $this->cms->lang_id );
		$result = $this->db_abs->select($xsql);

		if (!isset($result['0'])) {
			$result['0'] = 0;
		}

		$this->content->template['aktu_menu_name']=$result['0']['menuname'];

		//Nur durchführen wenn es mehrer Menueids gibt
		if (isset($this->menu_array) && is_array($this->menu_array)) {
			foreach ($this->menu_array as $key => $value) {
				$sql = sprintf(
					"SELECT * FROM %s WHERE bw_menu_id=%d AND bw_lang_id='%d' ORDER BY bw_order_id",
					$this->db_praefix."bildwechsler",
					$value,
					$this->cms->lang_id
				);
				$temp_return = $this->db->get_results($sql, ARRAY_A);

				//Durchgehen wg. Video
				if (is_array($temp_return)) {
					foreach ($temp_return as $k => $v) {
						if (stristr($v['bw_bild'],"iframe")) {
							$temp_return[$k]['has_iframe'] = "ok";
						}
						else {
							$temp_return[$k]['has_iframe'] = "";
						}
					}
				}
				if (!empty($temp_return)) {
					return $temp_return;
				}
			}
		}
		else {
			$sql = sprintf("SELECT * FROM %s WHERE bw_menu_id=%d AND bw_lang_id='%d' ORDER BY bw_order_id",
				$this->db_praefix."bildwechsler",
				$bw_menu_id,
				$this->cms->lang_id);
			$temp_return = $this->db->get_results($sql, ARRAY_A);

			//Durchgehen wg. Video
			if (is_array($temp_return)) {
				foreach ($temp_return as $k => $v) {
					if (stristr($v['bw_bild'],"iframe")) {
						$temp_return[$k]['has_iframe'] = "ok";
					}
					else {
						$temp_return[$k]['has_iframe'] = "";
					}
				}
			}
			return $temp_return;
		}
	}

	/**
	 * bildwechsler_class::bw_data()
	 *
	 * @param integer $bw_id
	 * @return mixed
	 */
	function bw_data($bw_id = 0)
	{
		$temp_return = array();

		if ($bw_id) {
			$sql = sprintf(
				"SELECT * FROM %s WHERE bw_id='%d' AND bw_lang_id='%d' ",
				$this->db_praefix . "bildwechsler",
				$bw_id,
				$this->cms->lang_id
			);
			$temp_return = $this->db->get_row($sql, ARRAY_A);
		}

		$temp_return['bw_text'] = isset($temp_return['bw_text']) ? "nobr:" . $temp_return['bw_text'] : NULL;
		$temp_return['bw_noch_mehr_text'] = isset($temp_return['bw_noch_mehr_text']) ? "nobr:" . $temp_return['bw_noch_mehr_text'] : NULL;

		return $temp_return;
	}

	/**
	 * bildwechsler_class::bw_save()
	 *
	 * @return void
	 */
	function bw_save()
	{
		$temp_id = $this->checked->bw_id;
		$temp_wechselbild = $this->checked->wechselbild;
		$temp_text = $this->checked->bw_text;
		$temp_ueberschrift = $this->checked->bw_ueberschrift;
		$temp_text2 = $this->checked->bw_noch_mehr_text;
		$temp_link2 = $this->checked->bw_extra_link;
		$temp_link2_text = $this->checked->bw_extra_link_text;

		if ($temp_wechselbild) {
			if (!stristr($temp_wechselbild,"iframe")) {
				$result_array = array ();
				preg_match('/<img (.*?) \/>/is', $temp_wechselbild, $result_array);
				IfNotSetNull($result_array[1]);
				$temp_bild = $result_array[1];

				preg_match('/<a (.*?)>/is', $temp_wechselbild, $result_array);
				IfNotSetNull($result_array[1]);
				$temp_link = $result_array[1];
			}
			else {
				$temp_bild = $temp_wechselbild;
			}

			if ($this->text_einbrennen) {
				// Dateinamen des Bildes heraussuchen
				$pattern = "/src=\"([^\"]*)\"/";
				$subject = $temp_wechselbild;
				preg_match($pattern, $subject, $matches);
				$img_url = $matches[1];
				$img_filename = 'text_' . str_replace("../images/", "", $img_url);

				// Bild - Ebene initialisieren
				$imageLayer = PHPImageWorkshop\ImageWorkshop::initFromPath($img_url);

				// Schablonen - Ebene initialisieren
				$patternLayer = PHPImageWorkshop\ImageWorkshop::initFromPath(
					'../plugins/bildwechsler/img/bildwechsler_grafikelement_neu.png'
				);

				// Text in Zeilen splitten
				$text = $temp_text;

				if ($text != "") {
					// Schablonen - Ebene auf Bild - Ebene draufpappen
					$imageLayer->addLayerOnTop($patternLayer);
					$lines = explode("<br />", $text);
					// Textvariablen
					$fontPath = "../plugins/bildwechsler/fonts/arial.ttf";
					$fontSize = 24;
					$fontColor = "FFFFFF";
					$textRotation = 0;
					$xOffset = 780;
					$yOffset = 80;
					$lineHeight = 1.6;

					// Fuer jede Zeile eine Textebene draufpappen, weil Zeilenabstand nicht
					// standardmaessig in der GD-Lib drin ist.
					$i = 0;
					foreach ($lines as $line) {
						$textLayers[$i] = PHPImageWorkshop\ImageWorkshop::initTextLayer(
							$line, $fontPath, $fontSize, $fontColor, $textRotation
						);
						$imageLayer->addLayerOnTop(
							$textLayers[$i], $xOffset, $yOffset + $i * $fontSize * $lineHeight
						);
						$i++;
					}
				}
				// Bild speichern
				$imageLayer->save("../images/", $img_filename, true, null, 95);
			}

			if (!$temp_id) {
				$sql = sprintf("INSERT INTO %s SET bw_order_id='999999'", $this->db_praefix."bildwechsler");
				$this->db->query($sql);
				$temp_id = $this->db->insert_id;
			}

			$sql = sprintf(
				"UPDATE %s SET bw_menu_id='%d',
                    bw_bild='%s',
                    bw_link='%s',
                    bw_text='%s',
                    bw_ueberschrift='%s',
                    bw_noch_mehr_text='%s',
                    bw_extra_link='%s',
                    bw_extra_link_text='%s',
                    bw_lang_id='%d'
                WHERE bw_id='%d'",
				$this->db_praefix . "bildwechsler",
				$this->checked->bw_menu_id,
				$this->db->escape($temp_bild),
				$this->db->escape($temp_link),
				$this->db->escape($temp_text),
				$this->db->escape($temp_ueberschrift),
				$this->db->escape($temp_text2),
				$this->db->escape($temp_link2),
				$this->db->escape($temp_link2_text),
				$this->cms->lang_id,
				$temp_id
			);
			$this->db->query($sql);

			$this->bw_reorder_auto($this->checked->bw_menu_id);
		}
	}

	/**
	 * bildwechsler_class::bw_reorder_auto()
	 *
	 * @param integer $bw_menu_id
	 * @return void
	 */
	function bw_reorder_auto($bw_menu_id = 0)
	{
		$temp_liste = $this->bw_liste($bw_menu_id, true);

		if (!empty($temp_liste)) {
			$temp_count = 10;

			foreach ($temp_liste as $temp_bild) {
				$sql = sprintf(
					"UPDATE %s SET bw_order_id='%d' WHERE bw_id='%d' AND bw_lang_id='%d'",
					$this->db_praefix . "bildwechsler",
					$temp_count,
					$temp_bild['bw_id'],
					$this->cms->lang_id
				);
				$this->db->query($sql);
				$temp_count += 10;
			}
		}
	}
}

$bildwechsler = new bildwechsler_class();