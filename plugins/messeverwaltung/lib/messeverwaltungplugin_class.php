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
 * Class messeplugin
 */
class messeplugin
{

	/** @var string  */
	var $news = "";

	/**
	 * messeplugin constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		global $cms, $db, $message, $user, $weiter, $content, $intern_image, $checked,
			   $mail_it, $replace, $db_praefix, $intern_stamm, $diverse, $dumpnrestore;

		// und einbinden in die Klasse

		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;
		$this->message = &$message;
		$this->user = &$user;
		$this->weiter = &$weiter;
		$this->intern_image = &$intern_image;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->intern_stamm = &$intern_stamm;
		$this->replace = &$replace;
		$this->diverse = &$diverse;
		$this->dumpnrestore = &$dumpnrestore;
		$this->make_messeplugin();
		$this->make_lang_var();
		$this->post_papoo();
		$this->content->template['plugin_message'] = "";
	}

	/**
	 * linkliste erstellen
	 */
	function make_messeplugin()
	{
		if ( defined("admin") ) {

			global $template;
			if ( $template != "login.utf8.html" ) {
				switch ( $this->checked->template ) {
					//Die Standardeinstellungen werden bearbeitet
				case "messeverwaltung/templates/messeverwaltungplugin.html":
					$this->check_pref();
					break;

					//Einen Eintrag erstellen
				case "messeverwaltung/templates/messeverwaltungplugin_create.html":
					$this->make_entry();
					break;

					//Einen EIntrag bearbeiten
				case "messeverwaltung/templates/messeverwaltungplugin_edit.html":
					$this->change_entry();
					break;

					//Einen Dump erstellen oder einspielen
				case "messeverwaltung/templates/messeverwaltungplugin_dump.html":
					$this->linkliste_dump();
					break;

				default:
					break;
				}
			}
		}
	}

	/**
	 * Sprachdateien einbinden, nach Sprache switchen
	 */
	function make_lang_var()
	{
		// Pfad einbinden
		global $pfad;

		// Backend Sprache
	}

	/**
	 * Wenn wir drau�en sind
	 */
	function post_papoo()
	{
		//Es ist kein Eintrag ausgew�hlt, daher entweder die Links oder komplett anzeigen
		if ( !defined("admin") ) {
			global $template;

			if ( stristr( $template,"messeverwaltung_do_front.html") ) {
				//Daten aktualisieren wenn gefordert
				$sql = sprintf( "SELECT messe_xml_link FROM %s",
					$this->cms->tbname['papoo_messe_pref']
				);
				#$xml_link=$this->db->get_var($sql);
				if ( !empty($xml_link) ) {
					#$this->diverse->get_sql("messe","pref",$xml_link);
				}
				//Sprachen einbinden
				if ( !defined("admin") ) {
					// cms Klasse einbinden
					global $cms;
					// Pfad einbinden

					// Frontend Sprache
				}

				//Wenn eine ausgew�hlt ist, anzeigen
				if ( !empty($this->checked->messe_id) ) {
					//rausuchen
					$sql = sprintf( "	SELECT * FROM %s, %s 
										WHERE messe_id='%s' 
										AND messe_lang_id='%s'",
						$this->cms->tbname['papoo_messe_daten'],
						$this->cms->tbname['papoo_messe_daten_lang'],
						$this->db->escape($this->checked->messe_id),
						$this->db->escape($this->cms->lang_id)
					);
					$result = $this->db->get_results( $sql, ARRAY_A );
					$i = 0;
					foreach ( $result as $dat ) {
						$result[$i]['messe_von'] = date( "d.m.Y", $dat['messe_von'] );
						$result[$i]['messe_bis'] = date( "d.m.Y", $dat['messe_bis'] );

						$tage = ( $dat['messe_bis'] - $dat['messe_von'] ) / 86400;
						$datum = explode( ".", $result[$i]['messe_von'] );
						$i++;
					}
					$this->content->template['anktt'] = $datum['0'];
					$this->content->template['ankmm'] = round( $datum['1'] ) - 1;
					$this->content->template['tage'] = $tage;
					$this->content->template['messe'] = $result;
				}
				else {
					//Sprachen ermitteln die eingestellt
					$lang_id = $this->cms->lang_id;
					$sql = sprintf( "	SELECT messe_lang  FROM %s ",
						$this->cms->tbname['papoo_messe_pref'] );
					$langstring = $this->db->get_var( $sql );
					$langarray = explode( ";", $langstring );
					if ( !in_array($this->cms->lang_id, $langarray) and $this->cms->lang_id > 2 ) {
						$lang_id = 2;
					}

					//Datum festsetzen
					$date = time();
					if ( $this->checked->sort_order =="messe_name" ) {
						$this->checked->sort_order = "messe_name";
					}
					elseif ($this->checked->sort_order =="messe_von") {
						$this->checked->sort_order = "messe_von";
					}
					else {
						$this->checked->sort_order = "messe_von";
					}
					//Alle Messen aus der Datenbank holen bis zum aktuellen Datum
					$sql = sprintf( "	SELECT * FROM %s, %s 
										WHERE messe_id=messe_id_id 
										AND messe_lang_id='%s' 
										AND messe_bis > '%s' 
										ORDER BY `%s` ASC",
						$this->cms->tbname['papoo_messe_daten'],
						$this->cms->tbname['papoo_messe_daten_lang'],
						$lang_id,
						$date,
						$this->db->escape($this->checked->sort_order)
					);
					$result = $this->db->get_results( $sql, ARRAY_A );
					$i = 0;
					if ( is_array($result) ) {
						foreach ( $result as $dat ) {
							if ( is_numeric($dat['messe_von']) ) {
								$result[$i]['messe_von'] = date( "d.m.Y", $dat['messe_von'] );
							}
							if ( is_numeric($dat['messe_bis']) ) {
								$result[$i]['messe_bis'] = date( "d.m.Y", $dat['messe_bis'] );
							}
							$i++;
						}
					}
					$this->content->template['messe_result'] = $result;

					$this->content->template['menuid_aktuell'] = $this->checked->menuid;
					$this->content->template['template'] = $this->checked->template;

					$this->content->template['linktext'] = "plugin.php";
					if ( $this->cms->mod_rewrite == 2 ) {
						#$this->content->template['linktext']="plugin";
					}
				}
			}
		}
	}

	/**
	 * Die Kategorienliste erstellen
	 */
	function get_cat_list()
	{
		$sql = sprintf( "SELECT * FROM %s, %s WHERE cat_id=cat_id_id AND cat_lang_id='1'",
			$this->cms->tbname['papoo_linkliste_cat'], $this->cms->tbname['papoo_linkliste_lang_cat'] );
		$result = $this->db->get_results( $sql, ARRAY_A );
		#print_r($result);
		$this->content->template['result_cat'] = $result;
	}

	/**
	 * Die Standardeinstellungen f�r das Glossar werden hier eingestellt
	 *
	 * @return void checked2 oder checked1
	 */
	function check_pref()
	{
		#$this->diverse->make_sql("linkliste");
		#$this->diverse->get_sql("linkliste","pref");

		$lang = "0";
		//Die Einstellungen sollen ver�ndert werden
		if ( !empty($this->checked->submitglossar) ) {
			//Sprache
			if ( empty($this->checked->lang) ) {
				$lang = 1;
			}
			else {
				foreach ( $this->checked->lang as $rein ) {
					$lang .= ";" . $rein;
				}
			}

			//Datenbank updaten
			$sql = sprintf( "	UPDATE %s SET 
								messe_xml_yn='%s', 
								messe_xml_link='%s',
								messe_lang='%s' 
								WHERE messe_id='1'",
				$this->cms->tbname['papoo_messe_pref'],
				$this->db->escape($this->checked->messe_xml_yn),
				$this->db->escape($this->checked->messe_xml_link),
				$lang
			);
			$this->db->query( $sql );

		}
		$this->make_lang();

		//Daten aus der Datenbank holen und zuweisen
		$sql = sprintf( "	SELECT * FROM %s",
			$this->cms->tbname['papoo_messe_pref'] );
		$result = $this->db->get_results( $sql, ARRAY_A );

		$this->content->template['xml_result'] = $result;
	}

	/**
	 * Spracheinstellungen ausgeben
	 */
	function make_lang()
	{
		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results( "SELECT * FROM " . $this->cms->papoo_name_language . "  " );

		$resultlang2 = $this->db->get_var( "SELECT messe_lang FROM " . $this->cms->
			tbname['papoo_messe_pref'] . "  " );
		$lang = explode( ";", $resultlang2 );

		$this->content->template['messe_language'] = array();
		// zuweisen welche Sprache ausgew�hlt sind
		foreach ( $resultlang as $rowlang ) {
			// chcken wenn Sprache gew�hlt
			if ( in_array($rowlang->lang_id, $lang) ) {
				$selected_more = 'nodecode:checked="checked"';
			}
			else {
				$selected_more = "";
			}
			// �berspringen, wenn Sprache als Standard ausgew�hlt ist
			if ( $row->lang_frontend != $rowlang->lang_short ) {
				array_push( $this->content->template['messe_language'],
					array(
						'language' => $rowlang->lang_long,
						'lang_id' => $rowlang->lang_id,
						'selected' => $selected_more,
					)
				);
			}
		}
	}

	/**
	 * Ein neuer Eintrag wird erstellt und die Option zum Datenbank durchloopen angeboten
	 * @return void
	 */
	function make_entry()
	{
		//Sprachen raussuchen
		$this->make_lang();

		if ( $this->checked->submitentry ) {
			//Bilddaten hochladen
			$this->intern_image->upload_picture();
			$this->checked->image_name = $this->content->template['image_name'];
			$this->checked->image_breite = $this->content->template['image_breite'];
			$this->checked->image_hoehe = $this->content->template['image_hoehe'];
			$this->checked->gruppe = $this->content->template['image_gruppe'];
			$this->checked->texte[1]['lang_id'] = 1;
			$this->checked->texte[1]['alt'] = $this->checked->messe_name;
			$this->checked->texte[1]['title'] = '';
			$this->checked->texte[1]['long_desc'] = '';

			$this->intern_image->upload_save( 'no' );
			$von_ar = explode( ".", $this->checked->messe_von );
			$bis_ar = explode( ".", $this->checked->messe_bis );

			if ( is_numeric($von_ar[0]) && is_numeric($von_ar[1]) && is_numeric($von_ar[2]) ) {
				$von = @mktime( 0, 0, 0, $von_ar[1], $von_ar[0], $von_ar[2] );
				$bis = @mktime( 0, 0, 0, $bis_ar[1], $bis_ar[0], $bis_ar[2] );
			}

			//Daten eintragen in Datenbank
			$sql = sprintf( "INSERT INTO %s SET messe_von='%s', messe_bis='%s', messe_logo='%s', messe_name='%s'",
				$this->cms->tbname['papoo_messe_daten'], $this->db->escape($von), $this->db->
				escape($bis), $this->db->escape($this->checked->image_name), $this->db->escape($this->
				checked->messe_name) );
			$this->db->query( $sql );
			$insertid = $this->db->insert_id;
			//Alle Sprachen linkliste_descrip
			foreach ( $this->checked->messe_descrip as $key => $value ) {
				$sql = sprintf( "INSERT INTO %s SET messe_id_id='%s', messe_lang_id='%s', messe_descrip='%s'",
					$this->cms->tbname['papoo_messe_daten_lang'], $insertid, $key, $value );
				$this->db->query( $sql );
			}
			//Sql Datei erneuern
			#$sql=sprintf("SELECT messe_xml_yn FROM %s",
			#$this->cms->tbname['papoo_messe_pref']
			#	);
			#	$this->export_xml=$this->db->get_var($sql);
			#	if ($this->export_xml==1){
			#	$this->diverse->make_sql("messe");
			#	}

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		else {
			$this->content->template['neuereintrag'] = "ok";
		}

		if ( $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
	}

	/**
	 * Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen
	 */
	function change_entry()
	{
		//Sprachen raussuchen
		$this->make_lang();

		//Es soll eingetragen werden
		if ( $this->checked->submitentry ) {
			//Bilddaten hochladen
			if ( !empty($_FILES['strFile']) ) {
				$this->intern_image->upload_picture();
				$this->checked->image_name = $this->content->template['image_name'];
				$this->checked->image_breite = $this->content->template['image_breite'];
				$this->checked->image_hoehe = $this->content->template['image_hoehe'];
				$this->checked->gruppe = $this->content->template['image_gruppe'];
				$this->checked->texte[1]['lang_id'] = 1;
				$this->checked->texte[1]['alt'] = $this->checked->messe_name;
				$this->checked->texte[1]['title'] = '';
				$this->checked->texte[1]['long_desc'] = '';

				if ( !empty($this->checked->image_name) ) {
					$this->intern_image->upload_save( 'no' );
				}
			}

			if ( empty($this->checked->image_name) ) {
				$sql = sprintf( "SELECT messe_logo FROM %s WHERE messe_id='%s'", $this->cms->
				tbname['papoo_messe_daten'], $this->db->escape($this->checked->messe_id) );
				$this->checked->image_name = $this->db->get_var( $sql );
			}
			if ( !empty($this->checked->messe_id) ) {
				$von_ar = explode( ".", $this->checked->messe_von );
				$bis_ar = explode( ".", $this->checked->messe_bis );
				if ( is_numeric($von_ar[0]) && is_numeric($von_ar[1]) && is_numeric($von_ar[2]) ) {
					$von = mktime( 0, 0, 0, $von_ar[1], $von_ar[0], $von_ar[2] );
					$bis = mktime( 0, 0, 0, $bis_ar[1], $bis_ar[0], $bis_ar[2] );
				}
				$sql = sprintf( "	UPDATE %s SET 
									messe_von='%s', 
									messe_bis='%s', 
									messe_logo='%s', 
									messe_name='%s' 
									WHERE messe_id='%s'",
					$this->cms->tbname['papoo_messe_daten'],
					$this->db->escape($von),
					$this->db->escape($bis),
					$this->db->escape($this->checked->image_name),
					$this->db->escape($this->checked->messe_name),
					$this->db->escape($this->checked->messe_id)
				);
				$this->db->query( $sql );
				$insertid = $this->db->escape( $this->checked->messe_id );
				//Alle Sprachen linkliste_descrip
				$sql = sprintf( "DELETE FROM %s WHERE messe_id_id='%s'", $this->cms->tbname['papoo_messe_daten_lang'],
					$insertid );
				$this->db->query( $sql );

				//Alle Sprachen
				foreach ( $this->checked->messe_descrip as $key => $value ) {
					$sql = sprintf( "	INSERT INTO %s SET 
										messe_id_id='%s', 
										messe_lang_id='%s', 
										messe_descrip='%s'",
						$this->cms->tbname['papoo_messe_daten_lang'],
						$insertid,
						$key,
						$value
					);
					$this->db->query( $sql );
				}
				//Sql Datei erneuern
				#$sql=sprintf("SELECT messe_xml_yn FROM %s",
				#$this->cms->tbname['papoo_messe_pref']
				#	);
				#	$this->export_xml=$this->db->get_var($sql);
				#	if ($this->export_xml==1){
				#		$this->diverse->make_sql("messe");
				#	}
			}

			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=" . $this->checked->template . "&fertig=drin";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		if ( !empty($this->checked->glossarid) ) {
			//Nach id aus der Datenbank holen
			$sql = sprintf( "SELECT * FROM %s WHERE messe_id='%s'",
				$this->cms->tbname['papoo_messe_daten'],
				$this->db->escape($this->checked->glossarid)
			);

			$result = $this->db->get_results( $sql );

			if ( !empty($result) ) {
				foreach ( $result as $glos ) {
					$this->content->template['messe_name'] = $glos->messe_name;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
					$this->content->template['messe_id'] = $glos->messe_id;
					$this->content->template['messe_logo'] = $glos->messe_logo;
					if ( is_numeric($glos->messe_von) ) {
						$this->content->template['messe_von'] = date( "d.m.Y", $glos->messe_von );
					}
					if ( is_numeric($glos->messe_bis) ) {
						$this->content->template['messe_bis'] = date( "d.m.Y", $glos->messe_bis );
					}
					$this->content->template['messe_logo'] = $glos->messe_logo;
				}
				//Sprachdaten raussuchen
				$sql = sprintf( "SELECT * FROM %s WHERE messe_id_id='%s'", $this->cms->tbname['papoo_messe_daten_lang'],
					$this->db->escape($this->checked->glossarid) );
				$resultx = $this->db->get_results( $sql );

				if ( !empty($resultx) ) {
					foreach ( $resultx as $lang ) {
						$wort[$lang->messe_lang_id] = "nobr:" . $lang->messe_descrip;
						#$des[$lang->linkliste_lang_lang] = "nobr:".$lang->linkliste_descrip;
					}
				}
				$this->content->template['messe_descrip'] = $wort;
				#$this->content->template['linkliste_descrip']=$des;
			}
		}
		else {
			//Daten rausholen und als Liste anbieten
			$this->content->template['list'] = "ok";
			//Daten rausholen
			$sql = sprintf( "	SELECT * FROM %s, %s 
								WHERE messe_id=messe_id_id 
								AND messe_lang_id='1' 
								ORDER BY messe_name ASC",
				$this->cms->tbname['papoo_messe_daten'],
				$this->cms->tbname['papoo_messe_daten_lang']
			);
			$result = $this->db->get_results( $sql, ARRAY_A );

			//Daten f�r das Template zuweisen
			$this->content->template['list_dat'] = $result;
			$this->content->template['messe_link'] = $_SERVER['PHP_SELF'] . "?menuid=" . $this->
				checked->menuid . "&template=" . $this->checked->template . "&glossarid=";
			;
		}
		if ( $this->checked->fertig == "drin" ) {
			$this->content->template['eintragmakeartikelfertig'] = "ok";
		}
		//Anzeigen das Eintrag gel�scht wurde
		if ( $this->checked->fertig == "del" ) {
			$this->content->template['deleted'] = "ok";
			$this->content->template['messe_id'] = $this->checked->glossarid;
		}
		//Soll  gel�scht werden
		if ( !empty($this->checked->submitdelecht) ) {
			//Eintrag nach id l�schen und neu laden
			$sql = sprintf( "	DELETE FROM %s 
								WHERE messe_id='%s'",
				$this->cms->tbname['papoo_messe_daten'],
				$this->db->escape($this->checked->messe_id)
			);
			$this->db->query( $sql );
			$insertid = $this->db->escape( $this->checked->messe_id );
			//Alle Sprachen linkliste_descrip
			$sql = sprintf( "	DELETE FROM %s 
								WHERE messe_id_id='%s'",
				$this->cms->tbname['papoo_messe_daten_lang'],
				$insertid
			);
			$this->db->query( $sql );
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=" . $this->checked->template . "&fertig=del";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}

		//Soll wirklich gel�scht werden?
		if ( !empty($this->checked->submitdel) ) {
			$this->content->template['glossarname'] = $this->checked->glossarname;
			$this->content->template['glossarid'] = $this->checked->glossarid;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * Die Glossardaten dumpen und wieder zur�ckspielen
	 */
	function linkliste_dump()
	{
		$this->diverse->extern_dump( "messe" );

		//Sql Datei erneuern
		#$sql=sprintf("SELECT messe_xml_yn FROM %s",
		#	$this->cms->tbname['papoo_messe_pref']
		#	);
		#	$this->export_xml=$this->db->get_var($sql);
		#	if ($this->export_xml==1){
		#	$this->diverse->make_sql("linkliste");
		#}
	}
}

$messeplugin = new messeplugin();
