<?php
#[AllowDynamicProperties]
class mv_config
{
	/**
	* Formulare mit brs zwischen label und Feld anzeigen
	* 1 = anzeigen
	* 0 = nicht anzeigen (dann mu� ein Float Mechanismus genutzt werden
	*/
	var $cfg = array(
						'showbrs' => '0',
						// Wieviel Treffer sollen bei der Suche auf einer Seite angezeigt werden
						'intervall_front' => null, // f�r mv_search_front_onemv.html, mv_show_all_front.html (Frontend)
						'intervall_back' => 20, // Mitglieder-/Objektsuche im Backend
						// maximale Bilderbreite und -h�he bei Bilduploads
						'pic_max_breite' => 850,
						'pic_max_hoehe' => 500,
						'is_mv_installed' => true,
						// Sonderfall: FelderIds von Feldern, bei denen Suchstring f�r einen Treffer am Anfang stehen muss
						// hier dzvhae PLZ Felder
						'search_from_beginn' => array(
											"20",
											"32"
											),
						// Sonderfall: 2 Timestampfelder IDs f�r dzvhae Import Datei
						'dzvhae_import_zeit_felder' => array(
											"3",
											"5"
											),
						// wenn Flag gesetzt dann ist es ein dzvhae Auftritt
						'dzvhae_system_id' => false,
						// welche Verwaltungs ID ist die dzvhae Mitlgiederverwaltung
						'dzvhae_mv_id' => 1,
						// Z�hler f�r den csv Import: 0 bedeutet das die Jahreszahlen hatten alle 4 Ziffern,
						// gr��erer Wert gibt die Anzahl der Importwerte an die nur 2 Ziffern hatten
						'import_timestamp' => 0,
						// wenn die Jahreszahl (2 Ziffern) kleiner als dieser Wert ist,
						// dann bekommt sie eine 20 vorangesetzt, ansonsten eine 19
						'import_timestamp_grenzwert' => 11,
						// Detailansicht im Frontend, von welchem Feld abh�ngig?
						'detail_feld_id' => 61,
						// Sonderfall dzvhae: welche Import Feld IDs haben diese Felder
						'dzvhae_feld_benutzername_id' => 1,
						'dzvhae_feld_vorname_id' => 13,
						'dzvhae_feld_nachname_id' => 16,
						'dzvhae_feld_system_id_id' => 93,
						'dzvhae_feld_flex_vorname' => 'Vorname_13',
						'dzvhae_feld_flex_nachname' => 'Nachname_16',
						'dzvhae_feld_flex_strasse' => 'Strasse_19',
						'dzvhae_feld_flex_plz' => 'PLZ_20',
						'dzvhae_feld_flex_ort' => 'Ort_21',
						'dzvhae_feld_flex_telvorwahl' => 'TelVorwahl_23',
						'dzvhae_feld_flex_telnummer' => 'TelNummer_24',
						'dzvhae_feld_flex_fax' => 'Fax_26',
						'dzvhae_feld_flex_landesverband' => 'Landesverband_63',
						//Das Feld bestimmt ob Eintr�ge sichtbar sind bei dzvhae, funktioniert nur wenn MV auf
						//dzvhae steht
						'dzvhae_feld_flex_sichtbar' => 'Sichtbar_65',
						'dzvhae_feld_flex_istmitglied' => 'istmitglied_67',
						'dzvhae_feld_flex_zahlungen' => 'Zahlungen_74',
						'dzvhae_feld_flex_mitgliedfeld1' => 'MitgliedFeld1_75',
						'dzvhae_feld_flex_mitgliedfeld2' => 'MitgliedFeld2_76',
						'dzvhae_feld_flex_mitgliedfeld3' => 'MitgliedFeld3_77',
						'dzvhae_feld_flex_mitgliedfeld4' => 'MitgliedFeld4_78',
						'dzvhae_feld_flex_mitgliedfeld5' => 'MitgliedFeld5_79',
						'dzvhae_feld_flex_mitgliedfeld6' => 'MitgliedFeld6_80',
						'dzvhae_feld_flex_zahlverfahren' => 'Zahlverfahren_85',
						'dzvhae_feld_flex_systemid' => 'SystemID_93',
						// Anzahl und Ids der Detailansichten
						'detail_anzahl' => array(
											"1",
											"2",
											"3"
											),
						// Meta ID f�r nicht eingeloggte Besucher
						'meta_id_no_login' => '1',
						// Meta ID f�r neu angemeldete Benutzer (ist jetzt dynamisch steuerbar �ber url extern_meta)
						'meta_id_new_user' => '5',
						// Anzeigen der Labels in der Auswahlliste
						'meta_include_label' => false,
						// Meta ID zur Auswahl f�r neu angemeldete Benutzer
						// hier 1, 5 f�r admin, neuer Benutzer
						'meta_id_choose_new_user' => array(
											"1",
											"5"
											),
						// Meta ID f�r die Systememails, wenn jemand sich neu anmeldet
						'mv_system_email_meta_id' => '1',
						// Soll die Lightbox genutzt werden bei den Einzelbildern 1=OK, 0=Nicht benutzen
						'mv_show_lightbox_single' => '1',
						// Soll die Lightbox genutzt werden bei den Galerien 1=OK, 0=Nicht benutzen
						'mv_show_lightbox_galerie' => '1',
						//Sortierungsrichtung bei Kalendereintr�gen ASC oder DESC mit Leerzeichen vorne dran!
						'kalender_order' => ' ASC',
						// Wenn ein Eintrag abgelaufen ist eine Mail rausschicken an den Eintr�ger (true/false)
						'mv_schick_mail_wenn_abgelaufen' => false,
						// Tage vor und nach dem Ablauf eine Mail schicken array(14,8,0,-1);
						'mv_abstand_mail_schicken' => array(
											8,
											0
											),
						//cc Felder
						#var $cc1 = "";
						#var $cc2 = "";
						//Ist nicht dzvhae zur Identifizierung normal/dzvhae Flex
						'is_not_dzvhae' => true,
						//Bei der Suche nach Objekten alle Metaebenen ber�cksichtigen
						'mv_meta_show_all' => 1,
						//Soll �berpr�ft werden ob die E-Mail Adresse schon existiert?
						'checken_ob_mail_existiert' => false,
						// Neue User werden neben "jeder" auch zu dieser Rechtegruppe hinzugef�gt (ID der Gruppe oder false)
						'zweite_default_gruppe' => false,
						'output_html_link_for_file_upload_field' => true,
						'download_protected' => false,
						'content_sperre_sprachabhaengig' => false,
						'checkbox_as_selectbox' => false,
						'mail_to_user_if_admin_creates' => false,
						'and_not_or_checkbox_search_fe' => true,
						'jahr_ende_displacement' => 0,
						'show_search_result_before_search' => false,
						'show_multiselect_values_no_br_single_view' => false,
						'show_textarea_linebreaks_single_view' => false,
						// Feld-ID f�r Standardsortierung
						// Kann auch ein Array in der Form array(mv_id => feld_id, ...) sein.
						'default_sort_field' => NULL
					);
}
