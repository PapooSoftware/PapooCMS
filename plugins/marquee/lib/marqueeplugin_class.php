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

define('DB_DEFAULT', '__$$DEFAULT$$__');

/**
 * Class Marquee
 */
#[AllowDynamicProperties]
class Marquee {

	/** @var string */
	var $news = "";

	/**
	 * Marquee constructor.
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

		$this->make_marquee();
		$this->content->template['plugin_message'] = "";
	}

	function output_filter()
	{
		global $output;
		$class = stripslashes($this->content->template['plugin']['marquee']['class']);
		$einbindung = array();

		$einbindung[] = '<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/marquee/js/jquery.marquee.js"></script>';
		$einbindung[] = '<script type="text/javascript">
		$(document).ready(function() {
			$("marquee").marquee("'.$class.'").mouseover(function () {
				$(this).trigger("stop");
			}).mouseout(function () {
				$(this).trigger("start");
			});
		});
	</script>';

		$einbindung = "\t".implode("\n\t", $einbindung)."\n";
		$i = strpos($output, '</head>');
		$output1 = substr($output, 0, $i);
		$output2 = substr($output, $i);
		$output = $output1.$einbindung.$output2;
	}

	function activate_jquery()
	{
		//SQL-Anweisung f�r das Auslesen des H�kchens
		$query = sprintf("SELECT config_jquery_aktivieren_label FROM %s", $this->cms->tbname['papoo_config']);

		$result = $this->db->get_results($query);

		//$value ist 0, falls das h�kchen nicht gestetzt ist und 1, falls doch
		$value = $result[0]->config_jquery_aktivieren_label;

		//Falls das H�kchen nicht gesetzt ist, wird es gesetzt
		if ($value == 0) {
			$query = sprintf("UPDATE %s 
                            SET config_jquery_aktivieren_label='1'", $this->cms->tbname['papoo_config']);
			$this->db->query($query);
		}
	}

	/**
	 * Interne Verwaltung
	 */
	function make_marquee()
	{
		if (defined("admin")) {
			global $template;

			$template2 = str_ireplace(PAPOO_ABS_PFAD."/plugins/","",$template);

			// �berpr�fen ob Zugriff auf die Inhalte besteht
			$this->user->check_access();
			if ( $template != "login.utf8.html" ) {
				switch ($template2) {

					//Die Standardeinstellungen werden bearbeitet
				case "marquee/templates/backend_start.html" :
					$this->check_pref();
					break;

				default:
					break;
				}
			}
		}
		else {
			$this->marquee_read();
		}
	}

	/**
	 * Was im Frontend passiert
	 */
	function marquee_read()
	{
		$sql = sprintf("SELECT * FROM %s WHERE marquee_lang = '%s'",
			$this->cms->tbname['papoo_marquee'],
			$this->db->escape($this->cms->lang_short)
		);

		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['plugin']['marquee'] = array(
			'content' => $result[0]['marquee_content'],
			'class' => htmlspecialchars($result[0]['marquee_class']),
			'behavior' => htmlspecialchars($result[0]['marquee_behavior']),
			'direction' => htmlspecialchars($result[0]['marquee_direction']),
			'scrollamount' => $result[0]['marquee_scrollamount'],
			'lang' => $this->cms->lang_long
		);
	}

	/**
	 * @param $table
	 * @param $values
	 * @return string
	 */
	function build_insert($table, $values)
	{

		$keys_r = array();
		$values_r = array();

		foreach ($values as $key=>$value) {
			$keys_r[] = '`'.$this->db->escape($key).'`';
			if ($value == DB_DEFAULT) $value = 'DEFAULT';
			elseif (is_string($value)) $value = '\''.$this->db->escape($value).'\'';
			elseif (is_null($value)) $value = 'NULL';
			elseif (is_bool($value)) $value = ($value)?'1':'0';
			elseif (is_array($value)) die(__FILE__ . ": build_insert: arrays are invalid database values");
			elseif (is_object($value)) die(__FILE__ . ": build_insert: arrays are invalid database values");
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
	function build_update($table, $values, $where, $limit=-1)
	{
		if (!is_string($values)) {
			$values_r = array();
			foreach ($values as $key=>$value) {
				if ($value == DB_DEFAULT) $value = 'DEFAULT';
				elseif (is_string($value)) $value = '\''.$this->db->escape($value).'\'';
				elseif (is_null($value)) $value = 'NULL';
				elseif (is_bool($value)) $value = ($value)?'1':'0';
				elseif (is_array($value)) die(__FILE__ . ": build_update: arrays are invalid database values");
				elseif (is_object($value)) die(__FILE__ . ": build_update: arrays are invalid database values");
				$values_r[] = '`'.$this->db->escape($key).'` = '.$value;
			}
			$values_r = implode(', ', $values_r);
		}
		else {
			$values_r = $values;
		}

		if ($limit >= 0) {
			$limit = ' LIMIT '.((int)$limit);
		}
		else {
			$limit = '';
		}
		return sprintf('UPDATE `%s` SET %s WHERE %s%s', $table, $values_r, $where, $limit);
	}

	/**
	 * Daten einstellen
	 * Funktion gibt nichts zur�ck, sondern �bergibt nur Daten an das Template
	 * Ge�nderte Daten werden eingetragen
	 */
	function check_pref()
	{
		// Ggf. �bergebene �nderungen �bernehmen
		$update = NULL;
		if (!empty($_POST['marquee_submit'])) {
			$values = array();
			// Sicherstellen, dass JQuery aktiviert ist
			$this->activate_jquery();

			$lang_e = $this->db->escape($this->cms->lang_short);
			$values['marquee_lang'] = $this->cms->lang_short;

			if (isset($_POST['marquee_content'])) {
				$values['marquee_content'] = $_POST['marquee_content'];
			}
			if (isset($_POST['marquee_class'])) {
				$values['marquee_class'] = $_POST['marquee_class'];
			}
			if (isset($_POST['marquee_behavior'])) {
				$values['marquee_behavior'] = $_POST['marquee_behavior'];
			}
			if (isset($_POST['marquee_direction'])) {
				$values['marquee_direction'] = $_POST['marquee_direction'];
			}
			if (isset($_POST['marquee_scrollamount'])) {
				$values['marquee_scrollamount'] = (int)($_POST['marquee_scrollamount']);
			}

			if ($values) {
				$sql = sprintf('SELECT COUNT(*) FROM %s WHERE marquee_lang = "%s"',
					$this->cms->tbname['papoo_marquee'], $lang_e);
				$count = $this->db->get_var($sql);
				if ($count == 0) {
					$sql = $this->build_insert($this->cms->tbname['papoo_marquee'], $values, true);
				}
				else {
					$sql = $this->build_update($this->cms->tbname['papoo_marquee'], $values, "marquee_lang = '$lang_e'", true);
				}
				$update = $this->db->query($sql);
			}
		}

		// Aktuelle Einstellungen aus Datenbank lesen
		$sql = sprintf("SELECT * FROM %s WHERE marquee_lang = '%s'",
			$this->cms->tbname['papoo_marquee'],
			$this->db->escape($this->cms->lang_short)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (!empty($result)) {
			$this->content->template['plugin']['marquee'] = array(
				'content' => htmlspecialchars($result[0]['marquee_content']),
				'class' => htmlspecialchars($result[0]['marquee_class']),
				'behavior' => htmlspecialchars($result[0]['marquee_behavior']),
				'direction' => htmlspecialchars($result[0]['marquee_direction']),
				'scrollamount' => $result[0]['marquee_scrollamount'],
				'lang' => $this->cms->lang_long
			);
		}
		else {
			$this->content->template['plugin']['marquee'] = array(
				'content' => '',
				'class' => 'marquee',
				'behavior' => 'scroll',
				'direction' => 'right',
				'scrollamount' => 1,
				'lang' => $this->cms->lang_long
			);
		}
		if ($update === TRUE || is_int($update)) {
			$this->content->template['plugin']['marquee']['updated'] = '1';
		}
		elseif($update === FALSE) {
			$this->content->template['plugin']['marquee']['update_failed'] = '1';
		}
	}
}

$Marquee = new Marquee();
