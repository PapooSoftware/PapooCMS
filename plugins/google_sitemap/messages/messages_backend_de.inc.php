<?php
/**

Deutsche Text-Daten des Plugins "google_sitemap" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['plugin']['google_sitemap']['text'] = '<h2>Das Google Sitemap Plugin</h2><p>Mit diesem Plugin kann man die Google Sitemap erstellen.</p>';
$this->content->template['plugin']['google_sitemap']['change'] = 'Ändern der Daten für die google sitemap';
$this->content->template['plugin']['google_sitemap']['text2'] = 'Geben Sie an wie oft Ihre Seite aktuallisiert wird und welche Priorität die Einträge haben sollen:';
$this->content->template['plugin']['google_sitemap']['changefreq'] = 'Wählen Sie die changefreq aus:';
$this->content->template['plugin']['google_sitemap']['prioritaet'] = 'Wählen Sie die Priorität aus:';
$this->content->template['plugin']['google_sitemap']['eintragen'] = 'Eintragen';
$this->content->template['plugin']['google_sitemap']['erlaeuterung'] = 'Erläuterung:';
$this->content->template['plugin']['google_sitemap']['text3'] = '<p><b>changefreq:</b> <br />Die Häufigkeit, mit der sich die Seite voraussichtlich ändern wird. Dieser Wert gibt Suchmaschinen allgemeine Informationen. Er steht nicht unbedingt mit der Häufigkeit in Zusammenhang, mit der Sie die Seite durchsuchen. Gültige Werte sind:<br />';
$this->content->template['plugin']['google_sitemap']['text4'] = 'Der Wert "always" wird zur Beschreibung von Dokumenten verwendet, die sich bei jedem Zugriff verändern. Der Wert "never" dient zur Beschreibung archivierter URLs. <br /> <br />Der Wert dieses Tags wird als Hinweis aufgefasst, nicht als Befehl. Die Suchmaschinen-Crawler berücksichtigen diese Information zwar bei ihren Entscheidungen. Sie durchsuchen jedoch Seiten, die mit "hourly" gekennzeichnet sind, eventuell seltener als stündlich, oder Seiten, die mit "yearly" gekennzeichnet sind, häufiger als jährlich. Selbst mit "never" gekennzeichnete Seiten werden von den Crawlern wahrscheinlich in gewissen Zeitabständen durchsucht, um unerwartete Änderungen an solchen Seiten zu erkennen. <br /></p><p><b>Priorität:</b> <br />Die Priorität dieser URL gegenüber anderen URLs auf Ihrer Website. Gültige Werte liegen zwischen 0,0 und 1,0. Dieser Wert hat keinen Einfluss auf einen Vergleich Ihrer Seiten mit Seiten auf anderen Websites, er informiert die Suchmaschinen lediglich darüber, welche Seiten für Sie die höchste Priorität haben. Auf dieser Grundlage werden die Seiten dann durchsucht. <br /> <br />Die Standardpriorität einer Seite ist 0.5. <br /> <br />Die Priorität, die Sie einer Seite zuordnen, hat keinen Einfluss auf die Position Ihrer URLs in den Ergebnisseiten einer Suchmaschine. Diese Information wird von den Suchmaschinen lediglich zur Auswahl zwischen URLs derselben Website genutzt. Die Verwendung dieses Tags erhöht somit die Wahrscheinlichkeit, dass Ihre wichtigeren Seiten im Suchindex aufgeführt werden. <br /> <br />Ebenso wenig ist es zielführend, sämtlichen URLs Ihrer Website hohe Priorität zuzuordnen. Da die Priorität relativ ist, wird sie nur zur Auswahl zwischen URLs innerhalb Ihrer eigenen Website verwendet. Die Priorität Ihrer Seiten wird nicht mit der Priorität von Seiten auf anderen Websites verglichen. <br /></p>';
$this->content->template['plugin']['google_sitemap']['ready'] = 'Die Google Sitemap wurde erstellt.';
$this->content->template['plugin']['google_sitemap']['link'] = 'Link für Ihren Google Account:';
$this->content->template['plugin']['google_sitemap']['error'] = 'Es konnte keine Google Sitemap erstellt werden.';
$this->content->template['plugin']['google_sitemap']['datei'] = 'Die Datei ';
$this->content->template['plugin']['google_sitemap']['datei2'] = ' existiert, konnte aber nicht überschrieben werden. Bitte die Zugriffsrechte (öffentliche Berechtigung zum Schreiben) der Datei ändern.';
$this->content->template['plugin']['google_sitemap']['gespeichert'] = 'Die sitemap wurde gespeichert.';
$this->content->template['plugin']['google_sitemap']['ordner'] = 'Der Ordner ';
$this->content->template['plugin']['google_sitemap']['ordner2'] = ' konnte nicht beschrieben werden. Bitte per ftp die Zugriffsrechte ändern. Bzw. eine leere Datei " . $dateiname . " im htdocs Verzeichnis speichern und die Zugriffsrechte (öffentliche Berechtigung zum Schreiben) der Datei ändern.';
$this->content->template['plugin']['google_sitemap']['geaendert'] = 'Die Daten wurden geändert';


?>