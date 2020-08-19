<?php
/**
Hier werden alle messages zentral eingebunden,
das wird später die Mehrsprachigkeit ermöglichen.
Alle Messages sind nummeriert und unterteilt in
normale messages und errors.

Here are all messages centraly includet. This is important for
different languages use.
To include another language, please use this file for your purpose.

Auf korrektes Deutsch überprüft von Ulrich Gartmann.
*/

/**
normal messages
*/

/**
Der Admin Bereich Messages
*/
// Administration Ihrer Internetseite
$this->content->template['head_inc']['title'] = 'Administration Ihrer Internetseite';
//direkt zum Inhalt
$this->content->template['message_2120']="direkt zum Inhalt";
//zur Bereichsnavigation
$this->content->template['message_2121']="zur Bereichsnavigation";
// direkt zur Suche
$this->content->template['message_2122']=" direkt zur Suche ";
//direkt zum einloggen
$this->content->template['message_2123']="direkt zum einloggen";
// Kein Ergebniss
$this->content->template['message_5']="Es wurde leider nichts gefunden. Bitte präzisieren Sie Ihren Suchbegriff, damit wir Ihnen helfen können.";
$this->content->template['message_2099']="Username: ";
// Emailadresse:
$this->content->template['message_2100']=" Emailadresse:";
//Passwort:
$this->content->template['message_2101']="Passwort:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Passwort (zur Überprüfung):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']=" Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde? ";
//Antwortmail?
$this->content->template['message_2104']="Antwortmail?";
// erstellen
$this->content->template['message_2105']=" erstellen";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Hier können Sie Ihre Daten bearbeiten ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Hier können Sie die Daten für Ihren Account eintragen. ";
//Username:
$this->content->template['message_2108']="Username:";
// Emailadresse:
$this->content->template['message_2109']=" Emailadresse:";
//Neues Passwort:
$this->content->template['message_2110']="Neues Passwort:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?";
// Antwortmail?
$this->content->template['message_2112']=" Antwortmail? ";
//bearbeiten
$this->content->template['message_2113']="bearbeiten ";
//Styleswitcher
$this->content->template['message_2139']="Styleswitcher";
// User Sprach-Wahl
$this->content->template['message_2140'] = "Sprache";
$this->content->template['message_2141'] = "Hier können Sie festlegen, welche Standard-Sprache der Benutzer nach der Anmeldung verwendet.";
$this->content->template['message_2142'] = "Frontend";
$this->content->template['message_2143'] = "Backend";


// weiter.html
/*
"<h1>Hier können Sie die Foren Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Foren bearbeiten/erstellen</strong> können Sie Ihre Foren verwalten, unter
            <strong>Nachrichten bearbeiten</strong> können Sie Nachrichten verändern und ergänzen oder ganze Threads löschen, aber keine einzelnen Nachrichten, nur ganze Threads..</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
*/
$this->content->template['message_13']="<h1>Hier können Sie die Foren Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Foren bearbeiten/erstellen</strong> können Sie Ihre Foren verwalten, unter
            <strong>Nachrichten bearbeiten</strong> können Sie Nachrichten verändern und ergänzen oder ganze Threads löschen, aber keine einzelnen Nachrichten, nur ganze Threads..</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
// Stimmen die Daten, wenn ja dann auf abschicken klicken!
$this->content->template['message_14']="<h2>Stimmen die Daten, wenn ja dann auf abschicken klicken!</h2>";
// Hier können die Forumdaten bearbeitet werden!
$this->content->template['message_15']="<h1>Hier können die Forumdaten bearbeitet werden!</h1>";
// Dieses Forum existiert schon
$this->content->template['message_16']="<h2 style=\"background-color:white;color:red;\">Dieses Forum existiert schon, bitte wählen Sie einen anderen Namen aus.</h2>";
// <h3>Dieses Forum wurde gelöscht, bitte fahren Sie über das Menu fort</h3>
$this->content->template['message_17']="<h3>Dieses Forum wurde gelöscht, bitte fahren Sie über das Menu fort</h3>";
/**<h1>Hier können Sie die Benutzer und Gruppen Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>neuer Benutzer/Gruppe</strong> können Sie einen neuen Benutzer eingeben, unter
            <strong>Benutzer/Gruppen bearbeiten</strong> können bestehende Benutzer/Gruppen verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/
$this->content->template['message_18']="<h1>Hier können Sie die Benutzer und Gruppen Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>neuer Benutzer/Gruppe</strong> können Sie einen neuen Benutzer eingeben, unter
            <strong>Benutzer/Gruppen bearbeiten</strong> können bestehende Benutzer/Gruppen verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
/*
<h1>Hier können Sie Dateien hochladen für die Benutzung innerhalb von papoo</h1>
            <p>Unter dem Menupunkt <strong>Dateien hochladen</strong> können Sie eine Datei hochladen, unter
            <strong>Dateien ändern</strong> können bestehende Daten zu Dateien verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/
$this->content->template['message_19']="<h1>Hier können Sie Dateien hochladen für die Benutzung innerhalb von papoo</h1>
            <p>Unter dem Menupunkt <strong>Dateien hochladen</strong> können Sie eine Datei hochladen, unter
            <strong>Dateien ändern</strong> können bestehende Daten zu Dateien verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
//<strong style']="color:red">Etwas stimmt nicht, bitte überprüfen Sie Ihre Daten!</strong>')
$this->content->template['message_20']="<strong>Etwas stimmt nicht, bitte überprüfen Sie Ihre Daten!</strong>";
// Die Daten wurden eingetragen. Bitte fahren Sie über das Menü links fort.
$this->content->template['message_21']="Die Daten wurden eingetragen.";
// Diese Datei wurde gelöscht. Bitte fahren Sie über das Menü links fort.
$this->content->template['message_22']="Die Datei wurde gelöscht.";
// Die Datei konnte nicht gelöscht werden. Bitte manuell die folgende Datei löschen:
$this->content->template['message_23']="Die Datei konnte nicht gelöscht werden. Bitte löschen Sie die folgende Datei manuell:";
/*
<h1>Hier können Sie automatisch erstellte Abkürzungen und Sprachauszeichnungen Ihrer Homepage verwalten</h1>
                       <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/
$this->content->template['message_24']="<h1>Hier können Sie automatisch erstellte Abkürzungen und Sprachauszeichnungen Ihrer Homepage verwalten</h1>
                       <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
// Kein Ergebnis
$this->content->template['message_25']="Es sind keine Dateien vorhanden, die bearbeitet werden können.";
// Abkürzung löschen
$this->content->template['message_26']="Diese Abkürzung wurde gelöscht. Bitte fahren Sie über das Menü links fort.";
// Auszeichnung löschen
$this->content->template['message_27']="Diese Auszeichnung wurde gelöscht. Bitte fahren Sie über das Menü links fort.";
/**
<h1>Hier können Sie automatisch erstellte Links Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Link eingeben</strong> können Sie einen Link eingeben, unter
            <strong>Links ändern</strong> können bestehende Links verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/$this->content->template['message_28']="<h1>Hier können Sie automatisch erstellte Links Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Link eingeben</strong> können Sie einen Link eingeben, unter
            <strong>Links ändern</strong> können bestehende Links verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
// Link löschen
$this->content->template['message_29']="Dieser Link wurde gelöscht. Bitte fahren Sie über das Menü links fort.";
// Menü verwalten
$this->content->template['message_30']="<h1>Hier können Sie die Menüstruktur Ihrer Homepage verwalten</h1>
            <p>Unter dem Menüpunkt <strong>Menüpunkt erstellen</strong> können Sie einen Menüpunkt eingeben, unter
            <strong>Menüpunkt bearbeiten</strong> können bestehende Menüpunkte verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
//<strong>Der Menupunkt wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!
$this->content->template['message_31']="<strong>Der Menüpunkt wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort.</strong>";
// Dieser Menüpunkt existiert schon
$this->content->template['message_32']="Dieser Menüpunkt existiert schon.";
// <strong>Eine der Eingaben stimmt nicht, bitte überprüfen!</strong>
$this->content->template['message_33']="<strong>Eine der Eingaben stimmt nicht, bitte überprüfen!</strong>";
// <strong>Der Menupunkt wurde gelöscht, bitte fahren Sie über das Menü fort.</strong>
$this->content->template['message_34']="<strong>Der Menupunkt wurde gelöscht, bitte fahren Sie über das Menü fort.</strong>";
/**
<h1>Hier können Sie die Bilder Ihrer Homepage verwalten</h1>
                <p>Bitte beachten Sie die Formatierungshinweise,
                da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>

*/
$this->content->template['message_35']="<h1>Hier können Sie die Bilder Ihrer Homepage verwalten</h1>
                <p>Bitte beachten Sie die Formatierungshinweise,
                da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
//<h2>Das Bild ist zu groß!</h2>  Bitte nochmal versuchen.
$this->content->template['message_36']="<h2>Das Bild ist zu groß!</h2>  Bitte nochmal versuchen.";
// <h1>Eine Datei diesen Namens existiert schon</h1><p>Bitte benennen Sie die Datei um!</p>
$this->content->template['message_37']="<h1>Eine Datei diesen Namens existiert schon</h1><p>Bitte benennen Sie die Datei um!</p>";
//<h1>Das geht leider nicht</h1><p>Sie haben kein Bild ausgewählt, oder es handelt sich um kein unterstütztes Format.</p>
$this->content->template['message_38']="<h1>Das geht leider nicht</h1><p>Sie haben kein Bild ausgewählt, oder es handelt sich um kein unterstütztes Format.</p>";

$this->content->template['message_image_change_imagefile_legend'] = "Change image-file";
$this->content->template['message_image_change_imagefile_text'] = 
"Replace the existing image-file with a new one.
Only the file on the Web-Server will be replaced. All images-inplacements in articles etc. will be left untouched.
<strong>Please pay attention that the pixel-size of the new image is the same than the existing one.</strong>";
$this->content->template['message_image_change_imagefile_label'] = "New image-file";

$this->content->template['message_39']="<h1>Daten wurden eingetragen</h1><p>Bitte fahren Sie über das Menü fort.</p>";
/*<h1>Hier können Sie die Artikel in der 3. Spalte Ihrer Homepage verwalten</h1>
                        <p>Unter dem Menupunkt <strong>neuer Artikel</strong>
                        können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können
                        bestehende Artikel verändert und ergänzt werden,
                        Bilder oder Links eingefügt werden.</p>
                        <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten
                        keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/

$this->content->template['message_40']="<h1>Hier können Sie die Artikel in der 3.Spalte Ihrer Homepage verwalten</h1>
                        <p>Unter dem Menupunkt <strong>neuer Artikel</strong>
                        können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können
                        bestehende Artikel verändert und ergänzt werden,
                        Bilder oder Links eingefügt werden.</p>
                        <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten
                        keine fehlerfreie Darstellung gewährleistet werden kann.</p>";

/*<h1>Hier können Sie die Artikel auf der Homepage verwalten</h1><p>Unter dem Menupunkt <strong>neuer Artikel</strong> können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können bestehende Artikel verändert und ergänzt werden, Bilder oder Links eingefügt werden.</p><p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/
$this->content->template['message_41']="<h1>Hier können Sie die Artikel auf der Homepage verwalten</h1><p>Unter dem Menupunkt <strong>neuer Artikel</strong> können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können bestehende Artikel verändert und ergänzt werden, Bilder oder Links eingefügt werden.</p><p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
/*<h2>Artikel wurde eingetragen</h2><p>Klicken Sie hier um einen weiteren Artikel in die Datenbank einzutragen: <a href']="./artikel.php?menuid=10&obermenuid=5&untermenuid=10&freimachen=1" title \"Neuer Eintrag\">Neuen Eintrag erstellen</a></p>*/
$this->content->template['message_42']='<h2>Artikel wurde eingetragen</h2>';
// "<h2>Ihr Teaser wird so dargestellt werden:</h2>"
$this->content->template['message_43']="<h2>Ihr Teaser wird so dargestellt werden:</h2>";
/*
<hr /><h2>Ihr Artikel wird so (oder ähnlich) dargestellt werden</h2><p>Für Änderungen oder die endgültige Eintragung in die Datenbank bitte unten klicken.</p><hr />
*/
$this->content->template['message_44']="<hr /><h2>Ihr Artikel wird so (oder ähnlich) dargestellt werden</h2><p>Für Änderungen oder die endgültige Eintragung in die Datenbank bitte unten klicken.</p><hr />";
// Nicht genug Daten eingegeben im Textfeld, bitte ergänzen Sie!
$this->content->template['message_45']="Nicht genug Daten eingegeben im Textfeld, bitte ergänzen Sie!";
/* <h2>Artikel bearbeiten</h2><p>Um einen Artikel zu bearbeiten müssen Sie ihn hier suchen und anschließend im Suchergebnis auf den Link <strong>(Name Ihres Artikels) ändern</strong> klicken</p>
*/
$this->content->template['message_46']="<h2>Artikel bearbeiten</h2><p>Um einen Artikel zu bearbeiten müssen Sie ihn hier suchen und anschließend im Suchergebnis auf den Link <strong>(Name Ihres Artikels) ändern</strong> klicken</p>";
// ändern
$this->content->template['message_47']="ändern";
// '<strong>Dieser Artikel wurde gelöscht, bitte fahren Sie über das Menü fort!</strong>
$this->content->template['message_2']="<strong>Dieser Artikel wurde gelöscht, bitte fahren Sie über das Menü fort!</strong>";
//<strong>Bitte überprüfen Sie Ihre Eingaben, etwas fehlt!</strong>
$this->content->template['message_48']="<strong style=\"color:red;\">Bitte überprüfen Sie Ihre Eingaben, etwas fehlt!</strong>";
//Diese Gruppe existiert schon!
$this->content->template['message_49']="<strong style=\"color:red;\">Diese Gruppe existiert schon!</strong>";
//Die Gruppe wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!<br>
$this->content->template['message_50']="Die Gruppe wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!<br>";
// Dieser Benutzer existiert schon!
$this->content->template['message_51']="Dieser Benutzer existiert schon!";
//<strong>Der Benutzer wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!</strong><br>
$this->content->template['message_52']="<strong>Der Benutzer wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!</strong><br>";
//<h3>Dieser Benutzer wurde gelöscht, bitte fahren Sie über das Menu fort.</h3>
$this->content->template['message_53']="<h3>Dieser Benutzer wurde gelöscht, bitte fahren Sie über das Menu fort.</h3>";
//<h3>root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_54']="<h3>root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>";
//<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_55']="<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>";
//<h1>Diese Gruppe exisitiert nicht</h1>
$this->content->template['message_56']="<h1>Diese Gruppe exisitiert nicht</h1>";
// <h3>Diese Gruppe wurde gelöscht, bitte fahren Sie über das Menu fort</h3>
$this->content->template['message_57']="<h3>Diese Gruppe wurde gelöscht, bitte fahren Sie über das Menu fort</h3>";
//<h3>papoo_root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_58']="<h3>papoo_root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>";
//<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_59']="<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>";

/**
Alle Daten im Template
*/
// Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_60']="<h2>Bitte überprüfen Sie Ihre Eingaben</h2>";
// Benutzername
$this->content->template['message_61']="Benutzername";
// Passwort
$this->content->template['message_62']="Passwort";
// Internes Menü zur Verwaltung Ihrer Internetseite
$this->content->template['message_63']="Internes Menü zur Verwaltung Ihrer Internetseite";
/* <p>Sie können hier je nach Gruppenzugehörigkeit Artikel eingeben oder sämtliche Daten Ihrer Seite verwalten.</p>
<p>Klicken Sie die Menüpunkte in der Menüleiste links an und erfahren Sie dort alles Weitere...</p>*/
$this->content->template['message_64']="<p>Sie können hier je nach Gruppenzugehörigkeit, Artikel eingeben oder sämtliche Daten Ihrer Seite verwalten.</p>
<p>Klicken Sie die Menüpunkte in der Menüleiste links an und erfahren Sie dort alles Weitere...</p>";
// Empfänger der Nachricht
$this->content->template['message_65']="Empfänger der Nachricht";
// Überschrift
$this->content->template['message_66']="Überschrift";
// Hier bitte Ihre Überschrift eingeben
$this->content->template['message_67']="Hier bitte Ihre Überschrift eingeben";
// Texteingabe
$this->content->template['message_68']="Texteingabe";
// Übermitteln
$this->content->template['message_69']="Übermitteln";
// Eintragen
$this->content->template['message_70']="Speichern";
$this->content->template['message_70a']="Als Kopie Speichern";
// Artikel, die Sie geschrieben und veröffentlicht haben.
$this->content->template['message_71']="Artikel, die Sie geschrieben und veröffentlicht haben.";
//Sie können die Artikel erneut bearbeiten und veröffentlichen, wenn Sie auf den entsprechenden Link klicken:
$this->content->template['message_72']="Sie können die Artikel erneut bearbeiten und veröffentlichen, wenn Sie auf den entsprechenden Link klicken:";
//weitere Seiten
$this->content->template['message_73']="Eine Seite zurück";
//Artikel die zu veröffentlichen sind
$this->content->template['message_74']="Artikel, die zu veröffentlichen sind";
//Sie können die Artikel bearbeiten und veröffentlichen, wenn Sie auf den Link klicken:
$this->content->template['message_75']="Sie können die Artikel bearbeiten und veröffentlichen, wenn Sie auf den Link klicken:";
//Ihr persönlichen Daten.
$this->content->template['message_76']="Ihr persönlichen Daten";
//Sie haben
$this->content->template['message_77']="Sie haben";
//neue Mitteilungen von anderen Mitgliedern.
$this->content->template['message_78']="neue Mitteilungen von anderen Mitgliedern.";
//Artikel stehen zur Veröffentlichung an
$this->content->template['message_79']="Artikel stehen zur Veröffentlichung an";
//Sie haben bereits
$this->content->template['message_80']="Sie haben bereits";
//Artikel veröffentlicht
$this->content->template['message_81']="Artikel veröffentlicht ";
//Hier können Sie die Daten Ihres Accounts bearbeiten
$this->content->template['message_82']="Hier können Sie die Daten Ihres Accounts bearbeiten";
//Email Addresse
$this->content->template['message_83']="Email Addresse";
//Sie haben keine Berechtigung.
$this->content->template['message_84']="Sie haben keine Berechtigung.";
//Erneut versuchen
$this->content->template['message_85']="Erneut versuchen";
//Seite weiter
//weitere Seiten
$this->content->template['message_86']="Eine Seite weiter.";
// Name des Forums
$this->content->template['message_87']="Name des Forums ";
//Forumname
$this->content->template['message_88']="Forumname";
//Beschreibung
$this->content->template['message_89']="Beschreibung";
//Beschreibung des Forums, max. 200 Zeichen
$this->content->template['message_90']="Beschreibung des Forums, max. 200 Zeichen";
// Soll das Forum im Internet oder im Intranet stehen
$this->content->template['message_91']="Soll das Forum im Internet oder im Intranet stehen? ";
//Intranet
$this->content->template['message_92']="Intranet";
//Internet
$this->content->template['message_93']="Internet";
//Geben Sie hier an, welche Gruppen das Forum lesen dürfen
$this->content->template['message_94']="Leserechte";
//Gruppe
$this->content->template['message_95']="Gruppe";
//Geben Sie hier an, welche Gruppen in das Forum schreiben dürfen.
$this->content->template['message_96']="Schreibrechte";
//Die letzten 10 Einträge
$this->content->template['message_97']="Die letzten 10 Einträge";
//Suche nach einer Message
$this->content->template['message_98']="Suche nach einer Message";
//Suche nach einer Message
$this->content->template['message_98a']="<h1>Beiträge suchen</h1>";
//Hier können die Messagedaten bearbeitet werden.
$this->content->template['message_99']="Hier können die Messagedaten bearbeitet werden.";
//Messagedaten bearbeiten
$this->content->template['message_100']="Messagedaten bearbeiten";
//Betreff
$this->content->template['message_101']="Betreff";
// Diese Message löschen
$this->content->template['message_102']="Diese Message löschen ";
//<h3>Um diese Message zu löschen muss der <strong>gesamte</strong> Thread gelöscht werden.</h3><p>Wollen Sie tatsächlich den gesamten thread löschen??</p>
$this->content->template['message_103']="<h3>Um diese Message zu löschen muss der <strong>gesamte</strong> Thread gelöscht werden.</h3><p>Wollen Sie tatsächlich den gesamten thread löschen??</p>";
//Forum löschen
$this->content->template['message_104']="Forum löschen";
//Dieses Forum löschen?
$this->content->template['message_105']="Dieses Forum löschen?";
//Löschen
$this->content->template['message_106']="Löschen";
//Message
$this->content->template['message_107']="Message";
//  Diese Gruppe löschen??
$this->content->template['message_108']=" Diese Gruppe löschen?";
//Gruppenname
$this->content->template['message_109']="Gruppenname";
//Verfügbare Gruppen
$this->content->template['message_110']="Verfügbare Gruppen";
//Um die Eigenschaften der Gruppen zu ändern einfach darauf klicken
$this->content->template['message_111']="Um die Eigenschaften der Gruppen zu ändern einfach darauf klicken ";
//Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum
$this->content->template['message_112']="Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum";
//Neue Gruppe anlegen
$this->content->template['message_114']="Gruppendaten einer Gruppe bearbeiten";
//Gruppenname und Gruppenleiter angeben
$this->content->template['message_115']="Gruppenname und Gruppenleiter angeben";
//Gruppenname
$this->content->template['message_116']="Gruppenname";
//Gruppenleiter
$this->content->template['message_117']="Gruppenleiter";
//Dürfen Gruppenmitglieder auf das Intranet (wenn vorhanden) zugreifen?
$this->content->template['message_118']="Dürfen Gruppenmitglieder auf das Intranet (wenn vorhanden) zugreifen?";
//Zugriff aufs Intranet?
$this->content->template['message_119']="Zugriff aufs Intranet?";
//Dürfen Benutzer dieser Gruppe Artikel für das Internet oder Intranet veröffentlichen?
$this->content->template['message_120']="Dürfen Benutzer dieser Gruppe Artikel für das Internet oder Intranet veröffentlichen? ";
//Internet ja?
$this->content->template['message_121']="Internet ja?";
//Intranet ja?:
$this->content->template['message_122']="Intranet ja?:";
//Wenn hier kein Häkchen gesetzt wird, wird der Artikel automatisch zur Freigabe an die Elterngruppe weitergegeben
$this->content->template['message_123']="Wenn hier kein Häkchen gesetzt wird, wird der Artikel automatisch zur Freigabe an die Elterngruppe weitergegeben";
//Dürfen Benutzer dieser Gruppe auf die Administration zugreifen?:
$this->content->template['message_124']="Dürfen Benutzer dieser Gruppe auf die Administration zugreifen?";
//Zugriff auf die Administration
$this->content->template['message_125']="Zugriff auf die Administration";
//Welcher der einzelnen Menüpunkte im Backend für welche Gruppen freigegeben wird, wird in den Stammdaten eingestellt.
$this->content->template['message_126']="Welcher der einzelnen Menüpunkte im Backend für welche Gruppen freigegeben wird, wird in den Stammdaten eingestellt.";
//Einfügen in die Hierarchie und Beschreibung:
$this->content->template['message_127']="Einfügen in die Hierarchie und Beschreibung (Nur statistische Werte)";
//Untergruppe von
$this->content->template['message_128']="Untergruppe von";
//Hier bitte die Beschreibung der Gruppe eingeben
$this->content->template['message_129']="Hier bitte die Beschreibung der Gruppe eingeben";
// Gruppe anlegen
$this->content->template['message_130']=" Gruppe speichern ";
//Neuen Benutzer anlegen
$this->content->template['message_131']="Neuen Benutzer anlegen";
//Gruppenname und Password angeben
$this->content->template['message_132']="Gruppenname und Password angeben";
//Welcher Gruppe gehört der Benutzer an?
$this->content->template['message_133']="Welcher Gruppe gehört der Benutzer an? ";
//Damit werden Lese- und Schreibrechte, genauso wie der Zugriff auf den Admin-Bereich geregelt.
$this->content->template['message_134']="Damit werden Lese- und Schreibrechte, genauso wie der Zugriff auf den Admin-Bereich geregelt.";
//Weitere Daten
$this->content->template['message_135']="Weitere Daten";
//Hier bitte die Beschreibung der Gruppe eingeben
$this->content->template['message_136']="Hier bitte die Beschreibung der Gruppe eingeben";
//Benutzer eintragen
$this->content->template['message_137']="Benutzer eintragen";
//Alle Benutzer
$this->content->template['message_138']="Alle Benutzer";
//Suche nach einem Benutzer
$this->content->template['message_139']="Suche nach einem Benutzer";
//Anzahl der Benutzer
$this->content->template['message_140']="Anzahl der Benutzer";
//Um die Eigenschaften der Benutzer zu ändern einfach darauf klicken
$this->content->template['message_141']="Um die Eigenschaften der Benutzer zu ändern einfach darauf klicken";
//Es wurde leider kein Benutzer im System gefunden, bitte versuchen Sie ein anderes Suchwort.
$this->content->template['message_142']="Es wurde leider kein Benutzer im System gefunden, bitte versuchen Sie ein anderes Suchwort.";
//Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum
$this->content->template['message_143']="Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum";
//Gruppenzugehörikeit
$this->content->template['message_144']="Gruppenzugehörigkeit";
//Eintrittsdatum
$this->content->template['message_145']="Eintrittsdatum";
//Anzahl der Beiträge
$this->content->template['message_146']="Anzahl der Beiträge ";
//Diesen Benutzer löschen??
$this->content->template['message_147']="Diesen Benutzer löschen?";
//Benutzer bearbeiten
$this->content->template['message_148']="Benutzer bearbeiten";
//Welcher Gruppe gehört der Benutzer an?
$this->content->template['message_149']="Welcher Gruppe gehört der Benutzer an?";
//Suche nach einem Artikel
$this->content->template['message_150']="Suche nach einem Artikel";
//Die gesuchten Artikel
$this->content->template['message_151']="Die gesuchten Artikel";
//Sie können die Artikel erneut bearbeiten und veröffentlichen wenn Sie auf den entsprechenden Link klicken:
$this->content->template['message_152']="Sie können die Artikel erneut bearbeiten und veröffentlichen, wenn Sie auf den entsprechenden Link klicken:";
//Eingabe/Bearbeitung eines Artikels:
$this->content->template['message_153']="Eingabe/Bearbeitung eines Artikels:";
//Hier bitte Ihre Überschrift eingeben
$this->content->template['message_154']="Hier bitte Ihre Überschrift eingeben";
//Teaser/Anreisser
$this->content->template['message_155']="Teaser/Anreisser ";
//Der Teaser oder auch Anreisser wird auf der Startseite oder auch auf den Unterseiten zusammen mit der Überschrift angezeigt.
$this->content->template['message_156']="Der Teaser oder auch Anreisser wird auf der Startseite oder auch auf den Unterseiten zusammen mit der Überschrift angezeigt.";
//Bild zum Teaser
$this->content->template['message_157']="Bild zum Teaser";
//Teaser-Bild auswählen (klein)
$this->content->template['message_158']="Teaser-Bild auswählen (klein)";
//Bild auswählen (klein)
$this->content->template['message_158a']="Bild auswählen (klein)";
//Hier kann ein Bild ausgewählt werden.
$this->content->template['message_159']="Hier kann ein Bild ausgewählt werden.";
//auswählen
$this->content->template['message_160']="auswählen";
//Bild links anzeigen?
$this->content->template['message_161']="Bild links anzeigen?";
//Bild rechts anzeigen?
$this->content->template['message_162']="Bild rechts anzeigen?";
//Teaser Link
$this->content->template['message_163']="Teaser Link";
//mehr über
$this->content->template['message_164']="mehr über";
//Hier bitte den Link zum Artikel eingeben
$this->content->template['message_165']="Hier bitte den Link zum Artikel eingeben";
//Eingabe und Formatierung der Inhalte
$this->content->template['message_166']="Eingabe und Formatierung der Inhalte";
// Menüpunkt auswählen.
$this->content->template['message_167']=" Menüpunkt auswählen.";
//Menupunkt
$this->content->template['message_168']="Menüpunkt";
//Menü für Internet
$this->content->template['message_169']="Menü für Internet";
//Menü für Intranet
$this->content->template['message_170']="Menü für Intranet ";
// Den Artikel veröffentlichen?
$this->content->template['message_171']=" Den Artikel veröffentlichen?";
//Die <strong>endgültige</strong> Freigabe erfolgt durch
$this->content->template['message_172']="Die <strong>endgültige</strong> Freigabe erfolgt durch Ihren Gruppenleiter. Bitte das Häkchen trotzdem setzen, wenn Sie das Dokument veröffentlichen wollen.</p>";
//Downloads zählen.
$this->content->template['message_174']="Downloads zählen.";
//Geben Sie hier an, ob die eventuell vorhandenen Downloads gezählt werden sollen.
$this->content->template['message_175']="Geben Sie hier an, ob die eventuell vorhandenen Downloads gezählt werden sollen.";
//Zählen
$this->content->template['message_176']="Zählen";
//Soll der Artikel auf der Startseite gelistet werden?
$this->content->template['message_177']="Soll der Artikel auf der Startseite gelistet werden?";
//Auf der Startseite listen?
$this->content->template['message_178']="Auf der Startseite listen?";
//Zugriff für andere.
$this->content->template['message_179']="Zugriff für andere";
// Dürfen andere Benutzer auf diesen Artikel schreibend zugreifen?
$this->content->template['message_180']=" Dürfen andere Benutzer auf diesen Artikel schreibend zugreifen? ";
// Freigabe für andere?
$this->content->template['message_181']=" Freigabe für andere? ";
//Daten löschen?
$this->content->template['message_182']="Daten löschen? ";
//Sie dürfen diesen Artikel nicht bearbeiten, nur anschauen.
$this->content->template['message_183']="Sie dürfen diesen Artikel nicht bearbeiten, nur anschauen.";
//Diesen Artikel löschen??
$this->content->template['message_184']="Diesen Artikel löschen?";
//Daten eintragen?
$this->content->template['message_185']="Daten eintragen?";
//Neue Vorschau
$this->content->template['message_186']="Neue Vorschau";
//Sie haben keine Rechte Artikel für das
$this->content->template['message_187']="Sie haben keine Rechte Artikel für das Internet ";
//zu veröffentlichen.
$this->content->template['message_188']="zu veröffentlichen.";
// Das Formular übermitteln.
$this->content->template['message_190']=" Das Formular übermitteln.";
//Damit wird erst die Vorschau aktiviert
$this->content->template['message_191']="Damit wird erst die Vorschau aktiviert";
// Die CSS Datei wurde eingetragen
$this->content->template['message_192']=" Die CSS Datei wurde eingetragen";
// Das Ergebnis können Sie hier überprüfen..
$this->content->template['message_193']=" Das Ergebnis können Sie hier überprüfen..";
// Hier können Sie die CSS Datei Ihrer Seite bearbeiten
$this->content->template['message_194']=" Hier können Sie die CSS Datei Ihrer Seite bearbeiten";
// Damit Ihre Änderungen auch in die Datei geschrieben werden können, muss diese die Dateirechte 646 haben.
$this->content->template['message_195']=" Damit Ihre Änderungen auch in die Datei geschrieben werden können, muss diese die Dateirechte 646 haben.";
// Eingabe/Bearbeitung der CSS-Datei:
$this->content->template['message_196']=" Eingabe/Bearbeitung der CSS-Datei:";
//CSS-Datei:
$this->content->template['message_197']="CSS-Datei:";
//Ihr Bild wurde hochgeladen
$this->content->template['message_198']="Ihr Bild wurde hochgeladen";
//Diese Daten wurden hochgeladen
$this->content->template['message_199']="Diese Daten wurden hochgeladen";
//Bitte geben Sie hier die nötigen Daten für das Bild ein
$this->content->template['message_203']="Bitte geben Sie hier die nötigen Daten für das Bild ein";
// Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt!
$this->content->template['message_204']=" Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt!";
//Alternativtext
$this->content->template['message_205']="Alternativtext";
//Wenn kein Bild angezeigt werden kann
$this->content->template['message_206']="Wenn kein Bild angezeigt werden kann";
// kurze Beschreibung
$this->content->template['message_207']=" kurze Beschreibung ";
//Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)
$this->content->template['message_208']="Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)";
// Laden Sie bitte das Bild hoch. Es wird zusätzlich ein Thumbnail erzeugt.
$this->content->template['message_209']=" Laden Sie bitte das Bild hoch. Es wird zusätzlich ein Thumbnail erzeugt.";
// Maximale Dateigröße ist 100 kbyte und 800x800px!
$this->content->template['message_210']=" Maximale Dateigröße ist 100 kbyte und 800x800px!";
//Unterstützte Formate: jpeg, jpg, pjpeg
$this->content->template['message_211']="Unterstützte Formate: JPG, GIF, PNG, SVG";
//Das Bild
$this->content->template['message_212']="Das Bild";
//abschicken
$this->content->template['message_213']="abschicken";
//Dieses Bild wird bearbeitet
$this->content->template['message_214']="Dieses Bild wird bearbeitet";
// Dieses Bild verkleinern.
$this->content->template['message_215']=" Dieses Bild verkleinern.";
// Verkleinern in Prozent (z.B. 80 für 80%)
$this->content->template['message_216']=" Verkleinern in Prozent (z.B. 80 für 80%)";
//verkleinern
$this->content->template['message_217']="verkleinern";
// Bitte geben Sie hier die nötigen Daten für das Bild ein
$this->content->template['message_218']=" Bitte geben Sie hier die nötigen Daten für das Bild ein";
// Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt!
$this->content->template['message_219']=" Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt! ";
// Alternativtext (Wenn kein Bild angezeigt werden kann)
$this->content->template['message_220']=" Alternativtext (Wenn kein Bild angezeigt werden kann) ";
// Titel (kurze Beschreibung):
$this->content->template['message_221']=" Titel (kurze Beschreibung):";
// Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)
$this->content->template['message_222']=" Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)";
// Dieses Bild löschen?-
$this->content->template['message_223']=" Dieses Bild löschen?";
//In die Datenbank eintragen
$this->content->template['message_224']="In die Datenbank eintragen";
//Sie haben die Informationen dieses Bilds verändert
$this->content->template['message_225']="Sie haben die Informationen dieses Bilds verändert";
//Dieses Bild wird in diesem(n) Artikel(n) verwendet.
$this->content->template['message_226']="Dieses Bild wird in diesem(n) Artikel(n) verwendet. ";
// Möchten Sie die Änderungen
$this->content->template['message_227']=" Möchten Sie die Änderungen";
// im Original speichern?
$this->content->template['message_228']=" im Original speichern?";
//oder möchten Sie dieses Bild lieber
$this->content->template['message_229']="oder möchten Sie dieses Bild lieber";
//unter dem Namen:
$this->content->template['message_230']="unter dem Namen: ";
//als Kopie speichern?
$this->content->template['message_231']="als Kopie speichern?";
// Suche nach einem Bild:
$this->content->template['message_232']=" Suche nach einem Bild:";
$this->content->template['message_232a']="<h1>Bilder bearbeiten</h1>";
//Finden
$this->content->template['message_233']="Finden";
//Eingabe neuer Linkdaten
$this->content->template['message_234']="Eingabe neuer Linkdaten";
//Hier können Sie neue Ersetzungsdaten für Links in die Datenbank eingeben.
$this->content->template['message_235']="Hier können Sie neue Ersetzungsdaten für Links in die Datenbank eingeben.";
//Es sind nur http://www. Adressen erlaubt.
$this->content->template['message_236']="Es sind nur http://www. Adressen erlaubt.";
//Eingabe der Linkdaten
$this->content->template['message_237']="Eingabe der Linkdaten";
//Ersetzungstext
$this->content->template['message_238']="Ersetzungstext";
//Link
$this->content->template['message_239']="Link";
//Titel des Links
$this->content->template['message_240']="Titel des Links";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist der Link falsch eingegeben.
$this->content->template['message_242']="Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist der Link falsch eingegeben.";
//Änderung der Linkdaten:
$this->content->template['message_243']="Änderung der Linkdaten:";
//Suche nach einem Link-Eintrag:
$this->content->template['message_244']="Suche nach einem Link-Eintrag:";
//Hier können Sie die bereits in der Datenbank vorhandenen Links ändern
$this->content->template['message_245']="Hier können Sie die bereits in der Datenbank vorhandenen Links ändern";
//Ändern Sie den folgenden Link. Es sind nur http://www. Adressen erlaubt.
$this->content->template['message_246']="Ändern Sie den folgenden Link. Es sind nur http://www. Adressen erlaubt.";
//Entfernen
$this->content->template['message_247']="Entfernen";
//Diesen Menupunkt löschen??
$this->content->template['message_248']="Diesen Menupunkt löschen?";
//Wenn Sie den Löschen Button drücken, wird der Menupunkt unwiderruflich gelöscht und alle Artikel die unter diesem Menüpunkt erreichbar waren werden nicht mehr erreichbar sein. Sie können diese über den Artikel Menüpunkt anderen Menüpunkten zuweisen.
$this->content->template['message_249']="Wenn Sie den Löschen Button drücken, wird der Menupunkt unwiderruflich gelöscht und alle Artikel, die unter diesem Menüpunkt erreichbar waren, werden nicht mehr erreichbar sein. Sie können diese über den Artikel Menüpunkt anderen Menüpunkten zuweisen.";
//Menupunkt Name:
$this->content->template['message_250']="Menupunkt Name:";
//Menu formtitel:
$this->content->template['message_251']="Menu formtitel:";
//Untermenu zu:
$this->content->template['message_252']="Untermenu zu: ";
//Eintrag löschen!!!
$this->content->template['message_253']="Eintrag löschen!";
//Hier sind alle verfügbaren Menüpunkte
$this->content->template['message_254']="Hier sind alle verfügbaren Menüpunkte";
//Diesen Menupunkt bearbeiten.
$this->content->template['message_255']="Diesen Menupunkt bearbeiten.";
//Name
$this->content->template['message_257']="Name";
//formtitel
$this->content->template['message_258']="Titel Attribut";
//formtitel
$this->content->template['message_258b']="Sprechende URL";
//Internet oder Intranet?
$this->content->template['message_259']="Internet oder Intranet?";
//Wenn der Menupunkt ein normaler Punkt der 1. Ordnung ist, dann ist er ein Unterpunkt zur Startseite
$this->content->template['message_260']="Wenn der Menupunkt ein normaler Punkt der 1. Ordnung ist, dann ist er ein Unterpunkt zur Startseite";
//Menü für Internet
$this->content->template['message_261']="Menü für Internet";
//Menü für Intranet
$this->content->template['message_262']="Menü für Intranet";
//Zugriff für andere.
$this->content->template['message_263']="Wer darf unter diesem Menüpunkt veröffentlichen:";
//Wenn auf eine besondere Seite verwiesen werden soll:
$this->content->template['message_264']="Einbindung des Links oder der Datei.";
//Einen neuen Menupunkt erstellen.
$this->content->template['message_265']="Einen neuen Menupunkt erstellen.";
//formlink
$this->content->template['message_266']="Datei (Standard index.php)";
//formlink
$this->content->template['message_266a']="Weitere Hilfe dazu.";
//Suche nach einem Eintrag:
$this->content->template['message_267']="Suche nach einem Eintrag:";
// Eingabe/Bearbeitung eines Artikels:
$this->content->template['message_268']=" Eingabe/Bearbeitung eines Artikels:";
// Neue Vorschau
$this->content->template['message_269']=" Neue Vorschau ";
//Eingabe/Bearbeitung eines Eintrags:
$this->content->template['message_270']="Eingabe/Bearbeitung eines Eintrags:";
//Diesen Artikel löschen??
$this->content->template['message_271']="Diesen Artikel löschen?";


//Eingabe neuer Abkürzungsdaten
$this->content->template['message_272']="Eingabe neuer Abkürzungsdaten ";
//<p>Hier können Sie neue Ersetzungsdaten für Abkürzungen in die Datenbank eingeben.</p><p>Unter Abkürzungen fallen auch die Akronyme.</p><p>Die Abkürzung muss mindestens 3 Zeichen lang sein.</p>
$this->content->template['message_273']="<p>Hier können Sie neue Ersetzungsdaten für Abkürzungen in die Datenbank eingeben.</p>
<p>Unter Abkürzungen fallen auch die Akronyme.</p>
<p>Die Abkürzung muss mindestens 3 Zeichen lang sein.</p>";
//Eingabe der Abkürzungsdaten:
$this->content->template['message_274']="Eingabe der Abkürzungsdaten:";
//Abkürzung
$this->content->template['message_275']="Abkürzung";
// Bedeutung der Abkürzung:
$this->content->template['message_276']=" Bedeutung der Abkürzung:";
//Eingabe neuer Abkürzungsdaten
$this->content->template['message_277']="Eingabe neuer Abkürzungsdaten";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Abkürzung zu kurz (mindestens 3 Buchstaben).
$this->content->template['message_278']="Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Abkürzung zu kurz (mindestens 3 Buchstaben).";
//Änderung der Abkürzungsdaten:
$this->content->template['message_279']="Änderung der Abkürzungsdaten:";
//Suche nach einem Abkürzungs-Eintrag:
$this->content->template['message_280']="Suche nach einem Abkürzungs-Eintrag:";
//Änderung der Abkürzungen
$this->content->template['message_281']="Änderung der Abkürzungen";

//Eingabe neuer Sprachauszeichnungen
$this->content->template['message_282']="Eingabe neuer Sprachauszeichnungen";
//<p>Hier können Sie neue Ersetzungsdaten für Sprachauszeichnung in die Datenbank eingeben.</p><p>Bitte nur Sprachauszeichnung aus der englischen Sprache auszeichnen.</p><p>Die Sprachauszeichnung muß mindestens 3 Zeichen lang sein.</p>
$this->content->template['message_283']="<p>Hier können Sie neue Ersetzungsdaten für Sprachauszeichnung in die Datenbank eingeben.</p>
<p>Bitte nur Wörter aus der englischen Sprache auszeichnen.</p>
<p>Die Sprachauszeichnung muß mindestens 3 Zeichen lang sein.</p>";
//Eingabe der Sprachauszeichnung:
$this->content->template['message_284']="Eingabe der Sprachauszeichnung:";
//Sprachauszeichnung
$this->content->template['message_285']="Sprachauszeichnung";
//Eingabe neuer Sprachauszeichnung
$this->content->template['message_286']="Eingabe neuer Sprachauszeichnung";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Sprachauszeichnung zu kurz (mindestens 3 Buchstaben).
$this->content->template['message_287']="Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Sprachauszeichnung zu kurz (mindestens 3 Buchstaben).";
//Änderung der Sprachauszeichnung:
$this->content->template['message_288']="Änderung der Sprachauszeichnung:";
//Sprachauszeichnung
$this->content->template['message_289']="Sprachauszeichnung";
//Suche nach einem Sprachauszeichnung-Eintrag:
$this->content->template['message_290']="Suche nach einem Sprachauszeichnung-Eintrag:";
//Überprüfung der Sprachauszeichnung
$this->content->template['message_291']="Überprüfung der Sprachauszeichnung";
//Hier können Sie die Stammdaten Ihrer Seite bearbeiten
$this->content->template['message_292']="Hier können Sie die Stammdaten Ihrer Seite bearbeiten";
//Sprache/Language Backend:
$this->content->template['message_293']="Sprache/Language Backend:";
//auswählen/select
$this->content->template['message_294']="auswählen/select";
//Sprache/Language Frontend:
$this->content->template['message_295']="Sprache/Language Frontend:";
//Seitenname und Admin Email:
$this->content->template['message_296']="Seitenname und Admin Email:";
//Seitenname
$this->content->template['message_297']="Seitenurl (ohne http:// - z.B. www.papoo.de)";
//Hier bitte den Seitennamen eingeben
$this->content->template['message_298']="Hier bitte den Seitennamen eingeben";
//Administrator - E-Mail:
$this->content->template['message_299']="Administrator - E-Mail:";
//Überschrift ganz oben, oberhalb des Textes.
$this->content->template['message_300']="Überschrift ganz oben, oberhalb des Textes";
//Kopf-Titel :
$this->content->template['message_301']="Kopf-Titel:";
//Meta-Daten Ihrer Seite
$this->content->template['message_302']="Meta-Daten Ihrer Seite";
//Beschreibung
$this->content->template['message_303']="Beschreibung";
//Stichwörter
$this->content->template['message_304']="Stichwörter";
//Autor offiziell:
$this->content->template['message_305']="Autor offiziell:";
//Die Einstellungen für das Internet
$this->content->template['message_306']="Die Einstellungen für Zusatz Optionen.";
//Möchten Sie die rechte Spalte?
$this->content->template['message_307']="Möchten Sie die rechte Spalte?";
//Soll man sich einloggen können?
$this->content->template['message_308']="Soll man sich einloggen können?";
//Möchten Sie einen Styleswitcher?
$this->content->template['message_309']="Möchten Sie einen Styleswitcher?";
//Existiert ein Intranet?
$this->content->template['message_310']="Existiert ein Intranet?";
//Die Einstellungen für das Intranet
$this->content->template['message_311']="Die Einstellungen für das Intranet";
//Möchten Sie die rechte Spalte?:
$this->content->template['message_312']="Möchten Sie die rechte Spalte?";
//Soll man sich einloggen können?:
$this->content->template['message_313']="Soll man sich einloggen können?";
//Möchten Sie einen Styleswitcher?:
$this->content->template['message_314']="Möchten Sie einen Styleswitcher?";
//Welcher Editor soll für die Eingabe benutzt werden?
$this->content->template['message_315']="Welcher Editor soll standardmäßig benutzt werden?";
//Echter WYSIWYG Editor (standard)?:
$this->content->template['message_316']="HTMLArea?";
//bbCode Editor?:
$this->content->template['message_317']="bbCode Editor?";
//Fremdeditor?:
$this->content->template['message_318']="Fremdeditor?";
//Kopftext?
$this->content->template['message_319']="Kopftext?";
//Sie können hier eingeben, welcher Text auf der Startseite immer oben als Erstes stehen soll.  Dieser Text rutscht niemals nach. Wenn dort nichts stehen soll, einfach nichts eintragen (auch kein Leerzeichen).
$this->content->template['message_320']="Sie können hier eingeben welcher Text auf der Startseite immer oben als Erstes stehen soll.
Dieser Text rutscht niemals nach. Wenn dort nichts stehen soll, einfach nichts eintragen (auch kein Leerzeichen).";
//Weitere Einstellungen für die Startseite:
$this->content->template['message_321']="Weitere Einstellungen:";
//Sollen neue Artikel automatisch auf der Startseite erscheinen?
$this->content->template['message_322']="Sollen neue Artikel automatisch auf der Startseite erscheinen?";
// Sollen Artikel immer komplett dargestellt werden, auch wenn es mehrere zu einem Menü-Punkt gibt?
$this->content->template['message_323']="Sollen Artikel immer komplett dargestellt werden, auch wenn es mehrere zu einem Menü-Punkt gibt?";
//
$this->content->template['message_324']="message_324";
//Soll die Anzahl der Kommentare angezeigt werden?
$this->content->template['message_325']="Soll die Anzahl der Kommentare angezeigt werden?";
//Suchmaschinenfreundliche Adressen (URLs):
$this->content->template['message_326']="Suchmaschinenfreundliche Adressen (URLs):";
//Suchmaschinenfreundliche URLs können dann verwendet werden, wenn Ihr Server bestimmte Module installiert hat. Dieses sind entweder "mod_rewrite" oder "mod_mime", wobei "mod_rewrite" zu bevorzugen ist, wenn beides installiert ist.
$this->content->template['message_327']="Suchmaschinenfreundliche URLs können dann verwendet werden, wenn Ihr Server bestimmte Module installiert hat. Dieses sind entweder \"mod_rewrite\" oder \"mod_mime\", wobei \"mod_rewrite\" zu bevorzugen ist, wenn beides installiert ist.";
//Adressen mit mod_rewrite verbessern?
$this->content->template['message_328']="Adressen mit mod_rewrite verbessern?";
//Adressen mit mod_mime verbessern?
$this->content->template['message_329']="Adressen mit mod_mime verbessern?";
//Kein Modul vorhanden?
$this->content->template['message_330']="Kein Modul vorhanden?";
//Cachefunktion aktivieren:
$this->content->template['message_331']="Cachefunktion aktivieren:";
//Die Cachefunktion beschleunigt den Seitenaufbau erheblich (Faktor 10), stellen Sie aber sicher, dass das Verzeichnis <strong>cache</strong> auch beschreibbar ist.
$this->content->template['message_332']="Die Cachefunktion beschleunigt den Seitenaufbau erheblich (Faktor 10), stellen Sie aber sicher, dass das Verzeichnis <strong>cache</strong> auch beschreibbar ist. ";
//Soll die Cache Funktion aktiviert werden?:
$this->content->template['message_333']="Soll die Cache Funktion aktiviert werden?";
//Das Cache-Verzeichnis ist nicht beschreibbar! Ändern Sie die Rechte für das Verzeichniss über Ihr FTP Programm oder Ähnliches. Die Rechte müssen 777 sein.
$this->content->template['message_334']="Das Cache-Verzeichnis ist nicht beschreibbar! Ändern Sie die Rechte für das Verzeichnis über Ihr FTP Programm oder Ähnliches. Die Rechte müssen 777 sein.";

// Hier können Sie den Text im Kontakt-Formular ändern
$this->content->template['message_kontakttext_h1'] = "Hier können Sie den Text im Kontakt-Formular ändern.";

//Eine neue Datei hochladen
$this->content->template['message_335']="Eine neue Datei hochladen";
//<p>Sie können hier eine Datei auswählen, die Sie hochladen möchten.</p><p>Es gelten dabei folgende Regeln:<ol><li>Maximale Dateigröße 2MB</li><li>Erlaubte Formate sind: zip; doc; txt; pdf;</li></ol></p>
$this->content->template['message_336']="<p>Sie können hier eine Datei auswählen, die Sie hochladen möchten.</p>
Es gelten dabei folgende Regeln:
<ol><li>Empfohlene maximale Dateigröße 2MB
</li><li>Erlaubte Formate sind alle außer: .php und .html</li></ol>";
//Eingabe der Datei:
$this->content->template['message_337']="Eingabe der Datei:";
//Das Dokument:
$this->content->template['message_338']="Das Dokument:";
//Name der Datei, wie sie im Text bezeichnet werden soll.
$this->content->template['message_339']="Name der Datei, wie sie im Text bezeichnet werden soll:";
//Bezeichnung
$this->content->template['message_340']="Bezeichnung";
//Datei hochladen:
$this->content->template['message_344']="Datei hochladen:";
//Eine neue Datei hochladen
$this->content->template['message_345']="Eine neue Datei hochladen";
//Suche nach einer Datei in der Datenbank:
$this->content->template['message_346']="Suche nach einer Datei in der Datenbank:";
//Eingabe neuer Daten
$this->content->template['message_347']="Eingabe neuer Daten";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten.
$this->content->template['message_348']="Etwas stimmt nicht, bitte überprüfen Sie ihre Daten.";
//Änderung der Daten zu der Datei:
$this->content->template['message_349']="Änderung der Daten zu der Datei:";
//Beschreibung der Datei:
$this->content->template['message_350']="Beschreibung der Datei:";
//Name der Datei:
$this->content->template['message_351']="Name der Datei:";
//Änderung der Daten zur Datei.
$this->content->template['message_352']="Änderung der Daten zur Datei.";
//Hier können Sie die bereits in der Datenbank vorhandenen Daten ändern oder löschen.
$this->content->template['message_353']="Hier können Sie die bereits in der Datenbank vorhandenen Daten ändern oder löschen.";
//Änderung der Daten zu der Datei:
$this->content->template['message_354']="Änderung der Daten zu der Datei:";

$this->content->template['message_upload_change_uploadfile_legend'] = "Change File";
$this->content->template['message_upload_change_uploadfile_text'] = 
"Replace the existing file with a new one.
All inplacements in articles etc. will be left untouched";
$this->content->template['message_upload_change_uploadfile_label'] = "Select new file";
$this->content->template['message_upload_change_uploadfile_submit'] = ".. change";

$this->content->template['message_upload_delete_text'] = "Do you really want to delete the file?";

//Daten übergeben.
$this->content->template['message_355']="Daten übergeben.";
//hochladen
$this->content->template['message_356']="hochladen";
//ändern
$this->content->template['message_357']="ändern";
//Welchen Editor wollen Sie benutzen?
$this->content->template['message_358']="Welchen Editor wollen Sie benutzen?";
//Um den Namen oder die Beschreibung eines Forums zu ändern, einfach auf das Forum klicken, es öffnet sich die Bearbeitungsmaske des jeweiligen Forums.
$this->content->template['message_359']="Um den Namen oder die Beschreibung eines Forums zu ändern, einfach auf das Forum klicken, es öffnet sich die Bearbeitungsmaske des jeweiligen Forums.";
//Und hier die komplette Forenliste
$this->content->template['message_360']="Und hier die komplette Forenliste";
//An User
$this->content->template['message_361']="An User";
//Löschen
$this->content->template['message_362']="Löschen";
//Hier kann eine Sprache ausgewählt werden.
$this->content->template['message_363']="Hier kann eine Sprache ausgewählt werden.";
//Weitere Sprachen im Frontend:
$this->content->template['message_364']="Weitere Sprachen im Frontend:";
//Weitere Sprachen:
$this->content->template['message_365']="Weitere Sprachen:";
//Hier wird die Standard-Sprache festgelegt
$this->content->template['message_366']="Hier wird die Standard-Sprache festgelegt";
//Hier werden weitere Sprachen für das Frontend ausgewählt. Bedenken Sie, dass Sie für jede Sprache eigene Menüpunkte und Artikel eingeben müssen!
$this->content->template['message_367']="Hier werden weitere Sprachen für das Frontend ausgewählt. Bedenken Sie, dass Sie für jede Sprache eigene Menüpunkte und Artikel eingeben müssen!";
//Frontend
$this->content->template['message_368']="Frontend";
//ausloggen
$this->content->template['message_369']="ausloggen";
//Menü überspringen
$this->content->template['message_370']="Menü überspringen";
//Bildname
$this->content->template['message_371']="Bildname";
//Breite
$this->content->template['message_372']="Breite";
// Höhe
$this->content->template['message_373']=" Höhe";
//Orginal anschauen
$this->content->template['message_374']="Orginal anschauen";
//Thumbnail
$this->content->template['message_375']="Thumbnail";
//Bild
$this->content->template['message_376']="Bild";
//Beschreibung
$this->content->template['message_377']="Beschreibung";
//ändern
$this->content->template['message_378']="ändern";
//Daten auf
$this->content->template['message_379']="Daten auf ";
//HTMLArea für Screenreader
$this->content->template['message_380']="HTMLArea für Screenreader";
//Links in diesem Text:
$this->content->template['message_381']="Links in diesem Text:";
//Diese Tabelle listet alle verfügbaren Menüpunkte des Backends auf und ermöglicht die Erteilung der Zugriffsrechte, da die Tabelle ebenfalls ein komplexes Formular darstellt.
$this->content->template['message_382']="Diese Tabelle listet alle verfügbaren Menüpunkte des Backends auf und ermöglicht die Erteilung der Zugriffsrechte, da die Tabelle ebenfalls ein komplexes Formular darstellt.";
//Eingabe/Bearbeitung der Rechteverwaltung:
$this->content->template['message_383']="Eingabe/Bearbeitung der Rechteverwaltung:";
//Rechteverwaltung
$this->content->template['message_384']="Rechteverwaltung für Menü in der Administration";
//<p>Geben Sie hier an, welche Gruppen auf welche Menüpunkte in der Administration Zugriff haben. <br />Es erscheinen hier nur Gruppen, die Sie in der Gruppenverwaltung auch freigegeben haben für die Administration.</p><p>Administratoren haben immer Zugriff und können nicht verändert werden.</p>
$this->content->template['message_385']="<p>Geben Sie hier an, welche Gruppen auf welche Menüpunkte <strong>in der Administration</strong> Zugriff haben. <br />Es erscheinen hier nur Gruppen, die Sie in der Gruppenverwaltung auch freigegeben haben für die Administration.</p><p>Administratoren haben immer Zugriff und können nicht verändert werden.</p>";
//Menüname
$this->content->template['message_386']="Menüname";
//Die Startseite kann nicht gelöscht werden!
$this->content->template['message_387']="Die Startseite kann nicht gelöscht werden!";
//Hier können Sie die Menüreihenfolge ändern
$this->content->template['message_388']="Hier können Sie die Menüreihenfolge ändern";
// <p>Die Startseite kann nicht verändert werden, diese bleibt immer auf Platz 1!</p>    <p>Der Klick auf rauf oder runter des jeweiligen Menüpunktes schiebt den Punkt jeweils einen Platz rauf oder runter.<br />    Möchten Sie einen Punkt mehrere Ebenen hoch schieben, dann müssen Sie mehrfach klicken. Nach jedem Klick wird die Tabelle erneuert.</p>    <p> An der <strong>Verschachtelung</strong> der Untermenüebenen ändert sich nichts.</p>

$this->content->template['message_389']=" <p>Die Startseite kann nicht verändert werden, diese bleibt immer auf Platz 1!</p><p>Der Klick auf rauf oder runter des jeweiligen Menüpunktes schiebt den Punkt jeweils einen Platz rauf oder runter.
Möchten Sie einen Punkt mehrere Ebenen hoch schieben, dann müssen Sie mehrfach klicken. Nach jedem Klick wird die Tabelle erneuert.</p><p> An der <strong>Verschachtelung</strong> der Untermenüebenen ändert sich nichts.</p>";
//Alle Menüpunkte, deren Reihenfolge verändert werden kann.
$this->content->template['message_390']="Alle Menüpunkte, deren Reihenfolge verändert werden kann.";
// Reihenfolge runter
$this->content->template['message_391']=" Reihenfolge runter";
//Reihenfolge rauf
$this->content->template['message_392']="Reihenfolge rauf";
//runter
$this->content->template['message_393']="runter";
//rauf
$this->content->template['message_394']="rauf";
//Zurück
$this->content->template['message_395']="Zurück";
//Ändern der Artikelreihenfolge
$this->content->template['message_396']="Ändern der Artikelreihenfolge";
//Artikelreihenfolge
$this->content->template['message_397']="Artikelreihenfolge";
//Dieses Forum darf nicht gellöscht werden.
$this->content->template['message_398']="Dieses Forum darf <strong>nicht</strong> gelöscht werden.";
//Das Plugin wurde installiert und ist nun bereit.
$this->content->template['message_399']="Das Plugin wurde installiert und ist nun bereit.";
//Das Plugin wurde deinstalliert und ist nun gelöscht.
$this->content->template['message_400']="Das Plugin wurde deinstalliert und ist nun gelöscht.";
//<!-- Wer darf downloaden?-->
$this->content->template['message_401']="Wer darf downloaden?";
// Einstellungen für das Forum
$this->content->template['message_402']="Einstellungen für das Forum:";
//Soll das Forum als Board angezeigt werden?
$this->content->template['message_402_2']="Soll das Forum als Board angezeigt werden?";
//Wie soll die Liste der letzten Forums-Beträge angezeigt werden?
$this->content->template['message_402_3']="Wie soll die Liste der letzten Forums-Beträge angezeigt werden?";
$this->content->template['message_402_3_1']="Einzelne Beträge anzeigen (default)";
$this->content->template['message_402_3_2']="Nur Themen anzeigen";
//Wer darf im Frontend darauf zugreifen?
$this->content->template['message_403']="Wer darf im Frontend darauf zugreifen?";
//Darf der Artikel kommentiert werden?
$this->content->template['message_404']="Darf der Artikel kommentiert werden?";
//Kommentare
$this->content->template['message_405']="Kommentare";
/*
 * <h2>CSS Dateien Ihrer Seite bearbeiten</h2>
<p> Sie können hier Ihre CSS Dateien bearbeiten, andere Layouts bequem einbinden und diese anpassen.</p>
 */
$this->content->template['message_406']="<h1>CSS Dateien Ihrer Seite bearbeiten</h1>
<p> Sie können hier Ihre CSS Dateien bearbeiten, andere Layouts bequem einbinden und diese anpassen.</p><h2>Weitere Layouts</h2>...</p> ";
/*
 * <h2>CSS Datei bearbeiten</h2>
<p>Sie können hier eine CSS Datei auswählen, auf den Link klicken und dann bearbeiten</p>
 */
$this->content->template['message_407']="nobr:<h1>CSS Datei bearbeiten</h1>
<p>Sie können hier eine CSS Datei auswählen, auf den Link klicken und dann bearbeiten.<br />Sie können hier in der Übersicht direkt mehrere Styles aktivieren oder deaktivieren.</p>
<p>Durch Klick auf das Bild sehen Sie eine Großansicht des Styles.</p>";
/*
 * <h2>Neue CSS Datei hochladen</h2>
<p> Wählen Sie eine CSS Datei aus, die Sie in das System einbinden wollen</p>
 */
$this->content->template['message_408']="<h2>Neue CSS Datei hochladen</h2>
<p > Wählen Sie eine CSS Datei aus, die Sie in das System einbinden wollen. Sie können sowohl die zip Dateien aus <a href=\"http://www.papoo.de/shop/index.php/cPath/2/category/templates.html\">unserem Shop</a> als auch einfache CSS Dateien hochladen. Die nötigen Verzeichnisse und Dateien werden automatisch angelegt. ES wird für jeden Style ein eigenes Verzeichnis mit allen Daten und Bildern angelegt.</p>";
//Eingabe der Datei:
$this->content->template['message_409']="Eingabe der Datei:";
//Das Dokument:
$this->content->template['message_410']="Das Dokument:";
//Name des Styles
$this->content->template['message_411']="Name des Styles";
//Bezeichnung
$this->content->template['message_412']="Bezeichnung";
//Datei hochladen
$this->content->template['message_413']="Datei hochladen";
//Name des Styles
$this->content->template['message_414']="Name des Styles";
//Quicktags Editor
$this->content->template['message_415']="Quicktags Editor";
//Markdown Editor
$this->content->template['message_416']="Markdown Editor";
//Standard
$this->content->template['message_417']="Standard";
//Diese Tabelle listet Menüpunkte der Administrattion auf für die Rechtezuweisung
$this->content->template['message_418']="Diese Tabelle listet Menüpunkte der Administrattion auf für die Rechtezuweisung ";
//Eintrag zu Menüpunkt
$this->content->template['message_419']="Eintrag zu Menüpunkt";
//
$this->content->template['message_420']="Möchten Sie die Suchbox?";
// Plugin-Manager Überschrift: "Hier können Sie die Plugins verwalten"
$this->content->template['message_421']="Hier können Sie die Plugins verwalten";
// Plugin-Button "installieren"
$this->content->template['message_422']="installieren";
// Plugin-Button "deinstallieren"
$this->content->template['message_423']="deinstallieren";
// Installierte Plugins
$this->content->template['message_424']="Installierte Plugins";
// Weitere zur Verfügung stehende Plugins
$this->content->template['message_425']="Weitere zur Verfügung stehende Plugins";
$this->content->template['message_425_2']='<p>Weitere Plugins können nur durch einen Administrator installiert werden.</p>';
//Ihr Account wurde für 10 Minuten gesperrt.
$this->content->template['message_426']="Ihr Account wurde nach 4 falschen Login versuchen für 10 Minuten gesperrt!";
$this->content->template['message_diskspacelow']='Server disk space is very low. The login may fail.';
//
$this->content->template['message_427']="Seitenname z.B. \"Barrierefreies CMS Papoo\"";
//Safemode ist aktiviert. Sie müssen daher nach dem Upload der zip Datei diese manuell auf Ihrem Rechner entpacken und per FTP in das CSS Verzeichnis hochladen. Wenn Sie eine CSS Datei hochladen müssen Sie ein Verzeichnis mit dem Namen des CSS Datei anlegen (z.B. papoo für papoo.css). In diesem Verzeichnis muß dann noch das Verzeichnis bilder angelegt werden.
$this->content->template['message_428']="<h2>CSS Datei hochladen</h2><p> Sie müssen  die zip Datei manuell auf Ihrem Rechner entpacken und mit einem FTP Programm in das Verzeichnis /css im root Verzeichnis Ihrer Papoo Installation kopieren.</p><p>Wenn Sie nur eine CSS Datei haben müßen Sie ein Verzeichnis im /css Verzeichnis mit dem Namen der CSS Datei anlegen und die CSS Datei dort hinein kopieren. Die eingebundene Datei heißt immer _index.css</p><p>Nach dem Upload rufen Sie bitte die Seite CSS bearbeiten auf um Ihren neuen Style in das System einbinden zu können.</p><p>Bei Problemen stehen wir Ihnen gerne in unserem <a href=\"http://www.papoo.de/forum/menuid/138\">Forum </a>zur Verfügung.</p>";
//
$this->content->template['message_429']="Die CSS Datei ist nicht beschreibbar, bitte ändern Sie die Rechte. ";
//
$this->content->template['message_430']="Die IEFixes_CSS Datei ist nicht beschreibbar, bitte ändern Sie die Rechte.";
//
$this->content->template['message_431']="<p class=\"anzeige\">Weitere Plugins finden Sie auf  <a href=\"http://www.papoo.de/\" >unserer Seite</a>.</p><p>Um diese hier einzubinden, kopieren Sie bitte den Inhalt der entsprechenden Plugin zip Datei nach dem entzippen in das Plugins Verzeichnis Ihrer Papoo Installation.</p><p>Laden Sie danach diese Seite neu.</p>";
//
$this->content->template['message_432']="<strong>Es sind keine neuen Styledateien vorhanden.</strong>";
//
$this->content->template['message_433']="Möchten Sie die News in der rechten Spalte?";
//
$this->content->template['message_434']="Wenn ja, wieviele sollen dort auftauchen?";
//
$this->content->template['message_435']="Meta Informationen ";
//
$this->content->template['message_436']="Meta Title";
//
$this->content->template['message_437']="Meta Beschreibung";
//
$this->content->template['message_438']="Meta Schlüsselwörter";
//
$this->content->template['message_439']="Im Newsbereich der rechten Spalte erscheinen?";
$this->content->template['message_einloggen']="Einloggen";
//Sie haben hier Zugriff auf Ihre persönlichen Daten.
$this->content->template['messagex_436']="Sie haben hier Zugriff auf Ihre persönlichen Daten. ";
//es stehen
$this->content->template['messagex_437']="es stehen";
//veröffentlicht
$this->content->template['messagex_438']="zur Veröffentlichung an.";
//Ihre Daten
$this->content->template['messagex_439']="Ihre Daten";
//ändern
$this->content->template['message_440']="ändern";
//You can change your Content.
$this->content->template['message_441']="Sie können hier den Inhalt bearbeiten.";
//
$this->content->template['message_442']="Sie können hier alle Daten erreichen und bearbeiten die etwas mit dem Inhalt der Seite zu tun haben.";
//
$this->content->template['message_443']="Die System Daten bearbeiten";
//
$this->content->template['message_444']="System Daten können hier verwaltet werden.";
//
$this->content->template['message_445']="Sie können hier die Plugins verwalten";
//Hier können Sie die Medien verwalten
$this->content->template['message_446']="Hier können Sie die Medien verwalten";
//Unter Medien verstehen wir Dateien (.doc, .pdf etc.) und Bilder.
$this->content->template['message_447']="Unter Medien verstehen wir Dateien (.doc, .pdf etc.) und Bilder.";
//Hier können Sie die Gruppen verwalten
$this->content->template['message_448']="Hier können Sie die Gruppen verwalten";
//Mit Hilfe der Gruppen kann eine feingesteuerte rechteverwaltung realisiert werden.
$this->content->template['message_449']="Mit Hilfe der Gruppen kann eine feingesteuerte Rechteverwaltung realisiert werden.";
//<h1>Here you can edit the Articles of the Page</h1>
$this->content->template['message_450']="<h1>Hier können Sie die Artikel Ihrer Seite bearbeiten.</h1>";
//Vor dem Sprachwechsel 1x auf eintragen klicken.
$this->content->template['message_451']="Vor dem Sprachwechsel 1x auf eintragen klicken.";
//Inhalt
$this->content->template['message_452']="Inhalt";
//Teaser
$this->content->template['message_453']="Teaser";
//Rechte
$this->content->template['message_454']="Rechte";
//Einstellungen
$this->content->template['message_455']="Einstellungen";
//Vorschau
$this->content->template['message_456']="Vorschau";
//Versionen
$this->content->template['message_457']="Versionen";
//Eintragen
$this->content->template['message_458']="Eintragen";
//Artikel mit Überschrift anzeigen
$this->content->template['message_459']="Artikel mit Überschrift anzeigen";
//Artikel mit Teaser anzeigen
$this->content->template['message_460']="Artikel mit Teaser anzeigen";
//Größe des Teaser Bildes:
$this->content->template['message_461']="Größe des Teaser Bildes:";
//Ändern Sie den Eintrag um eine andere Größe zu bekommen.
$this->content->template['message_462']="Ändern Sie den Eintrag um eine andere Größe zu bekommen.";
//Breite
$this->content->template['message_463']="Breite";
//Höhe
$this->content->template['message_464']="Höhe";
//Artikel für RSS Feed bereitstellen?
$this->content->template['message_465']="Artikel für RSS Feed bereitstellen?";
//Artikel für RSS
$this->content->template['message_466']="Artikel für RSS";
//Zeitrahmen
$this->content->template['message_467']="Zeitrahmen";
//Dauerhaft Veröffentlichen?
$this->content->template['message_468']="Dauerhaft Veröffentlichen?";
//Oder
$this->content->template['message_469']="Oder";
//Format.Tag.Monat.Jahr
$this->content->template['message_470']="Format: Tag.Monat.Jahr Stunde:Minute";
//Veröffentlichen von (z.B.:
$this->content->template['message_471']="Veröffentlichen von (z.B.:";
//Veröffentlichen bis (z.B.:
$this->content->template['message_472']="Veröffentlichen bis (z.B.: ";
//Danach verschieben zu Menüpunkt (ist dann dort dauerhaft aktiv)
$this->content->template['message_473']="Danach verschieben zu Menüpunkt (ist dann dort dauerhaft aktiv)";
// Artikel deaktivieren
$this->content->template['artikel_einstellung']['pub_wohin_standard'] = "Artikel deaktivieren";
// Wollen Sie den Artikel wirklich löschen?
$this->content->template['artikel_save']['loeschen_legend'] = "Wollen Sie den Artikel wirklich löschen?";
// Den Artikel übernehmen?
$this->content->template['artikel_save']['uebernehmen_legend'] = "Autor ändern?";
$this->content->template['artikel_save']['uebernehmen_text'] = "Sie sind nicht der Autor dieses Artikels. Wenn Sie als Autor dieses Artikel genannt werdenn wollen, klicken Sie hier";
$this->content->template['artikel_save']['uebernehmen_checkbox'] = "Autor ändern.";
//vom
$this->content->template['message_474']="vom";
//Versionen aus der aktuellen Bearbeitung
$this->content->template['message_475']="Versionen aus der aktuellen Bearbeitung";
//Versionen aus vorherigen Bearbeitungen
$this->content->template['message_476']="Versionen aus vorherigen Bearbeitungen";
//Alphabetisch
$this->content->template['message_477']="Alphabetisch";
//Zeitlich
$this->content->template['message_478']="Zeitlich";
//Menüpunkt
$this->content->template['message_479']="Menüpunkt";
//Menü-Punkt auswählen
$this->content->template['message_480']="Menü-Punkt auswählen";
//Wählen Sie den Menü-Punkt zu dem Sie die Artikel-Reihenfolge ändern wollen:
$this->content->template['message_481']="Wählen Sie den Menü-Punkt zu dem Sie die Artikel-Reihenfolge ändern wollen:";
//auswählen
$this->content->template['message_482']="auswählen";
//Soll ein Extra Stylesheet eingebunden werden?
$this->content->template['message_483']="Soll ein Extra Stylesheet eingebunden werden?";
//Der Pfad zum aktuellen CSS Verzeichnis wird automatisch erzeugt. Nötig ist hier also nur der Datei Name im CSS Verzeichnis.
$this->content->template['message_484']="Der Pfad zum aktuellen CSS Verzeichnis wird automatisch erzeugt. Nötig ist hier also nur der Datei Name im CSS Verzeichnis.";
//Name der Datei.
$this->content->template['message_485']="Name der Datei.";
//Soll das für alle Unterpunkte gelten?
$this->content->template['message_486']="Soll das für alle Unterpunkte gelten?";
//Nachricht(en) verschieben
$this->content->template['message_487']="Nachricht(en) verschieben";
//Wählen Sie das Forum aus:
$this->content->template['message_488']="Wählen Sie das Forum aus:";
//keine
$this->content->template['message_489']="keine";
//In die Datenbank eintragen
$this->content->template['message_490']="In die Datenbank eintragen";
//Bilder hochladen
$this->content->template['message_491']="Bilder hochladen";
//Wählen Sie eine Kategorie aus
$this->content->template['message_492']="Wählen Sie eine Kategorie aus";
//Es wurden keine Bilder gefunden.
$this->content->template['message_493']="Es wurden keine Bilder gefunden.";
//Soll dieser Eintrag wirklich gelöscht werden?
$this->content->template['message_494']="Soll dieser Eintrag wirklich gelöscht werden?";
//Kategorie
$this->content->template['message_495']="Kategorie";
//ändern
$this->content->template['message_496']="ändern";
//Neue Kategorie anlegen
$this->content->template['message_497']="Neue Kategorie anlegen";
//Name der Kategorie
$this->content->template['message_498']="Name der Kategorie";
//Name eingeben:
$this->content->template['message_500']="Name eingeben:";
//Welche Gruppen dürfen auf die Kategorie zugreifen?:
$this->content->template['message_501']="Welche Gruppen dürfen auf die Kategorie zugreifen?:";
//Gilt nur für die Bearbeitung in der Admin.
$this->content->template['message_502']="Gilt nur für die Bearbeitung in der Admin.";
//Die existierenden Kategorien
$this->content->template['message_503']="Die existierenden Kategorien";
//Sie können hier die Automatismen die Papoo bietet editieren.
$this->content->template['message_504']="Sie können hier die Automatismen die Papoo bietet editieren.";
//Einstellungen für Artikel
$this->content->template['message_505']="Einstellungen für Artikel:";
//Soll der Autor angezeigt werden?
$this->content->template['message_506']="Soll der Autor angezeigt werden?";
//Soll angezeigt werden, wie oft der Artikel schon besucht wurde?
$this->content->template['message_507']="Soll angezeigt werden, wie oft der Artikel schon besucht wurde?";
//Benachrichtigungen bei:
$this->content->template['message_508']="Benachrichtigungen bei:";
//Einem neuen Benutzer?
$this->content->template['message_509']="Einem neuen Benutzer?";
//Einem neuen Forumseintrag?
$this->content->template['message_510']="Einem neuen Forumseintrag?";
//Einem neuen Gästebucheintrag?
$this->content->template['message_511']="Einem neuen Gästebucheintrag?";
//Einem neuen Kommentar?
$this->content->template['message_512']="Einem neuen Kommentar?";
//Email Adresse für die Benachrichtigungen
$this->content->template['message_513']="Email Adresse für die Benachrichtigungen";
//Spamschutz
$this->content->template['message_514']="Spamschutz";
//Spamschutz-Modus
$this->content->template['message_515']="Spamschutz-Modus";
//kein Spamschutz
$this->content->template['message_516']="kein Spamschutz";
//Spamschutz durch Anzeige eines Bildes mit einer Kennzahl
$this->content->template['message_517']="Spamschutz durch Anzeige eines Bildes mit einer Kennzahl";
//Spamschutz durch Lösen einer einfach Aufgabe
$this->content->template['message_518']="Spamschutz durch Lösen einer einfachen Aufgabe";
//Spamschutz durch Sortieren einzelner Zeichen
$this->content->template['message_519']="Spamschutz durch Sortieren einzelner Zeichen";
//Spamschutz zufällig auswählen
$this->content->template['message_520']="Spamschutz zufällig auswählen";
// Spamschutz im Kontakt-Formular aktivieren.
$this->content->template['message_515_1']="Spamschutz im Kontakt-Formular aktivieren.";
//Sicherung der Datenbank erstellen
$this->content->template['message_521']="Sicherung der Datenbank erstellen";
//Sie können hier eine Sicherung der Datenbank erstellen, die Sie nach einer Neuinstallation oder zu einem beliebigen andern Zeitpunkt wieder einspielen können.
$this->content->template['message_522']="Sie können hier eine Sicherung der Datenbank erstellen, die Sie nach einer Neuinstallation oder zu einem beliebigen andern Zeitpunkt wieder einspielen können.";
$this->content->template['message_522a']="Sicherung downloaden mit Rechtsklick -> Speichern unter:.";
$this->content->template['message_522b']="Sicherung nach dem Download löschen auf dem Server!!! WICHTIG!!!.";

$this->content->template['message_522c']="Sicherung Download ";
$this->content->template['message_522d']="Sie können hier die restore.sql zurückspielen. Laden Sie dafür eine Sicherungs SQL Datei über FTP in das Verzeichnis /interna/templates_c, benennen diese in restore.sql um und klicken dann auf den unten stehenden Link. Das ist nur notwendig wenn die Datei zu groß ist und die Rücksicherung mit dem unten stehenden Formular nicht funktioniert. <br />";

$this->content->template['message_522e']="restore.sql Datei zurückspielen ";
//Jetzt eine Sicherung erstellen
$this->content->template['message_523']="Jetzt eine Sicherung erstellen";
//Eine Sicherung einspielen
$this->content->template['message_524']="Eine Sicherung einspielen";

$this->content->template['message_524a']="Eine Sicherung hochladen und einspielen";
//Sicherungs Datei wurde eingespielt.
$this->content->template['message_525']="Sicherungs Datei wurde eingespielt.";
/**Um eine Sicherung einzuspielen wählen Sie bitte die Sicherungsdatei aus:</p>
<p><strong style="color:red;">ACHTUNG - Wenn Sie eine Sicherung einspielen werden alle aktuellen Daten unwiederruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung!</strong>*/
$this->content->template['message_526']='Um eine Sicherung einzuspielen wählen Sie bitte die Sicherungsdatei aus:</p>
<p><strong style="color:red;">ACHTUNG - Wenn Sie eine Sicherung einspielen werden alle aktuellen Daten unwiederruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung!</strong>';
//Gruppe auswählen
$this->content->template['message_527']="Gruppe auswählen";
//Ergebnis exportieren
$this->content->template['message_528']="Ergebnis exportieren";
//exportieren
$this->content->template['message_529']="exportieren";
//Vorname
$this->content->template['message_530']="Vorname";
//Nachname
$this->content->template['message_531']="Nachname";
//Strasse + Hausnummer
$this->content->template['message_532']="Strasse + Hausnummer";
//Postleitzahl
$this->content->template['message_533']="Postleitzahl";
//Wohnort
$this->content->template['message_534']="Wohnort";
//Benutzername
$this->content->template['message_535']="Benutzername";
//Signatur
$this->content->template['message_signatur']="Signatur";
//Welche Forum Ansicht möchten Sie?
$this->content->template['message_536']="Welche Forum Ansicht möchten Sie?";
//Thread Ansicht
$this->content->template['message_537']="Thread Ansicht";
//Board Ansicht
$this->content->template['message_538']="Board Ansicht";
//Wählen Sie hier Ihren dauerhaften Style aus, mit dem Sie immer automatisch eingeloggt werden.
$this->content->template['message_539']="Wählen Sie hier Ihren dauerhaften Style aus, mit dem Sie immer automatisch eingeloggt werden.";
//Modul-Manager
$this->content->template['message_540']="Modul-Manager";
//Liste installierter Module:
$this->content->template['message_541']="Liste installierter Module:";
//Nummer
$this->content->template['message_542']="Nummer";
//Bereich
$this->content->template['message_543']="Bereich";
//Diesem Bereich sind keine Module zugewiesen.
$this->content->template['message_544']="Diesem Bereich sind keine Module zugewiesen.";
//Diesem Bereich ein weiteres Modul hinzufügen:
$this->content->template['message_545']="Diesem Bereich ein weiteres Modul hinzufügen:";
//Modul hinzufügen
$this->content->template['message_546']="Modul hinzufügen";
//Diese(r) Style(s) ist im Verzeichnis aber nicht aktiviert.
$this->content->template['message_547']="Diese(r) Style(s) ist im Verzeichnis aber nicht aktiviert.";
//Klicken Sie auf den Style um ihn zu aktivieren.
$this->content->template['message_548']="Klicken Sie auf den Style um ihn zu aktivieren.";
//Dateien auswählen von Style:
$this->content->template['message_549']="Dateien auswählen von Style:";
//Klicken Sie auf den Namen der Datei um sie zu bearbeiten.
$this->content->template['message_550']="Klicken Sie auf den Namen der Datei um sie zu bearbeiten.";
//Nicht beschreibbar, ändern Sie die Dateirechte!
$this->content->template['message_551']="Nicht beschreibbar, ändern Sie die Dateirechte!";
//Dieser Style ist Standard-Style.
$this->content->template['message_551']="Dieser Style ist Standard-Style.";
//Diesen Style zum Standard machen.
$this->content->template['message_552']="Diesen Style zum Standard machen.";
//Diesen Style löschen
$this->content->template['message_553']="Diesen Style löschen";
//Klicken Sie hier um den Style sofort zu löschen!!<br /><strong>Achtung</strong> Wenn Sie auf den Link klicken wird der Style gelöscht.
$this->content->template['message_554']="Klicken Sie hier um den Style sofort zu löschen!!<br /><strong>Achtung</strong> Wenn Sie auf den Link klicken wird der Style gelöscht. ";
//
$this->content->template['message_555']="Zurück zur Übersicht.";
//Aktuelle Papoo News
$this->content->template['message_556a']='Aktuelle Papoo News';
//
$this->content->template['message_556b']='<h1>Papoo Administration</h1>';
$this->content->template['message_556c']='nobr:Willkommen in der Papoo Administration.<br/>Sie können hier
alle Einstellungen Ihres Systems bearbeiten.<br /><br />';
$this->content->template['message_556c2']='nobr:

In der rechten Spalte auf der internen Startseite sehen Sie immer die aktuellen Papoo News, so bleiben Sie immer auf dem aktuellsten Stand.<br /><br /><div class="hilfetop"><strong>Hier lesen Sie auch die Sicherheitshinweise die evtl. einen Update erfordern!</strong></div>
';
$this->content->template['message_556c3']='nobr:<h1>Hilfe</h1>
Hilfe zu allen Menüpunkten hier in der Adminstration finden Sie immer wenn Sie auf den roten Hilfebutton rechts oben klicken. Diese Hilfe wird von der Papoo Community gestaltet.<br />
<strong>Die Hilfe ist immer kontextsensitiv!</strong><br />Es wird also immer der passende Eintrag angezeigt.<br /><br />
<div class="hilfetop">
<strong>Sie können selber an der Hilfe Dokumentation mitarbeiten</strong> und so die Community Hilfe mit verbessern!</div>';
$this->content->template['message_556']='nobr:<h1>Übersicht der Accesskeys</h1>
<ul>
<li>alt+0: Hilfe</li>
<li>alt+9: Hilfe Fenster schließen</li>
<li>alt+1: Interne Startseite</li>
<li>alt+2: Ausloggen</li>
<li>alt+8: Direkt zum Inhalt innerhalb einer Seite</li>
</ul>';
$this->content->template['message_556xy']='
<h2>Tabelle aller Menüpunkte zur Schnell Navigation</h2>
<p>
Um diese Funktion auszulösen, einfach nach dem Laden der Seite z.B. go01 eingeben und dann die Enter Taste drücken um zur internen Startseite zu gelangen.</p>
<p>Wenn man schon etwas geschrieben hat, vorher die Tastenkombination shift+x drücken, dann go01 gefolgt von der Enter Taste.</p>
<strong>Diese Funktion benötigt Javaskript!</strong>
<br /><br />
<div style="width:85%;height:250px;overflow:auto;border:1px solid #ccc;padding:10px;">
<table>
<tr>
<th>Nenü Name</th>
<th>Befehl</th>';
//

$this->content->template['message_557'] = 'Soll die "Artikel versenden/empfehlen"-Funktion angezeigt werden?';
$this->content->template['message_558'] = "Kein Link bei aktivem Menüpunkt?";
$this->content->template['message_559'] = 'Soll die Anzahl der Seiten-Besucher angezeigt werden?';
$this->content->template['message_560'] = 'Soll der Link zum Artikel-Drucken angezeigt werden?';
$this->content->template['message_560a']='Shall the automatic replacement be carried out (eg, acronyms, etc.)?';
$this->content->template['message_560b'] = 'Shall the information to check or publish an article also be send as e-mail?';
//Klicken Sie auf den Style um ihn zu aktivieren.
$this->content->template['intcss_klickaktiv'] = 'Klicken Sie auf den Style um ihn zu aktivieren.';
//Bezeichnung ändern
$this->content->template['intcss_bezaend'] = 'Bezeichnung ändern';
//Style löschen
$this->content->template['intcss_bezloesch'] = 'Style löschen';
//Wählen Sie aus bei welchem Style Sie die Module ändern möchten.
$this->content->template['intmod_staend'] = 'Wählen Sie aus bei welchem Style Sie die Module ändern möchten.';
//Auswählen
$this->content->template['intimg_ausw'] = 'Auswählen';
//Urls mit mod_rewrite und sprechenden Urls?
$this->content->template['stamm_mod_rewrite_sprech'] = 'Urls mit mod_rewrite und sprechenden Urls?';
//Cache Einstellungen
$this->content->template['stamm_cache1'] = 'Cache Einstellungen';
//Cache aktivieren?
$this->content->template['stamm_cache2'] = 'Cache aktivieren?';
//Cache Lifetime in Sekunden (3600 = 1 Stunde)
$this->content->template['stamm_cache3'] = 'Cache Lifetime in Sekunden (3600 = 1 Stunde)';
//Artikelbearbeitung in einer kompletten Übersicht (Sinnvoll für Screenreader)
$this->content->template['message_561'] = 'Artikelbearbeitung in einer kompletten Übersicht';
//Quicktags Editor Screenreader Extension
$this->content->template['message_562'] = 'Quicktags Editor Screenreader Extension';
$this->content->template['message_562a'] = 'show content- and menu-tree expanded';
//Voreinstellung veröffentlichen auf jeder?
$this->content->template['message_563'] = 'Voreinstellung veröffentlichen auf jeder?';
//Voreinstellung auf veröffentlichen?
$this->content->template['message_564'] = 'Voreinstellung auf veröffentlichen?';
//Voreinstellung auf Startseite listen?
$this->content->template['message_565'] = 'Voreinstellung auf Startseite listen?';
//Menürechte Voreinstellung auf jeder?
$this->content->template['message_566'] = 'Menürechte Voreinstellung auf jeder?';
// Voreinstellungen
$this->content->template['message_567'] = 'Voreinstellungen für Artikel';
// Voreinstellungen
$this->content->template['message_568'] = 'Chefredakteur hat immer Schreibrechte';
/**
<h2>Papoo Lizenzen und Autoren</h2>
<p>Papoo ist ein Produkt von Carsten Euwens - Papoo Software und Design und steht unter der GPL Lizenz.<br />
Der Name Papoo ist geschützt und darf nur mit Erlaubnis von Carsten Euwens - Papoo Software benutzt werden.</p>
<p>Ihre Version ist:
*/
$this->content->template['creditdata1'] = 'nobr:<h2>Papoo Lizenzen und Autoren</h2>
<p>Papoo ist ein Produkt von Carsten Euwens - Papoo Software und Design und steht unter der unten eingeblendeten Lizenz.<br />
Der Name Papoo ist geschützt und darf nur mit Erlaubnis von Dr. Carsten Euwens - Papoo Software benutzt werden.</p>
<p>Ihre Version ist: ';
/**
<h3>Autor</h3>
<p>
(c) Carsten Euwens, 2006<br />
<h3>Weitere Autoren</h3>
* Stephan Bergmann, 2006
</p>
<h3>Papoo benutzt die folgenden weiteren Software Bestandteile mit den jeweils genannten Lizenzen.</h3>
<ul>
<li>
PHP Mailer Klasse von Brent R. Matzelle, LGPL
</li>
<li>SMTP Klasse von Chris Ryan, LGPL
</li>
<li>Snoopy Klasse von Monte Ohrt, LGPL
</li>
<li>BBCode Klasse von Christian Seiler, Artistic License
</li>
<li>ezSQL Klasse von Justin Vincent, Freeware
</li>
<li>Upload Klasse von Timo Reith, GPL
</li>
<li>Icons von Noia Warm Icons - Carlitus (Carles Carbonell Bernado), GPL
</li>
<li>Smarty Template Engine - ispi of Lincoln, Inc., LGPL
</li>
<li>TinyMce Editor von Moxiecode Systems, GPL</li>
</ul>
*/
$this->content->template['creditdata2'] = 'nobr:<h3>Autor</h3>
<p>
(c) Carsten Euwens, 2007<br />
<h3>Weitere Autoren</h3>
* Stephan Bergmann, 2007
</p>
<h3>Papoo benutzt die folgenden weiteren Software Bestandteile mit den jeweils genannten Lizenzen.</h3>
<ul>
<li>
PHP Mailer Klasse von Brent R. Matzelle, LGPL
</li>
<li>SMTP Klasse von Chris Ryan, LGPL
</li>
<li>Snoopy Klasse von Monte Ohrt, LGPL
</li>
<li>BBCode Klasse von Christian Seiler, Artistic License
</li>
<li>ezSQL Klasse von Justin Vincent, Freeware
</li>
<li>Icons von Noia Warm Icons - Carlitus (Carles Carbonell Bernado), GPL
</li>
<li>Smarty Template Engine - ispi of Lincoln, Inc., LGPL
</li>
<li>TinyMce Editor von Moxiecode Systems, GPL</li>
</ul>
<br />
<br />
<h2>Lizenz</h2>
<h2>   Software-Lizenzbedingungen ab Papoo 3.6.1</h2><br><h3>   1. Vorbemerkung</h3><strong>   1.1</strong>    Diese Lizenzbedingungen gelten ergänzend zu den Allgemeinen Geschäftsbedingungen. Die Lizenzbedingungen werden durch das Fortsetzen der Installation anerkannt.<br><br><h2><strong>   2. Einräumung von Nutzungsrechten</strong></h2><strong>   2.1</strong>    Mit Vertragsschluss über die Lieferung/den Download von <span class="lang_en" xml:lang="en" lang="en"> Software</span>  (unabhängig vom Speichermedium) wird dem Kunden das nicht übertragbare und nicht ausschließliche Nutzungsrecht an der vertragsgegenständlichen <span class="lang_en" xml:lang="en" lang="en"> Software</span>  eingeräumt, das auf die nachfolgend beschriebene Nutzung beschränkt ist. Alle dort nicht ausdrücklich aufgeführten Nutzungsrechte verbleiben bei Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  bzw. Dr. Carsten Euwens (Papoo <span class="lang_en" xml:lang="en" lang="en"> Software)</span>  als Inhaber aller Urheber- und Schutzrechte.<br><br><h3>   3. Umfang der Nutzungsrechte</h3><strong>   3.1 </strong>   Mit der Lieferung erwirbt der Kunde das Recht, die ihm gelieferte <span class="lang_en" xml:lang="en" lang="en"> Software</span>  im vertragsgemäßen Umfang (Anzahl der erworbenen Lizenzen) auf beliebigen Rechnern zu nutzen, die für diese Zwecke geeignet sind. Die Dauer des Nutzungsrechts ist für Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> Produkte unbegrenzt. <br><br><strong>   3.2 </strong>   Der Kunde verpflichtet sich, das Programm nur für eigene Zwecke zu nutzen und es Dritten weder unentgeltlich noch entgeltlich zu überlassen. Die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  darf pro Lizenz nur unter einer <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  auf einem <span class="lang_en" xml:lang="en" lang="en"> Server,</span>  nicht jedoch gleichzeitig auf zwei oder mehreren Domains, genutzt werden. Für die Nutzung einer weiteren <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  ist eine weitere Domainlizenz erforderlich. Pro Domainlizenz darf eine weitere <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  mit der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  genutzt werden. Eine Domainlizenz ist nicht erforderlich, wenn verschiedene Domainnamen auf den gleichen Inhalt verweisen, wie z.B. papoo.de und papoo.org.<br><strong><br>   3.2.1</strong>    Die Papoo Light Version darf hingegen auf beliebig vielen Domains genutzt werden, dies gilt für nicht kommerzielle Auftritte wie rein private Internetauftritte und Internetauftritte gemeinnütziger Organisationen. Alle anderen Betreiber müßen eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  Lizenz erwerben die in allen Produkten außer Papoo Light schon enthalten ist. <br><strong><br>   3.2.2</strong>    Auf lokalen Testumgebungen die nicht der Öffentlichkeit zur Verfügung stehen, darf jede erworbene Version beliebig getestet werden.<br><br><strong>   3.3 </strong>   Der Kunde ist berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  auf die Festplatte des Servers zu installieren und zu nutzen sowie von der Originaldiskette oder CD-ROM eine Sicherungskopie zu fertigen, die aber nicht gleichzeitig neben der Originalversion genutzt werden darf. Im Falle eines Vertrages über eine Netzwerkversion/Mehrfach-Lizenz ist der Kunde berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  entsprechend der vertraglichen Vereinbarung zu jedem Zeitpunkt auf einem oder mehreren Rechnern mit mehreren Personen gleichzeitig zu nutzen.<br><br><strong>   3.4 </strong>   Der Kunde ist nicht berechtigt, Kopien der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu erstellen, sofern die Kopien nicht zu Datensicherungszwecken erfolgen und auch nur zu diesem Zwecke eingesetzt werden. Er darf ferner die Softwarebestandteile, mitgelieferte Bilder, das Handbuch, Begleittexte sowie die zur <span class="lang_en" xml:lang="en" lang="en"> Software</span>  gehörige Dokumentation durch Fotokopieren oder Mikroverfilmen, elektronische Sicherung oder durch andere Verfahren nicht vervielfältigen, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugehörige Dokumentation weder vertreiben, vermieten, Dritten Unterlizenzen hieran einräumen noch diese in anderer Weise Dritten zur Verfügung stellen. Der Kunde ist nicht berechtigt, Zugangskennungen und/oder Passwörter für das Produkt oder für Datenbankzugänge, die mit dem Produkt im Zusammenhang stehen, an Dritte weiterzugeben. <br><br><strong>   3.5</strong>    Der Kunde ist  befugt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugehörige Dokumentation ganz oder teilweise ausschließlich für die eigenen Bedürfnisse zu ändern, zu modifizieren, anzupassen oder zu dekompilieren. <br>   Weiterhin ist es dem Kunden untersagt, Copyrightvermerke, Kennzeichen/Markenzeichen und/oder Eigentumsangaben des Herausgebers an Programmen oder am Dokumentationsmaterial zu verändern. Allerdings ist es möglich die Copyrightvermerke durch das erwerben einer sogenannten Whitelabel Lizenz für jeweils eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  aus dem Fuß der Seite zu entfernen.<br><br><strong>   3.6 </strong>   Das Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> nutzt den TinyMCE Editor von Moxiecode, der unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz steht. Sie akzeptieren ebenfalls die Nutzung dieses Plugins und weitere Papoo Plugins von Drittherstellern unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz.<br><br><h3>   4. Haftung</h3><strong>   4.1 </strong>   Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  übernimmt keine Haftung für die Fehlerfreiheit der <span class="lang_en" xml:lang="en" lang="en"> Software.</span>  Insbesondere übernimmt die Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  keine Gewährleistung dafür, dass die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  Ihren Anforderungen und Zwecken genügt oder mit anderen von Ihnen ausgewählten Programmen zusammenarbeitet. Die Verantwortung für die richtige Auswahl und die Folgen der Benutzung der <span class="lang_en" xml:lang="en" lang="en"> Software,</span>  sowie der damit beabsichtigten oder erzielten Ergebnisse, tragen Sie selbst.<br><br><strong>   4.2</strong>    Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  haftet nicht für Schäden die aufgrund der Benutzung dieser <span class="lang_en" xml:lang="en" lang="en"> Software</span>  oder der Unfähigkeit diese <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu verwenden entstehen. Wir haften nicht auf Schadensersatz für Mängel oder andere Pflichtverletzungen. Ausgenommen hiervon sind Schäden aus der Verletzung des Lebens, des Körpers oder der Gesundheit, wenn wir die Pflichtverletzung zu vertreten haben, und für sonstige Schäden, die auf einer vorsätzlichen oder grob fahrlässigen Pflichtverletzung durch uns oder auf einer von uns erklärten Garantie beruhen. Ausgenommen sind auch Schäden, für die wir nach dem Produkthaftungsgesetz zwingend haften oder die auf einer schuldhaften Verletzung wesentlicher Vertragspflichten zurückzuführen sind. In letzterem Fall beschränkt sich unsere Haftung auf den vorhersehbaren, typischerweise eintretenden Schaden.<br><br>   Die Pflichtverletzung unserer gesetzlichen Vertreter oder unserer Erfüllungsgehilfen steht einer Pflichtverletzung durch uns gleich.
';
//Geben Sie hier das Präfix der alten Installation an:
$this->content->template['message_569'] = 'Geben Sie hier das Präfix der alten Installation an:';
// Das Präfix bitte ohne &quot;_&quot; angeben, also z.B. &quot;123&quot; und nicht &quot;123_&quot;.
$this->content->template['message_570'] = 'Das Präfix bitte ohne &quot;_&quot; angeben, also z.B. &quot;123&quot; und nicht &quot;123_&quot;.';
// Zugriffs-Parameter der alten Datenbank
$this->content->template['message_571'] = 'Zugriffs-Parameter der alten Datenbank';
// Befinden sich Ihre alten Daten nicht in derselben Datenbank wie die jetzige Installation,
#					müssen Sie hier zusätzlich die Zugriffs-Paramter der alten Datenbank angeben.<br />
#					Liegen die Daten in derselben Datenbank, könnnen sie die folgenden Felder einfach so belassen.
$this->content->template['message_572'] = 'Befinden sich Ihre alten Daten nicht in derselben Datenbank wie die jetzige Installation,
					müssen Sie hier zusätzlich die Zugriffs-Paramter der alten Datenbank angeben.<br />
					Liegen die Daten in derselben Datenbank, könnnen sie die folgenden Felder einfach so belassen.';
// Der alte Datenbank-Server:
$this->content->template['message_573'] = 'Der alte Datenbank-Server:';
// Der alte Datenbank-Name:
$this->content->template['message_574'] = 'Der alte Datenbank-Name:';
// Der alte Datenbank-Benutzer-Name:
$this->content->template['message_575'] = 'Der alte Datenbank-Benutzer-Name:';
// Das alte Datenbank-Benutzer-Passwort:
$this->content->template['message_576'] = 'Das alte Datenbank-Benutzer-Passwort:';
// Diese Felder können leider nicht auf ihre Richtigkeit überprüft werden. Haben Sie hier also falsche Angaben gemacht,
					#erhalten Sie nach Absenden der Seite eine Datenbank-Fehlermeldung oder eine komplett weisse Seite. In diesem Fall
				#	verwenden Sie bitte den &quot;Zurück-Knopf&quot; Ihres Browser und korrigieren Sie Ihre Angaben.
$this->content->template['message_577'] = 'Diese Felder können leider nicht auf ihre Richtigkeit überprüft werden. Haben Sie hier also falsche Angaben gemacht,
					erhalten Sie nach Absenden der Seite eine Datenbank-Fehlermeldung oder eine komplett weisse Seite. In diesem Fall
					verwenden Sie bitte den &quot;Zurück-Knopf&quot; Ihres Browser und korrigieren Sie Ihre Angaben.';
// CSS-Styles einbeziehen
$this->content->template['message_578'] = 'CSS-Styles einbeziehen';
/*
		Möchten Sie auch die Datenbank-Einträge der CSS-Sytles ihrer alten Installation übernehmen,
					dann können Sie die folgende Option aktivieren.<br />
					Beachten Sie dabei aber, dass die CSS-Styles dieser Installation dadurch verloren gehen.
					Wir empfehlen deshalb die CSS-Styles nicht zu übernehmen und sie anschließend über den Menü-Punkt
					&quot;System -> CSS-Layout&quot; einzupflegen.
*/
$this->content->template['message_579'] = '		Möchten Sie auch die Datenbank-Einträge der CSS-Sytles ihrer alten Installation übernehmen,
					dann können Sie die folgende Option aktivieren.<br />
					Beachten Sie dabei aber, dass die CSS-Styles dieser Installation dadurch verloren gehen.
					Wir empfehlen deshalb die CSS-Styles nicht zu übernehmen und sie anschließend über den Menü-Punkt
					&quot;System -> CSS-Layout&quot; einzupflegen.';
// CSS-Styles einbeziehen
$this->content->template['message_580'] = 'CSS-Styles einbeziehen';
// Datenbank-Parameter setzen
$this->content->template['message_581'] = 'Datenbank-Parameter setzen';
// Parameter setzen
$this->content->template['message_582'] = 'Parameter setzen';
// Die folgenden Tabellen sind in ihrer aktuellen Installation nicht vorhanden.
$this->content->template['message_583'] = 'Die folgenden Tabellen sind in ihrer aktuellen Installation nicht vorhanden.';
// Stellen Sie bitte sicher, dass Sie alle benötigten Plugins installiert haben.
$this->content->template['message_584'] = 'Stellen Sie bitte sicher, dass Sie alle benötigten Plugins installiert haben.';
// Sie können das Update trotzdem ausführen. Die oben aufgeführten Tabellen werden allerdings nicht in Ihre aktuelle
				#Installation übertragen.
$this->content->template['message_585'] = 'Sie können das Update trotzdem ausführen. Die oben aufgeführten Tabellen werden allerdings nicht in Ihre aktuelle
				Installation übertragen.';
// Update jetzt durchführen
$this->content->template['message_586'] = 'Update jetzt durchführen';
// update
$this->content->template['message_587'] = 'update';
// Die folgenden Tabellen wurden aktualisiert:
$this->content->template['message_588'] = 'Die folgenden Tabellen wurden aktualisiert:';
// Das alte Präfix:
$this->content->template['message_589'] = 'Das alte Präfix:';
//Update
//
$this->content->template['message_590'] = 'Update';
//Lesezeichen
$this->content->template['message_591'] = 'Lesezeichen';
//Lesezeichen
$this->content->template['message_591a'] = 'Inhaltssprache';
//Lesezeichen
$this->content->template['message_591b'] = 'Ausgewählt: ';
//Lesezeichen
$this->content->template['message_592'] = 'Lesezeichen setzen';
//Lesezeichen
$this->content->template['message_593'] = 'Lesezeichen löschen';
//Soll mit Kategorien gearbeitet werden?
$this->content->template['message_594'] = 'Soll mit Kategorien gearbeitet werden?';
//Kategorien
$this->content->template['message_595'] = 'Kategorien';
//Dieser Menü-Punkt ist die Startseite und kann keinem anderen Menü-Punkt untergeordnet werden.
$this->content->template['message_596'] = 'Dieser Menü-Punkt ist die Startseite und kann keinem anderen Menü-Punkt untergeordnet werden.';
//Wählen Sie hier die Kategorie aus. Die Einstellung wirkt sich im Frontend nur aus wenn die Nutzung der Kategorien in den Stammdaten aktiviert ist.
$this->content->template['message_597'] = 'Wählen Sie hier die Kategorie aus. Die Einstellung wirkt sich im Frontend nur aus wenn die Nutzung der Kategorien in den Stammdaten aktiviert ist.';
//Sortierung der Kategorien
$this->content->template['message_598'] = 'Sortierung der Kategorien';
//Sortieren Sie die Kategorie aus die Sie bearbeiten wollen.
$this->content->template['message_599'] = 'Sortieren Sie die Kategorie aus die Sie bearbeiten wollen.';
//Wirklich l&ouml;schen?
$this->content->template['message_600'] = 'Wirklich l&ouml;schen?';
//Den Eintrag
$this->content->template['message_601'] = 'Den Eintrag ';
//wirklich l&ouml;schen?
$this->content->template['message_602'] = 'wirklich l&ouml;schen?';
//Löschen
$this->content->template['message_603'] = 'Löschen';
//Die Daten wurden gel&ouml;scht!
$this->content->template['message_604'] = 'Die Daten wurden gel&ouml;scht!';
//Die Daten wurden eingetragen!
$this->content->template['message_605'] = 'Die Daten wurden eingetragen!';
//Bitte bearbeiten Sie die Daten.
$this->content->template['message_606'] = 'Bitte bearbeiten Sie die Daten.';
//Kategorie bearbeiten
$this->content->template['message_607'] = 'Kategorie bearbeiten';
//Liste der Kategorien
$this->content->template['message_608'] = 'Liste der Kategorien';
//W&auml;hlen Sie die Kategorie aus die Sie bearbeiten wollen. Die Hauptkategorie kann nicht gelöscht werden.
$this->content->template['message_609'] = 'W&auml;hlen Sie die Kategorie aus die Sie bearbeiten wollen. Die Hauptkategorie kann nicht gelöscht werden.';
//&auml;ndern
$this->content->template['message_610'] = '&auml;ndern';
//Sie k&ouml;nnen hier Kategorien erstellen und bearbeiten. Diesen Kategorien k&ouml;nnen die Menüpunkte und Artikel zugeordnet werden.
$this->content->template['message_611'] = 'Sie k&ouml;nnen hier Kategorien erstellen und bearbeiten. Diesen Kategorien k&ouml;nnen die Menüpunkte und Artikel zugeordnet werden.';
$this->content->template['message_categories_inactive']='Sie m&uuml;ssen erst noch in der Systemkonfiguration angeben, dass Sie mit Kategorien arbeiten wollen, bevor diese benutzt werden k&ouml;nnen.';
//Die Daten wurden eingetragen!
$this->content->template['message_612'] = 'Die Daten wurden eingetragen!';
//Bitte bearbeiten Sie die Daten.
$this->content->template['message_613'] = 'Bitte bearbeiten Sie die Daten.';
//Neue Kategorie anlegen
$this->content->template['message_614'] = 'Neue Kategorie anlegen';
//Bezeichnung auf der Webseite
$this->content->template['message_615'] = 'Bezeichnung auf der Webseite';
//Interne Bezeichnung
$this->content->template['message_616'] = 'Interne Bezeichnung';
//Welche Gruppen haben Schreibzugriff in der Administration
$this->content->template['message_617'] = 'Welche Gruppen haben Schreibzugriff in der Administration';
//Welche Gruppen haben Lesezugriff im Frontend
$this->content->template['message_618'] = 'Welche Gruppen haben Lesezugriff im Frontend';
//Binden Sie hier Ihre Videos ein.
$this->content->template['message_619'] = 'Binden Sie hier Ihre Videos ein.';
//Sie können hier Ihre Videos einbinden die Sie per FTP in das Verzeichnis /video hochgeladen haben. Ein direkter Upload ist wg. der Größe der Videos nicht möglich.
$this->content->template['message_620'] = 'Sie können hier Ihre Videos einbinden die Sie per FTP in das Verzeichnis /video hochgeladen haben. Ein direkter Upload ist wg. der Größe der Videos nicht möglich.';
//Um ein Video einzubinden klicken Sie auf Videos einbinden. Sie können die videos auch Kategorien zuordnen, dafür dann Video Kategorien auswählen.
$this->content->template['message_621'] = 'Um ein Video einzubinden klicken Sie auf Videos einbinden. Sie können die videos auch Kategorien zuordnen, dafür dann Video Kategorien auswählen.';
//Videos einbinden
$this->content->template['message_622'] = 'Videos einbinden';
//Bearbeiten Sie hier Ihre Video Daten
$this->content->template['message_623'] = 'Bearbeiten Sie hier Ihre Video Daten';
//Eingebundene Videos
$this->content->template['message_624'] = 'Eingebundene Videos';
//Klicken Sie auf ein Video um es zu starten und die Daten zu bearbeiten.
$this->content->template['message_625'] = 'Klicken Sie auf ein Video um es zu starten und die Daten zu bearbeiten.';
//Nicht eingebundene Videos
$this->content->template['message_626'] = 'Nicht eingebundene Videos';
//Klicken Sie auf ein Video um die Daten zu bearbeiten und es einzubinden.
$this->content->template['message_627'] = 'Klicken Sie auf ein Video um die Daten zu bearbeiten und es einzubinden.';
//Bearbeiten Sie die Video Daten
$this->content->template['message_628'] = 'Bearbeiten Sie die Video Daten';
//Geben Sie hier die notwendigen Daten ein.
$this->content->template['message_629'] = 'Geben Sie hier die notwendigen Daten ein.';
//Video
$this->content->template['message_630'] = 'Video';
//Name des Videos
$this->content->template['message_631'] = 'Name des Videos';
//Beschreibung (Was passiert auf dem Video, bitte in genauen Beschreibungen angeben ...):
$this->content->template['message_632'] = 'Beschreibung (Was passiert auf dem Video, bitte eine genaue Beschreibung angeben ...):';
//Dieser Link führt zur
$this->content->template['message_633'] = 'Dieser Link führt zur';
//Dieser Link führt eine Seite weiter.
$this->content->template['message_634'] = 'Dieser Link führt eine Seite weiter.';
//Seite
$this->content->template['message_635'] = 'Seite';
//Dieser Link führt eine Seite zurück.
$this->content->template['message_636'] = 'Dieser Link führt eine Seite zurück.';
//Die aktuell angezeigte Seite.
$this->content->template['message_637'] = 'Die aktuell angezeigte Seite.';
//Wenn nicht, welcher User soll darüber benachrichtigt werden:
$this->content->template['message_638'] = 'Wenn nicht, welcher User soll darüber benachrichtigt werden:';
//Auswählen
$this->content->template['message_639'] = 'Auswählen';
//alle
$this->content->template['message_640'] = 'alle';
//Username
$this->content->template['message_641'] = 'Benutzername';
//keine (default)
$this->content->template['message_642'] = 'keine (default)';
//Update
$this->content->template['message_643'] = 'Update';
//ändern
$this->content->template['message_644'] = 'ändern';
//	Sie haben nur noch
$this->content->template['message_645'] = 'Sie haben nur noch';
//MB Speicher &uuml;brig. Bevor Sie weitere Plugins installieren erh&ouml;hen 			Sie den verf&uuml;gbaren Speicher.
$this->content->template['message_646'] = 'MB Speicher &uuml;brig. Bevor Sie weitere Plugins installieren erh&ouml;hen 			Sie den verf&uuml;gbaren Speicher.';
//Achtung
$this->content->template['message_647'] = 'Achtung';
//Wollen Sie das folgende Plugin wirklich deinstallieren?
$this->content->template['message_648'] = 'Wollen Sie das folgende Plugin wirklich deinstallieren?';
//abbrechen
$this->content->template['message_649'] = 'Abbrechen';
//Direkt zum Bereich
$this->content->template['message_650'] = 'Direkt zum Bereich';
//Style XML:
$this->content->template['message_651'] = 'Style XML:';
//Style XML File
$this->content->template['message_652'] = 'Style XML File';
//Datei:
$this->content->template['message_653'] = 'Datei:';
//Nach unten verschieben
$this->content->template['message_654'] = 'Nach unten verschieben';
//Nach oben verschieben
$this->content->template['message_655'] = 'Nach oben verschieben';
//Modul aus diesem Bereich entfernen
$this->content->template['message_656'] = 'Modul aus diesem Bereich entfernen';
//Login
$this->content->template['message_657'] = 'Login';
//Einstellungen für die Bilderverwaltung
$this->content->template['message_658'] = 'Einstellungen für die Bilderverwaltung';
//Funktioniert nur im Zusammenhang mit dem Export/Import Plugin.
$this->content->template['message_659'] = 'Funktioniert nur im Zusammenhang mit dem Export/Import Plugin.';
//Daten exportieren?
$this->content->template['message_660'] = 'Daten exportieren?';
//Dabei werden die Bilder auf anderen Seiten direkt von diesem Server bezogen.
$this->content->template['message_661'] = 'Dabei werden die Bilder auf anderen Seiten direkt von diesem Server bezogen.';
//Daten importieren?
$this->content->template['message_662'] = 'Daten importieren?';
//Sprache
$this->content->template['message_663'] = 'Sprache';
//Titel
$this->content->template['message_664'] = 'Titel';
//Klicken Sie für Hilfe - es öffnet sich ein neues  Fenster
$this->content->template['message_665'] = 'Klicken Sie für Hilfe - es öffnet sich ein neues  Fenster';
//Hilfe Icon
$this->content->template['message_667'] = 'Hilfe Icon';
//Hits
$this->content->template['message_668'] = 'Hits';
//Freischalten
$this->content->template['message_669'] = 'Freischalten';
//Liste aller Menüpunkte zur Schnell Navigation
$this->content->template['message_670'] = 'Liste aller Menüpunkte zur Schnell Navigation';
//Start
$this->content->template['message_671'] = 'Start';
//Loggoff
$this->content->template['message_672'] = 'Ausloggen';
//W&auml;hlen Sie den Template Satz
$this->content->template['message_673'] = 'W&auml;hlen Sie den Template Satz';
//Templatesatz
$this->content->template['message_674'] = 'Templatesatz';
//Standard
$this->content->template['message_675'] = 'Standard';
//Menü
$this->content->template['message_676'] = 'Menü';
//Artikel
$this->content->template['message_677'] = 'Artikel';
//Sie dürfen diesen Artikel nicht verändern.
$this->content->template['message_678'] = 'Sie dürfen diesen Artikel nicht verändern.';
//Finden
$this->content->template['message_679'] = 'Finden';
//Eingabe
$this->content->template['message_680'] = 'Eingabe';
//Bilder
$this->content->template['message_681'] = 'Bilder';
// PopUp-Bilder
$this->content->template['message_681_1'] = 'PopUp-Bilder';
//auswählen
$this->content->template['message_682'] = 'auswählen';
//Downloads
$this->content->template['message_683'] = 'Downloads';
//CSS-Klassen
$this->content->template['message_684'] = 'CSS-Klassen';
//Sprache
$this->content->template['message_685'] = 'Sprache';
//
$this->content->template['message_686'] = 'Video löschen';
/*
<h2>Einträge in der 3. Spalte</h2>
		<p>Suchen Sie hier den Eintrag aus der dritten Spalte den Sie bearbeiten wollen.</p>
		<p>Um die Einträge zu sortieren klicken Sie auf orderid eintragen. Die Sortierung betrifft immer alle Einträge und werden dann entsprechend den Zuordnungen im Frontend angezeigt. Hier werden immer alle angezeigt.</p>
		*/
$this->content->template['message_687'] = 'nobr:<h1>Einträge in der 3. Spalte</h1>
		<p>Suchen Sie hier den Eintrag aus der dritten Spalte den Sie bearbeiten wollen.</p>
		<p>Um die Einträge zu sortieren klicken Sie auf orderid eintragen. Die Sortierung betrifft immer alle Einträge und werden dann entsprechend den Zuordnungen im Frontend angezeigt. Hier werden immer alle angezeigt.</p>';
//Name / bearbeiten
$this->content->template['message_688'] = 'Name / bearbeiten';
//Orderid
$this->content->template['message_689'] = 'Orderid';
//Orderid f&uuml;r
$this->content->template['message_690'] = 'Orderid f&uuml;r';
//Name
$this->content->template['message_691'] = 'Name';
//Name des Eintrags
$this->content->template['message_692'] = 'Name des Eintrags';
//anzeigen
$this->content->template['message_693'] = 'anzeigen';
//Immer anzeigen
$this->content->template['message_694'] = 'Immer anzeigen';
//Wenn immer anzeigen, Häckchen setzen und auf speichern klicken
$this->content->template['message_695'] = 'Wenn immer anzeigen, Häckchen setzen und auf speichern klicken';
//Vorhandene Einträge
$this->content->template['message_696'] = 'Vorhandene Einträge';
//Menuid
$this->content->template['message_697'] = 'Menuid';
//entfernen
$this->content->template['message_698'] = 'entfernen';
//Bearbeiten Sie hier das Kontaktformular
$this->content->template['message_699'] = 'Bearbeiten Sie hier das Kontaktformular';
//Eintrag wurde gelöscht
$this->content->template['message_700'] = 'Eintrag wurde gelöscht';
//Sie können hier die Felder Ihres Kontaktformulares bearbeiten.
$this->content->template['message_701'] = 'Sie können hier die Felder Ihres Kontaktformulares bearbeiten.</p><p><strong>Die E-Mail Adresse an die das Formular gesendet wird stellen Sie in den Stammdaten unter "Administrator - E-Mail" ein.</strong><p>Flexible Formulare mit mehreren Empfängern und wesentlich mehr Funktionen können Sie über unseren <a target="blank" href="http://www.papoo.de/index/menuid/204/reporeid/216">Formularmanger</a> erstellen.</p>';
//Neues Feld erzeugen
$this->content->template['message_702'] = 'Neues Feld erzeugen';
//Um ein neues Feld zu erzeugen klicken Sie hier:
$this->content->template['message_703'] = 'Um ein neues Feld zu erzeugen klicken Sie hier:';
//Neues Feld erzeugen
$this->content->template['message_704'] = 'Neues Feld erzeugen';
//Vorhanden Felder
$this->content->template['message_705'] = 'Vorhanden Felder';
//Um die Daten zu bearbeiten klicken Sie auf den Eintrag bearbeiten dann die Daten.
$this->content->template['message_706'] = 'Um die Daten zu bearbeiten klicken Sie auf den Eintrag bearbeiten dann die Daten.';
//Felder im Kontaktformular
$this->content->template['message_707'] = 'Felder im Kontaktformular';
//bearbeiten
$this->content->template['message_708'] = 'bearbeiten';
//Sie können hier ein neues Feld eintragen.
$this->content->template['message_709'] = 'Sie können hier ein neues Feld eintragen.';
//Sie können hier ein Feld bearbeiten.
$this->content->template['message_710'] = 'Sie können hier ein Feld bearbeiten.';
//Name des Feldes (keine Umlaute oder Sonderzeichen)
$this->content->template['message_711'] = 'Name des Feldes (keine Umlaute oder Sonderzeichen)';
//Wählen Sie den Typ aus.
$this->content->template['message_712'] = 'Wählen Sie den Typ aus.';
//Typ
$this->content->template['message_713'] = 'Typ';
//Text (default)
$this->content->template['message_714'] = 'Text (default)';
//Beschreibende Daten (Sprache:
$this->content->template['message_715'] = 'Beschreibende Daten (Sprache: ';
//Bezeichnung im Formular
$this->content->template['message_716'] = 'Bezeichnung im Formular';
//Einstellungen
$this->content->template['message_717'] = 'Einstellungen';
//Feld muß ausgefüllt werden
$this->content->template['message_718'] = 'Feld muß ausgefüllt werden';
//Dieses Feld löschen
$this->content->template['message_719'] = 'Dieses Feld löschen';
//Löschen
$this->content->template['message_720'] = 'Löschen';
//
$this->content->template['message_721'] = 'Menü hinzufügen';
/**
<h1>Blacklist Daten bearbeiten</h1>
<p>Sie können hier die Blacklist bearbeiten. Bei allen Foren- Gästebuch und Kommentareinträgen wird gegen diese Einträge hier kontrolliert.</p>
<p>Kommt ein Eintrag vor, wird der Eintrag verworfen.</p>
<h2>Einträge</h2>
<p>Um einen neuen Eintrag hinzuzufügen, tragen Sie ihn einfach in die Liste ein.
<strong>Jeder neue Zeile ist ein Eintrag.</strong><br />
Um einen Eintrag zu löschen, löschen Sie ihn einfach aus der Liste.
</p>*/
$this->content->template['message_722'] = 'nobr:<h1>Blacklist Daten bearbeiten</h1>
<p>Sie können hier die Blacklist bearbeiten. Bei allen Foren- Gästebuch und Kommentareinträgen wird gegen diese Einträge hier kontrolliert.</p>
<p>Kommt ein Eintrag vor, wird der Eintrag kommentarlos verworfen um Spambots kein Feedback zu liefern.</p>
<h2>Einträge</h2>
<p>Um einen neuen Eintrag hinzuzufügen, tragen Sie ihn einfach in die Liste ein.
<strong>Jede neue Zeile erzeugt einen Eintrag.</strong><br />
Um einen Eintrag zu löschen, löschen Sie ihn einfach aus der Liste.
</p>';
//Tragen Sie hier die Einträge ein.
$this->content->template['message_723'] = 'Tragen Sie hier die Blacklist Einträge ein.';
$this->content->template['message_724'] = 'Speichen Sie die Daten.';
$this->content->template['message_725'] = 'Speichern';
//CSS Style
$this->content->template['message_726'] = 'Moduleinstellungen';
$this->content->template['message_727'] = 'nobr:<p>Sie haben hier grundsätzlich 2 Möglichkeiten des Updates.</p><h2>Daten aus einer alten Installation übernehmen</h2>
<strong>Dies gilt für Versionen vor 3.6.1</strong><br />
Update des neu installierten Systems mit den Inhalten aus einer Vorversion, da werden die Inhalte aus der alten Datenbank in diese neu Installation übernommen.<br />
<a href="./update.php?menuid=65&amp;start_update=1">Update/Import starten</a>
<h2>Bestehende Installation updaten / Normalfall</h2>
<strong>Dies gilt für Versionen ab 3.6.1</strong><br />
Updates dieses Systems auf eine neue Version. Dafür muß die update.sql Datei per FTP hochgeladen werden. <br /><br /><strong>Kopieren Sie dafür das Verzeichnis /update aus der zip Datei der neuen Version in das root Verzeichnis dieser Installation.</strong><br /><br />Nach dem erfolgreichen Update können Sie alle Daten der neuen Version per FTP auf den Server kopieren und die alten Dateien überschreiben.<br />
<a href="./update.php?menuid=65&amp;start_update=2">Update der bestehenden Installation starten</a><br />
<h2>Achtung - Wichtig!</h2><strong>Erstellen Sie vorher unbedingt eine Sicherung aller Dateien und der Datenbank!!!</strong>';
$this->content->template['message_728'] = 'Papoo Update';
$this->content->template['message_729'] = 'Update der bestehenden Installation';
$this->content->template['message_730'] = 'nobr:
<p>Die Update Datei ist vorhanden. Sie sehen hier den Inhalt der ausgeführt wird:</p>
<p>Lesen Sie bitte vorher den Begleittext.</p>';
$this->content->template['message_731'] = 'nobr:
<h3>Update wurde erfolgreich durchgeführt.</h3>
<p>Sie können jetzt die Dateien der neuen Version über diese Installation (per FTP) drüberkopieren. Beachten Sie aber, dass auch die Standardtemplates überschrieben werden.</p><p><strong>Vergessen Sie nicht eine Sicherung der Dateien vorher zu erstellen!</strong></p>';
$this->content->template['message_732'] = 'Update Begleittext';
$this->content->template['message_733'] = 'Update Inhalt';
$this->content->template['message_734'] = 'nobr:
<h2>Update starten</h2>
<p><a href="./update.php?menuid=65&start_update=2&update_now=ok">Das Update mit den obigen SQL Anweisungen durchführen.</a></p>';
$this->content->template['message_735'] = 'Offline / Wartungsarbeiten';
$this->content->template['message_736'] = 'Seite offline? (Gilt nicht für User root)';
$this->content->template['message_737'] = 'Offline / Wartungstext';
$this->content->template['message_738'] = 'nobr:<h1>Artikel verwalten.</h1><p>Hier können Sie Artikel erstellen und bearbeiten, es sind beliebig viele Artikel möglich.</p>';
$this->content->template['message_739'] = '<h1>Layout bearbeiten</h1><p>Hier können Sie das Layout von Papoo bearbeiten, zum einem welche Module wo angezeigt werden sollen, welche CSS verwendet werden soll und welche Templates.</p>';
$this->content->template['message_740'] = '<legend>Menü-Punkt auswählen</legend>
			<p>Wählen Sie den Menü-Punkt zu dem Sie die Artikel-Reihenfolge ändern wollen:</p>';
$this->content->template['message_741'] = 'Menü-Punkt';
$this->content->template['message_742'] = 'Neues Layout einbinden -';
$this->content->template['message_743'] = 'Schritt 1 von 5';
$this->content->template['message_744'] = 'Schritt 2 von 5';
$this->content->template['message_745'] = 'Schritt 3 von 5';
$this->content->template['message_746'] = 'Schritt 4 von 5';
$this->content->template['message_747'] = 'Schritt 5 von 5';
$this->content->template['message_748'] = 'Der Style wurde eingetragen und steht jetzt im System zur Verfügung.';
$this->content->template['message_749'] = 'Sie können die Template Dateien und die CSS Dateien jetzt über die link stehenden Menüpunkte anpassen.';
$this->content->template['message_750'] = 'Die nächsten Schritte';
$this->content->template['message_751'] = 'nobr:<ol>
<li>
<strong>Stellen Sie jetzt im Modulmanger für den neu erstellten Style ein, welche Elemente wo erscheinen sollen. </strong></li>
<li>
<strong>Kopieren Sie jetzt (per FTP) die Bilder in das Verzeichnis /css/Ihr_style/images.</strong></li>
</ol>';
$this->content->template['message_752'] = 'nobr:<div class="message">
Die HTML Datei wurde angepasst.
</div>
<h2>Geben Sie nun die Ausgabe Module ein.</h2>
<p>Sie können hier nun die index.html Datei anpassen, damit auch die Inhalte von Papoo über dieses Template ausgegeben werden können, es stehen dafür fünf Module zur Verfügung.
</p>
<p>
Diese fünf Bereiche finden Sie auch im Modulmanager wieder wo Sie für jeden Bereich dann einfach hinzuklicken können was wo erscheinen soll, z.B. der Content in der Mitte, das Menü links, die Suchmaske oben usw.
</p>
<p>Sie können die Datei auch später wieder anpassen über die Template Verwaltung. Die Datei die hier erstellt wird ist die __index.html (mit 2 Unterstrichen).
</p>';
$this->content->template['message_753'] = '(Da werden alle Module im Kopfbereich eingebunden)';
$this->content->template['message_754'] = '(Da werden alle Module im linken Bereich eingebunden)';
$this->content->template['message_755'] = '(Da werden alle Module im rechten Bereich eingebunden)';
$this->content->template['message_756'] = '(Da werden alle Module im mittleren Bereich eingebunden)';
$this->content->template['message_757'] = '(Da werden alle Module im Fuss eingebunden)';
$this->content->template['message_758'] = '<p>Kopieren Sie die Module in die Bereiche Ihres HTML Templates wo die Inhalte erscheinen sollen.</p>';
$this->content->template['message_759'] = 'HTML Template - Inhalt aus Ihrer index.html';
$this->content->template['message_760'] = 'nobr:
<div class="message">
Die CSS Datei wurde gespeichert.
</div>
<h2>Geben Sie nun die index.html Datei ein.</h2>
<p>Sie können hier nun die index.html Datei aus der Vorlage einkopieren. Öffnen Sie dafür die Datei die Sie bekommen haben, kopieren Sie den Inhalt, das gesamte HTML, in das unten stehende Eingabe Feld.</p>
<p><strong>Es sind nur tabellenlose Templates mit div Strukturen möglich.</strong>
</p>
<p><strong>Wenn Sie das Feld leerlassen, wird das Standard Template genutzt und Sie springen direkt zum letzten Schritt.</strong>
</p>';
$this->content->template['message_761'] = 'HTML Template - Inhalt aus Ihrer index.html';
$this->content->template['message_762'] = 'nobr:
<div class="message">
Die Verzeichnisse wurden erstellt.
</div>
<h2>Geben Sie nun das CSS ein</h2>
<p>Sie können hier aus einer Vorlage die Sie bekommen haben das CSS einkopieren. Achten Sie darauf dass Sie später wahrscheinlich die Pfade zu den Bilder usw. anpassen müßen.</p>';
$this->content->template['message_763'] = 'CSS Anweisungen';
$this->content->template['message_764'] = 'nobr:
<div class="error">
Fehler! Die Verzeichnisse müßen per Hand kopiert werden.
</div>
<p>Kopieren Sie die Verzeichnisse in die neuen Verzeichnisse mit Ihrem Layout Namen über Ihren (FTP) Zugang. Eine automatische Erstellung ist leider nicht möglich.</p>
<ul>
<li>/templates/standard/</li>
<li>/templates_c/standard/</li>
<li>/css/vorlage/</li>
</ul>
';
$this->content->template['message_765'] = 'nobr:<div class="error">
Fehler! Die folgenden Verzeichnisse brauchen Schreibrechte.
</div>
<h2>Verzeichnisse</h2>
<p>Erstellen Sie diese Verzeichnis und/oder geben sie diesen Verzeichnissen Schreibrechte über Ihren (FTP) Zugang.</p>';
$this->content->template['message_766'] = 'nobr:<div class="error">
Bitte tragen Sie etwas ein.
</div>';
$this->content->template['message_767'] = 'nobr:
<p>Geben Sie hier den Namen an mit dem das Layout angelegt werden soll, am besten ein Wort da aus diesem Namen auch die Verzeichnisnamen erstellt werden, 15 Zeichen maximal.</p>';
$this->content->template['message_768'] = 'Name des neuen Layouts';
$this->content->template['message_769'] = 'nobr:
<p>Sie können hier Schritt für Schritt ein neues Layout einbinden, achten Sie bitte darauf dass die unten angezeigten Verzeichnisse die richtigen Schreibrechte haben.</p>
<h2>Verzeichnisse mit Schreibrechten</h2>
<p>Hier werden die Verzeichnisse angezeigt die SChreibrechte brauchen, neben jedem Verzeichnis steht ob die Schreibrechte in Ordnung sind oder nicht.</p>
<p>Falls die Schreibrechte nicht richtig sind, stellen Sie die Verzeichnisse bitte auf 777 bei Linux/Unix Servern und beschreibbar bei Windowsservern.</p>
<h2>Verzeichnisse</h2>';
$this->content->template['message_770'] = 'Schreibrechte ok';
$this->content->template['message_771'] = 'Schreibrechte bitte ändern';
$this->content->template['message_772'] = 'Neues Layout anlegen';
$this->content->template['message_773'] = 'Klicken Sie hier um eine neues Layout anzulegen.';
$this->content->template['message_774'] = 'Content Templates';
$this->content->template['message_775'] = 'Daten wurden eingetragen!';
$this->content->template['message_776'] = 'Daten wurden gelöscht!';
$this->content->template['message_777'] = 'nobr:
<p>Erstellen Sie hier einen neuen Content Eintrag, hier haben Sie nur die reine HTML Ansicht zur Verfügung.</p>
<strong>Verwenden Sie hier keinen body, titel oder head tag.</strong>';
$this->content->template['message_778'] = 'Name des Eintrages';
$this->content->template['message_779'] = 'HTML Inhalt (kein Body tag verwenden!)';
$this->content->template['message_780'] = 'Löschen';
$this->content->template['message_781'] = 'Template löschen';
$this->content->template['message_782'] = 'nobr:
<p>Die Content Templates können im TinyMCE Editor über den Template Button eingebunden werden. Damit kann man zum einem dann Vorlagen nutzen um immer das gleiche Layout zu verwenden, zum andern komplexere DIV Strukturen einbinden, die im TinyMce Editor nicht so ohne weiteres zu erstellen sind.</p>
<h2>Neuen Eintrag erstellen</h2>';
$this->content->template['message_783'] = 'Erstellen Sie hier ein neues Template';
$this->content->template['message_784'] = 'Liste der Content Templates';
$this->content->template['message_785'] = 'nobr:
<h1>Templates des Papoo Systems bearbeiten</h1>
<p>Sie können hier sowohl die System Templates des Papoo Systems bearbeiten als auch die Content Templates.</p>
<p>Mit den System Templates beeinflußen Sie die Ausgabe des gesamten Systems.</p>
<p>Die Content Templates werden im tinyMce Editor verwendet, diese können ale Formatierungsvorlagen verwendet werden.</p>
';
$this->content->template['message_786'] = 'System Templates';
$this->content->template['message_787'] = '<p>Bearbeiten Sie hier die Template Datei.</p>';
$this->content->template['message_788'] = 'zurück';
$this->content->template['message_789'] = 'Template Datei ';
$this->content->template['message_790'] = 'bearbeiten.';
$this->content->template['message_791'] = 'Inhalt des Templates';
$this->content->template['message_792'] = 'Template speichern';
$this->content->template['message_793'] = 'Speichern';
$this->content->template['message_794'] = 'nobr:
<p>Sie können hier die einzelnen Dateien des Templates aussuchen, achten Sie darauf das die Dateien auch Schreibrechte brauchen, damit Sie die Dateien auch speichern können.</p>
<p>
<strong>Die wichtigste Datei von der aus Sie alles einbinden ist die __index.html.</strong>
<h2>Einführung in Designumsetzung</h2>
<p>Eine Einführung in die CSS / Layout Umsetzung in Papoo finden Sie <a target="blank" href="../dokumente/design_einfuehrung.pdf">in diesem PDF Dokument</a>.</p>
</p>
<h2>Liste der Dateien</h2>';
$this->content->template['message_795'] = '(Nicht beschreibar)';
$this->content->template['message_796'] = '(beschreibbar)';
$this->content->template['message_797'] = 'nobr:
<p>Sie können hier die System Templates des Papoo Systems bearbeiten.</p>
<p>Mit den System Templates beeinflußen Sie die Ausgabe des gesamten Systems.</p>
<p>Wählen Sie aus der Liste der verfügbaren Templates das aus, welches Sie bearbeiten wollen.</p>
<p><strong>Die verfügbaren Templates finden Sie im Verzeichnis /templates über ihren FTP Zugang (bei xampp lokal natürlich über die Datei Ebene).</strong>
</p>
<h2>Einführung in Designumsetzung</h2>
<p>Eine Einführung in die CSS / Layout Umsetzung in Papoo finden Sie <a target="blank" href="../dokumente/design_einfuehrung.pdf">in diesem PDF Dokument</a>.</p>
<h2>Template Tabelle</h2>
<table>
<tr>
<th>Template Verzeichnis
</th>
<th>Template für folgende CSS Styles
</th>
<th>Standard (ja/nein)
</th>
</tr>';
$this->content->template['message_798'] = '
Nicht installiert.';
$this->content->template['message_799'] = 'Ja';
$this->content->template['message_800'] = 'Aktiv';
$this->content->template['message_801'] = 'Newsletter';
$this->content->template['message_802'] = 'Soll die Kategorie auf der Startseite mit Artikeln erscheinen?';
$this->content->template['message_803'] = 'Ja';
$this->content->template['message_804'] = 'Anzahl der Artikel die auf der Startseite erscheinen soll:';
$this->content->template['message_805'] = 'Wählen Sie hier die Kategorie aus.';
$this->content->template['message_806'] = 'Kategorie';
$this->content->template['message_807'] = 'Soll mit Kategorien für Artikel auf der Startseite gearbeitet werden?';
$this->content->template['message_807a'] = 'Gästebucheinträge ungeprüft freischalten.';
$this->content->template['message_807b'] = 'Papoo News im Interna Bereich anzeigen.';
$this->content->template['message_808'] = 'Trenner für die sprechenden urls (Achtung wenn die im laufenden Betrieb geändert wird, dann müßen alle Links innerhalb der Artikel korrigiert werden.)?:';
$this->content->template['message_809'] = 'Artikel fixieren?';
$this->content->template['message_810'] = 'Wenn ja, wird der Artikel bei dem ausgewähltem Menüpunkt oben als erster fixiert.';
$this->content->template['message_811'] = 'Teaser Link NICHT anzeigen?';
$this->content->template['message_812'] = 'Wenn ja, wird der Artikel <strong>nicht</strong> verlinkt.';
$this->content->template['message_813'] = 'Screenshot (Klick für Großansicht)';
$this->content->template['message_814'] = 'Name';
$this->content->template['message_815'] = 'Standard?';
$this->content->template['message_816'] = 'Aktiv (Ja/Nein)';
$this->content->template['message_817'] = 'Zum Standard machen.';
$this->content->template['message_818'] = 'Standardstyle wurde geändert.';
$this->content->template['message_819'] = 'Dump aus einer älteren Version';
$this->content->template['message_820'] = 'restore.sql aus einer älteren version zurückspielen';
$this->content->template['message_821'] = 'Alle Menüpunkte auf einmal zeigen? (Nützlich bei Flyout Menüs per JS)';
$this->content->template['message_config_uplaod_hide_praefix'] = 'Add no prefix on uploaded files';
$this->content->template['message_stamm_documents_change_backup'] = 'on change of a document-file, create a backup on the server?';
$this->content->template['message_config_tiny_advimg_filelist'] = 'In TinyMCE on link-select show file-list with small symbols';
$this->content->template['message_822'] = 'Alle Artikel der Untermenüpunkte anteasern?';
$this->content->template['message_823'] = 'Mit diesem Haken werden zu diesem Menüpunkt auch alle Artikel der zugehörigen Untermenüpunkte angeteasert.';
$this->content->template['message_824'] = 'hinzufügen';
$this->content->template['message_825'] = 'Liste der zusätzlich zugeordneten Menüpunkte';
$this->content->template['message_826'] = 'entfernen / delete';
$this->content->template['message_827'] = 'Soll mit Kategorien für Artikel auch auf Unterseiten gearbeitet werden?';
$this->content->template['message_828']='Neue Artikel oben einordnen. Dabei zählt das manipulierbare Datumsfeld eines jeden Artikels "Dokument erstellt am"';
$this->content->template['message_829'] = 'Videos hochladen';
$this->content->template['message_830'] = 'Hier finden Sie die Videos die Sie per FTP hochgeladen haben aus dem Verzeichnis /video.';
$this->content->template['message_831'] = 'Falls die Videos zu groß sind, laden Sie die bitte per FTP in das Verzeichnis /video.';
$this->content->template['message_832'] = '';
$this->content->template['message_833'] = '';
$this->content->template['message_834'] = '';

$this->content->template['message_restore'] = 'restore';

/**
<h1>Die Einträge des Top Menüs</h1>
	 <p>Sie sehen hier die Einträge des Topmenüs. Klicken Sie auf einen Eintrag um ihn zu bearbeiten.</p>
		<p>Um einen neuen Eintrag zu erstellen, klicken Sie auf das grüne Plussymbol.</p>
		<h2>Die Einträge</h2>*/
$this->content->template['message_topmenu'] = 'nobr:<h1>Die Einträge des Top Menüs</h1>
	 <p>Sie sehen hier die Einträge des Topmenüs. Klicken Sie auf einen Eintrag um ihn zu bearbeiten.</p>
		<p>Um einen neuen Eintrag zu erstellen, klicken Sie auf das grüne Plussymbol.</p>
		<h2>Die Einträge</h2>';
// Texte für menutop
$this->content->template['message']['menutop']['neu_edit']['link_legend'] = 'Link-Auswahl';
$this->content->template['message']['menutop']['neu_edit']['link_select_legend'] = 'Welcher Link soll verwendet werden?';
$this->content->template['message']['menutop']['neu_edit']['link_select_selection'] = 'Link aus Auswahl-Liste.';
$this->content->template['message']['menutop']['neu_edit']['link_select_text'] = 'Link aus Eingabefeld.';
$this->content->template['message']['menutop']['neu_edit']['link_extern'] = 'Link in neuem Fenster öffnen.';


// Texte für Stammdaten-Datumsformat
$this->content->template['message']['stamm']['dateformat']['legend'] = 'Datumsformat';
$this->content->template['message']['stamm']['dateformat']['label_short'] = '.. kurzes Datum (Standard). Bsp.:';
$this->content->template['message']['stamm']['dateformat']['label_long'] = '.. langes Datum. Bsp.:';

$this->content->template['ueberschriften']='Überschriften';
$this->content->template['ueberschrift']='Überschrift';
$this->content->template['Auszeichnungen']='Auszeichnungen';
$this->content->template['abk_von']='Abkürzung von';
$this->content->template['acr_von']='Acronym von';
$this->content->template['zit_von']='Zitat von';
$this->content->template['message_2170']='Welche Überschrift? 1-6 sind möglich.';
$this->content->template['message_2171']='Zusätzliche logische Auszeichnungen';
$this->content->template['letzte_aenderung'] = 'Datum letzte Änderung: ';
$this->content->template['letzte_aenderung_von'] = ' - durchgeführt von: ';
$this->content->template['letzte_aenderung_autor'] = ' - Eigentümer:  ';
/**
errors
*/
// wrong Email adress
$this->content->template['error_1']="Diese E-Mail-Adresse ist leider nicht in Ordnung. Wahrscheinlich haben Sie sich vertippt.";