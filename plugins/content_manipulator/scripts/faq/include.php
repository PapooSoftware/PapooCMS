<?php

/**
 * Hier handelt es sich um eine faq_cm Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class faq_cm
 */
class faq_cm
{
	/**
	 * faq_cm constructor.
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
			if (strstr( $output,"#faq_")) {
				//Ausgabe erstellen
				$output=$this->create_faq_cmintegration($output);
			}
		}
	}

	/**
	 * Hiermit setzt man die Eintr�ge in der Administrations�bersicht
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		//Zuerst die �berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript FAQ an beliebiger Stelle";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten die Fragen und Antworten einer FAQ Kategorie oder alle FAQ Einträge ausgeben lassen.Die Syntax lautet.<br /><strong>#faq_kat_1_templ_1#</strong><br />Damit geben Sie die FAQ einer bestimmten Kategorie aus. Es werden nur die FAQ Einträge ohne die Kategorie Headline ausgegeben. kat_1 bedeutet nutze Kategorie 1, templ_1 = Template 1. Derzeit gibt es 2 verschiedene Anpassungen k&ouml;nnen jederzeit gemacht werden.";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_faq_cmintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#faq_(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_faq_cm($ndat['1'],$ndat['3']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $id
	 * @param int $tmpl
	 *
	 * @return string
	 */
	function get_faq_cm($id = 0,$tmpl=1)
	{
		//faq_cm zur�ckgeben.
		//return "Dies ist der faq_cmtext Nr: ".$id;

		//Einträge der Kategorie rausholen
		$faqs= $this->get_faq_from_kat($id);

		if ($tmpl<1) {
			$tmpl=1;
		}
		//Get Templatedata
		$tmpl = @file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/faq/templates/faq_".$tmpl.".html");

		//Daraus daten erstellen
		$ready=$this->create_html_from_data($faqs,$tmpl);

		return $ready;
	}

	/**
	 * @param array $faqs
	 * @param string $tmpl
	 *
	 * @return string
	 */
	private function create_html_from_data($faqs=array(), $tmpl="")
	{
		//INI
		$html="";

		//Daten durchgehen
		foreach ($faqs as $k=>$v) {
			//Für jeden Datensatz Template neu setzen
			$tpl2=$tmpl;
			foreach ($v as $k1=>$v1) {
				//Erstetzung
				$tpl2=str_ireplace("#".$k1."#",$v1,$tpl2);
			}
			$html.=$tpl2;
		}
		return $html;
	}

	/**
	 * @param int $id
	 *
	 * @return array|void
	 */
	private function get_faq_from_kat($id=0)
	{
		$sql=sprintf("SELECT * FROM %s AS T1
			LEFT JOIN %s AS T2  ON faq_id=id
                      WHERE cat_id='%d'
                      AND T1.version_id = T2.version_id

		      GROUP BY faq_id
		      ORDER BY order_id ASC
		      ",
			DB_PRAEFIX."papoo_faq_cat_link",
			DB_PRAEFIX."papoo_faq_content",
			$this->db->escape($id));
		$result=$this->db->get_results($sql,ARRAY_A);

		return $result;
	}
}

$faq_cm=new faq_cm();
