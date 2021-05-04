<?php

/**
#####################################
# Papoo CMS                         #
# (c) Dr. Carsten Euwens 2008       #
# Authors: Carsten Euwens           #
# http://www.papoo.de               #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################
 */

/**
 * Class glossar_class
 */
class glossar_class
{
	/** @var array Einstellungen aus der Tabelle glossar_pref */
	var $praeferenzen;

	/**
	 * glossar_class constructor.
	 */
	function __construct()
	{
		global $cms, $db, $db_praefix, $user, $content, $checked, $replace, $intern_stamm, $diverse;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->db_praefix = $db_praefix;
		$this->user = & $user;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->replace = & $replace;
		$this->intern_stamm = & $intern_stamm;
		$this->diverse = &$diverse;

		// Aktionsweiche...
		$this->make_glossar();
		$this->content->template['plugin_message'] = "";
	}

	/**
	 * glossar_class::make_glossar()
	 *
	 * @return void
	 */
	function make_glossar()
	{
		global $template;
		// Frontend
		if (!defined("admin")) {
			$this->praeferenzen_laden();
			if (strpos("XXX" . $template, "glossar/templates/glossar_front.html") OR
				strpos("XXX" . $template, "glossar/templates/wortdefinitionen.html") OR
				(isset($this->checked->inline) && $this->checked->inline=="true" || stristr($_SERVER['REQUEST_URI'],"inlinetrue"))) {
				$temp_glossar_id = isset($this->checked->glossar) ? $this->checked->glossar : NULL;

				$anzahl=explode("/",$this->praeferenzen['glosspref_menu_pfad']);
				$count=count($anzahl);

				if (empty($temp_glossar_id )) {
					$var="var".$count;
					IfNotSetNull($this->checked->$var);
					$temp_glossar_id_array = explode("-", $this->checked->$var); // var2 wenn Glossar in Men�-Ebene 0; var3 Ebene 1; var4 Ebene 2; etc.
					$temp_glossar_id = $temp_glossar_id_array[0];
				}

				$this->praeferenzen_laden();
				$this->praeferenzen['glosspref_introtext_de'] = $this->helper_frontend_texte_ersetzungen($this->praeferenzen['glosspref_introtext_de']);
				//print_r($this->praeferenzen);
				$this->glossar_front($temp_glossar_id);
			}
			if ($this->praeferenzen['glossar_mit_popup']==1) {
				//Lighbox Einbindung (nicht bei IE6 oder kleiner)
				if (!$this->helper_is_ie6_test()) {
					$this->do_glossar_colorbox();
				}
			}
		}
		// Backend
		else {
			$this->user->check_intern();

			$this->praeferenzen_laden();

			IfNotSetNull($this->checked->glossar_action);

			if (strpos("XXX" . $template, "glossar/templates/glossar_back.html")) {
				if ($this->checked->glossar_action == "loeschen_echt") {
					$this->eintrag_loeschen($this->checked->glossarid);
				}
				$this->switch_back($this->checked->glossar_action);
			}

			if (strpos("XXX" . $template, "glossar/templates/glossar_back_set.html")) {
				if ($this->checked->glossar_action == "sichern") {
					$this->praeferenzen_sichern();
					$this->praeferenzen_laden();
				}

				if ($this->checked->glossar_action == "text_speichern") {
					$this->praeferenzen_text_sichern();
					$this->praeferenzen_laden();
				}

				if ($this->checked->glossar_action == "ersetzungen_in_allen_artikeln") {
					//$this->ersetzungen_in_allen_artikeln(); // !! geht hier nicht, da Papoo-interne Klasse zur Ersetzung nohc nicht geladen ist -> ganz fieses Konstrut per post_papoo();
					$this->do_post_papoo_funktion_ersetzung = true;
					$this->content->template['plugin']['glossar']['template_weiche'] = "ERSETZUNG_FERTIG";
				}

				$this->helper_init_editor();
				$this->content->template['Beschreibung'] = "nobr:".$this->praeferenzen['glosspref_introtext_de'];
			}
		}
	}

	/**
	 * glossar_class::do_glossar_lightbox()
	 *
	 * @return void
	 */
	function do_glossar_colorbox()
	{
		//Thickbox CSS
		//$this->content->template['plugin_header'][]='<link type="text/css" media="screen" rel="stylesheet" href="'.PAPOO_WEB_PFAD.'/styles_default/css/colorbox.css" />';

		//Zuerst die JS Dateien laden
		//$this->content->template['plugin_header'][]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/jquery.js"></script>';

		//Thickbox JS
		//$this->content->template['plugin_header'][]='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/jquery.colorbox-min.js"></script>';

		if ($this->cms->mod_rewrite==2 || $this->cms->mod_free) {
			$this->content->template['plugin_header'][] =
				'nobr:<script type="text/javascript">
					$(document).ready(function() {
						$(\'a.glossar_link, a.glossar_link_inline\').each(
							function( intIndex ) {
								var bkValue = "-inlinetrue";
								var newHREF = $(this).attr(\'href\').replace(/.html/g, bkValue + ".html");    
								$(this).attr(\'href\', newHREF);
								//alert("Neu: "+newHREF);
							}
							);
					});
				</script>';
		}
		else {
			$this->content->template['plugin_header'][] =
				'nobr:<script type="text/javascript">
					<!--
					$(document).ready(function() {
						$(\'a.glossar_link\').each(
						function( intIndex ){
							var bkValue = "&inline=true";
							var newHREF = $( this ).attr(\'href\') + bkValue;
							$(this).attr(\'href\', newHREF);
							//alert("Neu: "+newHREF);
						}
						);
					});
					//-->
				</script>';
		}

		IfNotSetNull($this->content->template['message']['plugin']['glossar']['schliessen']);

		//Colorbox INI
		$this->content->template['plugin_header'][] =
			'nobr:<script type="text/javascript">
				<!--
				$(document).ready(function(){
					$("a[rel=\'lightbox\']").colorbox({
					transition:"fade",
					scalePhotos:"true",
					maxWidth: "80%",
					maxHeight: "80%"});
					$(".glossar_link").colorbox(
					{
						transition:"none",
						width:"'.$this->praeferenzen['glossar_colorbox_breite'].'",
						height:"'.$this->praeferenzen['glossar_colorbox_hoehe'].'",
						opacity:"'.$this->praeferenzen['glossar_colorbox_fading'].'",
						close: "'.$this->content->template['message']['plugin']['glossar']['schliessen'].'",
						iframe: true,
						maxWidth: "100%",
						maxHeight: "100%"
					});
				});
				//-->
			</script>';

		//Achtung - Inline Ansicht in JS Fenster... z.B. Colorbox
		if (isset($this->checked->inline) && $this->checked->inline=="true" || stristr($_SERVER['REQUEST_URI'],"inlinetrue")) {
			//Ausgeben
			//Ende damit nicht das drumherum da reinkommt
			global $diverse;
			$diverse->no_output = "no";
			global $template;
			global $webverzeichnis;
			$template = PAPOO_ABS_PFAD."/plugins/glossar/templates/inline_glossar.html";

			IfNotSetNull($this->content->template['result']['glossar_descrip']);

			$this->content->template['result']['glossar_descrip'] =
				str_replace("wortdefinitionen.html", "wortdefinitionen.html&amp;inline=true", $this->content->template['result']['glossar_descrip']);
			$this->content->template['result']['glossar_descrip'] =
				str_replace("glossar_link", "glossar_link_inline", $this->content->template['result']['glossar_descrip']);

			if ($this->cms->mod_rewrite==2 || $this->cms->mod_free) {
				$this->content->template['result']['glossar_descrip'] =
					str_replace(".html", "-inlinetrue.html", $this->content->template['result']['glossar_descrip']);
			}
		}
	}

	/**
	 * glossar_class::praeferenzen_laden()
	 *
	 * @return void
	 */
	function praeferenzen_laden()
	{
		$sql = sprintf("SELECT * FROM %s", $this->db_praefix."glossar_pref");
		$this->praeferenzen = $this->db->get_row($sql, ARRAY_A);
		$this->content->template['plugin']['glossar']['praeferenzen'] = $this->praeferenzen;

		$sql = sprintf("SELECT glosspref_introtext_de FROM %s WHERE glosspref_lang_id='%d' ", $this->db_praefix."glossar_pref_html",$this->cms->lang_id);
		$glosspref_introtext_de = $this->db->get_var($sql);
		$this->praeferenzen['glosspref_introtext_de'] = $glosspref_introtext_de;
		//print_r($sql);
		$this->content->template['plugin']['glossar']['praeferenzen']['glosspref_introtext_de'] = $glosspref_introtext_de;
		//print_r($this->content->template['plugin']['glossar']['praeferenzen']);
		//exit();

	}

	/**
	 * glossar_class::glossar_front()
	 *
	 * @param integer $glossar_id
	 * @return void
	 */
	function glossar_front($glossar_id = 0)
	{
		if ($this->praeferenzen['glosspref_liste'] == "1") {

			//Wenn ein Eintrag ausgew�hlt ist
			if (!empty($glossar_id)) {
				//Daten aus der Datenbank holen
				$sql = sprintf("SELECT * FROM %s WHERE glossar_id='%d' AND glossar_lang_id='%d' ",
					$this->db_praefix."glossar_daten",
					$glossar_id,
					$this->cms->lang_id
				);
				$result=$this->db->get_row($sql, ARRAY_A);

				if (empty($result)) {
					$this->glossar_front();
				}
				else {
					$this->content->template['result'] = $result;
					$this->content->template['result']['glossar_descrip'] = $this->helper_frontend_texte_ersetzungen($this->content->template['result']['glossar_descrip']);
					$this->content->template['glossarid'] = $result;
					$this->content->template['description']=strip_tags($result['glossar_meta_descrip']);
					$this->content->template['keywords'] = strip_tags($result['glossar_meta_key']);

					$temp_title = strip_tags($result['glossar_meta_title']);
					if (empty($temp_title)) {
						$temp_title = strip_tags($result['glossar_Wort']);
					}
					$this->content->template['site_title'] = $temp_title;
				}
			}
			else {
				$this->content->template['modus1']="ok";
				//Alle W�rter aus der Datenbank holen
				$sql = sprintf("SELECT * FROM %s WHERE glossar_lang_id='%d' AND glossar_lang_id='%d' ORDER BY glossar_Wort ASC",
					$this->db_praefix."glossar_daten",
					$this->cms->lang_id,
					$this->cms->lang_id
				);
				//und die Links erzeugen
				$result=$this->db->get_results($sql, ARRAY_A);
				$this->content->template['glossarliste_org'] = $result;
				if ($this->praeferenzen['glossar_mit_alphabet']==1) {
					$result=$this->make_lex($result);
				}
				$this->content->template['result'] = $result;
				$this->content->template['glossarliste'] = $result;

				if ($this->cms->mod_free==1) {
					$this->content->template['link_glossar'] = "";
					$this->content->template['link_glossar_2'] = "";
				}
				else {
					$this->content->template['link_glossar']=PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid."&glossar=";
					$this->content->template['link_glossar_2'] = "&template=glossar/templates/wortdefinitionen.html";
				}
			}
		}
		$this->content->template['glosspref_menu_pfad_org']= $this->praeferenzen['glosspref_menu_pfad'];
		$this->content->template['glosspref_menu_pfad_org_webverzeichnis']= PAPOO_WEB_PFAD;
	}

	/**
	 * @param $liste
	 * @return array
	 */
	function make_lex($liste)
	{
		$alphabet = range('A', 'Z');
		$this->content->template['glossarliste_alphabet'] = $alphabet;
		$i=0;
		foreach ($alphabet as $key=>$value) {
			$alphabet2[$i]['glossar_Wort']=$value;
			$i++;
		}
		IfNotSetNull($alphabet2);
		$complete = array_merge($alphabet2, $liste);
		if (is_array($complete)) {
			foreach ($complete as $keyx => $rowx) {
				$ertrag2[$keyx] = strtoupper($this->replace_uml($rowx['glossar_Wort']));
				$complete[$keyx]['glossar_Wort']=$this->replace_uml($rowx['glossar_Wort']);
			}
			//Multisort durchf�hren nach ASC oder DESC

			array_multisort($ertrag2, SORT_STRING, SORT_ASC,$complete);
			foreach ($complete as $keyx => $rowx) {
				$complete[$keyx]['glossar_Wort']=$this->reverse_replace_uml($rowx['glossar_Wort']);
			}
		}
		if($this->content->template['plugin']['glossar']['praeferenzen']['glossar_tabs']==1) {
			$this->content->template['is_jq_tabs']="ok";
			$this->content->template['plugin_header'][]='nobr:<style type="text/css">
 ul.alphabet_liste {

}
ul.alphabet_liste li {

}
ul.alphabet_liste li a {

}
ul.alphabet_liste li a:hover {
	font-weight:600;
}	
html ul.alphabet_liste li.active, html ul.alphabet_liste li.active a:hover  {
	font-weight:600;
}
.tab_container {

	clear: both;
	float: left; 
	width: 100%;
	background: #fff;
	-moz-border-radius-bottomright: 5px;
	-khtml-border-radius-bottomright: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-khtml-border-radius-bottomleft: 5px;
	-webkit-border-bottom-left-radius: 5px;
}
.tab_content {
	padding: 0;
	
	font-size: 1.2em;
}
.tab_content h2 {
	font-weight: normal;
	padding-bottom: 10px;
	border-bottom: 1px dashed #ddd;
	font-size: 1.8em;
}
.tab_content h3 a{
	color: #254588;
}
.tab_content img {
	float: left;
	margin: 0 20px 20px 0;
	border: 1px solid #ddd;
	padding: 5px;
}
</style>


<script type="text/javascript">

$(document).ready(function() {

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.alphabet_liste li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.alphabet_liste li").click(function() {
		$("ul.alphabet_liste li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); 
		//alert(activeTab);
		//Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});

});
</script>
';
		};
		return $complete;
	}

	/**
	 * @param $var
	 * @return mixed
	 */
	function replace_uml($var)
	{
		$var=str_replace(utf8_encode("�"),"UUML",$var);
		$var=str_replace(utf8_encode("�"),"AUML",$var);
		$var=str_replace(utf8_encode("�"),"OUML",$var);

		return $var;
	}

	/**
	 * @param $var
	 * @return mixed
	 */
	function reverse_replace_uml($var)
	{
		$var=str_replace("UUML",utf8_encode("�"),$var);
		$var=str_replace("AUML",utf8_encode("�"),$var);
		$var=str_replace("OUML",utf8_encode("�"),$var);

		return $var;
	}

	/**
	 * glossar_class::praeferenzen_sichern()
	 *
	 * @return void
	 */
	function praeferenzen_sichern()
	{
		IfNotSetNull($this->checked->glosspref_liste);
		IfNotSetNull($this->checked->glosspref_menu_pfad);
		IfNotSetNull($this->checked->glossar_mit_popup);
		IfNotSetNull($this->checked->glossar_mit_alphabet);
		IfNotSetNull($this->checked->glossar_colorbox_hoehe);
		IfNotSetNull($this->checked->glossar_colorbox_breite);
		IfNotSetNull($this->checked->glossar_colorbox_fading);
		IfNotSetNull($this->checked->glossar_tabs);

		//Datenbank updaten
		$sql = sprintf("UPDATE %s SET 
						glosspref_liste='%d', 
						glosspref_menu_pfad='%s', 
						glossar_mit_popup='%s', 
						glossar_mit_alphabet='%s',
						glossar_colorbox_hoehe='%s', 
						glossar_colorbox_breite='%s',
						glossar_colorbox_fading='%s',
						glossar_tabs='%s'
						WHERE glosspref_id='1'",
			$this->db_praefix."glossar_pref",
			$this->db->escape($this->checked->glosspref_liste),
			$this->db->escape($this->checked->glosspref_menu_pfad),
			$this->db->escape($this->checked->glossar_mit_popup),
			$this->db->escape($this->checked->glossar_mit_alphabet),
			$this->db->escape($this->checked->glossar_colorbox_hoehe),
			$this->db->escape($this->checked->glossar_colorbox_breite),
			$this->db->escape($this->checked->glossar_colorbox_fading),
			$this->db->escape($this->checked->glossar_tabs)
		);
		$this->db->query($sql);

	}

	/**
	 * glossar_class::praeferenzen_text_sichern()
	 *
	 * @return void
	 */
	function praeferenzen_text_sichern()
	{
		$this->helper_tinymce_post_encode();
		//ini_set("display_errors", true);
		//error_reporting(E_ALL);
		$sql = sprintf("UPDATE %s SET glosspref_introtext_de='%s'
						WHERE glosspref_id_id='1' AND glosspref_lang_id='%d' ",
			$this->db_praefix."glossar_pref_html",
			$this->db->escape($this->checked->inhalt_ar['inhalt']),
			$this->cms->lang_id
		);
		//print_r($sql);
		$this->db->query($sql);
		//exit();
	}

	/**
	 * glossar_class::switch_back()
	 *
	 * @param string $action
	 * @return void
	 */
	function switch_back($action = "")
	{
		switch ($action) {
		case "neu":
			$this->make_entry();
			break;

		case "":
		default:
			$this->change_entry();
			break;
		}
	}

	/**
	 * Ein neuer Eintrag wird erstellt und die Option zum Datenbank durchloopen angeboten
	 *
	 * @return void
	 */
	function make_entry()
	{
		if (isset($this->checked->submitentry) && $this->checked->submitentry) {
			//checken ob der Eintrag existiert
			$sql = sprintf("SELECT glossar_id FROM %s WHERE glossar_Wort='%s' LIMIT 1",
				$this->db_praefix."glossar_daten",
				$this->db->escape($this->checked->glossarname)
			);
			$result = $this->db->get_var($sql);

			if (empty ($result)) {
				$this->helper_tinymce_post_encode();

				$this->replace->links_ausschluss[] = strtolower($this->checked->glossarname);
				if (!empty($this->checked->glossarname_alt)) {
					$temp_link_name_alt = $this->diverse->explode_text_lines($this->checked->glossarname_alt);
					$this->replace->links_ausschluss = array_merge($this->replace->links_ausschluss, $temp_link_name_alt);
				}
				$temp_text_ausgezeichnet = $this->replace->do_glossar($this->checked->inhalt_ar['inhalt'], $this->praeferenzen['glosspref_menu_pfad']);

				$sql = sprintf("SELECT MAX(glossar_id) FROM %s",DB_PRAEFIX."glossar_daten");
				$max = $this->db->get_var($sql);
				$max++;

				$sql = sprintf("SELECT * FROM %s",
					DB_PRAEFIX.'papoo_name_language');
				//print_r($sql);
				$result = $this->db->get_results($sql,ARRAY_A);

				//Create for all possible languages...
				foreach ($result as $lang) {
//Daten eintragen in Datenbank
					$sql = sprintf("INSERT INTO %s SET 
								glossar_Wort='%s',
								glossar_Wort_alt='%s',
								glossar_lang_id='%d', 
								glossar_descrip='%s',
								glossar_descrip_sans='%s',
								
								glossar_meta_title='%s', 
								glossar_meta_descrip='%s', 
								glossar_meta_key='%s',
								
								glossar_gramatinfo='%s',
								glossar_abk='%s',
								glossar_sachgebiet='%s',
								glossar_frequenz='%s',
								glossar_definition='%s',
								glossar_anwendungsbeispiel='%s',
								glossar_siehe='%s',
								
								glossar_synonym1='%s',
								glossar_synonym2='%s',
								glossar_synonym3='%s',
								glossar_synonym4='%s',
								glossar_synonym5='%s'",
						$this->db_praefix."glossar_daten",
						$this->db->escape($this->checked->glossarname),
						$this->db->escape($this->checked->glossarname_alt),
						$this->db->escape($lang['lang_id']),
						//$this->db->escape($this->checked->glossardaten),
						$this->db->escape($temp_text_ausgezeichnet),
						$this->db->escape($this->checked->inhalt_ar['inhalt']),

						$this->db->escape($this->checked->metatitel),
						$this->db->escape($this->checked->metadescrip),
						$this->db->escape($this->checked->metakey),

						$this->db->escape($this->checked->glossar_gramatinfo),
						$this->db->escape($this->checked->glossar_abk),
						$this->db->escape($this->checked->glossar_sachgebiet),
						$this->db->escape($this->checked->glossar_frequenz),
						$this->db->escape($this->checked->glossar_definition),
						$this->db->escape($this->checked->glossar_anwendungsbeispiel),
						$this->db->escape($this->checked->glossar_siehe),

						$this->db->escape($this->checked->glossar_synonym1),
						$this->db->escape($this->checked->glossar_synonym2),
						$this->db->escape($this->checked->glossar_synonym3),
						$this->db->escape($this->checked->glossar_synonym4),
						$this->db->escape($this->checked->glossar_synonym5)
					);
					$this->db->query($sql);
				}



				//Direkt alle Eintr�ge durchloopen und ersetzen
				/*
				if ($this->checked->direkt)
				{
					//Ersetzung durchf�hren
					$this->ersetz($this->db->insert_id);
				}
				*/
				$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">..weiter</a>';
				}
				else {
					header("Location: $location_url");
				}

				exit;
			}
			//Eintrag existiert schon
			else {
				$this->content->template['exist'] = "ok";
				$this->content->template['glossarname'] = $this->checked->glossarname;
				$this->content->template['glossarname_alt'] = "nobr:".$this->checked->glossarname_alt;
				$this->content->template['glossardaten'] = $this->checked->glossardaten;
				if ($this->checked->direkt === "ok") {
					$this->content->template['checked'] = 'nodecode:checked="checked"';
				}
				$this->content->template['neuereintrag'] = "ok";
			}
		}
		else {
			$this->content->template['neuereintrag'] = "ok";
			$this->helper_init_editor();
		}

		if (isset($this->checked->fertig) && $this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
	}

	/**
	 * glossar_class::post_papoo()
	 *
	 * @return void
	 */
	function post_papoo()
	{
		if (isset($this->do_post_papoo_funktion_ersetzung) && $this->do_post_papoo_funktion_ersetzung) {
			// Muss per fiesem post_papoo()-Konstrukt stattfinden, da sonst Papoo-interne Klasse zur Ersetzung noch nicht geladen ist
			$this->ersetzungen_in_allen_artikeln();
		}
	}

	function output_filter()
	{
		global $output;
		if (isset($this->checked->glossar) && is_numeric($this->checked->glossar)) {
			$output=str_ireplace("#ID#","gl".$this->checked->glossar,$output);
			$output=str_ireplace("#MVID#","",$output);
		}

		//Nur wenn Commentplus nicht vorhanden ist
		$output=preg_replace("|#forum(.*?)#|", "", $output, PREG_PATTERN_ORDER);
	}

	/**
	 * glossar_class::ersetzungen_in_allen_artikeln()
	 *
	 * @return void
	 */
	function ersetzungen_in_allen_artikeln()
	{
		// Startseiten-Text ersetzen
		$sql = sprintf("SELECT start_text_sans FROM %s WHERE lang_id='%d' AND stamm_id='2' LIMIT 1",
			$this->db_praefix."papoo_language_stamm",
			$this->cms->lang_id
		);
		$temp_start_text = $this->db->get_var($sql);
		$temp_start_text = $this->replace->do_replace($temp_start_text);
		$sql = sprintf("UPDATE %s SET start_text='%s' WHERE lang_id='%d' AND stamm_id='2' LIMIT 1",
			$this->db_praefix."papoo_language_stamm",
			$this->db->escape($temp_start_text),
			$this->cms->lang_id
		);
		$this->db->query($sql);

		unset($_SESSION['dbp']['papoo_daten_lang']);

		// Alle (roh) Artikel-Texte auslesen
		$sql = sprintf("SELECT lan_repore_id, lang_id, lan_article_sans
						FROM %s WHERE lang_id='%d'",
			$this->db_praefix."papoo_language_article",
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$artikel_roh_liste = $this->db->get_results($sql, ARRAY_A);

		//Ersetzung durchf�hren
		if (!empty($artikel_roh_liste)) {
			foreach ($artikel_roh_liste as $artikel_roh) {
				$neutext = $this->replace->do_replace($artikel_roh['lan_article_sans']);

				//Wieder in die Datenbank eintragen
				$sql = sprintf("UPDATE %s SET lan_article='%s'
								WHERE lan_repore_id='%d' AND lang_id='%d'",
					$this->db_praefix."papoo_language_article",
					$this->db->escape($neutext),
					$artikel_roh['lan_repore_id'],
					$artikel_roh['lang_id']
				);
				$this->db->query($sql);
			}
		}

		// Alle Glossar-Eintr�ge ersetzen
		$sql = sprintf("SELECT * FROM %s WHERE glossar_lang_id='%d'",
			$this->db_praefix."glossar_daten",
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$eintrag_roh_liste = $this->db->get_results($sql, ARRAY_A);

		if (!empty($eintrag_roh_liste)) {
			foreach ($eintrag_roh_liste as $eintrag_roh) {
				$this->replace->links_ausschluss = array();
				$this->replace->links_ausschluss[] = strtolower($eintrag_roh['glossar_Wort']);
				if (!empty($eintrag_roh['glossar_Wort_alt'])) {
					$temp_link_name_alt = $this->diverse->explode_text_lines($eintrag_roh['glossar_Wort_alt']);
					$this->replace->links_ausschluss = array_merge($this->replace->links_ausschluss, $temp_link_name_alt);
				}
				$neutext = $this->replace->do_glossar($eintrag_roh['glossar_descrip_sans'], $this->praeferenzen['glosspref_menu_pfad']);

				//Wieder in die Datenbank eintragen
				$sql = sprintf("UPDATE %s SET glossar_descrip='%s'
								WHERE glossar_id='%d' AND glossar_lang_id='%d'",
					$this->db_praefix."glossar_daten",
					$this->db->escape($neutext),
					$eintrag_roh['glossar_id'],
					$eintrag_roh['glossar_lang_id']
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * glossar_class::eintrag_loeschen()
	 *
	 * @param integer $glossar_id
	 * @return void
	 */
	function eintrag_loeschen($glossar_id = 0)
	{
		if ($glossar_id) {
			//Eintrag nach id l�schen und neu laden
			$sql = sprintf("DELETE FROM %s WHERE glossar_id='%d'",
				$this->db_praefix."glossar_daten",
				$glossar_id
			);
			$this->db->query($sql);

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=del";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">.. weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen
	 */
	/**
	 * glossar_class::change_entry()
	 *
	 * @return void
	 */
	function change_entry()
	{
		//Es soll eingetragen werden
		if (isset($this->checked->submitentry) && $this->checked->submitentry) {
			$this->helper_tinymce_post_encode();

			$this->replace->links_ausschluss[] = strtolower($this->checked->glossarname);
			if (!empty($this->checked->glossarname_alt)) {
				$temp_link_name_alt = $this->diverse->explode_text_lines($this->checked->glossarname_alt);
				$this->replace->links_ausschluss = array_merge($this->replace->links_ausschluss, $temp_link_name_alt);
			}
			$temp_text_ausgezeichnet = $this->replace->do_glossar($this->checked->inhalt_ar['inhalt'], $this->praeferenzen['glosspref_menu_pfad']);

			$sql = sprintf("UPDATE %s SET
							glossar_Wort='%s', glossar_Wort_alt='%s', glossar_lang_id='%d', glossar_descrip='%s', glossar_descrip_sans='%s',
							glossar_meta_title='%s', glossar_meta_descrip='%s', glossar_meta_key='%s',
							glossar_gramatinfo='%s', glossar_abk='%s', glossar_sachgebiet='%s',
							glossar_frequenz='%s', glossar_definition='%s', glossar_anwendungsbeispiel='%s', glossar_siehe='%s',
							glossar_synonym1='%s', glossar_synonym2='%s', glossar_synonym3='%s', glossar_synonym4='%s', glossar_synonym5='%s' 
							
							WHERE glossar_id='%s' AND glossar_lang_id='%s'",
				$this->db_praefix."glossar_daten",

				$this->db->escape($this->checked->glossarname),
				$this->db->escape($this->checked->glossarname_alt),
				$this->db->escape($this->cms->lang_back_content_id),
				//$this->db->escape($this->checked->glossardaten),
				$this->db->escape($temp_text_ausgezeichnet),
				$this->db->escape($this->checked->inhalt_ar['inhalt']),

				$this->db->escape($this->checked->metatitel),
				$this->db->escape($this->checked->metadescrip),
				$this->db->escape($this->checked->metakey),

				$this->db->escape($this->checked->glossar_gramatinfo),
				$this->db->escape($this->checked->glossar_abk),
				$this->db->escape($this->checked->glossar_sachgebiet),

				$this->db->escape($this->checked->glossar_frequenz),
				$this->db->escape($this->checked->glossar_definition),
				$this->db->escape($this->checked->glossar_anwendungsbeispiel),
				$this->db->escape($this->checked->glossar_siehe),

				$this->db->escape($this->checked->glossar_synonym1),
				$this->db->escape($this->checked->glossar_synonym2),
				$this->db->escape($this->checked->glossar_synonym3),
				$this->db->escape($this->checked->glossar_synonym4),
				$this->db->escape($this->checked->glossar_synonym5),
				$this->db->escape($this->checked->glossarid),
				$this->db->escape($this->cms->lang_id)
			);
			$this->db->query($sql);

			/*
			//Direkt alle Eintr�ge durchloopen und ersetzen
			if ($this->checked->direkt)
			{
				//Ersetzung durchf�hren
				$this->ersetz($this->db->escape($this->checked->glossarid));
			}
			*/

			$location_url = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&fertig=drin";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="'.$location_url.'">.. weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}

		if (!empty($this->checked->glossarid)) {
			//Nach id aus der Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE glossar_id='%s' AND glossar_lang_id='%d'",
				$this->db_praefix."glossar_daten",
				$this->db->escape($this->checked->glossarid),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$result = $this->db->get_results($sql);
			$this->helper_init_editor();

			if (!empty ($result)) {
				foreach ($result as $glos) {
					//$this->content->template['glossarname'] = htmlentities($glos->glossar_Wort);
					$this->content->template['glossarname'] = $glos->glossar_Wort;
					$this->content->template['glossarname_alt'] = "nobr:".$glos->glossar_Wort_alt;
					//$this->content->template['glossardaten'] = "nobr:".$glos->glossar_descrip;
					$this->content->template['Beschreibung'] = "nobr:".$glos->glossar_descrip_sans;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
					$this->content->template['glossarid'] = $glos->glossar_id;
					$this->content->template['metatitel'] = "nobr:".$glos->glossar_meta_title;
					$this->content->template['metadescrip'] = "nobr:".$glos->glossar_meta_descrip;
					$this->content->template['metakey'] = "nobr:".$glos->glossar_meta_key;

					$this->content->template['glossar_gramatinfo'] = $glos->glossar_gramatinfo;
					$this->content->template['glossar_abk'] = $glos->glossar_abk;
					$this->content->template['glossar_sachgebiet'] = $glos->glossar_sachgebiet;
					$this->content->template['glossar_frequenz'] = $glos->glossar_frequenz;
					$this->content->template['glossar_definition'] = $glos->glossar_definition;
					$this->content->template['glossar_anwendungsbeispiel'] = $glos->glossar_anwendungsbeispiel;
					$this->content->template['glossar_siehe'] = $glos->glossar_siehe;
					$this->content->template['glossar_synonym1'] = $glos->glossar_synonym1;
					$this->content->template['glossar_synonym2'] = $glos->glossar_synonym2;
					$this->content->template['glossar_synonym3'] = $glos->glossar_synonym3;
					$this->content->template['glossar_synonym4'] = $glos->glossar_synonym4;
					$this->content->template['glossar_synonym5'] = $glos->glossar_synonym5;
				}
			}
		}
		else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			//Daten rausholen
			$sql = sprintf("SELECT * FROM %s WHERE glossar_lang_id='%d' AND glossar_lang_id='%d' ORDER BY glossar_Wort ASC",
				$this->db_praefix."glossar_daten",
				$this->db->escape($this->cms->lang_back_content_id),
				$this->cms->lang_id
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['link_glossar'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&template=".$this->checked->template."&glossarid=";
		}
		if (isset($this->checked->fertig) && $this->checked->fertig == "drin") {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		//Anzeigen das Eintrag gel�scht wurde
		if (isset($this->checked->fertig) && $this->checked->fertig == "del") {
			$this->content->template['deleted'] = "ok";
		}

		//Soll wirklich gel�scht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	// ************************************************************************
	// interne HILFS-FUNKTIONEN
	// ************************************************************************
	/**
	 * glossar_class::helper_frontend_texte_ersetzungen()
	 *
	 * @param string $text
	 * @return mixed|string
	 */
	function helper_frontend_texte_ersetzungen($text = "")
	{
		if (!empty($text)) {
			global $diverse;
			global $download;

			$text = $diverse->do_pfadeanpassen($text);
			$text = $download->replace_downloadlinks($text);
		}
		return $text;
	}

	/**
	 * glossar_class::helper_init_editor()
	 *
	 * @return void
	 */
	function helper_init_editor()
	{
		// 1. Test welcher Editor geladen werden soll
		$temp_user_editor_id = 3;
		if (isset($this->user->editor)) {
			$temp_user_editor_id = $this->user->editor;
		}
		$this->content->template['editor_default'] = $temp_user_editor_id;
		$this->content->template['tinymce_lang_short'] = $this->cms->lang_back_short;

		// 2. Editor initialisieren
		global $intern_artikel;
		// Bilder zuweisen
		$intern_artikel->get_images($this->cms->lang_back_id);
		// Downloads und Artikel zuweisen
		$intern_artikel->get_downloads($this->cms->lang_back_id);
		// CSS-Klassen zuweisen (f�r QuickTag-Editor)
		$intern_artikel->get_css_klassen();
	}

	function helper_tinymce_post_encode()
	{
		if (empty($this->checked->editor_name)) {
			$this->checked->editor_name = "";
		}
		if (empty($this->checked->inhalt_ar['inhalt'])) {
			$this->checked->inhalt_ar['inhalt'] = "";
		}
		if ($this->checked->editor_name == "tinymce" AND !empty($this->checked->inhalt_ar['inhalt'])) {
			global $diverse;
			$this->checked->inhalt_ar['inhalt'] = $diverse->recode_entity_n_br($this->checked->inhalt_ar['inhalt'], "nobr");
		}
	}

	/**
	 * @return bool
	 */
	function helper_is_ie6_test()
	{
		return preg_match('#^Mozilla/4.0 \(compatible; MSIE [456]#i', $_SERVER['HTTP_USER_AGENT']) ? true : false;
	}
}

$glossar = new glossar_class();
