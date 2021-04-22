<?php

/**
 * Class flex2sitemap
 */
class flex2sitemap
{
	/**
	 * flex2sitemap constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms, $db_abs, $diverse;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->db_abs = &$db_abs;
		$this->diverse = & $diverse;

		/*
		// Einbindung der Modul-Klasse
		global $module;
		$this->module = & $module;
		*/

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {
				//$this->get_fb_data();
				if ($template2=="flex2sitemap_back.html") {
					//Daten sicher und anzeigen
					$this->make_settings();
				}
			}
		}
	}

	/**
	 * flex2sitemap::make_settings()
	 *
	 * @return void
	 */
	private function make_settings()
	{
		$this->content->template['sitemap2flex_self']="plugin.php?menuid=".$this->checked->menuid."&template=flex2sitemap/templates/flex2sitemap_back.html";

		$this->get_menupunkte();

		$this->set_menupunkte();
	}

	/**
	 * flex2sitemap::set_menupunkte()
	 *
	 * @return void
	 */
	private function set_menupunkte()
	{
		if (is_numeric($this->checked->menuid2flex)) {
			$this->content->template['is_singel_flex2sitemap']="ok";

			if (is_array($this->content->template['is_flex_menu_data'])) {
				foreach ($this->content->template['is_flex_menu_data'] as $key=>$value) {
					if ($value['menuid']==$this->checked->menuid2flex) {
						$this->content->template['is_flex_menu_data_menuname']=$value;
						//MV ID rausholen
						$mv_id=$this->get_mv_id($value);

						//Alle Felder aus der Verwaltung
						$this->content->template['is_flex_felder_liste']=$this->get_alle_felder_from_mv($mv_id);
					}
				}
			}

			//Speichern
			if (!empty($this->checked->flex2sitemap_settings)) {
				//Zuerst l�schen
				$sql=sprintf("DELETE  FROM %s
							WHERE plugin_flex2sitemap_menuid='%d'",
					$this->cms->tbname['plugin_flex2sitemap'],
					$this->db->escape($this->checked->plugin_flex2sitemap_menuid)
				);
				$this->db->query($sql);

				if (empty($this->checked->plugin_flex2sitemap_in_sitemap_erscheinen)) {
					$this->checked->plugin_flex2sitemap_in_sitemap_erscheinen=0;
				}

				//Dann speichern
				$xsql['dbname'] = "plugin_flex2sitemap";
				$xsql['praefix'] = "plugin_flex2sitemap";
				$this->checked->plugin_flex2sitemap_mvid=$mv_id;
				;
				$cat_dat = $this->db_abs->insert( $xsql );
			}

			//alte Daten rausholen
			$sql=sprintf("SELECT * FROM %s
							WHERE plugin_flex2sitemap_menuid ='%d'",
				$this->cms->tbname['plugin_flex2sitemap'],
				$this->db->escape($this->checked->menuid2flex)
			);
			$this->content->template['is_singel_flex_menu_data']=$this->db->get_results($sql,ARRAY_A);

		}
	}

	/**
	 * flex2sitemap::get_alle_felder_from_mv()
	 *
	 * @param $mv_id
	 * @return void
	 */
	private function get_alle_felder_from_mv($mv_id)
	{
		$sql=sprintf("SELECT * FROM %s
						LEFT JOIN %s ON   	mvcform_lang_id=mvcform_id
						WHERE mvcform_form_id='%d'
						AND mvcform_lang_lang='%d'
						AND mvcform_type='text' ",
			$this->cms->tbname['papoo_mvcform'],
			$this->cms->tbname['papoo_mvcform_lang'],
			$this->db->escape($mv_id),
			$this->cms->lang_back_content_id

		);
		return $this->db->get_results($sql,ARRAY_A);
	}

	/**
	 * flex2sitemap::get_mv_id()
	 *
	 * @param $data
	 * @return void
	 */
	private function get_mv_id($data)
	{
		$url=explode("mv_id",$data['menulinklang']);
		$url2=explode("&",$url['1']);

		return str_replace("=","",$url2['0']);
	}

	/**
	 * flex2sitemap::get_menupunkte()
	 *
	 * @return void
	 */
	private function get_menupunkte()
	{
		global $menu;
		$men_data=($menu->menu_data_read("FRONT"));
		if (is_array($men_data)) {
			foreach ($men_data as $key=>$value) {
				if (stristr($value['menulinklang'],"mv_search_front")) {
					$is_flex[]=$value;
				}
			}
		}

		$this->content->template['is_flex_menu_data']=$is_flex;

	}

	/**
	 * flex2sitemap::get_alle_fuer_sitemap()
	 *
	 * @return array|void
	 */
	private function get_alle_fuer_sitemap()
	{
		$sql=sprintf("SELECT * FROM %s
						WHERE  plugin_flex2sitemap_in_sitemap_erscheinen='1' 	",
			$this->cms->tbname['plugin_flex2sitemap']
		);
		return $this->db->get_results($sql,ARRAY_A);
	}

	/**
	 * flex2sitemap::post_papoo()
	 *
	 * @return void
	 */
	public function post_papoo()
	{
		//echo "Hallo Welt";
		if (!defined("admin")) {
			global $template;

			if($template=="inhalt.html") {
				//Erstmal alle rausholen f�r die Sitemap gemacht werden soll.
				$alle=$this->get_alle_fuer_sitemap();
				//print_r($alle);

				//Dann diese durchgehen und Flexinhalte zuweisen
				$flex_data=$this->get_flex_content_fuer_Sitemap($alle);
				if (is_array($this->content->template['table_data'])) {
					foreach ($this->content->template['table_data'] as $key=>$value) {
						if (in_array($value['menuid'],$this->menuids)) {
							$this->content->template['table_data'][$key]['flex']=$flex_data[$value['menuid']];
						}
					}
				}
			}
		}
	}

	/**
	 * flex2sitemap::get_flex_content_fuer_Sitemap()
	 *
	 * @param array $data
	 * @return void
	 */
	private function get_flex_content_fuer_Sitemap($data=array())
	{
		$this->menuids=array();

		if (is_array($data)) {
			foreach ($data as $key=>$value) {
				//ID der Flex
				$mv_id=$value['plugin_flex2sitemap_mvid'];

				//Menuid der Flex
				$menuid=$value['plugin_flex2sitemap_menuid'];

				$this->menuids[]=$menuid;

				//Name des Feldes
				$mv_name_feld=$this->get_feld_name($value['plugin_flex2sitemap_feld_auswhlen'])."_".$value['plugin_flex2sitemap_feld_auswhlen'];

				//Notwendige Daten einer Flex rausholen
				/**
				 * mv_id
				 * mv_content_id
				 * Linkinhalt
				 *
				 *
				 * http://localhost/papoo_trunk/
				 * plugin.php?menuid=2
				 * &template=mv/templates/mv_show_front.html
				 * &mv_id=1
				 * &extern_meta=x
				 * &mv_content_id=1
				 * */
				$dbname = "papoo_mv_content_" . $mv_id . "_search_" . $this->cms->lang_id;

				$sql=sprintf("SELECT
								mvcform_name,
								mvcform_id
								 FROM %s
								 WHERE mvcform_type = 'sprechende_url'
								 AND mvcform_form_id = %s",
					$this->cms->tbname['papoo_mvcform'],
					$mv_id
				);
				$result= $this->db->get_results($sql,ARRAY_A);

				//prüfen ob es sprechende urls für diese flex gibt
				if(!empty($result)){
					$feld_name = $result[0]['mvcform_name'] . '_' . $result[0]['mvcform_id'];
					$sql=sprintf("SELECT
								mv_content_id,
								%s AS name_feld,
								%s AS sprechendeurl_feld
								 FROM %s
						",
						$mv_name_feld,
						$feld_name,
						$this->cms->tbname[$dbname]

					);
					$result= $this->db->get_results($sql,ARRAY_A);
					//$sprechende_url = "f-1"."-". $v['mv_content_id']."-". $v['Sprechendeurl_7'].".html";
				}
				else {
					$dbname = "papoo_mv_content_" . $mv_id . "_search_" . $this->cms->lang_id;
					$sql = sprintf("SELECT
								mv_content_id,
								%s AS name_feld
								 FROM %s",
						$mv_name_feld,
						$this->cms->tbname[$dbname]
					);
					$result = $this->db->get_results($sql, ARRAY_A);

				}
				if (is_array($result)) {
					foreach ($result as $key2 => $value2) {
						$result[$key2]['menuid'] = $menuid;
						$result[$key2]['mv_id'] = $mv_id;
						if($result[$key2]['sprechendeurl_feld']) {
							$result[$key2]['sprechendeurl_feld'] = "f-".$result[$key2]['mv_id']."-". $result[$key2]['mv_content_id']."-". $result[$key2]['sprechendeurl_feld'].".html";
						}
					}
				}

				$result_data[$value['plugin_flex2sitemap_menuid']] = $result;
			}
		}
		return $result_data;
	}

	/**
	 * flex2sitemap::get_feld_name()
	 *
	 * @param mixed $id
	 * @return void
	 */
	private function get_feld_name($id)
	{
		$sql=sprintf("SELECT mvcform_name FROM %s
						WHERE mvcform_id='%d'",
			$this->cms->tbname['papoo_mvcform'],
			$id
		);
		return $this->db->get_var($sql);
	}
}

$flex2sitemap = new flex2sitemap();
