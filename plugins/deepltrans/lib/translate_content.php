<?php

/**
 * Diese Klasse übersetzt den übergebenden Inhalt in die gewünschte Sprache
 * Class translate
 */

class translate_content extends deepltrans_class
{
	/**
	 * @var transDeeplNow
	 * die TranslateDeepl Klasse
	 */
	private $transDeeplNow;

	/**
	 * translate constructor.
	 */
	public function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content,$cms;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;
		$this->cms = &$cms;


		require_once __DIR__."/transdeepl.php";
		$this->transDeeplNow = new transdeepl();
	}

	/**
	 * @return bool
	 */
	public function translateAll()
	{
		$this->content->template['translinkContent']="plugin.php?menuid=".$this->checked->menuid."&template=deepltrans/templates/content_bersetzungen_backend.html";
		//exit();
		#ini_set("display_errors", true);
		#error_reporting(E_ALL);
		if(!empty($this->checked->transcontent))
		{
			$langId = $this->getLangId($this->checked->lang);
			if(is_numeric($langId))
			{
				$this->translateNow($this->checked->lang,$langId);
			}
			return true;
		}
	}

	/**
	 * @param string $lang
	 * @return false|mixed
	 */
	public function getLangId($lang="")
	{
		$data = $this->get_active_lngs();
		foreach ($data as $k=>$v)
		{
			if($v['lang_short']==$lang)
			{
				return $v['lang_id'];
			}
		}
		return false;
	}

	/**
	 * @param string $lang
	 * @param string $langId
	 * @return mixed|void
	 */
	public function translateNow($lang="",$langId="")
	{
		//Set Deepl Data
		$this->transDeeplNow->set_aut_key($this->get_key());
		$this->transDeeplNow->setDeeplUrl();

		//Lang Data for items
		$langData= array($lang,$langId);

		$this->db->csrfok = true;

		/**
		 * Start Translation Backend
		 */
		//$this->translateBackendMenu($langData);

		/**
		 * Start Translation Frontend
		 */
		/**
		$this->translateFrontendMenu($langData);
		$this->translateStartPage($langData);
		$this->translateArticle($langData);
		$this->translateCollum3($langData);
		$this->translateImages($langData);
		$this->translateTopMenu($langData);
		$this->translateGalImages($langData);
		$this->translateGaleries($langData);
		$this->translateCForm($langData);
		$this->translateDownload($langData);
		$this->translateVideo($langData);
		$this->translateCategories($langData);
		$this->translateLinklisten($langData);
		$this->translateLinklistenCat($langData);
		$this->translateLinklistenCatPref($langData);
		$this->translateModule($langData);
		$this->translateModule($langData);
		$this->translatePlugins($langData);
		$this->translateKalender($langData);
		$this->translateKalenderEntries($langData);
		$this->translateFreieModule($langData);
		$this->translateBildwechsler($langData);
		$this->translateFormCformLang($langData);
		$this->translateFormGroupLang($langData);
		$this->translateFormLang($langData);
		$this->translateGlossar($langData);
		$this->translateGlossarPref($langData);
		$this->translateMVForms($langData);
		$this->translateMVFormsFelder($langData);
		$this->translateMVContents($langData);
		$this->translateMVTemplate($langData);
		$this->translateMVMetaLang($langData);
		$this->translateMVLang($langData);
		$this->translateBanner($langData);
		$this->translateFAQCats($langData);
		$this->translateFAQQuestions($langData);
		$this->translateFAQConfig($langData);
		 **/

		$this->translateFAQConfig($langData);
		//Shop noch offen...

		//TODO die Alternativtexte der Bilder müssen noch ersetzt werden
		//
		$this->db->csrfok = false;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateFAQConfig($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"title",
			"descript",
			"keywords",
			"faq_header",
			"faq_head_text",
			"faq_footer"
		);

		$felderMitLang = array();

		$tbName = "papoo_faq_config";
		$idName = "id";
		$langIdName = "lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateFAQQuestions($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"question",
			"answer"
		);

		$felderMitLang = array();

		$tbName = "papoo_faq_content";
		$idName = "id";
		$langIdName = "lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateFAQCats($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"catname",
			"catdescript"
		);

		$felderMitLang = array();

		$tbName = "papoo_faq_categories";
		$idName = "id";
		$langIdName = "lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateBanner($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"banner_name",
			"banner_code"
		);

		$felderMitLang = array();

		$tbName = "papoo_bannerverwaltung_daten";
		$idName = "banner_id";
		$langIdName = "banner_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateMVLang($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"mv_name_label"
		);

		$felderMitLang = array();

		$tbName = "papoo_mv_lang";
		$idName = "mv_id_id";
		$langIdName = "mv_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateMVMetaLang($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"mv_meta_top_text",
			"mv_meta_bottom_text",
			"mv_meta_antwort_text"
		);

		$felderMitLang = array();

		$idName = "mv_meta_lang_id";
		$langIdName = "mv_meta_lang_lang_id";

		//Die MVs raus suchen -... müssen ja alle
		$sql = sprintf("SELECT * FROM %s ",DB_PRAEFIX."papoo_mv");
		$mvs = $this->db->get_results($sql,ARRAY_A);

		if(!empty($mvs))
		{
			foreach($mvs as $mv)
			{
				//Die Base Tabelle der Daten...
				$tbName = "papoo_mv_meta_lang_".$mv['mv_id'];

				//Dann übersetzen und in DB schreiben.
				$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);
			}
		}

		return true;
	}

	public function translateMVTemplate($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"template_content_all",
			"template_content_one",
			"template_content_flex_link_selection",
			"template_content_flex_link_tree"
		);

		$felderMitLang = array();

		$idName = "id";
		$langIdName = "lang_id";

		//Die MVs raus suchen -... müssen ja alle
		$sql = sprintf("SELECT * FROM %s ",DB_PRAEFIX."papoo_mv");
		$mvs = $this->db->get_results($sql,ARRAY_A);

		if(!empty($mvs))
		{
			foreach($mvs as $mv)
			{
				//Die Base Tabelle der Daten...
				$tbName = "papoo_mv_template_".$mv['mv_id'];

				//Dann übersetzen und in DB schreiben.
				$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);
			}
		}

		return true;
	}

	public function translateMVContents($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felderNot = array(
			"mv_content_id",
			"mv_content_owner",
			"mv_content_userid",
			"mv_content_search",
			"mv_content_sperre",
			"mv_content_teaser",
			"mv_content_create_date",
			"mv_content_edit_date",
			"mv_content_create_owner",
			"mv_content_edit_user"
		);

		$felderMitLang = array();

		$idName = "mv_content_id";
		$langIdName = "xxx";

		//Die MVs raus suchen -... müssen ja alle
		$sql = sprintf("SELECT * FROM %s ",DB_PRAEFIX."papoo_mv");
		$mvs = $this->db->get_results($sql,ARRAY_A);

		if(!empty($mvs))
		{
			foreach($mvs as $mv)
			{
				//Die Base Tabelle der Daten...
				$tbName = "papoo_mv_content_".$mv['mv_id']."_search_1";

				//Dann übersetzen und in DB schreiben.
				$this->translateLangTableFlex($tbName,$idName,$felderNot,$langData,$felderMitLang,$langIdName);
			}
		}

		return true;
	}

	public function translateMVFormsFelder($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"mvcform_label",
			"mvcform_content_list",
			"mvcform_descrip",
			"mvcform_lang_header",
			"mvcform_lang_tooltip"
		);

		$felderMitLang = array();

		$tbName = "papoo_mvcform_lang";
		$idName = "mvcform_lang_id";
		$langIdName = "mvcform_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateMVForms($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"mvcform_group_lang_meta",
			"mvcform_group_text",
			"mvcform_group_text_intern"
		);

		$felderMitLang = array();

		$tbName = "papoo_mvcform_group_lang";
		$idName = "mvcform_group_lang_id";
		$langIdName = "mvcform_group_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateGlossarPref($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"glosspref_introtext_de"
		);

		$felderMitLang = array();

		$tbName = "glossar_pref_html";
		$idName = "glosspref_id_id";
		$langIdName = "glosspref_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateGlossar($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"glossar_descrip",
			"glossar_descrip_sans",
			"glossar_link",
			"glossar_Wort",
			"glossar_Wort_alt",
			"glossar_meta_title",
			"form_manager_lang_button",
			"glossar_meta_descrip",
			"glossar_meta_key",
			"form_manager_text_html"
		);

		$felderMitLang = array();

		$tbName = "glossar_daten";
		$idName = "glossar_id";
		$langIdName = "glossar_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateFormLang($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"form_manager_antwort_html",
			"form_manager_toptext_html",
			"form_manager_bottomtext_html",
			"form_manager_antwort_email_betreff",
			"form_manager_antwort_email",
			"form_manager_antwort_email_html",
			"form_manager_lang_button",
			"mail_an_betreiber_betreff",
			"mail_an_betreiber_inhalt",
			"form_manager_text_html"
		);

		$felderMitLang = array();

		$tbName = "papoo_form_manager_lang";
		$idName = "form_manager_id_id";
		$langIdName = "form_manager_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateFormGroupLang($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"plugin_cform_group_text",
			"plugin_cform_group_text_intern"
		);

		$felderMitLang = array();

		$tbName = "papoo_plugin_cform_group_lang";
		$idName = "plugin_cform_group_lang_id";
		$langIdName = "plugin_cform_group_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateFormCformLang($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"plugin_cform_label",
			"plugin_cform_descrip",
			"plugin_cform_tooltip",
			"plugin_cform_lang_header",
			"plugin_cform_content_list"
		);

		$felderMitLang = array();

		$tbName = "papoo_plugin_cform_lang";
		$idName = "plugin_cform_lang_id";
		$langIdName = "plugin_cform_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	//
	public function translateBildwechsler($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"bw_text",
			"bw_ueberschrift",
			"bw_noch_mehr_text",
			"bw_extra_link_text"
		);

		$felderMitLang = array();

		$tbName = "bildwechsler";
		$idName = "bw_id";
		$langIdName = "bw_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	public function translateFreieModule($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"freiemodule_name",
			"freiemodule_code"
		);

		$felderMitLang = array();

		$tbName = "papoo_freiemodule_daten";
		$idName = "freiemodule_id";
		$langIdName = "freiemodule_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	public function translateKalenderEntries($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"pkal_date_titel_des_termins",
			"pkal_date_terminbeschreibung"
		);

		$felderMitLang = array();

		$tbName = "plugin_kalender_date";
		$idName = "pkal_date_id";
		$langIdName = "pkal_date_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateKalender($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"kalender_bezeichnung_des_kalenders",
			"kalender_text_oberhalb"
		);

		$felderMitLang = array();

		$tbName = "plugin_kalender";
		$idName = "kalender_id";
		$langIdName = "kalender_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	public function translatePlugins($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"pluginlang_beschreibung"
		);

		$felderMitLang = array();

		$tbName = "papoo_plugin_language";
		$idName = "pluginlang_plugin_id";
		$langIdName = "pluginlang_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateModule($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"modlang_name",
			"modlang_beschreibung"
		);

		$felderMitLang = array();

		$tbName = "papoo_module_language";
		$idName = "modlang_mod_id";
		$langIdName = "modlang_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateLinklistenCatPref($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"linkliste_lang",
			"linkliste_title",
			"linkliste_description",
			"linkliste_keywords"
		);

		$felderMitLang = array();

		$tbName = "papoo_linkliste_lang_pref";
		$idName = "linkliste_id_id";
		$langIdName = "linkliste_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateLinklistenCat($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"cat_name",
			"cat_title",
			"cat_description",
			"cat_keywords"
		);

		$felderMitLang = array();

		$tbName = "papoo_linkliste_lang_cat";
		$idName = "cat_id_id";
		$langIdName = "cat_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}


	public function translateLinklisten($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"linkliste_Wort",
			"linkliste_link_lang",
			"linkliste_descrip",
			"linkliste_lang_header"
		);

		$felderMitLang = array();

		$tbName = "papoo_linkliste_daten_lang";
		$idName = "linkliste_lang_id";
		$langIdName = "linkliste_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}



	public function translateCategories($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"cat_text",
			"cat_text_intern",
			"cat_descrip",
			"cat_lang_header"
		);

		$felderMitLang = array();

		$tbName = "papoo_category_lang";
		$idName = "cat_lang_id";
		$langIdName = "cat_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateGaleries($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"gallang_name",
			"gallang_beschreibung"
		);

		$felderMitLang = array();

		$tbName = "galerie_galerien_language";
		$idName = "gallang_gal_id";
		$langIdName = "gallang_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateVideo($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"video_alt",
			"video_title",
			"video_longdesc"
		);

		$felderMitLang = array();

		$tbName = "papoo_language_video";
		$idName = "lan_video_id";
		$langIdName = "video_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateDownload($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"downloadname"
		);

		$felderMitLang = array();

		$tbName = "papoo_language_download";
		$idName = "download_id";
		$langIdName = "lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateCForm($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"cform_text",
			"cform_text_intern",
			"cform_descrip",
			"cform_lang_header"
		);

		$felderMitLang = array();

		$tbName = "papoo_cform_lang";
		$idName = "cform_lang_id";
		$langIdName = "cform_lang_lang";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateGalImages($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"bildlang_name",
			"bildlang_beschreibung"
		);

		$felderMitLang = array();

		$tbName = "galerie_bilder_language";
		$idName = "bildlang_bild_id";
		$langIdName = "bildlang_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateTopMenu($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"mtlang_name",
			"mtlang_title",
			"mtlang_link"
		);

		$felderMitLang = array("mtlang_link"=>true);

		$tbName = "papoo_menutop_language";
		$idName = "mtlang_menutop_id";
		$langIdName = "mtlang_lang_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang,$langIdName);

		return true;
	}

	public function translateImages($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"alt",
			"title",
			"longdesc"
		);

		$felderMitLang = array();

		$tbName = "papoo_language_image";
		$idName = "lan_image_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang);

		return true;
	}

	public function translateCollum3($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"name",
			"article",
			"article_sans"
		);

		$felderMitLang = array();

		$tbName = "papoo_language_collum3";
		$idName = "collum_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang);

		return true;
	}

	public function translateArticle($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"header",
			"lan_teaser",
			"lan_article",
			"lan_article_sans",
			"lan_article_markdown",
			"lan_metatitel",
			"lan_metadescrip",
			"lan_metakey",
			"url_header",
			"lan_article_search"
		);

		$felderMitLang = array("url_header"=>true);

		$tbName = "papoo_language_article";
		$idName = "lan_repore_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang);

		return true;
	}



	public function translateStartPage($langData=array())
	{

		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"head_title",
			"start_text",
			"start_text_sans",
			"beschreibung",
			"stichwort",
			"seitentitle",
			"kontakt_text",
			"kontakt_text_sans"
		);

		$tbName = "papoo_language_stamm";
		$idName = "stamm_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateFrontendMenu($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"menuname",
			"lang_title",
			"url_menuname",
			"url_metadescrip",
			"url_metakeywords"
		);

		$felderMitLang = array("url_menuname"=>true);

		$tbName = "papoo_menu_language";
		$idName = "menuid_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang);

		return true;
	}

	/**
	 * @param array $langData
	 * @return bool
	 */
	public function translateBackendMenu($langData=array())
	{
		//erstmal die Daten festlegen der Felder und CO.
		$felder = array(
			"menuname",
			"lang_title"
		);

		$tbName = "papoo_men_uint_language";
		$idName = "menuid_id";

		//Dann übersetzen und in DB schreiben.
		$this->translateLangTable($tbName,$idName,$felder,$langData);

		return true;
	}

	/**
	 * Nimmt Felder und sonstige Daten und übersetzt die Inhalte und trägt sie dann auch anhand der target lang_id wieder ein...
	 * @param $tbName //Tabellenanme
	 * @param $idName
	 * @param $felder
	 * @param $langData
	 */
	public function translateLangTable($tbName,$idName,$felder,$langData,$felderMitLang=array(),$langIdName="lang_id",$show=false)
	{
		$xsql = array();
		$xsql['dbname'] = $tbName;
		$xsql['select_felder'] = array("*");
		$xsql['where_data'] = array($langIdName => 1);
		$Items = $this->db_abs->select($xsql);
		//print_r($Items);exit();

		foreach($Items as $item)
		{
			$transData = array();
			foreach($felder as $feld)
			{
				$transData[] = $item[$feld];
			}
			//print_r($transData);

			$transText = $this->transDeeplNow->translateArray($langData['0'],$transData);
			//print_r($transText);exit();

			//alten Eintrag raus
			$xsql=array();
			$xsql['dbname']         = $tbName;
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " ".$idName."='".$item[$idName]."' AND ".$langIdName."='".$langData['1']."'";
			$this->db_abs->delete( $xsql );

			//Daten für neuen Eintrag
			foreach($item as $feld=>$feldValue)
			{
				$this->checked->$feld=$feldValue;
			}
			//print_r($transData);
			//print_r($felderMitLang);
			//translated Data
			foreach ($felder as $k=>$feld)
			{
				if(array_key_exists($feld,$felderMitLang))
				{
					$this->checked->$feld="/".$langData['0'].$transText['trans_text']['translations'][$k]['text'];
				}
				else{
					$this->checked->$feld=$transText['trans_text']['translations'][$k]['text'];
				}

			}
			//set target langid
			$this->checked->$langIdName=$langData['1'];

			//neuen Eintrag rein
			$xsql=array();
			$xsql['dbname'] = $tbName;
			$xsql['must'] = array($idName,$langIdName);
			$this->db_abs->insert( $xsql,$show);
			//exit();
		}
	}

	public function translateLangTableFlex($tbName,$idName,$felder,$langData,$felderMitLang=array(),$langIdName="lang_id",$show=false)
	{
		$xsql = array();
		$xsql['dbname'] = $tbName;
		$xsql['select_felder'] = array("*");
		$xsql['where_data'] = array($idName => "%%");
		$xsql['where_data_like'] = true;
		$Items = $this->db_abs->select($xsql);
		//print_r($Items);exit();

		foreach($Items as $item)
		{
			//save org data for later use...
			$org = $item;
			// here unset because this are NOT felder
			foreach($felder as $feld)
			{
				unset($item[$feld]);
			}
			$transData = $item;
			$transText = $this->transDeeplNow->translateArray($langData['0'],$transData);

			$tbNameTarget = str_ireplace("search_1","search_".$langData['1'],$tbName);

			//alten Eintrag raus
			$xsql=array();
			$xsql['dbname']         = $tbNameTarget;
			$xsql['limit']          = " LIMIT 1";
			$xsql['del_where_wert'] = " ".$idName."='".$org[$idName]."' ";
			$this->db_abs->delete( $xsql ,1);

			//Daten für neuen Eintrag
			foreach($org as $feld=>$feldValue)
			{
				$this->checked->$feld=$feldValue;
			}
			//translated Data
			$i =0;
			foreach ($item as $k=>$feld)
			{
				$this->checked->$k=$transText['trans_text']['translations'][$i]['text'];
				$i++;
			}

			//set target langid
			$this->checked->$langIdName=$langData['1'];
			//neuen Eintrag rein
			$xsql=array();
			$xsql['dbname'] = $tbNameTarget;
			$xsql['must'] = array($idName);
			$this->db_abs->insert( $xsql,$show);
		}
	}




	public function translate_freie_module()
	{
		/**
		return false;
		print_r("hier");
		$sql = sprintf("SELECT * FROM %s
		WHERE freiemodule_lang='de' ",
		DB_PRAEFIX."papoo_freiemodule_daten"
		);
		$result = $this->db->get_results($sql,ARRAY_A);
		//print_r($result);
		$active_lng_data = $this->get_active_lngs();

		#print_r($result);
		$trans_frei_content = "";

		foreach ($active_lng_data as $lk=>$lv)
		{
		if (is_array($result))
		{
		foreach ($result as $k=>$v)
		{
		print_r(($v['freiemodule_code']));
		$trans_frei_content = $this->transDeeplNow->translate($lv['lang_short'],$v['freiemodule_code']);
		print_r(($trans_frei_content));
		//exit();
		}
		}
		}

		print_r("<p>Freie Module fertig </p>");
		return true;
		 * **/
	}

	public function get_active_lngs()
	{
		$sql = sprintf("SELECT * FROM %s WHERE lang_short<>'de'",
			DB_PRAEFIX. "papoo_name_language"
		);
		return $this->db->get_results($sql, ARRAY_A);
	}
	/**
	 * @return transDeeplNow
	 */
	public function getTransDeeplNow()
	{
		$this->setDeeplUrl();
		return $this->transDeeplNow;
	}



}
?>
