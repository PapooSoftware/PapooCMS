<?php

/**
 * Class imageflow_galerieintegration
 */
class imageflow_galerieintegration
{
	/**
	 * imageflow_galerieintegration constructor.
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
			if ( strstr( $output, "#flow_gal" ) ) {
				//Ausgabe erstellen
				$output = $this->create_galerieintegration( $output );
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Galerie Cicle Slideshow an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = 'Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Galerie als ImageFlow Slideshow ausgeben lassen, die Syntax lautet.<br /><strong>#flow_gal_1#</strong><br />Wobei Sie mit der ersten Ziffer die ID der Galerie bezeichnen.<br /><strong>Beachten Sie die Lizenzbedinungen dieses Skriptes für kommerzielle Webseiten: <a href="http://finnrudolph.de/ImageFlow/Download">http://finnrudolph.de/ImageFlow/Download</a></strong>';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Diese Funktion holt die Daten aus dem Text raus und erzeugt
	 * anhand der ids die Barack Galerie mit lightbox links
	 *
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_galerieintegration( $inhalt = "" )
	{
		$galerie_html_top_einbindung_js_css ='<link rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/plugins/content_manipulator/scripts/imageflow/js/imageflow.packed.css" type="text/css" />';
		$galerie_html_top_einbindung_js_css .=
			'<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/content_manipulator/scripts/imageflow/js/imageflow.packed.js"></script> <script type="text/javascript">'."domReady(function()
{
	var instanceOne = new ImageFlow();
	instanceOne.init({ ImageFlowID: 'myImageFlow2',
	reflectPath: '".PAPOO_WEB_PFAD."/plugins/content_manipulator/scripts/imageflow/',
  imagePath:'../../../../../',
	slideshow: true,
  slideshowSpeed: 3000,
  circular: true,
  slider: false,
  imageFocusM:2,
  slideshowAutoplay: true
	 });
});
</script>
";
		preg_match_all( "|#flow_gal(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode( "_", $dat );
			$galerie_daten = $this->get_gal_aus_plugin($ndat['1']);
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$i++;
		}
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

		//$imgwidth = is_numeric($width) ? $width * 0.8 : 0 * 0.8;
		//$imgheight = $height * 0.8;

		$galpfad=PAPOO_WEB_PFAD."/plugins/galerie/galerien/";
		// Liste der Bilder dieser Galerie holen
		if ( is_object( $galerie ) ) {
			$liste = $galerie->bilder_liste( $galid, "" );

			if (!empty($liste)) {
				$html = "";
				$html.='
				
		<div id="myImageFlow2" class="imageflow2">
      ';
				// Durchgehen und HTML erzeuge
				foreach ( $liste as $bild ) {

					$bildlang_beschreibung = htmlentities(( ( $bild['bildlang_beschreibung'] ) ),ENT_QUOTES, 'UTF-8' );
					$bildname= htmlentities( strip_tags( $diverse->encode_quote( $bild['bildlang_name'] ) ),ENT_QUOTES, 'UTF-8' );

					//HTML erzeugen
					$html.='
                <img src="'.$galpfad.$bild['gal_verzeichnis']."/".$bild['bild_datei'].'" title="'.$bildname.'" alt="'.$bildname.'" longdesc="'.$bildlang_beschreibung.'" />
              ';
				}
				$html.="</ul>
       

    </div>";
				return $html;
			}
		}
	}
}

$imageflow_galerieintegration = new imageflow_galerieintegration();
