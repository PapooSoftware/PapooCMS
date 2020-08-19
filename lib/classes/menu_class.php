<?php
/**
 * #####################################
 * # papoo Version 3.0                 #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version 4.3                   #
 * #####################################
 */

/**
 * Hier wird die Menüklasse initialisiert, zurückgegeben wird das komplette Menü
 *
 * Class menu_class
 */
class menu_class
{
	// Daten der Frontend-Menüs
	/** @var bool Für Sitemap im Tiny MCE um die echten Links zu bekommen. */
	var $is_tiny=false;
	/** @var array Alle Menü-Punkte */
	var $data_front_complete;
	/** @var array Nur Navigations-Menü-Punkte */
	var $data_front;
	/** @var array Nur "Breadcrump"-Menü-Punkte */
	var $data_front_breadcrump;
	/** @var array Navigations-Menü mit allen Anpassungen */
	var $menu_front;
	/** @var array "Breadcrump"-Menü mit allen Anpassungen */
	var $menu_front_breadcrump;
	/** @var array Ebenen des Menüs */
	var $menu_front_level; //
	/** @var mixed Menü-Punkte für die der User Veröffentlichungs-Rechte hat */
	var $data_front_publish;
	/** @var int Ebene ab der Menü-Punkte dem Sub-Menü zugewiesen werden */
	var $menu_front_sub_startlevel;

	// Daten der Backend-Menüs
	/** @var array Alle Menü-Pnukte */
	var $data_back_complete; //
	/** @var array Nur Navigations-Menü-Punkte */
	var $data_back; //
	/** @var array Nur "Breadcrump"-Menü-Punkte */
	var $data_back_breadcrump;
	/** @var array Navigations-Menü mit allen Anpassungen */
	var $menu_back;
	/** @var array "Breadcrump"-Menü mit allen Anpassungen */
	var $menu_back_breadcrump;
	/** @var string Pfad zum aktuellen Menü-Punkte der Form: "menuid menuid .. aktumenuid" */
	var $pfad_aktu_menupunkt;
	/** @var string Name des aktuellen Menü-Punktes */
	var $aktu_menuname;

	/**
	 * menu_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = &$cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = &$db;
		// User Klasse einbinden
		global $user;
		$this->user = &$user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = &$content;
		// checked Klasse einbinde
		global $checked;
		$this->checked = &$checked;
		// Diverse Klasse
		global $diverse;
		$this->diverse = &$diverse;

		/*
		* Redirect vom Menü zum Artikel, wenn
		*   - im Frontend
		*   - freie URLs aktiviert
		*   - nicht auf der Startseite
		*   - URL in Form eines Menüpunktes
		*   - keine Untermenüs vorhanden
		*   - nur ein Artikel zugeordnet
		*/

		$this->redirect_to_article();


		$this->data_front_complete = array ();
		$this->data_front = array ();
		$this->data_front_breadcrump = array ();
		$this->menu_front = array ();
		$this->menu_front_breadcrump = array ();
		$this->menu_front_level = array ();
		$this->menu_front_publish = array ();
		$this->menu_front_sub_startlevel = 1;

		$this->data_back_complete = array ();
		$this->data_back = array ();
		$this->data_back_breadcrump = array ();
		$this->menu_back = array ();
		$this->menu_back_breadcrump = array ();
		$this->backarray = array ();
		$this->pfad_aktu_menupunkt = "/";
		$this->aktu_menuname = "";

		$this->list_menuy_aktiv = array();

		$this->make_menu();
	}

	/**
	 * @param $menuid
	 * @return bool
	 */
	function has_submenus($menuid)
	{
		$sql = sprintf("SELECT * FROM `%s` WHERE `untermenuzu`=%d;",
			$this->cms->tbname["papoo_me_nu"],
			$menuid
		);
		$result = $this->db->get_results($sql);

		if(is_array($result) && sizeof($result) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * @param $menuid
	 * @param $menus
	 * @return bool
	 */
	function has_menu_descendants($menuid, $menus)
	{
		foreach ($menus as $menu) {
			if ((int)$menuid == (int)$menu["untermenuzu"]) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param $menuid
	 * @return boolean
	 */
	function has_but_one_article($menuid)
	{
		$sql = sprintf("SELECT * FROM `%s` WHERE `lcat_id`=%d;",
			$this->cms->tbname["papoo_lookup_art_cat"],
			$menuid
		);
		$result = $this->db->get_results($sql);
		if(is_array($result) && sizeof($result) == 1) {
			return true;
		}
		return false;
	}

	/**
	 *
	 */
	function redirect_to_article()
	{
		$menuid = $this->checked->menuid;
		$ismenu = (substr($this->cms->surl_dat, -5) !== ".html"); #isset($this->checked->var2);
		if (isset($this->checked->template) && $this->checked->template!="form_manager/templates/form.html") {
			if(!defined("admin") && $this->cms->mod_free == 1 && $menuid > 1 && $ismenu
				&& !$this->has_submenus($menuid) && $this->has_but_one_article($menuid)) {
				if (stristr($_SERVER['REQUEST_URI'], "index.php") &&
					empty($this->checked->uebermittelformular) && empty($this->checked->no_output) &&
					preg_match("/(".implode("|", array_map("preg_quote", [
							"print=ok", "reporeid_send", "search", "pdf", "edit", "downloadid", "ps_act",
							"logoff", "gesendet=1", "&easyedit", "&waehrung="
						])).")/", $_SERVER["REQUEST_URI"]) != 1
				) {
					$sql = sprintf("SELECT `lart_id` FROM `%s` WHERE `lcat_id`=%d LIMIT 0,1;",
						$this->cms->tbname["papoo_lookup_art_cat"],
						$menuid
					);
					$reporeid = $this->db->get_var($sql);

					$sql = sprintf("SELECT `url_header` FROM `%s` WHERE `lan_repore_id`=%d;",
						$this->cms->tbname["papoo_language_article"],
						$reporeid
					);
					$url = $this->db->get_var($sql);

					$server = (substr($_SERVER["HTTP_HOST"], -1) === "/") ? substr($_SERVER["HTTP_HOST"], 0, -1) : $_SERVER["HTTP_HOST"];
					$server = (substr($server, 0, 7) !== "http://") ? "http://" . $server : $server;

					$path = (substr(PAPOO_WEB_PFAD, -1) === "/") ? substr(PAPOO_WEB_PFAD, 0, -1) : PAPOO_WEB_PFAD;

					header("Location: " . $server . $path . $url);
					exit;
				}

			}
		}
	}

	/**
	 * @param $menuid
	 * @return mixed
	 * @desc Liefert die MenüID des Elternmenüpunktes
	 * @author martin
	 */
	function get_parent_menuid ($menuid)
	{
		$sql = sprintf ("
			SELECT untermenuzu
			FROM %s
			WHERE menuid = %u",
			$this->cms->tbname['papoo_me_nu'],
			$this->db->escape($menuid)
		);

		$result = $this->db->get_results($sql, ARRAY_A);

		IfNotSetNull($result[0]);

		return $result[0]['untermenuzu'];
	}

	/**
	 *
	 */
	function set_is_tiny()
	{
		$this->is_tiny=true;
	}

	/**
	 * Aktionsweiche der Klasse menu_class
	 */
	function make_menu()
	{
		// aktive menuid zuweisen
		$this->checked->menuid = (int)$this->checked->menuid;
		//Keine numerische ID - dann 404
		if ($this->checked->menuid == 0 || empty($this->checked->menuid) )$this->checked->menuid = 1;

		if (!is_numeric($this->checked->menuid) || !empty($this->checked->do_404)) {
			if (strlen($_SERVER['REQUEST_URI'])>3) {
				if (!isset($this->checked->downloadid)) {
					$this->diverse->not_found();
				}
			}
		}

		$this->content->template['aktive_menuid'] = $this->checked->menuid;
		$this->content->template['eltern_menuid'] = $this->get_parent_menuid($this->checked->menuid);
		$this->content->template['menuids_aller_vorfahren'] = array();

		$elternmenuid = $this->content->template['eltern_menuid'];
		$count = 0;
		while ($elternmenuid != 0 && (
				count($this->content->template['menuids_aller_vorfahren']) == 0 ||
				$elternmenuid != end($this->content->template['menuids_aller_vorfahren'])
			)
		) {
			$this->content->template['menuids_aller_vorfahren'][] = $elternmenuid;
			$elternmenuid = $this->get_parent_menuid($elternmenuid);
			$count++;
		}
		$this->content->template['aktueller_menulevel'] = $count;

		// FRONTEND
		if (!defined("admin")) {
			// Artikeldaten raussuchen
			$this->get_artikel_data();
			// 1. Daten aller Frontend-Menü-Punkte laden für die User Lese-Rechte hat
			$this->data_front_complete = $this->menu_data_read("FRONT");

			$this->data_front = $this->menu_navigation_read("FRONT");
			$this->menuy_select("front");
			$this->menuy_mark("front");
			// 2. Daten des Frontend-Navigations-Menüs erstellen für die User Lese-Rechte hat
			$this->data_front = $this->menu_navigation_read("FRONT");

			if ($this->cms->categories == 1) {
				// Kategorien rausholen
				$this->preset_categories();
				$this->get_menu_cats();
				$this->content->template['categories'] = 1;
			}
			else {
				$this->content->template['catlist_data'][0] = array("catid"=>-1);
				$this->content->template['catlist_data'][1] = array("catid"=>-1);
				$this->content->template['catlist_data'][2] = array("catid"=>-1);
				$this->content->template['catlist_data'][3] = array("catid"=>-1);
				$this->content->template['catlist_data'][4] = array("catid"=>-1);
				$this->content->template['catlist_data'][5] = array("catid"=>-1);
				$this->content->template['catlist_data'][6] = array("catid"=>-1);
				$this->content->template['catlist_data'][7] = array("catid"=>-1);
				$this->content->template['catlist_data'][8] = array("catid"=>-1);
				$this->content->template['catlist_data'][9] = array("catid"=>-1);
				$this->content->template['no_categories'] = 1;
			}

			// 3. Frontend-Navigations-Menü erstellen (Pfad anpassen, <ul>-Struktur aufbauen etc.)
			if ($this->cms->show_menu_all!=1) {
				$this->menu_front = $this->menu_navigation_build("FRONT");
			}
			else {
				$this->menu_front = $this->menu_navigation_build("FRONT_COMPLETE");
			}

			$this->menu_front=$this->cat_sub_sort($this->menu_front);

			$this->content->template['menu_data'] = &$this->menu_front;
			$this->content->template['menu_level'] = &$this->menu_front_level;
			$this->content->template['menu_front_sub_startlevel'] = $this->menu_front_sub_startlevel;

			$this->menuy_select("front");
			$this->menuy_mark("front");

			// 4. Daten des Frontend-"Breadcrump"-Menüs erstellen für die User Lese-Rechte hat
			$this->data_front_breadcrump = $this->menu_navigation_read("FRONT", "BREADCRUMP");
			// 5. Frontend-"Breadcrump"-Menü erstellen (Pfad anpassen, <ul>-Struktur aufbauen etc.)
			$this->menu_front_breadcrump = $this->menu_navigation_build("FRONT", "", "BREADCRUMP");

			$this->content->template['menu_data_breadcrump'] = &$this->menu_front_breadcrump;

			IfNotSetNull($this->content->template['aktumenuname']);
			$this->aktu_menuname = $this->content->template['aktumenuname']; // Muß hier stehen, da mark_aktiv() den Name "noch mal verwurschtelt".
			//$this->mark_aktiv();

			$this->make_menucats();
			if (!strstr($_SERVER['HTTP_USER_AGENT'],"NT") ) {
				if (!strstr($_SERVER['HTTP_USER_AGENT'],"Intel Mac")) {
					if (!strstr($_SERVER['HTTP_USER_AGENT'],"Linux")) {
						$this->content->template['is_mobile']=1;
					}
				}

			}
		}
		// BACKEND
		if (defined("admin")) {
			// 1. Daten aller Backend-Menü-Punkte laden für die User Zugriffs-Rechte hat
			$this->data_back_complete = $this->menu_data_read("BACK");

			// 2. Daten des Backend-Navigations-Menüs erstellen für die User Zugriffs-Rechte hat
			$this->data_back = $this->menu_navigation_read("BACK");

			// 3. Backend-Navigations-Menü erstellen (Pfad anpassen, <ul>-Struktur aufbauen etc.)
			$this->menu_back = $this->menu_navigation_build("BACK");
			//$this->mark_aktiv();

			$this->menuy_select("back");
			$this->menuy_mark("back");

			$this->menu_back = $this->appendClassesToCertainPlugins($this->menu_back);

			$this->content->template['menu_data'] = &$this->menu_back;
			$this->vorganger_menuid = ""; // ???
			$this->vorganger_orderid = ""; // ???
			// 4. Daten aller Frontend-Menü-Punkte laden für die User Veröffentlichungs-Rechte hat
			#$this->data_front_publish = $this->menu_data_read("FRONT_PUBLISH");
			// Menue fuer accesskey
			$this->make_access_foot();
		}
		else {
			/**
			 * checken ob Zugriff auf diesen Menüpunkt besteht,
			 * ansonsten einen 404 rausgeben
			 */
			/*
			// Später mal benutzen wenn wirklich alles auf surls umgestellt ist.
			if ($this->cms->mod_rewrite==2 && strstr($_SERVER['REQUEST_URI'],"&"))
			{
				$this->diverse->not_found();
			}
			*/
			if (isset($this->accessok) && $this->accessok != "ok") {
				$this->diverse->not_found();
			}
		}
	}

	/**
	 * @param $menu
	 * @return mixed
	 */
	function cat_sub_sort($menu)
	{
		$this->make_menucats();
		$i=1;

		if (empty($this->menucatse2['0'])) {
			$this->menucatse2['0']="";
		}

		if (is_array($this->menucatse2['0'])) {
			foreach ($this->menucatse2['0'] as $cat) {
				$j=0;
				foreach ($menu as $item) {
					if(!isset($item['cat_cid'])) {
						$item['cat_cid'] = NULL;
					}

					if ($item['cat_cid']==$cat['cat_id']) {
						$menu[$j]['nummer']="x".$menu[$j]['nummer'];
						$split1=explode(".",$menu[$j]['nummer']);

						if (!empty($split1['1'])) {

							$menu[$j]['nummer']=str_ireplace($split1['0'],"",$menu[$j]['nummer']);
							$x=$i-1;
							$menu[$j]['nummer']=$x.$menu[$j]['nummer'];
						}
						else {
							$menu[$j]['nummer']=$i;
							$i++;
						}

					}
					$j++;
				}
			}
		}
		return $menu;
	}

	/**
	 * Die Menükategorien eindeutig zuordnen
	 *
	 * @return mixed|void
	 */
	function make_menucats()
	{
		if ($this->cms->categories == 1) {
			$this->menucatse = array();
			$i = 0;

			if (is_array($this->menucats)) {
				foreach ($this->menucats as $key => $value) {
					$j = 0;
					foreach ($value as $levels) {
						foreach ($this->catlist_data as $data) {
							if ($data['cat_id'] == $levels) {
								$this->menucatse[$key][$j]['cat_id'] = $data['cat_id'];
								$this->menucatse[$key][$j]['cat_text'] = $data['cat_text'];
							}
						}
						$j++;
					}
					// sort ($this->menucatse[$key],SORT_NUMERIC);
					$i++;
				}
			}

			foreach ($this->menucatse as $key => $value) {
				$j = 0;
				foreach ($this->catlist_data as $data) {
					foreach ($this->menucatse as $daten) {
						foreach ($daten as $dat) {
							if ($data['cat_id'] == $dat['cat_id']) {
								$this->menucatse2[$key][$j]['cat_id'] = $data['cat_id'];
								$this->menucatse2[$key][$j]['cat_text'] = $data['cat_text'];
							}
						}
					}
					$j++;
				}
			}

			//wenn wir "jeder" sind
			if(!is_array($this->user->gruppenid)) {
				$this->user->gruppenid = array("0");
				$this->user->gruppenid[0] = array("gruppenid"=>"10");
			}

			//alle gruppenids in schönes array packen
			$groups = array_map(function($elem) {
				return $elem["gruppenid"];
			},$this->user->gruppenid);



			//leserechte für kategorien holen
			$sql = sprintf("SELECT cat_rlid FROM %s WHERE cat_rlgruppe IN (%s) GROUP BY cat_rlid",
				$this->cms->tbname['papoo_category_lookup_read'],
				implode(",", $groups)
			);

			$leserechte = $this->db->get_results($sql, ARRAY_A);

			$leserechte = array_map(function($elem){
				return $elem["cat_rlid"];
			},$leserechte);

			//wenn kein admin
			if(!in_array("1",$groups)) {
				//Kategorien ohne Leserechte nicht in Template Variable schreiben
				$this->menucatse2[0] = array_filter($this->menucatse2[0], function ($cat) use ($leserechte) {
					return in_array($cat["cat_id"], $leserechte);
				});
			}

			$this->content->template['catlist_data'] = $this->menucatse2;
		}
	}

	/**
	 * Alle Einträge mit Kategorien rausholen
	 */
	function get_menu_cats()
	{
		$sql = sprintf("SELECT * FROM %s ",
			$this->cms->tbname['papoo_lookup_mencat']
		);

		$this->menu_cats = $this->db->get_results($sql, ARRAY_A);

		$this->max_cat_level = 10;

		// FIXME: Warum ist hier nicht dann alles auskommentiert?
		IfNotSetNull($module);

		#global $module;
		if (!empty($module->style_module_liste)) {
			foreach ($module->style_module_liste as $module) {
				if ($module['stylemod_mod_id'] == 15) {
					$this->max_cat_level = 1;
				}
				if ($module['stylemod_mod_id'] == 16) {
					$this->max_cat_level = 2;
				}
				if ($module['stylemod_mod_id'] == 17) {
					$this->max_cat_level = 3;
				}
				if ($module['stylemod_mod_id'] == 18) {
					$this->max_cat_level = 1;
				}
			}
		}
	}

	/**
	 * Kategorien rausholen
	 *
	 * TODO: Umschreiben das hier ein wert zurückgegeben wird. Dann an verwendeten Stellen umschreiben.
	 * @param bool $get_return
	 * @return void|array
	 */
	function preset_categories($get_return=false)
	{
		$this->menucats = array();

		$sql = sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_lang_id AND cat_lang_lang='%s' ORDER BY cat_order_id",
			$this->cms->tbname['papoo_category_lang'],
			$this->cms->tbname['papoo_category'],
			$this->db->escape($this->cms->lang_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->catlist_data = $result;

		if($get_return) {
			return $result;
		}
	}

	/**
	 * @param $art_id
	 * @return null|array
	 */
	function getURL($art_id)
	{
		$sql = sprintf("SELECT url_header FROM %s WHERE lan_repore_id = %s AND lang_id = %s
						",
			//	AND T1.cattextid       = '%d'",
			$this->cms->tbname['papoo_language_article'],
			$art_id,
			$this->cms->lang_id
		// $this->db->escape($this->checked->menuid)
		);
		return $this->db->get_var($sql);
	}

	/**
	 * @return array
	 */
	function getURLs()
	{
		$sql = sprintf("SELECT `lan_repore_id`, `url_header` FROM `%s` WHERE `lang_id` = %d ORDER BY `lan_repore_id`",
			$this->cms->tbname['papoo_language_article'],
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$urls = array();
		foreach($result as $row) {
			$urls[(int)$row["lan_repore_id"]] = $row["url_header"];
		}
		return $urls;
	}

	/**
	 * Artikel Daten für mod_rewrite rausuchen
	 */
	function get_artikel_data()
	{
		$sql = sprintf("SELECT
							DISTINCT (T1.reporeID),
							T5.lcat_id AS cattextid,
							T2.url_header
						FROM
							%s as T1,
							%s as T2,
							%s as T3,
							%s as T4,
                            %s as T5
						WHERE
							T2.lang_id             = '%d'
							AND T1.reporeID        = T2.lan_repore_id
							AND T2.publish_yn_lang      = '1'
							AND T1.allow_publish   = '1'
							AND T1.reporeID        = T3.article_id
							AND T3.gruppeid_id     = T4.gruppenid
							AND T4.userid          = '%d'
                            AND T5.lart_id         = T1.reporeID
                            AND T1.dok_show_teaser_teaser = '0'
						",
			//	AND T1.cattextid       = '%d'",

			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_language_article'],
			$this->cms->papoo_lookup_article,
			$this->cms->papoo_lookup_ug,
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->cms->lang_id,
			$this->user->userid
		// $this->db->escape($this->checked->menuid)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$adaten = array();

		//KOntrolle ob Mehrfachzuweisung...
		//Dazu ein Arry befüllen mit dem später nachgeschaut werden kann
		if (is_array($result)) {
			foreach ($result as $dat) {
				$neu_art[$dat['cattextid']][]=$dat['reporeID'];
			}
		}

		if (is_array($result)) {
			$urls = $this->getURLs();
			IfNotSetNull($neu_art);

			foreach ($result as $dat) {
				//Festellen ob pro Menüpunkt mehrere Artikel vorhanden sind,
				//Wenn ja, dann soll später die surl des Menüpunktes
				//genommen werden
				if (count($neu_art[$dat['cattextid']])==1) {
					//Nur ein Artikel - dann diese url nehmen
					$adaten[$dat['cattextid']] = $dat['url_header'];

					if ($this->cms->mod_rewrite==2 && !stristr($adaten[$dat['cattextid']],".html")) {
						$adaten[$dat['cattextid']]=$adaten[$dat['cattextid']].".html";
					}
				}
				else {
					foreach ($neu_art[$dat['cattextid']] as $artikel_list_item_id) {
						$url = $urls[$artikel_list_item_id];
						$adaten[$dat['cattextid']][ $artikel_list_item_id ] = $url;
					}
				}

				/**
				if (empty($adaten[$dat['cattextid']]))
				{
				$adaten[$dat['cattextid']] = $dat['url_header'];
				} else
				{
				$adaten[$dat['cattextid']] = "nono";
				}
				 */
				if ($dat['cattextid']==$this->checked->menuid) {
					$sqlCatTextIds = sprintf("SELECT
												DISTINCT(lcat_id)
												FROM
													%s
												WHERE
													lart_id         =  '%s'
												AND lcat_id         != '%s'
												",
						$this->cms->tbname['papoo_lookup_art_cat'],
						$dat['reporeID'],
						$dat['cattextid']
					);
					$resultCatTextIds = $this->db->get_results($sqlCatTextIds, ARRAY_A);
				}

				if (empty($resultCatTextIdsx)) {
					$resultCatTextIdsx="";
				}

				if (is_array($resultCatTextIdsx)) {
					$cattextids = array();
					IfNotSetNull($resultCatTextIds);

					foreach ($resultCatTextIds as $cattextid) {
						$cattextids[] = $cattextid['lcat_id'];
					}

					$cattextids = array_unique($cattextids);

					foreach ($cattextids as $cattextid) {
						if (empty($adaten[$cattextid])) {
							$adaten[$cattextid] = $dat['url_header'];

							if ($this->cms->mod_rewrite==2 && !stristr($adaten[$cattextid],".html")) {
								$adaten[$cattextid]=$adaten[$cattextid].".html";
							}
						}
						elseif ($adaten[$cattextid] != $dat['url_header']) {
							$adaten[$cattextid] = "nono";
						}
					}
				}
			}
		}
		$this->artdata = $adaten;
	}

	/**
	 * Checkt ob ein Extra Stylesheet eingebunden werden soll für diesen Punkt
	 *
	 * @param $menupunkt
	 */
	function check_extra_style($menupunkt)
	{
		//Zähler für mögliche Inkonsistenzen
		$this->inko=0;

		$temp_extra_style = "";
		// Es gibt einen Extrastyle für diesen Punkt
		if (!empty ($menupunkt['extra_css_file'])) {
			$temp_extra_style = $menupunkt['extra_css_file'];
		}
		elseif ($menupunkt['untermenuzu'] > 0) {
			//Hochzählen, damit sich das hier nicht unbegrenzt hochzählt
			$this->inko++;
			if ($this->inko<10) {
				$temp_extra_style = $this->select_extra_css_up($menupunkt['untermenuzu']);
			}
			//$temp_extra_style = $this->select_extra_css_up($menupunkt['untermenuzu']);
		}

		$temp_pfad_extra_style = PAPOO_ABS_PFAD."/styles/".$this->cms->style_dir."/css/".$temp_extra_style;

		if (!empty($temp_extra_style) AND file_exists($temp_pfad_extra_style)) {
			$this->content->template['extra_css'] = $temp_extra_style;
		}
	}

	/**
	 * Herausfinden ob ein Punkt oberhalb Extra CSS hat
	 *
	 * @param $id
	 * @return string|null
	 */
	function select_extra_css_up($id)
	{
		$temp_extra_style = "";
		$sql = sprintf("SELECT untermenuzu, extra_css_file, extra_css_sub, menu_spez_layout FROM %s WHERE menuid='%s'", $this->cms->tbname['papoo_me_nu'], $id);
		$result = $this->db->get_results($sql, ARRAY_A);

		if(count($result) <= 0) {
			return NULL;
		}

		if (!empty ($result['0']['extra_css_file']) and !empty ($result['0']['extra_css_sub'])) {
			$temp_extra_style = $result['0']['extra_css_file'];
			#$this->cms->make_style(0,$result['0']['menu_spez_layout']);
			//$_SESSION['style']=0;
		}
		elseif ($result['0']['untermenuzu'] > 0) {
			$temp_extra_style = $this->select_extra_css_up($result['0']['untermenuzu']);
		}
		return $temp_extra_style;
	}


	/**
	 * Liest Daten sämtlicher Menü-Punkte ein
	 *
	 * @param string $front_back
	 * @param string $sprach_id
	 * @return array|bool|mixed
	 */
	function menu_data_read($front_back = "", $sprach_id = "")
	{
		if(!empty($_SESSION['langid_front']) and empty($sprach_id)) {
			$sprach_id=$_SESSION['langid_front'];
		}

		if (empty($sprach_id)) $sprach_id = $this->cms->lang_id;
		// 1. Parameter für die Abfrage festlegen
		switch ($front_back) {
		case "FRONT" :
			$tabelle_menu = $this->cms->papoo_menu;
			$tabelle_menu_sprache = $this->cms->papoo_menu_language;
			$tabelle_menu_lookup = $this->cms->papoo_lookup_me_all_ext;
			$t3_menuid = "menuid_id";
			$t3_gruppenid = "gruppeid_id";
			$order = "order_id";
			break;

		case "FRONT_NAME" :
			$tabelle_menu = $this->cms->papoo_menu;
			$tabelle_menu_sprache = $this->cms->papoo_menu_language;
			$tabelle_menu_lookup = $this->cms->papoo_lookup_me_all_ext;
			$t3_menuid = "menuid_id";
			$t3_gruppenid = "gruppeid_id";
			$order = "menuname";
			break;

		case "FRONT_PUBLISH" :
			$tabelle_menu = $this->cms->papoo_menu;
			$tabelle_menu_sprache = $this->cms->papoo_menu_language;
			$tabelle_menu_lookup = $this->cms->papoo_lookup_men;
			$t3_menuid = "menuid";
			$t3_gruppenid = "gruppenid";
			$order = "order_id";
			break;

		case "FRONT_ORDERMENUID" :
			$tabelle_menu = $this->cms->papoo_menu;
			$tabelle_menu_sprache = $this->cms->papoo_menu_language;
			$tabelle_menu_lookup = $this->cms->papoo_lookup_me_all_ext;
			$t3_menuid = "menuid_id";
			$t3_gruppenid = "gruppeid_id";
			$order = "menuid";
			break;

		case "BACK" :
			$sprach_id = $this->cms->lang_back_id;
			$tabelle_menu = $this->cms->papoo_menu_int;
			$tabelle_menu_sprache = $this->cms->papoo_menuint_language;
			$tabelle_menu_lookup = $this->cms->papoo_lookup_men_int;
			$t3_menuid = "menuid";
			$t3_gruppenid = "gruppenid";
			$order = "order_id, t1.menuid";
			break;

		default :
			return false;
		}

		$publish_yn_lang_men = "";
		if (!defined("admin")) {
			$publish_yn_lang_men = " AND publish_yn_lang_men = '1' ";
		}
		// 2. Abfrage durchführen
		$sql = sprintf("SELECT DISTINCT t1.*, t2.*
						FROM
						%s AS t1, %s AS t2, %s AS t3,
						%s AS t4, %s AS t5, %s AS t6

						WHERE
						t1.menuid = t2.menuid_id
						AND t1.menuid = t3.%s AND t3.%s = t6.gruppeid

						AND t4.userid = t5.userid AND t5.gruppenid = t6.gruppeid
						AND t4.userid='%d' AND t2.lang_id='%d'
                        AND CHAR_LENGTH(t2.menuname) > 1
                        %s
						ORDER BY t1.%s ASC",
			$tabelle_menu,
			$tabelle_menu_sprache,
			$tabelle_menu_lookup,
			$this->cms->papoo_user,
			$this->cms->papoo_lookup_ug,
			$this->cms->papoo_gruppe,
			$this->db->escape($t3_menuid),
			$this->db->escape($t3_gruppenid),
			$this->user->userid,
			$sprach_id,
			$publish_yn_lang_men,
			$order
		);
		$this->result_temp = $this->db->get_results($sql, ARRAY_A);

		// ermoeglicht die eingabe einer existierenden sprechenden url eines menues bzw. eines artikels als menuelink
		// in der menue_sortieren() wird die url noch ggf. entsprechend ueberschrieben, um das verhalten zu erzwingen
		$urls = [];
		if ($this->cms->mod_free == 1) {
			$urls = array_map(function ($url) {
				return $url["url"];
			},
				$this->db->get_results("SELECT `url_header` AS `url` FROM `{$this->cms->tbname["papoo_language_article"]}` UNION SELECT `url_menuname` AS `url` FROM `{$this->cms->tbname["papoo_menu_language"]}`", ARRAY_A)
			);
		}
		foreach ($this->result_temp as &$menu) {
			$menuLink = $menu['menulinklang'];

			// Prüfe auf freie URL anhand von Mustern, die durch Plugins definiert worden sein können
			$isExtraFreeUrl = array_reduce(
				$this->cms->getExtraFreeUrlPatterns(),
				function ($match, $pattern) use ($menuLink) {
					return $match || (bool)preg_match($pattern, $menuLink);
				},
				false
			);

			if (substr($menu["menulinklang"], 0, 1) === "/") {
				if ($isExtraFreeUrl || in_array($menu["menulinklang"], $urls, true)) {
					$menu["url_menuname"] = $menu["menulinklang"];
				}
				else if (in_array($this->cms->mod_rewrite, [2, 3]) && strpos($menu["menulinklang"], $this->cms->surl_urlvar) === 0) {
					$menu["url_menuname"] = rtrim(substr($menu["menulinklang"], strlen($this->cms->surl_urlvar)), "/");
				}
			}
		}
		unset($menu);

		// 3. Daten korrekt sortierenif (!is_array($_SESSION['dbp']['menu_data']) && $front_back == "FRONT_PUBLISH")
		$this->untermenuzu=array();
		if (is_array($this->result_temp)) {
			foreach ($this->result_temp as $dat) {
				$this->untermenuzu[$dat['untermenuzu']]=$dat['untermenuzu'];
			}
		}
		$menu_data =$this->menue_sortieren();

		if ($front_back === "BACK") {
			$menu_data = $this->sortPluginsByName($menu_data);
		}

		// 3.a "Untermenü-Punkte" für Veröffentlichung anfügen
		if ($front_back == "FRONT_PUBLISH") $menu_data = $this->menue_sub_sortieren($menu_data);
		// unsortiertes temporäres Array löschen
		$this->result_temp = array();
		// 4. Daten zurückgeben

		return $menu_data;
	}

	/**
	 * @param $menu_items
	 * @return mixed
	 */
	private function sortPluginsByName($menu_items)
	{
		$plugins = [];
		$pluginsOffset = null;

		$priority = 0;
		$currentPlugin = "";
		// plugin menuepunkte aus dem array extrahieren (name des plugins als index)
		foreach ($menu_items as $key => $val) {
			if ($val["menuid"] == 54) {
				$pluginsOffset = $key+1;
			}
			else if ($pluginsOffset !== null) {
				if (strpos($val["menulink"], "plugin") === 0) {
					if ($val["level"] == 1) {
						$priority = $val["menulink"] === "plugins.php" ? 0 : (strpos($val["menulink"], "plugin:devtools/") === 0 ? 1 : 2);
						$currentPlugin = $val["menuname"];
						$plugins["$priority-$currentPlugin"] = [];
					}
					$plugins["$priority-$currentPlugin"][] = array_merge($val, ["menuklasse" => "test"]);
				}
				else {
					break;
				}
			}
		}

		if ($pluginsOffset !== null) {
			$sortedPluginNames = array_keys($plugins);
			natsort($sortedPluginNames);

			// neues array mit sortierten plugin menuepunkten generieren
			$sortedPlugins = array_reduce($sortedPluginNames, function ($sortedPlugins, $name) use ($plugins) {
				$sortedPlugins = array_merge($sortedPlugins, $plugins[$name]);
				return $sortedPlugins;
			}, []);

			// unsortierte menuepunkte durch die sortierten ersetzen
			array_splice($menu_items, $pluginsOffset, count($sortedPlugins), $sortedPlugins);
		}

		return $menu_items;
	}

	/**
	 * @param $menu_items
	 * @return bool
	 */
	private function appendClassesToCertainPlugins($menu_items)
	{
		if (is_array($menu_items) == false) {
			return false;
		}

		foreach ($menu_items as $key => &$val) {
			$class = "";
			if ($val["level"] == 1 && $val["menulink"] === "plugins.php") {
				$class = "plugin-manager";
			}
			else if ($val["level"] == 1 && strpos($val["template"], "devtools/templates/devtools_backend.html") !== false) {
				$class = "dev-tools";
			}
			if (strlen($class) > 0) {
				$val["menuklasse"] = isset($val["menuklasse"]) && strlen($val["menuklasse"]) > 0
					? "{$val["menuklasse"]} $class"
					: $class;
			}
		}
		return $menu_items;
	}

	/**
	 * Baut aus der Liste aller Menü-Punkte die Liste für die Navigation (bzw, das Breadcrump-Menü)
	 *
	 * @param string $front_back
	 * @param string $breadcrump
	 * @return array|bool
	 */
	function menu_navigation_read($front_back = "", $breadcrump = "")
	{
		$menu_data = array ();
		// !!! (Möglichkeit für) Schalter "Anzeige-Modus des Menüs" (0: wie bisher, 1: immer kompletten Menü-Baum anzeigen)
		$menu_anzeige_modus = 0; // .. = $this->cms->menu_anzeige_modus oder so :-)
		// print_r($this->data_front_complete);
		// 1. Zu bearbeitende Daten zuweisen
		switch ($front_back) {
		case "FRONT" :
			$menu_data_complete = $this->data_front_complete;
			$menu_anzeige_modus = $this->cms->anzeig_menue_komplett;
			break;

		case "BACK" :
			$menu_data_complete = $this->data_back_complete;
			break;

		default :
			return false;
		}
		// 2. Daten bearbeiten (Menüpunkte die nicht angezeigt werden sollen aussortieren)
		if (!empty ($menu_data_complete)) {
			foreach ($menu_data_complete as $menupunkt) {
				// Regeln welcher Menü-Punkt angezeigt wird
				$anzeigen = false;

				if ($breadcrump == "") {

					if ( $this->cms->categories && !defined("admin"))
					{
						$this->content->template['menu_front_sub_aktiv'] = TRUE;

						//Sonderfall Kategorien aber alle anzeigen
						if ($menupunkt['level'] == 0)
							$menu_anzeige_modus=0;
						#if ($menupunkt['menuid'] == $this->checked->menuid || in_array($menupunkt['menuid'],$this->list_menuy_aktiv))
						#	$menu_anzeige_modus=1;
					}

					// a. Alle Eltern-Elemente und Elemente der Ebene des aktuellen Menü-Punktes
					// Test: Pfad des zu testenden Menüpunktes ist Teil oder Gleich Pfad zum aktuellen Menü-Punkt
					if (strpos("XXX" . $this->pfad_aktu_menupunkt, $menupunkt['pfad']))
						$anzeigen = true;
					// b. Alle Kind-Elemente des aktuellen Menü-Punktes
					if ($menupunkt['menuid'] == $this->checked->menuid)
						$anzeigen = true;
					// c. Wenn Anzeige-Modus == 1, dann sowieso anzeigen
					if ($menu_anzeige_modus == 1)
						$anzeigen = true;
				}

				if ($breadcrump == "BREADCRUMP") {
					// a. Alle Eltern-Elemente des aktuellen Menü-Punktes
					// Test: Menü-ID des zu testenden Menüpunktes ist Teil des Pfades zum aktuellen Menü-Punkt
					if (strpos("XXX" . $this->pfad_aktu_menupunkt, "/" . $menupunkt['menuid'] . "/")) {
						$menupunkt['menuname'] = str_ireplace("- ", "", $menupunkt['menuname']);
						$anzeigen = true;
					}
					// b. aktueller Menü-Punkt
					if ($menupunkt['is_aktiv'])
						$anzeigen = true;
				}
				// anzuzeigenden Menüpunkt zuweisen
				if ($anzeigen)
					$menu_data[] = $menupunkt;
			}
		}

		if (!empty ($menu_data)) {
			return $menu_data;
		}
		else {
			return false;
		}
	}

	/**
	 * Baut in Navigations-Liste alle nötigen zusätzlichen Informationen ein (Pfade anpassen, <ul>s etc.)
	 *
	 * @param string $front_back
	 * @param string $shift_modus
	 * @param string $breadcrump
	 * @return array|bool
	 */
	function menu_navigation_build($front_back = "", $shift_modus = "NAVIGATION", $breadcrump = "")
	{

		$menu_data = array ();
		$is_front = true;

		// 1. Zu bearbeitende Daten zuweisen
		switch ($front_back) {
		case "FRONT" :
			if ($breadcrump == "BREADCRUMP")
				$menu_data_copy = $this->data_front_breadcrump;
			else
				$menu_data_copy = $this->data_front;
			$mod_rewrite = $this->cms->mod_rewrite;
			break;

		case "FRONT_COMPLETE":
			// wird für Inhaltsverzeichnis verwendet
			$menu_data_copy = $this->data_front_complete;
			// print_r($this->data_front_complete);
			$mod_rewrite = $this->cms->mod_rewrite;
			break;

		case "FRONT_TOTAL":
			// wird für Inhaltsverzeichnis verwendet
			$menu_data_copy = $this->total_menu_data;
			$mod_rewrite = $this->cms->mod_rewrite;
			break;

		case "FRONT_PUBLISH":
			$menu_data_copy = $this->data_front_publish;
			$mod_rewrite = $this->cms->mod_rewrite;
			break;

		case "BACK":
			if ($breadcrump == "BREADCRUMP") {
				$menu_data_copy = $this->data_back_breadcrump;
			}
			else {
				$menu_data_copy = $this->data_back;
			}
			$mod_rewrite = 0;
			$this->menu_front_sub_startlevel = 1;
			break;

		default :
			return false;
		}
		// 2. Daten bearbeiten (<ul>s zuweisen, Pfade anpassen etc.)
		// a. (globale) Presets
		$level_vorgaenger = 0;
		$counter = 0;
		$anzahl_menuepunkte = is_array($menu_data_copy) ? count($menu_data_copy) : 0;
		$last_sub_id = 0;

		if (!empty ($menu_data_copy)) {
			foreach ($menu_data_copy as $menupunkt) {
				if ($menupunkt['menuid'] == $this->checked->menuid) {
					$this->accessok = "ok";
				}
				// a. Presets des Menü-Punktes
				if ($shift_modus == "SITEMAP"){
					$menupunkt['shift_anfang'] = '<li id="sitemap_menuid_' . $menupunkt['menuid'] . '">';
				}
				else {
					if ((count($menu_data_copy) > 0 ? $this->has_menu_descendants($menupunkt['menuid'], $menu_data_copy) : $this->has_submenus($menupunkt['menuid']))) {
						$menupunkt['shift_anfang'] = '<li class="has-dropdown">';
					}
					else {
						$menupunkt['shift_anfang'] = '<li>';
					}
				}

				$menupunkt['shift_ende'] = "</li>";
				$menupunkt['shift_anfang_sub'] = '<li>';
				$menupunkt['shift_ende_sub'] = "</li>";
				$menupunkt['shift_anfang_sub'] = "";
				$menupunkt['shift_ende_sub'] = "";
				$is_plugin = false;

				$the_template = "";
				$menupunkt['template'] = "";
				$menupunkt['menuklasse'] = "menuxaktiv active";

				if ($this->cms->categories == 1) {
					// Kategorie zuweisen wenn Rechte
					if (!empty($this->menu_cats)) {
						foreach ($this->menu_cats as $cats) {
							// wenn eine Kategorie dem Menüpunkt zugeordnet werden kann
							if ($cats['menuid_cid'] == $menupunkt['menuid']) {
								if ($menupunkt['level'] <= $this->max_cat_level) {
									$menupunkt['cat_cid'] = $cats['cat_cid'];
									$cat_id = $cats['cat_cid'];
									$level = $menupunkt['level'];
									$this->menucats[$level][$cat_id] = $cat_id;
								} else {
									$menupunkt['cat_cid'] = $this->catid;
								}
							}
						}
					}

					if ($front_back !== "BACK" && empty($menupunkt['cat_cid'])) {
						$menupunkt['cat_cid'] = 1;
					}
					$this->catid = $menupunkt['cat_cid'];
				}
				if (empty($menu_data[$counter])) {
					$menu_data[$counter]=array();
				}
				$menu_data[$counter];
				if ($menupunkt['level']>$level_vorgaenger) {
					$menu_data[$counter-1]['has_sub']=1;
					$this->content->template['has_sub_menu']=1;
					if ($menupunkt['level']>1) {
						#$menu_data[$counter-1]['shift_anfang']='<li class="dropdown-submenu">';
						$menu_data[$counter-1]['shift_anfang']=str_ireplace('inhaltmap','inhaltmap dropdown-submenu',$menu_data[$counter-1]['shift_anfang']);
						if (!stristr($menu_data[$counter-1]['shift_anfang'],"class"))
						{
							$menu_data[$counter-1]['shift_anfang']=str_ireplace('id=','class="dropdown-submenu" id=',$menu_data[$counter-1]['shift_anfang']);
						}

					}
					#$menu_data[$counter-1]['shift_anfang']='<li class="dropdown-submenu">';
				}
				// b.1. Ebenen-Shift Anpassen (<ul>s auf/zu
				if ($menupunkt['level'] != $level_vorgaenger) {
					if(!isset($menu_data[$counter-1]['has_sub'])) {
						$menu_data[$counter-1]['has_sub'] = false;
					}

					$ebenen_shift = $this->menu_ebenen_shift($menupunkt['level'] - $level_vorgaenger, $menupunkt['level'], $menupunkt['menuid'], $shift_modus, $menu_data[$counter-1]['has_sub'], $menu_data_copy);
					$menu_data[$counter -1]['shift_ende'] = $ebenen_shift['shift_ende_vorgaenger'];

					IfNotSetNull($ebenen_shift['shift_ende_vorgaenger_bootstrap']);
					IfNotSetNull($ebenen_shift['shift_anfang_bootstrap']);

					$menu_data[$counter -1]['shift_ende_bootstrap'] = $ebenen_shift['shift_ende_vorgaenger_bootstrap'];
					$menupunkt['shift_anfang'] = $ebenen_shift['shift_anfang'];
					$menupunkt['shift_anfang_bootstrap'] = $ebenen_shift['shift_anfang_bootstrap'];
					$menupunkt['shift_ende'] = $ebenen_shift['shift_ende'];
				}
				$level_vorgaenger = $menupunkt['level'];
				// b.2. Ebenen_Shift des LETZTEN Menü-Punktes festlegen
				if (($counter + 1) >= $anzahl_menuepunkte) {
					$ebenen_shift = $this->menu_ebenen_shift((0 - $menupunkt['level']), 0, 0, $shift_modus, "", $menu_data_copy);
					$menupunkt['shift_ende'] = $ebenen_shift['shift_ende_vorgaenger'];
				}
				// b.3. Ebenen-Tiefe vermerken
				// if ($front_back == "FRONT") $this->menu_front_level[$menupunkt['level']] = TRUE;
				if ($is_front) {
					$this->menu_front_level[$menupunkt['level']] = true;
				}
				// b.4. SUB-Ebenen-Shift Anpassen (<ul>s auf/zu
				// if (($front_back == "FRONT") && ($breadcrump != "BREADCRUMP"))
				if (($is_front) && ($breadcrump != "BREADCRUMP")) {
					if ($menupunkt['level'] >= $this->menu_front_sub_startlevel) {
						if ($front_back == "FRONT") $this->content->template['menu_front_sub_aktiv'] = TRUE;

						$ebenen_shift = $this->menu_ebenen_shift($menupunkt['level'] - $menu_data[$last_sub_id]['level'], $menupunkt['level'], $menupunkt['menuid'], $shift_modus, "", $menu_data_copy);
						$menu_data[$last_sub_id]['shift_ende_sub'] = $ebenen_shift['shift_ende_vorgaenger'];

						if(!isset($ebenen_shift['shift_ende_vorgaenger_bootstrap'])) {
							$ebenen_shift['shift_ende_vorgaenger_bootstrap'] = 0;
						}

						$menu_data[$last_sub_id]['shift_ende_sub_bootstrap'] = $ebenen_shift['shift_ende_vorgaenger_bootstrap'];
						$menupunkt['shift_anfang_sub'] = $ebenen_shift['shift_anfang'];
						$menupunkt['shift_ende_sub'] = $ebenen_shift['shift_ende'];
						$last_sub_id = $counter;
					}
					// b.5. SUB-Ebenen_Shift des LETZTEN SUB-Menü-Punktes festlegen
					if (($counter + 1) >= $anzahl_menuepunkte) {
						// Test ob momentaner Menü-Punkt der letzte ist, oder $menu_data[$last_sub_id];
						// entsprechend die "Level-Differenz" setzen
						if ($counter == $last_sub_id) {
							$level_differenz = $this->menu_front_sub_startlevel - $menupunkt['level'] - 1;
						}
						else {
							$level_differenz = $this->menu_front_sub_startlevel - $menu_data[$last_sub_id]['level'] - 1;
						}
						$ebenen_shift = $this->menu_ebenen_shift($level_differenz, $this->menu_front_sub_startlevel, 0, "SUB", "", $menu_data_copy);
						$menupunkt['shift_ende_sub'] = $ebenen_shift['shift_ende_vorgaenger'];
						$menu_data[$last_sub_id]['shift_ende_sub'] = $ebenen_shift['shift_ende_vorgaenger'];
					}
				}
				// c. Links anpassen
				// c.1. Plugin-Links
				if (strpos("XXX_" . $menupunkt["menulink"], "plugin:")) {
					$is_plugin = true;
					$the_template = str_replace("plugin:", "", $menupunkt["menulink"]);
					$menupunkt["menulink"] = "plugin.php";
				}
				// c.2. Mod-Rewrite
				if ($mod_rewrite == 2) {
					if (!$this->diverse->is_externlink($menupunkt["menulink"])) {
						$menupunkt["menulink"] = str_ireplace(".php", "", $menupunkt["menulink"]);
					}

					if ($is_plugin) {
						$the_template = str_replace("/", ":::", $the_template);
						$the_template = $this->urlencode($the_template);
						$menupunkt["template"] = "/template/" . $the_template;
					}
				}
				// c.3. Kein Mod-Rewrite
				else {
					if ($is_plugin) {
						$menupunkt["template"] = "&amp;template=" . $the_template;
					}
				}
				// d. Prüfung auf externen Link
				$menupunkt['extern'] = $this->diverse->is_externlink($menupunkt["menulink"]);
				$menupunkt['extern_bread'] = $this->diverse->is_externlink_bread($menupunkt["menulink"]);
				// e. CSS-Marken festlegen
				$menupunkt['htmltag_id'] = "menu_" . $menupunkt['menuid'];
				// Wenn Menü-Punkt = aktueller Menü-Punkt: CSS-Klasse wechseln
				if ($menupunkt['is_aktiv']) {
					$menupunkt['menuklasse'] = "menuxaktiv_back";
					if ($menupunkt['level'] == 0) {
						$this->content->template['aktueller_menupunkt_level0'] = $menupunkt;
					}
					$this->content->template['aktumenuname'] = str_ireplace("- ", "", $menupunkt['menuname']);
					if (defined('admin') && empty($this->is_admin_set)) {
						$this->content->template['aktumenuname_admin']=$this->content->template['aktumenuname'];
						$this->is_admin_set=1;
					}
					if (empty($menupunkt['cat_cid'])) {
						$menupunkt['cat_cid']="";
					}

					$this->content->template['aktumenu_cat']=$menupunkt['cat_cid'];
					$this->is_aktiv=1;

					if (strlen($menupunkt['lang_title']) > 0) {
						$this->content->template['site_title'] = $menupunkt['lang_title'];
					}

					IfNotSetNull($menupunkt['url_metadescrip']);
					IfNotSetNull($menupunkt['url_metakeywords']);

					if (strlen($menupunkt['url_metadescrip']) > 0) {
						$this->content->template['description'] = $menupunkt['url_metadescrip'];
					}

					if (strlen($menupunkt['url_metakeywords']) > 0) {
						$this->content->template['keywords'] = $menupunkt['url_metakeywords'];
					}
				}

				// Dem Menüpunkt die Extra-CSS-Datei noch als Klasse mitgeben, falls vorhanden.
				IfNotSetNull($menupunkt['extra_css_file']);
				if ($menupunkt['extra_css_file']) {
					$css_file = $menupunkt['extra_css_file'];
					$css_class = strtolower(str_replace(".css", "-css", $css_file));
					$css_class = preg_replace("/[^a-z-]/", "-", $css_class);
					$menupunkt['menuklasse'] .= " extra-css-" . $css_class;
				}

				// f. sonstige Anpassungen
				$menupunkt['lang_title'] = $this->diverse->encode_quote($menupunkt['lang_title']);

				// Fehlermeldungen durch mögl. fehlende Einträge beseitigen
				if(!isset($menupunkt['shift_anfang_bootstrap'])) {
					$menupunkt['shift_anfang_bootstrap'] = NULL;
				}

				if(!isset($menupunkt['shift_ende_bootstrap'])) {
					$menupunkt['shift_ende_bootstrap'] = NULL;
				}

				if(!isset($menupunkt['cat_cid'])) {
					$menupunkt['cat_cid'] = NULL;
				}

				if(!isset($menupunkt['has_sub'])) {
					$menupunkt['has_sub'] = NULL;
				}

				if ($front_back !== "BACK") {
					if ($this->cms->categories == 1) {
						// Kategorien rausholen
						if(empty($this->menucats)) {
							$catlist_data = $this->preset_categories(true);
						}
						$this->content->template['categories'] = 1;
					}
					IfNotSetNull($catlist_data);
					$menupunkt["categoryFound"] = array_reduce((array)$catlist_data,
						function ($categoryFound, $category) use ($menupunkt) {
							return $categoryFound || $category["cat_lang_id"] == $menupunkt["cat_cid"];
						}, false
					);
				}
				// g. Daten zuweisen
				$menu_data[$counter] = $menupunkt;
				$counter += 1;
			}
		}

		if ($front_back !== "BACK") {
			if ($this->cms->categories == 1) {
				$this->content->template["orphanedMenuItemsExist"] = array_reduce($menu_data,
					function ($orphanFound, $menu) {
						return $orphanFound || $menu["categoryFound"] == false;
					}, false
				);
			}
		}

		if (!empty ($menu_data)) {
			return $menu_data;
		}
		else {
			return false;
		}
	}

	/**
	 * gibt Anzahl (absolut) $differenz öffnende/schliesende <ul>s zurück
	 *
	 * @param $differenz
	 * @param int $level
	 * @param int $menuid
	 * @param string $shift_modus
	 * @param string $has_sub
	 * @param array $menus
	 * @return array
	 */
	function menu_ebenen_shift($differenz, $level = 0, $menuid = 0, $shift_modus = "", $has_sub="", $menus = [])
	{
		$antwort = array ();

		switch ($shift_modus) {
		case "SUB" :
			$shift_anfang = '<div><ul><li>';
			$shift_ende_vorgaenger = "";
			break;

		case "CLEAN" :
			$shift_anfang = '<div><ul><li>';
			$shift_ende_vorgaenger = "</li>";
			break;

		case "SITEMAP" :
			$shift_anfang = '<div class="sitemap_level_' . $level . '" id="sitemap_menu_' . $menuid . '"><ul><li class="inhaltmap" id="sitemap_menuid_' . $menuid . '">';
			$shift_ende_vorgaenger = "</li>";
			break;

		case "NAVIGATION" :
		default :
			// $shift_anfang = '<li><div class="untermenu' . $level . '" id="untermenucontainer' . $menuid . '"><ul><li>';
			$shift_anfang = '<div class="untermenu' . $level . '" ><ul><li>';
			// class="dropdown-menu"

			$shift_anfang_bootstrap = '<ul><li>';
			$shift_ende_vorgaenger = "</li>";
			$shift_ende_vorgaenger_bootstrap = "</li>";
			break;
		}

		if(!isset($shift_anfang_bootstrap)) {
			$shift_anfang_bootstrap = NULL;
		}

		if ($differenz > 0) {
			// Eine Ebene weiter runter
			$antwort['shift_anfang'] = $shift_anfang;
			$antwort['shift_anfang_bootstrap'] = $shift_anfang_bootstrap;
			$antwort['shift_ende'] = "</li>";
			$antwort['shift_ende_vorgaenger'] = "";
		}
		else {
			// Eine oder mehrere Ebenen hoch
			if ($shift_modus == "SITEMAP") {
				$antwort['shift_anfang'] = '<li id="sitemap_menuid_' . $menuid . '">';
			}
			else {
				$antwort['shift_anfang'] = '<li>';

				if ((count($menus) > 0 ? $this->has_menu_descendants($menuid, $menus) : $this->has_submenus($menuid))) {
					$antwort['shift_anfang_bootstrap'] = '<li class="has-dropdown">';
				}
				else {
					$antwort['shift_anfang_bootstrap'] = '<li>';
				}
			}
			$antwort['shift_ende'] = '</li>';

			//for ($i = 0; $i < abs($differenz); $i++) $shift_ende_vorgaenger = '</li></ul></div>' . $shift_ende_vorgaenger;
			if ($shift_modus == 'NAVIGATION') {
				for ($i = 0; $i < abs($differenz); $i++) $shift_ende_vorgaenger = '</li></ul>' . $shift_ende_vorgaenger;
			}
			else {
				for ($i = 0; $i < abs($differenz); $i++) $shift_ende_vorgaenger = '</li></ul></div>' . $shift_ende_vorgaenger;
			}
			$antwort['shift_ende_vorgaenger'] = $shift_ende_vorgaenger;

			if (empty($shift_ende_vorgaenger_bootstrap)) {
				$shift_ende_vorgaenger_bootstrap="";
			}

			for ($i = 0; $i < abs($differenz); $i++) $shift_ende_vorgaenger_bootstrap = '</li></ul>' . $shift_ende_vorgaenger_bootstrap;
			$antwort['shift_ende_vorgaenger_bootstrap'] = $shift_ende_vorgaenger_bootstrap;
		}
		return $antwort;
	}

	/**
	 * @param string $front_back
	 * @param int $aktu_menuid
	 */
	function menuy_select($front_back = "front", $aktu_menuid = 0)
	{
		if (!$aktu_menuid) {
			$aktu_menuid = $this->checked->menuid;
		}

		if ($front_back == "back") {
			$temp_loop_data = &$this->data_back;
		}
		else {
			$temp_loop_data = &$this->data_front;
		}

		if (!empty($temp_loop_data)) {
			$temp_count = 0;
			foreach ($temp_loop_data as $punkt) {
				if ($punkt['menuid'] == $aktu_menuid) {
					if ($aktu_menuid != $this->checked->menuid) $this->list_menuy_aktiv[] = $aktu_menuid;
					if ($punkt['untermenuzu']) $this->menuy_select($front_back, $punkt['untermenuzu']);
				}
				$temp_count++;
			}
		}
	}

	/**
	 * @param string $front_back
	 */
	function menuy_mark($front_back = "front")
	{
		if ($front_back == "back") {
			$temp_loop_data = &$this->menu_back;
		}
		else {
			$temp_loop_data = &$this->menu_front;
		}

		if (!empty($temp_loop_data) AND !empty($this->list_menuy_aktiv)) {
			$temp_count = 0;

			foreach ($temp_loop_data as $punkt) {
				if (in_array($punkt['menuid'], $this->list_menuy_aktiv)) {
					if ($punkt['level'] == 0) {
						$this->content->template['aktueller_menupunkt_level0'] = $punkt;
					}
					$temp_loop_data[$temp_count]['menuklasse'] = "menuy_aktiv";
				}
				$temp_count++;
			}
		}
	}

	/**
	 * Funktion zum sortieren des Menüs
	 *
	 * @param int $submenue_id
	 * @param string $pfad
	 * @param string $nummer
	 * @return array
	 */
	function menue_sortieren($submenue_id = 0, $pfad = "/", $nummer = "")
	{
		$temp_array = array();
		$erster = true;
		$vorheriger_menue_punkt = -1;
		$counter = 1;
		// Preset für aktiven Menü-Punkt. Wenn nichts ausgewählt, dann ist Menü-ID[1] aktiv
		if (!empty ($this->checked->menuid)) {
			$aktiv_menuid = $this->checked->menuid;
		}
		else {
			$aktiv_menuid = 1;
		}
		#$this->aufruf++;
		if (!empty($this->result_temp)) {
			foreach ($this->result_temp as $menue_punkt) {
				#$this->ipls++;
				$is_aktiv = false;
				$link_aktiv = true;

				if ($menue_punkt['untermenuzu'] == $submenue_id) {
					// Einige Tests
					if ($menue_punkt['menuid'] == $aktiv_menuid) {
						$is_aktiv = true;
						// Extra Stylesheet hinzufügen
						$this->check_extra_style($menue_punkt);
						$this->pfad_aktu_menupunkt = $pfad . $menue_punkt['menuid'] . "/";
						if (empty($this->checked->reporeid)) {
							$link_aktiv = false;
						}
					}
					// Einige zusätzliche Einträge für diesen Menü-Punkt vornehmen
					$menue_punkt['erster'] = $erster;
					$erster = false;

					$menue_punkt['letzter'] = true;
					if ($vorheriger_menue_punkt >= 0) {
						$temp_array[$vorheriger_menue_punkt]['letzter'] = false;
					}
					// urlsencoding
					if (!defined("admin") || $this->is_tiny==true) {
						$menue_punkt['menuname_url'] = "";
						$artikelurl = "";

						if (empty($this->artdata[$menue_punkt['menuid']])) {
							$this->artdata[$menue_punkt['menuid']]="";
						}
						if(is_array($this->artdata[$menue_punkt['menuid']])) {
							$art_array = $this->artdata[$menue_punkt['menuid']];

							foreach($art_array as $key => $value) {
								if (empty($this->counter_artikel[$value][$menue_punkt['menuid']])) {
									$this->counter_artikel[$value][$menue_punkt['menuid']]="";
								}
								$this->counter_artikel[$value][$menue_punkt['menuid']]++;
							}
						}

						if (!is_array($this->artdata[$menue_punkt['menuid']])
							&& !empty($this->artdata[$menue_punkt['menuid']])
							&& $menue_punkt['menuid'] != 1
							&& !stristr( $menue_punkt['menulinklang'],"plugin:")
							&& $menue_punkt['menu_subteaser']!=1
							&& $menue_punkt['menulinklang']!="guestbook.php"
							&& $menue_punkt['menulink']!="guestbook.php"
						) {
							// es existiert nur ein Artikel
							$artikelurl =(($this->artdata[$menue_punkt['menuid']])) . "";

							if ($this->cms->mod_free==1) {
								$artikelurl = $artikelurl . ".html";
							}
							//Für die freie Variante Übergeben

							$free_artikelurl =(($this->artdata[$menue_punkt['menuid']])) ;

							if (empty($this->counter_artikel[$free_artikelurl][$menue_punkt['menuid']])) {
								$this->counter_artikel[$free_artikelurl][$menue_punkt['menuid']]="";
							}
							$this->counter_artikel[$free_artikelurl][$menue_punkt['menuid']]++;


							$last2  = substr($free_artikelurl,-5,5);
							if ($last2 !=".html") {
								if ($this->cms->artikel_url_mit_html) {
									$free_artikelurl.=".html";
								}
							}
							if (count($this->counter_artikel[$free_artikelurl])>1) {
								$free_artikelurl = str_ireplace(".html", "-m-" . $menue_punkt['menuid'] . ".html", $free_artikelurl);
							}
						}
						else {
							$free_artikelurl=$menue_punkt['url_menuname'];
							$last  = substr($free_artikelurl,-1,1);
							$last2 = substr($free_artikelurl,-5,5);
							if ($last !="/" && $last2 !=".html") {
								$free_artikelurl.="/";
							}
						}
						$first_url=$free_artikelurl[0];
						if ($first_url=="/") {
							$free_artikelurl=substr($free_artikelurl,1,strlen($free_artikelurl));
						}
						$free_artikelurl=str_replace("//","/",$free_artikelurl);
						// $menue_punkt['url_menuname']=str_replace("&","und",$menue_punkt['url_menuname']);
						$menue_punkturl = ($menue_punkt['url_menuname']);

						$menue_punkt['menuname_url'] = (($menue_punkturl)) . "/" . $artikelurl;

						//Übergeben der FREE sprechenden url
						if (!empty($this->cms->url_lang_var)) {
							$menue_punkt['menuname_free_url'] =  $free_artikelurl;
						}
						else {
							$menue_punkt['menuname_free_url'] =  $free_artikelurl;
						}

						// checken, ob die freie url im menuelink ueberschrieben wurde - arbeitet zusammen mit code aus der menu_data_read()
						if ($menue_punkt["url_menuname"] === $menue_punkt["menulinklang"]) {
							$menue_punkt["menuname_free_url"] = substr($menue_punkt["url_menuname"], 1);
						}

						// Es ist ein Unterpunkt, dann zusätzlich einfügen
						if ($menue_punkt['untermenuzu'] > 0) {
							$menue_punkt['untermenuzu'];
							// items durchloopen
							if ($this->vorganger_level == $menue_punkt['level'] and $menue_punkt['untermenuzu'] != 0) {
							}
							else {
								$this->vgm[] = $this->vorganger_menuname;
							}
							if ($this->vorganger_level > $menue_punkt['level']) {
								$this->vgm[$menue_punkt['level']] = $menue_punkturl;
								$this->vgm = array_slice($this->vgm, 0, $menue_punkt['level']);
							}

							$urld = "";
							$i = 0;
							foreach ($this->vgm as $vgm) {
								// Nur für den richtigen Level Übergeben
								if ($i >= $menue_punkt['level']) {
									continue;
								}
								$urld .= $this->urlencode($vgm) . "/";
								$i++;
							}
							$menue_punkt['menuname_url'] = $urld . $menue_punkt['menuname_url'];
						}

						if ($menue_punkt['untermenuzu'] == 0) {
							$this->vgm = array();
						}

						$menue_punkt['menuname_url'] = $this->cms->webvar . $menue_punkt['menuname_url'];

					}
					$menue_punkt['pfad'] = $pfad;
					if (!empty($menue_punkt['menulinklang'])) {
						$menue_punkt['menulink'] = $menue_punkt['menulinklang'];
					}
					$menue_punkt['is_aktiv'] = $is_aktiv;
					$menue_punkt['link_aktiv'] = $link_aktiv;
					$menue_punkt['nummer'] = $nummer . $counter;

					if (empty($vorganger_menuid)) {
						$vorganger_menuid="";
					}
					if (empty($vorganger_orderid)) {
						$vorganger_orderid="";
					}

					IfNotSetNull($menue_punkt['url_menuname']);
					$menue_punkt['vorgaenger_menuid'] = $vorganger_menuid;
					$menue_punkt['vorgaenger_orderid'] = $vorganger_orderid;
					// Daten an temporäres Array zuweisen
					$temp_array[] = $menue_punkt;
					$vorheriger_menue_punkt = count($temp_array) - 1;
					$this->vorganger_menuname = $menue_punkt['url_menuname'];
					$this->vorganger_level = $menue_punkt['level'];
					// Umtermenü-Punkte hinzufügen (per Rekursion)
					#if ($menue_punkt['level']<2)
					#{
					if (!empty($this->untermenuzu[$menue_punkt['menuid']])) {
						$temp_array = array_merge($temp_array, $this->menue_sortieren($menue_punkt['menuid'], $pfad . $menue_punkt['menuid'] . "/", $nummer . $counter . "."));
					}
					// Abschliesenden Zuweisungen für nächsten Durchlauf
					$counter += 1;

					$vorganger_menuid = $menue_punkt['menuid'];
					$vorganger_orderid = $menue_punkt['order_id'];
				}
			}
		}
		return $temp_array;
	}

	/**
	 * Sortiert die "ausgelassenen" (Unter-)Menü-Punkte, für die der User Rechte zum Veröffentlichen hat in die Liste mit ein
	 *
	 * @param array $menue_data
	 * @return array
	 */
	function menue_sub_sortieren($menue_data = array())
	{
		if (count($this->result_temp) > count($menue_data)) {
			$temp_menue_ids = array();
			// 1. Menu-IDs aller schon "registrierten" Menü-Punkte sammeln
			if (!empty($menue_data)) {
				foreach($menue_data as $menue_punkt) $temp_menue_ids[] = $menue_punkt['menuid'];
			}
			// 2. Menü-Punkte aus SQL-Abfrage durchgehen und testen ob sie schon registriert sind.
			// wenn nicht, werden sie an $menue_data angehängt.
			if (!empty($this->result_temp)) {
				foreach($this->result_temp as $menue_punkt) {
					if (!in_array($menue_punkt['menuid'], $temp_menue_ids)) $menue_data[] = $menue_punkt;
				}
			}
		}
		return $menue_data;
	}

	/**
	 *
	 */
	function make_access()
	{
		if (defined("admin")) {
			$plugin_pfad = "../../";

			$menu = $this->menu->data_back_complete;
			$i = 0;
			foreach ($menu as $menupunkt) {
				if (strpos("XXX_" . $menupunkt["menulink"], "plugin:")) {
					$the_template = str_replace("plugin:", "", $menupunkt["menulink"]);
					$the_template = $plugin_pfad . "" . $the_template;

					$the_template = "" . $the_template;
					$menu[$i]["template"] = "&amp;template=" . $the_template;
					$menu[$i]["menulink"] = "plugin.php";
				}
				else {
					$menu[$i]["template"] = NULL;
				}
				$i++;
			}
			$this->content->template['menuallback'] = $menu;
		}
	}
	/**
	 * Damit wird das Fuss Menue erstellt fuer den schnellen Zugang via accesskey
	 */
	function make_access_foot()
	{
		if (defined("admin"))
		{
			$plugin_pfad = "../../";

			$menux = $this->data_back_complete;
			$i = 0;
			if (is_array($menux)) {
				foreach ($menux as $menupunkt) {
					if (strpos("XXX_" . $menupunkt["menulink"], "plugin:")) {
						$the_template = str_replace("plugin:", "", $menupunkt["menulink"]);
						$the_template = $plugin_pfad . "" . $the_template;

						$the_template = "" . $the_template;
						$menux[$i]["template"] = "&amp;template=" . $the_template;
						$menux[$i]["menulink"] = "plugin.php";
					}
					else {
						$menux[$i]['template'] = NULL;
					}

					if ($menux[$i]["menuid"] < 10) {
						$menux[$i]["menuid2"] = ("0" . $menux[$i]["menuid"]);
					}
					else {
						$menux[$i]["menuid2"] = ($menux[$i]["menuid"]);
					}
					$i++;
				}
				asort($menux);
			}
			$this->content->template['menuallback'] = $menux;
		}
	}

	/**
	 * @param $get
	 * @return bool|mixed|null
	 */
	function http_request($get)
	{
		if (empty($_SESSION['curl_ok_aktivierung_link_not_ok'])) {
			$url="http://verify.papoo.de/".($get);
			$curl_ret = null;
			if (curl::installed()) {
				try {
					$curl = @new curl($url);
					@$curl->setopt(CURLOPT_TIMEOUT, 5);
					@$curl->setopt(CURLOPT_RETURNTRANSFER, true);
					@$curl->setopt(CURLOPT_HEADER, 0);
					@$curl->setopt(CURLOPT_USERAGENT, "Check Agent");
					$curl_ret = @$curl->exec();
				}
				catch (curl_exception $e) {
					$curl_ret = false;
				}
			}
			if (empty($curl_ret)) {
				$_SESSION['curl_ok_aktivierung_link_not_ok']="NO";
			}
		}
		IfNotSetNull($curl_ret);
		return $curl_ret;
	}

	/**
	 *
	 */
	function check_domain()
	{
		//Zuerst checken ob DM schonmal aktviert wurde
		if (!$this->check_old_aktivierung() && !empty($_SERVER['HTTP_REFERER'])) {
			$server_array=explode("/",$_SERVER['HTTP_REFERER']);
			$real_domain=$server_array['0']."/".$server_array['1']."/".$server_array['2'];
			if (!strstr("www.",$real_domain)) {
				$real_domain=str_replace("http://","http://www.",$real_domain);
			}
			$url=$this->title;
			$get='index.php?url='.urlencode($url).'&realurl='.urlencode($real_domain).'&version='.urlencode(PAPOO_VERSION_STRING).'&check=1';
			if ($real_domain=="http://localhost" || $real_domain=="http://127.0.0.1") {
				$this->content->template['not_verified']="";
			}
			else {
				$rdata=$this->http_request($get);
				if ($rdata=="domain_not_ok") {
					$this->content->template['not_verified']="ok";
				}
				else {
					$content=$real_domain.";".(time()+691200);
					$this->diverse->write_to_file("/interna/templates_c/aktivierung.txt",$content);
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	function check_old_aktivierung()
	{
		$aktivierungs_file=PAPOO_ABS_PFAD."/interna/templates_c/aktivierung.txt";
		$aktivierungs_file=str_replace("//","/",$aktivierungs_file);
		if (file_exists($aktivierungs_file)) {
			$data=@file_get_contents($aktivierungs_file);
			$dat_array=explode(";",$data);
			$server_array=explode("/",$_SERVER['HTTP_REFERER']);
			$real_domain=$server_array['0']."/".$server_array['1']."/".$server_array['2'];
			if (!strstr("www.",$real_domain)) {
				$real_domain=str_replace("http://","http://www.",$real_domain);
			}
			if (!strstr("www.",$_SERVER['HTTP_REFERER'])) {
				$_SERVER['HTTP_REFERER']=str_replace("http://","http://www.",$_SERVER['HTTP_REFERER']);
			}

			if ($dat_array['0']==$real_domain && $dat_array['1']>time()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Urls sauber umkodieren, damit die lesbar beleiben
	 *
	 * @param string $url
	 * @return mixed|string|string[]|null
	 */
	function urlencode($url = "")
	{
		if (!stristr($url,":::")) {
			$url = $this->replace_uml($url);
			//Wenn mod_free deaktivieren
			if ($this->cms->mod_free!=1) {
				$url = urlencode($url);
			}
		}
		return $url;
	}

	/**
	 * Umlaute und Spezialfälle ersetzen
	 *
	 * @param string $url
	 * @return mixed|string|string[]|null
	 */
	function replace_uml($url = "")
	{
		if (!stristr($url,":::"))
		{
			// Sonderzeichen ersetzen
			$url = str_ireplace(" ", "-", $url);
			$url = str_ireplace("_", "-", $url);
			$url = str_ireplace('&', "und", $url);
			$url = str_ireplace('?', "", $url);
			$url = str_replace('\\', '-', $url);
			$url = str_ireplace('"', '', $url);
			$url = str_ireplace("'", '', $url);
			$url = str_ireplace("%","",$url);
			$url = str_ireplace("<","",$url);
			$url = str_ireplace(">","",$url);
			$url = str_ireplace(",","",$url);
			$url = str_ireplace(";","",$url);
			$url = str_ireplace(":","",$url);

			# Kleinschreibung
			if (function_exists('mb_strtolower')) {
				$url = mb_strtolower($url, 'utf-8');
			}
			else {
				$url = strtolower(($url));
			}
			# Umlaute, etc. ersetzen
			$url = replace_umlaute($url);

			# Wenn nicht mod_free
			if ($this->cms->mod_free!=1) {
				$url = str_ireplace('/', '-', $url);
				$url = str_ireplace("\.","",$url);
				$url = urlencode($url);
			}
			else {
				$url = make_ascii_safe_string($url, '-', 'a-zA-Z0-9./-');
			}

			$url=str_ireplace("%","",$url);
		}
		return $url;
	}
}

$menu = new menu_class();