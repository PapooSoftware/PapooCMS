<?php

/**
 * Klasse ermöglicht den Download eine Galerie als ZIP File
 *
 * @author dr. carsten euwens - papoo-media.de
 *
 */
class galdownload
{
	/** @var int  */
	var $page_size = 2;

	/**
	 * galdownload constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;

			#$this->content->template['is_dev'] = "OK"; zentrale_self
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/", "", $template);
			$template2 = basename($template2);

			if ($template != "login.utf8.html") {
				//$this->check_domain();

				//Generelle Einstellungen
				if ($template2 == "galdownload_back.html") {
					$this->make_config();
				}
			}
		}
		else {
			//Das mus noch integriert werden
			$this->make_download();
		}
	}

	/**
	 * Funktion macht derzeit nix,..
	 * @return bool
	 */
	private function make_config()
	{
		return false;
	}

	/**
	 * Hier die Ersetzung des Links damit Menüpunkt und Artikel ID erhalten bleiben und
	 * man nicht wegspringt!
	 */
	public function output_filter()
	{
		global $output;
		if (stristr($output, "download_zip_galID")) {
			$output = str_ireplace('?download_zip_galID', '?menuid=' . $this->checked->menuid
				. '&reporeid=' . $this->checked->reporeid . '&download_zip_galID',
				$output);
		}
	}

	/**
	 * Hier den Filter setzen damit der Button auch nur zu sehen ist
	 * wenn das Plugin aktiv ist
	 */
	public function output_filter_admin()
	{
		global $is_gal_download;
		$is_gal_download="ok";
	}

	/**
	 * Realisierung des Downloads
	 * Daten aus DB holen und zip Datei erstellen
	 */
	private function make_download()
	{
		//Check ob auch eine Gal heruntergeladen werden soll
		if (is_numeric($this->checked->download_zip_galID)) {
			$gal_id = $this->checked->download_zip_galID;
		} //Nein? Dann raus
		else {
			return false;
		}

		//Daten der Galerie holen
		$gal_data = $this->get_gal_data();

		//Verzeichnis der Galerie
		$verzeichnis = $this->get_verzeichnis_gal();

		//Zip Klasse einbinden
		require_once(PAPOO_ABS_PFAD . "/lib/classes/zip_class.php");

		//Klasse ini
		$zip = new Zip();
		// Kommentar
		$zip->setComment("Bilder der Galerie.\nCreated on " . date('l jS \of F Y h:i:s A'));
		$counter = 0;

		//Alle durchgehen
		foreach ($gal_data as $k => $v) {
			//Pfad
			$file = PAPOO_ABS_PFAD . "/plugins/galerie/galerien/" . $verzeichnis . "/" . $v['bild_datei'];

			//Datei vorhanden und nicht leer (Verzeichnis) - dann befüllen
			if (file_exists($file) && !empty($v['bild_datei'])) {
				//einlesen
				$filedata = file_get_contents($file);

				//Daten zuweisen
				$zip->addFile($filedata, $v['bild_datei']);

				//Zähler um spöter zu bestimmen das auch Dateien drin sind... einfacher.
				$counter++;
			}

		}
		//Wenn Keine Dateien vorhanden, nix machen...

		if ($counter == 0) {
			debug::print_d("FEHLER - Keine Dateien vorhanden die heruntergeladen werden k&ouml;nnen!");
			exit();
		}
		else {
			//Zip Datei Downloaden
			$zip->sendZip("Galerie _" . $verzeichnis . "_" . $gal_id . ".zip");
		}
		//Raus
		exit();
	}

	/**
	 * Holt den Namen des Galerie Verzeichnisses aus der DB
	 * @return array|void
	 */
	private function get_verzeichnis_gal()
	{
		$sql = sprintf("SELECT gal_verzeichnis FROM %s WHERE gal_id='%d'",
			DB_PRAEFIX . "galerie_galerien",
			$this->db->escape($this->checked->download_zip_galID)
		);
		$result = $this->db->get_var($sql);

		return $result;
	}


	/**
	 * HOlt alle Galeriebilder aus der Datenbank
	 * @return array|void
	 */
	private function get_gal_data()
	{
		//Anhand der ID die Bilder einer Galerie rausholen
		$sql = sprintf("SELECT * FROM %s WHERE bild_gal_id='%d'",
			DB_PRAEFIX . "galerie_bilder",
			$this->db->escape($this->checked->download_zip_galID)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		return $result;
	}
}

$galdownload = new galdownload();
