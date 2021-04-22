<?php
/**

Englische Text-Daten des Plugins "template" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

 */

$this->content->template['message']['plugin']['imgexpired']['kopf'] = '<h1>Images expiry check</h1>'.
    '<p>This plugin parses the generated site for images that are expired and removes them before the output is sent to the user.</p>'.
    '<p>The images that need to be checked must contain a class defined like one of the following:</p>'.
    '<ul><li>class="ablauf-tt-mm-jj"</li><li>class="ablauf-tt-mm-jjjj"</li></ul><p>Examples:</p>'.
    '<ul><li>&lt;img src="../images/image.jpg class="ablauf-18-08-14" height="180" width="320" /&gt;</li>'.
    '<li>&lt;img src="../images/image2.jpg class="ablauf-18-08-2014" height="180" width="320" /&gt;</li></ul>'.
    '<p>There are no settings at this moment.</p>';

?>