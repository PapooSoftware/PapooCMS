<?php
/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.3                  #
 * #####################################
 */

/**
 * Class intern_styles_class
 */
class intern_styles_class
{
	/**
	 * intern_styles_class constructor.
	 */
	function __construct()
	{
		global $cms;
		$this->cms = &$cms;

		global $db;
		$this->db = &$db;
		global $db_praefix;
		$this->db_praefix = $db_praefix;

		global $user;
		$this->user = &$user;

		global $content;
		$this->content = &$content;

		global $checked;
		$this->checked = &$checked;

		global $diverse;
		$this->diverse = &$diverse;

		global $ctemplate;
		$this->ctemplate=$ctemplate;

		global $dumpnrestore;
		$this->dumpnrestore = &$dumpnrestore;

		IfNotSetNull($this->content->template['css_themessage']);
		IfNotSetNull($this->content->template['template_cont_start']);
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		$this->user->check_access();

		$this->content->template['css_jquery_first'] = "0";
		IfNotSetNull($this->checked->tab);
		IfNotSetNull($this->checked->style_bereich);
		IfNotSetNull($this->checked->action);
		if (is_numeric($this->checked->tab)) {
			$this->content->template['css_jquery_first'] = $this->checked->tab;
		}

		// Abfrage nach "Bereich" (also Styles, Module, CSS..)
		switch($this->checked->style_bereich) {
		case "css":
			#$this->switch_css($this->checked->action);
			break;

		case "module":
			$this->ctemplate->do_admin();
			$this->switch_module($this->checked->action);
			$this->content->template['css_jquery_first'] = "0";
			break;

		case "styles":
		default:
			$this->ctemplate->do_admin();
			$this->switch_module($this->checked->action);
			$this->switch_styles($this->checked->action);
			$this->css_do_change();
			break;
		}

	}

	/**
	 * @param string $action
	 */
	function switch_module($action="")
	{
		global $module;

		IfNotSetNull($this->checked->mod_style_id);
		IfNotSetNull($this->checked->style_id);
		IfNotSetNull($this->checked->module_aktion);
		switch ($action) {
		case "module_get_xml":
			if($module->helper_make_xml($this->checked->mod_style_id)) {

				exit;
			}
			else {

				$this->switch_module();
			}
			break;

		case "":
		default:
			$module->switch_back($this->checked->module_aktion, $this->checked->style_id);
			$this->content->template['styles']['style_id'] = $this->checked->mod_style_id;
			$this->content->template['styles']['style_data'] = $this->styles_data($this->checked->style_id);
			$this->content->template['styles']['template_weiche'] = "MODULE_LISTE";
			break;
		}
	}

	/**
	 * @param string $action
	 */
	function switch_styles($action="")
	{

		switch ($action)
		{
		case "style_edit":
			$this->styles_edit($this->checked->style_id);
			$this->switch_styles($_GET['action']);
			break;

		case "style_select":
			$this->content->template['styles']['style_id'] = $this->checked->style_id;
			$this->content->template['styles']['style_data'] = $this->styles_data($this->checked->style_id);
			$this->content->template['styles']['template_weiche'] = "BEREICHE_LISTE";
			break;

		case "style_multi_aktiv":
			IfNotSetNull($this->checked->styles_deaktiv);
			$this->styles_multi_aktiv($this->checked->styles_aktiv, $this->checked->styles_deaktiv);
			$this->switch_styles();
			break;

		case "style_add":
			$this->styles_aktivieren($this->checked->style_name);
			$this->switch_styles();
			break;

		case "style_make_standard":
			$this->styles_make_standard($this->checked->style_id);
			$this->switch_styles();
			break;

		case "style_reset":
			$this->content->template['styles']['style_id'] = $this->checked->style_id;
			$this->content->template['styles']['style_data'] = $this->styles_data($this->checked->style_id);
			// ist Style Standardstyle; sollte ohne Modifizierung der Seite clientseitig nicht nötig sein
			if(isset($this->content->template['styles']['style_data']['standard_style']) &&
				$this->content->template['styles']['style_data']['standard_style'] == 1)
			{
				$this->content->template['styles']['style_pfad'] = $path = sprintf("%s/styles/%s/",
					PAPOO_ABS_PFAD,
					$this->content->template['styles']['style_data']['style_pfad']
				);
				$filename = $path."sql/dumpnrestore.sql";
				// das break für case style_reset nur hier drin, damit default ausgeführt wird,
				// falls der Style keine sql Datei hat
				if(is_file($filename)) {
					// Zurücksetzen wurde bestätigt
					if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['style_reset_form_submit'])) {
						// Passwort des Benutzers root merken
						$password = $this->db->get_var("SELECT `password` FROM `".$this->cms->tbname['papoo_user']."` WHERE `username`='root'");
						// Initialisierung der Datenbank durchführen
						$this->dumpnrestore->restore($filename);
						// Passwort wiederherstellen
						$this->db->query("UPDATE `".$this->cms->tbname['papoo_user']."` SET `password`='".$password."' WHERE `username`='root';");
						// Rückmeldung an das Template
						$this->content->template['styles']['template_weiche'] = "STYLE_RESET_DONE";
						$this->content->template['css_themessage'] = $this->content->template['style_reset_done'];
					}
					else {
						// Warnung ausgeben
						$this->content->template['styles']['template_weiche'] = "STYLE_RESET";
					}
					break;
				}
				else {
					$this->content->template['css_themessage'] = $this->content->template['style_reset_no_sql'];
				}
			}
			break;

		case "":
		default:
			$this->content->template['styles']['template_weiche'] = "STYLES_LISTE";
			$this->content->template['styles']['liste_aktiv'] = $this->helper_styles_dumpnrestore_test($this->helper_styles_screenshot_test($this->styles_liste()));
			$this->content->template['styles']['liste_deaktiv'] = $this->helper_styles_screenshot_test($this->styles_liste_deaktiv());
			//

			break;
		}
	}

	/**
	 * @return array|void
	 */
	function styles_liste()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY style_name ASC ",
			$this->db_praefix."papoo_styles"
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param int $style_id
	 * @return array|void
	 */
	function styles_data($style_id = 0)
	{
		$temp_return = array();

		if ($style_id) {
			$sql = sprintf("SELECT * FROM %s WHERE style_id='%d'",
				$this->db_praefix."papoo_styles",
				$style_id
			);
			$temp_return = $this->db->get_row($sql, ARRAY_A);
		}

		return $temp_return;
	}

	/**
	 * @param int $style_id
	 */
	function styles_edit($style_id = 0)
	{
		if ($style_id AND !empty($this->checked->style_name)) {
			$sql = sprintf("UPDATE %s SET style_name='%s' WHERE style_id='%d'",
				$this->db_praefix."papoo_styles",
				$this->db->escape($this->checked->style_name),
				$style_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @return array
	 */
	function styles_liste_deaktiv()
	{
		$temp_return = array();

		// Liste der Verzeichnisse in /styles erstellen
		$verzeichnisse = array ();
		$pfad = PAPOO_ABS_PFAD."/styles/";
		$handle = @opendir($pfad);

		while(($file = readdir($handle)) !== false ) {
			if (@is_dir($pfad . $file))
				if (@strpos( "XXX" . $file, ".") != 3)
					$verzeichnisse[] = $file; // Nur nicht-unsichtbare Verzeichnisse aufnehmen
		}

		// Liste der bereits aktiven Styles erstellen
		$sql = sprintf( "SELECT style_pfad FROM %s ", $this->db_praefix."papoo_styles");
		$temp_aktive_styles = $this->db->get_col($sql);


		// aus obigen beiden Listen, die Liste nicht aktiver Styles erstellen
		if (!empty($verzeichnisse)) {
			foreach ($verzeichnisse as $verzeichnis ) {
				$temp_test_datei = PAPOO_ABS_PFAD."/styles/".$verzeichnis."/css/_index.css";

				if (!in_array($verzeichnis, $temp_aktive_styles) AND file_exists($temp_test_datei)) {
					$temp_return[] = array("style_pfad" => $verzeichnis);
				}
			}
		}
		return $temp_return;
	}

	/**
	 * @param string $name
	 */
	function styles_aktivieren($name = "")
	{
		if (is_dir(PAPOO_ABS_PFAD."/styles/".$name)) {
			// Eintrag in Style-Tabelle schreiben
			$sql = sprintf("INSERT INTO %s SET style_name='%s', style_pfad='%s'",
				$this->db_praefix."papoo_styles",
				$this->db->escape($name),
				$this->db->escape($name)
			);
			$this->db->query($sql);

			$temp_style_id = $this->db->insert_id;

			// Module anhand der XML-Datei ermitteln
			$temp_xml_datei = PAPOO_ABS_PFAD."/styles/".$name."/module.xml";
			if (!file_exists($temp_xml_datei)) {
				$temp_xml_datei = PAPOO_ABS_PFAD."/styles_default/module.xml";
			}

			global $xmlparser;

			$xmlparser->parse($temp_xml_datei);
			$temp_xml_array = $xmlparser->xml_data;

			$temp_xml_array = $temp_xml_array['modul_liste'][0]['modul'];

			// Liste der Mod-IDs und der mod_dateien auslesen (für Ermittlung der Mod-ID aus Angabe der mod_datei in XML
			$sql = sprintf("SELECT mod_id, mod_datei FROM %s", $this->db_praefix."papoo_module");
			$temp_module_roh = $this->db->get_results($sql, ARRAY_A);
			$temp_module = array();
			foreach ($temp_module_roh as $temp_modul) {
				$temp_module[$temp_modul['mod_id']] = $temp_modul['mod_datei'];
			}


			// Styles-Module in Tabelle eintragen
			$temp_order_id = 1;
			if (!empty($temp_xml_array)) {
				foreach ($temp_xml_array as $temp_xml_modul) {
					if ($temp_mod_id = array_search($temp_xml_modul['mod_datei'][0]['cdata'], $temp_module)) {
						$sql = sprintf("INSERT INTO %s SET stylemod_style_id='%d', stylemod_mod_id='%d', stylemod_bereich_id='%d', stylemod_order_id='%d'",
							$this->db_praefix."papoo_styles_module",
							$temp_style_id,
							$temp_mod_id,
							$temp_xml_modul['bereich_id'][0]['cdata'],
							$temp_order_id++
						);
						$this->db->query($sql);
					}
				}
			}

			// Styles-Module des neuen Styles einmal neu sortieren
			global $module;
			for ($i = 1; $i <= 5; $i++) {
				$module->admin_bereich_sortieren($temp_style_id, $i);
			}
		}
	}

	/**
	 * @param int $style_id
	 */
	function styles_deaktivieren($style_id = 0)
	{
		if ($style_id) {
			// Eintraege in Style-Module-Tabelle loeschen
			$sql = sprintf("DELETE FROM %s WHERE stylemod_style_id='%d'",
				$this->db_praefix."papoo_styles_module",
				$style_id
			);
			$this->db->query($sql);

			// Eintrag in Style-Tabelle loeschen
			$sql = sprintf("DELETE FROM %s WHERE style_id='%d'",
				$this->db_praefix."papoo_styles",
				$style_id
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param array $styles_aktiv
	 * @param array $styles_deaktiv
	 */
	function styles_multi_aktiv($styles_aktiv = array(), $styles_deaktiv = array())
	{
		if (!empty($styles_aktiv)) {
			// Liste aktiver Styles bearbeiten (nicht mehr aktive deaktivieren)
			// .. Liste bisher aktiver Style-IDs auslesen
			$sql = sprintf("SELECT style_id FROM %s",
				$this->db_praefix."papoo_styles"
			);
			$temp_style_ids = $this->db->get_col($sql);

			if (!empty($temp_style_ids)) {
				foreach ($temp_style_ids as $temp_style_id) {
					if (!in_array($temp_style_id, $styles_aktiv)) {
						//echo ".. Style deaktivieren ID: ".$temp_style_id."<br />\n";
						$this->styles_deaktivieren($temp_style_id);
					}
				}
			}

			// Liste bisher nicht aktiver Styles bearbeiten (.. diese aktivieren)
			if (!empty($styles_deaktiv)) {
				foreach ($styles_deaktiv as $temp_style_name) {
					$this->styles_aktivieren($temp_style_name);
				}
			}
		}
	}

	/**
	 * @param int $style_id
	 */
	function styles_make_standard($style_id = 0)
	{
		if ($style_id) {
			// Testen ob Style mit ID $style_id existiert
			$sql = sprintf("SELECT COUNT(style_id) FROM %s WHERE style_id='%d'",
				$this->db_praefix."papoo_styles",
				$style_id
			);
			$temp_style_exists = $this->db->get_var($sql);

			// Wenn Style existiert, deisen zum Standard machen
			if ($temp_style_exists) {
				$this->db->csrfok = true;

				$sql = sprintf("UPDATE %s SET standard_style='0'",$this->db_praefix."papoo_styles");
				$this->db->query($sql);

				$sql = sprintf("UPDATE %s SET standard_style='1' WHERE style_id='%d'",
					$this->db_praefix."papoo_styles",
					$style_id
				);
				$this->db->query($sql);

				$this->db->csrfok = false;

				$this->diverse->write_to_file("/interna/templates_c/css.txt",$style_id,"w+");
				$this->content->template['css_themessage'] = $this->content->template['message_818'];
			}
		}
	}

	/**
	 * @param array $styles
	 * @return array
	 */
	function helper_styles_screenshot_test($styles = array())
	{
		$temp_return = array();
		if (!empty($styles)) {
			foreach ($styles as $style) {
				$style['screenshot_exists'] = file_exists(PAPOO_ABS_PFAD."/styles/".$style['style_pfad']."/screenshot.gif");
				$temp_return[] = $style;
			}
		}
		return $temp_return;
	}

	/**
	 * @param array $styles
	 * @return array
	 */
	function helper_styles_dumpnrestore_test($styles = array())
	{
		$temp_return = array();
		if (!empty($styles)) {
			foreach ($styles as $style) {
				$style['dumpnrestore_exists'] = file_exists(PAPOO_ABS_PFAD."/styles/".$style['style_pfad']."/sql/dumpnrestore.sql");
				$temp_return[] = $style;
			}
		}
		return $temp_return;
	}

	/**
	 * @deprecated Wozu ist das noch hier?
	 */
	function BACKUP_make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();
		if ( empty ( $this->checked->messageget ) ) {
			$this->checked->messageget = "";
		}
		if ($this->checked->messageget==818) {
			$this->content->template['css_themessage'] = $this->content->template['message_818'];
		}

		switch ($this->checked->menuid) {
		case "22" :
			// FIXME: Funktion existiert nicht: in css_do_change? Was hatte man hier vor?
			$this->do_change();
			break;

		default :
			break;
		}
	}

	/**
	 * Hier wird eine existierende CSS Datei geändert oder löschen wird initiiert
	 *
	 * @throws Exception
	 */
	function css_do_change()
	{
		// Wenn Datei gelöscht werden soll
		if (!empty($this->checked->loeschen)) {
			$sql = "SELECT * FROM " . $this->cms->papoo_styles . " ";
			$sql .= " WHERE ";
			$sql .= " style_id='" . $this->db->escape($this->checked->id) . "'";
			$sql .= " LIMIT 1";
			$sql .= "";
			$results = $this->db->get_results( $sql );
			foreach ( $results as $css ) {
				// Aus der Datenbank löschen
				$sql = " DELETE FROM  " . $this->cms->papoo_styles . " WHERE";
				$sql .= " style_id='" . $this->db->escape( $this->checked->id ) . "'";
				$sql .= "";
				$this->db->query( $sql );
				$sql = sprintf( "DELETE FROM %s WHERE mod_style_id='%d'",
					$this->cms->tbname['papoo_module'],
					$this->db->escape( $this->checked->id )
				);
				$this->db->get_results( $sql );
				// Text gelöscht ausgeben
				// header("Location: ./styles.php?menuid=22");
				$location_url = "./styles.php?menuid=22";
				if ( $_SESSION['debug_stopallredirect'] ) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header( "Location: $location_url" );
				}
				exit ();
			}
		}
		else {
			if (!empty($this->checked->style_id)) {
				// Tempaltes rausssuchen
				#$this->content->template['templates_dir'] = $this->get_templates_dir();


				/*
				* Wenn eine Datei bearbeite wird, aus der Datenbank raussuchen
				*/
				$sql = "SELECT * FROM " . $this->cms->papoo_styles . " ";
				$sql .= " WHERE ";
				$sql .= " style_id='" . $this->db->escape($this->checked->style_id) . "'";
				$results = $this->db->get_results( $sql );
				if($results) {
					foreach ( $results as $css ) {

						$pfad = explode( "/", $css->style_pfad );

						$dir = $this->diverse->lese_dir( "/styles/" . $pfad['0']."/css/", 'css' );

						$this->content->template['file_list'] = $dir;
						$this->content->template['style_name'] = $css->style_name;
						//$this->content->template['template_name'] = $css->html_datei;
						$this->content->template['style_id'] = $this->checked->style_id;

						if ( !empty( $this->checked->submit ) ) {
							$file = basename( $this->checked->file );
							$ok = $this->diverse->write_to_file( "/styles/" . $pfad['0'] . "/css/" . $file, utf8_decode($this->checked->inhalt) );
							$this->checked->file = "";
							if ( $ok ) {
								$this->content->template['eingetragen'] = "Die Daten wurden eingetragen!";
							}
							else {
								$this->content->template['eingetragen'] = "Die Daten wurden leider nicht eingetragen!";
							}
						}

						if ( !empty ( $this->checked->file ) || !empty ( $this->checked->form_file ) ) {

							if (!empty($this->checked->form_file)) {
								$this->checked->file=$this->checked->form_file;
							}
							$this->content->template['file_list'] = "";
							$file = basename( $this->checked->file );
							$inhalt = $this->diverse->open_file( "/styles/" . $pfad['0'] . "/css/" . $file );
							if (empty($inhalt)) {
								$inhalt=" ";
							}
							if (!is_writable(PAPOO_ABS_PFAD."/styles/" . $pfad['0'] . "/css/" . $file)) {
								$this->content->template['not_writeable'] = "no";
								$this->content->template['not_writeable_file']="/styles/" . $pfad['0'] . "/css/" . $file;
							}
							$this->content->template['style_id'] = $this->checked->style_id;
							$this->content->template['xinhalt'] = "nodecode:" . utf8_encode($inhalt);
							$this->content->template['style_file_form'] = $file;
							$this->content->template['css_jquery_first'] = "1";
						}
					}
				}
			}
		}
	}

	/**
	 * @deprecated Wozu ist das noch hier?
	 *
	 * Name eines Styles ändern
	 */
	function BACKUP_change_style_name()
	{
		$sql = sprintf( //"UPDATE %s SET style_name='%s', html_datei='%s' WHERE style_id='%s'",
			"UPDATE %s SET style_name='%s' WHERE style_id='%s'",
			$this->cms->papoo_styles,
			$this->db->escape($this->checked->linkname),
			//$this->db->escape($this->checked->template_dir),
			$this->db->escape($this->checked->id)
		);
		$this->db->query($sql);
	}

	/**
	 * @deprecated Wozu ist das noch hier?
	 *
	 * Neuen Style eintragen
	 *
	 * @param string $name
	 * @param string $reload
	 */
	function BACKUP_insert_new_style($name = "", $reload="")
	{
		// Daten in die Datenbank eintragen
		$name2 = "/css/" . $name . "/cc.txt";
		//$name3 = "/css/" . $name . "/template.txt";

		if (file_exists( PAPOO_ABS_PFAD.$name2)) {
			$cc = $this->diverse->open_file( $name2 );
		}
		else {
			$cc = "";
		}

		$sql = sprintf( "INSERT INTO %s SET style_name='%s', style_pfad='%s', style_cc='%s'", //, html_datei='%s' ",
			$this->cms->tbname['papoo_styles'],
			$this->db->escape($name),
			$this->db->escape($name),
			//$this->db->escape( $name . "/_index.css" ),
			$this->db->escape($cc)
		//, $this->db->escape( $templ)
		);
		$this->db->query($sql);

		// Modulmanager updaten
		global $xmlparser;

		$xml = $xmlparser;
		$name = $name."/module.xml";

		if (file_exists( PAPOO_ABS_PFAD . $name)) {
			$xml->parse( PAPOO_ABS_PFAD . $name );
		}

		// TODO: wenn keine module.xml vorhanden, dann eventuell default-module.xml laden !
		// TODO: Module nicht in Modul-Tabelle eintragen, sondern in Kreuztabelle style_module !

		if (empty($reload)) {
			// Neu laden
			$location_url = "./styles.php?menuid=22&messageget=199";

			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit ();
		}
	}

	/**
	 * @deprecated Warum ist das dann noch hier wenn es ein Backup ist?
	 *
	 * Styles raussuchen, die nicht in der Datenbank sind, aber im Verzeichnis
	 *
	 * @return array
	 */
	function BACKUP_get_styles_not_in_database()
	{
		$temp_return = array();

		// Liste der Verzeichnisse in /styles erstellen
		$verzeichnisse = array ();
		$pfad = $this->cms->pfadhier . "/styles/";
		$handle = @opendir($pfad);

		while(($file = readdir($handle)) !== false ) {
			if (is_dir( $pfad . $file) && strpos( "XXX" . $file, "." ) != 3 ) {
				// Nur nicht-unsichtbare Verzeichnisse aufnehmen
				$verzeichnisse[] = $file;
			}
		}

		// Liste der bereits aktiven Styles erstellen
		$sql = sprintf( "SELECT style_pfad FROM %s ", $this->cms->tbname['papoo_styles'] );
		$temp_aktive_styles = $this->db->get_col($sql);

		// aus obigen beiden Listen, die Liste nicht aktiver Styles erstellen
		if (!empty($verzeichnisse)) {
			foreach ($verzeichnisse as $verzeichnis ) {
				$temp_test_datei = PAPOO_ABS_PFAD."/styles/".$verzeichnis."/css/_index.css";

				if (!in_array($verzeichnis, $temp_aktive_styles) AND file_exists($temp_test_datei)) {
					$temp_return[] = $verzeichnis;
				}
			}
		}
		return $temp_return;
	}

	/**
	 * @deprecated Wozu ist das noch hier?
	 *
	 * Styles raussuchen, die nicht in der Datenbank sind, aber im Verzeichnis
	 *
	 * @return array
	 */
	function BACKUP_get_templates_dir()
	{

		$verzeichnisse = array ();
		$pfad = $this->cms->pfadhier . "/templates/";
		$handle = opendir( $pfad );
		while (($file = readdir( $handle)) !== false ) {
			if (is_dir( $pfad . $file) && strpos( "XXX" . $file, ".") != 3) {
				// Nur nicht-unsichtbare Verzeichnisse aufnehmen
				$verzeichnisse[] = $file;
			}
		}
		return $verzeichnisse;
	}
}

$intern_styles = new intern_styles_class();