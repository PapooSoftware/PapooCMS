<?php

/**
 * Class galerie_ul
 */
class galerie_ul
{
	/**
	 * galerie_ul constructor.
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
			if (strstr($output, "#galerie_ul")) {
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
		$this->content->template['plugin_cm_head']['de'][] = "Galerie ul";
		$this->content->template['plugin_cm_body']['de'][] =
			'<p>Dieses Skritp gibt eine Bildergalerie in der kanonischen HTML Syntax aus. Es wird eine ungeordnete Liste erzeugt, deren Elemente die Thumbnails mit Links auf die Bilder sind. Die Syntax lautet: <strong>#galerie_ul_ID#</strong>, wobei <strong>ID</strong> die ID der Bildergalerie ist.</p>';
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 * @return mixed|string|string[]|null
	 */
	function create_galerieintegration($inhalt = "")
	{
		preg_match_all("|#galerie_ul(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);

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
				$html = '<ul id="galerie-'.$galid.'" class="bildergalerie">';

				foreach ($liste as $bild) {
					$bildname = htmlentities(strip_tags($diverse->encode_quote($bild['bildlang_name'])), ENT_QUOTES, 'UTF-8');
					$bilddatei = $galpfad.$bild['gal_verzeichnis']."/".$bild['bild_datei'];
					$thumbdatei = $galpfad . $bild['gal_verzeichnis'] . "/thumbs/" . $bild['bild_datei'];

					$html .= '<li><a href="'.$bilddatei.'"><img src="' . $thumbdatei . '" title="' . $bildname . '" alt="' . $bildname . '" /></a></li>';
				}

				$html .= '</ul>';

				return $html;
			}
		}
	}
}

$galerie_ul = new galerie_ul();
