TRUNCATE TABLE `XXX_papoo_counter`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_stamm`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_repore`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_article`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_art_cat`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_article`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_write_article`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_version_article`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_version_repore`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_version_language_article`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_version_lookup_art_cat`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_version_lookup_article`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_version_lookup_write_article`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_teaser_lookup_art`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_teaser_lookup_men`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_collum3`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_collum3`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_lookup_me_all_ext`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_men_collum3`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_men_ext`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_me_nu`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_menu_language`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_menutop`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_menutop_language`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_images`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_image`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_image`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_download`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_download_versionen`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_download`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_lookup_download`; ##b_dump##

TRUNCATE TABLE `XXX_papoo_video`; ##b_dump##
TRUNCATE TABLE `XXX_papoo_language_video`; ##b_dump##


TRUNCATE TABLE `XXX_papoo_mail`; ##b_dump##


INSERT INTO `XXX_papoo_lookup_me_all_ext` SET menuid_id='1', gruppeid_id='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_me_all_ext` SET menuid_id='1', gruppeid_id='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_me_all_ext` SET menuid_id='1', gruppeid_id='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_me_all_ext` SET menuid_id='1', gruppeid_id='12'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_ext` SET menuid='1', gruppenid='1'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_ext` SET menuid='1', gruppenid='10'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_ext` SET menuid='1', gruppenid='11'  ; ##b_dump##
INSERT INTO `XXX_papoo_lookup_men_ext` SET menuid='1', gruppenid='12'  ; ##b_dump##

INSERT INTO `XXX_papoo_me_nu` SET menuid='1', menuname='Startseite', menulink='index.php', menutitel='Hier geht es zur Startseite', untermenuzu='0', level='0', lese_rechte='g1,g10,', schreibrechte='', intranet_yn='0', order_id='1', extra_css_file='', extra_css_sub=''  ; ##b_dump##
INSERT INTO `XXX_papoo_menu_language` SET lang_id='2', menuid_id='1', menuname='Home', back_front='1', lang_title='Way out to Startpage', url_menuname='home'  ; ##b_dump##
INSERT INTO `XXX_papoo_menu_language` SET lang_id='1', menuid_id='1', menuname='Startseite', back_front='1', lang_title='Hier geht es zur Startseite', url_menuname='startseite'  ; ##b_dump##
INSERT INTO `XXX_papoo_language_stamm` (`stamm_id` ,`lang_id` ,`head_title` ,`start_text` ,`start_text_sans` ,`beschreibung` ,`stichwort` ,`intranet_yn` ,`seitentitle` ,`kontakt_text` ,`kontakt_text_sans`)VALUES ('2', '1', '', '', '', '', '', '0', '', '', '') ; ##b_dump##
INSERT INTO `XXX_papoo_language_stamm` (`stamm_id` ,`lang_id` ,`head_title` ,`start_text` ,`start_text_sans` ,`beschreibung` ,`stichwort` ,`intranet_yn` ,`seitentitle` ,`kontakt_text` ,`kontakt_text_sans`)VALUES ('2', '2', '', '', '', '', '', '0', '', '', '') ; ##b_dump##
INSERT INTO `XXX_papoo_language_stamm` (`stamm_id` ,`lang_id` ,`head_title` ,`start_text` ,`start_text_sans` ,`beschreibung` ,`stichwort` ,`intranet_yn` ,`seitentitle` ,`kontakt_text` ,`kontakt_text_sans`)VALUES ('2', '3', '', '', '', '', '', '0', '', '', '') ; ##b_dump##
INSERT INTO `XXX_papoo_language_stamm` (`stamm_id` ,`lang_id` ,`head_title` ,`start_text` ,`start_text_sans` ,`beschreibung` ,`stichwort` ,`intranet_yn` ,`seitentitle` ,`kontakt_text` ,`kontakt_text_sans`)VALUES ('2', '4', '', '', '', '', '', '0', '', '', '') ; ##b_dump##
