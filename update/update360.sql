ALTER TABLE `XXX_papoo_daten` ADD `stamm_limit_zahl` varchar(255) NOT NULL  DEFAULT '1' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `lang_autodetect` varchar(255) NOT NULL  DEFAULT '1' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `lang_dateformat` varchar(255) NOT NULL  DEFAULT '1' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `stamm_cat_startseite_ok` varchar(255) NOT NULL  DEFAULT '1' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `stamm_surls_trenner` varchar(255) NOT NULL  DEFAULT '1' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `stamm_kontakt_spamschutz` int(4)  NULL  DEFAULT '0' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `off_yn` varchar(255)  NULL  DEFAULT '0' ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `wartungstext` text NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_repore` ADD `dokuser_last` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_repore` ADD `dok_teaserfix` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_repore` ADD `dok_show_teaser_link` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_repore` ADD `dok_show_teaser_teaser` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `do_replace` int(11) NOT NULL DEFAULT '1'; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `gaestebuch_msg_frei` int(11) NOT NULL DEFAULT '1'; ##b_dump##
ALTER TABLE `XXX_papoo_daten` ADD `showpapoo` int(11) NOT NULL DEFAULT '1'; ##b_dump## 
ALTER TABLE `XXX_papoo_daten` ADD `show_menu_all` int(11) NOT NULL DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_daten` ADD `stamm_cat_sub_ok` int(11) NOT NULL DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_daten` ADD `stamm_artikel_order` int(11) NOT NULL DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` ADD `banner_lang` varchar(255) NOT NULL DEFAULT 'de'; ##b_dump## 
ALTER TABLE `XXX_papoo_freiemodule_daten` ADD `freiemodule_lang` varchar(255) NOT NULL DEFAULT 'de'; ##b_dump## 
ALTER TABLE `XXX_papoo_me_nu` ADD `menu_image` text ; ##b_dump## 
ALTER TABLE `XXX_papoo_me_nu` ADD `menu_subteaser` int(11) NOT NULL DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_menu_language` ADD `menulinklang` TEXT NOT NULL ; ##b_dump## 
ALTER TABLE `XXX_papoo_category` ADD `cat_startseite_ok` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_category` ADD `cat_startseite_anzahl` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_version_language_article` ADD `url_header` text NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_repore` ADD `cat_category_id` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_version_repore` ADD `cat_category_id` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_version_repore` ADD `dokuser_last` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_version_repore` ADD `dok_teaserfix` int(11) NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_version_repore` ADD `dok_show_teaser_link` int(11) NOT NULL ; ##b_dump##

ALTER TABLE `XXX_papoo_version_repore` ADD `dok_show_teaser_teaser` int(11) NOT NULL ; ##b_dump##

ALTER TABLE `XXX_papoo_language_article` ADD `lan_teaser_img_alt` text NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_language_article` ADD `lan_teaser_img_title` text NOT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_language_article` ADD `lan_teaser_img_fertig` text NOT NULL ; ##b_dump##

INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='81', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='82', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='83', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='84', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='85', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='86', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='87', gruppenid='1'  ; ##b_dump##
DELETE FROM `XXX_papoo_men_uint_language` WHERE menuid_id<'999'; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='3', menuname='Forum verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='4', menuname='User verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='5', menuname='Artikel verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='6', menuname='Neue Gruppe', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='7', menuname='Neuer User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='8', menuname='User bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='9', menuname='Gruppe bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='10', menuname='Neuer Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='11', menuname='Artikel ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='40', menuname='Foren erstellen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='42', menuname='Nachrichten bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='14', menuname='Menü verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='15', menuname='3.Spalte verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='16', menuname='Neuer Eintrag', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='17', menuname='Eintrag bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='18', menuname='Stammdaten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='19', menuname='Bilder verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='20', menuname='Bilder hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='21', menuname='Bilder bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='22', menuname='CSS - Layout', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='1', menuname='Startseite', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='24', menuname='Links eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='25', menuname='Links ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='26', menuname='Autom. Auszeichungen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='27', menuname='Abkürz. eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='28', menuname='Abkürz. bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='29', menuname='Sprachausz. eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='30', menuname='Sprachausz. ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='31', menuname='Dateien verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='32', menuname='Dateien hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='33', menuname='Dateien bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='41', menuname='Foren bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='43', menuname='Menü erstellen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='34', menuname='Mails lesen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='36', menuname='Offene Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='37', menuname='Publizierte Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='38', menuname='Pers. Daten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='35', menuname='Mails schreiben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='44', menuname='Menü bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='3', menuname='Manage Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='4', menuname='Manage User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='5', menuname='Manage Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='6', menuname='New group', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='7', menuname='New User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='8', menuname='Edit User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='9', menuname='Edit group', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='10', menuname='New Article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='11', menuname='Edit Article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='40', menuname='Create Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='42', menuname='Edit Messages', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='14', menuname='Manage Menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='15', menuname='Manage 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='16', menuname='New Entry', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='17', menuname='Edit Entry', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='18', menuname='Master Data', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='19', menuname='Manage Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='20', menuname='Upload Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='21', menuname='Edit Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='22', menuname='CSS - Layout', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='1', menuname='Home', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='24', menuname='Enter Links', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='25', menuname='Edit Links', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='26', menuname='autom. Span', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='27', menuname='Enter Span', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='28', menuname='Edit Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='29', menuname='Language change', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='30', menuname='Edit Language Change', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='31', menuname='Upload Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='32', menuname='Upload the Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='33', menuname='Edit Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='41', menuname='Edit Forums', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='43', menuname='Create menu item', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='34', menuname='Read Mails', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='36', menuname='Open articles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='37', menuname='Published articles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='38', menuname='Pers. Data', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='35', menuname='Write mails', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='44', menuname='Edit Menu items', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='45', menuname='Reihenfolge ', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='45', menuname='Change sequence', back_front='1', lang_title='Change sequence of the articles'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='46', menuname='Reihenfolge ', back_front='1', lang_title='Reihenfolge der Menuepunkte aendern'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='46', menuname='Change sequence', back_front='1', lang_title='Change sequence of menu items'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='47', menuname='Plugin Manager', back_front='1', lang_title='Hier koennen Plugins installiert werden'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='47', menuname='Plugin Manager', back_front='1', lang_title='Administrate Plugins'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='50', menuname='Rechteverwaltung', back_front='1', lang_title='Verwaltung der Zugriffsrechte auf die Menuepunkte'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='50', menuname='Manage permissions', back_front='1', lang_title='Manage Rights'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='49', menuname='Startseite-Inhalt', back_front='1', lang_title='Metadaten und Kopftext'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='49', menuname='Homepage-Start', back_front='1', lang_title='Metadata and Headtext'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='51', menuname='CSS Datei hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='51', menuname='Upload CSS File', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='53', menuname='Sicherungen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='53', menuname='Dumps', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='55', menuname='Inhalte', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='55', menuname='Contents', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='56', menuname='System', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='56', menuname='System', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='57', menuname='Gruppen verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='57', menuname='Manage groups', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='58', menuname='Kopf-Menü verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='58', menuname='Manage menu-top', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='59', menuname='Automatismen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='60', menuname='Modul-Manager', back_front='1', lang_title='Zuweisung der Module zu den Bereichen'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='60', menuname='Modul-manager', back_front='1', lang_title='Assignment of moduls'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='61', menuname='Info', back_front='1', lang_title='Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='61', menuname='Info', back_front='1', lang_title='Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='62', menuname='Kategorien', back_front='1', lang_title='Ordner für Bilder'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='62', menuname='Categories', back_front='1', lang_title='Dir for images'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='63', menuname='Kategorien', back_front='1', lang_title='Ordner für Dateien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='63', menuname='Categories', back_front='1', lang_title='Dir for files'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='64', menuname='System Info', back_front='1', lang_title='System Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='64', menuname='System Info', back_front='1', lang_title='System Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='65', menuname='Papoo Update', back_front='1', lang_title='Papoo Update'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='65', menuname='Papoo Update', back_front='1', lang_title='Papoo Update'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='48', menuname='Kontakt-Formular', back_front='1', lang_title='Text für das Kontakt-Formular'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='48', menuname='Contactform content', back_front='1', lang_title='Text für das Kontakt-Formular'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='66', menuname='Kategorien', back_front='1', lang_title='Kategorien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='67', menuname='Kategorie erstellen', back_front='1', lang_title='Kategorie erstellen'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='68', menuname='Kategorie bearbeiten', back_front='1', lang_title='Kategorie bearbeiten'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='69', menuname='Kategorien sortieren', back_front='1', lang_title='Kategorie sortieren'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='70', menuname='Videos verwalten', back_front='1', lang_title='Videos'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='70', menuname='manage videos', back_front='1', lang_title='Videos'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='71', menuname='Videos einbinden', back_front='1', lang_title='Videos einbinden'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='73', menuname='Kontakt Felder', back_front='1', lang_title='Kontakt Felder bearbeiten und erstellen'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='73', menuname='contact form', back_front='1', lang_title='Manage the contact form'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='71', menuname='embed videos', back_front='1', lang_title='embed Videos'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='72', menuname='Video Kategorien', back_front='1', lang_title='Video Kategorien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='72', menuname='video categories', back_front='1', lang_title='Video categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='66', menuname='Categories', back_front='1', lang_title='Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='67', menuname='Create categories', back_front='1', lang_title='Create Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='68', menuname='Change categories', back_front='1', lang_title='Change Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='69', menuname='Order categories', back_front='1', lang_title='Order Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='5', menuname='Les articles administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='4', menuname='Des utilisateurs administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='3', menuname='Le forum administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='6', menuname='Nouveau groupe', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='7', menuname='Nouvel utilisateur', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='8', menuname='Des utilisateurs travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='9', menuname='Un groupe travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='10', menuname='Le nouvel article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='11', menuname='Les articles modifier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='40', menuname='Des forums fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='42', menuname='Des informations travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='14', menuname='Le menu administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='15', menuname='3.Spalte administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='16', menuname='Nouvelle entrée', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='17', menuname='Une entrée travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='18', menuname='Données de base', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='19', menuname='Des images administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='20', menuname='Images hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='21', menuname='Des images travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='22', menuname='CSS - maquette', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='1', menuname='Page de commencement', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='23', menuname='Moteur de lien', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='24', menuname='Suggérer à gauche', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='25', menuname='Modifier à gauche', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='26', menuname='Auszeichungen automat.', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='27', menuname='Raccourcir. suggérer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='28', menuname='Raccourcir. travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='29', menuname='Sprachausz. suggérer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='30', menuname='Sprachausz. modifier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='31', menuname='Des fichiers administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='32', menuname='Fichiers hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='33', menuname='Des fichiers travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='41', menuname='Des forums travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='43', menuname='Le menu fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='34', menuname='Des Mails lisent', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='36', menuname='Les articles ouverts', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='37', menuname='Les articles publiés', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='38', menuname='Pers. Données', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='35', menuname='Des Mails écrivent', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='44', menuname='Le menu travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='45', menuname='Séquence', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='46', menuname='Séquence', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='47', menuname='Plugin directeurs', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='50', menuname='Administration juridique', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='49', menuname='Contenu de côté de commencement', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='51', menuname='CSS fichier hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='53', menuname='Garanties', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='55', menuname='Contenus', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='56', menuname='Système', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='57', menuname='Des groupes administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='58', menuname='Le menu de tête administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='59', menuname='Automatismes', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='60', menuname='Directeur de module', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='62', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='63', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='64', menuname='Système info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='65', menuname='Papoo mises à jour', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='48', menuname='Formulaire de contact', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='66', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='67', menuname='La catégorie fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='68', menuname='La catégorie travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='69', menuname='Des catégories trier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='70', menuname='Des vidéos administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='71', menuname='Des vidéos fusionner', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='73', menuname='Contact de champs', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='72', menuname='Vidéo catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='3', menuname='Controllare la tribuna', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='4', menuname='Controllare l\'utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='5', menuname='Controllare Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='6', menuname='Nuovo gruppo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='7', menuname='Nuovo utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='8', menuname='Pubblicare l\'utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='9', menuname='Pubblicare il gruppo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='10', menuname='Nuovo articolo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='11', menuname='Pubblicare l\'articolo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='40', menuname='Generare la tribuna', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='42', menuname='Pubblicare i messaggi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='14', menuname='Controllare il menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='15', menuname='Controllare 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='16', menuname='Nuova entrata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='17', menuname='Pubblicare l\'entrata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='18', menuname='Dati matrici', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='19', menuname='Controllare le immagini', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='20', menuname='Immagini di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='21', menuname='Pubblicare le immagini', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='22', menuname='CSS - Disposizione', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='1', menuname='Domestico', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='24', menuname='Entrare nei collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='25', menuname='Pubblicare i collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='26', menuname='autom. Portata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='27', menuname='Entrare nella portata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='28', menuname='Pubblicare Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='29', menuname='Cambiamento di lingua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='30', menuname='Pubblicare il cambiamento di lingua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='31', menuname='Lime di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='32', menuname='Upload le lime', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='33', menuname='Pubblicare le lime', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='41', menuname='Pubblicare le tribune', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='43', menuname='Generare la voce di menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='34', menuname='Leggere le poste', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='36', menuname='Aprire gli articoli', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='37', menuname='Articoli pubblicati', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='38', menuname='Pers. Dati', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='35', menuname='Scrivere le poste', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='44', menuname='Pubblicare le voci di menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='45', menuname='Cambiare la sequenza', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='46', menuname='Cambiare la sequenza', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='47', menuname='Responsabile Plugin', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='50', menuname='Controllare i permessi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='49', menuname='Homepage-Avviare', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='51', menuname='Lima di CSS di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='53', menuname='Depositi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='54', menuname='Collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='55', menuname='Indice', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='56', menuname='Sistema', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='57', menuname='Controllare i gruppi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='58', menuname='Controllare la menu-parte superiore', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='60', menuname='Modul-responsabile', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='62', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='63', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='64', menuname='Sistema Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='65', menuname='Aggiornamento di Papoo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='48', menuname='Soddisfare di Contactform', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='70', menuname='controllare i videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='73', menuname='forma del contatto', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='71', menuname='incastonare i videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='72', menuname='video categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='66', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='67', menuname='Generare le categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='68', menuname='Cambiare le categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='69', menuname='Categorie di ordine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='3', menuname='Controlar o Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='4', menuname='Controlar o usuário', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='5', menuname='Controlar Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='6', menuname='Grupo novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='7', menuname='Usuário novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='8', menuname='Editar o usuário', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='9', menuname='Editar o grupo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='10', menuname='Artigo novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='11', menuname='Editar o artigo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='40', menuname='Criar o Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='42', menuname='Editar mensagens', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='14', menuname='Controlar o menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='15', menuname='Controlar 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='16', menuname='Entrada nova', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='17', menuname='Editar a entrada', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='18', menuname='Dados mestres', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='19', menuname='Controlar retratos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='20', menuname='Retratos do Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='21', menuname='Editar retratos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='22', menuname='CSS - Disposição', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='1', menuname='Home', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='24', menuname='Incorporar as ligações', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='25', menuname='Editar as ligações', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='26', menuname='autom. Extensão', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='27', menuname='Entrar na extensão', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='28', menuname='Editar Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='29', menuname='Mudança da língua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='30', menuname='Editar a mudança da língua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='31', menuname='Limas do Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='32', menuname='Upload as limas', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='33', menuname='Editar limas', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='41', menuname='Editar Forums', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='43', menuname='Criar o artigo de menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='34', menuname='Ler correios', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='36', menuname='Abrir artigos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='37', menuname='Artigos publicados', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='38', menuname='Pers. Dados', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='35', menuname='Escrever correios', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='44', menuname='Editar artigos de menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='45', menuname='Mudar a seqüência', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='46', menuname='Mudar a seqüência', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='47', menuname='Gerente Plugin', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='50', menuname='Controlar permissões', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='49', menuname='Homepage-Iniciar', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='51', menuname='Lima do CSS do Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='53', menuname='Dumps', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='54', menuname='Encaixes', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='55', menuname='Índices', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='56', menuname='Sistema', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='57', menuname='Controlar grupos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='58', menuname='Controlar o menu-alto', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='60', menuname='Modul-gerente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='62', menuname='Categorias', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='63', menuname='Categorias', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='64', menuname='Sistema Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='65', menuname='Update de Papoo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='48', menuname='Índice de Contactform', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='70', menuname='controlar videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='73', menuname='formulário do contato', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='71', menuname='encaixar videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='72', menuname='categorias video', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='66', menuname='Categorias', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='67', menuname='Criar categorias', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='68', menuname='Mudar categorias', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='69', menuname='Categorias da ordem', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='3', menuname='Manejar el foro', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='4', menuname='Manejar a usuario', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='5', menuname='Manejar Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='6', menuname='Nuevo grupo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='7', menuname='Nuevo usuario', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='8', menuname='Corregir a usuario', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='9', menuname='Corregir a grupo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='10', menuname='Nuevo artículo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='11', menuname='Corregir el artículo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='40', menuname='Crear el foro', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='42', menuname='Corregir los mensajes', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='14', menuname='Manejar el menú', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='15', menuname='Manejar 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='16', menuname='Nueva entrada', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='17', menuname='Corregir la entrada', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='18', menuname='Datos principales', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='19', menuname='Manejar los cuadros', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='20', menuname='Cuadros del Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='21', menuname='Corregir los cuadros', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='22', menuname='CSS - Disposición', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='1', menuname='Casero', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='24', menuname='Incorporar los acoplamientos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='25', menuname='Corregir los acoplamientos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='26', menuname='autom. Palmo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='27', menuname='Entrar en el palmo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='28', menuname='Corregir Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='29', menuname='Cambio de la lengua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='30', menuname='Corregir el cambio de la lengua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='31', menuname='Archivos del Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='32', menuname='Upload los archivos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='33', menuname='Corregir los archivos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='41', menuname='Corregir los foros', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='43', menuname='Crear el artículo de menú', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='34', menuname='Leer los correos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='36', menuname='Abrir los artículos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='37', menuname='Artículos publicados', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='38', menuname='Pers. Datos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='35', menuname='Escribir los correos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='44', menuname='Corregir los artículos de menú', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='45', menuname='Cambiar la secuencia', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='46', menuname='Cambiar la secuencia', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='47', menuname='Encargado Plugin', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='50', menuname='Manejar los permisos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='49', menuname='Homepage-Empezar', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='51', menuname='Archivo del CSS del Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='53', menuname='Descargas', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='55', menuname='Contenido', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='56', menuname='Sistema', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='57', menuname='Manejar a grupos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='58', menuname='Manejar la menú-tapa', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='60', menuname='Modul-encargado', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='62', menuname='Categorías', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='63', menuname='Categorías', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='64', menuname='Sistema Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='65', menuname='Actualización de Papoo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='48', menuname='Contenido de Contactform', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='70', menuname='manejar los videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='73', menuname='forma del contacto', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='71', menuname='encajar los videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='72', menuname='categorías video', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='66', menuname='Categorías', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='67', menuname='Crear las categorías', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='68', menuname='Cambiar las categorías', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='69', menuname='Categorías de la orden', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='81', menuname='Layout', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='82', menuname='Blacklist', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='83', menuname='Templates', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='84', menuname='Seiten Templates', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='85', menuname='Content Templates', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='86', menuname='Neues Layout', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='87', menuname='Logfiles', back_front='1', lang_title=''  ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET untermenuzu='81',  level='1', order_id='4' WHERE  menuid='22' ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET untermenuzu='81',  level='1', order_id='10' WHERE  menuid='60' ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET order_id='5' WHERE  menuid='56' ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='81', menuname='Layout', menulink='content.php', menutitel='Bearbeitung Layout', untermenuzu='0', lese_rechte='g1,', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/layout.png', order_id='6'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='82', menuname='Blacklist', menulink='blacklist.php', menutitel='Blacklist', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/blacklist.png', order_id='55'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='83', menuname='Templates', menulink='template.php', menutitel='Bearbeitung Templates', untermenuzu='81', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/templates.png', order_id='5'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='84', menuname='Seiten Templates', menulink='template.php', menutitel='Bearbeitung Templates', untermenuzu='83', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/template_site.png', order_id='5'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='85', menuname='Content Templates', menulink='template.php', menutitel='Content Templates', untermenuzu='83', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/content_template.png', order_id='6'  ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET  order_id='5' WHERE  menuid='83' ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET   menulink='template.php' WHERE  menuid='84' ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET   menulink='template.php' WHERE  menuid='85' ; ##b_dump##
UPDATE `XXX_papoo_name_language` SET lang_short='es' WHERE lang_id='4' ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='86', menuname='Neues Layout', menulink='template.php', menutitel='Neues Layout', untermenuzu='81', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/newlayout.png', order_id='16'  ; ##b_dump##
UPDATE `XXX_papoo_menuint` SET  order_id='16' WHERE  menuid='86' ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='87', menuname='Logfiles', menulink='logfile.php', menutitel='Logfiles', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/logfiles.png', order_id='100'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_content_templates`; ##b_dump##
CREATE TABLE `XXX_papoo_content_templates` (
  `ctempl_id` int(11) NOT NULL auto_increment ,
  `ctempl_lang_id` int(11) NOT NULL ,
  `ctempl_name` text NOT NULL ,
  `ctempl_content` text NOT NULL ,
  PRIMARY KEY (`ctempl_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_repore` SET reporeID='12', text='', text_bbcode='', cattextid='1', dokuser='10', dokgruppe='1', dokschreibengrid='', dokschreiben_userid='1', doklesengrid='g1,', ueberschrift='', uberschrift_hyn='1', timestamp='2007-12-09 18:31:16', erstellungsdatum='2007-12-09 18:31:44', verfall='', pub_verfall='0', pub_start='0', pub_verfall_page='0', pub_start_page='0', pub_wohin='0', pub_danach_aktiv='', pub_dauerhaft='1', publish_yn='1', publish_yn_intra='0', intranet_yn='0', count='1', teaser='', teaser_list='0', teaser_bbcode='', teaser_text='', teaser_bild='', teaser_bild_html='', teaser_link='', teaser_bild_lr='', teaser_atyn='1', count_download='', allow_publish='1', comment_yn='0', allow_other='0', order_id='1', allow_comment='0', list_site='', artikel_news_list='', teaser_bild_groesse='x', teaser_menu='', teaser_sub_menu='', teaser_artikel='', order_id_start='1', stamptime='1197221476'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_article` SET article_id='12', gruppeid_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_article` SET article_id='12', gruppeid_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_write_article` SET article_wid_id='12', gruppeid_wid_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_write_article` SET article_wid_id='12', gruppeid_wid_id='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_language_article` SET lan_repore_id='12', lang_id='1', header='Hilfe und Antworten', lan_teaser='', lan_teaser_link='', lan_teaser_img_alt='', lan_teaser_img_title='', lan_teaser_img_fertig='', lan_article='Hier können Sie Hilfen und Antworten eintragen.', lan_article_sans='Hier können Sie Hilfen und Antworten eintragen.', lan_article_markdown='', lan_metatitel='', lan_metadescrip='', lan_metakey='', lan_rss_yn='0', url_header='-1-12'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_cache_dat`; ##b_dump##
CREATE TABLE `XXX_papoo_cache_dat` (
  `cache_id` int(100) NOT NULL auto_increment ,
  `cache_user_id` int(11) NOT NULL ,
  `cache_array_name` text NOT NULL ,
  `cache_array_dat` longtext NOT NULL ,
  PRIMARY KEY (`cache_id`) 
) ENGINE=MyISAM ; ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_name_language`; ##b_dump##
CREATE TABLE `XXX_papoo_name_language` (
  `lang_id` int(11) NOT NULL auto_increment ,
  `lang_short` text NOT NULL ,
  `lang_long` text NOT NULL ,
  `more_lang` int(11) NOT NULL  DEFAULT '1' ,
  `lang_img` text NOT NULL ,
  `lang_dir` varchar(255) NOT NULL  DEFAULT 'ltr' ,
  `lang_dateformat_short` varchar(50) NOT NULL  DEFAULT '%m.%d.%G; %H:%M:%S h' ,
  `lang_dateformat_long` varchar(50) NOT NULL  DEFAULT '%A, %B %d, %G; %H:%M:%S h' ,
  PRIMARY KEY (`lang_id`) 
) ENGINE=MyISAM ; ##b_dump##

INSERT INTO `XXX_papoo_name_language` SET lang_id='1', lang_short='de', lang_long='Deutsch', more_lang='2', lang_img='deutsch.jpg', lang_dir='ltr', lang_dateformat_short='%d.%m.%G; %H:%M:%S Uhr', lang_dateformat_long='%A, %d. %B %G; %H:%M:%S Uhr'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='2', lang_short='en', lang_long='English', more_lang='2', lang_img='engl.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='3', lang_short='it', lang_long='Italiano', more_lang='1', lang_img='ital.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='4', lang_short='es', lang_long='Español', more_lang='1', lang_img='span.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='5', lang_short='fr', lang_long='Français', more_lang='1', lang_img='fr.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='6', lang_short='pt', lang_long='Português', more_lang='1', lang_img='port.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##
INSERT INTO `XXX_papoo_name_language` SET lang_id='7', lang_short='nl', lang_long='Nederlands', more_lang='1', lang_img='nl.jpg', lang_dir='ltr', lang_dateformat_short='%m.%d.%G; %H:%M:%S h', lang_dateformat_long='%A, %B %d, %G; %H:%M:%S h'  ; ##b_dump##

ALTER TABLE `XXX_papoo_user` ADD `user_mv_id` int(11) NOT NULL ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_lookup_art_cat`; ##b_dump##
CREATE TABLE `XXX_papoo_lookup_art_cat` (
  `lart_id` int(11) NOT NULL auto_increment ,
  `lcat_id` int(11) NOT NULL,
  `lart_order_id` int(11) NOT NULL,
  PRIMARY KEY (`lart_id`,`lcat_id`) 
) ENGINE=MyISAM ; ##b_dump##

DROP TABLE IF EXISTS `XXX_papoo_version_lookup_art_cat`; ##b_dump##
CREATE TABLE `XXX_papoo_version_lookup_art_cat` (
  `lart_id` int(11) NOT NULL auto_increment ,
  `lcat_id` int(11) NOT NULL,
  `lart_order_id` int(11) NOT NULL,
  PRIMARY KEY (`lart_id`,`lcat_id`) 
) ENGINE=MyISAM ; ##b_dump##
ALTER TABLE `XXX_papoo_me_nu` CHANGE `menulink` `menulink` TEXT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_repore` CHANGE `erstellungsdatum` `erstellungsdatum` VARCHAR(255) ##b_dump##


ALTER TABLE `XXX_papoo_daten` ADD `stamm_benach_artikelfreigabe_email` int(4) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_daten` ADD `stamm_documents_change_backup` int(4) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_user` ADD `user_content_tree_show_all` int(4) NOT NULL DEFAULT '0'; ##b_dump## 

DROP TABLE IF EXISTS `XXX_papoo_download_versionen`; ##b_dump##
CREATE TABLE `XXX_papoo_download_versionen` (
  `dv_id` bigint(20) NOT NULL auto_increment ,
  `dv_downloadid` bigint(20) NULL ,
  `dv_downloadlink` varchar(255) NULL ,
  `dv_downloaduserid` int(11) NULL ,
  `dv_zeitpunkt` varchar(50) NULL ,
  `dv_org_filename` varchar(255) NULL ,
  PRIMARY KEY (`dv_id`),
  KEY `dv_downloadid` (`dv_downloadid`),
  KEY `dv_zeitpunkt` (`dv_zeitpunkt`) 
) ENGINE=MyISAM ; ##b_dump##
