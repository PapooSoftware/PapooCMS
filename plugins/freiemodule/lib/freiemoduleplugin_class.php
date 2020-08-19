<?php

/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2010       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >5.0                  #
 * #####################################
 */

/**
 * Class freiemodule
 */
class freiemodule {

	/** @var string  */
	var $news = "";

	/**
	 * freiemodule constructor.
	 */
	function __construct()
	{
		// Hier die Klassen als Referenzen
		global $cms, $db, $db_praefix, $content, $message, $user, $menu, $checked, $diverse, $intern_artikel;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->message = & $message;
		$this->user = & $user;
		$this->menu = & $menu;
		$this->checked = & $checked;
		$this->diverse = & $diverse;
		$this->intern_artikel = & $intern_artikel;

		IfNotSetNull($this->content->template['freiemodule_name']);
		IfNotSetNull($this->content->template['freiemodule_code']);
		IfNotSetNull($this->content->template['eintragmakeartikelfertig']);
		IfNotSetNull($this->content->template['exist']);
		IfNotSetNull($this->content->template['altereintrag']);
		IfNotSetNull($this->content->template['banner_ar']);
		IfNotSetNull($this->content->template['freiemodule_start']);
		IfNotSetNull($this->content->template['freiemodule_stop']);
		IfNotSetNull($this->content->template['nod_link']);
		IfNotSetNull($this->content->template['plugin_metaebene']);
		IfNotSetNull($this->content->template['edit']);
		IfNotSetNull($this->content->template['fragedel']);
		IfNotSetNull($this->content->template['deleted']);

		$this->make_freiemodule();
		$this->content->template['plugin_message'] = "";
	}

	/**
	* Interne Verwaltung
	*/
	function make_freiemodule()
	{
		if (defined("admin")) {
			global $template;
			global $intern_artikel;

			$intern_artikel->get_css_klassen();

			$template2 =str_ireplace(PAPOO_ABS_PFAD."/plugins/","",$template);
			// �berpr�fen ob Zugriff auf die Inhalte besteht
			$this->user->check_access();
			if ( $template != "login.utf8.html" ) {
				switch ($template2) {

					//Die Standardeinstellungen werden bearbeitet
				case "freiemodule/templates/freiemodule.html" :
					$this->check_pref();
					break;

					//Ein Banner wird eingebunden
				case "freiemodule/templates/freiemodule_create.html" :
					$this->new_banner();
					break;

					//Ein Banner wird bearbeitet
				case "freiemodule/templates/freiemodule_edit.html" :
					$this->edit_banner();
					break;

					//Einen Dump erstellen oder einspielen
				case "freiemodule/templates/freiemodule_dump.html" :
					$this->freiemodule_dump();
					break;

				default:
					break;
				}
			}
		}
		else {
			$this->freiemodule_read();
		}
	}

	/**
	 * Was im Frontend passiert
	 */
	function freiemodule_read()
	{
		if (defined("admin") == false) {
			$result_artikel= array();
			$date = date("Y-m-d", time());
			$menuId = (int)$this->checked->menuid;

			$sql =
				"SELECT * ".
				"FROM {$this->cms->tbname["papoo_freiemodule_daten"]} module ".
				"JOIN {$this->cms->tbname["papoo_freiemodule_menu_lookup"]} whitelist ON whitelist.freiemodule_mid = module.freiemodule_id ".
				"LEFT JOIN {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} blacklist ON blacklist.blacklist_module_id = module.freiemodule_id AND blacklist.blacklist_menu_id = $menuId ".
				"WHERE ".
				"whitelist.freiemodule_menu_id IN (0, $menuId) AND blacklist.blacklist_menu_id IS NULL AND module.freiemodule_lang = '{$this->db->escape($this->cms->lang_short)}' AND ( ".
				"	module.freiemodule_start <= '$date' AND module.freiemodule_stop >= '$date' OR ".
				"	module.freiemodule_start <= '0000-00-00' AND module.freiemodule_stop >= '0000-00-00' ".
				") ".
				"GROUP BY module.freiemodule_id";
			$result_menu = $this->db->get_results($sql, ARRAY_A);
			//Alle Eintr�ge zum Artikel
			if (!empty($this->checked->reporeid)) {
				$sql = sprintf("SELECT * FROM %s
								WHERE freiemodule_artikelid='%s' AND freiemodule_start<='%s' AND freiemodule_stop>='%s' AND freiemodule_lang='%s'
								OR freiemodule_artikelid='%s' AND freiemodule_start<='0000-00-00' AND freiemodule_stop>='0000-00-00'  AND freiemodule_lang='%s'  ",

					$this->cms->tbname['papoo_freiemodule_daten'],

					$this->db->escape($this->checked->reporeid),
					$date,
					$date,
					$this->db->escape($this->cms->lang_short),

					$this->db->escape($this->checked->reporeid),
					$this->db->escape($this->cms->lang_short)
				);
				$result_artikel=$this->db->get_results($sql,ARRAY_A);
			}
			// Module zusammenfuegen und Duplikate entfernen
			$result_merge = array_reduce(array_merge($result_artikel, $result_menu), function ($items, $item) {
				$item["freiemodule_code"] = $this->diverse->do_pfadeanpassen($item['freiemodule_code']);
				$items[$item["freiemodule_id"]] = $item;
				return $items;
			}, []);
			$this->content->template['freiemodule_result'] = $result_merge;
		}
	}

	/**
	 * Daten einstellen
	 * Funktion gibt nichts zur�ck, sondern �bergibt nur Daten an das Template
	 * Ge�nderte Daten werden eingetragen
	 */
	function check_pref()
	{

	}

	/**
	 * Liste der Artikel besorgen
	 */
	function get_liste_artikel()
	{
		$lim=$this->cms->sqllimit="";
		$this->intern_artikel->replangid=$this->cms->lang_id;
		$this->intern_artikel->make_artikel_list("all");

		$this->content->template['artikel_liste'] =$this->intern_artikel->result_liste ;
		$this->cms->sqllimit=$lim;
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
	 * Neuen Banner erstellen
	 */
	function new_banner()
	{

		IfNotSetNull($this->checked->fertig);

		//eintragmakeartikelfertig
		if ($this->checked->fertig=="drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}

		$this->content->template['neuereintrag'] = "ok";
		$this->get_liste_artikel();
		$this->get_liste_menu();
		if (!empty ($this->checked->submitentry)) {
			if (strlen($this->checked->banner_lang)<2) {
				$this->checked->banner_lang="de";
			}

			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->freiemodule_start,$res);
			$this->checked->freiemodule_start=$res[3]."-".$res[2]."-".$res[1];
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->freiemodule_stop,$res);

			$this->checked->freiemodule_stop=$res[3]."-".$res[2]."-".$res[1];

			//Datumswert prüfen
			if(!strtotime($this->checked->freiemodule_start)) {
				$this->checked->freiemodule_start = "0000-00-00";
			}

			if(!strtotime($this->checked->freiemodule_stop)) {
				$this->checked->freiemodule_start = "0000-00-00";
			}

			$rawOutput = (bool)($this->checked->freiemodule_raw_output ?? false);

			$sql = sprintf("INSERT INTO %s SET freiemodule_name='%s', freiemodule_code='%s', freiemodule_menuid='%s', freiemodule_artikelid='%s',
							freiemodule_raw_output = %d,
							freiemodule_modulid='%s', freiemodule_start='%s', freiemodule_stop='%s',freiemodule_lang='%s' ",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->db->escape($this->checked->freiemodule_name),
				$this->db->escape($this->checked->freiemodule_code),
				$this->db->escape($this->checked->freiemodule_menuid),
				$this->db->escape($this->checked->freiemodule_artikelid),

				$this->db->escape($rawOutput ? 1 : 0),

				$this->db->escape($this->checked->freiemodule_modulid),
				$this->db->escape($this->checked->freiemodule_start),
				$this->db->escape($this->checked->freiemodule_stop),
				$this->db->escape($this->checked->banner_lang)
			);

			$this->db->query($sql);
			$insertid2=$this->db->insert_id;

			//Die Lookups f�r Men�punkte eintragen
			if (is_array($this->checked->inhalt_ar['cattext_ar'])) {
				foreach ($this->checked->inhalt_ar['cattext_ar'] as $key=>$value) {
					$sql=sprintf("INSERT INTO %s SET
												freiemodule_mid='%d',
												freiemodule_menu_id='%d'
												",
						$this->cms->tbname['papoo_freiemodule_menu_lookup'],
						$this->db->escape($insertid2),
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}

			// Menue-Blacklist eintragen
			$blacklist = &$this->checked->freiemodule_menu_blacklist;
			if (is_array($blacklist) && count($blacklist) > 0) {
				$this->db->query("INSERT IGNORE INTO {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} ".
					"(blacklist_menu_id, blacklist_module_id) VALUES (".
					implode("), (", array_map(function ($item) use ($insertid2) {
						return (int)$item.", ".(int)$insertid2;
					}, $blacklist)).
					")"
				);
			}

			//Modul eintragen
			$mod_datei="plugin:freiemodule/templates/freiemodule_front.html";
			$sql = sprintf("INSERT INTO %s SET mod_datei='%s' ",
				$this->cms->tbname['papoo_module'],
				$this->db->escape($mod_datei)
			);
			$this->db->query($sql);
			$insertid = $this->db->insert_id;

			//Sprachdaten rausholen
			$sql=sprintf("SELECT lang_id FROM %s",
				$this->cms->tbname['papoo_name_language']
			);
			$result_langdat=$this->db->get_results($sql,ARRAY_A);
			//Sprachdaten
			if (is_array($result_langdat)) {
				foreach ($result_langdat as $key=>$value) {
					// 2. in die Tabelle eintragen
					$sql = sprintf("INSERT INTO %s
									SET modlang_mod_id='%d',
									modlang_lang_id='%d',
									modlang_name='%s',
									modlang_beschreibung='Freies Modul'
									",
						$this->cms->tbname['papoo_module_language'],
						$insertid,
						$value['lang_id'],
						$this->db->escape($this->checked->freiemodule_name)
					);
					$this->db->query($sql);
				}
			}

			$sql = sprintf("UPDATE %s SET freiemodule_modulid='%s' WHERE freiemodule_id='%s'",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$insertid,
				$insertid2
			);
			$this->db->query($sql);

			// 3. Modul-ID in Tabelle papoo_plugins eintragen (wird zum L�schen ben�tigt)
			$plugin_id = 0;
			$the_modulids = "";
			// .. Ermitteln der Plugin-ID des Plugins "Freie Module"
			$sql = sprintf("SELECT plugin_id FROM %s WHERE plugin_menuids LIKE '%% %d %%'",
				$this->cms->papoo_plugins,
				$this->checked->menuid
			);
			$plugin_id = $this->db->get_var($sql);

			// .. lesen der bisherigen modulids
			$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				$plugin_id
			);
			$result = $this->db->get_var($sql);
			$the_modulids = $result." ".$insertid." ";

			// .. schreiben der neuen modulids
			$sql = sprintf("UPDATE %s SET plugin_modulids='%s' WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				$this->db->escape($the_modulids),
				$plugin_id
			);
			$this->db->get_results($sql);
			$mid=$this->checked->menuid+1;
			$location_url = $_SERVER['PHP_SELF']."?menuid=".$mid."&template=freiemodule/templates/freiemodule_edit.html&bannerid=".$insertid2."&fertig=drin";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.".. weiter".'</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * Banner bearbeiten
	 */
	function edit_banner()
	{
		IfNotSetNull($this->checked->fertig);
		IfNotSetNull($this->checked->submitentry);

		//Anzeigen das Eintrag gel�scht wurde
		if ($this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}

		//Es soll eingetragen werden
		if ($this->checked->submitentry) {
			if (strlen($this->checked->banner_lang)<2) {
				$this->checked->banner_lang="de";
			}

			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->freiemodule_start,$res);
			$this->checked->freiemodule_start=$res[3]."-".$res[2]."-".$res[1];
			preg_match("/(\d+).(\d+).(\d+)/",$this->checked->freiemodule_stop,$res);
			$this->checked->freiemodule_stop=$res[3]."-".$res[2]."-".$res[1];
			$this->checked->freiemodule_code=str_replace("\n","",$this->checked->freiemodule_code);

			$rawOutput = (bool)($this->checked->freiemodule_raw_output ?? false);

			$sql = sprintf("UPDATE %s SET
					freiemodule_name='%s',
					freiemodule_code='%s',
					freiemodule_menuid='%s',
					freiemodule_artikelid='%s',
					freiemodule_raw_output = %d,
					freiemodule_start='%s',
					freiemodule_stop='%s',
					freiemodule_lang='%s'
					WHERE freiemodule_id='%s' ",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->db->escape($this->checked->freiemodule_name),
				$this->db->escape($this->checked->freiemodule_code),
				$this->db->escape($this->checked->freiemodule_menuid),
				$this->db->escape($this->checked->freiemodule_artikelid),
				$this->db->escape($rawOutput ? 1 : 0),
				$this->db->escape($this->checked->freiemodule_start),
				$this->db->escape($this->checked->freiemodule_stop),
				$this->db->escape($this->checked->banner_lang),
				$this->db->escape($this->checked->freiemodule_id)
			);
			$this->db->query($sql);
			$insertid=$this->db->escape($this->checked->cat_id);

			// Alte Menüpunktezuweisungen entfernen und neue eintragen
			$sql=sprintf("DELETE FROM %s WHERE freiemodule_mid='%d'",
				$this->cms->tbname['papoo_freiemodule_menu_lookup'],
				$this->db->escape($this->checked->freiemodule_id)
			);
			$this->db->query($sql);

			if (is_array($this->checked->inhalt_ar['cattext_ar'])) {
				foreach ($this->checked->inhalt_ar['cattext_ar'] as $key=>$value) {
					$sql=sprintf("INSERT INTO %s SET
												freiemodule_mid='%d',
												freiemodule_menu_id='%d'
												",
						$this->cms->tbname['papoo_freiemodule_menu_lookup'],
						$this->db->escape($this->checked->freiemodule_id),
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}

			// Menue-Blacklist aktualisieren
			$moduleId = (int)$this->checked->bannerid;
			$this->db->query(
				"DELETE FROM {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} ".
				"WHERE blacklist_module_id = $moduleId"
			);

			$blacklist = &$this->checked->freiemodule_menu_blacklist;
			if (is_array($blacklist) && count($blacklist) > 0) {
				$this->db->query(
					"INSERT IGNORE INTO {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} ".
					"(blacklist_menu_id, blacklist_module_id) VALUES (".
					implode("), (", array_map(function ($item) use ($moduleId) {
						return (int)$item.", ".$moduleId;
					}, $blacklist)).
					")"
				);
			}

			// Namen der freien Module in der Papoo-Module-Tabelle aktualisieren
			$modul_id = (int)$this->db->get_var("SELECT freiemodule_modulid FROM `{$this->cms->tbname["papoo_freiemodule_daten"]}` WHERE freiemodule_id = ".(int)$this->checked->freiemodule_id);
			$this->db->query("UPDATE `{$this->cms->tbname["papoo_module_language"]}` SET modlang_name = '{$this->db->escape($this->checked->freiemodule_name)}' WHERE modlang_mod_id = {$modul_id}");

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&bannerid=".$this->checked->bannerid."&fertig=drin";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.".. weiter".'</a>';
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
			$sql = sprintf("SELECT * FROM %s WHERE freiemodule_id='%s'",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->db->escape($this->checked->bannerid)
			);

			$result = $this->db->get_results($sql);
			if (!empty ($result)) {
				foreach ($result as $glos) {
					$this->content->template['freiemodule_id'] = $glos->freiemodule_id;
					$this->content->template['freiemodule_name'] = $glos->freiemodule_name;
					$this->content->template['freiemodule_lang'] = $glos->freiemodule_lang;
					$this->content->template['freiemodule_code'] = "nodecode:".$glos->freiemodule_code;
					$this->content->template['freiemodule_menuid'] = $glos->freiemodule_menuid;
					$this->content->template['freiemodule_artikelid'] = $glos->freiemodule_artikelid;
					$this->content->template['freiemodule_raw_output'] = (bool)$glos->freiemodule_raw_output;
					$this->content->template['freiemodule_modulid'] = $glos->freiemodule_modulid;
					$this->content->template['freiemodule_start'] = $glos->freiemodule_start;
					$this->content->template['freiemodule_stop'] = $glos->freiemodule_stop;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
				}
			}
			preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['freiemodule_start'],$res);
			$this->content->template['freiemodule_start']=$res[3].".".$res[2].".".$res[1];
			preg_match("/(\d+)-(\d+)-(\d+)/",$this->content->template['freiemodule_stop'],$res);
			$this->content->template['freiemodule_stop']=$res[3].".".$res[2].".".$res[1];

			//Lookups
			$sql=sprintf("SELECT * FROM %s WHERE freiemodule_mid='%d'",
				$this->cms->tbname['papoo_freiemodule_menu_lookup'],
				$this->db->escape($this->checked->bannerid)
			);
			$this->content->template['banner_ar'] = $this->db->get_results($sql,ARRAY_A);

			$moduleId = (int)$this->checked->bannerid;
			$this->content->template["module_menu_blacklist"] = $this->db->get_results(
				"SELECT * ".
				"FROM {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} ".
				"WHERE blacklist_module_id = $moduleId", ARRAY_A);
		}
		//alle anzeigen
		else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			$sql = sprintf("SELECT * FROM %s ORDER BY freiemodule_name ASC",
				$this->cms->tbname['papoo_freiemodule_daten']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;

			if (isset($this->content->template['list_dat']['freiemodule_start'])) {
				preg_match("/(\\d+)-(\\d+)-(\\d+)/",$this->content->template['list_dat']['freiemodule_start'],$res);
			}

			//Was ist das???
			#$this->content->template['list_dat']['freiemodule_start']=$res[3].".".$res[2].".".$res[1];
			preg_match("/(\\d+)-(\\d+)-(\\d+)/",$this->content->template['freiemodule_stop'],$res);

			IfNotSetNull($res[1]);
			IfNotSetNull($res[2]);
			IfNotSetNull($res[3]);

			$this->content->template['freiemodule_stop']=$res[3].".".$res[2].".".$res[1];
			$this->content->template['link_frei'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&bannerid=";
		}

		//Anzeigen das Eintrag gel�scht wurde
		if ($this->checked->fertig == "del") {
			$this->content->template['deleted'] = "ok";
		}
		//Soll  gel�scht werden
		if (!empty($this->checked->submitdelecht)) {
			// Blacklist saeubern
			$moduleId = (int)$this->checked->freiemodule_id;
			$this->db->query(
				"DELETE FROM {$this->cms->tbname["papoo_freiemodule_menu_blacklist"]} ".
				"WHERE blacklist_module_id = $moduleId"
			);

			// Modul aus der lookup-Tabelle löschen
			$sql = sprintf("DELETE FROM %s WHERE freiemodule_mid='%d'",
				$this->cms->tbname['papoo_freiemodule_menu_lookup'],
				$this->checked->freiemodule_id
			);
			$this->db->query($sql);

			// Modul-ID rauslesen und aus Tabelle der freien Module l�schen
			$sql = sprintf("SELECT freiemodule_modulid FROM %s WHERE freiemodule_id='%d'",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->checked->freiemodule_id
			);
			$modid=$this->db->get_var($sql);
			$sql = sprintf("DELETE FROM %s WHERE freiemodule_id='%d'",
				$this->cms->tbname['papoo_freiemodule_daten'],
				$this->checked->freiemodule_id
			);
			$this->db->query($sql);

			// Modul in Modul-Tabellen l�schen
			$sql = sprintf("DELETE FROM %s WHERE mod_id='%d'",
				$this->cms->tbname['papoo_module'],
				$modid
			);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s WHERE modlang_mod_id='%d'",
				$this->cms->tbname['papoo_module_language'],
				$modid
			);
			$this->db->query($sql);
			$sql = sprintf("DELETE FROM %s WHERE stylemod_mod_id='%d'",
				$this->cms->tbname['papoo_styles_module'],
				$modid
			);
			$this->db->query($sql);

			// Modul-ID in Tabelle papoo_plugins entfernen
			$plugin_id = 0;
			$the_modulids = "";
			// .. Ermitteln der Plugin-ID des Plugins "Freie Module"
			$sql = sprintf("SELECT plugin_id FROM %s WHERE plugin_menuids LIKE '%% %d %%'",
				$this->cms->papoo_plugins,
				$this->checked->menuid
			);
			$plugin_id = $this->db->get_var($sql);

			// .. lesen der bisherigen modulids
			$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				$plugin_id
			);
			$result = $this->db->get_var($sql);
			$the_modulids = str_replace(" ".$modid." ", " ", $result);

			// .. schreiben der neuen modulids
			$sql = sprintf("UPDATE %s SET plugin_modulids='%s' WHERE plugin_id='%d'",
				$this->cms->papoo_plugins,
				$this->db->escape($the_modulids),
				$plugin_id
			);
			$this->db->get_results($sql);

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=del";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">'.".. weiter".'</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;

		}
		//Soll wirklich gel�scht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['freiemodule_name'] = $this->checked->freiemodule_name;
			$this->content->template['freiemodule_id'] = $this->checked->freiemodule_id;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * Die Bannerdaten dumpen und wieder zur�ckspielen
	 */
	function freiemodule_dump() {
		$this->diverse->extern_dump("freiemodule");
	}
}

$freiemodule = new freiemodule();
