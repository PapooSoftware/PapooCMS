<?php
// Vor Aufruf müssen folgende Variablen gesetzt werden:
// $formid
/**
* Ein Formular raussuchen und ausgeben im Backend
// called by change_user.php, fp_content.php
*/
// Daten raussuchen
$this->get_form_group_list($formid);
// Aus den Daten Formular machen
$formdata = $this->result_groups;
require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_form.php');
#$this->make_form($this->result_groups);
// Sprachdaten rausholen
/*$sql = sprintf("SELECT * FROM %s
							WHERE mv_id_id = '%d' 
							AND mv_lang_id = '%d'",
							$this->cms->tbname['papoo_mv_lang'],
							$this->db->escape($this->checked->mv_id),
							$this->db->escape($this->cms->lang_back_content_id)
				);
$result = $this->db->get_results($sql, ARRAY_A);

if (!empty($result))
{
	foreach($result as $daten)
	{
		$result[0]['mv_toptext_html'] = str_ireplace("\.\./images", "./images", $daten['mv_toptext_html']);
		$result[0]['mv_bottomtext_html'] = str_ireplace("\.\./images", "./images", $daten['mv_bottomtext_html']);
	}
}
$this->content->template['pcfronttext'] = $result;*/
?>