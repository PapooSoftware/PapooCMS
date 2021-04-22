DROP TABLE IF EXISTS `XXX_papoo_module`; ##b_dump##
CREATE TABLE `XXX_papoo_module` (
  `mod_id` bigint(20) NOT NULL auto_increment ,
  `mod_aktiv` int(11) NULL ,
  `mod_bereich_id` int(11) NULL ,
  `mod_order_id` int(11) NULL ,
  `mod_modus` varchar(10) NULL ,
  `mod_datei` varchar(255) NOT NULL ,
  `mod_style_id` varchar(10) NULL ,
  `mod_echt_id` varchar(20) NULL ,
  PRIMARY KEY (`mod_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_module` SET mod_id='1', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='2', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_suchbox.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='3', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_breadcrump.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='4', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_dritte_spalte.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='5', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_efa_fontsize.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='6', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_login.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='7', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_news.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='8', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_sprachwahl.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='9', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_styleswitcher.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='10', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_kopftext.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='11', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_top.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='12', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='[template]', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='13', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_sub.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='14', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_ebene0.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='15', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_ebene1.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='16', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_ebene2.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='17', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_ebene3.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='18', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_menue_sub.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
INSERT INTO `XXX_papoo_module` SET mod_id='19', mod_aktiv='', mod_bereich_id='', mod_order_id='', mod_modus='', mod_datei='_module/mod_accesskeypad.html', mod_style_id='', mod_echt_id=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_module_language`; ##b_dump##
CREATE TABLE `XXX_papoo_module_language` (
  `modlang_mod_id` bigint(20) NOT NULL ,
  `modlang_lang_id` int(11) NOT NULL ,
  `modlang_name` varchar(255) NOT NULL ,
  `modlang_beschreibung` text NOT NULL ,
  KEY `modlang_mod_id` (`modlang_mod_id`),
  KEY `modlang_lang_id` (`modlang_lang_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='1', modlang_name='Navigations-Menü Komplett', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='2', modlang_name='navigation-menue complete', modlang_beschreibung='list of Navigation-Menuepoints - complete'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='3', modlang_name='Navigations-Menü  Komplett', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='4', modlang_name='Navigations-Menü', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='5', modlang_name='Navigations-Menü', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='6', modlang_name='Navigations-Menü', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='1', modlang_lang_id='7', modlang_name='Navigations-Menü', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='1', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='2', modlang_name='searchbox', modlang_beschreibung='formular to search in articles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='3', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='4', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='5', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='6', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='2', modlang_lang_id='7', modlang_name='Such-Box', modlang_beschreibung='Formular zur Suche innerhalb der Artikel.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='1', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='2', modlang_name='breadcrump-menu', modlang_beschreibung='list of breadcrump-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='3', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='4', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='5', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='6', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='3', modlang_lang_id='7', modlang_name='Brotkrumen-Menü', modlang_beschreibung='Liste der \"Brotkrumen-Menüpunkte\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='1', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='2', modlang_name='third collumn', modlang_beschreibung='entries of the 3. collumn'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='3', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='4', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='5', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='6', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='4', modlang_lang_id='7', modlang_name='Dritte Spalte', modlang_beschreibung='Die Einträge der dritten Spalte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='1', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='2', modlang_name='efa-fontsizer', modlang_beschreibung='Script to change fontsize'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='3', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='4', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='5', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='6', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='5', modlang_lang_id='7', modlang_name='EFA-Schriftgröße', modlang_beschreibung='Enbindung der Schriftgrößen-Änderung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='1', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='2', modlang_name='loginbox', modlang_beschreibung='formular for user-login'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='3', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='4', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='5', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='6', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='6', modlang_lang_id='7', modlang_name='Anmelde-Box', modlang_beschreibung='Formular zur Benutzer-Anmeldung'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='1', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='2', modlang_name='news', modlang_beschreibung='articles that are marked as \"news\"'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='3', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='4', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='5', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='6', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='7', modlang_lang_id='7', modlang_name='Neuigkeiten', modlang_beschreibung='Artikel die als \"News\" gekennzeichnet sind'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='1', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='2', modlang_name='language-select', modlang_beschreibung='selection for language'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='3', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='4', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='5', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='6', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='8', modlang_lang_id='7', modlang_name='Sprachwahl', modlang_beschreibung='Auswahl der Sprache'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='1', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='2', modlang_name='style-switcher', modlang_beschreibung='formular to select CSS-style'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='3', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='4', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='5', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='6', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='9', modlang_lang_id='7', modlang_name='Style-Auswahl', modlang_beschreibung='Formular zur Auswahl des CSS-Styles'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='1', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='2', modlang_name='headtext', modlang_beschreibung='H1-text of the site'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='3', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='4', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='5', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='6', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='10', modlang_lang_id='7', modlang_name='Kopftext', modlang_beschreibung='H1-Text der Seite'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='1', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='2', modlang_name='head-menue', modlang_beschreibung='list of fix menuepoints like impress, help etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='3', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='4', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='5', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='6', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='11', modlang_lang_id='7', modlang_name='Kopf-Menü', modlang_beschreibung='Liste der fixen Menüpunkte zum Impressum, Hilfe etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='1', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='2', modlang_name='content', modlang_beschreibung='the content of the Site, eg. articles, guestbook etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='3', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='4', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='5', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='6', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='12', modlang_lang_id='7', modlang_name='Inhalt', modlang_beschreibung='Der eigentliche Inhalt der Seite, also z.B. die Artikel, oder das Gästebuch etc.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='1', modlang_name='Unter-Navigation', modlang_beschreibung='Liste der Unter-Navigation, also alle außer der Ebene 0.'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='2', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='3', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='4', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='5', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='6', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='13', modlang_lang_id='7', modlang_name='sub-navigation', modlang_beschreibung='list of sub-navigation-menuepoints (all besides those of level 0)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='1', modlang_name='Navigations-Menü Ebene 0', modlang_beschreibung='Liste der Navigations-Menüpunkte der obersten Ebene (Haupt-Punkte)'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='2', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='3', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='4', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='5', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='6', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='14', modlang_lang_id='7', modlang_name='navigation-menue level 0', modlang_beschreibung='list of navigation-menuepoints of the top-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='1', modlang_name='Navigations-Menü Ebene 1', modlang_beschreibung='Liste der Navigations-Menüpunkte der 1. Untermenü-Ebene'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='2', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='3', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='4', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='5', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='6', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='15', modlang_lang_id='7', modlang_name='navigation-menue level 1', modlang_beschreibung='list of navigation-menuepoints of the first sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='1', modlang_name='Navigations-Menü Ebene 2', modlang_beschreibung='Liste der Navigations-Menüpunkte der 2. Untermenü-Ebene'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='2', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='3', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='4', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='5', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='6', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='16', modlang_lang_id='7', modlang_name='navigation-menue level 2', modlang_beschreibung='list of navigation-menuepoints of the second sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='1', modlang_name='Navigations-Menü Ebene 3', modlang_beschreibung='Liste der Navigations-Menüpunkte der 3. Untermenü-Ebene'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='2', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='3', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='4', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='5', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='6', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='17', modlang_lang_id='7', modlang_name='navigation-menue level 3', modlang_beschreibung='list of navigation-menuepoints of the third sub-level'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='1', modlang_name='Navigations-Menü Sub', modlang_beschreibung='Liste der Sub-Navigations-Menüpunkte'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='2', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='3', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='4', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='5', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='6', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='18', modlang_lang_id='7', modlang_name='navigation-menue sub', modlang_beschreibung='list of sub-navigation-menuepoints'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='1', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='2', modlang_name='Accesskeypad', modlang_beschreibung='list of Navigation-Menuepoints - complete'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='3', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='4', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='5', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='6', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
INSERT INTO `XXX_papoo_module_language` SET modlang_mod_id='19', modlang_lang_id='7', modlang_name='Accesskeypad', modlang_beschreibung='Liste der Navigations-Menüpunkte - Komplett'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_styles`; ##b_dump##
CREATE TABLE `XXX_papoo_styles` (
  `style_id` int(11) NOT NULL auto_increment ,
  `style_name` varchar(255) NOT NULL ,
  `style_pfad` varchar(255) NOT NULL ,
  `style_dir` varchar(255) NULL ,
  `html_datei` varchar(255) NULL ,
  `standard_style` int(11) NOT NULL  DEFAULT '0' ,
  `style_cc` text NULL ,
  KEY `style_id` (`style_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_styles` SET style_id='401', style_name='Mein neuer Style', style_pfad='_new_style', style_dir='', html_datei='', standard_style='1', style_cc=''  ; ##b_dump##
INSERT INTO `XXX_papoo_styles` SET style_id='404', style_name='Neu 2', style_pfad='_new_style_2', style_dir='', html_datei='', standard_style='0', style_cc=''  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_styles_module`; ##b_dump##
CREATE TABLE `XXX_papoo_styles_module` (
  `stylemod_style_id` bigint(20) NOT NULL ,
  `stylemod_mod_id` bigint(20) NULL ,
  `stylemod_bereich_id` int(11) NULL ,
  `stylemod_order_id` int(11) NULL ,
  KEY `stylemod_style_id` (`stylemod_style_id`),
  KEY `stylemod_mod_id` (`stylemod_mod_id`),
  KEY `stylemod_bereich_id` (`stylemod_bereich_id`),
  KEY `stylemod_order_id` (`stylemod_order_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='6', stylemod_bereich_id='4', stylemod_order_id='30'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='9', stylemod_bereich_id='4', stylemod_order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='1', stylemod_bereich_id='4', stylemod_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='12', stylemod_bereich_id='3', stylemod_order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='3', stylemod_bereich_id='3', stylemod_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='404', stylemod_mod_id='9', stylemod_bereich_id='4', stylemod_order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='404', stylemod_mod_id='1', stylemod_bereich_id='4', stylemod_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='401', stylemod_mod_id='10', stylemod_bereich_id='1', stylemod_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='404', stylemod_mod_id='12', stylemod_bereich_id='3', stylemod_order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='404', stylemod_mod_id='3', stylemod_bereich_id='3', stylemod_order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_styles_module` SET stylemod_style_id='404', stylemod_mod_id='10', stylemod_bereich_id='1', stylemod_order_id='10'  ; ##b_dump##