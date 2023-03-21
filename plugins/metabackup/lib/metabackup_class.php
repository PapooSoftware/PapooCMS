<?php

/**
 * Class metabackup_class
 */
#[AllowDynamicProperties]
class metabackup_class
{
	private $checked, $content, $db, $db_praefix, $csv_file, $csv_webpath;

	public function __construct()
	{
		global $checked, $content, $db, $db_praefix, $db_abs, $user;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->db = & $db;
		$this->db_abs = &$db_abs;
		$this->user = &$user;
		$this->db_praefix = $db_praefix;

		$this->csv_file = PAPOO_ABS_PFAD."/plugins/metabackup/tmp/metabackup.csv";
		$this->csv_webpath = PAPOO_WEB_PFAD."/plugins/metabackup/tmp/metabackup.csv";
		$this->content->template['plugin']['metabackup']['csv_webpath'] = $this->csv_webpath;

		if(defined("admin"))
			$this->make_admin();
	}

	public function make_admin()
	{
		if(isset($this->checked->backup)) {
			$this->backup();
			/*$this->download(); */
		}
		elseif(isset($this->checked->download) && $this->checked->download == "Download") {
			$this->download_prepare();
		}
		elseif(isset($this->checked->download) && $this->checked->download == "download_now") {
			$this->download();
		}
		elseif(isset($this->checked->upload)) {
			$this->upload();
		}
		elseif(isset($this->checked->upload_neu)) {
			$this->upload_neu();
		}
		elseif(isset($this->checked->rollback)) {
			$this->rollback();
		}
		elseif(isset($this->checked->insertnow)) {
			$this->insertnow();
		}
		if(file_exists($this->csv_file)) {
			$this->content->template['plugin']['metabackup']['last_backup'] = date("d.m.Y H:i:s", filemtime($this->csv_file));
		}
	}

	public function download_prepare()
	{
		$this->content->template['plugin']['metabackup']['message'] = "Danke. Ihr Download beginnt in 3 Sekunden.";
		$url = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."&download=download_now";
		header("refresh: 3; url=". $url);
	}

	public function download()
	{
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="metabackup.csv"');
		readfile($this->csv_file);
		exit();
	}

	public function upload()
	{
		if(isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0) {
			$this->content->template['plugin']['metabackup']['error'] = "Der Upload war fehlerhaft.";
			$this->content->template['plugin']['metabackup']['upload']['status'] = "danger";
		}
		else {
			$tmp_name = $_FILES["file"]["tmp_name"];
			move_uploaded_file($tmp_name, $this->csv_file);
			$this->content->template['plugin']['metabackup']['message'] = "Die Datei wurde hoch geladen und kann jetzt verwendet werden.";
			$this->content->template['plugin']['metabackup']['upload']['status'] = "success";
			$this->content->template['plugin']['metabackup']['step'] = 2;
		}
	}
	public function upload_neu()
	{
		if(isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0) {
			$this->content->template['plugin']['metabackup']['error'] = "Der Upload war fehlerhaft.";
			$this->content->template['plugin']['metabackup']['upload']['status'] = "danger";
		}
		else {
			$tmp_name = $_FILES["file_insert"]["tmp_name"];
			move_uploaded_file($tmp_name, $this->csv_file);
			$this->content->template['plugin']['metabackup']['message'] = "Die Datei wurde hoch geladen und kann jetzt verwendet werden.";
			$this->content->template['plugin']['metabackup']['upload']['status'] = "success";
			$this->content->template['plugin']['metabackup']['step'] = 3;
		}
	}

	public function rollback()
	{
		$fh = fopen($this->csv_file, "r");
		$head = fgetcsv($fh);

		while (($entry = fgetcsv($fh)) !== FALSE) {
			$entry = @array_combine($head, $entry);
			if($entry != false) {
				$result = $this->update($entry);
				if($result === false) {
					break;
				}
			}
			else {
				$result = false;
				break;
			}
		}

		if($result === false) {
			$this->content->template['plugin']['metabackup']['error'] = "Das Backup konnte nicht zurück gespielt werden.";
			$this->content->template['plugin']['metabackup']['rollback']['status'] = "danger";
			$this->content->template['plugin']['metabackup']['step'] = 3;
		}
		else {
			$this->content->template['plugin']['metabackup']['message'] = "Das Backup wurde zurück gespielt.";
			$this->content->template['plugin']['metabackup']['rollback']['status'] = "success";
			$this->content->template['plugin']['metabackup']['step'] = 3;
		}
	}

	public function insertnow()
	{
		$fh = fopen($this->csv_file, "r");
		$head = fgetcsv($fh,0,"\t");

		while (($entry = fgetcsv($fh,0,"\t")) !== FALSE) {
			$entry = @array_combine($head, $entry);
			if($entry != false) {
				$result = $this->insert($entry);
				if($result === false) {
					break;
				}
			}
			else {
				$result = false;
				break;
			}
		}

		if($result === false) {
			$this->content->template['plugin']['metabackup']['error'] = "Das Backup konnte nicht zurück gespielt werden.";
			$this->content->template['plugin']['metabackup']['rollback']['status'] = "danger";
			$this->content->template['plugin']['metabackup']['step'] = 3;
		}
		else {
			$this->content->template['plugin']['metabackup']['message'] = "Das Backup wurde zurück gespielt.";
			$this->content->template['plugin']['metabackup']['rollback']['status'] = "success";
			$this->content->template['plugin']['metabackup']['step'] = 3;
		}
	}

	public function backup()
	{
		$result = $this->select();
		if(@fopen($this->csv_file, "w")) {
			$fh = fopen($this->csv_file, "w");
			fputcsv($fh, array("id","url","title","description", "teaser", "article"),"\t");
			foreach ($result as $entry) {
				fputcsv($fh, $entry,"\t");
			}
			fclose($fh);

			$this->content->template['plugin']['metabackup']['message'] = "Das Backup wurde angelegt und kann jetzt verwendet werden.";
			$this->content->template['plugin']['metabackup']['backup']['status'] = "success";
			$this->content->template['plugin']['metabackup']['step'] = 1;
		}
		else {
			$this->content->template['plugin']['metabackup']['error'] = "Das Backup konnte nicht angelegt werden, da " . $this->csv_file . " nicht beschreibbar ist.";
			$this->content->template['plugin']['metabackup']['backup']['status'] = "danger";
		}
	}

	/**
	 * @return array|void
	 */
	private function select()
	{
		$sql = sprintf("SELECT
                        lan_repore_id AS id,
                        url_header AS url,
                        lan_metatitel AS title,
                        lan_metadescrip AS description,
                        lan_teaser AS teaser,
                        lan_article_sans AS inhalt
                        FROM %s",
			$this->db_praefix."papoo_language_article");

		$data= $this->db->get_results($sql, ARRAY_A);
		return $data;
	}

	/**
	 * @param $entry
	 * @return bool|void
	 */
	private function update($entry)
	{
		if( !isset($entry['id']) && !is_null($entry['id']) ||
			!isset($entry['url']) && !is_null($entry['url']) ||
			!isset($entry['title']) && !is_null($entry['title']) ||
			!isset($entry['description']) && !is_null($entry['description'])
		) {
			return false;
		}

		$sql = sprintf("UPDATE %s SET
                        url_header = '%s',
                        lan_metatitel = '%s',
                        lan_metadescrip = '%s'
                        WHERE lan_repore_id = %d",
			$this->db_praefix."papoo_language_article",
			$entry['url'],
			$entry['title'],
			$entry['description'],
			$entry['id']);

		$this->db->query($sql);
	}

	/**
	 * @param string $url
	 * @return mixed|string|string[]|null
	 */
	private function check_umlaute($url="")
	{
		// $url=urldecode($url);
		$ae = utf8_encode("ä");
		$aeb = utf8_encode("Ä");
		$ue = utf8_encode("ü");
		$ueb = utf8_encode("Ü");
		$oe = utf8_encode("ö");
		$oeb = utf8_encode("Ö");
		$amp = utf8_encode("&");
		$frag = utf8_encode("\?");
		$ss = utf8_encode("ß");
		$url = str_ireplace(" ", "-", $url);
		// $url = str_ireplace($a1, "a", $url);
		// $url = str_ireplace($a2, "a", $url);
		// $url = str_ireplace($a3, "a", $url);
		// $url = str_ireplace($c1, "c", $url);
		// $url = str_ireplace($o1, "o", $url);
		// $url = str_ireplace($o2, "o", $url);
		// $url = str_ireplace($o3, "o", $url);
		// $url = str_ireplace($i1, "i", $url);
		// Ende Kunden Mod

		// $url=str_ireplace("ue","u-e",$url);
		// $url=str_ireplace("ae","a-e",$url);
		// $url=str_ireplace("oe","o-e",$url);
		$url = str_ireplace("ä", "ae", $url);
		$url = str_ireplace("ö", "oe", $url);
		$url = str_ireplace("ü", "ue", $url);
		$url = str_ireplace("Ä", "ae", $url);
		$url = str_ireplace("Ö", "oe", $url);
		$url = str_ireplace("Ü", "ue", $url);
		$url = str_ireplace($ae, "ae", $url);
		$url = str_ireplace($aeb, "ae", $url);
		$url = str_ireplace($oe, "oe", $url);
		$url = str_ireplace($oeb, "oe", $url);
		$url = str_ireplace($ue, "ue", $url);
		$url = str_ireplace($ueb, "ue", $url);
		$url = str_ireplace("_", "-", $url);

		$url = str_ireplace($amp, "und", $url);
		$url = str_ireplace($ss, "ss", $url);
		$url = str_ireplace($frag, "", $url);

		$url = str_replace('\\', '-', $url);
		$url = str_ireplace('"', '', $url);
		$url = str_ireplace("'", '', $url);

		$url=str_ireplace("%","",$url);

		$url=str_ireplace("<","",$url);
		$url=str_ireplace(">","",$url);
		$url=str_ireplace(",","",$url);
		$url=str_ireplace(";","",$url);
		$url=str_ireplace(":","",$url);
		// $url = urlencode($url);

		return $url;
	}

	/**
	 * @param $entry
	 * @return bool|void
	 */
	private function insert($entry)
	{
		if( !isset($entry['menuid']) && !is_null($entry['menuid']) ||
			!isset($entry['url']) && !is_null($entry['url']) ||
			!isset($entry['title']) && !is_null($entry['title']) ||
			!isset($entry['description']) && !is_null($entry['description'])
		) {
			return false;
		}
		$i=1;
		//Erstmal die Basisdaten

		//Die Variablen
		$this->checked->dokuser = $this->user->userid;
		$this->checked->dokschreiben_userid = $this->user->userid;
		$this->checked->pub_dauerhaft = 1;
		$this->checked->publish_yn = 1;
		$this->checked->allow_publish = 1;
		$this->checked->order_id = $i;
		$this->checked->stamptime = time();
		$this->checked->dokuser_last = 10;
		$this->checked->cattextid = $entry['menuid'];
		$this->checked->dokschreibengrid = "";
		$this->checked->pub_verfall = "0";
		$this->checked->pub_start = "0";
		$this->checked->pub_start_page = "0";
		$this->checked->pub_wohin = "0";
		$this->checked->teaser_list = "0";

		$i=$i+10;

		//das statement
		$xsql = array();
		$xsql['dbname'] = "papoo_repore";
		$xsql['must'] = array("dokuser");
		$cat_dat = $this->db_abs->insert($xsql);
		$insertid = $cat_dat['insert_id'];

		if (!stristr($entry['inhalt'],"<p>")) {
			$entry['inhalt']="<p>".$entry['inhalt']."</p>";
		}
		$entry['inhalt']=str_ireplace("##br##","<br />",$entry['inhalt']);

		$entry['url']=$this->check_umlaute($entry['url']);

		//Dann die Sprachdaten
		$this->checked->lan_repore_id = $insertid;
		$this->checked->lang_id = 1;
		$this->checked->header = $entry['headline'];
		$this->checked->lan_teaser = $entry['teaser'];
		$this->checked->lan_article = $entry['inhalt'];
		$this->checked->lan_article_sans = $entry['inhalt'];
		$this->checked->lan_metatitel = $entry['title'];
		$this->checked->lan_metadescrip = $entry['description'];
		$this->checked->url_header = $entry['url'];
		$this->checked->publish_yn_lang = 1;

		$xsql = array();
		$xsql['dbname'] = "papoo_language_article";
		$xsql['must'] = array("lan_repore_id");
		$this->db_abs->insert($xsql);

		//Dann die Lookups schreiben
		$this->checked->article_wid_id = $insertid;
		$this->checked->gruppeid_wid_id = 1;

		$xsql = array();
		$xsql['dbname'] = "papoo_lookup_write_article";
		$xsql['must'] = array("article_wid_id");
		$this->db_abs->insert($xsql);

		$this->checked->article_wid_id = $insertid;
		$this->checked->gruppeid_wid_id = 11;

		$xsql = array();
		$xsql['dbname'] = "papoo_lookup_write_article";
		$xsql['must'] = array("article_wid_id");
		$this->db_abs->insert($xsql);

		//Dann die Lookups lesen
		$this->checked->article_id = $insertid;
		$this->checked->gruppeid_id = 1;

		$xsql = array();
		$xsql['dbname'] = "papoo_lookup_article";
		$xsql['must'] = array("article_id");
		$this->db_abs->insert($xsql);

		$this->checked->article_id = $insertid;
		$this->checked->gruppeid_id = 10;

		$xsql = array();
		$xsql['dbname'] = "papoo_lookup_article";
		$xsql['must'] = array("article_id");
		$this->db_abs->insert($xsql);

		//Dann die Lookups Menu
		$this->checked->lart_id = $insertid;
		$this->checked->lcat_id = $entry['menuid'];
		$this->checked->lart_order_id = $i;

		$xsql = array();
		$xsql['dbname'] = "papoo_lookup_art_cat";
		$xsql['must'] = array("lart_id");
		$this->db_abs->insert($xsql);
	}
}

$metabackup = new metabackup_class;
