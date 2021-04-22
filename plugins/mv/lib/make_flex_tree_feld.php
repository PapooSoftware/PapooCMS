<?php
/**
* Flex Verbindung
 */
$flexverwaltungen = Flexverwaltung::all();

$current_language_id = $feld['mvcform_lang_lang'];
$saved_value = $this->checked->{$feld['mvcform_name']};

if(!empty($saved_value)) {
	$saved_value = explode(';', $saved_value);
}
else {
	$saved_value = [];
}

// echo "<pre>";
// var_dump($feld['mvcform_name']);
// echo "</pre>";
// exit;

$choose_flexeintrag_html = implode('', array_map(function($flexverwaltung) use($current_language_id, $feld, $saved_value, $meta_id) {

	// Ausgabe-Template zum anzeigen in dieser Liste aus der Datenbank holen
	$mv_template_vorlage = \Flextemplate::find_by_mv_id($flexverwaltung->id);

	$flexeintrage = $flexverwaltung->entries($current_language_id);

	$current_mv_id = $flexverwaltung->id;

	$disabled_html = (is_array($saved_value) && count($saved_value) >= 2 && $current_mv_id == $saved_value[0]) ? "" : "disabled style=\"display: none;\"";

	$all_entries_of_one_mv_options_html = implode('', array_map(function($flexeintrag) use($flexverwaltung, $feld, $saved_value, $mv_template_vorlage) {
		$is_selected = ($flexverwaltung->id == $saved_value[0] && $flexeintrag->id == $saved_value[1]);
		return "<span class=\"mv_flexverbindung_item mv_flex_{$flexverwaltung->id} mv_flex_item_{$flexeintrag->id}\">".$flexeintrag->replace_fields_in_template_vorlage($mv_template_vorlage->template_content_flex_link_selection, $flexverwaltung->id, $feld['mvcform_name'], $is_selected)."</span>";
	}, $flexeintrage));

	$html =<<<EOF
	<select class="mv-select-flex-entry" data-of-form-id="{$feld['mvcform_id']}" data-of-mv="$current_mv_id" $disabled_html name="{$feld['mvcform_name']}">
		$all_entries_of_one_mv_options_html
	</select>
EOF;

	return $html;

}, $flexverwaltungen));

$options_html = implode('',array_map(function($flexverwaltung) use($current_language_id, $saved_value) {
	$is_selected_html = ($saved_value[0] == $flexverwaltung->mv_id) ? "selected" : "";
	return "<option $is_selected_html value=\"".$flexverwaltung->mv_id."\">".$flexverwaltung->mv_name."</option>";
}, $flexverwaltungen));

// $options_select_feld_html = implode('',array_map(function($feld) use($flexverwaltungen, $current_language_id, $saved_value) {
// 	$is_selected_html = ($saved_value[2] == $feld->id) ? "selected" : "";
// 	$flexverwaltung = array_reduce($flexverwaltungen, function($c, $i) use($feld) {
// 		return $feld->mvcform_form_id == $i->id ? $i : $c;
// 	}, null);
// 	$flexname = $flexverwaltung ? $flexverwaltung->mv_name : "";
// 	return "<option $is_selected_html value=\"".$feld->id."\">{$flexname} - {$feld->mvcform_name}</option>";
// }, Feldtyp::all()));

$stern_wenn_sein_muss = $feld['mvcform_must'] ? " * " : "";
$cfeld =<<<EOF
<div class="flex-verbindung" style="float: left;">
	<label for="{$feld['mvcform_name']}">{$feld['mvcform_label']} $stern_wenn_sein_muss</label>

	<div class="flexlink flex-auswahl-feld">
		<label for="{$feld['mvcform_name']}">Filtern nach einer bestimmten Flexverwaltung dessen Eintrag Verknüpft werden soll (Verwaltung muss Ausgabe-"Template für die Übersicht bei der Flexverwaltungs-Verknüpfung" definiert haben, damit es hier angezeigt werden kann):</label>
		<select id="{$feld['mvcform_name']}" onchange="flexselection_to(this);" data-of-form-id="{$feld['mvcform_id']}">
			<option value="0;0"></option>
			{$options_html}
		</select>
	</div>
	<div class="flexlink eintrag-auswahl-radio" style="float: left; margin: 1rem 1rem 1rem 1rem;">
		{$choose_flexeintrag_html}
	</div>
</div>
EOF;
$cfeld .= '<script src="'.PAPOO_WEB_PFAD.'/plugins/mv/js/flexlinkselection.js"></script>';
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';

// Daten in das Array schreiben, welches das Formular representiert. Wichtig: Die Variable muss auch cfeld heissen!
$this->feldarray[] = $cfeld;
