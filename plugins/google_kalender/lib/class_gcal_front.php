<?php

/**
 * class_gcal_front
 * @package Papoo
 * @author Papoo Software
 * @copyright 2017
 * @version $Id$
 * @access public
 */
if (stristr($_SERVER['PHP_SELF'], 'class_cal_front.php')) die('You are not allowed to see this page directly');

/**
 * Class class_gcal_front
 */
class class_gcal_front
{
	/**
	 * class_cal_front::class_cal_front()
	 * Initialisierung und Einbindung von Klassen
	 * @return void
	 */
	function __construct()
	{
		global $content, $user, $checked, $cms, $db_abs, $db, $diverse, $module;
		$this->content = &$content;
		$this->user = &$user;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->diverse = &$diverse;
		$this->module = &$module;

		if (defined('admin')) {
			$this->user->check_intern();
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

		if ($cal['kalender_access_token']) {
			$client->setAccessToken(json_decode($cal['kalender_access_token'], true));
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
	 * @param $table
	 * @param $idcols
	 * @param $allowed_columns
	 * @param $data
	 * @param bool $orinsert
	 * @return bool|int|mixed|mysqli_result|void
	 */
	protected function _update_table($table, $idcols, $allowed_columns, $data, $orinsert=false)
	{
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
						EXISTS (SELECT * FROM %4$s AS `gr` WHERE `gr`.`kalender_id` = `k`.`kalender_id` AND `gr`.`gruppeid` IN (%6$s)) AS `kalender_can_read`,
						(`kalender_access_token` IS NOT NULL AND EXISTS (SELECT * FROM %3$s AS `gw` WHERE `gw`.`kalender_id` = `k`.`kalender_id` AND `gw`.`gruppeid` IN (%6$s))) AS `kalender_can_write`
					FROM %1$s AS `k`
						NATURAL LEFT JOIN %2$s AS `kl`
					WHERE (kalender_lang_id=%5$d OR kalender_lang_id IS NULL) AND kalender_id=%7$d
					LIMIT 1',
			$this->cms->tbname['plugin_google_kalender'],
			$this->cms->tbname['plugin_google_kalender_lang'],
			$this->cms->tbname['plugin_google_kalender_lookup_write'],
			$this->cms->tbname['plugin_google_kalender_lookup_read'],
			$this->cms->lang_id,
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
				$result[0]['kalender_can_write'] = false;
			}
			if (!empty($result[0]['kalender_text_above'])) {
				$result[0]['kalender_text_above'] = 'nobr:'.$result[0]['kalender_text_above'];
			}
			return $result[0];
		}
	}

	/**
	 * @param $date_field
	 * @param $time_field
	 * @param $tz
	 * @return Google_Service_Calendar_EventDateTime
	 */
	private function _get_post_datetime($date_field, $time_field, $tz)
	{
		$result = new Google_Service_Calendar_EventDateTime();
		$date = new DateTime($_POST[$date_field]);
		if (isset($_POST[$time_field]) and $_POST[$time_field] !== '') {
			$timeparts = explode(':',$_POST[$time_field]);
			$date->setTime($timeparts[0], $timeparts[1]);
			$result->setDateTime($date->format('Y-m-d\TH:i:00'));
		}
		else {
			$result->setDate($date->format('Y-m-d'));
		}
		$result->setTimeZone($tz);
		return $result;
	}

	/**
	 * @param $event
	 * @return bool
	 */
	private function _validate_event($event)
	{
		// Daten validieren
		$errors = [];

		if (!$event->getSummary()) {
			$errors['gkal_summary'] = $this->content->template['plugin_google_kalender_error'];
		}

		$midnight = strtotime('today midnight');
		$start = $event->getStart();
		if ($start->getDateTime()) {
			$start = strtotime($start->getDateTime());
		}
		else {
			$start = strtotime($start->getDate());
		}
		if ($start < $midnight) {
			$errors['gkal_date_start_datum'] = $this->content->template['plugin_google_kalender_error_date'];
			$errors['gkal_date_uhrzeit_beginn'] = $this->content->template['plugin_google_kalender_error_date'];
		}
		$end = $event->getEnd();
		if ($end->getDateTime()) {
			$end = strtotime($end->getDateTime());
		}
		else {
			$end = strtotime($end->getDate());
		}
		if ($end < $start) {
			$errors['gkal_date_end_datum'] = $this->content->template['plugin_google_kalender_error_date'];
			$errors['gkal_date_uhrzeit_ende'] = $this->content->template['plugin_google_kalender_error_date'];
		}

		$this->content->template['plugin_error'] = $errors;
		return count($errors) == 0;
	}

	/**
	 * class_cal_front::get_alle_datums_eines_kalenders()
	 *
	 * @return void
	 */
	function get_kalender_front()
	{
		if (empty($this->checked->kal_id)) {
			return;
		}

		$this->content->template['kal_id'] = $this->checked->kal_id;

		$cal = $this->get_pcal($this->checked->kal_id);
		if (!$cal) {
			return;
		}

		if ($cal['kalender_can_read']) {
			$this->content->template['gkalender_daten'] = $cal;
		}
		else {
			$this->content->template['gkalender_daten'] = null;
		}

		$this->content->template['plugin_calender_view'] = 'cal';
		if ($cal['kalender_can_write']) {
			if (!empty($this->checked->cal_view) and $this->checked->cal_view == 'new') {
				$this->content->template['plugin_calender_view'] = 'new';
			}
		}

		$this->content->template['url_gcal'][$cal['kalender_id']] = $url = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $this->checked->menuid . "&amp;template=google_kalender/templates/google_kalender_front.html&amp;kal_id=";
		$this->content->template['url_gcal2'][$cal['kalender_id']] = PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $this->checked->menuid . "&amp;template=google_kalender/templates/google_kalender_front.html&amp;kal_id=";

		if ($cal['kalender_can_write']) {
			if (!empty($this->checked->is_eingetragen) and $this->checked->is_eingetragen=='yes') {
				$this->content->template['is_eingetragen'] = 'ok';
			}
			if (!empty($_POST['formSubmit_save_pcal_date'])) {
				$this->content->template['gkal_date'] = [];
				foreach ($_POST as $key=>$value) {
					if (substr($key, 0, 5) === 'gkal_' and is_string($value)) {
						$this->content->template['gkal_date'][$key] = $value;
					}
				}
				$client = $this->getClient($cal);
				$service = new Google_Service_Calendar($client);

				// Event zusammensetzen
				$event = new Google_Service_Calendar_Event();

				$start = $this->_get_post_datetime('gkal_date_start_datum', 'gkal_date_uhrzeit_beginn', $cal['kalender_timezone']);
				$event->setStart($start);

				if (empty($_POST['gkal_date_end_datum']) and !empty($_POST['gkal_date_uhrzeit_ende'])) {
					$_POST['gkal_date_end_datum'] = $_POST['gkal_date_start_datum'];
				}
				$end = $this->_get_post_datetime('gkal_date_end_datum', 'gkal_date_uhrzeit_ende', $cal['kalender_timezone']);
				$event->setEnd($end);

				$event->setSummary($_POST['gkal_summary']);
				$event->setDescription($_POST['gkal_description']);
				if (!empty($_POST['gkal_location'])) {
					$event->setLocation($_POST['gkal_location']);
				}

				// Validieren
				if (!$this->_validate_event($event)) {
					return;
				}
				try {
					$service->events->insert($cal['kalender_google_id'], $event);
					if ($cal['kalender_send_email']) {
						$this->send_mail_kalender($cal);
					}
					$loc_url .= htmlspecialchars_decode($url).$cal['kalender_id'].'&is_eingetragen=yes';
					header('HTTP/1.1 303 See Other');
					header('Location: '.$loc_url);
					echo '<a href="'.htmlspecialchars($loc_url).'" rel="next">Weiter</a>';
					exit();
				}
				catch (Google_Service_Exception $e) {
					$this->content->template['is_eingetragen'] = 'no';
				}
			}
		}
	}

	/**
	 * class_cal_front::send_mail_kalender()
	 * $this->checked->psel_cal_id
	 *
	 * @return bool
	 */
	function send_mail_kalender($cal)
	{
		global $mail_it;
		$this->mail_it=&$mail_it;
		$this->mail_it->to = $cal['kalender_info_email'];
		$this->mail_it->from = $this->cms->admin_email;
		$this->mail_it->from_text = $this->content->template['plugin_google_kalender_neuer_eintrag'];
		$this->mail_it->subject = $this->content->template['plugin_google_kalender_neuer_eintrag'];
		$this->mail_it->body = $this->content->template['plugin_google_kalender_neuer_eintrag']." von ".$this->user->username."\n\n"
			.$this->checked->gkal_date_start_datum.' '.$this->checked->gkal_date_uhrzeit_beginn."\n"
			.'Titel: '.$this->checked->gkal_summary."\n"
			.'Beschreibung: '.str_replace("\n", "\n  ", $this->checked->gkal_description)."\n"
			.'Ort: '.$this->checked->gkal_location."\n";
		$this->mail_it->priority = 5;
		$this->mail_it->do_mail();
		return true;
	}
}

$class_gcal_front = new class_gcal_front();
