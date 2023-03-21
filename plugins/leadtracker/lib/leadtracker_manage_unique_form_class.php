<?php

/**
 * Class leadtracker_manage_unique_form_class
 */
#[AllowDynamicProperties]
class leadtracker_manage_unique_form_class
{
	/**
	 * leadtracker_manage_unique_form_class constructor.
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

		$this->set_vars();

		if ( defined("admin") ) {
			$this->user->check_intern();

			global $template;

			//$this->content->template['is_dev'] = "OK";

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html") {
				//Erstmal den Link setzen
				$this->content->template['follow_up_standard_basis_link']="plugin.php?menuid=".$this->checked->menuid."&template=";

				//Follow Up Mails generieren
				if ($template2=="leadtracker_unique_form.html") {
					if(isset($this->checked->check_autorefill) && $this->checked->check_autorefill) {
						foreach((Array)$this->checked as $key => $value) {
							if(preg_match("/form_id\-.*/", $key)) {
								preg_match('/\d*$/', $key, $match1);
								$sql = sprintf("UPDATE %s
                        						SET `leadtracker_autofill` = 0
                        						WHERE `leadtracker_form_id` = %d",
									$this->cms->tbname['plugin_leadtracker'],
									(int)$match1[0]
								);
								$this->db->query($sql);
							}
							if(preg_match("/autorefill\-.*/", $key)) {
								preg_match('/\d*$/', $key, $match2);
								$sql = sprintf("UPDATE %s
                        			        	SET `leadtracker_autofill` = 1
                        				        WHERE `leadtracker_form_id` = %d",
									$this->cms->tbname['plugin_leadtracker'],
									(int)$match2[0]
								);
								$this->db->query($sql);
							}
						}
					}
					#$this->make_follow_up_liste();
				}

				if ($template2=="leadtracker_follow_up_manager_set_follow_form.html") {
					$this->make_follow_up_neu_form();
				}

				//Follow Upmails mit Download Dateien verknüpfen
				if ($template2=="leadtracker_unique_form.html") {
					$this->make_formular_verknuepfung();
				}

				if ($template2=="leadtracker_follow_up_manager.html") {
					$this->manage_follow_up_mails();
				}
			}
		}
	}

	private function set_vars()
	{
		/**
		 * welche Daten sollen an das 2. Formular weitergegeben werden
		 * 1 = Daten aus vorherigem Formularanschluss
		 * 2 = Daten aus dem Checked Objekt das beim 1. übergeben wird - damit man eine Vorauswahl übergeben kann
		 */
		$this->gib_werte_weiter=2;
	}

	/**
	 * Das Formular bei Neuauswahl mit einem anderen? verknüpfen...
	 */
	private function make_follow_up_neu_form()
	{
		if (!empty($this->checked->submit_neu_form)) {
			if (empty($this->checked->leadtracker_formular_direkt_neu_laden)) {
				$this->checked->leadtracker_formular_direkt_neu_laden=0;
			}
			$this->checked->leadtracker_form_id=$this->checked->set_fum;
			$xsql=array();
			$xsql['dbname']         = "plugin_leadtracker";
			$xsql['praefix']        = "leadtracker_";
			$xsql['must']           = array( "leadtracker_form_id" );
			$xsql['where_name']     = "leadtracker_form_id";

			$this->db_abs->update( $xsql );

			$this->content->template['is_eingetragen']="ok";
		}

		//Alle Formulare
		$result=$this->get_liste_form("all");
		$this->content->template['all_forms']=$result;

		//Verknuepfungsdaten
		$verkn=$this->get_verknuepfungen($this->checked->set_fum);

		//Noch keine Verknüpfung, dann selbst verknüpfen... das ist auch das Fallback im Frontend...
		if ($verkn['0']['leadtracker_formular_fr_neuauswahl_form']<1) {
			$verkn['0']['leadtracker_formular_fr_neuauswahl_form']=$this->checked->set_fum;
		}

		//die ID des Neuasuwahl Formulares zuweisen
		$this->content->template['aktu_form_id_select']=$verkn['0']['leadtracker_formular_fr_neuauswahl_form'];
		$this->content->template['leadtracker_formular_direkt_neu_laden']=$verkn['0']['leadtracker_formular_direkt_neu_laden'];

		//Die ID des Formulares zuweisen
		$this->content->template['aktu_form_id']=$this->checked->set_fum;
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

	/**
	 * Lookup eintragen
	 */
	private function save_lookups()
	{
		$xsql=array();
		$this->checked->leadtracker_form_id=(int) $this->checked->form_manager_id;
		$this->checked->leadtracker_uid=$_COOKIE['uid'];
		$this->checked->leadtracker_lead_id=(int) $this->checked->leadtracker_lead_id;

		$sql = sprintf("SELECT
                            t1 . form_manager_content_lead_id_id AS lead_id,
                            t1 . form_manager_content_lead_feld_name AS `name`,
                            t1 . form_manager_content_lead_feld_content AS content
                        FROM
                            %s AS t1,
                            %s AS t2
                        WHERE
                            t1 . form_manager_content_lead_feld_name = t2 . plugin_cform_name
                            AND t2 . plugin_cform_type = 'email'
                            AND t1 . form_manager_content_lead_id_id = %d
                        GROUP BY t1 . form_manager_content_lead_id_id",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$this->cms->tbname['papoo_plugin_cform'],
			$this->checked->leadtracker_lead_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if($result) {
			$sql = sprintf("SELECT
                                t1 . form_manager_lead_id AS lead_id,
                                t2 . leadtracker_fum_id AS fum_id,
                                t2 . leadtracker_checkreplace as isset_checkreplace
                            FROM
                                %s AS t1,
                                %s AS t2
                            WHERE
                                t1 . form_manager_form_id = t2 . leadtracker_fum_form_id
                                AND t1 . form_manager_lead_id = %d",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['plugin_leadtracker_follow_up_mails'],
				$this->checked->leadtracker_lead_id
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			if($result) {
				foreach($result as $row) {
					$filllookup = true;
					if ($row['isset_checkreplace'] !== NULL) {
						$sql = sprintf("SELECT
                                            t1 . form_manager_content_lead_feld_content AS content,
                                            t2 . plugin_cform_id AS check_id
                                        FROM
                                            %s AS t1,
                                            %s AS t2
                                        WHERE
                                            t1 . form_manager_content_lead_feld_name = t2 . plugin_cform_name
                                            AND t2 . plugin_cform_type = 'check_replace'
                                            AND t1 . form_manager_content_lead_id_id = %d
                                        GROUP BY t1 . form_manager_content_lead_id_id",
							$this->cms->tbname['papoo_form_manager_lead_content'],
							$this->cms->tbname['papoo_plugin_cform'],
							$this->checked->leadtracker_lead_id
						);
						$get_fields = $this->db->get_results($sql, ARRAY_A);
						if($row['isset_checkreplace'] == -1) {
							$field_isset = 0;
							foreach($get_fields as $row2) {
								if($row2['content']) {
									$field_isset = $field_isset + 1;
								}
							}
							if($field_isset < 2) {
								$filllookup = false;
							}
						}
						else {
							foreach ($get_fields as $row2) {
								if ($row2['check_id'] == $row['isset_checkreplace']) {
									if (!$row2['content']) {
										$filllookup = false;
									}
								}
							}
						}
					}
					if ($filllookup == false) {
						continue;
					}
					$resultintarray['lead_id'] = (int)$row['lead_id'];
					$resultintarray['fum_id'] = (int)$row['fum_id'];
					$sql = sprintf("INSERT INTO
                                        %s(`lookup_lead_fum_lead_id`, `lookup_lead_fum_fum_id`)
                                    VALUES (%d,%d)",
						$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
						$resultintarray['lead_id'],
						$resultintarray['fum_id']
					);
					$this->db->query($sql);
				}
			}
		}

		$xsql['dbname']     = "plugin_leadtracker_lookup_form_uid_leadid";
		$xsql['praefix']    = "leadtracker_";
		$xsql['must']       = array( "leadtracker_form_id" );

		$cat_dat = $this->db_abs->insert( $xsql );
		$insertid=$cat_dat['insert_id'];
	}

	/**
	 * Lead ID eines Formular Eintrages rausholen
	 *
	 * @return mixed
	 */
	private function get_lead_id()
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_form_manager_leads";
		$xsql['select_felder']  = array("form_manager_lead_id","form_manager_form_id");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("form_manager_form_datum"=>$this->checked->savetime);
		$result = $this->db_abs->select( $xsql );
		$this->checked->leadtracker_lead_id = $result['0']['form_manager_lead_id'];
		return $result['0']['form_manager_lead_id'];
	}

	/**
	 * Checken ob es schon Einen Lead des Besuchers gibt zu diesem Formular
	 *
	 * @param int $id
	 * @return array|null|string
	 */
	private function get_uid_form_vorhanden($id=0)
	{
		if (empty($_COOKIE['uid'])) {
			return false;
		}

		$xsql=array();
		$xsql['dbname']             = "plugin_leadtracker_lookup_form_uid_leadid";
		$xsql['select_felder']      = array("*");
		$xsql['limit']              = " LIMIT 1";
		$xsql['order_by']           = " leadtracker_lead_id";
		$xsql['order_by_asc_desc']  = " DESC";
		$xsql['where_data']         = array("leadtracker_form_id" => $id, "leadtracker_uid"=>$_COOKIE['uid']);
		$result = $this->db_abs->select( $xsql );
		return $result;
	}

	/**
	 * Checken ob das Formular behandelt werden soll im FUM Prozess
	 *
	 * @param int $id
	 * @return array|null|string
	 */
	private function get_form_in_follow_up($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "plugin_leadtracker";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("leadtracker_form_id" => $id );
		$result = $this->db_abs->select( $xsql );
		return $result;
	}

	/**
	 * Alle Daten eines Leads auslesen
	 *
	 * @param $id
	 * @return array|null|string
	 */
	private function get_data_from_lead_id($id)
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_form_manager_lead_content";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("form_manager_content_lead_id_id" => (int) $id );
		$result = $this->db_abs->select( $xsql );
		return $result;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function get_time_from_last_lead_id($id)
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_form_manager_leads";
		$xsql['select_felder']  = array("form_manager_form_datum");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("form_manager_lead_id" => (int) $id );
		$result = $this->db_abs->select( $xsql );
		return $result['0']['form_manager_form_datum'];
	}

	/**
	 * Läuft im Frontend durch, wird aber über das LT Plugin aktiviert und nicht über Papoo...
	 *  Wg der Weiche - das hier läuft nur wenn eine Formlar ID gesetzt ist
	 *
	 * @return bool|void
	 */
	public function post_papoo_leadtracker()
	{
		//Admin?? dann raus
		if (defined("admin")) {
			return false;
		}
		$sql=array();
		$sql['dbname'] = "plugin_leadtracker";
		$sql['select_felder'] = array("leadtracker_autofill");
		$sql['limit'] = "";
		$sql['where_data'] = array("leadtracker_form_id" => $this->checked->form_manager_id);
		$autofill = $this->db_abs->select($sql);
		//check ob das Formular behandelt werden soll
		$ok = $this->get_form_in_follow_up($this->checked->form_manager_id);

		//Kein Array, dann nicht behandeln
		if (count($ok) != 1) {
			return false;
		}

		//Id die abgefragt werden soll
		if (is_numeric($this->checked->rldif)) {
			$form_id = $this->checked->rldif;
		}
		else {
			$form_id = $this->checked->form_manager_id;
		}

		//Hat es schon einen Eintrag mit diesem Formular und dieser UID?
		$vorhanden = $this->get_uid_form_vorhanden($form_id);

		//Sonderfall - kommt aus reload, dann neu speichern
		if ($this->checked->fertig == "1" && $_SESSION['sonderfall_form_leadtracker'] == "ok") {

			//Sonderfall wieder deaktivieren
			unset($_SESSION['sonderfall_form_leadtracker']);

			/**
			 * NEUE Daten speichern
			 *
			 *///*

			//Id des Leadeintrages holen
			$this->get_lead_id();

			//Speichern
			$this->save_lookups();

			//Und Daten zuweisen
			$vorhanden = $this->get_uid_form_vorhanden($form_id);
		}

		//Noch kein Eintrag, dann normal durchlaufen
		if (empty($vorhanden)) {
			//Nur Aktion wenn gespeichert wurde...
			if ($this->checked->fertig == "1") {
				//Id des LEadeintrages holen
				$this->get_lead_id();

				//Speichern
				$this->save_lookups();
			}

		} //Schon ein Eintrag, dann direkt auf die Danke Seite, außer ...
		else {
			if($autofill[0]['leadtracker_autofill']) {
				//Es gibt schon einen Eintrag, dann die Daten rausholen
				$lead_data = $this->get_data_from_lead_id($vorhanden['0']['leadtracker_lead_id']);

				//NUr beim ersten Formular - damit das übergeben werden kann
				if (!is_numeric($this->checked->rldif) && $this->gib_werte_weiter == 2) {
					//Die checkedwerte übergeben... damit im 2. Formular die Daten ausgewählt sind die im ersten ausgewählt sein sollten
					$_SESSION['first_form_data'] = json_encode($this->checked);
				}
				if (!empty($this->checked->fertig)) {
					unset($_SESSION['first_form_data']);
				}

				//Und korrekt zuweisen  $_SESSION['formdat']
				$this->erzeuge_korekte_zuweisung($lead_data);

				/**
				 * Formmanger Daten neu übergeben... muss hier neu durchlaufen da erst nach dem
				 * Formmanager das hier durchläuft...
				 * */

				global $form_manager;
				$form_manager->front_get_form($this->checked->form_manager_id);

				//Nur wenn wir nicht schon auf der Danke Seite sind
				if (empty($this->checked->fertig) && empty($this->checked->reload_form)) {
					//Zeitpunkt des letzten Leads
					$lead_time = $this->get_time_from_last_lead_id($vorhanden['0']['leadtracker_lead_id']);

					//Es soll nicht die Danke Seite sondern das zweite Formular genommen werden..
					if ($ok['0']['leadtracker_formular_direkt_neu_laden'] == 1 && $lead_time < (time() - 1)) {
						//exit("RELOAD FORM");
						//Die ID des zweiten Formulars übergeben
						$this->reload_form_zweite_form_seite($ok['0']['leadtracker_formular_fr_neuauswahl_form'], $this->checked->form_manager_id);

						//Daten in Session wieder auf 0...
						unset($_SESSION['first_form_data']);
					}
					else {
						//Und Seite neu laden auf Zielseite gehen...
						$this->reload_form_danke_seite();
					}
				}
			}

			//Sonderfall neu geladen
			if (!empty($this->checked->reload_form)) {
				$_SESSION['sonderfall_form_leadtracker'] = "ok";
			}
		}
	}

	/**
	 * Es soll das 2. Formular übergeben werden.
	 *
	 * @param int $id
	 * @param int $rldif
	 */
	private function reload_form_zweite_form_seite($id=0,$rldif=0)
	{
		$location_url=PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid.
			"&fertig=0".
			"&template=".$this->checked->template.
			"&form_manager_id=".$id.
			"&flexid=".$this->checked->flexid.
			"&flex_mv_id=".$this->checked->flex_mv_id.
			"&style=".$this->checked->style.
			"&reload_form=ok".
			"&rldif=".$rldif.
			"&getlang=".$this->content->template['lang_short'];

		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * Daten zuweisen in Session für die Ausgabe auf der Danke Seite...
	 *
	 * @param $lead_data
	 * @return bool|void
	 */
	private function erzeuge_korekte_zuweisung($lead_data=array())
	{
		//Felder holen
		$felder=$this->get_form_felder($this->checked->form_manager_id);

		//Felder Daten und Typen zuweisen
		foreach ($felder as $k=>$v) {
			$feld[$v['plugin_cform_name']]=$v['plugin_cform_type'];
		}

		//Wenn die Daten aus der Session kommen... das sind die Daten aus der Vorauswahl
		if (!empty($_SESSION['first_form_data'])  && is_numeric($this->checked->rldif) && $this->checked->fertig!=1) {
			//Übergeben für die hidden Felder
			$lead_data_org=$lead_data;

			$lead_data=(json_decode($_SESSION['first_form_data'],true));
			//unset($_SESSION['first_form_data']);

			foreach ($lead_data as $k=>$v) {
				if ($k=="form_manager_id" || $k=="spamcode" || $k=="fertig") {
					//2. Formular - dann nix machen
					if (is_numeric($this->checked->rldif)) {
						continue;
					}
				}
				//Wenn es kein Feld aus dem Formular ist dann nix übergeben
				if (empty($feld[$k])) {
					continue;
				}

				//In Session für Danke Seite
				$_SESSION['formdat'][$k]=$v;

				//Im Reload Fall für Formular...
				$this->checked->$k=$v;
			}

			//Jetzt sind die checked Daten aus dem 1. Formular drin...

			//Jetzt müssen wir die Daten aus dem alten Formular holen die hidden sind..
			//Daten in Sessino zuweisen für die Ausgabe
			foreach ($lead_data_org as $k=>$v) {
				//Nur Daten übergeben wenn es Typ hidden ist
				if (($feld[$v['form_manager_content_lead_feld_name']]!="hidden")) {
					continue;
				}

				if ($v['form_manager_content_lead_feld_name']=="form_manager_id" && is_numeric($this->checked->rldif)) {
					continue;
				}

				//In Session für Danke Seite
				$_SESSION['formdat'][$v['form_manager_content_lead_feld_name']]=$v['form_manager_content_lead_feld_content'];

				//Im Reload Fall für Formular...
				$this->checked->{$v['form_manager_content_lead_feld_name']}=$v['form_manager_content_lead_feld_content'];
			}

			unset($_SESSION['first_form_data']);

			return true;
		}
		else {
			unset($_SESSION['formdat']);
		}

		//Kein Array, dann raus...
		if (!is_array($lead_data)) {
			return false;
		}

		//Daten in Sessino zuweisen für die Ausgabe
		foreach ($lead_data as $k=>$v) {
			//Nix wenn Session....
			if (($feld[$v['form_manager_content_lead_feld_name']]!="hidden")) {
				continue;
			}

			if ($v['form_manager_content_lead_feld_name']=="form_manager_id" && is_numeric($this->checked->rldif)) {
				continue;
			}

			//In Session für Danke Seite
			$_SESSION['formdat'][$v['form_manager_content_lead_feld_name']]=$v['form_manager_content_lead_feld_content'];

			//Im Reload Fall für Formular...
			$this->checked->{$v['form_manager_content_lead_feld_name']}=$v['form_manager_content_lead_feld_content'];
		}
	}

	/**
	 * Danke Seite neu laden mit alten Daten
	 */
	private function reload_form_danke_seite()
	{
		$location_url = PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid.
			"&fertig=1".
			"&template=".$this->checked->template.
			"&form_manager_id=".$this->checked->form_manager_id.
			"&flexid=".$this->checked->flexid.
			"&flex_mv_id=".$this->checked->flex_mv_id.
			"&style=".$this->checked->style.
			"&savetime=".$this->time_save.
			"&getlang=".$this->content->template['lang_short'];

		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * Die Umsetzung der Verknüpfung von Downloaddateien und Formularen
	 */
	private function make_formular_verknuepfung()
	{
		//Alle Formulare
		$result=$this->get_liste_form("all");

		//durchlaufen um im Template zuordnen zu können
		foreach ($result as $k=>$v) {
			$form[$v['form_manager_id']]=$v['form_manager_name'];
		}
		$this->content->template['all_forms_zuordnung'] = $form;

		//Eigenen Link setzen
		$this->content->template['leadtracker_own_link']="plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template;

		if (isset($this->checked->set_new_form_del) && is_numeric($this->checked->set_new_form_del)) {
			//Verjnuepfung lösen
			$this->del_verknuepfung((int) $this->checked->set_new_form_del);

			//Wurde gelöscht setztn
			$this->content->template['leadtracker_is_del']="ok";
		}

		//Neues übernehmen
		if (isset($this->checked->set_new_form) && is_numeric($this->checked->set_new_form)) {
			//Template auf neu setzen
			$this->content->template['is_new_form']="ok";

			//Die Daten der Datei rausholen
			$this->content->template['is_new_form_data'] = $this->get_form((int) $this->checked->set_new_form);

			//Daten speichern
			$this->save_verknuepfung((int) $this->checked->set_new_form);

			//Daten rausholen
			// $this->content->template['leadtracker_form']=$this->get_verknuepfungen((int) $this->checked->set_new_form);
		}
		//Nicht verknüpfte anzeigen
		else {
			//Tempalte setzen
			$this->content->template['leadtracker_liste_form']="ok";
		}

		//Verknuepfte Dokumente
		$this->content->template['leadtracker_verknuepfte_forms']= $this->get_liste_form(1);

		//die Liste holen mit nicht verknuepften
		$this->content->template['leadtracker_nicht_verknuepfte_forms']= $this->get_liste_form(0);
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
		$xsql['del_where_wert'] = " leadtracker_form_id='".$this->db->escape($id)."' ";
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
	 *
	 */
	private function get_verknuepfungen($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "plugin_leadtracker";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("leadtracker_form_id" => $id);
		$result = $this->db_abs->select( $xsql );

		return $result;
	}

	/**
	 * @param $id
	 * Verknuepfungen speichern
	 *
	 */
	private function save_verknuepfung($id)
	{
		//Id übergeben
		$this->checked->leadtracker_form_id=(int) $id;

		//Daten speichern submit_verknuepfung_files
		if ($id) {
			//Checken ob die Datei nicht schon verknüpft ist
			$result = $this->get_verknuepfungen($id);

			//Daten speichern wenn noch kein Eintrag zu dieser Download id
			if (count($result)<1) {
				$xsql=array();
				$xsql['dbname']     = "plugin_leadtracker";
				$xsql['praefix']    = "leadtracker_";
				$xsql['must']       = array( "leadtracker_form_id" );

				$cat_dat = $this->db_abs->insert( $xsql );
				$insertid=$cat_dat['insert_id'];
				if (is_numeric($insertid)) {
					$this->content->template['is_eingetragen']="ok";
				}
			}
			else {
				$xsql=array();
				$xsql['dbname']         = "plugin_leadtracker";
				$xsql['praefix']        = "leadtracker_";
				$xsql['must']           = array( "leadtracker_form_id" );
				$xsql['where_name']     = "leadtracker_form_id";
				$this->checked->leadtracker_form_id=$id;

				$cat_dat = $this->db_abs->update( $xsql );

				$insertid=$cat_dat['insert_id'];
				if (is_numeric($insertid)) {
					$this->content->template['is_eingetragen']="ok";
				}
			}
		}
	}

	/**
	 * Daten eines Formulars rausholen und zurückgeben
	 *
	 * @param int $id
	 * @return array|null
	 *
	 */
	private function get_form($id=0)
	{
		$sql= sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON form_manager_id=form_manager_id_id
                    WHERE form_manager_id='%d'
                    GROUP BY form_manager_id",
			DB_PRAEFIX."papoo_form_manager",
			DB_PRAEFIX."papoo_form_manager_lang",
			$this->db->escape($id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * Die Leade Daten eines Leads zurückgeben
	 *
	 * @param int $id
	 * @return array|null
	 *
	 */

	private function get_form_lead_tracker_data($id=0)
	{
		$sql= sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON form_manager_id=form_manager_id_id
                    LEFT JOIN %s On form_manager_id=leadtracker_form_id
                    WHERE form_manager_id='%d'
                    GROUP BY form_manager_id",
			DB_PRAEFIX."papoo_form_manager",
			DB_PRAEFIX."papoo_form_manager_lang",
			DB_PRAEFIX."plugin_leadtracker",
			$this->db->escape($id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * Felder eines Formulars holen vom typ
	 *
	 * @param int $id
	 * @return array|null
	 */
	private function get_form_felder($id=0)
	{
		$sql= sprintf("SELECT * FROM %s
                    WHERE plugin_cform_form_id='%d'
                    GROUP BY plugin_cform_id",
			DB_PRAEFIX."papoo_plugin_cform",
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
	private function get_liste_form($vkn=0)
	{
		// TODO: Fehlt noch LIMIT
		if ($vkn===0) {
			$where = ' WHERE leadtracker_form_id IS NULL';
		}
		else {
			$where = ' WHERE leadtracker_form_id IS NOT NULL';
			if ($vkn=="all")
			{
				$where="";
			}
		}

		//Sql  LEFT JOIN %S ON x = y
		$sql= sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON form_manager_id=form_manager_id_id
                    LEFT JOIN %s On form_manager_id=leadtracker_form_id
                    %s
                    GROUP BY form_manager_id",
			DB_PRAEFIX."papoo_form_manager",
			DB_PRAEFIX."papoo_form_manager_lang",
			DB_PRAEFIX."plugin_leadtracker",
			$where
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		foreach ($result as $key => $row) {
			$sql = sprintf('SELECT COUNT(*) FROM `%s` WHERE `leadtracker_fum_form_id` = %d',
				$this->cms->tbname['plugin_leadtracker_follow_up_mails'],
				(int)$row['form_manager_id']
			);
			$result[$key]['count_fum'] = $this->db->get_var($sql);
			if($result[$key]['count_fum'] == 0) {
				$result[$key]['count_fum'] = "-";
			}
		}

		if (count($result)<1) {
			return false;
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return mixed
	 */
	private function get_different_form_id_for_reload($id=0)
	{
		//Die ID des neuen Formlars rausholen
		$data=$this->get_form_lead_tracker_data($id);

		//Wenn kleiner 1, dann auf form_manager_id setzen
		if ($data['0']['leadtracker_formular_fr_neuauswahl_form']<1) {
			$data['0']['leadtracker_formular_fr_neuauswahl_form']=(int) $this->checked->form_manager_id;
		}

		//Die ID zurück geben
		return $data['0']['leadtracker_formular_fr_neuauswahl_form'];
	}

	public function output_filter_leadtracker()
	{
		//check ob ein anderes Formular genommen werden soll
		$form_reload_id = $this->get_different_form_id_for_reload($this->checked->form_manager_id);

		//dif Faktor - bestimmt ob Daten aus ursprünglichen Formular gesetzt werden soll mit der ID
		$rldif="";

		//Wenn anders ist, den trigger auch setzen
		if ($form_reload_id!=$this->checked->form_manager_id) {
			//Anders, dann altes Formular übergeben
			$rldif=$this->checked->form_manager_id;
		}

		global $output;

		//Reload Link setzen
		$reloadlink=PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid.
			"&fertig=0".
			"&template=".$this->checked->template.
			"&form_manager_id=".$form_reload_id.
			"&flexid=".$this->checked->flexid.
			"&flex_mv_id=".$this->checked->flex_mv_id.
			"&style=".$this->checked->style.
			"&reload_form=ok".
			"&rldif=".$rldif.
			"&getlang=".$this->content->template['lang_short'];

		$output=str_ireplace("#reload_form_link#",$reloadlink,$output);
	}
}
