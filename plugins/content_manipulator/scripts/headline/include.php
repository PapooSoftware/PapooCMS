<?php

/**
 * Headline script - Stellt Unterartikel(-menüs) in einer Liste (Baumstruktur) oder Tabelle dar.
 * @author Christoph Zimmer
 * @date 13.11.2014
 */
class CM_Headline
{
	//Speichert ul-html
	private $list;
	//Speichert table-html
	private $table;
	//ul oder table ausgeben
	private $type;
	//Hierarchie der aktuellen menuid um climbLevels hochgehen
	//und Headline von dort beginnen zu generieren
	private $climbLevels;
	//Spracheinträge für alle Menüpunkte in der aktuellen Sprache
	private $sys_menus;

	/**
	 * CM_Headline constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db, $output;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->output = &$output;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#cm_headline")) {
				$enable = false;
				$this->type = null;
				$this->climbLevels = 0;
				$this->offset = 0;
				$this->chapter = false;
				//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
				if(preg_match_all("/#cm_headline_([^#]*)#/", $output, $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches['1'] as $val) {
						//Die Unterstriche rausholen
						$opt = explode("_", $val);

						//Platzhalter #cm_headline_on#
						if(strcmp($opt[0], "on") == 0) {
							$enable = true;
						}
						//Platzhalter #cm_headline_table#
						else if($opt[0] === "table") {
							$this->type = "table";
						}
						//Platzhalter #cm_headline_list#
						else if($opt[0] === "list") {
							$this->type = "list";
						}
						//Platzhalter #cm_headline_off#
						else if (strcmp($opt[0], "off") == 0) {
							$enable = false;
							break;
						}
						else if(strcmp($opt[0], "climb") == 0) {
							if(sizeof($opt) > 1) {
								$this->climbLevels = (int) $opt[1];
							}
						}
						//noch nicht in Verwendung
						else if (strcmp($opt[0], "offset") == 0) {
							if(sizeof($opt) > 1) {
								$offset = (int)$opt[1];
								if(is_numeric($offset)) {
									$this->offset = --$offset;
								}
							}
						}
						//noch nicht in Verwendung
						else if (strcmp($opt[0], "chapter") == 0) {
							$this->chapter = true;
							if(sizeof($opt) > 1) {
								$chapter = (int)$opt[1];
								if(is_numeric($chapter)) {
									$this->chapter = $chapter;
								}
							}
						}
					}
					if($enable) {
						//Ausgabe erstellen
						$this->create_headline();
					}
				}
				//Die übrigen Platzhalter entfernen
				$this->output = preg_replace("/#cm_headline[^#]*#/", "", $this->output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript Headline; Einbau an beliebiger Stelle.";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man in einem Artikel alle Unterartikel in einem Inhaltsverzeichnis (Baumstruktur) angezeigen lassen.<br/>Verwendete Platzhalter:<br /><table><tr><td><strong>#cm_headline_on#</strong></td><td>Aktiviert und platziert das Inhaltsverzeichnis.</td></tr><tr><td><strong>#cm_headline_off#</strong></td><td>Schaltet das Inhaltsverzeichnis ab. (&Uuml;berschreibt #cm_headline_on#)</td></tr><tr><td><strong>#cm_headline_list#</strong></td><td>Die Darstellung erfolgt als Liste. (Standard)</td></tr><tr><td><strong>#cm_headline_table#</strong></td><td>Die Darstellung erfolgt als Tabelle.</td></tr><tr><td><strong>#cm_headline_climb_X#</strong></td><td>Die Seitenhierarchie wird vom aktuellen Men&uuml;punkt aus um X Men&uuml;punkte hochgeklettert und das Inhaltsverzeichnis von dort aus angezeigt. <strong>X = {0,1,2,3,...}</strong></td></tr></table>";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	private function showErrors()
	{
		ini_set("display_errors", 1);
		error_reporting(-1);
	}

	/**
	 * Wandelt eine Ganzzahl in die römische Schreibweise um.
	 * @see http://stackoverflow.com/a/15023547
	 *
	 * @param int $integer
	 * @param bool $upcase
	 *
	 * @return string
	 */
	private function romanic_number($integer, $upcase = true)
	{
		if(!is_int($integer)) {
			return $integer;
		}

		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
		$return = '';
		while($integer > 0) {
			foreach($table as $rom=>$arb) {
				if($integer >= $arb) {
					$integer -= $arb;
					$return .= $rom;
					break;
				}
			}
		}
		return $return;
	}

	/**
	 * @param $menuid
	 * @param int $levels
	 *
	 * @return mixed
	 */
	private function climb_up_menu_x_levels($menuid, $levels = 1)
	{
		$levels = (int)abs($levels);

		if($levels == 0) {
			return $menuid;
		}
		else {
			$sql = sprintf("SELECT * FROM `%s` WHERE `menuid`=%d;",
				$this->cms->tbname['papoo_me_nu'],
				$menuid
			);
			$result = $this->db->get_row($sql, ARRAY_A);
			if($result['untermenuzu'] == 0) {
				return $menuid;
			}
			else {
				return $this->climb_up_menu_x_levels($result['untermenuzu'], $levels-1);
			}
		}
	}

	/**
	 * @param $menuid
	 * @param int $level
	 *
	 * @return mixed
	 */
	private function climb_up_menu_to_level($menuid, $level = 0)
	{
		//Infos zu aktuellem Menü, insbesondere des Levels, herausholen
		$sql = sprintf("SELECT * FROM `%s` WHERE `menuid`=%d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_row($sql, ARRAY_A);
		//Im spezifizierten Level angekommen
		if($result['level'] <= $level) {
			return $menuid;
		}
		//Klettere um einen Level weiter nach oben
		else {
			return $this->climb_up_menu_to_level($result['untermenuzu'], $level);
		}
	}

	private function load_menus()
	{
		$this->sys_menus = array();

		$sql = sprintf("SELECT * FROM `%s` WHERE `lang_id`=%d ORDER BY `menuid_id`;",
			$this->cms->tbname['papoo_menu_language'],
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if(is_array($result)) {
			foreach($result as $menu) {
				$this->sys_menus[$menu['menuid_id']] = $menu;
			}
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|void
	 */
	private function get_articles($menuid)
	{
		$sql = sprintf("SELECT * FROM `%s` AS t1 LEFT OUTER JOIN `%s` AS t2 ON t1.`lan_repore_id`=t2.`lart_id` WHERE t2.`lcat_id`=%d AND t1.`lang_id`=%d ORDER BY t2.`lart_order_id` ASC;",
			$this->cms->tbname['papoo_language_article'],
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid,
			$this->cms->lang_id
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param $menuid
	 *
	 * @return array|void
	 */
	private function get_submenus($menuid)
	{
		$sql = sprintf("SELECT * FROM `%s` AS t1 LEFT OUTER JOIN `%s` AS t2 ON t1.`menuid`=t2.`menuid_id` WHERE t1.`untermenuzu`=%d AND t2.`lang_id`=%d ORDER BY t1.`order_id` ASC;",
			$this->cms->tbname['papoo_me_nu'],
			$this->cms->tbname['papoo_menu_language'],
			$menuid,
			$this->cms->lang_id
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param $menuid
	 * @param $nth
	 */
	private function menu_offset($menuid, &$nth)
	{
		if(!is_array($nth)) {
			$nth = array();
		}
		$sql = sprintf("SELECT t1.`menuid`, t1.`untermenuzu`, t1.`order_id`, t3.`lan_article` " .
			"FROM `%s` AS t1 " .
			"INNER JOIN `%s` AS t2 ON t1.`menuid`=t2.`lcat_id` " .
			"INNER JOIN `%s` AS t3 ON t3.`lan_repore_id`=t2.`lart_id` " .
			"WHERE t1.`untermenuzu`=(SELECT `untermenuzu` FROM `%s` WHERE `menuid`=%d) " .
			"AND t1.`order_id`<=(SELECT `order_id` FROM `%s` WHERE `menuid`=%d) " .
			"AND t3.`lang_id`=%d " .
			"ORDER BY t1.`order_id` DESC;",
			$this->cms->tbname['papoo_me_nu'],
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->cms->tbname['papoo_language_article'],
			$this->cms->tbname['papoo_me_nu'],
			$menuid,
			$this->cms->tbname['papoo_me_nu'],
			$menuid,
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if(is_array($result) && sizeof($result) > 0) {
			if(preg_match('/#cm_headline_base_(\d*)/', $result['lan_article'], $match)) {
				if(is_array($match)) {
					array_unshift($nth, $match[1]);
					return;
				}
			}
			array_unshift($nth, sizeof($result));
			if($result[0]['untermenuzu'] > 0) {
				$this->menu_offset($result[0]['untermenuzu'], $nth);
			}
		}
	}

	/**
	 * Diese Funktion bildet den Hauptmechanismus zur Erstellung des HTML Codes.
	 *
	 * @param int $menuid
	 * @param int $level How many levels deep from the beginning menu
	 */
	private function process_articles($menuid, $level = 0)
	{
		$this->list[] = "<ol".($level==0?" id=\"headline-box\"":"").">";
		$this->table[] = "<table".($level==0?" id=\"headline-box\"":"")."><tbody>";

		$articles = $this->get_articles($menuid);

		if($level > -1 && (is_array($articles) == false || count($articles) > 1)) {
			$url = $this->cms->mod_free == 1
				? PAPOO_WEB_PFAD.$this->sys_menus[$menuid]['url_menuname']
				: PAPOO_WEB_PFAD."/?menuid=$menuid";

			$this->list[] = sprintf("<li><a href=\"%s\">%s</a></li>",
				$url,
				$this->sys_menus[$menuid]['menuname']
			);

			$this->table[] = sprintf("<tr><td><a href=\"%s\">%s</a></td></tr>",
				$url,
				$this->sys_menus[$menuid]['menuname']
			);
		}

		//Artikel des aktuellen Menüpunktes bearbeiten
		if(is_array($articles) && count($articles) > 0 && ($level > -1 || count($articles) > 1)) {
			foreach($articles as $article) {
				$url = $this->cms->mod_free == 1
					? PAPOO_WEB_PFAD.$article['url_header']
					: PAPOO_WEB_PFAD."/?menuid=$menuid&reporeid={$article["lan_repore_id"]}";

				$this->list[] = sprintf("<li><a href=\"%s\">%s</a></li>",
					$url,
					$article['header']
				);

				$this->table[] = sprintf("<tr><td><a href=\"%s\">%s</a></td></tr>",
					$url,
					$article['header']
				);
			}
		}

		$submenus = $this->get_submenus($menuid);
		$menuHasSubmenus = is_array($submenus) && count($submenus) > 0;

		//Untermenüs bearbeiten
		if($menuHasSubmenus) {
			foreach($submenus as $key => $menu) {
				$this->list[] = "<li>";
				$this->table[] = "<tr><td>";
				$this->process_articles($menu["menuid"], $level+1);
				$this->list[] = "</li>";
				$this->table[] = "</td></tr>";
			}
		}

		$this->list[] = "</ol>";
		$this->table[] = "</tbody></table>";
	}

	private function create_headline()
	{
		$this->load_menus();

		$this->list = array();
		$this->table = array();

		$menuid = $this->climb_up_menu_x_levels($this->checked->menuid, $this->climbLevels);

		$this->process_articles($menuid);

		//Platzhalter durch Liste ersetzen
		$this->output = preg_replace("/#cm_headline_on[^#]*#/", implode("\n", (($this->type === "table") ? $this->table : $this->list)), $this->output, 1);
	}
}

$cm_headline=new CM_Headline();
