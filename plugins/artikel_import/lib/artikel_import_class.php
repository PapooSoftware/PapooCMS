<?php

/**
 * Hauptdatei für das Artikel-Import-Plugin.
 *
 * Hauptklasse des Plugins welches ...
 *
 * @author Achim Harrichhausen <info@papoo.de>
 */

/**
 * class artikel_import
 *
 * Hauptklasse des Plugins.
 *
 * @return void
 */
#[AllowDynamicProperties]
class artikel_import
{
	/** @var checked_class */
	public $checked;
	/** @var cms */
	public $cms;
	/** @var content_class */
	public $content;
	/** @var ezSQL_mysqli */
	public $db;
	/** @var module_class */
	public $module;
	/** @var string Gibt den Template Pfad an */
	private $templatePath = "artikel_import/templates/";

	public function __construct()
	{
		// Enbindung globaler Klassen
		global $content, $db, $cms, $checked, $module;

		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->module = &$module;

		if (defined("admin")) {
			$this->makeBackend();
		}
	}

	/**
	 * Bestimmt die Aufrufe des Backends.
	 *
	 * @return void
	 */
	private function makeBackend()
	{
		// Pfad zum css Ordner zur Einbindung des backend.css
		$this->content->template['css_path'] = PAPOO_WEB_PFAD . '/plugins/artikel_import/css';

		// Aufruf der verschiedenen Backend Templates
		if ($this->checked->template == $this->templatePath . "artikel_import_backend.html") {
			unset($_SESSION['ImportFile']);
		} elseif ($this->checked->template == $this->templatePath . "artikel_import_start_backend.html") {
			//Aufruf nach dem speichern der Einstellungen
			if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($this->checked->startupload)) {
				$this->startImport();
			}
			// Eine Zuordnung wird getätigt
			elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($this->checked->startzuordnen)) {
				$this->doZuordnung();
			}
			// Eine Zuordnung wird gelöscht
			elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($this->checked->deletezuordnung)) {
				$this->deleteZuordnung();
			}
			// Das Mapping wird beendet und der finale Import wird gestartet
			elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($this->checked->finishmapping)) {
				$this->finishImport();
			}
			// Damit die Namen der Menü's in der Auswahl auftauchen
			$query = sprintf(
				"SELECT menuid_id, menuname FROM %s WHERE lang_id = %d",
				$this->cms->tbname['papoo_menu_language'],
				1
			);
			$this->content->template['menues'] = $this->db->get_results($query, ARRAY_A);

			// Damit die Namen der Sprachen in der Auswahl auftauchen
			// FIXME: Braucht evtl. einen Zwischen-Schritt um die Menüs nachträglich auszuwählen
			/**
			$query = sprintf(
				"SELECT lang_id, lang_short, lang_long FROM %s WHERE more_lang = %d",
				$this->cms->tbname['papoo_name_language'],
				2
			);
			$this->content->template['languages'] = $this->db->get_results($query, ARRAY_A);
			 **/
		}
	}

	/**
	 * Starten des initiellen Imports nach dem Upload der Datei.
	 */
	private function startImport()
	{
		$this->doFileUpload();

		// Fehler beim hochladen
		if (!$_SESSION['ImportFile'] || $this->content->template['ImportErrortext']) {
			if (!$this->content->template['ImportErrortext']) {
				$this->content->template['ImportErrortext'] = "Unbekannter Fehler";
			}
			$this->content->template['ImportError'] = true;
			return;
		}

		// Einstellungen wieder speichern, um diese durchs Template zu schleifen | TODO: In Session einfügen
		$this->content->template['settings'] = array(
			'encodingType' => $this->checked->encodingType,
			'delimiter' => $this->checked->delimiter,
			'menueID' => $this->checked->menueID,
			'fieldsNumber' => $this->checked->fieldsNumber,
		);

		$this->content->template['connectedFields'] = [];
		$this->content->template['ArtikelImportStep2'] = true;

		$this->setFields($this->content->template['settings']);
	}

	/**
	 * Datei auf den Server laden.
	 */
	private function doFileUpload()
	{
		// Upload durchführen
		$fileUpload = new file_upload(PAPOO_ABS_PFAD);

		$fileUpload->copy_mode = 2;
		if (isset($_FILES['importFile']) && $fileUpload->upload($_FILES['importFile'], 'dokumente/upload', 0, array('php'), 1)) {
			$filename = $fileUpload->file['name'];
			$download = "/dokumente/upload/" . $filename;
			$_SESSION['ImportFile'] = $download;
		} else {
			$this->content->template['ImportErrortext'] = $this->content->template['message_20'] . "<p>$fileUpload->error</p>";
		}
	}

	/**
	 * Felder setzen für das Mapping
	 * @param $settings
	 */
	private function setFields($settings)
	{
		// Felder aus dem Upload
		$this->content->template['csv_felder'] = $this->getFileFields($settings);

		// Vorbestimmte Felder für den finalen Import
		$this->content->template['artikel_felder'] = [
			'header' => 'Artikel-Überschrift',
			'lan_teaser' => 'Artikel-Teaser',
			'lan_teaser_link' => 'Text für Verlinkung zum Artikel im Teaser',
			'lan_article' => 'Artikel-Inhalt',
			'lan_metatitel' => 'Meta-Titel',
			'lan_metadescrip' => 'Meta-Beschreibung',
			'lan_metakey' => 'Meta-Schlüssel',
			'url_header' => 'Artikel-Link',
			'erstellungsdatum' => 'Erstellungsdatum',
		];
	}

	/**
	 * Felder aus der csv holen mit den Einstellungen die der Benutzer getroffen hat
	 * @param $settings
	 * @return array|false
	 */
	private function getFileFields($settings): array|bool
	{
		$delimiterArray = [
			'tab' => 0x09,
			'comma' => ',',
			'semicolon' => ';',
			'space' => 0x20,
		];
		$delimiter = $delimiterArray[$settings['delimiter']] ?? ",";

		$row = 0;
		if (($handle = fopen(PAPOO_ABS_PFAD . $_SESSION['ImportFile'], "r")) !== false) {
			while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
				if (++$row < $settings['fieldsNumber']) {
					continue;
				}
				fclose($handle);
				return $line;
			}
		}
		return false;
	}

	/**
	 * Eine Zuordnung durchführen
	 */
	private function doZuordnung()
	{
		// Übersetzungen für Felder speichern
		$this->content->template['artikelFelderTranslation'] = [
			'header' => 'Artikel-Überschrift',
			'lan_teaser' => 'Artikel-Teaser',
			'lan_teaser_link' => 'Text für Verlinkung zum Artikel im Teaser',
			'lan_article' => 'Artikel-Inhalt',
			'lan_metatitel' => 'Meta-Titel',
			'lan_metadescrip' => 'Meta-Beschreibung',
			'lan_metakey' => 'Meta-Schlüssel',
			'url_header' => 'Artikel-Link',
			'erstellungsdatum' => 'Erstellungsdatum',
		];

		$this->content->template['ArtikelImportStep2'] = true;

		$settings = array(
			'encodingType' => $this->checked->encodingType,
			'delimiter' => $this->checked->delimiter,
			'menueID' => $this->checked->menueID,
			'fieldsNumber' => $this->checked->fieldsNumber,
		);
		$this->content->template['settings'] = $settings;
		$this->content->template['connectedFields'] = array_filter($this->checked->connectedFields, function ($connectedField) {
			return strlen($connectedField['csv']) > 0 && strlen($connectedField['artikel']) > 0;
		});
		$this->setFields($settings);

		$connectedCsvFields = array_unique(array_map(function ($connectedField) {
			return $connectedField['csv'];
		}, $this->content->template['connectedFields']));
		$this->content->template['csv_felder'] = array_filter($this->content->template['csv_felder'], function ($field) use ($connectedCsvFields) {
			return in_array($field, $connectedCsvFields) == false;
		});

		$connectedArticleFields = array_unique(array_map(function ($connectedField) {
			return $connectedField['artikel'];
		}, $this->content->template['connectedFields']));
		$this->content->template['artikel_felder'] = array_filter($this->content->template['artikel_felder'], function ($field) use ($connectedArticleFields) {
			return in_array($field, $connectedArticleFields) == false;
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Zuordnung entfernen
	 */
	private function deleteZuordnung()
	{
		unset($this->checked->connectedFields[$this->checked->deletezuordnung]);
		$this->doZuordnung();
	}

	/**
	 * Import nach Mapping abschließen
	 */
	private function finishImport()
	{
		$settings = array(
			'encodingType' => $this->checked->encodingType,
			'delimiter' => $this->checked->delimiter,
			'menueID' => $this->checked->menueID,
			'fieldsNumber' => $this->checked->fieldsNumber,
		);
		$this->setFields($settings);

		$connectedFields = $this->checked->connectedFields;

		$newConnectedFields = [];
		foreach ($connectedFields as $connectedField) {
			$newConnectedFields[$connectedField['artikel']] = $connectedField['csv'];
		}

		$csvData = $this->getCsvData($settings);

		$sql = sprintf("SELECT MAX(lart_order_id) FROM `%s` WHERE lcat_id = %d GROUP BY lcat_id",
			$this->cms->tbname['papoo_lookup_art_cat'],
			$settings['menueID']
		);
		$menuOrderId = (int)$this->db->get_var($sql) ?: 0;

		$importedArticleNumber = 0;

		foreach ($csvData as $importedArticle) { // TODO: Mehr Fehler-Prüfung und Fehler ausgeben lassen (Array bauen für jeden durchlauf wo dann der Fehler gespeichert wird)
			$time = strtotime($importedArticle[$newConnectedFields['erstellungsdatum']] ?? '') ?: time();

			// Artikel
			$sql = sprintf("INSERT INTO %s SET
						`cattextid` = %d,
						`dokuser` = 10,
						`timestamp` = '%s',
						`erstellungsdatum` = '%s',
						`pub_verfall` = 0,
						`pub_start` = 0, `pub_verfall_page` = 0, `pub_start_page` = 0, `pub_wohin` = 0, `publish_yn` = 1,
						`teaser_list` = 0, `teaser_atyn` = 1, `allow_publish` = 1, `order_id` = 1, `dokuser_last` = 0,
						`dok_teaserfix` = 0, `dok_show_teaser_link` = 0, `dok_show_teaser_teaser` = 0, `cat_category_id` = 0",
				$this->cms->tbname['papoo_repore'],
				$settings['menueID'],
				date('Y-m-d H:i:s', $time),
				date('Y-m-d H:i:s', $time)
			);
			$this->db->query($sql);

			// Erstellte Artikel-ID entnehmen
			$articleId = $this->db->insert_id;

			// Menüzuordnung
			$sql = sprintf("INSERT INTO %s SET
						`lart_id` = %d,
						`lcat_id` = %d,
						`lart_order_id` = %d",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$articleId,
				$settings['menueID'],
				++$menuOrderId
			);
			$this->db->query($sql);

			// Schreibrechte
			$sql = sprintf('INSERT INTO %s (`article_wid_id`, `gruppeid_wid_id`) VALUES (%2$d, 1), (%2$d, 11)',
				$this->cms->tbname['papoo_lookup_write_article'],
				$articleId
			);
			$this->db->query($sql);

			// Leserechte
			$sql = sprintf('INSERT INTO %s (`article_id`, `gruppeid_id`) VALUES (%2$d, 1), (%2$d, 10)',
				$this->cms->tbname['papoo_lookup_article'],
				$articleId
			);
			$this->db->query($sql);

			global $intern_artikel, $menu;

			// TODO: Anpassen, wenn eine Sprach-Auswahl gewünscht ist.
			// URL generieren, falls keine gesetzt wird oder diese Fehlerhaft ist
			$articleUrl = $importedArticle[$newConnectedFields['url_header']] && $importedArticle[$newConnectedFields['url_header']] != "" ??
				$intern_artikel->check_url_header($menu->replace_uml($importedArticle[$newConnectedFields['header']]), 1);

			// Artikel für Sprach-Tabelle
			$sql = sprintf("INSERT INTO %s SET
						`lan_repore_id` = %d,
						`lang_id` = %d,
						`header` = '%s',
						`lan_teaser` = '%s',
						`lan_teaser_link` = '%s',
						`lan_article` = '%s',
						`lan_article_sans` = '%s',
						`lan_metatitel` = '%s',
						`lan_metadescrip` = '%s',
						`lan_metakey` = '%s',
						`lan_rss_yn` = 1,
						`url_header` = '%s',
						`publish_yn_lang` = 1",
				$this->cms->tbname['papoo_language_article'],
				$articleId,
				// TODO: Anpassen, wenn eine Sprach-Auswahl gewünscht ist.
				1,
				$this->db->escape($importedArticle[$newConnectedFields['header']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_teaser']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_teaser_link']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_article']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_article']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_metatitel']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_metadescrip']] ?? ""),
				$this->db->escape($importedArticle[$newConnectedFields['lan_metakey']] ?? ""),
				$this->db->escape($articleUrl)
			);
			$this->db->query($sql);

			$importedArticleNumber++;
		}
		$this->content->template["ArtikelImportDone"] = true;
		$this->content->template["ImportedArticleNumber"] = $importedArticleNumber;
	}

	/**
	 * Gibt alle Daten des CSV's aus, nach ge eingestellten $settings
	 * @param $settings
	 * @return array
	 */
	private function getCsvData($settings): array
	{
		// Vorbestimmte Trennzeichen
		$delimiterArray = [
			'tab' => 0x09,
			'comma' => ',',
			'semicolon' => ';',
			'space' => 0x20,
		];
		$delimiter = $delimiterArray[$settings['delimiter']] ?? ",";

		// Vorbestimmte Encodings | TODO: Encodings ausgeben, die PHP unterstützt. (Dynamisch generiert) https://developer.mozilla.org/de/docs/Web/HTML/Element/optgroup | https://www.php.net/manual/de/function.mb-list-encodings.php
		$encodingArray = [
			'iso' => 'ISO-8859-1',
			'utf8' => 'UTF-8',
		];
		$encoding = $encodingArray[$settings['encodingType']] ?? "UTF-8";

		// Datei einlesen
		$file = file(PAPOO_ABS_PFAD . $_SESSION['ImportFile'], FILE_SKIP_EMPTY_LINES);
		$csv = array_map("str_getcsv", $file, array_fill($settings['fieldsNumber'], count($file), $delimiter));

		// In anderes Encoding converten, wenn es nicht UTF-8 sein soll
		if ($encoding != 'UTF-8') {
			array_walk($csv, function (&$row) use ($encoding) {
				$row = array_map(function ($entry) use ($encoding) {
					return mb_convert_encoding($entry, 'UTF-8', $encoding);
				}, $row);
			});
		}

		// Zeilen vor Feldnamen auswerfen
		for ($i = $settings['fieldsNumber'] - 1; $i > 0; $i--) {
			array_shift($csv);
		}

		// Spaltennamen aus CSV Nehmen
		$keys = array_shift($csv);

		// Array aus dem CSV und den Spaltennamen bauen
		foreach ($csv as $i => $row) {
			$csv[$i] = array_combine($keys, $row);
		}

		// CSV Zurückgeben
		return $csv;
	}

}

$artikel_import = new artikel_import();
