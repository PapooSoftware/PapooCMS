<?php

/**
######################################
# CMS Papoo                          #
# (c) Dr. Carsten Euwens 2008        #
# Authors: Carsten Euwens            #
# http://www.papoo.de                #
# Internet                           #
######################################
# PHP Version 4.2                    #
######################################
 */

class download_class
{
	/** @var int Soll Download-Link mit weiteren Informationen angezeigt werden? (z.B. Anzahl-Downloads, Icon oder Datum letzter Download) */
	var $infos_anzeigen;
	/** @var int maximale Datei-Grö?e [Byte] für "direkten Download. Größere Dateien werden per Meta-Refresh "heruntergeladen" */
	var $max_datei_groesse;

	/**
	 * download_class constructor.
	 */
	function __construct()
	{
		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		$this->db = & $db;
		// cms Klasse einbinden
		global $cms;
		$this->cms = & $cms;
		// User Klasse einbinden
		global $user;
		$this->user = & $user;
		// Checked-Klasse einbinden
		global $checked;
		$this->checked = & $checked;
		// content-Klasse einbinden
		global $content;
		$this->content = & $content;

		// lokale Variblen initialisieren
		$this->infos_anzeigen = 1;
		$this->max_datei_groesse = 0;

		$this->make_download();
	}

	/**
	 * Aktions-Weiche der Download-Klasse
	 */
	function make_download()
	{
		if(isset($this->checked->downloadid) && $this->checked->downloadid) {
			$this->download_file($this->checked->downloadid);
		}
	}

	/**
	 * @param string $uri
	 */
	function do_redirect($uri)
	{
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$uri);
		header("Connection: close");
		exit();
	}

	/**
	 * den Download durchführen
	 * @param int $download_id
	 * @return bool|void
	 */
	function download_file($download_id = 0)
	{
		if (is_numeric($download_id)) {
			if (empty($this->download_sofort)) {

				/**
				 * @return bool
				 */
				function is_leadtracker_installed()
				{
					require_once(PAPOO_ABS_PFAD . "/lib/classes/intern_plugin_class.php");
					$intern_plugin = new intern_plugin();
					$intern_plugin->read_installed();
					foreach($intern_plugin->plugin_installed as $plugininfo) {
						if ($plugininfo['plugin_papoo_id'] == 98) {
							return true;
						}
					}
					return false;
				}
				//Erweiterung für das Leadtracker Plugin, läuft nur wenn das auch vorhanden ist
				// ... und installiert ist
				if (file_exists(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_download.php") and is_leadtracker_installed()) {
					require_once(PAPOO_ABS_PFAD."/plugins/leadtracker/lib/leadtracker_download.php");
					$leadtracker_download = new leadtracker_download_class();
					$leadtracker_download->do_lead_tracker_download();

					if (!$leadtracker_download->no_download_sofort) {
						return false;
					}
				}
			}

			// Prüfung ob Benutzer genügend Rechte hat um die Datei herunter zu laden
			if ($this->check_download_rights($download_id)) {
				// Temporarily disable CSRF protection
				$oldCsrfState = $this->db->csrfok;
				$this->db->csrfok = true;

				// Abfrage formulieren Downloads zählen
				$sql = "UPDATE " . $this->cms->papoo_download . " SET wieoft=wieoft+1, zeitpunkt=now() WHERE downloadid='" . $this->db->escape($download_id) . "'";
				// Abfrage durchführen
				$this->db->query($sql);

				// Revert CSRF protection to old state
				$this->db->csrfok = $oldCsrfState;

				$sql2 = "SELECT  downloadname  FROM " . $this->cms->papoo_language_download . " WHERE download_id='" . $this->db->escape($download_id) . "' AND lang_id='".$this->cms->lang_id."'";
				$filename2 =$this->db->get_var($sql2);
				//$filename2=str_ireplace(" ","_",$filename2);

				// Downloaddatei rausholen
				$sql = "SELECT * FROM " . $this->cms->papoo_download . " WHERE downloadid='" . $this->db->escape($download_id) . "'";
				$resultiert = $this->db->get_results($sql);
				// für jedes Ergebnis durchgehen
				foreach ($resultiert as $rowdown) {
					// Filename berichtigen
					$filename = basename($rowdown->downloadlink);

					$this->do_redirect($this->cms->webverzeichnis . $rowdown->downloadlink);

					$datei_groesse = round(filesize(PAPOO_ABS_PFAD . $rowdown->downloadlink), 0) . "kB - ";
					// Wenn die Datei größer als $this->max_datei_groesse ist, Meta Refresh Download durchführen, da ansonsten bei langsamen Verbindungen Timeout droht!! Im Zweifel sogar noch kleiner machen!!!
					if ($datei_groesse > $this->max_datei_groesse && empty($this->download_sofort)) {
						$this->refresh = '<meta http-equiv="refresh" content="0; url=' . $this->cms->webverzeichnis . $rowdown->downloadlink . '" />';
						$this->content->template['refresh'] = $this->refresh;
					}
					// Datei kleiner $this->max_datei_groesse
					else {
						//Größe ermitteln

						if (file_exists(PAPOO_ABS_PFAD . $rowdown->downloadlink)) {
							$size = filesize(PAPOO_ABS_PFAD . $rowdown->downloadlink);
						}
						// Header zuweisen
						header("Pragma: public");
						header("Expires: 0");
						header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						header("Content-Description: File Transfer");
						header("Accept-Ranges: bytes");
						//header("Content-Type: application/force-download");
						//header("Content-Type: application/download");
						// Wenn Opera oder MSIE auftauchen
						//if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT')) or preg_match('#MSIE ([0-9].[0-9]{1,2})#', getenv('HTTP_USER_AGENT'))) {
						//	header("Content-Type: application/octetstream");
						//} else {
						//	header("Content-Type: application/octet-stream");
						//}

						// Mime-Type der Datei festestellen und Content-Type festlegen
						$fragmente = explode(".", $filename);
						$endung = $fragmente[count($fragmente) - 1];

						switch ($endung) {
						case "pdf" :
							header("Content-Type: application/pdf");
							break;
						case "zip" :
							header("Content-Type: application/zip");
							break;
						case "doc" :
							header("Content-Type: application/msword");
							break;
						case "txt" :
							header("Content-Type: text/plain");
							break;
						default :
							header("Content-Type: application/octet-stream");
						}

						IfNotSetNull($size);
						header("Content-Length: $size ");
						header('Content-Disposition: attachment; filename="' . utf8_decode($filename2) .'.'. $endung .'"');

						// Send Content-Transfer-Encoding HTTP header
						// (use binary to prevent files from being encoded/messed up during transfer)
						header('Content-Transfer-Encoding: binary');

						// File auslesen
						@readfile(PAPOO_ABS_PFAD . $rowdown->downloadlink);

						// Wichtig, sonst gibt es Chaos!
						exit;
					}
				}
			}
		}
	}

	/**
	 * Download-Link-Ersetzungen
	 *
	 * @param string $text
	 * @return mixed|string
	 */
	function replace_downloadlinks($text = "")
	{
		if ($text) {
			// 1. Feststellen ob Download-Links existieren
			$downloadlink_array = $this->find_downloadlinks($text);

			if (!empty($downloadlink_array)) {
				// 2. Wenn Download-Links existieren..
				foreach ($downloadlink_array as $link_array) {
					$old_link = $link_array[0];

					// Wenn Link schon einmal bearbeitet, überspringe diesen
					if(preg_match("/class=\"[^\"]*downloadlink/", $old_link)) {
						continue;
					}

					// Prüfung ob Datei existiert
					if(!$this->check_download_file($link_array[2])) {
						$new_link = $link_array[3] . '<span style="font-size: 80%;"> ('.$this->content->template['download']['keine_datei'].')</span> ';
					}
					// Prüfen ob Benutzer das Recht zum herunterladen hat
					elseif ($this->check_download_rights($link_array[2]) < 1) {
						// Keine Rechte zum herunterladen
						$nachlinktext = ' (<strong class="error">'.$this->content->template['download']['kein_recht'].'</strong>)';
						$new_link = $link_array[3] . $nachlinktext;
					}
					else {
						// Menu-ID in Download-Link ersetzen
						$menuid = $this->checked->menuid;

						// Benutzer hat Rechte zum herunterladen:
						if ($this->infos_anzeigen && $this->cms->do_replace_ok || $this->cms->do_replace_ok && $menuid == 1) {
							$nachlinktext = $this->get_downloadlink_text($link_array[2]);
						}
						else {
							$nachlinktext = "";
						}

						// menuid mit 1 belegen, falls nicht vorhanden
						if ($menuid < 1) {
							$menuid = 1;
						}
						$link_array[0] = str_replace("=xxmenuidxx", "=".$menuid, $link_array[0]);

						$new_link = $link_array[0] . $nachlinktext;

						// Downloads in einem neuen Fenster, !!! stimmt bei großen Dateien nicht

						//$new_link = str_replace(' href="', ' title="'.$this->content->template['download']['link_title'].'" class="downloadlink" href="', $new_link);
						// Erhalt der evtl. vergebenen CSS-Klasse
						if (strpos($old_link, ' class="')) {
							$new_link = str_replace(' class="', ' class="downloadlink ', $new_link);
						}
						else {
							$new_link = str_replace(' href="', ' class="downloadlink" href="', $new_link);
						}

						if (!stristr($new_link,'title')) {
							$new_link = str_replace(' href="', ' title="'.$this->content->template['download']['link_title'].'" href="', $new_link);
						}

						// VVV diese Zeilen wieder einbauen, wenn ein direkter Link erwuenscht ist VVV
						// !!! ACHTUNG - die downloads koennen dann nicht mehr gezaehlt werden!
						//
						// Falls die Datei die Rechte "jeder" benötigt, dann ersetzen wir den Link
						// durch den direkt Link
						//
						// global $db_praefix;
						// $downloadid = $link_array[2];
						// $sql = sprintf("SELECT downloadlink FROM (SELECT * FROM %s A RIGHT JOIN %s B ON A.downloadid = B.download_id_id WHERE B.gruppen_id_id=10) C WHERE downloadid='%s'", $db_praefix . "papoo_download", $db_praefix . "papoo_lookup_download", $this->db->escape($downloadid));
						//
						// $result = $this->db->get_results($sql, ARRAY_A);
						// if(!empty($result)) {
						//    $new_link = preg_replace("/href=\".*?\"/", "href=\"" . PAPOO_WEB_PFAD . $result[0]['downloadlink'] . "\"", $new_link);
						// }
					}
					$text = str_replace($old_link, $new_link, $text);
				}
			}
		}
		return $text;
	}

	/**
	 * @param $text
	 * @return array|bool
	 */
	function find_downloadlinks($text)
	{
		$download_links = array ();
		$pattern = "/(<a [^>]{1,}downloadid=([0-9]*)[^>]{1,}>)(.*?)(<\/a>)/is";
		preg_match_all($pattern, $text, $download_links, PREG_SET_ORDER);

		if (!empty ($download_links)) {
			return $download_links;
		}
		else {
			return false;
		}
	}

	/**
	 * Download-Link-Ersetzungen
	 *
	 * @param $id
	 * @return bool
	 */
	function check_download_file($id)
	{
		$sql = sprintf("SELECT downloadlink FROM %s WHERE downloadid='%d' LIMIT 1",
			$this->cms->papoo_download,
			$this->db->escape($id)
		);
		$link = $this->db->get_var($sql);

		if (!empty($link) && file_exists(PAPOO_ABS_PFAD.$link)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Sicherheitsabfrage formulieren, ob die Datei vom User herunter geladen werden darf oder nicht.
	 *
	 * @param $id
	 * @return array|null
	 */
	function check_download_rights($id)
	{
		$sql = sprintf("SELECT COUNT(downloadid) FROM %s as T1, %s as T2, %s as T3
						WHERE T1.downloadid='%d' AND T1.downloadid=T2.download_id_id
						AND T2.gruppen_id_id=T3.gruppenid AND T3.userid='%d'
						LIMIT 1",
			$this->cms->papoo_download,
			$this->cms->papoo_lookup_download,
			$this->cms->papoo_lookup_ug,
			$this->db->escape($id),
			$this->user->userid);
		return $this->db->get_var($sql);
	}

	/**
	 * @param int $download_id
	 * @return string|void
	 */
	function get_downloadlink_text($download_id = 0)
	{
		if ($download_id) {
			// Daten aus Tabelle download holen
			$query = sprintf("SELECT T1.downloadlink, T1.downloadgroesse,
							T1.wieoft, DATE_FORMAT(T1.zeitpunkt, '%%d.%%m.%%Y') as datum, T2.downloadname
							FROM %s as T1, %s as T2 WHERE T1.downloadid='%d' AND T1.downloadid=T2.download_id",
				$this->cms->papoo_download,
				$this->cms->papoo_language_download,
				$this->db->escape($download_id));
			$result = $this->db->get_results($query);

			if (!empty ($result)) {
				//Größe ermitteln
				if (file_exists(PAPOO_ABS_PFAD . $result[0]->downloadlink)) {
					//Verwendung von filesize macht das Handling mit großen Datein deutlich einfacher!!
					$datei_groesse = round(filesize(PAPOO_ABS_PFAD . $result[0]->downloadlink) / 1024, 0) . " kB";
					#$datei_groesse = round($result[0]->downloadgroesse / 1024, 0) . "kB - ";
					if ($datei_groesse > 1000) {
						$datei_groesse = round($datei_groesse / 1024, 2) . " MB";
					}

					if (strpos($result[0]->downloadlink, ".pdf")) {
						$icon_span = '<span class="pdf-icon" style="padding-left: 20px;'
							.' background-image: url('.$this->cms->webverzeichnis.'/bilder/icon_pdf.gif);'
							.' background-repeat: no-repeat; background-position: 5px 0;"> </span>';
					}
					else {
						$icon_span = "";
					}

					//$text = "<small>" . $datei_icon . " (Gr&ouml;&szlig;e: " . $datei_groesse . " Downloads bisher: " . $result[0]->wieoft . "; Letzter Download am: " . $result[0]->datum . ") </small>";
					$text = $icon_span.'<span class="download-info" style="font-size: 80%;"> ('.$this->content->template['download']['info_01'].': '.$datei_groesse
						.'; '.$this->content->template['download']['info_02'].': '.$result[0]->wieoft
						.'; '.$this->content->template['download']['info_03'].': '.$result[0]->datum.')</span> ';
				}
				// Datei existiert nicht mehr
				else {
					$text = '<span style="font-size: 80%;"> ('.$this->content->template['download']['keine_datei'].')</span> ';
				}
			}
		}
		IfNotSetNull($text);
		return $text;
	}
}

$download = new download_class();