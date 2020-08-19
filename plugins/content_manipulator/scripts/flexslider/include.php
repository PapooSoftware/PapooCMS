<?php

/**
 * Class flexslider
 */
class flexslider
{
	/**
	 * flexslider constructor.
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
		if (!defined("admin") || ($_GET['is_lp'] == 1)) {
			//Fertige Seite einbinden
			global $output;

			//Zuerst check ob es auch vorkommt
			if (strstr($output, "#gal_flexslider")) {
				//Ausgabe erstellen
				$output = $this->create_galerieintegration($output);
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Galerie Flexslider";
		$this->content->template['plugin_cm_body']['de'][] = '<p>Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten eine bestimmte Galerie als <a target="_blank" href="http://flexslider.woothemes.com/">Flexslider</a> ausgeben lassen. Die Syntax lautet: <strong>#gal_flexslider_ID#</strong></strong>, wobei <strong>ID</strong> die ID der Bildergalerie ist.</p>';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_galerieintegration($inhalt = "")
	{
		preg_match_all("|#gal_flexslider(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);

		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			$galerie_daten = $this->get_gal_aus_plugin($ndat['1']);
			$inhalt = str_ireplace($ausgabe['0'][$i], $galerie_daten, $inhalt);
			$i++;
		}

		return $inhalt;
	}

	/**
	 * @param integer $galid
	 *
	 * @return string|void
	 */
	function get_gal_aus_plugin($galid)
	{
		if ($_GET['is_lp'] == 1) {
			$galerie = new galerie_class();
		}
		else {
			global $galerie;
		}

		global $diverse;

		$galpfad = PAPOO_WEB_PFAD . "/plugins/galerie/galerien/";

		// Liste der Bilder dieser Galerie holen
		if (is_object($galerie)) {
			$liste = $galerie->bilder_liste($galid, "");

			if (!empty($liste)) {
				$html = "<script src=\"../plugins/content_manipulator/scripts/flexslider/jquery.flexslider-min.js\"></script>
		<script>
				$(function(){
					$('.flexslider').flexslider({
						animation: \"fade\",
						slideshow: true,
						slideshowSpeed: 2500,
						animationDuration: 600,
						controlNav: true,
					})
				})
		</script>";
				$html .= '<div class="flexslider">';
				$html .= '<ul id="galerie-'.$galid.'" class="slides">';

				foreach ($liste as $bild) {
					$bildname = htmlentities(strip_tags($diverse->encode_quote($bild['bildlang_name'])), ENT_QUOTES, 'UTF-8');
					$bilddatei = $galpfad.$bild['gal_verzeichnis']."/".$bild['bild_datei'];
					$thumbdatei = $galpfad . $bild['gal_verzeichnis'] . "/thumbs/" . $bild['bild_datei'];

					$html .= '<li data-thumb="'.$thumbdatei.'">';
					$html .= '<img src="'.$bilddatei.'">';
					$html .= '</li>';
				}

				$html .= '</ul>';
				$html .= '</div>';

				return $html;
			}
		}
	}
}

$flexslider = new flexslider();
