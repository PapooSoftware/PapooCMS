<?php

/**
 * Class bildwechsler
 */
class bildwechsler
{
	/**
	 * bildwechsler constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgabe erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp'] == 1)) {
			//Fertige Seite einbinden
			global $output;

			//Nur was machen, wenn der Platzhalter gefunden wird
			if (strstr( $output,"#bildwechsler#")) {
				$js = '<script>
				function outerHTML(node){
					return node.outerHTML || new XMLSerializer().serializeToString(node);
				}
				bw = document.getElementById("mod_bildwechsler");
				bwHtml = bw.outerHTML;
				bw.parentNode.removeChild(bw);
				document.getElementById("mod_bildwechsler_dummy").outerHTML = bwHtml;
				</script>';

				$this->replace_in_output('#bildwechsler#', '<div id="mod_bildwechsler_dummy"></div>' . $js);
			}

		}
	}

	/**
	 * @param string $strFindText
	 * @param string $strReplaceText
	 */
	function replace_in_output($strFindText="", $strReplaceText="")
	{
		global $output;
		$output = str_ireplace($strFindText, $strReplaceText, $output);
	}

	function set_backend_message()
	{
		//�berschrift - de = Deutsch; en = Englisch
		$this->content->template['plugin_cm_head']['de'][]="Skript bildwechsler an beliebiger Stelle";

		//Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem Skript kann man ein vorhandenes Bildwechsler-Modul an eine beliebige Stelle verschieben. Die Syntax lautet:<br /><strong>#bildwechsler#</strong><br />";

		//Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}
}

$bildwechsler=new bildwechsler();
