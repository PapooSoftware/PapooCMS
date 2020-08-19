<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class intern_ordner_class
 */
class intern_ordner_class
{
	/**
	 * intern_ordner_class constructor.
	 */
	function __construct()
	{
		/**
		Klassen globalisieren
		 */

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// checkedblen Klasse einbinden
		global $checked;
		$this->checked = & $checked;
		// Diverse-Klasse einbinden
		global $diverse;
		$this->diverse = & $diverse;
	}

	/**
	 *
	 */
	function make_inhalt()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		switch ($this->checked->menuid) {

		case "62" :
			// Starttext Übergeben
			$this->show_ordner_bilder("papoo_kategorie_bilder");
			$this->content->template['extra_key'] ="_Bilder";
			break;

		case "63" :
			// Starttext Übergeben
			$this->show_ordner_bilder("papoo_kategorie_dateien");
			$this->content->template['extra_key'] ="_Dateien";
			break;

		case "72" :
			// Starttext Übergeben
			$this->show_ordner_bilder("papoo_kategorie_video");
			$this->content->template['extra_key'] ="_Video";
			break;

		default :
			break;

		}
	}

	/**
	 * Gruppenliste holen
	 */
	function hol_gruppen_liste()
	{
		// Gruppendaten holen
		$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM " . $this->cms->papoo_gruppe . " ORDER BY gruppenname ");
		$table_data2 = array ();
		// alle Gruppen durchloopen und aktive anhaken
		foreach ($result as $rowx) {
			// Administratoren nicht anzeigen. Diese bekommen immer alle Rechte.
			if ($rowx->gruppeid != 1) {
				$checkedx = "";
				if (!empty ($this->result_gr)) {
					foreach ($this->result_gr as $x) {
						if ($rowx->gruppeid == $x['0']) {
							$checkedx = 'nodecode:checked="checked"';
						}
					}
				}
				array_push($table_data2, array (
					'gruppename' => $rowx->gruppenname,
					'gruppeid' => $rowx->gruppeid,
					'checkedx' => $checkedx
				));
			}
		}
		$this->content->template['table_data'] = $table_data2;
	}

	/**
	 * Kategorien anzeigen und neue eingeben
	 *
	 * @param $pfad
	 */
	function show_ordner_bilder($pfad)
	{
		//Weiche
		if ($pfad == "papoo_kategorie_bilder") {
			$name = "bilder_cat_name";
			$catname = "bilder_cat_name";
			$catid = "bilder_cat_id";
			$catid_lookup = "bilder_cat_id_id";
			$lookup_name = "papoo_lookup_cat_images";
			$cattgruppe = "gruppeid_id";
			$level="image_sub_cat_level";
			$sub_cat_von="image_sub_cat_von";
			$order_by=" ORDER BY bilder_cat_name ASC ";
		}
		if ($pfad == "papoo_kategorie_dateien") {
			$name = "dateien_cat_name";
			$catname = "dateien_cat_name";
			$catid = "dateien_cat_id";
			$catid_lookup = "dateien_cat_id_id";
			$lookup_name = "papoo_lookup_cat_dateien";
			$cattgruppe = "gruppeid_id";
			$level="dateien_sub_cat_level";
			$sub_cat_von="dateien_sub_cat_von";
			$order_by=" ORDER BY dateien_cat_name ASC ";
		}

		if ($pfad == "papoo_kategorie_video") {
			$name = "video_cat_name";
			$catname = "video_cat_name";
			$catid = "video_cat_id";
			$catid_lookup = "video_cat_id_id";
			$lookup_name = "papoo_lookup_cat_video";
			$cattgruppe = "gruppeid_id";
			$level="video_sub_cat_level";
			$sub_cat_von="video_sub_cat_von";
			$order_by=" ORDER BY video_cat_name ASC ";
		}

		//Wenn ändern
		if (!empty ($this->checked->submit)) {

			if ($this->checked->image_sub_cat_von!=$this->checked->catid) {
				//Level der ausgewählten KAtegorie rausholen
				$sql = sprintf("SELECT %s FROM %s WHERE %s='%s'",
					$level,
					$this->cms->tbname[$pfad],
					$catid,
					$this->db->escape($this->checked->image_sub_cat_von)
				);
				$result_level = $this->db->get_var($sql);

				if (!empty($result_level)) {
					$result_level=$result_level+1;
				}
				else {
					if (!empty($this->checked->image_sub_cat_von)) {
						$result_level=$result_level+1;
					}
				}

				//Daten ändern
				$sql = sprintf("UPDATE %s SET 
										%s='%s', 
										%s='%s',
										%s='%s' 
										WHERE %s='%s'",
					$this->cms->tbname[$pfad],
					$name,
					$this->db->escape($this->checked->dir),
					$sub_cat_von,
					$this->db->escape($this->checked->image_sub_cat_von),
					$level,
					$result_level,
					$catid,
					$this->db->escape($this->checked->catid)
				);

				$this->db->query($sql);

				$insertid = $this->db->escape($this->checked->catid);

				$sql = sprintf("DELETE FROM %s WHERE %s='%s'",
					$this->cms->tbname[$lookup_name],
					$catid_lookup,
					$insertid
				);
				$this->db->query($sql);

				if (!empty ($this->checked->inhalt_ar)) {
					foreach ($this->checked->inhalt_ar['gruppe_write'] as $insert) {
						$insert=$this->db->escape($insert);

						$sqlin = "INSERT INTO " . $this->cms->tbname[$lookup_name] .
							" SET $catid_lookup='$insertid', 
									gruppeid_id='$insert' 
									";
						$this->db->query($sqlin);
					}
				}
				$sqlin = "INSERT INTO " . $this->cms->tbname[$lookup_name] .
					" SET $catid_lookup='$insertid', gruppeid_id='1' ";
				$this->db->query($sqlin);

				//Jetzt nochmal einen Reset durchführen der Ebenen

				$sql = sprintf("	SELECT * FROM %s ORDER BY %s,%s ASC",
					$this->cms->tbname[$pfad],
					$sub_cat_von,
					$level
				);
				$results = $this->db->get_results($sql,ARRAY_A);
				$max=20;
				if (is_array($results)) {
					for ($i=0;$i<$max;$i++) {
						foreach ($results as $key=>$value) {
							foreach ($results as $key1=>$value1) {
								if ($value1[$sub_cat_von]==$value[$catid]) {
									$neu[$value1[$catid]]=$value1;
									$neu[$value1[$catid]][$level]=$value[$level]+1;
								}
							}
						}
					}
				}
				IfNotSetNull($neu);
				if (is_array($neu)) {
					foreach ($neu as $key=>$value) {
						if ($value[$sub_cat_von]>0 ) {
							$level_nr=$value[$level];
							$sql=sprintf("	UPDATE %s SET 
											%s='%s' 
											WHERE %s='%s'",
								$this->cms->tbname[$pfad],
								$level,
								$level_nr,
								$catid,
								$key
							);
							$this->db->query($sql);
						}
					}
				}
				$sql=sprintf("	UPDATE %s SET 
											%s='0' 
											WHERE %s='0'",
					$this->cms->tbname[$pfad],
					$level,
					$sub_cat_von
				);
				$this->db->query($sql);
			}

			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($menuidid);

			$location_url = "./ordner.php?menuid=" . $this->checked->menuid . "&messageget=" . $menuidid;

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		if (!empty ($this->checked->dirchange)) {
			//Daten rausholen
			$sql = sprintf("SELECT * FROM %s WHERE %s='%s'",
				$this->cms->tbname[$pfad],
				$catid,
				$this->db->escape($this->checked->dirchange)
			);

			$result = $this->db->get_results($sql, ARRAY_N);

			$this->content->template['catname'] = "nodecode:".$this->diverse->encode_quote($result['0']['1']);
			$this->content->template['catid'] = $result['0']['0'];
			$this->content->template['cat_sub'] = $result['0']['3'];

			//Daten aus Lookup
			$sql = sprintf("SELECT %s FROM %s WHERE %s='%s'",
				$cattgruppe,
				$this->cms->tbname[$lookup_name],
				$catid_lookup,
				$this->db->escape($this->checked->dirchange)
			);
			$this->result_gr = $this->db->get_results($sql, ARRAY_N);

			//edit
			$this->content->template['edit'] = "edit";
		}
		else {
			if (!empty ($this->checked->dir)) {
				if (empty ($this->checked->loeschen)) {
					//Level der ausgewählten KAtegorie rausholen
					$sql = sprintf("SELECT %s FROM %s WHERE %s='%s'",
						$level,
						$this->cms->tbname[$pfad],
						$catid,
						$this->db->escape($this->checked->image_sub_cat_von)
					);
					$result_level = $this->db->get_var($sql);

					if (!empty($result_level)) {
						$result_level=$result_level+1;
					}
					else {
						if (!empty($this->checked->image_sub_cat_von)) {
							$result_level=$result_level+1;
						}
					}


					//Wenn neu anlegen, eingeben
					$sql = sprintf("INSERT INTO %s SET %s='%s', 
									%s='%s',
									%s='%s'",
						$this->cms->tbname[$pfad],
						$name,
						$this->db->escape($this->checked->dir),
						$sub_cat_von,
						$this->db->escape($this->checked->image_sub_cat_von),
						$level,
						$result_level
					);
					$this->db->query($sql);

					$insertid = $this->db->insert_id;

					if (!empty ($this->checked->inhalt_ar)) {
						foreach ($this->checked->inhalt_ar['gruppe_write'] as $insert) {
							$insert=$this->db->escape($insert);

							$sqlin = "INSERT INTO " . $this->cms->tbname[$lookup_name] .
								" SET $catid_lookup='$insertid', gruppeid_id='$insert' ";
							$this->db->query($sqlin);
						}
					}
					$sqlin = "INSERT INTO " . $this->cms->tbname[$lookup_name] .
						" SET $catid_lookup='$insertid', gruppeid_id='1' ";
					$this->db->query($sqlin);

					// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
					IfNotSetNull($menuidid);

					$location_url = "./ordner.php?menuid=" . $this->checked->menuid . "&messageget=" . $menuidid;

					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
			}
			//Wenn geändert, eingeben
		}
		//Soll gelöscht werden
		if (!empty ($this->checked->submitdelecht)) {
			if ($this->checked->catid >= 1) {
				//Eintrag nach id löschen und neu laden
				$sql = sprintf("DELETE FROM %s WHERE %s='%s'",
					$this->cms->tbname[$pfad],
					$catid,
					$this->db->escape($this->checked->catid)
				);
				$this->db->query($sql);

				$insertid = $this->db->escape($this->checked->catid);

				$sql = sprintf("UPDATE %s 
								SET 
								%s='0',
								%s='0'
								WHERE %s='%s'",
					$this->cms->tbname[$pfad],
					$sub_cat_von,
					$level,
					$sub_cat_von,
					$insertid
				);
				$this->db->query($sql);

				$insertid = $this->db->escape($this->checked->catid);

				//Alle Sprachen linkliste_descrip
				$sql = sprintf("DELETE FROM %s WHERE %s='%s'",
					$this->cms->tbname[$lookup_name],
					$catid_lookup,
					$insertid
				);
				$this->db->query($sql);

				if ($pfad == "papoo_kategorie_bilder") {
					//Bei den Bildern löschen
					$sql = sprintf("UPDATE %s SET image_dir='0' 
									WHERE image_dir='%s'",
						$this->cms->tbname['papoo_images'],
						$insertid
					);
					$this->db->query($sql);
				}

				if ($pfad == "papoo_kategorie_dateien") {
					//Bei den Dateien löschen
					$sql = sprintf("UPDATE %s SET downloadkategorie='0' 
									WHERE downloadkategorie='%s'",
						$this->cms->tbname['papoo_download'],
						$insertid
					);
					$this->db->query($sql);
				}
			}
			//Jetzt nochmal einen Reset durchführen der Ebenen
			$sql = sprintf("	SELECT * FROM %s ORDER BY %s,%s ASC",
				$this->cms->tbname[$pfad],
				$sub_cat_von,
				$level
			);
			$results = $this->db->get_results($sql,ARRAY_A);
			$max=20;
			if (is_array($results)) {
				for ($i=0;$i<$max;$i++) {
					foreach ($results as $key=>$value) {
						foreach ($results as $key1=>$value1) {
							if ($value1[$sub_cat_von]==$value[$catid]) {
								$neu[$value1[$catid]]=$value1;
								$neu[$value1[$catid]][$level]=$value[$level]+1;
							}
						}
					}
				}
			}
			IfNotSetNull($neu);
			if (is_array($neu)) {
				foreach ($neu as $key=>$value) {
					if ($value[$sub_cat_von]>0 ) {
						$level_nr=$value[$level];
						$sql=sprintf("	UPDATE %s SET 
										%s='%s' 
										WHERE %s='%s'",
							$this->cms->tbname[$pfad],
							$level,
							$level_nr,
							$catid,
							$key
						);
						$this->db->query($sql);
					}
				}
			}
			$sql=sprintf("	UPDATE %s SET 
										%s='0' 
										WHERE %s='0'",
				$this->cms->tbname[$pfad],
				$level,
				$sub_cat_von
			);
			$this->db->query($sql);

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=del";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
		//Soll wirklich gelöscht werden?
		if (!empty ($this->checked->loeschen)) {
			// Wird der Name geändert und Löschen ausgeführt, erscheint der falsche Name in der Abfrage,
			// ob wirklich gelöscht werden soll. Dies täuscht das Löschen der angegebenen Kategorie vor.
			//$this->content->template['dir'] = $this->checked->dir;
			IfNotSetNull($this->checked->catname_save);
			IfNotSetNull($this->checked->catid);
			$this->content->template['dir'] = $this->checked->catname_save;
			$this->content->template['catid'] = $this->checked->catid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
		//Gruppenliste
		$this->hol_gruppen_liste();

		//Liste der Kategorien rausholen und anzeigen
		$sql = sprintf("SELECT * FROM %s %s",
			$this->cms->tbname[$pfad],
			$order_by
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (is_array($result)) {
			$ik=0;
			foreach ($result as $key=>$value) {
				$result[$ik]['cat_id']=$value[$catid];
				$result[$ik]['sub_cat_von']=$value[$sub_cat_von];
				$result[$ik]['sub_cat_level']=$value[$level];

				$result[$ik]['dateien_cat_name'] = !isset($result[$ik]['dateien_cat_name']) ? "": $result[$ik]['dateien_cat_name'];
				$result[$ik]['video_cat_name'] = !isset($result[$ik]['video_cat_name']) ? "": $result[$ik]['video_cat_name'];
				$result[$ik]['bilder_cat_name'] = !isset($result[$ik]['bilder_cat_name']) ? "": $result[$ik]['bilder_cat_name'];
				$ik++;
			}
		}
		#$this->checked->xcat_id=$this->checked->dirchange;
		//Nested Klasse einbinden
		require_once("make_nested_class.php");
		$nested=new make_nested();
		//Nested Array erzeugen
		IfNotSetNull($this->checked->dirchange);
		$result = $nested->create_nested_array($result,"NO",$this->checked->dirchange);
		$result = is_array($result) ? $result : array();
		//HTML Liste erzeugen
		$result=$nested->create_nested_list_from_array($result,"ul");

		$this->content->template['dirlist'] = $result;

		$this->content->template['xlink'] = "ordner.php?menuid=" . $this->checked->menuid;
	}
}

$intern_ordner = new intern_ordner_class();