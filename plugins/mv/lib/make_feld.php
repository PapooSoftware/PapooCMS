<?php
// Vor Aufruf m�ssen folgende Variablen gesetzt werden:
// $feld
/**
* Feld HTML erzeugen
*/
// Lookup Tabellen
if ($feld['mvcform_type'] == "select"
	|| $feld['mvcform_type'] == "multiselect"
	|| $feld['mvcform_type'] == "radio"
	|| $feld['mvcform_type'] == "check"
	|| $feld['mvcform_type'] == "pre_select") $feld = $this->get_lang_werte_lookup($feld, 0);
// Markierung f�r neue Feldtypen
switch($feld['mvcform_type'])
{
	case "text":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_feld.php');
		break;
	case "textarea":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_textarea_feld.php');
		break;
	case "textarea_tiny":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_textarea_tiny_feld.php');
		break;
	case "email":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_feld.php');
		break;
	case "radio":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_radio_feld.php');
		break;
	case "hidden":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_hidden_feld.php');
		break;
	case "select":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_select_feld.php');
		break;
	case "pre_select":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_pre_select_feld.php');
		break;
	case "check":
		// Sonderfall dzvhae Miltgliederanmeldung im FE f�r alle Metaebenen per Default aktivieren. S. auch 2 x in make_content_entry.php, prepare_content_entry.php
		// Im FE das Feld nicht anzeigen
		if ($feld['mvcform_name'] == "active_7"
			AND
				($this->checked->template == "mv/templates/mv_edit_front.html"
				OR $this->checked->template == "mv/templates/mv_edit_own_front.html"
				OR $this->checked->template == "mv/templates/mv_create_front.html")
			AND $this->dzvhae_system_id) break;
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_check_feld.php');
		break;
	case "password":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_password_feld.php');
		break;
	case "timestamp":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_timestamp_feld.php');
		break;
	case "multiselect":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_multiselect_feld.php');
		break;
	case "picture":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_picture_feld.php');
		break;
	case "file":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_file_feld.php');
		break;
	case "galerie":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_galerie_feld.php');
		break;
	case "artikel":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_artikel_feld.php');
		break;
	case "flex_verbindung":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_flex_verbindung_feld.php');
		break;
	case "flex_tree":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_flex_tree_feld.php');
		break;
	case "link":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_input_feld.php');
		break;
	case "zeitintervall":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_zeitintervall_feld.php');
		break;
	case "preisintervall":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_preisintervall_feld.php');
		break;
	case "sprechende_url":
		require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_sprechende_url_feld.php');
		break;
}
?>
