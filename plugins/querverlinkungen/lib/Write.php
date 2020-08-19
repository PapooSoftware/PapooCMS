<?php

/**
 * Class Write
 */
class Write
{
	/**
	 * Write constructor.
	 */
	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;

		$this->read = new Read();
	}

	public function artikel_artikel_zuordnungen()
	{
		$checked = (array)$this->checked;
		$table_name = DB_PRAEFIX . "querverlinkungen_artikel_artikel";

		if (isset($checked['click']) && $checked['click']) {
			// Erstmal alle Zuordnungen löschen
			$sql = sprintf('
                        DELETE FROM %s
                    ',
				$table_name
			);
			$this->db->query($sql);

			// Und dann die ausgewählten eintragen

			foreach ($checked as $k => $v) {
				if (strstr($k, "artikel_artikel")) {
					// Wir haben was einzutragen
					$target_ids = array(); // Hier landen alle IDs, die eingetragen werden
					$origin_id = explode("_", $k)[2]; // In diesem Artikel bedinden wir uns gerade

					// Ein und dieselbe Zuordnung soll nicht mehrfach eingetragen werden.
					$v = array_unique($v);

					foreach ($v as $target_artikel) {
						$re = '/\[ID:\s(\d+)\]/';
						$str = $target_artikel;
						preg_match_all($re, $str, $matches);
						$target_id = $matches[1][0];
						$target_ids[] = $target_id;
					}

					$sql = sprintf('
                        INSERT INTO %s (origin_id, target_id)
                        VALUES',
						$table_name);

					foreach ($target_ids as $k => $target_id) {
						$sql .= sprintf('(%u, %u)',
							$origin_id, $target_id
						);
						if ($k != (sizeof($target_ids) - 1)) {
							$sql .= ',';
						}
						else {
							$sql .= ';';
						}
					}
					$this->db->query($sql);
				}
			}
		}
	}

	public function menuepunkte_menuepunkte_zuordnungen()
	{
		$checked = (array)$this->checked;
		$table_name = DB_PRAEFIX . "querverlinkungen_menuepunkte_menuepunkte";

		if (isset($checked['click']) && $checked['click']) {
			// Erstmal alle Zuordnungen löschen
			$sql = sprintf('
                        DELETE FROM %s
                    ',
				$table_name
			);
			$this->db->query($sql);

			// Und dann die ausgewählten eintragen

			foreach ($checked as $k => $v) {
				if (strstr($k, "menuepunkte_menuepunkte")) {
					// Wir haben was einzutragen
					$target_ids = array(); // Hier landen alle IDs, die eingetragen werden
					$origin_id = explode("_", $k)[2]; // In diesem Artikel bedinden wir uns gerade

					// Ein und dieselbe Zuordnung soll nicht mehrfach eingetragen werden.
					$v = array_unique($v);

					foreach ($v as $target_artikel) {
						$re = '/\[ID:\s(\d+)\]/';
						$str = $target_artikel;
						preg_match_all($re, $str, $matches);
						$target_id = $matches[1][0];
						$target_ids[] = $target_id;
					}

					$sql = sprintf('
                        INSERT INTO %s (origin_id, target_id)
                        VALUES',
						$table_name);

					foreach ($target_ids as $k => $target_id) {
						$sql .= sprintf('(%u, %u)',
							$origin_id, $target_id
						);
						if ($k != (sizeof($target_ids) - 1)) {
							$sql .= ',';
						}
						else {
							$sql .= ';';
						}
					}
					$this->db->query($sql);
				}
			}
		}
	}

	public function messe_zuordnungen()
	{
		$checked = (array)$this->checked;
		$table_name = DB_PRAEFIX . "plugin_mitarbeiter_messen";

		if ($checked['click']) {
			// Erstmal alle Zuordnungen löschen
			$sql = sprintf('
                        DELETE FROM %s
                    ',
				$table_name
			);
			$this->db->query($sql);

			$alle_mitarbeiter = $this->read->mitarbeiter();
			foreach ($checked as $k => $v) {
				if (strstr($k, "messe_mitarbeiter")) {
					// Wir haben was einzutragen
					$mitarbeiter_ids = array(); // Hier landen alle IDs, die eingetragen werden
					$messe_id = explode("_", $k)[2];
					if ($v != "") {
						foreach ($alle_mitarbeiter as $mitarbeiter) {
							// Für alle ausgewählten Mitarbeiter die IDs raussuchen, denn die kommen in die DB
							if (in_array($mitarbeiter['name'], $v)) {
								$mitarbeiter_ids[] = $mitarbeiter['id'];
							}
						}

						$sql = sprintf('
                        INSERT INTO %s (mitarbeiter_id, messe_id)
                        VALUES',
							$table_name);

						foreach ($mitarbeiter_ids as $k => $mitarbeiter_id) {
							$sql .= sprintf('(%u, %u)',
								$mitarbeiter_id, $messe_id
							);
							if ($k != (sizeof($mitarbeiter_ids) - 1)) {
								$sql .= ',';
							} else {
								$sql .= ';';
							}
						}
						$this->db->query($sql);
					}
				}
			}
		}
	}
}