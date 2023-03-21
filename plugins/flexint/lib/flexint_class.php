<?php

/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.2                  #
 * #####################################
 */

/**
 * Class flexintplugin
 */
#[AllowDynamicProperties]
class flexintplugin
{

	/** @var string */
	var $news = "";

	/**
	 * flexintplugin constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		global $cms, $db, $message, $user, $weiter, $content, $searcher, $checked, $mail_it, $replace, $db_praefix, $intern_stamm, $diverse;

		// und einbinden in die Klasse
		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->searcher = &$searcher;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->replace = &$replace;
		$this->diverse = &$diverse;

		$this->make_flexintplugin();
		$this->content->template['plugin_message'] = "";
	}

	function make_flexintplugin()
	{
		global $template;

		$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
		$template2 = basename( $template2 );

		$this->user->check_intern();

		if ($template2 != "login.utf8.html") {

			if (stristr($template2,"flexint")) {
				//Eintragen

				if (is_numeric($this->checked->flexint_id_new) or is_numeric($this->checked->flexint_id)) {
					if (!stristr( $template2,"flexintfront")) {
						//lang_id zur gew�hlten Sprache (Flaggen-Auswahl) setzen f�r tiny und Tabelle flexint_texte_lang
						//gilt f�r neue und zu bearbeitende S�tze im Backen, nicht im Frontend
						global $intern_artikel;
						$this->intern_artikel = & $intern_artikel;
						$this->intern_artikel->set_replangid();
						if ($this->checked->flexint_id) $this->get_texte(); // nur bei Edit
					}
					if (!empty($this->checked->flexint_id_new)) {
						//Formular anzeigen
						$this->content->template['flexint_id_new'] = 1;
						//WEnn neu dann speichern
						if (!empty($this->checked->submitflexint)) {
							$sql = sprintf( "INSERT INTO %s SET 
														flexint_url='%s', 
														flexint_url_sans = '%s', 
														flexint_name = '%s', 
														flexint_unterverzeichnis = '%s'",
								$this->cms->tbname['papoo_flexint_flexinttabelle'],
								$this->db->escape($this->checked->flexint_url),
								$this->db->escape($this->checked->flexint_url_sans),
								$this->db->escape($this->checked->flexint_name),
								$this->db->escape($this->checked->flexint_unterverzeichnis));
							$this->db->query( $sql );
							$insertid = $this->db->insert_id;

							$sql = sprintf( "INSERT INTO %s SET
														flexint_id = %s,
														flexint_langid = %s,
														flexint_text_oben='%s',
														flexint_text_unten='%s'",
								$this->cms->tbname['papoo_flexint_texte_lang'],
								$insertid,
								$this->content->template['tinymce_lang_id'],
								$this->db->escape($this->checked->flexint_text_oben),
								$this->db->escape($this->checked->flexint_text_unten) );
							$this->db->query( $sql );
							$this->flexint_reload( "fertig_new" );
						}
					}
					if (!empty($this->checked->flexint_id)) {
						$this->content->template['flexint_id'] = 1;
						//Daten eintragen
						if (!empty($this->checked->submitflexint)) {
							$sql = sprintf( "UPDATE %s SET flexint_url='%s', 
												flexint_url_sans='%s', 
												flexint_name='%s', 
												flexint_unterverzeichnis='%s'
												WHERE flexint_id='%d'",
								$this->cms->tbname['papoo_flexint_flexinttabelle'],
								$this->db->escape($this->checked->flexint_url),
								$this->db->escape($this->checked->flexint_url_sans),
								$this->db->escape($this->checked->flexint_name),
								$this->db->escape($this->checked->flexint_unterverzeichnis),
								$this->db->escape($this->checked->flexint_id) );
							$this->db->query( $sql );

							//Bei bestehender Installation fehlt der Satz evtl. noch 
							$sql = sprintf( "SELECT * FROM %s
												WHERE flexint_id='%d' AND flexint_langid = %s",
								$this->cms->tbname['papoo_flexint_texte_lang'],
								$this->db->escape($this->checked->flexint_id),
								$this->content->template['tinymce_lang_id']);
							$result = $this->db->get_var($sql);
							if ($result) { // Satz ist vorhanden
								$sql = sprintf( "UPDATE %s SET
													flexint_langid = '%s',
													flexint_text_oben='%s',
													flexint_text_unten='%s'
													WHERE flexint_id='%d' AND flexint_langid = %s",
									$this->cms->tbname['papoo_flexint_texte_lang'],
									$this->content->template['tinymce_lang_id'],
									$this->db->escape($this->checked->flexint_text_oben),
									$this->db->escape($this->checked->flexint_text_unten),
									$this->db->escape($this->checked->flexint_id),
									$this->content->template['tinymce_lang_id'] );
							}
							else { // Satz ist neu
								$sql = sprintf( "INSERT INTO %s SET
														flexint_id = %s,
														flexint_langid = %s,
														flexint_text_oben='%s',
														flexint_text_unten='%s'",
									$this->cms->tbname['papoo_flexint_texte_lang'],
									$this->db->escape($this->checked->flexint_id),
									$this->content->template['tinymce_lang_id'],
									$this->db->escape($this->checked->flexint_text_oben),
									$this->db->escape($this->checked->flexint_text_unten) );
							}
							$this->db->query( $sql );
							$this->flexint_reload( "fertig_update" );
						}
						//Daten rausholen
						$this->get_url_one();
					}
				}
				//Auslesen
				else {
					//Liste der Eintr�ge rausholen
					$this->make_flexliste();
					//Anzeige message
					$this->make_flex_message();
				}
				//Menuid zuweisen
				$this->content->template['menuid_back_aktuell'] = $this->checked->menuid;
			}
		}
	}

	function make_flex_message()
	{
		//Inhalt �bergeben
		$this->content->template['flex_message'] = $this->checked->flex_var;
	}

	/**
	 * flexintplugin::flexint_reload()
	 * Diese Funktion l�d das Plugin neu
	 *
	 * @param string $var
	 * @return void
	 */
	function flexint_reload( $var = "" )
	{
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
			"&template=" . $this->checked->template . "&flex_var=" . $var;
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	function make_flexliste()
	{
		$this->content->template['flexliste'] = "OK";
		$this->get_url_all();
	}

	/**
	 * URL rausholen f�r alle Ints
	 */
	function get_url_all()
	{
		//Daten auslesen
		$sql = sprintf( "SELECT * FROM %s ", $this->cms->tbname['papoo_flexint_flexinttabelle'] );
		$result = $this->db->get_results( $sql, ARRAY_A );

		$this->content->template['flexint_result'] = $result;
		return $result['0']['flexint_url'];
	}

	/**
	 * flexintplugin::get_url_one()
	 * Nur eine INteg rausholen
	 *
	 * @return array|void
	 */
	function get_url_one()
	{
		//Daten auslesen
		$sql = sprintf( "SELECT * FROM %s WHERE flexint_id='%d'", $this->cms->tbname['papoo_flexint_flexinttabelle'],
			$this->db->escape($this->checked->flexint_id) );
		$result = $this->db->get_results( $sql, ARRAY_A );

		$this->content->template['flexint_result'] = $result;
		$this->subverzeichnis = $result['0']['flexint_unterverzeichnis'];
		return $result['0']['flexint_url'];
	}

	/**
	 * flexintplugin::get_texte()
	 * Texte f�r Frontend top und bottom holen
	 * @return array|void
	 */
	function get_texte()
	{
		$sql = sprintf( "SELECT * FROM %s WHERE flexint_id='%d' AND flexint_langid = %s",
			$this->cms->tbname['papoo_flexint_texte_lang'],
			$this->checked->flexint_id,
			$this->content->template['tinymce_lang_id']);
		$result = $this->db->get_results( $sql, ARRAY_A );
		$this->content->template['flexint_back_texte'] = $result;
		return $result;
	}

	/**
	 * URL rausholen
	 */
	function get_seite_url()
	{
		//Daten auslesen
		$sql = sprintf( "SELECT flexint_url_sans FROM %s WHERE flexint_id='%d' LIMIT 1",
			$this->cms->tbname['papoo_flexint_flexinttabelle'], $this->db->escape($this->checked->flexint_id) );
		$result = $this->db->get_results( $sql, ARRAY_A );

		return $result['0']['flexint_url_sans'];
	}

	/**
	 * flexintplugin::get_vars()
	 *
	 * @return array Liste der Get Variablen
	 */
	function get_vars()
	{
		$get = "";
		foreach ($_GET as $key => $value) {
			if ($key == "template") {
				continue;
			}
			if ($key == "var1" or $key == "var2" or $key == "var3") {
				continue;
			}
			$key = strip_tags( htmlentities($key) );
			$value = strip_tags( htmlentities($value) );
			$get .= "&" . $key . "=" . $value . "";
		}

		$get = str_ireplace( "template_fremd", "template", $get );
		return $get;
	}

	/**
	 * Wenn wir drau�en sind
	 */
	function post_papoo()
	{
		if (!defined("admin")) {

			if (stristr($this->checked->template,"flexintfront")) {
				//Wenn es sich um eine Flex Geschichte handelt
				if (stristr( $this->checked->template_fremd,"mv") || empty($this->checked->template_fremd)) {
					$ausgabe = $this->get_mv_daten();
				}
				//Wenn es sich um eine Formmanager Geschichte handelt
				if (stristr($this->checked->template_fremd,"manager")) {
					$ausgabe = $this->get_formmanger_daten();
				}

				$back = utf8_encode( "zur�ck" );
				#$this->content->template['charset']="UTF-8";

				$sql = sprintf( "SELECT * FROM %s T1, %s T2 WHERE T2.flexint_id='%d' AND T2.flexint_langid = %s",
					$this->cms->tbname['papoo_flexint_flexinttabelle'],
					$this->cms->tbname['papoo_flexint_texte_lang'],
					$this->db->escape($this->checked->flexint_id),
					$_SESSION['langid_front'] );
				$result = $this->db->get_results( $sql, ARRAY_A );

				$this->content->template['flexint_texte'] = $result;
				$this->content->template['flexint'] = ( "nobr:" . $ausgabe );
			}
		}
	}
	/**
	 * flexintplugin::get_mv_daten()
	 * Hier werden die Flexdaten verarbeitet
	 * aus einer externen Flexverwaltung
	 * @return void
	 */
	function get_formmanger_daten()
	{
		require_once ( PAPOO_ABS_PFAD . "/lib/classes/third-party/Snoopy.class.inc.php" );

		$seite = $this->get_url_one();
		$seite_url = $this->get_seite_url();
		$get_vars = $this->get_vars();

		$url = $seite . $get_vars;
		$titel = $_GET['flexint'];
		$html = new Snoopy();
		$html->agent = "Web Browser";
		$html->referer = $_SERVER["HTTP_REFERER"];
		if (!empty($_SESSION['kookies'])) {
			$html->cookies=$_SESSION['kookies'];
		}
		#$html->cookies['PHPSESSID']="";
		//Wenn das Formular verschickt wurde
		if (!empty($this->checked->form_manager_submit)) {
			foreach ($_POST as $key=>$value) {
				$formvar[$key]=stripslashes($value);
			}
			$html->submit($url,$formvar);
		}
		else {
			$html->fetch( $url );
		}
		$html->setcookies();

		$_SESSION['kookies']=$html->cookies;
		$daten = $html->results;

		//TODO: Bla
		$daten1 = explode( '<!-- START FORM -->', $daten );
		//<div class="printfooter">
		$daten2 = explode( '<!-- STOP FORM -->', $daten1['1'] );
		$zwischen = $daten2['0'];

		//<input value="1" name="menuid" type="hidden">
		return $zwischen;
	}

	/**
	 * flexintplugin::get_mv_daten()
	 * Hier werden die Flexdaten verarbeitet
	 * aus einer externen Flexverwaltung
	 * @return void|string
	 */
	function get_mv_daten()
	{
		require_once (PAPOO_ABS_PFAD . "/lib/classes/third-party/Snoopy.class.inc.php");
		$seite = $this->get_url_one() . "&getlang=" . $this->cms->lang_short;
		$seite_url = $this->get_seite_url();
		$get_vars = $this->get_vars();
		$get_vars = $this->HTMLEntities_to_literals($get_vars);
		$url = $seite . $get_vars;
		$titel = $_GET['flexint'];
		$html = new Snoopy();
		$html->agent = "Web Browser";
		$html->referer = $_SERVER["HTTP_REFERER"];
		$html->fetch( $url );
		$daten = $html->results;
		$daten1 = explode( '<!-- START FLEX -->', $daten );
		//<div class="printfooter">
		$daten2 = explode( '<!-- STOP FLEX -->', $daten1['1'] );
		$zwischen = $daten2['0'];
		//Externe Links gehen ins Blank
		$zwischen = str_ireplace( "href=\"http://", "target=\"blank\" href=\"http://", $zwischen );

		//Unterverzeichnis ersetzen $this->subverzeichnis
		$zwischen = str_ireplace( 'href="' . $this->subverzeichnis . "/", 'href="', $zwischen );

		//Bilder korriegieren /images/
		$zwischen = str_ireplace( "/images/", $seite_url . "/images/", $zwischen );

		//template Variable verschleiern
		$zwischen = str_ireplace( 'name="template"', 'name="template_fremd"', $zwischen );

		//Download zu files umbiegen
		$zwischen = str_ireplace( 'href="files', 'href="' . $seite_url . '/files', $zwischen );

		//Action umbieren
		$zwischen = str_ireplace( 'action="/plugin.php"', 'action="' . PAPOO_WEB_PFAD .
			'/plugin.php"', $zwischen );
		$zwischen = str_ireplace( 'action="plugin.php"', 'action="' . PAPOO_WEB_PFAD .
			'/plugin.php"', $zwischen );
		$zwischen = str_ireplace( '</form>',
			'<input value="flexint/templates/flexintfront.html" name="template" type="hidden"><input value="' .
			$this->checked->flexint_id . '" name="flexint_id" type="hidden"></form>', $zwischen );

		//template Varialen korrigieren
		$zwischen = str_ireplace( 'template=',
			'template=flexint/templates/flexintfront.html&template_fremd=', $zwischen );
		//Links korrigieren
		$zwischen = str_ireplace( 'href="/plugin.php', 'href="' . PAPOO_WEB_PFAD .
			'/plugin.php', $zwischen );
		$zwischen = str_ireplace( 'href="plugin.php', 'href="' . PAPOO_WEB_PFAD .
			'/plugin.php', $zwischen );
		$zwischen = str_ireplace( 'menuid=', 'menuid=' . $this->checked->menuid .
			'&flexint_id=' . $this->checked->flexint_id . '&xyz=', $zwischen );
		//$zwischen= preg_replace('/<a(.*?)Upload(.*?)>(.*?)<\\/a>/i', '', $zwischen);
		$zwischen = preg_replace( '/<input type="hidden" value="(.*?)" name="menuid" \/>/i',
			'<input value="' . $this->checked->menuid . '" name="menuid" type="hidden">', $zwischen );
		//<input value="1" name="menuid" type="hidden">
		return $zwischen;
	}

	/**
	 * convert html entities to literals
	 *
	 * @param $conv
	 * @return string|string[]|null
	 */
	function HTMLEntities_to_literals($conv)
	{
		$search = array(
			"'&(quot|#34);'i",
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",
			"'&(iexcl|#161);'i",
			"'&(cent|#162);'i",
			"'&(pound|#163);'i",
			"'&(curren|#164);'i",
			"'&(yen|#165);'i",
			"'&(brvbar|#166);'i",
			"'&(sect|#167);'i",
			"'&(uml|#168);'i",
			"'&(copy|#169);'i",
			"'&(ordf|#170);'i",
			"'&(laquo|#171);'i",
			"'&(not|#172);'i",
			"'&(shy|#173);'i",
			"'&(reg|#174);'i",
			"'&(macr|#175);'i",
			"'&(neg|#176);'i",
			"'&(plusmn|#177);'i",
			"'&(sup2|#178);'i",
			"'&(sup3|#179);'i",
			"'&(acute|#180);'i",
			"'&(micro|#181);'i",
			"'&(para|#182);'i",
			"'&(middot|#183);'i",
			"'&(cedil|#184);'i",
			"'&(supl|#185);'i",
			"'&(ordm|#186);'i",
			"'&(raquo|#187);'i",
			"'&(frac14|#188);'i",
			"'&(frac12|#189);'i",
			"'&(frac34|#190);'i",
			"'&(iquest|#191);'i",
			"'&(Agrave|#192);'",
			"'&(Aacute|#193);'",
			"'&(Acirc|#194);'",
			"'&(Atilde|#195);'",
			"'&(Auml|#196);'",
			"'&(Aring|#197);'",
			"'&(AElig|#198);'",
			"'&(Ccedil|#199);'",
			"'&(Egrave|#200);'",
			"'&(Eacute|#201);'",
			"'&(Ecirc|#202);'",
			"'&(Euml|#203);'",
			"'&(Igrave|#204);'",
			"'&(Iacute|#205);'",
			"'&(Icirc|#206);'",
			"'&(Iuml|#207);'",
			"'&(ETH|#208);'",
			"'&(Ntilde|#209);'",
			"'&(Ograve|#210);'",
			"'&(Oacute|#211);'",
			"'&(Ocirc|#212);'",
			"'&(Otilde|#213);'",
			"'&(Ouml|#214);'",
			"'&(times|#215);'i",
			"'&(Oslash|#216);'",
			"'&(Ugrave|#217);'",
			"'&(Uacute|#218);'",
			"'&(Ucirc|#219);'",
			"'&(Uuml|#220);'",
			"'&(Yacute|#221);'",
			"'&(THORN|#222);'",
			"'&(szlig|#223);'",
			"'&(agrave|#224);'",
			"'&(aacute|#225);'",
			"'&(acirc|#226);'",
			"'&(atilde|#227);'",
			"'&(auml|#228);'",
			"'&(aring|#229);'",
			"'&(aelig|#230);'",
			"'&(ccedil|#231);'",
			"'&(egrave|#232);'",
			"'&(eacute|#233);'",
			"'&(ecirc|#234);'",
			"'&(euml|#235);'",
			"'&(igrave|#236);'",
			"'&(iacute|#237);'",
			"'&(icirc|#238);'",
			"'&(iuml|#239);'",
			"'&(eth|#240);'",
			"'&(ntilde|#241);'",
			"'&(ograve|#242);'",
			"'&(oacute|#243);'",
			"'&(ocirc|#244);'",
			"'&(otilde|#245);'",
			"'&(ouml|#246);'",
			"'&(divide|#247);'i",
			"'&(oslash|#248);'",
			"'&(ugrave|#249);'",
			"'&(uacute|#250);'",
			"'&(ucirc|#251);'",
			"'&(uuml|#252);'",
			"'&(yacute|#253);'",
			"'&(thorn|#254);'",
			"'&(yuml|#255);'"
		);
		$replace = array(
			"\"",
			"&",
			"<",
			">",
			" ",
			chr(161),
			chr(162),
			chr(163),
			chr(164),
			chr(165),
			chr(166),
			chr(167),
			chr(168),
			chr(169),
			chr(170),
			chr(171),
			chr(172),
			chr(173),
			chr(174),
			chr(175),
			chr(176),
			chr(177),
			chr(178),
			chr(179),
			chr(180),
			chr(181),
			chr(182),
			chr(183),
			chr(184),
			chr(185),
			chr(186),
			chr(187),
			chr(188),
			chr(189),
			chr(190),
			chr(191),
			chr(192),
			chr(193),
			chr(194),
			chr(195),
			chr(196),
			chr(197),
			chr(198),
			chr(199),
			chr(200),
			chr(201),
			chr(202),
			chr(203),
			chr(204),
			chr(205),
			chr(206),
			chr(207),
			chr(208),
			chr(209),
			chr(210),
			chr(211),
			chr(212),
			chr(213),
			chr(214),
			chr(215),
			chr(216),
			chr(217),
			chr(218),
			chr(219),

			chr(220),
			chr(221),
			chr(222),
			chr(223),
			chr(224),
			chr(225),
			chr(226),
			chr(227),
			chr(228),
			chr(229),
			chr(230),
			chr(231),
			chr(232),
			chr(233),
			chr(234),
			chr(235),
			chr(236),
			chr(237),
			chr(238),
			chr(239),
			chr(240),
			chr(241),
			chr(242),
			chr(243),
			chr(244),
			chr(245),
			chr(246),
			chr(247),
			chr(248),
			chr(249),
			chr(250),
			chr(251),
			chr(252),
			chr(253),
			chr(254),
			chr(255)
		);
		return $conv = preg_replace($search, $replace, $conv);
	}
}

$flexintplugin = new flexintplugin();
