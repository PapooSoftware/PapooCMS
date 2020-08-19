<?php
/************************************
 * Cantao (TYPOlight) Import Klasse
 * @author  Christoph Zimmer
 * created  2014-09-02
 * modified 2014-10-23
 */
class ContaoImporter {

	//Werden für die Berechnung einer eindeutigen ID (1,2,3,...) verwendet.
	//Für den Fall, dass die IDs in der Contao DB Lücken haben (2,5,24).
	private $menuIDs = [];
	private $menuIDsCount = 0;
	private $articleIDs = [];
	private $articleIDsCount = 0;

	//Speichert Handle zur Contao-DB-Verbindung
	private $contaoDB;
	//Speichert das Ergebnis der SQL-Query aus fetch_contao_data
	private $contaoData;

	private $contaoFiles = [];
	private $contaoImages = [];

	private $papooInfo = [];
	private $contaoMenuLang = [];
	private $contaoMenuRootIDs = [];

	private $contaoPages = [];
	private $contaoArticles = [];
	private $contaoPageHasArticle = [];

	/**
	 * ContaoImporter constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $checked, $diverse, $user, $cms;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;

		if (defined("admin")) {
			$this->user->check_intern();

			//Wenn Formular abgeschickt.
			if (isset($this->checked->plugin_contao_import_import_starten)) {

				$this->fetch_papoo_languages();

				//Session-Variablen mit Contao DB Daten füttern.
				$this->eval_form();

				//Stelle eine Verbindung mit der Contao DB her
				$this->connect_to_contao_db();

				if(strpos($this->contaoDB->last_error, "Unknown database") !== FALSE) {
					$this->content->template['database_error'] = 'true';
					unset($this->content->template['import_step1']);
				}
				else {
					//Leere Menü- und Artikel-Tabellen
					$this->truncate_papoo_data();

					//Hole notwendige Daten aus TYPOlight/Contao Datenbank raus.
					//$this->contaoData = $this->fetch_contao_data();
					$this->fetch_contao_data();

					//Werte das Ergebnis des letzten fetch_contao_data-Aufrufs aus.
					$this->eval_contao_data();
					//Lade Bilder herunter
					$this->fetch_images();

					//Lade Dateien herunter
					$this->fetch_files();

					//Downloadlinks korrigieren
					$this->correct_downloadlinks();

					$this->content->template['daten_saved_import_blog']='true';

					//Triggert grüne Box "Daten Import erfolgt" im Template
					$_SESSION['shop_fertig_import']['artikel']=1;
				}
			}
		}
	}

	private function fetch_papoo_languages()
	{
		$this->papooInfo['language'] = [];
		$sql=sprintf("SELECT lang_id, lang_short, lang_long FROM %s",
			$this->cms->tbname["papoo_name_language"]
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		foreach ($result as $value) {
			$this->papooInfo['language'][$value['lang_short']] = $value['lang_id'];
		}
	}

	/**
	 * @param $lang
	 * @return mixed
	 */
	private function get_papoo_lang_id($lang)
	{
		return $this->papooInfo['language'][$lang];
	}

	/**
	 * @param $pid
	 * @param $lang
	 */
	private function set_contao_language($pid, $lang)
	{
		$this->contaoMenuLang[$pid] = $lang;
	}

	/**
	 * @param $pid
	 * @return mixed
	 */
	private function get_contao_language($pid)
	{
		return $this->contaoMenuLang[$pid];
	}

	/**
	 * Diese Funktion stellt eine Verbindung mit der Datenbank (im Formular angegeben) her.
	 */
	private function connect_to_contao_db()
	{
		//Verbindung zur Contao Datenbank herstellen
		$this->contaoDB = new ezSQL_mysqli(
			$_SESSION['plugin_contao_import_db_user'],
			$_SESSION['plugin_contao_import_db_passwort'],
			$_SESSION['plugin_contao_import_db_name'],
			$_SESSION['plugin_contao_import_db_server'],
			true);
		$this->contaoDB->query("SET NAMES 'utf8'");
	}

	private function eval_form()
	{
		if (!empty($this->checked->plugin_contao_import_db_server)
			&& !empty($this->checked->plugin_contao_import_db_name)
			&& !empty($this->checked->plugin_contao_import_db_user)
		)
		{
			//Verbindungsdaten setzen
			$_SESSION['plugin_contao_import_db_server']=$this->checked->plugin_contao_import_db_server;
			$_SESSION['plugin_contao_import_db_name']=$this->checked->plugin_contao_import_db_name;
			$_SESSION['plugin_contao_import_db_user']=$this->checked->plugin_contao_import_db_user;
			$_SESSION['plugin_contao_import_db_passwort']=$this->checked->plugin_contao_import_db_passwort;
			$_SESSION['plugin_contao_import_prfix']=$this->checked->plugin_contao_import_prfix."_";
			//plugin_contao_import_url_der_installation3
			$_SESSION['plugin_contao_import_url_der_installation3']=$this->checked->plugin_contao_import_url_der_installation3;

			//template setzen
			$this->content->template['import_step1']="true";
		}
	}

	/**
	 * Diese Funktion zieht alle relevanten Daten aus der TYPOlight/Contao-Datenbank heraus.
	 *
	 * @return mixed
	 * @author Christoph Zimmer
	 */
	private function fetch_contao_data()
	{
		if(!is_array($this->contaoData)) {
			$this->contaoData = [];
		}

		//Contao Tabellen-Präfix "tl_"
		$prefix = $_SESSION['plugin_contao_import_prfix'];

		//Hole Informationen aus tl_page
		$sql=sprintf("SELECT p.id AS pID, p.pid AS pPID, p.title AS pTitle, p.type AS pType, p.language AS pLang, " .
			"p.sorting AS pSorting, p.hide AS pHide, p.languageMain AS pLanguageMain, p.pageTitle AS pPageTitle, " .
			"p.description AS pDescription FROM %spage AS p ORDER BY p.id", $prefix);
		$this->contaoData['pages'] = $this->contaoDB->get_results($sql,ARRAY_A);

		//Hole Informationen aus tl_article
		$sql=sprintf("SELECT a.id AS aID, a.pid AS aPID, a.title AS aTitle, a.tstamp AS aTime, a.keywords AS aKeywords, " .
			"a.published AS aPublished FROM %sarticle AS a", $prefix);
		$this->contaoData['articles'] = $this->contaoDB->get_results($sql,ARRAY_A);

		//Hole Informationen aus tl_content
		$sql=sprintf("SELECT c.id AS cID, c.pid AS cPID, c.headline AS cHeadline, c.text AS cText, c.type AS cType, c.tstamp AS cTime, " .
			"c.singleSRC AS cSingleSrc, c.multiSRC AS cMultiSrc, c.addImage AS cAddImage, c.linkTitle AS cLinkTitle, " .
			"c.size AS cSize, c.alt AS cAlt, c.caption AS cCaption, c.floating AS cFloating, c.imagemargin AS cImageMargin, c.linkTitle AS cLinkTitle " .
			"FROM %scontent AS c " .
			"ORDER BY c.pid ASC, c.sorting ASC", $prefix);
		$this->contaoData['content'] = $this->contaoDB->get_results($sql,ARRAY_A);

		//Hole Informationen aus allen drei Tabellen
		$sql=sprintf("SELECT p.id AS pID, p.pid AS pPID, p.title AS pTitle, p.type AS pType, p.language AS pLang, a.id AS aID, " .
			"a.pid AS aPID, a.title AS aTitle, a.tstamp AS aTime, a.keywords AS aKeywords, a.published AS aPublished, " .
			"c.id AS cID, c.pid AS cPID, c.text AS cText, c.type AS cType, c.tstamp AS cTime, p.sorting AS pSorting, p.hide AS pHide " .
			"FROM %spage AS p " .
			"LEFT JOIN %sarticle AS a ON a.pid=p.id " .
			"LEFT JOIN %scontent AS c ON c.pid=a.id " .
			"WHERE p.language='de' " .
			"ORDER BY p.pid, p.id, p.sorting, a.pid, a.sorting, c.sorting;", $prefix, $prefix, $prefix);
		$this->contaoData['lookup'] = $this->contaoDB->get_results($sql,ARRAY_A);

		/*$sql=sprintf("SELECT p.id AS pID, p.pid AS pPID, p.title AS pTitle, p.type AS pType, p.language AS pLang, a.id AS aID, " .
			"a.pid AS aPID, a.title AS aTitle, a.tstamp AS aTime, a.keywords AS aKeywords, a.published AS aPublished, " .
			"c.id AS cID, c.pid AS cPID, c.text AS cText, c.type AS cType, c.tstamp AS cTime, p.sorting AS pSorting, p.hide AS pHide " .
			"FROM %spage AS p " .
			"LEFT JOIN %sarticle AS a ON a.pid=p.id " .
			"LEFT JOIN %scontent AS c ON c.pid=a.id " .
			"WHERE p.language='de' " .
			"ORDER BY p.pid, p.id, p.sorting, a.pid, a.sorting, c.sorting;", $prefix, $prefix, $prefix);

		$artikel = $this->contaoDB->get_results($sql,ARRAY_A);

		return $artikel;*/
	}

	/**
	 * Diese Funktion reicht einzelne Datensätze aus der Contao-Datenbank weiter.
	 */
	private function eval_contao_data()
	{
		if (is_array($this->contaoData)) {
			/*for ($i = 0; $i < 3; $i++) {
				foreach ($this->contaoData as $value) {
					switch ($i) {
						case 0:
							$this->import_menu($value);
							break;
						case 1:
							$this->create_article($value);
							break;
						case 2:
							$this->import_content($value);
							break;
						case 3:
							$this->import_constraint($value);
							break;
						default:
							break;
					}
				}
			}*/
			foreach ($this->contaoData['pages'] as $value) {
				$this->analyze_pages($value);
			}
			foreach ($this->contaoData['articles'] as $value) {
				$this->analyze_articles($value);
			}
			foreach ($this->contaoData['pages'] as $value) {
				$this->import_menu($value);
			}
			// foreach ($this->contaoData['pages'] as $value) {
			//     //=====================================================
			//     //Berechne den Level der gerade erstellten Menüpunkte.
			//     //=====================================================
			//     $this->correct_menu_level($value);
			// }
			foreach ($this->contaoData['articles'] as $value) {
				$this->import_article($value);
			}
			#foreach ($this->contaoData['lookup'] as $value) {
			foreach ($this->contaoData['articles'] as $value) {
				$this->import_constraint($value);
			}
			foreach ($this->contaoData['content'] as $value) {
				$this->import_content($value);
			}
		}
	}

	/**
	 * @param $tuple
	 */
	private function analyze_pages($tuple)
	{
		$id = $tuple['pID'];
		$this->contaoPages[$id] = $tuple;
		$this->contaoPages[$id]['imported'] = false;
	}

	/**
	 * @param $tuple
	 */
	private function analyze_articles($tuple)
	{
		$id = $tuple['aID'];
		$this->contaoArticles[$id] = $tuple;
		$this->contaoArticles[$id]['imported'] = false;
		$this->contaoPageHasArticle[$tuple['aPID']] = $id;
	}

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. einen Menüpunkt und Spracheintrag in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	private function import_menu($tuple)
	{
		//Contao kann Menüpunkte für verschiedene Seiten verwalten => pPID = 0
		//Irrelevant für Papoo; also return
		if($tuple['pType'] == 'root') {
			$this->contaoMenuRootIDs[] = $tuple['pID'];
			return;
		}

		$menuid = $this->get_proper_menuid($tuple['pID']);
		$menuid_id = $this->get_proper_menuid($tuple['pPID']);

		$this->set_contao_language($menuid, $tuple['pLang']);


		$isMenuPapooRootMenu = false;

		foreach($this->contaoMenuRootIDs as $id) {
			if ($menuid_id == $id) {
				$isMenuPapooRootMenu = true;
			}
		}

		//Ist der Menüpunkt Kind von einer Contao-Seite
		if ($isMenuPapooRootMenu) {
			//Menüpunkt auf oberster Ebene
			$untermenuzu = 0;
		}
		else {
			//Menüpunkt ist Kind entsprechender ID
			$untermenuzu = $menuid_id;
		}
		//=============================
		//Prüfe ob Menüpunkt existiert.
		//=============================
		$sql = sprintf("SELECT menuid FROM %s WHERE menuid=%d",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if (empty($result) && !$tuple['pHide']) {
			#if(!$this->contaoPages[$menuid]['imported']) {
			//===================
			//Erstelle Menüpunkt.
			//===================
			$sql = sprintf("INSERT INTO %s (menuid, menulink, untermenuzu, level, order_id) VALUES (%d, 'index.php', %d, %d, %d)",
				$this->cms->tbname['papoo_me_nu'],
				$menuid,
				$untermenuzu,
				$this->calc_menu_level($menuid),
				$tuple['pSorting']
			);
			$this->db->query($sql);

			//==============================================
			//Rechte für den Zugriff auf Menüpunkt vergeben.
			//==============================================
			if(!$tuple['pHide']) {
				//Lookup Tabelle Rechte Jeder
				$sql = sprintf("INSERT INTO %s SET
                                        menuid_id='%d',
                                        gruppeid_id='10'",
					$this->cms->tbname["papoo_lookup_me_all_ext"],
					$menuid
				);
			}
			$this->db->query($sql);

			//Lookup Tabelle Rechte Admin
			$sql=sprintf("INSERT INTO %s SET
                                        menuid_id='%d',
                                        gruppeid_id='1'",
				$this->cms->tbname["papoo_lookup_me_all_ext"],
				$menuid
			);
			$this->db->query($sql);

			//Dann die Rechte zur Bearbeitung auf Admin schalten
			$sql=sprintf("INSERT INTO %s SET
                                        menuid='%d',
                                        gruppenid='1'",
				$this->cms->tbname["papoo_lookup_men_ext"],
				$menuid
			);
			$this->db->query($sql);
			$sql=sprintf("INSERT INTO %s SET
                                        menuid='%d',
                                        gruppenid='11'",
				$this->cms->tbname["papoo_lookup_men_ext"],
				$menuid
			);
			$this->db->query($sql);

			$this->contaoPages[$menuid]['imported'] = true;
		}

		//=====================================================
		//Berechne die Sprache des gerade erstellten Menüpunktes.
		//=====================================================
		#$langid = $this->calc_menu_lang_id($menuid);
		#$langid = $this->get_papoo_lang_id($this->get_contao_language($this->calc_menu_lang_id($menuid)));
		$langid = $this->get_papoo_lang_id($tuple['pLang']);
		$this->contaoMenuLang[$menuid] = $langid;

		//===============================================
		//Ist dem Menüpunkt ein Spracheintrag zugewiesen?
		//===============================================
		$sql=sprintf("SELECT lang_id FROM %s WHERE lang_id=%d AND menuid_id=%d",
			$this->cms->tbname['papoo_menu_language'],
			$langid,
			$menuid
		);
		$result = $this->db->get_var($sql);

		if(empty($result))
		{
			//Erstelle Menüpunkt Spracheintrag
			$sql=sprintf("INSERT INTO %s (lang_id, menuid_id, menuname, menulinklang, " .
				"url_metadescrip, url_metakeywords, url_menuname, lang_title) VALUES (%d, %d, '%s', 'index.php', '%s', '%s', '%s', '%s')",
				$this->cms->tbname['papoo_menu_language'],
				$langid,
				$menuid,
				$tuple['pTitle'],
				$tuple['pDescription'],
				$this->contaoArticles[$this->get_articleid_from_menuid($tuple['pID'])]['aKeywords'],
				strtolower(preg_replace("/[^a-zA-Z0-9]/", "-", $tuple['pTitle'])),
				$tuple['pPageTitle']
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. einen Artikel und Spracheintrag in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	private function import_article($tuple)
	{
		$aPID = $this->get_proper_menuid($tuple['aPID']);
		$reporeid = $this->get_articleid_from_menuid($aPID);

		if($reporeid == false) {
			return;
		}

		#$reporeid = $this->get_proper_articleid($tuple['aID']);
		#$aPID = $this->get_proper_menuid($tuple['aPID']);

		$this->menuid_from_reporeid($reporeid, $aPID);

		#$langid = $this->calc_menu_lang_id($this->menuid_from_reporeid($reporeid));
		#$langid = $this->get_papoo_lang_id($this->get_contao_language($aPID));
		$langid = $this->get_papoo_lang_id($this->contaoPages[$tuple['aPID']]['pLang']);

		$sql = sprintf("SELECT reporeID FROM %s WHERE reporeID=%d",
			$this->cms->tbname['papoo_repore'],
			$reporeid
		);
		$result = $this->db->get_var($sql);
		if (empty($result)) {
			//=================
			//Erstelle Artikel.
			//=================
			$sql = sprintf("INSERT INTO %s SET reporeID = '%d',
                                    dokuser     ='10',
                                    dokschreibengrid='',
                                    timestamp='2010-09-02 11:59:59',
                                    erstellungsdatum='2010-04-22 23:10:08',
                                    pub_dauerhaft='1',
                                    publish_yn='1',
                                    teaser_list ='1',
                                    allow_publish='1',
                                    order_id='1',
                                    stamptime='1283421623',
                                    dokuser_last ='10',
                                    count='0',
                                    dok_show_teaser_teaser='0',
                                    cattextid='1',
                                    pub_verfall_page='0'",
				$this->cms->tbname['papoo_repore'],
				$reporeid
			);
			$this->db->query($sql);

			//Sind Rechte für Artikel vergeben?
			$sql = sprintf("SELECT article_id FROM %s WHERE article_id=%d AND gruppeid_id=1",
				$this->cms->tbname['papoo_lookup_article'],
				$reporeid
			);
			$result = $this->db->get_var($sql);

			if (empty($result)) {
				//============================================
				//Rechte für den Zugriff auf Artikel vergeben.
				//============================================
				//Ist Artikel veröffentlicht?
				if ($tuple['aPublished'] == 1) {
					//Lesen: jeder
					$sql = sprintf("INSERT INTO %s SET
                                        article_id=%d,
                                        gruppeid_id=10",
						$this->cms->tbname["papoo_lookup_article"],
						$reporeid
					);
					$this->db->query($sql);
				}
				//Lesen: Administrator
				$sql = sprintf("INSERT INTO %s SET
                                        article_id=%d,
                                        gruppeid_id=1",
					$this->cms->tbname["papoo_lookup_article"],
					$reporeid
				);
				$this->db->query($sql);
				//Schreiben: Administrator
				$sql = sprintf("INSERT INTO %s SET
                                        article_wid_id=%d,
                                        gruppeid_wid_id=1",
					$this->cms->tbname["papoo_lookup_write_article"],
					$reporeid
				);
				$this->db->query($sql);
				//Schreiben: Chefredakteur
				$sql = sprintf("INSERT INTO %s SET
                                        article_wid_id=%d,
                                        gruppeid_wid_id=11",
					$this->cms->tbname["papoo_lookup_write_article"],
					$reporeid
				);
				$this->db->query($sql);
			}
		}

		$sql = sprintf("SELECT lan_repore_id FROM %s WHERE lan_repore_id=%d AND lang_id=%d",
			$this->cms->tbname['papoo_language_article'],
			$reporeid,
			$langid
		);
		$result = $this->db->get_var($sql);
		if (empty($result)) {

			$sql = sprintf("INSERT INTO %s (lan_repore_id, lang_id, header, lan_metatitel, lan_metadescrip) VALUES (%d, %d, '%s', '%s', '%s')",
				$this->cms->tbname['papoo_language_article'],
				$reporeid,
				$langid,
				$this->contaoPages[$tuple['aPID']]['pTitle'], #$result #$tuple['aTitle']
				$tuple['aTitle'],
				$this->contaoPages[$tuple['aPID']]['pDescription']
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. einen Artikel und Spracheintrag in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	private function import_content($tuple)
	{
		#$aPID = $this->get_proper_menuid($tuple['aPID']);
		#$reporeid = $this->get_articleid_from_menuid($aPID);

		$cID = $tuple['cID'];
		#$cPID = $this->get_proper_articleid($tuple['cPID']);
		#$aPID = $this->menuid_from_reporeid($cPID);

		$aPID = $this->get_proper_menuid($this->contaoArticles[$tuple['cPID']]['aPID']);
		$cPID = $this->get_articleid_from_menuid($aPID);

		//Verknüpfe ContentID mit ReporeID
		$this->reporeid_from_contentid($cID, $cPID);

		#$langid = $this->calc_menu_lang_id($aPID);
		#$langid = $this->get_papoo_lang_id($this->get_contao_language($aPID));
		#$langid = $this->contaoMenuLang[$aPID];
		$langid = $this->get_papoo_lang_id($this->contaoPages[$this->contaoArticles[$tuple['cPID']]['aPID']]['pLang']);
		#debug::print_d($langid);

		if($cID == 8) {
			//exit($tuple['cSingleSrc']);
		}

		//===========================================
		//Ist Artikel ein Spracheintrag zugewiesen?
		//===========================================
		#$sql=sprintf("SELECT lan_repore_id FROM %s WHERE lan_repore_id=%d AND lang_id=1",
		$sql=sprintf("SELECT lan_repore_id FROM %s WHERE lan_repore_id=%d AND lang_id=%d",
			$this->cms->tbname['papoo_language_article'],
			$cPID,
			$langid
		);
		$result = $this->db->get_var($sql);

		$newEntry = (empty($result)) ? true : false;

		$lan_article = "";

		if(!$newEntry) {
			$sql=sprintf("SELECT lan_article FROM %s WHERE lan_repore_id=%d AND lang_id=%d",
				$this->cms->tbname['papoo_language_article'],
				$cPID,
				$langid
			);
			//Hole aktuellen Inhalt des Artikels, falls vorhanden.
			$lan_article .= $this->db->get_var($sql);
		}

		//========================================================================
		//Gehe die verschiedenen Möglichkeiten eines Contao-tl_content-Typs durch.
		//========================================================================
		if ($tuple['cType'] == 'text') {
			//Ab jetzt folgende Bilder (cType=image) in diesen text-Bereich floaten
			$lan_article .= "<br style=\"clear:both;\" />";
			$lan_article .= $this->create_content_headline($tuple['cHeadline']);

			$text = $tuple['cText'];

			//Bei tl_content.type = 'text' kann es sein, dass tl_content.addImage benutzt wird.
			if ($tuple['cAddImage'] && !empty($tuple['cSingleSrc'])) {
				//==========================================
				//Werte Daten über die Bildformatierung aus.
				//==========================================
				$margin = $this->create_image_margin($tuple['cImageMargin']);

				//Bild hinzufügen. In tl_content.singleSRC steht die URL zum Bild.
				$lan_article .= sprintf("<img src=\"/images/%s\" %s style=\"margin:%s %s %s %s; float:%s;\" alt=\"%s\" title=\"%s\" />",
					$this->create_basename($tuple['cSingleSrc']),
					$this->create_image_dimension($tuple['cSize']),
					$margin['top'],
					$margin['right'],
					$margin['bottom'],
					$margin['left'],
					$tuple['cFloating'],
					$tuple['cAlt'],
					$tuple['cCaption']);

				//Bild zum Herunterladen in die Warteschlange, falls nicht schon vorhanden.
				$this->add_image_to_queue($tuple['cSingleSrc'], $tuple['cAlt'], $tuple['cCaption']);
			}

			//Download
			preg_match_all("/href=\"(tl_files[^\"]*)/", $text, $matches, PREG_SET_ORDER);
			foreach($matches as $value) {
				#$text = preg_replace("/href=\"tl_files.*/([^\"])/","href=\"/dokumente/upload/$1", $text);
				$text = str_replace($value[1], "/dokumente/upload/".$this->create_basename($value[1]), $text);
				$this->contaoFiles[] = array($value[1], "");
			}

			$lan_article .= $text;
		}
		else if ($tuple['cType'] == 'image') {
			//Bild hinzufügen. In tl_content.singleSRC steht die URL zum Bild.
			$lan_article .= sprintf("<img src=\"/images/%s\" style=\"margin:0px 0px 10px 10px; float:right;\" alt=\"%s\" title=\"%s\" />",
				$this->create_basename($tuple['cSingleSrc']),
				$tuple['cAlt'],
				$tuple['cCaption']);

			//Bild zum Herunterladen in die Warteschlange, falls nicht schon vorhanden.
			$this->add_image_to_queue($tuple['cSingleSrc'], $tuple['cAlt'], $tuple['cCaption']);
		}
		else if ($tuple['cType'] == 'download') {
			//Bild hinzufügen. In tl_content.singleSRC steht die URL zum Bild.
			$lan_article .= sprintf("<div><a href=\"/dokumente/upload/%s\" title=\"%s\" >%s</a></div>",
				$this->create_basename($tuple['cSingleSrc']),
				$tuple['cLinkTitle'],
				$tuple['cLinkTitle']
			);
			//Bild zum Herunterladen in die Warteschlange
			$this->contaoFiles[] = array($tuple['cSingleSrc'], $tuple['cLinkTitle']);
		}
		else if ($tuple['cType'] == 'gallery') {
			preg_match_all("/\"(tl_files[^\"]*)/", $tuple['cMultiSrc'], $matches, PREG_PATTERN_ORDER);
			#debug::print_d($matches);

			foreach ($matches[1] as $match) {
				$lan_article .= sprintf("<div class=\"large-3 medium-6 columns\"><img src=\"/images/%s\" /></div>",
					$this->create_basename($match)
				);
				//Bild zum Herunterladen in die Warteschlange.
				$this->add_image_to_queue($match);
			}
		}

		$lan_article = $this->placeholder_contao_to_papoo($lan_article, $this->menuid_from_reporeid($cPID));
		$lan_article = $this->db->dbh->real_escape_string($lan_article);

		if($newEntry) {
			//Hole Artikelüberschrift
			/*$sql=sprintf("SELECT title FROM tl_article WHERE id=%d",
				$this->get_proper_articleid($tuple['cPID'])
			);
			$aTitle = $this->contaoDB->get_var($sql);*/
			$aTitle = "";

			//=================================
			//Erstelle Menüpunkt Spracheintrag.
			//=================================
			$sql = sprintf("INSERT INTO %s (lan_repore_id, lang_id, header, lan_article, lan_article_sans) VALUES (%d, %d, '%s', '%s', '%s')",
				$this->cms->tbname['papoo_language_article'],
				$cPID,
				$langid,
				$aTitle,
				$lan_article,
				$lan_article
			);
			$this->db->query($sql);
		}
		else {
			//=================================
			//Aktualisiere Menüpunkt Spracheintrag.
			//=================================
			$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
				$this->cms->tbname['papoo_language_article'],
				$lan_article,
				$lan_article,
				$cPID,
				$langid
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. einen Artikel und Spracheintrag in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	/*private function import_article($tuple)
	{
		//Handelt es sich bei dem Inhalt des Artikels um Text?
		if($tuple['cType'] == 'text') {

			//===========================
			//Prüfe ob Artikel existiert.
			//===========================
			$sql=sprintf("SELECT reporeID FROM %s WHERE reporeID=%d",
				$this->cms->tbname['papoo_repore'],
				$this->get_proper_articleid($tuple['aID'])
			);
			$result = $this->db->get_var($sql);
			if(empty($result))
			{
				//=================
				//Erstelle Artikel.
				//=================
				$sql=sprintf("INSERT INTO %s SET reporeID = '%d',
											dokuser     ='10',
											dokschreibengrid='',
											timestamp='2010-09-02 11:59:59',
											erstellungsdatum='2010-04-22 23:10:08',
											pub_dauerhaft='1',
											publish_yn='1',
											teaser_list ='1',
											allow_publish='1',
											order_id='1',
											stamptime='1283421623',
											dokuser_last ='10',
											count='0',
											dok_show_teaser_teaser='0',
											cattextid='1',
											pub_verfall_page='0'",
					$this->cms->tbname['papoo_repore'],
					$this->get_proper_articleid($tuple['aID'])
				);
				$this->db->query($sql);
			}

			//===========================================
			//Ist Artikel ein Spracheintrag zugewiesen?
			//===========================================
			$sql=sprintf("SELECT lan_repore_id FROM %s WHERE lan_repore_id=%d AND lang_id=1",
				$this->cms->tbname['papoo_language_article'],
				$this->get_proper_articleid($tuple['aID'])
			);
			$result = $this->db->get_var($sql);
			if(empty($result))
			{
				$lan_article = $this->db->dbh->real_escape_string($this->placeholder_contao_to_papoo($tuple['cText'],$tuple['aPID']));
				//=================================
				//Erstelle Menüpunkt Spracheintrag.
				//=================================
				$sql=sprintf("INSERT INTO %s (lan_repore_id, lang_id, header, lan_article, lan_article_sans) VALUES (%d, 1, '%s', '%s', '%s')",
					$this->cms->tbname['papoo_language_article'],
					$this->get_proper_articleid($tuple['aID']),
					$tuple['aTitle'],
					$lan_article,
					$lan_article
				);
				$this->db->query($sql);
			}
		}
	}*/

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. eine Artikel-Menü-Verknüpfung in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	private function import_constraint($tuple)
	{
		#$reporeid = $this->get_proper_articleid($tuple['aID']);
		#$menuid = $this->menuid_from_reporeid($reporeid);
		$menuid = $this->get_proper_menuid($tuple['aPID']);
		$reporeid = $this->get_articleid_from_menuid($menuid);

		//==========================================
		//Prüfe ob Artikel Menüpunkt zugewiesen ist.
		//==========================================
		$sql = sprintf("SELECT lart_id FROM %s WHERE lart_id=%d AND lcat_id=%d",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$reporeid, #$this->reporeid_from_contentid($tuple['cID']),
			$menuid #$this->menuid_from_reporeid($tuple['aID'])
		);
		$result = $this->db->get_var($sql);

		if (empty($result)) {
			//Erstelle Artikel-Menüpunkt-Verknüpfung
			$sql = sprintf("INSERT INTO %s (lart_id, lcat_id) VALUES (%d, %d)",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$reporeid, #$this->reporeid_from_contentid($tuple['cID']),
				$menuid #$this->menuid_from_reporeid($tuple['aID'])
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Diese Funktion säubert alle Tabellen die für Menüs oder Artikel benötigt werden.
	 * @author Christoph Zimmer
	 */
	private function truncate_papoo_data()
	{
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_repore']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_language_article']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_me_nu']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_menu_language']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_art_cat']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_article']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_write_article']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_me_all_ext']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_men_ext']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_images']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_language_image']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_download']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_lookup_download']);
		$this->db->query($sql);
		$sql=sprintf("TRUNCATE TABLE %s", $this->cms->tbname['papoo_language_download']);
		$this->db->query($sql);

		//Startseite einfügen
		$sql = "INSERT INTO `cms_ppx07_papoo_me_nu` (`menuid`, `menuname`, `menulink`, `menutitel`, `untermenuzu`, `level`, `lese_rechte`, `schreibrechte`, `intranet_yn`, `order_id`, `extra_css_file`, `extra_css_sub`, `menu_image`, `menu_subteaser`, `menu_spez_layout`, `artikel_sort`) VALUES
(1, 'Startseite', 'index.php', 'Hier geht es zur Startseite', 0, 0, 'g1,g10,', '', 0, 10, 'startseite.css', '', '', 0, 0, 1);";
		$this->db->query($sql);

		foreach($this->papooInfo['language'] as $value)
		{
			//Erstelle Menüpunkt Spracheintrag
			$sql=sprintf("INSERT INTO %s (lang_id, menuid_id, menuname, menulinklang) VALUES (%d, %d, '%s', 'index.php')",
				$this->cms->tbname['papoo_menu_language'],
				$value,
				1,
				"Startseite"
			);
			$this->db->query($sql);
		}

		$sql = sprintf("INSERT INTO %s SET menuid_id=1, gruppeid_id=10",
			$this->cms->tbname["papoo_lookup_me_all_ext"]
		);
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO %s SET menuid_id=1, gruppeid_id=1",
			$this->cms->tbname["papoo_lookup_me_all_ext"]
		);
		$this->db->query($sql);
		$sql=sprintf("INSERT INTO %s SET menuid=1, gruppenid=1",
			$this->cms->tbname["papoo_lookup_men_ext"]
		);
		$this->db->query($sql);
		$sql=sprintf("INSERT INTO %s SET menuid=1, gruppenid=11",
			$this->cms->tbname["papoo_lookup_men_ext"]
		);
		$this->db->query($sql);
		$sql=sprintf("INSERT INTO %s SET menuid=1, gruppenid=12",
			$this->cms->tbname["papoo_lookup_men_ext"]
		);
		$this->db->query($sql);
	}

	/**
	 * @param $src
	 * @param string $alt
	 * @param string $title
	 */
	private function add_image_to_queue($src, $alt = "", $title = "")
	{
		//Bild zum Herunterladen in die Warteschlange, falls nicht schon vorhanden.
		$imageExists = false;
		foreach($this->contaoImages as $image) {
			if($image[0] == $src) {
				$imageExists = true;
				break;
			}
		}
		if(!$imageExists) {
			$this->contaoImages[] = array($src, $alt, $title);
		}
	}

	/**
	 * Diese Funktion wandelt alle TYPOlight/Contao-Platzhalter in Papoo-Platzhalter um.
	 * @author Christoph Zimmer
	 * @param string $content
	 * @param int $menuid
	 * @return mixed
	 */#tl_files/grundelemente/newprologo.gif
	private function placeholder_contao_to_papoo($content, $menuid)
	{
		//Zwischenergebnis
		$ret = $content;

		//Links vom Contao-Format in Papoo-Links umwandeln
		//$ret = preg_replace("/{{link_url::(\d*)}}/", "index.php?menuid=".$menuid."&reporeid=$1", $ret);
		preg_match_all("/{{link_url::(\d*)}}/", $ret, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$id = $match[1];
			$article = "";
			if(!empty($this->contaoPageHasArticle[$id])) {
				$article = "&reporeid=" . $this->contaoPageHasArticle[$id];
			}
			$ret = str_replace(sprintf("{{link_url::%d}}", $id), sprintf("index.php?menuid=%d%s", $id, $article), $ret);
		}

		//Alle CSS-Regeln im HTML entfernen
		#$ret = preg_replace("/style=\".*?\"/", "", $ret);

		//Textzentrierung entgernen
		$ret = preg_replace("/text-align:.?center;/", "", $ret);

		//[nbsp]-Platzhalter entfernen
		$ret = preg_replace("/\[nbsp\]/", "<br/>", $ret);

		//Bilderlinks korrigieren
		if(preg_match_all("/<img[^>]*src=\"(tl_files\/.*?(?:jpg|png|gif))\".*?>/", $ret, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $image) {
				$ret = str_replace($image[1], sprintf("/images/%s", $this->create_basename($image[1])), $ret);
				//Bild zum Herunterladen in die Warteschlange, falls nicht schon vorhanden.
				$this->add_image_to_queue($image[1]);
			}
		}
		#$ret = preg_replace("/(<img[^>]*?src=\")tl_files.*?([^\/]*?)\"/", "$1images/10_$2\"", $ret);

		return $ret;
	}

	/**
	 * Diese Funktion berechnet den Level eines Menüpunktes.
	 *
	 * @param $id
	 * @return int
	 */
	private function calc_menu_level($id)
	{
		//Speichere ElternIDs (untermenuzu) um level zu berechnen
		$parentMenus = [];
		//Endlosschleifengefahr; hoffentlich nicht! :)
		while(true) {
			//Starte beim aktuellen Menüpunkt oder einem Elternmenüpunkt
			$currentMenu = (sizeof($parentMenus) == 0) ? $id : $parentMenus[sizeof($parentMenus)-1];
			//Hole nächste ElternID
			$next = $this->contaoPages[$currentMenu]['pPID'];
			//Wenn Statement leer oder beim höchsten Elternmenü angekommen, Schleife abbrechen
			if($next == 0) {
				break;
			}
			//Merke untermenuzu
			$parentMenus[] = $next;
		}
		return (sizeof($parentMenus) > 0) ? sizeof($parentMenus)-1 : 0;
	}

	/**
	 * Diese Funktion berechnet den Level eines Menüpunktes.
	 *
	 * @param $tuple
	 * @return void
	 */
	private function correct_menu_level($tuple)
	{
		//Speichere ElternIDs (untermenuzu) um level zu berechnen
		$parentMenus = [];
		//Endlosschleifengefahr; hoffentlich nicht! :)
		while(true) {
			//Starte beim aktuellen Menüpunkt oder einem Elternmenüpunkt
			$currentMenu = (sizeof($parentMenus) == 0) ? $tuple['pID'] : $parentMenus[sizeof($parentMenus)-1];
			//Hole nächste ElternID
			$sql=sprintf("SELECT untermenuzu FROM %s WHERE menuid=%d",
				$this->cms->tbname['papoo_me_nu'],
				$currentMenu
			);
			$result = $this->db->get_var($sql);
			//Wenn Statement leer oder beim höchsten Elternmenü angekommen, Schleife abbrechen
			if(empty($result) || $result == 0) {
				break;
			}
			//Merke untermenuzu
			$parentMenus[] = $result;
		}
		$sql=sprintf("UPDATE %s SET level=%d WHERE menuid=%d",
			$this->cms->tbname['papoo_me_nu'],
			sizeof($parentMenus),
			$this->get_proper_menuid($tuple['pID'])
		);
		$this->db->query($sql);
	}

	/**
	 * @param $menuid
	 * @return mixed
	 */
	private function calc_menu_lang_id($menuid)
	{
		//Speichere ElternIDs (untermenuzu) um level zu berechnen
		$parentMenus = [];
		//Endlosschleifengefahr; hoffentlich nicht! :)
		while(true) {
			//Starte beim aktuellen Menüpunkt oder einem Elternmenüpunkt
			$currentMenu = (sizeof($parentMenus) == 0) ? $menuid : $parentMenus[sizeof($parentMenus)-1];
			//Hole nächste ElternID
			$sql=sprintf("SELECT untermenuzu FROM %s WHERE menuid=%d",
				$this->cms->tbname['papoo_me_nu'],
				$currentMenu
			);
			$result = $this->db->get_var($sql);
			//Wenn Statement leer oder beim höchsten Elternmenü angekommen, Schleife abbrechen.
			if(empty($result) || $result == 0) {
				if($result == 0) {
					$page_lang = $this->get_contao_language($currentMenu);
					$id = $this->get_papoo_lang_id($page_lang);
					return $id;
				}
				break;
			}
			//Merke untermenuzu
			$parentMenus[] = $result;
		}
		return $menuid;
	}

	/**
	 * @param $reporeid
	 * @param int $menuid
	 * @return mixed
	 */
	private function menuid_from_reporeid($reporeid, $menuid = 0)
	{
		//Wenn Contao ID noch keiner geordneten ID zugewiesen wurde.
		if (empty($this->menuIDs[$reporeid]) && $menuid > 0) {
			$this->menuIDs[$reporeid] = $menuid;
		}
		else {
			return $this->menuIDs[$reporeid];
		}
	}

	/**
	 * @param $contentid
	 * @param int $reporeid
	 * @return mixed
	 */
	private function reporeid_from_contentid($contentid, $reporeid = 0)
	{
		//Wenn Contao ID noch keiner geordneten ID zugewiesen wurde.
		if (empty($this->articleIDs[$contentid]) && $reporeid > 0) {
			$this->articleIDs[$contentid] = $reporeid;
		}
		else {
			return $this->articleIDs[$contentid];
		}
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function get_proper_menuid($id)
	{
		$page = $this->contaoPages[$id]['pLanguageMain'];
		return ($page > 0) ? $page : $id;

//        //Wenn Contao ID noch keiner geordneten ID zugewiesen wurde.
//        if (empty($this->menuIDs[$id])) {
//            $this->menuIDs[$id] = ++$this->menuIDsCount;
//        }
//        #printf("%d => %s (sizeof=%s)<br/>", $id, $this->menuIDs[$id], sizeof($this->menuIDs));
//        return $this->menuIDs[$id];
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function get_proper_articleid($id)
	{
		return $id;
		//Wenn Contao ID noch keiner geordneten ID zugewiesen wurde.
		if (empty($this->articleIDs[$id])) {
			$this->articleIDs[$id] = ++$this->articleIDsCount;
		}
		#printf("%d => %s (sizeof=%s)<br/>", $id, $this->articleIDs[$id], sizeof($this->articleIDs));
		return $this->articleIDs[$id];
	}

	/**
	 * @param $menuid
	 * @return bool|mixed
	 */
	private function get_articleid_from_menuid($menuid)
	{
		$menu = $menuid; #$this->get_proper_menuid($menuid);
		foreach($this->contaoArticles as $value)
		{
			if($value['aPID'] == $menu)
			{
				return $value['aID'];
			}
		}
		return false;
	}

	/**
	 * Diese Funktion bearbeitet einen Datensatz aus der TYPOlight/Contao-Datenbank
	 * und fügt ggf. Datensätze in die Papoo-Datenbank ein.
	 * @author Christoph Zimmer
	 * @param mixed $tuple
	 */
	/*private function import($tuple)
	{
		#debug::print_d($tuple);

		//Contao kann Menüpunkte für verschiedene Seiten verwalten => pPID = 0
		//Irrelevant für Papoo; also return
		if($tuple['pPID'] == 0)
		{
			return;
		}
		//Handelt es sich bei dem Inhalt des Artikels um Text?
		else if($tuple['cType'] == 'text')
		{
			//Ist der Menüpunkt Kind von einer Contao-Seite
			if($tuple['pPID'] == 1)
			{
				//Menüpunkt auf oberster Ebene
				$untermenuzu = 0;
			}
			else
			{
				//Menüpunkt ist Kind entsprechender ID
				$untermenuzu = $tuple['pPID'];
			}

			//=============================
			//Prüfe ob Menüpunkt existiert.
			//=============================
			$sql=sprintf("SELECT menuid FROM %s WHERE menuid=%d",
				$this->cms->tbname['papoo_me_nu'],
				$this->get_proper_menuid($tuple['pID'])
			);
			#debug::print_d($sql);
			$result = $this->db->get_var($sql);

			if(empty($result))
			{
				//===================
				//Erstelle Menüpunkt.
				//===================
				$sql=sprintf("INSERT INTO %s (menuid, menulink, untermenuzu, order_id) VALUES (%d, 'index.php', %d, %d)",
					$this->cms->tbname['papoo_me_nu'],
					$this->get_proper_menuid($tuple['pID']),
					$untermenuzu,
					$tuple['pSorting']
				);
				$this->db->query($sql);
				#debug::print_d($sql);

				//=====================================================
				//Berechne den Level des gerade erstellten Menüpunktes.
				//=====================================================
				$level = $this->calc_menu_level($this->get_proper_menuid($tuple['pID']));

				//======================
				//Update Menüpunkt Level
				//======================
				$sql=sprintf("UPDATE %s SET level=%d WHERE menuid=%d",
					$this->cms->tbname['papoo_me_nu'],
					$level,
					$this->get_proper_menuid($tuple['pID'])
				);
				$this->db->query($sql);
				#debug::print_d($sql);
			}

			//===========================
			//Prüfe ob Artikel existiert.
			//===========================
			$sql=sprintf("SELECT reporeID FROM %s WHERE reporeID=%d",
				$this->cms->tbname['papoo_repore'],
				$this->get_proper_articleid($tuple['aID'])
			);
			#debug::print_d($sql);
			$result = $this->db->get_var($sql);
			$exists = empty($result) ? false : true;
			if(!$exists)
			{
				//Erstelle Artikel
				$sql=sprintf("INSERT INTO %s SET reporeID = '%d',
											dokuser     ='10',
											dokschreibengrid='',
											timestamp='2010-09-02 11:59:59',
											erstellungsdatum='2010-04-22 23:10:08',
											pub_dauerhaft='1',
											publish_yn='1',
											teaser_list ='1',
											allow_publish='1',
											order_id='1',
											stamptime='1283421623',
											dokuser_last ='10',
											count='0',
											dok_show_teaser_teaser='0',
											cattextid='1',
											pub_verfall_page='0'",
					$this->cms->tbname['papoo_repore'],
					$this->get_proper_articleid($tuple['aID'])
				);

//                $sql=sprintf("INSERT INTO %s (reporeID, publish_yn) VALUES (%d, 1)",
//                    $this->cms->tbname['papoo_repore'],
//                    $tuple['aID']
//                );
				#debug::print_d($sql);
				$this->db->query($sql);
			}

			//==========================================
			//Prüfe ob Artikel Menüpunkt zugewiesen ist.
			//==========================================
			$sql=sprintf("SELECT lart_id FROM %s WHERE lart_id=%d AND lcat_id=%d",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$this->get_proper_articleid($tuple['aID']),
				$this->get_proper_menuid($tuple['aPID'])
			);
			#debug::print_d($sql);
			$result = $this->db->get_var($sql);
			$exists = empty($result) ? false : true;
			if(!$exists)
			{
				//Erstelle Artikel-Menüpunkt-Verknüpfung
				$sql=sprintf("INSERT INTO %s (lart_id, lcat_id) VALUES (%d, %d)",
					$this->cms->tbname['papoo_lookup_art_cat'],
					$this->get_proper_articleid($tuple['aID']),
					$this->get_proper_menuid($tuple['aPID'])
				);
				#debug::print_d($sql);
				$this->db->query($sql);
			}

			//===========================================
			//Ist Menüpunkt ein Spracheintrag zugewiesen?
			//===========================================
			$sql=sprintf("SELECT lang_id FROM %s WHERE lang_id=1 AND menuid_id=%d",
				$this->cms->tbname['papoo_menu_language'],
				$this->get_proper_menuid($tuple['pID'])
			);
			#debug::print_d($sql);
			$result = $this->db->get_var($sql);
			$exists = empty($result) ? false : true;
			if(!$exists)
			{
				//Erstelle Menüpunkt Spracheintrag
				$sql=sprintf("INSERT INTO %s (lang_id, menuid_id, menuname, menulinklang) VALUES (1, %d, '%s', 'index.php')",
					$this->cms->tbname['papoo_menu_language'],
					$this->get_proper_menuid($tuple['pID']),
					$tuple['pTitle']
				);
				#debug::print_d($sql);
				$this->db->query($sql);
			}

			//===========================================
			//Ist Artikel ein Spracheintrag zugewiesen?
			//===========================================
			$sql=sprintf("SELECT lan_repore_id FROM %s WHERE lan_repore_id=%d AND lang_id=1",
				$this->cms->tbname['papoo_language_article'],
				$this->get_proper_articleid($tuple['aID'])
			);
			#debug::print_d($sql);
			$result = $this->db->get_var($sql);
			$exists = empty($result) ? false : true;
			if(!$exists)
			{
				//Erstelle Menüpunkt Spracheintrag
				$sql=sprintf("INSERT INTO %s (lan_repore_id, lang_id, header, lan_article) VALUES (%d, 1, '%s', '%s')",
					$this->cms->tbname['papoo_language_article'],
					$this->get_proper_articleid($tuple['aID']),
					$tuple['aTitle'],
					$this->db->dbh->real_escape_string($this->placeholder_contao_to_papoo($tuple['cText'],$tuple['aPID']))
				);
				#debug::print_d($sql);
				$this->db->query($sql);
			}
		}

	}*/
	/**
	 * blog_import_class::http_request_open()
	 *
	 * @param mixed $url
	 * @param integer $timeout
	 * @return string
	 */
	private function http_request_open($url,$timeout=10000)
	{
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );

		$curl_ret = curl_exec( $ch );
		curl_close( $ch );

		return trim($curl_ret);
	}

	/**
	 * @param $filename
	 * @return mixed|string
	 */
	private function create_basename($filename)
	{
		$img_basename=basename($filename);
		$img_basename=str_replace(" ","_",$img_basename);
		$img_basename=strtolower($img_basename);
		$img_basename="10_".$img_basename;
		return $img_basename;
	}

	/**
	 * Wertet die Spalte tl_content.imagemargin aus.
	 * @param string $imagemargin
	 * @return array
	 */
	private function create_image_margin($imagemargin)
	{
		$pat = "/.*\"bottom\".*?\"(\d*)\".*\"left\".*?\"(\d*)\".*\"right\".*?\"(\d*)\".*\"top\".*?\"(\d*)\".*\"unit\".*?\"([^\"]*)/";
		preg_match($pat, $imagemargin, $match);

		$margin = [];
		$margin['top']    = (!empty($match[4]) ? $match[4]."".$match[5] : "0");
		$margin['right']  = (!empty($match[3]) ? $match[3]."".$match[5] : "0");
		$margin['bottom'] = (!empty($match[1]) ? $match[1]."".$match[5] : "0");
		$margin['left']   = (!empty($match[2]) ? $match[2]."".$match[5] : "0");

		return $margin;
	}

	/**
	 * Wertet die Spalte tl_content.size aus.
	 * Gibt ggf. einen String "width=x height=x" zurück.
	 * @param string $size
	 * @return string
	 */
	private function create_image_dimension($size)
	{
		$pat = "/\"(\d*)\"[^\"]*\"(\d*)\"/";
		preg_match($pat, $size, $match);

		$width  = (!empty($match[1]) ? "width=\"".$match[1]."\"" : "");
		$height = (!empty($match[2]) ? "height=\"".$match[2]."\"" : "");
		return $width." ".$height;
	}

	/**
	 * Wertet die Spalte tl_content.headline aus.
	 * @param string $headline
	 * @return string
	 */
	private function create_content_headline($headline)
	{
		$pat = "/.*\"unit\".*?\"([^\"]*)\".*\"value\".*?\"([^\"]*)\"/";
		preg_match($pat, $headline, $match);

		if(empty($match[2])) {
			return "";
		}
		return sprintf("<%s>%s</%s>", $match[1], $match[2], $match[1]);
	}


	private function fetch_images()
	{
		#return;
		#fclose(fopen("/var/www/html/kunden/newpro/images/muster-abziehlack.jpg","w"));
		#return;

		#copy("http://www.g-pro.com/tl_files/anwendung/Dach-d.JPG", "/var/www/html/kunden/newpro/images/dach.jpg");
		#copy("http://www.g-pro.com/tl_files/abziehlack/Muster-Abziehlack.JPG", "/var/www/html/kunden/newpro/images/muster-abziehlack.jpg");
		#return;

		foreach ($this->contaoImages as $image) {
			$singleSRC = $image[0];
			$alt = $image[1];

			$remote_file=str_replace(" ", "%20", $singleSRC);

			$url = $_SESSION['plugin_contao_import_url_der_installation3']."/".$remote_file;

			#continue;

			$basename = $this->create_basename($singleSRC);

			//===========================================
			//Bild herunterladen.
			//===========================================
			$dst = PAPOO_ABS_PFAD."/images/".$basename;
			#continue;
			if(!file_exists($dst)) {
				//Kopiere Bild auf eigenen Server.
				if(!@copy($url, $dst)) {
					continue;
				}
			}

			//Breite und Höhe
			$image_info=getimagesize($dst);

			//===========================================
			//Thumbnail erstellen.
			//===========================================
			$src = $dst;
			$dst = PAPOO_ABS_PFAD."/images/thumbs/".$basename;
			//Existiert das Thumbnail schon?
			if(!file_exists($dst)) {
				//Ist das Originalbild größer als 200px; runterskalieren!
				if($image_info['0'] > 200) {
					$quotient = 200.0 / $image_info['0'];
					$newWidth = round($image_info['0'] * $quotient);
					$newHeight = round($image_info['1'] * $quotient);

					global $image_core;
					$this->image_core = &$image_core;

					$this->image_core->image_load(basename($src));
					$infos = $this->image_core->image_infos;

					$img_new = $this->image_core->image_create(array($newWidth, $newHeight), $infos['type']);
					$img_src = $this->image_core->image_create($infos['bild_temp']);
					imagecopyresampled($img_new, $img_src, 0, 0, 0, 0, $newWidth, $newHeight, $infos['breite'], $infos['hoehe']);

					$this->image_core->image_save($img_new, $dst);
				}
				else {
					//Kopiere das Bild in den Thumbnail-Ordner
					@copy($src, $dst);
				}
			}

			//===========================================
			//Bild in Datenbank eintragen.
			//===========================================
			$sql=sprintf("INSERT INTO %s SET
                                            image_name='%s',
                                            image_width ='%d',
                                            image_height ='%d',
                                            image_dir=0",
				$this->cms->tbname["papoo_images"],
				$basename,
				$image_info['0'],
				$image_info['1']
			);
			$this->db->query($sql);
			$insert_id=$this->db->insert_id;

			$sql=sprintf("INSERT INTO %s SET
                                            lan_image_id=%d,
                                            lang_id =1,
                                            alt ='%s',
                                            title ='%s'",
				$this->cms->tbname["papoo_language_image"],
				$insert_id,
				$_SESSION['data_import']['lang'],
				$alt,
				$alt
			);
			$this->db->query($sql);
		}
	}

	private function fetch_files()
	{
		foreach ($this->contaoFiles as $file) {
			$singleSRC = $file[0];
			$linkTitle = $file[1];

			$remote_file=str_replace(" ", "%20", $singleSRC);

			$url = $_SESSION['plugin_contao_import_url_der_installation3']."/".$remote_file;

			$basename = $this->create_basename($singleSRC);

			//===========================================
			//Bild herunterladen.
			//===========================================
			$dst = PAPOO_ABS_PFAD."/dokumente/upload/".$basename;

			if(!file_exists($dst)) {
				//Kopiere Datei auf eigenen Server.
				if(!@copy($url, $dst)) {
					continue;
				}
			}

			//===========================================
			//Datei in Datenbank eintragen.
			//===========================================
			$sql=sprintf("INSERT INTO %s SET
                                            downloadname='%s',
                                            downloadlink='%s',
                                            downloadkategorie=1,
                                            downloaduserid=10",
				$this->cms->tbname["papoo_download"],
				$linkTitle,
				"/dokumente/upload/".$basename
			);
			$this->db->query($sql);
			$insert_id=$this->db->insert_id;

			$sql = sprintf("INSERT INTO %s SET
                                        download_id_id=%d,
                                        gruppen_id_id=1",
				$this->cms->tbname["papoo_lookup_download"],
				$insert_id
			);
			$this->db->query($sql);
			$sql = sprintf("INSERT INTO %s SET
                                        download_id_id=%d,
                                        gruppen_id_id=10",
				$this->cms->tbname["papoo_lookup_download"],
				$insert_id
			);
			$this->db->query($sql);
			$sql = sprintf("INSERT INTO %s SET
                                        download_id=%d,
                                        lang_id=1,
                                        downloadname='%s'",
				$this->cms->tbname["papoo_language_download"],
				$insert_id,
				$linkTitle
			);
			$this->db->query($sql);
		}
	}

	private function correct_downloadlinks()
	{
		//Hole alle Downloadlinks
		$sql=sprintf("SELECT downloadid, downloadlink FROM %s",
			$this->cms->tbname['papoo_download']
		);
		$downloads = $this->db->get_results($sql,ARRAY_A);

		//Ordne jedem Downloadlink die eigene ID zu.
		$IDs = [];
		foreach ($downloads as $row) {
			$IDs[$row['downloadlink']] = $row['downloadid'];
		}

		//Hole alle Artikel-Spracheinträge
		$sql=sprintf("SELECT lan_repore_id, lan_article FROM %s",
			$this->cms->tbname['papoo_language_article']
		);
		$articles = $this->db->get_results($sql,ARRAY_A);

		//Durchsuche alle Artikel nach den Downloadlinks.
		$pat = "/href=\"(\\/dokumente\\/upload\\/[^\"]*)/";
		foreach ($articles as $row) {
			$linkFixed = false;
			$newString = $row['lan_article'];

			preg_match_all($pat, $newString, $matches, PREG_PATTERN_ORDER);

			foreach ($matches[1] as $match) {
				if(!empty($IDs[$match])) {
					$link = sprintf("index.php?menuid=xxmenuidxx&amp;downloadid=%d&amp;reporeid=xxreporeidxx",
						$IDs[$match]
					);
					$newString = str_replace($match, $link, $newString);
					$linkFixed = true;
				}
			}
			if($linkFixed) {
				$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
					$this->cms->tbname['papoo_language_article'],
					$this->db->dbh->real_escape_string($newString),
					$this->db->dbh->real_escape_string($newString),
					$row['lan_repore_id'],
					$row['lang_id']
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * aktivierung_check_plugin_class::reload()
	 *
	 * @param string $template
	 * @param string $id
	 * @return void
	 */
	function reload($template="",$id="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$template."&vorlage=new&akquise_vorlage_id=".$id;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' .
				$this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$contao_importer = new ContaoImporter();
