<?php

/**
 * Klasse die die Tabelle 'papoo_user' wiederspiegelt.
 * Jede Instanz dieser Klasse ist ein Benutzer-Account des Papoo-Systems.
 */
class User extends ActiveRecord\Model
{
	static $table_name;

	static $primary_key = 'userid';

	static $has_many = [
		['lookups', 'class_name' => 'GroupUserLookup', 'foreign_key' => 'userid'],
		['groups', 'through' => 'lookups', 'class_name' => 'Group', 'primary_key' => 'gruppeid']
	];

	static $validate_uniqueness_of = ['email', 'username'];

	static $before_save = ['callback_hash_password'];
	static $before_destroy = ['callback_prevent_deletion_of_root_and_jeder'];

	static $after_save = ['callback_save_groups'];

	/**
	 * Gibt die Gruppen Assoziation zurueck.
	 *
	 * (Sollte eigentlich durch $has_many funktionieren, geht aber mit den custom id namen anscheinend nicht richtig.)
	 * @return array Gibt eine Array zurueck, wobei jeder Eintrag ein Objekt der Gruppe ist, welchem dieser
	 * Benutzer angehoerig ist.
	 * @see Group
	 */
	public function get_groups()
	{
		foreach($this->lookups as $lookup) {
			$groups[] = $lookup->group;
		}
		return $groups;
	}

	/**
	 * @param $groups
	 * @return bool
	 */
	public function set_groups($groups)
	{
		$group_ids = [];
		foreach($groups as $group)
		{
			$group_ids[] = ($group instanceof Group) ? $group->gruppeid : $group;
		}

		for($i = 0; $i < count($this->lookups); $i++)
		{
			if(!in_array($this->lookups[$i]->gruppenid, $group_ids)) {
				unset($this->lookups[$i]);
			}
		}

		$to_add_group_ids = array_udiff($group_ids, $this->lookups, function($a, $b) {
			$a = ($a instanceof GroupUserLookup ? $a->gruppenid : $a);
			$b = ($b instanceof GroupUserLookup ? $b->gruppenid : $b);
			if($a == $b) {
				return 0; 
			}
			return $a < $b ? -1 : 1;
		});

		foreach($to_add_group_ids as $to_add_group_id)
		{
			$this->lookups[] = GroupUserLookup::create(['gruppenid' => $to_add_group_id, 'userid' => $this->userid]);
		}

		return true;
	}

	/**
	 * @return bool
	 * @throws ActiveRecord\RecordNotFound
	 */
	public function callback_save_groups()
	{
		foreach($this->lookups as $lookup) {
			// FIXME: Funktion existiert nicht
			$lookup->save();
		}

		$all_user_lookups = GroupUserLookup::find('all', ['userid' => $this->userid]);

		$delete_lookups = array_udiff($all_user_lookups, $this->lookups, function($a, $b) {
			$a = ($a instanceof GroupUserLookup ? $a->gruppenid : $a);
			$b = ($b instanceof GroupUserLookup ? $b->gruppenid : $b);
			if($a == $b) {
				return 0; 
			}
			return $a < $b ? -1 : 1;
		});

		foreach($delete_lookups as $lookup) {
			// FIXME: Funktion existiert nicht
			$lookup->delete();
		}
		return true;
	}

	/**
	 * Callback der dafuer sorgt, dass das Passwort immer gehasht in der Datenbank steht,
	 * aber ein schon gehashtes Passwort nicht nochmal hasht.
	 * (Nicht Teil der API, muss public sein)
	 *
	 * @return boolean
	 */
	public function callback_hash_password()
	{
		if($this->attribute_is_dirty('password')) {
			$this->password = md5($this->password);
		}
		return true;
	}

	/**
	 * @return boolean
	 */
	public function callback_prevent_deletion_of_root_and_jeder()
	{
		if($this->userid == 10 or $this->userid == 11) {
			return false;
		}
		return true;
	}
}

// Direkt in die Klasse schreiben geht nicht, der kann das dann nicht parsen
global $db_praefix;
User::$table_name = "{$db_praefix}papoo_user";