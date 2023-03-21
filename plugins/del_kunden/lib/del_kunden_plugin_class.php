<?php

/**
 * Class del_kunden_plugin
 */
#[AllowDynamicProperties]
class del_kunden_plugin
{
	/**
	 * del_kunden_plugin_class::del_kunden_plugin_class()
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

			//aktivieren_link
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template2 != "login.utf8.html" && $template2=="del_kunden.html") {
				//Eintr�ge erstellen
				$this->delete_kunden();
			}
		}
	}

	/**
	 * del_kunden_plugin_class::do_var_frontend()
	 *
	 * IP geht vor Domain var
	 *
	 * @return void
	 */
	public function output_filter()
	{

	}

	/**
	 * del_kunden_plugin_class::get_liste()
	 *
	 * @return void
	 */
	private function delete_kunden()
	{
		if (!empty($this->checked->del_kunden_jetzt_alle_kunden_lschen)) {
			//Datenn mit LIMIT rausholen  GROUP BY 	aktiv_plugin_real_domain
			$sql=sprintf("DELETE FROM %s
                      WHERE extended_user_user_id > 10
						",
				$this->cms->tbname['plugin_shop_crm_extended_user']
			);
			$result=$this->db->query($sql);

			$this->content->template['del_kunden_variablen']="ok";
		}
	}

	/**
	 * del_kunden_plugin_class::reload()
	 *
	 * @param string $dat
	 * @param string $dat2
	 * @return void
	 */
	function reload($dat="",$dat2="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=del_kunden/templates/del_kunden_back.html&is_saved_data=ok";
		if ($_SESSION['debug_stopallvar']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$del_kunden_plugin = new del_kunden_plugin();
