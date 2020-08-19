<?php

if ( stristr( $_SERVER['PHP_SELF'], 'class_datums.php' ) ) die( 'You are not allowed to see this page directly' );
/**
 * shop_class
 * Die Shop Klasse realisiert den komplette Shop
 * Dazu geh�ren noch einige weitere Klassen
 * die entsprechend eingebunden werden
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
class class_datums
{
	/**
	 * shop_class::shop_class()
	 * Initialisierung und Einbindung von Klassen
	 * @return void
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $user, $checked, $cms, $db_abs, $db, $diverse;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->diverse = &$diverse;

		if (defined("admin")) {
			$this->user->check_intern();
		}
		else {
			$this->get_kategorien();
		}
	}

	/**
	 * class_datums::get_alle_datums_eines_kalenders()
	 *
	 * @return void
	 */
	function get_alle_datums_eines_kalenders()
	{
		$this->content->template['pcal_is_datum_liste']="OK";
		$this->content->template['psel_cal_id']=$this->checked->psel_cal_id;

		$this->content->template['psel_cal_name']=$this->get_cal_name($this->checked->psel_cal_id);

		$this->check_cal_zugriff();

		//Es soll gel�scht werden
		if (isset($this->checked->action) && $this->checked->action=="delete" || !empty($this->checked->formSubmit_delete_pcal)) {
			$this->delete_pcal_date();
		}
		//Normal weiter
		else {
			//Kategorien rausholen
			$this->get_kategorien();

			//Neuen Termin erstellen
			if (isset($this->checked->pcal_termin) && $this->checked->pcal_termin=="new") {
				$this->create_new_termin();
			}

			//termin bearbeiten
			if (isset($this->checked->pcal_termin) && is_numeric($this->checked->pcal_termin)) {
				$this->update_termin();
			}

			if (empty($this->checked->pcal_termin)) {
				$this->pcal_get_termin_liste();
			}
		}
	}

	function delete_pcal_date()
	{
		if (!empty($this->checked->formSubmit_delete_pcal_date)) {
			$sql=sprintf("DELETE  FROM %s
										WHERE  pkal_date_id='%d'",
				$this->cms->tbname['plugin_kalender_date'],
				$this->db->escape($this->checked->pkal_date_id)
			);
			$this->db->query($sql);

			//Leserechte l�schen
			$sql=sprintf("DELETE  FROM %s
													WHERE date_id_id='%d'",
				$this->cms->tbname['plugin_kalender_lookup_read_date'],
				$this->db->escape($this->checked->pkal_date_id)
			);
			$this->db->query($sql);


			$this->reload("","del");
		}

		//Daten rausholen nach Sprache f�r Liste
		$sql=sprintf("SELECT * FROM %s
										WHERE pkal_date_lang_id='%d'
										AND pkal_date_id='%d'",
			$this->cms->tbname['plugin_kalender_date'],
			$this->cms->lang_back_content_id,
			$this->db->escape($this->checked->pcal_termin)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['pkal_date']=$result;

		$this->content->template['date_del']="ok";
	}

	/**
	 * class_datums::pcal_get_termin_liste()
	 *
	 * @return void
	 */
	function pcal_get_termin_liste()
	{
		if (empty($this->checked->get_all_termine)) {
			//Daten rausholen nach Sprache f�r Liste
			$sql=sprintf("SELECT * FROM %s
										WHERE pkal_date_lang_id='%d'
										AND pkal_date_kalender_id='%d'
										AND pkal_date_start_datum >'%d'
										ORDER BY pkal_date_start_datum 	ASC",
				$this->cms->tbname['plugin_kalender_date'],
				$this->cms->lang_back_content_id,
				$this->db->escape($this->checked->psel_cal_id),
				time()-86400
			);
		}
		else {
			//Daten rausholen nach Sprache f�r Liste
			$sql=sprintf("SELECT * FROM %s
										WHERE pkal_date_lang_id='%d'
										AND pkal_date_kalender_id='%d'",
				$this->cms->tbname['plugin_kalender_date'],
				$this->cms->lang_back_content_id,
				$this->db->escape($this->checked->psel_cal_id)
			);
			$this->content->template['get_all_termine']="ok";
		}
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['pcal_date_liste']=$result;
	}

	/**
	 * class_datums::get_cal_name()
	 *
	 * @param integer $id
	 * @return array|null|void
	 */
	function get_cal_name($id=0)
	{
		if (is_numeric($id)) {
			$sql=sprintf("SELECT kalender_bezeichnung_des_kalenders 
										FROM %s
										WHERE  kalender_id='%d'",
				$this->cms->tbname['plugin_kalender'],
				$this->db->escape($id)
			);
			$result=$this->db->get_var($sql);
			return $result;
		}
	}

	/**
	 * class_datums::update_termin()
	 *
	 * @return void
	 */
	function update_termin()
	{
		//Nur wenn wirklich numerische ID dann beabeiten
		if (is_numeric($this->checked->pcal_termin)) {
			$id=$this->checked->pcal_termin;
			$this->content->template['pcal_is_datum_edit']="OK";

			if (!empty($this->checked->formSubmit_save_pcal_date_copy)) {
				unset($this->checked->pcal_termin);
				unset($this->checked->pkal_date_id);
				$this->checked->formSubmit_save_pcal_date=1;
				$this->create_new_termin();
				unset($this->checked->formSubmit_save_pcal_date);
			}

			if (!empty($this->checked->formSubmit_save_pcal_date)) {
				//Zeit und Datumsformat checken
				if ($this->check_date_time()) {
					//Datum umwandeln
					$this->create_time_stamps_from_date();

					if (empty($this->checked->pkal_date_eintrag_im_frontend_freischalten)) {
						$this->checked->pkal_date_eintrag_im_frontend_freischalten = 0;
					}

					$xsql['dbname'] = "plugin_kalender_date";
					$xsql['praefix'] = "pkal_date";
					$xsql['must'] = array("pkal_date_titel_des_termins");
					$this->checked->pkal_date_lang_id=$this->cms->lang_back_content_id;
					$this->checked->pkal_date_kalender_id=$this->checked->psel_cal_id;
					$xsql['where_name'] = "pkal_date_id";
					//$this->checked->kalender_id =1;

					//var_dump($this->checked);exit;
					$update = $this->db_abs->update($xsql);

					if (is_numeric($update['insert_id'])) {
						$this->content->template['is_eingetragen']="ok";
						//Rechte setzen
						$this->pcal_save_date_rights();
					}
				}
				else {
					$this->content->template['is_eingetragen']="no";
					if (is_object($this->checked)) {
						foreach ($this->checked as $key=>$value) {
							$this->content->template['pkal_date']['0'][$key]=$value;
						}
					}
				}
			}
			else {
				//Daten rausholen nach Sprache f�r Liste
				$sql=sprintf("SELECT * FROM %s
											WHERE pkal_date_lang_id='%d'
											AND pkal_date_id='%d'",
					$this->cms->tbname['plugin_kalender_date'],
					$this->cms->lang_back_content_id,
					$this->db->escape($id)
				);
				$result=$this->db->get_results($sql,ARRAY_A);
				$result['0']['pkal_date_terminbeschreibung']="nobr:".$result['0']['pkal_date_terminbeschreibung'];

				$this->content->template['pkal_date']=$result;
			}
			//Rechte zuweisen
			$this->get_user_gruppen();
		}
	}

	/**
	 * class_datums::create_time_stamps_from_date()
	 *
	 * @return void
	 */
	function create_time_stamps_from_date()
	{
		$this->checked->pkal_date_end_datum=$this->create_time_stamp($this->checked->pkal_date_end_datum);
		$this->checked->pkal_date_start_datum=$this->create_time_stamp($this->checked->pkal_date_start_datum);
	}

	/**
	 * class_datums::create_time_stamp()
	 *
	 * @param integer $date
	 * @return false|int
	 */
	function create_time_stamp($date=0)
	{
		$d=explode(".",$date);
		return mktime("0","0","0",$d['1'],$d['0'],$d['2']);
	}

	/**
	 * class_datums::check_date_time()
	 * Zeit und Datumsformat checken
	 *
	 * @return bool
	 */
	function check_date_time()
	{
		//Datum checken
		if (empty($this->checked->pkal_date_titel_des_termins)) {
			$this->content->template['plugin_error']['pkal_date_titel_des_termins']=$this->content->template['db_abs_fehlt_eintrag'];
		}

		//Datum checken
		if (!$this->check_date($this->checked->pkal_date_end_datum)) {
			$this->content->template['plugin_error']['pkal_date_end_datum']=$this->content->template['db_abs_fehlt_eintrag'];

		}
		//
		if (!$this->check_date($this->checked->pkal_date_start_datum)) {
			$this->content->template['plugin_error']['pkal_date_start_datum']=$this->content->template['db_abs_fehlt_eintrag'];

		}

		//Time checken
		if (!$this->check_time($this->checked->pkal_date_uhrzeit_beginn)) {
			$this->content->template['plugin_error']['pkal_date_uhrzeit_beginn']=$this->content->template['db_abs_fehlt_eintrag'];

		}
		if (!$this->check_time($this->checked->pkal_date_uhrzeit_ende)) {
			$this->content->template['plugin_error']['pkal_date_uhrzeit_ende']=$this->content->template['db_abs_fehlt_eintrag'];

		}

		if (!empty($this->content->template['plugin_error'])) {
			return false;
		}
		return true;
	}

	/**
	 * class_datums::check_date()
	 *
	 * @param integer $date
	 * @return bool
	 */
	function check_date($date=0)
	{
		$date_array=explode(".",$date);
		if (is_array($date_array)) {
			foreach ($date_array as $key=>$value) {
				$date_array[$key]=trim($value);
			}
		}

		if ((is_numeric($date_array['0']) and strlen($date_array['0'])==2)&&
			(is_numeric($date_array['1']) and strlen($date_array['1'])==2) &&
			(is_numeric($date_array['2']) and strlen($date_array['2'])==4) ) {
			return true;
		}
		return false;
	}

	/**
	 * class_datums::check_time()
	 *
	 * @param integer $date
	 * @return bool
	 */
	function check_time($date=0)
	{
		//Leer ist auch ok
		if (empty($date)) {
			return true;
		}

		$date_array=explode(":",$date);
		if (is_array($date_array)) {
			foreach ($date_array as $key=>$value) {
				$date_array[$key]=trim($value);
			}
		}

		if ((is_numeric($date_array['0']) and strlen($date_array['0'])==2) &&
			(is_numeric($date_array['1']) and strlen($date_array['1'])==2) ) {
			return true;
		}
		return false;
	}

	/**
	 * class_datums::create_new_termin()
	 * Neuuen Termin erstellen
	 *
	 * @return void
	 */
	function create_new_termin()
	{
		$this->content->template['pcal_is_datum_edit']="OK";

		//Rechte zuweisen
		$this->get_user_gruppen();
		if (!empty($this->checked->formSubmit_save_pcal_date)) {
			if ($this->check_date_time()) {
				//Datum umwandeln
				$this->create_time_stamps_from_date();

				$xsql['dbname'] = "plugin_kalender_date";
				$xsql['praefix'] = "pkal_date";
				$xsql['must'] = array("pkal_date_titel_des_termins");
				$this->checked->pkal_date_lang_id=$this->cms->lang_back_content_id;
				$this->checked->pkal_date_kalender_id=$this->checked->psel_cal_id;
				//$xsql['where_name'] = "config_id";
				$insert=$this->db_abs->insert($xsql);
				if (is_numeric($insert['insert_id'])) {
					//Rechte speichern
					$this->pcal_save_date_rights($insert['insert_id']);

					$sql=sprintf("SELECT * 
														FROM %s
														WHERE kalender_id='%d'",
						$this->cms->tbname['plugin_kalender'],
						$this->db->escape($this->checked->psel_cal_id)
					);
					$kal_dat=$this->db->get_results($sql,ARRAY_A);

					if ($kal_dat['0']['kalender_email_versenden_bei_neuen_eintrag_von_auen']==1 && !defined("admin")) {
						$this->send_mail_kalender($insert['insert_id'],$kal_dat);
					}

					$this->reload("","saved","front");
				}
			}
			else {
				$this->content->template['is_eingetragen']="no";
				if (is_object($this->checked)) {
					foreach ($this->checked as $key=>$value) {
						$this->content->template['pkal_date']['0'][$key]=$value;
					}
				}
			}
		}
	}

	/**
	 * class_cal_front::send_mail_kalender()
	 * $this->checked->psel_cal_id
	 *
	 * @param int $date_id
	 * @param $kal_dat
	 * @return bool
	 */
	function send_mail_kalender($date_id=0,$kal_dat)
	{
		global $mail_it;
		$this->mail_it=&$mail_it;
		$this->mail_it->to = $kal_dat['0']['kalender_email_adresse_fr_den_versand_dieser_mail'];
		$this->mail_it->from = $kal_dat['0']['kalender_email_adresse_fr_den_versand_dieser_mail'];

		$this->mail_it->from_text = $this->content->template['neuer_kal_eintrag'];
		$this->mail_it->subject = $this->content->template['neuer_kal_eintrag'];
		$this->mail_it->body = $this->content->template['neuer_kal_eintrag']."\n".$this->checked->pkal_date_titel_des_termins."\n".$this->checked->pkal_date_terminbeschreibung."\n LINK: ".$this->content->template['link'].PAPOO_WEB_PFAD."/interna/plugin.php?plugin.php?menuid=1019&template=pkalender/templates/pkalender_back_date.html&psel_cal_id=".$this->checked->psel_cal_id."&pcal_termin=".$date_id;
		$this->mail_it->priority = 5;
		$this->mail_it->do_mail();

		return true;
	}

	/**
	 * class_datums::check_cal_zugriff()
	 *
	 * @return void
	 */
	function check_cal_zugriff()
	{
		//Checken ob rechte bestehen
		$sql=sprintf("SELECT kalender_wid_id FROM %s
									LEFT JOIN %s ON kalender_gruppe_write_id=gruppenid
									WHERE userid='%d'
									AND kalender_wid_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_write'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid,
			$this->db->escape($this->checked->psel_cal_id)
		);
		$zugriff=$this->db->get_var($sql);

		if (empty($zugriff)) {
			$this->reload();
		}
	}

	/**
	 * class_datums::get_kategorien()
	 *
	 * @return void
	 */
	function get_kategorien()
	{
		if(isset($this->checked->kal_id)) {
			$sql=sprintf("SELECT * FROM %s WHERE kalender_cid_id='%d'",
				$this->cms->tbname['plugin_kalender_lookup_cats'],
				$this->db->escape($this->checked->kal_id)
			);
			$result_cat=$this->db->get_results($sql,ARRAY_A);
			$this->content->template['sort_pcal_kategorien']=$result_cat;
		}

		if(isset($this->checked->psel_cal_id)) {
			//Zuerst die Kategorien holen
			$sql=sprintf("SELECT * FROM %s WHERE kalender_cid_id='%d'",
				$this->cms->tbname['plugin_kalender_lookup_cats'],
				$this->db->escape($this->checked->psel_cal_id)
			);
			$result_cat=$this->db->get_results($sql,ARRAY_A);
			$this->content->template['pcal_kategorien']=$result_cat;
		}
	}

	/**
	 * class_datums::get_user_gruppen()
	 * DIe Papoo Usergruppen auslesen und zuweisen
	 *
	 * @return void
	 */
	function get_user_gruppen()
	{
		//Die Gruppen raussuchen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$result_gr=$this->db->get_results($sql,ARRAY_A);

		IfNotSetNull($this->checked->pcal_termin);

		//Leserechte
		$sql=sprintf("SELECT * FROM %s
									WHERE date_id_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_read_date'],
			$this->db->escape($this->checked->pcal_termin)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Gruppen durchgehen und Leserechte setzen kalender_gruppe_write_id
		if (is_array($result_gr)) {
			$i=0;
			foreach ($result_gr as $key=>$value)
			{
				if (is_array($result)) {
					foreach ($result as $key2=>$value2) {
						if ($value['gruppeid']==$value2['date_gruppe_lese_id']) {
							$result_gr[$i]['lese_rights']="ok";
						}
					}
				}
				$i++;
			}
		}
		$this->content->template['pcal_gruppen']=$result_gr;
	}

	/**
	 * pkalenderplugin_class::pcal_save_cal_rights()
	 *
	 * @param int $id
	 * @return void
	 */
	function pcal_save_date_rights($id=0)
	{
		//Neu eintragen dann ID setzen
		if ($id>0) {
			$this->checked->pcal_date_id=$id;
			$this->checked->pcal_termin=$id;
		}

		//ZUerst die alten Eintr�ge l�schen
		$sql=sprintf("DELETE  FROM %s
												WHERE date_id_id='%d'",
			$this->cms->tbname['plugin_kalender_lookup_read_date'],
			$this->db->escape($this->checked->pcal_termin)
		);
		$this->db->query($sql);

		//Dann neu eintragen
		if (isset($this->checked->pcal_gruppe_lese) && is_array($this->checked->pcal_gruppe_lese)) {
			foreach ($this->checked->pcal_gruppe_lese as $key=>$value) {
				$sql=sprintf("INSERT INTO %s 
												SET date_id_id='%d',
												date_gruppe_lese_id='%d'",
					$this->cms->tbname['plugin_kalender_lookup_read_date'],
					$this->db->escape($this->checked->pcal_termin),
					$this->db->escape($value)
				);
				$result=$this->db->query($sql);
			}
		}

	}

	/**
	 * class_datums::reload()
	 * Seite neu laden mit den gegebenen Parametern
	 *
	 * @param string $template
	 * @param string $dat
	 * @param string $front
	 * @return void
	 */
	function reload($template = "",$dat="",$front="")
	{
		$url = "menuid=" . $this->checked->menuid;

		if (!empty($template)) {
			$url .= "&template=" . $template;
		}
		else {
			$url .= "&template=pkalender/templates/pkalender_back_date.html";
		}
		if (!empty($dat)) {
			$url .= "&message_pcalender=" . $dat."&psel_cal_id=".$this->checked->psel_cal_id;
		}
		if (!defined("admin")) {
			$url=PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&kal_id=".$this->checked->kal_id."&monats_id=".$this->checked->monats_id."&cal_view=cal&cal_insert=true";
			$location_url =  $url;
		}
		else {
			$self=$_SERVER['PHP_SELF'];
			$location_url = $self . "?" . $url;
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

$class_datums = new class_datums();
