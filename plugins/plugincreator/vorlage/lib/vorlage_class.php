<?php

/**
 * Hauptdatei für das vorlage-Plugin.
 *
 * @author {__autor_name__} <{__autor_email__}>
 */

/**
 * class vorlage_class
 *
 * Hauptklasse des Plugins.
 *
 * @author {__autor_name__} <{__autor_email__}>
 */
class vorlage_class
{
	const PREFIX = '{__PREFIX__}';

	/**
	 * vorlage_class constructor.
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
			$template2 = str_ireplace( PAPOO_ABS_PFAD . '/plugins/', '', $template );
			$template2 = basename( $template2 );

			if ( $template != 'login.utf8.html' ) {
				if({__stristr_templates__}){
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/vorlage/css';

					$this->rapid_dev();

					// TODO: Backend logic hierhin
				}
			}
		}
		else {
			// TODO: Frontend logic hierhin
		}
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
		{__xsql_code__}
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
	 * @return null
	 */
	private function rapid_dev_insert($tabelle)
	{
		$tabellenname = "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form";
		$abgesendete_tabelle = "plugin_" . $this->GetShortPrefix() . "_form_tablename";

		if(isset($_REQUEST[$abgesendete_tabelle]) and
			$_REQUEST[$abgesendete_tabelle] == "plugin_" . $this->GetShortPrefix() . "_" . self::ShortName($tabelle) . "_form") {
			$xsql = array();
			$xsql["dbname"] = $tabellenname;
			$xsql["praefix"] = $this->GetShortPrefix();

			// Siehe http://stackoverflow.com/questions/4142809/simple-post-redirect-get-code-example
			header("Location: " . $_SERVER['REQUEST_URI']);

			return $this->db_abs->insert($xsql)['insert_id'];
		}
		return NULL;
	}

	/**
	 * Funktion die vor dem Senden des Seiteninhalts für jedes Plugin aufgerufen wird,
	 * bei der dann per global $output und z.B. einem regulären Ausdruck der Inhalt
	 * verändert werden kann.
	 * (Backend)
	 */
	public function output_filter_admin()
	{
		#global $output;
	}

	/**
	 * Funktion die vor dem Senden des Seiteninhalts für jedes Plugin aufgerufen wird,
	 * bei der dann per global $output und z.B. einem regulären Ausdruck der Inhalt
	 * verändert werden kann.
	 * (Frontend)
	 */
	public function output_filter()
	{
		#global $output;
	}
}

$vorlage = new vorlage_class();
