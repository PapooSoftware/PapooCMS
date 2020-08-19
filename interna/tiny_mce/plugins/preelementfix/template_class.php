<?php
/**
######################################
# Papoo Software                     #
# (c) Dr. Carsten Euwens 2008        #
# Author: Carsten Euwens             #
# http://www.papoo.de                #
######################################
# PHP Version >= 4.3                 #
######################################
*/

/**
 * Class content_template_class
 */
class content_template_class
{
	/**
	 * content_template_class constructor.
	 */
	function __construct()
	{
		#$this->do_admin();
	}

	/**
	 * @return bool|void
	 */
	function do_admin()
	{
		// Papoo-Klassen globalisieren
		global $db;
		$this->db = & $db;
		global $db_praefix;
		$this->db_praefix = $db_praefix;

		global $checked;
		$this->checked = & $checked;

		global $content;
		$this->content = & $content;

		global $cms;
		$this->cms = & $cms;

		global $user;
		$this->user = & $user;

		global $diverse;
		$this->diverse = & $diverse;


		if (defined("admin")) {
			// Überprüfen ob Zugriff auf die Inhalte besteht
			$this->user->check_access();

			switch ($this->checked->menuid) {
				/*
				case 83 :
					//Test auf Start setzen
					$this->content->template['template_start']="ok";
					break;
				*/
				case 22 :

				#if (!is_numeric($this->checked->style_id))
				{
					if (is_numeric($this->checked->layout) ) {
						$this->content->template['template_news_start']="ok";
						$this->make_new_template();
					}
					else {
						//Neue Kategorie erstellen
						$this->content->template['template_sys_start']="ok";
						$this->make_sys_template();
					}
				}
					break;

				case 85 :
					//Kategorie bearbeiten
					$this->content->template['template_cont_start']="ok";
					$this->make_content_template();
					break;

				case 86 :
					//Kategorie bearbeiten
					$this->content->template['template_news_start']="ok";
					$this->make_new_template();
					break;


				case 83 :
					// Content-Templates bearbeiten
					$this->content->template['template_cont_start']="ok";
					$this->make_content_template();
					break;

				default:
					return false;
					break;
			}
		}
	}


	/**
	 * Die Funktion um die Content Templates bearbeiten zu können
	 */
	function make_content_template()
	{
		//$this->content->template['self']="./template.php?menuid=85";
		if (($this->checked->messageget=="insertok")) {
			$this->content->template['insertok']="ok";
		}
		if (($this->checked->messageget=="delok")) {
			$this->content->template['delok']="ok";
		}
		//Einträge in die Datenbank machen
		if ($this->checked->update_content=="ok") {
			// .. Eintrag aus DB löschen
			if (!empty($this->checked->del_ctemplate)) {
				//Alte Einträge löschen
				$sql = sprintf("DELETE FROM %s WHERE ctempl_id='%d'",
					$this->db_praefix."papoo_content_templates",
					$this->checked->ctempl_id
				);
				$result = $this->db->query($sql);

				$location_url = "./template.php?menuid=83&messageget=delok&makenew_content=null";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}

			// .. Eintrag in DB aktualisieren
			$this->content->template['contentnew']="ok";
			//Daten rausholen
			$sql = sprintf("SELECT * FROM %s WHERE ctempl_id='%d' AND ctempl_lang_id='%d' ",
				$this->db_praefix."papoo_content_templates",
				$this->checked->ctempl_id,
				$this->cms->lang_back_content_id
			);
			$result = $this->db->get_results($sql, ARRAY_A);

			$result['0']['ctempl_content']="nodecode:".$result['0']['ctempl_content'];

			//übergeben
			$this->content->template['content_ct']=$result;
			if (!empty($this->checked->save_ctemplate)) {
				$sql = sprintf("UPDATE %s SET ctempl_lang_id='%d', ctempl_name='%s', ctempl_content='%s' WHERE ctempl_id='%d'",
					$this->db_praefix."papoo_content_templates",
					$this->cms->lang_id,
					$this->db->escape($this->checked->ctempl_name),
					$this->db->escape($this->checked->ctempl_content),
					$this->db->escape($this->checked->ctempl_id)
				);
				$this->db->query($sql);

				$location_url = "./template.php?menuid=83&messageget=insertok&makenew_content=null";
				if ($_SESSION['debug_stopallredirect']) {
					echo '<a href="'.$location_url.'">Weiter</a>';
				}
				else {
					header("Location: $location_url");
				}
				exit;
			}
		}
		else {
			// neuen Eintrag in DB machen
			if ($this->checked->makenew_content=="ok") {
				$this->content->template['contentnew']="ok";

				if (!empty($this->checked->save_ctemplate)) {
					//Daten speichern
					$sql = sprintf("INSERT INTO %s SET ctempl_lang_id='%d', ctempl_name='%s', ctempl_content='%s'",
						$this->cms->tbname['papoo_content_templates'],
						$this->cms->lang_id,
						$this->db->escape($this->checked->ctempl_name),
						$this->db->escape($this->checked->ctempl_content)
					);
					$this->db->query($sql);

					$location_url = "./template.php?menuid=83&messageget=insertok&makenew_content=null";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="'.$location_url.'">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit;
				}
			}
			else {
				// Liste aus DB auslesen
				$sql = "SELECT * FROM ".$this->cms->tbname['papoo_content_templates']." WHERE ctempl_lang_id='".$this->cms->lang_back_content_id."' ORDER BY ctempl_name ASC ";
				$result = $this->db->get_results($sql,ARRAY_A);
				$this->content->template['contentlist']=$result;
			}
		}
	}

	/**
	 *
	 */
	function create_new_file()
	{
		$sql = sprintf("SELECT style_pfad FROM %s WHERE style_id ='%d'",
			$this->cms->tbname['papoo_styles'],
			$this->db->escape($this->checked->style_id)
		);
		$pfad=$this->db->get_var($sql);
		$file=basename($this->checked->template_file);

		if (!empty($this->checked->template_dir)) {
			$dir=basename($this->checked->template_dir);
			if (!is_dir(PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$dir)) {
				mkdir(PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$dir);
			}

			if (!file_exists(PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$dir."/".$file)) {
				@copy(PAPOO_ABS_PFAD."/styles_default/templates/".$dir."/".$file, PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$dir."/".$file);
			}
		}

		if (empty($this->checked->template_dir) && !file_exists(PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$file)) {
			@copy(PAPOO_ABS_PFAD."/styles_default/templates/".$file, PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$file);
		}
	}



	// *****************************************************************************************************************
	// BACKUP ALTE FUNKTONEN

	/**
	 * Die Funktion um die System Templates bearbeiten zu können
	 *
	 * @deprecated Siehe Kommentar weiter oben
	 */
	function make_sys_template()
	{

		$this->content->template['self']="./template.php?menuid=84";
		if (!empty($this->checked->template_file)) {
			if (empty($this->checked->get_file)) {
				$this->content->template['template_sys_file_one']="ok";
				$this->checked->template_file=basename($this->checked->template_file);
				$this->get_file_content($this->checked->template_file);
			}
			else {
				$this->create_new_file();
				$this->make_file_liste();
			}
		}
		else {
			if (!empty($this->checked->style_id)) {
				$this->make_file_liste();
			}
			/**
 * else
 * 			{
 * 				$this->content->template['template_sys_start_text']="ok";

 * 				//Liste der TEmplate Verzeichnisse rausholen
 * 				$template_liste=$this->get_template_list();
 * 				//Checken welche Styles und was standar
 * 				$template_liste=$this->get_css_from_templates($template_liste);
 * 				//Daten übergeben ans Template
 * 				$this->content->template['template_sys_liste']=$template_liste;
 * 			}
 */
		}
	}

	/**
	 * content_template_class::make_file_liste()
	 *
	 * @return void
	 */
	function make_file_liste()
	{
		$sql=sprintf(" 	SELECT style_pfad FROM %s WHERE style_id 	='%d'",
			$this->cms->tbname['papoo_styles'],
			$this->db->escape($this->checked->style_id)
		);
		$pfad=$this->db->get_var($sql);

		//TEmplate setzen
		$this->content->template['template_sys_file']="ok";
		//LIste der Dateien zuweisen
		$this->checked->template_dir=basename($pfad);

		$this->get_template_files($this->checked->template_dir);
	}

	/**
	 * Inhalt einer Datei anzeigen und evtl. speichern
	 *
	 * @param string $file_name
	 */
	function get_file_content($file_name="")
	{
		//Unterverzeichnis?
		$sub=basename($this->checked->template_dir)."/";

		$sql=sprintf(" 	SELECT style_pfad FROM %s WHERE style_id 	='%d'",
			$this->cms->tbname['papoo_styles'],
			$this->db->escape($this->checked->style_id)
		);
		$pfad=$this->db->get_var($sql);

		if (!empty($this->checked->save_template)) {
			$ok=$this->diverse->write_to_file("/styles/".$pfad."/templates/".$sub."".$file_name,utf8_decode($_POST["template_content"]));

			if ($ok) {
				$this->content->template['eingetragen'] = "Die Daten wurden eingetragen!";
			}
			else {
				$this->content->template['eingetragen'] = "Die Daten wurden leider nicht eingetragen!";
			}
		}

		#PAPOO_ABS_PFAD."/templates/".$sub.$file_name;
		$file=PAPOO_ABS_PFAD."/styles/".$pfad."/templates/".$sub."".$file_name;

		if(!empty($file_name)) {
			$inhalt = implode("",file($file));
		}
		$inhalt=str_ireplace("\t","  ",$inhalt);
		$inhalt=htmlentities(($inhalt));
		//template_file
		$this->content->template['template_dir']=$this->checked->template_dir;
		$this->content->template['template_file']=$sub2.$file_name;
		$this->content->template['template_content']="nobr:".$inhalt;
	}

	/**
	 * Liste der Dateien im Template
	 *
	 * @param string $dir
	 */
	function get_template_files($dir="")
	{
		$files_root = $this->diverse->lese_dir( "/styles/".$dir.'/templates/', 'html' );

		$files_mod_var = $this->diverse->lese_dir( "/styles/".$dir."/templates/_module", 'html' );

		$files_mod_intern = $this->diverse->lese_dir( "/styles/".$dir."/templates/_module_intern", 'html' );

		//Standard Dateien
		$files_root_def = $this->diverse->lese_dir( '/styles_default/templates/', 'html' );
		$files_mod_var_def = $this->diverse->lese_dir( "/styles_default/templates/_module", 'html' );

		$files_mod_intern_def = $this->diverse->lese_dir( "/styles_default/templates/_module_intern", 'html' );

		//root Files
		foreach ($files_root_def as $dat) {
			if (is_array($files_root)) {
				$gleich=0;
				foreach ($files_root as $dat2) {
					if ($dat['name']==$dat2['name']) {
						$gleich=1;
					}
				}
				if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );
                }
                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }
				$dat['schreib']=$dat2['schreib'];

				//NAme ist nicht vorhanden
				if ($gleich!=1) {
					$dat['def']=1;
					$neu[]=$dat;
				}
				else {
					$neu[]=$dat;
				}
			}
			else {
				$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );

                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }

				$dat['schreib']=$dat2['schreib'];
				$dat['def']=1;
				$neu[]=$dat;
			}
		}
		$files_root=$neu;
		$neu=array();

		foreach ($files_mod_var_def as $dat) {
			if (is_array($files_mod_var)) {
				$gleich=0;
				foreach ($files_mod_var as $dat2) {
					if ($dat['name']==$dat2['name']) {
						$gleich=1;
					}
				}
				if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );
                }

                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }
				$dat['schreib']=$dat2['schreib'];

				//NAme ist nicht vorhanden
				if ($gleich!=1) {
					$dat['def']=1;
					$neu[]=$dat;
				}
				else {
					$neu[]=$dat;
				}
			}
			else {
				$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );

                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }

				$dat['schreib']=$dat2['schreib'];
				$dat['def']=1;
				$neu[]=$dat;
			}
		}
		$files_mod_var=$neu;


		$neu=array();

		foreach ($files_mod_intern_def as $dat) {
			if (is_array($files_mod_intern)) {
				$gleich=0;
				foreach ($files_mod_intern as $dat2) {
					if ($dat['name']==$dat2['name']) {
						$gleich=1;
					}
				}

				if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );
                }

                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }
				$dat['schreib']=$dat2['schreib'];
				//NAme ist nicht vorhanden
				if ($gleich!=1) {
					$dat['def']=1;
					$neu[]=$dat;
				}
				else {
					$neu[]=$dat;
				}
			}
			else {
                $dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates/_module_intern"  );

                if (empty($dat2['schreib'])) {
                	$dat2['schreib'] = is_writeable( PAPOO_ABS_PFAD . "/styles/".$dir."/templates"  );
                }

				$dat['schreib']=$dat2['schreib'];

				$dat['def']=1;
				$neu[]=$dat;
			}
		}
		$files_mod_intern=$neu;
		if (!is_array($files_root))
		{
			$files_root=array();
		}

		//$files[]=array();
		$files['0']['name']="_module";
		$files['2']['name']="_module_intern";
		$files=array_merge($files,$files_root);
		$this->content->template['template_dir']=$dir;
		$this->content->template['root_files']=$files;
		$this->content->template['var_files']=$files_mod_var;
		$this->content->template['intern_files']=$files_mod_intern;
	}

	/**
	 * CSS rausholen und bestimmen welches Standard ist
	 *
	 * @param string $liste
	 * @return array
	 */
	function get_css_from_templates($liste="")
	{
		$sql = sprintf( "SELECT * FROM %s ", $this->cms->tbname['papoo_styles'] );
		$styls = $this->db->get_results( $sql );

		$tpl_array=array();
		$i=0;
		foreach ($liste as $tpl) {
			foreach ($styls as $css) {
				$pfad=$css->html_datei;
				if ($pfad=="index.html") {
					$pfad="standard";
				}
				if ($pfad==$tpl) {
					$tpl_array[$i]['dir']=$pfad;
					$tpl_array[$i]['css'][]=$css->style_name;
					if (empty($tpl_array[$i]['standard'])) {
						$tpl_array[$i]['standard']=$css->standard_style;
					}
				}
			}
			if (empty($tpl_array[$i]['dir'])) {
				$tpl_array[$i]['dir']=$tpl;
			}
			$i++;
		}
		return $tpl_array;
	}

	/**
	 * Liste der Template Verzeichnisse rausbekommen
	 *
	 * @return array
	 */
	function get_template_list()
	{
		$verzeichnisse = array ();
		$pfad = PAPOO_ABS_PFAD . "/templates/";
		$handle = opendir( $pfad );
		while (($file = readdir($handle)) !== false) {
			if (is_dir( $pfad . $file)) {
				if (strpos("XXX" . $file, ".") != 3) {
					// Nur nicht-unsichtbare Verzeichnisse aufnehmen
					$verzeichnisse[] = $file;
				}
			}
		}
		// $dir=$this->diverse->lese_dir(PAPOO_ABS_PFAD."/css");
		return $verzeichnisse;
	}

	/**
	 * Erster Schritt neues Layout anlegen
	 */
	function layout_schritt1()
	{
		//Formular anbieten
		$_SESSION['contentnew_layout_layout']="";
		$this->content->template['contentnew_layout_step1']="ok";
	}

	/**
	 * @return bool
	 */
	function check_exist()
	{
		$this->checked->layoutname=basename($this->checked->layoutname);
		if (@is_dir("../templates/".$this->checked->layoutname)) {
			return true;
		}
		if (@is_dir("../css/".$this->checked->layoutname)) {
			return true;
		}
		return false;
	}

	/**
	 * Zweiter Schritt neues Layout anlegen
	 */
	function layout_schritt2()
	{
		if (!empty($this->checked->layoutname)) {
			$this->checked->layoutname=basename($this->checked->layoutname);
			$this->content->template['contentnew_layout_step2']="ok";
			//Checken ob existiert
			if (!$this->check_exist()) {
				$this->create_layout($this->checked->layoutname);
			}
			else {
				if ($this->check_dirs($this->checked->layoutname)==true) {
					$this->copyok=$this->copy_files($this->checked->layoutname);
				}
				else {
					$this->content->template['contentnew_layout_step2']="ok";
					$this->content->template['contentnew_layout_step2_fehler']="ok";
				}
			}

			//Alle Dateien wurden kopiert
			if ($this->copyok==true) {
				$this->content->template['contentnew_layout_step2']="";
				$_SESSION['contentnew_layout_layout']=$this->checked->layoutname;
				$this->content->template['contentnew_layout_step3']="ok";
				$this->content->template['contentnew_layout_step3ok']="ok";
			}
			//Dateien wurden nicht kopiert, daher Fehlermeldung ausgeben
			else {
				$this->content->template['contentnew_layout_step3']="ok";
				$this->content->template['contentnew_layout_step3_fehler']="ok";
			}
		}
		else {
			$this->content->template['contentnew_layout_step1']="ok";
			$this->content->template['contentnew_layout_step1_fehler']="ok";
		}
	}

	/**
	 * Schritt 3 CSS einbinden
	 */
	function layout_schritt3()
	{
		if (!empty($this->checked->save_css)) {
			$layout=basename($_SESSION['contentnew_layout_layout']);
			$file = "/styles/".$layout."/css/style.css";
			$this->diverse->write_to_file($file, $this->checked->css);
			$this->content->template['contentnew_layout_step4']="ok";
		}
	}

	/**
	 * Neues Layout in die DB eintragen
	 */
	function insert_style_db()
	{
		$layout=basename($_SESSION['contentnew_layout_layout']);
		$sql = sprintf( "INSERT INTO %s SET style_name='%s', " . "style_pfad='%s', style_cc='%s', html_datei='%s' ",
			$this->cms->tbname['papoo_styles'],
			$this->db->escape( $layout ),
			$this->db->escape( $layout . "/_index.css" ),
			$this->db->escape( $cc ),
			$this->db->escape( $layout)
		);

		$this->db->query( $sql );
	}

	/**
	 * Schritt 3 CSS einbinden
	 */
	function layout_schritt4()
	{

		if (!empty($this->checked->save_html)) {
			$html=$this->checked->html;

			$html_ar=explode("<body",$html);

			$html_ar2=explode(">",$html_ar['1'],2);

			$html_ar3=explode("</body>",$html_ar2['1']);

			$html=$html_ar3['0'];
			$html=strip_tags($html,"<div>");
			$html=str_ireplace("\r\n","",$html);
			$html=str_ireplace("\t","",$html);
			$html=preg_replace("/>(.*?)</","> <",$html)."\n";
			$html=str_ireplace(">",">\r\n",$html);
			$html=str_ireplace("<div"," <div",$html);
			$html=trim($html);
			if (empty($html)) {
				$html='
	<div id="page_margins">
		<div id="page" class="hold_floats">

			<!-- Kopfbereich -->
			<div id="head">
				<div id="head_content" class="clearfix">
					{include file="_head.html"}
				</div> <!-- ENDE head_content -->
			</div> <!-- ENDE head -->

			<!-- Hauptbereich -->
			<div id="main">

			<!-- Inhalte in der linken Spalte -->
			{if $spalte_links OR 1}
				<div id="col1">
					<div id="col1_content" class="clearfix">
						{include file="_inhalt_links.html"}
					</div> <!-- ENDE col1_content -->
				</div> <!-- ENDE col1 -->
			{/if}

			<!-- Inhalte der rechten Spalte-->
			{if $spalte_rechts OR 1}
			<div id="col2">
				<div id="col2_content" class="clearfix">
					{include file="_inhalt_rechts.html"}
				</div> <!-- ENDE col2_content -->
			</div> <!-- ENDE col2 -->
			{/if}

			<!-- Hauptinhaltsbereich in der Mitte - Content !!-->
			{if $spalte_mitte OR 1}
			<div id="col3">
				<div id="col3_content" class="clearfix">
					{include file="_inhalt_mitte.html"}
				</div> <!-- ENDE col3_content -->
				<!-- Clearing für IE -->
				<div id="ie_clearing">.</div>
			</div> <!-- ENDE col3 -->
			{/if}

		</div> <!-- ENDE main -->
		<div id="footer" class="floatbox">
			{include file="_fuss.html"}
			</div> <!-- ENDE footer -->
		</div> <!-- ENDE page -->
	</div> <!-- ENDE page_margins -->';
				$html=str_ireplace("\t"," ",$html);
			}
			$html="nodecode:".($html);
			#$this->diverse->write_to_file($file, $this->checked->html);
			$this->content->template['contentnew_layout_step5a']="ok";
			$this->content->template['html']=$html;
		}
	}

	/**
	 * Schritt 3 CSS einbinden
	 */
	function layout_schritt5()
	{
		if (!empty($this->checked->save_html2)) {
			$layout=basename($_SESSION['contentnew_layout_layout']);
			$file = "/styles/".$layout."/templates/__index.html";
			$kopf='{foreach item=error from=$page_error}{$error}{/foreach}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_short}" lang="{$lang_short}" dir="{$lang_dir}">
<head>
<!-- Seiten Kopf, wird komplett über Admin realisiert-->
{include file="_kopf.utf8.html"}
</head>
{include file="_body_kopf.utf8.html"}';
			$html=$this->checked->html;
			$html=$kopf.$html.'
			</body>
			</html>';
			$this->diverse->write_to_file($file, $html);
			$this->insert_style_db();
			$this->content->template['contentnew_layout_step5b']="ok";
		}

	}

	/**
	 * Neues Layout erstellen mit allen Verzeichnissen und Dateien
	 *
	 * @param string $layout
	 */
	function create_layout($layout="")
	{
		if (!empty($layout)) {
			if ($this->checked->layout<"3") {
				$this->create_dirs($layout);
			}
			//Verzeichnisse erstellen
			if ($this->check_dirs($layout)==true) {
				//Dateien kopieren
				$this->copyok=$this->copy_files($layout);
			}
			else {
				$this->content->template['contentnew_layout_step2']="ok";
				$this->content->template['contentnew_layout_step2_fehler']="ok";
			}
		}
	}

	/**
	 * Alle nötigen Dateien und Verzeichnisse kopieren
	 *
	 * @param string $layout
	 * @return bool
	 */
	function copy_files($layout="")
	{
		//Root Verzeicnis
		$css=$this->diverse->lese_dir( "/styles/vorlage/css", 'css' );
		foreach ($css as $file) {
			if (!copy("../styles/vorlage/css/".$file['name'],"../styles/".$layout."/css/".$file['name'])) {
				$nocopy[]="../styles/".$layout."/css/".$file['name'];
			}
		}

		if (empty($nocopy)) {
			return true;
		}
		else {
			return false;
		}

		//CSS Dateien kopieren
	}

	/**
	 * Verzeichnisse erstellen
	 *
	 * @param $layout
	 */
	function create_dirs($layout)
	{
		$layout=basename($layout);
		//Verzeichnis im Template
		@mkdir("../styles/".$layout,0777);
		@mkdir("../styles/".$layout."/templates",0777);
		@mkdir("../styles/".$layout."/templates/_module",0777);
		@mkdir("../styles/".$layout."/templates/_module_intern",0777);
		@mkdir("../styles/".$layout."/css",0777);
		@mkdir("../styles/".$layout."/css/images",0777);
	}

	/**
	 * @param $layout
	 * @return bool
	 */
	function check_dirs($layout)
	{
		$layout=basename($layout);
		//Verzeichnis im Template
		$permok["../styles/".$layout]=$this->checkpermission("../styles/".$layout);

		$permok["../styles/".$layout."/templates/_module"]=$this->checkpermission("../styles/".$layout."/templates/_module");

		$permok["../styles/".$layout."/templates/_module_intern"]=$this->checkpermission("../styles/".$layout."/templates/_module_intern");

		$permok["../styles/".$layout."/templates"]=$this->checkpermission("../styles/".$layout."/templates");

		$permok["../styles/".$layout."/css"]=$this->checkpermission("../styles/".$layout."/css");

		$permok["../styles/".$layout."/css/images"]=$this->checkpermission("../styles/".$layout."/css/images");

		$this->content->template['permliste']=$this->noperm;
		foreach ($permok as $perm) {
			if ($perm===false) {
				$this->noperm=$permok;
				return false;
			}
		}
		return true;
	}

	/**
	 * Neues Template und Layout einbinden
	 */
	function make_new_template()
	{
		$this->checked->layoutname=basename($this->checked->layoutname);
		$this->checked->layoutname=trim($this->checked->layoutname);
		preg_replace("/[^A-Za-z0-9]/"," ", $this->checked->layoutname);
		$this->checked->layoutname=$this->replace_uml($this->checked->layoutname);
		$this->checked->layoutname=str_ireplace("%","",$this->checked->layoutname);

		switch ($this->checked->layout) {

			case 1 :
			//Test auf Start setzen
			$this->layout_schritt1();
			break;

			case 2 :
			//Test auf Start setzen
			$this->layout_schritt2();
			break;

			case 3 :
			//Test auf Start setzen
			$this->layout_schritt3();
			break;

			case 4 :
			//Test auf Start setzen
			$this->layout_schritt4();
			break;

			case 5 :
			//Test auf Start setzen
			$this->layout_schritt5();
			$_SESSION['dbp']=array();
			break;

			default:
			$this->content->template['contentnew_layout']="ok";
			$this->content->template['self']="./template.php?menuid=86";
			//Verzeichnisrechte checken
			$this->content->template['dir_perm']=$this->check_verzeichnis_rechte();
			break;

		}
	}

	/**
	 * Checken ob Schreibrechte bestehen
	 *
	 * @return mixed
	 */
	function check_verzeichnis_rechte()
	{
		$verz_array = array(
		"../styles");
		$i=0;
		foreach ($verz_array as $file) {
			if (!$this->checkpermission($file)) {
				$perm[$i]['perm'] = "no";
				$perm[$i]['file'] = $file;
			}
			else {
				$perm[$i]['perm'] = "ja";
				$perm[$i]['file'] = $file;
			}
			$i++;
		}
		return $perm;
	}

	/**
	 * checken ob die Verzeichnisse beschreibbar sind
	 *
	 * @param $filename
	 * @return bool
	 */
	function checkpermission($filename)
	{
		$dirname=$filename;
		$filename=$dirname."/index.html";
		if (@is_dir($dirname)) {
			@chmod ($dirname, 0777);
		}
		#if (!file_exists($filename))
		#{
		if ( !@fopen( "$filename", "w+" ) ) {
			return false;
		}
		else {
			@unlink($filename);
			return true;
		}
		#}
		#return false;
	}

	/**
	 * checken ob die Verzeichnisse beschreibbar sind
	 *
	 * @param $filename
	 * @return bool
	 */
	function checkpermission_new( $filename )
	{
		$dirname=$filename;
		if (is_dir($dirname)) {
			chmod ($dirname, 0777);
			mkdir($dirname."/newtemplate_test");
			$filename=$dirname."/newtemplate_test/index.html";
		}
		if ( !@fopen( "$filename", "w+" ) ) {
			unlink($dirname."/newtemplate_test/index.html");
			rmdir($dirname."/newtemplate_test");
			return false;
		}
		else {
			unlink($dirname."/newtemplate_test/index.html");
			rmdir($dirname."/newtemplate_test");
			return true;
		}
	}

	/**
	 * Umlaute und Spezialfälle ersetzen
	 *
	 * @param string $url
	 * @return mixed|string|string[]|null
	 */
	function replace_uml($url = "")
	{
		if (!stristr($url,":::")) {
			// $url=urldecode($url);
			$ae = utf8_encode("ä");
			$aeb = utf8_encode("Ä");
			$ue = utf8_encode("ü");
			$ueb = utf8_encode("Ü");
			$oe = utf8_encode("ö");
			$oeb = utf8_encode("Ö");
			$amp = utf8_encode("&");
			$frag = utf8_encode("\?");
			$ss = utf8_encode("ß");
			$url = str_ireplace(" ", "-", $url);
			// $url=str_ireplace("ue","u-e",$url);
			// $url=str_ireplace("ae","a-e",$url);
			// $url=str_ireplace("oe","o-e",$url);
			$url = str_ireplace("ä", "ae", $url);
			$url = str_ireplace("ö", "oe", $url);
			$url = str_ireplace("ü", "ue", $url);
			$url = str_ireplace("ä", "ae", $url);
			$url = str_ireplace("ö", "oe", $url);
			$url = str_ireplace("ü", "ue", $url);
			$url = str_ireplace($ae, "ae", $url);
			$url = str_ireplace($aeb, "ae", $url);
			$url = str_ireplace($oe, "oe", $url);
			$url = str_ireplace($oeb, "oe", $url);
			$url = str_ireplace($ue, "ue", $url);
			$url = str_ireplace($ueb, "ue", $url);
			$url = str_ireplace("_", "-", $url);
			$url = str_ireplace($amp, "und", $url);
			$url = str_ireplace($ss, "ss", $url);
			$url = str_ireplace($frag, "-digitff", $url);
			$url = str_ireplace('/', '-', $url);
			$url = str_replace('\\', '-', $url);
			$url = str_ireplace('"', '', $url);
			$url = str_ireplace("'", '', $url);
			if (function_exists("mb_strtolower")) {
				$url = mb_strtolower(($url));
			}
			else {
				$url = strtolower(($url));
			}
			$url=str_ireplace("%","",$url);
			$url = urlencode($url);
		}
		return $url;
	}
	// *****************************************************************************************************************
	// ENDE BACKUP ALTE FUNKTONEN
}

$ctemplate = new content_template_class();