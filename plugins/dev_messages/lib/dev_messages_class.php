<?php
/**
 * Class dev_messages_class
 */
class dev_messages_class
{
	var $datei_referenz; // Datei mit den Referenz Sprach-Definitionen
	var $datei_test; // Datei mit den zu testenden Sprach-Definitionen

	var $datei_export; // Datei in die Sprachdefinitionen gespeichert (exportiert) werden

	var $referenz; // Array mit Sprach-Definitionen der Referenz
	var $referenz_namen; // Array mit Namen der Sprach-Definitionen der Referenz
	var $referenz_werte; // Array mit Referenz-Wert f�r fehlende Sprach-Definition

	var $test; // Array mit Sprach-Definitionen der Test-Datei
	var $test_namen; // Array mit Namen der Sprach-Definitionen der Test-Datei

	var $differenz_namen; // Array mit Namen der fehlenden Sprach-Definitionen der Test-Datei

	var $content; // Dummie f�r Sprach-Definitionen aus den Dateien

	/**
	 * dev_messages_class constructor.
	 */
	function __construct()
	{
		global $content, $diverse, $checked, $db_praefix, $db, $user;
		$this->papoo_content = & $content;
		$this->checked=&$checked;
		$this->user=$user;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->diverse=&$diverse;

		// Variablen initialisieren.
		$this->datei_referenz = PAPOO_ABS_PFAD."/lib/messages/messages_frontend_de.inc.php";
		$this->datei_test = PAPOO_ABS_PFAD."/lib/messages/messages_frontend_en.inc.php";

		$this->datei_export = PAPOO_ABS_PFAD."/plugins/dev_messages/export/_dev_messages_export.inc.php";

		$this->referenz = array();
		$this->referenz_namen = array();
		$this->referenz_werte = array();
		$this->test = array();
		$this->test_namen = array();
		$this->differenz_namen = array();

		$this->content;

		$this->aktions_weiche();
	}

	// Die Aktions-Weiche der Klasse
	function aktions_weiche()
	{
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			if (strpos("XXX".$template, "dev_messages/templates/back.html")) {
				$this->make_sprachdatei_liste();
				if (!empty($this->checked->vergleichen)) {
					//Sprachdateien �bergeben
					$this->datei_referenz = PAPOO_ABS_PFAD."/lib/messages/".basename($this->checked->sprachreferenz);
					$this->datei_test = PAPOO_ABS_PFAD."/lib/messages/".basename($this->checked->vergleichsdatei);
					//Zur�ckgeben ans Tempalte damit die Selects ausgew�hlt bleiben
					$this->papoo_content->template['sprachreferenz']=basename($this->checked->sprachreferenz);
					$this->papoo_content->template['vergleichsdatei']=basename($this->checked->vergleichsdatei);
					//loslegen
					$this->init();
					//ausgeben
					$this->ausgabe_schirm($this->differenz_namen);
				}
				//$this->import_datei($this->datei_referenz, "FRONT", "de", "");
				//$this->messages_nummern_aktualisieren(4);
				//$this->groups_nummern_aktualisieren(3);
				//$this->export_datei($this->datei_export, "FRONT", "de", "");
			}
			if (strpos("XXX".$template, "dev_messages/templates/trans_back.html")) {
				//Sprachdaten rausholen
				$this->get_sprachen();
				//Dateiliste rausholen
				$this->get_dateilisten();
				if (!empty($this->checked->uebersetzen)) {
					//�bersetzung einleiten
					$vorschlag=$this->init_trans();
					$this->papoo_content->template['sprachdaten']=$vorschlag;
					$this->papoo_content->template['dev_messages']['template_weiche'] = "TRANSLATE";
					$this->papoo_content->template['pluginself_neu']="./plugin.php?menuid=".$this->checked->menuid."&template=dev_messages/templates/trans_google.html";
					//An Array �bergeben
				}
				else {
					$this->papoo_content->template['automat']=1;
				}
				if (!empty($this->checked->uebernehmen) and empty($this->checked->uebernehmen_alle)) {
					$this->save_eintrag("one");
				}
				if (!empty($this->checked->uebernehmen_alle)) {
					$this->save_eintrag("all");
				}
			}
			if (strpos("XXX".$template, "dev_messages/templates/trans_menu.html"))
			{
				$this->make_trans_menu();
			}
			if (strpos("XXX".$template, "dev_messages/templates/new_lang.html"))
			{
				$this->make_new_lang();
			}
			if (strpos("XXX".$template, "dev_messages/templates/trans_google.html"))
			{
				$this->make_trans_google();
			}
		}
	}

	/**
	 * Eine neue Sprache hinzuf�gen
	 */
	function make_new_lang()
	{
		if (!empty($this->checked->fertig)) {
			$this->papoo_content->template['fertig']="ok";
		}
		if (!empty($this->checked->insertlangnow)) {
			global $cms;
			$this->cms=&$cms;

			$sql=sprintf("INSERT INTO %s SET lang_short='%s', lang_long='%s', lang_img='%s', lang_dir='%s' ",
				$this->cms->tbname['papoo_name_language'],
				$this->db->escape($this->checked->lang_short),
				$this->db->escape($this->checked->lang_long),
				$this->db->escape($this->checked->lang_img),
				$this->db->escape($this->checked->lang_dir)
			);
			$this->db->query($sql);

			//Module setzen
			global $devtool_modlang;
			$devtool_modlang->modlang_do();

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=drin";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
	}

	/**
	 * Men� �bersetzung initialisieren
	 */
	function make_trans_menu()
	{
		//Sprachlisten setzen
		$this->get_sprachen();
		//Wenn eintragen
		if (!empty($this->checked->muebernehmen) and empty($this->checked->muebernehmen_alle)) {
			//Alte Eintr�ge speichern
			$this->save_old_menu();
			//Neuen Eintrag machen
			$this->save_menu_one();
		}
		if (!empty($this->checked->muebernehmen_alle)) {
			//Alte Eintr�ge speichern
			$this->save_old_menu();
			//Alle Eintr�ge machen die nicht leer sind
			$this->save_menu_all();
		}
		//Sprachen ausgew�hlt, dann rausholen
		if (!empty($this->checked->muebersetzen)) {
			$this->get_menu_lang();
		}
	}

	/**
	 * Alte Men� Eintr�ge speichern
	 */
	function save_old_menu()
	{
		global $dumpnrestore;
		$this->dumpnrestore = &$dumpnrestore;
		global $cms;
		$this->cms=&$cms;
		$tabelle = $this->cms->tbname['papoo_men_uint_language'];
		if (!empty($tabelle)) {
			$this->dumpnrestore->doupdateok = "ok";
			$this->dumpnrestore->donewdump = "ok";
			$this->dumpnrestore->dump_filename=PAPOO_ABS_PFAD."/dokumente/logs/sicherung_intmenu_lang".time().".sql";
			$this->dumpnrestore->dump_save(); // L�scht alte Sicherungs-Datei f�r append.
			$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($tabelle,0,10000);
			$this->dumpnrestore->dump_save("append");
		}
	}

	/**
	 * Alle Men�eintr�ge speichern die nicht leer sind
	 */
	function save_menu_all()
	{
		global $cms;
		$this->cms=&$cms;
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$resultlangm=$this->db->get_results($sql,ARRAY_A);
		foreach ($resultlangm as $lg) {
			if ($lg['lang_short']==$this->checked->mneusprach) {
				$langid=$lg['lang_id'];
			}
		}
		//Alle Eintr�ge durchgehen
		foreach ($this->checked->variablename as $key=>$menuxid) {
			//Pr�fen ob existiert
			$sql=sprintf("SELECT menuname FROM %s WHERE menuid_id='%d' AND lang_id='%d' ",
				$this->cms->tbname['papoo_men_uint_language'],
				$this->db->escape($menuxid),
				$this->db->escape($langid)
			);
			$resdat=$this->db->get_var($sql);
			if (!empty($this->checked->varinhalt[$key])) {
				if (empty($resdat)) {
					$sql=sprintf("INSERT INTO %s SET menuid_id='%d', lang_id='%d', menuname='%s'",
						$this->cms->tbname['papoo_men_uint_language'],
						$this->db->escape($menuxid),
						$this->db->escape($langid),
						$this->db->escape($this->checked->varinhalt[$key])
					);
					$this->db->query($sql);
				}
				else {
					//Updaten
					$sql=sprintf("UPDATE %s SET menuname='%s' WHERE menuid_id='%d' AND lang_id='%d' LIMIT 1",
						$this->cms->tbname['papoo_men_uint_language'],
						$this->db->escape($this->checked->varinhalt[$key]),
						$this->db->escape($menuxid),
						$this->db->escape($langid)
					);
					$this->db->query($sql);
				}
			}
		}
	}

	/**
	 * Einen Eintrag des Men�punktes speichern
	 */
	function save_menu_one()
	{
		global $cms;
		$this->cms=&$cms;
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$resultlangm=$this->db->get_results($sql,ARRAY_A);
		foreach ($resultlangm as $lg) {
			if ($lg['lang_short']==$this->checked->mneusprach) {
				$langid=$lg['lang_id'];
			}

		}
		//Id rausholen
		foreach ($this->checked->muebernehmen as $key=>$value) {
			$menuid=$this->checked->variablename[$key];
			$keyid=$key;
		}
		//Pr�fen ob existiert
		$sql=sprintf("SELECT menuname FROM %s WHERE menuid_id='%d' AND lang_id='%d' ",
			$this->cms->tbname['papoo_men_uint_language'],
			$this->db->escape($menuid),
			$this->db->escape($langid)
		);
		$resdat=$this->db->get_var($sql);
		if (!empty($this->checked->varinhalt[$keyid])) {
			if (empty($resdat)) {
				$sql=sprintf("INSERT INTO %s SET menuid_id='%d', lang_id='%d', menuname='%s'",
					$this->cms->tbname['papoo_men_uint_language'],
					$this->db->escape($menuid),
					$this->db->escape($langid),
					$this->db->escape($this->checked->varinhalt[$keyid])
				);
				$this->db->query($sql);
			}
			else {
				//Updaten
				$sql=sprintf("UPDATE %s SET menuname='%s' WHERE menuid_id='%d' AND lang_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_men_uint_language'],
					$this->db->escape($this->checked->varinhalt[$keyid]),
					$this->db->escape($menuid),
					$this->db->escape($langid)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * EIntr�ge der Men� zu den Sprachen rausholen
	 */
	function get_menu_lang()
	{
		global $cms;
		$this->cms=&$cms;
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$resultlangm=$this->db->get_results($sql,ARRAY_A);
		foreach ($resultlangm as $lg) {
			if ($lg['lang_short']==$this->checked->neusprach) {
				$this->checked->neusprach=$lg['lang_id'];
			}
			if ($lg['lang_short']==$this->checked->referenzsprache) {
				$this->checked->referenzsprache=$lg['lang_id'];
			}
		}
		//Referenzsprache
		$sql=sprintf("SELECT * FROM %s WHERE lang_id='%d'",
			$this->cms->tbname['papoo_men_uint_language'],
			$this->db->escape($this->checked->referenzsprache)
		);
		$result1=$this->db->get_results($sql,ARRAY_A);
		//zu �bersetzende Sprache
		$sql=sprintf("SELECT * FROM %s WHERE lang_id='%d'",
			$this->cms->tbname['papoo_men_uint_language'],
			$this->db->escape($this->checked->neusprach)
		);
		$result2=$this->db->get_results($sql,ARRAY_A);
		//Eintr�ge ans Template zur�ckgeben
		$this->papoo_content->template['neusprach']=$this->checked->neusprach;
		$this->papoo_content->template['referenzsprache']=$this->checked->referenzsprache;
		$this->papoo_content->template['mneusprach']=$this->checked->neusprach;
		$this->papoo_content->template['mreferenzsprache']=$this->checked->referenzsprache;
		$this->papoo_content->template['dev_messages']['template_weiche'] = "TRANSLATE";
		//Neue array
		$neuarray=array();
		$i=0;

		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$resultlangm=$this->db->get_results($sql,ARRAY_A);
		foreach ($resultlangm as $lg) {
			if ($lg['lang_id']==$this->checked->neusprach) {
				$this->checked->neusprach=$lg['lang_short'];
			}
			if ($lg['lang_id']==$this->checked->referenzsprache) {
				$this->checked->referenzsprache=$lg['lang_short'];
			}
		}
		$this->langpair=$this->get_langpair();
		if (!empty($result1)) {
			foreach ($result1 as $dat) {
				$neuarray[$dat['menuid_id']]['variable']=$dat['menuid_id'];
				$neuarray[$dat['menuid_id']]['original']=$dat['menuname'];
				$neuarray[$dat['menuid_id']]['bisher']=$result2[$i]['menuname'];
				if (($this->checked->automat==1)) {
					$neuarray[$dat['menuid_id']]['vorschlag']=$this->translate($dat['menuname']);
				}
				$neuarray[$dat['menuid_id']]['counter']=$i;
				$i++;
			}
		}
		$this->papoo_content->template['langdaten_men']=$neuarray;
	}

	/**
	 * Einen Spracheintrag speichern
	 *
	 * @param string $nmb
	 */
	function save_eintrag($nmb="")
	{
		//Sicherung der alten Datein anlegen
		$this->make_alt_save();
		//Nur einen speichern
		if ($nmb=="one") {
			$this->save_one();
		}
		//Alle speichern
		if ($nmb=="all") {
			$this->save_all();
		}
		//Variablen zur�ckgeben
		$vorschlag=$this->init_trans();
		$this->papoo_content->template['sprachdaten']=$vorschlag;
		$this->papoo_content->template['dev_messages']['template_weiche'] = "TRANSLATE";
	}

	/**
	 * Nur einen Eintrag speichern
	 */
	function save_one()
	{
		$transfile_trans=$this->get_transfile_trans();
		if (file_exists($transfile_trans)) {
			require($transfile_trans);
		}
		$this->transdata_trans = $this->content->template;
		foreach ($this->checked->uebernehmen as $key=>$value) {
			$drinkey=$key;
		}
		$i=0;
		if (!empty($this->transdata)) {
			foreach ($this->transdata as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3=>$dat) {
								//$dat enth�lt die neuen Sprachdaten
								if (is_array($dat)) {
									foreach ($dat as $key4=>$dat2) {
										//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
										$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\'][\''.$key4.'\']';
										if ($drinkey==$i) {
											$insert[$i]['bisher']=$this->checked->varinhalt[$drinkey];
										}
										else {
											$insert[$i]['bisher']=$this->transdata_trans[$key][$key2][$key3][$key4];
										}
										$insert[$i]['counter']=$i;
										$i++;
									}
								}
								else {
									//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
									$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\']';
									if ($drinkey==$i) {
										$insert[$i]['bisher']=$this->checked->varinhalt[$drinkey];
									}
									else {
										$insert[$i]['bisher']=$this->transdata_trans[$key][$key2][$key3];
									}
									$insert[$i]['counter']=$i;
									$i++;
								}
							}
						}
						else {
							//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
							$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\']';
							if ($drinkey==$i) {
								$insert[$i]['bisher']=$this->checked->varinhalt[$drinkey];
							}
							else {
								$insert[$i]['bisher']=($this->transdata_trans[$key][$key2]);
							}
							$insert[$i]['counter']=$i;
							$i++;
						}
					}
				}
				//Kein Array, dann klassische messages
				else {
					//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
					$insert[$i]['variable']='$this->content->template[\''.$key.'\']';
					if ($drinkey==$i) {
						$insert[$i]['bisher']=$this->checked->varinhalt[$drinkey];
					}
					else {
						$insert[$i]['bisher']=($this->transdata_trans[$key]);
					}
					$insert[$i]['counter']=$i;
					$i++;
				}
			}
		}
		$inhalt="<?php \n";
		$inhalt.='/**
* Deutsche Text-Daten des Plugins "bookmark" für das Backend
* !! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!
*/';
		$inhalt.="\n \n";
		foreach ($insert as $zeile) {
			$zeile['bisher']=str_ireplace("'","\"",$zeile['bisher']);
			$zeile['bisher']=$this->diverse->encode_quote($zeile['bisher']);
			$inhalt.=$zeile['variable']."="."'".$zeile['bisher']."';\n";
		}
		$inhalt.="\n \n ?>";

		$file=$this->get_file_name();
		$schreib=$this->check_schreib($file);
		if ($schreib==true) {
			$this->diverse->write_to_file($file,$inhalt);
		}
		else {
			$this->papoo_content->template['nicht_be']=$file;
		}
	}

	/**
	 * Checken ob beschreibbar ist
	 *
	 * @param string $file
	 * @return bool
	 */
	function check_schreib($file="")
	{
		$file = PAPOO_ABS_PFAD . $file;
		if (!empty($file) and file_exists($file)) {
			if (is_writable($file)) {
				return true;
			}
			else {
				chmod( $file, 0777 );
				if (is_writable($file)) {
					return true;
				}
				else {
					return false;
				}
			}
		}
		return false;
	}

	/**Alle Eintr�ge speichern
	 */
	function save_all()
	{
		$transfile_trans=$this->get_transfile_trans();
		if (file_exists($transfile_trans)) {
			require($transfile_trans);
		}
		$this->transdata_trans = $this->content->template;

		$i=0;
		if (!empty($this->transdata)) {
			foreach ($this->transdata as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3=>$dat) {
								//$dat enth�lt die neuen Sprachdaten
								if (is_array($dat)) {
									foreach ($dat as $key4=>$dat2) {
										//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
										$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\'][\''.$key4.'\']';
										if (!empty($this->checked->varinhalt[$i])) {
											$insert[$i]['bisher']=$this->checked->varinhalt[$i];
										}
										else {
											$insert[$i]['bisher']=$this->transdata_trans[$key][$key2][$key3][$key4];
										}
										$insert[$i]['counter']=$i;
										$i++;
									}
								}
								else {
									//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
									$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\']';
									if (!empty($this->checked->varinhalt[$i])) {
										$insert[$i]['bisher']=$this->checked->varinhalt[$i];
									}
									else {
										$insert[$i]['bisher']=$this->transdata_trans[$key][$key2][$key3];
									}
									$insert[$i]['counter']=$i;
									$i++;
								}
							}
						}
						else {
							//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
							$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\']';
							if (!empty($this->checked->varinhalt[$i])) {
								$insert[$i]['bisher']=$this->checked->varinhalt[$i];
							}
							else {
								$insert[$i]['bisher']=($this->transdata_trans[$key][$key2]);
							}
							$insert[$i]['counter']=$i;
							$i++;
						}
					}
				}
				//Kein Array, dann klassische messages
				else {
					//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
					$insert[$i]['variable']='$this->content->template[\''.$key.'\']';
					if (!empty($this->checked->varinhalt[$i])) {
						$insert[$i]['bisher']=$this->checked->varinhalt[$i];
					}
					else {
						$insert[$i]['bisher']=($this->transdata_trans[$key]);
					}
					$insert[$i]['counter']=$i;
					$i++;
				}
			}
		}
		$inhalt="<?php \n";
		$inhalt.='/**
* Deutsche Text-Daten des Plugins "bookmark" für das Backend
* !! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!
*/';
		$inhalt.="\n \n";
		foreach ($insert as $zeile) {
			$zeile['bisher']=str_ireplace("'","\"",$zeile['bisher']);
			$zeile['bisher']=$this->diverse->encode_quote($zeile['bisher']);
			$inhalt.=$zeile['variable']."="."'".$zeile['bisher']."';\n";
		}
		$inhalt.="\n \n ?>";

		$file=$this->get_file_name();
		$schreib=$this->check_schreib($file);
		if ($schreib==true) {
			$this->diverse->write_to_file($file,$inhalt);
		}
		else {
			$this->papoo_content->template['nicht_be']=$file;
		}
	}

	/**
	 * Filename rauskriegen
	 */
	function get_file_name()
	{
		//Frontend oder Backend?
		$fb="frontend";
		if ($this->checked->frontback==2) {
			$fb="backend";
		}
		switch ($this->checked->dateien) {
			//Dateien aus dem Frontend
		case  "System":
			$file="/lib/messages/messages_".$fb."_".basename($this->checked->neusprach).".inc.php";
			break;

		default:
			$file="/plugins/".basename($this->checked->dateien)."/messages/messages_".$fb."_".basename($this->checked->neusprach).".inc.php";
			$file=str_ireplace("Plugin: ","",$file);
			break;
		}
		return $file;
	}
	/**
	 * Alte Sprachdatei als Sicherung hinterlegen
	 */
	function make_alt_save()
	{
		//Filename rauskriegen
		$file=$this->get_file_name();
		$fb="frontend";
		if ($this->checked->frontback==2) {
			$fb="backend";
		}
		$inhalt=$this->diverse->open_file($file);
		$time=time();
		$datei="/dokumente/logs/".basename($this->checked->dateien)."_".$time."_"."messages_".$fb."_".basename($this->checked->neusprach).".inc.html";
		$datei=str_ireplace(": ","_",$datei);
		$this->diverse->write_to_file($datei,$inhalt);
	}

	/**
	 * Liste der vorhandenen Sprachen initialisieren
	 */
	function get_sprachen()
	{
		global $cms;
		//Liste der Sprachen aus der Datenbank
		$sql = sprintf("SELECT lang_id, lang_long, lang_img, lang_short FROM %s ",
			$cms->papoo_name_language
		);
		$temp_sprachen = $this->db->get_results($sql, ARRAY_A);
		$this->papoo_content->template['plugin_sprachdatar']=$temp_sprachen;
	}

	/**
	 * Liste der Verzeichnisse einlesen
	 * Plugins, Frontend, Backend
	 * Um eine Auswahl anzubieten
	 */
	function get_dateilisten()
	{
		$dirs=$this->diverse->lese_dir("/plugins");
		$i=1;
		$neudir=array();
		$neudir['0']['name']="System";
		$neudir['0']['counter']=0;

		if (!empty($dirs)) {
			foreach ($dirs as $fdir) {
				if (strstr($fdir['name'],"svn") || strstr($fdir['name'],"xml") || strstr($fdir['name'],"txt") || strstr($fdir['name'],"x_ml")) {
					continue;
				}
				$neudir[$i]['name']="Plugin: ".$fdir['name'];
				$neudir[$i]['counter']=$i;
				$i++;
			}
		}
		$this->papoo_content->template['plugin_sprachdatar_dat']=$neudir;
	}

	/**
	 *Liste der Sprachdateien erzeugen
	 */
	function make_sprachdatei_liste()
	{
		//normale Sprachdateien raussuchen
		//PAPOO_ABS_PFAD;
		$dirs=$this->diverse->lese_dir("/lib/messages");
		$i=0;
		$neudir=array();
		if (!empty($dirs)) {
			foreach ($dirs as $fdir) {
				if (!strstr($fdir['name'],"messages") || strstr($fdir['name'],"check")) {
					continue;
				}
				$neudir[$i]['name']=$fdir['name'];
				$neudir[$i]['counter']=$i;
				$i++;
			}
		}
		$this->papoo_content->template['plugin_sprachdatar']=$neudir;
	}

	/**
	 * Die zu �bersetzende Datei ermitteln
	 */
	function get_transfile_trans()
	{
		//Frontend oder Backend?
		$fb="frontend";
		if ($this->checked->frontback==2) {
			$fb="backend";
		}
		//Spezialfall
		switch ($this->checked->dateien) {
			//Dateien aus dem Frontend
		case  "System":
			$file=PAPOO_ABS_PFAD."/lib/messages/messages_".$fb."_".basename($this->checked->neusprach).".inc.php";
			break;

		default:
			$file=PAPOO_ABS_PFAD."/plugins/".basename($this->checked->dateien)."/messages/messages_".$fb."_".basename($this->checked->neusprach).".inc.php";
			$file=str_ireplace("Plugin: ","",$file);
			break;
		}
		return $file;
	}

	/**
	 * Die zu �bersetzende Datei ermitteln
	 */
	function get_transfile_ref()
	{
		//Frontend oder Backend?
		$fb="frontend";
		if ($this->checked->frontback==2) {
			$fb="backend";
		}

		//Spezialfall
		switch ($this->checked->dateien) {
			//Dateien aus dem Frontend
		case  "System":
			$file=PAPOO_ABS_PFAD."/lib/messages/messages_".$fb."_".basename($this->checked->referenzsprache).".inc.php";
			break;

		default:
			$file=PAPOO_ABS_PFAD."/plugins/".basename($this->checked->dateien)."/messages/messages_".$fb."_".basename($this->checked->referenzsprache).".inc.php";
			$file=str_ireplace("Plugin: ","",$file);
			break;
		}
		return $file;
	}

	/**
	 * Daten �bersetzen
	 */
	function init_trans()
	{
		//ZUerst die zu �bersetzende Datei ermitteln
		$transfile_trans=$this->get_transfile_trans();
		if (file_exists($transfile_trans)) {
			require($transfile_trans);
		}

		$this->transdata_trans = $this->content->template;
		//Dann die Referenzdatei ermitteln f�r die autom. �bersetzung
		$transfile_ref=$this->get_transfile_ref();

		global $checked;
		$file=$transfile_ref;
		$insert=$original=array();
		$this->translate_file = $transfile_ref;
		if (file_exists($transfile_ref)) {
			//Sprachkombi finden
			$this->langpair=$this->get_langpair();
			if (!empty($file)) {
				require($this->translate_file);
				$this->transdata = $this->content->template;

				#$this->translate($dat);
				$i=0;

				//Array durchgehen bei neuen plugin sprachdaten
				if (!empty($this->transdata)) {
					foreach ($this->transdata as $key=>$value) {
						if (is_array($value)) {
							foreach ($value as $key2=>$value2) {
								if (is_array($value2)) {
									foreach ($value2 as $key3=>$dat) {
										//$dat enth�lt die neuen Sprachdaten
										if (is_array($dat)) {
											foreach ($dat as $key4=>$dat2) {
												$fertigtrans=$this->translate($dat2);
												//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
												$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\'][\''.$key4.'\']';
												$insert[$i]['vorschlag']=$fertigtrans;
												//$this->transdata_trans

												$insert[$i]['bisher']=htmlentities($this->transdata_trans[$key][$key2][$key3][$key4],ENT_NOQUOTES,'UTF-8');
												$insert[$i]['original']=htmlentities($dat2,ENT_NOQUOTES,'UTF-8');
												if (empty($insert[$i]['bisher']) or $insert[$i]['bisher']==$insert[$i]['original']) {
													$insert[$i]['empty']="1";
												}
												$insert[$i]['counter']=$i;
												$i++;
											}
										}
										else {
											$fertigtrans=$this->translate($dat);
											//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
											$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\'][\''.$key3.'\']';
											$insert[$i]['vorschlag']=$fertigtrans;
											$insert[$i]['bisher']=htmlentities($this->transdata_trans[$key][$key2][$key3],ENT_NOQUOTES,'UTF-8');
											$insert[$i]['original']=htmlentities($dat,ENT_NOQUOTES,'UTF-8');
											if (empty($insert[$i]['bisher']) or $insert[$i]['bisher']==$insert[$i]['original']) {
												$insert[$i]['empty']="1";
											}
											$insert[$i]['counter']=$i;
											$i++;
										}
									}
								}
								else {
									$fertigtrans=$this->translate($value2);
									//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
									$insert[$i]['variable']='$this->content->template[\''.$key.'\'][\''.$key2.'\']';
									$insert[$i]['vorschlag']=$fertigtrans;
									$insert[$i]['bisher']=htmlentities($this->transdata_trans[$key][$key2],ENT_NOQUOTES,'UTF-8');
									$insert[$i]['original']=htmlentities($value2,ENT_NOQUOTES,'UTF-8');
									if (empty($insert[$i]['bisher']) or $insert[$i]['bisher']==$insert[$i]['original']) {
										$insert[$i]['empty']="1";
									}
									$insert[$i]['counter']=$i;
									$i++;
								}
							}
						}
						//Kein Array, dann klassische messages
						else {
							$fertigtrans=$this->translate($value);
							//$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
							$insert[$i]['variable']='$this->content->template[\''.$key.'\']';
							$insert[$i]['vorschlag']=$fertigtrans;
							$insert[$i]['bisher']=htmlentities($this->transdata_trans[$key],ENT_NOQUOTES,'UTF-8');
							$insert[$i]['original']=htmlentities($value,ENT_NOQUOTES,'UTF-8');
							if (empty($insert[$i]['bisher']) or $insert[$i]['bisher']==$insert[$i]['original']) {
								$insert[$i]['empty']="1";
							}

							$insert[$i]['counter']=$i;
							$i++;
						}
					}
				}
			}
		}
		return $insert;
	}

	/**
	 * Sprachkombin finden
	 */
	function get_langpair()
	{
		//Ref. Sprache
		$from=$this->checked->referenzsprache;
		$this->papoo_content->template['referenzsprache']=$from;
		//Zu Sprache �bersetzen
		$to=$this->checked->neusprach;
		$this->papoo_content->template['neusprach']=$to;
		//Datei �bergeben
		$this->papoo_content->template['dateien']=$this->checked->dateien;;
		$this->papoo_content->template['frontback']=$this->checked->frontback;;
		$this->papoo_content->template['automat']=$this->checked->automat;
		//Wenn nicht automatik, dann false

		$kombi=$from."|".$to;
		//Kombin Array ,""
		#$komar=array("ar|en","zh|en","de|en","de|fr","en|ar","en|zh-TW","en|zh-CN","en|de","en|fr","en|it","en|ja","en|ko","en|pt","en|ru","en|es","fr|de","fr|en","it|en","pt|en","en|nl","en|el","en|ja");
		$_SESSION['translate_lang_pair']=$kombi;
		if ($this->checked->automat!=1) {
			return false;
		}
		return $kombi;
		return false;
	}

	/**
	 * @param $text
	 * @param string $from
	 * @param string $to
	 */
	function google_translate($text, $from = '', $to = 'en')
	{

	}

	/**
	 * Convert UTF-8 Escape sequences in a string to UTF-8 Bytes
	 * @return String UTF-8 String
	 * @param $str String
	 */
	function _unescapeUTF8EscapeSeq($str)
	{
		return preg_replace_callback("/\\\u([0-9a-f]{4})/i",
			function($matches) {
				return html_entity_decode('&#x'.$matches[1].';', ENT_NOQUOTES, 'UTF-8');
			},
			$str);
	}
	function make_trans_google()
	{
		$translate=urldecode($this->checked->to_translate);
		$this->langpair=$_SESSION['translate_lang_pair'];
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head></head>
<body><textarea style="width:99%;height:300px;">
';
		echo $fertig=$this->translate($translate,$_SESSION['translate_lang_pair']);
		echo '</textarea></body>';
		exit();
	}

	/**
	 * �bersetzung durchf�hren
	 *
	 * @param mixed $data
	 * @param string $langpair
	 * @return string|UTF
	 */
	function translate($data="", $langpair="")
	{
		if ($this->langpair!=false) {
			$data=html_entity_decode($data,ENT_NOQUOTES,'UTF-8');

			//Verz�gerung um 1/2 Sekunde damit Google nicht blockt
			#usleep(500000);

			require_once (PAPOO_ABS_PFAD . "/lib/classes/extlib/Snoopy.class.inc.php");
			// $url="https://85.214.57.82/sportnaviTest/members/devlogin.aspx?email=cusrtis@cssaasas.de&passwort=toni";
			$data=str_replace("\n","",$data);
			$data=str_replace("\r","",$data);
			$data=str_replace("  ","",$data);
			$data=urlencode($data);
			$url="http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=$data&langpair=$this->langpair";
			#require_once(PAPOO_ABS_PFAD."/lib/classes/xmlparser_class.php");
			$html = new Snoopy();
			$html->agent="Mozilla/5.0";

			$html->fetch($url);

			$response=$html->results;
			if (preg_match("/{\"translatedText\":\"([^\"]+)\"/i", $response, $matches)) {
				$text= self::_unescapeUTF8EscapeSeq($matches[1]);
			}
			return $text;
		}
		return "";
	}

	function init()
	{
		// 1. Referenz-Datei einbinden und Definitionen an $referenz �bergeben.
		require($this->datei_referenz);
		$this->referenz = $this->content->template;
		$this->content = "";

		foreach ($this->referenz as $name => $wert) {
			if (is_array($wert)) {
				$this->referenz_namen = array_merge($this->referenz_namen, $this->get_array_name($name, $wert, "REFERENZ"));
			}
			else {
				$this->referenz_namen[] = "['".$name."']";
				$this->referenz_werte["['".$name."']"] = $wert;
			}
		}

		// 2. Test-Datei einbinden und Definitionen an $test �bergeben.
		require_once($this->datei_test);
		$this->test = $this->content->template;
		$this->content = "";

		foreach ($this->test as $name => $wert) {
			if (is_array($wert)) {
				$this->test_namen = array_merge($this->test_namen, $this->get_array_name($name, $wert));
			}
			else {
				$this->test_namen[] = "['".$name."']";
			}
		}

		// 3. Referenz mit Test vergleichen
		foreach ($this->referenz_namen as $name) {
			if (!in_array($name, $this->test_namen)) {
				$this->differenz_namen[] = $name;
			}
		}
	}

	/**
	 * wenn $modus = "REFERENZ", dann $wert in $referenz_werte speichern
	 *
	 * @param $array_name
	 * @param $array_wert
	 * @param string $modus
	 * @return array
	 */
	function get_array_name ($array_name, $array_wert, $modus = "")
	{
		$temp_return = array();

		foreach ($array_wert as $name => $wert) {
			if (is_array($wert)) {
				$temp_return = array_merge($temp_return, $this->get_array_name($array_name."']['".$name, $wert, $modus));
			}
			else {
				$temp_return[] = "['".$array_name."']['".$name."']";
				$this->referenz_werte["['".$array_name."']['".$name."']"] = $wert;
			}
		}
		return $temp_return;
	}

	/**
	 * gibt die Namen der $sprachdefinitionen inkl. Referenzwerte aus $this->referenz_werte aus
	 *
	 * @param array $sprachdefinitionen
	 */
	function ausgabe_schirm($sprachdefinitionen = array())
	{
		$this->papoo_content->template['dev_messages']['template_weiche'] = "AUSGABE_SCHIRM";

		$this->papoo_content->template['dev_messages']['datei_referenz'] = & $this->datei_referenz;
		$this->papoo_content->template['dev_messages']['datei_test'] = & $this->datei_test;

		$this->papoo_content->template['dev_messages']['sprachdefinitionen'] = & $sprachdefinitionen;

		// Referenzwerte mit "nodecode:" erweitern, einige Ersetzungen durchf�hren und �bergeben
		$temp_referenzen = array();
		$ersetzungen_org = array("\t", "<br>", "<br />");
		$ersetzungen_ersatz = array("", "\n", "\n");

		foreach ($this->referenz_werte as $name => $wert) {
			$temp_referenzen[$name] = str_replace($ersetzungen_org, $ersetzungen_ersatz, "nodecode:".$wert);
		}
		$this->papoo_content->template['dev_messages']['referenzwerte'] = $temp_referenzen;
	}

	/**
	 * @param string $datei
	 * @param string $frontback
	 * @param string $sprache
	 * @param string $plugin
	 */
	function import_datei($datei = "", $frontback = "FRONT", $sprache = "de", $plugin = "")
	{
		$this->papoo_content->template['dev_messages']['template_weiche'] = "IMPORT_DATEI";
		$this->papoo_content->template['dev_messages']['datei_referenz'] = & $this->datei_referenz;

		if (!empty($this->referenz_werte)) {
			$counter = 10;
			$ersetzungen_org = array("\t", "<br>", "<br />");
			$ersetzungen_ersatz = array("", "\n", "\n");

			foreach($this->referenz_werte as $name => $wert) {
				// 1. Eintr�ge in Tabelle dev_messages vornehmen
				$sql = sprintf("INSERT INTO %s SET msg_grp_id='1', msg_order_id='%d', msg_name='%s', msg_name_alt='%s'",

					$this->db_praefix."dev_messages",
					$counter,
					$this->db->escape($name),
					$this->db->escape($name)
				);
				$this->db->query($sql);

				$msg_id = $this->db->insert_id;
				$counter += 10;

				// 2. Eintr�ge in Tabelle dev_messages_language vornehmen (vorher Ersetzungen durchf�hren)
				$wert = str_replace($ersetzungen_org, $ersetzungen_ersatz, $wert);

				$sql = sprintf("INSERT INTO %s SET msglang_msg_id='%d', msglang_lang_short='%s', msglang_text='%s'",

					$this->db_praefix."dev_messages_language",
					$msg_id,
					$this->db->escape($sprache),
					$this->db->escape($wert)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Exportiert die Sprach-Definitionen in die Datei $datei
	 * f�r das Front-/Back-end
	 * der Sprache $sprache
	 * (des Plugins $plugin falls angegeben, sonst leer)
	 *
	 * @param string $datei
	 * @param string $frontback
	 * @param string $sprache
	 * @param string $plugin
	 */
	function export_datei($datei = "", $frontback = "FRONT", $sprache = "de", $plugin = "")
	{
		$this->papoo_content->template['dev_messages']['template_weiche'] = "EXPORT_DATEI";

		$sql = sprintf("SELECT * FROM %s as t1, %s as t2, %s as t3
						WHERE
						t1.msg_grp_id=t2.msggrp_id AND t1.msg_id=t3.msglang_msg_id

						AND t3.msglang_lang_short='%s' AND t2.msggrp_frontback='%s'

						ORDER BY t2.msggrp_parent_id, t2.msggrp_order_id, t1.msg_order_id",

			$this->db_praefix."dev_messages",
			$this->db_praefix."dev_messages_group",
			$this->db_praefix."dev_messages_language",

			$this->db->escape($sprache),
			$this->db->escape($frontback)

		);
		$temp = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param array $sprachdefinitionen
	 * @return bool
	 */
	function export_datei_BACKUP($sprachdefinitionen = array())
	{
		$the_return = true;
		$eintrag = "";
		if (empty($sprachdefinitionen)) {
			$the_return = false;
		}
		else {
			$datei = fopen($this->datei_export, "wb");
			foreach ($sprachdefinitionen as $name => $wert)
			{
				$eintrag = "/* ".$this->referenz_werte[$name]." */\n";
				$eintrag .= '$this->content->template'.$name.' = \''.$wert.'\';'."\n\n";
				fwrite($datei, $eintrag);
			}
			fclose($datei);
		}

		return $the_return;
	}

	/**
	 * Nummeriert alle Messages der Gruppe $gruppen_id neu druch
	 *
	 * @param int $gruppen_id
	 */
	function messages_nummern_aktualisieren ($gruppen_id = 0)
	{
		$sql = sprintf("SELECT msg_id FROM %s WHERE msg_grp_id='%d' ORDER BY msg_order_id",
			$this->db_praefix."dev_messages",
			$gruppen_id
		);
		$tmp_ids = $this->db->get_results($sql, ARRAY_A);

		if (!empty($tmp_ids))
		{
			$counter = 10;
			foreach ($tmp_ids as $message)
			{
				$sql = sprintf("UPDATE %s SET msg_order_id='%d' WHERE msg_id='%d'",
					$this->db_praefix."dev_messages",
					$counter,
					$message['msg_id']
				);
				$this->db->query($sql);
				$counter +=10;
			}

		}
	}

	/**
	 * Nummeriert alle Gruppen neu durch, deren msggrp_parent_id = $parent_id ist
	 *
	 * @param int $parent_id
	 */
	function groups_nummern_aktualisieren ($parent_id = 0)
	{
		$sql = sprintf("SELECT msggrp_id FROM %s WHERE msggrp_parent_id='%d' ORDER BY msggrp_order_id",
			$this->db_praefix."dev_messages_group",
			$parent_id
		);
		$tmp_ids = $this->db->get_results($sql, ARRAY_A);

		if (!empty($tmp_ids)) {
			$counter = 10;
			foreach ($tmp_ids as $group) {
				$sql = sprintf("UPDATE %s SET msggrp_order_id='%d' WHERE msggrp_id='%d'",
					$this->db_praefix."dev_messages_group",
					$counter,
					$group['msggrp_id']
				);
				$this->db->query($sql);
				$counter +=10;
			}
		}
	}
}
$dev_messages = new dev_messages_class();
