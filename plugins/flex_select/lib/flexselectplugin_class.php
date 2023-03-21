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
 * Class FlexSelect
 */
#[AllowDynamicProperties]
class FlexSelect {

	/** @var string */
	var $news = "";

	/**
	 * FlexSelect constructor.
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

		$this->make_data();
		$this->content->template['plugin_message'] = "";
	}

	function output_filter()
	{
		if ($this->checked->template != 'mv/templates/mv_search_front_onemv.html') {
			return;
		}
		$mv_id = ((int)$this->checked->mv_id);

		/* Hole Liste der Verknüpfungen */
		$sql = sprintf('SELECT `link_id`, `field1_id`, `field2_id`
						FROM `%s`
						WHERE form_id=%d',
			$this->cms->tbname['papoo_mv_select_linking'],
			$mv_id);
		$links = $this->db->get_results($sql, ARRAY_A);
		// Gespeicherte Auswahlmöglichkeiten holen
		$data = array();
		if (!$links) {
			return;
		}
		foreach ($links as $link) {
			$sql = sprintf('SELECT * FROM %s WHERE `link_id` = %d',
				$this->cms->tbname['papoo_mv_select_linking_items'],
				$link['link_id']);
			$result = $this->db->get_results($sql, ARRAY_A);
			if (!$result) {
				continue;
			}
			foreach($result as $item) {
				$data[$link['field1_id']][$link['field2_id']][$item['lookup1_id']][] = $item['lookup2_id'];
			}
		}

		// Ausgabe erstellen
		global $output;
		$einbindung = array();

		$einbindung[] = '<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/flex_select/js/flex_select.js"></script>';
		$einbindung[] = '<script type="text/javascript">//<![CDATA[';
		foreach($data as $field1=>$data2)
			foreach($data2 as $field2=>$data3) {
				$einbindung[] = '	flex_select('.$field1.','.$field2.', [';
				foreach($data3 as $lookup1=>$data4) {
					$einbindung[] = '		['.$lookup1.', [';
					$einbindung[] = '			'.implode(',', $data4);
					$einbindung[] = '		]],';
				}
				$einbindung[] = '	]);';
			}
		$einbindung[] = '//]]>';
		$einbindung[] = '</script>';

		$einbindung = "\t".implode("\n\t", $einbindung)."\n";
		$i = strpos($output, '</head>');
		$output1 = substr($output, 0, $i);
		$output2 = substr($output, $i);
		$output = $output1.$einbindung.$output2;
	}

	function activate_jquery()
	{
		//SQL-Anweisung für das Auslesen des Häkchens
		$query = sprintf("SELECT config_jquery_aktivieren_label FROM %s", $this->cms->tbname['papoo_config']);

		$result = $this->db->get_results($query);

		//$value ist 0, falls das Häkchen nicht gesetzt ist und 1, falls doch
		$value = $result[0]->config_jquery_aktivieren_label;

		//Falls das Häkchen nicht gesetzt ist, wird es gesetzt
		if ($value == 0) {
			$query = sprintf("UPDATE %s 
                            SET config_jquery_aktivieren_label='1'", $this->cms->tbname['papoo_config']);
			$this->db->query($query);
		}
	}

	/**
	 * Interne Verwaltung
	 */
	function make_data()
	{
		if (defined("admin")) {
			global $template;

			$template2 = str_ireplace(PAPOO_ABS_PFAD."/plugins/","",$template);

			// Überprü ob Zugriff auf die Inhalte besteht
			$this->user->check_access();
			if ( $template != "login.utf8.html" ) {
				$this->content->template['fl_link'] = 'plugin.php?menuid='.((int)$this->checked->menuid).'&amp;template='.$this->checked->template;
				switch ($template2) {

					//Die Standardeinstellungen werden bearbeitet
				case "flex_select/templates/backend_main.html" :
					if (empty($this->checked->mv_id))
						$this->load_form_list();
					else
						if (!empty($this->checked->link_id)) {
							if ($this->checked->link_id == 'new')
								$this->load_create();
							else
								$this->load_edit();
						}
						else
							$this->load_existing_list();
					break;

				default:
					break;
				}
			}
		}
	}

	function load_form_list()
	{
		$sql = sprintf('SELECT mv_id, mv_name FROM `%s`',
			$this->cms->tbname['papoo_mv']);
		$result = $this->db->get_results($sql,ARRAY_A);
		$this->content->template['plugin']['flex_select'] = array(
			'form_list' => $result,
			'show_form_list' => 1
		);
	}

	function load_existing_list()
	{
		$mv_name = $this->get_mv_name($this->checked->mv_id);
		/* Hole Liste der Verknüpfungen */
		$sql = sprintf('SELECT `link_id`, `form_id`, `field1_id`, `field2_id`,
						f1.`mvcform_name` as `field1_name`, f2.`mvcform_name` as `field2_name`
						FROM `%s` JOIN `%s` AS f1 ON form_id=f1.mvcform_form_id AND field1_id=f1.mvcform_id
						JOIN `%s` AS f2 ON form_id=f2.mvcform_form_id AND field2_id=f2.mvcform_id
						WHERE form_id=%d ORDER BY `link_id`',
			$this->cms->tbname['papoo_mv_select_linking'],
			$this->cms->tbname['papoo_mvcform'],
			$this->cms->tbname['papoo_mvcform'],
			((int)$this->checked->mv_id));
		$result = $this->db->get_results($sql,ARRAY_A);
		$this->content->template['plugin']['flex_select'] = array(
			'existing_list' => $result,
			'show_existing_list' => 1,
			'mv_id' => ((int)$this->checked->mv_id),
			'mv_name' => $mv_name
		);
	}

	function load_create()
	{
		$mv_name = $this->get_mv_name($this->checked->mv_id);
		// Hole die Liste der Select-Felder
		$sql = sprintf('SELECT mvcform_name as `name`, mvcform_id as `id`
						FROM `%s` WHERE mvcform_form_id=%d AND mvcform_type="select"',
			$this->cms->tbname['papoo_mvcform'],
			((int)$this->checked->mv_id));
		$result = $this->db->get_results($sql,ARRAY_A);
		foreach($result as &$item) {
			if(!empty($this->checked->field1) && $this->checked->field1 == $item['id']) {
				$item['selected'] = 1;
			}
			elseif(!empty($this->checked->field2) && $this->checked->field2 == $item['id']) {
				$item['selected'] = 2;
			}
		}

		// Template-Daten setzen
		$this->content->template['plugin']['flex_select'] = array(
			'create' => 1,
			'mv_id' => ((int)$this->checked->mv_id),
			'mv_name' => $mv_name,
			'select_fields' => $result
		);

		// Neuer Eintrag gesendet, in DB übernehmen
		if (!empty($_POST['create'])) {
			// Prüfe ob alle nötigen Felder ausgefüllt
			if (!empty($this->checked->field1) && !empty($this->checked->field2) && ($this->checked->field2 != $this->checked->field1)) {
				// Prüfe ob bereits ein Eintrag existiert
				$selectsql = sprintf('SELECT `link_id` FROM %s WHERE `form_id` = %d AND
									`field1_id` = %d AND `field2_id` = %d',
					$this->cms->tbname['papoo_mv_select_linking'],
					((int)$this->checked->mv_id),
					((int)$this->checked->field1),
					((int)$this->checked->field2)
				);
				if($this->db->get_results($selectsql, ARRAY_A)) {
					$this->content->template['plugin']['flex_select']['insertfailed'] = true;
				}
				else {
					// Insert
					$sql = $this->build_insert($this->cms->tbname['papoo_mv_select_linking'], array(
						'form_id' => ((int)$this->checked->mv_id),
						'field1_id' => ((int)$this->checked->field1),
						'field2_id' => ((int)$this->checked->field2)
					));
					if(@$this->db->query($sql)) {
						// Hole Link-Id des neuen Eintrags
						$link_id = $this->db->get_var($selectsql);
						// Weiterleiten zu Edit
						$locationuri = './plugin.php?menuid='.((int)$this->checked->menuid).'&template='.$this->checked->template;
						$locationuri .= '&mv_id='.((int)$this->checked->mv_id).'&link_id='.$link_id;
						header('HTTP/1.1 303 See Other');
						header("Location: $locationuri");
						print('<html><body><a href=\"'.urlescape($locationuri)."\">$locationuri</a></body></html>");
						exit();
					}
					else {
						$this->content->template['plugin']['flex_select']['insertfailed'] = true;
					}
				}
			}
			else {
				$this->content->template['plugin']['flex_select']['incomplete'] = true;
			}
		}
	}

	function load_edit()
	{
		// Sammle grundlegende Daten
		$mv_id = (int)$this->checked->mv_id;
		$mv_name = $this->get_mv_name($this->checked->mv_id);
		$link_id = (int)$this->checked->link_id;

		$sql = sprintf('SELECT * FROM %s WHERE `link_id` = %d',
			$this->cms->tbname['papoo_mv_select_linking'],
			$link_id);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return;
		}
		$result = $result[0];
		$select1_id = $result['field1_id'];
		$select2_id = $result['field2_id'];
		$presql = sprintf('SELECT mvcform_name FROM %s WHERE `mvcform_form_id` = %d AND mvcform_id = %%d',
			$this->cms->tbname['papoo_mvcform'],
			$mv_id);
		$select1 = $this->db->get_var(sprintf($presql, $select1_id));
		$select2 = $this->db->get_var(sprintf($presql, $select2_id));
		// Auswahlmöglichkeiten holen
		$dbname = $this->cms->tbname['papoo_mv'] . "_content_${mv_id}_lang_${select1_id}";
		$sql = sprintf('SELECT `lookup_id` as `id`, `content` as `name`  FROM %s WHERE `lang_id` = %d',
			$dbname, $this->cms->lang_id);
		$options1 = $this->db->get_results($sql, ARRAY_A);

		$dbname = $this->cms->tbname['papoo_mv'] . "_content_${mv_id}_lang_${select2_id}";
		$sql = sprintf('SELECT `lookup_id` as `id`, `content` as `name`  FROM %s WHERE `lang_id` = %d',
			$dbname, $this->cms->lang_id);
		$options2 = $this->db->get_results($sql, ARRAY_A);

		// Speichern, wenn gefragt
		if (!empty($_POST['update'])) {
			// Lösche zuerst die alten Einträge
			$sql = sprintf('DELETE FROM `%s` WHERE `link_id` = %d',
				$this->cms->tbname['papoo_mv_select_linking_items'],
				$link_id);
			$this->db->query($sql);
			// Dann speichere die neuen
			foreach($options1 as $op1) {
				$selected = ((array)$_POST['options'.$op1['id']]);

				foreach($selected as $item) {
					if (is_numeric($item)) {
						$sql = $this->build_insert($this->cms->tbname['papoo_mv_select_linking_items'], array(
							'link_id'=>$link_id,
							'lookup1_id'=>$op1['id'],
							'lookup2_id'=>((int)$item)
						));
						$this->db->query($sql);
					}
				}
			}
			// Umleiten
			$locationuri = './plugin.php?menuid='.((int)$this->checked->menuid).'&template='.$this->checked->template;
			$locationuri .= '&mv_id='.((int)$this->checked->mv_id);
			header('HTTP/1.1 303 See Other');
			header("Location: $locationuri");
			print('<html><body><a href=\"'.urlescape($locationuri)."\">$locationuri</a></body></html>");
			exit();
		}

		// Gespeicherte Auswahlmöglichkeiten holen
		$sql = sprintf('SELECT * FROM %s WHERE `link_id` = %d',
			$this->cms->tbname['papoo_mv_select_linking_items'],
			$link_id);
		$result = $this->db->get_results($sql, ARRAY_A);
		if($result === NULL) {
			$result = array();
		}
		// Baum erstellen
		$data = array();
		foreach($options1 as $dat1) {
			$r = $dat1;
			$r['choices'] = array();
			foreach($options2 as $dat2) {
				foreach($result as $item)
					if (($dat1['id'] == $item['lookup1_id']) && ($dat2['id'] == $item['lookup2_id'])) {
						$dat2['checked'] = true;
					}
				$r['choices'][] = $dat2;
			}
			$data[] = $r;
		}
		// Template-Daten setzen
		$this->content->template['plugin']['flex_select'] = array(
			'edit' => 1,
			'link_id' => $link_id,
			'mv_id' => $mv_id,
			'mv_name' => $mv_name,
			'field1' => $select1,
			'field2' => $select2,
			'data' => $data
		);

		// Löschen, wenn gefragt
		if (!empty($_POST['remove'])) {
			$sql = sprintf('DELETE FROM `%s` WHERE `link_id` = %d',
				$this->cms->tbname['papoo_mv_select_linking'],
				$link_id);
			$this->db->query($sql);
			$locationuri = './plugin.php?menuid='.((int)$this->checked->menuid).'&template='.$this->checked->template;
			$locationuri .= '&mv_id='.((int)$this->checked->mv_id);
			header('HTTP/1.1 303 See Other');
			header("Location: $locationuri");
			print('<html><body><a href=\"'.urlescape($locationuri)."\">$locationuri</a></body></html>");
			exit();
		}
	}

	/**
	 * @param $mv_id
	 * @return array|null
	 */
	function get_mv_name($mv_id)
	{
		$sql = sprintf('SELECT `mv_name` FROM %s WHERE `mv_id` = %d',
			$this->cms->tbname['papoo_mv'],
			((int)$mv_id));
		return $this->db->get_var($sql);
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
			if ($value == DB_DEFAULT) {
				$value = 'DEFAULT';
			}
			elseif (is_string($value)) {
				$value = '\'' . $this->db->escape($value) . '\'';
			}
			elseif (is_null($value)) {
				$value = 'NULL';
			}
			elseif (is_bool($value)) {
				$value = ($value) ? '1' : '0';
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
}

$FlexSelect = new FlexSelect();
