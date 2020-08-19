<?php
/**
#####################################
# CMS Papoo                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >5                    #
#####################################
 */

/**
 * Class do_it_tidy
 *
 * Mit dieser Klasse sollen einige groben Validierungsfehler behoben werden, die
 * unter papoo bedingt durch Eingabe fehler und andere Probleme entstehe
 *
 * Ab PHP5 steht auch eine echte tidy Funktion zur Verfügung.
 *
 * TODO: Warum ist das dann noch hier?
 *
 */
class do_it_tidy {
	/**
	 * durchführen aller Funktionen am Inhalt
	 *
	 * @param $inhalt
	 * @return string
	 */
	function do_tidy($inhalt) {
		//Escape Zeichen entfernen // SCHROTT !!!
		//$inhalt = stripslashes($inhalt);

		// hiermit werden alle & in den url in &amp; umgewandelt // SCHROTT !!!
		//$inhalt = $this->do_ampersand($inhalt);

		// hiermit wird das HTML gerichtet.
		//$inhalt = $this->valid_tidy($inhalt);

		// hiermit wird XHTML Makrup erzeugt.
		$inhalt = $this->do_it_xhtml($inhalt);

		// der Inhalt wird zurück gegeben
		return $inhalt;
	}

	/**
	 * hiermit werden  alle & mit &amp; ersetzen
	 *
	 * @return mixed $inhalt_map
	 * @param $inhalt_amp
	 * @desc hiermit werden  alle & mit &amp; ersetzen
	 */
	function do_ampersand($inhalt_amp) {
		// alle & mit &amp; ersetzen
		//$inhalt_amp = str_ireplace("&", "&amp;", $inhalt_amp);
		// Inhalt zurückgeben
		return $inhalt_amp;
	}

	/**
	 * @param $inhalt_tidy
	 * @return mixed
	 */
	function valid_tidy($inhalt_tidy)
	{
		// Validierungs funktionen durchführen
		return $inhalt_tidy;

		/*
		$inhalt_tidy = str_ireplace("<p><h2>", "<h2>", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("<p><br />", "<br />", $inhalt_tidy);
		$inhalt_tidy = preg_replace('/<br \/>/', '<br />', $inhalt_tidy);
		$inhalt_tidy = preg_replace('/<br \/><br \/>/', '</p><p>', $inhalt_tidy);
		
		// Inhalt zurückgeben
		return $inhalt_tidy;
		*/
	}

	/**
	 * Einige Umwandlungen für schlechten Code durchführen
	 *
	 * @param $inhalt_tidy
	 * @return mixed|string|string[]|null
	 */
	function do_it_xhtml($inhalt_tidy)
	{

		#$inhalt_tidy = str_ireplace("<br /></li>", "</li>", $inhalt_tidy);
		//$inhalt_tidy= str_ireplace("<p />","<br />",$inhalt_tidy);
		#$inhalt_tidy = str_ireplace("</b>", "</strong>", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace("<b>", "<strong>", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace("<i>", "<em>", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace("</i>", "</em>", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace("<u>", "<span style=\"text-decoration:underline;\">", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace("</u>", "</span>", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("<font size=\"", "<span style=\"font-size:1", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("<font color=\"", "<span style=\"color:", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("<font face=\"", "<span style=\"font:", $inhalt_tidy);
		#$inhalt_tidy = str_ireplace(" align=\"", " style=\"text-align:", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("<font", "<span", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("</font>", "</span>", $inhalt_tidy);


		/*
		$inhalt_tidy = str_ireplace("&amp;amp;", "&amp;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&", "&amp;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;amp;", "&amp;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;nbsp;", "&nbsp;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;uuml;", "&uuml;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;auml;", "&auml;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;ouml;", "&ouml;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;szlig;", "&szlig;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;gt;", "&gt;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;lt;", "&lt;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;quot;", "&quot;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;copy;", "&copy;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;bdquo;", "&bdquo;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;ldquo;", "&ldquo;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;reg;", "&reg;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace("&amp;raquo;", "&raquo;", $inhalt_tidy);
		$inhalt_tidy = str_ireplace('"&amp;', "\"&", $inhalt_tidy);
		*/

		$inhalt_tidy = preg_replace('/mce_href="(.*?)"/',"" ,$inhalt_tidy);
		$inhalt_tidy = preg_replace('/mce_style="(.*?)"/',"" ,$inhalt_tidy);
		$inhalt_tidy = preg_replace('/mce_src="(.*?)"/',"" ,$inhalt_tidy);

		// Inhalt zurückgeben
		return $inhalt_tidy;
	}

	/**
	 * Echte Tidy Funktionen durchführen (PHP5).
	 *
	 * @param $inhalt
	 * @return void
	 */
	function make_tidy($inhalt)
	{
		global $cms;

		// echte PHP5 tidy Funktion durchführen, wenn diese existieren
		if (function_exists('tidy_parse_string') && $cms->config_html_tidy_funktionaktivieren==1) {
			$config = array (
				'bare' => TRUE,
				'clean' => TRUE,
				'drop-proprietary-attributes' => TRUE,
				'output-xhtml' => TRUE,
				'logical-emphasis' => TRUE,
				'show-body-only' => TRUE,
				'indent' => TRUE,
				'word-2000' => TRUE,
				'force-output' => TRUE,
				'accessibility-check' => 3,
				'wrap' => 1,
				'new-blocklevel-tags' => 'header, footer, article, section, hgroup, nav, figure',
				'drop-empty-paras' => FALSE
			);

			// Inhalt nach den obigen Optionen parsen
			//$tidy = tidy_parse_string($inhalt, $config);
		}
		else {
			$tidy = $inhalt;
		}
	}

	/**
	 * Echte Tidy Funktionen durchführen (PHP5) fürs Frontend.
	 * @param $inhalt
	 * @return mixed|string|string[]|tidy|void|null
	 */
	function make_tidy_front($inhalt)
	{
		$tidy=$inhalt;
		$tidy=$this->do_it_xhtml($tidy);
		global $cms;

		// echte PHP5 tidy Funktion durchführen, wenn diese existieren
		if (function_exists('tidy_parse_string') && $cms->config_html_tidy_funktionaktivieren==1) {
			$inhalt=str_replace("&nbsp;","###########",$inhalt);
			$config = array (
				'clean' => TRUE,
				'output-xhtml' => TRUE,
				'output-xml' => FALSE,
				'force-output' => TRUE,
				'logical-emphasis' => TRUE,
				'join-styles' => FALSE,
				'merge-divs' => FALSE,
				'input-encoding' => "utf-8",
				'output-encoding' => "utf-8",
				'add-xml-decl' => FALSE,
				'quote-marks' => TRUE,
				'css-prefix' => FALSE,
				'indent' => TRUE,
				'wrap' => 0,
				'new-blocklevel-tags' => 'header, footer, article, section, hgroup, nav, figure',
				'drop-empty-paras' => FALSE
			);

			// Inhalt nach den obigen Optionen parsen
			$tidy = tidy_parse_string($inhalt, $config, 'UTF8');
			// Inhalt reparieren
			$tidy->cleanRepair();

			$tidy=str_replace("###########","&nbsp;",$tidy);

			$tidy = str_ireplace("<\?xml version=\"1.0\"\?\>", "", $tidy);
			if (!stristr($tidy,'<!DOCTYPE')) {
				$tidy = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.$tidy;
			}

			// $tidy=preg_replace('/name=".*? >/s','>',$tidy);
			$tidy = str_ireplace('name="suche">', '>', $tidy);
			$tidy = str_ireplace('name="suchefor">', '>', $tidy);
			$tidy = str_ireplace(" name=\"styleswitcher\">", "", $tidy);
			$tidy = str_ireplace(" method=\"post\" name=\"artikel\"", " method=\"post\"", $tidy);
			if (!strstr( $tidy,'bilder/poweredbypapoo3.png')) {
				$tidy = str_ireplace('</body>', '<div style="display: none;height: 0; width:0; left: -1000px; overflow:hidden; position: absolute; top: -1000px;">.<h2 class="ignore">xxnoxx_zaehler</h2></div></body>', $tidy);
			}
			//google_ads catchen, damit die auch zu sehen sind
			if (strstr($tidy,"google_ad_client")) {
				$tidy = str_ireplace('<!-- google_ad_client',"<!-- \n google_ad_client",$tidy);
			}
		}

		//$tidy = str_ireplace('name="suche">', '>', $inhalt);
		//$tidy = str_ireplace(" name=\"styleswitcher\">", "", $tidy);
		//$tidy = str_ireplace(" method=\"post\" name=\"artikel\"", " method=\"post\"", $tidy);

		if (!stristr( $tidy,'bilder/poweredbypapoo3.png')) {
			$tidy = str_ireplace('</body>', '<div style="display:none; height: 0; width:0; left: -1000px; overflow:hidden; position:absolute; top:-1000px;">.<h2 class="ignore">xxnoxx_zaehler</h2></div></body>', $tidy);
		}

		return $tidy;
	}

	/**
	 * @param $inhalt
	 * @return mixed
	 */
	function do_smilies($inhalt)
	{
		$inhalt = str_replace(":-)", '<img src="./bilder/smilies/smile.gif" />', $inhalt);
		$inhalt = str_replace(":-(", '<img src="./bilder/smilies/traurig.gif" />', $inhalt);
		$inhalt = str_replace(":-D", '<img src="./bilder/smilies/grins.gif" />', $inhalt);
		$inhalt = str_replace(";-)", '<img src="./bilder/smilies/zwinker.gif" />', $inhalt);
		$inhalt = str_replace(":-o", '<img src="./bilder/smilies/erstaunt.gif" />', $inhalt);
		$inhalt = str_replace(":-O", '<img src="./bilder/smilies/erstaunt.gif" />', $inhalt);
		$inhalt = str_replace("8-)", '<img src="./bilder/smilies/cool.gif" />', $inhalt);
		$inhalt = str_replace(":-?", '<img src="./bilder/smilies/fragend.gif" />', $inhalt);
		$inhalt = str_replace(":-p", '<img src="./bilder/smilies/zunge_raus.gif" />', $inhalt);
		$inhalt = str_replace(":-P", '<img src="./bilder/smilies/zunge_raus.gif" />', $inhalt);
		$inhalt = str_replace(":-I", '<img src="./bilder/smilies/boese.gif" />', $inhalt);
		return $inhalt;
	}
}

$html = new do_it_tidy();