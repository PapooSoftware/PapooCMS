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
$this->content->template['message_1']="De e-mail is verstuurd."; 
// Text über dem Forum
$this->content->template['message_2']="Hier vindt u alle forums."; 
// Die letzten zehn Einträge
$this->content->template['message_3']="De laatste 10 berichten"; 
$this->content->template['message_3_1']="De laatste 10 berichten"; 
$this->content->template['message_3_2']="Lijst van laatste thema’s"; 
// $neuer = "Einen neuen Beitrag schreiben?";
$this->content->template['message_4']="een nieuw bericht schrijven?"; 
// Kein Ergebniss
$this->content->template['message_5']="Sorry, niets gevonden. S.v.p. uw zoekomschrijving wijzigen."; 
// Kein Zugriff auf Message
$this->content->template['message_6']="U heeft geen toegang tot deze berichten."; 
// Sie müssen sich einlogen
$this->content->template['message_7']="U moet inloggen om een bericht te kunnen schrijven."; 
//<p>Diese Nachricht existiert nicht. \n</body></html>\n
$this->content->template['message_8']="<p>Dit bericht bestaat niet. \n</body></html>\n"; 
//<p>Dieses Forum existiert nicht, bitte wählen <a href=\"./forum.php\">Sie ein anderes aus.</a>.\n</div></body></html>\n
$this->content->template['message_9']="<p>Dit forum bestaat niet, s.v.p. selecteren <a href=\"./forum.php\">a different forum.</a>.\n</div></body></html>\n"; 
//<p>Sie müssen einen Text eingeben!</p>
$this->content->template['message_10']="<p>U moet tekst invoeren!</p>"; 
// Sie haben leider keine Rechte um einen Beitrag schreiben zu k&ouml;nnen.
$this->content->template['message_11']="U bent niet bevoegd om een bericht te schrijven."; 
// Ihre Daten wurden eingetragen und Sie sind jetzt eingelogt.
$this->content->template['message_12 ']="<h1>Uw gegevens zijn verwerkt en u kunt nu inloggen.</h1>"; 
$this->content->template['message_73']="Eén pagina terug"; 
//weitere Seiten
$this->content->template['message_86']="Een pagina vooruit"; 
/**index.html*/
//Dieser Link führt zur Startseite von
$this->content->template['message_2000']="Deze link verwijst naar de home pagina. "; 
//Den obigen Artikel versenden.
$this->content->template['message_2001']="Verstuur het bovenstaande artikel."; 
//Empfänger-Email:
$this->content->template['message_2002']="Ontvanger e-mail:"; 
// Ihre Email:
$this->content->template['message_2003']=" Uw e-mail:"; 
//Was möchten Sie gerne senden?
$this->content->template['message_2004']="Wat wilt u verzenden?"; 
//Nur den Link
$this->content->template['message_2005']="alleen de link"; 
//Den ganzen Text
$this->content->template['message_2006']="de gehele tekst"; 
//Kommentar
$this->content->template['message_2007']="Opmerkingen"; 
//Senden
$this->content->template['message_2008']="Verstuuren"; 
//Direkt zum einlogen.
$this->content->template['message_2009']="Direct inloggen."; 
//Autor:
$this->content->template['message_2010']="Auteur: "; 
//Dieser Artikel wurde bereits
$this->content->template['message_2011']="Dit artikel"; 
//Dieser Link öffnet den Artikel
$this->content->template['message_2012']="Deze link opent het volgende artikel:"; 
//im selben Fenster.
$this->content->template['message_2013']=" in hetzelfde venster.";
//Optionen zu diesen Artikel:
$this->content->template['message_2014']="Opties voor dit artikel:";
//Sie können diesen Artikel Versenden/Empfehlen
$this->content->template['message_2015']="U kunt dit artikel versturen of aanbevelen"; 
//DruckVersion dieses Artikels
$this->content->template['message_2016']="Print versie van dit artikel"; 
//Kommentar von
$this->content->template['message_2017']="Opmerkingen van  "; 
// Kommentar zu
$this->content->template['message_2018']=" Opmerkingen met betrekking tot  "; 
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2019']="Hier kunt u uw opmerkingen noteren."; 
//Thema:
$this->content->template['message_2020']="Thema:"; 
//Beitrag:
$this->content->template['message_2021']="Bijdrage:"; 
//Eintragen
$this->content->template['message_2022']="Enter"; 
//Besucher seit 20.04.2004.
$this->content->template['message_2023']="Bezoekers "; 
//Nach oben.
$this->content->template['message_2024']="naar boven."; 
//mal angesehen.
$this->content->template['message_2025']="aantal keren bezocht."; 

// forum.html

//Menü überspringen
$this->content->template['message_2026']="Meteen naar het (de) artikel(en)"; 
//Login
$this->content->template['message_2027']="inloggen"; 
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2028']="S.v.p. uw gegevens controleren"; 
//Username
$this->content->template['message_2029']="gebruikersnaam"; 
//Passwort
$this->content->template['message_2030']="wachtwoord"; 
//einlogen
$this->content->template['message_2031']="inloggen"; 
//Einen neuen Account anlegen.
$this->content->template['message_2032']="een nieuw account aanmaken."; 
// Account bearbeiten
$this->content->template['message_2033']="account wijzigen"; 
//auslogen
$this->content->template['message_2034']="uitloggen"; 
//Suchbegriff hier eingeben
$this->content->template['message_2035']="invoeren trefwoord"; 
//Im Forum
$this->content->template['message_2036']="in het forum"; 
//Finden
$this->content->template['message_2037']="zoek"; 
//Zurück zur Übersicht
$this->content->template['message_2038']="Terug naar het overzicht"; 
//Die Resultate Ihrer Suche:
$this->content->template['message_2039']="De resultaten van uw zoekopdracht:"; 
//Hier finden Sie alle Foren auf
$this->content->template['message_2040']="Hier kunt u alle forums vinden van"; 
//Forum
$this->content->template['message_2041']="Forum";

//forumthread.html

//Menü überspringen
$this->content->template['message_2042']="Direct naar het artikel"; 
//Login
$this->content->template['message_2043']="inloggen "; 
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2044']="S.v.p. uw gegevens controleren"; 
//Username
$this->content->template['message_2045']="gebruikersnaam"; 
//Passwort
$this->content->template['message_2046']="wachtwoord"; 
//einlogen
$this->content->template['message_2047']="inloggen"; 
//Account bearbeiten
$this->content->template['message_2048']="wijzig account"; 
//auslogen
$this->content->template['message_2049']="uitloggen "; 
//zurück zur Übersicht
$this->content->template['message_2050']="terug naar het overzicht"; 
//Suchbegriff hier eingeben
$this->content->template['message_2051']="invoeren zoekopdracht"; 
//Im Forum
$this->content->template['message_2052']="in het forum"; 
//Finden
$this->content->template['message_2053']="zoek"; 
//Eintrag ändern:
$this->content->template['message_2054']="wijzig de toegang:"; 
//Hier wird der Eintrag geändert.
$this->content->template['message_2055']="U kunt uw toegang hier wijzigen.";  
//Autor
$this->content->template['message_2056']="Auteur"; 
//Thema
$this->content->template['message_2057']="Thema";
//Eingabe und Formatierung der Inhalte:
$this->content->template['message_2058']="Hier uw toegang invoeren en opmaken:"; 
//Beitrag
$this->content->template['message_2059']="Bericht"; 
//eintragen
$this->content->template['message_2060']="Invoeren"; 
//Diesen Beitrag editieren
$this->content->template['message_2061']="Wijzig het bericht"; 
//Hits
$this->content->template['message_2062']="Treffers"; 
//Beitrag
$this->content->template['message_2063']="bericht"; 
//als Antwort auf:
$this->content->template['message_2064']="als antwoord schrijven naar:"; 
//schreiben?
$this->content->template['message_2065']="?";
//Hier kann eine Eintrag in das Forum gemacht werden
$this->content->template['message_2066']="Hier kunt u een bericht opmaken aan het forum"; 
//Einen neuen Account anlegen.
$this->content->template['message_2067']="een nieuw account aanmaken."; 

// guestbook.html

//Direkt zum einlogen.
$this->content->template['message_2068']="Direct inloggen.";  
//Das Gästebuch wurde bereits
$this->content->template['message_2069']="Het gastenboek is bezocht"; 
//mal angesehen.
$this->content->template['message_2070']=" tijden."; 
//Dieser Link öffnet den Artikel
$this->content->template['message_2071']="Deze link opent dit artikel"; 
//im selben Fenster.
$this->content->template['message_2072']="iin hetzelfde venster."; 
//Gästebucheintrag von
$this->content->template['message_2073']="Invoer van "; 
//Kommentar ins
$this->content->template['message_2074']="Schrijf een opmerking in de    "; 
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2075']="Hier kunt u een opmerking maken."; 
//Autor:
$this->content->template['message_2076']="Auteur: "; 
//Thema:
$this->content->template['message_2077']="Thema:  "; 
// Beitrag:
$this->content->template['message_2078']=" Bericht:"; 
//eintragen
$this->content->template['message_2079']="Invoeren  "; 
//Besucher seit 20.04.2004.
$this->content->template['message_2080']="Bezoekers:"; 
//Optionen zu diesen Artikel:
$this->content->template['message_2081']="Opties voor dit artikel:"; 
//DruckVersion dieses Artikels
$this->content->template['message_2082']="Print versie van dit artikel "; 
// inhalt.php

//Direkt zum einlogen.
$this->content->template['message_2083']="Direct inloggen."; 
//Besucher.
$this->content->template['message_2084']="Bezoeker. "; 

// kontakt.php

// <h2>Kontaktformular</h2> <p>Wenn Sie uns direkt erreichen wollen, k&ouml;nnen Sie das &uuml;ber unser Kontaktformular machen.</p> <p>Bitte f&uuml;llen Sie alle Felder aus, damit wir Ihnen auch antworten k&ouml;nnen.</p>
$this->content->template['message_2085']=" <h2>Contactformulier</h2> 
 <p>Als u contact met ons wilt opnemen, kunt u gebruikmaken van dit formulier.</p> 
 <p>S.v.p. alle benodigde gegevens invullen.</p>"; 
//Kontakt zu
$this->content->template['message_2086']="Contact opnemen met";  
//Name:
$this->content->template['message_2087']="Naam"; 
//Hier bitte Ihren Namen eingeben
$this->content->template['message_2088']="S.v.p. uw naam invoeren "; 
//Ihre <span lang']="en">E-Mail</span>:
$this->content->template['message_2089']="Uw <span lang=\"en\">E-mail </span>:";
//Hier bitte Ihre E-Mail Adresse eingeben
$this->content->template['message_2090']="S.v.p. uw e-mail adres invullen "; 
//Ihre Nachricht:
$this->content->template['message_2091']=" Uw bericht: "; 
//Hier bitte Ihre Nachricht eingeben
$this->content->template['message_2092']="S.v.p. uw bericht invoeren "; 
//- Nachricht -
$this->content->template['message_2093']="- bericht -"; 
//<h2>Ihre Nachricht wurde übermittelt.</h2><p>Vielen Dank für Ihre Interesse, wir werden uns so bald wie möglich mit Ihnen in Verbindung setzen.</p><p>Sie können über das Menü links nun fortfahren.</p>
$this->content->template['message_2094']="<h2>Uw bericht is verzonden.</h2> 
<p>Bedankt voor uw interesse. We zullen zo snel mogelijk contact met u opnemen.</p> ";

// print.php

//Autor:
$this->content->template['message_2095']="Auteur:"; 
//Kommentar von
$this->content->template['message_2096']="Opmerkingen van "; 

// profil.html

//<h2>Login</h2>    <p>Sie können hier einen Account für sich erstellen. Sämtliche Daten die in die Datenbank eingetragen werden, werden Ihnen auch per Email zugestellt.</p>
$this->content->template['message_2097']="<h2>Log in</h2>
    <p>Hier kunt u een account aanmaken voor uzelf. Alle gegevens die ingevoerd zullen worden in de databank, zullen per e-mail naar u worden verzonden.</p>";   
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2098']="U kunt hier de gegevens invoeren voor uw account."; 
//Username:
$this->content->template['message_2099']="Gebruikersnaam: "; 
// Emailadresse:
$this->content->template['message_2100']=" E-mail adres:"; 
//Passwort:
$this->content->template['message_2101']="Wachtwoord:"; 
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Wachtwoord (check):"; 
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']=" Wilt u een e-mail ontvangen, als iemand heeft gereageerd op uw plaatsing in het forum? "; 
//Antwortmail?
$this->content->template['message_2104']="E-mail als antwoord??"; 
// erstellen
$this->content->template['message_2105']=" maken"; 
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Hier kunt u uw gegevens bewerken "; 
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Hier kunt u de gegevens invoeren voor uw account. "; 
//Username:
$this->content->template['message_2108']="Gebruikersnaam:"; 
// Emailadresse:
$this->content->template['message_2109']=" E-mail adres:"; 
//Neues Passwort:
$this->content->template['message_2110']="Nieuw wachtwoord:"; 
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" Wilt u een e-mail ontvangen, als iemand heeft gereageerd op uw plaatsing in het forum?"; 
// Antwortmail?
$this->content->template['message_2112']=" E-mail als antwoord?? "; 
//bearbeiten
$this->content->template['message_2113']=" bewerken "; 

// weiter.html

//Dieser Link führt eine Seite zurück.
$this->content->template['message_2114']="Eén pagina terug."; 
//Die aktuell angezeigte Seite.
$this->content->template['message_2115']="Deze pagina."; 
//Dieser Link führt zur
$this->content->template['message_2116']="Deze link verwijst naar de pagina:"; 
//Seite.
$this->content->template['message_2117']=".";
//Eine Seite weiter.
$this->content->template['message_2118']="Eén pagina vooruit."; 
//Dieser Link führt eine Seite weiter.
$this->content->template['message_2119']="Eén pagina verder."; 

//Hilfsmenü

//direkt zum Inhalt
$this->content->template['message_2120']="Direct naar de inhoud";  
//zur Bereichsnavigation
$this->content->template['message_2121']="Naar de navigatie binnen de sectie"; 
// direkt zur Suche
$this->content->template['message_2122']=" Direct naar zoeken "; 
//direkt zum einlogen
$this->content->template['message_2123']="Direct inloggen"; 
//Frontend
$this->content->template['message_2124']="Frontend"; 
//auslogen
$this->content->template['message_2125']="uitloggen"; 

//rightcollum.html

//ausprobieren
$this->content->template['message_2126']="test"; 
//Login
$this->content->template['message_2127']="inloggen"; 
//Einlogen
$this->content->template['message_2128']="inloggen"; 
//Username
$this->content->template['message_2129']="Gebruikersnaam"; 
//Passwort
$this->content->template['message_2130']="wachtwoord"; 
//einlogen
$this->content->template['message_2131']="inloggen "; 
//Einen neuen Account anlegen.
$this->content->template['message_2132']="een nieuw account aanmaken."; 
//Account bearbeiten
$this->content->template['message_2133']="account beheren"; 
//auslogen
$this->content->template['message_2134']="uitloggen"; 
//Suche
$this->content->template['message_2135']="zoeken"; 
//Suchbegriff hier eingeben
$this->content->template['message_2136']="Zoekopdracht invoeren"; 
//eingeben
$this->content->template['message_2137']="invoeren"; 
//Finden
$this->content->template['message_2138']="vinden"; 
//Styleswitcher
$this->content->template['message_2139']="stijlkeuze"; 
//w&auml;hlen
$this->content->template['message_2140']="selecteer"; 
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2141']="S.v.p. uw ingevoerde gegevens checken"; 


//am
$this->content->template['message_2142']="aan"; 
//Bitte einen Suchbegriff eingeben.
$this->content->template['message_2143']="S.v.p. een zoekwoord invoeren."; 
//There was no entry found
$this->content->template['message_2145']="Er is geen ingave gevonden"; 
//Links in diesem text
$this->content->template['message_2146']="Links in deze tekst"; 
//Abbreveations in this text:
$this->content->template['message_2147']="Verkortingen in deze tekst:"; 
//Inhaltsverzeichnis
$this->content->template['message_2148']="Inhoudsopgave (sitemap) "; 
//Impressum
$this->content->template['message_2149']="Afdruk"; 
//Kontakt
$this->content->template['message_2150']="Contact"; 
//Hilfe
$this->content->template['message_2151']="Help"; 
//Kopie für mich?
$this->content->template['message_2152']="kopie voor mij?"; 
//
$this->content->template['message_2153']="U heeft geen bevoegdheid";
//
$this->content->template['message_2154']="Probeer opnieuw"; 
//
//foot.inc.html
$this->content->template['message_2155']="";
//Your Account is closed for 10 Minutes, because of 4 wrong triales to Login!
$this->content->template['message_2156']="Uw account is voor 10 minuten gesloten, vanwege 4 verkeerde inlog pogingen!"; 
//
$this->content->template['message_2157']="Het artikel is verstuurd."; 
//
$this->content->template['message_2158']="Nieuwsbrief"; 
//
$this->content->template['message_2159']="Wilt u de Papoo nieuwsbrief ontvangen?"; 
//
$this->content->template['message_passwort_vergessen']="Wachtwoord vergeten?"; 
//


/**
Neu ab 14.03.2006
*/

//
$this->content->template['Schriftart']="Lettertype"; 
//
$this->content->template['Schriftfarbe']="Letter kleur"; 
//
$this->content->template['auswaehlen']="Selecteer"; 
//
$this->content->template['ueberschriften']="Titels"; 
//
$this->content->template['ueberschrift']="Titel"; 
//
$this->content->template['Auszeichnungen']="Omschrijving"; 
//
$this->content->template['abk_von']="Verkorting van"; 
//
$this->content->template['acr_von']="Acroniem van"; 
//
$this->content->template['zit_von']="Citeren van"; 
//
$this->content->template['message_2170']="Welke titels van 1-6 zijn mogelijk."; 
//
$this->content->template['message_2171']="Additionele logische markering"; 
//
$this->content->template['sprung_menu']="Ga naar het menu "; 
//
$this->content->template['spez_seiten']="Speciale sites"; 
//
$this->content->template['message_2174']="Gebruikersnaam"; 
//
$this->content->template['message_2175']="Wachtwoord"; 
//
$this->content->template['mensch']="Spamshield"; 
//
$this->content->template['message_2177']="Vanwege veiligheidsmaatregelingen is dit formulier beveiligd door Spamshield.";  
//
$this->content->template['message_2178']="Indien u wilt versturen, moet u het nummer typen op de afbeelding."; 
//
$this->content->template['message_2179']="Veiligheidscode voor insturen van formulier";  
//
$this->content->template['message_2180']="Code"; 
//
$this->content->template['message_2181']="S.v.p. berekenen om aan het forum voor te leggen."; 
//
$this->content->template['message_2182']='U moet de tekens in de juiste volgorde zetten. e.g.:<br /> 
				2. Sign: x; 1. Sign: 4; 3. Sign: s; means &quot;4xs&quot;
			</p>';
//
$this->content->template['spamschutz_the_digit'] = 'getal';
$this->content->template['spamschutz_plus'] = 'plus getal';
$this->content->template['spamschutz_minus'] = 'min getal';


$this->content->template['message_2183']="U bent hier"; 
//
$this->content->template['message_2184']="Menu"; 
//
$this->content->template['message_2185']="Selecteer een taal"; 
//
$this->content->template['suche']="Zoeken"; 
//
$this->content->template['message_2187']="Rechterkolom"; 
//
$this->content->template['message_2188']="Bord"; 
//
$this->content->template['message_2189']="Bord"; 
//
$this->content->template['message_2190']="Laatste invoer"; 
//
$this->content->template['message_2191']="Thema’s"; 
//
$this->content->template['message_2192']="Bericht"; 
//
$this->content->template['message_2193']="Gastenboek"; 
//
$this->content->template['message_2194']="Belangrijkste inhoud"; 
//
$this->content->template['message_2195']="Bewerk dit artikel."; 
//
$this->content->template['message_2196']="terug"; 
//
$this->content->template['message_2197']="Inhoudsopgave"; 
//
$this->content->template['message_2198']="Contactformulier"; 
//
$this->content->template['message_2199']="Bewerk gebruikersgegevens"; 
//
$this->content->template['message_2200']="Uw account"; 
//
$this->content->template['message_2201']="Uw account is actief en u kan uw gegevens hier bewerken."; 
//
$this->content->template['message_2202']="Uw account gegevens"; 
//
$this->content->template['message_2203']="Gegevens voor toegang"; 
//
$this->content->template['message_2204']="Gebruikersnaam"; 
//
$this->content->template['message_2205']="Persoonlijke gegevens"; 
//
$this->content->template['message_2206']="Voornaam"; 
//
$this->content->template['message_2207']="Achternaam"; 
//
$this->content->template['message_2208']="Adres"; 
//
$this->content->template['message_2209']="Postcode"; 
//
$this->content->template['message_2210']="Woonplaats"; 
//
$this->content->template['message_2211']="Bord"; 
//
$this->content->template['message_2212']="Welke kant van het bord bevalt u het beste?"; 
//
$this->content->template['message_2213']="Thread View";
//
$this->content->template['message_2214']="Board View";
//
$this->content->template['message_2215']="Thread + View";
//
$this->content->template['message_2216']="Meer opties"; 
//
$this->content->template['message_2217']="Wilt u ingelogd blijven?"; 
//
$this->content->template['message_2218']="Ingelogd blijven?"; 
//
$this->content->template['message_2219']="Selecteer uw persoonlijke stijl."; 
//
$this->content->template['message_signatur'] = "Handtekening"; 
$this->content->template['message_signatur_text'] = "Uw persoonlijke handtekening in het forum."; 
//
$this->content->template['message_2220']="Bewerk het account"; 
//
$this->content->template['message_2221']="Gegevens opslaan?"; 
//
$this->content->template['message_2222']="opslaan"; 
//
$this->content->template['message_2223']="Account verwijderen"; 
//
$this->content->template['message_2224']="Wilt u het account verwijderen?"; 
//
$this->content->template['message_2225']="verwijderen"; 
//
$this->content->template['message_2226']="Straat en nummer"; 
//
$this->content->template['message_2227']="De gegevens."; 
//
$this->content->template['reservierung']="reserveren"; 
//
$this->content->template['message_2229']="Dag"; 
//
$this->content->template['message_2230']="Maand"; 
//
$this->content->template['message_2231']="Nachten"; 
//
$this->content->template['message_2232']="Eénpersoonskamer"; 
//
$this->content->template['message_2233']="Tweepersoonskamer"; 
//
$this->content->template['message_2234']="controleren"; 
//
$this->content->template['message_2235']="Uw account is geactiveerd!"; 
//
$this->content->template['message_2236']="tekst tekst"; 
//
$this->content->template['message_2237']="Broodkruimelmenu"; 
//
$this->content->template['message_2238']="Hoofdtekst"; 
//
$this->content->template['message_2239']="bbcode editor afbeelding"; 
//
$this->content->template['message_2240']="bbcode editor"; 
//
$this->content->template['message_2241']="Login formulier"; 
//
$this->content->template['message_2242']="Spam protectie"; 
//
$this->content->template['message_2243']="Wachtwoord vergeten"; 
//
$this->content->template['message_2244']="Vooruit"; 
//
$this->content->template['message_2245']="Derde kolom"; 
//
$this->content->template['message_2246']="Inloggen"; 
//
$this->content->template['message_2247']="Menu level 0"; 
//
$this->content->template['message_2248']="Menu level 1"; 
//
$this->content->template['message_2249']="Menu level 2"; 
//
$this->content->template['message_2250']="Menu level 3"; 
//
$this->content->template['message_2251']="Menu level links"; 
//
$this->content->template['message_2252']="Nieuws"; 
//
$this->content->template['message_2253']="Voetnoot"; 
//
$this->content->template['message_2254']="Inhoud links"; 
//
$this->content->template['message_2255']="Inhoud midden"; 
//
$this->content->template['message_2256']="Inhoud rechts"; 

$this->content->template['message_2257']="tekst tekst";
//Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein
$this->content->template['message_2258']="Indien u uw wachtwoord bent vergeten, s.v.p. uw e-mailadres of uw gebruikersnaam hier invoeren."; 
//
$this->content->template['message_2259']="Indien u uw wachtwoord bent vergeten, s.v.p. uw e-mailadres of uw gebruikersnaam hier invoeren."; 
//
$this->content->template['message_2260']="Het wachtwoord is verstuurd, u zult een e-mail ontvangen met een nieuw wachtwoord binnen enkele minuten."; 
//
$this->content->template['message_2261']="Maak een nieuw wachtwoord aan."; 
//
$this->content->template['message_2262']="Verander het wachtwoord"; 

// Texte für Downloads
// *******************
$this->content->template['download']['kein_recht'] = "U heeft geen rechten om dit bestand te downloaden."; 
$this->content->template['download']['link_title'] = "Het downloaden zal starten in een nieuw venster."; 
$this->content->template['download']['info_01'] = "Grootte"; 
$this->content->template['download']['info_02'] = "Het aantal downloads tellen"; 
$this->content->template['download']['info_03'] = "Laatste download op"; 
$this->content->template['download']['keine_datei'] = "Sorry, dit bestand bestaat niet meer."; 


//
$this->content->template['message_2263']="Melden Sie sich jetzt bitte in der nebenstehenden Anmeldemaske an, dann können Sie Ihren Account nutzen.";


// Texte für Modul "mod-efa_fontsize"
$this->content->template['mod_efa_fontsize']['text'] = "Lettergrootte:"; 
$this->content->template['mod_efa_fontsize']['bigger'] = "Lettertype vergroten"; 
$this->content->template['mod_efa_fontsize']['normal'] = "Normale lettergrootte"; 
$this->content->template['mod_efa_fontsize']['smaller'] = "Lettertype verkleinen"; 

// Texte für die lokalisierung von LightBox
$this->content->template['lightbox']['text_1a'] = "Druk op de sleutel"; 
$this->content->template['lightbox']['text_1b'] = "Klik op de afbeelding"; 
$this->content->template['lightbox']['text_2'] = "Om dit venster te sluiten."; 

//Kontaktformular der Seite
$this->content->template['contact']['mail1'] = "Contactformulier van deze site ";


//Suche
$this->content->template['suche'] = [];
$this->content->template['suche']['serg'] = "Zoekresultaten"; 
$this->content->template['suche']['ergbn'] = "Resultaten van uw zoektocht naar "; 
$this->content->template['suche']['res'] = "resultaten"; 
$this->content->template['suche']['bis'] = "naar"; 
$this->content->template['suche']['insg'] = "van"; 
// Plural
$this->content->template['suche']['seiten'] = "pagina’s";
// Singular
$this->content->template['suche']['seite'] = "pagina"; 
$this->content->template['suche']['Stand'] = "Gevestigd te"; 
$this->content->template['suche']['erst'] = "Geschreven op"; 
$this->content->template['suche']['Erweiterte'] = "Meer"; 
$this->content->template['suche']['susst'] = "Start het zoeken"; 
$this->content->template['suche']['Suchtyp'] = "Zoek type"; 
$this->content->template['suche']['finein'] = "vind een "; 
$this->content->template['suche']['oder'] = "of&nbsp;&nbsp;"; 
$this->content->template['suche']['findall'] = "Vindt ze allemaal"; 
$this->content->template['suche']['und'] = "en&nbsp;&nbsp;"; 
$this->content->template['suche']['findd'] = "vind exact dit woord"; 
$this->content->template['suche']['Exakt'] = "";
$this->content->template['suche']['anergb1'] = "aantal zoekresultaten (treffers)"; 
$this->content->template['suche']['sort'] = "Sorteren"; 
$this->content->template['suche']['z10'] = "Laat 10 resultaten per pagina zien"; 
$this->content->template['suche']['z20'] = "Laat 20 resultaten per pagina zien"; 
$this->content->template['suche']['z30'] = "Laat 30 resultaten per pagina zien"; 
$this->content->template['suche']['z100'] = "Laat 100 resultaten per pagina zien"; 
$this->content->template['suche']['all100'] = "Allemaal (maximaal 100)"; 
$this->content->template['suche']['sortti'] = "Sorteren op titel"; 
$this->content->template['suche']['Titel'] = "titel"; 
$this->content->template['suche']['sortart'] = "Sorteren op de datum van wijziging van het artikel"; 
$this->content->template['suche']['datl'] = "Datum laatstgewijzigd"; 
$this->content->template['suche']['aufst'] = "Laat onderliggend niveau zien";
$this->content->template['suche']['ergba'] = "Laat bovenliggend niveau zien"; 

$this->content->template['suche']['Absteigend'] = "Onderliggend"; 
$this->content->template['article']['coment'] = "opmerkingen"; 
$this->content->template['forum']['forum'] = "bord"; 
$this->content->template['article']['schreiben'] = "schrijven?"; 
$this->content->template['article']['mail1'] = "";
/**
errors
*/
// wrong Email address
$this->content->template['error_1']="Diese Email Adresse ist leider nicht in Ordnung. Wahrscheinlich haben Sie sich vertippt.";