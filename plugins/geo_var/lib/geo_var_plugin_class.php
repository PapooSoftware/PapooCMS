<?php

/**
 * Class geo_var_plugin
 */
class geo_var_plugin
{
	/**
	 * geo_var_plugin_class::geo_var_plugin_class()
	 *
	 * @return void
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

			#$this->create_admin();
			//aktivieren_link

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template2 != "login.utf8.html" && $template2=="geo_var_back.html") {
				//Einträge erstellen
				$this->create_entries();

				//Liste rausholen
				$this->get_liste();
			}
		}
	}

	/**
	 * geo_var_plugin_class::do_var_frontend()
	 * IP geht vor Domain var
	 *
	 * @return bool|void
	 */
	public function output_filter()
	{
		//Aktuelle Sprache
		$aktu_lang=$this->cms->lang_id;

		//IP ADresse
		$ip=$_SERVER['REMOTE_ADDR'];

		if (function_exists('geoip_country_code_by_name')) {
			//Land der IP Adresse $ip
			$country = geoip_continent_code_by_name($ip);

			if (empty($country) || $country!="EU") {
				$country="NA";
			}

			//Landzuweisung rausholen
			$sql=sprintf("SELECT * FROM %s ",
				$this->cms->tbname['plugin_geo_var']
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			//darauf achten dass keine Endlosschleife l�uft...

			$data=$result['0']['geo_var_variablen'];
			$d_a1=explode("\n",$data);

			if (!is_array($d_a1)) {
				return false;
			}

			foreach ($d_a1 as $k=>$v) {
				//Zeilen noch Daten rausholen
				$d2=explode(";",$v);

				//Kein Array, weiter
				if (!is_array($d2)) {
					continue;
				}

				$lang_dat[strtoupper($d2['0'])][$d2['1']]=$d2['2'];
			}

			global $output;

			//ersetzen
			foreach ($lang_dat[$country] as $k=>$v) {
				$output = str_ireplace($k,$v,$output);
			}
		}
	}

	/**
	 * geo_var_plugin_class::get_lang_short()
	 *
	 * @param $id
	 * @return void
	 */
	private function get_lang_short($id)
	{
		$sql=sprintf("SELECT * FROM %s 
						WHERE
						more_lang='2'
						AND lang_id='%d'",
			$this->cms->tbname['papoo_name_language'],
			$this->db->escape($id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result['0']['lang_short'];
	}

	/**
	 * geo_var_plugin_class::get_liste()
	 *
	 * @return void
	 */
	private function get_liste()
	{
		//Datenn mit LIMIT rausholen  GROUP BY 	aktiv_plugin_real_domain
		$sql=sprintf("SELECT * FROM %s 

						",
			$this->cms->tbname['plugin_geo_var']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$fe_daten["domain"]=$result;
		$this->content->template['geo_var_variablen']="nobr:".$result['0']['geo_var_variablen'];
	}


	/**
	 * geo_var_plugin_class::create_entries()
	 *
	 * @return void
	 */
	private function create_entries()
	{
		if (!function_exists("geoip_country_code_by_name")) {
			$this->content->template['error_geo_ip']="NO GEO IP FUNCTIONS";
		}
		else {
			$this->content->template['geo_ip_ok']="GEO IP FUNCTIONS INSTALLED";
		}

		//Daten speichern wenn neu
		if (!empty($this->checked->formSubmit_savelang)) {
			//Alte Einträge löschen
			$sql=sprintf("DELETE FROM %s
										 ",
				$this->cms->tbname['plugin_geo_var']
			);
			$this->db->query($sql);

			//Aktuellen Eintrag hinzufügen
			$xsql['dbname'] = "plugin_geo_var";
			$xsql['praefix'] = "geo_var";
			$xsql['must'] = array("geo_var_variablen");
			$cat_dat = $this->db_abs->insert( $xsql );

			//Neu Laden mit ID
			$this->reload("geo_var/templates/geo_var_back.html", $cat_dat['insert_id']);
		}
	}

	/**
	 * geo_var_plugin_class::get_country_list()
	 *
	 * @return void
	 */
	private function get_country_list()
	{
		$countries=	file_get_contents(PAPOO_ABS_PFAD."/plugins/geo_var/lib/country_list.txt");
		$countries_ar1=explode("\n",$countries);
		if (is_array($countries_ar1)) {
			foreach ($countries_ar1 as $key=>$value) {
				$value=trim(str_replace('"',"",$value));
				$data=explode(",",$value);
				$clist[$data['0']]=$data['1'];
			}
		}
		$this->content->template['country_list']=$clist;
	}

	/**
	 * geo_var_plugin_class::create_lang_select()
	 *
	 * @return void
	 */
	private function create_lang_select()
	{
		//Sprachen rausholen die aktiv sind
		$sql=sprintf("SELECT * FROM %s WHERE 	more_lang='2'",
			$this->cms->tbname['papoo_name_language']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['geo_lang_select']=$result;
	}

	/**
	 * geo_var_plugin_class::reload()
	 *
	 * @param string $dat
	 * @param string $dat2
	 * @return void
	 */
	function reload($dat="",$dat2="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=geo_var/templates/geo_var_back.html&is_saved_data=ok";
		if ($_SESSION['debug_stopallvar']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$geo_var_plugin = new geo_var_plugin();
