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
$this->content->template['head_inc']['title'] = 'Amministrazione del tuo sito';
//direkt zum Inhalt
$this->content->template['message_2120']="direttamente al contenuto";
//zur Bereichsnavigation
$this->content->template['message_2121']="alla navigazione locale";
// direkt zur Suche
$this->content->template['message_2122']=" direttamente alla ricerca ";
//direkt zum einloggen
$this->content->template['message_2123']="direttamente al login";
// Kein Ergebniss
$this->content->template['message_5']="Non è stato possibile trovare alcunchè. Si prega di affinare il termine di ricerca, in modo da fornire risposte più precise.";
$this->content->template['message_2099']="Nome Utente:  ";
// Emailadresse:
$this->content->template['message_2100']=" Indirizzo Email:";
//Passwort:
$this->content->template['message_2101']="Password:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Password (controllo):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']=" Vuoi ricevere una email, se qualcuno ha risposto sul tuo post nel forum? ";
//Antwortmail?
$this->content->template['message_2104']="Mail di risposta?";
// erstellen
$this->content->template['message_2105']=" creare";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Qui puoi modificare i tuoi dati ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Qui potete inserire i dati per il vostro account. ";
//Username:
$this->content->template['message_2108']="Nome Utente:";
// Emailadresse:
$this->content->template['message_2109']=" Indirizzo email:";
//Neues Passwort:
$this->content->template['message_2110']="Nuova password:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" Vuoi ricevere una email, se qualcuno ha risposto sul tuo post nel forum?";
// Antwortmail?
$this->content->template['message_2112']=" Email di risposta? ";
//bearbeiten
$this->content->template['message_2113']="edita ";
//Styleswitcher
$this->content->template['message_2139']="Cambio di Stile";
// User Sprach-Wahl
$this->content->template['message_2140'] = "Lingua";
$this->content->template['message_2141'] = "Qui è possibile impostare la lingua di default per questo utente dopo il suo accesso.";
$this->content->template['message_2142'] = "Frontend";
$this->content->template['message_2143'] = "Backend";


// weiter.html
/*
"<h1>Hier können Sie die Foren Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Foren bearbeiten/erstellen</strong> können Sie Ihre Foren verwalten, unter
            <strong>Nachrichten bearbeiten</strong> können Sie Nachrichten verändern und ergänzen oder ganze Threads löschen, aber keine einzelnen Nachrichten, nur ganze Threads..</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>";
*/
$this->content->template['message_13']="<h1> Gestione del tuo forum sul sito web </h1><br>
             <p> La voce del menu <strong>Edita forum</strong> è il link per la gestione del forum, con
             <strong>Edita messaggi</strong> è possibile modificare o eliminare i messaggi e sarai in grado di eliminare un intero thread.</ p>
             <p>Considerare la modifica dei riferimenti per correggere la formattazione.</ p>";
// Stimmen die Daten, wenn ja dann auf abschicken klicken!
$this->content->template['message_14']="<h2> Se i dati sono corretti, allora fare clic su inviare.</ h2>";
// Hier können die Forumdaten bearbeitet werden!
$this->content->template['message_15']="<h2>Qui tu puoi amministrare i forum.</h2>";
// Dieses Forum existiert schon
$this->content->template['message_16']="<h2 style=\"background-color:white;color:red;\">Questo forum non esiste, si prega di sceglierne un'altro.</h2>";
// <h3>Dieses Forum wurde gelöscht, bitte fahren Sie über das Menu fort</h3>
$this->content->template['message_17']="<h3>Questo forum è stato cancellato, si prega di continuare con il menu.</h3>";
/**<h1>Hier können Sie die Benutzer und Gruppen Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>neuer Benutzer/Gruppe</strong> können Sie einen neuen Benutzer eingeben, unter
            <strong>Benutzer/Gruppen bearbeiten</strong> können bestehende Benutzer/Gruppen verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/
$this->content->template['message_18']="<h1>Qui si possono gestire gli utenti ed i gruppi del tuo sito web.</h1><p>È possibile aggiungere nuovi utenti o gruppi e si possono modificare le proprietà degli utenti e dei gruppi. Si prega di seguire i consigli di formattazione per una corretta formattazione.</p>";
/*
<h1>Hier können Sie Dateien hochladen für die Benutzung innerhalb von papoo</h1>
            <p>Unter dem Menupunkt <strong>Dateien hochladen</strong> können Sie eine Datei hochladen, unter
            <strong>Dateien ändern</strong> können bestehende Daten zu Dateien verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/
$this->content->template['message_19']="<h1>Qui puoi caricare i file per uso interno in Papoo.</h1><p>Sotto la voce di menu <strong>caricare file</strong>, è possibile caricare un file, sotto <Strong>edita files</strong>, i file di dati esistenti possono essere modificati e integrati</p><p>E' necessario seguire le istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>";
//<strong style']="color:red">Etwas stimmt nicht, bitte überprüfen Sie Ihre Daten!</strong>')
$this->content->template['message_20']="<strong style=\"color:red\">C'è qualcosa di sbagliato, vi preghiamo di verificare i dati.</strong>";
// Die Daten wurden eingetragen. Bitte fahren Sie über das Menü links fort.
$this->content->template['message_21']="Ora i Dati sono stati registrati.";
// Diese Datei wurde gelöscht. Bitte fahren Sie über das Menü links fort.
$this->content->template['message_22']="Questo file è stato eliminato.";
// Die Datei konnte nicht gelöscht werden. Bitte manuell die folgende Datei löschen:
$this->content->template['message_23']="Il file potrebbe non essere stato cancellato, si prega di eliminare manualmente il file seguente:";
/*
<h1>Hier können Sie automatisch erstellte Abkürzungen und Sprachauszeichnungen Ihrer Homepage verwalten</h1>
                       <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/
$this->content->template['message_24']="<h1>Qui si possono amministrare abbreviazioni e contrassegni linguistici della tua homepage</h1><p>Si osservino le istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>";
// Kein Ergebnis
$this->content->template['message_25']="Non ci sono dati, che possano essere modificati.";
// Abkürzung löschen
$this->content->template['message_26']="Non ci sono dati, che possono essere modificati.";
// Auszeichnung löschen
$this->content->template['message_27']="Il contrassegno è stato eliminato.";
/**
<h1>Hier können Sie automatisch erstellte Links Ihrer Homepage verwalten</h1>
            <p>Unter dem Menupunkt <strong>Link eingeben</strong> können Sie einen Link eingeben, unter
            <strong>Links ändern</strong> können bestehende Links verändert und ergänzt werden.</p>
            <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>
*/$this->content->template['message_28']="<h1>Qui puoi amministrare i link (collegamenti) della tua home page</h1>
            <p>Sotto la voce di menu <strong>Nuovo Link</strong> puoi inserire un nuovo link; <br/>sotto <strong>Modifica Link</strong> i link esistenti possono essere modificati ed integrati</p>
            <p>Si osservino la istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>";
// Link löschen
$this->content->template['message_29']="Questo link (collegamento) è stato eliminato.";
// Menü verwalten
$this->content->template['message_30']="<h1>Qui si può gestire la struttura della tua Homepage</h1>
            <p>Sotto la voce di menu <strong>Creare menu</strong> puoi inserire una voce di menu, sotto
            <strong>Edita menu</strong> puoi modificare le voci di menu esistenti.</p>
            <p>Seguire la istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>";
//<strong>Der Menupunkt wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!
$this->content->template['message_31']="<strong>Questa voce di menu è ora registrata nel database.</strong>";
// Dieser Menüpunkt existiert schon
$this->content->template['message_32']="Questa voce di menu esiste già.";
// <strong>Eine der Eingaben stimmt nicht, bitte überprüfen!</strong>
$this->content->template['message_33']="<strong>C'è qualcosa di sbagliato, vi preghiamo di verificare i dati.</strong>";
// <strong>Der Menupunkt wurde gelöscht, bitte fahren Sie über das Menü fort.</strong>
$this->content->template['message_34']="<strong>La voce di menu è stata eliminata.</strong>";
/**
<h1>Hier können Sie die Bilder Ihrer Homepage verwalten</h1>
                <p>Bitte beachten Sie die Formatierungshinweise,
                da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>

*/
$this->content->template['message_35']="<h1>Qui si possono gestire le immagini e le foto della tua home page.</h1>
                <p>Seguire la istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>
				<p>Scegliere una voce del menu.</p>";
//<h2>Das Bild ist zu groß!</h2>  Bitte nochmal versuchen.
$this->content->template['message_36']="<h2>L'immagine è troppo grande! </ h2> Si prega di riprovare.";
// <h1>Eine Datei diesen Namens existiert schon</h1><p>Bitte benennen Sie die Datei um!</p>
$this->content->template['message_37']="<h1>Un file con questo nome esiste già. </ h1> <p>Rinominare il file prima di caricarlo!</p>";
//<h1>Das geht leider nicht</h1><p>Sie haben kein Bild ausgewählt, oder es handelt sich um kein unterstütztes Format.</p>
$this->content->template['message_38']="<h1>Qualcosa non funziona! </ h1> <p> Questa non è una immagine o è in un formato non supportato.</p>";

$this->content->template['message_image_change_imagefile_legend'] = "Change image-file";
$this->content->template['message_image_change_imagefile_text'] = 
"Replace the existing image-file with a new one.
Only the file on the Web-Server will be replaced. All images-inplacements in articles etc. will be left untouched.
<strong>Please pay attention that the pixel-size of the new image is the same than the existing one.</strong>";
$this->content->template['message_image_change_imagefile_label'] = "New image-file";

$this->content->template['message_39']="<h1>I dati sono stati inseriti.</ h1><p>Usa il menu per andare avanti.</p>";
/*<h1>Hier können Sie die Artikel in der 3. Spalte Ihrer Homepage verwalten</h1>
                        <p>Unter dem Menupunkt <strong>neuer Artikel</strong>
                        können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können
                        bestehende Artikel verändert und ergänzt werden,
                        Bilder oder Links eingefügt werden.</p>
                        <p>Bitte beachten Sie die Formatierungshinweise, da ansonsten
                        keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/

$this->content->template['message_40']="<h1>Qui è possibile amministrare la 3a Colonna del tuo sito web.</h1>
<p>Sotto la voce di menu <strong>Nuovo Articolo</strong> puoi inserire un nuovo articolo, sotto <strong>Cambia Articolo</strong> puoi modificare gli articoli esistenti ed aggiungere links ed immagini.</p><p>Si osservino la istruzioni di formattazione, altrimenti non può essere garantita un'accurata rappresentazione.</p>
                        <p>La 3a Colonna è in realtà non una colonna, ma un blocco di informazioni che è possibile posizionare dove si desidera.</p>";

/*<h1>Hier können Sie die Artikel auf der Homepage verwalten</h1><p>Unter dem Menupunkt <strong>neuer Artikel</strong> können Sie einen neuen Artikel eingeben, unter <strong>Artikel ändern</strong> können bestehende Artikel verändert und ergänzt werden, Bilder oder Links eingefügt werden.</p><p>Bitte beachten Sie die Formatierungshinweise, da ansonsten keine fehlerfreie Darstellung gewährleistet werden kann.</p>*/
$this->content->template['message_41']="<h1>Qui si possono gestire gli articoli dell'homepage</h1><p>Sottola voce di menu <strong>Nuovo Articolo</strong> puoi inserire un nuovo articolo, sotto <strong>Edita Articolo</strong> gli articoli esistenti possono essere modificati/integrati e si possono aggiungere immagini e links</p><p>Si prega di fare attenzione alle istruzioni di formattazione, altrimenti non può essere garantita una perfetta presentazione.</p>";
/*<h2>Artikel wurde eingetragen</h2><p>Klicken Sie hier um einen weiteren Artikel in die Datenbank einzutragen: <a href']="./artikel.php?menuid=10&obermenuid=5&untermenuid=10&freimachen=1" title \"Neuer Eintrag\">Neuen Eintrag erstellen</a></p>*/
$this->content->template['message_42']="<h2>L'articolo è stato inviato ed inserito nel database.</h2>";
// "<h2>Ihr Teaser wird so dargestellt werden:</h2>"
$this->content->template['message_43']="<h2>L'occhiello apparirà così:</h2>";
/*
<hr /><h2>Ihr Artikel wird so (oder ähnlich) dargestellt werden</h2><p>Für Änderungen oder die endgültige Eintragung in die Datenbank bitte unten klicken.</p><hr />
*/
$this->content->template['message_44']="<hr /><h2>L'articolo apparirà più o meno così:</h2><p>Per rendere definitive le modifiche cliccate qui sotto</p><hr />";
// Nicht genug Daten eingegeben im Textfeld, bitte ergänzen Sie!
$this->content->template['message_45']="I dati inseriti nella casella di testo sono insuffcienti, si prega di aggiungerne altri!";
/* <h2>Artikel bearbeiten</h2><p>Um einen Artikel zu bearbeiten müssen Sie ihn hier suchen und anschließend im Suchergebnis auf den Link <strong>(Name Ihres Artikels) ändern</strong> klicken</p>
*/
$this->content->template['message_46']="<h2>Gestione Articolo</h2><p>Um einen Artikel zu bearbeiten müssen Sie ihn hier suchen und anschließend im Suchergebnis auf den Link <strong>(Name Ihres Artikels) ändern</strong> klicken</p>";
// ändern
$this->content->template['message_47']="( modifica )";
// '<strong>Dieser Artikel wurde gelöscht, bitte fahren Sie über das Menü fort!</strong>
$this->content->template['message_2']="<strong>Questo articolo è stato eliminato, usare il menu per proseguire!</strong>";
//<strong>Bitte überprüfen Sie Ihre Eingaben, etwas fehlt!</strong>
$this->content->template['message_48']="<strong style=\"color:red;\">Verifica i dati immessi, c'è qualcosa che non va</strong>";
//Diese Gruppe existiert schon!
$this->content->template['message_49']="<strong style=\"color:red;\">Questo gruppo è già esistente!</strong>";
//Die Gruppe wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!<br>
$this->content->template['message_50']="Il Gruppo è stato inserito nel database, per continuare usare il menu sulla sinistra<br>";
// Dieser Benutzer existiert schon!
$this->content->template['message_51']="Questo Utente è già esistente!";
//<strong>Der Benutzer wurde in die Datenbank eingetragen, bitte fahren Sie über das Menü links fort!</strong><br>
$this->content->template['message_52']="<strong>L'Utente è stato inserito nel database, per continuare usare il menu sulla sinistra!</strong><br>";
//<h3>Dieser Benutzer wurde gelöscht, bitte fahren Sie über das Menu fort.</h3>
$this->content->template['message_53']="<h3>Questo utente è stato cancellato, per continuare usare il menu sulla sinistra.</h3>";
//<h3>root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_54']="<h3>la root non può essere eliminata, per continuare usare il menu sulla sinistra!</h3>";
//<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_55']="<h3>nessuno può essere eliminato, per continuare usare il menu sulla sinistra!</h3>";
//<h1>Diese Gruppe exisitiert nicht</h1>
$this->content->template['message_56']="<h1>Questo Gruppo non esiste</h1>";
// <h3>Diese Gruppe wurde gelöscht, bitte fahren Sie über das Menu fort</h3>
$this->content->template['message_57']="<h3>Questo Gruppo è stato cancellato, per continuare usare il menu sulla sinistra</h3>";
//<h3>papoo_root kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_58']="<h3>papoo_root non può essere eliminata, per continuare usare il menu sulla sinistra!</h3>";
//<h3>jeder kann nicht gelöscht werden, bitte fahren Sie über das Menu fort!</h3>
$this->content->template['message_59']="<h3>nessuno può essere eliminato, per continuare usare il menu sulla sinistra!</h3>";

/**
Alle Daten im Template
*/
// Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_60']="<h2>Controlla i dati, grazie!</h2>";
// Benutzername
$this->content->template['message_61']="Nome Utente";
// Passwort
$this->content->template['message_62']="Password";
// Internes Menü zur Verwaltung Ihrer Internetseite
$this->content->template['message_63']="Menu interno per gestire il tuo sito internet";
/* <p>Sie können hier je nach Gruppenzugehörigkeit Artikel eingeben oder sämtliche Daten Ihrer Seite verwalten.</p>
<p>Klicken Sie die Menüpunkte in der Menüleiste links an und erfahren Sie dort alles Weitere...</p>*/
$this->content->template['message_64']="<p>È possibile gestire, a seconda del gruppo di appartenenza, gli articoli o tutti i datidel tuo sito</p>
<p>Fare clic sulla voce di menu nella barra dei menu a sinistra, per vedere il resto...</p>";
// Empfänger der Nachricht
$this->content->template['message_65']="Destinatario.";
// Überschrift
$this->content->template['message_66']="Header (intestazione)";
// Hier bitte Ihre Überschrift eingeben
$this->content->template['message_67']="Qui puoi editare il tuo header";
// Texteingabe
$this->content->template['message_68']="Scrivi il Testo";
// Übermitteln
$this->content->template['message_69']="Invio Dati";
// Eintragen
$this->content->template['message_70']="Invia";
// Artikel, die Sie geschrieben und veröffentlicht haben.
$this->content->template['message_71']="Articoli che avete scritto e pubblicato.";
//Sie können die Artikel erneut bearbeiten und veröffentlichen, wenn Sie auf den entsprechenden Link klicken:
$this->content->template['message_72']="Puoi modificare questi articoli, se fai clic sul link (collegamento) relativo ad ogni articolo:";
//weitere Seiten
$this->content->template['message_73']="una pagina indietro";
//Artikel die zu veröffentlichen sind
$this->content->template['message_74']="Articoli che devono essere pubblicati";
//Sie können die Artikel bearbeiten und veröffentlichen, wenn Sie auf den Link klicken:
$this->content->template['message_75']="È possibile modificare e pubblicare l'articolo, se si fa clic sul relativo link:";
//Ihr persönlichen Daten.
$this->content->template['message_76']="I tuoi dati personali";
//Sie haben
$this->content->template['message_77']="Essi hanno";
//neue Mitteilungen von anderen Mitgliedern.
$this->content->template['message_78']="nuovi messaggi dagli altri utenti.";
//Artikel stehen zur Veröffentlichung an
$this->content->template['message_79']="Articoli sono disponibili per la pubblicazione";
//Sie haben bereits
$this->content->template['message_80']="Essi hanno già";
//Artikel veröffentlicht
$this->content->template['message_81']="Articoli pubblicati ";
//Hier können Sie die Daten Ihres Accounts bearbeiten
$this->content->template['message_82']="Qui puoi modificare i dati del tuo profilo";
//Email Addresse
$this->content->template['message_83']="Indirizzo email";
//Sie haben keine Berechtigung.
$this->content->template['message_84']="Tu non hai permessi.";
//Erneut versuchen
$this->content->template['message_85']="Prova di nuovo";
//Seite weiter
//weitere Seiten
$this->content->template['message_86']="una pagina avanti.";
// Name des Forums
$this->content->template['message_87']="Nome del Forum ";
//Forumname
$this->content->template['message_88']="Nome del Forum";
//Beschreibung
$this->content->template['message_89']="Descrizione";
//Beschreibung des Forums, max. 200 Zeichen
$this->content->template['message_90']="Dettagli del Forum, max 200 caratteri";
// Soll das Forum im Internet oder im Intranet stehen
$this->content->template['message_91']="Forum per Internet o Intranet? ";
//Intranet
$this->content->template['message_92']="Intranet";
//Internet
$this->content->template['message_93']="Internet";
//Geben Sie hier an, welche Gruppen das Forum lesen dürfen
$this->content->template['message_94']="Diritti di lettura";
//Gruppe
$this->content->template['message_95']="Gruppo";
//Geben Sie hier an, welche Gruppen in das Forum schreiben dürfen.
$this->content->template['message_96']="Diritti di scrittura";
//Die letzten 10 Einträge
$this->content->template['message_97']="Gli ultimi 10 messaggi";
//Suche nach einer Message
$this->content->template['message_98']="Cerca un Messaggio";
//Hier können die Messagedaten bearbeitet werden.
$this->content->template['message_99']="Qui puoi editare i messaggi";
//Messagedaten bearbeiten
$this->content->template['message_100']="Edita i dati del messaggio";
//Betreff
$this->content->template['message_101']="Oggetto";
// Diese Message löschen
$this->content->template['message_102']="Elimina questo messaggio ";
//<h3>Um diese Message zu löschen muss der <strong>gesamte</strong> Thread gelöscht werden.</h3><p>Wollen Sie tatsächlich den gesamten thread löschen??</p>
$this->content->template['message_103']="<h3>Per cancellare questo messaggio, devi cancellare l'<strong>intero</strong> Thread.</h3><p>Volete veramente eliminare l'intero thread?</p>";
//Forum löschen
$this->content->template['message_104']="Forum eliminato";
//Dieses Forum löschen?
$this->content->template['message_105']="Cancellare questo forum?";
//Löschen
$this->content->template['message_106']="Rimuovi";
//Message
$this->content->template['message_107']="Messaggio";
//  Diese Gruppe löschen??
$this->content->template['message_108']=" cancellare questo gruppo?";
//Gruppenname
$this->content->template['message_109']="Nome del Gruppo";
//Verfügbare Gruppen
$this->content->template['message_110']="Gruppi disponibili";
//Um die Eigenschaften der Gruppen zu ändern einfach darauf klicken
$this->content->template['message_111']="Per cambiare le caratteristiche dei gruppi basta cliccare ";
//Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum
$this->content->template['message_112']="Questa tabella elenca tutti gli utenti del CMS, compresa l'appartenenza ad un gruppo, data e numero di post nel forum";
//Neue Gruppe anlegen
$this->content->template['message_114']="Gestione dati del Gruppo";
//Gruppenname und Gruppenleiter angeben
$this->content->template['message_115']="Nome del Gruppo e leader del Gruppo";
//Gruppenname
$this->content->template['message_116']="Nome del Gruppo";
//Gruppenleiter
$this->content->template['message_117']="Leader del Gruppo";
//Dürfen Gruppenmitglieder auf das Intranet (wenn vorhanden) zugreifen?
$this->content->template['message_118']="Possono i membri del Gruppo accedere alla rete Intranet (se esiste)?";
//Zugriff aufs Intranet?
$this->content->template['message_119']="Accesso a Intranet?";
//Dürfen Benutzer dieser Gruppe Artikel für das Internet oder Intranet veröffentlichen?
$this->content->template['message_120']="Possono gli utenti di questo Gruppo pubblicare articoli su Internet o Intranet? ";
//Internet ja?
$this->content->template['message_121']="Internet, si?";
//Intranet ja?:
$this->content->template['message_122']="Intranet, si?";
//Wenn hier kein Häkchen gesetzt wird, wird der Artikel automatisch zur Freigabe an die Elterngruppe weitergegeben
$this->content->template['message_123']="Se non impostato alcun segno di spunta, l'articolo verrà rispedito automaticamente al gruppo d'origine.";
//Dürfen Benutzer dieser Gruppe auf die Administration zugreifen?:
$this->content->template['message_124']="Possono gli utenti di questo gruppo accedere all'area di amministrazione?";
//Zugriff auf die Administration
$this->content->template['message_125']="Accesso all'amministrazione";
//Welcher der einzelnen Menüpunkte im Backend für welche Gruppen freigegeben wird, wird in den Stammdaten eingestellt.
$this->content->template['message_126']="Se è permesso l'accesso alle voci di menu del backend allora i dati saranno registrati nel database.";
//Einfügen in die Hierarchie und Beschreibung:
$this->content->template['message_127']="Gerarchia e Descrizione:";
//Untergruppe von
$this->content->template['message_128']="sottogruppo di";
//Hier bitte die Beschreibung der Gruppe eingeben
$this->content->template['message_129']="Descrizione del Gruppo";
// Gruppe anlegen
$this->content->template['message_130']=" Crea Gruppo ";
//Neuen Benutzer anlegen
$this->content->template['message_131']="Crea nuovo Utente";
//Gruppenname und Password angeben
$this->content->template['message_132']="Indicare nome e password";
//Welcher Gruppe gehört der Benutzer an?
$this->content->template['message_133']="A quale Gruppo appartiene l'utente? ";
//Damit werden Lese- und Schreibrechte, genauso wie der Zugriff auf den Admin-Bereich geregelt.
$this->content->template['message_134']="Permesso di lettura e scrittura ed accesso all'area amministrazione";
//Weitere Daten
$this->content->template['message_135']="Maggiori dati";
//Hier bitte die Beschreibung der Gruppe eingeben
$this->content->template['message_136']="Inserire descrizione del gruppo";
//Benutzer eintragen
$this->content->template['message_137']="Registra Utente";
//Alle Benutzer
$this->content->template['message_138']="Tutti gli Utenti";
//Suche nach einem Benutzer
$this->content->template['message_139']="Cerca un utente";
//Anzahl der Benutzer
$this->content->template['message_140']="Numero di utenti";
//Um die Eigenschaften der Benutzer zu ändern einfach darauf klicken
$this->content->template['message_141']="Per modificare le caratteristiche di un utente basta cliccarci";
//Es wurde leider kein Benutzer im System gefunden, bitte versuchen Sie ein anderes Suchwort.
$this->content->template['message_142']="Spiacente, utente non presente nel sistema, prova di nuovo.";
//Diese Tabelle listet alle Benutzer des CMS auf, inkl. Gruppenzugehörigkeit, Eintrittsdatum und Anzahl der Beiträge im Forum
$this->content->template['message_143']="Questa tabella elenca tutti gli utenti del CMS, compresa l'appartenenza ad un gruppo, data e numero di post nel forum";
//Gruppenzugehörikeit
$this->content->template['message_144']="Gruppo di appartenenza";
//Eintrittsdatum
$this->content->template['message_145']="Data di registrazione";
//Anzahl der Beiträge
$this->content->template['message_146']="Numero di post ";
//Diesen Benutzer löschen??
$this->content->template['message_147']="Eliminare questo utente?";
//Benutzer bearbeiten
$this->content->template['message_148']="Gestione Utente";
//Welcher Gruppe gehört der Benutzer an?
$this->content->template['message_149']="A quale gruppo appartiene l'utente?";
//Suche nach einem Artikel
$this->content->template['message_150']="Cerca un Articolo";
//Die gesuchten Artikel
$this->content->template['message_151']="Articoli editabili";
//Sie können die Artikel erneut bearbeiten und veröffentlichen wenn Sie auf den entsprechenden Link klicken:
$this->content->template['message_152']="È possibile rieditare e pubblicare gli articoli facendo clic sul link appropriato:";
//Eingabe/Bearbeitung eines Artikels:
$this->content->template['message_153']="Edita/Crea articolo:";
//Hier bitte Ihre Überschrift eingeben
$this->content->template['message_154']="Inserisci un header/intestazione, grazie";
//Teaser/Anreisser
$this->content->template['message_155']="Occhiello ";
//Der Teaser oder auch Anreisser wird auf der Startseite oder auch auf den Unterseiten zusammen mit der Überschrift angezeigt.
$this->content->template['message_156']="L'occhiello verrà visualizzato sulla Home Page insieme con l'intestazione o sulle pagine secondarie con il titolo.";
//Bild zum Teaser
$this->content->template['message_157']="Foto per l'occhiello";
//Teaser-Bild auswählen (klein)
$this->content->template['message_158']="Seleziona una piccola foto per l'occhiello";
//Hier kann ein Bild ausgewählt werden.
$this->content->template['message_159']="Seleziona una foto.";
//auswählen
$this->content->template['message_160']="seleziona";
//Bild links anzeigen?
$this->content->template['message_161']="Foto sulla sinistra?";
//Bild rechts anzeigen?
$this->content->template['message_162']="Foto sulla destra?";
//Teaser Link
$this->content->template['message_163']="Link Occhiello";
//mehr über
$this->content->template['message_164']="più notizie su";
//Hier bitte den Link zum Artikel eingeben
$this->content->template['message_165']="Inserire il link per questo articolo, grazie.";
//Eingabe und Formatierung der Inhalte
$this->content->template['message_166']="Modifica e formatta il contenuto";
// Menüpunkt auswählen.
$this->content->template['message_167']=" Seleziona la voce di menu.";
//Menupunkt
$this->content->template['message_168']="Voce di Menu";
//Menü für Internet
$this->content->template['message_169']="Menü per Internet";
//Menü für Intranet
$this->content->template['message_170']="Menü per Intranet ";
// Den Artikel veröffentlichen?
$this->content->template['message_171']=" Pubblicare questo articolo?";
//Die <strong>endgültige</strong> Freigabe erfolgt durch Ihren Chefredakteur, bitte das Häkchen trotzdem setzen wenn Sie das Dokument veröffentlichen wollen.
$this->content->template['message_172']="Metti la spunta se vuoi pubblicare il documento; la release <strong>finale</strong> sarà curata dai superiori, che provvederanno alla pubblicazione.";
//Downloads zählen.
$this->content->template['message_174']="Monitorare i downloads.";
//Geben Sie hier an, ob die eventuell vorhandenen Downloads gezählt werden sollen.
$this->content->template['message_175']="Metti la spunta, se vuoi monitorare i downloads effettuati.";
//Zählen
$this->content->template['message_176']="Monitora";
//Soll der Artikel auf der Startseite gelistet werden?
$this->content->template['message_177']="Elenca questo articolo sulla home page?";
//Auf der Startseite listen?
$this->content->template['message_178']="Metti nella lista della home page questo articolo?";
//Zugriff für andere.
$this->content->template['message_179']="Autorizzazione per gli altri";
// Dürfen andere Benutzer auf diesen Artikel schreibend zugreifen?
$this->content->template['message_180']=" Vuoi permettere la modifica dell'articolo ad altre persone? ";
// Freigabe für andere?
$this->content->template['message_181']=" scambiare con altri? ";
//Daten löschen?
$this->content->template['message_182']="Eliminare i dati? ";
//Sie dürfen diesen Artikel nicht bearbeiten, nur anschauen.
$this->content->template['message_183']="Non puoi modificare questo articolo ma solo leggerlo.";
//Diesen Artikel löschen??
$this->content->template['message_184']="Cancellare questo articolo?";
//Daten eintragen?
$this->content->template['message_185']="Inserire i dati?";
//Neue Vorschau
$this->content->template['message_186']="Nuova anteprima";
//Sie haben keine Rechte Artikel für das
$this->content->template['message_187']="Non puoi pubblicare questo articolo su Internet ";
//zu veröffentlichen.
$this->content->template['message_188']="per pubblicare.";
// Das Formular übermitteln.
$this->content->template['message_190']=" Invia il modulo.";
//Damit wird erst die Vorschau aktiviert
$this->content->template['message_191']="Questa azione permette l'anteprima";
// Die CSS Datei wurde eingetragen
$this->content->template['message_192']=" il file CSS è stato aggiunto";
// Das Ergebnis können Sie hier überprüfen..
$this->content->template['message_193']=" Qui si possono revisionare i risultati.";
// Hier können Sie die CSS Datei Ihrer Seite bearbeiten
$this->content->template['message_194']=" Qui puoi editare il file CSS";
// Damit Ihre Änderungen auch in die Datei geschrieben werden können, muss diese die Dateirechte 646 haben.
$this->content->template['message_195']=" Per garantire che le modifiche al file possano essere effettuate, il file deve avere diritti 646.";
// Eingabe/Bearbeitung der CSS-Datei:
$this->content->template['message_196']=" Crea/edita il file CSS:";
//CSS-Datei:
$this->content->template['message_197']="CSS file:";
//Ihr Bild wurde hochgeladen
$this->content->template['message_198']="La tua immagine è stata salvata.";
//Diese Daten wurden hochgeladen
$this->content->template['message_199']="Dati salvati";
//Bitte geben Sie hier die nötigen Daten für das Bild ein
$this->content->template['message_203']="Inserisci i dati necessari per questa immagine";
// Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt!
$this->content->template['message_204']=" Testo Alternativo e Titolo sono obbligatori (altrimenti nessun dato verrà inserito nel database)!";
//Alternativtext
$this->content->template['message_205']="Testo Alternativo";
//Wenn kein Bild angezeigt werden kann
$this->content->template['message_206']="Se l'immagine non può essere visualizzata";
// kurze Beschreibung
$this->content->template['message_207']=" Breve descrizione ";
//Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)
$this->content->template['message_208']="Descrizione estesa (Cosa succede nella foto, si prega di indicare in modo preciso la descrizione ...)";
// Laden Sie bitte das Bild hoch. Es wird zusätzlich ein Thumbnail erzeugt.
$this->content->template['message_209']=" Si prega di caricare un'immagine. Una miniatura sarà automaticamente generata.";
// Maximale Dateigröße ist 100 kbyte und 800x800px!
$this->content->template['message_210']=" Dimensione max del file 100 kbyte e formato max 800x800 px !";
//Unterstützte Formate: jpeg, jpg, pjpeg
$this->content->template['message_211']="Formati ammessi: JPG, GIF, PNG, SVG";
//Das Bild
$this->content->template['message_212']="Immagine";
//abschicken
$this->content->template['message_213']="Upload";
//Dieses Bild wird bearbeitet
$this->content->template['message_214']="Modifica Immagine";
// Dieses Bild verkleinern.
$this->content->template['message_215']=" Ridimensiona Immagine.";
// Verkleinern in Prozent (z.B. 80 für 80%)
$this->content->template['message_216']=" Cambia le dimensioni percentualmente (ad es. 80 per 80%)";
//verkleinern
$this->content->template['message_217']="ridimensiona";
// Bitte geben Sie hier die nötigen Daten für das Bild ein
$this->content->template['message_218']=" Inserire i dati necessari per l'immagine.";
// Alternativtext und Titel müssen unbedingt angegeben werden, da ansonsten kein Eintrag in die Datenbank erfolgt!
$this->content->template['message_219']="Testo alternativo e titolo devono essere necessariamente inseriti, altrimenti non saranno inseritiii dati nel database! ";
// Alternativtext (Wenn kein Bild angezeigt werden kann)
$this->content->template['message_220']=" testo alternativo (se nessuna immagine può essere visualizzata) ";
// Titel (kurze Beschreibung):
$this->content->template['message_221']=" Titolo (breve descrizione):";
// Beschreibung (Was passiert auf dem Bild, bitte in genauen Beschreibungen angeben ...)
$this->content->template['message_222']=" descrizione estesa (Cosa succede nella foto, si prega di indicare in modo preciso la descrizione ...)";
// Dieses Bild löschen?-
$this->content->template['message_223']=" Eliminare questa immagine?";
//In die Datenbank eintragen
$this->content->template['message_224']="Inserisci nel database";
//Sie haben die Informationen dieses Bilds verändert
$this->content->template['message_225']="Hai cambiato le informazioni per questa immagine";
//Dieses Bild wird in diesem(n) Artikel(n) verwendet.
$this->content->template['message_226']="Questa immagine è usata nel(i) seguente(i) articolo(i) ";
// Möchten Sie die Änderungen
$this->content->template['message_227']=" Vuoi salvare le modifiche ";
// im Original speichern?
$this->content->template['message_228']=" nell'immagine Originale?";
//oder möchten Sie dieses Bild lieber
$this->content->template['message_229']="o vuoi salvare l'immagine,";
//unter dem Namen:
$this->content->template['message_230']="con il nome: ";
//als Kopie speichern?
$this->content->template['message_231']="come una copia?";
// Suche nach einem Bild:
$this->content->template['message_232']=" cerca un'immagine:";
//Finden
$this->content->template['message_233']="Trova";
//Eingabe neuer Linkdaten
$this->content->template['message_234']="Inserisci un nuovo link";
//Hier können Sie neue Ersetzungsdaten für Links in die Datenbank eingeben.
$this->content->template['message_235']="Qui potete inserire nuovi dati per la sostituzione automatica di testo con link.";
//Es sind nur http://www. Adressen erlaubt.
$this->content->template['message_236']="Solo indirizzi 'http://www.'  sono consentiti.";
//Eingabe der Linkdaten
$this->content->template['message_237']="Dati del link";
//Ersetzungstext
$this->content->template['message_238']="Testo sostituitivo";
//Link
$this->content->template['message_239']="Link";
//Titel des Links
$this->content->template['message_240']="Titolo del link";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist der Link falsch eingegeben.
$this->content->template['message_242']="Qualcosa è sbagliato, si prega verificare i dati, probabilmente il link è errato.";
//Änderung der Linkdaten:
$this->content->template['message_243']="Modifica dati dei link:";
//Suche nach einem Link-Eintrag:
$this->content->template['message_244']="Cerca un link:";
//Hier können Sie die bereits in der Datenbank vorhandenen Links ändern
$this->content->template['message_245']="Qui è possibile effettuare modifiche ai link (collegamenti) esistenti.";
//Ändern Sie den folgenden Link. Es sind nur http://www. Adressen erlaubt.
$this->content->template['message_246']="Modifica il seguente link. Solo indirizzi 'http://www.'  sono consentiti.";
//Entfernen
$this->content->template['message_247']="Elimina";
//Diesen Menupunkt löschen??
$this->content->template['message_248']="Eliminare questa voce di menu?";
//Wenn Sie den Löschen Button drücken, wird der Menupunkt unwiderruflich gelöscht und alle Artikel die unter diesem Menüpunkt erreichbar waren werden nicht mehr erreichbar sein. Sie können diese über den Artikel Menüpunkt anderen Menüpunkten zuweisen.
$this->content->template['message_249']="Premendo il pulsante Elimina, il menu sarà eliminato in modo permanente: tutti gli articoli accessibili e coperti da questa voce di menu, non saranno più raggiungibili. È possibile utilizzare questo articolo assegandolo a diversa voce del menu.";
//Menupunkt Name:
$this->content->template['message_250']="Nome della voce di menu:";
//Menu formtitel:
$this->content->template['message_251']="Titolo del Menu:";
//Untermenu zu:
$this->content->template['message_252']="Sottomenu di: ";
//Eintrag löschen!!!
$this->content->template['message_253']="Elimina dati!";
//Hier sind alle verfügbaren Menüpunkte
$this->content->template['message_254']="Tutte le voci di menu disponibili";
//Diesen Menupunkt bearbeiten.
$this->content->template['message_255']="Modifica voce di menu.";
//Name
$this->content->template['message_257']="Nome";
//formtitel
$this->content->template['message_258']="Titolo";
//formtitel
$this->content->template['message_258b']="URL riconiscimento vocale";
//Internet oder Intranet?
$this->content->template['message_259']="Internet o Intranet?";
//Wenn der Menupunkt ein normaler Punkt der 1. Ordnung ist, dann ist er ein Unterpunkt zur Startseite
$this->content->template['message_260']="Quando la voce di menu è ordinata come voce di primo livello, allora è una sottovoce della homepage.";
//Menü für Internet
$this->content->template['message_261']="Menu per Internet";
//Menü für Intranet
$this->content->template['message_262']="Menu per Intranet";
//Zugriff für andere.
$this->content->template['message_263']="A chi è consentito pubblicare in questo menu:";
//Wenn auf eine besondere Seite verwiesen werden soll:
$this->content->template['message_264']="Integrazione di link o file.";
//Einen neuen Menupunkt erstellen.
$this->content->template['message_265']="Crea un nuova voce di menu.";
//formlink
$this->content->template['message_266']="Pagina del Link (default: index.php)";
//formlink
$this->content->template['message_266a']="Aiuto e maggiori info.";
//Suche nach einem Eintrag:
$this->content->template['message_267']="Cerca una voce:";
// Eingabe/Bearbeitung eines Artikels:
$this->content->template['message_268']=" Crea/modifica un Articolo:";
// Neue Vorschau
$this->content->template['message_269']=" nuova anteprima ";
//Eingabe/Bearbeitung eines Eintrags:
$this->content->template['message_270']="Crea/Modifica una voce:";
//Diesen Artikel löschen??
$this->content->template['message_271']="Eliminare questo articolo?";


//Eingabe neuer Abkürzungsdaten
$this->content->template['message_272']="Nuova Abbreviazione ";
//<p>Hier können Sie neue Ersetzungsdaten für Abkürzungen in die Datenbank eingeben.</p><p>Unter Abkürzungen fallen auch die Akronyme.</p><p>Die Abkürzung muss mindestens 3 Zeichen lang sein.</p>
$this->content->template['message_273']="<p>Qui puoi inserire nuovi dati per abbreviazioni nel database.</p>
<p>Acronimi sono anche abbreviazioni.</p>
<p>Minimo 3 lettere.</p>";
//Eingabe der Abkürzungsdaten:
$this->content->template['message_274']="Dati per le abbreviazioni:";
//Abkürzung
$this->content->template['message_275']="Abbreviazione";
// Bedeutung der Abkürzung:
$this->content->template['message_276']=" Significato della abbreviazione:";
//Eingabe neuer Abkürzungsdaten
$this->content->template['message_277']="Inserisci nuovi dati per abbreviazione";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Abkürzung zu kurz (mindestens 3 Buchstaben).
$this->content->template['message_278']="Qualcosa non va, controlla i dati, grazie. (probabilmente la lunghezza della sigla è troppo breve (almeno 3 lettere)).";
//Änderung der Abkürzungsdaten:
$this->content->template['message_279']="Edita l'abbreviazione:";
//Suche nach einem Abkürzungs-Eintrag:
$this->content->template['message_280']="Cerca una abbreviazioneg:";
//Änderung der Abkürzungen
$this->content->template['message_281']="Edita l'abbreviazione";

//Eingabe neuer Sprachauszeichnungen
$this->content->template['message_282']="Nuovi dati per i tag di lettura automatica";
//<p>Hier können Sie neue Ersetzungsdaten für Sprachauszeichnung in die Datenbank eingeben.</p><p>Bitte nur Sprachauszeichnung aus der englischen Sprache auszeichnen.</p><p>Die Sprachauszeichnung muß mindestens 3 Zeichen lang sein.</p>
$this->content->template['message_283']="<p>Qui puoi creare nuovi dati sostitutivi per i tag di lettura automatica.</p>
<p>Usar solo parole diverse dalla lingua inglese.</p>
<p>I tag di lettura automatica devono essere lunghi almeno 3 caratteri.</p>";
//Eingabe der Sprachauszeichnung:
$this->content->template['message_284']="Dati per i tag di lettura automatica:";
//Sprachauszeichnung
$this->content->template['message_285']="Tag di lettura automatica";
//Eingabe neuer Sprachauszeichnung
$this->content->template['message_286']="Immissione di tag di lettura automatica";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten, vermutlich ist die Länge der Sprachauszeichnung zu kurz (mindestens 3 Buchstaben).
$this->content->template['message_287']="Qualcosa non va, controlla i dati, grazie. (probabilmente la lunghezza dei tag linguisitici è troppo breve (almeno 3 lettere)).";
//Änderung der Sprachauszeichnung:
$this->content->template['message_288']="Edita il tag:";
//Sprachauszeichnung
$this->content->template['message_289']="Tag di lettura automatica";
//Suche nach einem Sprachauszeichnung-Eintrag:
$this->content->template['message_290']="Cerca un tag od abbreviazione:";
//Überprüfung der Sprachauszeichnung
$this->content->template['message_291']="Controlla il tag";
//Hier können Sie die Stammdaten Ihrer Seite bearbeiten
$this->content->template['message_292']="Qui puoi modificare i dati principali del tuo sito";
//Sprache/Language Backend:
$this->content->template['message_293']="Lingua/Language Backend:";
//auswählen/select
$this->content->template['message_294']="seleziona/select";
//Sprache/Language Frontend:
$this->content->template['message_295']="Lingua/Language Frontend:";
//Seitenname und Admin Email:
$this->content->template['message_296']="Nome del sito - Email amministratore:";
//Seitenname
$this->content->template['message_297']="url del sito (senza http:// - ad es. www.tin.it)";
//Hier bitte den Seitennamen eingeben
$this->content->template['message_298']="Inserire nome del sito";
//Administrator - E-Mail:
$this->content->template['message_299']="E-Mail dell'amministratore:";
//Überschrift ganz oben, oberhalb des Textes.
$this->content->template['message_300']="Intestazione sopra il testo normale, dentro al logo";
//Kopf-Titel :
$this->content->template['message_301']="Titolo Intestazione:";
//Meta-Daten Ihrer Seite
$this->content->template['message_302']="Meta-Dati del sito";
//Beschreibung
$this->content->template['message_303']="Descrizione";
//Stichwörter
$this->content->template['message_304']="Parole chiave";
//Autor offiziell:
$this->content->template['message_305']="Autore ufficiale:";
//Die Einstellungen für das Internet
$this->content->template['message_306']="Impostazioni avanzate.";
//Möchten Sie die rechte Spalte?
$this->content->template['message_307']="Inserire la colonna destra?";
//Soll man sich einloggen können?
$this->content->template['message_308']="Consentire login da parte di tutti?";
//Möchten Sie einen Styleswitcher?
$this->content->template['message_309']="Permettere il cambio di Stile?";
//Existiert ein Intranet?
$this->content->template['message_310']="Esiste una rete Intranet?";
//Die Einstellungen für das Intranet
$this->content->template['message_311']="Impostazioni per Intranet";
//Möchten Sie die rechte Spalte?:
$this->content->template['message_312']="Vuoi la colonna destra?";
//Soll man sich einloggen können?:
$this->content->template['message_313']="Permettere il login?";
//Möchten Sie einen Styleswitcher?:
$this->content->template['message_314']="Vuoi permettere il cambio Stile?";
//Welcher Editor soll für die Eingabe benutzt werden?
$this->content->template['message_315']="Quale Editor dovrà essere usato di default?";
//Echter WYSIWYG Editor (standard)?:
$this->content->template['message_316']="HTMLArea?";
//bbCode Editor?:
$this->content->template['message_317']="Editor bbCode?";
//Fremdeditor?:
$this->content->template['message_318']="Altro editor?";
//Kopftext?
$this->content->template['message_319']="Testo nell'intestazione?";
//Sie können hier eingeben, welcher Text auf der Startseite immer oben als Erstes stehen soll.  Dieser Text rutscht niemals nach. Wenn dort nichts stehen soll, einfach nichts eintragen (auch kein Leerzeichen).
$this->content->template['message_320']="Qui puoi editare il testo che apparirà sempre in primo piano sull'home page (solo sull'home page); il testo rimane sempre nella parte alta, al primo posto; se non vuoi che appaia non inserire nulla, neanche spazi.";
//Weitere Einstellungen für die Startseite:
$this->content->template['message_321']="Ulteriori impostazioni:";
//Sollen neue Artikel automatisch auf der Startseite erscheinen?
$this->content->template['message_322']="Vuoi che i nuovi Articoli appaiano automaticamente sulla Home Page?";
// Sollen Artikel immer komplett dargestellt werden, auch wenn es mehrere zu einem Menü-Punkt gibt?
$this->content->template['message_323']="Vuoi visualizzare gli Articoli in forma integrale, anche se ce ne sono più di 1 per voce di menu?";
//
$this->content->template['message_324']="message_324";
//Soll die Anzahl der Kommentare angezeigt werden?
$this->content->template['message_325']="Vuoi mostrare il numero di commenti?";
//Suchmaschinenfreundliche Adressen (URLs):
$this->content->template['message_326']="Indirizzi internet amichevoli (URLs):";
//Suchmaschinenfreundliche URLs können dann verwendet werden, wenn Ihr Server bestimmte Module installiert hat. Dieses sind entweder "mod_rewrite" oder "mod_mime", wobei "mod_rewrite" zu bevorzugen ist, wenn beides installiert ist.
$this->content->template['message_327']="Si possono utilizzare Indirizzi internet amichevoli o semplificati (URLs), se risultano installati sul Server alcuni Moduli. Questi sono o \"mod_rewrite\" o \"mod_mime\", dove \"mod_rewrite\" è la scelta da preferire, se sono installati entrambi.";
//Adressen mit mod_rewrite verbessern?
$this->content->template['message_328']="Indirizzi migliorati con mod_rewrite?";
//Adressen mit mod_mime verbessern?
$this->content->template['message_329']="Indirizzi migliorati con mod_mime?";
//Kein Modul vorhanden?
$this->content->template['message_330']="Nessun modulo è installato?";
//Cachefunktion aktivieren:
$this->content->template['message_331']="Attivazione Funzione di Cache.";
//Die Cachefunktion beschleunigt den Seitenaufbau erheblich (Faktor 10), stellen Sie aber sicher, dass das Verzeichnis <strong>cache</strong> auch beschreibbar ist.
$this->content->template['message_332']="La funzione Cache velocizza in modo significativo (10x) la visualizzazione delle pagine: assicurarsi che la directory <strong>cache</strong> sia scrivibile.";
//Soll die Cache Funktion aktiviert werden?:
$this->content->template['message_333']="Vuoi che la funzione Cache sia attivata?";
//Das Cache-Verzeichnis ist nicht beschreibbar! Ändern Sie die Rechte für das Verzeichniss über Ihr FTP Programm oder Ähnliches. Die Rechte müssen 777 sein.
$this->content->template['message_334']="La directory di cache non è scrivibile! Cambia i diritti per la directory, col tuo programma FTP o qualcosa di simile. I Diritti devono essere 777.";

// Hier können Sie den Text im Kontakt-Formular ändern
$this->content->template['message_kontakttext_h1'] = "Qui puoi cambiare le impostazioni del Modulo di Contatto";

//Eine neue Datei hochladen
$this->content->template['message_335']="Upload (caricamento) Nuovo File";
//<p>Sie können hier eine Datei auswählen, die Sie hochladen möchten.</p><p>Es gelten dabei folgende Regeln:<ol><li>Maximale Dateigröße 2MB</li><li>Erlaubte Formate sind: zip; doc; txt; pdf;</li></ol></p>
$this->content->template['message_336']="<p>Qui puoi selezionare un file di cui vuoi fare l'upload (caricare) sul sito.</p>
Considera ed osserva le seguenti regole:
<ol><li>Dimensione Max del File: 2MB
</li><li>Formati/estensioni non ammesse: .php e .html</li></ol>";
//Eingabe der Datei:
$this->content->template['message_337']="Scegli il file:";
//Das Dokument:
$this->content->template['message_338']="Documento:";
//Name der Datei, wie sie im Text bezeichnet werden soll.
$this->content->template['message_339']="Nome del file, come sarà visualizzato nel testo:";
//Bezeichnung
$this->content->template['message_340']="Etichettatura";
//Datei hochladen:
$this->content->template['message_344']="upload file:";
//Eine neue Datei hochladen
$this->content->template['message_345']="Upload di un Nuovo file";
//Suche nach einer Datei in der Datenbank:
$this->content->template['message_346']="Cerca un file nel database:";
//Eingabe neuer Daten
$this->content->template['message_347']="Inserisci nuovi dati";
//Etwas stimmt nicht, bitte überprüfen Sie ihre Daten.
$this->content->template['message_348']="Qualcosa non va, controlla i dati, grazie.";
//Änderung der Daten zu der Datei:
$this->content->template['message_349']="Edita i dati del file:";
//Beschreibung der Datei:
$this->content->template['message_350']="Descrizione dle file:";
//Name der Datei:
$this->content->template['message_351']="Nome del file:";
//Änderung der Daten zur Datei.
$this->content->template['message_352']="Modifica dati file.";
//Hier können Sie die bereits in der Datenbank vorhandenen Daten ändern oder löschen.
$this->content->template['message_353']="Qui si possono modificare i dati dei file esistenti oppure eliminare i file stessi";
//Änderung der Daten zu der Datei:
$this->content->template['message_354']="Cambia i dati del file:";
//Daten übergeben.
$this->content->template['message_upload_change_uploadfile_legend'] = "Change File";
$this->content->template['message_upload_change_uploadfile_text'] = 
"Replace the existing file with a new one.
All inplacements in articles etc. will be left untouched";
$this->content->template['message_upload_change_uploadfile_label'] = "Select new file";
$this->content->template['message_upload_change_uploadfile_submit'] = ".. change";

$this->content->template['message_upload_delete_text'] = "Do you really want to delete the file?";

$this->content->template['message_355']="Invia dati";
//hochladen
$this->content->template['message_356']="upload";
//ändern
$this->content->template['message_357']="modifica";
//Welchen Editor wollen Sie benutzen?
$this->content->template['message_358']="Che Editor vuoi usare?";
//Um den Namen oder die Beschreibung eines Forums zu ändern, einfach auf das Forum klicken, es öffnet sich die Bearbeitungsmaske des jeweiligen Forums.
$this->content->template['message_359']="Per modificare nome o descrizione di un Forum cliccare sul link relativo e si aprirà la maschera per le modifiche";
//Und hier die komplette Forenliste
$this->content->template['message_360']="Lista completa dei Forum";
//An User
$this->content->template['message_361']="Utente";
//Löschen
$this->content->template['message_362']="Elimina";
//Hier kann eine Sprache ausgewählt werden.
$this->content->template['message_363']="Qui si può selezionare una lingua.";
//Weitere Sprachen im Frontend:
$this->content->template['message_364']="Altre lingue per il Frontend:";
//Weitere Sprachen:
$this->content->template['message_365']="Altre Lingue:";
//Hier wird die Standard-Sprache festgelegt
$this->content->template['message_366']="Questa è la lingua di default";
//Hier werden weitere Sprachen für das Frontend ausgewählt. Bedenken Sie, dass Sie für jede Sprache eigene Menüpunkte und Artikel eingeben müssen!
$this->content->template['message_367']="Ci sono più lingue disponibili per il front-end. Ricordarsi che per ogni lingua sono necessari articoli e voci di menu specifici";
//Frontend
$this->content->template['message_368']="Frontend";
//ausloggen
$this->content->template['message_369']="Esci";
//Menü überspringen
$this->content->template['message_370']="Vai al Menu";
//Bildname
$this->content->template['message_371']="Nome dell'immagine";
//Breite
$this->content->template['message_372']="Larghezza";
// Höhe
$this->content->template['message_373']=" Altezza";
//Orginal anschauen
$this->content->template['message_374']="Vedi originale";
//Thumbnail
$this->content->template['message_375']="Miniatura";
//Bild
$this->content->template['message_376']="Immagine";
//Beschreibung
$this->content->template['message_377']="Descrizione";
//ändern
$this->content->template['message_378']=" - modifica";
//Daten auf
$this->content->template['message_379']="Data in ";
//HTMLArea für Screenreader
$this->content->template['message_380']="HTMLArea per lettori di schermo";
//Links in diesem Text:
$this->content->template['message_381']="Links in questo Testo:";
//Diese Tabelle listet alle verfügbaren Menüpunkte des Backends auf und ermöglicht die Erteilung der Zugriffsrechte, da die Tabelle ebenfalls ein komplexes Formular darstellt.
$this->content->template['message_382']="Questa tabella elenca tutte le voci di menu disponibili sul backend e consente la concessione di diritti di accesso, da die Tabelle ebenfalls ein komplexes Formular darstellt.";
//Eingabe/Bearbeitung der Rechteverwaltung:
$this->content->template['message_383']="Input/modifica gestione dei diritti:";
//Rechteverwaltung
$this->content->template['message_384']="Gestione dei Diritti";
//<p>Geben Sie hier an, welche Gruppen auf welche Menüpunkte in der Administration Zugriff haben. <br />Es erscheinen hier nur Gruppen, die Sie in der Gruppenverwaltung auch freigegeben haben für die Administration.</p><p>Administratoren haben immer Zugriff und können nicht verändert werden.</p>
$this->content->template['message_385']="<p>Inserire i gruppi che possono accedere ed a quali voci di menu della sezione amministrazione. Solo i gruppi che sono autorizzati a vedere l'area amministrazione possono essere mostrati qui.</p><p>Gli Amministratori hanno l'accesso garantito a tutte le aree: questa è una opzione non modificabile.</p>";
//Menüname
$this->content->template['message_386']="Nome del Menu";
//Die Startseite kann nicht gelöscht werden!
$this->content->template['message_387']="La home page non può essere eliminata!";
//Hier können Sie die Menüreihenfolge ändern
$this->content->template['message_388']="Qui è possibile modificare l'ordine di successione delle voci di menu";
// <p>Die Startseite kann nicht verändert werden, diese bleibt immer auf Platz 1!</p>    <p>Der Klick auf rauf oder runter des jeweiligen Menüpunktes schiebt den Punkt jeweils einen Platz rauf oder runter.<br />    Möchten Sie einen Punkt mehrere Ebenen hoch schieben, dann müssen Sie mehrfach klicken. Nach jedem Klick wird die Tabelle erneuert.</p>    <p> An der <strong>Verschachtelung</strong> der Untermenüebenen ändert sich nichts.</p>
//$this->content->template['message_389']=" <p>La posizione della homepage non può essere modificata: essa deve essere sempre al posto 1!</p><p>Fare clic sul pulsante \"in alto\" o \"in basso\" per cambiare l'ordine. L'ordine cambia di un postto alla volta. Cliccando più volte si riuscirà a far salire di diverse posizioni la voce di menu scelta. Ogni volta la tabella viene ricaricata
//</p><p>L'<strong>annidamento</strong> non sarà modificato.</p>";
$this->content->template['message_389']=" <p>La posizione della homepage non può essere modificata: essa deve essere sempre al posto 1!</p><p>Fare clic sul pulsante in alto o in basso per cambiare l'ordine. L'ordine cambia di un postto alla volta. Cliccando più volte si riuscirà a far salire di diverse posizioni la voce di menu scelta. Ogni volta la tabella viene ricaricata
</p><p>L'<strong>annidamento</strong> non sarà modificato.</p>";
//Alle Menüpunkte, deren Reihenfolge verändert werden kann.
$this->content->template['message_390']="Si può cambiare l'ordine di qualunque voce di menu.";
// Reihenfolge runter
$this->content->template['message_391']=" ordina verso il basso";
//Reihenfolge rauf
$this->content->template['message_392']="sposta verso l'alto";
//runter
$this->content->template['message_393']="sotto";
//rauf
$this->content->template['message_394']="su";
//Zurück
$this->content->template['message_395']="Indietro";
//Ändern der Artikelreihenfolge
$this->content->template['message_396']="Cambio sequenza articoli";
//Artikelreihenfolge
$this->content->template['message_397']="sequenza articoli";
//Dieses Forum darf nicht gellöscht werden.
$this->content->template['message_398']="Questo Forum <strong>non</strong> può essere eliminato.";
//Das Plugin wurde installiert und ist nun bereit.
$this->content->template['message_399']="Il plugin è stato installato ed è operativo.";
//Das Plugin wurde deinstalliert und ist nun gelöscht.
$this->content->template['message_400']="Plugin disinstallato.";
//<!-- Wer darf downloaden?-->
$this->content->template['message_401']="Chi è autorizzato al download?";
// Einstellungen für das Forum
$this->content->template['message_402']="Impostazioni per il Forum:";
//Soll das Forum als Board angezeigt werden?
$this->content->template['message_402_2']="Vuoi mostrare il forum come Board?";
//Wie soll die Liste der letzten Forums-Beträge angezeigt werden?
$this->content->template['message_402_3']="Cosa vuoi che appaia nelle lista dei post recenti del forum?";
$this->content->template['message_402_3_1']="Mostra i post singoli (default)";
$this->content->template['message_402_3_2']="Mostra solo i temi";
//Wer darf im Frontend darauf zugreifen?
$this->content->template['message_403']="Chi è autorizzato alla visione sul Frontend?";
//Darf der Artikel kommentiert werden?
$this->content->template['message_404']="Vuoi commenti per questo articolo?";
//Kommentare
$this->content->template['message_405']="Commenti";
/*
 * <h2>CSS Dateien Ihrer Seite bearbeiten</h2>
<p> Sie können hier Ihre CSS Dateien bearbeiten, andere Layouts bequem einbinden und diese anpassen.</p>
 */
$this->content->template['message_406']="<h1>Gestione File CSS</h1>
<p> Si possono modificare i file CSS; altri layout sono facilmente incorporabili ed adattabili</p><h2>Altri Layouts</h2>...</p> ";
/*
 * <h2>CSS Datei bearbeiten</h2>
<p>Sie können hier eine CSS Datei auswählen, auf den Link klicken und dann bearbeiten</p>
 */
$this->content->template['message_407']="<h1>Modifica file CSS</h1>
<p>Per modificare un file CSS, cliccare sul link relativo, quindi editarlo</p>";
/*
 * <h2>Neue CSS Datei hochladen</h2>
<p> Wählen Sie eine CSS Datei aus, die Sie in das System einbinden wollen</p>
 */
$this->content->template['message_408']="<h2>Upload Nuovo file CSS</h2>
<p >Scegliere un file CSS che si desidera importare nel sistema. Si possono importare file zip scelti nel <a href=\"http://www.papoo.de/shop/index.php/cPath/2/category/templates.html\">nostro negozio</a> oppure semplici file CSS. Le cartelle ed i file necessari saranno creati in modo automatico. Per ogni stile verrà creata una cartella differente ed univoca.</p>";
//Eingabe der Datei:
$this->content->template['message_409']="Inserisci il file:";
//Das Dokument:
$this->content->template['message_410']="Documento:";
//Name des Styles
$this->content->template['message_411']="Nome dello stile";
//Bezeichnung
$this->content->template['message_412']="Nome";
//Datei hochladen
$this->content->template['message_413']="File Upload";
//Name des Styles
$this->content->template['message_414']="Nome dello stile";
//Quicktags Editor
$this->content->template['message_415']="Quicktags Editor";
//Markdown Editor
$this->content->template['message_416']="Markdown Editor";
//Standard
$this->content->template['message_417']="Default";
//Diese Tabelle listet Menüpunkte der Administrattion auf für die Rechtezuweisung
$this->content->template['message_418']="Questa tabella elenca le voci del menu di Amministrazione per l'assegnazione dei diritti ";
//Eintrag zu Menüpunkt
$this->content->template['message_419']="Inserimento voci di menu";
//
$this->content->template['message_420']="Vuoi il modulo di ricerca?";
// Plugin-Manager Überschrift: "Hier können Sie die Plugins verwalten"
$this->content->template['message_421']="Gestione dei Plugins";
// Plugin-Button "installieren"
$this->content->template['message_422']="installa";
// Plugin-Button "deinstallieren"
$this->content->template['message_423']="rimuovi";
// Installierte Plugins
$this->content->template['message_424']="Plugins Installati";
// Weitere zur Verfügung stehende Plugins
$this->content->template['message_425']="Altri Plugins disponibili";
$this->content->template['message_425_2']='<p>Weitere Plugins können nur durch einen Administrator installiert werden.</p>';
//Ihr Account wurde für 10 Minuten gesperrt.
$this->content->template['message_426']="Il tuo Account sarà inattivo per 10 Minuti, poichè ci sono stati 4 tentativi di accesso  non andati a buon fine!";
$this->content->template['message_diskspacelow']='Server disk space is very low. The login may fail.';
//
$this->content->template['message_427']="Nome del Sito ad es. \"CMS Papoo Accessibile\"";
//Safemode ist aktiviert. Sie müssen daher nach dem Upload der zip Datei diese manuell auf Ihrem Rechner entpacken und per FTP in das CSS Verzeichnis hochladen. Wenn Sie eine CSS Datei hochladen müssen Sie ein Verzeichnis mit dem Namen des CSS Datei anlegen (z.B. papoo für papoo.css). In diesem Verzeichnis muß dann noch das Verzeichnis bilder angelegt werden.
$this->content->template['message_428']="<h2>Upload (caricamento) file CSS</h2><p>E' necessario decomprimere manualmente il file zip e copiarlo, tramite programma ftp, nella cartella /css che si trova nella directory principale di installazione.</p><p>Se si dispone di un solo file CSS, è necessario creare una cartella, nella cartella /css, con il nome del file CSS e dopo copiare il file css al suo interno. Il file deve essre sempre denominato _index.css</p><p>Dopo di ciò si deve ricaricare la pagina \"Cambia CSS \", per far riconoscere il nuovo layout CSS al sistema.</p><p>Se ci sono problemi è disponibile il nostro <a href=\"http://www.papoo.de/forum/menuid/138\">Forum </a> (attualmente solo in tedesco).</p>";
//
$this->content->template['message_429']="Il file CSS non è scrivibile, modificarne i diritti, grazie. ";
//
$this->content->template['message_430']="Il file IEFixes_CSS non è scrivibile, modificarne i diritti, grazie.";
//
$this->content->template['message_431']="<p class=\"anzeige\">Altri Plugins sono disponibili  <a href=\"http://www.papoo.de/\" >sun nostro sito</a>.</p><p>A tal riguardo, è necessrio decomprimere il contenuto del corrispondente file zip del Plugin nella cartella di installazione di Paopoo.</p><p>Dopo ricaricare questa pagina.</p>";
//
$this->content->template['message_432']="<strong>Non ci sono nuovi file di stile.</strong>";
//
$this->content->template['message_433']="Inserire le News nella colonna di destra?";
//
$this->content->template['message_434']="Se si, quante ne volete visualizzare?";
//
$this->content->template['message_435']="Meta Informazioni ";
//
$this->content->template['message_436']="Meta Titolo";
//
$this->content->template['message_437']="Meta Descrizione";
//
$this->content->template['message_438']="Meta Parole Chiave";
//
$this->content->template['message_439']="Vuoi che la sezione notizie appaia nella colonna destra?";
$this->content->template['message_einloggen']="Login";
//Sie haben hier Zugriff auf Ihre persönlichen Daten.
$this->content->template['messagex_436']="Qui puoi modificare i dati del tuo profilo. ";
//es stehen
$this->content->template['messagex_437']="ci sono";
//veröffentlicht
$this->content->template['messagex_438']="articoli pubblicati.";
//Ihre Daten
$this->content->template['messagex_439']="I Tuoi Dati";
//ändern
$this->content->template['message_440']=" - modifica";
//You can change your Content.
$this->content->template['message_441']="Qui puoi modificare i contenuti del sito.";
//
$this->content->template['message_442']="Qui è possibile accedere ai dati del sito: cioè Articoli, Menu e tutto ciò che è modificabile.<br /> Alcune sezioni possono essere riservate e non accessibili.<br /> Cliccare sulle voci di menu relative, poste sulla sinistra, per cominciare.";
//
$this->content->template['message_443']="Edita i dati di sistema del sito";
//
$this->content->template['message_444']="Qui puoi fare dei cambiamenti ai dati di sistema: usa il menu sulla sinitra per le tue scelte.";
//
$this->content->template['message_445']="Qui puoi installare i Plugins";
//Hier können Sie die Medien verwalten
$this->content->template['message_446']="Gestione Media";
//Unter Medien verstehen wir Dateien (.doc, .pdf etc.) und Bilder.
$this->content->template['message_447']="Gestione dei file (con estensione: .doc, .pdf, ecc.) e delle immagini. <br />Scegli una voce dal menu!";
//Hier können Sie die Gruppen verwalten
$this->content->template['message_448']="Gestione Gruppi";
//Mit Hilfe der Gruppen kann eine feingesteuerte rechteverwaltung realisiert werden.
$this->content->template['message_449']="Con la funzione Gruppi si può realizzare una gestione accurata dei diritti.";
//<h1>Here you can edit the Articles of the Page</h1>
$this->content->template['message_450']="<h1>Qui si possono modificare gli articoli del sito</h1>";
//Vor dem Sprachwechsel 1x auf eintragen klicken.
$this->content->template['message_451']="Prima di cambiare la lingua cliccate su 'invia' una volta.";
//Inhalt
$this->content->template['message_452']="Contenuto";
//Teaser
$this->content->template['message_453']="Occhiello";
//Rechte
$this->content->template['message_454']="Diritti";
//Einstellungen
$this->content->template['message_455']="Gestione";
//Vorschau
$this->content->template['message_456']="Anteprima";
//Versionen
$this->content->template['message_457']="Versione";
//Eintragen
$this->content->template['message_458']="Invia";
//Artikel mit Überschrift anzeigen
$this->content->template['message_459']="Visualizza l'articolo con l'intestazione";
//Artikel mit Teaser anzeigen
$this->content->template['message_460']="Mostrare l'articolo con l'occhiello";
//Größe des Teaser Bildes:
$this->content->template['message_461']="Dimensioni della foto dell'occhiello:";
//Ändern Sie den Eintrag um eine andere Größe zu bekommen.
$this->content->template['message_462']="Cambiare i dati per ottenere dimensioni differenti.";
//Breite
$this->content->template['message_463']="Larg.";
//Höhe
$this->content->template['message_464']="Altez.";
//Artikel für RSS Feed bereitstellen?
$this->content->template['message_465']="Articolo da inserire nei feed RSS?";
//Artikel für RSS
$this->content->template['message_466']="Articolo per RSS";
//Zeitrahmen
$this->content->template['message_467']="Datazione";
//Dauerhaft Veröffentlichen?
$this->content->template['message_468']="Publicare la data?";
//Oder
$this->content->template['message_469']="Oppure";
//Format.Tag.Monat.Jahr
$this->content->template['message_470']="Formato: Giorno.Mese.Anno Ore:Minuti";
//Veröffentlichen von (z.B.:
$this->content->template['message_471']="Pubblicato a cura di (ad es.:";
//Veröffentlichen bis (z.B.:
$this->content->template['message_472']="Pubblicato fino a (ad es.: ";
//Danach verschieben zu Menüpunkt (ist dann dort dauerhaft aktiv)
$this->content->template['message_473']="Dopo spostati alla voce di menu (permanentemente attivo)";
// Artikel deaktivieren
$this->content->template['artikel_einstellung']['pub_wohin_standard'] = "Disattiva Articolo";
// Wollen Sie den Artikel wirklich löschen?
$this->content->template['artikel_save']['loeschen_legend'] = "Vuoi veramente eliminare questo articolo?";
// Den Artikel übernehmen?
$this->content->template['artikel_save']['uebernehmen_legend'] = "Cambiare Autore?";
$this->content->template['artikel_save']['uebernehmen_text'] = "Non sei l'autore di questo articolo. Se si desidera diventare autore di questo articolo, cliccare qui";
$this->content->template['artikel_save']['uebernehmen_checkbox'] = "Cambia Autore";
//vom
$this->content->template['message_474']="da";
//Versionen aus der aktuellen Bearbeitung
$this->content->template['message_475']="Versione della sessione attuale";
//Versionen aus vorherigen Bearbeitungen
$this->content->template['message_476']="Versione di altre sessioni";
//Alphabetisch
$this->content->template['message_477']="alfabetico";
//Zeitlich
$this->content->template['message_478']="cronologico";
//Menüpunkt
$this->content->template['message_479']="voci menu";
//Menü-Punkt auswählen
$this->content->template['message_480']="Selezione voce menu";
//Wählen Sie den Menü-Punkt zu dem Sie die Artikel-Reihenfolge ändern wollen:
$this->content->template['message_481']="Seleziona la voce del menu per modificare gli articoli inerenti:";
//auswählen
$this->content->template['message_482']="Seleziona";
//Soll ein Extra Stylesheet eingebunden werden?
$this->content->template['message_483']="Volete un extra stile per questa voce?";
//Der Pfad zum aktuellen CSS Verzeichnis wird automatisch erzeugt. Nötig ist hier also nur der Datei Name im CSS Verzeichnis.
$this->content->template['message_484']="Il percorso alla cartella CSS attuale è generato automaticamente: è necessario solo il nome del file";
//Name der Datei.
$this->content->template['message_485']="Nome del file.";
//Soll das für alle Unterpunkte gelten?
$this->content->template['message_486']="Si applica anche alle voci di sottomenu?";
//Nachricht(en) verschieben
$this->content->template['message_487']="Movimento dei Messaggi(o)";
//Wählen Sie das Forum aus:
$this->content->template['message_488']="Selezionare dal Forum:";
//keine
$this->content->template['message_489']="nessuna";
//In die Datenbank eintragen
$this->content->template['message_490']="Inserisci nel database";
//Bilder hochladen
$this->content->template['message_491']="Upload (carica) Immagini";
//Wählen Sie eine Kategorie aus
$this->content->template['message_492']="Seleziona una Categoria";
//Es wurden keine Bilder gefunden.
$this->content->template['message_493']="Non ci sono immagini";
//Soll dieser Eintrag wirklich gelöscht werden?
$this->content->template['message_494']="Vuoi veramente eliminare questo record?";
//Kategorie
$this->content->template['message_495']="Categoria";
//ändern
$this->content->template['message_496']=" - modifica";
//Neue Kategorie anlegen
$this->content->template['message_497']="Creare Nuova Categoria";
//Name der Kategorie
$this->content->template['message_498']="Nome della Categoria";
//Name eingeben:
$this->content->template['message_500']="Inserisci Nome:";
//Welche Gruppen dürfen auf die Kategorie zugreifen?:
$this->content->template['message_501']="Gruppi che hanno i diritti per entrare nella categoria?:";
//Gilt nur für die Bearbeitung in der Admin.
$this->content->template['message_502']="Solo per amministrazione.";
//Die existierenden Kategorien
$this->content->template['message_503']="Categorie Esistenti";
//Sie können hier die Automatismen die Papoo bietet editieren.
$this->content->template['message_504']="Qui puoi editare gli automatismi del sito.";
//Einstellungen für Artikel
$this->content->template['message_505']="Impostazioni per gli Articoli:";
//Soll der Autor angezeigt werden?
$this->content->template['message_506']="Mostrare l'Autore?";
//Soll angezeigt werden, wie oft der Artikel schon besucht wurde?
$this->content->template['message_507']="Mostrare quanto volte gli Articoli vengono visionati?";
//Benachrichtigungen bei:
$this->content->template['message_508']="Notifica/Comunica nel caso di:";
//Einem neuen Benutzer?
$this->content->template['message_509']="un nuovo Utente?";
//Einem neuen Forumseintrag?
$this->content->template['message_510']="un nuovo messaggio nel Forum?";
//Einem neuen Gästebucheintrag?
$this->content->template['message_511']="un nuovo messaggio nel 'Libro degli Ospiti' (Guestbook)?";
//Einem neuen Kommentar?
$this->content->template['message_512']="un nuovo commento?";
//Email Adresse für die Benachrichtigungen
$this->content->template['message_513']="Indirizzo Email per le comunicazioni";
//Spamschutz
$this->content->template['message_514']="Protezione Spam";
//Spamschutz-Modus
$this->content->template['message_515']="Metodo Protezione Spam";
//kein Spamschutz
$this->content->template['message_516']="nessuna Protezione Spam";
//Spamschutz durch Anzeige eines Bildes mit einer Kennzahl
$this->content->template['message_517']="Protezione Spam mediante immagine con un codice";
//Spamschutz durch Lösen einer einfach Aufgabe
$this->content->template['message_518']="Protezione Spam mediante risoluzione di una semplice operazione";
//Spamschutz durch Sortieren einzelner Zeichen
$this->content->template['message_519']="Protezione Spam mediante successione ordinata di caratteri";
//Spamschutz zufällig auswählen
$this->content->template['message_520']="Protezione Spam casuale";
//Sicherung der Datenbank erstellen
$this->content->template['message_521']="Funzione Copia-Database";
//Sie können hier eine Sicherung der Datenbank erstellen, die Sie nach einer Neuinstallation oder zu einem beliebigen andern Zeitpunkt wieder einspielen können.
$this->content->template['message_522']="Si può fare un backup del database, per trarre vantaggio dopo nuova installazione od ogni volta si desideri (operazione ripetibile).";
$this->content->template['message_522a']="Conferma download (tasto destro mouse -> salva come).";
$this->content->template['message_522b']="Conferma dopo Download la cancellazione sul Server!!! ATTENZIONE!!!.";

$this->content->template['message_522c']="Conferma Download ";
$this->content->template['message_522d']="Qui è rispristinabile il file restore.sql. Effettuare il backup (in luogo sicuro) del file SQL attraverso FTP, nella cartella /interna/templates_c, rinominarlo in restore.sql quindi cliccare sul link sottostante. Questo procedimento è necessario se il file è molto grosso e il modulo sottostante non funziona (non garantisce il ripristino). <br />";

$this->content->template['message_522e']="ripristino file restore.sql  ";
//Jetzt eine Sicherung erstellen
$this->content->template['message_523']="Ora creare un backup";
//Eine Sicherung einspielen
$this->content->template['message_524']="Inserire un backup";

$this->content->template['message_524a']="Upload di bacup ed inserimento";
//Sicherungs Datei wurde eingespielt.
$this->content->template['message_525']="Il file di backup è stato registrato.";
/**Um eine Sicherung einzuspielen wählen Sie bitte die Sicherungsdatei aus:</p>
<p><strong style="color:red;">ACHTUNG - Wenn Sie eine Sicherung einspielen werden alle aktuellen Daten unwiederruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung!</strong>*/
$this->content->template['message_526']='Per effettuare il backup selezionare un file di backup:</p>
<p><strong style="color:red;">ATTENZIONE - Se viene ripristinato un file di backup, tutti i dati attualmente presenti nel database verranno cancellati. Dunque, fate necessariamente un backup prima di ogni altra cosa!</strong>';
//Gruppe auswählen
$this->content->template['message_527']="Seleziona Gruppo";
//Ergebnis exportieren
$this->content->template['message_528']="Esporta risultati";
//exportieren
$this->content->template['message_529']="Esporta";
//Vorname
$this->content->template['message_530']="Nome";
//Nachname
$this->content->template['message_531']="Cognome";
//Strasse + Hausnummer
$this->content->template['message_532']="via + numero";
//Postleitzahl
$this->content->template['message_533']="CAP";
//Wohnort
$this->content->template['message_534']="Città";
//Benutzername
$this->content->template['message_535']="Nome Utente";
//Signatur
$this->content->template['message_signatur']="Firma";
//Welche Forum Ansicht möchten Sie?
$this->content->template['message_536']="Come vuoi vedere il Forum?";
//Thread Ansicht
$this->content->template['message_537']="Thread";
//Board Ansicht
$this->content->template['message_538']="Board";
//Wählen Sie hier Ihren dauerhaften Style aus, mit dem Sie immer automatisch eingeloggt werden.
$this->content->template['message_539']="Scegli lo stile con il quale hai fatto il login";
//Modul-Manager
$this->content->template['message_540']="Gestione Moduli";
//Liste installierter Module:
$this->content->template['message_541']="Lista dei Moduli installati:";
//Nummer
$this->content->template['message_542']="Numero";
//Bereich
$this->content->template['message_543']="Gamma";
//Diesem Bereich sind keine Module zugewiesen.
$this->content->template['message_544']="Questa area non ha moduli disponibili.";
//Diesem Bereich ein weiteres Modul hinzufügen:
$this->content->template['message_545']="Aggiungi nuovo modulo:";
//Modul hinzufügen
$this->content->template['message_546']="Aggiungi modulo";
//Diese(r) Style(s) ist im Verzeichnis aber nicht aktiviert.
$this->content->template['message_547']="Stile(i) installato ma non attivato.";
//Klicken Sie auf den Style um ihn zu aktivieren.
$this->content->template['message_548']="Clicca sullo stile per attivarlo.";
//Dateien auswählen von Style:
$this->content->template['message_549']="Selezionail file di stile:";
//Klicken Sie auf den Namen der Datei um sie zu bearbeiten.
$this->content->template['message_550']="Clicca sul nome del file per editarlo.";
//Nicht beschreibbar, ändern Sie die Dateirechte!
$this->content->template['message_551']="Non modificabile, cambia i diritti del file!";
//Dieser Style ist Standard-Style.
$this->content->template['message_551']="Questo è lo Stile Standard.";
//Diesen Style zum Standard machen.
$this->content->template['message_552']="Fai diventare questo Stile lo Standard.";
//Diesen Style löschen
$this->content->template['message_553']="Elimina questo Stile";
//Klicken Sie hier um den Style sofort zu löschen!!<br /><strong>Achtung</strong> Wenn Sie auf den Link klicken wird der Style gelöscht.
$this->content->template['message_554']="Clicca qui per eliminare subito lo stile!!<br /><strong>Attenzione</strong> Se clicchi sul link lo stile sarà eliminato definitivamente.";
//
$this->content->template['message_555']="Indietro alla vista complessiva.";
//
$this->content->template['message_556']='nobr:<h2>Panoramica sulle chiavi di accesso rapido</h2>
<ul>
<li>alt+0: Guida</li>
<li>alt+9: Chiude la finestra della Guida</li>
<li>alt+1: Amministrazione sito</li>
<li>alt+2: Esci (Log out)</li>
<li>alt+8: Direttamente al contento pricipale</li>
</ul>
<h2>Tabella di Quicknav (navigazione veloce)</h2>
<p>
Per usare questa funzione, ad esempio, basta digitare go01 seguito da invio. Se hai digitato qualcosa prima, usa la combinazione shift + x. Dopo puoi digitare go01 seguito da invio.</p>
<strong>Questa funzione ha bisogno di Javascript!</strong>
<br /><br />
<table><tr><th>Nome Menu
</th><th>Comando</th>';
//


$this->content->template['message_557'] = 'Attivare funzione "Dillo ad un amico"?';
$this->content->template['message_558'] = "Nessun link se la voce di menu è attiva?";
$this->content->template['message_559'] = 'Mostrare il numero di pagine viste (visitatori)?';
$this->content->template['message_560'] = 'Mostrare il Link "Stampa Articolo"?';
$this->content->template['message_560a']='Shall the automatic replacement be carried out (eg, acronyms, etc.)?';
$this->content->template['message_560b'] = 'Shall the information to check or publish an article also be send as e-mail?';
//Klicken Sie auf den Style um ihn zu aktivieren.
$this->content->template['intcss_klickaktiv'] = 'Clicca sullo stile per attivarlo.';
//Bezeichnung ändern
$this->content->template['intcss_bezaend'] = 'modifica nome';
//Style löschen
$this->content->template['intcss_bezloesch'] = 'Elimina Stile';
//Wählen Sie aus bei welchem Style Sie die Module ändern möchten.
$this->content->template['intmod_staend'] = 'Scegliere lo stile per il modulo.';
//Auswählen
$this->content->template['intimg_ausw'] = 'Seleziona';
//Urls mit mod_rewrite und sprechenden Urls?
$this->content->template['stamm_mod_rewrite_sprech'] = 'Urls con mod_rewrite und Urls dettate?';
//Cache Einstellungen
$this->content->template['stamm_cache1'] = 'Impostazioni Cache';
//Cache aktivieren?
$this->content->template['stamm_cache2'] = 'Attivare Cache?';
//Cache Lifetime in Sekunden (3600 = 1 Stunde)
$this->content->template['stamm_cache3'] = 'Durata Cache in secondi (3600 = 1 ora)';
//Artikelbearbeitung in einer kompletten Übersicht (Sinnvoll für Screenreader)
$this->content->template['message_561'] = 'Gestione globale di tutti gli articoli';
//Quicktags Editor Screenreader Extension
$this->content->template['message_562'] = 'Editor Estensione per lettori di schermo';
$this->content->template['message_562a'] = 'show content- and menu-tree expanded';
//Voreinstellung veröffentlichen auf jeder?
$this->content->template['message_563'] = 'Preimpostazioni di pubblicazione per ognuno?';
//Voreinstellung auf veröffentlichen?
$this->content->template['message_564'] = 'Preimpostazioni di pubblicazione?';
//Voreinstellung auf Startseite listen?
$this->content->template['message_565'] = 'Preimpostazioni per la lista della home page?';
//Menürechte Voreinstellung auf jeder?
$this->content->template['message_566'] = 'Preimpostazioni del menu di destra per ognuno?';
// Voreinstellungen
$this->content->template['message_567'] = 'Preimpostazioni per gli Articoli';
// Voreinstellungen
$this->content->template['message_568'] = 'Il caporedattore puo modificare sempre gli articoli';
/**
<h2>Papoo Lizenzen und Autoren</h2>
<p>Papoo ist ein Produkt von Carsten Euwens - Papoo Software und Design und steht unter der GPL Lizenz.<br />
Der Name Papoo ist geschützt und darf nur mit Erlaubnis von Carsten Euwens - Papoo Software benutzt werden.</p>
<p>Ihre Version ist:
*/
$this->content->template['creditdata1'] = 'nobr:<h2>Autori e licenza di Papoo</h2>
<p>Papoo è un prodotto di Carsten Euwens - Papoo Software <sub>(c)</sub> Design ed è sotto diversi tipi di licenza relativamente alle diverse versioni.<br />
Il nome Papoo è registrato ed è necessario ottenere una autorizzazione, prima di un qualsiasi suo utilizzo, a Dr. Carsten Euwens - Papoo Software.</p>
<p>La tua versione è: ';
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
$this->content->template['creditdata2'] = 'nobr:<h3>Autore</h3>
<p>
(c) Carsten Euwens, 2007<br />
<h3>Altri Autori</h3>
* Stephan Bergmann, 2007
</p>
<h3>Papoo utilizza i seguenti componenti software aggiuntivi, con le licenze qui sotto elencate.</h3>
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
<h2>Licenza  (originale)</h2>
<h2>   Software-Lizenzbedingungen ab Papoo 3.6.1</h2><br><h3>   1. Vorbemerkung</h3><strong>   1.1</strong>    Diese Lizenzbedingungen gelten ergänzend zu den Allgemeinen Geschäftsbedingungen. Die Lizenzbedingungen werden durch das Fortsetzen der Installation anerkannt.<br><br><h2><strong>   2. Einräumung von Nutzungsrechten</strong></h2><strong>   2.1</strong>    Mit Vertragsschluss über die Lieferung/den Download von <span class="lang_en" xml:lang="en" lang="en"> Software</span>  (unabhängig vom Speichermedium) wird dem Kunden das nicht übertragbare und nicht ausschließliche Nutzungsrecht an der vertragsgegenständlichen <span class="lang_en" xml:lang="en" lang="en"> Software</span>  eingeräumt, das auf die nachfolgend beschriebene Nutzung beschränkt ist. Alle dort nicht ausdrücklich aufgeführten Nutzungsrechte verbleiben bei Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  bzw. Dr. Carsten Euwens (Papoo <span class="lang_en" xml:lang="en" lang="en"> Software)</span>  als Inhaber aller Urheber- und Schutzrechte.<br><br><h3>   3. Umfang der Nutzungsrechte</h3><strong>   3.1 </strong>   Mit der Lieferung erwirbt der Kunde das Recht, die ihm gelieferte <span class="lang_en" xml:lang="en" lang="en"> Software</span>  im vertragsgemäßen Umfang (Anzahl der erworbenen Lizenzen) auf beliebigen Rechnern zu nutzen, die für diese Zwecke geeignet sind. Die Dauer des Nutzungsrechts ist für Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> Produkte unbegrenzt. <br><br><strong>   3.2 </strong>   Der Kunde verpflichtet sich, das Programm nur für eigene Zwecke zu nutzen und es Dritten weder unentgeltlich noch entgeltlich zu überlassen. Die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  darf pro Lizenz nur unter einer <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  auf einem <span class="lang_en" xml:lang="en" lang="en"> Server,</span>  nicht jedoch gleichzeitig auf zwei oder mehreren Domains, genutzt werden. Für die Nutzung einer weiteren <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  ist eine weitere Domainlizenz erforderlich. Pro Domainlizenz darf eine weitere <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  mit der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  genutzt werden. Eine Domainlizenz ist nicht erforderlich, wenn verschiedene Domainnamen auf den gleichen Inhalt verweisen, wie z.B. papoo.de und papoo.org.<br><strong><br>   3.2.1</strong>    Die Papoo Light Version darf hingegen auf beliebig vielen Domains genutzt werden, dies gilt für nicht kommerzielle Auftritte wie rein private Internetauftritte und Internetauftritte gemeinnütziger Organisationen. Alle anderen Betreiber müßen eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  Lizenz erwerben die in allen Produkten außer Papoo Light schon enthalten ist. <br><strong><br>   3.2.2</strong>    Auf lokalen Testumgebungen die nicht der Öffentlichkeit zur Verfügung stehen, darf jede erworbene Version beliebig getestet werden.<br><br><strong>   3.3 </strong>   Der Kunde ist berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  auf die Festplatte des Servers zu installieren und zu nutzen sowie von der Originaldiskette oder CD-ROM eine Sicherungskopie zu fertigen, die aber nicht gleichzeitig neben der Originalversion genutzt werden darf. Im Falle eines Vertrages über eine Netzwerkversion/Mehrfach-Lizenz ist der Kunde berechtigt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  entsprechend der vertraglichen Vereinbarung zu jedem Zeitpunkt auf einem oder mehreren Rechnern mit mehreren Personen gleichzeitig zu nutzen.<br><br><strong>   3.4 </strong>   Der Kunde ist nicht berechtigt, Kopien der <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu erstellen, sofern die Kopien nicht zu Datensicherungszwecken erfolgen und auch nur zu diesem Zwecke eingesetzt werden. Er darf ferner die Softwarebestandteile, mitgelieferte Bilder, das Handbuch, Begleittexte sowie die zur <span class="lang_en" xml:lang="en" lang="en"> Software</span>  gehörige Dokumentation durch Fotokopieren oder Mikroverfilmen, elektronische Sicherung oder durch andere Verfahren nicht vervielfältigen, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugehörige Dokumentation weder vertreiben, vermieten, Dritten Unterlizenzen hieran einräumen noch diese in anderer Weise Dritten zur Verfügung stellen. Der Kunde ist nicht berechtigt, Zugangskennungen und/oder Passwörter für das Produkt oder für Datenbankzugänge, die mit dem Produkt im Zusammenhang stehen, an Dritte weiterzugeben. <br><br><strong>   3.5</strong>    Der Kunde ist  befugt, die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  und/oder die zugehörige Dokumentation ganz oder teilweise ausschließlich für die eigenen Bedürfnisse zu ändern, zu modifizieren, anzupassen oder zu dekompilieren. <br>   Weiterhin ist es dem Kunden untersagt, Copyrightvermerke, Kennzeichen/Markenzeichen und/oder Eigentumsangaben des Herausgebers an Programmen oder am Dokumentationsmaterial zu verändern. Allerdings ist es möglich die Copyrightvermerke durch das erwerben einer sogenannten Whitelabel Lizenz für jeweils eine <span class="lang_en" xml:lang="en" lang="en"> Domain</span>  aus dem Fuß der Seite zu entfernen.<br><br><strong>   3.6 </strong>   Das Papoo <acronym class="acronym" title="Content Management System">CMS</acronym> nutzt den TinyMCE Editor von Moxiecode, der unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz steht. Sie akzeptieren ebenfalls die Nutzung dieses Plugins und weitere Papoo Plugins von Drittherstellern unter der <acronym class="acronym" title="General Public License">GPL</acronym> Lizenz.<br><br><h3>   4. Haftung</h3><strong>   4.1 </strong>   Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  übernimmt keine Haftung für die Fehlerfreiheit der <span class="lang_en" xml:lang="en" lang="en"> Software.</span>  Insbesondere übernimmt die Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  keine Gewährleistung dafür, dass die <span class="lang_en" xml:lang="en" lang="en"> Software</span>  Ihren Anforderungen und Zwecken genügt oder mit anderen von Ihnen ausgewählten Programmen zusammenarbeitet. Die Verantwortung für die richtige Auswahl und die Folgen der Benutzung der <span class="lang_en" xml:lang="en" lang="en"> Software,</span>  sowie der damit beabsichtigten oder erzielten Ergebnisse, tragen Sie selbst.<br><br><strong>   4.2</strong>    Papoo <span class="lang_en" xml:lang="en" lang="en"> Software</span>  haftet nicht für Schäden die aufgrund der Benutzung dieser <span class="lang_en" xml:lang="en" lang="en"> Software</span>  oder der Unfähigkeit diese <span class="lang_en" xml:lang="en" lang="en"> Software</span>  zu verwenden entstehen. Wir haften nicht auf Schadensersatz für Mängel oder andere Pflichtverletzungen. Ausgenommen hiervon sind Schäden aus der Verletzung des Lebens, des Körpers oder der Gesundheit, wenn wir die Pflichtverletzung zu vertreten haben, und für sonstige Schäden, die auf einer vorsätzlichen oder grob fahrlässigen Pflichtverletzung durch uns oder auf einer von uns erklärten Garantie beruhen. Ausgenommen sind auch Schäden, für die wir nach dem Produkthaftungsgesetz zwingend haften oder die auf einer schuldhaften Verletzung wesentlicher Vertragspflichten zurückzuführen sind. In letzterem Fall beschränkt sich unsere Haftung auf den vorhersehbaren, typischerweise eintretenden Schaden.<br><br>   Die Pflichtverletzung unserer gesetzlichen Vertreter oder unserer Erfüllungsgehilfen steht einer Pflichtverletzung durch uns gleich.
';
//Geben Sie hier das Präfix der alten Installation an:
$this->content->template['message_569'] = 'Inserire il prefisso della vecchia installazione:';
// Das Präfix bitte ohne &quot;_&quot; angeben, also z.B. &quot;123&quot; und nicht &quot;123_&quot;.
$this->content->template['message_570'] = 'Prefisso senza &quot;_&quot; ad esempio &quot;123&quot; e non &quot;123_&quot;.';
// Zugriffs-Parameter der alten Datenbank
$this->content->template['message_571'] = 'Parametri di accesso del vecchio database';
// Befinden sich Ihre alten Daten nicht in derselben Datenbank wie die jetzige Installation,
#					müssen Sie hier zusätzlich die Zugriffs-Paramter der alten Datenbank angeben.<br />
#					Liegen die Daten in derselben Datenbank, könnnen sie die folgenden Felder einfach so belassen.
$this->content->template['message_572'] = 'Se i dati della precedente installazione sono in database diverso rispetto a quello della installazione corrente, è necessario inserire i parametri del tuo vecchio database.<br />
					Se utilizzava lo stesso database, allora lasciate i dati sottostanti.';
// Der alte Datenbank-Server:
$this->content->template['message_573'] = 'Il server del vecchio database:';
// Der alte Datenbank-Name:
$this->content->template['message_574'] = 'Il nome del vecchio database:';
// Der alte Datenbank-Benutzer-Name:
$this->content->template['message_575'] = 'Nome utente del vecchio database:';
// Das alte Datenbank-Benutzer-Passwort:
$this->content->template['message_576'] = 'La password del vecchio database:';
// Diese Felder können leider nicht auf ihre Richtigkeit überprüft werden. Haben Sie hier also falsche Angaben gemacht,
					#erhalten Sie nach Absenden der Seite eine Datenbank-Fehlermeldung oder eine komplett weisse Seite. In diesem Fall
				#	verwenden Sie bitte den &quot;Zurück-Knopf&quot; Ihres Browser und korrigieren Sie Ihre Angaben.
$this->content->template['message_577'] = "Questi dati non sono verificabili in automatico, controllarne l'esatezza prima 
di cliccare su invio; nel caso in cui fossero inseriti dati errati apparirà un messaggio di errore od una pagina bianca: in tal caso cliccare sul pulsante 'Indietro' del browser, per correggere i dati.";
// CSS-Styles einbeziehen
$this->content->template['message_578'] = 'Inclusione stili CSS';
/*
		Möchten Sie auch die Datenbank-Einträge der CSS-Sytles ihrer alten Installation übernehmen,
					dann können Sie die folgende Option aktivieren.<br />
					Beachten Sie dabei aber, dass die CSS-Styles dieser Installation dadurch verloren gehen.
					Wir empfehlen deshalb die CSS-Styles nicht zu übernehmen und sie anschließend über den Menü-Punkt
					&quot;System -> CSS-Layout&quot; einzupflegen.
*/
$this->content->template['message_579'] = 'Se si vogliono conservare le impostazioni del database anche con riferimento al vecchio stile CSS, puoi attivare la seguente opzione.<br />Considera peraltro che gli stili CSS di questa installazione andranno persi. Ricordarsi anche le alternative disponibili nella voce menu &quot;Impostazioni -> Layout CSS&quot;';
//Non è consigliabile conservare gli stili CSS e quindi conseguentemente sulla voce menu &quot;System -> CSS-Layout&quot;
// CSS-Styles einbeziehen
$this->content->template['message_580'] = 'Inclusione stili CSS';
// Datenbank-Parameter setzen
$this->content->template['message_581'] = 'Imposta parametri database';
// Parameter setzen
$this->content->template['message_582'] = 'Imposta parametri';
// Die folgenden Tabellen sind in ihrer aktuellen Installation nicht vorhanden.
$this->content->template['message_583'] = 'Le seguenti tabelle non sono disponibili nella attuale installazione.';
// Stellen Sie bitte sicher, dass Sie alle benötigten Plugins installiert haben.
$this->content->template['message_584'] = 'Si prega di assicurarsi di avere tutti i necessari plug-in installati.';
// Sie können das Update trotzdem ausführen. Die oben aufgeführten Tabellen werden allerdings nicht in Ihre aktuelle
				#Installation übertragen.
$this->content->template['message_585'] = 'Aggiornamento ancora possibile. Le tabelle soprastanti non sono disponibili per la installazione corrente.';
// Update jetzt durchführen
$this->content->template['message_586'] = 'Aggiornare';
// update
$this->content->template['message_587'] = 'Aggiorna';
// Die folgenden Tabellen wurden aktualisiert:
$this->content->template['message_588'] = 'Le seguenti tabelle sono state aggiornate:';
// Das alte Präfix:
$this->content->template['message_589'] = 'Prefisso precedente:';
//Update
//
$this->content->template['message_590'] = 'Aggiorna';
//Lesezeichen
$this->content->template['message_591'] = 'Collegamenti';
//Lesezeichen
$this->content->template['message_592'] = 'Imposta Collegamenti';
//Lesezeichen
$this->content->template['message_593'] = 'Cancella Collegamenti';
//Soll mit Kategorien gearbeitet werden?
$this->content->template['message_594'] = 'Lavorare con le categorie?';
//Kategorien
$this->content->template['message_595'] = 'Categorie';
//Dieser Menü-Punkt ist die Startseite und kann keinem anderen Menü-Punkt untergeordnet werden.
$this->content->template['message_596'] = 'Questa voce di menu è della home page e si possono subordinare tutte le altre voci di menu.';
//Wählen Sie hier die Kategorie aus. Die Einstellung wirkt sich im Frontend nur aus wenn die Nutzung der Kategorien in den Stammdaten aktiviert ist.
$this->content->template['message_597'] = "Selezionare la Categoria. Le impostazioni appariranno sul Frontend solo se l'utilizzodelle categorie è attivato fra le opzioni principali.";
//Sortierung der Kategorien
$this->content->template['message_598'] = 'Ordine delle categorie';
//Sortieren Sie die Kategorie aus die Sie bearbeiten wollen.
$this->content->template['message_599'] = 'Ordinare categorie che vuoi editare.';
//Wirklich l&ouml;schen?
$this->content->template['message_600'] = 'Eliminare definitivamente?';
//Den Eintrag
$this->content->template['message_601'] = 'Immissione Dati ';
//wirklich l&ouml;schen?
$this->content->template['message_602'] = 'Cancellare veramente?';
//Löschen
$this->content->template['message_603'] = 'Elimina';
//Die Daten wurden gel&ouml;scht!
$this->content->template['message_604'] = 'I dati sono stati cancellati!';
//Die Daten wurden eingetragen!
$this->content->template['message_605'] = 'I dati sono stati immessi!';
//Bitte bearbeiten Sie die Daten.
$this->content->template['message_606'] = 'Si prega di editare i dati.';
//Kategorie bearbeiten
$this->content->template['message_607'] = 'Modifica Categorie';
//Liste der Kategorien
$this->content->template['message_608'] = 'Elenco categorie';
//W&auml;hlen Sie die Kategorie aus die Sie bearbeiten wollen. Die Hauptkategorie kann nicht gelöscht werden.
$this->content->template['message_609'] = 'Selezionare la categoria che vuoi modificare. La categoria principale non può essere eliminata.';
//&auml;ndern
$this->content->template['message_610'] = 'cambia';
//Sie k&ouml;nnen hier Kategorien erstellen und bearbeiten. Diesen Kategorien k&ouml;nnen die Menüpunkte und Artikel zugeordnet werden.
$this->content->template['message_611'] = 'È possibile creare e modificare le categorie. Queste categorie possono essere assegnate a voci di menu ed articoli.';
$this->content->template['message_categories_inactive']='Sie m&uuml;ssen erst noch in der Systemkonfiguration angeben, dass Sie mit Kategorien arbeiten wollen, bevor diese benutzt werden k&ouml;nnen.';
//Die Daten wurden eingetragen!
$this->content->template['message_612'] = 'Dati immessi!';
//Bitte bearbeiten Sie die Daten.
$this->content->template['message_613'] = 'Si prega di modificare i dati.';
//Neue Kategorie anlegen
$this->content->template['message_614'] = 'Creare nuova Categoria';
//Bezeichnung auf der Webseite
$this->content->template['message_615'] = 'Designazione sulla web page';
//Interne Bezeichnung
$this->content->template['message_616'] = 'Designazione interna';
//Welche Gruppen haben Schreibzugriff in der Administration
$this->content->template['message_617'] = 'Quali gruppi hanno accesso ai diritti di scrittura in amministrazione';
//Welche Gruppen haben Lesezugriff im Frontend
$this->content->template['message_618'] = 'Quali gruppi hanno accesso di lettura sul Frontend';
//Binden Sie hier Ihre Videos ein.
$this->content->template['message_619'] = 'Qui puoi inserire i tuoi video.';
//Sie können hier Ihre Videos einbinden die Sie per FTP in das Verzeichnis /video hochgeladen haben. Ein direkter Upload ist wg. der Größe der Videos nicht möglich.
$this->content->template['message_620'] = 'Puoi caricare il tuo video <strong>via FTP</strong> nella directory /video. Non è permesso il caricamento(upload) diretto di file molto grandi';
//Um ein Video einzubinden klicken Sie auf Videos einbinden. Sie können die videos auch Kategorien zuordnen, dafür dann Video Kategorien auswählen.
$this->content->template['message_621'] = 'Per gestire i video cliccare sul link del video da gestire. Si possono assegnare categorie ai video, ma solo categorie video';
//Videos einbinden
$this->content->template['message_622'] = 'Video inseriti e gestibili';
//Bearbeiten Sie hier Ihre Video Daten
$this->content->template['message_623'] = 'Gestione e modifica dei dati video';
//Eingebundene Videos
$this->content->template['message_624'] = 'Video inseriti';
//Klicken Sie auf ein Video um es zu starten und die Daten zu bearbeiten.
$this->content->template['message_625'] = 'Cliccare sul video da gestire, per modificarne i dati.';
//Nicht eingebundene Videos
$this->content->template['message_626'] = 'Video Non-embedded';
//Klicken Sie auf ein Video um die Daten zu bearbeiten und es einzubinden.
$this->content->template['message_627'] = 'Cliccare sul video da gestire, per modificarne i dati e fonderli.';
//Bearbeiten Sie die Video Daten
$this->content->template['message_628'] = 'Gestisci i dati video';
//Geben Sie hier die notwendigen Daten ein.
$this->content->template['message_629'] = 'Inserire i dati necessari.';
//Video
$this->content->template['message_630'] = 'Video';
//Name des Videos
$this->content->template['message_631'] = 'Nome del Video';
//Beschreibung (Was passiert auf dem Video, bitte in genauen Beschreibungen angeben ...):
$this->content->template['message_632'] = 'Descrizione (cosa succede nel video, fornire specifiche esaurienti, grazie...):';
//Dieser Link führt zur
$this->content->template['message_633'] = 'Questo link rimanda a';
//Dieser Link führt eine Seite weiter.
$this->content->template['message_634'] = 'Questo link rimanda ad una pagina.';
//Seite
$this->content->template['message_635'] = 'Pagina';
//Dieser Link führt eine Seite zurück.
$this->content->template['message_636'] = 'Questo link rimanda ad una pagina indietro.';
//Die aktuell angezeigte Seite.
$this->content->template['message_637'] = 'La pagina attualmente visualizzata.';
//Wenn nicht, welcher User soll darüber benachrichtigt werden:
$this->content->template['message_638'] = 'Se no, quali utenti devono essere avvisati su questo:';
//Auswählen
$this->content->template['message_639'] = 'Seleziona';
//alle
$this->content->template['message_640'] = 'Tutti';
//Username
$this->content->template['message_641'] = 'nome utente';
//keine (default)
$this->content->template['message_642'] = 'nessuna (default)';
//Update
$this->content->template['message_643'] = 'Aggiornamenti';
//ändern
$this->content->template['message_644'] = 'modifica';
//	Sie haben nur noch
$this->content->template['message_645'] = 'Tu hai solo';
//MB Speicher &uuml;brig. Bevor Sie weitere Plugins installieren erh&ouml;hen 			Sie den verf&uuml;gbaren Speicher.
$this->content->template['message_646'] = 'MB di memoria disponibile. Prima di installare plug-ins aggiuntivi devi aumentare la quantità di memoria disponibile.';
//Achtung
$this->content->template['message_647'] = 'Attenzione';
//Wollen Sie das folgende Plugin wirklich deinstallieren?
$this->content->template['message_648'] = 'Sei sicuro di disinstallare il seguente Plugin?';
//abbrechen
$this->content->template['message_649'] = 'Elimina';
//Direkt zum Bereich
$this->content->template['message_650'] = "Direttamente all'area";
//Style XML:
$this->content->template['message_651'] = 'Stile XML:';
//Style XML File
$this->content->template['message_652'] = 'File Stile XML';
//Datei:
$this->content->template['message_653'] = 'Dati:';
//Nach unten verschieben
$this->content->template['message_654'] = 'Sposta giù';
//Nach oben verschieben
$this->content->template['message_655'] = 'Sposta sopra';
//Modul aus diesem Bereich entfernen
$this->content->template['message_656'] = 'Rimuovi Moduli da questa area';
//Login
$this->content->template['message_657'] = 'Login';
//Einstellungen für die Bilderverwaltung
$this->content->template['message_658'] = 'Impostazioni per la gestione delle immagini';
//Funktioniert nur im Zusammenhang mit dem Export/Import Plugin.
$this->content->template['message_659'] = 'Funziona solo insieme al Plugin Export/Import.';
//Daten exportieren?
$this->content->template['message_660'] = 'Esportare i Dati?';
//Dabei werden die Bilder auf anderen Seiten direkt von diesem Server bezogen.
$this->content->template['message_661'] = 'In questo modo le immagini sono direttamente disponibili da altri siti  attraverso questo server.';
//Daten importieren?
$this->content->template['message_662'] = 'Importare i dati?';
//Sprache
$this->content->template['message_663'] = 'Lingua';
//Titel
$this->content->template['message_664'] = 'Titolo';
//Klicken Sie für Hilfe - es öffnet sich ein neues  Fenster
$this->content->template['message_665'] = 'Cliccare qui per Aiuto in linea - si aprirà una nuova finestra';
//Hilfe Icon
$this->content->template['message_667'] = 'Icona Aiuto';
//Hits
$this->content->template['message_668'] = 'Hits';
//Freischalten
$this->content->template['message_669'] = 'Sblocca';
//Liste aller Menüpunkte zur Schnell Navigation
$this->content->template['message_670'] = 'Lista di tutte le opzioni del menu per navigazione veloce';
//Start
$this->content->template['message_671'] = 'Start';
//Loggoff
$this->content->template['message_672'] = 'Log Out';
//W&auml;hlen Sie den Template Satz
$this->content->template['message_673'] = 'Selezionare il tipo di Template';
//Templatesatz
$this->content->template['message_674'] = 'Frase di Template';
//Standard
$this->content->template['message_675'] = 'Default';
//Menü
$this->content->template['message_676'] = 'Menu';
//Artikel
$this->content->template['message_677'] = 'Articolo';
//Sie dürfen diesen Artikel nicht verändern.
$this->content->template['message_678'] = 'Questo articolo non è modificabile.';
//Finden
$this->content->template['message_679'] = 'Trova';
//Eingabe
$this->content->template['message_680'] = 'Entrata';
//Bilder
$this->content->template['message_681'] = 'Immagine';
//auswählen
$this->content->template['message_682'] = 'seleziona';
//Downloads
$this->content->template['message_683'] = 'Download';
//CSS-Klassen
$this->content->template['message_684'] = 'Classi CSS';
//Sprache
$this->content->template['message_685'] = 'Lingua';
//
$this->content->template['message_686'] = 'Elimina Video';
/*
<h2>Einträge in der 3. Spalte</h2>
		<p>Suchen Sie hier den Eintrag aus der dritten Spalte den Sie bearbeiten wollen.</p>
		<p>Um die Einträge zu sortieren klicken Sie auf orderid eintragen. Die Sortierung betrifft immer alle Einträge und werden dann entsprechend den Zuordnungen im Frontend angezeigt. Hier werden immer alle angezeigt.</p>
		*/
$this->content->template['message_687'] = "<h2>Inserimento dati nella 3. Colonna</h2>
		<p>Qui si possono cercare le voci immesse nella terza colonna, che si vogliono modificare.</p>
		<p>Per ordinare una voce cliccare sull'id Ordine, relativo alla voce. Ordinare la sequenza ha effetto su tutte le voci e sono indicate in sintonia con l'ordine del FrontEnd. Qui sono sempre indicate tutte.</p>";
//Name / bearbeiten
$this->content->template['message_688'] = 'Nome / lavora su';
//Orderid
$this->content->template['message_689'] = 'id Ordine';
//Orderid f&uuml;r
$this->content->template['message_690'] = 'Ordina id per';
//Name
$this->content->template['message_691'] = 'Nome';
//Name des Eintrags
$this->content->template['message_692'] = 'Nome della voce';
//anzeigen
$this->content->template['message_693'] = 'Mostra';
//Immer anzeigen
$this->content->template['message_694'] = 'Mostra sempre';
//Wenn immer anzeigen, Häckchen setzen und auf speichern klicken
$this->content->template['message_695'] = 'Se si vuole sempre mostrata, cliccare per impostare e salvare';
//Vorhandene Einträge
$this->content->template['message_696'] = 'Voci disponibili';
//Menuid
$this->content->template['message_697'] = 'id Menu';
//entfernen
$this->content->template['message_698'] = 'elimina';
//Bearbeiten Sie hier das Kontaktformular
$this->content->template['message_699'] = 'Modifica modulo di contatto';
//Eintrag wurde gelöscht
$this->content->template['message_700'] = 'La voce è stata eliminata';
//Sie können hier die Felder Ihres Kontaktformulares bearbeiten.
$this->content->template['message_701'] = 'Qui è possibile modificare i singoli campi del modulo di contatto.';
//Neues Feld erzeugen
$this->content->template['message_702'] = ' Crea nuovo Campo';
//Um ein neues Feld zu erzeugen klicken Sie hier:
$this->content->template['message_703'] = 'Cliccare qui per creare un nuovo campo:';
//Neues Feld erzeugen
$this->content->template['message_704'] = 'Creare nuovo campo';
//Vorhanden Felder
$this->content->template['message_705'] = 'Campi Disponibili';
//Um die Daten zu bearbeiten klicken Sie auf den Eintrag bearbeiten dann die Daten.
$this->content->template['message_706'] = 'Per modificare i dati cliccare sulla voce i cui dati vanno modificati.';
//Felder im Kontaktformular
$this->content->template['message_707'] = 'Campi del Modulo di contatto';
//bearbeiten
$this->content->template['message_708'] = '- modifica';
//Sie können hier ein neues Feld eintragen.
$this->content->template['message_709'] = 'Qui è possibile inserire e registrare un nuovo campo.';
//Sie können hier ein Feld bearbeiten.
$this->content->template['message_710'] = 'Qui si può gestire un campo specifico.';
//Name des Feldes (keine Umlaute oder Sonderzeichen)
$this->content->template['message_711'] = 'Nome del campo (nessuna dieresi o carattere speciale)';
//Wählen Sie den Typ aus.
$this->content->template['message_712'] = 'Selezionare la tipologia.';
//Typ
$this->content->template['message_713'] = 'Tipologia';
//Text (default)
$this->content->template['message_714'] = 'Testo (default)';
//Beschreibende Daten (Sprache:
$this->content->template['message_715'] = 'Dati descrittivi (Lingua: ';
//Bezeichnung im Formular
$this->content->template['message_716'] = 'Denominazione che appare sul Modulo';
//Einstellungen
$this->content->template['message_717'] = 'Impostazioni';
//Feld muß ausgefüllt werden
$this->content->template['message_718'] = 'Campo Obbligatorio';
//Dieses Feld löschen
$this->content->template['message_719'] = 'Eliminare questo campo';
//Löschen
$this->content->template['message_720'] = 'Eliminare';
//
$this->content->template['message_721'] = 'Aggiungi Menu';

$this->content->template['message_restore'] = 'restore';


/**
<h1>Die Einträge des Top Menüs</h1>
	 <p>Sie sehen hier die Einträge des Topmenüs. Klicken Sie auf einen Eintrag um ihn zu bearbeiten.</p>
		<p>Um einen neuen Eintrag zu erstellen, klicken Sie auf das grüne Plussymbol.</p>
		<h2>Die Einträge</h2>*/
$this->content->template['message_topmenu'] = 'nobr:<h1>Voci del Top Menu</h1>
	 <p>Qui è possibile vedere le voci del Top menu (menu superiore). Cliccare sulla voce che si desidera modificare.</p>
		<p>Per aggiungere una nuova voce, cliccare sul simbolo + verde.</p>
		<h2>Le voci</h2>';
// Texte für menutop
$this->content->template['message']['menutop']['neu_edit']['link_legend'] = 'Selezione Link';
$this->content->template['message']['menutop']['neu_edit']['link_select_legend'] = 'Quale link si deve usare?';
$this->content->template['message']['menutop']['neu_edit']['link_select_selection'] = 'Link da lista di selezione.';
$this->content->template['message']['menutop']['neu_edit']['link_select_text'] = 'Link da modulo di inserimento.';
$this->content->template['message']['menutop']['neu_edit']['link_extern'] = 'Apri Linkin nuova finestra.';

$this->content->template['message_stamm_documents_change_backup'] = 'on change of a document-file, create a backup on the server?';


/**
errors
*/
// wrong Email adress
$this->content->template['error_1']="Questo indirizzo email non è corretto. Probabilmente hai sbagliato nel digitare";


/**
errors by Jos
*/
// >>>ordner.html<<< (line 55)
$this->content->template['message_eintragen'] = 'Inserisci';


// >>>image.html<<< (line 76-same message of message 489)
//['message_489'] = 'nessuna';


// >>>video.html<<< (line 27-same message of message 489), (line 85)
//['message_489'] = 'nessuna';
$this->content->template['message_beschreibende'] = 'Dati descrittivi(Lingua'; 


// >>>upload.html<<< (line 74-same message of message 482) 
//['message_482']="Seleziona";


// >>>span.html<<< (line 87-same message of message 47) 
//['message_47']="( modifica )";


// >>>kategorie_form.html<<< (line 41-same message of message_eintragen) 
//['message_eintragen']



$this->content->template['message_config_uplaod_hide_praefix'] = 'Add no prefix on uploaded files';
$this->content->template['message_config_tiny_advimg_filelist'] = 'In TinyMCE on link-select show file-list with small symbols';
