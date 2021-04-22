<?php
/**

Deutsche Text-Daten des Plugins "newsletter" für das Frontend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['message_20001']="<h2>Newsletter verschicken</h2>
<p>Sie können hier den Newsletter eingeben, den Sie verschicken möchten. Das Impressum, welches Sie erstellt haben, wird automatisch mit weitergeleitet.</p>";

//Betreff
$this->content->template['message_20002']="Betreff";
// Inhalt des Newsletters: 
$this->content->template['message_20003']=" Inhalt des Newsletters: ";
//Inhalt
$this->content->template['message_20004']="Inhalt als reiner Text";
//<h2>Newsletter Impressum bearbeiten</h2><p>Tragen Sie hier wichtige Daten in das Impressum des Newsletters ein.</p>
$this->content->template['message_20005']="<h2>Newsletter Impressum bearbeiten</h2>
<strong>Tragen Sie hier wichtige Daten in das Impressum des Newsletters ein.</strong>";
// Inhalt des Impressums:
$this->content->template['message_20006']=" Inhalt des Impressums:";
//Inhalt
$this->content->template['message_20007']="Inhalt";
/*<h2>Newsletter Abonnenten</h2>
<p>Sie sehen hier die Email Adressen der Abonnenten</p>
<p>Klicken Sie auf eine um Sie zu bearbeiten.</p>
<p>Um eine neue Adresse hinzuzufügen klicken Sie auf
*/
$this->content->template['message_20008']="<h2>Newsletter Abonnenten</h2>
<p>Sie sehen hier die E-Mail-Adressen der Abonnenten</p>
<p>Klicken Sie auf eine E-Mail-Adresse, um Sie zu bearbeiten.</p>
<p>Um eine neue Adresse hinzuzufügen klicken Sie auf";
//neue Email hinzufügen 
$this->content->template['message_20009']="neue E-Mail Hinzufügen ";
//
$this->content->template['message_20010']="";
//Ja
$this->content->template['message_20011']="Ja ";
//Nein
$this->content->template['message_20012']="Nein";
//Aktiv
$this->content->template['message_20013']="Aktiv";
//Email Adresse
$this->content->template['message_20014']="E-Mail-Adresse";
//Email
$this->content->template['message_20015']="Email";
//<h1>Newsletter bearbeiten</h1><p>Sie können hier den Newsletter bearbeiten, Abonnenten bearbeiten und das Impressum.</p>
$this->content->template['news_message_1']="<h1>Newsletter bearbeiten</h1><p>Sie können hier den Newsletter bearbeiten, Abonnenten bearbeiten und das Impressum.</p><p>Wenn sie den Newsletter einbinden möchten, dann können sie:<br/><ol><li>Einen Menüpunkt erstellen. Bei der Erstellung können Sie von Hand unter Formlink den folgenden Eintrag hinzufügen: <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html.</strong></li><li>Wenn Sie keinen eigenen Menüpunkt haben wollen, können Sie in jedem beliebigen Artikel einen Link erstellen durch die Linkfunktion im Editor. Der Link sollte den folgenden Inhalt haben: /plugin.php?menuid=1&template=newsletter/templates/subscribe_newsletter.html .</li><li>Außerdem können Sie den Modulmanager nutzen um das Anmeldeformular  an beliebiger Stelle einzubinden. </li></ol>";
//<h2>Der Newsletter wurde verschickt!</h2>
$this->content->template['news_message_2']="<h2 style=\"color:red;\">Der Newsletter wurde verschickt!</h2>";
//<h2>Newsletter speichern!</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabhängig von der generellen Speicherung.</p>
$this->content->template['news_message_3']="<h2>Newsletter speichern!</h2><p>Klicken Sie auf Newsletter speichern und alle relevanten Daten zum Newsletter werden in einer dump Datei gespeichert. Diese Speicherung ist unabhängig von der generellen Speicherung.</p>";
//Newsletter speichern
$this->content->template['news_message_4']="Newsletter speichern";
//Email Adresse
$this->content->template['message_20016']="E-Mail-Adresse mit der gesendet wird:";
//Name im From Email Adresse
$this->content->template['message_20017']="Der Name für den *von:* Teil:";
/**
 * <h2>Newsletter abonnieren.</h2>
<p>Sie können hier den Newsletter abonnieren. Dazu füllen Sie bitte das untenstehende Formular aus. Sie bekommen <strong>anschließend</strong> eine Bestätigungsemail, die sie beantworten <strong>müssen</strong>.</p>
<p>Erst dann sind Sie für den Newsletter angemeldet.</p>
 */
$this->content->template['message_20018']="<h3>Newsletter abonnieren.</h3><p>Sie können hier unseren Newsletter abonnieren. Dazu füllen Sie bitte das untenstehende Formular aus. Sie bekommen anschließend eine Bestätigungs-E-Mail, die Sie beantworten müssen.</p>
<p>Erst dann sind Sie für den Newsletter angemeldet.</p>";
$this->content->template['message_20018_ds']='Ich habe die Datenschutzerklärung zur Kenntnis genommen. Ich stimme zu, dass meine Angaben und Daten zur Beantwortung meiner Anfrage elektronisch erhoben und gespeichert werden. Hinweis: Sie können Ihre Einwilligung jederzeit für die Zukunft per E-Mail an info@ihre-mail.de widerrufen.';
$this->content->template['message_20018_1']="Newsletter-Archiv";
$this->content->template['message_20018_a']="Newsletter abonnieren.";
//Ihre Daten bitte eingeben 
$this->content->template['message_20019']="Ihre Daten bitte eingeben.";
//Newsletter abonnieren
$this->content->template['message_20020']="abonnieren";
//verschicken
$this->content->template['message_20021']="Verschicken";
//Erneut versenden.
$this->content->template['erneut_versenden']="Erneut versenden.";
//Erneut versenden.
$this->content->template['datum']="Datum";
//Erneut versenden.
$this->content->template['inhalt']="Inhalt";
//Erneut versenden.
$this->content->template['useranzahl']="Anzahl der Empfänger";
//Gruppe
$this->content->template['gruppe']="Gruppe";
//Gruppe
$this->content->template['newsletter_texthtml']="Inhalt als HTML";
//news_message1
$this->content->template['news_message1']="<h2>Wählen Sie eine Sprache aus</h2><p>Wählen Sie hier die Sprache in der ein Newsletter erstellt werden soll.</p>";
//Auswählen
$this->content->template['news_message2']="Auswählen";
/**
 * -- \n\r Um den Newsletter zu kündigen, klicken Sie bitte hier:\n\r http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=1&activate=$key&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html \n\r
 */
// Fuer Text NL
$this->content->template['news_imptext1']="-- \n\r Um den Newsletter zu kündigen, klicken Sie bitte hier:\n\r http://#url#/plugin.php?menuid=1&activate=#key#&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html \n\r#imp#";
/**
 * -- <span style=\"color:#999999;font-size:80%;\"><br />" .
							" Um den Newsletter zu kündigen, klicken Sie bitte hier:<br />" .
							" <a href=\"http://".$this->cms->title_send.PAPOO_WEB_PFAD."/plugin.php?menuid=1&activate=$key&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html\">Newsletter kündigen</a>
 */
// fuer HTML NL
$this->content->template['news_imptext2']=' ' .
							' Um den Newsletter zu kündigen, klicken Sie bitte hier:<br />' .
							' <a href="http://#url#/plugin.php?menuid=1&activate=#key#&news_message=de_activate&template=newsletter/templates/subscribe_newsletter.html">Newsletter kündigen</a><br />';
//Newsletter von ".$this->cms->title_send." abonniert.
$this->content->template['news_mail1']="Newsletter von seitenurl abonniert.";
/**
 * Sie haben den Newsletter von ".$this->cms->title_send." abonniert. \n\r Wenn Sie diesen Newsletter nicht abonniert haben oder Ihn nicht wollen, ignorieren Sie diese Mail, Sie werden keine weitere bekommen. Um den Newsletter zu aktivieren bitte klicken Sie auf den folgenden Link:\n\r
 */
$this->content->template['news_mail2']="Sie haben den Newsletter von seitenurl abonniert. \n\r Wenn Sie diesen Newsletter nicht abonniert haben oder Ihn nicht wollen, ignorieren Sie bitte diese E-Mail, Sie werden keine weitere bekommen. Um den Newsletter zu aktivieren, klicken Sie bitte auf den folgenden Link:\n\r";
$this->content->template['news_mail3']="Ein neuer Abonnent hat sich für ein oder mehrere moderierte Listen eingetragen:\n\r";
//Sprache/Language
$this->content->template['news_front1']="nodecode:<div id=\"hl\"><h1 class=\"home\">Newsletter abonniert</h1></div><p>Sie haben unseren Newsletter abonniert. Sie sollten in wenigen Minuten eine E-Mail mit einem Bestätigungslink bekommen.</p><p>Bitte klicken Sie auf den Link in der E-Mail, um diesen Newsletter endgültig zu bestellen.</p>";
//Sprache/Language
$this->content->template['news_front2']="nodecode:<div id=\"hl\"><h1 class=\"home\">Newsletter abonniert</h1></div><p>Ihr Abonnement unseres Newsletters wurde aktiviert. Sie werden ab heute unseren Newsletter beziehen. Wenn Sie den Newsletter kündigen wollen, klicken Sie einfach den Link zum Löschen des Newsletters in einer von uns empfangenen E-Mail.</p>";
//Sprache/Language
$this->content->template['news_front3']="<div id=\"hl\"><h1 class=\"home\">Newsletter storniert</h1></div><p>Der Newsletter ist storniert und Ihre Daten wurden gelöscht.</p>";
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
$this->content->template['news_front9']="Stra&szlig;e und Hausnummer";
//Sprache/Language
$this->content->template['news_front10']="Postleitzahl";
//Sprache/Language
$this->content->template['news_front11']="Wohnort";
//Sprache/Language
$this->content->template['news_front12']="Sprache";
$this->content->template['news_front13']="Staat";
$this->content->template['news_front14']=" Angabe fehlt";
$this->content->template['news_front15']=" Ungültige Angabe";
$this->content->template['news_front16']=" schon vorhanden";
$this->content->template['news_front17']="<div id=\"hl\"><h1 class=\"home\">Newsletter Archiv</h1></div>";
$this->content->template['news_front18']="Newsletter";
$this->content->template['news_front19']="Newsletter";
//Derzeit sind noch keine Archivdaten vorhanden.
$this->content->template['news_front20']="Derzeit sind noch keine Archivdaten vorhanden.";
$this->content->template['news_front21']=" ==> Keine Gruppe(n) ausgewählt";
$this->content->template['news_front22']="Telefon";
$this->content->template['news_message3']="Sprache";
$this->content->template['newsletter_anzeigen']="anzeigen";
$this->content->template['plugin']['newsletter']['unsubscribe_newsletter_title'] = 'Newsletter wirklich abbestellen?';
$this->content->template['plugin']['newsletter']['unsubscribe_newsletter'] = 'Newsletter abbestellen';
$this->content->template['plugin']['newsletter']['cancel'] = 'Abbrechen';
$this->content->template['plugin']['newsletter']['alle'] = 'Alle';

$this->content->template['plugin']['newsletter']['altnewsletter'] = 'Alte Newsletter';
$this->content->template['plugin']['newsletter']['inhalt_text'] = 'Inhalt als Text';
$this->content->template['plugin']['newsletter']['inhalt_html'] = 'Inhalt als HTML';
$this->content->template['plugin']['newsletter']['userdaten'] = 'Erweiterte Userdaten';
$this->content->template['plugin']['newsletter']['sprachwahl'] = 'Sprachwahl ermöglichen bei der Benutzeranmeldung im Frontend?';
$this->content->template['plugin']['newsletter']['text'] = 'Text oberhalb der Anmeldung anzeigen?';
$this->content->template['plugin']['newsletter']['html_mails'] = 'HTML mails?';
$this->content->template['plugin']['newsletter']['editor'] = 'WYSIWYG Editor tinymce?';
$this->content->template['plugin']['newsletter']['sprache'] = 'Sprache/Language';
$this->content->template['plugin']['newsletter']['daten'] = 'Die Daten.';
$this->content->template['plugin']['newsletter']['vorname'] = 'Vorname';
$this->content->template['plugin']['newsletter']['nachname'] = 'Nachname';
$this->content->template['plugin']['newsletter']['strasse'] = 'Stra&szlig;e und Hausnummer';
$this->content->template['plugin']['newsletter']['postleitzahl'] = 'Postleitzahl';
$this->content->template['plugin']['newsletter']['wohnort'] = 'Wohnort';
$this->content->template['plugin']['newsletter']['staat'] = 'Staat';
$this->content->template['plugin']['newsletter']['abschicken'] = 'abschicken';
$this->content->template['plugin']['newsletter']['email'] = 'E-Mail';
$this->content->template['plugin']['newsletter']['eingabe_datei'] = 'Eingabe der Datei:';
$this->content->template['plugin']['newsletter']['dokument'] = 'Das Dokument:';
$this->content->template['plugin']['newsletter']['durchsuchen'] = 'Durchsuchen...';
$this->content->template['plugin']['newsletter']['datei_upload'] = 'Datei hochladen:';
$this->content->template['plugin']['newsletter']['upload'] = 'hochladen';
$this->content->template['plugin']['newsletter']['sicherung'] = '<h3>Sicherung der Datenbank erstellen</h3><p> Sie können hier eine Sicherung der Datenbank erstellen, die Sie nach einer Neuinstallation oder zu einem beliebigen anderen Zeitpunkt wieder einspielen können.</p>';
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = 'Eine Sicherung einspielen';
$this->content->template['plugin']['newsletter']['sicherung_ready'] = 'Sicherungs Datei wurde eingespielt.';
$this->content->template['plugin']['newsletter']['hinweis'] = 'Um eine Sicherung einzuspielen, wählen Sie bitte die Sicherungsdatei aus:';
$this->content->template['plugin']['newsletter']['warnung'] = 'ACHTUNG - Wenn Sie eine Sicherung einspielen, werden alle aktuellen Daten unwiederruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung!';
$this->content->template['plugin']['newsletter']['make_dump'] = 'Jetzt eine Sicherung erstellen';
?>