<?php

/**
 * Class Hintergrundbild
 */
#[AllowDynamicProperties]
class Hintergrundbild {
	/**
	 * Hintergrundbild constructor.
	 */
	function __construct()
	{
		global $content, $db, $db_praefix, $checked, $user, $menu;
		$this->content = &$content;
		$this->db = &$db;
		$this->db_praefix = & $db_praefix;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->menu = &$menu;

		if (defined("admin") && $this->user->check_intern()) {
			$this->backend();
		}
		else {
			// $this->frontend();
		}
	}

	/**
	 * Backend
	 */
	private function backend()
	{
		global $template;
		if (strpos("XXX".$template, "hintergrundbild/templates/backend.html")) {
			$this->content->template['plugins']['hintergrundbild']['hgb_menu_id'] =
				isset($this->checked->hgb_menu_id) ? $this->checked->hgb_menu_id : "";
			IfNotSetNull($this->checked->hgb_action);
			$this->backend_template_switcher($this->checked->hgb_action);
		}
	}

	/**
	 * Frontend
	 */
	private function frontend()
	{
		// Bild für aktiven Menüpunkt aus der DB holen
		$image = $this->db_load_hintergrundbild_by_menuid($this->content->template['aktive_menuid']);

		// Wenn kein Bild für akiven Menüpunkt eingetragen, dann nach vererbtem Bild suchen
		if ($image == "" && $this->db_load_vererbung() == 1) {
			$image = $this->suche_vererbtes_image($this->content->template['aktive_menuid']);
		}

		// Wenn kein Bild für akiven Menüpunkt eingetragen, und kein vererbtes Bild gefunden, dann
		// Bild für Grundzustand aus der DB holen
		if ($image == "") {
			$image = $this->db_load_hintergrundbild_by_menuid(0);
		}

		// Wenn Hintergrundbild gesetzt, dann per CSS einbinden
		if ($image != "") {
			global $output;
			$this->output = &$output;
			$css_path = $this->db_load_css_path();
			$image = PAPOO_WEB_PFAD . str_replace("..", "", $image);
			$css= '<style>' . $css_path . ' { background-image: url(' . $image .'); }</style>';
			$this->output = str_replace("</head>", $css . "</head>", $this->output);
		}
	}

	/**
	 * "Controller" für das Backend.
	 *
	 * @param string $action
	 */
	private function backend_template_switcher($action = "")
	{
		switch ($action) {
		case "edit":
			$bilddatei = $this->db_load_hintergrundbild_by_menuid($this->checked->hgb_menu_id);

			// Wenn kein Hintergrundbild zu diesem Menüpunkt, nichts in den Tiny rein schreiben
			if ($bilddatei != "") {
				$this->content->template['plugins']['hintergrundbild']['image_code'] = '<p><img src="' . $bilddatei . '" /></p>';
			}
			else {
				$this->content->template['plugins']['hintergrundbild']['image_code'] = '';
			}

			$this->active_backend_template = "_backend_edit.html";
			break;

		case "save":
			// Speichern
			$this->db_zuordnung_speichern($this->checked->hgb_menu_id, $this->checked->bilddatei);

			// Laden
			$this->load_config_data();

			// Template
			$this->active_backend_template = "_backend_main.html";
			break;

		case "save_config":
			// Speichern
			$this->db_save_css_path($this->checked->css_path);
			$this->db_save_vererbung($this->checked->vererbung);

			// Laden
			$this->load_config_data();

			// Template
			$this->active_backend_template = "_backend_main.html";
			break;

		default:
			// Speichern

			// Laden
			$this->load_config_data();

			// Template
			$this->active_backend_template = "_backend_main.html";
			break;
		}
		$this->content->template['plugins']['hintergrundbild']['active_backend_template'] =
			"../../../plugins/hintergrundbild/templates/" . $this->active_backend_template;
	}

	/**
	 * Lädt alle nötigen Daten aus der Datenbank
	 */
	private function load_config_data()
	{
		$this->content->template['plugins']['hintergrundbild']['vererbung'] = $this->db_load_vererbung();
		$this->content->template['plugins']['hintergrundbild']['css_path'] = $this->db_load_css_path();
		$this->menu_liste();
	}

	/**
	 * Geht im Menübaum alle Elternmenüids der Reihe nach nach oben durch und liefert das erste gefundene Bild;
	 *
	 * @param $menuid
	 * @return mixed|string
	 */
	private function suche_vererbtes_image($menuid)
	{
		$image = "";
		$eltern_menuid = $this->menu->get_parent_menuid($menuid);

		// Wenn wir noch nicht auf der 0-ten ebene sind.
		if ($eltern_menuid != 0) {
			// Bild der Eltern-menuid als Hintergrundbild setzen
			$image = $this->db_load_hintergrundbild_by_menuid($eltern_menuid);

			// Falls die auch noch kein Bild hat, weiter hoch gehen
			if ($image == "") {
				$image = $this->suche_vererbtes_image($eltern_menuid);
			}
		}
		return $image;
	}

	/*********************************************************************
	 * Datenbank - Methoden
	 *********************************************************************/

	/**
	 * generiert den Menübaum
	 */
	private function menu_liste()
	{
		global $menu, $intern_menu, $cms;
		$this->menu = &$menu;
		$this->intern_menu = &$intern_menu;
		$this->cms = &$cms;

		$this->menu->data_front_publish = $this->intern_menu->mak_menu_liste_zugriff();

		if ($this->cms->categories == 1) {
			// Kategorien rausholen
			$this->menu->preset_categories();
			$this->menu->get_menu_cats();
		}

		//Zusammenbauen und die ohne rechte entsprechend triggern
		$this->content->template['menulist_data'] = $this->menu->menu_navigation_build("FRONT_PUBLISH", "CLEAN");

		if ($this->cms->categories == 1) {
			$this->menu->get_menu_cats();
			$this->menu->make_menucats();
			// Kategorien rausholen
			$catlist_data = $this->menu->preset_categories(true);
			$this->content->template['catlist_data'] = $catlist_data;
			$this->menu->get_menu_cats();
			$this->content->template['categories'] = 1;
		}
		else {
			$this->content->template['catlist_data'][0] = array("catid"=>-1);
			$this->content->template['no_categories'] = 1;
		}
		$this->content->template['menulist_data_hintergrundbild'] = $this->content->template['menulist_data'];

		// Für jeden Menüpunkt das entsprechende Hintergrundbild (falls vorhanden) aus der DB laden
		// und in die Template Variable 'menulist_data_hintergrundbild' (Menübaum)
		// eintragen
		$i = 0;
		foreach ($this->content->template['menulist_data_hintergrundbild'] as $menupunkt) {
			$this->content->template['menulist_data_hintergrundbild'][$i]['hintergrundbild'] = $this->db_load_hintergrundbild_by_menuid($menupunkt['menuid']);
			$i++;
		}
		$this->content->template['plugins']['hintergrundbild']['grundzustand'] = $this->db_load_hintergrundbild_by_menuid(0);
	}

	/**
	 * Speichert eine Zuordnung von Bild und Menüpunkt in der Datenbank
	 *
	 * @param $menu_id
	 * @param $bilddatei
	 */
	private function db_zuordnung_speichern($menu_id, $bilddatei)
	{
		preg_match('/src="([^"]*)"/', $bilddatei, $matches);
		$bilddatei = $matches[1];

		// Dieser Menüpunkt hat noch kein Hintergrundbild -> INSERT
		if ($this->db_menu_item_has_image($menu_id) == 0) {
			$sql = sprintf ("INSERT INTO %s (menu_id, bilddatei) VALUES (%u, '%s')",
				$this->db_praefix."plugin_background_lookup_bilder_menuepunkte",
				$menu_id,
				$bilddatei
			);
		}
		// Dieser Menüpunkt hat schon kein Hintergrundbild
		else {
			// Neues Hintergrundbild -> UPDATE
			if ($bilddatei != "") {
				$sql = sprintf ("
                UPDATE %s
                SET bilddatei='%s'
                WHERE menu_id='%u'",

					$this->db_praefix."plugin_background_lookup_bilder_menuepunkte",
					$bilddatei,
					$menu_id
				);
			}
			// Kein Hintergrundbild -> DELETE
			else {
				$sql = sprintf ("
                    DELETE FROM %s
                    WHERE menu_id='%u'",

					$this->db_praefix."plugin_background_lookup_bilder_menuepunkte",
					$menu_id
				);
			}
		}
		$this->db->query($sql);
	}

	/**
	 * Holt die Daten zu einem Menüpunkt aus der Datenbank
	 *
	 * @param $menu_id
	 * @return array
	 */
	private function db_load_data_by_menuid($menu_id)
	{
		$sql = sprintf ("
            SELECT *
            FROM %s
            WHERE menu_id = %u",

			$this->db_praefix."plugin_background_lookup_bilder_menuepunkte",
			$menu_id
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Holt das Hintergrundbild zu einem Menüpunkt aus der Datenbank
	 *
	 * @param $menu_id
	 * @return mixed
	 */
	private function db_load_hintergrundbild_by_menuid($menu_id)
	{
		$result = $this->db_load_data_by_menuid($menu_id);
		IfNotSetNull($result[0]['bilddatei']);
		return $result[0]['bilddatei'];
	}

	/**
	 * Gibt 1 zurück, falls der Menüpunkt schon ein Hintergrundbild hat. Sonst 0.
	 *
	 * @param $menu_id
	 * @return int
	 */
	private function db_menu_item_has_image($menu_id)
	{
		return sizeof($this->db_load_data_by_menuid($menu_id));
	}

	/**
	 * Holt den CSS-Pfad aus der Datenbank
	 *
	 * @return mixed
	 */
	private function db_load_css_path()
	{
		$sql = sprintf ("
            SELECT css_path
            FROM %s
            LIMIT 1",

			$this->db_praefix."plugin_background_config"
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result[0]['css_path'];
	}

	/**
	 * Speichert den CSS-Pfad in der Datenbank
	 *
	 * @param $path
	 */
	private function db_save_css_path($path="body")
	{
		$path = strtolower(strip_tags($path));

		$sql = sprintf ("
            UPDATE %s
            SET css_path = '%s'",

			$this->db_praefix."plugin_background_config",
			$path
		);
		$this->db->query($sql);
	}

	/**
	 * Lädt den Vererbungs-Zustand aus der DB
	 *
	 * @return mixed
	 */
	private function db_load_vererbung()
	{
		$sql = sprintf ("
            SELECT vererbung
            FROM %s
            LIMIT 1",

			$this->db_praefix."plugin_background_config"
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result[0]['vererbung'];
	}

	/**
	 * Speichert den Vererbungs-Zustand aus der DB
	 *
	 * @param string $vererbung
	 */
	private function db_save_vererbung($vererbung)
	{
		if ($vererbung == "") {
			$vererbung = 0;
		}

		$sql = sprintf ("
            UPDATE %s
            SET vererbung = '%u'",

			$this->db_praefix."plugin_background_config",
			$vererbung
		);
		$this->db->query($sql);
	}

	function output_filter()
	{
		$this->frontend();
	}
}

$hintergrundbild = new Hintergrundbild();
