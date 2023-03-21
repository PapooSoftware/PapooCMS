<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class single_artikel
 */
#[AllowDynamicProperties]
class single_artikel
{
	/**
	 * single_artikel constructor.
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
			if (strstr( $output,"#single")) {
				//Ausgabe erstellen
				$output=$this->create_single_artikelintegration($output);
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
		$this->content->template['plugin_cm_head']['de'][]="Bestimmter Artikel X angeteasert ausgeben";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten einen bestimmten Artikel platzieren. Die Synthax lautet <strong>#single_artikel_2_1#</strong> - die 2 entspricht der Artikel ID, die 1 dem Template.";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_single_artikelintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#single(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);
			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_single_artikel($ndat['2'],$ndat['3']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		$this->content->assign(false);

		IfNotSetNull($this->fourcss);
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
		global $cms, $smarty;
		$filename = 'plugins/content_manipulator/scripts/'.basename(__DIR__).'/templates/teaser'.$id.'.html';

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$cms->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		], function ($template, $filename) use ($smarty) {
			return $template !== false ? $template : (is_file($filename) ? $smarty->fetch($filename) : $template);
		}, false);
	}

	/**
	 * @param int $id
	 * @return string|null Der Pfad zum Template, wenn es im Style- oder Pluginverzeichnis gefunden wird; ansonsten null.
	 */
	private function findTemplate(int $id)
	{
		global $cms;
		$filename = 'plugins/content_manipulator/scripts/'.basename(__DIR__).'/templates/teaser'.$id.'.html';

		return array_reduce([
			PAPOO_ABS_PFAD."/styles/{$cms->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		], function ($template, $filename) {
			return $template ?? (is_file($filename) ? $filename : $template);
		}, null);
	}

	/**
	 * @param int $reporeid
	 * @param int $template_var
	 *
	 * @return string
	 */
	function get_single_artikel($reporeid=0,$template_var=0)
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

		$this->checked->reporeid=$reporeid;
		$org_id=$this->checked->menuid;
		$this->checked->menuid="xy";
		$tmp404Check = $cms->system_config_data['config_404_benutzen_check'];
		$cms->system_config_data['config_404_benutzen_check'] = false;

		#$this->cms->sqllimit=" LIMIT 0,".$count;
		#$artikel->orderfield=' GROUP BY reporeID ORDER BY count DESC';
		#$this->checked->reporeid="";

		if (defined("admin")) {
			$menu->data_front_complete = $menu->menu_data_read("FRONT");
		}

		$artikel_daten=$artikel->make_teaser();

		$cms->system_config_data['config_404_benutzen_check'] = $tmp404Check;
		$this->checked->menuid=$org_id;

		if (!empty($artikel_daten)) {
			foreach ($artikel_daten as $key=>$value) {
				if(!isset($value['lan_teaser'])) {
					$value['lan_teaser'] = NULL;
				}

				$datum=$value['article_datum'];
				$datum1=explode(" ",$datum);
				$datum2=explode("-",$datum1['0']);
				$mon=$this->make_monat(@date("M",@mktime(0,0,0,$datum2['1'],1,$datum2['0'])));
				$artikel_daten[$key]['article_datum_mon']=$mon;
				$artikel_daten[$key]['lan_teaser_sans']=substr(strip_tags($value['lan_teaser']),0,60)."...";
				$artikel_daten[$key]['lan_teaser_sans2']=substr(strip_tags($value['lan_teaser']),0,300)."...";
				# $artikel_daten[$key]['uberschrift']=substr(trim(strip_tags($value['uberschrift'])),0,120)."...";
				#$artikel_daten[$key]['uberschrift']=substr($value['uberschrift'],0,120)."...";
				//BIld aus Teaser
				;
				preg_match_all( '/<img[^>]*>/Ui', $value['lan_teaser'], $img );
				#$img['0']['0']=str_replace("/thumbs","",$img['0']['0']);
				$img['0']['0']=str_replace("style","rel",$img['0']['0']);
				$artikel_daten[$key]['img_tag']=$img['0']['0'];
				IfNotSetNull($datum2['2']);
				$artikel_daten[$key]['article_datum_tag']=$datum2['2'];
				#$neu[]=get_object_vars($value);

				$createTimestamp = (int)$value['erstellungsdatum'];
				$artikel_daten[$key] += [
					'create_date_dd_mm_yyyy' => date('d.m.Y', $createTimestamp),
				];
			}
		}
		#$content->template['single_artikel'] =  $artikel_daten;
		#$content->assign();

		// templates parsen
		# $output = $smarty->fetch(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/letzte_artikel/templates/teaser".$template.".html");

		// Sofern Template als Smarty-Template gekennzeichnet, alle Artikel in einem Schwung durchreichen.
		if ($templatePathname = $this->findTemplate((int)$template_var)) {
			$smartyIdentifier = '{*##SMARTY##*}';
			if (strpos(file_get_contents($templatePathname, false, null, 0, strlen($smartyIdentifier)), $smartyIdentifier) === 0) {
				$content->assign(false);
				$smarty->assign([
					'article' => is_array($artikel_daten) ? reset($artikel_daten) ?: [] : [],
					'modFree' => $cms->mod_free,
				]);

				$result = $smarty->fetch($templatePathname);
				return $result;
			}
		}

		$return="";

		if (($template = $this->loadTemplate($template_var)) === false) {
			return "<strong>FEHLER: [Content-Manipulator] Single-Artikel Template teaser{$template_var}.html nicht gefunden.</strong>";
		}
		$template_org=$template;

		if (is_array($artikel_daten)) {
			foreach ($artikel_daten as $key=>$value) {
				$template=$template_org;
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						$template=str_replace("#".$key2."#",$value2,$template);
					}
				}
				#url_trenner
				$template=str_replace("#url_trenner#",$content->template['slash'].$content->template['sulrstrenner'],$template);

				$return.=$template;
			}
		}
		#global $content;
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
				$mon = "Jan";
				break;

			case "Feb":
				$mon = "Feb";
				break;

			case "Mar":
				$mon = "M&auml;r";
				break;

			case "Apr":
				$mon = "Apr";
				break;

			case "May":
				$mon = "Mai";
				break;

			case "Jun":
				$mon = "Jun";
				break;

			case "Jul":
				$mon = "Jul";
				break;

			case "Aug":
				$mon = "Aug";
				break;

			case "Sep":
				$mon = "Sep";
				break;

			case "Oct":
				$mon = "Okt";
				break;

			case "Nov":
				$mon = "Nov";
				break;

			case "Dec":
				$mon = "Dez";
				break;

			default:

				break;
			}
			return $mon;
		}
	}
}

$single_artikel=new single_artikel();
