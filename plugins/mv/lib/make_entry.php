<?php
/**
* Neue Verwaltung einbauen
*/
$this->content->template['language'] = "";
// welche Rechtegruppen gibt es?
$this->get_group_user();
// Standard lang Daten holen
$this->make_lang();
// wurde auf Speichern gedr�ckt und ist der Verwaltungsname ausgef�llt?
if (!empty($this->checked->mv_submit)
	&& !empty($this->checked->mv_name))
{
	$sql = sprintf("INSERT INTO %s
							SET mv_name = '%s',
							mv_art = '%d',
							mv_set_suchmaske = '%d'",

							$this->cms->tbname['papoo_mv'],

							$this->db->escape($this->checked->mv_name),
							$this->db->escape($this->checked->mv_art),
							$this->db->escape($this->checked->mv_suchmaske)
					);
	$this->db->query($sql);
	$insertid = $this->db->insert_id;
	$this->checked->mv_id = $insertid;
	$this->content->template['ausgabe'] = $this->content->template['plugin']['mv']['eintrag_gespeichert'];
	$this->content->template['mv_art'] = $this->checked->mv_art;
	// wurde was an der Gruppen ID (Name) ge�ndert
	// ja, bei neu ist es immer wie eine �nderung.
	#if ($this->content->template['group_name'] != $this->checked->group_name) $this->change_group_user();
	$this->change_group_user();
	// neuen Menupunkt im Internabereich f�r die neue Verwaltung einbauen
	require(PAPOO_ABS_PFAD . '/plugins/mv/lib/make_new_menu.php');
	#$this->make_new_menu($insertid);
	// Modul f�r Kalender einbauen
	// stadard Sytle ID aus der Datenbank holen
	$sql = sprintf("SELECT style_id
							FROM %s
							WHERE standard_style = '1'
							LIMIT 1",
							$this->cms->tbname['papoo_styles']
					);
	$standard_style_id = $this->db->get_var($sql);
	// neues Kalendermodul in die Datenbank eintragen
	$sql = sprintf("INSERT INTO %s
							SET mod_aktiv = '0', 
							mod_bereich_id = '0',
							mod_order_id = '0',
							mod_modus = 'var',
							mod_datei = 'plugin:mv/templates/mv_kalender_%d.html',
							mod_style_id = '%d'",

							$this->cms->tbname['papoo_module'],

							$this->db->escape($insertid),
							$this->db->escape($standard_style_id)
					);
	$this->db->query($sql);
	$insertid_module = $this->db->insert_id;
	// noch die Eintr�ge f�r die Sprachtabelle der Module
	$sql = sprintf("INSERT INTO %s
							SET modlang_mod_id = '%d',
							modlang_lang_id = '%d',
							modlang_name = '%s',
							modlang_beschreibung = '%s'",

							$this->cms->tbname['papoo_module_language'],

							$this->db->escape($insertid_module),
							"1",

							"Kalender f$uuml;r "
							. $this->db->escape($this->checked->mv_name),

							"Ein Kalender, der mit der Verwaltung "
							. $this->db->escape($this->checked->mv_name)
							. " verkn&uuml;pft ist."
					);
	$this->db->query($sql);
	// neue mv_content_ID Tabelle
	$sql = sprintf("CREATE TABLE %s (
									  `mv_content_id` int(11) NOT NULL auto_increment,
									  `mv_content_owner` int(11)  NULL  DEFAULT '0',
									  `mv_content_userid` int(11)  NULL  DEFAULT '0',
									  `mv_content_sperre` int(1)  NOT NULL DEFAULT '0',
									  `mv_content_teaser` tinyint(1) DEFAULT 1,
									  `mv_content_create_date` text NOT NULL,
									  `mv_content_edit_date` text NOT NULL,
									  `mv_content_create_owner` text NOT NULL,
									  `mv_content_edit_user` text NOT NULL,						  
									  PRIMARY KEY (`mv_content_id`)
									)
									ENGINE=MyISAM ;",

									$this->cms->tbname['papoo_mv']
									. "_content_"
									. $this->db->escape($insertid)
					);
	#$this->db->query($sql);
	// Tabelle f�r die Meta Ebene
	$sql_meta = sprintf("CREATE TABLE %s (
											`mv_meta_id` int(11) NOT NULL auto_increment,
											`mv_meta_group_id` int(11) NOT NULL default '0',
											`mv_meta_group_name` text NOT NULL,
											`mv_meta_emails` text NOT NULL,
				  							`mv_meta_allow_frontend` int(11) DEFAULT '1',
				  							`mv_meta_allow_direct_entry` int(11) DEFAULT '1',
				 							`mv_meta_allow_direct_unlock` int(11) DEFAULT '1',
											PRIMARY KEY  (`mv_meta_id`)
										)
										ENGINE = MyISAM
										DEFAULT CHARSET = utf8;",

										$this->cms->tbname['papoo_mv']
										. "_meta_"
										. $this->db->escape($insertid)
							);
	$this->db->query($sql_meta);
	// Langtabelle f�r die Meta Ebene
	$sql_meta = sprintf("CREATE TABLE %s (
											`mv_meta_lang_id` int(11) NOT NULL,
											`mv_meta_lang_lang_id` int(11) NOT NULL,
											`mv_meta_top_text` text NOT NULL,
											`mv_meta_bottom_text` text NOT NULL,
											`mv_meta_antwort_text` text NOT NULL,
											`mv_mail_text_create_user` text NOT NULL,
											`mv_mail_text_create_admin` text NOT NULL,
											`mv_mail_text_change_user` text NOT NULL,
											`mv_mail_text_change_admin` text NOT NULL,
											KEY `mv_meta_lang_id` (`mv_meta_lang_id`)
										)
										ENGINE = MyISAM
										DEFAULT CHARSET = utf8;",

										$this->cms->tbname['papoo_mv']
										. "_meta_lang_"
										. $this->db->escape($insertid)
						);
	$this->db->query($sql_meta);
	// Tabelle f�r die Verkn�pfung von Papoo Rechte Gruppen mit den Meta Gruppen der Verwaltung
	$sql_mpg = sprintf("CREATE TABLE %s (
											`mv_mpg_id` int(11) NOT NULL default '0' COMMENT 'Meta ID',
											`mv_mpg_group_id` int(11) NOT NULL default '0',
											`mv_mpg_group_name` text NOT NULL,
											`mv_mpg_write` int(1) DEFAULT '0',
											`mv_mpg_read` int(1) DEFAULT '0',
											KEY `mv_mpg_id` (`mv_mpg_id`)
										)
										ENGINE = MyISAM
										DEFAULT CHARSET = utf8;",

										$this->cms->tbname['papoo_mv']
										. "_mpg_"
										. $this->db->escape($insertid)
						);
	$this->db->query($sql_mpg);
	// holt die MAX meta_id aus allen Verwaltungen
	$max_meta_id = $this->get_max_meta_id();
	$max_meta_id++;
	// Eintrag in den Meta Tabellen f�r die Standard Ebene
	$sql = sprintf("INSERT INTO %s
								SET mv_meta_group_name = 'standard', 
								mv_meta_id = '%d'",

								$this->cms->tbname['papoo_mv']
								. "_meta_"
								. $this->db->escape($insertid),

								$this->db->escape($max_meta_id)
					);
	$this->db->query($sql);
	$sql = sprintf("INSERT INTO %s
								SET mv_mpg_id = '%d', 
								mv_mpg_group_id = '1', 
								mv_mpg_write = '1', 
								mv_mpg_read = '1'",

								$this->cms->tbname['papoo_mv']
								. "_mpg_"
								. $this->db->escape($insertid),

								$this->db->escape($max_meta_id)
					);
	$this->db->query($sql);
	// Sprachtabellen f�r die schnellere Suche
	$sql = sprintf("SELECT mv_lang_id
							FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
					);
	$sprachen = $this->db->get_results($sql);
	if (!empty($sprachen))
	{
		foreach($sprachen as $sprache)
		{
			// Sprachtabelle
// TODO: mv_content_search unused field !! Entfernen, aber Abfragen ber�cksichtigen, die auf > 8 Spalten vergleichen
// wie fp_content.php und make_input_entry.php
			$sql = sprintf("CREATE TABLE %s (
												`mv_content_id` int(11) NOT NULL auto_increment,
												`mv_content_owner` int(11) NULL DEFAULT '0',
												`mv_content_userid` int(11) NULL DEFAULT '0',
												`mv_content_search` int(1) NULL DEFAULT '0',
												`mv_content_sperre` int(1) NOT NULL DEFAULT '0',
												`mv_content_teaser` tinyint(1) NOT NULL DEFAULT 1,
												`mv_content_create_date` text NOT NULL,
												`mv_content_edit_date` text NOT NULL,
												`mv_content_create_owner` text NOT NULL,
												`mv_content_edit_user` text NOT NULL,	
												 PRIMARY KEY (`mv_content_id`)
											)
											ENGINE=MyISAM
											DEFAULT CHARSET = utf8;",

											$this->cms->tbname['papoo_mv']
											. "_content_"
											. $this->db->escape($insertid)
											. "_search_"
											. $this->db->escape($sprache->mv_lang_id)
							);
			$this->db->query($sql);
			// Metaebene Sprachtabellen f�r die Standard Ebene
			$sql = sprintf("INSERT INTO %s
										SET mv_meta_lang_id = '%d',
										mv_meta_lang_lang_id = '%d'",

										$this->cms->tbname['papoo_mv']
										. "_meta_lang_"
										. $this->db->escape($insertid),

										$this->db->escape($max_meta_id),
										$this->db->escape($sprache->mv_lang_id)
							);
			$this->db->query($sql);
		}
	}
	// neue Template Tabelle
	$sql = sprintf("CREATE TABLE %s (
										`id` int(11) NOT NULL ,
										`template_content_all` text NOT NULL,
										`template_content_one` text NOT NULL,
										`template_content_flex_link_selection` text NULL,
										`template_content_flex_link_tree` text NULL,
										`lang_id` int(11) NOT NULL,
										`meta_id` int(11) NOT NULL,
						  				`detail_id` int(11) NOT NULL,
										KEY `id` (`id`)
									)
									ENGINE = MyISAM
									DEFAULT CHARSET = utf8;",

									$this->cms->tbname['papoo_mv']
									. "_template_"
									. $this->db->escape($insertid)
					);
	$this->db->query($sql);
	// F�r jede m�gliche Sprache einen Eintrag machen mit der Metaebene 1 = admin
	$sql = sprintf("SELECT * FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
					);
	$result = $this->db->get_results($sql);
	if (!empty($result))
	{
		$i = 0;
		foreach($result as $row)
		{
			foreach($this->detail_anzahl as $detail_id)
			{
				$sql = sprintf("INSERT INTO %s
											SET	id = '%d',
											lang_id = '%d', 
											meta_id = '1',
							  				detail_id = '%d'",

											$this->cms->tbname['papoo_mv']
											. "_template_"
											. $this->db->escape($insertid),

											$this->db->escape($i),
											$this->db->escape($row->mv_lang_id),
											$this->db->escape($detail_id)
								);
				$this->db->query($sql);
			}
			$i++;
		}
	}
	// wenn Mitgliederverwaltung, dann pw und Kennung einbauen
	if ($this->checked->mv_art == "2")
	{
		$mv_felder = array(
			0 => array(
				'name' => "Benutzername",
				'type' => "text",
				'label1' => "Benutzername",
				'label2' => "Username"
				),
			1 => array(
				'name' => "passwort",
				'type' => "password",
				'label1' => "Passwort",
				'label2' => "Password"
				),
			2 => array(
				'name' => "email",
				'type' => "email",
				'label1' => "E-Mail",
				'label2' => "E-Mail"
				),
			3 => array(
				'name' => "antwortmail",
				'type' => "check",
				'label1' => "Antwortmail",
				'label2' => "Answermail"
				),
			4 => array(
				'name' => "newsletter",
				'type' => "check",
				'label1' => "Newsletter",
				'label2' => "Newsletter"
				),
			5 => array(
				'name' => "board",
				'type' => "select",
				'label1' => "Forum",
				'label2' => "Board",
				'select' => array(
					'de' => "Board Ansicht\r\nThread Ansicht",
					'en' => "Board View\r\nThread View"
					)
				),
			6 => array(
				'name' => "active",
				'type' => "check",
				'label1' => "User aktiv?",
				'label2' => "Active"
				),
			7 => array(
				'name' => "signatur",
				'type' => "textarea",
				'label1' => "Signatur",
				'label2' => "Signatur"
				)
			);
		$this->new_mv = 1;
		$this->checked->mv_id = $insertid; // papoo_mv_content_$insertid
		// Gruppe Systemdaten
		$this->checked->mvcform_group_name = "Systemdaten";
		$this->checked->mvcform_group_text = "Systemdaten";
		$this->checked->mvcform_group_id = $this->insup_new_group("insert");
		$this->checked->mvcform_id = $this->checked->mv_id; // = $insertid
		// die einzelnen SystemFelder f�r eine MV durchgehen
		foreach($mv_felder as $feld)
		{
			$this->mv_default_wert = "";
			// default Wert f�r das Board definieren + Forumsauswahlm�glichkeiten
			if (!empty($feld['select']))
			{
				#$this->mv_default_wert = "DEFAULT '1' ";
				$this->checked->mvcform_content_list = $feld['select']['de'];
			}
			$this->checked->mvcform_name = $feld['name'];
			$this->checked->mvcform_type = $feld['type'];
			$this->checked->mvcform_label = $feld['label1'];
			// require kann hier nicht eingesetzt werden !! Der w�rde nur 1mal durchlaufen.
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/alter_new_field.php');
			#if ($feld['name'] == "Benutzername") $this->checked->mvcform_lang_dependence = 1; // immer sprachunabh�ngig
			require(PAPOO_ABS_PFAD . '/plugins/mv/lib/insup_new_field.php');
			#$this->insup_new_field();
			// damits in der n�chsten Runde keine Lookup Tabelle gibt, obwohl das Feld keine braucht
			$this->checked->mvcform_content_list = "";
		}
		$this->new_mv = 0;
	}
	// Die language Daten f�r die neue Verwaltung speichern
	// Welche Sprachen gibt es?
	$sql = sprintf("SELECT mv_lang_id
							FROM %s",
							$this->cms->tbname['papoo_mv_name_language']
					);
	$result = $this->db->get_results($sql);
	// gehe die einzelnen Sprachen durch
	if (!empty($result))
	{
		foreach($result as $sprache)
		{
			// und speicher f�r alle das Gleiche wie in der ausgew�hlten Sprache ab
			$sql = sprintf("INSERT INTO %s
										SET mv_id_id = '%s',
										mv_name_label = '%s',
										mv_lang_id = '%s'",
										
										$this->cms->tbname['papoo_mv_lang'],
										
										$this->db->escape($insertid),
										$this->db->escape($this->checked->mv_name),
										$this->db->escape($sprache->mv_lang_id)
							);
			$this->db->query($sql);
		}
	}
	// Rechtetabellen f�r diese Verwaltung
	$this->make_content_rights();
	// Default Werte f�r Admin in die Tabelle
	$this->change_content_rights('', 1, 1, 1);
	$this->change_content_rights('', 11, 1, 0); // dito jeder lesen
	$this->make_field_rights();
	$this->make_group_rights();
	// damit der neue Men�punkt auch gleich erscheint, nochmals die Seite aufrufen
	if ($this->checked->fertig != 1)
	{
		$location_url = $_SERVER['PHP_SELF']
						. "?menuid="
						. $this->checked->menuid
						. "&fertig=1&template="
						. $this->checked->template
						. "&mv_id="
						. $this->checked->mv_id;

		if ($_SESSION['debug_stopallredirect'])
			echo '<a href="'
			. $location_url
			. '">'
			. $this->content->template['plugin']['mv']['weiter']
			. '</a>';
		else header("Location: $location_url");
		exit;
	}
}
?>
