<?php
// #####################################
// # CMS Papoo                         #
// # (c) Carsten Euwens 2003           #
// # Authors: Carsten Euwens           #
// # http://www.papoo.de               #
// # Internet                          #
// #####################################
// # PHP Version 4.2                   #
// #####################################

if (stristr( $_SERVER['PHP_SELF'],'message_class.php')) die('You are not allowed to see this page directly');

/**
 * Class message_class
 */
class message_class
{
	/**
	 * message_class constructor.
	 */
	function __construct()
	{
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// Pfad einbinden

		$this->pfadhier = PAPOO_ABS_PFAD;
		//content Klasse
		global $content;
		$this->content = & $content;

		$this->einbinden();
	}

	/**
	 * @param string $verzeichnis
	 * @param int $fallback_level
	 */
	function einbinden($verzeichnis = "", $fallback_level = 0)
	{
		if (defined("admin")) {
			$front_back = "backend";
			$sprache = $this->cms->lang_back_short;
		}
		else {
			$front_back = "frontend";
			$sprache = $this->cms->lang_short;
		}

		$fallback_sprachen = array($sprache, "en", "de");

		if (empty($verzeichnis)) $verzeichnis = "/lib/messages";

		$datei = $this->pfadhier.$verzeichnis."/messages_".$front_back."_".$fallback_sprachen[$fallback_level].".inc.php";

		if (file_exists($datei)) {
			require_once($datei);
		}
		elseif ($fallback_level < 2) {
			$this->einbinden($verzeichnis, $fallback_level + 1);
		}
		else {
			if (empty($_GET['restore'])) {
				echo 'Fehler beim Einbinden der Sprach-Datei: "'.$datei.'"<br />\n';
			}
		}
	}
}

$message = new message_class();