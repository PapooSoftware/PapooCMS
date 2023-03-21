<?php
/**
 * extended_search_class
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */

if ( stristr( $_SERVER['PHP_SELF'],'extended_search_class.php') ) die( 'You are not allowed to see this page directly' );

/**
 * Class extended_search_class
 */
#[AllowDynamicProperties]
class extended_search_class {
	// ACHTUNG!!! Wenn die PDF-Indexing-Funktionalit�t
	// benutzt werden soll, bitte folgendes beachten:
	// 1. Es m�ssen Abh�ngigkeiten installiert werden.
	// - php fileinfo extension support
	// - poppler
	// 2. in class_search_create::pdf_to_text den pfad anpassen

	public $pdf_extension = true;
	private $doc_path;

	/**
	 * extended_search_class::__construct()
	 * Initialisierung und Einbindung von Klassen
	 * @return void
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $diverse, $db, $checked, $content, $weiter, $user, $cms, $db_abs, $menu;
		$this->diverse = &$diverse;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->weiter = &$weiter;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->menu = &$menu;

		if (!defined("admin")) {
			return;
		}

		$content->template['pdf_extension'] = $this->pdf_extension;
		$this->content->template['menu_dat_show_search_aktuell'] = NULL;

		$this->user->check_intern();
		#$this->shop->echo_test();
		global $template;

		#$this->content->template['is_dev'] = "OK";

		$this->del_wenn_aufgerufen();

		$this->create_admin();

		$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
		$template2 = basename( $template2 );

		if ( $template == "login.utf8.html" ) {
			return;
		}

		$this->cms->lang_back_content_id = $this->cms->lang_back_content_id;
		$template2 = ( "extended_search/templates/" . $template2 );
		switch ( $template2 ) {
			// KOnfiguration
		case "extended_search/templates/search_back_config.html":
			$this->shop_extended_search_config();
			break;
			//Statistik
		case "extended_search/templates/search_back_stat.html":
			$this->shop_extended_search_stat();
			break;

		case "extended_search/templates/search_back_thesaurus.html":
			$this->shop_extended_search_thesaurus();
			break;
		case "extended_search/templates/search_back_index.html":
			$this->shop_extended_search_create_index();
			break;

		default:
			break;
		}
	}

	function shop_extended_search_create_index()
	{
		if (empty($this->checked->formSubmit_create_index) || !defined("admin"))
			return;

		require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

		//Klasse initialisieren und zur Verf�gung stellen
		$search_create = new class_search_create;

		//Zuerst mal timelimit etc. setzen
		@ini_set('max_execution_time', '36000');	//1 Stunde sollte reichen
		@ini_set("memory_limit", "1024M");			// so hoch wie m�glich

		$this->truncate_all_data();

		//Zuerst mal alle Artikel einbauen
		$this->insert_all_articles($search_create);

		//Dann die Produkteintr�ge
		$this->insert_all_produkte($search_create);

		//Dann die FAQ Eintr�ge
		$this->insert_all_faqs($search_create);

		//Dann die Forum Eintr�ge
		$this->insert_all_messages($search_create);

		//Dann die Flex Eintr�ge
		$this->insert_all_flex($search_create);

		//Dann die WOrd-Dokumente
		$this->insert_all_word();

		//Dann PDF Eintr�ge
		if($this->pdf_extension === true) {
			$this->insert_all_pdf($search_create);
		}

		$this->content->template['ext_search_index_fertig']="OK";
	}

	/**
	 * extended_search_class::truncate_all_data()
	 *
	 * @return void|bool
	 */
	function truncate_all_data()
	{
		if(!defined("admin")) {
			return;
		}

		//Nur neu setzen wenn es deutsch ist... damit die anderen Spracheinträg hinten dran kommen
		if ($this->cms->lang_id!=1) {
			return false;
		}

		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_ext_search_page']
		);
		$result=$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_ext_search_gruppen']
		);
		$result=$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_ext_search_vorkommen']
		);
		$result=$this->db->query($sql);
	}

	/**
	 * extended_search_class::insert_all_pdf()
	 * ben�tigt php fileinfo extension support
	 *
	 * @param class_search_create $search_create
	 *
	 * @return void
	 */
	function insert_all_pdf($search_create)
	{
		$files = array();
		$doc_path = PAPOO_ABS_PFAD.
			DIRECTORY_SEPARATOR."dokumente".
			DIRECTORY_SEPARATOR."upload";
		$dir = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($doc_path)
		);
		$finfo = new finfo(FILEINFO_MIME_TYPE); // needs fileinfo extenstion

		/** @var cms $cms */
		global $cms;
		/** @var ezSQL_mysqli $db */
		global $db;

		// Öffentliche Dateien ermitteln
		$publicCmsFiles = array_map(
			function ($filename) {
				return basename($filename);
			},
			$db->get_col(
				"SELECT _dl.downloadlink ".
				"FROM {$cms->tbname['papoo_download']} _dl ".
				"JOIN {$cms->tbname['papoo_lookup_download']} _perm ON _perm.download_id_id = _dl.downloadid ".
				"WHERE _perm.gruppen_id_id = 10 ".
				"GROUP BY _dl.downloadid"
			)
		);
		natcasesort($publicCmsFiles);
		$publicCmsFiles = array_values(array_filter($publicCmsFiles));

		foreach($dir as $path => $file) {
			if($file->isDir()) {
				continue;
			}
			elseif (in_array($file->getBasename(), $publicCmsFiles) == false) {
				continue;
			}

			if($finfo->file($path) != "application/pdf") {
				continue;
			}

			$files[] = preg_replace('/\\\/',DIRECTORY_SEPARATOR,$path);
		}
		if(empty($files)) {
			return;
		}

		foreach($files as $path) {
			$search_create->create_page_pdf($path);
		}
	}

	function insert_all_word()
	{
		include "WordConversion.php";
		//hole alle wordokumente
		$sql=sprintf("SELECT B.downloadname, A.downloadlink, B.lang_id FROM %s as A 
											LEFT JOIN %s as B ON A.downloadid = B.download_id 
											LEFT JOIN %s AS C ON C.download_id_id =  A.downloadid 
											WHERE downloadlink LIKE '%s' OR downloadlink LIKE '%s' OR downloadlink = '%s' AND C.gruppen_id_id = '10' ",
			DB_PRAEFIX."papoo_download",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_lookup_download",
			'%.doc%',
			'%.xlsx%',
			'%.pptx%');

		$result=$this->db->get_results($sql,ARRAY_A);

		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$config = $this->db->get_results($sql, ARRAY_A);

		foreach($result as $res) {

			$file_path =  PAPOO_ABS_PFAD .$res['downloadlink'];

			$doc = new WordConversion($file_path);

			$docText= $doc->convertToText();

			$sql=sprintf("INSERT INTO %s
                            SET
                            ext_search_title='%s',
                            ext_search_url='%s',
                            ext_search_completet_text='%s',
                            ext_search_lang_id='%d',
                            ext_search_document_path='%s',
                            ext_search_url_time='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$res['downloadname'],
				$file_path,
				$this->db->escape($docText),
				$res['lang_id'],
				$file_path,
				time()+	$config['0']['ext_search_blaufzeit']
			);

			$this->db->query($sql);
			$insert_id = $this->db->insert_id;
			$words = "$docText";

			//Alten Eintrag l�schen
			$sql=sprintf("DELETE FROM %s
                            WHERE ext_search_id_rid='%d'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($insert_id)
			);
			$this->db->query($sql);

			$sql=sprintf("INSERT INTO %s SET
                            ext_search_id_rid='%d',
                            ext_search_gruppen_id='%s'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($insert_id),
				$this->db->escape(10)
			);
			$this->db->query($sql);

			//Diese Klasse setzt die Suche um!!
			require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

			//Klasse initialisieren und zur Verf�gung stellen
			$search_create = new class_search_create;
			$search_create->insert_woerter($words , $insert_id);
		}
	}

	/**
	 * @param $search_create
	 */
	function insert_all_flex($search_create)
	{
		//ZUerst mal die Flextabellen raussuchen
		if (is_array($this->cms->tbname)) {
			foreach ($this->cms->tbname as $key=>$value) {
				if (stristr($value,"mv_content") && stristr($value,"_search_1")) {
					$tbs[$value]=$value;
				}
			}
		}

		if (!isset($tbs) || isset($tbs) && !is_array($tbs)) {
			return;
		}

		foreach ($tbs as $key=>$value) {
			$sql=sprintf("SELECT *
                    FROM %s
                    WHERE `mv_content_sperre` != '1'
                    GROUP BY mv_content_id",
				$value,
				$this->cms->lang_back_content_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if(!is_array($result)) {
				continue;
			}

			foreach ($result as $key2=>$value2) {
				$this->checked->submit="1";

				$search_create->create_page_flex($value2['mv_content_id'],$value,$value2);
			}
		}
	}

	/**
	 * @param $search_create
	 */
	function insert_all_faqs($search_create)
	{
		if (empty($this->cms->tbname['papoo_faq_cat_link'])) {
			return;
		}

		$sql=sprintf("SELECT *
                FROM %s
                LEFT JOIN %s ON id=faq_id
                WHERE lang_id='%d'
                GROUP BY id",
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->cms->lang_back_content_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if(!is_array($result)) {
			return;
		}

		foreach ($result as $key=>$value) {
			$this->checked->submit="1";
			$this->checked->faq_answer=$value['answer'];
			$this->checked->faq_question=$value['question'];
			#$this->checked->forumid=$value['forumid'];
			if ($value['active']=="j") {
				$this->checked->faq_new_release="true";
			}
			else {
				$this->checked->faq_new_release="";
			}

			$search_create->create_page_faq($value['id']);
		}
	}

	/**
	 * @param $search_create
	 */
	function insert_all_messages($search_create)
	{
		$sql=sprintf("SELECT * FROM %s ",
			$this->cms->tbname['papoo_message']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if(!is_array($result)) {
			return;
		}

		foreach ($result as $key=>$value) {
			$this->checked->uebermittelformular="1";
			$this->checked->inhalt=$value['messagetext'];
			$this->checked->formthema=$value['thema'];
			$this->checked->forumid=$value['forumid'];
			$this->checked->formSubmit_produxkte="true";

			$search_create->create_page_front($value['msgid']);
		}
	}

	/**
	 * @param $search_create
	 */
	function insert_all_produkte($search_create)
	{
		if (empty($this->cms->tbname['plugin_shop_produkte_lang']))
			return;
		//Alle Produktdaten rausholen
		$sql=sprintf("SELECT *
                FROM %s
                LEFT JOIN %s ON kategorien_id=produkte_kategorie_id
                LEFT JOIN %s ON produkte_produkt_id=produkte_lang_id
                WHERE produkte_lang_lang_id='%d'
                AND produkte_lang_aktiv ='1'
                GROUP BY produkte_lang_id",
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->lang_back_content_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (!is_array($result)) {
			return;
		}

		foreach ($result as $key=>$value) {
			foreach ($value as $k=>$v) {
				$this->checked->$k=$v;
			}
			$this->checked->formSubmit_produxkte="true";
			//Daten zuweisen
			/**
			$this->checked->produkte_lang_aktiv=$value['produkte_lang_aktiv'];
			$this->checked->produkte_lang_produkt_beschreibung_lang=$value['produkte_lang_produkt_beschreibung_lang'];
			$this->checked->produkte_lang_meta_title=$value['produkte_lang_produktename'];
			$this->checked->produkte_lang_meta_beschreibung=$value['produkte_lang_meta_beschreibung']." ".
			$value['produkte_lang_externeroduktid']." ".
			$value['produkte_lang_lagerung'];
			$this->checked->formSubmit_produxkte="true";
			$this->checked->produkte_lang_produkt_beschreibung.
			$this->checked->produkte_lang_produktename.
			$this->checked->produkte_lang_externeroduktid.
			$this->checked->produkte_lang_ernte.
			$this->checked->produkte_lang_teepflanzenvarietaet.
			" ".$this->checked->produkte_lang_lagerung.
			" ".$this->checked->produkte_lang_charakter.
			" ".$this->checked->produkte_lang_produkt_beschreibung.
			" ".$this->checked->produkte_lang_höhe.
			" ".$this->checked->produkte_lang_herkunft
			 *
			 * */
			$search_create->create_page_shop($value['produkte_lang_id']);
		}
	}

	/**
	 * @param $search_create
	 */
	function insert_all_articles($search_create)
	{
		$sql=sprintf("SELECT *
                FROM %s
                LEFT JOIN %s ON lan_repore_id=lart_id
                LEFT JOIN %s ON lan_repore_id=reporeID
                WHERE lang_id='%d'
                GROUP BY  lan_repore_id",
			$this->cms->tbname['papoo_language_article'],
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->cms->tbname['papoo_repore'],
			$this->cms->lang_back_content_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if(!is_array($result)) {
			return;
		}

		foreach ($result as $key => $value) {
			if($value['publish_yn_lang']!=1) {
				continue;
			}

			if(!isset($value['lan_teaser'])) {
				$value['lan_teaser'] = NULL;
			}

			IfNotSetNull($value['metatitel']);

			//Daten zuweisen
			$this->checked->reporeid=$value['lan_repore_id'];
			$this->checked->inhalt_ar['metatitel']=$value['lan_metatitel'];
			$this->checked->inhalt_ar['lcat_id']=$value['lcat_id'];
			$this->checked->inhalt_ar['metadescrip']=$value['lan_metadescrip'];
			//$this->checked->inhalt_ar['teaser']=$value['lan_article'];
			$this->checked->inhalt_ar['inhalt']=$value['lan_article']." ".$value['lan_teaser']." ".$value['lan_metadescrip']." ".$value['metatitel'];
			$this->checked->inhalt_ar['header']=$value['header'];
			$this->checked->inhalt_ar['url_header']=$value['url_header'];
			$this->checked->inhalt_ar['metadescrip']=$this->checked->inhalt_ar['metadescrip']." ".$value['lan_metakey'];
			//Gruppenrechte des Artikels
			$sql2=sprintf("SELECT * FROM %s
                    WHERE article_id='%d'",
				$this->cms->tbname['papoo_lookup_article'],
				$value['lan_repore_id']
			);
			$result2=$this->db->get_results($sql2,ARRAY_A);
			$result_rights=array();
			if(is_array($result2)) {
				foreach ($result2 as $keyg=>$valueg) {
					$result_rights[]=$valueg['gruppeid_id'];
				}
			}

			$this->checked->inhalt_ar['gruppe_write'] = $result_rights;

			$search_create->insert_artikel_search();
		}
	}

	/**
	 * extended_search_class::del_wenn_aufgerufen()
	 * Funktion l�scht Eintr�ge wenn diese in der Admin aufgerufen werden
	 *
	 * @return void
	 */
	function del_wenn_aufgerufen()
	{
		//Zuerst Artikel
		if (!empty($this->checked->loeschenecht)) {
			$sql = sprintf("SELECT ext_search_id FROM %s
                    WHERE ext_search_seite_reporeid='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->reporeid)
			);
			$ids=$this->db->get_results($sql,ARRAY_A);

			//L�schen
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_seite_reporeid='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->reporeid)
			);
			$this->db->query($sql);

			$this->delete_rest($ids);
		}
		//Messages
		if (isset($this->checked->message_del) && is_array($this->checked->message_del)) {
			foreach ($this->checked->message_del as $key=>$value) {
				$sql=sprintf("	SELECT ext_search_id FROM %s
                  				WHERE ext_search_seite_message_id='%d'",
					$this->cms->tbname['plugin_ext_search_page'],
					$this->db->escape($key)
				);
				$ids=$this->db->get_results($sql,ARRAY_A);

				//L�schen
				$sql=sprintf("DELETE  FROM %s
  												WHERE ext_search_seite_message_id='%d'",
					$this->cms->tbname['plugin_ext_search_page'],
					$this->db->escape($key)
				);
				$this->db->query($sql);

				$this->delete_rest($ids);
			}
		}
		//Messages
		if (isset($this->checked->msgid) && is_numeric($this->checked->msgid)) {
			$sql=sprintf("SELECT ext_search_id FROM %s
                  WHERE ext_search_seite_message_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->msgid)
			);
			$ids=$this->db->get_results($sql,ARRAY_A);

			//L�schen
			$sql=sprintf("DELETE  FROM %s
  												WHERE ext_search_seite_message_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->msgid)
			);
			$this->db->query($sql);

			$this->delete_rest($ids);
		}
		//SHop Eintr�ge
		if (isset($this->checked->produkt_id) && is_numeric($this->checked->produkt_id)) {

		}
		//Flex Eintr�ge
		if (isset($this->checked->mv_content_idx) && is_numeric($this->checked->mv_content_idx)) {
			$sql=sprintf("SELECT ext_search_id FROM %s
                  WHERE ext_search_seite_mv_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->mv_content_id)
			);
			$ids=$this->db->get_results($sql,ARRAY_A);

			//L�schen
			$sql=sprintf("DELETE  FROM %s
  												WHERE ext_search_seite_mv_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->mv_content_id)
			);
			$this->db->query($sql);

			$this->delete_rest($ids);
		}
	}

	/**
	 * extended_search_class::delete_rest()
	 *
	 * @param $ids
	 * @return void
	 */
	function delete_rest($ids)
	{
		if(!is_array($ids))
			return;

		foreach ($ids as $key=>$value) {
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_id_rid='%d'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$value['ext_search_id']
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_seite_id='%d'",
				$this->cms->tbname['plugin_ext_search_vorkommen'],
				$value['ext_search_id']
			);
			$this->db->query($sql);
		}
	}

	/**
	 * extended_search_class::post_papoo()
	 *
	 * @return void
	 */
	function post_papoo()
	{

		//Checken ob die ID in der DB ist
		$sql=sprintf("SELECT * FROM %s WHERE ext_search_menus LIKE '%s' ",
			DB_PRAEFIX."plugin_ext_search_config",
			'%'.'"'.$this->db->escape($this->checked->menuid).'"'.'%');
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['menu_dat_show_search_aktuell']="ok";
		$this->content->template['is_extended_search']="OK";

		//Diese Klasse setzt die Suche um!!
		require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search.php";
		//Klasse initialisieren und zur Verf�gung stellen
		$search_front = new class_search_front();

		//Wenn keine Suche
		if(empty($this->checked->search)) {
			$search_front->make_js_header_modul();
			return;
		}

		//ergebniss raussuchen
		$result=$search_front->jetzt_suchen();
		//Ergebnissde der Standardsuche resetten
		$this->content->template['table_data']=$result;	}


	/**
	 * extended_search_class::create_admin()
	 *
	 * @return void
	 */
	function create_admin()
	{
		//Diese Klasse setzt die Suche um!!
		require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

		//Klasse initialisieren und zur Verf�gung stellen
		$search_create = new class_search_create;

		$search_create->create_page();
	}

	/**
	 * extended_search_class::output_filter()
	 *
	 * @return void
	 */
	function output_filter()
	{
		//Diese Klasse setzt die Suche um!!
		require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

		//Klasse initialisieren und zur Verf�gung stellen
		$search_create = new class_search_create();

		//$search_create->create_page_front();

		$search_create->make_highlighting();
	}


	/**
	 * extended_search_class::shop_extended_search_thesaurus()
	 *
	 * @return void
	 */
	function shop_extended_search_thesaurus()
	{
		//Self link setzen
		$self=$this->content->template['thesaurus_self_link']="plugin.php?menuid=".$this->checked->menuid."&template=extended_search/templates/search_back_thesaurus.html";
		//L�schen
		if (!empty($this->checked->formSubmit_delete_thesaurus)) {
			//zuerst l�schen
			$sql=sprintf("DELETE  FROM %s
                WHERE ext_search_thesaurus_org_id='%d'",
				$this->cms->tbname['plugin_ext_search_thesaurus_search'],
				$this->db->escape($this->checked->thes_id)
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_thesaurus_id='%d'",
				$this->cms->tbname['plugin_ext_search_thesaurus'],
				$this->db->escape($this->checked->thes_id)
			);
			$this->db->query($sql);

			$location_url = $self."&is_the_del=OK";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		//Auf neu setzen
		if (isset($this->checked->thes_id) && $this->checked->thes_id=="new") {
			$this->content->template['is_new_thesaurus']="1";
			$this->content->template['thes_id']="new";
		}

		if (isset($this->checked->is_the_saved) && $this->checked->is_the_saved=="OK") {
			$this->content->template['is_eingetragen']="ok";
			$this->content->template['thes_id']="new";
		}

		if (isset($this->checked->is_the_del) && $this->checked->is_the_del=="OK") {
			$this->content->template['is_eingetragen']="del";
		}

		if (!empty($this->checked->formSubmit_search_thesaurus) && $this->checked->thes_id=="new") {
			$this->checked->ext_search_thesaurus_lang_id = $this->cms->lang_id;

			//Insert
			$xsql['dbname'] = "plugin_ext_search_thesaurus";
			$xsql['praefix'] = "ext_search_thesaurus";
			$xsql['must'] = array("ext_search_thesaurus_uchbegriffe","ext_search_thesaurus_einten_ie_vielleicht_egriff");
			$cat_dat = $this->db_abs->insert( $xsql );

			//Wenn eingetragen
			if (!empty($cat_dat)) {
				$this->insert_search($cat_dat['insert_id']);

				$location_url = $self."&is_the_saved=OK";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}
		}

		if (!empty($this->checked->formSubmit_search_thesaurus) && is_numeric($this->checked->thes_id)) {
			$this->checked->ext_search_thesaurus_lang_id=$this->cms->lang_id;

			$this->checked->ext_search_thesaurus_id=$this->checked->thes_id;

			//Insert
			$xsql['dbname'] = "plugin_ext_search_thesaurus";
			$xsql['praefix'] = "ext_search_thesaurus";
			$xsql['must'] = array("ext_search_thesaurus_uchbegriffe","ext_search_thesaurus_einten_ie_vielleicht_egriff");
			$xsql['where_name'] = "ext_search_thesaurus_id";
			$cat_dat = $this->db_abs->update( $xsql );

			//Wenn eingetragen
			if (!empty($cat_dat)) {
				$this->insert_search($this->checked->thes_id);

				$location_url = $self."&is_the_saved=OK";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}
		}
		//Einer ausgew�hlt
		if (isset($this->checked->thes_id) && is_numeric($this->checked->thes_id)) {
			$sql=sprintf("SELECT * FROM %s
                    WHERE ext_search_thesaurus_id='%d'
                    AND ext_search_thesaurus_lang_id='%d'",
				$this->cms->tbname['plugin_ext_search_thesaurus'],
				$this->db->escape($this->checked->thes_id),
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$this->content->template['is_new_thesaurus']="1";
			$this->content->template['is_edit_thesaurus_now']="OK";
			$this->content->template['thes_id']=$result['0']['ext_search_thesaurus_id'];
			$result['0']['ext_search_thesaurus_uchbegriffe']="nobr:".$result['0']['ext_search_thesaurus_uchbegriffe'];

			$this->content->template['ext_search_thesaurus']=$result;
		}

		//Liste rausholen
		if (empty($this->checked->thes_id)) {
			$sql=sprintf("SELECT * FROM %s
                    WHERE ext_search_thesaurus_lang_id=%d
                    ORDER BY ext_search_thesaurus_einten_ie_vielleicht_egriff ASC",
				$this->cms->tbname['plugin_ext_search_thesaurus'],
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$this->content->template['ext_search_thesaurus']=$result;
		}
	}

	/**
	 * extended_search_class::insert_search()
	 *
	 * @param mixed $org_id
	 * @return void
	 */
	function insert_search($org_id) {
		//zuerst l�schen
		$sql=sprintf("DELETE  FROM %s
                WHERE ext_search_thesaurus_org_id='%d'",
			$this->cms->tbname['plugin_ext_search_thesaurus_search'],
			$this->db->escape($org_id)
		);
		$this->db->query($sql);

		//Dann eintragen
		$begriffe=explode("\n",$this->checked->ext_search_thesaurus_uchbegriffe);

		if(empty($begriffe))
			return;

		foreach ($begriffe as $key=>$value) {
			$this->checked->ext_search_thesaurus_org_id=$org_id;
			$this->checked->ext_search_thesaurus_id="";
			$this->checked->ext_search_thesaurus_uchbegriffe=trim($value);

			$xsql['dbname'] = "plugin_ext_search_thesaurus_search";
			$xsql['praefix'] = "ext_search_thesaurus";
			$xsql['must'] = array("ext_search_thesaurus_uchbegriffe","ext_search_thesaurus_einten_ie_vielleicht_egriff");
			$this->db_abs->insert( $xsql );
		}
	}

	/**
	 * extended_search_class::shop_extended_search_stat()
	 *
	 * @return void
	 */
	function shop_extended_search_stat()
	{
		if (empty($this->checked->einstellungen_lang_ie_letzten)) {
			$this->checked->einstellungen_lang_ie_letzten=date("d.m.Y",time()-2592000);
		}
		if (empty($this->checked->einstellungen_lang_inheit2)) {
			$this->checked->einstellungen_lang_inheit2=date("d.m.Y",time());
		}
		//Self link setzen
		$self=$this->content->template['thesaurus_self_link']="plugin.php?menuid=".$this->checked->menuid."&template=extended_search/templates/search_back_thesaurus.html";
		//Datumswerte neu ausgeben
		$this->content->template['einstellungen_lang_ie_letzten'] = $this->checked->
		einstellungen_lang_ie_letzten;
		$this->content->template['einstellungen_lang_inheit2'] = $this->checked->
		einstellungen_lang_inheit2;

		$split_time1 = explode( ".", $this->checked->einstellungen_lang_ie_letzten );
		$start_time = @mktime( 0, 0, 0, $split_time1['1'], $split_time1['0'], $split_time1['2'] );
		if ( empty($this->checked->einstellungen_lang_ie_letzten) ) {
			$start_time = time() - 2592000;
		}

		//Endzeitpunkt
		$split_time2 = explode( ".", $this->checked->einstellungen_lang_inheit2 );
		$stop_time = @mktime( 0, 0, 0, $split_time2['1'], $split_time2['0'], $split_time2['2'] ) +
			172800;

		if ( empty($this->checked->einstellungen_lang_inheit2) ) {
			$stop_time = time();
		}
		$show_count="  ";
		if (isset($this->checked->ext_search_stat_ur_zusammengesetzte) && $this->checked->ext_search_stat_ur_zusammengesetzte==1) {
			$this->content->template['ext_search_stat_ur_zusammengesetzte']=1;
			$show_count=" AND ext_search_count='2' ";
		}

		//Daten rausholen
		$sql = sprintf( "SELECT ext_search_stat_search ,
                  SUM(ext_search_count) as ext_search_count
                  FROM %s  WHERE
                  ext_search_stat_time >'%d'
                  AND
                  ext_search_stat_time <'%d'
                  %s
                  GROUP BY ext_search_stat_search
                  ORDER BY ext_search_count DESC
                  LIMIT 5000",
			$this->cms->tbname['plugin_ext_search_statistik'],
			$start_time,
			$stop_time,
			$show_count
		);
		$result = $this->db->get_results( $sql, ARRAY_A );

		//�bergeben
		$this->content->template['ext_search_stat'] = $result;
	}

	/**
	 * extended_search_class::shop_extended_search_config()
	 *
	 * @return void
	 */
	function shop_extended_search_config()
	{
		//Daten speichern formSubmit_search_settings
		if ( !empty($this->checked->formSubmit_search_settings) ) {
			//ANzahl der Ergebnisse numerisch, im Zeifel 20
			if ( !is_numeric($this->checked->ext_search_nzahl_der_uchergebnisse) ) {
				$this->checked->ext_search_nzahl_der_uchergebnisse = 20;
			}

			$this->checked->ext_search_teaser_max_length = max(0, min((int)$this->checked->ext_search_teaser_max_length, 0xFFFF));

			//Ablaufzeit, im Zweifel auf 8 Tage setzen
			if (!isset($this->checked->ext_search_blaufzeit) || isset($this->checked->ext_search_blaufzeit) &&  !is_numeric($this->checked->ext_search_blaufzeit) ) {
				$this->checked->ext_search_blaufzeit = 691200;
			}
			IfNotSetNull($this->checked->inhalt_ar);
			$this->checked->ext_search_menus=serialize($this->checked->inhalt_ar);

			//Zuerst delete ext_search_id
			$xsql['dbname'] = "plugin_ext_search_config";
			$xsql['del_where_wert'] = " ext_search_id > '0'";
			$this->db_abs->delete( $xsql );

			//Insert
			$xsql['dbname'] = "plugin_ext_search_config";
			$xsql['praefix'] = "ext_search";
			$this->db_abs->insert( $xsql );

			$this->content->template['is_eingetragen']="ok";
		}

		//Daten holen und zuweisen
		$sql = sprintf( "SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		$result2=$result;

		//Nobrs �bergeben
		$result['0']['ext_search_lemente_mit_ewichtung'] = "nobr:" . $result['0']['ext_search_lemente_mit_ewichtung'];

		$result['0']['ext_search_top_rter'] = "nobr:" . $result['0']['ext_search_top_rter'];

		$result['0']['ext_search__fr_igh'] = "nobr:" . $result['0']['ext_search__fr_igh'];

		//Zuordnung Mneü
		$mdat= unserialize( $result2['0']['ext_search_menus']);
		$mdat=$mdat['cattext_ar'];
		$i=0;
		if (is_array($mdat)) {
			foreach ( $mdat as $k=>$v) {
				$i++;
				$mdat2[$i]['banner_menu_id']=$v;
			}
		}
		//banner_menu_id
		$this->content->template['banner_ar']= isset($mdat2) ? $mdat2 : NULL;
		//�bergeben
		$this->content->template['ext_search'] = $result;

		$this->get_liste_menu();

		$ausgabe=serialize($result2);
		$this->diverse->write_to_file("/interna/templates_c/highlight.txt",$ausgabe);
	}

	/**
	 * Liste der Men�punkte besorgen
	 */
	function get_liste_menu()
	{
		$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");
		$this->content->template['menulist_data'] = $this->menu->data_front_complete;
	}
}

$ext_search = new extended_search_class();