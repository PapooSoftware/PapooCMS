<?php
/**
 *
 * @package Papoo
 * @author Papoo Software
 * @copyright 2009
 * @version $Id$
 * @access public
 */
if ( stristr( $_SERVER['PHP_SELF'],'class_search_create.php') ) die( 'You are not allowed to see this page directly' );

/**
 * Class class_search_create
 */
class class_search_create {
	/**
	 * class_search_create::__construct()
	 * Konstruktor
	 * @return void
	 */
	//private $pdf_to_text = "c:\\xampp\\bin\\pdftotext.exe"; // Windows
	private $pdf_to_text = "/usr/bin/pdftotext"; // Linux

	public function __construct()
	{
		// Einbindung globaler papoo-Klassen
		global $diverse, $db, $checked, $content, $weiter, $user, $cms;
		$this->diverse = &$diverse;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->weiter = &$weiter;
		$this->user = &$user;
		$this->cms = &$cms;

		if (!defined("admin")) {
			# $this->post_papoo();
		}
	}

	public function make_highlighting()
	{
		//Konfig f�r Highlighting
		$sql = sprintf( "SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config'] );
		$result = $this->db->get_results( $sql, ARRAY_A );

		//Modus der Markierung
		if ($result['0']['ext_search_ighlighting_rt']==1) {
			$exact="exact";
		}

		if ($result['0']['ext_search_ighlighting_rt']==2) {
			$exact="whole";
		}

		if ($result['0']['ext_search_ighlighting_rt']==3) {
			$exact="partial";
		}

		if (is_array($_SESSION['search_var']) && empty($this->checked->search)) {
			if ($result['0']['ext_search_highlighting_aktivieren']==1) {
				//Suchvar erstellen aus Array
				$search_var=utf8_encode(implode(" ",$_SESSION['search_var']));

				//Js einbinden
				$js_files=$this->include_hl_js();

				//HL umsetzen
				$js_files.='
	      <style type=\'text/css\'>
	        span.hilite {'.$result['0']['ext_search__fr_igh'].'}
	      </style>

	      <script type=\'text/javascript\'>
	      jQuery(function(){
	        var options  = {
	          exact:"'.$exact.'",
	          style_name_suffix:false,
	           keys:"'.htmlspecialchars ($search_var,ENT_QUOTES,"UTF-8").'"
	          };
	        jQuery(document).SearchHighlight(options);
	      });
	  		</script>
			';
			}
			//Session zur�cksetzen damit nicht die ganze Zeit gehighlighted wird
			unset($_SESSION['search_var']);
		}

		//Suche aus Google, dann davon HL
		elseif ($result['0']['ext_search_ighlighting_auch_bei_oogle_reffern_aktivieren']==1 && empty($this->checked->search)) {
			if ($result['0']['ext_search_highlighting_aktivieren']==1) {
				//Js einbinden
				$js_files=$this->include_hl_js();

				$js_files.='
      <style type=\'text/css\'>
        span.hilite {'.$result['0']['ext_search__fr_igh'].'}
      </style>

      <script type=\'text/javascript\'>
      jQuery(function(){
        var options  = {
           exact:"'.$exact.'",
          style_name_suffix:false,
          };
        jQuery(document).SearchHighlight(options);
      });
  </script>
';
			}
			$this->binde_js_ein($js_files);
		}
	}

	/**
	 * class_search_create::binde_js_ein()
	 *
	 * @param mixed $dat
	 * @return void
	 */
	public function binde_js_ein($dat)
	{
		global $output;
		$output=preg_replace('/<\/head>/',$dat.'</head>',$output);
	}

	/**
	 * extended_search_class::include_hl_js()
	 *
	 * @return string
	 */
	function include_hl_js()
	{
		#$js ='<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/js/jquery.js"></script>';
		$js = '<script type="text/javascript" src="'.PAPOO_WEB_PFAD.'/plugins/extended_search/js/jquery.searchhighlight.js"></script>';
		return $js;
	}

	/**
	 * class_search_create::create_page()
	 *
	 * @return void
	 */
	function create_page()
	{
		//Zuerst Ausnahmen checken
		if (!empty($this->checked->inhalt_ar['Submit1']) &&
			!empty($this->checked->inhalt_ar['freigabe_internet']) ||
			!empty($this->checked->inhalt_ar['Submit_zwischen']) &&
			!empty($this->checked->inhalt_ar['freigabe_internet'])
		) {
			$this->checked->inhalt_ar['header'] = $this->checked->inhalt_ar['uberschrift'];
			$this->insert_artikel_search();
		}
	}


	/**
	 * extended_search_class::delete_rest()
	 *
	 * @param array $ids
	 * @return void
	 */
	function delete_rest($ids)
	{
		if(!is_array($ids))
			return;

		foreach ($ids as $key=>$value) {
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_id_rid='%d'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$value['ext_search_id']
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
                        WHERE ext_search_seite_id='%d'",
				$this->cms->tbname['plugin_ext_search_vorkommen'],
				$value['ext_search_id']
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param $path
	 */
	function create_page_pdf($path)
	{
		// PDF in Text umwandeln und per stdout einlesen
		$exec = "{$this->pdf_to_text} -raw -nopgbrk -enc UTF-8 '".str_replace("'", "'\''", $path)."' -";
		$txt = @shell_exec($exec) ?? '';

		$title = basename($path);

		//HIer den echten Titel holen wenn nicht leer
		$sql=sprintf("SELECT %s.downloadname FROM %s, %s
						WHERE downloadid=download_id
						AND downloadlink='%s' ",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_download",
			"/dokumente/upload/".$title);
		$downloadname=$this->db->get_var($sql);

		if (!empty($downloadname)) {
			$title=$downloadname;
		}

		//Jetzt schauen in welchem Artikel das vorkommt
		$sql=sprintf("SELECT downloadid FROM %s, %s
						WHERE downloadid=download_id
						AND downloadlink='%s' ",
			DB_PRAEFIX."papoo_language_download",
			DB_PRAEFIX."papoo_download",
			"/dokumente/upload/".basename($path));
		$downloadid=$this->db->get_var($sql);

		$sql=sprintf("SELECT lcat_id FROM %s, %s
						WHERE lan_teaser LIKE '%s'
						AND lan_repore_id=lart_id
						OR lan_article LIKE '%s'
						AND lan_repore_id=lart_id
						LIMIT 1",
			DB_PRAEFIX."papoo_lookup_art_cat",
			DB_PRAEFIX."papoo_language_article",
			"%downloadid=".$downloadid."%",
			"%downloadid=".$downloadid."%"


		);
		$menuid=0;

		$plain = $this->make_clean_content($txt);
		$description = substr($plain, 0, 300);

		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$config = $this->db->get_results($sql, ARRAY_A);

		$this->delete_page_pdf($path);

		$sql=sprintf("INSERT INTO %s SET
                ext_search_title='%s',
                ext_search_description='%s',
                ext_search_completet_text='%s',
                ext_search_document_path='%s',
                ext_search_url_time='%d',
                ext_search_lang_id='%d',
                ext_search_seite_menu_id='%d'

                ",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->db->escape($title),
			$this->db->escape($description),
			$this->db->escape($plain),
			$this->db->escape($path),
			$this->db->escape(time() + $config['0']['ext_search_blaufzeit']),
			$this->db->escape($this->cms->lang_back_content_id),
			$this->db->escape($menuid)
		);
		$result = $this->db->query($sql);
		$insert_id = $this->db->insert_id;
		$words = "$title $description $plain";
		$this->insert_woerter($words , $insert_id);

		$sql=sprintf("DELETE FROM %s WHERE ext_search_id_rid='%d'",
			$this->cms->tbname['plugin_ext_search_gruppen'],
			$this->db->escape($insert_id)
		);
		$this->db->query($sql);

		$sql=sprintf("INSERT INTO %s SET
                        ext_search_id_rid='%d',
                        ext_search_gruppen_id='%s'",
			$this->cms->tbname['plugin_ext_search_gruppen'],
			$this->db->escape($insert_id),
			$this->db->escape(10));
		$this->db->query($sql);
	}

	/**
	 * @param $path
	 */
	function delete_page_pdf($path)
	{
		$sql = sprintf("SELECT ext_search_id FROM %s WHERE ext_search_document_path='%s'
        ",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->db->escape($path)
		);
		$ids = $this->db->get_results($sql,ARRAY_A);

		$sql = sprintf("DELETE FROM %s WHERE ext_search_document_path='%s'",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->db->escape($path)
		);
		$result = $this->db->query($sql);

		$this->delete_rest($ids);
	}

	/**
	 * @param int $id
	 */
	function create_page_shop($id=0)
	{
		if (!empty($this->checked->formSubmit_produxkte)) {
			if ($id>0 && is_numeric($id)) {
				//&& !empty($this->checked->produkte_lang_produkt_beschreibung_lang)
				if (!empty($this->checked->produkte_lang_aktiv)) {

					$sql=sprintf("SELECT ext_search_id FROM %s
                            WHERE ext_search_seite_produkt_id='%d'
			    AND ext_search_lang_id='%d' ",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($id),
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$ids=$this->db->get_results($sql,ARRAY_A);

					//Löschen
					$sql=sprintf("DELETE  FROM %s
                            WHERE ext_search_seite_produkt_id='%d'
			    AND ext_search_lang_id='%d' ",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($id),
						$this->db->escape($this->cms->lang_back_content_id)
					);
					$this->db->query($sql);

					$this->delete_rest($ids);

					//Einstellungen rausholen
					$sql=sprintf("SELECT * FROM %s",
						$this->cms->tbname['plugin_ext_search_config']
					);
					$config = $this->db->get_results($sql,ARRAY_A);


					//Title der Seite
					$title=$this->checked->produkte_lang_produktename;

					//Meta Description der Seite
					$meta_descrption = $this->make_clean_content($this->checked->produkte_lang_meta_beschreibung);

					//Kompletter Text ohne HTML
					//Kompletter Text ohne HTML
					$inhalt_ohne = $this->make_clean_content($this->checked->produkte_lang_produkt_beschreibung.
						" ".$this->checked->produkte_lang_produktename.
						" ".$this->checked->produkte_lang_externeroduktid.
						" ".$this->checked->produkte_lang_ernte.
						" ".$this->checked->produkte_lang_teepflanzenvarietaet.
						" ".$this->checked->produkte_lang_lagerung.
						" ".$this->checked->produkte_lang_charakter.
						" ".$this->checked->produkte_lang_produkt_beschreibung.
						" ".$this->checked->produkte_lang_höhe.
						" ".$this->checked->produkte_lang_herkunft);

					//Daten eintragen
					$sql=sprintf("INSERT INTO %s
                            SET
                            ext_search_title='%s',
                            ext_search_description='%s',
                            ext_search_completet_text='%s',
                            ext_search_url_time='%d',
                            ext_search_seite_produkt_id='%d',
                            ext_search_lang_id='%d'",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($title),
						$this->db->escape($meta_descrption),
						$this->db->escape($inhalt_ohne),
						$this->db->escape(time()+$config['0']['ext_search_blaufzeit']),
						$this->db->escape($id),
						$this->cms->lang_back_content_id
					);
					$this->db->query($sql);

					$insert_page_id=$this->db->insert_id;

					//Gruppen durchgehen und eintragen

					//Alten Eintrag l�schen
					$sql=sprintf("DELETE FROM %s
                            WHERE ext_search_id_rid='%d'",
						$this->cms->tbname['plugin_ext_search_gruppen'],
						$this->db->escape($insert_page_id)
					);
					$this->db->query($sql);

					$sql=sprintf("INSERT INTO %s SET
                            ext_search_id_rid='%d',
                            ext_search_gruppen_id='%s'",
						$this->cms->tbname['plugin_ext_search_gruppen'],
						$this->db->escape($insert_page_id),
						$this->db->escape(10)
					);
					$this->db->query($sql);
				}

				//Inhalte splitten und eintragen
				$this->insert_woerter($inhalt_ohne." ".$title,$insert_page_id);
				//und einbauen
				//$this->make_js_file();
			}
		}
	}

	/**
	 * @param int $id
	 * @param string $tbname
	 * @param array $data
	 */
	function create_page_flex($id=0, $tbname="", $data=array())
	{
		if (!empty($this->checked->submit)) {
			if ($id>0 && is_numeric($id)) {
				//if (!empty($this->checked->faq_new_release) || !empty($this->checked->faq_release))
				{
					//Die mv_id noch auslesen
					$dat1=explode("content_",$tbname);
					$dat2=explode("_",$dat1['1']);
					$mv_ID=$dat2['0'];

					$sql=sprintf("SELECT ext_search_id FROM %s
                    WHERE ext_search_seite_mv_id='%d'
                    AND ext_search_seite_mv_content_id='%d'
                    AND ext_search_lang_id='%d' ",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($id),
						$mv_ID,
						$this->cms->lang_back_content_id
					);
					$ids=$this->db->get_results($sql,ARRAY_A);

					//L�schen
					$sql=sprintf("DELETE  FROM %s
  												WHERE ext_search_seite_mv_id='%d'
													AND ext_search_seite_mv_content_id='%d'
													AND ext_search_lang_id='%d' ",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($id),
						$mv_ID,
						$this->cms->lang_back_content_id
					);
					$this->db->query($sql);

					$this->delete_rest($ids);

					//Einstellungen rausholen
					$sql=sprintf("SELECT * FROM %s",
						$this->cms->tbname['plugin_ext_search_config']
					);
					$config=$this->db->get_results($sql,ARRAY_A);

					//Tabellle mit den Feldern
					$feld_tab="papoo_mv_content_".$mv_ID."_field_rights";

					//Felder an die Rechte 10 = jeder bestehen rausholen
					$sql=sprintf("SELECT mvcform_name,mvcform_id FROM %s
	      							LEFT JOIN %s ON mvcform_id = field_id
                  WHERE group_id='10' ",
						$this->cms->tbname[$feld_tab],
						$this->cms->tbname['papoo_mvcform']
					);
					$felder=$this->db->get_results($sql,ARRAY_A);
					if (is_array($felder)) {
						foreach ($felder as $key=>$value) {
							$feld[]=$value['mvcform_name']."_".$value['mvcform_id'];
						}
					}
					//Inhalt zusammensetzen
					if (is_array($data)) {
						foreach ($data as $key=>$value) {
							if (in_array($key,$feld)) {
								$content.=$value." ";
							}
						}
					}

					//Title der Seite
					$title=substr($content,0,150);

					//Meta Description der Seite
					$meta_descrption=$this->make_clean_content(substr($content,0,300));

					//Kompletter Text ohne HTML
					$inhalt_ohne=$this->make_clean_content($content);


					//Daten eintragen
					$sql=sprintf("INSERT INTO %s
                      SET
                      ext_search_title='%s',
                      ext_search_description='%s',
                      ext_search_completet_text='%s',
                      ext_search_url_time='%d',
                      ext_search_seite_mv_id='%d',
                      ext_search_seite_mv_content_id='%d',
                      ext_search_lang_id='%d'
                      ",
						$this->cms->tbname['plugin_ext_search_page'],
						$this->db->escape($title),
						$this->db->escape($meta_descrption),
						$this->db->escape($inhalt_ohne),
						$this->db->escape(time()+$config['0']['ext_search_blaufzeit']),
						$this->db->escape($mv_ID),
						$this->db->escape($id),

						$this->cms->lang_back_content_id
					);
					$this->db->query($sql);

					$insert_page_id=$this->db->insert_id;

					//Gruppen durchgehen und eintragen

					//Alten Eintrag l�schen
					$sql=sprintf("DELETE FROM %s
											WHERE ext_search_id_rid='%d'",
						$this->cms->tbname['plugin_ext_search_gruppen'],
						$this->db->escape($insert_page_id)
					);
					$this->db->query($sql);


					$sql=sprintf("INSERT INTO %s
                      SET
                      ext_search_id_rid='%d',
                      ext_search_gruppen_id='%s'
                      ",
						$this->cms->tbname['plugin_ext_search_gruppen'],
						$this->db->escape($insert_page_id),
						$this->db->escape(10)
					);
					$this->db->query($sql);
				}

				//Inhalte splitten und eintragen
				$this->insert_woerter($inhalt_ohne,$insert_page_id);
				//und einbauen
				//$this->make_js_file();
			}
		}
	}

	/**
	 * @param int $id
	 */
	function create_page_faq($id=0)
	{
		if(empty($this->checked->submit)) {
			return;
		}

		if ($id == 0 && !is_numeric($id)) {
			return;
		}

		if (!empty($this->checked->faq_new_release) ||
			!empty($this->checked->faq_release)) {

			$sql=sprintf("SELECT ext_search_id FROM %s
                    WHERE ext_search_seite_faq_id='%d'
                    	AND ext_search_lang_id='%d' ",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($id),
				$this->cms->lang_back_content_id
			);
			$ids=$this->db->get_results($sql,ARRAY_A);

			//L�schen
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_seite_faq_id='%d'
                    	AND ext_search_lang_id='%d' ",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($id),
				$this->cms->lang_back_content_id
			);
			$this->db->query($sql);

			$this->delete_rest($ids);

			//Einstellungen rausholen
			$sql=sprintf("SELECT * FROM %s",
				$this->cms->tbname['plugin_ext_search_config']
			);
			$config=$this->db->get_results($sql,ARRAY_A);

			//Title der Seite
			$title=$this->checked->faq_question;

			//Meta Description der Seite
			$meta_descrption=$this->make_clean_content($this->checked->faq_answer);

			//Kompletter Text ohne HTML
			$inhalt_ohne=$this->make_clean_content($this->checked->faq_answer);

			//Daten eintragen
			$sql=sprintf("INSERT INTO %s SET
                    ext_search_title='%s',
                    ext_search_description='%s',
                    ext_search_completet_text='%s',
                    ext_search_url_time='%d',
                    ext_search_seite_faq_id='%d',
                    ext_search_lang_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($title),
				$this->db->escape($meta_descrption),
				$this->db->escape($inhalt_ohne),
				$this->db->escape(time()+$config['0']['ext_search_blaufzeit']),
				$this->db->escape($id),
				$this->cms->lang_back_content_id
			);
			$this->db->query($sql);

			$insert_page_id=$this->db->insert_id;

			//Gruppen durchgehen und eintragen

			//Alten Eintrag l�schen
			$sql=sprintf("DELETE FROM %s
                    WHERE ext_search_id_rid='%d'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($insert_page_id)
			);
			$this->db->query($sql);

			$sql=sprintf("INSERT INTO %s SET
                            ext_search_id_rid='%d',
                            ext_search_gruppen_id='%s'
                            ",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($insert_page_id),
				$this->db->escape(10)
			);
			$this->db->query($sql);
		}

		//Inhalte splitten und eintragen
		$this->insert_woerter($this->checked->faq_answer,$insert_page_id);
		//und einbauen
		//$this->make_js_file();
	}

	function insert_artikel_search()
	{
		if ($this->checked->reporeid > 1) {
			//Daten zu dem Artikel raussuchen
			$sql = sprintf("SELECT ext_search_id FROM %s
                  	WHERE ext_search_seite_reporeid='%d'
                  	AND ext_search_lang_id='%d' ",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->reporeid),
				$this->cms->lang_back_content_id
			);
			$ids=$this->db->get_results($sql,ARRAY_A);

			//L�schen
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_seite_reporeid='%d'
                    AND ext_search_lang_id='%d' ",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($this->checked->reporeid),
				$this->cms->lang_back_content_id
			);
			$this->db->query($sql);
			$this->delete_rest($ids);
		}

		//Einstellungen rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$config = $this->db->get_results($sql,ARRAY_A);

		//Title der Seite
		$title = $this->checked->inhalt_ar['metatitel'];

		IfNotSetNull($this->checked->inhalt_ar['metakey']);

		//Meta Description der Seite //metakey
		$meta_descrption = $this->make_clean_content(
			$this->checked->inhalt_ar['metadescrip']." ".
			$this->checked->inhalt_ar['metakey']
		);
		//Kompletter Text ohne HTML
		$inhalt_ohne = $this->make_clean_content($this->checked->inhalt_ar['inhalt']);

		if(empty($this->checked->reporeid)) {
			return;
		}

		//Daten eintragen
		$sql=sprintf("INSERT INTO %s SET
                ext_search_title='%s',
                ext_search_description='%s',
                ext_search_header='%s',
                ext_search_completet_text='%s',
                ext_search_url_time='%d',
                ext_search_seite_reporeid='%d',
                ext_search_url='%s',
                ext_search_lang_id='%d',
                ext_search_seite_menu_id='%d'",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->db->escape($title),
			$this->db->escape($meta_descrption),
			$this->db->escape($this->checked->inhalt_ar['header']),
			$this->db->escape($inhalt_ohne),
			$this->db->escape(time()+$config['0']['ext_search_blaufzeit']),
			$this->db->escape($this->checked->reporeid),
			$this->db->escape($this->checked->inhalt_ar['url_header']),
			$this->cms->lang_back_content_id,
			$this->db->escape($this->checked->inhalt_ar['lcat_id'])
		);
		$this->db->query($sql);
		$insert_page_id = $this->db->insert_id;
		//Gruppen durchgehen und eintragen
		if(is_array($this->checked->inhalt_ar['gruppe_write'])) {
			//Alten Eintrag l�schen
			$sql=sprintf("DELETE FROM %s
                    WHERE ext_search_id_rid='%d'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($insert_page_id)
			);
			$this->db->query($sql);

			foreach($this->checked->inhalt_ar['gruppe_write'] as $key=>$value) {
				$sql=sprintf("INSERT INTO %s SET
                        ext_search_id_rid='%d',
                        ext_search_gruppen_id='%s'",
					$this->cms->tbname['plugin_ext_search_gruppen'],
					$this->db->escape($insert_page_id),
					$this->db->escape($value)
				);
				$this->db->query($sql);
			}
		}
		//Inhalte splitten und eintragen

		// metainformationen und inhalt eintragen

		$inhalt = $this->checked->inhalt_ar['inhalt'] . " " .
			$title . " " .
			$meta_descrption . " " .
			$this->checked->inhalt_ar['header'];

		$this->insert_woerter($inhalt, $insert_page_id);
		//und einbauen
		//$this->make_js_file();
	}

	/**
	 * class_search_create::create_page()
	 *
	 * @param int $id
	 * @return void
	 */
	function create_page_front($id=0)
	{
		//Zuerst Ausnahmen checken
		if (empty($this->checked->uebermittelformular) || empty($this->checked->inhalt)) {
			return;
		}

		//Einstellungen rausholen
		$sql=sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$config=$this->db->get_results($sql,ARRAY_A);

		//Title der Seite
		$title=$this->checked->formthema;

		require_once(PAPOO_ABS_PFAD."/lib/bbcode.inc.php");
		$bbcode = new BBCode();
		// Objekt erzeugen

		$inhalt = $bbcode->parse($this->checked->inhalt);
		//Meta Description der Seite
		$meta_descrption = $this->make_clean_content($inhalt);

		//Kompletter Text ohne HTML
		$inhalt_ohne = $this->make_clean_content($inhalt);

		#$this->alle_woerter_mit_bonus
		if (!empty($this->checked->forumid)) {
			//Daten eintragen
			$sql=sprintf("INSERT INTO %s SET
                    ext_search_title='%s',
                    ext_search_description='%s',
                    ext_search_completet_text='%s',
                    ext_search_url_time='%d',
                    ext_search_seite_forum_id='%d',
                    ext_search_seite_message_id='%d',
                    ext_search_lang_id='%d'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($title),
				$this->db->escape($meta_descrption),
				$this->db->escape($inhalt_ohne),
				$this->db->escape(time()+$config['0']['ext_search_blaufzeit']),
				$this->db->escape($this->checked->forumid),
				$this->db->escape($id),
				$this->cms->lang_id
			);
			$this->db->query($sql);

			$insert_page_id=$this->db->insert_id;

			//Gruppenrechte rausholen
			$sql=sprintf("SELECT gruppenid FROM %s
                    WHERE forumid='%d'",
				$this->cms->tbname['papoo_lookup_forum_read'],
				$this->db->escape($this->checked->forumid)
			);
			$result=$this->db->get_results($sql,ARRAY_A);

			//Gruppen durchgehen und eintragen
			if(is_array($result)) {
				//Alten Eintrag l�schen
				$sql=sprintf("DELETE FROM %s WHERE
                        ext_search_id_rid='%d'",
					$this->cms->tbname['plugin_ext_search_gruppen'],
					$this->db->escape($insert_page_id)
				);
				$this->db->query($sql);

				foreach ($result as $key=>$value) {
					$sql=sprintf("INSERT INTO %s SET
                            ext_search_id_rid='%d',
                            ext_search_gruppen_id='%s'",
						$this->cms->tbname['plugin_ext_search_gruppen'],
						$this->db->escape($insert_page_id),
						$this->db->escape($value['gruppenid'])
					);
					$this->db->query($sql);
				}
			}
		}

		//Inhalte splitten und eintragen
		$this->insert_woerter($inhalt, $insert_page_id);

		//und einbauen
		//$this->make_js_file();
	}

	/**
	 * extended_search_class::insert_woerter()
	 *
	 * @param mixed $inhalt
	 * @param $page_id
	 * @return void
	 */
	function insert_woerter($inhalt, $page_id)
	{
		$this->alle_woerter_mit_bonus = array();
		$this->alle_woerter=array();

		//Zuerst das Array mit den Bonus Punkten erstellen
		$bonus = $this->get_bonus();
		// $hits_complete=array();
		//Boni durchgehen
		if (is_array($bonus)) {
			foreach (array_keys($bonus) as $tag) {
				preg_match_all('#<'.$tag.'\b.+?</'.$tag.'>#',$inhalt,$hits);
				#$hits_complete[]=$hits;
				//Jetzt den jeweiligen Eintrag durchgehen
				if(!is_array($hits)) {
					continue;
				}
				foreach ($hits as $element) {
					IfNotSetNull($element['0']);
					$this->woerter_ermitteln($element['0'],$bonus[$tag]);
				}
			}
		}
		//Komplett indexieren
		$this->woerter_ermitteln($inhalt, 1);

		$statement = array();

		//Statement erzeugen
		if(is_array($this->alle_woerter_mit_bonus)) {
			foreach($this->alle_woerter_mit_bonus as $key => $value) {
				$statement[] ="('".$page_id."',
                    '".$this->db->escape($key)."',
                    '".$this->db->escape($value)."')";
			}
			$statement = implode(", ", $statement);
		}

		if(empty($statement))
			return;

		//Zuerst alten Eintrag l�schen
		$sql=sprintf("DELETE  FROM %s
                WHERE ext_search_seite_id='%d'",
			$this->cms->tbname['plugin_ext_search_vorkommen'],
			$page_id
		);
		$this->db->query($sql);

		//ausf�hren
		$sql=sprintf("INSERT INTO %s (
                `ext_search_seite_id`,
                `ext_search_wort_id`,
                `ext_search_score_id`)
                VALUES %s",
			$this->cms->tbname['plugin_ext_search_vorkommen'],
			$statement
		);
		$this->db->query($sql);
	}

	/**
	 * extended_search_class::woerter_ermitteln()
	 *
	 * @param $content
	 * @param $bonus_el
	 * @return void
	 */
	function woerter_ermitteln($content,$bonus_el)
	{
		$stop_woerter=$this->get_Stoppwoerter();


		// Zuerst s�ubern
		$content = $this->make_clean($content);

		// W�rter erzeugen
		$woerter=explode(' ',trim($content));

		if(!is_array($woerter)) {
			return;
		}

		foreach ($woerter as $key=>$wort) {
			if (strlen($wort)<=2) {
				continue;
			}

			if (is_numeric($wort)) {
				continue;
			}

			if (function_exists("mb_strtolower")) {
				$wort = mb_strtolower($wort,"UTF-8");
			}

			if (in_array($wort, $stop_woerter)) {
				continue;
			}

			//Werte eintragen
			IfNotSetNull($this->alle_woerter_mit_bonus[$wort]);

			$this->alle_woerter_mit_bonus[$wort] += (int)$bonus_el;
			$this->alle_woerter[$wort]=$wort;
		}
	}

	/**
	 * extended_search_class::make_clean()
	 *
	 * @param $content
	 * @return mixed $contet
	 */
	function make_clean($content)
	{
		//Skripte etc ersetzen
		$content=preg_replace('/\n/'," ",$content);
		$content=preg_replace('/\r/'," ",$content);
		$content=preg_replace('/\t/'," ",$content);
		$content=preg_replace('/\?/'," ",$content);
		$content=preg_replace('/&nbsp;/'," ",$content);
		$content=preg_replace('/\./'," ",$content);
		$content=preg_replace('/,/'," ",$content);
		$content=preg_replace('/;/'," ",$content);
		$content=preg_replace('/_/'," ",$content);
		$content=preg_replace('/<\/label>/'," </label>",$content);
		$content=preg_replace('#<style\b.+?</style>#',"",$content);
		$content=preg_replace('#<script\b.+?</script>#',"",$content);
		$content=preg_replace('#<option\b.+?</option>#',"",$content);

		$content=preg_replace('/</'," <",$content);
		//HTML entfernen
		#$content=html_entity_decode($content);
		$content=strip_tags($content);

		//Satzzeichen etc. entfernen
		#$content=(preg_replace('/\W+/',' ',($content)));
		$content = preg_replace('/\s+/', ' ', $content);

		return $content;
	}

	/**
	 * extended_search_class::make_clean_content()
	 *
	 * @param mixed $content
	 * @return mixed $content
	 */
	function make_clean_content($content)
	{
		//Skripte etc ersetzen
		$content=preg_replace('/\n/'," ",$content);
		$content=preg_replace('/\r/'," ",$content);
		$content=preg_replace('/\t/'," ",$content);
		$content=preg_replace('/&nbsp;/'," ",$content);
		$content=preg_replace('/<\/label>/'," </label>",$content);
		$content=preg_replace('#<style\b.+?</style>#',"",$content);
		$content=preg_replace('#<script\b.+?</script>#',"",$content);
		$content=preg_replace('#<option\b.+?</option>#',"",$content);
		$content=preg_replace('#<label\b.+?</label>#',"",$content);
		$content=preg_replace('/</'," <",$content);

		//HTML entfernen
		#$content=html_entity_decode($content);
		$content=strip_tags($content);

		//Satzzeichen etc. entfernen
		#$content = preg_replace('/\s+/', ' ', $content);
		#$content=utf8_encode(preg_replace('/\W-/',' ',utf8_decode($content)));

		return $content;
	}

	/**
	 * extended_search_class::get_bonus()
	 *
	 * @return void|array
	 */
	function get_bonus()
	{
		$sql=sprintf("SELECT ext_search_lemente_mit_ewichtung FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$result=$this->db->get_var($sql);

		//Einzeldaten erzeugen
		$gewichtungen = explode("\n",$result);

		//Daten durchgehen
		if(!is_array($gewichtungen)) {
			return;
		}

		$gewichte = array();
		foreach ($gewichtungen as $key=>$value) {
			$ge1=explode("|",$value);
			$gewichte[$ge1['0']]=$ge1['1'];
		}

		return $gewichte;
	}

	/**
	 * @return array
	 */
	function get_Stoppwoerter()
	{
		$sql=sprintf("SELECT ext_search_top_rter
                FROM %s",
			$this->cms->tbname['plugin_ext_search_config']
		);
		$result=$this->db->get_var($sql);

		//Einzeldaten erzeugen
		$stopp_woerter=explode("\n",$result);

		if (is_array($stopp_woerter)) {
			foreach ($stopp_woerter as $key=>$value) {
				$neu[]=trim($value);
			}
		}

		return $neu;
	}

	/**
	 * extended_search_class::delete_alten_eintrag()
	 *
	 * @param mixed $url_existst
	 * @param string $url
	 * @return void
	 */
	function delete_alten_eintrag($url_existst="NO",$url="NO")
	{
		$sql=sprintf("SELECT ext_search_id
                FROM %s
                LEFT JOIN %s
                ON ext_search_id_rid=ext_search_id
                LEFT JOIN %s
                ON ext_search_gruppen_id=gruppenid
                WHERE
                ext_search_url='%s'
                AND
                ext_search_url_time<'%d'
                AND
                userid='%d'",
			$this->cms->tbname['plugin_ext_search_page'],
			$this->cms->tbname['plugin_ext_search_gruppen'],
			$this->cms->tbname['papoo_lookup_ug'],
			$this->db->escape($url),
			time(),
			$this->user->userid
		);
		$url_existst=$this->db->get_results($sql,ARRAY_A);
		if(!is_array($url_existst))
			return;
		foreach ($url_existst as $key=>$value) {
			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_id='%s'",
				$this->cms->tbname['plugin_ext_search_page'],
				$this->db->escape($value['ext_search_id'])
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_id_rid='%s'",
				$this->cms->tbname['plugin_ext_search_gruppen'],
				$this->db->escape($value['ext_search_id'])
			);
			$this->db->query($sql);

			$sql=sprintf("DELETE  FROM %s
                    WHERE ext_search_seite_id='%s'",
				$this->cms->tbname['plugin_ext_search_vorkommen'],
				$this->db->escape($value['ext_search_id'])
			);
			$this->db->query($sql);
		}
	}


	/**
	 * extended_search_class::check_if_ausnahme()
	 *
	 * @return void|false
	 */
	function check_if_ausnahme()
	{
		/**
		 * //In der Suche, dann nix
		 *     if (!empty($this->checked->search))
		 *       return true;
		 *
		 *     //url rausholen
		 *     $url=$_SERVER['REQUEST_URI'];
		 *
		 *     if (strstr($url,"var="))
		 *       return true;
		 *
		 *     if (strstr($url,"print="))
		 *       return true;
		 *
		 *     if (!empty($this->checked->menuid) && !is_numeric($this->checked->menuid))
		 *       return true;
		 *
		 *     if (!empty($this->checked->reporeid) && !is_numeric($this->checked->reporeid))
		 *       return true;
		 *
		 *     if (!empty($this->checked->msgid) && !is_numeric($this->checked->msgid))
		 *       return true;
		 *
		 *     if (!empty($this->checked->forumid) && !is_numeric($this->checked->forumid))
		 *       return true;
		 *
		 *     if (!strstr($url,"html"))
		 *       return true;
		 *
		 */
		//Alles ok, dann keine Ausnahme also false
		return false;
	}

	function make_js_file()
	{
		if(empty($_SESSION['search_var']))
			return;

		if (is_array($_SESSION['search_var'])) {
			foreach ($_SESSION['search_var'] as $key => $value) {
				$resultdat .= utf8_encode($value).";";
			}
		}
		#unset($_SESSION['search_var']);

		$data= $this->replace_var($resultdat);

		#$this->make_js($data);
	}

	/**
	 * @param $replace
	 * @return mixed|string|string[]|null
	 */
	function replace_var($replace) {
		$javvar='var visi_text="xxreplxx";var subdir="xxsubdirxx";function doHighlight(bodyText, searchTerm, highlightStartTag, highlightEndTag)
  {
    if ((!highlightStartTag) || (!highlightEndTag)) {
  	replaceArray = searchTerm.split("##");
  	 if(replaceArray[1])
  		 {
  		 searchTermx=replaceArray[1];

  		 searchTerm=replaceArray[0];
  		 }
  		 else {
  			 searchTermx=searchTerm;
  				}
  		searchTermx = searchTermx.replace(/ /g,"_");
      highlightStartTag = "<span class=\"highlight_search\" >";
      highlightEndTag = "</span>";
    }
  	searchTerm=" "+searchTerm;
    var newText = "";
    var i = -1;
  	var insert ="";
    var lcSearchTerm = searchTerm.toLowerCase();
    var lcBodyText = bodyText.toLowerCase();

    while (bodyText.length > 0) {
      i = lcBodyText.indexOf(lcSearchTerm, i+1);
      if (i < 0) {
        newText += bodyText;
        bodyText = "";
      } else {
        // skip anything inside an HTML tag
        if (bodyText.lastIndexOf(">", i) >= bodyText.lastIndexOf("<", i)) {
  			  if (lcBodyText.lastIndexOf("/script>", i) >= lcBodyText.lastIndexOf("<script", i)) {
           if (lcBodyText.lastIndexOf("/a>", i) >= lcBodyText.lastIndexOf("<a ", i)) {
  				  insert=(bodyText.substr(i+1, searchTerm.length-1));
  					//insert = insert.replace(/ /g,"");
            newText += bodyText.substring(0, i) +\' \'+ highlightStartTag + insert + highlightEndTag;

            bodyText = bodyText.substr(i + searchTerm.length);
            lcBodyText = bodyText.toLowerCase();
            i = -1;
  					}
  				}
  			}
      }
    }
    return newText;
  }

  function visilexit(searchText, treatAsPhrase, warnOnFailure, highlightStartTag, highlightEndTag)
  {

    if (treatAsPhrase) {
      searchArray = [searchText];
    } else {
      searchArray = searchText.split(";");
    }

    if (!document.body || typeof(document.body.innerHTML) == "undefined") {
      if (warnOnFailure) {
        alert("Sorry, for some reason the text of this page is unavailable. Searching will not work.");
      }
      return false;
    }
    var bodyText = document.body.innerHTML;

    for (var i = 0; i < searchArray.length; i++) {
      bodyText = doHighlight(bodyText, searchArray[i], highlightStartTag, highlightEndTag);
    }

    document.body.innerHTML = bodyText;
    return true;
  }
  ';
		//erstezen
		$javneu = str_ireplace("xxreplxx",$replace,$javvar);
		//unterverz. xxsubdirxx
		global $webverzeichnis;
		$javneu = str_ireplace("xxsubdirxx",$webverzeichnis,$javneu);
		return $javneu;

	}

	/**
	 * XML Datei erstellen f�r die CSS Einbindung
	 *
	 * @param $data
	 */
	function make_js($data){
		$zeile = "";
		$file = "/interna/templates_c/highlight.js";
		$this->diverse->write_to_file($file, $data);
	}
}
