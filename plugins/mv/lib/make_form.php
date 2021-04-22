<?php
// Vor Aufruf müssen folgende Variablen gesetzt werden:
// $formdata
/**
* echtes HTML Form erstellen (BE)
*/
// Flag setzen, damit die make_feld Funktionen auch wissen, dass sie das Pflichtfeld-Häkchen vom Frontend checken sollen
// called by back_get_form.php
$this->mv_back_or_front = "back";
$groupar = $this->result_groups;
$idx1 = 0;
// Für diese Gruppen durchgehen
if ((!empty($this->result_groups))) // set by get_form_group_list() mv.php
{
	foreach($this->result_groups as $group)
	{
		$this->feldarray = array();
		// Alle Felder der jeweiligen Gruppe rausholen
		$sql = sprintf("SELECT DISTINCT *
							FROM %s, %s
							WHERE mvcform_group_id = '%d'
							AND mvcform_aktiv = 1
							AND mvcform_id = mvcform_lang_id
							AND mvcform_lang_lang = '%d' 
							AND mvcform_meta_id = mvcform_lang_meta_id
							AND mvcform_meta_id = '%d'
							GROUP BY mvcform_id
							ORDER BY mvcform_order_id DESC",
							$this->cms->tbname['papoo_mvcform_lang'],
							$this->cms->tbname['papoo_mvcform'],
							$this->db->escape($group['mvcform_group_id']),
							$this->db->escape($this->cms->lang_back_content_id),
							$this->db->escape($this->meta_gruppe)
					);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!empty($result))
		{
			foreach($result as $fld)
			{
				// Bösen HTML rausflitschen
				$this->checked->$fld = $this->html2txt($this->checked->$fld);
				// Anführeungszeichen kodieren
				$this->checked->$fld = $this->diverse->encode_quote($this->checked->$fld);
				// weil Felder gleiche Namen haben können, den Namen mit der ID versehen
				$fld['mvcform_name'] .= "_" . $fld['mvcform_id'];
				$feld = $fld;
				require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_feld.php');
			}
		}
		//Wenn leer, Eintrag mit fieldset entfernen
		if (empty($this->feldarray)) $groupar[$idx1] = "";
		//Normal dann füllen
		else $groupar[$idx1]['felder'] = $this->feldarray;
		$idx1++;
	}
	//Nochmal durchgehen und die leeren Einträge rausflitschen
	foreach($groupar as $dat) { if (!empty($dat)) $grup_ar[] = $dat; }
}
$this->tiny_elements = substr($this->tiny_elements, 0, -1);
$this->content->template['show_tiny'] = $this->show_tiny;
$this->content->template['tiny_elements'] = $this->tiny_elements;
$this->content->template['gfliste'] = $grup_ar;
?>
