<?php
$this->content->template['plugin_pdf_new_tab_head']='PDFs in neuem Tab';
$this->content->template['plugin_pdf_new_tab_description']='Öffnet alle PDFs in einem neuen Tab.';
$this->content->template['plugin_pdf_new_tab_warning']=sprintf("<h2 style=\"color:red;\">Achtung: Es werden bestimmte Eintr&auml;ge in der Datenbank modifiziert!<br/>Erstellen Sie vorsichtshalber ein Backup folgender Tabellen:</h2><ul><li>%s</li></ul><br/>",
    $this->cms->tbname['papoo_language_article']
    );
$this->content->template['plugin_pdf_new_tab_start']='Start';
$this->content->template['plugin_pdf_new_tab_succeeded_message']='Artikel wurden bearbeitet.';
?>