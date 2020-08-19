<?php
/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >=4.3                 #
#####################################
 */

/**
 * Class intern_image_class
 */
class intern_image_class
{
	/**
	 * intern_image_class constructor.
	 */
	function __construct()
	{
		//Klassen globalisieren

		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		global $db_praefix;
		$this->db_praefix = DB_PRAEFIX;
		// Messages einbinden
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// inhalt Klasse einbinden
		global $content;
		$this->content = & $content;
		// checkedblen Klasse einbinde
		global $checked;
		$this->checked = & $checked;
		// Diverse-Klasse einbinde
		global $diverse;
		$this->diverse = & $diverse;
		// Image_Core-Klasse einbinden
		global $image_core;
		$this->image_core = $image_core;

		IfNotSetNull($this->content->template['catname']);
		IfNotSetNull($this->content->template['error1']);
		IfNotSetNull($this->content->template['import_export']);
		IfNotSetNull($this->content->template['multiupload']);
		IfNotSetNull($this->content->template['bilder_cat_name']);
		IfNotSetNull($this->content->template['checked1']);
		IfNotSetNull($this->content->template['image_dir']);
	}

	/**
	 * Loescht alle Bilder die in der Liste unter "Bilder bearbeiten" ausgewaehlt wurden
	 */
	function erase_all_selected_images()
	{
		// FIXME: img_box wird nie gesetzt
		foreach($this->checked->img_box as $img) {
			$sql = "SELECT `image_name` FROM " . $this->cms->tbname['papoo_images'] . " WHERE image_id=$img";
			$result = $this->db->get_var($sql);
			unlink(PAPOO_ABS_PFAD . "/images/" . $result);
			unlink(PAPOO_ABS_PFAD . "/images/thumbs/" . $result);
			$this->db->query("DELETE FROM " . $this->cms->tbname['papoo_images'] . " WHERE image_id=$img");
			$this->db->query("DELETE FROM " . $this->cms->tbname['papoo_language_image'] . " WHERE lan_image_id=$img");
		}
		$this->content->template['text_success'] = true;
	}

	/**
	 * @deprecated Wird nicht verwendet?
	 *
	 * @throws Exception
	 */
	private function del_all_not_db_images()
	{
		$data = $this->diverse->lese_dir("/images");

		if (is_array($data)) {
			foreach ($data as $k=>$v) {
				if (is_dir(PAPOO_ABS_PFAD."/images/".$v['name'])) {
					continue;
				}

				$sql=sprintf("SELECT * FROM %s WHERE image_name='%s' ",
					DB_PRAEFIX."papoo_images",
					$v['name']);
				$result = $this->db->get_results($sql);

				if (empty($result['0']) && $v['name']!="_spamcode_image.php" && $v['name']!="leer.txt" ) {
					@unlink(PAPOO_ABS_PFAD . "/images/" . $v['name']);
					@unlink(PAPOO_ABS_PFAD . "/images/thumbs/" . $v['name']);
				}
			}
		}
	}

	/**
	 * Aktionsweiche für Bilder im Backend
	 */
	function make_inhalt()
	{
		IfNotSetNull($this->checked->action);
		// Überprüfen ob Zugriff auf die Inhalte besteht
		$this->user->check_access();

		switch ($this->checked->menuid) {
		case "19":
			// Starttext Übergeben
			if (empty ($this->checked->messageget)) {
				$this->content->template['text'] = $this->content->template['message_35'];
			}
			else {
				//$this->content->template['text'] = $this->content->template['message_35'].$this->content->template['message_'.$this->checked->messageget.''];
				$this->content->template['text'] = $this->content->template['message_'.$this->checked->messageget.''];
			}
			$this->do_pref();
			break;

		case "20":
			// Bilder hochladen
			$this->upload_switch($this->checked->action);
			break;

		case "21":
			// Bilder aendern
			if (isset($this->checked->multi_image_delete_confirmed) && $this->checked->multi_image_delete_confirmed) {
				$this->erase_all_selected_images();
			}

			// QUICKFIX: Wenn Bilder über die Flex hochgeladen werden,
			// wird u.U. zu viel gelöscht. Daher erstmal nichts löschen!

			// alle bilder entfernen die nicht in der DB sind
			// $this->del_all_not_db_images();

			$this->change_switch($this->checked->action);
			break;
		}
		$this->content->template['timestamp']=time();

		if (function_exists("Imagick")) {
			$this->content->template['imagick']="_imagick";
		}
		else {
			$this->content->template['imagick']="";
		}
	}

	/**
	 * Einstellungen eingeben für Export/Import
	 */
	function do_pref()
	{
		$this->content->template['start'] = "ok";
		//Daten eintragen
		if (!empty($this->checked->submitglossar)) {
			$sql=sprintf("UPDATE %s SET image_export='%s', image_import='%s'",
				$this->cms->tbname['papoo_pref_images'],
				$this->db->escape($this->checked->image_export),
				$this->db->escape($this->checked->image_import)
			);
			$this->db->query($sql);
		}
		//Daten ausgeben xml_result
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_pref_images']
		);
		$this->content->template['xml_result'] =$result= $this->db->get_results($sql,ARRAY_A);
	}

	/**
	 * Löscht die temporär erzeugten Bilder in /interna/templates_c/upload/
	 */
	private function erase_tmp_images()
	{
		$images=$this->diverse->directoryToArray(PAPOO_ABS_PFAD."/interna/templates_c/upload/");
		foreach($images as $k=>$v) {
			unlink($v);
		}
	}

	/**
	 * Unter-Aktionsweiche für Bilder hochladen
	 *
	 * @param $action
	 * @param string $noreload
	 */
	function upload_switch($action, $noreload="")
	{
		switch ($action) {
		case "SPEICHERN":
			$this->upload_save($noreload);
			break;

		case "MULTI":
			if (empty($this->checked->image_dir) || is_numeric($this->checked->image_dir)) {
				$this->multi_upload();
			}
			else {
				$this->content->template['template_weiche'] = 'HOCHLADEN';
				$this->get_cat_list();
			}
			break;

		case "MULTI_UPLOAD_ABORT":
			$this->erase_tmp_images();
			$this->content->template['text_error'] = true;
			// Formular zum Upload einer Bild-Datei anbieten
			$this->content->template['template_weiche'] = 'HOCHLADEN';
			$this->get_cat_list();
			break;

		case "MULTI_UPLOAD":
			$this->make_multi_upload_now();
			break;

		case "EDIT":
			// es wurde keine Bild ausgewählt
			if (empty($_FILES['strFile']['name'])) {
				// Fehlermeldung ausgeben
				$this->content->template['text_error'] = $this->content->template['message_38'];
				// Formular zum Upload einer Bild-Datei anbieten
				$this->content->template['template_weiche'] = 'HOCHLADEN';
				$this->get_cat_list();
			}
			// Bild wurde hochgeladen
			else {
				$this->content->template['template_weiche'] = 'NEU_EDIT';
				$this->content->template['image_modus'] = 'NEU';
				$this->upload_picture();
			}
			break;

		default:
			// Formular zum Upload einer Bild-Datei anbieten
			$this->content->template['template_weiche'] = 'HOCHLADEN';
			$this->get_cat_list();
			break;
		}
	}

	/**
	 *
	 */
	private function make_multi_upload_now()
	{
		//Bilder im Pfad auslesen
		$images=$this->diverse->directoryToArray(PAPOO_ABS_PFAD."/interna/templates_c/upload/");
		$images_path=$images;
		//debug::print_d($images);

		if (is_array($images)) {
			foreach ($images as $k=>$v) {
				$images[$k]=str_ireplace(PAPOO_ABS_PFAD,"..",$v);
			}
		}

		//Ans Template geben für die Eingabe der alt / title Daten
		$this->content->template['image_liste']=$images;

		//Template passend setzen
		$this->content->template['multiupload']="ok";
		$this->content->template['create_image_data']="ok";
		$this->content->template['image_dir']=$this->checked->image_dir;
		require_once PAPOO_ABS_PFAD.'/lib/classes/class_simple_image.php';
		$simple_image = new SimpleImage();

		if (!empty($this->checked->strSubmit_multiimages_data)) {
			//Die Bilder aus dem Upload Verzeichnis kopieren
			if (is_array($images_path)) {
				foreach ($images_path as $k=>$v) {
					$v_temp = preg_replace( "/ /", "-", $v );
					$v_dest = preg_replace("/[^a-zA-Z0-9-\.]/", "", strtolower(basename($v_temp)));
					$v_dest = $this->user->userid."-".$v_dest;

					if (file_exists(PAPOO_ABS_PFAD."/images/".$v_dest)) {
						$v_dest=str_ireplace($this->user->userid."-",$this->user->userid."-".rand(0,time())."-",$v_dest);
					}


					$imageFile = $v_dest;
					if (strtolower(pathinfo($imageFile, PATHINFO_EXTENSION)) === "svg") {
						$imagePath = rtrim(realpath(PAPOO_ABS_PFAD."/images"), "/")."/";

						$imageDestination = $imagePath.$imageFile;
						$thumbnailDestination = "{$imagePath}thumbs/$imageFile";

						copy($v, $imageDestination);
						copy($v, $thumbnailDestination);

						list($width, $height) = image_core_class::determineDimensionsFromSVG($imageDestination);
					}
					else {
						//Kopieren
						copy($v,PAPOO_ABS_PFAD."/images/".$v_dest);

						//Bild für die Image Klasse laden
						$simple_image->load($v);

						//Ziel Verzeichnis /thumbs
						$dest=PAPOO_ABS_PFAD."/images/thumbs/".$v_dest;
						//Bild resizen und direkt speichern
						$simple_image->image_resize($v,$dest,$this->image_core->tumbnail_max_groesse['breite'],$this->image_core->tumbnail_max_groesse['hoehe']);

						$width = $simple_image->image_info['0'];
						$height = $simple_image->image_info['1'];
					}

					if (empty($this->checked->image_dir)) {
						$this->checked->image_dir=0;
					}

					//Löschen
					unlink($v);
					//In der Datenbank speichern
					$sql = sprintf("INSERT INTO %s SET image_name='%s', image_width='%d', image_height='%s', image_dir='%s' ",
						$this->cms->papoo_images,
						$this->db->escape($v_dest),
						$this->db->escape($width),
						$this->db->escape($height),
						$this->db->escape($this->checked->image_dir)
					);
					$this->db->query($sql);

					// Imageid Übergeben
					$this->image_id_extern = $this->db->insert_id;

					$this->checked->texte['1']['alt']=$this->checked->alt[$k];
					$this->checked->texte['1']['title']= $this->checked->alt[$k];#$this->checked->title[$k];
					$this->checked->texte['1']['longdesc']=$this->checked->alt[$k];#$this->checked->title[$k];

					$this->checked->texte['1']['lang_id']=1;

					// alt, title, longdesc eingeben in die Datenbank
					$this->insert_lang($this->db->insert_id, $this->user->userid."_".basename($v));
				}
			}
			$this->content->template['multiupload_fin']="ok";
		}
	}

	/**
	 *
	 */
	private function multi_upload()
	{
		$this->content->template['multiupload']="ok";
		$this->content->template['image_dir']=$this->checked->image_dir;
	}

	/**
	 * Das hochladen der Bilder durchführen.
	 */
	function upload_picture()
	{
		$this->image_core->image_load($_FILES['strFile']);
		$image_infos = $this->image_core->image_infos;

		IfNotSetNull($image_infos['name']);
		IfNotSetNull($image_infos['type']);
		IfNotSetNull($image_infos['breite']);
		IfNotSetNull($image_infos['hoehe']);

		$imageOriginalName = $image_infos['name'];

		$this->get_cat_list();

		$max_gr = $this->cms->system_config_data['config_maximale_bildgrel'];
		$max_gr_array = explode("x",$max_gr);

		if ($max_gr_array['0']<250) {
			$max_gr_array['0']=250;
		}
		if ($max_gr_array['1']<250) {
			$max_gr_array['1']=250;
		}

		// Test: Bild zu groß
		if ($image_infos["type"] !== "SVG" && (
				$image_infos['breite'] > $max_gr_array['0'] || $image_infos['hoehe'] > $max_gr_array['1']
			)) {
			// Fehlermeldung ausgeben
			$this->content->template['text'] = $this->content->template['message_36'];
			// temporäre Bild-Datei löschen
			@unlink ($image_infos['bild_temp']);
			// Formular zum Upload einer Bild-Datei anbieten
			$this->content->template['template_weiche'] = 'HOCHLADEN';

			return false;
		}

		// Test: Bild-Typ (JPG, GIF, PNG)
		if (!$image_infos['type']) {
			// Fehlermeldung ausgeben
			$this->content->template['text'] = $this->content->template['message_38'];
			// temporäre Bild-Datei löschen
			@unlink ($image_infos['bild_temp']);
			// Formular zum Upload einer Bild-Datei anbieten
			$this->content->template['template_weiche'] = 'HOCHLADEN';

			return false;
		}

		// Test: Datei existiert schon oder Bild diesen Namens ist in Artikel/3.Spalte/Startseite eingebunden
		// ... dann Dateiname mit _<timestamp> erweitern
		if (file_exists($this->image_core->pfad_images.$image_infos['name']) || $this->image_is_used("images/".$image_infos['name'])) {
			/*
			// Fehlermeldung ausgeben
			$this->content->template['text'] = $this->content->template['message_37'];
			// temporäre Bild-Datei löschen
			@unlink ($image_infos['bild_temp']);
			// Formular zum Upload einer Bild-Datei anbieten
			$this->content->template['template_weiche'] = 'HOCHLADEN';
			return false;
			*/
			$temp_info = pathinfo($image_infos['name']);
			$temp_name = $temp_info['filename'];
			$image_infos['name'] = str_ireplace($temp_name, $temp_name."_".time(), $image_infos['name']);
		}

		$dateiname = $this->image_core->pfad_images.$image_infos['name'];
		$dateiname_web = $this->image_core->pfad_images_web.$image_infos['name'];
		$dateiname_thumbnail = $this->image_core->pfad_thumbs.$image_infos['name'];
		$dateiname_thumbnail_web = $this->image_core->pfad_thumbs_web.$image_infos['name'];

		if ($image_infos["type"] === "SVG") {
			copy($image_infos["bild_temp"], $dateiname);
			copy($image_infos["bild_temp"], $dateiname_thumbnail);
		}
		else {
			// Bild sichern
			$image_infos['name'] = strtolower($image_infos['name']);
			$image = $this->image_core->image_create($image_infos['bild_temp']);
			//$this->image_core->image_save($image, $dateiname); // geht leider nicht, da so animierte GIFs nicht mehr funktionieren :-(
			$this->image_core->image_save($image_infos['bild_temp'], $dateiname);

			// ThumbNail erzeugen und sichern
			//$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
			//$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));
			//imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $dimension['breite'], $dimension['hoehe'], $image_infos['breite'], $image_infos['hoehe']);


			// !!! EDIT: Bildgroesse thumbnail
			//$this->image_core->thumbnail_scale = 1;
			$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
			$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));

			imagecopyresampled(	$thumbnail,
				$image,
				0,
				0,
				$dimension['skalierung']['offset_x'],
				$dimension['skalierung']['offset_y'],
				$dimension['breite'],
				$dimension['hoehe'],
				$dimension['skalierung']['breite_korrigiert'],
				$dimension['skalierung']['hoehe_korrigiert']
			);
			// !!! ENDE EDIT Bildgroesse thumbnail

			$this->image_core->image_save($thumbnail, $dateiname_thumbnail);

			// temporäre Daten löschen
			ImageDestroy($image);
			ImageDestroy($thumbnail);
		}

		@unlink ($image_infos['bild_temp']);

		// Informationen an Template Übergeben
		$this->make_div_lang();
		//$this->content->template['image_name'] = $image_infos['name'];
		$this->content->template['image_name'] = $imageOriginalName;
		$this->content->template['dateiname_web'] = $dateiname_web;
		$this->content->template['dateiname_thumbnail_web'] = $dateiname_thumbnail_web;
		$this->content->template['image_breite'] = $image_infos['breite'];
		$this->content->template['image_hoehe'] = $image_infos['hoehe'];
		$this->content->template['image_gruppe'] = 1; // Defaultwert für "Zugriff für andere: JA"
		$this->content->template["image_type"] = $image_infos["type"];
		return true;
	}

	/**
	 * Das Bild in die Datenbank eintragen, wenn es noch nicht existiert
	 *
	 * @param string $location_url
	 */
	function upload_save($location_url="")
	{
		//Bild in die Datenbank eintragen
		$sql = sprintf("INSERT INTO %s SET image_name='%s', image_width='%d', image_height='%s', image_dir='%s' ",
			$this->cms->papoo_images,
			$this->db->escape($this->checked->image_name),
			$this->db->escape($this->checked->image_breite),
			$this->db->escape($this->checked->image_hoehe),
			$this->db->escape($this->checked->image_dir)
		);
		$this->db->query($sql);

		// Imageid Übergeben
		$this->image_id_extern = $this->db->insert_id;

		// alt, title, longdesc eingeben in die Datenbank
		$this->insert_lang($this->db->insert_id, $this->checked->image_name);

		//Wenn thumbnail skaliert wurde, dieses speichern
		if (!empty($this->checked->new_img_src))
		{
			copy(PAPOO_ABS_PFAD .
				"/interna/templates_c/" .
				basename($this->checked->new_img_src),PAPOO_ABS_PFAD .
				"/images/thumbs/" .
				basename($this->checked->image_name)
			);
			unlink(PAPOO_ABS_PFAD."/interna/templates_c/".basename($this->checked->new_img_src));
		}

		// zur Image Startseite
		if (empty($location_url)) {
			$location_url = "./image.php?menuid=19&amp&messageget=39";

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit();
		}
	}

	/**
	 * Unter-Aktionsweiche für Bilder bearbeiten
	 *
	 * @param $action
	 */
	function change_switch($action)
	{
		IfNotSetNull($this->checked->zoom);
		IfNotSetNull($this->checked->change_imagefile_do);

		// Sonderbehandlung für ZOOM (geht leider nicht anders, da Zoom-Knopf innerhalb des NEU_EDIT-Formulars)
		if ($this->checked->zoom)  {
			$action = "ZOOM";
		}

		// Sonderbehandlung für change_imagefile
		if ($this->checked->change_imagefile_do) {
			$action = "CHANGE_IMAGEFILE";
		}

		$this->get_cat_list();

		switch ($action) {
		case "SUCHE":
			// Bild-Liste der Suche ausgeben (wie default:, nur nicht aller Bilder)
			$this->content->template['template_weiche'] = 'LISTE';
			$this->change_liste($this->checked->search);
			break;

		case "EDIT":
			// Informationen des Bildes laden
			$this->content->template['template_weiche'] = 'NEU_EDIT';
			$this->content->template['image_modus'] = 'EDIT';

			$this->change_load($this->checked->image_id);
			break;

		case "CHANGE_IMAGEFILE":
			// Bild-Datei tauschen und erneut zum editieren anbieten
			$this->content->template['template_weiche'] = 'NEU_EDIT';
			$this->content->template['image_modus'] = 'EDIT';

			$this->change_imagefile($this->checked->image_name_org);
			$this->change_load($this->checked->image_id);
			break;

		case "ZOOM":
			// Bild zoomen und erneut zum editieren anbieten
			$this->content->template['template_weiche'] = 'NEU_EDIT';
			$this->content->template['image_modus'] = 'EDIT';

			$this->change_zoom($this->checked->image_name_org, $this->checked->zoom_faktor);
			break;

		case "SPEICHERN_DIALOG":
			// Bild speichern
			$this->content->template['template_weiche'] = 'SPEICHERN_DIALOG';
			$this->change_save_dialog($this->checked->image_id);
			break;

		case "SPEICHERN":
			// Bild speichern
			if (!empty($this->checked->kopie)) {
				$modus = 1;
			}
			else {
				$modus = 0;
			}

			if (!$this->change_save($modus)) {
				// Fehler beim speichern, Dialog erneut anzeigen
				$this->content->template['template_weiche'] = 'SPEICHERN_DIALOG';
				$this->change_save_dialog($this->checked->image_id);
			}
			break;

		case "LOESCHEN":
			// Bild löschen
			$this->change_delete($this->checked->image_id, $this->checked->image_name_org);
			break;

		default:
			// Liste mit Bildern anzeigen
			$this->content->template['template_weiche'] = 'LISTE';
			$this->change_liste();
		}
	}

	/**
	 * Erzeugt eine Liste mit allen Bildern
	 *
	 * @param string $suchtext Wenn angegeben, werden nur gesuchte Bidler angezeigt.
	 */
	function change_liste($suchtext = "")
	{
		IfNotSetNull($this->checked->show);
		IfNotSetNull($this->checked->sort);

		$image_data = array ();
		$this->content->template['link_self_image']="./image.php?menuid=21";
		$this->content->template['show']=$this->checked->show;

		if (empty($this->checked->image_dir)) {
			$this->checked->image_dir = "0";
		}
		$this->content->template['image_dir'] = $this->checked->image_dir;
		$this->get_cat_list();

		if (!empty($suchtext)) {
			$search = " CONCAT(t2.alt, ' ', t2.title, ' ', t2.longdesc) LIKE '%".$this->db->escape($suchtext)."%' ";
		}
		else {
			$search = " t1.image_dir = '".$this->db->escape($this->checked->image_dir)."'";
		}

		$sort_direction="ASC";
		if ($this->checked->sort=="alpha_desc") {
			$sort_direction="DESC";
		}

		if (empty($this->checked->image_dir)) {
			$sql = sprintf("SELECT DISTINCT t1.*, t2.* FROM %s AS t1, %s AS t2
							WHERE t1.image_id=t2.lan_image_id AND %s
							GROUP BY t1.image_id
							ORDER BY title %s",
				$this->cms->papoo_images,
				$this->cms->papoo_language_image,
				$search,
				$sort_direction
			);
		}
		else {
			$sql = sprintf("SELECT DISTINCT t1.*, t2.* FROM %s AS t1, %s AS t2, %s AS t3, %s AS t4, %s AS t5
							WHERE t1.image_id=t2.lan_image_id AND %s

							AND t1.image_dir = t3.bilder_cat_id_id
							AND t4.userid='%d' AND t4.gruppenid=t5.gruppeid AND t5.gruppeid=t3.gruppeid_id
							GROUP BY t1.image_id
							ORDER BY title %s",
				$this->cms->papoo_images,
				$this->cms->papoo_language_image,
				$this->cms->tbname['papoo_lookup_cat_images'],
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->tbname['papoo_gruppe'],
				$search,
				$this->user->userid,
				$sort_direction
			);
		}
		$result = $this->db->get_results($sql);

		// Daten vorbereiten
		if (!empty($result)) {
			foreach ($result as $image) {
				$time=@filemtime(PAPOO_ABS_PFAD."/images/".$image->image_name);
				$last_date=date("d.m.Y - G:i",$time);
				$image_data[] = array (
					'image_id' => $image->image_id,
					'image_name' => $image->image_name,
					'image_width' => $image->image_width,
					'image_height' => $image->image_height,
					'image_alt' => $this->diverse->encode_quote($image->alt),
					'image_title' => $this->diverse->encode_quote($image->title),
					'image_longdesc' => $image->longdesc,
					'image_last_date' => $last_date,
					'image_last_date_time' => $time
				);
			}
		}
		$this->content->template['sort']=$this->checked->sort;

		if ($this->checked->sort=="date_asc") {
			$image_data = $this->array_msort($image_data, array('image_last_date_time'=>SORT_ASC));
		}
		if ($this->checked->sort=="date_desc") {
			$image_data = $this->array_msort($image_data, array('image_last_date_time'=>SORT_DESC));
		}
		$this->content->template['image_data'] = $image_data;
	}

	/**
	 * @param $array
	 * @param $cols
	 * @return array
	 */
	function array_msort($array, $cols)
	{
		$colarr = array();
		foreach ($cols as $col => $order) {
			$colarr[$col] = array();
			foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		$eval = 'array_multisort(';
		foreach ($cols as $col => $order) {
			$eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		$eval = substr($eval,0,-1).');';
		eval($eval);
		$ret = array();
		foreach ($colarr as $col => $arr) {
			foreach ($arr as $k => $v) {
				$k = substr($k,1);
				if (!isset($ret[$k])) $ret[$k] = $array[$k];
				$ret[$k][$col] = $array[$k][$col];
			}
		}
		return $ret;
	}

	/**
	 * Läd die Daten des Bildes $image_id
	 *
	 * @param $image_id
	 */
	function change_load($image_id)
	{
		// Daten für Formular Bearbeitung und Resize
		$result = $this->db->get_results("SELECT * FROM ".$this->cms->papoo_images." WHERE image_id = ".$image_id." LIMIT 1");
		foreach ($result as $row) {
			$this->content->template['image_id'] = $row->image_id;
			$this->content->template['image_name'] = $row->image_name;
			$this->content->template['image_name_org'] = $row->image_name;
			$this->content->template['dateiname_web'] = $this->image_core->pfad_images_web.$row->image_name;
			$this->content->template['dateiname_thumbnail_web'] = $this->image_core->pfad_thumbs_web.$row->image_name;

			$this->content->template['image_breite'] = $row->image_width;
			$this->content->template['image_hoehe'] = $row->image_height;

			$this->content->template['image_dir'] = $row->image_dir;
			$this->content->template["image_type"] = strtoupper(trim(pathinfo($row->image_name, PATHINFO_EXTENSION)));

			$this->make_div_lang($image_id);
		}
	}

	/**
	 * bestehende Bild-Datei ersetzen
	 *
	 * @param string $imagename_org
	 */
	function change_imagefile($imagename_org = "")
	{
		if ($imagename_org AND !empty($_FILES['change_imagefile']['tmp_name'])) {
			$this->image_core->image_load($_FILES['change_imagefile']);
			$image_infos = $this->image_core->image_infos;

			if (!empty($image_infos)) {
				$dateiname = $this->image_core->pfad_images.$imagename_org;
				$dateiname_web = $this->image_core->pfad_images_web.$imagename_org;
				$dateiname_thumbnail = $this->image_core->pfad_thumbs.$imagename_org;
				$dateiname_thumbnail_web = $this->image_core->pfad_thumbs_web.$imagename_org;

				if ($image_infos["type"] === "SVG") {
					copy($image_infos["bild_temp"], $dateiname);
					copy($image_infos["bild_temp"], $dateiname_thumbnail);
				}
				else {
					// Bild sichern
					$image = $this->image_core->image_create($image_infos['bild_temp']);
					//$this->image_core->image_save($image, $dateiname); // geht leider nicht, da so animierte GIFs nicht mehr funktionieren :-(
					@unlink ($dateiname);
					$this->image_core->image_save($image_infos['bild_temp'], $dateiname);

					// ThumbNail erzeugen und sichern
					//$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
					//$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));
					//imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $dimension['breite'], $dimension['hoehe'], $image_infos['breite'], $image_infos['hoehe']);

					// !!! EDIT: Bildgroesse thumbnail
					//$this->image_core->thumbnail_scale = 1;
					$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
					$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']));

					imagecopyresampled(	$thumbnail,
						$image,
						0,
						0,
						$dimension['skalierung']['offset_x'],
						$dimension['skalierung']['offset_y'],
						$dimension['breite'],
						$dimension['hoehe'],
						$dimension['skalierung']['breite_korrigiert'],
						$dimension['skalierung']['hoehe_korrigiert']
					);
					// !!! ENDE EDIT Bildgroesse thumbnail

					@unlink ($dateiname_thumbnail);
					$this->image_core->image_save($thumbnail, $dateiname_thumbnail);

					// temporäre Daten löschen
					ImageDestroy($image);
					ImageDestroy($thumbnail);
				}
				@unlink ($image_infos['bild_temp']);
			}
		}
	}

	/**
	 * zoomt $image um den Faktor $zoom_faktor und speichert es als _temp_<username>_<timestamp>.ext
	 *
	 * @param $image Original-Bild
	 * @param $zoom_faktor
	 */
	function change_zoom($image, $zoom_faktor)
	{
		if ($zoom_faktor < 10) {
			$zoom_faktor = 10;
		} // nicht kleiner als 10% machen

		if ($zoom_faktor > 200) {
			$zoom_faktor = 200;
		} // nicht mehr als 200% vergrößern (da sonst Qualitätsverlust zu groß)

		$this->image_core->image_load($image);
		$infos = $this->image_core->image_infos;

		// Neue Bildgröße berechnen
		$neue_breite = round($infos['breite'] * $zoom_faktor / 100);
		$neue_hoehe = round($infos['hoehe'] * $zoom_faktor / 100);

		$img_new = $this->image_core->image_create(array($neue_breite, $neue_hoehe), $infos['type']);
		$img_src = $this->image_core->image_create($infos['bild_temp']);
		imagecopyresampled($img_new, $img_src, 0, 0, 0, 0, $neue_breite, $neue_hoehe, $infos['breite'], $infos['hoehe']);

		// Evtl. altes temporäres Bild löschen
		if (strpos("XXX".$this->checked->image_name, "temp_".$this->user->username)) {
			@unlink($this->image_core->pfad_images.$this->checked->image_name);
		}

		// Datei-Name für temporäres Bild festlegen
		$temp_name = "_temp_".$this->user->username."_".time().".".strtolower($infos['type']);

		$this->image_core->image_save($img_new, $this->image_core->pfad_images.$temp_name);
		ImageDestroy($img_new);
		ImageDestroy($img_src);

		// Template-Variablen setzen
		$this->content->template['image_id'] = $this->checked->image_id;
		$this->content->template['image_name'] = $temp_name;
		$this->content->template['image_name_org'] = $image;
		$this->content->template['dateiname_web'] = $this->image_core->pfad_images_web.$temp_name;
		$this->content->template['dateiname_thumbnail_web'] = $this->image_core->pfad_thumbs_web.$image;

		$this->content->template['image_breite'] = $neue_breite;
		$this->content->template['image_hoehe'] = $neue_hoehe;
		$this->content->template['zoom_faktor'] = $zoom_faktor;

		$this->content->template['image_dir'] = $this->checked->image_dir;

		$this->make_div_lang($this->checked->image_id);
	}

	/**
	 * Setzt die Daten für den Speichern-Dialog
	 *
	 * @param int $image_id
	 */
	function change_save_dialog($image_id = 0)
	{
		$this->content->template['image_id'] = $this->checked->image_id;
		$this->content->template['image_name'] = $this->checked->image_name;
		$this->content->template['image_name_org'] = $this->checked->image_name_org;
		$this->content->template['dateiname_web'] = $this->image_core->pfad_images_web.$this->checked->image_name;
		$this->content->template['dateiname_thumbnail_web'] = $this->image_core->pfad_thumbs_web.$this->checked->image_name_org;

		if (!empty($this->checked->new_img_src)) {
			$this->content->template['dateiname_thumbnail_web'] = $this->checked->new_img_src;
			$this->content->template['new_img_src'] = $this->checked->new_img_src;
		}

		// Kopie-Name erstellen
		$this->image_core->image_load($this->checked->image_name_org);
		$infos = $this->image_core->image_infos;
		$name_kopie = str_replace(strtolower(".".$infos['type']), "", strtolower($this->checked->image_name_org));
		$name_kopie .= "_".time().".".strtolower($infos['type']);
		$this->content->template['image_name_kopie'] = $name_kopie;

		$this->content->template['image_breite'] = $this->checked->image_breite;
		$this->content->template['image_hoehe'] = $this->checked->image_hoehe;

		$this->content->template['image_dir'] = $this->checked->image_dir;

		$this->make_div_lang($image_id);
	}

	/**
	 * Speichert das Bild in Abhängigkeit des Modus $modus
	 * $modus = 0: im Original speichern
	 * $modus = 1: als Kopie speichern
	 *
	 * @param int $modus
	 * @return bool
	 */
	function change_save($modus = 0)
	{
		// Bild welches zu speichern ist
		$image = $this->checked->image_name;

		// Feststellen ob Bild gezoomt wurde
		if ($this->checked->image_name_org != $this->checked->image_name) {
			$zoomed = true;
		}
		else {
			$zoomed = false;
		}

		// Bild im Original speichern
		if ($modus == 0) {
			// Wenn gezoomt wurde,
			if ($zoomed) {
				// gezoomtest Bild mit Orginal-Namen speichern und temporäres Bild löschen
				@copy ($this->image_core->pfad_images.$image, $this->image_core->pfad_images.$this->checked->image_name_org);
				@unlink ($this->image_core->pfad_images.$image);

				//Daten in Tabelle papoo_images aktualisieren
				$sql = sprintf("UPDATE %s SET image_width='%d', image_height='%d' WHERE image_id='%d' ",
					$this->cms->papoo_images,
					$this->db->escape($this->checked->image_breite),
					$this->db->escape($this->checked->image_hoehe),
					$this->checked->image_id
				);
				$this->db->query($sql);
			}
			//Daten in Tabelle papoo_images aktualisieren
			$sql = sprintf("UPDATE %s SET image_dir='%s' WHERE image_id='%d' ",
				$this->cms->papoo_images,
				$this->db->escape($this->checked->image_dir),
				$this->checked->image_id
			);
			$this->db->query($sql);
			// Texte in Tabelle papoo_language_image aktualisieren
			$this->insert_lang($this->checked->image_id, $this->checked->image_name);
		}

		// Bild als Kopie speichern
		if ($modus == 1) {
			$image_name = $this->diverse->sicherer_dateiname($this->checked->image_name_kopie);

			// Test: Datei existiert schon oder Bild diesen Namens ist in Artikel/3.Spalte/Startseite eingebunden
			// ... dann Dateiname mit _<timestamp> erweitern

			// FIXME: Eigentlich nicht gesetzt, was war hier die Verwendung?
			IfNotSetNull($image_infos);

			if (file_exists($this->image_core->pfad_images.$image_name) ||
				$this->image_is_used("images/". $image_infos['name'])) {
				$this->content->template['text'] = '<div class="alert alert-error">' .
					"Diese Datei besteht schon oder wurde bereits einmal verwendet. Bitte einen anderen Name verwenden." .
					'</div>';
				$this->change_save_dialog($this->checked->image_id);
				return false;
			}

			// Bild erzeugen
			copy ($this->image_core->pfad_images.$image, $this->image_core->pfad_images.$image_name);
			copy ($this->image_core->pfad_thumbs.$image, $this->image_core->pfad_thumbs.$image_name);

			//Bild in die Datenbank eintragen
			$sql = sprintf("INSERT INTO %s SET image_name='%s', image_width='%d', image_height='%s', image_dir='%s' ",
				$this->cms->papoo_images,
				$this->db->escape($image_name),
				$this->db->escape($this->checked->image_breite),
				$this->db->escape($this->checked->image_hoehe),
				$this->db->escape($this->checked->image_dir)
			);

			$this->db->query($sql);
			// alt, title, longdesc eingeben in die Datenbank
			$this->insert_lang($this->db->insert_id, $image_name);
		}

		//Wenn thumbnail skaliert wurde, dieses speichern
		if (!empty($this->checked->new_img_src)) {
			copy(PAPOO_ABS_PFAD . "/interna/templates_c/" . basename($this->checked->new_img_src),
				PAPOO_ABS_PFAD . "/images/thumbs/" . basename($this->checked->image_name));

			unlink(PAPOO_ABS_PFAD . "/interna/templates_c/" . basename($this->checked->new_img_src));
		}

		// Wenn gezoomt wurde, temporäre Datei löschen
		//if ($zoomed) @unlink($this->image_core->pfad_images.$image);

		// zur Image Startseite
		//$location_url = "./image.php?menuid=19&messageget=39";
		$location_url = "./image.php?menuid=21&messageget=39";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="'.$location_url.'">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit ();
	}

	/**
	 * Löscht das Bild mit der ID $image_id und dem Namen $name
	 *
	 * @param $image_id
	 * @param $name
	 */
	function change_delete($image_id, $name)
	{
		//$this->content->template['text'] = $this->content->template['message_22'];
		// 1. Einträge aus Tabelle papoo_images löschen
		$sql = sprintf("DELETE FROM %s WHERE image_id='%d'",
			$this->cms->papoo_images,
			$image_id
		);
		$this->db->query($sql);

		// 2. Einträge aus Sprachtabelle papoo_language_image löschen
		$del = sprintf("DELETE FROM %s WHERE lan_image_id='%d'",
			$this->cms->papoo_language_image,
			$image_id
		);
		$this->db->query($del);

		// 3. Dateien löschen (Bild und Thumbnail)
		@unlink($this->image_core->pfad_images.$name);
		@unlink($this->image_core->pfad_thumbs.$name);

		//header("Location: ./image.php?menuid=21&messageget=22");
		$location_url = "./image.php?menuid=21&messageget=22";
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="'.$location_url.'">Weiter</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}


	/**
	 * @return void
	 * @desc Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 */
	/**
	 * Ausgabe von alt und title und longdesc in verschiedenen Sprachen
	 *
	 * @param int $image_id
	 * @return void
	 */
	function make_div_lang($image_id = 0)
	{
		$texte = array();

		// Wenn Daten aus Formular vorliegen, dann diese durchreichen (nach Größen-Änderung oder Fehler)
		if (!empty ($this->checked->texte)) {
			foreach ($this->checked->texte as $text) {
				$text['alt'] = "nodecode:".$this->diverse->encode_quote($text['alt']);
				$text['title'] = "nodecode:".$this->diverse->encode_quote($text['title']);
				$text['longdesc'] = isset($text['longdesc']) ? "nodecode:".$this->diverse->encode_quote($text['longdesc']): "";
				$texte[$text['lang_id']] = $text;
			}
		}
		// sonst Daten aus Datenbank laden
		else {
			// aktive Sprachen raussuchen
			$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2'",
				$this->cms->papoo_name_language
			);
			$aktive_sprachen = $this->db->get_results($sql);

			if (!empty($aktive_sprachen)) {
				foreach($aktive_sprachen as $sprache) {
					$sql = sprintf("SELECT alt, title, longdesc FROM %s WHERE lang_id='%d' AND lan_image_id='%d' LIMIT 1",
						$this->cms->papoo_language_image,
						$sprache->lang_id,
						$image_id
					);
					$bild_texte =  $this->db->get_results($sql);

					if (!empty($bild_texte)) {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"alt" => "nodecode:".$this->diverse->encode_quote($bild_texte[0]->alt),
							"title" => "nodecode:".$this->diverse->encode_quote($bild_texte[0]->title),
							"longdesc" => "nodecode:".$bild_texte[0]->longdesc,
						);
					}
					else {
						$temp_texte = array("lang_id" => $sprache->lang_id,
							"sprache" => $sprache->lang_long,
							"alt" => "",
							"title" => "",
							"longdesc" => "",
						);
					}
					$texte[$sprache->lang_id] = $temp_texte;
				}
			}
		}

		$this->content->template['menlang'] = $texte;
	}

	/**
	 * Sprach-Texte (alt, title, longdesc) in Tabelle papoo_language_imgae eintragen
	 * image_name als Default-Werrt fuer alt / title
	 *
	 * @param int $image_id
	 * @param string $image_name
	 */
	function insert_lang($image_id = 0, $image_name = "")
	{
		$temp_default_text = " ";

		$texte = $this->checked->texte;

		if (!empty($texte) && $image_id != 0) {
			// 1. alte Einträge aus Tabelle papoo_languga_image löschen
			$sql = sprintf("DELETE FROM %s WHERE lan_image_id='%d'",
				$this->cms->papoo_language_image,
				$image_id
			);
			$this->db->query($sql);

			// 2. neue Text in Tabelle papoo_languag_image eintragen
			foreach($texte as $text) {
				$temp_alt = $text['alt'];
				if (empty($temp_alt)) {
					$temp_alt = $temp_default_text;
				}

				$temp_title = $text['title'];
				if (empty($temp_title)) {
					$temp_title = $temp_default_text;
				}

				$sql = sprintf("INSERT INTO %s
								SET lan_image_id='%d',
								lang_id='%d',
								alt='%s',
								title='%s',
								longdesc='%s' ",
					$this->cms->papoo_language_image,
					$image_id,
					$text['lang_id'],
					$this->db->escape($temp_alt),
					$this->db->escape($temp_title),
					$this->db->escape($text['longdesc'])
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Die Kategorienliste erstellen
	 *
	 * @return array|void
	 */
	function get_cat_list()
	{
		//Ordnerliste
		$intern_ordner = new intern_ordner_class();
		$intern_ordner->show_ordner_bilder("papoo_kategorie_bilder");

		IfNotSetNull($this->checked->image_dir);
		IfNotSetNull($level);

		//Aktueller Level
		$aktu_sub_id = NULL;
		if (is_array($this->content->template['dirlist'])) {
			foreach ($this->content->template['dirlist'] as $key=>$value) {
				if ($this->checked->image_dir==$value['bilder_cat_id']) {
					$level=$value['image_sub_cat_level']+1;
					$aktu_sub_id=$value['image_sub_cat_von'];
				}
			}
		}

		$this->content->template['bilder_cat_id'] = $aktu_sub_id;
		$this->content->template['bilder_active_cat_id'] = $this->checked->image_dir;

		$sql = sprintf("SELECT bilder_cat_id,COUNT(image_id) AS count FROM %s
						LEFT JOIN %s ON image_dir=bilder_cat_id
						GROUP BY bilder_cat_id
						ORDER BY bilder_cat_name ASC",
			$this->cms->tbname['papoo_images'],
			$this->cms->tbname['papoo_kategorie_bilder']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (empty($value['bilder_cat_id'])) {
					$value['bilder_cat_id']=0;
				}
				$neu[$value['bilder_cat_id']]=$value['count'];
			}
		}
		IfNotSetNull($neu);
		$this->content->template['bilder_cat_id_count'] = $neu;
		IfNotSetNull($this->content->template['bilder_cat_id_count'][0]);

		$sql = sprintf("SELECT DISTINCT(bilder_cat_id), bilder_cat_name FROM
						%s, %s, %s
						WHERE userid='%d'
						AND gruppenid=gruppeid_id
						AND bilder_cat_id_id=bilder_cat_id
						AND image_sub_cat_level ='%d'
						AND image_sub_cat_von ='%d'
						ORDER BY bilder_cat_name
						ASC",
			$this->cms->tbname['papoo_kategorie_bilder'],
			$this->cms->tbname['papoo_lookup_cat_images'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid,
			$level,
			$this->db->escape($this->checked->image_dir)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (!empty($result)) {
			foreach ($result as $dat) {
				if ($dat['bilder_cat_id']==$this->checked->image_dir) {
					$this->content->template['bilder_cat_name'] = $dat['bilder_cat_name'];
				}
			}
		}
		$this->content->template['result_cat'] = $result;
		$this->content->template['result_cat_images'] = $result;
		return $result;
	}

	/**
	 * Prueft ob Bild image_name in einem Artikel / 3. Spalte / Startseite eingebunden ist.
	 *
	 * @param string $image_name
	 * @return bool
	 */
	function image_is_used($image_name = "")
	{
		$temp_return = false;
		if (!empty($image_name) && strlen($image_name)>7) {
			$sql = sprintf('SELECT COUNT(*) AS totalcount FROM %s AS t1, %s AS t2, %s AS t3
							WHERE t1.lan_teaser LIKE "%%%s%%"
							OR t1.lan_article LIKE "%%%s%%"
							OR t2.article LIKE "%%%s%%"
							OR t3.start_text LIKE "%%%s%%"',
				$this->db_praefix.'papoo_language_article',
				$this->db_praefix.'papoo_language_collum3',
				$this->db_praefix.'papoo_language_stamm',
				$this->db->escape($image_name),
				$this->db->escape($image_name),
				$this->db->escape($image_name),
				$this->db->escape($image_name)
			);
			$temp_count = $this->db->get_var($sql);

			if ($temp_count > 0) {
				$temp_return = true;
			}
		}
		return $temp_return;
	}
}

$intern_image = new intern_image_class();
