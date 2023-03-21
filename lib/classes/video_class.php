<?php
/**
 * ######################################
 * # Papoo Software                     #
 * # (c) Dr. Carsten Euwens 2008        #
 * # Author: Carsten Euwens             #
 * # http://www.papoo.de                #
 * ######################################
 * # PHP Version >= 4.3                 #
 * ######################################
 */

/**
 * Hier werden Videos bearbeitet.
 */
#[AllowDynamicProperties]
class video_class
{
	/** @var ezSQL_mysqli */
	public $db;
	/** @var checked_class */
	public $checked;
	/** @var content_class */
	public $content;
	/** @var cms */
	public $cms;
	/** @var user_class */
	public $user;
	/** @var diverse_class */
	public $diverse;
	/** @var string */
	public $db_praefix;
	/** @var int */
	public $nur_flv, $nur_mp3;
	/** @var array */
	public $temp_styles;

	/**
	 * video_class constructor.
	 */
	function __construct()
	{

	}

	/**
	 *
	 */
	function do_video()
	{
		global $db, $checked, $content, $cms, $user, $diverse, $db_praefix;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->cms = &$cms;
		$this->user = &$user;
		$this->diverse = &$diverse;
		$this->db_praefix = &$db_praefix;

		if (defined("admin")) {
			// überprüfen ob Zugriff auf die Inhalte besteht
			$this->user->check_access();
		}

		switch ($this->checked->menuid) {
		case "70" :
			$this->do_start();
			break;

		case "88" :
			$this->do_bind();
			break;

		case "71" :
			$this->do_change();
			break;
		}

		$ifNotSetNullTemplateVars = ['bindvid', 'changevid', 'newvid', 'startvid', 'video_error', 'drin', 'video_id',];
		foreach ($ifNotSetNullTemplateVars as $var) {
			IfNotSetNull($this->content->template[$var]);
		}
	}

	/**
	 * Startseite Video anzeigen
	 */
	function do_start()
	{
		$this->content->template['startvid'] = "ok";
	}

	/**
	 * Video EIntrag löschen
	 */
	function del_video()
	{
		if (is_numeric($this->checked->exist)) {
			$sql = sprintf(
				"DELETE FROM %s WHERE video_id='%s' LIMIT 1",
				$this->cms->tbname['papoo_video'],
				$this->db->escape($this->checked->exist)
			);
			$this->db->query($sql);

			$sql = sprintf(
				"DELETE FROM %s WHERE lan_video_id='%s'",
				$this->cms->tbname['papoo_language_video'],
				$this->db->escape($this->checked->exist)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param string $modus
	 */
	function bindOrUpload($modus = 'bind')
	{
		if (!in_array($modus, ['bind', 'change'])) {
			throw new UnexpectedValueException(sprintf('Der Modus "%1$s" existiert nicht!', $modus));
		}

		//Upload durchführen
		$this->make_upload();

		if ($modus === 'change' || $modus === 'bind') {
			IfNotSetNull($this->checked->video_id);
		}

		if (!empty($this->checked->drin)) {
			$this->content->template['drin'] = "ok";
		}
		if (!empty($this->checked->loeschen) && is_numeric($this->checked->video_id)) {
			$this->del_video();
			$location_url = "./video.php?menuid=71";

			$this->diverse->http_redirect($location_url, 303);
		}

		if (!empty($this->checked->file) or !empty($this->checked->video_id)) {
			$this->content->template['newvid'] = "ok";
			// Sprachen setzen
			$this->make_div_lang($this->checked->video_id);
			// Kategorien holen
			$this->get_cat_list();
			// Datei anzeigen {$dateiname_web}#
			IfNotSetNull($this->checked->file);
			$filename = basename($this->checked->file);

			$this->content->template['dateiname_web'] = PAPOO_WEB_PFAD . "/video/" . $filename;
			$this->content->template['dateiname'] = $filename;

			// Alte daten rausholen
			if (is_numeric($this->checked->video_id)) {
				$sql = sprintf(
					"SELECT * FROM %s WHERE video_id='%s'",
					$this->cms->tbname['papoo_video'],
					$this->db->escape($this->checked->video_id)
				);
				$result = $this->db->get_results($sql, ARRAY_A);

				$this->content->template['dateiname_web'] =
					PAPOO_WEB_PFAD . "/video/" . $result['0']['video_file_name'];
				$this->content->template['dateiname'] = $result['0']['video_file_name'];
				$this->content->template['video_kat'] = $result['0']['video_kat'];
				$this->content->template['video_id'] = $result['0']['video_id'];
			}

			// Eintragen
			if (!empty($this->checked->eintrag)) {
				$ok = '';
				$filename = basename($this->checked->filename);
				//Checken, ob Einträge vorhanden sind
				foreach ($this->checked->texte as $key => $value) {
					if (!empty($value['alt'])) {
						$ok = "ok";
					}
				}
				// Existierende löschen
				if (!empty($this->checked->exist)) {
					$this->del_video();
				}
				if (is_numeric($this->checked->exist) or empty($this->checked->exist)) {
					if ($ok == "ok") {
						// File eintragen
						$sql = sprintf(
							'INSERT INTO %s SET video_file_name = "%s", video_kat = "%s"',
							$this->cms->tbname['papoo_video'],
							$this->db->escape($filename),
							$this->db->escape($this->checked->image_dir)
						);
						$this->db->query($sql);
						$insertid = $this->db->insert_id;

						// Sprachen eintragen
						foreach ($this->checked->texte as $key => $value) {
							$sql = sprintf(
								'INSERT INTO %s 
								SET video_alt = "%s",
									video_longdesc = "%s",
									video_lang_id = "%s",
									lan_video_id = "%s"',
								$this->cms->tbname['papoo_language_video'],
								$this->db->escape($value['alt']),
								$this->db->escape($value['longdesc']),
								$this->db->escape($key),
								$this->db->escape($insertid)
							);
							$this->db->query($sql);
						}
						$location_url = "./video.php?menuid=71&drin=ok";

						$this->diverse->http_redirect($location_url, 303);
					}
				}
			}
		}
		else {
			$this->content->template[($modus === 'bind' ? 'bind' : 'change') . 'vid'] = "ok";
			$not_style = $this->get_videos_not_in_database();
			$this->content->template['file_list'] = $not_style;

			//Kategorien anzeigen zur Auswahl
			$this->get_cat_list();
			//Liste der Videos rausholen
			$this->content->template['file_list_drin'] = $this->get_video_list($this->checked->cat);
		}
	}

	/**
	 * Videos einbinden und anzeigen
	 */
	function do_bind()
	{
		$this->bindOrUpload('bind');
	}

	/**
	 * Videos einbinden und anzeigen
	 */
	function do_change()
	{
		$this->bindOrUpload('change');
	}

	/**
	 * Liste der vorhandenen Videos
	 *
	 * @param string $cat spezifische Kategorie
	 *
	 * @return array
	 */
	function get_video_list($cat = "0")
	{
		IfNotSetNull($this->nur_flv);
		IfNotSetNull($this->nur_mp3);

		$cat = $cat ?? "0";

		$flv = "";
		if ($this->nur_flv) {
			$flv = " AND video_file_name LIKE '%flv%'";
		}
		if ($this->nur_mp3) {
			$flv = " AND video_file_name LIKE '%mp3%'";
		}

		$sql = sprintf(
			'SELECT * 
			FROM %1$s AS v
				INNER JOIN %2$s AS lv ON lv.lan_video_id = v.video_id
			WHERE video_lang_id = %3$d
				AND video_kat LIKE "%4$s" 
				%5$s
			%6$s;',
			$this->cms->tbname['papoo_video'],
			$this->cms->tbname['papoo_language_video'],
			$this->cms->lang_id,
			$this->db->escape($cat == 'all' ? "%" : $cat),
			$flv,
			$cat == 'all' ? "ORDER BY video_kat ASC" : ''
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Styles raussuchen, die nicht in der Datenbank sind, aber im Verzeichnis
	 *
	 * @return array
	 */
	function get_videos_not_in_database()
	{
		$pfad = $this->cms->pfadhier . "/video/";
		$handle = opendir($pfad);
		while (($file = readdir($handle)) !== false) {
			if (is_file($pfad . $file)) {
				if (strpos("XXX" . $file, ".") != 3) {
					$vfiles[] = $file;
				}
			} // Nur nicht-unsichtbare Verzeichnisse aufnehmen
		}

		$sql = sprintf("SELECT video_file_name FROM %s ", $this->cms->tbname['papoo_video']);
		$styls = $this->db->get_col($sql);
		if (empty($styls)) {
			$styls = [];
		}

		//Anzeigen wenn nicht
		$this->temp_styles = $not_style = [];
		//dafür die Styles aus der Datenbank durchgehen
		if (!empty($vfiles)) {
			foreach ($vfiles as $verz) {
				//Verzeichnisse checken
				if (in_array($verz, $styls)) {
					$this->temp_styles[] = $verz;
				}
				else {
					$not_style[] = $verz;
				}
			}
		}

		$filtered_extensions = ["php", "js", "html", "htm", "txt", ""];
		$not_style = array_filter($not_style, function($file_name) use ($filtered_extensions) {
			return in_array(pathinfo($file_name, PATHINFO_EXTENSION), $filtered_extensions) == false;
		});

		return $not_style;
	}

	/**
	 * Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 *
	 * @param int $image_id
	 *
	 * @return void
	 */
	function make_div_lang($image_id = 0)
	{
		$texte = [];
		// Wenn Daten aus Formular vorliegen, dann diese durchreichen (nach Größen-Änderung oder Fehler)

		// aktive Sprachen raussuchen
		$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2'", $this->cms->papoo_name_language);
		$aktive_sprachen = $this->db->get_results($sql);

		if (!empty($aktive_sprachen)) {
			foreach ($aktive_sprachen as $sprache) {
				$sql = sprintf(
					"SELECT video_alt, video_title, video_longdesc
				FROM %s WHERE video_lang_id='%d' AND lan_video_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_language_video'],
					$sprache->lang_id,
					$this->db->escape($image_id)
				);
				$bild_texte = $this->db->get_results($sql);

				if (!empty($bild_texte)) {
					$temp_texte = [
						"lang_id" => $sprache->lang_id,
						"sprache" => $sprache->lang_long,
						"alt" => "nodecode:" . $this->diverse->encode_quote($bild_texte[0]->video_alt),
						"title" => "nodecode:" . $this->diverse->encode_quote($bild_texte[0]->video_title),
						"longdesc" => "nodecode:" . $bild_texte[0]->video_longdesc,
					];
				}
				else {
					$temp_texte = [
						"lang_id" => $sprache->lang_id,
						"sprache" => $sprache->lang_long,
						"alt" => "",
						"title" => "",
						"longdesc" => "",
					];
				}
				$texte[$sprache->lang_id] = $temp_texte;
			}
		}
		$this->content->template['menlang'] = $texte;
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @param string $sel
	 *
	 * @return array
	 */
	function get_cat_list($sel = "")
	{
		$intern_ordner = new intern_ordner_class();
		$intern_ordner->show_ordner_bilder("papoo_kategorie_video");

		//Aktueller Level
		IfNotSetNull($this->checked->cat);
		$aktu_sub_id = $neu = $level = null;
		if (is_array($this->content->template['dirlist'])) {
			foreach ($this->content->template['dirlist'] as $key => $value) {
				if ($this->checked->cat == $value['cat_id']) {
					$level = $value['sub_cat_level'] + 1;
					$aktu_sub_id = $value['sub_cat_von'];
				}
			}
		}
		$this->content->template['video_cat_id'] = $aktu_sub_id;
		$this->content->template['video_active_cat_id'] = $this->checked->cat;

		$sql = sprintf(
			"	SELECT video_kat,COUNT(video_id) AS count FROM %s
							LEFT JOIN %s ON video_kat=video_cat_id
							 GROUP BY video_cat_id",
			$this->cms->tbname['papoo_video'],
			$this->cms->tbname['papoo_kategorie_video']
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key => $value) {
				if (empty($value['video_kat'])) {
					$value['video_kat'] = 0;
				}
				$neu[$value['video_kat']] = $value['count'];
			}
		}
		$this->content->template['video_cat_id_count'] = $neu;
		IfNotSetNull($this->content->template['video_cat_id_count'][0]);

		$result2 = [];
		if ($sel == "all") {
			$sql = sprintf(
				"SELECT DISTINCT video_cat_id, video_cat_name FROM %s",
				$this->cms->tbname['papoo_kategorie_video']
			);
			$result2['0']['video_cat_id'] = "0";
			$result2['0']['video_cat_name'] = "";
		}
		else {
			$sql = sprintf(
				"SELECT DISTINCT video_cat_id, video_cat_name 
				FROM %s, %s, %s 
				WHERE userid='%d' 
					AND gruppenid=gruppeid_id 
					AND video_cat_id_id=video_cat_id
					AND video_sub_cat_level ='%d' 
					AND video_sub_cat_von ='%d'
				ORDER BY video_cat_name",
				$this->cms->tbname['papoo_kategorie_video'],
				$this->cms->tbname['papoo_lookup_cat_video'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->user->userid,
				$level,
				$this->db->escape($this->checked->cat)
			);
		}
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['result_cat'] = $result;
		return $result;
	}

	/**
	 *
	 */
	function make_upload()
	{
		if (!empty ($this->checked->formSubmit)) {
			// Zielverzeichniss
			$destination_dir = '/video';
			// nicht erlaubte Dateiendungen
			$extensions = [
				'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phps', 'php-s', 'pht', 'phar', 'cgi', 'pl',
			];
			// Upload durchführen
			$upload_do = new file_upload(PAPOO_ABS_PFAD);
			// Wenn Files hochgeladen wurden
			if (count($_FILES) > 0) {
				// Durchführen und falls etwas schief geht
				$falsch = !$upload_do->upload($_FILES['myfile'], $destination_dir, 0, $extensions, 1);
			}

			// wenn der Upload geklappt hat, Daten übergeben.
			if (!$falsch) {
				$location_url = "./video.php?menuid=71&file=" . $upload_do->file['name'];
				$this->diverse->http_redirect($location_url, 303);
			}
			// Fehler beim Hochladen..
			else {
				$this->content->template['video_error'] =
					$this->content->template['message_20'] . "<br />$upload_do->error";
			}
		}
	}
}

$video_class = new video_class();
