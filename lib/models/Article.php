<?php

/**
 * Class Article
 */
class Article extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'reporeid';

	/**
	 * @param int $language_id
	 * @return mixed
	 */
	public function give_url_header(int $language_id)
	{
		global $db_praefix;
		$language = static::find_by_sql("SELECT url_header as lan_url_header FROM {$db_praefix}papoo_language_article WHERE lan_repore_id = {$this->id} AND lang_id = $language_id;");
		$language = end($language);
		return $language->lan_url_header;
	}

	/**
	 * @param int $language_id
	 * @return mixed
	 */
	public function give_header(int $language_id)
	{
		global $db_praefix;
		$language = static::find_by_sql("SELECT header as lan_header FROM {$db_praefix}papoo_language_article WHERE lan_repore_id = {$this->id} AND lang_id = $language_id;");
		$language = end($language);
		return $language->lan_header;
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Article::$table_name = "{$db_praefix}papoo_repore";
