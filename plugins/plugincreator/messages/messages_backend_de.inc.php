<?php
/**

Deutsche Text-Daten des Plugins "Plugin Creator Plugin" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['message']['plugin']['plugincreator']['infotext'] = 'Das Plugin Creator Plugin kann Plugins erstellen, bearbeiten, neuinstallieren und deinstallieren. Soll es ein Plugin erstellen, erstellt es dazu die Verzeichnis Struktur, erstellt und füllt die XML Datei mit sovielen Daten wie möglich, erstellt die (soweit absehbar) erforderlichen Templates, die SQL Dateien, die css Datei, die php Datei und füllt diese mit der Klasse, und den Messages Dateien. Beim Bearbeiten werden die z.B. Modul Einträge in der XML Datei gelöscht, bei den Datenbank Einträgen die Einträge in den SQL-Installations und -Deinstallations Dateien.';
$this->content->template['message']['plugin']['plugincreator']['create_backend_expl'] = 'Hier können Sie ein Plugin erstellen oder - falls zutreffend - bearbeiten. Klicken Sie auf speichern, wird die Verzeichnisstruktur inklusive Dateien - soweit es Ihre Angaben ermöglichen - erstellt.';
$this->content->template['message']['plugin']['plugincreator']['create_backend_menu_annotation'] = '<p>Anmerkung: Es wird automatisch ein Menüpunkt mit dem Namen ihres Plugins angelegt. Hier können Sie nur Untermenüpunkte hinzufügen oder ändern.</p>';
$this->content->template['message']['plugin']['plugincreator']['loeschenFrage'] = 'Plugin incl. Verzeichnisstruktur wirklich löschen?';
$this->content->template['message']['plugin']['plugincreator']['achtungLoeschen'] = 'ACHTUNG: Dieser Schritt ist nicht rückgangig zu machen!';
$this->content->template['message']['plugin']['plugincreator']['deinstallLink'] = 'Wenn Sie das Plugin stattdessen nur Deinstallieren wollen klicken Sie hier';
$this->content->template['message']['plugin']['plugincreator']['checkboxCheck'] = 'Ja, ich bin mir sicher, dass ich dieses Plugin unwiderruflich löschen möchte.';
$this->content->template['message']['plugin']['plugincreator']['rapid_dev_popup_info'] = 'Hier können Sie sich mittels einfacher Steuerungselemente ein Formular zusammen klicken.
Wählen Sie einen Namen für die Variable und das Label, den Formularelementen-Typ, sowie bei bestimmten Element-Typen die jeweiligen Einträge die das Element haben soll und klicken Sie auf "In die Datenbank eintragen".
Hierdurch eingetragene Elemente benötigen unter Umständen 2 Seiten-Refreshes um sichtbar zu werden.
Daten die in das erstellte Formular eingetragen wurden werden in eine automatisch angelegte Tabelle eingetragen.
Diese Daten werden dann automatisch in einer Tabelle unterhalb des "neue felder erstellen"-Formulars angezeigt.
Sollten Sie die "neue felder erstellen" und "demo data" Elemente nicht angezeigt haben wollen, löschen Sie diese aus dem template oder
deinstallieren Sie das "plugin creator"-Plugin.';
?>
