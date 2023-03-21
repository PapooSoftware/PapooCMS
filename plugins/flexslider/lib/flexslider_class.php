<?php

/**
 * Class flexslider_class
 */
#[AllowDynamicProperties]
class flexslider_class
{
	/**
	 * flexslider_class constructor.
	 */
	function __construct()
	{
		// Einbindung der globalen Papoo-Objekte
		global $checked, $db, $db_praefix, $content;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->content = & $content;

		// Aufruf Aktionsweiche
		$this->make_flexslider();
	}

	private function set_flexslider_erben()
	{
		$this->flexslider_menu_erben=true;
	}

	function make_flexslider()
	{
		// FRONTEND:
		if (!defined("admin")) {
			$this->switch_front();
		}

		// BACKEND:
		if (defined("admin")) {
			global $template;
			if (strpos("XXX".$template, "flexslider/templates/back.html")) {
				global $user;
				$user->check_intern();
				$this->switch_back($this->checked->fs_action);
			}
		}
	}

	/**
	 * flexslider_class::switch_front()
	 *
	 * @return void
	 */
	function switch_front()
	{
		$temp_wechselbilder = $this->fs_liste($this->checked->menuid);
		// FallBack Grundeinstellungen
		if (empty($temp_wechselbilder)) {
			$temp_wechselbilder = $this->fs_liste(0);
		}

		if (!empty($temp_wechselbilder)) {
			$this->content->template['plugins']['flexslider'] = $temp_wechselbilder;
		}
		else {
			global $module;
			$module->module_aktiv['mod_flexslider'] = false;
		}
	}

	/**
	 * flexslider_class::switch_back()
	 *
	 * @param string $action
	 * @return void
	 */
	function switch_back($action="")
	{
		switch($action) {
		case "do_delete":
			$sql = sprintf("DELETE FROM %s WHERE fs_id='%d'",
				$this->db_praefix."plugin_flexslider",
				$this->checked->fs_id
			);
			$this->db->query($sql);
			$this->fs_reorder_auto($this->checked->fs_menu_id);

			$this->switch_back("fs_liste");
			break;

		case "delete":
			$this->content->template['plugins']['flexslider'] = $this->fs_data($this->checked->fs_id);
			//print_r($this->content->template['plugins']['flexslider']);
			$this->content->template['template_weiche'] = "FS_DELETE";
			break;

		case "edit":
			$temp_data = $this->fs_data($this->checked->fs_id);
			$temp_data['wechselbild'] = '<img '.$temp_data['fs_bild'].' />';

			/*if ($temp_data['fs_text'])
			{
				$temp_data['wechselbild'] = '<a '.$temp_data['fs_link'].'>'.'<img '.$temp_data['fs_bild'].' />'.'</a>'.'<h2>'.$temp_data['fs_text'].'</h2>';
			}*/
			$this->content->template['plugins']['flexslider'] = $temp_data;

			$this->content->template['template_weiche'] = "FS_EDIT";
			break;

		case "fs_order_runter":
		case "fs_order_hoch":
			$temp_count = 15;
			if ($action == "fs_order_hoch") $temp_count = -15;

			$sql = sprintf("UPDATE %s SET fs_order_id=(fs_order_id+(%d)) WHERE fs_id='%d'",
				$this->db_praefix."plugin_flexslider",
				$temp_count,
				$this->checked->fs_id
			);
			$this->db->query($sql);
			$this->fs_reorder_auto($this->checked->fs_menu_id);

			$this->switch_back("fs_liste");
			break;


		case "save":
			$this->fs_save();
			$this->switch_back("fs_liste");
			break;

		case "fs_liste":
			$this->content->template['plugins']['flexslider']['liste'] = $this->fs_liste($this->checked->fs_menu_id);
			$this->content->template['template_weiche'] = "FS_LISTE";
			break;

		case "":
		default:
			$this->menu_liste();
			$this->content->template['plugins']['flexslider']['menu_status'] = $this->menu_fs_status();
			$this->content->template['template_weiche'] = "MENU_LISTE";
			break;
		}
	}

	/**
	 * flexslider_class::menu_liste()
	 *
	 * @return void
	 */
	function menu_liste()
	{
		global $menu;
		global $intern_menu;

		$menu->data_front_publish = $intern_menu->mak_menu_liste_zugriff();
		$this->content->template['templateweiche'] = "LISTE";
		if ($this->cms->categories == 1) {
			// Kategorien rausholen
			$menu->preset_categories();
			$menu->get_menu_cats();
		}

		//Zusammenbauen und die ohne rechte entsprechend triggern
		$this->content->template['menulist_data'] = $menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

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
	}

	/**
	 * flexslider_class::menu_fs_status()
	 *
	 * @return array
	 */
	function menu_fs_status()
	{
		$temp_return = array();

		$sql = sprintf("SELECT fs_menu_id, count(fs_id) AS anzahl_bilder FROM %s GROUP by fs_menu_id", $this->db_praefix."plugin_flexslider");
		$temp_menu_anzahl = $this->db->get_results($sql, ARRAY_A);

		if (!empty($temp_menu_anzahl)) {
			foreach ($temp_menu_anzahl as $temp_anzahl) {
				$temp_return[$temp_anzahl['fs_menu_id']] = $temp_anzahl;
			}
		}

		return $temp_return;
	}



	/**
	 * flexslider_class::get_menuids_sub()
	 * Ruft sich rekursiv auf um alle Eltern IDs zu holen
	 * @param integer $menuid
	 * @return void
	 */
	private function get_menuids_sub($menuid=0)
	{
		$sql = sprintf("SELECT untermenuzu FROM %s WHERE menuid='%d' ", $this->db_praefix."papoo_me_nu", $menuid);

		$subid = $this->db->get_var($sql);

		if ($subid>=1) {
			$this->menu_array[] =$subid;
			$this->get_menuids_sub($subid);
		}
	}

	/**
	 * flexslider_class::fs_liste()
	 *
	 * @param integer $fs_menu_id
	 * @return array|void
	 */
	function fs_liste($fs_menu_id = 0)
	{
		$this->set_flexslider_erben();

		if ($this->flexslider_menu_erben) {
			$this->get_menuids_sub($fs_menu_id);
		}

		//Nur durchf�hren wenn es mehrer Men�ids gibt
		if (is_array($this->menu_array)) {
			if (is_array($this->menu_array)) {
				foreach ($this->menu_array as $key=>$value) {
					$sql = sprintf("SELECT * FROM %s WHERE fs_menu_id=%d ORDER BY fs_order_id", $this->db_praefix."plugin_flexslider", $value);
					$temp_return = $this->db->get_results($sql, ARRAY_A);

					if (!empty($temp_return)) {
						return $temp_return;
					}
				}
			}
		}
		else {
			$sql = sprintf("SELECT * FROM %s WHERE fs_menu_id=%d ORDER BY fs_order_id", $this->db_praefix."plugin_flexslider", $fs_menu_id);
			$temp_return = $this->db->get_results($sql, ARRAY_A);
			return $temp_return;
		}
	}

	/**
	 * flexslider_class::fs_data()
	 *
	 * @param integer $fs_id
	 * @return array|void
	 */
	function fs_data($fs_id = 0)
	{
		$temp_return = array();

		if ($fs_id) {
			$sql = sprintf("SELECT * FROM %s WHERE fs_id='%d'", $this->db_praefix."plugin_flexslider", $fs_id);
			$temp_return = $this->db->get_row($sql, ARRAY_A);
		}
		return $temp_return;
	}

	/**
	 * flexslider_class::fs_save()
	 *
	 * @return void
	 */
	function fs_save()
	{
		$temp_id = $this->checked->fs_id;
		$temp_wechselbild = $this->checked->wechselbild;
		$temp_text = $this->checked->fs_text;
		$temp_link = $this->checked->fs_link;
		$temp_ueberschrift_1 = $this->checked->fs_ueberschrift_1;
		$temp_ueberschrift_2 = $this->checked->fs_ueberschrift_2;

		if ($temp_wechselbild) {
			$temp_result_array = array ();
			preg_match('/<img (.*?) \/>/is', $temp_wechselbild, $result_array);
			$temp_bild = $result_array[1];

			preg_match('/<a (.*?)>/is', $temp_wechselbild, $result_array);
			$temp_link = $result_array[1];

			if (!$temp_id) {
				$sql = sprintf("INSERT INTO %s SET fs_order_id='999999'", $this->db_praefix."plugin_flexslider");
				$this->db->query($sql);
				$temp_id = $this->db->insert_id;
			}

			$sql = sprintf("UPDATE %s SET fs_menu_id='%d', fs_bild='%s', fs_link='%s', fs_text='%s', fs_ueberschrift_1='%s', fs_ueberschrift_2='%s' WHERE fs_id='%d'",
				$this->db_praefix."plugin_flexslider",
				$this->checked->fs_menu_id,
				$this->db->escape($temp_bild),
				$this->db->escape($temp_link),
				$this->db->escape($temp_text),
				$this->db->escape($temp_ueberschrift_1),
				$this->db->escape($temp_ueberschrift_2),
				$temp_id
			);

			$this->db->query($sql);
			$this->fs_reorder_auto($this->checked->fs_menu_id);
		}
	}

	/**
	 * flexslider_class::fs_reorder_auto()
	 *
	 * @param integer $fs_menu_id
	 * @return void
	 */
	function fs_reorder_auto($fs_menu_id=0)
	{
		$temp_liste = $this->fs_liste($fs_menu_id);

		if (!empty($temp_liste)) {
			$temp_count = 10;

			foreach ($temp_liste as $temp_bild) {
				$sql = sprintf("UPDATE %s SET fs_order_id='%d' WHERE fs_id='%d'",
					$this->db_praefix."plugin_flexslider",
					$temp_count,
					$temp_bild['fs_id']
				);
				$this->db->query($sql);
				$temp_count += 10;
			}
		}
	}
}

$flexslider = new flexslider_class();
