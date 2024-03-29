SET NAMES 'utf8' ; ##b_dump## 
ALTER TABLE `XXX_papoo_menu_language` ADD `menulinklang` TEXT NOT NULL ; ##b_dump## 
ALTER TABLE `XXX_papoo_repore` CHANGE `erstellungsdatum` `erstellungsdatum` VARCHAR(255) ##b_dump##
ALTER TABLE `XXX_papoo_bannerverwaltung_daten` ADD `banner_views` int(11)  NULL  DEFAULT '0' ; ##b_dump## 

ALTER TABLE `XXX_papoo_keywords_tabelle` ADD `keywords_basis_der_tag_cloud` varchar(255) NOT NULL; ##b_dump## 
ALTER TABLE `XXX_papoo_keywords_tabelle` ADD `keywords_stop_wrter` varchar(255) NOT NULL; ##b_dump## 


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

ALTER TABLE `XXX_papoo_form_manager_lang` ADD `mail_an_betreiber_inhalt` text NOT NULL ; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_bilder` ADD `image_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_bilder` ADD `image_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 

ALTER TABLE `XXX_papoo_kategorie_dateien` ADD `dateien_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_dateien` ADD `dateien_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 

ALTER TABLE `XXX_papoo_kategorie_video` ADD `video_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_video` ADD `video_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 

ALTER TABLE `XXX_papoo_menu_language` ADD `url_metadescrip` text NOT NULL; ##b_dump## 
ALTER TABLE `XXX_papoo_menu_language` ADD `url_metakeywords` text NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_me_nu` ADD `menu_spez_layout` int(11) NOT NULL  DEFAULT '0'; ##b_dump##
ALTER TABLE `XXX_papoo_language_article` ADD `lan_article_search` LONGTEXT NULL ; ##b_dump##
ALTER TABLE `XXX_papoo_language_article` ADD FULLTEXT (`lan_article_search`); ##b_dump##
DROP TABLE IF EXISTS `XXX_papoo_config`; ##b_dump##
CREATE TABLE `XXX_papoo_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_metatagszusatz` text,
  `config_maximale_bildgrel` text,
  `config_maximale_bildgroesse_in_kb` text,
  `config_jpg_komprimierungsfaktor_feld_` text,
  `config_thumbnailgroesse` text,
  `config_paginierung` text,
  `config_session_lifetime` text,
  `config_memory_limit_feld` text,
  `config_skriptlaufzeit_feld` text,
  `config_upload_dateigre_feld` text,
  `config_404_benutzen_feld` text,
  `config_adresse_der_404_seite` text,
  `config_404_benutzen_check` varchar(255) NOT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_papoo_config` SET config_id='1', config_metatagszusatz='<meta name=\"verify-v1\" content=\"xyz\" />', config_maximale_bildgrel='4000x4000', config_maximale_bildgroesse_in_kb='', config_jpg_komprimierungsfaktor_feld_='80', config_thumbnailgroesse='120x120', config_paginierung='4', config_session_lifetime='432000', config_memory_limit_feld='256', config_skriptlaufzeit_feld='120', config_upload_dateigre_feld='10000000', config_404_benutzen_feld='', config_adresse_der_404_seite='/papoo_trunk/index.php?menuid=1&reporeid=7', config_404_benutzen_check='1'  ; ##b_dump##

ALTER TABLE `XXX_papoo_config` ADD `config_jquery_aktivieren_label` VARCHAR( 255 ) NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_config` ADD `config_jquery_colorbox_aktivieren` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_config` ADD `config_basis_js_funktionen_frontend` VARCHAR( 255 ) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_user` ADD `user_last_login` varchar(30) NOT NULL; ##b_dump##

ALTER TABLE `XXX_papoo_user` ADD `user_konfiguration_des_tinymce` text; ##b_dump##



ALTER TABLE `XXX_papoo_user` ADD `user_content_tree_show_all` text; ##b_dump##

DELETE FROM `XXX_papoo_men_uint_language` WHERE menuid_id<'999'; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='1', menuname='Home', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='22', menuname='Styles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='21', menuname='Editar retratos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='20', menuname='Retratos do Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='19', menuname='Controlar retratos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='18', menuname='Dados mestres', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='17', menuname='Editar a entrada', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='16', menuname='Entrada nova', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='15', menuname='Controlar 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='14', menuname='Controlar o menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='42', menuname='Editar mensagens', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='40', menuname='Criar o Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='11', menuname='Editar o artigo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='10', menuname='Artigo novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='9', menuname='Editar o grupo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='8', menuname='Editar o usuário', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='7', menuname='Usuário novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='6', menuname='Grupo novo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='5', menuname='Controlar Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='4', menuname='Controlar o usuário', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='6', menuid_id='3', menuname='Controlar o Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='69', menuname='Categorie di ordine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='68', menuname='Cambiare le categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='67', menuname='Generare le categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='66', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='72', menuname='video categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='71', menuname='incastonare i videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='73', menuname='forma del contatto', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='70', menuname='controllare i videos', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='48', menuname='Soddisfare di Contactform', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='65', menuname='Aggiornamento di Papoo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='64', menuname='Sistema Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='63', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='62', menuname='Categorie', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='58', menuname='Controllare la menu-parte superiore', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='57', menuname='Controllare i gruppi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='56', menuname='Sistema', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='55', menuname='Indice', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='54', menuname='Collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='53', menuname='Depositi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='51', menuname='Lima di CSS di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='49', menuname='Homepage-Avviare', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='50', menuname='Controllare i permessi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='47', menuname='Responsabile Plugin', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='46', menuname='Cambiare la sequenza', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='45', menuname='Cambiare la sequenza', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='44', menuname='Pubblicare le voci di menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='35', menuname='Scrivere le poste', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='38', menuname='Pers. Dati', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='37', menuname='Articoli pubblicati', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='36', menuname='Aprire gli articoli', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='34', menuname='Leggere le poste', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='43', menuname='Generare la voce di menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='41', menuname='Pubblicare le tribune', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='33', menuname='Pubblicare le lime', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='32', menuname='Upload le lime', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='31', menuname='Lime di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='30', menuname='Pubblicare il cambiamento di lingua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='29', menuname='Cambiamento di lingua', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='28', menuname='Pubblicare Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='27', menuname='Entrare nella portata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='26', menuname='autom. Portata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='25', menuname='Pubblicare i collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='24', menuname='Entrare nei collegamenti', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='1', menuname='Domestico', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='22', menuname='Styles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='21', menuname='Pubblicare le immagini', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='20', menuname='Immagini di Upload', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='19', menuname='Controllare le immagini', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='18', menuname='Dati matrici', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='17', menuname='Pubblicare l\'entrata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='16', menuname='Nuova entrata', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='15', menuname='Controllare 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='14', menuname='Controllare il menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='42', menuname='Pubblicare i messaggi', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='40', menuname='Generare la tribuna', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='11', menuname='Pubblicare l\'articolo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='10', menuname='Nuovo articolo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='9', menuname='Pubblicare il gruppo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='8', menuname='Pubblicare l\'utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='7', menuname='Nuovo utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='6', menuname='Nuovo gruppo', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='5', menuname='Controllare Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='4', menuname='Controllare l\'utente', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='3', menuid_id='3', menuname='Controllare la tribuna', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='72', menuname='Vidéo catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='73', menuname='Contact de champs', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='71', menuname='Des vidéos fusionner', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='70', menuname='Des vidéos administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='69', menuname='Des catégories trier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='68', menuname='La catégorie travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='67', menuname='La catégorie fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='66', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='48', menuname='Formulaire de contact', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='65', menuname='Papoo mises à jour', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='64', menuname='Système info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='63', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='62', menuname='Catégories', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='61', menuname='Info', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='59', menuname='Automatismes', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='58', menuname='Le menu de tête administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='57', menuname='Des groupes administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='56', menuname='Système', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='55', menuname='Contenus', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='53', menuname='Garanties', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='51', menuname='CSS fichier hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='49', menuname='Contenu de côté de commencement', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='50', menuname='Administration juridique', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='47', menuname='Plugin directeurs', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='46', menuname='Séquence', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='45', menuname='Séquence', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='44', menuname='Le menu travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='35', menuname='Des Mails écrivent', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='38', menuname='Pers. Données', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='37', menuname='Les articles publiés', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='36', menuname='Les articles ouverts', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='34', menuname='Des Mails lisent', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='43', menuname='Le menu fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='41', menuname='Des forums travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='33', menuname='Des fichiers travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='32', menuname='Fichiers hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='31', menuname='Des fichiers administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='30', menuname='Sprachausz. modifier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='29', menuname='Sprachausz. suggérer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='28', menuname='Raccourcir. travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='27', menuname='Raccourcir. suggérer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='26', menuname='Auszeichungen automat.', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='25', menuname='Modifier à gauche', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='24', menuname='Suggérer à gauche', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='23', menuname='Moteur de lien', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='1', menuname='Page de commencement', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='22', menuname='Styles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='21', menuname='Des images travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='20', menuname='Images hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='19', menuname='Des images administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='18', menuname='Données de base', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='17', menuname='Une entrée travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='16', menuname='Nouvelle entrée', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='15', menuname='3.Spalte administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='14', menuname='Le menu administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='42', menuname='Des informations travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='40', menuname='Des forums fournir', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='11', menuname='Les articles modifier', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='10', menuname='Le nouvel article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='9', menuname='Un groupe travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='8', menuname='Des utilisateurs travailler', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='7', menuname='Nouvel utilisateur', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='6', menuname='Nouveau groupe', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='3', menuname='Le forum administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='5', menuname='Les articles administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='5', menuid_id='4', menuname='Des utilisateurs administrer', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='69', menuname='Order categories', back_front='1', lang_title='Order Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='68', menuname='Change categories', back_front='1', lang_title='Change Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='67', menuname='Create categories', back_front='1', lang_title='Create Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='66', menuname='Categories', back_front='1', lang_title='Categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='72', menuname='video categories', back_front='1', lang_title='Video categories'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='72', menuname='Video Kategorien', back_front='1', lang_title='Video Kategorien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='71', menuname='embed videos', back_front='1', lang_title='embed Videos'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='73', menuname='contact form', back_front='1', lang_title='Manage the contact form'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='73', menuname='Kontakt Felder', back_front='1', lang_title='Kontakt Felder bearbeiten und erstellen'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='71', menuname='Videos bearbeiten', back_front='1', lang_title='Videos bearbeiten'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='70', menuname='manage videos', back_front='1', lang_title='Videos'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='70', menuname='Videos verwalten', back_front='1', lang_title='Videos verwalten'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='69', menuname='Kategorien sortieren', back_front='1', lang_title='Kategorie sortieren'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='68', menuname='Kategorie bearbeiten', back_front='1', lang_title='Kategorie bearbeiten'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='67', menuname='Kategorie erstellen', back_front='1', lang_title='Kategorie erstellen'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='66', menuname='Kategorien', back_front='1', lang_title='Kategorien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='48', menuname='Contactform content', back_front='1', lang_title='Text für das Kontakt-Formular'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='48', menuname='Kontakt-Formular', back_front='1', lang_title='Text für das Kontakt-Formular'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='65', menuname='Papoo Update', back_front='1', lang_title='Papoo Update'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='65', menuname='Papoo Update', back_front='1', lang_title='Papoo Update'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='64', menuname='System Info', back_front='1', lang_title='System Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='64', menuname='Server PHP Info', back_front='1', lang_title='System Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='63', menuname='Categories', back_front='1', lang_title='Dir for files'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='63', menuname='Kategorien', back_front='1', lang_title='Ordner für Dateien'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='62', menuname='Categories', back_front='1', lang_title='Dir for images'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='62', menuname='Kategorien', back_front='1', lang_title='Ordner für Bilder'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='61', menuname='Info', back_front='1', lang_title='Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='61', menuname='CMS Info', back_front='1', lang_title='Info'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='59', menuname='Automatics', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='59', menuname='Automatismen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='58', menuname='Manage menu-top', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='58', menuname='Kopf-Menü verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='57', menuname='Manage groups', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='57', menuname='Gruppen verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='56', menuname='System', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='56', menuname='System', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='55', menuname='Contents', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='55', menuname='Inhalte', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='54', menuname='Plugins', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='53', menuname='Dumps', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='53', menuname='Sicherungen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='51', menuname='Upload CSS File', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='51', menuname='CSS Datei hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='49', menuname='Homepage-Start', back_front='1', lang_title='Metadata and Headtext'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='49', menuname='Startseite-Inhalt', back_front='1', lang_title='Metadaten und Kopftext'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='50', menuname='Manage permissions', back_front='1', lang_title='Manage Rights'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='50', menuname='Rechteverwaltung', back_front='1', lang_title='Verwaltung der Zugriffsrechte auf die Menuepunkte'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='47', menuname='Plugin Manager', back_front='1', lang_title='Administrate Plugins'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='47', menuname='Plugin Manager', back_front='1', lang_title='Hier koennen Plugins installiert werden'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='46', menuname='Change sequence', back_front='1', lang_title='Change sequence of menu items'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='46', menuname='Reihenfolge ', back_front='1', lang_title='Reihenfolge der Menuepunkte aendern'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='45', menuname='Change sequence', back_front='1', lang_title='Change sequence of the articles'  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='45', menuname='Reihenfolge ', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='44', menuname='Edit Menu items', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='35', menuname='Write mails', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='38', menuname='Pers. Data', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='37', menuname='Published articles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='36', menuname='Open articles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='34', menuname='Read Mails', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='43', menuname='Create menu item', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='41', menuname='Edit Forums', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='33', menuname='Edit Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='32', menuname='Upload the Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='31', menuname='Upload Files', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='30', menuname='Edit Language Change', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='29', menuname='Language change', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='28', menuname='Edit Abbreveation', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='27', menuname='Enter Span', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='26', menuname='autom. Span', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='25', menuname='Edit Links', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='24', menuname='Enter Links', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='1', menuname='Home', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='22', menuname='Styles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='21', menuname='Edit Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='20', menuname='Upload Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='19', menuname='Manage Pictures', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='18', menuname='Master Data', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='17', menuname='Edit Entry', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='16', menuname='New Entry', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='15', menuname='Manage 3. Collum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='14', menuname='Manage Menu', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='42', menuname='Edit Messages', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='40', menuname='Create Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='11', menuname='Edit Article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='10', menuname='New Article', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='9', menuname='Edit group', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='8', menuname='Edit User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='7', menuname='New User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='6', menuname='New group', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='5', menuname='Manage Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='4', menuname='Manage User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='2', menuid_id='3', menuname='Manage Forum', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='44', menuname='Menü bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='35', menuname='Mails schreiben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='38', menuname='Pers. Daten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='37', menuname='Publizierte Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='36', menuname='Offene Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='34', menuname='Mails lesen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='43', menuname='Menü erstellen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='41', menuname='Foren bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='33', menuname='Dateien bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='32', menuname='Dateien hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='31', menuname='Dateien verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='30', menuname='Sprachausz. ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='29', menuname='Sprachausz. eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='28', menuname='Abkürz. bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='27', menuname='Abkürz. eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='26', menuname='Autom. Auszeichungen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='25', menuname='Links ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='24', menuname='Links eingeben', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='23', menuname='Linkengine', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='1', menuname='Start', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='22', menuname='Styles + Module', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='21', menuname='Bilder bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='20', menuname='Bilder hochladen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='19', menuname='Bilder verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='18', menuname='Konfiguration', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='17', menuname='Eintrag bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='16', menuname='Neuer Eintrag', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='15', menuname='3.Spalte verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='14', menuname='Menü verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='42', menuname='Nachrichten bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='40', menuname='Foren erstellen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='11', menuname='Artikel ändern', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='10', menuname='Neuer Artikel', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='9', menuname='Gruppe bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='8', menuname='User bearbeiten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='7', menuname='Neuer User', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='6', menuname='Neue Gruppe', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='5', menuname='Artikel verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='4', menuname='User verwalten', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='3', menuname='Forum verwalten', back_front='1', lang_title=''  ; ##b_dump##
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
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='4', menuid_id='22', menuname='Styles', back_front='1', lang_title=''  ; ##b_dump##
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
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='83', menuname='Inhalte-Vorlagen', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='87', menuname='Logfiles', back_front='1', lang_title=''  ; ##b_dump##
INSERT INTO `XXX_papoo_men_uint_language` SET lang_id='1', menuid_id='88', menuname='Videos hochladen', back_front='1', lang_title=''  ; ##b_dump##

DELETE FROM `XXX_papoo_lookup_men_int` WHERE menuid<'999'; ##b_dump##

INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='1', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='1', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='1', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='3', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='4', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='5', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='5', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='5', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='6', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='7', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='8', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='9', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='10', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='10', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='10', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='11', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='11', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='11', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='12', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='13', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='14', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='14', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='15', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='15', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='15', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='16', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='16', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='17', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='17', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='18', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='19', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='19', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='19', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='19', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='20', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='20', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='20', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='20', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='21', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='21', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='21', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='22', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='22', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='23', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='23', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='23', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='24', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='24', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='24', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='25', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='25', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='25', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='26', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='26', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='26', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='26', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='27', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='27', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='27', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='27', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='28', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='28', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='28', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='28', gruppenid='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='29', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='29', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='29', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='30', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='30', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='30', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='31', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='31', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='31', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='32', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='32', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='32', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='33', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='33', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='33', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='34', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='34', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='34', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='35', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='35', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='35', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='36', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='36', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='36', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='37', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='37', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='37', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='38', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='38', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='38', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='39', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='40', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='41', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='42', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='43', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='43', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='44', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='44', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='45', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='45', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='45', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='46', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='46', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='47', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='48', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='49', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='49', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='50', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='51', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='53', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='54', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='54', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='54', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='55', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='55', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='55', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='56', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='56', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='57', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='58', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='58', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='58', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='59', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='59', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='59', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='61', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='62', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='62', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='62', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='63', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='63', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='63', gruppenid='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='64', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='65', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='66', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='67', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='68', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='69', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='70', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='71', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='72', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='73', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='81', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='82', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='83', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='87', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_int` SET menuid='88', gruppenid='1'  ; ##b_dump##
DELETE FROM `XXX_papoo_menuint` WHERE menuid<'999'; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='3', menuname='Forum verwalten', menulink='forum.php', menutitel='Administrierung des Forums', untermenuzu='55', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/forum.gif', order_id='15'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='4', menuname='User verwalten', menulink='user.php', menutitel='Administrierung der User und Gruppen', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/user.gif', order_id='21'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='5', menuname='Artikel verwalten', menulink='artikel.php', menutitel='Hier koennen die Artikel verwaltet werden', untermenuzu='55', lese_rechte='g1,g18,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/artikel.gif', order_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='6', menuname='neue Gruppe', menulink='user.php', menutitel='Neue Gruppe einf', untermenuzu='57', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/gruppe_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='7', menuname='neuer User', menulink='user.php', menutitel='Einen neuen User einf', untermenuzu='4', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/user_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='8', menuname='User bearbeiten', menulink='user.php', menutitel='Einen vorhandenen User bearbeiten', untermenuzu='4', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/user_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='9', menuname='Gruppe bearbeiten', menulink='user.php', menutitel='Eine vorhandene Gruppe bearbeiten', untermenuzu='57', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/gruppe_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='10', menuname='neuer Artikel', menulink='artikel.php', menutitel='Einen neuen Artikel eingaben', untermenuzu='5', lese_rechte='g1,g18,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/artikel_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='11', menuname='Artikel', menulink='artikel.php', menutitel='Einen vorhandenen Artikel bearbeiten', untermenuzu='5', lese_rechte='g1,g18,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/artikel_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='40', menuname='Foren erstellen', menulink='forum.php', menutitel='Foren bearbeiten/erstellen', untermenuzu='3', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/forum_erstellen.gif', order_id='17'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='42', menuname='Nachrichten bearbeiten', menulink='forum.php', menutitel='Nachrichten bearbeiten', untermenuzu='3', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/forum_bearbeiten.gif', order_id='19'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='14', menuname='Menue', menulink='menu.php', menutitel='Hier koennen Menuepunkte verwaltet werden', untermenuzu='55', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/menu.gif', order_id='13'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='15', menuname='3.Spalte verwalten', menulink='spalte.php', menutitel='Hier kann die 3. Spalte verwaltet werden', untermenuzu='55', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/spalte.gif', order_id='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='16', menuname='neuer Eintrag', menulink='spalte.php', menutitel='Hier kann ein neuer Eintrag erstellt werden', untermenuzu='15', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/spalte_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='17', menuname='Eintrag bearbeiten', menulink='spalte.php', menutitel='Hier kann ein Eintrag bearbeitet werden', untermenuzu='15', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/spalte_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='18', menuname='Stammdaten', menulink='stamm.php', menutitel='Hier koennen die Stammdaten verwaltet werden', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/stamm.gif', order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='19', menuname='Bilder bearbeiten', menulink='image.php', menutitel='Hier kann man die Bilder bearbeiten', untermenuzu='55', lese_rechte='g9,g1,g3,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/bild.gif', order_id='16'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='20', menuname='Bilder hochladen', menulink='image.php', menutitel='Hier koennen Bilder hochgeladen werden', untermenuzu='19', lese_rechte='g9,g1,g3,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/bild_erstellen.gif', order_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='21', menuname='Bilderdaten bearbeiten', menulink='image.php', menutitel='Hier koennen Bilder bearbeitet werden', untermenuzu='19', lese_rechte='g9,g1,g3,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/bild_aendern.gif', order_id='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='22', menuname='Styles', menulink='styles.php', menutitel='Hier kann das gesamte Aussehen der Seite gesteuert werden ...', untermenuzu='81', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/templates.png', order_id='4'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='1', menuname='Meine Daten', menulink='index.php', menutitel='Hier werden alle persoenlichen Daten bearbeitet', untermenuzu='0', lese_rechte='g1,g2,g18,', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/home.gif', order_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='23', menuname='Linkengine', menulink='link.php', menutitel='Hier kann man Links eingeben', untermenuzu='59', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/link.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='24', menuname='Links eingeben', menulink='link.php', menutitel='Hier die Links eingeben', untermenuzu='23', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/link_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='25', menuname='Links', menulink='link.php', menutitel='Links', untermenuzu='23', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/link_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='26', menuname='autom. Auszeichungen', menulink='span.php', menutitel='Hier koennen automatische Auszeichnungen erstellt werden', untermenuzu='59', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/auszeichnung.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='27', menuname='Abk', menulink='span.php', menutitel='Hier werden Abkuerzungen erstellt', untermenuzu='26', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/abk_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='28', menuname='Abk', menulink='span.php', menutitel='Abkuerzungen bearbeiten', untermenuzu='26', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/abk_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='29', menuname='Sprachausz. eingeben', menulink='span.php', menutitel='Hier werden Sprachauszeichnungen eingegeben', untermenuzu='26', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/lang_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='30', menuname='Sprachausz.', menulink='span.php', menutitel='Hier werden Sprachauszeichnungen ge', untermenuzu='26', lese_rechte='g1,', level='3', schreibrechte='', intranet_yn='0', menu_icon='../bilder/lang_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='31', menuname='Dateien verwalten', menulink='upload.php', menutitel='Hier kann man Dateien hochladen', untermenuzu='55', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/file.gif', order_id='17'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='32', menuname='Dateien hochladen', menulink='upload.php', menutitel='Hier werden die Dateien hochgeladen', untermenuzu='31', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/file_open.gif', order_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='33', menuname='Dateien bearbeiten', menulink='upload.php', menutitel='Hier werden die hochgeladenen Dateien bearbeitet oder gel', untermenuzu='31', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/file_aendern.gif', order_id='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='41', menuname='Foren bearbeiten', menulink='forum.php', menutitel='Hier kann ein Forum bearbeitet werden', untermenuzu='3', lese_rechte='g1,', level='2', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/forum_bearbeiten.gif', order_id='18'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='43', menuname='Menue', menulink='menu.php', menutitel='Hier kann das Menue bearbeitet werden', untermenuzu='14', lese_rechte='g1,', level='2', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/menu_erstellen.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='34', menuname='Mails lesen', menulink='index.php', menutitel='', untermenuzu='1', lese_rechte='g1,', level='1', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/mail_get.gif', order_id='5'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='36', menuname='Offene Artikel', menulink='index.php', menutitel='Artikel die noch veroeffentlicht werden mÃ¼ssen', untermenuzu='1', lese_rechte='g1,', level='1', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/unknown.gif', order_id='7'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='37', menuname='publizierte Artikel', menulink='index.php', menutitel='Alle Artikel die Sie publiziert haben', untermenuzu='1', lese_rechte='g1,', level='1', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/list.gif', order_id='8'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='38', menuname='pers. Daten', menulink='index.php', menutitel='Ihre persoenlichen Daten', untermenuzu='1', lese_rechte='g1,', level='1', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/edit.gif', order_id='9'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='35', menuname='Mails schreiben', menulink='index.php', menutitel='Hier koennen interne Mails geschrieben werden.', untermenuzu='1', lese_rechte='g1,', level='1', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/mail_write.gif', order_id='6'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='44', menuname='Men', menulink='menu.php', menutitel='', untermenuzu='14', lese_rechte='g1,', level='2', schreibrechte='g1,', intranet_yn='0', menu_icon='../bilder/menu_aendern.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='45', menuname='Reihenfolge', menulink='artikel.php', menutitel='', untermenuzu='5', lese_rechte='1', level='2', schreibrechte='1', intranet_yn='0', menu_icon='../bilder/reihe.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='46', menuname='Reihenfolge', menulink='menu.php', menutitel='Reihenfolge der Menuepunkte aendern', untermenuzu='14', lese_rechte='1', level='2', schreibrechte='1', intranet_yn='0', menu_icon='../bilder/reihe.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='47', menuname='Plugin Manager', menulink='plugins.php', menutitel='HIer koennen Plugins erstellt werden', untermenuzu='54', lese_rechte='1', level='1', schreibrechte='1', intranet_yn='0', menu_icon='../bilder/plugin.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='50', menuname='Rechteverwaltung', menulink='stamm.php', menutitel='Verwaltung der Zugriffsrechte auf die Men', untermenuzu='56', lese_rechte='1', level='1', schreibrechte='1', intranet_yn='0', menu_icon='../bilder/rechte.gif', order_id='23'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='49', menuname='Meta / Daten', menulink='stamm.php', menutitel='Metadaten und Kopftext', untermenuzu='55', lese_rechte='1', level='1', schreibrechte='1', intranet_yn='0', menu_icon='../bilder/meta.gif', order_id='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='53', menuname='Sicherungen', menulink='stamm.php', menutitel='', untermenuzu='18', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/reload.gif', order_id='0'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='54', menuname='Plugins', menulink='content.php', menutitel='Plugins', untermenuzu='0', lese_rechte='', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/plugin.gif', order_id='3'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='55', menuname='Inhalte', menulink='content.php', menutitel='', untermenuzu='0', lese_rechte='', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/inhalt.gif', order_id='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='56', menuname='System', menulink='content.php', menutitel='', untermenuzu='0', lese_rechte='', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/system.gif', order_id='5'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='57', menuname='Gruppen verwalten', menulink='content.php', menutitel='', untermenuzu='56', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/user.gif', order_id='22'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='58', menuname='menutop', menulink='menutop.php', menutitel='', untermenuzu='55', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/menutop.gif', order_id='14'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='59', menuname='Automatismen', menulink='auto.php', menutitel='', untermenuzu='55', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/automatik.gif', order_id='19'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='61', menuname='Info', menulink='stamm.php', menutitel='Info', untermenuzu='18', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/info.gif', order_id='61'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='62', menuname='Ordner Bilder', menulink='ordner.php', menutitel='Info', untermenuzu='19', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/ordner.gif', order_id='3'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='63', menuname='Ordner Dateien', menulink='ordner.php', menutitel='Info', untermenuzu='31', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/ordner.gif', order_id='3'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='64', menuname='System Info', menulink='stamm.php', menutitel='System Info', untermenuzu='18', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/info.gif', order_id='64'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='65', menuname='Update', menulink='update.php', menutitel='Papoo Update', untermenuzu='18', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/info.gif', order_id='65'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='48', menuname='Kontakt Forumlar', menulink='stamm.php', menutitel='Inhalt des Kontakt Formulars', untermenuzu='56', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/kontakt.gif', order_id='56'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='66', menuname='Kategorien', menulink='kategorie.php', menutitel='Kategorien', untermenuzu='55', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/cat.gif', order_id='20'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='67', menuname='Kategorien erstellen', menulink='kategorie.php', menutitel='Kategorien erstellen', untermenuzu='66', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/cat.gif', order_id='58'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='68', menuname='Kategorien bearbeiten', menulink='kategorie.php', menutitel='Kategorien bearbeiten', untermenuzu='66', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/cat.gif', order_id='59'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='69', menuname='Kategorien sortieren', menulink='kategorie.php', menutitel='Kategorien bearbeiten', untermenuzu='66', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/cat.gif', order_id='560'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='70', menuname='Videos', menulink='video.php', menutitel='Videos', untermenuzu='55', lese_rechte='', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/video.gif', order_id='18'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='71', menuname='Videos einbinden', menulink='video.php', menutitel='Videos einbinden', untermenuzu='70', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/video.gif', order_id='2'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='72', menuname='Video Kategorien', menulink='ordner.php', menutitel='Video Kategorien', untermenuzu='70', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/video.gif', order_id='3'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='73', menuname='Formular Felder', menulink='kontaktform.php', menutitel='Kontakt Formular Felder bearbeiten', untermenuzu='48', lese_rechte='', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/kontakt.gif', order_id='563'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='81', menuname='Layout', menulink='content.php', menutitel='Bearbeitung Layout', untermenuzu='0', lese_rechte='g1,', level='0', schreibrechte='', intranet_yn='0', menu_icon='../bilder/layout.png', order_id='6'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='82', menuname='Blacklist', menulink='blacklist.php', menutitel='Blacklist', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/blacklist.png', order_id='55'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='83', menuname='Templates', menulink='template.php', menutitel='Bearbeitung Templates', untermenuzu='81', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/content_template.png', order_id='5'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='87', menuname='Logfiles', menulink='logfile.php', menutitel='Logfiles', untermenuzu='56', lese_rechte='g1,', level='1', schreibrechte='', intranet_yn='0', menu_icon='../bilder/logfiles.png', order_id='100'  ; ##b_dump##
INSERT INTO `XXX_papoo_menuint` SET menuid='88', menuname='Videos hochladen', menulink='video.php', menutitel='Videos hochladen', untermenuzu='70', lese_rechte='g1,', level='2', schreibrechte='', intranet_yn='0', menu_icon='../bilder/video.gif', order_id='1'  ; ##b_dump##


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
ALTER TABLE `XXX_papoo_me_nu` ADD `menu_spez_layout` int(11)  NULL  DEFAULT '0' ; ##b_dump## 
ALTER TABLE `XXX_papoo_gruppe` ADD `user_konfiguration_des_tinymce` TEXT NOT NULL ; ##b_dump## 

DELETE FROM `XXX_papoo_module_language`
WHERE modlang_mod_id=
	(SELECT mod_id
	  FROM `XXX_papoo_module`
	  WHERE mod_datei='_module/mod_efa_fontsize.html'
	  LIMIT 1); ##b_dump##
	  
DELETE FROM `XXX_papoo_module`
WHERE mod_datei='_module/mod_efa_fontsize.html'; ##b_dump##
