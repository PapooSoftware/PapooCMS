<?php
/**

Deutsche Text-Daten des Plugins "template" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

 */

$this->content->template['message']['plugin']['imgexpired']['kopf'] = '<h1>Bilder-Ablaufdatums-Check</h1>'.
    '<p>Dieses Plugin durchsucht den Seiteninhalt, noch bevor er zum Client gesendet wird, nach Bildern, die abgelaufen sind und entfernt diese.</p>'.
    '<p>Die zu überprüfenden Bilder müssen eine Klasse in einem der folgenden Formate definiert haben:</p>'.
    '<ul><li>class="ablauf-tt-mm-jj"</li><li>class="ablauf-tt-mm-jjjj"</li></ul><p>Beispiele:</p>'.
    '<ul><li>&lt;img src="../images/Beispiel.jpg class="ablauf-18-08-14" height="180" width="320" /&gt;</li>'.
    '<li>&lt;img src="../images/Beispiel2.jpg class="ablauf-18-08-2014" height="180" width="320" /&gt;</li></ul>'.
    '<p>Derzeit sind keine Einstellmöglichkeiten vorhanden.</p>';

?>