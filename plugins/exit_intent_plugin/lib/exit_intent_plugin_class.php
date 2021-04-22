<?php

/**
 * Class exit_intent_plugin_class
 */
class exit_intent_plugin_class {
	/**
	 * exit_intent_plugin_class constructor.
	 */
	function __construct()
	{
		// Einbindung der globalen Papoo-Objekte
		global $checked, $db, $db_praefix, $content, $menu, $intern_menu, $cms, $db_abs;
		$this->checked = &$checked;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->content = &$content;
		$this->menu = &$menu;
		$this->intern_menu = &$intern_menu;
		$this->cms = $cms;
		$this->db_abs = &$db_abs;

		$this->make_exit_intent_plugin();
	}

	/**
	 * exit_intent_plugin_class::make_exit_intent_plugin()
	 *
	 * @return void
	 */
	function make_exit_intent_plugin()
	{
		// FRONTEND:
		if (!defined("admin")) {
			$this->switch_front();
		}

		// BACKEND:
		if (defined("admin")) {
			global $template;
			global $user;
			$user->check_intern();

			$this->content->template['exit_intent_plugin_css_path'] =
				PAPOO_WEB_PFAD . '/plugins/exit_intent_plugin/css';

			$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid
				. "&template=exit_intent_plugin/templates/back_edit.html";
			$menu_link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid;
			$eintragmakeartikelfertig = $this->checked->eintragmakeartikelfertig;

			$this->content->template['ei_link'] = $link;
			$this->content->template['menu_link'] = $menu_link;
			$this->content->template['eintragmakeartikelfertig'] = $eintragmakeartikelfertig;

			$this->content->template['cattext_ar'] = $this->get_cattext_ar($this->checked->ei_id);

			$this->get_liste_menu();

			if (strpos("XXX".$template, "exit_intent_plugin/templates/back_edit.html")) {
				if (is_numeric($this->checked->ei_id) ) {
					$this->content->template['singleItem'] =
						$this->get_single_exit_intent_for_id($this->checked->ei_id, '');
				}
				$this->switch_back($this->checked->updateExitIntent);
			}

			if (strpos("XXX" . $template, "exit_intent_plugin/templates/back_list.html")) {
				//daten holen
				$this->content->template['list'] = $this->get_list();
			}

			if (strpos("XXX" . $template, "exit_intent_plugin/templates/back_list.html") && $this->checked->cop_id) {
				$this->copy_exit_intent($this->checked->cop_id);
			}
		}
	}

	public function output_filter()
	{
		global $output;
		$output = str_replace("</body>", $this->output_script . "</body>", $output);
	}

	/**
	 * @param string $action
	 */
	function switch_back($action = "")
	{
		switch($action)
		{
		case "Speichern":
			$this->exit_intent_save();
			break;

		case "Update":
			$this->exit_intent_update();
			break;

		case "Löschen":
			$this->exit_intent_delete();
			break;
		}
	}

	/**
	 * @param string $action
	 */
	function switch_front($action = "")
	{
		$ei_id = $this->get_exit_intent_id_for_menu_id($this->checked->menuid);

		if ($ei_id != '') {
			$this->content->template['singleItem'] = $this->get_single_exit_intent_for_id_front($ei_id);
			$this->create_exit_intent($this->content->template['singleItem']);
		}
		else {
			$ei_id = $this->get_exit_intent_id_for_menu_id('all');
			$this->content->template['singleItem'] = $this->get_single_exit_intent_for_id_front($ei_id);
			$this->create_exit_intent($this->content->template['singleItem']);
		}
	}

	/**
	 * @param int $menu_id
	 * @return mixed
	 */
	function get_exit_intent_id_for_menu_id($menu_id = 0)
	{
		$sql = sprintf(
			'SELECT ei_id FROM %1$s WHERE menu_id = \'%2$s\';',
			$this->db_praefix . "plugin_exit_intent_plugin_lookup",
			$menu_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		return $result[0]['ei_id'];
	}

	/**
	 * @return array|void
	 */
	function get_list()
	{
		$sql = sprintf('SELECT * FROM %1$s;', $this->db_praefix . "plugin_exit_intent_plugin");
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param int $id
	 * @return array|void
	 */
	function get_cattext_ar($id = 0)
	{
		$sql = sprintf(
			'SELECT * FROM %s WHERE ei_id = \'%d\';',
			$this->db_praefix . "plugin_exit_intent_plugin_lookup",
			$id
		);

		return $this->db->get_results($sql);
	}

	/**
	 * @param int $id
	 * @param string $copy
	 * @return array|void
	 */
	function get_single_exit_intent_for_id($id = 0, $copy = "")
	{
		$sql = sprintf(
			'SELECT * FROM %s WHERE ei_id = \'%d\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$id
		);

		$result = $this->db->get_results($sql, ARRAY_A);
		if ($copy != 'COPY'){
			foreach ($result['0'] as $k => $v) {
				if ($k == 'ei_link' || $k == 'ei_css_datei_link')
					$result['0'][$k] = "nobr:" . $v;
			}
		}

		return $result;
	}

	/**
	 * @param string $id
	 * @return array|void
	 */
	function get_single_exit_intent_for_id_front($id="")
	{

		$sql = sprintf(
			'SELECT * FROM %s WHERE ei_id = \'%d\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$id
		);

		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @return mixed
	 */
	function get_last_exit_intent_id()
	{
		$sql = sprintf('SELECT MAX(ei_id) FROM %1$s;', $this->db_praefix . "plugin_exit_intent_plugin");
		$result = $this->db->get_results($sql, ARRAY_A);
		return max($result[0]);
	}

	/**
	 * @param int $cop_id
	 */
	function copy_exit_intent($cop_id = 0)
	{
		$item_to_copy = $this->get_single_exit_intent_for_id($cop_id,'COPY');
		$last_id = (int)$this->get_last_exit_intent_id()+1;//+1 to make new id

		$sql = sprintf(
			'INSERT INTO %1$s 
			SET
                ei_link = \'%2$s\', ei_id = \'%3$s\', ei_hoehe = \'%4$s\', ei_breite = \'%5$s\',
                ei_delay = \'%6$s\', ei_auslaufdatum = \'%7$s\', ei_css_datei_link = \'%8$s\', 
                ei_cookie_name = \'%9$s\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$item_to_copy[0]['ei_link'],
			$last_id,
			$item_to_copy[0]['ei_hoehe'],
			$item_to_copy[0]['ei_breite'],
			$item_to_copy[0]['ei_delay'],
			$item_to_copy[0]['ei_auslaufdatum'],
			$item_to_copy[0]['ei_css_datei_link'],
			'bioep_shown_'. (md5($item_to_copy[0]['ei_link']))
		);

		$this->db->query($sql);

		$this->set_menu_for_copy_exit_intent($cop_id,$last_id);

		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid
			. "&template=exit_intent_plugin/templates/back_list.html";
		$this->reload($location_url);
	}

	/**
	 * @param int $cop_id
	 * @param int $last_id
	 */
	function set_menu_for_copy_exit_intent($cop_id = 0, $last_id = 0)
	{
		//menu liste für kopie holen
		$sql = sprintf(
			'SELECT menu_id FROM %1$s WHERE ei_id = \'%2$d\';',
			$this->db_praefix . "plugin_exit_intent_plugin_lookup",
			$cop_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		//neues menu schreiben
		foreach ($result as $result_id){
			$sql = sprintf(
				'INSERT INTO %s SET ei_id = \'%s\', menu_id = \'%s\';',
				$this->db_praefix . "plugin_exit_intent_plugin_lookup",
				$last_id,
				$result_id['menu_id']
			);
			$this->db->query($sql, ARRAY_A);
		}
	}

	function get_liste_menu()
	{
		$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");

		// 1. Auswahl-Liste Men�-Punkte aufbauen
		$this->content->template['menulist_data'] = $this->menu->menu_data_read("FRONT_PUBLISH", $this->replangid);

		// 2. ID des ausgew�hlten Men�-Punktes bestimmen
		if (!empty($this->checked->formmenuid)) {
			$menuid = $this->checked->formmenuid;
		}
		else {
			$menuid = 1;
		}
		$this->content->template['formmenuid'] = $menuid;

		$this->content->template['catlist_data'] = array(array("catid" => -1));
		$this->content->template['no_categories'] = 1;
	}

	/**
	 * @param $exit_intent_data
	 */
	function create_exit_intent($exit_intent_data){

		$exit_intent_script =
			'<div class="modul" id="mod_exit_intent_plugin">
                 <script type="text/javascript" src="' . PAPOO_WEB_PFAD . '/plugins/exit_intent_plugin/js/bioep.js"></script>
                 <script type="text/javascript">
                    bioEp.init({
                        width:' . $exit_intent_data[0]['ei_breite'] . ',
                        height:' . $exit_intent_data[0]['ei_hoehe'] . ',
                        html: \'' .
			str_replace(array("\r\n", "\r", "\n"), '', $exit_intent_data[0]['ei_link'])
			. '\',
                        delay: ' . $exit_intent_data[0]['ei_delay'] . ',
                        showOnDelay: false,
                        cookieExp: ' . $exit_intent_data[0]['ei_auslaufdatum'] . ',
                        cookie_name: \'' . $exit_intent_data[0]['ei_cookie_name'] . '\',
                        css: \'' .
			str_replace(array("\r\n", "\r", "\n"), '', $exit_intent_data[0]['ei_css_datei_link'])
			. '\'
                     });
                 </script>
             </div>';

		$this->output_script = $exit_intent_script;
	}
	function exit_intent_save()
	{
		$name = $this->checked->exit_intent_ei_name;
		$id = $this->checked->ei_id;
		$link_adresse = $this->checked->exit_intent_ei_linkadresse;
		$hoehe = $this->checked->exit_intent_ei_hoehe_popup;
		$breite = $this->checked->exit_intent_ei_breite_popup;
		$delay = $this->checked->exit_intent_ei_delay;
		$auslaufdatum = $this->checked->exit_intent_ei_auslaufdatum;
		$css_link = $this->checked->exit_intent_ei_css;
		$cookie_name = 'bioep_shown_';

		$sql = sprintf(
			'INSERT INTO %1$s 
        	SET
                ei_link=\'%2$s\', ei_id=\'%3$s\', ei_hoehe=\'%4$s\', ei_breite=\'%5$s\',
                ei_delay=\'%6$s\', ei_auslaufdatum=\'%7$s\', ei_css_datei_link=\'%8$s\', ei_name=\'%9$s\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$link_adresse,
			$id,
			$hoehe,
			$breite,
			$delay,
			$auslaufdatum,
			$css_link,
			$name
		);
		$this->db->query($sql);

		//letzte gespeicherte id holen um den cookie_name mit der id zu ergänzen
		$last_id = $this->get_last_exit_intent_id();
		$cookie_name_ext = md5($link_adresse);
		$cookie_name = $cookie_name.$cookie_name_ext;

		$sql = sprintf(
			'UPDATE %1$s SET ei_cookie_name = \'%2$s\' WHERE ei_id = \'%3$d\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$cookie_name,
			$last_id
		);
		$this->db->query($sql);

		//menu id's setzen
		$menu_ar = $_POST["inhalt_ar"]["cattext_ar"];

		foreach ($menu_ar as $menu_id){
			$sql = sprintf(
				'INSERT INTO %1$s SET ei_id = \'%2$s\', menu_id = \'%3$s\';',
				$this->db_praefix . "plugin_exit_intent_plugin_lookup",
				$last_id,
				$menu_id
			);
			$this->db->query($sql);
		}

		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid
			. "&template=exit_intent_plugin/templates/back_edit.html"
			. "&ei_id=" . $this->get_last_exit_intent_id() . "&eintragmakeartikelfertig=ok";

		$this->reload($location_url);
	}

	function exit_intent_update()
	{
		$id = $this->checked->ei_id;
		$name = $this->checked->exit_intent_ei_name;
		$link_adresse = $this->checked->exit_intent_ei_linkadresse;
		$hoehe = $this->checked->exit_intent_ei_hoehe_popup;
		$breite = $this->checked->exit_intent_ei_breite_popup;
		$delay = $this->checked->exit_intent_ei_delay;
		$auslaufdatum = $this->checked->exit_intent_ei_auslaufdatum;
		$css_link = $this->checked->exit_intent_ei_css;
		$cookie_name = $this->checked->exit_intent_ei_cookie_name;

		$sql = sprintf(
			'UPDATE %1$s 
        	SET
                ei_link = \'%2$s\', ei_name = \'%3$s\', ei_hoehe = \'%4$s\', ei_breite = \'%5$s\',
                ei_delay = \'%6$s\', ei_auslaufdatum = \'%7$s\', ei_css_datei_link = \'%8$s\', ei_cookie_name = \'%9$s\'
			WHERE ei_id = \'%10$d\';',
			$this->db_praefix . "plugin_exit_intent_plugin",
			$link_adresse,
			$name,
			$hoehe,
			$breite,
			$delay,
			$auslaufdatum,
			$css_link,
			$cookie_name,
			$id
		);

		$this->db->query($sql);

		//altes menu löschen
		$sql = sprintf(
			'DELETE FROM %1$s WHERE ei_id = \'%2$d\';',
			$this->db_praefix . "plugin_exit_intent_plugin_lookup",
			$id
		);
		$this->db->query($sql);

		//neues menu in lookup schreiben
		$menu_ar = $_POST["inhalt_ar"]["cattext_ar"];

		foreach ($menu_ar as $menu_id){
			$sql = sprintf(
				'INSERT INTO %1$s SET ei_id=\'%2$s\', menu_id=\'%3$s\';',
				$this->db_praefix . "plugin_exit_intent_plugin_lookup",
				$id,
				$menu_id
			);
			$this->db->query($sql);
		}

		$this->content->template['singleItem'] = $this->get_single_exit_intent_for_id($this->checked->ei_id, '');
		$this->content->template['eintragmakeartikelfertig'] = "ok";
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid
			. "&template=exit_intent_plugin/templates/back_edit.html&ei_id=" . $id . "&eintragmakeartikelfertig=ok";

		$this->reload($location_url);
	}

	function exit_intent_delete()
	{
		$id = $this->checked->ei_id;
		$sql = sprintf(
			'DELETE FROM %1$s WHERE ei_id = \'%2$d\';',
			$this->db_praefix."plugin_exit_intent_plugin",
			$id
		);
		$this->db->query($sql);

		//menu lookup einträge löschen
		$sql = sprintf('DELETE FROM %1$s WHERE ei_id = \'%2$d\';',
			$this->db_praefix . "plugin_exit_intent_plugin_lookup",
			$id
		);
		$this->db->query($sql);

		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid
			. "&template=exit_intent_plugin/templates/back_list.html";
		$this->reload($location_url);
	}

	/**
	 * Seite neu laden
	 *
	 * @param string $location_url
	 */
	function reload($location_url = "")
	{
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter']
				. '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}
}

$exit_intent_plugin_class= new exit_intent_plugin_class();
