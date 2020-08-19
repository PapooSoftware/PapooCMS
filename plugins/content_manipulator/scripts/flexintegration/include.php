<?php

/**
 * Skript ist in dieser Form noch nicht gut getestet...
 *
 * Class flexcmintegration
 */
class flexcmintegration
{
	/**
	 * Counter aktivieren mit true
	 * ACHTUNG wenn aktiviert mu� das Feld angepasst werden in der Flex
	 * indem die Counterzahl gespeichert wird
	 * Der CM legt keine eigene Counter Tabelle an
	 * */
	public $counter_aktiv = false;

	/**
	 * flexcmintegration constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db, $user;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->user = &$user;

		//Admin Ausgab erstellen
		$this->set_backend_message();

		IfNotSetNull($_GET['is_lp']);

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;

			//Speziell für die papoo.de Seite - Referenzen rausholen
			if (strstr( $output,"#flexgetkat")) {
				//Ausgabe erstellen
				$output=$this->create_flexgetkat($output);
			}

			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#flex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration($output);
			}

			if (strstr( $output,"#map_flex_data")) {
				//Ausgabe erstellen
				$output=$this->create_flex_map_integration($output);
			}

			if (strstr( $output,"#goflex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_liste($output);
			}

			if (strstr( $output,"#randflex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_rand($output);
			}

			if (strstr( $output,"#countflex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_count($output);
			}

			if (strstr( $output,"#htmloptionsflex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_htmloptions($output);
			}

			//Counter Skript ist aktiv
			if ($this->counter_aktiv) {
				$output=$this->make_count($output);
			}

			//HIer�ber die Daten eines Eintrages bereitstellen
			//ausgegeben wird pro Variable die einzelen im Artikel stehen mu�
			if (strstr( $output,"#user_flex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_user_dat($output);
			}

			if (stristr( $output,"insert_flex")) {
				//Ausgabe erstellen
				$output=$this->create_flexintegration_search($output);
			}
		}
	}

	/**
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript Flexverwaltung an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = "nobr:Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten z.B. einen bestimmten Eintrag aus der Flexverwaltung ausgeben lassen, die Syntax lautet.<ol>
     <li><strong>#flex_1_1#</strong> - Wobei Sie mit der Ziffer am Anfang die ID der Verwaltung und die Ziffer am Ende die ID des Eintrags selber definieren.</li>
     
     <li><strong>#goflex_3#</strong> - Das können Sie in einem Eintrag eines Users / Mitglieds in der Flexverwaltung eintragen. An dieser Stelle werden dann die Einträge des jeweiligen Users  aus der angegebenen anderen Verwaltung ausgegeben. Die 3 entspricht also der mv_id der Flexverwaltung. Es werden also an der Stelle alle Einträge des Users aus der Verwaltung 3 ausgegeben.</li>
     
     <li><strong>#randflex_1_5_12_4#</strong> - Gibt <b>5</b> Zufallseinträge aus der Verwaltung <b>1</b> aus. Die Einträge verlinken zu dem Menüpunkt mit der ID <b>12</b>. Wird als ID 0 angegeben, wird das Linkziel, soweit möglich, automatisch erkannt. Durch den letzten Parameter erfolgt nach jedem <b>4</b>. Eintrag ein Zeilenumbruch. 0 schaltet diese Funktionalität ab. Die letzten beiden Parameter können auch weggelassen werden und verhalten sich dann, als ob 0 angegeben wäre.</li>

     <li><strong>#user_flex_1#</strong>Tragen Sie im Artikel, oder sonst wo, in dem die Ersetzung erfolgen soll #user_flex_1# ein - die 1 entspricht der mv_id, also der ID der Verwaltung. Auf jeder Seite wo das eingetragen ist, erfolgt die Ersetzung der Inhalte. Um die Inhalte jetzt einzeln in die Seite zu bekommen, tragen Sie einfach die Feldernamen wie sie in der Ausgabe der Flex stehen irgendwo im Fließtext ein, z.B. #Vorname_10# - der Inhalt wird dann wie gewohnt ersetzt.</li>
     
     <li><strong>#user_flex_1#</strong>Tragen Sie im Artikel, oder sonst wo, in dem die Ersetzung erfolgen soll #user_flex_1# ein - die 1 entspricht der mv_id, also der ID der Verwaltung. Auf jeder Seite wo das eingetragen ist, erfolgt die Ersetzung der Inhalte. Um die Inhalte jetzt einzeln in die Seite zu bekommen, tragen Sie einfach die Feldernamen wie sie in der Ausgabe der Flex stehen irgendwo im Fließtext ein, z.B. #Vorname_10# - der Inhalt wird dann wie gewohnt ersetzt.</li>
     <li><strong>&lt;div class=\"insert_flex\"&gt;url mit Suchvariablen&lt;/div&gt;</strong> Hiermit können Sie eine komplette Suche aus der Flex an einer beliebigen Stelle einbauen.<br /> Führen Sie dazu auf der Seite wo die jeweiligen Verwaltung eingebunden ist eine Suche durch, kopieren Sie die resultierende URL aus der Adresszeile des Browsers und fügen Sie diese in den div Block ein.</li>
     </ol> ";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_flexintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#flex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_flex_entry($ndat['1'],$ndat['2']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_flex_map_integration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#map_flex_data(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_flex_entrys($ndat);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_flexintegration_user_dat($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#user_flex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_flex_entry_user($inhalt,$ndat['1']);

			//Ersetzung durchf�hren
			$inhalt = $banner_daten;

			$inhalt = str_ireplace('#user_flex_'.$ndat['1'].'#', "", $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_flexintegration_liste($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#goflex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_flex_entry_liste($ndat['1'],$ndat['2']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_flexintegration_rand($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#randflex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe[1] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID einen Eintrag rausholen
			if (!isset($ndat[3]) || !($ndat[3])) {
				$ndat[3] = 0;
			}
			if (!isset($ndat[4]) || !($ndat[4])) {
				$ndat[4] = 0;
			}
			$banner_daten = $this->get_flex_entry_rand($ndat[1],$ndat[2],$ndat[3],$ndat[4]);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe[0][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string
	 */
	function create_flexgetkat($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#flexgetkat_(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe[1] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);
			//Mit Hilfe der ID die Anzahl ermitteln
			$banner_daten = (string)$this->get_flex_kat_data($ndat[0],$ndat[1]);

			//Ersetzung durchf�hren
			$inhalt = str_replace($ausgabe[0][$i], $banner_daten, $inhalt);
			$i++;
		}

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return string
	 */
	function create_flexintegration_count($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#countflex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe[1] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der ID die Anzahl ermitteln
			$banner_daten = (string)$this->get_flex_count($ndat[1]);

			//Ersetzung durchf�hren
			$inhalt = str_replace($ausgabe[0][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return string
	 */
	function create_flexintegration_htmloptions($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#htmloptionsflex(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe[1] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			//Mit Hilfe der IDs die Eintr�ge ermitteln
			$banner_daten = $this->get_flex_htmloptions($ndat[1], $ndat[2]);

			//Ersetzung durchf�hren
			$inhalt = str_replace($ausgabe[0][$i], $banner_daten, $inhalt);
			$i++;
		}
		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return string|void
	 */
	function create_flexintegration_search($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks

		$inhalt = $this->filter_ausgabe($inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;

	}

	/**
	 * @param int $flex_id
	 *
	 * @return bool|string
	 */
	function get_flex_entrys($flex_id=0)
	{
		$tbname='papoo_mv_content_'.$flex_id['1'].'_search_1';

		$sql=sprintf("SELECT * FROM %s ",
			$this->cms->tbname[$tbname]
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		// ['Auguststr. 4, 53229 Bonn', 'Sonstiger Text'],
		//['Heerstr. 110, 53119 Bonn', 'Noch n Text'],
		//['Burbacherstr. 231, 53129 Bonn', 'Noch n 3. Text']
		// flex_map_data
		$neu="";
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$text=$value['firma_4']."<br />".$value['strnr_15']."<br />".$value['plz_5']." ".$value['Ort_6']."<br />Tel.: ".
					$value['Telefon_9']."<br />Fax.: ".$value['Telefax_10'].'<br />E-Mail: <a href="mailto:'.
					$value['Email_11'].'">'.
					$value['Email_11'].'</a><br />Webseite: <a href="'.$value['Webseite1_12'].'">'.$value['Webseite1_12'].'</a><br /><a href="'.$value['Webseite2_13'].'">'.$value['Webseite2_13'].'</a><br /><a href="'.$value['Webseite3_14'].'">'.$value['Webseite3_14']."</a><br />";

				$neu.="['".$value['strnr_15'].",".$value['plz_5'].",".$value['Ort_6']."','".$text."'],";
			}
		}
		$neu=substr( $neu,0,-1);
		return ($neu);
	}

	/**
	 * @param integer $flex_id
	 * @param integer $id
	 *
	 * @return mixed
	 */
	function get_flex_entry($flex_id=0,$id=0)
	{
		//Flex integrieren
		$this->bind_mv();
		if (is_object($this->mv)) {
			global $db;

			//template=mv/templates/mv_show_front.html&mv_id=1&extern_meta=x&mv_content_id=1
			$this->checked->template='mv/templates/mv_show_front.html';
			$this->checked->mv_id=$flex_id;
			$this->checked->extern_meta="x";
			$this->checked->mv_content_id=$id;
			$this->db = &$db;
			#$this->db->hide_errors();
			$this->mv->meta_gruppe = 1;
			$this->mv->get_users_groups();
			#$this->content->template['mv_template_all'] = "";
			$this->mv->nocontent_ok = "ok";
			$this->mv->show_front();
			$search_mv_id = 1;
			#require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user_front.php');
			#$this->mv->search_user_front(1);
			$temp = ($this->content->template['mv_template_all']);
			$daten = "";
			if (is_array($temp)) {
				foreach ($temp as $single) {
					$daten .= $single;
				}
			}
			else {
				$daten=$temp;
			}
		}
		$daten=str_replace("nobr:","",$daten);
		return ($daten);
	}

	/**
	 * @param integer $flex_id = mv_id
	 * @param integer $id = feld id
	 *
	 * @return string|void
	 */
	function get_flex_htmloptions($flex_id=0,$id=0)
	{
		if (is_numeric($flex_id)) {
			global $db;

			//Flex kann ja nur einmal durchlaufen... daher manuell
			$tbname='papoo_mv_content_'.$flex_id.'_field_rights';

			$sql=sprintf("SELECT * FROM %s 
										LEFT JOIN %s ON gruppenid=group_id
										LEFT JOIN %s ON field_id=	mvcform_id
										WHERE userid='%d' AND LENGTH(mvcform_name) > 2
										GROUP BY mvcform_id",
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->tbname[$tbname],
				$this->cms->tbname['papoo_mvcform'],
				$this->user->userid
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$feld = null;
			//FELD suchen und zusammensetzen
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					if ($value['mvcform_id'] == $id or $value['mvcform_name'] == $id) {
						$feld = $value['mvcform_name'].'_'.$value['mvcform_id'];
						$id = (int)$value['mvcform_id'];
						break;
					}
				}
			}
			if ($feld === NULL) {
				return '';
			}

			$werte = array();

			// Suche nach Lookup-Tabelle und hole ggf. die Daten daraus
			$tbname='papoo_mv_content_'.$flex_id.'_lang_'.$id;
			if (isset($this->cms->tbname[$tbname])) {
				$sql=sprintf("SELECT `lookup_id`, `content` FROM `%s`
								WHERE `lang_id` = %d ORDER BY `order_id`",
					$this->cms->tbname[$tbname],
					(int)$this->cms->lang_id
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				$werte = array();
				foreach ($result as $row) {
					$werte[] = array((int)$row['lookup_id'], $row['content']);
				}
				/*foreach ($result as $row) {
					foreach ($werte as $wert) {
						if ($row['lookup_id'] == $wert[0])
							$neuewerte[] = array($wert[0], $row['content']);
					}
				}
				$werte = $neuewerte;*/
			}

			if (!$werte) {
				//Hole die Eintr�ge aus der Search
				$tbname='papoo_mv_content_'.$flex_id.'_search_1';

				$sql=sprintf("SELECT DISTINCT `%s` FROM `%s`
										WHERE mv_content_sperre = 0
										",
					$feld,
					$this->cms->tbname[$tbname]
				);
				$result=$this->db->get_results($sql, ARRAY_A);

				if ($result) {
					foreach ($result as $row) {
						$werte[] = array($row[$feld], $row[$feld]);
					}
				}
			}

			$daten='';

			$tmp = array();
			foreach ($werte as $wert)
			{
				$tmp[] = '<option value="'.htmlspecialchars($wert[0]).'">'.htmlspecialchars($wert[1]).'</option>';
			}
			$daten = implode('', $tmp);

			return $daten;
		}
	}

	/**
	 * @param integer $flex_id
	 * @param integer $id
	 *
	 * @return string|void
	 */
	function get_flex_entry_liste($flex_id=0,$id=0)
	{
		//Flex integrieren
		$this->bind_mv();
		if (is_numeric($flex_id) && is_numeric($id)) {
			global $db;

			//Flex kann ja nur einmal durchlaufen... daher manuell
			$tbname='papoo_mv_template_'.$flex_id;
			//Zuerst Template rausholen der Flex
			$sql=sprintf("SELECT * FROM %s
										WHERE detail_id=1 AND lang_id='%d'",
				$this->cms->tbname[$tbname],
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$template=($result['0']['template_content_one']);

			//Jetzt die Felder mit den Rechten
			$tbname='papoo_mv_content_'.$flex_id.'_field_rights';

			$sql=sprintf("SELECT * FROM %s 
										LEFT JOIN %s ON gruppenid=group_id
										LEFT JOIN %s ON field_id=	mvcform_id
										WHERE userid='%d' AND LENGTH(mvcform_name) > 2
										GROUP BY mvcform_id",
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->tbname[$tbname],
				$this->cms->tbname['papoo_mvcform'],
				$this->user->userid
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$order="";
			//FELDER zusammensetzen
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					$felder.=$value['mvcform_name'].'_'.$value['mvcform_id'].', ';

					//Wenn eine Orderid vorhanden ist
					if (strstr($value['mvcform_name'],"Sortieren")) {
						$order=' ORDER BY '.$value['mvcform_name'].'_'.$value['mvcform_id'].' ASC';
					}
				}
			}
			$felder=substr($felder,0,-2);

			//Dann die Daten aus der Search mit Rechten
			$tbname='papoo_mv_content_'.$flex_id.'_search_1';


			$sql=sprintf("SELECT %s FROM %s WHERE mv_content_userid='%d' %s",
				$felder,
				$this->cms->tbname[$tbname],
				$id,
				$order
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					$template_schleife=$template;
					if (is_array($value)) {
						foreach ($value as $key2=>$value2) {
							$template_schleife=str_replace("#".$key2."#",$value2,$template_schleife);
						}
					}
					$fertiges_template.=$template_schleife;
				}
			}

			$daten=str_replace("nobr:","",$fertiges_template);
			return ($fertiges_template);
		}
	}

	/**
	 * @param $inhalt
	 * @param $flex_id
	 *
	 * @return mixed
	 */
	function get_flex_entry_user($inhalt, $flex_id)
	{
		global $db;
		//Jetzt die Felder mit den Rechten
		$tbname='papoo_mv_content_'.$flex_id.'_field_rights';

		$sql=sprintf("SELECT * FROM %s 
                                    LEFT JOIN %s ON gruppenid=group_id
                                    LEFT JOIN %s ON field_id=	mvcform_id
                                    WHERE userid='%d' AND LENGTH(mvcform_name) > 2
                                    GROUP BY mvcform_id",
			$this->cms->tbname['papoo_lookup_ug'],
			$this->cms->tbname[$tbname],
			$this->cms->tbname['papoo_mvcform'],
			$this->user->userid
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$order="";
		//FELDER zusammensetzen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if ($value['mvcform_name'].'_'.$value['mvcform_id']!="Informationen_54") {
					$felder.=$value['mvcform_name'].'_'.$value['mvcform_id'].', ';
				}

				//Wenn eine Orderid vorhanden ist
				if (strstr($value['mvcform_name'],"orderby")) {
					$order=' ORDER BY '.$value['mvcform_name'].'_'.$value['mvcform_id'].' DESC';
				}
			}
		}
		$felder=substr($felder,0,-2);

		//Dann die Daten aus der Search mit Rechten
		$tbname='papoo_mv_content_'.$flex_id.'_search_1';


		$sql=sprintf("SELECT %s FROM %s
                                    WHERE mv_content_userid='%d'
                                    %s",
			$felder,
			$this->cms->tbname[$tbname],
			$this->user->userid,
			$order
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (is_array($value)) {
					foreach ($value as $key2=>$value2) {
						$inhalt=str_replace("#".$key2."#",$value2,$inhalt);
					}
				}
			}
		}
		return ($inhalt);
	}

	/**
	 * @param int $flex_id = mv_id
	 * @param int $id = anzahl
	 * @param int $menuid Men�-ID auf die verlinkt wird (0=automatisch)
	 * @param int $br_after F�ge BR nach x Eintr�gen ein (0=nie)
	 *
	 * @return string|void
	 */
	function get_flex_entry_rand($flex_id=0,$id=0,$menuid=0,$br_after=0)
	{
		//Flex integrieren
		$this->bind_mv();
		$br_after = (int)($br_after);
		if (is_numeric($flex_id) && is_numeric($id)) {
			global $db;

			//Flex kann ja nur einmal durchlaufen... daher manuell
			$tbname='papoo_mv_template_'.$flex_id;

			//Zuerst Template rausholen der Flex
			$sql=sprintf("SELECT * FROM %s
										WHERE detail_id=1 AND lang_id='%d'",
				$this->cms->tbname[$tbname],
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$template=($result[0]['template_content_all']);

			//Jetzt die Felder mit den Rechten
			$tbname='papoo_mv_content_'.$flex_id.'_field_rights';

			$sql=sprintf("SELECT * FROM %s 
										LEFT JOIN %s ON gruppenid=group_id
										LEFT JOIN %s ON field_id=	mvcform_id
										WHERE userid='%d' AND LENGTH(mvcform_name) > 2
										GROUP BY mvcform_id",
				$this->cms->tbname['papoo_lookup_ug'],
				$this->cms->tbname[$tbname],
				$this->cms->tbname['papoo_mvcform'],
				$this->user->userid
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$felder="mv_content_id, ";
			//FELDER zusammensetzen
			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					if ($value['mvcform_name'].'_'.$value['mvcform_id']!="Informationen_54") {
						$felder.=$value['mvcform_name'].'_'.$value['mvcform_id'].', ';
					}
				}
			}
			$felder=substr($felder,0,-2);

			//Dann die Daten aus der Search mit Rechten
			$tbname='papoo_mv_content_'.$flex_id.'_search_1';

			$sql=sprintf("SELECT %s FROM %s
										WHERE  mv_content_sperre ='0'
										ORDER BY RAND() LIMIT %d
										",
				$felder,
				$this->cms->tbname[$tbname],
				$id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			if ($menuid<1) {
				$sql=sprintf("SELECT menuid_id FROM %s
										WHERE lang_id='%d'
										AND menulinklang LIKE '%s'",
					$this->cms->tbname['papoo_menu_language'],
					$this->cms->lang_id,
					'%mv_id='.$flex_id.'%'
				);
				$menuid=$this->db->get_var($sql);
			}

			if (is_array($result)) {
				foreach ($result as $key=>$value) {
					$template_schleife=$template;
					if (is_array($value)) {
						//Variablen Inhalte durchgehen
						foreach ($value as $key2=>$value2) {
							$template_schleife=str_replace("#".$key2."#",$value2,$template_schleife);
						}

						//Dann den Link setzen
						$link=PAPOO_WEB_PFAD.'/plugin.php?menuid='.$menuid.'&amp;template=mv/templates/mv_show_front.html&amp;mv_id='.$flex_id.'&amp;extern_meta=x&amp;mv_content_id='.$value['mv_content_id'];
						$template_schleife = preg_replace('/\$\$(.*?)\$\$/',
							"<a href=\"$link\">\\1</a>",
							$template_schleife);
					}
					if ($br_after && (($key % $br_after) == 0))
						$template_schleife .= '<br />';
					$fertiges_template.=$template_schleife;
				}
			}

			$daten=str_replace("nobr:","",$fertiges_template);
			return ($fertiges_template);
		}
	}

	/**
	 * @param int $katid
	 * @param int $typ_id
	 *
	 * @return string
	 */
	function get_flex_kat_data($katid=0, $typ_id=1)
	{
		if (empty($typ_id)) {
			$typ_id=1;
		}

		if ($katid==1) {
			$orderby='ORDER BY orderidstart_9 DESC';
		}
		else {
			$orderby='ORDER BY orderid_8 DESC';
		}

		#$orderby='ORDER BY orderid_8 DESC';

		if ($katid==1) {
			$like='%'.$katid.'\n%';
		}
		else {
			$like='%\n'.$katid.'\n%';
		}

		//Das ist fix so zusortiert...
		$sql=sprintf("SELECT * FROM %s WHERE kategorie_6 LIKE '%s'
						 %s LIMIT 16",
			DB_PRAEFIX."papoo_mv_content_1_search_1",
			$like,
			$orderby
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		//$template=file_get_contents(PAPOO_ABS_PFAD."/plugins/content_manipulator/scripts/flexintegration/templates/papoo_ref.html");

		$html=' <div class="row"><div class="slider multiple-items">';

		foreach ($result as $k=>$v) {
			$beschreibung=substr($v['Beschreibungkurz_3'],0,180)."";
			$sprechende_url = "f-1"."-". $v['mv_content_id']."-". $v['Sprechendeurl_7'].".html";

			if ($typ_id==1) {
				$inner_html = '<div class="item">
                                    <div class="inner">
                                    <a href="' . $sprechende_url . '"><img alt="" src="/images/' . $v['Screenshot_2'] . '" />
                                    	<button><i class="fa fa-arrow-circle-right"></i>Ansehen</button>
                                    </a>
                                        <div>
                                            <strong>' . $v['berschrift_1'] . '</strong>
                                            <div>
                                               ' . $beschreibung . '
                                            </div>
                                            <a href="' . $sprechende_url . '">mehr erfahren</a>
                                        </div>
                                    </div>
                               </div>';
			}

			if ($typ_id==2) {
				$inner_html='<div class="item">
					<div class="inner">
						<a href="'.$sprechende_url.'">
							<img title="'.$v['berschrift_1'].'" alt="'.$v['berschrift_1'].'" src="/images/'.$v['Screenshot_2'].'" height="670" width="902" />
							<button>Ansehen</button> </a>
						<strong>'.$v['berschrift_1'].'<br /></strong>
					</div>
				</div>';
			}
			$html .= $inner_html;
		}
		$html.='</div></div>';
		return $html;
	}

	/**
	 * @param integer $flex_id
	 *
	 * @return integer
	 */
	function get_flex_count($flex_id=0)
	{
		$lang_id = (int)$this->cms->lang_id;
		$flex_id = (int)$flex_id;

		// Rechte bestimmen
		$tbname='papoo_mv_content_'.$flex_id.'_content_rights';
		if (!isset($this->cms->tbname[$tbname])) return NULL;
		$sql = sprintf('SELECT MAX(`group_read`) FROM `%s` LEFT JOIN `%s`
						ON `group_id` = `gruppenid`
						WHERE `userid` = %d OR `user_id` = %d',
			$this->cms->tbname[$tbname],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid,
			$this->user->userid
		);
		$can_read = (bool)$this->db->get_var($sql);
		if (!$can_read) {
			return NULL;
		}

		// Anzahl der Eintr�ge lesen
		$tbname='papoo_mv_content_'.$flex_id.'_search_'.$lang_id;
		if (!isset($this->cms->tbname[$tbname])) {
			return 0;
		}

		$sql = sprintf('SELECT COUNT(*) FROM `%s` WHERE mv_content_sperre = 0',
			$this->cms->tbname[$tbname]);
		return (int)$this->db->get_var($sql);
	}

	/**
	 * Z�hlt einen Counter hoch,
	 * Feld dazu mu� in der Flex erzeugt werden
	 *
	 * @param $output
	 *
	 * @return mixed|string|string[]|null $output
	 */
	function make_count($output)
	{
		//Nur wenn mv_content_id gesetzt
		if (is_numeric($this->checked->mv_content_id)) {
			//Dann den Eintrag mit dieser ID einen hochz�hlen
			$sql=sprintf("UPDATE %s
										SET counter_74=counter_74 + 1
                                        WHERE mv_content_id='%d'",
				$this->cms->tbname['papoo_mv_content_2_search_1'],
				$this->db->escape($this->checked->mv_content_id)
			);
			$this->db->query($sql);
		}

		$sql=sprintf("SELECT counter_74 FROM %s
									WHERE mv_content_owner='%d'",
			$this->cms->tbname['papoo_mv_content_2_search_1'],
			$this->user->userid
		);
		$counter=$this->db->get_var($sql);
		$output = str_ireplace('#counter_flex#', $counter, $output);

		return $output;
	}

	/**
	 * Die mv Klasse einbinden
	 *
	 * @return void
	 */
	function bind_mv()
	{
		global $mv;
		$this->mv = &$mv;
	}

	/**
	 * Hier werden die Daten per Snoopy ausgelesen
	 *
	 * @param string $url
	 * @param string $seite_url
	 *
	 * @return string Daten der externen Seite
	 */
	function get_data_extern($url = "", $seite_url = "")
	{
		$url = html_entity_decode($seite_url);
		$url = str_replace('/', '%2F', $url);
		$url = str_replace('http:%2F%2F', 'http://', $url);
		$url = str_replace('%2Fplugin.php', '/plugin.php', $url);
		require_once (PAPOO_ABS_PFAD . "/lib/classes/extlib/Snoopy.class.inc.php");
		$html = new Snoopy();
		$html->agent = "Web Browser";
		$html->referer = $_SERVER["HTTP_REFERER"];
		$html->fetch($url);
		$daten = $html->results;
		return $daten;
	}

	/**
	 * Inhalte mit Links wird umgewandelt
	 *
	 * @param string $daten
	 * @param string $seite_url
	 *
	 * @return string|string[]
	 */
	function content_mani_recode_content($daten = "", $seite_url = "")
	{
		$daten1 = explode('<!-- START-LISTE -->', $daten);
		//<div class="printfooter">
		$daten2 = explode('<!-- STOP-LISTE -->', $daten1['1']);
		$zwischen = $daten2['0'];
		// Externe Links gehen ins Blank
		$zwischen = str_ireplace("href=\"http://", "target=\"blank\" href=\"http://", $zwischen);
		// Bilder korriegieren /images/
		$zwischen = str_ireplace("/images/", $seite_url . "/images/", $zwischen);
		// template Variable verschleiern
		$zwischen = str_ireplace('name="template"', 'name="template_fremd"', $zwischen);
		// Action umbieren
		$zwischen = str_ireplace('action="/plugin.php"', 'action="' . PAPOO_WEB_PFAD . '/plugin.php"', $zwischen);
		$zwischen = str_ireplace('action="plugin.php"', 'action="' . PAPOO_WEB_PFAD . '/plugin.php"', $zwischen);
		$zwischen = str_ireplace('</form>',
			'<input value="flexint/templates/flexintfront.html" name="template" type="hidden"></form>', $zwischen);
		// template Varialen korrigieren
		$zwischen = str_ireplace('template=', 'template=flexint/templates/flexintfront.html&template_fremd=', $zwischen);
		// Links korrigieren
		$zwischen = str_ireplace('href="/plugin.php', 'href="' . PAPOO_WEB_PFAD . '/plugin.php', $zwischen);
		$zwischen = str_ireplace('href="plugin.php', 'href="' . PAPOO_WEB_PFAD . '/plugin.php', $zwischen);
		$zwischen = str_ireplace('menuid=', 'menuid=' . $this->checked->menuid . '&xyz=', $zwischen);
		$zwischen = preg_replace('/<input type="hidden" value="(.*?)" name="menuid" \/>/i',
			'<input value="' . $this->checked->menuid . '" name="menuid" type="hidden">', $zwischen);
		$ausgabe = $zwischen;
		return $ausgabe;
	}

	/**
	 * Hier wird aus einer externen Seite der Content
	 * ausgelesen und angepasst
	 *
	 * @param array $check
	 *
	 * @return string|string[] Insert
	 */
	function get_extern($check = array())
	{
		// Startwert setzen
		$get = "?nix=1";
		// Daten aus der URL im Text durchgehen und �bergeben
		foreach ($check as $key => $value) {
			if ($key == "seite_url") continue;
			$get .= "&" . $key . "=" . $value . "";
		}
		// Link erstellen
		$url = trim($check['seite_url']) . $get;
		$url_echt = trim($check['seite_url']);
		$url_echt = str_replace("/plugin.php", "", $url_echt);
		// Daten holen per Snoopy
		#$daten = $this->get_data_extern($url, $url_echt); 
		$daten = $this->get_data_extern($url, $check['seite_url2']);
		// Daten umwandeln
		$daten = $this->content_mani_recode_content($daten, $url_echt);
		// R�ckgabe
		return $daten;
	}

	/**
	 * Inhalte zusammenf�hren
	 *
	 * @param string $insert
	 * @param $inhalt
	 *
	 * @return string|string[]
	 */
	function make_text($insert = "",$inhalt)
	{
		$insert = str_ireplace("nobr:", "", $insert);
		$insert = str_ireplace("index.php", "plugin.php", $insert);
		$text = $this->inhalt;
		$text_insert = '<div class="ausgabe_flex">' . $insert . '</div>';
		#$text= preg_replace('/<span class="insert_flex">(.*?)<\\/span>/i', $text_insert, $inhalt,1);
		$text= preg_replace('/<(span|p|div) class="insert_flex">(.*?)<\\/(span|p|div)>/i', $text_insert, $inhalt,1);

		#preg_replace('/<(span|p|div) class="insert_flex">(.*?)<\\/(span|p|div)>/i', $text_insert, $text, 1);
		return $text;
	}

	/**
	 * Die Inhalte an das checked Objekt �bergeben
	 *
	 * @param string $check
	 *
	 * @return void
	 */
	function give_checked($check = "")
	{
		// Eintr�ge �bergeben
		if (is_array($check)) {
			foreach ($check as $key => $value) {
				if ($key == "menuid") {
					$value = $this->checked->menuid;
				}
				$this->checked->$key = $value;
			}
		}
	}

	/**
	 * Var Inhalte des Links rausholen
	 *
	 * @param array $link_a
	 *
	 * @return void
	 */
	function get_link_content($link_a = array())
	{
		if (is_array($link_a)) {
			$i = 0;
			foreach ($link_a as $link) {
				if (stristr( $link,"mv_search")) {
					if (stristr($link,"insert_flex")) {
						$link1 = explode("insert_flex", $link);
					}
					else {
						$link1['1']=$link;
					}

					$link2 = explode("</", $link1['1']);
					$link3 = explode("http", $link2['0']);
					$link4= "http".$link3['1'];
					$link4 = str_ireplace("amp;", "", $link4);
					$link4 = urldecode($link4);
					$check['0']['seite_url']=$link4;
					$check['0']['seite_url2']=$link4;
				}
			}
		}
		return $check;
	}

	/**
	 * Filter den Link aus dem Content raus
	 *
	 * @param mixed $inhalt
	 *
	 * @return array mit Links
	 */
	function get_link_data($inhalt = array())
	{
		//Inhalte aufexploden
		preg_match_all("|<span [^>]+>(.*)</span>|U", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		preg_match_all("|<div [^>]+>(.*)</div>|U", $inhalt, $ausgabe3, PREG_PATTERN_ORDER);
		preg_match_all("|<p[^>]+>(.*)</p>|U", $inhalt, $ausgabe2, PREG_PATTERN_ORDER);
		$causgabe = array_merge($ausgabe['1'], $ausgabe2['1'], $ausgabe3['1']);
		// R�ckgabe ARRAY mit den Links
		return $causgabe;
	}

	/**
	 * Diese Funktion filter die Ausgabe im 3. Spalte
	 * und ersetzt die Vorgaben mit den Inhalten aus der Flexverwaltung
	 *
	 * @param $inhalt
	 *
	 * @return string|string[]
	 */
	function filter_ausgabe($inhalt)
	{
		//Link rausholen
		$link = $this->get_link_data($inhalt);
		if (!empty($link)) {
			//Link aufexploden
			$check = $this->get_link_content($link);
			if (is_array($check)) {
				foreach ($check['0'] as $key=>$value) {
					if (stristr($value,"http") && stristr($value,"mv") ) {
						$value=str_ireplace("\n","",$value);
						$value=html_entity_decode(str_ireplace("\r","",$value));
						$data=diverse_class::get_url_get(($value));
						$d1=explode("<!-- START-LISTE -->",$data);
						$d2=explode("<!-- STOP-LISTE -->",$d1['1']);
						$text= $this->make_text($d2['0'],$inhalt);
					}
				}
			}
			//Sonderfall leer - dann trotzdem was �bergeben
			if (empty($text)) {
				$text=$inhalt;
			}

			// An Template �bergeben
			$ausgabe = $text;
			$i++;
		}

		return $ausgabe;
	}



	/**
	 * Hier wird die Suchmaske aus der MV rausgeholt und ausgegeben
	 *
	 * @return void
	 */
	function show_search_mv()
	{
		//Flex integrieren
		$this->bind_mv();
		if (is_object($this->mv)) {
			global $db;
			//template=mv/templates/mv_show_front.html&mv_id=1&extern_meta=x&mv_content_id=1
			$this->checked->template='mv/templates/mv_search_front_onemv.html';
			$this->checked->mv_id=$this->checked->onemv;
			$this->checked->extern_meta="x";
			$this->checked->mv_content_id=$id;
			$this->db = &$db;
			$this->db->hide_errors();
			$this->mv->meta_gruppe = 1;
			$this->mv->get_users_groups();
			#$this->content->template['mv_template_all'] = "";
			$this->mv->nocontent_ok = "ok";
			$this->mv->post_papoo();
			$this->mv->show_front();
			$search_mv_id = 1;
			#require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/search_user_front.php');
			$this->mv->search_user_front(1);
			$temp = ($this->content->template['mv_template_all']);
			$daten = "";
			if (is_array($temp)) {
				foreach ($temp as $single) {
					$daten .= str_replace("nobr:","",$single);
				}
			}
			else {
				$daten=$temp;
			}
		}
		return $daten;
	}
}

$flexcmintegration=new flexcmintegration();
