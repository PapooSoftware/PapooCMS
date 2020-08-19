<?php

/**
 * Class dbcleanup_class
 */
class dbcleanup_class
{
	/**
	 * dbcleanup_class constructor.
	 */
	function __construct()
	{
		global $db, $db_praefix, $cms, $user, $checked, $content, $plugin, $dumpnrestore;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->plugin = & $plugin;
		$this->dumpnrestore = & $dumpnrestore;

		if (defined("admin")) {
			$user->check_intern();

			global $template;
			if (strpos("XXX" . $template, "dbcleanup_backend.html")) {
				IfNotSetNull($this->checked->dbcleanup_action);
				switch ($this->checked->dbcleanup_action) {
				case "run":
					$this->content->template['plugin']['devtools']['template_weiche'] = "RUN";
					$this->delete_unused_plugin_tables();
					break;

				case "":
				default;
					$this->content->template['plugin']['devtools']['template_weiche'] = "START";
					break;
				}
			}
		}
	}

	public function delete_unused_plugin_tables()
	{
		$this->plugin->read_installed();
		$this->plugin->read_lokal();
		$this->plugin->make_content();
		$this->plugin->read_lokal();

		$sql_files = $this->get_all_sql_files();
		$installed_plugins = $this->get_installed_plugins();

		foreach ($installed_plugins as $installed_plugin) {
			unset ($sql_files[$installed_plugin]);
		}

		foreach ($sql_files as $sql_file) {
			$this->dumpnrestore->restore('../plugins/' . $sql_file);
		}
	}

	/**
	 * Liefert eine Liste von allen Plugin-SQL-Deinstall-Dateien, die vorhanden sind
	 *
	 * @return array
	 */
	public function get_all_sql_files()
	{
		$all_plugins = $this->plugin->plugin_lokal;
		$deinstall_files = array();
		foreach ($all_plugins as $plugin) {
			$plugin_name = $plugin['plugin'][0]['name'][0]['cdata'];
			$sql_file = $plugin['plugin'][0]['datenbank'][0]['deinstallation'][0]['cdata'];
			if ($sql_file != "") {
				$deinstall_files[$plugin_name] = $plugin['plugin'][0]['datenbank'][0]['deinstallation'][0]['cdata'];
			}
		}
		ksort($deinstall_files);
		return $deinstall_files;
	}

	/**
	 * @return array
	 */
	public function get_installed_plugins()
	{
		$all_plugins = $this->plugin->plugin_content;

		$installed_plugins = array();
		foreach ($all_plugins as $k => $v) {
			if( $v['switch_installed'] == 1 ) {
				$installed_plugins[] = $v['name'];
			}
		}
		return $installed_plugins;
	}
}

$dbcleanup = new dbcleanup_class();
