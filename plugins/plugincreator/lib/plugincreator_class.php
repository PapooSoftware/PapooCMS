<?php

/**
 * Hauptdatei für das Plugin Creator Plugin.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 *
 * TODO: Besser kapseln (namensgebung, z.b.)
 */


/**
 * Class plugincreator_class
 *
 * Hauptklasse des Plugins zum erstellen/bearbeiten von Plugins.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 */
class plugincreator_class
{

	private $creation_params;
	private $plugin_dir_name;
	private $pluginpath;
	private $maintemplate;
	private $subtemplates;
	private $modultemplates;

	/**
	 * Konstruktor der plugincreator_class, füllt die Plugin Liste für das plugincreator_plugins_backend
	 * und überprüft das Post auf etwaige Änderungen.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $db_praefix, $checked, $user, $plugin, $diverse, $cms;
		$this->content = & $content;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->checked = & $checked;
		$this->user = & $user;
		$this->intern_plugin = & $plugin;
		$this->diverse = & $diverse;
		$this->cms = & $cms;

		if(defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			// Nur is_dev verursacht Fehler in den templates.
			$this->content->template['plugin_creator_is_dev'] = 'ok';

			if ($template != "login.utf8.html") {
				if (stristr($template2,"plugincreator_delete_backend") ||
					stristr($template2,"plugincreator_backend") ||
					stristr($template2,"plugincreator_plugins_backend") ||
					stristr($template2,"plugincreator_create_backend")) {
					// CSS für u.A. die "Änderungen gespeichert"-Nachricht im backend
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/plugincreator/css';
					$this->content->template['creator_plugin_template_weiche'] = "delete_list";
					$this->content->template['plugin']['plugincreator']['edit_return_code'] = 0;

					// Schreibrechte im "/plugins/"-Verzeichnis pruefen und ausgeben
					if(!is_writable(PAPOO_ABS_PFAD . "/plugins")) {
						$this->content->template['plugin']['plugincreator']['plugin_dir_not_writeable'] = true;
					}

					$this->CheckPost();
					$this->MakeContent();

					// devtools werden benötigt, also Warnung ausgeben wenn nicht vorhanden
					if(!$this->PluginIsInstalled("devtools")) {
						$this->content->template['plugin']['plugincreator']['plugin_devtools_not_installed'] = true;
					}
				}
			}
		}
	}

	/**
	 * Funktion die zurückgibt ob das Plugin mit Namen $plugin_name im Papoo CMS installiert ist.
	 *
	 * @param string $plugin_name Name des Plugins dessen Installiertheit zu überprüfen es gilt.
	 * @param string $version Version die installiert sein muss.
	 * @return bool true falls das Plugin mit Namen $plugin_name installiert ist, false sonst
	 */
	public function PluginIsInstalled($plugin_name, $version = null)
	{
		$plugintable = $this->cms->tbname['papoo_plugins'];
		$sql = "SELECT plugin_name FROM $plugintable WHERE plugin_name='$plugin_name'";

		if($version) {
			$sql .= " AND plugin_version='$version'";
		}

		$plugins = $this->db->get_results($sql, ARRAY_A);

		return !empty($plugins);
	}

	/**
	 * Funktion die die vorhandenen Einstellungen in das content->template lädt, damit die Forms mit den
	 * vorhandenen Einstellungen gefüllt werden können.
	 *
	 * @uses intern_plugin Lädt die installierten und nicht installierten Plugins in das template.
	 * @return void
	 */
	private function MakeContent()
	{
		$this->intern_plugin->read_installed();
		$this->intern_plugin->read_lokal();
		$this->intern_plugin->make_content();

		$this->content->template['plugin']['plugincreator']['plugin_data'] = $this->content->template['plugin_data'];
	}

	/**
	 * Überprüft ob neue Einstellungen per POST abgeschickt wurden und erstellt, editiert, reinstalliert oder deinstalliert
	 * je nach überschickten Daten ein Plugin.
	 *
	 * @uses CreatePlugin()
	 * @uses EditPlugin()
	 * @uses ReinstallPlugin()
	 * @uses DeinstallPlugin()
	 * @uses checked_class
	 * @return void
	 */
	private function CheckPost()
	{
		// Check Plugin erstellen
		if(isset($this->checked->creator_name)) {
			unset($_SESSION['plugin']['plugincreator']['editplugin_info']);

			IfNotSetNull($this->checked->creator_pluginid);
			IfNotSetNull($this->checked->creator_plugindir);
			IfNotSetNull($this->checked->creator_plugin_identifier);
			IfNotSetNull($this->checked->creator_desc);
			IfNotSetNull($this->checked->creator_autor_name);
			IfNotSetNull($this->checked->creator_autor_email);
			IfNotSetNull($this->checked->menupunkt);
			IfNotSetNull($this->checked->datenbank);
			IfNotSetNull($this->checked->module);

			$create_params =
				[
					"pluginid" => $this->checked->creator_pluginid,
					"plugindir" => $this->checked->creator_plugindir,
					"plugin_identifier" => $this->checked->creator_plugin_identifier,
					"name" => $this->checked->creator_name,
					"description" => $this->checked->creator_desc,
					"autor_name" => $this->checked->creator_autor_name,
					"autor_email" => $this->checked->creator_autor_email,
					"menupunkte" => $this->checked->menupunkt,
					"datenbanken" => $this->checked->datenbank,
					"module" => $this->checked->module
				];
			$this->CreatePlugin($create_params);
		}
		// Check Plugin Liste Button gedrückt/Lösch Liste button gedrückt
		if(isset($this->checked->creatorplugin_action) and isset($this->checked->creatorplugin_id)) {
			try {
				switch($this->checked->creatorplugin_action) {
				case 'plugin_edit':
					$this->content->template['plugin']['plugincreator']['edit_return_code'] = $this->FillContentEditPlugin($this->checked->creatorplugin_id, $this->checked->creatorplugin_identifier);
					break;
				case 'plugin_reinstall':
					$this->ReinstallPlugin($this->checked->creatorplugin_id, $this->checked->creatorplugin_identifier);
					break;
				case 'plugin_install':
					$this->InstallPlugin($this->checked->creatorplugin_identifier);
					break;
				case 'plugin_uninstall':
					$this->DeinstallPlugin($this->checked->creatorplugin_id);
					break;
				case 'plugin_delete':
					#$this->content->template['plugin']['plugincreator']['edit_return_code'] = $this->DeletePlugin($this->checked->creatorplugin_id, $this->checked->creatorplugin_identifier);
					$this->content->template['creator_plugin_template_weiche'] = "delete_confirm";
					$this->content->template['plugin_id'] = $this->checked->creatorplugin_id;
					$this->content->template['plugin_identifier'] = $this->checked->creatorplugin_identifier;
					break;
				case 'DoDelete':
					$this->content->template['plugin']['plugincreator']['edit_return_code'] = $this->DeletePlugin($this->checked->creatorplugin_id, $this->checked->creatorplugin_identifier);
					$this->content->template['creator_plugin_template_weiche'] = "delete_list";
					#echo $this->content->template['plugin']['plugincreator']['edit_return_code']; exit;
					break;
				}
			}
			catch(Exception $e) {
			}
		}
	}

	/**
	 * Kreiert einen Verzeichnis Namen aus dem Plugin Namen.
	 *
	 * Dieser neue Name soll dann für prefixes, Dateinamen, etc. benutzt werden können.
	 *
	 * @param $plugin_name string Den Plugin Namen, oder einen anderen string der auf kleinbuchstaben reduziert werden soll.
	 * @return string Eine auf kleinbuchstaben reduzierte Version der Eingabe.
	 */
	static public function ToSecureName($plugin_name)
	{
		$matches = array();

		if(!preg_match_all("/[a-zA-Z]+/", $plugin_name, $matches))
			return NULL;

		$plugin_name = "";

		foreach($matches[0] as $match) {
			$plugin_name .= $match;
		}

		return strtolower($plugin_name);
	}

	/**
	 * @param $input
	 * @return bool|string
	 */
	static public function ShortName($input)
	{
		return substr(plugincreator_class::ToSecureName($input), 0, 10);
	}

	/**
	 * Erzeugt ein neues Plugin incl. Ordner, etc. anhand der $creation_params.
	 *
	 * Erstellt zuerst die nötige Ordnerstruktur, setzt dann die Rechte auf 0777,
	 * erstellt dann die erste Version der Dateien in dem Verzeichnis und dann
	 * wird per EditPlugin() die zweite Version der Dateien gesetzt.
	 *
	 * @param $creation_params array Die Parameter die den Namen des Plugins, des Authors, der Tabellen, etc. angeben.
	 * @uses WriteTemplateFiles()
	 * @uses WriteXMLFile()
	 * @uses WriteCSSFile()
	 * @uses WriteSQLFile()
	 * @uses WriteMessageFiles()
	 * @uses WritePHPFiles()
	 * @uses EditPlugin()
	 * @return void
	 */
	private function CreatePlugin($creation_params)
	{
		$plugin_dir_name = plugincreator_class::ToSecureName($creation_params['name']);

		$plugin_path = PAPOO_ABS_PFAD . "/plugins/" . $plugin_dir_name . "/";

		$this->plugin_dir_name = $plugin_dir_name;
		$this->pluginpath = $plugin_path;
		$this->creation_params = $creation_params;

		if(is_dir(PAPOO_ABS_PFAD . "/plugins/" . $creation_params['plugindir']) and !empty($creation_params['plugindir'])) {
			$this->pluginpath = PAPOO_ABS_PFAD . "/plugins/" . $creation_params['plugindir'];
			$this->plugin_dir_name = $creation_params['plugindir'];

			$returncode = $this->EditPlugin();

			$this->content->template['plugin']['plugincreator']['edit_return_code'] = $returncode;

			return;
		}
		else if(is_dir($plugin_path)) {
			$this->content->template['plugin']['plugincreator']['edit_return_code'] = -8;
			#$this->EditPlugin();
			return;
		}

		mkdir($plugin_path, 0777, true);
		mkdir($plugin_path . "lib/", 0777, true);
		mkdir($plugin_path . "css/", 0777, true);
		mkdir($plugin_path . "messages/", 0777, true);
		mkdir($plugin_path . "sql/", 0777, true);
		mkdir($plugin_path . "js/", 0777, true);
		mkdir($plugin_path . "img/", 0777, true);
		mkdir($plugin_path . "templates/", 0777, true);

		chmod($plugin_path, 0777);
		chmod($plugin_path . "lib/", 0777);
		chmod($plugin_path . "css/", 0777);
		chmod($plugin_path . "messages/", 0777);
		chmod($plugin_path . "sql/", 0777);
		chmod($plugin_path . "js/", 0777);
		chmod($plugin_path . "img/", 0777);
		chmod($plugin_path . "templates/", 0777);

		$this->WriteTemplateFiles();
		$this->WriteXMLFile();
		$this->WriteCSSFile();
		$this->WriteSQLFiles();
		$this->WriteMessageFiles();
		$this->WritePHPFiles();

		$this->creation_params['plugin_identifier'] = $this->diverse->sicherer_dateiname($this->creation_params['name']."_1.0");

		$this->content->template['plugin']['plugincreator']['edit_return_code_create'] = $this->EditPlugin(false);
	}

	/**
	 * Unterfunktion von EditPlugin(). Ändert die XML Datei so, dass sie zu den angegebenen Menüpunkten passt.
	 * Dabei erstellt, benennnt um oder löscht je nach Bedarf Menüpunkt-Einträge.
	 *
	 * @param $xmlinhalt string Der Inhalt der XML Datei per ref.
	 * @uses DOMDocument
	 * @return void
	 */
	private function EditMenupunkte(&$xmlinhalt)
	{
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;

		$dom->loadXML($xmlinhalt);

		$root = $dom->documentElement;

		$menue = $root->getElementsByTagName("menue")->item(0);

		$eintrag_de = $menue->getElementsByTagName("eintrag_de")->item(0);
		$eintrag_en = $menue->getElementsByTagName("eintrag_en")->item(0);

		// Andere CDATA Sachen löschen
		while($eintrag_de->firstChild) {
			$eintrag_de->removeChild($eintrag_de->firstChild);
		}
		while($eintrag_en->firstChild) {
			$eintrag_en->removeChild($eintrag_en->firstChild);
		}

		$eintrag_de->appendChild($dom->createCDATASection($this->creation_params['name']));
		$eintrag_en->appendChild($dom->createCDATASection($this->creation_params['name']));

		foreach($menue->getElementsByTagName('submenue') as $submenue) {
			$to_hash = "";

			$to_hash .= $submenue->getElementsByTagName("eintrag_de")->item(0)->nodeValue;
			$to_hash .= $submenue->getElementsByTagName("eintrag_en")->item(0)->nodeValue;
			$to_hash .= $submenue->getElementsByTagName("icon")->item(0)->nodeValue;
			$to_hash .= $submenue->getElementsByTagName("link")->item(0)->nodeValue;

			$md5 = hash("md5", $to_hash);
			$abgearbeitet = false;

			if(is_array($this->creation_params['menupunkte'])) {
				foreach($this->creation_params['menupunkte'] as $hash => &$name) {
					// Ist der richtige Menüpunkt => ersetzen!
					if($hash === $md5 && !is_array($name)) {
						$eintrag_de = $submenue->getElementsByTagName("eintrag_de")->item(0);
						$eintrag_en = $submenue->getElementsByTagName("eintrag_en")->item(0);

						// Andere CDATA Sachen löschen
						while($eintrag_de->firstChild) {
							$eintrag_de->removeChild($eintrag_de->firstChild);
						}
						while($eintrag_en->firstChild) {
							$eintrag_en->removeChild($eintrag_en->firstChild);
						}

						$eintrag_de->appendChild($dom->createCDATASection($name));
						$eintrag_en->appendChild($dom->createCDATASection($name));

						// Menupunkt als abgearbeitet markieren durch in-array-stecken
						$name = [$name];
						$abgearbeitet = true;

						break;
					}
				}

				// Menüpunkt wurde nicht markiert => Soll gelöscht werden
				if(!$abgearbeitet) {
					$menue->removeChild($submenue);
				}
			}
		}

		// Menüpunkt soll hinzugefügt werden
		if(is_array($this->creation_params['menupunkte'])) {
			foreach($this->creation_params['menupunkte'] as $hash => $name) {
				if(!is_array($name)) {
					$submenue = $dom->createElement("submenue");

					$eintrag_de = $dom->createElement("eintrag_de");
					$eintrag_en = $dom->createElement("eintrag_en");
					$icon = $dom->createElement("icon");
					$link = $dom->createElement("link");

					$eintrag_de->appendChild($dom->createCDATASection($name));
					$eintrag_en->appendChild($dom->createCDATASection($name));
					$link->nodeValue = "plugin:" . $this->plugin_dir_name . "/templates/" . plugincreator_class::ToSecureName($name) . "_backend.html";

					$submenue->appendChild($eintrag_de);
					$submenue->appendChild($eintrag_en);
					$submenue->appendChild($icon);
					$submenue->appendChild($link);

					$menue->appendChild($submenue);

					$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/templates/vorlage_backend.html");
					$default_content = preg_replace("/vorlage/", plugincreator_class::ToSecureName($this->creation_params['name']), $default_content);
					$default_content = preg_replace("/\{__PREFIX__\}/", plugincreator_class::ShortName($this->creation_params['name']), $default_content);
					$default_content = preg_replace("/\{__TEMPLATE_NAME__\}/", plugincreator_class::ShortName($name), $default_content);

					file_put_contents(PAPOO_ABS_PFAD . "/plugins/". $this->plugin_dir_name . "/templates/" . plugincreator_class::ToSecureName($name) . "_backend.html", $default_content);
					chmod(PAPOO_ABS_PFAD . "/plugins/". $this->plugin_dir_name . "/templates/" . plugincreator_class::ToSecureName($name) . "_backend.html", 0777);
				}
			}

		}

		$xmlinhalt = $dom->saveXML();
	}

	/**
	 * Unterfunktion von EditPlugin(). Ändert die XML Datei so, dass sie zu den angegebenen Modulen passt.
	 * Dabei erstellt, benennnt um oder löscht je nach Bedarf Modul-Einträge.
	 *
	 * @param $xmlinhalt string Der Inhalt der XML Datei per ref.
	 * @uses DOMDocument
	 * @uses hash
	 * @return void
	 */
	private function EditModule(&$xmlinhalt)
	{
		$creation_params = $this->creation_params;

		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;

		$dom->loadXML($xmlinhalt);

		$root = $dom->documentElement;

		foreach($root->getElementsByTagName("modul") as $modul) {
			$to_hash = "";

			$to_hash .= $modul->getElementsByTagName("datei")->item(0)->nodeValue;
			$to_hash .= $modul->getElementsByTagName("name_de")->item(0)->nodeValue;
			$to_hash .= $modul->getElementsByTagName("beschreibung_de")->item(0)->nodeValue;
			$to_hash .= $modul->getElementsByTagName("name_en")->item(0)->nodeValue;
			$to_hash .= $modul->getElementsByTagName("beschreibung_en")->item(0)->nodeValue;
			$to_hash .= $modul->getElementsByTagName("modus")->item(0)->nodeValue;

			$md5 = hash("md5", $to_hash);

			$abgearbeitet = false;

			if(is_array($creation_params['module'])) {
				foreach($creation_params['module'] as $hash => &$mod_array) {
					// Ist der richtige Menüpunkt, ersetzen!
					if($hash === $md5 && !is_array($mod_array['name'])) {
						$abgearbeitet = true;

						$name_de = $modul->getElementsByTagName("name_de")->item(0);
						$beschreibung_de = $modul->getElementsByTagName("beschreibung_de")->item(0);

						// Andere CDATA Sachen löschen
						while($name_de->firstChild) {
							$name_de->removeChild($name_de->firstChild);
						}
						while($beschreibung_de->firstChild) {
							$beschreibung_de->removeChild($beschreibung_de->firstChild);
						}

						$name_de->appendChild($dom->createCDATASection($mod_array['name']));
						$beschreibung_de->appendChild($dom->createCDATASection($mod_array['desc']));

						// Menupunkt als abgearbeitet markieren
						$mod_array['name'] = [$mod_array['name']];

						break;
					}
				}
				if(!$abgearbeitet) {
					$root->removeChild($modul);
				}
			}
		}
		// Hinzufügen von den modulen die nicht abgearbeitet wurden
		if(is_array($creation_params['module'])) {
			foreach($creation_params['module'] as $hash => $mod_array) {
				if(!is_array($mod_array['name'])) {
					$modul = $dom->createElement("modul");

					$datei = $dom->createElement("datei");
					$datei->nodeValue = "plugin:" . $this->plugin_dir_name . "/templates/mod_" . plugincreator_class::ToSecureName($mod_array['name']) . "_frontend.html";

					$name_de = $dom->createElement("name_de");
					$name_de->appendChild($dom->createCDATASection($mod_array['name']));

					$beschreibung_de = $dom->createElement("beschreibung_de");
					$beschreibung_de->appendChild($dom->createCDATASection($mod_array['desc']));

					$name_en = $dom->createElement("name_en");
					$name_en->appendChild($dom->createCDATASection($mod_array['name']));

					$beschreibung_en = $dom->createElement("beschreibung_en");
					$beschreibung_en->appendChild($dom->createCDATASection($mod_array['desc']));

					$modus = $dom->createElement("modus");
					$modus->nodeValue = "var";

					$modul->appendChild($datei);
					$modul->appendChild($name_de);
					$modul->appendChild($beschreibung_de);
					$modul->appendChild($name_en);
					$modul->appendChild($beschreibung_en);
					$modul->appendChild($modus);

					$root->appendChild($modul);

					$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/templates/mod_vorlage_frontend.html");
					$default_content = preg_replace("/mod_vorlage_frontend/", "mod_" . plugincreator_class::ToSecureName($mod_array['name']) . "_frontend", $default_content);

					file_put_contents(PAPOO_ABS_PFAD . "/plugins/". $this->plugin_dir_name . "/templates/mod_" . plugincreator_class::ToSecureName($mod_array['name']) . "_frontend.html", $default_content);
					chmod(PAPOO_ABS_PFAD . "/plugins/". $this->plugin_dir_name . "/templates/mod_" . plugincreator_class::ToSecureName($mod_array['name']) . "_frontend.html", 0777);
				}
			}
		}

		$xmlinhalt = $dom->saveXML();
	}

	/**
	 * Unterfunktion von EditPlugin(). Ändert die SQL Installations Datei so, dass sie zu den angegebenen Tabellen passt.
	 * Dabei erstellt, benennnt um oder löscht je nach Bedarf CREATE TABLE/DROP TABLE/INSERT INTO ...-Einträge.
	 *
	 * @param $datenbankpath string Absoluter oder relativer pfad zur SQL Installations Datei des Plugins.
	 * @uses preg_replace()
	 * @uses preg_replace_callback()
	 * @uses file_get_contents()
	 * @uses file_put_content()
	 * @return void
	 */
	private function EditDatenbankInstall($datenbankpath)
	{
		$datenbankinhalt = file_get_contents($datenbankpath);

		$regex = "/(DROP .+?`XXX_)(.+?)(`.+?##b_dump##.+?CREATE.+?`XXX_)(.+?)(`.+?##b_dump##)/s";

		$creation_params = &$this->creation_params;

		$datenbankinhalt = preg_replace_callback($regex,
			function ($matches) use (&$creation_params)
			{

				$to_hash = $matches[2];

				$md5 = hash("md5", $to_hash);

				if(is_array($creation_params['datenbanken'])) {
					foreach($creation_params['datenbanken'] as $hash => &$name) {
						// Ist der richtige Menüpunkt, ersetzen!
						if($hash === $md5 && !is_array($name)) {
							$return = $matches[1] . $name . $matches[3] . $name . $matches[5];

							// Menupunkt als abgearbeitet markieren
							$namensarray = array();
							$namensarray['name'] = $name;
							$name = $namensarray;

							// Ersetzen
							return $return;
						}
					}
				}
				// Löschen
				return "";
			}
			, $datenbankinhalt);

		// Hinzufügen
		if(is_array($creation_params['datenbanken'])) {
			foreach($creation_params['datenbanken'] as $hash => $name) {
				if(!is_array($name)) {
					$tabellenname = "XXX_" . plugincreator_class::ToSecureName($this->creation_params['name']) . "_" . $name;
					$regex = "/(?(?=.*?DROP TABLE)(DROP TABLE)|)/s";
					$repl = "DROP TABLE IF EXISTS `" . $tabellenname . "`; ##b_dump##\nCREATE TABLE `" . $tabellenname . "` (\n\n) ENGINE=MyISAM; ##b_dump##\n\n$1";

					$datenbankinhalt = preg_replace($regex, $repl, $datenbankinhalt, 1);
				}
			}
		}

		$creation_params = &$this->creation_params;

		// Inserts, etc. fixen
		$regex = "/`XXX_(.+?)`/s";

		$datenbankinhalt = preg_replace_callback($regex,
			function ($matches) use (&$creation_params)
			{

				$to_hash = $matches[1];

				$md5 = hash("md5", $to_hash);

				if(is_array($creation_params['datenbanken'])) {
					foreach($creation_params['datenbanken'] as $hash => &$name) {
						// Ist der richtige Menüpunkt, ersetzen!
						if($hash === $md5 && is_array($name)) {
							$return = "`XXX_" . $name['name'] . "`";

							// Menupunkt als abgearbeitet markieren (kein array mehr)
							#$name = $name['name'];

							// Ersetzen
							return $return;
						}
					}
				}
				// Löschen
				return $matches[0];
			}
			, $datenbankinhalt);

		file_put_contents($datenbankpath, $datenbankinhalt);
	}

	/**
	 * Unterfunktion von EditPlugin(). Ändert die SQL Deinstallations Datei so, dass sie zu den angegebenen Tabellen passt.
	 * Dabei erstellt oder benennt je nach Bedarf CREATE TABLE/DROP TABLE/INSERT INTO ...-Einträge um.
	 *
	 * @param $datenbankpath string Absoluter oder relativer pfad zur SQL Deinstallations Datei des Plugins.
	 * @uses preg_replace()
	 * @uses preg_replace_callback()
	 * @uses file_get_contents()
	 * @uses file_put_content()
	 * @return void
	 */
	private function EditDatenbankDeinstall($datenbankpath)
	{
		$datenbankinhalt = file_get_contents($datenbankpath);

		$creation_params = &$this->creation_params;

		$regex = "/(DROP .+?`XXX_)(.+?)(`.+?##b_dump##)/s";

		$datenbankinhalt = preg_replace_callback($regex,
			function ($matches) use (&$creation_params)
			{
				$to_hash = $matches[2];

				$md5 = hash("md5", $to_hash);

				if(is_array($creation_params['datenbanken'])) {
					foreach($creation_params['datenbanken'] as $hash => &$name) {
						if($hash === $md5 && !is_array($name)) {
							$return = $matches[1] . $name . $matches[3];

							// Menupunkt als abgearbeitet markieren
							$namensarray = array();
							$namensarray['name'] = $name;
							$name = $namensarray;

							if($name['name'] === $matches[2]) {
								return $matches[0];
							}
							// Ersetzen
							return $return;
						}
					}
				}
				// Löschen tun wir hier nicht, damit bei der deinstallation von geänderten Plugins
				// die alten Datenbanken auch anständig gelöscht werden.
				return $matches[0];
			}
			, $datenbankinhalt);

		// Hinzufügen
		if(is_array($creation_params['datenbanken'])) {
			foreach($creation_params['datenbanken'] as $hash => $name) {
				if(!is_array($name)) {
					$regex = "/(?(?=.*?DROP TABLE)(DROP TABLE)|)/s";
					$repl = "DROP TABLE IF EXISTS `XXX_" . plugincreator_class::ToSecureName($this->creation_params['name']) . "_" . $name . "`; ##b_dump##\n\n$1";

					$datenbankinhalt = preg_replace($regex, $repl, $datenbankinhalt, 1);
				}
			}
		}

		file_put_contents($datenbankpath, $datenbankinhalt);
	}

	/**
	 * Funktion die das bearbeiten eines Plugins anhand der plugincreator_class::creation_params durchführt.
	 *
	 * @param $redirect bool Ob zum plugins Überblick gesprungen werden soll.
	 * @uses plugincreator_class::creation_params
	 * @uses file_get_contents()
	 * @uses file_put_contents
	 * @uses preg_replace
	 * @return mixed Error Code. true = Erfolgreich, kleiner 0 Fehler. -4 führt zu einer Fehlermeldung, dass der Ordner nicht
	 *               die richtigen Rechte freigeschaltet hat.
	 */
	private function EditPlugin($redirect = true)
	{
		$plugin_identifier = $this->creation_params['plugin_identifier'];

		// Alte Plugin ID benutzen um Pfad zu kriegen.
		$sql = sprintf("SELECT pluginclass_datei FROM %s WHERE pluginclass_plugin_id=%d", $this->db_praefix . "papoo_pluginclasses", $this->creation_params['pluginid']);

		$result = $this->db->get_results($sql, ARRAY_A);

		if(empty($result)) {
			foreach (new DirectoryIterator('../plugins') as $fileinfo) {
				if($fileinfo->isDot()) {
					continue;
				}

				if($fileinfo->isDir()) {
					$xmlfilepath = "../plugins/" . $fileinfo->getFilename() . "/" . $fileinfo->getFilename() . ".xml";

					if(is_readable($xmlfilepath)) {
						global $xmlparser;
						$xmlparser->parse($xmlfilepath);

						$plugin = $xmlparser->xml_data;

						$identifier = $this->diverse->sicherer_dateiname($plugin['plugin'][0]['name'][0]['cdata']."_".$plugin['plugin'][0]['version'][0]['cdata']);

						if ($identifier == $plugin_identifier) {
							$pluginfolder = $fileinfo->getFilename();
							break;
						}
					}
				}
			}
		}
		else {
			$phpdatei = $result[0]['pluginclass_datei'];

			$splitdatei = preg_split('/\//', $phpdatei);

			if(empty($splitdatei)) {
				return -3;
			}
			$pluginfolder = $splitdatei[0];
		}

		if(!isset($pluginfolder)) {
			return -4;
		}

		$this->creation_params['plugindir'] = $pluginfolder;
		$xmlfilepath = PAPOO_ABS_PFAD . "/plugins/" . $pluginfolder . "/" . $pluginfolder . ".xml";

		if(!is_writable($xmlfilepath)) {
			return -4;
		}

		$xmlinhalt = file_get_contents($xmlfilepath);

		// Plugin Name, Beschreibung, Autor, etc. berarbeiten
		$this->EditMisc($xmlinhalt);

		// Menupunkte bearbeiten
		$this->EditMenupunkte($xmlinhalt);

		// Module bearbeiten
		$this->EditModule($xmlinhalt);

		// Bearbeitete XML-Datei schreiben
		file_put_contents($xmlfilepath, $xmlinhalt);

		// Datenbanken bearbeiten
		if(preg_match("/(?<!<datenbank>).*?<installation>(.*?)<\\/installation>.*?<deinstallation>(.*?)<\\/deinstallation>.*?(?=<\\/datenbank>)/s", $xmlinhalt, $matches)) {
			// matches[1]; # Hat den Pfad zur installations Datei
			// matches[2]; # Hat den Pfad zur deinstallations Datei

			$datenbankpath_install = PAPOO_ABS_PFAD . "/plugins/" . $matches[1];

			// is_array wurde als flag benutzt um zu wissen ob diese Tabelle schon bearbeitet wurde => wieder normal machen
			if(is_array($this->creation_params['datenbanken'])) {
				foreach($this->creation_params['datenbanken'] as $hash => &$name) {
					if(is_array($name)) {
						$name = $name['name'];
					}
				}
			}

			$datenbankpath_deinstall = PAPOO_ABS_PFAD . "/plugins/" . $matches[2];

			if(!is_writable($datenbankpath_install) or !is_writable($datenbankpath_deinstall)) {
				return -4;
			}

			$this->EditDatenbankInstall($datenbankpath_install);
			$this->EditDatenbankDeinstall($datenbankpath_deinstall);
		}

		if($redirect) {
			header("Location: plugin.php?menuid=" . $this->checked->menuid . "&template=plugincreator/templates/plugincreator_plugins_backend.html");
		}
		return true;
	}

	/**
	 * Unterfunktion von EditPlugin(). Ändert die XML Datei bzgl. der Namen, Autor, Datum, etc tags.
	 *
	 * Setzt außerdem den Inhalt des Datum tags auf das heutige Datum.
	 *
	 * @param $xmlinhalt string Der Inhalt der XML Datei per ref.
	 * @uses DOMDocument
	 * @uses hash
	 * @return void
	 */
	private function EditMisc(&$xmlinhalt)
	{
		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;

		$dom->loadXML($xmlinhalt);

		$root = $dom->documentElement;

		$autor = $root->getElementsByTagName("autor")->item(0);

		$autorname = $autor->getElementsByTagName('name')->item(0);
		if($autorname === NULL) {
			$neuenode = $dom->createElement("name");
			$neuenode->appendChild($dom->createCDATASection($this->creation_params['autor_name']));
			$autor->appendChild($neuenode);
		}
		else {
			$cdatasection = $dom->createCDATASection($this->creation_params['autor_name']);

			while ($autorname->lastChild) {
				$autorname->removeChild($autorname->lastChild);
			}

			$autorname->appendChild($cdatasection);
		}

		$autoremail = $autor->getElementsByTagName('email')->item(0);
		if($autoremail === NULL) {
			$neuenode = $dom->createElement("email");
			$neuenode->appendChild($dom->createCDATASection($this->creation_params['autor_email']));
			$autor->appendChild($neuenode);
		}
		else {
			$cdatasection = $dom->createCDATASection($this->creation_params['autor_email']);

			while ($autoremail->lastChild) {
				$autoremail->removeChild($autoremail->lastChild);
			}

			$autoremail->appendChild($cdatasection);
		}

		$tag = $root->getElementsByTagName('name')->item(0);
		if($tag === NULL) {
			$neuenode = $dom->createElement("name");
			$neuenode->appendChild($dom->createCDATASection($this->creation_params['name']));
			$root->appendChild($neuenode);
		}
		else {
			$cdatasection = $dom->createCDATASection($this->creation_params['name']);

			while ($tag->lastChild) {
				$tag->removeChild($tag->lastChild);
			}

			$tag->appendChild($cdatasection);
		}

		$tag = $root->getElementsByTagName('beschreibung_de')->item(0);
		if($tag === NULL) {
			$neuenode = $dom->createElement("beschreibung_de");
			#$neuenode->nodeValue = '<![CDATA[' . $this->creation_params['description'] . ']]>';
			$root->appendChild($neuenode);

			$cdatasection = $dom->createCDATASection($this->creation_params['description']);
			$neuenode->appendChild($cdatasection);
		}
		else {
			$cdatasection = $dom->createCDATASection($this->creation_params['description']);

			while ($tag->lastChild) {
				$tag->removeChild($tag->lastChild);
			}

			$tag->appendChild($cdatasection);
		}

		$tag = $root->getElementsByTagName('datum')->item(0);
		if($tag === NULL) {
			$neuenode = $dom->createElement("datum");
			$neuenode->nodeValue = date('d.m.Y');
			$root->appendChild($neuenode);
		}
		else {
			$tag->nodeValue = date('d.m.Y');
		}

		$xmlinhalt = $dom->saveXML();

		/*$ersetzungen = array();

		// Namen ändern
		$ersetzungen["/<name>.+?<\\/name>/"] = "<name>" . $this->creation_params['name'] . "</name>";

		// Beschreibung ändern
		$ersetzungen["/<beschreibung_de>\\s*<!\\[CDATA\\[.*?\\]\\]>\\s*<\\/beschreibung_de>/s"] = "<beschreibung_de><![CDATA[" . $this->creation_params['description'] . "]]></beschreibung_de>";

		// Editiert => Datum ändern
		$ersetzungen["/<datum>.*?<\\/datum>/"] = "<datum>" . date('d.m.Y') . "</datum>";

		// Autornamen ändern
		$ersetzungen["/(<autor>.+?<name>).*?(<\\/name>)(?=.+?<\\/autor>)/s"] = "$1" . $this->creation_params['autor_name'] . "$2";

		// Autoremail ändern/hinzufügen
		$ersetzungen["/(<autor>.+?<email>).*?(<\\/email>)(?=.+?<\\/autor>)/s"] = "$1" . $this->creation_params['autor_email'] . "$2";

		foreach($ersetzungen as $regex=>$replace_value)
		{
			$xmlinhalt = preg_replace($regex, $replace_value, $xmlinhalt, 1);
		}*/
	}

	/**
	 * Funktion die die neu generierte .php Datei in das lib/ Verzeichnis schreibt.
	 *
	 * Es wird die vorlage php-Datei aus dem vorlage/ Verzeichnis benutzt und dann
	 * die {__*__} tokens durch anderen Inhalt ersetzt, wie z.B. nötige Aufrufe
	 * für Funktionen des rapid dev -Plugin.
	 *
	 * @uses preg_replace()
	 * @uses file_get_contents()
	 * @uses file_put_contents()
	 * @uses chmod()
	 * @return void
	 */
	private function WritePHPFiles()
	{
		$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/lib/vorlage_class.php");

		$default_content = preg_replace("/vorlage/", plugincreator_class::ToSecureName($this->creation_params['name']), $default_content);
		$default_content = preg_replace("/\{__autor_name__\}/", $this->creation_params['autor_name'], $default_content);
		$default_content = preg_replace("/\{__autor_email__\}/", $this->creation_params['autor_email'], $default_content);

		$replacement = "stristr(\$template2, \"" . $this->maintemplate['name'] . "\") or ";
		foreach($this->subtemplates as $template) {
			$replacement .= "stristr(\$template2, \"" . $template['name'] . "\") or ";
		}
		$replacement = substr($replacement, 0, -4);
		$default_content = preg_replace("/\{__stristr_templates__\}/", $replacement, $default_content);

		$replacement_name = plugincreator_class::ToSecureName($this->maintemplate['name']);
		#$replacement = "\$this->rapid_dev_update(\"$replacement_name\");\n";
		#$replacement .= "\t\t";
		$replacement = "\$this->rapid_dev_insert(\"" . $replacement_name . "\");\n";
		$replacement .= "\$this->rapid_dev_templify(\"" . $replacement_name . "\");\n";

		foreach($this->subtemplates as $template) {
			$replacement_name = plugincreator_class::ToSecureName($template['name']);
			#$replacement .= "\t\t\$this->rapid_dev_update(\"$replacement_name\");\n";
			$replacement .= "\t\t\$this->rapid_dev_insert(\"" . $replacement_name . "\");\n";
			$replacement .= "\t\t\$this->rapid_dev_templify(\"" . $replacement_name . "\");\n";
		}

		$default_content = preg_replace("/\{__xsql_code__\}/", $replacement, $default_content);

		$default_content = preg_replace("/\{__PREFIX__\}/", plugincreator_class::ShortName($this->creation_params['name']), $default_content);

		file_put_contents($this->pluginpath . "lib/" . $this->plugin_dir_name . "_class.php", $default_content);
		chmod($this->pluginpath . "lib/" . $this->plugin_dir_name . "_class.php", 0777);
	}

	/**
	 * Funktion die die message Dateien, jeweils englisch/deutsch - frontend/backend, in das messages/ Verzeichnis schreibt.
	 *
	 * @uses file_put_contents()
	 * @uses chmod()
	 */
	private function WriteMessageFiles()
	{
		file_put_contents($this->pluginpath . "messages/messages_backend_de.inc.php", "<?php\n/**\n\nDeutsche Text-Daten des Plugins \"" . $this->creation_params['name'] . "\" für das Backend\n\n\n*/\n\n#start#\n?>");
		chmod($this->pluginpath . "messages/messages_backend_de.inc.php", 0777);

		file_put_contents($this->pluginpath . "messages/messages_backend_en.inc.php", "<?php\n/**\n\nEnglische Text-Daten des Plugins \"" . $this->creation_params['name'] . "\" für das Backend\n\n\n*/\n\n#start#\n?>");
		chmod($this->pluginpath . "messages/messages_backend_en.inc.php", 0777);

		file_put_contents($this->pluginpath . "messages/messages_frontend_de.inc.php", "<?php\n/**\n\nDeutsche Text-Daten des Plugins \"" . $this->creation_params['name'] . "\" für das Frontend\n\n\n*/\n\n#start#\n?>");
		chmod($this->pluginpath . "messages/messages_frontend_de.inc.php", 0777);

		file_put_contents($this->pluginpath . "messages/messages_frontend_en.inc.php", "<?php\n/**\n\nEnglische Text-Daten des Plugins \"" . $this->creation_params['name'] . "\" für das Frontend\n\n\n*/\n\n#start#\n?>");
		chmod($this->pluginpath . "messages/messages_frontend_en.inc.php", 0777);
	}

	/**
	 * Funktion die die SQL Dateien in den sql/ Ordner schreibt.
	 *
	 * @uses file_put_contents()
	 * @uses chmod()
	 */
	private function WriteSQLFiles()
	{
		$installdata = "";
		$deinstalldata = "";

		if(is_array($this->creation_params['datenbanken'])) {
			foreach($this->creation_params['datenbanken'] as $tabelle) {
				$tabellenname = "XXX_plugin_" . plugincreator_class::ShortName($this->plugin_dir_name) . "_" . $tabelle;
				$installdata .= "DROP TABLE IF EXISTS `" . $tabellenname . "`; ##b_dump##\n";
				$installdata .= "CREATE TABLE `" . $tabellenname . "` (\n\n) ENGINE=MyISAM; ##b_dump##\n\n";

				$deinstalldata .= "DROP TABLE IF EXISTS `" . $tabellenname . "`; ##b_dump##\n";
			}

			$tabellenname = "XXX_plugin_" . plugincreator_class::ShortName($this->plugin_dir_name) . "_" . plugincreator_class::ShortName($this->plugin_dir_name) . "_form";
			$deinstalldata .= "DROP TABLE IF EXISTS `" . $tabellenname . "`; ##b_dump##\n";

			foreach($this->subtemplates as $template) {
				$tabellenname = "XXX_plugin_" . plugincreator_class::ShortName($this->plugin_dir_name) . "_" . plugincreator_class::ShortName($template['name']) . "_form";
				$deinstalldata .= "DROP TABLE IF EXISTS `" . $tabellenname . "`; ##b_dump##\n";
			}
		}

		/*
		CREATE PROCEDURE drop_like()
		BEGIN
		SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
		AS statement FROM information_schema.tables
		WHERE table_schema = 'root' AND table_name LIKE '%kalender%';
		END;


		CALL drop_like;
		*/

		// alle Tabellen mit dem prefix plugin_<pluginprefix>_ löschen
		#$deinstalldata = "SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
		#AS statement FROM information_schema.tables
		#WHERE table_schema = 'database_name' AND table_name LIKE 'myprefix_%';";

		file_put_contents($this->pluginpath . "sql/install.sql", $installdata);
		chmod($this->pluginpath . "sql/install.sql", 0777);
		file_put_contents($this->pluginpath . "sql/deinstall.sql", $deinstalldata);
		chmod($this->pluginpath . "sql/deinstall.sql", 0777);
	}

	/**
	 * Funktion die die CSS Datei Leer in das css/ Verzeichnis schreibt.
	 *
	 * @uses file_put_contents()
	 * @uses chmod()
	 */
	private function WriteCSSFile()
	{
		$csscontent_frontend = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/css/frontend.css");
		file_put_contents($this->pluginpath . "css/frontend.css", $csscontent_frontend);
		chmod($this->pluginpath . "css/frontend.css", 0777);

		$csscontent_backend = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/css/backend.css");
		file_put_contents($this->pluginpath . "css/backend.css", $csscontent_backend);
		chmod($this->pluginpath . "css/backend.css", 0777);
	}

	/**
	 * Unterfunktion von CreatePlugin() welche die absehbar benötigten Template Dateien vorbereitet.
	 *
	 * @uses $plugincreator_class::creation_params
	 * @uses file_put_contents()
	 * @uses chmod()
	 */
	private function WriteTemplateFiles()
	{
		$maintemplate = array();
		$maintemplate['template'] = $this->plugin_dir_name . "/templates/" . $this->plugin_dir_name . "_backend.html";
		$maintemplate['name'] = $this->plugin_dir_name;
		$this->maintemplate = $maintemplate;

		$this->subtemplates = array();
		if(is_array($this->creation_params['menupunkte'])) {
			foreach($this->creation_params['menupunkte'] as $menupunkt) {
				$subtemplate = array();
				$subtemplate['template'] = $this->plugin_dir_name . "/templates/" . plugincreator_class::ToSecureName($menupunkt) . "_backend.html";
				$subtemplate['name'] = plugincreator_class::ToSecureName($menupunkt);
				$this->subtemplates[] = $subtemplate;
			}
		}

		$this->modultemplates = array();
		if(is_array($this->creation_params['module'])) {
			foreach($this->creation_params['module'] as &$modul) {
				$modultemplate = array();
				$modultemplate['template'] = $this->plugin_dir_name . '/templates/mod_' . plugincreator_class::ToSecureName($modul['name']) . '_frontend.html';
				$modultemplate['name'] = 'mod_' . plugincreator_class::ToSecureName($modul['name']) . '_frontend';
				$this->modultemplates[] = $modultemplate;
			}
		}

		/*$nl = "\n";
		$default_content = "{if \$IS_ADMIN}{*<!-- Wir nur in der Admin angezeigt -->*}" . $nl;
		$default_content .= "{*<!-- Hier kommt der Kopf rein-->*}" . $nl;
		$default_content .= "{include file=head.inc.utf8.html}" . $nl . $nl;
		$default_content .= "<!-- Men� kommt hier rein-->" . $nl;
		$default_content .= "{include file=menu.inc.html}" . $nl . $nl;
		$default_content .= '<div class="artikel">' . $nl . $nl;maintemplate
		$default_content .= "</div>" . $nl . $nl;
		$default_content .= "{*<!-- Hier kommt der Fuss rein-->*}" . $nl;
		$default_content .= "{include file=foot.inc.html}" . $nl;
		$default_content .= "{/if}";*/

		$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/templates/vorlage_backend.html");
		$default_content = preg_replace("/vorlage/", plugincreator_class::ToSecureName($this->creation_params['name']), $default_content);
		$default_content = preg_replace("/\{__PREFIX__\}/", plugincreator_class::ShortName($this->creation_params['name']), $default_content);
		$default_content = preg_replace("/\{__TEMPLATE_NAME__\}/", plugincreator_class::ShortName($this->creation_params['name']), $default_content);

		file_put_contents(PAPOO_ABS_PFAD . "/plugins/" . $this->maintemplate['template'], $default_content);
		chmod(PAPOO_ABS_PFAD . "/plugins/" . $this->maintemplate['template'], 0777);

		foreach($this->subtemplates as $template) {
			$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/templates/vorlage_backend.html");
			$default_content = preg_replace("/vorlage/", plugincreator_class::ToSecureName($this->creation_params['name']), $default_content);
			$default_content = preg_replace("/\{__PREFIX__\}/", plugincreator_class::ShortName($this->creation_params['name']), $default_content);
			$default_content = preg_replace("/\{__TEMPLATE_NAME__\}/", plugincreator_class::ShortName($template['name']), $default_content);

			file_put_contents(PAPOO_ABS_PFAD . "/plugins/" . $template['template'], $default_content);
			chmod(PAPOO_ABS_PFAD . "/plugins/" . $template['template'], 0777);
		}

		$default_content = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/templates/mod_vorlage_frontend.html");

		foreach($this->modultemplates as $template) {
			$content = preg_replace("/mod_vorlage_frontend/", $template['name'], $default_content);

			file_put_contents(PAPOO_ABS_PFAD . "/plugins/" . $template['template'], $content);
			chmod(PAPOO_ABS_PFAD . "/plugins/" . $template['template'], 0777);
		}
	}

	/**
	 * Schreibt die XML Datei des zu erstellenden Plugins mit Hilfe der $creation_params.
	 *
	 * @uses plugincreator_class::creation_params
	 * @uses file_put_contents()
	 * @uses chmod()
	 */
	private function WriteXMLFile()
	{
		$data = file_get_contents(PAPOO_ABS_PFAD . "/plugins/plugincreator/vorlage/vorlage.xml");

		// Nur den Namen auf den eigentlichen Namen, nicht den klein gemachten, setzen.
		// In CDATA setzen, damit z.B. M&M Plugin kein Fehler ausgibt.
		$data = preg_replace("/vorlage/", $this->creation_params['name'], $data, 1);

		$data = preg_replace("/vorlage/", $this->plugin_dir_name, $data);

		file_put_contents($this->pluginpath . $this->plugin_dir_name . ".xml", $data);
		chmod($this->pluginpath . $this->plugin_dir_name . ".xml", 0777);
	}

	/**
	 * Löscht ein Verzeichnis rekursiv.
	 *
	 * @param $dir Das zu löschende Verzeichnis
	 * @return bool
	 */
	function RecursiveDeleteDirectory($dir)
	{
		if(!is_writable($dir) and is_dir($dir) || is_link($dir)) {
			throw new Exception();
		}

		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir) || is_link($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;

			$ubergabe = $dir . "/" . $item;

			if (!$this->RecursiveDeleteDirectory($ubergabe)) {
				chmod($ubergabe, 0777);
				if(!$this->RecursiveDeleteDirectory($ubergabe)) {
					return false;
				}
			};
		}
		return rmdir($dir);
	}

	/**
	 * Löscht ein Plugin.
	 *
	 * Das Plugin wird zuerst deinstalliert
	 * und dann dessen Verzeichnis von dir dieser Methode hier gelöscht.
	 *
	 * @param $plugin_id integer ID des Plugins welches deinstalliert werden soll.
	 * @param $plugin_identifier string Ein string der das Plugin im plugins/ Ordner eindeutig indentifiziert.
	 * @return int Error code. True oder -4 (Keine Permissions).
	 */
	private function DeletePlugin($plugin_id, $plugin_identifier)
	{
		if($plugin_id != 0) {
			$this->DeinstallPlugin($plugin_id);
		}

		foreach (new DirectoryIterator('../plugins') as $fileinfo) {
			if($fileinfo->isDot()) {
				continue;
			}

			if($fileinfo->isDir()) {
				$xmlfilepath = "../plugins/" . $fileinfo->getFilename() . "/" . $fileinfo->getFilename() . ".xml";

				if(is_readable($xmlfilepath)) {
					global $xmlparser;
					$xmlparser->parse($xmlfilepath);

					$plugin = $xmlparser->xml_data;

					$identifier = $this->diverse->sicherer_dateiname($plugin['plugin'][0]['name'][0]['cdata']."_".$plugin['plugin'][0]['version'][0]['cdata']);
					if ($identifier == $plugin_identifier) {
						$pluginfolder = $fileinfo->getFilename();
						break;
					}
				}
			}
		}

		$pluginfolder = PAPOO_ABS_PFAD . "/plugins/" . $pluginfolder;

		if(!is_dir($pluginfolder)) {
			return -4;
		}

		// Erfolg => true; Kein Erfolg => Nicht genug Rechte Fehler anzeigen mittels -4 Rückgabewert.
		try {
			$this->RecursiveDeleteDirectory($pluginfolder);
		}
		catch(Exception $e) {
			return -4;
		}

		return true;
	}

	/**
	 * Deinstalliert ein Plugin anhand dessen ID.
	 *
	 * Wird benötigt da die Original Methode in der intern_plugin Klasse per
	 * header("Location:") ein redirect eingebaut hat.
	 *
	 * @param $plugin_id integer ID des Plugins welches deinstalliert werden soll.
	 */
	private function DeinstallPlugin($plugin_id)
	{
		$this->intern_plugin->delete_menue($plugin_id);
		$this->intern_plugin->delete_modul_entry($plugin_id);
		$this->intern_plugin->delete_dbtables($plugin_id);

		if (isset($this->mv->is_mv_installed) && $this->mv->is_mv_installed) {
			$this->intern_plugin->delete_mv_dbtables($plugin_id);
		}

		$this->intern_plugin->delete_plugin_entry($plugin_id);
		$this->intern_plugin->delete_pluginclass_entry($plugin_id);

		// CSS-Datei f�r Backend-Men� neu erstellen
		$this->intern_plugin->menuintcss->make_menuintcss();
		// PluginsCSS-Klasse einbinden
		require_once(PAPOO_ABS_PFAD."/lib/classes/pluginscss_class.php");
		$this->intern_plugin->pluginscss = new pluginscss_class();

		// CSS-Datei f�r Plugins-CSS neu erstellen
		$this->intern_plugin->pluginscss->make_pluginscss();
	}

	/**
	 * Installiert ein Plugin anhand dessen ID neu.
	 * Dazu werden die in papoo schon vorkommenden Methoden benutzt, soweit mögl.,
	 * allerdings kann die Original Plugin installations Methode nicht benutzt werden,
	 * da diese per header("Location") ein redirect eingebaut hat.
	 *
	 * @param $plugin_id integer ID des plugins welches neuinstalliert werden soll.
	 * @param $plugin_identifier
	 * @throws Exception Plugin ist schon installiert
	 * @uses plugincreator_class::DeinstallPlugin()
	 * @uses intern_plugin
	 */
	private function ReinstallPlugin($plugin_id, $plugin_identifier)
	{
		$this->DeinstallPlugin($plugin_id);
		$this->InstallPlugin($plugin_identifier);
	}

	/**
	 * Installiert ein Plugin anhand seines 'plugin_identifier'-s, einer Verbindung aus Plugin Name und Version.
	 *
	 * @param $plugin_identifier
	 * @throws Exception Plugin ist schon installiert
	 */
	private function InstallPlugin($plugin_identifier)
	{
		// Sofort wieder neuinstallieren via der Funktion die auch beim
		// installieren im Plugin Manager benutzt wird.
		$this->intern_plugin->read_sprachen();
		$this->intern_plugin->read_installed();
		$this->intern_plugin->read_lokal();
		$_SESSION['dbp'] = [];

		$plugin_array = array_filter($this->intern_plugin->plugin_lokal, function($plugin) use ($plugin_identifier) {
			return $plugin_identifier == $this->intern_plugin->diverse->sicherer_dateiname($plugin['plugin'][0]['name'][0]['cdata']."_".$plugin['plugin'][0]['version'][0]['cdata']);
		});

		if(!empty($plugin_array)) {
			$plugin = array_shift($plugin_array);
			$plugin_name = $plugin['plugin'][0]['name'][0]['cdata'];

			if($this->PluginIsInstalled($plugin_name)) {
				throw new Exception("Plugin $plugin_name ist schon installiert!");
			}
			IfNotSetNull($plugin['plugin'][0]['mtop']);
			IfNotSetNull($plugin['plugin'][0]['modul']);

			// .. das ist also das zu installierende Plugin
			// 1. Plugin in Tabelle papoo_plugin eintragen
			if (($plugin['plugin'][0]['mtop'][0]['cdata'] != 1)) {
				$parentx=54;
			}
			else {
				$parentx = Null;
			}
			$plugin_id = $this->intern_plugin->make_plugin_entry($plugin['plugin'][0]);

			// 2. Men�-Eintr�ge vornehmen
			if ($plugin['plugin'][0]['menue']) {
				foreach($plugin['plugin'][0]['menue'] as $menue) {
					// 2.A Hauptmen�-Punkt anlegen
					$parent_id = $this->intern_plugin->make_menue($menue, $plugin_id,$parentx,$plugin['plugin'][0]['mtop'][0]['cdata']);

					// 2.B Wenn Submen�-Punkte vorhanden, diese auch anlegen
					IfNotSetNull($menue['submenue']);
					if ($menue['submenue']) {
						foreach($menue['submenue'] as $submenue) {
							$parent_id2=$this->intern_plugin->make_menue($submenue, $plugin_id, $parent_id,$plugin['plugin'][0]['mtop'][0]['cdata']);
							if ($submenue['submenue2']) {
								foreach($submenue['submenue2'] as $submenue2) {
									$this->intern_plugin->make_menue($submenue2, $plugin_id, $parent_id2,0);
								}
							}
						}
					}
				}
			}

			// 3. DB-Tabelle des Plugins installieren einbinden
			// !!!
			if ($plugin['plugin'][0]['datenbank'][0]['installation']) {
				$this->intern_plugin->dumpnrestore->restore('../plugins/'.$plugin['plugin'][0]['datenbank'][0]['installation'][0]['cdata']);
			}

			// 4. Klasse(n) einbinden
			if ($plugin['plugin'][0]['klasse']) {
				foreach($plugin['plugin'][0]['klasse'] as $klasse) {
					$this->intern_plugin->make_pluginclass_entry($klasse, $plugin_id);
				}
			}

			// 5. Modul(e) einbinden
			if ($plugin['plugin'][0]['modul']) {
				foreach($plugin['plugin'][0]['modul'] as $modul) {
					$this->intern_plugin->make_modul_entry($modul, $plugin_id);
				}
			}

			// CSS-Datei f�r Backend-Men� neu erstellen
			$this->intern_plugin->menuintcss->make_menuintcss();

			// PluginsCSS-Klasse einbinden
			require_once(PAPOO_ABS_PFAD."/lib/classes/pluginscss_class.php");
			$this->intern_plugin->pluginscss = new pluginscss_class();

			// CSS-Datei f�r Plugins-CSS neu erstellen
			$this->intern_plugin->pluginscss->make_pluginscss();
		}
	}

	/**
	 * Füllt das plugincreator_create_backend template mit den Informationen des gewählten plugins aus der Plugin Liste.
	 *
	 * @param $plugin_id integer ID des gewählten plugins
	 * @param $plugin_identifier
	 * @return bool|int Error Code. True bei Erfolg, andernfalls ein Wert kleiner Null der den Fehler
	 *              beschreibt und zur Anzeige einer Fehlermeldung führt.
	 */
	private function FillContentEditPlugin($plugin_id, $plugin_identifier)
	{
		$plugin_info = ["pluginid" => $plugin_id, "plugin_identifier" => $plugin_identifier];

		$sql = sprintf("SELECT pluginclass_datei FROM %s WHERE pluginclass_plugin_id=%d", $this->db_praefix . "papoo_pluginclasses", $plugin_id);

		$result = $this->db->get_results($sql, ARRAY_A);

		$pluginfolder = NULL;

		// Plugin ist nicht installiert!
		if(empty($result)) {
			foreach (new DirectoryIterator('../plugins') as $fileinfo) {
				if($fileinfo->isDot()) {
					continue;
				}

				if($fileinfo->isDir()) {
					$xmlfilepath = "../plugins/" . $fileinfo->getFilename() . "/" . $fileinfo->getFilename() . ".xml";

					if(is_readable($xmlfilepath)) {
						global $xmlparser;
						$xmlparser->parse($xmlfilepath);

						$plugin = $xmlparser->xml_data;

						$identifier = $this->diverse->sicherer_dateiname($plugin['plugin'][0]['name'][0]['cdata']."_".$plugin['plugin'][0]['version'][0]['cdata']);
						if ($identifier == $plugin_identifier) {
							$pluginfolder = $fileinfo->getFilename();
							break;
						}
					}
				}
			}
		}
		else {
			$phpdatei = $result[0]['pluginclass_datei'];

			$splitdatei = preg_split('/\//', $phpdatei);

			if(empty($splitdatei)) {
				return -2;
			}
			$pluginfolder = $splitdatei[0];
		}

		$xmlfile = PAPOO_ABS_PFAD . "/plugins/" . $pluginfolder . "/" . $pluginfolder . ".xml";

		if(!file_exists($xmlfile) or !is_writable($xmlfile)) {
			return -4;
		}

		global $xmlparser;
		$xml = $xmlparser;

		$xml->parse($xmlfile);

		$plugin_info['name'] = $xml->xml_data['plugin'][0]['name'][0]['cdata'];
		$plugin_info['description'] = $xml->xml_data['plugin'][0]['beschreibung_de'][0]['cdata'];
		$plugin_info['version'] = $xml->xml_data['plugin'][0]['version'][0]['cdata'];
		$plugin_info['datum'] = $xml->xml_data['plugin'][0]['datum'][0]['cdata'];

		$plugin_info['autor']['name'] = $xml->xml_data['plugin'][0]['autor'][0]['name'][0]['cdata'];
		$plugin_info['autor']['email'] = $xml->xml_data['plugin'][0]['autor'][0]['email'][0]['cdata'];

		if(isset($xml->xml_data['plugin'][0]['menue'][0]['submenue']) and is_array($xml->xml_data['plugin'][0]['menue'][0]['submenue'])) {
			$i = 0;
			foreach($xml->xml_data['plugin'][0]['menue'][0]['submenue'] as $menupunkt) {
				$m = array();
				$m['name'] = $menupunkt['eintrag_de'][0]['cdata'];
				$m['rid'] = "plugincreator_menu" . $i++;
				$m['hash'] = hash("md5", $menupunkt['eintrag_de'][0]['cdata'] . $menupunkt['eintrag_en'][0]['cdata'] . $menupunkt['icon'][0]['cdata'] . $menupunkt['link'][0]['cdata']);

				$plugin_info['menu'][] = $m;
			}
		}

		if(isset($xml->xml_data['plugin'][0]['modul']) and is_array($xml->xml_data['plugin'][0]['modul'])) {
			$i = 0;
			foreach($xml->xml_data['plugin'][0]['modul'] as $modul) {
				$rid = "plugincreator_modul_" . $i;
				$modul_array = array();
				$modul_array['name'] = $modul['name_de'][0]['cdata'];
				$modul_array['desc'] = $modul['beschreibung_de'][0]['cdata'];
				$modul_array['rid'] = $rid;
				#$modul_array['hash'] = ($modul['datei'][0]['cdata'] . $modul['name_de'][0]['cdata'] . $modul['beschreibung_de'][0]['cdata'] . $modul['name_en'][0]['cdata'] . $modul['beschreibung_en'][0]['cdata'] . $modul['modus'][0]['cdata']);
				$modul_array['hash'] = hash("md5", $modul['datei'][0]['cdata'] . $modul['name_de'][0]['cdata'] . $modul['beschreibung_de'][0]['cdata'] . $modul['name_en'][0]['cdata'] . $modul['beschreibung_en'][0]['cdata'] . $modul['modus'][0]['cdata']);
				$plugin_info['modul'][] = $modul_array;
				$i++;
			}
		}

		// In xxx_install.sql gehen um rauszufinden welche Tabellen wir haben
		$installfile = PAPOO_ABS_PFAD . "/plugins/" . $xml->xml_data['plugin'][0]['datenbank'][0]['installation'][0]['cdata'];

		if(file_exists($installfile)) {
			$installfile = file_get_contents($installfile);

			$matches = array();
			if(preg_match_all("/CREATE TABLE `XXX_([^`]+)`/", $installfile, $matches)) {
				$i = 0;
				foreach($matches[1] as $match) {
					$rid = "plugincreator_datenbank_" . $i;
					$tabelle = array();
					$tabelle['name'] = $match;
					$tabelle['rid'] = $rid;
					$tabelle['hash'] = hash("md5", $tabelle['name']);
					$plugin_info['datenbank'][] = $tabelle;
					$i++;
				}
			}
		}

		$plugin_info['plugin_dir'] = $pluginfolder;

		$_SESSION['plugin']['plugincreator']['editplugin_info'] = $plugin_info;

		$sql = sprintf("SELECT plugin_menuids FROM %s WHERE plugin_name LIKE '%%Plugin Creator%%'", $this->db_praefix . "papoo_plugins", 'Plugin Creator');

		$results = $this->db->get_results($sql, ARRAY_A);

		$results = preg_split("/[\\s]/", $results[0]['plugin_menuids']);

		#$_SESSION['plugin']['plugincreator']['menuid_create'] = $results[2];
		$_SESSION['plugin']['plugincreator']['menuid_create_plugins'] = $results[3];

		header("Location: plugin.php?menuid=" . $this->checked->menuid . "&template=plugincreator/templates/plugincreator_create_backend.html");
		return true;
	}

	/**
	 * @ignore
	 */
	function post_papoo()
	{

	}

	/**
	 * @ignore
	 */
	function output_filter()
	{

	}
}

$plugincreator = new plugincreator_class();
