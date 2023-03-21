<?php
require_once PAPOO_ABS_PFAD . '/plugins/newsletter/lib/newsletterplugin_message_class.php';
require_once PAPOO_ABS_PFAD . '/plugins/newsletter/lib/newsletterplugin_pop3_class.php';
require_once PAPOO_ABS_PFAD . '/plugins/newsletter/messages/addon_messages_backend_de.inc.php';
require_once PAPOO_ABS_PFAD . '/plugins/newsletter/messages/addon_messages_frontend_de.inc.php';

/**
 * Class newsletter_addon
 */
#[AllowDynamicProperties]
class newsletter_addon
{
	var $_pop3;
	var $_html_mail;

	function __construct()
	{
		global $cms, $db, $message, $user, $weiter, $content, $searcher, $checked, $db_praefix, $intern_stamm;
		require_once (PAPOO_ABS_PFAD . "/lib/classes/third-party/class.phpmailer.php");
		require_once (PAPOO_ABS_PFAD . "/lib/classes/mail_it_class.php");
		$mail_it = new mail_it();
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;

		$this->papoo_newsletter = $db_praefix . "papoo_newsletter";
		$this->papoo_news_impressum = $db_praefix . "papoo_news_impressum";
		$this->papoo_news_user = $db_praefix . "papoo_news_user";

		$this->checkNewsletterAddon();
	}

	function post_papoo()
	{
		if(isset($this->checked->template)) {
			switch ($this->checked->template) {
			case 'newsletter/templates/addon_checkHardBounce.html':
				$this->checkHardBounce();
				break;
			case 'newsletter/templates/newsimp.html':
				$this->make_imprint();
				break;
			}
		}
	}

	/**
	 * @param $impressum
	 * @param $impressum_html
	 * @return bool|false|mixed|string|void
	 */
	function send_news_make_html_mail($impressum, $impressum_html)
	{
		$html_mail = '';
		$template_file = PAPOO_ABS_PFAD . 'plugins/newsletter/templates/addon_newsletter.template.utf8.html';

		if (file_exists($template_file)) {
			require_once (PAPOO_ABS_PFAD . "/lib/smarty/Smarty.class.php");
			$smarty_newsletter = new Smarty;
			$smarty_newsletter->assign('betreff', $_SESSION['nl']['news_betreff']);
			$smarty_newsletter->assign('content', $_SESSION['nl']['news_inhalt_html']);

			$url = '';

			if (!empty($this->cms->title_send)) {
				$url = $this->cms->title_send . PAPOO_WEB_PFAD;
				if (!stristr( $url,'http:')) { $url = 'http://' . $url; }
			}
			$smarty_newsletter->assign('url', $url);

			$tmp_impressum = nl2br($impressum);

			if (!empty($impressum_html)) {
				$tmp_impressum = $impressum_html;
			}

			$smarty_newsletter->assign('impressum', $tmp_impressum);
			$html_mail = $smarty_newsletter->fetch($template_file);
		}
		$this->_html_mail = $html_mail;
		return $html_mail;
	}

	function send_news_get_html_mail()
	{
		return $this->_html_mail;
	}

	/**
	 * @param $seitenurl
	 * @param $key
	 */
	function send_news_make_imprint($seitenurl, $key)
	{
		#$imptext = str_replace('seitenurl', $seitenurl, $this->content->template['plugin']['newsletter_addon']['news_imptext']);
		$this->content->template['plugin']['newsletter_addon']['news_imptext'] =
			sprintf($this->content->template['plugin']['newsletter_addon']['news_imptext'], $key);
		$this->_html_mail = str_replace('#Newsletter_Kuendigen#',
			$this->content->template['plugin']['newsletter_addon']['news_imptext'], $this->_html_mail);
	}

	/**
	 * @param $search
	 * @param $replace
	 */
	function send_news_mail_replace($search, $replace)
	{
		$this->_html_mail = str_replace($search, $replace, $this->_html_mail);
	}

	function make_imprint()
	{
		$this->content->template['news_addon_file'] =
			PAPOO_ABS_PFAD . '/plugins/newsletter/templates/addon_extended_newsimp.html';

		IfNotSetNull($this->checked->addon_pop3_server);
		IfNotSetNull($this->checked->addon_pop3_username);
		IfNotSetNull($this->checked->addon_pop3_password);
		IfNotSetNull($this->checked->addon_pop3_port);
		IfNotSetNull($this->checked->addon_check_hardbounce);
		IfNotSetNull($this->checked->addon_max_hardbounce);
		IfNotSetNull($this->checked->addon_max_hardbounce_time);

		if (!empty($this->checked->submit)) {
			$sql = sprintf("UPDATE %s SET pop3_server = '%s',
											pop3_username = '%s',
											pop3_password = '%s',
											pop3_port = '%s',
											check_hardbounce = '%s',
											max_hardbounce = '%s',
											max_hardbounce_time = '%s';",
				$this->db->escape($this->papoo_news_impressum),
				$this->db->escape($this->checked->addon_pop3_server),
				$this->db->escape($this->checked->addon_pop3_username),
				$this->db->escape($this->checked->addon_pop3_password),
				$this->db->escape($this->checked->addon_pop3_port),
				$this->db->escape($this->checked->addon_check_hardbounce),
				$this->db->escape($this->checked->addon_max_hardbounce),
				$this->db->escape($this->checked->addon_max_hardbounce_time)
			);
			$this->db->query($sql);
		}
		$sql = sprintf("SELECT * FROM %s;",
			$this->db->escape($this->papoo_news_impressum)
		);
		$newsImprint = $this->db->get_results($sql, ARRAY_A);

		if (is_array($newsImprint)) {
			$this->content->template['newsletter_addon']['pop3_server'] = $newsImprint[0]['pop3_server'];
			$this->content->template['newsletter_addon']['pop3_username'] = $newsImprint[0]['pop3_username'];
			$this->content->template['newsletter_addon']['pop3_password'] = $newsImprint[0]['pop3_password'];
			$this->content->template['newsletter_addon']['pop3_port'] = $newsImprint[0]['pop3_port'];
			$this->content->template['newsletter_addon']['check_hardbounce'] = (bool)$newsImprint[0]['check_hardbounce'];
			$this->content->template['newsletter_addon']['max_hardbounce'] = $newsImprint[0]['max_hardbounce'];
			$this->content->template['newsletter_addon']['max_hardbounce_time'] = $newsImprint[0]['max_hardbounce_time'];
		}
	}

	function checkNewsletterAddon()
	{
		// Pr�fen, ob Spalten f�r Mailconfig existieren
		$columnPop3ServerExists = false;
		$columnPop3UsernameExists = false;
		$columnPop3PasswordExists = false;
		$columnPop3PortExists = false;
		$columnCheckHardBounceExists = false;
		$columnMaxHardBounceExists = false;
		$columnMaxHardBounceTimeExists = false;

		$sql = sprintf('SHOW COLUMNS FROM %s;',
			$this->db->escape($this->papoo_news_impressum)
		);
		$columns = $this->db->get_results($sql);
		foreach ($columns as $column) {
			switch ($column->Field) {
			case 'pop3_server':
				$columnPop3ServerExists = true;
				break;
			case 'pop3_username':
				$columnPop3UsernameExists = true;
				break;
			case 'pop3_password':
				$columnPop3PasswordExists = true;
				break;
			case 'pop3_port':
				$columnPop3PortExists = true;
				break;
			case 'check_hardbounce':
				$columnCheckHardBounceExists = true;
				break;
			case 'max_hardbounce':
				$columnMaxHardBounceExists = true;
				break;
			case 'max_hardbounce_time':
				$columnMaxHardBounceTimeExists = true;
				break;
			default:
				break;
			}
		}
		if (!$columnPop3ServerExists) {
			$sql = sprintf("ALTER TABLE `%s`
									ADD `pop3_server`
									VARCHAR( 255 ) NOT NULL DEFAULT '';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnPop3UsernameExists) {
			$sql = sprintf("ALTER TABLE `%s`
									ADD `pop3_username`
									VARCHAR( 255 ) NOT NULL DEFAULT '';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnPop3PasswordExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `pop3_password`
									VARCHAR( 255 ) NOT NULL DEFAULT '';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnPop3PortExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `pop3_port`
									INT( 5 ) NOT NULL DEFAULT '110';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnCheckHardBounceExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `check_hardbounce`
									INT( 1 ) NOT NULL DEFAULT '0';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnMaxHardBounceExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `max_hardbounce`
									INT( 2 ) NOT NULL DEFAULT '3';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}
		if (!$columnMaxHardBounceTimeExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `max_hardbounce_time`
									INT( 3 ) NOT NULL DEFAULT '14';",
				$this->db->escape($this->papoo_news_impressum)
			);
			$this->db->query($sql);
		}

		// Pr�fen, ob in der Newsletter Usertabelle Spalten f�r Hardbounce existieren 
		$columnHardBounceExists = false;
		$columnHardBounceTimeExists = false;

		$sql = sprintf('SHOW COLUMNS FROM %s;',
			$this->db->escape($this->papoo_news_user)
		);
		$columns = $this->db->get_results($sql);
		if (count($columns)) {
			foreach ($columns as $column)
			{
				if ($column->Field == 'news_hardbounce') $columnHardBounceExists = true;
				if ($column->Field == 'news_hardbounce_time') $columnHardBounceTimeExists = true;
			}
		}
		if (!$columnHardBounceExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `news_hardbounce`
									INT( 2 ) NOT NULL DEFAULT '0';",
				$this->db->escape($this->papoo_news_user)
			);
			$this->db->query($sql);
		}
		if (!$columnHardBounceTimeExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `news_hardbounce_time` date NOT NULL;",
				$this->db->escape($this->papoo_news_user)
			);
			$this->db->query($sql);
		}
		// Pr�fen, ob in der Newsletter Usertabelle Spalten f�r Hardbounce existieren 
		$columnHardBounceExists = false;
		$columnHardBounceTimeExists = false;

		$sql = sprintf('SHOW COLUMNS FROM %s;',
			$this->db->escape($this->cms->tbname['papoo_user'])
		);
		$columns = $this->db->get_results($sql);
		if (count($columns)) {
			foreach ($columns as $column) {
				if ($column->Field == 'news_hardbounce') {
					$columnHardBounceExists = true;
				}
				if ($column->Field == 'news_hardbounce_time') {
					$columnHardBounceTimeExists = true;
				}
			}
		}
		if (!$columnHardBounceExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `news_hardbounce`
									INT( 2 ) NOT NULL DEFAULT '0';",
				$this->db->escape($this->cms->tbname['papoo_user'])
			);
			$this->db->query($sql);
		}
		if (!$columnHardBounceTimeExists) {
			$sql = sprintf("ALTER TABLE `%s` ADD `news_hardbounce_time` date NOT NULL;",
				$this->db->escape($this->cms->tbname['papoo_user'])
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @return bool
	 */
	function pop3Connect()
	{
		$sql = sprintf("SELECT pop3_server,
								pop3_username,
								pop3_password,
								pop3_port
							FROM %s
							WHERE pop3_server != '';",
			$this->db->escape($this->papoo_news_impressum)
		);
		$pop3Data = $this->db->get_results($sql, ARRAY_A);

		if (0 < count($pop3Data)) {
			$this->_pop3 = new newsletter_pop3($pop3Data[0]['pop3_server'],
				$pop3Data[0]['pop3_username'],
				$pop3Data[0]['pop3_password'],
				$pop3Data[0]['pop3_port']
			);
			$this->_pop3->connect();
			$this->_pop3->login();
			return true;
		}
		return false;
	}

	function pop3Close()
	{
		$this->_pop3->close();
	}

	function checkHardBounce()
	{
		$sql = sprintf("SELECT * FROM %s;",
			$this->db->escape($this->papoo_news_impressum)
		);
		$newsImprint = $this->db->get_results($sql, ARRAY_A);

		if (!is_array($newsImprint)) {
			return;
		}

		$max_hardbounce = (int)$newsImprint[0]['max_hardbounce'];
		$check_hardbounce = (bool)$newsImprint[0]['check_hardbounce'];

		if (!$check_hardbounce or 0 === $max_hardbounce) {
			return;
		}

		$mailaddresses = $this->getHardBouncedMailaddresses();

		foreach ($mailaddresses as $mailaddress) {
			// Bounce in Newsletter User Tabelle aktualisieren
			$sql = sprintf("SELECT * FROM %s
										WHERE news_user_email = '%s';",
				$this->db->escape($this->papoo_news_user),
				$this->db->escape($mailaddress)
			);
			$newsletter_user = $this->db->get_results($sql, ARRAY_A);

			if (is_array($newsletter_user))
			{
				$news_hardbounce = $newsletter_user[0]['news_hardbounce'];
				$lastBounce = explode('-', $newsletter_user[0]['news_hardbounce_time']);
				$lastBounce = mktime(0, 0, 0, $lastBounce[1], $lastBounce[2], $lastBounce[0]);

				if ($lastBounce + ($newsImprint[0]['max_hardbounce_time'] * 86400) <= time()) {
					$news_hardbounce = 1;
				}
				else {
					$news_hardbounce++;
				}
				$sql = sprintf("UPDATE %s SET   news_hardbounce = '%s',
                                    			news_hardbounce_time = '%s'
                                			WHERE news_user_id = '%s'
                                			AND news_active = '1';",
					$this->db->escape($this->papoo_news_user),
					$this->db->escape($news_hardbounce),
					date('Y-m-d'),
					$this->db->escape($newsletter_user[0]['news_user_id'])
				);
				$this->db->query($sql);
			}

			// Bounce in Papoo User Tabelle aktualisieren
			$sql = sprintf("SELECT * FROM %s WHERE email = '%s';",
				$this->db->escape($this->cms->tbname['papoo_user']),
				$this->db->escape($mailaddress)
			);
			$papoo_user = $this->db->get_results($sql, ARRAY_A);
			if (is_array($papoo_user)) {
				$news_hardbounce = $papoo_user[0]['news_hardbounce'];
				$lastBounce = explode('-', $papoo_user[0]['news_hardbounce_time']);
				$lastBounce = mktime(0, 0, 0, $lastBounce[1], $lastBounce[2], $lastBounce[0]);

				if ($lastBounce + ($newsImprint[0]['max_hardbounce_time'] * 86400) <= time()) {
					$news_hardbounce = 1;
				}
				else {
					$news_hardbounce++;
				}
				$sql = sprintf("UPDATE %s SET   news_hardbounce = '%s',
                                    			news_hardbounce_time = '%s'
                                			WHERE userid = '%s'
                                			AND user_newsletter = 'ok';",
					$this->db->escape($this->cms->tbname['papoo_user']),
					$this->db->escape($news_hardbounce),
					date('Y-m-d'),
					$this->db->escape($papoo_user[0]['userid'])
				);
				$this->db->query($sql);
			}
		}
		$sql = sprintf("UPDATE %s SET news_active = '0',
										news_hardbounce = '0',
										news_hardbounce_time = '0000-00-00'
									WHERE news_hardbounce >= %s;",
			$this->db->escape($this->papoo_news_user),
			$this->db->escape($max_hardbounce)
		);
		$this->db->query($sql);
		$sql = sprintf("UPDATE %s SET user_newsletter = '0',
										news_hardbounce = '0',
										news_hardbounce_time = '0000-00-00'
									WHERE news_hardbounce >= %s;",
			$this->db->escape($this->cms->tbname['papoo_user']),
			$this->db->escape($max_hardbounce)
		);
		$this->db->query($sql);
	}

	/**
	 * @return array
	 */
	function getHardBouncedMailaddresses()
	{
		$bouncedMailaddresses = array();
		if ($this->pop3Connect()) {
			$messages = $this->_pop3->getMessages();
			foreach ($messages as $messageId => $message) {
				if (!stristr( $message->getHeader('from'),'.*Mailer-Daemon.*')) {
					continue;
				}
				$headers = $message->getHeaders();

				foreach ($headers as $header) {
					if (stristr( $header,'^x-failed')) {
						// Header auf Empf�nger pr�fen
						$mailaddresses = explode(',', $header);
						foreach ($mailaddresses as $mailaddress) {
							$mailaddress = trim($mailaddress);
							if (stristr( $mailaddress,'.*@.*')) {
								$bouncedMailaddresses[] = $mailaddress;
							}
						}
					}
				}
				$body = '';
				if (!$message->hasParts()) {
					$body = $message->getBody();
				}
				$body = explode("\n", $body);

				foreach ($body as $line) {
					$line = strtolower($line);
					if (stristr( $line,'.*copy of the message.*')) {
						break;
					}
					$line = trim(str_replace(array(
							'<',
							'>',
							':',
						), '', $line)
					);
					if (stristr( $line,'.*@.*')) {
						$bouncedMailaddresses[] = $line;
					}
				}
				$this->_pop3->deleteMessage($messageId);
			}
			$this->pop3Close();
		}
		return $bouncedMailaddresses;
	}
}