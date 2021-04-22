<?php
if ( stristr( $_SERVER['PHP_SELF'], 'googlekalenderplugin_class.php' ) ) die( 'You are not allowed to see this page directly' );

/**
 * Google-Kalender-Plugin-Hauptklasse
 * @package Papoo
 * @author Papoo Software
 * @copyright 2017
 * @version $Id$
 * @access public
 */
class googlekalenderplugin_class
{
	/**
	 * googlekalenderplugin_class constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $cms, $db_abs, $db, $template;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;

		if ( defined("admin") ) {
			$this->user->check_intern();
			if ( strpos( "XXX" . $template, "google_kalender_back_cal.html" ) ) {
				require_once __DIR__ . '/vendor/autoload.php';
				//Einstellungen überabeiten
				$this->start_cal_admin();
			}
		}
		else {
			if ( strpos( "XXX" . $template, "google_kalender/templates/" ) ) {
				require_once __DIR__ . '/vendor/autoload.php';
				require_once PAPOO_ABS_PFAD."/plugins/google_kalender/lib/class_gcal_front.php";
				$class_gcal_front->get_kalender_front();
			}
		}

	}

	/**
	 * Returns an authorized API client.
	 *
	 * @param array $cal
	 * @return Google_Client
	 */
	function getClient($cal)
	{
		$id = $cal['kalender_id'];

		$client = new Google_Client();
		$client->setApplicationName('Papoo Google-Kalender-Plugin');
		$client->setScopes(Google_Service_Calendar::CALENDAR);
		$client->setAuthConfig(__DIR__.'/client_id.json');
		$client->setAccessType('offline');
		$client->setPrompt('consent');

		$https = !empty($_SERVER['HTTPS']);
		$url = ($https?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$client->setState(bin2hex($url));
		$client->setRedirectUri('https://www.papoo.de/google-oauth/kalender_callback.php');

		if ($cal['kalender_access_token']) {
			$client->setAccessToken(json_decode($cal['kalender_access_token'], true));
			if (!empty($client->getAccessToken()['refresh_token'])) {
				$client->setPrompt('auto');
			}
		}
		elseif (!empty($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$this->_update_table('plugin_google_kalender', ['kalender_id'=>$id],
				['kalender_access_token'],
				['kalender_access_token'=>json_encode($client->getAccessToken())]
			);
		}
		else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			header('HTTP/1.1 307 Temporary Redirect');
			header('Location: '.filter_var($authUrl, FILTER_SANITIZE_URL));
			echo '<a href="'.htmlspecialchars($authUrl).'" rel="next">Weiter</a>';
			exit();
		}

		// Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			$this->_update_table('plugin_google_kalender', ['kalender_id'=>$id],
				['kalender_access_token'],
				['kalender_access_token'=>json_encode($client->getAccessToken())]
			);
		}
		return $client;
	}

	/**
	 * Bestimmt die Kalender-ID aus der Benutzereingabe (falls embed-URL etc. angegeben wurde)
	 *
	 * @param string $user_input
	 * @return string
	 */
	private function clean_calendar_id($user_input)
	{
		$match = array();
		if (preg_match('%^https://calendar.google.com/calendar/ical/([^/]*)/.*\.ics%i', $user_input, $match)) {
			return urldecode($match[1]);
		}
		if (preg_match('%^https?://calendar.google.com/calendar/embed\?src=([^&]*)(?:&|$)%i', $user_input, $match)) {
			return urldecode($match[1]);
		}
		return $user_input;
	}

	/**
	 * Setzt die google_access-Template-Variable und gibt zurück
	 * ob die Kalender-ID valide ist.
	 *
	 * @param string $google_cal_id
	 * @param string $tz
	 * @return bool true bei Erfolg, sonst false
	 */
	private function check_cal_access($google_cal_id, $tz='Europe/Berlin')
	{
		$url = 'https://calendar.google.com/calendar/embed?src='.urlencode($google_cal_id).'&ctz='.urlencode($tz);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux) Papoo/".PAPOO_VERSION." (Google-Kalender-Plugin)");
		curl_exec($ch);

		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($code == 302 or $code == 307 or $code == 403 or $code == 401) {
			$this->content->template['google_access'] = 'private';
			return true;
		}
		elseif ($code == 404) {
			$this->content->template['google_access'] = 'no_calendar';
			return false;
		}
		else {
			$this->content->template['google_access'] = 'ok';
			return true;
		}
	}

	/**
	 * googlekalenderplugin_class::start_cal_admin()
	 * Kalenderadministration im Backend
	 *
	 * @return void
	 */
	function start_cal_admin()
	{
		//Link erzeugen
		$this->create_self_link();

		//Wenn vorhanden Messages ausgeben
		$this->create_pcalender_message();

		//Es soll gelöscht werden
		if ($this->checked->cal_act=="delete" || !empty($_POST['formSubmit_delete_pcal'])) {
			$this->delete_pcal($this->checked->pcal_id);
		}
		//Normal weiter
		else {
			//Neuer Eintrag
			if ($this->checked->pcal_id=="new") {
				$this->create_new_calender_entry();
			}
			//Alter Eintrag
			if (is_numeric($this->checked->pcal_id)) {
				$this->change_pcalender_entry($this->checked->pcal_id);
			}
			//Liste anzeigen
			if (empty($this->checked->pcal_id)) {
				$this->content->template['pcal_liste'] = $this->get_pcal_liste();
			}
		}
	}

	/**
	 * googlekalenderplugin_class::delete_pcal()
	 * Kalender löschen
	 *
	 * @param int $id
	 * @return void
	 */
	function delete_pcal($id)
	{
		//Daten rausholen nach Sprache für Liste
		$this->content->template['kalender']=$this->get_pcal($id);

		if (!$this->content->template['kalender']['kalender_can_write']) {
			return;
		}

		if (!empty($_POST['formSubmit_delete_pcal'])) {
			$sql=sprintf("DELETE FROM %s WHERE kalender_id = %d",
				$this->cms->tbname['plugin_google_kalender_lang'],
				(int)$id
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE FROM %s WHERE kalender_id = %d",
				$this->cms->tbname['plugin_google_kalender_lookup_read'],
				(int)$id
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE FROM %s WHERE kalender_id = %d",
				$this->cms->tbname['plugin_google_kalender_lookup_write'],
				(int)$id
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE FROM %s WHERE kalender_id = %d",
				$this->cms->tbname['plugin_google_kalender'],
				(int)$id
			);
			$this->db->query($sql);
			$this->reload("","del");
		}

		$this->content->template['kalender_del']="ok";
	}

	/**
	 * googlekalenderplugin_class::get_pcal_liste()
	 *
	 * @return array[]
	 */
	private function get_pcal_liste()
	{
		$groups = array_map(function ($x) { return (int)$x['gruppenid']; }, $this->user->get_groups());
		//Daten nach Sprache und Rechten
		$sql=sprintf('SELECT
						`k`.*,
						`kl`.`kalender_name`,
						`kl`.`kalender_text_above`,
						(EXISTS (SELECT * FROM %4$s AS `gr` WHERE `gr`.`kalender_id` = `k`.`kalender_id` AND `gr`.`gruppeid` IN (%6$s)) OR 1 IN (%6$s)) AS `kalender_can_read`,
						(EXISTS (SELECT * FROM %3$s AS `gw` WHERE `gw`.`kalender_id` = `k`.`kalender_id` AND `gw`.`gruppeid` IN (%6$s)) OR 1 IN (%6$s)) AS `kalender_can_write`
					FROM %1$s AS `k`
						NATURAL LEFT JOIN %2$s AS `kl`
					WHERE kalender_lang_id=%5$d OR kalender_lang_id IS NULL',
			$this->cms->tbname['plugin_google_kalender'],
			$this->cms->tbname['plugin_google_kalender_lang'],
			$this->cms->tbname['plugin_google_kalender_lookup_write'],
			$this->cms->tbname['plugin_google_kalender_lookup_read'],
			$this->cms->lang_back_content_id,
			implode(',', $groups)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (!$result) {
			$result = [];
		}
		return $result;
	}

	/**
	 * googlekalenderplugin_class::get_pcal()
	 *
	 * @param int $id
	 * @return array
	 */
	private function get_pcal($id)
	{
		$groups = array_map(function ($x) { return (int)$x['gruppenid']; }, $this->user->get_groups());
		//Daten nach Sprache und Rechten
		$sql=sprintf('SELECT
						`k`.*,
						`kl`.`kalender_name`,
						`kl`.`kalender_text_above`,
						(EXISTS (SELECT * FROM %4$s AS `gr` WHERE `gr`.`kalender_id` = `k`.`kalender_id` AND `gr`.`gruppeid` IN (%6$s)) OR 1 IN (%6$s)) AS `kalender_can_read`,
						(EXISTS (SELECT * FROM %3$s AS `gw` WHERE `gw`.`kalender_id` = `k`.`kalender_id` AND `gw`.`gruppeid` IN (%6$s)) OR 1 IN (%6$s)) AS `kalender_can_write`
					FROM %1$s AS `k`
						NATURAL LEFT JOIN %2$s AS `kl`
					WHERE (kalender_lang_id=%5$d OR kalender_lang_id IS NULL) AND kalender_id=%7$d
					LIMIT 1',
			$this->cms->tbname['plugin_google_kalender'],
			$this->cms->tbname['plugin_google_kalender_lang'],
			$this->cms->tbname['plugin_google_kalender_lookup_write'],
			$this->cms->tbname['plugin_google_kalender_lookup_read'],
			$this->cms->lang_back_content_id,
			implode(',', $groups),
			$id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (!$result) {
			return null;
		}
		else {
			if (substr(trim($result[0]['kalender_google_id']), 0, 1) === '#') {
				$result[0]['kalender_read_only'] = true;
			}
			if (!empty($result[0]['kalender_text_above'])) {
				$result[0]['kalender_text_above'] = 'nobr:'.$result[0]['kalender_text_above'];
			}
			return $result[0];
		}
	}

	/**
	 * googlekalenderplugin_class::create_self_link()
	 *
	 * @return void
	 */
	private function create_self_link()
	{
		$this->content->template['pkal_self1']="./plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&pcal_id=";
		$this->content->template['pkal_self2']="./plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."";
	}

	/**
	 * googlekalenderplugin_class::create_new_calender_entry()
	 *
	 * @return void
	 */
	private function create_new_calender_entry()
	{
		//Zuerst einen Eintrag anlegen
		$this->content->template['edit_pcal_entry'] = true;

		//Rechte zuweisen
		$this->get_user_gruppen();

		if (!empty($_POST['formSubmit_save_pcal'])) {
			$cal = [];
			foreach ($_POST as $key=>$val) {
				if (substr($key, 0, 9) == 'kalender_') {
					if ($key == 'kalender_text_above') {
						$cal[$key] = 'nobr:'.$val;
					}
					else {
						$cal[$key] = $val;
					}
				}
			}
			$this->content->template['kalender'] = $cal;

			$_POST['kalender_google_id'] = $this->clean_calendar_id($_POST['kalender_google_id']);

			$success = $this->check_cal_access($_POST['kalender_google_id'], $_POST['kalender_timezone']);
			if (!$success) {
				return;
			}

			$data = $_POST;
			$data['kalender_send_email'] = !empty($data['kalender_send_email']);
			$data['kalender_id'] = $this->_insert_table('plugin_google_kalender', ['kalender_google_id', 'kalender_timezone', 'kalender_send_email', 'kalender_info_email'], $data);

			if (!$data['kalender_id']) {
				$this->content->template['is_eingetragen'] = 'no';
				return;
			}
			$data['kalender_lang_id'] = $this->cms->lang_back_content_id;
			$this->_insert_table('plugin_google_kalender_lang', ['kalender_id', 'kalender_lang_id', 'kalender_name', 'kalender_text_above'], $data);

			//Rechte setzen
			$this->pcal_save_cal_rights($data['kalender_id']);

			$this->reload("","saved");
		}
	}

	/**
	 * @param $table
	 * @param $allowed_columns
	 * @param $data
	 * @return mixed|null
	 */
	protected function _insert_table($table, $allowed_columns, $data) {
		$insert_values = array();
		$insert_columns = array();
		foreach ($allowed_columns as $col) {
			if (isset($data[$col])) {
				$insert_columns[] = $col;
				if ($data[$col] === null) {
					$valuestr = 'NULL';
				}
				elseif (is_bool($data[$col])) {
					$valuestr = ($data[$col] ? 'TRUE' : 'FALSE');
				}
				elseif (is_int($data[$col])) {
					$valuestr = (string)$data[$col];
				}
				elseif ($data[$col] === 'CURRENT_TIMESTAMP()') {
					$valuestr = 'CURRENT_TIMESTAMP()';
				}
				else {
					$valuestr = '\''.$this->db->escape((string)$data[$col]).'\'';
				}
				$insert_values[] = $valuestr;
			}
			//else {}//$insert_values[] = 'DEFAULT';
		}

		$sql = sprintf('INSERT INTO %s (`%s`) VALUES (%s)',
			$this->cms->tbname[$table],
			implode('`, `', $insert_columns),
			implode(', ', $insert_values));
		if ($this->db->query($sql)) {
			return $this->db->insert_id;
		}
		else {
			return null;
		}
	}

	/**
	 * @param $table
	 * @param $idcols
	 * @param $allowed_columns
	 * @param $data
	 * @param bool $orinsert
	 * @return bool|int|mixed|mysqli_result|void
	 */
	protected function _update_table($table, $idcols, $allowed_columns, $data, $orinsert=false) {
		$values = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $allowed_columns)) {
				if ($value === null) {
					$valuestr = 'NULL';
				}
				elseif (is_bool($value)) {
					$valuestr = ($value ? 'TRUE' : 'FALSE');
				}
				elseif (is_int($value)) {
					$valuestr = (string)$value;
				}
				elseif ($value === 'CURRENT_TIMESTAMP()') {
					$valuestr = $value;
				}
				else {
					$valuestr = '\''.$this->db->escape((string)$value).'\'';
				}
				$values[] = '`'.$this->db->escape($key).'` = '.$valuestr;
			}
		}
		if (!is_int($id)) {
			$id = '\''.$this->db->escape((string)$id).'\'';
		}

		if (!$orinsert) {
			$where = [];
			foreach ($idcols as $col=>$val) {
				$where[] = '(`'.$col.'` = \''.$this->db->escape((string)$val).'\')';
			}
			$sql = sprintf('UPDATE %s SET %s WHERE %s LIMIT 1',
				$this->cms->tbname[$table],
				implode(', ', $values), implode(' AND ', $where));
		}
		else {
			$insert_values = array_values($idcols);
			$insert_columns = array();
			foreach ($allowed_columns as $col) {
				if (isset($data[$col])) {
					$insert_columns[] = $col;
					if ($data[$col] === null) {
						$valuestr = 'NULL';
					}
					elseif (is_bool($data[$col])) {
						$valuestr = ($data[$col] ? 'TRUE' : 'FALSE');
					}
					elseif (is_int($data[$col])) {
						$valuestr = (string)$data[$col];
					}
					elseif ($data[$col] === 'CURRENT_TIMESTAMP()') {
						$valuestr = 'CURRENT_TIMESTAMP()';
					}
					else {
						$valuestr = '\''.$this->db->escape((string)$data[$col]).'\'';
					}
					$insert_values[] = $valuestr;
				}
				//else {}//$insert_values[] = 'DEFAULT';
			}
			$sql = sprintf('INSERT INTO %s (`%s`, `%s`) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
				$this->cms->tbname[$table],
				implode('`, `', array_keys($idcols)),
				implode('`, `', $insert_columns),
				implode(', ', $insert_values),  implode(', ', $values));
		}
		return $this->db->query($sql);
	}

	/**
	 * googlekalenderplugin_class::change_pcalender_entry()
	 * Einen Kalender kann man hier bearbeiten
	 *
	 * @param int $id
	 * @return void
	 */
	private function change_pcalender_entry($id)
	{
		$kalender=$this->get_pcal($id);

		$this->get_user_gruppen($id);
		$this->content->template['kalender'] = $kalender;

		if ($kalender['kalender_can_read']) {
			$this->content->template['edit_pcal_entry']="OK";

			if ($kalender['kalender_can_write']) {
				if (isset($_GET['code'])) {
					$this->getClient($kalender);
					$https = !empty($_SERVER['HTTPS']);
					$url = ($https?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					$url = preg_replace('/&code=[^&]*/', '', $url);
					header('HTTP/1.1 303 See Other');
					header('Location: '.filter_var($url, FILTER_SANITIZE_URL));
					echo '<a href="'.htmlspecialchars($url).'" rel="next">Weiter</a>';
					exit();
				}
				if (!empty($_POST['formSubmit_save_pcal'])) {
					$_POST['kalender_google_id'] = $this->clean_calendar_id($_POST['kalender_google_id']);

					$success = $this->check_cal_access($_POST['kalender_google_id'], $_POST['kalender_timezone']);
					if (!$success) {
						return;
					}

					if (empty($_POST['kalender_send_email'])) {
						$_POST['kalender_send_email']=0;
					}

					$this->_update_table('plugin_google_kalender', ['kalender_id'=>$id],
						['kalender_google_id', 'kalender_timezone', 'kalender_send_email', 'kalender_info_email'],
						$_POST
					);
					$this->_update_table('plugin_google_kalender_lang', ['kalender_id'=>$id, 'kalender_lang_id'=>$this->cms->lang_back_content_id],
						['kalender_name', 'kalender_text_above'],
						$_POST,
						true
					);

					//Rechte setzen
					$this->pcal_save_cal_rights($id);

					if ($_POST['formSubmit_save_pcal'] == 'connect_oauth') {
						$this->getClient($kalender);
					}
					elseif ($_POST['formSubmit_save_pcal'] == 'disconnect_oauth') {
						$client = $this->getClient($kalender);
						$client->revokeToken();
						$this->_update_table('plugin_google_kalender', ['kalender_id'=>$id],
							['kalender_access_token'], ['kalender_access_token'=>null]
						);
					}
					else {
						$this->reload("","saved");
					};
				}
			}
			//Rechte zuweisen
			$this->get_user_gruppen($id);
			$this->content->template['kalender'] = $kalender;
		}
	}

	/**
	 * googlekalenderplugin_class::pcal_save_cal_rights()
	 *
	 * @param int $id
	 * @return void
	 */
	private function pcal_save_cal_rights($id)
	{
		$id = (int)$id;
		$read_groups = [];
		$read_group_inserts = [];
		if (!empty($_POST['pcal_gruppe_lese'])) {
			foreach ($_POST['pcal_gruppe_lese'] as $gid) {
				if (is_int($gid) or ctype_digit($gid)) {
					$read_groups[] = (int)$gid;
					$read_group_inserts[] = sprintf('(%d, %d)', $id, $gid);
				}
			}
		}
		$write_groups = [];
		$write_group_inserts = [];
		if (!empty($_POST['pcal_gruppe_write'])) {
			foreach ($_POST['pcal_gruppe_write'] as $gid) {
				if (is_int($gid) or ctype_digit($gid)) {
					$write_groups[] = (int)$gid;
					$write_group_inserts[] = sprintf('(%d, %d)', $id, $gid);
				}
			}
		}

		//Zuerst die alten Einträge löschen
		$sql=sprintf("DELETE FROM %s
						WHERE kalender_id=%d AND gruppeid NOT IN (%s)",
			$this->cms->tbname['plugin_google_kalender_lookup_read'],
			$this->db->escape($this->checked->pcal_id),
			($read_groups) ? implode(',', $read_groups) : '0'
		);
		$this->db->query($sql);

		$sql=sprintf("DELETE FROM %s
						WHERE kalender_id=%d AND gruppeid NOT IN (%s)",
			$this->cms->tbname['plugin_google_kalender_lookup_write'],
			$this->db->escape($this->checked->pcal_id),
			($write_groups) ? implode(',', $write_groups) : '0'
		);
		$this->db->query($sql);

		//Dann neu eintragen
		if ($read_group_inserts) {
			$sql=sprintf("INSERT INTO %s (`kalender_id`, `gruppeid`)
							VALUES %s
							ON DUPLICATE KEY UPDATE `gruppeid`=`gruppeid`",
				$this->cms->tbname['plugin_google_kalender_lookup_read'],
				implode(', ', $read_group_inserts)
			);
			$result=$this->db->query($sql);
		}

		if ($write_group_inserts) {
			$sql=sprintf("INSERT INTO %s (`kalender_id`, `gruppeid`)
							VALUES %s
							ON DUPLICATE KEY UPDATE `gruppeid`=`gruppeid`",
				$this->cms->tbname['plugin_google_kalender_lookup_write'],
				implode(', ', $write_group_inserts)
			);
			$result=$this->db->query($sql);
		}
	}

	/**
	 * googlekalenderplugin_class::get_user_gruppen()
	 * Die Papoo Usergruppen auslesen und zuweisen
	 *
	 * @param null $pcal_id
	 * @return void
	 */
	private function get_user_gruppen($pcal_id=null)
	{
		//Die Gruppen raussuchen
		$sql=sprintf('SELECT * FROM %s',
			$this->cms->tbname['papoo_gruppe']
		);
		$result_gr=$this->db->get_results($sql,ARRAY_A);

		//Leserechte
		if ($pcal_id === null or $pcal_id === 'new') {
			$result = [['gruppeid'=>10]];
		}
		else {
			$sql=sprintf('SELECT * FROM %s
							WHERE kalender_id=%d',
				$this->cms->tbname['plugin_google_kalender_lookup_read'],
				$this->checked->pcal_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}

		//Gruppen durchgehen und Leserechte setzen
		if ($result_gr) {
			foreach ($result_gr as &$grp) {
				$grp['lese_rights'] = false;
				if ($result) {
					foreach ($result as $kal_lookup) {
						if ($grp['gruppeid']==$kal_lookup['gruppeid']) {
							$grp['lese_rights'] = true;
						}
					}
				}
			}
			unset($grp);
		}

		//Schreibrechte
		if ($pcal_id === null or $pcal_id === 'new') {
			$result = [['gruppeid'=>1]];
		}
		else {
			$sql=sprintf('SELECT * FROM %s
							WHERE kalender_id=%d',
				$this->cms->tbname['plugin_google_kalender_lookup_write'],
				$this->checked->pcal_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}
		//Gruppen durchgehen und Leserechte setzen
		if ($result_gr) {
			foreach ($result_gr as &$grp) {
				$grp['write_rights'] = false;
				if ($result) {
					foreach ($result as $kal_lookup) {
						if ($grp['gruppeid']==$kal_lookup['gruppeid']) {
							$grp['write_rights'] = true;
						}
					}
				}
			}
			unset($grp);
		}
		$this->content->template['pcal_gruppen']=$result_gr;
	}

	/**
	 * googlekalenderplugin_class::create_pcalender_message()
	 * Hier werden Nachrichten ans Template übergeben
	 * z.B. ist Eingetragen
	 *
	 * @return void
	 */
	private function create_pcalender_message()
	{
		//message_pcalender
		if ($this->checked->message_pcalender=="saved") {
			$this->content->template['is_eingetragen']="ok";
		}
		if ($this->checked->message_pcalender=="del") {
			$this->content->template['is_eingetragen']="del";
		}
	}

	/**
	 * googlekalenderplugin_class::reload()
	 * Seite neu laden mit den gegebenen Parametern
	 *
	 * @param string $template
	 * @param string $dat
	 * @return void
	 */
	private function reload($template = "",$dat="")
	{
		$url = "menuid=" . $this->checked->menuid;

		if (!empty($template)) {
			$url .= "&template=" . $template;
		}
		else {
			$url .= "&template=google_kalender/templates/google_kalender_back_cal.html";
		}
		if (!empty($dat)) {
			$url .= "&message_pcalender=" . $dat;
		}

		$self=$_SERVER['PHP_SELF'];

		$location_url = $self . "?" . $url;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . htmlspecialchars($location_url) . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$googlekalenderplugin = new googlekalenderplugin_class();
