<?php


/**
#####################################
# CMS Papoo                								 #
# (c) Dr. Carsten Euwens 2008       							 #
# Authors: Carsten Euwens                                             #
# http://www.papoo.de                                                   #
# Internet                                                                         #
#####################################
# PHP Version >4.2                                                           #
#####################################

















*/

/**

*/

#[AllowDynamicProperties]
class linklisteplugin {

	//  ...
	var $news = "";

	// Konstruktor
	function __construct() {
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
		// Mail Klasse einbinden
		global $mail_it;
		//Ersetzungsklasse
		global $replace;
		// Datenbank Praefix einbinden
		global $db_praefix;
		//interne Stammklasse
		global $intern_stamm;
		//Diverse Klasse einbinden
		global $diverse;
		global $dumpnrestore;
		$this->dumpnrestore = & $dumpnrestore;

		/**
		und einbinden in die Klasse
		*/

		// Hier die Klassen als Referenzen
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->weiter = & $weiter;
		$this->searcher = & $searcher;
		$this->checked = & $checked;
		$this->mail_it = & $mail_it;
		$this->intern_stamm = & $intern_stamm;
		$this->replace = & $replace;
		$this->diverse = & $diverse;
		$this->make_linklisteplugin();
		$this->make_lang_var();
		$this->content->template['plugin_message'] = "";

	}

	/*
	*
	*/

	/*
	* linkliste erstellen
	*/
	function make_linklisteplugin() {

		//echo $this->checked->template;
		if (defined("admin")){

			global $template;
			if ($template!="login.utf8.html"){

				switch ($this->checked->template) {

					//Die Standardeinstellungen werden bearbeitet
					case "../../plugins/linkliste/templates/linklisteplugin.html" :
					$this->check_pref();
					break;

					//Einen Eintrag erstellen
					case "../../plugins/linkliste/templates/linklisteplugin_create.html" :
					$this->make_entry();
					break;

					//Einen EIntrag bearbeiten
					case "../../plugins/linkliste/templates/linklisteplugin_edit.html" :
					$this->change_entry();
					break;

					//Eine e Kategorie erstellen
					case "../../plugins/linkliste/templates/linklistecat_create.html" :
					$this->make_cat_entry();
					break;

					//Einee Kategorie bearbeiten
					case "../../plugins/linkliste/templates/linklistecat_edit.html" :
					$this->change_cat_entry();
					break;

					//Einen Dump erstellen oder einspielen
					case "../../plugins/linkliste/templates/linklisteplugin_dump.html" :
					$this->linkliste_dump();
					break;

					//Linkeintr�ge sortieren
					case "../../plugins/linkliste/templates/linklistesort.html" :
					$this->linkliste_sort();
					break;

					//Kategorien sortieren
					case "../../plugins/linkliste/templates/linklistecat_sort.html" :
					$this->linkliste_sort_cat();
					break;

					default :

					break;

				}
			}
		}

	}
	/*
	* Sprachdateien einbinden, nach Sprache switchen
	*/

	function make_lang_var() {
		// Pfad einbinden
		global $pfad;

		// Backend Sprache
		if ($this->cms->lang_backend == "de") {
			// alle messages einbinden (deutsch, sp�ter auch mehr, daher $cms eingebunden)
			require_once $pfad."/plugins/linkliste/lib/messages/messages_linkliste_de.inc.php";
		}
		if ($this->cms->lang_backend == "en") {

			// alle messages einbinden (deutsch, sp�ter auch mehr, daher $cms eingebunden)
			require_once $pfad."/plugins/linkliste/lib/messages/messages_linkliste_en.inc.php";
		}

	}

	/**
	 * @abstract  Wenn wir drau�en sind
	 */
	function post_papoo() {

		//Es ist kein Eintrag ausgew�hlt, daher entweder die Links oder komplett anzeigen
		if (!defined("admin")){
			global $template;
			if (stristr($template,"linkliste_do_front.html")){
				//Sprachen ermitteln die eingestellt
				$lang_id= $this->cms->lang_id;
				$sql=sprintf("SELECT *  FROM %s ",
				$this->cms->tbname['papoo_linkliste_pref']
				);
				$result=$this->db->get_results($sql);
				#print_r($result);
				$show=$result['0']->linkliste_show;
				$langstring=$result['0']->linkliste_lang;
				$xml_link=$result['0']->linkliste_xml_link;
				if (!empty($xml_link)){
					$this->diverse->get_sql("linkliste","pref");
				}
				$langarray=explode(";",$langstring);
				if (!in_array($this->cms->lang_id,$langarray) and $this->cms->lang_id>2){
					$lang_id=2;
				}
				//komoplette Liste anzeigen
				if ($show==1){
					$this->cat_result=array();
					$this->get_cat_list_sort('0','0',$lang_id);
					$result2=$this->cat_result;
					#print_r($result2);
					$this->cat_result=array();
					$i=0;
					if (!empty($result2)){
						foreach ($result2 as $res){
							//Alle Links aus der Datenbank holen
							$sql=sprintf("SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id ",
							$this->cms->tbname['papoo_linkliste_daten'],
							$this->cms->tbname['papoo_linkliste_daten_lang'],
							$lang_id,
							$res['cat_id']
							);
							$result2[$i]['glist']=$this->db->get_results($sql, ARRAY_A);
							$i++;

						}
					}
					#print_r($result2);
					#print_r($result);
					$this->content->template['result']=$result;
					$this->content->template['show_komplett']="komplett";

					$this->content->template['categories']=$result2;

				}
				//Liste mit unterkategorien anzeigen
				if ($show==2){
					if (empty($this->checked->cat_level)){
						$this->checked->cat_level=0;
					}

					if (empty($this->checked->cat_sub)){
						$this->checked->cat_sub=0;
					}
					if (!empty($this->checked->cat_id)){
						$this->checked->cat_level++;
					}
					//Liste raussuchen die auf der aktuellen Ebene darzustellen sind.
					$sql=sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' AND cat_level='%s' AND cat_sub='%s' ORDER BY cat_order_id",
					$this->cms->tbname['papoo_linkliste_cat'],
					$this->cms->tbname['papoo_linkliste_lang_cat'],
					$this->db->escape($this->checked->cat_level),
					$this->db->escape($this->checked->cat_id)
					);
					$result2=$this->db->get_results($sql, ARRAY_A);
					#print_r($result2);
					$i=0;

					if (!empty($result2)){
						foreach ($result2 as $res){
							//UNterkategorien raussuchen
							$sql_sub=sprintf("SELECT COUNT(cat_id) FROM %s WHERE cat_sub='%s'",
							$this->cms->tbname['papoo_linkliste_cat'],
							$this->db->escape($res['cat_id'])
							);
							$result2[$i]['sub']=$this->db->get_var($sql_sub);
							$i++;
						}
					}
					//Alle Links aus der Datenbank holen
					$sql=sprintf("SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id ",
					$this->cms->tbname['papoo_linkliste_daten'],
					$this->cms->tbname['papoo_linkliste_daten_lang'],
					$lang_id,
					$this->db->escape($this->checked->cat_id)
					);
					$result=$this->db->get_results($sql, ARRAY_A);

					$this->content->template['result']=$result;
					$this->content->template['categories']=$result2;
					$this->content->template['show_link']="link";

					//2. Brotkrumen f�r die Linkliste
					$sqls=sprintf("SELECT cat_sub FROM %s WHERE cat_id='%s' ",
					$this->cms->tbname['papoo_linkliste_cat'],
					$this->db->escape($this->checked->cat_id)
					);

					$catid=$this->db->get_var($sqls);
					//Aktuelle Kategorie
					$sqln=sprintf("SELECT cat_name FROM %s,%s WHERE cat_id='%s' AND cat_id=cat_id_id AND cat_lang_id='%s' ",
					$this->cms->tbname['papoo_linkliste_cat'],
						$this->cms->tbname['papoo_linkliste_lang_cat'],
						$this->db->escape($this->checked->cat_id),
						$this->cms->lang_id
					);

					$cataktuellername=$this->db->get_var($sqln);
					$this->content->template['cataktuellername']=$cataktuellername;
					
					if ($catid!=0){
						$this->do_brot($catid);
						#print_r($this->catid);
						$catarray=array();


						$sqlc=sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'",
						$this->cms->tbname['papoo_linkliste_cat'],
						$this->cms->tbname['papoo_linkliste_lang_cat'],
						$this->db->escape($this->checked->cat_id),
						$this->cms->lang_id
						);
						$catname=$this->db->get_results($sqlc,ARRAY_A);
						$catarray=array_merge_recursive($catname,$catarray);
						#print_r($catarray);

						foreach ($this->catid as $id){
							#print_r($id);
							$sqlc=sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'",
							$this->cms->tbname['papoo_linkliste_cat'],
							$this->cms->tbname['papoo_linkliste_lang_cat'],
							$this->db->escape($id['cat_id']),
							$this->cms->lang_id
							);
							$catname=$this->db->get_results($sqlc,ARRAY_A);
							#print_r($catname);
							$catarray=array_merge_recursive($catname,$catarray);

						}

						#print_r($catarray);
						$catname=$catarray;

					}
					else {
						$sqlc=sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_id_id AND cat_id='%s' AND cat_lang_id='%s'",
						$this->cms->tbname['papoo_linkliste_cat'],
						$this->cms->tbname['papoo_linkliste_lang_cat'],
						$this->db->escape($this->checked->cat_id),
						$this->cms->lang_id
						);
						$catname=$this->db->get_results($sqlc,ARRAY_A);
						#print_r($catname);
					}

				}
				#print_r($result2);
				$this->content->template['catname']=$catname;

				$this->content->template['menuid_aktuell']=$this->checked->menuid;
				$this->content->template['template']=$this->checked->template;
				$this->content->template['linktext']="plugin.php";
				if ($this->cms->mod_rewrite==2){
					$this->content->template['linktext']="plugin";
				}
			}
		}

	}

	/**
	 * Linklisten Brotkrumen anzeigen
	 */
	function do_brot($cat_id){
		$sql=sprintf("SELECT cat_sub FROM %s WHERE cat_id='%s' ",
		$this->cms->tbname['papoo_linkliste_cat'],
		$cat_id
		);
		$this->catid[$cat_id]['cat_sub']=$ergebnis=$this->db->get_var($sql);
		$this->catid[$cat_id]['cat_id']=$cat_id;
		if ($ergebnis>0){
			$this->do_brot($ergebnis);
		}
	}

	/**
	 * Die Kategorienliste erstellen
	 */

	function get_cat_list(){
		$sql=sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ",
		$this->cms->tbname['papoo_linkliste_cat'],
		$this->cms->tbname['papoo_linkliste_lang_cat']
		);
		$sql2=sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ORDER BY cat_level,cat_order_id ",
		$this->cms->tbname['papoo_linkliste_cat'],
		$this->cms->tbname['papoo_linkliste_lang_cat']
		);
		$result=$this->db->get_results($sql, ARRAY_A);
		$result2=$this->db->get_results($sql2, ARRAY_A);
		#print_r($result);
		$this->content->template['result_cat']=$result;
		return $result2;
	}

	/**
	 * @abstract Die Standardeinstellungen f�r das Glossar werden hier eingestellt
	 * @return checked2 oder checked1
	 * 
	 */
	function check_pref() {
		#$this->diverse->make_sql("linkliste");
		#$this->diverse->get_sql("linkliste","pref");

		$lang="0";
		#print_r($this->checked->lang);
		//Die Einstellungen sollen ver�ndert werden
		if (!empty ($this->checked->submitglossar)) {
			//Sprache
			if (empty($this->checked->lang)){
				$lang=1;

			}
			else {
				foreach ($this->checked->lang as $rein){
					$lang.=";".$rein;
				}
			}

			//Datenbank updaten linkliste_show
			$sql = sprintf("UPDATE %s SET linkliste_xml_yn='%s', linkliste_xml_link='%s', linkliste_lang='%s', linkliste_liste='%s',linkliste_show='%s' WHERE linkliste_id='1'",
			$this->cms->tbname['papoo_linkliste_pref'],
			$this->db->escape($this->checked->linkliste_xml_yn),
			$this->db->escape($this->checked->linkliste_xml_link),
			$lang,
			$this->db->escape($this->checked->linkliste_liste),
			$this->db->escape($this->checked->linkliste_show)
			);
			$this->db->query($sql);

		}
		$this->make_lang();

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_linkliste_pref']);
		$result = $this->db->get_results($sql, ARRAY_A);
		#print_r($result);
		$this->content->template['xml_result']=$result;
	}


	/**
	 * Spracheinstellungen ausgeben
	 *
	 */

	function make_lang(){
		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results("SELECT * FROM ".$this->cms->papoo_name_language."  ");
		$resultlang2 = $this->db->get_var("SELECT linkliste_lang FROM ".$this->cms->tbname['papoo_linkliste_pref']."  ");
		$lang=explode(";",$resultlang2);
		#print_r($lang);
		$this->content->template['language']=array();
		// zuweisen welche Sprache ausgew�hlt sind
		foreach ($resultlang as $rowlang) {
			// chcken wenn Sprache gew�hlt
			if (in_array($rowlang->lang_id,$lang)) {
				$selected_more = 'nodecode:checked="checked"';
			} else {
				$selected_more = "";
			}
			// �berspringen, wenn Sprache als Standard ausgew�hlt ist
			if ($row->lang_frontend != $rowlang->lang_short) {
				array_push($this->content->template['language'], array (
				'language' => $rowlang->lang_long,
				'lang_id' => $rowlang->lang_id,
				'selected' => $selected_more,
				));
			}
		}
	}

	/**
	 * Eine Kategorie erstellen
	 */
	function make_cat_entry(){
		//Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();

		if ($this->checked->submitentry) {
			//Level rasubekommen
			$sql2=sprintf("SELECT cat_level FROM %s WHERE cat_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub)
			);
			$cat_level = $this->db->get_var($sql2);
			if (!empty($this->checked->cat_sub)){
				$cat_level++;
			}
			else {
				$cat_level="0";
			}
			//Gr��ten Order_id Eintrag rausbekommen in dieser Ebene
			$sql2=sprintf("SELECT MAX(cat_order_id) FROM %s WHERE cat_sub='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub)
			);
			$cat_orderid = $this->db->get_var($sql2);
			$cat_orderid++;

			//Daten eintragen in Datenbank
			$sql = sprintf("INSERT INTO %s SET cat_sub='%s', cat_level='%s', cat_order_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub),
			$this->db->escape($cat_level),
			$cat_orderid
			);
			$this->db->query($sql);
			$insertid=$this->db->insert_id;

			//Sprachdaten eintragen cat_name
			foreach ($this->checked->cat_name as $key => $value){
				$sql=sprintf("INSERT INTO %s SET cat_id_id='%s', cat_lang_id='%s', cat_name='%s'",
				$this->cms->tbname['papoo_linkliste_lang_cat'],
				$insertid,
				$this->db->escape($key),
				$this->db->escape($value)
				);
				$this->db->query($sql);
			}

			//Sql Datei erneuern
			$sql=sprintf("SELECT linkliste_xml_yn FROM %s",
			$this->cms->tbname['papoo_linkliste_pref']
			);
			$this->export_xml=$this->db->get_var($sql);
			if ($this->export_xml==1){
				$this->diverse->make_sql("linkliste");
			}

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;


		}
		else {
			$this->content->template['neuereintrag'] = "ok";
		}
		if ($this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}

	}

	/**
	 * Kategorie �ndern
	 */
	function change_cat_entry(){
		//Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();

		//Es soll eingetragen werden
		if ($this->checked->submitentry) {
			//Level rasubekommen
			$sql2=sprintf("SELECT cat_level FROM %s WHERE cat_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub)
			);
			$cat_level = $this->db->get_var($sql2);
			if (!empty($this->checked->cat_sub)){
				$cat_level++;
			}
			else {
				$cat_level="0";
			}

			;
			//Gr��ten Order_id Eintrag rausbekommen in dieser Ebene
			$sql2=sprintf("SELECT MAX(cat_order_id) FROM %s WHERE cat_sub='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub)
			);
			$cat_orderid = $this->db->get_var($sql2);
			$cat_orderid++;

			$sql = sprintf("UPDATE %s SET  cat_sub='%s', cat_level='%s', cat_order_id='%s' WHERE cat_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_sub),
			$this->db->escape($cat_level),
			$cat_orderid,
			$this->db->escape($this->checked->cat_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->cat_id);
			//Alle Sprachen linkliste_descrip
			$sql=sprintf("DELETE FROM %s WHERE cat_id_id='%s'",
			$this->cms->tbname['papoo_linkliste_lang_cat'],
			$insertid
			);
			$this->db->query($sql);

			//Sprachdaten eintragen cat_name
			foreach ($this->checked->cat_name as $key => $value){
				$sql=sprintf("INSERT INTO %s SET cat_id_id='%s', cat_lang_id='%s', cat_name='%s'",
				$this->cms->tbname['papoo_linkliste_lang_cat'],
				$insertid,
				$this->db->escape($key),
				$this->db->escape($value)
				);
				$this->db->query($sql);
			}
			//Sql Datei erneuern
			$sql=sprintf("SELECT linkliste_xml_yn FROM %s",
			$this->cms->tbname['papoo_linkliste_pref']
			);
			$this->export_xml=$this->db->get_var($sql);
			if ($this->export_xml==1){
				$this->diverse->make_sql("linkliste");
			}


			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;
		}

		if (!empty ($this->checked->glossarid)) {
			//Nach id aus der Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE cat_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->glossarid)
			);

			$result = $this->db->get_results($sql);
			#print_r($result);
			if (!empty ($result)) {
				foreach ($result as $glos) {
					$this->content->template['cat_id'] = $glos->cat_id;
					$this->content->template['cat_sub'] = $glos->cat_sub;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
				}
				//Sprachdaten raussuchen
				$sql=sprintf("SELECT * FROM %s WHERE cat_id_id='%s'",
				$this->cms->tbname['papoo_linkliste_lang_cat'],
				$this->db->escape($this->checked->glossarid)
				);
				$resultx = $this->db->get_results($sql);
				#print_r($resultx);
				if (!empty($resultx)){
					foreach ($resultx as $lang) {
						#print_r($lang);
						$wort[$lang->cat_lang_id] = $lang->cat_name;
					}

					$this->content->template['cat_name']=$wort;
				}
				#print_r($this->content->template['cat_name']);
			}
		}
		//alle anzeigen
		else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			//Daten rausholen
			$sql = sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1' ORDER BY cat_name ASC",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->cms->tbname['papoo_linkliste_lang_cat']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//print_r($result);
			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['link'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&glossarid=";
			;
		}
		if ($this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		//Anzeigen das Eintrag gel�scht wurde
		if ($this->checked->fertig == "del") {
			$this->content->template['deleted'] = "ok";
		}
		//Soll  gel�scht werden
		if (!empty ($this->checked->submitdelecht)) {
			//Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s WHERE cat_id='%s'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$this->db->escape($this->checked->cat_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->cat_id);
			//Alle Sprachen linkliste_descrip
			$sql=sprintf("DELETE FROM %s WHERE cat_lang_id='%s'",
			$this->cms->tbname['papoo_linkliste_lang_cat'],
			$insertid
			);
			$this->db->query($sql);
			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=del";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;

		}

		//Soll wirklich gel�scht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}


	}

	/**
	 * @abstract Ein neuer Eintrag wird erstellt und die Option zum Datenbank durchloopen angeboten
	 * @return 
	 */

	function make_entry() {

		//Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();

		if ($this->checked->submitentry) {

			//Daten eintragen in Datenbank
			$sql = sprintf("INSERT INTO %s SET  linkliste_link='%s', cat_linkliste_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$this->db->escape($this->checked->linkliste_link),
			$this->db->escape($this->checked->cat_linkliste_id)

			);
			$this->db->query($sql);
			$insertid=$this->db->insert_id;
			//Alle Sprachen linkliste_descrip
			foreach ($this->checked->linkliste_Wort as $key => $value) {
				$sql=sprintf("INSERT INTO %s SET linkliste_lang_id='%s', linkliste_lang_lang='%s', linkliste_Wort='%s', linkliste_descrip='%s'",
				$this->cms->tbname['papoo_linkliste_daten_lang'],
				$insertid,
				$key,
				$value,
				$this->db->escape($this->checked->linkliste_descrip[$key])
				);
				$this->db->query($sql);
			}
			//Sql Datei erneuern
			if ($this->export_xml==1){
				$this->diverse->make_sql("linkliste");
			}

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;


		} else {
			$this->content->template['neuereintrag'] = "ok";
		}

		if ($this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}

	}



	/**
	 * Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen
	 */
	function change_entry() {
		//Sprachen raussuchen
		$this->make_lang();
		$this->get_cat_list();


		//Es soll eingetragen werden
		if ($this->checked->submitentry) {

			$sql = sprintf("UPDATE %s SET  linkliste_link='%s', cat_linkliste_id='%s' WHERE linkliste_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$this->db->escape($this->checked->linkliste_link),
			$this->db->escape($this->checked->cat_linkliste_id),
			$this->db->escape($this->checked->linkliste_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->linkliste_id);
			//Alle Sprachen linkliste_descrip
			$sql=sprintf("DELETE FROM %s WHERE linkliste_lang_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten_lang'],
			$insertid
			);
			$this->db->query($sql);

			foreach ($this->checked->linkliste_Wort as $key => $value) {
				$sql=sprintf("INSERT INTO %s SET linkliste_lang_id='%s', linkliste_lang_lang='%s', linkliste_Wort='%s', linkliste_descrip='%s'",
				$this->cms->tbname['papoo_linkliste_daten_lang'],
				$insertid,
				$key,
				$value,
				$this->db->escape($this->checked->linkliste_descrip[$key])
				);
				$this->db->query($sql);
			}
			//Sql Datei erneuern
			$sql=sprintf("SELECT linkliste_xml_yn FROM %s",
			$this->cms->tbname['papoo_linkliste_pref']
			);
			$this->export_xml=$this->db->get_var($sql);
			if ($this->export_xml==1){
				$this->diverse->make_sql("linkliste");
			}



			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;
		}

		if (!empty ($this->checked->glossarid)) {
			//Nach id aus der Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE linkliste_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$this->db->escape($this->checked->glossarid)
			);

			$result = $this->db->get_results($sql);
			#print_r($result);
			if (!empty ($result)) {
				foreach ($result as $glos) {
					$this->content->template['linkliste_Wort'] = $glos->linkliste_Wort;
					$this->content->template['linkliste_descrip'] = "nobr:".$glos->linkliste_descrip;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
					$this->content->template['linkliste_id'] = $glos->linkliste_id;
					$this->content->template['linkliste_link'] = $glos->linkliste_link;
					$this->content->template['cat_linkliste_id'] = $glos->cat_linkliste_id;

				}
				//Sprachdaten raussuchen
				$sql=sprintf("SELECT * FROM %s WHERE linkliste_lang_id='%s'",
				$this->cms->tbname['papoo_linkliste_daten_lang'],
				$this->db->escape($this->checked->glossarid)
				);
				$resultx = $this->db->get_results($sql);
				#print_r($resultx);
				foreach ($resultx as $lang) {
					#print_r($lang);
					$wort[$lang->linkliste_lang_lang] = $lang->linkliste_Wort;
					$des[$lang->linkliste_lang_lang] = "nobr:".$lang->linkliste_descrip;
				}
				$this->content->template['linkliste_Wort']=$wort;
				$this->content->template['linkliste_descrip']=$des;
				#print_r($this->content->template['linkliste_descrip']);
			}
		} else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			//Daten rausholen
			$sql = sprintf("SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='1' ORDER BY linkliste_Wort ASC",
			$this->cms->tbname['papoo_linkliste_daten'],
			$this->cms->tbname['papoo_linkliste_daten_lang']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//print_r($result);
			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['link'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&glossarid=";
			;
		}
		if ($this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		//Anzeigen das Eintrag gel�scht wurde
		if ($this->checked->fertig == "del") {
			$this->content->template['deleted'] = "ok";
		}
		//Soll  gel�scht werden
		if (!empty ($this->checked->submitdelecht)) {
			//Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s WHERE linkliste_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$this->db->escape($this->checked->linkliste_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->linkliste_id);
			//Alle Sprachen linkliste_descrip
			$sql=sprintf("DELETE FROM %s WHERE linkliste_lang_id='%s'",
			$this->cms->tbname['papoo_linkliste_daten_lang'],
			$insertid
			);
			$this->db->query($sql);
			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=del";
			if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'.$location_url.'">Weiter</a>';
			else
			header("Location: $location_url");
			exit;
		}

		//Soll wirklich gel�scht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}

	}

	/**
	 * @abstract Die Glossardaten dumpen und wieder zur�ckspielen
	 */

	function linkliste_dump() {
		$this->diverse->extern_dump("linkliste");

		//Sql Datei erneuern
		$sql=sprintf("SELECT linkliste_xml_yn FROM %s",
		$this->cms->tbname['papoo_linkliste_pref']
		);
		$this->export_xml=$this->db->get_var($sql);
		if ($this->export_xml==1){
			$this->diverse->make_sql("linkliste");
		}
	}

	/**
	 * @abstract Glosarbeschreibung durchgehen um Erw�hnungen untereinander zu verlinken
	 */
	function selfcheck($daten,$wort){
		//Daten rausholen
		$sql = sprintf("SELECT * FROM %s ", $this->cms->tbname['papoo_glossar_daten']);
		$result=$this->db->get_results($sql);
		//print_r($result);
		
		$test = array ();
		$drin = array ();
		$neutext = ($daten);




		//Ersetzung durchf�hren
		foreach ($result as $var) {
			//echo ".".$var->glossar_Wort."-";
			/**
			 * 	$link= "<a href=\"".PAPOO_WEB_PFAD.'/plugin.php?menuid=1&glossar='.$var->glossar_id.'&template=../plugins/glossar/templates/glossarfront.html#'.$var->glossar_id."\">".$var->glossar_Wort."</a>";
				$neutext=str_ireplace($var->glossar_Wort,$link,$daten);
			 */


			if (stristr($daten,$var->glossar_Wort)){

				if ($var->glossar_Wort!=$wort){
					$drin['link_body'] = PAPOO_WEB_PFAD.'/plugin.php?menuid=1&glossar='.$var->glossar_id.'&template=../plugins/glossar/templates/glossarfront.html#'.$var->glossar_id;
					$drin['link_name'] =trim($var->glossar_Wort) ;
					$test['0'] = $drin;
					$this->replace->glossar = "ok";
					//print_r($test);
					$this->replace->links = $test;
					$neutext = $this->replace->name_to_link($daten);
				}
				else {

				}
			}
		}
		//echo "Q";
		return $neutext;
	}

	/**
	 * Alle Linkeintr�ge raussuchen
	 */
	function get_link_list($catid=""){
		//Alle Eintr�ge zu dieser Kategorie rausholen
		$sql=sprintf("SELECT * FROM %s, %s WHERE linkliste_id=linkliste_lang_id AND linkliste_lang_lang='%s' AND cat_linkliste_id='%s' ORDER BY linkliste_order_id",
		$this->cms->tbname['papoo_linkliste_daten'],
		$this->cms->tbname['papoo_linkliste_daten_lang'],
		1,
		$this->db->escape($catid)
		);
		$result2=$this->db->get_results($sql, ARRAY_A);
		$result3=$this->db->get_results($sql);
		#print_r($result2);
		$this->content->template['linklist_data'] = $result2;
		return $result3;
	}

	/**
	 * Linkliste sortieren
	 */
	function linkliste_sort(){

		//Liste der Kategorien raussuchen und �bergeben
		$this->get_cat_list();

		//catid aus Formular �bergeben cat_linkliste_id
		$this->content->template['cat_id'] = $this->checked->cat_linkliste_id;


		//Sortierung
		$this->order_change($this->checked->formartikelid,$this->checked->cat_linkliste_id);

		//Linkliste holen
		$this->get_link_list($this->checked->cat_linkliste_id);

	}

	/**
	 * Kategorien sortieren
	 */
	function linkliste_sort_cat(){

		$this->cat_result=array();
		$this->level= 0;

		if (!empty($this->checked->menuorder_menuid)){
			$this->order_change_cat($this->checked->formartikelid);
		}
		//Liste der Kategorien raussuchen und �bergeben
		$this->get_cat_list_sort();
		#print_r($this->cat_result);
		$this->content->template['result_cat'] =$this->cat_result;

	}


	/**
	 * Die Kategorienliste erstellen
	 */

	function get_cat_list_sort($catid="0",$level="0",$langid="1"){

		$sql=sprintf("SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s' ORDER BY cat_order_id",
		$this->cms->tbname['papoo_linkliste_cat'],
		$this->cms->tbname['papoo_linkliste_lang_cat'],
		$langid,
		$catid
		);

		$sqlcount=sprintf("SELECT COUNT(cat_id) FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='%s' AND cat_sub='%s'",
		$this->cms->tbname['papoo_linkliste_cat'],
		$this->cms->tbname['papoo_linkliste_lang_cat'],
		$langid,
		$catid
		);
		$resultcount=$this->db->get_var($sqlcount);

		$result2=$this->db->get_results($sql, ARRAY_A);
		#print_r($result2);
		$i=1;
		if (!empty($result2)){
			foreach ($result2 as $sub){
				#echo $resultcount."-".$i;
				$letzter="";

				if ($resultcount==$i){
					$letzter="letzter";
				}
				$i++;
				#echo $this->level."-".$sub['cat_level'];

				//Alter gr��er, dann runter
				if ($this->level>$sub['cat_level']){
					$shift_anfang="</ul><li>";
					$shift_ende="</li>";
					#echo "gr��er";
				}
				//Alter kleiner, dann rauf
				if ($this->level<$sub['cat_level']){
					$shift_anfang="<ul><li>";
					$shift_ende="</li>";
					#echo "kleiner";
				}
				//gleich dann nix
				if ($this->level==$sub['cat_level']){
					$shift_anfang="<li>";
					$shift_ende="</li>";
					#echo "gleich";

				}
				array_push($this->cat_result,array(
				'letzter'=>$letzter,
				'shift_anfang'=>$shift_anfang,
				'shift_ende'=>$shift_ende,
				'cat_order_id'=>$sub['cat_order_id'],
				'cat_sub'=> $sub['cat_sub'],
				'cat_level'=> $sub['cat_level'],
				'cat_id'=> $sub['cat_id'],
				'cat_name'=> $sub['cat_name'])
				);
				$this->level=$sub['cat_level'];
				$this->get_cat_list_sort($sub['cat_id'],$sub['cat_level']);
			}
		}
		#return $result2;

		#$this->content->template['result_cat']=$result;
	}


	/**
	* @return void
	* @desc Hiermit kann die Reihenfolge der Men�punkte ver�ndert werden.
	*/
	function order_change($artikel_id = 0, $menuid = 0)
	{
		// Ordnung ermitteln
		if ($this->checked->artikelorder_action_plus) $ordnung = "hoch";
		else $ordnung = "runter";

		//Test ob Artikel der Start-Seite ($menuid = 1) getauscht werden
		if ($menuid == 1 || $menuid == 0) $startseite = 1;
		else $startseite = 0;

		// Wechsel-Partner emitteln
		$artikel_liste = $this->get_cat_list();
		#print_r($artikel_liste);
		$vorgaenger = array();
		$nachfolger = array();

		if (!empty($artikel_liste))
		{
			$artikel_vorgaenger = NULL;

			foreach ($artikel_liste as $artikel)
			{
				#echo $ordnung."-".$artikel_id."-".$artikel->linkliste_id;
				// Mit dem Vorg�nger tauschen
				if ($ordnung == "hoch" && $artikel_id == $artikel->linkliste_id)
				{
					#echo $artikel_vorgaenger->linkliste_order_id."-".$artikel->linkliste_order_id;
					//Fall beide sind gleich
					if ($artikel_vorgaenger->linkliste_order_id==$artikel->linkliste_order_id){
						$artikel_vorgaenger->linkliste_order_id=$artikel_vorgaenger->linkliste_order_id+1;
					}
					$vorgaenger['linkliste_id'] = $artikel_vorgaenger->linkliste_id;
					$nachfolger['linkliste_id'] = $artikel->linkliste_id;

					if ($startseite)
					{
						$vorgaenger['orderid'] = $artikel_vorgaenger->order_id_start;
						$nachfolger['orderid'] = $artikel->order_id_start;
					}
					else
					{
						$vorgaenger['orderid'] = $artikel_vorgaenger->linkliste_order_id;
						$nachfolger['orderid'] = $artikel->linkliste_order_id;
					}
				}
				// Mit dem Nachfolger tauschen
				if ($ordnung == "runter" && $artikel_id == $artikel_vorgaenger->linkliste_id)
				{
					//Fall beide sind gleich

					if ($artikel_vorgaenger->linkliste_order_id==$artikel->linkliste_order_id){
						if ($artikel->linkliste_order_id>=1){
							$artikel->linkliste_order_id=$artikel->linkliste_order_id-1;
						}
						else {
							$artikel_vorgaenger->linkliste_order_id=$artikel_vorgaenger->linkliste_order_id+1;
						}
					}

					$vorgaenger['linkliste_id'] = $artikel_vorgaenger->linkliste_id;
					$nachfolger['linkliste_id'] = $artikel->linkliste_id;

					if ($startseite)
					{
						$vorgaenger['orderid'] = $artikel_vorgaenger->linkliste_order_id;
						$nachfolger['orderid'] = $artikel->linkliste_order_id;
					}
					else
					{
						$vorgaenger['orderid'] = $artikel_vorgaenger->linkliste_order_id;
						$nachfolger['orderid'] = $artikel->linkliste_order_id;
					}
				}
				$artikel_vorgaenger = $artikel;
			}
		}
		//print_r($vorgaenger);
		//print_r($nachfolger);

		$this->order_partner_wechsel($vorgaenger, $nachfolger, $startseite);

		// Seite neu laden (so kann erneutes Verschieben durch Browser-Reload verhindert werden)
		//$location_url = "./artikel.php?menuid=45&formmenuid=".$menuid;
		//if ($_SESSION['debug_stopallredirect']) echo '<a href="'.$location_url.'">Weiter</a>';
		//else header("Location: $location_url");
		//exit ();
	}

	/**
	* @return void
	* @desc Hiermit kann die Reihenfolge der Men�punkte ver�ndert werden.
	*/
	function order_change_cat($artikel_id = 0, $menuid = 0)
	{

		// Ordnung ermitteln
		if ($this->checked->menuorder_action_plus) $ordnung = "hoch";
		else $ordnung = "runter";

		// Wechsel-Partner emitteln
		$menu = $this->get_cat_list();

		#print_r($menu);

		$vorgaenger = array();
		$nachfolger = array();
		$menupunkt_vorgaenger_cat_id=1;

		if (!empty($menu))
		{
			foreach ($menu as $menupunkt)
			{

				// Mit dem Vorg�nger tauschen
				if ($ordnung == "hoch" && $this->checked->menuorder_menuid == $menupunkt['cat_id'])
				{
					#echo "#";echo $menupunkt_vorgaenger_cat_id;
					#echo $ordnung."-".$this->checked->menuorder_menuid."-".$menupunkt['cat_id']."xx";
					//Fall beide sind gleich
					if ( $menupunkt['vorgaenger_cat_order_id']==$menupunkt['cat_order_id']){
						$menupunkt['vorgaenger_cat_order_id']=$menupunkt['vorgaenger_cat_order_id']+1;
					}
					$vorgaenger['cat_id'] = $menupunkt_vorgaenger_cat_id;
					$vorgaenger['cat_order_id'] = $menupunkt_vorgaenger_cat_order_id;
					$nachfolger['cat_id'] = $menupunkt['cat_id'];
					$nachfolger['cat_order_id'] = $menupunkt['cat_order_id'];
				}
				// Mit dem Nachfolger tauschen
				if ($ordnung == "runter" && $this->checked->menuorder_menuid == $menupunkt_vorgaenger_cat_id)
				{
					#echo "#";echo $menupunkt_vorgaenger_cat_id."**";
					#echo $ordnung."-".$this->checked->menuorder_menuid."-".$menupunkt['cat_id']."xx".$menupunkt['cat_order_id']."++";
					//Fall beide sind gleich
					if ($menupunkt['vorgaenger_cat_order_id']==$menupunkt['cat_order_id']){
						if ($menupunkt['cat_order_id']>=1){
							$menupunkt['cat_order_id']=$menupunkt['cat_order_id']-1;
						}
						else {
							$menupunkt['vorgaenger_cat_order_id']=$menupunkt['vorgaenger_cat_order_id']+1;
						}

					}
					$vorgaenger['cat_id'] = $menupunkt_vorgaenger_cat_id;
					$vorgaenger['cat_order_id'] = $menupunkt_vorgaenger_cat_order_id;
					$nachfolger['cat_id'] = $menupunkt['cat_id'];
					$nachfolger['cat_order_id'] = $menupunkt['cat_order_id'];
				}
				$menupunkt_vorgaenger_cat_id=$menupunkt['cat_id'];
				$menupunkt_vorgaenger_cat_order_id=$menupunkt['cat_order_id'];
			}
		}
		#print_r($vorgaenger);
		#print_r($nachfolger);

		$this->order_partner_wechsel_cat($vorgaenger, $nachfolger);
	}


	// Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	function order_partner_wechsel($vorgaenger, $nachfolger, $startseite = 0)
	{
		if (!empty($vorgaenger) && !empty($nachfolger))
		{
			// Test ob Artikel auf der Startseite ist.
			if ($startseite) $order_feld = "linkliste_order_id";
			else $order_feld = "linkliste_order_id";

			$sql = sprintf("UPDATE %s SET %s='%d' WHERE linkliste_id='%d'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$order_feld,
			$vorgaenger['orderid'],
			$nachfolger['linkliste_id']
			);
			$this->db->query($sql);

			$sql = sprintf("UPDATE %s SET %s='%d' WHERE linkliste_id='%d'",
			$this->cms->tbname['papoo_linkliste_daten'],
			$order_feld,
			$nachfolger['orderid'],
			$vorgaenger['linkliste_id']
			);
			$this->db->query($sql);
		}
	}
	// Wechselt die Ordnungs-ID $order_id der beiden Wechsel-Partner $vorgaenger und $nachfolger
	function order_partner_wechsel_cat($vorgaenger, $nachfolger, $startseite = 0)
	{
		if (!empty($vorgaenger) && !empty($nachfolger))
		{
			// Test ob Artikel auf der Startseite ist.
			if ($startseite) $order_feld = "cat_order_id";
			else $order_feld = "cat_order_id";

			$sql = sprintf("UPDATE %s SET %s='%d' WHERE cat_id='%d'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$order_feld,
			$vorgaenger['cat_order_id'],
			$nachfolger['cat_id']
			);
			$this->db->query($sql);

			$sql = sprintf("UPDATE %s SET %s='%d' WHERE cat_id='%d'",
			$this->cms->tbname['papoo_linkliste_cat'],
			$order_feld,
			$nachfolger['cat_order_id'],
			$vorgaenger['cat_id']
			);
			$this->db->query($sql);
		}
	}

}
// Hiermit wird die Klasse initialisiert und kann damit sofort �berall benutzt werden.

$linklisteplugin = new linklisteplugin();
?>