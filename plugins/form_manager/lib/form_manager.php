<?php

/**
 * form_manager
 *
 * @package
 * @author Administrator
 * @copyright Copyright (c) 2007
 * @version $ID$
 * @access public
 */
#[AllowDynamicProperties]
class form_manager
{
	/**
	 * Formular-Manager-Plugin
	 */

	/** @var bool Hiermit kann man einstellen, ob die Salesforce Schnittstellt genutzt werden soll oder nicht */
	var $salesforce = true;
	/** @var bool Hiermit kann man einstellen, ob die Anhänge mit an die Bestätigungsmail für den Kunden gehängt werden */
	var $antwort_mail_attachments = true;
	/** @var bool Hiermit kann man einstellen, ob der Benutzer mehrere Empfänger auswählen kann oder nur einen */
	var $select_mail_multiple = false;
	/** @var int CSV Datei mit den Formulardaten anhängen an Mail an Betreiber */
	var $add_csv_to_mail = 0;
	/** @var int Upload und Daten per xml in Verzeichnis */
	var $xml_verzeichnis = 0;

	/**
	 * form_manager constructor.
	 */
	function __construct()
	{
		global $cms, $db, $message, $content, $checked, $intern_menu, $menu, $blacklist,
			   $weiter, $diverse, $mail_it, $download, $user, $intern_image, $intern_artikel;
		$this->cms = &$cms;
		$this->db = &$db;
		$this->message = &$message;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->intern_menu = &$intern_menu;
		$this->menu = &$menu;
		$this->blacklist = &$blacklist;
		$this->weiter = &$weiter;
		$this->diverse = &$diverse;
		$this->mail_it = &$mail_it;
		$this->download = &$download;
		$this->user = &$user;
		$this->intern_artikel = &$intern_artikel;
		$this->intern_image = &$intern_image;

		// Wenn Admin dann durchführen
		$this->make_form_manager_plugin();
		$this->check_installed_leadtracker();

		//Nur intern
		if (defined("admin")) {
			//Update einbauen - damit es live upgedated werden kann
			require_once(PAPOO_ABS_PFAD . "/plugins/form_manager/lib/form_update.php");

			//INI Update
			$update = new form_update();

			//Jetzt die Kategorien managen
			require_once(PAPOO_ABS_PFAD . "/plugins/form_manager/lib/form_cats.php");

			//INI Kategorien
			$this->cats = new form_cats();
		}

		IfNotSetNull($_SERVER['HTTP_REFERER']);

		$expire = time() + 2592000;
		if (stristr($_SERVER['HTTP_REFERER'], "google") && empty($_COOKIE["RefererG"])) {
			setcookie("RefererG", $_SERVER['HTTP_REFERER'], $expire, "/");
		}

		if (!stristr($_SERVER['HTTP_REFERER'], "http://www.fahr-zeit")) {
			setcookie("RefererN", $_SERVER['HTTP_REFERER'], $expire, "/");
		}

		if (!stristr($_SERVER['HTTP_REFERER'], "http://www.fahr-zeit") && empty($_COOKIE["RefererF"])) {
			setcookie("RefererF", $_SERVER['HTTP_REFERER'], $expire, "/");
		}
	}

	function check_installed_leadtracker()
	{
		$sql = sprintf("SELECT COUNT(*) FROM %s WHERE plugin_name = 'Leadtracker-Plugin'",
			$this->cms->tbname['papoo_plugins']
		);
		$result = $this->db->get_var($sql);
		if ($result) {
			$this->content->template['plugin']['form_manager']['installed_leadtracker'] = true;
		}
	}

	/**
	 * @param $val
	 * @return mixed|string|string[]|null
	 */
	function tidy_string($val)
	{
		// whitespace durch Bindestrich ersetzen
		$new = preg_replace('=(\s+)=', '-', $val);

		// Liste aller Umlaute
		$map = array(
			'ä' => 'ae',
			'Ä' => 'AE',
			'ß' => 'ss',
			'ö' => 'oe',
			'Ö' => 'OE',
			'Ü' => 'UE',
			'ü' => 'ue',
		);

		// Umlaute konvertieren
		$new = str_replace(array_keys($map), array_values($map), $new);

		// alle anderen Zeichen verwerfen
		$new = preg_replace('#[^A-Za-z0-9_.-]#', '', $new);

		$new = strtolower($new);

		return $new;
	}

	/**
	 * Admin switch
	 */
	function make_form_manager_plugin()
	{
		if (defined("admin")) {
			global $template;

			IfNotSetNull($this->checked->template);

			$this->user->check_intern();

			$templatedat = basename($template);
			if ($templatedat != "login.utf8.html") {
				switch ($this->checked->template) {
					// Die Standardeinstellungen werden bearbeitet
				case "form_manager/templates/form_manager_start.html" :
					break;

					// Einen Dump erstellen oder einspielen
				case "form_manager/templates/backup.html" :
					$this->form_manager_dump();
					break;

					// Einen Eintrag erstellen
				case "form_manager/templates/create_email.html" :
					$this->make_entry();
					break;

					// Eine Exportdatei erstellen
					// Einen Eintrag bearbeiten
				case "form_manager/templates/change_email.html" :
					if (isset($this->checked->form_manager_export_id)) {
						$this->form_export();
					}
					$this->change_entry();
					break;

					// Einen Eintrag erstellen
				case "form_manager/templates/create_input.html" :
					$this->make_input_entry();
					break;

					// Einen Eintrag erstellen
				case "form_manager/templates/messages.html" :
					$this->check_leads_entry();
					break;

				default :
					break;
				}
				$this->content->template['salesforce'] = (int)($this->salesforce);
			}
		}
	}

//----------------------------------------------------------------------------------------------anfang form export
	/**
	 * @author Christian Klihm
	 *
	 * zum exportieren von Formularen
	 *
	 */
	function form_export()
	{
		$queries = array();
		$delimiter = "; ##b_dump##\n";
		$form_id = (int)$this->checked->form_manager_export_id;

//----------------------------------------------------------------------------------------------------- form_manager
		// hole das Formular aus der Datenbank
		$sql = sprintf("SELECT * FROM `%s` WHERE `form_manager_id`='%d'",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($form_id)
		);
		$result = $this->db->get_row($sql, ARRAY_A);

		if (is_array($result)) {
			// erstelle INSERT Statement für das Formular
			$values = array();
			foreach ($result as $column => $cell) {
				if ($column === "form_manager_id") {
					// überspringe Primärschlüssel für AUTO INCREMENT
					continue;
				}
				else {
					if ($column === "form_manager_kategorie") {
						$cell = 0;
					}
				}
				$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
			}
			$queries[] = sprintf("INSERT INTO `XXX_papoo_form_manager` SET %s",
				implode(", ", $values)
			);

			// hole die ID des neu angelegten Formulars
			$queries[] = "SET @new_form_manager_id = LAST_INSERT_ID()";
//------------------------------------------------------------------------------------------------------- form_manager_email
			// hole die zugehörigen emailadressen aus der Datenbank
			$sql = sprintf("SELECT * FROM `%s` WHERE `form_manager_email_form_id`='%d'",
				$this->cms->tbname['papoo_form_manager_email'],
				$this->db->escape($form_id)
			);
			$results = $this->db->get_results($sql, ARRAY_A);

			if (is_array($results)) {
				foreach ($results as $result) {
					// erstelle INSERT Statements für die emailadressen
					$values = array();
					foreach ($result as $column => $cell) {
						if ($column === "form_manager_email_id") {
							// überspringe Primärschlüssel für AUTO INCREMENT
							continue;
						}
						else {
							if ($column === "form_manager_email_form_id") {
								// neue form_manager_id als Fremdschlüssel setzen
								$values[] = "`" . $column . "`=@new_form_manager_id";
							}
							else {
								$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
							}
						}
					}
					$queries[] = sprintf("INSERT INTO `XXX_papoo_form_manager_email` SET %s",
						implode(", ", $values)
					);
				}
			}
//-------------------------------------------------------------------------------------------------------- form_manager_lang
			// hole die zugehörigen form_lang-Daten aus der Datenbank
			$sql = sprintf("SELECT * FROM `%s` WHERE `form_manager_id_id`='%d'",
				$this->cms->tbname['papoo_form_manager_lang'],
				$this->db->escape($form_id)
			);
			$results = $this->db->get_results($sql, ARRAY_A);

			if (is_array($results)) {
				foreach ($results as $result) {
					// erstelle INSERT Statements für die form_lang-Daten
					$values = array();
					foreach ($result as $column => $cell) {
						if ($column === "form_manager_id_id") {
							// neue form_manager_id als Fremdschlüssel setzen
							$values[] = "`" . $column . "`=@new_form_manager_id";
						} else {
							$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
						}
					}
					$queries[] = sprintf("INSERT INTO `XXX_papoo_form_manager_lang` SET %s",
						implode(", ", $values)
					);
				}
			}
//-------------------------------------------------------------------------------------------------------- plugin_cform_group
			// hole ALLE Daten der dazugehörigen Gruppen aus der Datenbank
			$sql = sprintf("SELECT * FROM `%s` WHERE `plugin_cform_group_form_id`='%d'",
				$this->cms->tbname['papoo_plugin_cform_group'],
				$this->db->escape($form_id)
			);
			$results_group = $this->db->get_results($sql, ARRAY_A);

			if (is_array($results_group)) {
				foreach ($results_group as $result_group) {
					$group_id = (int)$result_group['plugin_cform_group_id'];

					// erstelle INSERT Statements für die Gruppen
					$values = array();
					foreach ($result_group as $column => $cell) {
						if ($column === "plugin_cform_group_id") {
							// überspringe Primärschlüssel für AUTO INCREMENT
							continue;
						}
						else {
							if ($column === "plugin_cform_group_form_id") {
								// neue form_manager_id als Fremdschlüssel setzen
								$values[] = "`" . $column . "`=@new_form_manager_id";
							}
							else {
								$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
							}
						}
					}
					$queries[] = sprintf("INSERT INTO `XXX_papoo_plugin_cform_group` SET %s",
						implode(", ", $values)
					);
					// hole die ID der neu angelegten Gruppe
					$queries[] = "SET @new_cform_group_id = LAST_INSERT_ID()";
					//------------------------------------------------------------------------------- plugin_cform_group_lang
					// Hole den zur Gruppe zugehörigen Spracheintrag aus der Datenbank
					$sql = sprintf("SELECT * FROM `%s` WHERE `plugin_cform_group_lang_id`='%d'",
						$this->cms->tbname['papoo_plugin_cform_group_lang'],
						$group_id
					);
					$results_group_lang = $this->db->get_results($sql, ARRAY_A);

					if (is_array($results_group_lang)) {
						foreach ($results_group_lang as $result_group_lang) {
							// erstelle INSERT Statements für die Gruppen
							$values = array();
							foreach ($result_group_lang as $column => $cell) {
								if ($column === "plugin_cform_group_lang_id") {
									// neue cform_group_id als Fremdschlüssel setzen
									$values[] = "`" . $column . "`=@new_cform_group_id";
								} else {
									$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
								}
							}
							$queries[] = sprintf("INSERT INTO `XXX_papoo_plugin_cform_group_lang` SET %s",
								implode(", ", $values)
							);
						}
					}
					//----------------------------------------------------------------------------------------- plugin_cform
					// hole Daten der aktuellen Gruppe und dessen Felder aus der Datenbank
					$sql = sprintf("SELECT * FROM `%s` WHERE `plugin_cform_group_id`='%d'",
						$this->cms->tbname['papoo_plugin_cform'],
						$group_id
					);
					$results_field = $this->db->get_results($sql, ARRAY_A);

					if (is_array($results_field)) {
						foreach ($results_field as $result_field) {
							// erstelle INSERT Statements für die Gruppen
							$values = array();
							foreach ($result_field as $column => $cell) {
								if ($column === "plugin_cform_id") {
									// überspringe Primärschlüssel für AUTO INCREMENT
									continue;
								}
								else {
									if ($column === "plugin_cform_group_id") {
										// neue group_id als 1.Fremdschlüssel setzen
										$values[] = "`" . $column . "`=@new_cform_group_id";
									}
									else {
										if ($column === "plugin_cform_form_id") {
											// neue form_manager_id als 2.Fremdschlüssel setzen
											$values[] = "`" . $column . "`=@new_form_manager_id";
										}
										else {
											$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
										}
									}
								}
							}
							$queries[] = sprintf("INSERT INTO `XXX_papoo_plugin_cform` SET %s",
								implode(", ", $values)
							);
							// hole die ID des neu angelegten Feldes der neu angelegten Gruppe
							$queries[] = "SET @new_cform_id = LAST_INSERT_ID()";
							//----------------------------------------------------------------------------- plugin_cform_lang
							// Hole die zu den Feldern zugehörigen Spracheinträge aus der Datenbank
							$sql = sprintf("SELECT * FROM `%s` WHERE `plugin_cform_lang_id`='%d'",
								$this->cms->tbname['papoo_plugin_cform_lang'],
								(int)$result_field['plugin_cform_id']
							);
							$results_field_lang = $this->db->get_results($sql, ARRAY_A);

							if (is_array($results_field_lang)) {
								foreach ($results_field_lang as $result_field_lang) {
									// erstelle INSERT Statements für die Gruppen
									$values = array();
									foreach ($result_field_lang as $column => $cell) {
										if ($column === "plugin_cform_lang_id") {
											// neue cform_id als Fremdschlüssel setzen
											$values[] = "`" . $column . "`=@new_cform_id";
										} else {
											$values[] = "`" . $column . "`='" . $this->db->escape($cell) . "'";
										}
									}
									$queries[] = sprintf("INSERT INTO `XXX_papoo_plugin_cform_lang` SET %s",
										implode(", ", $values)
									);
								}
							}
						}
					}
				}
			}
			header("Content-Length: " . strlen(implode($delimiter, $queries)));
			header("Content-Disposition: attachement; filename=form.sql");
			echo implode($delimiter, $queries);
			exit;
		}
	}

//----------------------------------------------------------------------------------------------ende form export

	function set_lead_name()
	{
		//lead_eintrag_name
		$sql = sprintf("SELECT form_manager_name FROM %s WHERE form_manager_id='%d'",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($this->checked->form_manager_id)
		);
		$name = $this->db->get_var($sql);
		$this->content->template['lead_eintrag_name'] = $name;
		$this->content->template['form_name_tidy'] = $this->tidy_string($name);
	}

	/**
	 * Wenn wir draußen sind
	 */
	function post_papoo()
	{
		if (!defined("admin")) {
			global $template;

			//Update einbauen - damit es live upgedated werden kann
			require_once(PAPOO_ABS_PFAD . "/lib/classes/intern_menu_class.php");

			$menu = new intern_menu_class();

			$menues_mit_rechten = $menu->mak_menu_liste_zugriff();

			$leserechte = false;

			foreach($menues_mit_rechten as $menue){
				if($this->checked->menuid == $menue['menuid']){
					$leserechte = true;
				}
			}

			// Formular anzeigen
			if ((stristr($template, "form.html") && is_numeric($this->checked->form_manager_id) && $leserechte) ||
				$this->content->template['module_aktiv']['form_modul'] == 1 && $leserechte
			) {
				$this->set_lead_name();

				// Form-Modul deaktivieren, wenn anderes Formular fuer diese Seite angezeigt werden soll.
				if (stristr($template, "/form_manager/templates/form.html")) {
					$this->content->template['module_aktiv']['form_modul'] = false;
				} // sonst (bei Form-Modul) Spamschutz deaktivieren
				else {
					$this->content->template['stamm_kontakt_spamschutz'] = false;
				}

				global $cache;
				$cache->aktiv = false;

				// Messages zuweisen
				//Formular fertig - Nachricht ausgeben
				if (isset($this->checked->fertig) && $this->checked->fertig == 1) {
					$sql = sprintf("SELECT * FROM %s
					WHERE form_manager_id_id='%s'
					AND form_manager_lang_id='%s'",
						$this->cms->tbname['papoo_form_manager_lang'],
						$this->db->escape($this->checked->form_manager_id),
						$this->db->escape($this->cms->lang_id)
					);
					$result = $this->db->get_results($sql);

					//Formulardaten
					$sql = sprintf("SELECT * FROM %s
					WHERE form_manager_id='%s' ",
						$this->cms->tbname['papoo_form_manager'],
						$this->db->escape($this->checked->form_manager_id)
					);
					$result2 = $this->db->get_results($sql, ARRAY_A);
					$this->content->template['etracker_ziel'] = $result2['0']['form_manager_id'];

					//Dann die Felder rausholen
					$sql = sprintf("SELECT * FROM %s
                    LEFT JOIN %s ON plugin_cform_lang_id=plugin_cform_id
					WHERE plugin_cform_form_id='%s'
					AND plugin_cform_lang_lang='%d'",
						$this->cms->tbname['papoo_plugin_cform'],
						$this->cms->tbname['papoo_plugin_cform_lang'],
						$this->db->escape($this->checked->form_manager_id),
						$this->cms->lang_id
					);
					$result3 = $this->db->get_results($sql, ARRAY_A);
					foreach ($result3 as $k => $v) {
						$feld[$v['plugin_cform_name']]['typ'] = $v['plugin_cform_type'];
						$feld[$v['plugin_cform_name']]['label'] = $v['plugin_cform_label'];
					}

					$save_mv_content_id = isset($this->checked->mv_content_id) ? $this->checked->mv_content_id : NULL;
					$save_mv_id = isset($this->checked->mv_id) ? $this->checked->mv_id : NULL;
					$this->checked->mv_content_id = isset($this->checked->flexid) ? $this->checked->flexid : NULL;
					$this->checked->mv_id = isset($this->checked->flex_mv_id) ? $this->checked->flex_mv_id : NULL;
					$this->form_bind_mv();
					$this->form_show_search_mv();
					$this->checked->mv_content_id = $save_mv_content_id;
					$this->checked->mv_id = $save_mv_id;
					if (!empty ($result)) {
						foreach ($result as $spalte) {
							if (is_array($_SESSION['formdat'])) {
								foreach ($_SESSION['formdat'] as $key => $value) {
									$key = $this->html2txt($key);
									$value = $this->html2txt($value);

									if ($feld[$key]['typ'] == "check_replace" && $value == 1) {
										$expld1 = explode("|", $feld[$key]['label']);
										$value = trim($expld1['1']);

										//Wenn Link
										if (stristr($value, "http")) {
											$value = '<a href="' . $expld1['1'] . '" target="blank" >' . $expld1['0'] . '</a>';
										}
									}

									if (is_string($value)) {
										$spalte->form_manager_antwort_html = preg_replace('/#' . preg_quote($key, '/') . '#/', $value,
											$spalte->form_manager_antwort_html);
									}

								}
							}
							if (isset($this->mv_daten_array) && is_array($this->mv_daten_array)) {
								foreach ($this->mv_daten_array as $key => $value) {
									$key = str_ireplace("/", "", $key);
									$key = str_ireplace(">", "", $key);
									$spalte->form_manager_antwort_html = preg_replace('/#flex_' . preg_quote($key, '/') . '#/', $value,
										$spalte->form_manager_antwort_html);
								}
							}

							$temp_form_manager_antwort_html = $spalte->form_manager_antwort_html;
							$temp_form_manager_antwort_html = $this->diverse->do_pfadeanpassen("nobr:" . $temp_form_manager_antwort_html);
							$temp_form_manager_antwort_html = $this->download->replace_downloadlinks($temp_form_manager_antwort_html);
							$this->content->template['form_html'] = $temp_form_manager_antwort_html;
						}
					}
					$this->content->template['message1'] = "ok";
				}
				else {
					// Wenn verschickt wurde
					if (!empty($this->checked->form_manager_submit)) {
						$inhalt = "";

						// Daten aus POST rausholen und in ein string einlesen
						foreach ($_POST as $key => $value) {
							$inhalt .= $key . ": ";
							if (is_array($value)) {
								$first = true;
								foreach ($value as $item) {
									if (!$first) {
										$inhalt .= '; ';
									}
								}
								$first = false;
								$inhalt .= $this->html2txt($item);
							}
							else {
								$inhalt .= $this->html2txt($value);
							}
							$inhalt .= "\n";
						}

						$this->make_form_check();

						global $spamschutz;

						if ($this->cms->stamm_kontakt_spamschutz && $spamschutz->is_spam) {
							$this->error["__spamschutz__"] = "error";
						}
						else {
							// Daten verschicken
							if ($this->insert_ok == true && $this->checked->is_last_page == 1) {
								//Wenn ok dann senden,
								if ($this->blok == "ok") {
									$this->verschick($inhalt);
								}

								if (empty($this->checked->template)) {
									$this->checked->template = "form_manager/templates/form.html";
								}
								$location_url =
									PAPOO_WEB_PFAD . "/plugin.php?menuid=" . $this->checked->menuid .
									"&fertig=1&template=" . $this->checked->template .
									"&form_manager_id=" . $this->checked->form_manager_id .
									"&flexid=" . $this->checked->flexid .
									"&flex_mv_id=" . $this->checked->flex_mv_id .
									"&style=" . $this->checked->style .
									"&savetime=" . $this->time_save .
									"&getlang=" . $this->content->template['lang_short'];

								if ($_SESSION['debug_stopallredirect']) {
									echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
								}
								else {
									header("Location: $location_url");
								}
								exit;
							}
						}
					}
				}

				$this->checked->form_manager_id = $this->get_form_id();

				// Formular raussuchen und anzeigen
				if ((is_numeric($this->checked->form_manager_id))) {
					$this->content->template['formok'] = "ok";
					$this->content->template['form_manager_id'] = $this->checked->form_manager_id;
					// FOrmular raussuchen und anzeigen
					$this->front_get_form($this->checked->form_manager_id);
				}
			}
			//Auf löschen checken
			$this->form_manager_check_loesch();
		}
	}

	/**
	 * form_manager::get_form_id()
	 *
	 * @return int
	 */
	private function get_form_id()
	{
		global $template;
		//Wenn normales Formular - dann einfach die ID zurückgeben
		if (is_numeric($this->checked->form_manager_id) && $template != "index.html") {
			return $this->checked->form_manager_id;
		}
		$no_anzeige = false;

		//Module durchgehen und schauen ob es ein Formular gibt
		if (is_array($this->content->template['module'])) {
			foreach ($this->content->template['module'] as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key1 => $value1) {
						//Hossa - da ist eines - dann die ID des zugehörigen Formulares raussuchen
						if ($value1['mod_datei'] == "plugin:form_manager/templates/form_modul.html") {
							//debug::print_d($value1);
							$sql = sprintf("SELECT * FROM %s
											WHERE form_manager_modul_id='%d'",
								$this->cms->tbname['papoo_form_manager'],
								$this->db->escape($value1['mod_id'])
							);
							$result = $this->db->get_results($sql, ARRAY_A);

							//Jetzt checken ob im Menüpunkt ok
							$sql = sprintf("SELECT * FROM %s
											WHERE
											form_menu_id='%d'
											AND  form_mid ='%d'
											OR
											form_menu_id='999999999'
											AND  form_mid ='%d'",
								$this->cms->tbname['papoo_form_manager_menu_lookup'],
								$this->db->escape($this->checked->menuid),
								$this->db->escape($result['0']['form_manager_id']),
								$this->db->escape($result['0']['form_manager_id'])
							);
							$result_men = $this->db->get_results($sql, ARRAY_A);

							if (!empty($result_men)) {
								//Nix zugewiesen, daher anzeigen
								$no_anzeige = true;

								//checken ob überhaupt, dann immer
								$sql = sprintf("SELECT * FROM %s
												WHERE
												form_mid ='%d'
												",
									$this->cms->tbname['papoo_form_manager_menu_lookup'],
									$this->db->escape($result['0']['form_manager_id'])
								);
								$result_men2 = $this->db->get_results($sql, ARRAY_A);
								if (empty($result_men2)) {
									//Nix eingetragen, dann immer Backkompatibilität
									$no_anzeige = false;
								}
								$form_id_return = $result['0']['form_manager_id'];
							}
							else {
								unset($this->content->template['module'][$key][$key1]);
							}
						}
					}
				}
			}
			return $form_id_return;
		}
	}

	/**
	 * if ($this->blacklist->do_blacklist($this->checked->mitteilung)!="not_ok")
	 * {
	 */
	function check_blacklist()
	{
		$felder = $this->get_felder();
		//durchloopen
		foreach ($felder as $feld) {
			if ($this->blacklist->do_blacklist($this->checked->{$feld['cform_name']}) == "not_ok") {
				$this->blerror = "pfui";
			}
		}
		if (strlen($this->blerror) > 0) {
			$this->blok = "no";
		}
		else {
			$this->blok = "ok";
		}
	}

	/**
	 * form_manager::form_manager_check_loesch()
	 * Checken welche Einträge gelöscht werden können
	 * Hier werden ja nur die echten Löschungen durchgeführt
	 * @return void
	 */
	function form_manager_check_loesch()
	{
		//3 Tages Frist
		$dreitage = time() - 3 * 24 * 60 * 60;

		//Alle rausholen die in 3 Tagen gelöscht werden und dann mailen an...
		$sql = sprintf("SELECT * FROM %s
									WHERE form_manager_form_loesch_datum2<'%d'
									AND form_manager_form_loesch_yn!='1'
									AND form_manager_lang_mail2_send!='1'",
			$this->cms->tbname['papoo_form_manager_leads'],
			$dreitage
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (is_array($result)) {
			foreach ($result as $daten) {
				IfNotSetNull($this->content->template['plugin']['form_manager']['lead_inhalt']);
				$inhalt = $this->content->template['plugin']['form_manager']['lead_inhalt'];
				$menuidhier = $this->checked->menuid + 4;
				$link =
					$this->cms->title . PAPOO_WEB_PFAD .
					"/interna/plugin.php?menuid=" . $menuidhier .
					"&template=form_manager/templates/messages.html&form_manager_id=" . $daten['form_manager_form_id'] .
					"&lead_id=" . $daten['form_manager_lead_id'];

				$inhalt = str_ireplace("#leadid#", $link, $inhalt);
				//E-Mail rausholen form_manager_lead_id
				// E-Mail Adress an die gesendet werden soll
				$sql = sprintf("SELECT form_manager_email_id, form_manager_email_email FROM %s
												WHERE form_manager_email_form_id='%s' ORDER BY form_manager_email_id ASC",
					$this->cms->tbname['papoo_form_manager_email'],
					$this->db->escape($daten['form_manager_form_id'])
				);
				$die_mails = $this->db->get_results($sql, ARRAY_A);
				$form_manager_email = "";
				if (isset($this->checked->sendto) && is_numeric($this->checked->sendto)) {
					if (is_array($die_mails)) {
						foreach ($die_mails as $dm) {
							if ($this->checked->sendto == $dm['form_manager_email_id']) {
								$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
							}
						}
					}
				}
				else {
					if (is_array($die_mails)) {
						foreach ($die_mails as $dm) {
							$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
						}
					}
				}
				//versenden
				$form_manager_email_ar = explode(";", $form_manager_email);

				foreach ($form_manager_email_ar as $email) {
					if (($this->validateEmail($email))) {
						// Mailen vorbereiten
						$this->mail_it->to = $email;
						$this->mail_it->from = $email;

						IfNotSetNull($this->content->template['plugin']['form_manager']['FORMMANAGER']);
						IfNotSetNull($this->content->template['plugin']['form_manager']['laueft_ab']);

						$this->mail_it->from_text = $this->content->template['plugin']['form_manager']['FORMMANAGER'];
						$this->mail_it->subject = $this->content->template['plugin']['form_manager']['laueft_ab'];
						$this->mail_it->body = $inhalt;

					}
				}
				//Auf versendet setzen
				$sqlu = sprintf("UPDATE %s SET form_manager_lang_mail2_send='1'
				WHERE form_manager_lead_id='%d'",
					$this->cms->tbname['papoo_form_manager_leads'],
					$daten['form_manager_lead_id']
				);
				$this->db->query($sqlu);
			}
		}

		//Dateien löschen
		$sql = sprintf("SELECT * FROM %s
									WHERE form_manager_form_loesch_datum2<'%d'
									AND form_manager_form_loesch_yn!='1'",
			$this->cms->tbname['papoo_form_manager_leads'],
			time()
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		//ALle löschen die Datum überschritten haben und nicht ale nicht LÖschen markiert sind.
		if (is_array($result)) {
			$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_type='file'",
				$this->cms->tbname['papoo_plugin_cform']
			);
			$result_file = $this->db->get_results($sql, ARRAY_A);

			// SQL Query nur einmal ausführen und nach den Fremdschlüsseln gruppieren,
			// um mehrere Zugriffe auf die Datenbank zu vermeiden

			$sql = sprintf("SELECT COUNT(form_manager_content_lead_id_id) FROM %s ",
				$this->cms->tbname['papoo_form_manager_lead_content']
			);
			$tmp = $this->db->get_var($sql);
			$rand = rand(0, ($tmp - 1001));
			if ($rand < 0) {
				$rand = 0;
			}

			$sql = sprintf("SELECT * FROM %s LIMIT %d, 1000",
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$rand
			);
			$tmp = $this->db->get_results($sql, ARRAY_A);

			$result_f = array();
			if (is_array($tmp)) {
				foreach ($tmp as $val) {
					if (isset($result_f[$val['form_manager_content_lead_id_id']])) {
						$result_f[$val['form_manager_content_lead_id_id']] = array();
					}
					$result_f[$val['form_manager_content_lead_id_id']][] = $val;
				}
				unset($tmp);
			}

			$deleteIDs = array();

			foreach ($result as $daten) {
				if (isset($result_f[$daten['form_manager_lead_id']])) {
					foreach ($result_f[$daten['form_manager_lead_id']] as $feld) {
						if (is_array($result_file)) {
							foreach ($result_file as $feldfile) {
								if ($feld['form_manager_content_lead_feld_name'] == $feldfile['plugin_cform_name']) {
									$file = basename($feld['form_manager_content_lead_feld_content']);
									unlink(PAPOO_ABS_PFAD . "/dokumente/files/" . $file);
								}
							}
						}
					}
				}
				$deleteIDs[] = (int)$daten['form_manager_lead_id'];
			}

			if (count($deleteIDs) > 0) {
				//löschen
				$sql = sprintf("DELETE FROM %s
								WHERE form_manager_content_lead_id_id IN (%s)",
					$this->cms->tbname['papoo_form_manager_lead_content'],
					implode(",", $deleteIDs)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * form_manager::check_leads_entry()
	 * Hier werden die Leads gecheckt die generiert wurden
	 *
	 * @return bool
	 */
	function check_leads_entry()
	{
		//Update einbauen - damit es live upgedated werden kann
		require_once(PAPOO_ABS_PFAD . "/plugins/form_manager/lib/form_messages.php");

		//INI Update
		$form_messages = new form_messages();

		$this->get_form_list();

		return true;
	}

	/**
	 * form_manager::formmanager_do_upload()
	 * Hier werden Dateien hochgeladen
	 *
	 * @param string $filename
	 * @return void
	 */
	function formmanager_do_upload($filename = "")
	{
		// Zielverzeichniss
		$destination_dir = 'dokumente/files';

		/**
		 * Wenn Daten in Extra Verzeichnis sollen
		 */
		if ($this->xml_verzeichnis && empty($_SESSION['form_destination_dir'])) {
			$destination_dir = 'dokumente/files/' . sha1(rand(0, time()));
			mkdir($destination_dir, 0755);
			$this->destination_dir = $destination_dir;
			$_SESSION['form_destination_dir'] = $this->destination_dir;
		}
		if (!empty($_SESSION['form_destination_dir'])) {
			$this->destination_dir = $destination_dir = $_SESSION['form_destination_dir'];
		}

		// nicht erlaubte Dateiendungen
		$extensions = array('php', 'php3', 'php4', 'php5', 'php6', 'phtml', 'cgi', 'pl', 'html', 'css', 'js', 'htm');
		// Upload durchführen
		$upload_do = new file_upload(PAPOO_ABS_PFAD);
		if (!is_array($_SESSION['filename'])) {
			$_SESSION['filename'] = array();
		}
		// Wenn Files hochgeladen wurden
		if (count($_FILES) > 0) {
			$upload_do->copy_label = "_";
			$copyMode = 1;
			// Durchführen und falls etwas schief geht
			if (!$upload_do->upload($_FILES[$filename], $destination_dir, $copyMode, $extensions, 1)) {
				// falsch setzen
				$falsch = 1;
				// wenn etwas passiert ist, und ein Error vorliegt
				if (!empty ($upload_do->error)) {
					$falsch_exists = 1;
				}
			}
			else {
				$falsch = 0;
				//Alte Datei löschen
				$_SESSION['filename'][$filename] = basename($_SESSION['filename'][$filename]);
			}
		}
		// #######################################
		// wenn dder Upload geklappt hat, Daten übergeben.
		if (isset($falsch) && $falsch != 1) {
			$this->filename = $upload_do->file['name'];
			$_SESSION['filename'][$filename] = $this->filename;
		}
		else {
			// Meldung zeigen..
			$this->content->template['upload_error'] =
				isset($this->content->template['message_20']) ? $this->content->template['message_20'] . "<br />$upload_do->error" : "$upload_do->error";

		}
	}

	/**
	 * form_manager::make_form_check()
	 *
	 * Formularfelder durch checken
	 *
	 * @return bool
	 */
	function make_form_check()
	{
		$fehler = array();
		$this->insert_ok = true;

		// ALle Felder des Formulars raussuchen, sortiert nach Gruppen...
		$sql = sprintf("SELECT * FROM %s, %s , %s WHERE plugin_cform_form_id='%d'
									AND plugin_cform_lang_id=plugin_cform_id
									AND %s.plugin_cform_group_id=%s.plugin_cform_group_id
									AND plugin_cform_lang_lang='%d'
									ORDER BY plugin_cform_group_order_id ASC, plugin_cform_order_id ASC",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($this->checked->form_manager_id),
			$this->cms->tbname['papoo_plugin_cform'],
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql);

		if (empty($result)) {
			//Dann gibts das Formular nicht
			$this->insert_ok = false;
			$this->blerror = "pfui";
			$this->blok = "no";
			return false;
		}

		if (isset($this->checked->is_last_page) && $this->checked->is_last_page == 1) {
			$this->checked->form_manager_next_page_id = @$this->checked->form_manager_next_page_id + 1;
		}
		$page = 0;
		// Durchgehen
		foreach ($result as $daten) {

			// Name übergeben
			$name = $daten->plugin_cform_name;

			//Hier die Pagebreaks checken
			if ($daten->plugin_cform_type == "pagebreak") {
				$page++;

			}
			//Betreffzeile für Zwischenmail zuweisen
			if ($daten->plugin_cform_type == "mailafterpagebreak") {
				$mail_after_betreff[$page] = $daten->plugin_cform_label;
			}

			//Nur die Felder checken die auch gerade dran sind...
			if ($page != ($this->checked->form_manager_next_page_id - 1)) {
				continue;
			}

			//WEnn in Session und nicht in checked, dann kommt es aus einem vorherigen Schritt
			if (!empty($_SESSION['form_entry'][$this->checked->form_manager_id][$name]) && empty($this->checked->$name)) {
				//Dann Daten eintragen
				$this->checked->$name = $_SESSION['form_entry'][$this->checked->form_manager_id][$name];
			}

			//ZUweisen in SESSION für check
			$_SESSION['form_entry'][$this->checked->form_manager_id][$name] = isset($this->checked->$name) ? $this->checked->$name : NULL;
			// Must?
			if ($daten->plugin_cform_must == 1) {
				if ((empty($this->checked->$name)) && $daten->plugin_cform_type != "file") {
					$this->error[$name] = "error";
					$this->insert_ok = false;
				}

				// Mindestlänge
				if (($daten->plugin_cform_type != "multiselect") && ($daten->plugin_cform_type != 'file')) {
					if (isset($this->checked->$name) && $daten->plugin_cform_minlaeng > strlen($this->checked->$name)) {
						$this->error[$name] = "error";
						$this->insert_ok = false;
					}
				}

				if ($daten->plugin_cform_type == "file") {
					if (empty($_FILES[$name]) || empty($_FILES[$name]['tmp_name'])) {
						//also empty Session files... than its really an error
						if(empty($_SESSION['filename'][$name]))
						{
							$this->error[$name] = "error";
							$this->insert_ok = false;
						}
					}
				}

			}
			// alpha / numerisch?
			if ($daten->plugin_cform_content_type == "num") {
				if ((!is_numeric($this->checked->$name) && !empty($this->checked->$name))) {
					$this->error[$name] = "error";
					$this->insert_ok = false;
				}
			}
			// email
			if ($daten->plugin_cform_type == "email") {
				if (!empty($this->checked->$name)) {
					if ((!$this->validateEmail($this->checked->$name))) {
						$this->error[$name] = "error";
						$this->insert_ok = false;
					}
				}
			}
			if ($daten->plugin_cform_type == "file") {
				//Datei hochladen
				$this->formmanager_do_upload($name);
			}
			if (!empty($this->error[$name])) {
				$fehler[] = $daten->plugin_cform_label;
			}
			if(isset($this->checked->$name)) {
				$this->checked->$name;
			}
			if (isset($this->checked->$name) && $this->blacklist->do_blacklist($this->checked->$name) == "not_ok") {
				$this->blerror = "pfui";
			}
		}

		if (count($fehler) > 0) {
			//Nächste Seite Formular wieder zurücksetzen
			$this->checked->form_manager_next_page_id = $this->checked->form_manager_next_page_id - 1;
		}
		if (isset($this->blerror) && strlen($this->blerror) > 0) {
			$this->blok = "no";
			//Nächste Seite Formular wieder zurücksetzen
			$this->checked->form_manager_next_page_id = $this->checked->form_manager_next_page_id - 1;
		}
		else {
			$this->blok = "ok";
		}

		// Raussuchen, ob Empfänger-E-Mail-Adresse wählbar ist
		$sql = sprintf("SELECT `form_manager_anzeig_select_email` FROM %s
						WHERE `form_manager_id` = %d LIMIT 1",
			$this->cms->tbname['papoo_form_manager'],
			(int)$this->checked->form_manager_id);
		$select_email = (bool)$this->db->get_var($sql);

		// Wenn wählbar, prüfe, dass wirklich eine gewählt wurde
		if ($select_email) {
			if (isset($this->checked->sendto) && is_numeric($this->checked->sendto)) {
			}
			elseif (isset($this->checked->sendto) && is_array($this->checked->sendto) && count($this->checked->sendto) > 0) {
			}
			else {
				$this->error["__sendto__"] = "error";
				$fehler[] = $this->content->template['message']['plugin']['form_manager']['replace']['sendto'];
				$this->insert_ok = false;
				$this->content->template['error']['sendto'] = true;
			}
		}

		// SPAMSCHUTZ PRÜFEN
		global $spamschutz;

		if (empty($this->checked->form_manager_next_page_id)) {
			$this->checked->form_manager_next_page_id = 1;
			$next = "leer";
		}

		if ($this->cms->stamm_kontakt_spamschutz && $spamschutz->is_spam && $page == ($this->checked->form_manager_next_page_id - 1)) {
			if ($this->content->template['module_aktiv']['form_modul'] != 1) {

				$this->error["__spamschutz__"] = "error";
				$fehler[] = $this->content->template['message']['plugin']['form_manager']['replace']['spamschutz'];
				$this->insert_ok = false;
				$this->content->template['error']['spamschutz'] = true;

				//Nächste Seite Formular wieder zurücksetzen
				$this->checked->form_manager_next_page_id = $this->checked->form_manager_next_page_id - 1;
			}
			else {
				if (!empty($this->checked->formname_check)) {
					$this->insert_ok = false;
					$this->content->template['error']['spamschutz'] = true;
				}
			}
		}

		if (isset($next) && $next == "leer") {
			$this->checked->form_manager_next_page_id = "";
		}

		$this->content->template['fehlerliste'] = $fehler;
		if (!is_numeric($this->checked->form_manager_next_page_id)) {
			$this->checked->form_manager_next_page_id=0;
		}
		$mail_id = $this->checked->form_manager_next_page_id - 1;

		if (empty($fehler) && !empty($mail_after_betreff[$mail_id])) {
			//Hier ist klar ob die Seite durchgegangen ist, daher hier dann die Zwischenmail...
			$this->verschick_zwischen_mail($mail_after_betreff[$mail_id]);
		}
	}

	/**
	 * form_manager::html2txt()
	 * HTML und Script aus einem Text herausziehen
	 *
	 * @param string $document
	 * @return
	 */
	function html2txt($document)
	{
		$search = array(
			'@<script[^>]*?>.*?</script>@si', // Strip out javascript
			'@<[\\/\\!]*?[^<>]*?>@si', // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
			'@<![\\s\\S]*?--[ \\t\\n\\r]*>@' // Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);
		return $text;
	}

	/**
	 * content_manipulator::bind_mv()
	 * Die mv Klasse einbinden
	 *
	 * @return void
	 */
	function form_bind_mv()
	{
		global $mv;
		$this->mv = &$mv;
	}

	/**
	 * content_manipulator::show_search_mv()
	 * Hier wird die Suchmaske aus der MV rausgeholt und ausgegeben
	 *
	 * @return void
	 */
	function form_show_search_mv()
	{
		if (is_object($this->mv)) {
			global $db;
			$this->db = &$db;
			$this->db->hide_errors();
			$this->mv->meta_gruppe = 1;
			$this->content->template['mv_template_all'] = "";
			require_once(PAPOO_ABS_PFAD . '/plugins/mv/lib/mv.php');
			$this->mv->show_front();

			$this->mv_daten_array = ($this->content->template['vw_one_item']);
		}
	}

	/**
	 * Ein Formular raussuchen und ausgeben
	 *
	 * @param mixed $formid Formular-ID
	 */
	function front_get_form($formid = "")
	{
		$save_mv_content_id = isset($this->checked->mv_content_id) ? $this->checked->mv_content_id : NULL;
		$save_mv_id = isset($this->checked->mv_id) ? $this->checked->mv_id : NULL;
		$this->checked->mv_content_id = isset($this->checked->flexid) ? $this->checked->flexid : NULL;
		$this->checked->mv_id = isset($this->checked->flex_mv_id) ? $this->checked->flex_mv_id : NULL;
		$this->form_bind_mv();
		$this->form_show_search_mv();
		$this->checked->mv_content_id = $save_mv_content_id;
		$this->checked->mv_id = $save_mv_id;

		// Daten raussuchen
		$this->get_form_group_list($formid);

		// Aus den Daten FOrmular machen
		$this->make_form($this->result_groups);

		// Sprachdaten rausholen
		$sql = sprintf("SELECT * FROM %s WHERE form_manager_id_id='%d' AND form_manager_lang_id='%d'",
			$this->cms->tbname['papoo_form_manager_lang'],
			$this->db->escape($this->checked->form_manager_id),
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!empty($result)) {
			foreach ($result as $daten) {
				$result[0]['form_manager_toptext_html'] = $this->diverse->do_pfadeanpassen("nobr:" . $daten['form_manager_toptext_html']);
				$result[0]['form_manager_toptext_html'] = $this->download->replace_downloadlinks($result[0]['form_manager_toptext_html']);
				$result[0]['form_manager_bottomtext_html'] = $this->diverse->do_pfadeanpassen("nobr:" . $daten['form_manager_bottomtext_html']);
				$result[0]['form_manager_bottomtext_html'] = $this->download->replace_downloadlinks($result[0]['form_manager_bottomtext_html']);
				//Ersetzen der Daten aus dem Formmanger wenn vorhanden
				if (isset($this->mv_daten_array) && is_array($this->mv_daten_array)) {
					foreach ($this->mv_daten_array as $key => $value) {
						$key = str_ireplace("/", "", $key);
						$key = str_ireplace(">", "", $key);

						$result[0]['form_manager_toptext_html'] = str_ireplace('#flex_' . $key . '#', $value,
							$result[0]['form_manager_toptext_html']);

						$result[0]['form_manager_bottomtext_html'] = preg_replace('/#flex_' . $key . '#/', $value,
							$result[0]['form_manager_bottomtext_html']);

					}
				}
			}
			$sql = sprintf("SELECT * FROM %s WHERE form_manager_id='%d'",
				$this->cms->tbname['papoo_form_manager'],
				$this->db->escape($this->checked->form_manager_id)
			);
			$result2 = $this->db->get_results($sql, ARRAY_A);
			if ((!empty($result2))) {
				foreach ($result2 as $daten) {
					$this->content->template['form_manager_name'] = $daten['form_manager_name'];
					if ($daten['form_manager_anzeig_select_email'] == 1) {
						if ($this->select_mail_multiple) {
							$this->content->template['form_versende'] = $this->formmanger_make_versende_selectbox(true);
						} else {
							$this->content->template['form_versende'] = $this->formmanger_make_versende_selectbox();
						}
					}
				}
			}

			$this->content->template['pcfronttext'] = $result;
		}
	}

	/**
	 * form_manager::formmanger_make_versende_selectbox()
	 * Versende Auswahl erstellen
	 * @return void
	 */
	function formmanger_make_versende_selectbox($make_checkboxes = false)
	{
		$feld = "";
		$sql = sprintf("SELECT * FROM %s WHERE form_manager_email_form_id='%d' ORDER BY form_manager_email_id ASC",
			$this->cms->tbname['papoo_form_manager_email'],
			$this->db->escape($this->checked->form_manager_id)
		);
		$result2 = $this->db->get_results($sql, ARRAY_A);

		// Bereits vom Benutzer ausgewählte Empfänger holen
		$checked = array();
		if (!empty($this->checked->sendto)) {
			if (is_numeric($this->checked->sendto)) {
				$checked[] = (int)($this->checked->sendto);
			}
			elseif (is_array($this->checked->sendto)) {
				foreach ($this->checked->sendto as $x) {
					if (is_numeric($x)) {
						$checked[] = (int)($x);
					}
				}
			}
		}

		// Ausgabe bauen
		if (!empty($result2)) {
			foreach ($result2 as $daten) {
				if ($make_checkboxes) {
					$feld .= "<label style='float:none;'><input type='checkbox' name='sendto[]' value='" . $daten['form_manager_email_id'] . "' ";
					if (array_search($daten['form_manager_email_id'], $checked) !== false) {
						$feld .= 'checked="checked" ';
					}
					$feld .= " />&nbsp;&nbsp;" . $daten['form_manager_email_name'] . "</label>";
				}
				else {
					$feld .= "<option value='" . $daten['form_manager_email_id'] . "'";
					if (array_search($daten['form_manager_email_id'], $checked) !== false) {
						$feld .= 'selected="selected" ';
					}
					$feld .= ">&nbsp;&nbsp;" . $daten['form_manager_email_name'] . "&nbsp;&nbsp;&nbsp;</option>";
				}
			}
		}
		if (!$make_checkboxes) {
			return '<select id="sendto" name="sendto" size="1">' . $feld . '</select>';
		}
		else {
			return $feld;
		}
	}

	/**
	 * echtes HTML Form erstelln
	 */
	function make_form($formdata)
	{
		$groupar = $this->result_groups;
		$i = 0;

		// Für diese Gruppen durchgehen
		if ((!empty($this->result_groups))) {
			foreach ($this->result_groups as $group) {
				$this->feldarray = array();
				// Alle Felder der jeweiligen Gruppe rausholen
				$sql = sprintf("SELECT * FROM %s,%s WHERE
										plugin_cform_group_id='%d'
										AND plugin_cform_id=plugin_cform_lang_id
										AND plugin_cform_lang_lang='%d' ORDER BY plugin_cform_order_id ASC",
					$this->cms->tbname['papoo_plugin_cform_lang'],
					$this->cms->tbname['papoo_plugin_cform'],
					$group['plugin_cform_group_id'],
					$this->cms->lang_id
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				if (is_array($result)) {
					foreach ($result as $fld) {
						if(is_string($fld) && empty($this->checked->$fld) == FALSE) {
							// Bösen HTML rausflitschen
							$this->checked->$fld = $this->html2txt($this->checked->$fld);
							// ANführeungszeichen kodieren
							$this->checked->$fld = $this->diverse->encode_quote($this->checked->$fld);
						}
						$this->make_feld_front($fld);
					}
				}

				$groupar[$i]['felder'] = $this->feldarray;
				$i++;
			}
			if (isset($this->mv_daten_array) && is_array($this->mv_daten_array)) {
				$this->make_hidden_feld_mv();
				$groupar[$i - 1]['felder'] = $this->feldarray;
			}
		}
		$groupar[0]['felder'][] =
			'<div style="display:none;" id="formname_check_div"><label for="formname_check">Name:</label><input type="text" size="30" name="name" id="formname_check" value=""/></div>';

		$groupar = $this->check_page_break($groupar);

		// Gruppen können durch Style-Hook noch modifiziert werden
		run_style_hook('onFormManagerFieldsLoaded', [&$groupar]);

		$this->content->template['gfliste'] = $groupar;
	}

	/**
	 * Checked das Formular auf Pagebreaks und setzt diese um...
	 * @param $groupar
	 * @return mixed
	 */
	private function check_page_break($groupar)
	{
		$neu = array();
		$page = 0;
		$return_page = 0;

		//Wenn eine nächste Seite kommt, dann umstellen
		if (is_numeric(@$this->checked->form_manager_next_page_id)) {
			$return_page = $this->checked->form_manager_next_page_id;
		}

		//Continue auf false damit alle Felder bis zum nä. Pagebreak durchlaufen...
		$continue = false;

		//Fieldsets durchgehen
		if (is_array($groupar)) {
			foreach ($groupar as $k1 => $v1) {
				//Felder und Gruppierung je Page aufbohren...
				$neu[$page][$k1] = $v1;
				$neu[$page][$k1]['felder'] = array();

				//WEnn Felder...
				if (is_array($v1['felder'])) {
					foreach ($v1['felder'] as $k => $v) {
						//Pagebreak erreicht, daher alle Felder danach in dieser Gruppierung ignorieren
						if ($continue) {
							continue;
						}

						if (stristr($v, "pagebreak") && stristr($v, "mail")) {
							continue;
						}

						//Wenn man am Pagebreak vorbei kommt, die Page erhöhen
						if (stristr($v, "pagebreak") && !stristr($v, "mail")) {
							//Seite hochzählen
							$page++;
							if (empty($this->checked->form_manager_next_page_id)) {
								$hidden_field = ' <input type="hidden" name="form_manager_next_page_id" value="'
									. ($page + @$this->checked->form_manager_next_page_id)
									. '" />';
							}
							else {
								$hidden_field = ' <input type="hidden" name="form_manager_next_page_id" value="'
									. ($page)
									. '" />';
							}

							//Hier noch zwischenbutton zuweisen und aus dem Feld array rausnehmen...
							$this->content->template['submit_page_break'][$page] = $v;
							$this->content->template['submit_page_break_hidden'][$page] = $hidden_field;

							if (empty($this->checked->form_manager_next_page_id)) {
								$this->content->template['page_count_form'] =
									@$this->checked->form_manager_next_page_id + 1;
							}
							else {
								$this->content->template['page_count_form'] =
									$this->checked->form_manager_next_page_id + 1;
							}

							$continue = true;
							continue;
						}
						//Feld zuweisen
						$neu[$page][$k1]['felder'][$k] = $v;
					}
				}
				//Continue auf false damit alle Felder bis zum nä. Pagebreak durchlaufen...
				$continue = false;
			}
		}
		//Hier das ist die letzte Seite wenn beide Werte gleich sind.
		if ($page == $return_page) {
			$this->content->template['page_count_form'] = $this->content->template['page_count_form'] - 1;
			$this->content->template['is_last_page_form'] = "ok";
		}
		return $neu[$return_page];
	}

	/**
	 * Feld HTML erzeugen
	 *
	 * @param string $feld
	 */
	function make_feld_front($feld = "")
	{
		$template = [
			'text' => 'fields/text',
			'email' => 'fields/text',
			'check' => 'fields/checkbox',
			'select' => 'fields/select',
			'textarea' => 'fields/textarea',
		][$feld['plugin_cform_type']] ?? '';

		if ($template = self::findTemplate($template)) {
			/** @var content_class $content */
			global $content;
			/** @var Smarty $smarty */
			global $smarty;

			$userValue = trim($this->checked->{$feld['plugin_cform_name']} ?? '') ?: null;

			// Template-Variablen definieren
			$smarty->assign([
				'field' => $feld,
				'options' => array_map(function ($option) use ($userValue) {
					return [
						'value' => $option,
						'selected' => $option == $userValue,
					];
				}, array_filter(preg_split('~\s*(\r\n|\r|\n)+\s*~', trim($feld['plugin_cform_content_list'])))),
				'checked' => (bool)$userValue,
				'value' => $userValue,
				'error' => ($this->error[$feld['plugin_cform_name']] ?? null) == 'error',
			]);

			// Eingabefeld rendern
			$this->feldarray[] = 'nobr:'.$smarty->fetch($template);

			// Template-Variablen wiederherstellen
			$content->assign();
			return;
		}

		switch ($feld['plugin_cform_type']) {
		case "text" :
			$this->make_input_feld($feld);
			break;

		case "textarea" :
			$this->make_textarea_feld($feld);
			break;

		case "email" :
			$this->make_input_feld($feld);
			break;

		case "radio" :
			$this->make_radio_feld($feld);
			break;

		case "hidden" :
			$this->make_hidden_feld($feld);
			break;

		case "file" :
			$this->make_file_feld($feld);
			break;

		case "select" :
			$this->make_select_feld($feld);
			break;

		case "multiselect" :
			$this->make_multi_select_feld($feld);
			break;

		case "check" :
			$this->make_check_feld($feld);
			break;

		case "check_replace" :
			$this->make_check_replace_feld($feld);
			break;

		case "datum" :
			$this->make_datum_feld($feld);
			break;

		case "remark":
			$this->make_remark_feld($feld);
			break;

		case "pagebreak":
			$this->make_pagebreak_feld($feld);
			break;

		case "mailafterpagebreak":
			$this->make_mailafterpagebreak_feld($feld);
			break;
		}

	}

	public function make_pagebreak_feld($feld)
	{
		$cfeld = '<legend>' . $feld['plugin_cform_label'] . '</legend>';
		$cfeld .= '<input type="submit" name="form_manager_submit" class="senden_pagebr_' . $feld['plugin_cform_id'] . '
        pagebreak btn btn-info"
        value="' . $feld['plugin_cform_label'] . '" />';
		$this->feldarray[] = $cfeld;
	}

	public function make_mailafterpagebreak_feld()
	{
		$cfeld = "MAIL AFTER Pagebreak";
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Ein Datei Upload Feld erzeugen
	 */
	function make_file_feld($feld)
	{
		$cfeld = '<div id="labdiv_' . $feld['plugin_cform_name'] . '" class="labdiv"><label for="plugin_cform_' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld .= $popup_info_img;
		}
		$cfeld .= '</label>';

		$cfeld .= '<input type="file" size="';
		if ((!empty($feld['plugin_cform_size']))) {
			$cfeld .= $feld['plugin_cform_size'];
		}
		else {
			$cfeld .= "20";
		}
		$cfeld .= '" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="';
		// OPtions eintragen
		$cfeld .= isset($this->checked->{$feld['plugin_cform_name']}) ? $this->diverse->encode_quote($this->checked->{$feld['plugin_cform_name']}) : NULL;
		$cfeld .= '"/>';
		$cfeld .= '</div>';
		if (!empty($_SESSION['filename'])) {
			$cfeld .= '<div class="labdiv">';
			$cfeld .= $feld['plugin_cform_label'] . ':&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$cfeld .= $this->html2txt($_SESSION['filename'][$feld['plugin_cform_name']]);
			$cfeld .= '</div>';
		}
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Radio Buttons erzeugen
	 */
	function make_radio_feld($feld)
	{
		$cfeld = "";
		$drin = isset($this->checked->{$feld['plugin_cform_name']}) ? $this->checked->{$feld['plugin_cform_name']} : NULL;
		if (!empty($feld['plugin_cform_content_list'])) {
			$cdaten = explode("\n", $feld['plugin_cform_content_list']);
			// Einträge durchgehen

			$cfeld .= '<span class="radioLabel">' . $feld['plugin_cform_label'];

			if ($feld['plugin_cform_must'] == 1) {
				$cfeld .= '<span class="must_stern"> *</span>';
			}

			if (!empty($feld['plugin_cform_tooltip'])) {
				$cfeld .= $this->createTooltip($feld['plugin_cform_tooltip']);
			}

			$cfeld .= '<br /></span>';

			if ((is_array($cdaten))) {
				foreach ($cdaten as $daten) {
					$daten = trim($daten);
					$daten_trimmed = preg_replace("/[^A-Za-z0-9]/", "", $daten);
					$cfeld .= '<div  id="labdiv_' . $feld['plugin_cform_name'] . '_' . $daten_trimmed . '" class="labdiv labdiv_radio">';
					$cfeld .= '<input type="radio" name="' . $feld['plugin_cform_name'] . '" id="plugin_cform_' . $feld['plugin_cform_name'] . '_' .
						$daten_trimmed . '" value="' . $daten . '" ';
					// echo $drin."=".$daten."<br />";
					if ($drin == $daten) {
						// echo $daten;
						$cfeld .= ' checked="checked"';
					}
					$cfeld .= '/>';
					$cfeld .= '<label for="plugin_cform_' . $feld['plugin_cform_name'] . '_' . $daten_trimmed . '"';
					// Wenn ein Fehler besteht
					if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
						if ((!empty($feld['plugin_cform_descrip']))) {
							$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
						} else {
							$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
						}
					} else {
						$cfeld .= '>';
					}

					$cfeld .= $daten . '';
					if (!empty($feld['plugin_cform_label_img'])) {
						$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] .
							'" alt="" title="' . $feld['plugin_cform_label'] . '" />';
					}
					$cfeld .= '</label>';

					$cfeld .= '</div>';
				}
			}
		}
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Hidden fields erzeugen
	 */
	function make_hidden_feld($feld)
	{
		$cfeld = "";

		$cfeld .= '<input type="hidden" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="';
		// OPtions eintragen
		$cfeld .= htmlspecialchars(trim($this->checked->{$feld['plugin_cform_name']} ?? $feld['plugin_cform_content_list']));

		$cfeld .= '"/>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Hidden fields erzeugen
	 */
	function make_hidden_feld_mv()
	{
		$cfeld = '<input type="hidden" name="flexid" id="flexid" value="';
		// OPtions eintragen
		$cfeld .= $this->checked->flexid;
		$cfeld .= '"/>';
		$cfeld .= '<input type="hidden" name="flex_mv_id" id="flex_mv_id" value="';
		// OPtions eintragen
		$cfeld .= $this->checked->flex_mv_id;
		$cfeld .= '"/>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Select Boxen erzeugen
	 */
	function make_select_feld($feld)
	{
		$cfeld = '<div class="labdiv labdiv_select" id="labdiv_' . $feld['plugin_cform_name'] . '"><label for="plugin_cform' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			} else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		} else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';

		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (isset($this->showbrs) && $this->showbrs == 1) {
			$cfeld .= '';
		}

		$cfeld .= '<select name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" size="1" >';
		// OPtions eintragen
		if (!empty($feld['plugin_cform_content_list'])) {
			$cdaten = explode("\n", $feld['plugin_cform_content_list']);
			// Einträge durchgehen
			if ((is_array($cdaten))) {
				foreach ($cdaten as $daten) {
					$daten = trim($daten);
					$cfeld .= '<option value="' . $this->diverse->encode_quote($daten);
					// Wenn ausgewählt aus redo
					if (isset($this->checked->{$feld['plugin_cform_name']}) && $this->checked->{$feld['plugin_cform_name']} == $daten) {
						$cfeld .= '" selected="selected"';
					}
					else {
						$cfeld .= '"';
					}
					$cfeld .= '>' . $daten . '</option>';
				}
			}
		}
		$cfeld .= '</select>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		// Daten übergeben
		$this->feldarray[] = $cfeld;
	}

	function make_multi_select_feld($feld)
	{
		$cfeld = '<div class="labdiv labdiv_multi" id="labdiv_' . $feld['plugin_cform_name'] . '"><label for="plugin_cform' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (isset($this->showbrs) && $this->showbrs == 1) {
			$cfeld .= '';
		}

		$cfeld .= '<select name="' . $feld['plugin_cform_name'] . '[]" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" size="5" multiple="multiple">';
		// OPtions eintragen
		if (!empty($feld['plugin_cform_content_list'])) {
			$cdaten = explode("\n", $feld['plugin_cform_content_list']);
			// Einträge durchgehen
			if ((is_array($cdaten))) {
				foreach ($cdaten as $keyd => $daten) {
					$daten = trim($daten);
					$cfeld .= '<option value="' . $this->diverse->encode_quote($daten);
					// Wenn ausgewählt aus redo
					$array_dat1 = isset($this->checked->{$feld['plugin_cform_name']}) ? $this->checked->{$feld['plugin_cform_name']} : NULL;
					if (!is_array($array_dat1)) {
						$array_dat1 = array();
					}

					if (in_array($daten, $array_dat1)) {
						$cfeld .= '" selected="selected"';
					}
					else {
						$cfeld .= '"';
					}
					$cfeld .= '>' . $daten . '</option>';
				}
			}
		}
		$cfeld .= '</select>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Check Bixen erzeugen
	 */
	function make_check_feld($feld)
	{
		$cfeld = '<div class="labdiv labdiv2 labdiv_checkbox" id="labdiv_' . $feld['plugin_cform_name'] . '"><input type="checkbox" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="1" ';
		if (isset($this->checked->{$feld['plugin_cform_name']}) && $this->checked->{$feld['plugin_cform_name']} == "1") {
			$cfeld .= ' checked="checked"';
		}
		$cfeld .= '/>';
		$cfeld .= '<label  id="plugin_cform_' . $feld['plugin_cform_name'] . '_label" for="plugin_cform_' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * @param $feld
	 * Checked Feld mit Ersetzungstext vor dem | ausgeben im FOrmular
	 */
	function make_check_replace_feld($feld)
	{
		$label_ar1 = explode("|", $feld['plugin_cform_label']);
		$feld['plugin_cform_label'] = trim($label_ar1['0']);

		$cfeld = '<div class="labdiv labdiv2 labdiv_check_replace" id="labdiv_' . $feld['plugin_cform_name'] . '"><input type="checkbox" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="1" ';
		if (isset($this->checked->{$feld['plugin_cform_name']}) && $this->checked->{$feld['plugin_cform_name']} == "1") {
			$cfeld .= ' checked="checked"';
		}
		$cfeld .= '/>';
		$cfeld .= '<label  id="plugin_cform_' . $feld['plugin_cform_name'] . '_label" for="plugin_cform_' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Ein Textinuput Feld erzeugen
	 */
	function make_input_feld($feld)
	{
		$cfeld = '<div class="labdiv labdiv_text"  id="labdiv_' . $feld['plugin_cform_name'] . '"><label for="plugin_cform_' . $feld['plugin_cform_name'] . '"';

		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1 && $feld['plugin_cform_id'] != 22) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (isset($this->showbrs) && $this->showbrs == 1) {
			$cfeld .= '';
		}
		$cfeld .= '<input type="text" size="';
		if ((!empty($feld['plugin_cform_size']))) {
			$cfeld .= $feld['plugin_cform_size'];
		}
		else {
			$cfeld .= "30";
		}
		$cfeld .= '" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="';
		if (empty($this->checked->{$feld['plugin_cform_name']})) {
			$cfeld .= $feld['plugin_cform_content_list'];
		}
		// OPtions eintragen
		$cfeld .= isset($this->checked->{$feld['plugin_cform_name']}) ? $this->diverse->encode_quote($this->checked->{$feld['plugin_cform_name']}) : NULL;
		$cfeld .= '"/>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Ein Textinput-Feld erzeugen
	 */
	function make_datum_feld($feld)
	{
		if (isset($this->is_datepicker) && $this->is_datepicker != 1) {
			$this->content->template['plugin_header'][] = '<script type="text/javascript" src="' . PAPOO_WEB_PFAD . '/plugins/form_manager/js/jquery.ui.js"></script>';

			$this->content->template['plugin_header'][] = '<link rel="stylesheet" href="' . PAPOO_WEB_PFAD . '/plugins/form_manager/css/jquery_ui.css" type="text/css" media="screen"  charset="utf-8">';

			$this->is_datepicker = 1;
		}
		$cfeld = '<div class="labdiv"  id="labdiv_' . $feld['plugin_cform_name'] . '"><label for="plugin_cform_' . $feld['plugin_cform_name'] . '"';

		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			}
			else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		}
		else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (isset($this->showbrs) && $this->showbrs == 1) {
			$cfeld .= '';
		}
		$cfeld .= '<input type="date" size="';
		if ((!empty($feld['plugin_cform_size']))) {
			$cfeld .= $feld['plugin_cform_size'];
		}
		else {
			$cfeld .= "30";
		}
		$cfeld .= '" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" value="';
		// OPtions eintragen
		$cfeld .= isset($this->checked->{$feld['plugin_cform_name']}) ? $this->diverse->encode_quote($this->checked->{$feld['plugin_cform_name']}) : NULL;
		$cfeld .= '"/>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$popup_info_img = $this->createTooltip($feld['plugin_cform_tooltip']);
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Hinweis-Feld erzeugen
	 */
	function make_remark_feld($feld)
	{
		$cfeld = '<div class="remark" id="labdiv_' . $feld['plugin_cform_name'] . '">';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$cfeld .= $feld['plugin_cform_tooltip'];
		}
		else {
			$cfeld .= $feld['plugin_cform_label'];
		}

		$cfeld .= '</div>';

		$this->feldarray[] = $cfeld;
	}

	/**
	 * Validates an email address
	 *
	 * @param string $address email address
	 * @return bool true if mail address is valid
	 * @access public
	 */
	function validateEmail($address)
	{
		return preg_match('/^[_a-z0-9+~-]+(\.[_a-z0-9+!#$%&*=?|^~-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i', $address);
	}

	/**
	 * Ein Textarea Feld erzeugen
	 */
	function make_textarea_feld($feld)
	{
		$cfeld = "nobr:";
		$cfeld .= '<div class="labdiv labdiv_textarea" id="labdiv_' . $feld['plugin_cform_name'] . '"><label for="plugin_cform_' . $feld['plugin_cform_name'] . '"';
		// Wenn ein Fehler besteht
		if (isset($this->error[$feld['plugin_cform_name']]) && $this->error[$feld['plugin_cform_name']] == "error") {
			if ((!empty($feld['plugin_cform_descrip']))) {
				$cfeld .= '  class="form_error" >' . $feld['plugin_cform_descrip'] . ' ';
			} else {
				$cfeld .= '  class="form_error" >' . $this->content->template['plugin']['form_manager']['fehlermeldung'] . ' ';
			}
		} else {
			$cfeld .= '>';
		}

		$cfeld .= $feld['plugin_cform_label'] . '';
		if ($feld['plugin_cform_must'] == 1) {
			$cfeld .= '<span class="must_stern"> *</span> ';
		}
		if (!empty($feld['plugin_cform_label_img'])) {
			$cfeld .= '<br /><img class="form_labelimg" src="' . PAPOO_WEB_PFAD . '/images/' . $feld['plugin_cform_label_img'] . '" alt="" title="' . $feld['plugin_cform_label'] . '" />';
		}
		$cfeld .= '</label>';
		if (isset($this->showbrs) && $this->showbrs == 1) {
			$cfeld .= '';
		}
		$cfeld .= '<textarea cols="40" rows="6" name="' . $feld['plugin_cform_name'] . '" ';
		$cfeld .= 'id="plugin_cform_' . $feld['plugin_cform_name'] . '" >';
		// OPtions eintragen
		$cfeld .= isset($this->checked->{$feld['plugin_cform_name']}) ? $this->diverse->encode_quote($this->checked->{$feld['plugin_cform_name']}) : NULL;
		$cfeld .= '</textarea>';
		if (!empty($feld['plugin_cform_tooltip'])) {
			$feld['plugin_cform_tooltip'] = nl2br($feld['plugin_cform_tooltip']);
			$feld['plugin_cform_tooltip'] = str_ireplace("\n", "", $feld['plugin_cform_tooltip']);
			$feld['plugin_cform_tooltip'] = str_ireplace("\r", "", $feld['plugin_cform_tooltip']);
			$popup_info_img = '<img class="descrip_img" title="';
			$popup_info_img .= $this->diverse->encode_quote($feld['plugin_cform_tooltip']);
			$popup_info_img .= '" alt="info" src="' . PAPOO_WEB_PFAD . '/plugins/form_manager/bilder/info.gif" height="16" width="16" />';
			$cfeld = str_replace('</label>', $popup_info_img . '</label>', $cfeld);
		}
		$cfeld .= '</div>';
		// Daten übergeben
		$this->feldarray[] = $cfeld;
	}

	/**
	 * Daten einer Fieldsetgruppe rausholen
	 */
	function get_groupdata()
	{
		// NOrmale Daten
		$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_id='%d'",
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($this->checked->grupid)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['dat'] = $result;
	}

	/**
	 * *Eine Gruppe löschen
	 */
	function del_group()
	{
		// Standardaten löschen
		$sql = sprintf("DELETE FROM %s WHERE plugin_cform_group_id='%d'",
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($this->checked->plugin_cform_group_id)
		);
		$this->db->query($sql);
		// SPrachdateien löschen
		$sql = sprintf("DELETE FROM %s WHERE plugin_cform_group_lang_id='%d'",
			$this->cms->tbname['papoo_plugin_cform_group_lang'],
			$this->db->escape($this->checked->plugin_cform_group_id)
		);
		$this->db->query($sql);
		// Felder der Gruppe auf nogroup setzen
		$sql = sprintf("UPDATE %s SET plugin_cform_no_group='1' WHERE plugin_cform_group_id='%d'",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->plugin_cform_group_id)
		);
		$this->db->query($sql);
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
		$this->reload($location_url);
	}

	/**
	 * Gruppen bearbeiten
	 */
	function change_group()
	{
		if (!empty($this->checked->del_group)) {
			$this->del_group();
		}

		if (empty($this->checked->submit_group)) {
			$this->content->template['groupedit'] = "ok";
			// Sprachen zuweisen
			$this->make_lang_group();
			// Daten rausholen und zuweisen
			$this->get_groupdata();
		} else {
			// Wenn ok, eintragen
			$this->insup_new_group("update");
			// Neu laden
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
			$this->reload($location_url);
		}
	}

	/**
	 * Neues gruppen erstellen
	 */
	function create_new_group()
	{
		if (empty($this->checked->submit_group)) {
			$this->content->template['groupedit'] = "ok";
			// Sprachen zuweisen
			$this->make_lang_group();
		} else {
			// Wenn ok, eintragen
			$this->insup_new_group();
			// Neu laden
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
			$this->reload($location_url);
		}
	}

	/**
	 * Daten aus Post zurückgeben
	 */
	function do_it_again()
	{
		// Alle Post daten erneut ins TEmplate
		foreach ($_POST as $key => $value) {
			// Bösen HTML rausflitschen
			$value = $this->html2txt($value);
			// ANführeungszeichen kodieren
			$value = $this->diverse->encode_quote($value);
			$this->content->template[$key] = $value;
			$this->content->template['fdat'][0][$key] = $value;
		}
	}

	/**
	 * Gruppendaten aktualisieren
	 */
	function insup_new_group($modus = "insert")
	{
		if ($modus == "insert") {
			$ins1 = "INSERT INTO";
			$ins2 = "INSERT INTO";
			$up1 = "";
			$up2 = "";
		}
		if ($modus == "update") {
			$ins1 = "UPDATE";
			$up1 = "WHERE plugin_cform_group_id=" . $this->db->escape($this->checked->plugin_cform_group_id);
			$ins2 = "INSERT INTO";
			$insertid = $this->db->escape($this->checked->plugin_cform_group_id);
		}
		// generelle Daten eintragen
		$sql = sprintf("%s %s SET
					plugin_cform_group_form_id='%d',
					plugin_cform_group_name='%s' %s",
			$ins1,
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($this->checked->form_manager_id),
			$this->db->escape($this->checked->plugin_cform_group_name),
			$up1
		);
		$this->db->query($sql);
		// Neu sortieren
		$this->reorder_groups($this->db->escape($this->checked->form_manager_id));

		if ((empty($insertid))) {
			$insertid = $this->db->insert_id;
		}
		// Wenn Update alte Sprachdateien löschen
		if ($modus == "update") {
			$sql = sprintf("DELETE FROM %s WHERE plugin_cform_group_lang_id='%d'",
				$this->cms->tbname['papoo_plugin_cform_group_lang'],
				$insertid
			);
			$this->db->query($sql);
		}
		// Sprachdaten eintragen
		if (!empty($this->checked->plugin_cform_group_text)) {
			foreach ($this->checked->plugin_cform_group_text as $key => $value) {
				$sql = sprintf("INSERT INTO %s
																				SET
																				plugin_cform_group_lang_id='%s',
																				plugin_cform_group_text='%s',
																				plugin_cform_group_lang_lang='%s'",
					$this->cms->tbname['papoo_plugin_cform_group_lang'],
					$this->db->escape($insertid),
					$this->db->escape($this->checked->plugin_cform_group_text[$key]),
					$this->db->escape($key)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Liste der Gruppen
	 */
	function get_form_group_list($formid = "")
	{
		if (empty($formid)) {
			$formid = $this->checked->form_manager_id;
		}

		// Gruppen auslesen
		$sql = sprintf("SELECT DISTINCT * FROM %s,%s WHERE
						plugin_cform_group_form_id='%d'
						AND
						plugin_cform_group_lang_id=plugin_cform_group_id
						AND plugin_cform_group_lang_lang='%d' ORDER BY plugin_cform_group_order_id ASC",
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->cms->tbname['papoo_plugin_cform_group_lang'],
			$this->db->escape($formid),
			$this->cms->lang_id
		);

		$result_groups = $this->db->get_results($sql, ARRAY_A);
		// Wenn eine exitiert dann können auch Felder angelegt werden
		if (count($result_groups) > 0) {
			$this->content->template['gruppeok'] = "ok";
		}
		// Ans Template
		$this->content->template['glist'] = $result_groups;
		// Für interne Weiterverarbeitung
		$this->result_groups = $result_groups;
	}

	/**
	 * Typen zuweisen in ein Array
	 */
	function cform_make_typ()
	{
		$typ_array[0] = array("type" => "text", "name" => "Text");
		$typ_array[1] = array("type" => "textarea", "name" => "Textarea");
		$typ_array[2] = array("type" => "email", "name" => "E-Mail");
		$typ_array[3] = array("type" => "select", "name" => "Selectbox");
		$typ_array[4] = array("type" => "radio", "name" => "Radiobutton");
		$typ_array[5] = array("type" => "check", "name" => "Checkbox");
		$typ_array[13] = array("type" => "check_replace", "name" => "Checkbox | Replace");
		$typ_array[7] = array("type" => "file", "name" => "Datei-Upload");
		$typ_array[6] = array("type" => "hidden", "name" => "Versteckt");
		$typ_array[8] = array("type" => "datum", "name" => "Datum / Datepicker JS");
		$typ_array[9] = array("type" => "remark", "name" => "Hinweis");
		$typ_array[10] = array("type" => "multiselect", "name" => "Multiselect-Box");
		$typ_array[11] = array(
			"type" => "mailafterpagebreak",
			"name" => "Beim Seitenumbruch Mail versenden (vor
        Umbruch einsortieren!) - Label  = Betreff der Mail"
		);
		$typ_array[12] = array(
			"type" => "pagebreak",
			"name" => "Seiten Umbruch im Formular - immer ans Ende des
        Fieldsets setzen"
		);

		$this->content->template['result_cat'] = $typ_array;
	}

	/**
	 * Typen zuweisen in ein Array
	 */
	function cform_make_content_typ()
	{
		$typ_array[1] = array("type" => "alpha", "name" => "alphabetisch (Standard)");
		$typ_array[0] = array("type" => "num", "name" => "numerisch");
		$this->content->template['result_cat_ctyp'] = $typ_array;
	}

	/**
	 * Daten der Felder checken
	 * Wenn neue eingetragen wurden oder
	 * alte geändert
	 */
	function check_data_fields()
	{
		$this->error = "";
		// Name
		if ((empty($this->checked->plugin_cform_name))) {
			$this->content->template['ffname'] = "error";
			$this->error .= "Name;";
		} else {
			$this->checked->plugin_cform_name = trim($this->checked->plugin_cform_name);
		}
		// Labels checken nach Sprache
		foreach ($this->checked->plugin_cform_label as $key => $value) {
			if (empty($value)) {
				$this->content->template['fflabel'][$key] = "error";
				$this->error .= "Label;" . $key;
			}
		}
		// Wenn radio oder checkbox oder multiselect
		if ($this->checked->plugin_cform_type == "radio" or $this->checked->plugin_cform_type == "select" or $this->checked->plugin_cform_type == "multiselect") {
			// Dann muß mindestens ein Eintrag drin sein
			foreach ($this->checked->plugin_cform_content_list as $key => $value) {
				if (empty($value)) {
					$this->content->template['ffctype'][$key] = "error";
					$this->error .= "Content Liste;" . $key;
				}
			}
		}
	}

	/**
	 * Feld Formular ertellen
	 */
	function make_feld_form()
	{
		$this->content->template['fieldedit'] = "ok";
		// Sprachen zuweisen
		$this->make_lang_field();
		// Typen zuweisen
		$this->cform_make_typ();
		// Content Typen zuweisen
		$this->cform_make_content_typ();
		// Gruppierungsliste aussuchen
		$this->get_form_group_list();
	}

	/**
	 * Neues Feld erstellen
	 */
	function create_new_field()
	{
		$this->get_image_list();

		if (empty($this->checked->submit_field)) {
			// Feld Formular erstellenb
			$this->make_feld_form();
		} else {
			// Daten checken
			$this->check_data_fields();
			// WEnn ok dann eintragen
			if (empty($this->error)) {
				$this->insup_new_field();
				// Neu laden
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
				$this->reload($location_url);
			} else {
				// Feld Formular erstellenb
				$this->make_feld_form();
				// Daten erneut ausgeben
				$this->do_it_again();
				// Template Error
				$this->content->template['fehler'] = "ok";
			}
		}
	}

	/**
	 * Felder neu ordnen
	 */
	function reorder_fields($groupid = "0")
	{
		// Felder der Gruppe rausholen
		$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_id='%d' ORDER BY plugin_cform_order_id ASC",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($groupid)
		);
		$result = $this->db->get_results($sql);
		$i = 10;
		// Durchzählen und neu einsortieren
		if ((!empty($result))) {
			foreach ($result as $dat) {
				$sql = sprintf("UPDATE %s SET plugin_cform_order_id='%d' WHERE plugin_cform_id='%d'",
					$this->cms->tbname['papoo_plugin_cform'],
					$i,
					$this->db->escape($dat->plugin_cform_id)
				);
				$this->db->query($sql);
				$i = $i + 10;
			}
		}
	}

	/**
	 * Felder neu ordnen
	 */
	function reorder_groups($formid = "0")
	{
		// Felder der Gruppe rausholen
		$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_form_id='%d' ORDER BY plugin_cform_group_order_id ASC",
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($formid)
		);
		$result = $this->db->get_results($sql);
		$i = 1;
		// Durchzählen und neu einsortieren
		if ((!empty($result))) {
			foreach ($result as $dat) {
				$sql = sprintf("UPDATE %s SET plugin_cform_group_order_id='%d' WHERE plugin_cform_group_id='%d'",
					$this->cms->tbname['papoo_plugin_cform_group'],
					$i,
					$this->db->escape($dat->plugin_cform_group_id)
				);
				$this->db->query($sql);
				$i++;
			}
		}
	}

	/**
	 * Spracheinträge für Felder erstellen
	 */
	function make_lang_field()
	{
		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . " WHERE more_lang = '2'  ");
		$this->content->template['language_form'] = array();
		$this->schleife = "notok";
		// zuweisen welche Sprache ausgewählt sind
		foreach ($resultlang as $rowlang) {
			// chcken wenn Sprache gewählt
			$selected_more = 'nodecode:checked="checked"';
			if (isset($this->checked->feldid) && (is_numeric($this->checked->feldid) && empty($this->checked->plugin_cform_label) or $this->schleife == "ok")) {
				$sql = sprintf("SELECT * FROM %s WHERE
											plugin_cform_lang_id='%d'
											AND plugin_cform_lang_lang='%d'",
					$this->cms->tbname['papoo_plugin_cform_lang'],
					$this->db->escape($this->checked->feldid),
					$this->db->escape($rowlang->lang_id)
				);
				$resdat = $this->db->get_results($sql);

				$this->checked->plugin_cform_label[$rowlang->lang_id] = $resdat[0]->plugin_cform_label;
				$this->checked->plugin_cform_tooltip[$rowlang->lang_id] = $resdat[0]->plugin_cform_tooltip;
				$this->checked->plugin_cform_content_list[$rowlang->lang_id] = $resdat[0]->plugin_cform_content_list;
				$this->checked->plugin_cform_descrip[$rowlang->lang_id] = $resdat[0]->plugin_cform_descrip;
				$this->schleife = "ok";
			}
			IfNotSetNull($this->checked->plugin_cform_label[$rowlang->lang_id]);
			IfNotSetNull($this->checked->plugin_cform_descrip[$rowlang->lang_id]);
			IfNotSetNull($this->checked->plugin_cform_tooltip[$rowlang->lang_id]);
			IfNotSetNull($this->checked->plugin_cform_content_list[$rowlang->lang_id]);

			array_push($this->content->template['language_form'], array(
				'language' => $rowlang->lang_long,
				'lang_id' => $rowlang->lang_id,
				'selected' => $selected_more,
				'plugin_cform_label' => $this->diverse->encode_quote($this->checked->plugin_cform_label[$rowlang->lang_id]),
				'plugin_cform_descrip' => $this->diverse->encode_quote($this->checked->plugin_cform_descrip[$rowlang->lang_id]),
				'plugin_cform_tooltip' => "nobr:" . $this->diverse->encode_quote($this->checked->plugin_cform_tooltip[$rowlang->lang_id]),
				'plugin_cform_content_list' => "nobr:" . $this->checked->plugin_cform_content_list[$rowlang->lang_id]
			));
		}
	}

	/**
	 * Felderdaten in die Datenbank eintragen
	 */

	function insup_new_field($modus = "insert")
	{
		// Bilddaten hochladen
		if (!empty ($_FILES['strFile'])) {
			if (empty ($this->checked->paket_logo2)) {
				$this->intern_image->upload_picture();

				IfNotSetNull($this->content->template['image_name']);
				IfNotSetNull($this->content->template['image_breite']);
				IfNotSetNull($this->content->template['image_hoehe']);
				IfNotSetNull($this->content->template['image_gruppe']);
				IfNotSetNull($this->checked->plugin_cform_name);

				$this->checked->image_name = $this->content->template['image_name'];
				$this->checked->image_breite = $this->content->template['image_breite'];
				$this->checked->image_hoehe = $this->content->template['image_hoehe'];
				$this->checked->gruppe = $this->content->template['image_gruppe'];
				$this->checked->texte[1]['lang_id'] = 1;
				$this->checked->texte[1]['alt'] = $this->checked->plugin_cform_name;
				$this->checked->texte[1]['title'] = '';
				$this->checked->texte[1]['long_desc'] = '';
				$this->checked->image_dir = "0";

				if (!empty($this->checked->image_name)) {
					$this->intern_image->upload_save('no');
				}
			} else {
				$this->checked->image_name = $this->checked->paket_logo2;
			}
		} else {
			$this->checked->image_name = $this->checked->paket_logo2;
		}
		if ($modus == "insert") {
			$ins1 = "INSERT INTO";
			$ins2 = "INSERT INTO";
			$up1 = "";
			$up2 = "";
		}
		if ($modus == "update") {
			$ins1 = "UPDATE";
			$up1 = "WHERE plugin_cform_id=" . $this->db->escape($this->checked->plugin_cform_id);
			$ins2 = "INSERT INTO";
			$insertid = $this->db->escape($this->checked->plugin_cform_id);
		}

		IfNotSetNull($this->checked->plugin_cform_must);

		$this->checked->plugin_cform_name = $this->menu->urlencode($this->checked->plugin_cform_name, false);
		$this->checked->plugin_cform_name = str_replace("-", "_", $this->checked->plugin_cform_name);
		// Normale Daten eintragen
		$sql = sprintf("%s %s SET
								plugin_cform_group_id='%d',
								plugin_cform_name='%s',
								plugin_cform_label_img='%s',
								plugin_cform_no_group='0',
								plugin_cform_must='%d',
								plugin_cform_size='%d',
								plugin_cform_form_id='%d',
								plugin_cform_type='%s',
								plugin_cform_content_type='%s',
								plugin_cform_minlaeng='%d' %s
								",
			$ins1,
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->plugin_cform_group_id),
			$this->db->escape($this->checked->plugin_cform_name),
			$this->db->escape($this->checked->image_name),
			$this->db->escape($this->checked->plugin_cform_must),
			$this->db->escape($this->checked->plugin_cform_size),
			$this->db->escape($this->checked->form_manager_id),
			$this->db->escape($this->checked->plugin_cform_type),
			$this->db->escape($this->checked->plugin_cform_content_type),
			$this->db->escape($this->checked->plugin_cform_minlaeng),
			$up1
		);
		$this->db->query($sql);
		// Neu sortieren
		$this->reorder_fields($this->db->escape($this->checked->plugin_cform_group_id));
		// WEnn leer neu setzen
		if (empty($insertid)) {
			$insertid = $this->db->insert_id;
		}
		// Wenn Update alte Sprachdateien löschen
		if ($modus == "update") {
			$sql = sprintf("DELETE FROM %s WHERE plugin_cform_lang_id='%d'",
				$this->cms->tbname['papoo_plugin_cform_lang'],
				$insertid
			);
			$this->db->query($sql);
		}
		// Sprachdaten eintragen
		foreach ($this->checked->plugin_cform_label as $key => $value) {
			$sql = sprintf("%s %s
								SET
								plugin_cform_lang_id='%s',
								plugin_cform_lang_lang='%s',
								plugin_cform_label='%s',
								plugin_cform_content_list='%s',
								plugin_cform_descrip='%s',
								plugin_cform_tooltip='%s',
								plugin_cform_lang_header='%s'",
				$ins2,
				$this->cms->tbname['papoo_plugin_cform_lang'],
				$this->db->escape($insertid),
				$this->db->escape($key),
				$this->db->escape($this->checked->plugin_cform_label[$key]),
				$this->db->escape($this->checked->plugin_cform_content_list[$key]),
				$this->db->escape($this->checked->plugin_cform_descrip[$key]),
				$this->db->escape($this->checked->plugin_cform_tooltip[$key]),
				""
			);
			$this->db->query($sql);
		}
	}

	/**
	 * Daten eines Feldes rausholen
	 */
	function get_field_data()
	{
		$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_id='%d'
									",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->feldid)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['fdat'] = $result;
	}

	/**
	 * Feld löschen
	 */
	function del_field()
	{
		// Normale Daten löschen
		$sql = sprintf("DELETE FROM %s WHERE plugin_cform_id='%d'",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->plugin_cform_id)
		);
		$this->db->query($sql);
		// Sprachdaten löschen
		$sql = sprintf("DELETE FROM %s WHERE plugin_cform_lang_id='%d'",
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$this->db->escape($this->checked->plugin_cform_id)
		);
		$this->db->query($sql);
		$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
		$this->reload($location_url);
	}

	/**
	 * Feld bearbeiten
	 */
	function change_field()
	{
		$this->get_image_list();
		// Feld löschen
		if ((!empty($this->checked->del_field))) {
			$this->del_field();
		}
		if (empty($this->checked->submit_field)) {
			// Feld Formular erstellenb
			$this->make_feld_form();
			// Feldaten rausholen
			$this->get_field_data();
		} else {
			// Daten checken
			$this->check_data_fields();
			// WEnn ok dann eintragen
			if (empty($this->error)) {
				$this->insup_new_field("update");
				if ((!empty($this->checked->copy))) {
					$this->insup_new_field();
				}
				// Neu laden
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;
				$this->reload($location_url);
			} else {
				// Feld Formular erstellenb
				$this->make_feld_form();
				// Daten erneut ausgeben
				$this->do_it_again();
				// Template Error
				$this->content->template['fehler'] = "ok";
			}
		}
	}

	/**
	 * Neue Gruppen und Felder hinzufügen
	 */
	function make_input_entry()
	{
		$this->content->template['form_manager_id'] = $this->checked->form_manager_id;
		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		$this->content->template['fl_link'] = $link;
		// Wenn form_managerid gesetzt,
		if (is_numeric($this->checked->form_manager_id)) {
			// Sortierung
			$this->switch_order();
			$this->switch_order_groups();
			// Gruppierung erstellen
			if (isset($this->checked->pfgruppeid) && $this->checked->pfgruppeid == "new") {
				$this->create_new_group();
			}
			// Gruppierung bearbeiten
			if (isset($this->checked->grupid) && is_numeric($this->checked->grupid)) {
				$this->change_group();
			}
			// Feld erstellen
			if (isset($this->checked->pffeldid) && $this->checked->pffeldid == "new") {
				$this->create_new_field();
			}
			// Feld bearbeiten
			if (isset($this->checked->feldid) && is_numeric($this->checked->feldid)) {
				$this->change_field();
			}

			//form_manager_name,
			$sql = sprintf("SELECT form_manager_name FROM %s
							WHERE form_manager_id='%d'",
				$this->cms->tbname['papoo_form_manager'],
				$this->checked->form_manager_id
			);
			$this->content->template['form_manager_name'] = $this->db->get_var($sql);

			$this->content->template['form'] = "ok";
			// Liste der Gruppen und Felder des Formulares
			$this->get_form_group_field_list();
		} else {
			// Liste der Formulare anbieten
			$this->get_form_list();
			$this->content->template['liste'] = "ok";
		}
	}

	/**
	 * Alle Gruppen mit den jeweiligen Feldern
	 *
	 * @param mixed $formid Formular-ID
	 */
	function get_form_group_field_list($formid = "")
	{
		// Liste der Gruppen rausholen
		$this->get_form_group_list($formid);
		$groupar = $this->result_groups;
		$i = 0;
		// Für diese Gruppen durchgehen
		if ((!empty($this->result_groups))) {
			foreach ($this->result_groups as &$group) {
				// Alle Felder der jeweiligen Gruppe rausholen
				$sql = sprintf("SELECT DISTINCT * FROM %s,%s WHERE
										plugin_cform_group_id='%d'
										AND plugin_cform_id=plugin_cform_lang_id
										AND plugin_cform_lang_lang='%d' ORDER BY plugin_cform_order_id ASC",
					$this->cms->tbname['papoo_plugin_cform_lang'],
					$this->cms->tbname['papoo_plugin_cform'],
					$group['plugin_cform_group_id'],
					$this->cms->lang_id
				);
				$result = $this->db->get_results($sql, ARRAY_A);

				$this->content->template['felder_form'][] = $result;
				$groupar[$i]['felder'] = $result;
				$group['felder'] = $result;
				$i++;
			}
			unset($group);
		}

		$sql = sprintf("SELECT DISTINCT * FROM %s,%s WHERE
										plugin_cform_form_id='%d'
										AND plugin_cform_no_group='1'
										AND plugin_cform_id=plugin_cform_lang_id
										AND plugin_cform_lang_lang='%d' ",
			$this->cms->tbname['papoo_plugin_cform_lang'],
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->form_manager_id),
			$this->cms->lang_id
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ((!empty($result))) {
			$groupar[$i + 1]['felder'] = $result;
			$groupar[$i + 1]['plugin_cform_group_text'] = "Keine Gruppe / No group";
			$groupar[$i + 1]['plugin_cform_group_id'] = "xx";
		}
		$this->content->template['gfliste'] = $groupar;
	}

	/**
	 * Felder sortieren
	 */
	function switch_order()
	{
		if (!empty($this->checked->submitorder)) {
			if (is_numeric($this->checked->plugin_cform_id) && is_numeric($this->checked->plugin_cform_order_id)) {
				$sql = sprintf("SELECT plugin_cform_group_id FROM %s WHERE plugin_cform_id='%s'",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($this->checked->plugin_cform_id)
				);
				$group_id = $this->db->get_var($sql);
				// Orderid des Vorgängers
				$sql = sprintf("SELECT plugin_cform_order_id FROM %s WHERE plugin_cform_id='%s'",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($this->checked->plugin_cform_id)
				);
				$alt_order_id = $this->db->get_var($sql);
				// Orderid des Vorgängers auf order des Nachfolgers setzen
				$sql = sprintf("UPDATE %s SET plugin_cform_order_id='%s' WHERE plugin_cform_order_id='%s' AND plugin_cform_group_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($alt_order_id),
					$this->db->escape($this->checked->plugin_cform_order_id),
					$group_id

				);
				$this->db->query($sql);

				$sql = sprintf("UPDATE %s SET plugin_cform_order_id='%s' WHERE plugin_cform_id='%s' LIMIT 1",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($this->checked->plugin_cform_order_id),
					$this->db->escape($this->checked->plugin_cform_id)

				);
				$this->db->query($sql);

				$this->reorder_fields($group_id);
			}
		}
	}

	/**
	 * Gruppen sortieren
	 */
	function switch_order_groups()
	{
		if (!empty($this->checked->submitorder_gr)) {
			if (is_numeric($this->checked->plugin_cform_group_id) && is_numeric($this->checked->plugin_cform_group_order_id)) {
				// Orderid des Vorgängers
				$this->checked->plugin_cform_group_order_id;
				$sql = sprintf("SELECT plugin_cform_group_order_id FROM %s WHERE plugin_cform_group_id='%s'",
					$this->cms->tbname['papoo_plugin_cform_group'],
					$this->db->escape($this->checked->plugin_cform_group_id)
				);
				$alt_order_id = $this->db->get_var($sql);
				// Orderid des Vorgängers auf order des Nachfolgers setzen
				$sql = sprintf("UPDATE %s SET plugin_cform_group_order_id='%s' WHERE plugin_cform_group_order_id='%s' AND plugin_cform_group_form_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_plugin_cform_group'],
					$this->db->escape($alt_order_id),
					$this->db->escape($this->checked->plugin_cform_group_order_id),
					$this->db->escape($this->checked->form_manager_id)

				);
				$this->db->query($sql);

				$sql = sprintf("UPDATE %s SET plugin_cform_group_order_id='%s' WHERE plugin_cform_group_id='%s' AND plugin_cform_group_form_id='%d' LIMIT 1",
					$this->cms->tbname['papoo_plugin_cform_group'],
					$this->db->escape($this->checked->plugin_cform_group_order_id),
					$this->db->escape($this->checked->plugin_cform_group_id),
					$this->db->escape($this->checked->form_manager_id)

				);
				$this->db->query($sql);
				$this->reorder_groups($this->db->escape($this->checked->form_manager_id));
			}
		}
	}

	/**
	 * Felder und Gruppen bearbeiten
	 */
	function change_input_entry()
	{}

	/**
	 * form_manager::do_sales_force()
	 * Kontakt mit Salesforce zur Übergabe eines Leads
	 *
	 * @return void
	 */
	function do_sales_force($conf = array())
	{

		//WEnn Debugging aktiv sein soll übergeben
		if ($conf['form_manager_saleforce_debug'] == 1) {
			$_POST['debug'] = $conf['form_manager_saleforce_debug'];
			$_POST['debugEmail'] = $conf['form_manager_saleforce_debug_email'] . " <mailto:" . $conf['form_manager_saleforce_debug_email'] . ">";
		}
		//ID übergeben
		$_POST['oid'] = $conf['form_manager_saleforce_oid'];

		//Snoopy starten
		$url1 = $conf['form_manager_saleforce_action'];
		$ch = curl_init($url1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT,
			"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST, '', '&'));
		$curl_ret = curl_exec($ch);
		curl_close($ch);

		$daten = $curl_ret;
		if ($conf['form_manager_saleforce_debug'] == 1) {
			echo '<div style="position:absolute; width:300px; height:400px; overflow:auto;border:1px solid #000;background:#fff;padding:10px;" ><h2>Debugging</h2>';
			print_r($daten);
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&fertig=1&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id;

			echo '<br /><a href="' . $location_url . '">WEITER</a>';
			echo "</div>";
		}

	}

	/**
	 * form_manager::do_save_lead()
	 * Leads in der DB speichern
	 *
	 * @return void
	 */
	function do_save_lead($result)
	{
		if (empty($result[0]['form_manager_loesch_dat1'])) {
			$result[0]['form_manager_loesch_dat1'] = 0;
		}
		if (empty($result[0]['form_manager_loesch_dat2'])) {
			$result[0]['form_manager_loesch_dat2'] = 0;
		}

		$loeschdat1 = $result[0]['form_manager_loesch_dat1'] * 24 * 60 * 60;
		$loeschdat2 = $result[0]['form_manager_loesch_dat2'] * 24 * 60 * 60;
		$this->time_save = time();
		//Generelle Daten speichern
		$sql = sprintf("INSERT INTO %s SET
									form_manager_form_id='%d',
									form_manager_form_datum='%d',
									form_manager_form_ip_sender='%s',
									form_manager_form_loesch_datum1='%d',
									form_manager_form_loesch_datum2='%d'
									",
			$this->cms->tbname['papoo_form_manager_leads'],
			$result[0]['form_manager_id'],
			$this->time_save,
			$this->db->escape($_SERVER['REMOTE_ADDR']),
			time() + $loeschdat1,
			time() + $loeschdat2);
		$this->db->query($sql);
		$insertid = $this->db->insert_id;
		$this->formmanagersendid = $result[0]['form_manager_id'];
		$this->leadsendid = $insertid;
		//Flexible Daten speichern
		foreach ($_POST as $key => $value) {
			//Sonderfall Upload - der kommt später
			if (!empty($_SESSION['filename'][$key])) {
				continue;
			}

			$key = $this->html2txt($key);
			if (is_array($value)) {
				$vdat = "";
				foreach ($value as $keyy => $valuey) {
					$vdat .= $valuey . "\n";
				}
				$value = $vdat;
			}
			$value = $this->html2txt($value);
			$sql = sprintf("INSERT INTO %s SET
									form_manager_content_lead_id_id='%d',
									form_manager_content_lead_feld_name='%s',
									form_manager_content_lead_feld_content='%s'
									",
				$this->cms->tbname['papoo_form_manager_lead_content'],
				$insertid,
				$this->db->escape($key),
				$this->db->escape($value)
			);
			$this->db->query($sql);

		}

		//Den Referrer einlesen
		$sql = sprintf("INSERT INTO %s SET
										form_manager_content_lead_id_id='%d',
										form_manager_content_lead_feld_name='Referrer Google',
										form_manager_content_lead_feld_content='%s'
										",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$insertid,
			$this->db->escape($_COOKIE["RefererG"])
		);
		$this->db->query($sql);

		//Den Referrer einlesen
		$sql = sprintf("INSERT INTO %s SET
										form_manager_content_lead_id_id='%d',
										form_manager_content_lead_feld_name='gclid',
										form_manager_content_lead_feld_content='%s'
										",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$insertid,
			$this->db->escape($_COOKIE["gclid"])
		);
		$this->db->query($sql);

		$sql = sprintf("INSERT INTO %s SET
										form_manager_content_lead_id_id='%d',
										form_manager_content_lead_feld_name='Letzter direkter Referrer',
										form_manager_content_lead_feld_content='%s'
										",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$insertid,
			$this->db->escape($_COOKIE["RefererN"])
		);
		$this->db->query($sql);

		$sql = sprintf("INSERT INTO %s SET
										form_manager_content_lead_id_id='%d',
										form_manager_content_lead_feld_name='Primaerer direkter Referrer',
										form_manager_content_lead_feld_content='%s'
										",
			$this->cms->tbname['papoo_form_manager_lead_content'],
			$insertid,
			$this->db->escape($_COOKIE["RefererF"])
		);
		$this->db->query($sql);

		//Die Files einlesen

		if (is_array($_SESSION['filename'])) {
			foreach ($_SESSION['filename'] as $key => $value) {
				$attach = basename($value);
				$attach_array = PAPOO_ABS_PFAD . "/dokumente/files/" . $attach;

				$key = $this->html2txt($key);
				$value = $this->html2txt($value);
				$sql = sprintf("INSERT INTO %s SET
							form_manager_content_lead_id_id='%d',
							form_manager_content_lead_feld_name='%s',
							form_manager_content_lead_feld_content='%s'
							",
					$this->cms->tbname['papoo_form_manager_lead_content'],
					$insertid,
					$this->db->escape($key),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}

		//Flex MV Daten speichern
		if (is_array($this->mv_daten_array)) {
			foreach ($this->mv_daten_array as $key => $value) {
				$key = $this->html2txt($key);
				$value = $this->html2txt($value);
				$sql = sprintf("INSERT INTO %s SET
									form_manager_content_lead_id_id='%d',
									form_manager_content_lead_feld_name='%s',
									form_manager_content_lead_feld_content='%s'
									",
					$this->cms->tbname['papoo_form_manager_lead_content'],
					$insertid,
					"flex_" . $this->db->escape($key),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}

		$leadDataResultSet = $this->db->get_results(
			"SELECT * ".
			"FROM {$this->cms->tbname['papoo_form_manager_leads']} _lead ".
			"JOIN {$this->cms->tbname['papoo_form_manager_lead_content']} _content ON _content.form_manager_content_lead_id_id = _lead.form_manager_lead_id ".
			"WHERE _lead.form_manager_lead_id = {$this->db->escape((int)$insertid)}", ARRAY_A
		);

		$leadData = array_reduce($leadDataResultSet, function ($leadData, $row) {
			$leadData = $leadData ?? [
					'formId' => (int)$row['form_manager_form_id'],
					'leadId' => (int)$row['form_manager_lead_id'],
					'timestamp' => (int)$row['form_manager_form_datum'],
					'fields' => [],
				];

			$leadData['fields'][$row['form_manager_content_lead_feld_name']] = $row['form_manager_content_lead_feld_content'];

			return $leadData;
		}, null);

		if ($leadData) {
			run_style_hook('onFormManagerLeadSaved', [$leadData]);
		}
	}

	/**
	 * form_manager::do_replace_dat()
	 *
	 * Ersetzungen in Inhalten für Endkunde durchführen
	 *
	 * @param string $inhalt
	 * @return void
	 */
	function do_replace_dat($inhalt = "")
	{
		// $_POST-Felder bereinigen
		$temp_POST = array();
		foreach ($_POST as $key => $value) {
			$key = $this->html2txt($key);
			if (is_array($value)) {
				foreach ($value as &$value2) {
					$value2 = $this->html2txt($value2);
				}
			} else {
				$value = $this->html2txt($value);
				$key = str_replace("/", "", $key);
				$key = str_replace(">", "", $key);
			}
			$temp_POST[$key] = $value;
		}
		$_POST = $temp_POST;

		// Formular-Felder und -Gruppen holen
		$this->get_form_group_field_list($this->checked->form_manager_id);

		// Formular-Gruppen durchgehen
		foreach ($this->result_groups as $group) {
			$g_id = (int)$group['plugin_cform_group_id'];
			$g_name = $group['plugin_cform_group_name'];
			if (strpos($inhalt, '#group_' . $g_id . '_bulk#') !== false) {
				/* Größte Anzahl von Semikolon-getrennten Elementen berechnen */
				$length = 0;
				foreach ($group['felder'] as $feld) {
					$tmp = $temp_POST[$feld['plugin_cform_name']];
					if ($tmp) {
						$len = count(explode(';', $tmp));
					} else {
						$len = 0;
					}
					if ($len > $length) {
						$length = $len;
					}
				}
				/* Ausgabe bauen */
				$result = array("$g_name:");
				for ($i = 0; $i < $length; $i++) {
					$tmp = array();
					foreach ($group['felder'] as $feld) {
						$f_name = $feld['plugin_cform_name'];
						$f_label = $feld['plugin_cform_label'];
						$f_values = explode(';', $temp_POST[$f_name]);
						$f_value = (count($f_values) > $i) ? $f_values[$i] : '';
						$tmp[] = $f_label . ': ' . $f_value;
					}
					$result[] = '  Teilstrecke ' . ($i + 1) . ":\n    " . implode("\n    ", $tmp);
				}
				$result = implode("\n", $result);
				$inhalt = str_replace('#group_' . $g_id . '_bulk#', $result, $inhalt);
			}
		}

		// Formular-Felder durchgehen
		if (!empty($this->content->template['felder_form'])) {
			foreach ($this->content->template['felder_form'] as $groupfeld) {
				if (count($groupfeld)) {
					foreach ($groupfeld as $feld) {
						$temp_key = $feld['plugin_cform_name'];
						$temp_value = isset($temp_POST[$temp_key]) ? $temp_POST[$temp_key] : NULL;

						// chgd. khmweb to include label
						// Multiselect
						if ($feld['plugin_cform_type'] == "multiselect") {
							if (isset($temp_POST[$temp_key]) && is_array($temp_POST[$temp_key])) {
								$data_multi = "";
								foreach ($temp_POST[$temp_key] as $value_multi) {
									$data_multi .= $value_multi . "\n";
								}
								$inhalt = str_replace('#' . $temp_key . '#', $data_multi, $inhalt);
							}
						} else {
							if ($feld['plugin_cform_type'] == "check" OR $feld['plugin_cform_type'] == "remark" ) {
								if (isset($temp_POST[$temp_key]) && $temp_POST[$temp_key]) {
									$inhalt = str_replace('#' . $temp_key . '#', $feld['plugin_cform_label'], $inhalt);
								}
								else {
									$temp_value = $this->content->template['message']['plugin']['form_manager']['replace']['no'];
									$inhalt = str_replace('#' . $temp_key . '#', $temp_value . "",
										$inhalt); // falls ohne Code
									$inhalt = str_replace('#' . $temp_key . '#<br />', $temp_key,
										$inhalt); // Keine Ausgabe, wenn nicht checked
									$inhalt = str_replace('#' . $temp_key . '#<br>', $temp_key,
										$inhalt); // Keine Ausgabe, wenn nicht checked
									$inhalt = str_replace('#' . $temp_key . '#<br/>', $temp_key,
										$inhalt); // Keine Ausgabe, wenn nicht checked
									$inhalt = str_replace('#' . $temp_key . '#</p>', $temp_key, $inhalt); // falls mit p
									$inhalt = str_replace('<b>#' . $temp_key . '#</b><br />', $temp_key,
										$inhalt); // falls mit b und br
									$inhalt = str_replace('#' . $temp_key . "#\r\n", $temp_key,
										$inhalt); // falls mit CR und LF
									$inhalt = str_replace('#' . $temp_key . '#', $temp_value . "",
										$inhalt); // falls ohne Code
								}
							}
							else {
								$inhalt = str_replace('#' . $temp_key . '#', $temp_value, $inhalt);
							} // Ausgabe für alles andre als check/radio/remark

						}
						$_SESSION['formdat'][$temp_key] = $temp_value;
					}
				}
			}
		}

		//Flex MV Daten speichern
		if (isset($this->mv_daten_array) && is_array($this->mv_daten_array)) {
			foreach ($this->mv_daten_array as $key => $value) {
				$key = str_ireplace("/", "", $key);
				$key = str_ireplace(">", "", $key);
				$inhalt = preg_replace('/#flex_' . $key . '#/', $value, $inhalt);
			}
		}

		IfNotSetNull($_COOKIE["RefererG"]);
		IfNotSetNull($_COOKIE["gclid"]);
		IfNotSetNull($_COOKIE["RefererN"]);
		IfNotSetNull($_COOKIE["RefererF"]);
		IfNotSetNull($_COOKIE["uid"]);

		$inhalt = str_replace('#remote_ip#', "" . $_SERVER['REMOTE_ADDR'] . "", $inhalt); // falls ohne Code
		$inhalt = str_replace('#RefererG#', "Referrer von Google: " . $_COOKIE["RefererG"] . "",
			$inhalt); // falls ohne Code
		$inhalt = str_replace('#gclid#', "Google ClickID (ADwords): " . $_COOKIE["gclid"] . "",
			$inhalt); // falls ohne Code
		$inhalt = str_replace('#RefererN#', "Letzter direkter Referrer: " . $_COOKIE["RefererN"] . "",
			$inhalt); // falls ohne Code
		$inhalt = str_replace('#RefererF#', "Erster direkter Referrer: " . $_COOKIE["RefererF"] . "",
			$inhalt); // falls ohne Code
		if (isset($this->content->template['plugin']['form_manager']['installed_leadtracker']) &&
			$this->content->template['plugin']['form_manager']['installed_leadtracker']
		) {
			$inhalt = str_replace('#UserID#', "User-ID: " . $_COOKIE["uid"] . "", $inhalt);
		}
		else {
			$inhalt = str_replace("#UserID#", "User-ID konnte nicht ausgegeben werden (Leadtracker nicht installiert)",
				$inhalt);
		}

		$inhalt = preg_replace('/#(.*?)#/', "", $inhalt);

		return $inhalt;
	}

	/**
	 * form_manager::make_csv_single()
	 *
	 * @return void
	 */
	function make_csv_single()
	{
		$sql = sprintf("SELECT * FROM %s
								WHERE 	plugin_cform_form_id='%d'",
			$this->cms->tbname['papoo_plugin_cform'],
			$this->db->escape($this->checked->form_manager_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$csv = '';
		if (is_array($result)) {
			foreach ($result as $key => $value) {
				foreach ($this->checked as $key2 => $value2) {
					if ($value['plugin_cform_name'] == $key2) {
						$csv .= '"' . $key2 . '",';
					}
				}
			}
			$csv .= "\n";

			foreach ($result as $input) {
				foreach ($this->checked as $key => $value) {
					if ($input['plugin_cform_name'] == $key) {
						if ($csv) {
							$csv .= ',';
						}
						if (is_array($value)) {
							$value = implode(';', $value);
						}
						if (is_null($value)) {
							$value = '-';
						}
						$value = str_replace('"', '\\"', ((string)$value));
						$csv .= '"' . $value . '"';
						break;
					}
				}
			}
		}

		$file = "/dokumente/logs/formular_daten.csv";
		$this->diverse->write_to_file($file, $csv);
	}

	/**
	 * form_manager::save_lead_id()
	 * Lead ID noch speichern damit die Daten dann auch gelöscht werden können
	 * @return void
	 */
	function save_lead_id()
	{
		if ($this->xml_verzeichnis) {
			$csv = $this->leadsendid;
			$file = "/" . $this->destination_dir . "/id.csv";
			$this->diverse->write_to_file($file, $csv);
		}
	}

	/**
	 * form_manager::make_xml_single_save()
	 *
	 * Speichert die Formulardaten
	 * in dem aktuellen Verzeichnis im Sonderfall xml Verzeichnis = true
	 *
	 * @return bool
	 */
	function make_xml_single_save()
	{
		if ($this->xml_verzeichnis) {
			$sql = sprintf("SELECT * FROM %s
						WHERE 	plugin_cform_form_id='%d'",
				$this->cms->tbname['papoo_plugin_cform'],
				$this->db->escape($this->checked->form_manager_id)
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			$xml = '<?xml version="1.0"?>
<jobapplication xmlns="http://www.papoo.de"
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				xsi:schemaLocation="http://www.papoo.de/plugins/jobapplication_scheme.xsd">
';
			if (is_array($result)) {
				foreach ($result as $input) {
					foreach ($this->checked as $key => $value) {
						if ($input['plugin_cform_name'] == $key) {
							$xml .= "\t<$key>";
							if (is_array($value)) {
								$value = implode(";\n\t\t", $value);
							}
							if (is_null($value)) {
								$value = '';
							}
							$value = (string)$value;
							if (defined('ENT_XML1') && defined('ENT_DISALLOWED')) {
								$xml .= htmlspecialchars($value, ENT_NOQUOTES | ENT_DISALLOWED | ENT_XML1);
							} else {
								$xml .= htmlspecialchars($value, ENT_NOQUOTES);
							}
							$xml .= "</$key>\n";
						}
					}
				}
			}
			$xml .= '</jobapplication>';
			$file = "/" . $this->destination_dir . "/jobapplication.xml";
			$this->diverse->write_to_file($file, $xml);
			return true;
		}
		return false;
	}

	/**
	 * @param $betreff
	 */
	private function verschick_zwischen_mail($betreff)
	{
		$sql = sprintf("SELECT * FROM %s
                                            WHERE form_manager_id_id='%s'
                                            AND form_manager_lang_id='%s'",
			$this->cms->tbname['papoo_form_manager_lang'],
			$this->db->escape($this->checked->form_manager_id),
			$this->db->escape($this->cms->lang_id)
		);
		$result = $this->db->get_results($sql);

		/**
		 * Das Ganze nochmal als Array
		 */
		$result_ar = $this->db->get_results($sql, ARRAY_A);

		// SMTP Einstellungen abfragen
		$mailSettings = $this->getMailConfigByFormId($this->checked->form_manager_id);

		// Bestimme E-Mail-Empfänger
		if (!empty ($result)) {
			foreach ($result as $spalte) {
				// E-Mail-Adresse an die gesendet werden soll
				$sql = sprintf("SELECT * FROM %s
                                                    WHERE form_manager_email_form_id='%s'
                                                    ORDER BY form_manager_email_id ASC",
					$this->cms->tbname['papoo_form_manager_email'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$die_mails = $this->db->get_results($sql, ARRAY_A);
				$form_manager_email = "";
				if (is_array($die_mails)) {
					foreach ($die_mails as $dm) {
						$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
					}
				}

				$form_manager_email_ar = explode(";", $form_manager_email);

				if (isset($_SESSION['filename']) && is_array($_SESSION['filename'])) {
					foreach ($_SESSION['filename'] as $attach) {
						$attach = basename($attach);
						$attach_array[] = PAPOO_ABS_PFAD . "/dokumente/files/" . $attach;
					}
				}
				IfNotSetNull($sendto_namex);

				$spalte->form_manager_antwort_email_betreff = $this->do_replace_dat($betreff);
				$subject = preg_replace('/#sendto#/', $sendto_namex, $spalte->form_manager_antwort_email_betreff);

				if (strlen($result_ar[0]['mail_an_betreiber_inhalt']) > 10) {
					$inhalt = $this->do_replace_dat($result_ar[0]['mail_an_betreiber_inhalt']);
				}

				if (is_file(PAPOO_ABS_PFAD . "/dokumente/logs/formular_daten.csv")) {
					$attach_array[] = PAPOO_ABS_PFAD . "/dokumente/logs/formular_daten.csv";
				}
				foreach ($form_manager_email_ar as $email) {
					if (($this->validateEmail($email))) {
						// Mailen vorbereiten
						$this->mail_it->to = $email;
						$this->mail_it->from = $mailSettings['sender_mail'];
						if (isset($attach_array) && is_array($attach_array)) {
							$this->mail_it->attach = $attach_array;
						}

						$this->mail_it->subject = $this->do_replace_dat($betreff);
						$this->mail_it->body = isset($inhalt) ? $inhalt : NULL;
						if (!empty($inhalt)) {
							$this->mail_it->do_mail($mailSettings);
						}
					}
				}
			}
		}
	}

	private function check_ob_checked_mail_vorhanden()
	{
		foreach ($this->checked as $k => $v) {
			if ($this->validateEmail($v)) {
				return $k;
			}
		}
	}

	/**
	 * form_manager::verschick()
	 * Daten verschicken
	 *
	 * @param mixed $inhalt
	 * @return
	 */
	function verschick($inhalt, $additional_mail = "")
	{
		if (is_array($_SESSION['form_entry'][$this->checked->form_manager_id])) {
			foreach ($_SESSION['form_entry'][$this->checked->form_manager_id] as $k => $v) {
				$this->checked->$k = $v;
				$_POST[$k] = $v;
			}
		}
		//Session Daten auf 0 setzen für das nä. Formular...
		$_SESSION['form_entry'] = array();
		unset($_SESSION['form_entry']);

		/**
		 * Daten des Formulars rausholen
		 * der jeweiligen Sprache
		 */
		$sql = sprintf("SELECT * FROM %s
									WHERE form_manager_id_id='%s'
									AND form_manager_lang_id='%s'",
			$this->cms->tbname['papoo_form_manager_lang'],
			$this->db->escape($this->checked->form_manager_id),
			$this->db->escape($this->cms->lang_id)
		);
		$result = $this->db->get_results($sql);

		/**
		 * Das Ganze nochmal als Array
		 */
		$result_ar = $this->db->get_results($sql, ARRAY_A);

		/**
		 * Kerndaten sprachunabhängig
		 */
		$sql = sprintf("SELECT * FROM %s
										WHERE form_manager_id='%s'",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($this->checked->form_manager_id)
		);
		$result2 = $this->db->get_results($sql, ARRAY_A);

		/**
		 * SMTP Einstellungen
		 */
		$mailSettings = $this->getMailConfigByFormId($this->checked->form_manager_id);

		/**
		 * Daten für Salesforce
		 */
		$salesres = $result2[0];
		if ($salesres['form_manager_saleforce_yn'] == 1) {
			$this->do_sales_force($salesres);
		}
		/**
		 * Wenn MV
		 *
		 */
		if (!empty($this->checked->flexid)) {
			//Flex Sachen machen
			$save_mv_content_id = $this->checked->mv_content_id;
			$save_mv_id = $this->checked->mv_id;
			$this->checked->mv_content_id = $this->checked->flexid;
			$this->checked->mv_id = $this->checked->flex_mv_id;
			$this->form_bind_mv();
			$this->form_show_search_mv();
			$this->checked->mv_content_id = $save_mv_content_id;
			$this->checked->mv_id = $save_mv_id;
		}
		//Generell speichern
		$this->do_save_lead($result2);

		// Bestimme E-Mail-Empfänger
		if (!empty ($result)) {
			foreach ($result as $spalte) {
				// E-Mail-Adresse an die gesendet werden soll
				$sql = sprintf("SELECT * FROM %s
												WHERE form_manager_email_form_id='%s'
												ORDER BY form_manager_email_id ASC",
					$this->cms->tbname['papoo_form_manager_email'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$die_mails = $this->db->get_results($sql, ARRAY_A);
				$form_manager_email = "";
				if (is_numeric($this->checked->sendto)) {
					if (is_array($die_mails)) {
						foreach ($die_mails as $dm) {
							if ($this->checked->sendto == $dm['form_manager_email_id']) {
								$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
								$sendto_namex = $dm['form_manager_email_name'];
							}
						}
					}
				} elseif (is_array($this->checked->sendto)) {
					if (is_array($die_mails)) {
						$sendto_namex = '';
						foreach ($this->checked->sendto as $sendto) {
							if (is_numeric($sendto)) {
								foreach ($die_mails as $dm) {
									if ($sendto == $dm['form_manager_email_id']) {
										$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
										if ($sendto_namex) {
											$sendto_namex .= '; ';
										}
										$sendto_namex .= $dm['form_manager_email_name'];
									}
								}
							}
						}
					}
				} else {
					if (is_array($die_mails)) {
						foreach ($die_mails as $dm) {
							$form_manager_email .= trim($dm['form_manager_email_email']) . ";";
						}
					}
				}
				if (!empty($additional_mail)) {
					$form_manager_email = $form_manager_email . ";" . $additional_mail;
				}

				$form_manager_email_ar = explode(";", $form_manager_email);
				if (is_array($_SESSION['filename'])) {
					foreach ($_SESSION['filename'] as $attach) {
						$attach = basename($attach);
						$attach_array[] = PAPOO_ABS_PFAD . "/dokumente/files/" . $attach;
					}
				}
				$sql = sprintf("SELECT plugin_cform_name FROM %s
											WHERE plugin_cform_type='email' AND plugin_cform_form_id='%d' ",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($this->checked->form_manager_id)
				);
				// Mail versenden
				$form_manager_antwort_mail = $this->db->get_var($sql);
				if (empty($form_manager_antwort_mail)) {
					//Hier noch check ob es eine checked Var gibt die eine Mail Adresse ist
					$form_manager_antwort_mail = $this->check_ob_checked_mail_vorhanden();
				}
				if (!empty($this->checked->$form_manager_antwort_mail)) {
					$email_to = trim($this->checked->$form_manager_antwort_mail);
				}
				else {
					$email_to = null;
				}

				$spalte->form_manager_antwort_email_betreff = $this->do_replace_dat($spalte->form_manager_antwort_email_betreff);
				$subject = preg_replace('/#sendto#/', $sendto_namex, $spalte->form_manager_antwort_email_betreff);

				$sendtoflexmail = "";
				if (is_array($this->mv_daten_array)) {

					foreach ($this->mv_daten_array as $key => $value) {
						#echo $value;
						if (($this->validateEmail($value))) {
							$sendtoflexmail .= $value;
						}
					}
				}

				//Überprüfen ob es eine Vorlage für die Mail an Betreiber gibt
				if (strlen($result_ar[0]['mail_an_betreiber_inhalt']) > 10) {
					$inhalt = $this->do_replace_dat($result_ar[0]['mail_an_betreiber_inhalt']);
				}

				/**
				 * CSV Daten speichern
				 */
				if ($this->add_csv_toMail == 1) {
					$this->make_csv_single();
				}

				/**
				 * XML Daten speichern wenn gewünscht
				 * */
				$this->make_xml_single_save();
				if ($this->add_csv_toMail == 1) {
					if (is_file(PAPOO_ABS_PFAD . "/dokumente/logs/formular_daten.csv")) {
						$attach_array[] = PAPOO_ABS_PFAD . "/dokumente/logs/formular_daten.csv";
					}
				}

				if ($email_to) {
					$this->mail_it->ReplyTo[$email_to] = $email_to;
				}

				foreach ($form_manager_email_ar as $email) {
					if (($this->validateEmail($email))) {
						// Mailen vorbereiten
						$this->mail_it->to = $email;
						$this->mail_it->from = $mailSettings['sender_mail'];
						if (is_array($attach_array)) {
							$this->mail_it->attach = $attach_array;
						}

						$temp_betreiber_subject = $this->do_replace_dat($result_ar['0']['mail_an_betreiber_betreff']);
						if (empty($temp_betreiber_subject)) {
							$temp_betreiber_subject = $this->do_replace_dat($subject);
						}
						if ($sendtoflexmail) {
							$this->mail_it->subject = $temp_betreiber_subject . " - (" . $sendtoflexmail . ")";
						} else {
							$this->mail_it->subject = $temp_betreiber_subject;
						}

						$this->mail_it->body = $inhalt;
						if (!empty($inhalt)) {
							$this->mail_it->do_mail($mailSettings);
						}
					}
				}

				$this->mail_it->ReplyTo = [];

				$this->content->template['form_manager_antwort_html'] = $spalte->form_manager_antwort_html;
				$sql = sprintf("SELECT form_manager_antwort_yn FROM %s
												WHERE form_manager_id='%s' ",
					$this->cms->tbname['papoo_form_manager'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$form_manager_antwort_yn = $this->db->get_var($sql);

				//Generell Ersetzungen machen
				$spalte->form_manager_antwort_email = $this->do_replace_dat($spalte->form_manager_antwort_email);
				$spalte->form_manager_antwort_email_html = $this->do_replace_dat($spalte->form_manager_antwort_email_html);

				// Wenn geantwortet werden soll
				if ($form_manager_antwort_yn == 1 && $email_to) {
					// Name des E-Mail Feldes raussuchen
					$this->mail_it->to = $email_to;
					$this->mail_it->from = $mailSettings['sender_mail'];
					$this->mail_it->subject = $this->do_replace_dat($subject);
					$this->mail_it->body = $spalte->form_manager_antwort_email;
					$this->mail_it->body_html = $spalte->form_manager_antwort_email_html;
					// Anhänge an Antwortmail
					if ($this->antwort_mail_attachments) {
						if (is_array($attach_array)) {
							$this->mail_it->attach = $attach_array;
						}
					} else {
						$this->mail_it->attach = array();
					}

					if (!empty($inhalt)) {
						$this->mail_it->do_mail($mailSettings);
					}
				}

				if (is_array($this->mv_daten_array)) {
					$text = "";
					foreach ($this->mv_daten_array as $key => $value) {
						$text .= $key . ": " . $value . "\n\r";
					}
					global $mv;
					if ($mv->dzvhae_system_id != true) {
						$link = "\n Link zum Eintrag: \n http://" .
							$this->cms->title_send . "/interna/plugin.php?menuid=1084&template=form_manager/templates/messages.html&form_manager_id=" .
							$this->formmanagersendid . "&lead_id=" . $this->leadsendid;

					} else {
						$link = "\n Link zum Eintrag: \n http://" .
							$this->cms->title_send . "/plugin.php?menuid=" . $this->checked->menuid . "&template=mv/templates/mv_show_front.html&mv_id=" .
							$this->checked->flex_mv_id . "&extern_meta=x&mv_content_id=" . $this->checked->flexid . "";
					}

					foreach ($this->mv_daten_array as $key => $value) {
						if (($this->validateEmail($value))) {
							// Mailen vorbereiten
							$this->mail_it->to = $value;
							$this->mail_it->from = $mailSettings['sender_mail'];
							if (is_array($attach_array)) {
								$this->mail_it->attach = $attach_array;
							}
							$this->mail_it->subject = $this->do_replace_dat($subject);
							$this->mail_it->body = $this->do_replace_dat($inhalt . $text . "" . $link);
							if (!empty($inhalt)) {
								$this->mail_it->do_mail($mailSettings);
							}

							if ($form_manager_antwort_yn == 1 && $email_to) {
								// Name des E-Mail Feldes raussuchen
								$this->mail_it->to = $email_to;
								$this->mail_it->from = $mailSettings['sender_mail'];
								$this->mail_it->subject = $this->do_replace_dat($subject);
								$this->mail_it->body = $spalte->form_manager_antwort_email;
								$this->mail_it->body_html = $spalte->form_manager_antwort_email_html;
								if (!empty($inhalt)) {
									$this->mail_it->do_mail($mailSettings);
								}
							}
						}
					}
				}
				$this->save_lead_id();
				unset($_SESSION['filename']);
				unset($_SESSION['form_destination_dir']);
			}
		}
	}

	/**
	 *
	 * @abstract Die Glossardaten dumpen und wieder zurückspielen
	 */

	function form_manager_dump()
	{
		$this->diverse->extern_dump("form_manager,plugin_cform");
	}

	/**
	 *
	 * @abstract Ein neuer Eintrag wird erstellt und die Option zum Datenbank durchloopen angeboten
	 * @return
	 */

	function make_entry()
	{
		$this->content->template['language'] = "";
		$this->make_lang();

		if (!empty ($this->checked->formSubmit) && !empty($this->checked->form_manager_name)) {
			$sql = sprintf("INSERT INTO %s
							SET form_manager_email='%s',
							form_manager_antwort_html='%s',
							form_manager_anzeig_select_email='%s',
							form_manager_name='%s',
							form_manager_antwort_email='%s',
							form_manager_saleforce_yn='%s',
							form_manager_saleforce_oid='%s',
							form_manager_saleforce_action='%s',
							form_manager_saleforce_debug='%s',
							form_manager_loesch_dat1='%s',
							form_manager_loesch_dat2='%s',
							form_manager_saleforce_debug_email='%s',
							form_manager_antwort_yn='%s',
							form_manager_kategorie='%s',
							form_manager_sender_mail='%s',
							form_manager_mail_settings_type='%s',
							form_manager_smtp_host='%s',
							form_manager_smtp_port='%s',
							form_manager_smtp_user='%s',
							form_manager_smtp_pass='%s',
							form_manager_erstellt='%s',
							form_manager_geaendert='%s'",
				$this->cms->tbname['papoo_form_manager'],
				$this->db->escape($this->checked->form_manager_email),
				"",
				$this->db->escape($this->checked->form_manager_anzeig_select_email),
				$this->db->escape($this->checked->form_manager_name),
				"",
				$this->db->escape($this->checked->form_manager_saleforce_yn),
				$this->db->escape($this->checked->form_manager_saleforce_oid),
				$this->db->escape($this->checked->form_manager_saleforce_action),
				$this->db->escape($this->checked->form_manager_saleforce_debug),
				$this->db->escape($this->checked->form_manager_loesch_dat1),
				$this->db->escape($this->checked->form_manager_loesch_dat2),
				$this->db->escape($this->checked->form_manager_saleforce_debug_email),
				$this->db->escape($this->checked->form_manager_antwort_yn),
				$this->db->escape($this->checked->form_manager_kategorie),
				$this->db->escape($this->checked->form_manager_sender_mail),
				$this->db->escape($this->checked->form_manager_mail_settings_type),
				$this->db->escape($this->checked->form_manager_smtp_host),
				$this->db->escape($this->checked->form_manager_smtp_port),
				$this->db->escape($this->checked->form_manager_smtp_user),
				$this->db->escape($this->checked->form_manager_smtp_pass),
				time(),
				time()
			);
			$this->db->query($sql);
			$insertid = $this->db->insert_id;
			//Für Module übergeben
			$insertid2 = $this->db->insert_id;

			//E-Mail Daten eintragen
			$email_array = explode("\n", $this->checked->form_manager_email);

			//Neue E-Mails durchgehen und eintragen
			if (is_array($email_array)) {
				foreach ($email_array as $email) {
					if (strlen($email) > 2) {
						$email_dat = explode(";", $email);
						if (!empty($email_dat[1])) {

							$sql = sprintf("INSERT INTO %s
									SET form_manager_email_form_id='%s',
									form_manager_email_email='%s',
									form_manager_email_name='%s'",
								$this->cms->tbname['papoo_form_manager_email'],
								$this->db->escape($insertid),
								$this->db->escape($email_dat[1]),
								$this->db->escape($email_dat[0])
							);
							$this->db->query($sql);
						}
					}
				}
			}
			if (!empty($this->checked->form_manager_antwort_html)) {
				foreach ($this->checked->form_manager_antwort_html as $key => $value) {
					if (empty($this->checked->form_manager_lang_button[$key])) {
						$this->checked->form_manager_lang_button[$key] = "Absenden";
					}
					// echo $key;
					$sql = sprintf("INSERT INTO %s
								SET form_manager_id_id='%s',
								form_manager_antwort_html='%s',
								form_manager_toptext_html='%s',
								form_manager_bottomtext_html='%s',
								form_manager_antwort_email_betreff='%s',
								form_manager_antwort_email='%s',
								form_manager_antwort_email_html='%s',
								form_manager_lang_button='%s',
								form_manager_lang_id='%s',
								mail_an_betreiber_betreff='%s',
								mail_an_betreiber_inhalt='%s'",
						$this->cms->tbname['papoo_form_manager_lang'],
						$this->db->escape($insertid),
						$this->db->escape($this->checked->form_manager_antwort_html[$key]),
						$this->db->escape($this->checked->form_manager_toptext_html[$key]),
						$this->db->escape($this->checked->form_manager_bottomtext_html[$key]),
						$this->db->escape($this->checked->form_manager_antwort_email_betreff[$key]),
						$this->db->escape($this->checked->form_manager_antwort_email[$key]),
						$this->db->escape($this->checked->form_manager_antwort_email_html[$key]),
						$this->db->escape($this->checked->form_manager_lang_button[$key]),
						$this->db->escape($key),
						$this->db->escape($this->checked->mail_an_betreiber_betreff[$key]),
						$this->db->escape($this->checked->mail_an_betreiber_inhalt[$key])
					);
					$this->db->query($sql);
				}
			}

			//Dann jetzt in die Module Tabelle eintragen
			//Modul eintragen
			$this->insert_module($insertid2);

			//Die Lookups für Menüpunkte eintragen
			if (is_array($this->checked->inhalt_ar['cattext_ar'])) {
				$sql = sprintf("DELETE FROM %s WHERE form_mid='%d'",
					$this->cms->tbname['papoo_form_manager_menu_lookup'],
					$this->db->escape($insertid2)
				);
				$this->db->query($sql);

				foreach ($this->checked->inhalt_ar['cattext_ar'] as $key => $value) {
					$sql = sprintf("INSERT INTO %s SET
												form_mid='%d',
												form_menu_id='%d'
												",
						$this->cms->tbname['papoo_form_manager_menu_lookup'],
						$this->db->escape($insertid2),
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}

			$this->content->template['ausgabe'] = $this->content->template['plugin']['form_manager']['eintrag_gespeichert'];
			$location_url = "./plugin.php?menuid=" . ($this->checked->menuid) . "&template=form_manager/templates/change_email.html";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
			} else {
				header("Location: $location_url");
			}
			exit;
		}

	}

	function insert_module($insertid2 = 0)
	{
		$mod_datei = "plugin:form_manager/templates/form_modul.html";
		$sql = sprintf("INSERT INTO %s SET mod_datei='%s' ",
			$this->cms->tbname['papoo_module'],
			$this->db->escape($mod_datei)
		);
		$this->db->query($sql);
		$insertid = $this->db->insert_id;

		//Sprachdaten rausholen
		$sql = sprintf("SELECT lang_id FROM %s",
			$this->cms->tbname['papoo_name_language']
		);
		$result_langdat = $this->db->get_results($sql, ARRAY_A);

		//Sprachdaten
		if (is_array($result_langdat)) {
			foreach ($result_langdat as $key => $value) {
				// 2. in die Tabelle eintragen
				$sql = sprintf("INSERT INTO %s
									SET modlang_mod_id='%d',
									modlang_lang_id='%d',
									modlang_name='%s',
									modlang_beschreibung='Formularmanager Modul'
									",
					$this->cms->tbname['papoo_module_language'],
					$insertid,
					$value['lang_id'],
					$this->db->escape($this->checked->form_manager_name)
				);
				$this->db->query($sql);
			}
		}

		$sql = sprintf("UPDATE %s SET  	form_manager_modul_id='%s' WHERE form_manager_id='%s'",
			$this->cms->tbname['papoo_form_manager'],
			$insertid,
			$insertid2
		);
		$this->db->query($sql);

		// 3. Modul-ID in Tabelle papoo_plugins eintragen (wird zum Löschen benötigt)
		$plugin_id = 0;
		$the_modulids = "";
		// .. Ermitteln der Plugin-ID des Plugins "Freie Module"
		$sql = sprintf("SELECT plugin_id FROM %s WHERE plugin_menuids LIKE '%% %d %%'",
			$this->cms->papoo_plugins,
			$this->checked->menuid
		);
		$plugin_id = $this->db->get_var($sql);

		// .. lesen der bisherigen modulids
		$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$plugin_id
		);
		$result = $this->db->get_var($sql);
		$the_modulids = $result . " " . $insertid . " ";

		// .. schreiben der neuen modulids
		$sql = sprintf("UPDATE %s SET plugin_modulids='%s' WHERE plugin_id='%d'",
			$this->cms->papoo_plugins,
			$this->db->escape($the_modulids),
			$plugin_id
		);
		$this->db->get_results($sql);
	}

	function get_liste_menu()
	{
		$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT");
		$this->content->template['menulist_data'] = $this->menu->data_front_complete;
	}

	/**
	 * Spracheinstellungen ausgeben
	 */

	function make_lang()
	{
		$this->get_liste_menu();

		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . " WHERE more_lang = '2'  ");
		// print_r($resultlang);
		$this->content->template['language_form'] = array();
		// zuweisen welche Sprache ausgewählt sind
		foreach ($resultlang as $rowlang) {
			// chcken wenn Sprache gewählt
			$selected_more = 'nodecode:checked="checked"';
			array_push($this->content->template['language_form'], array(
				'language' => $rowlang->lang_long,
				'lang_id' => $rowlang->lang_id,
				'selected' => $selected_more,

			));
		}
	}

	/**
	 * Spracheinstellungen der Gruppen ausgeben
	 */

	function make_lang_group()
	{
		// daher hier auch eine weitere Abfrage
		$resultlang = $this->db->get_results("SELECT * FROM " . $this->cms->papoo_name_language . " WHERE more_lang = '2'  ");
		// print_r($resultlang);
		$this->content->template['language_form'] = array();
		// zuweisen welche Sprache ausgewählt sind
		foreach ($resultlang as $rowlang) {
			// chcken wenn Sprache gewählt
			$selected_more = 'nodecode:checked="checked"';
			$text = "";
			// Wenn Editieren
			if (isset($this->checked->grupid) && is_numeric($this->checked->grupid)) {
				$sql = sprintf("SELECT plugin_cform_group_text FROM %s WHERE
											plugin_cform_group_lang_id='%d'
											AND plugin_cform_group_lang_lang='%d'",
					$this->cms->tbname['papoo_plugin_cform_group_lang'],
					$this->db->escape($this->checked->grupid),
					$rowlang->lang_id
				);
				$text = $this->db->get_var($sql);
			}
			array_push($this->content->template['language_form'], array(
				'language' => $rowlang->lang_long,
				'lang_id' => $rowlang->lang_id,
				'selected' => $selected_more,
				'plugin_cform_group_text' => $text,
			));
		}
	}

	/**
	 * form_manager::copy_form()
	 * Ein komplette Formular kopieren
	 *
	 * @return
	 */
	function copy_form($id = "")
	{
		// Temporarily disable CSRF protection
		$oldCsrfState = $this->db->csrfok;
		$this->db->csrfok = true;

		// Formular Daten
		$sql = sprintf("SELECT * FROM %s WHERE form_manager_id='%d'",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$sub_sql = "";
		if (is_array($result[0])) {
			foreach ($result[0] as $key => $value) {
				if ($key == "form_manager_name") {
					$value = "Kopie / Copy - " . $value;
				}
				if ($key == "form_manager_erstellt") {
					$value = time();
				}

				if ($key != "form_manager_id") {
					$sub_sql .= $key . '=\'' . $this->db->escape($value) . "', ";
				}
				if ($key == "form_manager_name") {
					$this->checked->form_manager_name = $value;
				}
			}
			$sub_sql = substr($sub_sql, 0, -2);
		}

		$sql = sprintf("INSERT INTO %s
							SET
							%s ",
			$this->cms->tbname['papoo_form_manager'],
			$sub_sql
		);
		$this->db->query($sql);
		$insertid = $this->db->insert_id;

		$insert_id_modul = $insertid;

		// Formular Sprachdaten
		$sql = sprintf("SELECT * FROM %s WHERE form_manager_id_id='%d'",
			$this->cms->tbname['papoo_form_manager_lang'],
			$this->db->escape($id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!empty($result)) {
			foreach ($result as $daten) {
				$sub_sql = "";
				if (is_array($daten)) {
					foreach ($daten as $key => $value) {
						if ($key != "form_manager_id_id") {
							$sub_sql .= $key . '=\'' . $this->db->escape($value) . "', ";
						}

					}
					$sub_sql = substr($sub_sql, 0, -2);
				}

				$sql = sprintf("INSERT INTO %s
										SET form_manager_id_id='%d',
										%s
										",
					$this->cms->tbname['papoo_form_manager_lang'],
					$insertid,
					$sub_sql
				);
				$this->db->query($sql);
			}
		}

		//Email Daten
		$sql = sprintf("SELECT * FROM %s WHERE form_manager_email_form_id ='%d'",
			$this->cms->tbname['papoo_form_manager_email'],
			$this->db->escape($id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$sub_sql = "";
		if (isset($result[0]) && is_array($result[0])) {
			foreach ($result[0] as $key => $value) {

				if ($key != "form_manager_email_form_id" && $key != "form_manager_email_id") {
					$sub_sql .= $key . '=\'' . $this->db->escape($value) . "', ";
				}
			}
			$sub_sql = substr($sub_sql, 0, -2);
		}
		if ($sub_sql !== "") {
			$sql = sprintf("INSERT INTO %s
							SET form_manager_email_form_id ='%d',
							%s ",
				$this->cms->tbname['papoo_form_manager_email'],
				$insertid,
				$sub_sql
			);
			$this->db->query($sql);
		}

		// Gruppen Daten
		$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_form_id='%d'",
			$this->cms->tbname['papoo_plugin_cform_group'],
			$this->db->escape($id)
		);
		$result = $this->db->get_results($sql);
		foreach ($result as $dat) {
			$sql = sprintf("INSERT INTO %s SET
					plugin_cform_group_form_id='%d',
					plugin_cform_group_order_id='%d',
					plugin_cform_group_name='%s'",
				$this->cms->tbname['papoo_plugin_cform_group'],
				$this->db->escape($insertid),
				$this->db->escape($dat->plugin_cform_group_order_id),
				$this->db->escape($dat->plugin_cform_group_name)
			);
			$this->db->query($sql);
			$insertid_gr = $this->db->insert_id;
			$groupid = $dat->plugin_cform_group_id;
			// Gruppen Sprachdaten
			$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_lang_id='%d'",
				$this->cms->tbname['papoo_plugin_cform_group_lang'],
				$this->db->escape($groupid)
			);
			$result = $this->db->get_results($sql);
			if ((!empty($result))) {
				foreach ($result as $value) {
					$sql = sprintf("INSERT INTO %s
												SET
												plugin_cform_group_lang_id='%s',
												plugin_cform_group_text='%s',
												plugin_cform_group_lang_lang='%s'",
						$this->cms->tbname['papoo_plugin_cform_group_lang'],
						$this->db->escape($insertid_gr),
						$this->db->escape($value->plugin_cform_group_text),
						$this->db->escape($value->plugin_cform_group_lang_lang)
					);
					$this->db->query($sql);
				}
			}
			// Felder Daten
			$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_group_id='%d'",
				$this->cms->tbname['papoo_plugin_cform'],
				$this->db->escape($groupid)
			);
			$result = $this->db->get_results($sql);
			foreach ($result as $dat) {
				$sql = sprintf("INSERT INTO %s SET
								plugin_cform_group_id='%d',
								plugin_cform_name='%s',
								plugin_cform_no_group='0',
								plugin_cform_must='%d',
								plugin_cform_form_id='%d',
								plugin_cform_type='%s',
								plugin_cform_order_id='%d',
								plugin_cform_content_type='%s',
								plugin_cform_minlaeng='%d'
								",
					$this->cms->tbname['papoo_plugin_cform'],
					$this->db->escape($insertid_gr),
					$this->db->escape($dat->plugin_cform_name),
					$this->db->escape($dat->plugin_cform_must),
					$this->db->escape($insertid),
					$this->db->escape($dat->plugin_cform_type),
					$this->db->escape($dat->plugin_cform_order_id),
					$this->db->escape($dat->plugin_cform_content_type),
					$this->db->escape($dat->plugin_cform_minlaeng)
				);
				$this->db->query($sql);
				$feldid = $dat->plugin_cform_id;
				$insertid_fd = $this->db->insert_id;
				// Feldersprachdaten
				$sql = sprintf("SELECT * FROM %s WHERE plugin_cform_lang_id='%d'",
					$this->cms->tbname['papoo_plugin_cform_lang'],
					$this->db->escape($feldid)
				);
				$result = $this->db->get_results($sql);

				foreach ($result as $dat) {
					$sql = sprintf("INSERT INTO %s
								SET
								plugin_cform_lang_id='%s',
								plugin_cform_lang_lang='%s',
								plugin_cform_label='%s',
								plugin_cform_content_list='%s',
								plugin_cform_descrip='%s',
								plugin_cform_tooltip='%s',
								plugin_cform_lang_header='%s'",
						$this->cms->tbname['papoo_plugin_cform_lang'],
						$this->db->escape($insertid_fd),
						$this->db->escape($dat->plugin_cform_lang_lang),
						$this->db->escape($dat->plugin_cform_label),
						$this->db->escape($dat->plugin_cform_content_list),
						$this->db->escape($dat->plugin_cform_descrip),
						$this->db->escape($dat->plugin_cform_tooltip),
						""
					);
					$this->db->query($sql);
				}
			}
		}

		// Revert CSRF protection to old state
		$this->db->csrfok = $oldCsrfState;

		$this->checked->form_manager_name =
			//Beim Kopieren auch ein Modul erzeugen
			$this->insert_module($insert_id_modul);
	}

	/**
	 * Ein Eintrag wird rausgeholt und bearbeitet und wieder eingetragen
	 */
	function change_entry()
	{
		if (isset($this->checked->form_manager_cop_id) && is_numeric($this->checked->form_manager_cop_id)) {
			$this->copy_form($this->checked->form_manager_cop_id);
		}
		$link2 = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=form_manager/templates/create_input.html";
		$this->content->template['fl_link_felder'] = $link2;
		$menid_message = $this->checked->menuid;
		$link3 = $_SERVER['PHP_SELF'] . "?menuid=" . $menid_message . "&template=form_manager/templates/messages.html";
		$this->content->template['fl_link_messages'] = $link3;

		$link4 = $_SERVER['PHP_SELF'] . "?menuid=" . $menid_message . "&template=form_manager/templates/create_email.html";
		$this->content->template['fl_link_create'] = $link4;

		$this->make_lang();
		// Es soll eingetragen werden
		if (isset($this->checked->formSubmit) && $this->checked->formSubmit) {
			//E-Mail Daten eintragen
			$email_array = explode("\n", $this->checked->form_manager_email);
			//Alte E-Mail Einträge löschen dieses Formulares
			$sql = sprintf("DELETE FROM %s
							WHERE form_manager_email_form_id='%d'",
				$this->cms->tbname['papoo_form_manager_email'],
				$this->db->escape($this->checked->form_manager_id)
			);
			$this->db->query($sql);
			//Neue E-Mails durchgehen und eintragen
			if (is_array($email_array)) {
				foreach ($email_array as $email) {
					if (strlen($email) > 2) {

						$email_dat = explode(";", $email);
						#print_r($email_dat);
						$sql = sprintf("INSERT INTO %s
										SET form_manager_email_form_id='%s',
										form_manager_email_email='%s',
										form_manager_email_name='%s'",
							$this->cms->tbname['papoo_form_manager_email'],
							$this->db->escape($this->checked->form_manager_id),
							$this->db->escape($email_dat[1]),
							$this->db->escape($email_dat[0])
						);
						$this->db->query($sql);
					}
				}
			}

			IfNotSetNull($this->checked->form_manager_antwort_yn);
			IfNotSetNull($this->checked->form_manager_anzeig_select_email);
			IfNotSetNull($this->checked->form_manager_name);
			IfNotSetNull($this->checked->form_manager_saleforce_yn);
			IfNotSetNull($this->checked->form_manager_saleforce_oid);
			IfNotSetNull($this->checked->form_manager_saleforce_action);
			IfNotSetNull($this->checked->form_manager_saleforce_debug);
			IfNotSetNull($this->checked->form_manager_loesch_dat1);
			IfNotSetNull($this->checked->form_manager_loesch_dat2);
			IfNotSetNull($this->checked->form_manager_saleforce_debug_email);
			IfNotSetNull($this->checked->form_manager_kategorie);

			$sql = sprintf("UPDATE %s
							SET form_manager_email='%s',
							form_manager_antwort_html='%s',
							form_manager_antwort_yn='%s',
							form_manager_anzeig_select_email='%s',
							form_manager_name='%s',
							form_manager_saleforce_yn='%s',
							form_manager_saleforce_oid='%s',
							form_manager_saleforce_action='%s',
							form_manager_saleforce_debug='%s',
							form_manager_loesch_dat1='%s',
							form_manager_loesch_dat2='%s',
							form_manager_saleforce_debug_email='%s',
							form_manager_kategorie='%s',
							form_manager_sender_mail='%s',
							form_manager_mail_settings_type='%s',
							form_manager_smtp_host='%s',
							form_manager_smtp_port='%s',
							form_manager_smtp_user='%s',
							form_manager_smtp_pass='%s',
							form_manager_geaendert='%s'
							WHERE form_manager_id='%s' ",
				$this->cms->tbname['papoo_form_manager'],
				"",
				"",
				$this->db->escape($this->checked->form_manager_antwort_yn),
				$this->db->escape($this->checked->form_manager_anzeig_select_email),
				$this->db->escape($this->checked->form_manager_name),
				$this->db->escape($this->checked->form_manager_saleforce_yn),
				$this->db->escape($this->checked->form_manager_saleforce_oid),
				$this->db->escape($this->checked->form_manager_saleforce_action),
				$this->db->escape($this->checked->form_manager_saleforce_debug),
				$this->db->escape($this->checked->form_manager_loesch_dat1),
				$this->db->escape($this->checked->form_manager_loesch_dat2),
				$this->db->escape($this->checked->form_manager_saleforce_debug_email),
				$this->db->escape($this->checked->form_manager_kategorie),
				$this->db->escape($this->checked->form_manager_sender_mail),
				$this->db->escape($this->checked->form_manager_mail_settings_type),
				$this->db->escape($this->checked->form_manager_smtp_host),
				$this->db->escape($this->checked->form_manager_smtp_port),
				$this->db->escape($this->checked->form_manager_smtp_user),
				$this->db->escape($this->checked->form_manager_smtp_pass),
				time(),
				$this->db->escape($this->checked->form_manager_id)
			);
			$this->db->query($sql);
			$this->content->template['ausgabe'] = $this->content->template['plugin']['form_manager']['eintrag_geaendert'];

			//Checken ob Modul vorhanden, wenn nicht eintragen
			$sql = sprintf("SELECT  	form_manager_modul_id FROM %s WHERE
							form_manager_id='%d'",
				$this->cms->tbname['papoo_form_manager'],
				$this->db->escape($this->checked->form_manager_id)
			);
			$is_modul = $this->db->get_var($sql);

			//Noch kein Modul vorhanden
			if (!$is_modul > 0) {
				$mod_datei = "plugin:form_manager/templates/form_modul.html";
				$sql = sprintf("INSERT INTO %s SET mod_datei='%s' ",
					$this->cms->tbname['papoo_module'],
					$this->db->escape($mod_datei)
				);
				$this->db->query($sql);
				$insertid = $this->db->insert_id;

				//Sprachdaten rausholen
				$sql = sprintf("SELECT lang_id FROM %s",
					$this->cms->tbname['papoo_name_language']
				);
				$result_langdat = $this->db->get_results($sql, ARRAY_A);

				//Sprachdaten
				if (is_array($result_langdat)) {
					foreach ($result_langdat as $key => $value) {
						// 2. in die Tabelle eintragen
						$sql = sprintf("INSERT INTO %s
										SET modlang_mod_id='%d',
										modlang_lang_id='%d',
										modlang_name='%s',
										modlang_beschreibung='Formularmanager Modul'
										",
							$this->cms->tbname['papoo_module_language'],
							$insertid,
							$value['lang_id'],
							$this->db->escape($this->checked->form_manager_name)
						);
						$this->db->query($sql);
					}
				}

				$sql = sprintf("UPDATE %s SET  	form_manager_modul_id='%s' WHERE form_manager_id='%s'",
					$this->cms->tbname['papoo_form_manager'],
					$insertid,
					$this->db->escape($this->checked->form_manager_id)
				);
				$this->db->query($sql);

				// 3. Modul-ID in Tabelle papoo_plugins eintragen (wird zum Löschen benötigt)
				$plugin_id = 0;
				$the_modulids = "";
				// .. Ermitteln der Plugin-ID des Plugins "Freie Module"
				$sql = sprintf("SELECT plugin_id FROM %s WHERE plugin_menuids LIKE '%% %d %%'",
					$this->cms->papoo_plugins,
					$this->checked->menuid
				);
				$plugin_id = $this->db->get_var($sql);

				// .. lesen der bisherigen modulids
				$sql = sprintf("SELECT plugin_modulids FROM %s WHERE plugin_id='%d'",
					$this->cms->papoo_plugins,
					$plugin_id
				);
				$result = $this->db->get_var($sql);
				$the_modulids = $result . " " . $insertid . " ";

				// .. schreiben der neuen modulids
				$sql = sprintf("UPDATE %s SET plugin_modulids='%s' WHERE plugin_id='%d'",
					$this->cms->papoo_plugins,
					$this->db->escape($the_modulids),
					$plugin_id
				);
				$this->db->get_results($sql);
			}

			//Die Lookups für Menüpunkte eintragen
			if (isset($this->checked->inhalt_ar['cattext_ar']) && is_array($this->checked->inhalt_ar['cattext_ar'])) {
				$sql = sprintf("DELETE FROM %s WHERE form_mid='%d'",
					$this->cms->tbname['papoo_form_manager_menu_lookup'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$this->db->query($sql);

				foreach ($this->checked->inhalt_ar['cattext_ar'] as $key => $value) {
					$sql = sprintf("INSERT INTO %s SET
												form_mid='%d',
												form_menu_id='%d'
												",
						$this->cms->tbname['papoo_form_manager_menu_lookup'],
						$this->db->escape($this->checked->form_manager_id),
						$this->db->escape($value)
					);
					$this->db->query($sql);
				}
			}
		}

		if (!empty($this->checked->form_manager_antwort_html)) {
			$sql = sprintf("DELETE FROM %s
										WHERE form_manager_id_id='%s'",
				$this->cms->tbname['papoo_form_manager_lang'],
				$this->db->escape($this->checked->form_manager_id)
			);
			$this->db->query($sql);
			foreach ($this->checked->form_manager_antwort_html as $key => $value) {

				if (empty($this->checked->form_manager_lang_button[$key])) {
					$this->checked->form_manager_lang_button[$key] = "Absenden";
				}

				$sql = sprintf("INSERT INTO %s
							SET form_manager_id_id='%s',
							form_manager_antwort_html='%s',
							form_manager_toptext_html='%s',
							form_manager_bottomtext_html='%s',
							form_manager_antwort_email_betreff='%s',
							form_manager_antwort_email='%s',
							form_manager_antwort_email_html='%s',
							form_manager_lang_button='%s',
							form_manager_lang_id='%s',
							mail_an_betreiber_betreff='%s',
							mail_an_betreiber_inhalt='%s'",
					$this->cms->tbname['papoo_form_manager_lang'],
					$this->db->escape($this->checked->form_manager_id),
					$this->db->escape($this->checked->form_manager_antwort_html[$key]),
					$this->db->escape($this->checked->form_manager_toptext_html[$key]),
					$this->db->escape($this->checked->form_manager_bottomtext_html[$key]),
					$this->db->escape($this->checked->form_manager_antwort_email_betreff[$key]),
					$this->db->escape($this->checked->form_manager_antwort_email[$key]),
					$this->db->escape($this->checked->form_manager_antwort_email_html[$key]),
					$this->db->escape($this->checked->form_manager_lang_button[$key]),
					$this->db->escape($key),
					$this->db->escape($this->checked->mail_an_betreiber_betreff[$key]),
					$this->db->escape($this->checked->mail_an_betreiber_inhalt[$key])
				);
				$this->db->query($sql);
			}
		}
		$link = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template;
		$this->content->template['fl_link'] = $link;

		if (!empty ($this->checked->form_manager_id)) {
			//Liste der Felder generieren
			$this->get_form_group_field_list();

			// Nach id aus der Datenbank holen
			$sql = sprintf("SELECT * FROM %s WHERE form_manager_id='%s'", $this->cms->tbname['papoo_form_manager'],
				$this->db->escape($this->checked->form_manager_id));
			$result = $this->db->get_results($sql);
			if (!empty ($result)) {
				foreach ($result as $spalte) {
					$this->content->template['form_manager_id'] = $spalte->form_manager_id;

					$this->content->template['form_manager_name'] = $spalte->form_manager_name;
					$this->content->template['form_manager_saleforce_yn'] = $spalte->form_manager_saleforce_yn;
					$this->content->template['form_manager_saleforce_oid'] = $spalte->form_manager_saleforce_oid;
					$this->content->template['form_manager_saleforce_action'] = $spalte->form_manager_saleforce_action;
					$this->content->template['form_manager_loesch_dat1'] = $spalte->form_manager_loesch_dat1;
					$this->content->template['form_manager_loesch_dat2'] = $spalte->form_manager_loesch_dat2;
					$this->content->template['form_manager_saleforce_debug'] = $spalte->form_manager_saleforce_debug;
					$this->content->template['form_manager_saleforce_debug_email'] = $spalte->form_manager_saleforce_debug_email;
					//form_manager_anzeig_select_email
					$this->content->template['form_manager_anzeig_select_email'] = $spalte->form_manager_anzeig_select_email;
					$this->content->template['form_manager_antwort_yn'] = $spalte->form_manager_antwort_yn;
					$this->content->template['edit'] = "ok";
					$this->content->template['altereintrag'] = "ok";
					$this->content->template['form_manager_link'] = "nodecode:plugin.php?menuid=1&template=form_manager/templates/form.html&form_manager_id=" . $spalte->form_manager_id;
					$this->content->template['form_manager_kategorie'] = $spalte->form_manager_kategorie;

					$this->content->template['form_manager_sender_mail'] = $spalte->form_manager_sender_mail;
					$this->content->template['form_manager_smtp_active'] = isset($spalte->form_manager_smtp_active) && $spalte->form_manager_smtp_active == 1 ? 'nodecode:checked="checked"' : '';
					$this->content->template['form_manager_mail_settings_type'] = $spalte->form_manager_mail_settings_type;
					$this->content->template['form_manager_smtp_host'] = $spalte->form_manager_smtp_host;
					$this->content->template['form_manager_smtp_port'] = $spalte->form_manager_smtp_port;
					$this->content->template['form_manager_smtp_user'] = $spalte->form_manager_smtp_user;
					$this->content->template['form_manager_smtp_pass'] = $spalte->form_manager_smtp_pass;
				}
				//E-Mail Daten rausholen
				$sql = sprintf("SELECT * FROM %s
							WHERE form_manager_email_form_id='%s' ORDER BY form_manager_email_id ASC",
					$this->cms->tbname['papoo_form_manager_email'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$resultmail = $this->db->get_results($sql, ARRAY_A);
				$mail_dat = "nobr:";

				//Check Modul
				$sql = sprintf("SELECT * FROM %s WHERE mod_id='%d'",
					DB_PRAEFIX . "papoo_module",
					$spalte->form_manager_modul_id);
				$result_mod = $this->db->query($sql);
				if (empty($result_mod)) {
					$this->checked->form_manager_name = $spalte->form_manager_name;
					$this->insert_module($spalte->form_manager_id);
				}

				//Lookups
				$sql = sprintf("SELECT * FROM %s WHERE  form_mid='%d'",
					$this->cms->tbname['papoo_form_manager_menu_lookup'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$this->content->template['form_men_ar'] = $this->db->get_results($sql, ARRAY_A);

				if (is_array($resultmail)) {
					foreach ($resultmail as $mail) {
						$mail_dat .= $mail['form_manager_email_name'] . ";" . $mail['form_manager_email_email'] . "\n";
					}
				}
				$this->content->template['form_manager_email'] = $mail_dat;

				$sql = sprintf("SELECT * FROM %s WHERE form_manager_id_id='%s'",
					$this->cms->tbname['papoo_form_manager_lang'],
					$this->db->escape($this->checked->form_manager_id)
				);
				$res2 = $this->db->get_results($sql);
				if (!empty($res2)) {
					foreach ($res2 as $ldat) {
						$lang = $ldat->form_manager_lang_id;
						$this->content->template['form_manager_antwort_html'][$lang] = "nobr:" . $ldat->form_manager_antwort_html;
						$this->content->template['form_manager_toptext_html'][$lang] = "nobr:" . $ldat->form_manager_toptext_html;
						$this->content->template['form_manager_bottomtext_html'][$lang] = "nobr:" . $ldat->form_manager_bottomtext_html;
						$this->content->template['form_manager_antwort_email_betreff'][$lang] = "nobr:" . $ldat->form_manager_antwort_email_betreff;
						$this->content->template['form_manager_antwort_email'][$lang] = "nobr:" . $ldat->form_manager_antwort_email;
						$this->content->template['form_manager_antwort_email_html'][$lang] = "nobr:" . $ldat->form_manager_antwort_email_html;
						$this->content->template['form_manager_lang_button'][$lang] = "nobr:" . $ldat->form_manager_lang_button;

						$this->content->template['mail_an_betreiber_betreff'][$lang] = "nobr:" . $ldat->mail_an_betreiber_betreff;
						$this->content->template['mail_an_betreiber_inhalt'][$lang] = "nobr:" . $ldat->mail_an_betreiber_inhalt;
					}
				}
			}
		} else {
			// Liste der Formulare
			$this->get_form_list();
		}
		// Soll  gelöscht werden
		if (!empty ($this->checked->submitdelecht)) {
			// Eintrag nach id löschen und neu laden
			$sql = sprintf("DELETE FROM %s WHERE form_manager_id='%s'", $this->cms->tbname['papoo_form_manager'],
				$this->db->escape($this->checked->form_manager_id));
			$this->db->query($sql);
			//Felder löschen nach plugin_cform_form_id
			$sql = sprintf("DELETE FROM %s WHERE plugin_cform_form_id='%s'", $this->cms->tbname['papoo_plugin_cform'],
				$this->db->escape($this->checked->form_manager_id));
			$this->db->query($sql);
			//Gruppierungen löschen nach plugin_cform_form_id
			$sql = sprintf("DELETE FROM %s WHERE plugin_cform_group_form_id='%s'",
				$this->cms->tbname['papoo_plugin_cform_group'], $this->db->escape($this->checked->form_manager_id));
			$this->db->query($sql);
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&template=" . $this->checked->template . "&fertig=del";
			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
			} else {
				header("Location: $location_url");
			}
			exit;
		}
		// Soll wirklich gelöscht werden?
		if (!empty ($this->checked->submitdel)) {
			$this->content->template['form_manager_email'] = $this->checked->form_manager_email;
			$this->content->template['form_manager_id'] = $this->checked->form_manager_id;
			$this->content->template['fragedel'] = "ok";
			$this->content->template['edit'] = "";
		}
	}

	/**
	 * Liste fer Formulare
	 */
	function get_form_list()
	{
		require_once(PAPOO_ABS_PFAD . "/plugins/form_manager/lib/form_get_form_list.php");
		$form_list = new form_get_form_list();
		$this->content->template['list'] = $form_list->get_form_liste();
	}

	/**
	 * Seite neu laden
	 */
	function reload($location_url = "")
	{
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['form_manager']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 *
	 * @abstract Bilder Liste erstellen
	 */
	function get_image_list()
	{
		$this->intern_artikel->get_images($this->cms->lang_id);
	}

	function helper_check_ssl()
	{
		if (!$_SERVER['HTTPS']) {
			$location_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			if ($_SESSION['debug_stopallredirect']) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header("Location: $location_url");
			}
			exit;
		}
	}

	/**
	 * @param string $tooltipContent Inhalt des Tooltips (auch mit HTML)
	 * @return string Fertiger Tooltip
	 */
	private function createTooltip($tooltipContent)
	{
		$tooltipContent = nl2br($tooltipContent);
		$tooltipContent = str_ireplace("\n", "", $tooltipContent);
		$tooltipContent = str_ireplace("\r", "", $tooltipContent);

		$tooltipTemplate = "<div style=\"display:inline-block;width:16px;height:16px;background-image:url('".PAPOO_WEB_PFAD ."/plugins/form_manager/bilder/info.gif');\" ".
			"data-tooltip aria-haspopup=\"true\" class=\"has-tip\" title=\"#popupContent#\"></div>";
		return str_replace(
			'#popupContent#',
			$this->diverse->encode_quote($tooltipContent),
			$tooltipTemplate
		);
	}

	/**
	 * @param string $name
	 * @return string|null Der Dateiname des Templates, wenn es im Style- oder Pluginverzeichnis gefunden wird, ansonsten null.
	 * @author Christoph Zimmer
	 */
	private static function findTemplate(string $name)
	{
		$pluginName = basename(dirname(__DIR__));
		$pathname = "plugins/$pluginName/templates/$name.html";

		return array_reduce(array(
			rtrim(PAPOO_ABS_PFAD, '/')."/styles/{$GLOBALS['cms']->style_dir}/templates/$pathname",
			rtrim(PAPOO_ABS_PFAD, '/')."/$pathname"
		), function ($carry, $item) {
			return $carry ?? (is_file($item) ? $item : $carry);
		}, null);
	}

	/**
	 * SMTP Einstellungen abfragen
	 *
	 * @param $formId int FormID to get the settings ofr
	 * @return array
	 */
	function getMailConfigByFormId(int $formId) : array
	{
		$sql = sprintf("SELECT
		 form_manager_mail_settings_type as `settingsType`,
		 form_manager_smtp_host as `host`,
		 form_manager_smtp_port as `port`,
		 form_manager_smtp_user as `user`,
		 form_manager_smtp_pass as `password`,
		 form_manager_sender_mail as `sender_mail`
		FROM %s WHERE form_manager_id='%s' LIMIT 1",
			$this->cms->tbname['papoo_form_manager'],
			$this->db->escape($formId)
		);
		return array_map('trim', $this->db->get_row($sql, ARRAY_A));
	}
}

$form_manager = new form_manager();
