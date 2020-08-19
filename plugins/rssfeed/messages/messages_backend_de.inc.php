<?php 
/*
* Alle messages für den RSS-Feed
*/

$this->content->template['message_5000']="Beschreibung";
$this->content->template['message_5001']="RSS Feed 0.9.6a";
$this->content->template['message_5002']="Originally (c) by Thomas Schoessow";
$this->content->template['message_5003']="Webseite des Autors (öffnet in neuem Fenster)";
$this->content->template['message_5004']="Webseite";
$this->content->template['message_5005']="Mailadresse";
$this->content->template['message_5006']="Mailadresse";
$this->content->template['message_5007']="RSS-Feed für Papoo";
$this->content->template['message_5008']="Mit diesem Plugin wird Papoo um einen RSS-Feed erweitert. Dieser Feed zeigt die X-letzten Artikel (konfigurierbar) als RSS-Feed an. Der erzeugte Feed validiert gemäß W3C. Er zeigt die Teaser der Artikel und einen Link auf den jeweiligen Artikel an.";
$this->content->template['message_5009']="";
$this->content->template['message_5010']="";
$this->content->template['message_5011']="In den RSS-Feed werden nur Dateien aufgenommen, die das Feld 'Artikel für RSS'und das Feld 'Dauerhaft veröffentlichen' gesetzt haben.";
$this->content->template['message_5012']="Wenn beim Validieren des Feed eine Meldung erscheint, die besagt, dass der Feed ein Charset 'xxx' benutzt, der Server aber 'yyy' liefert, dann hilft das Einfügen folgender Zeile in die .htaccess.";
$this->content->template['message_5013']="Der Link zur Feed-Datei (evtl. manuell erzeugen mit 777) lautet dann ";
$this->content->template['message_5014']="Link zum Feed";
$this->content->template['message_5015']="Externe RSS-Feed als statische Seiten anzeigen";
$this->content->template['message_5016']="Es ist nun ebenfalls möglich, bis zu 10 RSS-Feeds als Seiten in Papoo anzuzeigen. Die Einbindung der Seiten ist sehr einfach. Erstellen Sie einfach einen neuen Menüeintrag in Papoo und fügen Sie unter \"Einbindung des Links oder der Datei.\" eine Angabe ein wie 'plugin:rssfeed/templates/rssfeed_show.html', wenn Sie Feed 1 anzeigen lassen wollen. Für Feed 10 wäre das dann analog plugin:rssfeed/templates/rssfeed_show9.html.'";
$this->content->template['message_5017']="Die Feeds werden in einem Cache auf dem Server gehalten, dieser ist auf eine Dauer von 1 Stunde eingestellt.";
$this->content->template['message_5018']="Das Verzeichnis RSSCACHE muss mit CHMOD 777 eingestellt werden, damit die Feeds gecacht werden können.Das Verzeichnis RSSFEED im Plugins Verzeichnis muss ebenfalls mit Chmod 777 eingestellt werden.";
$this->content->template['message_5019']="Konfiguration des eigenen RSS-Feeds";
$this->content->template['message_5020']="Bitte tragen Sie hier die notwendigen Angaben zum Feed ein.";
$this->content->template['message_5021']="Titel ist der Titel, den der Feed haben soll";
$this->content->template['message_5022']="Das Feld Beschreibung sollte einen kurzen Abriss beinhalten, was der Inhalt des Feeds ist.";
$this->content->template['message_5023']="Das Feld Anzahl Artikel enthält die Anzahl der letzten Artikel, die im Feed angezeigt werden sollen.";
$this->content->template['message_5024']="Das Feld Sprache enthält den Wert für die Sprache des Feeds. 1 entspricht Deutsch 2, Englisch.";
$this->content->template['message_5025']="Das Prefix für den RSS-Feed. Bei Domänen ohne www lautet es http:// ansonsten http://www.";
$this->content->template['message_5026']="RSS-Feed Einstellungen";
$this->content->template['message_5027']="Titel des Feeds";
$this->content->template['message_5028']="Enthält den Titel des RSS-Feed";
$this->content->template['message_5029']="Beschreibung";
$this->content->template['message_5030']="Enthält die Beschreibung des RSS-Feed";
$this->content->template['message_5031']="Anzahl Artikel";
$this->content->template['message_5032']="Enthält die Anzahl der angezeigten Artikel des RSS-Feed";
$this->content->template['message_5033']="Sprache";
$this->content->template['message_5034']="Angabe der Sprache des RSS-Feed (1=Deutsch, 2=Englisch)";
$this->content->template['message_5035']="Prefix";
$this->content->template['message_5036']="Enthält das Prefix für den RSS-Feed. Bei Domänen ohne www lautet es http:// ansonsten http://www.";
$this->content->template['message_5037']="Nachfolgend können Sie bis zu 10 URL zu RSS-Feed's eintragen, die dann als Artikel in Papoo dargestellt werden können.";
$this->content->template['message_5038']="Konfiguration Feed";
$this->content->template['message_5039']="Bitte tragen Sie hier die URL zum Feed ein.";
$this->content->template['message_5040']="Fehler im Formular:";
$this->content->template['message_5041']="Feed-Url";
$this->content->template['message_5042']="Enthält die URL des Feed";
$this->content->template['message_5043']="Daten des Feed";
$this->content->template['message_5044']="Feed erstellen";
$this->content->template['message_5045']="Mit dem Klick auf den unten aufgeführten Button wird der Feed erstellt.";
$this->content->template['message_5046']="Das Verzeichnis RSSCACHE muss mit CHMOD 777 eingestellt werden, damit die Feeds gecacht werden können.Das Verzeichnis RSSFEED im Plugins Verzeichnis muss ebenfalls mit Chmod 777 eingestellt werden.";
$this->content->template['message_5047']="RSS-Feed erstellen";
$this->content->template['message_5048']="Erstellen";
$this->content->template['message_5049']="Externe Feeds einbinden";
$this->content->template['plugin']['rssfeed']['error1'] = 'Bitte geben Sie einen Titel für den Feed an.';
$this->content->template['plugin']['rssfeed']['error2'] = 'Bitte geben Sie eine Beschreibung für den Feed an.';
$this->content->template['plugin']['rssfeed']['error3'] = 'Nur Zahlen für Feld Anzahl Artikel erlaubt';
$this->content->template['plugin']['rssfeed']['error4'] = 'Die Anzahl Artikel muss größer sein als 0';
$this->content->template['plugin']['rssfeed']['error5']= 'Feld Anzahl Artikel enthält keine Zahl';
$this->content->template['plugin']['rssfeed']['error6'] = 'Nur Zahlen für Feld Sprache erlaubt';
$this->content->template['plugin']['rssfeed']['error7'] = 'Die Sprache muss größer sein als 0';
$this->content->template['plugin']['rssfeed']['error8'] = 'Feld Sprache enthält keine Zahl';


?>