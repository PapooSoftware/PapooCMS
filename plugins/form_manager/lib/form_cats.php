<?php
/**
 * form_manager Update Klasse
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2015
 * @version $ID$
 * @access public
 *
 * Checkt ab 2015 das Update
 */
class form_cats {
	/**
	 * form_cats constructor.
	 */
	public function __construct() {

		global $db, $checked, $diverse, $content, $user, $cms, $weiter, $db_abs;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->content = &$content;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		//Check Tabellen
		$this->check_cats();
	}

	public function check_cats()
	{
		$this->content->template['self_form_cat']="plugin.php?menuid=".$this->checked->menuid."&template=form_manager/templates/manage_form_cat.html";

		IfNotSetNull($this->checked->del_form_cat_id);

		//Nicht genutzten Eintrag löschen
		if (is_numeric($this->checked->del_form_cat_id)) {
			//Nur löschen wenn keine Formulare zugeordnet
			if ($this->get_anz_forms($this->checked->del_form_cat_id) < 1) {
				$this->delete_cat($this->checked->del_form_cat_id);
			}
		}

		if (!empty($this->checked->form_cat_id)) {
			$this->create_form_cat();
		}
		else {
			$this->get_all_form_cats();
		}
	}

	/**
	 * @param int $id
	 */
	private function delete_cat($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_plugin_cform_cats";
		$xsql['limit']          = " LIMIT 1";
		$xsql['del_where_wert'] = " formcat__id='".$this->db->escape($id)."' ";
		$this->db_abs->delete( $xsql );
	}


	private function get_all_form_cats()
	{
		$this->db->hide_errors();

		$xsql=array();
		$xsql['dbname']         = "papoo_plugin_cform_cats";
		$xsql['select_felder']  = array("*");
		$xsql['where_data_like']= " LIKE ";
		$xsql['where_data']     = array("formcat__id" => "%%");

		$result = $this->db_abs->select( $xsql );

		if (is_array($result)) {
			foreach ($result as $k=>$v) {
				$result[$k]['anzahl'] = $this->get_anz_forms($v['formcat__id']);
			}
		}

		$this->content->template['formcat_liste']=$result;

		$this->db->show_errors();
	}

	/**
	 * @param int $id
	 * @return int
	 */
	private function get_anz_forms($id=0)
	{
		$xsql=array();
		$xsql['dbname']         = "papoo_form_manager";
		$xsql['select_felder']  = array("*");
		$xsql['where_data']     = array("form_manager_kategorie" => $id);


		$result = $this->db_abs->select( $xsql );

		return (count($result));
	}

	private function get_form_cats()
	{
		if (is_numeric($this->checked->form_cat_id)) {
			$xsql=array();
			$xsql['dbname']         = "papoo_plugin_cform_cats";
			$xsql['select_felder']  = array("*");
			$xsql['limit']          = " LIMIT 1";
			$xsql['where_data']     = array("formcat__id" => $this->checked->form_cat_id);
			$result = $this->db_abs->select( $xsql );

			$this->content->template['formcat_']=$result;
		}
	}

	/**
	 * @return bool|void
	 */
	private function create_form_cat()
	{
		/**
		//Verfügbare Sprachen rausholen
		$xsql=array();
		$xsql['dbname']         = "papoo_name_language";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("more_lang" => 2);
		$result = $this->db_abs->select( $xsql );
		$this->content->template['form_lang_vorhanden']=$result;
		 */
		$this->get_form_cats();

		$this->content->template['is_form_cat']="ok";

		//Nicht abgeschickt, dann nix machen
		if (empty($this->checked->submit_form_cats)) {
			return false;
		}
		#$data=$this->checked;

		//Sprache erstmal auf 1 = Deutsch setzen
		$this->checked->formcat__cat_lang_id=1;

		if (!is_numeric($this->checked->form_cat_id)) {
			$xsql=array();
			$xsql['dbname']     = "papoo_plugin_cform_cats";
			$xsql['praefix']    = "formcat_";
			$xsql['must']       = array( "formcat__name_der_kategorie" );

			$cat_dat = $this->db_abs->insert( $xsql );
			$insertid=$cat_dat['insert_id'];
			#$this->checked->formcat__cat_lang_id				= $data->formcat__cat_lang_id[$v['lang_id']];
			#$this->checked->formcat__name_der_kategorie			= $data->formcat__name_der_kategorie[$v['lang_id']];
			#$this->checked->formcat__beschreibung_der_kategorie	= $data->formcat__beschreibung_der_kategorie[$v['lang_id']];

			if (is_numeric($insertid)) {
				$this->content->template['is_eingetragen']="ok";
			}
		}
		else {
			$xsql=array();
			$xsql['dbname']         = "papoo_plugin_cform_cats";
			$xsql['praefix']        = "formcat_";
			$xsql['must']           = array( "formcat__name_der_kategorie" );
			$xsql['where_name']     = "formcat__id";

			$cat_dat = $this->db_abs->update( $xsql );

			$insertid=$cat_dat['insert_id'];
			if (is_numeric($insertid)) {
				$this->content->template['is_eingetragen']="ok";
			}
		}
	}
}
