<?php
/**
 * class faq
 * FAQ Plugin f�r Papoo
 * zus�tzlich wird die class faqsearcher eingebunden
 */
class faq {
	private $template;

	/**
	 * faq constructor.
	 */
	function __construct()
	{
		global $db, $db_praefix, $user, $message, $weiter, $content, $checked, $cms, $diverse,
			   $dumpnrestore, $faqsearcher, $inputfilter, $template, $mail_it, $cache, $intern_artikel;

		$this->faqsearcher = &$faqsearcher;
		$this->inputfilter = &$inputfilter;
		$this->diverse = &$diverse;
		$this->dumpnrestore = &$dumpnrestore;
		$this->content = &$content;
		$this->message = &$message;
		$this->weiter = &$weiter;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->checked = &$checked;
		$this->mail_it = &$mail_it;
		$this->cache = &$cache;
		$this->intern_artikel = &$intern_artikel;

		$this->template = preg_match('~.*/plugins/faq/templates/(.+)$~', $template, $match) ? $match[1] : '';

		if (!stristr( $template,"faq/") && $template != "indexint.html") {
			// raus, wenn nicht dieses Plugin
			return;
		}
		// f�r Suchen, Upload
		$this->pfadhier = PAPOO_ABS_PFAD;
		// akt. Men�-ID Pflege
		$this->content->template['menuid_aktuell'] = $this->checked->menuid;
		require (PAPOO_ABS_PFAD."/plugins/faq/lib/faqsearch_class.php"); // Suchen einbinden
		// sqllimit f�r das Paginating zur�cksetzen auf Papoo-internen Standardwert
		$_SESSION['suchanzahl'] = "";
		$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
		$this->cms->makecmslimit(); // setzt intern auf 20
		require ("inputfilter_class.php");
		$this->sanitize_inputs();  // Eingaben bereinigen
		// Backend, nur f�r den Admin. Frontend siehe post_papoo().
		if (defined("admin")) {
			$this->faq_get_write_config(0); // Konfigurationsdaten in die Session als Variablen einbringen
			$_SESSION['faq']['template_data'] = $this->checked; // Sichern der Eingabedaten
			// Reihenfolge f�r order_id SQL SELECT ORDER BY ...
			$this->content->template['faq_order'] = $_SESSION['faq']['FAQ_FAQ_ORDER'];
			// Submit Button "Suchen" gedr�ckt?
			if (isset($this->checked->find_faq) && $this->checked->find_faq) {
				// Auf die Suchergebnisseite springen (via faq_back_main.html), Suchwort �bergeben (via Link/Browserzeile)
				$template = "../../../plugins/faq/templates/faq_back_main.html";
				$this->content->template['search_faq'] = $this->checked->search_faq; // Suchwort zur�ckgeben
				// Bei erneuter Suche die erste Seite (weiter) und die ersten DB-S�tze forciert zeigen
				$this->cms->sqllimit = "LIMIT 0,20";
				$this->checked->page = 1; // paginating: aktuelle Seite 1 erzwingen
				// Suchwort(e) initialisieren
				$suchwort = $this->content->template['search_faq'] ? $this->content->template['search_faq'] : null;
			}
			else {
				// Suchen erneut nach Kategorieauswahl zur Anzeige der Fragen?
				if (isset($this->checked->search_faq) && $this->checked->search_faq) {
					// Suchwort(e) initialisieren
					$suchwort = $this->checked->search_faq ? $this->checked->search_faq : null;
					// aktuelle Seite (weiter) beim Suchen wiederherstellen, damit wir bei der Auswahl einer Antwort dort bleiben
					$this->content->template['page'] = ctype_digit($this->checked->page) ? $this->checked->page : 1;
					$faq_main_id = ctype_digit($this->checked->faq_main_id) ? $this->checked->faq_main_id : null;
					if ($faq_main_id) // Wurde eine Kategorie gew�hlt?{
						// Ausklappen und die gefundenen Fragen anzeigen
						// cat_id ans Template zur Anzeige der ausgew�hlten Kategorie (select-Kennzeichnung)
						$this->content->template['cat_selected_id']  = $this->checked->faq_main_id;
				}
			}
		}
		// Gemeinsames beim Suchen, egal ob per Button oder via Link
		if (isset($this->checked->find_faq) && $this->checked->find_faq OR isset($this->checked->search_faq) && $this->checked->search_faq) {
			// Suchen bei Suchwort-Eingabe
			if (isset($suchwort) && $suchwort) {
				// Suche ausf�hren
				$this->content->template['faq_search_data'] = $this->find_faq($suchwort);
			}
			else {
				$_SESSION['faq']['search_matches'] = 0;
			}
			$_SESSION['faq']['search_matches'] = ctype_digit((string)$_SESSION['faq']['search_matches']) ? $_SESSION['faq']['search_matches'] : 0;
			$this->content->template['search_matches'] = $_SESSION['faq']['search_matches']; // Anzahl matches
			$this->content->template['faq_anzahl'] = $this->count_faqs(); // Anzahl aller FAQs ans Template
			if (is_array($this->content->template['faq_search_data']) && !count($this->content->template['faq_search_data'])) {
				$this->content->template['faq_nomatch'] = 1;
			}
		}
		// Kein Suchen - alles andere jetzt
		else {
			if(defined("admin")) {
				// Sprache (z. B. f�r TinyMCE) setzen
				$this->intern_artikel->set_replangid();
				// Funktions-Aufrufe aufgrund der Templates
				switch ($this->template) {
					// Neue Kategorie anlegen
				case "faq_cat_back_new.html":
					$this->new_category();
					break;
					// Hauptseite Kategorien, Kategorien anzeigen
				case "faq_cat_back_main.html":
					$this->content->template['cat_data'] = array();
					$this->fetchAllCategories(0, ""); // Alle Kategoriedaten holen
					break;
					// Kategorien l�schen
				case "faq_cat_back_del.html":
					$this->del_category();
					break;
					// Kategorien bearbeiten
				case "faq_cat_back_edit.html":
					// Zu meiner Erinnerung: switch benutzt immer integers und loose comparison, daher "1+" vor dem String.
					// Das f�hrt zum Ergebnis 0 (kein submit) oder 1 (submit)
					// case muss wegen der doppelt auftretetenden 1 erneut gegen submit vergleichen
					// wobei auch ein Vergleich von Variablen vom selben Typ (durch ===) erforderlich ist
					IfNotSetNull($this->checked->submit);
					switch (1+(int)$this->checked->submit) {
						// Anzeige der Daten der zwecks multiple Edit gew�hlten Kategorien (switch case ergab 1)
					case ($this->checked->submit === $this->content->template['plugin']['faq_back']['submit']['cat_edit_select']): // Auswahl
						$this->edit_select_category();
						break 2;
						// Bearbeiten einer Kategorie (switch case ergab 1)
					case ($this->checked->submit === $this->content->template['plugin']['faq_back']['submit']['cat_edit']): // Speichern
						$this->edit_category(); // Kategorien bearbeiten
						break 2;
						// Anzeigen aller Kategorien (beim Aufruf ohne submit-Button; switch case ergab 0) oder
						// es wurde eine Kategorie aus der Liste f�r Edit ausgew�hlt (cat_edit_id = true)
					case ($this->checked->submit == 0): // integer-Vergleich, um case default zu vermeiden
						if (isset($this->checked->cat_edit_id) && $this->checked->cat_edit_id) {
							// Es wurde ein Link in faq_cat_back_main.html ausgew�hlt (nur eine Kategorie editieren)
							$this->edit_category();
							break 2;
						}
						// Links (Edit, alles ausw�hlen, etc.)
						$this->content->template['self'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
						// Toggle Selectboxen
						$this->content->template['checkeddel'] = !empty($this->checked->checkalldel) ? "checked='checked'" : "";
						break; // statt continue 2, um beim 1. Aufruf alle Kategorien anzuzeigen
					}
					$this->fetchAllCategories(0, ""); // Liste aller Kategorien anzeigen
					break;
					// Kategorien verschieben oder deren Reihenfolge �ndern
				case "faq_cat_back_move.html":
					if (isset($this->checked->submit) && $this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['cat_move']) {
						// Kategorien verschieben
						$this->move_category();
						break;
					}
					elseif (isset($this->checked->submit) && $this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['cat_order']) {
						// Reihenfolge der Kategorien �ndern
						$this->save_cat_order();
						break;
					}
					elseif (isset($this->checked->submit) && $this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['cat_copy']) {
						// Kategorien kopieren
						$this->copy_category();
						break;
					}
					$this->fetchAllCategories(0, "x"); // Liste der Kategorien anzeigen
					break;
					// Aufruf FAQ aus dem Men�: Kategorienliste anzeigen
				case "faq_back_main.html":
					$this->main_faq();
					break;
					// Neue FAQ erstellen
				case "faq_back_new.html":
					$this->new_faq();
					break;
					// FAQ bearbeiten
				case "faq_back_edit.html":
					$this->edit_faq_main();
					break;
					// FAQ Reihenfolge �ndern
				case "faq_back_renum.html":
					$this->save_faq_order();
					break;
					// Offene Fragen
				case "faq_back_offene.html":
					$this->faq_list_offene();
					break;
					// Gesperrte FAQs
				case "faq_back_release.html":
					$this->faq_list_release();
					break;
					// Vorschl�ge aus dem Frontend
				case "faq_back_new_frontend.html":
					$this->faq_new_faq_from_frontend();
					break;
					// Vorschl�ge aus dem Frontend �bernehmen/bearbeiten/l�schen
				case "faq_back_accept_faq.html":
					$this->edit_faq_main();
					break;
					// Offene Frage aus dem Frontend �bernehmen/bearbeiten/freigeben/l�schen (wird zur FAQ bei �bernahme)
				case "faq_back_accept_question.html":
					$this->edit_faq_main();
					break;
					// Konfiguration schreiben
				case "config_back.html":
					$this->faq_get_write_config(1);
					break;
					// Save / Restore
				case "backup_back.html":
					$this->faq_dump();
					break;

				case "faq_import.html": ;
				break;
					// Aufruf Plugin FAQ Backend (Anzeige Einbindung etc.)
				case "faq_back.html":
					break;
				default:
					break;
				}
			}
		}
	}

	/**
	 * Neue Kategorie anlegen (Backend)
	 * template faq_cat_back_new.html
	 *
	 * @property-read string $this->checked->cat_new_name_name Kategoriename
	 * @property-read string $this->checked->cat_new_sel_id Kategorie ID
	 * @property-read string $this->checked->cat_new_descript_name Kategorie Beschreibung
	 * @property-read string $this->checked->submit Submit-Button-value
	 * @property-read array|object $_SESSION['faq']['template_data'] gesicherte Eingabedaten
	 * @property-read string $this->cms->lang_back_content_id numerische Sprach ID
	 *
	 * @property-write array|mixed $this->content->template['cat_data'] Kategoriedaten
	 * @property-write integer $this->content->template['cat_is_new'] Schaltet die Erfolgsmeldung
	 * @property-write array|mixed $this->content->template Templatedaten Wiederherstellung
	 */
	function new_category()
	{
		// 1. Aufruf (Start).
		if (empty($this->checked->submit)) {
			//  Alle Kategoriedaten holen
			$this->fetchAllCategories(0, "");
			$_SESSION['faq']['template_data'] = "";
		}
		// Alles ok - speichern in die DB
		else {
			// Falls kein Kategoriename angegeben wurde:
			if (empty($this->checked->cat_new_name_name) AND $this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['cat_new']) {
				// Fehler (Kategoriename nicht angegeben). Eingabe-Daten vorerst ungepr�ft wiederherstellen
				$this->content->template['fehler1'] = $fehler = 1;
				$this->fetchAllCategories($this->checked->cat_new_sel_id, "");
			}
			if (!ctype_digit($this->checked->cat_new_sel_id)) {
				// Fehler (Kategorie ID fehlt/nicht numerisch)
				$this->content->template['fehler2'] = $fehler = 1;
				$this->fetchAllCategories(0, ""); // Kategorien anzeigen
			}
			if (!isset($fehler) || isset($fehler) && !$fehler) {
				// H�chste vorhandene order_id ermitteln und mit +$_SESSION['faq']['FAQ_RENUM_STEP'] addiert speichern
				// Da die neue Kategorie als Unterkategorie zur vorgegebenen Kategorie eingef�gt wird,
				// muss die n�chste order_id der max. order_id+$_SESSION['faq']['FAQ_RENUM_STEP'] von der parent_id entsprechen
				$sql = sprintf("SELECT MAX(id) FROM %s",DB_PRAEFIX."papoo_faq_categories");
				$max = $this->db->get_var($sql);
				$max++;

				//Get Languages of the system
				$sql = sprintf("SELECT * FROM %s",
					DB_PRAEFIX.'papoo_name_language');
				//print_r($sql);
				$result = $this->db->get_results($sql,ARRAY_A);
				$orderId=$this->getNextOrderId($this->checked->cat_new_sel_id, "", 0);

				foreach ($result as $k =>$v) {
					$sql = sprintf("INSERT INTO %s
								SET id='%d',
									catname = '%s',
									catdescript = '%s',
									parent_id = '%d',
									order_id = '%d',
									lang_id = '%d'",
						$this->cms->tbname['papoo_faq_categories'],
						$max,
						$this->db->escape($this->checked->cat_new_name_name),
						$this->db->escape($this->checked->cat_new_descript_name),
						$this->db->escape($this->checked->cat_new_sel_id),
						$this->db->escape($orderId),
						$this->db->escape($v['lang_id'])
					);
					$this->db->query($sql);
				}
				$this->fetchAllCategories($this->checked->cat_new_sel_id, ""); // Kategorien anzeigen & letzte Auswahl markieren
				$this->content->template['cat_is_new'] = 1; // Meldung �ber das erfolgreiche Anlegen einer neuen Kategorie
			}
			$this->content->template['cat_new_name_name'] = $this->nobr($this->checked->cat_new_name_name);
			$this->content->template['cat_new_descript_name'] = $this->nobr($this->checked->cat_new_descript_name);
		}
	}

	/**
	 * Anzeige der durch multiple Edit gew�hlten Kategoriedaten. (Backend)
	 * template faq_cat_back_edit.html
	 *
	 * @property-read array|string $this->checked->cat_edit_select Kategorie ID
	 * @property-read array|string $this->checked->cat_edit_name_name Kategoriename
	 * @property-read array|string $this->checked->cat_edit_descript_name Kategoriebeschreibung
	 * @property-read string $_SERVER['PHP_SELF'] string script-URL
	 * @property-read string $this->checked->template Template
	 * @property-read integer $this->checked->menuid Aktuelle Men� ID
	 *
	 * @property-write array|mixed $this->content->template['cats_data'] Kategoriedaten
	 * @property-write string $this->content->template['self'] URL zum Template
	 * @property-write integer $this->content->template['anzahl_faq_cats'] Anzahl aller Kategorien
	 */
	function edit_select_category()
	{
		if ($this->checkNumeric($this->checked->cat_edit_select)) {
			// Daten der ausgew�hlten Kategorien (checkboxen) bereitstellen
			foreach ($this->checked->cat_edit_select as $key => $edit) {
				$result = $this->getCatData($edit, 0, 0);
				$this->content->template['cats_data'][$key]['cat_edit_name_name'] = $this->nobr($result[0]['catname']); //Kategoriename
				$this->content->template['cats_data'][$key]['cat_edit_descript_name'] = $this->nobr($result[0]['catdescript']); // description
				$this->content->template['cats_data'][$key]['id'] = $result[0]['id']; // cat id
			}
			$this->content->template['anzahl_faq_cats'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
		}
		else {
			// (Noch) keine Auswahl oder nicht numerische Werte vorhanden, dann nur Formulardaten (erneut) bereitstellen
			$this->fetchAllCategories(0, "");
			$this->content->template['self'] =
				$_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
		}
	}

	/**
	 * Eine/mehrere ausgew�hlte Kategorie(n) wurde(n) durch Submit zur Bearbeitung angefordert. (Backend)
	 * template faq_cat_back_edit.html
	 *
	 * @property-read string $this->checked->cat_edit_id Id der zu bearbeitenden Kategorie
	 * @property-read array|string $this->checked->cats_edit_id IDs der zu bearbeitenden Kategorien
	 * @property-read string $this->checked->submit Submit-Button-value
	 * @property-read array|object $_SESSION['faq']['template_data'] gesicherte Eingabedaten
	 * @property-read string $this->checked->cat_move_to_id
	 * @property-read string $this->checked->cat_move_from_id
	 * @property-read string $this->cms->lang_back_content_id numerische Sprach ID
	 *
	 * @property-write integer $this->content->template['fehler1'] Fehler Kategoriename nicht angegeben
	 * @property-write integer $this->content->template['fehler2'] Fehler Inkorrekte Kategorie ID wurde �bermittelt
	 * @property-write string $this->content->template['cat_edit_name_name'] Kategoriename
	 * @property-write string $this->content->template['cat_edit_descript_name'] Kategorie Beschreibung
	 * @property-write integer $this->content->template['cat_move_from_id']
	 * @property-write integer $this->content->template['cat_edit_id'] Kategorie ID
	 * @property-write array|mixed $this->content->template['cat_data'] Kategoriedaten
	 * @property-write array|mixed $this->content->template Templatedaten
	 * @property-write integer $this->content->template['cat_is_edit'] Schaltet Erfolgsmeldung
	 * @property-write array|mixed $this->content->template['cats_data'] Kategoriedaten
	 *
	 */
	function edit_category()
	{
		$this->content->template['form_submitted'] = 1;
		// Entweder-oder, aber nicht beide (cat_edit_id: Kategorie-Id, wenn nur eine Kategorie bearbeitet wird,
		// ansonsten liegt array(cats_edit_id) vor
		if (isset($this->checked->cats_edit_id) && $this->checked->cat_edit_id AND is_array($this->checked->cats_edit_id) AND count($this->checked->cats_edit_id)) {
			$this->content->template['fehler2'] = $fehler = 1;
		}
		else {
			// eine davon sollte zumindest da sein
			if (isset($this->checked->cats_edit_id) && !is_array($this->checked->cats_edit_id) AND !$this->checked->cat_edit_id ||
				isset($this->checked->cats_edit_id) && is_array($this->checked->cats_edit_id) && !count($this->checked->cats_edit_id) AND !$this->checked->cat_edit_id) {
				$this->content->template['fehler2'] = $fehler = 1;
			}
			else {
				// numerischer check f�r die Bearbeitung mehrerer Kategorien
				if (isset($this->checked->cats_edit_id) && is_array($this->checked->cats_edit_id) && count($this->checked->cats_edit_id)) {
					if (!$this->checkNumeric($this->checked->cats_edit_id)) $this->content->template['fehler2'] = $fehler = 1;
				}
				else {
					// numerischer check der ID f�r die Bearbeitung einer Kategorie allein
					if (!ctype_digit($this->checked->cat_edit_id)) $this->content->template['fehler2'] = $fehler = 1;
				}
			}
		}
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			//  Ist nur eine bestimmte Kategorie ausgew�hlt?
			if ($this->checked->cat_edit_id) {
				// Ja, Daten einer per Link ausgew�hlten Kategorie anzeigen (Auswahlliste von faq_cat_back_main.html)
				if (empty($this->checked->submit)) {
					$result = $this->getCatData($this->checked->cat_edit_id, 0, 0);
					$this->content->template['cat_edit_name_name'] = $this->nobr($result[0]['catname']);
					$this->content->template['cat_edit_descript_name'] = $this->nobr($result[0]['catdescript']);
					$this->content->template['cat_move_from_id'] = $result[0]['id'];
					$this->content->template['cat_edit_id'] = $this->checked->cat_edit_id;
					//  Alle Kategoriedaten holen und Option-Eintr�ge der Selectbox f�llen
					$this->fetchAllCategories($this->checked->cat_edit_id, "");
				}
				// Die Daten einer bestimmten Kategorie �ndern (faq_cat_back_main.html, Linkparameter: $this->checked->cat_edit_id)
				else {
					// Falls kein Kategoriename angegeben wurde: Fehler ausl�sen:
					if (empty($this->checked->cat_edit_name_name)) {
						// Fehler (Kategoriename fehlt). Eingabe-Daten wiederherstellen
						$this->content->template['fehler1'] = 1;
						// Erneute Anzeige der zuletzt gemachten Eingaben
					}
					// Es wurden komplette Daten abgeschickt - speichern in die DB
					else {
						// numerische Pr�fung der IDs vor Ausf�hrung aller �nderungen
						if ($this->checked->cat_move_from_id AND !ctype_digit($this->checked->cat_move_from_id)) {
							$this->content->template['fehler2'] = $fehler = 1;
						}
						if ($this->checked->cat_move_to_id AND !ctype_digit($this->checked->cat_move_to_id)) {
							$this->content->template['fehler2'] = $fehler = 1;
						}
						// Falls sich die Kategorie ge�ndert hat, diese neu zuordnen, wenn kein Fehler
						if ($this->checked->cat_move_from_id != $this->checked->cat_move_to_id AND !isset($fehler) || isset($fehler) && !$fehler) {
							$this->move_category();
						}
						if (!isset($fehler) || isset($fehler) && !$fehler) {
							// Update nur dann, wenn der obige Fehler nicht aufgetreten ist
							// Daten hinzuf�gen
							$sql = sprintf("UPDATE %s
											SET catname = '%s',
												catdescript = '%s'
											WHERE id = '%d' AND lang_id ='%d'",
								$this->cms->tbname['papoo_faq_categories'],
								$this->db->escape($this->checked->cat_edit_name_name),
								$this->db->escape($this->checked->cat_edit_descript_name),
								$this->db->escape($this->checked->cat_edit_id),
								$this->db->escape($this->cms->lang_back_content_id)
							);
							$this->db->query($sql);
						}
						$this->content->template['cat_is_edit'] = 1; // Meldung Daten sind aktualisiert
					}
					// Daten ans Template zur�ckgeben
					$this->content->template['cat_edit_id'] = $this->checked->cat_edit_id;
					$this->content->template['cat_move_to_id'] = $this->checked->cat_move_to_id;
					$this->content->template['cat_move_from_id'] = $this->checked->cat_move_from_id;
					$this->content->template['cat_edit_name_name'] = $this->nobr($this->checked->cat_edit_name_name);
					$this->content->template['cat_edit_descript_name'] = $this->nobr($this->checked->cat_edit_descript_name);
					$this->fetchAllCategories($this->checked->cat_move_to_id, ""); // restore Kategorien plus Auswahl
				}
			}
			else {
				// Nein, es ist keine Kategorie via Link ausgew�hlt. Mehrere Kategoriedaten in die DB schreiben
				// Auswahl der zu bearbeitenden Kategorien
				if (count($this->checked->cats_edit_id)) {
					// alle ausgew�hlten in die DB
					foreach ($this->checked->cats_edit_id as $key => $value) {
						// Fehler ausl�sen, wenn der Name fehlt
						if (!empty($this->checked->cat_edit_name_name[$key])) {
							$sql = sprintf("UPDATE %s
											SET catname = '%s',
												catdescript = '%s'
											WHERE id = '%d' AND lang_id = '%d'",
								$this->cms->tbname['papoo_faq_categories'],
								$this->db->escape($this->checked->cat_edit_name_name[$key]),
								$this->db->escape($this->checked->cat_edit_descript_name[$key]),
								$this->db->escape($key),
								$this->db->escape($this->cms->lang_back_content_id)
							);
							$this->db->query($sql);
						}
						else {
							$this->content->template['cats_data'][$key]['fehler1'] = $this->content->template['fehler1'] = 1;
						}
						// Daten ans Template zur�ckgeben
						$this->content->template['cats_data'][$key]['cat_edit_name_name'] =
							$this->nobr($this->checked->cat_edit_name_name[$key]);
						$this->content->template['cats_data'][$key]['cat_edit_descript_name'] =
							$this->nobr($this->checked->cat_edit_descript_name[$key]);
						$this->content->template['cats_data'][$key]['id'] = $key;
					}// Gesamtanzahl aller Kategorien bereitstellen (= "", 1)
					$this->content->template['anzahl_faq_cats'] = $this->read_count_all_categories("", 1);
				}
			}
		}
	}

	/**
	 * Kategorie(n) verschieben (Backend)
	 * template faq_cat_back_move.html
	 * template faq_cat_back_edit.html ( caller: edit_category() )
	 *
	 * @return mixed|void
	 */
	function move_category()
	{
		global $fehler;
		// Fehler, wenn die zu verschiebende Kategorie nicht ausgew�hlt ist
		if (!ctype_digit($this->checked->cat_move_from_id)) {
			$this->content->template['fehler1'] = $fehler = "x";
		}
		// Fehler, wenn nicht ausgew�hlt ist, wohin verschoben werden soll
		if (!ctype_digit($this->checked->cat_move_to_id)) {
			$this->content->template['fehler2'] = $fehler = "x";
		}
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			// Fehler, wenn Ziel und Quelle gleich sind. Auf "" vergleichen wegen id=0 f�r die Hauptkategorie im Ziel
			if ($this->checked->cat_move_from_id and $this->checked->cat_move_to_id != "") {
				if ($this->checked->cat_move_from_id ==
					$this->checked->cat_move_to_id) $this->content->template['fehler3'] = $fehler = "x";
			}
			// Fehler, wenn zur gleichen Unterkategorie verschoben werden soll (das Ziel ist der Quelle bereits untergeordnet)
			$parent_id_from = $this->getParentId($this->checked->cat_move_from_id);
			if ($parent_id_from == $this->checked->cat_move_to_id) {
				$this->content->template['fehler4'] = $fehler = "x";
			}
		}
		IfNotSetNull($parent_id_from);
		// Aktionen im Fehlerfall
		if (isset($fehler) && $fehler) {
			// Eingabe-Daten wiederherstellen (beide Auswahlen) und Kategorienliste bereitstellen
			$this->fetchAllCategories($this->checked->cat_move_from_id . $this->content->template['fehler1'],
				$this->checked->cat_move_to_id . $this->content->template['fehler2']);
		}
		else {
			// Quelle erh�lt als parent_id immer die id des Ziels (wird zur Unterkategorie der Zielkategorie)
			// Da die neue Kategorie als Unterkategorie zur vorgegebenen Kategorie eingef�gt wird,
			// muss die n�chste order_id der max. order_id+$_SESSION['faq']['FAQ_RENUM_STEP'] von der parent_id entsprechen
			$sql = sprintf("UPDATE %s
							SET parent_id ='%d',
								order_id = '%d'
							WHERE id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_categories'],
				$this->db->escape($this->checked->cat_move_to_id),
				$this->db->escape($this->getNextOrderId($this->checked->cat_move_to_id, "", 0)),
				$this->db->escape($this->checked->cat_move_from_id),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->db->query($sql);
			$this->content->template['cat_is_moved'] = 1; // Meldung �ber die Aktualisierung
			// Feststellen, ob in die eigene Kategorie mit einer niedrigeren oder der gleichen Ebene verschoben werden soll
			$found = $this->checkOwnTree($this->checked->cat_move_from_id, $this->checked->cat_move_to_id);
			// Ja, Verschiebung in die eigene Kategorie (parent wird zum child, child zum parent).
			// Zeiger der vorherigen Unterkategorien neu setzen.
			if ($found) {
				// Da die neue Kategorie als Unterkategorie zur vorgegebenen Kategorie eingef�gt wird,
				// muss die n�chste order_id der max. order_id+$_SESSION['faq']['FAQ_RENUM_STEP'] von der parent_id entsprechen
				$sql = sprintf("UPDATE %s
								SET parent_id ='%d',
									order_id = '%d'
								WHERE parent_id = '%d' AND lang_id ='%d'",
					$this->cms->tbname['papoo_faq_categories'],
					$this->db->escape($parent_id_from),
					$this->db->escape($this->getNextOrderId($this->checked->cat_move_to_id, "", 0)),
					$this->db->escape($this->checked->cat_move_from_id),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$this->db->query($sql);
			}
			// Renumber childs von Quelle und Ziel
			$this->cat_renumber($parent_id_from, $this->checked->cat_move_to_id);
			// Alle Kategorien erneut anzeigen (Quelle und Ziel)
			$this->fetchAllCategories($this->checked->cat_move_from_id, $this->checked->cat_move_to_id);
		}
	}

	/**
	 * Kategorie(n) kopieren (Backend)
	 * template faq_cat_back_move.html
	 *
	 * @return mixed|void
	 */
	function copy_category()
	{
		// Fehler, wenn die zu kopierende Kategorie nicht ausgew�hlt ist
		if (!ctype_digit($this->checked->cat_copy_from_id)) {
			$this->content->template['fehler5'] = $fehler = "x";
		}
		// Fehler, wenn nicht ausgew�hlt ist, wohin kopiert werden soll
		if (!ctype_digit($this->checked->cat_copy_to_id)) {
			$this->content->template['fehler6'] = $fehler = "x";
		}
		// Aktionen im Fehlerfall
		if (isset($fehler) && $fehler) {
			// Eingabe-Daten wiederherstellen (beide Auswahlen) und Kategorienliste bereitstellen
			$this->fetchAllCategories($this->checked->cat_copy_from_id . $this->content->template['fehler5'],
				$this->checked->cat_copy_to_id . $this->content->template['fehler6']);
		}
		else {
			$copy_of = "";
			// Zusatz, wenn Ziel und Quelle gleich sind. Auf "" vergleichen wegen id=0 f�r die Hauptkategorie im Ziel
			if ($this->checked->cat_copy_from_id and $this->checked->cat_copy_to_id != "") {
				if ($this->checked->cat_copy_from_id == $this->checked->cat_copy_to_id) {
					$copy_of = "Kopie von ";
				}
			}
			// Zusatz, wenn zur gleichen Unterkategorie kopiert werden soll (das Ziel ist der Quelle bereits untergeordnet)
			if ($this->getParentId($this->checked->cat_copy_from_id) == $this->checked->cat_copy_to_id) {
				$copy_of = "Kopie von ";
			}
			if ($this->checkOwnTree($this->checked->cat_copy_from_id, $this->checked->cat_copy_to_id)) {
				$copy_of = "Kopie von ";
			};
			// Hole die Unterverzeichnisse des zu kopierenden parents
			$tree = $this->categoriesTree($this->getCatData("", 1, 0), $this->checked->cat_copy_from_id);
			$this->cat_Tree = array();
			// Hole die Kategoriedaten des zu kopierenden parents
			$result = $this->getCatData($this->checked->cat_copy_from_id, 0, 0);
			// Einf�gen des parents in den Baum als 1. Element
			if (count($tree)) {
				$tree = array_merge($tree, $result);
			}
			else {
				$tree = $result;
			}
			// Zufallswert. Wird bei den Inserts genutzt, um die eingef�gten S�tze beim UPDATE exakt ansprechen zu k�nnen
			$criteria = mt_rand();
			// Korrigieren der parent_id
			if ($copy_of) {
				$tree[count($tree)-1]['parent_id'] = $criteria;
			}
			else {
				$tree[count($tree)-1]['parent_id'] = $this->checked->cat_copy_to_id;
			}
			IfNotSetNull($parentid);
			// Den Baum in die DB schreiben
			for ($i=0; $i < count($tree); $i++) {
				$sql = sprintf("INSERT INTO %s
								SET catname = '%s',
									catdescript = '%s',
									parent_id = '%d',
									lang_id = '%d'",
					$this->cms->tbname['papoo_faq_categories'],
					$this->db->escape($copy_of.$tree[$i]['catname']),
					$criteria,
					$this->db->escape($tree[$i]['parent_id']),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$this->db->query($sql);
				// Die neuen IDs merken
				$parentid[$i] = $this->db->insert_id;
				// Die zu dieser Kategorie geh�renden FAQs mit der neuen Kategorie verkn�pfen
				// Hole alle zu dieser Kategorie geh�renden FAQs mit faq_id's und order_id
				$sql = sprintf("SELECT MAX(version_id) version_id, faq_id, order_id
								FROM %s
								WHERE cat_id = '%d'
								GROUP BY faq_id",
					$this->cms->tbname['papoo_faq_cat_link'],
					$this->db->escape($tree[$i]['id'])
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				// Speichern der Relationen aller FAQs zu dieser Kategorie
				if (count($result)) {
					foreach ($result as $key =>$value) {
						// Relation zwischen neuer Kategorie und bestehender FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
										faq_id = '%s',
										order_id = '%d'",
							$this->cms->tbname['papoo_faq_cat_link'],
							$this->db->escape($parentid[$i]),
							$this->db->escape($result[$key]['faq_id']),
							$this->db->escape($result[$key]['order_id'])
						);
						$this->db->query($sql);
					}
				}
			}
			// Die alten parent_id-Werte mit den neuen ID-Werten ersetzen und auch den Zufallswert wieder entfernen
			for ($i=0; $i < count($tree); $i++) {
				// Hinweis: Da mehrere S�tze auf einmal bearbeitet werden, kann sich die Reihenfolge (order_id) �ndern
				// S�tze, die bei erf�llter Bedingung gefunden werden, erhalten die alte order_id
				// Abhilfe, falls erforderlich: jeden Satz einzeln verarbeiten
				$sql = sprintf("UPDATE %s
								SET parent_id ='%d',
									catdescript = '%s',
									order_id ='%d'
								WHERE parent_id = '%d' AND catdescript = '%d' AND lang_id = '%d'",
					$this->cms->tbname['papoo_faq_categories'],
					$this->db->escape($parentid[$i]),
					$this->db->escape($tree[$i]['catdescript']),
					$this->db->escape($tree[$i]['order_id']),
					$this->db->escape($tree[$i]['id']),
					$criteria,
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$this->db->query($sql);
				// Nochmal durchnummerieren (s. Hinweis oben)
				$this->cat_renumber($parentid[$i], 0);
			}
			// Beim neuen parent die parent_id setzen
			// Da die neue Kategorie als Unterkategorie zur vorgegebenen Kategorie eingef�gt wird,
			// muss die n�chste order_id der max. order_id+$_SESSION['faq']['FAQ_RENUM_STEP'] von der parent_id entsprechen
			$sql = sprintf("UPDATE %s
							SET parent_id ='%d',
								catdescript = '%s',
								order_id ='%d'
							WHERE parent_id = '%d' AND catdescript = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_categories'],
				$this->db->escape($this->checked->cat_copy_to_id),
				$this->db->escape($tree[count($tree)-1]['catdescript']),
				$this->db->escape($this->getNextOrderId($this->checked->cat_copy_to_id, "", 0)),
				$criteria,
				$criteria,
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->db->query($sql);
			$this->cat_renumber($this->checked->cat_copy_to_id, 0); // Neu nummerieren
			$this->fetchAllCategories($this->checked->cat_copy_from_id, $this->checked->cat_copy_to_id); // Kat.daten holen
			$this->content->template['cat_is_copied'] = 1; // Meldung �ber Aktualisierung
		}
	}

	/**
	 * Kategorie(n) l�schen (Backend)
	 * template faq_cat_back_del.html
	 *
	 * @return mixed|void
	 */
	function del_category()
	{
		// Submit-Button aktiv? (Entfernen oder positive Antwort auf die L�schabfrage
		if (isset($this->checked->submit) && $this->checked->submit) {
			// Auswahl(en) in checkboxen?
			if ($this->checkNumeric($this->checked->cat_delete)) {
				// Schalter: bei catdelete = true ist die L�schabfrage noch nicht erfolgt
				if (isset($this->checked->catdelete) && $this->checked->catdelete) {
					$this->content->template['delete'] = 1;
					$this->content->template['cat_delete'] = $this->checked->cat_delete;
				}
				else {
					// L�schen? Ja / Nein
					if ($this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['delete_yes']) {
						// Verwaiste Eintr�ge kennzeichnen
						$this->content->template['cat_data'] = array();
						foreach ($this->checked->cat_delete as $id) {
							$this->content->template['cat_data'] =
								array_merge($this->content->template['cat_data'], $this->getCatData($id, 1, 0));
							if (isset($this->checked->cats_orphan[$id]) && $this->checked->cats_orphan[$id]) {
								$this->content->template['cat_data'][]['orphan'] = 1;
							}
						}
						// Die in den checkboxen ausgew�hlten l�schen
						foreach ($this->checked->cat_delete as $key => $del) {
							// Kategoriedaten l�schen
							$sql = sprintf("DELETE FROM %s
											WHERE id='%s' AND lang_id = '%d'",
								$this->cms->tbname['papoo_faq_categories'],
								$this->db->escape($del),
								$this->db->escape($this->cms->lang_back_content_id)
							);
							$this->db->query($sql);
							$this->cat_renumber($del, 0);
							// Relation Kategorie/FAQ l�schen
							$sql = sprintf("DELETE FROM %s WHERE cat_id='%s'",
								$this->cms->tbname['papoo_faq_cat_link'],
								$this->db->escape($del)
							);
							$this->db->query($sql);
							$this->content->template['cat_is_del'] = 1; // Fertig-Meldung
						}
					}
				}
			}
			elseif (isset($this->checked->cat_delete)) $this->content->template['fehler1'] = 1;
		}
		// Pfad etc. bereitstellen
		$this->content->template['self'] = $_SERVER['PHP_SELF']."?menuid=".$this->checked->menuid."&amp;template=".$this->checked->template;
		// Toggle Selectboxen (Vorbelegung der checkboxen alle/nix)
		$this->content->template['checkeddel'] = !empty($this->checked->checkalldel) ? "checked='checked'" : "";
		$this->fetchAllCategories(0, ""); // Kategoriedaten erneut holen
	}

	/**
	 * Hole parent_id zu einer cat_id (Backend)
	 * called by move_category(), copy_category(), checkOwnTree()
	 *
	 * @param int $cat_id
	 * @return array|null
	 */
	function getParentId($cat_id = 0)
	{
		$sql = sprintf("SELECT parent_id
						FROM %s
						WHERE id = '%d' AND lang_id = '%d'",
			$this->cms->tbname['papoo_faq_categories'],
			$this->db->escape($cat_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		return ($this->db->get_var($sql));
	}

	/**
	 * Hole alle childs zu einer Kategorie-id (Backend)
	 * called by cat_renumber()
	 *
	 * @param int $cat_id
	 * @return array|void
	 */

	function getChilds($cat_id = 0)
	{
		$sql = sprintf("SELECT id, parent_id
						FROM %s
						WHERE parent_id = '%d' AND lang_id = '%d'
						ORDER BY order_id",
			$this->cms->tbname['papoo_faq_categories'],
			$this->db->escape($this->cms->lang_back_content_id),
			$this->db->escape($cat_id)
		);
		return ($this->db->get_results($sql, ARRAY_A));
	}

	/**
	 * Alle Kategorien ans Template mit Baumstruktur �bergeben und Daten zur Listensteuerung f�rs Template erzeugen
	 * (Backend, Frontend)
	 * used for templates faq_cat_back_main.html, faq_cat_back_edit.html, faq_cat_back_move.html
	 * called by copy_category(), del_category(), edit_category(), edit_faq_main(), edit_select_category(), faq_front,
	 *            faq_front_answer_a_question, faq_redisplay_postdata(), find_faq(), main_faq(), move_catagory(), new_category(),
	 *            new_faq(), save_cat_order(), save_faq_order()
	 * $selected und $selectedto: ber�cksichtigen die gemachte(n) Auswahl(en) und liefern diese zur�ck mit Kennzeichnung "selected"
	 * active:
	 *
	 * @param string $selected
	 * @param string $selectedto
	 * @param int $active
	 * @param string $get_single_cat
	 * @return void
	 */
	function fetchAllCategories($selected ="", $selectedto = "", $active = 0,$get_single_cat="")
	{
		$result = $this->getCatData($get_single_cat, 1, $active);
		// Baumstruktur erstellen, dazu die Level, parents, childs und Anzahl der n�tigen ul/li-Endetags ans Template
		// Das gibt dem Template die M�glichkeit die ul-li-Struktur komplett zu erstellen (s. z. B. faq_cat_back_main.html)
		// Verwaiste Eintr�ge kennzeichnen (entstanden durch delete des parents)
		if (count($result)) {
			// Kategorien mit Baumstruktur erstellen
			// Es kommen nur nicht verwaiste Eintr�ge durch categoriesTree nach $cat_tree_data
			// In $result haben wir dann alle Eintr�ge
			$cat_tree_data = $this->categoriesTree($result);
			// Verwaiste Eintr�ge suchen und kennzeichnen
			foreach ($result as $key => $value) {
				$found = 1; // Verwaist
				foreach ($cat_tree_data as $key2 => $value2) {
					if ($result[$key]['id'] == $cat_tree_data[$key2]['id']) {
						// Eintrag im Baum ist nicht verwaist
						$found = 0;
						$cat_tree_data[$key2]['orphan'] = 0;
						break;
					}
				}
				if ($found) {
					// Verwaisten Eintrag gefunden und kennzeichnen
					$temp[$key] = $result[$key];
					$temp[$key]['orphan'] = 1;
				}
			}
			// Baumstruktur ans Template
			$this->content->template['cat_data'] = $cat_tree_data;
			// Zus�tzlich die verwaisten Eintr�ge anh�ngen
			if (isset($temp) && is_array($temp) && count($temp)) {
				$this->content->template['cat_data'] = array_merge($this->content->template['cat_data'], $temp);
			}
			$this->cat_Tree = array(); // Clear
			$this->content->template['anzahl_faq_cats'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
			// Listenstruktur erstellen (Kennzeichnung parent mit childs, last child und Anzahl ul-/li-close-Tags ermitteln)
			for ($i = 0; $i < $this->content->template['anzahl_faq_cats']; $i++) {
				// Parent ermitteln
				if (isset($this->content->template['cat_data'][$i]) && isset($this->content->template['cat_data'][$i+1]) &&
					$this->content->template['cat_data'][$i]['level'] < $this->content->template['cat_data'][$i+1]['level']) {
					// Es geht einen Level tiefer im n�chsten Eintrag
					$this->content->template['cat_data'][$i]['parent'] = 1; // parent kennzeichnen
					$this->content->template['cat_data'][$i]['close_tags'] = 0; // 1 Level runter, also jetzt keine close-tags
				}
				else {
					IfNotSetNull($this->content->template['cat_data'][$i]);
					$nextItemLevel = $this->content->template['cat_data'][$i+1]['level'] ?? 0;
					// Es geht einen oder mehrere Level h�her. Anzahl der close-Tags berechnen und �bergeben.
					$this->content->template['cat_data'][$i]['close_tags'] =
						$this->content->template['cat_data'][$i]['level'] - $nextItemLevel;
					// es geht rauf, also ist dies kein parent (hat keine Unterkategorien)
					$this->content->template['cat_data'][$i]['parent'] = 0;  // Kein parent
				}
				// Lastchild ermitteln. Ab Position $i den Rest durchsuchen
				// Dieser Wert l�st dann zusammen mit dem Wert in 'close_tags' die Ausgabe aller /ul-/li's im Template aus
				for ($i2 = $i + 1; $i2 <= count($this->content->template['cat_data']); $i2++) {
					$found = 0;
					// TODO chck ist nicht Test auf > allein schon m�glich?
					IfNotSetNull($this->content->template['cat_data'][$i]['level']);
					$i2Level = $this->content->template['cat_data'][$i2]['level'] ?? 0;
					if ($this->content->template['cat_data'][$i]['level'] <= $i2Level) {
						if ($this->content->template['cat_data'][$i]['level'] == $i2Level) {
							// Es wurde derselbe Level noch einmal gefunden, also ist das aktuelle Child kein Lastchild
							$this->content->template['cat_data'][$i]['close_tags'] = 0; // hier jetzt keine close-Tags
							$found = 0; // kein last child
							break;
						}
					}
					// Lastchild, da eine h�here Levelstufe gefunden wurde
					else {
						$found = 1; // last child
						break;
					}
				}
				// lastchild Wert ans Template �bergeben
				$this->content->template['cat_data'][$i]['lastchild'] = $found;
			}
			// Liste Ziel mit denselben Kategoriedaten f�llen
			if ($selectedto != "") {
				$this->content->template['cat_data2'] = $this->content->template['cat_data'];
			}
			// Ausgew�hlte Kategorie(n) von der Quelle merken und wieder anzeigen
			if (is_array($selected)) {
				// Multiselect
				foreach ($selected as $sel_data) {
					$i = 0;
					foreach ($this->content->template['cat_data'] as $template_data) {
						if ($template_data['id'] == $sel_data['cat_id']) {
							$this->content->template['cat_data'][$i]['cat_selected'] = 1; // selected
							$this->content->template['cat_name'] = $this->content->template['cat_data'][$i]['catname'];
							break;
						}
						$i++; // check next
					}
				}
			}
			else {
				// Nur eine ausgew�hlt
				if ($selected != "") {
					$i = 0;
					foreach ($this->content->template['cat_data'] as $data) {
						if ($data['id'] == $selected) {
							$this->content->template['cat_data'][$i]['cat_selected'] = 1; // selected
							$this->content->template['cat_name'] = $this->content->template['cat_data'][$i]['catname'];
							break; // raus, nur eine Auswahl ist m�glich/erlaubt
						}
						else {
							$this->content->template['cat_data'][$i]['cat_selected'] = 0;
						} // nicht ausgew�hlt
						$i++; // check next
					}
				}
			}
			// Ausgew�hlte Ziel-Kategorie merken und wieder anzeigen
			// Beide Routinen k�nnten zu einer zusammengefasst werden. Vorerst noch getrennt.
			// Bisheriger einziger Unterschied: Hauptkategorie im Ziel
			if ($selectedto != "") {
				// Hauptkategorie als Ziel ausgew�hlt?
				if ($this->checked->cat_move_to_id == "0") {
					$this->content->template['cat_0_selected'] = 1;
				} // ja, dann fertig
				else {
					// Ausgew�hlte Kategorie finden und kennzeichnen.
					$i = 0;
					foreach ($this->content->template['cat_data2'] as $data) {
						if ($data['id'] == $selectedto) {
							$this->content->template['cat_data2'][$i]['cat_selected'] = 1; // ausgew�hlt
							$this->content->template['cat_name2'] = $this->content->template['cat_data2'][$i]['catname'];
							break;
						}
						else $this->content->template['cat_data2'][$i]['cat_selected'] = 0; // nicht ausgew�hlt
						$i++;
					}
				}
			}
			// Anzahl der im Frontend anzuzeigenden Kategorien, wie in der Konfig festgelegt
			if (!defined("admin")) {
				$sql = sprintf("SELECT COUNT(*)
								FROM %s
								WHERE lang_id ='%d'",
					$this->cms->tbname['papoo_faq_categories'],
					$this->db->escape($this->cms->lang_id)
				);
				$result = $this->db->get_var($sql);
				$this->weiter->result_anzahl = $result;
				$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_CATS_PER_PAGE'];
				$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
				$this->cms->makecmslimit();
				// Wenn weitere Ergebnisse angezeigt werden k�nnen
				$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
					"&amp;template=faq/templates/faq_front.html";
				$this->weiter->do_weiter("teaser");
				//$this->content->template['menuid'] = $this->checked->menuid;
				//$this->content->template['template'] = $this->checked->template;
				$start =
					ctype_digit($this->checked->page) ? ($this->checked->page - 1) * $_SESSION['faq']['FAQ_CATS_PER_PAGE'] : 0;
				if (count($this->content->template['cat_data']) <= $start) $start = 0; // f�r page > 1 (Linkliste 4)
				$this->content->template['cat_data'] =
					array_slice($this->content->template['cat_data'], $start, $_SESSION['faq']['FAQ_CATS_PER_PAGE']);
			}
		}
		else {
			$this->content->template['anzahl_faq_cats'] = 0; // es gibt keine Kategorien
			$this->content->template['cat_data'] = [];
		}
	}

	/**
	 * Beziehung FAQ / Kategorien speichern und bedingt eine neue Version erstellen
	 * called by edit_faq_main(), faq_accept_question(), faq_edit(), faq_front_answer_a_question(), new_faq()
	 *
	 * @param string $faq_id
	 * @param int $version_id ist 0 f�r eine neue FAQ (nur BE)
	 *         oder enth�lt die aktuelle Versions ID bei Att. Upload/Delete und FAQ Edit (beides FE/BE)
	 * @return mixed
	 */
	function saveFaqCatLink($faq_id = "", $version_id = 0)
	{
		// Beim Upload/Delete eines Attachments ist der Submit-Button zum Speichern einer FAQ submit[4] nicht gesetzt
		// new_version_inwork ist beim Upload/Delete des 1. Attachments nicht gesetzt, jedoch danach immer
		// Beim Speichern der FAQ ist also new_version_inwork nur nach einem Attachment Upload/Delete gesetzt
		// und verhindert das Anlegen einer weiteren Version, wenn vorher mind. ein Upload/Delete gemacht wurde
		// Beim 1. Upload wird eine neue Version erstellt,
		// bei weiteren Uploads/Deletes und/oder beim nachfolgenden Speichern wird keine weitere Version erzeugt

		// Es wurde im FE noch keine neue Version angelegt oder es soll im BE eine neue FAQ angelegt werden (Version 0)
		if (!$_SESSION['faq']['new_version_inwork'] AND !defined("admin")) {
			// nur FE: beim 1. Attachment Upload/Delete oder beim Speichern ohne vorherigem Upload/Delete von Attachments
			if (count($this->checked->faq_cat_id)) {
				$catid = $this->saveFaqCatLink_sub($faq_id, $version_id);
			}
		}
		elseif (defined("admin") AND count($this->checked->faq_cat_id)) {
			// Beim Submit kann die Zuordnung wieder ge�ndert worden sein, daher erst einmal alles l�schen
			// und danach erneut speichern
			if (isset($this->checked->submit[4]) AND $_SESSION['faq']['new_version_inwork']) {
				$sql = sprintf("DELETE FROM %s
										WHERE faq_id='%s'
										AND version_id = '%d'",
					$this->cms->tbname['papoo_faq_cat_link'],
					$this->db->escape($faq_id),
					$version_id
				);
				$this->db->query($sql);
			}
			// Speichern beim Submit und beim 1. Attachment Upload/Delete
			if (isset($this->checked->submit[4]) OR !$_SESSION['faq']['new_version_inwork']) {
				$catid = $this->saveFaqCatLink_sub($faq_id, $version_id);
			}
		}
		IfNotSetNull($catid);
		return $catid;
	}

	/**
	 * alle in der Selectbox ausgew�hlten Kategorien speichern
	 * template
	 *
	 * @param string $faq_id
	 * @param int $version_id
	 * @return array
	 */
	function saveFaqCatLink_sub($faq_id = "", $version_id = 0)
	{
		// Im FE kann keine �nderung der Kategorienzuordnung erfolgen, daher alle aktuellen in die neue Version �bernehmen
		if (!defined("admin")) {
			// Aktuell Kategoriedaten in neue Version �bernehmen
			// $version_id zeigt bereits auf die aktuelle Versionsnummer, daher Korrektur mit minus 1 beim SELECT
			// komplette Feldangaben zur Einhaltung der Reihenfolge beim INSERT !
			// Korrektur der version_id auf neue Version (-1 lesen, +1 speichern)
			$sql = sprintf("INSERT INTO %s
								(cat_id,
								faq_id,
								version_id,
								order_id)
								SELECT cat_id,
								faq_id,
								version_id + 1,
								order_id
								FROM %s
								WHERE faq_id = '%s'
								AND version_id = '%d'",
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->db->escape($faq_id),
				$this->db->escape($version_id) - 1
			);
			$this->db->query($sql);
		}
		else {
			// Nicht bei einer neuen FAQ (BE)
			$catid = array();
			foreach ($this->checked->faq_cat_id as $key) {
				if ($version_id) {
					// Letzte order_id holen und �bernehmen
					$sql = sprintf("SELECT order_id FROM %s
										WHERE faq_id = '%d' AND version_id = '%d' AND cat_id = '%d'",
						$this->cms->tbname['papoo_faq_cat_link'],
						$this->db->escape($faq_id),
						$this->db->escape($version_id) - 1,
						$this->db->escape($key)
					);
					$result = $this->db->get_var($sql);
				}
				else {
					// neue FAQ immer ans Ende des Zweiges
					$result = $this->getMaxOrderId("", $key, 1); // ohne Ber�cksichtigung der Versionen, renumber folgt unten
				}
				// Neue Relation(en) zwischen Kategorie und FAQ herstellen
				$sql = sprintf("INSERT INTO %s
								SET cat_id = '%s',
									faq_id = '%s',
									order_id = '%d',
									version_id = '%d'",
					$this->cms->tbname['papoo_faq_cat_link'],
					$this->db->escape($key),
					$this->db->escape($faq_id),
					$this->db->escape($result),
					$this->db->escape($version_id)
				);
				$this->db->query($sql);
				// cat_id merken f�r die Kennzeichnung in der Selectbox bei erneuter Anzeige
				$catid[$key]['cat_id'] = $key;
				// renumber diesen Zweig
				$this->faq_renumber($this->read_faqdata_for_savefaqorder($key));
			}
		}
		IfNotSetNull($catid);
		return $catid;
	}

	/**
	 * Reihenfolge der Kategorien �ndern (Backend)
	 * template faq_cat_back_move.html
	 *
	 * @return void
	 */
	function save_cat_order()
	{
		// Keys auf numerisch pr�fen
		if ($this->checkNumeric($this->checked->cat_order_name, 1)) {
			// Eingaben auf rein numerisch pr�fen
			// Kategoriedaten vor einem evtl. Fehlerfall holen
			$this->fetchAllCategories(0, "x");
			// Keys aus Template holen
			$temp = array_keys($this->checked->cat_order_name);
			$this->content->template['not_numeric'] = 0; // Init
			for ($i = 0; $i < count($temp); $i++) {
				// Numerische Daten?
				if (!ctype_digit($this->checked->cat_order_name[$temp[$i]])) {
					// Eingabefeld als nicht numerisch kennzeichnen
					$this->content->template['not_numeric'] = $this->content->template['cat_data'][$i]['not_numeric'] = 1;
				}
				// Kategoriedaten im Template mit den Eingabedaten aktualisieren
				$this->content->template['cat_data'][$i]['order_id'] = $this->checked->cat_order_name[$temp[$i]];
			}
			// In die DB speichern, wenn alle Daten numerisch sind
			if (!$this->content->template['not_numeric']) {
				// Eingaben durchloopen und speichern
				for ($i = 0; $i < count($temp); $i++) {
					// Alte order_id Daten holen...
					$sql = sprintf("SELECT order_id
									FROM %s
									WHERE id = '%d' AND lang_id = '%d'",
						$this->cms->tbname['papoo_faq_categories'],
						$this->db->escape($temp[$i]),
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$db_order_id = $this->db->get_var($sql);
					// und mit der Eingabe vergleichen
					if ($db_order_id != $this->checked->cat_order_name[$temp[$i]]) {
						// Speichern, wenn neue Daten vorliegen
						$this->saveFaqOrCatOrderId($temp[$i], $this->checked->cat_order_name[$temp[$i]], 0);
					}
				}
				$this->fetchAllCategories(0, "x");
				$this->content->template['cat_is_renumbered'] = 1; // Meldung �ber Aktualisierung
			}
		}
		else {
			$this->content->template['fehler7'] = 1;
		}
	}

	/**
	 * Die order_id einer Kategorie oder einer FAQ speichern (0 = Kategorien, x = FAQ) (Backend)
	 * called by cat_renumber(), save_cat_order(), save_faq_order()
	 *
	 * @param int $id
	 * @param $order_id
	 * @param int $table
	 * @param string $catid
	 * @param string $faqid
	 * @param string $version_id
	 * @return void
	 */
	function saveFaqOrCatOrderId($id = 0, $order_id, $table = 0, $catid = "", $faqid = "", $version_id ="")
	{
		if ($table)
		{
			$sql = sprintf("UPDATE %s
							SET order_id ='%d'
							WHERE cat_id = '%d' AND faq_id = '%d' AND version_id = '%d'",
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->db->escape($order_id),
				$this->db->escape($catid),
				$this->db->escape($faqid),
				$this->db->escape($version_id)
			);
		}
		else {
			$sql = sprintf("UPDATE %s
							SET order_id ='%d'
							WHERE id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_categories'],
				$this->db->escape($order_id),
				$this->db->escape($id),
				$this->db->escape($this->cms->lang_back_content_id)
			);
		}
		$this->db->query($sql);
	}

	/**
	 * Alle Kategoriedaten sortiert nach der order_id holen (Backend, Frontend)
	 * zus�tzlich alle FAQs z�hlen, wenn $faq_count
	 * zus�tzlich nur die freigegebenen FAQs z�hlen, wenn $active
	 * ist $active = 2, dann nur die Fragen z�hlen, die freigegeben UND beantwortet sind.
	 * called by copy_category(), del_category(), edit_category(), edit_select_category(), faq_front()
	 *            faq_front_new_faq(), fetchAllCategories()
	 *
	 * @param string $id
	 * @param int $faq_count
	 * @param int $active
	 * @return mixed
	 */
	function getCatData($id = "", $faq_count = 0, $active = 0)
	{
		// Kategoriedaten komplett einlesen
		$result = $this->read_count_all_categories($id, 0);
		if ($faq_count OR $active) {
			$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			// Anzahl der FAQs je Kategorie ermitteln und zum Array $result hinzuf�gen
			for ($i = 0; $i < count($result); $i++) {
				if ($faq_count) {
					$sql = sprintf("SELECT COUNT(DISTINCT id)
									FROM %s T1
									INNER JOIN %s AS T2 ON (id = faq_id) AND (T1.version_id = T2.version_id)
									WHERE cat_id ='%d' AND lang_id ='%d'",
						$this->cms->tbname['papoo_faq_content'],
						$this->cms->tbname['papoo_faq_cat_link'],
						$result[$i]['id'],
						$lang_id
					);
					$result[$i]['faq_count'] = $this->db->get_var($sql);
				}
				if ($active) {
					$answer = $active == 1 ? "" : " AND T1.answer != '' ";
					$sql = sprintf("SELECT COUNT(DISTINCT id)
									FROM %s T1
									INNER JOIN %s AS T2 ON (id = faq_id) AND (T1.version_id = T2.version_id)
									WHERE cat_id ='%d' AND lang_id ='%d' AND active = 'j'" . $answer,
						$this->cms->tbname['papoo_faq_content'],
						$this->cms->tbname['papoo_faq_cat_link'],
						$result[$i]['id'],
						$lang_id
					);
					$result[$i]['faq_count_active'] =$this->db->get_var($sql);
				}
			}
		}
		return $result;
	}

	/**
	 * Lesen aller Kategoriedaten oder z�hlen aller Kategorien (Backend, Frontend)
	 * called by checkOwnTree(), edit_category(), edit_select_category(), fetchAllCategories(), getCatData()
	 *
	 * @param string $id
	 * @param int $count
	 * @return mixed
	 */
	function read_count_all_categories($id = "", $count = 0)
	{
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		$where = (empty($id)) ? " WHERE lang_id = " . $lang_id . " " : " WHERE id = " . $id . " AND lang_id = " . $lang_id . " ";
		// Kategoriedaten komplett einlesen
		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS *
						FROM %s 
						$where 
						ORDER BY order_id ",
			$this->cms->tbname['papoo_faq_categories']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		if ($count) {
			// Anzahl ermitteln
			$sql = sprintf("SELECT FOUND_ROWS()");
			$result = $this->db->get_var($sql);
		}
		return $result;
	}

	/**
	 * Baumstruktur f�r die Kategorien erzeugen (Level-Werte f�rs Template setzen) (Backend, Frontend)
	 * called by categoriesTree(), copy_category(), fetchAllCategories()
	 *
	 * @param array $categories enth�lt alle Daten
	 * @param int $id id des parents f�r das ein Baum oder eine Section des Baums auszugeben ist
	 *  $id = 0 inkludiert alle Hauptkategorien mit parent_id = 0.
	 *  Durch Vorgabe einer ID >0 wird nur die Section unterhalb des vorgegebenen parents �bergeben,
	 * also alle childs zum vorgegebenen parent. Weitere M�glichkeit zur Eingrenzung: Vorgabe des Levels.
	 * @param int $level Level ab dem die Ausgabe beginnen soll
	 * @return mixed|void
	 */
	function categoriesTree($categories = array(), $id = 0, $level = 0)
	{
		$i1 = $i2 = 0;
		$temp_cat_array1 = array();
		// Schritt 1: Alle Unterkategorien finden (parent_id zur id finden/child zu parent) mit demselben parent
		// $temp_cat_array1 erh�lt hierzu die Schl�ssel (index von $categories).
		// $temp_cat_array bleibt leer, wenn kein parent (id) gefunden wird. Dann ist am Ende $i1 = 0.
		foreach ($categories as $data1) { // parent_id zur id finden (child zum parent)
			// Unterkategorien in einem Array zusammenfassen
			// $i2 enth�lt den Schl�ssel.
			if ($data1['parent_id'] == $id) $temp_cat_array1[$i1++] = $i2;
			$i2++;
		}

		// Daten zusammenstellen, wenn etwas gefunden wurde ($i1 != 0)
		if ($i1 != 0) {
			// Schritt 2: Zuweisung der Kategoriedaten aufgrund der zuvor gefundenen Schl�ssel. Level einf�gen.
			// Die Daten zu den parents/childs zuweisen
			foreach ($temp_cat_array1 as $data2) {
				$temp_cat_array2 = array();
				// Daten�bergabe aufgrund des Schl�ssels in $data2
				foreach ($categories[$data2] as $key => $value) {
					$temp_cat_array2[$key] = $value;
				}
				$temp_cat_array2['level'] = $level; // Nummer der Ebene zum "CSS-Einr�cken" in der Selectbox
				$this->cat_Tree[] = $temp_cat_array2; // Ergebnis komplett fortschreiben ins Array
				// Rekursiver Aufruf ist erforderlich f�r jede Kategorie. Anzahl der Aufrufe ist abh�ngig von der Leveltiefe
				// innerhalb einer Section.
				$this->categoriesTree($categories, $temp_cat_array2["id"], $level + 1); // n�chsten Level untersuchen
			}
		}
		else {
			// Kein parent gefunden. Zur�ck zum Level vorher.
			return; // back to caller
		}
		return $this->cat_Tree; // fertige Baumstruktur zur�ckgeben
	}

	/**
	 * N�chste verf�gbare order_id holen (Backend, Frontend)
	 * $table: 0 = Kategorie, 1 = FAQ einer Kategorie
	 * $parent_id, wenn $table = 0
	 * $catid und $faqid, wenn $table = 1
	 * called by copy_category(), faq_edit(), faq_front_new_faq(), move_category(), new_category(), new_faq()
	 *
	 * @param int $parent_id
	 * @param string $catid
	 * @param int $table
	 * @return mixed
	 */
	function getNextOrderId($parent_id = 0, $catid = "", $table = 0)
	{
		return ($this->getMaxOrderId($parent_id , $catid, $table) + $_SESSION['faq']['FAQ_RENUM_STEP']);
	}

	/**
	 * Renumber Baumstruktur von Quelle und Ziel (Backend)
	 * called by copy_category(), del_category(), move_category()
	 *
	 * @param int $cat_parent_id_from
	 * @param int $cat_id_to
	 * @return void
	 */

	function cat_renumber($cat_parent_id_from = 0, $cat_id_to = 0)
	{
		// Quelle neu numerieren
		$cat_childs_from = $this->getChilds($cat_parent_id_from);
		for ($i = 0; $i < (count($cat_childs_from)); $i++) {
			$this->saveFaqOrCatOrderId($cat_childs_from[$i]['id'], ($i + 1) * $_SESSION['faq']['FAQ_RENUM_STEP'], 0);
		}
		// Ziel neu numerieren
		$cat_childs_to = $this->getChilds($cat_id_to);
		for ($i = 0; $i < (count($cat_childs_to)); $i++) {
			$this->saveFaqOrCatOrderId($cat_childs_to[$i]['id'], ($i + 1) * $_SESSION['faq']['FAQ_RENUM_STEP'], 0);
		}
	}

	/**
	 * Renumber FAQs eines Kategorienzweiges
	 * called by
	 *
	 * @param array $faqs
	 * @return void
	 */

	function faq_renumber($faqs = array())
	{
		// FAQs eines Kategorienzweiges neu numerieren
		for ($i = 0; $i < (count($faqs)); $i++) {
			$this->saveFaqOrCatOrderId("", ($i + 1) * $_SESSION['faq']['FAQ_RENUM_STEP'], 1, $faqs[$i]['cat_id'], $faqs[$i]['faq_id'], $faqs[$i]['version_id']);
		}
	}

	/**
	 * H�chste order_id ermitteln ($table: 0 = Kategorie, 1 = FAQ in Kategorie) (Backend, Frontend)
	 * called by getNextOrderId()
	 *
	 * @param int $parent_id
	 * @param string $catid
	 * @param int $table
	 * @return mixed
	 */
	function getMaxOrderId($parent_id = 0, $catid = "", $table = 0)
	{
		if ($table) {
			// FAQ
			$sql = sprintf("SELECT MAX(order_id)
							FROM %s
							WHERE cat_id = '%d'",
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->db->escape($catid)
			);
		}
		else {
			$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			// Kategorie
			$sql = sprintf("SELECT MAX(order_id)
							FROM %s
							WHERE parent_id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_categories'],
				$this->db->escape($parent_id),
				$lang_id
			);
		}
		$result = $this->db->get_var($sql);
		return ($result);
	}

	/**
	 * Feststellen, ob die angegebenen ids im selben Zweig liegen (Backend)
	 * called by copy_category(), move_category()
	 *
	 * @param int $from_id
	 * @param int $to_id
	 * @return int
	 */
	function checkOwnTree($from_id = 0, $to_id = 0)
	{
		// Hierzu die Baumstruktur der Zielkategorie ab der Zielkategorie $to_id aufw�rts lesen bis Baumende oder bis $from_id
		// Baumende ist erreicht, wenn die parent_id = 0 ist
		$found = 0; // init
		$cat_count = $this->read_count_all_categories("", 1); // Alle Kategoriedaten einlesen
		// In der session ist die Anzahl aller Kategorien vorhanden
		for ($i = 0; $i < $cat_count; $i++) {
			if ($to_id == 0) {
				break;
			} // Baumende erreicht - nicht gefunden
			// Hole die parent_id der Zielkategorie
			$to_id = $this->getParentId($to_id);
			if ($to_id == $from_id) {
				$found = 1; // Verkettung zwischen Ziel und Quelle vorhanden
				break;
			}
		}
		return $found;
	}

	/**
	 * FAQ Main (Backend)
	 * template faq_back_main.html
	 *
	 * @return void
	 */
	function main_faq()
	{
		// Alle Kategoriedaten holen
		$this->fetchAllCategories(0, "");
		// Verwaiste FAQs ermitteln (ohne Kategorie) und an die zuvor geholten Kategoriedaten anh�ngen
		// Die Ausgabe von "question" in "cat_data" ist im Template ein Indiz f�r "verwaist"
		$sql = sprintf("SELECT DISTINCT T2.id, T2.question
						FROM %s T1, %s T2
						WHERE T2.id NOT IN (SELECT T3.faq_id FROM %s T3) AND T2.lang_id = '%d'",
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$result =  $this->db->get_results($sql, ARRAY_A);
		if (count($result)) {
			$this->content->template['cat_data'] = array_merge($this->content->template['cat_data'], $result);
		}
		// FAQ-Daten ausgeben an faq_back_main.html, wenn eine Kategorie ausgew�hlt ist
		if (isset($this->checked->faq_main_id) && $this->checked->faq_main_id) {
			if (ctype_digit($this->checked->faq_main_id)) {
				// F�r "ORDER BY order_id" Tabelle T2, sonst Tabelle T1
				$order = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "T2." : "T1.";
				// Datum in absteigender Folge, sonst aufsteigend
				$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
				$sql = sprintf("SELECT
								T1.id,
								T1.question,
								T2.cat_id
								FROM %s AS T1
								INNER JOIN %s AS T2 ON (T1.id = T2.faq_id) AND (T1.version_id = T2.version_id)
								WHERE T2.cat_id = '%d' AND T1.lang_id = '%d' GROUP BY question
								ORDER BY %s" . $desc,
					$this->cms->tbname['papoo_faq_content'],
					$this->cms->tbname['papoo_faq_cat_link'],
					$this->db->escape($this->checked->faq_main_id),
					$this->db->escape($this->cms->lang_back_content_id),
					$order.$_SESSION['faq']['FAQ_FAQ_ORDER']
				);
				$this->content->template['faq_data'][0] = $this->db->get_results($sql, ARRAY_A);
				$this->convertDate();
			}
			else {
				$this->content->template['fehler1'] = 1;
			}
		}
		// Meldung "FAQ gel�scht" nach redirect ausgeben, falls angefordert
		$this->content->template['faq_is_del'] = isset($this->checked->faq_is_del) ? $this->checked->faq_is_del : NULL;
		$this->content->template['faq_is_edit'] = isset($this->checked->faq_is_edit) ? $this->checked->faq_is_edit : NULL;
		$this->content->template['faq_anzahl'] = $this->count_faqs();
	}

	/**
	 * Eine neue FAQ anlegen (Backend)
	 * template faq_back_new.html
	 *
	 * @return void
	 */
	function new_faq()
	{
		// Startseite
		if (empty($this->checked->submit)) {
			// Alle Kategoriedaten holen und Option-Eintr�ge der Selectbox f�llen
			IfNotSetNull($this->checked->faq_cat_id);
			$this->fetchAllCategories($this->checked->faq_cat_id, "");
		}
		else {
			// Falls keine Kategorie ausgew�hlt wurde
			if (!$this->checkNumeric($this->checked->faq_cat_id)) {
				$fehler = $this->content->template['fehler1'] = 1;
			}
			// Falls die Frage fehlt
			if (empty($this->checked->faq_question)) {
				$fehler = $this->content->template['fehler2'] = 1;
			}
			if (!isset($fehler) || isset($fehler) && !$fehler) {
				// Antwort f�r die Liste der offenen Fragen bereinigen,
				// sodass nur druckbare Zeichen zu einer Antwort mit Inhalt f�hren
				// Die Antwort ist leer, wenn nur nicht druckbare Zeichen gefunden wurden
				$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
				#$answer = htmlentities($this->diverse->do_pfadeanpassen($answer), ENT_QUOTES);
				// Freigabe-Status der FAQ active or not
				$this->checked->faq_new_release ? $release = "j" : $release = "n";

				$sql = sprintf("SELECT MAX(id) FROM %s",DB_PRAEFIX."papoo_faq_content");
				$max = $this->db->get_var($sql);
				$max++;

				//Get Languages of the system
				$sql = sprintf("SELECT * FROM %s",
					DB_PRAEFIX.'papoo_name_language');
				//print_r($sql);
				$result = $this->db->get_results($sql,ARRAY_A);

				foreach ($result as $k =>$v) {
					// Speichern der FAQ Daten
					$sql = sprintf("INSERT INTO %s
								SET 
								id='%s',
								question = '%s',
								answer = '%s',
								active = '%s',
								created = '%s',
								createdby = '%s',
								lang_id = '%d',
								version_id = 0,
								upload_count = 0",
						$this->cms->tbname['papoo_faq_content'],
						$max,
						$this->db->escape(trim($this->checked->faq_question)),
						$this->db->escape($answer),
						$this->db->escape($release),
						date('YmdHis'),
						$this->db->escape($this->user->username),
						$this->db->escape($v['lang_id'])
					);
					$this->db->query($sql);
				}
				// Die neue Record-ID merken
				$faq_id = $max;
				//erweiterte Suche
				$this->ext_search($faq_id);
				$_SESSION['faq']['new_version_inwork'] = 0; // sicherheitshalber, m�sste aber 0 sein
				$catid = $this->saveFaqCatLink($faq_id, 0);
				$this->content->template['faq_is_new'] = 1; // Message "...wurde eingetragen"
				// Nach dem L�schen unbedingt raus aus dem Template & zur Hauptseite springen
				$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
					"&template=faq/templates/faq_back_edit.html&faq_back_edit_list=1";
				if ( $_SESSION['debug_stopallredirect'] ) {
					echo '<a href="' . $location_url . '">Weiter</a>';
				}
				else {
					header( "Location: $location_url" );
				}
				exit;
			}
		}
		// Eingabe-Daten wiederherstellen
		IfNotSetNull($this->checked->faq_answer);
		IfNotSetNull($this->checked->faq_question);
		$this->content->template['faq_new_answer'] = $this->checked->faq_answer; // Antwort wiederherstellen
		$this->content->template['faq_question'] = $this->nobr($this->checked->faq_question); // Frage wiederherstellen
		// Freigabe-Status der FAQ
		$this->content->template['checkedrel'] = !empty($this->checked->faq_new_release) ? "checked='checked'" : "";
		// Kategoriedaten der Selectbox wiederherstellen
		IfNotSetNull($catid); // FIXME: Uhrsprünglich nicht gesetzt
		$this->fetchAllCategories($catid, "");
		$this->content->template['faq_anzahl'] = $this->count_faqs();
	}

	/**
	 * FAQ zur Bearbeitung anzeigen, Edit und Delete, inkl. Attachment-Verarbeitung (Backend)
	 * Template faq_back_edit.html
	 *
	 * @return void
	 */
	function edit_faq_main()
	{
		// FAQ ID
		if (isset($this->checked->faq_id) AND !ctype_digit($this->checked->faq_id)) {
			$fehler = $this->content->template['fehler12'] = 1;
		}
		// ID des zu l�schenden Attachments
		if (isset($this->checked->submit[3])) {
			$sub3 = array_keys($this->checked->submit[3]);
			if (!ctype_digit((string)$sub3[0])) $fehler = $this->content->template['fehler13'] = 1;
		}
		// Kategorien IDs (array)
		if (isset($this->checked->faq_cat_id) AND !$this->checkNumeric($this->checked->faq_cat_id)) {
			$fehler = $this->content->template['fehler14'] = 1;
		}
		// ID der aktuellen Version
		if (isset($this->checked->faq_current_version) AND !ctype_digit($this->checked->faq_current_version)) {
			$fehler = $this->content->template['fehler15'] = 1;
		}
		// ID der ausgew�hlten Version
		if (isset($this->checked->faq_version_selected) AND !ctype_digit((string)$this->checked->faq_version_selected)) {
			$fehler = $this->content->template['fehler16'] = 1;
		}
		// Quelle (aus FE / BE)
		global $template;
		if (!isset($this->checked->src) AND $this->template == "faq_back_accept_question.html") {
			$fehler = $this->content->template['fehler11'] = 1;
		}
		// $this->checked->faq_current_version: Beim 1. Aufruf oder nach Attachment Upload/delete (neue Version)
		// $this->checked->submit[0]: Auswahl einer Version
		// $this->checked->faq_version_selected == $this->checked->faq_current_version: Bei erneuter Auswahl der aktuellen Version
		// Wenn die aktuelle Version ausgew�hlt wurde:
		if (isset($fehler) && $fehler) {
			$this->faq_redisplay_postdata();
		} // Postdaten wieder anzeigen
		else {
			// Aktiven Submit-Button ermitteln -> $i
			if (isset($this->checked->submit) && is_array($this->checked->submit)) {
				for ($i = 0; $i < 10; $i++) {
					if (isset($this->checked->submit[$i])) break; // Aktiven Submit-Button gefunden
				}
				switch ($i) {
				case 0:
					// Versionsauswahl
					//$this->checked->faq_current_version = $this->content->template['faq_current_version'] = $this->checked->faq_version_selected;
					break;
				case 1;
					// Reset Kategorie Selectbox (derzeit zugeordnete Kategorien erneut anzeigen)
					break;
				case 2;
				case 3;
					// Attachment hochladen (2) oder l�schen (3)
					if (!$this->checked->faq_id) {
						$this->content->template['fehler3'] = 1;
					} // FAQ ID fehlt
					else {
						if ($i == 2) $rc = $this->faq_attachment_upload(); // Liefert RC = 1 bei Upload error
						// L�schen mit Versionierung (alle Attachments in die neue Version �bernehmen, au�er das zu l�schende)
						else {
							$this->faq_attachment_delete($sub3[0], 1);
						}
						if (!$rc) {
							// und wenn kein Fehler aufgetreten ist, beim 1. Mal auch eine neue FAQ Version erzeugen
							if (!$_SESSION['faq']['new_version_inwork']) {
								$this->faq_makeNewVersion(); // Neue Version der FAQ-Daten anlegen
								// Relation FAQ / Kategorien speichern
								$this->saveFaqCatLink($this->checked->faq_id, $this->getMaxVersionId($this->checked->faq_id));
								// Kennzeichen setzen, dass bereits eine neue Version in Arbeit ist.
								$_SESSION['faq']['new_version_inwork'] = 1;
							} // else k�nnte noch Daten in die DB der bereits angelegten neuen Version speichern
						} // kein else, evtl. aufgetretene Upload-Fehlermeldungen sind bereits ans Template �bergeben worden
					}
					break;
				case 4; // Speichern/Edit FAQ (BE & FE)
				case 6; // Vorschlag aus FE �bernehmen
				case 8; // Offene Frage zur FAQ machen (= �bernahme. Ist nur bei vorhandener Antwort m�glich)
					// danach raus mit exit
					if (count($this->checked->faq_cat_id)) {
						// Falls die Frage fehlt, Fehler ausl�sen
						if (empty($this->checked->faq_question)) {
							$this->content->template['fehler2'] = 1;
						}
						else {
							if ($i == 4) {
								$this->faq_edit();
							} // FAQ Edit FE/BE alle Daten speichern
							elseif ($i == 6) {
								$this->faq_accept_faq();
							} // FAQ Vorschlag vom Frontend �bernehmen
							elseif ($i == 8) {
								$this->faq_accept_question();
							} // offene Frage �bernehmen
						}
					}
					// Fehler, keine Kategorie bei der Bearbeitung ausgew�hlt
					else {
						$this->content->template['fehler1'] = 1;
					}
					break;
				case 5;
					if (!$this->checked->faq_id) {
						$this->content->template['fehler3'] = 1;
					} // FAQ ID fehlt
					// FAQ l�schen, L�schabfrage senden
					// Schalter: bei catdelete = true ist die L�schabfrage noch nicht erfolgt
					$this->content->template['delete'] = 1;
					$this->content->template['faq_id'] = $this->checked->faq_id;
					break;
				case 7;
					// Unused
					break;
				case 9:
					if ($this->checked->submit[9] == $this->content->template['plugin']['faq_back']['submit']['delete_yes']) {
						// FAQ l�schen, danach raus mit exit
						if ($this->checked->faq_id) {
							$this->faq_delete("faq_back_edit.html&faq_back_edit_list=1");
						}
						$this->content->template['fehler3'] = 1; // FAQ ID fehlt
					}
					break;
				default:
					break;
				}
				// Versionsauswahl im Template deaktivieren, wenn eine neue Version erstellt wurde (nach Attachment Upload/Delete)
				// dann ist $_SESSION['faq']['new_version_inwork'] gesetzt
				$this->content->template['version_change_inactive'] = $_SESSION['faq']['new_version_inwork'];
			}
			else { // Kein Submit-Button aktiv
				// Beim Edit-Button im horizontalen Men� oder bei "weiter" (paginating) Linkliste erstellen
				if (isset($this->checked->faq_back_edit_list) && $this->checked->faq_back_edit_list or $this->checked->page) {
					$this->faq_link_list(); // Liste aller FAQs erstellen
					$linklist = 1; // FAQ-Daten wurden bereits angezeigt, keine FAQ Daten/Timestamps aus der DB
				}
			}
		}
		global $template;
		if ($this->template == "faq_back_accept_faq.html" AND !count($this->checked->submit)) {
			// FAQ Daten f�r die �bernahme einer FAQ aus dem FE bereitstellen (ohne Timestamps, da hier keine Versionierung)
			$this->content->template['faq_data'][0] = $this->getDataFromTable($this->cms->tbname['papoo_faq_content_frontend']);
			// Frontend Freigabe Kennzeichnung der FAQ
			// ist eigentlich nicht erforderlich
			$this->content->template['checkedrel'] =
				$this->content->template['faq_data'][0][0]['active'] == 'j' ? "checked='checked'" : "";
		}
		else { // Edit Frage/FAQ alle: FE, BE (offene, Freigaben, vorhandene), auch bei Fehler
			if (!isset($linklist) || isset($linklist) && !$linklist) { // (Linkaufruf, aber nicht bei Linkliste)
				// Aktuelle Daten anzeigen (FAQ-Daten, Versions-Timestamps).
				$this->get_timestamps_and_faq_data();
				// Bei Versionswechsel auf die ausgew�hlte Version umschalten (f�r Anzeige Attachments und Kategorien)
				if (!$_SESSION['faq']['new_version_inwork']
					AND $this->content->template['faq_version_selected'] != $this->getMaxVersionId($this->checked->faq_id)) {
					$version_id = $this->content->template['faq_version_selected'];
				}
				else {
					$version_id = $this->getMaxVersionId($this->checked->faq_id);
				}
				// Attachments zur FAQ-Version bereitstellen nur f�r BE
				if (isset($this->checked->src) && $this->checked->src != "FE") {
					$this->getAttachmentsList($this->checked->faq_id, $version_id);
				}
			}
		}
		// Postdaten wieder anzeigen (f�r 1 bis 3. 4, 5 und 6 evtl. bei Fehler, aber nicht bei Linkaufruf oder Linkliste)
		if (isset($i) && $i >= 1 AND $i <= 8) {
			$this->faq_redisplay_postdata();
		} // Anm.: exakte Abfrage wegen zuk�nftiger �nderungen
		// Nur bei Reset (1), bei der Versionsauswahl (0), bei Linkaufruf, aber nicht bei der Linkliste, auch bei L�schen=no (9):
		// Die Kategorie-Daten der zur FAQ zugeordneten Kategorien aus der DB nach $result holen
		// und diese als selected in der Selectbox kennzeichnen
		if (@$i < 2 AND !@$linklist OR isset($i) && $i == 9) {
			// Welche Tabelle?
			// FAQ aus FE oder Freigaben
			if ($this->template == "faq_back_accept_faq.html") {
				$sql = sprintf("SELECT
								T1.cat_id,
								T2.catname,
								T2.catdescript					
								FROM %s T1
								INNER JOIN %s T2 ON (T1.cat_id = T2.id)
								WHERE T1.faq_id = '%d'",
					$this->cms->tbname['papoo_faq_cat_link_frontend'],
					$this->cms->tbname['papoo_faq_categories'],
					$this->db->escape($this->checked->faq_id)
				);
			}
			else {
				// Offene Frage aus FE
				if ($this->checked->src == "FE") {
					$sql = sprintf("SELECT
									T1.cat_id,
									T2.catname,
									T2.catdescript					
									FROM %s T1
									INNER JOIN %s T2 ON (T1.cat_id = T2.id)
									WHERE T1.faq_id = '%d'",
						$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
						$this->cms->tbname['papoo_faq_categories'],
						$this->db->escape($this->checked->faq_id)
					);
				}
				else {
					// FAQ aus BE
					$sql = sprintf("SELECT
									T1.cat_id,
									T2.catname,
									T2.catdescript
									FROM %s T1
									INNER JOIN %s T2 ON (T1.cat_id = T2.id)
									WHERE T1.faq_id = '%d' AND T1.version_id = '%d'",
						$this->cms->tbname['papoo_faq_cat_link'],
						$this->cms->tbname['papoo_faq_categories'],
						$this->db->escape($this->checked->faq_id),
						$this->db->escape($version_id)
					);
				}
			}
			$result = $this->db->get_results($sql, ARRAY_A);
			$this->fetchAllCategories($result, "");
		}
		IfNotSetNull($result);
		$this->content->template['faq_catdata'] = $result; // f�r FE: Edit FAQ
		// Diese Daten immer ans Template
		IfNotSetNull($this->checked->faq_id);
		$this->content->template['faq_id'] = $this->checked->faq_id; // hidden ID weiterreichen f�rs Speichern
		$this->content->template['faq_anzahl'] = $this->count_faqs(); // Gesamtanzahl FAQs
	}

	/**
	 * FAQ l�schen
	 * called by
	 *
	 * @param string $template
	 * @return void
	 */
	function faq_delete($template = "")
	{
		// Zugeh�rige Attachments l�schen
		$this->getAttachmentsList($this->checked->faq_id); // Attachments Liste holen
		if (count($this->content->template['faq_attach'])) {
			// Alle zugeh�rigen Attachments l�schen
			foreach ($this->content->template['faq_attach'] AS $key =>$value) {
				// Fehler beim L�schen aufgetreten (RC = true) ?
				// L�schen ohne Versionierung (alle Attachments l�schen)
				if ($this->faq_attachment_delete($this->content->template['faq_attach'][$key]['id'], 0)) {
					$del_error = 1;
				}
			}
		}
		// Die FAQ nur dann l�schen, wenn auch das/die Attachment/s gel�scht werden konnten
		// die nicht gel�schten Attachments k�nnten sonst nirgendwo mehr gel�scht werden
		//if (!$del_error)
		//{
		// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
		$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->db->escape($this->checked->faq_id)
		);
		$this->db->query($sql);
		// FAQ Daten l�schen
		$sql = sprintf("DELETE FROM %s WHERE id='%s' AND lang_id = '%d'",
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($this->checked->faq_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$this->db->query($sql);
		// Versionen l�schen
		$sql = sprintf("DELETE FROM %s WHERE id='%s' AND lang_id = '%d'",
			$this->cms->tbname['papoo_faq_versions'],
			$this->db->escape($this->checked->faq_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$this->db->query($sql);
		$_SESSION['faq']['new_version_inwork'] = 0;
		if ($template == "faq_back_edit.html&faq_back_edit_list=1") {
			$this->remove_cache_file('plugin:faq/templates/faq_front.html'); // delete cache file(s)
			// Nach dem L�schen unbedingt raus aus dem Template & zur Hauptseite springen
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/" . $template . "&faq_is_del=1";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
	}

	/**
	 * FAQ Vorschlag aus Frontend �bernehmen
	 * called by
	 *
	 * @return void
	 */
	function faq_accept_faq()
	{
		if (empty($this->checked->faq_question)) {
			$this->content->template['fehler2'] = $fehler = 1;
		}
		$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
		if (empty($answer)) {
			$this->content->template['fehler4'] = $fehler = 1;
		}
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			$answer = $this->diverse->do_pfadeanpassen($answer);
			// Neue Version mit original Daten speichern und Template-Daten als Version 1 (FAQ Daten und Kategorien)
			$this->faq_accept_faq_or_question("faq", $answer);
			// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
			$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
				$this->cms->tbname['papoo_faq_cat_link_frontend'],
				$this->db->escape($this->checked->faq_id)
			);
			$this->db->query($sql);
			// FAQ Daten l�schen
			$sql = sprintf("DELETE FROM %s WHERE id='%s' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_content_frontend'],
				$this->db->escape($this->checked->faq_id),
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->db->query($sql);
			// Nach dem Speichern unbedingt raus aus dem Template & zur Hauptseite springen
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/faq_back_new_frontend.html&faq_new_is_edit=1";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
	}

	/**
	 * Offene Frage �bernehmen
	 * called by
	 *
	 * @return void
	 */
	function faq_accept_question()
	{
		if (empty($this->checked->faq_question)) {
			$this->content->template['fehler2'] = $fehler = 1;
		}
		$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
		if (empty($answer)) {
			$this->content->template['fehler4'] = $fehler = 1;
		}
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			$answer = $this->diverse->do_pfadeanpassen($answer);
			if ($this->checked->src == "FE") {
				// Neue Version mit original Daten speichern und Template-Daten als Version 1 (FAQ Daten und Kategorien)
				$this->faq_accept_faq_or_question("question", $answer);
				// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
				$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
					$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
					$this->db->escape($this->checked->faq_id)
				);
				$this->db->query($sql);
				// FAQ Daten l�schen
				$sql = sprintf("DELETE FROM %s WHERE id='%s'",
					$this->cms->tbname['papoo_faq_new_question_frontend'],
					$this->db->escape($this->checked->faq_id)
				);
				$this->db->query($sql);
			}
			elseif ($this->checked->src == "BE") {
				// �bernahme einer offenen Frage, die im Backend erstellt wurde: neue Version erzeugen
				$this->faq_makeNewVersion();
				// Relation zwischen Kategorie und FAQ setzen
				$this->saveFaqCatLink($this->checked->faq_id, $this->getMaxVersionId($this->checked->faq_id));
			}
			// Nach dem Speichern unbedingt raus aus dem Template & zur Hauptseite springen
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/faq_back_offene.html&faq_is_accepted=1";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
	}

	/**
	 * FAQ Daten & Timestamp-Daten der Versionen holen und ans Template �bergeben (BE)
	 * called by edit_faq_main()
	 *
	 * @param string $accept
	 * @param string $answer
	 * @return void
	 */
	function faq_accept_faq_or_question($accept = "", $answer = "")
	{
		// Daten-Quellen festlegen
		switch ($accept) {
		case "question":
			$table_content = $this->cms->tbname['papoo_faq_new_question_frontend'];
			$table_cat_link = $this->cms->tbname['papoo_faq_cat_link_new_question_frontend'];
			break;
		case "faq":
			$table_content = $this->cms->tbname['papoo_faq_content_frontend'];
			$table_cat_link = $this->cms->tbname['papoo_faq_cat_link_frontend'];
			break;
		}
		// Originale Daten holen FE & diese als alte Version speichern
		$result = $this->getDataFromTable($table_content);
		// Freigabe-Status der FAQ active or not
		$this->checked->faq_release ? $release = "j" : $release = "n";
		#$answer = htmlentities($answer, ENT_QUOTES);
		// Eingabe-Daten als neue Version 1 speichern
		$sql = sprintf("INSERT INTO %s
							SET
							version_id = '1',
							lang_id = '%d',
							question = '%s',
							answer = '%s',
							active = '%s',
							upload_count = '0',
							created = '%s',
							createdby = '%s',
							changedd = '%s',
							changedby = '%s'",
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($result[0]['lang_id']),
			$this->db->escape(trim($this->checked->faq_question)),
			$this->db->escape($answer),
			$this->db->escape($release),
			$this->db->escape($result[0]['created']),
			$this->db->escape($result[0]['createdby']),
			date('YmdHis'),
			$this->db->escape($this->user->username)
		);
		$this->db->query($sql);
		// Die neue Record-ID merken
		$faq_id = $this->db->insert_id;
		// Gelesene Daten als Version 0 speichern
		$sql = sprintf("INSERT INTO %s
							SET
							id = '%d',
							version_id = '0',
							lang_id = '%d',
							question = '%s',
							answer = '%s',
							active = '%s',
							upload_count = '0',
							created = '%s',
							createdby = '%s',
							changedd = '%s',
							changedby = '%s'",
			$this->cms->tbname['papoo_faq_versions'],
			$this->db->escape($faq_id),
			$this->db->escape($result[0]['lang_id']),
			$this->db->escape($result[0]['question']),
			$this->db->escape($result[0]['answer']),
			$this->db->escape($result[0]['active']),
			$this->db->escape($result[0]['created']),
			$this->db->escape($result[0]['createdby']),
			$this->db->escape($result[0]['changedd']),
			$this->db->escape($result[0]['changedby'])
		);
		$this->db->query($sql);
		// Kategorie-Zuordnung holen
		$result = $this->getDataFromTable($table_cat_link, $this->db->escape($this->checked->faq_id));
		// original Zuordnung als Version 0 speichern
		$sql = sprintf("INSERT INTO %s
							SET cat_id = '%d',
							faq_id = '%d',
							order_id = '%d',
							version_id = '0'",
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->db->escape($result[0]['cat_id']),
			$this->db->escape($faq_id),
			$this->db->escape($result[0]['order_id'])
		);
		$this->db->query($sql);
		// Neue Zuordnung als Version 1 speichern
		$catid = $this->saveFaqCatLink_sub($faq_id, $version_id = 1);
	}

	/**
	 * FAQ Daten & Timestamp-Daten der Versionen holen und ans Template �bergeben
	 * called by edit_faq_main()
	 *
	 * @return void
	 */
	function get_timestamps_and_faq_data()
	{
		IfNotSetNull($this->checked->src);
		$table_content = $this->checked->src == "FE" ? $this->cms->tbname['papoo_faq_new_question_frontend'] : $this->cms->tbname['papoo_faq_content'];
		// Die Timestamp Daten der aktuellen Version werden immer ben�tigt, die FAQ-Daten darin jedoch nur bei Auswahl der Version
		$actual_version_data = $this->getDataFromTable($table_content);
		if (count($actual_version_data)) { // Falls nicht vorhanden, nichts ausgeben (z. B. bei falscher, manueller Browsereingabe)
			if (!isset($this->checked->faq_current_version)
				OR (isset($this->checked->submit[0]) AND $this->checked->faq_version_selected == $this->checked->faq_current_version)) {
				$this->content->template['faq_current_version'] = $this->content->template['faq_version_selected'] =
				$this->checked->faq_version_selected = $actual_version_data[0]['version_id'];
			}
			else {
				// Eine alte Version wurde ausgew�hlt - hole die FAQ-Daten zu dieser Version
				$old_version_data = $this->getVersionData($this->checked->faq_version_selected);
				$this->content->template['faq_current_version'] = $actual_version_data[0]['version_id']; // Restore
				$this->content->template['faq_version_selected'] = $this->checked->faq_version_selected; // Update
			}
			//  nur f�r BE: Timestamp-Daten aller alten Versionen f�r die Selectbox "Versionsauswahl" bereitstellen
			if ($this->checked->src != "FE") {
				$sql = sprintf("SELECT version_id,
								created,
								createdby,
								changedd,
								changedby
								FROM %s
								WHERE id = '%d' AND lang_id = '%d'
								ORDER BY version_id DESC",
					$this->cms->tbname['papoo_faq_versions'],
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$old_timestamps = $this->db->get_results($sql, ARRAY_A);
			}
			// Timestamps aller Versionen ins Ausgabeformat konvertieren
			$this->content->template['faq_data'][0] =
				count($old_timestamps) ? array_merge($actual_version_data, $old_timestamps) : $actual_version_data;
			$this->content->template['all_timestamps'] = $this->convertDate(); // Alle Timestamps mit konvert. Datums ans Template
			// FAQ Daten ans Template nur f�r BE
			if ($this->checked->src != "FE") {
				$this->content->template['faq_data'][0] =
					$this->checked->faq_version_selected == $this->getMaxVersionId($this->checked->faq_id) ? $actual_version_data : $old_version_data;
				//Timestamps der FAQ Daten ins Ausgabeformat konvertieren
				$this->convertDate();
			}
			// Aktuelle Timestampdaten ans Template als Infotext der angezeigten Version
			// Die Kennzeichnung "selected" dieser Version in der Selectbox erfolgt durch das Template
			$creation_version = end($this->content->template['all_timestamps']);

			$this->content->template['timestamp'] =
				$this->content->template['faq_current_version'] == $creation_version['version_id'] ? $this->content->template['faq_data'][0][0]['created'] : $this->content->template['all_timestamps'][0]['changedd'];

			$this->content->template['timestampby'] =
				$this->content->template['faq_current_version'] == $creation_version['version_id'] ? $this->content->template['faq_data'][0][0]['createdby'] : $this->content->template['all_timestamps'][0]['changedby'];

			$this->content->template['timestamp_oldversion'] =
				$this->content->template['faq_version_selected'] == $creation_version['version_id'] ? $this->content->template['faq_data'][0][0]['created'] : $this->content->template['faq_data'][0][0]['changedd'];

			$this->content->template['timestampby_oldversion'] =
				$this->content->template['faq_version_selected'] == $creation_version['version_id'] ? $this->content->template['faq_data'][0][0]['createdby'] : $this->content->template['faq_data'][0][0]['changedby'];
			// Templatedaten weiter aufbereiten
			// Umwandlung von \n in <br> verhindern f�r die Textarea Frage
			$this->content->template['faq_data'][0][0]['question'] = $this->nobr($this->content->template['faq_data'][0][0]['question']);
			// Frontend Freigabe Kennzeichnung der FAQ
			$this->content->template['checkedrel'] =
				$this->content->template['faq_data'][0][0]['active'] == 'j' ? "checked='checked'" : "";
		}
	}

	/**
	 * Verarbeitung/Speichern der bearbeiteten FAQ-Daten (Frontend, Backend)
	 * called by edit_faq_main()
	 *
	 * @return void
	 */
	function faq_edit()
	{
		// Falls noch keine neue Version erzeugt wurde
		// (Attachment Upload oder Attachment delete erzeugen neue Version vor dem Speichern)
		if (!$_SESSION['faq']['new_version_inwork']) {
			// Vorhandene Attachments in die neue Version �bernehmen
			$this->transferAttachmentsToNewVersion();
			// Eine neue FAQ Version erzeugen (FAQ Daten), falls nicht schon gemacht, sonst nur Update
			$this->faq_makeNewVersion();
			//erweiterte Suche
			$this->ext_search($this->checked->faq_id);
		}
		else { // Neue Version wurde bereits erzeugt
			// Antwort f�r die Liste der offenen Fragen bereinigen,
			// sodass nur druckbare Zeichen zu einer Antwort mit Inhalt f�hren
			// Die Antwort ist leer, wenn nur nicht druckbare Zeichen gefunden wurden
			$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
			#$answer = htmlentities($this->diverse->do_pfadeanpassen($answer), ENT_QUOTES);
			// Freigabe-Status der FAQ active or not
			$this->checked->faq_release ? $release = "j" : $release = "n";
			// Begrenzung der Attachment-Uploads zur�cksetzen;
			$upload_count = $release == "j" ? ", upload_count = 0 " : "";
			// Setzen der Sprache aus Backend/Frontend-Einstellung
			$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			// Update der FAQ Daten
			$sql = sprintf("UPDATE %s
						SET version_id = '%d',
						question = '%s',
						answer = '%s',
						active = '%s',
						changedd = '%s',
						changedby = '%s' $upload_count
						WHERE id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_content'],
				$this->db->escape($this->getMaxVersionId($this->checked->faq_id)),
				$this->db->escape($this->checked->faq_question),
				$this->db->escape($answer),
				$release,
				date('YmdHis'),
				$this->db->escape($this->user->username),
				$this->db->escape($this->checked->faq_id),
				$this->db->escape($lang_id)
			);
			$this->db->query($sql);
			//erweiterte Suche
			$this->ext_search($this->checked->faq_id);
		}
		// Relation zwischen Kategorie und FAQ setzen
		$this->saveFaqCatLink($this->checked->faq_id, $this->getMaxVersionId($this->checked->faq_id));
		#$this->content->template['faq_is_edit'] = 1; // Erfolgsmeldung durch Template ausl�sen
		$_SESSION['faq']['new_version_inwork'] = 0;
		$this->remove_cache_file('plugin:faq/templates/faq_front.html'); // delete cache file(s)
		// Nach dem Speichern unbedingt raus aus dem Template & zur Hauptseite springen (BE/FE)
		if (defined("admin")) {
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/faq_back_main.html&faq_is_edit=1";
		}
		else {
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/faq_front.html&faq_is_edit=1";
		}
		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">Weiter</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 * @param int $id
	 */
	function ext_search($id=0)
	{
		if (!empty($this->cms->tbname['plugin_ext_search_page'])) {
			//Diese Klasse setzt die Suche um!!
			require_once PAPOO_ABS_PFAD."/plugins/extended_search/lib/class_search_create.php";

			//Klasse initialisieren und zur Verf�gung stellen
			$search_create = new class_search_create();

			$search_create->create_page_faq($id);
		}
	}

	/**
	 * Eine neue FAQ-Version erzeugen (FAQ Daten)
	 *
	 * @return mixed
	 */
	function faq_makeNewVersion()
	{
		// Setzen der Sprache aus Backend/Frontend-Einstellung
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		if (!$_SESSION['faq']['new_version_inwork']) {
			// Version zur alten Version machen
			$sql = sprintf("INSERT INTO %s
							SELECT * FROM %s
							WHERE id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_versions'],
				$this->cms->tbname['papoo_faq_content'],
				$this->db->escape($this->checked->faq_id),
				$this->db->escape($lang_id)
			);
			$this->db->query($sql);
		}
		// Freigabe-Status der FAQ active or not (wird nur im BE angeliefert)
		// derzeit nur bei einer neuen FAQ / Frage
		#if (defined("admin")) $this->checked->faq_release ? $release = "j" : $release = "n";
		#else $_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'j' ? $release = "j" : $release = "n";
		global $template;
		// Beantwortung einer Frage im Frontend: Setze Freigabe laut Konfig.
		if ($this->template == "faq_front_answer_a_question.html" AND ($this->checked->src == "FE")) {
			$_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'j' ? $release = "j" : $release = "n";
		}
		// Bearbeitung einer Frage im Frontend: Setze Freigabe = j.
		if ($this->template == "faq_front_edit.html") {
			$release = "j";
		}
		// Im BE immer freigeben
		if (defined("admin")) {
			$release = $this->checked->faq_release == "j" ? "j" : "n";
		}
		#if (!isset($release)) $release = "n";
		$upload_count = $release == "j" ? ", upload_count = 0 " : "";

		$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
		#$answer = htmlentities($this->diverse->do_pfadeanpassen($answer), ENT_QUOTES);
		// Timestamp & Version aktualisieren.
		$sql = sprintf("UPDATE %s
						SET version_id = '%d',
						question = '%s',
						answer = '%s',
						active = '%s',
						changedd = '%s',
						changedby = '%s' $upload_count
						WHERE id = '%d' AND lang_id = '%d'",
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($this->getMaxVersionId($this->checked->faq_id)) + 1,
			$this->db->escape($this->checked->faq_question),
			$this->db->escape($answer),
			$release,
			date('YmdHis'),
			$this->db->escape($this->user->username),
			$this->db->escape($this->checked->faq_id),
			$this->db->escape($lang_id)
		);
		$this->db->query($sql);
	}

	/**
	 * Alle Daten einer alten Version holen (Backend)
	 * called by
	 *
	 * @param $version_id
	 * @return array|void
	 */
	function getVersionData($version_id)
	{
		$result = $this->getDataFromTable($this->cms->tbname['papoo_faq_versions'], "", $this->db->escape($version_id));
		return $result;
	}

	/**
	 * Anzahl aller FAQs ermitteln (Backend)
	 * used by all templates
	 * called by edit_faq_main(), faq_link_list(), faq_list_offene(), faq_list_release(), main_faq(), new_faq(), save_faq_order()
	 *
	 * @return array|void
	 */
	function count_faqs()
	{
		// Gesamtanzahl aller FAQs ermitteln und an die Templates �bergeben
		$sql = sprintf("SELECT COUNT(*)
						FROM %s
						WHERE lang_id = '%d'",
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$faq_count = ($this->db->get_var($sql));
		return $faq_count;
	}

	/**
	 * Reihenfolge der Kategorien �ndern (Backend)
	 * template faq_back_renum.html
	 *
	 * @return void
	 */
	function save_faq_order()
	{
		// Startseite? Dann Kategorien anzeigen.
		if (empty($this->checked->submit) AND empty($this->checked->cat_sel_id)) {
			$this->fetchAllCategories(0, "");
		}
		else {
			if (isset($this->checked->cat_sel_id) AND !ctype_digit($this->checked->cat_sel_id)) {
				$this->content->template['fehler1'] = "x";
				$this->fetchAllCategories(0, "");
			}
			else {
				// Kategorie f�r die Anzeige der FAQs ausgew�hlt?
				if ($this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['cat_select']) {
					$this->content->template['faq_data'] = $this->read_faqdata_for_savefaqorder($this->checked->cat_sel_id); // FAQ Daten bereitstellen
				}
				else { // Es liegt eine �nderung vor, Daten sollen gespeichert werden.
					$this->content->template['faq_data'] = $this->read_faqdata_for_savefaqorder($this->checked->cat_sel_id); // FAQ Daten erneut lesen
					// Erst einmal feststellen, ob die Werte numerisch sind, sonst abweisen und kennzeichnen als nicht numerisch
					// Keys aus dem Template holen
					$temp = array_keys($this->checked->faq_order_name);
					$this->content->template['not_numeric'] = 0; // Init ist numerisch
					for ($i = 0; $i < count($temp); $i++) {
						// Numerische Daten?
						if (!ctype_digit($this->checked->faq_order_name[$temp[$i]])) {
							// Eingabefeld als nicht numerisch kennzeichnen
							$this->content->template['not_numeric'] = $this->content->template['faq_data'][$i]['not_numeric'] = 1;
						}
						// Kategoriedaten im Template mit den Eingabedaten aktualisieren
						$this->content->template['faq_data'][$i]['order_id'] = $this->checked->faq_order_name[$temp[$i]];
					}
					if (!$this->content->template['not_numeric']) {
						// ist numerisch
						$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
						for ($i = 0; $i < count($temp); $i++) {
							// Alte order_id holen...
							$sql = sprintf("SELECT T1.order_id,
												   T1.version_id
											FROM %s T1
											WHERE T1.faq_id = '%d' AND T1.cat_id = '%d'
											AND T1.version_id = (SELECT T2.version_id
																	FROM %s T2
																	WHERE id = '%d' AND lang_id = '%d')",
								$this->cms->tbname['papoo_faq_cat_link'],
								$this->db->escape($temp[$i]),
								$this->db->escape($this->checked->cat_sel_id),
								$this->cms->tbname['papoo_faq_content'],
								$this->db->escape($temp[$i]),
								$this->db->escape($lang_id)
							);
							$result = $this->db->get_results($sql, ARRAY_A);
							// und mit der Eingabe vergleichen
							if ($result[0]['order_id'] != $this->checked->faq_order_name[$temp[$i]]) {
								// Speichern der FAQ oder_id, wenn neue Daten vorliegen
								$this->saveFaqOrCatOrderId("", $this->checked->faq_order_name[$temp[$i]], 1, $this->checked->cat_sel_id, $temp[$i], $result[0]['version_id']);
							}
						}
						// FAQ Daten dieses Kategorienzweiges lesen
						$this->faq_renumber($this->read_faqdata_for_savefaqorder($this->checked->cat_sel_id));
						// Neue Reihenfolge anzeigen (Templatedaten aktualisieren)
						$this->content->template['faq_data'] = $this->read_faqdata_for_savefaqorder($this->checked->cat_sel_id);
						$this->content->template['faq_is_renumbered'] = 1;
					}
				}
				$this->fetchAllCategories($this->checked->cat_sel_id, "");
			}
		}
		$this->content->template['faq_anzahl'] = $this->count_faqs();
	}

	/**
	 * FAQ Daten lesen f�r save_faq_order()
	 * called by save_faq_order()
	 *
	 * @param string $cat_id
	 * @return mixed faq data
	 */
	function read_faqdata_for_savefaqorder($cat_id = "")
	{
		$sql = sprintf("SELECT
						faq_id,
						question,
						cat_id,
						order_id,
						id,
						T1.version_id
						FROM %s T1
						INNER JOIN %s T2 ON (id = faq_id) AND (T1.version_id = T2.version_id)
						WHERE cat_id = '%d' AND lang_id = '%d' GROUP BY question
						ORDER BY order_id",
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->db->escape($cat_id),
			$this->db->escape($this->cms->lang_back_content_id)
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * FAQs durchsuchen augrund des Inhaltes von $search (Backend, Frontend)
	 * used by all templates
	 *
	 * @param string $search
	 * @return mixed
	 */
	function find_faq($search = "")
	{
		$this->fetchAllCategories(0, "", 2);
		// Datenbank dursuchen und Ergebnisse abholen
		$ergebnis = $this->faqsearcher->do_search(strip_tags($search), $search);
		// Anzahl der Ergebnisse aus der Suche Eigenschaft �bergeben und an Template zur Anzeige der Gesamtanzahl
		$this->content->template['matches'] = $this->weiter->result_anzahl = $this->faqsearcher->result_anzahl;
		// Suchwort(e) ans Template
		$this->content->template['suchworte'] = strip_tags($search);
		// Wenn weitere Ergebnisse angezeigt werden k�nnen (Ergebnisseite anzeigen FE oder BE)
		//$template = defined("admin") ? "faq_back_search_results.html" : "faq_front.html";
		$this->content->template['weiter_link'] = $this->weiter->weiter_link =
			"./plugin.php?menuid=".$this->checked->menuid."&amp;template=faq/templates/".$template."&amp;search_faq=".$search;
		// Wenn es sie gibt, weitere Seiten anzeigen
		$this->weiter->do_weiter("search");
		$this->content->template['menuid'] = $this->checked->menuid;
		// zuweisen der Inhalte aus der Suchfunktion als array
		$this->content->template['faq_search_data'] = $ergebnis;
		return $ergebnis;
	}

	/**
	 * Liste aller FAQs erzeugen (Backend)
	 * called by edit_faq_main()
	 *
	 * @return void
	 */
	function faq_link_list()
	{
		$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_FAQS_PER_PAGE'];
		$this->content->template['menuid'] = $this->checked->menuid;
		$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
		$this->cms->makecmslimit();
		$this->weiter->result_anzahl = $this->count_faqs();
		if ($this->weiter->result_anzahl) {
			// Wenn weitere Ergebnisse angezeigt werden k�nnen
			$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
				"&amp;template=faq/templates/faq_back_edit.html";
			$what = "teaser";
			$this->weiter->do_weiter($what);

			// F�r "ORDER BY order_id" Tabelle T2, sonst Tabelle T1
			#$order = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "T2." : "T1.";
			// Datum in absteigender Folge, sonst aufsteigend
			#$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
			// FAQ Daten bereitstellen; Anzahl der Kategorien und Attachments ermitteln
			$sql = sprintf("SELECT  DISTINCT(T1.id),
									T2.faq_id,
									T1.question,
									T1.created,
									T1.createdby,
									T1.active,
									T2.cat_id,
									COUNT(DISTINCT T2.cat_id) AS catcount,
									COUNT(DISTINCT T3.id) AS attcount
									FROM %s T1
									LEFT JOIN %s T2 ON (T2.faq_id = T1.id) AND (T1.version_id = T2.version_id)
									LEFT JOIN %s T3 ON (T3.faq_id = T1.id) AND (T1.version_id = T3.version_id)
									WHERE T1.lang_id = '%d'
									GROUP BY T1.id
									ORDER BY T1.created DESC " . $this->cms->sqllimit,
				$this->cms->tbname['papoo_faq_content'],
				$this->cms->tbname['papoo_faq_cat_link'],
				$this->cms->tbname['papoo_faq_attachments'],
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$this->content->template['faq_data'][0] = $result = $this->db->get_results($sql, ARRAY_A);
			if (count($result)) {
				// Timestamps Datum umwandeln
				$this->convertDate();
				// Hinweis auf verwaisten Eintrag hinzuf�gen
				$this->content->template['faq_list'] = $this->faq_orphan_check($this->content->template['faq_data'][0]);
				$this->content->template['faq_data'] = array(); // Leer f�r Templatesteuerung
			}
		}
		// Anzahl der Kategorien mitteilen. Bei 0 keine Anzeige mit Meldung.
		$this->content->template['anzahl_faq_cats'] = $this->read_count_all_categories("", 1); // Gesamtanzahl aller Kategorien
	}

	/**
	 * Liste mit offenen Fragen anzeigen, sperren, freigeben oderl�schen (Backend)
	 * template faq_back_offene.html
	 *
	 * @return void
	 */
	function faq_list_offene()
	{
		// FAQ ID
		if (isset($this->checked->faq_id) AND !ctype_digit($this->checked->faq_id)) {
			$fehler = $this->content->template['fehler1'] = 1;
		}
		// freigeben/sperren
		if (isset($this->checked->lock) AND ($this->checked->lock == "j" OR $this->checked->lock == "n")) { }
		elseif (isset($this->checked->lock)) {
			$fehler = $this->content->template['fehler2'] = 1;
		}
		// Woher?
		if (isset($this->checked->src) AND ($this->checked->src == "FE" OR $this->checked->src == "BE")) { }
		elseif (isset($this->checked->src)) {
			$fehler = $this->content->template['fehler3'] = 1;
		}
		// Nicht beim Aufruf der Liste der offenen Fragen
		if (!isset($fehler) || isset($fehler) && !$fehler AND isset($this->checked->src)) {
			// Woher? FE/BE
			$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			if ($this->checked->src == "BE" AND !$this->checked->del) {
				// Version zur alten Version machen
				$sql = sprintf("INSERT INTO %s
								SELECT * FROM %s
								WHERE id = '%d' AND lang_id = '%d'",
					$this->cms->tbname['papoo_faq_versions'],
					$this->cms->tbname['papoo_faq_content'],
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($lang_id)
				);
				$this->db->query($sql);
			}
			if ($this->checked->src == "FE") {
				$table = $this->cms->tbname['papoo_faq_new_question_frontend'];
			}
			if ($this->checked->src == "BE") {
				$table = $this->cms->tbname['papoo_faq_content'];
			}
			// Freigeben / sperren
			if (!$this->checked->del) {
				$sql = sprintf("UPDATE %s
								SET 
								active = '%s',
								changedd = '%s',
								changedby = '%s',
								version_id = '%d'
								WHERE id = '%d' AND lang_id = '%d'",
					$table,
					$this->db->escape($this->checked->lock),
					date('YmdHis'),
					$this->db->escape($this->user->username),
					$this->db->escape($this->getMaxVersionId($this->checked->faq_id)) + 1,
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($lang_id)
				);
				$this->db->query($sql);
				$this->content->template['faq_offene_released'] = $this->checked->lock;
				if ($this->checked->lock == "n") {
					$this->remove_cache_file('plugin:faq/templates/faq_front_list_questions.html');
				} // delete cache file(s)
			}
			else {
				// Schalter: bei catdelete = true ist die L�schabfrage noch nicht erfolgt
				if ($this->checked->del == 1) {
					$this->content->template['delete'] = 1;
					$this->content->template['faq_id'] = $this->checked->faq_id;
				}
				else {
					// L�schen? Ja / Nein
					if ($this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['delete_yes']) {
						// L�schen
						if ($this->checked->src == "BE") {
							$this->faq_delete("faq_back_offene.html");
						}
						elseif ($this->checked->src == "FE") {
							// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
							$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
								$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
								$this->db->escape($this->checked->faq_id)
							);
							$this->db->query($sql);
							// FAQ Daten l�schen
							$sql = sprintf("DELETE FROM %s WHERE id='%s' AND lang_id = '%d'",
								$this->cms->tbname['papoo_faq_new_question_frontend'],
								$this->db->escape($this->checked->faq_id),
								$this->db->escape($this->cms->lang_back_content_id)
							);
							$this->db->query($sql);
						}
						$this->content->template['faq_offene_deleted'] = 1;
						$this->remove_cache_file('plugin:faq/templates/faq_front_list_questions.html'); // delete cache file(s)
					}
				}
			}
		}
		$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_FAQS_PER_PAGE'];
		$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
		$this->cms->makecmslimit();
		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS
							T3.id,
							T3.id1,
							T3.question,
							T3.active,
							T3.createdby,
							T3.created
							FROM ( (SELECT *, 'BE' AS id1 FROM %s T1) UNION (SELECT *, 'FE' AS id1 FROM %s T2) ) AS T3
							WHERE T3.answer = '' AND T3.lang_id = '%d'
							ORDER BY T3.created DESC " . $this->cms->sqllimit,
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_new_question_frontend'],
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$this->content->template['faq_data'][0] = $this->db->get_results($sql, ARRAY_A);
		$this->convertDate();
		$this->content->template['faq_offene'] = $this->content->template['faq_data'][0];
		// Anzahl ermitteln, weiter setzen
		$sql = sprintf("SELECT FOUND_ROWS()");
		$this->content->template['faq_offene_anzahl'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
		$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
			"&amp;template=faq/templates/faq_back_offene.html";
		$this->weiter->do_weiter("teaser");
		// wg. redirect
		IfNotSetNull($this->checked->faq_is_accepted);
		$this->content->template['faq_is_accepted'] = $this->checked->faq_is_accepted;
		$this->content->template['faq_anzahl'] = $this->count_faqs(); // Gesamtanzahl
	}

	/**
	 * Liste mit offenen Fragen anzeigen (Frontend)
	 * template faq_front_list_questions.html
	 *
	 * @return void
	 */
	function faq_list_offene_frontend()
	{
		// Datum in absteigender Folge, sonst aufsteigend
		$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
		$orderby = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "question" : $_SESSION['faq']['FAQ_FAQ_ORDER'];
		$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_CATS_PER_PAGE'];
		$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
		$this->cms->makecmslimit();
		// Offene Fragen FE / BE miteinander zu einer Ergebnismenge (T3) verbinden
		// und daraus die aktiven, offenen Fragen ausw�hlen
		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS
							T3.id,
							T3.id1,
							T3.question,
							T3.active,
							T3.createdby,
							T3.created,
							T3.cat_id
							FROM (
									(SELECT T1.id,
										T1.lang_id,
										T1.question,
										T1.answer,
										T1.active,
										T1.createdby,
										T1.created,
										T4.cat_id,
										 'BE' AS id1
										FROM %s T1
										INNER JOIN %s T4 ON (T1.id = T4.faq_id AND T1.version_id = T4.version_id)) 
								UNION
									(SELECT T2.id,
										T2.lang_id,
										T2.question,
										T2.answer,
										T2.active,
										T2.createdby,
										T2.created,
										T5.cat_id,
										'FE' AS id1
										FROM %s T2
										INNER JOIN %s T5 ON (T2.id = T5.faq_id))
								) AS T3
								WHERE T3.answer = '' AND T3.lang_id = '%d' AND T3.active = 'j'
								ORDER BY T3." .$orderby . $desc . $this->cms->sqllimit,
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->cms->tbname['papoo_faq_new_question_frontend'],
			$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$this->content->template['faq_questions'] = $this->db->get_results($sql, ARRAY_A);
		// Anzahl ermitteln
		$sql = sprintf("SELECT FOUND_ROWS()");
		$this->content->template['faq_offene_anzahl'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
		// Wenn weitere Ergebnisse angezeigt werden k�nnen
		$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
			"&amp;template=faq/templates/faq_front_list_questions.html";
		$this->weiter->do_weiter("teaser");
		$sql = sprintf("SELECT COUNT(T3.answer)
							FROM (
									(SELECT 
										T1.lang_id,
										T1.answer,
										T1.active, T1.question
										FROM %s T1)
								UNION
									(SELECT 
										T2.lang_id,
										T2.answer,
										T2.active,
										T2.question
										FROM %s T2)
								) AS T3
								WHERE T3.answer = '' AND T3.lang_id = '%d' AND T3.active != 'j'",
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_new_question_frontend'],
			$this->db->escape($this->cms->lang_id)
		);
		$this->content->template['faq_offene_anzahl_waiting'] = $this->db->get_var($sql);
		$this->content->template['faq_anzahl'] = $this->count_faqs(); // Gesamtanzahl
	}

	/**
	 * Liste mit gesperrten Fragen anzeigen (Backend)
	 * template faq_back_release.html
	 *
	 * @return void
	 */
	function faq_list_release()
	{
		// FAQ ID
		if (isset($this->checked->faq_id) AND !ctype_digit($this->checked->faq_id)) {
			$fehler = $this->content->template['fehler1'] = 1;
		}
		// freigeben/sperren
		if (isset($this->checked->lock) AND ($this->checked->lock != "j")) {
			$fehler = $this->content->template['fehler2'] = 1;
		}

		#$this->content->template['faq_current_version'] = 0;
		if (!isset($fehler) || isset($fehler) && !$fehler) {
			$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_FAQS_PER_PAGE'];
			$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
			$this->cms->makecmslimit();
			if (isset($this->checked->lock) && $this->checked->lock == "j") { // �berfl�ssige Abfrage s. o., ist hier "j", wenn nicht $fehler
				// Vorhandene Attachments in die neue Version �bernehmen
				$this->transferAttachmentsToNewVersion();
				// Version zur alten Version machen
				$sql = sprintf("INSERT INTO %s
								SELECT * FROM %s
								WHERE id = '%d' AND lang_id = '%d'",
					$this->cms->tbname['papoo_faq_versions'],
					$this->cms->tbname['papoo_faq_content'],
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$this->db->query($sql);
				// Timestamp, Version, active aktualisieren.
				$sql = sprintf("UPDATE %s
								SET version_id = '%d',
								active = '%s',
								changedd = '%s',
								changedby = '%s'
								WHERE id = '%d' AND lang_id = '%d'",
					$this->cms->tbname['papoo_faq_content'],
					$this->db->escape($this->getMaxVersionId($this->checked->faq_id)) + 1,
					'j',
					date('YmdHis'),
					$this->db->escape($this->user->username),
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($this->cms->lang_back_content_id)
				);
				$this->db->query($sql);
				// Zuordnung lesen
				$sql = sprintf("SELECT *
									FROM %s
									WHERE faq_id = '%d' AND version_id = '%d'",
					$this->cms->tbname['papoo_faq_cat_link'],
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($this->getMaxVersionId($this->checked->faq_id)) - 1
				);
				$result = $this->db->get_results($sql, ARRAY_A);
				if (count($result)) {
					foreach ($result as $key =>$value) {
						// Neue Relation(en) zwischen Kategorie und FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
											faq_id = '%s',
											order_id = '%d',
											version_id = '%d'",
							$this->cms->tbname['papoo_faq_cat_link'],
							$this->db->escape($result[$key]['cat_id']),
							$this->db->escape($result[$key]['faq_id']),
							$this->db->escape($result[$key]['order_id']),
							$this->db->escape($result[$key]['version_id']) + 1
						);
						$this->db->query($sql);
					}
				}
				$this->content->template['faq_is_released'] = $this->checked->faq_is_released;
			}
		}
		// Datum in absteigender Folge, sonst aufsteigend
		$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
		$orderby = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "question" : $_SESSION['faq']['FAQ_FAQ_ORDER'];

		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS
						id,
						question,
						created,
						createdby
						FROM %s T1
						WHERE active !='j' AND lang_id = '%d' AND answer != ''
						ORDER BY %s" . $desc . $this->cms->sqllimit,
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($this->cms->lang_back_content_id),
			$orderby
		);
		$this->content->template['faq_data'][0] = $this->db->get_results($sql, ARRAY_A);
		$this->convertDate();
		$this->content->template['faq_release'] = $this->content->template['faq_data'][0];
		// Anzahl ermitteln
		$sql = sprintf("SELECT FOUND_ROWS()");
		$this->content->template['faq_release_anzahl'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
		// Anzahl der FAQs je Seite �bergeben
		$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
			"&amp;template=faq/templates/faq_back_release.html";
		$this->weiter->do_weiter("teaser");
		$this->content->template['faq_anzahl'] = $this->count_faqs(); // Gesamtanzahl
	}

	/**
	 * Liste mit vorgeschlagenen Fragen anzeigen (Backend)
	 * template faq_back_new_frontend.html
	 *
	 * @return void
	 */
	function faq_new_faq_from_frontend()
	{
		IfNotSetNull($this->checked->faq_is_del);
		IfNotSetNull($this->checked->faq_new_is_edit);
		$this->content->template['faq_is_del'] = $this->checked->faq_is_del;
		$this->content->template['faq_is_accepted'] = $this->checked->faq_new_is_edit;
		#$this->content->template['faq_current_version'] = 0;
		if (!isset($this->checked->del) AND isset($this->checked->faq_id)) {
			$this->edit_faq_main();
		}
		elseif (isset($this->checked->del) AND isset($this->checked->faq_id)) {
			// Schalter: bei catdelete = true ist die L�schabfrage noch nicht erfolgt
			if ($this->checked->del == 1) {
				$this->content->template['delete'] = 1;
				$this->content->template['faq_id'] = $this->checked->faq_id;
			}
			else {
				// L�schen? Ja / Nein
				if ($this->checked->submit == $this->content->template['plugin']['faq_back']['submit']['delete_yes']) {
					// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
					$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
						$this->cms->tbname['papoo_faq_cat_link_frontend'],
						$this->db->escape($this->checked->faq_id)
					);
					$this->db->query($sql);
					// FAQ Daten l�schen
					$sql = sprintf("DELETE FROM %s WHERE id='%s' AND lang_id = '%d'",
						$this->cms->tbname['papoo_faq_content_frontend'],
						$this->db->escape($this->checked->faq_id),
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$this->db->query($sql);
					$this->content->template['faq_frontend_deleted'] = 1;
				}
			}
		}
		// Datum in absteigender Folge, sonst aufsteigend
		$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
		$orderby = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "question" : $_SESSION['faq']['FAQ_FAQ_ORDER'];
		$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_FAQS_PER_PAGE'];
		$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
		$this->cms->makecmslimit();
		$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS
						id,
						question,
						answer,
						created,
						createdby
						FROM %s
						WHERE lang_id = '%d'
						ORDER BY %s" . $desc . $this->cms->sqllimit,
			$this->cms->tbname['papoo_faq_content_frontend'],
			$this->db->escape($this->cms->lang_back_content_id),
			$orderby
		);
		$this->content->template['faq_data'][0] = $this->db->get_results($sql, ARRAY_A);
		$this->convertDate();
		$this->content->template['faq_content_frontend'] = $this->content->template['faq_data'][0];
		#$this->content->template['faq_current_version'] = 0;
		// Anzahl ermitteln
		$sql = sprintf("SELECT FOUND_ROWS()");
		$this->content->template['faq_content_frontend_anzahl'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
		// Anzahl der FAQs je Seite �bergeben
		$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
			"&amp;template=faq/templates/faq_back_new_frontend.html";
		$this->weiter->do_weiter("teaser");
		$this->content->template['faq_anzahl'] = $this->count_faqs(); // Gesamtanzahl
	}

	/**
	 * Attachment hochladen (Backend, Frontend)
	 * called by edit_faq_main()
	 *
	 * @return mixed
	 */
	function faq_attachment_upload()
	{
		$fehler = 0;
		// Filename angegeben oder bloss "Hochladen" gedr�ckt?
		if (empty($_FILES['faq_edit_attachment_filename']['name'])) {
			$this->content->template['fehler4'] = $fehler = 1; // Keine Datei ausgew�hlt
		}
		else {
			// Check max. Attachmentsize
			if ($_FILES['faq_edit_attachment_filename']['size'] > $_SESSION['faq']['FAQ_ATTACHSIZE']) {
				$this->content->template['fehler10'] = $fehler = 1; // Attachment lt. Konfig zu gross
				$this->content->template['faq_attachsize'] = $_SESSION['faq']['FAQ_ATTACHSIZE']; // Info Att.size
			}
			else {
				// beim 1. Aufruf die aktuelle version_id + 1, bei folgenden die aktuelle Version nutzen
				$version_id = $this->getMaxVersionId($this->checked->faq_id);
				$version_id = $_SESSION['faq']['new_version_inwork'] ? $version_id : $version_id + 1;
				$result = "";
				// Er�brigt sich beim 1. Aufruf
				if ($_SESSION['faq']['new_version_inwork']) {
					// Pr�fen, ob die Datei f�r diese FAQ-Version bereits hochgeladen wurde.
					// Die Dateien sind identisch, wenn filename und -size gleich sind.
					$sql = sprintf("SELECT *
									FROM %s
									WHERE faq_id = '%d' AND name = '%s' AND size = '%d' AND version_id = '%d'",
						$this->cms->tbname['papoo_faq_attachments'],
						$this->db->escape($this->checked->faq_id),
						$this->db->escape($_FILES['faq_edit_attachment_filename']['name']),
						$this->db->escape($_FILES['faq_edit_attachment_filename']['size']),
						$this->db->escape($version_id)
					);
					$result = $this->db->get_var($sql);
				}
				// Wurde schon f�r diese FAQ hochgeladen, dann Fehler ausl�sen
				if ($result) {
					$this->content->template['fehler9'] = $fehler = 1;
				}
				else {
					// Datei hochladen. Falls der File-Name bei ungleicher Filesize bereits vorhanden ist, eine Kopie erstellen
					// Zielverzeichnis
					$destination_dir = 'plugins/faq/attachments'; // evtl. �ber Konfig zug�nglich machen
					// nicht erlaubte Dateiendungen
					$extensions = array ( 'php', 'php3', 'php4', 'php5', 'phtml', 'html', 'htm', 'exe', 'cgi', 'pl', 'js');
					// basedir und Klasse file_upload bekanntmachen
					$upload_do = new file_upload($this->pfadhier);
					// Upload-Parameter-Aufbau (filename als array!):
					// ((array('filename','filetype','filesize','tmp_name','error')), dir, rename, disallowed ext, block)
					if (!$upload_do->upload($_FILES['faq_edit_attachment_filename'], $destination_dir, 1, $extensions, 1)) {
						// Fehlermeldung ausgeben; Fehler-Text erzeugt die Upload class selbst
						$this->content->template['fehler5'] = $fehler = $upload_do->error;
					}
					else {
						// �bernahme der Attachments aus der alten Version in die neue Version, wenn dies noch nicht erfolgt ist
						// beim 1. Update die aktuelle version_id + 1, bei weiteren Updates die aktuelle Version nutzen
						if (!$_SESSION['faq']['new_version_inwork']) {
							$this->transferAttachmentsToNewVersion();
						}
						// Hole Filedaten
						$files = $upload_do->file;
						$result = "";
						// Er�brigt sich beim 1. Aufruf
						if ($_SESSION['faq']['new_version_inwork']) {
							// Schon in der DB? (gleicher generierter Name, andere Size und Kopie...)
							// Ist, warum auch immer, die Datei im Verzeichnis gel�scht worden,
							// dann haben wir bereits diesen Eintrag in der DB und er darf nicht erneut eingetragen werden.
							$sql = sprintf("SELECT *
											FROM %s
											WHERE faq_id = '%d' AND name_stored = '%s' AND version_id = '%d'",
								$this->cms->tbname['papoo_faq_attachments'],
								$this->db->escape($this->checked->faq_id),
								$this->db->escape($files['name']),
								$this->db->escape($version_id)
							);
							$result = $this->db->get_var($sql);
						}
						// Nur eintragen, wenn noch nicht vorhanden
						if (empty($result)) {
							// neues Attachment in die DB eintragen
							$sql = sprintf("INSERT INTO %s
											SET faq_id = '%d',
												name = '%s', 
												name_stored = '%s',
												size = '%s',
												version_id = '%d'",
								$this->cms->tbname['papoo_faq_attachments'],
								$this->db->escape($this->checked->faq_id),
								$this->db->escape($_FILES['faq_edit_attachment_filename']['name']),
								$this->db->escape($files['name']),
								$this->db->escape($files['size']),
								$this->db->escape($version_id)
							);
							$this->db->query($sql);
						}
						// Meldung Attachment hochgeladen ausgeben und
						$this->content->template['attachment_loaded'] = 1;
						$this->upload_count("count", $this->checked->faq_id); // Pflege des Upload Counters
					}
				}
			}
		}
		return $fehler;
	}

	/**
	 * max. Anzahl von Attachment-Uploads je FAQ-�nderung (Frontend)
	 * called by
	 *
	 * @param string $ctrl
	 * @param $faq_id
	 * @return int
	 */
	function upload_count($ctrl = "", $faq_id)
	{
		// Pr�fen, ob Attachment-Uploads begrenzt sind (0 = unbegrent)
		if ($_SESSION['faq']['FAQ_UPLOADS_PER_FAQ']) {
			$sql = sprintf("SELECT upload_count
									FROM %s
									WHERE id = '%d' AND lang_id = '%d'",
				$this->cms->tbname['papoo_faq_content'],
				$this->db->escape($faq_id),
				$this->db->escape($this->cms->lang_id)
			);
			$upload_count = $this->db->get_var($sql);
			$update = 0;
			switch ($ctrl) {
			case "check":
				$this->content->template['max_upload_reached'] =
				$return = $upload_count >= $_SESSION['faq']['FAQ_UPLOADS_PER_FAQ'] ? 1 : 0;
				break;

			case "count":
				$upload_count++;
				$update = 1;
				$this->content->template['max_upload_reached'] =
					$upload_count >= $_SESSION['faq']['FAQ_UPLOADS_PER_FAQ'] ? 1 : 0;
				break;
			case "decount":
				if ($upload_count) $upload_count--;
				$update = 1;
				break;
			}
			if ($update) {
				$sql = sprintf("UPDATE %s
									SET upload_count = '%d'
									WHERE id = '%d' AND lang_id = '%d'",
					$this->cms->tbname['papoo_faq_content'],
					$this->db->escape($upload_count),
					$this->db->escape($faq_id),
					$this->db->escape($this->cms->lang_id)
				);
				$this->db->query($sql);
			}
		}
		else {
			$return = 0;
		} // keine Begrenzung
		$this->content->template['faq_upload_count'] = $_SESSION['faq']['FAQ_UPLOADS_PER_FAQ']; // Info Upload Anzahl
		return $return;
	}

	/**
	 * Liste aller Attachments einer FAQ ans Template �bergeben & schauen, ob sie noch da sind (Backend, Frontend)
	 * called by edit_faq_main(), faq_front(), faq_redisplay_postdate()
	 *
	 * @param $faq_id
	 * @param string $version_id
	 * @return void
	 */
	function getAttachmentsList($faq_id, $version_id = "")
	{
		$version = $version_id != "" ? " AND version_id = '" . $this->db->escape($version_id) . "'" : "";
		// Hole Attachment-Daten f�rs Template
		$sql = sprintf("SELECT id,
								name,
								name_stored,
								size
						FROM %s
						WHERE faq_id = '%d' $version
						ORDER BY name, size",
			$this->cms->tbname['papoo_faq_attachments'],
			$this->db->escape($faq_id)
		);
		$result = $this->content->template['faq_attach'] = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['faq_id'] = $faq_id;
		// Sind die Dateien auch (noch) vorhanden?
		if ($result) {
			foreach ($this->content->template['faq_attach'] AS $key =>$value) {
				file_exists($this->pfadhier . '/plugins/faq/attachments/' . $this->content->template['faq_attach'][$key]['name_stored']) ? $status = 1 : $status = 0;
				if (!$status) {
					$this->content->template['fehler8'] = 1;
				} // Hilfetext zum Fehler ausgeben
				$this->content->template['faq_attach'][$key]['file_status'] = $status; // Fehlermeldung bei dieser Datei anzeigen
			}
		}
	}

	/**
	 * Ein Attachment l�schen (Backend)
	 * called by edit_faq_main()
	 *
	 * @param int $attach_id
	 * @param int $versions
	 * @return mixed
	 */
	function faq_attachment_delete($attach_id = 0, $versions = 0)
	{
		// Hole den realen Dateinamen der Datei, wie er im Verzeichnis existiert
		$sql = sprintf("SELECT name_stored
						FROM %s
						WHERE id='%s'",
			$this->cms->tbname['papoo_faq_attachments'],
			$this->db->escape($attach_id)
		);
		$name_stored = $this->db->get_var($sql);
		// Ist die Datei vorhanden? Sonst nur in der DB entfernen.
		file_exists($this->pfadhier . '/plugins/faq/attachments/' . $name_stored) ? $status = 1 : $status = 0;
		// F�r das L�schen einer FAQ soll die FAQ auch gel�scht werden k�nnen, wenn durch einen Fehler die zugeh�rige Datei
		// im Verzeichnis nicht mehr existiert. Es w�rde sonst ein Fehler auftreten, der das L�schen der FAQ verhindert.
		// Beim L�schen einer FAQ werden ohnehin alle Attachments gel�scht und es ist daher uninteressant, ob sie schon weg ist.
		// Andere Fehler beim L�schen verhindern jedoch weiterhin das L�schen der FAQ
		if ($status) { // Datei im Verzeichnis l�schen, wenn sie da ist
			// Verzeichnis
			$destination_dir = 'plugins/faq/attachments';
			// basedir und Klasse file_upload bekanntmachen
			$upload_do = new file_upload($this->pfadhier);
			// Upload ((array('filename','filetype','filesize','tmp_name','error')), dir, rename, disallowed ext, block)
			$del_ok = $upload_do->delete_file($name_stored, "/plugins/faq/attachments/");
		}
		// �bernahme der alten Versionen in die neue Version, wenn dies noch nicht erfolgt ist
		// Das zu l�schende Attachment kommt dabei nicht wieder in die DB
		if (!$_SESSION['faq']['new_version_inwork'] AND $versions) {
			$this->transferAttachmentsToNewVersion($attach_id);
		}
		else {
			$version =
				$versions ? "AND version_id = '" . $this->getMaxVersionId($this->checked->faq_id) . "' AND name_stored = '" . $this->db->escape($name_stored) . "'" : "";
			// Eintrag des Attachments in der zuvor angelegten neuen Version l�schen
			$sql = sprintf("DELETE
								FROM %s
								WHERE faq_id ='%d' $version",
				$this->cms->tbname['papoo_faq_attachments'],
				$this->db->escape($this->checked->faq_id)
			);
			$this->db->query($sql);
		}
		// Meldung gel�scht ausgeben
		$this->content->template['attachment_is_del'] = 1;
		$this->upload_count("decount", $this->checked->faq_id); // Pflege des Upload Counters, runterz�hlen
		// Wenn Datei nicht gel�scht werden konnte
		if (!$del_ok) {
			$this->content->template['fehler7'] = $fehler = $upload_do->error;
		}
		return $fehler;
	}

	/**
	 * Attachments aus der aktuellen Version in die neue Version �bernehmen
	 *
	 * @param string $attachid
	 * @return void
	 */
	function transferAttachmentsToNewVersion($attachid = "")
	{
		// �bernahme der alten Versionen in die neue Version, wenn dies noch nicht erfolgt ist
		// Beim L�schen das zu l�schende Attachment nicht mit �bernehmen
		$where = empty($attachid) ? "" : "AND id != '" . $attachid . "'";
		$version_to = $version_from = $this->getMaxVersionId($this->checked->faq_id);
		global $template;
		if (defined("admin") AND $this->template != "faq_back_release.html") {
			$version_from = $this->checked->faq_version_selected;
		} // Backend: die ausgew�hlte Version
		#elseif ($version_from) $version_from = $version_from - 1; // nur wenn > 0 (Frontend immer eine Version davor)
		$version_to++;
		// Alle Attachments zu dieser FAQ holen und in die neue Version �bernehmen
		$sql = sprintf("SELECT *
					FROM %s
					WHERE faq_id = '%d' AND version_id = '%d' $where",
			$this->cms->tbname['papoo_faq_attachments'],
			$this->db->escape($this->checked->faq_id),
			$this->db->escape($version_from)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		// Sind Attachments zur Version vorhanden?
		if (count($result)) {
			// Ja, dann diese Attachments aus alter Version in die neue Version �bernehmen
			foreach ($result AS $key =>$value) {
				$sql = sprintf("INSERT INTO %s
							SET faq_id = '%d',
								name = '%s', 
								name_stored = '%s',
								size = '%s',
								version_id = '%d'",
					$this->cms->tbname['papoo_faq_attachments'],
					$this->db->escape($this->checked->faq_id),
					$this->db->escape($result[$key]['name']),
					$this->db->escape($result[$key]['name_stored']),
					$this->db->escape($result[$key]['size']),
					$this->db->escape($version_to)
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * Verwaiste FAQs kennzeichnen (FAQs ohne Zuordnung zu einer Kategorie) (Backend)
	 * called by faq_link_list()
	 *
	 * @param array $faqdata
	 * @return array
	 */
	function faq_orphan_check($faqdata = array())
	{
		if (count($faqdata)) {
			// catcount ist 0, wenn keine Kategorien zugeordnet sind (Anmerkung: faq_id ist dabei Null)
			foreach ($faqdata as $key =>$data) {
				$faqdata[$key]['orphan'] = $faqdata[$key]['catcount'] ? 0 : 1;
				// Zus�tzliche Pr�fung, ob die cat_link auf einen existenten Eintrag in der faq_categories zeigt
				// Falls nicht, DB-Fehler.
				if ($faqdata[$key]['catcount'])
				{
					$sql = sprintf("SELECT *
										FROM %s
										WHERE id = '%d'",
						$this->cms->tbname['papoo_faq_categories'],
						$this->db->escape($faqdata[$key]['cat_id'])
					);
					$faqdata[$key]['db_error'] = $this->db->get_var($sql) ? 0 : 1;
				}
			}
		}
		return $faqdata;
	}

	/**
	 * Anzeige von <br> unterdr�cken
	 *
	 * @param $nobr
	 * @return unknown
	 */
	function nobr($nobr)
	{
		if (is_array($nobr)) {
			foreach ($nobr AS $key =>$value) {
				$nobr[$key]['question'] = "nobr:" . $nobr[$key]['question'];
			}
		}
		else {
			$nobr = "nobr:" . $nobr;
		}
		return $nobr;
	}

	/**
	 * Template Abfrage L�schen Ja / Nein anzeigen
	 * template backup_back.html
	 *
	 * @param unknown_type $document
	 * @return unknown
	 */
	function ask_for_deletion()
	{
		return $this->content->template['submit'];
	}

	/**
	 * SAVE / Restore FAQ-Tabellen (Backend)
	 * template backup_back.html
	 *
	 * @return void
	 */
	function faq_dump()
	{
		$this->diverse->extern_dump( "faq" );
	}

	/**
	 * Post-Daten erneut anzeigen (Backend)
	 * called by edit_faq_main()
	 *
	 * @return void
	 */
	function faq_redisplay_postdata()
	{
		$this->content->template['faq_data'][0][0]['question'] = $this->nobr($this->checked->faq_question);
		// die Antwort wiederherstellen
		$this->content->template['faq_data'][0][0]['answer'] = $this->checked->faq_answer;
		// Frontend-Freigabe checkbox wiederherstellen
		$this->content->template['checkedrel'] = $this->checked->faq_release == "j" ? "checked='checked'" : "";
		// Kategoriedaten holen und Auswahl(en) wiederherstellen (nicht bei Reset)
		if (count($this->checked->faq_cat_id) AND !isset($this->checked->submit[1])) {
			foreach ($this->checked->faq_cat_id as $key) { $catid[$key]['cat_id'] = $key; }
		}
		else {
			$catid  = array();
		}
		$this->fetchAllCategories($catid, ""); // In jedem Fall, auch, wenn keine Kategorie ausgew�hlt wurde (= Fehler)
	}

	/**
	 * Konfigurationsdaten holen/schreiben inkl. Lese- und Schreibrechte (Backend, Frontend)
	 * und der Session zuweisen
	 * Alle Templates Backend/Frontend (lesend)
	 * template config_back.html Backend (Daten erstellen/modifizieren)
	 * called by post_papoo()
	 * $to_template:
	 * Bei true alles in die DB schreiben und wieder ans Template �bergeben,
	 * bei false die Konfigdaten in die Session einbringen
	 *
	 * @param int $to_template
	 * @return void
	 */
	function faq_get_write_config($to_template = 0)
	{
		if (!$to_template) {
			$results = $this->read_config(false); // Konfig-Daten holen
			// Konfigurationsdaten in die Session als Variablen einbringen
			// FAQ_TITLE, FAQ_DESCRIPT, FAQ_KEYWORDS, FAQ_LAYOUT, FAQ_FAQ_ORDER, FAQ_RENUM_STEP, FAQ_CATS_PER_PAGE,
			// FAQ_FAQ_HEADER, FAQ_FAQS_PER_PAGE, FAQ_ATTACHSHOW, FAQ_ATTACHSIZE, FAQ_UPLOADS_PER_FAQ, FAQ_SHOWNEWFAQ,
			// FAQ_SHOWNEWF, FAQ_AUTODETECT_LANG, FAQ_SENDMAIL, FAQ_ADMINMAIL, FAQ_HEAD_TEXT, FAQ_FOOTER
			foreach ($results AS $row => $value) { $_SESSION['faq']["FAQ_" . strtoupper($row)] = (string)$value; }
			// Hinweis Text in den Templates ausgeben, wenn erst eine Freigabe erforderlich ist
			if ($_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'n') {
				$this->content->template['shownewfaq'] = 1;
			}
			if ($_SESSION['faq']['FAQ_SHOWNEWF'] == 'n') {
				$this->content->template['shownewf'] = 1;
			}
		}
		else {
			if (isset($this->checked->submit) && $this->checked->submit) {
				// Die Daten sollen gespeichert werden
				$_SESSION['faq']['faq_config'] = $this->checked; // User-Eingabedaten sichern
				// Den Kurzformen Daten zuweisen
				$orderid_stepsize = $this->checked->renum_step;
				$cats_per_page = $this->checked->cats_per_page;
				$faqs_per_page = $this->checked->faqs_per_page;
				$attachsize = $this->checked->attachsize;
				$uploads_per_faq = $this->checked->uploads_per_faq;
				// Defaultwerte setzen, falls nichts angegeben wurde. Die Kurzformen daf�r verwenden.
				$orderid_stepsize = empty($orderid_stepsize) ? 10 : $orderid_stepsize; // Schrittweite Reihenfolge
				$cats_per_page = empty($cats_per_page) ? 20 : $cats_per_page; // Anzahl Kategorien je Seite im FE
				$faqs_per_page = empty($faqs_per_page) ? 20 : $faqs_per_page; // Anzahl FAQs je Seite im BE
				$attachsize = empty($attachsize) ? 102400 : $attachsize; // max. Bytesize eines Attachments
				$uploads_per_faq = $uploads_per_faq = "" ? 5 : $uploads_per_faq; // max. Anzahl von Attachment-Uploads
				// Nur rein numerische & integer-Werte zulassen
				if (!ctype_digit((string)$orderid_stepsize)) {
					$fehler = $this->content->template['fehler1'] = 1;
				}
				if (!ctype_digit((string)$cats_per_page)) {
					$fehler = $this->content->template['fehler2'] = 1;
				}
				if (!ctype_digit((string)$faqs_per_page)) {
					$fehler = $this->content->template['fehler3'] = 1;
				}
				if (!ctype_digit((string)$attachsize)) {
					$fehler = $this->content->template['fehler4'] = 1;
				}
				if (isset($this->checked->sendMail) && $this->checked->sendMail == 'j' AND empty($this->checked->adminmail)) {
					$fehler = $this->content->template['fehler5'] = 1;
				}
				if (!ctype_digit((string)$uploads_per_faq)) {
					$fehler = $this->content->template['fehler6'] = 1;
				}
				if (!isset($fehler) || isset($fehler) && !$fehler) {
					// Layout-Typ f�r die DB �bersetzen
					if ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_compact']) {
						$layout = "Kompakt";
					}
					elseif ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_extrapage']) {
						$layout = "Extrapage";
					}
					elseif ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist']) {
						$layout = "Linkliste";
					}
					elseif ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist2']) {
						$layout = "Linkliste 2";
					}
					elseif ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist3']) {
						$layout = "Linkliste 3";
					}
					elseif ($this->checked->layout ==
						$this->content->template['plugin']['faq_back']['formtext']['option_faq_layout_linklist4']) {
						$layout = "Linkliste 4";
					}
					else {
						$layout = "Linkliste 5";
					}
					IfNotSetNull($this->checked->attachshow);
					IfNotSetNull($this->checked->shownewfaq);
					IfNotSetNull($this->checked->shownewf);
					IfNotSetNull($this->checked->autodetect_lang);
					IfNotSetNull($this->checked->sendMail);

					// Checkboxen f�r die DB �bersetzen
					$attachshow = $this->checked->attachshow == 'j' ? 'j' : 'n'; // Nur j/n in die DB
					$shownewfaq = $this->checked->shownewfaq == 'j' ? 'j' : 'n';
					$shownewf = $this->checked->shownewf == 'j' ? 'j' : 'n';
					$autodetect_lang = $this->checked->autodetect_lang == 'j' ? 'j' : 'n';
					$sendMail = $this->checked->sendMail == 'j' ? 'j' : 'n';
					$sql = sprintf("UPDATE %s
									SET title = '%s',
										descript = '%s',
										keywords = '%s',
										layout = '%s',
										faq_order = '%s',
										renum_step = '%d',
										cats_per_page = '%d',
										faq_header = '%s',
										faq_head_text = '%s',
										faq_footer = '%s',
										faqs_per_page = '%d',
										attachshow = '%s',
										attachsize = '%d',
										uploads_per_faq = '%d',
										shownewfaq = '%s',
										shownewf = '%s',
										sendMail = '%s',
										adminmail = '%s'
									WHERE id = '%d' AND lang_id = '%d'",
						$this->cms->tbname['papoo_faq_config'],
						$this->db->escape($this->checked->title),
						$this->db->escape($this->checked->descript),
						$this->db->escape($this->checked->keywords),
						$this->db->escape($layout),
						$this->db->escape($this->checked->faq_order),
						$this->db->escape($orderid_stepsize),
						$this->db->escape($cats_per_page),
						$this->db->escape($this->checked->faq_header),
						$this->db->escape($this->checked->faq_head_text),
						$this->db->escape($this->checked->faq_footer),
						$this->db->escape($faqs_per_page),
						$this->db->escape($attachshow),
						$this->db->escape($attachsize),
						$this->db->escape($uploads_per_faq),
						$this->db->escape($shownewfaq),
						$this->db->escape($shownewf),
						$this->db->escape($sendMail),
						$this->db->escape($this->checked->adminmail),
						$this->db->escape($this->checked->id),
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$this->db->query($sql);
					// Update ID=0 Einheitliche Konfig.-Daten
					$sql = sprintf("UPDATE %s
									SET autodetect_lang = '%s'
									WHERE id = '0'",
						$this->cms->tbname['papoo_faq_config'],
						$this->db->escape($autodetect_lang)
					);
					$this->db->query($sql);
					$this->content->template['faq']['config_saved'] = 1; // Meldung �ber Konfiguration wurde gespeichert
				}
				// Die vorgegebenen Lese-/Schreibrechte schon mal speichern (auch bei evtl. vorangegangenen Fehlern)
				IfNotSetNull($this->checked->faq_back_read_privileges);
				IfNotSetNull($this->checked->faq_back_write_privileges);

				$this->write_group_privileges($this->cms->tbname['papoo_faq_read_privileges'], $this->checked->faq_back_read_privileges); // Leserechte
				$this->write_group_privileges($this->cms->tbname['papoo_faq_write_privileges'], $this->checked->faq_back_write_privileges); // Schreibrechte
			}
			// Wiederherstellen aller anderen User-Eingabedaten im Fehlerfall
			if (isset($fehler) && $fehler) {
				$this->content->template['faq_config'][0] = get_object_vars($_SESSION['faq']['faq_config']);
				$this->content->template['faq_config'][0]['title'] = $this->nobr($this->checked->title);
				$this->content->template['faq_config'][0]['descript'] = $this->nobr($this->checked->descript);
				$this->content->template['faq_config'][0]['keywords'] = $this->nobr($this->checked->keywords);
				$this->content->template['faq_config'][0]['faq_header'] = $this->nobr($this->checked->faq_header);
				$this->content->template['faq_config'][0]['faq_head_text'] = $this->nobr($this->checked->faq_head_text);
				$this->content->template['faq_config'][0]['faq_footer'] = $this->nobr($this->checked->faq_footer);
			}
			else {
				// Konfigurations-Daten aus der DB �bertragen
				$this->content->template['faq_config'][0] = $this->read_config(false);
				$results = $this->read_config(false); // Konfig-Daten holen
				if (count($results) <= 6) {
					$this->content->template['fehler7'] = 1;
				} // 6 nobr's
				else {
					// Konfigurationsdaten in die Session als Variablen einbringen
					// FAQ_TITLE, FAQ_DESCRIPT, FAQ_KEYWORDS, FAQ_LAYOUT, FAQ_FAQ_ORDER, FAQ_RENUM_STEP, FAQ_CATS_PER_PAGE,
					// FAQ_FAQ_HEADER, FAQ_FAQS_PER_PAGE, FAQ_ATTACHSHOW, FAQ_ATTACHSIZE, FAQ_UPLOADS_PER_FAQ, FAQ_SHOWNEWFAQ,
					// FAQ_SHOWNEWF, FAQ_AUTODETECT_LANG, FAQ_SENDMAIL, FAQ_ADMINMAIL, FAQ_HEAD_TEXT, FAQ_FOOTER
					foreach ($results AS $row => $value) {
						$_SESSION['faq']["FAQ_" . strtoupper($row)] = (string)$value;
					}
					// Sprach-Daten aus der DB �bertragen
					$this->content->template['faq_config'][0] =
						array_merge($this->content->template['faq_config'][0], $this->read_config(true));
				}
			}
			$this->content->template['group_privileges'] = $this->read_group_privileges(); // dito Lese-/Schreibrechte, immer
		}
	}

	/**
	 * Konfigurationsdaten aus der DB holen (Backend, Frontend)
	 * called by faq_get_write_config()
	 *
	 * @param bool $lang
	 * @return array
	 */
	function read_config($lang = false)
	{
		if ($lang) {
			$sql = sprintf("SELECT autodetect_lang
							FROM %s
							WHERE id = '0'",
				$this->cms->tbname['papoo_faq_config']
			);
			$result = $this->db->get_row($sql, ARRAY_A);
		}
		else {
			// eingestellte Sprache FE/BE ermitteln
			$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
			$sql = sprintf("SELECT *
							FROM %s
							WHERE lang_id = '%d'",
				$this->cms->tbname['papoo_faq_config'],
				$this->db->escape($lang_id)
			);
			$result = $this->db->get_row($sql, ARRAY_A);
			$result['title'] = $this->nobr($result['title']);
			$result['descript'] = $this->nobr($result['descript']);
			$result['keywords'] = $this->nobr($result['keywords']);
			$result['faq_header'] = $this->nobr($result['faq_header']);
			$result['faq_head_text'] = $this->nobr($result['faq_head_text']);
			$result['faq_footer'] = $this->nobr($result['faq_footer']);
		}
		return $result;
	}

	/**
	 * verf�gbare Gruppen aus der DB holen mit ihren read/write-Privilegien (Backend, Frontend)
	 * gruppeid_read bzw gruppeid_write werden nur gesetzt, wenn auch in der DB gesetzt, sonst null
	 * called by faq_get_write_config()
	 *
	 * @return array
	 */
	function read_group_privileges()
	{
		$sql = sprintf("SELECT 
  						T1.gruppeid,
  						T1.gruppenname,
  						T2.gruppeid AS gruppeid_read,
  						T3.gruppeid AS gruppeid_write
						FROM %s T1
  						LEFT OUTER JOIN %s T2 ON (T1.gruppeid = T2.gruppeid)
  						LEFT OUTER JOIN %s T3 ON (T1.gruppeid = T3.gruppeid)
						ORDER BY T1.gruppenname",
			$this->cms->tbname['papoo_gruppe'],
			$this->cms->tbname['papoo_faq_read_privileges'],
			$this->cms->tbname['papoo_faq_write_privileges']
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Vorgegebene read/write-Privilegien in die DB schreiben (Backend, Frontend)
	 * called by faq_get_write_config()
	 *
	 * @param string $db
	 * @param array $privileges
	 * @return void
	 */
	function write_group_privileges($db = "", $privileges = array())
	{
		// Ist-Zustand der Gruppen-Zuordnung holen
		$sql = sprintf("SELECT *
						FROM %s
						WHERE id = '1'",
			$db
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		// Soll-Zustand mit Ist-Zustand vergleichen, neue Zuordnungen herstellen, abgew�hlte l�schen
		$last = count($result);
		$insert_array = $privileges;
		// Neue Zuordnungen kommen nach $insert_array, zu l�schende verbleiben in $result
		// unver�nderte Zuordnungen aus beiden arrays entfernen
		if (is_array($insert_array) && count($insert_array)) {
			for ($i = 0; $i < $last; $i++) {
				// gruppeid in $insert_array suchen und key erhalten oder false
				$key = array_search($result[$i]['gruppeid'], $insert_array);
				if ($key !== false) {
					// Diese sollen in der DB bleiben, also raus damit aus den Tabellen
					unset($result[$i]);
					unset($insert_array[$key]);
				}
			}
		}
		// Neu hinzugekommene in die DB schreiben
		if (is_array($insert_array) && count($insert_array)) {
			foreach ($insert_array as $neu) {
				// Es wurde ein neues Recht vorgegeben
				$sql = sprintf("INSERT INTO %s
								SET id = '1',
								gruppeid = '%s'",
					$db,
					$this->db->escape($neu)
				);
				$this->db->query($sql);
			}
		}
		// Diejenigen l�schen, die nicht wieder vorgegeben worden sind
		if (is_array($result) && count($result)) {
			foreach ($result as $del =>$value) {
				// Nun den Rest l�schen, da nicht mehr erneut vorgegeben
				$sql = sprintf("DELETE
								FROM %s
								WHERE id = '1' AND gruppeid='%d'",
					$db,
					$this->db->escape($value['gruppeid'])
				);
				$this->db->query($sql);
			}
		}
	}

	/**
	 * ::remove_cache_file()
	 * Cache Dateien entfernen
	 * L�scht alle Dateien im Cache Verzeichnis in denen $url vorkommt
	 *
	 * @param string $menulink
	 * @return void
	 */
	function remove_cache_file($menulink = "")
	{
		// Ist-Zustand der Gruppen-Zuordnung holen
		$sql = sprintf("SELECT *
						FROM %s
						WHERE menulinklang ='%s'",
			$this->cms->tbname['papoo_menu_language'],
			$menulink
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		// cache Dateiname der Datei, die gel�scht werden soll
		$this->url_save =
			$this->cache->make_clean_url($this->cms->webverzeichnis . '/' . $this->cms->webvar . $result[0]['url_menuname']
				. '/') . ".html";
		if (count($result)) {
			$pfad = PAPOO_ABS_PFAD."/cache/";
			$handle = @opendir($pfad);
			while (false !== ($file = @readdir($handle))) {
				@unlink($pfad . $this->url_save);
			}
			@closedir($handle);
		}
	}

	/**
	 * Frontend Kategoriedaten, FAQ-Daten und Suchen, Layoutsteuerung
	 *
	 * @return void
	 */
	function post_papoo()
	{
		if (isset($_SESSION['faq']) && !is_array($_SESSION['faq'])) {
			unset($_SESSION['faq']);
		}
		// Falls vom Frontend ins Backend zum Bearbeiten einer FAQ gewechselt wird,
		// dann die Anzeige von Versionen, das Men� und die Freigabe abschalten
		global $template;
		// Nach diesen ist wieder das Anlegen einer neuen Version erlaubt
		if (!stristr( $template,"faq/templates/faq_back_edit.html") AND !stristr( $template,"faq/templates/faq_front_edit.html") OR
			(isset($this->checked->faq_back_edit_list) AND stristr( $template,"faq/templates/faq_back_edit.html"))
		) {
			// Editor wurde verlassen
			$_SESSION['faq']['new_version_inwork'] = 0;
		}
		if (stristr( $template,"faq/templates/") AND !defined("admin")) { // Nur Frontend und nur FAQ bedienen
			$this->autodetect_language(); // Frontend Sprache ermitteln und ggfs. setzen
			$this->faq_get_write_config(0); // Konfigurationsdaten in die Session als Variablen einbringen
			// Suchmaschinen META Daten
			$this->content->template['site_title'] = $_SESSION['faq']['FAQ_TITLE']; // Seitentitel
			$this->content->template['keywords'] = $_SESSION['faq']['FAQ_KEYWORDS']; // Keywords
			$this->content->template['description'] = $_SESSION['faq']['FAQ_DESCRIPT']; // Description
			$this->content->template['faq_header'] = $_SESSION['faq']['FAQ_FAQ_HEADER']; // �berschrift
			$this->content->template['faq_head_text'] = $_SESSION['faq']['FAQ_FAQ_HEAD_TEXT']; // �berschrift
			$this->content->template['faq_footer'] = $_SESSION['faq']['FAQ_FAQ_FOOTER']; // �berschrift

			// Dateinamen des Templates extrahieren
			// Liegen keine Leserechte f�r den angemeldeten User oder f�r "jeder" (unangemeldet) vor,
			// dann Hinweis auf keine Leserechte ausgeben
			if (!$this->check_read_write_access(0)) { // 0 = Leserechte pr�fen.
				$this->content->template['faq_no_readaccess'] = 1;
			}
			elseif ($this->template == "faq_front.html") { // alle Daten an faq_front.html übergeben
				$this->faq_front();
			}
			// Ist die userid hier 11 (jeder), dann liegt keine Anmeldung vor (f�r userid 11 ist keine Anmeldung m�glich)
			// In dem Fall Hinweis auf "Anmeldung zum Schreiben erforderlich" ausgeben
			if (empty ($this->user->userid) or $this->user->userid == "11") {
				$this->content->template['faq_no_login'] = 1;
			}
			else {
				// Ein User ist jetzt eingeloggt - pr�fen ob Schreibrechte vorliegen (= 1)
				if (!$this->check_read_write_access(1)) { // Keine Schreibrechte
					$this->content->template['faq_no_writeaccess'] = 1;
				}
				else {
					$this->content->template['faq_user_may_write'] = 1; // Alles ok, User darf schreiben
					switch ($this->template) {
						// Startseite
					case "faq_front.html":
						break;
						// Neue FAQ erstellen
					case "faq_front_new.html":
						$this->faq_front_new_faq();
						break;
						// FAQ bearbeiten
					case "faq_front_edit.html":
						$this->content->template['faq_main_id'] = $this->checked->faq_main_id;
						$this->edit_faq_main();
						break;
						// Neue Frage stellen
					case "faq_front_new_question.html":
						$this->faq_front_new_question();
						break;
						// Offene Fragen anzeigen
					case "faq_front_list_questions.html":
						$this->faq_list_offene_frontend();
						break;
						// Frage beantworten
					case "faq_front_answer_a_question.html":
						$this->faq_front_answer_a_question();
						break;
					default:
						break;
					}
				}
			}
		}
	}

	/**
	 * Frontend: Daten ans Template �bergeben
	 * called by post_papoo()
	 * alle Layouts im Frontend
	 *
	 * @return void
	 */
	function faq_front()
	{
		// Meldung ausgeben, falls vorher gespeichert wurde (redirects)
		IfNotSetNull($this->checked->faq_is_edit);
		IfNotSetNull($this->checked->faq_is_new);
		IfNotSetNull($this->checked->faq_question_is_new);
		IfNotSetNull($this->checked->faq_question_is_answered);

		$this->content->template['faq_is_edit'] = $this->checked->faq_is_edit;
		$this->content->template['faq_is_new'] = $this->checked->faq_is_new;
		$this->content->template['faq_question_is_new'] = $this->checked->faq_question_is_new;
		$this->content->template['faq_question_is_answered'] = $this->checked->faq_question_is_answered;
		// Suchwort initialisieren
		$suchwort = isset($this->checked->search_faq) && $this->checked->search_faq ? $this->checked->search_faq : null;
		// Suchen?
		if ($suchwort) {
			$this->content->template['cat_selected_id']  = $this->checked->faq_main_id;
			$this->content->template['faq_search_data'] = $this->find_faq($suchwort); // Suchen $ Ergebnisse ans Template
			// convertDate erwartet faq_data
			$this->content->template['faq_data'] = $this->content->template['faq_search_data'];
			$this->convertDate();
			$this->content->template['faq_search_data'] = $this->content->template['faq_data'];
			$this->content->template['faq_data'] = array();
			// aktuelle Seite (weiter) beim Suchen wiederherstellen, damit wir bei der Auswahl einer Antwort dort bleiben
			$this->content->template['page'] = ctype_digit($this->checked->page) ? $this->checked->page : 1;
			$this->content->template['search_matches'] = ctype_digit((string)$_SESSION['faq']['search_matches']) ? $_SESSION['faq']['search_matches'] : 0;
			if (!count($this->content->template['faq_search_data'])) {
				$this->content->template['faq_nomatch'] = 1;
			}
		}
		else {
			// Hole Kategoriedaten. FAQs je Kategorie z�hlen, jedoch nur die freigegebenen FAQs
			IfNotSetNull($this->checked->get_faq_single_cat);
			$this->fetchAllCategories(0, "", 2, $this->checked->get_faq_single_cat); // F�r alle anderen F�lle ausser -> "Extrapage"
			//$this->content->template['cat_data'];
		}
		// Layout Linkliste/Linkliste 5: alle Fragen und Antworten, alle Attachments ausgeben
		if ($_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste" OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 5") {
			if (count($this->content->template['cat_data'])) {
				// FAQ Daten bereitstellen aus den Tabellen content, versions, cat_link (nur die cat_id)
				foreach ($this->content->template['cat_data'] AS $key =>$value) {
					// Hole alle aktiven FAQ-Ids zu dieser Kategorie und ber�cksichtige die order_id (lt. Konfig.)
					$faqids = $this->getFaqIDsByCatId($this->content->template['cat_data'][$key]['id']);
					// Zusammenstellung der FAQ Daten f�r das Template
					if (count($faqids)) {
						foreach ($faqids AS $key2 =>$value2) {
							$sql = sprintf("SELECT
													T1.version_id,
													T1.question,
													T1.answer,
													T1.created,
													T1.createdby,
													T1.changedd,
													T1.changedby,
													T2.cat_id,
													T2.faq_id,
													T1.id
													FROM %s T2
													INNER JOIN %s T1 ON (T2.faq_id = T1.id) AND (T2.version_id = T1.version_id)
													WHERE T1.id = '%d' AND T2.cat_id = '%d'
															AND T1.lang_id = '%d' AND answer != ''",
								$this->cms->tbname['papoo_faq_cat_link'],
								$this->cms->tbname['papoo_faq_content'],
								$this->db->escape($faqids[$key2]['faq_id']),
								$this->db->escape($this->content->template['cat_data'][$key]['id']),
								$this->db->escape($this->cms->lang_id)
							);
							$result = $this->db->get_results($sql, ARRAY_A);
							$this->content->template['faq_data'][$key][$key2] = $result[0];
						}
					}
				}
				$this->convertDate(); // Timestamps aufbereiten
				if ($_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 5") {
					$this->content->template['faq_selected_id'] = $this->checked->faq_id;
				}
			}
		}
		// f�r die Layout-Typen (Kompakt, Extrapage, Linkliste2, Linkliste3, Linkliste 4)
		else {
			// Eine ausgew�hlte Antwort soll zus�tzlich angezeigt werden
			// aufklappen; per Klick auf die Frage/Kat. bzw. neue Seite)
			// Falls nicht numerisch, nur eine Liste der FAQs zur Kategorie anzeigen
			if (isset($this->checked->faq_id) && ctype_digit($this->checked->faq_id)
				OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 2"
				OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 3") {
				// faq_id ans Template zur Anzeige der ausgew�hlten Antwort zur�ck (select-Kennzeichnung)
				$this->content->template['faq_selected_id'] = $this->checked->faq_id;
				if ($_SESSION['faq']['FAQ_LAYOUT'] == "Extrapage") {
					// Nur die Daten der gew�hlten Kategorie f�r die Extraseite holen
					$this->content->template['cat_data'] = $this->getCatData($this->checked->faq_main_id, 0, 2);
					$this->content->template['faq_show_extrapage'] = 1;
				}
			}
			else {
				// Layout Linkliste 4:  Alle FAQ-Daten
				if ($_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 4") {
					$_SESSION['suchanzahl'] = $_SESSION['faq']['FAQ_CATS_PER_PAGE'];
					$this->cms->system_config_data['config_paginierung'] = $_SESSION['suchanzahl'];
					$this->cms->makecmslimit();
					// Datum in absteigender Folge, sonst aufsteigend
					$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
					$sql = sprintf("SELECT SQL_CALC_FOUND_ROWS *
										FROM %s T2
										INNER JOIN %s T1 ON (T2.id = T1.faq_id)
										AND (T2.version_id = T1.version_id)
										WHERE lang_id = '%d' AND answer != '' AND active = 'j'
										ORDER BY %s" . $desc . $this->cms->sqllimit,
						$this->cms->tbname['papoo_faq_content'],
						$this->cms->tbname['papoo_faq_cat_link'],
						$this->db->escape($this->cms->lang_id),
						$_SESSION['faq']['FAQ_FAQ_ORDER']
					);
					$this->content->template['faq_data'][0] = $this->db->get_results($sql, ARRAY_A);
					$sql = sprintf("SELECT FOUND_ROWS()");
					$this->content->template['faq_anzahl'] = $this->weiter->result_anzahl = $this->db->get_var($sql);
					// Wenn weitere Ergebnisse angezeigt werden k�nnen
					$this->weiter->weiter_link = "./plugin.php?menuid=" . $this->checked->menuid .
						"&amp;template=faq/templates/faq_front.html";
					$this->weiter->do_weiter("teaser");
					// F�r Linkiste 4 ol-Startwert setzen
					$this->content->template['ol_start'] = $this->checked->page ? ($this->checked->page * $_SESSION['faq']['FAQ_CATS_PER_PAGE']) - ($_SESSION['faq']['FAQ_CATS_PER_PAGE'] - 1) : 1;
				}
			}
			// FAQ-Daten f�r die ausgew�hlte Kategorie ausgeben
			// Falls nicht numerisch, nur eine Liste der Kategorien anzeigen
			if (isset($this->checked->faq_main_id) && ctype_digit($this->checked->faq_main_id)) {
				// cat_id ans Template zur Anzeige der ausgew�hlten Fragen (select-Kennzeichnung)
				// und zur Unterdr�ckung nicht ausgew�hlter Kategorien (Linkliste2, Linkliste3)
				$this->content->template['cat_selected_id'] = $this->checked->faq_main_id;
				if (empty($suchwort)) {
					// Hole alle aktiven FAQ-Ids zu dieser Kategorie und ber�cksichtige die order_id (lt. Konfig.)
					$faqids = $this->getFaqIDsByCatId($this->checked->faq_main_id);
					// Zusammenstellung der Daten (FAQ / Cat) f�r das Template
					if (count($faqids)) {
						foreach ($faqids AS $key =>$value)
						{
							$sql = sprintf("SELECT
													T1.version_id,
													T1.question,
													T1.answer,
													T1.created,
													T1.createdby,
													T1.changedd,
													T1.changedby,
													T2.cat_id,
													T2.faq_id,
													T3.id,
													T3.catname,
													T3.catdescript
													FROM %s T2
													INNER JOIN %s T1 ON (T2.faq_id = T1.id)
													INNER JOIN %s T3 ON (T2.cat_id = T3.id)
													WHERE T1.id = '%d' AND T2.cat_id = '%d'
															AND T1.lang_id = '%d' AND T3.lang_id = '%d' AND answer != ''",
								$this->cms->tbname['papoo_faq_cat_link'],
								$this->cms->tbname['papoo_faq_content'],
								$this->cms->tbname['papoo_faq_categories'],
								$faqids[$key]['faq_id'],
								$this->db->escape($this->checked->faq_main_id),
								$this->db->escape($this->cms->lang_id),
								$this->db->escape($this->cms->lang_id)
							);
							$result = $this->db->get_results($sql, ARRAY_A);
							$this->content->template['faq_data'][0][$key] = $result[0];
						}
						$this->convertDate();
					}
				}
			}
		}
		if ($suchwort) {
			$this->content->template['faq_data'] = $this->content->template['faq_search_data'];
		}
		// Attachment-Daten bereitstellen Layouts Linkliste/Linkliste2/Linkliste3/Linkliste4/Linkliste5
		// aber nur bei vorhandenen FAQ Daten
		if (isset($this->content->template['faq_data']) && $_SESSION['faq']['FAQ_ATTACHSHOW'] == "j" AND count($this->content->template['faq_data'])
			AND
			($_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste"
				OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 2"
				OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 3"
				OR $_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 4")) {
			// Attachmentdaten f�r jede FAQ holen
			foreach ($this->content->template['faq_data'] AS $key =>$value) {
				if (count($value)) {
					foreach ($value AS $key2 =>$value2) {
						// getAttachmentsList nutzt das array, daher vorher sichern -> $save
						IfNotSetNull($this->content->template['faq_attach']);
						$save = $this->content->template['faq_attach'];
						if ($_SESSION['faq']['FAQ_LAYOUT'] == "Linkliste 4") {
							$id = $this->content->template['faq_data'][$key][$key2]['id'];
						}
						else {
							$id = $this->content->template['faq_data'][$key][$key2]['faq_id'];
						}
						$this->getAttachmentsList($id, $this->content->template['faq_data'][$key][$key2]['version_id']);
						// Sichern der Daten von getAttachmentsList ->$save2
						$save2 = $this->content->template['faq_attach'];
						// Templatedaten wiederherstellen
						$this->content->template['faq_attach'] = $save;
						// Daten aus getAttachmentsList hinzuf�gen
						$this->content->template['faq_attach'][$key][$key2] = $save2;
					}
				}
				else {
					$this->content->template['faq_attach'][$key][$key2] = array();
				} // keine Attachments zu dieser FAQ
			}
			if ($suchwort) {
				$this->content->template['faq_search_data'] = $this->content->template['faq_data'];
			}
		}
		else {
			// Attachments bereitstellen f�r Layouts Kompakt, Extrapage, Linkliste 5
			if (isset($this->content->template['faq_data']) && $_SESSION['faq']['FAQ_ATTACHSHOW'] == "j" AND count($this->content->template['faq_data'])) {
				// Aktuelle, aktive Versions-Id zu dieser FAQ ermitteln
				#$version_id = $this->getMaxVersionIdActive($this->checked->faq_id);
				// Attachments holen
				#$this->getAttachmentsList($this->checked->faq_id, $version_id[0]['version_id']);
				IfNotSetNull($this->checked->faq_id);
				$this->getAttachmentsList($this->checked->faq_id, $this->getMaxVersionId($this->checked->faq_id));
			}
		}
		$this->content->template['faq_layout'] = $_SESSION['faq']['FAQ_LAYOUT']; // Layout-Typ aus Konfig ans Template
	}

	/**
	 * Hole die FAQ-Ids zu einer Kategorie unter Ber�cksichtigung von ORDER BY, wie in der Konfig. festgelegt (Frontend)
	 * called by
	 *
	 * @param int $cat_id
	 * @return array|void
	 */
	function getFaqIDsByCatId($cat_id = 0)
	{
		// F�r "ORDER BY order_id" Tabelle T2, sonst Tabelle T1
		$order = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "order_id" ? "T2." : "T1.";
		// Datum in absteigender Folge, sonst aufsteigend
		$desc = $_SESSION['faq']['FAQ_FAQ_ORDER'] == "created" ? " DESC " : " ";
		$sql = sprintf("SELECT faq_id
  							FROM %s T2
							INNER JOIN %s T1 ON (T2.faq_id = T1.id) AND (T2.version_id = T1.version_id)
 							WHERE cat_id = '%d' AND T1.active = 'j'
							ORDER BY %s" . $desc,
			$this->cms->tbname['papoo_faq_cat_link'],
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($cat_id),
			$order.$_SESSION['faq']['FAQ_FAQ_ORDER']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return ($result);
	}

	/**
	 * H�chste aktive version_id mit Ursprungstabelle ermitteln (Frontend)
	 * called by
	 *
	 * @param int $faq_id
	 * @return array|void
	 */
	function getMaxVersionIdActive($faq_id = 0)
	{
		$sql = sprintf("SELECT MAX(COALESCE(T1b.version_id, T2.version_id)) AS version_id,
							CASE
								WHEN T1b.version_id > T2.version_id THEN 'faq_content'
								WHEN T1b.version_id >= 0  THEN 'faq_content'
								WHEN T2.version_id >= 0  THEN 'faq_versions'
							END AS fromtable
  							FROM %s AS T1a
							LEFT JOIN %s AS T1b ON T1b.id = T1a.id AND T1b.active = 'j'
							LEFT JOIN %s AS T2 ON T2.id = T1a.id AND T2.active = 'j'
 							WHERE T1a.id = '%d'",
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_content'],
			$this->cms->tbname['papoo_faq_versions'],
			$this->db->escape($faq_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return ($result);
	}

	/**
	 * H�chste vorhandene version_id ermitteln (Frontend)
	 * (H�chste version_id ist immer in der papoo_faq_content)
	 * Zus�tzlich den Freigabe-Status
	 * called by
	 *
	 * @param int $faq_id
	 * @return unknown
	 */
	function getMaxVersionId($faq_id = 0)
	{
		$sql = sprintf("SELECT version_id, active
						FROM %s
						WHERE id = '%d'",
			$this->cms->tbname['papoo_faq_content'],
			$this->db->escape($faq_id)
		);
		$result = $this->db->get_var($sql);
		return ($result);
	}

	/**
	 * Frontend Neue FAQ speichern
	 * template faq_front_new.html
	 * called by post_papoo()
	 *
	 * @return void
	 */
	function faq_front_new_faq()
	{
		// Kategorie ID muss numerisch sein
		if (isset($this->checked->faq_main_id) AND !ctype_digit($this->checked->faq_main_id)) {
			$this->content->template['fehler1'] = 1;
		}
		else {
			if ($this->checked->submit) { // Daten wurden zum Eintragen abgeschickt
				// Fehler, falls die Frage fehlt
				if (empty($this->checked->faq_question)) {
					$this->content->template['fehler2'] = $fehler = 1;
				}
				// Die Antwort ist leer und inakzeptabel, wenn nur nicht druckbare Zeichen gefunden wurden
				$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
				if (empty($answer)) {
					$this->content->template['fehler3'] = $fehler = 1;
				}
				if (!isset($fehler) || isset($fehler) && !$fehler) {
					#$answer = htmlentities($this->diverse->do_pfadeanpassen($answer), ENT_QUOTES);
					// Wenn keine Adminfreigabe erforderlich ist
					if ($_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'j') {
						// Speichern der FAQ Daten
						$sql = sprintf("INSERT INTO %s
										SET question = '%s',
											answer = '%s',
											active = 'j',
											created ='%s',
											createdby = '%s',
											lang_id = '%d',
											version_id = 0,
											upload_count = 0",
							$this->cms->tbname['papoo_faq_content'],
							$this->db->escape(trim($this->checked->faq_question)),
							$this->db->escape($answer),
							date('YmdHis'),
							$this->db->escape($this->user->username),
							$this->db->escape($this->cms->lang_id)
						);
						$this->db->query($sql);
						// Die neue Record-ID merken
						$faq_id = $this->db->insert_id;
						// Relation zwischen Kategorie und FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
											faq_id = '%s',
											order_id = '%d',
											version_id = 0",
							$this->cms->tbname['papoo_faq_cat_link'],
							$this->db->escape($this->checked->faq_main_id),
							$this->db->escape($faq_id),
							$this->db->escape($this->getNextOrderId("", $this->checked->faq_main_id, 1))
						);
						$this->db->query($sql);
					}
					else {
						// Muss erst freigegeben werden: Speichern der Daten in die Tabelle faq_new
						$sql = sprintf("INSERT INTO %s
										SET question = '%s',
											answer = '%s',
											active = 'n',
											created ='%s',
											createdby = '%s',
											lang_id = '%d',
											version_id = 0,
											upload_count = 0",
							$this->cms->tbname['papoo_faq_content_frontend'],
							$this->db->escape(trim($this->checked->faq_question)),
							$this->db->escape($answer),
							date('YmdHis'),
							$this->db->escape($this->user->username),
							$this->db->escape($this->cms->lang_id)
						);
						$this->db->query($sql);
						// Die neue Record-ID merken
						$faq_id = $this->db->insert_id;
						// Relation zwischen Kategorie und FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
											faq_id = '%s',
											order_id = '%d',
											version_id = 0",
							$this->cms->tbname['papoo_faq_cat_link_frontend'],
							$this->db->escape($this->checked->faq_main_id),
							$this->db->escape($faq_id),
							$this->db->escape($this->getNextOrderId("", $this->checked->faq_main_id, 1))
						);
						$this->db->query($sql);
					}
					// Falls Freigabe erforderlich und E-Mail an Admin, dann E-Mail an Admin senden
					if ($_SESSION['faq']['FAQ_SENDMAIL'] == 'j' AND $_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'n') {
						$this->sendMail();
					}
					// raus aus dem Template & zur Hauptseite springen
					$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
						"&template=faq/templates/faq_front.html&faq_is_new=1";
					if ( $_SESSION['debug_stopallredirect'] ) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header( "Location: $location_url" );
					}
					exit;
				}
				// Eingabe-Daten wiederherstellen
				$this->content->template['faq_question'] = $this->nobr($this->checked->faq_question); // Frage
				$this->content->template['faq_new_answer'] = $this->checked->faq_answer; // Antwort
			}
			$this->content->template['cat_data'] = $this->getCatData($this->checked->faq_main_id, 0, 1);
			$this->content->template['faq_main_id'] = $this->checked->faq_main_id; // hidden Kategorie ID zum Speichern merken
		}
	}

	/**
	 * Frontend Neue Frage speichern
	 * template faq_front_new_question.html
	 * called by post_papoo()
	 *
	 * @return void
	 */
	function faq_front_new_question()
	{
		// Kategorie ID muss numerisch sein
		if (isset($this->checked->faq_main_id) AND !ctype_digit($this->checked->faq_main_id)) {
			$this->content->template['fehler1'] = 1;
		}
		else {
			if ($this->checked->submit) { // Daten wurden zum Eintragen abgeschickt
				// Fehler, falls die Frage fehlt
				if (empty($this->checked->faq_question)) {
					$this->content->template['fehler2'] = $fehler = 1;
				}
				if (!isset($fehler) || isset($fehler) && !$fehler) {
					// Wenn keine Adminfreigabe erforderlich ist
					if ($_SESSION['faq']['FAQ_SHOWNEWF'] == 'j') {
						// Speichern der FAQ Daten
						$sql = sprintf("INSERT INTO %s
										SET question = '%s',
											answer = '',
											active = 'j',
											created ='%s',
											createdby = '%s',
											lang_id = '%d',
											version_id = 0,
											upload_count = 0",
							$this->cms->tbname['papoo_faq_content'],
							$this->db->escape(trim($this->checked->faq_question)),
							date('YmdHis'),
							$this->db->escape($this->user->username),
							$this->db->escape($this->cms->lang_id)
						);
						$this->db->query($sql);
						// Die neue Record-ID merken
						$faq_id = $this->db->insert_id;
						// Relation zwischen Kategorie und FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
											faq_id = '%s',
											order_id = '%d',
											version_id = 0",
							$this->cms->tbname['papoo_faq_cat_link'],
							$this->db->escape($this->checked->faq_main_id),
							$this->db->escape($faq_id),
							$this->db->escape($this->getNextOrderId("", $this->checked->faq_main_id, 1))
						);
						$this->db->query($sql);
					}
					else {
						// Muss erst freigegeben werden: Speichern der Daten in die Tabelle faq_new
						$sql = sprintf("INSERT INTO %s
										SET question = '%s',
											answer = '',
											active = 'n',
											created ='%s',
											createdby = '%s',
											lang_id = '%d',
											version_id = 0,
											upload_count = 0",
							$this->cms->tbname['papoo_faq_new_question_frontend'],
							$this->db->escape(trim($this->checked->faq_question)),
							date('YmdHis'),
							$this->db->escape($this->user->username),
							$this->db->escape($this->cms->lang_id)
						);
						$this->db->query($sql);
						// Die neue Record-ID merken
						$faq_id = $this->db->insert_id;
						// Relation zwischen Kategorie und FAQ herstellen
						$sql = sprintf("INSERT INTO %s
										SET cat_id = '%s',
											faq_id = '%s',
											order_id = '%d',
											version_id = 0",
							$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
							$this->db->escape($this->checked->faq_main_id),
							$this->db->escape($faq_id),
							$this->db->escape($this->getNextOrderId("", $this->checked->faq_main_id, 1))
						);
						$this->db->query($sql);
					}
					// Falls Freigabe erforderlich und E-Mail an Admin, dann E-Mail an Admin senden
					if ($_SESSION['faq']['FAQ_SENDMAIL'] == 'j' AND $_SESSION['faq']['FAQ_SHOWNEWF'] == 'n') {
						$this->sendMail();
					}
					$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
						"&template=faq/templates/faq_front.html&faq_question_is_new=1";
					if ( $_SESSION['debug_stopallredirect'] ) {
						echo '<a href="' . $location_url . '">Weiter</a>';
					}
					else {
						header( "Location: $location_url" );
					}
					exit;
				}
				// Eingabe-Daten wiederherstellen
				$this->content->template['faq_question'] = $this->nobr($this->checked->faq_question); // Frage
			}
			$this->content->template['cat_data'] = $this->getCatData($this->checked->faq_main_id, 0, 1);
			// Hinweis Text ausgeben, wenn erst eine Freigabe erforderlich ist
			if ($_SESSION['faq']['FAQ_SHOWNEWF'] == 'n') {
				$this->content->template['shownewf'] = 1;
			}
			$this->content->template['faq_main_id'] = $this->checked->faq_main_id; // hidden Kategorie ID zum Speichern merken
		}
	}

	/**
	 * Eine Frage im Frontend beantworten
	 * template faq_front_answer_a_question.html
	 * called by post_papoo()
	 *
	 * @return void
	 */
	function faq_front_answer_a_question()
	{
		// FAQ ID
		if (isset($this->checked->faq_id) AND !ctype_digit($this->checked->faq_id)) {
			$fehler = $this->content->template['fehler1'] = 1;
		}
		// Kategorien IDs (array)
		if (isset($this->checked->faq_cat_id) AND !$this->checkNumeric($this->checked->faq_cat_id)) {
			$fehler = $this->content->template['fehler2'] = 1;
		}
		// Quelle (aus FE / BE)
		if (!isset($this->checked->src)) {
			$fehler = $this->content->template['fehler3'] = 1;
		}
		// Fehler, falls die Frage / Antwort beim submit fehlt
		if ($this->checked->submit) { // Daten wurden zum Eintragen abgeschickt
			if (empty($this->checked->faq_question)) {
				$this->content->template['fehler4'] = $fehler = 1;
			}
			// Die Antwort ist leer und inakzeptabel, wenn nur nicht druckbare Zeichen gefunden wurden
			$answer = $this->check_string_empty($this->checked->faq_answer, 32, 255, true, true) ? "" : $this->checked->faq_answer;
			if (empty($answer)) {
				$this->content->template['fehler5'] = $fehler = 1;
			}
			#else $answer = htmlentities($this->diverse->do_pfadeanpassen($answer), ENT_QUOTES);
		}
		if (isset($fehler) && $fehler) {
			$this->faq_redisplay_postdata();
		} // Postdaten wieder anzeigen
		elseif ($this->checked->submit) {
			if ($this->checked->src == "FE") {
				// Wenn keine Adminfreigabe erforderlich ist
				if ($_SESSION['faq']['FAQ_SHOWNEWFAQ'] == 'j') {
					// Originale Daten holen und aus FE als eine alte Version speichern
					$result = $this->getDataFromTable($this->cms->tbname['papoo_faq_new_question_frontend']);
					// Eingabe-Daten als neue Version 1 speichern
					$sql = sprintf("INSERT INTO %s
										SET
										version_id = '1',
										lang_id = '%d',
										question = '%s',
										answer = '%s',
										active = '%s',
										upload_count = '0',
										created = '%s',
										createdby = '%s',
										changedd = '%s',
										changedby = '%s'",
						$this->cms->tbname['papoo_faq_content'],
						$this->db->escape($result[0]['lang_id']),
						$this->db->escape(trim($this->checked->faq_question)),
						$this->db->escape($answer),
						'j',
						$this->db->escape($result[0]['created']),
						$this->db->escape($result[0]['createdby']),
						date('YmdHis'),
						$this->db->escape($this->user->username)
					);
					$this->db->query($sql);
					// Die neue Record-ID merken
					$faq_id = $this->db->insert_id;
					// Gelesene Daten als Version 0 speichern
					$sql = sprintf("INSERT INTO %s
										SET
										id = '%d',
										version_id = '0',
										lang_id = '%d',
										question = '%s',
										answer = '%s',
										active = '%s',
										upload_count = '0',
										created = '%s',
										createdby = '%s',
										changedd = '%s',
										changedby = '%s'",
						$this->cms->tbname['papoo_faq_versions'],
						$this->db->escape($faq_id),
						$this->db->escape($result[0]['lang_id']),
						$this->db->escape($result[0]['question']),
						$this->db->escape($result[0]['answer']),
						$this->db->escape($result[0]['active']),
						$this->db->escape($result[0]['created']),
						$this->db->escape($result[0]['createdby']),
						$this->db->escape($result[0]['changedd']),
						$this->db->escape($result[0]['changedby'])
					);
					$this->db->query($sql);
					// Kategorie-Zuordnung holen
					$result = $this->getDataFromTable($this->cms->tbname['papoo_faq_cat_link_new_question_frontend']);
					// original Zuordnung als Version 0 speichern
					$sql = sprintf("INSERT INTO %s
										SET cat_id = '%d',
										faq_id = '%d',
										order_id = '%d',
										version_id = '0'",
						$this->cms->tbname['papoo_faq_cat_link'],
						$this->db->escape($result[0]['cat_id']),
						$this->db->escape($faq_id),
						$this->db->escape($result[0]['order_id'])
					);
					$this->db->query($sql);
					$catid = $this->saveFaqCatLink_sub($faq_id, $version_id = 1);
					// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
					$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
						$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
						$this->db->escape($this->checked->faq_id)
					);
					$this->db->query($sql);
					// FAQ Daten l�schen
					$sql = sprintf("DELETE FROM %s WHERE id='%s'",
						$this->cms->tbname['papoo_faq_new_question_frontend'],
						$this->db->escape($this->checked->faq_id)
					);
					$this->db->query($sql);
				}
				else { // Adminfreigabe ist erforderlich
					// Originale Daten holen
					$result = $this->getDataFromTable($this->cms->tbname['papoo_faq_new_question_frontend']);
					// Gelesene Daten speichern
					$sql = sprintf("INSERT INTO %s
										SET
										version_id = '0',
										lang_id = '%d',
										question = '%s',
										answer = '%s',
										active = '%s',
										upload_count = '0',
										created = '%s',
										createdby = '%s',
										changedd = '%s',
										changedby = '%s'",
						$this->cms->tbname['papoo_faq_content_frontend'],
						$this->db->escape($result[0]['lang_id']),
						$this->db->escape($result[0]['question']),
						$this->db->escape($answer),
						'n',
						$this->db->escape($result[0]['created']),
						$this->db->escape($result[0]['createdby']),
						date('YmdHis'),
						$this->db->escape($this->user->username)
					);
					$this->db->query($sql);
					$faq_id = $this->db->insert_id;
					// Kategorie-Zuordnung holen
					$result = $this->getDataFromTable($this->cms->tbname['papoo_faq_cat_link_new_question_frontend'], $this->db->escape($this->checked->faq_id));
					// original Zuordnung als Version 0 speichern
					$sql = sprintf("INSERT INTO %s
										SET cat_id = '%d',
										faq_id = '%d',
										order_id = '%d',
										version_id = '0'",
						$this->cms->tbname['papoo_faq_cat_link_frontend'],
						$this->db->escape($result[0]['cat_id']),
						$this->db->escape($faq_id),
						$this->db->escape($result[0]['order_id'])
					);
					$this->db->query($sql);
					$catid = $this->saveFaqCatLink_sub($faq_id, $version_id = 1);
					// Alle Beziehungen zwischen cat-faq f�r diese FAQ l�schen
					$sql = sprintf("DELETE FROM %s WHERE faq_id='%s'",
						$this->cms->tbname['papoo_faq_cat_link_new_question_frontend'],
						$this->db->escape($this->checked->faq_id)
					);
					$this->db->query($sql);
					// FAQ Daten l�schen
					$sql = sprintf("DELETE FROM %s WHERE id='%s'",
						$this->cms->tbname['papoo_faq_new_question_frontend'],
						$this->db->escape($this->checked->faq_id)
					);
					$this->db->query($sql);
				}
			}
			elseif ($this->checked->src == "BE") {
				$this->faq_makeNewVersion();
				$this->saveFaqCatLink($this->checked->faq_id, $this->getMaxVersionId($this->checked->faq_id));
			}
			// raus aus dem Template & zur Hauptseite springen
			$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid .
				"&template=faq/templates/faq_front.html&faq_question_is_answered=1";
			if ( $_SESSION['debug_stopallredirect'] ) {
				echo '<a href="' . $location_url . '">Weiter</a>';
			}
			else {
				header( "Location: $location_url" );
			}
			exit;
		}
		if ($this->checked->src == "BE") {
			$table_content = $this->cms->tbname['papoo_faq_content'];
			$table_cat_link = $this->cms->tbname['papoo_faq_cat_link'];
		}
		elseif ($this->checked->src == "FE") {
			$table_content = $this->cms->tbname['papoo_faq_new_question_frontend'];
			$table_cat_link = $this->cms->tbname['papoo_faq_cat_link_new_question_frontend'];
		}
		$this->content->template['faq_data'][0] = $this->getDataFromTable($table_content);
		$this->get_timestamps_and_faq_data();
		$sql = sprintf("SELECT
							T1.cat_id,
							T2.catname,
							T2.catdescript					
							FROM %s T1
							INNER JOIN %s T2 ON (T1.cat_id = T2.id)
							WHERE T1.faq_id = '%d'",
			$table_cat_link,
			$this->cms->tbname['papoo_faq_categories'],
			$this->db->escape($this->checked->faq_id)
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->fetchAllCategories($result, "");
		$this->content->template['faq_catdata'] = $result;
		$this->content->template['faq_main_id'] = $this->checked->faq_main_id;
		$this->content->template['faq_id'] = $this->checked->faq_id;
		$this->content->template['src'] = $this->checked->src;
	}

	/**
	 * Daten zu einer id, lang_id, version_id aus vorgegebener Tabelle lesen (alle Felder)
	 * called by
	 *
	 * @param string $table
	 * @param string $id
	 * @param string $versionid
	 * @return array|void
	 */
	function getDataFromTable($table = "", $id = "", $versionid = "")
	{
		// Sprache ermitteln (BE / FE)
		$lang_id = defined("admin") ? $this->cms->lang_back_content_id : $this->cms->lang_id;
		// condition: Nur faq_id bei den Relationen FAQ / Kategorie, sonst id mit lang_id
		if ($id == "") {
			$where = sprintf(" WHERE id = '%d' AND lang_id = '%d' ",
				$this->db->escape($this->checked->faq_id),
				$this->db->escape($lang_id)
			);
		}
		else {
			$where = " WHERE faq_id = '" . $this->db->escape($id) . "' ";
		}
		if ($versionid != "") {
			$where .= " AND version_id = '" . $this->db->escape($versionid) . "' ";
		}
		$sql = sprintf("SELECT * FROM %s $where",
			$table
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * Lese- oder Schreibrechte pr�fen f�r die aktuelle userid (User muss aktiviert sein) (Frontend)
	 * $rw = 0: Leserechte pr�fen
	 * $rw = 1: Schreibrechte pr�fen
	 * called by post_papoo()
	 *
	 * @param int $rw
	 * @return boolean
	 */
	function check_read_write_access($rw = 0)
	{
		$rw = $rw ? $this->cms->tbname['papoo_faq_write_privileges'] : $this->cms->tbname['papoo_faq_read_privileges'];
		$sql = sprintf("SELECT T3.gruppeid
						FROM %s T1
  						INNER JOIN %s T2 ON (T1.userid = T2.userid)
						INNER JOIN %s T3 ON (T2.gruppenid = T3.gruppeid)
						WHERE T2.userid = '%d' AND T1.active = '1'",
			$this->cms->tbname['papoo_user'],
			$this->cms->tbname['papoo_lookup_ug'],
			$rw,
			$this->user->userid
		);
		return count($this->db->get_results($sql, ARRAY_A)) ? true : false;
	}

	/**
	 * Datum umwandeln �hnl. ISO 8601 (Backend, Frontend)
	 * called by edit_faq_main(), faq_front(), main_faq()
	 *
	 * @param string $date
	 * @return mixed
	 */
	function convertDate($date = "")
	{
		if (isset($this->content->template['faq_data']) && count($this->content->template['faq_data']) AND empty($date)) {
			foreach ($this->content->template['faq_data'] AS $key =>$value) {
				if (count($value)) {
					foreach ($value AS $key2 =>$value2) {
						if (isset($this->content->template['faq_data'][$key][$key2]['created']) && $this->content->template['faq_data'][$key][$key2]['created']) {
							$date = $this->content->template['faq_data'][$key][$key2]['created'];
							$trans = strtotime(substr($date, 0, 4) . '-' .
								substr($date, 4, 2) . '-' .
								substr($date, 6, 2) . ' ' .
								substr($date, 8, 2) . ':' .
								substr($date, 10, 2));
							$this->content->template['faq_data'][$key][$key2]['created'] = date('Y-m-d H:i', $trans);
						}
						if (isset($this->content->template['faq_data'][$key][$key2]['changedd']) && $this->content->template['faq_data'][$key][$key2]['changedd']) {
							$date = $this->content->template['faq_data'][$key][$key2]['changedd'];
							$trans = strtotime(substr($date, 0, 4) . '-' .
								substr($date, 4, 2) . '-' .
								substr($date, 6, 2) . ' ' .
								substr($date, 8, 2) . ':' .
								substr($date, 10, 2));
							$this->content->template['faq_data'][$key][$key2]['changedd'] = date('Y-m-d H:i', $trans);
						}
					}
				}
			}
			return $this->content->template['faq_data'][0];
		}
		else {
			$trans = strtotime(substr($date, 0, 4) . '-' .
				substr($date, 4, 2) . '-' .
				substr($date, 6, 2) . ' ' .
				substr($date, 8, 2) . ':' .
				substr($date, 10, 2));
			return date('Y-m-d H:i', $trans);
		}
	}

	/**
	 * Pr�fung auf numerisch (Backend, Frontend)
	 * called by del_category(), edit_category(), edit_select_category(), new_faq(), save_cat_order() TODO...more
	 *
	 * @param
	 * @return bool
	 */
	function checkNumeric($test, $key_check = 0)
	{
		$numeric = true; // Vorbelegen mit "Wert ist numerisch"
		if (is_array($test)) { // Check array, das rein numerische Werte enthalten sollte
			if (!count($test)) {
				$numeric = false;
			} // Leeres array als nicht numerisch behandeln
			else {
				foreach ($test AS $key =>$value) {
					if (!ctype_digit((string)$key)) {
						$numeric = false;
						break;
					}
					if (!$key_check) { // Nur die Keys pr�fen?
						if (!ctype_digit((string)$value)) { // Auch Werte pr�fen
							$numeric = false;
							break;
						}
					}
				}
			}
		}
		else {
			$numeric = false;
		} // Kein Array als nicht numerisch behandeln
		return $numeric;
	}

	/**
	 * Pr�fung auf leere Antwort (Backend, Frontend)
	 * Antwort gilt als leer, wenn diese nur nicht druckbare Zeichen enth�lt
	 * called by new_faq(), faq_edit(), faq_front_new_faq(), faq_front_edit()
	 *
	 * @param string $subject
	 * @param int $startvalue
	 * @param int $endvalue
	 * @param bool $tags
	 * @param bool $entities
	 * @return boolean true = empty
	 */
	function check_string_empty($subject="", $startvalue=0, $endvalue=0, $tags=false, $entities=false)
	{
		// UTF8 to ASCII
		$subject_decoded = utf8_decode($subject);

		// Umwandlung der HTML-Entities
		if ($entities) {
			$subject_decoded = $this->HTMLEntities_to_literals($subject_decoded);
		}

		// HTML-Tags raus
		if ($tags) {
			$subject_decoded = strip_tags($subject_decoded);
		}

		// Sind nur noch Leerstellen (32/160/$20/$A0) im Text �brig?
		$leerzeichen_1 = str_replace(chr(32), "", $subject_decoded);

		$leer = strlen($leerzeichen_1) ? false : true;
		// Test ohne Leerzeichen 32
		$leer = strlen(str_replace(chr(160), "", $leerzeichen_1)) ? false : true;
		if (!$leer) {
			// Sind nur Steuerzeichen im Text vorhanden?
			// Hierzu alle druckbaren Zeichen entfernen, Rest nicht druckbar
			$len_before = strlen($subject_decoded);
			if ($startvalue and $endvalue) {
				$i = $startvalue;
				for (;$i<=$endvalue;) {
					$subject_decoded = str_replace(chr($i), "", $subject_decoded);
					$i++;
				}
				$leer = ($len_before == strlen($subject_decoded)) ? true : false;
				//(strlen($subject_decoded)) ? $leer=1 : $leer=0;
			}
		}
		return $leer;
	}

	/**
	 * convert html entities to literals (Backend, Frontend)
	 *
	 * called by check_string_empty()
	 *
	 * @param
	 * @return string
	 */
	function HTMLEntities_to_literals($umw_inhalt)
	{
		$search = array(
			"'&(quot|#34);'i",
			"'&(amp|#38);'i",
			"'&(lt|#60);'i",
			"'&(gt|#62);'i",
			"'&(nbsp|#160);'i",   "'&(iexcl|#161);'i",  "'&(cent|#162);'i",   "'&(pound|#163);'i",  "'&(curren|#164);'i",
			"'&(yen|#165);'i",    "'&(brvbar|#166);'i", "'&(sect|#167);'i",   "'&(uml|#168);'i",    "'&(copy|#169);'i",
			"'&(ordf|#170);'i",   "'&(laquo|#171);'i",  "'&(not|#172);'i",    "'&(shy|#173);'i",    "'&(reg|#174);'i",
			"'&(macr|#175);'i",   "'&(neg|#176);'i",    "'&(plusmn|#177);'i", "'&(sup2|#178);'i",   "'&(sup3|#179);'i",
			"'&(acute|#180);'i",  "'&(micro|#181);'i",  "'&(para|#182);'i",   "'&(middot|#183);'i", "'&(cedil|#184);'i",
			"'&(supl|#185);'i",   "'&(ordm|#186);'i",   "'&(raquo|#187);'i",  "'&(frac14|#188);'i", "'&(frac12|#189);'i",
			"'&(frac34|#190);'i", "'&(iquest|#191);'i", "'&(Agrave|#192);'",  "'&(Aacute|#193);'",  "'&(Acirc|#194);'",
			"'&(Atilde|#195);'",  "'&(Auml|#196);'",    "'&(Aring|#197);'",   "'&(AElig|#198);'",   "'&(Ccedil|#199);'",
			"'&(Egrave|#200);'",  "'&(Eacute|#201);'",  "'&(Ecirc|#202);'",   "'&(Euml|#203);'",    "'&(Igrave|#204);'",
			"'&(Iacute|#205);'",  "'&(Icirc|#206);'",   "'&(Iuml|#207);'",    "'&(ETH|#208);'",     "'&(Ntilde|#209);'",
			"'&(Ograve|#210);'",  "'&(Oacute|#211);'",  "'&(Ocirc|#212);'",   "'&(Otilde|#213);'",  "'&(Ouml|#214);'",
			"'&(times|#215);'i",  "'&(Oslash|#216);'",  "'&(Ugrave|#217);'",  "'&(Uacute|#218);'",  "'&(Ucirc|#219);'",
			"'&(Uuml|#220);'",    "'&(Yacute|#221);'",  "'&(THORN|#222);'",   "'&(szlig|#223);'",   "'&(agrave|#224);'",
			"'&(aacute|#225);'",  "'&(acirc|#226);'",   "'&(atilde|#227);'",  "'&(auml|#228);'",    "'&(aring|#229);'",
			"'&(aelig|#230);'",   "'&(ccedil|#231);'",  "'&(egrave|#232);'",  "'&(eacute|#233);'",  "'&(ecirc|#234);'",
			"'&(euml|#235);'",    "'&(igrave|#236);'",  "'&(iacute|#237);'",  "'&(icirc|#238);'",   "'&(iuml|#239);'",
			"'&(eth|#240);'",     "'&(ntilde|#241);'",  "'&(ograve|#242);'",  "'&(oacute|#243);'",  "'&(ocirc|#244);'",
			"'&(otilde|#245);'",  "'&(ouml|#246);'",    "'&(divide|#247);'i", "'&(oslash|#248);'",  "'&(ugrave|#249);'",
			"'&(uacute|#250);'",  "'&(ucirc|#251);'",   "'&(uuml|#252);'",    "'&(yacute|#253);'",  "'&(thorn|#254);'",
			"'&(yuml|#255);'");

		$replace = array(
			"\"",
			"&",
			"<",
			">",
			" ",      chr(161), chr(162), chr(163), chr(164), chr(165), chr(166), chr(167), chr(168), chr(169),
			chr(170), chr(171), chr(172), chr(173), chr(174), chr(175), chr(176), chr(177), chr(178), chr(179),
			chr(180), chr(181), chr(182), chr(183), chr(184), chr(185), chr(186), chr(187), chr(188), chr(189),
			chr(190), chr(191), chr(192), chr(193), chr(194), chr(195), chr(196), chr(197), chr(198), chr(199),
			chr(200), chr(201), chr(202), chr(203), chr(204), chr(205), chr(206), chr(207), chr(208), chr(209),
			chr(210), chr(211), chr(212), chr(213), chr(214), chr(215), chr(216), chr(217), chr(218), chr(219),
			chr(220), chr(221), chr(222), chr(223), chr(224), chr(225), chr(226), chr(227), chr(228), chr(229),
			chr(230), chr(231), chr(232), chr(233), chr(234), chr(235), chr(236), chr(237), chr(238), chr(239),
			chr(240), chr(241), chr(242), chr(243), chr(244), chr(245), chr(246), chr(247), chr(248), chr(249),
			chr(250), chr(251), chr(252), chr(253), chr(254), chr(255));
		return $umw_inhalt = preg_replace($search, $replace, $umw_inhalt);
	}

	function sendMail()
	{
		//$this->mail_it->to = "khmweb-netz@t-online.de";
		$this->mail_it->to = $_SESSION['faq']['FAQ_ADMINMAIL'];
		$this->mail_it->from = $_SESSION['faq']['FAQ_ADMINMAIL'];
		$this->mail_it->from_text = $this->checked->submit === $this->content->template['plugin']['faq_back']['submit']['from_txt'];
		$this->mail_it->subject = $this->checked->submit === $this->content->template['plugin']['faq_back']['submit']['subject'];
		$this->mail_it->body = $this->checked->submit === $this->content->template['plugin']['faq_back']['submit']['body'];;
		//$this->mail_it->body_html = $mail_dat['0']['mail__nhalt_der_ail_'];
		$this->mail_it->priority = 5;
		$this->mail_it->do_mail();
	}

	/**
	 * automatische Sprachumschaltung aufgrund der Browser-Einstellung (Frontend)
	 *
	 * called by post_papoo()
	 *
	 * @param
	 * @return void
	 */
	function autodetect_language()
	{
		// Hole die aktiven Sprachen
		$sql = sprintf("SELECT lang_short
								FROM %s
								WHERE more_lang ='2'",
			$this->cms->tbname['papoo_name_language']
		);
		$papoo_languages = $this->db->get_results($sql, ARRAY_A); // aktive Sprachen
		// keys = Werte setzen
		foreach ($papoo_languages as $langCode =>$value) {
			$papoo_languages2[$value['lang_short']] = $value['lang_short'];
		}
		$langs = array ();
		// Browserdaten z. B. in der Form de,de-DE;q=0.9,en;0.8
		$browserLangs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); // Browser-Spracheinstellungen
		// Key: Sprache, Wert: quality -> $accepted_langs
		foreach($browserLangs as $lang_and_quality) {
			@list($langCode, $quality) = explode(';', $lang_and_quality);
			$accepted_langs[$langCode] = $quality ? (float)substr($quality, 2) : (float)1;
		}
		// Sort DESC Browser-Sprachen nach quality und Array anlegen, das nur die Sprachen enth�lt (selbe Reihenfolge)
		if (isset($accepted_langs) && is_array($accepted_langs)) {
			arsort($accepted_langs); // Reihenfolge der Werte umkehren
			$langCodes = array_keys($accepted_langs); // Keys -> Werte
			if (is_array($langCodes)) {
				foreach ($langCodes as $langCode) { // Keys = Werte setzen
					$langs[$langCode] = $langCode;
				}
			}
		}
		$lang_result = FALSE;
		$config_lang = $this->read_config(true);
		if ($this->checked->getlang) {
			$lang_result = $this->checked->getlang;
		}
		elseif ($config_lang['autodetect_lang'] == "n") {
			$lang_result = $this->cms->lang_short;
		} // Wenn Autodetect off (Konfig)
		else { // Autodetect ist eingeschaltet
			// Papoo-Sprachen durchloopen (f�r z. B. de)
			for($i = 0; $i < count($langs); $i++) {
				$current_lang = array_values($langs);
				$current_lang = $current_lang[$i];
				if(isset($papoo_languages2[$current_lang])) {
					$lang_result = $papoo_languages2[$current_lang];
					break;
				}
			}
			// f�r z. B. de-DE (Safari, Opera...)
			if(strlen($current_lang) > 2 AND $lang_result === FALSE) {
				$current_lang_short = substr($current_lang, 0, 2);
				if (isset($papoo_languages2[$current_lang_short])) {
					$lang_result = $papoo_languages2[$current_lang_short];
				}
			}
		}
		if ($lang_result) { // Sprache im Frontend umschalten
			$sprache = $this->cms->lang_get($lang_result);
			$this->cms->lang_save("FRONT", $sprache);
		}
	}

	/**
	 * Eingaben bereinigen (ausser arrays und Antwort mit HTML)
	 *
	 * called by
	 *
	 * @param
	 * @return void
	 */
	function sanitize_inputs()
	{
		if(isset($this->checked)) {
			$checked = get_object_vars($this->checked);
			foreach($checked as $key =>$value) {
				if (!is_array($checked[$key]) AND $key != "faq_answer") {
					$this->checked->$key = trim($this->inputfilter->process($value));
				}
			}
		}
	}
}

$faq = new faq();
