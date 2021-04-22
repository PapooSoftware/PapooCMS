<?php

/**
 * Class immo
 */
class immo
{
	/** @var int */
	var $page_size=2;

	/**
	 * immo constructor.
	 */
	function __construct()
	{
		global $content, $user, $checked, $db, $cms, $db_abs, $diverse, $weiter, $menu;
		$this->content = & $content;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->db_abs = &$db_abs;
		$this->diverse = & $diverse;
		$this->weiter = & $weiter;
		$this->menu = & $menu;
		require_once(PAPOO_ABS_PFAD."/plugins/immo/lib/immo_class_special_felder.php");
		$this->immo_special=$immo_special;

		/*
		// Einbindung der Modul-Klasse
		global $module;
		$this->module = & $module;
		*/

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			$this->content->template['immo_self'] = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ( $template != "login.utf8.html") {
				if (!empty($this->checked->saved)) {
					$this->content->template['is_eingetragen']="ok";
				}
				if (!empty($this->checked->del)) {
					$this->content->template['is_del']="ok";
				}

				//Generelle Einstellungen
				if ($template2=="immo_back.html") {
					//Daten sicher und anzeigen
					$this->make_config();

					//Jetzt die Daten checken... develop Methoden...
					$this->check_data();
				}

				if ($template2=="immo_back_objekte.html") {
					//Typen
					$this->get_typen();

					//Speichern Lookup
					if (!empty($this->checked->submit_lookup)) {
						$this->save_lookups();
					}

					//Alle Objekte rausholen
					$this->get_all_objects();
				}

				if ($template2=="immo_back_referenzen.html") {
					//Typen
					$this->get_typen();

					//Speichern Lookup
					if (!empty($this->checked->submit_lookup)) {
						$this->save_lookups("ref");
					}

					//Alle Objekte rausholen
					$this->get_all_objects("ref");
				}

				//Einstellungen für die Ausgabe
				if ($template2=="immo_back2.html") {
					//Erstmal die Typen
					$this->get_typen();

					//Typ ausgewählt, dann Ausgabe definieren
					if (is_numeric($this->checked->immo_typ_id)) {
						//Auf Ausgabe setzen
						$this->content->template['is_immo_typ']=$this->checked->immo_typ_id;

						//Die Ausgabe rausholen
						$this->get_ausgabe();

						//Die Variablen holen
						$this->get_var_data();

						//Daten sicher und anzeigen
						$this->save_ausgabe();
					}
				}
			}
		}
		else {
			//Frontend generieren
			$this->generate_frontend_ausgabe();
		}
	}

	/**
	 * @param string $ref
	 * @return bool|void
	 */
	private function save_lookups($ref="")
	{
		//KEy definieren die gelooped werden sollen
		$dakeys=array("plugin_immo_lookup_toplisting","plugin_immo_lookup_sperren1","plugin_immo_lookup_ref");

		$chdata=json_decode(json_encode($this->checked),true);

		//Daten zuweisen, auch die nullen
		if (is_array($this->checked->objekte)) {
			foreach ($this->checked->objekte as $k1=>$v1) {
				if (empty($chdata[$dakeys['0']][$v1]))$chdata[$dakeys['0']][$v1]=0;
				if (empty($chdata[$dakeys['1']][$v1]))$chdata[$dakeys['1']][$v1]=0;
				if (empty($chdata[$dakeys['2']][$v1]))$chdata[$dakeys['2']][$v1]=0;

				$data[$v1][$dakeys['0']]=$chdata[$dakeys['0']][$v1];
				$data[$v1][$dakeys['1']]=$chdata[$dakeys['1']][$v1];
				$data[$v1][$dakeys['2']]=$chdata[$dakeys['2']][$v1];
			}
		}

		if (!is_array($data)) {
			return false;
		}

		$tbname=DB_PRAEFIX."plugin_immo_lookup";
		if (!empty($ref)) {
			$tbname=DB_PRAEFIX."plugin_immo_lookup_ref";
		}

		foreach ($data as $key=>$value) {
			//Zuerst den alten Eintrag löschen
			$sql=sprintf("DELETE FROM %s WHERE plugin_immo_lookup_id_immoid='%d'",
				$tbname,
				$this->db->escape($key));
			$this->db->query($sql);

			//Dann neu eintragen
			$sql=sprintf("INSERT %s SET plugin_immo_lookup_id_immoid = '%s',
								plugin_immo_lookup_toplisting='%d',
								plugin_immo_lookup_sperren1='%d',
								plugin_immo_lookup_ref='%d'",
				$tbname,
				$this->db->escape($key),
				$this->db->escape($value['plugin_immo_lookup_toplisting']),
				$this->db->escape($value['plugin_immo_lookup_sperren1']),
				$this->db->escape($value['plugin_immo_lookup_ref'])
			);
			$this->db->query($sql);

			if ($value['plugin_immo_lookup_ref']=="1") {
				$this->create_ref_now($key);
			}
		}
	}

	/**
	 * @param $value
	 */
	private function create_ref_now_manuell($value)
	{
		//Zuerste checken ob noch nicht vorhanden
		$sql=sprintf("SELECT * FROM objekte WHERE Kennziffer='%s'",
			$this->db->escape($value['Kennziffer']));
		$ref_data=$this->db->get_results($sql,ARRAY_A);
		$data['realestates.houseBuy']['@id']=rand(1,1000000);
		$data['realestates.houseBuy']['externalId']=$ref_data['0']['Kennziffer'];
		$data['realestates.houseBuy']['title']=utf8_encode($ref_data['0']['Bezeichnung']);
		$data['realestates.houseBuy']['address']['street']=$ref_data['0']['ID'];
		$data['realestates.houseBuy']['address']['postcode']=$ref_data['0']['PLZ'];
		$data['realestates.houseBuy']['address']['city']=utf8_encode($ref_data['0']['Ort']);
		$data['realestates.houseBuy']['creationDate']=$ref_data['0']['Datum'];
		$data['realestates.houseBuy']['refdata']="Homepage ref";
		$att['common.attachments']['0']['attachment']['0']['urls']['0']['url']['0']['@href']='http://www.staudt-firmengruppe.de/images/objekte_'.$ref_data['0']['ID'].'_Objektbild_01_t.jpg';
		$att['common.attachments']['0']['attachment']['0']['urls']['0']['url']['1']['@href']='http://www.staudt-firmengruppe.de/images/objekte_'.$ref_data['0']['ID'].'_Objektbild_01_t.jpg';

		$data_s=json_encode($data);
		#if (!is_numeric($ref))
		{
			#$sql=sprintf("SELECT * FROM %s WHERE plugin_immo_scout_id='%d'",
			#		DB_PRAEFIX."plugin_immo",
			#		$this->db->escape($id));
			#$data=$this->db->get_results($sql,ARRAY_A);


			$xsql['dbname'] = "plugin_immo_referenzen";
			$xsql['praefix'] = "plugin_immo";
			$this->checked->plugin_immo_scout_id=$data['realestates.houseBuy']['@id'];
			$this->checked->plugin_immo_uebersicht=$data_s;
			$this->checked->plugin_immo_einzel_daten=$data_s;
			$this->checked->plugin_immo_timestamp=$data['0']['plugin_immo_timestamp'];
			$this->checked->plugin_immo_einzel_daten_attachement=json_encode($att);
			$cat_dat = $this->db_abs->insert( $xsql);

			//Lookup
			$sql=sprintf("INSERT INTO %s 
							SET plugin_immo_lookup_id_immoid='%d',
							plugin_immo_lookup_ref='1'
							",
				DB_PRAEFIX."plugin_immo_lookup_ref",
				$data['realestates.houseBuy']['@id']);
			$this->db->query($sql);
		}
	}

	/**
	 * @param mixed $id
	 */
	private function create_ref_now($id)
	{
		//Zuerste checken ob noch nicht vorhanden
		$sql=sprintf("SELECT plugin_immo_id FROM %s WHERE plugin_immo_scout_id='%d'",
			DB_PRAEFIX."plugin_immo_referenzen",
			$this->db->escape($id));
		$ref=$this->db->get_var($sql);

		if (!is_numeric($ref)) {
			$sql=sprintf("SELECT * FROM %s WHERE plugin_immo_scout_id='%d'",
				DB_PRAEFIX."plugin_immo",
				$this->db->escape($id));
			$data=$this->db->get_results($sql,ARRAY_A);


			$xsql['dbname'] = "plugin_immo_referenzen";
			$xsql['praefix'] = "plugin_immo";
			$this->checked->plugin_immo_scout_id=$data['0']['plugin_immo_scout_id'];
			$this->checked->plugin_immo_uebersicht=$data['0']['plugin_immo_uebersicht'];
			$this->checked->plugin_immo_einzel_daten=$data['0']['plugin_immo_einzel_daten'];
			$this->checked->plugin_immo_timestamp=$data['0']['plugin_immo_timestamp'];
			$this->checked->plugin_immo_einzel_daten_attachement=$data['0']['plugin_immo_einzel_daten_attachement'];
			$cat_dat = $this->db_abs->insert( $xsql );
		}
	}

	/**
	 * @param mixed $id
	 * @return boolean
	 */
	private function check_schon_eintrag_oid($id)
	{
		$sql=sprintf("SELECT plugin_immo_lookup_id FROM %s WHERE plugin_immo_lookup_id_immoid='%d'",
			DB_PRAEFIX."plugin_immo_lookup",
			$this->db->escape($id)
		);
		$result=$this->db->get_var($sql);
		if (is_numeric($result)) {
			return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	private function get_sort_by()
	{
		//Standard, abwärts
		$this->content->template['sort']="ASC";

		//Wenn Standard, dann Link auf Aufsteigend setzen
		if ($this->checked->sort=="ASC") {
			$this->checked->sort="ASC";
			$this->content->template['sort']="DESC";
		}
		//WEnn aufsteigend, dann auch absteigend
		elseif ($this->checked->sort=="DESC") {
			$this->checked->sort="DESC";
			$this->content->template['sort']="ASC";
		}
		//Var ist anders, dann Standard
		else {
			$this->checked->sort="ASC";
			$this->content->template['sort']="ASC";
		}

		if ($this->checked->st=="sperren") {
			$sortby=' plugin_immo_lookup_sperren1 '.$this->checked->sort;
		}
		if ($this->checked->st=="ref") {
			$sortby=' plugin_immo_lookup_ref '.$this->checked->sort;
		}
		if ($this->checked->st=="isid") {
			$sortby=' plugin_immo_scout_id '.$this->checked->sort;
		}

		if (empty($sortby)) {
			$sortby=' plugin_immo_id ASC';
		}
		return $sortby;
	}

	/**
	 * @return bool|void
	 */
	private function create_from_old_ref()
	{
		return true;
		$sql=sprintf("SELECT Kennziffer FROM objekte ORDER BY Kennziffer DESC");
		$data=$this->db->get_results($sql,ARRAY_A);

		$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."plugin_immo_referenzen");
		$this->db->query($sql);

		$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."plugin_immo_lookup_ref");
		$this->db->query($sql);

		foreach ($data as $key=>$value) {
			$sql=sprintf("SELECT plugin_immo_scout_id
									FROM %s

									WHERE plugin_immo_einzel_daten LIKE '%s'",
				DB_PRAEFIX."plugin_immo",
				"%".$this->db->escape($value['Kennziffer'])."%"
			);
			$result=$this->db->get_var($sql);

			$this->create_ref_now_manuell($value);
			/**
			//So werden die Daten direkt synchronisiert
			if (!empty($result))
			{
			$this->create_ref_now($result);
			}
			else {
			$this->create_ref_now_manuell($value);
			}
			 */
		}
	}

	/**
	 * @param string $ref
	 */
	private function get_all_objects($ref="")
	{
		//Alte Referenzen
		$this->create_from_old_ref();

		$this->content->template['selected_typ']=htmlentities(strip_tags($this->checked->immotyp));
		$this->content->template['search']=htmlentities(strip_tags($this->checked->search));

		//self daten
		$this->content->template['immo_self']="./plugin.php?menuid=".$this->checked->menuid."&template=immo/templates/immo_back_objekte.html&immotyp=".$this->checked->immotyp."";
		$this->content->template['immo_self'].="&search".$this->checked->search."&page=".$this->checked->page."&submit_search=send";

		//Sortierung einrichten
		$sort_by=$this->get_sort_by();

		//Tabelle bestimmen
		if (empty($ref)) {
			$tabname=$this->cms->tbname['plugin_immo'];
			$tabname_join=$this->cms->tbname['plugin_immo_lookup'];
		}
		else {
			$tabname=$this->cms->tbname['plugin_immo_referenzen'];
			$tabname_join=$this->cms->tbname['plugin_immo_lookup_ref'];
		}

		//Zuerst mal die Anzahl
		$sql=sprintf("SELECT COUNT(plugin_immo_id)
							FROM %s
							WHERE plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
							 ",
			$tabname,
			"%".$this->db->escape($this->checked->immotyp)."%",
			"%Homepage%",
			"%".$this->db->escape($this->checked->search)."%",
			"%".$this->db->escape($this->checked->region)."%"
		);

		$anzahl=$this->db->get_var($sql);
		//An weiter Klasse übergeben
		$this->weiter->make_limit($this->get_paginating());
		$this->weiter->result_anzahl = $anzahl;
		$this->weiter->weiter_link="./plugin.php?menuid=".$this->checked->menuid."&template=immo/templates/immo_back_objekte.html&immotyp=".$this->checked->immotyp;
		$what = "search";

		// Links zu weiteren Seiten anzeigen
		$this->weiter->do_weiter($what);
		//Daten nach LIMIT anzeigen plugin_immo_einzel_daten,
		$sql=sprintf("SELECT plugin_immo_id AS id,
									plugin_immo_einzel_daten,
									plugin_immo_einzel_daten_attachement,
									plugin_immo_lookup_toplisting,
									plugin_immo_lookup_sperren1,
									plugin_immo_lookup_ref
									FROM %s
									LEFT JOIN %s ON plugin_immo_lookup_id_immoid=plugin_immo_scout_id
									WHERE plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									  GROUP BY plugin_immo_scout_id
									ORDER BY %s
									%s",
			$tabname,
			$tabname_join,
			"%".$this->db->escape($this->checked->immotyp)."%",
			"%Homepage%",
			"%".$this->db->escape($this->checked->search)."%",
			"%".$this->db->escape($this->checked->region)."%",
			$sort_by,
			$this->weiter->sqllimit
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['immoobjektes']=$this->get_ausgabe_admin($result);
	}

	/**
	 * Geo Daten eines Datensatzes setzen
	 *
	 * @param mixed $data
	 * @return bool|void
	 */
	private function set_geo_data($data)
	{
		$string=json_encode($data);
		if (!stristr($string,"Homepage")) {
			return false;
		}
		//Ortsname
		$ort=$data['address']['city'];

		//Schon vorhanden?
		$sql=sprintf("SELECT plugin_immotyp_region FROM %s WHERE plugin_immotyp_region='%s'",
			DB_PRAEFIX."plugin_immo_region",
			$this->db->escape($ort)
		);
		$ok=$this->db->get_var($sql);

		//Noch nicht vorhanden, also rein damit
		if (empty($ok)) {
			$sql=sprintf("INSERT INTO %s SET  plugin_immotyp_region='%s'",
				DB_PRAEFIX."plugin_immo_region",
				$this->db->escape($ort)
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param void $result
	 * @return array
	 */
	private function get_ausgabe_admin($result)
	{
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//Dekodieren in Array - Basisdaten
				$sdata		= json_decode($value['plugin_immo_einzel_daten'],true);
				$this->set_geo_data(current($sdata));

				//Die Typ ID für die Vorlagen Auswahl
				$typ_id=$this->get_immo_typ_id($sdata);

				//Hier die Bilder
				$sdata_a	= json_decode($value['plugin_immo_einzel_daten_attachement'],true);

				//Erstmal checken ob vorhanden und Bildnamen zurückgeben
				$images 	= $this->get_images_data($sdata_a);

				//Vorlage übergeben
				#$vlx=$this->get_single_vorlage($vorlagen,$typ_id);

				//Eintrag durchgehen
				if (is_array($sdata)) {
					$data=$this->flatten(current($sdata));
					$data['id']=$data['@id'];
					$data['title']=str_ireplace("=","",$data['title']);
					$data['plugin_immo_lookup_toplisting']=$value['plugin_immo_lookup_toplisting'];
					$data['plugin_immo_lookup_sperren1']=$value['plugin_immo_lookup_sperren1'];
					$data['plugin_immo_lookup_ref']=$value['plugin_immo_lookup_ref'];
				}
				$vl[]=$data;

				#$vl[]=str_ireplace("#".$key."#",$value,$vl);
				//$vl[]=str_ireplace("#id#",$value['id'],$vlx);
			}
		}
		return $vl;
	}

	/**
	 * immo::generate_frontend_ausgabe()
	 *
	 * @return bool|void
	 */
	private function generate_frontend_ausgabe()
	{
		//Daten nach Query rausholen
		if ($this->checked->search=="Suchbegriff")
			$this->checked->search="";
		//Suchmaske einbinden aber nur bei Keiner Kat oder Suche ausgwählt...
		if (empty($this->checked->immotyp) || (!empty($this->checked->immotyp) && !empty($this->checked->search_button))) {
			$this->content->template['is_search']="ok";
		}
		$this->content->template['search_immo']="nodecode:".strip_tags(($this->checked->search_immo));
		//Wenn keine Immoseite, dann raus.
		if (!stristr($this->checked->template,"immo")) {
			return false;
		}

		if(stristr($_SERVER['REQUEST_URI'],"xref-")) {
			$this->checked->immotyp="ref";
		}

		//referenzen anzeigen, dann DB setzen
		if ($this->checked->immotyp!="ref") {
			$tabname=$this->cms->tbname['plugin_immo'];
			$tabname_join=$this->cms->tbname['plugin_immo_lookup'];
		}
		//Standard DB
		else {
			$tabname=$this->cms->tbname['plugin_immo_referenzen'];
			$tabname_join=$this->cms->tbname['plugin_immo_lookup_ref'];
		}

		//surl daten rausholen - ist ID gesetzt?
		$this->objekt_id=$this->get_surl_daten_immo();

		if (is_numeric($this->objekt_id)) {
			//Daten des Objekts rausholen
			$sql=sprintf("SELECT plugin_immo_id AS id,
			plugin_immo_einzel_daten,
									plugin_immo_einzel_daten_attachement
									FROM %s
									LEFT JOIN %s ON plugin_immo_lookup_id_immoid=plugin_immo_scout_id
									WHERE plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_lookup_sperren1 <> '1'
									  GROUP BY plugin_immo_scout_id
									%s",
				$tabname,
				$tabname_join,
				"%".$this->db->escape($this->objekt_id)."%",
				"%Homepage%",
				$this->weiter->sqllimit
			);
		}
		else {
			//Zuerst mal die Anzahl
			$sql=sprintf("SELECT COUNT(DISTINCT(plugin_immo_id))
							FROM %s
							LEFT JOIN %s ON plugin_immo_lookup_id_immoid=plugin_immo_scout_id
							WHERE plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
							AND plugin_immo_einzel_daten LIKE '%s'
					        AND plugin_immo_lookup_sperren1 <> '1'

					        ",
				$tabname,
				$tabname_join,
				"%".$this->db->escape($this->checked->immotyp)."%",
				"%Homepage%",
				"%".$this->db->escape($this->checked->region)."%",
				"%".$this->db->escape($this->checked->search_immo)."%"
			);

			$anzahl=$this->db->get_var($sql);

			//An weiter Klasse übergeben
			$this->weiter->make_limit($this->get_paginating());
			$this->weiter->result_anzahl = $anzahl;
			$this->weiter->weiter_link="";
			$what = "teaser";

			if (!empty($this->checked->search_immo)) {
				$this->weiter->weiter_link=PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&search_immo=".urlencode($this->checked->search_immo);
				$what = "search";
			}

			// Links zu weiteren Seiten anzeigen
			$this->weiter->do_weiter($what);

			//Daten nach LIMIT anzeigen plugin_immo_einzel_daten,
			$sql=sprintf("SELECT plugin_immo_id AS id,
			plugin_immo_einzel_daten,
									plugin_immo_einzel_daten_attachement
									FROM %s
									LEFT JOIN %s ON plugin_immo_lookup_id_immoid=plugin_immo_scout_id
									WHERE plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									 AND plugin_immo_lookup_sperren1 <> '1'
									  GROUP BY plugin_immo_scout_id
									%s",
				$tabname,
				$tabname_join,
				"%".$this->db->escape($this->checked->immotyp)."%",
				"%Homepage%",
				"%".$this->db->escape($this->checked->region)."%",
				"%".$this->db->escape($this->checked->search_immo)."%",
				$this->weiter->sqllimit
			);
		}
		$result=$this->db->get_results($sql,ARRAY_A);

		//Ausgabe generieren
		//$ausgabe=$this->create_ausgabe($result);
		if (!is_numeric($this->objekt_id)) {
			$ausgabe='<div class="grid_9 drei-spalten">
				<h4 class="h4-border p2 big">Ausgesuchte  <span>Immobilien</span></h4>
				<div class="data-1-block">
				<div class="data-1">
				<ul>'.$this->create_ausgabe($result).'</ul>
						</div>
						</div>
						</div>';
		}
		else {
			$ausgabe='<div class="grid_9 drei-spalten">
								<div class="data-1-block">
				<div class="data-1">
				<ul>'.$this->create_ausgabe($result).'</ul>
						</div>
						</div>
						</div>';
		}
		//Ans Template
		$this->content->template['ausgabe_immo']="nobr:".$ausgabe;
	}

	/**
	 * @return string|void
	 */
	private function get_surl_daten_immo()
	{
		//Es ist eine ID in der url
		if (stristr($_SERVER['REQUEST_URI'],"/oid-")) {
			//Dann die ID rausholen
			$explode1=explode("/oid-",$_SERVER['REQUEST_URI']);
			$explode2=explode("-",$explode1['1']);

			//Und zurückgeben...
			return trim($explode2['0']);
		}
	}

	/**
	 * @param array $immodata
	 * @param bool $only_id
	 * @return NULL
	 */
	private function get_immo_typ_id($immodata=array(),$only_id=false)
	{
		$typ_array=(array_keys($immodata));
		$typ_name=$typ_array['0'];

		//referenzen
		if ($this->checked->immotyp=="ref" && !$only_id) {
			$typ_name="ref";
		}

		//Typ ID anhand des Typ Namens rausholen
		$sql=sprintf("SELECT plugin_immotyp_id FROM %s
						WHERE plugin_immotyp_code='%s'",
			DB_PRAEFIX."plugin_immo_typ",
			$this->db->escape($typ_name));

		//Aufruf
		$typ_id=$this->db->get_var($sql);

		//Rückgabe
		return $typ_id;
	}

	/**
	 * @return mixed <NULL, multitype:, unknown>
	 */
	private function get_vorlagen_html()
	{
		$this->objekt_id;

		//Alle Vorlagen auf einmal
		$sql=sprintf("SELECT * FROM %s
						",
			$this->cms->tbname['plugin_immo_ausgabe']
		);
		$vorlage=$this->db->get_results($sql,ARRAY_A);

		//Durchgehen und nach Typ ID mappen
		foreach ($vorlage as $key=>$value) {
			$vorl[$value['plugin_immo_typ_id']]=$value;
		}

		//rückgabe
		return $vorl;
	}

	/**
	 * Gibt aus dem Vorlagen Array Einzel oder Übersichts Vorlage raus - hängt ab von Objekt ID
	 *
	 * @param mixed $vorlagen
	 * @param mixed $typ_id
	 * @return array
	 */
	private function get_single_vorlage($vorlagen,$typ_id)
	{
		//Debuggin 
		//$typ_id = 1;

		//Einzelansicht
		if (is_numeric($this->objekt_id)) {
			return $vorlagen[$typ_id]['plugin_immo_einzelansicht'];
		}
		//übersicht
		else {
			return $vorlagen[$typ_id]['plugin_immo_uebersicht'];
		}
	}

	/**
	 * @return boolean
	 */
	private function get_menu_punkte_immo()
	{
		if (!is_array($this->menu->data_front_complete)) {
			return false;
		}
		foreach ($this->menu->data_front_complete as $key=>$value) {
			if (stristr($value['menulink'],"plugin:immo")) {
				$typname=array();
				//Type rausholen
				$ta=explode("immotyp=",$value['menulink']);
				$typname[trim($ta['1'])]=trim($ta['1']);

				if (!empty($typname)) {
					$typid=$this->get_immo_typ_id(($typname),true);
					$neu[$typid]=$value;
				}
			}
		}
		return $neu;
	}

	/**
	 * immo::create_ausgabe()
	 *
	 * @param mixed $result
	 * @return string
	 */
	private function create_ausgabe($result)
	{
		//INI
		$vl=array();

		//Die Vorlagen rausholen
		$vorlagen=$this->get_vorlagen_html();

		//Mögliche Menüpunkte rausholen
		$this->menu_punkte=$this->get_menu_punkte_immo();

		//ergebnisse durchgehen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//Dekodieren in Array - Basisdaten
				$sdata		= json_decode($value['plugin_immo_einzel_daten'],true);

				//Die Typ ID für die Vorlagen Auswahl
				$typ_id=$this->get_immo_typ_id($sdata);

				//Hier die Bilder
				$sdata_a	= json_decode($value['plugin_immo_einzel_daten_attachement'],true);

				//Erstmal checken ob vorhanden und Bildnamen zurückgeben
				$images 	= $this->get_images_data($sdata_a);

				//Vorlage übergeben
				$vlx=$this->get_single_vorlage($vorlagen,$typ_id);

				//Eintrag durchgehen
				if (is_array($sdata)) {
					foreach ($sdata as $key2=>$value2) {
						$fertig=$this->get_single_data_ausgabe($value2,$vlx,$images,$typ_id);
					}
				}

				$vl[]=$fertig;

				#$vl[]=str_ireplace("#".$key."#",$value,$vl);
				//$vl[]=str_ireplace("#id#",$value['id'],$vlx);
			}
		}
		$vl_flat="".implode(" ",$vl);

		return $vl_flat;
	}

	/**
	 * @param mixed $img_data
	 * @return boolean
	 *
	 * [common.attachments] => Array
	(
	[0] => Array
	(
	[attachment] => Array
	(
	[0] => Array
	(
	 */
	private function get_images_data($img_data)
	{
		if (!is_array($img_data)) {
			return false;
		}

		foreach ($img_data as $key=>$value) {
			if (empty($value['0'])) {
				return false;
			}

			//Sonderfall nur 1 Bild
			if (!is_array($value['0']['attachment']['0'])) {
				$zw=$value['0']['attachment'];
				unset($value);
				$value['0']['attachment']['0']=$zw;
			}
			foreach ($value['0']['attachment'] as $key1=>$value1) {
				//Grosses Bild
				$img_big=$value1['urls']['0']['url']['3']['@href'];
				$img_big=str_ireplace("540","740",$img_big);

				$lokal_big=PAPOO_ABS_PFAD."/templates_c/".md5($img_big).".jpg";

				//Thumbnail
				$img_thumb=$value1['urls']['0']['url']['4']['@href'];
				$lokal_thumb=PAPOO_ABS_PFAD."/templates_c/thumb_".md5($img_big).".jpg";

				//Kopieren wenn noch nicht vorhanden
				if (!file_exists($lokal_big) && !empty($img_big)) {
					@copy($img_big,$lokal_big);
					@copy($img_thumb,$lokal_thumb);
				}
				$img_return[$value1['title']]=md5($img_big).".jpg";
			}
		}
		return $img_return;
	}

	/**
	 * immo::set_special_array()
	 *
	 * @return array
	 */
	private function set_special_array()
	{
		$specials=array("constructionPhase",
			"buildingType",
			"cellar",
			"handicappedAccessible",
			"condition",
			"interiorQuality",
			"heatingType",
			"buildingEnergyRatingType",
			"energyConsumptionContainsWarmWater",
			"guestToilet",
			"parkingSpaceType",
			"marketingType",
			"priceIntervalType",
			"hasCourtage",
			"common.publishChannels",
			"value",
			"courtage",
			"apartmentType",
			"builtInKitchen",
			"firingType",
			"certificateOfEligibilityNeeded",
			"garden",
			"petsAllowed",
			"lift",
			"balcony",
			"investmentType",
			"gastronomyType",
			"terrace",
			"siteDevelopmentType",
			"recommendedUseTypes.siteRecommendedUseType",
			"shortTermConstructible",
			"storeType",
			"industryType",
			"commercializationType"
		);
		return $specials;
	}

	/**
	 * @param $key
	 * @param $special
	 * @return bool
	 */
	private function in_immo_array($key, $special)
	{
		if (!is_array($special)) {
			return false;
		}
		foreach ($special as $k=>$v) {
			if (stristr($key,$v)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * immo::get_single_data_ausgabe()
	 *
	 * @param mixed $value2
	 * @param $vorlage
	 * @param $images
	 * @param $typ_id
	 * @return bool|mixed|string|string[]|null
	 */
	private function get_single_data_ausgabe($value2,$vorlage,$images,$typ_id)
	{
		/**
		 * Erstmal die Specials definieren
		 */
		//Darf Addresse angezeigt werden
		$show_address=$value2->showAddress;

		//Array mit den Specials
		$specials=$this->set_special_array();

		//Einträge flach machen
		$this->return=array();
		$data=$this->flatten($value2);
		//Kein Array, dann raus
		if (!is_array($value2)) {
			return false;
		}

		//NUmerich, dann für Import des Bilder die ID rausholen aus den alten Referenzen
		if (is_numeric($data['address.street'])) {
			$this->ref_object_id=$data['address.street'];
			unset($data['address.street']);
		}

		//Durchgehen und ersetzen
		foreach ($data as $key=>$value) {
			///spezialfall
			if ($this->in_immo_array($key,$specials)) {
				$value=$this->get_special($value, $key);
			}

			//Nicht anwendbar
			if ($value=="NOT_APPLICABLE" || $value=="NO_INFORMATION") {
				continue;
			}

			//== Zeichen entfernen
			if ($key=="title") {
				$value=str_ireplace("=","",$value);
				$value=str_ireplace('"',"",$value);
			}
			$vorlage=str_ireplace("#".$key."#",$value,$vorlage);
		}

		// = Zeichen entfernen aus Überschriften usw.
		#$vorlage=str_ireplace("=","",$vorlage);

		if (!is_array($images)) {
			$images=array();
		}

		//Bilder hinzufügen
		$image_tag=$this->get_image_tag(current($images),"thumb_");
		$vorlage=str_ireplace("#start_image#",$image_tag,$vorlage);

		//Galeriebilder hinzufügen
		// u.U. die Methode get_galerie_images_tags_galleriffic verwenden
		$image_tags=$this->get_galerie_images_tags($images);
		$vorlage=str_ireplace("#galerie_images#",$image_tags,$vorlage);


		//Nicht belegte entfernen
		$vorlage = preg_replace('/#(.*?)#/', "xxxxxxxx", $vorlage);
		$vorlage = str_ireplace("xxxxxxxx\r\n","",$vorlage);
		$vorlage = str_ireplace("xxxxxxxx","",$vorlage);

		//Link setzen wenn vorhanden
		$link=$this->get_link($value2,$typ_id);

		$vorlage = preg_replace('/\$\$(.*?)\$\$/', '<a href="'.$link.'">'."$1</a>", $vorlage);


		$vorlage=trim($vorlage);

		//Rückgabe fertig
		return $vorlage;
	}

	/**
	 * @param mixed $value2
	 * @param $typid
	 * @return bool|mixed|string|string[]|null
	 */
	private function get_link($value2,$typid)
	{
		if (stristr($_SERVER['REQUEST_URI'],"/oid-")) {
			return false;
		}

		//Objekt ID bestimmen
		$id=$value2['@id'];

		//Basis URI holen
		//$link = preg_replace("/[^a-zA-Z0-9_\-\/]/", "", $_SERVER['REQUEST_URI']);

		if (empty($this->menu_punkte[$typid])) {
			reset($this->menu_punkte);
			$link_array=current($this->menu_punkte);
			$link=$link_array['menuname_free_url'];
		}
		else {
			$link=($this->menu_punkte[$typid]['menuname_free_url']);
		}
		$link=PAPOO_WEB_PFAD."/".$link;
		//title in url umwandeln
		$value2['title']=str_ireplace(" ","-",trim(strtolower($value2['title'])));
		$value2['title']=str_ireplace("--","-",$value2['title']);
		$value2['title']=str_ireplace("ä","ae",$value2['title']);
		$value2['title']=str_ireplace("ö","oe",$value2['title']);
		$value2['title']=str_ireplace("ü","ue",$value2['title']);
		$value2['title']=str_ireplace("ß","ss",$value2['title']);

		$sub_link=preg_replace("/[^a-zA-Z0-9\-]/", "", trim($value2['title']));

		if($sub_link[strlen($sub_link)-1] == '-' ) {
			$length=strlen($sub_link)-1;
			$sub_link=substr($sub_link,0,$length);
		}
		if ($this->checked->immotyp=="ref") {
			$ref_link="-xref";
		}

		$link .= "oid-".$id.$ref_link."-".$sub_link.".html";
		$link=str_ireplace("--","-",$link);
		return $link;
	}


	/**
	 * @param mixed $img
	 * @return boolean
	 */
	private function get_galerie_images_tags($img)
	{
		//Kein Array
		if (!is_array($img)) {
			return false;
		}

		//INI
		$image_tags='<div class="galerie_immo"><ul>';
		$thumb="thumb_";

		//Durchgehen...
		foreach ($img as $key=>$value) {
			//Keine Datei, dann weiter
			if (!file_exists(PAPOO_ABS_PFAD."/templates_c/".$value)) {
				continue;
			}

			//Pfad zur Datei
			$img_src=PAPOO_ABS_PFAD."/templates_c/".$thumb.$value;
			$img_src_web=PAPOO_WEB_PFAD."/templates_c/".$thumb.$value;
			$img_src_web_big=PAPOO_WEB_PFAD."/templates_c/".$value;

			//Daten rausholen
			list($width, $height, $type, $attr) = getimagesize($img_src);

			//Tag erzeugen
			$image_tags.='<li><a href="'.$img_src_web_big.'" title="'.$key.'" rel="lightbox"><img src="'.$img_src_web.'" alt="" title="" width="'.$width.'" height="'.$height.'"/></a></li>';
		}
		$image_tags.='</ul></div>';

		return $image_tags;
	}

	/**
	 * alternative Methode um die Liste an Bildern zu erzeugen
	 *       liefert die Liste an Bildern im Markup, welches für Gallerriffic beötigt wird
	 *       http://jquery.kvijayanand.in/galleriffic/example-2.html
	 *       die Bilder können teilweise vor der Ausgabe noch serverseitig bearbeitet werden
	 *       siehe auch Content-Manipulator-Plugin -> Skript "Galleriffic"
	 *
	 * @param unknown $img
	 * @return boolean
	 */
	private function get_galerie_images_tags_galleriffic($img)
	{
		//Kein Array
		if (!is_array($img)) {
			return false;
		}

		//INI
		$image_tags= '<div id="gallery" class="content">
                    <!-- <div id="controls" class="controls"></div> -->
                    <div class="slideshow-container">
                      <!-- <div id="loading" class="loader"></div> -->
                      <div id="slideshow" class="slideshow"></div>
                    </div>
                    <div id="caption" class="caption-container"></div>
                  </div>
                  
                  <div id="thumbs" class="navigation">
                    <ul class="thumbs noscript">';

		$thumb="thumb_";

		//Durchgehen...
		$pic_no=0;
		foreach ($img as $key=>$value) {
			//Keine Datei, dann weiter
			if (!file_exists(PAPOO_ABS_PFAD."/templates_c/".$value)) {
				continue;
			}

			$pic_no++;

			//Pfad zur Datei
			$abs_src_thumb=PAPOO_ABS_PFAD."/templates_c/".$thumb.$value;
			$rel_src_thumb=PAPOO_WEB_PFAD."/templates_c/".$thumb.$value;
			$abs_src_image=PAPOO_ABS_PFAD."/templates_c/".$value;
			$rel_src_image=PAPOO_WEB_PFAD."/templates_c/".$value;

			/**/
			// Thumbnail bearbeiten - quadratisch croppen und auf 72px Kantenlänge bringen//
			$max_thumb_size = 72;
			$thumbnail = PHPImageWorkshop\ImageWorkshop::initFromPath('templates_c/' . $thumb.$value);

			//Thumbnail quadratisch croppen
			$thumbnail->cropMaximumInPercent(0, 0, "MM");

			//Thumbnail eventuell verkleinern
			if ($thumbnail -> getLargestSideWidth() > $max_thumb_size) {
				$tmp_filename = 'cropped_'.$thumb.$value;
				$rel_src_thumb = PAPOO_WEB_PFAD . '/templates_c/' . $tmp_filename;
				$abs_src_thumb = PAPOO_ABS_PFAD . '/templates_c/' . $tmp_filename;

				//Nur verkleinern, falls nicht schon geschehen
				if (!file_exists(PAPOO_ABS_PFAD . '/templates_c/' . $tmp_filename)) {
					$thumbnail -> resizeByLargestSideInPixel($max_thumb_size, true);
					$thumbnail -> save (PAPOO_ABS_PFAD . '/templates_c', $tmp_filename, false, '#000', 90);
				}
			}
			// Ende Thumbnail bearbeiten
			/**/

			/**
			// Bild bearbeiten
			$max_width = 400;
			$max_height = 300;
			$img = PHPImageWorkshop\ImageWorkshop::initFromPath('templates_c/'.$value);

			//Bild eventuell verkleinern
			if ($img -> getWidth() > $max_width || $img -> getHeight() > $max_height)
			{
			$tmp_filename = 'resized_' . $value;
			$rel_src_image = PAPOO_WEB_PFAD . '/templates_c/' . $tmp_filename;
			$abs_src_image = PAPOO_ABS_PFAD . '/templates_c/' . $tmp_filename;

			//Nur verkleinern, falls nicht schon geschehen
			if (!file_exists(PAPOO_ABS_PFAD . '/templates_c/' . $tmp_filename))
			{
			if ($img->getWidth > $img->getHeight)
			{
			$img -> resizeInPixel($max_width, null, true);
			}
			else
			{
			$img -> resizeInPixel($null, $max_height, true);
			}
			$img -> save (PAPOO_ABS_PFAD . '/templates_c', $tmp_filename, false, '#000', 90);
			}
			}
			/**/

			//Daten rausholen
			list($width, $height, $type, $attr) = getimagesize($abs_src_thumb);

			//Tag erzeugen
			$image_tags.='<li>
                      <a class="thumb" href="'.$rel_src_image.'" title="'.$key.'">
                        <div class="pp-inner"></div>
                        <img src="'.$rel_src_thumb.'" alt="" title="" width="'.$width.'" height="'.$height.'"/>
                      </a>';

			if ($pic_no > 1) {
				$image_tags .= '<a rel="lightbox" class="cBoxElement" href=' . $rel_src_image . '></a>';
			}

			$image_tags .= '<div class="caption">
                        <a class="originalPic" href=' . $rel_src_image . '></a>
                      </div>
                    </li>';

		}
		$image_tags.='</ul></div>';

		return $image_tags;
	}

	/**
	 * @param mixed $img
	 * @param string $thumb
	 * @return bool|string
	 */
	private function get_image_tag($img,$thumb="")
	{
		//Gibts nich, also raus...
		if (!file_exists(PAPOO_ABS_PFAD."/templates_c/".$img) || empty($img)) {
			//Wenn es eine Referenzen ist
			if (is_numeric($this->ref_object_id)) {
				//Org Datei existiert, dann nur kopieren
				if (file_exists(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg")) {
					//copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
					//	.$this->ref_object_id.$thumb.$img);
					copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
						.$this->ref_object_id.$img);
				}
				//Von alter Seite kopieren
				else {
					//VOn Seiten kopieren
					//	."_Objektbild_01.jpg");
					copy("http://www.staudt-firmengruppe.de/images/objekte_".$this->ref_object_id."_Objektbild_01.jpg",PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg");

					//Dann in templates:c
					//copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
					//	.$this->ref_object_id.$thumb.$value);
					copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
						.$this->ref_object_id.$img);
				}
			}

			else {
				return false;
			}
		}
		//Wenn es eine Referenzen ist
		if (is_numeric($this->ref_object_id)) {
			//Org Datei existiert, dann nur kopieren
			if (file_exists(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg")) {
				//copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
				//	.$this->ref_object_id.$thumb.$img);
				@copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
					.$this->ref_object_id.$thumb.$img);
			}
			//Von alter Seite kopieren
			else {
				//VOn Seiten kopieren
				//	."_Objektbild_01.jpg");
				@copy("http://www.staudt-firmengruppe.de/images/objekte_".$this->ref_object_id."_Objektbild_01.jpg",PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg");

				//Dann in templates:c
				//copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
				//	.$this->ref_object_id.$thumb.$value);
				@copy(PAPOO_ABS_PFAD."/images/ref_".$this->ref_object_id.".jpg",PAPOO_ABS_PFAD."/templates_c/"
					.$this->ref_object_id.$thumb.$img);
			}
		}

		//Pfad zur Datei
		$img_src=PAPOO_ABS_PFAD."/templates_c/".$this->ref_object_id.$thumb.$img;
		$img_src_web=PAPOO_WEB_PFAD."/templates_c/".$this->ref_object_id.$thumb.$img;

		//Daten rausholen
		list($width, $height, $type, $attr) = getimagesize($img_src);

		//Tag erzeugen
		$first_image='<img src="'.$img_src_web.'" alt="" title="" width="'.$width.'" height="'.$height.'"/>';

		return $first_image;
	}

	/**
	 * immo::get_special()
	 *
	 * @param $data
	 * @param $key
	 * @return void
	 */
	private function get_special($data,$key)
	{
		//Erstzungen durchführen
		$data=$this->immo_special->get_special($data,$key);

		//Rückgabe
		return $data;
	}

	/**
	 * immo::get_var_data()
	 *
	 * @return bool|void
	 */
	private function get_var_data()
	{
		//Der Typname
		$sql=sprintf("SELECT plugin_immotyp_code FROM %s
					WHERE plugin_immotyp_id='%d'",
			$this->cms->tbname['plugin_immo_typ'],
			$this->db->escape($this->checked->immo_typ_id)
		);
		$result=$this->db->get_var($sql);

		//Dann einen passenden Eintrag
		$sql=sprintf("SELECT plugin_immo_einzel_daten FROM %s
					WHERE plugin_immo_einzel_daten LIKE '%s'",
			$this->cms->tbname['plugin_immo'],
			$this->db->escape("%".$result."%")
		);
		$result=$this->db->get_var($sql);
		$data=json_decode($result,true);
		if (empty($data))
			return false;
		$flat=$this->flatten(current($data));
		$this->content->template['plugin_immo_felder']=$flat;
		/**
		$sql=sprintf("SELECT * FROM %s",
		$this->cms->tbname['plugin_immo_felder']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['plugin_immo_felder']=$result;
		 */
	}

	/**
	 * immo::get_ausgabe()
	 *
	 * @return void
	 */
	private function get_ausgabe()
	{
		$sql=sprintf("SELECT * FROM %s WHERE plugin_immo_typ_id='%d'",
			$this->cms->tbname['plugin_immo_ausgabe'],
			$this->db->escape($this->checked->immo_typ_id)
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (is_array($result['0'])) {
			foreach ($result['0'] as $key=>$value) {
				$result['0'][$key]="nobr:".$value;
			}
		}
		$this->content->template['plugin_immo']=$result;
	}

	/**
	 * immo::save_ausgabe()
	 *
	 * @return void
	 */
	private function save_ausgabe()
	{
		if (!empty($this->checked->formSubmitimmo)) {
			//Zuerst l�schen
			$sql=sprintf("DELETE FROM %s WHERE plugin_immo_typ_id ='%d'",
				DB_PRAEFIX."plugin_immo_ausgabe",
				$this->db->escape($this->checked->immo_typ_id)
			);
			$this->db->query($sql);

			//Dann neu eintragen
			$xsql['dbname'] = "plugin_immo_ausgabe";
			$xsql['praefix'] = "plugin_immo";
			$cat_dat = $this->db_abs->insert( $xsql );
		}
	}

	/**
	 * immo::check_data()
	 *
	 * @return void
	 */
	private function check_data()
	{
		//Typen holen
		$this->get_typen();

		//Felder für die Ausgabe...
		//$this->create_object_vars();

		//Typen bestimmen
		//$this->create_typen();
	}

	/**
	 * immo::create_object_vars()
	 * Alle möglichen Variablen als Vorlagen definieren.
	 *
	 * @return void
	 */
	private function create_object_vars()
	{
		//Ids rausholen zum durchloopen...
		$sql=sprintf("SELECT plugin_immo_id FROM %s ",
			$this->cms->tbname['plugin_immo']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//ALle durchlaufen
		$data1=$data2=array();
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//reset
				$data=array();

				$sql=sprintf("SELECT plugin_immo_einzel_daten FROM %s
									WHERE plugin_immo_id='%d'",
					$this->cms->tbname['plugin_immo'],
					$this->db->escape($value['plugin_immo_id'])
				);
				$single_data=$this->db->get_var($sql);

				//Array erstellen 
				$single_data=json_decode($single_data,true);

				//Array flach machen auf eine Ebene - einfacher zu händeln
				$data=array_flip(array_keys($this->flatten(current($single_data))));

				//Alle zusammenlegen
				$data2=array_merge($data2,$data);
			}
		}

		//Array mit den Specials
		//$specials=$this->set_special_array();
		$data2['start_image']="1";
		$data2['galerie_images']="1";

		//Durchlaufen und eintragen als Ausgabefelder
		if (is_array($data2)) {
			$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."plugin_immo_felder");
			$this->db->query($sql);

			foreach ($data2 as $key=>$value) {
				#if (in_array($value,$specials))
				#{
				#	continue;
				#}
				$xsql['dbname'] = "plugin_immo_felder";
				$xsql['praefix'] = "plugin_immofeld";
				$this->checked->plugin_immofeld_code=$key;
				$cat_dat = $this->db_abs->insert( $xsql );
			}
		}
	}

	/**
	 * @param array $array
	 * @param string $key_outside
	 * @return mixed
	 * private function flatten_old(array $array) {
	 * $return = array();
	 * array_walk_recursive($array, function($a,$b) use (&$return) {
	 * $return[][$b] = $a;
	 * });
	 * return $return;
	 * }
	 */
	private function flatten(array $array,$key_outside="")
	{

		foreach ($array as $key=>$value) {
			if (is_array($value)) {
				if (strlen($key_outside)>0) {
					$key=$key_outside.".".$key;
				}
				$this->flatten($value,$key);
			}
			else {
				if (strlen($key_outside)>0) {
					$this->return[$key_outside.".".$key]=$value;
				}
				else {
					$this->return[$key]=$value;
				}
			}
		}
		return $this->return;
	}

	/**
	 * immo::get_typen()
	 *
	 * @return void
	 */
	private function get_typen()
	{
		$sql=sprintf("SELECT * FROM %s ORDER BY plugin_immotyp_name ASC",
			$this->cms->tbname['plugin_immo_typ']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $this->content->template['immotypen']=$result;
	}

	/**
	 * @return mixed <NULL, multitype:, unknown>
	 */
	private function get_regionen()
	{
		$sql=sprintf("SELECT * FROM %s ORDER BY plugin_immotyp_region ASC",
			$this->cms->tbname['plugin_immo_region']
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $this->content->template['immoregionen']=$result;
	}

	/**
	 * immo::create_typen()
	 *
	 * @return void
	 */
	private function create_typen()
	{
		//IDs rausholen
		$sql=sprintf("SELECT plugin_immo_id FROM %s ",
			$this->cms->tbname['plugin_immo']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//ALle durchlaufen
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$sql=sprintf("SELECT plugin_immo_einzel_daten FROM %s
									WHERE plugin_immo_id='%d'",
					$this->cms->tbname['plugin_immo'],
					$this->db->escape($value['plugin_immo_id'])
				);
				$single_data=$this->db->get_var($sql);
				$single_data=json_decode($single_data);

				if (is_object($single_data)) {
					foreach ($single_data as $key2=>$value2) {
						$typ[$key2]=$key2;
					}
				}
			}
		}

		if (is_array($typ)) {
			$sql=sprintf("TRUNCATE TABLE %s",DB_PRAEFIX."plugin_immo_typ");
			$this->db->query($sql);

			foreach ($typ as $key=>$value) {
				$xsql['dbname'] = "plugin_immo_typ";
				$xsql['praefix'] = "plugin_immotyp";
				$this->checked->plugin_immotyp_code=$value;
				$cat_dat = $this->db_abs->insert( $xsql );
			}
		}
	}

	/**
	 * @return Immocaster_Sdk
	 */
	private function set_new_immocaster()
	{
		/**
		 * Verbindung zum Service von ImmobilienScout24 aufbauen.
		 * Die Daten (Key und Secret) erhält man auf
		 * http://developer.immobilienscout24.de.
		 */
		$sImmobilienScout24Key    = 'Papoo Plugin ImmoApiKey';
		$sImmobilienScout24Secret = 'uRzc9rfWcPuesnUB';
		$oImmocaster              = Immocaster_Sdk::getInstance('is24',$sImmobilienScout24Key,$sImmobilienScout24Secret);

		/**
		 * Verbindung zur MySql-Datenbank (wird für einige Anfragen
		 * an die API benötigt, wie z.B. nur Maklerobjekte anzeigen).
		 */
		$oImmocaster->setDataStorage(array('mysql',EZSQL_DB_HOST,EZSQL_DB_USER,EZSQL_DB_PASSWORD,EZSQL_DB_NAME));

		/**
		 * cURL verwenden
		 */
		$oImmocaster->setReadingType('curl');

		/**
		 * JSON verwenden
		 */
		$oImmocaster->setContentResultType('json');

		/**
		 * Auf Live-System arbeiten.
		 * Für die Arbeit mit Livedaten, muss man von
		 * ImmobilienScout24 extra freigeschaltet werden.
		 * Standardmäßig wird auf der Sandbox gearbeitet!
		 */
		$oImmocaster->setRequestUrl('live');

		return $oImmocaster;

	}

	/**
	 * immo::make_domains()
	 *
	 * @return bool|void
	 */
	private function make_config()
	{
		//snyced
		if ($this->checked->ready=="ok") {
			$this->content->template['immo_sync_ok']="OK";
		}

		require_once(PAPOO_ABS_PFAD.'/plugins/immo/lib/Immocaster/Sdk.php');

		//Immocaster Holen
		$oImmocaster=$this->set_new_immocaster();

		/**
		 * Zertifizierung anstossen...
		 * */
		if(isset($_GET['main_registration'])||isset($_GET['state'])) {
			$link="http://".$this->cms->title.PAPOO_WEB_PFAD."/interna/".$this->content->template['immo_self'];

			$aParameter = array('callback_url'=>$link,'verifyApplication'=>true);
			if($oImmocaster->getAccess($aParameter)) {
				//Zertifizierung war erfolgreich
				$this->content->template['immo_zert']="OK";
			}
		}

		//User id speichern
		$this->userid_immo=$this->get_userid_immo();
		/**
		 * F�r den laufenden Betrieb checken ob aktiv...
		 * */
		$data=$oImmocaster->getApplicationTokenAndSecret();
		if (!empty($data['0']->secret) && !empty($this->userid_immo)) {
			$this->content->template['immo_zert']="OK";
		}
		/**
		 * Synchronisierung der DB mit ImmoScout24 starten - alle Eintr�ge des Users rausholen und
		 * in die Datenbank eintragen inkl. Attachments und Einzeldaten
		 *
		 * */
		if (!empty($this->checked->sync_now)) {
			if (!is_numeric($this->checked->pageNumber)) {
				//Schritt 1 - alle alten Einträge löschen
				$this->delete_entry();

				//Zuerst mal alle holen...
				$res = $oImmocaster->fullUserSearch(array("pagesize"=>$this->page_size,"pageNumber"=>"1"));

				$res = json_decode($res);

				//Objektname setzen wg. dem Punkt im Namen
				$realestates='realestates.realEstates';
				$hits=$res->$realestates->Paging->numberOfHits;

				//Daten der ersten Seite eintragen
				$this->insert_immo_db($res,$realestates);

				//Seitenzahl ausrechnen
				$pages=round((($hits/$this->page_size)+1),0);

				$this->reload_scrape("plugin.php?menuid=".$this->checked->menuid."&template=immo/templates/immo_back.html&sync_now=ok&page_size=".$this->page_size."&pageNumber=1&pages=".$pages,1,$pages);

			}
			else {
				//set_time_limit(1);
				$pages=$this->checked->pages;
				$this->page_size=$this->checked->page_size;

				//Seiten durchloopen, Daten rausholen und speichern

				//Schon neu geladen, dann i hochsetzen
				if (is_numeric($this->checked->pageNumber)) {
					$i=$this->checked->pageNumber+1;
				}
				if ($i>$pages) {
					debug::print_d("FERTIG");
					$this->reload("immo/templates/immo_back.html&ready=ok")	;
				}
				//Hier neu laden
				$res = $oImmocaster->fullUserSearch(array("pagesize"=>$this->page_size,"pageNumber"=>$i));
				$res = json_decode($res);

				//Objektname setzen wg. dem Punkt im Namen
				$realestates='realestates.realEstates';
				$hits=$res->$realestates->Paging->numberOfHits;

				$this->insert_immo_db($res,$realestates);

				$this->reload_scrape("plugin.php?menuid=".$this->checked->menuid."&template=immo/templates/immo_back.html&sync_now=ok&page_size=".$this->page_size."&pageNumber=".$i."&pages=".$pages,$i,$pages);
				exit();

			}
			return true;
		}
	}

	/**
	 * @return NULL|boolean
	 */
	private function get_userid_immo()
	{
		if (!empty($this->checked->submit_user_immo)) {

			$xsql['dbname'] = "plugin_immo_config";
			$xsql['praefix'] = "plugin_immo";
			$xsql['where_name'] = "plugin_immo_id";
			$this->checked->plugin_immo_id=1;
			$cat_dat = $this->db_abs->update( $xsql );
		}

		$sql=sprintf("SELECT plugin_immo_userid  FROM %s",DB_PRAEFIX."plugin_immo_config");
		$userid=$this->db->get_var($sql);
		if (strlen($userid)>1) {
			return $userid;
		}
		return false;
	}

	/**
	 * immo::insert_immo_db()
	 * Daten der Einzelobjekte in die DB eintragen
	 *
	 * @param mixed $res
	 * @param $realestates
	 * @return bool
	 * anja.miller@lhmtk.de
	 */
	private function insert_immo_db($res,$realestates)
	{
		$i=0;
		if (is_object($res)) {
			//Alle Objekte der uebrsicht durchgehen
			foreach ($res->$realestates->realEstateList->realEstateElement as $key=>$value) {
				//Id des Eintrages rausholen
				$id_name="@id";
				$id=$value->$id_name;

				//Jetzt die Daten dieses Objektes holen
				$objekt=$this->get_object_data($id);
				//Dann speichern
				$xsql['dbname'] = "plugin_immo";
				$xsql['praefix'] = "plugin_immo";
				$this->checked->plugin_immo_scout_id=$id;
				$this->checked->plugin_immo_uebersicht=json_encode($value);
				$this->checked->plugin_immo_einzel_daten=$this->objekt_data;
				$this->checked->plugin_immo_timestamp=$this->timestamp;
				$this->checked->plugin_immo_einzel_daten_attachement=$this->objekt_data_attachement;
				$cat_dat = $this->db_abs->insert( $xsql );

				$sql=sprintf("INSERT %s SET plugin_immo_lookup_id_immoid = '%s',
								plugin_immo_lookup_toplisting='0',
								plugin_immo_lookup_sperren1='0',
								plugin_immo_lookup_ref='0'",
					DB_PRAEFIX."plugin_immo_lookup",
					$this->db->escape($id)
				);
				$this->db->query($sql);
			}
		}
		//zurueck
		return true;
	}

	/**
	 * immo::get_object_data()
	 * Daten eines Objektes rausholen
	 *
	 * @param mixed $id
	 * @return bool
	 */
	private function get_object_data($id)
	{
		if (empty($id)) {
			debug::print_d("KEINE ID");
			return false;
		}
		//Immocaster Holen
		$oImmocaster=$this->set_new_immocaster();

		//Daten des Objektes rausholen
		$aParameter = array('username'=>'me','exposeid'=>$id); // Expose-ID hinterlegen
		$data        = $oImmocaster->getUserExpose($aParameter);

		//Datum Zuweisen damit es in die DB eingetragen werden kann
		$this->objekt_data=$data;
		$data = json_decode($data,true);
		$dx=current($data);
		$this->timestamp=(strtotime($dx['lastModificationDate']));
		if ($this->timestamp<1000)
		{
			$this->timestamp=(strtotime($dx['creationDate']));
		}

		//Städte und Regionen übernehmen
		$this->set_geo_data(current($data));
		//Attachments holen wenn welche da sind, ansonsten gibt es einen error
		##$aParameter = array('exposeid'=>$id); // Expose-ID hinterlegen
		#$data_attachement        = $oImmocaster->getAttachment(array("exposeid"=>$id));
		$data_attachement=$oImmocaster->getExpose_images(array("user"=>$this->userid_immo,"realestate"=>$id));
		$this->objekt_data_attachement=$data_attachement;

		//Jetzt die Bilder runterladen und einbinden
		$this->get_images_data(json_decode($data_attachement,true));

		if (stristr($data_attachement,"ERROR_COMMON_ACCESS_DENIED")) {
			debug::print_d("KEIN Zugriff auf die EInzeldaten bei Immoscout - bitte dort freischalten lassen für diesen Account.
					Fehler: ERROR_COMMON_ACCESS_DENIED");
		}
		//$data_attachement = json_decode($data_attachement);
		return true;
	}



	/**
	 * immo::delete_entry()
	 * ALLE Immoeintraege loeschen - Truncate Table - > Reset um altes Zeugs zu loeschen...
	 *
	 * @return void
	 */
	private function delete_entry()
	{
		//Einträge löschen
		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_immo']
		);
		$this->db->query($sql);

		//Regionen löschen
		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_immo_region']
		);

		//Lookups löschen plugin_immo_lookup
		$sql=sprintf("TRUNCATE TABLE %s",
			$this->cms->tbname['plugin_immo_lookup']
		);

		$this->db->query($sql);
	}

	/**
	 * immo::output_filter()
	 *
	 * @return void
	 */
	public function output_filter()
	{
		global $output;

		//Felder für Suche füllen
		$typen_options=$this->create_search_felder("typen");
		$regio_options=$this->create_search_felder("regionen");

		preg_match_all("|#immo_top(.*?)#|", $output, $ausgabe, PREG_PATTERN_ORDER);

		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			$banner_daten = $this->get_data_from_immo($ndat['1']);
			$output = str_ireplace($ausgabe['0'][$i], $banner_daten, $output);
			$i++;
		}
		$output = "" . $output;

		$output = str_ireplace("#select_typ#", $typen_options, $output);
		$output = str_ireplace("#regionen#", $regio_options, $output);
	}

	/**
	 * @param string $typ
	 * @return bool|string
	 */
	private function create_search_felder($typ="")
	{
		//Typen rausholen
		if ($typ=="typen") {
			$Daten=$this->get_typen();
		}

		//Regionen rausholen
		if ($typ=="regionen") {
			$Daten=$this->get_regionen();
		}

		//Options füllen
		return $this->create_options($Daten);

	}

	/**
	 * Erzeugt die Optionen für die Formularfelder
	 *
	 * @param array $optionen
	 * @return bool|string
	 */
	private function create_options($optionen=array())
	{
		if (!is_array($optionen)) {
			return false;
		}

		$options='';

		foreach ($optionen as $key=>$value) {
			$selected="";

			//Region
			if (!empty($value['plugin_immotyp_region'])) {
				if ($this->checked->region==$value['plugin_immotyp_region']) {
					$selected=' selected="selected" ';
				}
				$options.='<option '.$selected.' value="'.$value['plugin_immotyp_region'].'">'.$value['plugin_immotyp_region'].'</option>';
			}

			//Typen
			if (!empty($value['plugin_immotyp_code'])) {
				if ($this->checked->immotyp==$value['plugin_immotyp_code']) {
					$selected=' selected="selected" ';
				}
				$options.='<option '.$selected.' value="'.$value['plugin_immotyp_code'].'">'.$value['plugin_immotyp_name'].'</option>';
			}
		}
		return $options;
	}

	/**
	 * @param int $count
	 * @return string
	 */
	private function get_data_from_immo($count=0)
	{
		//Daten nach LIMIT anzeigen plugin_immo_einzel_daten,
		$sql=sprintf("SELECT plugin_immo_id AS id,
			plugin_immo_einzel_daten,
									plugin_immo_einzel_daten_attachement
									FROM %s
									LEFT JOIN %s ON plugin_immo_lookup_id_immoid=plugin_immo_scout_id
									WHERE plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_einzel_daten LIKE '%s'
									AND plugin_immo_lookup_sperren1 <> '1'
									GROUP BY plugin_immo_scout_id 
									ORDER BY plugin_immo_timestamp DESC
									%s",
			$this->cms->tbname['plugin_immo'],
			DB_PRAEFIX."plugin_immo_lookup",
			"%".$this->db->escape($this->checked->immotyp)."%",
			"%Homepage%",
			"LIMIT 0,".(int) $count
		);

		$result=$this->db->get_results($sql,ARRAY_A);
		#$this->checked->immotyp

		//$this->special_vorlage=file_get_contents(PAPOO_ABS_PFAD."/plugins/immo/templates/immo_do_front_special1.html");

		//Ausgabe generieren
		$ausgabe='<div class="grid_12 top alpha omega vier-spalten-carousel">
				<h4 class="h4-border p2">Aktuelle <span>Immobilien</span></h4>
				<div class="carousel-1-block">
				<div class="carousel-1">
				<ul>'.$this->create_ausgabe($result).'</ul>
						</div>
						<a href="#" class="carousel-prev carousel-1-button" data-type="prevPage"><span></span></a> 
						<a href="#" class="carousel-next carousel-1-button" data-type="nextPage"><span></span></a>
						</div>
						</div>';

		return $ausgabe;
		//Ans Template
		#$this->content->template['ausgabe_immo']=$ausgabe;
	}

	/**
	 * @param string $location_url
	 * @param int $aktu_count
	 * @param int $max_count
	 */
	function reload_scrape($location_url="",$aktu_count=0,$max_count=0)
	{
		header("Location: $location_url");
		exit();
		header('REFRESH: 0; URL='.$location_url);
		echo "Einspielen l&auml;uft. Bitte haben Sie einen Moment Geduld<br /><br />";
		echo $aktu_count . " von " . $max_count . " Datens&auml;tzen sind zur&uuml;ckgespielt.<br />\n<br />\n";
		echo '<a href="' . $location_url . '">Weiter</a><br />';
		exit;
	}

	/**
	 * immo::reload()
	 *
	 * @param string $template
	 * @param string $id
	 * @param string $delete
	 * @return void
	 */
	function reload($template="",$id="",$delete="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$template;
		if (!empty($delete)) {
			$location_url.="&del=ok";
		}
		if (is_numeric($id)) {
			$location_url.="&saved=ok&domain_id=".$id;
		}

		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * Holt das Paginating aus der Datenbank
	 *
	 * immo::get_paginating()
	 *
	 * @return array|null
	 */
	private function get_paginating()
	{
		$sql = 'SELECT config_paginierung FROM ' . DB_PRAEFIX . 'papoo_config';
		$paginating = $this->db->get_var($sql);
		return $paginating;
	}
}

$immo = new immo();
