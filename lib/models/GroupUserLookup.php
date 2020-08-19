<?php

/**
 * Class GroupUserLookup
 */
class GroupUserLookup extends ActiveRecord\Model
{
	static $table_name;

	// static $primary_key = 'userid';
	
	static $belongs_to = [
		['user', 'primary_key' => 'userid', 'foreign_key' => 'userid'],
		['group', 'primary_key' => 'gruppeid', 'foreign_key' => 'gruppenid']
	];
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
GroupUserLookup::$table_name = "{$db_praefix}papoo_lookup_ug";