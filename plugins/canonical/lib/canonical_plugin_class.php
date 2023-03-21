<?php

/**
 * Class canonical_plugin_class
 * Diese Klasse erstellt im Frontend die canonischen Links die
 * man in der Admin eingegeben hat...
 */
#[AllowDynamicProperties]
class canonical_plugin_class
{
	/**
	 * canonical_plugin_class constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $diverse, $cms, $db_abs;

		// Einbindung des globalen Content-Objekts
		$this->content = $content;
		$this->db = $db;
		$this->checked = $checked;
		// User Klasse einbinden
		$this->user = $user;
		$this->diverse = $diverse;
		//CMS Daten einbinden
		$this->cms = $cms;
		//Intern Menü Klass einbinden
		$this->db_abs = $db_abs;

		//Nur in der Admin
		if (defined("admin")) {
			// �berpr�fen ob der User Zugriff haben darf
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
				if ($template2=="canonical_back.html") {
					// Daten speichern wenn notwendig
					$this->save_settings();
					// Daten rausholen f�r die Anzeige der Links
					$this->show_data();
				}
			}
		}
	}

	/**
	 * @return array|null
	 * Daten aus der DB holen
	 */
	private function get_data()
	{
		$sql=sprintf("SELECT * FROM %s LIMIT 1",
			$this->cms->tbname['plugin_canonical_config']
		);
		$result=$this->db->get_results($sql,ARRAY_A);

		return $result;
	}

	/**
	 * canonical_plugin_class::show_data()
	 * Daten rausholen f�r die Anzeige der Links
	 * @return void
	 */
	private function show_data()
	{
		// Dann die DAten rausholen f�r die Ausgabe
		$result=$this->get_data();

		/**
		 * Daten in eine Var zuweisen,
		 * als Array hat es komischerweise nicht
		 * geklappt
		 */
		IfNotSetNull($result['0']['canonical_plugin_redirect_liste']);
		$this->content->template['canonical_plugin_redirect_liste_data']="nobr:".$result['0']['canonical_plugin_redirect_liste'];
	}

	/**
	 * canonical_plugin_class::save_settings()
	 *
	 * @return void
	 */
	private function save_settings()
	{
		/**
		 * Zuerst den alten Eintrag l�schen
		 */
		if (!empty($this->checked->formSubmit_canonical_settings)) {
			$sql=sprintf("DELETE FROM %s ",
				$this->cms->tbname['plugin_canonical_config']
			);
			$this->db->query($sql);

			// Dann den neuen eintragen
			$xsql['dbname'] = "plugin_canonical_config";
			$xsql['praefix'] = "canonical_plugin";
			$this->db_abs->insert($xsql);
		}
	}

	/**
	 * MIt der Outputfilter die Inhalte in der Seite manipulieren
	 */
	public function output_filter()
	{
		//erstmal die Daten holen
		$result=$this->get_data();

		//LInks aus der DB
		$links=$result['0']['canonical_plugin_redirect_liste'];

		//Zeilen vereinzeln
		$l_ar=explode("\n",$links);

		//Zeilen aufbrechen um die einzelnen Links rauszuholen
		if (is_array($l_ar)) {
			foreach ($l_ar as $key=>$value) {
				//Links in array zuweisen
				$dat=explode(";",$value);
				$neu[$dat['0']]=$dat['1'];
			}
		}
		//Die aktuell aufgerufene url
		$aktu_url=$_SERVER['REQUEST_URI'];

		//Wenn die aktuelle url im Array ist, dann soll canonisch sein...
		if (!empty($neu[$aktu_url])) {
			//Canonische url
			$canonical=$neu[$aktu_url];

			//Inhalte einbinden
			global $output;

			//canonische url ausgeben
			$output=str_ireplace("<head>","<head>\n".'<link rel="canonical" href="'.$canonical.'">',$output);
		}
	}
}
//Klasse INI
$canonical_plugin = new canonical_plugin_class();
