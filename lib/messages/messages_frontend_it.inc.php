<?php
/**
Hier werden alle messages zentral eingebunden,
das wird später die Mehrsprachigkeit ermöglichen.
Alle Messages sind nummeriert und unterteilt in
normale messages und errors.

Here are all messages centraly includet. This is important for
different languages use.
To include another language, please use this file for your purpose.
*/

/**
normal messages
*/

/**

Nachrichten aus dem Skript heraus
*/

// mail send
$this->content->template['message_1']="L'email é stata inviata.";
// Text über dem Forum
$this->content->template['message_2']="Ecco la lista completa dei forum.";
// Die letzten zehn Einträge
$this->content->template['message_3']="Le ultime dieci registrazioni";
$this->content->template['message_3_1']="Le ultime dieci registrazioni";
$this->content->template['message_3_2']="Lista dei temi attuali";
// $neuer = "Scrivere una nuova registrazione?";
$this->content->template['message_4']="Scrivere un nuovo messaggio?";
// Kein Ergebniss
$this->content->template['message_5']="Nessun risultato trovato. Per favore precisare la richiesta, per una migliore ricerca.";
// Kein Zugriff auf Message
$this->content->template['message_6']="Non avete accesso a questo messaggio.";
// Sie müssen sich einloggen
$this->content->template['message_7']="Dovete eseguire il login per poter scrivere un articolo.";
//<p>Diese Nachricht existiert nicht. \n</body></html>\n
$this->content->template['message_8']="<p>Messaggio inesistente. \n</body></html>\n";
//<p>Dieses Forum existiert nicht, bitte wählen <a href=\"./forum.php\">Sie ein anderes aus.</a>.\n</div></body></html>\n
$this->content->template['message_9']="<p>Forum non esistente, selezionare <a href=\"./forum.php\">un forum differente, grazie.</a>.\n</div></body></html>\n";
//<p>Sie müssen einen Text eingeben!</p>
$this->content->template['message_10']="<p>Devi inserire un testo!</p>";
// Sie haben leider keine Rechte um einen Beitrag schreiben zu können.
$this->content->template['message_11']="Purtroppo non avete diritto a scrivere un contributo.";
// Ihre Daten wurden eingetragen und Sie sind jetzt eingeloggt.
$this->content->template['message_12']="<h1>I tuoi dati sono stati immessi. </h1><p>Tra poco dovresti ricevere una email. Seguire le istruzioni contenute nella email per attivare l'account.</p>";
//weitere Seiten
$this->content->template['message_73']="Una pagina indietro";
//weitere Seiten
$this->content->template['message_86']="Una pagina avanti";
/**index.html*/
//Dieser Link führt zur Startseite von
$this->content->template['message_2000']="Questo link porta alla pagina principale di";
//Den obigen Artikel versenden.
$this->content->template['message_2001']="Spedire l'articolo soprastante";
//Empfänger-Email:
$this->content->template['message_2002']="Email destinatario:";
// Ihre Email:
$this->content->template['message_2003']="Vostra Email:";
//Was möchten Sie gerne senden?
$this->content->template['message_2004']="Che cosa volete inviare?";
//Nur den Link
$this->content->template['message_2005']="Solo il Link";
//Den ganzen Text
$this->content->template['message_2006']="Tutto il testo";
//Kommentar
$this->content->template['message_2007']="Commento";
//Senden
$this->content->template['message_2008']="Invia";
//Direkt zum einloggen.
$this->content->template['message_2009']="Direttamente al login.";
//Autor:
$this->content->template['message_2010']="Autore: ";
//Dieser Artikel wurde bereits
$this->content->template['message_2011']="Questo articolo é stato visto ";
//Dieser Link öffnet den Artikel
$this->content->template['message_2012']="Questo link apre l'articolo";
//im selben Fenster.
$this->content->template['message_2013']="nella stessa finestra.";
//Optionen zu diesen Artikel:
$this->content->template['message_2014']="Opzioni per questo articolo:";
//Sie können diesen Artikel Versenden/Empfehlen
$this->content->template['message_2015']="Potete inviare/raccomandare questo articolo";
//DruckVersion dieses Artikels
$this->content->template['message_2016']="versione stampata di questo articolo";
//Kommentar von
$this->content->template['message_2017']="Commento di   ";
// Kommentar zu
$this->content->template['message_2018']="Commento su ";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2019']="Scrivi commento:";
//Thema:
$this->content->template['message_2020']="Tema:";
//Beitrag:
$this->content->template['message_2021']="Proprio articolo:";
//Eintragen
$this->content->template['message_2022']="Registrazione";
//Besucher seit 20.04.2004.
$this->content->template['message_2023']="Ospite ";
//Nach oben.
$this->content->template['message_2024']="In alto";
//mal angesehen.
$this->content->template['message_2025']=" volte.";

// forum.html

//Menü überspringen
$this->content->template['message_2026']="Salta menu";
//Login
$this->content->template['message_2027']="Log-in";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2028']="Per favore controllare le indicazioni";
//Username
$this->content->template['message_2029']="Nome utente";
//Passwort
$this->content->template['message_2030']="Password";
//einloggen
$this->content->template['message_2031']="Esegui Login";
//Registrierung.
$this->content->template['message_2032']="Registrazione.";
// Account bearbeiten
$this->content->template['message_2033']="Modifica Profilo";
//ausloggen
$this->content->template['message_2034']="Logging (Entra)";
//Suchbegriff hier eingeben
$this->content->template['message_2035']="Immettere qui nome ricerca";
//Im Forum
$this->content->template['message_2036']="Nel forum ";
//Finden
$this->content->template['message_2037']="Trova";
//Zurück zur Übersicht
$this->content->template['message_2038']="Torna all'indice";
//Die Resultate Ihrer Suche:
$this->content->template['message_2039']="Risultati della ricerca.";
//Hier finden Sie alle Foren auf
$this->content->template['message_2040']="Qui trovate tutti i forum";
//Forum
$this->content->template['message_2041']="Forum";

//forumthread.html

//Menü überspringen
$this->content->template['message_2042']="Salta menun";
//Login
$this->content->template['message_2043']="Log-in";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2044']="Per favore controllare le indicazioni";
//Username
$this->content->template['message_2045']="Nome utente";
//Passwort
$this->content->template['message_2046']="Password";
//einloggen
$this->content->template['message_2047']="Esegui Login";
//Account bearbeiten
$this->content->template['message_2048']="Modifica Account";
//ausloggen
$this->content->template['message_2049']="Logging (Entra)";
//zurück zur Übersicht
$this->content->template['message_2050']="Torna all'indice";
//Suchbegriff hier eingeben
$this->content->template['message_2051']="Immettere qui nome ricerca";
//Im Forum
$this->content->template['message_2052']="Nel Forum";
//Finden
$this->content->template['message_2053']="Trova";
//Eintrag ändern:
$this->content->template['message_2054']="Modifica la registrazione:";
//Hier wird der Eintrag geändert.
$this->content->template['message_2055']="Qui viene modificata la registrazione.";
//Autor
$this->content->template['message_2056']="Autore";
//Thema
$this->content->template['message_2057']="Tema";
//Eingabe und Formatierung der Inhalte:
$this->content->template['message_2058']="Indicazione dei contenuti:";
//Beitrag
$this->content->template['message_2059']="Proprio articolo";
//eintragen
$this->content->template['message_2060']="Iscrizione";
//Diesen Beitrag editieren
$this->content->template['message_2061']="Edita questo articolo";
//Hits
$this->content->template['message_2062']="Hits";
//Beitrag
$this->content->template['message_2063']="texttext";
//als Antwort auf:
$this->content->template['message_2064']="come risposta a:";
//schreiben?
$this->content->template['message_2065']="scrivere?";
//Hier kann eine Eintrag in das Forum gemacht werden
$this->content->template['message_2066']="contributo al forum";
//Registrierung.
$this->content->template['message_2067']="Registrazione.";

// guestbook.html

//Direkt zum einloggen.
$this->content->template['message_2068']="Direttamente al Log-In.";
//Das Gästebuch wurde bereits
$this->content->template['message_2069']="Il libro degli ospiti é stato";
//mal angesehen.
$this->content->template['message_2070']="giá consultato.";
//Dieser Link öffnet den Artikel
$this->content->template['message_2071']="Questo link apre l'articolo";
//im selben Fenster.
$this->content->template['message_2072']="nella stessa finestra.";
//Gästebucheintrag von
$this->content->template['message_2073']="Registrazione nel libro degli ospiti di ";
//Kommentar ins
$this->content->template['message_2074']="Commento in   ";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2075']="Scrivi commento:";
//Autor:
$this->content->template['message_2076']="Autore: ";
//Thema:
$this->content->template['message_2077']="Tema:  ";
// Beitrag:
$this->content->template['message_2078']=" Contributo:";
//eintragen
$this->content->template['message_2079']="Registrazione  ";
//Besucher seit 20.04.2004.
$this->content->template['message_2080']="Visitatori dal 20.04.2004.";
//Optionen zu diesen Artikel:
$this->content->template['message_2081']="Opzioni su questo articolo:";
//DruckVersion dieses Artikels
$this->content->template['message_2082']="versione stampata di questo articolo ";

// inhalt.php

//Direkt zum einloggen.
$this->content->template['message_2083']="Direttamente al Log-in.";
//Besucher.
$this->content->template['message_2084']="Ospite. ";

// kontakt.php

// <h2>Kontaktformular</h2> <p>Wenn Sie uns direkt erreichen wollen, können Sie das über unser Kontaktformular machen.</p> <p>Bitte füllen Sie alle Felder aus, damit wir Ihnen auch antworten können.</p>
$this->content->template['message_2085']=" <h2>Modulo di contatto</h2>
 <p>Se vuoi contattarci, è possibile utilizzare questo modulo di contatto</p>
 <p>Si prega di compilare tutti i campi, per darci la possibilità di rispondere.</p>";
//Kontakt zu
$this->content->template['message_2086']="contatto con";
//Name:
$this->content->template['message_2087']="Nome";
//Hier bitte Ihren Namen eingeben
$this->content->template['message_2088']="Per favore indicare qui il proprio nome";
//Ihre <span lang']="en">E-Mail</span>:
$this->content->template['message_2089']="Vostra <span lang=\"en\">E-Mail</span>:";
//Hier bitte Ihre E-Mail Adresse eingeben
$this->content->template['message_2090']="Per favore indicare qui il proprio indirizzo email";
//Ihre Nachricht:
$this->content->template['message_2091']="Vostro messaggio: ";
//Hier bitte Ihre Nachricht eingeben
$this->content->template['message_2092']="Per favore scrivete qui il vostro messaggio";
//- Nachricht -
$this->content->template['message_2093']="- Messaggio -";
//<h2>Ihre Nachricht wurde übermittelt.</h2><p>Vielen Dank für Ihre Interesse, wir werden uns so bald wie möglich mit Ihnen in Verbindung setzen.</p><p>Sie können nun über das Menü links fortfahren.</p>
$this->content->template['message_2094']="<h2>Il tuo messaggio è stato inviato.</h2>
<p>Ti ringraziamo per il tuo interesse, ti risponderemo. se necessario, il prima possibile.</p>
<p>Continua la navigazione cliccando sui link dei Menù.</p>  ";

// print.php

//Autor:
$this->content->template['message_2095']="Autore:";
//Kommentar von
$this->content->template['message_2096']="commento di ";

// profil.html

//<h2>Login</h2>    <p>Sie können hier einen Account für sich erstellen. Sämtliche Daten die in die Datenbank eingetragen werden, werden Ihnen auch per Email zugestellt.</p>
$this->content->template['message_2097']="<h2>Registrazione</h2>
    <p>Qui puoi creare il tuo account. Tutti i dati che immetterai, saranno inviati alla tua email.</p><p>I campi marcati con un asterisco * sono obbligatori.</p><p>In linea di massima nome utente, password ed indirizzo email sono i dati essenziali.</p> <p>Per maggiori informazioni sulla <a href=\"#\">Privacy</a></p>";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2098']="Qui potete immettere i dati per il vostro conto.";
//Username:
$this->content->template['message_2099']="Nome utente: ";
// Emailadresse:
$this->content->template['message_2100']="Indirizzo Email:";
//Passwort:
$this->content->template['message_2101']="Password:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Password (controllo):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']="Volete ricevere una email quando é stata data una risposta al vostro contributo nel forum?";
//Antwortmail?
$this->content->template['message_2104']="Email di risposta?";
// erstellen
$this->content->template['message_2105']=" Crea";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Qui potete modificare i vostri dati ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Qui potete immettere i dati per il vostro conto. ";
//Username:
$this->content->template['message_2108']="Nome utente:";
// Emailadresse:
$this->content->template['message_2109']=" Indirizzo Email:";
//Neues Passwort:
$this->content->template['message_2110']="Nuova password:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" Volete ricevere una email quando é stata data una risposta al vostro contributo nel forum?";
// Antwortmail?
$this->content->template['message_2112']=" Email di risposta? ";
//bearbeiten
$this->content->template['message_2113']="modifica ";

// weiter.html

//Dieser Link führt eine Seite zurück
$this->content->template['message_2114']="Questo link vi porta alla pagina precedente";
//Die aktuell angezeigte Seite
$this->content->template['message_2115']="Pagina attualmente visualizzata";
//Dieser Link führt zur
$this->content->template['message_2116']="Questo link porta a";
//Seite
$this->content->template['message_2117']="pagina";
//Eine Seite weiter
$this->content->template['message_2118']="una pagina avanti";
//Dieser Link führt eine Seite weiter
$this->content->template['message_2119']="Questo link porta una pagina avanti";

//Hilfsmenü

//direkt zum Inhalt
$this->content->template['message_2120']="direttamente al contenuto";
//zur Bereichsnavigation
$this->content->template['message_2121']="alla navigazione nei settori";
// direkt zur Suche
$this->content->template['message_2122']=" direttamente alla ricerca ";
//direkt zum einloggen
$this->content->template['message_2123']="direttamente al Log-in";
//Frontend
$this->content->template['message_2124']="Frontend";
//ausloggen
$this->content->template['message_2125']="Esegui Log-out";

//rightcollum.html

//ausprobieren
$this->content->template['message_2126']="Prova";
//Login
$this->content->template['message_2127']="Login";
//Einloggen
$this->content->template['message_2128']="Login";
//Username
$this->content->template['message_2129']="Nome utente";
//Passwort
$this->content->template['message_2130']="Password";
//einloggen
$this->content->template['message_2131']="Accedi";
//Registrierung.
$this->content->template['message_2132']="Registrazione.";
//Account bearbeiten
$this->content->template['message_2133']="Modifica Account";
//ausloggen
$this->content->template['message_2134']="Logout (esci)";
//Suche
$this->content->template['message_2135']="Cerca nel sito";
//Suchbegriff hier eingeben
$this->content->template['message_2136']="Immettere qui nome ricerca";
//eingeben
$this->content->template['message_2137']="immetti";
//Finden
$this->content->template['message_2138']="Ricerca";
//Styleswitcher
$this->content->template['message_2139']="Cambia Stile";
//wählen
$this->content->template['message_2140']="seleziona";


//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2141']="Controllare i dati!";
//am
$this->content->template['message_2142']="il";
//Bitte einen Suchbegriff eingeben.
$this->content->template['message_2143']="Per favore immettere nome ricerca.";
//mehr über
$this->content->template['message_2144']=" ";
//Es wurde kein Eintrag gefunden
$this->content->template['message_2145']="Non é stata trovata nessuna informazione";
//Links in diesem text
$this->content->template['message_2146']="Links in questo testo";
//Akkürzungen in diesem Text:
$this->content->template['message_2147']="Abbreviazioni in questo testo:";
//Inhaltsverzeichnis
$this->content->template['message_2148']="Indice dei contenuti";
//Impressum
$this->content->template['message_2149']="Impressum";
//Kontakt
$this->content->template['message_2150']="Contatto";
//Hilfe
$this->content->template['message_2151']="Aiuto";

$this->content->template['message_2152']="Copiare per me?";
//
$this->content->template['message_2153']="Autorizzazione non disponibile!";
//
$this->content->template['message_2154']="Riprova.";
//
$this->content->template['message_2155']="";
//Ihr Account wurde nach 4 falschen Login versuchen für 10 Minuten gesperrt!
$this->content->template['message_2156']="Dopo 4 tentativi errati di Login l'account é bloccato per 10 minuti!";
//
$this->content->template['message_2157']="L'articolo é stato spedito.";
//
$this->content->template['message_2158']="Newsletter";
//
$this->content->template['message_2159']="Volete ricevere la Newsletter?";
//
$this->content->template['message_passwort_vergessen']="Ricorda Password";

/**
Neu ab 14.03.2006
*/

//
$this->content->template['Schriftart']="Famiglia Font";
//
$this->content->template['Schriftfarbe']="Colore Font";
//
$this->content->template['auswaehlen']="Seleziona";
//
$this->content->template['ueberschriften']="Intestazione";
//
$this->content->template['ueberschrift']="Intestazione";
//
$this->content->template['Auszeichnungen']="Descrizione";
//
$this->content->template['abk_von']="Abbreviazione di";
//
$this->content->template['acr_von']="Acronimo di";
//
$this->content->template['zit_von']="Citazione di";

//
$this->content->template['message_2170']="Tipo Intestazione: hai 6 possibilità.";
//
$this->content->template['message_2171']="Ulteriori Tag logici";
//
$this->content->template['sprung_menu']="Jump Menu";
//
$this->content->template['spez_seiten']="Siti Speciali";
//
$this->content->template['message_2174']="Nome Utente";
//
$this->content->template['message_2175']="Password";
//
$this->content->template['mensch']="Sei un essere umano?";
//
$this->content->template['message_2177']="Per ragioni di sicurezza questo modulo è protetto da Spamshield.";
//
$this->content->template['message_2178']="Se si desidera inviare, è necessario digitare il numero sulla immagine.";
//
$this->content->template['message_2179']="Codice di sicurezza per l'invio del Modulo";
//
$this->content->template['message_2180']="Codice";
//
$this->content->template['message_2181']="Per inviare questo modulo è necessario risolvere il seguente calcolo:";
//
$this->content->template['message_2182']="Devi ordinare i segni nell'ordine corretto. ad es.:<br />
				2. Sign: x; 1. Sign: 4; 3. Sign: s; significa &quot;4xs&quot;
			</p>";
//
$this->content->template['message_2183']="Tu sei qui";
//
$this->content->template['message_2184']="Menu";
//
$this->content->template['message_2185']="Seleziona Lingua";
//
$this->content->template['suche']="Cerca";
//
$this->content->template['message_2187']="Colonna ottica di destra";
//
$this->content->template['message_2188']="Board";
//
$this->content->template['message_2189']="Board";
//
$this->content->template['message_2190']="Ultimo messaggio";
//
$this->content->template['message_2191']="Temi";
//
$this->content->template['message_2192']="Messaggio";
//
$this->content->template['message_2193']="Guestbook";
//
$this->content->template['message_2194']="Contentuto principale";
//
$this->content->template['message_2195']="Edita questo Articolo";
//
$this->content->template['message_2196']="Indietro";
//
$this->content->template['message_2197']="Mappa del sito";
//
$this->content->template['message_2198']="Modulo di contatto";
//
$this->content->template['message_2199']="Modifica dati utenti";
//
$this->content->template['message_2200']="Il tuo Profilo";
//
$this->content->template['message_2201']="Il tuo Profilo è attivo e puoi modificare i dati qui.";
//
$this->content->template['message_2202']="I dati del tuo Profilo";
//
$this->content->template['message_2203']="Dati per Login (Ingresso)";
//
$this->content->template['message_2204']="Nome Utente";
//
$this->content->template['message_2205']="dati personali";
//
$this->content->template['message_2206']="Nome";
//
$this->content->template['message_2207']="Cognome";
//
$this->content->template['message_2208']="Indirizzo";
//
$this->content->template['message_2209']="codice postale";
//
$this->content->template['message_2210']="Città";
//
$this->content->template['message_2211']="Board";
//
$this->content->template['message_2212']="Che tipo di Board ti piace?";
//
$this->content->template['message_2213']="Vedi il Thread";
//
$this->content->template['message_2214']="Vedi il Board";
//
$this->content->template['message_2215']="Thread + Vista";
//
$this->content->template['message_2216']="Più opzioni";
//
$this->content->template['message_2217']="Vuoi rimanere loggato?";
//
$this->content->template['message_2218']="Stay loged in?";
//
$this->content->template['message_2219']="Seleziona il tuo stile personale.";
//
$this->content->template['message_2220']="Modifica Profilo";
//
$this->content->template['message_2221']="Salvare i Dati?";
//
$this->content->template['message_2222']="salva";
//
$this->content->template['message_2223']="Eliminare Account";
//
$this->content->template['message_2224']="Vuoi eliminare questo Account?";
//
$this->content->template['message_2225']="elimina";
//
$this->content->template['message_2226']="via e numero";
//
$this->content->template['message_2227']="I dati.";
//
$this->content->template['reservierung']="riserve";
//
$this->content->template['message_2229']="Giorno";
//
$this->content->template['message_2230']="Mese";
//
$this->content->template['message_2231']="Notte";
//
$this->content->template['message_2232']="Stanza singola";
//
$this->content->template['message_2233']="Stanza doppia";
//
$this->content->template['message_2234']="controlla";
//
$this->content->template['message_2235']="Il tuo Account è attivato!";
//
$this->content->template['message_2236']="texttext";
//
$this->content->template['message_2237']="Breadcrump Menu";
//
$this->content->template['message_2238']="Headtext";
//
$this->content->template['message_2239']="bbcode Editor Picture";
//
$this->content->template['message_2240']="bbcode Editor";
//
$this->content->template['message_2241']="Modulo di Login";
//
$this->content->template['message_2242']="Protezione Spam";
//
$this->content->template['message_2243']="Password dimenticata";
//
$this->content->template['message_2244']="Avanti";
//
$this->content->template['message_2245']="Terza colonna";
//
$this->content->template['message_2246']="Login";
//
$this->content->template['message_2247']="Menu Livello 0";
//
$this->content->template['message_2248']="Menu Livello 1";
//
$this->content->template['message_2249']="Menu Livello 2";
//
$this->content->template['message_2250']="Menu Livello 3";
//
$this->content->template['message_2251']="Menu Livello Links";
//
$this->content->template['message_2252']="News";
//
$this->content->template['message_2253']="Calce";
//
$this->content->template['message_2254']="Contenuto sinistra";
//
$this->content->template['message_2255']="Contenuto Centro";
//
$this->content->template['message_2256']="Contenuto Destra";

$this->content->template['message_2257']="Riempire tutti i campi, grazie.";
//Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein
$this->content->template['message_2258']="Se hai dimenticato la password, inserisci il tuo indirizzo e-mail o il tuo nome utente qui.";
//
$this->content->template['message_2259']="Se hai forgoten è la password, inserisci il tuo indirizzo e-mail o il tuo nome utente qui.";
//
$this->content->template['message_2260']="La tua password è stata inviata, dovresti ricevere una email con la nuova password in pochi minuti.";
//
$this->content->template['message_2261']="Crea una nuova password.";
//
$this->content->template['message_2262']="Rinnova la password";


//
$this->content->template['message_2263']="Melden Sie sich jetzt bitte in der nebenstehenden Anmeldemaske an, dann können Sie Ihren Account nutzen.";
// &nbsp;
$this->content->template['nbs']="&nbsp;";

// Texte für Downloads
// *******************
$this->content->template['download']['kein_recht'] = "Non sei autorizzato a scaricare questo file.";
$this->content->template['download']['link_title'] = "Il Download partirà in una nuova finestra.";
$this->content->template['download']['info_01'] = "Peso";
$this->content->template['download']['info_02'] = "Numero download";
$this->content->template['download']['info_03'] = "Ultimo download alle";
$this->content->template['download']['keine_datei'] = "Spiacenti, file inesistente.";



$this->content->template['mod_access1'] = 'Accesso tramite tastiera';
//Zugangstasten des Accesskey-Pad und deren Funktion
$this->content->template['mod_access2'] = 'Chiavi di accesso alle funzioni avanzate della tastiera';
//Tasten
$this->content->template['mod_access3'] = 'Tasti';
//Funktion
$this->content->template['mod_access4'] = 'Funzione';
//Alt+0
$this->content->template['mod_access5'] = 'Alt+0';
//Startseite
$this->content->template['mod_access6'] = 'Home page';
//Alt+3
$this->content->template['mod_access7'] = 'Alt+3';
//Vorherige Seite
$this->content->template['mod_access8'] = 'Pagina precedente';
//Alt+6
$this->content->template['mod_access9'] = 'Alt+6';
//Inhaltsverzeichnis
$this->content->template['mod_access10'] = 'Indice';
//Alt+7
$this->content->template['mod_access11'] = 'Alt+7';
//Suchfunktion
$this->content->template['mod_access12'] = 'Funzione ricerca';
//Alt+8
$this->content->template['mod_access13'] = 'Alt+8';
//Direkt zum Inhalt
$this->content->template['mod_access14'] = 'vai al contenuto';
//Alt+9
$this->content->template['mod_access15'] = 'Alt+9';
//Kontaktseite
$this->content->template['mod_access16'] = 'pagina di contatto';
//Sitemap

$this->content->template['mod_access17'] = 'Mappa del sito';


// Texte fÃ¼r Modul "mod-efa_fontsize"
$this->content->template['mod_efa_fontsize']['text'] = "Grandezza Font:";
$this->content->template['mod_efa_fontsize']['bigger'] = "ingrandisci Font";
$this->content->template['mod_efa_fontsize']['normal'] = "Font normale";
$this->content->template['mod_efa_fontsize']['smaller'] = "Font più piccola";

// Texte für die lokalisierung von LightBox
$this->content->template['lightbox']['text_1a'] = "premi il tasto";
$this->content->template['lightbox']['text_1b'] = "clicca sull'immagine";
$this->content->template['lightbox']['text_2'] = "per chiudere la finestra.";

//Kontaktformular der Seite
$this->content->template['contact']['mail1'] = "Modulo di contatto del sito ";


//Suche
$this->content->template['templ_serg'] = "Risultati della Ricerca";
$this->content->template['templ_ergbn'] = "risultato della tua ricerca per ";
$this->content->template['templ_res'] = "Risultati";
$this->content->template['templ_bis'] = ", max.";
$this->content->template['templ_insg'] = "totale";
// Plural
$this->content->template['templ_seiten'] = "Pagine";
// Singular
$this->content->template['templ_seite'] = "Pagina";
$this->content->template['templ_Stand'] = "Creato il";
$this->content->template['templ_erst'] = "Aggiornato il";
$this->content->template['templ_Erweiterte'] = "Ricerca avanzata - ";
$this->content->template['templ_susst'] = "Inizia la Ricerca";
$this->content->template['templ_Suchtyp'] = "Tipo Ricerca";
$this->content->template['templ_finein'] = "Trova uno qualsiasi dei termini";
$this->content->template['templ_oder'] = "Una delle parole&nbsp;&nbsp;";
$this->content->template['templ_findall'] = "Trova tutti i termini";
$this->content->template['templ_und'] = "Tutte le parole&nbsp;&nbsp;";
$this->content->template['templ_findd'] = "Trova questo termine";
$this->content->template['templ_Exakt'] = "Frase esatta";
$this->content->template['templ_anergb1'] = "Numero di risultati per pagina (occorrenze)";
$this->content->template['templ_sort'] = "Criterio Ordine";
$this->content->template['templ_z10'] = "Visualizza 10 risultati per pagina";
$this->content->template['templ_z20'] = "Visualizza 20 risultati per pagina";
$this->content->template['templ_z30'] = "Visualizza 30 risultati per pagina";
$this->content->template['templ_z100'] = "Visualizza 100 risultati per pagina";
$this->content->template['templ_all100'] = "Tutti (max. 100)";
$this->content->template['templ_sortti'] = "Ordina i risultati con il titolo dell'articolo";
$this->content->template['templ_Titel'] = "Titolo";
$this->content->template['templ_sortart'] = "Ordina i risultati con il titolo dell'articolo";
$this->content->template['templ_datl'] = "Data ultimo aggiornamento";
$this->content->template['templ_aufst'] = "Ordina i risultati in ordine ascendente";
$this->content->template['templ_ergba'] = "Ordina i risultati in ordine discendente";
// new message by Josef -
$this->content->template['templ_asc'] = "Ascendente";
///
$this->content->template['templ_Absteigend'] = "Discendente";
$this->content->template['tplarticle_coment'] = "Commenti";
$this->content->template['tplforum_forum'] = "Forum";
$this->content->template['tplarticle_schreiben'] = "scrivere?";
$this->content->template['tplarticle_mail1'] = "";
/*
//
$this->content->template['message_2264']="texttext";
//
$this->content->template['message_2265']="texttext";
//
$this->content->template['message_2266']="texttext";
//
$this->content->template['message_2267']="texttext";
//
$this->content->template['message_2268']="texttext";
//
$this->content->template['message_2269']="texttext";
//
$this->content->template['message_2270']="texttext";
//
$this->content->template['message_2271']="texttext";
//
$this->content->template['message_2272']="texttext";
//
$this->content->template['message_2273']="texttext";
//
$this->content->template['message_2274']="texttext";
//
$this->content->template['message_2275']="texttext";
//
$this->content->template['message_2276']="texttext";
//
$this->content->template['message_2277']="texttext";
//
$this->content->template['message_2278']="texttext";
//
$this->content->template['message_2279']="texttext";
//
$this->content->template['message_2280']="texttext";
//
$this->content->template['message_2281']="texttext";
//
$this->content->template['message_2282']="texttext";
//
$this->content->template['message_2283']="texttext";
//
$this->content->template['message_2284']="texttext";
//
$this->content->template['message_2285']="texttext";
//
$this->content->template['message_2286']="texttext";
//
$this->content->template['message_2287']="texttext";
//
$this->content->template['message_2288']="texttext";
//
$this->content->template['message_2289']="texttext";
//
$this->content->template['message_2290']="texttext";
//
$this->content->template['message_2291']="texttext";
//
$this->content->template['message_2292']="texttext";
//
$this->content->template['message_2293']="texttext";
//
$this->content->template['message_2294']="texttext";
//
$this->content->template['message_2295']="texttext";
//
$this->content->template['message_2296']="texttext";
//
$this->content->template['message_2297']="texttext";
//
$this->content->template['message_2298']="texttext";
//
$this->content->template['message_2299']="texttext";
//
$this->content->template['message_2300']="texttext";
//
$this->content->template['message_2301']="texttext";
//
$this->content->template['message_2302']="texttext";
//
$this->content->template['message_2303']="texttext";
//
$this->content->template['message_2304']="texttext";
//
$this->content->template['message_2305']="texttext";
//
$this->content->template['message_2306']="texttext";
//
$this->content->template['message_2307']="texttext";
//
$this->content->template['message_2308']="texttext";
//
$this->content->template['message_2309']="texttext";
//
$this->content->template['message_2310']="texttext";
//
$this->content->template['message_2311']="texttext";
//
$this->content->template['message_2312']="texttext";
//
$this->content->template['message_2313']="texttext";
//
$this->content->template['message_2314']="texttext";
//
$this->content->template['message_2315']="texttext";
//
$this->content->template['message_2316']="texttext";
//
$this->content->template['message_2317']="texttext";
//
$this->content->template['message_2318']="texttext";
//
$this->content->template['message_2319']="texttext";
//
$this->content->template['message_2320']="texttext";
//
$this->content->template['message_2321']="texttext";
//
$this->content->template['message_2322']="texttext";
//
$this->content->template['message_2323']="texttext";
//
$this->content->template['message_2324']="texttext";
//
$this->content->template['message_2325']="texttext";
//
$this->content->template['message_2326']="texttext";
//
$this->content->template['message_2327']="texttext";
//
$this->content->template['message_2328']="texttext";
//
$this->content->template['message_2329']="texttext";
//
$this->content->template['message_2330']="texttext";
//
$this->content->template['message_2331']="texttext";
//
$this->content->template['message_2332']="texttext";
//
$this->content->template['message_2333']="texttext";
//
$this->content->template['message_2334']="texttext";
//
$this->content->template['message_2335']="texttext";
//
$this->content->template['message_2336']="texttext";
//
$this->content->template['message_2337']="texttext";
//
$this->content->template['message_2338']="texttext";
//
$this->content->template['message_2339']="texttext";
//
$this->content->template['message_2340']="texttext";
//
$this->content->template['message_2341']="texttext";
//
$this->content->template['message_2342']="texttext";
//
$this->content->template['message_2343']="texttext";
//
$this->content->template['message_2344']="texttext";
//
$this->content->template['message_2345']="texttext";
//
$this->content->template['message_2346']="texttext";
//
$this->content->template['message_2347']="texttext";
//
$this->content->template['message_2348']="texttext";
//
$this->content->template['message_2349']="texttext";
//
$this->content->template['message_2350']="texttext";
//
$this->content->template['message_2351']="texttext";
//
$this->content->template['message_2352']="texttext";
//
$this->content->template['message_2353']="texttext";
//
$this->content->template['message_2354']="texttext";
//
$this->content->template['message_2355']="texttext";
//
$this->content->template['message_2356']="texttext";
//
$this->content->template['message_2357']="texttext";
//
$this->content->template['message_2358']="texttext";
//
$this->content->template['message_2359']="texttext";
//
$this->content->template['message_2360']="texttext";
//
$this->content->template['message_2361']="texttext";
//
$this->content->template['message_2362']="texttext";
//
$this->content->template['message_2363']="texttext";
//
$this->content->template['message_2364']="texttext";
//
$this->content->template['message_2365']="texttext";
//
$this->content->template['message_2366']="texttext";
//
$this->content->template['message_2367']="texttext";
//
$this->content->template['message_2368']="texttext";
//
$this->content->template['message_2369']="texttext";
//
$this->content->template['message_2370']="texttext";
//
$this->content->template['message_2371']="texttext";
//
$this->content->template['message_2372']="texttext";
//
$this->content->template['message_2373']="texttext";
//
$this->content->template['message_2374']="texttext";
//
$this->content->template['message_2375']="texttext";
//
$this->content->template['message_2376']="texttext";
//
$this->content->template['message_2377']="texttext";
//
$this->content->template['message_2378']="texttext";
//
$this->content->template['message_2379']="texttext";
//
$this->content->template['message_2380']="texttext";
//
$this->content->template['message_2381']="texttext";
//
$this->content->template['message_2382']="texttext";
//
$this->content->template['message_2383']="texttext";
//
$this->content->template['message_2384']="texttext";
//
$this->content->template['message_2385']="texttext";
//
$this->content->template['message_2386']="texttext";
//
$this->content->template['message_2387']="texttext";
//
$this->content->template['message_2388']="texttext";
//
$this->content->template['message_2389']="texttext";
//
$this->content->template['message_2390']="texttext";
//
$this->content->template['message_2391']="texttext";
//
$this->content->template['message_2392']="texttext";
//
$this->content->template['message_2393']="texttext";
//
$this->content->template['message_2394']="texttext";
//
$this->content->template['message_2395']="texttext";
//
$this->content->template['message_2396']="texttext";
//
$this->content->template['message_2397']="texttext";
//
$this->content->template['message_2398']="texttext";
//
$this->content->template['message_2399']="texttext";
//
$this->content->template['message_2400']="texttext";
//
$this->content->template['message_2401']="texttext";
//
$this->content->template['message_2402']="texttext";
//
$this->content->template['message_2403']="texttext";
//
$this->content->template['message_2404']="texttext";
//
$this->content->template['message_2405']="texttext";
//
$this->content->template['message_2406']="texttext";
//
$this->content->template['message_2407']="texttext";
//
$this->content->template['message_2408']="texttext";
//
$this->content->template['message_2409']="texttext";
//
$this->content->template['message_2410']="texttext";
//
$this->content->template['message_2411']="texttext";
//
$this->content->template['message_2412']="texttext";
//
$this->content->template['message_2413']="texttext";
//
$this->content->template['message_2414']="texttext";
//
$this->content->template['message_2415']="texttext";
//
$this->content->template['message_2416']="texttext";
//
$this->content->template['message_2417']="texttext";
//
$this->content->template['message_2418']="texttext";
//
$this->content->template['message_2419']="texttext";
//
$this->content->template['message_2420']="texttext";
//
$this->content->template['message_2421']="texttext";
//
$this->content->template['message_2422']="texttext";
//
$this->content->template['message_2423']="texttext";
//
$this->content->template['message_2424']="texttext";
//
$this->content->template['message_2425']="texttext";
//
$this->content->template['message_2426']="texttext";
//
$this->content->template['message_2427']="texttext";
//
$this->content->template['message_2428']="texttext";
//
$this->content->template['message_2429']="texttext";
//
$this->content->template['message_2430']="texttext";
//
$this->content->template['message_2431']="texttext";
//
$this->content->template['message_2432']="texttext";
//
$this->content->template['message_2433']="texttext";
//
$this->content->template['message_2434']="texttext";
//
$this->content->template['message_2435']="texttext";
//
$this->content->template['message_2436']="texttext";
//
$this->content->template['message_2437']="texttext";
//
$this->content->template['message_2438']="texttext";
//
$this->content->template['message_2439']="texttext";
//
$this->content->template['message_2440']="texttext";
//
$this->content->template['message_2441']="texttext";
//
$this->content->template['message_2442']="texttext";
//
$this->content->template['message_2443']="texttext";
//
$this->content->template['message_2444']="texttext";
//
$this->content->template['message_2445']="texttext";
//
$this->content->template['message_2446']="texttext";
//
$this->content->template['message_2447']="texttext";
//
$this->content->template['message_2448']="texttext";
//
$this->content->template['message_2449']="texttext";
//
$this->content->template['message_2450']="texttext";
//
$this->content->template['message_2451']="texttext";
//
$this->content->template['message_2452']="texttext";
//
$this->content->template['message_2453']="texttext";
//
$this->content->template['message_2454']="texttext";
//
$this->content->template['message_2455']="texttext";
//
$this->content->template['message_2456']="texttext";
//
$this->content->template['message_2457']="texttext";
//
$this->content->template['message_2458']="texttext";
//
$this->content->template['message_2459']="texttext";
//
$this->content->template['message_2460']="texttext";
//
$this->content->template['message_2461']="texttext";
//
$this->content->template['message_2462']="texttext";
//
$this->content->template['message_2463']="texttext";
//
$this->content->template['message_2464']="texttext";
//
$this->content->template['message_2465']="texttext";
//
$this->content->template['message_2466']="texttext";
//
$this->content->template['message_2467']="texttext";
//
$this->content->template['message_2468']="texttext";
//
$this->content->template['message_2469']="texttext";
//
$this->content->template['message_2470']="texttext";
//
$this->content->template['message_2471']="texttext";
//
$this->content->template['message_2472']="texttext";
//
$this->content->template['message_2473']="texttext";
//
$this->content->template['message_2474']="texttext";
//
$this->content->template['message_2475']="texttext";
//
$this->content->template['message_2476']="texttext";
//
$this->content->template['message_2477']="texttext";
//
$this->content->template['message_2478']="texttext";
//
$this->content->template['message_2479']="texttext";
//
$this->content->template['message_2480']="texttext";
//
$this->content->template['message_2481']="texttext";
//
$this->content->template['message_2482']="texttext";
//
$this->content->template['message_2483']="texttext";
//
$this->content->template['message_2484']="texttext";
//
$this->content->template['message_2485']="texttext";
//
$this->content->template['message_2486']="texttext";
//
$this->content->template['message_2487']="texttext";
//
$this->content->template['message_2488']="texttext";
//
$this->content->template['message_2489']="texttext";
//
$this->content->template['message_2490']="texttext";
//
$this->content->template['message_2491']="texttext";
//
$this->content->template['message_2492']="texttext";
//
$this->content->template['message_2493']="texttext";
//
$this->content->template['message_2494']="texttext";
//
$this->content->template['message_2495']="texttext";
//
$this->content->template['message_2496']="texttext";
//
$this->content->template['message_2497']="texttext";
//
$this->content->template['message_2498']="texttext";
//
$this->content->template['message_2499']="texttext";
//
*/
//
// <<<<<<<<<<<>>>>>>>>>>>>>>>>
//
//error - not translated - message new - by Josef
//
// error in profil.html
$this->content->template['message_2500']="Firma ";
$this->content->template['message_2501']="Firma per il Forum";
// error in mod_bbcode_editor.html
$this->content->template['message_2502']="Qui puoi selezionare il colore del testo";
$this->content->template['message_2503']="Qui puoi selezionare il tipo di font";
$this->content->template['message_2504']="rosso";
$this->content->template['message_2505']="verde";
$this->content->template['message_2506']="blu";
$this->content->template['message_2507']="personale";
$this->content->template['fett']="Grassetto";
$this->content->template['kursiv']="Corsivo";
$this->content->template['Link_eingeben']="Link";
$this->content->template['Externes_Bild_eingeben']="Immagine";
$this->content->template['geordnetete_Liste']="Lista numerata";
// error in Kopie von user_class.php
$this->content->template['andern']="Modifica";
$this->content->template['pass1falsch'] = "<strong style=\"color:red\";>ndern</strong>";
//
//end new by Josef
//
//<<<<<<<<<<<<<<<<<<<<>>>>>>>>>>>>>>>>>>>



/**
errors
*/
// wrong Email adress
$this->content->template['error_1']="Questo indirizzo Email é purtroppo errato. Forse avete commesso un errore di battitura";