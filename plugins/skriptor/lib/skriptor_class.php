<?php

/**
 * Class skriptor_class
 */
#[AllowDynamicProperties]
class skriptor_class
{
	/** @var array Filter von Keywords welche beim zusammenfassen von JS Dateien nicht berücksichtigt werden. */
	public $jsfilter = [
		"facebook", "geocoder", "http", "piwik", "ie6", "<fb", "google",
		"tiny", "template=galerie", "webtrekk", "main", "wt_sendinfo",
	];

	/** @var array Filter von Keywords welche beim zusammenfassen von CSS Dateien nicht berücksichtigt werden. */
	public $cssfilter = [
		"IEFixes", "ie6bar", "awesome", "dropdown", "mobile", "jquery", "hreflang",
	];

	function __construct()
	{
		// Einbindung globaler Papoo-Objekte
		global $content, $checked, $db, $user, $image_core;
		$this->content =& $content;
		$this->checked =& $checked;
		$this->db =& $db;
		$this->user =& $user;
		$this->image_core = $image_core;

		$this->make_skriptor();
	}

	/**
	 * skriptor_class::make_skriptor()
	 *
	 * @return void
	 */
	function make_skriptor()
	{
		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			// Überprüft, ob in der aktuellen URL der String "skriptor_back.html" vorkommt
			if (strpos("XXX" . $template, "skriptor_back.html")) {

				//Überprüft, ob der "submit"-Button geklickt wurde
				if (isset($this->checked->submit) && $this->checked->submit) {
					$this->write_into_db();
				}
				$this->read_from_db();
			}
		}
	}

	/**
	 * skriptor_class::write_into_db()
	 *
	 * @return void
	 */
	function write_into_db()
	{
		global $db_praefix;

		//Checkboxen abfragen und Wert entsprechend setzen
		$js_active_checked = 0;
		$js_compress_checked = 0;
		$js_remove_comments_checked = 0;

		$css_active_checked = 0;
		$css_compress_checked = 0;
		$css_remove_comments_checked = 0;
		$site_speed_plugin_leerzeichen_tabs_checked = 0;
		$site_speed_plugin_komplet_html_checked = 0;

		//$_POST ist ein Array, welches beim Klick auf den submit Button übergeben wird.
		if(isset($_POST['options']) && $_POST['options']) {
			foreach($_POST['options'] as $opt) {
				if($opt == "js_active") {
					$js_active_checked = 1;
				}
				if($opt == "js_compress") {
					$js_compress_checked = 1;
				}
				if($opt == "js_remove_comments") {
					$js_remove_comments_checked = 1;
				}

				if($opt == "css_active") {
					$css_active_checked = 1;
				}
				if($opt == "css_compress") {
					$css_compress_checked = 1;
				}
				if($opt == "css_remove_comments") {
					$css_remove_comments_checked = 1;
				}

				if($opt == "site_speed_plugin_leerzeichen_tabs") {
					$site_speed_plugin_leerzeichen_tabs_checked = 1;
				}
				if($opt == "site_speed_plugin_komplet_html") {
					$site_speed_plugin_komplet_html_checked = 1;
				}
				if($opt == "site_speed_plugin_bilder_komprimieren") {
					$site_speed_plugin_bilder_komprimieren = 1;
				}
			}
			if($js_active_checked == 0) {
				$js_compress_checked = 0;
				$js_remove_comments_checked = 0;
			}

			if($css_active_checked == 0) {
				$css_compress_checked = 0;
				$css_remove_comments_checked = 0;
			}
		}

		IfNotSetNull($site_speed_plugin_bilder_komprimieren);

		// SQL-Anweisung zusammenstellen
		$query = sprintf( "UPDATE %s 
                            SET js_active='%s',
                            js_compress='%s',
                            js_remove_comments='%s', 
                            css_active='%s',
                            css_compress='%s', 
                            css_remove_comments='%s',
                            site_speed_plugin_leerzeichen_tabs='%d',
                            site_speed_plugin_komplet_html='%d',
                            site_speed_plugin_bilder_komprimieren='%d'
                            WHERE id=1;",
			$db_praefix . "plugin_skriptor",
			$this->db->escape($js_active_checked),
			$this->db->escape($js_compress_checked),
			$this->db->escape($js_remove_comments_checked),
			$this->db->escape($css_active_checked),
			$this->db->escape($css_compress_checked),
			$this->db->escape($css_remove_comments_checked),
			$this->db->escape($site_speed_plugin_leerzeichen_tabs_checked),
			$this->db->escape($site_speed_plugin_komplet_html_checked),
			$this->db->escape($site_speed_plugin_bilder_komprimieren)
		);

		// SQL-Anweisung ausführen
		$this->db->query( $query );
	}

	/**
	 * skriptor_class::read_from_db()
	 * Daten aus der DB auslesen und in die entsprechenden Varisablen schreiben
	 *
	 * @return array|void
	 */
	function read_from_db()
	{
		global $db_praefix;

		// SQL-Anweisung zusammenstellen
		$query = sprintf("SELECT * FROM %s WHERE id=1", $db_praefix . "plugin_skriptor");

		// Resultat der SQL-Anweisung in die Eigenschaft "$result" schreiben
		$result = $this->db->get_results( $query );

		$js_active = $result[ 0 ]->js_active;
		$js_compress = $result[ 0 ]->js_compress;
		$js_remove_comments = $result[ 0 ]->js_remove_comments;
		$css_active = $result[ 0 ]->css_active;
		$css_compress = $result[ 0 ]->css_compress;
		$css_remove_comments = $result[ 0 ]->css_remove_comments;

		$site_speed_plugin_leerzeichen_tabs = $result[ 0 ]->site_speed_plugin_leerzeichen_tabs;
		$site_speed_plugin_komplet_html = $result[ 0 ]->site_speed_plugin_komplet_html;
		$site_speed_plugin_bilder_komprimieren = $result[ 0 ]->site_speed_plugin_bilder_komprimieren;

		//checkboxen leer machen
		$this->content->template['plugin']['skriptor']['js_active_checked'] = "";
		$this->content->template['plugin']['skriptor']['js_compress_checked'] = "";
		$this->content->template['plugin']['skriptor']['js_remove_comments_checked'] = "";
		$this->content->template['plugin']['skriptor']['css_active_checked'] = "";
		$this->content->template['plugin']['skriptor']['css_compress_checked'] = "";
		$this->content->template['plugin']['skriptor']['site_speed_plugin_leerzeichen_tabs_checked'] = "";
		$this->content->template['plugin']['skriptor']['site_speed_plugin_komplet_html_checked'] = "";
		$this->content->template['plugin']['skriptor']['site_speed_plugin_bilder_komprimieren'] = "";

		//checkboxen evtl. anhaken
		if($js_active == 1) {
			$this->content->template['plugin']['skriptor']['js_active_checked'] = "checked=\"checked\"";
		}
		if($js_compress == 1) {
			$this->content->template['plugin']['skriptor']['js_compress_checked'] = "checked=\"checked\"";
		}
		if($js_remove_comments == 1) {
			$this->content->template['plugin']['skriptor']['js_remove_comments_checked'] = "checked=\"checked\"";
		}

		if($css_active == 1) {
			$this->content->template['plugin']['skriptor']['css_active_checked'] = "checked=\"checked\"";
		}
		if($css_compress == 1) {
			$this->content->template['plugin']['skriptor']['css_compress_checked'] = "checked=\"checked\"";
		}
		if($css_remove_comments == 1) {
			$this->content->template['plugin']['skriptor']['css_remove_comments_checked'] = "checked=\"checked\"";
		}

		if($site_speed_plugin_leerzeichen_tabs == 1) {
			$this->content->template['site_speed_plugin']['0']['site_speed_plugin_leerzeichen_tabs'] = "1";
		}
		if($site_speed_plugin_komplet_html == 1) {
			$this->content->template['site_speed_plugin']['0']['site_speed_plugin_komplet_html'] = "1";
		}
		if($site_speed_plugin_bilder_komprimieren == 1) {
			$this->content->template['site_speed_plugin']['0']['site_speed_plugin_bilder_komprimieren'] = "1";
		}

		return ( $result );
	}

	/**
	 * skriptor_class::flat_array()
	 *
	 * @param mixed $example_array
	 * @return mixed
	 */
	function flat_array( $example_array )
	{
		$index = 0;
		$index2 = 0;
		foreach($example_array as $files) {
			foreach($example_array[$index2] as $file) {
				$flat_array[$index] = $file;
				$index++;
			}
			$index2++;
		}
		IfNotSetNull($flat_array);
		return $flat_array;
	}

	/**
	 * @param $data
	 * @return mixed
	 */
	private function comp_images($data )
	{
		// Die img tags komplett
		preg_match_all( '/<img[^>]*>/Ui', $data, $img );

		//Bilder Pfade und HTML Größe rausholen
		if ( is_array( $img[ '0' ] ) ) {
			foreach ( $img[ '0' ] as $key => $value ) {
				$regexp = '/(<img[^>]*src="(.*?)"[^>]*>)/i';
				preg_match_all( $regexp, $value, $aMatches );

				$img_array[$key]['src'] = PAPOO_ABS_PFAD . str_replace(PAPOO_WEB_PFAD, "", $aMatches['2']['0']);
				$img_array[$key]['src_web'] = $aMatches['2']['0'];

				$regexp = '/(<img[^>]*height="(.*?)"[^>]*>)/i';
				preg_match_all($regexp, $value, $bMatches);
				IfNotSetNull($bMatches['2']['0']);
				$img_array[$key]['height'] = $bMatches['2']['0'];

				$regexp = '/(<img[^>]*width="(.*?)"[^>]*>)/i';
				preg_match_all($regexp, $value, $cMatches);
				IfNotSetNull($cMatches['2']['0']);
				$img_array[$key]['width'] = $cMatches['2']['0'];
			}
		}

		//Dann die Bilder durchgehen und tatsächliche Größe rausholen
		if (isset($img_array) && is_array($img_array)) {
			foreach ( $img_array as $key => $value ) {
				$size = @getimagesize( $value[ 'src' ] );
				$img_array[ $key ][ 'real_height' ] = $size[ '1' ];
				$img_array[ $key ][ 'real_width' ]  = $size[ '0' ];

				switch ( $size[ 2 ] ) {
				case 1:
					$img_array[ $key ][ 'mime' ] = "GIF";
					break;

				case 2:
					$img_array[ $key ][ 'mime' ] = "JPG";
					break;

				case 3:
					$img_array[ $key ][ 'mime' ] = "PNG";
					break;

				case 4: // SWF ist kein unterstütztes Format
					//$this->image_infos['type'] = "SWF";
					//break;

				default:
					$img_array[ $key ][ 'mime' ] = false;
				}
			}
		}

		if (isset($img_array) && is_array($img_array)) {
			foreach ( $img_array as $key => $value ) {
				$img_array[ $key ][ 'new_img' ] = $this->create_resized_image( $value );
			}
		}

		if (isset($img_array) && is_array($img_array)) {
			foreach ( $img_array as $key => $value ) {
				//Nur wenn auch ein neues File vorhanden ist
				if ( !empty( $value[ 'new_img' ] ) && @file_exists( $value[ 'new_img' ] ) ) {
					$value[ 'new_img' ] = PAPOO_WEB_PFAD . "/" . str_replace( PAPOO_ABS_PFAD, "", $value[ 'new_img' ] );
					$value[ 'new_img' ] = str_replace( "//", "/", $value[ 'new_img' ] );
					//ersetzen
					$data = str_replace( 'src="' . $value[ 'src_web' ], 'src="' . $value[ 'new_img' ], $data );
				}
			}
		}
		return $data;
	}

	/**
	 * detects if a picture is animated or not
	 *
	 * @param $filename
	 * @return bool
	 */
	function is_ani($filename)
	{
		if(!($fh = @fopen($filename, 'rb'))) {
			return false;
		}
		$count = 0;
		//an animated gif contains multiple "frames", with each frame having a
		//header made up of:
		// * a static 4-byte sequence (\x00\x21\xF9\x04)
		// * 4 variable bytes
		// * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

		// We read through the file til we reach the end of the file, or we've found
		// at least 2 frame headers
		$chunk = false;
		while(!feof($fh) && $count < 2) {
			//add the last 20 characters from the previous string, to make sure the searched pattern is not split.
			$chunk = ($chunk ? substr($chunk, -20) : "") . fread($fh, 1024 * 100); //read 100kb at a time
			$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
		}

		fclose($fh);
		return $count > 1;
	}

	/**
	 * skriptor_class::create_resized_image()
	 *
	 * @param mixed $image
	 * @return bool|string
	 */
	private function create_resized_image( $image )
	{
		if ( @file_exists( $image[ 'src' ] ) && @getimagesize( $image[ 'src' ] ) ) {
			$this->image_core->image_infos[ 'breite' ] = $image[ 'real_width' ];
			$this->image_core->image_infos[ 'hoehe' ]  = $image[ 'real_height' ];
			$this->image_core->image_infos[ 'name' ]   = basename( $image[ 'src' ] );
			$this->image_core->image_infos[ 'type' ]   = $image[ 'mime' ];

			if ( empty( $image[ 'width' ] ) ) {
				$image[ 'width' ] = $image[ 'real_width' ];
			}
			if ( empty( $image[ 'height' ] ) ) {
				$image[ 'height' ] = $image[ 'real_height' ];
			}

			$image_infos = $this->image_core->image_infos;
			$tmp_pfad    = PAPOO_ABS_PFAD . "/templates_c/";

			$dateiname_thumbnail = $tmp_pfad . $image[ 'width' ] . "_" . $image[ 'height' ] . "-" . $image_infos[ 'name' ];

			if (file_exists($dateiname_thumbnail)) {
				return $dateiname_thumbnail;
			}

			$image_resource = $this->image_core->image_create( $image[ 'src' ] );

			// ThumbNail erzeugen und sichern
			$this->image_core->tumbnail_max_groesse[ 'breite' ] = $image[ 'width' ];
			$this->image_core->tumbnail_max_groesse[ 'hoehe' ]  = $image[ 'height' ];

			//$dimension = $this->image_core->image_get_thumbnail_size($image_infos['breite'], $image_infos['hoehe']);
			//$thumbnail = $this->image_core->image_create(array($dimension['breite'], $dimension['hoehe']), "GIF");
			$thumbnail = $this->image_core->image_create( array(
				$image[ 'width' ],
				$image[ 'height' ]
			) );

			if ( $image[ 'mime' ] == "PNG" ) {
				// Turn off alpha blending and set alpha flag
				@imagealphablending( $thumbnail, false );
				imagesavealpha( $thumbnail, true );
			}

			if ( $image[ 'mime' ] == "GIF" ) {
				if ($this->is_ani( $image[ 'src' ] ) ) {
					return false;
				}

				$trnprt_indx = @imagecolortransparent( $image_resource );
				if ( $trnprt_indx >= 0 ) {
					// Get the original image's transparent color's RGB values
					$trnprt_color = @imagecolorsforindex( $image_resource, $trnprt_indx );

					// Allocate the same color in the new image resource
					$trnprt_indx = @imagecolorallocate( $thumbnail, $trnprt_color[ 'red' ], $trnprt_color[ 'green' ], $trnprt_color[ 'blue' ] );

					// Completely fill the background of the new image with allocated color.
					@imagefill( $thumbnail, 0, 0, $trnprt_indx );

					// Set the background color for new image to transparent
					@imagecolortransparent( $thumbnail, $trnprt_indx );
				}
			}
			@imagecopyresampled( $thumbnail, $image_resource, 0, 0, 0, 0,
				$image[ 'width' ], $image[ 'height' ], $image_infos[ 'breite' ], $image_infos[ 'hoehe' ] );

			$this->image_core->image_save( $thumbnail, $dateiname_thumbnail );

			// temporäre Daten löschen
			@ImageDestroy( $image_resource );
			@ImageDestroy( $thumbnail );
			//unlink ($image_infos['bild_temp']);

			return $dateiname_thumbnail;
		}
		return false;
	}

	/**
	 * skriptor_class::get_linked_stylesheets()
	 * Durchsucht einen String nach CSS-Dateien, die per <link rel="stylesheet"......>
	 * eingebunden werden und gibt diese sortiert zurück
	 * in $css_files[0] stehen die für media screen
	 * in $css_files[1] stehen die für media print
	 *
	 * @param mixed $haystack
	 * @return array
	 */
	function get_linked_stylesheets( $haystack )
	{
		$css_files = array();
		$css_files[0] = array();
		$css_files[1] = array();
		$print_css_files = array();
		$screen_css_files = array();

		preg_match_all( '|<link(.*)>|U', $haystack, $result );
		$list_of_embeddings = $result[ 1 ];

		$index  = 0;
		$index1 = 0;
		foreach ( $list_of_embeddings as $list_item ) {
			//Überprüfen, ob mit media = "print" eingebunden wird
			if ( preg_match( '|media(.*)=(.*)"print"|i', $list_item ) ) {
				preg_match_all( '|stylesheet(.*)href(.*)"(.*)"(.*)|Us', $list_item, $result_2 );
				IfNotSetNull($result_2[ 3 ][ 0 ]);
				$print_css_files[ $index1 ] = $result_2[ 3 ][ 0 ];
				$index1++;
			}
			else {
				preg_match_all( '|stylesheet(.*)href(.*)"(.*)"(.*)|Us', $list_item, $result_2 );
				IfNotSetNull($result_2[ 3 ][ 0 ]);
				$screen_css_files[ $index ] = $result_2[ 3 ][ 0 ];
				$index++;
			}
		}

		$css_files[ 0 ] = $screen_css_files;
		$css_files[ 1 ] = $print_css_files;

		//Absolut-Pfade relativieren
		$index2 = 0;
		foreach ( $css_files as $files ) {
			$index = 0;
			foreach ( $css_files[ $index2 ] as $file ) {
				$webpfad = PAPOO_WEB_PFAD;
				if ( !empty( $webpfad ) ) {
					$file = str_replace( PAPOO_WEB_PFAD . '/', '', $file );
				}
				else {
					//$filename=substr($filename,1,-1);
					$file = PAPOO_ABS_PFAD ."/". $file;

				}

				$css_files[ $index2 ][ $index ] = $file;
				$index++;
			}
			$index2++;
		}
		return $css_files;
	}

	/**
	 * skriptor_class::get_at_imported_stylesheets()
	 * Durchsucht einen String nach CSS-Dateien, die per @import eingebunden werden
	 * und gibt diese in einem Array zurück.
	 * Lässt dabei spezielle IE-CSS-Files außen vor.
	 *
	 * @param mixed $haystack
	 * @param int $sub
	 * @return array
	 */
	function get_at_imported_stylesheets( $haystack, $sub = 1 )
	{
		$css_files = array();
		$wanted_css_files = array();

		// Imports matchen und ins result array legen
		preg_match_all('|@import(.*)url\((.*)\);|Us', $haystack, $result );
		$css_files = array_merge($css_files, $result[2]);
		//Absolut-Pfade relativieren
		$index = 0;

		foreach($css_files as $css_file) {
			if(!strpos($css_file, "IEFixes") && !strpos($css_file, "ie6bar")) {
				if(!empty(PAPOO_WEB_PFAD)) {
					$css_file = str_replace(PAPOO_WEB_PFAD . "/", "", $css_file);
				}
				else {
					//$filename=substr($filename,1,-1);
					if(!stristr($css_file, PAPOO_ABS_PFAD) && $sub == 1) {
						$css_file = PAPOO_ABS_PFAD . "/" . $css_file;
					}
				}

				$wanted_css_files[$index] = $css_file;
				$index++;
			}
		}
		return $wanted_css_files;
	}

	/**
	 * skriptor_class::scan_css_files()
	 * Scannt eine css Datei auf weitere CSS-Dateien, die durch @import
	 * eingebunden werden und gibt diese als Array zurück
	 *
	 * @param mixed $css_file - Datei, die druchsucht werden soll
	 * @return array
	 */
	function scan_css_files( $css_file )
	{
		if ( !empty( $css_file ) ) {
			//Pfad zur css-Datei raussuchen, die gescannt wird
			preg_match_all( '|(.*)/(.*)css|Us', $css_file, $tmp_array );
			$css_path = $tmp_array[ 0 ][ 0 ];
			//Die css-Datei auf @imports scannen
			if ( file_exists( $css_file ) ) {
				$stylesheet = file_get_contents( $css_file );
				$css_files  = ( $this->get_at_imported_stylesheets( $stylesheet, "2" ) );

				$css_files_with_path = array( );
				$index = 0;
				if ( is_array( $css_files ) ) {
					foreach ( $css_files as $file ) {
						$css_files_with_path[ $index ] = $css_path . '/' . $file;
						$index++;
					}
				}
				return $css_files_with_path;
			}
			else {
				return array( );
			}
		}
		return array( );
	}

	/**
	 * skriptor_class::get_css_files()
	 * Durchsucht einen string nach eingebundenen CSS-Files und scannt jedes gefunde
	 * nach weiteren @imports. Gibt alles sortiert zurück
	 * in $css_files[0] stehen die für screen
	 * in $css_files[1] stehen die für print
	 *
	 * @param mixed $haystack
	 * @return array
	 */
	function get_css_files( $haystack )
	{
		//<link rel="stylesheet" raussuchen
		//in $css-files[0] stehen die für screen
		//in $css-files[1] stehen die für print
		$css_files = $this->get_linked_stylesheets( $haystack );
		$screen_css_files[ 0 ] = $css_files[ 0 ];
		////@import stylesheets dazupacken
		$screen_css_files[ 0 ] = array_merge( $screen_css_files[ 0 ], $this->get_at_imported_stylesheets( $haystack ) );
		//alle stylesheets nach weiteren @imports durchsuchen
		$index = 0;
		do {
			$screen_css_files[ $index + 1 ] = array( );
			foreach ( $screen_css_files[ $index ] as $key => $file ) {
				if ( !strpos( $file, "dropdown" ) ) {
					if ( !stristr( $file, PAPOO_ABS_PFAD ) ) {
						//$file=PAPOO_ABS_PFAD.$file;
					}
					$screen_css_files[ $index + 1 ] = array_merge( $screen_css_files[ $index + 1 ], $this->scan_css_files( $file ) );
				}
				else {
					//$screen_css_files[$index]=array();
					unset( $screen_css_files[ $index ][ $key ] );
				}

			}
			$index++;
			IfNotSetNull($screen_css_files[ $index ][ 0 ]);
		}
		while ( $screen_css_files[ $index ][ 0 ] );
		//sortieren
		$css_files[ 0 ] = $this->flat_array( $screen_css_files );
		return $css_files;
	}

	/**
	 * skriptor_class::del_stylesheets()
	 * Durchsucht einen string nach Stylesheets, die eingebunden werden, ohne
	 * die gefundenen weiter zu scannen.
	 * Wird benutzt, um die stylesheets der "ersten Ebene zu finden, die dann
	 * ausgeblendet werden
	 *
	 * @param mixed $haystack
	 * @return array|mixed
	 */
	function del_stylesheets( $haystack )
	{
		preg_match_all( '|<link(.*)=(.*)"stylesheet"(.*)/>|Us', $haystack, $tmp_array );
		$to_delete = $tmp_array[ 0 ];
		preg_match_all( '|<style(.*)=(.*)"text/css">(.*)</style>|Us', $haystack, $tmp_array );

		// FIXME: Was ist das? ($this->cssfilter verwenden für eine verbesserung)
		foreach($tmp_array[0] as $file) {
			if(!strpos($file, "IEFixes") &&
				!strpos($file, "ie6bar") &&
				!strpos($file, "awesome") &&
				!strpos($file, "dropdown") &&
				!strpos($file, "mobile") &&
				!strpos($file, "jquery") &&
				!strpos($file, "hreflang")
			) {
				$to_delete = array_merge($to_delete, (array)$file);
			}
		}
		foreach($to_delete as $key => $file) {
			if(!strpos($file, "IEFixes") &&
				!strpos($file, "ie6bar") &&
				!strpos($file, "awesome") &&
				!strpos($file, "mobile") &&
				!strpos($file, "dropdown") &&
				!strpos($file, "jquery") &&
				!strpos($file, "hreflang")
			) {
				$to_delete2[$key] = $file;
			}
		}
		IfNotSetNull($to_delete2);
		$to_delete = $to_delete2;

		if(isset($this->not_delete) && is_array($this->not_delete)) {
			foreach($this->not_delete as $key => $value) {
				if(is_array($to_delete)) {
					foreach($to_delete as $key2 => $value2) {
						if(stristr($value2, basename($value))) {
							$this->special_css[] = $value2;
						}
					}
				}
			}
		}
		return $to_delete;
	}

	/**
	 * skriptor_class::replace_css_files()
	 *
	 * Ersetzt alle CSS-Dateien, die eingebunden werden, durch eine "master.css"-Datei
	 *
	 * @param mixed $css_files - Liste der css-Dateien, die eingebunden werden
	 * @param mixed $css_dir - styles-Verzeichnis, das aktiv ist
	 * @param mixed $db_result - Parameter für die Datenbankabfrage der Optionen
	 * @return void
	 */
	function replace_css_files( $css_files, $css_dir, $db_result )
	{
		global $output;

		//CSS-Dateien zusammenpacken (screen)
		$css_content = '';
		if (is_array($css_files[ 2 ])) {
			foreach ( $css_files[ 2 ] as $file ) {
				//Pfad zur jeweiligen CSS-Datei herausfinden;
				preg_match_all( '|(.*)/(.*)|Us', $file, $tmp_array );
				$index = 0;

				$css_path = PAPOO_WEB_PFAD . '/';
				do {
					IfNotSetNull($tmp_array[ 0 ][ $index ]);
					$css_path = $css_path . $tmp_array[ 0 ][ $index ];
					$index++;
					IfNotSetNull($tmp_array[ 0 ][ $index ]);
				}
				while ( $tmp_array[ 0 ][ $index ] );

				$css_path = str_replace( PAPOO_ABS_PFAD, "", $css_path );
				$css_path = str_replace( "//", "/", $css_path );

				//relative Pfade zu Bilddateien im CSS absoluten
				if ( !empty( $file ) && file_exists( $file ) ) {
					$file_content = file_get_contents( $file );

					$file_content = preg_replace( '/(url\\(\\s*(?:"|\')?)/', '\1' . $css_path, $file_content );
					$file_content = str_replace('url(bilder', 'url(' . $css_path . 'bilder', $file_content);

					//Anfang und Ende der jeweiligen CSS-Datei im Stream markieren
					$css_content = $css_content . '/* CSS-Datei: ' . $file . '*/' . "\n" . $file_content . "\n" . '/* ENDE CSS-Datei: ' . $file . '*/' . "\n\n";
				}

			}
		}
		//CSS-Dateien zusammenpacken (print)
		$print_css_content = '';
		foreach ( $css_files[ 1 ] as $file ) {
			if ( !empty( $file ) && file_exists( $file ) ) {
				$print_css_content = $print_css_content . '/* CSS-Datei: ' . $file . '*/' . "\n" . file_get_contents( $file ) . "\n" .
					'/* ENDE CSS-Datei: ' . $file . '*/' . "\n\n";
			}
		}

		$css_content = $css_content . '@media print {' . $print_css_content . '}';

		//@imports entfernen
		preg_match_all( '|@import url\((.*)\);|Us', $css_content, $result );
		foreach ( $result[ 0 ] as $at_import_css ) {
			$css_content = str_replace( $at_import_css, '', $css_content );
		}

		/* remove comments */
		if ( $db_result[ 0 ]->css_remove_comments == "1" ) {
			$css_content = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_content );
		}
		/* remove tabs, spaces, newlines, etc. */
		if ( $db_result[ 0 ]->css_compress == "1" ) {
			$css_content = str_replace( array(
				"\r\n",
				"\r",
				"\n",
				"\t",
				'  ',
				'    ',
				'    '
			), ' ', $css_content );
		}

		//Master-CSS-Datei schreiben
		file_put_contents( PAPOO_ABS_PFAD . "/templates_c/" . $this->content->template[ 'style_dir' ] . "_master.css", $css_content );

		//Master-CSS-Datei in output einbinden
		$einbindung = '<link rel="stylesheet" type="text/css" media="screen" href="' . PAPOO_WEB_PFAD . '/templates_c/' .
			$this->content->template[ 'style_dir' ] . '_master.css" />';
		$output = str_replace( "</head>", "\n" . $einbindung . "</head>", $output );

		//$einbindung='<link rel="stylesheet" href="/styles/mb2011/css/dropdown.css" type="text/css" />';

		//$output = str_replace("</head>", "\n". $einbindung . "</head>", $output);

		//Special CSS $this->special_css
		if(isset($this->special_css) && is_array($this->special_css)) {
			foreach($this->special_css as $special_css) {
				$output = str_replace("</head>", "\n" . $special_css . "</head>", $output);
			}
		}
	}

	/**
	 * skriptor_class::replace_javascripts()
	 * durchsucht $output nach eingebundenen Javascripts, schreibt alle in eine
	 * externe Datei und gibt Liste von <script type="javascript">...</script> Blöcken
	 * zurück, die dann ausgeblendet werden können.
	 * *
	 *
	 * @param $db_result
	 * @return mixed $to_delete
	 * @throws Exception
	 */
	function replace_javascripts($db_result)
	{
		global $output;

		$javascript_files = array();

		preg_match_all('|<script(.*)</script>|Us', $output, $result);
		$javascript_embeddings = $result[ 1 ];

		$filtered_keywords = $this->jsfilter;
		foreach ($javascript_embeddings as $script) {
			foreach ($filtered_keywords as $keyword) {
				if (strpos($script, $keyword) !== false) {
					$neu = array_filter($javascript_embeddings, function($script) use ($keyword) {
						return strpos($script, $keyword) === false;
					});
				}
			}
		}

		IfNotSetNull($neu);
		$javascript_embeddings = $neu;
		$javascript_complete = "";

		if ($javascript_embeddings) {
			$index = 0;
			foreach ($javascript_embeddings as $embedding) {
				if (stripos($embedding, "javascript")) {
					//Liste von Javascrip-Einbindungen erstellen, die später ausgeblendet werden sollen
					$to_delete[$index] = '<script' . $embedding . '</script>';

					if (strpos($embedding, "src")) {
						preg_match_all('|src(.*)=(.*)"(.*)"|Us', $embedding, $result);
						$filename = $result[3][0];

						$webpfad = PAPOO_WEB_PFAD;
						if (!empty($webpfad)) {
							$filename = str_replace(PAPOO_WEB_PFAD . '/', '', $filename);
						}
						else {
							//$filename=substr($filename,1,-1);
							$filename = PAPOO_ABS_PFAD . '/' . $filename;
						}

						$filename = str_replace('//', '/', $filename);

						//Falls externes Skript doppelt vorhanden, Zähler um 1 erniedrigen
						//damit es in der Liste überschieben wird
						foreach ($javascript_files as $file) {
							if ($file == $filename) {
								$index--;
							}
						}

						$javascript_files[$index] = $filename;
						$index++;

						//Dateiinhalt von externem Javascript in globales Skript schreiben
						$javascript_complete .= "\r\n" . @file_get_contents($filename);
					}
					else {
						$embedding = str_replace("<!--", "", $embedding);
						$embedding = str_replace("-->", "", $embedding);
						$embedding = str_replace('<![CDATA[', "", $embedding);
						$embedding = str_replace(']]>', "", $embedding);
						$embedding_array = explode('>', $embedding);

						//$embedded_script = preg_replace('|(.*)>(.*)|Us', '', $embedding);
						$embedded_script = str_replace($embedding_array[ '0' ] . ">", "", $embedding);
						//eingebundenes (inline-)Javascript in globales Skript schreiben

						$javascript_complete .= "\r\n" . $embedded_script;
					}
					$index++;
				}
			}
		}


		if ($db_result[ 0 ]->js_compress == "1") {
			require_once('Minifier.php');
			if ($db_result[ 0 ]->js_remove_comments == "1") {
				$javascript_complete = \JShrink\Minifier::minify($javascript_complete, array('flaggedComments' => false));
			}
			else {
				$javascript_complete = \JShrink\Minifier::minify($javascript_complete, array('flaggedComments' => true));
			}
		}
		//Master JS-Datei schreiben
		file_put_contents(PAPOO_ABS_PFAD . "/templates_c/master.js", $javascript_complete);

		//Liste von auszublendendem Javascript übergeben
		IfNotSetNull($to_delete);
		return $to_delete;
	}

	/**
	 * skriptor_class::loesch_extra_files()
	 *
	 * @param mixed $files
	 * @return array|null
	 */
	function loesch_extra_files($files)
	{
		global $menu;
		if (is_array($files)) {
			foreach ($files as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						$file[$value2] = basename($value2);
					}
				}
			}
		}
		if (is_array($menu->data_front_complete)) {
			foreach ($menu->data_front_complete as $key => $value) {
				if (!empty($value['extra_css_file'])) {
					$extra[] = $value['extra_css_file'];
				}
			}
		}
		if (!isset($extra) || !is_array($extra)) {
			$extra = array();
		}

		if (isset($file) && is_array($file)) {
			foreach ($file as $key => $value) {
				if (!in_array($value, $extra)) {
					if (!stristr($key, "print")) {
						$return[] = $key;
					}
				}
				else {
					$this->not_delete[] = ($key);
				}
			}
		}
		IfNotSetNull($return);
		return $return;
	}

	/**
	 * skriptor_class::comp_html()
	 *
	 * @param mixed $output
	 * @param mixed $var
	 * @return mixed
	 */
	function comp_html($output, $var)
	{
		if ($var == 1) {
			$output = str_replace(array(
				"\t"
			), ' ', $output);
			$output = str_replace(array(
				'    ', '    '
			), ' ', $output);
		}
		if ($var == 2) {
			$output = str_replace(array(
				"\r\n", "\r", "\n", "\t",
				'  ', '    ', '    '
			), ' ', $output);

			$output = str_replace(array(
				'document.write'
			), "\n" . "document.write", $output);
			$output = str_replace(array(
				"like-box>');"
			), "like-box>');" . "\n", $output);
			$output = str_replace(array(
				"like>');"
			), "like>');" . "\n", $output);
			$output = str_replace(array(
				'mp;'
			), 'mp##' . "", $output);
			$output = str_replace(array(
				';'
			), ';' . "\n", $output);
			$output = str_replace(array(
				'<!--'
			), '<!--' . "\n", $output);
			$output = str_replace(array(
				'mp##'
			), 'mp;' . "", $output);
			$output = str_replace(array(
				' var map'
			), "\n" . ' var map', $output);
		}
		return $output;
	}

	/**
	 * skriptor_class::output_filter()
	 *
	 * @return void
	 * @throws Exception
	 */
	function output_filter()
	{
		$db_result = $this->read_from_db();
		global $output;

		global $diverse;
		if ($diverse->no_output!="no") {
			//CSS
			if ($db_result[0]->css_active == "1") {
				//Unterverzeichnis in /styles herraussuchen, welches aktiv ist
				preg_match_all('|@import url\(styles/(.*)/css/_index.css\);|Us', $output, $result);
				IfNotSetNull($result[1][0]);
				$css_dir = PAPOO_WEB_PFAD . '/styles/' . $result[1][0] . '/css';

				//Liste aller CSS-Dateien, die eingebunden werden, erstellen.
				$css_files = $this->get_css_files($output);

				$css_files[] = $this->loesch_extra_files($css_files);
				//CSS-Dateien der ersten Ebene suchen und ausblenden.
				$output = str_replace($this->del_stylesheets($output), '', $output);

				//aus den gefundenen CSS-Dateien eine master.css generieren und diese Einbinden.
				$this->replace_css_files($css_files, $css_dir, $db_result);
			}

			//JS
			if ($db_result[0]->js_active == "1") {
				$output = str_replace($this->replace_javascripts($db_result), '', $output);

				//Master-JS-Datei in output einbinden
				$einbindung = '<script type="text/javascript" src="' . PAPOO_WEB_PFAD . '/templates_c/master.js"></script>';
				$output = str_replace("</head>", $einbindung . "</head>", $output);
			}

			//Nur Tabs und Leerzeichen
			if ($db_result[0]->site_speed_plugin_leerzeichen_tabs == "1" && $db_result[0]->site_speed_plugin_komplet_html != 1) {
				$output = $this->comp_html($output, 1);
			}
			//Alles
			if ($db_result[0]->site_speed_plugin_komplet_html == "1") {
				$output = $this->comp_html($output, 2);
			}
			if ($db_result[0]->site_speed_plugin_bilder_komprimieren == "1") {
				$output = $this->comp_images($output);
			}
		}
	}
}

$skriptor = new skriptor_class();
