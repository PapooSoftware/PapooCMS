<?php
/**

Deutsche Text-Daten des Plugins "newsletter" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['message_20001']="Newsletter verschicken";
$this->content->template['message_20001a']="Sprache: ";
//Betreff
$this->content->template['message_20002']="Betreff";
// Inhalt des Newsletters: 
$this->content->template['message_20003']=" Inhalt des Newsletters: ";
//Inhalt
$this->content->template['message_20004']="Alternative Text Nachricht";
//<h2>Newsletter Impressum bearbeiten</h2><p>Tragen Sie hier wichtige Daten in das Impressum des Newsletters ein.</p>
$this->content->template['message_20005']="Newsletter Einstellungen";
// Inhalt des Impressums:
$this->content->template['message_20006']=" Inhalt des Impressums:";
//Inhalt
$this->content->template['message_20007']="Inhalt";
$this->content->template['message_20008']="Newsletter Abonnentenverwaltung";
//neue E-Mail hinzufügen 
$this->content->template['message_20009']="Einen neuen Abonnenten hinzufügen";
//
$this->content->template['message_20010']="";
//Ja
$this->content->template['message_20011']="Ja ";
//Nein
$this->content->template['message_20012']="Nein";
//Aktiv
$this->content->template['message_20013']="Aktiv";
//E-Mail Adresse
$this->content->template['message_20014']="E-Mail-Adresse";
//E-Mail
$this->content->template['message_20015']="E-Mail";
//<h1>Newsletter bearbeiten</h1><p>Sie können hier den Newsletter bearbeiten, Abonnenten bearbeiten und das Impressum.</p>
$this->content->template['news_message_1']="<h2>Newsletter bearbeiten</h2><p>Sie können hier den Newsletter bearbeiten, Abonnenten bearbeiten und das Impressum.</p><p>Wenn sie den Newsletter einbinden möchten, dann können sie:<br/><ol><li>Einen Menüpunkt erstellen. Bei der Erstellung können Sie von Hand unter \"Einbindung des Links oder der Datei.\" den folgenden Eintrag hinzufügen: <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>Wenn Sie keinen eigenen Menüpunkt haben wollen, können Sie in jedem beliebigen Artikel einen Link erstellen durch die Linkfunktion im Editor. Der Link sollte den folgenden Inhalt haben: /plugin.php?menuid=1&template=newsletter/templates/subscribe_newsletter.html .<br /></li><li>Außerdem können Sie den Modulmanager nutzen um das Anmeldeformular an beliebiger Stelle einzubinden. <br /></li><li>Weiterhin können Sie auch ein Archiv auf Ihrer Seite einbinden mit dem folgenden Link in einem Menüpunkt:<br /><strong>plugin:newsletter/templates/news_archiv.html</strong>
</li><li>Mit dem Platzhalter #Online_Link# können Sie auf den Archiveintrag verlinken auf der Webseite. Dort wird automatisch der korrekte Link eingetragen.</li><li>F&uuml;r den Newsletter können folgende Platzhalter verwendet werden : #title# (Anrede) #name# (Nachname) #Newsletter_Kuendigen# (K&uuml;ndigungslink)</li></ol>";
//<h2>Der Newsletter wurde verschickt!</h2>
$this->content->template['news_message_2']="<h2 style=\"color:red;\">Der Newsletter wurde verschickt.</h2>";
//<h2>Newsletter speichern!</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabhängig von der generellen Speicherung.</p>
$this->content->template['news_message_3']="<h2>Newsletter speichern</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabhängig von der generellen Speicherung.</p>";
//Newsletter speichern
$this->content->template['news_message_4']="Newsletter speichern";
//E-Mail Adresse
$this->content->template['message_20016']="E-Mail Adresse mit der gesendet wird:";
$this->content->template['message_20016a']="Diverse Einstellungen";
//Name im From E-Mail Adresse
$this->content->template['message_20017']="Der Name für den *von:* Teil:";
/**
 * <h2>Newsletter abonnieren.</h2>
<p>Sie können hier den Newsletter abonnieren. Dazu füllen Sie bitte das untenstehende Formular aus. Sie bekommen <strong>anschließend</strong> eine BestätigungsE-Mail, die sie beantworten <strong>müssen</strong>.</p>
<p>Erst dann sind Sie für den Newsletter angemeldet.</p>
 */
$this->content->template['message_20018']="<p>Sie können hier unseren Newsletter abonnieren. Dazu füllen Sie bitte das untenstehende Formular aus. Sie bekommen anschließend eine Bestätigungs-E-Mail, die Sie beantworten müssen.</p>
<p>Erst dann sind Sie für den Newsletter angemeldet.</p>";
$this->content->template['message_20018_1']="Newsletter-Archiv";
$this->content->template['message_20018_a']="nodecode:<h2>Newsletter abonnieren.</h2>";
//Ihre Daten bitte eingeben 
$this->content->template['message_20019']="Ihre Daten bitte eingeben.";
//Newsletter abonnieren
$this->content->template['message_20020']="Newsletter abonnieren";
//verschicken
$this->content->template['message_20021']="Verschicken";
//verschicken
$this->content->template['message_20021d']="Verschicken an die folgenden Verteilerliste";
//verschicken
$this->content->template['message_20021c']="Vorschau";
//verschicken
$this->content->template['message_20021a']="Korrigieren";
$this->content->template['newsmessage_20122']="Datei Anhänge hinzufügen";
$this->content->template['newsmessage_20122a']="Angehängte Dateien";
$this->content->template['message_20023']="Der Betreff fehlt.";
$this->content->template['message_20024']="Neuen Newsletter erstellen";
$this->content->template['message_20025']="Die Nachricht fehlt.";
$this->content->template['message_20026']="Sprache nicht ausgewählt.";
$this->content->template['message_20027']="Neue Newsletter-Verteilerliste erstellen";
$this->content->template['message_21027']="Verteilerliste im Frontend anzeigen?";
$this->content->template['message_21028']="Verteilerliste moderiert?";
$this->content->template['message_20028']="Alle Abonnenten inkl. System-Verteilerlisten";
$this->content->template['message_20029']="Alle Newsletter-Verteilerlisten";
$this->content->template['message_20030']="System-Verteilerlisten";
$this->content->template['message_20030a']=" und Flex-Suchergebnis";
$this->content->template['message_20031']="Newsletter Verteilerlisten";
$this->content->template['message_20032']="Keine Verteilerliste angegeben";
$this->content->template['message_20033']='Soll die Newsletter Verteilerliste ';
$this->content->template['message_20034']=' wirklich gelöscht werden?';
$this->content->template['message_20035']='Soll der Newsletter ';
$this->content->template['message_20036']='Aktive Abonnenten ';
$this->content->template['message_20037']='Soll der Abonnent ';
$this->content->template['message_20038']='"Alle..." oder einzelne Verteilerlisten dürfen nur ausgewählt werden';
$this->content->template['message_20039']='"Die Verteilerliste Test darf nur als einzige ausgewählt werden.';
$this->content->template['message_20040']='"Abonnenten';
$this->content->template['message_20041']='Zum Testversand eines Newsletters k&ouml;nnen Sie sich die Verteilerliste "Test" einrichten. Nur diejenigen, die Sie dieser Verteilerliste zuordnen, erhalten den an die Verteilerliste "Test" versendeten Newsletter als Vorschau. Die Verteilerliste "Test" wird nicht im Frontend angezeigt, daher ist eine Anmeldung im Frontend an diese Verteilerliste nicht m&ouml;glich. Die versendeten Test-Newsletter werden auch nicht im Newsletter-Archiv im Frontend angezeigt.';
$this->content->template['message_20042']='Newsletter-Empfang aktivieren';
$this->content->template['message_20043']='Newsletter-Empfang deaktivieren';
$this->content->template['message_20044']='Der Buchstabe "A" vor dem Anmeldedatum kennzeichnet einen Abonnenten, der vom Admin eingegeben wurde..<br />Der Buchstabe "I" vor dem Anmeldedatum kennzeichnet einen Abonnenten, der via Addressen-Import eingefügt wurde.';//Erneut versenden.
$this->content->template['erneut_versenden']="Erneut versenden.";

$this->content->template['datum']="Erstellt";
$this->content->template['senddate']="Gesendet";
$this->content->template['kundensuchen']="Newsletter Abonnenten suchen";
//Erneut versenden.
$this->content->template['useranzahl']="# Abonn.";
//Verteilerliste
$this->content->template['gruppe']="Verteilerliste";
$this->content->template['newsletter_texthtml']="HTML-WYSIWYG";
//news_message1
$this->content->template['news_message1']="<h2>Wählen Sie eine Sprache aus</h2><p>Wählen Sie hier die Sprache in der ein Newsletter erstellt werden soll.</p>";
//Auswählen
$this->content->template['news_message2']="Auswählen";
$this->content->template['news_loeschen']="Löschen";
$this->content->template['news_loeschene']="Diesen Newsletter löschen";
$this->content->template['news_grp_loeschene']="Diese Newsletter Verteilerliste löschen";
$this->content->template['news_edit']="Edit";
$this->content->template['news_edite']="Diesen Newsletter bearbeiten";
$this->content->template['news_grpname']="Newsletter Verteilerliste";
$this->content->template['news_grpnamen']="Newsletter Verteilerlisten";
$this->content->template['news_grpdescript']="Beschreibung";
$this->content->template['news_grpfehlt']="Es wurde keine Verteilerliste ausgewählt";
$this->content->template['grp_edite']="Diese Newsletter Verteilerliste bearbeiten";
$this->content->template['abo_loeschene']="Diesen Abonnent löschen";
$this->content->template['abo_edite']="Abonnenten Einstellungen bearbeiten";
//news_is_del
$this->content->template['message_news_is_del']="Der Eintrag wurde erfolgreich gelöscht.";
$this->content->template['message_news_not_del']="Diese Verteilerliste kann nicht bearbeitet oder gelöscht werden.";
/**
 * -- \n\r Um den Newsletter zu kündigen, klicken Sie bitte hier:\n\r http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=1&activate=$key&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html \n\r
 */
// Fuer Text NL
$this->content->template['news_imptext1']="\r\n-- \r\n Um den Newsletter zu kündigen, klicken Sie bitte hier:\r\n http://#url#/plugin.php?menuid=1&activate=#key#&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html \r\n#imp#";
/**
 * -- <span style=\"color:#999999;font-size:80%;\"><br />" .
							" Um den Newsletter zu kündigen, klicken Sie bitte hier:<br />" .
							" <a href=\"http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=1&activate=$key&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html\">Newsletter kündigen</a>
 */
// fuer HTML NL
$this->content->template['news_imptext2']="<hr/>\r\n" .
							' Um den Newsletter zu kündigen, klicken Sie bitte hier:<br />' .
							' <a href="http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html" rel="unsubscribe nofollow">Newsletter kündigen</a><br />';//Newsletter von ".$this->cms->title_send." abonniert.
$this->content->template['news_mail1']="Newsletter von seitenurl abonniert.";
/**
 * Sie haben den Newsletter von ".$this->cms->title_send." abonniert. \n\r Wenn Sie diesen Newsletter nicht abonniert haben oder Ihn nicht wollen, ignorieren Sie diese Mail, Sie werden keine weitere bekommen. Um den Newsletter zu aktivieren bitte klicken Sie auf den folgenden Link:\n\r
 */
$this->content->template['news_mail2']="Sie haben den Newsletter von seitenurl abonniert. \n\r Wenn Sie diesen Newsletter nicht abonniert haben oder Ihn nicht wollen, ignorieren Sie diese Mail, Sie werden keine weitere bekommen. Um den Newsletter zu aktivieren bitte klicken Sie auf den folgenden Link:\n\r";
$this->content->template['news_mail3']="Ein neuer Abonnent hat sich für ein oder mehrere moderierte Listen eingetragen:\n\r";
//Sprache/Language
$this->content->template['news_front1']="<h2>Newsletter abonniert</h2><p>Sie haben unseren Newsletter abonniert. Sie sollten in wenigen Minuten eine E-Mail mit einem Bestätigungslink bekommen.</p><p>Bitte klicken Sie auf den Link in der E-Mail um diesen Newsletter endgültig zu bestellen.</p>";
//Sprache/Language
$this->content->template['news_front2']="<h2>Newsletter </h2><p>Ihr Abonnement unseres Newsletters wurde aktiviert. Sie werden ab heute unseren Newsletter beziehen. Wenn Sie den Newsletter kündigen wollen, klicken Sie einfach den Link zum Löschen des Newsletters in einer von uns empfangenen E-Mail.</p>";
//Sprache/Language
$this->content->template['news_front3']="<h2>Newsletter storniert</h2>','<p>Der Newsletter ist storniert und Ihre Daten wurden gelöscht.</p>";
//Sprache/Language
$this->content->template['news_front4']="Ihre Daten";
//Sprache/Language
$this->content->template['news_front5']="Herr";
//Sprache/Language
$this->content->template['news_front6']="Frau";
//Sprache/Language
$this->content->template['news_front7']="Vorname";
//Sprache/Language
$this->content->template['news_front8']="Nachname";
//Sprache/Language
$this->content->template['news_front9']="Strasse und Hausnummer";
//Sprache/Language
$this->content->template['news_front10']="Postleitzahl";
//Sprache/Language
$this->content->template['news_front11']="Wohnort";
//Sprache/Language
$this->content->template['news_front12']="Sprache";
//Sprache/Language
$this->content->template['news_front13']="Staat";
//Sprache/Language
$this->content->template['news_front14']=" Angabe fehlt";
//Sprache/Language
$this->content->template['news_front15']=" Ungültige Angabe";
//Sprache/Language
$this->content->template['news_front16']=" schon vorhanden. Der Abonnent wurde den ausgewählten Verteilern zugewiesen.";
//Sprache/Language
$this->content->template['news_front17']="IAKS-Mitglied";
//Sprache/Language
$this->content->template['news_front18']="sb Abonnent";
$this->content->template['news_front19']="Firma";
$this->content->template['news_show_recipients']="Mail-Adressen anzeigen, an die der Newsletter versendet wurde.";

//Sprache/Language
$this->content->template['news_message3']="Sprache";
//message_aboeintragen
$this->content->template['message_aboeintragen']="Abonnenten Einstellungen eintragen/ändern";
$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
$this->content->template['plugin']['newsletter']['allow_delete'] = 'Ist dieser Schalter gesetzt, werden Abonnenten unwiederbringlich gelöscht (manuell oder durch eine Abmeldung vom Newsletter), andernfalls wird ein Abonnent lediglich als gelöscht gekennzeichnet und nicht mehr zur Bearbeitung bereit gestellt. Letzteres dient dem vom Gesetzgeber verlangten Nachweis.';
$this->content->template['plugin']['newsletter']['altnewsletter'] = 'Newsletter Verwaltung';
$this->content->template['plugin']['newsletter']['inhalt_text'] = 'Inhalt als Text';
$this->content->template['plugin']['newsletter']['inhalt_html'] = 'Inhalt als HTML';
$this->content->template['plugin']['newsletter']['userdaten'] = 'Erweiterte Userdaten';
$this->content->template['plugin']['newsletter']['sprachwahl'] = 'Sprachwahl ermöglichen bei der Newsletter-Anmeldung?';
$this->content->template['plugin']['newsletter']['text'] = 'Text oberhalb der Anmeldung anzeigen?';
$this->content->template['plugin']['newsletter']['html_mails'] = 'HTML mails?';
$this->content->template['plugin']['newsletter']['editor'] = 'WYSIWYG Editor tinymce?';
$this->content->template['plugin']['newsletter']['sprache'] = 'Sprache/Language';
$this->content->template['plugin']['newsletter']['daten'] = 'Die Daten.';
$this->content->template['plugin']['newsletter']['vorname'] = 'Vorname';
$this->content->template['plugin']['newsletter']['nachname'] = 'Nachname';
$this->content->template['plugin']['newsletter']['strasse'] = 'Strasse und Hausnummer';
$this->content->template['plugin']['newsletter']['postleitzahl'] = 'Postleitzahl';
$this->content->template['plugin']['newsletter']['wohnort'] = 'Wohnort';
$this->content->template['plugin']['newsletter']['staat'] = 'Staat';
$this->content->template['plugin']['newsletter']['phone'] = 'Telefon';
$this->content->template['plugin']['newsletter']['speichern'] = 'Eintragen';
$this->content->template['plugin']['newsletter']['email'] = 'E-Mail';
$this->content->template['plugin']['newsletter']['eingabe_datei'] = 'Eingabe der Datei:';
$this->content->template['plugin']['newsletter']['dokument'] = 'Das Dokument:';
$this->content->template['plugin']['newsletter']['durchsuchen'] = 'Durchsuchen...';
$this->content->template['plugin']['newsletter']['datei_upload'] = 'Datei hochladen:';
$this->content->template['plugin']['newsletter']['upload'] = 'hochladen';
$this->content->template['plugin']['newsletter']['sicherung'] = '<h3>Sicherung der Datenbank erstellen</h3><p> Sie können hier eine Sicherung der Datenbank erstellen, die Sie nach einer Neuinstallation oder zu einem beliebigen anderen Zeitpunkt wieder einspielen können.</p>';
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = 'Eine Sicherung einspielen';
$this->content->template['plugin']['newsletter']['sicherung_ready'] = 'Sicherungs Datei wurde eingespielt.';
$this->content->template['plugin']['newsletter']['hinweis'] = 'Um eine Sicherung einzuspielen wählen Sie bitte die Sicherungsdatei aus:';
$this->content->template['plugin']['newsletter']['warnung'] = 'ACHTUNG - Wenn Sie eine Sicherung einspielen werden alle aktuellen Daten unwiederruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung!';
$this->content->template['plugin']['newsletter']['make_dump'] = 'Jetzt eine Sicherung erstellen';
$this->content->template['plugin']['newsletter']['anzahlgef'] = 'Anzahl der gefundenen Abonnenten:';
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = 'Anzahl der gefundenen Verteilerlisten:';
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = 'Anzahl der gefundenen Newsletter:';
$this->content->template['plugin']['newsletter']['asc'] = 'aufsteigend';
$this->content->template['plugin']['newsletter']['desc'] = 'absteigend';
$this->content->template['plugin']['newsletter']['sort'] = 'Sortierung';
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = 'Ihr Suchbegriff';
$this->content->template['plugin']['newsletter']['aktivjn'] = 'Aktiviert';
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = 'Newsletter Abonnenten';
$this->content->template['plugin']['newsletter']['Anrede'] = 'Anrede';
$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
$this->content->template['plugin']['newsletter']['groups'] = 'Newsletter Verteilerlisten-Verwaltung';
$this->content->template['plugin']['newsletter']['alle'] = 'Alle';
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = 'Das Attachment wurde für diesen Newsletter bereits hochgeladen.';
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = 'Datei nicht gefunden.';
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = 'Dateiname des Attachment fehlt.';
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = 'Löschen Attachment:';
$this->content->template['plugin']['newsletter']['label']['language'] = 'Wählen Sie die Sprachen, die bei der Newsletter-Anmeldung zur Auswahl stehen sollen.';
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = 'Attachment in neuem Fenster anzeigen.';
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = 'Die Datei wurde als Attachment hochgeladen.<br />Bitte speichern Sie noch alle Änderungen.';
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = 'Das Attachment wurde gelöscht.<br />Bitte speichern Sie noch alle Änderungen.';
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = 'Ihre Newsletterdaten wurden gespeichert.';
$this->content->template['plugin']['newsletter']['registration'] = 'Anmeldung';
$this->content->template['plugin']['newsletter']['submit']['cancel'] = 'Abbrechen';
$this->content->template['plugin']['newsletter']['submit']['save'] = 'Speichern';
$this->content->template['plugin']['newsletter']['submit']['send'] = 'Versenden';
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = 'Hinweis: Die jeweils angezeigte Anzahl ist die Anzahl der vorhandenen, jedoch ungeprüften Abonnenten-Einträge in der Datenbank. Beim Versand kommen evtl. vorhandene ungültige E-Mail-Adressen und doppelt vorhandene Adressen nicht zum Versand. Daher kann die in der Übersicht gezeigte Gesamtanzahl der Abonnenten, die den Newsletter erhalten, von den hier angegebenen Werten abweichen.';
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = 'Anzahl der E-Mails je Versand-Schritt:';
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = 'Das Hochladen von Dateianhängen ist erst nach der Eingabe des Betreffs und der Nachricht möglich.';
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = 'Eine oder mehrere Ihrer Dateien sind nur noch in der DB eingetragen, jedoch nicht mehr im Verzeichnis zu finden. Zur Beseitigung des Fehlers können Sie diese Datei(en) hier oder per FTP neu hochladen oder ggfs. sofort löschen. Beachten Sie, dass die Dateien beim Hochladen denselben Namen und dieselbe Grösse (letzteres nicht via FTP) haben müssen.';
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = 'Newsletter bearbeiten';
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = 'Hinweis: Attachments und das von Ihnen erstellte Impressum werden ebenfalls versendet.';
define( "NEWS_NL_SEND_TO_X_SUBSCRIBERS", "Newsletter an <strong>%s</strong> von <strong>%s</strong> Abonnenten versendet." );
define( 'NEWS_NL_SEND_NEXT_X_MAILS', 'Klicken Sie auf den Button "Versenden", um die nächsten %s E-Mails abzuschicken.<br />(Nach 10 Sekunden wird dies automatisch veranlasst.)');
$this->content->template['plugin_glossar_dubletten_entfernen']='Doppelte entfernen';
$this->content->template['plugin_newsletter_dubletten_entfernen_text']='Doppelte Mail-Adressen aus der Datenbank entfernen.';
$this->content->template['plugin_newsletter_dubletten_entfernen_field']='Doppelte entfernen';
$this->content->template['plugin_newsletter_import']='Adressen importieren';
$this->content->template['plugin_newsletter_export']='Adressen exportieren';
$this->content->template['plugin_newsletter_import_text']='Adressen importieren (CSV-Datei)';
$this->content->template['plugin_newsletter_export_text']='Adressen exportieren (CSV-Datei)';
$this->content->template['plugin_newsletter_inaktive_lschen']='Inaktive löschen';


$this->content->template['plugin_newsletter_blacklist_lschen']='Via Blacklist Import Abonnenten löschen';
$this->content->template['plugin_newsletter_inaktive_lschen_text']='Löscht alle inaktiven Abonnenten ohne Rückfrage!';
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen']='Inaktive Abonnenten löschen';
$this->content->template['plugin_newsletter_inaktive_geloescht']='Inaktive Abonnenten wurden gelöscht.';
$this->content->template['plugin_newsletter_dubletten_geloescht']='Doppelte Mail-Adressen wurden gelöscht.';
$this->content->template['plugin']['newsletter']['label']['timeout'] = 'Timeout-Schutz: Anzahl der Mails, die in 10 Sekunden-Intervallen auf einmal gesendet werden';
$this->content->template['plugin']['newsletter']['link']['grp_std'] = 'NL Verteilerliste Standard';
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = 'Standard NL Verteilerliste';
$this->content->template['plugin']['newsletter']['used_file'] = 'Dateiname';
$this->content->template['plugin']['newsletter']['size_text'] = 'Größe';
$this->content->template['plugin']['newsletter']['datum'] = 'Datum';
$this->content->template['plugin']['newsletter']['loeschen3'] = 'Löschen';
$this->content->template['plugin']['newsletter']['export'] = 'Export CSV';
$this->content->template['plugin']['newsletter']['header01'] = 'Hochgeladene Dateien';
$this->content->template['plugin']['newsletter']['datei_loeschen'] = 'Auswahl löschen';
$this->content->template['plugin']['newsletter']['eingabe_datei'] = 'Eingabe der Datei:';
$this->content->template['plugin']['newsletter']['das_dokument'] = 'Das Dokument:';
$this->content->template['plugin']['newsletter']['import_starten'] = 'Import starten';
$this->content->template['plugin']['newsletter']['datei_hochladen'] = 'Datei hochladen';
$this->content->template['plugin']['newsletter']['text03'] = 'Wenn Ihre Datei schon vorhanden ist, können Sie diese jetzt vor dem Import löschen, um Probleme beim Hochladen zu vermeiden.';
$this->content->template['plugin']['newsletter']['text04'] = 'Die 1. Zeile der Importdatei muss diese Feldernamen in beliebiger Reihenfolge enthalten: VORNAME, NAME, STRASSE, PLZ, ORT, MAIL. Die Importdatei muss eine CSV-Datei sein. Die Felder müssen mit HT (Tab) separiert sein (x09, \t), die Zeilen müssen mit CR LF beendet sein (x0D0A, \r\n).';
$this->content->template['plugin']['newsletter']['datei_importieren'] = '1. Schritt: Datei importieren';
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = '2. Schritt: Importieren';
$this->content->template['plugin']['newsletter']['liste_waehlen'] = 'Bitte w&auml;hlen Sie die Verteilerliste/n';
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = 'Verteilerliste/n beim Import leeren?';
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = 'Die Datei wurde erfolgreich hochgeladen.';
$this->content->template['plugin']['newsletter']['import_starten'] = 'Import starten';
$this->content->template['plugin']['newsletter']['importprotokoll'] = 'Importprotokoll';
$this->content->template['plugin']['newsletter']['importprotokoll3'] = 'Übersicht der Import-Fehlerprotokolle';
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = 'Datensätze wurden eingetragen.';
$this->content->template['plugin']['newsletter']['daten_del'] = 'Datensätze wurden gelöscht.';
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = 'Keine Datensätze eingetragen';
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = 'Datensätze nicht eingetragen';
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = 'Import-Fehlerprotokoll Übersicht';
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = 'Import-Fehlerprotokoll Details';
$this->content->template['plugin']['newsletter']['report_deleted'] = 'Fehlerprotokoll gelöscht';
$this->content->template['plugin']['newsletter']['id'] = 'Id';
$this->content->template['plugin']['newsletter']['import_time'] = 'Zeitpunkt';
$this->content->template['plugin']['newsletter']['normaler_user'] = 'User';
$this->content->template['plugin']['newsletter']['used_file'] = 'Dateiname';
$this->content->template['plugin']['newsletter']['records_to_import'] = 'Gesamt #';
$this->content->template['plugin']['newsletter']['error_count'] = 'Fehler #';
$this->content->template['plugin']['newsletter']['success_count'] = 'Erfolg #';
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = 'Details anzeigen';
$this->content->template['plugin']['newsletter']['linktext']['sync'] = 'Soll dieser Satz mit der Id ';
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = ' wirklich gelöscht werden?';
$this->content->template['plugin']['newsletter']['alttext']['sync'] = 'Dieses Fehlerprotokoll löschen';
$this->content->template['plugin']['newsletter']['error_count2'] = 'Anzahl Fehler insgesamt';
$this->content->template['plugin']['newsletter']['error_no'] = 'Lfd. #';
$this->content->template['plugin']['newsletter']['import_file_record_no'] = 'Satz #';
$this->content->template['plugin']['newsletter']['import_file_field_position'] = 'Feld #';
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = 'Excel-Pos.';
$this->content->template['plugin']['newsletter']['import_file_field_name'] = 'Feldname';
$this->content->template['plugin']['newsletter']['import_error_msg'] = 'Fehlermeldung';
$this->content->template['plugin']['newsletter']['completion_code'] = 'Code';
$this->content->template['plugin']['newsletter']['email_error'] = 'Keine valide E-Mail-Adresse';
$this->content->template['plugin']['newsletter']['max255_4'] = 'Die max. Länge der Eingabe von 255 Zeichen ist überschritten.';
$this->content->template['plugin']['newsletter']['email_schon_da'] = 'Diese E-Mail-Adresse ist schon vorhanden.';
$this->content->template['plugin']['newsletter']['feldanzahl'] = 'Es fehlt ein Feldname: VORNAME, NAME, STRASSE, PLZ, ORT, MAIL.';
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = 'Falscher Feldname: VORNAME, NAME, STRASSE, PLZ, ORT, MAIL..';


$this->content->template['newsletter_verteilerliste'] = 'Verteilerliste';

?>