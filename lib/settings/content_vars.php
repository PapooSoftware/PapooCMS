<?php
//INI
IfNotSetNull($content->template['refresh']);
IfNotSetNull($content->template['plugin_header']);
IfNotSetNull($content->template['hrefruck']);
IfNotSetNull($content->template['hrefweiter']);
IfNotSetNull($content->template['searchneu']);
IfNotSetNull($content->template['menu_dat_show_search_aktuell']);
IfNotSetNull($content->template['fb_like_me']);
IfNotSetNull($content->template['linktext']);
IfNotSetNull($content->template['dok_show_teaser_link']);
IfNotSetNull($content->template['author']);
IfNotSetNull($content->template['editlink']);
IfNotSetNull($content->template['easyeditok']);
IfNotSetNull($content->template['nl']);
IfNotSetNull($content->template['site_title_shop']);
IfNotSetNull($content->template['description_shop']);
IfNotSetNull($content->template['keywords_shop']);
IfNotSetNull($content->template['is_search']);
IfNotSetNull($content->template['is_shopping_cart']);
IfNotSetNull($content->template['is_shop']);
IfNotSetNull($content->template['weiter']); // Notice in mod_weiter.html
IfNotSetNull($content->template['fehler']); // Notice in guestbook.html
IfNotSetNull($content->template['__spamschutz__']);
IfNotSetNull($content->template['inhalt']);
IfNotSetNull($content->template['is_extended_search']);
IfNotSetNull($content->template['gesucht']);

if(!isset($content->template['module_aktiv']['mod_loginform'])) {
	$content->template['module_aktiv']['mod_loginform'] = NULL;
}
if(!isset($content->template['plugin']['mehrstufige_freigabe'])) {
	$content->template['plugin']['mehrstufige_freigabe'] = NULL;
}
if(!isset($cms->tbname['papoo_smarty_user_plugins'])) {
	$cms->tbname['papoo_smarty_user_plugins'] = NULL;
}

//Canonicals Handling
if (strstr($_SERVER['REQUEST_URI'],'plugin.php')) {
	$content->template['canonical']= $_SERVER['REQUEST_URI'];
}
$content->template['aktiv_canonical']=false;
$content->template['referrer'] = $_SERVER['HTTP_REFERER'];


// Spezielle Zustände an Template_Variable übergeben
if (strstr($content->template['urldatprint'], "search")) {
	$content->template['is_search']='is-search';
}

if (strstr($content->template['urldatprint'], "warenkorb_aktu")) {
	$content->template['is_shopping_cart'].='is-shopping-cart';
}

if (defined("admin") == false) {
	loadStyleSpecificFrontendMessages();
}
