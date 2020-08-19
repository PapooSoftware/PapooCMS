<?php
if ( stristr( $_SERVER['PHP_SELF'], 'pkalenderplugin_class.php' ) ) die( 'You are not allowed to see this page directly' );

/**
 * Class pkalenderplugin_class
 */
class pkalenderplugin_class
{
	/**
	 * pkalenderplugin_class constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $cms, $db_abs, $db;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;

		$this->pkalender();
	}

	function pkalender()
	{
		global $template;
		if (defined('admin')) {
			$this->user->check_intern();
			#$template=basename($template);

			if (strpos("XXX" . $template, "pkalender_back_cal.html")) {
				//Einstellungen überabeiten
				$this->start_cal_admin();
			}

			if (strpos("XXX" . $template, "pkalender_back_date.html")) {
				//Einstellungen überabeiten
				$this->start_date_admin();
			}
		}
		else {
			require_once PAPOO_ABS_PFAD."/plugins/pkalender/lib/class_cal_front.php";
			$class_cal_front->get_kalender_front();
		}
	}

	function start_date_admin()
	{
		//Link erzeugen
		$this->create_self_link();

		//Wenn vorhanden Messages ausgeben
		$this->create_pcalender_message();

		if (empty($this->checked->pcal_date_id)) {
			$this->get_pcal_liste("rights");
		}

		if (!empty($this->checked->psel_cal_id)) {
			require_once PAPOO_ABS_PFAD."/plugins/pkalender/lib/class_datums.php";
			$class_datums->get_alle_datums_eines_kalenders();
		}
	}

	/**
	 * pkalenderplugin_class::start_cal_admin()
	 *
	 * @return void
	 */
	function start_cal_admin()
	{
		//Link erzeugen
		$this->create_self_link();

		//Wenn vorhanden Messages ausgeben
		$this->create_pcalender_message();

		//Es soll gelöscht werden
		if ((isset($this->checked->cal_act) && $this->checked->cal_act=="delete") || !empty($this->checked->formSubmit_delete_pcal)) {
			$this->delete_pcal();
		}
		//Normal weiter
		else {
			//Neuer Eintrag
			if (isset($this->checked->pcal_id) && $this->checked->pcal_id=="new") {
				$this->create_new_calender_entry();
			}
			//Alter Eintrag
			if (isset($this->checked->pcal_id) && is_numeric($this->checked->pcal_id)) {
				$this->change_pcalender_entry($this->checked->pcal_id);
			}
			//Liste anzeigen
			if (empty($this->checked->pcal_id)) {
				$this->get_pcal_liste();
			}
		}
	}

	/**
	 * pkalenderplugin_class::delete_pcal()
	 * Kalender löschen
	 *
	 * @return void
	 */
	function delete_pcal()
	{
		if (!empty($this->checked->formSubmit_delete_pcal)) {
			$sql=sprintf("SELECT * FROM %s
										WHERE kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$this->db->escape($this->checked->pcal_id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//Kalender mit Modul ID versorgen
			$sql=sprintf("DELETE  FROM %s
										WHERE stylemod_mod_id='%d'",
				$this->cms->tbname['papoo_styles_module'],
				$result['0']['kalender_modul_id']
			);
			$this->db->query($sql);
			$sql=sprintf("DELETE  FROM %s
										WHERE mod_id='%d'",
				$this->cms->tbname['papoo_module'],
				$result['0']['kalender_modul_id']
			);
			$this->db->query($sql);
			$sql=sprintf("DELETE  FROM %s
										WHERE 	modlang_mod_id='%d'",
				$this->cms->tbname['papoo_module_language'],
				$result['0']['kalender_modul_id']
			);
			$this->db->query($sql);

			//Kalender mit Modul ID versorgen
			$sql=sprintf("DELETE  FROM %s
										WHERE stylemod_mod_id='%d'",
				$this->cms->tbname['papoo_styles_module'],
				$result['0']['kalender_modul_liste_id']
			);
			$this->db->query($sql);
			$sql=sprintf("DELETE  FROM %s
										WHERE mod_id='%d'",
				$this->cms->tbname['papoo_module'],
				$result['0']['kalender_modul_liste_id']
			);
			$this->db->query($sql);
			$sql=sprintf("DELETE  FROM %s
										WHERE 	modlang_mod_id='%d'",
				$this->cms->tbname['papoo_module_language'],
				$result['0']['kalender_modul_liste_id']
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
										WHERE  kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$this->db->escape($this->checked->pcal_id)
			);
			$this->db->query($sql);

			//Leserechte löschen
			$sql=sprintf("DELETE  FROM %s
													WHERE kalender_id_id='%d'",
				$this->cms->tbname['plugin_kalender_lookup_read'],
				$this->db->escape($this->checked->pcal_id)
			);
			$this->db->query($sql);

			//Schreibrechte lösöchen
			$sql=sprintf("DELETE  FROM %s
													WHERE kalender_wid_id='%d'",
				$this->cms->tbname['plugin_kalender_lookup_write'],
				$this->db->escape($this->checked->pcal_id)
			);
			$this->db->query($sql);

			$this->reload("","del");
		}

		//Daten rausholen nach Sprache für Liste
		$sql=sprintf("SELECT * FROM %s
										WHERE kalender_lang_id='%d'
										AND kalender_id='%d'",
			$this->cms->tbname['plugin_kalender'],
			$this->cms->lang_back_content_id,
			$this->db->escape($this->checked->pcal_id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$this->content->template['kalender']=$result;

		$this->content->template['kalender_del']="ok";
	}

	/**
	 * pkalenderplugin_class::get_pcal_liste()
	 *
	 * @param string $rights
	 * @return void
	 */
	function get_pcal_liste($rights="")
	{
		if (!empty($rights)) {
			//Daten nach Sprache und Rechten
			$sql=sprintf("SELECT * FROM %s
											LEFT JOIN %s ON kalender_wid_id=kalender_id
											LEFT JOIN %s ON kalender_gruppe_write_id=	gruppenid
											WHERE kalender_lang_id='%d'
											AND userid='%d'
											GROUP BY kalender_id",
				$this->cms->tbname['plugin_kalender'],
				$this->cms->tbname['plugin_kalender_lookup_write'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->lang_back_content_id,
				$this->user->userid
			);
		}
		else {
			//Daten rausholen nach Sprache für Liste
			$sql=sprintf("SELECT * FROM %s
											WHERE kalender_lang_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$this->cms->lang_back_content_id
			);
		}

		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			$i=0;
			foreach ($result as $key=>$value) {
				$sql=sprintf("SELECT COUNT(pkal_date_id) FROM %s
												WHERE pkal_date_kalender_id='%d'",
					$this->cms->tbname['plugin_kalender_date'],
					$this->db->escape($value['kalender_id'])
				);
				$result[$i]['anzahl_termine']=$this->db->get_var($sql);
				$i++;
			}
		}
		$this->content->template['pcal_liste']=$result;
	}

	/**
	 * pkalenderplugin_class::create_self_link()
	 *
	 * @return void
	 */
	function create_self_link()
	{
		$this->content->template['pkal_self1']="./plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&pcal_id=";
		$this->content->template['pkal_self2']="./plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."";
	}

	/**
	 * pkalenderplugin_class::create_new_calender_entry()
	 *
	 * @return void
	 */
	function create_new_calender_entry()
	{
		//Zuerst einen Eintrag anlegen
		$this->content->template['edit_pcal_entry']="OK";

		//Rechte zuweisen
		$this->get_user_gruppen();

		if (!empty($this->checked->formSubmit_save_pcal)) {
			$xsql['dbname'] = "plugin_kalender";
			$xsql['praefix'] = "kalender";
			$xsql['must'] = array("kalender_bezeichnung_des_kalenders");
			$this->checked->kalender_lang_id=$this->cms->lang_back_content_id;
			//$xsql['where_name'] = "config_id";
			//$this->checked->kalender_id =1;
			#$xsql['must'] = array("produkte_lang_internername");
			$insert=$this->db_abs->insert($xsql);
			if (is_numeric($insert['insert_id'])) {
				//Rechte setzen
				$this->pcal_save_cal_rights($insert['insert_id']);
				//Modul erstellen
				$this->pcal_save_modul($insert['insert_id']);
				$this->reload("","saved");
			}
		}
	}

	/**
	 * pkalenderplugin_class::pcal_save_modul()
	 *
	 * @param int $id
	 * @return bool
	 */
	function pcal_save_modul($id=0)
	{
		//MOdul ID
		$sql=sprintf("SELECT kalender_modul_id FROM %s
									WHERE kalender_id='%d'",
			$this->cms->tbname['plugin_kalender'],
			$this->db->escape($id)
		);
		$modul_id=$this->db->get_var($sql);

		//Module??
		$sql=sprintf("SELECT mod_datei FROM %s
									WHERE mod_id='%d'",
			$this->cms->tbname['papoo_module'],
			$modul_id
		);
		$mod_Datei=$this->db->get_var($sql);
		//Noch kein Modul vorhanden
		if (empty($mod_Datei)) {
			$cal_name=$this->checked->kalender_bezeichnung_des_kalenders;
			$cal_descrip=substr(strip_tags($this->checked->kalender_text_oberhalb),0,50);
			//Modul Tabelle Eintrag erstellen
			$sql=sprintf("INSERT INTO %s SET
										mod_datei='plugin:pkalender/templates/mod_pkalender_front.html'",
				$this->cms->tbname['papoo_module']
			);
			$this->db->query($sql);
			$insertid=$this->db->insert_id;

			//Sprache rausholen
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_name_language']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					//Sprachtabelle Modul Eintrag erstellen
					$sql=sprintf("INSERT INTO %s SET
												modlang_mod_id='%d',
												modlang_lang_id='%d',
												modlang_name='%s',
												modlang_beschreibung='%s'
												",
						$this->cms->tbname['papoo_module_language'],
						$insertid,
						$value['lang_id'],
						$this->db->escape($cal_name),
						$this->db->escape($cal_descrip)

					);
					$this->db->query($sql);
				}
			}
			//Kalender mit Modul ID versorgen
			$sql=sprintf("UPDATE %s SET kalender_modul_id='%d'
										WHERE kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$insertid,
				$this->db->escape($id)
			);
			$this->db->query($sql);

			//Modul Tabelle Eintrag erstellen für Modul Liste
			$sql=sprintf("INSERT INTO %s SET
										mod_datei = 'plugin:pkalender/templates/mod_pkalender_front_aktuell.html'",
				$this->cms->tbname['papoo_module']
			);
			$this->db->query($sql);
			$insertid=$this->db->insert_id;

			//Sprache rausholen
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['papoo_name_language']
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					//Sprachtabelle Modul Eintrag erstellen
					$sql=sprintf("INSERT INTO %s SET
												modlang_mod_id='%d',
												modlang_lang_id='%d',
												modlang_name='%s',
												modlang_beschreibung='%s'
												",
						$this->cms->tbname['papoo_module_language'],
						$insertid,
						$value['lang_id'],
						$this->db->escape($cal_name.$this->content->template['plugin_pkal_lsite_aktuellle']),
						$this->db->escape($cal_descrip)
					);
					$this->db->query($sql);
				}
			}
			//Kalender mit Modul ID versorgen
			$sql=sprintf("UPDATE %s SET kalender_modul_liste_id='%d'
										WHERE kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$insertid,
				$this->db->escape($id)
			);
			$this->db->query($sql);
		}
		return true;
	}

	/**
	 * pkalenderplugin_class::change_pcalender_entry()
	 * Einen Kalender kann man hier bearbeiten
	 *
	 * @param integer $id
	 * @return void
	 */
	function change_pcalender_entry($id=0)
	{
		//Nur wenn wirklich numerische ID dann beabeiten
		if (is_numeric($id)) {
			$this->content->template['edit_pcal_entry']="OK";

			if (!empty($this->checked->formSubmit_save_pcal)) {
				if (empty($this->checked->kalender_eintrge_von_aussen)) {
					$this->checked->kalender_eintrge_von_aussen=0;
				}
				if (empty($this->checked->kalender_direkt_freischalten)) {
					$this->checked->kalender_direkt_freischalten=0;
				}
				if (empty($this->checked->kalender_email_versenden_bei_neuen_eintrag_von_auen)) {
					$this->checked->kalender_email_versenden_bei_neuen_eintrag_von_auen=0;
				}

				$xsql['dbname'] = "plugin_kalender";
				$xsql['praefix'] = "kalender";
				$xsql['must'] = array("kalender_bezeichnung_des_kalenders");
				$this->checked->kalender_lang_id = $this->cms->lang_back_content_id;
				$xsql['where_name'] = "kalender_id";
				//$this->checked->kalender_id =1;

				$update=$this->db_abs->update($xsql);
				if (is_numeric($update['insert_id'])) {
					$this->content->template['is_eingetragen']="ok";
					//Rechte setzen
					$this->pcal_save_cal_rights($update['insert_id']);

					//Modul erstellen
					$this->pcal_save_modul($this->checked->kalender_id);
				}
			}
			//Rechte zuweisen
			$this->get_user_gruppen();

			//Daten rausholen nach Sprache für Liste
			$sql=sprintf("SELECT * FROM %s
										WHERE kalender_lang_id='%d'
										AND kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$this->cms->lang_back_content_id,
				$this->db->escape($id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$result['0']['kalender_text_oberhalb']="nobr:".$result['0']['kalender_text_oberhalb'];
			$result['0']['kalender_kategorien_im_kalender_jede_zeile_eine_kategorie']="nobr:".$result['0']['kalender_kategorien_im_kalender_jede_zeile_eine_kategorie'];

			$this->content->template['kalender']=$result;
		}
	}

	/**
	 * pkalenderplugin_class::save_kategorien()
	 * Kategotrien diese Kalenders speichern
	 *
	 * @param $id
	 * @return void
	 */
	function save_kategorien($id)
	{
		if (is_numeric($id)) {
			$this->checked->pcal_id=$id;
		}
		//Alte Kats löschen
		$sql=sprintf("DELETE  FROM %s
									WHERE kalender_cid_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_cats'],
			$this->db->escape($this->checked->pcal_id)
		);
		$this->db->query($sql);
		//Splitten
		$kat=$this->checked->kalender_kategorien_im_kalender_jede_zeile_eine_kategorie;
		$kat_ar=explode("\n",$kat);
		//Neue Kats eintragen
		if (is_array($kat_ar)) {
			foreach ($kat_ar as $key=>$value) {
				$value=trim($value);
				$sql=sprintf("INSERT INTO %s
													SET cat_id='%d',
													kalender_cid_id='%d',
													cat_name='%s'",
					$this->cms->tbname['plugin_kalender_lookup_cats'],
					$this->db->escape($key+1),
					$this->db->escape($this->checked->pcal_id),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * pkalenderplugin_class::pcal_save_cal_rights()
	 *
	 * @param int $id
	 * @return void
	 */
	function pcal_save_cal_rights($id=0)
	{
		//Kategorien speichern
		$this->save_kategorien($id);

		//Neu eintragen dann ID setzen
		if ($id>0) {
			$this->checked->pcal_id=$id;
		}

		//ZUerst die alten Einträge löschen
		$sql=sprintf("DELETE  FROM %s WHERE kalender_id_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_read'],
			$this->db->escape($this->checked->pcal_id)
		);
		$this->db->query($sql);

		$sql=sprintf("DELETE  FROM %s WHERE kalender_wid_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_write'],
			$this->db->escape($this->checked->pcal_id)
		);
		$this->db->query($sql);

		//Dann neu eintragen
		if (isset($this->checked->pcal_gruppe_lese) && is_array($this->checked->pcal_gruppe_lese)) {
			foreach ($this->checked->pcal_gruppe_lese as $key=>$value) {
				$sql=sprintf("INSERT INTO %s  SET kalender_id_id='%d', kalender_gruppe_lese_id='%d'",
					$this->cms->tbname['plugin_kalender_lookup_read'],
					$this->db->escape($this->checked->pcal_id),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}

		if (isset($this->checked->pcal_gruppe_write) && is_array($this->checked->pcal_gruppe_write)) {
			foreach ($this->checked->pcal_gruppe_write as $key=>$value) {
				$sql=sprintf("INSERT INTO %s SET kalender_wid_id='%d', kalender_gruppe_write_id='%d'",
					$this->cms->tbname['plugin_kalender_lookup_write'],
					$this->db->escape($this->checked->pcal_id),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * pkalenderplugin_class::get_user_gruppen()
	 * DIe Papoo Usergruppen auslesen und zuweisen
	 * @return void
	 */
	function get_user_gruppen()
	{
		//Die Gruppen raussuchen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$result_gr=$this->db->get_results($sql,ARRAY_A);

		//Leserechte
		$sql=sprintf("SELECT * FROM %s
									WHERE kalender_id_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_read'],
			$this->checked->pcal_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Gruppen durchgehen und Leserechte setzen kalender_gruppe_write_id
		if (is_array($result_gr)) {
			$i=0;
			foreach ($result_gr as $key=>$value) {
				if (is_array($result)) {
					foreach ($result as $key2=>$value2) {
						if ($value['gruppeid']==$value2['kalender_gruppe_lese_id']) {
							$result_gr[$i]['lese_rights']="ok";
						}
					}
				}
				$i++;
			}
		}

		//Schreibrechte
		$sql=sprintf("SELECT * FROM %s
									WHERE kalender_wid_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_write'],
			$this->checked->pcal_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result_gr)) {
			$i=0;
			foreach ($result_gr as $key=>$value) {
				if (is_array($result)) {
					foreach ($result as $key2=>$value2) {
						if ($value['gruppeid']==$value2['kalender_gruppe_write_id']) {
							$result_gr[$i]['write_rights']="ok";
						}
					}
				}
				$i++;
			}
		}
		$this->content->template['pcal_gruppen']=$result_gr;
	}

	/**
	 * pkalenderplugin_class::create_pcalender_message()
	 * Hier werden Nachrichten ans Template übergeben
	 * z.B. ist Eingetragen
	 *
	 * @return void
	 */
	function create_pcalender_message()
	{
		//message_pcalender
		if (isset($this->checked->message_pcalende) && $this->checked->message_pcalender=="saved") {
			$this->content->template['is_eingetragen']="ok";
		}
		if (isset($this->checked->message_pcalender) && $this->checked->message_pcalender=="del") {
			$this->content->template['is_eingetragen']="del";
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
			$url .= "&template=pkalender/templates/pkalender_back_cal.html";
		}
		if (!empty($dat)) {
			$url .= "&message_pcalender=" . $dat;
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

$pkalenderplugin = new pkalenderplugin_class();
