<?php

/**
 * Hauptdatei für das kontrastwechsler-Plugin.
 *
 * Hauptklasse des Plugins welches eine editierbare Liste von Kontrasten ausgibt
 * und im Front zur Verfügung stellt.
 *
 * @author Achim Harrichhausen <info@papoo.de>
 */

/**
 * class kontrastwechsler_class
 *
 * Hauptklasse des Plugins.
 *
 * @return void
 */
#[AllowDynamicProperties]
class kontrastwechsler_class
{
	/** @var checked_class  */
	public $checked;
	/** @var cms  */
	public $cms;
	/** @var content_class  */
	public $content;
	/** @var ezSQL_mysqli  */
	public $db;
	/** @var module_class  */
	public $module;
	/** @var weiter  */
	public $weiter;
	/** @var string Gibt den Template Pfad an */
	private $templatePath = "kontrastwechsler/templates/";

	/**
	 * Kontruktor
	 */
	public function __construct()
	{
		// Enbindung globaler Klassen
		global $content, $db, $weiter, $cms, $checked, $module;

		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->module = &$module;
		$this->weiter = &$weiter;

		if (defined("admin")) {
			$this->makeBackend();
		}
		else {
			$this->makeFrontend();
		}
	}

	/**
	 * Bestimmt die Aufrufe des Backends.
	 *
	 * @return void
	 */
	private function makeBackend()
	{
		// Aufruf der verschiedenen Backend Templates
		if ($this->checked->template == $this->templatePath . "kontrastwechsler_backend.html") {
			// Pfad zum css Ordner zur Einbindung des backend.css
			$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/kontrastwechsler/css';
			$this->makeIndex();
			// Aufruf nach dem speichern
			if ($_SERVER['REQUEST_METHOD'] == "POST" && $this->checked->editCSS) {
				$this->editCSS();
			}
		}
		// Aufruf zum Template zum erstellen von Kontrasten
		else if ($this->checked->template == $this->templatePath . "kontrasterstellen_backend.html") {
			$this->makeContrast();
		}
		// Aufruf zum Template zum bearbeiten von Kontrasten
		else if ($this->checked->template == $this->templatePath . "kontrastbearbeiten_backend.html") {
			$this->editContrast();
			// Aufruf nach dem speichern
			if ($_SERVER['REQUEST_METHOD'] == "POST" && $this->checked->editContrast) {
				$this->editContrastFinish();
			}
		}
		// Aufruf zum Template der Einstellungen des Kontrastwechslers
		else if ($this->checked->template == $this->templatePath . "kontrastwechslereinstellungen_backend.html") {
			$this->contrastSettings();
			$this->showContrastSettings();
			// Aufruf nach dem speichern
			if ($_SERVER['REQUEST_METHOD'] == "POST" && $this->checked->changeSettings) {
				$this->editContrastSettings();
			}
		}
		else if ($this->checked->template == $this->templatePath . "confirm_delete_backend.html") {
			// Aufruf nach der Bestätigung des Löschvorgangs
			if ($_SERVER['REQUEST_METHOD'] == "POST" && $this->checked->confirmDeletion) {
				$this->deleteContrast();
			}
		}
	}

	/**
	 * Schreiben des Frontends.
	 *
	 * @return void
	 */
	private function makeFrontend()
	{
		// Letzte Überprüfung für EFA da es im Backend ja gesetzt sein kann ohne das es das soll.
		$folder = PAPOO_ABS_PFAD."/plugins/efafontsize";

		// Überprüfen ob das EFA Plugin installiert ist
		if (isset($this->cms->tbname["plugin_efa_fontsize"])) {
			// Query zum überprüfen ob EFA im Frontend vom Kontrastwechsler angezeigt werden soll.
			$efaQuery = sprintf(
				"SELECT `showEFA` FROM %s WHERE id = '1'",
				$this->cms->tbname['plugin_kontrastwechsler_einstellungen']
			);
			$efa = $this->db->get_results($efaQuery, ARRAY_A);

			// Überprüfen ob EFA angezeigt werden soll und der EFA Ordner vorhanden ist
			if ($efa[0]["showEFA"] == 1 && $this->findFolder($folder)) {
				// Eigene Funktion für das Frontend von EFA
				// (Im Orginalen EFA Plugin geht es leider nicht da ein Filter eingebaut ist)
				$this->getFontsizeDat();
				// Efa wird angezeigt mit dem Kontrastwechsler
				$this->content->template['efa'] = "1";
			}
			else {
				// EFA Soll nicht angezeigt werden oder der Ordner ist nicht vorhanden
				$this->content->template['efa'] = "0";
			}
		}
		else {
			// EFA Plugin nicht installiert
			$this->content->template['efa'] = "0";
		}

		// Nimmt die ID wie das Module gestyled werden soll und das CSS
		$styleQuery = sprintf(
			"SELECT `moduleStyle`, `ownStyleCSS` FROM %s WHERE id = '1'",
			$this->cms->tbname['plugin_kontrastwechsler_einstellungen']
		);
		$style = $this->db->get_results($styleQuery, ARRAY_A);

		$this->content->template['moduleStyleID'] = $style[0]['moduleStyle'];
		$this->content->template['moduleStyleCSS'] = $style[0]['ownStyleCSS'];

		// Nehmen der Kontrast Liste für das Frontend
		$kontrastListe = $this->getContrastList();

		$this->content->template['kontraste'] = $kontrastListe;
		$this->content->template['plugin']['kontrastwechsler']['initialStyle'] =
			isset($_COOKIE["contrast"]) ? (int)$_COOKIE["contrast"] : NULL;
	}

	/**
	 * Schreiben des Indexes im Backend. (Limit von Seiten der Tabelle von Kontrasten + weitere Aufrufe)
	 *
	 * @return void
	 */
	private function makeIndex()
	{
		// Limit von Einträgen bis eine weitere Seite angezeigt wird.
		$this->weiter->make_limit(10);

		// Aufruf um die Kontraste auszugeben.
		$kontrastListe = $this->getContrastList();
		$this->content->template['plugin']['kontrastwechsler']['daten'] = $kontrastListe;
		// Aufruf um das CSS auszugeben.
		$cssListe = $this->getCssList();
		$this->content->template['plugin']['kontrastwechsler']['css'] = $cssListe;
	}

	/**
	 * Ausgabe von Kontrasten.
	 *
	 * @return array $kontraste Liste der Kontraste aus der Datenbank
	 */
	private function getContrastList()
	{
		$this->weiter->make_limit(10);

		// Zählen der IDs für die weiteren Seiten.
		$sql = "SELECT COUNT(kontrastID) FROM `{$this->cms->tbname["plugin_kontrastwechsler"]}` ";
		$this->weiter->result_anzahl = $this->db->get_var($sql);

		// Query für alle Kontraste
		$query = sprintf(
			"SELECT *, `textColor` as `textcolor`, `backgroundColor` as `backgroundcolor` FROM %s %s",
			$this->cms->tbname['plugin_kontrastwechsler'],
			$this->weiter->sqllimit
		);
		$kontraste = $this->db->get_results($query,ARRAY_A);

		// Links zu weiteren Seiten erstellen.
		$this->weiter->weiter_link = "plugin.php?menuid=" . $this->checked->menuid .
			"&template=" . $this->templatePath . "kontrastwechsler_backend.html";

		// Links zu weiteren Seiten anzeigen.
		$this->weiter->do_weiter("teaser");

		return $kontraste;
	}

	/**
	 * Ausgabe vom CSS.
	 *
	 * @return array $css Liste des CSS aus der Datenbank
	 */
	private function getCssList()
	{
		// Query für alle Kontraste
		$query = "SELECT *, `cssText` as `csstext` FROM " . $this->cms->tbname['plugin_kontrastwechsler_css'];
		return $this->db->get_results($query,ARRAY_A);
	}

	/**
	 * Bearbeiten vom CSS im Backend.
	 *
	 * @return void
	 */
	private function editCSS()
	{
		$this->writeCssTable($this->checked->css_id, $this->checked->css_text);

		// $this->content->template für das hinzufügen.
		$this->content->template['kontrastwechsler']['css_id'] = $this->checked->css_id;
		$this->content->template['kontrastwechsler']['css_text'] = "nobr:" . $this->checked->css_text;

		// Weiterleitung nach Abschluss
		header("Location: plugin.php?menuid=" . $this->checked->menuid .
			"&template=" . $this->templatePath . "kontrastwechsler_backend.html&edit_css_success=1"
		);

		exit;
	}

	/**
	 * Bearbeiten vom CSS im Backend.
	 *
	 * @param string $cssID ID des CSS
	 * @param string $cssText Gesamtes CSS
	 *
	 * @return void
	 */
	private function writeCssTable($cssID, $cssText)
	{
		if ($cssID == 1) {
			// Bearbeiten des CSS in der Datenbank
			$query = sprintf(
				"UPDATE %s SET `cssText` = '%s' WHERE `cssID` = %d;",
				$this->cms->tbname['plugin_kontrastwechsler_css'],
				$this->db->escape($cssText),
				$this->db->escape($cssID)
			);
			$this->db->query($query);
		}
	}

	/**
	 * Hinzufügen von Kontrasten im Backend.
	 *
	 * @return void
	 */
	private function makeContrast()
	{
		$id = $this->checked->kontrast_id;
		$name = $this->checked->kontrast_name;
		$description = $this->checked->kontrast_description;
		$textColor = $this->checked->{'kontrast_text-color'};
		$backgroundColor = $this->checked->{'kontrast_background-color'};

		// Kein Aufruf wenn keine Name gegeben ist.
		if ($name != "") {
			$this->writeContrastTable($id, $name, $description, $textColor, $backgroundColor);

			// $this->content->template für das hinzufügen.
			$this->content->template['kontrastwechsler']['kontrast_id'] = $id;
			$this->content->template['kontrastwechsler']['kontrast_name'] = $name;
			$this->content->template['kontrastwechsler']['kontrast_description'] = $description;
			$this->content->template['kontrastwechsler']['kontrast_text-color'] = $textColor;
			$this->content->template['kontrastwechsler']['kontrast_background-color'] = $backgroundColor;

			// Weiterleitung nach Abschluss
			header("Location: plugin.php?menuid=" .
				$this->checked->menuid .
				"&template=" . $this->templatePath . "kontrastwechsler_backend.html&add_success=1"
			);
			exit;
		}
	}

	/**
	 * Bearbeiten von Kontrasten im Backend. (Nehmen der Kontrast ID über checked Klasse)
	 *
	 * @return void
	 */
	private function editContrast()
	{
		// Nehmen der ID des zu bearbeitenden Kontrastes über checked Klasse.
		$id = $this->checked->kontrast_id;
		// SELECT für den zu bearbeitenden Kontrast.
		$query = sprintf(
			"SELECT * FROM %s WHERE kontrastID = %d",
			$this->cms->tbname['plugin_kontrastwechsler'],
			$this->db->escape($id)
		);
		$result = $this->db->get_results($query, ARRAY_A);

		// Wenn nichts zurück kommt.
		if (is_array($result) && count($result) != 1) {
			return;
		}

		// $this->content->template für das bearbeiten von Kontrasten.
		$this->content->template['kontrastwechsler']['kontrastID'] = $result[0]['kontrastID'];
		$this->content->template['kontrastwechsler']['kontrastName'] = $result[0]['name'];
		$this->content->template['kontrastwechsler']['kontrastDescription'] = $result[0]['description'];
		$this->content->template['kontrastwechsler']['kontrastTextColor'] = $result[0]['textColor'];
		$this->content->template['kontrastwechsler']['kontrastBackgroundColor'] = $result[0]['backgroundColor'];
	}

	/**
	 * Aufruf zum Speichern nachdem ein Kontrast bearbeitet wurde.
	 *
	 * @return void
	 */
	private function editContrastFinish()
	{
		$id = $this->checked->kontrast_id;
		$name = $this->checked->kontrast_name;
		$description = $this->checked->kontrast_description;
		$textColor = $this->checked->{'kontrast_text-color'};
		$backgroundColor = $this->checked->{'kontrast_background-color'};

		// Kein Aufruf wenn kein Name gegeben ist
		if ($name != "") {
			$this->writeContrastTable($id, $name, $description, $textColor, $backgroundColor);
			// $this->content->template für das hinzufügen.
			$this->content->template['kontrastwechsler']['kontrast_id'] = $id;
			$this->content->template['kontrastwechsler']['kontrast_name'] = $name;
			$this->content->template['kontrastwechsler']['kontrast_description'] = $description;
			$this->content->template['kontrastwechsler']['kontrast_text-color'] = $textColor;
			$this->content->template['kontrastwechsler']['kontrast_background-color'] = $backgroundColor;

			// Weiterleitung nach Abschluss
			header("Location: plugin.php?menuid=" . $this->checked->menuid .
				"&template=" . $this->templatePath . "kontrastwechsler_backend.html&edit_success=1");
			exit;
		}
	}

	/**
	 * Löschen von Kontrasten im Backend.
	 *
	 * @return void
	 */
	private function deleteContrast()
	{
		// Nehmen der Kontrast ID über checked Klasse
		$kontrastID = $this->checked->kontrast_id;

		// Löschen eines Kontrasts in der Datenbank
		$query = sprintf(
			"DELETE FROM %s WHERE kontrastID = %d",
			$this->cms->tbname['plugin_kontrastwechsler'],
			$this->db->escape($kontrastID)
		);
		$this->db->query($query);

		// Weiterleitung nach Abschluss
		header("Location: plugin.php?menuid=" .
			$this->checked->menuid .
			"&template=" . $this->templatePath . "kontrastwechsler_backend.html&delete_success=1"
		);
		exit;
	}

	/**
	 * Schreiben von Kontrasten in die Datenbank.
	 *
	 * @param int $id Kontrast ID
	 * @param string $name Kontrast Name
	 * @param string $description Kontrast Beschreibung
	 * @param string $textColor Kontrast Hex Nr. für den Text
	 * @param string $backgroundColor Kontrast Hex Nr. für den Hintergrund
	 *
	 * @return void
	 */
	private function writeContrastTable($id, $name, $description, $textColor, $backgroundColor)
	{
		if ($id == NULL) {
			// Hinzufügen eines Kontrasts in die Datenbank.
			$query = sprintf(
				"INSERT INTO %s (`kontrastID`, `name`, `description`, `textColor`, `backgroundColor`)
				VALUES (%d, '%s', '%s', '%s', '%s');",
				$this->cms->tbname['plugin_kontrastwechsler'],
				(int)$id,
				$this->db->escape($name),
				$this->db->escape($description),
				$this->db->escape($textColor),
				$this->db->escape($backgroundColor)
			);
			$this->db->query($query);
		}
		else {
			// Bearbeiten eines Kontrastes in der Datenbank
			$query = sprintf(
				"UPDATE %s
				SET `name` = '%s', `description` = '%s', `textColor` = '%s', `backgroundColor` = '%s'
				WHERE `kontrastID` = %d;",
				$this->cms->tbname['plugin_kontrastwechsler'],
				$this->db->escape($name),
				$this->db->escape($description),
				$this->db->escape($textColor),
				$this->db->escape($backgroundColor),
				(int)$id
			);
			$this->db->query($query);
		}
	}

	/**
	 * Einstellungen des Kontrastwechslers.
	 *
	 * Einstellungen:
	 * - EFA in verbindung mit dem Kontrastwechsler im Frontend ausgeben
	 * - Verschiedene Frontend Styles
	 *
	 * @return void
	 */
	private function contrastSettings()
	{
		$moduleName = "plugin:efafontsize/templates/mod_efafontsize_front.html";
		$folder = PAPOO_ABS_PFAD."/plugins/efafontsize";

		// Überprüfung ob ein Error schon ausgegeben wird damit kein Loop entsteht
		if ($this->checked->efa_error_1 !== "1" &&
			$this->checked->efa_error_2 !== "1" &&
			$this->checked->efa_error_3 !== "1") {

			// Ist EFA als Datei nicht vorhanden?
			if (!$this->findFolder($folder)) {
				header("Location: plugin.php?menuid=" . $this->checked->menuid .
					"&template=" . $this->templatePath . "kontrastwechslereinstellungen_backend.html&efa_error_3=1");
				exit;
			}
			// Ist EFA als Plugin nicht installiert?
			if (!isset($this->cms->tbname["plugin_efa_fontsize"])) {
				header("Location: plugin.php?menuid=" . $this->checked->menuid .
					"&template=" . $this->templatePath . "kontrastwechslereinstellungen_backend.html&efa_error_2=1");
				exit;
			}
			// Ist EFA nicht als Modul eingebunden?
			if ($this->findModules($moduleName)) {
				header("Location: plugin.php?menuid=" . $this->checked->menuid .
					"&template=" . $this->templatePath . "kontrastwechslereinstellungen_backend.html&efa_error_1=1");
				exit;
			}
		}
	}

	/**
	 * Anzeigen der momentan gesetzen Einstellungen.
	 *
	 * @return void
	 */
	private function showContrastSettings()
	{
		// Alle Einstellungen aus der Datenbank
		$settingsQuery = sprintf(
			"SELECT * FROM %s WHERE id = '1'", $this->cms->tbname['plugin_kontrastwechsler_einstellungen']
		);
		$settings= $this->db->get_results($settingsQuery, ARRAY_A);

		// $this->content->template für das bearbeiten von Kontrasten.
		$this->content->template['plugin']['kontrastwechsler']['settings']['showEFA'] = $settings[0]['showEFA'];
		$this->content->template['plugin']['kontrastwechsler']['settings']['moduleStyle'] = $settings[0]['moduleStyle'];
		$this->content->template['plugin']['kontrastwechsler']['settings']['ownStyleCSS'] = "nobr:" . $settings[0]['ownStyleCSS'];
	}

	/**
	 * Aufruf zum Speichern nachdem die Einstellungen bearbeitet wurde.
	 *
	 * @return void
	 */
	private function editContrastSettings()
	{
		// Soll es im Frontend angezeigt werden wird $show_efa auf 1 gesetzt ansonsten auf 0.
		$showEFA = ($this->checked->efaconnect == 'on' ? 1 : 0);

		// Query zum bearbeiten der Einstellungen
		$settingsQuery = sprintf(
			"UPDATE %s SET `showEFA` = '%d', `moduleStyle` = '%d', `ownStyleCSS` = '%s' WHERE `id` = 1;",
			$this->cms->tbname['plugin_kontrastwechsler_einstellungen'],
			$showEFA,
			$this->db->escape($this->checked->styleselectorID),
			$this->db->escape($this->checked->ownStyleCSS)
		);
		$this->db->query($settingsQuery);

		header("Location: plugin.php?menuid=" . $this->checked->menuid .
			"&template=" . $this->templatePath . "kontrastwechslereinstellungen_backend.html&edit_success=1");
		exit;
	}

	/**
	 * Sucht nach einem Ordner auf der lokalen Seite
	 *
	 * @param string $folder Gesuchter Ordner mit Pfad.
	 *
	 * @return bool true, wenn es gefunden wird, ansonsten false
	 */
	private function findFolder($folder)
	{
		$path = realpath($folder);
		return $path !== false && is_dir($path);
	}

	/**
	 * Sucht ob ein übergebener Modulname eingebunden ist.
	 *
	 * @param string $modulName Name des zu suchenden Modules
	 *
	 * @return bool True wenn das Modul im Aktuellen Style eingebunden ist, ansonsten false
	 */
	private function findModules($modulName)
	{
		// Suchen nach dem aktiven Style um dann nach dem Template zu suchen ob es aktiv ist
		$moduleQuery = sprintf(
			"SELECT stylemod_style_id, stylemod_mod_id, mod_datei, mod_id
			FROM %s INNER JOIN %s ON mod_id = stylemod_mod_id
			WHERE mod_datei = '%s' AND stylemod_style_id = (SELECT style_id FROM %s WHERE standard_style = '%d')",
			$this->cms->tbname['papoo_module'],
			$this->cms->tbname['papoo_styles_module'],
			$this->db->escape($modulName),
			$this->cms->tbname['papoo_styles'], 1
		);
		$moduleResult= $this->db->get_results($moduleQuery, ARRAY_A);

		// Gibt true zurück wenn das Modul sich im Aktuellen Style befindet, ansonsten false
		return (isset($moduleResult[0]) && is_array($moduleResult[0]));
	}

	/**
	 * Umgeschrieben da in dem normalen EFA Plugin ein Filter hier eingesetzt wird.
	 *
	 * efafontsize_class::getFontsizeDat()
	 *
	 * @return void
	 */
	private function getFontsizeDat()
	{
		// Normalerweise !empty hier ist es aber leer.
		if (empty($this->module->module_aktiv['mod_efafontsize_front'])) {
			$sql = "SELECT * FROM ". $this->cms->tbname['plugin_efa_fontsize'];
			$this->content->template['efa_fontsize_spez'] = $this->db->get_results($sql,ARRAY_A);;
		}
	}
}
$kontrastwechsler = new kontrastwechsler_class();
