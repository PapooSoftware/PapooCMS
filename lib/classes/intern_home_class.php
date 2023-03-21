<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Diese Klasse initialisiert die Home Seite des Admin Bereiches.
 *
 * Class intern_home
 */
#[AllowDynamicProperties]
class intern_home
{
	/**
	 * intern_home constructor.
	 */
	function  __construct()
	{
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		// Messages einbinden
		global $message;
		// User Klasse einbinden
		global $user;
		// weitere Seiten Klasse einbinden
		global $weiter;
		// inhalt Klasse einbinden
		global $content;
		// Suchklasse einbinden
		global $searcher;
		// checkedblen Klasse einbinde
		global $checked;
		// Interne Menüklasse einbinden
		global $intern_menu;
		// html Bereinigunsklasse
		global $html;
		// Ersetzungklasse
		global $replace;
		// Verschlüsselung etc. einbinden
		global $diverse;

		/**
		und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->cms			= & $cms;
		$this->db			= & $db;
		$this->content 		= & $content;
		$this->message		= & $message;
		$this->user			= & $user;
		$this->weiter		= & $weiter;
		$this->searcher		= & $searcher;
		$this->checked		= & $checked;
		$this->intern_menu	= & $intern_menu;
		$this->html			= & $html;
		$this->replace		= & $replace;
		$this->diverse		= & $diverse;

		IfNotSetNull($this->content->template['mail_ok']);
		IfNotSetNull($this->content->template['uberschrift']);
		IfNotSetNull($this->content->template['mail_data']);
		IfNotSetNull($this->content->template['table']['produkte']);
	}

	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		// Restore ohne Zugriff-Check
		if (!empty($this->checked->restore)) {
			// FIXME: Funktion existiert nicht
			$this->make_restore();
		}
		else {
			$this->user->check_access();
		}

		switch ($this->checked->menuid) {

		case "1";
			// Startseite anzeigen
			$this->do_start();
			break;

		case "34";
			//Die privaten Messages lesen und anzeigen wenn welche da
			$this->view_mail();
			break;

		case "35";
			// mail schreiben
			$this->write_mail();
			break;

		case "36";
			// Die zu veröffentlichenden Artikel anzeigen
			$this->view_article_to_open();
			break;

		case "37";
			// veröffentlichte Artikel anzeigen
			$this->artikel_published();
			break;

		case "38";
			// persönliche Daten bearbeiten
			$this->do_pers();
			break;

		default:
			// Startseite anzeigen
			$this->do_start();
			break;
		}
	}

	function do_start()
	{
		if (empty($this->checked->submithome)) {
			$this->content->template['case0']= 'hier';
		}

		// feststellen wieviel Artikel veröffentlicht werden sollen
		#$this->check_offen();
		// feststellen wieviel Artikel bereits unter diesem Account veröffentlicht wurden
		#$this->check_publish();
		// wieviele neue mails warten
		#$this->check_mail();
		$this->check_rss();
	}

	/**
	 * RSS Feed der papoo.de Seite einbinden
	 */
	function check_rss()
	{
		if ($this->cms->showpapoo == 1) {
			$url = "https://www.papoo.de/cms-blog/";
			$daten = diverse_class::get_url_get(($url));

			if ($daten == false) {
				$this->content->template['papoo_news'] = "Keine Verbindung!";
				$this->content->template['menuid_aktuell'] = $this->checked->menuid;
				return;
			}

			$daten1 = explode('id="artikel"></a>', ($daten));
			$daten2 = explode('<!-- MODUL: weiter -->', $daten1['1']);

			$rssData = $daten2['0'];

			// Strip out src tags
			$rssData = preg_replace('#\ssrc=".*?"#', '', $rssData);

			// Replace data-src attributes with src and set the real Papoo-Link
			$rssData = preg_replace('#\sdata-src="(/.*?)"#', ' src="https://www.papoo.de$1"', $rssData);

			// Edit href with real Papoo-Link and add target="_blank"
			$rssData = preg_replace('#\shref="(/.*?)"#', ' href="https://www.papoo.de$1" target="_blank" rel="noopener"', $rssData);

			// Strip non-allowed html-tags
			$rssData = strip_tags($rssData, "<h2><a><img><br><p><span>");

			$rssData .= "<br /><br />";

			$rssData = htmlentities($rssData);
			$rssData = str_ireplace("&bdquo;", '"', $rssData);
			$rssData = str_ireplace("&ldquo;", '"', $rssData);
			$this->content->template['papoo_news'] = "nobr:" . (html_entity_decode($rssData));
			$this->content->template['menuid_aktuell'] = $this->checked->menuid;
		}
	}

	/**
	 * Wieviele Artikel müssen noch veröffentlicht werden
	 *
	 * @return void
	 */
	function check_offen()
	{
		// lang_id erstellen
		$userid=$this->user->userid;
		/* feststellen wieviel Artikel veröffentlicht werden sollen
		* recht komplexe Abfrage
		* orientiert sich an der Elterngruppenid
		*/

		//rausfinden ob der User zu denen gehört die Veröffentlichungen checken müssen.
		$sql=sprintf("SELECT COUNT(gruppeid) FROM %s WHERE publish_user=%s ",
			$this->cms->tbname['papoo_gruppe'],
			$userid
		);
		$okuser=$this->db->get_var($sql);

		//Der aktuelle User gehört zu den Chefs
		if (!empty($okuser)){
			//Artikel raussuchen die den User angehen
			$sql=sprintf("SELECT COUNT(reporeID) FROM %s, %s, %s, %s WHERE dokuser=%s.userid".
				"	AND allow_publish='0'".
				" AND gruppenid=gruppeid ".
				" AND publish_user=%s ".
				" AND reporeID=lan_repore_id ".
				" AND lang_id=%s
			",
				$this->cms->tbname['papoo_gruppe'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->tbname['papoo_repore'],
				$this->cms->tbname['papoo_language_article'],
				$this->cms->tbname['papoo_lookup_ug'],
				$userid,
				$this->db->escape($this->cms->lang_id)
			);
		}
		$result=$this->db->get_var($sql);
		$sql=sprintf("SELECT COUNT(reporeID) FROM %s, %s WHERE ".
			"	publish_yn_lang='0'" .
			" AND dokuser=%s ".
			" AND reporeID=lan_repore_id ".
			" AND lang_id=%s
			",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$userid,
			$this->db->escape($this->cms->lang_id)
		);
		$result2=($this->db->get_var($sql));
		$this->content->template['artikel_veroffen']=$this->resultzahl =$result+$result2;
	}

	/**
	 * Wieviele Artikel müssen noch veröffentlicht werden
	 *
	 * @return void
	 */
	function offen_artikel()
	{
		// lang_id erstellen

		$userid=$this->user->userid;
		/* anzeigen der Artikel die veröffentlicht werden sollen
		* recht komplexe Abfrage
		* orientiert sich an der Elterngruppenid
		*
		* reporeID,header,lan_teaser   ,".$this->cms->papoo_language_article."
		*/
		//Artikel raussuchen die den User angehen
		$sql=sprintf("SELECT * FROM %s, %s, %s, %s WHERE dokuser=%s.userid".
			"	AND publish_yn_lang='0'" .
			" AND gruppenid=gruppeid ".
			" AND publish_user=%s ".
			" AND reporeID=lan_repore_id ".
			" AND lang_id=%s
			",
			$this->cms->tbname['papoo_gruppe'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$this->cms->tbname['papoo_lookup_ug'],
			$userid,
			$this->db->escape($this->cms->lang_id)
		);

		// Ergebniss zuweisen für das Template
		$result=$this->db->get_results($sql);
		if (empty($result)) {
			$result=array();
		}
		$sql=sprintf("SELECT * FROM %s, %s WHERE ".
			"	publish_yn_lang='0'" .
			" AND dokuser=%s ".
			" AND reporeID=lan_repore_id ".
			" AND lang_id=%s
			",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$userid,
			$this->db->escape($this->cms->lang_id)
		);
		$result2=($this->db->get_results($sql));

		if (empty($result2)) {
			$result2=array();
		}
		$this->content->template['resultoffen']=$this->resultoffen=array_merge($result,$result2);
	}

	/**
	 * Wieviele Artikel wurden bereits unter diesem Account veröffentlicht
	 *
	 * @return void
	 */
	function check_publish()
	{
		$userid=$this->user->userid;
		// feststellen wieviel Artikel bereits unter diesem Account veröffentlicht wurden
		$suchergebniss2 = "SELECT COUNT(reporeID) FROM " . $this->cms->papoo_repore . " WHERE dokuser='$userid' AND

		/*publish_yn_lang='1' AND*/

		allow_publish='1' OR dokuser='$userid' AND publish_yn_intra='1'  AND allow_publish='1' ";
		// Ergebniss zuweisen für das Template
		$this->content->template['artikel_von_user']=$this->resultzahl2 = $this->db->get_var($suchergebniss2);
	}

	/**
	 * Herausfinden wiewiele Mails neu sin
	 *
	 * @return void
	 */
	function check_mail()
	{
		// Mitteilungen zählen von andern Mitgliedern
		$userid=$this->user->userid;
		$suchergebniss3 = "SELECT COUNT(*) FROM ".$this->cms->papoo_mail." WHERE mail_to_user='$userid' AND mail_read='0'" ;
		// zuweisen für das Template
		$this->content->template['neue_mail']=$this->resultzahl3 = $this->db->get_var($suchergebniss3);
	}

	/**
	 * Die privaten Messages lesen und anzeigen wenn welche da
	 *
	 * @return void
	 */
	function view_mail()
	{
		// Die privaten Messages lesen und anzeigen wenn welche da
		$this->content->template['mailen']= '1';
		// userid zuweisen
		$userid=$this->user->userid;
		if (!empty($this->checked->delete)) {
			$sql=sprintf("DELETE FROM %s WHERE mail_id='%s' LIMIT 1",
				$this->cms->tbname['papoo_mail'],
				$this->db->escape($this->checked->mail_id)
			);
			$this->db->query($sql);

		}
		// es ist eine Mail ausgesucht,
		if (empty($this->checked->mail_id)){
			$this->checked->mail_id="";
		}
		if ($this->checked->mail_id != "" and is_numeric($this->checked->mail_id)) {
			// diese raussuchen
			$suchergebnissmail = "SELECT * FROM ".$this->cms->papoo_mail." WHERE mail_to_user='$userid' AND mail_archiv='0' AND mail_id='".$this->db->escape($this->checked->mail_id)."'" ;
			// abfragen
			$resultmail = $this->db->get_results($suchergebnissmail);
			// auf gelesen setzen
			$read="UPDATE ".$this->cms->papoo_mail." SET mail_read='1' WHERE mail_id='".$this->db->escape($this->checked->mail_id)."'";
			$this->db->query($read);
			// und diese anzeigen
			if (!empty($resultmail)){
				foreach ($resultmail as $row) {
					$this->content->template['mailhead'] = $row->mail_head;
					$this->content->template['mail_id'] = $row->mail_id;
					$this->content->template['mailtext'] = "nodecode:".$row->mail_text;
				}
			}
			// es ist eine rausgesucht -> Template
			$this->content->template['mail_ok']= '1';
		}
		// Array erzeugen
		$mail_data=array();
		// Alle Mails raussuchen
		$suchergebniss3 = "SELECT * FROM ".$this->cms->papoo_mail.", ".$this->cms->papoo_user." WHERE mail_to_user='$userid' AND mail_archiv='0' AND mail_to_user=userid ORDER BY mail_id DESC" ;
		// ABfrage
		$result3 = $this->db->get_results($suchergebniss3);
		// wenn nicht ller
		if (!empty($result3)) {
			// für jeden Eintrag Daten fürs template zuweisen
			foreach ($result3 as $row) {
				// Daten der Mails zuweisen
				array_push($mail_data, array(
					'mail_head' 		=> $row->mail_head,
					'mail_from_user' 	=> $row->username,
					'mail_read' 		=> $row->mail_read,
					'mail_date' 		=> $row->mail_date,
					'mail_id' 			=> $row->mail_id,));
			}
			$this->content->template['mail_data']=$mail_data;
		}
	}

	/**
	 * Hiermit kann eine interne Mai geschrieben werden
	 *
	 * @return void
	 */
	function write_mail()
	{
		$this->content->template['writemail']= '1';
		$this->content->template['input']='eingabe';
		$this->user_data=array();
		// Wenn eine neue Mail geschrieben wurd
		if (!empty($this->checked->eingabe)) {
			// Datum feststellen
			$maildate = date("j.n.Y  G:i:s");
			if (empty($this->checked->uberschrift)) {
				$this->checked->uberschrift="Message - ".$this->user->username;
			}
			// Userid einstellen
			$userid=$this->user->userid;
			// Abfrage formulieren
			$sql = "INSERT INTO ".$this->cms->papoo_mail." SET mail_head='".$this->db->escape($this->checked->uberschrift)."', mail_text='".$this->db->escape($this->checked->inhalt)."', mail_from_user='$userid', mail_to_user='".$this->db->escape($this->checked->formiusername)."', mail_date='$maildate'";
			// Abfrage in die Datenbank
			$this->db->query($sql);
			// Seite neu laden und schluß
			//header("Location: ./index.php");
			$location_url = "./index.php";
			if (empty($_SESSION['debug_stopallredirect'])) {
				$_SESSION['debug_stopallredirect']="";
			}
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		// Mails raussuchen
		$suchergebniss3 = "SELECT * FROM ".$this->cms->papoo_user." " ;
		$result3 = $this->db->get_results($suchergebniss3);
		// Einträge zuweisen
		foreach ($result3 as $row) {
			array_push($this->user_data, array(
				'selected' => "",
				'username_id' => $row->userid,
				'username_name' => $row->username,
				));
		}
		$this->content->template['user_data']=$this->user_data;
	}

	function view_article_to_open()
	{
		// Die offenen Artikel -> Template
		$this->content->template['artikel_offen']= '1';

		// Anzahl rausbekommen
		$this->check_offen();

		// Anzahl zuweisen
		$result_anzahl = $this->resultzahl;

		// Limit setzen
		$this->sqllimit=$this->cms->sqllimit;

		// offene artikel auslesen
		$this->offen_artikel();

		// Daten zuweisen
		$result =$this->resultoffen;

		$this->link_data=array();

		if (!empty ($result)) {
			foreach ($result as $row) {
				$temp_array = array();
				$temp_array['ueberschrift'] = $row->header;
				$temp_array['reporeID'] = $row->reporeID;
				$temp_array['teaser'] = $row->lan_teaser;
				//$temp_array['timestamp'] = $row->timestamp;
				$temp_array['date_time'] = $row->timestamp;
				$sql=sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser_last
				);
				$temp_array['username'] =$this->db->get_var($sql);
				IfNotSetNull($row->username);
				if (($temp_array['username']<10)) {
					$temp_array['username']=$row->username;
				}
				$temp_array['autor']=$row->username;

				// Daten zuweisen
				$this->content->template['link_data'][] = $temp_array;
			}
		}
		$this->weiter->weiter_link = "./index.php?menuid=".$this->checked->menuid."&myhome=3";
		$this->weiter->result_anzahl= $result_anzahl;
		$this->weiter->do_weiter("teaser");
	}

	/**
	 * Daten der Artikel die bereits unter diesem Account veröffentlicht wurden
	 *
	 * @return void
	 */
	function check_publish_artikel()
	{
		$userid=$this->user->userid;
		// feststellen wieviel Artikel bereits unter diesem Account veröffentlicht wurden
		$suchergebniss2 = "SELECT reporeID,header,lan_teaser, dokuser_last, timestamp FROM " . $this->cms->papoo_repore . "," . $this->cms->papoo_language_article . " WHERE dokuser='$userid' AND

		/*publish_yn_lang='1' AND*/

		allow_publish='1'AND reporeID=lan_repore_id AND lang_id='" . $this->db->escape($this->cms->lang_id) . "' OR dokuser='$userid' AND publish_yn_intra='1'  AND allow_publish='1' AND reporeID=lan_repore_id AND lang_id='" . $this->db->escape($this->cms->lang_id) . "' ORDER BY header ASC";
		// Ergebniss zuweisen für das Template
		$result2 = $this->db->get_results($suchergebniss2);

		if (!empty ($result2)) {
			foreach ($result2 as $row) {

				$temp_array = array();
				$temp_array['ueberschrift'] = $row->header;
				$temp_array['reporeID'] = $row->reporeID;
				$temp_array['teaser'] = $row->lan_teaser;
				//$temp_array['timestamp'] = $row->timestamp;
				$temp_array['date_time'] = $row->timestamp;
				$sql=sprintf("SELECT username FROM %s WHERE userid='%d'",
					$this->cms->tbname['papoo_user'],
					$row->dokuser_last
				);
				$temp_array['username'] =$this->db->get_var($sql);
				IfNotSetNull($row->username);
				if (($temp_array['username']<10)) {
					$temp_array['username']=$row->username;
				}
				$temp_array['autor']=$row->username;

				// Daten zuweisen
				$this->content->template['link_data'][] = $temp_array;
			}
		}

	}

	/**
	 * Hier werden alle publizierten Artikel aufgezeigt
	 *
	 * @return void
	 */
	function artikel_published()
	{
		// alle Artikel anzeigen -> Template
		$this->content->template['artikel_alle']= '1';
		// Limit setzen
		$this->sqllimit="";
		// Artikel auslesen
		$this->check_publish();
		// ANzahl der Artikel auslesen
		$result_anzahl = $this->resultzahl2;
		// Daten der Artikel zuweisen
		// Limit setzen
		$this->sqllimit=$this->cms->sqllimit;
		// Artikel auslesen
		$this->check_publish_artikel();

		if (!empty($this->result2)) {
			$this->content->template['link_data']=array();
			foreach ($this->result2 as $row) {
				$textartikel = "<div class=\"artikel_list_teaser\"><h3>" . $row->header . "</h3><p >" . $row->lan_teaser . "</p>";

				$textueberschrift = ($row->header);

				$reporeID = $row->reporeID;
				$textart = $textartikel . "<a class=\"artikel_link_teaser\" href=\"artikel.php?menuid=11&obermenuid=5&untermenuid=11&reporeid=$reporeID\">  $textueberschrift  ".$this->content->template['message_440']." </a><br /></div>";
				// if ($bild) echo"<img class=\"bildrechts\" src=\"thumb/$bild\" />";
				array_push($this->content->template['link_data'], array(
					'textart' => $textart,));
			}
		}
		$this->weiter->weiter_link = "./index.php?menuid=".$this->checked->menuid."&obermenuid=&untermenuid=&myhome=4";
		$this->weiter->result_anzahl= $result_anzahl;
		$this->weiter->do_weiter("teaser");
	}

	function do_pers() {
		// Wenn das Formular Übermittelt wurde
		if (!empty($this->checked->submithome)) {
			// Userdaten updaten
			$this->update_user();
		}
		else {
			$sql = sprintf(
				"SELECT user_list_artikel FROM %s WHERE username='%s'",
				$this->cms->papoo_user,
				$this->user->username
			);
			$user_list_artikel=$this->db->get_var($sql);

			$this->content->template['persdaten']= '1';
			$this->content->template['formusername']=$this->user->username;
			$this->content->template['user_list_artikel']=$user_list_artikel;

			// Editor der verwendet werden soll

			$checkededitor_tiny =
				($this->user->editor == 3 or empty($this->user->editor) or $this->user->editor == 0) ? 'nodecode:checked="checked"' : "";

			$checkededitor_quick = ($this->user->editor == 2) ? 'nodecode:checked="checked"' : "";

			$checkededitor_markdown = ($this->user->editor == 1) ? 'nodecode:checked="checked"' : "";

			$menuinput_data=array();
			if (empty($checkededitor_default)) {
				$checkededitor_default="";
			}
			if (empty($checkededitor_fremd)) {
				$checkededitor_fremd="";
			}
			if (empty($checkededitor_screen)) {
				$checkededitor_screen="";
			}
			array_push($menuinput_data, array(
				'checkededitor_default' => $checkededitor_default,
				'checkededitor_tiny' => $checkededitor_tiny,
				'checkededitor_fremd' => $checkededitor_fremd,
				'checkededitor_screen' => $checkededitor_screen,
				'checkededitor_quick' => $checkededitor_quick,
				'checkededitor_markdown' => $checkededitor_markdown,
			));

			$this->content->template['menuinput_data']=$menuinput_data;
		}
	}


	/**
	 * Hier werden die persönlichen Userdaten geändert
	 *
	 * @return void
	 */
	function update_user()
	{
		// ändern der Editor Daten
		if ($this->checked->editor != "")
		{
			$sqled=", editor='".$this->db->escape($this->checked->editor)."' ";
			//Session setzen
			$_SESSION['sessioneditor'] = $this->checked->editor;
		}
		else {
			$sqled="";
		}
		// Änderung in "ClubStufe" an SESSION Übergeben
		$user_club_stufe = 0;
		if ($this->checked->user_club_stufe) {
			$user_club_stufe = 1;
			$_SESSION['user_club_stufe'] = $this->checked->user_club_stufe;
			$_SESSION['dbp']['papoo_user_club_stufe'] = $this->checked->user_club_stufe;
		}
		else {
			$_SESSION['user_club_stufe'] = 0;
			$_SESSION['dbp']['papoo_user_club_stufe'] = 0;
		}
		// Änderung in Anzeige Auf-Zu-Klapp-Baum
		$temp_user_content_tree_show_all = 0;
		if ($this->checked->user_content_tree_show_all) {
			$temp_user_content_tree_show_all = 1;
		}
		$_SESSION['user_content_tree_show_all'] = $temp_user_content_tree_show_all;
		$_SESSION['dbp']['user_content_tree_show_all'] = $temp_user_content_tree_show_all;

		// FIXME: $sqlpa $sqlem nicht gesetzt, was war hier die Verwendung?

		$sql = sprintf("UPDATE %s
						SET username='%s' %s %s %s
						, user_list_artikel='%s', user_club_stufe='%s', user_content_tree_show_all='%d'
						WHERE userid='%d'",
			$this->cms->papoo_user,
			$this->user->username,
			$sqlpa,
			$sqlem,
			$sqled,
			$this->db->escape($this->checked->user_list_artikel),
			$this->db->escape($user_club_stufe),
			$temp_user_content_tree_show_all,
			$this->user->userid
		);

		$this->db->query($sql);

		//header ("Location: ./index.php?menuid=38&messageget=1");
		$location_url = "./index.php?menuid=38&messageget=1";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="'.$location_url.'">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$intern_home= new intern_home();