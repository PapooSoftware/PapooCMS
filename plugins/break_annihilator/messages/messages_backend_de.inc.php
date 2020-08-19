<?php
$this->content->template['plugin_break_annihilator_head']='Löscht alle &lt;br&gt;-Tags';
$this->content->template['plugin_break_annihilator_description']='Hier k&ouml;nnen Sie alle &lt;br&gt;-Tags aus allen Artikeln l&ouml;schen.';
$this->content->template['plugin_break_annihilator_warning']=sprintf("<h2 style=\"color:red;\">Achtung: Es werden alle &lt;br&gt;-Tags aus s&auml;mtlichen Artikel gel&ouml;scht!<br/>Erstellen Sie vorsichtshalber ein Backup folgender Tabelle:</h2><ul><li>%s</li></ul><br/>",
    $this->cms->tbname['papoo_language_article']
    );
$this->content->template['plugin_break_annihilator_br_zerstoeren']='Break-Elemente jetzt löschen!';
$this->content->template['plugin_break_annihilator_succeeded_message']='Es wurden alle &lt;br&gt;s vernichtet, alle leeren Tags zerberstet!';
?>