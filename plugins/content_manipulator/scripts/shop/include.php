<?php

/**
 * Hier handelt es sich um eine Beispiel Klasse
 * Die mu� man nicht so benutzen.
 * Im Prinzip kann man hier reinschreiben was man m�chte
 *
 * Class cm_shop
 */
#[AllowDynamicProperties]
class cm_shop
{
	/**
	 * cm_shop constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		if (!empty($this->cms->tbname['plugin_shop_attribute'])) {
			#$shop = new shop_class();
			#$this->shop = &$shop;

			#$shop_front = new shop_class_frontend();
			#$this->shop_front=$shop_front;
		}
		if(function_exists("shop_class_frontend")) {
			$this->shop_front = new shop_class_frontend();
		}

		#print_r($this->shop_front)
		//Admin Ausgab erstellen
		$this->set_backend_message();

		//Frontend - dann Skript durchlaufen
		if (!defined("admin") || ($_GET['is_lp']==1)) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#shop_produkt")) {
				//Ausgabe erstellen
				$output=$this->create_single_produktintegration($output);
			}

			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#shop_kat")) {
				$output=$this->create_kat_produktintegration($output);
				//Ausgabe erstellen
			}

			if (strstr( $output,"#shop_sub_kat")) {
				//Ausgabe erstellen
				$output=$this->create_kat_sub_produktintegration($output);
			}

			//Zuerst check ob es auch vorkommt
			if (strstr( $output,"#shop+prodfeld")) {
				//Ausgabe erstellen
				$output=$this->create_search_produktintegration($output);
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
		$this->content->template['plugin_cm_head']['de'][]="Shop Produkte ausgeben";

		//Dann den Beschreibungstext
		$this->content->template['plugin_cm_body']['de'][]="nobr:" . "Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten bestimmte Produkte ausgeben. Diese Möglichkeiten kann man nutzen:<ul>
	  <li><strong>#shop_produkt_ID_LID#</strong> -> ID = ProduktID, LID = Layout ID</li>
		<li><strong>#shop_kat_ANZ_LID#</strong> -> ANZ = Anzahl der Produkte, LID = Layout ID</li>
		<li><strong>#shop_kat_katID_ANZ#</strong> -> katID = ID der Kategorie, ANZ = Anzahl der Produkte - ausgegeben wird der HTML Inhalt</li>
		<li><strong>#shop_sub_kat#</strong> -> Ausgegeben wird der HTML Inhalt
	aller Kategorien die unterhalb der aktuellen liegen.</li>
		<li><strong>#shop_kat_all#</strong> -> katID = ID der Kategorie - ausgegeben wird der HTML Inhalt</li>
		<li><strong>#shop+prodfeld+FELD+FELDWERT+ANZ+LID#</strong> -> FELD = Name des Feldes, VALUE = Wert des Feldes, ANZ = Anzahl, LID = Layout ID (Achten Sie auf die Verwendung der + Zeichen!)</li>
		</ul>";

		//Dann ein Bild, wenn keines vorhanden ist, dann Eintrag trotzdem leer drin belassen
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	private function create_search_produktintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#shop\+prodfeld(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("+", $dat);
			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_search_produkte($ndat['1'],$ndat['2'],$ndat['3'],$ndat['4']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		$inhalt = str_ireplace('</head>', $this->fourcss.'</head>', $inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param $feld
	 * @param $wert
	 * @param int $anz
	 * @param int $lid
	 *
	 * @return string
	 */
	function get_search_produkte($feld,$wert,$anz=0,$lid=0)
	{
		//ZUerst die Produktdaten aus der DB
		$produkt_data=$this->get_produkt_from_db_search($feld,$wert,$anz);

		//Dann das Layout rausholenn
		#$layout = $this->get_layout($lid);

		//Dann Produkt durchgehen
		if (is_array($produkt_data)) {
			foreach ($produkt_data as $key=>$value) {
				//Aus Einzelprodukt Layout generieren
				$data.=$this->create_produkt_layout($value,$lid);
			}
		}
		//Div drumherumg
		$data='<div class="cat_list_dat" >'.$data.'</div><div class="clear"></div>';
		return $data;
	}

	/**
	 * @param $feld
	 * @param $wert
	 * @param $anz
	 *
	 * @return void
	 */
	private function get_produkt_from_db_search($feld,$wert,$anz)
	{
		$sql=sprintf("SELECT * FROM %s, %s, %s
									WHERE
									produkte_lang_id=produkte_produkt_id
									AND produkte_kategorie_id=kategorien_id
									AND produkte_lang_lang_id='%d'
									AND %s = '%s'
									LIMIT %d",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->lang_id,
			$this->db->escape($feld),
			$this->db->escape($wert),
			$this->db->escape($anz)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_single_produktintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#shop_produkt(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);
			//Mit Hilfe der ID einen Eintrag rausholen
			$banner_daten = $this->get_single_produkt($ndat['1'],$ndat['2']);

			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}

		$inhalt = str_ireplace('</head>', $this->fourcss.'</head>', $inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param int $pid
	 * @param int $lid
	 *
	 * @return string
	 */
	function get_single_produkt($pid=0,$lid=0)
	{
		//ZUerst die Produktdaten aus der DB
		$produkt_data=$this->get_produkt_from_db($pid);

		//Dann das Layout rausholenn
		#$layout = $this->get_layout($lid);

		//Dann Produkt durchgehen
		if (is_array($produkt_data)) {
			foreach ($produkt_data as $key=>$value) {
				//Aus Einzelprodukt Layout generieren
				$data.=$this->create_produkt_layout($value,$lid);
			}
		}
		//Div drumherumg
		$data='<div class="single_produkt_liste" >'.$data.'</div>';
		return $data;
	}

	/**
	 * @param mixed $pid
	 *
	 * @return void
	 */
	private function get_produkt_from_db($pid)
	{
		$sql=sprintf("SELECT * FROM %s, %s, %s
									WHERE
									produkte_lang_id=produkte_produkt_id
									AND produkte_kategorie_id=kategorien_id
									AND produkte_lang_lang_id='%d'
									AND produkte_lang_id='%d'
									LIMIT 1",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->lang_id,
			$this->db->escape($pid)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		return $result;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	private function create_kat_produktintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		preg_match_all("|#shop_kat(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			//Die Unterstriche rausholen
			$ndat = explode("_", $dat);

			if ($ndat['1']=="all") {
				//Alle Kategorie Daten rausholen
				$banner_daten = $this->get_kat_all();
			}
			else {

				//Mit Hilfe der ID einen Eintrag rausholen
				$banner_daten = $this->get_kat_produkte($ndat['1'],$ndat['2'],$ndat['3']);
			}
			$inhalt = str_ireplace('#menuid#', $this->checked->menuid, $inhalt);
			//Ersetzung durchf�hren
			$inhalt = str_ireplace($ausgabe['0'][$i], $banner_daten, $inhalt);
			$i++;
		}


		$inhalt = str_ireplace('</head>', $this->fourcss.'</head>', $inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string
	 */
	private function create_kat_sub_produktintegration($inhalt = "")
	{
		//Ids rausholen mit Hilfe eines Regul�ren Ausdrucks
		$banner_daten = $this->get_kat_kat_data($this->checked->menuid);

		$inhalt = str_ireplace('#shop_sub_kat#', $banner_daten, $inhalt);

		//Ge�nderten Inhalt zur�ckgeben
		return $inhalt;
	}

	/**
	 * @param string $menuid
	 *
	 * @return string
	 */
	private function get_kat_kat_data($menuid="")
	{
		$sql=sprintf("SELECT order_id,menuid, url_menuname,untermenuzu, kategorien_lang_TitelderSeite, kategorien_lang_Beschreibung, level,kategorien_id FROM %s
										LEFT JOIN %s ON kategorien_lang_kat_id = kategorien_id
										LEFT JOIN %s ON kategorien_menu_id = menuid
										LEFT JOIN %s ON menuid = menuid_id
										WHERE kategorien_lang_lang_id ='%d'
										AND lang_id ='%d'
										AND untermenuzu='%d'
										ORDER BY  level ASC,order_id  ASC",
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->tbname['plugin_shop_kategorien_lang'],
			$this->cms->tbname['papoo_me_nu'],
			$this->cms->tbname['papoo_menu_language'],
			$this->cms->lang_back_content_id,
			$this->cms->lang_back_content_id,
			$this->db->escape($menuid)
		);
		$menu_array=$this->db->get_results($sql,ARRAY_A);
		$item="";
		if (is_array($menu_array)) {
			foreach ($menu_array as $k=>$v) {
				if (stristr($v['kategorien_lang_Beschreibung'],"img")) {
					preg_match_all("/<img .*?(?=src)src=\"([^\"]+)\"/si", $v['kategorien_lang_Beschreibung'], $m);
					$m['1']['0']=str_ireplace('..','.',$m['1']['0']);
					$item.='<div class="katlisting large-4 medium-4 columns">
						<a href="'.PAPOO_WEB_PFAD.$v['url_menuname'].'">
						<img src="'.$m['1']['0'].'" />
						<span>'.$v['kategorien_lang_TitelderSeite'].'</span>
						</a>
						</div>';
				}
			}
		}
		return $item;
	}

	/**
	 * @return string
	 */
	private function get_kat_all()
	{
		$sql=sprintf("SELECT order_id,menuid, url_menuname,untermenuzu, kategorien_lang_TitelderSeite, kategorien_lang_Beschreibung, level,kategorien_id FROM %s
										LEFT JOIN %s ON kategorien_lang_kat_id = kategorien_id 
										LEFT JOIN %s ON kategorien_menu_id = menuid
										LEFT JOIN %s ON menuid = menuid_id
										WHERE kategorien_lang_lang_id ='%d' 
										AND lang_id ='%d' 
										ORDER BY  level ASC,order_id  ASC",
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->tbname['plugin_shop_kategorien_lang'],
			$this->cms->tbname['papoo_me_nu'],
			$this->cms->tbname['papoo_menu_language'],
			$this->cms->lang_back_content_id,
			$this->cms->lang_back_content_id
		);
		$menu_array=$this->db->get_results($sql,ARRAY_A);
		$item="";
		if (is_array($menu_array)) {
			foreach ($menu_array as $k=>$v) {
				if (stristr($v['kategorien_lang_Beschreibung'],"img")) {
					preg_match_all("/<img .*?(?=src)src=\"([^\"]+)\"/si", $v['kategorien_lang_Beschreibung'], $m);
					$m['1']['0']=str_ireplace('..','.',$m['1']['0']);
					$item.='<div class="katlisting large-4 medium-4 columns">
						<a href="'.PAPOO_WEB_PFAD.$v['url_menuname'].'">
						<img src="'.$m['1']['0'].'" />
						<span>'.$v['kategorien_lang_TitelderSeite'].'</span>
						</a>
						</div>';
				}
			}
		}
		return $item;
	}

	/**
	 * @param $kid
	 * @param $anz
	 * @param $lid
	 *
	 * @return string
	 */
	private function get_kat_produkte($kid,$anz,$lid)
	{
		//ZUerst die Produktdaten aus der DB
		$produkt_data=$this->get_produkt_from_db_fuer_kat($kid,$anz);

		//Dann das Layout rausholenn
		//$layout = $this->get_layout($lid);

		//Dann Produkt durchgehen
		if (is_array($produkt_data)) {
			foreach ($produkt_data as $key=>$value) {
				//Aus Einzelprodukt Layout generieren
				$data.=$this->create_produkt_layout($value,$lid);
			}
		}

		// Div drumherumg
		$data='<div class="cat_list_dat" >'.$data.'</div><div class="clear"></div>';
		return $data;
	}


	/**
	 * @param mixed $kid
	 * @param mixed $anz
	 *
	 * @return array|null
	 */
	private function get_produkt_from_db_fuer_kat($kid,$anz)
	{
		$sql=sprintf("SELECT * FROM %s, %s, %s
									WHERE produkte_lang_id=produkte_produkt_id
									AND produkte_kategorie_id=kategorien_id
									AND produkte_lang_lang_id='%d'
									AND produkte_kategorie_id='%d'
									AND produkte_lang_aktiv=1
									LIMIT %d",
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->lang_id,
			$this->db->escape($kid),
			$this->db->escape($anz)
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		return $result;
	}

	/**
	 * @param $produkt
	 * @param $lid
	 *
	 * @return string
	 */
	private function create_produkt_layout($produkt,$lid)
	{
		$shop_front = new shop_class_frontend();
		//Settings rausholen
		$shop_settings = new shop_class_settings();
		$shop_settings->shop_get_system_settings_fuer_form();

		//Settings setzen
		$this->content->template['shop_system_settings']=$shop_settings->shop_settings_daten;

		//Klasse auf Spezial setzen
		$shop_front->special_layout="OK";

		//Produkt �bergeben
		$result['0']=$produkt;

		//Layout setzen
		$result['0']['produkte_lang_layout_id']=$lid;

		$org_menuid=$this->checked->menuid;

		$this->checked->menuid=$produkt['kategorien_menu_id'];

		//Die Ausgabe Layouts erstellen
		$produkt_liste= $shop_front->shop_create_frontend_produkte_ausgabe_liste($result);

		$this->checked->menuid=$org_menuid;

		//Produkte durchgehen
		if (is_array($produkt_liste)) {
			foreach ($produkt_liste as $key=>$value) {
				$gesamt.=$value['daten'];
			}
		}
		//Zeilenumbr�che
		$gesamt=nl2br($gesamt);

		$gesamt='<div class="single_produkt_liste large-4 medium-4 columns" >'.$gesamt.'</div>';

		//R�ckgabe
		return $gesamt;
	}
}

$cm_shop = new cm_shop();
