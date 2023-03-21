<?php

/**
 * Class leadtracker_download_class_form_now
 */
#[AllowDynamicProperties]
class leadtracker_download_class_form_now
{
	/**
	 * leadtracker_download_class_form_now constructor.
	 */
	function __construct()
	{
		global $db, $db_abs, $cms, $user, $checked, $content, $download;
		$this->db = & $db;
		$this->db_abs = & $db_abs;
		$this->cms = & $cms;
		$this->user = & $user;
		$this->checked = & $checked;
		$this->content = & $content;
		$this->download=$download;
	}

	/**
	 * @return bool|void
	 */
	function do_lead_tracker_download()
	{
		//OK - hier jetzt checken ob es sich um eine Downloaddatei handelt die per Formular gesperrt ist..
		$result =$this->get_verknuepfungen($this->checked->downloadid);

		//Wenn ja, dann Formular ausgeben.
		if (count($result)>=1) {
			//Daten rausholen
			$this->get_formular_aus_plugin($result['0']['leadtracker_verknpfen_mit']);

			//MOdul setten wg. Spamschutz der da unterdrückt wird
			if ($this->content->template['module_aktiv']['form_modul']!=1) {
				$formmodulnotaktiv="ok";
				$this->content->template['module_aktiv']['form_modul']=1;
			}

			//Formular und Danke Seite laden (Spamschutz noch deaktivieren...)
			global $template;
			$template=PAPOO_ABS_PFAD."/plugins/leadtracker/templates/form.html";

			//Seite wurde neu geladen und JS ist angestoßen worden...
			if ($this->checked->fertig==1 && $this->checked->download_now=="ok" && $_SESSION['darf_downloaden']=="ok") {
				//Nach derm ersten Download auf 0 setzen damit man nicht unbegrenzt runterladen kann...
				$_SESSION['darf_downloaden']="";

				//Download sofort anstoßen
				$this->download->download_sofort=true;
				$this->download->download_file((int) $this->checked->downloadid);
			}

			//noch checken ob es wirklich eine KANN Datei ist
			$data=$this->get_verknuepfungen((int) $this->checked->downloadid);

			if ($data['0']['leadtracker_kann_muss']!=1) {
				$muss=true;
			}
			else {
				$this->content->template['leadtracker_is_kann_datei']="ok";
			}

			if (!empty($this->checked->form_manager_submit_dl_now)) {
				//Es ist keine KANN Datei
				if (isset($muss) && $muss) {
					$load_file="<p class=\"error\">Die Datei darf nicht heruntergeladen werden.</p>";
					$_SESSION['darf_downloaden']="";
				}
				else {
					$location_href = PAPOO_WEB_PFAD."/index.php?menuid=".$this->checked->menuid."&reporeid=".$this->checked->reporeid."&downloadid=".$this->checked->downloadid."&download_now_direct=ok";
					$load_file='<script type="text/javascript">

          $(document).ready(function(){

                window.location.href = \''.$location_href.'\';
});
                </script>';
					$_SESSION['darf_downloaden']="ok";
				}

				$this->content->template['form_html']= "nobr: ". $this->content->template['download_Startet'].$load_file;
			}

			if ($this->checked->download_now_direct=="ok" && $_SESSION['darf_downloaden']=="ok") {
				//Nach derm ersten Download auf 0 setzen damit man nicht unbegrenzt runterladen kann...
				$_SESSION['darf_downloaden']="";

				//Es ist keine KANN Datei
				if (isset($muss) && $muss) {
					return false;
				}

				//Download sofort anstoßen
				$this->download->download_sofort=true;
				$this->download->download_file((int) $this->checked->downloadid);
			}

			//Modul unsetten
			if (isset($formmodulnotaktiv) && $formmodulnotaktiv=="ok") {
				$this->content->template['module_aktiv']['form_modul']="";
			}
		}

		//Check kann / muss

		//Wenn kann, dann Download starten Button ausgehen
	}

	/**
	 * @param int $id
	 * @return array|string|null
	 */
	private function get_verknuepfungen($id=0)
	{

		$xsql=array();
		$xsql['dbname']         = "plugin_leadtracker";
		$xsql['select_felder']  = array("*");
		$xsql['limit']          = "";
		$xsql['where_data']     = array("leadtracker_die_downloaddatei" => $id);
		$result = $this->db_abs->select( $xsql );

		return $result;
	}

	/**
	 * @param int $formular_id
	 * @param int $empfanger_flex_id
	 */
	function get_formular_aus_plugin($formular_id = 0, $empfanger_flex_id = 0)
	{
		//Download Klasse einbinden
		// require_once(PAPOO_ABS_PFAD."/plugins/form_manager/lib/form_manager.php");


		// Heutiges Datum
		global $form_manager;
		//$form_manager = new form_manager();

		$this->checked->form_manager_id = trim($formular_id);

		if ($this->checked->fertig == 1) {
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
					if ($_SESSION['darf_downloaden']=="ok") {
						$location_href = PAPOO_WEB_PFAD."/index.php?menuid=".$this->checked->menuid."&fertig=1&reporeid=".$this->checked->reporeid."&downloadid=".$this->checked->downloadid."&download_now=ok";
						$load_file='<script type="text/javascript">

          $(document).ready(function(){

                window.location.href = \''.$location_href.'\';
});
                </script>';
					}
					else {
						$load_file='<p class="error">Download nur 1x möglich</p>';
					}

					//$this->content->template['form_html'] = "nodecode:" . $spalte->form_manager_antwort_html;
					$temp_form_manager_antwort_html ="nobr: ". $spalte->form_manager_antwort_html.$load_file;
					#$temp_form_manager_antwort_html = $this->diverse->do_pfadeanpassen("nobr:".$temp_form_manager_antwort_html);
					# $temp_form_manager_antwort_html = $this->download->replace_downloadlinks($temp_form_manager_antwort_html);
					$this->content->template['form_html'] = $temp_form_manager_antwort_html;
				}
			}
			$this->content->template['message1'] = "ok";
		}
		else {
			// Wenn verschickt wurde
			if (!empty ($this->checked->form_manager_submit)) {
				$inhalt= "";
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
						$inhalt .= $form_manager->html2txt($item);
					}
					else
						$inhalt .= $form_manager->html2txt($value);
					$inhalt .= "\n";
				}

				$form_manager->make_form_check();
				global $spamschutz;
				$spamschutz->is_spam=false;

				if (!empty($this->checked->nicht_ausfuellen)) {
					$spamschutz->is_spam=true;
				}

				if ($this->cms->stamm_kontakt_spamschutz && $spamschutz->is_spam) {
					echo "Ist Spam <br />";
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

							$_SESSION['darf_downloaden']="ok";
						}
						// Statistik
						//$this->make_stat($_POST['paket_id']);
						// Neu laden mit Message
						//$location_url = $_SERVER['PHP_SELF'] . "?menuid=" . $this->checked->menuid . "&fertig=1&template=" . $this->checked->template . "&form_manager_id=" . $this->checked->form_manager_id. "&flexid=".$this->checked->flexid."&flex_mv_id=".$this->checked->flex_mv_id."&style=".$this->checked->style;


						$location_url = PAPOO_WEB_PFAD."/index.php?menuid=".$this->checked->menuid."&fertig=1&reporeid=".$this->checked->reporeid."&downloadid=".$this->checked->downloadid."";
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
		#global $smarty;

		#$smarty->template_dir = PAPOO_ABS_PFAD. "/plugins/form_manager/templates/";
		# $this->content->assign();
		#$output = $smarty->fetch("form_modul_cm.html");
	}
}
