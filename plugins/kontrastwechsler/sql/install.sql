DROP TABLE IF EXISTS `XXX_plugin_kontrastwechsler`; ##b_dump##
CREATE TABLE `XXX_plugin_kontrastwechsler` (
  `kontrastID` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `textColor` VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#000000',
  `backgroundColor` VARCHAR(9) COLLATE 'ascii_general_ci' NOT NULL DEFAULT '#FFFFFF',
  PRIMARY KEY (`kontrastID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler` SET `kontrastID`='1', `name`='Kontrast1', `description`='schwarz/weiß Kontrast', `textColor`='#FFFFFF', `backgroundColor`='#000000'; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler` SET `kontrastID`='2', `name`='Kontrast2', `description`='weiß/schwarz Kontrast', `textColor`='#000000', `backgroundColor`='#FFFFFF'; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler` SET `kontrastID`='3', `name`='Kontrast3', `description`='weinrot/weiß Kontrast', `textColor`='#FFFFFF', `backgroundColor`='#7B0000'; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler` SET `kontrastID`='4', `name`='Kontrast4', `description`='gelb/schwarz Kontrast', `textColor`='#000000', `backgroundColor`='#FFFF00'; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler` SET `kontrastID`='5', `name`='Kontrast5', `description`='schwarz/grün Kontrast', `textColor`='#8AF408', `backgroundColor`='#000000'; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_kontrastwechsler_einstellungen`; ##b_dump##
CREATE TABLE `XXX_plugin_kontrastwechsler_einstellungen` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `showEFA` INT(11) DEFAULT NULL,
  `moduleStyle` INT(11) DEFAULT NULL,
  `ownStyleCSS` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler_einstellungen` SET `id`='1', `showEFA`='0', `moduleStyle`='2', `ownStyleCSS`=''; ##b_dump##

DROP TABLE IF EXISTS `XXX_plugin_kontrastwechsler_css`; ##b_dump##
CREATE TABLE `XXX_plugin_kontrastwechsler_css` (
  `cssID` INT(11) NOT NULL AUTO_INCREMENT,
  `cssText` TEXT DEFAULT NULL,
  PRIMARY KEY (`cssID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ##b_dump##
INSERT INTO `XXX_plugin_kontrastwechsler_css` SET `cssID`='1', `cssText`='body, body p, header nav.top-bar ul li a, header #mod_freiemodule_5 a, body h1, body h2, body h3, body h4, body h5, footer #mod_menue_top ul li a
{
    color:#text_color#;
}
body, .top-bar, .top-bar-section ul li, .top-bar-section li:not(.has-form) a:not(.button), .top-bar-section li:not(.has-form) a:hover:not(.button), .top-bar-section li:not(.has-form) a:visited:not(.button), .subfooter
{
    background:#background_color#!important;
}
header #kopf_wrapper #mod_freiemodule_1 img, header #mod_freiemodule_5 #mod_efa_fontsize a, footer #map_footer #mod_freiemodule_3 > div img
{
    background:#text_color#;
}
header #mod_freiemodule_5 #mod_efa_fontsize a
{
    color:#text_color#;
    background:#background_color#;
    border:1px solid #text_color#;
}
footer.row, #mod_menue_top h2, .subfooter, .artikel_liste .teaserlink, #kopflogo, #main .artikel_liste h1, #main .row, #main a.teaselinkcm, .panel.callout, #main p, .startpagerow1 .large-4 h2 i, .startpagerow1 .large-4 li, .startpagerow1 .large-4 li i, .orbit-container .orbit-slides-container li .orbit-caption a, #main .row h3 i, #main .row h3, #mod_freiemodule_3, #mod_freiemodule_3 p, #mod_freiemodule_3 h2, #mod_freiemodule_3 i, .teaser_datum, #main .artikel_content1 h3 a, #main .artikel_dat1, #main .artikel_dat1 a, #main h2, #main a.button.secondary, .button.secondary, .columns, .kalender_modul td, #main .artikel_details h3, #sidebar h2 i, #main .artikel_details h1, .date_article, #mod_breadcrump, #mod_menue_sub li a.menuxaktiv_back, #main h3, label, select, select:hover, option, input[type="password"], p > a, li > a, h2 > a, h3 > a, .pin, #mod_breadcrump a, #mod_login input[type="submit"] ~ a, div.rightitem > a[href*="action_edit=1"], #content .kalender_front_content a, td > a, tr > th, acronym, #main ul.pagination li.current a, ul.pagination li a:focus, ul.pagination li a:hover, h6, .top-bar-section .has-dropdown a, fieldset, textarea, .forumboard
{
  background: #background_color#;
  border-color:#text_color#;
  color:#text_color#!important;
}
label > span
{
  background-color: #background_color#!important;
  color: #text_color#!important;
}
span > a
{
  background-color: transparent;
  color:#text_color#!important;
}
tr, table tr:nth-of-type(2n)
{
  background-color: #background_color#!important;
  border-color: #text_color#!important;
}
.orbit-container .orbit-slides-container li .orbit-caption a:hover, select:hover, option:focus, p > a:hover, li > a:hover, h2 > a:hover, h3 > a:hover, #mod_breadcrump a:hover, #mod_login input[type="submit"] ~ a:hover, td > a:hover
{
  background-color: #background_color#;
  text-decoration: underline;
}
#main .artikel_dat1 a:hover, .last_artikel_liste:hover .teaser_datum, #main a.button.secondary:hover, .artikel_liste .teaserlink:hover, #mod_menue_sub li a:hover, input[type="submit"]:hover, input[type="text"]:focus, input[type="password"]:focus, #content .kalender_front_content a:hover, span > a:hover
{
  text-decoration: underline;
  background-color: inherit;
}
table, .kalender_front_content table
{
  background: #text_color#;
  border: solid 1px #text_color#;
}
.kalender_front_content table td
{
  border-color: #text_color#;
}
div.orbit-timer
{
  background: transparent;
}
#main .artikel_liste a.teaserlink:hover
{
  background-color: #background_color#;
}
orbit-container .orbit-slides-container li .orbit-caption:hover a
{
  text-decoration: underline;
  background-color: #background_color#;
  color: #background_color#;
}
.last_artikel_liste:hover .cm_teaser_img .fasearchback
{
  background-color:rgba(138, 244, 8, 0.5);
}
.artikel_liste, #mod_menue_sub li a, select, .pin img
{
  border-bottom: 1px solid #text_color#;
}
.orbit-container
{
  border-bottom: 5px solid #text_color#;
}
nav.top-bar
{
  border-top: 1px solid #text_color#;
}
#main .large-8
{
  border-right: 1px solid #text_color#;
}
.top-bar-section li:not(.has-form)  a:not(.button).menuxaktiv_back, .top-bar-section li:not(.has-form) a:not(.button):hover
{
  border-bottom:3px solid #text_color#;
}
.top-bar-section li:not(.has-form) a:not(.button)
{
  border-bottom:3px solid #background_color#;
}
#page, #head, #main, #footer, #col1, #col2, #col3, #kopf-zeile, #mod_menue, #mod_suchbox, #suchen, #mod_suchbox .senden, #mod_suchbox #search, pre, #formk fieldset, #formk textarea, input[type="text"], input[type="submit"], #formk legend, strong, #col3 a, #col3 a:visited, #col3 a:hover, #col3 h1, #col3 h2, #col3 h3, #formk fieldset, .form fieldset, .form_newsletter fieldset, .form legend, #col3 #inhalt_sitemap h2 a, .select-selected
{
  background: #background_color#;
  border-color:#text_color#;
  color:#text_color#!important;
}
#col1 .modul h3, #col2 .modul h3, #col1 .modul h2, #col2 .modul h2, #mod_menue li a, #mod_menue li a:hover, #mod_menue li a:active, #mod_menue li a:focus, #mod_menue li a.menuxaktiv_back, #mod_menue li a.menuy_aktiv, #mod_menue .untermenu1 li a, #mod_menue .untermenu1 li a.menuxaktiv_back, #mod_menue .untermenu1 li a.menuy_aktiv, #mod_menue .untermenu1 li a.menuxaktiv:hover, #mod_menue .untermenu1 li a.menuxaktiv:active, #mod_menue .untermenu1 li a.menuxaktiv:focus
{
  color: inherit;
  background-color:#background_color#;
}
#mod_menue li a:hover, #mod_menue li a:active, #mod_menue li a:focus, .select-items div:hover, .same-as-selected
{
text-decoration: underline;
}
#col3 a:active
{
  background-color:#background_color#;
}
#main #sidebar #mod_dritte_spalte .rightitem h2, input[type="password"]:focus, input[type="text"]:focus
{
    border-color:#text_color#;
}
footer #map_footer #mod_freiemodule_3 > div
{
    background:rgba(0,0,0,0.5);
}
header nav.top-bar .top-bar-section .mod_menue_ul .dropdown, .top-bar-section .dropdown li:not(.has-form):not(.active) > a:not(.button),.top-bar-section .dropdown li:not(.has-form):not(.active) > a:hover:not(.button)
{
    background:#background_color#!important;
    color:#text_color#;
}
.top-bar-section .dropdown li:not(.has-form):not(.active) > a:not(.button)
{
    color:#text_color#;
}
header nav.top-bar .top-bar-section .mod_menue_ul > li >.menuxaktiv_back, header nav.top-bar .top-bar-section .mod_menue_ul > li >.menuy_aktiv
{
    border-color:#text_color#;
}
header #kopf_wrapper #mod_freiemodule_6 i, header #kopf_wrapper #mod_freiemodule_7 i , header #kopf_wrapper #mod_suchbox .senden
{
    background:#background_color#;
    color:#text_color#;
    border-color:#text_color#;
}
header #kopf_wrapper #mod_freiemodule_6 i, header #kopf_wrapper #mod_freiemodule_7 i
{
    border:1px solid #text_color#;
}
#main #mod_freiemodule_8 h1, body a:hover, #main #mod_freiemodule_9 .tease_top i
{
    color:#text_color#;
}
#main #content .artikel_details h2, #main #sidebar #mod_dritte_spalte .rightitem #medien-links a, #main #mod_freiemodule_9 .tease_top i>
{
    border-color:#text_color#;
}
#main #sidebar #likebox h2, header nav.top-bar .top-bar-section .mod_menue_ul li > ul
{
    border-color:#text_color#;
}
#main #content .last_artikel_liste:nth-of-type(1) .shariff .facebook a , footer #map_footer #mod_freiemodule_3 > div div:nth-of-type(3) i
{
    color:#background_color#;
    background:#text_color#;
}
.button, #main #mod_freiemodule_9 .tease_bottom .button:hover, #main #mod_freiemodule_9 .tease_bottom .button:focus
{
    color:#text_color#;/>
    background:#background_color#;
    border:1px solid #text_color#;>
}
.top-bar-section .dropdown li:not(.has-form):not(.active) > a:not(.button), header nav.top-bar .top-bar-section .mod_menue_ul > li > a:hover
{
    color:#text_color#!important;
}
body, .top-bar, .top-bar-section ul li, .top-bar-section li:not(.has-form) a:not(.button), .top-bar-section li:not(.has-form) a:hover:not(.button), .top-bar-section li:not(.has-form) a:visited:not(.button)>
{
    background:#background_color#!important;
    color:#text_color#!important;
}
#main #content .artikel_liste h2, header #kopf_wrapper , header nav.top-bar .top-bar-section .mod_menue_ul > li > a::after
{
    border-color:#text_color#;
}
#main #content #mod_breadcrump .breadlink , #main #content #mod_breadcrump .breadlink:nth-last-of-type(1), #main #content #mod_breadcrump i, header nav.top-bar .top-bar-section .mod_menue_ul >.has-dropdown > a::after
{
    color:#text_color#;
}
header nav.top-bar .top-bar-section .mod_menue_ul >.has-dropdown > a::before
{
    border-color:#text_color#;
}
#main #content .artikel_liste .shariff .facebook a
{
    background:#background_color#;
    color:#text_color#;
    border:1px solid #text_color#;
}
#main #mod_freiemodule_11 .tease_top i, #main #mod_freiemodule_11 .tease_top , #main #mod_freiemodule_11 .tease_bottom, #main #mod_freiemodule_11 img , #main #sidebar .kalender_modul h3
{
    color:#text_color#;
    border-color:#text_color#;
}
#main #mod_freiemodule_11 .tease_bottom .button:hover, #main #mod_freiemodule_11 .tease_bottom .button:focus
{
    background:#background_color#;
    color:#text_color#;
}
#main #sidebar .kalender_modul, #main #sidebar #mod_dritte_spalte .rightitem, #main #sidebar #likebox , header #mod_freiemodule_5 #kontrast_wechsel #contrast1 , header #mod_freiemodule_5 #kontrast_wechsel #contrast5
{
    background:#background_color#;
    border:1px solid #text_color#;
}
header #mod_freiemodule_5
{
    background:#background_color#;
    border-bottom:1px solid #text_color#;
}
#monats_id
{
    color:#text_color#;
    border-color:#text_color#;
    background-color:#background_color#;
}
.kalender_modul table , #main #mod_freiemodule_11 .tease_bottom .button:hover, #main #mod_freiemodule_11 .tease_bottom .button:focus
{
    background: #background_color#;
    color:#text_color#;
}
.kalender_modul table td.cal_set
{
    font-weight:bold;
}
table tr th, table tr td
{
    color:#text_color#;
}
table tr.even, table tr.alt, table tr:nth-of-type(2n)
{
    background:#313839;
}
.kalender_modul td, .kalender_modul tr
{
    border-color:#text_color#;
}
header #kopf_wrapper #mod_suchbox #search
{
    background:#background_color#;
    border:1px solid #text_color#;
    color:#text_color#;
}
header nav.top-bar .top-bar-section .mod_menue_ul li > ul li > a , #main #mod_freiemodule_9 .tease_top , #main #mod_freiemodule_9 .tease_bottom , #main #mod_freiemodule_9 img
{
    border-color:#text_color#;
}
#main .artikel_details h1, #main #content .artikel_details .last_artikel_liste .teaser_text h2, hr
{
    border-color:#text_color#;
}
#main #mod_freiemodule_8 img
{
    border:1px solid #text_color#;
}
#main .artikel_details .shariff .facebook a
{
    border:1px solid #text_color#;
    color:#text_color#;
    background:#background_color#;
}
.last_artikel_liste:hover .cm_teaser_img .fasearchback
{
  background-color: #text_color#80!important;
}
.orbit-container .orbit-prev > span
{
  border-right-color:#background_color#;
}
.orbit-container .orbit-next > span
{
  border-left-color:#background_color#;
}
.orbit-container .orbit-slide-number
{
  color:#background_color#;
}
.orbit-container .orbit-timer > span
{
  border-color:#background_color#;
}
.orbit-container .orbit-timer.paused > span
{
  border-left-color:#background_color#;
}
.orbit-container .orbit-timer .orbit-progress
{
  background-color:#background_color#4D!important
}
#buttonlist:last-child
{
  background-color: white;
}
#buttonlist > .bbeditor_element
{
  background-color:#background_color#!important
}'
