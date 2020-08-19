<?php

/**
 * Class carousel_galerieintegration
 */
class carousel_galerieintegration
{
	/**
	 * carousel_galerieintegration constructor.
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
			if ( strstr( $output, "#carousel" ) ) {
				//Ausgabe erstellen
				$output = $this->create_carousel_galerieintegration( $output );
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Galerie Carousel Slideshow an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = 'Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Galerie als Carousel Slideshow ausgeben lassen, die Syntax lautet.<br /><strong>#carousel_1_2_t#</strong><br />Wobei Sie mit der ersten Ziffer die ID der Galerie bezeichnen, die zweite Ziffer bezeichnet die Anzahl der Bilder die auf einmal angezeigt werden sollen, t oder f bedeuten true oder false für automatischen Start. ';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Diese Funktion holt die Daten aus dem Text raus und erzeugt
	 * anhand der ids die Barack Galerie mit lightbox links
	 *
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_carousel_galerieintegration( $inhalt = "" )
	{
		$galpfad = PAPOO_WEB_PFAD . "/plugins/content_manipulator/scripts/carousel/js/";
		$galerie_html_top_einbindung_js_css = '<script type="text/javascript" charset="utf-8" src="' . $galpfad;
		$galerie_html_top_einbindung_js_css .= 'jquery.bxcarousel.min.js"></script> <style type="text/css">
.carousel_wrap {
	position: relative;
	clear: both;
}
.carousel_wrap .bx_wrap {
	margin-left: 65px;
}

.carousel_wrap .prev {
	position: absolute;
	top: 45px;
	outline: 0;
	left: 0;
}

.carousel_wrap .next {
	position: absolute;
	top:45px;
	left: 513px;
	outline: 0;
}
</style>';

		preg_match_all( "|#carousel(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ( $ausgabe['1'] as $dat ) {
			$ndat = explode( "_", $dat );
			$galerie_daten = $this->get_gal_aus_plugin($ndat['1']);
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$true="false";
			$controls="true";
			if ($ndat['3']=="t") {
				$true="true";
				$controls="false";
			}
			$galerie_html_top_einbindung_js_css .='
					<script type="text/javascript">
			 $(document).ready(function(){
				$(\'#carousel_'.$ndat['1'].'\').bxCarousel({
					display_num: '.$ndat['2'].',
					move: 2,
					speed: 600,
					auto: '.$true.',
					controls: '.$controls.',
					margin: 10,
					next_image: \''.$galpfad.'next.png\',
					prev_image: \''.$galpfad.'prev.png\', 
					auto_hover: true
				});
			});
			
			  </script>
		';
			$i++;
		}
		IfNotSetNull($this->fuer_kopf);
		$inhalt = str_replace( "</head>", $galerie_html_top_einbindung_js_css . "</head>", $inhalt );
		return $inhalt;
	}

	/**
	 * @param integer $galid
	 * @param integer $width
	 * @param integer $height
	 *
	 * @return string|void
	 */
	function get_gal_aus_plugin( $galid = 1, $width = 0, $height = 0 )
	{
		if ($_GET['is_lp']==1) {
			$galerie = new galerie_class();
		}
		else {
			global $galerie;
		}

		global $diverse;

		// $imgwidth = $width * 0.8;
		// $imgheight = $height * 0.8;
		$galpfad=PAPOO_WEB_PFAD."/plugins/galerie/galerien/";
		// Liste der Bilder dieser Galerie holen
		if ( is_object( $galerie ) ) {
			$html = "";
			$liste = $galerie->bilder_liste($galid, "");

			$html .= '
		<div class="carousel_wrap">
          <ul id="carousel_' . $galid . '">';
			// Durchgehen und HTML erzeuge
			if(is_array($liste)) {
				foreach($liste as $bild) {
					$bildlang_beschreibung = htmlentities((($bild['bildlang_beschreibung'])), ENT_QUOTES, 'UTF-8');
					$bildname = htmlentities(strip_tags($diverse->encode_quote($bild['bildlang_name'])), ENT_QUOTES, 'UTF-8');
					//HTML erzeugen
					$html .= '<li><img src="' . $galpfad . $bild['gal_verzeichnis'] . "/thumbs/" . $bild['bild_datei'] . '" title="' . $bildname . '" alt="' . $bildname . '" longdesc="' . $bildlang_beschreibung . '" /></li>';
				}
			}
			$html .= "</ul></div>";
			return $html;
		}
	}
}

$carousel_galerieintegration = new carousel_galerieintegration();
