<?php

/**
 * rapid_db_class
 * Damit können adhoc sehr schnell DB Felder und dazugehörige
 * HTML Felder erstellt werden
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class rapid_db_class
{
	/**
	 * rapid_db_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $diverse, $db, $checked, $content, $user, $cms;
		$this->diverse = &$diverse;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->user = &$user;
		$this->cms = &$cms;
		// Aktions-Weiche
		// **************
		$this->do_rapid();
	}

	/**
	 * rapid_db_class::do_rapid()
	 *
	 * @return void
	 */
	function do_rapid()
	{
		//Wenn Formular
		if (!empty($this->checked->submit_rapid)) {
			//Inhalte �beergeben
			$this->alle_rapid_variablen_setzen();

			//Wenn brav alles ausgef�llt wurde
			if (!empty($this->praefix) && !empty($this->name) && !empty($this->label) && $this->check_ob_feld_schon_existiert_in_der_datenbank()) {
				//Typ auf normal setzen
				$type = "normal";
				//DB Typ setzen
				$db_type = "normal";

				//Fallunterscheidungen durchf�hren und dann
				switch ($this->checked->rapid_type) {
				case "text":
					$this->create_text_feld();
					break;
				case "textarea":
					$this->create_textarea_feld();
					break;
				case "email":
					$this->create_text_feld();
					break;
				case "select":
					$this->create_select_feld();
					$db_type = "varchar";
					break;
				case "radio":
					$this->create_radio_feld();
					$db_type = "varchar";
					break;
				case "check":
					$this->create_check_feld();
					$db_type = "varchar";
					break;
				case "hidden":
					$this->create_hidden_feld();
					$db_type = "varchar";
					break;
				case "password":
					$this->create_password_feld();
					break;
				case "timestamp":
					//
					break;
				case "link":
					$this->create_link();
					$db_type = "no";
					break;
				case "leer":
					$this->create_leer();
					$db_type = "no";
					break;
				case "bild":
					$this->create_image_file_feld();
					break;
				case "file":
					$this->create_file_feld();
					break;
				case "ueberschrift_h1":
					$this->create_h1();
					$db_type = "no";
					break;
				case "ueberschrift_h2":
					$this->create_h2();
					$db_type = "no";
					break;
				case "ueberschrift_h3":
					$this->create_h3();
					$db_type = "no";
					break;
				case "absatz":
					$this->create_p();
					$db_type = "no";
					break;
				case "fieldset":
					$type = "fieldset";
					$db_type = "no";
					$this->create_fieldset();
					break;
				case "divblock":
					$type = "divblock";
					$db_type = "no";
					$this->create_div();
					break;
				case "submit":
					$db_type = "no";
					$this->create_submit();
					break;

				default:
					break;
				}
				//Ersetzungen im Template durchf�hren
				$this->baue_neues_feld_ins_template_ein($type);

				//NEuen Spracheintrag vornehmen
				$this->erstelle_neuen_sprachdatei_eintrag();

				if ($this->checked->tbname!="NO") {
					//Db Felder erzeugen
					$this->erstelle_neues_db_feld_in_der_tabelle($db_type);
				}

				//Coder erzeugen
				$this->erzeuge_input_output_php_code($this->php_datei_name);

				//Neu laden
				$this->reload();
			}
			else {
				$this->content->template['rapid_error1'] = "NO";
			}
		}
	}

	/**
	 * rapid_db_class::erzeuge_input_output_php_code()
	 * Erzeuge den IO Code in der PHP Datei
	 * @param mixed $php_file
	 * @return void
	 */
	function erzeuge_input_output_php_code($php_file)
	{
		//Checken das es nichts b�ses ist
		#	$php_file = basename($php_file);
		#	$php_file = $php_file . ".php";

		//Inhalt auslesen
		#$file_content = implode("", file(PAPOO_ABS_PFAD . "/plugins/" . $this->
		#	plugin_name . "/lib/" . $php_file));

		//Eintrag erstellen
		#$php = $this->erzeuge_io_php_codde();

		//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
		#$file_content2 = str_ireplace('#start#', $php . " #start#", $file_content);

		//Inhalt speichern
		#	$this->diverse->write_to_file("/plugins/" . $this->plugin_name . "/lib/" . $php_file,
		#		$file_content2);
	}

	/**
	 * rapid_db_class::erzeuge_io_php_codde()
	 * Diese Funktion erzeugt den PHP Code f�r IO
	 * @return void
	 */
	function erzeuge_io_php_codde()
	{
		/**
		 * Erstmal offen lassen, das kann man ja mit einer generellen
		 * Funktion besser erschlagen
		 *
		 * //Output Vorlage
		 * $output='function #platzhalter_function_name#_output()
		 * {
		 * $sql=sprintf("SELECT * FROM %s WHERE %s=\'%d\' AND #platzhalter_spezial_praefix#_lang_id=\'%d\'",
		 * $this->cms->tbname[\'#platzhalte_db#\'],
		 * #platzhalter_id_name#,
		 * $this->db->escape($this->checked->#platzhalter_id_name#),
		 * $this->cms->lang_id
		 * );
		 * $result=$this->db->get_results($sql,ARRAY_A);
		 * $this->content->template[\'#platzhalter_outputname#\']=$result;
		 * }';
		 * //Ersetzungen durchf�hren
		 *
		 * //Input Vorlage
		 */
	}

	/**
	 * rapid_db_class::check_ob_name_schon_existiert_in_der_datenbank()
	 *
	 * @return bool
	 */
	function check_ob_feld_schon_existiert_in_der_datenbank()
	{
		//Tabelle existiert noch nicht also ok
		if (empty($this->cms->tbname[$this->tb_name])) {
			return true;
		}

		//�berpr�fen ob spalte existiert
		$sql = sprintf("SHOW COLUMNS FROM %s LIKE '%s'", $this->cms->tbname[$this->
		tb_name], $this->name);
		$result = $this->db->get_results($sql, ARRAY_A);

		//wenn ja- dann false
		if (!empty($result)) {
			return false;
		}
		return true;
	}

	/**
	 * rapid_db_class::erstelle_neues_db_feld_in_der_tabelle()
	 *
	 * @param string $db_type
	 * @return void
	 */
	function erstelle_neues_db_feld_in_der_tabelle($db_type = "normal")
	{
		if ($this->checked->is_system==1) {
			$this->plugin_name="system";
		}
		if ($db_type != "no") {
			//Checken ob die Tabelle existiert
			if (empty($this->cms->tbname[$this->tb_name])) {
				//Wenn nicht anlegen
				$this->erzeuge_neue_tabelle($this->tb_name);
			}

			if ($db_type == "normal") {
				$this->erzeuge_neues_normales_feld();
			}

			if ($db_type == "varchar") {
				$this->erzeuge_neues_varchar_feld();
			}
		}
	}

	/**
	 * rapid_db_class::erzeuge_neue_tabelle()
	 * Eine Neue tabelle erzeugen
	 *
	 * @param mixed $tbname
	 * @return bool
	 */
	function erzeuge_neue_tabelle($tbname)
	{
		global $db_praefix;
		//Tabelle erzeugen
		if (!stristr($tbname,"lang")) {
			$sql = sprintf("CREATE TABLE `%s` (`%s` int(11) NOT NULL auto_increment, 
				  							PRIMARY KEY (`%s`)) ENGINE=MyISAM",
				$db_praefix . $tbname,
				$this->praefix_spezial . "_id",
				$this->praefix_spezial . "_id"
			);
		}
		else {
			$sql = sprintf("CREATE TABLE `%s` (`%s` int(11) NOT NULL , 
												%s_lang_id int(11) NOT NULL
				  						) ENGINE=MyISAM",
				$db_praefix . $tbname,
				$this->praefix_spezial . "_id",
				$this->praefix_spezial);
		}
		$this->db->query($sql);
		$sql=str_replace($db_praefix,"XXX_",$sql);
		$sql=$sql."; ##b_dump##\n\r";
		$this->diverse->write_to_file("/plugins/" . $this->plugin_name .
			"/sql/update.sql", $sql,"a+");

		return true;
	}

	/**
	 * rapid_db_class::erzeuge_neues_feld()
	 * Neues Feld in der Tabelle erzeugen
	 *
	 * @return bool
	 */
	function erzeuge_neues_normales_feld()
	{
		global $db_praefix;
		$sql = sprintf("ALTER TABLE `%s` ADD `%s` TEXT",
			$db_praefix .
			$this->tb_name,
			$this->name
		);
		$this->db->query($sql);
		$sql=str_replace($db_praefix,"XXX_",$sql);
		$sql=$sql."; ##b_dump##\n\r";
		$this->diverse->write_to_file("/plugins/" . $this->plugin_name .
			"/sql/update.sql", $sql,"a+");
		return true;
	}

	/**
	 * rapid_db_class::erzeuge_neues_varchar_feld()
	 *
	 * @return bool
	 */
	function erzeuge_neues_varchar_feld()
	{
		global $db_praefix;
		$sql = sprintf("ALTER TABLE `%s` ADD `%s` VARCHAR( 255 ) NOT NULL",
			$db_praefix .
			$this->tb_name,
			$this->name
		);
		$this->db->query($sql);
		$sql=str_replace($db_praefix,"XXX_",$sql);
		$sql=$sql."; ##b_dump##\n\r";
		$this->diverse->write_to_file("/plugins/" . $this->plugin_name .
			"/sql/update.sql", $sql,"a+");
		return true;
	}


	/**
	 * rapid_db_class::reload()
	 * Seite nue laden
	 * @return void
	 */
	function reload()
	{
		$url = "";
		foreach ($_GET as $key => $value)
		{
			$url .= strip_tags(htmlentities($key)) . "=" . strip_tags(htmlentities($value)) .
				"&";
		}
		$location_url = $_SERVER['PHP_SELF'] . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * rapid_db_class::erstelle_neuen_sprachdatei_eintrag()
	 * Diese Funktion erstellt einen neuen Sprachdatei eintrag
	 *
	 * @return void
	 */
	function erstelle_neuen_sprachdatei_eintrag()
	{
		$lf="backend";
		if ($this->checked->rapid_lang=="front") {
			$lf="frontend";
		}
		//Hier die Plugin Dateien
		if (empty($this->checked->is_system)) {
			//Inhalt auslesen
			$file_content = implode("", file(PAPOO_ABS_PFAD . "/plugins/" . $this->
				plugin_name . "/messages/messages_".$lf."_de.inc.php"));

			//Spracheintrag erstellen
			$spracheintrag = '$this->content->template[\'plugin_' . $this->praefix . "_" . $this->
				name_org . '\']=\'' . $this->label . "';\n";

			//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
			$file_content2 = str_ireplace('#start#', $spracheintrag . " #start#", $file_content);

			//Inhalt speichern
			$this->diverse->write_to_file("/plugins/" . $this->plugin_name .
				"/messages/messages_".$lf."_de.inc.php", $file_content2);
		}
		//Hier dann Systemdateien
		else {
			//Inhalt auslesen
			$file_content = implode("", file(PAPOO_ABS_PFAD . "/lib/messages/messages_".$lf."_de.inc.php"));

			//Spracheintrag erstellen
			$spracheintrag = '$this->content->template[\'system_' . $this->praefix . "_" . $this->
				name_org . '\']=\'' . $this->label . "';\n";

			//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
			$file_content2 = str_ireplace('#start#', $spracheintrag . " #start#", $file_content);

			//Inhalt speichern
			$this->diverse->write_to_file("/lib/messages/messages_".$lf."_de.inc.php", $file_content2);
		}
	}

	/**
	 * rapid_db_class::baue_neues_feld_ins_template_ein()
	 * Diese FUnktion baut das neue Feld ins Template ein
	 *
	 * @param string $type
	 * @return void
	 */
	function baue_neues_feld_ins_template_ein($type = "normal")
	{
		//Dateiname rausholen
		$file = basename($this->checked->template);

		//HIer die Plugin Template Dateien einlesen
		if (empty($this->checked->is_system)) {
			//Inhalt auslesen
			$file_content = implode("", file(PAPOO_ABS_PFAD . "/plugins/" . $this->
				plugin_name . "/templates/" . $file));
		}
		//HIer dann die Systemdateien
		else {
			$extra="";
			if (stristr($this->checked->system_template,"sub_")) {
				$extra="sub_templates/";
			}
			$file = $extra.basename($this->checked->system_template);
			$file_content = implode("", file(PAPOO_ABS_PFAD . "/interna/templates/standard/" . $file));
		}
		//NOrmal - dann einfach einf�gen
		if ($type == "normal") {
			//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
			$file_content2 = str_ireplace('#start#', $this->cfeld . "\n\n #start#", $file_content);
		}
		//Spezialfall fieldset
		if ($type == "fieldset") {
			//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
			if (stristr( $file_content,"#start#</fieldset>")) {
				#$file_content2=str_ireplace('#start#',"",$file_content);
				$file_content2 = str_ireplace('#start#</fieldset>', "</fieldset>" . $this->cfeld, $file_content);
			}
			else {
				$file_content2 = str_ireplace('#start#', $this->cfeld, $file_content);
			}
		}
		//Spezialfall fieldset
		if ($type == "divblock") {
			//Den neuen Inhalt einbauen und den Platzhalter wieder setzen
			if (stristr( $file_content,"</div><!-- END DIV -->")) {
				$file_content2 = str_ireplace('#start#', "", $file_content);
				$file_content2 = str_ireplace('</div><!-- END DIV -->', "</div>\n" . $this->
					cfeld, $file_content2);
			}
			else {
				$file_content2 = str_ireplace('#start#', $this->cfeld, $file_content);
			}
		}
		IfNotSetNull($file_content2);
		//Hier Plugin Dateien
		if (empty($this->checked->is_system)) {
			//Inhalt speichern
			$this->diverse->write_to_file("/plugins/" . $this->plugin_name . "/templates/" . $file, $file_content2);
		}
		//Hier dann die Systemdateien speichern
		else {
			$file_content2=str_ireplace("plugin_","system_",$file_content2);
			$this->diverse->write_to_file("/interna/templates/standard/" . $file, $file_content2);
		}
	}

	/**
	 * rapid_db_class::alle_rapid_variablen_setzen()
	 *
	 * @return void
	 */
	function alle_rapid_variablen_setzen()
	{
		//Namen raussuchen
		$this->plugin_name = $this->get_plugin_name();

		#$this->checked->praefix=str_ireplace(" ","_",$this->checked->praefix);
		#$this->checked->praefix_spezial=str_ireplace(" ","_",$this->checked->praefix_spezial);
		$this->checked->rapid_name=str_ireplace(" ","_",$this->checked->rapid_name);

		//Pr�fix �bergeben
		#$this->checked->praefix=strtolower($this->checked->praefix);
		$this->praefix = preg_replace("/[^a-zA-Z0-9_]_/", "", $this->checked->praefix);
		$this->praefix_spezial = preg_replace("/[^a-zA-Z0-9_]/", "", $this->checked->
		praefix_spezial);

		//Name der Variable
		$this->checked->rapid_name=strtolower($this->checked->rapid_name);
		$this->name_org = preg_replace("/[^a-z0-9_]/", "", $this->checked->rapid_name);
		$this->name = $this->praefix_spezial . "_" . $this->name_org;

		//LAbel des Eintrages
		$this->label = $this->db->escape(strip_tags(htmlentities($this->checked->
		rapid_label, ENT_QUOTES, "UTF-8")));

		//php_datei_name
		$this->php_datei_name = preg_replace("/[^a-zA-Z0-9_]/", "", $this->checked->
		php_datei_name);

		//NAme der DB
		$this->tb_name = preg_replace("/[^a-zA-Z0-9_]/", "", $this->checked->tb_name);
	}

	/**
	 * rapid_db_class::get_plugin_name()
	 * Holt den Plugin Namen raus
	 *
	 * @return string|array|string[]
	 */
	function get_plugin_name()
	{
		$split = explode('/', $this->checked->template);
		if (defined("admin")) {
			return $split['0'];
		}
		else {
			return $split['3'];
		}
	}

	/**
	 * rapid_db_class::get_rapid_content_liste()
	 * Aus den OPtionen ein Array erzeugen
	 *
	 * @param mixed $liste
	 * @return array
	 */
	function get_rapid_content_liste($liste)
	{
		$liste_array = explode("\n", $liste);
		return $liste_array;
	}

	/**
	 * rapid_db_class::create_text_feld()
	 *
	 * @return void
	 */
	function create_text_feld()
	{
		$cfeld = ' <div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '';
		$cfeld .= "\n";
		$cfeld .= '<input type="text" name="' . $this->name . '" value="{$' . $this->
			praefix_spezial . '.0.' . $this->name . '|escape:"html"}" class="' . $this->name . '" id="' .
			$this->name . '"/>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_check_feld()
	 * Eine Checkbox erstellen
	 *
	 * @return void
	 */
	function create_check_feld()
	{
		$cfeld = "";

		$cfeld .= '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" .
			$this->name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= ' <input type="checkbox" name="' . $this->name . '" value="1" {if $' .
			$this->praefix_spezial . '.0.' . $this->name .
			'==1}checked="checked"{/if} class="' . $this->name . '" id="' . $this->name .
			'"/>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_image_file_feld()
	 * Ein Bild Upload Feld erstellen
	 *
	 * @return void
	 */
	function create_image_file_feld()
	{
		$cfeld = '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '<br />';
		$cfeld .= "\n";
		$cfeld .= '<input type="file" accept="image/*" name="' . $this->name .
			'" value="{$' . $this->praefix_spezial . '.0.' . $this->name . '}" class="' . $this->
			name . '" id="' . $this->name . '"/>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
		$this->cfeld = $cfeld;
	}

	function create_file_feld()
	{
		$cfeld = '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '<br />';
		$cfeld .= "\n";
		$cfeld .= '<input type="file" accept="image/*" name="' . $this->name .
			'" value="{$' . $this->praefix_spezial . '.0.' . $this->name . '}" class="' . $this->
			name . '" id="' . $this->name . '"/>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
		$this->cfeld = $cfeld;
	}


	/**
	 * rapid_db_class::create_select_feld()
	 * Erstelle eine Selectbox
	 *
	 * @return void
	 */
	function create_select_feld()
	{
		$optionen = $this->get_rapid_content_liste($this->checked->rapid_content_list);

		$cfeld = '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '';
		$cfeld .= "\n";
		$cfeld .= '<select name="' . $this->name . '" id="' . $this->name .
			'" size="1"/>';
		$cfeld .= '<option value="0">{$message_160}</option>';
		foreach ($optionen as $key => $value) {
			$key = $key + 1;
			$cfeld .= '<option {if $' . $this->praefix_spezial . '.0.' . $this->name . '==' .
				$key . '}selected="selected"{/if} value="' . $key . '">' . $value . '</option>';
		}
		$cfeld .= "</select>\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_select_feld()
	 * Erstelle eine Radiobox
	 *
	 * @return void
	 */
	function create_radio_feld()
	{

	}

	/**
	 * rapid_db_class::create_password_feld()
	 *
	 * @return void
	 */
	function create_password_feld()
	{
		$cfeld = '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '';
		$cfeld .= "\n";
		$cfeld .= '<input type="password" name="' . $this->name . '" value="{$' . $this->
			praefix_spezial . '.0.' . $this->name . '|escape:"html"}" class="' . $this->name . '" id="' .
			$this->name . '"/>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_hidden_feld()
	 *
	 * @return void
	 */
	function create_hidden_feld()
	{
		$cfeld = '<input type="hidden" name="' . $this->name . '" value="{$' . $this->
			praefix_spezial . '.0.' . $this->name . '|escape:"html"}" class="' . $this->name . '" id="' .
			$this->name . '"/>';
		$cfeld .= "\n";
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_text_feld()
	 *
	 * @return void
	 */
	function create_textarea_feld()
	{
		$cfeld = '<div class="labdiv"><label for="' . $this->name . '">{$plugin_' . $this->praefix . "_" . $this->
			name_org . '}{$plugin_error.'.$this->name.'} </label>';
		$cfeld .= "\n";
		$cfeld .= '<br />';
		$cfeld .= "\n";
		$cfeld .= '<textarea cols="30" rows="6" name="' . $this->name . '"  class="' . $this->name .
			'" id="' . $this->name . '">';
		$cfeld .= '{$' . $this->praefix_spezial . '.0.' . $this->name . '|escape:"html"}';
		$cfeld .= '</textarea>';
		$cfeld .= "\n";
		$cfeld .= '</div><br />';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_fieldset()
	 *
	 * @return void
	 */
	function create_fieldset()
	{
		$cfeld = '<fieldset>';
		$cfeld .= "\n";
		$cfeld .= '<legend>{$plugin_' . $this->praefix . "_" . $this->name_org .
			'}</legend>';
		$cfeld .= "\n";
		$cfeld .= "\n";
		$cfeld .= '#start#';
		$cfeld .= '</fieldset><br />';
		$cfeld .= "\n";
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_div()
	 *
	 * @return void
	 */
	function create_div()
	{
		$cfeld = '<div id="' . $this->praefix . "_" . $this->name_org .
			'" class="divblock" >';
		$cfeld .= "\n";
		$cfeld .= '#start#';
		$cfeld .= '</div><!-- END DIV --><br />';
		$cfeld .= "\n";
		$this->cfeld = $cfeld;
	}

	function create_submit()
	{
		$cfeld = '<input type="submit" id="p_' . $this->praefix . "_form_submit" .
			'" name="plugin_' . $this->praefix . "_form_submit" .
			'" class="rapid_dev_submit_button btn btn-info" value="' . $this->name_org . '" >';
		$cfeld .= "\n";
		$cfeld .= '<input type="hidden" id="p_' . $this->praefix . "_form_tabelle" .
			'" name="plugin_' . $this->praefix . "_form_tablename" .
			'" value="' . $this->tb_name . '" >';
		$cfeld .= "\n";

		$this->cfeld = $cfeld;
	}

	function create_leer()
	{
		$cfeld = '{$plugin_' . $this->praefix . "_" . $this->name_org . '}';
		$this->cfeld = $cfeld;
	}

	function create_link()
	{
		$cfeld = '<a href="" id="' . $this->praefix . "_" . $this->name_org .
			'" class="132link" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</a>';
		$this->cfeld = $cfeld;
	}

	/**
	 * rapid_db_class::create_h()
	 * EIne �berschrift kreieren
	 * @return void
	 */
	function create_h1()
	{
		$cfeld = '<h1 id="' . $this->praefix . "_" . $this->name_org .
			'" class="h1" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</h1>';
		$this->cfeld = $cfeld;
	}
	function create_h2()
	{
		$cfeld = '<h2 id="' . $this->praefix . "_" . $this->name_org .
			'" class="h2" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</h2>';
		$this->cfeld = $cfeld;
	}
	function create_h3()
	{
		$cfeld = '<h3 id="' . $this->praefix . "_" . $this->name_org .
			'" class="h3" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</h3>';
		$this->cfeld = $cfeld;
	}

	function create_p()
	{
		$cfeld = '<p id="' . $this->praefix . "_" . $this->name_org .
			'" class="h1" >{$plugin_' . $this->praefix . "_" . $this->name_org . '}</p>';
		$this->cfeld = $cfeld;
	}
}

$rapid_db = new rapid_db_class();
