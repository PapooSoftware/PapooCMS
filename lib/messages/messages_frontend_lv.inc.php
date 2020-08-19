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
$this->content->template['message_1']="E-pasts ir nosūtīts.";
// Text über dem Forum
$this->content->template['message_2']="Šeit atrodami visi forumi.";
// Die letzten zehn Einträge
$this->content->template['message_3']="Pēdējās 10 ziņas";
$this->content->template['message_3_1']="Pēdējās 10 ziņas";
$this->content->template['message_3_2']="Pēdējo tēmu saraksts";
// $neuer = "Einen neuen Beitrag schreiben?";
$this->content->template['message_4']="Rakstīt jaunu ziņu?";
// Kein Ergebniss
$this->content->template['message_5']="Atvainojiet, nkas netika atrasts. Lūdzu izmainiet meklēšanas parametrus.";
// Kein Zugriff auf Message
$this->content->template['message_6']="Ziņas Jums nav pieejamas.";
// Sie müssen sich einlogen
$this->content->template['message_7']="Jums ir jāpieslēdzas, lai uzrakstītu ziņu.";
//<p>Diese Nachricht existiert nicht. \n</body></html>\n
$this->content->template['message_8']="<p>Šī ziņa neeksistē. \n</body></html>\n";
//<p>Dieses Forum existiert nicht, bitte wählen <a href=\"./forum.php\">Sie ein anderes aus.</a>.\n</div></body></html>\n
$this->content->template['message_9']="<p>Šis forums neeksistē, lūdzu izvēlieties citu <a href=\"./forum.php\">a different forum.</a>.\n</div></body></html>\n";
//<p>Sie müssen einen Text eingeben!</p>
$this->content->template['message_10']="<p>Jums jāievada teksts!</p>";
// Sie haben leider keine Rechte um einen Beitrag schreiben zu k&ouml;nnen.
$this->content->template['message_11']="Jums nav atļauts rakstīt ziņu.";
// Ihre Daten wurden eingetragen und Sie sind jetzt eingelogt.
$this->content->template['message_12 ']="<h1>Jūsu dati tika iesniegti un Jūs varat pieslēgties.</h1>";
$this->content->template['message_73']="Iepriekšējā lapa";
//weitere Seiten
$this->content->template['message_86']="Nākamā lapa";
/**index.html*/
//Dieser Link führt zur Startseite von
$this->content->template['message_2000']="Uz mājaslapu.";
//Den obigen Artikel versenden.
$this->content->template['message_2001']="Nosūtīt šo rakstu.";
//Empfänger-Email:
$this->content->template['message_2002']="Saņēmēja e-pasts:";
// Ihre Email:
$this->content->template['message_2003']="Jūsu e-pasts:";
//Was möchten Sie gerne senden?
$this->content->template['message_2004']="Ko Jūs vēlaties nosūtīt?";
//Nur den Link
$this->content->template['message_2005']="Tikai saiti";
//Den ganzen Text
$this->content->template['message_2006']="Visu tekstu";
//Kommentar
$this->content->template['message_2007']="Komentārs";
//Senden
$this->content->template['message_2008']="Sūtīt";
//Direkt zum einlogen.
$this->content->template['message_2009']="Pieslēgties.";
//Autor:
$this->content->template['message_2010']="Autors:";
//Dieser Artikel wurde bereits
$this->content->template['message_2011']="Šis raksts";
//Dieser Link öffnet den Artikel
$this->content->template['message_2012']="Šī saite atver šo rastu:";
//im selben Fenster.
$this->content->template['message_2013']="šajā pašā logā.";
//Optionen zu diesen Artikel:
$this->content->template['message_2014']="Iespējas:";
//Sie können diesen Artikel Versenden/Empfehlen
$this->content->template['message_2015']="Jūs varat nosutīt vai ieteikt šo rakstu";
//DruckVersion dieses Artikels
$this->content->template['message_2016']="Drukāt šo rakstu";
//Kommentar von
$this->content->template['message_2017']="Komentārs no";
// Kommentar zu
$this->content->template['message_2018']="Komentārs par";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2019']="Šeit varat ierakstīt savu komentāru.";
//Thema:
$this->content->template['message_2020']="Tēma:";
//Beitrag:
$this->content->template['message_2021']="Ieraksts:";
//Eintragen
$this->content->template['message_2022']="Ievadīt";
//Besucher seit 20.04.2004.
$this->content->template['message_2023']="Viesi";
//Nach oben.
$this->content->template['message_2024']="Uz augšu.";
//mal angesehen.
$this->content->template['message_2025']="apmeklējumu skaits.";

// forum.html

//Menü überspringen
$this->content->template['message_2026']="Doties uz rakstu(iem)";
//Login
$this->content->template['message_2027']="Pieslēgties";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2028']="Lūdzu pārbaudiet savus datus";
//Username
$this->content->template['message_2029']="Lietotājvārds";
//Passwort
$this->content->template['message_2030']="Parole";
//einlogen
$this->content->template['message_2031']="pieslēgties";
//Einen neuen Account anlegen.
$this->content->template['message_2032']="Izveidot jaunu lietotājkontu.";
// Account bearbeiten
$this->content->template['message_2033']=" Rediģēt lietotājkontu";
//auslogen
$this->content->template['message_2034']="atslēgties";
//Suchbegriff hier eingeben
$this->content->template['message_2035']="Ievadīt meklējamo vārdu";
//Im Forum
$this->content->template['message_2036']="Forumā";
//Finden
$this->content->template['message_2037']="Atrast";
//Zurück zur Übersicht
$this->content->template['message_2038']="Atpakaļ uz pārskatu";
//Die Resultate Ihrer Suche:
$this->content->template['message_2039']="Jūsu meklēšanas rezultāti:";
//Hier finden Sie alle Foren auf
$this->content->template['message_2040']="Šeit varat atrast visus forumus saistītus ar";
//Forum
$this->content->template['message_2041']="Forums";

//forumthread.html

//Menü überspringen
$this->content->template['message_2042']="doties uz rakstu";
//Login
$this->content->template['message_2043']="Pieslēgties";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2044']="Lūdzu pātbaudiet savus datus";
//Username
$this->content->template['message_2045']="Lietotājvārds";
//Passwort
$this->content->template['message_2046']="Parole";
//einlogen
$this->content->template['message_2047']="pieslēgties ";
//Account bearbeiten
$this->content->template['message_2048']="rediģēt savu lietotājkontu";
//auslogen
$this->content->template['message_2049']="atslēgties ";
//zurück zur Übersicht
$this->content->template['message_2050']="Atpakaļ uz pārskatu";
//Suchbegriff hier eingeben
$this->content->template['message_2051']="Ievadīt meklētājā";
//Im Forum
$this->content->template['message_2052']="Forumā";
//Finden
$this->content->template['message_2053']="Atrast";
//Eintrag ändern:
$this->content->template['message_2054']="rediģēt ierakstu:";
//Hier wird der Eintrag geändert.
$this->content->template['message_2055']="Šeit Jūs varat rediģēt savu ierakstu.";
//Autor
$this->content->template['message_2056']="Autors";
//Thema
$this->content->template['message_2057']="Tēma";
//Eingabe und Formatierung der Inhalte:
$this->content->template['message_2058']="Ierakstiet un formatējiet savu ierakstu šeit:";
//Beitrag
$this->content->template['message_2059']="Ziņa";
//eintragen
$this->content->template['message_2060']="ievadīt";
//Diesen Beitrag editieren
$this->content->template['message_2061']="Rediģēt ziņu";
//Hits
$this->content->template['message_2062']="trāpījumi";
//Beitrag
$this->content->template['message_2063']="ziņa";
//als Antwort auf:
$this->content->template['message_2064']="rakstīt kā atbildi uz:";
//schreiben?
$this->content->template['message_2065']="?";
//Hier kann eine Eintrag in das Forum gemacht werden
$this->content->template['message_2066']="Šeit varat rediģēt ziņu forumam";
//Einen neuen Account anlegen.
$this->content->template['message_2067']="Izveidot jaunu lietotājkontu.";

// guestbook.html

//Direkt zum einlogen.
$this->content->template['message_2068']="uz pieslēgšanos.";
//Das Gästebuch wurde bereits
$this->content->template['message_2069']="Viesu grāmata tikusi apmeklēta";
//mal angesehen.
$this->content->template['message_2070']="reizes.";
//Dieser Link öffnet den Artikel
$this->content->template['message_2071']="Atvērt rakstu";
//im selben Fenster.
$this->content->template['message_2072']="šajā logā.";
//Gästebucheintrag von
$this->content->template['message_2073']="Ievietojis";
//Kommentar ins
$this->content->template['message_2074']="Rakstīt komentāru";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2075']="Komentāru varat rakstīt šeit.";
//Autor:
$this->content->template['message_2076']="Autors:";
//Thema:
$this->content->template['message_2077']="Tēma:";
// Beitrag:
$this->content->template['message_2078']="Ziņojums:";
//eintragen
$this->content->template['message_2079']="ievadīt";
//Besucher seit 20.04.2004.
$this->content->template['message_2080']="Viesi:";
//Optionen zu diesen Artikel:
$this->content->template['message_2081']="Iespējas:";
//DruckVersion dieses Artikels
$this->content->template['message_2082']="Drukāt šo rakstu";

// inhalt.php

//Direkt zum einlogen.
$this->content->template['message_2083']="uz pieslēgšanos.";
//Besucher.
$this->content->template['message_2084']="Viesis.";

// kontakt.php

// <h2>Kontaktformular</h2> <p>Wenn Sie uns direkt erreichen wollen, k&ouml;nnen Sie das &uuml;ber unser Kontaktformular machen.</p> <p>Bitte f&uuml;llen Sie alle Felder aus, damit wir Ihnen auch antworten k&ouml;nnen.</p>
$this->content->template['message_2085']=" <h2>Saziņa ar mums</h2>
 <p>Ja vēlaties ar mums sazināties, izmantojiet šo formulāru.</p>
 <p>Aizpildiest visus nepieciešamos laukus.</p>";
//Kontakt zu
$this->content->template['message_2086']="Sazināties ar";
//Name:
$this->content->template['message_2087']="Vārds";
//Hier bitte Ihren Namen eingeben
$this->content->template['message_2088']="Ievadiet Jūsu vārdu";
//Ihre <span lang']="en">E-Mail</span>:
$this->content->template['message_2089']="Jūsu <span lang=\"en\">e-pasts</span>:";
//Hier bitte Ihre E-Mail Adresse eingeben
$this->content->template['message_2090']="Ievadiet Jūsu e-pasta adresi";
//Ihre Nachricht:
$this->content->template['message_2091']="Jūsu ziņa:";
//Hier bitte Ihre Nachricht eingeben
$this->content->template['message_2092']="Ievadīet Jūsu ziņu ";
//- Nachricht -
$this->content->template['message_2093']="- ziņa -";
//<h2>Ihre Nachricht wurde übermittelt.</h2><p>Vielen Dank für Ihre Interesse, wir werden uns so bald wie möglich mit Ihnen in Verbindung setzen.</p><p>Sie können über das Menü links nun fortfahren.</p>
$this->content->template['message_2094']="<h2>Jūsu ziņa ir nosūtīta.</h2>
<p>Pateicamies par Jūsu interesi. Mēs ar Jums sazināsimies, tiklīdz varēsim.</p> ";

// print.php

//Autor:
$this->content->template['message_2095']="Autors:";
//Kommentar von
$this->content->template['message_2096']="Komentārs no";

// profil.html

//<h2>Login</h2>    <p>Sie können hier einen Account für sich erstellen. Sämtliche Daten die in die Datenbank eingetragen werden, werden Ihnen auch per Email zugestellt.</p>
$this->content->template['message_2097']="<h2>Log in</h2>
    <p>Šeit Jūs varat izveidot savu lietotājkontu. Visi dati, kas tiks ievadīti datubāzē, tiks nosūtīti Jums pa e-pastu.</p>";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2098']="Jūs varat ievadīt sava lietotājkonta datus šeit.";
//Username:
$this->content->template['message_2099']="Lietotājvārds:";
// Emailadresse:
$this->content->template['message_2100']="E-pasta adrese:";
//Passwort:
$this->content->template['message_2101']="Parole:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Parole (pārbaude):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']=" Vai vēlaties saņemt e-pastu, ja kāds ir atbildējis Jūsu ierakstam forumā? ";
//Antwortmail?
$this->content->template['message_2104']="Atbildes e-pasts?";
// erstellen
$this->content->template['message_2105']="izveidot";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Šeit varat rediģēt savus datus ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Šeit varat ievadīt sava lietotājkonta datus";
//Username:
$this->content->template['message_2108']="Lietotājkonts:";
// Emailadresse:
$this->content->template['message_2109']="E-pasta adrese:";
//Neues Passwort:
$this->content->template['message_2110']="Jauna parole:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']="Vai vēlaties saņemt e-pastu, ja kāds ir atbildējis Jūsu ierakstam forumā?";
// Antwortmail?
$this->content->template['message_2112']="Atbildes e-pasts?";
//bearbeiten
$this->content->template['message_2113']="rediģēt";

// weiter.html

//Dieser Link führt eine Seite zurück.
$this->content->template['message_2114']="Iepriekšējā lapa.";
//Die aktuell angezeigte Seite.
$this->content->template['message_2115']="Šī lapa.";
//Dieser Link führt zur
$this->content->template['message_2116']="Šī saite ved uz lapu:";
//Seite.
$this->content->template['message_2117']=".";
//Eine Seite weiter.
$this->content->template['message_2118']="Nākamā lapa.";
//Dieser Link führt eine Seite weiter.
$this->content->template['message_2119']="Uz nākamo lapu.";

//Hilfsmenü

//direkt zum Inhalt
$this->content->template['message_2120']="doties uz saturu";
//zur Bereichsnavigation
$this->content->template['message_2121']="uz navigāciju pa jomām";
// direkt zur Suche
$this->content->template['message_2122']="uz meklēšanu";
//direkt zum einlogen
$this->content->template['message_2123']="uz pieslēgšanos";
//Frontend
$this->content->template['message_2124']="Priekšgals";
//auslogen
$this->content->template['message_2125']="atslēgties";

//rightcollum.html

//ausprobieren
$this->content->template['message_2126']="izmēģināt";
//Login
$this->content->template['message_2127']="Pieslēgšanās";
//Einlogen
$this->content->template['message_2128']="Pieslēgties";
//Username
$this->content->template['message_2129']="Lietotājvārds";
//Passwort
$this->content->template['message_2130']="Parole";
//einlogen
$this->content->template['message_2131']="Pieslēgties ";
//Einen neuen Account anlegen.
$this->content->template['message_2132']="Izveidot jaunu lietotājkontu.";
//Account bearbeiten
$this->content->template['message_2133']="Pārraudzīt lietotājkontu";
//auslogen
$this->content->template['message_2134']="atslēgties";
//Suche
$this->content->template['message_2135']="Meklēt";
//Suchbegriff hier eingeben
$this->content->template['message_2136']="Ievadīt meklējamos vārdus";
//eingeben
$this->content->template['message_2137']="ievadīt";
//Finden
$this->content->template['message_2138']="Atrast";
//Styleswitcher
$this->content->template['message_2139']="Mainīt stilu";
//w&auml;hlen
$this->content->template['message_2140']="izvēlēties";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2141']="Lūdzu pārbaudiet savus ierakstus";


//am
$this->content->template['message_2142']="";
//Bitte einen Suchbegriff eingeben.
$this->content->template['message_2143']="Ievadiet meklēšanas vārdu.";
//There was no entry found
$this->content->template['message_2145']="Ieraksts netika atrasts";
//Links in diesem text
$this->content->template['message_2146']="Saites šajā tekstā";
//Abbreveations in this text:
$this->content->template['message_2147']="Saīsinājumi šajā tekstā";
//Inhaltsverzeichnis
$this->content->template['message_2148']="Lapas karte";
//Impressum
$this->content->template['message_2149']="Imprints";
//Kontakt
$this->content->template['message_2150']="Kontakti";
//Hilfe
$this->content->template['message_2151']="Palīdzība";
//Kopie für mich?
$this->content->template['message_2152']="Kopija man?";
//
$this->content->template['message_2153']="Jums nav atļauta pieeja";
//
$this->content->template['message_2154']="Mēģināt vēlreiz";
//
//foot.inc.html
$this->content->template['message_2155']="";
//Your Account is closed for 10 Minutes, because of 4 wrong triales to Login!
$this->content->template['message_2156']="Jūsu lietotājkonts tiek slēgts uz 10 minūtēm, jo jūs 4 reizes kļūdījāties mēģinot pieslēgties!";
//
$this->content->template['message_2157']="Raksts tika nosūtīts.";
//
$this->content->template['message_2158']="Apkārtraksts";
//
$this->content->template['message_2159']="Vai vēlies lasīt Papoo apkārtrakstu?";
//
$this->content->template['message_passwort_vergessen']="Aizmirsi paroli?";
//


/**
Neu ab 14.03.2006
*/

//
$this->content->template['Schriftart']="Fontu grupa";
//
$this->content->template['Schriftfarbe']="Fontu krāsa";
//
$this->content->template['auswaehlen']="Izvēlēties";
//
$this->content->template['ueberschriften']="Galvene";
//
$this->content->template['ueberschrift']="Galvene";
//
$this->content->template['Auszeichnungen']="Apraksts";
//
$this->content->template['abk_von']="Saīsinājums no";
//
$this->content->template['acr_von']="Akronīms no";
//
$this->content->template['zit_von']="Citāts no";
//
$this->content->template['message_2170']="Kuri virsraksti 1-6 ir iespējami.";
//
$this->content->template['message_2171']="Papildus loģiskā iezīmēšana";
//
$this->content->template['sprung_menu']="Ātrā izvēlne";
//
$this->content->template['spez_seiten']="Īpašās lapas";
//
$this->content->template['message_2174']="Lietotājvārds";
//
$this->content->template['message_2175']="Parole";
//
$this->content->template['mensch']="Vai Tu esi cilvēks?";
//
$this->content->template['message_2177']="Drošības apsvērumu dēļ šo formu sargā Spamvairogs.";
//
$this->content->template['message_2178']="Ja vēlaties nosūtīt, Jums jāuzraksta numurs, kas redzams attēlā.";
//
$this->content->template['message_2179']="Drošības kods formas nosūtīšanai";
//
$this->content->template['message_2180']="Kods";
//
$this->content->template['message_2181']="Izmantojiet matemātiku, lai nosūtītu formu.";
//
$this->content->template['message_2182']='Jums jāsakārto zīmes noteiktā kārtībā, piemēram.:<br />
				2. Sign: x; 1. Sign: 4; 3. Sign: s; means &quot;4xs&quot;
			</p>';
//
$this->content->template['message_2183']="Jūs esat šeit";
//
$this->content->template['message_2184']="Izvēlne";
//
$this->content->template['message_2185']="Izvēlieties valodu";
//
$this->content->template['suche']="Meklēt";
//
$this->content->template['message_2187']="Labā kolonna";
//
$this->content->template['message_2188']="Ziņojumu dēlis";
//
$this->content->template['message_2189']="Ziņojumu dēlis";
//
$this->content->template['message_2190']="Pēdējais ieraksts";
//
$this->content->template['message_2191']="Tēmas";
//
$this->content->template['message_2192']="Ziņa";
//
$this->content->template['message_2193']="Viesu grāmata";
//
$this->content->template['message_2194']="Galvenais saturs";
//
$this->content->template['message_2195']="Rediģēt šo rakstu.";
//
$this->content->template['message_2196']="atpakaļ";
//
$this->content->template['message_2197']="Lapas karte";
//
$this->content->template['message_2198']="Saziņas forma";
//
$this->content->template['message_2199']="Rediģēt lietotāja datus";
//
$this->content->template['message_2200']="Tavs lietotājkonts";
//
$this->content->template['message_2201']="Tavs lietotājkonts ir aktivizēts un to vari rediģēt šeit.";
//
$this->content->template['message_2202']="Tava lietotājkonta dati";
//
$this->content->template['message_2203']="Pieejamie dati";
//
$this->content->template['message_2204']="Lietotājvārds";
//
$this->content->template['message_2205']="personiskie dati";
//
$this->content->template['message_2206']="Vārds";
//
$this->content->template['message_2207']="Uzvārds";
//
$this->content->template['message_2208']="Adrese";
//
$this->content->template['message_2209']="Pasta indekss";
//
$this->content->template['message_2210']="Pilsēta";
//
$this->content->template['message_2211']="Ziņojumu dēlis";
//
$this->content->template['message_2212']="Kurš no ziņojuma dēļa izskatiem Tev patīk vislabāk?";
//
$this->content->template['message_2213']="Pavedienu izskats";
//
$this->content->template['message_2214']="Ziņojumu dēļa izskats";
//
$this->content->template['message_2215']="Pavediens + izskats";
//
$this->content->template['message_2216']="Papildus iespējas";
//
$this->content->template['message_2217']="Vai vēlies turpināt šo sesiju?";
//
$this->content->template['message_2218']="Turpināt sesiju?";
//
$this->content->template['message_2219']="Izvēlēties savu individuālo stilu.";
//
$this->content->template['message_signatur'] = "Paraksts";
$this->content->template['message_signatur_text'] = "Tavs personiskais paraksts forumā.";
//
$this->content->template['message_2220']="Rediģēt lietotājkontu";
//
$this->content->template['message_2221']="Saglabāt datus?";
//
$this->content->template['message_2222']="saglabāt";
//
$this->content->template['message_2223']="Izdzēst lietotājkontu";
//
$this->content->template['message_2224']="Vai vēlies izdzēst lietotājkontu?";
//
$this->content->template['message_2225']="izdzēst";
//
$this->content->template['message_2226']="Iela un numurs";
//
$this->content->template['message_2227']="Dati.";
//
$this->content->template['reservierung']="rezervēt";
//
$this->content->template['message_2229']="Diena";
//
$this->content->template['message_2230']="Mēnesis";
//
$this->content->template['message_2231']="Naktis";
//
$this->content->template['message_2232']="Vienvietīgā istaba";
//
$this->content->template['message_2233']="Divvietīgā istaba";
//
$this->content->template['message_2234']="atzīmēt";
//
$this->content->template['message_2235']="Jūsu lietotājkonts ir aktivizēts!";
//
$this->content->template['message_2236']="teksts";
//
$this->content->template['message_2237']="Sīkā izvēlne";
//
$this->content->template['message_2238']="Galvenais teksts";
//
$this->content->template['message_2239']="bbcode Redaktora attēls";
//
$this->content->template['message_2240']="bbcode Redaktors";
//
$this->content->template['message_2241']="Pieslēgšanās forma";
//
$this->content->template['message_2242']="Spam aizsardzība";
//
$this->content->template['message_2243']="Aizmirsta parole";
//
$this->content->template['message_2244']="Pārsūtīt";
//
$this->content->template['message_2245']="Trešā kolonna";
//
$this->content->template['message_2246']="Pieslēgšanās";
//
$this->content->template['message_2247']="Izvēlnes 0. līmenis";
//
$this->content->template['message_2248']="Izvēlnes 1. līmenis";
//
$this->content->template['message_2249']="Izvēlnes 2. līmenis";
//
$this->content->template['message_2250']="Izvēlnes 3.līmenis";
//
$this->content->template['message_2251']="Izvēlnes līmeņu saites";
//
$this->content->template['message_2252']="Jaunumi";
//
$this->content->template['message_2253']="Kājene";
//
$this->content->template['message_2254']="Saturs pa kreisi";
//
$this->content->template['message_2255']="Saturs vidū";
//
$this->content->template['message_2256']="Saturs pa labi";

$this->content->template['message_2257']="texttext";
//Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein
$this->content->template['message_2258']="Ja esat aizmirsis savu paroli, ievadiet savu e-pasta adresi vai lietotājvārdu šeit.";
//
$this->content->template['message_2259']="Ja esat aizmirsis savu paroli, ievadiet savu e-pasta adresi vai lietotājvārdu šeit.";
//
$this->content->template['message_2260']="Jūsu parole tika nosūtīta, dažu minūšu laikā Jūs saņemsiet e-pastu ar jauno paroli.";
//
$this->content->template['message_2261']="Izveidot jaunu paroli.";
//
$this->content->template['message_2262']="Atjaunināt paroli";

// Texte für Downloads
// *******************
$this->content->template['download']['kein_recht'] = "Jūs nevarat lejupielādēt šo failu.";
$this->content->template['download']['link_title'] = "Lejupielāde sāksies jaunā logā.";
$this->content->template['download']['info_01'] = "Izmērs";
$this->content->template['download']['info_02'] = "Saskaitīt lejupielādes";
$this->content->template['download']['info_03'] = "Pēdējās lejupielādes laiks";
$this->content->template['download']['keine_datei'] = "Atvainojiet. Šis dokuments vairs neeksistē.";


//
$this->content->template['message_2263']="Melden Sie sich jetzt bitte in der nebenstehenden Anmeldemaske an, dann können Sie Ihren Account nutzen.";


// Texte für Modul "mod-efa_fontsize"
$this->content->template['mod_efa_fontsize']['text'] = "Burtu izmērs:";
$this->content->template['mod_efa_fontsize']['bigger'] = "palielināt burtu izmēru";
$this->content->template['mod_efa_fontsize']['normal'] = "vidējs burtu izmērs";
$this->content->template['mod_efa_fontsize']['smaller'] = "samazināt burtu izmēru";

// Texte für die lokalisierung von LightBox
$this->content->template['lightbox']['text_1a'] = "Nospiest pogu";
$this->content->template['lightbox']['text_1b'] = "Uzklikšķināt uz attēla";
$this->content->template['lightbox']['text_2'] = "aizvērt šo logu.";

//Kontaktformular der Seite
$this->content->template['contact']['mail1'] = "Lapas saziņas forma ";


//Suche
$this->content->template['suche']['serg'] = "Meklēšanas rezultāti";
$this->content->template['suche']['ergbn'] = "Jūsu meklēšanas rezultāti par tēmu ";
$this->content->template['suche']['res'] = "rezultāti";
$this->content->template['suche']['bis'] = "uz";
$this->content->template['suche']['insg'] = "no";
$this->content->template['suche']['seiten'] = "lapas";
$this->content->template['suche']['seite'] = "lapa";
$this->content->template['suche']['Stand'] = "Stāvēt";
$this->content->template['suche']['erst'] = "Rakstīts";
$this->content->template['suche']['Erweiterte'] = "Vairāk";
$this->content->template['suche']['susst'] = "Sākt meklēšanu";
$this->content->template['suche']['Suchtyp'] = "Meklēšanas veids";
$this->content->template['suche']['finein'] = "atrast vienu ";
$this->content->template['suche']['oder'] = "vai&nbsp;&nbsp;";
$this->content->template['suche']['findall'] = "atrast visus";
$this->content->template['suche']['und'] = "un&nbsp;&nbsp;";
$this->content->template['suche']['findd'] = "atrast tieši šo vārdu";
$this->content->template['suche']['Exakt'] = "";
$this->content->template['suche']['anergb1'] = "Meklēšanas rezultātu skaits (trāpījumi)";
$this->content->template['suche']['sort'] = "Šķirošana";
$this->content->template['suche']['z10'] = "Rādīt 10 rezultātus katrā lapā";
$this->content->template['suche']['z20'] = "Rādīt 20 rezultātus katrā lapā";
$this->content->template['suche']['z30'] = "Rādīt 30 rezultātus katrā lapā";
$this->content->template['suche']['z100'] = "Rādīt 100 rezultātus katrā lapā";
$this->content->template['suche']['all100'] = "Visus (max. 100)";
$this->content->template['suche']['sortti'] = "Šķirot pēc nosaukuma";
$this->content->template['suche']['Titel'] = "nosaukums";
$this->content->template['suche']['sortart'] = "Šķirot pēc raksta rediģēšanas datuma";
$this->content->template['suche']['datl'] = "pēdējā papildinājuma datums";
$this->content->template['suche']['aufst'] = "Rādīt dilstošā kārtībā";
$this->content->template['suche']['ergba'] = "Rādīt augošā kārtībā";
$this->content->template['suche']['Absteigend'] = "dilstoši";
$this->content->template['article']['coment'] = "komentāri";
$this->content->template['forum']['forum'] = "Ziņojumu dēlis";
$this->content->template['article']['schreiben'] = "rakstīt?";
$this->content->template['article']['mail1'] = "";
/**
errors
*/
// wrong Email address
$this->content->template['error_1']="Diese Email Adresse ist leider nicht in Ordnung. Wahrscheinlich haben Sie sich vertippt.";
