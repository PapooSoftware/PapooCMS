<?php

/**
 * Class tagcloud
 */
class tagcloud
{
	/**
	 * tagcloud constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#tagcloud")) {
				//Ausgabe erstellen
				$output=$this->create_tagcloud($output);
			}
		}
	}

	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][]="Skript TagCloud an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten die Papoo TagCloud ausgeben lassen, die Syntax lautet.<br /><strong>#tagcloud#</strong><br />";
		$this->content->template['plugin_cm_img']['de'][] = "";
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_tagcloud($inhalt = "")
	{
		// Ids rausholen
		preg_match_all("|#tagcloud(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			IfNotSetNull($ndat['1']);
			$banner_daten = $this->get_tagcloud_aus_plugin($ndat['1']);
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * @param int $anzahl
	 *
	 * @return mixed|string
	 */
	function get_tagcloud_aus_plugin($anzahl = 0)
	{
		// Wenn Tabelle existiert
		if (!empty($this->cms->tbname['papoo_keywords_liste'])) {
			$keyword = new keywords();
		}

		#$content->template['keywords_liste'] =  $keyword->keywordliste;
		#$content->assign();

		$return="";
		$template_cloud=file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/tagcloud/templates/cloud.html");
		$template_sub=file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/tagcloud/templates/cloud_sub.html");
		$template_org=$template_sub;
		$liste=($keyword->keywordliste);

		if (is_array($liste)) {
			foreach ($liste as $key=>$value) {
				//Url /cms-keywordplugin/keywords-front/barrierefrei/
				$url=$value['keywords_link'];
				$thema=		$value['keywords_liste_keyword'];
				$size=		$value['keywords_font_size'];

				$template=str_replace("#keywords_link#",$url,$template_org);
				$template=str_replace("#keywords_liste_keyword#",$thema,$template);
				$template=str_replace("#keywords_font_size#",$size,$template);
				$return.=$template;
			}
		}
		global $content;

		IfNotSetNull($content->template['message_plugin_keywords_schlagworte']);

		$template_cloud=str_replace("#message_plugin_keywords_schlagworte#", $content->template['message_plugin_keywords_schlagworte'], $template_cloud);
		$return=str_replace("#cloud_liste#", $return, $template_cloud);

		return $return;
	}
}

$tagcloud = new tagcloud();
