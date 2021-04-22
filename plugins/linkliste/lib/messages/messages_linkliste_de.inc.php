<?php 
/*
* Alle messages für die Linkliste
*
*/

/**
 * <h2>Das Glossar Plugin</h2>
<p>Hier können Sie die Einstellungen Ihres Glossars bearbeiten.</p>
<p>SIe können das Glossar so einstellen, das alle Einträge auf einer Seite erscheinen, oder als Linkliste zu den einzelnen Bedeutungen und Erklärungen. Dadurch haben Sie für jede Erläuterung eine eigene Seite.</p>
<p>Wenn ein Eintrag in einem Artikel erscheint, dann wird der automatisch beim anlegen eines neuen Artikel mit dem Wort oder der Floskel verlinkt.</p>
<p>Wenn Sie einen neuen Eintrag erstellt haben, oder einen alten verändert haben, können Sie die Datenbank durchsuchen lassen und automatisch die neuen Einträge ergänzen lassen. Das funktioniert nur für genau einen Eintrag und nicht mit mehreren gleichzeitig, da die Datenbank Belastung sonst zu hoch wäre und es zu Ausfällen kommen könnte.</p>
<p>Für einen Menüpunkt sollten Sie unter formlink das hier eintragen:</p>
<strong>plugin:glossar/templates/glossarfront.html</strong>
 */
$this->content->template['linkliste_toptext']="<h2>Das Linklisten Plugin</h2>
<strong>Hier können Sie die Einstellungen Ihrer Linkliste bearbeiten.</strong><br />Sie können die Linkliste so einstellen, daß alle Einträge mit einer externen xml Datei abgeglichen werden. Dazu tragen Sie die Adresse der xml Datei ein. Wenn Sie eine xml Datei aus allen Links zur Verfügung stellen wollen, machen Sie einfach das Hähcken im Formular.

<p>Für einen Menüpunkt sollten Sie unter \"Einbindung des Links oder der Datei.\" das hier eintragen:
<strong>plugin:linkliste/templates/linkliste_do_front.html</strong></p>";
//Einstellungen für die Linkliste.
$this->content->template['linkliste_einstellungen']="Einstellungen für die Linkliste.";

?>