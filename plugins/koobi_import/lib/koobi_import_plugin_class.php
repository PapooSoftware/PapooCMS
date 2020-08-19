<?php

/**
 * Class koobi_import_plugin_class
 */
class koobi_import_plugin_class
{
	/**
	 * koobi_import_plugin_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $diverse, $cms, $db_abs;
		$this->content = $content;
		$this->db = $db;
		$this->checked = $checked;
		$this->user = $user;
		$this->diverse = $diverse;
		$this->cms = $cms;
		$this->db_abs = $db_abs;

		//Nur in der Admin
		if ( defined("admin") ) {
			// Überprüfen ob der User Zugriff haben darf
			$this->user->check_intern();

			/**
			 * Das aktuelle Template auslesen, anhand dessen kann sichergestellt werden
			 * dass man im richtigen Plugin ist
			 */
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			// Korrekt eingeloggt
			if ($template != "login.utf8.html") {
				// und auch noch das richtige Template
				if (isset($this->checked->form_import_step) && stristr($template2,"koobi_import")) {
					switch ($this->checked->form_import_step) {
						// Auf CSV Import stellen
					case "db_eintragen":
						$this->import_step1();
						break;

					case "import_step2":
						$this->import_step2();
						break;

						// Auf XTC Import stellen
					case "import_step3":
						$this->import_step3();
						break;

					default;
						$this->import_step1();
						break;
					}
				}
			}
		}
	}

	/**
	 * koobi_import_plugin_class::import_step3()
	 *
	 * @return void
	 */
	function import_step3()
	{

	}

	function import_step2()
	{
		$this->content->template['import_act'] = "import_step4";

		$db2=new db_class($_SESSION['data_import']['dbuser'],$_SESSION['data_import']['dbpasswort'],$_SESSION['data_import']['dbname'],$_SESSION['data_import']['dbserver'],true);
		$gett= "SHOW TABLES";;
		$tables=$db2->get_results($gett,ARRAY_A);

		if (is_array($tables)) {
			foreach ($tables as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						if (!empty($value2)) {
							if (stristr($value2,"activation")) {
								$praefix_array=explode("_",$value2);
								$this->extern_praefix=$praefix_array['0'];
							};
						}
					}
				}
			}
		}
		//Die einzelnen Imports durchf�hren

		//Wenn Gruppen, dann die zuerst um die User sp�ter zuordnen zu k�nnen
		if ($_SESSION['shop_fertig_import']['gruppen']!=1) {
			//$this->import_xtc_gruppen();
		}

		//Dann die User importieren
		if ($_SESSION['shop_fertig_import']['kunden']!=1) {
			//$this->import_xtc_kunden();
		}
		//Nun zuerst die Kategorien um die Zuordnung zu erhalten
		//if ($_SESSION['shop_fertig_import']['kategorien']!=1)
		{
			$this->import_kategorien();
		}
		//Dann die Produkte mit den Bildern && $_SESSION['shop_fertig_import']['produkte']!=1
		//if ($_SESSION['shop_fertig_import']['artikel']!=1)
		{
			$this->import_artikel();
		}

		//Crosselling (noch offen)
		if ($_SESSION['shop_xtc_import']['cross']==1 && $_SESSION['shop_fertig_import']['cross']!=1) {
			#$this->import_xtc_cross();
		}

		//die Belege importieren
		if ($_SESSION['shop_xtc_import']['belege']==1) {
			//$this->import_xtc_belege();
		}

		//Bewertungen (noch offen)
		if ($_SESSION['shop_xtc_import']['bewert']==1) {

		}
		//Timestamp der Belege resetten
		//$this->reset_time_stamp();

		$this->reload("step3");
	}


	/**
	 * koobi_import_plugin_class::import_artikel()
	 *
	 * @return void
	 */
	function import_artikel()
	{
		//Erstemal Verbindung zur DB aufbauen mit den gepr�ften Daten
		$db2=new db_class($_SESSION['data_import']['dbuser'],$_SESSION['data_import']['dbpasswort'],$_SESSION['data_import']['dbname'],$_SESSION['data_import']['dbserver'],true);

		//Dann die Gruppendaten raussuchen
		$artikel=$this->get_artikel_daten($db2);

		//Dann diese einf�gen
		$this->insert_artikel($artikel);

		//Dann die Links korrigieren
		$this->do_correct_links();

		$_SESSION['shop_fertig_import']['artikel']=1;
	}

	/**
	 * koobi_import_plugin_class::do_correct_links()
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
				$text=$value['lan_article'];

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
	 * @param $alte_url
	 * @return string
	 */
	function get_neu_url($alte_url)
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
	 * koobi_import_plugin_class::get_artikel_daten()
	 *
	 * @param mixed $db2
	 * @return mixed
	 */
	function get_artikel_daten($db2)
	{
		$sql=sprintf("SELECT * FROM ".$this->extern_praefix."_static");

		$artikel=$db2->get_results($sql,ARRAY_A);

		return $artikel;
	}

	/**
	 * koobi_import_plugin_class::insert_artikel()
	 *
	 * @param $artikel
	 * @return void
	 */
	function insert_artikel($artikel)
	{
		if (is_array($artikel)) {
			//Artikel durchlaufen und schauen zu welchem Men�punkte er geh�rt
			foreach ($artikel as $key=>$value) {
				$artikel_menuid=0;
				$anteasern=0;
				if (is_array($value)) {
					//Alle Eintr�ge durchgehen und utf8 Kodieren
					foreach ($value as $key2=>$value2)
					{
						$value[$key2]=utf8_encode($value2);
					}

					//Dann schauen welcher Men�punkt
					if (is_array($this->subcat_daten)) {
						foreach ($this->subcat_daten as $key_sub=>$value_sub) {
							//Wenn der Name in der Url der Subkats vorkommt, dann zu diesem Men�punkt sortieren
							if (stristr($value_sub['document'],$value['pagename'])) {
								$artikel_menuid=$value_sub['menuid'];
							}
						}
					}

					//Kam nicht drin vor
					if (($artikel_menuid==0)) {
						//Dann die Area ID zuweisen $this->kategorien_all
						$artikel_menuid=$this->kategorien_all[$value['area']];
						$anteasern=1;
					}

					//Dann die passenden Artikel erstellen
					$this->create_singel_article($artikel_menuid,$value,$anteasern);
				}
			}
		}
	}

	/**
	 * koobi_import_plugin_class::create_singel_article()
	 *
	 * @param mixed $menuid
	 * @param $value
	 * @param int $anteasern
	 * @return void
	 */
	function create_singel_article($menuid,$value,$anteasern=0)
	{
		//zuerst mal den Basiseintrage erstellen
		//$menuid=$value['id']+$max_id;
		$vorhanden=0;


		//Zuerst checken ob der Artikel schon vorhanden ist
		$sql=sprintf("SELECT (reporeID) FROM %s
									WHERE dokschreibengrid='%d'",
			$this->cms->tbname['papoo_repore'],
			$value['id']
		);
		$vorhanden=$this->db->get_var($sql);

		$this->order_id++;
		//Eintrag noch nicht vorhanden - wurde noch nicht importiert
		if ($vorhanden<1) {
			$alte_url="index.php?area=".$value['area']."&p=static&page=".$value['pagename'];
			//Dann den Artikel dazu anlegen
			$sql=sprintf("INSERT INTO %s SET
										dokuser 	='10',
										dokschreibengrid='%d',
										doklesengrid='%s',
										timestamp='%s',
										erstellungsdatum='%s',
										pub_dauerhaft='1',
										publish_yn='1',
										teaser_list ='0',
										allow_publish='1',
										order_id='%d',
										stamptime='%d',
										dokuser_last ='10',
										count='%d',
										dok_show_teaser_teaser='%d',
										cattextid='%d'

										",
				$this->cms->tbname["papoo_repore"],
				$value['id'],
				$alte_url,
				date("Y-m-d h:i:s"),
				date("Y-m-d h:i:s"),
				$this->order_id,
				$value['ctime'],
				$value['hits'],
				$anteasern,
				$menuid
			);
			$this->db->query($sql);
			$max_order_id++;
			$insertid=$this->db->insert_id;
		}
		else {
			$insertid=$vorhanden;
		}

		//Dann den Spracheintrag
		$url=strtolower(str_replace(" ","-",$value['pagename']));
		$url = preg_replace("/[^a-z0-9\-]/", "", $url);
		$vorhanden_lang=0;

		//Sprachdaten bereinigen
		$value=$this->make_correct_sprachdata($value);
		$value['content']=utf8_encode(html_entity_decode($value['content']));
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
											url_header  ='%s'",
					$this->cms->tbname["papoo_language_article"],
					$insertid,
					$_SESSION['data_import']['lang'],
					$value['title'],
					$this->db->escape(strip_tags($value['content']),0,400),
					$this->db->escape($value['content']),
					$this->db->escape($value['content']),
					$value['title'],
					$this->db->escape(substr(strip_tags($value['content']),0,140)),
					$url
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
											url_header  ='%s'",
				$this->cms->tbname["papoo_language_article"],
				$insertid,
				$_SESSION['data_import']['lang'],
				$value['title'],
				$this->db->escape(strip_tags($value['content']),0,400),
				$this->db->escape($value['content']),
				$this->db->escape($value['content']),
				$value['title'],
				$this->db->escape(substr(strip_tags($value['content']),0,140)),
				$url
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

	/**
	 * koobi_import_plugin_class::make_correct_sprachdata()
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	function make_correct_sprachdata($value)
	{
		$content=$value['content'];
		$value['content']=str_replace("\n","",$value['content']);
		$value['content']=str_replace("\r","",$value['content']);
		//Bild daten rausholen pro Text
		preg_match_all( '/<img[^>]*>/Ui', $value['content'], $img );

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

				$value['content']=str_replace($value3,PAPOO_WEB_PFAD."/images/".$img_basename,$value['content']);
			}
		}

		//Settings rausholen
		if (empty($this->settings_daten)) {
			$this->get_settings();
		}
		else {
			if (is_array($this->settings_daten['0'])) {
				foreach ($this->settings_daten['0'] as $key4=>$value4) {
					$value['content']=str_replace('{$'.$key4."}",$value4,$value['content']);
				}
			}
		}
		$value['content']=str_replace('{$sname}',"",$value['content']);

		//$value['content']=preg_replace('/style="(.*?)"/',"",$value['content']);
		return $value;
	}

	/**
	 * koobi_import_plugin_class::get_images()
	 *
	 * @param mixed $images
	 * @param $title
	 * @return void
	 */
	function get_images($images,$title)
	{
		if (is_array($images)) {
			foreach ($images as $key=>$value) {
				$remote_file=str_replace(" ","%20",$value);
				$url = $_SESSION['data_import']['url_page']."".$remote_file;

				$img=$this->http_request_open($url);
				$img_basename=basename($value);
				$img_basename=str_replace(" ","_",$img_basename);
				$img_basename=strtolower($img_basename);
				//Dateinamen
				$name="/images/".$img_basename;
				$name2="/images/thumbs/".$img_basename;

				//Datei Speichern
				$this->diverse->write_to_file($name,$img);
				$this->diverse->write_to_file($name2,$img);

				//Breite und H�he
				$image_info=getimagesize(PAPOO_ABS_PFAD."/images/".$img_basename);
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

					$sql=sprintf("INSERT INTO %s SET
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
	 * koobi_import_plugin_class::http_request_open()
	 *
	 * @param mixed $url
	 * @param integer $timeout
	 * @return string
	 */
	function http_request_open($url,$timeout=10)
	{
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		##curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );

		$curl_ret = curl_exec( $ch );
		curl_close( $ch );
		return trim($curl_ret);
	}

	/**
	 * koobi_import_plugin_class::import_kategorien()
	 *
	 * @return void
	 */
	function import_kategorien()
	{
		//Erstemal Verbindung zur DB aufbauen mit den gepr�ften Daten
		$db2=new db_class($_SESSION['data_import']['dbuser'],$_SESSION['data_import']['dbpasswort'],$_SESSION['data_import']['dbname'],$_SESSION['data_import']['dbserver'],true);

		//Dann die Gruppendaten raussuchen
		$kategorien=$this->get_kategorien_daten($db2);

		//Dann diese einf�gen
		$this->insert_kategorien($kategorien);

		$_SESSION['shop_fertig_import']['kategorien']=1;
	}

	/**
	 * koobi_import_plugin_class::get_settings()
	 *
	 * @return void
	 */
	function get_settings()
	{
		//Erstemal Verbindung zur DB aufbauen mit den gepr�ften Daten
		$db2=new db_class($_SESSION['data_import']['dbuser'],$_SESSION['data_import']['dbpasswort'],$_SESSION['data_import']['dbname'],$_SESSION['data_import']['dbserver'],true);

		//Dann die Gruppendaten raussuchen
		$this->get_settings_daten($db2);

		$_SESSION['shop_fertig_import']['kategorien']=1;
	}

	/**
	 * koobi_import_plugin_class::get_settings_daten()
	 *
	 * @param $db2
	 * @return void
	 */
	function get_settings_daten($db2)
	{
		$sql=sprintf("SELECT * FROM ".$this->extern_praefix."_settings");

		$this->settings_daten=$db2->get_results($sql,ARRAY_A);
	}

	/**
	 * koobi_import_plugin_class::get_kategorien_daten()
	 *
	 * @param mixed $db2
	 * @return mixed
	 */
	function get_kategorien_daten($db2)
	{
		$sql=sprintf("SELECT * FROM ".$this->extern_praefix."_areas");

		$kategorien=$db2->get_results($sql,ARRAY_A);

		return $kategorien;
	}

	/**
	 * koobi_import_plugin_class::insert_kategorien()
	 *
	 * @param mixed $kategorien
	 * @return void
	 */
	function insert_kategorien($kategorien)
	{
		if (is_array($kategorien)) {

			if ($_SESSION['data_import']['lang']<1) {
				$_SESSION['data_import']['lang']=1;
			}

			if (empty($_SESSION['shop_max_menuid'])) {
				//Maximale Menuid rausholen
				$sql=sprintf("SELECT MAX(menuid) FROM %s",
					$this->cms->tbname['papoo_me_nu']
				);
				$_SESSION['shop_max_menuid']=$this->db->get_var($sql);
			}
			$max_id=$_SESSION['shop_max_menuid'];
			$max_order_id=$max_id*10;

			//Dann jetzt schauen ob es Unterkategorien gibt
			$this->get_sub_categorie();

			if (is_array($kategorien)) {
				foreach ($kategorien as $key=>$value) {
					$kategorien[$key]['id']=$value['area_id'];
					$kategorien[$key]['name']=$value['area_name'];
				}
			}

			//Eintr�ge durchgehen
			foreach ($kategorien as $key=>$value) {
				if (is_array($value)) {
					//Alle Eintr�ge durchgehen und utf8 Kodieren
					foreach ($value as $key2=>$value2) {
						$value[$key2]=utf8_encode($value2);
					}
				}
				//Daten eintragen
				$insert_id=$this->insert_single_categorie($value,$max_id,$max_order_id);

				$kategorien_neu[$value['id']]=$insert_id;
			}
			$this->kategorien_all=$kategorien_neu;

			$max_id=$max_id+ end($kategorien_neu); //

			//So - jetzt die Unterpunkte aus der Navi Tabelle eintragen
			if (is_array($this->subcat_daten)) {
				foreach ($this->subcat_daten as $key=>$value) {
					//Name des Men�punktes �bergeben
					$value['name']=$value['title'];
					$this->subcat_daten[$key]['name']=$value['title'];

					//Den Level setzen , geht erstmal nur bis Ebene 2... bei h�heren analysieren
					if ($value['parent_id']==0) {
						$level=1;
					}
					else {
						$level=2;
					}
					//Daten �bergehen
					$this->subcat_daten[$key]['level']=$level;

					//Men�punkte erstellen
					$insert_id=$this->insert_single_categorie($value,$max_id,$max_order_id,$kategorien_neu[$value['area']],$level);

					$this->subcat_daten[$key]['menuid']=$insert_id;
				}
			}
		}
	}

	/**
	 * koobi_import_plugin_class::get_sub_categorie()
	 *
	 * @return void
	 */
	function get_sub_categorie()
	{
		//Erstemal Verbindung zur DB aufbauen mit den gepr�ften Daten
		$db2=new db_class($_SESSION['data_import']['dbuser'],$_SESSION['data_import']['dbpasswort'],$_SESSION['data_import']['dbname'],$_SESSION['data_import']['dbserver'],true);

		//Dann die Gruppendaten raussuchen
		$this->get_subcat_daten($db2);
	}

	/**
	 * koobi_import_plugin_class::get_settings_daten()
	 *
	 * @param $db2
	 * @return void
	 */
	function get_subcat_daten($db2)
	{
		$sql=sprintf("SELECT * FROM ".$this->extern_praefix."_navi");

		$this->subcat_daten=$db2->get_results($sql,ARRAY_A);
	}

	/**
	 * koobi_import_plugin_class::insert_single_categorie()
	 *
	 * @param mixed $value
	 * @param mixed $max_id
	 * @param mixed $max_order_id
	 * @param integer $untermenuzu
	 * @param integer $level
	 * @return void
	 */
	function insert_single_categorie($value,$max_id,$max_order_id,$untermenuzu=0,$level=0)
	{
		//Unterordnung bzw. Verschachtelung behalten
		$menuid=$value['id']+$max_id;
		$vorhanden=0;
		//Zuerst checken ob der Men�punkt schon vorhanden ist
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
			//Dann die Men�punkte dazu anlegen
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
			$max_order_id++;
			$insertid=$this->db->insert_id;
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
			/**
			 * }
			 * else
			 * {
			 * 	//DAnn die Lookup Tabelle Rechte NUr Admin
			 * 	$sql=sprintf("INSERT INTO %s SET
			 * 								menuid_id='%d',
			 * 								gruppeid_id='1'",
			 * 	$this->cms->tbname["papoo_lookup_me_all_ext"],
			 * 	$menuid
			 * 	);
			 * 	$this->db->query($sql);
			 * }
			 */

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
		#if ($value['categories_status']==1)
		#	{
		return $insertid;
	}

	/**
	 * koobi_import_plugin_class::show_data()
	 * Daten rausholen f�r die Anzeige der Links
	 * @return void
	 */
	function import_step1()
	{
		//Daten zur�ckgeben
		$this->gib_var_zuruech_an_template();
		//unset($_SESSION['fertig_import']);
		//unset($_SESSION['data_import']);
		//unset($_SESSION['data_max_menuid']);

		if (!empty($this->checked->formSubmit_data_settings)) {
			if (empty($this->checked->redirectkoobi_plugin_benutzername) ||
				//empty($this->checked->redirectkoobi_plugin_passwort)  ||
				empty($this->checked->redirectkoobi_plugin_servername) ||
				empty($this->checked->redirectkoobi_plugin_datenbankname)) {
				$this->content->template['data_error']['import']['alle_felder']="NO";
			}
			else {
				//Aus den Daten aus dem Formular die Verbindung checken
				if (!$this->check_verbindung()) {
					$this->content->template['data_error']['import']['no_verbindung']="NO";
				}
				else {
					$this->reload("step2");
				}
			}
		}
	}

	/**
	 * shop_class_import_xtc::gib_var_zuruech_an_template()
	 *
	 * @return void
	 */
	function gib_var_zuruech_an_template()
	{
		//Sprachdaten
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$options="";
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (isset($this->checked->redirectkoobi_plugin_sprache_auswhlen) && $this->checked->redirectkoobi_plugin_sprache_auswhlen==$value['lang_id']) {
					$options.='<option selected="selected" value="'.$value['lang_id'].'">'.$value['lang_long'].'</option>';
				}
				else {
					$options.='<option value="'.$value['lang_id'].'">'.$value['lang_long'].'</option>';
				}
			}
		}
		$this->content->template['options_koobi_import_lang']=$options;
		//options_koobi_import_lang
		foreach ($this->checked as $key=>$value) {
			$this->content->template['redirectkoobi_plugin']['0'][$key]=$value;
		}
	}

	/**
	 * shop_class_import_xtc::check_verbindung()
	 * �berpr�ft die Verbindung mit einer externen Datenbank
	 *
	 * @return bool
	 */
	function check_verbindung()
	{
		//Kurze vars draus machen
		$dbuser=$this->checked->redirectkoobi_plugin_benutzername;
		$dbpasswort=$this->checked->redirectkoobi_plugin_passwort;
		$dbname=$this->checked->redirectkoobi_plugin_datenbankname;
		$dbserver=$this->checked->redirectkoobi_plugin_servername;
		//Verbindung herstellen
		$db=@mysqli_connect($dbserver,$dbuser,$dbpasswort);
		if ($db) {
			//Wenn keine Verbindung zur DB hergestellt werden kann, false
			if ( @mysqli_select_db($dbname,$db)) {
				//Zugangsdaten an Session �bergeben
				$_SESSION['data_import']['dbuser']=$dbuser;
				$_SESSION['data_import']['dbpasswort']=$dbpasswort;
				$_SESSION['data_import']['dbname']=$dbname;
				$_SESSION['data_import']['dbserver']=$dbserver;
				$_SESSION['data_import']['bilder_server']=$this->checked->shop_imexnow_erver_inkl_fad_zu_den_ildern;
				$_SESSION['data_import']['lang']=$this->checked->redirectkoobi_plugin_sprache_auswhlen;
				$_SESSION['data_import']['url_page']=$this->checked->redirectkoobi_plugin_url_der_seite_data;
				//OK
				return true;
			}
			else {
				return false;
			}
		}
		return false;
	}


	/**
	 * koobi_import_plugin_class::save_settings()
	 *
	 * @return void
	 */
	function save_settings()
	{
		/**
		 * Jetzt die Daten noch in der Datei speichern
		 * Nur dann kann im FE auch darauf zugegriffen werden
		 */
		try {
			$this->diverse->write_to_file("/templates_c/redirect.csv",$this->checked->koobi_import_plugin_redirect_liste);
		}
		catch (Exception $e) {
			$this->content->template['redirect_error']="Die Datei konnte nicht erstellt werden. Bitte �ndern Sie die Schreibrechte des Verzeichnisses /templates_c auf 777.";
		}
	}

	/**
	 * shop_class_import_csv::reload()
	 *
	 * @param string $key
	 * @return void
	 */
	function reload( $key = "" )
	{
		if ( $key == "step2" ) {
			$var1 = "&template=koobi_import/templates/koobi_import_back_step2.html&import=true&form_import_step=2";
		}

		if ( $key == "step3" ) {
			$var1 = "&template=koobi_import/templates/koobi_import_back_step3.html&import=true&form_import_step=3";
		}

		if ( $key == "step4" ) {
			$var1 = "&import=koobishop&form_import_xtc_step=koobi_step4";
		}

		//selected_kunde
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
			"" . $var1 . "&message=" . $message;
		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}
}

$koobi_import_plugin = new koobi_import_plugin_class();
