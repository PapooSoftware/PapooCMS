<?php

/**
 * Class geo_redirect_plugin_class
 */
#[AllowDynamicProperties]
class geo_redirect_plugin_class
{
	/**
	 * geo_redirect_plugin_class::geo_redirect_plugin_class()
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

			if ($template2 != "login.utf8.html" && $template2=="geo_redirect_back.html") {
				//Eintr�ge erstellen
				$this->create_entries();

				//Liste rausholen
				$this->get_liste();
			}
		}
		else {
			$this->do_redirect_frontend();
		}
	}

	/**
	 * geo_redirect_plugin_class::do_redirect_frontend()
	 *
	 * IP geht vor Domain redirect
	 *
	 *
	 * @return void
	 */
	private function do_redirect_frontend()
	{
		//Aktuelle Sprache
		$aktu_lang=$this->cms->lang_id;

		//IP ADresse
		$ip=$_SERVER['REMOTE_ADDR'];

		if (function_exists('geoip_country_code_by_name')) {
			//Land der IP Adresse
			$country = geoip_country_code_by_name($ip);

			//Landzuweisung rausholen
			$sql=sprintf("SELECT * FROM %s
							WHERE geo_redirect_ip_countrycodeip='%s'

							",
				$this->cms->tbname['plugin_geo_redirect_ip'],
				$this->db->escape($country)
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//darauf achten dass keine Endlosschleife l�uft...
			if ($result['0']['geo_redirect_ip_sprache_erzwingen'] !=$aktu_lang && !empty($result) ) {
				//SprachK�rzel rausholen
				$lang_short=$this->get_lang_short($result['0']['geo_redirect_ip_sprache_erzwingen']);

				//Neu Laden mit SprachID...
				$url=$_SERVER['REQUEST_URI'];

				if (!stristr($url,'?')) {
					$location_url = $url."?getlang=".$lang_short;
				}
				else {
					$location_url = $url."&getlang=".$lang_short;
				}

				#exit();
				if (!empty($location_url)) {
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}

			}
		}
		//Wenn bis jetzt noch nix passiert ist, Domain checken
		$domain_pur=$_SERVER['HTTP_HOST'];
		$domain=str_replace('www.',"",$domain_pur);

		//Rausholen ob red
		$sql=sprintf("SELECT * FROM %s
						WHERE geo_redirect_domain__domainname='%s'

						",
			$this->cms->tbname['plugin_geo_redirect_domain'],
			$this->db->escape($domain)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (isset($result['0']['geo_redirect_domain__sprache_die_erzwungen_werden_soll']) &&
			$result['0']['geo_redirect_domain__sprache_die_erzwungen_werden_soll'] !=$aktu_lang && !empty($result) ) {
			// Neu Laden mit SprachID...
			//$url=$_SERVER['REQUEST_URI'];

			// SprachK�rzel rausholen
			//$lang_short=$this->get_lang_short($result['0']['geo_redirect_domain__sprache_die_erzwungen_werden_soll']);

			//if (!stristr($url,'?')) {
			//	$location_url = $url."?getlang=".$lang_short;
			//}
			//else {
			//	$location_url = $url."&getlang=".$lang_short;
			//}

			if (!empty($location_url)) {
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}
		}
	}

	/**
	 * geo_redirect_plugin_class::get_lang_short()
	 *
	 * @param $id
	 * @return void|array
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
	 * geo_redirect_plugin_class::get_liste()
	 *
	 * @return void
	 */
	private function get_liste()
	{
		//Datenn mit LIMIT rausholen  GROUP BY 	aktiv_plugin_real_domain
		$sql=sprintf("SELECT * FROM %s
						ORDER BY geo_redirect_domain__id ASC
						",
			$this->cms->tbname['plugin_geo_redirect_domain']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$fe_daten["domain"]=$result;
		$this->content->template['domain_geo_liste']=$result;
		$this->content->template['domain_geo_liste_self_link'] =
			"./plugin.php?menuid=".$this->checked->menuid."&del=now&template=geo_redirect/templates/geo_redirect_back.html";


		//Datenn mit LIMIT rausholen  GROUP BY 	aktiv_plugin_real_domain
		$sql=sprintf("SELECT * FROM %s
						ORDER BY geo_redirect_ip_id ASC
						",
			$this->cms->tbname['plugin_geo_redirect_ip']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$fe_daten["ip"]=$result;

		$this->content->template['domain_geo_ip_liste']=$result;
		$this->content->template['domain_geo_ip_liste_self_link'] =
			"./plugin.php?menuid=".$this->checked->menuid."&del=now&template=geo_redirect/templates/geo_redirect_back.html";

		//Sprachen rausholen die aktiv sind
		$sql=sprintf("SELECT * FROM %s WHERE 	more_lang='2'",
			$this->cms->tbname['papoo_name_language']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$fe_daten["sprachen"]=$result;

		//Daten f�r Caching speichern
		$this->diverse->write_to_file("/templates_c/redirect_cache.txt",serialize($fe_daten));
	}


	/**
	 * geo_redirect_plugin_class::create_entries()
	 *
	 * @return void
	 */
	private function create_entries()
	{
		//Sprachen rausholen
		$this->create_lang_select();

		//L�nderliste rausholen
		$this->get_country_list();

		if (!function_exists("geoip_country_code_by_name")) {
			$this->content->template['error_geo_ip']="NO GEO IP FUNCTIONS";
		}

		//Daten speichern wenn neu
		if (!empty($this->checked->formSubmit_save_domain_lang) ) {
			$xsql['dbname'] = "plugin_geo_redirect_domain";
			$xsql['praefix'] = "geo_redirect_domain_";
			$xsql['must'] = array("geo_redirect_domain__domainname","geo_redirect_domain__sprache_die_erzwungen_werden_soll");
			$cat_dat = $this->db_abs->insert( $xsql );

			//Neu Laden mit ID
			$this->reload("geo_redirect/templates/geo_redirect_back.html", $cat_dat['insert_id']);
		}


		//Daten speichern wenn neu
		if (!empty($this->checked->formSubmit_ip_lang) ) {
			$xsql['dbname'] = "plugin_geo_redirect_ip";
			$xsql['praefix'] = "geo_redirect_ip";
			$xsql['must'] = array("geo_redirect_ip_sprache_erzwingen","geo_redirect_ip_countrycodeip");
			$cat_dat = $this->db_abs->insert( $xsql );

			//Neu Laden mit ID
			$this->reload("geo_redirect/templates/geo_redirect_back.html", $cat_dat['insert_id']);
		}

		//Daten l�schen
		if (isset($this->checked->dom_geo_id) && is_numeric($this->checked->dom_geo_id)) {
			//NUr die der ID
			$sql=sprintf("DELETE FROM %s
										WHERE 	geo_redirect_domain__id = '%d' ",
				$this->cms->tbname['plugin_geo_redirect_domain'],
				$this->checked->dom_geo_id
			);
			$this->db->query($sql);
			$this->reload("geo_redirect/templates/geo_redirect_back.html");
		}

		//...
		if (isset($this->checked->dom_geo_ip_id) && is_numeric($this->checked->dom_geo_ip_id)) {
			//NUr die der ID
			$sql=sprintf("DELETE FROM %s
										WHERE 	geo_redirect_ip_id = '%d' ",
				$this->cms->tbname['plugin_geo_redirect_ip'],
				$this->checked->dom_geo_ip_id
			);
			$this->db->query($sql);
			$this->reload("geo_redirect/templates/geo_redirect_back.html");
		}
	}

	/**
	 * geo_redirect_plugin_class::get_country_list()
	 *
	 * @return void
	 */
	private function get_country_list()
	{
		$countries=	file_get_contents(PAPOO_ABS_PFAD."/plugins/geo_redirect/lib/country_list.txt");
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
	 * geo_redirect_plugin_class::create_lang_select()
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
	 * geo_redirect_plugin_class::reload()
	 *
	 * @param string $dat
	 * @param string $dat2
	 * @return void
	 */
	function reload($dat="",$dat2="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=geo_redirect/templates/geo_redirect_back.html&is_saved_data=ok";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$geo_redirect_plugin_class = new geo_redirect_plugin_class();
