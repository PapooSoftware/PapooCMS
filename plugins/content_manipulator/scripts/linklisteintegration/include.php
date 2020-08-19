<?php

/**
 * Class linklisteintegration
 */
class linklisteintegration
{
	/**
	 * linklisteintegration constructor.
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
		if ( !defined("admin") || ($_GET['is_lp']==1) ) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if ( strstr( $output, "#linkliste" ) ) {
				//Ausgabe erstellen
				$output = $this->create_linklisteintegration( $output );
			}

			if ( strstr( $output, "#listelinks" ) ) {
				//Ausgabe erstellen
				$output = $this->create_linklisteintegration_text( $output );
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Galerie der Linkliste an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = 'Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Linkliste , bzw. nur die Bilder dazu, ausgeben lassen, die Syntax lautet.<br /><strong>#linkliste_kat_1_600x240#</strong><br />Wobei Sie mit der ersten Ziffer die ID der Kategorie bezeichnen, danach folgen Breite x Höhe. <br /><strong>#listelinks_kat_1_2#</strong><br />Gibt die Daten der Linkliste der Kategorie 1 so aus wie in der Linkliste, Bild, Text und Link in der Anzahl 2';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * Diese Funktion holt die Daten aus dem Text raus und erzeugt
	 * anhand der ids die Barack Galerie mit lightbox links
	 *
	 * @param string $inhalt
	 *
	 * @return void
	 */
	function create_linklisteintegration( $inhalt = "" )
	{
		$galpfad = PAPOO_WEB_PFAD . "plugins/content_manipulator/scripts/linklisteintegration/";
		$galerie_html_top_einbindung_js_css =
			'<link rel="stylesheet" type="text/css" href="'.$galpfad.'skin.css" />';
		$galerie_html_top_einbindung_js_css .=
			'<script type="text/javascript" src="'.$galpfad.'jquery.jcarousel.min.js"></script>
			<script type="text/javascript">

jQuery(document).ready(function() {
jQuery(\'#mycarousel\').jcarousel({
    	wrap: \'circular\',
    	scroll:1
    });
});

</script>';
		preg_match_all( "|#linkliste_kat(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ( $ausgabe['1'] as $dat ) {
			$ndat = explode( "_", $dat );
			$xdat = explode( "x", $ndat['2'] );
			$galerie_daten = $this->get_gal_aus_plugin( $ndat['1'], $xdat['0'], $xdat['1'] );
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$i++;
		}
		$inhalt = str_replace( "</head>", $galerie_html_top_einbindung_js_css .$this->cssdaten.
			"</head>", $inhalt );
		$inhalt = $inhalt;
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_linklisteintegration_text($inhalt = "" )
	{

		preg_match_all( "|#listelinks_kat(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER );
		$i = 0;
		foreach ( $ausgabe['1'] as $dat ) {
			$ndat = explode( "_", $dat );
			$galerie_daten = $this->get_gal_aus_plugin_text( $ndat['1'],$ndat['2']);
			$inhalt = str_ireplace( $ausgabe['0'][$i], $galerie_daten, $inhalt );
			$i++;
		}
		#	$inhalt = str_replace( "</head>", $galerie_html_top_einbindung_js_css .$this->cssdaten.
		#	"</head>", $inhalt );
		#$inhalt = $inhalt;
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
		global $linklisteplugin;
		global $diverse;
		$imgwidth = $width * 0.8;
		$imgheight = $height * 0.8;
		// Liste der Bilder dieser Galerie holen
		if ( is_object( $linklisteplugin ) ) {
			$liste = $linklisteplugin->get_link_list( $galid);
			$css = implode( file( PAPOO_ABS_PFAD .
				"plugins/content_manipulator/scripts/linklisteintegration/skin.css" ) );
			$css='.jcarousel-skin-tango .jcarousel-container-horizontal {
    width: '.$width.'px;
}

.jcarousel-skin-tango .jcarousel-container-vertical {
    width: '.$width.'px;
    height: '.$height.'px;
}

.jcarousel-skin-tango .jcarousel-clip-horizontal {
    width:  '.$width.'px;
    height: '.$height.'px;
}

.jcarousel-skin-tango .jcarousel-clip-vertical {
    width:  '.$width.'px;
    height: '.$height.'px;
}

.jcarousel-skin-tango .jcarousel-item {
    width: '.$height.'px;
    height: '.$height.'px;
    overflow:hidden;
}
.jcarousel-skin-tango .jcarousel-item img{
    width: '.$height*0.8.'px;
    height: '.$height*0.8.'px;
    overflow:hidden;
        	-moz-box-shadow:6px 8px 16px #323252;
	-webkit-box-shadow:6px 8px 16px  #323252;
	box-shadow:6px 8px 16px  #323252;
}';
			if (!empty($liste)) {
				// Durchgehen und HTML erzeuge
				foreach ( $liste as $bild ) {
					$bildlang_beschreibung = htmlentities( strip_tags( $diverse->encode_quote( $bild->linkliste_lang_header ) ),
						ENT_QUOTES, 'UTF-8' );
					//HTML erzeugen
					$pictures[] = '<li><img src="' . PAPOO_WEB_PFAD . 'images/' . $bild->paket_logo . '" alt="' . $bildlang_beschreibung . '" title="' . $bildlang_beschreibung .
						'"/></li>';
				}

				//Bl�cke erzeugen mit den Bildern
				$this->cssdaten='<style type="text/css">' . $css . '</style>';
				$html = '<ul id="mycarousel" class="jcarousel-skin-tango">';
				foreach ( $pictures as $pic ) {
					$html .= $pic;
				}
				$html .= "</ul>";
				//Liste mit den Links

				return $html;
			}
		}
	}

	/**
	 * @param int $galid
	 * @param int $count
	 *
	 * @return string|void
	 */
	function get_gal_aus_plugin_text($galid = 1 , $count=1)
	{
		global $linklisteplugin;
		global $diverse;


		// Liste der Bilder dieser Galerie holen
		if ( is_object( $linklisteplugin ) ) {

			$liste = $linklisteplugin->get_link_list( $galid,"rand");

			if (!empty($liste)) {
				$i=1;
				// Durchgehen und HTML erzeuge
				foreach ( $liste as $bild ) {

					if ($i>$count) {
						continue;
					}

					if($i % 2 == 0) {
						// Zahl ist gerade
						$col="#fff";
					}
					else {
						// Zahl ist ungerade
						$col="#fff";
					}

					$data='<div class="list_entry" style="background:'.$col.'">';
					$bildlang_beschreibung = substr(htmlentities( strip_tags( $diverse->encode_quote( $bild->linkliste_descrip )),
							ENT_QUOTES, 'UTF-8' ),0,100)."...";
					$header=htmlentities( strip_tags($bild->linkliste_lang_header),
						ENT_QUOTES, 'UTF-8');
					$url=$bild->linkliste_link_lang;
					//HTML erzeugen
					$data.="<h4>".$header."</h4>";
					$data.= '<a href="#" onclick="window.open(\''.$url.'\',\''.$header.'\' )" ><img src="' . PAPOO_WEB_PFAD . '/images/' . $bild->paket_logo . '" alt="' . $header . '" title="' . $header .
						'"/></a><p>'.$bildlang_beschreibung.'</p></div>';

					$pictures[]=$data;
					$i++;
				}

				$html=implode(" ",$pictures);
				//Bl�cke erzeugen mit den Bildern
				#$this->cssdaten='<style type="text/css">' . $css . '</style>';
				#$html = '<ul id="mycarousel" class="jcarousel-skin-tango">';
				#foreach ( $pictures as $pic )
				#{
				#	$html .= $pic;
				#}
				#$html .= "</ul>";
				//Liste mit den Links

				return $html;
			}
		}
	}
}

$linklisteintegration = new linklisteintegration();
