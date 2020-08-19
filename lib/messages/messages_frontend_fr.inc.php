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
$this->content->template['message_1']="L'e-mail a été transmis.";
// Text über dem Forum
$this->content->template['message_2']="Et, ici, la liste complète des forums.";
// Die letzten zehn Einträge
$this->content->template['message_3']=" Les dix dernières entrées";
$this->content->template['message_3_1']=" Les dix dernières entrées ";
$this->content->template['message_3_2']="Liste des thèmes actuels";
// $neuer = "Ecrire un nouvel article ?";
$this->content->template['message_4']=" Ecrire un nouvel article ?";
// Kein Ergebnis
$this->content->template['message_5']="Malheureusement, rien n'a été trouvé. Veuillez préciser votre terme de recherche pour que nous puissions vous aider.";
// Kein Zugriff auf Message
$this->content->template['message_6']="Vous n'avez aucun accès à ce message ou celui-ci n'existe pas.";
// Sie müssen sich einloggen
$this->content->template['message_7']="Vous devez ouvrir une session pour pouvoir écrire un article.";
//<p>Diese Nachricht existiert nicht. \n</body></html>\n
$this->content->template['message_8']="<p> Ce message n'existe pas. \n</body></html>\n";
//<p>Dieses Forum existiert nicht, bitte wählen <a href=\"./forum.php\">Sie ein anderes.</a>.\n</div></body></html>\n
$this->content->template['message_9']="<p> Ce forum n'existe pas, veuillez en choisir <a href=\"./forum.php\">un autre.</a>.\n</div></body></html>\n";
//<p>Sie müssen einen Text eingeben!</p>
$this->content->template['message_10']="<p> Vous devez saisir un texte!</p>";
// Sie haben leider keine Rechte um einen Beitrag schreiben zu können.
$this->content->template['message_11']=" Vous n'avez malheureusement aucun droit vous permettant d'écrire un article.";
// Ihre Daten wurden eingetragen und Sie sind jetzt eingeloggt.
$this->content->template['message_12']="<h1> Vos données ont été inscrites. </h1><p>Vous devriez recevoir un e-mail dans quelques secondes. Suivez les instructions données dans le e-mail pour activer votre compte.</p>";
//weitere Seiten
$this->content->template['message_73']="Revenez à la pager précédente";
//weitere Seiten
$this->content->template['message_86']="Passez à la page suivante";
/**index.html*/
//Dieser Link führt zur Startseite von
$this->content->template['message_2000']="Ce lien mène à la page de départ de";
//Den obigen Artikel versenden.
$this->content->template['message_2001']="Envoyer l'article ci-dessus";
//Empfänger-Email:
$this->content->template['message_2002']="E-mail destinataire:";
// Ihre Email:
$this->content->template['message_2003']="Votre e-mail ";
//Was möchten Sie gerne senden?
$this->content->template['message_2004']="Qu'aimeriez-vous envoyer?";
//Nur den Link
$this->content->template['message_2005']="Uniquement le lien";
//Den ganzen Text
$this->content->template['message_2006']="Tout le texte";
//Kommentar
$this->content->template['message_2007']="Commentaire";
//senden
$this->content->template['message_2008']="Envoyer";
//Direkt zum Einloggen.
$this->content->template['message_2009']="Ouvrir directement une session.";
//Autor:
$this->content->template['message_2010']="Auteur:";
//Dieser Artikel wurde bereits
$this->content->template['message_2011']=" Cet article a déjà été";
//Dieser Link öffnet den Artikel
$this->content->template['message_2012']=" Ce lien ouvre l'article";
//im selben Fenster.
$this->content->template['message_2013']="dans la même fenêtre.";
//Optionen zu diesem Artikel:
$this->content->template['message_2014']=" Options concernant cet article:";
//Sie können diesen Artikel versenden/empfehlen
$this->content->template['message_2015']=" vous pouvez envoyer/recommander cet article";
// DruckVersion dieses Artikels
$this->content->template['message_2016']="Version imprimée de cet article";
//Kommentar von
$this->content->template['message_2017']="Commentaire de";
// Kommentar zu
$this->content->template['message_2018']="Commentaire au sujet de";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2019']="Ecrire commentaire:";
//Thema:
$this->content->template['message_2020']="Thème:";
//Beitrag:
$this->content->template['message_2021']="Article:";
//Eintragen
$this->content->template['message_2022']="Inscrire";
//Besucher seit 20.04.2004.
$this->content->template['message_2023']="Visiteur";
// Zurück
$this->content->template['message_2196']="Retour";
//Nach oben.
$this->content->template['message_2024']="Vers le haut.";
//mal angesehen.
$this->content->template['message_2025']="Regarder.";

// forum.html

//Menü überspringen
$this->content->template['message_2026']="Sauter menu";
//Login
$this->content->template['message_2027']="Ouverture de session";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2028']="Veuillez vérifier vos saisies";
//Username
$this->content->template['message_2029']="Nom d'utilisateur";
//Passwort
$this->content->template['message_2030']="Mot de passe";
//einloggen
$this->content->template['message_2031']="Ouvrir session";
//Registrierung.
$this->content->template['message_2032']="Enregistrement.";
// Account bearbeiten
$this->content->template['message_2033']="Traiter compte";
//ausloggen
$this->content->template['message_2034']="Fermer session";
//Suchbegriff hier eingeben
$this->content->template['message_2035']="Saisir ici le terme de recherche";
//Im Forum
$this->content->template['message_2036']="Sur le forum";
//Finden
$this->content->template['message_2037']="Trouver";
//Zurück zur Übersicht
$this->content->template['message_2038']="Retour à vue d'ensemble";
//Die Resultate Ihrer Suche:
$this->content->template['message_2039']="Les résultats de votre recherche:";
//Hier finden Sie alle Foren auf
$this->content->template['message_2040']="Ici, vous trouverez tous les forums";
//Forum
$this->content->template['message_2041']="Forum";

//forumthread.html

//Menü überspringen
$this->content->template['message_2042']="Sauter menu";
//Login
$this->content->template['message_2043']="Ouverture de session";
//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2044']="Veuillez vérifier vos saisies";
//Username
$this->content->template['message_2045']="Nom d'utilisateur";
//Passwort
$this->content->template['message_2046']="Passeport";
//einloggen
$this->content->template['message_2047']="Ouvrir une session";
//Account bearbeiten
$this->content->template['message_2048']="Traiter compte";
//ausloggen
$this->content->template['message_2049']="Fermer session";
//zurück zur Übersicht
$this->content->template['message_2050']="Retour à vue densemble";
//Suchbegriff hier eingeben
$this->content->template['message_2051']="Saisir ici terme de recherche";
//Im Forum
$this->content->template['message_2052']="Sur le forum";
//Finden
$this->content->template['message_2053']="Trouver";
//Eintrag ändern:
$this->content->template['message_2054']="Modifier entrée:";
//Hier wird der Eintrag geändert.
$this->content->template['message_2055']="Ici, l'entrée est modifiée.";
//Autor
$this->content->template['message_2056']="Auteur";
//Thema
$this->content->template['message_2057']="Thème";
//Eingabe und Formatierung der Inhalte:
$this->content->template['message_2058']="Saisie des contenus:";
//Beitrag
$this->content->template['message_2059']="Article";
//eintragen
$this->content->template['message_2060']="Inscrire";
//Diesen Beitrag editieren
$this->content->template['message_2061']="Editer cet article";
//Hits
$this->content->template['message_2062']="Hits";
//Beitrag
$this->content->template['message_2063']="Texte de texte";
//als Antwort auf:
$this->content->template['message_2064']="Comme réponse à:";
//schreiben?
$this->content->template['message_2065']="Ecrire?";
//Hier kann eine Eintrag in das Forum gemacht werden
$this->content->template['message_2066']="Article pour forum";
//Registrierung.
$this->content->template['message_2067']="Enregistrement.";

// guestbook.html

//Direkt zum einloggen.
$this->content->template['message_2068']="Ouvrir directement session.";
//Das Gästebuch wurde bereits
$this->content->template['message_2069']="Le livre des invités a déjà été";
//mal angesehen.
$this->content->template['message_2070']="consulté.";
//Dieser Link öffnet den Artikel
$this->content->template['message_2071']="Ce lien ouvre l'article";
//im selben Fenster.
$this->content->template['message_2072']="dans la même fenêtre.";
//Gästebucheintrag von
$this->content->template['message_2073']="Entrée au livre des invités de";
//Kommentar ins
$this->content->template['message_2074']="Commentaire dans";
//Hier kann ein Kommentar geschrieben werden.
$this->content->template['message_2075']="Ecrire commentaire:";
//Autor:
$this->content->template['message_2076']="Auteur: ";
//Thema:
$this->content->template['message_2077']="Thème:  ";
// Beitrag:
$this->content->template['message_2078']=" Article:";
//eintragen
$this->content->template['message_2079']="Inscrire";
//Besucher seit 20.04.2004.
$this->content->template['message_2080']="Visiteurs depuis le 20.4.2004.";
//Optionen zu diesen Artikel:
$this->content->template['message_2081']="Options concernant cet article:";
//DruckVersion dieses Artikels
$this->content->template['message_2082']="Version imprimée de cet article";

// inhalt.php

//Direkt zum einloggen.
$this->content->template['message_2083']="Ouvrir directement session.";
//Besucher.
$this->content->template['message_2084']="Visiteurs.";

// kontakt.php

// <h2>Kontaktformular</h2> <p>Wenn Sie uns direkt erreichen wollen, können Sie das über unser Kontaktformular machen S.</p> <p> Bitte füllen Sie alle Felder aus, damit wir Ihnen auch antworten können.</p>
$this->content->template['message_2085']=" <h2>Formulaire de contact</h2>
 <p>i vous voulez nous joindre directement, vous pouvez le faire par le biais de notre formulaire de contact.</p>
 <p> Veuillez remplir tous les champs pour que nous puissions aussi vous répondre.</p>";
//Kontakt zu
$this->content->template['message_2086']="Contact avec";
//Name:
$this->content->template['message_2087']="Votre nom";
//Hier bitte Ihren Namen eingeben
$this->content->template['message_2088']="Veuillez saisir votre nom ici";
//Ihre <span lang']="en">E-Mail</span>:
$this->content->template['message_2089']="Votre adresse <span lang=\"en\">E-mail</span>:";
//Hier bitte Ihre E-Mail Adresse eingeben
$this->content->template['message_2090']="Veuillez saisir votre adresse e-mail ici";
//Ihre Nachricht:
$this->content->template['message_2091']="Votre message: ";
//Hier bitte Ihre Nachricht eingeben
$this->content->template['message_2092']="Veuillez saisir votre message ici";
//- Nachricht -
$this->content->template['message_2093']="- Message -";
//<h2>Ihre Nachricht wurde übermittelt.</h2><p>Vielen Dank für Ihre Interesse, wir werden uns so bald wie möglich mit Ihnen in Verbindung setzen.</p><p>Sie können nun über das Menü links fortfahren.</p>
$this->content->template['message_2094']="<h2>Votre message a été transmis.</h2>
<p>Merci beaucoup pour votre intérêt; nous allons reprendre contact avec vous le plus rapidement possible.</p>
<p>Vous pouvez maintenant continuer avec le menu de gauche.</p>  ";

// print.php

//Autor:
$this->content->template['message_2095']="Auteur:";
//Kommentar von
$this->content->template['message_2096']="Commentaire de";

// profil.html

//<h2>Login</h2>    <p>Sie können hier einen Account für sich erstellen. Sämtliche Daten die in die Datenbank eingetragen werden, werden Ihnen auch per Email zugestellt.</p>
$this->content->template['message_2097']="<h2>Login</h2>
    <p>Vous pouvez créer ici un compte pour vous. La totalité des données qui vont être inscrites dans la banque de données vous seront communiquées aussi par e-mail.</p><p>Les champs marqués d'un * doivent absolument être remplis.</p><p>En principe, on n'a besoin que d'un nom d'utilisateur, d'un mot de passe et de l'adresse e-mail.</p> <p>Ici, vous pouvez consulter nos informations sur la <a href=\"#\">protection des données</a></p>";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2098']="Ici, vous pouvez inscrire les données pour votre compte.";
//Username:
$this->content->template['message_2099']="Nom d'utilisateur: ";
// Emailadresse:
$this->content->template['message_2100']="Adresse e-mail:";
//Passwort:
$this->content->template['message_2101']="Mot de passe:";
//Passwort (zur Überprüfung):
$this->content->template['message_2102']="Mot de passe (pour vérification):";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2103']="Souhaitez-vous recevoir un e-mail si une réponse à votre article a été donnée sur le forum? ";
//Antwortmail?
$this->content->template['message_2104']="mail de réponse?";
// erstellen
$this->content->template['message_2105']="Réaliser";
//Hier können Sie Ihre Daten bearbeiten
$this->content->template['message_2106']="Ici, vous pouvez traiter vos données ";
//Hier können Sie die Daten für Ihren Account eintragen.
$this->content->template['message_2107']="Ici, vous pouvez inscrire les données pour votre compte.";
//Username:
$this->content->template['message_2108']="Nom d'utilisateur:";
// Emailadresse:
$this->content->template['message_2109']="Adresse e-mail:";
//Neues Passwort:
$this->content->template['message_2110']="Nouveau mot de passe:";
// Möchten Sie eine Mail erhalten wenn auf Ihren Beitrag im Forum geantwortet wurde?
$this->content->template['message_2111']=" Souhaitez-vous recevoir un e-mail si une réponse à votre article a été donnée sur le forum?";
// Antwortmail?
$this->content->template['message_2112']="mail de réponse? ";
//bearbeiten
$this->content->template['message_2113']="Traiter";

// weiter.html

//Dieser Link führt eine Seite zurück
$this->content->template['message_2114']="Ce lien vous fait revenir une page en arrière";
//Die aktuell angezeigte Seite
$this->content->template['message_2115']="La page actuellement affichée";
//Dieser Link führt zur
$this->content->template['message_2116']="Ce lien mène à";
//Seite
$this->content->template['message_2117']="page";
//Eine Seite weiter
$this->content->template['message_2118']="une page plus loin";
//Dieser Link führt eine Seite weiter
$this->content->template['message_2119']="Ce lien mène une page plus loin";

//Hilfsmenü
//direkt zum Inhalt
$this->content->template['message_2120']="Directement au contenu";
//zur Bereichsnavigation
$this->content->template['message_2121']="Pour la navigation dans les domaines";
// direkt zur Suche
$this->content->template['message_2122']="Directement à la recherche";
//direkt zum einloggen
$this->content->template['message_2123']="Ouvrir directement session";
//Frontend
$this->content->template['message_2124']="Frontal";
//ausloggen
$this->content->template['message_2125']="Fermer session";

//rightcollum.html

//ausprobieren
$this->content->template['message_2126']="essayer";
//Login
$this->content->template['message_2127']="Ouverture de session";
//Einloggen
$this->content->template['message_2128']="Ouvrir session";
//Username
$this->content->template['message_2129']="Nom d'utilisateur";
//Passwort
$this->content->template['message_2130']="Mot de passe";
//einloggen
$this->content->template['message_2131']="Ouvrir session";
//Registrierung.
$this->content->template['message_2132']="Enregistrement.";
//Account bearbeiten
$this->content->template['message_2133']="Traiter compte";
//ausloggen
$this->content->template['message_2134']="Fermer session";
//Suche
$this->content->template['message_2135']="Recherche";
//Suchbegriff hier eingeben
$this->content->template['message_2136']="Saisir ici terme de recherche";
//eingeben
$this->content->template['message_2137']="Saisir";
//Finden
$this->content->template['message_2138']="Trouver";
//Styleswitcher
$this->content->template['message_2139']="Styleswitcher";
//wählen
$this->content->template['message_2140']="Choisir";


//Bitte überprüfen Sie Ihre Eingaben
$this->content->template['message_2141']="Veuillez vérifier vos saisies";
//am
$this->content->template['message_2142']="le";
//Bitte einen Suchbegriff eingeben.
$this->content->template['message_2143']="Veuillez saisir un terme de recherche.";
//Es wurde kein Eintrag gefunden
$this->content->template['message_2145']="Aucune entrée n'a été trouvée";
//Links in diesem text
$this->content->template['message_2146']="Liens dans ce texte";
//Abkürzungen in diesem Text
$this->content->template['message_2147']="Abréviations dans ce texte";

$this->content->template['message_2152']="Copier pour moi?";
//
$this->content->template['message_2153']="Vous ne possédez pas d'autorisation!";
//
$this->content->template['message_2154']="Essayez encore une fois.";
//
$this->content->template['message_2155']="";
//Ihr Account wurde nach 4 falschen Login versuchen für 10 Minuten gesperrt!
$this->content->template['message_2156']="Votre compte a été bloqué pour une durée de dix minutes après quatre tentatives d'ouverture de session soldées par un échec!";
//
$this->content->template['message_2157']="L'article a été envoyé.";
//
$this->content->template['message_2158']="Infolettre";
//
$this->content->template['message_2159']="Souhaitez-vous recevoir la newsletter de Papoo?";
//
$this->content->template['message_passwort_vergessen']="Oublié mot de passe?";

// Texte für Downloads
// *******************
$this->content->template['download']['kein_recht'] = "Vous n'avez pas le droit de télécharder ce fichier.";
$this->content->template['download']['link_title'] = "Le téléchargement va s'ouvrir dans une autre fenêtre.";
$this->content->template['download']['info_01'] = "Taille";
$this->content->template['download']['info_02'] = "Total des téléchargements";
$this->content->template['download']['info_03'] = "Dernier téléchargement à";
$this->content->template['download']['keine_datei'] = "SDésolé, ce fichier n'esiste plus.";



$this->content->template['message_signatur'] = "Signatur";
$this->content->template['message_signatur_text'] = "Your personal signatur in the forum.";
//


// Texte für Modul "mod-efa_fontsize"
$this->content->template['mod_efa_fontsize']['text'] = "Taille des caractères:";
$this->content->template['mod_efa_fontsize']['bigger'] = "agrandir taille des caractères";
$this->content->template['mod_efa_fontsize']['normal'] = "normal taille des caractères";
$this->content->template['mod_efa_fontsize']['smaller'] = "réduire taille des caractères";

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
$this->content->template['mensch']='Protection anti-spam';
$this->content->template['message_2177']='Par mesure de sécurité, le présent formulaire est doté d\'une protection anti-spam. ';
$this->content->template['message_2178']=' Veuillez inscrire le résultat de cette petite opération de calcul afin de pouvoir envoyer le formulaire.';
$this->content->template['message_2179']='Zugangs-Code für Formular-Übertragung';
$this->content->template['message_2180']='Kennzahl';
$this->content->template['message_2181']='Veuillez inscrire le résultat de cette petite opération de calcul afin de pouvoir envoyer le formulaire.';
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
$this->content->template['message_2197']='Plan du site';
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
$this->content->template['message_2242']='protection contre les spams';
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

//$this->content->template['message_2258']='Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein.';
//$this->content->template['message_2259']='Wenn Sie Ihr Passwort vergessen haben geben Sie entweder Ihren Benutzernamen oder ihre E-Mail Adresse in das Feld ein.';
//$this->content->template['message_2260']='Ihr Passwort wurde versendet. Sie sollten in wenigen Minuten eine Emai mit Ihrem neuen Passwort erhalten.';
//$this->content->template['message_2261']='Neues Passwort erstellen.';
//$this->content->template['message_2262']='Passwort erneuern';
$this->content->template['message_2258']='Indiquez votre nom d\'utilisateur ou votre adresse électronique dans le champ du formulaire.
Le système va vous envoyer automatiquement un nouveau mot de passe:';
$this->content->template['message_2259']='Indiquez votre nom d\'utilisateur ou votre adresse électronique dans le champ du formulaire.
Le système va vous envoyer automatiquement un nouveau mot de passe:';
$this->content->template['message_2260']='Merci. Un nouveau mot de passe va vous être envoyé dans un bref délai.';
$this->content->template['message_2261']='Créer un nouveau mot de passe';
$this->content->template['message_2262']='Vous faut-il un nouveau mot de passe?';

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
$this->content->template['templ_serg']='Résultats de recherche';
$this->content->template['templ_ergbn']='Résultats de recherche pour ';
$this->content->template['templ_res']='Résultats';
$this->content->template['templ_bis']='à';
$this->content->template['templ_insg']='de Total';
// Plural
$this->content->template['templ_seiten']='pages';
// Singular
$this->content->template['templ_seite']='page';
$this->content->template['templ_Stand']='Stand';
$this->content->template['templ_erst']='Erstellt am';
$this->content->template['templ_Erweiterte']='Avancé';
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

$this->content->template['plugin_userlogin_logout']='Déconnexion';
/**
errors
*/
// wrong Email adress
$this->content->template['error_1']="Diese Email Adresse ist leider nicht in Ordnung. Wahrscheinlich haben Sie sich vertippt.";