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
#[AllowDynamicProperties]
class form_get_form_list {
	/**
	 * form_get_form_list constructor.
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

		$this->show_all=0;
	}

	/**
	 * @param string $extern
	 * @return array|bool|void
	 */
	public function get_form_liste($extern="")
	{
		if ($this->checked->template!="form_manager/templates/change_email.html") {
			$extern="ok";
		}

		//return true;
		if (empty($extern)) {
			//Sortierung
			if (!empty($this->checked->order_by)) {
				if ($this->checked->order_by == "bezeichnung") {
					$order_by = " ORDER BY form_manager_name";
				}

				if ($this->checked->order_by == "id") {
					$order_by = " ORDER BY form_manager_id";
				}

				if ($this->checked->order_by == "plugin_form_manager_erstellt_am") {
					$order_by = " ORDER BY form_manager_erstellt";
				}

				if ($this->checked->order_by == "plugin_form_manager_letzte_nderung") {
					$order_by = " ORDER BY form_manager_geaendert";
				}
			}
			else {
				$order_by = " ORDER BY form_manager_name";
			}

			//Richung ini wenn nix gesetzt ist
			$this->content->template['sort_order'] = "desc";

			//Richtung auswerten
			if (!empty($this->checked->sort_order)) {
				if ($this->checked->sort_order == "asc") {
					$sort_by = "ASC";

					//Fürs Template dann umdrehen
					$this->content->template['sort_order'] = "desc";
				}
				if ($this->checked->sort_order == "desc") {
					$sort_by = "DESC";

					//Fürs Template dann umdrehen
					$this->content->template['sort_order'] = "asc";
				}
			}
		}

		IfNotSetNull($order_by);
		IfNotSetNull($sort_by);
		if ((empty($this->checked->form_search_now) || (empty($this->checked->form_manager_kategorie_form) && empty($this->checked->form_manager_name_form) )) && $this->show_all==1 ) {
			$sql = sprintf("SELECT * FROM %s
								 %s %s ",
				$this->cms->tbname['papoo_form_manager'],
				$order_by,
				$sort_by
			);
		}
		else {
			$search_kat = "";

			if (isset($this->checked->form_manager_kategorie_form_message) && is_numeric($this->checked->form_manager_kategorie_form_message) &&
				$this->show_all!=1
			) {
				$this->checked->form_manager_kategorie_form=$this->checked->form_manager_kategorie_form_message;
			}

			//Nur setzen wenn auch eine Kat gewählt - sonst wird nix gefunden...
			if (isset($this->checked->form_manager_kategorie_form) && $this->checked->form_manager_kategorie_form >= 1) {
				$search_kat = "form_manager_kategorie = '".$this->db->escape($this->checked->form_manager_kategorie_form)."' AND ";
			}

			IfNotSetNull($this->checked->form_manager_name_form);

			$sql = sprintf("SELECT * FROM %s
								WHERE
								%s
								form_manager_name LIKE '%s'
								 %s %s 
								",
				$this->cms->tbname['papoo_form_manager'],
				$search_kat,
				"%".$this->db->escape($this->checked->form_manager_name_form)."%",
				$order_by,
				$sort_by
			);
		}

		$result = $this->db->get_results($sql, ARRAY_A);

		//Übersicht setzen
		$this->content->template['uebersicht']="OK";
		$this->content->template['form_manager_kategorie_form'] =
			isset($this->checked->form_manager_kategorie_form ) ? $this->checked->form_manager_kategorie_form : NULL;
		$this->content->template['form_manager_name_form'] =
			isset($this->checked->form_manager_name_form) ? $this->checked->form_manager_name_form : NULL;
		$this->content->template['form_search_now'] =
			isset($this->checked->form_search_now) ? $this->checked->form_search_now : NULL;

		//Noch weitere Daten ergänzen
		if (!is_array($result)) {
			return false;
		}

		foreach ($result as $k=>$v) {
			//Anzahl der Felder
			$result[$k]['anzahl_felder']=$this->get_anzahl_felder($v['form_manager_id']);

			//Anzahl der Anfragen
			$result[$k]['anzahl_anfragen']=$this->get_anzahl_anfragen($v['form_manager_id']);

			//Letzte Anfrage
			$result[$k]['letzte_anfrage']=$this->get_last_anfrage($v['form_manager_id']);

			//ID der letzten Anfrage
			$result[$k]['letzte_anfrage_id']=$this->max_lead_id;

			//Erstellt
			if ($result[$k]['form_manager_erstellt']>1) {
				$result[$k]['form_manager_erstellt'] = date("d.m.Y - H:i",$result[$k]['form_manager_erstellt']);
			}
			else {
				$result[$k]['form_manager_erstellt'] = "N.A.";
			}

			//Geändert
			if ($result[$k]['form_manager_geaendert']>1) {
				$result[$k]['form_manager_geaendert'] = date("d.m.Y - H:i",$result[$k]['form_manager_geaendert']);
			}
			else {
				$result[$k]['form_manager_geaendert'] = "N.A.";
			}
		}
		return $result;
	}

	/**
	 * @param int $form_id
	 * @return array|null
	 */
	public function get_anzahl_felder($form_id=0)
	{
		$sql = sprintf("SELECT COUNT(plugin_cform_id) FROM %s
							WHERE plugin_cform_form_id='%d'
							",
			$this->cms->tbname['papoo_plugin_cform'],
			$form_id);
		$result = $this->db->get_var($sql);

		return $result;
	}

	/**
	 * @param int $form_id
	 * @return array|null
	 */
	public function get_anzahl_anfragen($form_id=0)
	{
		$sql = sprintf("SELECT COUNT(form_manager_lead_id) FROM %s
							WHERE form_manager_form_id='%d'
							",
			$this->cms->tbname['papoo_form_manager_leads'],
			$form_id);
		$result = $this->db->get_var($sql);

		return $result;
	}

	/**
	 * @param int $form_id
	 * @return array|false|string|void
	 */
	public function get_last_anfrage($form_id=0)
	{
		$sql = sprintf("SELECT MAX(form_manager_form_datum) AS max_dat, MAX(form_manager_lead_id) AS max_lead_id FROM %s
							WHERE form_manager_form_id='%d'
							",
			$this->cms->tbname['papoo_form_manager_leads'],
			$form_id);
		$result = $this->db->get_results($sql,ARRAY_A);

		$this->max_lead_id=$result['0']['max_lead_id'];
		$result = date("d.m.Y - H:i",$result['0']['max_dat']);

		return $result;
	}
}
