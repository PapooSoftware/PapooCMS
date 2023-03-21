<?php

/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2010       #
# Authors: Christoph Grenz          #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >5.0                  #
#####################################
 */

/**
 * Class MesseReminder
 */
#[AllowDynamicProperties]
class MesseReminder {

	var $MV_ID = 1;
	var $MV_LANG = 1;
	var $NAMEFELD = 'name';
	var $BRANCHEFELD = 'branche';
	var $DATUMFELD = 'datumbeginn';
	var $ENDDATUMFELD = 'datumende';

	/** @var string */
	var $news = "";

	/**
	 * MesseReminder constructor.
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

		$this->messetable = $this->cms->tbname['papoo_mv'].'_content_'.$this->MV_ID.'_search_'.$this->MV_LANG;

		$this->make_data();
		$this->content->template['plugin_message'] = "";
	}

	/**
	 * Interne Verwaltung
	 */
	function make_data()
	{
		if (defined("admin")) {
			global $template;

			$template2 = str_ireplace(PAPOO_ABS_PFAD."/plugins/","",$template);

			// Überprüfe ob Zugriff auf die Inhalte besteht
			$this->user->check_access();
			if ( $template != "login.utf8.html" ) {
				$this->content->template['fl_link'] = 'plugin.php?menuid='.((int)$this->checked->menuid).'&amp;template='.$this->checked->template;
				switch ($template2) {
					//Die Startseite
				case "messe_reminder/templates/backend_start.html" :
					$urlbase = ($_SERVER['SERVER_PORT'] == 443)?'https://':'http://';
					$urlbase .= $_SERVER['HTTP_HOST'];
					if (!empty($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != 80) && ($_SERVER['SERVER_PORT'] != 443)) {
						$urlbase .= ':'.$_SERVER['SERVER_PORT'];
					}
					$urlbase .= PAPOO_WEB_PFAD.'/';
					$urlbase .= 'interna/plugin.php?menuid='.((int)$this->checked->menuid).'&amp;';
					$this->content->template['urlbase'] = $urlbase;

					break;
					//Die Standardeinstellungen werden bearbeitet
				case "messe_reminder/templates/backend_defaults.html" :
					$this->load_defaults();
					break;
				case "messe_reminder/templates/backend_entries.html":
					if (!empty($_POST['ajax'])) {
						$this->ajax_enable_disable();
					}
					elseif (!empty($this->checked->messe_id)) {
						$this->load_edit();
					}
					else {
						$this->check_create_items();
						$this->check_delete_items();
						$this->load_list();
					}
					break;
				case "messe_reminder/templates/cronjob.html":
					$this->check_create_items();
					$this->check_delete_items();
					$this->do_cronjob();
					break;

				default:
					break;
				}
			}
		}
	}

	function check_create_items()
	{
		// Hole Text der Vorlage. Wenn leer lege noch nichts an, da die Vorlage noch nicht bearbeitet wurde.
		$sql = sprintf('SELECT text FROM `%s` WHERE messe_id = 0',
			$this->cms->tbname['papoo_messe_reminder']);
		$text = $this->db->get_var($sql);
		if (!$text) {
			return;
		}
		// Hole Liste aller Messeeinträge, die noch keinen Eintrag im Reminder haben
		$sql = sprintf('SELECT mv_content_id FROM `%s` WHERE mv_content_id NOT IN (SELECT `messe_id` FROM `%s`)',
			$this->messetable, $this->cms->tbname['papoo_messe_reminder']);
		$result = $this->db->get_results($sql,ARRAY_A);

		if ($result) {
			// Hole Default-Werte
			$sql = sprintf('SELECT * FROM `%s` WHERE `messe_id` = 0',
				$this->cms->tbname['papoo_messe_reminder']);
			$defaults = $this->db->get_results($sql,ARRAY_A);
			$defaults = $defaults[0];
			// Für jede Messe Eintrag erstellen
			foreach ($result as $item) {
				$sql = $this->build_insert($this->cms->tbname['papoo_messe_reminder'], array(
					'messe_id' => ((int)$item['mv_content_id']),
					'enabled' => ((bool)$defaults['enabled']),
					'subject' => $defaults['subject'],
					'text' => $defaults['text'],
					'interval' => ((int)$defaults['interval'])
				));
				$this->db->query($sql);
			}
		}
	}

	function check_delete_items()
	{
		// Hole Liste aller Remindereinträge, die keine zugeordnete existierende Messe haben
		$sql = sprintf('SELECT messe_id FROM `%s` WHERE `messe_id` > 0 AND `messe_id` NOT IN (SELECT `mv_content_id` FROM `%s`)',
			$this->cms->tbname['papoo_messe_reminder'], $this->messetable);
		$result = $this->db->get_results($sql,ARRAY_A);

		if ($result) {
			$where = '';
			foreach ($result as $item) {
				if ($where) {
					$where .= ' OR ';
				}
				$where .= 'messe_id = ' . $item['messe_id'];
			}

			$sql = sprintf('DELETE FROM `%s` WHERE %s',
				$this->cms->tbname['papoo_messe_reminder'],
				$where);
			$this->db->query($sql);
		}
	}

	function load_list()
	{
		$sql = sprintf('SELECT `messe_id` as id, `email`, `%s` as `name`, `interval`, mv_content_owner as `owner`, `last_timestamp`, `enabled` FROM `%s` JOIN `%s` ON `messe_id` = `mv_content_id` ORDER BY `messe_id`',
			$this->feld_by_name($this->MV_ID, $this->NAMEFELD),
			$this->cms->tbname['papoo_messe_reminder'],
			$this->messetable);
		$result = $this->db->get_results($sql,ARRAY_A);
		if (!$result) {
			$result = array();
		}
		foreach ($result as &$item) {
			$item['name'] = htmlspecialchars($item['name']);
			if ($item['last_timestamp'] !== NULL) {
				$item['last_timestamp'] = date('d.m.Y H:i', $item['last_timestamp']);
			}
			else {
				$item['last_timestamp'] = '&ndash;';
			}
			if (!$item['email']) {
				$item['email'] = $this->get_user_mail((int)$item['owner']);
			}
		}
		$this->content->template['plugin']['messe_reminder'] = array(
			'messe_list' => $result,
			'edit' => FALSE
		);
	}

	function load_defaults()
	{
		if (!empty($_POST['update'])) {
			$sql = $this->build_update($this->cms->tbname['papoo_messe_reminder'], array(
				'enabled' => (!empty($_POST['active']) && $_POST['active']),
				'subject' => $_POST['subject'],
				'text' => $_POST['content'],
				'interval' => ((int)$_POST['interval'])
			), '`messe_id`=0', 1);
			$updated = $this->db->query($sql);
			$update_failed = !$updated;
		}
		else {
			$updated=FALSE; $update_failed=FALSE;
		}

		$sql = sprintf('SELECT * FROM `%s` WHERE `messe_id` = 0',
			$this->cms->tbname['papoo_messe_reminder']);
		$defaults = $this->db->get_results($sql,ARRAY_A);
		$defaults = $defaults[0];
		$this->content->template['plugin']['messe_reminder'] = array(
			'active' => (bool)($defaults['enabled']),
			'subject' => htmlspecialchars($defaults['subject']),
			'content' => htmlspecialchars($defaults['text']),
			'interval' => ((int)$defaults['interval']),
			'updated' => $updated,
			'update_failed' => $update_failed,
		);
	}

	function ajax_enable_disable()
	{
		$messe_id = (int)$_POST['messe_id'];
		if ($messe_id <= 0) {
			exit("INVALID ID");
		}

		$sql = $this->build_update($this->cms->tbname['papoo_messe_reminder'], array(
			'enabled' => (!empty($_POST['active']) && $_POST['active'])
		), "messe_id = ".$messe_id);
		if($this->db->query($sql)) {
			header("HTTP/1.1 200 OK");
		}
		else {
			header("HTTP/1.1 409 Conflict");
		}
		exit();
	}

	function load_edit()
	{
		$messe_id = (int)$this->checked->messe_id;
		if ($messe_id <= 0) {
			return;
		}

		// Speichern wenn gefordert
		if (!empty($_POST['update'])) {
			$sql = sprintf('SELECT mv_content_owner 
						FROM `%s` WHERE `mv_content_id` = %d',
				$this->messetable,
				$messe_id);
			$ownerid = (int)$this->db->get_var($sql);
			$ownermail = $this->get_user_mail($ownerid);
			if ($_POST['email'] === $ownermail) {
				$email = NULL;
			}
			else {
				$email = $_POST['email'];
			}

			$sql = $this->build_update($this->cms->tbname['papoo_messe_reminder'], array(
				'email' => $email,
				'enabled' => (!empty($_POST['active']) && $_POST['active']),
				'subject' => stripslashes($_POST['subject']),
				'text' => stripslashes($_POST['content']),
				'interval' => ((int)$_POST['interval']),
			), '`messe_id` = '.$messe_id, 1);
			$updated = $this->db->query($sql);
			$update_failed = !$updated;
		}
		else {
			$updated = $update_failed = FALSE;
		}

		// Daten sammeln
		$sql = sprintf('SELECT `messe_id` as id, `email`, `%s` as `name`, mv_content_owner as `owner`,
						`enabled`, `subject`, `text`, `interval`
						FROM `%s` JOIN `%s` ON `messe_id` = `mv_content_id`
						WHERE `messe_id` = %d',
			$this->feld_by_name($this->MV_ID, $this->NAMEFELD),
			$this->cms->tbname['papoo_messe_reminder'],
			$this->messetable,
			$messe_id);
		$result = $this->db->get_results($sql,ARRAY_A);
		$result = $result[0];

		$email = $result['email'];
		if (!$email) {
			$email = $this->get_user_mail($result['owner']);
		}

		// Daten in Template packen
		$this->content->template['plugin']['messe_reminder'] = array(
			'edit' => TRUE,
			'name' => $result['name'],
			'email' => $email,
			'active' => ((bool)$result['enabled']),
			'subject' => $result['subject'],
			'content' => "nodecode:".htmlspecialchars($result['text'], ENT_NOQUOTES),
			'interval' => ((int)$result['interval']),
			'user' => $this->get_user_name($result['owner']),
			'updated' => $updated,
			'update_failed' => $update_failed,
		);
	}

	function do_cronjob()
	{
		global $mail_it;
		$results = array();
		$time = time();
		// Daten sammeln
		$sql = sprintf('SELECT *
						FROM `%s` JOIN `%s` ON `messe_id` = `mv_content_id` WHERE `messe_id` > 0 AND `enabled` = 1',
			$this->cms->tbname['papoo_messe_reminder'],
			$this->messetable);
		$result = $this->db->get_results($sql,ARRAY_A);
		if (!$result) {
			$result = array();
		}
		// Felddaten sammeln
		$namefeld = $this->feld_by_name($this->MV_ID, $this->NAMEFELD);
		$branchefeld = $this->feld_by_name($this->MV_ID, $this->BRANCHEFELD);
		$datumfeld = $this->feld_by_name($this->MV_ID, $this->DATUMFELD);
		$enddatumfeld = $this->feld_by_name($this->MV_ID, $this->ENDDATUMFELD);

		// Alle Reminder durchgehen
		foreach ($result as &$item) {
			$messe_id = (int)$item['messe_id'];
			$name = $item['name'] = $item[$namefeld];
			$branche = $item['branche'] = $item[$branchefeld];
			$datum = strtotime(str_replace('.', '-', $item[$datumfeld]));
			$enddatum = strtotime(str_replace('.', '-', $item[$enddatumfeld]));
			$email = $item['email'];
			$interval = (int)$item['interval'];
			$last_timestamp = $item['last_timestamp'];
			$subject = $item['subject'];
			$body = $item['text'];

			if (!$email) {
				$email = $item['email'] = $this->get_user_mail($item['mv_content_owner']);
			}
			if (!$email) {
				// Wenn keine E-Mail-Adresse vorhanden: überspringen
				$results[] = array('status'=>'invalid email address', 'data'=>$item);
				continue;
			}
			if (!$datum or !$enddatum) {
				// Wenn keine validen Datumsangaben: überspringen
				$results[] = array('status'=>'invalid date', 'data'=>$item);
				continue;
			}

			// Prüfen ob Bedingungen für Mail erfüllt
			if (($enddatum < $time) AND
				($last_timestamp === NULL or (($interval != 0) and ($time >= $last_timestamp+($interval*60*60*24*7))))) {
				// Templates für Betreff und Mailtext füllen
				$subject = str_replace('#messe#', $item['name'], $subject);
				$subject = str_replace('#branche#', $item['branche'], $subject);
				$subject = str_replace('#datum#', date("d.m.Y", $datum), $subject);
				$subject = str_replace('#enddatum#', date("d.m.Y", $enddatum), $subject);
				$subject = str_replace('#vorname#', $this->get_user_givenname($item['mv_content_owner']), $subject);
				$subject = str_replace('#nachname#', $this->get_user_lastname($item['mv_content_owner']), $subject);

				$body = str_replace('#messe#', $item['name'], $body);
				$body = str_replace('#branche#', $item['branche'], $body);
				$body = str_replace('#datum#', date("d.m.Y", $datum), $body);
				$body = str_replace('#enddatum#', date("d.m.Y", $enddatum), $body);
				$body = str_replace('#vorname#', $this->get_user_givenname($item['mv_content_owner']), $body);
				$body = str_replace('#nachname#', $this->get_user_lastname($item['mv_content_owner']), $body);

				// E-Mail erstellen
				$mail_it->from = $this->cms->admin_email;
				$mail_it->from_text = '';
				$mail_it->to = $email;
				$mail_it->cc = "";
				$mail_it->subject = $subject;
				$mail_it->body = $body;
				$mail_it->body_html = NULL;
				$mail_it->attach = array();
				$mail_it->ReplyTo = NULL;
				$mail_it->priority = 5;
				// mail übermitteln
				$mailed = $mail_it->do_mail();

				if ($mailed) {
					$sql = sprintf('UPDATE %s SET `last_timestamp` = UNIX_TIMESTAMP(NOW()) WHERE `messe_id` = %d',
						$this->cms->tbname['papoo_messe_reminder'],
						$messe_id);
					$this->db->query($sql);
				}
				$results[] = array('status'=>(($mailed)?'ok':$mail_it->error), 'data'=>$item);
			}
			else {
				$results[] = array('status'=>'skipped', 'data'=>$item);
			}
		}
		// Daten in Template packen
		$this->content->template['plugin']['messe_reminder'] = array(
			'cronjob' => TRUE,
			'data' => $results
		);
	}

	/**
	 * @param $table
	 * @param $values
	 * @return string
	 */
	function build_insert($table, $values) {

		$keys_r = array();
		$values_r = array();

		foreach ($values as $key=>$value) {
			$keys_r[] = '`'.$this->db->escape($key).'`';
			if (is_string($value)) {
				$value = '\''.$this->db->escape($value).'\'';
			}
			elseif (is_null($value)) {
				$value = 'NULL';
			}
			elseif (is_bool($value)) {
				$value = ($value)?'1':'0';
			}
			elseif (is_array($value)) {
				die(__FILE__ . ": build_insert: arrays are invalid database values");
			}
			elseif (is_object($value)) {
				die(__FILE__ . ": build_insert: arrays are invalid database values");
			}
			$values_r[] = $value;
		}
		$keys_r = implode(', ', $keys_r);
		$values_r = implode(', ', $values_r);

		return sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, $keys_r, $values_r);
	}

	/**
	 * @param $table
	 * @param $values
	 * @param $where
	 * @param int $limit
	 * @return string
	 */
	function build_update($table, $values, $where, $limit=-1) {

		if (!is_string($values)) {
			$values_r = array();
			foreach ($values as $key=>$value) {
				if ($value == DB_DEFAULT) {
					$value = 'DEFAULT';
				}
				elseif (is_string($value)) {
					$value = '\''.$this->db->escape($value).'\'';
				}
				elseif (is_null($value)) {
					$value = 'NULL';
				}
				elseif (is_bool($value)) {
					$value = ($value)?'1':'0';
				}
				elseif (is_array($value)) {
					die(__FILE__ . ": build_update: arrays are invalid database values");
				}
				elseif (is_object($value)) {
					die(__FILE__ . ": build_update: arrays are invalid database values");
				}
				$values_r[] = '`'.$this->db->escape($key).'` = '.$value;
			}
			$values_r = implode(', ', $values_r);
		}
		else $values_r = $values;

		if ($limit >= 0) {
			$limit = ' LIMIT '.((int)$limit);
		}
		else {
			$limit = '';
		}
		return sprintf('UPDATE `%s` SET %s WHERE %s%s', $table, $values_r, $where, $limit);
	}

	/**
	 * @param $mv
	 * @param $name
	 * @return string
	 */
	function feld_by_name($mv, $name) {
		return $name.'_'.$this->feldid_by_name($mv, $name);
	}

	/**
	 * @param $mv
	 * @param $name
	 * @return array|null
	 */
	function feldid_by_name($mv, $name) {
		$sql = sprintf("SELECT mvcform_id FROM %s WHERE mvcform_form_id = %d and mvcform_name = '%s' LIMIT 1",
			$this->cms->tbname['papoo_mvcform'],
			$mv,
			$this->db->escape($name));
		return $this->db->get_var($sql);
	}

	/**
	 * @param $userid
	 * @return array|null
	 */
	function get_user_mail($userid) {
		$sql = sprintf('SELECT email FROM `%s` WHERE `userid` = %d',
			$this->cms->tbname['papoo_user'],
			((int)$userid));
		return $this->db->get_var($sql);
	}

	/**
	 * @param $userid
	 * @return array|null
	 */
	function get_user_name($userid) {
		$sql = sprintf('SELECT username FROM `%s` WHERE `userid` = %d',
			$this->cms->tbname['papoo_user'],
			((int)$userid));
		return $this->db->get_var($sql);
	}

	/**
	 * @param $userid
	 * @return array|null
	 */
	function get_user_lastname($userid) {
		$sql = sprintf('SELECT user_nachname FROM `%s` WHERE `userid` = %d',
			$this->cms->tbname['papoo_user'],
			((int)$userid));
		return $this->db->get_var($sql);
	}

	/**
	 * @param $userid
	 * @return array|null
	 */
	function get_user_givenname($userid) {
		$sql = sprintf('SELECT user_vorname FROM `%s` WHERE `userid` = %d',
			$this->cms->tbname['papoo_user'],
			((int)$userid));
		return $this->db->get_var($sql);
	}
}

$MesseReminder = new MesseReminder();
