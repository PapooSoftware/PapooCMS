<?php

/**
 * Class lang_export_class
 */
#[AllowDynamicProperties]
class lang_export_class
{
	/** @var array Tabellenname und Feldnamen des lang_id-Feldes */
	var $export_tables;
	/** @var bool Onlineon Portal oder nicht */
	var $onlineon = false;

	/**
	 * lang_export_class constructor.
	 */
	function __construct()
	{
		global $user, $db, $db_praefix, $dumpnrestore, $checked, $cms, $diverse, $content;
		$this->user = & $user;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->dumpnrestore = &$dumpnrestore;
		$this->checked = & $checked;
		$this->cms = & $cms;
		$this->diverse = & $diverse;
		$this->content = & $content;

		$this->export_tables = array();

		$this->make_lang_export();
	}

	/**
	 * Aktions-Weiche
	 */
	function make_lang_export()
	{
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			#$template=basename($template);

			if (strpos("XXX".$template, "lang_export_back.html")) {
				//Referenzdaten umkopieren
				if (!empty($this->checked->ref_submit) && $this->checked->lang_select_id_ref!=$this->checked->lang_select_id_into) {
					$this->sprach_kop();
					//Wurde kopiert setzen
					$this->content->template['plugin']['lang_export']['template_weiche'] = "KOPOK";
				}
				else {
					//Datei l�schen
					if (isset($this->checked->lang_export_delete) && $this->checked->lang_export_delete) {
						$this->dumpnrestore->dump_save(); // L�scht Sicherungs-Datei
						$this->content->template['plugin']['lang_export']['template_weiche'] = "END";
						$this->delete_csv();
					}
					//Datei erzeugen zum Download
					elseif (isset($this->checked->lang_select_id) && $this->checked->lang_select_id) {
						$this->start_export_now($this->checked->lang_select_id);
					}
					//Star anzeigen
					else {
						$this->content->template['plugin']['lang_export']['template_weiche'] = "START";
					}
				}
			}
		}
	}

	private function delete_csv()
	{
		$files=$this->diverse->lese_dir("/interna/templates_c/");

		if (is_array($files)) {
			foreach ($files as $key=>$value) {
				if(stristr($value['name'],"csv")) {
					unlink(PAPOO_ABS_PFAD."/interna/templates_c/".$value['name']);
				}
			}
		}
	}

	/**
	 * Export starten
	 *
	 * @param $langid
	 */
	function start_export_now($langid)
	{
		$this->dumpnrestore->dump_save(); // L�scht Sicherungs-Datei
		$this->init_export_tables();
		$this->lang_export_do($langid);
		$this->content->template['plugin']['lang_export']['dump_filename'] = $this->dumpnrestore->dump_filename;
		$this->content->template['plugin']['lang_export']['template_weiche'] = "DOWNLOAD";

		$this->create_csv_dateien($langid);
	}

	/**
	 * @param int $langid
	 */
	private function create_csv_dateien($langid=1)
	{
		//Men�punktdaten rausholen
		$sql=sprintf("SELECT 	lang_id, 
								menuid_id, 
								lang_title, 	
								url_menuname, 
								menuname, 
								url_metadescrip, 
								url_metakeywords 
					FROM %s
					WHERE lang_id='%d'",
			$this->cms->tbname['papoo_menu_language'],
			$this->db->escape($langid)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$cols=$this->db->get_col_info();
		$csv_menu="";
		if (is_array($cols)) {
			foreach ($cols as $key=>$value) {
				$csv_menu.=$value."\t";
			}
		}
		$csv_menu.="\n";

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						$value2=str_replace("\n","",$value2);
						$value2=str_replace("\r","",$value2);
						$csv_menu.=strip_tags($value2)."\t";
					}
				}
				$csv_menu.="\n";
			}
		}

		//Artikeldaten rausholen
		$sql=sprintf("SELECT 	lang_id, 
								lan_repore_id AS artikel_id, 
								header AS Ueberschrift, 	
								lan_teaser AS Teaser_Anreisser, 
								lan_article_sans AS Artikel_inhalt, 
								lan_metatitel AS Titel_meta, 
								lan_metadescrip AS Metabeschreibung,
								lan_metakey AS Meta_stichwoerter,
								url_header AS url
					FROM %s
					WHERE lang_id='%d'",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape($langid)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$cols=$this->db->get_col_info();
		$csv_artikel="";
		if (is_array($cols)) {
			foreach ($cols as $key=>$value) {
				$csv_artikel.=($value)."\t";
			}
		}
		$csv_artikel.="\n";

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						$value2=str_replace("\n","",$value2);
						$value2=str_replace("\r","",$value2);
						$csv_artikel.=strip_tags($value2)."\t";
					}
				}
				$csv_artikel.="\n";
			}
		}

		$filename="export_csv_menue".time().".csv";
		$this->diverse->write_to_file("/interna/templates_c/".$filename,$csv_menu);
		$this->content->template['export_csv_menu_file']=$filename;

		$filename="export_csv_artikel".time().".csv";
		$this->diverse->write_to_file("/interna/templates_c/".$filename,$csv_artikel);
		$this->content->template['export_csv_artikel_file']=$filename;
	}
	/**
	 * Import der SQL Datei starten
	 */
	function start_import_now()
	{
		//DateiName
		$tmp_file = PAPOO_ABS_PFAD . "/interna/templates_c/dumpnrestore.sql";
		//Resore Datei laden
		$this->dumpnrestore->restore_load($tmp_file);

		$neu_lang="lang_id='".$this->db->escape($this->checked->lang_select_id_into)."'";

		//Alte langid setzen
		$alt_lang="lang_id='".$this->db->escape($this->checked->lang_select_id_ref)."'";

		$i = 0;
		//Durchgehen
		foreach ($this->dumpnrestore->querys as $query) {
			//Alte Sprachid mit der neuen ersezten
			$query = str_ireplace($alt_lang, $neu_lang, $query);
			//In das Array einlesen
			$this->dumpnrestore->querys[$i] = $query;
			$i++;
		}

		//Neue langid setzen
		$neu_lang="plugin_cform_lang_lang='".$this->db->escape($this->checked->lang_select_id_into)."'";

		//Alte langid setzen
		$alt_lang="plugin_cform_lang_lang='".$this->db->escape($this->checked->lang_select_id_ref)."'";

		$i = 0;
		//Durchgehen
		foreach ($this->dumpnrestore->querys as $query) {
			//Alte Sprachid mit der neuen ersezten
			$query = str_ireplace($alt_lang, $neu_lang, $query);
			//In das Array einlesen
			$this->dumpnrestore->querys[$i] = $query;
			$i++;
		}

		//Neue langid setzen
		$neu_lang="plugin_cform_group_lang_lang='".$this->db->escape($this->checked->lang_select_id_into)."'";

		//Alte langid setzen
		$alt_lang="plugin_cform_group_lang_lang='".$this->db->escape($this->checked->lang_select_id_ref)."'";
		/** */
		$i=0;

		//Durchgehen
		foreach ($this->dumpnrestore->querys as $query) {
			//Alte Sprachid mit der neuen ersezten
			$query=str_ireplace($alt_lang,$neu_lang,$query);
			//In das Array einlesen
			$this->dumpnrestore->querys[$i]=$query;
			$i++;
		}

		//REstore zur�ckspielen
		$this->dumpnrestore->restore_save();
	}
	/**
	 * Sprachdaten umkopieren
	 */
	function sprach_kop()
	{
		//Dateien erzeugen
		$this->start_export_now($this->checked->lang_select_id_ref);

		//Datein einlesen
		$this->start_import_now();
	}

	/**
	 * definiert die Liste der zu exportierenden Tabellen
	 */
	function init_export_tables()
	{
		/**
		//Plugins
		$this->export_tables[] = array("papoo_plugin_cform_group_lang", "plugin_cform_group_lang_lang");
		$this->export_tables[] = array("papoo_plugin_cform_lang", "plugin_cform_lang_lang");
		$this->export_tables[] = array("papoo_plugin_language", "pluginlang_lang_id");

		$this->export_tables[] = array("papoo_plugin_cform_group_lang", "plugin_cform_group_lang_lang");
		$this->export_tables[] = array("papoo_plugin_cform_lang", "plugin_cform_lang_lang");
		 */
		// Tabellen Papoo-Kern
		$this->export_tables[] = array("papoo_language_stamm", "lang_id");
		$this->export_tables[] = array("papoo_menu_language", "lang_id");
		$this->export_tables[] = array("papoo_language_article", "lang_id");
		#$this->export_tables[] = array("papoo_version_language_article", "lang_id");
		$this->export_tables[] = array("papoo_language_collum3", "lang_id");

		$this->export_tables[] = array("papoo_language_image", "lang_id");
		$this->export_tables[] = array("papoo_language_download", "lang_id");
		$this->export_tables[] = array("papoo_language_video", "video_lang_id");

		/**

		//Flex
		$this->export_tables[] = array("papoo_mv_content_1_lang_1", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_2", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_3", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_4", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_5", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_6", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_7", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_8", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_9", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_10", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_11", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_12", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_13", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_14", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_15", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_16", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_17", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_18", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_19", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_20", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_21", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_22", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_23", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_24", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_25", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_26", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_27", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_28", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_29", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_30", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_31", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_32", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_33", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_34", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_35", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_36", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_37", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_38", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_39", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_40", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_41", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_42", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_43", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_44", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_45", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_46", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_47", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_48", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_49", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_50", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_51", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_52", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_53", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_54", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_55", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_56", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_57", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_58", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_59", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_60", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_61", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_62", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_63", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_64", "lang_id");
		$this->export_tables[] = array("papoo_mv_content_1_lang_65", "lang_id");
		 */
		/** */

		if ($this->onlineon) {
			// Tabellen der onlineon-Plugins
			$this->export_tables[] = array("onlineon_learningcards_group_language", "o_lcglang_lang_id");
			$this->export_tables[] = array("onlineon_learningcards_language", "o_lclang_lang_id");

			$this->export_tables[] = array("onlineon_myportfolio_test_language", "o_mptlang_lang_id");
			$this->export_tables[] = array("onlineon_myportfolio_question_language", "o_mpqlang_lang_id");
		}
	}

	/**
	 * @param int $lang_id
	 */
	function lang_export_do ($lang_id = 0)
	{
		if ($lang_id) {
			// Test-Daten schreiben...
			foreach ($this->export_tables as $table) {
				// 1. bestehende Querys l�schen
				$this->dumpnrestore->querys = array();

				// 2. DELETE-Query erstellen
				$temp_query = sprintf("\n\nDELETE FROM `%s` WHERE %s='%d'",
					$this->dumpnrestore->global_db_praefix.$table[0],
					$table[1],
					$lang_id
				);
				$this->dumpnrestore->querys[] = $temp_query.$this->dumpnrestore->eofline_identifier."\n";

				// 3. Daten der Tabelle lesen
				$sql = sprintf("SELECT * FROM %s WHERE %s='%d'",
					$this->db_praefix.$table[0],
					$table[1],
					$lang_id
				);
				$temp_result = $this->db->get_results($sql, ARRAY_A);

				// 4. INSERT-Querys erstellen
				if (!empty($temp_result)) {
					foreach ($temp_result as $row) {
						$temp_query = "INSERT INTO `".$this->dumpnrestore->global_db_praefix.$table[0]."` SET ";

						foreach($row as $feldname => $feldwert) {
							$temp_query .= $feldname . "='" . $this->db->escape(($feldwert)) . "', ";
						}
						$temp_query[strlen($temp_query) - 2] = " ";

						$this->dumpnrestore->querys[] = $temp_query.$this->dumpnrestore->eofline_identifier."\n";
					}
				}

				// 5. Querys in Datei schreiben
				$this->dumpnrestore->dump_save("append");
			}
		}
	}
}

$lang_export = new lang_export_class();
