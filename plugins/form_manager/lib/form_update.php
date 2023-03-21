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
class form_update {
	/**
	 * form_update constructor.
	 */
	public function __construct() {

		global $db, $checked, $diverse, $user, $cms, $weiter, $db_abs;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->diverse = &$diverse;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->weiter = &$weiter;
		$this->db_abs = &$db_abs;

		//Check Tabellen
		$this->check_tables();

		//Check Felder
		self::check_felder();

		//Check Menüpunkte
		self::check_menu();

		// Check auf SMTP-Update
		self::updateDatabaseSMTP();
	}

	public function check_tables()
	{
		$this->db_abs->show_version();
		if ($this->db_abs->version <1) {
			die("/lib/classes/db_abstract_class.php aktualisieren auf Version 2!");
		}

		$isUpdated = isset($this->cms->tbname['papoo_plugin_cform_cats']);

		if ($isUpdated == false) {
			$sql='CREATE TABLE `'.DB_PRAEFIX.'papoo_plugin_cform_cats` (
				  `formcat__id` int(11) NOT NULL AUTO_INCREMENT,
				  `formcat__cat_lang_id` int(11) NOT NULL,
				  `formcat__name_der_kategorie` text,
				  `formcat__beschreibung_der_kategorie` text,
				  PRIMARY KEY (`formcat__id`)
				) ENGINE=MyISAM AUTO_INCREMENT=8';
			$tableCreated = (bool)$this->db->query($sql);

			if ($tableCreated == false) {
				throw new Exception('Formular-Manager Updateroutine: Tabelle papoo_plugin_cform_cats konnte nicht angelegt werden.');
			}
			else {
				$this->cms->tbname['papoo_plugin_cform_cats'] = DB_PRAEFIX.'papoo_plugin_cform_cats';
			}

			$sql="INSERT INTO `{$this->cms->tbname['papoo_plugin_cform_cats']}` SET 
						`formcat__id`=1, 
						`formcat__cat_lang_id`=1, 
						`formcat__name_der_kategorie`='Standard', 
						`formcat__beschreibung_der_kategorie`='Dies ist die Standard Kategorie.'";
			$this->db->query($sql);

			//Kategorie
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager']}` ADD `form_manager_kategorie` int(11) NOT NULL";
			$this->db->query($sql);
			//Erstellt am
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager']}` ADD `form_manager_erstellt` int(11) NOT NULL";
			$this->db->query($sql);
			//Geändert am
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager']}` ADD `form_manager_geaendert` int(11) NOT NULL";
			$this->db->query($sql);

			//Indexe ergänzen
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager_lead_content']}` ADD PRIMARY KEY (`form_manager_content_lead_id`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager_lead_content']}` ADD INDEX idx_pfmlc_form_manager_content_lead_id_id (`form_manager_content_lead_id_id`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager_lead_content']}` ADD INDEX idx_pfmlc_form_manager_content_lead_feld_name (`form_manager_content_lead_feld_name`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager_lead_content']}` ADD FULLTEXT idx_pfmlc_form_manager_content_lead_feld_content (`form_manager_content_lead_feld_content`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_form_manager_leads']}` ADD INDEX idx_pfml_form_manager_form_id (`form_manager_form_id`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_plugin_cform_lang']}` ADD PRIMARY KEY (`plugin_cform_lang_id`, `plugin_cform_lang_lang`)";
			$this->db->query($sql);
			$sql="ALTER TABLE `{$this->cms->tbname['papoo_plugin_cform']}` ADD INDEX idx_ppc_plugin_cform_name (`plugin_cform_name`)";
			$this->db->query($sql);
		}
	}

	public function check_felder()
	{

	}

	/**
	 * Menüpunkt Kategorie erstellen und Menüpunkt Felder managen löschen
	 *
	 * @return bool
	 */
	public function check_menu()
	{
		//Den Eintrag Felder managen löschen
		$xsql=array();
		$xsql['dbname']         = "papoo_menuint";
		$xsql['select_felder']  = array("menuid","menulink");
		$xsql['limit']          = "";
		$xsql['where_data_like'] = "LIKE";
		$xsql['where_data']     = array("menulink"=>"%form_manager/templates/create_input.html%");
		$result = $this->db_abs->select( $xsql );

		//wenn noch vorhanden, dann löschen
		if (count($result)>=1) {
			//Basis Eintrag löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_menuint";
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " menuid='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );

			//Spracheintrag löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_men_uint_language";
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " menuid_id='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );

			//in rechte Tabelle Daten löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_lookup_men_int";
			$xsql['limit']          = " LIMIT 10";
			$xsql['del_where_wert'] = " menuid='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );
		}

		//Den Eintrag NEU löschen
		$xsql=array();
		$xsql['dbname']         = "papoo_menuint";
		$xsql['select_felder']  = array("menuid","menulink");
		$xsql['limit']          = "";
		$xsql['where_data_like'] = "LIKE";
		$xsql['where_data']     = array("menulink"=>"%form_manager/templates/create_email.html%");
		$result = $this->db_abs->select( $xsql );

		//wenn noch vorhanden, dann löschen
		if (count($result)>=1) {
			//Basis Eintrag löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_menuint";
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " menuid='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );

			//Spracheintrag löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_men_uint_language";
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " menuid_id='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );

			//in rechte Tabelle Daten löschen
			$xsql=array();
			$xsql['dbname']         = "papoo_lookup_men_int";
			$xsql['limit']          = " LIMIT 10";
			$xsql['del_where_wert'] = " menuid='".$this->db->escape($result['0']['menuid'])."' ";
			$result = $this->db_abs->delete( $xsql );
		}

		//Den Eintrag Formular bearbeiten in Formulare umbennen
		$xsql=array();
		$xsql['dbname']         = "papoo_menuint";
		$xsql['select_felder']  = array("menuid","menulink");
		$xsql['limit']          = "";
		$xsql['where_data_like'] = "LIKE";
		$xsql['where_data']     = array("menulink"=>"%form_manager/templates/change_email.html%");
		$result = $this->db_abs->select( $xsql );

		$xsql=array();
		$sql=sprintf("UPDATE %s SET menuname='%s' WHERE menuid_id='%d' AND lang_id='1'",
			DB_PRAEFIX."papoo_men_uint_language",
			"Formulare",
			$result['0']['menuid']
		);
		$this->db->query($sql);

		//Checken ob der Eintrag Kategorien schon vorhanden ist.
		$xsql=array();
		$xsql['dbname']         = "papoo_menuint";
		$xsql['select_felder']  = array("menuid","menulink");
		$xsql['limit']          = "";
		$xsql['where_data_like'] = "LIKE";
		$xsql['where_data']     = array("menulink"=>"%form_manager/templates/manage_form_cat.html%");
		$result = $this->db_abs->select( $xsql);

		if (count($result)<1) {
			//Unterpunkt zu herausholen
			$xsql=array();
			$xsql['dbname']         = "papoo_menuint";
			$xsql['select_felder']  = array("menuid","menulink");
			$xsql['limit']          = "";
			$xsql['where_data_like'] = "LIKE";
			$xsql['where_data']     = array("menulink"=>"%form_manager/templates/form_manager_start.html%");
			$result_sub = $this->db_abs->select( $xsql ,1);

			//Basis Eintrag anlegen
			$xsql=array();
			$xsql['dbname']     = "papoo_menuint";
			$xsql['praefix']    = "";
			$xsql['must']       = array( "menuname" );

			$this->checked->menuid = "";
			$this->checked->menuname="Kategorien";
			$this->checked->menulink="plugin:form_manager/templates/manage_form_cat.html";
			$this->checked->menutitel="Kategorien";
			$this->checked->untermenuzu=$result_sub['0']['menuid'];
			$this->checked->lese_rechte="";
			$this->checked->level="2";
			$this->checked->order_id="2000";

			$cat_dat = $this->db_abs->insert( $xsql );
			$insertid=$cat_dat['insert_id'];

			//Spracheintrag anlegen
			$xsql=array();
			$xsql['dbname']     = "papoo_men_uint_language";
			$xsql['praefix']    = "";
			$xsql['must']       = array( "menuname" );

			$this->checked->lang_id=	1;
			$this->checked->menuid_id=$insertid;
			$this->checked->menuname="Kategorien";
			$this->checked->back_front=1;

			$cat_dat = $this->db_abs->insert( $xsql );

			$xsql=array();
			$xsql['dbname']     = "papoo_men_uint_language";
			$xsql['praefix']    = "";
			$xsql['must']       = array( "menuname" );

			$this->checked->lang_id=	2;
			$this->checked->menuid_id=$insertid;
			$this->checked->menuname="Kategorien";
			$this->checked->back_front=1;

			$cat_dat = $this->db_abs->insert( $xsql );

			//Rechte Eintrag anlegen...
			$xsql=array();
			$xsql['dbname']     = "papoo_lookup_men_int";
			$xsql['praefix']    = "";
			$xsql['must']       = array( "menuid" );

			$this->checked->menuid=$insertid;
			$this->checked->gruppenid=1;

			$cat_dat = $this->db_abs->insert( $xsql );

			//Noch in Plugin Tabelle registrieren damit die auch deinstalliert werden
			$xsql=array();
			$xsql['dbname']         = "papoo_plugins";
			$xsql['select_felder']  = array("plugin_menuids","plugin_id");
			$xsql['limit']          = "";
			$xsql['where_data_like'] = "LIKE";
			$xsql['where_data']     = array("plugin_name"=>"%Formular%");
			$result_ids = $this->db_abs->select( $xsql );

			$xsql=array();
			$xsql['dbname']         = "papoo_plugins";
			$xsql['praefix']        = "";
			$xsql['must']           = array( "plugin_menuids" );
			$xsql['where_name']     = "plugin_id";
			$this->checked->plugin_id=$result_ids['0']['plugin_id'];
			$this->checked->plugin_menuids=$result_ids['0']['plugin_menuids']." ".$insertid;

			$cat_dat = $this->db_abs->update( $xsql );
		}
		else {
			$_SESSION['form_manager']['is_set_menu']="OK";
		}
		return true;
	}

	/**
	 * Check ob die Absender-mail leer ist und nimmt dann die aus alten Versionen genommene Mail
	 */
	private function updateDatabaseSMTP() {
		$sql = sprintf("SELECT form_manager_id, form_manager_sender_mail FROM %s",
			$this->cms->tbname['papoo_form_manager']
		);
		$forms = $this->db->get_results($sql, ARRAY_A);

		// leere settingstype zu system machen

		foreach ($forms as $form) {
			if (empty($form['form_manager_sender_mail'])) {
				$sql = sprintf("SELECT form_manager_email_id, form_manager_email_email FROM %s WHERE form_manager_email_form_id = '%d' ORDER BY form_manager_email_id",
					$this->cms->tbname['papoo_form_manager_email'],
					$form['form_manager_id']
				);
				$mailAdresses = $this->db->get_results($sql, ARRAY_A);
				$mainMailAdress = $mailAdresses[0]['form_manager_email_email'];

				$sql = sprintf("UPDATE %s SET form_manager_sender_mail = '%s' WHERE form_manager_id = '%d'",
					$this->cms->tbname['papoo_form_manager'],
					$this->db->escape($mainMailAdress),
					$form['form_manager_id']
				);
				$this->db->query($sql);
			}
		}
	}
}
