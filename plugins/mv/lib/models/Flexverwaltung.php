<?php

class Flexverwaltung extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'mv_id';

	protected $template_cache = null;

	public function entries(int $language_id, array $conditions = []) : array
	{
		return \Flexeintrag::all_by_sql($this->id, $language_id, "");
	}

	public function get_template()
	{
		if($this->template_cache) {
			return $this->template_cache;
		}

		$this->template_cache = \Flextemplate::find_by_mv_id($this->id);
		return $this->template_cache;
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Flexverwaltung::$table_name = "{$db_praefix}papoo_mv";
