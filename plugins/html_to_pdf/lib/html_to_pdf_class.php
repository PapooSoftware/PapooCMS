<?php

/**
 * Class html_to_pdf
 */
class html_to_pdf {
	/**
	 * html_to_pdf constructor.
	 */
	function __construct()
	{
		global $cms, $db, $user, $content, $checked;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->user = & $user;
		$this->content = & $content;
		$this->checked = & $checked;

		$this->do_action();
	}

	/**
	 * html_to_pdf::do_action()
	 *
	 * @return void
	 */
	function do_action()
	{
		global $template;

		if (strpos('XXX'.$template, 'html_to_pdf_back.html')) {
			//Nicht vergessen  - intern immer checken ob der Zugriff erlaubt ist
			if ($this->user->check_intern()) {
				//Intern
				$this->make_config();
			}
		}
		else {
			//{if $menuid_aktuell==1}index.php?{else}{$slash}{$aktue_pdf_uri}&{/if}html2pdf_sumbit=1
			$this->content->template['aktue_pdf_uri']=$this->make_pdf_uri();
		}
	}

	/**
	 * @return string
	 */
	function make_pdf_uri()
	{
		///plugin.php?menuid=2&template=mv/templates/mv_show_front.html&mv_id=1&extern_meta=x&mv_content_id=13
		if ($this->cms->mod_free==1) {
			if (!stristr($_SERVER['REQUEST_URI'],".html") || stristr($_SERVER['REQUEST_URI'],"templates")) {
				if (stristr($_SERVER['REQUEST_URI'],".php")) {
					if (!stristr($_SERVER['REQUEST_URI'],"?")) {
						$url=$_SERVER['REQUEST_URI']."?html2pdf_sumbit=1";
					}
					else {
						$url=$_SERVER['REQUEST_URI']."&html2pdf_sumbit=1";
					}
				}
				else {
					$url=$_SERVER['REQUEST_URI']."?html2pdf_sumbit=1";
				}
			}
			else {
				$url=$_SERVER['REQUEST_URI']."?html2pdf_sumbit=1";
			}
		}
		else {
			//sprechende urls aktiv
			if ($this->cms->mod_surls==1) {
				if (!stristr($_SERVER['REQUEST_URI'],".html")) {
					if (stristr($_SERVER['REQUEST_URI'],".php")) {
						if (!stristr($_SERVER['REQUEST_URI'],"?")) {
							$url=$_SERVER['REQUEST_URI']."?html2pdf_sumbit=1";
						}
						else {
							$url=$_SERVER['REQUEST_URI']."&html2pdf_sumbit=1";
						}
					}
					else {
						$url=$_SERVER['REQUEST_URI']."index.html?html2pdf_sumbit=1";
					}
				}
				else {
					$url=$_SERVER['REQUEST_URI']."&html2pdf_sumbit=1";
				}
			}
			//keine sprechenden urls
			else {
				if (!stristr($_SERVER['REQUEST_URI'],".php")) {
					$url="index.php?html2pdf_sumbit=1";
				}
				else {
					//Gibts schon eine Variable oder nicht?
					if (!stristr($_SERVER['REQUEST_URI'],"?")) {
						$url=$_SERVER['REQUEST_URI']."?html2pdf_sumbit=1";
					}
					else {
						$url=$_SERVER['REQUEST_URI']."&html2pdf_sumbit=1";
					}
				}
			}
		}
		return $url;
	}

	/**
	 * html_to_pdf::make_config()
	 *
	 * @return void
	 */
	function make_config()
	{
		if (!empty($this->checked->formSubmit_pdf_settings)) {
			// Zielverzeichniss
			$destination_dir = '/dokumente/upload';
			// nicht erlaubte Dateiendungen
			$extensions = array ( 'php', 'html', 'cgi', 'pl', 'js');
			// Upload durchf�hren
			$upload_do = new file_upload(PAPOO_ABS_PFAD);
			// Wenn Files hochgeladen wurden
			if (!empty($_FILES['ext_search_pdf_template_file']['name'])) {
				if ($_FILES['ext_search_pdf_template_file']['type']!="application/pdf") {
					$upload_do->error="Missing parameter type";
					$falsch = 1;
				}
				else {
					// Durchf�hren und falls etwas schief geht
					if (!$upload_do->upload($_FILES['ext_search_pdf_template_file'], $destination_dir, 0, $extensions, 1)) {
						// falsch setzen
						$falsch = 1;
						// wenn etwas passiert ist, und ein Error vorliegt
						if (!empty ($upload_do->error)) {
							$falsch_exists = 1;
						}
					}
					else {
						$falsch = 0;
					}
				}

				// wenn der Upload geklappt hat, Daten eintragen.
				if ($falsch != 1) {
					$filename = $upload_do->file['name'];
					$filesize = $upload_do->file['size'];
					$download = "/dokumente/upload/".$filename;

					$sql = sprintf("UPDATE %s SET
												ext_search_pdf_template_file='%s', 
												ext_search_css_daten='%s'
												WHERE ext_search_id='1'",
						$this->cms->tbname['plugin_html2pdf'],

						$this->db->escape($filename),
						$this->db->escape($this->checked->ext_search_css_daten)
					);
					$this->db->query($sql);
					$this->content->template['message_upload'] =$this->content->template['plugin_html2pdf_die_daten_wurden_gespeichert_html2pdf'];
				}
				// Fehler beim Hochladen..  {$}
				else {
					// Meldung zeigen.. Missing parameter type
					if ($upload_do->error=="Missing parameter type") {
						$upload_do->error=$this->content->template['plugin_html2pdf_fehler_diese_datei_ist_keine_pdf_datei'];
					}
					$this->content->template['error_upload'] =$upload_do->error."";
				}
			}
			else {
				$sql = sprintf("UPDATE %s SET
												ext_search_css_daten='%s'
												WHERE ext_search_id='1'",
					$this->cms->tbname['plugin_html2pdf'],
					$this->db->escape($this->checked->ext_search_css_daten)
				);
				$this->db->query($sql);
				$this->content->template['message_upload'] =$this->content->template['plugin_html2pdf_die_daten_wurden_gespeichert_html2pdf'];
			}
		}
		$this->content->template['ext_search']=$this->get_settings();
		$this->content->template['ext_search']['0']['ext_search_css_daten']="nobr:".$this->content->template['ext_search']['0']['ext_search_css_daten'];
	}

	/**
	 * html_to_pdf::get_settings()
	 *
	 * @return array|void
	 */
	function get_settings()
	{
		//Alle Daten rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_html2pdf']
		);
		return $this->db->get_results($sql,ARRAY_A);
	}

	/**
	 * html_to_pdf::output_filter()
	 *
	 * @return void
	 */
	function output_filter()
	{
		global $output;

		if (strstr($_SERVER['REQUEST_URI'],"html2pdf_sumbit=1")) {
			$find = '/<!-- ### start_of_content -->.*<!-- ### end_of_content -->/s';
			preg_match($find, $output, $treffer, PREG_OFFSET_CAPTURE);
			$html = $treffer['0']['0'];
			$html=$this->replace_some_modules($html);
			$html=$this->create_html($html);
			$this->create_pdf_now($html);
		}
		//Wenn aktiv das normale Modul rauslutschen
		$output = preg_replace('/<div class="modul" id="mod_artikel_optionen">(.*?)<\/div>/s', '', $output);
	}

	/**
	 * html_to_pdf::create_html()
	 *
	 * @param mixed $html
	 * @return mixed|string
	 */
	function create_html($html)
	{
		$this->settings=$this->get_settings();

		$html=str_replace('"/images','"http://'.$this->cms->title_send.'/images',$html);

		$style='<style type="text/css">
						'.$this->settings['0']['ext_search_css_daten'].'
						</style>';
		$html='<html><head>'.$style.'</head><body><div class="pdf_content">'.$html.'</div></body></html>';

		return $html;
	}

	/**
	 * html_to_pdf::create_pdf_now()
	 *
	 * @param mixed $html
	 * @param integer $out
	 * @return void
	 */
	function create_pdf_now($html,$out=0)
	{
		//Daten direkt ausgeben.
		if ($out==0) {
			$file=PAPOO_ABS_PFAD."/dokumente/upload/".$this->settings['0']['ext_search_pdf_template_file'];
			$this->settings['0']['ext_search_pdf_template_file']=trim($this->settings['0']['ext_search_pdf_template_file']);

			//Damit diverse deprecated Meldungen nicht kommen.
			ini_set("display_errors","1");

			// Standard Font Directory von Mpdf
			$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
			$fontDirs = $defaultConfig['fontDir'];

			// Standard Fonts von Mpdf
			$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
			$fontData = $defaultFontConfig['fontdata'];

			// Extra Fonts für Mpdf hinzufügen
			$fonts = [
				'fontDir' => array_merge($fontDirs, [ // fontDir array zum setzen von Font Directories (Standard Directory wird behalten)
					PAPOO_ABS_PFAD . '/lib/fonts/mpdf_ttfonts_extra/standard', // Directory für extra Fonts
				]),
				'fontdata' => $fontData + [ // fontdata array zum inizialisieren von Fonts (Standard Fonts werden behalten)
						'norasi' => [ // Fontname der in der font-family genommen wird
							'R' => 'Norasi.ttf', // R: Normal
							'B' => 'Norasi-Bold.ttf', // B: Bold
							'I' => 'Norasi-Oblique.ttf', // I: Italic/Oblique
							'BI' => 'Norasi-BoldOblique.ttf', // BI: Bold Italic/Oblique
						],
						'fontawesome' => [
							'R' => 'fontawesome-webfont.ttf',
						]
				],
			];

			if (file_exists($file) && !empty($this->settings['0']['ext_search_pdf_template_file'])) {
				// Config für die PDF Ausgabe
				$config = [
					'tempDir' => rtrim(PAPOO_ABS_PFAD, '/').'/cache/mpdf',
					"mode" => "utf-8",
					"format" => "A4",
					"ignore_invalid_utf8" => true,
					// Fonts verfügbar machen
					$fonts,
				];

				// Mpdf init
				$mpdf = new \Mpdf\Mpdf($config);

				$mpdf->setSourceFile($file);

				$tplId = $mpdf->ImportPage(1);
				$mpdf->UseTemplate($tplId);

				#$mpdf->SetPageTemplate($tplId); 
				$mpdf->WriteHTML($html);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->Output(PAPOO_ABS_PFAD."/dokumente/upload/vorschau.pdf","I");
			}
			else {
				// Config für die PDF Ausgabe
				$config = [
					'tempDir' => rtrim(PAPOO_ABS_PFAD, '/').'/cache/mpdf',
					"mode" => "utf-8",
					"format" => "A4",
					"ignore_invalid_utf8" => true,
					// Fonts verfügbar machen
					$fonts,
				];

				// Mpdf init
				$mpdf = new \Mpdf\Mpdf($config);

				$mpdf->WriteHTML($html);
				$mpdf->SetDisplayMode('fullpage');
				$mpdf->Output("vorschau.pdf","I");
			}
		}
		exit;
	}

	/**
	 * L�scht den Code bestimmter Module aus dem darzustellenden Bereich
	 *
	 * @param $html
	 * @return mixed|string|string[]|null
	 */
	function replace_some_modules($html)
	{

		$html=str_replace("  "," ",$html);
		$html=str_replace("<br />","\n",$html);
		//<!-- ENDE Produkt Einzelansicht -->
		$html = preg_replace('/<!-- ENDE Produkt Einzelansicht -->(.*?)<!-- ### end_of_content -->/s', '', $html);
		$html = preg_replace('/<!-- ENDE TEMPLATE: index.html -->(.*?)<!-- ### end_of_content -->/s', '', $html);
		return $html;
	}
}

$html_to_pdf = new html_to_pdf();
