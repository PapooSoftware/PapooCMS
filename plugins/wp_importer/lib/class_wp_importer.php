<?php

/**
 * Class class_wp_importer
 */
class class_wp_importer
{
	/**
	 * class_wp_importer constructor.
	 */
	public function __construct()
	{
		global $content, $db, $checked, $diverse, $user, $cms, $weiter, $db_abs;
		$this->content = &$content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {
				//Neue anlegen
				if(!empty($this->checked->startimportblogger)) {
					$this->start_import();
					$this->content->template['daten_saved_import_blog'] = 'true';
				}
			}
		}
	}

	/**
	 * blog_import_class::start_import()
	 *
	 * @return void
	 */
	private function start_import()
	{
		if (empty($this->checked->blogger_import_db_server) && empty($this->checked->blogger_import_db_name) && empty($this->checked->blogger_import_db_user)) {
			$this->content->template['import_error'] = "true";
			return;
		}
		//Verbindungsdaten setzen
		$_SESSION['blogger_import_db_server'] = $this->checked->blogger_import_db_server;
		$_SESSION['blogger_import_db_name'] = $this->checked->blogger_import_db_name;
		$_SESSION['blogger_import_db_user'] = $this->checked->blogger_import_db_user;
		$_SESSION['blogger_import_db_passwort'] = $this->checked->blogger_import_db_passwort;
		$_SESSION['blogger_import_prfix'] = $this->checked->blogger_import_prfix;

		//blogger_import_url_der_installation3 -- (keine ahnung wofuer die 3 steht)
		$_SESSION['blogger_import_url_der_installation3'] = $this->checked->blogger_import_url_der_installation3;

		//template setzen
		$this->content->template['import_step1']="true";

		$this->import_menu();

		$this->import_artikel();
	}

	/**
	 * blog_import_class::import_menu()
	 *
	 * @return void
	 */
	private function import_menu() 
	{
		//Erstemal Verbindung zur DB aufbauen mit den geprüften Daten
		$db2=new ezSQL_mysqli(
			$_SESSION['blogger_import_db_user'],
			$_SESSION['blogger_import_db_passwort'],
			$_SESSION['blogger_import_db_name'],
			$_SESSION['blogger_import_db_server'],
			true
		);
		$menu_array = $this->get_menu($db2);

		$this->create_menu($menu_array);
	}

	/**
	 * blog_import_class::import_menu()
	 *
	 * @return void
	 * @param array $menu_array
	 */
	private function create_menu(array $menu_array)
	{
		// versuch die alte insert_kategorien() funktion zu recycln
		$re_mapped_menu_array = array_map(function ($menu) {
			return [
				"term_id" => $menu["menu_id"],
				"name" => $menu["title"],
				"slug" => preg_replace('~-{2,}~', "-",
				preg_replace('~[^a-z]~', "-", strtolower($menu["title"]))),
				"term_group" => $menu["parent_id"],
			];
		}, $menu_array);
		$this->insert_kategorien($re_mapped_menu_array);
	}

	/**
	 * blog_import_class::import_menu()
	 *
	 * @return array
	 * @param ezSQL_mysqli $db2
	 */
	private function get_menu(ezSQL_mysqli $db2) : array
	{
		$db_prefix = $_SESSION['blogger_import_prfix'];

		// alle post_entries rausholen die auf ein menu verweisen
		$post_sql = "SELECT menu_id, order_id, parent_id, `post_title` AS title
			FROM `{$db_prefix}posts`
			JOIN (
				SELECT menu_id, order_id, parent_id, {$db_prefix}postmeta.meta_value AS title_id
				FROM `{$db_prefix}postmeta`
				JOIN (
					SELECT {$db_prefix}posts.ID AS menu_id, {$db_prefix}posts.menu_order AS order_id, {$db_prefix}postmeta.meta_value AS parent_id
					FROM `{$db_prefix}posts`
					JOIN `{$db_prefix}postmeta` ON `post_id` = `ID`
					WHERE `post_type` LIKE 'nav_menu_item'
					AND `meta_key` LIKE '_menu_item_menu_item_parent'
				) AS temp_post ON `post_id` = `menu_id`
				WHERE `meta_key` LIKE '_menu_item_object_id'
			) AS final_temp ON `title_id` = `ID`
			ORDER BY order_id;";	

		return $db2->get_results($post_sql, ARRAY_A);
	}


	/**
	 * blog_import_class::import_artikel()
	 *
	 * @return void
	 */
	private function import_artikel()
	{
		//Erstemal Verbindung zur DB aufbauen mit den geprüften Daten
		$db2=new ezSQL_mysqli(
			$_SESSION['blogger_import_db_user'],
			$_SESSION['blogger_import_db_passwort'],
			$_SESSION['blogger_import_db_name'],
			$_SESSION['blogger_import_db_server'],
			true
		);

		$db2->query("SET NAMES 'utf8'");

		//Dann die Gruppendaten raussuchen
		$artikel = $this->get_artikel_daten($db2);

		//Dann diese einfügen
		$this->insert_articles($artikel);

		//Dann die Links korrigieren
		$this->do_correct_links();

		$_SESSION['shop_fertig_import']['artikel'] = 1;
	}

	/**
	 * blog_import_class::insert_articles()
	 *
	 * @param $articles
	 * @return void
	 */
	private function insert_articles($articles)
	{
		if(!is_array($articles)) {
			return;
		}

		//Artikel durchlaufen und schauen zu welchem Menüpunkte er gehört
		foreach ($articles as $article) {
			$artikel_menuid = 0;

			//Dann schauen welcher Menüpunkt
			if (is_array($this->kategorien_all)) {
				foreach ($this->kategorien_all as $key_sub=>$value_sub) {
					//Wenn der Name in der Url der Subkats vorkommt, dann zu diesem Menüpunkt sortieren
					if ($key_sub==$article['term_taxonomy_id']) {
						$artikel_menuid=$value_sub;
					}
				}
			}
			if($artikel_menuid == 0) {
				$artikel_menuid = $this->kategorien_all[$article['menu_id']];
			}
			//Dann die passenden Artikel erstellen
			$this->create_single_article($artikel_menuid, $article, 0);
		}
	}

	/**
	 * blog_import_class::create_singel_article()
	 *
	 * @param mixed $menuid
	 * @param mixed $value
	 * @param integer $anteasern
	 * @return void
	 */
	private function create_single_article($menuid, $value, $anteasern = 0)
	{
		$vorhanden = 0;

		//Zuerst checken ob der Artikel schon vorhanden ist
		$sql = sprintf("SELECT (reporeID) FROM %s
			WHERE dokschreibengrid='%d'",
			$this->cms->tbname['papoo_repore'],
			$value['ID']
		);
		$vorhanden = $this->db->get_var($sql);

		//Nur wenn es auch einen Menüpunkt dazu gibt, ansonsten ist das eine Revision...
		//if ($value['term_taxonomy_id']>=1)
		{
			$this->order_id++;
			//Eintrag noch nicht vorhanden - wurde noch nicht importiert
			if ($vorhanden<1) {
				$alte_url="index.php?area=".$value['area']."&p=static&page=".$value['pagename'];
				//Dann den Artikel dazu anlegen
				$sql=sprintf("INSERT INTO %s SET
					dokuser     ='10',
					dokschreibengrid='%d',
					timestamp='%s',
					erstellungsdatum='%s',
					pub_dauerhaft='1',
					publish_yn='1',
					teaser_list ='1',
					allow_publish='1',
					order_id='%d',
					stamptime='%d',
					dokuser_last ='10',
					count='%d',
					dok_show_teaser_teaser='%d',
					cattextid='%d',
					pub_verfall_page='0'

					",
					$this->cms->tbname["papoo_repore"],
					$value['ID'],
					date("Y-m-d h:i:s"),
					date("Y-m-d h:i:s"),
					$this->order_id,
					strtotime($value['post_modified']),
					0,
					$anteasern,
					$menuid
				);

				$this->db->query($sql);
				$insertid = $this->db->insert_id;
			}

			//Dann den Spracheintrag
			$url=strtolower(str_replace(" ","-",$value['post_title']));
			$url ="/". preg_replace("/[^a-z0-9\-]/", "", $url).".html";
			$vorhanden_lang=0;

			//Sprachdaten bereinigen
			$value=$this->make_correct_sprachdata($value);
			$value['post_content']=str_ireplace("font-family: Myriad Pro;","",$value['post_content']);
			// $value['post_content']=strip_tags(($value['post_content']),"<br><p><img><h1><h2><h3>");
			// $value['post_content']=strip_tags(html_entity_decode($value['post_content']),"<br><p><img><h1><h2><h3>");
			$value['post_content']=(html_entity_decode($value['post_content']));
			if ($vorhanden>0) {
				//Checken ob schon in Sprachtabelle vorhanden
				$sql=sprintf("SELECT (lan_repore_id) FROM %s
					WHERE lan_repore_id='%d'
					AND lang_id='%d'",
					$this->cms->tbname['papoo_language_article'],
					$vorhanden,
					$_SESSION['data_import']['lang']
				);
				$vorhanden_lang=$this->db->get_var($sql);
				if ($vorhanden_lang<1) {
					//Dann die Sprachtabelle
					$sql=sprintf("INSERT INTO %s SET
						lan_repore_id='%d',
						lang_id='%d',
						header='%s',
						lan_teaser ='%s',
						lan_article='%s',
						lan_article_sans='%s',
						lan_metatitel='%s',
						lan_metadescrip='%s',
						url_header  ='%s',
						lan_teaser_img_fertig='%s'",
						$this->cms->tbname["papoo_language_article"],
						$insertid,
						$_SESSION['data_import']['lang'],
						$value['post_title'],
						$this->db->escape(($value['post_content']),0,400),
						$this->db->escape($value['post_content']),
						$this->db->escape($value['post_content']),
						$this->db->escape($value['post_title']),
						$this->db->escape(substr(strip_tags($value['post_content']),0,140)),
						$url,
						$this->db->escape($value['post_content'])
					);
					$this->db->query($sql);
				}
			}
			else {
				//Dann die Sprachtabelle
				$sql=sprintf("INSERT INTO %s SET
					lan_repore_id='%d',
					lang_id='%d',
					header='%s',
					lan_teaser ='%s',
					lan_article='%s',
					lan_article_sans='%s',
					lan_metatitel='%s',
					lan_metadescrip='%s',
					url_header  ='%s',
					lan_teaser_img_fertig='%s'",
					$this->cms->tbname["papoo_language_article"],
					$insertid,
					$_SESSION['data_import']['lang'],
					$this->db->escape($value['post_title']),
					$this->db->escape(substr($value['post_content'],0,400)),
					$this->db->escape($value['post_content']),
					$this->db->escape($value['post_content']),
					$this->db->escape($value['post_title']),
					$this->db->escape(substr(strip_tags($value['post_content']),0,140)),
					$url,
					$this->db->escape(substr($value['post_content'],0,400))
				);
				$this->db->query($sql);
			}
			//Dann die Lese  und Schreibrechte
			if ($vorhanden<1) {
				//DAnn die Lookup Tabelle Rechte JEder
				$sql=sprintf("INSERT INTO %s SET
					article_id='%d',
					gruppeid_id='10'",
					$this->cms->tbname["papoo_lookup_article"],
					$insertid
				);
				$this->db->query($sql);

				//Lookup Tabelle Rechte Admin
				$sql=sprintf("INSERT INTO %s SET
					article_id='%d',
					gruppeid_id='1'",
					$this->cms->tbname["papoo_lookup_article"],
					$insertid
				);
				$this->db->query($sql);

				//Dann die Rechte zur Bearbeitung auf Admin schalten
				$sql=sprintf("INSERT INTO %s SET
					article_wid_id='%d',
					gruppeid_wid_id='1'",
					$this->cms->tbname["papoo_lookup_write_article"],
					$insertid
				);
				$this->db->query($sql);

				//Dann die Lookups.
				$sql=sprintf("INSERT INTO %s SET
					lart_id='%d',
					lcat_id ='%d',
					lart_order_id ='%d'",
					$this->cms->tbname["papoo_lookup_art_cat"],
					$insertid,
					$menuid,
					$this->order_id
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * blog_import_class::make_correct_sprachdata()
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	private function make_correct_sprachdata($value)
	{
		$content=$value['post_content'];

		$value['post_content']=str_replace("\n","",$value['post_content']);
		$value['post_content']=str_replace("\r","",$value['post_content']);

		//Bild daten rausholen pro Text
		preg_match_all( '/<img[^>]*>/Ui', $value['post_content'], $img );

		if (is_array($img['0'])) {
			foreach ($img['0'] as $key2=>$value2) {
				$pfad_1=explode("src=\"",$value2);
				$pfad_2=explode("\"",$pfad_1['1']);
				$images[]=$pfad_2['0'];
			}
		}
		// Die Bilder selber
		//Dann die Bilder downloaden und in die DB eintragen
		$this->get_images($images,$value['title']);
		//Dann den Text durchgehen und ersetzen
		if (is_array($images)) {
			foreach ($images as $key3=>$value3) {
				$img_basename=basename($value3);
				$img_basename=str_replace(" ","_",$img_basename);
				$img_basename=strtolower($img_basename);
				$value['post_content']=str_replace($value3,PAPOO_WEB_PFAD."/images/".$img_basename,$value['post_content']);
			}
		}
		$value['post_content']=str_replace('{$sname}',"",$value['post_content']);

		//$value['post_content']=preg_replace('/style="(.*?)"/',"",$value['post_content']);
		return $value;
	}


	/**
	 * blog_import_class::do_correct_links()
	 *
	 * @return void
	 */
	private function do_correct_links()
	{
		//zuerst mal alle rausholen
		$sql=sprintf("SELECT * FROM %s
			WHERE lang_id='%d'",
			$this->cms->tbname['papoo_language_article'],
			$_SESSION['data_import']['lang']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Dann die Links aus dem Text
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//text
				$text = $value['lan_article'];

				//Links rausholen
				preg_match_all('|<a [^>]+>(.*)</a>|Us', $text, $ausgabe, PREG_PATTERN_ORDER);

				if (is_array($ausgabe['0'])) {
					//Danach suchen
					foreach ($ausgabe['0'] as $key_l=>$value_l) {
						//Url alt rausholen
						$link=$value_l;
						$link_1=explode("href=\"",$link);
						$link_2=explode("\"",$link_1['1']);
						$url_alt=(str_replace("&amp;","&",$link_2['0']));
						$url_alt_org=($link_2['0']);
						$url_alt=str_replace($_SESSION['data_import']['url_page'],"",$url_alt);
						//Mit der aklten url die neue suchen
						$neu_url=$this->get_neu_url($url_alt);
						// erstezen
						if (!stristr($url_alt_org,"reporeid")) {
							$text=str_replace($url_alt_org,$neu_url,$text);
						}
					}
					//wieder speichern
					$sql=sprintf("UPDATE %s
						SET lan_article='%s',
						lan_article_sans='%s'
						WHERE lan_repore_id='%d'
						AND lang_id='%d'",
						$this->cms->tbname['papoo_language_article'],
						$this->db->escape($text),
						$this->db->escape($text),
						$value['lan_repore_id'],
						$_SESSION['data_import']['lang']
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * blog_import_class::get_neu_url()
	 *
	 * @param mixed $alte_url
	 * @return mixed|string
	 */
	private function get_neu_url($alte_url)
	{
		$sql=sprintf("SELECT * FROM %s
			WHERE doklesengrid LIKE '%s'",
			$this->cms->tbname['papoo_repore'],
			"%".$alte_url."%"
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$neu_url="./index.php?menuid=".$result['0']['cattextid']."&reporeid=".$result['0']['reporeID']."&rest=";

		if (stristr("mailto",$alte_url)) {
			return $alte_url;
		}

		if (empty($result)) {
			return "#";
		}
		return $neu_url;
	}

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
	 * blog_import_class::get_images()
	 *
	 * @param mixed $images
	 * @param mixed $title
	 * @return void
	 */
	private function get_images($images,$title)
	{
		if (is_array($images)) {
			foreach ($images as $key=>$value) {
				$remote_file=str_replace(" ","%20",$value);

				if (!stristr("http",$remote_file)) {
					$url =$remote_file;
				}
				else {
					$url = $_SESSION['blogger_import_url_der_installation3']."".$remote_file;
				}

				$url =$remote_file;
				$img=$this->http_request_open($url);

				$img_basename=basename($value);
				$img_basename=str_replace(" ","_",$img_basename);
				#$img_basename=strtolower($img_basename);
				//Dateinamen
				$name="/images/".$img_basename;
				$name2="/images/thumbs/".$img_basename;

				//Datei Speichern
				$this->diverse->write_to_file($name,$img);
				$this->diverse->write_to_file($name2,$img);

				//Breite und Höhe
				$image_info=getimagesize(PAPOO_ABS_PFAD."/images/".$img_basename);
				if (empty($image_info)) {
					continue;
				}
				$vorhanden_lang=0;

				$sql=sprintf("SELECT (image_id) FROM %s
					WHERE image_name='%s'",
					$this->cms->tbname['papoo_images'],
					basename($value)
				);
				$vorhanden_lang=$this->db->get_var($sql);
				//Noch nicht vorhanden...
				if ($vorhanden_lang==0) {
					//Datei in DB speichern
					$sql=sprintf("INSERT INTO %s SET
						image_name='%s',
						image_width ='%d',
						image_height ='%d'",
						$this->cms->tbname["papoo_images"],
						basename($value),
						$image_info['0'],
						$image_info['1']
					);
					$this->db->query($sql);
					$insert_id=$this->db->insert_id;

					$sql = sprintf("INSERT INTO %s SET
						lan_image_id='%s',
						lang_id ='%d',
						alt ='%s',
						title ='%s'
						",
						$this->cms->tbname["papoo_language_image"],
						$insert_id,
						$_SESSION['data_import']['lang'],
						$title,
						$title
					);
					$this->db->query($sql);
				}
			}
		}
	}


	/**
	 * blog_import_class::get_artikel_daten()
	 *
	 * @param mixed $db2
	 * @return
	 */
	private function get_artikel_daten($db2)
	{
		$db_prefix = $_SESSION['blogger_import_prfix'];

		$sql = "SELECT * FROM {$db_prefix}posts
			WHERE `post_type`
			LIKE '%post%'
			OR `post_type`
			LIKE '%page%';";

		return array_map(function($article) use ($db2, $db_prefix) {
			$sql = "SELECT post_id as menu_id FROM {$db_prefix}postmeta WHERE meta_value = {$article['ID']} AND meta_key = '_menu_item_object_id'";
			$article['menu_id'] = $db2->get_results($sql, ARRAY_A);

			$article['menu_id'] = (is_array($article['menu_id']) and !empty($article['menu_id'])) 
				? end(end($article['menu_id'])) 
				: null;

			return $article;
		}, $db2->get_results($sql, ARRAY_A));
	}


	/**
	 * blog_import_class::insert_kategorien()
	 *
	 * @param mixed $kategorien
	 * @return void
	 */
	private function insert_kategorien($kategorien)
	{
		if (!is_array($kategorien)) {
			return;
		}

		// Einträge in deutscher Sprache erzwingen
		$_SESSION['data_import']['lang'] = 1;

		if (empty($_SESSION['shop_max_menuid'])) {
			//Maximale Menuid rausholen
			$sql = sprintf("SELECT MAX(menuid) FROM %s",
				$this->cms->tbname['papoo_me_nu']
			);
			$_SESSION['shop_max_menuid'] = $this->db->get_var($sql);
		}

		$max_id = $_SESSION['shop_max_menuid'];
		$max_order_id = $max_id*10;

		//Dann jetzt schauen ob es Unterkategorien gibt
		//$this->get_sub_categorie();

		foreach ($kategorien as $key=>$value) {
			$kategorien[$key]['id']=$value['term_id'];
			$kategorien[$key]['parent_id']=$value['term_group'];
		}

		//Einträge durchgehen
		foreach ($kategorien as $key=>$value) {
			//Daten eintragen
			$insert_id=$this->insert_single_categorie($value,$max_id,$max_order_id, $value["parent_id"]);
			$this->kategorien_all[$value['id']]=$insert_id;
		}
	}

	/**
	 * blog_import_class::insert_single_categorie()
	 *
	 * @param mixed $value
	 * @param mixed $max_id
	 * @param mixed $max_order_id
	 * @param int $untermenuzu
	 * @param null $level
	 * @return mixed|null ???
	 */
	private function insert_single_categorie($value, $max_id, $max_order_id, $untermenuzu = 2, $level = null)
	{
		$untermenuzu = (int)$this->kategorien_all[$untermenuzu];

		if($level === null) {
			$level = $this->db->get_var("SELECT COALESCE(level, -1) + 1 FROM ".$this->cms->tbname['papoo_me_nu']." WHERE menuid = {$untermenuzu};");
		}

		//Unterordnung bzw. Verschachtelung behalten
		$menuid=$value['id']+$max_id;
		$vorhanden=0;
		//Zuerst checken ob der Menüpunkt schon vorhanden ist
		$sql=sprintf("SELECT (menuid) FROM %s
			WHERE lese_rechte='%d'
			AND untermenuzu='%d'",
			$this->cms->tbname['papoo_me_nu'],
			$value['id'],
			$untermenuzu
		);
		$vorhanden=$this->db->get_var($sql);
		//Eintrag noch nicht vorhanden - wurde noch nicht importiert
		if ($vorhanden<1) {
			//Dann die Menüpunkte dazu anlegen
			$sql=sprintf("INSERT INTO %s SET
				menuid='%d',
				menuname='%s',
				menulink='index.php',
				untermenuzu='%d',
				level='%d',
				order_id='%d',
				lese_rechte='%d'
				",
				$this->cms->tbname["papoo_me_nu"],
				$menuid,
				$value['name'],
				$untermenuzu,
				$level,
				$max_order_id,
				$value['id']
			);
			$this->db->query($sql);

			$insertid = $this->db->insert_id;
		}
		else {
			$insertid=$vorhanden;
		}

		//Url erzeugen und bereinigen
		$url=strtolower(str_replace(" ","-",$value['name']));
		$url = preg_replace("/[^a-z0-9\-]/", "", $url);
		$vorhanden_lang=0;
		if ($vorhanden>0) {
			//Checken ob schon in Sprachtabelle vorhanden
			$sql=sprintf("SELECT (menuid_id) FROM %s
				WHERE menuid_id='%d'
				AND lang_id='%d'",
				$this->cms->tbname['papoo_menu_language'],
				$vorhanden,
				$_SESSION['data_import']['lang']
			);
			$vorhanden_lang=$this->db->get_var($sql);
			if ($vorhanden_lang<1) {
				//Dann die Sprachtabelle
				$sql=sprintf("INSERT INTO %s SET
					menuid_id='%d',
					lang_id='%d',
					menuname='%s',
					menulinklang='index.php',
					url_menuname='%s'",
					$this->cms->tbname["papoo_menu_language"],
					$menuid,
					$_SESSION['data_import']['lang'],
					$value['name'],
					$url."-".$menuid
				);
				$this->db->query($sql);
			}
		}
		else {
			//Dann die Sprachtabelle
			$sql=sprintf("INSERT INTO %s SET
				menuid_id='%d',
				lang_id='%d',
				menuname='%s',
				menulinklang='index.php',
				url_menuname='%s'",
				$this->cms->tbname["papoo_menu_language"],
				$menuid,
				$_SESSION['data_import']['lang'],
				$value['name'],
				$url."-".$menuid
			);
			$this->db->query($sql);
		}
		if ($vorhanden<1) {
			//DAnn die Lookup Tabelle Rechte JEder
			$sql=sprintf("INSERT INTO %s SET
				menuid_id='%d',
				gruppeid_id='10'",
				$this->cms->tbname["papoo_lookup_me_all_ext"],
				$menuid
			);
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
		}
		//Wenn Status aktiv Dann Frontend Rechte einstellen
		return $insertid;
	}

	/**
	 * blog_import_class::import_kategorien()
	 *
	 * @return void
	 */
	private function import_kategorien()
	{
		//Erstemal Verbindung zur DB aufbauen mit den geprüften Daten
		$db2=new ezSQL_mysqli(
			$_SESSION['blogger_import_db_user'],
			$_SESSION['blogger_import_db_passwort'],
			$_SESSION['blogger_import_db_name'],
			$_SESSION['blogger_import_db_server'],
			true
		);

		//Dann die Gruppendaten raussuchen
		$kategorien = $this->get_kategorien_daten($db2);

		//Dann diese einfügen
		$this->insert_kategorien($kategorien);

		$_SESSION['shop_fertig_import']['kategorien'] = 1;
	}


	/**
	 * blog_import_class::get_kategorien_daten()
	 *
	 * @return array
	 * @param ezSQL_mysqli $db2
	 */
	private function get_kategorien_daten($db2)
	{
		$sql = sprintf("SELECT * FROM " . $_SESSION['blogger_import_prfix'] . "terms GROUP BY name");

		$kategorien = $db2->get_results($sql, ARRAY_A);

		return $kategorien;
	}


	/**
	 * aktivierung_check_plugin_class::reload()
	 *
	 * @param string [$template = ""]
	 * @param string [$id = ""]
	 * @return void
	 */
	function reload($template = "", $id = "")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$template."&vorlage=new&akquise_vorlage_id=".$id;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}
$wp_importer = new class_wp_importer;
