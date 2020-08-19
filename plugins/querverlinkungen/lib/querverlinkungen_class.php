<?php

require_once (__DIR__) . "/Read.php";
require_once (__DIR__) . "/Write.php";

/**
 * Hauptdatei für das querverlinkungen-Plugin.
 *
 * @author Martin Güthler <mg@papoo.de>
 */

/**
 * class querverlinkungen_class
 *
 * Hauptklasse des Plugins.
 *
 * @author Martin Güthler <mg@papoo.de>
 */
class querverlinkungen_class
{
	const PREFIX = 'querverlin';

	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content, $sitemap, $cms;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->sitemap = &$sitemap;
		$this->sitemap->make_sitemap();
		$this->cms = &$cms;

		$this->read = new Read();
		$this->write = new Write();

		if (defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . '/plugins/', '', $template);
			$template2 = basename($template2);

			if ($template != 'login.utf8.html') {
				if (stristr($template2, "querverlinkungen") or stristr($template2,
						"artikelartikel") or stristr($template2, "menpunktemenpunkte")
				) {
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD . '/plugins/querverlinkungen/css';
					if (stristr($template2, "artikelartikel_backend.html")) {
						$this->backend_artikel_artikel();
					}
					if (stristr($template2, "menpunktemenpunkte_backend.html")) {
						$this->backend_menuepunkte_menuepunkte();
					}
				}
			}
		}
		else {
			$this->frontend();
		}
	}

	private function backend_artikel_artikel()
	{
		$this->write->artikel_artikel_zuordnungen();
		$zuordnungen = $this->read->artikel_artikel_zuordnungen();

		$alle_lesbaren_artikel = $this->read->readable_articles();
		$alle_schreibbaren_artikel_ids = $this->read->writable_articles();

		$select_fields = array();
		foreach ($alle_schreibbaren_artikel_ids as $artikel_id) {
			$select = '<select data-placeholder="Artikel auswählen..." class="chosen" id="artikel_artikel_' . $artikel_id . '" name="artikel_artikel_' . $artikel_id . '[]" multiple>';
			foreach ($alle_lesbaren_artikel as $artikel) {
				$select .= "<option";
				foreach ($zuordnungen as $zuordnung) {
					if ($zuordnung['origin_id'] == $artikel_id && $artikel['id'] == $zuordnung['target_id']) {
						$select .= " selected";
					}
				}
				$select .= ">" . $artikel['name'] . " [ID: " . $artikel['id'] . "]";
				$select .= "</option>";
			}

			$select .= "</select>";

			$select_fields[$artikel_id] = $select;
		}
		$this->content->template['plugin_querverlinkungen']['artikel_artikel_zuordnungen']['select'] = $select_fields;
	}

	private function backend_menuepunkte_menuepunkte()
	{
		$this->write->menuepunkte_menuepunkte_zuordnungen();
		$zuordnungen = $this->read->menuepunkte_menuepunkte_zuordnungen();

		$alle_lesbaren_menuepunkte = $this->read->readable_menuepunkte();
		$alle_schreibbaren_menuepunkte_ids = $this->read->writable_menuepunkte();

		$select_fields = array();
		foreach ($alle_schreibbaren_menuepunkte_ids as $menuepunkt_id) {
			$select = '<select data-placeholder="Menüpunkt auswählen..." class="chosen" id="menuepunkte_menuepunkte_' . $menuepunkt_id . '" name="menuepunkte_menuepunkte_' . $menuepunkt_id . '[]" multiple>';
			foreach ($alle_lesbaren_menuepunkte as $menuepunkt) {
				$select .= "<option";
				foreach ($zuordnungen as $zuordnung) {
					if ($zuordnung['origin_id'] == $menuepunkt_id && $menuepunkt['id'] == $zuordnung['target_id']) {
						$select .= " selected";
					}
				}
				$select .= ">" . $menuepunkt['name'] . " [ID: " . $menuepunkt['id'] . "]";
				$select .= "</option>";
			}

			$select .= "</select>";

			$select_fields[$menuepunkt_id] = $select;
		}
		$this->content->template['plugin_querverlinkungen']['menuepunkte_menuepunkte_zuordnungen']['select'] = $select_fields;
	}

	public function frontend()
	{
		$artikel_id = $this->checked->reporeid;
		$zuordnungen = $this->read->artikel_artikel_zuordnungen_by_origin_id($artikel_id);

		foreach ($zuordnungen as $zuordnung) {
			$this->content->template['plugin_querverlinkungen']['verlinkte_artikel'][] = $this->read->article_data($zuordnung['target_id']);
		}

		$menu_id = $this->checked->menuid;
		$zuordnungen = $this->read->menuepunkt_menuepunkt_zuordnungen_by_origin_id($menu_id);

		foreach ($zuordnungen as $zuordnung) {
			$this->content->template['plugin_querverlinkungen']['verlinkte_menuepunkte'][] = $this->read->menuepunkt_data($zuordnung['target_id']);
		}
	}
}

$querverlinkungen = new querverlinkungen_class();
