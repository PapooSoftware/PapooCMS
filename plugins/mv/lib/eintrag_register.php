<?php

/**
 * Klasse die die Registeransicht einer Flexverwaltung darstellt.
 */
#[AllowDynamicProperties]
class EintragRegister
{
	protected $mv_id = null;

	public function __construct(&$checked, &$cms, &$template)
	{
		$this->mv_id = intval($checked->mv_id);
		$template["letter_to_show"] = $letter_to_show = $checked->letter ?? 'A';
		$template["word_cutoff"] = $word_cutoff = intval($checked->word_cutoff ?? 3);

		\Flexeintrag::initstatic();

		$feldtyp = \Feldtyp::has_default_name($this->mv_id);

		if(!$feldtyp) {
			$template["mv_error"] = "Fehler, es ist bei keinem Feld der Verwaltung #{$this->mv_id} 'Baumansicht Name' ausgewählt.";
			return;
		}

		$template["register_words"] = $this->get_words($feldtyp, $word_cutoff, $cms->lang_id, $letter_to_show);
		$word_to_show = $template["word_to_show"] = $checked->word_to_show ?? $template["register_words"][0]->word;

		$column = $feldtyp->get_column_name();
		// $word_to_show = PDO::quote($word_to_show);
		$template["register_contents"] = \Flexeintrag::all_by_sql($this->mv_id, $cms->lang_id, "WHERE `$column` LIKE '{$word_to_show}%' AND mv_content_sperre <> 1 ORDER BY `$column` ASC");
	}

	protected function get_words($feldtyp, $cutoff, $lang_id, $letter)
	{                                        
		$column = $feldtyp->get_column_name();

		if($letter == '0-9') {
			return \Flexeintrag::all_by_sql($this->mv_id, $lang_id,
				"WHERE $column REGEXP '^[0-9]' AND mv_content_sperre <> 1 GROUP BY SUBSTRING($column, 1, $cutoff);",
				"SUBSTRING($column, 1, $cutoff) as word, COUNT(mv_content_id) as count"
			);
		}
		else if($letter == '') {
			return \Flexeintrag::all_by_sql($this->mv_id, $lang_id,
				"WHERE $column REGEXP '^[^a-zA-Z0-9]' AND mv_content_sperre <> 1 GROUP BY SUBSTRING($column, 1, $cutoff);",
				"SUBSTRING($column, 1, $cutoff) as word, COUNT(mv_content_id) as count"
			);
		}

		return \Flexeintrag::all_by_sql($this->mv_id, $lang_id,
			"WHERE $column LIKE '$letter%' AND mv_content_sperre <> 1 GROUP BY SUBSTRING($column, 1, $cutoff);",
			"SUBSTRING($column, 1, $cutoff) as word, COUNT(mv_content_id) as count"
		);
	}
}
