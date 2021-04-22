<?php
/**
#####################################
# Papoo CMS                         #
# (c) Carsten Euwens 2007           #
# Authors: Dr. Carsten Euwens       #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.3                  #
#####################################
 */

/**
 * Class imex_class
 */
class imex_class
{
	/**
	 * imex_class constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen User-Objekts zur �berpr�fung des Logins
		global $user;
		$this->user = & $user;

		//Administration durchf�hren
		$this->do_admin();
	}

	/**
	 * Admin Weiche
	 */
	function do_admin()
	{
		global $template;
		//WEnn Admin
		if (defined("admin")) {
			//Besteht Zugriff?
			$this->user->check_intern();

			//Klassen einbinden
			global $content, $cms, $db, $checked, $diverse;
			$this->content = & $content;
			$this->cms = & $cms;
			$this->db = & $db;
			$this->checked = & $checked;
			$this->diverse = & $diverse;

			//Daten sollen exportiert werden
			if (strpos("XXX".$template, "imex/templates/imex_exportit.html")) {
				$this->do_export();
			}

			//Daten sollen importiert werden
			if (strpos("XXX".$template, "imex/templates/imex_importit.html")) {
				$this->do_import();
			}
		}
	}

	/**
	 * Daten exportieren
	 */
	function do_export()
	{
		//Auswahlliste der Tabellen ans Template �bergeben
		$this->content->template['tabtar'] = $this->cms->tbname;

		//Vorhandene Felder zur�ckgeben
		$this->content->template['tabelle'] = isset($this->checked->tabelle) ? $this->checked->tabelle : NULL;
		$this->content->template['format'] = isset($this->checked->format) ? $this->checked->format : NULL;
		$this->content->template['feld'] = isset($this->checked->feld) ? $this->checked->feld : NULL;

		if (!empty($this->checked->delete)) {
			//GEl�scht ausgeben
			$this->content->template['delete']="ok";
		}
		//Datei l�schen
		if (!empty($this->checked->file)) {
			//Filtern
			$file=basename($this->checked->file);
			$file=str_replace("/","",$file);
			$file=str_replace("\\","",$file);
			//Pfad erzeugen
			$file="/interna/templactes_c/".$file;
			//Datei l�schen
			$this->diverse->delete_file($file);
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&delete=ok";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}

		//WEnn ausgeben gew�hlt durchlaufen
		if (!empty($this->checked->startexport)) {
			if (!empty($this->checked->tabelle)) {
				//Daten holen
				$daten=$this->get_data($this->checked->tabelle,$this->checked->format);
				//Daten erzeugen
				IfNotSetNull($this->checked->feld);
				$datenfertig=$this->export_data($daten,$this->checked->tabelle,$this->checked->format,$this->checked->feld);
				//Ausgeben als Link
				if (!empty($datenfertig) and ($this->checked->format=="csv" or $this->checked->format =="xml")) {
					//Zeitstempel
					$time=time();
					//Dateinamen
					$file="/interna/templates_c/export_".basename($this->checked->tabelle).$time.".".$this->checked->format;
					//Datei erzeugen
					$this->diverse->write_to_file($file,$datenfertig);
					//Pfad ans TEmplate
					$this->content->template['file']=$file;
					//Link
					$this->content->template['self'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "";
					//Pfad zur Datei
					$this->content->template['pfad'] = PAPOO_WEB_PFAD;
				}
			}
			else {
				// Meldung zeigen..
				$this->content->template['no_table_selected'] = 1;
			}
		}
	}

	/**
	 * Daten aus einger gew�hlten Tabelle rausholen
	 *
	 * @param $tab
	 * @param string $format
	 * @return array|null
	 */
	function get_data($tab,$format="")
	{
		//Pr�fix holen
		global $db_praefix;
		//echten Tabellen Namen erstellen
		$tab=$db_praefix."".$tab;
		//Abfrage - alles rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->db->escape($tab)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//Zeilenumbr�che etc. entfernen wenn CSV
		if ($format!="xml") {
			if (!empty($result)) {
				$i=0;
				//Allte Eintr�ge durchgehen
				foreach ($result as $dat) {
					foreach ($dat as $key=>$value) {
						//Entfernen
						$value  = str_replace("\n", '', $value  );
						$value  = str_replace("\r", '', $value  );
						$result[$i][$key]=$value;
					}
					$i++;
				}
			}
		}
		//R�ckgabe
		return $result;
	}

	/**
	 * Daten exportieren
	 *
	 * @param array $results
	 * @param string $tabname
	 * @param string $mode
	 * @param string $feld
	 * @return string
	 */
	function export_data($results=array(), $tabname="", $mode="csv", $feld="ohne")
	{
		// daten in eine Variable laden
		$cvs = "";
		if ( !empty ( $results ) ) {
			//Modus CSV
			if ( $mode == "csv" ) {
				foreach ( $results as $erg ) {
					if ($feld=="mit") {
						if (isset($iskey) && $iskey!=1) {
							//Alle Feldnamen rausholen
							$keys=array_keys($erg);
							//Feldnamen zuweisen
							foreach ($keys as $dk) {
								$cvs .= $dk . ";";
							}
							//Neue Zeile
							$cvs .= "\n";
							$iskey=1;
						}
					}
					foreach ( $erg as $key=>$item ) {
						//Semikolons auskommentieren
						$item=str_ireplace(";","\;",$item);
						//Eintrag erstellen
						$cvs .= $item . ";";
					}
					//Neue Zeile
					$cvs .= "\n";
				}
			}
			//Modus xml
			else {
				//xml Klasse einbinden
				include (PAPOO_ABS_PFAD."/lib/classes/class_informations.php");
				//Klasse initialisieren
				$xml = new informations2;
				//Start erzeugen
				$cvs='<?xml version="1.0" encoding="utf-8" ?>'."\n<data>";
				//Alle Eintr�ge durchgehen
				foreach ($results as $erg){
					//Daten aus Array in ein xml konvertieren
					$cvs.= $xml->array2xml->convert($erg);
				}
				//Schlu� erzeugen
				$cvs.="</data>";
			}
		}
		//Daten zur�ckgeben
		return $cvs;
	}

	/**
	 * Daten aus einer Datei einlesen wenn CVS
	 * Erste Zeile ausgeben als Array
	 *
	 */
	function lese_erste_zeile_csv()
	{
		//Datei einlesen
		$daten=$this->get_csv_content();
		//Erste Zeile auslesen
		$felder= trim($daten['0']);
		//Semikolons die kodiert sind umkodieren
		$felder=str_replace('\;',"###sem###",$felder);
		//Inhalte in ein array einlesen
		$feld_ar=explode(";",$felder);
		$i=1;
		//Array durchgehen und Kodierung wieder umschalten
		foreach ($feld_ar as $feld) {
			$feld=trim($feld);
			if (!empty($feld) or $feld=="0") {
				//Inhalt k�rzen
				//if (strlen($feld)>25) {
					#$feld=substr($feld,0,25)."...";
				//}
				//Kodierte Semikolons wieder einlesen
				$feld_ar2[$i]=str_ireplace("###sem###",";",$feld);
				$i++;
			}
		}
		IfNotSetNull($feld_ar2);
		return $feld_ar2;
	}

	/**
	 * Daten aus einer Datei einlesen wenn CVS
	 * Erste Zeile ausgeben als Array
	 *
	 * @return mixed
	 */
	function lese_erste_zeile_xml()
	{
		//Datei einlesen
		$daten=$this->get_xml_content();
		//Erste Zeile auslesen
		$i=1;
		if (is_array($daten['data']['0']['data']['0'])) {
			foreach ($daten['data']['0']['data']['0']as $key=>$value) {
				if ($key!="attribute" and $key!="cdata") {
					$feld_ar2[$i]=$key;
					$i++;
				}
			}
		}
		IfNotSetNull($feld_ar2);
		return $feld_ar2;
	}

	/**
	 * Die Felder einer ausgew�hlten Tabelle zur�ckgeben
	 *
	 * @param string $tabelle
	 * @return array
	 */
	function lese_felder_tabelle($tabelle="")
	{
		//excapen
		$tabelle=$this->db->escape($tabelle);
		//Ein ROw rausholen
		$row=$this->db->get_results("SHOW COLUMNS FROM  ".$this->cms->tbname[$tabelle],ARRAY_A);

		$output=array();
		$i=1;
		//Array durchgehen
		foreach ($row as $key=>$value) {
			$output[$i]=$value['Field'];
			$i++;
		}
		return $output;
	}

	/**
	 * Das Feld rausholen das entfernt werden soll
	 *
	 * @param string $modus
	 * @return bool|string
	 */
	function get_entfernfeld($modus="")
	{
		if (!empty($this->checked)) {
			//Alle checked Felder durchgehen
			foreach ($this->checked as $key=>$value) {
				//MOdus Update
				if ($modus=="update") {
					//Wenn es sich um einen Entfern Button handelt
					if (stristr($key,"refstartentfernen")) {
						//Nummer rausholen ist immer vorne ala 1_start...
						$datar['0']="entf";
					}
				}
				//Modus normal
				else {
					//Wenn es sich um einen Entfern Button handelt
					if (stristr($key,"startentfernen")) {
						//Nummer rausholen ist immer vorne ala 1_start...
						$datar=explode("_",$key);
					}
				}
			}
		}
		//Wenn nicht leer dann Inhalt zur�ckgeben
		if (!empty($datar['0'])) {
			return $datar['0'];
		}
		//Leer dann false
		else {
			return false;
		}
	}
	/**
	 * Komplette CSV Daten einlesen
	 */
	function get_csv_content()
	{
		$daten=file(PAPOO_ABS_PFAD."/dokumente/logs/".basename($_SESSION['uploaded_file']));
		return $daten;
	}
	/**
	 * Komplette XML Daten einlesen
	 */
	function get_xml_content()
	{
		//DAtei bestimmen
		$file=PAPOO_ABS_PFAD."/dokumente/logs/".basename($_SESSION['uploaded_file']);
		//XML PArser globalisieren
		global $xmlparser;
		//�bergeben
		$xml = $xmlparser;
		//Inhalt einlesen und in ein Array parsen
		$xml->parse($file);
		//Array �bergeben
		$xml_array = $xml->xml_data;
		if (is_array($xml_array)) {
			return $xml_array;
		}
		return false;
	}

	/**
	 * Eingelesene Daten konvertieren
	 *
	 * @param array $dat
	 * @param string $mit
	 * @return array
	 */
	function convert_to_array($dat=array(),$mit="")
	{
		$retdaten=array();
		$k=0;
		//Wenn Eintr�ge vorhanden
		if (!empty($dat)) {
			//Alle Eintr�ge durchgehen
			foreach ($dat as $key=>$value) {
				//Wenn erste Zeile Datennamen sind, diese �berspringen
				if ($mit=="1") {
					$k++;
					if ($k<2)continue;
				}
				//Semikolons die kodiert sind umkodieren
				$value=str_replace('\;',"###sem###",$value);
				//Inhalte in ein array einlesen
				$feld_ar=explode(";",$value);
				$i=1;
				$co=count($feld_ar);
				//Array durchgehen und Kodierung wieder umschalten
				foreach ($feld_ar as $feld) {
					//Zeilenumbr�che und sonstigen M�ll entfernen
					$feld=trim($feld);
					//Wenn sinnvoller Inhalt dann �bergeben
					if ($i<=$co) {
						//Inhalt k�rzen
						//if (strlen($feld)>25) {
							#$feld=substr($feld,0,25)."...";
						//}
						//Kodierte Semikolons wieder einlesen
						$feld_ar2[$i]=str_ireplace("###sem###",";",$feld);
						$i++;
					}
				}
				IfNotSetNull($feld_ar2);
				$retdaten[$key]=$feld_ar2;
			}
		}
		return $retdaten;
	}
	/**
	 * Eine Sicherung bei jedem Insert machen
	 *
	 */
	function make_sicherung()
	{
		//Daten holen
		$daten=$this->get_data($this->checked->tabelle,$this->checked->format);
		//Daten erzeugen
		IfNotSetNull($this->checked->feld);
		$datenfertig=$this->export_data($daten,$this->checked->tabelle,"xml",$this->checked->feld);
		//Ausgeben als Link
		if (!empty($datenfertig) ) {
			//Zeitstempel
			$time=time();
			//Dateinamen
			$file="/dokumente/logs/sicherung_vor_import_".basename($this->checked->tabelle).$time.".xml";
			//Datei erzeugen
			$this->diverse->write_to_file($file,$datenfertig);
		}
	}

	/**
	 * Daten in die Datenbank eintragen
	 *
	 * @param array $cvs_ar
	 * @param string $zuord
	 * @param string $anz
	 * @return string
	 */
	function insert_into_database($cvs_ar=array(),$zuord="",$anz="")
	{
		//DB_Pr�fix einbinden
		global $db_praefix;

		//Sicherheitshalber einen Dump erstellen... evtl....
		$this->make_sicherung();

		//WEnn Neue komplett alles ersetzen sollen
		if ($_SESSION['imex_update_insert']=="ins_del_neu") {
			//Alte Eintr�ge l�schen
			$sql=sprintf("DELETE FROM %s ",
				$this->db->escape($db_praefix.$this->checked->tabelle)
			);
			$this->db->query($sql);
		}

		//Zuordnungsfelder einlesen
		$zuord=$_SESSION['csv_ar'];
		//Anzahl der Zuordnungsfelder
		$anz=count($zuord);

		//Datenbankfelder einlesen
		$tabelle_felder=$this->lese_felder_tabelle($this->checked->tabelle);
		//Return initialisieren
		$datenfertig="";
		//WEnn Eintr�ge vorhanden
		if (!empty($cvs_ar)) {
			//Alle Eintr�ge durchgehen
			foreach ($cvs_ar as $key=>$value) {
				$insert="";
				$i=1;
				//Wenn eine Zuordnung vorhanden ist
				if (!empty($zuord)) {
					//Diese durchlaufen
					foreach ($zuord as $key2=>$value2) {
						//FIXME HIer mu� dann die Abfrage rein ob Lookup Tabelle f�r MV

						//FIXME REchte bei MV abfragen hier oder woanders???

						//Insert aus Feld und Inhalt zusammensetzen
						$insert.=$this->db->escape($tabelle_felder[$value2])."='".$this->db->escape($value[$key2])."'";
						if ($i<$anz) {
							$insert.=", ";
						}
						$i++;

					}
					//Nur Insert machen wenn auch neue eingetragen werden soll
					if ($_SESSION['imex_update_insert']=="ins_del_neu" || $_SESSION['imex_update_insert']=="ins_neu") {
						//Daten in die Datenabnk einlesen
						$sql=sprintf("INSERT INTO %s SET %s",
							$this->db->escape($db_praefix.$this->checked->tabelle),
							($insert)
						);
						$this->db->query($sql);
					}
					//Update machen mit Update Bezug
					if ($_SESSION['imex_update_insert']=="ins_upd") {
						//Update Aussage
						$update=$this->db->escape($tabelle_felder[$this->checked->ref_dat_tabelle])."='".$this->db->escape($value[$this->checked->ref_csv_tabelle])."'";
						//Daten in die Datenabnk einlesen
						$sql=sprintf("UPDATE %s SET %s WHERE %s LIMIT 1",
							$this->db->escape($db_praefix.$this->checked->tabelle),
							($insert),
							($update)
						);
						$this->db->query($sql);
					}
					IfNotSetNull($sql);
					$datenfertig.=$sql."; ##b_dump## \n";
				}
			}
		}
		//Daten zur�ckgeben um zu sichern
		return $datenfertig;
	}

	/**
	 * XML Daten datenbank verwertbar machen
	 *
	 * @param array $daten
	 * @return mixed
	 */
	function convert_to_array_xml($daten=array())
	{
		//1 weil null mist ist :-)
		$i=1;
		//Es ist ein Array
		if (is_array($daten['data']['0']['data'])) {
			//Alle Eintr�ge durchloopen die als Dateneintr�ge drin stehen
			foreach ($daten['data']['0']['data'] as $key=>$value) {
				$k=0;
				foreach ($value as $key2=>$value2) {
					//DIe ersten beiden Eintr�ge ignorieren, die sind xml speziell
					if ($key2!="attribute" and $key2!="cdata") {
						//Nur bei echten Eintr�gen z�hlen
						$k++;
						//Zuweisen Nummer und Inhalt
						$feld_ar2[$i][$k]=$value2['0']['cdata'];
					}
				}
				//Keynummer hochz�hlen
				$i++;
			}
		}
		IfNotSetNull($feld_ar2);
		return $feld_ar2;
	}
	/**
	 * Aus einer Vorlage eine Sessio machen
	 * Damit wird die Zuordnung vorbelegt
	 */
	function make_session_from_vorlage()
	{
		//Daten der ID auslesen
		$sql=sprintf("SELECT * FROM %s WHERE imex_id='%s'",
			$this->cms->tbname['papoo_imex_daten'],
			$this->db->escape($this->checked->myvorlage)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		//Liste der CSV Nummer auslesne
		$csv=explode(";",$result['0']['imex_csv']);
		//Liste der Feldnummern auslesen (name in der db ist komisch)
		$feld=explode(";",$result['0']['imex_felder']);
		$this->checked->ref_dat_tabelle=$result['0']['imex_felder_update'];
		$this->checked->ref_csv_tabelle=$result['0']['imex_csv_update'];
		//Wenn auch wirklich was da ist
		if (!empty($csv)) {
			//Daten an das Session Array �bergeben
			foreach ($csv as $key=>$value) {
				//Der letzte Eintrag ist ja immer leer
				if (!empty($value)) {
					//Jeder CSV Eintrag bekommt seinen Feld Eintrag wieder
					$_SESSION['csv_ar'][$value]=$feld[$key];
				}
			}
		}
		//Zur�ckgeben brauchen wir nichts.
	}

	/**
	 * Daten importieren
	 */
	function do_import()
	{
		$start=true;
		//Auswahlliste der Tabellen ans Template �bergeben
		$this->content->template['tabtar']=$this->cms->tbname;

		//Vorlage speichern wenn gew�nscht
		if (!empty($this->checked->makevorlage) && !empty($this->checked->importvorlage_name)) {
			IfNotSetNull($csvdtb);
			IfNotSetNull($felddtb);
			//Zuordnungen durchgehen und auslesen
			foreach ($_SESSION['csv_ar'] as $key=>$value) {
				//CSV auslesen
				$csvdtb.=$key.";";
				//Tabellen auslesen
				$felddtb.=$value.";";
			}
			//In die Datenbank speichern
			$sql=sprintf("INSERT INTO %s SET imex_name='%s', imex_csv='%s', imex_felder='%s', imex_csv_update='%s', imex_felder_update='%s' ",
				$this->cms->tbname['papoo_imex_daten'],
				$this->db->escape($this->checked->importvorlage_name),
				$this->db->escape($csvdtb),
				$this->db->escape($felddtb),
				$this->db->escape($this->checked->ref_csv_tabelle),
				$this->db->escape($this->checked->ref_csv_tabelle)
			);
			$this->db->query($sql);
			$this->content->template['vorsaved']="1";
		}
		//Import wirklich durchf�hren
		if (!empty($this->checked->makeimport)) {
			//Kein Start
			$start=false;
			global $db_praefix;
			//Auf Schritt 2 setzen
			$this->content->template['is_uploaded_step3']="ok";
			//Wenn CVS ohne Feldname
			if ($this->checked->format=="csvohne") {
				//CSV Datei einlesen in Array
				$csv=$this->get_csv_content();
				//File Array in fertiges Array konvertieren
				$cvs_ar=$this->convert_to_array($csv);
			}
			//Wenn CVS ohne Feldname
			if ($this->checked->format=="csvmit") {
				//CSV Datei einlesen in Array
				$csv=$this->get_csv_content();
				//File Array in fertiges Array konvertieren
				$cvs_ar=$this->convert_to_array($csv,$mit="1");
			}
			//Wenn CVS ohne Feldname
			if ($this->checked->format=="xml") {
				//CSV Datei einlesen in Array
				$xml=$this->get_xml_content();
				//File Array in fertiges Array konvertieren
				$cvs_ar=$this->convert_to_array_xml($xml);
			}
			//Daten eintragen
			IfNotSetNull($cvs_ar);
			$datenfertig=$this->insert_into_database($cvs_ar);

			//Zeitstempel
			$time=time();
			//Dateinamen
			$file="/dokumente/logs/".basename($this->checked->tabelle)."_".$time."_csv_import.sql";
			//Datei erzeugen zur DOkumentation des IMports (nur Debugging)
			//$this->diverse->write_to_file($file,$datenfertig);
			//Hochgeladenes File l�schen delete_file
			#echo basename($_SESSION['uploaded_file']);
			$this->diverse->delete_file("/dokumente/logs/".basename($_SESSION['uploaded_file']));
		}
		//2. Schritt des Imports
		else {
			//Import Schritt 2
			if (!empty($this->checked->startimport) or !empty($this->checked->startzuordnen) or !empty($this->checked->is_auswahl)) {
				if (!empty($this->checked->tabelle)) {
					if (!empty($this->checked->ins)) {
						//Update/Insert Art �bergeben f�r sp�ter
						$_SESSION['imex_update_insert']=$this->checked->ins;
					}
					if ($_SESSION['imex_update_insert']=="ins_upd") {
						$this->content->template['is_update']="ok";
					}
					//Wenn eine Vorlage gew�hlt wurde, Daten zuweisen
					if (!empty($this->checked->myvorlage)) {
						$this->make_session_from_vorlage();
					}
					//Kein Start
					$start=false;
					//Entfernfeld rausholen
					$entfeld=$this->get_entfernfeld();
					//Feld entfernen
					if (!empty($entfeld)) {
						unset ($_SESSION['csv_ar'][$entfeld]);
					}
					//Auf Schritt 2 setzen
					$this->content->template['is_uploaded_step2']="ok";
					//Name der ausgew�hlten Tabelle
					$this->content->template['tabelle']=$this->checked->tabelle;
					$this->content->template['format']=$this->checked->format;
					//Erste Zeile bei CSV Dateien
					if ($this->checked->format=="csvohne" or $this->checked->format=="csvmit") {
						//Dateifelder einlesen
						$csv_felder=$this->lese_erste_zeile_csv();
					}
					//Feldnamen bei xml Dateien
					if ($this->checked->format=="xml") {
						//Dateifelder einlesen
						$csv_felder=$this->lese_erste_zeile_xml();
					}
					IfNotSetNull($csv_felder);
					//Felder der ausew�hlten Datenbank auslesen
					$tabelle_felder=$this->lese_felder_tabelle($this->checked->tabelle);

					//wenn beide Eintr�ge ausgew�hlt sind
					if (!empty($this->checked->dat_tabelle) && !empty($this->checked->csv_tabelle)) {
						//Felder ins Session Array zur Weitergabe
						$_SESSION['csv_ar'][$this->checked->csv_tabelle]=$this->checked->dat_tabelle;
					}
					//�bergabe sprechend an TEmplate
					$templar=array();
					if (!empty($_SESSION['csv_ar'])) {
						foreach ($_SESSION['csv_ar'] as $key=>$value) {
							$templar[$key]['tab']=$tabelle_felder[$value];
							$templar[$key]['csv']=$csv_felder[$key];
						}
					}
					//Entfernfeld rausholen
					$entfeld_upd=$this->get_entfernfeld("update");
					//WEnn nicht die Zuordnung entfernt werden soll
					if (empty($entfeld_upd)) {
						IfNotSetNull($this->checked->ref_dat_tabelle);
						IfNotSetNull($this->checked->ref_csv_tabelle);
						IfNotSetNull($tabelle_felder[$this->checked->ref_dat_tabelle]);
						IfNotSetNull($csv_felder[$this->checked->ref_csv_tabelle]);
						//Namen der Felder f�r die Ausgabe im Template
						$this->content->template['ref_dat_tabelle'] = $tabelle_felder[$this->checked->ref_dat_tabelle];
						$this->content->template['ref_csv_tabelle'] = $csv_felder[$this->checked->ref_csv_tabelle];
						//Ids der Felder um die als hidden Felder weiterzugeben und auch wieder l�schen zu k�nnen
						$this->content->template['ref_dat_tabelle_value'] = $this->checked->ref_dat_tabelle;
						$this->content->template['ref_csv_tabelle_value'] = $this->checked->ref_csv_tabelle;
					}
					//Zuordnung aufheben und die IDs wieder freigeben
					else {
						//die IDs wieder freigeben f�r die Felder Liste
						$this->checked->ref_dat_tabelle="";
						$this->checked->ref_csv_tabelle="";
					}

					//Daten ans Template �bergeben
					$this->content->template['csv_tabelle_daten']=$templar;

					//Felder durchgehen ob schon drin, wenn ja dann rausflitschen
					if (!empty($csv_felder)) {
						//Alle Felder aus dem CSV durchgehen
						foreach ($csv_felder as $key=>$feld) {
							//Wenn noch nicht zugeordnet, �bergeben
							IfNotSetNull($this->checked->ref_csv_tabelle);
							if (empty($_SESSION['csv_ar'][$key]) && $key != $this->checked->ref_csv_tabelle) {
								//Zuweisen vorhandene nicht ausgew�hlte Felder
								$felder[$key]=$feld;
							}
						}
					}
					//Daten ans Template
					IfNotSetNull($felder);
					$this->content->template['csv_felder']=$felder;
					$tabelle_felder2=array();

					//Tabellenfelder durchgehen ob schon drin, wenn ja dann rausflitschen
					if (!empty($tabelle_felder)) {
						//Alle Felder aus der Tabelle durchgehen
						foreach ($tabelle_felder as $key=>$tab) {
							$isdrin=0;
							//Alle Session Arraydaten durchgehen
							if (!empty($_SESSION['csv_ar'])) {
								foreach ($_SESSION['csv_ar'] as $key2=>$value) {
									//Wenn schon drin - also  ausgew�hlt - dann auf drin setzen
									if($key==$key2 or $key==$this->checked->ref_dat_tabelle) {
										$isdrin=1;
									}
								}
							}
							//Wenn nocht nicht drin, dann an Template �bergeben
							if ($isdrin!=1) {
								$tabelle_felder2[$key]=$tab;
							}
						}
					}
					//Durchgehen ob schon zugeordnet
					$this->content->template['tabelle_felder']=$tabelle_felder2;
				}
				else {
					// Meldung zeigen..
					$this->content->template['no_table_selected'] = 1;
					//Template auf Fehler setzen.
					$this->content->template['is_start_import']="ok";
					$this->content->template['is_uploaded']="1";
				}
			}
			//Wenn eine Datei ausgew�hlt wurde
			if (!empty($this->checked->startupload)) {
				//Upload hat stattgefunden
				$this->content->template['is_uploaded']="ok";
				//Datei hochladen
				$this->do_upload();
				//Vorlagen raussuchen
				$vorlagen=$this->get_vorlagen();
				//Vorlgen Ans Template �bergeben
				$this->content->template['vorlage_array']=$vorlagen;

				//Endung CSV dann im Template auf CSV Voreinstellung setzen
				if (stristr($_SESSION['uploaded_file'],"csv")) {
					$this->content->template['csvchecked']='checked="checked"';
				}
				//Endung xml dann im Template auf xml Voreinstellung setzen
				if (stristr($_SESSION['uploaded_file'],"xml")) {
					$this->content->template['xmlcheck']='checked="checked"';
				}
			}
			//Ist noch Start, nix passiert, Upload Maske anbieten
			elseif ($start==true) {
				//Template auf Start setzen
				$this->content->template['is_start_import']="ok";
				//Session zur�cksetzen
				unset($_SESSION['csv_ar']);
				unset($_SESSION['imex_update_insert']);

				//WEnn Vorlage l�schen
				if (!empty($this->checked->delete_vorlage)) {
					$sql=sprintf("DELETE FROM %s WHERE imex_id='%d' LIMIT 1",
						$this->cms->tbname['papoo_imex_daten'],
						$this->db->escape($this->checked->myvorlage)
					);
					$this->db->query($sql);
					//Template ist gel�scht Aussage ausgeben
					$this->content->template['vorlage_geloescht']="ok";
				}
				//Vorlagen raussuchen
				$vorlagen=$this->get_vorlagen();
				//Vorlgen Ans Template �bergeben
				$this->content->template['vorlage_array']=$vorlagen;
			}
		}
	}

	/**
	 * Alle Vorlagen raussuchen die vorhanden sind
	 */
	function get_vorlagen()
	{
		//Abfrage formulieren, alle raussuchen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_imex_daten']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * Eine Datei hochladen in das interna/templates_c Verzeichnis
	 */
	function do_upload()
	{
		// Zielverzeichniss
		$destination_dir = 'dokumente/logs';
		// nicht erlaubte Dateiendungen
		$extensions = array ( 'php', 'php3', 'php4', 'phtml', 'cgi', 'pl');
		// Upload durchf�hren
		$upload_do = new file_upload(PAPOO_ABS_PFAD);
		// Wenn Files hochgeladen wurden
		if (count($_FILES) > 0) {
			// Durchf�hren und falls etwas schief geht
			if (!$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $extensions, 1)) {
				// falsch setzen
				$falsch = 1;
				// wenn etwas passiert ist, und ein Error vorliegt
				if (!empty ($upload_do->error)) {
					$falsch_exists = 1;
				}
			}
			else {
				$falsch = 0;
			}
		}
		// #######################################
		// wenn dder Upload geklappt hat, Daten eintragen.
		if (isset($falsch) && $falsch != 1) {
			$filename = $upload_do->file['name'];
			//$filesize = $upload_do->file['size'];
			$download = "/dokumente/logs/".$filename;
			$_SESSION['uploaded_file']=$download;
		}
		// Fehler beim Hochladen..
		else {
			// Meldung zeigen..
			$this->content->template['errortext'] = $this->content->template['message_20']."<p>$upload_do->error</p>";
			//Template auf Fehler setzen.
			$this->content->template['is_start_import']="ok";
			$this->content->template['is_upload']="";
		}
	}
}

$imex = new imex_class();
