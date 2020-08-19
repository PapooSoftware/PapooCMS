<?php

/**
 * Class bootstrapdropdown_class
 */
class bootstrapdropdown_class
{
	/**
	 * bootstrapdropdown_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler Papoo-Objekte
		global $content, $checked, $db, $user, $cms;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->db = &$db;
		$this->cms = &$cms;
		$this->user = &$user;
		$this->make_bootstrapdropdown();
	}

	function make_bootstrapdropdown()
	{
		global $menu;
		global $cms;
		global $content;

		if (!stristr($_SERVER["HTTP_USER_AGENT"],"MSIE 6")) {
			$cms->anzeig_menue_komplett = 1;
			$menu->make_menu();
			$content->template['menu_data_mit_fly'] = $content->template['menu_data'];
			$cms->anzeig_menue_komplett = 0;
			$menu->make_menu();
		}
		else {
			$cms->anzeig_menue_komplett = 0;
			$menu->make_menu();
			$content->template['menu_data_mit_fly'] = $content->template['menu_data'];
			$cms->anzeig_menue_komplett = 0;
			$menu->make_menu();
		}

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			#$this->disable_show_all_menus();
			#$this->activate_jquery();

			// �berpr�ft, ob in der aktuellen URL der String "bootstrapdropdown_back.html" vorkommt
			//if (strpos("XXX" . $template, "bootstrapdropdown_back.html"))
			{
				// print_r($this->cms->tbname);

				//�berpr�ft, ob der "submit"-Button geklickt wurde
				//if ($this->checked->submit)
				{
					# $this->write_into_db();
				}
				#$this->read_from_db();
			}
		}
	}

	function activate_jquery()
	{
		//SQL-Anweisung f�r das Auslesen des H�kchens
		$query = sprintf("SELECT config_jquery_aktivieren_label FROM %s", $this->cms->tbname['papoo_config']);

		$result = $this->db->get_results($query);

		//$value ist 0, falls das h�kchen nicht gestetzt ist und 1, falls doch
		$value = $result[0]->config_jquery_aktivieren_label;

		//Falls das H�kchen gesetzt ist, wird es entfernt
		if ($value == 0) {
			$query = sprintf("UPDATE %s 
                            SET config_jquery_aktivieren_label='1'", $this->cms->tbname['papoo_config']);
			$this->db->query($query);
		}
	}

	function output_filter()
	{

	}
}

$bootstrapdropdown = new bootstrapdropdown_class();
