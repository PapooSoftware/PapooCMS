<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class last_artikel
 */
#[AllowDynamicProperties]
class last_artikel
{
	/**
	 * last_artikel constructor.
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
			if (strstr( $output,"#last_artikel")) {
				//Ausgabe erstellen
				$output=$this->create_last_artikelintegration($output);
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
		$this->content->template['plugin_cm_head']['de'][]="Die letzten x Artikel";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein die letzten x Artikel des Menüpunktes y ausgeben lassen, die Syntax lautet.<br /><strong>#last_artikel_2_10_4#</strong><br />Die Ziffer am Anfang bezeichnet das Template, die Ziffer am Ende die ID des Menüpunktes und die Ziffer in der Mitte die Anzahl.  Die folgenden TemplateIDs sind möglich:<ol><li>Standardausgabe </li><li>Ausgabe mit vorangestelltem Datum ohne Bilder - der gesamte Eintrag ist verlinkt. </li><li>Ausgabe nur Bild und Link </li><li>Nicht belegt </li><li>Standardausgabe </li><li>Ausgabe als Slideshow - Breite und Sliding muss im Skript noch angegeben werden <br />(plugins/content_manipulator/scripts/letzte_artikel/templates/teaser6.css)<br />(plugins/content_manipulator/scripts/letzte_artikel/include.php Zeile 144 / 145) </li></ol> ";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_last_artikelintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#last_artikel(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);
			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_last_artikel($ndat['2'],$ndat['3'],$ndat['1']);

			//Ersetzung durchf�hren
			if (stristr($inhalt,"<p>".$ausgabe['0'][$i]."</p>")) {
				$inhalt = str_ireplace("<p>".$ausgabe['0'][$i]."</p>", $banner_daten, $inhalt);
			}
			else {
				$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			}
			$i++;
		}

		if(!isset($this->fourcss)) {
			$this->fourcss = NULL;
		}

		$inhalt = str_ireplace('</head>', $this->fourcss.'</head>', $inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $id
	 * @return bool|string Das angeforderte Template, wenn es im Style- oder Pluginverzeichnis gefunden wird, ansonsten false
	 * @author Christoph Zimmer
	 */
	private function loadTemplate($id) {
		$filename = "plugins/content_manipulator/scripts/letzte_artikel/templates/teaser{$id}.html";

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$this->cms->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		], function ($template, $filename) {
			return $template !== false ? $template : (is_file($filename) ? $GLOBALS["smarty"]->fetch($filename) : $template);
		}, false);
	}

	/**
	 * @param int $id
	 * @return string|null Der Pfad zum Template, wenn es im Style- oder Pluginverzeichnis gefunden wird; ansonsten null.
	 */
	private function findTemplate(int $id)
	{
		$filename = "plugins/content_manipulator/scripts/letzte_artikel/templates/teaser{$id}.html";

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$this->cms->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		], function ($template, $filename) {
			return $template ?? (is_file($filename) ? $filename : $template);
		}, null);
	}

	/**
	 * @param int $count
	 * @param int $menuid
	 * @param int $template_var
	 *
	 * @return string
	 */
	function get_last_artikel($count=0,$menuid = 0,$template_var=0)
	{

		global $artikel;
		global $cms;
		/** @var Smarty $smarty */
		global $smarty;
		global $content;
		global $menu;
		if (!is_numeric($template_var)) {
			$template_var=1;
		}

		if ($template_var==4 ) {
			//<link type="text/css" media="screen" rel="stylesheet" href="/papoo_trunk/styles_default/css/colorbox.css" />

			//PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.".css"
			$this->fourcss='<link type="text/css" media="screen" rel="stylesheet" href="'.PAPOO_WEB_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.'.css" />';

			//<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>

			$this->fourcss.='<script type="text/javascript" src="'.PAPOO_WEB_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.'.js"></script>';
		}
		if ($template_var==4 || $template_var==6) {
			//<link type="text/css" media="screen" rel="stylesheet" href="/papoo_trunk/styles_default/css/colorbox.css" />

			//PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.".css"
			$this->fourcss='<link type="text/css" media="screen" rel="stylesheet" href="'.PAPOO_WEB_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.'.css" />';

			//<script type="text/javascript" src="js/jquery.colorbox-min.js"></script>

			$this->fourcss.='<script type="text/javascript" src="'.PAPOO_WEB_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template_var.'.js"></script><script type="text/javascript">
$(document).ready(function() {
    $(\'.slideshow\').cycle({
		fx:    \'fade\',
    delay: -100
	});
});

</script>
';
		}
		$org_id=$this->checked->menuid;
		$this->checked->menuid=$menuid;

		if ($template_var==5) {
			//Alle Untermenupunkte des Men�punktes
			$sql=sprintf("SELECT menuid FROM %s WHERE untermenuzu='%d'",
				$this->cms->tbname['papoo_me_nu'],
				$this->db->escape($menuid));
			$menuids=$this->db->get_results($sql,ARRAY_A);
			if (is_array($menuids)) {
				foreach ($menuids as $key=>$value) {
					$mnids[]=$value['menuid'];
				}
			}
			$mnids[]=$menuid;
			#$artikel->sub_menids=$mnids;

			$this->checked->menuid='x';
			$this->checked->nocat='x';
			$artikel->orderfield_org=$artikel->orderfield;
			$artikel->orderfield=' GROUP BY reporeID ORDER BY erstellungsdatum DESC, lart_order_id ASC';
		}

		$this->cms->sqllimit=" LIMIT 0,".$count;

		$this->checked->reporeid="";

		if ($template_var==4) {
			$this->checked->menuid='x';
			#$this->cms->stamm_artikel_order=0;
		}

		if (defined("admin")) {
			$menu->data_front_complete = $menu->menu_data_read("FRONT");
		}

		$artikel_daten=$artikel->make_teaser();

		//Menuname rausfischen
		$sql=sprintf("SELECT menuname FROM %s WHERE menuid_id='%d'",
			$this->cms->tbname['papoo_menu_language'],
			$this->db->escape($menuid));
		$menuname=$this->db->get_var($sql);

		// FIXME: Hat einen Fehler produziert
		//$artikel->orderfield=$artikel->orderfield_org;
		$this->checked->menuid=$org_id;

		if (!empty($artikel_daten)) {
			foreach ($artikel_daten as $key=>$value) {
				if ($template_var==5) {
					if (!in_array($value['cattextid'],$mnids)) {
						unset($artikel_daten[$key]);
						continue;
					}
				}

				if ($template_var==4) {
					if ($value['cat_category_id']!="1") {
						unset($artikel_daten[$key]);
						continue;
					}
				}
				if ($template_var==3) {
					$i++;
					if ($i>3) {
						unset($artikel_daten[$key]);
						continue;
					}
				}
				$datum=$value['erstellungsdatum'];
				$datum1=explode(" ",$datum);
				$datum2=explode("-",$datum1['0']);
				$mon=$this->make_monat(@date("M",$datum));
				$tag=$this->make_monat(@date("d",$datum));
				$artikel_daten[$key]['lan_datum']=date("d M Y",$value['erstellungsdatum']);
				$artikel_daten[$key]['article_datum_mon']=$mon;

				$createTimestamp = (int)$value['erstellungsdatum'];
				$artikel_daten[$key] += [
					'create_date_dd_mm_yyyy' => date('d.m.Y', $createTimestamp),
				];

				$umlaute = array(
					"&euro;" => "€",
					"&nbsp;" => " ",
					"&uml;" => "'",
					"&copy;" => "©",
					"&Auml;" => "Ä",
					"&Ouml;" => "Ö",
					"&Uuml;" => "Ü",
					"&szlig;" => "ß",
					"&auml;" => "ä",
					"&ouml;" => "ö",
					"&uuml;" => "ü"
				);
				$mon = strtr($mon, $umlaute);

				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				$artikel_daten[$key]['article_datum_mon_short']=mb_substr($mon,0,3);
				$artikel_daten[$key]['lan_teaser_sans']=substr(strip_tags($value['lan_teaser']),0,120)."...";
				$artikel_daten[$key]['lan_teaser_sans2']=substr(strip_tags($value['lan_teaser']),0,300)."...";

				$artikel_daten[$key]["full_teaser_text_no_html"] = strip_tags($value["lan_teaser"]);
				$artikel_daten[$key]["heading"] = strip_tags($value["uberschrift"]);

				# $artikel_daten[$key]['uberschrift']=substr(trim(strip_tags($value['uberschrift'])),0,120)."...";
				#$artikel_daten[$key]['uberschrift']=substr($value['uberschrift'],0,120)."...";
				//BIld aus Teaser
				;
				preg_match_all( '/<img[^>]*>/Ui', $value['lan_teaser'], $img );
				//$img['0']['0']=str_replace("/thumbs","",$img['0']['0']);
				$img['0']['0']=str_replace("style","longdesc",$img['0']['0']);
				$artikel_daten[$key]['img_tag']=$img['0']['0'];

				if (preg_match('~src="(?<path>[^"]+)~', $img[0][0], $match)) {
					$artikel_daten[$key]["thumbnailPath"] = preg_replace('~(plugins/galerie/galerien/[^/]+/)(?!thumbs/)~', '\1thumbs/', $match["path"]);
					$artikel_daten[$key]["imagePath"] = preg_replace('~(plugins/galerie/galerien/[^/]+/)(?=thumbs/)~', '\1', $match["path"]);
					$artikel_daten[$key]["imageUID"] = uniqid();
				}

				$artikel_daten[$key]['article_datum_tag']=$tag;
				$artikel_daten[$key]['menuname']=$menuname;
				#$neu[]=get_object_vars($value);
			}
		}
		#$content->template['last_artikel'] =  $artikel_daten;
		#$content->assign();

		// templates parsen
		# $output = $smarty->fetch(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template.".html");

		// Sofern Template als Smarty-Template gekennzeichnet, alle Artikel in einem Schwung durchreichen.
		if ($templatePathname = $this->findTemplate((int)$template_var)) {
			$smartyIdentifier = '{*##SMARTY##*}';
			if (strpos(file_get_contents($templatePathname), $smartyIdentifier) === 0) {
				$smarty->assign([
					'lastArticles' => is_array($artikel_daten) ? $artikel_daten : [],
					'modFree' => $cms->mod_free,
				]);

				return $smarty->fetch($templatePathname);
			}
		}

		$return="";

		if (($template = $this->loadTemplate($template_var)) === false) {
			return "<strong>FEHLER: [Content-Manipulator] Letzte-Artikel Template teaser{$template_var}.html nicht gefunden.</strong>";
		}
		$template_org=$template;

		if (is_array($artikel_daten)) {
			$art_count=0;
			foreach ($artikel_daten as $key=>$value) {
				if ($art_count >= $count) {
					continue;
				}
				$template=$template_org;

				if ($this->cms->mod_free!=1) {
					$value['url_header']="index.php?menuid=".$value['cattextid']."&reporeid=".$value['reporeid'];
				}
				if ($value['dok_show_teaser_link']==1) {
					$value['islink']="";
				}
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						$template=str_replace("#".$key2."#",$value2,$template);
					}
				}

				if ($this->cms->mod_free==1) {
					#url_trenner
					//$template = str_replace("#url_trenner#", $content->template['slash'] . $content->template['sulrstrenner'], $template);
					$template = str_replace("#url_trenner#", $content->template['slash'] , $template);
				}
				else {
					$template = str_replace("#url_trenner#", $content->template['slash'], $template);
				}
				$return.=$template;

				$art_count++;
			}
		}
		if ($template_var==6) {
			$return='<div class="slideshow">'.$return.'</div>'	;
		}
		//$return.='<div class="break_artikel"></div>';
		#	global $content;
		#$template_cloud=str_replace("#message_plugin_keywords_schlagworte#",$content->template['message_plugin_keywords_schlagworte'],$template_cloud);
		#$return=str_replace("#cloud_liste#",$return,$template_cloud);
		#$return.="";

		// templates parsen
		#$output = $smarty->fetch(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/tagcloud/templates/cloud.html");
		return $return;

		#return $output;

		//Beispiel zur�ckgeben.
		#return "Dies ist der Beispieltext Nr: ".$id;
	}

	/**
	 * Monatsnamen korrekt darstellen
	 *
	 * @param $mon
	 *
	 * @return string
	 */
	function make_monat( $mon )
	{
		if ( $this->cms->lang_id == 2 ) {
			return $mon;
		}
		else {
			switch ( $mon ) {
			case "Jan":
				$mon = "Januar";
				break;

			case "Feb":
				$mon = "Februar";
				break;

			case "Mar":
				$mon = "M&auml;rz";
				break;

			case "Apr":
				$mon = "April";
				break;

			case "May":
				$mon = "Mai";
				break;

			case "Jun":
				$mon = "Juni";
				break;

			case "Jul":
				$mon = "Juli";
				break;

			case "Aug":
				$mon = "August";
				break;

			case "Sep":
				$mon = "September";
				break;

			case "Oct":
				$mon = "Oktober";
				break;

			case "Nov":
				$mon = "November";
				break;

			case "Dec":
				$mon = "Dezember";
				break;

			default:
				break;
			}
			return $mon;
		}
	}
}

$last_artikel=new last_artikel();
