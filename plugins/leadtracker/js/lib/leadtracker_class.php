<?php

require_once(PAPOO_ABS_PFAD."/lib/classes/class.phpmailer.php");

class leadtracker_class
{
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content;
		$this->content = & $content;
		
		// Test ob Einbindung funktioniert hat:
		//print_r($this->content->template['plugin']['test']);

		// Einbindung des globalen Content-Objekts
		global $content;
		$this->content = & $content;

		// Test ob Einbindung funktioniert hat:
		//print_r($this->content->template['plugin']['test']);

		global $db;
		$this->db = &$db;
		global $checked;
		$this->checked = &$checked;

		global $diverse;
		$this->diverse = &$diverse;
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		//CMS Daten einbinden
		global $cms;
		$this->cms = &$cms;
		//Intern Men� Klass einbinden
		global $weiter;
		$this->weiter = &$weiter;
        global $mail_it;
        $this->mail_it = &$mail_it;


		global $db_abs;
		$this->db_abs = &$db_abs;
		//$this->check_domain();

		if (self::in_my_template()) $this->do_action();

		if ( defined("admin") )
		{
			$this->user->check_intern();
			#$this->shop->echo_test();
			global $template;

			//$this->content->template['is_dev'] = "OK";
			//error_reporting(E_ALL);

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );
			//$this->content->template['is_dev']="ok";

			if ( $template != "login.utf8.html")
			{
				//$this->check_domain();

				$this->set_template_sets();

				//Erstmal den Link setzen
				$this->content->template['follow_up_standard_basis_link']="plugin.php?menuid=".$this->checked->menuid."&template=";

				//Erstmal den Link setzen
				$this->content->template['follow_up_new_link']="plugin.php?menuid=".$this->checked->menuid."&template=";
				//debug::print_d($template);
				//Standard Download einer Datei managen
				if (in_array($template2,$this->template_set['download_file']))
				{
					$this->manage_standard_download();
				}

				if (in_array($template2,$this->template_set['unique_form']))
				{
					$this->manage_unique_form();
				}

			}
		}


	}

	private static function in_my_template()
	{
		global $template;
		return strpos($template, 'plugins/leadtracker/') !== false;
	}

	private function in_template($name)
	{
		global $template;
		return strpos($template, $name) == strlen($template)-strlen($name);
	}

	private function make_settings() {
		if (!empty($_POST['ajax'])) {
			session_commit();
			$this->ajax_generate_statistics($_POST['step']);
		}
		if (!empty($_POST['generate_statistics'])) {
			$this->generate_statistics();
		}
	}

	private function do_action()
	{
		$basepath = $_SERVER['PHP_SELF'].'?menuid='.((int)$this->checked->menuid);
		$this->content->template['base_path'] = htmlspecialchars($basepath);
		$this->content->template['script_path'] = $script_path = PAPOO_WEB_PFAD.'/plugins/leadtracker/js';
		$this->content->template['css_path'] = $script_path = PAPOO_WEB_PFAD.'/plugins/leadtracker/css';
		$this->content->template['frontend_base_path'] = PAPOO_WEB_PFAD.'/plugin.php?template=';
		$this->content->template['backend_base_path'] = PAPOO_WEB_PFAD.'/interna/plugin.php?template=';
		$this->content->template['backend_root'] = PAPOO_WEB_PFAD.'/interna/';
		$this->content->template['template_base_path'] = $basepath.'&template=leadtracker/templates/';
		//$this->generate_statistics();
		if($this->in_template('leadtracker/templates/leadtracker_user_statistik.html'))
		{
			$this->content->template['template'] = 'leadtracker/templates/leadtracker_user_statistik.html';
			$this->make_settings();
			$this->make_statistics();
		}
		else if($this->in_template('leadtracker/templates/leadtracker_backend_details.html'))
		{
			$this->make_details();
		}
		elseif ($this->in_template('leadtracker/templates/leadtracker_backend_formdetails.html'))
		{
			$this->make_formdetails();
		}
		elseif ($this->in_template('leadtracker/templates/leadtracker_cronjob.html'))
		{
			$this->get_and_send_due_fum();
		}
		elseif ($this->in_template('leadtracker/templates/leadtracker_follow_up.html'))
		{
			$this->make_followup_list();
		}
	}

	private function manage_unique_form()
	{
		require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_manage_unique_form_class.php");
		//debug::print_d("MANAGE_DOWNLOAD");
		//ini
		$this->leadtracker_manage_unique_form_class = new leadtracker_manage_unique_form_class();
	}

	/**
	* Download einer Datei - Einbindung der Klasse und Managen.
	*/

	private function manage_standard_download()
	{
		require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_manage_download.php");
		//debug::print_d("MANAGE_DOWNLOAD");
		//ini
		$leadtracker_manage_download_class = new leadtracker_manage_download_class();

		//Admin mangen
	// $leadtracker_manage_download_class->manage_admin();
	}

	/**
	* Hier die Template Sets setzen anhand die Verzweigung in die Klasse erfolgt
	*/
	private function set_template_sets()
	{
		$this->template_set['download_file']=array("leadtracker_follow_up.html",
													"leadtracker_download_conversions.html",
													"leadtracker_follow_up_manager.html"
		);

		$this->template_set['unique_form']=array("leadtracker_unique_form.html",
												"leadtracker_follow_up_manager_set_follow_form.html");
	}


	/**
	* Mit der Postpapoo die Frontend Dinge regeln
	*/

	public function post_papoo()
	{
	//debug::print_d($this->checked);
	// debug::Print_d($_COOKIE);
		if (!empty($this->checked->form_manager_id))
		{
			//Formulartracking ini
			$this->manage_unique_form();

			//Frontend Handling aktivieren
			$this->leadtracker_manage_unique_form_class->post_papoo_leadtracker();

		}

		//Achtung , es gibt einen Download... den checken
		if ($this->checked->download_form=="ok")
		{
			//Einbinden damit der gesteuerte Download durchgerführt werden kann
			require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_download_form_now.php");

			//ini
			$leadtracker_download= new leadtracker_download_class_form_now();
			$leadtracker_download->do_lead_tracker_download();
		}
	}


	/**
	* Output direkt filtern...
	*/
	function output_filter()
	{

		if (!empty($this->checked->form_manager_id))
		{
			//Formulartracking ini
			$this->manage_unique_form();

			//Frontend Handling aktivieren
			$this->leadtracker_manage_unique_form_class->output_filter_leadtracker();

		}
	}

	//Besucherstatistiken anlegen (ursprünglich in Daylite leadtrackernector)

	function ajax_generate_statistics($step) {
		@set_time_limit(0);
		header('Content-Type: application/json; charset=UTF-8');
		if (!ctype_digit($step) && !is_int($step)) {
			header('HTTP/1.0 400 Bad Request');
			echo '{"error": "bad request"}';
		}
		$step = (int)$step;
		$this->generate_statistics($step);
		echo '{"success": true}';
		exit();
	}

	function make_statistics() {
		if ($_SERVER['REQUEST_METHOD'] != 'HEAD')
		{
			$this->incremental_statistics();
		}
		$search_mail = null;
		$search_visits = null;
		$search_forms = null;
		if (isset($this->checked->search_mail) and $this->checked->search_mail !== '') {
			$search_mail = $this->checked->search_mail;
		}
		if (isset($this->checked->search_visits) and $this->checked->search_visits !== '') {
			$search_visits = (int)$this->checked->search_visits;
		}
		if (isset($this->checked->search_forms) and $this->checked->search_forms !== '') {
			$search_forms = (int)$this->checked->search_forms;
		}
		$this->content->template['search_mail']  = $search_mail;
		$this->content->template['search_forms']  = $search_forms;
		$this->content->template['search_visits']  = $search_visits;

		$link = $this->content->template['template_base_path'].'leadtracker_user_statistik.html';
		if ($search_mail)
			$link .= "&search_mail=".urlencode($this->checked->search_mail);
		if ($search_visits)
			$link .= "&search_visits=".urlencode($this->checked->search_visits);
		if ($search_forms)
			$link .= "&search_forms=".urlencode($this->checked->search_forms);
		$this->weiter->weiter_link = htmlspecialchars($link);

		$this->content->template['users'] = $this->get_statistics($search_mail, $search_visits, $search_forms);
	}

	function make_details() {
		global $template;
		$tab = (!empty($this->checked->tab)?$this->checked->tab:'forms');
		$this->content->template['subpage'] = $tab;
		$this->content->template['cookie_id'] = (!empty($this->checked->cookie_id)?$this->checked->cookie_id:'');
		$this->content->template['statistic'] = $this->get_single_statistic($this->checked->cookie_id);

		$this->weiter->weiter_link = htmlspecialchars(
			$this->content->template['template_base_path'].'leadtracker_backend_details.html&cookie_id='
			.urlencode($this->checked->cookie_id).'&tab='.urlencode($tab)
		);

		if ($tab == 'forms')
			$this->content->template['forms'] = $this->get_forms($this->checked->cookie_id);
		if ($tab == 'pages')
			$this->content->template['pages'] = $this->get_pages($this->checked->cookie_id);
		if ($tab == 'downloads')
			$this->content->template['downloads'] = $this->get_downloads($this->checked->cookie_id);
		if ($tab == 'fums')
			$this->content->template['fums'] = $this->get_fums($this->checked->cookie_id);

		$accept = $_SERVER['HTTP_ACCEPT'];
		if (strpos($accept, 'fragment=#subcontent') !== FALSE) {
			header('X-Html-Fragment: #subcontent');
			header('Vary: *');
			if ($tab == 'forms')
				$template = str_replace('/leadtracker_backend_details.html', '/leadtracker_backend_details_sub_forms.html', $template);
			elseif ($tab == 'pages')
				$template = str_replace('/leadtracker_backend_details.html', '/leadtracker_backend_details_sub_pages.html', $template);
			elseif ($tab == 'downloads')
				$template = str_replace('/leadtracker_backend_details.html', '/leadtracker_backend_details_sub_downloads.html', $template);
			elseif ($tab == 'fums')
				$template = str_replace('/leadtracker_backend_details.html', '/leadtracker_backend_details_sub_fums.html', $template);
			else {
				echo '<p class="error">Error.</p>';
				die();
			}
		}
	}

	function generate_statistics($step=null) {
		if ($step === null or $step === 0) {
			// Statistiken leeren
			$sql = sprintf('TRUNCATE TABLE `%s`', $this->cms->tbname['plugin_leadtracker_statistics']);
			$this->db->query($sql);
			$sql = sprintf('TRUNCATE TABLE `%s`', $this->cms->tbname['plugin_leadtracker_incremental']);
			$this->db->query($sql);
		}
		if ($step === null or $step === 1) {
			// Temporäre Tabelle um Dopplungen bei Formularen wegzukriegen
			$sql = "CREATE TEMPORARY TABLE `_leadtracker_lookup_form` (
							`leadtracker_uid` VARCHAR(80),
							`leadtracker_form_id` INT,
							`leadtracker_lead_id` INT,
							PRIMARY KEY (`leadtracker_uid`, `leadtracker_lead_id`))";
            $this->db->query($sql);
			$sql = sprintf("INSERT INTO `_leadtracker_lookup_form`
						SELECT DISTINCT `leadtracker_uid`, `leadtracker_form_id`, `leadtracker_lead_id`
						      FROM `%s` WHERE `leadtracker_uid` <> '' AND `leadtracker_lead_id` <> 0 GROUP BY `leadtracker_uid`, `leadtracker_lead_id`",
				$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid']
			);
			$this->db->query($sql);
            /*$sql = "SELECT * FROM `_leadtracker_lookup_form`";
            echo '<pre>' . $sql;
            $res = $this->db->get_results($sql, ARRAY_A);
            echo '<table><tr>';
            foreach ($res[0] as $key=>$val)
            {
                echo '<th>' . $key . '</th>';
            }
            echo '</tr>';
            foreach($res as $row)
            {
                echo '<tr>';
                foreach($row as $val)
                    echo '<td>' . $val . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            var_dump($res);
            echo '</pre>';
            exit;//*/
			// Neue Statistiken generieren
			$sql = sprintf("INSERT INTO `%s`
					(`cookie_id`, `count_visits`, `count_forms`, `count_downloads`, `first_visit`, `last_visit`, `last_download`)
					SELECT `leadtracker_track_cookie`,
						COUNT(NULLIF(`leadtracker_track_download_id` = 0, 0)),
						IFNULL(`form_count`, 0),
						COUNT(NULLIF(`leadtracker_track_download_id`, 0)),
						MIN(`leadtracker_track_timestamp`),
						MAX(`leadtracker_track_timestamp`),
						MAX(IF(`leadtracker_track_download_id` <> 0, `leadtracker_track_timestamp`, NULL))
					FROM `%s` AS `a` LEFT JOIN (
						SELECT `leadtracker_uid`, COUNT(*) AS `form_count`
						FROM `_leadtracker_lookup_form` GROUP BY `leadtracker_uid`
					) AS `b` ON `leadtracker_track_cookie` = `leadtracker_uid`
					GROUP BY `leadtracker_track_cookie`",
				$this->cms->tbname['plugin_leadtracker_statistics'],
				$this->cms->tbname['plugin_leadtracker_tracking']
			);
			$this->db->query($sql);
		}
		if ($step === null or $step === 2) {
			$this->fill_email_statistics();
		}
		if ($step === null or $step === 3) {
			$this->add_check_replace_statistics();
		}
		if ($step === null or $step === 4) {
			$sql = sprintf("INSERT INTO `%s` (`last_tracking_id`, `last_form_track_id`) SELECT MAX(`leadtracker_track_id`), MAX(`leadtracker_uid_id`) FROM `%s` JOIN `%s` ON `leadtracker_track_cookie` = `leadtracker_uid`",
				$this->cms->tbname['plugin_leadtracker_incremental'],
				$this->cms->tbname['plugin_leadtracker_tracking'],
				$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid']
			);
			$this->db->query($sql);
		}
	}

	function incremental_statistics($maxcount=1000000) {
		$this->db->query('BEGIN');
		// Daten des letzten inkrementellen Updates holen
		$sql = sprintf('SELECT * FROM `%s` ORDER BY `id` DESC LIMIT 1',
			$this->cms->tbname['plugin_leadtracker_incremental']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			$last_tracking_id = 0;
			$last_form_track_id = 0;
			// Abbrechen wenn noch keine Statistiken da
			$this->db->query('COMMIT');
			return;
		} else {
			$last_tracking_id = (int)$result[0]['last_tracking_id'];
			$last_form_track_id = (int)$result[0]['last_form_track_id'];
		}

		// Temporäre Tabelle um Dopplungen bei Formularen wegzukriegen
		$sql = "CREATE TEMPORARY TABLE `_leadtracker_lookup_form` (
					`leadtracker_uid` VARCHAR(250) NOT NULL,
					`leadtracker_form_id` INT,
					`leadtracker_lead_id` INT,
					PRIMARY KEY (`leadtracker_uid`, `leadtracker_lead_id`))";
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO `_leadtracker_lookup_form`
					SELECT DISTINCT `leadtracker_uid`, `leadtracker_form_id`, `leadtracker_lead_id`
					FROM `%s` WHERE `leadtracker_uid_id` > %d AND `leadtracker_uid` <> '' AND `leadtracker_lead_id` <> 0 GROUP BY `leadtracker_uid`, `leadtracker_lead_id`",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$last_form_track_id
		);
        $this->db->query($sql);

		// Neue Statistiken generieren und hinzufügen
		$sql = sprintf("INSERT INTO `%s`
				(`cookie_id`, `count_visits`, `count_forms`, `count_downloads`, `first_visit`, `last_visit`, `last_download`)
				SELECT `leadtracker_track_cookie`,
					COUNT(NULLIF(`leadtracker_track_download_id` = 0, 0)),
					IFNULL(`form_count`, 0),
					COUNT(NULLIF(`leadtracker_track_download_id`, 0)),
					MIN(`leadtracker_track_timestamp`),
					MAX(`leadtracker_track_timestamp`),
					MAX(IF(`leadtracker_track_download_id` <> 0, `leadtracker_track_timestamp`, NULL))
				FROM `%s` AS `a` LEFT JOIN (
					SELECT `leadtracker_uid`, COUNT(*) AS `form_count`
						FROM `_leadtracker_lookup_form` GROUP BY `leadtracker_uid`
				) AS `b` ON `leadtracker_track_cookie` = `leadtracker_uid`
				WHERE `leadtracker_track_id` > %d
				GROUP BY `leadtracker_track_cookie`
				ON DUPLICATE KEY UPDATE
					`count_visits` = `count_visits` + VALUES(`count_visits`),
			`count_forms` = `count_forms` + VALUES(`count_forms`),
			`count_downloads` = `count_downloads` + VALUES(`count_downloads`),
			`last_visit` = VALUES(`last_visit`),
			`last_download` = IFNULL(VALUES(`last_download`), `last_download`)",
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$this->cms->tbname['plugin_leadtracker_tracking'],
			$last_tracking_id
		);
		$this->db->query($sql);
		$this->fill_email_statistics($last_form_track_id);
		$this->add_check_replace_statistics($last_form_track_id);
		// Neue Grenze für inkrementelles Update speichern
        $sql = sprintf("SELECT MAX(`leadtracker_uid_id`) FROM `%s` INNER JOIN `%s` ON `leadtracker_uid` = `leadtracker_track_cookie`",
            $this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
            $this->cms->tbname['plugin_leadtracker_tracking']
        );
        $maxuid = $this->db->get_var($sql);
        $sql = sprintf("SELECT MAX(`leadtracker_track_id`) FROM `%s`",
            $this->cms->tbname['plugin_leadtracker_tracking']
        );
        $maxtrackid = $this->db->get_var($sql);
        $sql = sprintf("INSERT INTO `%s` (`last_tracking_id`, `last_form_track_id`) SELECT MAX(`leadtracker_track_id`), MAX(`leadtracker_uid_id`) FROM `%s` JOIN `%s` ON `leadtracker_track_cookie` = `leadtracker_uid`",
            $this->cms->tbname['plugin_leadtracker_incremental'],
            $this->cms->tbname['plugin_leadtracker_tracking'],
            $this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid']
        );
        $this->db->query($sql);
		// Alte Einträge aus Liste inkrementeller Updates löschen
		$sql = sprintf("DELETE FROM `%s` USING `%s` JOIN (SELECT MAX(`id`) AS max_id FROM `%s`) AS `t2` WHERE `id` < `max_id`-50",
			$this->cms->tbname['plugin_leadtracker_incremental'],
			$this->cms->tbname['plugin_leadtracker_incremental'],
			$this->cms->tbname['plugin_leadtracker_incremental']
		);
		$this->db->query($sql);
		$this->db->query('COMMIT');
	}

	function fill_email_statistics($min_form_id=0) {
		// Update die Statistik-Tabelle mit dem ersten 'email'-Feld des letzten abgeschickten Formulars

		// Ermittle das erste E-Mail-Feld pro Formular und speichere die Zuordnung in einer
		// temporären Tabelle
		$sql = sprintf("CREATE TEMPORARY TABLE `_leadtracker_email_fields` (
						`plugin_cform_form_id` INT,
						`plugin_cform_id` INT,
						`plugin_cform_name` VARCHAR(255),
						PRIMARY KEY (`plugin_cform_form_id`)
					) SELECT `plugin_cform_form_id`, `plugin_cform_id`, `plugin_cform_name`
					FROM `%s` WHERE `plugin_cform_type` = 'email'
					GROUP BY `plugin_cform_form_id`
					HAVING `plugin_cform_id` = MIN(`plugin_cform_id`)",
			$this->cms->tbname['papoo_plugin_cform']
		);
		$this->db->query($sql);

		// Ermittle den letzten Lead mit E-Mail-Feld pro UID und speichere die Zuordnung in einer
		// temporären Tabelle
		$sql = sprintf("CREATE TEMPORARY TABLE `_leadtracker_last_email_leads` (
						`leadtracker_uid` VARCHAR(250),
						`leadtracker_uid_id` INT,
						`leadtracker_form_id` INT,
						`leadtracker_lead_id` INT,
						`plugin_cform_name` VARCHAR(255),
						PRIMARY KEY (`leadtracker_uid`)
					) SELECT `leadtracker_uid`, `leadtracker_uid_id`, `leadtracker_form_id`, `leadtracker_lead_id`, `plugin_cform_name`
					FROM `%s` LEFT JOIN `_leadtracker_email_fields` ON `plugin_cform_form_id` = `leadtracker_form_id`
					WHERE `leadtracker_uid_id` > %d AND `leadtracker_uid` <> '' AND `plugin_cform_id` IS NOT NULL
					GROUP BY `leadtracker_uid`
					HAVING `leadtracker_uid_id` = MAX(`leadtracker_uid_id`)",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$min_form_id
		);
		$this->db->query($sql);

		//var_dump($this->db->get_results('SELECT * FROM _leadtracker_last_email_leads', ARRAY_A));
		//die();

		// Update die Statistik-Tabelle mit den E-Mail-Adressen
		$sql = sprintf("UPDATE `%s`
					INNER JOIN (SELECT `leadtracker_uid`, `form_manager_content_lead_feld_content`, `leadtracker_uid_id`
						FROM `_leadtracker_last_email_leads`
						INNER JOIN `%s` ON `leadtracker_lead_id` = `form_manager_content_lead_id_id` AND `plugin_cform_name` = `form_manager_content_lead_feld_name`
						WHERE `form_manager_content_lead_feld_content` <> '') AS `sub` ON `cookie_id` = `leadtracker_uid`
					SET `mail` = `form_manager_content_lead_feld_content`
					WHERE `form_manager_content_lead_feld_content` IS NOT NULL",
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$this->cms->tbname['papoo_form_manager_lead_content']
		);
		$this->db->query($sql);
	}

	function add_check_replace_statistics($min_form_id=0) {
		// Update die Statistik-Tabelle mit dem ersten 'email'-Feld des letzten abgeschickten Formulars

		// Ermittle Check-Replace-Felder pro Formular und speichere die Zuordnung in einer
		// temporären Tabelle
		$sql = sprintf("CREATE TEMPORARY TABLE `_leadtracker_checkreplace_fields` (
						`plugin_cform_form_id` INT,
						`plugin_cform_id` INT,
						`plugin_cform_name` VARCHAR(255),
						PRIMARY KEY (`plugin_cform_form_id`, `plugin_cform_id`)
					) SELECT `plugin_cform_form_id`, `plugin_cform_id`, `plugin_cform_name`
					FROM `%s` WHERE `plugin_cform_type` = 'check_replace'",
			$this->cms->tbname['papoo_plugin_cform']
		);
		$this->db->query($sql);

		// Ermittle die relevanten Leads pro UID und speichere die Ergebnisse in einer
		// temporären Tabelle
		$sql = sprintf("CREATE TEMPORARY TABLE `_leadtracker_checkreplace_leads` (
						`leadtracker_uid` VARCHAR(250),
						`leadtracker_form_id` INT,
						`leadtracker_lead_id` INT,
						PRIMARY KEY (`leadtracker_uid`, `leadtracker_lead_id`)
					) SELECT DISTINCT `leadtracker_uid`, `leadtracker_form_id`, `leadtracker_lead_id`
					FROM `%s` JOIN `_leadtracker_checkreplace_fields` ON `plugin_cform_form_id` = `leadtracker_form_id`
					WHERE `leadtracker_uid_id` > %d AND `leadtracker_uid` <> ''",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			(int)$min_form_id
		);
		$this->db->query($sql);

		//var_dump($this->db->get_results('SELECT * FROM _leadtracker_checkreplace_leads', ARRAY_A));
		//die();

		// Ermittle pro Lead die Anzahl der Check-Replace-Felder mit '1' und speichere die Ergebnisse in einer
		// temporären Tabelle. DAS DAUERT GGF. LANGE!
		$sql = sprintf("CREATE TEMPORARY TABLE `_leadtracker_checkreplace_counts` (
						`form_manager_content_lead_id_id` INT,
						`count_checkreplace` INT,
						PRIMARY KEY (`form_manager_content_lead_id_id`)
					) SELECT `form_manager_content_lead_id_id`, COUNT(*) AS `count_checkreplace`
					FROM `_leadtracker_checkreplace_leads`
					INNER JOIN `_leadtracker_checkreplace_fields` ON `plugin_cform_form_id` = `leadtracker_form_id`
					INNER JOIN `%s` ON `leadtracker_lead_id` = `form_manager_content_lead_id_id` AND `plugin_cform_name` = `form_manager_content_lead_feld_name`
					WHERE `form_manager_content_lead_feld_content` = '1'
					GROUP BY `form_manager_content_lead_id_id`",
			$this->cms->tbname['papoo_form_manager_lead_content']
		);
		$this->db->query($sql);

		//var_dump($this->db->get_results('SELECT * FROM _leadtracker_checkreplace_counts', ARRAY_A));
		//die();

		// Update die Statistik-Tabelle mit check_replace-Formularfeldern, die als Downloads gezählt werden
		$sql = sprintf("UPDATE %s
				INNER JOIN (SELECT `leadtracker_uid`, SUM(`count_checkreplace`) AS `count_checkreplace`, MAX(`form_manager_form_datum`) AS `max_datum`
					FROM `%s`
					INNER JOIN `%s` ON `leadtracker_lead_id` = `form_manager_lead_id`
					INNER JOIN `_leadtracker_checkreplace_counts` ON `leadtracker_lead_id` = `form_manager_content_lead_id_id`
					WHERE `leadtracker_uid_id` > %d
					GROUP BY `leadtracker_uid`
				) AS `sub` ON `cookie_id` = `leadtracker_uid`
				SET `count_downloads` = `count_downloads` + `count_checkreplace`,
				`last_download` = IFNULL(`max_datum`, `last_download`)
				WHERE `leadtracker_uid` IS NOT NULL",
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$this->cms->tbname['papoo_form_manager_leads'],
			(int)$min_form_id
		);
		$this->db->query($sql);
	}

	function get_statistics($search_mail=null, $search_visits=null, $search_forms=null, $skip_empty_mail=true, $use_weiter=20, $search_last_visit=null) {
		$sql = sprintf('SELECT COUNT(*) FROM `%s`', $this->cms->tbname['plugin_leadtracker_incremental']);
		$count = (int)$this->db->get_var($sql);
		if ($count == null){
			return null;
		}

		// Where-Bedingungen bauen
		$where = array();
		if ($skip_empty_mail) {
			$where[] = "`mail` IS NOT NULL";
		}
		if ($search_mail !== null)
			$where[] = "`mail` LIKE '%".$this->db->escape($search_mail)."%'";
		if ($search_visits !== null)
			$where[] = "`count_visits` >= ".((int)$search_visits);
		if ($search_forms !== null)
			$where[] = "`count_forms` >= ".((int)$search_forms);
		if ($search_last_visit !== null)
			$where[] = "`last_visit` >= ".((int)$search_last_visit);
		if(!$where) $where = '';
		else $where = 'WHERE ('.implode(') AND (', $where).')';

		// Wenn weiter-Klasse benutzt hole Anzahl initialisiere die Klasse
		if ($use_weiter) {
			$sql = sprintf('SELECT COUNT(*) FROM `%s` %s',
				$this->cms->tbname['plugin_leadtracker_statistics'],
				$where
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}

		$sql = sprintf('SELECT * FROM `%s` %s ORDER BY `last_visit` DESC %s',
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$where,
			($use_weiter)?$this->weiter->sqllimit:''
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	function get_single_statistic($cookie_id) {
		$sql = sprintf("SELECT * FROM `%s` WHERE `cookie_id` = '%s' LIMIT 1",
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$this->db->escape($cookie_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) return null;
		return $result[0];
	}

	function get_pages($cookie_id, $use_weiter=20) {
		// Wenn weiter-Klasse benutzt hole Anzahl der Aufrufe und initialisiere die Klasse
		if ($use_weiter) {
			$sql = sprintf("SELECT COUNT(*) FROM `%s` WHERE `leadtracker_track_cookie` = '%s'",
				$this->cms->tbname['plugin_leadtracker_tracking'],
				$this->db->escape($cookie_id)
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}
		// Hole Daten der Seitenaufrufe
		$sql = sprintf("SELECT * FROM `%s` WHERE `leadtracker_track_cookie` = '%s'
				ORDER BY `leadtracker_track_id` DESC %s",
			$this->cms->tbname['plugin_leadtracker_tracking'],
			$this->db->escape($cookie_id),
			($use_weiter)?$this->weiter->sqllimit:''
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) return array();
		foreach ($result as $key => $row)
		{
			if ($row['leadtracker_track_direct_referrer'] == "Direkter Zugriff" || $row['leadtracker_track_direct_referrer'] == "E-Mail")
			{
				$result[$key]['leadtracker_track_nolink'] = TRUE;
			}
		}
		return $result;
	}

	function get_forms($cookie_id, $use_weiter=20, $include_three_values=true, $since=null, $until=null) {
		// Baue WHERE für Cookie-ID
		$where = array();
		if ($cookie_id !== null)
			$where[] = "`leadtracker_uid` = '".$this->db->escape($cookie_id)."'";
		else
			$where[] = "`leadtracker_uid` <> ''";
		if ($where)
			$where = 'WHERE ('.implode(') AND (', $where).')';
		else
			$where = '';

		// Temporäre Tabelle um Dopplungen wegzukriegen und vorzufiltern
		$sql = "CREATE TEMPORARY TABLE `_leadtracker_lookup_form` (
					`leadtracker_uid` VARCHAR(250) NOT NULL,
					`leadtracker_form_id` INT NOT NULL,
					`leadtracker_lead_id` INT NOT NULL,
					PRIMARY KEY (`leadtracker_uid`, `leadtracker_lead_id`)
				)";
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO `_leadtracker_lookup_form` SELECT DISTINCT `leadtracker_uid`, `leadtracker_form_id`, `leadtracker_lead_id`
					FROM `%s` %s",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$where
		);
		$this->db->query($sql);

		// Wenn weiter-Klasse benutzt hole Anzahl der Formulare und initialisiere die Klasse
		if ($use_weiter) {
			$sql = sprintf("SELECT COUNT(*) FROM `_leadtracker_lookup_form` WHERE `leadtracker_uid` = '%s'",
				$this->db->escape($cookie_id)
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}
		// Baue WHERE
		$where = array();
		if ($cookie_id !== null)
			$where[] = "`leadtracker_uid` = '".$this->db->escape($cookie_id)."'";
		if ($since)
			$where[] = "`form_manager_form_datum` >= ".((int)$since);
		if ($until)
			$where[] = "`form_manager_form_datum` <= ".((int)$until);
		if ($where)
			$where = 'WHERE ('.implode(') AND (', $where).')';
		else
			$where = '';

		// Hole Liste der Formulare
		$sql = sprintf("SELECT `form_manager_form_datum` AS `date`,
				`form_manager_lead_id` AS `id`,
				`leadtracker_uid` AS `cookieid`,
				`form_manager_form_id` AS `form_id` FROM `_leadtracker_lookup_form`
				LEFT JOIN `%s` ON `form_manager_lead_id` = `leadtracker_lead_id`
					AND `form_manager_form_id` = `leadtracker_form_id`
				%s ORDER BY `leadtracker_lead_id` DESC %s",
			$this->cms->tbname['papoo_form_manager_leads'],
			$where,
			($use_weiter)?$this->weiter->sqllimit:''
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) return array();
		if ($include_three_values) {
			// Hole für jedes Formular die ersten 3 Felder
			foreach ($result as &$item) {
				$sql = sprintf("SELECT `form_manager_content_lead_feld_content` AS `content`,
						`plugin_cform_type` AS `type`
						FROM `%s` JOIN `%s`
						ON `plugin_cform_name` = `form_manager_content_lead_feld_name`
						WHERE `form_manager_content_lead_id_id` = %d AND `plugin_cform_form_id` = %d
						ORDER BY `form_manager_content_lead_id` ASC LIMIT 3",
					$this->cms->tbname['papoo_form_manager_lead_content'],
					$this->cms->tbname['papoo_plugin_cform'],
					$item['id'],
					$item['form_id']
				);
				$r = $this->db->get_results($sql, ARRAY_A);
				if (!$r) $r = array();
				if (count($r) < 3) {
					for ($i=0; $i < 3-count($r); ++$i) {
						$r[] = array('type'=>'', 'content'=>null);
					}
				}
				$item['fields'] = $r;
			}
		}
		return $result;
	}

	function get_downloads($cookie_id, $use_weiter=20) {
		// Baue WHERE für Cookie-ID
		$where = array();
		if ($cookie_id !== null)
			$where[] = "`leadtracker_uid` = '".$this->db->escape($cookie_id)."'";
		else
			$where[] = "`leadtracker_uid` <> ''";
		if ($where)
			$where = 'WHERE ('.implode(') AND (', $where).')';
		else
			$where = '';

		// Temporäre Tabelle um Dopplungen bei check_replace wegzukriegen und vorzufiltern
		$sql = "CREATE TEMPORARY TABLE `_leadtracker_lookup_form` (
					`leadtracker_uid` VARCHAR(250),
					`leadtracker_form_id` INT,
					`leadtracker_lead_id` INT,
					PRIMARY KEY (`leadtracker_uid`, `leadtracker_lead_id`)
				)";
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO `_leadtracker_lookup_form` SELECT DISTINCT `leadtracker_uid`, `leadtracker_form_id`, `leadtracker_lead_id`
					FROM `%s` %s",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$where
		);
		$this->db->query($sql);

		// Wenn weiter-Klasse benutzt hole Anzahl der Downloads und initialisiere die Klasse
		if ($use_weiter) {
			$sql = sprintf("SELECT COUNT(*) FROM `%s`
				JOIN `%s` ON `leadtracker_track_download_id` = `downloadid`
				WHERE `leadtracker_track_cookie` = '%s' AND `leadtracker_track_download_id` != 0",
				$this->cms->tbname['plugin_leadtracker_tracking'],
				$this->cms->tbname['papoo_download'],
				$this->db->escape($cookie_id)
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$sql = sprintf("SELECT COUNT(*)
					FROM `_leadtracker_lookup_form`
					LEFT JOIN `%s` ON `leadtracker_lead_id` = `form_manager_content_lead_id_id`
					LEFT JOIN `%s` ON `leadtracker_form_id` = `plugin_cform_form_id`
						AND `plugin_cform_name` = `form_manager_content_lead_feld_name`
					WHERE `leadtracker_uid` = '%s'
						AND	`plugin_cform_type` = 'check_replace'
						AND `form_manager_content_lead_feld_content` = '1'
					",
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$this->cms->tbname['papoo_plugin_cform'],
				$this->db->escape($cookie_id)
			);
			$this->weiter->result_anzahl += (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}
		$sql = sprintf("(SELECT `leadtracker_track_cookie` AS `cookie_id`,
						`leadtracker_track_id` AS `id`,
						`leadtracker_track_timestamp` AS `timestamp`,
						`downloadid`,
						`d`.`downloadlink` AS `downloadlink`,
						`dl`.`downloadname` AS `downloadname`,
						'download' AS `type`,
						0 AS `form_id`
					FROM `%s` AS `t`
					LEFT JOIN `%s` AS `d` ON `leadtracker_track_download_id` = `downloadid`
					LEFT JOIN `%s` AS `dl` ON `downloadid` = `download_id` AND `lang_id` = %d
					WHERE `leadtracker_track_cookie` = '%s'
					AND `leadtracker_track_download_id` != 0)
				UNION
				(SELECT `leadtracker_uid` AS `cookie_id`,
						`leadtracker_lead_id` AS `id`,
						`form_manager_form_datum` AS `timestamp`,
						`plugin_cform_id` AS `downloadid`,
						`plugin_cform_name` AS `downloadlink`,
						`plugin_cform_label` AS `downloadname`,
						'check_replace' AS `type`,
						`leadtracker_form_id` AS `form_id`
					FROM `_leadtracker_lookup_form`
					LEFT JOIN `%s` ON `leadtracker_lead_id` = `form_manager_lead_id`
					LEFT JOIN `%s` ON `leadtracker_lead_id` = `form_manager_content_lead_id_id`
					LEFT JOIN `%s` ON `leadtracker_form_id` = `plugin_cform_form_id`
						AND `plugin_cform_name` = `form_manager_content_lead_feld_name`
					LEFT JOIN `%s` ON `plugin_cform_id` = `plugin_cform_lang_id`
						AND `plugin_cform_lang_lang` = %d
					WHERE `leadtracker_uid` = '%s'
						AND	`plugin_cform_type` = 'check_replace'
						AND `form_manager_content_lead_feld_content` = '1')
				ORDER BY `timestamp` DESC %s",
			$this->cms->tbname['plugin_leadtracker_tracking'],
			$this->cms->tbname['papoo_download'],
			$this->cms->tbname['papoo_language_download'],
			$this->cms->lang_id,
			$this->db->escape($cookie_id),
			$this->cms->tbname['papoo_form_manager_leads'],
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$this->cms->tbname['papoo_plugin_cform'],
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$this->cms->lang_id,
			$this->db->escape($cookie_id),
			($use_weiter)?$this->weiter->sqllimit:''
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (!$result) return array();
        $sql = sprintf("SELECT `menuid` FROM `%s` WHERE `menulink` LIKE '%%form_manager/templates/change_email.html%%'",
                        $this->cms->tbname['papoo_menuint']
        );
        $menuids = $this->db->get_results($sql, ARRAY_A);
        $menuid = $menuids[0]['menuid'];

		foreach ($result as &$row) {
			if ($row['type'] == 'check_replace') {
				$name = $row['downloadlink'];
				list($title, $url) = explode('|', $row['downloadname'], 2);
				$row['downloadname_internal'] = $name;
				$row['downloadname'] = $title;
				$row['downloadlink'] = $url;
                $row['menu_id'] = $menuid;
			}
		}

		return $result;
	}

	function get_fums($cookie_id, $use_weiter=20)
	{
		$sql = sprintf("SELECT
							t1 . lookup_lead_fum_sentstamp AS sendedatum,
							t4 . form_manager_content_lead_feld_content AS mailaddr,
							t6 . form_manager_name AS formname,
							t2 . leadtracker_betreff_fum AS fumname
						FROM
							%s AS t1,
							%s AS t2,
							%s as t3,
							%s AS t4,
							%s AS t5,
							%s AS t6
						WHERE t2 . leadtracker_fum_id = t1 . lookup_lead_fum_fum_id
							AND t1 . lookup_lead_fum_lead_id = t3 . leadtracker_lead_id
							AND t3 . leadtracker_uid = '%s'
							AND t1 . lookup_lead_fum_lead_id = t4 . form_manager_content_lead_id_id
							AND t5 . plugin_cform_name = t4 . form_manager_content_lead_feld_name
							AND t5 . plugin_cform_type = 'email'
							AND t6 . form_manager_id = t2 . leadtracker_fum_form_id
						GROUP BY t1 . lookup_lead_fum_sentstamp",
						$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
						$this->cms->tbname['plugin_leadtracker_follow_up_mails'],
						$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
						$this->cms->tbname['papoo_form_manager_lead_content'],
						$this->cms->tbname['papoo_plugin_cform'],
						$this->cms->tbname['papoo_form_manager'],
						$cookie_id
		);
		$sql = sprintf("SELECT * FROM (%s) AS 1t
						WHERE TRUE
						%s",
			$sql,
			($use_weiter)?$this->weiter->sqllimit:''
		);
		$countsql = $sql;
		$result = $this->db->get_results($sql, ARRAY_A);
		$dateformat = "d. m. y";
		foreach ($result as $key => $row)
		{
			if($row['sendedatum']) {
				$this->content->template['followups'][$key]['sendedatum'] = $row['sendedatum'];
				$this->content->template['followups'][$key]['mailaddr'] = $row['mailaddr'];
				$this->content->template['followups'][$key]['formname'] = $row['formname'];
				$this->content->template['followups'][$key]['fumname'] = $row['fumname'];
			}
		}
		$tmpl_string = "cookie_id=" . $cookie_id . "&tab=" . $this->checked->tab;
		$this->weiter->weiter_link = htmlspecialchars(
			$this->content->template['template_base_path'].'leadtracker_backend_details.html&'.$tmpl_string);
		if ($use_weiter)
		{
			$sql = sprintf("SELECT COUNT(*) FROM (%s) AS s1",
				$countsql
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}
	}

	function get_form($lead_id, $use_labels=true) {
		// Hole Lead
		$sql = sprintf("SELECT `form_manager_form_id` AS `form_id`,
					`form_manager_form_datum` AS `datum`,
					`form_manager_form_ip_sender` AS `ip_sender`
				FROM `%s`
				WHERE `form_manager_lead_id` = %d LIMIT 1",
			$this->cms->tbname['papoo_form_manager_leads'],
			(int)$lead_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) return array(null, null, null);
		$lead = $result[0];

		// Hole Inhalt des Formulars und die Formularstruktur
		if ($use_labels) {
			$sql = sprintf("SELECT `form_manager_content_lead_feld_content` AS `content`,
						`form_manager_content_lead_feld_name` AS `name`,
						`plugin_cform_type` AS `type`,
						`plugin_cform_label` AS `label`
					FROM `%s`
					LEFT JOIN `%s` ON `form_manager_content_lead_feld_name` = `plugin_cform_name`
						AND `plugin_cform_form_id` = %d
					LEFT JOIN `%s` ON `plugin_cform_id` = `plugin_cform_lang_id` AND `plugin_cform_lang_lang` = %d
					WHERE `form_manager_content_lead_id_id` = %d ORDER BY `form_manager_content_lead_id` ASC",
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$this->cms->tbname['papoo_plugin_cform'],
				(int)$lead['form_id'],
				$this->cms->tbname['papoo_plugin_cform_lang'],
				(int)$this->cms->lang_id,
				(int)$lead_id
			);
		}
		else {
			$sql = sprintf("SELECT `form_manager_content_lead_feld_content` AS `content`,
						`form_manager_content_lead_feld_name` AS `name`,
						`plugin_cform_type` AS `type`
					FROM `%s`
					LEFT JOIN `%s` ON `form_manager_content_lead_feld_name` = `plugin_cform_name`
						AND `plugin_cform_form_id` = %d
					WHERE `form_manager_content_lead_id_id` = %d ORDER BY `form_manager_content_lead_id` ASC",
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$this->cms->tbname['papoo_plugin_cform'],
				(int)$lead['form_id'],
				(int)$lead_id
			);
		}
		$result = $this->db->get_results($sql, ARRAY_A);
		$formdata = array();
		$metadata = array();
		foreach ($result as $item) {
			if ($item['type'] !== null)
				$formdata[] = $item;
			else {
				if (!isset($metadata[$item['name']]) or $item['content'] !== '')
					$metadata[$item['name']] = $item;
			}
		}
		return array($lead, $formdata, $metadata);
	}

	function make_formdetails() {
		$this->content->template['cookie_id'] = (!empty($this->checked->cookie_id)?$this->checked->cookie_id:'');

		list($this->content->template['lead'], $this->content->template['formdata'], $this->content->template['metadata'])
			= $this->get_form($this->checked->lead_id);

		if ($tab == 'pages')
			$this->content->template['pages'] = $this->get_pages($this->checked->cookie_id);
		if ($tab == 'downloads')
			$this->content->template['downloads'] = $this->get_downloads($this->checked->cookie_id);
	}

	function get_last_interactions($cookie_id) {
		// Letztes Formular
		$sql = sprintf("SELECT MAX(form_manager_form_datum)
					FROM `%s` JOIN `%s` ON `leadtracker_lead_id` = `form_manager_lead_id`
					WHERE `leadtracker_uid` = '%s'",
			$this->cms->tbname['plugin_leadtracker_lookup_form_uid_leadid'],
			$this->cms->tbname['papoo_form_manager_leads'],
			$this->db->escape($cookie_id)
		);
		$last_form = $this->db->get_var($sql);
		if ($last_form !== null)
			$last_form = (int)$last_form;

		// Letzter Besuch
		$sql = sprintf("SELECT MAX(leadtracker_track_timestamp)
					FROM `%s`
					WHERE `leadtracker_track_cookie` = '%s' AND `leadtracker_track_download_id` = 0",
			$this->cms->tbname['plugin_leadtracker_tracking'],
			$this->db->escape($cookie_id)
		);
		$last_visit = $this->db->get_var($sql);
		if ($last_visit !== null)
			$last_visit = (int)$last_visit;

		// Letzter Download
		$sql = sprintf("SELECT `last_download`
					FROM `%s`
					WHERE `cookie_id` = '%s'",
			$this->cms->tbname['plugin_leadtracker_statistics'],
			$this->db->escape($cookie_id)
		);
		$last_download = $this->db->get_var($sql);
		if ($last_download !== null)
			$last_download = (int)$last_download;

		return array($last_form, $last_visit, $last_download);
	}


	function get_and_send_due_fum()
	{
		$cron_guard = 'cron-'.substr(md5(gethostname().PAPOO_ABS_PFAD.'$cronjob'), 0, 6);
		$allowed = ($this->checked->guard_code === $cron_guard);

		$this->content->template['cron_guard'] = $cron_guard;

		if (!$allowed)
		{
			return false;
		}

		$sql = sprintf("SELECT
							t2 . form_manager_content_lead_id_id AS lead_id,
							t1 . lookup_lead_fum_timestamp AS datum,
							t1 . lookup_lead_fum_sentstamp AS sent,
							t2 . form_manager_content_lead_feld_name AS feldname,
							t2 . form_manager_content_lead_feld_content AS content,
							t1 . lookup_lead_fum_fum_id AS fum_id,
							t4 . leadtracker_versand_nach AS duetime,
							t4 . leadtracker_betreff_fum AS mail_betreff,
							t4 . leadtracker_mail_inhalt_text AS mail_text,
							t4 . leadtracker_mail_inhalt_html AS mail_html,
							t4 . leadtracker_fum_form_id AS form_id
						FROM
							%s AS t1,
							%s AS t2,
							%s AS t3,
							%s AS t4
						WHERE
							t1 . lookup_lead_fum_lead_id = t2 . form_manager_content_lead_id_id
							AND t2 . form_manager_content_lead_feld_name = t3 . plugin_cform_name
							AND t3 . plugin_cform_type = 'email'
							AND t1 . lookup_lead_fum_fum_id = t4 . leadtracker_fum_id
							GROUP BY t1 . lookup_lead_fum_id",
						$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
						$this->cms->tbname['papoo_form_manager_lead_content'],
						$this->cms->tbname['papoo_plugin_cform'],
						$this->cms->tbname['plugin_leadtracker_follow_up_mails']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result)
		{
		}
		foreach ($result as $key => $row)
		{
			if($row['sent'] === NULL)
			{
				$now = time();
				$sendtime = (int) strtotime($row['datum']) + (int)$row['duetime']*24*3600;
				$dateformat = "d. m. y";
				$diff = $now - $sendtime;
				if($now > $sendtime)
				{
					$texts['mail_betreff'] = $row['mail_betreff'];
					$texts['mail_text'] = $row['mail_text'];
					$texts['mail_html'] = $row['mail_html'];
					$texts = $this->replace_field_vars($texts, (int)$row['form_id'], (int)$row['lead_id']);
					$mailinfo['subject'] = $texts['mail_betreff'];
					$mailinfo['content_text'] = $texts['mail_text'];
					$mailinfo['content_html'] = $texts['mail_html'];
					$mailinfo['to_address'] = $row['content'];
					if($this->send_mail($mailinfo)) {
						$this->set_sent((int)$row['lead_id'], (int)$row['fum_id']);
					}
				}
			}
			else
			{
				continue;
			}
		}
	}

	function send_mail($mail_data)
	{
		$sender = $this->cms->admin_email;
		$this->mail_it->to = $mail_data['to_address'];
		$this->mail_it->from = $sender;
		$this->mail_it->from_text = $sender;
		$this->mail_it->subject = $mail_data['subject'];
		$this->mail_it->Encoding = '8bit';
		$this->mail_it->body_html = $mail_data['content_html'];
		$this->mail_it->body = $mail_data['content_text'];
		// Mail senden
        $result = $this->mail_it->do_mail();
        if ($result == 'ok')
            return true;
        else
            return false;
	}

	function set_sent($leadid, $fumid)
	{
		$sql = sprintf("UPDATE %s
						SET lookup_lead_fum_sentstamp = CURRENT_TIMESTAMP
						WHERE lookup_lead_fum_lead_id = %d
							AND lookup_lead_fum_fum_id = %d",
						$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
						$leadid,
						$fumid
		);
		$this->db->query($sql);
	}

	private function replace_field_vars($contents, $form_id, $lead_id)
	{
		$sql = sprintf("SELECT t1 . plugin_cform_name AS fieldname,
							t2 . plugin_cform_label AS label
						FROM
							%s AS t1,
							%s AS t2
						WHERE t2 . plugin_cform_lang_id = t1 . plugin_cform_id
							AND t1 . plugin_cform_form_id = %d",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$form_id
		);
		$results = $this->db->get_results($sql, ARRAY_A);
		foreach ($results as $row)
		{
			$sql = sprintf("SELECT `form_manager_content_lead_feld_content` AS content
							FROM `%s`
							WHERE `form_manager_content_lead_feld_name` = '%s'
							AND `form_manager_content_lead_id_id` = %d",
							$this->cms->tbname['papoo_form_manager_lead_content'],
							$row['fieldname'],
							$lead_id
			);
			$temp = $this->db->get_results($sql, ARRAY_A);
			$fieldcontent = $temp[0]['content'];
			$valuestring = $row['fieldname'];
			$valuestring = preg_replace("/^\s*/", "", $valuestring);
			$valuestring = preg_replace("/\s*$/", "", $valuestring);
			if(strlen($valuestring) > 33)
			{
				$valuestring = substr($valuestring, 0, 33);
				$valuestring = $valuestring . "...";
			}
			$valuestring = "#" . $valuestring . "#";
			foreach($contents as $key => $value)
			{
				$contents[$key] = str_ireplace($valuestring, $fieldcontent, $value);
			}
		}
		return $contents;
	}

	function make_followup_list($use_weiter = 10)
	{
		$this->content->template['template'] = 'leadtracker/templates/leadtracker_follow_up.html';
		if($this->checked->delete)
		{
			$fum = (int) $this->checked->fum;
			if($fum) {
				$sql = sprintf("DELETE FROM %s WHERE lookup_lead_fum_id = %d",
					$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
					$fum
				);
				if ($this->db->query($sql)){}
				else
					return false;
			}
		}
		if($this->checked->post_del) {
			$sql = sprintf("SELECT lookup_lead_fum_id FROM %s WHERE lookup_lead_fum_id = %d",
				$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
				(int)$this->checked->fum
			);
			$result=$this->db->get_results($sql);
			if(!$result)
				$this->content->template['isdone'] = true;
			else
			{
				$this->content->template['itfailed'] = true;
			}
		}
		$searched = "";
		$tmpl_string = "";
		$countsql = "";
		if($this->checked->search_mail)
		{
			$searched = "AND 1t . mailaddr LIKE '%" . $this->checked->search_mail . "%'";
			$this->content->template['search_mail'] = $this->checked->search_mail;
			$tmpl_string = $tmpl_string . "search_mail=" . $this->checked->search_mail;
		}
		$sql = sprintf("SELECT
							t1 . lookup_lead_fum_id AS fum_id,
							t2 . form_manager_content_lead_feld_content AS mailaddr,
							t6 . form_manager_name AS formname,
							t4 . leadtracker_betreff_fum AS mailname,
							t4 . leadtracker_versand_nach AS duetime,
							t1 . lookup_lead_fum_timestamp AS fumtimestamp,
							t1 . lookup_lead_fum_sentstamp AS issent
						FROM
							%s AS t1,
							%s AS t2,
							%s AS t3,
							%s AS t4,
							%s as t5,
							%s as t6
						WHERE
							t1 . lookup_lead_fum_lead_id = t2 . form_manager_content_lead_id_id
							AND t2 . form_manager_content_lead_feld_name = t3 . plugin_cform_name
							AND t5 . form_manager_lead_id = t2 . form_manager_content_lead_id_id
							AND t6 . form_manager_id = t5 . form_manager_form_id
							AND t3 . plugin_cform_type = 'email'
							AND t1 . lookup_lead_fum_fum_id = t4 . leadtracker_fum_id
							GROUP BY t1 . lookup_lead_fum_id",
							$this->cms->tbname['plugin_leadtracker_lookup_lead_fum'],
							$this->cms->tbname['papoo_form_manager_lead_content'],
							$this->cms->tbname['papoo_plugin_cform'],
							$this->cms->tbname['plugin_leadtracker_follow_up_mails'],
							$this->cms->tbname['papoo_form_manager_leads'],
							$this->cms->tbname['papoo_form_manager']
		);
		$sql = sprintf("SELECT * FROM (%s) AS 1t
						WHERE TRUE
						%s
						%s",
						$sql,
						$searched,
						($use_weiter)?$this->weiter->sqllimit:''
		);
		$countsql = $sql;
		$result = $this->db->get_results($sql, ARRAY_A);
		$dateformat = "d. m. y";
		foreach ($result as $key => $row)
		{
			if($row['issent'] === NULL) {
				$sendtimeint = strtotime($row['fumtimestamp']) + (int)$row['duetime'] * 24 * 3600;
				$sendtime = date($dateformat, $sendtimeint);
				$this->content->template['followups'][$key]['mailaddr'] = $row['mailaddr'];
				$this->content->template['followups'][$key]['formname'] = $row['formname'];
				$this->content->template['followups'][$key]['mailname'] = $row['mailname'];
				$this->content->template['followups'][$key]['sendtime'] = $sendtime;
				$this->content->template['followups'][$key]['fum_id'] = $row['fum_id'];
			}
		}
		$this->weiter->weiter_link = htmlspecialchars(
			$this->content->template['template_base_path'].'leadtracker_follow_up.html&'.$tmpl_string);
		if ($use_weiter)
		{
			$sql = sprintf("SELECT COUNT(*) FROM (%s) AS s1",
				$countsql
			);
			$this->weiter->result_anzahl = (int)$this->db->get_var($sql);
			$this->weiter->make_limit($use_weiter);
			$this->weiter->do_weiter("teaser");
		}
	}
}

$leadtracker_class = new leadtracker_class();

?>