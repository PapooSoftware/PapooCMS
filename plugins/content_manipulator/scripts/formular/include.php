<?php

/**
 * Class formularintegration
 */
class formularintegration
{
	/**
	 * formularintegration constructor.
	 */
	function __construct()
	{
		global $content, $checked, $cms, $db;
		$this->content = &$content;
		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->db = &$db;

		//Admin Ausgab erstelle
		$this->set_backend_message();
		//Frontend - dann Skript durchlaufen
		if (!defined("admin")) {
			//Fertige Seite einbinden
			global $output;
			//Zuerst check ob es auch vorkommt
			if (strstr($output, "#form")) {
				//Ausgabe erstellen
				$output = $this->create_formularintegration($output);
			}
		}
	}

	/**
	 * @param string $name
	 * @return bool|string Der Dateiname des Templates, wenn es im Style- oder Pluginverzeichnis gefunden wird, ansonsten false
	 * @author Christoph Zimmer
	 */
	private static function findTemplate($name) {
		$filename = "plugins/form_manager/templates/$name";

		return array_reduce(array(
			PAPOO_ABS_PFAD."/styles/{$GLOBALS["cms"]->style_dir}/templates/$filename",
			PAPOO_ABS_PFAD."/$filename"
		), function ($template, $filename) {
			return $template !== false ? $template : (is_file($filename) ? $filename : $template);
		}, false);
	}

	/**
	 * formularintegration::set_backend_message()
	 *
	 * @return void
	 */
	function set_backend_message()
	{
		$this->content->template['plugin_cm_head']['de'][] = "Skript formular an beliebiger Stelle";
		$this->content->template['plugin_cm_body']['de'][] = "Mit diesem kleinen Skript kann man an beliebiger Stelle in Inhalten ein bestimmtes Formular aus dem Papoo Formularmanager ausgeben lassen, die Syntax lautet.<br /><strong>#form_1#</strong><br />Wobei Sie mit der Ziffer am Ende die ID des Formulars bezeichnen. ";
		$this->content->template['plugin_cm_img']['de'][] = '';
	}

	/**
	 * @param string $inhalt
	 *
	 * @return mixed|string|string[]|null
	 */
	function create_formularintegration($inhalt = "")
	{
		// Ids rausholen
		preg_match_all("|#form(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($ausgabe['1'] as $dat) {
			$ndat = explode("_", $dat);
			IfNotSetNull($ndat['1']);
			IfNotSetNull($ndat['2']);
			$formular_daten = $this->get_formular_aus_plugin($ndat['1'],$ndat['2']);
			$inhalt = preg_replace('~<p>\s*'.preg_quote($ausgabe[0][$i], "~").'\s*</p>~', $formular_daten, $inhalt);
			$inhalt = str_ireplace($ausgabe['0'][$i], $formular_daten, $inhalt);
			$i++;
		}
		$inhalt = "" . $inhalt;
		return $inhalt;
	}

	/**
	 * @param int $formular_id
	 * @param int $empfanger_flex_id
	 * @return mixed
	 */
	function get_formular_aus_plugin($formular_id = 0, $empfanger_flex_id = 0)
	{
		// Heutiges Datum
		global $form_manager;

		$this->checked->form_manager_id = trim($formular_id);

		if (isset($this->checked->fertig) && $this->checked->fertig == 1 && $this->checked->template!="form_manager/templates/form.html") {
			$sql = sprintf("SELECT * FROM %s
					WHERE form_manager_id_id='%s'
					AND form_manager_lang_id='%s'",
				$this->cms->tbname['papoo_form_manager_lang'],
				$this->db->escape($this->checked->form_manager_id),
				$this->db->escape($this->cms->lang_id)
			);
			$result = $this->db->get_results($sql);
			$save_mv_content_id = $this->checked->mv_content_id;
			$save_mv_id = $this->checked->mv_id;
			# $this->checked->mv_content_id=$this->checked->flexid;
			# $this->checked->mv_id=$this->checked->flex_mv_id;
			$form_manager->form_bind_mv();
			$form_manager->form_show_search_mv();
			#$this->checked->mv_content_id = $save_mv_content_id;
			# $this->checked->mv_id = $save_mv_id;
			if (!empty ($result)) {
				foreach ($result as $spalte) {
					if (is_array($_SESSION['formdat'])) {
						foreach ($_SESSION['formdat'] as $key=>$value) {
							$key=$form_manager->html2txt($key);
							$value=$form_manager->html2txt($value);
							if (is_string($value)) {
								$spalte->form_manager_antwort_html= preg_replace('/#'.$key.'#/', $value, $spalte->form_manager_antwort_html);
							}
						}
					}
					$_SESSION['formdat']=array();
					if (is_array($this->mv_daten_array)) {
						foreach ($this->mv_daten_array as $key=>$value) {
							$key=str_ireplace("/","",$key); $key=str_ireplace(">","",$key);
							$spalte->form_manager_antwort_html= preg_replace('/#flex_'.$key.'#/', $value, $spalte->form_manager_antwort_html);
						}
					}

					//$this->content->template['form_html'] = "nodecode:" . $spalte->form_manager_antwort_html;
					$temp_form_manager_antwort_html = $spalte->form_manager_antwort_html;
					$temp_form_manager_antwort_html = $this->diverse->do_pfadeanpassen("nobr:".$temp_form_manager_antwort_html);
					$temp_form_manager_antwort_html = $this->download->replace_downloadlinks($temp_form_manager_antwort_html);
					$this->content->template['form_html'] = $temp_form_manager_antwort_html;
				}
			}
			$this->content->template['message1'] = "ok";
		}
		else {
			// Wenn verschickt wurde
			if (!empty ($this->checked->form_manager_submit)) {
				$inhalt = "";
				// Daten aus POST rausholen und in ein string einlesen
				foreach ($_POST as $key => $value) {
					$inhalt .= $key . ": ";
					if (is_array($value)) {
						$first = true;
						foreach ($value as $item)
							if (!$first) {
								$inhalt .= '; ';
							}
						$first = false;
						$inhalt .= $form_manager->html2txt($item);
					}
					else {
						$inhalt .= $form_manager->html2txt($value);
					}
					$inhalt .= "\n";
				}
				$form_manager->make_form_check();
				global $spamschutz;

				if ($this->content->template['module_aktiv']['form_modul']==1) {
					$spamschutz->is_spam=false;
				}

				if ($this->cms->stamm_kontakt_spamschutz && $spamschutz->is_spam) {
					$form_manager->error["__spamschutz__"] = "error";
				}
				else {
					// Daten verschicken
					if ($form_manager->insert_ok == true) {
						//Wenn ok dann senden,
						if ($form_manager->blok=="ok") {
							$mailad="";
							//hier noch die Flexmail Einträge...
							if (!empty($this->checked->mv_content_id)) {
								//Felder rausholen
								$sql=sprintf("SELECT * FROM %s WHERE
                                                mvcform_type='email'
                                                AND mvcform_form_id='%d'
                                                ",
									DB_PRAEFIX."papoo_mvcform",
									$this->db->escape($this->checked->mv_id)
								);
								$mailad_res=$this->db->get_results($sql,ARRAY_A);

								if (is_array($mailad_res)) {
									foreach ($mailad_res as $ka=>$va) {
										$sql=sprintf("SELECT %s FROM %s WHERE mv_content_id='%d' ",
											$va['mvcform_name']."_".$va['mvcform_id'],
											DB_PRAEFIX."papoo_mv_content_".$this->checked->mv_id
											."_search_1",
											$this->db->escape($this->checked->mv_content_id)
										);
										$mailad.=";".$this->db->get_var($sql);
									}
								}
							}
							$form_manager->verschick($inhalt,$mailad);
						}
						// Statistik
						//$this->make_stat($_POST['paket_id']);
						// Neu laden mit Message
						//$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&fertig=1&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id. "&flexid=".$this->checked->flexid."&flex_mv_id=".$this->checked->flex_mv_id."&style=".$this->checked->style;
						if (empty($this->checked->template)) {
							$this->checked->template="form_manager/templates/form.html";
						}
						$location_url = PAPOO_WEB_PFAD."/plugin.php?menuid=".$this->checked->menuid."&fertig=1&template=form_manager/templates/form.html&form_manager_id=".$this->checked->form_manager_id."&flexid=".$this->checked->flexid."&flex_mv_id=".$this->checked->flex_mv_id."&style=".$this->checked->style."&getlang=".$this->cms->lang_short;
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

		// Formular raussuchen und anzeigen
		if ((is_numeric($this->checked->form_manager_id))) {
			$this->content->template['formok'] = "ok";
			$this->content->template['form_manager_id'] = $this->checked->form_manager_id;
			// FOrmular raussuchen und anzeigen
			$form_manager->front_get_form($this->checked->form_manager_id);
		}
		$this->content->template['is_cm_formok'] = "ok";
		$this->content->assign();

		$output = $GLOBALS["smarty"]->fetch(self::findTemplate("form_modul_cm.html"));
		return $output;
	}
}

$formularintegration = new formularintegration();
