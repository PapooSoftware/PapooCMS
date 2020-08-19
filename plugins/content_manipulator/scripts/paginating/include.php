<?php

/**
 * Paginating script - Ermöglicht die Navigation zum vorigen
 *                     als auch nächsten Artikel.
 * @author Christoph Zimmer
 * @date 07.11.2014
 * */
class CM_Paginating
{
	/**
	 * CM_Paginating constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#cm_paginating")) {
				//Ausgabe erstellen
				$this->create_paginating($output);
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
		$this->content->template['plugin_cm_head']['de'][] = "Skript Paginating; Einbau an beliebiger Stelle.";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][] =
			"Mit diesem kleinen Skript kann man aus dem aktuellen Artikel in den vorigen bzw. n&auml;chsten wechseln.<br/>
			Verwendete Platzhalter:<table><tr><td><strong>#cm_paginating_on#</strong></td><td>Aktiviert und platziert die Anzeige.</td>
			</tr><tr><td><strong>#cm_paginating_off#</strong></td><td>Deaktiviert die Anzeige. (&Uuml;berschreibt #cm_paginating_on#)</td>
			</tr><tr><td><strong>#cm_paginating_type_prev#</strong></td><td>Zeigt nur einen Link zum vorigen Artikel.</td></tr>
			<tr><td><strong>#cm_paginating_type_next#</strong></td><td>Zeigt nur einen Link zum n&auml;chsten Artikel.</td></tr></table>";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param $output
	 */
	private function create_paginating(&$output)
	{
		$enable = null;
		$this->type = null;

		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#cm_paginating(.*?)#|", $output, $ausgabe, PREG_PATTERN_ORDER);
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			if(sizeof($ndat) >= 2) {
				$switch = $ndat[1];

				if (strcmp($switch, "on") == 0) {
					if ($enable === null) {
						$enable = true;
					}
				}
				else if (strcmp($switch, "off") == 0) {
					$enable = false;
				}
				else if ($switch === "type" && sizeof($ndat) >= 3) {
					$this->type = $ndat[2];
				}
			}
		}

		//=======================================
		// HIER GEHT ES LOS!!!
		//=======================================
		if ($enable === true) {
			$reporeid = $this->checked->reporeid;
			$menuid = $this->checked->menuid;
			$langid = (int)$this->cms->lang_id;

			//Hole die relevanten Artikel-IDs
			$prevReporeid = (int)$this->prevArticle($menuid, $reporeid);
			$nextReporeid = (int)$this->nextArticle($menuid, $reporeid);

			//Hole sprechende URLs
			$result = $this->db->get_results(
				"SELECT lan_repore_id AS article_id, url_header AS free_url, lcat_id AS menu_id ".
				"FROM {$this->cms->tbname["papoo_language_article"]} ".
				"LEFT JOIN {$this->cms->tbname["papoo_lookup_art_cat"]} ON lan_repore_id = lart_id ".
				"WHERE lan_repore_id IN ({$prevReporeid}, {$nextReporeid}) AND `lang_id` = {$langid}", ARRAY_A);

			$urls = [];
			foreach($result as $tuple) {
				$urls[$tuple["article_id"]] = [
					"raw" => PAPOO_WEB_PFAD."/?menuid={$tuple["menu_id"]}&reporeid={$tuple["article_id"]}",
					"free" => PAPOO_WEB_PFAD."{$tuple["free_url"]}"
				];
			}

			$prevUrl = $this->cms->mod_free == 1 ? $urls[$prevReporeid]["free"] : $urls[$prevReporeid]["raw"];
			$nextUrl = $this->cms->mod_free == 1 ? $urls[$nextReporeid]["free"] : $urls[$nextReporeid]["raw"];

			$prevLink = "<a href=\"$prevUrl\">&lt; Einen Artikel zur&uuml;ck</a>";
			$nextLink = "<a href=\"$nextUrl\">Einen Artikel weiter &gt;</a>";

			$paginating = $this->type === "prev"
				? $prevLink
				: ($this->type === "next"
					? $nextLink
					: "$prevLink | $nextLink");

			//URL hier anpassen für lokale Installationen mit anderem Pfad zu Seite
			$output =  str_replace("#cm_paginating_on#", $paginating, $output);
		}
		//Jetzt noch jedes andere Vorkommen eines Platzhalters dieser Klasse entfernen und fertig.
		$output = preg_replace("|#cm_paginating(.*?)#|", "", $output);

		//Geänderten Inhalt zurückgeben
		#return $inhalt;
	}

	/*========================================
	 * ARTICLE STUFF
	 *=====================================*/
	/**
	 * Diese Funktion geht einen Artikel zurück.
	 *
	 * @param $menuid int
	 * @param $reporeid int
	 *
	 * @return int|array
	 */
	private function prevArticle($menuid, $reporeid)
	{
		$sql = sprintf("SELECT `lart_order_id` FROM `%s` WHERE `lcat_id` = %d AND `lart_id` = %d;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid,
			$reporeid
		);
		$orderid = $this->db->get_var($sql);

		//Hole vorherigen Artikel.
		$sql = sprintf("SELECT `lart_id` FROM `%s` WHERE `lcat_id` = %d AND `lart_order_id` < %d ORDER BY `lart_order_id` DESC LIMIT 1;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid,
			$orderid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Vorhergehendes Menü in gleicher Ebene gefunden.
			//Dieses Menü in die Tiefe durchsuchen.
			return $result;
		}
		else {
			return $this->lastArticleInMenu($this->prevMenu($menuid));
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|null
	 */
	private function lastArticleInMenu($menuid)
	{
		//Hole vorherigen Artikel.
		$sql = sprintf("SELECT `lart_id` FROM `%s` WHERE `lcat_id` = %d ORDER BY `lart_order_id` DESC LIMIT 1;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Artikel gefunden.
			return $result;
		}
		else {
			//Kein Artikel im Menüpunkt vorhanden. Suche weiter!
			$prevMenu = $this->prevMenu($menuid);
			if($prevMenu !== null)
			{
				return $this->lastArticleInMenu($this->prevMenu($menuid));
			}
		}
		return null;
	}
	/**
	 * Diese Funktion geht einen Artikel weiter.
	 *
	 * @param $menuid int
	 * @param $reporeid int
	 *
	 * @return int|array
	 */
	private function nextArticle($menuid, $reporeid)
	{
		$sql = sprintf("SELECT `lart_order_id` FROM `%s` WHERE `lcat_id` = %d AND `lart_id` = %d;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid,
			$reporeid
		);
		$orderid = $this->db->get_var($sql);

		//Hole nächsten Artikel.
		$sql = sprintf("SELECT `lart_id` FROM `%s` WHERE `lcat_id` = %d AND `lart_order_id` > %d ORDER BY `lart_order_id` ASC LIMIT 1;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid,
			$orderid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Vorhergehendes Menü in gleicher Ebene gefunden.
			//Dieses Menü in die Tiefe durchsuchen.
			return $result;
		}
		else {
			return $this->firstArticleInMenu($this->nextMenu($menuid));
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|null
	 */
	private function firstArticleInMenu($menuid)
	{
		//Hole vorherigen Artikel.
		$sql = sprintf("SELECT `lart_id` FROM `%s` WHERE `lcat_id` = %d ORDER BY `lart_order_id` ASC LIMIT 1;",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Artikel gefunden.
			return $result;
		}
		else {
			//Kein Artikel im Menüpunkt vorhanden. Suche weiter!
			$nextMenu = $this->nextMenu($menuid);
			if($nextMenu !== null) {
				return $this->firstArticleInMenu($this->nextMenu($menuid));
			}
		}
		return null;
	}

	/*========================================
	 * MENU STUFF
	 *=====================================*/
	/**
	 * Diese Funktion wühlt sich zum tiefsten Menüpunkt durch.
	 *
	 * @param $menuid
	 *
	 * @return mixed
	 */
	private function digMenu($menuid)
	{
		//Vorheriges Menü selber Tiefe
		$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d ORDER BY `order_id` DESC LIMIT 1;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			return $this->digMenu($result);
		}
		else {
			return $menuid;
		}
	}

	/**
	 * Diese Funktion wechselt in der obersten Ebene ein Menü zurück.
	 *
	 * @param $menuid
	 *
	 * @return null
	 */
	private function prevBaseMenu($menuid)
	{
		$sql = sprintf("SELECT `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$orderid = $this->db->get_var($sql);

		//Vorheriges Menü selber Tiefe
		$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = 0 AND `order_id` < %d ORDER BY `order_id` DESC LIMIT 1;",
			$this->cms->tbname['papoo_me_nu'],
			$orderid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Voriges Menü in der obersten Ebene
			return $result;
		}
		else {
			return null;
		}
	}

	/**
	 * Diese Funktion geht ein Menü zurück.
	 *
	 * @param $menuid
	 *
	 * @return mixed
	 */
	private function prevMenu($menuid)
	{
		//Hole Elternmenü
		$sql = sprintf("SELECT `untermenuzu`, `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_row($sql, ARRAY_A);

		$parentMenu = $result['untermenuzu'];
		$orderid    = $result['order_id'];

		//Valides Menü
		if($parentMenu > 0) {
			//Hole vorheriges Menü gleicher Tiefe
			$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d AND `order_id` < %d ORDER BY `order_id` DESC LIMIT 1;",
				$this->cms->tbname['papoo_me_nu'],
				$parentMenu,
				$orderid
			);
			$result = $this->db->get_var($sql);
			if($result) {
				//Vorhergehendes Menü in gleicher Ebene gefunden.
				//Dieses Menü in die Tiefe durchsuchen.
				return $this->digMenu($result);
			}
			else {
				//Kein Menü ich gleicher Ebene mehr vorhanden.
				//Springe zum Eltenmenü.
				return $parentMenu;
			}
		}
		else {
			//Gehe ein Menü zurück in oberster Ebene und gehe tief rein.
			return $this->digMenu($this->prevBaseMenu($menuid));
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|null
	 */
	private function climbUpMenu($menuid)
	{
		$sql = sprintf("SELECT `untermenuzu` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Eine Ebene nach oben gewandert.
			return $result;
		}
		else {
			return null;
		}
	}
	/**
	 * Diese Funktion wechselt in der obersten Ebene ein Menü weiter.
	 *
	 * @param $menuid
	 *
	 * @return null
	 */
	private function nextBaseMenu($menuid)
	{
		$sql = sprintf("SELECT `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$orderid = $this->db->get_var($sql);

		//Vorheriges Menü selber Tiefe
		$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = 0 AND `order_id` > %d ORDER BY `order_id` ASC LIMIT 1;",
			$this->cms->tbname['papoo_me_nu'],
			$orderid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Voriges Menü in der obersten Ebene
			return $result;
		}
		else {
			return null;
		}
	}
	/**
	 * Diese Funktion geht ein Menü weiter.
	 *
	 * @param $menuid
	 *
	 * @return mixed|void
	 */
	private function nextMenu($menuid)
	{
		//Hole Elternmenü
		$sql = sprintf("SELECT `untermenuzu`, `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_row($sql, ARRAY_A);

		$parentMenu = $result['untermenuzu'];
		$orderid    = $result['order_id'];

		//Hat das Menü ein Untermenü?
		$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d ORDER BY `order_id` ASC LIMIT 1;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_var($sql);
		if($result) {
			//Nächstes Menü in einer Ebene tiefer gefunden.
			return $result;
		}
		else {
			//Suche nach Menü in gleicher Ebene.
			$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d AND `order_id` > %d ORDER BY `order_id` ASC LIMIT 1;",
				$this->cms->tbname['papoo_me_nu'],
				$parentMenu,
				$orderid
			);
			$result = $this->db->get_var($sql);
			if($result) {
				//Nächstes Menü in gleicher Ebene gefunden.
				return $result;
			}
			else {
				//Gehe den Seitenbaum eine Ebene hoch und suche nach nächstem Menü
				$menu = $this->nextSiblingMenu($this->climbUpMenu($menuid));

				if($menu == 0 || $menu === null) {
					return $this->nextBaseMenu($menuid);
				}
				else {
					return $menu;
				}
				//Suche nach Menü in gleicher Ebene.
				$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d AND `order_id` > %d ORDER BY `order_id` ASC LIMIT 1;",
					$this->cms->tbname['papoo_me_nu'],
					$parentMenu,
					$orderid
				);
				$result = $this->db->get_var($sql);
				if($result) {
					//Nächstes Menü in gleicher Ebene gefunden.
					return $result;
				}
			}
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|void|null
	 */
	private function nextSiblingMenu($menuid)
	{
		$sql = sprintf("SELECT `untermenuzu`, `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_row($sql, ARRAY_A);

		if (is_array($result)) {
			$parentMenu = $result['untermenuzu'];
			$orderid    = $result['order_id'];

			$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d AND `order_id` > %d ORDER BY `order_id` ASC LIMIT 1;",
				$this->cms->tbname['papoo_me_nu'],
				$parentMenu,
				$orderid
			);
			$result = $this->db->get_var($sql);
			if($result) {
				return $result;
			}
			else {
				return null;
			}
		}
	}

	/**
	 * @param $menuid
	 *
	 * @return array|void|null
	 */
	private function firstChildOfMenu($menuid)
	{
		//Hole Elternmenü
		$sql = sprintf("SELECT `untermenuzu`, `order_id` FROM `%s` WHERE `menuid` = %d;",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result = $this->db->get_row($sql, ARRAY_A);

		$parentMenu = $result['untermenuzu'];
		$orderid    = $result['order_id'];

		//Valides Menü
		if($parentMenu > 0) {
			//Hole vorheriges Menü gleicher Tiefe
			$sql = sprintf("SELECT `menuid` FROM `%s` WHERE `untermenuzu` = %d AND `order_id` > %d ORDER BY `order_id` ASC LIMIT 1;",
				$this->cms->tbname['papoo_me_nu'],
				$parentMenu,
				$orderid
			);
			$result = $this->db->get_var($sql);
			if($result) {
				//Nächstes Menü in gleicher Ebene gefunden.
				return $result;
			}
			else {
				//Kein Menü ich gleicher Ebene mehr vorhanden.
				//Springe zum Eltenmenü und ein Menü weiter.
				return $parentMenu;
			}
		}
		else {
			return null;
		}
	}
}

$cm_paginating=new CM_Paginating();
