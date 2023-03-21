<?php

#[AllowDynamicProperties]
class Feldtyp extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'mvcform_id';

	static public function has_default_name($mv_id)
	{
		return static::find(['conditions' => ['mvcform_defaulttreeviewname' => 1, 'mvcform_form_id' => $mv_id]]);
	}

	public function get_column_name()
	{
		return "{$this->mvcform_name}_{$this->id}";
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Feldtyp::$table_name = "{$db_praefix}papoo_mvcform";
