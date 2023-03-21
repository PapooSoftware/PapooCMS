<?php

/**
 * #####################################
 * # Papoo CMS                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.3                  #
 * #####################################
 */

/**
 * Die Easyedit Klasse
 * Damit kann man im Frontend ganz einfach Artikel bearbeiten
 * und neue erstellen
 *
 * Class easyedit
 */
#[AllowDynamicProperties]
class easyedit
{
	/**
	 * easyedit constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren

		global $cms, $db, $message, $user, $menu, $content, $searcher, $checked, $replace,
			   $db_praefix, $intern_stamm, $intern_artikel, $intern_image, $diverse, $article, $html;

		// und einbinden in die Klasse
		// Hier die Klassen als Referenzen
		$this->article = &$article;
		$this->intern_image = &$intern_image;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->menu = &$menu;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->intern_stamm = &$intern_stamm;
		$this->replace = &$replace;
		$this->db_praefix = &$db_praefix;
		$this->diverse = &$diverse;
		$this->intern_artikel = &$intern_artikel;
		$this->html = &$html;
		$this->make_easyedit();
		$this->content->template['plugin_message'] = "";

		if(!isset($this->content->template['plugin']['easyedit'])) {
			$this->content->template['plugin']['easyedit'] = NULL;
		}
		if(!isset($this->content->template['easyedit'])) {
			$this->content->template['easyedit'] = NULL;
		}
		if(!isset($this->content->template['plugin']['easyeditok'])) {
			$this->content->template['plugin']['easyeditok'] = NULL;
		}
		if(!isset($this->content->template['easyeditok'])) {
			$this->content->template['easyeditok'] = NULL;
		}

		$this->post_papoo();
	}

	/**
	 * Interne Verwaltung
	 */
	function make_easyedit()
	{
		global $template;
		$this->klasse = "make_easyedit()";
		if (defined("admin")) {
			// echo $template;
			$this->user->check_intern();
			$templatedat = basename( $template );
			switch ($templatedat) {
				// Die Standardeinstellungen werden bearbeitet
			case "easyedit.html" :
				// $this->check_pref();
				break;
				// Ein Banner wird eingebunden
			case "easyedit_create.html" :
				$this->easy_set_menu();
				break;
				// Ein Banner wird eingebunden
			case "easyedit_text.html" :
				$this->easy_set_group();
				break;
				// Einen Dump erstellen oder einspielen
			case "easyedit_dump.html" :
				$this->easy_dump();
				break;

			default:
				break;
			}
		}
	}

	/**
	 * Was im Frontend passiert
	 */
	function post_papoo()
	{
		if(!isset($this->content->template['easyeditok'])) {
			$this->content->template['easyeditok'] = NULL;
		}

		if ((!defined("admin"))) {
			$this->content->template['is_easy']="ok";
			// Checken ob der User einer Gruppe angeh�rt die easy editieren darf
			$sql = sprintf( "SELECT * FROM %s AS t1,%s AS t2,%s AS t3
							WHERE t2.userid='%s'
							AND t2.userid=t3.userid
							AND t3.gruppenid=easy_group_id",
				$this->cms->tbname['papoo_easyedit_gruppe'],
				$this->cms->tbname['papoo_user'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->db->escape( $this->user->userid )
			);
			$grpi = $this->db->get_results( $sql, ARRAY_A );
			// Wenn ja, dann Template setzen
			if (!empty($grpi)) {
				foreach ( $grpi as $gr ) {
					if ( $gr['easy_geasy_ok'] == 1 ) {
						// Immer checken ob f�r den Men�punkt Easyedit aktiv ist
						$sql1 = sprintf( "SELECT easy_easy_ok FROM %s WHERE easy_men_id='%d'
									AND easy_easy_ok='%d'",
							$this->cms->tbname['papoo_easyedit_daten'],
							$this->db->escape( $this->checked->menuid ),
							$this->db->escape($gr['easy_group_id'])
						);
						$meni = $this->db->get_var( $sql1 );

						//Wenn eine Gruppe ok ist und nicht jeder ist ok setzen
						if ( ( !empty( $meni ) && $gr['easy_group_id'] != 10 ) ) {
							$grok = 1;
						}
					}
				}
			}

			//Es darf editiert werden
			if ($grok == "1") {
				$this->content->template['easyeditok'] = "ok";

				if ((!empty( $this->checked->uploadimg ))) {
					// Bild hochladen
					$this->easy_upload_img();
					// Neu laden und POST Daten neu �bergeben
					$this->do_it_again();
				}
				$this->content->template['easyedit'] = "";
				// Easy editieren
				if ($this->checked->edit == "easy") {
					$this->content->template['tinymce_lang_id'] = $this->cms->lang_id;
					// Speichern
					$this->easy_save_article();
					// Nochmal �berpr�fen ob editieren ok
					// Wenn ja, dann Edit setzen
					$this->content->template['easyedit'] = "edit";
					$this->content->template['easyedit2'] = "easy";
					// Artikel bearbeiten
					$this->manage_article();
				}
				// Neu Easy editieren
				if ($this->checked->edit == "new") {
					$this->content->template['tinymce_lang_id'] = $this->cms->lang_id;
					// Wenn ja, dann Edit setzen
					$this->content->template['easyedit'] = "edit";
					$this->content->template['easyedit2'] = "new";
					// Artikel bearbeiten
					$this->manage_new_article();
				}
			}
		}

		if(!isset($this->content->template['easyeditok'])) {
			$this->content->template['easyeditok'] = NULL;
		}
	}

	/**
	 * POST Date erneut �bergeben
	 */
	function do_it_again()
	{

		// Alle Post Daten durchgehen und neu �bergeben
		foreach( $_POST as $key => $value ) {
			$value=stripslashes($value);
			$this->content->template['easyc']['0'][$key] = "nodecode:".$value;
		}
		//reporeid
		$this->content->template['easyc']['0']['reporeID'] = "nodecode:".$this->checked->reporeid;
		$this->done="ok";
	}

	/**
	 * EIn Bild hochladen
	 */
	function easy_upload_img()
	{
		$this->intern_image->upload_picture();
		$this->content->template['easyfehlerimg'] = $this->content->template['text'];
		$this->checked->image_name = $this->content->template['image_name'];
		$this->checked->image_breite = $this->content->template['image_breite'];
		$this->checked->image_hoehe = $this->content->template['image_hoehe'];
		$this->checked->gruppe = $this->content->template['image_gruppe'];
		$this->checked->texte[1]['lang_id'] = $this->cms->lang_id;
		$this->checked->texte[1]['alt'] = $this->checked->alt;
		$this->checked->texte[1]['title'] = $this->checked->alt;
		$this->checked->texte[1]['long_desc'] = '';
		$this->checked->image_dir = $this->checked->image_dir;

		if (!empty($this->checked->image_name)) {
			$this->intern_image->upload_save( 'no' );
			$this->content->template['easyfehlerimg'] = $this->content->template['plugin']['easyedit']['eingetragen'];
		}
		return true;
	}

	/**
	 * Einen neuen Artikel erstellen
	 */
	function manage_new_article()
	{
		// einen neuen Artikel erstellen
		if (!empty($this->checked->easysave)) {
			// Neu eintragen mit Standards
			$this->easy_save_new();
		}
		// Biler liste raussuchen
		$this->get_bilder_liste();
	}

	/**
	 * Neuen Artikel speichern
	 * ZUm einen in der Tabelle repore
	 * Dann in den lookup Tabellen read/write
	 * Und in der Sprachtabelle
	 *
	 * @return void
	 */
	function easy_save_new()
	{
		// Zuerst die Links ersetzen
		$this->easy_replace_links();
		// automatisch listen?
		$teaserlist = 0;
		if ($this->cms->artikel_yn == 1) {
			$teaserlist = 1;
		}
		if ($this->checked->menuid == 1) {
			$teaserlist = 1;
		}

		$this->timestamp = date("j.n.Y  G:i:s");
		// Tabelle repore speichern
		$sql = sprintf( "INSERT INTO  %s SET
			teaser_bild='%s',
			teaser_bild_lr='%s',
			dokuser='%d',
			stamptime='%d',
			cattextid='%d',
			pub_verfall_page='0',
			pub_start_page='0',
			pub_dauerhaft='1',
			publish_yn='1',
			allow_publish='1',
			teaser_list='%d',
			timestamp='%s',
			erstellungsdatum=NOW(),
			teaser_bild_html='%s'",
			$this->cms->tbname['papoo_repore'],
			$this->db->escape( $this->checked->teaser_bild ),
			$this->db->escape( $this->checked->teaser_bild_lr ),
			$this->user->userid,
			time(),
			$this->db->escape( $this->checked->menuid ),
			$this->db->escape($teaserlist),
			$this->db->escape($this->timestamp),
			$this->db->escape( $this->teaserbild_in )
		);
		$this->db->query( $sql );
		$insertid = $this->db->insert_id;
		// Sprachdaten speichern
		// Daten ersetzen
		$artikel = $this->replace->do_replace( $this->checked->lan_article_sans );
		$artikel = str_ireplace( "ppmenuidpp", $this->checked->menuid, $artikel );
		// HTML bereinigen
		$artikel = $this->html->do_tidy( $artikel );
		$this->checked->lan_article_sans = str_ireplace( "\./image", "../image", $this->checked->lan_article_sans );
		$url_header = $this->menu->replace_uml( strtolower( $this->checked->header ) );


		if ($this->cms->mod_free==1) {
			if (empty($url_header)) {
				$url_header="/".substr($this->checked->header,0,60).".html";
			}
			if (!stristr($url_header,".html") && !stristr($url_header,".shtml") && $url_header[strlen($url_header)-1] !="/") {
				//auskommentieren wenn mit .html am Ende gew�nscht bei Artikeln
				if ($this->cms->artikel_url_mit_html) {
					$url_header=$url_header.".html";
				}
				else {
					$url_header=$url_header.".html";
				}
			}
			if ($url_header[0] !="/") {
				$url_header="/".$url_header;
			}
		}
		else {
			if (empty($url_header)) {
				$url_header=substr($this->checked->header ,0,60);
			}
		}

		$url_header=$this->check_url_header($url_header,$this->cms->lang_id);

		$sql = sprintf( "INSERT INTO %s SET
					header='%s',
					lan_teaser='%s',
					lan_article_sans='%s',
					lan_article='%s',
					url_header='%s',
					lan_repore_id='%d',
					lang_id='%d', ".
			"publish_yn_lang = 1",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape( $this->checked->header ),
			$this->db->escape( $this->checked->lan_teaser ),
			$this->db->escape( $this->checked->lan_article_sans ),
			$this->db->escape( $artikel ),
			$this->db->escape( $url_header ),
			$this->db->escape( $insertid ),
			$this->cms->lang_id
		);
		// $sql;
		$this->db->query( $sql );
		// Neu sortieren
		$this->artikel_reorder( $this->checked->menuid );
		// Lookup Tabelle read
		$this->db->hide_errors();
		$dat['gruppe_write'] = array( "1", "11", "10" );
		foreach ( $dat['gruppe_write'] as $insert ) {
			$sqlin = "INSERT INTO " . $this->cms->papoo_lookup_article . " SET article_id='$insertid', gruppeid_id='$insert' ";
			$this->db->query( $sqlin );
		}
		// Lookup Tabelle write
		// Adminstratoren d�rfen IMMER schreibend auf Artikel zugreifen
		$sess_dat['write_article'] = array( "1", "11" );
		// print_r( $sess_dat['write_article'] );
		foreach ( $sess_dat['write_article'] as $insert ) {
			$sqlin = "INSERT INTO " . $this->cms->tbname['papoo_lookup_write_article'] . " SET article_wid_id='$insertid', gruppeid_wid_id='$insert' ";
			$this->db->query( $sqlin );
		}

		//Lookup Daten f�r die Men�punkte l�schen.
		$sql = "DELETE FROM 
		".$this->cms->tbname['papoo_version_lookup_art_cat']." 
		WHERE lart_id='".$this->db->escape($insertid_vers)."'";
		//$this->db->query($sql);
		//Lookup Daten f�r die Men�punkte eintragen.
		$sqlin = "INSERT INTO ".$this->cms->tbname['papoo_lookup_art_cat'].
			" SET lart_id='$insertid', lcat_id='".$this->db->escape( $this->checked->menuid )."' ";
		$this->db->query($sqlin);


		global $template;
		$location_url = "./index.php?menuid=" . $this->checked->menuid . "&reporeid=" . $this->checked->reporeid . "&easyedit&getlang=".$this->content->template['lang_short'];

		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 *
	 * Damit wird das ausgew�hlte Teaser BIld an den Teaser Text angeh�ngt
	 *
	 * @return void
	 */
	function make_teaser_bild()
	{
		$float = "float:" . $this->checked->teaser_bild_lr;
		// Sprache festlegen
		$sprach_id = $this->cms->lang_id;

		$selectbild = sprintf( "SELECT * FROM %s AS t1, %s AS t2 WHERE t1.image_name='%s' AND t1.image_id=t2.lan_image_id AND t2.lang_id='%d'",
			$this->cms->papoo_images,
			$this->cms->papoo_language_image,
			$this->db->escape( $this->checked->teaser_bild ),
			$sprach_id
		);
		$resultbild = $this->db->get_results( $selectbild, ARRAY_A );
		if (!empty($resultbild)) {
			foreach ($resultbild as $bilddrin) {
				// st�rende Anf�hrungszeichen entfernen
				$bilddrin = str_replace( '"', '', $bilddrin );
				// Image-Tag zusammen setzen
				$this->teaserbild_in = '<img alt="' . $bilddrin['alt'] . '" title="' . $bilddrin['title'] . '" src="./images/thumbs/' . $this->checked->teaser_bild . '" class="teaserbild' . $this->checked->teaser_bild_lr . '" style="' . $float . '" />';
			}
		}
		else {
			$this->teaserbild_in = "";
		}
	}

	/**
	 * Sortiert die Artikel, die zum Men�-Punkt $menuid geh�ren, neu
	 * so werden evtl. entstandene L�cher entfernt
	 *
	 * @param int $menuid
	 */
	function artikel_reorder($menuid = 0)
	{
		// Test ob Artikel der Start-Seite zugeh�rt
		if ( $menuid == 1 || $menuid == 0 ) {
			// Artikel-Liste der Startseite zusammenstellen, geordnet nach order_id_start
			$sql = sprintf( "SELECT reporeID FROM %s WHERE teaser_list='1' ORDER BY order_id_start",
				$this->cms->papoo_repore
			);
			$order_feld = "order_id_start";
		}
		else {
			// Artikel-Liste des aktuellen Men�-Punktes zusammenstellen, geordnet nach order_id
			$sql = sprintf( "SELECT reporeID FROM %s WHERE cattextid='%d' ORDER BY order_id",
				$this->cms->papoo_repore,
				$this->db->escape($menuid)
			);
			$order_feld = "order_id";
		}
		$artikel_liste = $this->db->get_results( $sql );
		// Artikel neu durchnummerieren
		if ( !empty( $artikel_liste ) ) {
			$nummer_neu = 1;
			foreach( $artikel_liste as $artikel ) {
				$sql = sprintf( "UPDATE %s SET %s='%d' WHERE reporeID='%d'",
					$this->cms->papoo_repore,
					$this->db->escape($order_feld),
					$this->db->escape($nummer_neu),
					$this->db->escape($artikel->reporeID)
				);
				$this->db->query( $sql );
				$nummer_neu += 1;
			}
		}
	}

	/**
	 * Artikel Easy speichern
	 * Seite mit Artikel neu laden
	 */
	function easy_save_article()
	{
		// Soll gespeichert werden
		if ( ( !empty( $this->checked->easysave ) ) ) {
			// Zuerst die Links ersetzen
			$this->easy_replace_links();


			$this->timestamp = date( "j.n.Y  G:i:s" );
			// Tabelle repore speichern
			$sql = sprintf( "UPDATE %s SET
			teaser_bild='%s',
			teaser_bild_lr='%s',
			dokuser='%d',
			stamptime='%d',
			timestamp='%s',
			teaser_bild_html='%s'
			WHERE reporeID='%d' LIMIT 1",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape( $this->checked->teaser_bild ),
				$this->db->escape( $this->checked->teaser_bild_lr ),
				$this->user->userid,
				time(),
				$this->timestamp,
				$this->db->escape( $this->teaserbild_in ),
				$this->db->escape( $this->checked->reporeid )
			);
			$this->db->query( $sql );
			// Sprachdaten speichern
			// Daten ersetzen
			$artikel = $this->replace->do_replace( $this->checked->lan_article_sans );
			$artikel = str_ireplace( "ppmenuidpp", $this->checked->menuid, $artikel );
			// HTML bereinigen
			$artikel = $this->html->do_tidy( $artikel );
			$this->checked->lan_article_sans = str_ireplace( "\./image", "../image", $this->checked->lan_article_sans );
			$url_header = $this->menu->replace_uml( strtolower( $this->checked->header ) );

			$sql = sprintf( "UPDATE %s SET
					header='%s',
					lan_teaser='%s',
					lan_article_sans='%s',
					lan_article='%s'
					WHERE lan_repore_id='%d' AND lang_id='%d' LIMIT 1",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape( $this->checked->header ),
				$this->db->escape( $this->checked->lan_teaser ),
				$this->db->escape( $this->checked->lan_article_sans ),
				$this->db->escape( $artikel ),
				$this->db->escape( $this->checked->reporeid ),
				$this->cms->lang_id
			);
			$this->db->query( $sql );
			global $template;
			$location_url = "./index.php?menuid=" . $this->checked->menuid . "&reporeid=" . $this->checked->reporeid . "&easyedit&getlang=".$this->content->template['lang_short'];

			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		// Soll gel�scht werden?
		if ( ( !empty( $this->checked->easydel ) ) ) {
			/**
			 * Damit das l�schen nicht zu einfach ist
			 * werdendie Artikel nur auf nicht ver�ffentlicht gesetzt
			 * Richtig gel�scht werden kann nur in der Admin
			 */
			$sql = sprintf( "UPDATE %s SET
			publish_yn='0'
			WHERE reporeID='%d' LIMIT 1",
				$this->cms->tbname['papoo_repore'],
				$this->db->escape( $this->checked->reporeid )
			);
			$this->db->query( $sql );
			$location_url = "./index.php?menuid=" . $this->checked->menuid;

			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
	}

	/**
	 * @param $url
	 * @param $langid
	 * @return bool|mixed|string
	 */
	function check_url_header($url, $langid)
	{
		if ($this->cms->mod_free==1) {
			$sql = sprintf("SELECT lang_short FROM %s WHERE lang_id='%s'",
				$this->cms->tbname['papoo_name_language'],
				$this->db->escape($langid)
			);
			$lang_short = $this->db->get_var($sql);

			//Nur Bei NICHT Standardsprache
			if ($lang_short!=$this->content->template['lang_front_default']) {
				if (!stristr($url,"/".$lang_short."/")) {
					//DIe neue url
					$url="/".$lang_short."".$url;
				}
			}

			$klammer_id="".time();
			$last  = substr($url,-1,1);
			$last2 = substr($url,-5,5);

			$sql=sprintf("SELECT COUNT(lan_repore_id) FROM %s
										WHERE url_header='%s'",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($url)
			);
			$result=$this->db->get_var($sql);
			if ($result>0) {

				//Artikel mit .html
				if ($last2 ==".html" ) {
					if ($this->cms->artikel_url_mit_html) {
						$url=str_replace(".html","-".$klammer_id.".html",$url);
					}

				}
				//Artikel mit /
				if ($last =="/") {
					$url  = substr($url,0,-1);
					$url=$url."-".$klammer_id."/";
				}
				else {
					if (!$this->cms->artikel_url_mit_html) {
						$url=$url."-".$klammer_id;
					}
				}

				if ($last!="/" && $last2!=".html") {
					$url=$url.".html";
				}

				return $url;
			}

			$sql=sprintf("SELECT COUNT(menuid_id) FROM %s
										WHERE url_menuname='%s'",
				$this->cms->tbname['papoo_menu_language'],
				$this->db->escape($url)
			);
			$result=$this->db->get_var($sql);
			if ($result>0) {
				//Artikel mit .html
				if ($last2 ==".html" ) {
					if ($this->cms->artikel_url_mit_html) {
						$url=str_replace(".html","-".$klammer_id.".html",$url);
					}
				}
				//Artikel mit /
				if ($last =="/") {
					$url  = substr($url,0,-1);
					$url=$url."-".$klammer_id."/";
				}
				else {
					if (!$this->cms->artikel_url_mit_html) {
						$url=$url."-".$klammer_id;
					}
				}
				if ($last!="/" && $last2!=".html") {
					$url=$url.".html";
				}
				return $url;
			}

			if ($last!="/" && $last2!=".html") {
				$url=$url.".html";
			}
			return $url;
		}
		else {
			return $url;
		}
	}

	/**
	 * Die Artikelbearbeitung starten
	 * Mit Easyedit erfolgt die Rechteerteilung ja etwas flacher,
	 * daher keine Rechte pro Artikel
	 */
	function manage_article()
	{
		// Daten des Artikel rausholen
		$reporeid = $this->db->escape( $this->checked->easyid );
		$sql = sprintf( "SELECT * FROM %s, %s WHERE reporeID='%d'
		AND reporeID=lan_repore_id
		AND lang_id='%d'",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$reporeid,
			$this->cms->lang_id
		);
		$result = $this->db->get_results( $sql, ARRAY_A );
		// Inhalte mit nobr von brs befreien
		foreach( $result as $dat ) {
			foreach( $dat as $key => $value ) {
				$value = str_ireplace( "\.\./image", "./image", $value );
				$result['0'][$key] = "nobr:" . $value;
			}
		}
		if ($this->done!="ok") {
			$this->content->template['easyc'] = $result;
		}
		// Biler liste raussuchen
		$this->get_bilder_liste();
	}
	/**
	 * Liste der Bilder rausholen
	 */
	function get_bilder_liste()
	{
		// Liste der verf�garen Bilder rausholen
		$sprach_id = $this->cms->lang_id;
		// Bilder aus der Datenbank holen ...
		// $arraysessiongruppenid = 1;
		// Sprache rausfinden
		$catok = $this->get_image_cat_list();
		$sqlbild = sprintf( "SELECT DISTINCT(image_id), image_name, alt, title, image_width, image_dir, image_height
							FROM %s, %s, %s
							WHERE image_id=lan_image_id  AND lang_id='%d' AND image_dir=bilder_cat_id
							OR image_id=lan_image_id  AND lang_id='%d' AND image_dir=0
							ORDER BY image_dir, alt ASC",
			$this->cms->papoo_images,
			$this->cms->papoo_language_image,
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->db->escape($sprach_id),
			$this->db->escape($sprach_id)
		);
		$resultbild = $this->db->get_results( $sqlbild );
		$bild_data = array ();

		if ( !empty ( $resultbild ) ) {
			foreach ( $resultbild as $rowbild ) {
				if ( $_SESSION['metadaten'][$this->unique_id][$this->replangid]['teaserbild'] == $rowbild->image_name ) {
					$checked = 'selected="selected"';
				}
				else {
					$checked = "";
				}

				$buh = "";
				// Gr��e der Thumbnails rausholen
				// echo "../images/thumbs/".$rowbild->image_name;
				if ( @file_exists( ( "../images/thumbs/" . $rowbild->image_name ) ) ) {
					$thumbsize = @GetImageSize( "../images/thumbs/" . $rowbild->image_name );
					$buh = $thumbsize[0] . "x" . $thumbsize[1];
				}
				// Nur wenn Cat ok
				// echo $rowbild->image_dir;
				if ( $this->diverse->deep_in_array( $rowbild->image_dir, $catok ) or $rowbild->image_dir == "0" ) {
					// print_r($thumbsize);
					array_push( $bild_data, array ( 'image_name' => $rowbild->image_name,
						'image_alt' => str_replace( '"', '', $rowbild->alt ),
						'image_title' => str_replace( '"', '', $rowbild->title ),
						'image_width' => $rowbild->image_width,
						'image_dir' => $this->diverse->isdeep_item['bilder_cat_name'],
						'image_height' => $rowbild->image_height,
						'thumb_buh' => $buh,
						'image_check_teaser' => $checked ) );
				}
			}
			$this->content->template['bild_data'] = $bild_data;
		}
		// ENDE --- Bilder aus der Datenbank holen f�r bbcode Editor ... ###
	}

	/**
	 * Die Kategorienliste erstellen
	 */
	function get_image_cat_list()
	{
		$sql = sprintf( "SELECT DISTINCT(bilder_cat_id), bilder_cat_name FROM %s,%s,%s WHERE userid='%s' AND gruppenid=gruppeid_id AND bilder_cat_id_id=bilder_cat_id",
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->cms->tbname['papoo_lookup_cat_images'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);

		$result = $this->db->get_results( $sql, ARRAY_A );
		$this->content->template['result_cat'] = $result;
		return $result;
	}

	/**
	 * Men�daten setzen
	 */
	function easy_set_menu()
	{
		// Alle Men�eintr�ge rausholen
		$this->content->template['menu_data_rights'] = array();
		$this->content->template['templateweiche'] = "LISTE";
		$this->content->template['menulist_data']=$this->menu->menu_data_read("FRONT_PUBLISH");
		#	$this->content->template['menulist_data'] = $this->menu->menu_navigation_build( "FRONT_PUBLISH", "CLEAN" );
		// �nderungen speichern
		if ( !empty( $this->checked->entermen ) ) {
			// Alte Eintr�ge l�schen
			$sql = sprintf( "DELETE FROM %s",
				$this->cms->tbname['papoo_easyedit_daten']
			);
			$this->db->query( $sql );
			// durchloopen
			foreach ( $this->content->template['menulist_data'] as $menu_punkt ) {
				$sql = sprintf( "INSERT INTO %s SET
				easy_men_id='%d',
				easy_order_id='%d',
				easy_easy_ok='%d'",
					$this->cms->tbname['papoo_easyedit_daten'],
					$this->db->escape( $menu_punkt['menuid'] ),
					$this->db->escape( $this->checked->easy_order_id[$menu_punkt['menuid']] ),
					$this->db->escape( $this->checked->easy_easy_ok[$menu_punkt['menuid']] )
				);
				$this->db->query( $sql );
			}
		}
		// Gruppendaten anzeigen
		$this->easy_set_group();

		$i = $j = 1;
		$checked_punkt = "";
		if (is_array($this->content->template['menulist_data'])) {
			foreach ( $this->content->template['menulist_data'] as $menu_punkt ) {
				if ( $i % 2 != 0 ) {
					$style = "e1e3d5";
				}
				else {
					$style = "fff";
				}
				$i ++;
				$menrechte = array ();
				// Rechte Daten auslesen, vertretbar als Abfrage, da nur begrenzt viele Abfragen stattfinden, besser als 400000 Loops als Arrays...
				$sql = sprintf( "SELECT * FROM %s WHERE easy_men_id='%d'",
					$this->cms->tbname['papoo_easyedit_daten'],
					$menu_punkt['menuid']
				);
				// Daten in Array zuweisen
				$resultgr = $this->db->get_results( $sql );
				$orderid = $easyok = "";
				// Gruppennamen durchgehen
				if ( !empty( $resultgr ) ){
					foreach ( $resultgr as $row2 ) {
						// Wenn id drin ist dann auszeichnen
						if ( $menu_punkt['menuid'] == $row2->easy_men_id ) {
							$orderid = $row2->easy_order_id;
							$easyok = $row2->easy_easy_ok;
						}
					}
				}

				array_push( $this->content->template['menu_data_rights'], array ( 'menuname' => $menu_punkt['menuname'],
						'menuid' => $menu_punkt['menuid'],
						'level' => $menu_punkt['level'],
						'gruppe' => $menrechte,
						'style' => $style,
						'easy_order_id' => $orderid,
						'easy_easy_ok' => $easyok,
					)
				);
			}
		}
	}

	/**
	 * Men�daten setzen
	 */
	function easy_set_group()
	{
		// �nderungen speichern
		// Alle Gruppeneintr�ge rausholen
		$sql = sprintf( "SELECT * FROM %s",
			$this->cms->tbname['papoo_gruppe']
		);
		$result = $this->db->get_results( $sql, ARRAY_A );
		// �nderungen speichern
		if ( !empty( $this->checked->entergr ) ) {
			// Alte Eintr�ge l�schen
			$sql = sprintf( "DELETE FROM %s",
				$this->cms->tbname['papoo_easyedit_gruppe']
			);
			$this->db->query( $sql );
			// durchloopen
			foreach ( $result as $group ) {
				$sql = sprintf( "INSERT INTO %s SET
				easy_group_id='%d',
				easy_geasy_ok='%d'",
					$this->cms->tbname['papoo_easyedit_gruppe'],
					$this->db->escape( $group['gruppeid'] ),
					$this->db->escape( $this->checked->easy_geasy_ok[$group['gruppeid']] )
				);
				$this->db->query( $sql );
			}
		}
		// vorhandene EIntr�ge raussuchen
		$i = 0;
		$this->content->template['gruppen'] = array();
		foreach ( $result as $gruppe ) {
			if ( $gruppe['gruppeid'] == 10 ) {
				continue;
			}
			if ( $i % 2 != 0 ) {
				$style = "e1e3d5";
			}
			else {
				$style = "fff";
			}
			$i ++;
			$menrechte = array ();
			// Rechte Daten auslesen, vertretbar als Abfrage, da nur begrenzt viele Abfragen stattfinden, besser als 400000 Loops als Arrays...
			$sql = sprintf( "SELECT * FROM %s WHERE easy_group_id='%d'",
				$this->cms->tbname['papoo_easyedit_gruppe'],
				$gruppe['gruppeid']
			);
			// Daten in Array zuweisen
			$resultgr = $this->db->get_results( $sql );
			// print_r($resultgr);
			$orderid = $easyok = "";
			// Gruppennamen durchgehen
			if ( !empty( $resultgr ) ) {
				foreach ( $resultgr as $row2 ) {
					// Wenn id drin ist dann auszeichnen
					if ( $gruppe['gruppeid'] == $row2->easy_group_id ) {
						$easyok = $row2->easy_geasy_ok;
					}
				}
			}

			array_push( $this->content->template['gruppen'], array ( 'menuname' => $menu_punkt['menuname'],
					'gruppeid' => $gruppe['gruppeid'],
					'gruppenname' => $gruppe['gruppenname'],
					'style' => $style,
					'easy_geasy_ok' => $easyok,
				)
			);
		}
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Men�namen f�r mod_rewrite mit sprechurls
	 *
	 * @param string $menid
	 * @return mixed|void
	 */
	function get_verzeichnis( $menid = "" )
	{
		if ( !empty( $menid ) ) {
			$mendata = $this->menu->data_front_complete;
			foreach ( $mendata as $menuitems ) {
				// aktuelle menid finden
				if ( $menid == $menuitems['menuid'] ) {
					// echo $menid;
					$this->m_url[] = ( $menuitems['url_menuname'] );
					// Nicht oberste Ebene, neu aufrufen
					if ( $menuitems['untermenuzu'] != 0 ) {
						$this->get_verzeichnis( $menuitems['untermenuzu'] );
					}
					else {
						return true;
					}
				}
			}
		}
	}

	/**
	 * Bestehende Links in Artikel etc. korrigieren
	 */
	function easy_replace_links()
	{
		// Teaser erzeugen
		$this->make_teaser_bild();
		$this->m_url = array();
		$this->get_verzeichnis( $this->checked->menuid );
		$this->m_url = array_reverse( $this->m_url );
		foreach ( $this->m_url as $dat ) {
			$link = $dat . "/";
		}
		$sql = sprintf( "SELECT url_header FROM %s WHERE lan_repore_id='%s' AND lang_id='%s'",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape( $this->checked->reporeid ),
			$this->cms->lang_id
		);
		$link2 = $link;
		$link .= $urlh_alt = $this->db->get_var( $sql );

		// Name �bergeben
		$name = $this->checked->header;
		$varcount = "";
		// Checken ob die �berschrift schon existiert
		$sql = sprintf( "SELECT COUNT(reporeID) FROM %s,%s WHERE header='%s' AND reporeID!='%s'",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape( $name ),
			$this->db->escape( $this->checked->reporeid )
		);
		$varcount = $this->db->get_var( $sql );
		if ( $varcount >= 1 ) {
			// $varcount=$varcount+1;
			$varcount = "-" . $this->cms->lang_id . "-" . $varcount + 1;
		}
		else {
			$varcount = "";
		}
		$urlh_neu = $this->menu->replace_uml( strtolower( $this->checked->header ) ) . $varcount;
		// Link in Artikel suchen
		$sql = sprintf( "SELECT * FROM %s WHERE lan_article LIKE '%s' AND lang_id='%s'",
			$this->cms->papoo_language_article,
			"%href=\"" . PAPOO_WEB_PFAD . "/web/" . $this->db->escape( $link ) . "%",
			$langid
		);

		$result = $this->db->get_results( $sql );
		// der alte Link
		$such = ( "href=\"" . PAPOO_WEB_PFAD . "/web/" . ( $link ) ) . ".html";
		// der neue Link
		$replace = ( "href=\"" . PAPOO_WEB_PFAD . "/web/" . $link2 . $this->menu->replace_uml( strtolower( $name . $varcount ) ) ) . ".html"; #echo "<br />";
		// Link in Artikel ersetzen und eintragen
		// $result="";
		if ( !empty( $result ) ) {
			foreach ( $result as $dat ) {
				// F�r jeden Eintrag ersetzen
				$dat->lan_article = str_ireplace( $such, $replace, $dat->lan_article );
				$dat->lan_article_sans = str_ireplace( $such, $replace, $dat->lan_article_sans );
				// Sql Statement zur Ersetzung
				$sql = sprintf( "UPDATE %s
											SET lan_article='%s', lan_article_sans='%s'
											WHERE lan_repore_id='%d' AND lang_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape( $dat->lan_article ),
					$this->db->escape( $dat->lan_article_sans ),
					$this->db->escape($dat->lan_repore_id),
					$this->db->escape($dat->lang_id)
				);
				$this->db->query( $sql );
			}
		}
		// Link in 3. Spalte suchen
		$sql = sprintf( "SELECT  * FROM %s WHERE article LIKE '%s' AND lang_id='%s'",
			$this->cms->tbname['papoo_language_collum3'],
			"%href=\"" . PAPOO_WEB_PFAD . "/web/" . $this->db->escape( $link ) . "%",
			$langid
		);
		$result = $this->db->get_results( $sql );
		// Link in 3. Spalte ersetzen
		if ( !empty( $result ) ) {
			foreach ( $result as $dat ) {
				// F�r jeden Eintrag ersetzen
				$dat->article = str_ireplace( $such, $replace, $dat->article );
				$dat->article_sans = str_ireplace( $such, $replace, $dat->article_sans );
				// Sql Statement zur Ersetzung
				$sql = sprintf( "UPDATE %s
											SET article='%s', article_sans='%s'
											WHERE collum_id='%d' AND lang_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_language_collum3'],
					$this->db->escape( $dat->article ),
					$this->db->escape( $dat->article_sans ),
					$this->db->escape($dat->collum_id),
					$this->db->escape($dat->lang_id)
				);
				$this->db->query( $sql );
			}
		}
		// Link in Startseite suchen
		$sql = sprintf( "SELECT * FROM %s WHERE lang_id='%s'",
			$this->cms->tbname['papoo_language_stamm'],
			$this->db->escape($lang)
		);
		$result = $this->db->get_results( $sql );
		// Link in Startseite ersetzen
		if ( !empty( $result ) ) {
			foreach ( $result as $dat ) {
				// F�r jeden Eintrag ersetzen
				$dat->start_text = str_ireplace( $such, $replace, $dat->start_text );
				$dat->start_text_sans = str_ireplace( $such, $replace, $dat->start_text_sans );
				// Sql Statement zur Ersetzung
				$sql = sprintf( "UPDATE %s
											SET start_text='%s', start_text_sans='%s'
											WHERE stamm_id='%d' AND lang_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_language_stamm'],
					$this->db->escape( $dat->start_text ),
					$this->db->escape( $dat->start_text_sans ),
					$this->db->escape($dat->stamm_id),
					$this->db->escape($dat->lang_id)
				);
				$this->db->query( $sql );
			}
		}
	}

	/**
	 * Die Daten dumpen und wieder zur�ckspielen
	 */
	function easy_dump()
	{
		$this->diverse->extern_dump( "easyedit" );
	}
}

$easyedit = new easyedit();
