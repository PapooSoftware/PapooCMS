<?php

/**
 * Class faqsearcher
 */
#[AllowDynamicProperties]
class faqsearcher{
	// Suchwert der zurückgegeben wird
	var $result_search;
	// Anzahl der Suchergebnisse
	var $result_anzahl;
	var $cat_data;

	/**
	 * faqsearcher constructor.
	 */
	function __construct()
	{
		// Klassen globalisieren
		global $cms, $db, $message, $weiter, $content, $checked;
		// Die Klassen als Referenzen
		$this->cms = & $cms;
		$this->db = & $db;
		$this->message = & $message;
		$this->content = & $content;
		$this->weiter = & $weiter;
		$this->checked = & $checked;
	}

	/**
	 * FAQs durchsuchen
	 *
	 * @param $searchfor
	 * @return mixed
	 */
	function do_search( $searchfor )
	{
		// Keine Eingabe?
		if ($searchfor == '') {
			return array();
		}
		// Eingabe checken und bereinigen
		$searchfor = trim( $searchfor );
		$searchfor = $this->db->escape($this->clean($searchfor));
		$this->content->template['search_faq'] = $searchfor;
		$where1_faq = array();

		// WHERE-Clause erstellen
		$worte = explode( ' ', $searchfor );
		foreach ($worte as $wort){
			$lower_faq = array();
			$lower_faq[] = "LOWER(question) LIKE '%$wort%'";
			$lower_faq[] = "LOWER(answer) LIKE '%$wort%'";
			$where1_faq[] = implode( ' OR ', $lower_faq );
		}
		$where_faq = '(' . implode(') OR (', $where1_faq ) . ')';
		$i = 0;
		$matches = 0;
		foreach ($this->content->template['cat_data'] AS $key =>$value) {
			// In der Admin alle, im Frontend nur die aktiven FAQs
			$active = (defined("admin")) ? '' : " AND T2.active = 'j'";
			// Daten holen
			$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS
							T1.cat_id,
							T1.faq_id,
							T2.question,
							T2.answer,
							T2.created,
							T2.createdby,
							T2.changedd,
							T2.changedby,
							MAX(T1.version_id) AS version_id
							FROM %s T1
							INNER JOIN %s T2 ON (T1.faq_id = T2.id AND T1.version_id = T2.version_id)
							WHERE ( %s ) AND T1.cat_id = '%d' $active
							GROUP BY T1.faq_id
							ORDER BY T1.order_id ",
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->cms->tbname['papoo_faq_content'],
				$where_faq,
				$this->content->template['cat_data'][$key]['id']
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			$result_faq_search[$i] = $result;
			$i++;
			// Zählen der matches
			$sql = sprintf("SELECT FOUND_ROWS()");
			//$this->result_anzahl = $this->result_anzahl + $this->db->get_var($sql);
			$matches = $matches + $this->db->get_var($sql);
		}
		$_SESSION['faq']['search_matches'] = $matches;
		IfNotSetNull($result_faq_search);
		return $result_faq_search;
	}

	/**
	 * Eingaben bereinigen
	 *
	 * @param $search
	 * @return mixed
	 */
	function clean($search) {
		// $search auf unerlaubte Zeichen überprüfen und evtl. bereinigen
		$search = trim($search);
		$remove = "<>'\"_%*\\";
		for ($i = 0; $i < strlen($remove); $i ++)
			$search = str_replace(substr($remove, $i, 1), "", $search);
		return $search;
	}
}

$faqsearcher = new faqsearcher();
