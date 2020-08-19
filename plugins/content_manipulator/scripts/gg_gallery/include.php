<?php

/**
 * Class adgalerieintegration
 */
class adgalerieintegration
{
	/**
	 * adgalerieintegration constructor.
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
			if ( strstr( $output, "#gg_gal" ) ) {
				//Ausgabe erstellen
				$output = $this->create_adgalerieintegration( $output );
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Galerie gg-Gallery Slideshow an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = 'Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Galerie im Layout der AD-Gallery Slideshow ausgeben lassen, die Syntax lautet.<br /><strong>#gg_gal_1_600x240#</strong><br />Wobei Sie mit der ersten Ziffer die ID der Galerie bezeichnen, danach folgen Breite x Höhe. ';
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
	function create_adgalerieintegration( $inhalt = "" )
	{
		$galpfad = PAPOO_WEB_PFAD . "/plugins/content_manipulator/scripts/gg_gallery/gg-gallery/";
		$galerie_html_top_einbindung_js_css =
			'<link rel="stylesheet" type="text/css" href="'.$galpfad.'jquery.gg-gallery.css" />
			<script type="text/javascript" charset="utf-8" src="' . $galpfad;
		$galerie_html_top_einbindung_js_css .='jquery.gg-gallery.js"></script>
		<script type="text/javascript">
  $(function() {
   
    var galleries = $(\'.gg-gallery\').adGallery();
    $(\'#switch-effect\').change(
      function() {
        galleries[0].settings.effect = $(this).val();
        return false;
      }
    );
    $(\'#toggle-slideshow\').click(
      function() {
        galleries[0].slideshow.toggle();
        return false;
      }
    );
  });
  </script>
		';

		preg_match_all( "|#gg_gal(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ( $ausgabe['1'] as $dat ) {
			$ndat = explode( "_", $dat );
			$xdat = explode( "x", $ndat['2'] );
			$galerie_daten = $this->get_gal_aus_plugin( $ndat['1'], $xdat['0'], $xdat['1'] );
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$i++;
		}
		$inhalt = str_replace( "</head>", $galerie_html_top_einbindung_js_css .$this->fuer_kopf. "</head>", $inhalt );
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

		$imgwidth = $width * 0.8;
		$imgheight = $height * 0.8;
		$galpfad=PAPOO_WEB_PFAD."/plugins/galerie/galerien/";
		// Liste der Bilder dieser Galerie holen
		if ( is_object( $galerie ) ) {
			$liste = $galerie->bilder_liste( $galid, "" );
			$css = implode( file( PAPOO_ABS_PFAD .
				"/plugins/galerie/css/barackslideshow.css" ) );
			if (!empty($liste)) {
				$html = "";
				$this->fuer_kopf='<style type="text/css">
		.gg-gallery {
		  width: '.$width.'px;
		}
		.gg-gallery .gg-image-wrapper {
		    height: '.$height.'px;
		  }
			</style>';
				$html.='
				
		<div id="gallery" class="gg-gallery">
      <div class="gg-image-wrapper">
      </div>
      <div class="gg-controls">
      </div>
      <div class="gg-nav">
        <div class="gg-thumbs">
          <ul class="gg-thumb-list">';
				// Durchgehen und HTML erzeuge
				foreach ( $liste as $bild ) {
					$bildlang_beschreibung = htmlentities(( ( $bild['bildlang_beschreibung'] ) ),ENT_QUOTES, 'UTF-8' );
					$bildname= htmlentities( strip_tags( $diverse->encode_quote( $bild['bildlang_name'] ) ),ENT_QUOTES, 'UTF-8' );

					//HTML erzeugen
					$html.='<li>
              <a  href="'.$galpfad.$bild['gal_verzeichnis']."/".$bild['bild_datei'].'">
                <img src="'.$galpfad.$bild['gal_verzeichnis']."/thumbs/".$bild['bild_datei'].'" title="'.$bildname.'" alt="'.$bildname.'" longdesc="'.$bildlang_beschreibung.'" />
              </a>
            </li>';

				}
				$html.="</ul>
        </div>
      </div>

    </div>";
				return $html;
			}
		}
	}
}

$adgalerieintegration = new adgalerieintegration();
