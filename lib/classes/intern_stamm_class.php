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
 * Class intern_stamm_class
 */
class intern_stamm_class {

	/** @var int ID der aktuellen Sprache */
	var $lang_aktu_id;

	/**
	 * intern_stamm_class constructor.
	 */
	function __construct()
	{
		// Hier die Klassen als Referenzen
		global $cms;
		$this->cms = &$cms;

		global $db;
		$this->db = &$db;

		global $content;
		$this->content = &$content;

		global $message;
		$this->message = &$message;

		global $user;
		$this->user = &$user;

		global $weiter;
		$this->weiter = &$weiter;

		global $searcher;
		$this->searcher = &$searcher;

		global $checked;
		$this->checked = &$checked;

		global $html;
		$this->html = &$html;

		global $intern_artikel;
		$this->intern_artikel = &$intern_artikel;

		global $replace;
		$this->replace = &$replace;

		global $db_abs;
		$this->db_abs = &$db_abs;

		/*
		global $bbcode;
		$this->bbcode = &$bbcode;
		*/

		global $dumpnrestore;
		$this->dumpnrestore = &$dumpnrestore;

		global $diverse;
		$this->diverse = &$diverse;

		global $menu;
		$this->menu = &$menu;

		IfNotSetNull($this->content->template['sysinfo']);
		IfNotSetNull($this->content->template['info']);
		IfNotSetNull($this->content->template['text']);
		IfNotSetNull($this->content->template['logindaten']);
		IfNotSetNull($this->content->template['textx']);
		IfNotSetNull($this->content->template['is_dev']);
		IfNotSetNull($this->content->template['case1']);
		IfNotSetNull($this->content->template['case2']);
		IfNotSetNull($this->content->template['weiter']);
		IfNotSetNull($this->content->template['upload_error']);
		IfNotSetNull($this->content->template['casedump']);
		IfNotSetNull($this->content->template['template_weiche']);
		IfNotSetNull($this->content->template['case0']);
		IfNotSetNull($this->content->template['case1b']);
		IfNotSetNull($this->content->template['case2b']);
		IfNotSetNull($this->content->template['loeschecht']);
		IfNotSetNull($this->content->template['loesch']);
		IfNotSetNull($this->content->template['xsuch']);
		IfNotSetNull($this->content->template['redirect']);
		IfNotSetNull($this->content->template['is_uploaded']);
		//IfNotSetNull($this->content->template['menuinput_data']['xinput']);
		if (empty($this->content->template['alerts'])) {
			$this->content->template['alerts'] = [];
		}
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		// Restore ohne Zugriff-Check
		if (!empty($this->checked->restore) or !empty($this->checked->backdump)){
			$this->make_restore();
		}
		else {
			$this->user->check_access();
		}
		// HACK für Rückumwandlung von <br>s im TinyMCE
		if (empty($this->checked->editor_name)) {
			$this->checked->editor_name = "";
		}
		if (empty($this->checked->inhalt_ar['inhalt'])) {
			$this->checked->inhalt_ar['inhalt'] = "";
		}
		if ($this->checked->editor_name == "tinymce") {
			$this->checked->inhalt_ar['inhalt'] = $this->diverse->recode_entity_n_br($this->checked->inhalt_ar['inhalt'], "nobr");
		}
		if (empty ($this->checked->makedump)) {
			if (($this->checked->menuid != "53")) {

				$this->diverse->remove_files(PAPOO_ABS_PFAD . "/interna/templates_c/", "sql");
			}
		}

		switch ($this->checked->menuid) {
		case "18" :
			// DIe reinen Stammdaten der Seite
			$this->make_stamm();
			break;

		case "49" :
			$this->make_div_lang();
			// DIe reinen Metadaten der Seite
			$this->make_meta();
			break;

		case "48" :
			$this->make_div_lang();
			// Text für Kontakt-Formular
			$this->make_contact_text();
			break;

		case "50" :
			// Rechteverwaltung im Intranet
			$this->make_rights();
			break;

		case "53" :
			$this->make_dump_intern();
			break;

		case "61" :
			$this->show_info();
			break;

		case "64" :
			$this->show_sys_info();
			break;

		case "65" :
			$this->make_update();
			break;
		}
	}

	/**
	 * Update durchführen
	 */
	function make_update()
	{
		// interne Update Klasse einbinden

		require_once(PAPOO_ABS_PFAD . "/lib/classes/update_class.php");
		$update = new update_class();
		$update->make_update();
	}

	/**
	 * @param $bytes
	 * @return string
	 */
	private function format_bytes($bytes)
	{
		$unit = 'B';
		if ($bytes > 1.1*1024*1024*1024*1024) {
			$unit = 'TiB';
			$bytes /= 1024*1024*1024*1024;
		}
		elseif ($bytes > 1.1*1024*1024*1024) {
			$unit = 'GiB';
			$bytes /= 1024*1024*1024;
		}
		elseif ($bytes > 1.1*1024*1024) {
			$unit = 'MiB';
			$bytes /= 1024*1024;
		}
		elseif ($bytes > 1.1*1024) {
			$unit = 'KiB';
			$bytes /= 1024;
		}
		else {
			return $bytes.'&thinsp;B';
		}
		return number_format($bytes, 1, ',', '.').'&thinsp;'.$unit;
	}

	/**
	 * System Info
	 */
	public function show_sys_info()
	{
		// Speicherbelegung holen
		$session_path = @session_save_path();
		if ($session_path) {
			$total_space = @disk_total_space($session_path);
			$free_space = @disk_free_space($session_path);
			if ($total_space  !== false and $free_space !== false) {
				$this->content->template['sess_total_space'] = (int)($total_space / 1024);
				$this->content->template['sess_used_space'] = (int)(($total_space - $free_space) / 1024);
				$this->content->template['sess_high_space'] = (int)(($total_space - 16*1024*1024) / 1024);

				$this->content->template['sess_free_space_f'] = $this->format_bytes($free_space);
				$this->content->template['sess_total_space_f'] = $this->format_bytes($total_space);
			}
		}
		$total_space = @disk_total_space(PAPOO_ABS_PFAD);
		$free_space = @disk_free_space(PAPOO_ABS_PFAD);
		if ($total_space  !== false and $free_space !== false) {
			$this->content->template['files_total_space'] = (int)($total_space / 1024);
			$this->content->template['files_used_space'] = (int)(($total_space-$free_space) / 1024);
			$this->content->template['files_high_space'] = (int)(($total_space * 0.80) / 1024);

			$this->content->template['files_free_space_f'] = $this->format_bytes($free_space);
			$this->content->template['files_total_space_f'] = $this->format_bytes($total_space);
		}

		// PHP-Infos holen
		ob_start();
		phpinfo();
		$phpinfo = ob_get_contents();
		ob_end_clean();
		$inf = explode("<body>", $phpinfo);
		$inf2 = explode("</body>", $inf['1']);

		$fertig = str_ireplace('width="600"', 'width="500px" style="overflow:hidden;border:1px solid black;"', $inf2['0']);

		$this->content->template['sysinfo'] = "nobr:" . $fertig;
	}

	/**
	 * Informationen zeigen
	 */
	function show_info()
	{
		$this->content->template['info'] = 1;
		global $version;
		$this->content->template['version'] = $version;
	}

	/**
	 *
	 */
	function make_stamm()
	{
		IfNotSetNull($this->checked->cache_yn);
		IfNotSetNull($this->checked->off_yn);
		IfNotSetNull($this->checked->editorpost);
		IfNotSetNull($this->checked->news);
		IfNotSetNull($this->checked->switcher);
		IfNotSetNull($this->checked->spalte);
		IfNotSetNull($this->checked->einloggen);
		IfNotSetNull($this->checked->spalte_intra);
		IfNotSetNull($this->checked->einloggen_intra);
		IfNotSetNull($this->checked->switcher_intra);
		IfNotSetNull($this->checked->artikel_yn);
		IfNotSetNull($this->checked->artikel_lang_yn);
		IfNotSetNull($this->checked->comment_yn);
		IfNotSetNull($this->checked->suchbox);
		IfNotSetNull($this->checked->suchbox_intra);
		IfNotSetNull($this->checked->lang_backend);
		IfNotSetNull($this->checked->usability);
		IfNotSetNull($this->checked->stamm_cat_startseite_ok);
		IfNotSetNull($this->checked->stamm_listen_yn);
		IfNotSetNull($this->checked->stamm_benach_artikelfreigabe_email);
		IfNotSetNull($this->checked->show_menu_all);
		IfNotSetNull($this->checked->categories);
		IfNotSetNull($this->checked->stamm_cat_sub_ok);

		if (empty($_POST['submitecht']) or empty($this->checked->submitecht)) {
			$this->checked->submitecht = "";
		}
		if (!empty($_POST['submitecht']) and $this->checked->submitecht != "") {
			// Cache Daten in ein File schreiben
			$file = "/interna/templates_c/cache.txt";
			if ($this->checked->cache_yn == 1) {
				$zeile = "ok;";
			}
			else {
				$zeile = ";";
			}

			if ($this->checked->cache_life >= 1) {
				$zeile .= $this->checked->cache_life . ";" . time();
				$zeile .=  ";" . time();
			}
			$this->diverse->write_to_file($file, $zeile);
			// Wartungs Daten in ein File schreiben
			if ($this->checked->off_yn == 1) {
				$zeile = "ok;";
			}
			else {
				$zeile = ";";
			}
			$file = "/interna/templates_c/wartung.txt";
			$this->diverse->write_to_file($file, $zeile);
			//Wartungshtml eintragen
			file_put_contents(rtrim(PAPOO_ABS_PFAD, "/")."/interna/templates_c/wartung.html", $this->checked->wartungstext);

			if ($this->checked->modrm == "rewriteurl") {
				$rewrite = 3;
			}
			else {
				if ($this->checked->modrm == "rewrite") {
					$rewrite = 2;
				}
				else {
					$rewrite = 1;
				}
			}

			if ($this->checked->modrm == "mime") {
				$mime = 2;
			}
			else {
				$mime = 1;
			}

			if ($this->checked->modrm=="free") {
				$mod_free=1;
			}
			else {
				$mod_free=0;
			}

			$this->checked->stamm_surls_trenner=$this->urlencode($this->checked->stamm_surls_trenner);
			$this->checked->stamm_surls_trenner=str_ireplace("-","",$this->checked->stamm_surls_trenner);
			// Forum Board umschalten
			if ($this->checked->forum_board == "on") {
				$this->checked->forum_board = 1;
			}
			// News ja/nein umschalten
			if ($this->checked->news == "on") {
				$this->checked->news = 1;
			}

			if (empty($this->checked->config_404_benutzen_check)) {
				$this->checked->config_404_benutzen_check=0;
			}

			if (empty($this->checked->config_jquery_aktivieren_label)) {
				$this->checked->config_jquery_aktivieren_label=0;
			}

			if (empty($this->checked->config_jquery_colorbox_aktivieren)) {
				$this->checked->config_jquery_colorbox_aktivieren=0;
			}

			if (empty($this->checked->config_basis_js_funktionen_frontend)) {
				$this->checked->config_basis_js_funktionen_frontend=0;
			}

			if (empty($this->checked->config_html_tidy_funktionaktivieren)) {
				$this->checked->config_html_tidy_funktionaktivieren=0;
			}

			if (function_exists('tidy_parse_string')) {
				$this->content->template['tidy_is_vorhanden'] = "ok";
			}
			else {
				$this->checked->config_html_tidy_funktionaktivieren = 0;
				$this->content->template['tidy_is_vorhanden'] = NULL;
			}


			if (!empty($this->checked->config_uplaod_hide_praefix)) {
				$this->checked->config_uplaod_hide_praefix = 1;
			}
			else {
				$this->checked->config_uplaod_hide_praefix = 0;
			}

			if (!empty($this->checked->config_tiny_advimg_filelist)) {
				$this->checked->config_tiny_advimg_filelist = 1;
			}
			else {
				$this->checked->config_tiny_advimg_filelist = 0;
			}

			if (!empty($this->checked->smtp_active)) {
				$this->checked->smtp_active = 1;
			}
			else {
				$this->checked->smtp_active = 0;
			}

			$xsql['dbname'] = "papoo_config";
			$xsql['praefix'] = "config";
			$xsql['where_name'] = "config_id";
			$this->checked->config_id = 1;
			#$xsql['must'] = array("produkte_lang_internername");
			$this->db_abs->update($xsql);

			//Metagedöhns eintragen
			$sql = sprintf("UPDATE %s SET 
							head_title='%s',
							beschreibung='%s',
							stichwort='%s',
							seitentitle='%s' 
							WHERE lang_id='%d'",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->checked->head_title),
				$this->db->escape($this->checked->beschreibung),
				$this->db->escape($this->checked->stichwort),
				$this->db->escape($this->checked->seitentitle),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->db->query($sql);

			// Ende speziell root Auswahl
			// Daten wurden überprüft und können eingetragen werden... ###

			$sql = sprintf("UPDATE %s
							SET style_switcher_internet='%s', 
							seitenname='%s', 
							rechte_spalte_internet='%s', 
							einloggen_internet='%s',
							mod_rewrite='%d', 
							mod_mime='%d',
							mod_free='%d',
							lang_dateformat='%d',
							admin_email='%s', 
							rechte_spalte='%s', 
							einloggen='%s', 
							style_switcher='%s', 
							autor_seite='%s',
							artikel_yn='%d', 
							artikel_lang_yn='%d', 
							comment_yn='%s', 
							editor='%s', 
							lang_frontend='%s', 
							cache_yn='%s',
							forum_board='%s', 
							forum_letzte_modus='%d', 
							spamschutz_modus='%d', 
							stamm_kontakt_spamschutz='%d',
							suchbox='%s', 
							suchbox_intra='%s', 
							lang_backend='%s',
							anzeig_besucher='%s', 
							anzeig_autor='%s',
							anzeig_filter='%s',
							anzeig_pageviews='%s', 
							anzeig_versende='%s', 
							anzeig_drucken='%s',
							benach_email='%s', 
							benach_neueruser='%s', 
							benach_neu_gaestebuch='%s', 
							benach_neu_kommentar='%s', 
							benach_neu_forum='%s',
							usability='%s',
							stamm_cat_startseite_ok='%s',
							stamm_surls_trenner='%s', 
							stamm_artikel_rechte_jeder='%s',
							stamm_veroffen_yn='%s', 
							stamm_listen_yn='%s', 
							do_replace='%d', 
							stamm_benach_artikelfreigabe_email='%d',
							gaestebuch_msg_frei='%d', 
							showpapoo='%d', 
							show_menu_all='%d',
							stamm_artikel_rechte_chef='%s', 
							stamm_menu_rechte_jeder='%s', 
							categories='%s', 
							stamm_cat_sub_ok='%s', 
							stamm_artikel_order='%s', 
							off_yn='%s', 
							wartungstext='%s',
							stamm_documents_change_backup='%d',
							smtp_active='%d',
							smtp_host='%s',
							smtp_port='%s',
							smtp_user='%s',
							smtp_pass='%s',
							captcha_sitekey='%s',
							captcha_secret='%s'
							",

				$this->cms->papoo_daten,

				$this->db->escape($this->checked->switcher),
				$this->db->escape($this->checked->seitenname),
				$this->db->escape($this->checked->spalte),
				$this->db->escape($this->checked->einloggen),

				$this->db->escape($rewrite),
				$this->db->escape($mime),
				$this->db->escape($mod_free),
				$this->db->escape($this->checked->lang_dateformat),

				$this->db->escape($this->checked->email),
				// $this->db->escape($this->checked->intranet_yn),
				$this->db->escape($this->checked->spalte_intra),
				$this->db->escape($this->checked->einloggen_intra),
				$this->db->escape($this->checked->switcher_intra),
				$this->db->escape($this->checked->autor_seite),

				$this->db->escape($this->checked->artikel_yn),
				$this->db->escape($this->checked->artikel_lang_yn),
				$this->db->escape($this->checked->comment_yn),
				$this->db->escape($this->checked->editorpost),
				$this->db->escape($this->checked->lang_frontend),
				$this->db->escape($this->checked->cache_yn),

				$this->db->escape($this->checked->forum_board),
				$this->db->escape($this->checked->forum_letzte_modus),
				$this->db->escape($this->checked->spamschutz_modus),
				$this->db->escape($this->checked->kontakt_spamschutz),

				$this->db->escape($this->checked->suchbox),
				$this->db->escape($this->checked->suchbox_intra),
				$this->db->escape($this->checked->lang_backend),

				$this->db->escape($this->checked->anzeig_besucher),
				$this->db->escape($this->checked->anzeig_autor),
				$this->db->escape($this->checked->anzeig_filter),
				$this->db->escape($this->checked->anzeig_pageviews),
				$this->db->escape($this->checked->anzeig_versende),
				$this->db->escape($this->checked->anzeig_drucken),

				$this->db->escape($this->checked->benach_email),
				$this->db->escape($this->checked->benach_neueruser),
				$this->db->escape($this->checked->benach_neu_gaestebuch),
				$this->db->escape($this->checked->benach_neu_kommentar),
				$this->db->escape($this->checked->benach_neu_forum),

				$this->db->escape($this->checked->usability),
				$this->db->escape($this->checked->stamm_cat_startseite_ok),
				$this->db->escape($this->checked->stamm_surls_trenner),
				$this->db->escape($this->checked->stamm_artikel_rechte_jeder),

				$this->db->escape($this->checked->stamm_veroffen_yn),
				$this->db->escape($this->checked->stamm_listen_yn),
				$this->db->escape($this->checked->do_replace),
				$this->db->escape($this->checked->stamm_benach_artikelfreigabe_email),

				$this->db->escape($this->checked->gaestebuch_msg_frei),
				$this->db->escape($this->checked->showpapoo),
				$this->db->escape($this->checked->show_menu_all),

				$this->db->escape($this->checked->stamm_artikel_rechte_chef),
				$this->db->escape($this->checked->stamm_menu_rechte_jeder),
				$this->db->escape($this->checked->categories),
				$this->db->escape($this->checked->stamm_cat_sub_ok),
				$this->db->escape($this->checked->stamm_artikel_order),
				$this->db->escape($this->checked->off_yn),
				$this->db->escape($this->checked->wartungstext),

				$this->db->escape($this->checked->stamm_documents_change_backup),

				$this->db->escape($this->checked->smtp_active),
				$this->db->escape($this->checked->smtp_host),
				$this->db->escape($this->checked->smtp_port),
				$this->db->escape($this->checked->smtp_user),
				$this->db->escape($this->checked->smtp_pass),

				$this->db->escape($this->checked->captcha_key),
				$this->db->escape($this->checked->captcha_secret)
			);
			$result = $this->db->query($sql);

			// Twitter Nutzernamen in die Datenbank eintragen und Regeln beachten
			// https://help.twitter.com/en/managing-your-account/twitter-username-rules
			$twitterHandle = substr(preg_replace('~[^a-z0-9_]~i', "", $this->checked->twitter_handle), 0, 15);
			if (strlen($twitterHandle) > 0) {
				$twitterHandle = "@$twitterHandle";
			}
			$this->db->query("UPDATE {$this->cms->tbname["papoo_daten"]} SET twitter_handle = '{$this->db->escape($twitterHandle)}'");

			// Sprachen updaten
			$sql = "UPDATE " . $this->cms->papoo_name_language . " SET more_lang=1";
			$this->db->query($sql);
			if (empty ($this->checked->lang)) {
				$this->checked->lang['1']=1;
			}

			if (!empty ($this->checked->lang)) {
				foreach ($this->checked->lang as $langid) {
					$sql = "UPDATE " . $this->cms->papoo_name_language . " SET more_lang = 2 WHERE lang_id = " . $this->db->escape($langid) . "";
					$this->db->query($sql);
				}
			}
			$_SESSION['dbp']['papoo_daten_lang']="";
			$_SESSION['dbp']['papoo_daten']="";
			$_SESSION['dbp']['papoo_daten2']="";

			if ($result !== false) {
				$this->content->template['content_text'] = $this->content->template['message_21'];
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=18&messageget=21";
			}
			else {
				$this->content->template['content_text'] = $this->content->template['message_20'];
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=18&messageget=20";
			}

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . htmlspecialchars($location_url) . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit ();
		}
		// ENDE ---- Daten wurden Überprüft und können eingetragen werden... ###
		// Daten zur Änderung anzeigen ... ###
		else {
			$sql=sprintf("SELECT * FROM %s", $this->cms->tbname['papoo_config']);
			$result_config=$this->db->get_results($sql,ARRAY_A);

			$result_config[0]['config_metatagszusatz'] = "nobr:".$result_config[0]['config_metatagszusatz'];
			$this->content->template['config']=$result_config;

			$sql = sprintf("SELECT * FROM %s WHERE lang_id='%d' LIMIT 1",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$result = $this->db->get_results($sql,ARRAY_A);
			// läuft nur einmal
			$result[0]['head_title'] = "nobr:".$result[0]['head_title'];
			$result[0]['beschreibung'] = "nobr:".$result[0]['beschreibung'];
			$result[0]['stichwort'] = "nobr:".$result[0]['stichwort'];

			$this->content->template['meta_texte_standard'] = $result;
			if (empty ($this->checked->messageget)) {
				$this->checked->messageget = "";
			}
			// Es wurden Änderungen vorgenommen,  anzeigen ...
			if ($this->checked->messageget == "21") {
				$this->content->template['alerts'][] = ['type'=>'message', 'content'=>$this->content->template['message_21']];
			}
			elseif ($this->checked->messageget) {
				$this->content->template['alerts'][] = ['type'=>'error', 'content'=>$this->content->template['message_20']];
			}
			// ENDE ---  Es wurden Änderungen vorgenommen, zur Überprüfung anzeigen ... ###
			// Daten aus der Datenbank holen und anzeigen ... ###
			// Editor zuweisen
			$this->intern_artikel->check_editor();
			$menuinput_data = $this->content->template['language'] = array ();
			$result = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_daten . " LIMIT 2");

			// läuft nur einmal
			foreach ($result as $row) {
				// daher hier auch eine weitere Abfrage
				$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . "  ");
				// zuweisen welche Sprache ausgewählt sind
				foreach ($resultlang as $rowlang) {
					// chcken wenn Sprache gewählt
					if ($rowlang->more_lang == 2) {
						$selected_more = 'nodecode:checked="checked"';
					}
					else {
						$selected_more = "";
					}
					// Überspringen, wenn Sprache als Standard ausgewählt ist
					// if ($row->lang_frontend != $rowlang->lang_short)
					array_push($this->content->template['language'],
						array ('language' => $rowlang->lang_long,
							'lang_id' => $rowlang->lang_id,
							'lang_short' => $rowlang->lang_short,
							'selected' => $selected_more,
						));

				}
				//preload_wartung
				if (empty($row->wartungstext)) {
					$wartungstext = 'nodecode:'.$this->preload_wartung();
				}
				else {
					$wartungstext = 'nodecode:' . $row->wartungstext;
				}

				if ($row->rechte_spalte_internet == 1) {
					$checkedspalte = 'nodecode:checked="checked"';
				}
				else {
					$checkedspalte = "";
				}

				if ($row->einloggen_internet == 1) {
					$checkedlogg = 'nodecode:checked="checked"';
				}
				else {
					$checkedlogg = "";
				}

				if ($row->style_switcher_internet == 1) {
					$checkedswitch = 'nodecode:checked="checked"';
				}
				else {
					$checkedswitch = "";
				}

				if ($row->usability == 1) {
					$checkedusability = 'nodecode:checked="checked"';
				}

				if ($row->einloggen == 1) {
					$checkedlogg_intra = 'nodecode:checked="checked"';
				}

				if ($row->style_switcher == 1) {
					$checkedswitch_intra = 'nodecode:checked="checked"';
				}
				else {
					$checkedswitch_intra = "";
				}

				// Editor der verwendet werden soll...
				if ($row->editor == 1) {
					$checkededitor_default = 'nodecode:checked="checked"';
				}
				else {
					$checkededitor_default = "";
				}

				if ($row->editor == 2) {
					$checkededitor_bbcode = 'nodecode:checked="checked"';
				}
				else {
					$checkededitor_bbcode = "";
				}

				if ($row->editor == 4) {
					$checkededitor_screen = 'nodecode:checked="checked"';
				}
				else {
					$checkededitor_screen = "";
				}

				if ($row->editor == 99) {
					$checkededitor_fremd = 'nodecode:checked="checked"';
				}
				else {
					$checkededitor_fremd = "";
				}

				if ($row->categories == 1) {
					$checkedcategories = 'nodecode:checked="checked"';
				}
				else {
					$checkedcategories = '';
				}

				if ($row->artikel_yn == 1) {
					$checkedartikel_yn = 'nodecode:checked="checked"';
				}
				else {
					$checkedartikel_yn = "";
				}

				if ($row->artikel_lang_yn == 1) {
					$checkedartikel_lang_yn = 'nodecode:checked="checked"';
				}
				else {
					$checkedartikel_lang_yn = '';
				}

				if ($row->comment_yn == 1) {
					$checkedcomment_yn = 'nodecode:checked="checked"';
				}
				else {
					$checkedcomment_yn = "";
				}

				if ($row->anzeig_besucher == 1) {
					$checkedanzeig_besucher = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_besucher = "";
				}

				if ($row->anzeig_autor == 1) {
					$checkedanzeig_autor = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_autor = "";
				}

				if ($row->anzeig_filter == 1) {
					$checkedanzeig_filter = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_filter = "";
				}

				if ($row->anzeig_pageviews == 1) {
					$checkedanzeig_pageviews = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_pageviews = "";
				}

				if ($row->anzeig_versende == 1) {
					$checkedanzeig_versende = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_versende = "";
				}

				if ($row->anzeig_drucken == 1) {
					$checkedanzeig_drucken = 'nodecode:checked="checked"';
				}
				else {
					$checkedanzeig_drucken = "";
				}

				if ($row->benach_neueruser == 1) {
					$checkedbenach_neueruser = 'nodecode:checked="checked"';
				}
				else {
					$checkedbenach_neueruser = "";
				}

				// stamm_artikel_rechte_chef
				if ($row->stamm_artikel_rechte_chef == 1) {
					$checkedstamm_artikel_rechte_chef = 'nodecode:checked="checked"';
				}
				else {
					$checkedstamm_artikel_rechte_chef = "";
				}

				if ($row->stamm_artikel_rechte_jeder == 1) {
					$checkedstamm_artikel_rechte_jeder = 'nodecode:checked="checked"';
				}
				else {
					$checkedstamm_artikel_rechte_jeder = "";
				}

				if ($row->stamm_veroffen_yn == 1) {
					$checkedstamm_veroffen_yn = 'nodecode:checked="checked"';
				}
				else {
					$checkedstamm_veroffen_yn = "";
				}

				if ($row->stamm_listen_yn == 1) {
					$checkedstamm_listen_yn = 'nodecode:checked="checked"';
				}
				else {
					$checkedstamm_listen_yn = "";
				}

				if ($row->stamm_menu_rechte_jeder == 1) {
					$checkedstamm_menu_rechte_jeder = 'nodecode:checked="checked"';
				}
				else {
					$checkedstamm_menu_rechte_jeder = "";
				}

				if ($row->benach_neu_gaestebuch == 1) {
					$checkedbenach_neu_gaestebuch = 'nodecode:checked="checked"';
				}
				else {
					$checkedbenach_neu_gaestebuch = "";
				}

				if ($row->benach_neu_kommentar == 1) {
					$checkedbenach_neu_kommentar = 'nodecode:checked="checked"';
				}
				else {
					$checkedbenach_neu_kommentar = "";
				}

				if ($row->benach_neu_forum == 1) {
					$checkedbenach_neu_forum = 'nodecode:checked="checked"';
				}
				else {
					$checkedbenach_neu_forum = "";
				}

				if ($row->mod_rewrite == 3) {
					$checked_mod_rewrite_url = 'nodecode:checked="checked"';
				}
				else {
					$checked_mod_rewrite_url = "";
				}

				if ($row->mod_rewrite == 2) {
					$checked_mod_rewrite = 'nodecode:checked="checked"';
				}
				else {
					$checked_mod_rewrite = "";
				}

				if ($row->mod_mime == 2) {
					$checked_mod_mime = 'nodecode:checked="checked"';
				}
				else {
					$checked_mod_mime = "";
				}

				if ($row->mod_free == 1) {
					$checked_mod_free = 'nodecode:checked="checked"';
					$checked_mod_mime = "";
					$checked_mod_rewrite_url = "";
					$checked_mod_rewrite = "";

				}
				else {
					$checked_mod_free = "";
				}

				if (empty ($checked_mod_rewrite) && empty ($checked_mod_mime) && empty ($checked_mod_rewrite_url) && empty ($checked_mod_free)) {
					$checked_mod_none = 'nodecode:checked="checked"';
				}
				else {
					$checked_mod_none = "";
				}
				// Sprachen für Front und Backend
				$checkedlang_back_de = "";
				if ($row->lang_backend == "de") {
					$checkedlang_back_de = "selected";
				}

				$lang_back_default = $row->lang_backend;

				if ($row->lang_backend == "en") {
					$checkedlang_back_en = "selected";
				}
				else {
					$checkedlang_back_en = "";
				}

				if ($row->lang_frontend == "de") {
					$checkedlang_front_de = "selected";
				}
				else {
					$checkedlang_front_de = "";
				}

				if ($row->lang_frontend == "en") {
					$checkedlang_front_en = "selected";
				}
				else {
					$checkedlang_front_en = "";
				}

				if ($row->suchbox == "1") {
					$checkedsuch = 'nodecode:checked="checked"';
				}
				else {
					$checkedsuch = "";
				}

				if ($row->suchbox_intra == "1") {
					$checkedsuch_intra = 'nodecode:checked="checked"';
				}
				else {
					$checkedsuch_intra = "";
				}
				// wenn das Cache Verzeichniss schreibbar ist, dann Häckchen anbieten
				if (file_exists("../cache/NICHT_loeschen.php") and is_writable("../cache/NICHT_loeschen.php")) {
					$this->content->template['writable'] = "yes";
					if ($row->cache_yn == "yes") {
						$checked_cache_yn = 'nodecode:checked="checked"';
					}
					else {
						$checked_cache_yn = "";
					}
				}
				else {
					$checked_cache_yn = "";
				}
				// Forum Ansicht
				if ($row->forum_board == 1) {
					$checked_forum_board = 'nodecode:checked="checked"';
				}
				else {
					$checked_forum_board = "";
				}
				// News in der r. Spalte
				if ($row->news == "1") {
					$news = 'nodecode:checked="checked"';
				}
				else {
					$news = "";
				}
				// Cache Daten auslesen
				$file = "/interna/templates_c/cache.txt";
				$daten = $this->diverse->open_file($file);
				$datar = explode(";", $daten);
				if ($datar['0'] == "ok") {
					$cache_yn = 'nodecode:checked="checked"';
				}

				if($row->smtp_active == "1") {
					$smtp = 'nodecode:checked="checked"';
				}
				else {
					$smtp = '';
				}

				IfNotSetNull($checkedusability);
				IfNotSetNull($cache_yn);
				IfNotSetNull($checkedartikel_lang_n);
				IfNotSetNull($datar['1']);
				$cache_lifetime = $datar['1'];

				array_push($menuinput_data, array (
					'seitenname' => $row->seitenname,
					'email' => $row->admin_email,
					'switcher' => $row->style_switcher,
					'autor_seite' => $row->autor_seite,
					'cache_yn' => $cache_yn,
					'cache_life' => $cache_lifetime,
					'checkedspalte' => $checkedspalte,
					'checkedlogg' => $checkedlogg,
					'checked_mod_none' => $checked_mod_none,
					'checkedswitch' => $checkedswitch,
					// 'checkedspalte_intra' => $checkedspalte_intra,
					// 'checkedlogg_intra' => $checkedlogg_intra,
					'checked_mod_mime' => $checked_mod_mime,
					'checked_mod_free' => $checked_mod_free,
					'checked_mod_rewrite' => $checked_mod_rewrite,
					'checked_mod_rewrite_url' => $checked_mod_rewrite_url,
					'checkedswitch_intra' => $checkedswitch_intra,
					'lang_dateformat' => $row->lang_dateformat,
					// 'checkedintranet' => $checkedintranet,
					'checkededitor_default' => $checkededitor_default,
					'checkededitor_bbcode' => $checkededitor_bbcode,
					'checkededitor_screen' => $checkededitor_screen,
					'checkededitor_fremd' => $checkededitor_fremd,
					'checkedartikel_yn' => $checkedartikel_yn,
					'checkedartikel_lang_n' => $checkedartikel_lang_n,
					'checkedartikel_lang_yn' => $checkedartikel_lang_yn,
					'checkedcomment_yn' => $checkedcomment_yn,
					'checkedlang_back_de' => $checkedlang_back_de,
					'checkedlang_back_en' => $checkedlang_back_en,
					'lang_back_default' => $lang_back_default,
					'checkedlang_front_de' => $checkedlang_front_de,
					'checkedlang_front_en' => $checkedlang_front_en,
					'checked_cache_yn' => $checked_cache_yn,
					'checked_forum_board' => $checked_forum_board,
					'forum_letzte_modus' => $row->forum_letzte_modus,
					'gaestebuch_msg_frei' => $row->gaestebuch_msg_frei,
					'spamschutz_modus' => $row->spamschutz_modus,
					'checkedsuch' => $checkedsuch,
					'checkedsuch_intra' => $checkedsuch_intra,
					'seitentitle' => "nodecode:" . $this->diverse->encode_quote($row->seitentitel),
					'checkednews' => $news,
					'newsnr' => $row->newsnr,
					'showpapoo'=>$row->showpapoo,
					'stamm_cat_startseite_ok' => $row->stamm_cat_startseite_ok,
					'stamm_surls_trenner' => $row->stamm_surls_trenner,
					'benach_email' => $row->benach_email,
					'do_replace' => $row->do_replace,
					'stamm_benach_artikelfreigabe_email' => $row->stamm_benach_artikelfreigabe_email,
					'show_menu_all'=>$row->show_menu_all,
					'stamm_cat_sub_ok'=>$row->stamm_cat_sub_ok,
					'stamm_artikel_order'=>$row->stamm_artikel_order,
					'checkedanzeig_besucher' => $checkedanzeig_besucher,
					'checkedanzeig_autor' => $checkedanzeig_autor,
					'checkedanzeig_filter' => $checkedanzeig_filter,
					'checkedanzeig_pageviews' => $checkedanzeig_pageviews,
					'checkedanzeig_versende' => $checkedanzeig_versende,
					'checkedanzeig_drucken' => $checkedanzeig_drucken,

					'checkedbenach_neueruser' => $checkedbenach_neueruser,
					'checkedbenach_neu_gaestebuch' => $checkedbenach_neu_gaestebuch,
					'checkedbenach_neu_kommentar' => $checkedbenach_neu_kommentar,
					'checkedbenach_neu_forum' => $checkedbenach_neu_forum,
					'checkedusability' => $checkedusability,
					'checkedstamm_artikel_rechte_jeder' => $checkedstamm_artikel_rechte_jeder,
					'checkedstamm_artikel_rechte_chef' => $checkedstamm_artikel_rechte_chef,
					'checkedstamm_veroffen_yn' => $checkedstamm_veroffen_yn,
					'checkedstamm_listen_yn' => $checkedstamm_listen_yn,
					'checkedstamm_menu_rechte_jeder' => $checkedstamm_menu_rechte_jeder,
					'checkedcategories' => $checkedcategories,
					'off_yn' => $row->off_yn,
					'wartungstext' => $wartungstext,
					'stamm_documents_change_backup' => $row->stamm_documents_change_backup,
					'twitter_handle' => $row->twitter_handle,

					'smtp_active' => $smtp,
					'smtp_host' => $row->smtp_host,
					'smtp_port' => $row->smtp_port,
					'smtp_user' => $row->smtp_user,
					'smtp_pass' => $row->smtp_pass,

					'captcha_sitekey' => $row->captcha_sitekey,
					'captcha_secret' => $row->captcha_secret
				));
			}
			$this->content->template['menuinput_data'] = $menuinput_data;
			$this->content->template['case0'] = '1';
		}
	}

	/**
	 * Alle Metadaten und sonstige Daten die in verschiedene Sprachen übersetzt werden können
	 *
	 * @return void
	 */
	function make_meta()
	{
		if (empty ($this->checked->submitecht)) {
			$this->checked->submitecht = "";
		}
		if ($_POST and $this->checked->submitecht != "") {

			$inhalt = $this->checked->inhalt_ar['inhalt'];
			$artikel = $this->replace->do_replace($inhalt);

			/**
			 * Check ob der Eintrag schon vorhanden ist
			 */
			$sql = sprintf("SELECT stamm_id FROM %s WHERE lang_id='%d'",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->lang_aktu_id)
			);
			$resultc = $this->db->get_results($sql);
			// print_r($resultc);
			if (empty($resultc)) {
				$sql = sprintf("INSERT INTO %s SET lang_id='%d', stamm_id='2'",
					$this->cms->papoo_language_stamm,
					$this->db->escape($this->lang_aktu_id)
				);
				$this->db->query($sql);
			}
			// Daten wurden Überprüft und können eingetragen werden... ###
			$sql = sprintf("UPDATE %s SET start_text='%s', start_text_sans='%s' WHERE lang_id='%d'",
				$this->cms->papoo_language_stamm,
				$this->db->escape($artikel),
				$this->db->escape($inhalt),
				$this->db->escape($this->lang_aktu_id)
			);

			$this->db->query($sql);
			$this->content->template['content_text'] = $this->content->template['message_21'];

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=49&messageget=21&lang_idx=" . $this->lang_aktu_id;
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit ();
		}
		// ENDE ---- Daten wurden Überprüft und können eingetragen werden... ###
		// Daten zur Änderung anzeigen ... ###
		else {
			if (empty ($this->checked->messageget)) {
				$this->checked->messageget = "";
			}
			// Es wurden Änderungen vorgenommen,  anzeigen ...
			if ($this->checked->messageget != "") {
				$this->content->template['alerts'][] = ['type'=>'message', 'content'=>$this->content->template['message_21']];
			}
			// ENDE ---  Es wurden Änderungen vorgenommen, zur Überprüfung anzeigen ... ###
			// Daten aus der Datenbank holen und anzeigen ... ###
			// Editor zuweisen
			$this->intern_artikel->check_editor();
			$menuinput_data = array ();

			$sql = sprintf("SELECT * FROM %s WHERE lang_id='%d' LIMIT 1",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->lang_aktu_id)
			);
			$result = $this->db->get_results($sql);
			// läuft nur einmal
			if (!empty ($result)) {
				foreach ($result as $row) {
					$this->content->template['Beschreibung'] = "nodecode:" . $row->start_text_sans;
					array_push($menuinput_data, array ('beschreibung' => "nodecode:" . $row->beschreibung, 'Beschreibung' => "nodecode:" . $row->start_text_sans, 'seitentitle' => "nodecode:" . $this->diverse->encode_quote($row->seitentitle)));
				}
			}
			// Leer,dann leer zuweisen
			else {
				$this->content->template['Beschreibung'] = "";
				array_push($menuinput_data, array ('beschreibung' => "", 'stichwort' => "", 'top_title' => "", 'Beschreibung' => "",));
			}
			// Bilder zuweisen
			$this->intern_artikel->get_images($this->lang_aktu_id);
			// Downloads und Artikel zuweisen
			$this->intern_artikel->get_downloads($this->lang_aktu_id);
			// CSS-Klassen zuweisen (für QuickTag-Editor)
			$this->intern_artikel->get_css_klassen();
			// SPrachen zuweisen
			// $this->make_div_lang();
			$this->content->template['menuinput_data'] = $menuinput_data;
			$this->content->template['case1'] = '1';
		}
	}

	/**
	 * Text für Kontakt-Formular verwalten.
	 *
	 * @return void
	 */
	function make_contact_text()
	{
		if (!empty($this->checked->submitecht)) {
			$inhalt = $this->checked->inhalt_ar['inhalt'];

			if ($this->checked->editor_name == "tinymce") {
				// Rückumwandlung von <br>s im TinyMCE
				//$inhalt = $this->diverse->recode_entity_n_br($inhalt, "nobr");
			}
			$artikel = $this->replace->do_replace($inhalt);
			// Check ob Eintrag in Sprach-Datei schon vorhanden ist
			$sql = sprintf("SELECT stamm_id FROM %s WHERE lang_id='%d'",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->lang_aktu_id)
			);
			$resultc = $this->db->get_results($sql);
			// Wenn noch kein Eintrag vorhanden ist, einen anlegen.
			if (empty($resultc)) {
				$sql = sprintf("INSERT INTO %s SET lang_id='%d', stamm_id='2'",
					$this->cms->papoo_language_stamm,
					$this->db->escape($this->lang_aktu_id)
				);
				$this->db->query($sql);
			}
			// Texte des Kontakt-Formulars eintragen
			$sql = sprintf("UPDATE %s SET kontakt_text='%s', kontakt_text_sans='%s' WHERE lang_id='%d'",
				$this->cms->papoo_language_stamm,
				$this->db->escape($artikel),
				$this->db->escape($inhalt),
				$this->db->escape($this->lang_aktu_id)
			);
			$this->db->query($sql);
			$this->content->template['content_text'] = $this->content->template['message_21'];

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=48&messageget=21&lang_idx=" . $this->lang_aktu_id;
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit ();
		}
		// ENDE ---- Daten wurden Überprüft und können eingetragen werden... ###
		// Daten zur Änderung anzeigen ... ###
		else {
			if (empty ($this->checked->messageget)) {
				$this->checked->messageget = "";
			}
			// Es wurden Änderungen vorgenommen,  anzeigen ...
			if ($this->checked->messageget != "") {
				$this->content->template['alerts'][] = ['type'=>'message', 'content'=>$this->content->template['message_21']];
			}
			// Daten aus der Datenbank holen und anzeigen ... ###
			$sql = sprintf("SELECT kontakt_text_sans FROM %s WHERE lang_id='%d' LIMIT 1",
				$this->cms->papoo_language_stamm,
				$this->db->escape($this->lang_aktu_id)
			);
			$this->content->template['Beschreibung'] = "nodecode:" . $this->db->get_var($sql);
			// Editor zuweisen
			$this->intern_artikel->check_editor();
			// Bilder zuweisen
			$this->intern_artikel->get_images($this->lang_aktu_id);
			// Downloads und Artikel zuweisen
			$this->intern_artikel->get_downloads($this->lang_aktu_id);
			// CSS-Klassen zuweisen (für QuickTag-Editor)
			$this->intern_artikel->get_css_klassen();
			// Sprachen zuweisen
			// $this->make_div_lang();
			$this->content->template['template_weiche'] = "KONTAKT-TEXT";
		}
	}

	/**
	 *
	 */
	function make_div_lang()
	{
		// lang_id rausfinden
		if(!empty($_SESSION['langid_front'])) {
			$this->lang_aktu_id=$_SESSION['langid_front'];
		}
		else {
			$this->lang_aktu_id = $this->cms->lang_id;
		}
		//elseif (empty($this->checked->lang_idx)) $this->lang_aktu_id = $this->cms->lang_id;
		//else $this->lang_aktu_id = $this->checked->lang_idx;
		// aktive Sprachen raussuchen
		// $eskuel="SELECT lang_id, lang_short, lang_long FROM ".$this->cms->papoo_name_language." WHERE more_lang='2' OR lang_short='".$this->cms->frontend_lang."' ";
		$eskuel = "SELECT lang_id, lang_short, lang_long FROM " . $this->cms->papoo_name_language . " WHERE more_lang='2'";
		$reslang = $this->db->get_results($eskuel);

		$sprachen = array();
		// Daten dazu raussuchen
		foreach ($reslang as $lan) {
			if ($lan->lang_id == $this->lang_aktu_id) {
				$this->content->template['active_language'] = $lan->lang_id;
				$this->content->template['article'] = $lan->lang_long;

				$this->content->template['tinymce_lang_id'] = $lan->lang_id;
				// lang_short für TinyMCE bestimmen, da TinyMCE nicht alle Sprachen untertützt solange mit Notlösung cms->lang_back_short arbeiten
				// $this->content->template['tinymce_lang_short'] = $lan->lang_short;
				$this->content->template['tinymce_lang_short'] = $this->cms->lang_back_short;
			}
			// Daten für die Links
			$sprachen[] = array('language' => $lan->lang_long, 'lang_id' => $lan->lang_id ,'lang_short' => $lan->lang_short);
		}
		$this->content->template['menlang'] = $sprachen;

		if(!isset($this->content->template['article'])) {
			$this->content->template['article'] = NULL;
		}
	}

	/**
	 *
	 */
	function make_rights()
	{
		IfNotSetNull($this->checked->rechte);
		// Wenn das Formular Übermittelt wurde Datenbank updaten
		if (is_array($this->checked->rechte) && count($this->checked->rechte) > 100) {
			// alte Daten löschen, aber nur die aktuellen gruppenids
			foreach ($this->checked->gruppe_read as $del) {
				if ($del == 1)
					continue;
				$sql = "DELETE FROM " . $this->cms->papoo_lookup_men_int . " WHERE gruppenid<>1 AND gruppenid='" . $this->db->escape($del) . "'";
				$this->db->query($sql);
			}
			// Neue Daten eintragen
			$rechte = $this->checked->rechte;
			$i=0;
			if (!empty($rechte)) {
				foreach ($rechte as $recht) {
					$i++;
					//&& $recht['menuid']==1
					if (empty ($recht['gruppenid']) && $recht['menuid']==1) {
						#$recht['gruppenid']=$rechte[$i]['gruppenid'];
					}

					if (!empty ($recht['gruppenid']) and $recht['gruppenid'] != 1) {
						$sql = sprintf("INSERT INTO %s SET menuid='%d', gruppenid='%d'",
							$this->cms->papoo_lookup_men_int,
							$recht['menuid'],
							$recht['gruppenid']
						);
						$this->db->query($sql);
					}
				}
			}
		}
		// Die Rechteveraltung der Menüs im Adminbereich
		$this->content->template['case2'] = '1';
		// lang_id aktualisieren
		// Gruppendaten auslesen  AND gruppeid <> 1
		// $co = "SELECT COUNT(gruppeid) FROM  ".$this->cms->papoo_gruppe." WHERE admin_zugriff=1 ";
		$co = sprintf("SELECT COUNT(gruppeid) FROM %s WHERE admin_zugriff='1' ",
			$this->cms->papoo_gruppe
		);
		$this->weiter->result_anzahl = $this->db->get_var($co);
		// maximale Gruppenanzahl pro Auswurf
		if ($this->weiter->result_anzahl >= 7) {
			$limit = 7;
		}
		else {
			$limit = $this->weiter->result_anzahl;
		}
		$this->weiter->make_limit($limit);
		$this->cms->pagesize = $limit;
		$this->weiter->weiter_link = "./stamm.php?menuid=50";
		$this->weiter->do_weiter("teaser");
		// Abfrage
		$sql = sprintf("SELECT gruppeid, gruppenname FROM  %s WHERE admin_zugriff='1' ORDER BY gruppeid ASC %s",
			$this->cms->papoo_gruppe,
			$this->weiter->sqllimit
		);
		$resultgr = $this->db->get_results($sql);

		$this->content->template['menu_rechte_data'] = $this->content->template['menu_data_rights'] = array ();
		// Daten fürs Template, Überschriften mit Gruppennamen
		if (!empty($resultgr)) {
			foreach ($resultgr as $row) {
				array_push($this->content->template['menu_rechte_data'], array ('gruppename' => $row->gruppenname, 'gruppeid' => $row->gruppeid));
			}
		}
		$i = $j = 1;
		foreach ($this->menu->data_back_complete as $menu_punkt) {
			if ($i % 2 != 0) {
				$style = "dedede";
			}
			else {
				$style = "fff";
			}
			$i ++;
			$menrechte = array ();
			// Rechte Daten auslesen, vertretbar als Abfrage, da nur begrenzt viele Abfragen stattfinden, besser als 400000 Loops als Arrays...
			$sql = sprintf("SELECT * FROM " . $this->cms->papoo_lookup_men_int . " WHERE menuid='%d'", $this->db->escape($menu_punkt['menuid']));
			// Daten in Array zuweisen
			$resultgr_look = $this->db->get_results($sql);
			// Gruppennamen durchgehen
			foreach ($resultgr as $row2) {
				$j ++;
				$checked_punkt = "";
				// dazu jeweiligen Rechte passend zur gruppenid + menuid
				foreach ($resultgr_look as $row3) {
					// wenn gruppenid in lookup vorkommt, dann checked machen
					if (empty ($checked_punkt)) {
						if ($row3->gruppenid == $row2->gruppeid) {
							$checked_punkt = 'nodecode:checked="checked"';
						}
						else {
							$checked_punkt = "";
						}
					}
				}
				array_push($menrechte, array ('gruppenid' => $row2->gruppeid, 'checked' => $checked_punkt, 'inputid' => $j));
			}

			array_push($this->content->template['menu_data_rights'], array (
					'menuname' => $menu_punkt['menuname'],
					'menuid' => $menu_punkt['menuid'],
					'level' => $menu_punkt['level'],
					'gruppe' => $menrechte,
					'style' => $style,)
			);
		}
	}

	/**
	 * @param string $tablelist
	 * @param string $pfad
	 */
	function make_dump_intern($tablelist = "", $pfad = "")
	{
		ini_set('upload_max_filesize', '2M');
		ini_set('upload_max_filesize', '4M');
		ini_set('upload_max_filesize', '8M');
		ini_set('upload_max_filesize', '16M');
		$this->content->template['max_upload_size'] = ini_get('upload_max_filesize');
		$this->content->template['casedump'] = 1;

		IfNotSetNull($this->upload_error);
		if ($this->upload_error) {
			$this->content->template['upload_error'] = '1';
		}

		if (!empty ($this->checked->deldump)) {
			// unlink(PAPOO_ABS_PFAD . "/interna/templates_c/*.sql");
			$this->diverse->remove_files(PAPOO_ABS_PFAD . "/interna/templates_c/", "sql");
		}
		if (!empty ($this->checked->makedump)) {
			/*
			Datenbank Sicherung erstellen und zum Download anbieten
			* Es wird keine Sicherung auf dem Server gemacht,
			* das ist deutlich zu unsicher.
			*/
			$zeit = date('j-m-y');
			if ($pfad != "no") {
				if (empty($pfad)) {
					// echo "HIER";
					$this->dumpnrestore->dump_filename = PAPOO_ABS_PFAD . "/interna/templates_c/papoo_dump" . $zeit . ".sql";
				}
				else {
					$this->dumpnrestore->dump_filename = $pfad;
				}
			}
			$dumptext = "";
			if (empty($tablelist)) {
				$tablelist = $this->dumpnrestore->make_tablelist();
			}
			$counttab = 0;
			$i = 0;
			$lim = 0;
			if ((is_numeric($this->checked->counttab))) {
				$counttab = $this->checked->counttab;
			}

			if ((is_numeric($this->checked->lim))) {
				$lim = $this->checked->lim;
			}

			if ($tablelist) {
				foreach ($tablelist as $tabelle) {
					$this->dumpnrestore->export = "data_dump";
					if (($i >= $counttab)) {
						$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($tabelle['tablename'], $lim, "5000", $i);
						$lim = 0;
					}
					$i++;
				}
			}
			if (empty($pfad)) {

				$derpfad = PAPOO_WEB_PFAD . "/interna/templates_c/papoo_dump" . $zeit . ".sql";
				$this->make_download($dumptext, $derpfad);
			}
		}
	}

	/**
	 * @param string $tablelist
	 * @param string $pfad
	 */
	function make_dump($tablelist = "", $pfad = "")
	{
		$this->content->template['casedump'] = 1;
		$this->dumpnrestore->doupdateok = "ok";

		if (!empty ($this->checked->makedump)) {
			/* Datenbank Sicherung erstellen und zum Download anbieten
			* Es wird keine Sicherung auf dem Server gemacht,
			* das ist deutlich zu unsicher.
			*/

			if ($pfad != "no") {
				if (empty($pfad)) {
					$this->dumpnrestore->dump_filename = PAPOO_ABS_PFAD . "/dokumente/logs/dump.sql";
				}
				else {
					$this->dumpnrestore->dump_filename = $pfad;
				}
			}
			$dumptext = "";
			if (empty($tablelist)) {
				$tablelist = $this->dumpnrestore->make_tablelist();
			}
			if ($tablelist) {
				foreach ($tablelist as $tabelle) {
					if (empty($this->structure_dump)) {
						$this->dumpnrestore->export = "data_dump";
					}
					$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($tabelle['tablename'],0,1000);
					$dumptext .= $this->dumpnrestore->dump_text();
				}
			}
			if ($pfad == "no") {
				echo $dumptext;
			}

			if (empty($pfad)) {
				$this->make_download($dumptext);
			}
		}
	}

	/**
	 *
	 */
	function make_restore()
	{
		@ini_set("memory_limit", "300M"); // Bei Speicher-Problemen evtl. aktivieren
		@set_time_limit(0);

		if (empty($this->checked->restore_nr)) {
			$this->checked->restore_nr = "";
		}

		$tmp_file = $this->cms->pfadhier . "/interna/templates_c/restore.sql";

		// Falls die Datei manuell über das Backend hochgeladen wird
		if (empty($this->checked->backdump)) {
			$this->upload_error = false;
			if ($_FILES['myfile']['error'] == 1) {
				$this->upload_error = true;
				return;
			}
			move_uploaded_file($_FILES['myfile']['tmp_name'], $tmp_file);
		}
		$this->dumpnrestore->restore($tmp_file);
	}

	/**
	 * Damit wird der Inhalt einer Variablen als *.txt zum Download angestoßen
	 *
	 * @param $download
	 * @param string $redirect
	 */
	function make_download($download, $redirect = "")
	{
		if ((!empty($redirect))) {
			$this->content->template['redirect'] = $redirect;
		}
		else {
			// Header zuweisen
			if (empty($this->fileext)) {
				$this->fileext = "";
			}

			$filename = "papoo_dump" . $this->fileext . date('j-m-y_H-m-s') . ".sql";
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Accept-Ranges: bytes");
			// header("Content-Type: application/force-download");
			header("Content-Type: application/download");
			// Wenn Opera oder MSIE auftauchen
			if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT')) or preg_match('#MSIE ([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT'))) {
				header("Content-Type: application/octetstream");
			}
			else {
				header("Content-Type: application/octetstream");
			}
			// header("Content-Length: $rowdown->downloadgroesse ");
			// header("Pragma: no-cache");
			$browser = $_SERVER['HTTP_USER_AGENT'];
			if (preg_match('/MSIE 5.5/', $browser) || preg_match('/MSIE 6.0/', $browser)) {
				header('Content-Disposition:  filename="' . $filename . '"');
				if (preg_match('/pdf/', $filename)) {
					header('Content-Disposition: attachment; filename="' . $filename . '"');
				}
			}
			else {
				header('Content-Disposition: attachment; filename="' . $filename . '"');
			}
			// header('Content-Disposition: attachment; filename=' . $filename . '');
			// Send Content-Transfer-Encoding HTTP header
			// (use binary to prevent files from being encoded/messed up during transfer)
			header('Content-Transfer-Encoding: binary');
			// File auslesen
			print($download);
			// Wichtig, sonst gibt es Chaos!
			exit;
		}
	}

	/**
	 * Falls der Wartungstext leer ist wird dieser hier geladen
	 *
	 * @return string
	 */
	function preload_wartung()
	{
		$wartungstext='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Wartungsarbeiten</title>
<style type="text/css">
* {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}
body {
	margin:0 auto;
	padding: 0;
	background: #EFEFEF;
	font-size: 0.8em;
	color: #444;
}
h1, h2, h3, h4, h5, h6 {
	margin: 0;
	padding: 0;
	text-transform: lowercase;
	font-weight: normal;
	color: #000000;
}
#content {
margin:15% 0 auto;
width:400px;
background:#fff;
border:1px solid black;
padding:30px;
}
#colone {
margin-top:30px;
}
</style>
</head>
<body>
<div id="content">
<div id="headertpl" style="">
<h1 class="weiter">Die Seite wird gewartet</h1>
</div>
<div id="colone">
Bitte besuchen Sie diese Seite in wenigen Minuten wieder.
<p>Wir führen derzeit dringende Wartungsarbeiten durch.</p>
</div>
</div>
</body>
</html>
		';
		return utf8_encode($wartungstext);
	}

	/**
	 * @param string $url
	 * @return mixed|string|string[]|null
	 */
	function urlencode($url = "")
	{
		if (!stristr($url,":::")) {
			$url = $this->replace_uml($url);
			$url = urlencode($url);
		}
		return $url;
	}

	/**
	 * Umlaute und Spezialfälle ersetzen
	 *
	 * @param string $url
	 * @return string
	 */
	function replace_uml($url = "")
	{
		if (!stristr($url,":::")) {
			// $url=urldecode($url);
			$ae = utf8_encode("ä");
			$aeb = utf8_encode("Ä");
			$ue = utf8_encode("ü");
			$ueb = utf8_encode("Ü");
			$oe = utf8_encode("ö");
			$oeb = utf8_encode("Ö");
			$amp = utf8_encode("&");
			$frag = utf8_encode("\?");
			$ss = utf8_encode("ß");
			$url = str_ireplace(" ", "-", $url);
			// $url=str_ireplace("ue","u-e",$url);
			// $url=str_ireplace("ae","a-e",$url);
			// $url=str_ireplace("oe","o-e",$url);
			$url = str_ireplace("ä", "ae", $url);
			$url = str_ireplace("ö", "oe", $url);
			$url = str_ireplace("ü", "ue", $url);
			$url = str_ireplace("ä", "ae", $url);
			$url = str_ireplace("ö", "oe", $url);
			$url = str_ireplace("ü", "ue", $url);
			$url = str_ireplace($ae, "ae", $url);
			$url = str_ireplace($aeb, "ae", $url);
			$url = str_ireplace($oe, "oe", $url);
			$url = str_ireplace($oeb, "oe", $url);
			$url = str_ireplace($ue, "ue", $url);
			$url = str_ireplace($ueb, "ue", $url);
			$url = str_ireplace("_", "-", $url);

			$url = str_ireplace($amp, "und", $url);
			$url = str_ireplace($ss, "ss", $url);
			$url = str_ireplace($frag, "-digitff", $url);
			$url = str_ireplace('/', '-', $url);
			$url = str_replace('\\', '-', $url);
			$url = str_ireplace('"', '', $url);
			$url = str_ireplace("'", '', $url);

			if (function_exists('mb_strtolower')) {
				$url = mb_strtolower(($url));
			}
			else {
				$url = strtolower(($url));
			}

			$url = str_ireplace("%","",$url);
			$url = urlencode($url);
		}
		return $url;
	}
}

$intern_stamm = new intern_stamm_class();
