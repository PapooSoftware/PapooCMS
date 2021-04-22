<?php

/**
 * Class leadtracker_manage_download_class
 */
class leadtracker_manage_download_class
{
	/**
	 * leadtracker_manage_download_class constructor.
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

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html") {

				//Erstmal den Link setzen
				$this->content->template['follow_up_standard_basis_link']="plugin.php?menuid=".$this->checked->menuid."&template=";

				//Follow Up Mails generieren
				if ($template2=="leadtracker_follow_up.html") {
					$this->make_follow_up_liste();
				}

				//Follow Upmails mit Download Dateien verknüpfen
				if ($template2=="leadtracker_download_conversions.html") {
					$this->make_download_verknuepfung();
				}

				if ($template2=="leadtracker_follow_up_manager.html") {
					$this->manage_follow_up_mails();
				}
			}
		}
	}

	private function set_template_sets()
	{
		$this->template_set['download_file']=array("leadtracker_follow_up.html",
			"leadtracker_download_conversions.html",
			"leadtracker_follow_up_manager.html"
		);
	}

	/**
	 * Die Follow Up Mails managen
	 */

	private function manage_follow_up_mails()
	{
		//Läuft über eine eigene Klasse
		require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_manage_follow_up_mails.php");

		//ini
		$follow_up_mail_class= new leadtracker_follow_up_mails();

		//Admin mangen
		$follow_up_mail_class->manage_admin();
	}

	private function make_follow_up_liste()
	{
		//Erstmal den Link setzen
		$this->content->template['follow_up_new_link']="plugin.php?menuid=".$this->checked->menuid."&template=";

		//TODO hier noch DOI speichern und auslesen...
	}

	public function post_papoo()
	{
		//Achtung , es gibt einen Download... den checken
		if ($this->checked->download_form=="ok") {
			//Einbinden damit der gesteuerte Download durchgerführt werden kann
			require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_download_form_now.php");

			//ini
			$leadtracker_download= new leadtracker_download_class_form_now();
			$leadtracker_download->do_lead_tracker_download();
		}
	}

	/**
	 * Die Umsetzung der Verknüpfung von Downloaddateien und Formularen
	 */
	private function make_download_verknuepfung()
	{
		//Eigenen Link setzen
		$this->content->template['leadtracker_own_link']="plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template;

		if (is_numeric($this->checked->set_new_lead_file_del)) {
			//Verjnuepfung lösen
			$this->del_verknuepfung((int) $this->checked->set_new_lead_file_del);

			//Wurde gelöscht setztn
			$this->content->template['leadtracker_is_del']="ok";
		}

		if (is_numeric($this->checked->set_new_lead_file)) {
			//Template auf neu setzen
			$this->content->template['is_new_lead_file']="ok";

			//Die Daten der Datei rausholen
			$this->content->template['is_new_lead_file_data'] = $this->get_file((int) $this->checked->set_new_lead_file);

			//Formulare rausholen
			$this->content->template['leadtracker_formulare']=$this->get_formulare();

			//Daten rausholen
			$this->content->template['leadtracker_']=$this->get_verknuepfungen((int) $this->checked->set_new_lead_file);

			//Daten speichern
			$this->save_verknuepfung((int) $this->checked->set_new_lead_file);
		}
		else {
			//Tempalte setzen
			$this->content->template['leadtracker_liste_files']="ok";

			//Verknuepfte Dokumente
			$this->content->template['leadtracker_verknuepfte_files']= $this->get_liste_files(1);

			//die Liste holen mit nicht verknuepften
			$this->content->template['leadtracker_nicht_verknuepfte_files']= $this->get_liste_files(0);
		}
	}

	/**
	 * Eine Verknuepfung löschen
	 *
	 * @param int $id
	 */
	private function del_verknuepfung($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "plugin_leadtracker";
		$xsql['limit']          = " LIMIT 1";
		$xsql['del_where_wert'] = " leadtracker_die_downloaddatei='".$this->db->escape($id)."' ";
		$this->db_abs->delete( $xsql );
	}

	/**
	 * Alle verfügbaren Formulare rausholen
	 *
	 * @return array|null|string
	 */
	private function get_formulare()
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_form_manager";
		$xsql['select_felder']  = array("form_manager_id","form_manager_name");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("");
		$result = $this->db_abs->select( $xsql );
		return $result;
	}

	/**
	 * Die Daten einer Downloaddatei rausholen
	 *
	 * @param int $id
	 * @return array|null|string
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

	/**
	 * Verknuepfungen speichern
	 *
	 * @param $id
	 */
	private function save_verknuepfung($id)
	{
		//Daten speichern submit_verknuepfung_files
		if ($this->checked->submit_verknuepfung_files) {

			//Checken ob die Datei nicht schon verknüpft ist
			$result = $this->get_verknuepfungen($id);

			//Daten speichern wenn noch kein Eintrag zu dieser Download id
			if (count($result)<1) {
				$xsql=array();
				$xsql['dbname']     = "plugin_leadtracker";
				$xsql['praefix']    = "leadtracker_";
				$xsql['must']    = array(   "leadtracker_verknpfen_mit",
					"leadtracker_kann_muss",
					"leadtracker_die_downloaddatei"
				);

				$cat_dat = $this->db_abs->insert( $xsql );
				$insertid=$cat_dat['insert_id'];
				//debug::print_d($insertid);
				if (is_numeric($insertid)) {
					$this->content->template['is_eingetragen']="ok";
				}
			}
			else {
				$xsql=array();
				$xsql['dbname']     = "plugin_leadtracker";
				$xsql['praefix']    = "leadtracker_";
				$xsql['must']    = array(   "leadtracker_verknpfen_mit",
					"leadtracker_kann_muss",
					"leadtracker_die_downloaddatei"
				);
				$xsql['where_name']    = "leadtracker_die_downloaddatei";
				$this->checked->leadtracker_die_downloaddatei=$id;

				$cat_dat = $this->db_abs->update( $xsql );

				$insertid=$cat_dat['insert_id'];
				if (is_numeric($insertid)) {
					$this->content->template['is_eingetragen']="ok";
				}
			}
		}
	}

	/**
	 * @param int $id
	 * @return array|void
	 */
	private function get_file($id=0)
	{
		$sql= sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON downloadid=download_id
                    LEFT JOIN %s On downloadkategorie=dateien_cat_id
                    WHERE downloadid='%d'
                    GROUP BY downloadid",
			DB_PRAEFIX."papoo_download",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_kategorie_dateien",
			$this->db->escape($id)

		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * Liste aller nicht verknüpften Dateien holen
	 * gibt die Liste als Array zurück
	 *
	 * @param int $vkn
	 * @return array|null|bool
	 */
	private function get_liste_files($vkn=0)
	{
		// TODO: Fehlt noch LIMIT
		if ($vkn==0) {
			$where = ' WHERE leadtracker_die_downloaddatei IS NULL';
		}
		else {
			$where = ' WHERE leadtracker_die_downloaddatei IS NOT NULL';
		}

		//Sql  LEFT JOIN %S ON x = y
		$sql= sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON downloadid=download_id
                    LEFT JOIN %s On downloadkategorie=dateien_cat_id
                    LEFT JOIN %s On downloadid=leadtracker_die_downloaddatei
                    %s
                    GROUP BY downloadid",
			DB_PRAEFIX."papoo_download",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_kategorie_dateien",
			DB_PRAEFIX."plugin_leadtracker",
			$where

		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (count($result)<1) {
			return false;
		}
		return $result;
	}

	function output_filter()
	{

	}
}
