<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $insertid 
/**
* Menu f�r die Verwaltung erweitern
**/
$menu_mv_sub = array(
	0 => array(
		"de" => "Meta Einstellungen",
		"en" => "Metalayer",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/create_meta.html"
		),
	1 => array(
		"de" => "Felder bearbeiten",
		"en" => "Manage Fields",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/create_input.html"
		),
	2 => array(
		"de" => "Mitglieder eintragen",
		"en" => "Add Member",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/fp_content.html"
		),
	3 => array(
		"de" => "Mitgliederliste",
		"en" => "Memberlist",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/userlist.html"
		),
	4 => array(
		"de" => "Mitgliedersuche",
		"en" => "Search Member",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/search_user.html"
		),
	5 => array(
		"de" => "Ausgabe",
		"en" => "Output",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/edit_templates.html"
		),
	6 => array(
		"de" => "Rechtevergabe",
		"en" => "Adminrights",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/edit_rights.html"
		),
	7 => array(
		"de" => "Linkliste",
		"en" => "Links",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/frontend_links.html"
		),
	8 => array(
		"de" => "Sortierung",
		"en" => "Sort",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/sortierung.html"
		)
	);
$menu_st_sub = array(
	0 => array(
		"de" => "Meta Einstellungen",
		"en" => "Metalayer ",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/create_meta.html"
		),
	1 => array(
		"de" => "Felder bearbeiten",
		"en" => "Manage Fields",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/create_input.html"
		),
	2 => array(
		"de" => "Objekt eintragen",
		"en" => "Add Object",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/fp_content.html"
		),
	3 => array(
		"de" => "&Uuml;bersichtsliste",
		"en" => "Overview list",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/userlist.html"
		),
	4 => array(
		"de" => "Objekt Suche",
		"en" => "Search Object",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/search_user.html"
		),
	5 => array(
		"de" => "Ausgabe",
		"en" => "Output",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/edit_templates.html"
		),
	6 => array(
		"de" => "Rechtevergabe",
		"en" => "Adminrights",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/edit_rights.html"
		),
	7 => array(
		"de" => "Linkliste",
		"en" => "Links",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/frontend_links.html"
		),
	8 => array(
		"de" => "Sortierung",
		"en" => "Sort",
		"icon" => "mv/bilder/pic_01.gif",
		"link" => "plugin:mv/templates/sortierung.html"
		)
	);
// ACHTUNG Sprache noch nicht eingebaut
$menuname = $this->checked->mv_name;
$menu_array = array(
	"attribute" => array(),
	"cdata" => "",
	"eintrag_de" => array(0 => array(
		"attribute" => array(),
		"cdata" => $menuname
		)),
	"eintrag_en" => array(0 => array(
		"attribute" => array(),
		"cdata" => $menuname
		)),
	"icon" => array(0 => array(
		"attribute" => array(),
		"cdata" => "mv/bilder/pic_01.gif"
		)),
	"link" => array(0 => array(
		"attribute" => array(),
		"cdata" => "plugin:mv/templates/mv_einleitung.html&mv_id=" . $insertid
		))
	);
$sql = sprintf("SELECT * FROM %s 
							WHERE plugin_papoo_id = '35'",
							$this->cms->tbname['papoo_plugins']
				);
$result = $this->db->get_results($sql);
$plugin_id = $result[0]->plugin_id;
$menu_main_id = substr($result[0]->plugin_menuids, 1, 5); // ACHTUNG klappt nat�rlich nur, wenn es dabei bleibt, dass plugin menuids zw. 1000 und 9999 sind
$menu_id = $this->plugin->make_menue($menu_array, $plugin_id, $menu_main_id, 1);

// F�r alle Sprachen das Menu $menuname in die Language Tabelle eintragen
$sql = sprintf("SELECT * FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
				);
$name_language = $this->db->get_results($sql);

if (!empty($name_language))
{
	foreach($name_language as $language)
	{
		$sql = sprintf("INSERT INTO %s 
									SET	lang_id = '%d', 
									menuid_id = '%d', 
									menuname = '%s', 
									back_front = '1'",
									$this->cms->tbname['papoo_men_uint_language'],
									$this->db->escape($language->mv_lang_id),
									$this->db->escape($menu_id),
									$this->db->escape($menuname)
						);
		$this->db->query($sql);
	}
}
// Menu ID in der mv Tabelle speichern
$sql = sprintf("UPDATE %s 
						SET mv_menu_id = '%d' 
						WHERE mv_id = '%d'",
						$this->cms->tbname['papoo_mv'],
						$this->db->escape($menu_id),
						$this->db->escape($insertid)
				);
$this->db->query($sql);

// welche Art von Verwaltung soll installiert werden?
if ($this->checked->mv_art == 2) $menu_foreach = $menu_mv_sub; // Mitgliederverwaltung
else $menu_foreach = $menu_st_sub; // Standard- und Kalenderverwaltung

foreach($menu_foreach as $menu)
{
	$menu_array = array(
		"attribute" => array(),
		"cdata" => "",
		"eintrag_de" => array(0 => array(
			"attribute" => array(),
			"cdata" => $menu['de']
			)),
		"eintrag_en" => array(0 => array(
			"attribute" => array(),
			"cdata" => $menu['en']
			)),
		"icon" => array(0 => array(
			"attribute" => array(),
			"cdata" => $menu['icon']
			)),
		"link" => array(0 => array(
			"attribute" => array(),
			"cdata" => $menu['link'] . "&mv_id=" . $insertid
			))
		);

	$menu_sub_id = $this->plugin->make_menue($menu_array, $plugin_id, $menu_id, 2);
	if (!empty($name_language))
	{
		foreach($name_language as $language)
		{
			// deutsche Sprache
			if ($language->mv_lang_short == "de") $menu_substi = $menu['de'];
			// alle anderen bekommen Englisches Menu
			else $menu_substi = $menu['en'];
			$sql = sprintf("INSERT INTO %s 
										SET lang_id = '%d', 
										menuid_id = '%d', 
										menuname = '%s',
										back_front = '1'",
										$this->cms->tbname['papoo_men_uint_language'],
										$this->db->escape($language->mv_lang_id),
										$this->db->escape($menu_sub_id),
										$this->db->escape($menu_substi), '1'
							);
			$this->db->query($sql);
		}
	}
}
$this->menuintcss->make_menuintcss();
$this->menuintcss->make_menuintcss_file();
?>