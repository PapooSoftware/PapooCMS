<?php
$this->content->template['plugin_point_whitespace_head']='Whitespacing-Korrektur';
$this->content->template['plugin_point_whitespace_description']='F&uuml;gt Leerzeichen dort ein, wo sie hingeh&ören und entfernt sie da, wo keine stehen sollten.';
$this->content->template['plugin_point_whitespace_warning']=sprintf("<h2 style=\"color:red;\">Achtung: Es werden bestimmte Eintr&auml;ge in der Datenbank modifiziert!<br/>Erstellen Sie vorsichtshalber ein Backup folgender Tabellen:</h2><ul><li>%s</li></ul><br/>",
    $this->cms->tbname['papoo_language_article']
    );
$this->content->template['plugin_point_whitespace_start']='Whitespacing korrigieren';
$this->content->template['plugin_point_whitespace_succeeded_message']='Artikel wurden bearbeitet.';
?>
