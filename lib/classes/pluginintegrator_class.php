<?php

// #####################################
// # CMS Papoo                         #
// # (c) Carsten Euwens 2003           #
// # Authors: Stephan Bergmann         #
// #          aka b.legt               #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 4.2                   #
// #####################################

/**
 * Diese Klasse sorgt für die automatische Einbindung von Plugins
 *
 * Class pluginintegrator_class
 */
class pluginintegrator_class
{
	/**
	 * pluginintegrator_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		global $user;
		$this->user = & $user;
		// das Messages-Objekt einbinden
		global $message;
		$this->message = & $message;

		# Die Sprach-Dateien (Messages) einbinden
		$this->make_messages_integration();
		# Die Klassen-Datei(en) einbinden
		$this->make_classes_integration();
	}

	/**
	 * Bindet die Klassen-Datei(en) der Plugins ein
	 */
	function make_classes_integration()
	{
		if (defined("admin")) {
			global $template;
			$this->user->check_intern();
			if ($template!="login.utf8.html") {
				$ok = "ok";
			}
			else {
				$ok = NULL;
			}
		}
		else {
			$ok="ok";
		}
		if ($ok==="ok") {
			// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
			if (!isset($_SESSION['dbp']['papoo_pluginclass'])){
				$_SESSION['dbp']['papoo_pluginclass'] = NULL;
			}
			// 1. die papoo_pluginclasses-Tabelle nach Einträgen durchsuchen
			$sql = sprintf("SELECT * FROM %s ORDER BY pluginclass_id ASC", $this->cms->papoo_pluginclasses);
			if (!is_array($_SESSION['dbp']['papoo_pluginclass'])) {
				$result = $this->db->get_results($sql);
				$_SESSION['dbp']['papoo_pluginclass']=$result;
			}
			else {
				$result=$_SESSION['dbp']['papoo_pluginclass'];
			}

			if ($result) {
				foreach ($result as $klasse) {
					// 2. die Klassen-Datei einbinden
					if (file_exists(PAPOO_ABS_PFAD."/plugins/".$klasse->pluginclass_datei)) {
						// !!! der Pfad ist alles andere als sauber !!!
						require_once(PAPOO_ABS_PFAD . "/plugins/" . $klasse->pluginclass_datei);

						// 3. Objekt der Klasse globalisieren
						if (empty(${$klasse->pluginclass_name})) ${$klasse->pluginclass_name}="";

						$temp = ${$klasse->pluginclass_name};
						global ${$klasse->pluginclass_name};
						${ $klasse->pluginclass_name } = $temp;
					}
				}
			}
		}
	}

	/**
	 * Bindet die Sprach-Datei (Messages) des Plugins ein
	 */
	function make_messages_integration()
	{
		// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
		if (!isset($_SESSION['dbp']['papoo_plugins'])){
			$_SESSION['dbp']['papoo_plugins'] = NULL;
		}
		// 1. die papoo_plugins-Tabelle nach Einträgen durchsuchen
		$sql = sprintf("SELECT * FROM %s", $this->cms->papoo_plugins);
		if (!is_array($_SESSION['dbp']['papoo_plugins'])) {
			$result = $this->db->get_results($sql);
			$_SESSION['dbp']['papoo_plugins'] = $result;
		}
		else {
			$result=$_SESSION['dbp']['papoo_plugins'];
		}

		if ($result) {
			foreach ($result as $plugin) {
				// 2. Die Messages-Dateien einbinden (wenn vorhanden)
				if (!empty($plugin->plugin_messages)){
					$this->message->einbinden("/plugins/".$plugin->plugin_messages);
				}
			}
		}
	}

	/**
	 * Existiert eine Methode "post_papoo()" wird diese ausgeführt. So ist es z.B. möglich von papoo gesetzte
	 * Template-Variablen über &smarty->_tpl_vars[] nachträglich noch zu beeinflussen.
	 */
	function post_papoo()
	{
		// Setze Variable auf NULL falls nicht existent, zwecks Behebung von Warnungen
		if (!isset($_SESSION['dbp']['papoo_pluginclasses2'])) {
			$_SESSION['dbp']['papoo_pluginclasses2'] = NULL;
		}
		// 1. die papoo_pluginclasses-Tabelle nach Einträgen durchsuchen
		$sql = sprintf("SELECT * FROM %s", $this->cms->papoo_pluginclasses);
		if (!is_array($_SESSION['dbp']['papoo_pluginclasses2'])) {
			$result = $this->db->get_results($sql);
			$_SESSION['dbp']['papoo_pluginclasses2']=$result;
		}
		else {
			$result=$_SESSION['dbp']['papoo_pluginclasses2'];
		}


		if ($result) {
			foreach ($result as $klasse) {
				$name = $klasse->pluginclass_name;
				global ${ $name };
				$plugin_klasse = & ${$name};
				// 2. Test auf function post_papoo()
				$methoden_array = get_class_methods($plugin_klasse);
				if (!empty($methoden_array)) {
					if (in_array("post_papoo", $methoden_array)) {
						// FIXME: Funktion existiert nicht
						$plugin_klasse->post_papoo();
					}
				}

			}
		}
	}

	/**
	 * output_filter()
	 *
	 * @return void
	 */
	public function output_filter()
	{
		// 1. die papoo_pluginclasses-Tabelle nach Einträgen durchsuchen
		$sql = sprintf("SELECT * FROM %s ORDER BY pluginclass_id ASC", $this->cms->papoo_pluginclasses);
		$result = $this->db->get_results($sql);

		if ($result) {
			foreach ($result as $klasse) {
				$name = $klasse->pluginclass_name;
				global ${ $name };
				$plugin_klasse = & ${$name};

				// 2. Test auf function post_papoo()
				$methoden_array = get_class_methods($plugin_klasse);
				if (!empty($methoden_array)) {
					if (in_array("output_filter", $methoden_array)) {
						// FIXME: Funktion existiert nicht
						$plugin_klasse->output_filter();
					}
				}
			}
		}
	}

	/**
	 * output_filter_admin()
	 *
	 * @return void
	 */
	public function output_filter_admin()
	{
		// 1. die papoo_pluginclasses-Tabelle nach Einträgen durchsuchen
		$sql = sprintf("SELECT * FROM %s", $this->cms->papoo_pluginclasses);
		if (!is_array($_SESSION['dbp']['papoo_pluginclasses2'])) {
			$result = $this->db->get_results($sql);
			$_SESSION['dbp']['papoo_pluginclasses2']=$result;
		}
		else {
			$result=$_SESSION['dbp']['papoo_pluginclasses2'];
		}


		if ($result) {
			foreach ($result as $klasse) {
				$name = $klasse->pluginclass_name;
				global ${ $name };
				$plugin_klasse = & ${$name};

				// 2. Test auf function post_papoo()
				$methoden_array = get_class_methods($plugin_klasse);
				if (!empty($methoden_array)) {
					if (in_array("output_filter_admin", $methoden_array)) {
						// FIXME: Funktion existiert nicht
						$plugin_klasse->output_filter_admin();
					}
				}
			}
		}
	}
}

$pluginintegrator = new pluginintegrator_class();