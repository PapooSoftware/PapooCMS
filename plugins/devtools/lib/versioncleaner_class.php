<?php

/**
 * Class versioncleaner_class
 */
class versioncleaner_class
{
	/**
	 * versioncleaner_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $db, $checked, $content, $user;
		$this->db = & $db;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->user = & $user;

		// Aktions-Weiche
		// **************
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();

			global $template;
			if (strpos("XXX".$template, "versioncleaner_backend.html")) {
				IfNotSetNull($this->checked->versioncleaner_action);
				switch ($this->checked->versioncleaner_action) {
				case "clean_aktu":
					$this->clean_aktu_versions();
					$this->content->template['plugin']['devtools']['switch_deleted'] = true;
					break;

				case "clean":
					$this->clean_versions();
					$this->content->template['plugin']['devtools']['switch_deleted'] = true;
					break;
				}
			}
		}
	}

	function clean_aktu_versions()
	{
		// Versionen IDs aller tempor�rer Artikel bestimmen.
		global $db_praefix;
		$sql = sprintf(
			"SELECT version_art_id FROM %s WHERE version_art_fix='0' OR version_art_reporeid<1",
			$db_praefix."papoo_version_article"
		);

		$temp_versionen_ids = $this->db->get_col($sql);

		if (!empty($temp_versionen_ids)) {
			foreach($temp_versionen_ids as $version_id) {
				$sql = sprintf("DELETE FROM %s WHERE version_art_id='%d'", $db_praefix."papoo_version_article", $version_id);
				$this->db->query($sql);

				$sql = sprintf("DELETE FROM %s WHERE versionid='%d'", $db_praefix."papoo_version_language_article", $version_id);
				$this->db->query($sql);

				$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'", $db_praefix."papoo_version_lookup_art_cat", $version_id);
				$this->db->query($sql);

				$sql = sprintf("DELETE FROM %s WHERE version_lookup_article_id='%d'", $db_praefix."papoo_version_lookup_article", $version_id);
				$this->db->query($sql);

				$sql = sprintf("DELETE FROM %s WHERE version_lookup_article_id='%d'", $db_praefix."papoo_version_lookup_write_article", $version_id);
				$this->db->query($sql);

				$sql = sprintf("DELETE FROM %s WHERE versionid='%d'", $db_praefix."papoo_version_repore", $version_id);
				$this->db->query($sql);
			}
		}
	}

	function clean_versions()
	{
		$versionen_tabellen = array("papoo_version_article",
			"papoo_version_language_article",
			"papoo_version_lookup_art_cat",
			"papoo_version_lookup_article",
			"papoo_version_lookup_write_article",
			"papoo_version_repore"
		);

		global $db_praefix;
		if (!empty($versionen_tabellen)) {
			foreach($versionen_tabellen as $tabelle) {
				$sql = sprintf("DELETE FROM %s", $db_praefix.$tabelle);
				$this->db->query($sql);
			}
		}
	}
}

$versioncleaner = new versioncleaner_class();
