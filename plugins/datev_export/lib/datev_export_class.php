<?php

require_once(PAPOO_ABS_PFAD.'/plugins/papoo_shop/lib/shop_class_laender.php');

/**
 * Class datev_export
 */
class datev_export
{
	/**
	 * datev_export constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $diverse, $cms, $db_abs;
		$this->content = &$content;
		$this->db = $db;
		$this->checked = $checked;
		$this->user = $user;
		$this->diverse = $diverse;
		$this->cms = $cms;
		$this->db_abs = $db_abs;

		if (defined("admin")) {
			// �berpr�fen ob der User Zugriff haben darf
			$this->user->check_intern();

			/**
			 * Das aktuelle Template auslesen, anhand dessen kann sichergestellt werden
			 * dass man im richtigen Plugin ist
			 */
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			// Korrekt eingeloggt
			if ($template != "login.utf8.html") {
				// und auch noch das richtige Template
				if ($template2=="datev_export_back.html") {
					// Daten speichern wenn notwendig
					$this->show_csv();

					if (!empty($this->checked->show_beleg)) {
						$this->show_beleg();
					}
				}
			}
		}
	}

	/**
	 * @return bool
	 */
	function show_beleg()
	{
		if (!is_numeric($this->checked->show_beleg)) {
			return false;
		}
		$data=file_get_contents(PAPOO_ABS_PFAD."/plugins/papoo_shop/doks/".$this->checked->show_beleg.".pdf");

		//Falls empty muss hier noch die Erzeugung rein...
		//Dann nochmal aufrufen...
		$this->download($data,"pdf");
		exit();
	}

	/**
	 * @return bool|void
	 */
	function show_csv()
	{
		if (empty($this->checked->formSubmit_zeitraum)) {
			return false;
		}
		//Daten rausholen
		$data=$this->get_faktura_data();

		//Daten umwandeln
		$csv=$this->create_csv($data);

		$pfad=$this->save_file($csv);

		//USerdaten
		$csv_user=$this->create_csv_user_data($data);

		//Ausgeben
		if ($this->checked->api=="show_belege") {
			$this->download($csv);
			exit();
		}

		if ($this->checked->api=="show_user") {
			$this->download($csv_user);
			exit();
		}

		$pfad2=$this->save_file_user($csv_user);

		$this->content->template['pfad_download']=$pfad;
		$this->content->template['pfad_download_user']=$pfad2;
	}

	/**
	 * @param $data
	 * @return string
	 */
	function create_csv_user_data($data)
	{
		$csv='"Konto";"Name (Adressatentyp Unternehmen)";"Unternehmensgegenstand";"Name (Adressatentyp natürl. Person)";"Vorname (Adressatentyp natürl. Person)";"Name (Adressatentyp keine Angabe)";"Adressatentyp";"Kurzbezeichnung";"EU-Land";"EU-USt-IdNr.";"Anrede";"Titel/Akad. Grad";"Adelstitel";"Namensvorsatz";"Adressart";"Straße";"Postfach";"Postleitzahl";"Ort";"Land";"Versandzusatz";"Adresszusatz";"Abweichende Anrede";"Abw. Zustellbezeichnung 1";"Abw. Zustellbezeichnung 2";"Kennz. Korrespondenzadresse";"Adresse gültig von";"Adresse gültig bis";"Telefon";"Bemerkung (Telefon)";"Telefon Geschäftsleitung";"Bemerkung (Telefon GL)";"E-Mail";"Bemerkung (E-Mail)";"Internet";"Bemerkung (Internet)";"Fax";"Bemerkung (Fax)";"Sonstige";"Bemerkung (Sonstige)";"Bankleitzahl 1";"Bankbezeichnung 1";"Bankkonto-Nummer 1";"Länderkennzeichen 1";"IBAN 1";"Leerfeld";"SWIFT-Code 1";"Abw. Kontoinhaber 1";"Kennz. Haupt-Bankverb. 1";"Bankverb. 1 Gültig von";"Bankverb. 1 Gültig bis";"Bankleitzahl 2";"Bankbezeichnung 2";"Bankkonto-Nummer 2";"Länderkennzeichen 2";"IBAN 2";"Leerfeld";"SWIFT-Code 2";"Abw. Kontoinhaber 2";"Kennz. Haupt-Bankverb. 2";"Bankverb. 2 gültig von";"Bankverb. 2 gültig bis";"Bankleitzahl 3";"Bankbezeichnung 3";"Bankkonto-Nummer 3";"Länderkennzeichen 3";"IBAN 3";"Leerfeld";"SWIFT-Code 3";"Abw. Kontoinhaber 3";"Kennz. Haupt-Bankverb. 3";"Bankverb. 3 gültig von";"Bankverb. 3 gültig bis";"Bankleitzahl 4";"Bankbezeichnung 4";"Bankkonto-Nummer 4";"Länderkennzeichen 4";"IBAN 4";"Leerfeld";"SWIFT-Code 4";"Abw. Kontoinhaber 4";"Kennz. Haupt-Bankverb. 4";"Bankverb. 4 Gültig von";"Bankverb. 4 Gültig bis";"Bankleitzahl 5";"Bankbezeichnung 5";"Bankkonto-Nummer 5";"Länderkennzeichen 5";"IBAN 5";"Leerfeld";"SWIFT-Code 5";"Abw. Kontoinhaber 5";"Kennz. Haupt-Bankverb. 5";"Bankverb. 5 gültig von";"Bankverb. 5 gültig bis";"Leerfeld";"Briefanrede";"Grußformel";"Kundennummer";"Steuernummer";"Sprache";"Ansprechpartner";"Vertreter";"Sachbearbeiter";"Diverse-Konto";"Ausgabeziel";"Währungssteuerung";"Kreditlimit (Debitor)";"Zahlungsbedingung";"Fälligkeit in Tagen (Debitor)";"Skonto in Prozent (Debitor)";"Kreditoren-Ziel 1 (Tage)";"Kreditoren-Skonto 1 (%)";"Kreditoren-Ziel 2 (Tage)";"Kreditoren-Skonto 2 (%)";"Kreditoren-Ziel 3 Brutto (Tage)";"Kreditoren-Ziel 4 (Tage)";"Kreditoren-Skonto 4 (%)";"Kreditoren-Ziel 5 (Tage)";"Kreditoren-Skonto 5 (%)";"Mahnung";"Kontoauszug";"Mahntext 1";"Mahntext 2";"Mahntext 3";"Kontoauszugstext";"Mahnlimit Betrag";"Mahnlimit %";"Zinsberechnung";"Mahnzinssatz 1";"Mahnzinssatz 2";"Mahnzinssatz 3";"Lastschrift";"Verfahren";"Mandantenbank";"Zahlungsträger";"Indiv. Feld 1";"Indiv. Feld 2";"Indiv. Feld 3";"Indiv. Feld 4";"Indiv. Feld 5";"Indiv. Feld 6";"Indiv. Feld 7";"Indiv. Feld 8";"Indiv. Feld 9";"Indiv. Feld 10";"Indiv. Feld 11";"Indiv. Feld 12";"Indiv. Feld 13";"Indiv. Feld 14";"Indiv. Feld 15";"Abweichende Anrede (Rechnungsadresse)";"Adressart (Rechnungsadresse)";"Straße (Rechnungsadresse)";"Postfach (Rechnungsadresse)";"Postleitzahl (Rechnungsadresse)";"Ort (Rechnungsadresse)";"Land (Rechnungsadresse)";"Versandzusatz (Rechnungsadresse)";"Adresszusatz (Rechnungsadresse)";"Abw. Zustellbezeichnung 1 (Rechnungsadresse)";"Abw. Zustellbezeichnung 2 (Rechnungsadresse)";"Adresse Gültig von (Rechnungsadresse)";"Adresse Gültig bis (Rechnungsadresse)";"Bankleitzahl 6";"Bankbezeichnung 6";"Bankkonto-Nummer 6";"Länderkennzeichen 6";"IBAN 6";"Leerfeld";"SWIFT-Code 6";"Abw. Kontoinhaber 6";"Kennz. Haupt-Bankverb. 6";"Bankverb 6 gültig von";"Bankverb 6 gültig bis";"Bankleitzahl 7";"Bankbezeichnung 7";"Bankkonto-Nummer 7";"Länderkennzeichen 7";"IBAN 7";"Leerfeld";"SWIFT-Code 7";"Abw. Kontoinhaber 7";"Kennz. Haupt-Bankverb. 7";"Bankverb 7 gültig von";"Bankverb 7 gültig bis";"Bankleitzahl 8";"Bankbezeichnung 8";"Bankkonto-Nummer 8";"Länderkennzeichen 8";"IBAN 8";"Leerfeld";"SWIFT-Code 8";"Abw. Kontoinhaber 8";"Kennz. Haupt-Bankverb. 8";"Bankverb 8 gültig von";"Bankverb 8 gültig bis";"Bankleitzahl 9";"Bankbezeichnung 9";"Bankkonto-Nummer 9";"Länderkennzeichen 9";"IBAN 9";"Leerfeld";"SWIFT-Code 9";"Abw. Kontoinhaber 9";"Kennz. Haupt-Bankverb. 9";"Bankverb 9 gültig von";"Bankverb 9 gültig bis";"Bankleitzahl 10";"Bankbezeichnung 10";"Bankkonto-Nummer 10";"Länderkennzeichen 10";"IBAN 10";"Leerfeld";"SWIFT-Code 10";"Abw. Kontoinhaber 10";"Kennz. Haupt-Bankverb. 10";"Bankverb 10 gültig von";"Bankverb 10 gültig bis";"Nummer Fremdsystem";"Insolvent";"SEPA-Mandatsreferenz 1";"SEPA-Mandatsreferenz 2";"SEPA-Mandatsreferenz 3";"SEPA-Mandatsreferenz 4";"SEPA-Mandatsreferenz 5";"SEPA-Mandatsreferenz 6";"SEPA-Mandatsreferenz 7";"SEPA-Mandatsreferenz 8";"SEPA-Mandatsreferenz 9";"SEPA-Mandatsreferenz 10";"Verknüpftes OPOS-Konto";"Mahnsperre bis";"Lastschriftsperre bis";"Zahlungssperre bis";"Gebührenberechnung";"Mahngebühr 1";"Mahngebühr 2";"Mahngebühr 3";"Pauschalberechnung";"Verzugspauschale 1";"Verzugspauschale 2";"Verzugspauschale 3"";'."\n";


		$csv_ar=explode(";",$csv);
		foreach ($csv_ar as $k=>$v) {
			$v = str_ireplace('"',"",$v);
			if (strlen($v)>1)
				$neu_datevg[$v]="";

		}

		$neu = array();
		$ids=array();

		foreach ((array)$data as $k=>$v) {

			$datev_array=$neu_datevg;

			if (empty($ids[$v['kunden_order_user_id']])) {
				$neu[]=$v;
			}

			$ids[$v['kunden_order_user_id']]=$v['kunden_order_user_id'];
		}

		//Durchgehen
		foreach ($neu as $k=>$v) {
			//Erstmal die Kunennummer
			if (strlen($v['kunden_order_user_id'])==4) {
				$v['kunden_order_user_id']="5".$v['kunden_order_user_id'];
			}
			if (strlen($v['kunden_order_user_id'])==3) {
				$v['kunden_order_user_id']="50".$v['kunden_order_user_id'];
			}
			if (strlen($v['kunden_order_user_id'])==2) {
				$v['kunden_order_user_id']="500".$v['kunden_order_user_id'];
			}


			/**
			Array
			(
			[0] => Konto
			[1] => Name (Adressatentyp Unternehmen)
			[2] => Unternehmensgegenstand
			[3] => Name (Adressatentyp natürl. Person)
			[4] => Vorname (Adressatentyp natürl. Person)
			[5] => Name (Adressatentyp keine Angabe)
			[6] => Adressatentyp
			[7] => Kurzbezeichnung
			[8] => EU-Land
			[9] => EU-USt-IdNr.
			[10] => Anrede
			[11] => Titel/Akad. Grad
			[12] => Adelstitel
			[13] => Namensvorsatz
			[14] => Adressart
			[15] => Straße
			[16] => Postfach
			[17] => Postleitzahl
			[18] => Ort
			[19] => Land
			[20] => Versandzusatz
			[21] => Adresszusatz
			[22] => Abweichende Anrede
			[23] => Abw. Zustellbezeichnung 1
			[24] => Abw. Zustellbezeichnung 2
			[25] => Kennz. Korrespondenzadresse
			[26] => Adresse gültig von
			[27] => Adresse gültig bis
			[28] => Telefon
			[29] => Bemerkung (Telefon)
			[30] => Telefon Geschäftsleitung
			[31] => Bemerkung (Telefon GL)
			[32] => E-Mail
			[33] => Bemerkung (E-Mail)
			[34] => Internet
			[35] => Bemerkung (Internet)
			[36] => Fax
			[37] => Bemerkung (Fax)
			[38] => Sonstige
			[39] => Bemerkung (Sonstige)
			[40] => Bankleitzahl 1
			[41] => Bankbezeichnung 1
			[42] => Bankkonto-Nummer 1
			[43] => Länderkennzeichen 1
			[44] => IBAN 1
			[45] => Leerfeld
			[46] => SWIFT-Code 1
			[47] => Abw. Kontoinhaber 1
			[48] => Kennz. Haupt-Bankverb. 1
			[49] => Bankverb. 1 Gültig von
			[50] => Bankverb. 1 Gültig bis
			[51] => Bankleitzahl 2
			[52] => Bankbezeichnung 2
			[53] => Bankkonto-Nummer 2
			[54] => Länderkennzeichen 2
			[55] => IBAN 2
			[56] => Leerfeld
			[57] => SWIFT-Code 2
			[58] => Abw. Kontoinhaber 2
			[59] => Kennz. Haupt-Bankverb. 2
			[60] => Bankverb. 2 gültig von
			[61] => Bankverb. 2 gültig bis
			[62] => Bankleitzahl 3
			[63] => Bankbezeichnung 3
			[64] => Bankkonto-Nummer 3
			[65] => Länderkennzeichen 3
			[66] => IBAN 3
			[67] => Leerfeld
			[68] => SWIFT-Code 3
			[69] => Abw. Kontoinhaber 3
			[70] => Kennz. Haupt-Bankverb. 3
			[71] => Bankverb. 3 gültig von
			[72] => Bankverb. 3 gültig bis
			[73] => Bankleitzahl 4
			[74] => Bankbezeichnung 4
			[75] => Bankkonto-Nummer 4
			[76] => Länderkennzeichen 4
			[77] => IBAN 4
			[78] => Leerfeld
			[79] => SWIFT-Code 4
			[80] => Abw. Kontoinhaber 4
			[81] => Kennz. Haupt-Bankverb. 4
			[82] => Bankverb. 4 Gültig von
			[83] => Bankverb. 4 Gültig bis
			[84] => Bankleitzahl 5
			[85] => Bankbezeichnung 5
			[86] => Bankkonto-Nummer 5
			[87] => Länderkennzeichen 5
			[88] => IBAN 5
			[89] => Leerfeld
			[90] => SWIFT-Code 5
			[91] => Abw. Kontoinhaber 5
			[92] => Kennz. Haupt-Bankverb. 5
			[93] => Bankverb. 5 gültig von
			[94] => Bankverb. 5 gültig bis
			[95] => Leerfeld
			[96] => Briefanrede
			[97] => Grußformel
			[98] => Kundennummer
			[99] => Steuernummer
			[100] => Sprache
			[101] => Ansprechpartner
			[102] => Vertreter
			[103] => Sachbearbeiter
			[104] => Diverse-Konto
			[105] => Ausgabeziel
			[106] => Währungssteuerung
			[107] => Kreditlimit (Debitor)
			[108] => Zahlungsbedingung
			[109] => Fälligkeit in Tagen (Debitor)
			[110] => Skonto in Prozent (Debitor)
			[111] => Kreditoren-Ziel 1 (Tage)
			[112] => Kreditoren-Skonto 1 (%)
			[113] => Kreditoren-Ziel 2 (Tage)
			[114] => Kreditoren-Skonto 2 (%)
			[115] => Kreditoren-Ziel 3 Brutto (Tage)
			[116] => Kreditoren-Ziel 4 (Tage)
			[117] => Kreditoren-Skonto 4 (%)
			[118] => Kreditoren-Ziel 5 (Tage)
			[119] => Kreditoren-Skonto 5 (%)
			[120] => Mahnung
			[121] => Kontoauszug
			[122] => Mahntext 1
			[123] => Mahntext 2
			[124] => Mahntext 3
			[125] => Kontoauszugstext
			[126] => Mahnlimit Betrag
			[127] => Mahnlimit %
			[128] => Zinsberechnung
			[129] => Mahnzinssatz 1
			[130] => Mahnzinssatz 2
			[131] => Mahnzinssatz 3
			[132] => Lastschrift
			[133] => Verfahren
			[134] => Mandantenbank
			[135] => Zahlungsträger
			[136] => Indiv. Feld 1
			[137] => Indiv. Feld 2
			[138] => Indiv. Feld 3
			[139] => Indiv. Feld 4
			[140] => Indiv. Feld 5
			[141] => Indiv. Feld 6
			[142] => Indiv. Feld 7
			[143] => Indiv. Feld 8
			[144] => Indiv. Feld 9
			[145] => Indiv. Feld 10
			[146] => Indiv. Feld 11
			[147] => Indiv. Feld 12
			[148] => Indiv. Feld 13
			[149] => Indiv. Feld 14
			[150] => Indiv. Feld 15
			[151] => Abweichende Anrede (Rechnungsadresse)
			[152] => Adressart (Rechnungsadresse)
			[153] => Straße (Rechnungsadresse)
			[154] => Postfach (Rechnungsadresse)
			[155] => Postleitzahl (Rechnungsadresse)
			[156] => Ort (Rechnungsadresse)
			[157] => Land (Rechnungsadresse)
			[158] => Versandzusatz (Rechnungsadresse)
			[159] => Adresszusatz (Rechnungsadresse)
			[160] => Abw. Zustellbezeichnung 1 (Rechnungsadresse)
			[161] => Abw. Zustellbezeichnung 2 (Rechnungsadresse)
			[162] => Adresse Gültig von (Rechnungsadresse)
			[163] => Adresse Gültig bis (Rechnungsadresse)
			[164] => Bankleitzahl 6
			[165] => Bankbezeichnung 6
			[166] => Bankkonto-Nummer 6
			[167] => Länderkennzeichen 6
			[168] => IBAN 6
			[169] => Leerfeld
			[170] => SWIFT-Code 6
			[171] => Abw. Kontoinhaber 6
			[172] => Kennz. Haupt-Bankverb. 6
			[173] => Bankverb 6 gültig von
			[174] => Bankverb 6 gültig bis
			[175] => Bankleitzahl 7
			[176] => Bankbezeichnung 7
			[177] => Bankkonto-Nummer 7
			[178] => Länderkennzeichen 7
			[179] => IBAN 7
			[180] => Leerfeld
			[181] => SWIFT-Code 7
			[182] => Abw. Kontoinhaber 7
			[183] => Kennz. Haupt-Bankverb. 7
			[184] => Bankverb 7 gültig von
			[185] => Bankverb 7 gültig bis
			[186] => Bankleitzahl 8
			[187] => Bankbezeichnung 8
			[188] => Bankkonto-Nummer 8
			[189] => Länderkennzeichen 8
			[190] => IBAN 8
			[191] => Leerfeld
			[192] => SWIFT-Code 8
			[193] => Abw. Kontoinhaber 8
			[194] => Kennz. Haupt-Bankverb. 8
			[195] => Bankverb 8 gültig von
			[196] => Bankverb 8 gültig bis
			[197] => Bankleitzahl 9
			[198] => Bankbezeichnung 9
			[199] => Bankkonto-Nummer 9
			[200] => Länderkennzeichen 9
			[201] => IBAN 9
			[202] => Leerfeld
			[203] => SWIFT-Code 9
			[204] => Abw. Kontoinhaber 9
			[205] => Kennz. Haupt-Bankverb. 9
			[206] => Bankverb 9 gültig von
			[207] => Bankverb 9 gültig bis
			[208] => Bankleitzahl 10
			[209] => Bankbezeichnung 10
			[210] => Bankkonto-Nummer 10
			[211] => Länderkennzeichen 10
			[212] => IBAN 10
			[213] => Leerfeld
			[214] => SWIFT-Code 10
			[215] => Abw. Kontoinhaber 10
			[216] => Kennz. Haupt-Bankverb. 10
			[217] => Bankverb 10 gültig von
			[218] => Bankverb 10 gültig bis
			[219] => Nummer Fremdsystem
			[220] => Insolvent
			[221] => SEPA-Mandatsreferenz 1
			[222] => SEPA-Mandatsreferenz 2
			[223] => SEPA-Mandatsreferenz 3
			[224] => SEPA-Mandatsreferenz 4
			[225] => SEPA-Mandatsreferenz 5
			[226] => SEPA-Mandatsreferenz 6
			[227] => SEPA-Mandatsreferenz 7
			[228] => SEPA-Mandatsreferenz 8
			[229] => SEPA-Mandatsreferenz 9
			[230] => SEPA-Mandatsreferenz 10
			[231] => Verknüpftes OPOS-Konto
			[232] => Mahnsperre bis
			[233] => Lastschriftsperre bis
			[234] => Zahlungssperre bis
			[235] => Gebührenberechnung
			[236] => Mahngebühr 1
			[237] => Mahngebühr 2
			[238] => Mahngebühr 3
			[239] => Pauschalberechnung
			[240] => Verzugspauschale 1
			[241] => Verzugspauschale 2
			[242] => Verzugspauschale 3
			[243] =>
			)
			 */
			if (empty($v['kunden_order_read_firma']) || strlen($v['kunden_order_read_firma'])<2) {
				$v['kunden_order_read_firma'] = $v['kunden_order_vorname']." ".$v['kunden_order_name'];
			}
			$datev_array['Konto']=$v['kunden_order_user_id'];
			$datev_array['Name (Adressatentyp Unternehmen)']=$v['kunden_order_read_firma'];
			$datev_array['Name (Adressatentyp natürl. Person)']=$v['kunden_order_name'];
			$datev_array['Vorname (Adressatentyp natürl. Person)']=$v['kunden_order_vorname'];
			$datev_array['Postleitzahl']=$v['kunden_order_postleitzahl'];
			$datev_array['Ort']=$v['kunden_order_readr_ort'];
			$datev_array['EU-USt-IdNr.']=$v['kunden_order_customers_vat_id'];
			$datev_array['Land']=$v['kunden_order_land'];

			//$datev_array['Buchungstext']=$v['kunden_order_vorname']." ".$v['kunden_order_name'];
			//if (empty($datev_array['Buchungstext']))
			{
				//	$datev_array['Buchungstext'] = $v['kunden_order_firma'];
			}
			//Stndard Daten
			// $datev_array['WKZ Umsatz']="EUR";
			// }
			foreach ($datev_array as $xk=>$xv) {
				$csv.='"'.$xv.'";';
			}

			//foreach ($v as $k1=>$v1) {
			/**
			$csv.=$v['kunden_order_user_id'].
			",".$v['kunden_order_read_firma']
			.",".$v['kunden_order_vorname']
			.",".$v['kunden_order_name']
			.",".$v['kunden_order_postleitzahl']
			.",".$v['kunden_order_readr_ort']
			.",".$v['kunden_order_customers_vat_id'];
			// }
			 **/

			$csv.="\r\n";

		}
		return $csv;
	}

	/**
	 * @param string $csv
	 * @return string
	 */
	function save_file($csv="")
	{
		$pfad= "/interna/templates_c/standard/csv_".time().".csv";
		$this->diverse->write_to_file($pfad,$csv);

		return $pfad;
	}

	/**
	 * @param string $csv
	 * @return string
	 */
	function save_file_user($csv="")
	{
		$pfad= "/interna/templates_c/standard/csv_user_".time().".csv";
		$this->diverse->write_to_file($pfad,$csv);

		return $pfad;
	}

	/**
	 * @return array|void
	 */
	function get_faktura_data()
	{
		$stop=strtotime($this->checked->einstellungen_lang_inheit2)+86401;
		$start=strtotime($this->checked->einstellungen_lang_ie_letzten);

		$sql=sprintf("SELECT * FROM %s LEFT JOIN %s
                  ON order_id=kunden_order_id
                  WHERE
                    order_order_date < '%d'
                    AND order_order_date > '%d'
                    AND order_typ= 6
                    OR
                     order_order_date < '%d'
                    AND order_order_date > '%d'
                    AND order_typ= 7
                    ",

			DB_PRAEFIX."plugin_shop_order",
			DB_PRAEFIX."plugin_shop_order_lookup_kunde",
			$stop,
			$start,
			$stop,
			$start
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	/**
	 * @param array $data
	 * @return string
	 */
	function create_csv($data=array())
	{
		// VOrlage
		/**
		 * Umsatz (ohne Soll/Haben-Kz),Soll/Haben-Kennzeichen,Konto,Gegenkonto (ohne BU-Schlüssel),BU-Schlüssel,Belegdatum,Belegfeld 1,Belegfeld 2,Skonto,Buchungstext
		119000,S,8400,50000,,3110,2014001,,,Umsatz 1
		11900,S,8400,50100,,3110,2014002,,,Umsatz 2
		1190,S,8400,50200,,3110,2014003,,,Umsatz 3
		119,S,8400,50300,,3110,2014004,,,Umsatz 4
		 */

		//$csv='Umsatz (ohne Soll/Haben-Kz),Soll/Haben-Kennzeichen,Konto,Gegenkonto (ohne BU-Schlüssel),BU-Schlüssel,Belegdatum,Belegfeld 1,Belegfeld 2,Skonto,Buchungstext'."\n";
		$csv='"Umsatz (ohne Soll/Haben-Kz)";"Soll/Haben-Kennzeichen";"WKZ Umsatz";"Kurs";"Basisumsatz";"WKZ Basisumsatz";"Konto";"Gegenkonto (ohne BU-Schlüssel)";"BU-Schlüssel";"Belegdatum";"Belegfeld 1";"Belegfeld 2";"Skonto";"Buchungstext";"Postensperre";"Diverse Adressnummer";"Geschäftspartnerbank";"Sachverhalt";"Zinssperre";"Beleglink";"Beleginfo – Art 1";"Beleginfo – Inhalt 1";"Beleginfo – Art 2";"Beleginfo – Inhalt 2";"Beleginfo – Art 3";"Beleginfo – Inhalt 3";"Beleginfo – Art 4";"Beleginfo – Inhalt 4";"Beleginfo – Art 5";"Beleginfo – Inhalt 5";"Beleginfo – Art 6";"Beleginfo – Inhalt 6";"Beleginfo – Art 7";"Beleginfo – Inhalt 7";"Beleginfo – Art 8";"Beleginfo – Inhalt 8";"KOST1 – Kostenstelle";"KOST2 – Kostenstelle";"Kost Menge";"EU-Land u. USt-IdNr.";"EU-Steuersatz";"Abw. Versteuerungsart";"Sachverhalt L+L";"Funktionsergänzung L+L";"BU 49 Hauptfunktionstyp";"BU 49 Hauptfunktionsnummer";"BU 49 Funktionsergänzung";"Zusatzinformation – Art 1";"Zusatzinformation – Inhalt 1";"Zusatzinformation – Art 2";"Zusatzinformation – Inhalt 2";"Zusatzinformation – Art 3";"Zusatzinformation – Inhalt 3";"Zusatzinformation – Art 4";"Zusatzinformation – Inhalt 4";"Zusatzinformation – Art 5";"Zusatzinformation – Inhalt 5";"Zusatzinformation – Art 6";"Zusatzinformation – Inhalt 6";"Zusatzinformation – Art 7";"Zusatzinformation – Inhalt 7";"Zusatzinformation – Art 8";"Zusatzinformation – Inhalt 8";"Zusatzinformation – Art 9";"Zusatzinformation – Inhalt 9";"Zusatzinformation – Art 10";"Zusatzinformation – Inhalt 10";"Zusatzinformation – Art 11";"Zusatzinformation – Inhalt 11";"Zusatzinformation – Art 12";"Zusatzinformation – Inhalt 12";"Zusatzinformation – Art 13";"Zusatzinformation – Inhalt 13";"Zusatzinformation – Art 14";"Zusatzinformation – Inhalt 14";"Zusatzinformation – Art 15";"Zusatzinformation – Inhalt 15";"Zusatzinformation – Art 16";"Zusatzinformation – Inhalt 16";"Zusatzinformation – Art 17";"Zusatzinformation – Inhalt 17";"Zusatzinformation – Art 18";"Zusatzinformation – Inhalt 18";"Zusatzinformation – Art 19";"Zusatzinformation – Inhalt 19";"Zusatzinformation – Art 20";"Zusatzinformation – Inhalt 20";"Stück";"Gewicht";"Zahlweise";"Forderungsart";"Veranlagungsjahr";"Zugeordnete Fälligkeit";"Skontotyp";"Auftragsnummer";"Buchungstyp";"USt-Schlüssel (Anzahlungen)";"EU-Mitgliedstaat (Anzahlungen)";"Sachverhalt L+L (Anzahlungen)";"EU-Steuersatz (Anzahlungen)";"Erlöskonto (Anzahlungen)";"Herkunft-Kz";"Leerfeld";"KOST-Datum";"SEPA-Mandatsreferenz";"Skontosperre";"Gesellschaftername";"Beteiligtennummer";"Identifikationsnummer";"Zeichnernummer";"Postensperre bis";""Bezeichnung";"SoBil-Sachverhalt"";""Kennzeichen";"SoBil-Buchung"";"Festschreibung";"Leistungsdatum";""Datum Zuord.";"Steuerperiode";"OrderID"";'."\r\n";

		$csv_ar=explode(";",$csv);
		foreach ($csv_ar as $k=>$v) {
			$v = str_ireplace('"',"",$v);
			if (strlen($v)>1)
				$neu[$v]="";

		}
		/**
		[Umsatz (ohne Soll/Haben-Kz)] =>
		[Soll/Haben-Kennzeichen] =>
		[WKZ Umsatz] =>
		[Kurs] =>
		[Basisumsatz] =>
		[WKZ Basisumsatz] =>
		[Konto] =>
		[Gegenkonto (ohne BU-Schlüssel)] =>
		[BU-Schlüssel] =>
		[Belegdatum] =>
		[Belegfeld 1] =>
		[Belegfeld 2] =>
		[Skonto] =>
		[Buchungstext] =>
		[Postensperre] =>
		[Diverse Adressnummer] =>
		[Geschäftspartnerbank] =>
		[Sachverhalt] =>
		[Zinssperre] =>
		[Beleglink] =>
		[Beleginfo – Art 1] =>
		[Beleginfo – Inhalt 1] =>
		[Beleginfo – Art 2] =>
		[Beleginfo – Inhalt 2] =>
		[Beleginfo – Art 3] =>
		[Beleginfo – Inhalt 3] =>
		[Beleginfo – Art 4] =>
		[Beleginfo – Inhalt 4] =>
		[Beleginfo – Art 5] =>
		[Beleginfo – Inhalt 5] =>
		[Beleginfo – Art 6] =>
		[Beleginfo – Inhalt 6] =>
		[Beleginfo – Art 7] =>
		[Beleginfo – Inhalt 7] =>
		[Beleginfo – Art 8] =>
		[Beleginfo – Inhalt 8] =>
		[KOST1 – Kostenstelle] =>
		[KOST2 – Kostenstelle] =>
		[Kost Menge] =>
		[EU-Land u. USt-IdNr.] =>
		[EU-Steuersatz] =>
		[Abw. Versteuerungsart] =>
		[Sachverhalt L+L] =>
		[Funktionsergänzung L+L] =>
		[BU 49 Hauptfunktionstyp] =>
		[BU 49 Hauptfunktionsnummer] =>
		[BU 49 Funktionsergänzung] =>
		[Zusatzinformation – Art 1] =>
		[Zusatzinformation – Inhalt 1] =>
		[Zusatzinformation – Art 2] =>
		[Zusatzinformation – Inhalt 2] =>
		[Zusatzinformation – Art 3] =>
		[Zusatzinformation – Inhalt 3] =>
		[Zusatzinformation – Art 4] =>
		[Zusatzinformation – Inhalt 4] =>
		[Zusatzinformation – Art 5] =>
		[Zusatzinformation – Inhalt 5] =>
		[Zusatzinformation – Art 6] =>
		[Zusatzinformation – Inhalt 6] =>
		[Zusatzinformation – Art 7] =>
		[Zusatzinformation – Inhalt 7] =>
		[Zusatzinformation – Art 8] =>
		[Zusatzinformation – Inhalt 8] =>
		[Zusatzinformation – Art 9] =>
		[Zusatzinformation – Inhalt 9] =>
		[Zusatzinformation – Art 10] =>
		[Zusatzinformation – Inhalt 10] =>
		[Zusatzinformation – Art 11] =>
		[Zusatzinformation – Inhalt 11] =>
		[Zusatzinformation – Art 12] =>
		[Zusatzinformation – Inhalt 12] =>
		[Zusatzinformation – Art 13] =>
		[Zusatzinformation – Inhalt 13] =>
		[Zusatzinformation – Art 14] =>
		[Zusatzinformation – Inhalt 14] =>
		[Zusatzinformation – Art 15] =>
		[Zusatzinformation – Inhalt 15] =>
		[Zusatzinformation – Art 16] =>
		[Zusatzinformation – Inhalt 16] =>
		[Zusatzinformation – Art 17] =>
		[Zusatzinformation – Inhalt 17] =>
		[Zusatzinformation – Art 18] =>
		[Zusatzinformation – Inhalt 18] =>
		[Zusatzinformation – Art 19] =>
		[Zusatzinformation – Inhalt 19] =>
		[Zusatzinformation – Art 20] =>
		[Zusatzinformation – Inhalt 20] =>
		[Stück] =>
		[Gewicht] =>
		[Zahlweise] =>
		[Forderungsart] =>
		[Veranlagungsjahr] =>
		[Zugeordnete Fälligkeit] =>
		[Skontotyp] =>
		[Auftragsnummer] =>
		[Buchungstyp] =>
		[USt-Schlüssel (Anzahlungen)] =>
		[EU-Mitgliedstaat (Anzahlungen)] =>
		[Sachverhalt L+L (Anzahlungen)] =>
		[EU-Steuersatz (Anzahlungen)] =>
		[Erlöskonto (Anzahlungen)] =>
		[Herkunft-Kz] =>
		[Leerfeld] =>
		[KOST-Datum] =>
		[SEPA-Mandatsreferenz] =>
		[Skontosperre] =>
		[Gesellschaftername] =>
		[Beteiligtennummer] =>
		[Identifikationsnummer] =>
		[Zeichnernummer] =>
		[Postensperre bis] =>
		[Bezeichnung] =>
		[SoBil-Sachverhalt] =>
		[Kennzeichen] =>
		[SoBil-Buchung] =>
		[Festschreibung] =>
		[Leistungsdatum] =>
		[Datum Zuord.] =>
		[Steuerperiode] =>
		 **/

		//Durchgehen
		foreach ((array)$data as $k=>$v) {
			$datev_array=$neu;
			//Erstmal die Kundennummer
			if (strlen($v['kunden_order_user_id'])==4) {
				$v['kunden_order_user_id']="5".$v['kunden_order_user_id'];
			}
			if (strlen($v['kunden_order_user_id'])==3) {
				$v['kunden_order_user_id']="50".$v['kunden_order_user_id'];
			}
			if (strlen($v['kunden_order_user_id'])==2) {
				$v['kunden_order_user_id']="500".$v['kunden_order_user_id'];
			}

			/**
			 *
			 * ACHTUNG  - die Nummer 8400 muss angepasst werden
			 *
			 * 8400 = papoo
			 * 8401 = jitc
			 * 8402 = Msa
			 * 8403 = bal
			 * 8404 = cre
			 * 8404 = 301
			 *
			 */
			//foreach ($v as $k1=>$v1) {

			$konto="8401";
			$v['order_summe_brutto2'] = $v['order_summe_brutto'];

			if ($v['order_origin']=="FRONTEND") {
				$v['order_summe_brutto2']=$v['order_summe_netto'];
			}
			else {
				$dabrutto = $v['order_summe_netto']*1.19;
				$dabrutto = round($dabrutto,2);
				$v['order_summe_brutto2']=$dabrutto;
			}

			$laender = new shop_class_laender();
			$inGermany = (bool)$laender->check_match('DE', $v['kunden_order_land']);
			$inEU = (bool)$laender->check_match('EU', $v['kunden_order_land']);
			$hasVatId = !empty($v['kunden_order_customers_vat_id']);

			//$umsatz = $v['order_summe_netto'] *1.19;

			if (!$inGermany and $inEU and $hasVatId) {
				$umsatz = $v['order_summe_netto'];
				$konto="8125";
			}
			elseif (!$inGermany and !$inEU) {
				$umsatz = $v['order_summe_netto'];
				$konto="8338";
			}

			/**
			 * Umsatz (ohne Soll/Haben-Kz),Soll/Haben-Kennzeichen,Konto,Gegenkonto (ohne BU-Schlüssel),BU-Schlüssel,Belegdatum,Belegfeld 1,Belegfeld 2,Skonto,Buchungstext
			119000,S,8400,50000,,3110,2014001,,,Umsatz 1
			11900,S,8400,50100,,3110,2014002,,,Umsatz 2
			1190,S,8400,50200,,3110,2014003,,,Umsatz 3
			119,S,8400,50300,,3110,2014004,,,Umsatz 4

			 *
			 *
			 */ //$csv.=self::pzk($umsatz).",S,".$konto.",".$v['kunden_order_user_id'].",,".date("d.m.Y",$v['order_order_date']).",3110,".$v['order_order_number'].",,".$v['kunden_order_vorname']." ".$v['kunden_order_name'];

			//Belegdaten
			$datev_array['Umsatz (ohne Soll/Haben-Kz)']=self::pzk($umsatz);
			$datev_array['Soll/Haben-Kennzeichen']="S";
			$datev_array['Konto']=$konto;
			$datev_array['Gegenkonto (ohne BU-Schlüssel)']=$v['kunden_order_user_id'];
			$datev_array['BU-Schlüssel']="";
			$datev_array['Belegdatum']=date("d.m.Y",$v['order_order_date']);
			$datev_array['Belegfeld 1']="3110";
			$datev_array['Belegfeld 2']=$v['order_order_number'];
			$datev_array['Buchungstext']=$v['kunden_order_vorname']." ".$v['kunden_order_name'];
			if (empty($datev_array['Buchungstext'])) {
				$datev_array['Buchungstext'] = $v['kunden_order_firma'];
			}
			//Stndard Daten
			$datev_array['WKZ Umsatz']="EUR";
			$datev_array['OrderID']=$v['order_id'];


			// }
			foreach ($datev_array as $xk=>$xv) {
				$csv.='"'.$xv.'";';
			}
			$csv.="\r\n";
		}
		return $csv;
	}

	/**
	 * @param string $dat
	 * @return float|string
	 */
	static function pzk($dat="")
	{
		$dat=round($dat,2);
		//$dat = str_ireplace(".",",",$dat);
		return $dat;
	}

	/**
	 * @param string $csv
	 * @param string $ext
	 */
	private function download($csv="", $ext="csv")
	{
		$size = strlen($csv);

		// Header zuweisen
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Description: File Transfer");
		header("Accept-Ranges: bytes");
		// Mime-Type der Datei festestellen und Content-Type festlegen
		header("Content-Type: text/plain");
		header("Content-Length: $size ");
		header('Content-Disposition: attachment; filename="' . utf8_decode("datev_export_".time()) .'.'. ".$ext" .'"');

		// Send Content-Transfer-Encoding HTTP header
		// (use binary to prevent files from being encoded/messed up during transfer)
		header('Content-Transfer-Encoding: binary');

		// File auslesen
		echo $csv;

		// Wichtig, sonst gibt es Chaos!
		exit;
	}
}

$datev_export = new datev_export();
