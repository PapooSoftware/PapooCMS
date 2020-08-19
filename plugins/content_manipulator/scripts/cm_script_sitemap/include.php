<?php

namespace Papoo\Plugins\ContentManipulator\Scripts;

/**
 * Mit diesem Content-Manipulator Skript kann das Inhaltsverzeichnis an einer beliebigen Stelle mit dem Platzhalter #sitemap# eingefuegt werden.
 * @package Papoo\Plugins\ContentManipulator\Scripts
 * @author Christoph Zimmer
 * @date 2017-10-25
 */
class Sitemap
{
	/**
	 * Sitemap constructor.
	 */
	function __construct()
	{
		global $output, $content;
		$this->content = &$content;

		//Admin Ausgabe erstellen
		$this->set_backend_message();

		//Frontend - dann Skript durchlaufen
		if (defined("admin") == false) {
			$bodyOffset = strpos($output, "<body");
			$body = substr($output, $bodyOffset);

			$body = str_replace("#sitemap#", $this->makeSitemap(), $body);

			$output = substr_replace($output, $body, $bodyOffset);
		}
	}

	private function set_backend_message()
	{
		//Zuerst die Überschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][] = "Ausgabe der Sitemap";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][] = "Mit dem Platzhalter <strong>#sitemap#</strong> paltzieren Sie an beliebiger Stelle das Inhaltsverzeichnis Ihrer Seite, so als würden Sie den Menülink inhalt.php verwenden.";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = "";
	}

	/**
	 * Diese Funktion liefert das benoetigte Template. Es wird an diversen Orten gesucht.
	 *
	 * @return bool|string Das benoetigte Template als String, wenn gefunden, ansonsten false.
	 * @author Christoph Zimmer
	 */
	private function getTemplateFileName()
	{
		$template = "inhalt.html";

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$GLOBALS["cms"]->style_dir}/templates/$template",
			PAPOO_ABS_PFAD."/styles_default/templates/$template"
		], function ($template, $filename) {
			return $template !== false ? $template : (is_file($filename) ? $filename : $template);
		}, false);
	}

	/**
	 * @return bool|false|mixed|string|void
	 */
	private function makeSitemap()
	{
		/** @var \sitemap $sitemap */
		global $sitemap;
		/** @var \Smarty $smarty */
		global $smarty;

		if (($template = $this->getTemplateFileName()) !== false) {
			$error_reporting = error_reporting();
			error_reporting($error_reporting & ~E_NOTICE & ~E_STRICT);

			$smarty->assign("table_data", $sitemap->sitemap_table_build());
			return $smarty->fetch($template);
		}
		else {
			return "Fehler: Template `inhalt.html` nicht gefunden. Sitemap kann nicht ausgegeben werden.";
		}
	}
}

$cm_script_sitemap = new Sitemap();