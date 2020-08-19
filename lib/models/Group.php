<?php

/**
 * Klasse die die Tabelle 'papoo_gruppe' wiederspiegelt (Gruppen name, beschreibung, etc.).
 */
class Group extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'gruppeid';

	static $has_many = [
		['lookups', 'class_name' => 'GroupUserLookup', 'foreign_key' => 'gruppenid'],
		['users', 'class_name' => 'User', 'through' => 'lookups', 'primary_key' => 'userid']
	];

	/**
	 * Gibt die Benutzer Assoziation zurueck.
	 *
	 * (Sollte eigentlich durch $has_many funktionieren, geht aber mit den custom id namen anscheinend nicht richtig.)
	 * @return array Gibt eine Array zurueck, wobei jeder Eintrag ein Objekt der User ist, die in dieser
	 * Gruppe sind.
	 *
	 * @see Group
	 */
	public function get_users()
	{
		foreach($this->lookups as $lookup) {
			$users[] = $lookup->user;
		}
		return $users;
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
Group::$table_name = "{$db_praefix}papoo_gruppe";