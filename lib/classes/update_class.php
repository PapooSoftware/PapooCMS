<?php
/**
#####################################
# Papoo CMS                         #
# (c) Carsten Euwens 2008           #
# Authors: Carsten Euwens,          #
#          Stephan Bergmann         #
# http://www.papoo.de               #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class update_class
 */
class update_class
{
	/** @var array Parameter der aktuellen DB, bzw. DB-Tabellen */
	var $db_aktu_parameter;
	/** @var array Parameter der alten DB, bzw. DB-Tabellen */
	var $db_alt_parameter;

	/** @var array Array mit allen Tabellen der aktuellen Installation */
	var $db_aktu_tabellen;
	/** @var array Array mit allen Tabellen der alten Installation */
	var $db_alt_tabellen;

	/** @var array Array mit allen Tabellen welche vom update ausgeschlossen werden sollen */
	var $db_nicht_tabellen;
	/** @var array Array mit allen Tabellen der alten Installation welche in der neuen Installation nicht vorhanden sind */
	var $db_alt_tabellen_nicht_vorhanden;


	/**
	 * update_class constructor.
	 */
	function __construct()
	{
		global $db;
		$this->db = & $db;

		global $content;
		$this->content = & $content;

		global $user;
		$this->user = & $user;

		global $cms;
		$this->cms = & $cms;

		global $checked;
		$this->checked = & $checked;

		global $dumpnrestore;
		$this->dumpnrestore = & $dumpnrestore;

		global $diverse;
		$this->diverse = & $diverse;

		// menuintcss Klasse einbinden
		global $menuintcss;
		$this->menuintcss = & $menuintcss;

		// lokale Variablen indizieren
		$this->eofline_identifier = $this->dumpnrestore->eofline_identifier; // Trennzeichen zur SQL-Zeilenende-Erkennung

		// DB-Parameter mit aktuellen Parametern vorbelegen
		global $db_host;
		global $db_name;
		global $db_user;
		global $db_pw;
		global $db_praefix;

		$this->db_aktu_parameter = array("dbserver" => $db_host,
			"dbname" => $db_name,
			"dbuser" => $db_user,
			"dbpass" => $db_pw,
			"dbpraefix" => $db_praefix
		);
		$this->db_alt_parameter = $this->db_aktu_parameter;
		$this->db_alt_parameter['praefix'] = "";
		$this->db_alt_parameter['nicht_css'] = false;

		$this->db_aktu_tabellen = array();
		$this->db_alt_tabellen = array();

		$this->db_nicht_tabellen = array("papoo_menuint",
			"papoo_men_uint_language",

			"papoo_lookup_men_int", // Nur für Tests ausgeschlossen. Bedarf Sonderbehandlung !!!!

			"papoo_plugins",
			"papoo_pluginclasses",
			"papoo_plugin_language",
			"papoo_plugin_dump_sicherung",
			"papoo_name_language",

			"papoo_module",
			"papoo_module_language"
		);
		$this->db_alt_tabellen_nicht_vorhanden = array();
	}

	/**
	 *
	 */
	function make_update()
	{
		IfNotSetNull($this->checked->start_update);
		IfNotSetNull($this->checked->drin);

		// &uuml;berpr&uuml;fen ob Zugriff auf die Inhalte besteht
		$this->user->check_intern();
		if (empty($this->checked->start_update)) {
			$_SESSION['start_update']=0;
			$_SESSION['fertig_update']=0;
		}
		//Wenn per url auf alt gesetzt dann Abschalten deaktivieren
		if ($this->checked->start_update==1 or $this->checked->start_update==2) {
			$_SESSION['fertig_update']=0;
		}
		//Wenn Fertig dann auf Anfang der seite setzen
		if ($_SESSION['fertig_update']==1) {
			$this->checked->start_update=$_SESSION['start_update']="";

		}
		//WEnn altes Update durchgeführt werden soll
		if ($this->checked->start_update==1 or $_SESSION['start_update']==1) {
			//Update durchf&uuml;hren
			$this->switch_update(@$this->checked->update_action);
			$_SESSION['start_update']=1;
			$this->content->template['update_templateweiche_ok'] = "OK";
			//update_templateweiche_alt
			$this->content->template['update_templateweiche_alt'] = "ALT";
		}
		//Wenn das neue Update durchgeführt werden soll
		if ($this->checked->start_update==2 or $_SESSION['start_update']==2) {
			//Update durchf&uuml;hren
			$this->new_update();
			$_SESSION['start_update']=2;
		}
		if ($this->checked->drin=="ok") {
			$_SESSION['start_update']=20;
		}
		// CSS-Datei für Backend-Menü neu erstellen
		$this->menuintcss->make_menuintcss();

		$_SESSION['dbp']=array();
	}

	/**
	 * update_class::do_old_menu()
	 * Menü aus der alten Weise in die neue übertragen
	 *
	 * @return void
	 */
	function do_old_menu()
	{
		//Alte Menüpunkte und Artikel rausholen
		$sql=sprintf("SELECT cattextid, reporeID, order_id FROM %s",
			$this->cms->tbname['papoo_repore']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$sql=sprintf("DELETE FROM %s",
			$this->cms->tbname['papoo_lookup_art_cat']
		);
		$this->db->query($sql);

		if (is_array($result)) {
			foreach ($result as $var) {
				if (empty($var['cattextid']) or ($var['cattextid']<1)) {
					$var['cattextid']=1;
				}
				$sql=sprintf("INSERT INTO %s SET lart_id='%d', lcat_id='%d', lart_order_id='%d'",
					$this->cms->tbname['papoo_lookup_art_cat'],
					$var['reporeID'],
					$var['cattextid'],
					$var['order_id']
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Update mit der update.sql Datei ab 3.6.1
	 */
	function new_update()
	{
		if (!defined(PAPOO_ABS_PFAD)) {
			global $version;
			define(PAPOO_ABS_PFAD,PAPOO_ABS_PFAD);
			define(PAPOO_VERSION_STRING,$version);
		}

		//Update der 3.6.0
		if (stristr(PAPOO_VERSION_STRING,"3.6.0")) {
			$nr=360;
		}
		//Update der 3.6.1
		if (stristr(PAPOO_VERSION_STRING,"3.6.1")) {
			$nr=361;
		}
		//Update der 3.6.0
		if (stristr(PAPOO_VERSION_STRING,"3.7.0")) {
			$nr=370;
		}
		//Update der 3.6.1
		if (stristr(PAPOO_VERSION_STRING,"3.7.1")) {
			$nr=371;
		}
		if (stristr(PAPOO_VERSION_STRING,"3.7.2")) {
			$nr=372;
		}
		if (stristr(PAPOO_VERSION_STRING,"3.7.3")) {
			$nr=373;
		}
		if (stristr(PAPOO_VERSION_STRING,"3.7.5")) {
			$nr=375;
		}

		$this->content->template['update_templateweiche_ok'] = "OK";
		$this->content->template['update_templateweiche_new'] = "NEU";

		//Update noch nicht durchgeführt
		if ($this->checked->drin!="ok") {
			//Update durchführen
			if ($this->checked->update_now=="ok") {
				$this->dumpnrestore->restore_load(PAPOO_ABS_PFAD."/update/update".$nr.".sql");
				$this->db->hide_errors();
				$this->dumpnrestore->restore_save();
				//Menüpunkte zuweisen aus der alten Version
				if ($nr<370) {
					$this->do_old_menu();
				}
				//Interne Templates_c löschen
				$this->diverse->remove_files(PAPOO_ABS_PFAD."/interna/templates_c/standard/",".html.php");

				$_SESSION['start_update']="20";
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=65&drin=ok&start_update=2";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit();
			}


			//Update Text anzeigen
			$dateiarray = @file(PAPOO_ABS_PFAD."/update/update".$nr.".txt");
			$this->content->template['update_content_text'] =@implode($dateiarray);
			if (empty($this->content->template['update_content_text'])) {
				$this->content->template['update_content_text']="Keine Datei vorhanden / No File";
			}
			//Update Datei anzeigen
			$dateiarray = @file(PAPOO_ABS_PFAD."/update/update".$nr.".sql");
			$this->content->template['update_content'] =(htmlentities(@implode($dateiarray), ENT_COMPAT,'utf-8'));
			if (empty($this->content->template['update_content'])) {
				$this->content->template['update_content']="Keine Datei vorhanden / No File";
			}
		}
		else {
			//Fertig anzeigen
			$this->content->template['fertig_update']="ok";
		}
	}

	/**
	 * Aktions-Weiche der Klass
	 *
	 * @param string $modus
	 */
	function switch_update($modus = "")
	{
		switch ($modus)
		{
		case "UPDATE":
			// 1. Liste der zu aktualisierenden Tabellen und alte DB-Parameter laden
			$this->db_alt_tabellen_restore();
			$this->db_alt_parameter_restore();

			// 2. Aktualisierung durchf&uuml;hren (evtl. ohne Styles-Tabelle)
			$this->update();

			// 3. Meldung ausgeben
			$this->content->template['update_db_alt_tabellen'] = $this->db_alt_tabellen;
			$this->content->template['update_templateweiche'] = "UPDATE";
			$_SESSION['fertig_update']=1;
			break;

		case "TABELLEN_SET": // Dieser Fall wird "intern" vom Fall "UPDATE_PARAMETER_SET" aufgerufen.
			// 1. komplette Liste der aktuellen Tabellen einlesen
			$this->db_aktu_tabellen_set();

			// 2. komplette Liste der alten Tabellen einlesen
			$this->db_alt_tabellen_set();

			// 3. Tabellen welche nicht aktualisiert werden sollen ausschliessen
			if ($this->db_alt_parameter['nicht_css']) $this->db_nicht_tabellen[] = "papoo_styles";
			$this->db_alt_tabellen_ausschliessen_nicht_tabellen();

			// 4. Tabellen welche in der aktuellen Installation nicht vorhanden sind ausschliessen
			$this->db_alt_tabellen_ausschliessen_nicht_vorhanden();

			// 5. Liste der zu aktualisierenden Tabellen speichern
			$this->db_alt_tabellen_save();

			// 6. Template-Daten &uuml;bergeben
			$this->content->template['update_db_alt_tabellen_nicht_vorhanden'] = $this->db_alt_tabellen_nicht_vorhanden;
			$this->content->template['update_templateweiche'] = "HINWEIS";
			break;

		case "PARAMETER_SET":
			// 1. Parameter der alten Datenbank setzen
			$this->db_alt_parameter_set();

			// 2. Pr&uuml;fung ob Pr&auml;fix leer
			if (empty($this->db_alt_parameter['praefix'])) {
				// Fehler setzen und Startseite neu laden
				$this->content->template['update_fehler_praefix'] = "Sie m&uuml;ssen das Pr&auml;fix der alten Installation angeben.\n";
				$this->switch_update();
			}
			else {
				// 3. Pr&uuml;fung ob alle Angaben zur alten Datenbank stimmen
				if (!$this->db_alt_connect()) {
					// Fehler setzen und Startseite neu laden
					$this->content->template['update_fehler_praefix'] = "Das angegebene Pr&auml;fix scheint nicht zu stimmen.\n";
					$this->switch_update();
				}
				// alle Angaben zur alten Datenbak stimmen
				else {
					// Angaben (in Session-Variable) speichern
					$this->db_alt_parameter_save();
					$this->switch_update("TABELLEN_SET");
				}
			}
			break;

		case "":

		default:
			// Preset-Daten und Template-Weiche setzen
			$this->content->template['update_db_alt_parameter'] = $this->db_alt_parameter;
			$this->content->template['update_templateweiche'] = "START";
		}
		// aktuelle DB-Verbindung wieder herstellen
		$this->db_aktu_connect();
	}

	/**
	 * Setzt die Zugriffs-Paramter der alten Datenbank in $this->db_alt_parameter
	 */
	function db_alt_parameter_set()
	{
		$this->db_alt_parameter['dbserver'] = $this->checked->update_dbserver_alt;
		$this->db_alt_parameter['dbname'] = $this->checked->update_dbname_alt;
		$this->db_alt_parameter['dbuser'] = $this->checked->update_dbuser_alt;
		$this->db_alt_parameter['dbpass'] = $this->checked->update_dbpass_alt;
		$this->db_alt_parameter['dbpraefix'] = $this->checked->update_praefix_alt."_";
		$this->db_alt_parameter['praefix'] = $this->checked->update_praefix_alt;
		$this->db_alt_parameter['nicht_css'] = $this->checked->update_nicht_css_alt;
	}

	/**
	 * Speichert die Angaben der alten DB $this->db_alt_parameter in der Session-Variablen ['update']['db_alt_parameter']
	 */
	function db_alt_parameter_save()
	{
		$_SESSION['update']['db_alt_parameter'] = $this->db_alt_parameter;
	}

	/**
	 * Setzt die Angaben der alten DB $this->db_alt_parameter aus der Session-Variablen ['update']['db_alt_parameter']
	 */
	function db_alt_parameter_restore()
	{
		$this->db_alt_parameter = $_SESSION['update']['db_alt_parameter'];
	}

	/**
	 * Baut die Verbindung zur aktuellen DB auf. Diese Verbindung steht dann als $this->db zur Verfügung.
	 * Außerdem wird der Parameter $lokal_db_praefix in der dumpnrestore-Instanz auf den aktuellen Wert gesetzt.
	 *
	 * @return bool
	 */
	function db_aktu_connect()
	{
		$this->dumpnrestore->lokal_db_praefix = $this->db_aktu_parameter['dbpraefix'];
		$this->db->db_class(
			$this->db_aktu_parameter['dbuser'],
			$this->db_aktu_parameter['dbpass'],
			$this->db_aktu_parameter['dbname'],
			$this->db_aktu_parameter['dbserver']
		);

		if ($this->db_test_connection("aktu")) {
			return true;
		}
		else {
			return false;
		}
	}

	//
	//
	/**
	 * Baut die Verbindung zur DB mit den alten Daten auf. Diese Verbindung steht dann als $this->db zur Verfügung.
	 * Außerdem wird der Parameter $lokal_db_praefix in der dumpnrestore-Instanz auf den Wert der alten Installation gesetzt.
	 *
	 * @return bool
	 */
	function db_alt_connect()
	{
		$this->dumpnrestore->lokal_db_praefix = $this->db_alt_parameter['dbpraefix'];
		$this->db->db_class(
			$this->db_alt_parameter['dbuser'],
			$this->db_alt_parameter['dbpass'],
			$this->db_alt_parameter['dbname'],
			$this->db_alt_parameter['dbserver']
		);

		if ($this->db_test_connection("alt")) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Prüft die Verbindung zur Datenbank (aktuelle "aktu" oder alte "alt")
	 *
	 * @param string $db
	 * @return bool
	 */
	function db_test_connection($db = "aktu")
	{
		$praefix = "";
		if ($db == "aktu") {
			$praefix = $this->db_aktu_parameter['dbpraefix'];
		}
		if ($db == "alt") {
			$praefix = $this->db_alt_parameter['dbpraefix'];
		}

		$sql = sprintf("SHOW TABLES LIKE '%s'", $praefix."papoo_user");
		$test = $this->db->query($sql);

		if ($test) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Liest die Liste aller DB-Tabellen der aktuellen Installation in $this->db_aktu_tabellen
	 */
	function db_aktu_tabellen_set()
	{
		$this->db_aktu_connect();
		$this->db_aktu_tabellen = $this->dumpnrestore->make_tablelist();
		$this->db_aktu_tabellen = $this->db_korrektur_tabellen_praefix("aktu", $this->db_aktu_tabellen);
	}

	/**
	 * Liest die Liste aller DB-Tabellen der alten Installation in $this->db_alt_tabellen
	 */
	function db_alt_tabellen_set()
	{
		$this->db_alt_connect();
		$this->db_alt_tabellen = $this->dumpnrestore->make_tablelist();
		$this->db_alt_tabellen = $this->db_korrektur_tabellen_praefix("alt", $this->db_alt_tabellen);
	}

	/**
	 * entfernt das Tabellen-Präfix aus der Liste der Tabellen (aktuelle "aktu" oder alte "alt")
	 *
	 * @param string $tabellen
	 * @param array $liste
	 * @return array|bool
	 */
	function db_korrektur_tabellen_praefix($tabellen = "aktu", $liste = array())
	{
		$the_return = array();
		$praefix = "";
		if ($tabellen == "aktu") {
			$praefix = $this->db_aktu_parameter['dbpraefix'];
		}
		if ($tabellen == "alt") {
			$praefix = $this->db_alt_parameter['dbpraefix'];
		}

		if (!empty($liste)) {
			foreach ($liste as $tabelle) {
				$the_return[] = substr_replace($tabelle['tablename'], "", 0, strlen($praefix));
			}
			return $the_return;
		}
		else {
			return false;
		}
	}

	/**
	 * Entfernt Tabellen aus der Liste aller DB-Tabellen der alten Installation,
	 * welche nicht aktualisiert werden sollen ($this->dn_nicht_tabellen)
	 * und speichert die Liste wieder in $this->db_alt_tabellen
	 */
	function db_alt_tabellen_ausschliessen_nicht_tabellen()
	{
		$temp_array = array();
		if (!empty($this->db_alt_tabellen)) {
			foreach ($this->db_alt_tabellen as $tabelle) {
				if (!in_array($tabelle, $this->db_nicht_tabellen)) {
					$temp_array[] = $tabelle;
				}
			}
		}
		$this->db_alt_tabellen = $temp_array;
	}

	/**
	 * Entfernt Tabellen aus der Liste aller DB-Tabellen der alten Installation,
	 * welche in der neuen Installation nicht vorhanden sind
	 * und speichert die Liste wieder in $this->db_alt_tabellen.
	 * Eine Liste der ausgeschlossenen Tabellen wird in $this->db_alt_tabellen_nicht_vorhanden gespeichert
	 */
	function db_alt_tabellen_ausschliessen_nicht_vorhanden()
	{
		$temp_array = array();
		$temp_array_nicht_vorhanden = array();
		if (!empty($this->db_alt_tabellen)) {
			foreach ($this->db_alt_tabellen as $tabelle) {
				if (in_array($tabelle, $this->db_aktu_tabellen)) {
					$temp_array[] = $tabelle;
				}
				else {
					$temp_array_nicht_vorhanden[] = $tabelle;
				}
			}
		}
		$this->db_alt_tabellen = $temp_array;
		$this->db_alt_tabellen_nicht_vorhanden = $temp_array_nicht_vorhanden;
	}

	/**
	 * Speichert die Namen der alten DB-Tabellen $this->db_alt_tabellen in der Session-Variablen ['update']['db_alt_tabellen']
	 */
	function db_alt_tabellen_save()
	{
		$_SESSION['update']['db_alt_tabellen'] = $this->db_alt_tabellen;
	}

	/**
	 * Setzt die Namen der alten DB-Tabellen $this->db_alt_tabellen aus der Session-Variablen ['update']['db_alt_tabellen']
	 */
	function db_alt_tabellen_restore()
	{
		$this->db_alt_tabellen = $_SESSION['update']['db_alt_tabellen'];
	}

	/**
	 *
	 */
	function update()
	{
		if (!empty($this->db_alt_tabellen)) {
			@ini_set("memory_limit", "300M"); // Bei Speicher-Problemen evtl. aktivieren
			@set_time_limit(300);
			$this->dumpnrestore->doupdateok = "ok";
			$this->dumpnrestore->export = true;
			//$this->dumpnrestore->dump_save(); // dumpnrestore.sql einmal leeren.

			$j=0;
			$zahl=0;

			if ((is_numeric($this->checked->counttab))) {
				$counttab = $this->checked->counttab;
			}
			if ((is_numeric($this->checked->lim))) {
				$zahl = $this->checked->lim;
			}

			foreach($this->db_alt_tabellen as $tabelle) {
				if (($j >= $counttab)) {
					$this->db_alt_connect();
					//maximale Anzahl
					$max=1000;
					$sql=sprintf("SELECT COUNT(*) FROM %s",$this->db_alt_parameter['dbpraefix'].$tabelle);
					$var=$this->db->get_var($sql);

					if ($var>$max && $var>$zahl) {

						$this->db_alt_connect();
						$this->dumpnrestore->querys ="";
						$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($this->db_alt_parameter['dbpraefix'].$tabelle,$zahl,$max);
						//$this->dumpnrestore->dump_save("append");

						$this->dumpnrestore->querys = str_replace($this->dumpnrestore->eofline_identifier, "", $this->dumpnrestore->querys);

						$this->db_aktu_connect();
						$this->dumpnrestore->restore_save();
						$zahl = $zahl+$max;
						echo "Update l&auml;uft <br />";
						echo "\n\n Update Der Tabelle ".$tabelle."<br />\n";
						echo $zahl."<br />";
						// $location_url = "./stamm.php?menuid=53&makedump=1&lim=".$limmax."&tab=".$counttab;
						echo $this->refresh = '<meta http-equiv="refresh" content="0; url=./update.php?menuid=65&start_update=1&update_action=UPDATE&lim=' . $zahl . '&counttab=' . $j . '" />';
						exit();
					}
					else {
						if (($var>$zahl)) {
							$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($this->db_alt_parameter['dbpraefix'].$tabelle,$zahl,$max);
							//$this->dumpnrestore->dump_save("append");

							$this->dumpnrestore->querys = str_replace($this->dumpnrestore->eofline_identifier, "", $this->dumpnrestore->querys);

							$this->db_aktu_connect();
							$this->dumpnrestore->restore_save();
						}
						$zahl=0;
					}
				}
				$j++;
			}
		}
	}
}

$update = new update_class();