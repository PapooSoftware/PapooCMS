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
$this->content->template['messe_messeblock1']="";
//Einstellungen für die Linkliste.
$this->content->template['messe_messeblock2']="";
$this->content->template['messe_diemessen']="Die Messen";
//zeitlich sortiert
$this->content->template['messe_zeit']="zeitlich sortiert";
//alphabetisch sortiert
$this->content->template['messe_alpha']="alphabetisch sortiert";
//zurück
$this->content->template['messe_zuruck']="zurück";
//messe_reservieren
$this->content->template['messe_reservieren']="Zimmer Reservieren";
//zimmeranfrage
$this->content->template['zimmeranfrage']="Zimmer Anfrage";
?>