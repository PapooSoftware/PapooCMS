<?php

/**
 * Hauptdatei für das deepltrans-Plugin.
 *
 * @author Carsten Euwens <info@papoo.de>
 */

/**
 * class deepltrans_class
 *
 * Hauptklasse des Plugins.
 *
 * @author Carsten Euwens <info@papoo.de>
 */
class deepltrans_class
{
	const PREFIX = 'deepltrans';

	/**
	 * deepltrans_class constructor.
	 */
	function __construct()
	{
		global $user, $db_abs, $db, $db_praefix, $checked, $content;
		$this->user = &$user;
		$this->db_abs = &$db_abs;
		$this->db = &$db;
		$this->db_praefix = &$db_praefix;
		$this->checked = &$checked;
		$this->content = &$content;

		if(defined('admin')) {
			$this->user->check_intern();
			global $template;
			$template2 = str_ireplace( PAPOO_ABS_PFAD . '/plugins/', '', $template );
			$template2 = basename( $template2 );

			//Deepl Klasse importieren
			require_once PAPOO_ABS_PFAD."/plugins/deepltrans/lib/transdeepl.php";
			$transdeepl = new transdeepl();

			if ( $template != 'login.utf8.html' ) {
				$this->content->template['plugin_creator_is_dev'] = true;
				if(stristr($template2, "deepltrans") ){
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/deepltrans/css';

					//$this->rapid_dev();

					// TODO: Backend logic hierhin
				}

				//hier werden die Einstellungen durchgeführt.
				if(stristr($template2, "setdeepl_backend"))
				{


					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/deepltrans/css';
					$this->make_settings();
					//$this->rapid_dev();
					//error_reporting(E_ALL);
					// TODO: Backend logic hierhin

				}

				if(stristr($template2, "content_bersetzungen_backend"))
				{
					require_once __DIR__."/transdeepl.php";
					require_once __DIR__."/translate_content.php";
					$translateContent = new translate_content();

					$translateContent->translateAll();

					$this->setLangFiles("content");

				}

				//hier werden die Übersetzungen durchgeführt.
				if(stristr($template2, "bersetzungen") && !stristr($template2, "content_bersetzungen_backend"))
				{
					// Pfad zum css Ordner zur Einbindung des backend css
					$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/deepltrans/css';

					$this->make_trans();
					//$this->rapid_dev();

				}
			}
		}
		else {}
	}

	/**
	 * @param string $content
	 */
	public function setLangFiles($content="")
	{
		$this->content->template['translink']="plugin.php?menuid=".$this->checked->menuid."&template=deepltrans/templates/bersetzungen_backend.html";
		$sql = sprintf("SELECT * FROM %s",
								DB_PRAEFIX.'papoo_name_language');
		//print_r($sql);
		$result = $this->db->get_results($sql,ARRAY_A);

		$log = $this->getLogData();


		//Base entry is german
		$translationState = $this->getState();
		//print_r($translationState);

		//Translation checkn
		foreach($result as $k => $lang)
		{
			if($lang['lang_short']=="de")
			{
				unset($result[$k]);
				continue;
			}
			$result[$k]['lastTime']=$log[$lang['lang_short']];
			if(empty($content))
			{
				$result[$k]['mainBackend']=$translationState['backend']['main'][$lang['lang_short']]['prozent'];
				$result[$k]['mainFrontend']=$translationState['frontend']['main'][$lang['lang_short']]['prozent'];
				$result[$k]['pluginBackend']=$translationState['backend']['plugin'][$lang['lang_short']]['prozent'];
				$result[$k]['pluginFrontend']=$translationState['frontend']['plugin'][$lang['lang_short']]['prozent'];
			}
		}
		//print_r($result);
		//languages
		$this->content->template['languages'] = $result;
		//exit();
	}

	/**
	 * @param string $lang
	 * @return bool
	 */
	public function setLogData($lang="en")
	{
		$log = $this->getLogData();
		$log[$lang]	= date("d.m.Y H:i:s");
		file_put_contents(PAPOO_ABS_PFAD."/dokumente/logs/translateContent.log",json_encode($log));
		return true;
	}

	/**
	 * @return mixed
	 */
	public function getLogData()
	{
		touch(PAPOO_ABS_PFAD."/dokumente/logs/translateContent.log");
		$data = file_get_contents(PAPOO_ABS_PFAD."/dokumente/logs/translateContent.log");
		return json_decode($data,true);
	}

	/**
	 *
	 * Get State of File Translations...
	 * @return mixed
	 */
	public function getState()
	{
		//ini_set("display_errors", true);
		//error_reporting(E_ALL);
		$dir=(str_ireplace("lib","api",__DIR__));
		$apiKey = md5($dir);
		$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/".PAPOO_WEB_PFAD."/plugins/deepltrans/api/tpapoo.php?compare=true&apiKey=".$apiKey;
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_exec($curl);

		return json_decode(file_get_contents(PAPOO_ABS_PFAD."/interna/templates_c/trans.txt"),true);
	}

	/**
	 * @return mixed
	 */
	public function translateNow()
	{
		$deeplKey = $this->get_key();
		$transData = urlencode(json_encode(array("lang"=>$this->checked->lang,"backfront"=>$this->checked->what,"pluginOrMain"=>$this->checked->trans,"deeplKey"=>$deeplKey)));
		$dir=(str_ireplace("lib","api",__DIR__));
		$apiKey = md5($dir);
		$url = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST']."/".PAPOO_WEB_PFAD."/plugins/deepltrans/api/tpapoo.php?translate=true&apiKey=".$apiKey."&transData=".$transData;

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl, CURLOPT_TIMEOUT, 2);
		$returndata = curl_exec($curl);

		return json_decode($returndata,true);
	}

	/**
	 * @return bool
	 */
	private function make_trans(){

		if(!empty($this->checked->trans))
		{
			$this->translateNow();
		}

		//verfügbare Sprachen
		$this->setLangFiles();
		return true;
	}


	/**
	 * Einen Eintrag rausholen
	 */
	public function get_key()
	{
		$xsql = array();
		$xsql['dbname'] = "plugin_deepltrans_einstellun_form";
		$xsql['select_felder'] = array("*");
		$xsql['limit'] = " LIMIT 1";
		$xsql['where_data'] = array("deepltrans_id" => 1);
		$result = $this->db_abs->select($xsql);
		//print_r($result);
		$this->content->template['deepltrans'] = $result;
		return $result['0']['deepltrans_deepl_key_input'];
	}

	/**
	 * @return bool
	 */
	public function make_settings()
	{
		//Daten eines Eintrages
		$this->get_key();

		//Nicht abgeschickt, dann nix machen
		if (empty($this->checked->plugin_deepltrans_form_submit)) {
			return false;
		}
		$xsql=array();
		$xsql['dbname']     = "plugin_deepltrans_einstellun_form";
		$xsql['praefix']    = "deepltrans_";
		$xsql['must']       = array( 	"deepltrans_deepl_key_input");
		$xsql['where_name']     = "deepltrans_id";
		$this->checked->deepltrans_id= 1;
		$cat_dat = $this->db_abs->update( $xsql );
		$insertid=$cat_dat['insert_id'];
		//exit();
		if (is_numeric($insertid)) {
			$this->reload($insertid);
		}
	}

	/**
	 * Seite mit der ID neu laden damit die Daten geladen werden können
	 *
	 * @param string $pnavid
	 */
	private function reload($pnavid="")
	{
		$location_url = "plugin.php?menuid=".$this->checked->menuid."&template=".$this->checked->template."&eingetragen=ok&deepltrans_id=".$pnavid;
		if ($_SESSION['debug_stopallvar']) {
			echo '<a href="' . $location_url . '">' .
				$this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header("Location: $location_url");
		}
		exit;
	}

	/**
	 * Funktion die vor dem Senden des Seiteninhalts für jedes Plugin aufgerufen wird,
	 * bei der dann per global $output und z.B. einem regulären Ausdruck der Inhalt
	 * verändert werden kann.
	 * (Backend)
	 */
	public function output_filter_admin()
	{
		#global $output;
	}

	/**
	 * Funktion die vor dem Senden des Seiteninhalts für jedes Plugin aufgerufen wird,
	 * bei der dann per global $output und z.B. einem regulären Ausdruck der Inhalt
	 * verändert werden kann.
	 * (Frontend)
	 */
	public function output_filter()
	{
		#global $output;
	}
}

$deepltrans = new deepltrans_class();
?>
