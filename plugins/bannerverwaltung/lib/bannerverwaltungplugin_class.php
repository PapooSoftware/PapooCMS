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
 * Class bannerverwaltung
 */
class bannerverwaltung {

	/** @var string  */
	var $news = "";

	/**
	 * bannerverwaltung constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		global $cms, $db, $message, $user, $menu, $content, $searcher, $checked, $mail_it,
			   $replace, $db_praefix, $intern_stamm, $intern_artikel, $diverse, $weiter;

		// und einbinden in die Klasse

		// Hier die Klassen als Referenzen
		$this->cms = & $cms;
		$this->weiter = & $weiter;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->menu = & $menu;
		$this->searcher = & $searcher;
		$this->checked = & $checked;
		$this->mail_it = & $mail_it;
		$this->intern_stamm = & $intern_stamm;
		$this->replace = & $replace;
		$this->diverse = & $diverse;
		$this->intern_artikel = & $intern_artikel;
		$this->make_bannerverwaltung();
		$this->content->template['plugin_message'] = "";
		$this->post_papoo();
	}

	/**
	 * Interne Verwaltung
	 */
	function make_bannerverwaltung()
	{

		global $template;

		$template2 =str_ireplace(PAPOO_ABS_PFAD."/plugins/","",$template);
		switch ($template2) {

			//Die Standardeinstellungen werden bearbeitet
		case "bannerverwaltung/templates/bannerverwaltungplugin.html" :
			$this->check_pref();
			break;

			//Ein Banner wird eingebunden
		case "bannerverwaltung/templates/bannerverwaltungplugin_create.html" :
			$this->new_banner();
			break;

			//Ein Banner wird bearbeitet
		case "bannerverwaltung/templates/bannerverwaltungplugin_edit.html" :
			$this->edit_banner();
			break;

			//Einen Dump erstellen oder einspielen
		case "bannerverwaltung/templates/bannerverwaltungplugin_dump.html" :
			$this->banner_dump();
			break;

		default:
			break;
		}
	}

	/**
	 * Was im Frontend passiert
	 *
	 */
	function post_papoo()
	{
		if (!defined("admin")) {
			$datum = date("Y-m-d",time());
			//Rausbekommen ob zuf�llig oder linear
			$sql=sprintf("SELECT * FROM %s ",
				$this->cms->tbname['papoo_bannerverwaltung_pref']
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			if (empty($result[0]['bannerverwaltung_wieviel']) or $result[0]['bannerverwaltung_wieviel'] == 0 ){
				$result[0]['bannerverwaltung_wieviel'] = 1;
			}

			//Wenn sql Datei bereitgestellt werden soll, diese erzeugen
			if (!empty($result[0]['bannerverwaltung_url'])) {
				$this->diverse->get_sql("bannerverwaltung","bannerverwaltung_pref",$result[0]['bannerverwaltung_url']);
			}
			$bannermodule_array=array(1,2,3,4,5,6,7,8,9,10);
			$result_allx=array();

			foreach ($bannermodule_array as $modid) {
				//alle Eintr�ge rausholen die immer angezeigt werden sollen
				$sql=sprintf("SELECT * FROM %s 
											WHERE banner_menuid ='all' 
											AND banner_start <='%s' 
											AND banner_stop >='%s' 
											AND banner_lang ='%s' 
											AND banner_modulid ='%d' 
											ORDER BY RAND() 
											LIMIT %s",
					$this->cms->tbname['papoo_bannerverwaltung_daten'],
					$datum,
					$datum,
					$this->cms->lang_short,
					$modid,
					$result[0]['bannerverwaltung_wieviel']
				);
				$result_all=$this->db->get_results($sql,ARRAY_A);
				if (!is_array($result_all)) {
					$result_all=array();
				}
				$result_allx=array_merge($result_allx,$result_all);
			}
			$result_all=$result_allx;

			//alle Eintr�ge zum Men�punkt
			if (!empty($this->checked->menuid)){
				$sql=sprintf("SELECT DISTINCT * FROM %s ,%s  
										WHERE banner_mid = banner_id AND banner_menu_id='%s' 
										AND banner_start<='%s' 
										AND banner_stop>='%s' 
										AND banner_lang='%s'
										GROUP BY banner_mid 
										ORDER BY rand() 
                                        LIMIT %s",
					$this->cms->tbname['papoo_bannerverwaltung_daten'],
					$this->cms->tbname['papoo_banner_menu_lookup'],
					$this->db->escape($this->checked->menuid),
					$datum,
					$datum,
					$this->cms->lang_short,
					$result[0]['bannerverwaltung_wieviel']
				);
				$result_menu=$this->db->get_results($sql,ARRAY_A);
			}

			//Alle Eintr�ge zum Artikel
			if (!empty($this->checked->reporeid)) {
				$sql=sprintf("SELECT * FROM %s WHERE 
											banner_artikelid='%s' 
											AND banner_start<='%s' 
											AND banner_stop>='%s' 
											AND banner_lang='%s'
											",
					$this->cms->tbname['papoo_bannerverwaltung_daten'],
					$this->db->escape($this->checked->reporeid),
					$datum,
					$datum,
					$this->cms->lang_short
				);
				$result_artikel=$this->db->get_results($sql,ARRAY_A);
			}

			//Auswahl mergen, artikel>menu>immer
			if (isset($result_menu) && is_array($result_menu)) {
				$result_gesamt1=array_merge($result_menu,$result_all);
			}
			else {
				$result_gesamt1=$result_all;
			}

			IfNotSetNull($result_artikel);
			IfNotSetNull($result_gesamt2);
			IfNotSetNull($result_gesamt3_neu);

			if (is_array($result_artikel)) {
				$result_gesamt2=array_merge($result_artikel,$result_gesamt1);
			}

			if (is_array($result_gesamt2)) {
				$result_gesamt3=array_merge($result_gesamt2,$result_gesamt1);
			}
			else {
				$result_gesamt3=$result_gesamt1;
			}

			$i=0;
			foreach ($result_gesamt3 as $dat) {
				$result_gesamt3[$i]['banner_code']="nodecode:".$dat['banner_code'];
				$i++;
			}

			foreach ($result_gesamt3 as $dat) {
				$result_gesamt3_neu[$dat['banner_id']]=$dat;
				//$i++;
			}
			$result_gesamt3=$result_gesamt3_neu;

			//Z�hlen
			if (is_array($result_gesamt3)) {
				foreach ($result_gesamt3 as $banids) {
					$sql = sprintf("UPDATE %s SET banner_views = banner_views+1 WHERE banner_id = '%d'",
						$this->cms->db_praefix . "papoo_bannerverwaltung_daten",
						$this->db->escape($banids['banner_id'])
					);
					$this->db->query($sql);
				}
			}
			//Daten ausgeben
			$this->content->template['banner_result']=$result_gesamt3;
		}
	}

	/**
	 * Daten einstellen
	 * Funktion gibt nichts zur�ck, sondern �bergibt nur Daten an das Template
	 * Ge�nderte Daten werden eingetragen
	 */
	function check_pref()
	{
		//wenn eintragen
		if (!empty ($this->checked->submitbanner)) {
			//Datenbank updaten
			$sql = sprintf("UPDATE %s SET bannerverwaltung_wieviel='%s', bannerverwaltung_export='%s', bannerverwaltung_import='%s',
              bannerverwaltung_url='%s' WHERE bannerverwaltung_id='1'",
				$this->cms->tbname['papoo_bannerverwaltung_pref'],
				$this->db->escape($this->checked->bannerverwaltung_wieviel),
				$this->db->escape($this->checked->bannerverwaltung_export),
				$this->db->escape($this->checked->bannerverwaltung_import),
				$this->db->escape($this->checked->bannerverwaltung_url)
			);
			$this->db->query($sql);
		}

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_bannerverwaltung_pref']);
		$result = $this->db->get_results($sql, ARRAY_A);

		//Wenn sql Datei bereitgestellt werden soll, diese erzeugen
		if ($result[0]['bannerverwaltung_export']==1) {
			$this->diverse->make_sql("bannerverwaltung");
		}

		$this->content->template['xml_result']=$result;

		//Checken ob alt
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_banner_menu_lookup']);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (empty($result)) {
			//Checken ob alt
			$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_bannerverwaltung_daten']);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					//Daten in Lookup eintragen
					$sql=sprintf("INSERT INTO %s SET banner_mid='%d', banner_menu_id='%d'",
						$this->cms->tbname['papoo_banner_menu_lookup'],
						$value['banner_id'],
						$value['banner_menuid']

					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Liste der Artikel besorgen
	 */
	function get_liste_artikel()
	{
		$this->intern_artikel->replangid=$this->cms->lang_id;

		$this->intern_artikel->make_artikel_list("all");

		$this->content->template['artikel_liste'] = $this->intern_artikel->result_liste ;
	}

	/**
	 * Liste der Men�punkte besorgen
	 */
	function get_liste_menu()
	{
		$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");
		$this->content->template['menulist_data'] = $this->menu->data_front_complete;
	}

	/**
	 * bannerverwaltung::do_redir_link()
	 *
	 * @param string $link
	 * @param int $id
	 * @return string korrigierter Link
	 */
	function do_redir_link($link="", $id=0)
	{
		$redir="href=\"".PAPOO_WEB_PFAD."/plugins/bannerverwaltung/bannerredirect.php?bannerid=".$id."&url=http://www";
		$redir2="href=\"".PAPOO_WEB_PFAD."/plugins/bannerverwaltung/bannerredirect.php?bannerid=".$id."&url=http://";
		$link=str_ireplace("href=\"http://www",$redir,$link);
		$link=str_ireplace("href=\"http://",$redir2,$link);
		return $link;
	}

	/**
	 * bannerverwaltung::do_redir_link()
	 *
	 * @param string $link
	 * @param int $id
	 * @return string korrigierter Link
	 */
	function undo_redir_link($link="", $id=0)
	{
		$redir2=PAPOO_WEB_PFAD.'/plugins/bannerverwaltung/bannerredirect.php\?bannerid='.$id.'&url=http://';
		$link=str_ireplace($redir2,"http://",$link);
		return $link;
	}

	/**
	 * Neuen Banner erstellen
	 */
	function new_banner()
	{
		//eintragmakeartikelfertig
		if (isset($this->checked->fertig) && $this->checked->fertig=="drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		$this->content->template['neuereintrag'] = "ok";
		$this->get_liste_artikel();
		$this->get_liste_menu();
		if (!empty ($this->checked->submitentry)) {
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->banner_start,$res);
			$this->checked->banner_start=$res[3]."-".$res[2]."-".$res[1];
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->banner_stop,$res);

			$this->checked->banner_stop=$res[3]."-".$res[2]."-".$res[1];

			if (empty($this->checked->banner_lang)) {
				$this->checked->banner_lang=$this->cms->lang_short;
			}

			$sql = sprintf("INSERT INTO %s SET banner_name='%s', banner_code='%s', banner_menuid='%s', banner_artikelid='%s',
                   banner_modulid='%s', banner_start='%s', banner_stop='%s',banner_count_yn='%d', banner_lang='%s' ",
				$this->cms->tbname['papoo_bannerverwaltung_daten'],
				$this->db->escape($this->checked->banner_name),
				$this->db->escape($this->checked->banner_code),
				$this->db->escape($this->checked->banner_menuid),
				$this->db->escape($this->checked->banner_artikelid),
				$this->db->escape($this->checked->banner_modulid),
				$this->db->escape($this->checked->banner_start),
				$this->db->escape($this->checked->banner_stop),
				$this->db->escape($this->checked->banner_count_yn),
				$this->db->escape($this->checked->banner_lang)
			);
			$this->db->query($sql);
			$insertid=$this->db->insert_id;
			if ($this->checked->banner_count_yn==1) {
				$this->checked->banner_code=$this->do_redir_link($this->checked->banner_code,$insertid);
				$sql = sprintf("UPDATE %s SET banner_code='%s' WHERE banner_id='%s' ",
					$this->cms->tbname['papoo_bannerverwaltung_daten'],
					$this->db->escape($this->checked->banner_code),
					$this->db->escape($insertid)
				);
				$this->db->query($sql);
			}

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.$this->content->template['plugin']['akquise']['weiter'].'</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * Banner bearbeiten
	 *
	 */
	function edit_banner()
	{
		//Es soll eingetragen werden
		if (isset($this->checked->submitentry) && $this->checked->submitentry) {
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->banner_start,$res);
			$this->checked->banner_start=$res[3]."-".$res[2]."-".$res[1];
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->banner_stop,$res);
			$this->checked->banner_stop=$res[3]."-".$res[2]."-".$res[1];
			if (empty($this->checked->banner_lang)) {
				$this->checked->banner_lang=$this->cms->lang_short;
			}
			if ($this->checked->banner_count_yn==1) {
				$this->checked->banner_code=$this->do_redir_link($this->checked->banner_code,$this->checked->banner_id);
			}

			$sql = sprintf("UPDATE %s SET  
											banner_name='%s', 
											banner_code='%s', 
											banner_menuid='%s', 
											banner_artikelid='%s', 
											banner_modulid='%s', 
											banner_start='%s', 
											banner_stop='%s', 
											banner_lang='%s',
											banner_count_yn='%d' 
											WHERE banner_id='%s' ",
				$this->cms->tbname['papoo_bannerverwaltung_daten'],
				$this->db->escape($this->checked->banner_name),
				$this->db->escape($this->checked->banner_code),
				$this->db->escape($this->checked->banner_menuid),
				$this->db->escape($this->checked->banner_artikelid),
				$this->db->escape($this->checked->banner_modulid),
				$this->db->escape($this->checked->banner_start),
				$this->db->escape($this->checked->banner_stop),
				$this->db->escape($this->checked->banner_lang),
				$this->db->escape($this->checked->banner_count_yn),
				$this->db->escape($this->checked->banner_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->cat_id);

			//Die Lookups f�r Men�punkte eintragen
			if (is_array($this->checked->inhalt_ar['cattext_ar'])) {
				$sql=sprintf("DELETE FROM %s WHERE banner_mid='%d'",
					$this->cms->tbname['papoo_banner_menu_lookup'],
					$this->db->escape($this->checked->banner_id)
				);
				$this->db->query($sql);

				foreach ($this->checked->inhalt_ar['cattext_ar'] as $key=>$value)
				{
					$sql=sprintf("INSERT INTO %s SET 
												banner_mid='%d',
												banner_menu_id='%d'
												",
						$this->cms->tbname['papoo_banner_menu_lookup'],
						$this->db->escape($this->checked->banner_id),
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.$this->content->template['plugin']['akquise']['weiter'].'</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		$this->get_liste_artikel();
		$this->get_liste_menu();

		if (!empty ($this->checked->bannerid)) {
			//Nach id aus der Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE banner_id='%s'",
				$this->cms->tbname['papoo_bannerverwaltung_daten'],
				$this->db->escape($this->checked->bannerid)
			);

			$result = $this->db->get_results($sql);
			if (!empty ($result)) {
				foreach ($result as $glos) {
					$this->content->template['banner_id'] = $glos->banner_id;
					$this->content->template['banner_name'] = $glos->banner_name;
					$this->content->template['banner_code'] = $this->undo_redir_link("nodecode:".$glos->banner_code,$glos->banner_id);
					$this->content->template['banner_menuid'] = $glos->banner_menuid;
					$this->content->template['banner_artikelid'] = $glos->banner_artikelid;
					$this->content->template['banner_modulid'] = $glos->banner_modulid;
					$this->content->template['banner_start'] = $glos->banner_start;
					$this->content->template['banner_stop'] = $glos->banner_stop;
					$this->content->template['banner_lang'] = $glos->banner_lang;
					$this->content->template['banner_count_yn'] = $glos->banner_count_yn;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
				}
			}
			preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['banner_start'],$res);
			$this->content->template['banner_start']=$res[3].".".$res[2].".".$res[1];
			preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['banner_stop'],$res);
			$this->content->template['banner_stop']=$res[3].".".$res[2].".".$res[1];

			//Lookups
			$sql=sprintf("SELECT * FROM %s WHERE  banner_mid='%d'",
				$this->cms->tbname['papoo_banner_menu_lookup'],
				$this->db->escape($this->checked->bannerid)
			);
			$this->content->template['banner_ar'] = $this->db->get_results($sql,ARRAY_A);
		}
		//alle anzeigen
		else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			$sql = sprintf("SELECT * FROM %s ORDER BY banner_name ASC",
				$this->cms->tbname['papoo_bannerverwaltung_daten']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			/*preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['list_dat']['banner_start'],$res);
			//Was ist das???
			#$this->content->template['list_dat']['banner_start']=$res[3].".".$res[2].".".$res[1];
			preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['banner_stop'],$res);
			$this->content->template['banner_stop']=$res[3].".".$res[2].".".$res[1];*/
			$this->content->template['banner_link'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&bannerid=";
		}

		//Anzeigen das Eintrag gel�scht wurde
		if (isset($this->checked->fertig) && $this->checked->fertig == "del") {
			$this->content->template['deleted'] = "ok";
		}
		//Soll  gel�scht werden
		if (!empty ($this->checked->submitdelecht)) {
			//Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s WHERE banner_id='%s'",
				$this->cms->tbname['papoo_bannerverwaltung_daten'],
				$this->db->escape($this->checked->banner_id)
			);
			$this->db->query($sql);

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=del";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.$this->content->template['plugin']['akquise']['weiter'].'</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		//Soll wirklich gel�scht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['banner_name'] = $this->checked->banner_name;
			$this->content->template['banner_id'] = $this->checked->banner_id;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * @abstract Die Bannerdaten dumpen und wieder zur�ckspielen
	 */
	function banner_dump()
	{
		$this->diverse->extern_dump("bannerverwaltung");
		//Sql Datei erneuern
		$sql=sprintf("SELECT bannerverwaltung_export FROM %s",
			$this->cms->tbname['papoo_bannerverwaltung_pref']
		);
		$this->export_xml=$this->db->get_var($sql);
		if ($this->bannerverwaltung_export==1){
			#$this->diverse->make_sql("linkliste");
		}
	}
}

$bannerverwaltung = new bannerverwaltung();