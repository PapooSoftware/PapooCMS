<?php

/**
 * Class landing_pagesplugin_class
 */
class landing_pagesplugin_class
{
	/**
	 * landing_pagesplugin_class constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $user, $db_abs, $db;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->cms = &$cms;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html" ) {
				//$this->get_fb_data();
				if (stristr($template2,"landing_pages")) {
					$this->landing_pages();
				}
			}
		}
	}

	function landing_pages()
	{
		$this->content->template['lp_self_link']="./plugin.php?menuid=".$this->checked->menuid."&template=landing_pages/templates/landing_pages_back_cat.html";
		$this->content->template['lp_self_link_site']="./plugin.php?menuid=".$this->checked->menuid."&template=landing_pages/templates/landing_pages_back_sites.html";

		if (is_numeric($this->checked->lp_site_aktu_id)) {
			require_once PAPOO_ABS_PFAD."/plugins/landing_pages/lib/class_create_site.php";
			$class_create_site->create_page($this->checked->lp_site_aktu_id);
		}
		if ($this->checked->template=="landing_pages/templates/landing_pages_back_sites.html") {
			$this->get_site_liste();
		}
		if (!empty($this->checked->formSubmit_save_lanpag_site_copy)) {
			$this->checked->lp_site_id="new";
			$this->checked->kalender_interne_bezeichnung="COPY - ".$this->checked->kalender_interne_bezeichnung;
			$this->checked->formSubmit_save_lanpag_site="OK";
			$this->checked->kalender_id="";
		}

		if ($this->checked->del_site=="del") {
			$this->delete_site_entry();
		}

		if ($this->checked->lp_site_id=="new") {
			$this->create_new_lp_site_entry();
		}

		if (is_numeric($this->checked->lp_site_id)) {
			$this->edit_lp_site_entry($this->checked->lp_site_id);
		}

		if ($this->checked->lp_cat_id=="new") {
			$this->create_new_lp_entry();
		}

		if (is_numeric($this->checked->lp_cat_id)) {
			$this->edit_lp_entry($this->checked->lp_cat_id);
		}

		if ($this->checked->message_planding_page=="saved") {
			$this->content->template['is_eingetragen']="ok";
		}

		if ($this->checked->message_planding_page=="del") {
			$this->content->template['is_eingetragen']="del";
		}
		$this->get_cat_liste();
	}

	/**
	 * pkalenderplugin_class::delete_pcal()
	 * Kalender l�schen
	 *
	 * @return void
	 */
	function delete_site_entry()
	{
		if (!empty($this->checked->formSubmit_del_lanpag_site)) {
			$sql=sprintf("DELETE FROM %s
										WHERE kalender_id='%d'",
				$this->cms->tbname['plugin_landing_page_sites'],
				$this->db->escape($this->checked->kalender_id)
			);
			$result=$this->db->query($sql);

			$this->reload("landing_pages/templates/landing_pages_back_sites.html","del");
		}

		//Daten rausholen nach Sprache f�r Liste
		$sql=sprintf("SELECT * FROM %s
										WHERE kalender_id='%d'",
			$this->cms->tbname['plugin_landing_page_sites'],
			$this->db->escape($id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$this->content->template['kalender']=$result;

		$this->content->template['kalender_del']="ok";
	}

	/**
	 * landing_pagesplugin_class::create_new_lp_site_entry()
	 *
	 * @return void
	 */
	function create_new_lp_site_entry()
	{
		//Zuerst einen Eintrag anlegen
		$this->content->template['lp_form']="ok";

		//KAtegorien rausholen
		$this->get_cat_liste();

		//Styles
		$this->get_style_liste();

		if (!empty($this->checked->formSubmit_save_lanpag_site)) {
			$xsql['dbname'] = "plugin_landing_page_sites";
			$xsql['praefix'] = "kalender";
			$xsql['must'] = array("kalender_interne_bezeichnung","kalender_kategori_der_sseite","kalender_domain");
			//$xsql['where_name'] = "config_id";
			//$this->checked->kalender_id =1;
			#$xsql['must'] = array("produkte_lang_internername");
			$insert=$this->db_abs->insert($xsql);
			if (is_numeric($insert['insert_id'])) {
				$this->reload("landing_pages/templates/landing_pages_back_sites.html","saved");
			}
		}
	}

	/**
	 * pkalenderplugin_class::change_pcalender_entry()
	 * Einen Kalender kann man hier bearbeiten
	 * @param integer $id
	 * @return void
	 */
	function edit_lp_site_entry($id=0)
	{
		//Nur wenn wirklich numerische ID dann beabeiten
		if (is_numeric($id))
		{
			$this->content->template['lp_form']="ok";

			//KAtegorien rausholen
			$this->get_cat_liste();

			//Styles
			$this->get_style_liste();

			if (!empty($this->checked->formSubmit_save_lanpag_site)) {
				$xsql['dbname'] = "plugin_landing_page_sites";
				$xsql['praefix'] = "kalender";
				$xsql['must'] =  array("kalender_interne_bezeichnung","kalender_kategori_der_sseite","kalender_domain");
				$xsql['where_name'] = "kalender_id";
				//$this->checked->kalender_id =1;

				$update=$this->db_abs->update($xsql);
				if (is_numeric($update['insert_id'])) {
					$this->content->template['is_eingetragen']="ok";
				}
			}

			//Daten rausholen nach Sprache f�r Liste
			$sql=sprintf("SELECT * FROM %s
										WHERE kalender_id='%d'",
				$this->cms->tbname['plugin_landing_page_sites'],
				$this->db->escape($id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$result['0']['kalender_meta_description']="nobr:".$result['0']['kalender_meta_description'];
			$result['0']['kalender_meta_keywords']="nobr:".$result['0']['kalender_meta_keywords'];
			$result['0']['kalender_content']="nobr:".$result['0']['kalender_content'];

			$this->content->template['kalender']=$result;
		}
	}


	/**
	 * landing_pagesplugin_class::get_cat_liste()
	 *
	 * @return void
	 */
	function get_cat_liste()
	{
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_landing_page']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['landing_page_cat_liste']=$result;
	}

	/**
	 * landing_pagesplugin_class::get_cat_liste()
	 *
	 * @return void
	 */
	function get_site_liste()
	{
		$sql=sprintf("SELECT DISTINCT * FROM %s
									LEFT JOIN %s 
									kalender_kategori_der_sseite ON landing_page_id
									WHERE kalender_kategori_der_sseite = landing_page_id
									ORDER BY kalender_kategori_der_sseite ASC, kalender_id ASC
									",
			$this->cms->tbname['plugin_landing_page_sites'],
			$this->cms->tbname['plugin_landing_page']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$this->content->template['landing_page_site_liste']=$result;
	}

	/**
	 * pkalenderplugin_class::create_new_calender_entry()
	 *
	 * @return void
	 */
	function create_new_lp_entry()
	{
		//Zuerst einen Eintrag anlegen
		$this->content->template['lp_form']="ok";

		if (!empty($this->checked->formSubmit_save_lanpag)) {
			$xsql['dbname'] = "plugin_landing_page";
			$xsql['praefix'] = "landing_page";
			$xsql['must'] = array("landing_page_der_name_der_kategorie_");
			//$xsql['where_name'] = "config_id";
			//$this->checked->kalender_id =1;
			#$xsql['must'] = array("produkte_lang_internername");
			$insert=$this->db_abs->insert($xsql);
			if (is_numeric($insert['insert_id'])) {
				$this->reload("","saved");
			}
		}
	}

	/**
	 * pkalenderplugin_class::change_pcalender_entry()
	 * Einen Kalender kann man hier bearbeiten
	 * @param integer $id
	 * @return void
	 */
	function edit_lp_entry($id=0)
	{
		//Nur wenn wirklich numerische ID dann beabeiten
		if (is_numeric($id)) {
			$this->content->template['lp_form']="ok";

			if (!empty($this->checked->formSubmit_save_lanpag)) {

				$xsql['dbname'] = "plugin_landing_page";
				$xsql['praefix'] = "landing_page";
				$xsql['must'] = array("landing_page_der_name_der_kategorie_");
				$xsql['where_name'] = "landing_page_id";
				//$this->checked->kalender_id =1;

				$update=$this->db_abs->update($xsql);
				if (is_numeric($update['insert_id'])) {
					$this->content->template['is_eingetragen']="ok";
				}
			}

			//Daten rausholen nach Sprache f�r Liste
			$sql=sprintf("SELECT * FROM %s
										WHERE landing_page_id='%d'",
				$this->cms->tbname['plugin_landing_page'],
				$this->db->escape($id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$this->content->template['landing_page']=$result;
		}
	}

	/**
	 * pkalenderplugin_class::reload()
	 * Seite neu laden mit den gegebenen Parametern
	 *
	 * @param string $template
	 * @param string $dat
	 * @return void
	 */
	function reload($template = "",$dat="")
	{
		$url = "menuid=" . $this->checked->menuid;

		if (!empty($template)) {
			$url .= "&template=" . $template;
		}
		else {
			$url .= "&template=landing_pages/templates/landing_pages_back_cat.html";
		}
		if (!empty($dat)) {
			$url .= "&message_planding_page=" . $dat;
		}

		$self=$_SERVER['PHP_SELF'];

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' .
				$this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	function get_style_liste()
	{
		$temp_return = array();

		$sql = sprintf("SELECT * FROM %s ORDER BY style_name ASC ",
			DB_PRAEFIX . "papoo_styles"
		);
		$temp_return = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['lp_style_liste']=$temp_return;
	}
}

$landing_pagesplugin = new landing_pagesplugin_class();
