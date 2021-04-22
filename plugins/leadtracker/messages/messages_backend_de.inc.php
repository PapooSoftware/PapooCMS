<?php
/**

Deutsche Text-Daten des Plugins "test" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

 $this->content->template['message']['plugin']['test']['name'] =
 'Plugin "Test"';

 $this->content->template['message']['plugin']['test']['kopf'] =
 '<h1>Backend des Test-Plugins</h1>'.
 '<p>Dieses Template ist zwar nicht barrierefrei, dafür aber sinnfrei und nicht X-HTML-konform. Nichts-desto-trotz sollte es zur Erklärung der Programmierung von Papoo-Plugins dienstreiche Hilfe leisten.</p>'.
 '<p>Die verschiedenen Menü-Punkte dieses Plugins sind ebenfalls sinnfrei. Sie verweisen immer auf dasselbe Template "test_back.html". Die Punkte sind nur um zu zeigen, wie in der Plugin-XML-Datei Menü-Punkte angelegt werden können.</p>'.
 '<p>Die Einbindung des Frontend-Templates geht wie folgt:
	Erstelle einen neuen Menü-Punkt. Gebt dort unter "Einbindung des Links oder der Datei." (ganz unten) Folgendes ein: <strong>plugin:test/templates/test_front.html</strong>. Damit steht das Template im Frontend zur Verfügung.</p>'.
 '<p>Die in diesem Template enthaltenen Module können mit dem Modul-Manager hier in der Administration eingefügt werden. Für alle die das Ding noch nicht entdeckt haben, zu finden ist er unter "System -> Modul-Manager".</p>';

 $this->content->template['message']['plugin']['test']['form_kopf'] =
 'Und hier ein kleines Formular:';

 $this->content->template['message']['plugin']['test']['form_legend'] =
 'Testwert';

 $this->content->template['message']['plugin']['test']['form_testwert_label'] =
 'Einen Testwert per POST übergeben';
 $this->content->template['plugin_test__test']='test';
 $this->content->template['plugin_test__leadtracker_plugin']='Leadtracker Plugin';
 $this->content->template['plugin_test__mit_dem_leadtracker']='Mit dem Leadtracker lassen sich vielf&auml;ltige Analyseformen auf der Webseite realisieren.';
 $this->content->template['plugin_test__downloaddateien_mit_formularen_verknpfen']='Downloaddateien mit Formularen verkn&uuml;pfen';
 $this->content->template['plugin_test__hier_bestimmen_sie_welche_ihrer_dateien']='Hier bestimmen Sie welche Ihrer Dateien Sie nutzen m&ouml;chten um Kontaktdaten zu generieren bzw. um FolloUp Mails automatisch zu versenden.';
 $this->content->template['plugin_test__nicht_verknpfte_dokumente']='Nicht verkn&uuml;pfte Dokumente';
 $this->content->template['plugin_test__verzeichnis_dl']='Verzeichnis';
 $this->content->template['plugin_test__name_dl']='Name der Datei';
 $this->content->template['plugin_test__typ_dl']='Typ';
 $this->content->template['plugin_test__aufrufe']='Aufrufe';
 $this->content->template['plugin_test__klicks__downloads']='Klicks / Downloads';
 $this->content->template['plugin_test__verknpfen']='Mit Formular verkn&uuml;pfen';
 $this->content->template['plugin_test__downloaddateien_mit_formularen_verknpfen2']='Ausgew&auml;hlte Downloaddatei mit Formular verkn&uuml;pfen';
 $this->content->template['plugin_test__die_ausgewhlte_datei']='Die ausgew&auml;hlte Datei:';
 $this->content->template['plugin_leadtracker_die_downloaddatei']='Downloaddatei';
 $this->content->template['plugin_leadtracker_verknpfen_mit']='Verkn&uuml;pfen mit:';
 $this->content->template['plugin_leadtracker_verknpfung_herstellen']='Verkn&uuml;pfung herstellen';
 $this->content->template['plugin_leadtracker_kann_muss']='Kann / Muss ausgef&uuml;llt werden';
 $this->content->template['plugin_leadtracker_verknpfung_speichern']='Verkn&uuml;pfung speichern';
 $this->content->template['plugin_leadtracker_dl_gre']='Gr&ouml;&szlig;e';
 $this->content->template['plugin_leadtracker_achtung_bitte_eintrge_korrigieren']='Achtung, bitte Eintr&auml;ge korrigieren';
 $this->content->template['plugin_leadtracker_daten_wurden_gespeichert']='Daten wurden gespeichert';
 $this->content->template['plugin_leadtracker_verknpfte_dokumente']='Verkn&uuml;pfte Dokumente';
 $this->content->template['plugin_leadtracker_verknpfung_lsen']='Verkn&uuml;pfung l&ouml;sen';
 $this->content->template['plugin_leadtracker_der_eintrag_wurde_gelscht']='Die Verkn&uuml;pfung wurde gel&ouml;st';
 $this->content->template['plugin_leadtracker_follow_up_mails']='Follow Up Mails';
 $this->content->template['plugin_leadtracker_hier_definieren_sie_die_aktionen']='Hier definieren Sie die Aktionen die Sie mit Follow Up Mails verkn&uuml;pfen wollen';
 $this->content->template['plugin_leadtracker_whlen_sie_dazu_einfach_eine']='W&auml;hlen Sie dazu einfach eine der unten stehenden Optionen aus und folgen Sie den Anweisungen auf der Seite.';
 $this->content->template['plugin_leadtracker_zustzlich_knnen_sie_hier_definieren_ob_sie_die_followup_mails_mit']='Zus&auml;tzlich k&ouml;nnen Sie hier definieren ob Sie die FollowUp Mails mit dem DoubleOptIn Verfahren durchf&uuml;hren wollen, das bedeutet die Empf&auml;nger m&uuml;ssen zuerst zustimmen ob Sie weitere Mails von Ihnen erhalten wollen. Die Best&auml;tigung wird dann im Account des jeweiligen Users dokumentiert und kann durch diesen auch jederzeit widerrufen werden.';
 $this->content->template['plugin_leadtracker_einstellungen_fum']='Einstellungen';
 $this->content->template['plugin_leadtracker_doubleoptin_nutzen']='DoubleOptIn nutzen';
 $this->content->template['plugin_leadtracker_follow_up_doi_nutzen']='DoubleOptIn nutzen';
 $this->content->template['plugin_leadtracker_follow_up_die_erste_mail_fr_das_jeweilige_doubleoptin_definieren_sie_immer']='Die erste Mail f&uuml;r das jeweilige DoubleOptIn definieren Sie immer f&uuml;r jeden Ablauf individuell.';
 $this->content->template['plugin_leadtracker_follow_up_einstellunge_speichern']='Einstellung speichern';
 $this->content->template['plugin_leadtracker_follow_up_mgliche_aktionen']='M&ouml;gliche Aktionen';
 $this->content->template['plugin_leadtracker_follow_up_whlen_sie_hier_die_aktion_aus']='W&auml;hlen Sie hier die Aktion aus die Sie mit einer Follow Up Mail verbinden wollen.';
 $this->content->template['plugin_leadtracker_follow_up_download_einer_datei']='Download einer Datei';
 $this->content->template['plugin_leadtracker_follow_up_aktion']='Aktion';
 $this->content->template['plugin_leadtracker_follow_up_followup_mail_generieren']='Follow Up Mails generieren / bearbeiten';
 $this->content->template['plugin_leadtracker_followup_mails_anzahl']='Follow Up Mails';
 $this->content->template['plugin_leadtracker_followup_mails_bearbeiten']='Mails bearbeiten';
 $this->content->template['plugin_leadtracker_follow_up_follow_up_mails_bearbeiten']='Follow Up Mails bearbeiten';
 $this->content->template['plugin_leadtracker_follow_up_bearbeiten_sie_hier_die_fum']='Bearbeiten Sie hier die Follow Up Mails f&uuml;r: ';
 $this->content->template['plugin_leadtracker_follow_up_bearbeiten_desc'] = 'Hier k&ouml;nnen Sie Follow Up Mails f&uuml;r das oben genannte Formular anlegen.';
 $this->content->template['plugin_leadtracker_follow_up_name_der_fum']='Bezeichnung der Mail';
 $this->content->template['plugin_leadtracker_follow_up_versand_nach_zeit']='Versand nach Zeit';
 $this->content->template['plugin_leadtracker_follow_up_bearbeiten_fum']='Bearbeiten';
 $this->content->template['plugin_leadtracker_follow_up_lschen_fum']='L&ouml;schen';
 $this->content->template['plugin_leadtracker_follow_up_neue_followup_mail_generieren']='Neue Follow Up Mail generieren';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_der_follow_up_mail']='Inhalt der Follow Up Mail';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc1'] = '<ul class="nobullets"><li>Hier k&ouml;nnen Sie eine Follow Up Mail erstellen.</li><li>Versand nach Tagen gibt an, wie viele Tage nach dem Ausf&uuml;hren des Formulars der Nutzer die Mail erhalten soll.</li><li>';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc2'] = 'Sie k&ouml;nnen den Versand einer Follow Up Mail vom setzen eines (bzw. von mehr als einem) Check-Replace abh&auml;ngig machen.</li><li>Hierzu klicken Sie auf das H&auml;kchen bei "aktivieren" und w&auml;hlen die entsprechende Option aus dem daraufhin erscheinenden Dropdownmenu aus.</li><li>';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc3'] = 'Betreff, Mail Inhalt Text und Mail Inhalt HTML stellen den Inhalt der Follow Up Mail dar (wobei Mail Inhalt Text nur gesendet wird, falls der Empf&auml;nger kein HTML darstellen kann).</li><li>Hier k&ouml;nnen Sie die angegebenen Platzhalter für die Formular';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc4'] = '- und Check-Replace';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc5'] = 'felder verwenden. Dieser wird dann beim Senden der Mail durch den vom Nutzer eingetragenen Inhalt ';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc6'] = '(bzw. durch die im Check-Replace-Feld hinterlegte Ersetzung) ';
 $this->content->template['plugin_leadtracker_follow_up_inhalt_desc7'] = 'ersetzt.</li></ul>';
 $this->content->template['plugin_leadtracker_follow_up_daten_der_followup_mail']='Daten der Follow Up Mail';
 $this->content->template['plugin_leadtracker_follow_up_mails_betreff_fum']='Betreff';
 $this->content->template['plugin_leadtracker_follow_up_cronjob_daten'] = 'Informationen zur Cronjob-Einrichtung';
 $this->content->template['plugin_leadtracker_follow_up_check_replace'] = 'Versand bei';

 $this->content->template['plugin_leadtracker_betreff_fum']='Betreff';
 $this->content->template['plugin_leadtracker_mail_inhalt_text']='Mail Inhalt Text';
 $this->content->template['plugin_leadtracker_mail_inhalt_html']='Mail Inhalt HTML';
 $this->content->template['plugin_leadtracker_id_von_follow_element']='id_von_follow_element';
 $this->content->template['plugin_leadtracker_type_von_follow_element']='type_von_follow_element';
 $this->content->template['plugin_leadtracker_daten_speichern_fum_maske']='Daten speichern';
 $this->content->template['plugin_leadtracker_zurck_zur_bersicht']='Zur&uuml;ck zur &Uuml;bersicht';

 $this->content->template['plugin_leadtracker_versand_nach_tagen']='Versand nach x Tagen:';
 $this->content->template['plugin_leadtracker_versand_nach']='Versand nach Tagen';
 $this->content->template['plugin_leadtracker_die_followup_mail_wurde_gelscht']='Die Follow Up Mail wurde gel&ouml;scht';
 $this->content->template['fum_for_data_text']="Datei ";
 $this->content->template['plugin_leadtracker_checkreplace_fum'] = 'Versand in Abh&auml;ngigkeit eines Check-Replace Feldes';
 $this->content->template['plugin_leadtracker_placeholders_checkreplace'] = 'Check-Replace';
 $this->content->template['plugin_leadtracker_placeholders_formfields'] = 'Formularfelder';
 $this->content->template['plugin_leadtracker_all_placeholders'] = 'Platzhalter f&uuml;r Formularinhalte ';
 $this->content->template['plugin_leadtracker_all_placeholders_clap'][0] = '(ausklappen)';
 $this->content->template['plugin_leadtracker_all_placeholders_clap'][1] = '(einklappen)';

 $this->content->template['plugin_leadtracker_follow_up_download_mehrerer_dateien_auf_einmal']='Formular mit Wiedererkennung';
 $this->content->template['plugin_leadtracker_formular_verknpfen']='Formular verkn&uuml;pfen';
 $this->content->template['plugin_leadtracker_sie_knnen_hier_ein_formular']='Sie k&ouml;nnen hier ein Formular in den Follow Up Mail Prozess aufnehmen - Aufgrund der Browsererkennung muss das Formular dann in der Regel nicht erneut ausgef&uuml;llt werden. <br />Der Besucher wird bei Aufruf der Seite dann automatisch auf die vorhandene Danke-Seite geleitet. <br />Sie k&ouml;nnen die automatische Formularbef&uuml;llung auch abschalten.';
 $this->content->template['plugin_leadtracker_verknpfte_formulare']='Verkn&uuml;pfte Formulare';
 $this->content->template['plugin_leadtracker_nicht_verknpfte_formulare']='Nicht verkn&uuml;pfte Formulare';
 $this->content->template['plugin_leadtracker_bezeichnung_des_formulars']='Bezeichnung des Formulars';
 $this->content->template['plugin_leadtracker_als_followup_mail_form_definieren']='Als Follow Up Mail Form definieren';
 $this->content->template['plugin_leadtracker_formular_fr_neuauswahl']='Formular f&uuml;r Neuauswahl';
 $this->content->template['plugin_leadtracker_formular_fr_neuauswahl_set']='Formular f&uuml;r Neuauswahl setzen';
 $this->content->template['plugin_leadtracker_das_formular_fr_neuauswahl_kommt_dann_zum_tragen_wenn_der']='Das Formular f&uuml;r Neuauswahl kommt dann zum tragen wenn der Besucher auf der Danke-Seite auf den Link Neu ausw&auml;hlen klickt. Standardm&auml;&szlig;ig wird das aktive Formular dann wieder mit den alten Daten gef&uuml;llt, dies will man aber oft vermeiden, daher kann man hier ein weiteres Formular zuweisen. ';
 $this->content->template['plugin_leadtracker_kopie_anlegen']='Wenn Sie dazu dann eine Kopie des Formulars anlegen und die Felder die nicht angezeigt werden sollen einfach auf den Typ Hidden stellen, werden die Daten trotzdem &uuml;bertragen, aber ohne dass der Besucher das sieht.';
 $this->content->template['plugin_leadtracker_formular_verknpfen_neu']='Formular verkn&uuml;pfen ';
 $this->content->template['plugin_leadtracker_das_formular_auswhlen']='Das Formular ausw&auml;hlen';
 $this->content->template['plugin_leadtracker_formular_fr_neuauswahl_form']='Formular f&uuml;r Neuauswahl';
 $this->content->template['plugin_leadtracker_zurck_zur_bersicht_formneu']='Zur&uuml;ck zur &Uuml;bersicht';
 $this->content->template['plugin_leadtracker_formular_direkt_neu_laden']='Soll das Formular direkt neu geladen werden bei Wiederaufruf (Neu Laden erfolgt nach 1h - vorher bleibt man auf der Danke-Seite)';
 $this->content->template['plugin_leadtracker_statistics_ueberschrift'] = 'Besucherstatistik';
 $this->content->template['plugin_leadtracker_statistics_gen_wait'] = 'Bitte warten Sie. Die Statistiken werden generiert&hellip;';
 $this->content->template['plugin_leadtracker_statistics_gen_success'] = 'Die Statistiken wurden erfolgreich generiert.';
 $this->content->template['plugin_leadtracker_statistics_gen_failed'] = 'Beim Generieren der Statistiken ist leider ein unbekannter Fehler aufgetreten.';
 $this->content->template['plugin_leadtracker_statistics_gen_step_1'] = 'L&ouml;schen bestehender Statistiken';
 $this->content->template['plugin_leadtracker_statistics_gen_step_2'] = 'Besuchsstatistiken sammeln';
 $this->content->template['plugin_leadtracker_statistics_gen_step_3'] = 'E-Mail-Adressen der Besucher ermitteln';
 $this->content->template['plugin_leadtracker_statistics_gen_step_4'] = 'Downloads z&auml;hlen';
 $this->content->template['plugin_leadtracker_statistics_gen_step_5'] = 'Generierung abschließen';
 $this->content->template['plugin_leadtracker_statistics_statistiken_neu_generieren'] = 'Statistiken neu generieren';
 $this->content->template['plugin_leadtracker_statistics_desc_statistiken_neu_generieren'] = '<p>Hier k&ouml;nnen Sie die Statistiken komplett neu generieren lassen. Dies ist gew&ouml;hnlich nur ein einziges Mal n&ouml;tig.</p><p><strong>Achtung: Der Vorgang kann je nach Gesamtzahl der Besuche Ihrer Website mehrere Minuten dauern!</strong></p>';
 $this->content->template['plugin_leadtracker_statistics_mailaddr'] = 'E-Mail-Adresse';
 $this->content->template['plugin_leadtracker_statistics_anzahl_visits'] = 'Anzahl Visits';
 $this->content->template['plugin_leadtracker_statistics_anzahl_forms'] = 'Anzahl Formularaufrufe';
 $this->content->template['plugin_leadtracker_statistics_aktion'] = 'Aktion';
 $this->content->template['plugin_leadtracker_statistics_letzte_interaktion'] = 'Letzte&nbsp;Interaktion';
 $this->content->template['plugin_leadtracker_statistics_suchen'] = 'Suchen';
 $this->content->template['plugin_leadtracker_statistics_keine_statistiken'] = 'Keine Statistiken vorhanden. Bitte f&uuml;hren Sie <strong>Statistiken neu generieren</strong> aus.';
 $this->content->template['plugin_leadtracker_statistics_keine_eintraege'] = 'Keine Eintr&auml;ge gefunden';
 $this->content->template['plugin_leadtracker_statistics_unbekannt'] = 'unbekannt';

 $this->content->template['plugin_leadtracker_statistics_details'] = 'Einzelansicht';
 $this->content->template['plugin_leadtracker_statistics_zurueck'] = 'Zur&uuml;ck';
 $this->content->template['plugin_leadtracker_statistics_formularanfragen'] = 'Formularanfragen';
 $this->content->template['plugin_leadtracker_statistics_besuchte_webseiten'] = 'besuchte Webseiten';
 $this->content->template['plugin_leadtracker_statistics_downloads'] = 'Downloads';
 $this->content->template['plugin_leadtracker_statistics_fums'] = 'Gesendete Follow Up Mails';
 $this->content->template['plugin_leadtracker_statistics_keine_daten'] = 'Keine Daten.';

 $this->content->template['plugin_leadtracker_statistics_datum'] = 'Datum';
 $this->content->template['plugin_leadtracker_statistics_download'] = 'Download';
 $this->content->template['plugin_leadtracker_statistics_link'] = 'Link';
 $this->content->template['plugin_leadtracker_statistics_keine_eintraege'] = 'Keine Eintr&auml;ge gefunden';
 $this->content->template['plugin_leadtracker_statistics_date_format'] = '%d.%m.%Y&nbsp;%H:%M:%S';

 $this->content->template['plugin_leadtracker_statistics_adwords'] = 'AdWords';
 $this->content->template['plugin_leadtracker_statistics_campid'] = 'Campaign ID';
 $this->content->template['plugin_leadtracker_statistics_grpid'] = 'AdGroup ID';
 $this->content->template['plugin_leadtracker_statistics_keyword'] = 'Keyword';

 $this->content->template['plugin_leadtracker_statistics_feld_1'] = '1. Feld';
 $this->content->template['plugin_leadtracker_statistics_feld_2'] = '2. Feld';
 $this->content->template['plugin_leadtracker_statistics_feld_3'] = '3. Feld';
 $this->content->template['plugin_leadtracker_statistics_alles_anzeigen'] = 'alles anzeigen';
 $this->content->template['plugin_leadtracker_statistics_url'] = 'URL';
 $this->content->template['plugin_leadtracker_statistics_referrer'] = 'Referrer';

 $this->content->template['plugin_leadtracker_statistics_formdetails'] = 'Formular-Einzeldaten';
 $this->content->template['plugin_leadtracker_statistics_feldname'] = 'Feldname';
 $this->content->template['plugin_leadtracker_statistics_feldwert'] = 'Feldwert';
 $this->content->template['plugin_leadtracker_statistics_metadaten'] = 'Metadaten';
 $this->content->template['plugin_leadtracker_statistics_schluessel'] = 'Schl&uuml;ssel';
 $this->content->template['plugin_leadtracker_statistics_wert'] = 'Wert';

 $this->content->template['plugin_leadtracker_statistics_standalone_title'] = 'Besucherstatistik';

 $this->content->template['plugin_leadtracker_followup_autorefill'] = 'Auto Formularbef&uuml;llung';
 $this->content->template['plugin_leadtracker_followup_submit'] = 'Speichern';

 $this->content->template['plugin_leadtracker_cronjob_ueberschrift'] = 'Leadtracker Cronjob';
 $this->content->template['plugin_leadtracker_cronjob_zwischenschrift'] = 'Mailversand ausf&uuml;hren';
 $this->content->template['plugin_leadtracker_cronjob_sendnow'] = 'E-Mails <strong>jetzt</strong> versenden';
 $this->content->template['plugin_leadtracker_cronjob_autocode'] = 'Link zur Automatisierung (Cronjob)';
 $this->content->template['plugin_leadtracker_cronjob_zurueck'] = 'Zur&uuml;ck';

 $this->content->template['plugin_leadtracker_followupoverview_ueberschrift'] = 'Alle ausstehenden Follow Up Mails';
 $this->content->template['plugin_leadtracker_followupoverview_searchformail'] = 'Nach E-Mail-Adresse suchen';
 $this->content->template['plugin_leadtracker_followupoverview_mailaddr'] = 'E-Mail-Adresse';
 $this->content->template['plugin_leadtracker_followupoverview_form'] = 'Formular';
 $this->content->template['plugin_leadtracker_followupoverview_mail'] = 'Follow Up Mail';
 $this->content->template['plugin_leadtracker_followupoverview_send'] = 'Sendetermin';
 $this->content->template['plugin_leadtracker_followupoverview_action'] = 'Aktion';
 $this->content->template['plugin_leadtracker_followupoverview_delete'] = 'L&ouml;schen';
 $this->content->template['plugin_leadtracker_followupoverview_isdone'] = 'Die Follow Up Mail wurde erfolgreich gel&ouml;scht.';
 $this->content->template['plugin_leadtracker_followupoverview_itfailed'] = 'Ausgew&auml;hlte Follow Up Mail konnte nicht gel&ouml;scht werden.';
 $this->content->template['plugin_leadtracker_followupoverview_nodata'] = 'Es sind aktuell keine ausstehenden Follow Up Mails im System hinterlegt.';
 $this->content->template['plugin_leadtracker_followupoverview_nomail1'] = "Es konnten keine Follow Up Mails zu <strong>";
 $this->content->template['plugin_leadtracker_followupoverview_nomail2'] = "</strong> gefunden werden.";
 #start#
 $this->content->template['plugin_leadtracker_achtung1']='<strong>ACHTUNG</strong> - Wenn Sie hier ein anderes Formular auswählen muss das zwingend auch der Liste der verknüpften Formulare zugeordnet werden!! ';

?>