<?php
//if (eregi('userlogin_class.php', $_SERVER['PHP_SELF'])) die('You are not allowed to see this page directly');

/**
 * Class userlogin_class
 */
#[AllowDynamicProperties]
class userlogin_class
{
	/**
	 * userlogin_class constructor.
	 */
	function __construct()
	{
		global $cms, $checked, $db, $db_praefix,$content ;
		$this->cms = & $cms;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->db->praefix = & $db_praefix;
		$this->content = & $content;

		// Aufruf Aktionsweiche
		$this->make_userlogin();
	}

	function make_userlogin()
	{
		global $template;
		global $module;

		// FRONTEND:
		if (!defined("admin")) {
			if (empty($this->content->template['loggedin']) AND $module->module_aktiv['mod_userlogin_logout']) {
				$module->module_aktiv['mod_userlogin_logout'] = false;
			}

			if (strpos("XXX".$template, "userlogin/templates/front.html")) {
				global $cache;
				$cache->aktiv = false;

				/** @var user_class $user */
				global $user;

				$pageId = (int)$this->checked->page_id;

				$page = $this->loadPageWithAssignedGroups($pageId);
				$pageGroups = array_map(function ($group) { return $group["id"]; }, $page["groups"] ?? []);
				$userGroups = array_keys($user->get_groups());

				// Wenn man sich im Frontend in einem Menüpunkt befindet, der dieses Plugin ansteuert und zusätzlich
				// die Seite per POST angefragt wurde, ist davon auszugehen, dass ein Login stattgefunden hat. => Weiterleitung GET
				if ($_SERVER["REQUEST_METHOD"] === "POST") {
					$this->seeOther($_SERVER["REQUEST_URI"]);
				}

				if (empty($this->content->template['loggedin'])) {
					if ($module->module_aktiv['mod_login']) {
						$module->module_aktiv['mod_login'] = false;
					}

					if (!$this->checked->forgot) {
						global $artikel;
						$artikel->make_inhalt();
						$this->content->template['plugin']['userlogin']['templateweiche'] = "LOGIN";
					}
					else {
						$this->content->template['plugin']['userlogin']['templateweiche'] = "FORGOT";
						if ($this->checked->submituedat) {
							global $user;
							$user->remind_pass();
						}
					}
				}
				else if (empty(array_intersect($pageGroups, $userGroups))) {
					if ($module->module_aktiv['mod_login']) {
						$module->module_aktiv['mod_login'] = false;
					}

					global $artikel;
					$artikel->make_inhalt();

					$this->content->template['plugin']['userlogin']['templateweiche'] = "ACCESS_DENIED";

					// Pseudoartikel generieren, um über Zugriffsbeschränkung zu informieren
					$this->content->template["table_data"] = [
						[
							"uberschrift" => $this->content->template["plugin"]["userlogin"]["_access_denied"],
							"text" => $this->content->template["plugin"]["userlogin"]["_access_denied_text"],
						],
					];
				}
				else {
					$temp_texte = $this->text_laden($pageId, $this->cms->lang_id);
					$this->content->template['plugin']['userlogin']['text_angemeldet'] = $this->helper_frontend_texte_ersetzungen($temp_texte['text_angemeldet']);
				}
			}
		}

		// BACKEND:
		if (defined("admin")) {
			$protocol = empty($_SERVER["HTTPS"]) == false && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443 ? "https://" : "http://";

			$baseUrl =
				$protocol.$_SERVER["HTTP_HOST"].rtrim(PAPOO_WEB_PFAD, "/").
				"/interna/plugin.php?menuid={$this->checked->menuid}&template={$this->checked->template}";

			if (strpos("XXX".$template, "userlogin/templates/back.html")) {
				if (isset($this->checked->new)) {
					if ($_SERVER["REQUEST_METHOD"] === "POST") {
						$this->helper_tinymce_post_encode();
						$this->texte_speichern(null, $this->cms->lang_id, $this->checked->inhalt_ar['inhalt']);

						$_SESSION["plugin__userlogin"]["feedback"][] = "_page_created";
						$this->seeOther($baseUrl);
					}

					$this->helper_init_editor();

					$this->content->template["plugin"]["userlogin"]["groups"] = $this->loadGroups();
					$this->content->template["plugin"]["userlogin"]["isNew"] = true;
				}
				else if (isset($this->checked->edit)) {
					$pageId = (int)$this->checked->edit;

					if ($_SERVER["REQUEST_METHOD"] === "POST" && $this->checked->userlogin_action === "text_save") {
						$this->helper_tinymce_post_encode();
						$this->texte_speichern($pageId, $this->cms->lang_id, $this->checked->inhalt_ar['inhalt']);

						$_SESSION["plugin__userlogin"]["feedback"][] = "_page_saved";
						$this->seeOther($baseUrl);
					}

					$temp_texte = $this->text_laden($pageId, $this->cms->lang_id);
					$this->content->template['Beschreibung'] = $temp_texte['text_angemeldet'];
					$this->helper_init_editor();

					$this->content->template["plugin"]["userlogin"]["page"] = $this->loadPageWithAssignedGroups($pageId);
					$this->content->template["plugin"]["userlogin"]["groups"] = $this->loadGroups();
					$this->content->template["plugin"]["userlogin"]["page_group_ids"] = array_map(
						function ($group) {
							return (int)$group["id"];
						},
						$this->content->template["plugin"]["userlogin"]["page"]["groups"]
					);

					$this->content->template["plugin"]["userlogin"]["isEdit"] = true;
				}
				else if (isset($this->checked->delete)) {
					$pageId = (int)$this->checked->delete;

					if ($_SERVER["REQUEST_METHOD"] === "POST") {
						$this->deletePage($pageId);

						$_SESSION["plugin__userlogin"]["feedback"][] = "_page_deleted";
						$this->seeOther($baseUrl);
					}

					$this->content->template["plugin"]["userlogin"]["pageId"] = $pageId;
					$this->content->template["plugin"]["userlogin"]["isDelete"] = true;
				}
				else {
					$this->content->template["plugin"]["userlogin"]["pages"] = $this->loadPagesWithAssignedGroups();
				}

				$this->prepareFeedback();
			}

			$this->content->template["plugin"]["userlogin"]["baseUrl"] = htmlspecialchars($baseUrl);
		}
	}

	private function prepareFeedback()
	{
		$messages = isset($_SESSION["plugin__userlogin"]["feedback"]) && is_array($_SESSION["plugin__userlogin"]["feedback"])
			? $_SESSION["plugin__userlogin"]["feedback"]
			: [];

		if (count($messages) == 0) {
			return;
		}

		foreach (array_unique($messages) as $message) {
			$this->content->template["plugin"]["userlogin"]["feedback"][] = $this->content->template["plugin"]["userlogin"][$message];
		}

		unset($_SESSION["plugin__userlogin"]["feedback"]);
	}

	/**
	 * @param $url
	 */
	private function seeOther($url)
	{
		header("HTTP/1.1 303 See Other");
		header("Location: $url");
		exit;
	}

	/**
	 * @return array
	 */
	private function loadPagesWithAssignedGroups()
	{
		$pages = $this->db->get_results(
			"SELECT _page.id, _pageGroup.group_id, _papooGroup.gruppenname ".
			"FROM {$this->cms->tbname["userlogin_page"]} _page ".
			"LEFT JOIN {$this->cms->tbname["userlogin_page_group"]} _pageGroup ON _pageGroup.page_id = _page.id ".
			"LEFT JOIN {$this->cms->tbname["papoo_gruppe"]} _papooGroup ON _papooGroup.gruppeid = _pageGroup.group_id ".
			"ORDER BY _page.id, _pageGroup.group_id",
			ARRAY_A
		);

		// Gruppen pro Seite zusammenfassen
		$pages = array_reduce(
			$pages,
			function ($pages, $page) {
				$pageId = (int)$page["id"];

				if (array_key_exists($pageId, $pages) == false) {
					$pages[$pageId] = [
						"id" => $pageId,
						"groups" => [],
					];
				}

				$pages[$pageId]["groups"][] = [
					"id" => (int)$page["group_id"],
					"name" => $page["gruppenname"],
				];

				return $pages;
			},
			[]
		);

		return array_map(function ($page) {
			return array_merge($page, ["group_string" => implode(", ", array_column($page["groups"], "name"))]);
		}, $pages);
	}

	/**
	 * @param $page_id
	 * @return mixed|null
	 */
	private function loadPageWithAssignedGroups($page_id)
	{
		$pageId = (int)$page_id;

		$pages = $this->loadPagesWithAssignedGroups();

		return isset($pages[$pageId]) ? $pages[$pageId] : null;
	}

	/**
	 * @return array|void
	 */
	private function loadGroups()
	{
		return $this->db->get_results(
			"SELECT gruppeid `id`, gruppenname `name` ".
			"FROM {$this->cms->tbname["papoo_gruppe"]} _group ".
			"ORDER BY _group.gruppeid",
			ARRAY_A
		);
	}

	/**
	 * @param $page_id
	 */
	private function deletePage($page_id) {
		$pageId = (int)$page_id;

		// Aufgabe der Fremdschlüssel-DELETE-Constraints übernehmen, da MyISAM statt InnoDB verwendet wird.
		$this->db->query(
			"DELETE _page, _group, _language ".
			"FROM pdevppx07_userlogin_page _page ".
			"LEFT JOIN pdevppx07_userlogin_page_group _group ON _group.page_id = _page.id ".
			"LEFT JOIN pdevppx07_userlogin_page_language _language ON _language.page_id = _page.id ".
			"WHERE _page.id = $pageId"
		);
	}

	/**
	 * @param $page_id
	 * @param $lang_id
	 * @return array|void
	 */
	function text_laden($page_id, $lang_id)
	{
		$pageId = (int)$page_id;
		$langId = (int)$lang_id;

		return $this->db->get_row(
			"SELECT * ".
			"FROM {$this->cms->tbname["userlogin_page"]} _page ".
			"INNER JOIN {$this->cms->tbname["userlogin_page_language"]} _locale ON _locale.page_id = _page.id ".
			"WHERE _page.id = {$pageId} AND _locale.language_id = {$langId} ".
			"LIMIT 1",
			ARRAY_A
		);
	}

	/**
	 * @param $page_id
	 * @param int $lang_id
	 * @param string $text
	 */
	function texte_speichern($page_id, $lang_id = 0, $text = "")
	{
		$pageId = (int)$page_id;
		$langId = (int)$lang_id;

		// Ggf. neue Seite anlegen
		if ($page_id === null) {
			$this->db->query("INSERT INTO {$this->cms->tbname["userlogin_page"]} (id) VALUES (default)");
			$pageId = (int)$this->db->get_var("SELECT LAST_INSERT_ID()");
		}

		// Text speichern
		$this->db->query(
			"INSERT INTO {$this->cms->tbname["userlogin_page_language"]} ".
			"SET page_id = {$pageId}, language_id = {$langId}, text_angemeldet='{$this->db->escape($text)}' ".
			"ON DUPLICATE KEY UPDATE text_angemeldet = VALUES(text_angemeldet)"
		);

		$groupIds = isset($_POST["page_groups"]) && is_array($_POST["page_groups"]) ? $_POST["page_groups"] : [];

		// Gruppen aktualisieren
		$this->db->query("DELETE FROM {$this->cms->tbname["userlogin_page_group"]} WHERE page_id = {$pageId}");
		$this->db->query(
			"INSERT INTO {$this->cms->tbname["userlogin_page_group"]} (page_id, group_id) VALUES ({$pageId}, ".
			implode("), ({$pageId}, ", array_map(function ($id) { return (int)$id; }, $groupIds)).
			")"
		);
	}

	/**
	 * @param string $text
	 * @return mixed|string
	 */
	function helper_frontend_texte_ersetzungen($text = "")
	{
		$temp_return = $text;

		if (!empty($temp_return))
		{
			global $diverse;
			global $download;

			$temp_return = $diverse->do_pfadeanpassen($temp_return);
			$temp_return = $download->replace_downloadlinks($temp_return);
		}

		return $temp_return;
	}

	function helper_init_editor()
	{
		// 1. Test welcher Editor geladen werden soll
		$temp_user_editor_id = 3;
		if (isset($this->user->editor)) {
			$temp_user_editor_id = $this->user->editor;
		}
		$this->content->template['editor_default'] = $temp_user_editor_id;
		$this->content->template['tinymce_lang_short'] = $this->cms->lang_back_short;

		// 2. Editor initialisieren
		global $intern_artikel;
		// Bilder zuweisen
		$intern_artikel->get_images($this->cms->lang_back_id);
		// Downloads und Artikel zuweisen
		$intern_artikel->get_downloads($this->cms->lang_back_id);
		// CSS-Klassen zuweisen (f�r QuickTag-Editor)
		$intern_artikel->get_css_klassen();
	}

	function helper_tinymce_post_encode()
	{
		if (empty($this->checked->editor_name)) {
			$this->checked->editor_name = "";
		}
		if (empty($this->checked->inhalt_ar['inhalt'])) {
			$this->checked->inhalt_ar['inhalt'] = "";
		}
		if ($this->checked->editor_name == "tinymce" AND !empty($this->checked->inhalt_ar['inhalt'])) {
			global $diverse;
			$this->checked->inhalt_ar['inhalt'] = $diverse->recode_entity_n_br($this->checked->inhalt_ar['inhalt'], "nobr");
		}
	}
}

$userlogin = new userlogin_class();
