<?php

/**
 * Hauptdatei für das Krakenplugin.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 */

// Kraken.io gelieferte Interface Klasse einbinden
require_once('kraken.php');

// Custom Exception für Kraken Fehler einbinden
require_once('krakenerror.php');

/**
 * Class krakenplugin_class
 *
 * Hauptklasse des Plugins welches Bilder im CMS mittels kraken.io optimieren kann.
 *
 * @author Andreas Gritzan <ag@papoo.de>
 */
#[AllowDynamicProperties]
class krakenplugin_class
{

	/**
	 * Im Konstruktor werden alle mögliche Operationen überprüft und ggf. durchgeführt.
	 *
	 * Überprüft mittels CheckUpload ob eine Datei hochgeladen wurde und optimiert diese dann.
	 * Überprüft dann mittels CheckSettingUpdate ob neue API-Credentials, etc. eingegeben wurden und speichert diese.
	 * Und dann wird mittels CheckOptimizeNow geprüft ob die gesamte Datenbank optimiert werden soll und tut das dann auch ggf.
	 *
	 * @uses krakenplugin_class::CheckUpload
	 * @uses krakenplugin_class::CheckSettingUpdate
	 * @uses krakenplugin_class::CheckOptimizeNow
	 * @uses krakenplugin_class::is_aktiv
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content, $db, $cms, $image_core, $weiter, $db_praefix, $module, $checked, $user;
		$this->content = & $content;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->image_core = & $image_core;
		$this->weiter = & $weiter;
		$this->db_praefix = & $db_praefix;
		$this->module = & $module;
		$this->checked = & $checked;
		$this->user = & $user;

		if(defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace(PAPOO_ABS_PFAD . "/plugins/", "", $template);
			$template2 = basename($template2);

			if($template != "login.utf8.html") {
				if(stristr($template2, "kraken_backend")) {
					$this->content->template['plugin']['krakenplugin']['galery_plugin_exists'] = $this->CheckPluginExists("Bilder-Galerie");
					$this->content->template['plugin']['krakenplugin']['shop_plugin_exists'] = $this->CheckPluginExists("Onlineshop Plugin");

					if($this->is_aktiv()) {
						$this->CheckUpload();
					}

					$this->CheckSettingUpdate();

					$this->CheckOptimizeNow();

					$krakeninfo = $this->GetKrakenInfo();
					$krakeninfo['active'] = $this->is_aktiv();

					$this->content->template['plugin']['krakenplugin']['current_settings'] = $krakeninfo;
				}
			}
		}
	}

	/**
	 * Gibt zurück ob ein gegebenes Plugin installiert ist.
	 *
	 * Sucht in der XXX_papoo_plugins Datenbank nach dem Eintrag mit plugin_name=$name
	 * und gibt zurück ob ein solcher Eintrag gefunden wurde.
	 * @param $name Name des Plugins wie er in der XXX_papoo_plugins Tabelle steht.
	 * @return bool
	 * @uses ezSQL_mysqli Um auf die Datenbank zuzugreifen.
	 */
	private function CheckPluginExists($name)
	{
		$sql = sprintf("SELECT * FROM %s WHERE plugin_name='%s'", $this->db_praefix . "papoo_plugins", $name);

		$result = $this->db->get_results($sql, ARRAY_A);

		return !empty($result);
	}

	/**
	 * Ist das Plugin aktiv wird hier überprüft ob eine Datei hochgeladen wurde,
	 * welche dann per Upload-Methode optimiert und überschrieben wird.
	 * Falls ein Fehler in der Upload-Methode auftritt, wird der hier aufgefangen
	 * und an das template weiter gegeben um als Fehler ausgegeben zu werden.
	 *
	 * @uses checked_class
	 * @uses krakenplugin_class::Upload()
	 */
	private function CheckUpload()
	{
		try {
			if (isset($this->checked->action) && $this->checked->action == "SPEICHERN" && isset($this->checked->image_name)) {
				$this->Upload(PAPOO_ABS_PFAD . "/images/" . $this->checked->image_name);
			}
		}
		catch(KrakenError $e) {
			// Fehler ausgeben
			$this->content->template['plugin']['krakenplugin']['is_optimizing'] = $e->getMessage();
		}
	}

	/**
	 * Überprüft ob die gegebene Datei schon optimiert wurde und optimiert sie ggf.
	 * Da die Methode von CheckOptimizeNow benutzt wird, werden hier auch die Zähler
	 * incrementiert. *
	 *
	 * @param $filepath Der Pfad auf die Bild Datei incl. Dateiname und Endung.
	 * @param $filename Der Dateiname incl. Endung.
	 * @throws KrakenError
	 * @uses krakenplugin_class::CheckImageCompressed()
	 * @uses krakenplugin_class::Upload()
	 */
	private function CheckUploadFile($filepath, $filename)
	{
		if(!$this->CheckImageCompressed($filename)) {
			if($this->Upload($filepath)) {
				$this->content->template['plugin']['krakenplugin']['optinfo']['num_opt_files'] += 1;
			}
			else {
				$this->content->template['plugin']['krakenplugin']['optinfo']['num_failed_files'] += 1;
			}
		}
		else {
			$this->content->template['plugin']['krakenplugin']['optinfo']['already_optimized_files'] += 1;
		}
	}

	/**
	 * Geht durch das resultat einer kraken_settings Abfrage durch und gibt
	 * den zu $name passenden Eintrag zurück.
	 *
	 * @param $dbresult
	 * @param $name
	 * @return string|null
	 */
	private function GetValue($dbresult, $name)
	{
		foreach($dbresult as $entry) {
			if($entry['name'] == $name) {
				return $entry['value'];
			}
		}
		return NULL;
	}

	/**
	 * Holt sich den API-Key und API-Secret aus der kraken_settings Datenbank
	 * und gibt diese als array zurück.
	 *
	 * @return array|null Die API-Credentials oder null.
	 * @uses ezSQL_mysqli
	 */
	private function GetKrakenInfo()
	{
		$sql = sprintf("SELECT * FROM %s WHERE name='api_code' OR name='api_secret' OR name='lossless'", $this->db_praefix . "kraken_settings");

		$results = $this->db->get_results($sql, ARRAY_A);

		if(count($results) != 3) {
			return NULL;
		}

		$krakeninfo = array();

		$krakeninfo['secret'] = $this->GetValue($results, "api_secret");
		$krakeninfo['key'] = $this->GetValue($results, "api_code");
		$krakeninfo['lossless'] = $this->GetValue($results, "lossless");

		return $krakeninfo;
	}


	/**
	 * Schreibt den Namen der optimierten Datei in die kraken_history Datenbank.
	 *
	 * @param string $filename Bilder Dateiname incl. Endung
	 * @uses ezSQL_mysqli
	 */
	private function WriteDBOptimized($filename)
	{
		$sql = sprintf("INSERT INTO %s (bildname, optimiert) VALUES('%s', %d)", $this->db_praefix . "kraken_history", $this->db->escape($filename), 1);

		$this->db->query($sql);
	}

	/**
	 * Nimmt den Pfad einer Bilddatei und übergibt den an kraken.io zum optimieren.
	 * Dann wird die alte Datei mit den neuen Bildaten überschrieben und ein Eintrag in
	 * die Plugin Datenbank gemacht, dass das Bild optimiert wurde.
	 *
	 * @param string $filepath Absoluter Dateipfad auf die zu optimierende Bilder Datei.
	 * @return bool Ob der Upload erfolgreich war.
	 * @throws KrakenError
	 * @uses Kraken für die eigentliche Interaktion mit Kraken.io
	 * @uses krakenplugin_class::WriteDBOptimized()
	 * @uses krakenplugin_class::GetKrakenInfo()
	 */
	private function Upload($filepath)
	{
		$kraken_info = $this->GetKrakenInfo();
		$losless=false;

		if($kraken_info === NULL) {
			throw new KrakenError('API-Code oder API-Secret sind nicht gesetzt!');
		}

		$kraken = new Kraken($kraken_info['key'], $kraken_info['secret']);

		if ($kraken_info['lossless']==1) {
			$losless=true;
		}

		$params = array(
			"file" => $filepath,
			"wait" => true,
			"lossy" => $losless
		);

		@chmod($params['file'], 0777);

		$data = $kraken->upload($params);

		if(!$data['success']) {
			// In Datenbank schreiben dass diese Datei optimiert ist - damit es weitergehen kann...
			$this->WriteDBOptimized(basename($filepath));

			//errors setzen
			$this->error_files[]=$data['message']." Datei: ". $filepath;

			return false;
		}

		$this->content->template['plugin']['krakenplugin']['optinfo']['org_size'] += $data['original_size'];
		$this->content->template['plugin']['krakenplugin']['optinfo']['kraked_size'] += $data['kraked_size'];
		$this->content->template['plugin']['krakenplugin']['optinfo']['saved_bytes'] += $data['saved_bytes'];

		file_put_contents($filepath, fopen($data['kraked_url'], 'r'));

		// In Datenbank schreiben dass diese Datei optimiert ist
		$this->WriteDBOptimized($data['file_name']);

		return true;
	}

	/**
	 * Greift auf die kraken_settings zu und findet heraus ob das Plugin aktiv ist, also
	 * hochgeladene Bilder per kraken.io optimieren soll.
	 *
	 * @uses ezSQL_mysqli
	 * @return bool Ob das Plugin gerade hochgeladene Dateien abfangen und optimieren soll.
	 */
	private function is_aktiv()
	{
		$sql = sprintf("SELECT value FROM %s WHERE name='aktiv'", $this->db_praefix . "kraken_settings");

		$results = $this->db->get_results($sql, ARRAY_A);

		if(count($results) != 1) {
			return false;
		}

		return $results[0]['value'] == 1;
	}

	/**
	 * Implementiert den zugriff auf die kraken_settings Datenbank um die gewählten Einstellungen zu setzen.
	 *
	 * @param $name
	 * @param $value
	 * @uses ezSQL_mysqli
	 */
	private function UpdateSetting($name, $value)
	{
		if($value === NULL)
			return;

		$this->db->show_errors();

		$sql = sprintf("INSERT INTO %s (name, value) VALUES ('%s', '%s') ON DUPLICATE KEY UPDATE value=VALUES(value)", $this->db_praefix . "kraken_settings", $this->db->escape($name), $this->db->escape($value));

		$this->db->query($sql);
	}

	/**
	 * Überprüft ob das Eisntellungs-Form abgeschickt wurde und falls das der Fall ist, setzt die relevanten Einstellungen.
	 *
	 * @uses krakenplugin_class::UpdateSetting()
	 * @uses checked_class
	 * @return bool Gibt zurück ob die Einstellungen gespeichert wurden.
	 */
	private function CheckSettingUpdate()
	{
		if(!isset($this->checked->kraken_submitted1)) {
			return false;
		}

		$this->content->template['plugin']['krakenplugin']['settings_entered'] = '';

		// API-Key ist nicht gesetzt => Fehler ausgeben
		if(!isset($this->checked->kraken_api_code) or $this->checked->kraken_api_code == '') {
			$this->checked->kraken_api_code = NULL;
			$this->content->template['plugin']['krakenplugin']['settings_entered'] = 'missing_code';

			return false;
		}

		// API-Secret ist nicht gesetzt => Fehler ausgeben
		if(!isset($this->checked->kraken_api_secret) or $this->checked->kraken_api_secret == '') {
			$this->checked->kraken_api_secret = NULL;

			$this->content->template['plugin']['krakenplugin']['settings_entered'] = 'missing_secret';

			return false;
		}

		$this->content->template['plugin']['krakenplugin']['settings_entered'] = 'ok';

		$this->UpdateSetting("aktiv", (isset($this->checked->kraken_optimize_aktiv) and $this->checked->kraken_optimize_aktiv) ? 1 : 0);
		$this->UpdateSetting("api_code", $this->checked->kraken_api_code);
		$this->UpdateSetting("api_secret", $this->checked->kraken_api_secret);
		$this->UpdateSetting("lossless", $this->checked->lossless);

		return true;
	}

	/**
	 * Überprüft ob $ext eines der unterstüzten Formate ist und gibt true zurück falls das der Fall ist, andernfalls false.
	 *
	 * @param $ext
	 * @return bool
	 */
	private function IsLegalFileExtension($ext)
	{
		return $ext == 'jpg' or $ext == 'png' or $ext == 'gif';
	}

	/**
	 * Methode die ausschließlich für das Galerie Plugin benutzt wird, um dort alle Ordner durchzugehen
	 * und die enthaltenen Bilder ggf. hochzuladen.
	 *
	 * @param $directorypath
	 * @throws KrakenError
	 * @see krakenplugin_class::OptimizeAllImagesInDir() OptimizeAllImagesInDir()
	 * @see krakenplugin_class::CheckOptimizeNow() CheckOptimizeNow()
	 * @uses krakenplugin_class::IsLegalFileExtension()
	 * @uses krakenplugin_class::CheckUploadFile()
	 * @uses DirectoryIterator
	 */
	private function OptimizeAllImagesInMultiDir($directorypath)
	{
		$dir = new DirectoryIterator($directorypath);

		foreach($dir as $fileinfo) {
			if(!$fileinfo->isDot() and $fileinfo->getExtension() === '') {
				$subdir = new DirectoryIterator($fileinfo->getRealPath());

				foreach($subdir as $subfileinfo) {
					if(!$subfileinfo->isDot() and $this->IsLegalFileExtension($subfileinfo->getExtension())) {
						$this->CheckUploadFile($subfileinfo->getRealPath(), $subfileinfo->getFilename());
					}
				}
			}
		}
	}

	/**
	 * Durchläuft ein Ordner und optimiert alle Bilder darin falls diese nicht schon optimiert wurden.
	 *
	 * @param $directorypath
	 * @throws KrakenError
	 * @see krakenplugin_class::CheckOptimizeNow() CheckOptimizeNow()
	 * @uses krakenplugin_class::IsLegalFileExtension()
	 * @uses krakenplugin_class::CheckUploadFile()
	 * @uses DirectoryIterator
	 */
	private function OptimizeAllImagesInDir($directorypath)
	{
		$dir = new DirectoryIterator($directorypath);

		foreach($dir as $fileinfo) {
			if(!$fileinfo->isDot() and $this->IsLegalFileExtension($fileinfo->getExtension()))
			{
				$this->CheckUploadFile($fileinfo->getRealPath(), $fileinfo->getFilename());
			}
		}
	}

	/**
	 * Überprüft ob die Alle Bilder Datenbanken optimieren-Form abgeschickt wurde und optimiert die selektierten Ordner
	 * falls die Bilder nicht schon optimiert wurden.
	 *
	 * @uses krakenplugin_class::OptimizeAllImagesInDir()
	 * @uses krakenplugin_class::OptimizeAllImagesInMultiDir()
	 * @uses checked_class
	 */
	private function CheckOptimizeNow()
	{
		// Checkboxes sind nicht "isset" wenn Sie per post übergeben werden, also benutzen wir den hidden value kraken_submitted2
		if(!isset($this->checked->kraken_submitted2)) {
			return;
		}

		$kraken_optimize_1 = isset($this->checked->kraken_optimize_1) && $this->checked->kraken_optimize_1;
		$kraken_optimize_2 = isset($this->checked->kraken_optimize_2) && $this->checked->kraken_optimize_2;
		$kraken_optimize_3 = isset($this->checked->kraken_optimize_3) && $this->checked->kraken_optimize_3;

		$this->content->template['plugin']['krakenplugin']['optinfo'] = array();
		$this->content->template['plugin']['krakenplugin']['optinfo']['num_opt_files'] = 0;
		$this->content->template['plugin']['krakenplugin']['optinfo']['num_failed_files'] = 0;
		$this->content->template['plugin']['krakenplugin']['optinfo']['already_optimized_files'] = 0;
		$this->content->template['plugin']['krakenplugin']['optinfo']['org_size'] = 0;
		$this->content->template['plugin']['krakenplugin']['optinfo']['kraked_size'] = 0;
		$this->content->template['plugin']['krakenplugin']['optinfo']['saved_bytes'] = 0;

		try {
			// CMS
			if ($kraken_optimize_1) {
				$this->OptimizeAllImagesInDir(PAPOO_ABS_PFAD . "/images/");
			}

			// Shop
			if ($kraken_optimize_2) {
				$this->OptimizeAllImagesInDir(PAPOO_ABS_PFAD . "/plugins/papoo_shop/images/");
			}

			// Bilder Galerie
			if ($kraken_optimize_3) {
				$this->OptimizeAllImagesInMultiDir(PAPOO_ABS_PFAD . "/plugins/galerie/galerien/");
			}

			$this->content->template['plugin']['krakenplugin']['is_optimizing'] = 2;

			if($this->content->template['plugin']['krakenplugin']['optinfo']['org_size'] != 0) {
				$this->content->template['plugin']['krakenplugin']['optinfo']['saved_bytes_perc'] =
					$this->content->template['plugin']['krakenplugin']['optinfo']['saved_bytes'] /
					$this->content->template['plugin']['krakenplugin']['optinfo']['org_size'] * 100;
			}
			else {
				$this->content->template['plugin']['krakenplugin']['optinfo']['saved_bytes_perc'] = 0;
			}
		}
		catch(KrakenError $e) {
			if (isset($this->error_files) && count($this->error_files)>=1) {
				foreach ($this->error_files as $k=>$v) {
					// Fehler ausgeben
					$this->content->template['plugin']['krakenplugin']['is_optimizing'] .= $v."<br />\n";
				}
			}
		}
	}

	/**
	 * Überprüft ob ein gegebenes Bild schon optimiert wurde anhand der kraken_history Datenbank.
	 *
	 * @uses ezSQL_mysqli
	 * @param $imgname
	 * @return bool Ob das angegebene Bild optimiert ist.
	 */
	private function CheckImageCompressed($imgname)
	{
		// Ob die Datei schon komprimiert wurde aus der Datenbank holen
		$sql = sprintf("SELECT optimiert FROM %s WHERE bildname='%s'", $this->db_praefix . "kraken_history", $this->db->escape($imgname));

		$results = $this->db->get_results($sql, ARRAY_A);

		if(count($results) == 1) {
			return $results[0]['optimiert'];
		}
		else if(count($results) > 1) {
			// Mehrere mal die gleiche Datei => Da nur eine tatsächliche Datei reicht es, wenn ein "optimiert" Eintrag existiert
			foreach($results as $entry) {
				if($entry['optimiert'] == 1) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @ignore
	 */
	function post_papoo()
	{

	}

	/**
	 * @ignore
	 */
	function output_filter()
	{
		//global $output;
		//$output = preg_replace("[pP]lugin", "Plogwin", $output);
	}
}

$krakenplugin = new krakenplugin_class();
