<?php
/**

Deutsche Text-Daten des Plugins "test" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['message']['twitter']['test']['name'] =
'Plugin "Test"';

$this->content->template['message']['twitter']['test']['kopf'] =
'<h1>Backend des Test-Plugins</h1>'.
'<p>Dieses Template ist zwar nicht barrierefrei, dafür aber sinnfrei und nicht X-HTML-konform. Nichts-desto-trotz sollte es zur Erklärung der Programmierung von Papoo-Plugins dienstreiche Hilfe leisten.</p>'.
'<p>Die verschiedenen Menü-Punkte dieses Plugins sind ebenfalls sinnfrei. Sie verweisen immer auf dasselbe Template "test_back.html". Die Punkte sind nur um zu zeigen, wie in der Plugin-XML-Datei Menü-Punkte angelegt werden können.</p>'.
'<p>Die Einbindung des Frontend-Templates geht wie folgt:
	Erstelle einen neuen Menü-Punkt. Gebt dort unter "Einbindung des Links oder der Datei." (ganz unten) Folgendes ein: <strong>plugin:test/templates/test_front.html</strong>. Damit steht das Template im Frontend zur Verfügung.</p>'.
'<p>Die in diesem Template enthaltenen Module können mit dem Modul-Manager hier in der Administration eingefügt werden. Für alle die das Ding noch nicht entdeckt haben, zu finden ist er unter "System -> Modul-Manager".</p>';

$this->content->template['message']['twitter']['test']['form_kopf'] =
'Und hier ein kleines Formular:';

$this->content->template['message']['twitter']['test']['form_legend'] =
'Testwert';

$this->content->template['message']['twitter']['test']['form_testwert_label'] =
'Einen Testwert per POST übergeben';

?>