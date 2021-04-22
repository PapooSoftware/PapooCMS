<?php
/**

Deutsche Text-Daten des Plugins "lang_export" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!! cgfh7fgjh

*/

$this->content->template['message']['plugin']['lang_export']['head'] =
'Plugin "Sprach-Export"';


$this->content->template['message']['plugin']['lang_export']['start_text'] =
'Dieses Plugin exportiert die Sprach-Daten einer bestimmten Sprache.
Es wird eine SQL-Datei erzeugt, welche anschließend in eine andere Papoo-Installation importiert werden kann.<br />
Außerdem werden 2 CSV Dateien erzeugt mit einmal Menüpunkte und einmal Artikelinhalte für Übersetzungen.';

$this->content->template['message']['plugin']['lang_export']['start_text2'] =
'<h2>Sprachen umkopieren</h2><p>Sie können hier die Sprachdaten einer Sprache in eine andere Sprache umkopieren als Vorlage für die Übersetzung.<br /><strong>Es erfolgt keine Übersetzung</strong><br />
<div style="border:1px solid red; padding:5px; background:#ddd;">Machen Sie vorher unbedingt eine Sicherung Ihrer Daten!!!</div></p>';

$this->content->template['message']['plugin']['lang_export']['form1_legend'] =
'Sprachwahl';
$this->content->template['message']['plugin']['lang_export']['form1_label'] =
'Wählen Sie hier die Sprache aus, welche exportiert werden soll';
$this->content->template['message']['plugin']['lang_export']['formi2_label'] =
'Wählen Sie hier die Sprache aus, welche als Referenz dienen soll';
$this->content->template['message']['plugin']['lang_export']['formi3_label'] =
'Wählen Sie hier die Sprache aus, welche als Vorlage gefüllt werden soll';
$this->content->template['message']['plugin']['lang_export']['form1_submit'] =
'Sprache auswählen';
$this->content->template['message']['plugin']['lang_export']['form1_submit2'] =
'Sprachinhalte umkopieren';


$this->content->template['message']['plugin']['lang_export']['download_text'] =
'Unter dem Folgenden Link kann die erstellte Sicherungs-Datei heruntergeladen werden';
$this->content->template['message']['plugin']['lang_export']['download_link'] =
'Sicherungs-Datei';
$this->content->template['message']['plugin']['lang_export']['download_link_men'] =
'CSV Datei Menüpunkte';
$this->content->template['message']['plugin']['lang_export']['download_link_artikel'] =
'CSV Datei Artikel';

$this->content->template['message']['plugin']['lang_export']['form2_legend'] =
'Sicherung löschen';
$this->content->template['message']['plugin']['lang_export']['form2_label'] =
'Aus Sicherheitsgründen sollten Sie die erstellte SQL-Datei nach dem Herunterladen löschen';
$this->content->template['message']['plugin']['lang_export']['form2_submit'] =
'Sicherung löschen';

$this->content->template['message']['plugin']['lang_export']['kopok_text'] =
'Die Daten wurden umkopiert.';
$this->content->template['message']['plugin']['lang_export']['end_text'] =
'OK und Fertig :-)';

?>