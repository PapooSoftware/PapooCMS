<?php

/**
 * Class class_create_site_facebook
 */
class class_create_site_facebook
{
	/**
	 * class_create_site_facebook constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $pluginintegrator, $diverse, $db_abs, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->pluginintegrator = &$pluginintegrator;
		$this->diverse = &$diverse;
		$this->db_abs = &$db_abs;
		$this->db = &$db;

		#$this->create_page();
	}

	/**
	 * class_create_site_facebook::create_page()
	 *
	 * @param mixed $id
	 * @return void
	 */
	function create_page($id)
	{
		//Daten des Eintrages rausholen
		$sql = sprintf( "SELECT * FROM %s WHERE kalender_id='%d'",
			$this->cms->tbname['plugin_facebook_page_sites'],
			$this->db->escape( $id )
		);
		$result = $this->db->get_results( $sql, ARRAY_A );

		//Die FE Startseite im gew�hlten Design rausholen
		$this->get_data_frontend(  );

		//Indexhtml erstellen
		$this->index_html = $this->kopf . $result['0']['kalender_content'] . $this->fuss;

		//Outpufilter dr�berlaufen lassen
		global $output;
		$ausgabe = $output;
		$output = $this->index_html;

		$_GET['is_lp'] = 1;

		$pluginintegrator = new pluginintegrator_class();
		$pluginintegrator->output_filter();
		$this->index_html = $output;
		$output = $ausgabe;

		//Verzeichnis erstellen
		$this->create_verzeichniss( $result );

		//Pfade in der index korrigieren
		$this->do_correct_pfade( $result );

		//index.html speichern $this->verzeichnis
		$this->diverse->write_to_file( "/plugins/facebook_pages/sites/" . $this->
			verzeichnis . "/index.html", $this->index_html );
	}

	/**
	 * @param array $result
	 */
	function do_correct_pfade($result = array() )
	{
		//Styles ersetzen
		$this->index_html = preg_replace( '/styles(.*?)\/css/', "./css", $this->
		index_html );

		//Bilderpfade ersetzen
		if ( !empty( $this->cms->webverzeichnis ) ) {
			$this->index_html = preg_replace( '|' . $this->cms->webverzeichnis . '|', ".", $this->
			index_html );
		}

		preg_match_all( '/<img[^>]*>/Ui', $this->index_html, $img );

		//Dann die vorhandenen Bilder auslesen

		//Bilder kopieren in Verzeichnis /images
		if ( is_array( $img ) ) {
			foreach ( $img['0'] as $key => $value_img ) {
				preg_match_all( "/img(.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $value_img, $bilder );

				#echo $bilder['2']['0'];
				$value = str_replace( $this->cms->webverzeichnis, "..", $bilder['2']['0'] );
				$value = str_replace( "..", "", $value );
				$value = str_replace( "./", "/", $value );
				$value = str_replace( "thumbs/", "", $value );
				$value = str_replace( "bilder/", "/bilder/", $value );
				$value2 = str_replace( "/images/", "", $value );
				$value2 = str_replace( "bilder/", "", $value2 );

				$v_ar = explode( "/", $value2 );
				$pop = array_pop( $v_ar );

				if ( !strstr( $bilder['2']['0'], "http" ) ) {
					$this->index_html = str_replace( "src=\"" . $bilder['2']['0'], "src=\"./images/" .
						$pop, $this->index_html );
					$this->index_html = str_replace( "href=\"" . $bilder['2']['0'], "href=\"./images/" .
						$pop, $this->index_html );
				}
			}
		}

		//Nofollow Links und anderes l�schen
		$this->index_html = preg_replace( '/<a rel="nofollow"(.*?)<\/a>/i', "", $this->
		index_html );
		$this->index_html = preg_replace( '/<link rel=\'alternate\' (.*?) \/>/i', "", $this->
		index_html );
		//name="Page-topic"
		$this->index_html = preg_replace( '/<meta name="Page-topic"[^>]*>/Ui', "", $this->
		index_html );
		$this->index_html = preg_replace( '/<meta name="description"[^>]*>/Ui', "", $this->
		index_html );
		$this->index_html = preg_replace( '/<meta name="keywords" (.*?) \/>/i', "", $this->
		index_html );
		$this->index_html = preg_replace( '/<title>(.*?)<\/title>/i', "", $this->
		index_html );

		//CSS Pfad
		$this->index_html = preg_replace( '/templates_c/i', "./css", $this->index_html );
		$anker='<a href="#artikel" accesskey="8" >direkt zum Inhalt</a><span class="ignore">. </span>';
		$this->index_html = str_replace( $anker, '', $this->index_html );

		//Links auf die Hauptdomain setzen
		$seite='http://' . $this->cms->title_send . $this->cms->webverzeichnis . "/";

		$this->index_html = preg_replace( '/href="(.*?)\//i', 'href="'.$seite."", $this->index_html );

		#$seite=str_replace("/","\/",$seite);
		#$seite=str_replace(".","\.",$seite);

		$this->index_html = str_replace( 'href="'.$seite.'/www', 'href="http://www', $this->index_html );

		//ga code $result['0']['kalender_gacode']
		$this->index_html = preg_replace( '/\("UA(.*?)"\)/', '("' . $result['0']['kalender_gacode'] .
			'")', $this->index_html );

		#preg_match_all( "/script (.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $this->index_html, $js );
		preg_match_all( '/<script[^>]*>/Ui', $this->index_html, $js );

		if ( is_array( $js['0'] ) ) {
			foreach ( $js['0'] as $key => $value ) {
				preg_match_all( "/script (.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $value, $js2 );

				if ( is_array( $js2 ) ) {
					foreach ( $js2['2'] as $key2 => $value2 ) {
						$val_ar = explode( "/", $value2 );
						$last = array_pop( $val_ar );
						$this->index_html = str_replace( $value2, "./js/" . $last, $this->index_html );
					}
				}
			}
		}

		$this->index_html = preg_replace( '|../images|', "./images", $this->index_html );
		$this->index_html = preg_replace( '|bilder|', './images', $this->index_html );
		$this->index_html = preg_replace( '|src=./images|', 'src="./images', $this->
		index_html );
		$this->index_html = preg_replace( '|src./images|', 'src="./images', $this->
		index_html );

		$this->index_html = str_replace( "/./", "./", $this->index_html );
		$this->index_html = str_replace( "../js", "./js", $this->index_html );
		$this->index_html = str_replace( "./c.", "", $this->index_html );
		$this->index_html = str_replace( "://", "###", $this->index_html );
		$this->index_html = str_replace( "//", "/", $this->index_html );
		$this->index_html = str_replace( "###", "://", $this->index_html );
		// rel="./css" 
		$this->index_html = str_replace( "rel=\"./css\"", ' rel="stylesheet" type="text/css" ', $this->index_html );
		//href="http://www.papoo.de/plugins/content_manipulator/scripts/gg_gallery/gg-gallery
		$this->index_html = str_replace( 'href="http://www.papoo.de/plugins/content_manipulator/scripts/gg_gallery/gg-gallery', ' href="./css', $this->index_html );

		$meta='<title>'.$result['0']['kalender_titel_der_seite'].'</title>
			<meta name="description" content="'.$result['0']['kalender_meta_description'].'" />
			<meta name="keywords" content="'.$result['0']['kalender_meta_keywords'].'" />';

		$this->index_html = str_replace( "<head>", "<head>".$meta, $this->index_html );
	}


	/**
	 * class_create_site::get_data_frontend()
	 *
	 * @param integer $style
	 * @return void
	 */
	function get_data_frontend( $style = 0 )
	{
		$this->kopf ='<!DOCTYPE HTML>
				<html lang="de"  dir="ltr">
				<head>
				<meta charset="UTF-8" />
				<!--
				Ihre barrierefreie Internetseite wurde erm�glicht durch
				das barrierefreie CMS Papoo. 
				Information  unter http://www.papoo.de
				-->
				<title>#title#</title>
				<meta name="Robots" content="INDEX,FOLLOW" />
				<link rel="stylesheet" href="styles/#style#/css/print.css" type="text/css" media="print" />

				<style type="text/css">
					@import url(templates_c/1339676157_plugins.css);
					@import url(styles/#style#/css/_index.css);
				</style>
				<!--[if IE 9]>
					<style type="text/css">@import url(styles/#style#/css/IEFixes_9_index.css);</style>
				<![endif]-->
				<!--[if IE 8]>
					<style type="text/css">@import url(styles/#style#/css/IEFixes_8_index.css);</style>
				<![endif]-->
				<!--[if IE 7]>
					<style type="text/css">@import url(styles/#style#/css/IEFixes_7_index.css);</style>
				<![endif]-->
				<!--[if lte IE 6]>
					<style type="text/css">@import url(styles/#style#/css/IEFixes_6_index.css);</style>
				<![endif]-->
				<!--[if lt IE 6]>
					<style type="text/css">@import url(styles/#style#/css/IEFixes_5_5_index.css);</style>
				<![endif]-->
				</head><body style="width:780px;height:780px;margin:0 auto;">
				';
		$this->fuss = '</body></html>';
	}

	/**
	 * class_create_site::create_verzeichniss()
	 *
	 * @param array $result
	 * @return void
	 */
	function create_verzeichniss( $result = array() )
	{
		//Zuerst aus dem Namen das Verzeichnis erstellen
		$file = str_replace( ".", "_", $result['0']['kalender_interne_bezeichnung'] );
		$file = str_replace( " ", "_", $file );
		$this->verzeichnis = $file = preg_replace( "/[^a-zA-Z0-9_]_/", "", $file );

		//Das gesamte vorhanden Verzeichnis l�schen
		global $diverse;
		$diverse->rec_rmdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file );

		//Wenn noch nicht vorhanden anlegen
		if ( !is_dir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file ) ) {
			mkdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file );
			@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file, 0777 );

			mkdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css" );
			@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css", 0777 );

			mkdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/js" );
			@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/js", 0777 );

			mkdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/images" );
			@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/images", 0777 );

			mkdir( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/images/thumbs" );
			@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/images/thumbs", 0777 );
		}

		//Dann die CSS Verzeichnisse kopieren in Verzeichnis /css
		$sql = sprintf( "SELECT * FROM %s WHERE style_id='%d' ", $this->db_praefix .
			"papoo_styles", $this->db->escape( $result['0']['kalender_design'] ) );
		$temp_return = $this->db->get_results( $sql, ARRAY_A );
		$src = PAPOO_ABS_PFAD . "/styles/" . $temp_return['0']['style_pfad'] . "/css";
		$dest = PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css/";
		$this->copydirr( $src, $dest );

		$src = PAPOO_ABS_PFAD . "/plugins/content_manipulator/scripts/gg_gallery/copy";

		$dest = PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css";
		$this->copydirr( $src, $dest );
		@copy( PAPOO_ABS_PFAD . "/loader.gif" , PAPOO_ABS_PFAD .
			"/plugins/facebook_pages/sites/" . $file . "/loader.gif"  );
		@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/loader.gif"
			, 0777 );


		preg_match_all( '/<img[^>]*>/Ui', $this->index_html, $img );

		//Dann die vorhandenen Bilder auslesen

		//Bilder kopieren in Verzeichnis /images
		if ( is_array( $img ) ) {
			foreach ( $img['0'] as $key => $value_img ) {
				preg_match_all( "/img(.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $value_img, $bilder );
				$value = str_replace( $this->cms->webverzeichnis, "..", $bilder['2']['0'] );
				$value = str_replace( "..", "", $value );
				$value = str_replace( "./", "/", $value );
				$value = str_replace( "thumbs/", "", $value );
				$value = str_replace( "bilder/", "/bilder/", $value );
				$value2 = str_replace( "/images/", "", $value );
				$value2 = str_replace( "bilder/", "", $value2 );

				$v_ar = explode( "/", $value2 );
				$pop = array_pop( $v_ar );

				@copy( PAPOO_ABS_PFAD . "" . $value, PAPOO_ABS_PFAD .
					"/plugins/facebook_pages/sites/" . $file . "/images/" . $pop );
				@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/images/" .
					$pop, 0777 );
			}
		}

		//dann die vorhandenen js Dateien auslesen
		preg_match_all( '/<script[^>]*>/Ui', $this->index_html, $js );

		if ( is_array( $js['0'] ) ) {
			foreach ( $js['0'] as $key => $value ) {
				preg_match_all( "/script (.*) +src=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $value, $js2 );

				if ( is_array( $js2 ) ) {
					foreach ( $js2['2'] as $key2 => $value2 ) {
						$value2 = str_replace( $this->cms->webverzeichnis, "..", $value2 );
						$value2 = str_replace( "..", "", $value2 );
						$value2 = str_replace( "./", "/", $value2 );
						#$value=str_replace("js/","",$value);
						$v_ar = explode( "/", $value2 );
						$pop = array_pop( $v_ar );

						@copy( PAPOO_ABS_PFAD . "" . $value2, PAPOO_ABS_PFAD .
							"/plugins/facebook_pages/sites/" . $file . "/js/" . $pop );
						if ( file_exists( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file .
							"/js/" . $pop ) ) {
							@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/js/" . $pop,
								0777 );
						}
					}
				}
			}
		}

		//Dann die zus�tzlich CSS Dateien auslesen
		preg_match_all( "/link (.*) +href=[\"' ]?([^\"' >]+)[\"' ]?[^>]*>/i", $this->
		index_html, $css );

		//und kopieren
		if ( is_array( $css ) ) {
			foreach ( $css['2'] as $key => $value ) {
				if ( stristr( $value, "css" ) ) {
					$value = str_replace( $this->cms->webverzeichnis, "..", $value );
					$value = str_replace( "..", "", $value );
					$value = str_replace( "./", "/", $value );
					$v_ar = explode( "/", $value );
					$pop = array_pop( $v_ar );
					@copy( PAPOO_ABS_PFAD . "/" . $value, PAPOO_ABS_PFAD .
						"/plugins/facebook_pages/sites/" . $file . "/css/" . $pop );
					if ( file_exists( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file .
						"/css/" . $pop ) )
					{
						@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css/" . $pop,
							0777 );
					}
				}
			}
		}

		preg_match_all( "/import url\((.*?)\)/", $this->index_html, $css2 );
		if ( is_array( $css2 ) ) {
			foreach ( $css2['0'] as $key => $value ) {
				if ( stristr( $value, "templates_c" ) ) {
					$value = str_replace( "import url(", "", $value );
					$value = str_replace( ")", "", $value );
					$value = str_replace( $this->cms->webverzeichnis, "..", $value );
					$value = str_replace( "..", "", $value );
					$value = str_replace( "./", "/", $value );
					$v_ar = explode( "/", $value );

					@copy( PAPOO_ABS_PFAD . "/" . $value, PAPOO_ABS_PFAD .
						"/plugins/facebook_pages/sites/" . $file . "/css/" . array_pop( $v_ar ) );

					@chmod( PAPOO_ABS_PFAD . "/plugins/facebook_pages/sites/" . $file . "/css/" .
						array_pop( $v_ar ), 0777 );
				}
			}
		}
	}

	/**
	 * class_create_site::copydirr()
	 *
	 * @param mixed $srcdir
	 * @param mixed $dstdir
	 * @param bool $verbose
	 * @return int
	 */
	function copydirr($srcdir, $dstdir, $verbose = false) {
		$num = 0;
		if(!is_dir($dstdir)) @mkdir($dstdir);
		if($curdir = @opendir($srcdir)) {
			while($file = @readdir($curdir)) {
				if($file != '.' && $file != '..') {
					$srcfile = $srcdir . '/' . $file;
					$dstfile = $dstdir . '/' . $file;
					if(is_file($srcfile)) {
						if(is_file($dstfile)) $ow = filemtime($srcfile) - filemtime($dstfile); else $ow = 1;
						if($ow > 0) {
							if($verbose) echo "Copying '$srcfile' to '$dstfile'...";
							if(copy($srcfile, $dstfile)) {
								chmod( $dstfile, 0777 );
								touch($dstfile, filemtime($srcfile)); $num++;
								if($verbose) echo "OK\n";
							}
							else {
								echo "Error: File '$srcfile' could not be copied!\n";
							}
						}
					}
					else if(is_dir($srcfile)) {
						$num += $this->copydirr($srcfile, $dstfile, $verbose);
					}
				}
			}
			closedir($curdir);
		}
		return $num;
	}
}

$class_create_site_facebook = new class_create_site_facebook();
