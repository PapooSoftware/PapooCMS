<?php 
/*
* Alle messages f�r die Linkliste
*
*/

/**
 * <h2>Das Glossar Plugin</h2>
<p>Hier k�nnen Sie die Einstellungen Ihres Glossars bearbeiten.</p>
<p>SIe k�nnen das Glossar so einstellen, das alle Eintr�ge auf einer Seite erscheinen, oder als Linkliste zu den einzelnen Bedeutungen und Erkl�rungen. Dadurch haben Sie f�r jede Erl�uterung eine eigene Seite.</p>
<p>Wenn ein Eintrag in einem Artikel erscheint, dann wird der automatisch beim anlegen eines neuen Artikel mit dem Wort oder der Floskel verlinkt.</p>
<p>Wenn Sie einen neuen Eintrag erstellt haben, oder einen alten ver�ndert haben, k�nnen Sie die Datenbank durchsuchen lassen und automatisch die neuen Eintr�ge erg�nzen lassen. Das funktioniert nur f�r genau einen Eintrag und nicht mit mehreren gleichzeitig, da die Datenbank Belastung sonst zu hoch w�re und es zu Ausf�llen kommen k�nnte.</p>
<p>F�r einen Men�punkt sollten Sie unter formlink das hier eintragen:</p>
<strong>plugin:glossar/templates/glossarfront.html</strong>
 */
$this->content->template['linkliste_toptext']="<h2>Das Linklisten Plugin</h2>
<strong>Hier k�nnen Sie die Einstellungen Ihrer Linkliste bearbeiten.</strong><br />Sie k�nnen die Linkliste so einstellen, da� alle Eintr�ge mit einer externen xml Datei abgeglichen werden. Dazu tragen Sie die Adresse der xml Datei ein. Wenn Sie eine xml Datei aus allen Links zur Verf�gung stellen wollen, machen Sie einfach das H�hcken im Formular.

<p>F�r einen Men�punkt sollten Sie unter \"Einbindung des Links oder der Datei.\" das hier eintragen:
<strong>plugin:linkliste/templates/linkliste_do_front.html</strong></p>";
//Einstellungen f�r die Linkliste.
$this->content->template['linkliste_einstellungen']="Einstellungen f�r die Linkliste.";

?>