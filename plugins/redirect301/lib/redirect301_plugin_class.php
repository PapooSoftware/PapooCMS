<?php

/**
 * Class redirect301_plugin_class
 */
#[AllowDynamicProperties]
class redirect301_plugin_class
{
	/**
	 * redirect301_plugin_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $diverse, $cms, $db_abs;
		$this->content = $content;
		$this->db = $db;
		$this->checked = $checked;
		$this->user = $user;
		$this->diverse = $diverse;
		$this->cms = $cms;
		$this->db_abs = $db_abs;

		//Nur in der Admin
		if (defined('admin') ) {
			/**
			 * �berpr�fen ob der User Zugriff haben darf
			 */
			$this->user->check_intern();

			/**
			 * Das aktuelle Template auslesen, anhand dessen kann sichergestellt werden
			 * dass man im richtigen Plugin ist
			 */
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			// Korrekt eingeloggt
			if ( $template != "login.utf8.html" ) {
				// und auch noch das richtige Template
				if ($template2=="redirect301_back.html") {
					// Daten speichern wenn notwendig
					$this->save_settings();
					// Daten rausholen f�r die Anzeige der Links
					$this->show_data();
				}
			}
		}
	}

	/**
	 * redirect301_plugin_class::show_data()
	 * Daten rausholen f�r die Anzeige der Links
	 *
	 * @return void
	 */
	function show_data()
	{
		/**
		 * Dann die DAten rausholen f�r die Ausgabe
		 */
		$sql=sprintf("SELECT * FROM %s LIMIT 1",
			$this->cms->tbname['plugin_301_config']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		/**
		 * Daten in eine Var zuweisen,
		 * als Array hat es komischerweise nicht
		 * geklappt
		 */
		IfNotSetNull($result['0']['redirect301_plugin_redirect_liste']);
		$this->content->template['redirect301_plugin_redirect_liste_data']= "nobr:" . $result['0']['redirect301_plugin_redirect_liste'];

	}

	/**
	 * redirect301_plugin_class::save_settings()
	 *
	 * @return void
	 */
	function save_settings()
	{
		/**
		 * Zuerst den alten Eintrag l�schen
		 */
		if (!empty($this->checked->formSubmit_301_settings)) {
			$sql=sprintf("DELETE FROM %s ",
				$this->cms->tbname['plugin_301_config']
			);
			$this->db->query($sql);

			// Dann den neuen eintragen
			$xsql['dbname'] = "plugin_301_config";
			$xsql['praefix'] = "redirect301_plugin";
			$this->db_abs->insert( $xsql );

			/**
			 * Jetzt die Daten noch in der Datei speichern
			 * Nur dann kann im FE auch darauf zugegriffen werden
			 */
			try {
				$this->diverse->write_to_file("/templates_c/redirect.csv",$this->checked->redirect301_plugin_redirect_liste);
			}
			catch (Exception $e) {
				$this->content->template['redirect_error']="Die Datei konnte nicht erstellt werden. Bitte �ndern Sie die Schreibrechte des Verzeichnisses /templates_c auf 777.";
			}
		}
	}
}

$redirect301_plugin = new redirect301_plugin_class();
