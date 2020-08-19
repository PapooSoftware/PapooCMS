<?php
$this->content->template['plugin_contao_import_head']='Beitr&auml;ge aus Contao (TYPOlight) importieren';
$this->content->template['plugin_contao_import_description']='Hier k&ouml;nnen Sie Men&uuml;punkte und Artikel aus einer bestehenden Contao/TYPOlight-Installation importieren.';
$this->content->template['plugin_contao_import_warning']=sprintf("<h2 style=\"color:red;\">Achtung: Es werden alle Menüpunkte, Artikel, Bilder und Downloads aus der Datenbank entfernt!<br/>" .
    "Erstellen Sie vorsichtshalber ein Backup folgender Tabellen:</h2><ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li>" .
    "<li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul><br/>",
    $this->cms->tbname['papoo_me_nu'],
    $this->cms->tbname['papoo_menu_language'],
    $this->cms->tbname['papoo_repore'],
    $this->cms->tbname['papoo_language_article'],
    $this->cms->tbname['papoo_lookup_art_cat'],
    $this->cms->tbname['papoo_lookup_article'],
    $this->cms->tbname['papoo_lookup_write_article'],
    $this->cms->tbname['papoo_lookup_men_ext'],
    $this->cms->tbname['papoo_lookup_me_all_ext'],
    $this->cms->tbname['papoo_images'],
    $this->cms->tbname['papoo_language_image'],
    $this->cms->tbname['papoo_download'],
    $this->cms->tbname['papoo_language_download'],
    $this->cms->tbname['papoo_lookup_download']
    );
$this->content->template['plugin_contao_import_fieldset_db_head']='Tragen Sie hier die DB Verbindungsdaten zu dem Contao-Blog ein.';
$this->content->template['plugin_contao_import_db_verbindungsdaten']='DB Verbindungsdaten';
$this->content->template['plugin_contao_import_db_server']='DB Server';
$this->content->template['plugin_contao_import_db_name']='DB Name';
$this->content->template['plugin_contao_import_db_user']='DB User';
$this->content->template['plugin_contao_import_db_passwort']='DB Passwort';
$this->content->template['plugin_contao_import_prfix']='Pr&auml;fix';
$this->content->template['plugin_contao_import_import_starten']='Import starten';
$this->content->template['plugin_contao_import_bitte_alle_felder_ausfllen']='Bitte alle Felder ausf&uuml;llen';
$this->content->template['plugin_contao_import_datenbank_existiert_nicht']='Die angegebene Verbindungsdaten sind nicht Korrekt!';
$this->content->template['plugin_contao_import_url_der_installation2']='Url der Installation';
$this->content->template['plugin_contao_import_url_der_installation3']='Url der Installation';
$this->content->template['plugin_contao_import_daten_import_erfolgt']='Daten Import erfolgt';
$this->content->template['plugin_contao_import_bitte_berprfen_sie_die']='Bitte &uuml;berpr&uuml;fen Sie die Artikel und f&uuml;rhren evtl. eine manuelle Nachbearbeitung durch.';

$this->content->template['plugin_contao_import_version_head']='Contao / TYPOlight Version';
$this->content->template['plugin_contao_import_version_whle_lbl']='W&auml;hle Sie die installierte Version';
$this->content->template['plugin_contao_import_version_whle_name']='CMS Version';
?>