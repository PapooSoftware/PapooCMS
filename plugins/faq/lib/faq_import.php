<?php

/**
 * Class faq_import
 */
#[AllowDynamicProperties]
class faq_import
{
	/**
	 * faq_import constructor.
	 */
	public function __construct()
	{
		global $db, $db_abs, $cms, $user, $checked, $content;
		$this->db = & $db;
		$this->db_abs = & $db_abs;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->content = & $content;

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			#$this->content->template['is_dev'] = "OK"; zentrale_self
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/", "", $template);
			$template2 = basename($template2);

			if ($template != "login.utf8.html") {
				//Generelle Einstellungen
				if ($template2 == "faq_import.html") {
					$this->do_import();
				}
			}
		}
	}

	/**
	 * @return void|bool
	 */
	public function do_import()
	{
		//Temporärer Dateiname -reicht für IMport
		$filename = isset($_FILES['myfile']['tmp_name']) ? $_FILES['myfile']['tmp_name'] : NULL;

		if (empty($filename)) {
			return false;
		}

		//Inhalt der Datei
		$content=file_get_contents($filename);

		//Zeilen aufbrechen
		$rows=$this->get_rows_from_content($content);

		//Spalten aufbrechen
		$data=$this->get_data_from_rows($rows);

		//In Datenbank einlesen
		$this->insert_into_db($data);
		if ($this->counter_faq>0) {
			$this->content->template['faq_import_ok']="ok";
			$this->content->template['counter_faq']=$this->counter_faq;
		}
	}

	/**
	 * @param array $data
	 *
	 */
	private function insert_into_db($data=array())
	{
		//$this->debug_reset();
		$this->counter=0;
		//Daten durchgehen
		foreach ($data as $k=>$v) {
			//Ersten Datensatz ignorieren
			if ($k<1) {
				continue;
			}

			$kats=array();

			//Kategorie 1 importieren
			$kats[] = $this->import_kat($v['0']);

			//Kategorie 2 importieren
			$kats[] = $this->import_kat($v['1']);

			//Kategorie 3 importieren
			$kats[] = $this->import_kat($v['2']);

			//Frage und Antwort eintragen
			$import_id = $this->import_qa($v['3'],$v['4']);

			//Lookups speichern
			$this->import_lookups($kats,$import_id);
		}
	}

	private function debug_reset()
	{

		$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."papoo_faq_categories");
		$this->db->query($sql);

		$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."papoo_faq_content");
		$this->db->query($sql);

		$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."papoo_faq_cat_link");
		$this->db->query($sql);
	}

	/**
	 * @param string $katname
	 * @return int|mixed|null
	 */
	private function import_kat($katname="")
	{
		$katname = trim($katname);
		if (empty($katname)) {
			return 0;
		}

		//Checken ob vorhanden
		$sql=sprintf("SELECT id FROM %s WHERE catname='%s'
                        AND  lang_id = '%d' ",
			DB_PRAEFIX."papoo_faq_categories",
			$this->db->escape($katname),
			$this->cms->lang_back_content_id
		);
		$id=$this->db->get_var($sql);

		if ($id>=1) {
			return $id;
		}

		//Eintragen
		$sql=sprintf("INSERT INTO %s SET
                      lang_id = '%d',
                      catname='%s' ",
			DB_PRAEFIX."papoo_faq_categories",
			$this->cms->lang_back_content_id,
			$this->db->escape($katname));
		$this->db->query($sql);

		return $this->db->insert_id;
	}

	/**
	 * @param string $frage
	 * @param string $antwort
	 * @return mixed
	 */
	private function import_qa($frage="",$antwort="")
	{
		$sql=sprintf("INSERT INTO %s
                      SET
                      lang_id='%d',
                      question = '%s',
                      answer = '%s',
                      active='j',
                      created='%s',
                      createdby='%s'
                      ",
			DB_PRAEFIX."papoo_faq_content",
			$this->cms->lang_back_content_id,
			$this->db->escape($frage),
			$this->db->escape($antwort),
			date("YmdHis"),
			$this->user->username);
		$this->db->query($sql);

		$this->counter_faq++;

		return $this->db->insert_id;
	}

	/**
	 * @param array $kats
	 * @param int $import_id
	 */
	private function import_lookups($kats=array(),$import_id=0)
	{
		$this->counter=$this->counter+10;
		$catid=0;

		foreach ($kats as $k=>$v) {
			if ($v<=0 || $v==$catid) {
				continue;
			}

			$catid=$v;
			//papoo_faq_cat_link
			$sql=sprintf("INSERT INTO %s SET
                          cat_id='%d',
                          faq_id='%d',
                          order_id='%d' ",
				DB_PRAEFIX."papoo_faq_cat_link",
				$this->db->escape($v),
				$this->db->escape($import_id),
				$this->counter
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param array $rows
	 * @return array|null
	 */
	private function get_data_from_rows($rows=array())
	{
		foreach ($rows as $k=>$v) {
			//Nach Tabs explode
			$d1=explode("\t",$v);

			//Nix, dann nächster
			if (!is_array($d1)) {
				continue;
			}
			//Daten übergehen
			$data[]=$d1;
		}
		//raus
		IfNotSetNull($data);
		return $data;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	private function get_rows_from_content($content="")
	{
		$rows=explode("\n",$content);

		return $rows;
	}
}

$faq_import=new faq_import();
