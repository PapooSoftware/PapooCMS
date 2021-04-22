<?php
// Fehlermeldungen
$this->content->template['plugin']['faq_back']['errmsg']['adminmail_fehlt'] = 'Die E-Mail-Adresse des Admins fehlt';
$this->content->template['plugin']['faq_back']['errmsg']['antwort_fehlt'] = 'Keine Antwort eingegeben';
$this->content->template['plugin']['faq_back']['errmsg']['attachment_already_exist'] = 'Das Attachment wurde für diese FAQ bereits hochgeladen.';
$this->content->template['plugin']['faq_back']['errmsg']['attachment_too_large'] = 'Das Attachment ist zu gross. Max. Bytes:';
$this->content->template['plugin']['faq_back']['errmsg']['attsize_notnumeric'] = 'Die Angabe zur Grösse des Attachments ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['cat_fehlt'] = 'Keine Kategorie ausgewählt';
$this->content->template['plugin']['faq_back']['errmsg']['catname_fehlt'] = 'Bitte den Namen der Kategorie eingeben.';
$this->content->template['plugin']['faq_back']['errmsg']['catspp_notnumeric'] = 'Die Anzahl der Kategorien je Seite ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['copy_from_fehlt'] = 'Welche Kategorie soll kopiert werden?';
$this->content->template['plugin']['faq_back']['errmsg']['copy_to_fehlt'] = 'Wohin soll die Kategorie kopiert werden?';
$this->content->template['plugin']['faq_back']['errmsg']['db_error'] = 'DB-Fehler. Die Konfigurations-Tabelle faq_config ist zerstört.';
$this->content->template['plugin']['faq_back']['errmsg']['faqid_fehlt'] = 'Parameter-Fehler: faq_id nicht vorgegeben';
$this->content->template['plugin']['faq_back']['errmsg']['faqspp_notnumeric'] = 'Die Anzahl der FAqs je Seite ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['file_del_error'] = 'Das Attachment wurde nicht gelöscht.<br />Bitte speichern Sie noch Ihre sonstigen Änderungen.';
$this->content->template['plugin']['faq_back']['errmsg']['frage_fehlt'] = 'Keine Frage eingegeben';
$this->content->template['plugin']['faq_back']['errmsg']['file_fehlt'] = 'Datei nicht gefunden!';
$this->content->template['plugin']['faq_back']['errmsg']['file_vorhanden'] = 'Die Datei ist bereits vorhanden.';
$this->content->template['plugin']['faq_back']['errmsg']['incorrect_att_id'] = 'Inkorrekte Attachment ID.';
$this->content->template['plugin']['faq_back']['errmsg']['incorrect_cat_id'] = 'Inkorrekte Kategorie ID.';
$this->content->template['plugin']['faq_back']['errmsg']['incorrect_faq_id'] = 'Inkorrekte FAQ ID.';
$this->content->template['plugin']['faq_back']['errmsg']['incorrect_version_id'] = 'Inkorrekte Versions ID.';
$this->content->template['plugin']['faq_back']['errmsg']['kein_filename'] = 'Dateiname des Attachment fehlt.';
$this->content->template['plugin']['faq_back']['errmsg']['move_from_fehlt'] = 'Welche Kategorie soll verschoben werden?';
$this->content->template['plugin']['faq_back']['errmsg']['move_gleich'] = 'Die Kategorien (von/nach) sind identisch.';
$this->content->template['plugin']['faq_back']['errmsg']['move_gleich2'] = 'Diese Kategorie (von) befindet sich schon in der Kategorie (nach).';
$this->content->template['plugin']['faq_back']['errmsg']['move_to_fehlt'] = 'Wohin soll die Kategorie verschoben werden?';
$this->content->template['plugin']['faq_back']['errmsg']['order_notnumeric'] = 'Ein Wert der Angaben unter der Reihenfolge ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['order_notnumeric2'] = 'Dieser Wert ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['stepsize_notnumeric'] = 'Die Angabe zur Grösse der Schrittweite ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['src_missing'] = 'Quellenangabe (FE / BE) fehlt.';
$this->content->template['plugin']['faq_back']['errmsg']['uploads_notnumeric'] = 'Die Anzahl der Attachment-Uploads ist nicht numerisch';
$this->content->template['plugin']['faq_back']['errmsg']['wrong_release'] = "Freigabeparameter: Nur 'j' oder 'n' erlaubt";
$this->content->template['plugin']['faq_back']['errmsg']['wrong_release2'] = "Freigabeparameter: Nur 'j' erlaubt";
$this->content->template['plugin']['faq_back']['errmsg']['wrong_src'] = "src-Parameter: Nur 'FE' oder 'BE' erlaubt";

// Formular-Texte
$this->content->template['plugin']['faq_back']['formtext']['faq_order1'] = 'Wählen Sie die Kategorie für die Sie die Reihenfolge der darin enthaltenen FAQs festlegen möchten.';
$this->content->template['plugin']['faq_back']['formtext']['faq_new_release'] = 'FAQ-Anzeige im Frontend sperren/freigeben.';
$this->content->template['plugin']['faq_back']['formtext']['faq_release'] = 'FAQ-Anzeige im Frontend sperren/freigeben.';
$this->content->template['plugin']['faq_back']['formtext']['label_answer'] = 'Inhalt als HTML';
$this->content->template['plugin']['faq_back']['formtext']['label_cat'] = 'Mehrfachauswahl ist möglich, um diese FAQ mehreren Kategorien zuzuordnen.<br />Halten Sie hierzu am PC die Strg- oder Ctrl-Taste (beim Mac die Befehls-/Apfeltaste) während der Auswahl gedrückt.';
$this->content->template['plugin']['faq_back']['formtext']['label_cat_descript'] = 'Beschreibung:';
$this->content->template['plugin']['faq_back']['formtext']['label_cat_edit'] = '<br /><br />Durch Reset wird der aktuelle Zustand der Kategorien-Zuordnung erneut angezeigt. Eingegebene Daten bleiben erhalten, sind aber noch nicht gespeichert worden.';
$this->content->template['plugin']['faq_back']['formtext']['label_cat_edit_sel'] = 'Kategorie (bzw. Unterkategorie zu - wenn Sie die Zuordnung hier ändern)';
$this->content->template['plugin']['faq_back']['formtext']['label_cat_name'] = 'Name:';
$this->content->template['plugin']['faq_back']['formtext']['label_cat_new_sel'] = 'Unterkategorie zu: (nichts auswählen, wenn diese Kategorie eine Hauptkategorie werden soll)';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_adminmail'] = 'E-Mail-Adresse des Admins';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_attachshow'] = 'Attachments anzeigen?';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_attachsize'] = 'max. Grösse eines Attachments in Bytes (z. B. 102400 = 100kb)';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_autolang'] = 'automatische Spracherkennung';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_cats_per_page'] = 'Anzahl der Kategorien (und Fragen bei der Linkliste 4) je Seite';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_faq_order'] = 'Sortierung der FAQs im Frontend';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_faqs_per_page'] = 'Anzahl der FAQs je Seite im Backend';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_footer'] = 'Fusstext';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_header'] = 'Überschrift';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_head_text'] = 'Kopftext';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_meta_descript'] = 'Seitenbeschreibung';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_meta_keys'] = 'Keywords';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_meta_title'] = 'Seitentitel der FAQ';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_orderid_stepsize'] = 'Schrittweite für die benutzerdefinierte FAQ-Sortierung';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_sendmail'] = 'Admin-Benachrichtigung bei neuer FAQ/Frage (im Frontend)?';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_shownewfaq'] = 'Neue FAQ im Frontend ohne Adminfreigabe anzeigen?';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_shownewf'] = 'Neue Frage im Frontend ohne Adminfreigabe anzeigen?';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_config_uploads_per_faq'] = 'max. Attachment-Upload-Anzahl für eine Änderung (0 = unbegrenzt)';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_edit_attachment'] = 'Attachment hochladen (Pfad:  plugins/faq/attachment)';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_edit_release'] = 'FAQ im Frontend anzeigen';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_layout'] = 'Layout';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_new_attachment'] = 'Attachments können nur beim Bearbeiten hochgeladen werden.';
$this->content->template['plugin']['faq_back']['formtext']['label_faq_new_release'] = 'FAQ im Frontend anzeigen';
$this->content->template['plugin']['faq_back']['formtext']['label_question_delete'] = 'Soll wirklich gelöscht werden?';
$this->content->template['plugin']['faq_back']['formtext']['label_version_select'] = 'Ausgewählte Version anzeigen';
$this->content->template['plugin']['faq_back']['formtext']['label_versions'] = 'Wählen Sie die Version, die Sie entweder nur ansehen oder wiederherstellen und eventuell noch zusätzlich bearbeiten möchten. <strong>Speichern Sie Ihre Änderungen vor dem Wechsel zu einer anderen Version.</strong>';
$this->content->template['plugin']['faq_back']['formtext']['legend_answer'] = 'Antwort';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat'] = 'Kategorie';
$this->content->template['plugin']['faq_back']['formtext']['legend_cats_delete'] = 'Kategorien löschen';
$this->content->template['plugin']['faq_back']['formtext']['legend_cats_edit_select'] = 'Kategorien auswählen';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_copy_from'] = 'Kategorie kopieren von:';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_copy_to'] = 'Kategorie kopieren nach:';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_edit'] = 'Kategoriedaten';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_move_from'] = 'Kategorie verschieben von:';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_move_to'] = 'Kategorie verschieben nach:';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_new'] = 'Kategoriedaten';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_order'] = 'Reihenfolge der Kategorien festlegen';
$this->content->template['plugin']['faq_back']['formtext']['legend_cat_select'] = 'Kategoriedaten';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq'] = 'Kategorie';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_config_layout'] = 'Darstellung im Frontend';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_config_meta'] = 'Suchmaschinen META-Daten';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_config_read_privileges'] = 'Leserechte';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_config_settings'] = 'Plugin Einstellungen';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_config_write_privileges'] = 'Schreibrechte';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_delete'] = 'FAQ löschen';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_edit_attachment'] = 'Attachment zur FAQ hinzufügen';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_edit_release'] = 'Freigabe';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_new_release'] = 'Freigabe';
$this->content->template['plugin']['faq_back']['formtext']['legend_faq_order'] = 'Reihenfolge der FAQs festlegen für Kategorie';
$this->content->template['plugin']['faq_back']['formtext']['legend_question'] = 'Frage';
$this->content->template['plugin']['faq_back']['formtext']['legend_question_delete'] = 'Offene Frage löschen';
$this->content->template['plugin']['faq_back']['formtext']['legend_versions'] = 'Versionen';
$this->content->template['plugin']['faq_back']['formtext']['option_cat_edit_maincat'] = 'Hauptkategorie';
$this->content->template['plugin']['faq_back']['formtext']['option_cat_copy'] = 'Hauptkategorie';
$this->content->template['plugin']['faq_back']['formtext']['option_cat_move'] = 'Hauptkategorie';
$this->content->template['plugin']['faq_back']['formtext']['option_cat_new_maincat'] = 'Auswahl';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_compact'] = 'Kompakt';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_extrapage'] = 'Extra Seite';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist'] = 'Linkliste';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist2'] = 'Linkliste 2';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist3'] = 'Linkliste 3';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist4'] = 'Linkliste 4';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist5'] = 'Linkliste 5';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_order_autor'] = 'Autor';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_order_date'] = 'Datum';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_order_frage'] = 'Frage';
$this->content->template['plugin']['faq_back']['formtext']['option_faq_order_id'] = 'benutzerdefiniert';
$this->content->template['plugin']['faq_back']['formtext']['option_version_select'] = 'Version: ';
$this->content->template['plugin']['faq_back']['formtext']['version_select_inactive'] = 'Die Versionsauswahl ist deaktiviert bis zum erneuten Aufruf der FAQ.';

// IMG-alt/title-Texte
$this->content->template['plugin']['faq_back']['imgtext']['cat'] = 'Kategorie';
$this->content->template['plugin']['faq_back']['imgtext']['cat_select'] = 'markierte:';
$this->content->template['plugin']['faq_back']['imgtext']['faq'] = 'FAQ';
$this->content->template['plugin']['faq_back']['imgtext']['faq_edit_attachment'] = 'Löschen Attachment:';

// Linktexte
$this->content->template['plugin']['faq_back']['linktext']['cat_collapse'] = 'Erweitern/Reduzieren';
$this->content->template['plugin']['faq_back']['linktext']['cat_create'] = 'Neue Kategorie erstellen';
$this->content->template['plugin']['faq_back']['linktext']['cat_edit'] = 'Diese Kategorie bearbeiten';
$this->content->template['plugin']['faq_back']['linktext']['cat_delete'] = 'Diese Kategorie löschen';
$this->content->template['plugin']['faq_back']['linktext']['cat_deselect_all'] = 'Alle entfernen';
$this->content->template['plugin']['faq_back']['linktext']['cat_move'] = 'Struktur';
$this->content->template['plugin']['faq_back']['linktext']['cat_sel'] = 'Kategorie für die Bearbeitung der FAQs auswählen';
$this->content->template['plugin']['faq_back']['linktext']['cat_select_all'] = 'Alle auswählen';
$this->content->template['plugin']['faq_back']['linktext']['cats_edit'] = 'Kategorien bearbeiten';
$this->content->template['plugin']['faq_back']['linktext']['cats_delete'] = 'Kategorien löschen';
$this->content->template['plugin']['faq_back']['linktext']['cats_move'] = 'Kategorien-Struktur festlegen';
$this->content->template['plugin']['faq_back']['linktext']['faq_accept'] = 'Frage beantworten und übernehmen';
$this->content->template['plugin']['faq_back']['linktext']['faq_create'] = 'Neue FAQ erstellen';
$this->content->template['plugin']['faq_back']['linktext']['faq_delete'] = 'FAQs löschen';
$this->content->template['plugin']['faq_back']['linktext']['faq_edit'] = 'FAQ bearbeiten/löschen';
$this->content->template['plugin']['faq_back']['linktext']['faq_edit_attachment'] = 'Attachment in neuem Fenster anzeigen.';
$this->content->template['plugin']['faq_back']['linktext']['faq_new_frontend'] = 'FAQ Vorschläge aus dem Frontend';
$this->content->template['plugin']['faq_back']['linktext']['faq_new_frontend2'] = 'FAQ Vorschlag aus dem Frontend übernehmen/bearbeiten/löschen';
$this->content->template['plugin']['faq_back']['linktext']['faq_offene'] = 'Offene Fragen';
$this->content->template['plugin']['faq_back']['linktext']['faq_offene_delete'] = 'Frage unwiderruflich löschen';
$this->content->template['plugin']['faq_back']['linktext']['faq_offene_lock'] = 'Frage im Frontend nicht anzeigen (sperren)';
$this->content->template['plugin']['faq_back']['linktext']['faq_offene_release'] = 'Frage im Frontend anzeigen (freigeben)';
$this->content->template['plugin']['faq_back']['linktext']['faq_release'] = 'Gesperrte FAQs';
$this->content->template['plugin']['faq_back']['linktext']['faq_renum'] = 'FAQs ordnen';

// Meldungen
$this->content->template['plugin']['faq_back']['message']['attachment_loaded'] = 'Die Datei wurde als Attachment hochgeladen.<br />Bitte speichern Sie noch Ihre sonstigen Änderungen.';
$this->content->template['plugin']['faq_back']['message']['attachment_deleted'] = 'Das Attachment wurde gelöscht.<br />Bitte speichern Sie noch Ihre sonstigen Änderungen.';
$this->content->template['plugin']['faq_back']['message']['cat_is_copied'] = 'Die Kategorie(n) wurde(n) kopiert.';
$this->content->template['plugin']['faq_back']['message']['cat_is_del'] = 'Die Kategorie(n) wurde(n) erfolgreich gelöscht!';
$this->content->template['plugin']['faq_back']['message']['cat_is_edit'] = 'Ihre Änderungen wurden erfolgreich gespeichert.';
$this->content->template['plugin']['faq_back']['message']['cat_is_moved'] = 'Die Kategorie(n) wurde(n) verschoben.';
$this->content->template['plugin']['faq_back']['message']['cat_is_new'] = 'Ihre neue Kategorie wurde angelegt.';
$this->content->template['plugin']['faq_back']['message']['cat_is_renumbered'] = 'Die Reihenfolge wurde gespeichert.';
$this->content->template['plugin']['faq_back']['message']['cat_not_edit'] = 'Es sind Fehler aufgetreten. Nicht alle Änderungen wurden gespeichert.';
$this->content->template['plugin']['faq_back']['message']['config_saved'] = 'Die Daten wurden gespeichert.';
$this->content->template['plugin']['faq_back']['message']['confirm_del_1'] = 'Soll die Kategorie ';
$this->content->template['plugin']['faq_back']['message']['confirm_del_2'] = ' wirklich gelöscht werden?';
$this->content->template['plugin']['faq_back']['message']['faq_frontend_deleted'] = 'Die FAQ wurde gelöscht.';
$this->content->template['plugin']['faq_back']['message']['faq_is_accepted'] = 'Die Übernahme der FAQ war erfolgreich.';
$this->content->template['plugin']['faq_back']['message']['faq_is_del'] = 'Die FAQ wurde erfolgreich gelöscht!';
$this->content->template['plugin']['faq_back']['message']['faq_is_edit'] = 'Ihre Änderungen wurden gespeichert.';
$this->content->template['plugin']['faq_back']['message']['faq_is_new'] = 'Ihre neue FAQ wurde angelegt.';
$this->content->template['plugin']['faq_back']['message']['faq_is_renumbered'] = 'Die Reihenfolge wurde gespeichert.';
$this->content->template['plugin']['faq_back']['message']['faq_offene_accepted'] = 'Die FAQ wurde übernommen';
$this->content->template['plugin']['faq_back']['message']['faq_offene_deleted'] = 'Die offene Frage wurde gelöscht.';
$this->content->template['plugin']['faq_back']['message']['faq_offene_locked'] = 'Die offene Frage wurde gesperrt für die Anzeige im Frontend.';
$this->content->template['plugin']['faq_back']['message']['faq_offene_released'] = 'Die offene Frage wurde freigegeben für die Anzeige im Frontend.';
$this->content->template['plugin']['faq_back']['message']['faq_released'] = 'Die FAQ wurde freigegeben für die Anzeige im Frontend.';
$this->content->template['plugin']['faq_back']['message']['no_cats'] = 'Es ist noch keine Kategorie angelegt. Erst dann können FAQs erstellt und bearbeitet werden.';
$this->content->template['plugin']['faq_back']['message']['no_cats2'] = 'Es ist noch keine Kategorie angelegt.';

// Überschriften
$this->content->template['plugin']['faq_back']['pageheader']['faq_accept_faq'] = 'Eine FAQ vom Frontend in die FAQ übernehmen oder löschen';
$this->content->template['plugin']['faq_back']['pageheader']['faq_accept_question'] = 'Offene Frage übernehmen und beantworten';
$this->content->template['plugin']['faq_back']['pageheader']['cat_copy_h2'] = 'Kategorien kopieren';
$this->content->template['plugin']['faq_back']['pageheader']['cat_delete_select'] = 'Kategorien löschen';
$this->content->template['plugin']['faq_back']['pageheader']['cat_edit'] = 'Kategorie-Bearbeitung';
$this->content->template['plugin']['faq_back']['pageheader']['cat_edit_multiple'] = 'Kategorien bearbeiten';
$this->content->template['plugin']['faq_back']['pageheader']['cat_edit_select'] = 'Kategorien für die nachfolgende Bearbeitung auswählen';
$this->content->template['plugin']['faq_back']['pageheader']['cat_main'] = 'Kategorien-Verwaltung';
$this->content->template['plugin']['faq_back']['pageheader']['cat_move'] = 'Kategorien-Struktur festlegen';
$this->content->template['plugin']['faq_back']['pageheader']['cat_move_h2'] = 'Kategorien verschieben';
$this->content->template['plugin']['faq_back']['pageheader']['cat_new'] = 'Kategorie anlegen';
$this->content->template['plugin']['faq_back']['pageheader']['cat_order_h2'] = 'Reihenfolge der Kategorien';
$this->content->template['plugin']['faq_back']['pageheader']['faq_back'] = 'Papoo FAQ Plugin';
$this->content->template['plugin']['faq_back']['pageheader']['faq_config'] = 'Konfigurationseinstellungen des FAQ Plugins';
$this->content->template['plugin']['faq_back']['pageheader']['faq_edit'] = 'FAQ Eintrag bearbeiten';
$this->content->template['plugin']['faq_back']['pageheader']['faq_main'] = 'FAQ-Verwaltung';
$this->content->template['plugin']['faq_back']['pageheader']['faq_new'] = 'FAQ Eintrag erstellen';
$this->content->template['plugin']['faq_back']['pageheader']['faq_new_frontend'] = 'FAQs vom Frontend';
$this->content->template['plugin']['faq_back']['pageheader']['faq_offene'] = 'Offene Fragen';
$this->content->template['plugin']['faq_back']['pageheader']['faq_order'] = 'Reihenfolge der FAQs';
$this->content->template['plugin']['faq_back']['pageheader']['faq_release'] = 'Gesperrte FAQs';
$this->content->template['plugin']['faq_back']['pageheader']['faq_search'] = 'Suchergebnis';

// Button-Texte
$this->content->template['plugin']['faq_back']['submit']['body'] = 'Es wurde eine FAQ erstellt oder geändert. Die FAQ kann nach Prüfung freigeschaltet werden.';
$this->content->template['plugin']['faq_back']['submit']['cat_copy'] = 'Kopieren';
$this->content->template['plugin']['faq_back']['submit']['cat_delete'] = 'Entfernen';
$this->content->template['plugin']['faq_back']['submit']['cat_edit'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['cat_edit_select'] = 'Auswahl';
$this->content->template['plugin']['faq_back']['submit']['cat_copy_title'] = 'Kategorie kopieren';
$this->content->template['plugin']['faq_back']['submit']['cat_move'] = 'Verschieben';
$this->content->template['plugin']['faq_back']['submit']['cat_move_title'] = 'Kategorie verschieben';
$this->content->template['plugin']['faq_back']['submit']['cat_new'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['cat_order'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['cat_order_title'] = 'Neue Reihenfolge speichern';
$this->content->template['plugin']['faq_back']['submit']['cat_select'] = 'Auswahl';
$this->content->template['plugin']['faq_back']['submit']['delete_no'] = 'Nein';
$this->content->template['plugin']['faq_back']['submit']['delete_no2'] = 'Nicht löschen';
$this->content->template['plugin']['faq_back']['submit']['delete_yes'] = 'Ja';
$this->content->template['plugin']['faq_back']['submit']['delete_yes2'] = 'Löschen';
$this->content->template['plugin']['faq_back']['submit']['faq_accept_faq_submit'] = 'Übernehmen / Löschen';
$this->content->template['plugin']['faq_back']['submit']['faq_accept_question'] = 'Freigeben';
$this->content->template['plugin']['faq_back']['submit']['faq_accept_question2'] = 'Übernehmen der Daten in den FAQ-Datenbestand';
$this->content->template['plugin']['faq_back']['submit']['faq_accept_question_submit'] = 'Übernehmen in den Datenbestand';
$this->content->template['plugin']['faq_back']['submit']['faq_config'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['faq_edit'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_attachment'] = 'Attachment hochladen ';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_attachment_upload'] = 'Hochladen';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_del'] = 'Löschen';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_reset'] = 'Reset';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_sel'] = 'Auswahl';
$this->content->template['plugin']['faq_back']['submit']['faq_edit_submit'] = 'Speichern / Löschen';
$this->content->template['plugin']['faq_back']['submit']['faq_new'] = 'Speichern aller Daten';
$this->content->template['plugin']['faq_back']['submit']['faq_new_edit_frontend'] = 'Übernehmen';
$this->content->template['plugin']['faq_back']['submit']['faq_new_edit_frontend2'] = 'Übernehmen der FAQ aus dem Frontend in die aktuelle FAQ';
$this->content->template['plugin']['faq_back']['submit']['faq_order'] = 'Speichern';
$this->content->template['plugin']['faq_back']['submit']['faq_order_title'] = 'Neue Reihenfolge speichern';
$this->content->template['plugin']['faq_back']['submit']['from_text'] = 'FAQ Admin';
$this->content->template['plugin']['faq_back']['submit']['subject'] = 'FAQ Freigabe erforderlich';

// Allgemeine Texte
$this->content->template['plugin']['faq_back']['text']['author'] = 'Verfasser:';
$this->content->template['plugin']['faq_back']['text']['cat_copy'] = 'Beim Kopieren von Kategorien werden die darin enthaltenen FAQs ebenfalls kopiert, wobei hierbei keine Kopie der FAQs erzeugt wird. Es wird lediglich eine Verknüpfung auf ein und dieselben FAQs erzeugt (bitte beim Edit beachten). Liegen Ziel und Quelle innerhalb derselben Kategorie, wird eine Kopie mit dem Namen "Kopie von..." erstellt.<br /><br />Die ausgewählte Kategorie wird einschliesslich ihrer Unterkategorien kopiert und wird zur Unterkategorie der gewählten Ziel-Kategorie.';
$this->content->template['plugin']['faq_back']['text']['cat_count'] = 'Anzahl der vorhandenen Kategorien:';
$this->content->template['plugin']['faq_back']['text']['cat_delete'] = 'Löschen';
$this->content->template['plugin']['faq_back']['text']['cat_delete_top'] = 'Beim Löschen einer Kategorie werden die  Unterkategorien nicht gelöscht, sondern nur die betreffende Kategorie. Die Unterkategorien sind danach "verwaist" und müssen erst wieder einer Kategorie zugeordnet werden. <strong>Verwaiste Einträge erscheinen auch im Frontend!</strong><br /><br />Die zugehörigen FAQs werden nicht gelöscht. Diese können danach einer anderen Kategorie neu zugeordnet werden. Sind die FAQs nach dem Löschen keiner anderen Kategorie zugeordnet, werden sie ebenfalls bis zur Neuzuordnung als "verwaist" gekennzeichnet.';
$this->content->template['plugin']['faq_back']['text']['cat_descript'] = 'Beschreibung';
$this->content->template['plugin']['faq_back']['text']['cat_edit'] = 'Edit';
$this->content->template['plugin']['faq_back']['text']['cat_move'] = 'Beim Verschieben von Kategorien werden die darin enthaltenen FAQs ebenfalls verschoben.<br /><br />Die gewählte Kategorie wird einschliesslich ihrer Unterkategorien verschoben. Die verschobene Kategorie wird dabei zur Unterkategorie der gewählten Ziel-Kategorie.<br /><br />Verschieben in die eigene Unterkategorie:<br />Eine Kategorie kann auch in ihre eigene Unterkategorie verschoben werden. Dabei werden die vorhandenen Unterkategorien nur beim Verschieben in eine höhere Ebene mit verschoben.<br />Beim Verschieben in eine niedrigere Ebene wird nur die Kategorie allein verschoben und ihre Unterkategorien klettern um eine Ebene höher.';
$this->content->template['plugin']['faq_back']['text']['cat_name'] = 'Kategorie<br />&nbsp;&nbsp;&nbsp;&nbsp;|';
$this->content->template['plugin']['faq_back']['text']['cat_name2'] = 'FAQ';
$this->content->template['plugin']['faq_back']['text']['cat_new'] = 'Neu';
$this->content->template['plugin']['faq_back']['text']['cat_new_help'] = 'Neue Kategorien werden immer am Ende eingefügt. Die Reihenfolge können Sie nach dem Speichern ändern.';
$this->content->template['plugin']['faq_back']['text']['cat_orphan'] = 'verwaist';
$this->content->template['plugin']['faq_back']['text']['faq_back'] = '<h2>Links für die Einbindung über das Menü:</h2><p><strong>FAQ:</strong><br />plugin:faq/templates/faq_front.html</p><p><strong>Offene Fragen:</strong><br />plugin:faq/templates/faq_front_list_questions.html</p><p>Mit dem Parameter get_faq_single_cat = 123 können Sie eine individuelle Kategorie in der Linksliste ausgeben lassen,<br /><strong>Beispiel</strong> plugin:faq/templates/faq_front.html&get_faq_single_cat=1</p>';
$this->content->template['plugin']['faq_back']['text']['faq_back2'] = 'Menüpunkt für das FAQ Plugin erstellen';
$this->content->template['plugin']['faq_back']['text']['faq_back_edit_list'] = 'Diese Liste zeigt alle FAQs mit Ausnahme der  offenen Fragen und der FAQs aus dem Frontend.';
$this->content->template['plugin']['faq_back']['text']['faq_back_sort_created'] = 'Listensortierung: Erstellungs-Datum';
$this->content->template['plugin']['faq_back']['text']['faq_back_sort_createdby'] = 'Listensortierung: Autor';
$this->content->template['plugin']['faq_back']['text']['faq_back_sort_question'] = 'Listensortierung: Frage';
$this->content->template['plugin']['faq_back']['text']['faq_back_sort_user'] = 'Listensortierung: benutzerdefiniert (order_id)';
$this->content->template['plugin']['faq_back']['text']['faq_count'] = 'Anzahl vorhandener FAQs';
$this->content->template['plugin']['faq_back']['text']['faq_count_frontend'] = 'Anzahl Frontend FAQs';
$this->content->template['plugin']['faq_back']['text']['faq_count_offene'] = 'Anzahl offener Fragen';
$this->content->template['plugin']['faq_back']['text']['faq_count_release'] = 'Anzahl gesperrter FAQs';
$this->content->template['plugin']['faq_back']['text']['faq_created'] = 'Datum';
$this->content->template['plugin']['faq_back']['text']['faq_createdby'] = 'Autor';
$this->content->template['plugin']['faq_back']['text']['faq_delete'] = 'Löschen';
$this->content->template['plugin']['faq_back']['text']['faq_db_error'] = 'DB ERROR! (Tabelle faq_cat_link und/oder faq_categories ist fehlerhaft)';
$this->content->template['plugin']['faq_back']['text']['faq_edit'] = 'Edit /<br />Löschen';
$this->content->template['plugin']['faq_back']['text']['faq_edit_attachment'] = ' Vorhandene Attachments:';
$this->content->template['plugin']['faq_back']['text']['faq_edit_attachment2'] = '<br />Eine oder mehrere Ihrer Dateien sind nur noch in der DB eingetragen, jedoch nicht mehr im Verzeichnis zu finden. Zur Beseitigung des Fehlers können Sie diese Datei(en) hier oder per FTP neu hochladen oder ggfs. sofort löschen. Beachten Sie, dass die Dateien beim Hochladen denselben Namen und dieselbe Grösse (letzteres nicht via FTP) haben müssen.';
$this->content->template['plugin']['faq_back']['text']['faq_list_attcount'] = '# Att.';
$this->content->template['plugin']['faq_back']['text']['faq_list_catscount'] = '# Kat.';
$this->content->template['plugin']['faq_back']['text']['faq_list_frage'] = 'Frage';
$this->content->template['plugin']['faq_back']['text']['faq_lock'] = 'sperren';
$this->content->template['plugin']['faq_back']['text']['faq_locked'] = 'gesperrt';
$this->content->template['plugin']['faq_back']['text']['faq_main'] = 'Zur Bearbeitung oder zum Löschen einer FAQ wählen Sie bitte zuerst eine Kategorie aus der unten stehenden Liste aus, danach die FAQ. Alternativ können Sie den Edit-Button oben nutzen, um eine komplette Liste aller FAQs zur Bearbeitung oder zum Löschen zu erhalten oder indem Sie die Suche oben nutzen.';
$this->content->template['plugin']['faq_back']['text']['faq_new'] = 'Neu';
$this->content->template['plugin']['faq_back']['text']['faq_new_faq'] = 'Diese Liste zeigt neue FAQs und offene Fragen, die im Frontend erstellt bzw. beantwortet wurden, wobei in dem Fall in der Konfiguration "Neue FAQ im Frontend ohne Adminfreigabe anzeigen" nicht aktiviert wurde.';
$this->content->template['plugin']['faq_back']['text']['faq_new_frontend'] = 'FAQ aus<br />Frontend';
$this->content->template['plugin']['faq_back']['text']['faq_offene'] = 'Offene Fragen';
$this->content->template['plugin']['faq_back']['text']['faq_offene_frage'] = 'Frage';
$this->content->template['plugin']['faq_back']['text']['faq_orphan'] = 'verwaist';
$this->content->template['plugin']['faq_back']['text']['faq_rel'] = 'freigeben';
$this->content->template['plugin']['faq_back']['text']['faq_release'] = 'Freigaben Backend';
$this->content->template['plugin']['faq_back']['text']['faq_release_text'] = 'Diese Liste zeigt FAQs, die manuell beim Editieren im Backend gesperrt wurden. Offene Fragen, die nicht freigegeben sind, werden hier nicht gelistet.';
$this->content->template['plugin']['faq_back']['text']['faq_renum'] = 'Ordnen';
$this->content->template['plugin']['faq_back']['text']['faq_search_count'] = 'Anzahl gefundener FAQs';
$this->content->template['plugin']['faq_back']['text']['faq_source'] = 'von';
$this->content->template['plugin']['faq_back']['text']['faq_unsolved_problem'] = '<p>Diese Liste zeigt unbeantwortete (offene) Fragen. Eine offene Frage wird im Backend durch das Weglassen der Antwort beim Speichern erstellt, im Frontend über einen entsprechenden Link zum Erstellungs-Formular.</p><p>Ist in der Konfiguration "Neue Frage im Frontend ohne Adminfreigabe anzeigen" nicht aktiviert, erscheint die offene Frage in dieser Liste, andernfalls wird sie automatisch in die FAQ übernommen.</p><p>Sobald eine offene Frage beantwortet ist, wird diese zur FAQ und wird gemäss den Konfigurations-Einstellungen für die Freigabe einer FAQ behandelt.</p><p>Offene Fragen können hier gelöscht oder für die Anzeige im Frontend freigegeben oder gesperrt werden. Durch Klick auf die Frage kann diese im nachfolgend gezeigten Bearbeitungs-Formular bearbeitet und in die FAQ übernommen werden. Hierzu muss die Frage beantwortet werden.';
$this->content->template['plugin']['faq_back']['text']['no_match_found'] = 'Ihre Suche ergab keinen Treffer';
$this->content->template['plugin']['faq_back']['text']['search_match'] = 'Resultat';
$this->content->template['plugin']['faq_back']['text']['search_matches'] = 'Resultate';
$this->content->template['plugin']['faq_back']['text']['version'] = 'Eine neue Version wird entweder beim Speichern der Daten  erzeugt oder bereits nach dem ersten Hochladen oder Löschen eines Attachments. Weiteres Hochladen oder Löschen von Attachments erzeugt keine neue (zusätzliche) Version. Beim Speichern der Daten wird nur dann eine neue Version erzeugt, wenn vorher keine Attachments gelöscht oder hochgeladen wurden.<br /><br />Ihre Daten werden nur durch das Speichern oder durch das erste Hochladen/Löschen eines Attachments übernommen.<br /><br />Falls Sie ohne weitere Änderungen an der FAQ nur ein oder mehrere Attachments hochladen/löschen wollen, ist das Speichern nicht erforderlich. ';
$this->content->template['plugin']['faq_back']['text']['version_inwork'] = 'Sie bearbeiten die Version:';
$this->content->template['plugin']['faq_back']['text']['version_selected'] = 'Ausgewählte Version:';

// Backup Messages
$this->content->template['plugin']['faq']['datei_upload'] = 'Datei hochladen:';
$this->content->template['plugin']['faq']['dokument'] = 'Das Dokument:';
$this->content->template['plugin']['faq']['eingabe_datei'] = 'Eingabe der Datei:';
$this->content->template['plugin']['faq']['hinweis'] = 'Um eine Sicherung einzuspielen, wählen Sie bitte die Sicherungsdatei aus:';
$this->content->template['plugin']['faq']['make_dump'] = 'Jetzt eine Sicherung der FAQ-Daten erstellen.';
$this->content->template['plugin']['faq']['sicherung'] = '<h3>Sicherung der FAQ-Daten erstellen</h3><p>Sie können hier eine Sicherung der FAQ-Daten erstellen, die Sie nach einer Neuinstallation oder zu einem beliebig anderen Zeitpunkt wieder einspielen können.</p>';
$this->content->template['plugin']['faq']['sicherung_einspielen'] = 'Eine Sicherung der FAQ-Daten einspielen';
$this->content->template['plugin']['faq']['sicherung_ready'] = 'FAQ-Daten wurden eingespielt.';
$this->content->template['plugin']['faq']['warnung'] = 'ACHTUNG - Wenn Sie eine Sicherung einspielen, werden alle aktuellen Daten unwiderruflich gelöscht. Erstellen Sie daher vorher unbedingt eine Sicherung.';
$this->content->template['plugin']['faq']['upload'] = 'hochladen';

// Suche
$this->content->template['message_2135']='Suche';
$this->content->template['message_2136']='Suchbegriff hier eingeben';
$this->content->template['message_2138']='Finden';
$this->content->template['templ_bis']='bis';
$this->content->template['templ_ergbn']='Ergebnis Ihrer Suche nach ';
$this->content->template['templ_insg']='von insgesamt';
$this->content->template['templ_res']='Resultate';
// Singular
$this->content->template['templ_seite']='Seite';
// Plural
$this->content->template['templ_seiten']='Seiten';
$this->content->template['templ_serg']='Suchergebnis';
$this->content->template['plugin_faq__csv_datei_importierenn']='CSV Datei importieren';
 $this->content->template['plugin_faq__importieren_sie_hier']='Importieren Sie hier eine Liste von FAQ Eintr&auml;gen. Die CSV Datei sollte folgenderma&szlig;en aufgebaut sein:';
 $this->content->template['plugin_faq__kateogiimport']='1., 2. und 3. Spalte k&ouml;nnen Kategorien sein, die 1. muss vergeben sein, 2 und 3 sind optional';
 $this->content->template['plugin_faq__die_4_spalte']='Die 4. Spalte enth&auml;lt die Frage als reinen Text.';
 $this->content->template['plugin_faq__die_5_spalte_']='Die 5. Spalte enth&auml;lt die Antwort als Text oder als HTML.';
 $this->content->template['plugin_faq__anzahl_der_importierten_faq_eintrge']='Import erfolgreich<br />Anzahl der importierten FAQ Eintr&auml;ge:';
 #start#
?>