<?php

/**
 * Class galerieintegration
 */
class galerieintegration
{
	/**
	 * galerieintegration constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if ( !defined("admin")  || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if ( strstr( $output, "#barack_gal" ) ) {
				//Ausgabe erstellen
				$output = $this->create_galerieintegration( $output );
			}
		}
	}

	/**
	 * galerieintegration::set_backend_message()
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Galerie Barack Slideshow an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = 'Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Galerie im Layout der Barack Slideshow ausgeben lassen, die Syntax lautet.<br /><strong>#barack_gal_1_600x240#</strong><br />Wobei Sie mit der ersten Ziffer die ID der Galerie bezeichnen, danach folgen Breite x Höhe. ';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * content_manipulator::get_galerie_und_erzeuge_html()
	 * Diese Funktion holt die Daten aus dem Text raus und erzeugt
	 * anhand der ids die Barack Galerie mit lightbox links
	 *
	 * @param string $inhalt
	 * @return void
	 */
	function create_galerieintegration( $inhalt = "" )
	{
		$galpfad = PAPOO_WEB_PFAD . "/plugins/galerie/";
		$galerie_html_top_einbindung_js_css =
			'<script type="text/javascript" charset="utf-8" src="' . $galpfad;
		$galerie_html_top_einbindung_js_css .=
			'/js/mootools-1.2-core.js"></script><script type="text/javascript" charset="utf-8" src="';
		$galerie_html_top_einbindung_js_css .= $galpfad .
			'/js/mootools-1.2-more.js"></script><script type="text/javascript" charset="utf-8" src="';
		$galerie_html_top_einbindung_js_css .= $galpfad .
			'/js/morphlist.js"></script><script type="text/javascript" charset="utf-8" src="' .
			$galpfad . '/js/barackslideshow.js"></script>';
		preg_match_all( "|#barack_gal(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ( $ausgabe['1'] as $dat ) {
			$ndat = explode( "_", $dat );
			$xdat = explode( "x", $ndat['2'] );
			$galerie_daten = $this->get_gal_aus_plugin( $ndat['1'], $xdat['0'], $xdat['1'] );
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$i++;
		}
		$inhalt = str_replace( "</head>", $galerie_html_top_einbindung_js_css .
			"</head>", $inhalt );
		$inhalt = $inhalt;
		return $inhalt;
	}

	/**
	 * galerieintegration::get_gal_aus_plugin()
	 *
	 * @param integer $galid
	 * @param integer $width
	 * @param integer $height
	 * @return string|void
	 */
	function get_gal_aus_plugin( $galid = 1, $width = 0, $height = 0 )
	{
		global $galerie;
		global $diverse;
		$imgwidth = $width * 0.8;
		$imgheight = $height * 0.8;
		// Liste der Bilder dieser Galerie holen
		if ( is_object( $galerie ) ) {
			$liste = $galerie->bilder_liste( $galid, "" );
			$css = implode( file( PAPOO_ABS_PFAD .
				"/plugins/galerie/css/barackslideshow.css" ) );
			if (!empty($liste)) {
				// Durchgehen und HTML erzeuge
				foreach ( $liste as $bild ) {
					// Höhe und Breite einstellen
					if ( $bild['bild_breite'] >= $width ) {
						$imgwidth = $width;
						$faktor = $width / $bild['bild_breite'];
						$imgheight = $bild['bild_hoehe'] * $faktor;
					}
					else {
						$imgwidth = $bild['bild_breite'];
						$imgheight = $bild['bild_hoehe'];
					}
					if ( $imgheight >= $height ) {
						$imgheight = $height;
						$faktor = $height / $bild['bild_hoehe'];
						$imgwidth = $bild['bild_breite'] * $faktor;
					}
					if ( $imgwidth < $width ) {
						$imgwidth = $width - 166;
						$faktor = $bild['bild_breite'] / $width;
						$imgheight = $bild['bild_hoehe'] / $faktor;
					}
					$bildlang_beschreibung = htmlentities( strip_tags( $diverse->encode_quote( $bild['bildlang_beschreibung'] ) ),
						ENT_QUOTES, 'UTF-8' );
					//HTML erzeugen
					$pictures[] = '<li><a rel="lightbox" href="' . PAPOO_WEB_PFAD .
						'/plugins/galerie/galerien/' . $bild['gal_verzeichnis'] . '/' . $bild['bild_datei'] .
						'"><img src="' . PAPOO_WEB_PFAD . '/plugins/galerie/galerien/' . $bild['gal_verzeichnis'] .
						'/' . $bild['bild_datei'] . '" alt="' . $bildlang_beschreibung . '" title="' . $bildlang_beschreibung .
						'" width="' . $imgwidth . '" height="' . $imgheight . '" /></a></li>';

					$menu[] = '<li><a href="' . PAPOO_WEB_PFAD . '/plugins/galerie/galerien/' . $bild['gal_verzeichnis'] .
						'/' . $bild['bild_datei'] . '">' . ( substr( $bild['bildlang_name'], 0, 20 ) ) .
						'</a></li>';
				}

				$css = str_replace( "#loading", "#loading_" . $galid, $css );
				$css = str_replace( "#slideshow", "#slideshow_" . $galid, $css );
				$css = str_replace( "#pictures", "#pictures_" . $galid, $css );
				$css = str_replace( "#menu", "#galmenu_" . $galid, $css );
				//url('plugins
				$css = str_replace( 'url(\'plugins', "url('" . PAPOO_WEB_PFAD . "/plugins", $css );
				$css .= "#slideshow_" . $galid . " {width:" . $width . "px;height:" . $height .
					"px;}";
				//Blöcke erzeugen mit den Bildern
				$html = '<style type="text/css">' . $css . '</style>
	<script type="text/javascript" charset="utf-8">window.addEvent(\'domready\', function() {new BarackSlideshow(\'galmenu_' .
					$galid . '\', \'pictures_' . $galid . '\', \'loading_' . $galid . '\');});</script><div id="slideshow_' .
					$galid . '"><span id="loading_' . $galid . '">Loading</span><ul id="pictures_' .
					$galid . '">';
				foreach ( $pictures as $pic ) {
					$html .= $pic;
				}
				$html .= "</ul>";
				//Liste mit den Links
				$html .= '<ul id="galmenu_' . $galid . '">';
				foreach ( $menu as $men ) {
					$html .= $men;
				}
				$html .= "</ul></div>";
				return $html;
			}
		}
	}
}

$galerieintegration = new galerieintegration();
