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
 * Mit dieser Klasse können Kategorien erstellt, bearbeitet und verschoben werden
 * Diese Kategorien können dazu verwendet werden um Inhalte einzusortieren
 * unabhängig von Menüpunkten
 *
 * Außerdem können Menüpunkte diesen Kategorien zugeordnet werden.
 *
 */

#[AllowDynamicProperties]
class cat_class
{
	/**
	 * cat_class constructor.
	 */
	function __construct()
	{

	}

	/**
	 *
	 */
	function do_cat()
	{
		// Papoo-Klassen globalisieren
		global $db;
		$this->db = & $db;

		global $checked;
		$this->checked = & $checked;

		global $content;
		$this->content = & $content;

		global $cms;
		$this->cms = & $cms;


		// klassen-interne Variablen initialisieren
		global $db_praefix;
		$this->db_praefix = $db_praefix;


		if (defined("admin")) {
			// Überprüfen ob Zugriff auf die Inhalte besteht
			global $user;
			$user->check_access();
		}

		switch ($this->checked->menuid) {
		case "66" :
			//Test auf Start setzen
			$this->content->template['kat_start']="ok";
			break;

		case "67" :
			//Neue Kategorie erstellen
			$this->content->template['kat_edit']="ok";
			$this->cat_make_new();
			break;

		case "68" :
			//Kategorie bearbeiten
			$this->content->template['kat_change']="ok";
			$this->cat_change();
			break;

		case "69" :
			// Reihenfolge bearbeiten
			$this->switch_order();
			break;
		}

		IfNotSetNull($this->content->template['fragloesch']);
		IfNotSetNull($this->content->template['katsort']);
		IfNotSetNull($this->content->template['tcat_text']);
		IfNotSetNull($this->content->template['tcat_text']);
		IfNotSetNull($this->content->template['kat_alt']);
		IfNotSetNull($this->content->template['fragloesch']);
		IfNotSetNull($this->content->template['kat_start']);
		IfNotSetNull($this->content->template['kat_edit']);
		IfNotSetNull($this->content->template['kat_change']);
		IfNotSetNull($this->content->template['catdatlang']);
		IfNotSetNull($this->content->template['xlink']);
		IfNotSetNull($this->content->template['kat_one']);
	}

	/**
	 * Neue Kategorie erstellen
	 */
	function cat_make_new()
	{
		if (!empty($this->checked->drin)) {
			$this->content->template['tcat_text']="drin";
		}
		//Neue Kategorie eintragen
		if (!empty($this->checked->strSubmit)) {
			$error=0;
			foreach ($this->checked->cat_text as $key=>$value) {
				if (empty($value))$error=1;
			}
			if ($error!=1) {
				$sql=sprintf("SELECT COUNT(cat_order_id) FROM %s",
					$this->cms->tbname['papoo_category']
				);
				$count=$this->db->get_var($sql);
				//Daten eintragen
				$sql=sprintf("INSERT INTO %s SET cat_order_id='%s', cat_startseite_anzahl='%d', cat_startseite_ok='%d'",
					$this->cms->tbname['papoo_category'],
					$count,
					$this->db->escape($this->checked->cat_startseite_anzahl),
					$this->db->escape($this->checked->cat_startseite_ok)

				);
				$this->db->query($sql);
				$insertid=$this->db->insert_id;
				//Sprachdaten durchgehen
				foreach ($this->checked->cat_text as $key=>$value) {
					//Wenn nicht leer eintragen und auf id vergeben
					$sql=sprintf("INSERT INTO %s SET
								  		cat_lang_id='%s',
								  		cat_lang_lang='%s',
								  		cat_text='%s',
								  		cat_text_intern='%s',
								  		cat_descrip='%s'
								  		",
						$this->cms->tbname['papoo_category_lang'],
						$insertid,
						$this->db->escape($key),
						$this->db->escape($value),
						$this->db->escape($this->checked->cat_text_intern[$key]),
						"")
					;
					$this->db->query($sql);
				}
				// WEnn id gesetzt dann:
				if (!empty($insertid)) {
					//Schreibrechte eintragen
					if (is_array($this->checked->cat_write_ar)) {
						foreach ($this->checked->cat_write_ar as $write) {
							$sql=sprintf("INSERT INTO %s SET cat_wlid='%s', cat_wlgruppe='%s'",
								$this->cms->tbname['papoo_category_lookup_write'],
								$insertid,
								$this->db->escape($write)
							);
							$this->db->query($sql);
						}
					}
					//Leserechte eintragen
					if (is_array($this->checked->cat_read_ar)) {
						foreach ($this->checked->cat_read_ar as $read) {
							$sql=sprintf("INSERT INTO %s SET cat_rlid='%s', cat_rlgruppe='%s'",
								$this->cms->tbname['papoo_category_lookup_read'],
								$insertid,
								$this->db->escape($read)
							);
							$this->db->query($sql);
						}
					}
					$this->reorder();
					// 5. Weiterleitung für neuen Seitenaufbau
					$location_url = "./kategorie.php?menuid=67&drin=1";
					if ($_SESSION['debug_stopallredirect']) {
						echo '<a href="'.$location_url.'">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit();
				}
			}
			else {
				$this->content->template['tcat_text']="error1";
				$this->content->template['kat_neu']="ok";
				//Gruppen setzen
				$this->cat_hol_gruppen_liste();
				//Sprachen setzen
				$this->cat_preset_sprachen();
			}

		}
		else {
			$this->content->template['kat_neu']="ok";
			//Gruppen setzen
			$this->cat_hol_gruppen_liste();
			//Sprachen setzen
			$this->cat_preset_sprachen();
		}
	}
	/**
	 * Kategorie löschen
	 */
	function cat_del()
	{
		if (!empty($this->checked->realloeschen) && $this->checked->cat_id>1) {
			//löschen papoo_category
			$sql=sprintf("DELETE FROM %s WHERE cat_id='%s'",
				$this->cms->tbname['papoo_category'],
				$this->db->escape($this->checked->cat_id)
			);
			$this->db->query($sql);

			//löschen papoo_category_lang
			$sql=sprintf("DELETE FROM %s WHERE cat_lang_id='%s'",
				$this->cms->tbname['papoo_category_lang'],
				$this->db->escape($this->checked->cat_lang_id)
			);
			$this->db->query($sql);

			//alte Schreibrechte löschen
			$sql=sprintf("DELETE FROM %s WHERE cat_wlid='%s'",
				$this->cms->tbname['papoo_category_lookup_write'],
				$this->db->escape($this->checked->cat_id)
			);
			$this->db->query($sql);

			//alte Leserechte löschen
			$sql=sprintf("DELETE FROM %s WHERE cat_rlid='%s'",
				$this->cms->tbname['papoo_category_lookup_read'],
				$this->db->escape($this->checked->cat_id)
			);
			$this->db->query($sql);

			// 5. Weiterleitung für neuen Seitenaufbau
			$location_url = "./kategorie.php?menuid=68&del=1";
			if ($_SESSION['debug_stopallredirect']) echo '<a href="'.$location_url.'">Weiter</a>';
			else header("Location: $location_url");
			exit ();
		}
		else {
			$this->content->template['fragloesch']="ok";
			$this->content->template['cat_id']=$this->checked->cat_id;
			$this->content->template['fragloesch_name']=$this->checked->cat_text_intern[$this->cms->lang_id];
		}
	}

	/**
	 * Einträge der Kategorien ändern
	 */
	function cat_change()
	{
		if (!empty($this->checked->loeschen) or !empty($this->checked->realloeschen)) {
			$this->cat_del();
		}

		if (!empty($this->checked->drin)) {
			$this->content->template['tcat_text']="drin";
		}

		if (!empty($this->checked->del)) {
			$this->content->template['tcat_text']="del";
		}


		//Kategorie ändern
		if (!empty($this->checked->submit)) {
			$error=0;
			foreach ($this->checked->cat_text as $key=>$value) {
				if (empty($value))$error=1;
			}
			if ($error!=1) {
				//Sprachdaten durchgehen
				foreach ($this->checked->cat_text as $key=>$value) {
					$sql=sprintf("SELECT * FROM %s
								  		WHERE cat_lang_id='%s' AND cat_lang_lang='%s' ",
						$this->cms->tbname['papoo_category_lang'],
						$this->db->escape($this->checked->cat_id),
						$this->db->escape($key)
					)
					;
					$result=$this->db->get_results($sql);
					//Wenn nicht leer eintragen und auf id vergeben
					if (!empty($result)) {
						$sql=sprintf("UPDATE %s SET
									  		cat_text='%s',
									  		cat_text_intern='%s',
									  		cat_descrip='%s' 
									  		WHERE cat_lang_id='%s' AND cat_lang_lang='%s' ",
							$this->cms->tbname['papoo_category_lang'],
							$this->db->escape($value),
							$this->db->escape($this->checked->cat_text_intern[$key]),
							"-",
							$this->db->escape($this->checked->cat_id),
							$this->db->escape($key)
						)
						;
						$this->db->query($sql);
					}
					else {
						$sql=sprintf("INSERT INTO %s SET
									  		cat_text='%s',
									  		cat_text_intern='%s',
									  		cat_descrip='%s', 
												cat_lang_id='%s',
												cat_lang_lang='%s' ",
							$this->cms->tbname['papoo_category_lang'],
							$this->db->escape($value),
							$this->db->escape($this->checked->cat_text_intern[$key]),
							"-",
							$this->db->escape($this->checked->cat_id),
							$this->db->escape($key)
						)
						;
						$this->db->query($sql);

					}
				}
				$sql=sprintf("UPDATE %s SET cat_startseite_anzahl='%d', cat_startseite_ok='%d' WHERE cat_id='%d'",
					$this->cms->tbname['papoo_category'],
					$this->db->escape($this->checked->cat_startseite_anzahl),
					$this->db->escape($this->checked->cat_startseite_ok),
					$this->db->escape($this->checked->cat_id)

				);
				$this->db->query($sql);

				// WEnn id gesetzt dann:
				if (is_numeric($this->checked->cat_id)) {
					//alte Schreibrechte löschen
					$sql=sprintf("DELETE FROM %s WHERE cat_wlid='%s'",
						$this->cms->tbname['papoo_category_lookup_write'],
						$this->db->escape($this->checked->cat_id)
					);
					$this->db->query($sql);

					//Schreibrechte eintragen
					if (is_array($this->checked->cat_write_ar)) {
						foreach ($this->checked->cat_write_ar as $write) {
							$sql=sprintf("INSERT INTO %s SET cat_wlid='%s', cat_wlgruppe='%s'",
								$this->cms->tbname['papoo_category_lookup_write'],
								$this->db->escape($this->checked->cat_id),
								$this->db->escape($write)
							);
							$this->db->query($sql);
						}
					}

					//alte Leserechte löschen
					$sql=sprintf("DELETE FROM %s WHERE cat_rlid='%s'",
						$this->cms->tbname['papoo_category_lookup_read'],
						$this->db->escape($this->checked->cat_id)
					);
					$this->db->query($sql);

					//Leserechte eintragen
					if (is_array($this->checked->cat_read_ar)) {
						foreach ($this->checked->cat_read_ar as $read) {
							$sql=sprintf("INSERT INTO %s SET cat_rlid='%s', cat_rlgruppe='%s'",
								$this->cms->tbname['papoo_category_lookup_read'],
								$this->db->escape($this->checked->cat_id),
								$this->db->escape($read)
							);
							$this->db->query($sql);
						}
					}
					// 5. Weiterleitung für neuen Seitenaufbau
					$location_url = "./kategorie.php?menuid=68&drin=1";
					if ($_SESSION['debug_stopallredirect']){
						echo '<a href="'.$location_url.'">Weiter</a>';
					}
					else {
						header("Location: $location_url");
					}
					exit();
				}
			}
		}


		if (empty($this->checked->cat_id)) {
			if(!empty($_SESSION['langid_front']) ) {
				$sprach_id=$_SESSION['langid_front'];
			}
			else {
				$sprach_id=$this->cms->lang_id;
			}

			$sql = sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_lang_id AND cat_lang_lang='%s'",
				$this->cms->tbname['papoo_category_lang'],
				$this->cms->tbname['papoo_category'],
				$sprach_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			$this->content->template['kat_n']=$result;

		}
		else {
			//Editieren auf alt setzen
			$this->content->template['kat_alt']="ok";

			//Stammdaten rausholen
			$sql=sprintf("SELECT * FROM %s WHERE cat_id='%d'",
				$this->cms->tbname['papoo_category'],
				$this->db->escape($this->checked->cat_id)
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			#print_r($result);
			$this->content->template['kat_one']=$result;

			//Sprachen setzen
			$this->cat_preset_sprachen();
			#print_r($this->content->template['catlang']);

			//Sprachdaten rausholen
			$sql=sprintf("SELECT * FROM %s WHERE cat_lang_id='%d'",
				$this->cms->tbname['papoo_category_lang'],
				$this->db->escape($this->checked->cat_id)
			);
			$result_lang=$this->db->get_results($sql,ARRAY_A);
			#print_r($result_lang);

			//Sprachdaten einlesen für die Ausgabe
			$langdat=array();
			$i=0;
			foreach ($this->content->template['catlang'] as $lang_ar)
			{
				$langdat[$lang_ar['lang_id']]['cat_text']=$result_lang[$i]['cat_text'];
				$langdat[$lang_ar['lang_id']]['cat_text_intern']=$result_lang[$i]['cat_text_intern'];
				$i++;
			}
			#print_r($langdat);
			$this->content->template['catdatlang']=$langdat;

			//Gruppenrechte Lesen rausholen und Übergeben
			$sql=sprintf("SELECT * FROM %s WHERE cat_rlid='%s'",
				$this->cms->tbname['papoo_category_lookup_read'],
				$this->db->escape($this->checked->cat_id)
			);
			$this->res_read=$this->db->get_results($sql,ARRAY_A);
			//Gruppenrechte Schreiben rausholen und Übergeben
			$sql=sprintf("SELECT * FROM %s WHERE cat_wlid='%s'",
				$this->cms->tbname['papoo_category_lookup_write'],
				$this->db->escape($this->checked->cat_id)
			);
			$this->res_write=$this->db->get_results($sql,ARRAY_A);

			//Gruppen setzen
			$this->cat_hol_gruppen_liste();
		}
		//kat_alt
		IfNotSetNull($this->checked->cat_id);
		$this->content->template['kurl']="./kategorie.php?menuid=68";
		$this->content->template['cat_id']=$this->checked->cat_id;
	}

	/**
	 * Gruppenliste holen
	 */
	function cat_hol_gruppen_liste()
	{
		// Gruppendaten holen
		$result = $this->db->get_results("SELECT gruppenname, gruppeid FROM
		" . $this->cms->papoo_gruppe . " 
		ORDER BY gruppenname ");
		$table_data2 = array ();
		// alle Gruppen durchloopen und aktive anhaken
		foreach ($result as $rowx) {
			// Administratoren nicht anzeigen. Diese bekommen immer alle Rechte.
			if ($rowx->gruppeid != 1) {
				//Lesezugriffe setzen
				$checkedr = "";
				if (!empty ($this->res_read)) {
					foreach ($this->res_read as $x) {
						if ($rowx->gruppeid == $x['cat_rlgruppe']) {
							$checkedr = 'nodecode:checked="checked"';
						}
					}
				}
				//Schreibzugriffe setzen
				$checkedw = "";
				if (!empty ($this->res_write)) {
					foreach ($this->res_write as $x) {
						if ($rowx->gruppeid == $x['cat_wlgruppe']) {
							$checkedw = 'nodecode:checked="checked"';
						}
					}
				}
				array_push($table_data2, array (
					'gruppename' => $rowx->gruppenname,
					'gruppeid' => $rowx->gruppeid,
					'checkedw' => $checkedw,
					'checkedr' => $checkedr
				));
			}
		}
		$this->content->template['table_data'] = $table_data2;
	}

	/**
	 * Baut die Liste für Ausgabe und Anzeige der verschiedenen Sprachen auf
	 */
	function cat_preset_sprachen()
	{
		// aktive Sprachen raussuchen
		$sql = sprintf("SELECT lang_id, lang_long FROM %s WHERE more_lang='2' OR lang_short='%s' ",
			$this->cms->papoo_name_language,
			$this->cms->frontend_lang
		);
		$sprachen = $this->db->get_results($sql);
		foreach ($sprachen as $sprache) {
			//Spracharray füllen
			$this->content->template['catlang'][] = array (
				'lang_id' => $sprache->lang_id,
				'language' => $sprache->lang_long
			);
		}
	}


	/**
	 * Alle Kategorien neu durchsortieren
	 */
	function reorder()
	{
		$sql = sprintf("SELECT * FROM %s ORDER BY cat_order_id",
			$this->cms->tbname['papoo_category']
		);
		$result=$this->db->get_results($sql);

		$i = 10;

		foreach ($result as $dat) {
			$sql = sprintf("UPDATE %s SET cat_order_id='%s' WHERE cat_id='%s' LIMIT 1",
				$this->cms->tbname['papoo_category'],
				$i,
				$this->db->escape($dat->cat_id)
			);
			$this->db->query($sql);
			$i += 10;
		}
	}

	/**
	 * Kategorien sortieren
	 */
	function switch_order()
	{
		if (!empty($this->checked->submitorder)) {
			if (!empty($this->checked->cat_order)) {
				foreach($this->checked->cat_order as $cat_id => $order_id) {
					if (is_numeric($cat_id) && is_numeric($order_id)) {
						$sql = sprintf("UPDATE %s SET cat_order_id='%d' WHERE cat_id='%d' LIMIT 1",
							$this->cms->tbname['papoo_category'],
							$order_id,
							$cat_id
						);
						$this->db->query($sql);
					}
				}
				$this->reorder();
			}
		}

		$this->content->template['katsort'] ="ok";

		if(!empty($_SESSION['langid_front']) ) {
			$sprach_id=$_SESSION['langid_front'];
		}
		else {
			$sprach_id=$this->cms->lang_id;
		}
		$sql = sprintf("SELECT * FROM %s,%s WHERE cat_id=cat_lang_id AND cat_lang_lang='%s' ORDER BY cat_order_id",
			$this->cms->tbname['papoo_category_lang'],
			$this->cms->tbname['papoo_category'],
			$sprach_id
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		$this->content->template['kat_n'] = $result;
	}
}

$cat_class = new cat_class();