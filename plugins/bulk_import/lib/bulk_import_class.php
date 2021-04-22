<?php

/**
 * Class bulk_import
 */
class bulk_import
{
	/**
	 * bulk_import constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms, $db_abs, $diverse;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->db_abs = &$db_abs;
		$this->diverse = & $diverse;

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html") {
				if ($template2=="import_back.html") {
					//Daten sicher und anzeigen
					$this->import_now();
				}
			}
		}
	}

	/**
	 * bulk_import::import_now()
	 *
	 * @return void
	 */
	private function import_now()
	{
		if (!is_dir(PAPOO_ABS_PFAD."/dokumente/import/")) {
			@mkdir(PAPOO_ABS_PFAD."/dokumente/import/");
		}

		if (!empty($this->checked->submit_import_files)) {
			//Verzeichnis auslesen
			$data=$this->diverse->getDirectoryTree(PAPOO_ABS_PFAD."/dokumente/import/");
			$data_plain=$this->diverse->directoryToArray(PAPOO_ABS_PFAD."/dokumente/import/");
			if (is_array($data_plain)) {
				foreach ($data_plain as $key=>$value) {
					$basename=basename($value);
					$plain[$basename]=$value;
				}
			}
			//Inserts und Kopieren
			$this->create_inserts($data,$plain);

			//Verzeichnis l�schen
			$this->delete_verzeichnisse(PAPOO_ABS_PFAD."/dokumente/import/");

			//Import anzeigen
			$this->content->template['bulk_import_ok']="OK";
		}
	}

	/**
	 * bulk_import::delete_verzeichnisse()
	 *
	 * @param $dir
	 * @return void
	 */
	private function delete_verzeichnisse($dir)
	{
		$this->diverse->rec_rmdir($dir);

		if (!is_dir(PAPOO_ABS_PFAD."/dokumente/import/")) {
			@mkdir(PAPOO_ABS_PFAD."/dokumente/import/");
		}

	}

	/**
	 * bulk_import::create_inserts()
	 *
	 * @param mixed $data
	 * @param mixed $data_plain
	 * @param integer $kat_id
	 * @param integer $level
	 * @return void
	 */
	private function create_inserts($data,$data_plain,$kat_id=0,$level=0)
	{
		if (is_array($data)) {
			foreach ($data as $key=>$value)
			{
				$insertid_cat=$kat_id;

				//Wenn es ein Array ist
				if(is_array($value)) {
					$key_neu=strtolower($key);
					$key_neu=str_replace("ü","ue",$key);
					$key_neu=str_replace("ä","ae",$key_neu);
					$key_neu=str_replace("ö","oe",$key_neu);
					$key_neu=str_replace(" ","_",$key_neu);

					//Kategorie anlegen
					$sql=sprintf("INSERT INTO %s
									SET 
									dateien_cat_name='%s',
									dateien_sub_cat_von='%s',
									dateien_sub_cat_level='%s'
									",
						$this->cms->tbname['papoo_kategorie_dateien'],
						$this->db->escape($key_neu),
						$kat_id,
						$level
					);
					$result=$this->db->query($sql);
					$insertid_cat=$this->db->insert_id;

					//Lookup Kategorien anlegen
					$sql=sprintf("INSERT INTO %s
							SET 
							dateien_cat_id_id='%s',
							gruppeid_id='10'
							",
						$this->cms->tbname['papoo_lookup_cat_dateien'],
						$insertid_cat
					);
					$result=$this->db->query($sql);
					//Neu aufrufen
					$this->create_inserts($value,$data_plain,$insertid_cat,$level+1);
				}
				//Kein Array dann ist es eine Datei
				else {
					if (!empty($value)) {
						//Datei kopieren
						//$value_neu=strtolower($value);
						$value_neu=str_replace("ü","ue",$value);
						$value_neu=str_replace("ä","ae",$value_neu);
						$value_neu=str_replace("ö","oe",$value_neu);
						$value_neu=str_replace("ü","ue",$value_neu);
						$value_neu=str_replace("ä","ae",$value_neu);
						$value_neu=str_replace("ö","oe",$value_neu);
						$value_neu=str_replace("ß","ss",$value_neu);
						$value_neu=str_replace(" ","_",$value_neu);
						$value_neu = preg_replace("/[^a-zA-Z0-9_\.]/", "", $value_neu);
						$pfad=$data_plain[$value];

						if (!empty($pfad) && $pfad!="Thumbs.db") {
							copy($pfad,PAPOO_ABS_PFAD."/dokumente/upload/".$value_neu);

							//Gr��e
							$filesize=@filesize(PAPOO_ABS_PFAD."/dokumente/upload/".$value_neu);

							//Dann Dateien anlegen
							$sql=sprintf("INSERT INTO %s
									SET 
									downloadlink='%s',
									downloadgroesse='%s',
									downloaduserid 	='%s',
									downloadkategorie='%d'
									",
								$this->cms->tbname['papoo_download'],
								"/dokumente/upload/".$this->db->escape($value_neu),
								$filesize,
								$this->user->userid,
								$insertid_cat
							);
							$result=$this->db->query($sql);

							$insertid_file=$this->db->insert_id;

							//Sprachdaten anlegen
							$sql=sprintf("INSERT INTO %s
									SET 
									download_id='%s',
									lang_id='%s',
									downloadname 	='%s'
									",
								$this->cms->tbname['papoo_language_download'],
								$insertid_file,
								$this->cms->lang_back_content_id,
								$this->db->escape($value_neu)
							);
							$result=$this->db->query($sql);

							//Lookup Datei Rechte
							$sql=sprintf("INSERT INTO %s
									SET 
									download_id_id='%s',
									gruppen_id_id='10'
									",
								$this->cms->tbname['papoo_lookup_download'],
								$insertid_file
							);
							$result=$this->db->query($sql);
						}
					}
				}
			}
		}
	}
}

$bulk_import = new bulk_import();
