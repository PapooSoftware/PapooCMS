<?php
/**
* Artikel
 */
$articles = Article::all();

$current_language_id = $feld['mvcform_lang_lang'];
$saved_value = $this->checked->{$feld['mvcform_name']};

$options_html = "<option value=\"\"></option>".implode('',array_map(function($article) use($current_language_id, $saved_value) {
	$is_selected_html = ($saved_value == $article->give_url_header($current_language_id)) ? "selected" : "";
	return "<option $is_selected_html value=\"".$article->give_url_header($current_language_id)."\">".$article->give_header($current_language_id)."</option>";
}, $articles));

$stern_wenn_sein_muss = $feld['mvcform_must'] ? " * " : "";
$cfeld =<<<EOF
<label for="{$feld['mvcform_name']}">{$feld['mvcform_label']} $stern_wenn_sein_muss</label>
<select name="{$feld['mvcform_name']}">
	{$options_html}
</select>
EOF;
$cfeld .= $this->make_feld_tooltip($feld['mvcform_lang_tooltip']);
$cfeld .= '<br />';

// Daten in das Array schreiben, welches das Formular representiert. Wichtig: Die Variable muss auch cfeld heissen!
$this->feldarray[] = $cfeld;
