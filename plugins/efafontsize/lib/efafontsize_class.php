<?php

/**
 * Class efafontsize_class
 */
class efafontsize_class
{
	/**
	 * efafontsize_class constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $user, $checked, $cms, $db_abs, $db;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			if ( strpos( "XXX" . $template, "efafontsize_back.html" ) ) {
				//Einstellungen �berabeiten
				$this->start_efa_admin();
			}
		}
		else {
			$this->get_fontsize_dat();
		}
	}

	/**
	 * efafontsize_class::output_f()
	 *
	 * @return void
	 */
	function output_filter()
	{
		$js='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/cookies.js"></script>
		<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/efafontsize/js/efa_fontsize.js"></script>';

		global $output;
		$output = str_replace("</head>",$js."</head>",$output);
	}

	/**
	 * efafontsize_class::get_fontsize_dat()
	 *
	 * @return void
	 */
	function get_fontsize_dat()
	{
		global $module;
		$this->module = & $module;

		if (!empty($this->module->module_aktiv['mod_efafontsize_front'])) {
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['plugin_efa_fontsize']
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$this->content->template['efa_fontsize_spez']=$result;
		}

	}

	/**
	 * efafontsize_class::start_efa_admin()
	 *
	 * @return void
	 */
	function start_efa_admin()
	{
		if (isset($this->checked->message_efa) && $this->checked->message_efa=="saved") {
			$this->content->template['is_eingetragen']="ok";
		}

		//Daten rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_efa_fontsize']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['efa_fontsize_spez']=$result;

		if (!empty($this->checked->formSubmit_efa_fontsize)) {
			$sql=sprintf("DELETE  FROM %s ",
				$this->cms->tbname['plugin_efa_fontsize']
			);
			$this->db->query($sql);

			$xsql['dbname'] = "plugin_efa_fontsize";
			$xsql['praefix'] = "efa_fontsize_spez";
			$xsql['must'] = array("efa_fontsize_spez_schriftgre_default_wert",
				"efa_fontsize_spez_steigerung_pro_schritt_in_",
				"efa_fontsize_spez_maximale_schriftgre_in_",
				"efa_fontsize_spez_minimale_schriftgre_in_");

			//$xsql['where_name'] = "config_id";
			//$this->checked->kalender_id =1;
			#$xsql['must'] = array("produkte_lang_internername");
			$insert=$this->db_abs->insert($xsql);
			if (is_numeric($insert['insert_id'])) {
				$this->reload("saved");
			}
		}
	}

	/**
	 * efafontsize_class::reload()
	 *
	 * @param string $dat
	 * @return void
	 */
	function reload($dat="")
	{
		$url = "menuid=" . $this->checked->menuid;
		$url .= "&template=efafontsize/templates/efafontsize_back.html";

		if (!empty($dat)) {
			$url .= "&message_efa=" . $dat;
		}

		$self=$_SERVER['PHP_SELF'];

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$efafontsize = new efafontsize_class();
