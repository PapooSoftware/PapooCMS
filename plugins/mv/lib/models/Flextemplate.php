<?php

#[AllowDynamicProperties]
class Flextemplate extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'id';

	static public function all($conditions = [])
	{
		global $db_praefix;
		// TODO Desgin-Pattern oder so hierfuer ausdenken
		\Flextemplate::$table_name = "{$db_praefix}papoo_mv_template_{$conditions['mv_id']}";

		unset($conditions['mv_id']);

		return call_user_func_array('parent::find',array_merge(array('all'),$conditions));
	}

	static public function find($conditions = [])
	{
		global $db_praefix;
		// TODO Desgin-Pattern oder so hierfuer ausdenken
		\Flextemplate::$table_name = "{$db_praefix}papoo_mv_template_{$conditions['mv_id']}";

		unset($conditions['mv_id']);

		return call_user_func_array('parent::find', $conditions);
	}

	static public function find_by_mv_id($mv_id)
	{                                                                        
		$mv_id = intval($mv_id);
		$sql = "SELECT table_name FROM information_schema.tables where table_name LIKE '%ppx07_papoo_mv_template_$mv_id'";
		if(count(static::find_by_sql($sql)) <= 0 ) {
			return null;
		}
		
		global $db_praefix;
		// TODO Desgin-Pattern oder so hierfuer ausdenken
		$table_name = "{$db_praefix}papoo_mv_template_{$mv_id}";
		return end(static::find_by_sql("SELECT * FROM $table_name LIMIT 1;"));
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Flextemplate::$table_name = "{$db_praefix}papoo_mv_template_1";
