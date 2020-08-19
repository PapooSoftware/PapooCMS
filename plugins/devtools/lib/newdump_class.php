<?php

/**
 * Class newdump_class
 */
class newdump_class {
	/**
	 * newdump_class constructor.
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $dumpnrestore, $db, $checked, $content, $user;
		$this->dumpnrestore = &$dumpnrestore;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->user = & $user;
		// Aktions-Weiche
		// **************
		global $template;
		if (defined("admin")) {
			$this->user->check_intern();
			if (strpos("XXX" . $template, "newdump_backend.html")) {
				if (isset($this->checked->newdump_action)) {
					switch($this->checked->newdump_action) {
					case "delete":
						$this->dumpnrestore->dump_save(); // L�scht Sicherungs-Datei
						$this->content->template['newdump_message'] = $this->content->template['plugin']['devtools']['sicherung_deleted'];
						$this->content->template['newdump_delete'] = true;
						break;
					case "dump":
						$tablelist = $this->checked->tablelist;
						if(!empty($tablelist)) {
							$this->dumpnrestore->doupdateok = "ok";
							$this->dumpnrestore->donewdump = "ok";
							// Test ob nur Daten (keine Struktur gesichert werden soll
							if(isset($this->checked->nur_daten) && $this->checked->nur_daten) {
								$this->dumpnrestore->export = true;
							}
							$this->dumpnrestore->dump_save(); // L�scht alte Sicherungs-Datei f�r append.
							foreach($tablelist as $tabelle) {
								$this->dumpnrestore->querys = $this->dumpnrestore->dump_load($tabelle, 0, 100000);
								$this->dumpnrestore->dump_save("append");
							}
							$this->content->template['newdump_message'] = '<a href="' . $this->dumpnrestore->dump_filename . '">
							' . $this->content->template['plugin']['devtools']['sicherung_download'] . '</a>';
						}
						break;
					default:
						$tabellen = $this->dumpnrestore->make_tablelist();
						$this->content->template['newdump_tablelist'] = $tabellen;
					}
				}
				else {
					$tabellen = $this->dumpnrestore->make_tablelist();
					$this->content->template['newdump_tablelist'] = $tabellen;
				}
			}
		}
	}
}

$newdump = new newdump_class();
