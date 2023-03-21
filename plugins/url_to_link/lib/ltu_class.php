<?php

/**
 * Class ltu_class
 */
#[AllowDynamicProperties]
class ltu_class
{
	/**
	 * ltu_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $diverse, $user, $cms, $weiter, $db_abs;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {
				//Follow Up Mails generieren
				if ($template2=="ltu_back.html") {
					$this->make_verknuepfungen();
				}
			}
		}
	}

	/**
	 * Die Umsetzung der Verknüpfung von Downloaddateien und Formularen
	 *
	 */
	private function make_verknuepfungen()
	{
		if (!empty($this->checked->submit_ltu)) {
			//Immer nur den ersten Eintrag updaten
			$this->checked->ltu__id=1;

			$xsql = array();
			$xsql['dbname'] = "plugin_ltu";
			$xsql['praefix'] = "ltu_";
			$xsql['must'] = array("ltu__link_to_url_1", "ltu__link_to_url_2");
			$xsql['where_name'] = "ltu__id";

			$cat_dat = $this->db_abs->update( $xsql);
		}
		$this->content->template['ltu_']=$this->get_daten();
	}

	/**
	 * Alle verfügbaren Daten rausholen
	 *
	 * @return array|null|string
	 */
	private function get_daten()
	{
		$xsql = array();
		$xsql['dbname'] = "plugin_ltu";
		$xsql['select_felder'] = array("ltu__link_to_url_1", "ltu__link_to_url_2", "ltu__link_to_url_3", "ltu__link_to_url_4");
		$xsql['limit'] = "LIMIT 1";
		$xsql['where_data'] = array("ltu__id" => "1");
		$result = $this->db_abs->select($xsql);
		$result['0']['ltu__link_to_url_1'] = "nobr:" . $result['0']['ltu__link_to_url_1'];
		$result['0']['ltu__link_to_url_2'] = "nobr:" . $result['0']['ltu__link_to_url_2'];
		$result['0']['ltu__link_to_url_3'] = "nobr:" . $result['0']['ltu__link_to_url_3'];
		$result['0']['ltu__link_to_url_4'] = "nobr:" . $result['0']['ltu__link_to_url_4'];
		return $result;
	}

	/**
	 * Im Frontend die Ersetzung durchführen
	 */
	function output_filter()
	{
		global $output;
		// error_reporting(E_ALL);
		//Zuerst check ob es auch vorkommt
		if (strstr( $output,"#link_to_url")) {
			//Ausgabe erstellen
			$output=$this->create_linkintegration($output);
		}
	}

	/**
	 * @param $inhalt
	 * @return mixed|string|string[]|null
	 */
	private function create_linkintegration($inhalt)
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#link_to_url(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_daten_front($ndat['1']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 * @return mixed|string
	 */
	function get_daten_front($id = 0)
	{
		//Beispiel zur�ckgeben.
		$data=$this->get_daten();

		//Lookup Arrays erstellen
		$this->create_lookup_array($data);

		switch($id) {
			case 1:
				$replace=$this->create_link($this->lookup_dat_eins);
				break;
			case 2:
				$replace=$this->create_link($this->lookup_dat_zwei);
				break;
			case 3:
				$replace=$this->create_link($this->lookup_dat_drei);
				break;
			case 4:
				$replace = $this->create_link($this->lookup_dat_vier);
				break;
			default:
				$replace = NULL;
				break;
		}
		return $replace;
	}

	/**
	 * @param array $link_data
	 * ersetzt den Platzhalter mit dem gewünschten Link oder Standard
	 * @return mixed|string
	 */
	private function create_link($link_data=array())
	{
		//url zuweisen
		$url=$_SERVER['REQUEST_URI'];

		//Wenn die url existiert- korrekt zurückgeben
		if (!empty($link_data[$url])) {
			return $link_data[$url];
		}
		//Sonst Standard
		elseif (!empty($link_data['Standard'])) {
			return $link_data['Standard'];
		}
		else {
			return "Keine Daten vorhanden";
		}
	}

	/**
	 * @param $data
	 * Die Lookup Array erstellen
	 */
	private function create_lookup_array($data)
	{
		$this->lookup_dat_eins=$this->explode_data($data['0']['ltu__link_to_url_1']);
		$this->lookup_dat_zwei=$this->explode_data($data['0']['ltu__link_to_url_2']);
		$this->lookup_dat_drei=$this->explode_data($data['0']['ltu__link_to_url_3']);
		$this->lookup_dat_vier = $this->explode_data($data['0']['ltu__link_to_url_4']);
	}

	/**
	 * @param array|string $eins_data
	 * @return array|bool
	 * Aus den Daten Arrays erstellen damit leichter ausgelesen werden kann
	 */
	private function explode_data($eins_data=array())
	{
		//Einzelne Zeilen erstellen
		$eins=explode("\n",$eins_data);
		$return=array();

		//Kein Array, dann raus
		if (!is_array($eins)) {
			return false;
		}

		//Array aus einzelnen Zeilen erstellen
		foreach ($eins as $k=>$v) {
			$v = str_ireplace("nobr:", "", $v);
			$expl = explode(";", $v);
			IfNotSetNull($expl['1']);
			$return[$expl['0']] = trim($expl['1']);
		}
		return $return;
	}
}

$ltu_class = new ltu_class();
