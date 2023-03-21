<?php
/**
//#####################################
//# CMS Papoo                	#
//# (c) Dr. Carsten Euwens 2008       #
//# Authors: Carsten Euwens           #
//# http://www.papoo.de               #
//#                                   #
//# Class: intern_plugin              #
//# (c) Stephan Bergmann              #
//#                                   #
//#####################################
//# PHP Version >4.2                  #
//#####################################
 */

/**
 * Die Klasse intern_plugin regelt die Installation und De-Installation von papoo-Plugins.
 *
 * Sie scannt nach Plugin-XML-Dateien im Plugin-Verzeichniss und vergleicht diese
 * Informationen mit den installierten Plugins.
 *
 * Als zusätzliches Feature kann es "offizielle" papoo-Plugins mit den Informationen
 * auf dem papoo-Plugin-Server vergleichen. So kann kontrolliert werden, ob für dieses
 * Plugin ein Update vorliegt.
 *
 * Class intern_plugin
 */
#[AllowDynamicProperties]
class intern_plugin
{
	/** @var string Adresse des papoo-Plugin-Servers */
	var $papoo_plugin_server = "http://www.papoo.de/plugin2.xml";
	/** @var string Test-Adresse zu lokaler RSS-Feed-Datei für Testzwecke */
	//var $papoo_plugin_server = "http://localhost/papoo_plugins.xml";

	/** @var string lokales Präfix der DB-Tabellen */
	var $db_praefix;

	/** @var array Daten der lokalen Plugins */
	var $plugin_lokal = array();
	/** @var array Daten installierter Plugins */
	var $plugin_installed = array();
	/** @var array Daten der Plugins auf papoo-Plugin-Server */
	var $plugin_papoo = array();
	/** @var array "normalisierte" Daten der Plugins (z.B. für die Ausgabe im Backend) */
	var $plugin_content = array();

	/** @var array ? */
	var $sprachen = array();

	/**
	 * intern_plugin constructor.
	 */
	function __construct()
	{
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;

		// globales DB-Tabellen-Präfix
		global $db_praefix;
		$this->db_praefix = $db_praefix;

		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;

		// User Klasse einbinden
		global $user;
		$this->user = & $user;

		// checked Klasse einbinden
		global $checked;
		$this->checked = & $checked;

		// dumpnrestore Klasse einbinden
		global $dumpnrestore;
		$this->dumpnrestore = & $dumpnrestore;

		// menuintcss Klasse einbinden
		global $menuintcss;
		$this->menuintcss = & $menuintcss;

		// pluginscss Klasse einbinden
		global $pluginscss;
		$this->pluginscss = & $pluginscss;

		// Diverse-Klasse
		global $diverse;
		$this->diverse = & $diverse;

		// Mitgliederverwaltungs-Klasse
		global $mv;
		$this->mv = & $mv;
	}

	/**
	 * @desc Liefert eine Liste von Plugins, die in der Datenbank mit einer älteren Versionsnummer
	 * eingetragen sind als tatsächlich auf dem Server vorhanden ist.
	 * Das kann zum Beispiel passieren, wenn man ein neueres Plugin einfach per FTP drüberbügelt.
	 *
	 * @return array
	 */
	function vergleiche_plugins()
	{
		// Von den installierten Plugins den Namen und die Versionsnummer holen
		$this->read_installed();
		$installierte_plugins = array();
		$index = 0;
		if (!empty($this->plugin_installed)) {
			foreach ($this->plugin_installed as $installiertes_plugin) {
				$installierte_plugins[$index]['name']    = $installiertes_plugin['plugin_name'];
				$installierte_plugins[$index]['version'] = $installiertes_plugin['plugin_version'];
				$installierte_plugins[$index]['id'] = $installiertes_plugin['plugin_id'];
				$index++;
			}
		}

		// Von den im Verzeichnis /plugins liegenden Plugins den Namen und die Versionsnummer holen		
		$this->read_lokal();
		$lokale_plugins = array();
		$index = 0;
		foreach ($this->plugin_lokal as $lokales_plugin) {
			$lokale_plugins[$index]['name']    = $lokales_plugin['plugin'][0]['name'][0]['cdata'];
			$lokale_plugins[$index]['version'] = $lokales_plugin['plugin'][0]['version'][0]['cdata'];
			$index++;
		}
		// Überprüfen, ob es ein lokales Plugin gibt, das gleichen Namen,
		// aber neuere Versionsnummer wie ein installiertes hat.
		$konflikt_plugins = array();
		$index = 0;
		foreach ($installierte_plugins as $installiertes_plugin) {
			foreach ($lokale_plugins as $lokales_plugin) {
				if ($installiertes_plugin['name'] == $lokales_plugin['name'] && $installiertes_plugin['version'] != $lokales_plugin['version']) {
					$konflikt_plugins[$index]['name']    = $lokales_plugin['name'];
					$konflikt_plugins[$index]['version'] = $lokales_plugin['version'];
					$konflikt_plugins[$index]['id']      = $installiertes_plugin['id'];
					$index++;
				}
			}
		}
		return $konflikt_plugins;
	}


	/**
	 * Erneuert die Versionsnummer eines Plugins in der Datenbank.
	 *
	 * @param $new_version
	 * @param int $plugin_id
	 * @return bool
	 */
	function update_plugin_version($new_version, $plugin_id = 0)
	{
		$erfolg = TRUE;
		if ($plugin_id) {
			// 1. Einträge aus Tabelle papoo_plugins löschen
			$sql = sprintf("UPDATE %s SET %s='%s' WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				'plugin_version',
				$new_version,
				$plugin_id
			);
			$result = $this->db->get_results($sql);

			if (!$result) {
				$erfolg = FALSE;
			}
		}
		$_SESSION['dbp']=array();
		return $erfolg;
	}

	/**
	 *
	 */
	function make_plugin()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		if ($this->checked->menuid == "47") {
			//Überprüfen, ob "doppelte" Plugins gibt und ggf. korrigieren.
			$konflikte = $this->vergleiche_plugins();
			if (!empty($konflikte)) {
				foreach ($konflikte as $konflikt) {
					$this->update_plugin_version($konflikt['version'], $konflikt['id']);
				}
			}

			$this->read_sprachen();

			$this->read_installed();

			$this->read_lokal();

			if (empty($this->checked->plugin_action)) {
				$this->checked->plugin_action="";
			}

			switch ($this->checked->plugin_action) {
			case "uninstall_fragen":
				$this->content->template['plugins_templateweiche'] = "LOESCHEN";
				$this->uninstall_fragen($this->checked->plugin_id);
				break;

			case "uninstall":
				if (isset($this->checked->ABBRECHEN) && $this->checked->ABBRECHEN) {
					$this->content->template['plugins_templateweiche'] = "LISTE";
					$this->make_content();
				}
				else {
					$_SESSION['dbp']=array();
					$this->uninstall($this->checked->plugin_id);
				}
				break;

			case "install":
				$_SESSION['dbp']=array();
				$this->install($this->checked->plugin_identifier);

				break;

			case "check_update":
				$this->read_papoo($this->papoo_plugin_server, $this->checked->plugin_papoo_id);
				//print_r($this->plugin_papoo);
				break;

			case "":
			default:
				$this->content->template['plugins_templateweiche'] = "LISTE";
				$this->make_content();
				break;
			}
		}
	}

	/**
	 * Liest Liste aller installierten Sprachn in "$this->sprachen" ein.
	 */
	function read_sprachen()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->papoo_name_language);
		$this->sprachen = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Liest Daten aller installierten Plugins in "$this->plugin_installed" ein.
	 */
	function read_installed()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->papoo_plugins);
		$this->plugin_installed = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Es wird das lokale Plugins-Verzeichniss nach vorhandenen XML-Dateien durchsucht.
	 * Wenn vorhanden werden die Daten in "$this->plugin_lokal" eingelesen.
	 */
	function read_lokal()
	{
		//  Rückgabewert erstmal leeren, sonst ist bei mehrmaligem Aufruf jeder Eintrag doppelt vorhanden
		$this->plugin_lokal = null;

		if (empty($this->checked->messageget)) {
			$this->checked->messageget="";
		}

		if ($this->checked->messageget == 400) {
			$this->content->content_text = $this->content->template['message_400'];
		}

		$handle = opendir('../plugins');

		if (get_resource_type($handle)==='stream') {
			while (false !== ($file = readdir($handle))) {
				if (is_dir(PAPOO_ABS_PFAD . "/plugins/" . $file) && !strstr($file, '.')) {
					#echo "HIER";
					global $xmlparser;
					$xml = $xmlparser;
					// Liegt hier ein Verzeichnis ohne XML-Datei, wird das Plugin doppelt installiert
					// der xml-Parser liefert dann den letzten Wert erneut
					if (file_exists(PAPOO_ABS_PFAD . "/plugins/" . $file . "/" . $file . ".xml")) {
						$xml->parse(PAPOO_ABS_PFAD . "/plugins/" . $file . "/" . $file . ".xml");
						$xml_array = $xml->xml_data;
						$this->plugin_lokal[] = $xml_array;
					}
				}
			}
			closedir($handle);
		}
	}

	/**
	 * Es wird der papoo-Plugins-Server nach Plugins durchsucht.
	 * Wenn vorhanden werden die Daten in "$this->plugin_papoo" eingelesen.
	 *
	 * @param string $url
	 * @param int $plugin_papoo_id
	 */
	function read_papoo($url = "", $plugin_papoo_id = 0)
	{
		if ($url) {
			require_once('rss_fetch.inc.php'); // todo !!! Einbindung ist nicht optimal !!!
			$rss = @fetch_rss($url);
			if (!empty($rss)) {
				foreach ($rss->items as $items) {
					// nur Plugin mit papoo_plugin_id = $plugin_id in Array schreiben
					if ($items['dc']['pl_papoo_id'] == $plugin_papoo_id) {
						$this->plugin_papoo[] = $items;
					}
				}
			}
		}
	}

	/**
	 * Installations-Routine
	 *
	 * @param $plugin_identifier
	 */
	function install($plugin_identifier)
	{
		if (isset($this->plugin_lokal) && $this->plugin_lokal) {
			foreach($this->plugin_lokal as $plugin) {
				$identifier = $this->diverse->sicherer_dateiname($plugin['plugin'][0]['name'][0]['cdata']."_".$plugin['plugin'][0]['version'][0]['cdata']);

				if ($identifier == $plugin_identifier) {
					// .. das ist also das zu installierende Plugin
					// 1. Plugin in Tabelle papoo_plugin eintragen
					if (!isset($plugin['plugin'][0]['mtop'][0]['cdata']) ||
						isset($plugin['plugin'][0]['mtop'][0]['cdata']) && $plugin['plugin'][0]['mtop'][0]['cdata'] != 1
					) {
						$parentx = 54;
					}
					else {
						$parentx = Null;
					}

					$plugin_id = $this->make_plugin_entry($plugin['plugin'][0]);

					// 2. Menü-Einträge vornehmen
					if (isset($plugin['plugin'][0]['menue']) && $plugin['plugin'][0]['menue']) {
						foreach($plugin['plugin'][0]['menue'] as $menue) {
							// 2.A Hauptmenü-Punkt anlegen
							IfNotSetNull($plugin['plugin'][0]['mtop'][0]['cdata']);
							$parent_id = $this->make_menue($menue, $plugin_id,$parentx,$plugin['plugin'][0]['mtop'][0]['cdata']);

							// 2.B Wenn Submenü-Punkte vorhanden, diese auch anlegen
							if (isset($menue['submenue']) && $menue['submenue']) {
								foreach($menue['submenue'] as $submenue) {
									$parent_id2=$this->make_menue($submenue, $plugin_id, $parent_id,$plugin['plugin'][0]['mtop'][0]['cdata']);
									if (isset($submenue['submenue2']) && $submenue['submenue2']) {
										foreach($submenue['submenue2'] as $submenue2) {
											$this->make_menue($submenue2, $plugin_id, $parent_id2,0);
										}
									}
								}
							}
						}
					}
					// 3. DB-Tabelle des Plugins installieren einbinden
					// !!!
					if (isset($plugin['plugin'][0]['datenbank'][0]['installation']) && $plugin['plugin'][0]['datenbank'][0]['installation']) {
						$this->dumpnrestore->restore('../plugins/'.$plugin['plugin'][0]['datenbank'][0]['installation'][0]['cdata']);
					}

					// 4. Klasse(n) einbinden
					if (isset($plugin['plugin'][0]['klasse']) && $plugin['plugin'][0]['klasse']) {
						foreach($plugin['plugin'][0]['klasse'] as $klasse) {
							$this->make_pluginclass_entry($klasse, $plugin_id);
						}
					}

					// 5. Modul(e) einbinden
					if (isset($plugin['plugin'][0]['modul']) && $plugin['plugin'][0]['modul']) {
						foreach($plugin['plugin'][0]['modul'] as $modul) {
							$this->make_modul_entry($modul, $plugin_id);
						}
					}
				}
				$plugin['plugin'][0]['mtop'][0]['cdata']=0;
			}
		}

		// CSS-Datei für Backend-Menü neu erstellen
		$this->menuintcss->make_menuintcss();

		// PluginsCSS-Klasse einbinden
		require_once(PAPOO_ABS_PFAD."/lib/classes/pluginscss_class.php");
		$this->pluginscss = new pluginscss_class();

		// CSS-Datei für Plugins-CSS neu erstellen
		$this->pluginscss->make_pluginscss();
		IfNotSetNull($_SESSION['devtool_debug_install']);
		IfNotSetNull($_SESSION['debug_stopallredirect']);
		if ($_SESSION['devtool_debug_install'] OR $_SESSION['debug_stopallredirect']) {
			echo '<a href="./plugins.php?menuid=47&feed_id=1&messageget=399">Weiter</a>';
		}
		else {
			header("Location: ./plugins.php?menuid=47&feed_id=1&messageget=399");
		}
		exit;
	}

	/**
	 * @param $plugin_id
	 */
	function uninstall_fragen($plugin_id)
	{
		// 1. Sprach-ID feststellen
		/*
		$language = $this->cms->lang_backend;
		$query = sprintf("SELECT lang_id FROM %s WHERE lang_short='%s' LIMIT 1",
		$this->cms->papoo_name_language,
		$language
		);
		$result = $this->db->get_results($query, ARRAY_A);
		$lang_id = $result[0]['lang_id'];
		*/
		$lang_id = $this->cms->lang_back_id;
		// 2. Sprach-Informationen des Plugins laden
		$query = sprintf("SELECT T1.plugin_id, T1.plugin_name, T1.plugin_version, T2.pluginlang_beschreibung
							FROM %s as T1, %s as T2
							WHERE
							T1.plugin_id = '%d' AND
							T2.pluginlang_lang_id='%d' AND T2.pluginlang_plugin_id=T1.plugin_id LIMIT 1",
			$this->cms->papoo_plugins,
			$this->cms->papoo_plugin_language,
			$plugin_id,
			$lang_id
		);

		$plugin = $this->db->get_results($query, ARRAY_A);
		if (!empty($plugin)) {
			$plugin = $plugin[0];
		}
		$this->content->template['plugin'] = $plugin;
	}

	/**
	 * DeInstallations-Routine
	 *
	 * @param $plugin_id
	 */
	function uninstall($plugin_id)
	{
		$this->delete_menue($plugin_id);
		$this->delete_modul_entry($plugin_id);
		$this->delete_dbtables($plugin_id);
		if (isset($this->mv->is_mv_installed) && $this->mv->is_mv_installed) {
			$this->delete_mv_dbtables($plugin_id);
		}
		$this->delete_plugin_entry($plugin_id);
		$this->delete_pluginclass_entry($plugin_id);

		// CSS-Datei für Backend-Menü neu erstellen
		$this->menuintcss->make_menuintcss();
		// PluginsCSS-Klasse einbinden
		require_once(PAPOO_ABS_PFAD."/lib/classes/pluginscss_class.php");
		$this->pluginscss = new pluginscss_class();

		// CSS-Datei für Plugins-CSS neu erstellen
		$this->pluginscss->make_pluginscss();

		IfNotSetNull($_SESSION['devtool_debug_install']);
		IfNotSetNull($_SESSION['debug_stopallredirect']);
		if ($_SESSION['devtool_debug_install'] OR $_SESSION['debug_stopallredirect']) {
			echo '<a href="./plugins.php?menuid=47">Weiter</a>';
		}
		else {
			header("Location: ./plugins.php?menuid=47");
		}
		exit;
	}

	/**
	 * Löscht alle Content-Einträge des Plugins mit der ID $plugin_id durch Entfernen der Einträge in der Tabelle papoo_plugincontent
	 *
	 * @param $plugin_id
	 */
	function delete_dbtables($plugin_id)
	{
		// 1. Datei-Name der DeInstallations-Datei aus Tabelle papoo_plugins auslesen
		$sql = sprintf("SELECT plugin_db_deinstall FROM %s WHERE plugin_id='%d' LIMIT 1",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result = $this->db->get_results($sql);
		$deinstall_file = isset($result[0]->plugin_db_deinstall) ? $result[0]->plugin_db_deinstall : NULL;

		// 2. DeInstallations-Datei auf Datenbank anwenden
		$this->dumpnrestore->restore('../plugins/'.$deinstall_file);
	}

	/**
	 * Löscht alle vorhandenen MV-Tabellen
	 *
	 * @param $plugin_id
	 */
	function delete_mv_dbtables($plugin_id)
	{
		$sql = sprintf("SELECT plugin_papoo_id 
							FROM %s 
							WHERE plugin_id = '%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result = $this->db->get_var($sql);#echo "a=$result $sql";exit;
		// Flexverwaltung reserved ID. Flex soll deinstalliert werden
		if ($result == 35) {
			global $db_praefix;
			foreach ($this->cms->tbname AS $key => $value) {
				$tb = substr($value, strlen($db_praefix)); // Präfix eleminieren
				$arr = explode("_", $tb);
				// Filter: Nur die zur MV gehörenden Tabellen ermitteln, beginnen alle mit papoo_mv
				if (($arr[0] == "papoo"
					AND substr($arr[1], 0, 2) == "mv")
				) {
					$tables[$key] = $key;
				}
			}
			// $tables enthält nun die zu löschenden Tabellennamen
			if (count($tables)) {
				foreach ($tables as $key => $value) {
					$sql = sprintf("DROP TABLE IF EXISTS " . $this->cms->tbname[$tables[$key]]);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Trägt das Plugin in die Tabellen papoo_plugins und papoo_plugin_language ein
	 *
	 * @param array $plugin_array
	 * @return int plugin_id des erstellten Eintrags, bzw. 0 bei Fehler
	 */
	function make_plugin_entry($plugin_array = array())
	{
		$plugin_id = 0;
		if ($plugin_array) {
			//print_r($plugin_array);
			// 1. Daten in Tabelle papoo_plugins eintragen
			$sql = sprintf("INSERT INTO %s
							SET plugin_name='%s', plugin_version='%s', plugin_papoo_id='%d',
							plugin_menuids='%s', plugin_css='%s', plugin_db_deinstall='%s',
							plugin_messages='%s'",
				$this->cms->papoo_plugins,
				$this->db->escape($plugin_array['name'][0]['cdata']),
				$this->db->escape($plugin_array['version'][0]['cdata']),
				$plugin_array['papooid'][0]['cdata'],
				"",
				$this->db->escape($plugin_array['css'][0]['cdata']),
				$this->db->escape($plugin_array['datenbank'][0]['deinstallation'][0]['cdata']),
				$this->db->escape($plugin_array['messages'][0]['cdata'])
			);
			$this->db->query($sql);

			// 2. plugin_id des neuen Eintrags ermitteln
			$sql = sprintf("SELECT LAST_INSERT_ID() AS plugin_id FROM %s",
				$this->cms->papoo_plugins
			);
			$result_array = $this->db->get_results($sql, ARRAY_A);
			$plugin_id = $result_array[0]['plugin_id'];

			// 3. Daten in Tabelle papoo_plugin_language eintragen
			if (!empty($this->sprachen)) {
				foreach ($this->sprachen as $sprache) {
					// 3.a Beschreibungs-Text auslesen
					$beschreibung = $this->get_text_fallback($plugin_array, "beschreibung", $sprache['lang_short']);
					// 3.b Beschreibung in die Tabelle eintragen
					$sql = sprintf("INSERT INTO %s SET pluginlang_lang_id='%d', pluginlang_plugin_id='%d', pluginlang_beschreibung='%s'",
						$this->cms->papoo_plugin_language,
						$sprache['lang_id'],
						$plugin_id,
						$this->db->escape($beschreibung)
					);
					$this->db->query($sql);
				}
			}
		}
		return $plugin_id;
	}

	/**
	 * Löscht das Plugin mit der ID "$plugin_id" aus der Tabelle papoo_plugins
	 *
	 * @param int $plugin_id
	 * @return bool TRUE bei Erfolg, FALSE bei Fehler
	 */
	function delete_plugin_entry($plugin_id = 0)
	{
		$erfolg = TRUE;
		if ($plugin_id) {
			// 1. Einträge aus Tabelle papoo_plugins löschen
			$sql = sprintf("DELETE FROM %s WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				$plugin_id
			);
			$result = $this->db->get_results($sql);
			if (!$result) {
				$erfolg = FALSE;
			}

			// 2. Einträge aus Tabelle papoo_plugin_language löschen
			$sql = sprintf("DELETE FROM %s WHERE pluginlang_plugin_id='%d'",
				$this->cms->papoo_plugin_language,
				$plugin_id
			);
			$result = $this->db->get_results($sql);
			if (!$result) {
				$erfolg = FALSE;
			}
		}
		$_SESSION['dbp']=array();

		return $erfolg;
	}

	/**
	 * Erzeugt einen Menue-Eintrag im Backend-Menue anhand der übergebenen Menue-Informationen.
	 * Wird der optionale Paramter $parent mit übergeben, so handelt es sich um einen Unter-Menü-Punkt.
	 *
	 * @param array $menue_array
	 * @param $plugin_id
	 * @param int $parent
	 * @param int $top
	 * @return int Menue_id des erstellten Menü-Punktes.
	 */
	function make_menue($menue_array = array(), $plugin_id, $parent = 54, $top = 0)
	{
		// 1. Menue_ID und Level des neuen Menü-Punktes ermitteln
		// !!! Einschränkung auf ID-Bereich 1000..2000, könnte evtl. zu Problemen führen !!!

		$sql = sprintf("SELECT MAX(menuid) FROM %s WHERE menuid<2000 AND menuid>999", $this->cms->papoo_menu_int);
		$result_array = $this->db->get_results($sql, ARRAY_A);
		$menuid = $result_array[0]['MAX(menuid)']; // sieht seltsam aus.. ist es auch

		// PlugIns beginnen ab der id 1000
		if ($menuid < 1000) {
			$menuid = 1000;
		}
		else {
			$menuid += 1;
		}

		$level = 1;
		if ($parent) {
			if (($top!=1)) {
				$level = 2;
			}

			if ($parent==54) {
				if (($top!=1)) {
					$level = 1;
				}
			}
		}
		// geht also nur bis zu einer Tiefe von 1, sprich Unter-Unter-Menüs gehen nicht !!
		else {
			$level = 0;
		}

		// 2. Menü-Punkt in Tabellen eintragen
		// 2.A Menü-Punkt in Tabelle papoo_menuint eintragen
		//$sql = sprintf("INSERT INTO %s SET menuid='%d', menuname='%s', menulink='%s', menutitel='%s', untermenuzu='%d', level='%d', lese_rechte='%s', schreibrechte='%s', intranet_yn='%d', menu_icon='%s'",
		$temp_order_id = $menuid + 1 - 1000;
		$temp_order_id = $temp_order_id * 10 + 1000;
		IfNotSetNull($menue_array['icon'][0]['cdata']);

		$sql = sprintf("INSERT INTO %s
						SET menuid='%d', menuname='%s', menulink='%s', menutitel='%s', order_id='%s',
						untermenuzu='%d', level='%d', lese_rechte='%s', schreibrechte='%s', menu_icon='%s'",
			$this->cms->papoo_menu_int,
			$menuid,
			$this->db->escape($this->get_text_fallback($menue_array, "eintrag", "en")),
			$this->db->escape($menue_array['link'][0]['cdata']),
			$this->db->escape($this->get_text_fallback($menue_array, "titel", "en")),
			$temp_order_id,

			$this->db->escape($parent),
			$this->db->escape($level),
			"1",
			"1",
			$this->db->escape("../../plugins/" . $menue_array['icon'][0]['cdata'])
		);
		$this->db->get_results($sql);

		// 2.B Menü-Punkt in Tabelle papoo_men_uint_language eintragen (deutsch und englisch)
		if (!empty($this->sprachen)) {
			foreach ($this->sprachen as $sprache) {
				// 1. Eintrag- und Titel-Text auslesen
				$eintrag = $this->get_text_fallback($menue_array, "eintrag", $sprache['lang_short']);
				$titel = $this->get_text_fallback($menue_array, "titel", $sprache['lang_short']);

				// 2. und in die Tabelle eintragen
				$sql = sprintf("INSERT INTO %s SET lang_id='%d', menuid_id='%d', menuname='%s', back_front='%d', lang_title='%s'",
					$this->cms->papoo_menuint_language,
					$sprache['lang_id'],
					$menuid,
					$this->db->escape($eintrag),
					1,
					$this->db->escape($titel)
				);
				$this->db->get_results($sql);
			}
		}

		// 2.C Menü-Punkt in Tabelle papoo_lookup_men_int eintragen
		$sql = sprintf("INSERT INTO %s SET menuid='%d', gruppenid='%d'",
			$this->cms->papoo_lookup_men_int,
			$menuid,
			1
		);
		$this->db->get_results($sql);

		// 3.A lesen der bisherigen menuids
		$sql = sprintf("SELECT plugin_menuids FROM %s WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result = $this->db->get_results($sql);
		$the_menuids = $result[0]->plugin_menuids." ".$menuid;

		// 3.B schreiben der neuen menuids
		$sql = sprintf("UPDATE %s SET plugin_menuids='%s' WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$the_menuids,
			$plugin_id
		);
		$this->db->get_results($sql);

		return $menuid;
	}

	/**
	 * Löscht alle Menue-Einträge im Backend-Menue eines Plugins anhand der Übergebenen Plugin-ID.
	 *
	 * @param int $plugin_id
	 * @return bool TRUE bei Erfolg; FALSE bei Fehler
	 */
	function delete_menue($plugin_id = 0)
	{
		$erfolg = TRUE;
		// 1. Alle menuids des Plugins ermitteln
		$sql = sprintf("SELECT plugin_menuids FROM %s WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result_array = $this->db->get_results($sql, ARRAY_A);
		$menuids = explode(" ", $result_array[0]['plugin_menuids']);

		// 2. Menü-Punkte in Tabellen löschen
		if ($menuids) {
			foreach($menuids as $menuid) {
				$menuid = trim($menuid);
				if ($menuid) {
					// 2.A Menü-Punkt in Tabelle papoo_menuint löschen
					$sql = sprintf("DELETE FROM %s WHERE menuid='%d'",
						$this->cms->papoo_menu_int,
						$menuid
					);
					$result = $this->db->get_results($sql);
					if (!$result) $erfolg = FALSE;

					// 2.B Menü-Punkt in Tabelle papoo_men_uint_language löschen (deutsch und englisch)
					$sql = sprintf("DELETE FROM %s WHERE menuid_id='%d'",
						$this->cms->papoo_menuint_language,
						$menuid
					);
					$result = $this->db->get_results($sql);
					if (!$result) {
						$erfolg = FALSE;
					}

					// 2.C Menü-Punkt in Tabelle papoo_lookup_men_int löschen
					$sql = sprintf("DELETE FROM %s WHERE menuid='%d'",
						$this->cms->papoo_lookup_men_int,
						$menuid
					);
					$result = $this->db->get_results($sql);
					if (!$result) {
						$erfolg = FALSE;
					}
				}
			}
		}

		if ($erfolg) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Registriert die Klasse durch Eintrag in die Tabelle papoo_pluginclasses
	 *
	 * @param array $klasse_array
	 * @param $plugin_id
	 */
	function make_pluginclass_entry($klasse_array = array(), $plugin_id)
	{
		$sql = sprintf("INSERT INTO %s SET pluginclass_plugin_id='%d', pluginclass_name='%s', pluginclass_datei='%s'",
			$this->cms->papoo_pluginclasses,
			$plugin_id,
			$this->db->escape($klasse_array['name'][0]['cdata']),
			$this->db->escape($klasse_array['datei'][0]['cdata'])
		);
		$this->db->get_results($sql);
	}

	/**
	 * Löscht alle Plugin-Klassen des Plugins mit der ID $plugin_id durch Entfernen der Einträge in der Tabelle papoo_pluginclasses
	 *
	 * @param $plugin_id
	 */
	function delete_pluginclass_entry($plugin_id)
	{
		$sql = sprintf("DELETE FROM %s WHERE pluginclass_plugin_id='%d'",
			$this->cms->papoo_pluginclasses,
			$plugin_id
		);
		$this->db->get_results($sql);

		$_SESSION['dbp']=array();
	}

	/**
	 * Bindet das Modul $modul in die Modul-Tabellen papoo_module und papoo_module_language ein
	 *
	 * @param $modul
	 * @param $plugin_id
	 * @return mixed
	 */
	function make_modul_entry($modul, $plugin_id)
	{
		// 1. Modul in Tabelle papoo_module eintragen
		$sql = sprintf("INSERT INTO %s SET mod_datei='%s'",
			$this->db_praefix."papoo_module",
			$this->db->escape($modul['datei'][0]['cdata'])
		);

		$this->db->query($sql);

		$mod_id = $this->db->insert_id;

		// 2. Sprach-Einträge in Tabelle papoo_module_language vornehmen
		if (!empty($this->sprachen)) {
			foreach ($this->sprachen as $sprache) {
				// 1. Name- und Beschreibungs-Text auslesen
				$name = $this->get_text_fallback($modul, "name", $sprache['lang_short']);
				$beschreibung = $this->get_text_fallback($modul, "beschreibung", $sprache['lang_short']);

				// 2. in die Tabelle eintragen
				$sql = sprintf("INSERT INTO %s SET modlang_mod_id='%d', modlang_lang_id='%d', modlang_name='%s', modlang_beschreibung='%s'",
					$this->db_praefix."papoo_module_language",
					$mod_id,
					$sprache['lang_id'],
					$this->db->escape($name),
					$this->db->escape($beschreibung)
				);
				$this->db->query($sql);
			}
		}
		// 3.A lesen der bisherigen modulids
		$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result = $this->db->get_var($sql);
		$the_modulids = $result." ".$mod_id." ";
		// 3.B schreiben der neuen modulids
		$sql = sprintf("UPDATE %s SET plugin_modulids='%s' WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$this->db->escape($the_modulids),
			$plugin_id
		);
		$this->db->get_results($sql);

		return $mod_id;
	}

	/**
	 * Löscht alle Modul-Einträge eines Plugins anhand der Übergebenen Plugin-ID.
	 *
	 * @param int $plugin_id
	 * @return bool TRUE bei Erfolg; FALSE bei Fehler
	 */
	function delete_modul_entry($plugin_id = 0)
	{
		$erfolg = TRUE;
		// 1. Alle modulids des Plugins ermitteln
		$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$modulids = $this->db->get_var($sql);
		$modulids = explode(" ",$modulids);

		// 2. Module aus Tabellen papoo_module und papoo_module_language löschen
		if ($modulids) {
			foreach($modulids as $mod_id) {
				$mod_id = trim($mod_id);
				if ($mod_id) {
					// 2.A Modul aus Tabelle papoo_module löschen
					$sql = sprintf("DELETE FROM %s WHERE mod_id='%d'",
						$this->db_praefix."papoo_module",
						$mod_id
					);
					$result = $this->db->get_results($sql);
					if (!$result) {
						$erfolg = FALSE;
					}

					// 2.B Modul aus Tabelle papoo_module_language löschen (alle Sprachen)
					$sql = sprintf("DELETE FROM %s WHERE modlang_mod_id='%d'",
						$this->db_praefix."papoo_module_language",
						$mod_id
					);
					$result = $this->db->get_results($sql);
					if (!$result) {
						$erfolg = FALSE;
					}

					// 2.C Modul aus Tabelle papoo_styles_module löschen (alle Styles)
					$sql = sprintf("DELETE FROM %s WHERE stylemod_mod_id='%d'",
						$this->db_praefix."papoo_styles_module",
						$mod_id
					);
					$result = $this->db->get_results($sql);
					if (!$result) {
						$erfolg = FALSE;
					}
				}
			}
		}

		if ($erfolg) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Erstellt ein Array mit Informationen sämtlicher Plugin-Arrays.
	 * Dabei werden die Daten normalisiert, sprich in eine einheitliche Form gebracht.
	 */
	function make_content()
	{
		// 1. Sprache im Backend feststellen
		$lang_id = $this->cms->lang_back_id;
		//echo "Sprache: ".$language."; Sprach-ID: ".$lang_id;

		// 2. installierte Plugin-Informationen laden
		if ($this->plugin_installed) {
			foreach($this->plugin_installed as $plugin) {
				$identifier = $this->diverse->sicherer_dateiname($plugin['plugin_name']."_".$plugin['plugin_version']);
				// Sprachunterscheidung:
				$query = sprintf("SELECT pluginlang_beschreibung FROM %s WHERE pluginlang_lang_id='%d' AND pluginlang_plugin_id='%d' LIMIT 1",
					$this->cms->papoo_plugin_language,
					$lang_id,
					$plugin['plugin_id']
				);
				$result = $this->db->get_results($query, ARRAY_A);
				$beschreibung = $result[0]['pluginlang_beschreibung'];

				IfNotSetNull($plugin['datum']);
				IfNotSetNull($plugin['autor']);
				array_push($this->plugin_content, array (	'name' => $plugin['plugin_name'],
					'version' => $plugin['plugin_version'],
					'von_datum' => $plugin['datum'][0]['cdata'],
					'autor' => $plugin['autor'][0]['name']['0']['cdata'],
					'identifier' => $identifier,
					'plugin_id' => $plugin['plugin_id'],
					'papoo_plugin_id' => $plugin['plugin_papoo_id'],
					'beschreibung' => ($beschreibung),
					'switch_installed' => true,
					//'link_install' => "",
					//'link_uninstall' => "plugins.php?menuid=47&plugin_action=uninstall_fragen&plugin_id=".$plugin['plugin_id'],
					'update_check' => "plugins.php?menuid=47&plugin_action=check_update&plugin_id=".$plugin['plugin_id']."&plugin_papoo_id=".$plugin['plugin_papoo_id'],
					'update_information' => ""
				));
			}
		}

		// 3. lokale Plugin-Informationen laden
		if ($this->plugin_lokal) {
			foreach($this->plugin_lokal as $plugin) {
				$plugin = $plugin['plugin'][0];
				$identifier = $this->diverse->sicherer_dateiname($plugin['name'][0]['cdata']."_".$plugin['version'][0]['cdata']);
				// nur Plugins laden, welche noch nicht installiert, also noch nicht in $this->plugin_content, sind
				$is_installed = FALSE;
				if ($this->plugin_content) {
					foreach($this->plugin_content as $test) {
						if ($test['identifier'] == $identifier) {
							$is_installed = TRUE;
						}
					}
				}
				if (!$is_installed) {
					$beschreibung = $this->get_text_fallback($plugin, "beschreibung", $this->cms->lang_back_short);

					if (empty($plugin['version'][0]['cdata'])) {
						$plugin['version'][0]['cdata']="";
					}
					if (empty($plugin['papooid'][0]['cdata'])) {
						$plugin['papooid'][0]['cdata']="";
					}

					/*
					if ($_SERVER["SERVER_NAME"]!="localhost")
					{
						$plugin['name'][0]['cdata']=utf8_encode($plugin['name'][0]['cdata']);
					}
					*/
					IfNotSetNull($plugin['datum']);
					IfNotSetNull($plugin['autor']);
					IfNotSetNull($plugin['datum'][0]['cdata']);
					IfNotSetNull($plugin['version'][0]['cdata']);
					IfNotSetNull($plugin['autor'][0]['name']['0']['cdata']);
					array_push($this->plugin_content, array (
						'name' => $plugin['name'][0]['cdata'],
						'version' => $plugin['version'][0]['cdata'],
						'von_datum' => $plugin['datum'][0]['cdata'],
						'autor' => $plugin['autor'][0]['name']['0']['cdata'],
						'identifier' => $identifier,
						'plugin_id' => "",
						'papoo_plugin_id' => $plugin['papooid'][0]['cdata'],
						'beschreibung' => ($beschreibung),
						'switch_installed' => false,
						//'link_install' => "plugins.php?menuid=47&plugin_action=install&plugin_identifier=".$identifier,
						//'link_uninstall' => "",
						//'update_check' => "",
						'update_information' => ""
					));
				}
			}
		}

		// 4. Plugin-Informationen offizieller papoo-Plugins laden
		// FIXME: gibt allen Plugins "kein offizielles Plugin aus", wenn nicht gerade die Aktion "check_update" ausgeführt wird !!!
		if (!empty($this->plugin_papoo)) {
			foreach($this->plugin_papoo as $plugin) {
				$count = 0;
				if ($this->plugin_content) {
					foreach($this->plugin_content as $test) {
						if ($test['name'] == $plugin['title']) {
							if ($test['version'] < $plugin['dc']['plversion']) {
								$this->plugin_content[$count]['update_information'] =
									'Für dieses Plugin ist eine aktuellere Version verfügbar (Version: '.$plugin['dc']['plversion'].');
									Download unter diesem <a href="'.$plugin['link'].'">Link</a>';
							}
							else {
								$this->plugin_content[$count]['update_information'] = "Dieses PlugIn ist up-to-date!";
							}
						}
						$count++;
					}
				}
			}
		}
		// Plugin ist kein offizielles papoo-Plugin, installiertes Plugin entsprechend markieren.
		else if (isset($this->checked->plugin_action) && $this->checked->plugin_action == "check_update") {
			$count = 0;
			if ($this->plugin_content) {
				foreach($this->plugin_content as $test) {
					if ($test['plugin_id'] == $this->checked->plugin_id) {
						$this->plugin_content[$count]['update_information'] =
							'Dies ist kein offizielles papoo-Plugin, bzw. es existiert kein Eintrag für dieses Plugin auf dem papoo-Plugin-Server.';
					}
					$count++;
				}
			}
		}

		// Plugins alphabetisch sortieren
		$installed = array();
		$available = array();
		// Separiere Array zwischen installierten und nicht-installierten Plugins
		foreach($this->plugin_content as $value) {
			if($value["switch_installed"]) {
				$installed[] = $value;
			}
			else {
				$available[] = $value;
			}
		}
		for($i = 0; $i < (sizeof($installed) - 1); $i++) {
			$swap = false;
			for($j = 0; $j < (sizeof($installed) - 1); $j++) {
				// Whitespace trimmen und Kleinbuchstaben, um richtig zu sortieren
				if(mb_strtolower(trim($installed[$j]["name"])) > mb_strtolower(trim($installed[$j+1]["name"]))) {
					// Tauschen
					$tmp = $installed[$j];
					$installed[$j] = $installed[$j+1];
					$installed[$j+1] = $tmp;
					$swap = true;
				}
			}
			if(!$swap) {
				break;
			}
		}
		for($i = 0; $i < (sizeof($available) - 1); $i++) {
			$swap = false;
			for($j = 0; $j < (sizeof($available) - 1); $j++) {
				if(mb_strtolower(trim($available[$j]["name"])) > mb_strtolower(trim($available[$j+1]["name"]))) {
					$tmp = $available[$j];
					$available[$j] = $available[$j+1];
					$available[$j+1] = $tmp;
					$swap = true;
				}
			}
			if(!$swap) {
				break;
			}
		}
		// Plugins wieder zusammenfügen
		$this->plugin_content = array_merge($installed, $available);
		unset($installed, $available);

		$this->content->template['plugin_data'] = $this->plugin_content;
	}

	/**
	 * @param array $element
	 * @param $text
	 * @param string $sprache
	 * @return mixed|string
	 */
	function get_text_fallback($element = array(), $text, $sprache = "")
	{
		$text_fallback = "";

		IfNotSetNull($element[$text."_".$sprache]);
		IfNotSetNull($element[$text."_de"]);
		IfNotSetNull($element[$text."_en"]);

		if (!empty($element) && !empty($text)) {
			$text_fallback = $element[$text."_".$sprache][0]['cdata'];

			// 1. Fall-Back auf English
			if (empty($text_fallback)) {
				$text_fallback = $element[$text."_en"][0]['cdata'];
			}
			// 2. Fall-Back auf Deutsch
			if (empty($text_fallback)) {
				$text_fallback = $element[$text."_de"][0]['cdata'];
			}
		}

		/*
		if ($_SERVER["SERVER_NAME"]!="localhost")
		{
			$text_fallback=utf8_encode($text_fallback);
		}
		*/
		return $text_fallback;
	}
}

$plugin = new intern_plugin();