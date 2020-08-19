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
$this->content->template['message_1']="El correo electrónico ha sido transmitido";
// Text über dem Forum
$this->content->template['message_2']="y aquí la lista completa de foros";
// Die letzten zehn Einträge
$this->content->template['message_3']="Los últimos diez registros";
$this->content->template['message_3_1']="Los últimos diez registros";
$this->content->template['message_3_2']="Lista de temas actuales";
// $neuer = "¿Escribir una nueva contribución?";
$this->content->template['message_4']="¿Escribir una nueva contribución?";
// Kein Ergebniss
$this->content->template['message_5']="Lamentablemente no se ha encontrado nada. Por favor, precise su concepto de búsqueda, para que le podamos ayudar";
// Kein Zugriff auf Message
$this->content->template['message_6']="Usted no tiene acceso a esta noticia, o no existe.";
// Sie müssen sich einloggen
$this->content->template['message_7']="Usted tiene que registrarse para poder escribir una contribución.";
//<p>Diese Nachricht existiert nicht. \n</body></html>\n
$this->content->template['message_8']="<p>Esta noticia no existe. \n</body></html>\n";
//<p>Dieses Forum existiert nicht, bitte wählen <a href=\"./forum.php\">Sie ein anderes aus.</a>.\n</div></body></html>\n
$this->content->template['message_9']="<p>Este foro no existe, por favor seleccione <a href=\"./forum.php\"> otro foro.</a>.\n</div></body></html>\n";
//<p>Sie müssen einen Text eingeben!</p>
$this->content->template['message_10']="<p>¡Usted tiene que introducir un texto!</p>";
// Sie haben leider keine Rechte um einen Beitrag schreiben zu können.
$this->content->template['message_11']="Lamentablemente usted no dispone de derechos para poder escribir una contribución.";
// Ihre Daten wurden eingetragen und Sie sind jetzt eingeloggt.
$this->content->template['message_12']="<h1>Sus datos han sido registrados. </h1><p>En unos pocos segundoss usted debe recibir un e-Mail. Siga las instrucciones descritas en el e-Mail para activar su cuenta.</p>";
//weitere Seiten
$this->content->template['message_73']="Retroceder una página";
//weitere Seiten
$this->content->template['message_86']="Avanzar una página";
/**index.html*/
//Dieser Link führt zur Startseite von
$this->content->template['message_2000']="Este enlace lleva a la página de inicio de ";
//Den obigen Artikel versenden.
$this->content->template['message_2001']="Enviar la sección anterior";
//Empfänger-Email:
$this->content->template['message_2002']="Correo electrónico destinatario:";
// Ihre Email:
$this->content->template['message_2003']=" Su correo electrónico:";
//Was möchten Sie gerne senden?
$this->content->template['message_2004']="¿Qué desea enviar?";
//Nur den Link
$this->content->template['message_2005']="Sólo el enlace";
//Den ganzen Text
$this->content->template['message_2006']="El texto completo";
//Kommentar
$this->content->template['message_2007']="Comentario";
//Senden
$this->content->template['message_2008']="Enviar";
//Direkt zum einloggen.
$this->content->template['message_2009']="Directamente a registrar.";
//Autor:
$this->content->template['message_2010']="Autor: ";
//Dieser Artikel wurde bereits
$this->content->template['message_2011']="Este artículo ya ha sido";
//Dieser Link öffnet den Artikel
$this->content->template['message_2012']="Este enlace abre el artículo ";
//im selben Fenster.
$this->content->template['message_2013']="en la misma ventana.";
//Optionen zu diesen Artikel:
$this->content->template['message_2014']="Opciones sobre este artículo:";
//Sie können diesen Artikel Versenden/Empfehlen
$this->content->template['message_2015']="Si quiere, puede enviar o recomendar esta sección";
//DruckVersion dieses Artikels
$this->content->template['message_2016']="Versión para imprimir";
//Kommentar von
$this->content->template['message_2017']="Comentario de   ";
// Kommentar zu
$this->content->template['message_2018']=" Comentario sobre ";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2019']="Escribir comentario:";
//Thema:
$this->content->template['message_2020']="Tema:";
//Beitrag:
$this->content->template['message_2021']="Contribución:";
//Eintragen
$this->content->template['message_2022']="Registrar";
//Besucher seit 20.04.2004.
$this->content->template['message_2023']="Visitante ";
//Nach oben.
$this->content->template['message_2024']="Subir";
//mal angesehen.
$this->content->template['message_2025']="visto  veces.";

// forum.html

//Menü überspringen
$this->content->template['message_2026']="Saltar menú";
//Login
$this->content->template['message_2027']="Login";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2028']="Por favor, compruebe sus registros";
//Username
$this->content->template['message_2029']="Nombre de usuario";
//Passwort
$this->content->template['message_2030']="Contraseña";
//einloggen
$this->content->template['message_2031']="Registrar";
//Registrierung.
$this->content->template['message_2032']="Registro";
// Account bearbeiten
$this->content->template['message_2033']=" Editar cuenta";
//ausloggen
$this->content->template['message_2034']="salir";
//Suchbegriff hier eingeben
$this->content->template['message_2035']="Introducir aquí concepto de búsqueda";
//Im Forum
$this->content->template['message_2036']="En el foro ";
//Finden
$this->content->template['message_2037']="Encontrar";
//Zurück zur Übersicht
$this->content->template['message_2038']="Volver a la vista general";
//Die Resultate Ihrer Suche:
$this->content->template['message_2039']="Los resultados de su búsqueda:";
//Hier finden Sie alle Foren auf
$this->content->template['message_2040']="Aquí dispone usted de todos los foros";
//Forum
$this->content->template['message_2041']="Foro";

//forumthread.html

//Menü überspringen
$this->content->template['message_2042']="Saltar menú";
//Login
$this->content->template['message_2043']="Login";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2044']="Por favor compruebe sus registros";
//Username
$this->content->template['message_2045']="Nombre de usuario";
//Passwort
$this->content->template['message_2046']="Contraseña";
//einloggen
$this->content->template['message_2047']="Registrar";
//Account bearbeiten
$this->content->template['message_2048']="Editar cuenta";
//ausloggen
$this->content->template['message_2049']="Salir";
//zurück zur Übersicht
$this->content->template['message_2050']="Volver a la vista general";
//Suchbegriff hier eingeben
$this->content->template['message_2051']="Introducir concepto de búsqueda";
//Im Forum
$this->content->template['message_2052']="En el foro";
//Finden
$this->content->template['message_2053']="Encontrar";
//Eintrag ändern:
$this->content->template['message_2054']="Modificar registro:";
//Hier wird der Eintrag geändert.
$this->content->template['message_2055']="Aquí se modifica el registro.";
//Autor
$this->content->template['message_2056']="Autor";
//Thema
$this->content->template['message_2057']="Tema";
//Eingabe und Formatierung der Inhalte:
$this->content->template['message_2058']="Introducción de los contenidos:";
//Beitrag
$this->content->template['message_2059']="Contribución";
//eintragen
$this->content->template['message_2060']="Registrar";
//Diesen Beitrag editieren
$this->content->template['message_2061']="Editar esta contribución";
//Hits
$this->content->template['message_2062']="Hits";
//Beitrag
$this->content->template['message_2063']="texttext";
//als Antwort auf:
$this->content->template['message_2064']="como respuesta a:";
//schreiben?
$this->content->template['message_2065']="¿Escribir?";
//Hier kann eine Eintrag in das Forum gemacht werden
$this->content->template['message_2066']="Contribución para el foro";
//Registrierung.
$this->content->template['message_2067']="Registro.";

// guestbook.html

//Direkt zum einloggen.
$this->content->template['message_2068']="Directamente a registrar.";
//Das Gästebuch wurde bereits
$this->content->template['message_2069']="El libro de invitados ya ha sido";
//mal angesehen.
$this->content->template['message_2070']="Visto   veces.";
//Dieser Link öffnet den Artikel
$this->content->template['message_2071']="Este enlace abre el artículo";
//im selben Fenster.
$this->content->template['message_2072']="En la misma ventana.";
//Gästebucheintrag von
$this->content->template['message_2073']="Registro del libro de invitados de ";
//Kommentar ins
$this->content->template['message_2074']="Comentario en el   ";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2075']="Escribir comentario:";
//Autor:
$this->content->template['message_2076']="Autor: ";
//Thema:
$this->content->template['message_2077']="Tema:  ";
// Beitrag:
$this->content->template['message_2078']=" Contribución:";
//eintragen
$this->content->template['message_2079']="Registrar  ";
//Besucher seit 20.04.2004.
$this->content->template['message_2080']="Visitante desde 20.04.2004.";
//Optionen zu diesen Artikel:
$this->content->template['message_2081']="Opciones de este artículo:";
//DruckVersion dieses Artikels
$this->content->template['message_2082']="Versión para imprimir ";

// inhalt.php

//Direkt zum einloggen.
$this->content->template['message_2083']="Directamente a registrar.";
//Besucher.
$this->content->template['message_2084']="Vistante. ";

// kontakt.php

// <h2>Kontaktformular</h2> <p>Wenn Sie uns direkt erreichen wollen, können Sie das über unser Kontaktformular machen.</p> <p>Bitte füllen Sie alle Felder aus, damit wir Ihnen auch antworten können.</p>
$this->content->template['message_2085']=" <h2>Formulario de contacto</h2>
 <p>Si usted quiere contactar directamente con nosotros, lo puede hacer por medio de nuestro formulario de contacto.</p>
 <p>por favor, cumplimente todos los campos, para que le podamos responder.</p>";
//Kontakt zu
$this->content->template['message_2086']="Contacto con ";
//Name:
$this->content->template['message_2087']="Nombre";
//Hier bitte Ihren Namen eingeben
$this->content->template['message_2088']="Por favor introduzca aquí su nombre ";
//Ihre <span lang']="en">E-Mail</span>:
$this->content->template['message_2089']="Su <span lang=\"en\">e-Mail</span>:";
//Hier bitte Ihre E-Mail Adresse eingeben
$this->content->template['message_2090']="Por favor, introduzca aquí su dirección de correo electrónico";
//Ihre Nachricht:
$this->content->template['message_2091']="Su mensaje: ";
//Hier bitte Ihre Nachricht eingeben
$this->content->template['message_2092']="Por favor, introduzca aquí su mensaje ";
//- Nachricht -
$this->content->template['message_2093']="- Mensaje -";
//<h2>Ihre Nachricht wurde übermittelt.</h2><p>Vielen Dank für Ihre Interesse, wir werden uns so bald wie möglich mit Ihnen in Verbindung setzen.</p><p>Sie können nun über das Menü links fortfahren.</p>
$this->content->template['message_2094']="<h2>Su mensaje ha sido transmitido.</h2>
<p>Muchas gracias por su interés, nos pondremos en contacto con usted a la mayor brevedad posible.</p>
<p>Usted puede continuar ahora por medio de los enlaces del menú.</p>  ";

// print.php

//Autor:
$this->content->template['message_2095']="Autor:";
//Kommentar von
$this->content->template['message_2096']="Comentario de ";

// profil.html

//<h2>Login</h2>    <p>Sie können hier einen Account für sich erstellen. Sämtliche Daten die in die Datenbank eingetragen werden, werden Ihnen auch per Email zugestellt.</p>
$this->content->template['message_2097']="<h2>Login</h2>
    <p>Usted puede crear aquí una cuenta para usted. Todos los datos que se registran en la base de datos, también se ponen a su disposición por correo electrónico.</p><p>Los campos identificados con un * tienen que cumplimentarse obligatoriamente.</p><p>En principio sólo se necesita un nombre de usuario, una contraseña y la dirección de e-Mail.</p> <p>Aquí podrá conocer algo más sobre nuestra <a href=\"#\">Protección de datos</a></p>";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2098']="Aquí puede registrar usted los datos para su cuenta.";
//Username:
$this->content->template['message_2099']="Nombre de usuario: ";
// Emailadresse:
$this->content->template['message_2100']=" Dirección de correo electrónico:";
//Passwort:
$this->content->template['message_2101']="Contraseña:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Contraseña (para comprobación):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']=" ¿Desea recibir un mensaje cuando se responda a su contribución en el foro? ";
//Antwortmail?
$this->content->template['message_2104']="¿Correo electrónico de respuesta?";
// erstellen
$this->content->template['message_2105']=" Crear";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Aquí puede editar sus datos ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Aquí puede usted registrar los datos para su cuenta. ";
//Username:
$this->content->template['message_2108']="Nombre de usuario:";
// Emailadresse:
$this->content->template['message_2109']=" Dirección de correo electrónico:";
//Neues Passwort:
$this->content->template['message_2110']="Nueva contraseña:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" ¿Desea usted recibir un mensaje cuando se responda a su contribución en el foro?";
// Antwortmail?
$this->content->template['message_2112']=" ¿Correo electrónico de respuesta? ";
//bearbeiten
$this->content->template['message_2113']="Editar ";

// weiter.html

//Dieser Link führt eine Seite zurück
$this->content->template['message_2114']="Este enlace retrocede una página";
//Die aktuell angezeigte Seite
$this->content->template['message_2115']="La página mostrada actualmente";
//Dieser Link führt zur
$this->content->template['message_2116']="Estre enlace lleva a";
//Seite
$this->content->template['message_2117']="Página";
//Eine Seite weiter
$this->content->template['message_2118']="Avanzar una página";
//Dieser Link führt eine Seite weiter
$this->content->template['message_2119']="Este enlace avanza una página";

//Hilfsmenü

//direkt zum Inhalt
$this->content->template['message_2120']="Directamente al contenido";
//zur Bereichsnavigation
$this->content->template['message_2121']="A la navegación de la zona";
// direkt zur Suche
$this->content->template['message_2122']=" Directamente a la búsqueda ";
//direkt zum einloggen
$this->content->template['message_2123']="Directamente a registrar";
//Frontend
$this->content->template['message_2124']="Frontend";
//ausloggen
$this->content->template['message_2125']="Salir";

//rightcollum.html

//ausprobieren
$this->content->template['message_2126']="probar";
//Login
$this->content->template['message_2127']="Login";
//Einloggen
$this->content->template['message_2128']="Registrar";
//Username
$this->content->template['message_2129']="Nombre de usuario";
//Passwort
$this->content->template['message_2130']="Contraseña";
//einloggen
$this->content->template['message_2131']="registrar";
//Registrierung.
$this->content->template['message_2132']="Registro.";
//Account bearbeiten
$this->content->template['message_2133']="Editar cuenta";
//ausloggen
$this->content->template['message_2134']="salir";
//Suche
$this->content->template['message_2135']="Búsqueda";
//Suchbegriff hier eingeben
$this->content->template['message_2136']="Introducir aquí concepto de búsqueda";
//eingeben
$this->content->template['message_2137']="introducir";
//Finden
$this->content->template['message_2138']="Encontrar";
//Styleswitcher
$this->content->template['message_2139']="Styleswitcher";
//wählen
$this->content->template['message_2140']="seleccionar";


//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2141']="Por favor, compruebe sus introducciones";
//am
$this->content->template['message_2142']="en";
//Bitte einen Suchbegriff eingeben.
$this->content->template['message_2143']="Por favor, introducir un concepto de búsqueda.";
//Es wurde kein Eintrag gefunden
$this->content->template['message_2145']="No se ha encontrado ningún registro";
//Links in diesem text
$this->content->template['message_2146']="Enlaces en este texto";
//Abkürzungen in diesem Text
$this->content->template['message_2147']="Abreviaturas en este texto";

$this->content->template['message_2152']="¿Copiar para mi?";
//
$this->content->template['message_2153']="¡Usted no tiene autorización!";
//
$this->content->template['message_2154']="Inténtelo de nuevo.";
//
$this->content->template['message_2155']="";
//Ihr Account wurde nach 4 falschen Login versuchen für 10 Minuten gesperrt!
$this->content->template['message_2156']="¡Su cuenta ha sido bloqueada por 10 minutos después de 4 intentos de registro erróneos!";
//
$this->content->template['message_2157']="El artículo ha sido enviado.";
//
$this->content->template['message_2158']="Newsletter";
//
$this->content->template['message_2159']="¿Desea recibir el Papoo Newsletter   ?";
//
$this->content->template['message_passwort_vergessen']="¿Ha olvidado su contraseña?";

// Texte für Downloads
// *******************
$this->content->template['download']['kein_recht'] = "You have no right to download this file.";
$this->content->template['download']['link_title'] = "Download will start in a new window.";
$this->content->template['download']['info_01'] = "Size";
$this->content->template['download']['info_02'] = "Count downloads";
$this->content->template['download']['info_03'] = "Last download at";
$this->content->template['download']['keine_datei'] = "Sorry. This file does not exist any more.";



$this->content->template['message_signatur'] = "Signatur";
$this->content->template['message_signatur_text'] = "Your personal signatur in the forum.";
//


// Texte für Modul "mod-efa_fontsize"
$this->content->template['mod_efa_fontsize']['text'] = "Font-Size:";
$this->content->template['mod_efa_fontsize']['bigger'] = "increase Font-Size";
$this->content->template['mod_efa_fontsize']['normal'] = "normal Font-Size";
$this->content->template['mod_efa_fontsize']['smaller'] = "decrease Font-Size";

// Texte für die lokalisierung von LightBox
$this->content->template['lightbox']['text_1a'] = "Press key";
$this->content->template['lightbox']['text_1b'] = "Click image";
$this->content->template['lightbox']['text_2'] = "to close this window.";

$this->content->template['Schriftart']='Schriftart';
$this->content->template['Schriftfarbe']='Schriftfarbe';
$this->content->template['auswaehlen']='auswählen';
$this->content->template['ueberschriften']='Überschriften';
$this->content->template['ueberschrift']='Überschrift';
$this->content->template['Auszeichnungen']='Auszeichnungen';
$this->content->template['abk_von']='Abkürzung von';
$this->content->template['acr_von']='Acronym von';
$this->content->template['zit_von']='Zitat von';
$this->content->template['message_2170']='Welche Überschrift? 1-6 sind möglich.';
$this->content->template['message_2171']='Zusätzliche logische Auszeichnungen';
$this->content->template['sprung_menu']='Sprung-Menue';
$this->content->template['spez_seiten']='Spezielle Seiten';
$this->content->template['message_2174']='Benutzername';
$this->content->template['message_2175']='Passwort';
$this->content->template['mensch']='Protección antispam';
$this->content->template['message_2177']='Por motivos de seguridad, el presente formulario cuenta con una protección antispam (para evitar el envío de correo no deseado). Por eso tendrá que realizar primero la operación que a continuación se indica para poder enviar el formulario:';
$this->content->template['message_2178']='Damit Sie dieses Formular absenden können, müssen Sie die hier abgebildete Kennzahl in das Feld darunter eintragen.';
$this->content->template['message_2179']='Zugangs-Code für Formular-Übertragung';
$this->content->template['message_2180']='Kennzahl';
/* NACH PROFESSIONELLER ÜBERSETZUNG ENTFALLEN
$this->content->template['message_2181']='Damit Sie dieses Formular absenden können, müssen Sie die folgende Aufgabe lösen.';*/
$this->content->template['message_2182']='Damit Sie dieses Formular absenden können, müssen Sie die folgenden Zeichen in der richtigen Reihenfolge eingeben.

Beispiel:
2. Zeichen: x; 1. Zeichen: 4; 3. Zeichen: s; ergibt "4xs";';
$this->content->template['message_2183']='Sie sind hier';
$this->content->template['message_2184']='Menübereich';
$this->content->template['message_2185']='Sprachauswahl';
$this->content->template['templsuche']='Suche';
$this->content->template['message_2187']='Rechte optische Spalte';
$this->content->template['message_2188']='Forenbereich';
$this->content->template['message_2189']='Forum';
$this->content->template['message_2190']='Letzter Beitrag';
$this->content->template['message_2191']='Themen';
$this->content->template['message_2192']='Beiträge';
$this->content->template['message_2193']='Gästebuchbereich';
$this->content->template['message_2194']='Hauptinhalt';
$this->content->template['message_2195']='Diesen Artikel editieren.';
$this->content->template['message_2196']='Volver';
$this->content->template['message_2197']='Inhaltsverzeichnis';
$this->content->template['message_2198']='Kontakt Formular';
$this->content->template['message_2199']='Userdaten bearbeiten';
$this->content->template['message_2200']='Ihr Account';
$this->content->template['message_2201']='Ihr Account ist aktiv und Sie können hier Ihre Daten ändern';
$this->content->template['message_2202']='Ihre Accountdaten';
$this->content->template['message_2203']='Zugangsdaten';
$this->content->template['message_2204']='Benutzername';
$this->content->template['message_2205']='Persönliche Daten';
$this->content->template['message_2206']='Vorname';
$this->content->template['message_2207']='Nachname';
$this->content->template['message_2208']='Adresse';
$this->content->template['message_2209']='Postleitzahl';
$this->content->template['message_2210']='Wohnort';
$this->content->template['message_2211']='Forum';
$this->content->template['message_2212']='Welche Forum Ansicht möchten Sie?';
$this->content->template['message_2213']='Thread Ansicht';
$this->content->template['message_2214']='Board Ansicht';
$this->content->template['message_2215']='Thread + Ansicht';
$this->content->template['message_2216']='Weitere Optionen';
$this->content->template['message_2217']='Möchten Sie dauerhaft eingeloggt bleiben (erfordert Cookies)?';
$this->content->template['message_2218']='Dauerhaft einloggen?';
$this->content->template['message_2219']='Wählen Sie hier Ihren dauerhaften Style aus, mit dem Sie immer automatisch eingeloggt werden.';
$this->content->template['message_2220']='Account bearbeiten';
$this->content->template['message_2221']='Daten speichern?';
$this->content->template['message_2222']='speichern';
$this->content->template['message_2223']='Account löschen';
$this->content->template['message_2224']='Wollen Sie den Account löschen?';
$this->content->template['message_2225']='löschen';
$this->content->template['message_2226']='Strasse und Hausnummer';
$this->content->template['message_2227']='Die Daten.';
$this->content->template['reservierung']='Reservierung';
$this->content->template['message_2229']='Tag';
$this->content->template['message_2230']='Monat';
$this->content->template['message_2231']='Nächte';
$this->content->template['message_2232']='Einzelzimmer';
$this->content->template['message_2233']='Doppelzimmer';
$this->content->template['message_2234']='prüfen';
$this->content->template['message_2235']='Ihr Account wurde aktiviert!';
$this->content->template['message_2236']='Schriftvergrößerung';
$this->content->template['message_2237']='Breadcrump Menü';
$this->content->template['message_2238']='Kopftext';
$this->content->template['message_2239']='bbcode Editor Bild';
$this->content->template['message_2240']='bbcode Editor';
$this->content->template['message_2241']='Login Formular';
$this->content->template['message_2242']='Spamschutz';
$this->content->template['message_2243']='Passwort vergessen';
$this->content->template['message_2244']='Weiter';
$this->content->template['message_2245']='Dritte Spalte';
$this->content->template['message_2246']='Login';
$this->content->template['message_2247']='Menü Ebene 0';
$this->content->template['message_2248']='Menü Ebene 1';
$this->content->template['message_2249']='Menü Ebene 2';
$this->content->template['message_2250']='Menü Ebene 3';
$this->content->template['message_2251']='Menü Ebene Links';
$this->content->template['message_2252']='News';
$this->content->template['message_2253']='Fuss';
$this->content->template['message_2254']='Inhalt Links';
$this->content->template['message_2255']='Inhalt Mitte';
$this->content->template['message_2256']='Inhalt Rechts';
$this->content->template['message_2257']='Bitte alle Felder ausfüllen!';
$this->content->template['message_2258']='Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein.';
$this->content->template['message_2259']='Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein.';
$this->content->template['message_2260']='Ihr Passwort wurde versendet. Sie sollten in wenigen Minuten eine Emai mit Ihrem neuen Passwort erhalten.';
$this->content->template['message_2261']='Neues Passwort erstellen.';
$this->content->template['message_2262']='Passwort erneuern';
$this->content->template['mod_access1']='Access Keypad';
$this->content->template['mod_access2']='Zugangstasten des Accesskey-Pad und deren Funktion';
$this->content->template['mod_access3']='Tasten';
$this->content->template['mod_access4']='Funktion';
$this->content->template['mod_access5']='Alt+0';
$this->content->template['mod_access6']='Startseite';
$this->content->template['mod_access7']='Alt+3';
$this->content->template['mod_access8']='Vorherige Seite';
$this->content->template['mod_access9']='Alt+6';
$this->content->template['mod_access10']='Inhaltsverzeichnis';
$this->content->template['mod_access11']='Alt+7';
$this->content->template['mod_access12']='Suchfunktion';
$this->content->template['mod_access13']='Alt+8';
$this->content->template['mod_access14']='Direkt zum Inhalt';
$this->content->template['mod_access15']='Alt+9';
$this->content->template['mod_access16']='Kontaktseite';
$this->content->template['mod_access17']='Sitemap';
$this->content->template['message_2263']='Melden Sie sich jetzt bitte in der nebenstehenden Anmeldemaske an, dann können Sie Ihren Account nutzen.';
$this->content->template['nbs']=' ';
$this->content->template['contact']['mail1']='Kontaktformular der Seite ';
$this->content->template['templ_serg']='Suchergebnis';
$this->content->template['templ_ergbn']='Ergebnis Ihrer Suche nach ';
$this->content->template['templ_res']='Resultate';
$this->content->template['templ_bis']='bis';
$this->content->template['templ_insg']='von insgesamt';
// Plural
$this->content->template['templ_seiten']='páginas';
// Singular
$this->content->template['templ_seite']='página';
$this->content->template['templ_Stand']='Stand';
$this->content->template['templ_erst']='Erstellt am';
$this->content->template['templ_Erweiterte']='Erweiterte';
$this->content->template['templ_susst']='Suche starten';
$this->content->template['templ_Suchtyp']='Suchtyp';
$this->content->template['templ_finein']='Finde irgendeinen der Begriffe';
$this->content->template['templ_oder']='Oder  ';
$this->content->template['templ_findall']='Finde alle Begriffe';
$this->content->template['templ_und']='Und  ';
$this->content->template['templ_findd']='Finde diesen Begriff';
$this->content->template['templ_Exakt']='Exakt';
$this->content->template['templ_anergb1']='Anzahl Ergebnisse pro Seite (Treffer)';
$this->content->template['templ_sort']='Sortierung';
$this->content->template['templ_z10']='Zeige 10 Ergebnisse je Seite';
$this->content->template['templ_z20']='Zeige 20 Ergebnisse je Seite';
$this->content->template['templ_z30']='Zeige 30 Ergebnisse je Seite';
$this->content->template['templ_z100']='Zeige 100 Ergebnisse je Seite';
$this->content->template['templ_all100']='Alle (max. 100)';
$this->content->template['templ_sortti']='Sortiere die Ergebnisse nach dem Titel der Artikel';
$this->content->template['templ_Titel']='Titel';
$this->content->template['templ_sortart']='Sortiere die Ergebnisse nach dem Änderungsdatum der Artikel';
$this->content->template['templ_datl']='Datum der letzten Aktualisierung';
$this->content->template['templ_aufst']='Gib die Ergebnisse in aufsteigender Reihenfolge aus';
$this->content->template['templ_ergba']='Gib die Ergebnisse in absteigender Reihenfolge aus';
$this->content->template['templ_Absteigend']='Absteigend';
$this->content->template['tplarticle_coment']='Kommentare';
$this->content->template['tplforum_forum']='Forum';
$this->content->template['tplarticle_schreiben']='schreiben?';
$this->content->template['tplarticle_mail1']='';

/**
errors
*/
// wrong Email adress
$this->content->template['error_1']="Lamentablemente esta dirección de e-Mail no es correcta. Probablemente se haya equivocado al introducirla.";