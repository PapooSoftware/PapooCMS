<?php

class Read
{
	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;
	}

	/**
	 * @shortdesc Liefert die aktive SprachID
	 * @return mixed
	 */
	public function active_language_id()
	{
		return $this->language_id($this->content->template['lang_short']);
	}

	/**
	 * @shortdesc Liefert eine Sprache.
	 * @param $val {lang_short|lang_long}
	 * @return mixed
	 */
	public function language_id($val)
	{
		$sql = sprintf(
			'SELECT EXISTS(SELECT 1 FROM %s
             WHERE lang_short = "%s") as bool',

			DB_PRAEFIX . "papoo_name_language",
			$val
		);
		$val_is_lang_short = $this->db->get_results($sql, ARRAY_A)[0]['bool'];

		$sql = sprintf(
			'SELECT EXISTS(SELECT 1 FROM %s
             WHERE lang_long = "%s") as bool',

			DB_PRAEFIX . "papoo_name_language",
			$val
		);
		$val_is_lang_long = $this->db->get_results($sql, ARRAY_A)[0]['bool'];

		if ($val_is_lang_short) {
			$sql = sprintf(
				'SELECT lang_id
                FROM %s
                WHERE lang_short = "%s"
                LIMIT 1',

				DB_PRAEFIX . "papoo_name_language",
				$val
			);
		}
		elseif ($val_is_lang_long) {
			$sql = sprintf(
				'SELECT lang_id
                FROM %s
                WHERE lang_long = "%s"
                LIMIT 1',

				DB_PRAEFIX . "papoo_name_language",
				$val
			);
		}
		$language = $this->db->get_results($sql, ARRAY_A)[0];
		return $language['lang_id'];
	}

	/**
	 * @param $user_name
	 * @return mixed
	 * @desc Liefert die ID zu einem Usernamen
	 */
	public function user_id($user_name)
	{
		$sql = sprintf(
			'SELECT userid AS id
             FROM %s
             WHERE username = "%s"',

			DB_PRAEFIX . "papoo_user",
			$user_name
		);
		$results = $this->db->get_results($sql, ARRAY_A);
		$id = $results[0]['id'];
		return $id;
	}

	/**
	 * @return mixed
	 * @desc Liefert die aktuelle User ID
	 */
	public function active_user_id()
	{
		return $this->user_id($this->content->template['username']);
	}

	/**
	 * @param $user_id
	 * @desc Liefert Daten zu einem User.
	 */
	public function user_data($user_id)
	{
		$sql = sprintf(
			'SELECT userid AS id,
                    email,
                    password AS password_hash,
                    user_vorname AS vorname,
                    user_nachname AS nachname

             FROM %s
             WHERE username = "%s"',

			DB_PRAEFIX . "papoo_user",
			$user_id
		);
		$results = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @param $user_id
	 * @return array
	 * Liefert die Gruppen, in denen ein User ist
	 */
	public function user_groups($user_id)
	{
		$sql = sprintf(
			'SELECT gruppenid
             FROM %s
             WHERE userid = "%s"',

			DB_PRAEFIX . "papoo_lookup_ug",
			$user_id
		);
		$results = $this->db->get_results($sql, ARRAY_A);

		foreach ($results as $result) {
			$ids[] = $result['gruppenid'];
		}

		return $ids;
	}

	/**
	 * @param $article_id
	 * @return mixed
	 * Liefert Daten zu einem Artikel
	 */
	public function article_data($article_id)
	{
		$sql = sprintf(
			'SELECT lan_repore_id AS id,
                    header AS name,
                    lan_teaser AS teaser,
                    lan_article AS inhalt,
                    url_header AS url
            FROM %s
            WHERE lan_repore_id = %u
            AND lang_id = %u',

			DB_PRAEFIX . "papoo_language_article",
			$article_id,
			$this->active_language_id()
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		$article_data = $results[0];
		return $article_data;
	}

	/**
	 * @param $user_id
	 * @return array
	 * @desc Liefert die IDs der Artikel, die ein User lesen kann
	 */
	public function readable_article_ids($user_id)
	{
		$groups = implode(",", $this->user_groups($user_id));

		$sql = sprintf(
			'SELECT DISTINCT article_id AS id
             FROM %s
             WHERE gruppeid_id IN (%s)',

			DB_PRAEFIX . "papoo_lookup_article",
			$groups
		);

		foreach ($this->db->get_results($sql, ARRAY_A) as $result) {
			$article_ids[] = $result['id'];
		}

		return $article_ids;
	}

	/**
	 * @shortdesc Liefert alle Artikel, die vom aktuell eingeloggten Benutzer gelesen werden können
	 * @return array
	 */
	public function readable_articles()
	{
		$readable_article_ids = $this->readable_article_ids($this->active_user_id());

		foreach ($readable_article_ids as $id) {
			$articles[] = $this->article_data($id);
		}

		$new_order = array();
		foreach ($articles as $article) {
			$new_order[$article['id']] = $article;
		}

		return $new_order;
	}

	/**
	 * @param $user_id
	 * @return array
	 * @desc Liefert die IDs aller Artikel, die ein User bearbeiten kann
	 */
	public function writable_article_ids($user_id)
	{
		$groups = implode(",", $this->user_groups($user_id));

		$sql = sprintf(
			'SELECT DISTINCT article_wid_id AS id
             FROM %s
             WHERE gruppeid_wid_id IN (%s)',

			DB_PRAEFIX . "papoo_lookup_write_article",
			$groups
		);

		foreach ($this->db->get_results($sql, ARRAY_A) as $result) {
			$article_ids[] = $result['id'];
		}

		return $article_ids;
	}

	/**
	 * @shortdesc Liefert die IDs aller Artikel, die vom aktuell eingeloggten Benutzer verändert werden können
	 * @return array
	 */
	public function writable_articles()
	{
		$writable_article_ids = $this->writable_article_ids($this->active_user_id());
		return $writable_article_ids;
	}

	/**
	 * @desc Liefert alle Zuordnungen von Artikeln zu Artikeln aus der Datenbank.
	 * @return array
	 */
	public function artikel_artikel_zuordnungen()
	{
		$sql = sprintf(
			'SELECT * FROM %s
            ',

			DB_PRAEFIX . "querverlinkungen_artikel_artikel"
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		return $results;
	}

	/**
	 * @shortdesc Liefert alle Artikelzuordnungen zu einer bestimmten Artikel ID.
	 * @param $id
	 * @return array
	 */
	public function artikel_artikel_zuordnungen_by_origin_id($id)
	{
		$sql = sprintf(
			'SELECT * FROM %s WHERE origin_id = %d
            ',

			DB_PRAEFIX . "querverlinkungen_artikel_artikel",
			$id
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		return $results;
	}

	/**
	 * @desc Liefert alle Zuordnungen von Menüpunkten zu Menüpunkten aus der Datenbank.
	 * @return array
	 */
	public function menuepunkte_menuepunkte_zuordnungen()
	{
		$sql = sprintf(
			'SELECT * FROM %s
            ',

			DB_PRAEFIX . "querverlinkungen_menuepunkte_menuepunkte"
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		return $results;
	}

	/**
	 * @param $id
	 * @return array
	 * @desc Liefert alle Menüpunktzuordnungen zu einer bestimmten Menüpunkt ID.
	 */
	public function menuepunkt_menuepunkt_zuordnungen_by_origin_id($id)
	{
		$sql = sprintf(
			'SELECT * FROM %s WHERE origin_id = %d
            ',

			DB_PRAEFIX . "querverlinkungen_menuepunkte_menuepunkte",
			$id
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		return $results;
	}

	/**
	 * @param $menuepunkt_id
	 * @return mixed
	 * @desc Liefert Daten zu einem Menüpunkt
	 */
	public function menuepunkt_data($menuepunkt_id)
	{
		$sql = sprintf(
			'SELECT menuid_id AS id,
                    menuname AS name,
                    lang_title AS title,
                    url_menuname AS url
            FROM %s
            WHERE menuid_id = %u
            AND lang_id = %u',
			DB_PRAEFIX . "papoo_menu_language",
			$menuepunkt_id,
			$this->active_language_id()
		);

		$results = $this->db->get_results($sql, ARRAY_A);
		$article_data = $results[0];

		return $article_data;
	}

	/**
	 * @param $user_id
	 * @return array
	 * @desc Liefert die IDs von allen Menüpunkten, die ein User verändern kann
	 */
	public function readable_menuepunkt_ids($user_id)
	{
		$groups = implode(",", $this->user_groups($user_id));

		$sql = sprintf(
			'SELECT DISTINCT menuid_id AS id
             FROM %s
             WHERE gruppeid_id IN (%s)',

			DB_PRAEFIX . "papoo_lookup_me_all_ext",
			$groups
		);

		$results = $this->db->get_results($sql, ARRAY_A);

		foreach ($results as $result) {
			$menuepunkt_ids[] = $result['id'];
		}

		return $menuepunkt_ids;
	}

	/**
	 * @return mixed
	 * @desc Liefert die Daten aller Menüpunkte, die der aktuelle User lesen kann
	 */
	public function readable_menuepunkte()
	{
		$readable_menuepunkt_ids = $this->readable_menuepunkt_ids($this->active_user_id());

		foreach ($readable_menuepunkt_ids as $id) {
			$menuepunkte[] = $this->menuepunkt_data($id);
		}

		foreach ($menuepunkte as $menuepunkt) {
			$new_order[$menuepunkt['id']] = $menuepunkt;
		}

		return $new_order;
	}

	/**
	 * @param $user_id
	 * @return array
	 * @desc Liefert die Daten aller Menüpuntke, die ein User Verändern kann
	 */
	public function writable_menuepunkt_ids($user_id)
	{
		$groups = implode(",", $this->user_groups($user_id));

		$sql = sprintf(
			'SELECT DISTINCT menuid AS id
             FROM %s
             WHERE gruppenid IN (%s)',

			DB_PRAEFIX . "papoo_lookup_men_ext",
			$groups
		);

		foreach ($this->db->get_results($sql, ARRAY_A) as $result) {
			$menuepunkt_ids[] = $result['id'];
		}

		return $menuepunkt_ids;
	}

	/**
	 * @return array
	 * @desc Liefert die IDs aller Menüpunkte, die der aktuelle User verändern kann.
	 */
	public function writable_menuepunkte()
	{
		$writable_menuepunkt_ids = $this->writable_menuepunkt_ids($this->active_user_id());
		return $writable_menuepunkt_ids;
	}
}
