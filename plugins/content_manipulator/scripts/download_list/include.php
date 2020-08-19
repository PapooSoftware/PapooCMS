<?php

/**
 * Class download_list
 */
class download_list
{
	/**
	 * download_list constructor.
	 */
	function __construct()
	{
		// Einbindung des globalen Content-Objekts
		global $content;
		$this->content = &$content;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;

			if (strstr( $output,"#download_list")) {
				// Ordnername
				$ordner = "dokumente/download_list"; //auch komplette Pfade möglich ($ordner = "download/files";)
				//Liste
				$list = '';
				//Ausgabe erstellen
				$list=$this->generate($ordner, $list);

				$list = "<ul class=\"accordion\" data-accordion id='download_list'>" . $list . "</ul>";

				$output = str_ireplace('#download_list#', $list, $output);
			}

			if (strstr( $output,"#download_list_en")) {
				// Ordnername
				$ordner = "dokumente/download_list_en"; //auch komplette Pfade möglich ($ordner = "download/files";)
				//Liste
				$list = '';
				//Ausgabe erstellen
				$list=$this->generate($ordner, $list);

				$list = "<ul class=\"accordion\" data-accordion id='download_list'>" . $list . "</ul>";

				$output = str_ireplace('#download_list_en#', $list, $output);
			}
		}
	}

	/**
	 * @param $ordner
	 * @param $list
	 *
	 * @return string
	 */
	function generate($ordner, $list)
	{
		// Ordner auslesen und Array in Variable speichern
		$alledateien = scandir($ordner); // Sortierung A-Z
		// Sortierung Z-A mit scandir($ordner, 1)

		foreach ($alledateien as $datei) {
			$dateiinfo = pathinfo($ordner . "/" . $datei);

			// Größe ermitteln zur Ausgabe
			$size = ceil(filesize($ordner . "/" . $datei) / 1024);
			//1024 = kb | 1048576 = MB | 1073741824 = GB

			if ($datei != "." && $datei != ".." ) {

				if($dateiinfo['extension']) {
					$img = '';
					switch (strtolower($dateiinfo['extension'])) {
					case 'jpg' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/29264.jpg" alt=".jpg" title=".jpg">';
						break;
					case 'doc' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/28790.jpg" alt=".doc" title=".doc">';
						break;
					case 'rar' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/28792.jpg" alt=".rar" title=".rar">';
						break;
					case 'zip' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/28814.jpg" alt=".zip" title=".zip">';
						break;
					case 'txt' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/28878.jpg" alt=".txt" title=".txt">';
						break;
					case 'tiff' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/29058.jpg" alt=".tiff" title=".tiff">';
						break;
					case 'xls' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/29070.jpg" alt=".xls" title=".xls">';
						break;
					case 'pdf' :
						$img = '<img src="'.PAPOO_WEB_PFAD.'/bilder/29099.jpg" alt=".pdf" title=".pdf">';
						break;
					}
					$dateiinfo['basename'] = str_ireplace(' ', '%20', $dateiinfo['basename']);
					$list = $list . "<div style=\"padding-bottom:10px;\">" . $img . " <a class=\"downloadlink\" target=\"_blank\" download href=". PAPOO_WEB_PFAD . "/" . $dateiinfo['dirname'] . "/" . $dateiinfo['basename'] . ">" . $dateiinfo['filename']. "</a> (" . $dateiinfo['extension'] . " | " . $size . "kb)</div>";
				}
				else {
					if(is_dir($ordner . '/' . $dateiinfo['basename'])){
						$list = $list . '<ul  class="accordion" data-accordion style="margin:0;padding-bottom:10px;"><li class="accordion-navigation"><a download href="#accord'. $dateiinfo['basename'] .'" class="accordion-title">' . $dateiinfo['basename'] . '</a><div id="accord'.$dateiinfo['basename'].'" class="content">';
						$list = $this->generate($ordner . '/' . $dateiinfo['basename'], $list);
						$list = $list . "</div></li></ul>";
					}
				}
			}
		};
		return $list;
	}
	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript download liste";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle eine Download Liste ausgeben lassen. Hierzu muss unter Dokumente der Ordner <b>download_list</b> oder <b>download_list_en</b> existieren, dort können Dateien reingelegt werden die ein Benutzer herrunterladen kann.<br /><strong>#download_list# => Für Ordner download_list</strong><br /><strong>#download_list_en# => Für Ordner download_list_en</strong><br /> ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}
}

$download_list=new download_list();
