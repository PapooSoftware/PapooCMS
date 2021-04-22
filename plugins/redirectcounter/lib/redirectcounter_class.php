<?php

/**
 * Hauptdatei für das redirectcounter-Plugin.
 *
 * @author Dr. Carsten Euwens <info@papoo.de>
 */

/**
 * class redirectcounter_class
 *
 * Hauptklasse des Plugins.
 *
 * @author Dr. Carsten Euwens <info@papoo.de>
 */
class redirectcounter_class
{
	const PREFIX = 'redirectco';

	/**
	 * redirectcounter_class constructor.
	 */
	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;

		if(defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . '/plugins/', '', $template);
			$template2 = basename($template2);

			if ( $template != 'login.utf8.html' ) {
				if(stristr($template2, "redirectcounter")) {
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/redirectcounter/css';

					$this->rapid_dev();

					$this->make_redirect_count();
				}
			}
		}
		else {
			$this->fill_data();
		}
	}

	private function fill_data()
	{
		if (!empty($this->checked->prove) && $this->checked->prove=="dfgsjdfß07g20oregf") {
			$data = $this->make_redirect_count();

			IfNotSetNull($this->checked->redirectco_gesetzte_redirects_dat);
			IfNotSetNull($this->checked->redirectco_getestete_unterseiten_dat);
			IfNotSetNull($this->checked->redirectco_html_seiten_dat);
			IfNotSetNull($this->checked->redirectco_html_seiten_count_dat);
			IfNotSetNull($this->checked->redirectco_dokumente_dat);
			IfNotSetNull($this->checked->redirectco_images_dat);
			IfNotSetNull($this->checked->redirectco_domains_dat);
			IfNotSetNull($this->checked->redirectco_anzahl_vergleichsoperationen_dat);

			$this->checked->redirectco_gesetzte_redirects_dat = (int)$this->checked->redirectco_gesetzte_redirects_dat + $data['redirectco_gesetzte_redirects_dat'];
			$this->checked->redirectco_getestete_unterseiten_dat = (int)$this->checked->redirectco_getestete_unterseiten_dat + $data['redirectco_getestete_unterseiten_dat'];
			$this->checked->redirectco_html_seiten_dat = (int)$this->checked->redirectco_html_seiten_dat + $data['redirectco_html_seiten_dat'];
			$this->checked->redirectco_html_seiten_count_dat = (int)$this->checked->redirectco_html_seiten_count_dat + $data['redirectco_html_seiten_count_dat'];
			$this->checked->redirectco_dokumente_dat = (int)$this->checked->redirectco_dokumente_dat + $data['redirectco_dokumente_dat'];
			$this->checked->redirectco_images_dat = (int)$this->checked->redirectco_images_dat + $data['redirectco_images_dat'];
			$this->checked->redirectco_domains_dat = (int)$this->checked->redirectco_domains_dat + $data['redirectco_domains_dat'];
			$this->checked->redirectco_anzahl_vergleichsoperationen_dat = (int)$this->checked->redirectco_anzahl_vergleichsoperationen_dat + $data['redirectco_anzahl_vergleichsoperationen_dat'];

			//füllen
			$xsql = array();
			$xsql['dbname'] = "redirectcounter_daten";
			$xsql['praefix'] = "redirectco_";
			$xsql['must'] = array("redirectco_id");
			$xsql['where_name'] = "redirectco_id";
			$this->checked->redirectco_id = 1;

			$this->db_abs->update($xsql);
			//$cat_dat = $this->db_abs->update($xsql);
			//$insertid=$cat_dat['insert_id'];

			//ende - raus
			die("eingetragen");
			exit();
		}
	}

	/**
	 * @return mixed
	 */
	private function make_redirect_count()
	{
		$xsql = array();
		$xsql['dbname'] = "redirectcounter_daten";
		$xsql['select_felder'] = array("*");
		$xsql['where_data_like'] = " LIKE ";
		$xsql['where_data'] = array("redirectco_id" => "%%");

		$result = $this->db_abs->select($xsql);
		$this->content->template['redirect_counter']=$result['0'];

		return $result['0'];
	}

	/**
	 * @param $plugin_name
	 * @return string|null
	 */
	static public function ToSecureName($plugin_name)
	{
		$matches = array();

		if(!preg_match_all("/[a-zA-Z]+/", $plugin_name, $matches)) {
			return NULL;
		}

		$plugin_name = "";

		foreach($matches[0] as $match) {
			$plugin_name .= $match;
		}

		return strtolower($plugin_name);
	}

	/**
	 * @return bool|string
	 */
	public function GetShortPrefix()
	{
		return self::ShortName(self::PREFIX);
	}

	/**
	 * @param $input
	 * @return bool|string
	 */
	static public function ShortName($input)
	{
		return substr(self::ToSecureName($input), 0, 10);
	}

	/**
	 * Funktion die die Übertragung der rapid dev Form Daten in die Datenbank übernimmt.
	 *
	 * @uses db_abs
	 */
	private function rapid_dev()
	{
		$this->rapid_dev_insert("redirectcounter");
		$this->rapid_dev_templify("redirectcounter");
	}

	/**
	 * Holt den Inhalt der $tabelle aus der Datenbank als assoziatives array.
	 *
	 * Beispiel:
	 *  $this->get_results("testbackend", "SELECT * FROM %%"); // => enthält alle eintrage von testbackend
	 *
	 * @param string $tabelle
	 * @param string $sql SQL das ausgeführt werden soll. %% im string wird durch die übergebene Tabelle ersetzt.
	 * @param string $get_mode
	 * @return array
	 */
	private function get_results($tabelle, $sql, $get_mode = ARRAY_A)
	{
		$tabellenname = $this->db_praefix . "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form";
		$sql = preg_replace("/(?<!%)%%(?!%)/", $tabellenname, $sql);
		return $this->db->get_results($sql, $get_mode);
	}

	/**
	 * @param $tabelle
	 * @param $sql
	 * @return bool|int|mixed|mysqli_result|void
	 */
	private function query($tabelle, $sql)
	{
		$tabellenname = $this->db_praefix . "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form";
		$sql = preg_replace("/(?<!%)%%(?!%)/", $tabellenname, $sql);
		return $this->db->query($sql);
	}

	/**
	 * @param $tabelle
	 */
	private function rapid_dev_templify($tabelle)
	{
		$tabellenname = "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form";

		global $db_name;
		$sql = "SELECT DISTINCT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='{$this->db_praefix}{$tabellenname}' AND TABLE_SCHEMA='$db_name';";
		$labels = $this->db->get_results($sql, ARRAY_A);

		// Falls die Tabelle noch garnicht existiert
		if(!empty($labels)) {
			$this->content->template['plugin'][$this->GetShortPrefix()]['rapid_dev'][$tabellenname]['labels'] = $labels;

			$sql = "SELECT * FROM " . $this->db_praefix . $tabellenname;
			$this->content->template['plugin'][$this->GetShortPrefix()]['rapid_dev'][$tabellenname]['data'] = $this->db->get_results($sql, ARRAY_A);
		}
	}

	/**
	 * @param $tabelle
	 * @return array|null
	 */
	private function rapid_dev_insert($tabelle)
	{
		$tabellenname = "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form";
		$abgesendete_tabelle = "plugin_" . $this->GetShortPrefix() . "_form_tablename";

		if(isset($_REQUEST[$abgesendete_tabelle]) and $_REQUEST[$abgesendete_tabelle] == "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form") {
			$xsql = array();
			$xsql["dbname"] = $tabellenname;
			$xsql["praefix"] = $this->GetShortPrefix();

			// Siehe http://stackoverflow.com/questions/4142809/simple-post-redirect-get-code-example
			header("Location: " . $_SERVER['REQUEST_URI']);

			return $this->db_abs->insert($xsql)['insert_id'];
		}
		return NULL;
	}
}

$redirectcounter = new redirectcounter_class();
