<?php

/**
 * Hier handelt es sich um eine kalender Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class kalender
 */
class kalender
{
	/**
	 * kalender constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#kalender")) {
				//Ausgabe erstellen
				$output=$this->create_kalenderintegration($output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript Kalender Liste an beliebiger Stelle";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten die Liste der aktuellen Termine eines Kalenders ausgeben lassen.<br /><strong>#kalenderliste_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Kalenders bezeichnen.<br /><strong>#kalenderliste_1_3#</strong><br />Wobei 3 der Anzahl an Terminen entspricht, die ausgegeben werden sollen. (M&ouml;gliche Werte: 0-15)";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_kalenderintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#kalenderliste(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_kalender($ndat['1'], (isset($ndat[2]) ? (int)$ndat[2] : -1));

			//Ersetzung durchführen
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Geänderten Inhalt zurückgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 * @param int $count Anzahl der Termine, die ausgegeben werden sollen. Ein Wert von 0 - 15 gibt die entsprechende Anzahl an Terminen aus, -1 gibt alle aus.
	 *
	 * @return false|mixed|string|string[]|null
	 */
	function get_kalender($id = 0, $count = -1)
	{
		global $checked;

		$checked->kal_id=$id;

		//Kalender einbinden
		$class_cal_front_cm = new class_cal_front();

		$class_cal_front_cm->kalender_id_extern=$id;
		$class_cal_front_cm->show_module_ok="ok";

		//Daten erzeugen
		$class_cal_front_cm->get_kalender_front();

		//Die Daten des Kalenders aus der Liste holen $class_cal_front_cm->alle_termin_ab_jetzt

		//Ins Template verarbeiten... kann man hier das Smarty Template nehmen?
		$kal_rahmen=file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/kalender/templates/kalender.html");
		$kal_daten=file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/kalender/templates/kalender_content.html");

		//Termine
		if (!isset($class_cal_front_cm->alle_termin_ab_jetzt[$id]) ||
			isset($class_cal_front_cm->alle_termin_ab_jetzt[$id]) && !is_array($class_cal_front_cm->alle_termin_ab_jetzt[$id])) {
			return "<strong>Es sind keine Termine im Kalender eingetragen oder der Kalender existiert nicht. - bitte ID korrigieren</strong>";
		}
		else {
			$termine = array_slice($class_cal_front_cm->alle_termin_ab_jetzt[$id], 0, ($count >= 0 ? $count : null), true);
			$kal_items="";
			foreach ($termine as $k=>$v) {
				$kal_item=$kal_daten;
				if ($v["pkal_date_start_datum"] == $v["pkal_date_end_datum"]) {
					foreach ($v as $k1=>$v1) {
						if ($k1=="pkal_date_start_datum") {
							$v1=date("d.m",$v1)." ".$v["pkal_date_uhrzeit_beginn"];
						}
						if ($k1=="pkal_date_end_datum") {
							$v1 = $v["pkal_date_uhrzeit_ende"];
						}
						$kal_item=str_ireplace("#".$k1."#",$v1,$kal_item);
					}
				}
				else {
					foreach ($v as $k1=>$v1) {
						if ($k1=="pkal_date_start_datum" || $k1=="pkal_date_end_datum") {
							$v1=date("d.m",$v1);
						}
						$kal_item=str_ireplace("#".$k1."#",$v1,$kal_item);
					}
				}
				$kal_items.=$kal_item;
			}
		}

		//Kalender Basisdaten
		foreach ($class_cal_front_cm->all_cals as $k=>$v) {
			if ($v['kalender_id']==$id) {
				foreach ($v as $k1=>$v1) {
					$kal_rahmen=str_ireplace("#".$k1."#",trim(strip_tags($v1)),$kal_rahmen);
				}
			}
		}
		//COntent $kal_items
		$kal_rahmen=str_ireplace("#kalender_content#",$kal_items,$kal_rahmen);

		//kalender zur�ckgeben.
		return $kal_rahmen;
	}
}

$kalender=new kalender();
