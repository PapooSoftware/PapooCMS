<?php

/**
 * Class leadtracker_download_class
 */
#[AllowDynamicProperties]
class leadtracker_download_class
{
	function __construct()
	{
		global $db, $db_abs, $cms, $user, $checked, $content;
		$this->db = & $db;
		$this->db_abs = & $db_abs;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->content = & $content;
	}

	function do_lead_tracker_download()
	{
		//OK - hier jetzt checken ob es sich um eine Downloaddatei handelt die per Formular gesperrt ist..
		$result =$this->get_verknuepfungen($this->checked->downloadid);

		//erstmal auf true setzen für Standard
		$this->no_download_sofort=true;

		//Wenn ja, dann Formular ausgeben.
		if (count($result)>=1) {
			$this->checked->download_form="ok";

			//Setzen damit kein Download stattfindet...
			$this->no_download_sofort=false;
		}
	}

	/**
	 * @param int $id
	 * @return array|string|null
	 */
	private function get_verknuepfungen($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "plugin_leadtracker";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("leadtracker_die_downloaddatei" => $id);
		$result = $this->db_abs->select( $xsql );

		return $result;
	}
}
