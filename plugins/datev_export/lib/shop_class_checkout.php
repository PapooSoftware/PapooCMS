<?php
/**
 * shop_class_checkout
 * Die Checkout Klasse realisiert die Kasse des Shops
 *
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */


if (stristr( $_SERVER['PHP_SELF'],'shop_class_checkout.php')) die('You are not allowed to see this page directly');

/**
 * Class shop_class_checkout
 */
class shop_class_checkout
{
	/** @var array */
	public $BEZAHLMETHODEN_HANDLER = array(
		//Lastschrift
		3 => array(NULL, NULL, TRUE), // Immer auf payed setzen
		//Sofortüberweisung
		5 => array('/plugins/papoo_shop/lib/shop_class_sofort_ueberweisung.php', 'shop_class_sofort_ueberweisung', 'do_sofort_ueberweisung_checkout'),
		// Paypal
		6 => array('/plugins/papoo_shop/lib/shop_class_paypal.php', 'shop_class_paypal', 'do_paypal_checkout'),
		//Expercash Kreditkarte
		//7 => array('/plugins/papoo_shop/lib/shop_class_expercash_cc.php', 'shop_class_expercash_cc', 'do_shop_class_expercash_cc_checkout'),
		//Expercash Giropay
		8 => array('/plugins/papoo_shop/lib/shop_class_expercash_giropay.php', 'shop_class_expercash_giro', 'do_shop_class_expercash_giro_checkout'),
		//Telecash
		10 => array('/plugins/papoo_shop/lib/shop_class_telecash.php', 'shop_class_telecash', 'do_tcash'),
		// Firstcash Kreditkarte
		7 => array('/plugins/papoo_shop/lib/shop_class_firstcash.php', 'shop_class_firstcash', 'do_shop_class_firstcash_cc_checkout')
	);


	/**
	 * shop_class::shop_class()
	 * Initialisierung und Einbindung von Klassen
	 * @return void
	 */
	function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $diverse, $db, $checked, $content, $user, $cms;
		$this->diverse = &$diverse;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->user = &$user;
		$this->cms = &$cms;

		$shop = new shop_class();
		$this->shop = &$shop;
		//$this->trigger_abgelaufene_downloads_und_zugriffe();
	}

	function shop_make_checkout_cc()
	{
		$this->content->template['shop_iframe_cc']="OK";
		$this->content->template['iframe_data']=$_SESSION['shop_cc_url_data'];
	}

	function shop_make_checkout_gp()
	{
		$this->content->template['shop_iframe_gp']="OK";
		$this->content->template['iframe_data']=$_SESSION['shop_cc_url_data'];
	}

	/**
	 * shop_frontend_make_checkout_fertig function.
	 *
	 * @access public
	 * @return void
	 */
	function shop_frontend_make_checkout_fertig()
	{
		//Überprüfen ob gerade ein angemeldeter User da ist und ob der auch Shop Kunden ist
		$user_status=$this->shop_get_user_status();
		$this->shop_frontend_make_checkout_bestaetigen();
		$this->content->template['shopping_cart_zwsumme'];
		//Noch kein Shopkunde, dann auf Erste Seite lenken
		if (!is_numeric($user_status)) {
			$this->reload("checkout_rl","");
		};
		//Check ob noch was im Warenkorb

		if (!empty($_SESSION['shop_cart']) ) {
			$_SESSION['SHOP_Bestell_id']=time()."_".substr(microtime(),2,5);

			$bezahlmethode = (int)$this->user_Daten_bestellung[0]['extended_user_bezahl_art_letzt_bestellung'];

			// Vorbereitungen für Spezialfall Paypal
			if ($bezahlmethode == 5 || $bezahlmethode == 6) {
				//Nur wenn der Aufruf nicht von Paypal kommt.
				if (empty($this->checked->tx)) {
					//Belegtyp auf Angebot setzen
					$this->checked->order_typ=3;

					//Beleg erstellen damit der in der DB ist falls der Kunde nicht zurück springt.
					$insert_id=$this->shop_save_bestellung();
				}
			}

			// Klasse für Bezahlmethode suchen und ausführen
			if (isset($this->BEZAHLMETHODEN_HANDLER[$bezahlmethode])) {
				list($handler_include, $handler_class, $handler_method) = $this->BEZAHLMETHODEN_HANDLER[$bezahlmethode];

				if ($handler_include) {
					require_once PAPOO_ABS_PFAD.$handler_include;
				}

				if ($handler_class === NULL) {
					if ($handler_method === NULL) {}
					elseif ($handler_method === TRUE) { $_SESSION['shop_is_payed']=1; }
					elseif ($handler_method === FALSE) { $_SESSION['shop_is_payed']=0; }
					else {
						$handler_method();
					}
				}
				else {
					$handler=new $handler_class();
					$handler->$handler_method($this);
				}
			}

			if ($_SESSION['shop_is_payed']==1) {
				$this->beleg_is_bezahlt_check=1;
			}

			//Bestellung speichern
			$insert_id=$this->shop_save_bestellung();
			unset($_SESSION['shop_is_payed']);
			//Bestellungsdaten verschicken per Mail
			$this->shop_send_mail_an_besteller($insert_id);
		}
		#$this->shop_send_mail_an_besteller($insert_id);
		//Warenkorb auf 0 setzen
		#unset($_SESSION['shop_cart']);
		//Conversion tracken
		$sql=sprintf("SELECT einstellungen_lang_conversion_code_js FROM %s",
			$this->cms->tbname['plugin_shop_daten']
		);
		$this->content->template['conversion_code_js']="nodecode:".$this->db->get_var($sql);

		//Daten freisetzen
		$this->shop_unset_data_beleg_frontend();

		// Wenn Redirect gewünscht, ausführen
		if (!empty($this->checkout_fertig_redirect)) {
			$this->do_redirect($this->checkout_fertig_redirect);
		}

	}

	/**
	 * @param $insert_id
	 */
	function shop_send_mail_an_besteller($insert_id)
	{
		$mailing = new shop_class_mailing();
		$mailing->do_mail_beleg_frontend($insert_id);
	}

	/**
	 * shop_class_checkout::shop_fakturierung_create_basisdaten()
	 * Hier werden die Basisdaten eines Beleges vorbelegt
	 * @return void
	 */
	function shop_fakturierung_create_basisdaten_frontend()
	{
		//Sprache übergeben
		$this->checked->order_lang=$this->cms->lang_id;
		//AGB ist im Backend immer ok
		$this->checked->order_agb_ok=$this->checked->checkout_bitte_bestaetigen_dass_agb_gelsen;
		//Status des Beleges
		$this->checked->order_status=0;
		//IP Adresse des erstellenden
		$this->checked->order_ip=$_SERVER["REMOTE_ADDR"];
		//USer_Agent
		$this->checked->order_user_agent=$_SERVER["HTTP_USER_AGENT"];
		//ADmin USer
		$this->checked->order_admin_user=$this->user->userid;
		//Letzte Änderung
		$this->checked->order_last_change=time();
		//Datum Erstellung
		$this->checked->order_order_date=time();
		//Wo ertellt
		$this->checked->order_origin="FRONTEND";
		//KOmmentat
		$this->checked->order_kommentar=$this->checked->checkout_ie_agbs_area;
		//Versanddkosten
		$this->checked->order_summe_netto_versandkosten=$this->content->template['shopping_cart_versandkosten_summe'];

		$_SESSION['shopping_cart_zwsumme']=$this->content->template['shopping_cart_zwsumme'];

		//NEtto Betrag
		$this->checked->order_summe_netto=$_SESSION['shopping_cart_zwsumme'];
		$this->checked->order_summe_netto=(str_ireplace(",",".", $this->checked->order_summe_netto));
		$this->checked->order_summe_netto=(str_ireplace(" ","", $this->checked->order_summe_netto));
		if (is_array($_SESSION['steuer_anteile'])) {
			foreach ($_SESSION['steuer_anteile'] as $key=>$value) {
				$value['anteil']=str_ireplace(",",".", $value['anteil']);
				$value['anteil']=str_ireplace(" ","", $value['anteil']);
				$this->checked->order_summe_netto=$this->diverse->dround2($this->checked->order_summe_netto - $value['anteil']);
				$this->checked->order_summe_steuer=$value['satz'];;
			}
		}

		$_SESSION['shopping_cart_zwsumme']=str_ireplace(".",".",$this->content->template['shopping_cart_zwsumme']);
		//Steuerbetrag

		//Zwischensumme Punkt korrigieren
		$_SESSION['shopping_cart_zwsumme']=str_ireplace(",",".",$_SESSION['shopping_cart_zwsumme']);
		//Doppelt gerundetetn Wert holen für Bruttowert
		$brutto = str_ireplace( " ", "", $_SESSION['shopping_cart_zwsumme']);
		$this->checked->order_summe_brutto=$this->diverse->dround2($brutto);
		//Rabatt übergeben
		$this->checked->order_summe_rabatt="";
	}

	/**
	 * shop_class_fakturierung::shop_fakturierung_create_user_beleg_daten()
	 * Hier werden die Userdaten eines Beleges vorbelegt
	 * @return void
	 */
	function shop_fakturierung_create_user_beleg_daten_frontend()
	{
		//Bezahlart übergeben
		$this->checked->kunden_order_payment=$this->user_Daten_bestellung[0]['extended_user_bezahl_art_letzt_bestellung'];
		//Lieferart übergeben
		$this->checked->kunden_order_versand=$this->user_Daten_bestellung[0]['extended_user_versandart_der_letzten_bestellung'];
		//

		//herausbekommen ob Beleg als bezahlt markiert werden soll
		//Daten rausholen und ausgeben}
		$sql=sprintf("SELECT shop_payment_eleg_ist_bezahlt_wenn_ausgewhlt FROM %s WHERE 
									shop_payment_id='%d'",
			$this->cms->tbname['plugin_shop_daten_payment_arten'],
			$this->db->escape($this->checked->kunden_order_payment)
		);
		if ($this->beleg_is_bezahlt_check=1) {
			$this->beleg_is_bezahlt=$this->db->get_var($sql);
		}

		//Restliche Felder übergeben
		if (is_array($this->user_Daten_bestellung[0])) {
			foreach ($this->user_Daten_bestellung[0] as $key=>$value) {
				if ($key=="extended_user_id") {
					$kunden_order_user_id=$value;;
				}
				else {
					$feld=str_replace("extended_user","kunden_order",$key);
					$this->checked->$feld=$value;
				}
			}
		}
		//LS Daten übergeb
		$this->checked->kunden_order_kontonummer=$_SESSION['shop_checked_lastschrift']['checkout_ontonummer'];
		$this->checked->kunden_order_bankleitzahl_blz=$_SESSION['shop_checked_lastschrift']['checkout_ankleitzahl'];
		$this->checked->kunden_order_name_der_bank=$_SESSION['shop_checked_lastschrift']['checkout_ame_der_ank'];
		$this->checked->kunden_order_konto_inhaber=$_SESSION['shop_checked_lastschrift']['checkout_ame_des_ontoinhabers'];
		;
		$this->checked->extended_user_konto_verifiziert=$_SESSION['shop_checked_lastschrift']['extended_user_konto_verifiziert'];
		$this->checked->kunden_order_konto_verifiziert=$_SESSION['shop_checked_lastschrift']['extended_user_konto_verifiziert'];
		//Und direkt die Userdaten dazu auch noch korrigieren
		$sql=sprintf("UPDATE %s
									SET extended_user_kontonummer='%s',
									extended_user_bankleitzahl_blz='%s',
									extended_user_name_der_bank='%s',
									extended_user_konto_inhaber='%s'
									WHERE extended_user_user_id='%d'",
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			$this->db->escape($this->checked->kunden_order_kontonummer),
			$this->db->escape($this->checked->kunden_order_bankleitzahl_blz),
			$this->db->escape($this->checked->kunden_order_name_der_bank),
			$this->db->escape($this->checked->kunden_order_konto_inhaber),
			$this->user->userid
		);
		$this->db->query($sql);

		$this->checked->kunden_order_user_id=$kunden_order_user_id;;
	}

	/**
	 * shop_class_checkout::check_bestand_produkt_nach_kauf()
	 * Diese Funktion überprüft ob ein Artikel bei 0 Menge ist und
	 * gibt dann false zurück
	 *
	 * @param mixed $id
	 * @param mixed $anzahl
	 * @return bool
	 */
	function check_bestand_produkt_nach_kauf($id,$anzahl)
	{
		$sql=sprintf("SELECT * FROM %s 
									WHERE produkte_lang_wenn_null_deaktivieren=1 
									AND produkte_lang_id='%d'
									AND produkte_lang_anzahl_der_produkte<1",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($id)
		);
		$produkte_ausverkauft=$this->db->get_results($sql);

		//Wenn Produkte ausverkauft, dann melden
		if ($produkte_ausverkauft) {
			return false;
		}
		return true;
	}

	/**
	 * shop_class_checkout::make_bestand_produkt_nach_kauf()
	 * Diese Funktion updated die Anzahl des Produktes
	 * wenn die Bestandsführung genutzt wird
	 * und deaktiviert die Produkte die kleiner 1 sind
	 * wenn das Häkchen dafür gesetzt ist.
	 *
	 * @param $id
	 * @param $anzahl
	 * @return bool
	 */
	function make_bestand_produkt_nach_kauf($id,$anzahl)
	{
		//Anzahl Updaten wo Bestandsführung genutzt wird
		$sql=sprintf("UPDATE %s SET 
									produkte_lang_anzahl_der_produkte=produkte_lang_anzahl_der_produkte-'%d'
									WHERE produkte_lang_bestandsfuerhung_nutze=1 
									AND produkte_lang_id='%d'
									AND produkte_lang_lang_id='%d'
									",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($anzahl),
			$this->db->escape($id),
			$this->cms->lang_id
		);
		$this->db->query($sql);

		//Mindestmenge rausholen
		$sql=sprintf("SELECT produkte_lang_mindestmenge, 
									produkte_lang_anzahl_der_produkte FROM %s 
									WHERE 
									produkte_lang_id='%d'
									AND produkte_lang_lang_id='%d'
									",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($id),
			$this->cms->lang_id
		);
		$produkte_zahlen=$this->db->get_results($sql,ARRAY_A);

		//Wenn Mindestmenge unterschritten in Array zuweisen für späteren Versand
		if ($produkte_zahlen[0]['produkte_lang_mindestmenge']>$produkte_zahlen[0]['produkte_lang_anzahl_der_produkte']) {
			$this->produkt_unter_mindestmenge[$id]=$id;
		}

		$sql=sprintf("SELECT * FROM %s 
									WHERE produkte_lang_wenn_null_deaktivieren=1 
									AND produkte_lang_id='%d'
									AND produkte_lang_anzahl_der_produkte<1
									AND produkte_lang_lang_id='%d'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($id),
			$this->cms->lang_id
		);
		$produkte_ausverkauft=$this->db->get_results($sql,ARRAY_A);

		//Wenn Produkte ausverkauft, dann melden
		if ($produkte_ausverkauft) {
			$this->produkt_ausverkauft[$id]=$id;
			$this->del_cache_files($id);
			$this->diverse->remove_cache_file(basename($produkte_ausverkauft[0]['produkte_lang_produkt_surl']));
		}

		//Produkt deaktivieren wo gewünscht
		$sql=sprintf("UPDATE %s SET 
									produkte_lang_aktiv='0'
									WHERE produkte_lang_wenn_null_deaktivieren=1 
									AND produkte_lang_id='%d'
									AND produkte_lang_anzahl_der_produkte<1",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($id)
		);
		$this->db->query($sql);

		return true;
	}

	/**
	 * shop_class_checkout::check_bastand_variationen_beim_kauf()
	 * Diese Funktion überprüft zuerst ob eine Variation bei
	 * 0 Menge ist und gibt dann fals zurück
	 *
	 * @param mixed $varid
	 * @param mixed $pid
	 * @param mixed $anzahl
	 * @return bool
	 */
	function check_bastand_variationen_beim_kauf($varid,$pid,$anzahl)
	{
		//Checken ob auf 0
		$sql=sprintf("SELECT * FROM %s 
									WHERE prodvar_variations_id1='%d' 
									AND prodvar_variations_id2='%d' 
									AND prodvar_variations_produkt_anzahl<1",
			$this->cms->tbname['plugin_shop_produkt_variationen'],
			$this->db->escape($varid),
			$this->db->escape($pid)
		);
		$variationen_ausverkauft=$this->db->get_results($sql);

		if ($variationen_ausverkauft) {
			return false;
		}
		return true;
	}

	/**
	 * shop_class_checkout::make_bastand_variationen_beim_kauf()
	 * Diese Funktion setzt die Lagerhaltung bei den Variationen um
	 *
	 * @param mixed $varid
	 * @param mixed $pid
	 * @param $anzahl
	 * @return bool
	 */
	function make_bastand_variationen_beim_kauf($varid,$pid,$anzahl)
	{
		//Produkt deaktivieren wo gewünscht
		$sql=sprintf("UPDATE %s SET 
									prodvar_variations_produkt_anzahl=prodvar_variations_produkt_anzahl-'%d'
									WHERE prodvar_variations_id1='%d' 
									AND prodvar_variations_id2='%d' ",
			$this->cms->tbname['plugin_shop_produkt_variationen'],
			$this->db->escape($anzahl),
			$this->db->escape($varid),
			$this->db->escape($pid)
		);
		$this->db->query($sql);

		//Mindestmenge rausholen
		$sql=sprintf("SELECT produkte_lang_mindestmenge
									FROM %s 
									WHERE 
									produkte_lang_id='%d'
									",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($id)
		);
		$produkte_zahlen=$this->db->get_results($sql);

		//Menge der Variation bestimmen
		$sql=sprintf("SELECT prodvar_variations_produkt_anzahl FROM %s 
									WHERE prodvar_variations_id1='%d' 
									AND prodvar_variations_id2='%d'
									",
			$this->cms->tbname['plugin_shop_produkt_variationen'],
			$this->db->escape($varid),
			$this->db->escape($pid)
		);
		$variationen_anzahl=$this->db->get_results($sql,ARRAY_A);

		if ($produkte_zahlen[0]['produkte_lang_mindestmenge']>$variationen_anzahl[0]['prodvar_variations_produkt_anzahl']) {
			$this->produkt_unter_mindestmenge[$id]=$varid;
		}
		//Und auf Mindestmenge prüfen
		//Checken ob auf 0
		$sql=sprintf("SELECT * FROM %s 
									WHERE prodvar_variations_id1='%d' 
									AND prodvar_variations_id2='%d' 
									AND prodvar_variations_produkt_anzahl<1",
			$this->cms->tbname['plugin_shop_produkt_variationen'],
			$this->db->escape($varid),
			$this->db->escape($pid)
		);
		$variationen_ausverkauft=$this->db->get_results($sql);

		//Wenn Produkte ausverkauft, dann melden
		if ($variationen_ausverkauft) {
			$this->produkt_ausverkauft[$id]=$varid;
		}
		return true;
	}

	/**
	 * shop_class_checkout::insert_in_most_wanted()
	 *
	 * @param mixed $pid
	 * @return void
	 */
	function insert_in_most_wanted($pid)
	{
		if (is_numeric($pid)) {
			//Zuerst herausbekommen ob schon existiert
			$sql=sprintf("SELECT produkte_most_counter  FROM %s
										WHERE produkte_most_produkt_id='%d'  	",
				$this->cms->tbname['plugin_shop_lookup_prod_most_wanted'],
				$pid
			);
			$result=$this->db->get_var($sql);

			//Existiert schon, dann updaten
			if ($result>=1) {
				$sql=sprintf("UPDATE %s 
											SET produkte_most_counter=produkte_most_counter+1
											WHERE produkte_most_produkt_id='%d'",
					$this->cms->tbname['plugin_shop_lookup_prod_most_wanted'],
					$pid
				);
				$this->db->query($sql);
			}
			//Neu, dann eintragen
			else {
				$sql=sprintf("INSERT INTO %s
											SET produkte_most_produkt_id='%d',
											produkte_most_counter='1'",
					$this->cms->tbname['plugin_shop_lookup_prod_most_wanted'],
					$pid
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * @param $id
	 */
	function del_cache_files($id)
	{
		//Alle Kategorien surls produkte_lang_kategorie

		$katstate=substr($katstate,0,-2);
		$sql=sprintf("SELECT url_menuname FROM %s
		 							LEFT JOIN %s 
									ON menuid_id=kategorien_menu_id
									LEFT JOIN %s
									ON produkte_kategorie_id =kategorien_id
										
									WHERE produkte_produkt_id='%d' ",
			$this->cms->tbname['papoo_menu_language'],
			$this->cms->tbname['plugin_shop_kategorien'],
			$this->cms->tbname['plugin_shop_lookup_kategorie_prod'],
			$id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$this->diverse->remove_cache_file(basename($value['url_menuname'].""));
			}
		}
		$sql=sprintf("SELECT url_menuname FROM %s
			 							
										WHERE menulinklang LIKE '%s' ",
			$this->cms->tbname['papoo_menu_language'],
			"%papoo_shop_show_new%"
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$this->diverse->remove_cache_file(basename($value['url_menuname'].""));
			}
		}

		//Alle Cross urls
		$sql=sprintf("SELECT * FROM %s
			 							LEFT JOIN %s 
			 							ON produkte_produkt_id=produkte_lang_id
										WHERE produkte_cross_produkt_id='%d'",
			$this->cms->tbname['plugin_shop_lookup_prod_cross'],
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$id

		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$this->diverse->remove_cache_file(basename($value['produkte_lang_produkt_surl']));
			}
		}
	}

	/**
	 * shop_class_fakturierung::shop_insert_new_produkt_daten_fuer_beleg()
	 * Die Funktion geht die Produkte in einer Schleife durch
	 * und speichert diese direkt ab
	 * `produkte_order_id` int(11) NOT NULL ,
	 * `produkte_order_produkt_id` text NULL ,
	 * `produkte_order_produktname` text NULL,
	 * `produkte_order_produktdescription` text NULL,
	 * `produkte_order_produkt_netto_price` varchar(255) NOT NULL ,
	 * `produkte_order_produkt_brutto_price` varchar(255) NOT NULL ,
	 * `produkte_order_produkt_anzahl` varchar(255) NOT NULL ,
	 * `produkte_order_produkt_summe_netto` varchar(255) NOT NULL ,
	 * `produkte_order_produkt_rabatt` varchar(255) NOT NULL
	 *
	 * @param string $insert_id
	 * @return void
	 */
	function shop_insert_new_produkt_daten_fuer_beleg_frontend($insert_id="xy")
	{
		//Produkte vorab durchgehen und Menge checken
		foreach ($_SESSION['shop_cart'] as $item) {
			//Überprüfen wenn Bestandsführung genutzt
			if (!$this->check_bestand_produkt_nach_kauf($item['produkt_id'],$item['produkte_anzahl'])) {
				//Zurück zur Seite 1
				$this->reload("warenkorb_aktu","");
			};

			//Die Variationen überprüfen
			if (is_array($item['variationen'])) {
				foreach ($item['variationen'] as $key=>$value) {
					//Variationslagerhaltung umsetzen
					if ($value['produkt_lagerhaltung']==1) {
						if (!$this->check_bastand_variationen_beim_kauf($value['produkt_variations_id'],$value['produkt_id'],$item['produkte_anzahl'])) {
							//Zurück zur Seite 1
							$this->reload("warenkorb_aktu","");
						};
					}
				}
			}
		}

		//Produkte durchgehen
		foreach ($_SESSION['shop_cart'] as $item) {
			//Die Id des Beleges
			$this->checked->produkte_order_id=$insert_id;

			//Daten in Most Wanted Tabelle eintragen
			$this->insert_in_most_wanted($insert_id);

			//Die Id des Produktes
			$this->checked->produkte_order_produkt_id=$item['produkt_id'];

			//Wenn ein Download Produkt dann eintragen
			$this->insert_lookup_download($insert_id,$item['produkt_id']);

			//Falls Gruppenrechte dadurch entstehen, diese auch eintragen
			$this->insert_lookup_gruppenrechte($item['produkt_id'],$insert_id);

			//Eintragen wenn Bestandsführung genutzt wird
			$this->make_bestand_produkt_nach_kauf($item['produkt_id'],$item['produkte_anzahl']);

			$this->checked->produkte_order_steuerklasse=$item['produkte_lang_steuerklasse'];

			//Der Produktname
			$this->checked->produkte_order_produktname=$item['produkte_lang_produktename'];

			//Die Variationen
			$variationen="";
			if (is_array($item['variationen'])) {
				foreach ($item['variationen'] as $key=>$value) {
					$variationen.="\n".$value['Typ'].": ".$value['wert']."";

					//Bestandsführung durchführen
					$this->make_bastand_variationen_beim_kauf($value['produkt_variations_id'],$value['produkt_id'],$item['produkte_anzahl']);
				}
			}
			//Bei Losverkauf hier dann die Nummern erstellen
			if (!empty($this->cms->tbname['lose_losetabelle'])) {
				if (file_exists(PAPOO_ABS_PFAD."/plugins/lose/lib/loseplugin_class.php")) {
					require_once(PAPOO_ABS_PFAD."/plugins/lose/lib/loseplugin_class.php");
					$loseplugin = new loseplugin_class();
					$lose=$loseplugin->get_nummern_lose($item['produkt_id'],$item['produkte_anzahl']);
					$item['produkte_lang_produkt_beschreibung']=$item['produkte_lang_produkt_beschreibung']." ".$lose;
				}
			}

			//Die BEschreibung
			$this->checked->produkte_order_produktdescription=$item['produkte_lang_produkt_beschreibung'].$variationen;
			//Netto Preis eines Produktes
			$singel_netto=$this->checked->produkte_order_produkt_netto_price=$this->diverse->dround2($this->get_netto_preis($item['produkte_preis'],$item['produkte_lang_steuerklasse']));
			$this->checked->produkte_order_produkt_netto_price_org=$item['produkte_preis_org_inkl_var_netto'];
			$this->checked->produkte_order_produkt_rabatt=$item['produkte_rabatt'];
			//Anzahl der Produkte
			$p_anzahl=$this->checked->produkte_order_produkt_anzahl=$item['produkte_anzahl'];
			//Netto Summe aus Anzahl und Preis
			$snetto=$this->checked->produkte_order_produkt_summe_netto=$this->diverse->dround2($this->get_netto_preis($item['produkte_summe'],$item['produkte_lang_steuerklasse']));

			$snetto=str_replace(",",".",$snetto);
			$p_anzahl=str_replace(",",".",$p_anzahl);
			$singel_netto=str_replace(",",".",$singel_netto);
			if ($p_anzahl<=0) {
				continue;
			}
			if (($snetto/$p_anzahl)!=$singel_netto) {
				$singel_netto=$snetto/$p_anzahl;
				#$singel_netto=str_replace(".",",",$singel_netto);
				$this->checked->produkte_order_produkt_netto_price=$singel_netto;
			}
			$this->checked->produkte_order_sortier_id++;
			//Brutto Preis
			$item['produkte_summe']=str_ireplace(" ","",$item['produkte_summe']);
			$item['produkte_summe']=str_ireplace(",",".",$item['produkte_summe']);
			$this->checked->produkte_order_produkt_brutto_price=$this->diverse->dround2($item['produkte_summe']);
			//Und jetzt eintragen
			$xsql['dbname'] = "plugin_shop_order_lookup_produkte";
			$xsql['praefix'] = "produkte_order";
			$xsql['must'] = array("produkte_order_id");
			$shop=new shop_class();
			$dat=$shop->insert_new_eintrag_in_shop_db($xsql);
		}
		$this->mail_produkt_ausverkauft_und_mindestmenge();
	}

	function mail_produkt_ausverkauft_und_mindestmenge()
	{
		//Mailen der Mindestmenge
		$mailing = new shop_class_mailing();
		$mailing->do_mail_beleg_mindestmenge($this->produkt_unter_mindestmenge);

		//Mailen der ausverkauften
		$mailing->do_mail_beleg_ausverkauft($this->produkt_ausverkauft);
	}

	function check_ob_shop_gruppen_rechte_abgelaufen()
	{
		//Alle userids rausholen die abgelaufen sind inkl. USerdaten
		$sql=sprintf("SELECT * FROM %s
									LEFT JOIN %s ON shop_gruppen_user_id = extended_user_id 
									WHERE 
									shop_gruppen_ablaufzeitpunkt < '%d'
									AND shop_gruppen_ablaufzeitpunkt>'1'
									",
			$this->cms->tbname['plugin_shop_lookup_user_kunden_gruppen'],
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			time()


		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			//Mail Klasse Ini
			$mailing = new shop_class_mailing();

			foreach ($result as $key=>$value) {

				//Mailen und Daten übergeben, nur erster Eintrag
				$mailing->do_mail_gruppenrechte_abgelaufen($value);

				//Diesen Eintrag aus der DB entfernen
				$sql=sprintf("DELETE  FROM %s
												WHERE shop_gruppen_user_id='%d'
												AND shop_gruppen_gruppe_id='%d'",
					$this->cms->tbname['plugin_shop_lookup_user_kunden_gruppen'],
					$value['shop_gruppen_user_id'],
					$value['shop_gruppen_gruppe_id']

				);
				$this->db->query($sql);

				//Zuerst die Papoo Rechtegruppen rausholen
				$sql=sprintf("SELECT shop_gruppen_papoo_gruppen_id FROM %s
											WHERE shop_gruppen_id='%d'",
					$this->cms->tbname['plugin_shop_lookup_gruppen_ppgruppen'],
					$value['shop_gruppen_gruppe_id']
				);
				$result_pp=$this->db->get_results($sql,ARRAY_A);

				if (is_array($result_pp)) {
					foreach ($result_pp as $keypp=>$valuepp) {
						//Dazugehörigen Gruppenrechte aus Lookup entfernen
						$sql=sprintf("DELETE  FROM %s
														WHERE userid='%d'
														AND gruppenid='%d'",
							$this->cms->tbname['papoo_lookup_ug'],
							$valuepp['shop_gruppen_papoo_gruppen_id'],
							$value['shop_gruppen_user_id']
						);
						$this->db->query($sql);
					}
				}
			}
		}
	}

	/**
	 * shop_class_checkout::insert_lookup_gruppenrechte()
	 * Wenn durch ein Produkt Gruppenrechte entstehen, diese eintragen
	 *
	 * @param $produkt_id
	 * @param $beleg_id
	 * @return void
	 */
	function insert_lookup_gruppenrechte($produkt_id,$beleg_id)
	{
		$this->check_ob_shop_gruppen_rechte_abgelaufen();

		$sql=sprintf("SELECT * FROM %s WHERE
									produkte_lang_id='%d'
									AND produkte_lang_gruppen_rechte_produkt>='1'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$produkt_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		$sql=sprintf("SELECT extended_user_id FROM %s WHERE
									extended_user_user_id='%d'",
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			$this->user->userid
		);
		$ext_userid=$this->db->get_var($sql);

		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				$rechte=explode(";",$value['produkte_lang_gruppen_rechte_produkt']);
				$zeit=$value['produkte_lang_zeitraum_gruppenrechte']*24*60*60+time();

				//Dann die Rechte eintragen
				if (is_array($rechte)) {
					foreach ($rechte as $key=>$value) {
						$xsql['dbname'] = "plugin_shop_lookup_user_kunden_gruppen";
						$xsql['praefix'] = "shop_gruppen";
						$this->checked->shop_gruppen_gruppe_id=$value;
						$this->checked->shop_gruppen_user_id=$ext_userid;
						$this->checked->shop_gruppen_ablaufzeitpunkt=$zeit;
						$this->checked->shop_gruppen_beleg_id=$beleg_id;
						$this->shop->insert_new_eintrag_in_shop_db( $xsql );
					}
				}
			}
		}
	}

	/**
	 * shop_class_checkout::insert_lookup_download()
	 *
	 * @param $beleg_id
	 * @param $produkt_id
	 * @return void
	 */
	function insert_lookup_download($beleg_id,$produkt_id)
	{
		$sql=sprintf("SELECT * FROM %s WHERE
									produkte_lang_id='%d'
									AND produkte_lang_download_datei>'1'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$produkt_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		//ALso nur wenn ein Downloadprodukt
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				//Alte Einträge des Users löschen
				$sql=sprintf("DELETE  FROM %s
												WHERE download_user_id='%d' 
												AND
												download_produkt_id='%d'",
					$this->cms->tbname['plugin_shop_lookup_downloads'],
					$this->user->userid,
					$produkt_id
				);
				$this->db->query($sql);
				$value['produkte_lang_max_zeit_downloads']=$value['produkte_lang_max_zeit_downloads']*24*60*60+time();
				//Neu eintragen
				$sql=sprintf("INSERT INTO %s 
											SET 
											download_user_id='%d',
											download_produkt_id='%d',
											download_beleg_id='%d',
											download_time_max='%d',
											download_count_max='%d'
											",
					$this->cms->tbname['plugin_shop_lookup_downloads'],
					$this->user->userid,
					$produkt_id,
					$beleg_id,
					$value['produkte_lang_max_zeit_downloads'],
					$value['produkte_lang_max_anzahl_von_downloads']
				);
				$this->db->query($sql);
			}
		}
		$this->trigger_abgelaufene_downloads_und_zugriffe();
	}

	/**
	 * shop_class_checkout::trigger_abgelaufene()
	 *
	 * Diese Funktion liest aus welche Downloads und Zugriffe abgelaufen sind
	 * und mailt die User an wenn das Häkchen gesetzt ist im Produkt.
	 *
	 *
	 * @return void
	 */
	function trigger_abgelaufene_downloads_und_zugriffe()
	{
		//Userdaten rausholen über SQL JOIN
		$sql=sprintf("SELECT * FROM %s
									LEFT JOIN %s ON download_produkt_id =produkte_lang_id
									LEFT JOIN %s ON download_user_id = extended_user_user_id 
									WHERE 
									download_time_max < '%d'
									AND produkte_lang_mail_an_kunden_wenn_download_abgelaufen='1'
									AND produkte_lang_lang_id='%d'
									AND download_mail_is_send<>'1'
									OR download_count_max <'1'
									AND download_time_max < '1'
									AND produkte_lang_mail_an_kunden_wenn_download_abgelaufen='1'
									AND produkte_lang_lang_id='%d'
									AND download_mail_is_send<>'1'",
			$this->cms->tbname['plugin_shop_lookup_downloads'],
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			time(),
			$this->cms->lang_id,
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		if (is_array($result)) {
			//Mail Klasse Ini
			$mailing = new shop_class_mailing();

			foreach ($result as $key=>$value) {
				//Mailen und Daten übergeben
				$mailing->do_mail_download_abgelaufen($value);

				//Mail auf gesendet stellen
				$sql=sprintf("UPDATE %s SET download_mail_is_send='1'
											WHERE download_user_id ='%d'
											AND download_produkt_id='%d'",
					$this->cms->tbname['plugin_shop_lookup_downloads'],
					$value['download_user_id'],
					$value['download_produkt_id']
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * shop_class_checkout::get_netto_preis()
	 *
	 * @param integer $brutto
	 * @param integer $steuerid
	 * @return void
	 */
	function get_netto_preis($brutto=0,$steuerid=0)
	{
		if (is_numeric($_SESSION['shop_settings']['user_land_id'])) {
			$this->user_land_id=$_SESSION['shop_settings']['user_land_id'];
		}
		$this->user_land_id;

		$steuer = new shop_class_steuer();
		$this->die_steuer=$steuer->shop_get_shop_steuer($this->user_land_id);

		$brutto=str_ireplace(" ","",$brutto);
		$brutto=str_ireplace(",",".",$brutto);

		if (is_array($this->die_steuer)) {
			foreach ($this->die_steuer as $dat) {
				if ($dat['steuer_lang_id']==$steuerid) {
					$steuer_satz=$dat['steuer_lang_ert_teuersatz'];
				}
			}
		}
		$p1=$this->diverse->dround($brutto/(100+$steuer_satz)*100);
		return $p1;
	}

	/**
	 * shop_class_checkout::shop_get_order_nummer()
	 *
	 * @return int|null
	 */
	function shop_get_order_nummer()
	{
		$sql=sprintf("SELECT belegart_lang_ummernkreis_der_elege_ FROM %s WHERE belegart_lang_id='%d'",
			$this->cms->tbname['plugin_shop_belegarten_lang'],
			$this->checked->order_typ
		);
		$typ=$this->db->get_var($sql);
		$typ=$typ+1;
		$sql=sprintf("UPDATE %s SET belegart_lang_ummernkreis_der_elege_='%d' WHERE belegart_lang_id='%d' ",
			$this->cms->tbname['plugin_shop_belegarten_lang'],
			$typ,
			$this->checked->order_typ
		);
		$this->db->query($sql);

		//Sonderfall Re RE Liefer weil beide gleichen Kreis haben.
		if ($this->checked->order_typ==6) {
			$sql=sprintf("UPDATE %s SET belegart_lang_ummernkreis_der_elege_='%d' WHERE belegart_lang_id='%d' ",
				$this->cms->tbname['plugin_shop_belegarten_lang'],
				$typ,
				7
			);
			$this->db->query($sql);
		}
		if ($this->checked->order_typ==7) {
			$sql=sprintf("UPDATE %s SET belegart_lang_ummernkreis_der_elege_='%d' WHERE belegart_lang_id='%d' ",
				$this->cms->tbname['plugin_shop_belegarten_lang'],
				$typ,
				6
			);
			$this->db->query($sql);
		}

		return $typ;
	}

	/**
	 * @return array|null
	 */
	function shop_get_order_typ_frontend()
	{
		$sql=sprintf("SELECT einstellungen_lang_tandardbeleg_im_rontend FROM %s",
			$this->cms->tbname['plugin_shop_daten']
		);
		$typ=$this->db->get_var($sql);
		return $typ;
	}

	/**
	 * shop_class_checkout::shop_insert_new_beleg_basisdaten()
	 * Hier wird die tatsächliche Anweisung gegeben die Basisdaten des
	 * Beleges zu speichern
	 * @return void
	 */
	function shop_insert_new_beleg_basisdaten_frontend()
	{
		$xsql['dbname'] = "plugin_shop_order";
		$xsql['praefix'] = "order";
		$xsql['must'] = array("order_summe_netto");
		$xsql['not_praefix']= "kunden_order";
		//Belegtyp zuweisen
		if (empty($this->checked->order_typ)) {
			$this->checked->order_typ=$this->shop_get_order_typ_frontend();
		}

		$this->checked->order_order_number=$this->shop_get_order_nummer();
		$this->checked->order_summe_netto=str_replace(" ","",$this->checked->order_summe_netto);
		$shop=new shop_class();
		$dat=$shop->insert_new_eintrag_in_shop_db($xsql);
		if ($dat['insert_id']) {
			$this->content->template['belegid'] = $dat['insert_id'];
			$this->content->template['belegnr'] = $order_number;
		}
		return $dat['insert_id'];
	}

	/**
	 * shop_class_checkout::shop_insert_new_userdaten_beleg_frontend()
	 * Hier wird die tatsächliche Anweisung gegeben die Basisdaten des
	 * USers dieses Beleges zu speichern
	 *
	 * @param $insert_id
	 * @return void
	 */
	function shop_insert_new_userdaten_beleg_frontend($insert_id)
	{
		$this->checked->kunden_order_id=$insert_id;
		$xsql['dbname'] = "plugin_shop_order_lookup_kunde";
		$xsql['praefix'] = "kunden_order";
		$xsql['must'] = array("kunden_order_name");
		$shop=new shop_class();
		$dat=$shop->insert_new_eintrag_in_shop_db($xsql);
	}

	function shop_save_bestellung()
	{
		//Die Bestellung nochmals rausholen.
		#$warenkorb=new shop_class_warenkorb();
		#$warenkorb->shop_show_warenkorb();
		//Warenkorb $_SESSION['shop_cart']

		//Gutschein Code auf benutzt setzen
		if (!empty($_SESSION['shop_gutschein_code'])) {
			$sql=sprintf("UPDATE %s SET gutschein_code_used='1'
													WHERE 	gutschein_code_code='%s'",
				$this->cms->tbname['plugin_shop_gutschein_codes'],
				$this->db->escape($_SESSION['shop_gutschein_code'])
			);
			$this->db->query($sql);
		}

		$this->shop_fakturierung_create_basisdaten_frontend();

		//Dann die Belegbasisdaten eintragen
		$insert_id=$this->shop_insert_new_beleg_basisdaten_frontend();

		//Die Rechnungsdaten rausholen
		#$this->content->template['show_nur_daten']="ok";
		$this->shop_get_user_daten_schritt_repers();
		#print_r($this->user_Daten_bestellung);

		//Dann die Beleg Userdaten vorbereiten
		$this->shop_fakturierung_create_user_beleg_daten_frontend();

		//Wenn als bezahlt markiert, dann Beleg auch so updaten
		$this->update_beleg_als_bezahlt($this->beleg_is_bezahlt,$insert_id);

		//Dann die Beleg USerdaten eintragen
		$this->shop_insert_new_userdaten_beleg_frontend($insert_id);

		//Dann die Produktdaten vorbereiten, direkt eintragen wg. der Schleife
		$this->shop_insert_new_produkt_daten_fuer_beleg_frontend($insert_id);

		//Die Zahlungsinformationen rausholen
		$this->shop_get_aktive_payments();

		return $insert_id;
	}

	/**
	 * shop_class_checkout::update_beleg_als_bezahlt()
	 * Markiert den gerade erstellten Beleg als bezahlt
	 *
	 * @param $is_bezahlt
	 * @param $insert_id
	 * @return void
	 */
	function update_beleg_als_bezahlt($is_bezahlt,$insert_id)
	{
		if ($is_bezahlt==1) {
			//Daten rausholen und ausgeben}
			$sql=sprintf("UPDATE %s SET order_is_payd='1' 
											WHERE order_id='%d'",
				$this->cms->tbname['plugin_shop_order'],
				$insert_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}
	}

	function shop_unset_data_beleg_frontend()
	{
		unset($_SESSION['shop_cart']);
		unset($_SESSION['produkt_variationen']);
		unset($_SESSION['ext_user_daten']);
		unset($_SESSION['produkt_variationen']);
		unset($_SESSION['shopping_cart_zwsumme']);
		unset($_SESSION['shoppping_cart_zwischensumme_org']);
		unset($_SESSION['steuer_anteil']);
		unset($_SESSION['produkt_variationen']);
		unset($_SESSION['die_jeweiligen_produktvarationen']);
		unset($_SESSION['shop_front_produkt_anzahl']);
		unset($_SESSION['show_liefer_versandarten']);
		$this->checked->produkt_id="";
		$this->checked->einstellungen_ersandart_auswhlen="";
		$this->checked->einstellungen_ezahlart_auswhlenadmin="";
	}

	function shop_frontend_make_checkout_bestaetigen()
	{
		//Überprüfen ob gerade ein angemeldeter User da ist und ob der auch Shop Kunden ist
		$user_status=$this->shop_get_user_status();

		//Noch kein Shopkunde, dann auf Erste Seite lenken
		if (!is_numeric($user_status)) {
			$this->reload("checkout_rl","");
		};

		//Lieferdaten rausholen
		$this->content->template['show_liefer_versandarten']=$this->get_shop_liefer_versand_arten();

		//Ok, hier gehts weiter wenn Checkout fertig ist
		if (!empty($this->checked->checkout_bitte_bestaetigen_dass_agb_gelsen)
			&& !empty($this->checked->checkout_widerruf)
			&& !empty($this->checked->checkout_widerrufdigital)
		) {
			//Seite neu laden -> alles fertig.
			$this->reload("checkout_fertig","");
		}
		else {
			if (!empty($this->checked->formSubmit_ext_user_front_bestaetigen)) {
				//Fehlermeldung
				if (empty($this->checked->checkout_bitte_bestaetigen_dass_agb_gelsen)) {
					$this->content->template['is_agb_no']="no";
				}

				if (empty($this->checked->checkout_widerruf)) {
					$this->content->template['is_wid_no']="no";
				}

				if (empty($this->checked->checkout_widerrufdigital)) {
					$this->content->template['is_wid_dig_no']="no";
				}
			}
		}

		//AGB Daten rausholen
		$shop_settings = new shop_class_settings();
		$shop_settings->shop_get_system_settings_fuer_form();

		if ($this->cms->lang_id!="1") {
			$shop_settings->shop_settings_daten[0]['einstellungen_lang__ext']=$shop_settings->shop_settings_daten[0]['einstellungen_lang__ext_en'];
		}

		$this->content->template['checkout_ie_agbs_area']="nobr:".$shop_settings->shop_settings_daten[0]['einstellungen_lang__ext'];

		$this->content->template['ps_act']=$this->checked->ps_act;

		//Die Länderdaten
		$steuer = new shop_class_steuer();
		$this->laender_liste=$steuer->shop_get_laender();

		//Die Rechnungsdaten rausholen
		$this->content->template['show_nur_daten']="ok";
		$bereich=array("2");
		$this->shop_get_user_daten_schritt_repers($bereich);
		$this->content->template['shop_re_Daten_form']=$this->content->template['show_nur_daten_die_daten'];

		//Die Lieferdaten rausholen
		$bereich=array("3");
		$this->shop_get_user_daten_schritt_repers($bereich);
		$this->content->template['shop_liefer_Daten_form']=$this->content->template['show_nur_daten_die_daten'];

		//Die Versandart rausholen
		//FEHLT NOCH

		//Die Zahlungsinformationen rausholen
		$this->shop_get_aktive_payments();
		$this->content->template['shop_bezahl_daten']=$this->content->template['show_nur_daten_die_daten_bezahl'];

		//Die Bestellung nochmals rausholen.
		$warenkorb=new shop_class_warenkorb();
		$warenkorb->user_Daten_bestellung=$this->user_Daten_bestellung;
		$warenkorb->shop_show_warenkorb();

	}

	/**
	 * shop_class_checkout::shop_get_payment_daten_alt_des_kunden()
	 * Hier werden die alten Paymentdaten des Kunden rausgeholt
	 *
	 * @return void
	 */
	function shop_get_payment_daten_alt_des_kunden()
	{

	}

	/**
	 * shop_class_checkout::shop_speichere_bezahldaten_des_kunden()
	 *
	 * Hier werden nun die aktuellen Bezahldaten des Kunden gespeichert
	 * Eigentlich muß das in der Session noch mitlaufen, damit sich das nicht
	 * überschneidet...
	 *
	 * @return void
	 */
	function shop_speichere_bezahldaten_des_kunden()
	{
		//extended_user_bezahl_art_letzt_bestellung
		$xsql['dbname'] = "plugin_shop_crm_extended_user";
		$xsql['praefix'] = "extended_user";
		$xsql['must'] =$must_felder;
		$xsql['where_name'] = "extended_user_user_id";
		$orgid=$this->checked->extended_user_user_id=$this->user->userid;
		$orgid=$this->checked->extended_user_bezahl_art_letzt_bestellung=$this->checked->payment_art;
		#$xsql['must'] = array("produkte_lang_internername");
		$is_drin = $this->shop->update_eintrag_in_shop_db($xsql);
		$_SESSION['shop_bezahl_art']=$this->checked->payment_art;
	}

	function shop_frontend_make_checkout_zahlart()
	{
		//Überprüfen ob gerade ein angemeldeter User da ist und ob der auch Shop Kunden ist
		$user_status=$this->shop_get_user_status();

		//Noch kein Shopkunde, dann auf Erste Seite lenken
		if (!is_numeric($user_status)) {
			$this->reload("checkout_rl","");
		}

		$this->content->template['plugin_error']=$_SESSION['plugin_error'];
		unset($_SESSION['plugin_error']);

		//Daten ans Template
		$this->content->template['checkout'][0]['checkout_ontonummer']=$_SESSION['shop_checked_lastschrift']['checkout_ontonummer'];
		$this->content->template['checkout'][0]['checkout_ankleitzahl']=$_SESSION['shop_checked_lastschrift']['checkout_ankleitzahl'];
		$this->content->template['checkout'][0]['checkout_ame_der_ank']=$_SESSION['shop_checked_lastschrift']['checkout_ame_der_ank'];
		$this->content->template['checkout'][0]['checkout_ame_des_ontoinhabers']=$_SESSION['shop_checked_lastschrift']['checkout_ame_des_ontoinhabers'];


		if (!empty($this->checked->formSubmit_ext_user_front_zahl)) {
			//Eine Zahlmethode wurde ausgewählt, ansonsten nix machen
			if (!empty($this->checked->payment_art)) {
				//hier dann spezielle checks
				if ($this->check_lastschrift_daten()) {
					//Wenn ok dann erstmal speichern
					$this->shop_speichere_bezahldaten_des_kunden();
					//Daten übergeben an neue Seite mit Zusammenfassung
					//Reload
					$this->reload("checkout_bestaetigen","");
				}
				else {
					$_SESSION['shop_bezahl_art']=$this->checked->payment_art;
					$this->reload("checkout_zahl","");
				}
			}
			else {
				$this->content->template['plugin_error']['payment']="paymentart";
			}
		}
		//Liste der Zahlarten rausholen
		$this->content->template['shop_payment_arten']=$this->shop_get_aktive_payments();
		//Hier evtl. Daten aus ältern Payments rausholen, z.B. Kontodaten...
		$this->shop_get_payment_daten_alt_des_kunden();
	}

	/**
	 * @return bool
	 */
	function check_lastschrift_daten()
	{
		$_SESSION['shop_checked_lastschrift']['checkout_ontonummer']=$this->checked->checkout_ontonummer;
		$_SESSION['shop_checked_lastschrift']['checkout_ankleitzahl']=$this->checked->checkout_ankleitzahl;
		$_SESSION['shop_checked_lastschrift']['checkout_ame_der_ank']=$this->checked->checkout_ame_der_ank;
		$_SESSION['shop_checked_lastschrift']['checkout_ame_des_ontoinhabers']=$this->checked->checkout_ame_des_ontoinhabers;
		//Wenn Lastschrift
		if ($this->checked->payment_art==3) {
			require_once PAPOO_ABS_PFAD."/plugins/papoo_shop/lib/php-iban.php";
			//IBAN checken...
			if(!verify_iban($this->checked->checkout_ontonummer)) {
				$this->content->template['plugin_error']['checkout_ontonummer']=$this->content->template['plugin_checkout_itte_korrigieren'];
				$_SESSION['plugin_error']=$this->content->template['plugin_error'];
				return false;
			}
			// exit("YES");
			$_SESSION['plugin_error']=$this->content->template['plugin_error'];
			return true;
			/**require_once PAPOO_ABS_PFAD."/plugins/papoo_shop/lib/shop_class_konto_validierung.php";
			$BankCheck=new cpp_dd_de_check();

			$ok=$BankCheck->cpp_SelectMethodeAndCalculate($this->checked->checkout_ontonummer,$this->checked->checkout_ankleitzahl);
			if (!$ok>0)
			{
			$_SESSION['shop_checked_lastschrift']['extended_user_konto_verifiziert']=1;
			}

			//Kontonummer
			if (!is_numeric($this->checked->checkout_ontonummer))
			{
			$this->content->template['plugin_error']['checkout_ontonummer']=$this->content->template['plugin_checkout_itte_korrigieren'];

			}

			//BLZ
			if (!is_numeric($this->checked->checkout_ankleitzahl) || strlen($this->checked->checkout_ankleitzahl)!=8)
			{
			$this->content->template['plugin_error']['checkout_ankleitzahl']=$this->content->template['plugin_checkout_itte_korrigieren'];
			}

			//Name der Bank
			if (empty($this->checked->checkout_ame_der_ank))
			{
			$this->content->template['plugin_error']['checkout_ame_der_ank']=$this->content->template['plugin_checkout_itte_korrigieren'];
			}

			//Name des Kontoinhabers
			if (empty($this->checked->checkout_ame_des_ontoinhabers))
			{
			$this->content->template['plugin_error']['checkout_ame_des_ontoinhabers']=$this->content->template['plugin_checkout_itte_korrigieren'];
			}

			$_SESSION['plugin_error']=$this->content->template['plugin_error'];

			if (is_array($this->content->template['plugin_error']))
			{
			return false;
			}
			else
			{
			return true;
			}

			 **/
		}
		return true;
	}

	/**
	 * shop_class_checkout::shop_get_aktive_payments()
	 *
	 * @return array|void
	 */
	function shop_get_aktive_payments()
	{
		$sql=sprintf("SELECT * FROM %s
											WHERE shop_payment_ist_Paymentart_aktiv='1'
											AND shop_payment_paymenart_im_frontend='1'
											AND shop_payment_lang_id='%d'
											ORDER BY shop_payment_Orderid_des_Payments ASC",
			$this->cms->tbname['plugin_shop_daten_payment_arten'],
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (is_array($result)) {
			foreach($result as $dat) {
				if ($_SESSION['shop_bezahl_art']==$dat['shop_payment_id']) {
					$this->content->template['show_nur_daten_die_daten_bezahl'][]=$dat['shop_payment_Name_des_Payments'];
				}
			}
		}

		//Aktiv aus Session setzen
		$this->content->template['shop_aktive_bezahlmethode']=$_SESSION['shop_bezahl_art'];
		return $result;
	}

	/**
	 * shop_class_checkout::shop_get_pflicht_felder_fuer_ext_user()
	 * Hier werden die Pflichtfelder bestimmt anhand denen die neuen Einträge
	 * kontrolliert werden
	 *
	 * @param array $bereich
	 * @param string $pflicht
	 * @return array
	 */
	function shop_get_pflicht_felder_fuer_ext_user($bereich=array(),$pflicht="1")
	{
		if (count($bereich)==0) {
			$bereich=array("1","2","6","7");
		}

		$sql=sprintf("SELECT DISTINCT 
										prospect_lang_namedeskundenfeldes,
										prospect_lang_inwelcembereich 
										FROM %s 
										WHERE prospect_lang_Sollesimfrontendpflichtsein LIKE '%s' 
										AND prospect_lang_istaktivkundenfeld='1' 
										AND prospect_lang_lang_id='%d' 
										
										GROUP BY prospect_lang_namedeskundenfeldes
										ORDER BY prospect_lang_ser_order_id",
			$this->cms->tbname['plugin_shop_kunden_felder_lang'],
			$pflicht,
			$this->cms->lang_id
		);
		$result= $this->db->get_results($sql,ARRAY_A);
		$must=array();
		foreach ($result as $dat) {

			if (in_array($dat['prospect_lang_inwelcembereich'],$bereich)) {
				$must[]=$dat['prospect_lang_namedeskundenfeldes'];
			}
		}
		return $must;
	}

	function shop_get_user_liefer_status()
	{

	}


	function shop_frontend_make_checkout_liefer()
	{
		//Überprüfen ob gerade ein angemeldeter User da ist und ob der auch Shop Kunden ist
		$user_status=$this->shop_get_user_status();

		//Noch kein Shopkunde, dann auf Erste Seite lenken
		if (!is_numeric($user_status)) {
			$this->reload("checkout_rl","");
		};

		if ($_SESSION['shop_show_login']==1) {
			$this->content->template['show_danke_shop']="OK";
			unset($_SESSION['shop_show_login']);
		}
		else {
			if (empty($_SESSION['shop_cart']) && empty($this->checked->show_login)) {

				$this->reload("warenkorb_aktu","");
			}
		}
		//Überprüfen ob der User schon eine Lieferadresse hat
		#$liefer_status=$this->shop_get_user_liefer_status();

		//Wenn übermittelt
		if (!empty($this->checked->formSubmit_ext_user_front_liefer)) {
			$bereich=array("3");
			$must_felder=$this->shop_get_pflicht_felder_fuer_ext_user($bereich);

			$this->make_checked_felder_null(3);

			if (is_numeric($this->checked->extuser_id)) {
				//UPDATE
				$xsql['dbname'] = "plugin_shop_crm_extended_user";
				$xsql['praefix'] = "extended_user";
				$xsql['must'] =$must_felder;
				$xsql['where_name'] = "extended_user_user_id";
				$orgid=$this->checked->extended_user_user_id=$this->user->userid;
				#$xsql['must'] = array("produkte_lang_internername");
				$is_drin = $this->shop->update_eintrag_in_shop_db($xsql);
			}

			//Wenn keine Versandart ausgewählt
			if (empty($this->checked->extended_user_versandart_der_letzten_bestellung)) {
				$this->content->template['versand_error']="ok";
			}

			//Neu laden
			if ($is_drin && !empty($this->checked->extended_user_versandart_der_letzten_bestellung)) {

				if (is_numeric($this->checked->extuser_id)) {
					$produkt_id=$this->checked->extuser_id;
				}
				//Relaod
				$this->reload("checkout_zahl","");
			}
		}

		if (is_numeric($user_status)) {
			$bereich=array("3");
			$daten=$this->shop_get_user_daten_schritt_repers($bereich);
			//Alternative wenn ändern
			if ( $daten[0]['extended_user_re_gleich_liefer']!=1) {
				$this->content->template['ext_user_daten']=$this->shop_get_user_daten_schritt_repers();
				//Daten für das Formular rausholen
				$this->shop_daten_get_formdat_pers_redat();
				//Setzen auf Anzeige
				$this->content->template['reungleichliefer']="ok";
				//Lieferdaten rausholen
				$this->content->template['show_liefer_versandarten']=$this->get_shop_liefer_versand_arten();
			}
			else {
				//Lieferdaten rausholen
				$this->content->template['show_liefer_versandarten']=$this->get_shop_liefer_versand_arten();
			}
			$_SESSION['show_liefer_versandarten']=$this->content->template['show_liefer_versandarten'];
		}
	}

	/**
	 * shop_class_checkout::get_shop_liefer_versand_arten()
	 * Hier werden die Versandkosten pro Produkt errechnet... alles im Bruttomodus.
	 * @return void
	 */
	private function get_shop_liefer_versand_arten()
	{
		if (is_array($_SESSION['shop_cart'])) {
			foreach ($_SESSION['shop_cart'] as $key=>$value) {
				//Erstmal Versanddaten rausholen
				$versand_art=$this->get_versand_art_pro_produkt($value['produkt_id']);
				//Dann Kosten errechnen
				$kosten=$this->get_versand_kosten($versand_art,$value);
			}
		}

		//Jetzt die Kosten durchgehen
		if (is_array($this->versand_kosten_array)) {
			foreach ($this->versand_kosten_array as $key=>$value) {
				$gesamt_kosten=0;
				//Produkte durchgehen
				if (is_array($value)) {
					foreach ($value as $key2=>$value2)
					{
						$value2['kosten']=str_ireplace(",",".",$value2['kosten']);
						if ($value2['additiv']==1)
						{
							$gesamt_kosten+=$value2['kosten'];
						}
						else
						{
							$gesamt_kosten=$value2['kosten'];
						}
					}
				}
				//das sind dann die Gesamtkosten pro Versand - immer additiv...
				$alle_kosten+=$gesamt_kosten;;
			}
		}

		$versand[0]['versand_arten_id']=999;
		$versand[0]['versand_arten_Kosten_der_Versandart']=number_format($alle_kosten,2,",",".");
		$versand[0]['versand_arten_Daten_zur_Versandart']=$this->content->template['komplettversand'];
		return $versand;
	}

	/**
	 * shop_class_checkout::get_versand_kosten()
	 * Versandkosten errechnen pro Produkt
	 *
	 * @param $versand_art
	 * @param $value
	 * @return void
	 */
	private function get_versand_kosten($versand_art,$value)
	{
		switch ($versand_art['produkte_lang_versandart']) {
			//Download - keine Kosten
		case "1":
			//Kosten aus Produkt & additiv
			$this->versand_kosten_array['1'][$value['produkt_id']]['kosten']=0;
			$this->versand_kosten_array['1'][$value['produkt_id']]['additiv']=0;
			break;

			//Versand nach Umsatz
		case "4":
			//Dann erstmal die Versandartdaten rausholen
			$versand_daten=$this->get_versand_art_daten($versand_art['produkte_lang_versandart']);
			//Kosten errechne
			$kosten=$this->errechner_versandkosten_nach_umsatz($versand_daten,$value);
			//Kosten aus Produkt & additiv
			$this->versand_kosten_array['4'][$value['produkt_id']]['kosten']=$kosten;
			$this->versand_kosten_array['4'][$value['produkt_id']]['additiv']=$versand_art['produkte_lang_versandkosten_additiv'];
			break;

			//Versandkostenfrei
		case "5":
			//Kosten aus Produkt & additiv
			$this->versand_kosten_array['5'][$value['produkt_id']]['kosten']=0;
			$this->versand_kosten_array['5'][$value['produkt_id']]['additiv']=0;
			$this->versand_kosten_array['5'][$value['produkt_id']]['art']="free";
			break;

			//Individuelle Kosten, immer additiv
		case "6":
			//Kosten aus Produkt & additiv
			$this->versand_kosten_array['6'][$value['produkt_id']]['kosten']=$versand_art['produkte_lang_versandkosten'];
			$this->versand_kosten_array['6'][$value['produkt_id']]['additiv']=1;
			break;

		case "7":
			//Dann erstmal die Versandartdaten rausholen
			$versand_daten=$this->get_versand_art_daten($versand_art['produkte_lang_versandart']);
			//Kosten errechne
			$kosten=$this->errechner_versandkosten_nach_gewicht($versand_daten,$value);
			//Kosten aus Produkt & additiv
			$this->versand_kosten_array['7'][$value['produkt_id']]['kosten']=$kosten;
			$this->versand_kosten_array['7'][$value['produkt_id']]['additiv']=$versand_art['produkte_lang_versandkosten_additiv'];
			break;

			//defaullt Kosten nach Umsatz
		default:
			//Dann erstmal die Versandartdaten rausholen
			$daten=$this->get_versand_art_daten($versand_art['produkte_lang_versandart']);
			//Kosten errechnen
			$kosten=$this->errechner_versandkosten_nach_umsatz($versand_daten,$value);
			$this->versand_kosten_array[$versand_art['produkte_lang_versandart']][$value['produkt_id']]['kosten']=$kosten;
			$this->versand_kosten_array[$versand_art['produkte_lang_versandart']][$value['produkt_id']]['additiv']=$versand_art['produkte_lang_versandkosten_additiv'];
			break;
		}
	}

	/**
	 * @param $versand_daten
	 * @param $value
	 * @return int
	 */
	private function errechner_versandkosten_nach_umsatz($versand_daten, $value)
	{
		$lang_krz=array(34=>"de",
			191=>"en",
			146=>"ch",
			126=>"at",
			120=>"nl"
		);
		$lang_krz_eu=array(18,34,191,146,126,120,33,44,45,51,62,66,93,95,101,124,134,145,156,176,196
		);
		//$umsatz=$_SESSION['shoppping_cart_zwischensumme_org'];
		if ($umsatz==0) {
			if (is_array($_SESSION['shop_cart'])) {
				foreach ($_SESSION['shop_cart'] as $xk=>$yv) {
					$umsatz+=$yv['produkte_summe'];
				}
			}
		}

		//Erstmal die Aufsplittung
		$data1=explode("\n",$versand_daten[0]['versand_arten_umsatzgrenzen']);
		if (is_array($data1)) {
			foreach ($data1 as $key=>$value) {
				$data2=explode(";",$value);
				$land=$data2[0];
				#$kosten[$land][$key]['start']=@number_format(str_replace(",",".",$data2['1']),"3",".",".");
				#$kosten[$land][$key]['stop']=@number_format(str_replace(",",".",$data2['2']),"3",".",".");
				#$kosten[$land][$key]['kosten']=@number_format(str_replace(",",".",$data2['3']),"2",".",".");

				if (empty($land)) {
					continue;
				}
				$kosten[$land][$key]['start']=str_replace(",",".",$data2['1']);
				$kosten[$land][$key]['stop']=str_replace(",",".",$data2['2']);
				$kosten[$land][$key]['kosten']=str_replace(",",".",$data2['3']);
			}
		}
		if (!empty($kosten[$lang_krz[$this->user_Daten_bestellung[0]['extended_user_land']]])) {
			$kosten2=$kosten[$lang_krz[$this->user_Daten_bestellung[0]['extended_user_land']]];
		}
		elseif(in_array($this->user_Daten_bestellung[0]['extended_user_land'],$lang_krz_eu)) {
			$kosten2=$kosten['eu'];
		}
		else {
			$kosten2=$kosten['welt'];
		}

		if (empty($kosten2)) {
			$kosten2[0]=5.00;
		}
		$kosten=$kosten2;
		if (is_array($kosten)) {
			foreach ($kosten as $key=>$value) {
				if ($umsatz>$value['start'] && $umsatz < $value['stop']) {
					$kosten_gesamt=$value['kosten'];
				}
			}
		}
		if ($umsatz>0 && empty($kosten)) {
			$kosten_gesamt=2;
		}
		return $kosten_gesamt;
	}

	/**
	 * shop_class_checkout::errechner_versandkosten_nach_gewicht()
	 *
	 * @param mixed $versand_daten
	 * @param mixed $value
	 * @return void
	 */
	private function errechner_versandkosten_nach_gewicht($versand_daten,$value)
	{
		$lang_krz=array(34=>"de",
			191=>"en",
			146=>"ch",
			126=>"at",
			120=>"nl"
		);
		$lang_krz_eu=array(34,191,146,126,120,33,44,45,51,62,66,93,95,101,124,134,145,156,176,196
		);
		//Gewicht rausholen (ja das Feld heißt komisch...)
		$sql=sprintf("SELECT produkte_lang_gewicht FROM %s
								WHERE produkte_lang_id='%d'
								AND produkte_lang_lang_id='%d'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($value['produkt_id']),
			$this->db->escape($this->cms->lang_id)
		);
		$gewicht=$this->db->get_var($sql)*$value['produkte_anzahl'];

		//Spezial Laenggast
		if (($value['variationen'][0]['Typ']=="Gewicht")) {
			$gewicht = str_ireplace("g","",$value['variationen'][0]['wert'])*$value['produkte_anzahl'];
		}
		//Erstmal die Aufsplittung
		$data1=explode("\n",$versand_daten[0]['versand_arten_versandkosten_gewicht']);
		if (is_array($data1)) {
			foreach ($data1 as $key=>$value) {
				$data2=explode(";",$value);
				$land=$data2[0];
				$kosten[$land][$key]['start']=str_replace(",",".",$data2['1']);//number_format(str_replace(",",".",$data2['1']),"2",".",".");//*1000
				$kosten[$land][$key]['stop']=str_replace(",",".",$data2['2']);//number_format(str_replace(",",".",$data2['2']),"2",".",".");//*1000
				$kosten[$land][$key]['kosten']=number_format(str_replace(",",".",$data2['3']),"2",".",".");
			}
		}
		if (!empty($kosten[$lang_krz[$this->user_Daten_bestellung[0]['extended_user_land']]])) {
			$kosten2=$kosten[$lang_krz[$this->user_Daten_bestellung[0]['extended_user_land']]];
		}
		elseif(in_array($this->user_Daten_bestellung[0]['extended_user_land'],$lang_krz_eu)) {
			$kosten2=$kosten['eu'];
		}
		else {
			$kosten2=$kosten['welt'];
		}

		if (empty($kosten2)) {
			$kosten2[0]=5.00;
		}
		$kosten=$kosten2;
		if (is_array($kosten)) {
			foreach ($kosten as $key=>$value) {
				if ($gewicht>$value['start'] && $gewicht < $value['stop']) {
					$kosten_gesamt=$value['kosten'];
				}
			}
		}
		if ($gewicht>0 && empty($kosten)) {
			$kosten_gesamt=20;
		}

		return $kosten_gesamt;
	}

	/**
	 * shop_class_checkout::get_versand_art_daten()
	 *
	 * @param $id
	 * @return void
	 */
	private function get_versand_art_daten($id)
	{
		if ($id<1) {
			$id=4;
		}
		$sql=sprintf("SELECT * FROM %s
						  WHERE versand_arten_id='%d'
						  AND versand_arten_lang_id='%d'
											",
			$this->cms->tbname['plugin_shop_versand_arten'],
			$this->db->escape($id),
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * shop_class_checkout::get_versand_art_pro_produkt()
	 *
	 * @param $produkt_id
	 * @return void
	 */
	private function get_versand_art_pro_produkt($produkt_id)
	{
		$sql=sprintf("SELECT produkte_lang_versandart,
								 produkte_lang_versandkosten,
								 produkte_lang_versandkosten_additiv 
								 FROM %s
							WHERE produkte_lang_id='%d'
							AND produkte_lang_lang_id='%d'",
			$this->cms->tbname['plugin_shop_produkte_lang'],
			$this->db->escape($produkt_id),
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (empty($result[0]['produkte_lang_versandart'])) {
			$sql=sprintf("SELECT versand_arten_id AS produkte_lang_versandart
								 FROM %s
							WHERE 	versand_arten_Anzeige_der_Versandrart_aktiv='1'
							AND versand_arten_lang_id='%d' ORDER BY versand_arten_id ASC LIMIT 1",
				$this->cms->tbname['plugin_shop_versand_arten'],
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}
		return $result[0];
	}

	/**
	 * shop_class_checkout::get_shop_liefer_versand_arten()
	 *
	 * @return array|void
	 */
	function get_shop_liefer_versand_arten_old()
	{
		$sql=sprintf("SELECT * FROM %s
											WHERE versand_arten_Anzeige_der_Versandrart_aktiv='1'
											AND versand_arten_msatzstart<'%s' 
											AND versand_arten_msatzstopp>'%s'  
											AND 	versand_arten_lang_id='%d'																																	
											ORDER BY versand_arten_Sortierreihenfolge ASC",
			$this->cms->tbname['plugin_shop_versand_arten'],
			round($_SESSION['shoppping_cart_zwischensumme_org'],0),
			round($_SESSION['shoppping_cart_zwischensumme_org'],0),
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		//FAlls Keine ausgewählt ist einen Fallback anbieten.
		if (empty($result)) {
			$sql=sprintf("SELECT * FROM %s
							WHERE versand_arten_Anzeige_der_Versandrart_aktiv='1'
							AND 	versand_arten_lang_id='%d'																					
							ORDER BY versand_arten_Sortierreihenfolge ASC",
				$this->cms->tbname['plugin_shop_versand_arten'],
				round($_SESSION['shoppping_cart_zwischensumme_org'],0),
				round($_SESSION['shoppping_cart_zwischensumme_org'],0),
				$this->cms->lang_id
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}

		if (empty($result)) {
			$sql=sprintf("SELECT * FROM %s
											WHERE versand_arten_id='1'
											",
				$this->cms->tbname['plugin_shop_versand_arten']
			);
			$result=$this->db->get_results($sql,ARRAY_A);
		}
		return $result;
	}

	/**
	 * shop_class_checkout::shop_get_user_status()
	 * Bestimmt den aktuellen Status,
	 * 3 sind möglich
	 * 1. Neuer User
	 * 2. Angemeldet aber noch kein Shop Kunden
	 * 3. Angemeldet und Shop Kunde
	 * @return void|int|string
	 */
	function shop_get_user_status()
	{
		//Wenn >11 dann ist eine angemeldeter User
		if ($this->user->userid>11 or $this->user->userid==10) {
			//Abfragen ob Userkunde
			$sql=sprintf("SELECT extended_user_id FROM %s
											WHERE extended_user_user_id='%d' LIMIT 1",
				$this->cms->tbname['plugin_shop_crm_extended_user'],
				$this->user->userid
			);
			$r_var=$this->db->get_var($sql);
			//User angemeldet, aber kein Kunde
			if (!is_numeric($r_var)) {
				$status="user_no_kunde";
			}
			//User angemeldet und ist Kunde
			else {
				$status=$r_var;
			}
		}
		//Komplett neu
		else {
			$status="new";
		}
		return $status;
	}

	/**
	 * @return bool
	 */
	function check_ob_email_exist()
	{
		return true;
		if (!empty($this->checked->extended_user_email)) {
			$sql=sprintf("SELECT * FROM %s WHERE extended_user_email='%s' AND extended_user_user_id<>'%d'",
				$this->cms->tbname['plugin_shop_crm_extended_user'],
				$this->db->escape($this->checked->extended_user_email),
				$this->user->userid
			);
			$exist1=$this->db->get_results($sql);

			if (empty($exist1)) {
				$sql=sprintf("SELECT * FROM %s WHERE email='%s' AND userid <>'%d'",
					$this->cms->tbname['papoo_user'],
					$this->db->escape($this->checked->extended_user_email),
					$this->user->userid
				);
				$exist2=$this->db->get_results($sql);
			}

			#if (empty($exist1) && empty($exist2))
			{
				return true;
			}
			$this->checked->extended_user_email="";
			$this->checked->extended_user_email_false="NO";

		}
		return false;
	}

	/**
	 * @param int $bereich
	 */
	function make_checked_felder_null($bereich=1)
	{
		$sql=sprintf("SELECT prospect_lang_namedeskundenfeldes FROM %s
											WHERE prospect_lang_WhlenSiedenTypdesAttributesaus='4' 
											AND prospect_lang_lang_id='%d'
											AND prospect_lang_inwelcembereich='%d'",
			$this->cms->tbname['plugin_shop_kunden_felder_lang'],
			$this->cms->lang_back_content_id,
			$bereich
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (is_array($result)) {
			foreach ($result as $key=>$value) {
				if (empty($this->checked->$value['prospect_lang_namedeskundenfeldes'])) {
					$this->checked->$value['prospect_lang_namedeskundenfeldes']="0";
				}
			}
		}
	}

	/**
	 * shop_class_checkout::shop_frontend_make_checkout()
	 *
	 * Umsetzung der Eingabe und Neuanmeldung von Shop Usern
	 * Hier wird konsequent auf die übliche Maske verzichtet
	 *
	 * @return void
	 */
	function shop_frontend_make_checkout()
	{
		///shop.php?menuid=18&ps_act=warenkorb_aktu
		//Überprüfen ob gerade ein angemeldeter User da ist und ob der auch Shop Kunden ist
		$user_status=$this->shop_get_user_status();
		if (empty($_SESSION['shop_cart']) && empty($this->checked->show_login)) {
			$this->reload("warenkorb_aktu","");
		}
		$_SESSION['shop_show_login']=$this->checked->show_login;
		$this->content->template['show_danke_shop']=$this->checked->show_login;
		if (!empty($this->checked->formSubmit_ext_user_front)) {
			$must_felder=$this->shop_get_pflicht_felder_fuer_ext_user();
			//Checken ob E-Mail existiert
			$email_exist=$this->check_ob_email_exist();
			$this->make_checked_felder_null(2);
			if (is_numeric($this->checked->extuser_id)) {
				//UPDATE
				$xsql['dbname'] = "plugin_shop_crm_extended_user";
				$xsql['praefix'] = "extended_user";
				$xsql['must'] =$must_felder;
				$xsql['where_name'] = "extended_user_user_id";
				$orgid=$this->checked->extended_user_user_id=$this->user->userid;
				#$xsql['must'] = array("produkte_lang_internername");
				$is_drin = $this->shop->update_eintrag_in_shop_db($xsql);
			}
			//Neu eintragen
			else {
				//Neu eintragen dann auch einige Pflichfelder setzen
				$this->checked->extended_user_aktiv=1;

				//Felder in der Produkt TB erstellen
				//Insert
				$xsql['dbname'] = "plugin_shop_crm_extended_user";
				$xsql['praefix'] = "extended_user";
				$xsql['must'] =$must_felder;
				$is_drin = $this->shop->insert_new_eintrag_in_shop_db($xsql);
				$orgid=$is_drin['insert_id'];
				//Nur wenn auch gespeichert wurde
				if (is_numeric($orgid)) {
					$this->shop_create_system_user($orgid,$user_status);
				}
			}
			//Lookups sichern -> Kundengruppen
			if (is_numeric($orgid)) {
				$this->shop_speicher_kunden_gruppen_eines_users_front($orgid);
			}
			//Neu laden
			if ($is_drin) {
				if (is_numeric($this->checked->extended_user_land)) {
					$_SESSION['shop_settings']['user_land_id']=$this->checked->extended_user_land;
				}

				//Lookup Tabellen sichern
				#$this->shop_save_lookup_pro_produkt();
				if (is_numeric($this->checked->extuser_id)) {
					$produkt_id=$this->checked->extuser_id;
				}

				if ($this->content->template['shop_system_settings'][0]['einstellungen_lang_hop_odus']!=2) {
					//Reload
					$this->reload("checkout_li_adr","");
				}
				else {
					//Direkt zum auschecken
					$this->reload("checkout_bestaetigen","");
				}

			}
		}
		if (!is_numeric($user_status)) {
			$this->shop_daten_get_formdat_pers_redat();
		}
		else {
			$this->content->template['ext_user_daten']=$this->shop_get_user_daten_schritt_repers();
			//Daten für das Formular rausholen
			$this->shop_daten_get_formdat_pers_redat();
			$this->content->template['shop_user_bekannt']="ok";
		}
	}

	/**
	 * shop_class_checkout::shop_get_user_daten_schritt_repers()
	 * Die Daten des Users rausholen der gerade eingeloggt ist
	 * Gilt auch für den Zustand nach dem ersten eintragen der Daten,
	 * da ist man ja eingeloggt
	 *
	 * @param array $bereich
	 * @return void
	 */
	function shop_get_user_daten_schritt_repers($bereich=array())
	{
		//Daten rausholen
		$sql=sprintf("SELECT * FROM %s WHERE extended_user_user_id='%d'",
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			$this->user->userid
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		$this->content->template['extuser_user_array']=$result;
		//Die gesamten Userdaten momentan
		$this->user_Daten_bestellung=$result;

		//Felder rausholen
		$felder=$this->shop_get_pflicht_felder_fuer_ext_user($bereich,"%");
		//Felder durchgehen und anzuzeigende zuweisen
		foreach ($felder as $feld) {
			if ($feld=="extended_user_re_gleich_liefer" && $this->checked->ps_act=="checkout_bestaetigen") {
				continue;
			}

			//Wenn Land
			if ($feld=="extended_user_land") {
				$customerCountryId = (int)$result[0][$feld];
				$result[0][$feld] = array_reduce($this->laender_liste ?? [], function ($countryName, $item) use ($customerCountryId) {
					return $item['shop_land_id'] == $customerCountryId ? $item['shop_land_name'] : $countryName;
				}, '');
			}
			$dat[]=$result[0][$feld];
			if (empty($result[0][$feld])){
				$this->shop_user_hat_noch_keine_liefer_adresse=1;
			}
		}
		//Wenn keine Versandart ausgewählt ist
		if (empty($result[0]['extended_user_versandart_der_letzten_bestellung'])) {
			$this->content->template['shop_user_hat_noch_keine_versand_art']=1;
		}

		if (($result[0]['extended_user_re_gleich_liefer'])<1) {
			$this->content->template['shop_user_keine_lieferadresse']=1;
		}
		//AN Ausgabe übergeben
		$this->content->template['show_nur_daten_die_daten']=$dat;
		$this->content->template['extuser_id2']=$result[0]['extended_user_id'];
		$this->content->template['extuser_id']=$result[0]['extended_user_id'];
		$this->content->template['extended_user_versandart_der_letzten_bestellung']=$result[0]['extended_user_versandart_der_letzten_bestellung'];

		return $result;
	}

	/**
	 * shop_class_checkout::shop_create_system_user()
	 * Hier wird ein echte Systemuser aus den Formulardaten erzeugt
	 *
	 * @param string $extended_userid
	 * @param string $user_status
	 * @return void
	 */
	function shop_create_system_user($extended_userid="xy",$user_status="")
	{
		if ($user_status=="new") {
			//USerdaten erzeugen
			$username=$this->shop_make_username($this->checked->extended_user_name,$this->checked->extended_user_vorname,"",$this->checked->extended_user_str_nr_re_adresse);
			$passwort=$password_org=$this->shop_make_passwort();
			//USerdaten eintragen
			$sql=sprintf("INSERT INTO %s 
							SET username='%s',
								password='%s',
								zeitstempel='%s',
								confirm_code='%s',
								email='%s',
								active='1',
								board='1'",
				$this->cms->tbname['papoo_user'],
				$username,
				md5($passwort),
				time(),
				md5(rand(0,time())),
				$this->db->escape($this->checked->extended_user_email)
			);
			$this->db->query($sql);
			$userid=$this->db->insert_id;

			/**
			 * Gruppendaten abfragen
			 */

			//Standardgruppe finden
			$sql=sprintf("SELECT kd_gruppe_lang_id FROM %s
							WHERE kd_gruppe_lang_gruppe_ist_standard='1'",
				$this->cms->tbname['plugin_shop_kunden_gruppe_lang']
			);
			$gr_var=$this->db->get_var($sql);

			//Davon dann die Rechte gruppen
			$sql=sprintf("SELECT * FROM %s WHERE shop_gruppen_id='%d'",
				$this->cms->tbname['plugin_shop_lookup_gruppen_ppgruppen'],
				$gr_var
			);
			$gruppen=$this->db->get_results($sql,ARRAY_A);

			//Alte Gruppendaten löschen
			$sql=sprintf("DELETE FROM %s WHERE userid='%d' AND gruppenid>10",
				$this->cms->tbname['papoo_lookup_ug'],
				$userid
			);
			$this->db->query($sql);

			//Gruppendaten eintragen
			foreach ($gruppen as $grup) {
				$sql=sprintf("INSERT INTO %s SET userid='%d', gruppenid='%d'",
					$this->cms->tbname['papoo_lookup_ug'],
					$userid,
					$grup['shop_gruppen_papoo_gruppen_id']
				);
				$this->db->query($sql);
			}

			//Rechte für Gruppe jeder eintragen
			#$sql=sprintf("INSERT INTO %s SET userid='%d', gruppenid='%d'",
			#								$this->cms->tbname['papoo_lookup_ug'],
			#								$userid,
			#								10
			#	);
			#$this->db->query($sql);

			//User einloggen

			$this->userid=$userid;

			$this->username = $username;#echo "<br />";
			$this->password = $passwort;#echo "<br />";
			$this->user_club_stufe=1;
			$this->editor = 3;
			$this->board = 1;

			// Ausloggvariable löschen
			unset ($_SESSION['logoff']);
			unset ($_SESSION['logfalse']);
			unset ($_SESSION['meta_gruppe_id']);

			// Einloggzustand übergeben an template
			$userok = $this->content->template['loggedin'] = "user_ok";
			$server = $_SERVER['SERVER_NAME'].PAPOO_WEB_PFAD;
			// Hashwert erstellen
			$hash = md5($server.$username.$passwort.$this->userid);

			// Username und Passwort an Session übergeben
			$_SESSION['sessionusername'] = $username;
			$_SESSION['sessionpassword'] = $passwort;

			$_SESSION['sessionuserid'] = $userid;
			$_SESSION['sessionhash'] = $hash;
			$_SESSION['sessioneditor'] = $this->editor;
			$_SESSION['board'] = $this->board;

			$shop_mailing = new shop_class_mailing();
			#echo $password_org;
			$shop_mailing->shop_verschicke_mail($password_org,$userid);
		}
		//Es ist schon ein eingetragener User dann userid übergeben
		else {
			$userid=$this->user->userid;
		}
		//Extended Userid updaten und eintragen
		$sql=sprintf("UPDATE %s SET extended_user_user_id='%d'
						WHERE extended_user_id='%d'",
			$this->cms->tbname['plugin_shop_crm_extended_user'],
			$userid,
			$extended_userid
		);
		$this->db->query($sql);
		//Fertig  
	}

	/**
	 * Passwort erzeugen
	 *
	 * @param string $name
	 * @param string $vorname
	 * @param string $ort
	 * @param string $str
	 * @return string
	 */
	function shop_make_username($name="",$vorname="",$ort="",$str="") {
		#$name=strtolower($name);
		#$name=strtolower($vorname);
		$name=preg_replace("/[^a-zA-Z0-9]/", "", $name);
		$vorname=preg_replace("/[^a-zA-Z0-9]/", "", $vorname);
		$ort=preg_replace("/[^a-z0-9]/", "", $ort);
		$str=preg_replace("/[^a-z0-9]/", "", $str);

		preg_match_all("/./", $name, $matches);
		$Buchstaben = $matches[0];

		if (count($Buchstaben<5)) {
			$Buchstaben = array ("a", "b", "c", "d", "e", "f", "g", "h", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
		}

		preg_match_all("/./", $vorname, $matches);
		$Zahlen = $matches[0];

		preg_match_all("/./", $ort.$str, $matches);
		$Zahlen = $matches[0];

		$Laenge = 10;

		for ($i = 0, $Passwort = ""; strlen($Passwort) < $Laenge; $i ++) {
			if (rand(0, 2) == 0 && isset ($Buchstaben)) {
				$Passwort .= $Buchstaben[rand(0, count($Buchstaben))];
			}
			elseif (rand(0, 2) == 1 && isset ($Zahlen)) {
				$Passwort .= $Zahlen[rand(0, count($Zahlen))];
			}
			elseif (rand(0, 2) == 2 && isset ($Sonderzeichen)) {
				$Passwort .= $Sonderzeichen[rand(0, count($Sonderzeichen))];
			}
		}

		// Nutzen um merkbare Usernamen zu erstellen
		return user_class::makeUniqueUsername(mb_substr(trim($vorname), 0, 1).trim($name));
	}

	/**
	 * Passwort erzeugen
	 *
	 * @return string
	 */
	function shop_make_passwort() {
		$Buchstaben = array ("a", "b", "c", "d", "e", "f", "g", "h", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
		$Zahlen = array ("2", "3", "4", "5", "6", "7", "8", "9");
		$Sonderzeichen = array ("@", "!", "=", "#");

		$Laenge = 8;

		for ($i = 0, $Passwort = ""; strlen($Passwort) < $Laenge; $i ++) {
			if (rand(0, 2) == 0 && isset ($Buchstaben)) {
				$Passwort .= $Buchstaben[rand(0, count($Buchstaben))];
			}
			elseif (rand(0, 2) == 1 && isset ($Zahlen)) {
				$Passwort .= $Zahlen[rand(0, count($Zahlen))];
			}
			elseif (rand(0, 2) == 2 && isset ($Sonderzeichen)) {
				$Passwort .= $Sonderzeichen[rand(0, count($Sonderzeichen))];
			}
		}

		return $Passwort;
	}

	/**
	 * shop_class_checkout::shop_get_standard_kunden_gruppen_id_front()
	 * Holt die Standard Gruppenid raus.
	 * @return void|array
	 */
	function shop_get_standard_kunden_gruppen_id_front()
	{
		$sql=sprintf("SELECT kd_gruppe_lang_id FROM %s 
									WHERE kd_gruppe_lang_gruppe_ist_standard='1'
									AND kd_gruppe_lang_lang_id 	='%d'",
			$this->cms->tbname['plugin_shop_kunden_gruppe_lang'],
			$this->cms->lang_id
		);
		$rvar=$this->db->get_var($sql);
		return $rvar;
	}

	/**
	 * @param $orgid
	 */
	function shop_speicher_kunden_gruppen_eines_users_front($orgid)
	{
		//Standard Kundengruppeid rausholen
		$standard=$this->shop_get_standard_kunden_gruppen_id_front();
		//Alle löschen
		$sql=sprintf("DELETE FROM %s WHERE shop_gruppen_user_id='%d'
						AND shop_gruppen_gruppe_id='%d'",
			$this->cms->tbname['plugin_shop_lookup_user_kunden_gruppen'],
			$this->db->escape($orgid),
			$standard
		);
		$this->db->query($sql);

		//alle durchgehen und eintragen

		$sql=sprintf("INSERT INTO %s SET
						shop_gruppen_user_id='%d',
						shop_gruppen_gruppe_id='%d'",
			$this->cms->tbname['plugin_shop_lookup_user_kunden_gruppen'],
			$this->db->escape($orgid),
			$standard
		);
		$this->db->query($sql);

		//FIXME HIER USER erstellen in der papoo USer Tabelle mit Gruppenrechten!!!
		//$this->shop_create_echten_user_front();
	}

	/**
	 * @return array|void
	 */
	function shop_get_user_feld_liste()
	{
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_shop_kunden_felder']
		);
		$result1=$this->db->get_results($sql,ARRAY_A);

		//Sprachdaten
		$sql=sprintf("SELECT * FROM %s LEFT JOIN %s ON prospect_id=prospect_lang_id
						WHERE prospect_lang_lang_id='%d' AND prospect_lang_istaktivkundenfeld='1'
						ORDER BY prospect_lang_ser_order_id ASC",
			$this->cms->tbname['plugin_shop_kunden_felder'],
			$this->cms->tbname['plugin_shop_kunden_felder_lang'],
			$this->cms->lang_id
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;;
	}

	/**
	 * @return mixed
	 */
	function shop_get_ext_user_liste()
	{
		$sql=sprintf("SELECT * FROM %s ",
			$this->cms->tbname['plugin_shop_crm_extended_user']
		);
		#$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * shop_class_checkout::shop_daten_get_formdat_pers_redat()
	 * Hier holen wir die Daten für die persönlichen Daten
	 * und die Rechnungsdaten
	 *
	 * @return void
	 */
	function shop_daten_get_formdat_pers_redat()
	{
		//INI
		$berreich=array();

		//Alle Attribute rausholen die aktiv und zum Produkttypen gehören und zur Sprache
		$felder=$this->shop_get_user_feld_liste();

		//Colums rausholen
		$sql=sprintf("SHOW COLUMNS FROM %s",
			$this->cms->tbname['plugin_shop_crm_extended_user']
		);
		$colums=$this->db->get_results($sql,ARRAY_A);

		foreach ($felder as $at) {
			//Durchgehen ob die auch als Felder in der Produkttabelle existieren
			foreach ($colums as $col) {
				//WEnn Feld wirklich vorhanden dann übergeben
				if ($col['Field']==$at['prospect_lang_namedeskundenfeldes']) {
					$ok_cols[]=$at;
				}
			}
		}

		//Gruppieren in die Bereiche
		foreach ($ok_cols as $cols) {
			//Anhand der Bereichsid gruppieren
			$bereich[$cols['prospect_lang_inwelcembereich']][]=$cols;
		}

		//Diese Breiche durchgehen
		foreach ($bereich as $br) {
			$brid=$br[0]['prospect_lang_inwelcembereich'];
			//Felder erzeugen für den jeweiligen Bereich
			$felder_bereich[$brid]=$this->shop_erzeuge_bereich_felder($br);
		}
		$this->content->template['persdaten']=$felder_bereich['1'];
		$this->content->template['rechadressse']=$felder_bereich['2'];
		$this->content->template['shipadresse']=$felder_bereich['3'];
		$this->content->template['zahldaten']=$felder_bereich['4'];
		$this->content->template['sonstiges']=$felder_bereich['5'];
		$this->content->template['kontaktdaten']=$felder_bereich['6'];
		$this->content->template['firmendaten']=$felder_bereich['7'];
		//Felder ausgeben
	}

	/**
	 * shop_class_product::shop_erzeuge_bereich_felder()
	 * Erzeugt für den jeweiligen Bereich die Felder
	 *
	 * @param mixed $br
	 * @return array
	 */
	function shop_erzeuge_bereich_felder($br)
	{
		foreach ($br as $feld_vorlage) {
			$feld[]=$this->shop_erzeuge_feld($feld_vorlage);
		}
		return $feld;
	}

	/**
	 * @param $dat
	 * @return string
	 */
	function shop_erzeuge_feld($dat)
	{
		#print_r($dat);
		$dat2=array();
		foreach ($dat as $key=>$value) {
			switch ($key) {
			case "prospect_id":
				$dat2['attribute_lang_id']=$value;
				break;
			case "prospect_lang_namedeskundenfeldes":
				$dat2['attribute_lang_internername']=$value;
				break;
			case "prospect_lang_label_des_kundenfeldes":
				$dat2['attribute_lang_LabelNameattr']=$value;
				break;
			case "prospect_lang_default_wert_wenn_vorhanden":
				$dat2['attribute_lang_deafultwertattribut']=$value;
				break;
			case "prospect_lang_groessedes_kunden_feldes":
				$dat2['attribute_lang_groessedesfeldes']=$value;
				break;
			case "prospect_lang_Weitereerlauterungen":
				$dat2['attribute_lang_erlauterung']=$value;
				break;
			case "prospect_lang_InhalteeinerSelectbox":
				$dat2['attribute_lang_Inhaltedesselectbox']=$value;
				break;
			case "prospect_lang_Sollesimfrontendpflichtsein":
				$dat2['attribute_lang_sollpflichtseinattribut']=$value;
				break;
			case "prospect_lang_WhlenSiedenTypdesAttributesaus":
				$dat2['attribute_lang_typeattribut']=$value;
				break;
			}
		}
		$dat=$dat2;
		$felder=new shop_class_create_felder();
		$felder->set_produkt_data($this->content->template['ext_user_daten']);
		switch ($dat['attribute_lang_typeattribut']) {
		case "0":
			$feld=$felder->shop_create_text_feld($dat);
			break;
			// Einen Typ erstellen
		case "1":
			$feld=$felder->shop_create_text_feld($dat);
			break;

			// Ein Attribut erstellen/bearbeiten
		case "2":
			$feld=$felder->shop_create_textarea_feld($dat);
			break;

			// Ein Produkt erstellen/bearbeiten
		case "3":
			$feld=$felder->shop_create_selectbox_feld($dat);
			break;

		case "4":
			$feld=$felder->shop_create_checkbox_feld($dat);
			break;

		case "5":
			$feld=$felder->shop_create_image_upload_feld($dat);
			break;

		case "6":
			$feld=$felder->shop_create_file_upload_feld($dat);
			break;

		case "7":
			$feld=$felder->shop_create_datum_feld($dat);
			break;

		default:
			$feld=$felder->shop_create_text_feld($dat);
			break;
		}
		$feld="nobr:".$feld;
		return $feld;
	}

	/**
	 * shop_class::reload()
	 * Zu anderem ps_act weiterleiten
	 * @param string $shop_insert ps_act, zu dem weitergeleitet werden soll
	 * @param string $template (nicht verwendet)
	 * @return void
	 */
	function reload($shop_insert = "", $template = "")
	{
		$url = "menuid=" . $this->checked->menuid;
		$url .= "&ps_act=" . $shop_insert ;
		$location_url = "shop.php?" . $url;
		$this->do_redirect($location_url, 303);
	}

	/**
	 * shop_class_checkout::do_redirect()
	 * Führt eine Weiterleitung durch und ruft exit() auf.
	 *
	 * @param string $url URL
	 * @param int|string $code HTTP-Statuscode
	 * @return void
	 * @deprecated Ersetzt durch diverse_class::http_redirect()
	 */
	function do_redirect($url="", $code=307)
	{
		$codetexts = array(301=>'Moved Permanently', 302=>'Found', 303=>'See Other', 307=>'Temporary Redirect', 308=>'Permanent Redirect');
		$codetext = isset($codetexts[$code]) ? $codetexts[$code] : 'Redirect';

		$https = !empty($_SERVER['HTTPS']);

		$i = strpos($url, '://');
		if ($i !== FALSE and $i < 8) {
			$fullurl = $url;
		}
		elseif ($url[0] == '/') {
			$fullurl = ($https?'https://':'http://').$_SERVER['HTTP_HOST'].PAPOO_WEB_PFAD.$url;
		}
		else {
			$fullurl = ($https?'https://':'http://').$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/'.$url;
		}

		if (empty($_SESSION['debug_stopallredirect'])) {
			header('HTTP/1.1 '.$code.' '.$codetext);
			header("Location: ".$fullurl);
		}
		else {
			echo '<!-- '.$code.' '.$codetext.' -->';
		}
		echo '<html><body><a href="'.htmlspecialchars($fullurl).'">'.'Weiter'.'</a></body></html>';
		exit();
	}
}
$checkout=new shop_class_checkout();
