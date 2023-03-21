<?php
/**
 * form_manager Messages Klasse
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2015
 * @version $ID$
 * @access public
 *
 * Checkt ab 2015 das Update
 */
#[AllowDynamicProperties]
class form_messages {
	/**
	 * form_messages constructor.
	 */
	public function __construct() {

		global $db, $checked, $diverse, $content, $user, $cms, $weiter, $db_abs;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->content = &$content;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		//Check Tabellen
		$this->check_messages();
	}

	public function check_messages()
	{
		$this->content->template['self_form_messages']="plugin.php?menuid=".$this->checked->menuid."&template=form_manager/templates/messages.html";

		if (!empty($this->checked->form_cat_id)) {
			#$this->create_form_cat();
		}
		else {
			$this->check_leads_entry();
		}
	}

	/**
	 * form_manager::check_leads_entry()
	 * Hier werden die Leads gecheckt die generiert wurden
	 *
	 * @return void
	 */
	function check_leads_entry()
	{
		$this->content->template['form_manager_id'] = isset($this->checked->form_manager_id) ? $this->checked->form_manager_id : NULL;

		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		$this->content->template['fl_link'] = $link;
		$this->content->template['menuid_aktuell']=$this->checked->menuid;


		if (isset($this->checked->lead_id) && is_numeric($this->checked->lead_id)) {
			$this->make_single_lead();

			$this->content->template['form_single'] = "ok";
		}
		else {
			//Liste der LEads rausholen
			$this->get_lead_list();

			#$this->content->template['form'] = "ok";
			$this->content->template['liste'] = "ok";
		}

		/**
		// Wenn form_managerid gesetzt,
		if (is_numeric($this->checked->form_manager_id)) {

		if (is_numeric($this->checked->lead_id))
		{
		$this->make_single_lead();

		$this->content->template['form_single'] = "ok";
		}
		else
		{
		//Liste der LEads rausholen
		$this->get_lead_list();

		$this->content->template['form'] = "ok";
		}

		} else {
		// Liste der Formulare anbieten
		#$this->get_form_list();
		$this->content->template['liste'] = "ok";
		}
		 *
		 * */
	}

	/**
	 * @param string $filename
	 * @param string $filename2
	 */
	function form_show_file($filename="", $filename2="")
	{

		//Größe ermitteln
		$filename="/".$filename;
		if (file_exists(PAPOO_ABS_PFAD .$filename)) {
			$size = filesize(PAPOO_ABS_PFAD . $filename);
		}
		// Header zuweisen
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Accept-Ranges: bytes");
		//header("Content-Type: application/force-download");
		//header("Content-Type: application/download");
		// Wenn Opera oder MSIE auftauchen
		//if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT')) or preg_match('#MSIE ([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT'))) {
		//	header("Content-Type: application/octetstream");
		//} else {
		//	header("Content-Type: application/octet-stream");
		//}

		// Mime-Type der Datei festestellen und Content-Type festlegen
		$fragmente = explode(".", $filename2);
		// FIXME: Manchmal nicht zählbar
		$endung = $fragmente[count($fragmente) - 1];

		switch ($endung) {
		case "pdf" :
			header("Content-Type: application/pdf");
			break;
		case "zip" :
			header("Content-Type: application/zip");
			break;
		case "doc" :
			header("Content-Type: application/msword");
			break;
		case "txt" :
			header("Content-Type: text/plain");
			break;
		default :
			header("Content-Type: application/octet-stream");
		}

		IfNotSetNull($size);
		header("Content-Length: $size ");
		// header("Pragma: no-cache");
		/*
		$browser = $_ENV['HTTP_USER_AGENT'];
		if (preg_match('/MSIE 5.5/', $browser) || preg_match('/MSIE 6.0/', $browser)) {
		header('Content-Disposition:  filename="'.$this->cms->webverzeichnis.$filename.'"');
		if (preg_match('/pdf/', $filename)) {
		header('Content-Disposition: attachment; filename="'.$this->cms->webverzeichnis.$filename.'"');
		}
		} else {
		header('Content-Disposition: attachment; filename="'.$this->cms->webverzeichnis.$filename.'"');
		}
		*/
		header('Content-Disposition: attachment; filename="' . utf8_decode($filename2) .'.'. $endung .'"');

		// Send Content-Transfer-Encoding HTTP header
		// (use binary to prevent files from being encoded/messed up during transfer)
		header('Content-Transfer-Encoding: binary');

		// File auslesen
		@readfile(PAPOO_ABS_PFAD . $filename);

		// Wichtig, sonst gibt es Chaos!
		//exit;
	}


	/**
	 * form_manager::make_single_lead()
	 * Einen Lead in Details darstellen
	 *
	 * @return void
	 */
	function make_single_lead()
	{

		if (!empty($this->checked->show_file)) {
			$filename = "dokumente/files/".basename($this->checked->show_file);
			$this->form_show_file($filename,basename($this->checked->show_file));
		}

		if (!empty($this->checked->submit_one_field)) {
			//Entweder auf nicht löschbar setzen
			if (!empty($this->checked->loeschbar)) {
				$sql=sprintf("UPDATE %s SET form_manager_form_loesch_yn='1'
												WHERE form_manager_lead_id='%d'",
					$this->cms->tbname['papoo_form_manager_leads'],
					$this->db->escape($this->checked->lead_id)
				);
				$this->db->query($sql);
			}
			else {
				$sql=sprintf("UPDATE %s SET form_manager_form_loesch_yn='0'
												WHERE form_manager_lead_id='%d'",
					$this->cms->tbname['papoo_form_manager_leads'],
					$this->db->escape($this->checked->lead_id)
				);
				$this->db->query($sql);
			}

			//Oder löschen wenn löschbar
			if (!empty($this->checked->loesch)) {

				$sql=sprintf("SELECT form_manager_lead_id FROM %s
												WHERE form_manager_lead_id='%d'
												AND form_manager_form_loesch_yn!='1' ",
					$this->cms->tbname['papoo_form_manager_leads'],
					$this->db->escape($this->checked->lead_id)
				);
				$id=$this->db->get_var($sql);
				if ($id>1) {
					$sql=sprintf("DELETE FROM %s WHERE
												form_manager_lead_id='%d' ",
						$this->cms->tbname['papoo_form_manager_leads'],
						$this->db->escape($id)
					);
					$this->db->query($sql);
					$sql=sprintf("DELETE FROM %s WHERE
												form_manager_content_lead_id_id='%d' ",
						$this->cms->tbname['papoo_form_manager_lead_content'],
						$this->db->escape($id)
					);
					$this->db->query($sql);
				}
				$this->checked->lead_id=0;
			}
		}

		//Einträge rausholen
		$sql=sprintf("  SELECT * FROM %s
                        LEFT JOIN %s ON plugin_cform_name=form_manager_content_lead_feld_name
                        LEFT JOIN %s ON plugin_cform_id=plugin_cform_lang_id
						WHERE 	form_manager_content_lead_id_id='%d'
						GROUP BY form_manager_content_lead_feld_name
						ORDER BY plugin_cform_order_id DESC",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$this->cms->tbname['papoo_plugin_cform'],
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$this->db->escape($this->checked->lead_id)
		);
		$result2=$this->db->get_results($sql,ARRAY_A);

		/**
		if (is_array($result2))
		{
		foreach ($result2 as $res)
		{
		if (is_array($felderdrin))
		{
		foreach ($felderdrin as $drin)
		{
		if ($res['form_manager_content_lead_feld_name']==$drin['form_manager_content_lead_feld_name'])
		{
		$neu[]=$res;
		}
		}
		}
		$felder[$res['form_manager_content_lead_feld_name']]=($res['form_manager_content_lead_feld_name']);
		}
		}
		$result2=$neu;
		 * */

		$sql=sprintf("SELECT * FROM %s
									WHERE 	form_manager_lead_id='%d' ",
			$this->cms->tbname['papoo_form_manager_leads'],
			$this->db->escape($this->checked->lead_id)
		);
		$result3=$this->db->get_results($sql,ARRAY_A);

		$count=count($result2);

		if (is_array($result3[0])) {
			foreach ($result3[0] as $key=>$value) {
				$result2[$count][$key]=$value;
				$count++;
			}
		}

		/**
		if (is_array($result3[0]))
		{
		foreach ($result3[0] as $key=>$value)
		{
		$result2[$count]['form_manager_content_lead_feld_name']=str_ireplace("flex_","",$key);
		$result2[$count]['form_manager_content_lead_feld_content']=$value;
		}
		}**/

		if (is_array($result2)) {
			foreach ($result2 as $k=>$dat) {
				//plugin_cform_type
				if (isset($dat['plugin_cform_type']) && $dat['plugin_cform_type'] == "file") {
					$result2[$k]['form_manager_content_lead_feld_content'] =
						"<a href=\"./plugin.php?menuid=".$this->checked->menuid .
						"&template=form_manager/templates/messages.html&form_manager_id=" . $this->checked->form_manager_id .
						"&lead_id=" . $this->checked->lead_id .
						"&show_file=" . $result2[$k]['form_manager_content_lead_feld_content'] .
						"\">" . $result2[$k]['form_manager_content_lead_feld_content'] .
						"</a>";
				}
			}
		}

		$i=1;
		if (empty($result2)) {
			$result2[0]['form_manager_content_lead_id']="yx";
		}
		if (is_array($result2)) {
			foreach ($result2 as $key => $dat) {
				if (isset($dat['form_manager_form_loesch_datum1']) && $dat['form_manager_form_loesch_datum1']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_loesch_datum1']);
					$result2[$key]['form_manager_content_lead_feld_content'] = date("d.m.Y - H:i", $dat['form_manager_form_loesch_datum1']);
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_loesch_datum1'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_loesch_datum1";
				}
				if (isset($dat['form_manager_form_loesch_datum2']) && $dat['form_manager_form_loesch_datum2']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_loesch_datum2']);
					$result2[$key]['form_manager_content_lead_feld_content'] = date("d.m.Y - H:i", $dat['form_manager_form_loesch_datum2']);
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_loesch_datum2'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_loesch_datum2";
				}
				if (isset($dat['form_manager_form_datum']) && $dat['form_manager_form_datum']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_datum']);
					$result2[$key]['form_manager_content_lead_feld_content'] = date("d.m.Y - H:i", $dat['form_manager_form_datum']);
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_datum'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_datum";
				}
				//Löschen
				if (isset($dat['form_manager_form_loesch_yn']) && $dat['form_manager_form_loesch_yn']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_loesch_yn']);
					$result2[$key]['form_manager_content_lead_feld_content']=$dat['form_manager_form_loesch_yn'];
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_loesch_yn'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_loesch_yn";
				}
				//FormularID
				if (isset($dat['form_manager_form_id']) && $dat['form_manager_form_id']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_id']);
					$result2[$key]['form_manager_content_lead_feld_content']=$this->get_form_name($dat['form_manager_form_id']);
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_id'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_id";
				}
				//IP Adresse
				if (isset($dat['form_manager_form_ip_sender']) && $dat['form_manager_form_ip_sender']) {
					IfNotSetNull($this->content->template['plugin']['form_manager']['form_manager_form_ip_sender']);
					$result2[$key]['form_manager_content_lead_feld_content']=$dat['form_manager_form_ip_sender'];
					$result2[$key]['plugin_cform_label'] = $this->content->template['plugin']['form_manager']['form_manager_form_ip_sender'];
					$result2[$key]['form_manager_content_lead_feld_name']="form_manager_form_ip_sender";
				}

				IfNotSetNull($result2[$key]['form_manager_content_lead_feld_name']);
				$result2[$key]['form_manager_content_lead_feld_name'] = str_ireplace("flex_","",$result2[$key]['form_manager_content_lead_feld_name']);
				$result2[$key]['form_manager_content_lead_feld_name'] = str_ireplace("flex_","",$result2[$key]['form_manager_content_lead_feld_name']);

				$i++;
			}
		}
		else {
			$this->content->template['del_single_lead'] = "no";
		}
		//Sortieren bzw. statistische Daten extra ausweisen
		if(is_array($result2)) {
			$stat=array();
			$form_dat_array=array();
			foreach ($result2 as $k=>$v) {
				if (stristr($v['form_manager_content_lead_feld_name'],"form_")
					|| stristr($v['form_manager_content_lead_feld_name'],"referr")
					|| stristr($v['form_manager_content_lead_feld_name'],"gclid")
					|| stristr($v['form_manager_content_lead_feld_name'],"nicht")
					|| stristr($v['form_manager_content_lead_feld_name'],"last_page")
					|| stristr($v['form_manager_content_lead_feld_name'],"last_page")
					|| stristr($v['form_manager_content_lead_feld_name'],"spam")) {
					$stat[$k]=$v;
				}
				else {
					$form_dat_array[$k]=$v;
				}
			}
		}

		$this->content->template['single_lead'] = isset($form_dat_array) ? $form_dat_array : NULL;
		$this->content->template['single_lead_stat'] = isset($stat) ? $stat : NULL;
	}

	/**
	 * Alle Leads rausholen
	 *
	 * @return array|bool|null
	 */
	public function get_lead_list_new()
	{
		return true;
		//Sortierung
		if (!empty($this->checked->order_by)) {
			if ($this->checked->order_by=="bezeichnung") {
				$order_by=" ORDER BY form_manager_name";
			}

			if ($this->checked->order_by=="id") {
				$order_by=" ORDER BY form_manager_lead_id";
			}

			if ($this->checked->order_by=="plugin_form_manager_erstellt_am") {
				$order_by=" ORDER BY form_manager_erstellt";
			}

			if ($this->checked->order_by=="plugin_form_manager_letzte_nderung") {
				$order_by=" ORDER BY form_manager_geaendert";
			}
		}

		//Richung ini wenn nix gesetzt ist
		$this->content->template['sort_order']="desc";

		//Richtung auswerten
		if (!empty($this->checked->sort_order)) {
			if ($this->checked->sort_order=="asc") {
				$sort_by="ASC";

				//Fürs Template dann umdrehen
				$this->content->template['sort_order']="desc";
			}
			if ($this->checked->sort_order=="desc") {
				$sort_by="DESC";

				//Fürs Template dann umdrehen
				$this->content->template['sort_order']="asc";
			}
		}

		if (empty($this->checked->form_search_now) || (empty($this->checked->form_manager_kategorie_form) && empty($this->checked->form_manager_name_form) )) {
			//zuerst mal zählen
			$sql = sprintf("SELECT COUNT(form_manager_lead_id) FROM %s",
				$this->cms->tbname['papoo_form_manager_leads']
			);
			$count=$this->db->get_var($sql);

			//Dann die richtigen mit Limit rausholen
			$sql = sprintf("SELECT * FROM %s
                                LEFT JOIN %s ON form_manager_lead_id = form_manager_content_lead_id_id

								 %s %s

								",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$order_by,
				$sort_by
			);
		}
		else {
			$search_kat = "";

			//Nur setzen wenn auch eine Kat gewählt - sonst wird nix gefunden...
			if ($this->checked->form_manager_kategorie_form >= 1) {
				$search_kat = "form_manager_kategorie = '".$this->db->escape($this->checked->form_manager_kategorie_form)."' AND ";
			}

			$sql = sprintf("SELECT * FROM %s
                                LEFT JOIN %s ON form_manager_lead_id = form_manager_content_lead_id_id
								WHERE
								%s

								 %s %s
								",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$search_kat,
				$order_by,
				$sort_by
			);
		}
		$result = $this->db->get_results($sql, ARRAY_A);
		//Übersicht setzen
		$this->content->template['uebersicht']="OK";
		$this->content->template['form_manager_kategorie_form']=$this->checked->form_manager_kategorie_form;
		$this->content->template['form_manager_name_form']=$this->checked->form_manager_name_form;
		$this->content->template['form_search_now']=$this->checked->form_search_now;

		//Noch weitere Daten ergänzen
		if (!is_array($result)) {
			return false;
		}
		/**
		foreach ($result as $k=>$v) {
		//Anzahl der Felder
		$result[$k]['anzahl_felder']=$this->get_anzahl_felder($v['form_manager_id']);

		//Anzahl der Anfragen
		$result[$k]['anzahl_anfragen']=$this->get_anzahl_anfragen($v['form_manager_id']);

		//Letzte Anfrage
		$result[$k]['letzte_anfrage']=$this->get_last_anfrage($v['form_manager_id']);

		//ID der letzten Anfrage
		$result[$k]['letzte_anfrage_id']=$this->max_lead_id;

		//Erstellt
		if ($result[$k]['form_manager_erstellt']>1) {
		$result[$k]['form_manager_erstellt'] = date("d.m.Y - H:i",$result[$k]['form_manager_erstellt']);
		}
		else {
		$result[$k]['form_manager_erstellt'] = "N.A.";
		}

		//Geändert
		if ($result[$k]['form_manager_geaendert']>1) {
		$result[$k]['form_manager_geaendert'] = date("d.m.Y - H:i",$result[$k]['form_manager_geaendert']);
		}
		else {
		$result[$k]['form_manager_geaendert'] = "N.A.";
		}
		}
		 */
		return $result;
	}


	/**
	 * Holt den Formularnamen raus
	 *
	 * @param int $form_id
	 * @return mixed
	 */
	function get_form_name($form_id=0)
	{
		$xsql['dbname'] = "papoo_form_manager";
		$xsql['select_felder'] = array("form_manager_name");
		#$xsql['where_data_like']= " LIKE ";
		$xsql['where_data'] = array("form_manager_id" => $form_id);

		$result = $this->db_abs->select( $xsql );

		return $result['0']['form_manager_name'];
	}

	/**
	 * @param int $form_cat_id
	 * @return mixed
	 */
	function get_form_cat_name($form_cat_id=0)
	{
		$xsql['dbname'] = "papoo_form_manager";
		$xsql['select_felder'] = array("form_manager_kategorie");
		$xsql['where_data_like']= " LIKE ";
		$xsql['where_data'] = array("form_manager_id" => $form_cat_id);

		$result = $this->db_abs->select( $xsql );

		$cat_id=$result['0']['form_manager_kategorie'];

		$xsql['dbname']         = "papoo_plugin_cform_cats";
		$xsql['select_felder']  = array("formcat__name_der_kategorie");
		#$xsql['where_data_like']= " LIKE ";
		$xsql['where_data']     = array("formcat__id" => $cat_id);

		$result = $this->db_abs->select( $xsql );

		IfNotSetNull($result['0']['formcat__name_der_kategorie']);
		return $result['0']['formcat__name_der_kategorie'];
	}


	/**
	 * form_manager::get_lead_list()
	 *
	 * Liste der Leads rausholen zur Anzeige
	 *
	 * @return void
	 */
	function get_lead_list()
	{
		if (!empty($this->checked->gutschein_nachrichten_lschen_von)) {
			$this->delete_leads();
		}

		if (!empty($this->checked->submit_all_fields)) {
			/** @var int[] $leadIds */
			$leadIds = array_map(function ($id) { return (int)$id; }, array_keys($this->checked->daten ?: []));

			//Alle Einträge auf nicht löschbar setzen
			if (count($leadIds) > 0) {
				$this->db->query(
					"UPDATE {$this->cms->tbname['papoo_form_manager_leads']} ".
					"SET form_manager_form_loesch_yn = '0' ".
					"WHERE form_manager_lead_id IN (".implode(', ', $leadIds).")"
				);
			}

			if (is_array($this->checked->loeschbar)) {
				foreach ($this->checked->loeschbar as $key=>$value) {
					$sql=sprintf("UPDATE %s SET form_manager_form_loesch_yn='1'
												WHERE form_manager_lead_id='%d'",
						$this->cms->tbname['papoo_form_manager_leads'],
						$this->db->escape($key)
					);
					$this->db->query($sql);
				}
			}
			if (isset($this->checked->loesch) && is_array($this->checked->loesch)) {
				foreach ($this->checked->loesch as $key=>$value) {
					$sql=sprintf("SELECT form_manager_lead_id FROM %s
												WHERE form_manager_lead_id='%d'
												AND form_manager_form_loesch_yn!='1' ",
						$this->cms->tbname['papoo_form_manager_leads'],
						$this->db->escape($key)
					);
					$id=$this->db->get_var($sql);
					if ($id>1) {
						$sql=sprintf("DELETE FROM %s WHERE
												form_manager_lead_id='%d' ",
							$this->cms->tbname['papoo_form_manager_leads'],
							$this->db->escape($id)
						);
						$this->db->query($sql);

						$sql=sprintf("DELETE FROM %s WHERE
												form_manager_content_lead_id_id='%d' ",
							$this->cms->tbname['papoo_form_manager_lead_content'],
							$this->db->escape($id)
						);
						$this->db->query($sql);

						$this->content->template['del_single_lead']="ok";
					}
				}
			}
		}

		$this->set_lead_name();

		//Löschdat 1 rausholen
		$sql=sprintf("SELECT userid FROM %s WHERE userid='%d' AND gruppenid='1'",
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$resultuser=$this->db->get_var($sql);
		if ($resultuser>1) {
			$varloesch=0;
		}
		else {
			$varloesch=time();
		}

		if (isset($this->checked->export) && $this->checked->export != "now") {
			//Limit setzen für $this->weiter->sqllimit
			$this->weiter->make_limit(30);
		}
		else {
			//Limit setzen für $this->weiter->sqllimit
			$this->weiter->make_limit(99999999);
		}

		//Sortierung
		if (!empty($this->checked->order_by)) {
			if ($this->checked->order_by=="bezeichnung") {
				$order_by=" ORDER BY form_manager_name";
			}

			if ($this->checked->order_by=="id") {
				$order_by=" ORDER BY form_manager_lead_id";
			}

			if ($this->checked->order_by=="form_manager_form_datum") {
				$order_by=" ORDER BY form_manager_form_datum";
			}

			if ($this->checked->order_by=="mesagekategorie") {
				$order_by=" ORDER BY form_manager_kategorie";
			}
		}
		else {
			$order_by=" ORDER BY form_manager_lead_id";
		}

		//Richtung auswerten
		if (!empty($this->checked->sort_order)) {
			if ($this->checked->sort_order=="asc") {
				$sort_by = "ASC";

				//Fürs Template dann umdrehen
				$this->content->template['sort_orderm']="desc";
			}
			else {
				$sort_by = "";
			}
			if ($this->checked->sort_order=="desc") {
				$sort_by = "DESC";

				//Fürs Template dann umdrehen
				$this->content->template['sort_orderm']="asc";
			}
			else {
				$sort_by = "";
			}
		}
		else {
			$sort_by = "DESC";

			//Fürs Template dann umdrehen
			$this->content->template['sort_orderm']="asc";
		}

		$this->content->template['form_manager_formsuche'] =
			isset($this->checked->form_manager_formsuche) ? $this->checked->form_manager_formsuche : NULL;

		//Die Formmanger ID kommt über die url!! dann entsprechend setzen damit die Suche auch klappen kann
		if (isset($this->checked->form_manager_id) && is_numeric($this->checked->form_manager_id) && empty($this->checked->form_search_now)) {
			//BUtton auf suchen
			$this->checked->form_search_now="search";

			//ID übergeben
			$this->checked->form_manager_formsuche=$this->checked->form_manager_id;
			$this->content->template['form_manager_formsuche']=$this->checked->form_manager_id;
		}

		//form_manager_formsuche
		if (empty($this->checked->form_search_now)
			||
			(      empty($this->checked->form_manager_kategorie_form_message)
				&&  empty($this->checked->form_manager_name_form)
				&&  empty($this->checked->form_manager_formsuche)
				&&  empty($this->checked->form_manager_name_form_messages)
			)) {

			//Anzahl der Leads
			$sql = sprintf("SELECT COUNT(form_manager_lead_id) FROM %s
                                        WHERE
                                        form_manager_form_loesch_datum1 > '%d'
                                        ",
				$this->cms->tbname['papoo_form_manager_leads'],
				$varloesch
			);

			// zählen wieviele existieren
			$this->weiter->result_anzahl = $this->db->get_var($sql);

			$sql = sprintf("SELECT * FROM %s
                                     LEFT JOIN %s ON form_manager_id=form_manager_form_id
                                    WHERE
                                      form_manager_form_loesch_datum1>'%d'
                                    %s %s %s ",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['papoo_form_manager'],
				$varloesch,
				$order_by,
				$sort_by,
				$this->weiter->sqllimit
			);
			$result1 = $this->db->get_results($sql, ARRAY_A);
		}
		// Hier dann die suche ohne Einschränkungen
		else {
			//Nur die der Formular
			if (is_numeric($this->checked->form_manager_formsuche) && $this->checked->form_manager_formsuche > 0) {
				$form_id_search = "form_manager_form_id='" . $this->db->escape($this->checked->form_manager_formsuche) . " ' AND  ";
				$this->content->template['form_manager_formsuche']=$this->checked->form_manager_formsuche;
			}

			if (isset($this->checked->form_manager_kategorie_form_message) && is_numeric($this->checked->form_manager_kategorie_form_message) &&
				$this->checked->form_manager_kategorie_form_message > 0) {
				$form_id_search2 = "form_manager_kategorie='" . $this->db->escape($this->checked->form_manager_kategorie_form_message) . " ' AND  ";
				$this->content->template['form_manager_kategorie_form_message']=$this->checked->form_manager_kategorie_form_message;
			}

			if (!empty($this->checked->form_manager_name_form_messages)) {
				$subsearch=" form_manager_lead_id IN (". sprintf("      SELECT  form_manager_content_lead_id_id
                                            FROM %s
                                          WHERE form_manager_content_lead_feld_content LIKE '%s' ",
						DB_PRAEFIX.'papoo_form_manager_lead_content',
						"%". $this->db->escape($this->checked->form_manager_name_form_messages)."%"
					).") AND ";
				$this->content->template['form_manager_name_form_messages']=$this->checked->form_manager_name_form_messages;
			}

			IfNotSetNull($form_id_search);
			IfNotSetNull($form_id_search2);
			IfNotSetNull($subsearch);

			//Anzahl der Leads
			$sql = sprintf("SELECT COUNT(form_manager_lead_id) FROM %s
                                        LEFT JOIN %s ON form_manager_id=form_manager_form_id
                                        WHERE
                                        %s %s %s
                                        form_manager_form_loesch_datum1 > '%d'",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['papoo_form_manager'],
				$form_id_search,
				$form_id_search2,
				$subsearch,
				$varloesch
			);
			// zählen wieviele Artikel existieren
			$this->weiter->result_anzahl = $this->db->get_var($sql);

			$sql = sprintf("SELECT * FROM %s
                                     LEFT JOIN %s ON form_manager_id=form_manager_form_id
                                      WHERE
                                      %s %s %s
                                      form_manager_form_loesch_datum1>'%d'
                                       %s %s	%s ",
				$this->cms->tbname['papoo_form_manager_leads'],
				$this->cms->tbname['papoo_form_manager'],
				$form_id_search,
				$form_id_search2,
				$subsearch,
				$varloesch,
				$order_by,
				$sort_by,
				$this->weiter->sqllimit
			);
			$result1 = $this->db->get_results($sql, ARRAY_A);
		}
		//Liste der Felder einlesen

		$zahl=" form_manager_content_lead_id_id='xy' ";

		if(is_array($result1)) {
			foreach ($result1 as $dat) {
				$zahl.=" OR form_manager_content_lead_id_id='".$dat['form_manager_lead_id']."'";
			}
		}

		//Einträge rausholen
		$sql=sprintf("SELECT * FROM %s
									WHERE %s ORDER BY form_manager_content_lead_id DESC",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$zahl
		);
		$result2=$this->db->get_results($sql,ARRAY_A);
		/**

		$sql=sprintf("SELECT * FROM %s
		WHERE %s GROUP BY form_manager_content_lead_feld_name ORDER BY form_manager_content_lead_id DESC",
		$this->cms->tbname['papoo_form_manager_lead_content'],
		$zahl
		);
		$result3=$this->db->get_results($sql,ARRAY_A);
		foreach ($result3 as $dat3)
		{
		$felder[]=$dat3['form_manager_content_lead_feld_name'];
		}
		 */

		ini_set("memory_limit",-1);

		$i=0;
		#if ($this->checked->export=="now")
		{
			if(is_array($result1)) {
				foreach ($result1 as $dkey=>$dat) {
					$result1[$i]['form_manager_form_datum']=date("d.m.Y - H:i", $result1[$i]['form_manager_form_datum']);
					// FIXME: War vorher dat2
					$csv[$dat['form_manager_form_datum']]=$result1[$i]['form_manager_form_datum'];
					$neu_csv[$dkey]['form_manager_form_datum']=$result1[$i]['form_manager_form_datum'];
					foreach ($result2 as $dat2) {
						if ($dat['form_manager_lead_id']==$dat2['form_manager_content_lead_id_id']) {
							//Zeilenumbrüche entfernen
							$dat2['form_manager_content_lead_feld_content']=str_replace("\n"," ",$dat2['form_manager_content_lead_feld_content']);
							$dat2['form_manager_content_lead_feld_content']=str_replace("\r"," ",$dat2['form_manager_content_lead_feld_content']);

							$result1[$i][$dat2['form_manager_content_lead_feld_name']]=$dat2['form_manager_content_lead_feld_content'];
							$csv[$dat2['form_manager_content_lead_feld_name']]=$dat2['form_manager_content_lead_feld_content'];
							$neu_csv[$dkey][$dat2['form_manager_content_lead_feld_name']]=$dat2['form_manager_content_lead_feld_content'];
						}
					}
					$i++;
				}
			}
		}
		IfNotSetNull($result1[0]);
		$felder=$result1[0];
		//Wenn Export
		if (isset($this->checked->export) && $this->checked->export=="now") {
			$csv1 = "";
			$csv2 = "";
			/**
			foreach ($result2 as $datx)
			{
			//$lead[$datx['form_manager_content_lead_id_id']][]=$datx;
			$csv[$datx['form_manager_content_lead_feld_name']]=$dat2['form_manager_content_lead_feld_content'];
			}
			 */

			$csv_keys=(array_keys($neu_csv['0']));
			#$csv_keys=array_reverse($csv_keys);
			//CSV erzeugen 1. Zeile
			foreach ($csv_keys as $key=>$value) {
				$csv1.=$value."\t";
			}
			$csv1.="\n";
			//Dann die Einträge
			#$result1[$i]['form_manager_form_datum']=date(d.".".m.".".Y." - ".H.":".i,$result1[$i]['form_manager_form_datum']);
			#$csv[$dat2['form_manager_form_datum']]=$result1[$i]['form_manager_form_datum'];
			/**
			foreach ($result2 as $key=>$dat2)
			{
			$dat2['form_manager_content_lead_feld_content']=str_replace("\n","",$dat2['form_manager_content_lead_feld_content']);
			$dat2['form_manager_content_lead_feld_content']=str_replace("\r","",$dat2['form_manager_content_lead_feld_content']);
			$csv_result2[$dat2['form_manager_content_lead_id_id']][$dat2['form_manager_content_lead_feld_name']]=$dat2['form_manager_content_lead_feld_content'];
			unset($result2[$key]);
			}
			 */
			foreach ($neu_csv as $dat2) {
				foreach ($csv_keys as $key=>$value) {
					$csv2.=$dat2[$value]."\t";
				}
				$csv2.="\n";
			}
			$csv=$csv1.$csv2;

			$size = mb_strlen($csv);
			$filename2=$this->content->template['lead_eintrag_name'];

			/**
			 * // Header zuweisen
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Accept-Ranges: bytes");
			header("Content-Type: text/plain");
			header("Content-Length: $size ");
			header('Content-Disposition: attachment; filename="' . utf8_decode($filename2) .'-csv.csv"');

			// Send Content-Transfer-Encoding HTTP header
			// (use binary to prevent files from being encoded/messed up during transfer)
			header('Content-Transfer-Encoding: binary');

			// File auslesen
			echo(($csv));
			 */

			header("Content-Type: text/x-csv");
			header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Content-Disposition: attachment; filename=download.csv");
			header("Pragma: no-cache");
			echo $csv;

			// Wichtig, sonst gibt es Chaos!
			exit;
		}

		//Alle Leads
		$this->content->template['lead_list']=$result1;
		//Alle Feldernamen
		$this->content->template['lead_felder_liste']=$felder;

		/**
		//Die zugeordnetetn Felder
		$sql=sprintf("SELECT form_manager_content_lead_feld_name FROM %s WHERE
		form_manager_content_form_id='%d' AND form_manager_content_form_id_single='0'",
		$this->cms->tbname['papoo_form_manager_lead_show_felder'],
		$this->db->escape($this->checked->form_manager_id)
		);
		$felderdrin=$this->db->get_results($sql,ARRAY_A);
		 *

		#print_r($felderdrin);
		if (is_array($felderdrin))
		{
		foreach ($felderdrin as $fd)
		{
		#echo $fd['form_manager_content_lead_feld_name'];
		if (is_array($felder))
		{
		if (array_key_exists($fd['form_manager_content_lead_feld_name'],$felder))
		{
		$neu[]['form_manager_content_lead_feld_name']=$fd['form_manager_content_lead_feld_name'];
		}
		}
		}
		}
		 *
		 * * */
		//Einträge individuell durchgehen
		if (is_array($result1)) {
			foreach ($result1 as $k=>$v) {
				$neu[$k] = [];
				if(is_array($v)) {
					foreach ($v as $k1=>$v1) {
						//get_form_name
						if ($k1=="form_manager_form_id") {
							//Name des Formulars
							$result1[$k]["form_name"]=$this->get_form_name($v1);

							//UNd die Kategorie get_form_cat_name
							$result1[$k]["formcat__name_der_kategorie"]=$this->get_form_cat_name($v1);
						}

						//Die Standadarddaten aus der Übersicht raushalten
						if (stristr($k1,"form_") ||
							stristr($k1,"referr") ||
							stristr($k1,"glcid") ||
							stristr($k1,"nicht") ||
							stristr($k1,"last_page") ||
							stristr($k1,"spam")
						) {
							continue;
						}

						//Wenn vorname, name, mail oder tel oder firma dann Übergeben.  || stristr($k1,"tel") || stristr($k1,"firma")
						if (stristr($k1,"vorname") ||
							stristr($k1,"name") ||
							stristr($k1,"mail")
						) {
							if (!empty($v1)) {
								if (strlen($v1)>160) {
									$v1=substr($v1,0,160)."...";
								}
								$neu[$k][$k1]=$v1;
							}
						}
					}

					if (count($neu[$k])<5) {
						foreach ($v as $k1=>$v1) {
							if (count($neu[$k])>=5) {
								continue;
							}

							//Die Standadarddaten aus der Übersicht raushalten
							if (stristr($k1,"form_") ||
								stristr($k1,"referr") ||
								stristr($k1,"glcid") ||
								stristr($k1,"nicht") ||
								stristr($k1,"last_page")||
								stristr($k1,"spam")
							) {
								continue;
							}

							//Wenn vorname, name, mail oder tel oder firma dann Übergeben.
							if (!stristr($k1,"vorname") &&
								!stristr($k1,"name") &&
								!stristr($k1,"mail") &&
								!stristr($k1,"tel") &&
								!stristr($k1,"firma")
							) {
								if (!empty($v1)) {
									if (strlen($v1)>160) {
										$v1=substr($v1,0,160)."...";
									}
									$neu[$k][$k1]=$v1;
								}
							}
						}
					}
					if (count($neu[$k])<3) {
						$count=count($neu[$k]);
						for ($i=$count;$i<3;$i++) {
							$neu[$k][$i]="nix";
						}
					}
					if (count($neu[$k])>3) {
						$neu[$k]=array_slice($neu[$k],0,3);
					}
				}
			}
		}
		//Das neue Array nochmal durchlaufen und counten ob auch 5 Felder drin sind... wenn nicht restliche auffüllen

		IfNotSetNull($neu);
		$this->content->template['lead_felder_selected'] = $neu;
		$this->content->template['lead_liste'] = $result1;

		IfNotSetNull($this->checked->menuid);
		IfNotSetNull($this->checked->template);
		IfNotSetNull($this->checked->form_manager_id);
		IfNotSetNull($this->checked->form_manager_name_form_messages);
		IfNotSetNull($this->checked->form_manager_formsuche);
		IfNotSetNull($this->checked->form_manager_kategorie_form_message);
		IfNotSetNull($this->checked->form_manager_formsuche);
		IfNotSetNull($this->checked->order_by);
		IfNotSetNull($this->checked->sort_order);

		$this->weiter->weiter_link =
			"plugin.php?menuid=" . $this->checked->menuid .
			"&template=" . $this->checked->template .
			"&form_manager_id=" . $this->checked->form_manager_id .
			"&form_manager_name_form_messages=" . $this->checked->form_manager_name_form_messages .
			"&form_search_now=Anzeigen" .
			"&form_manager_formsuche=" . $this->checked->form_manager_formsuche .
			"&form_manager_kategorie_form_message=" . $this->checked->form_manager_kategorie_form_message .
			"&form_manager_formsuche=" . $this->checked->form_manager_formsuche .
			"&order_by=" . $this->checked->order_by .
			"&sort_order=" . $this->checked->sort_order;
		//form_manager_name_form_messages=test&form_manager_formsuche=0&form_manager_kategorie_form_message=0&form_search_now=Anzeigen
		$this->content->template['lead_liste_link']=$this->weiter->weiter_link;

		// Links zu weiteren Seiten anzeigen
		$this->weiter->do_weiter("teaser");


	}

	/**
	 * form_manager::delete_leads()
	 * Leads anhand von Datum löschen
	 *
	 * @return void
	 */
	private function delete_leads()
	{
		if (!empty($this->checked->gutschein_nachrichten_lschen_von) && !empty($this->checked->gutschein_bis)) {
			# $start_time=date("Y.m.d",strtotime ($this->checked->gutschein_nachrichten_lschen_von));

			$start_time = strtotime ($this->checked->gutschein_nachrichten_lschen_von);
			$stop_time  = strtotime ($this->checked->gutschein_bis);

			if (isset($this->checked->form_manager_id) && is_numeric($this->checked->form_manager_id) && $this->checked->form_manager_id > 0) {
				$sel_form=" AND form_manager_form_id='".$this->db->escape($this->checked->form_manager_id)."' '";
			}

			IfNotSetNull($sel_form);

			$sql=sprintf("SELECT * FROM %s
							WHERE form_manager_form_datum <= '%d'
							AND form_manager_form_datum >= '%d'
							% ",
				$this->cms->tbname['papoo_form_manager_leads'],
				$stop_time,
				$start_time,
				$sel_form
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			//Eintrag wirklich löschen
			$sql = sprintf("DELETE FROM %s
							WHERE form_manager_form_datum <= '%d'
							AND form_manager_form_datum >= '%d'
							%s ",
				$this->cms->tbname['papoo_form_manager_leads'],
				$stop_time,
				$start_time,
				$sel_form
			);
			//Löschen durchführen
			$this->db->query($sql);

			//FIXME: temp_dat1 existiert nicht
			if (isset($temp_dat1) && is_array($temp_dat1)) {
				foreach ($result as $key=>$value) {
					$sql = sprintf("DELETE FROM %s
							WHERE form_manager_content_lead_id_id = '%d'",
						$this->cms->tbname['papoo_form_manager_lead_content'],
						$this->db->escape($value->form_manager_lead_id)
					);
					//Löschen durchführen
					$this->db->query($sql);
				}
			}
		}
	}

	function set_lead_name()
	{
		IfNotSetNull($this->checked->form_manager_id);
		//lead_eintrag_name
		$sql=sprintf("SELECT form_manager_name FROM %s WHERE form_manager_id='%d'",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($this->checked->form_manager_id)
		);
		$name=$this->db->get_var($sql);
		$this->content->template['lead_eintrag_name']=$name;
		$this->content->template['form_name_tidy']=$this->tidy_string($name);
	}

	/**
	 * @param $val
	 * @return mixed|string|string[]|null
	 */
	function tidy_string($val)
	{
		// whitespace durch Bindestrich ersetzen
		$new = preg_replace('=(\s+)=', '-', $val);

		// Liste aller Umlaute
		$map = array(
			'ä' => 'ae',
			'Ä' => 'AE',
			'ß' => 'ss',
			'ö' => 'oe',
			'Ö' => 'OE',
			'Ü' => 'UE',
			'ü' => 'ue',
		);

		// Umlaute konvertieren
		$new = str_replace(array_keys($map), array_values($map), $new);

		// alle anderen Zeichen verwerfen
		$new = preg_replace('#[^A-Za-z0-9_.-]#', '', $new);

		$new = strtolower($new);

		return $new;
	}
}
