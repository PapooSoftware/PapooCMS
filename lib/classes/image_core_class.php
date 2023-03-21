<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class image_core_class
 */
#[AllowDynamicProperties]
class image_core_class
{
	/** @var mixed valide Bild-Endungen (kleingeschrieben: gif, giff, jpg, jpeg, png) */
	public $valid_image_extensions;
	/** @var mixed Informationen des Bildes z.B. Name, Bild-Typ (JPG, GIF, PNG), Breite, Hoehe etc. */
	public $image_infos;
	/** @var string Server-Pfad zum Verzeichnis des Bildes */
	public $pfad_images;
	/** @var mixed Server-Pfad zum Verzeichnis des ThumbNails */
	public $pfad_thumbs;
	/** @var mixed Web-Pfad zum Verzeichnis des Bildes */
	public $pfad_images_web;
	/** @var mixed Web-Pfad zum Verzeichnis des ThumbNails */
	public $pfad_thumbs_web;
	/** @var array maximale Breite und Höhe der TumbNails (in Pixel) */
	public $tumbnail_max_groesse;
	/** @var int Art der Thumbnail-Skalierung (0: wie bisher, Proportionen erhaltend; 1: Thumbnail immer so gross wie max. Groesse, Überstehende Raender beschnitten) */
	public $thumbnail_scale;

	/** @var cms */
	public $cms;
	/** @var diverse_class */
	public $diverse;
	/** @var user_class */
	public $user;

	/**
	 * image_core_class constructor.
	 */
	function __construct()
	{
		global $cms, $diverse, $user;
		$this->cms = &$cms;
		$this->diverse = &$diverse;
		$this->user = &$user;

		$this->init();
	}

	/**
	 * Standard-Werte der verschiedenen Variablen setzen
	 *
	 * @return void
	 */
	function init()
	{
		// Public-Variablen initialisieren:
		$this->valid_image_extensions = array("gif", "giff", "jpg", "jpeg", "png", "svg");

		$this->image_infos = array();
		$this->pfad_images = PAPOO_ABS_PFAD . "/images/";
		$this->pfad_thumbs = PAPOO_ABS_PFAD . "/images/thumbs/";
		$this->pfad_images_web = PAPOO_WEB_PFAD . "/images/";
		$this->pfad_thumbs_web = PAPOO_WEB_PFAD . "/images/thumbs/";

		$groesse_thumbnail = is_object($this->cms) ? $this->cms->system_config_data['config_thumbnailgroesse'] : 0;
		$groesse_thumbnail_ar = explode("x", $groesse_thumbnail);

		if ($groesse_thumbnail_ar['0'] > 50 && $groesse_thumbnail_ar['1'] > 50) {
			$this->tumbnail_max_groesse = array(
				'breite' => $groesse_thumbnail_ar['0'],
				'hoehe' => $groesse_thumbnail_ar['1']
			);
		}
		else {
			$this->tumbnail_max_groesse = array('breite' => 120, 'hoehe' => 120);
		}
		$this->thumbnail_scale = 0;
	}

	/**
	 * Erzeugt ein Bild anhand der Information
	 *
	 * @param array|string $information Bei Array: erzeugt ein neues Bild mit der Größe breite X höhe
	 *                                  Bei String: erzeugt ein neues Bild aus der Datei $file vom Typ
	 *                                  $this->image_infos['type']
	 * @param string $type
	 *
	 * @see image_core_class::image_infos['type']
	 *
	 * @return bool|false|resource
	 */
	function image_create($information, $type = "")
	{
		$image = false;

		if (is_array($information) && !empty($information)) {
			if ($type == "GIF") {
				$image = imagecreate($information[0], $information[1]);
			}
			else {
				$image = imagecreatetruecolor($information[0], $information[1]);
			}
		}
		if (is_string($information) && !empty($information)) {
			switch ($this->image_infos['type']) {
			case "JPG":
				$image = imagecreatefromjpeg($information);
				break;

			case "GIF":
				$image = imagecreatefromgif($information);
				break;

			case "PNG":
				$image = imagecreatefrompng($information);
				break;
			}
		}
		return $image;
	}

	/**
	 * Läd die Informationen eines Bildes anhand von $information und stellt sie unter
	 * $this->image_infos[] zur Verfügung.
	 * $information = $_FILES['formular_feldname']:
	 * kopiert ein Übertragenes Bild in das Verzeichnis $this->pfad_images,
	 * wandelt den Namen in einen "sicheren" Namen um (keine Umlaute etc.) und
	 * versieht es mit dem Präfix "TMP_"
	 * $information = Dateiname (ohne Pfad):
	 * Läd das entsprechende Bild
	 *
	 * @param string $information
	 *
	 * @return bool|void
	 */
	function image_load($information)
	{
		// $_FILES[] wurde Übergeben. Kopie mit "sicherem Name" erstellen.
		if (is_array($information) && !empty($information)) {
			if (!$this->test_image_extension_valid($information['name'])) {
				return false;
			}

			$this->image_infos['name'] = $this->user->userid."-".$this->diverse->sicherer_dateiname($information['name']);
			$this->image_infos['bild_temp'] = $this->pfad_images."TMP_".$this->image_infos['name'];
			move_uploaded_file($information['tmp_name'], $this->image_infos['bild_temp']);
		}

		// Bildname wurde übergeben.
		if (is_string($information) && !empty($information)) {
			if (!$this->test_image_extension_valid($information)) {
				return false;
			}

			$this->image_infos['name'] = $information;
			$this->image_infos['bild_temp'] = $this->pfad_images.$information;
		}

		$imageFile = $this->image_infos["bild_temp"];
		if (strtolower(pathinfo($imageFile, PATHINFO_EXTENSION)) === "svg") {
			$dimensions = self::determineDimensionsFromSVG($imageFile);

			$this->image_infos["breite"] = $dimensions["width"];
			$this->image_infos["hoehe"] = $dimensions["height"];
			$this->image_infos["type"] = "SVG";

			return true;
		}

		// sonstige Informationen ermitteln
		$temp_infos = @getimagesize($this->image_infos['bild_temp']);

		// Bild-Breite und -Höhe setzen
		$this->image_infos['breite'] = $temp_infos[0];
		$this->image_infos['hoehe'] = $temp_infos[1];

		// Bild-Typ setzen (JPG, GIF, PNG).
		// Hat das Bild kein gültiges Format, wird Typ = false
		switch ($temp_infos[2]) {
		case 1:
			$this->image_infos['type'] = "GIF";
			break;
		case 2:
			$this->image_infos['type'] = "JPG";
			break;
		case 3:
			$this->image_infos['type'] = "PNG";
			break;
		default:
			$this->image_infos['type'] = false;
		}
	}


	/**
	 * @param string $filename
	 * @return array [width, height, "width" => width, "height" => height]
	 */
	public static function determineDimensionsFromSVG($filename) {
		$width = $height = 0;

		if (class_exists("SimpleXMLElement")) {
			try {
				/** @noinspection PhpUsageOfSilenceOperatorInspection */
				$xml = @new SimpleXMLElement(file_get_contents($filename));

				if (isset($xml["viewBox"]) &&
					preg_match(
						'~-?(\d+(?:\.\d+)?)([\s,]+)-?(?1)(?2)(?<width>(?1))(?2)(?<height>(?1))~',
						(string)$xml["viewBox"],
						$match)
				) {
					$width = (int)round((float)$match["width"]);
					$height = (int)round((float)$match["height"]);
				}
				else if (isset($xml["width"], $xml["height"])) {
					$width = (int)round((float)$xml["width"]);
					$height = (int)round((float)$xml["height"]);
				}
			}
			catch (Exception $e) {}
		}

		return [$width, $height, "width" => $width, "height" => $height];
	}

	/**
	 * Speichert das Bild $bild in der Datei $datei (muß incl. Pfad angegeben werden)
	 * $bild kann dabei eine Bild-Ressource oder eine Datei (incl. Pfad) sein
	 *
	 * @param string|resource $bild
	 * @param string $datei
	 *
	 * @return bool true, wenn image kopiert oder angelegt wurde; false, wenn nicht alle Informationen übergeben wurden,
	 *              die Datei bereits existiert oder keinen gültigen Typ hat
	 */
	function image_save($bild = '', $datei = '')
	{
		// Wenn nicht alle Infos da sind oder die Datei schon existiert abbrechen
		if ((!$bild && !$datei) || file_exists($datei)) {
			return false;
		}
		// Bild als Datei-Kopie speichern
		if (is_string($bild)) {
			return copy($bild, $datei);
		}
		// Bild aus Bild-Ressource speichern
		else {
			switch ($this->image_infos['type']) {
			case "JPG":
				imagejpeg($bild, $datei, $this->cms->system_config_data['config_jpg_komprimierungsfaktor_feld_']);
				break;
			case "GIF":
				imagegif($bild, $datei);
				break;
			case "PNG":
				imagepng($bild, $datei);
				break;
			default:
				return false;
			}
			return true;
		}
	}

	/**
	 * @param int $breite
	 * @param int $hoehe
	 * @return array
	 */
	function image_get_thumbnail_size($breite = 0, $hoehe = 0)
	{
		$dimension = array(
			'breite' => 0,
			'hoehe' => 0,
			'skalierung' => array(
				"offset_x" => 0,
				"offset_y" => 0,
				"breite_korrigiert" => $breite,
				"hoehe_korrigiert" => $hoehe
			)
		);

		if ($this->thumbnail_scale == 1) {
			$temp_skalierung_x = $breite / $this->tumbnail_max_groesse['breite'];
			$temp_skalierung_y = $hoehe / $this->tumbnail_max_groesse['hoehe'];
			$temp_skalierung = min($temp_skalierung_x, $temp_skalierung_y);

			$temp_breite_korrigiert = round($temp_skalierung*$this->tumbnail_max_groesse['breite']);
			$temp_hoehe_korrigiert = round($temp_skalierung*$this->tumbnail_max_groesse['hoehe']);

			$temp_offset_x = floor(($breite - $temp_skalierung*$this->tumbnail_max_groesse['breite']) / 2);
			$temp_offset_y = floor(($hoehe - $temp_skalierung*$this->tumbnail_max_groesse['hoehe']) / 2);

			$dimension['breite'] = $this->tumbnail_max_groesse['breite'];
			$dimension['hoehe'] = $this->tumbnail_max_groesse['hoehe'];

			$dimension['skalierung']['offset_x'] = $temp_offset_x;
			$dimension['skalierung']['offset_y'] = $temp_offset_y;
			$dimension['skalierung']['breite_korrigiert'] = $temp_breite_korrigiert;
			$dimension['skalierung']['hoehe_korrigiert'] = $temp_hoehe_korrigiert;
		}
		else {
			// Wenn das Bild kleiner ist als die maximale Größe des Tumbnails
			if ($breite <= $this->tumbnail_max_groesse['breite'] && $hoehe <= $this->tumbnail_max_groesse['hoehe']) {
				$dimension['breite'] = $breite;
				$dimension['hoehe'] = $hoehe;
			}
			// sonst verkleinern
			else {
				$faktor_breite = ($breite / $this->tumbnail_max_groesse['breite']);
				$faktor_hoehe = ($hoehe / $this->tumbnail_max_groesse['hoehe']);
				$faktor = max($faktor_breite, $faktor_hoehe);

				$dimension['breite'] = round($breite / $faktor);
				$dimension['hoehe'] = round($hoehe / $faktor);
			}
		}

		return $dimension;
	}

	/**
	 * Erzeugt ein Bild mit einem Zugangscode für Formulare.
	 *
	 * @param string $code
	 * @param array $dimension optinal Größe des Bildes array(Breite, Hoehe) (Default 200px X 50 px)
	 * @return bool
	 */
	function image_zugangscode($code = "", $dimension = array())
	{
		// Defaultwerte setzen
		if (empty($code)) {
			return false;
		}
		$stellen = strlen($code);

		if (empty($dimension)) {
			$dimension = array(200, 50);
		}
		$anzahl_fonts = 1;
		$this->image_infos['type'] = "GIF";
		srand((double)microtime() * 1000000);

		// Muss leider so gemacht werden, da diese Funktion auch mal ohne den "Papoo-Kern" aufgerufen wird.

		// Bild erzeugen
		$image = $this->image_create($dimension, "GIF");
		if (!$image) {
			return false;
		}
		else {
			// set background color
			imagecolorallocate($image, 0, 0, 0);
			for ($i = 0; $i < $stellen; $i++) {
				// aktuellen Zeichen lesen
				$code_char = $code[$i];

				// Schriftfarbe, Font und X-Position des aktuellen Zeichens setzen
				$text_color = imagecolorallocate($image, 255, 255, 255);
				$font_nummer = rand(1, $anzahl_fonts);
				$font_file = PAPOO_ABS_PFAD."/lib/fonts/codefont".$font_nummer.".ttf";
				$font_size = $dimension[1] - 15;
				$position_x = round((($dimension[0] - 20) / $stellen) * $i + 10);
				$position_y = $dimension[1] - 10;

				// aktuelles Zeichen in's Bild schreiben
				imageTTFtext($image, $font_size, 0, $position_x, $position_y, $text_color, $font_file, $code_char);
			}
			// Das Bild ausgeben
			header("Content-type: image/gif");
			imagegif($image);
			exit;
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	function test_image_extension_valid($name = "")
	{
		if (empty($name)) {
			return false;
		}

		$temp_name_explode = explode(".", $name);
		$temp_bild_extension = strtolower($temp_name_explode[(count($temp_name_explode) - 1)]);

		return in_array($temp_bild_extension, $this->valid_image_extensions);
	}
}

$image_core = new image_core_class();