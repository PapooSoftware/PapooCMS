<?php

/**
 * Class partner_unterseiten_class
 */
#[AllowDynamicProperties]
class partner_unterseiten_class
{
	private $db, $content, $checked, $user, $cms, $menu, $intern_menu, $diverse;
	private $menu_front;

	public function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $checked, $user, $cms, $db_abs, $menu, $intern_menu, $diverse;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->db_abs = &$db_abs;
		$this->menu = &$menu;
		$this->intern_menu = &$intern_menu;
		$this->diverse = &$diverse;

		// Backend - Admin Dinge durchführen
		if ( defined('admin') ) {
			$this->user->check_intern();
			$tmpl = self::get_my_template_name();
			// Abbrechen wenn nicht in eigenem Template
			if (!$tmpl)
				return;

			// Frontend-Menü berechnen
			$this->menu->data_front_publish = $this->intern_menu->mak_menu_liste_zugriff();
			$this->content->template['menulist_data'] = $this->menu_front = $this->menu->menu_navigation_build("FRONT_COMPLETE", "CLEAN");
			// Backend-Pfade berechnen
			$this->content->template['backend_base_path'] = PAPOO_WEB_PFAD.'/interna/plugin.php?menuid='.$this->checked->menuid.'&template=';
			$this->content->template['backend_path'] = $this->content->template['backend_base_path'].'partner_unterseiten/templates/';
			// Funktionen für Backend-Seiten
			switch ($tmpl) {
			case "backend.html":
				$this->make_backend();
				break;
			case "backend_edit.html":
				$this->make_backend_edit();
				break;
			default:
			}
		}
		// Frontend:
		else {
			// Bei 'normalen' Seiten prüfen, ob nicht mehr auf Partnerseite
			if (!empty($_SESSION['partner_unterseite']) and strpos($_SERVER['PHP_SELF'], '/index.php') !== false) {
				if (empty($this->checked->partner)) {
					$_SESSION['partner_unterseite'] = null;
				}
			}

			// Wenn partner übergeben, in Session speichern
			if (!empty($this->checked->partner)) {
				$unterseite = $this->get_unterseite_by_name($this->checked->partner);
				if ($unterseite) {
					$_SESSION['partner_unterseite'] = $unterseite;
				} else {
					$_SESSION['partner_unterseite'] = null;
				}
			}
		}
	}

	/**
	 * @param string $prefix
	 * @return bool|string|null
	 */
	private static function get_my_template_name($prefix='plugins/partner_unterseiten/templates/')
	{
		global $template;
		$i = strpos($template, $prefix);
		if ($i === false) {
			return null;
		}
		return substr($template, $i+strlen($prefix));
	}

	public function make_backend()
	{
		$this->content->template['unterseiten'] = $this->get_unterseiten();
	}

	/**
	 * @return string
	 */
	private function get_papoo_base_url()
	{
		$https = !empty($_SERVER['HTTPS']);
		return rtrim(($https?'https://':'http://').$_SERVER['HTTP_HOST'].PAPOO_WEB_PFAD, '/');
	}

	/**
	 * @param array $unterseite
	 * @param int|null $menuid
	 * @return string
	 */
	public function get_partner_link($unterseite, $menuid=null)
	{
		// Menü-ID bestimmen
		if ($menuid === null) {
			$menuid = $unterseite['unterseiten_default_menuid'];
		}
		if (!$menuid) {
			$menuid = 1;
		}
		// Menüpunkt bestimmen
		$menuitem = null;
		foreach ($this->menu_front as $m) {
			if ($m['menuid'] == $menuid) {
				$menuitem = $m;
			}
		}
		if (!$menuitem) {
			return null;
		}

		// Freie URLs oder alte URLs
		if ($this->cms->mod_free) {
			$path = $menuitem['url_menuname'];
			$path .= '?partner='.urlencode($unterseite['unterseiten_name']);
		}
		else {
			$query = array(
				'partner' => $unterseite['unterseiten_name'],
				'menuid' => $menuitem['menuid']
			);
			$path = $menuitem['menulink'];
			if (strpos($path, 'plugin:')) {
				$query['template'] = substr($path, 7);
				$path = 'plugin.php';
			}
			$path .= '?'.http_build_query($query, '', '&');
		}

		// Basis bestimmen
		$https = !empty($_SERVER['HTTPS']);
		$base = ($https?'https://':'http://').$_SERVER['HTTP_HOST'].PAPOO_WEB_PFAD;
		$url = rtrim($base, '/').'/'.ltrim($path, '/');
		return $url;
	}

	public function make_backend_edit()
	{
		if (isset($_REQUEST['unterseiten_id'])) {
			$this->content->template['unterseiten_id'] = $unterseiten_id = $_REQUEST['unterseiten_id'];
		}
		else {
			$this->content->template['unterseiten_id'] = $unterseiten_id = null;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($_REQUEST['unterseiten_id'])) {
				// Erstellen
				if ($_POST['action'] == 'save') {
					$id = $this->create_unterseite($_POST);
					if ($id) {
						if (!empty($_GET['copy_from'])) {
							$this->clone_menuedits((int)$_GET['copy_from'], $id);
						}
						$url = '/interna/plugin.php?menuid='.$this->checked->menuid.'&template=partner_unterseiten/templates/backend_edit.html&unterseiten_id='.$id;
						$this->diverse->http_redirect($url, 303);
					}
					else {
						$this->content->template['error'] = true;
					}
				}
			}
			else {
				// Bearbeiten/Löschen
				$id = (int)$_REQUEST['unterseiten_id'];

				if (!empty($_POST['add_menu_item'])) {
					$this->update_unterseite($id, $_POST);
					$this->update_menuedits($id, $_REQUEST['menuedits']);
					$menuid = (int)$_POST['menuedits_add'];
					$this->create_menu_edit($id, $menuid);
				}
				elseif (!empty($_POST['delete_menu_item'])) {
					$this->update_unterseite($id, $_POST);
					$this->update_menuedits($id, $_REQUEST['menuedits']);
					$menuid = (int)$_POST['delete_menu_item'];
					$this->delete_menu_edit($id, $menuid);
				}

				if ($_POST['action'] == 'save') {
					$this->update_menuedits($id, $_REQUEST['menuedits']);
					$result = $this->update_unterseite($id, $_POST);
					if ($result) {
						$url = '/interna/plugin.php?menuid='.$this->checked->menuid.'&template=partner_unterseiten/templates/backend.html';
						$this->diverse->http_redirect($url, 303);
					} else {
						$this->content->template['error'] = true;
					}
				}
				elseif ($_POST['action'] == 'delete') {
					$result = $this->delete_unterseite($_REQUEST['unterseiten_id']);
					if ($result) {
						$url = '/interna/plugin.php?menuid='.$this->checked->menuid.'&template=partner_unterseiten/templates/backend.html';
						$this->diverse->http_redirect($url, 303);
					}
				}
			}
		}

		if (!empty($_GET['copy_from'])) {
			$data = $this->get_unterseite_by_id($_GET['copy_from']);
			foreach ($data as $key => $value) {
				if ($key != 'unterseiten_id' and $key != 'unterseiten_name') {
					$this->content->template[$key] = $value;
				}
			}
		}

		if ($unterseiten_id) {
			$data = $this->get_unterseite_by_id($unterseiten_id);
			foreach ($data as $key => $value) {
				$this->content->template[$key] = $value;
			}
			$this->content->template['menuedits'] = $this->get_menuedits($unterseiten_id);
		}
	}

	/**
	 * @return array
	 */
	private function get_unterseiten()
	{
		$sql = sprintf('SELECT * FROM `%1$s` ORDER BY `unterseiten_name` ASC', $this->cms->tbname['plugin_partner_unterseiten']);
		$dbresult = $this->db->get_results($sql, ARRAY_A);
		$result = array();
		if ($dbresult) {
			foreach ($dbresult as $item) {
				$item['link'] = $this->get_partner_link($item);
				$result[$item['unterseiten_id']] = $item;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @return array
	 */
	private function get_menuedits($id)
	{
		$sql = sprintf('SELECT *
						FROM `%1$s` LEFT JOIN `%2$s` ON (`menuid`=`menuid_id` AND `lang_id`=%4$d)
						WHERE `unterseiten_id` = %3$d',
			$this->cms->tbname['plugin_partner_unterseiten_menuedits'],
			$this->cms->tbname['papoo_menu_language'],
			$id,
			$this->cms->lang_id);
		$dbresult = $this->db->get_results($sql, ARRAY_A);
		$result = array();
		if ($dbresult) {
			foreach ($dbresult as $item) {
				$result[$item['menuid']] = $item;
			}
		}
		return $result;
	}

	/**
	 * @return array
	 */
	private function get_default_menuedits()
	{
		$sql = sprintf('SELECT *
						FROM `%1$s` LEFT JOIN `%2$s` ON (`menuid`=`menuid_id` AND `lang_id`=%4$d)
						WHERE `unterseiten_id` = (SELECT unterseiten_id FROM `%5$s` WHERE `unterseiten_name` = \'\' LIMIT 1)',
			$this->cms->tbname['plugin_partner_unterseiten_menuedits'],
			$this->cms->tbname['papoo_menu_language'],
			$id,
			$this->cms->lang_id,
			$this->cms->tbname['plugin_partner_unterseiten']);
		$dbresult = $this->db->get_results($sql, ARRAY_A);
		$result = array();
		if ($dbresult) {
			foreach ($dbresult as $item) {
				$result[$item['menuid']] = $item;
			}
		}
		return $result;
	}

	/**
	 * @param int $id
	 * @param array $menuedits
	 * @return int
	 */
	private function update_menuedits($id, $menuedits)
	{
		$menuids = array();
		$action_cases = array();
		$menuid2_cases = array();
		if (!$menuedits) return 0;
		foreach ($menuedits as $key=>$value) {
			$menuids[] = (int)$key;
			if (empty($value['menuid2'])) {
				$action = '\'delete\'';
				$menuid2 = 'NULL';
			}
			else {
				$action = '\'replace\'';
				$menuid2 = (int)$value['menuid2'];
			}
			$action_cases[] = 'WHEN '.((int)$key).' THEN '.$action;
			$menuid2_cases[] = 'WHEN '.((int)$key).' THEN '.$menuid2;
		}

		if (!$menuids) {
			return 0;
		}

		$sql = sprintf('UPDATE `%1$s` SET
						`action` = CASE `menuid` %4$s END,
						`menuid2` = CASE `menuid` %5$s END
						WHERE `unterseiten_id` = %2$d
							AND `menuid` IN (%3$s)',
			$this->cms->tbname['plugin_partner_unterseiten_menuedits'],
			$id,
			implode(',', $menuids),
			implode(' ', $action_cases),
			implode(' ', $menuid2_cases));
		return $this->db->query($sql);
	}

	/**
	 * @param int $id_from
	 * @param int $id_to
	 * @return int
	 */
	private function clone_menuedits($id_from, $id_to)
	{
		$sql = sprintf('INSERT INTO `%1$s` (`unterseiten_id`, `menuid`, `action`, `menuid2`)
						SELECT %3$d, `menuid`, `action`, `menuid2` FROM `%1$s`
						WHERE `unterseiten_id` = %2$d',
			$this->cms->tbname['plugin_partner_unterseiten_menuedits'],
			$id_from,
			$id_to);
		return $this->db->query($sql);
	}

	/**
	 * @param int $id
	 * @return array|null
	 */
	private function get_unterseite_by_id($id)
	{
		$sql = sprintf('SELECT * FROM `%1$s` WHERE `unterseiten_id` = %2$d LIMIT 1', $this->cms->tbname['plugin_partner_unterseiten'], $id);
		$dbresult = $this->db->get_results($sql, ARRAY_A);
		return ($dbresult)?$dbresult[0]:null;
	}

	/**
	 * @param int $name
	 * @return array|null
	 */
	private function get_unterseite_by_name($name)
	{
		$sql = sprintf('SELECT * FROM `%1$s` WHERE `unterseiten_name` = \'%2$s\' LIMIT 1',
			$this->cms->tbname['plugin_partner_unterseiten'],
			$this->db->escape($name)
		);
		$dbresult = $this->db->get_results($sql, ARRAY_A);
		return ($dbresult)?$dbresult[0]:null;
	}

	/**
	 * @param array $data
	 * @return int|null ID des neuen Eintrags
	 */
	private function create_unterseite($data)
	{
		$result = $this->_insert_table('plugin_partner_unterseiten',
			array('unterseiten_name', 'unterseiten_default_menuid', 'unterseiten_bildwechsler_menuid', 'unterseiten_shop_redirect', 'unterseiten_shop_redirect_time'),
			$data
		);
		if ($result) {
			return (int)$this->db->get_var('SELECT LAST_INSERT_ID()');
		}
		else {
			return null;
		}
	}

	/**
	 * @param int $id
	 * @param int $menuid
	 * @return bool
	 */
	private function create_menu_edit($id, $menuid) {
		$result = $this->_insert_table('plugin_partner_unterseiten_menuedits',
			array('unterseiten_id', 'menuid', 'action'),
			array('unterseiten_id'=>$id, 'menuid'=>$menuid, 'action'=>'delete')
		);
		return $result;
	}

	/**
	 * @param int $id
	 * @param int $menuid
	 * @return bool
	 */
	private function delete_menu_edit($id, $menuid) {
		$sql = sprintf('DELETE FROM `%1$s` WHERE `unterseiten_id` = %2$d AND `menuid` = %3$d LIMIT 1',
			$this->cms->tbname['plugin_partner_unterseiten_menuedits'],
			$id,
			$menuid);
		return $this->db->query($sql);
	}

	/**
	 * @param int $id
	 * @param array $data
	 * @return bool Erfolg
	 */
	private function update_unterseite($id, $data)
	{
		// Ggf. einige leere Felder nullen
		foreach (array('unterseiten_default_menuid', 'unterseiten_bildwechsler_menuid', 'unterseiten_shop_redirect') as $key) {
			if (isset($data[$key]) and $data[$key] === '') {
				$data[$key] = null;
			}
		}
		// Änderungen speichern
		$result = $this->_update_table('plugin_partner_unterseiten',
			'unterseiten_id',
			array('unterseiten_default_menuid', 'unterseiten_bildwechsler_menuid', 'unterseiten_shop_redirect', 'unterseiten_shop_redirect_time'),
			$id,
			$data
		);

		return ($result)?$result:(!$this->db->last_error);
	}

	/**
	 * @param int $id
	 * @return bool
	 */
	private function delete_unterseite($id) {
		$sql = sprintf('DELETE FROM `%1$s` WHERE `unterseiten_id` = %2$d LIMIT 1', $this->cms->tbname['plugin_partner_unterseiten'], $id);
		return $this->db->query($sql);
	}

	/**
	 * @param string $table
	 * @param array $allowed_columns
	 * @param array $data
	 * @return bool
	 */
	protected function _insert_table($table, $allowed_columns, $data) {
		$insert_values = array();
		$insert_columns = array();
		foreach ($allowed_columns as $col) {
			if (isset($data[$col])) {
				$insert_columns[] = $col;
				if ($data[$col] === null)
					$valuestr = 'NULL';
				elseif (is_bool($data[$col]))
					$valuestr = ($data[$col])?'TRUE':'FALSE';
				else
					$valuestr = '\''.$this->db->escape((string)$data[$col]).'\'';
				$insert_values[] = $valuestr;
			}
			else {}//$insert_values[] = 'DEFAULT';
		}

		$sql = sprintf('INSERT INTO %s (`%s`) VALUES (%s)',
			$this->cms->tbname[$table],
			implode('`, `', $insert_columns),
			implode(', ', $insert_values));
		return $this->db->query($sql);
	}

	/**
	 * @param string $table
	 * @param string $idcol
	 * @param array $allowed_columns
	 * @param string $id
	 * @param array $data
	 * @param bool $orinsert
	 * @return bool
	 */
	protected function _update_table($table, $idcol, $allowed_columns, $id, $data, $orinsert=false) {
		$values = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $allowed_columns)) {
				if ($value === null)
					$valuestr = 'NULL';
				elseif (is_bool($value))
					$valuestr = ($value)?'TRUE':'FALSE';
				else
					$valuestr = '\''.$this->db->escape((string)$value).'\'';
				$values[] = '`'.$this->db->escape($key).'` = '.$valuestr;
			}
		}
		if (!is_int($id)) {
			$id = '\''.$this->db->escape((string)$id).'\'';
		}

		if (!$values) {
			return true;
		}

		if (!$orinsert) {
			$sql = sprintf('UPDATE %s SET %s WHERE `%s` = %s LIMIT 1',
				$this->cms->tbname[$table],
				implode(', ', $values), $idcol, $id);
		}
		else {
			$insert_values = array($id);
			$insert_columns = array();
			foreach ($allowed_columns as $col) {
				if (isset($data[$col])) {
					$insert_columns[] = $col;
					if ($data[$col] === null)
						$valuestr = 'NULL';
					elseif (is_bool($data[$col]))
						$valuestr = ($data[$col])?'TRUE':'FALSE';
					else
						$valuestr = '\''.$this->db->escape((string)$data[$col]).'\'';
					$insert_values[] = $valuestr;
				}
				else {}//$insert_values[] = 'DEFAULT';
			}
			$sql = sprintf('INSERT INTO %s (`%s`, `%s`) VALUES (%s) ON DUPLICATE KEY UPDATE %s',
				$this->cms->tbname[$table],
				$idcol,
				implode('`, `', $insert_columns),
				implode(', ', $insert_values),  implode(', ', $values));
		}
		return $this->db->query($sql);
	}

	/**
	 * @param $matches
	 * @return mixed
	 */
	public function link_replace_cb($matches) {
		$result = $matches[0];
		$path = $matches[1];
		$ok = false;
		if (strpos($path, '.htm') !== false
			or strpos($path, '.php') !== false
			or $path[strlen($path)-1] == '/') {
			if (strpos($path, '?partner=') === false and strpos($path, '&amp;partner=') === false) {
				if (strpos($path, '?') !== false) {
					$path .= '&amp;partner='.htmlspecialchars(urlencode($_SESSION['partner_unterseite']['unterseiten_name']));
				}
				else {
					$path .= '?partner='.htmlspecialchars(urlencode($_SESSION['partner_unterseite']['unterseiten_name']));
				}
				$result = str_replace($matches[1], $path, $result);
			}
		}
		return $result;
	}

	/**
	 * @param $menu
	 * @param $menuedits
	 * @return bool|void
	 */
	private function edit_menu(&$menu, $menuedits)
	{
		if (empty($menu))
			return false;
		$result = array();
		$delete_level = null;
		foreach ($menu as $menukey=>$menuitem) {
			if ($delete_level) {
				if ($menuitem['level'] >= $delete_level) {
					continue;
				}
				else {
					$delete_level = null;
				}
			}

			if (isset($menuedits[$menuitem['menuid']])) {
				$menuedit = $menuedits[$menuitem['menuid']];
				if ($menuedit['action'] == 'delete') {
					$delete_level = $menuitem['level']+1;
					// nicht in $result kopieren
				}
				elseif ($menuedit['action'] == 'replace') {
					foreach ($menu as $menukey2=>$menuitem2) {
						if ($menuitem2['menuid'] == $menuedit['menuid2']) {
							$menuitem2['level'] = $menuitem['level'];
							$result[] = $menuitem2;
							break;
						}
					}
					$delete_level = $menuitem['level']+1;
				}
				else {
					$result[] = $menuitem;
				}
			}
			else {
				$result[] = $menuitem;
			}
		}
		$menu = $result;
	}

	public function post_papoo()
	{
		global $bildwechsler;

		if (!defined('admin')) {
			$menuedits = null;
			if (!empty($_SESSION['partner_unterseite'])) {
				$menuedits = $this->get_menuedits($_SESSION['partner_unterseite']['unterseiten_id']);
			}
			else {
				// Bearbeitung Hauptseite
				$menuedits = $this->get_default_menuedits();
			}

			if ($menuedits) {
				$this->edit_menu($this->menu->data_front, $menuedits);
				$this->edit_menu($this->menu->data_front_complete, $menuedits);
				$this->edit_menu($this->menu->data_front_breadcrumb, $menuedits);
				$new_menu = $this->menu->menu_navigation_build("FRONT");
				$new_menu_complete = $this->menu->menu_navigation_build("FRONT_COMPLETE");
				$this->content->template['menu_data'] = $this->menu->menu_front = $new_menu;
				$this->content->template['menu_data_mit_fly'] = $this->menu->menu_front_complete = $new_menu_complete;
				$this->content->template['menu_data_breadcrumb'] = $this->menu->menu_navigation_build("BREADCRUMB");

				if (!empty($bildwechsler) and $_SESSION['partner_unterseite']['unterseiten_bildwechsler_menuid']) {
					$orig_menuid = $this->checked->menuid;
					$this->checked->menuid = $_SESSION['partner_unterseite']['unterseiten_bildwechsler_menuid'];
					$bildwechsler->switch_front();
					$this->checked->menuid = $orig_menuid;
				}
			}
		}

	}

	public function output_filter()
	{
		global $output, $smarty;
		if (!empty($_SESSION['partner_unterseite'])) {
			$base = $this->get_papoo_base_url();
			$output = preg_replace_callback('@(?:href|action)=["\'](?:'.$base.')?(/[^\'"#]*)(?:#[^\'"]*)?[\'"]@i', array($this, 'link_replace_cb'), $output);
			$output = preg_replace_callback('@(?:href|action)=["\']([^:\'"?#]*(?:\?[^\'"#]*))(?:#[^\'"]*)?[\'"]@i', array($this, 'link_replace_cb'), $output);

			if ($this->checked->ps_act == 'checkout_fertig') {
				$unterseite = $_SESSION['partner_unterseite'];
				if ($unterseite['unterseiten_shop_redirect']) {
					// Link zusammensetzen
					$smarty->assign('unterseiten_shop_redirect', $unterseite['unterseiten_shop_redirect']);
					$smarty->assign('unterseiten_shop_redirect_time', $unterseite['unterseiten_shop_redirect_time']);
					$link = $smarty->fetch("../../plugins/partner_unterseiten/templates/shop_backlink.html");
					// Link einbauen
					$output = preg_replace('@(<div[^>]*checkout_process_div_fertig[^>]*>.*?)(</(?:div)>)@is', '\1'.$link.'\2', $output);

					if ($unterseite['unterseiten_shop_redirect_time']) {
						$headercontent = sprintf('%d;url=%s', $unterseite['unterseiten_shop_redirect_time'], $unterseite['unterseiten_shop_redirect']);
						@header('Refresh: '.$headercontent);
						$meta = sprintf('<meta http-equiv="refresh" content="%s" />', htmlspecialchars($headercontent));
						$output = preg_replace('@(</head>)@is', $meta.'\1', $output);
					}
				}
			}
		}
	}
}

$partner_unterseiten = new partner_unterseiten_class();
