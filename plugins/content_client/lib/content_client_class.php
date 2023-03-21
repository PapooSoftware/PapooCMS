<?php

/**
 * Class content_client
 */
#[AllowDynamicProperties]
class content_client
{
	/**
	 * content_client constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $user, $checked, $db, $cms, $db_abs, $diverse, $weiter, $menu;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->db_abs = &$db_abs;
		$this->diverse = & $diverse;
		$this->weiter = & $weiter;
		$this->menu = & $menu;

		//$this->check_domain();

		/*
		// Einbindung der Modul-Klasse
		global $module;
		$this->module = & $module;
		*/

		if (defined("admin") && isset($this->checked->template)) {
			$this->user->check_intern();
			global $template;

			#$this->content->template['is_dev'] = "OK"; zentrale_self
			$this->content->template['zentrale_self'] = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {
				//$this->check_domain();

				if (!empty($this->checked->saved)) {
					$this->content->template['is_eingetragen']="ok";
				}
				if (!empty($this->checked->del)) {
					$this->content->template['is_del']="ok";
				}

				//$this->get_fb_data();
				if ($template2=="content_client_back.html") {
					//Daten sicher und anzeigen
					$this->make_domain();
				}
			}
		}
	}

	/**
	 * content_client::make_domains()
	 *
	 * @return void
	 */
	private function make_domain()
	{
		$this->get_domain_data();

		IfNotSetNull($this->checked->plugin_content_client_name_der_domain);
		$this->checked->plugin_content_client_domainkey=sha1($this->checked->plugin_content_client_name_der_domain."fgnhe++#087goqe!brfh(/gk");

		//Daten speichern wenn update
		if (!empty($this->checked->formSubmit_save_domaindaten)) {
			$xsql['dbname'] = "plugin_cotent_client";
			$xsql['praefix'] = "plugin_cotent_client";
			$xsql['where_name'] = "plugin_cotent_client_id";
			$this->checked->plugin_cotent_client_id=1;
			$cat_dat = $this->db_abs->update( $xsql );
			$this->reload("content_client/templates/content_client_back.html", $cat_dat['insert_id']);
		}
	}

	/**
	 * content_client::get_domain_data()
	 *
	 * @return void
	 */
	private function get_domain_data()
	{
		$sql=sprintf("SELECT * FROM %s LIMIT 1 ",
			$this->cms->tbname['plugin_cotent_client']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$this->content->template['plugin_cotent_client']=$result;
	}

	/**
	 * content_client::reload()
	 *
	 * @param string $template
	 * @param string $id
	 * @param string $delete
	 * @return void
	 */
	function reload($template="",$id="",$delete="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$template;
		if (!empty($delete)) {
			$location_url.="&del=ok";
		}
		if (is_numeric($id)) {
			$location_url.="&saved=ok&domain_id=".$id;
		}

		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$content_client = new content_client();
