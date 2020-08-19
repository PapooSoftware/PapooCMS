<?php

/**
 * Class blogger_trackback_class
 */
class blogger_trackback_class {
	private $checked;
	private $cms;
	private $content;
	private $db;
	private $db_abs;
	private $menu;
	private $user;
	private $urls = array(
		'trackback' => array(),
		'pingback' => array()
	);

	/**
	 * blogger_trackback_class constructor.
	 */
	public function __construct()
	{
		global $content, $db, $checked, $user, $cms, $menu, $db_abs;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;
		$this->menu = &$menu;
		$this->db_abs = &$db_abs;

		/**
		 * Backend - Admin Dinge durchführen
		 */

		if (defined("admin")) {
			$this->user->check_intern();
			global $template;
			$this->get_config();

			$template2 = str_ireplace( PAPOO_ABS_PFAD . "/plugins/", "", $template );
			$template2 = basename( $template2 );

			if ($template != "login.utf8.html") {

				IfNotSetNull($this->checked->submenu);

				$this->content->template['submenu'] = $this->checked->submenu;
				//$this->get_fb_data();
				if ($template2 == "backend.html" && $this->checked->submenu == 'trackback' && isset($this->checked->save_config))
					$this->save_config();

				//Nur ausführen wenn Artikel gespeichert wird egal ob ändern oder neu
				if (isset($this->checked->inhalt_ar['Submit1']) || isset($this->checked->inhalt_ar['Submit_zwischen'])) {
					$this->ping();
					$this->trackback();
				}
			}
		}
		else {
			//Trackback INI
			$this->receive_trackbacks();
		}
	}

	/**
	 * blogger_class::create_trackbacks()
	 *
	 * @return void
	 */
	public function trackback()
	{
		//Checken ob die URL schon mal gepingt wurde aus diesem Artikel / Produkt / Eintrag
		$my_url = $this->create_url();
		if (!preg_match_all('/\<a.*href=\"(.*)\".*\>.*\<\/a\>/Usm', $this->checked->inhalt_ar['inhalt'], $remote_urls))
			return;
		$this->urls['trackback'] = $result[1];
		foreach ($remote_urls[1] as $remote_url) {
			///Speichern in  TB Links Tabelle
			$this->save_trackback($my_url, $remote_url);
		}
	}

	/**
	 * @param $my_url
	 * @param $remote_url
	 * @return bool
	 */
	public function get_trackback($my_url, $remote_url)
	{
		$sql = sprintf("SELECT * FROM %splugin_blogger_trackbacks
                WHERE `blogger_plugin_tb_my_url` = '%s'
                    AND `blogger_plugin_tb_remote_url` = '%s'
                    LIMIT 1",
			DB_PRAEFIX,
			$this->db->escape($my_url),
			$this->db->escape($remote_url)
		);

		$result = $this->db->get_results($sql, ARRAY_A);
		if (empty($result))
			return false;
		return $result[0];
	}

	/**
	 * @return array|void
	 */
	public function get_trackbacks()
	{
		$sql = sprintf("SELECT * FROM %splugin_blogger_trackbacks
                WHERE `blogger_plugin_tb_my_url` = '%s'",
			DB_PRAEFIX,
			$this->create_url()
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * @param $my_url
	 * @param $remote_url
	 */
	public function save_trackback($my_url, $remote_url)
	{
		$sql = sprintf("INSERT INTO %splugin_blogger_trackbacks VALUES(
                    '',
                    '%s',
                    '%s',
                    '1'
                )",
			DB_PRAEFIX,
			$this->db->escape($my_url),
			$this->db->escape($remote_url)
		);
		$result = $this->db->query($sql);
	}

	/**
	 * @param $my_url
	 * @param $remote_url
	 */
	public function send_trackback($my_url, $remote_url)
	{
		// $headers = array(
		//        "Content-type: application/json; charset=\"utf-8\"",
		//     "Cache-Control: no-cache, must-revalidate",
		//     "Pragma: no-cache",
		// );
		// $options = array(
		//     CURLOPT_URL => $remote_url . '/trackback/',
		//     CURLOPT_HTTPHEADER => $headers,
		//     CURLOPT_HEADER => false,
		//     CURLOPT_HTTPGET => true,
		//     CURLOPT_FRESH_CONNECT => true,
		//     CURLOPT_RETURNTRANSFER => true,
		//     CURLOPT_FOLLOWLOCATION => true,
		//     CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
		// );
		// $ch = curl_init();
		// curl_setopt_array($ch, $options);
		// $result = curl_exec($ch);
		// exit;
	}

	function post_papoo() {

	}

	/**
	 * blogger_class::receive_trackbacks()
	 *
	 * @return void
	 */
	public function receive_trackbacks()
	{
		$trackback_url = urldecode($_SERVER['REQUEST_URI']);
		if (stristr($trackback_url,"/trackback/")) {
			$trackback_url = str_replace("/trackback/","",$trackback_url);
			if (!empty($this->checked->id)) {
				$tb_id = $this->checked->id;
			}

			if (!empty($this->checked->url)) {
				$tb_url = $this->checked->url;
			}

			if (!empty($this->checked->title)) {
				$tb_title = $this->checked->title;
			}

			if (!empty($this->checked->exerpt)) {
				$tb_expert = $this->checked->exerpt;
			}

			//Checken ob der Artikel von dieser url schon mal gepingt wurde

			//Wenn nicht dann eintragen als KOmmentar -> Check auf Badwords

			//Wenn eingetragen
			echo "1";
			exit();
			// $this->reload_front($trackback_url);
			// exit($trackback_url);
		}

		//Return xml

		//Frontend Seite neu laden aber diesmal korrekt:
		//$this->reload_front($trackback_url);
	}

	/**
	 * @param $location_url
	 */
	public function reload_front($location_url)
	{
		//selected_kunde
		if ($_SESSION['debug_stopallredirect']) {
			echo '<a href="' . $location_url .
				'">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 * blogger_class::ping()
	 *
	 * @return void
	 */
	public function ping()
	{
		//Ping Klasse einbinden
		require_once(PAPOO_ABS_PFAD."/plugins/blogger/lib/class_ping.php");
		$ping = new ping();

		//Url erstellen
		$url = $this->create_url();

		//Titel
		$titel = $this->create_title();

		//RSS Feed
		$rss = $this->create_rss_feed();

		$ping->ping_now($titel, $url, $rss);

		//Hier fehlt noch die Funktion die das macht
		// TODO: Produkte submitten
	}

	/**
	 * @return string
	 */
	protected function create_rss_feed()
	{
		return "http://" . $this->cms->title_send . PAPOO_WEB_PFAD . "/plugins/rssfeed/feed1.xml";
	}

	/**
	 * blogger_class::create_title()
	 *
	 * @return void
	 */
	protected function create_title() {
		$sql = sprintf("SELECT header FROM %s
                WHERE lan_repore_id='%d'
                AND lang_id='%d'
                LIMIT 1",
			$this->cms->tbname['papoo_language_article'],
			$this->db->escape($this->checked->reporeid),
			$this->cms->lang_back_content_id
		);
		return $this->db->get_var($sql);
	}

	/**
	 * blogger_class::create_url()
	 *
	 * @return string|void
	 */
	protected function create_url()
	{
		/**
		 * Nach den 3 verschiedenen Formaten durchgehen...
		 */

		//1. Fall - keine sprechenden urls
		if (empty($this->cms->mod_free) && empty ($this->cms->mod_surls)) {
			//Menüpunkt
			$sql = sprintf("SELECT lcat_id FROM %s
                    WHERE lart_id='%d'
                    ORDER BY lart_order_id ASC
                    LIMIT 1",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$this->db->escape($this->checked->reporeid)
			);
			$menuid=$this->db->get_var($sql);
			//Artikel ID
			return "http://".$this->cms->title_send.PAPOO_WEB_PFAD."?index.php?menuid=".$menuid."&reporeid=".$this->checked->reporeid;
		}

		//2. Fall - Sprechende Urls
		if (empty($this->cms->mod_free) && !empty ($this->cms->mod_surls)) {
			//Artikel URL
			$sql = sprintf("SELECT url_header FROM %s
                    WHERE lan_repore_id='%d'
                    AND lang_id='%d'
                    LIMIT 1",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($this->checked->reporeid),
				$this->cms->lang_back_content_id
			);
			$url_header=$this->db->get_var($sql);

			//Menüpunkt ID
			$sql = sprintf("SELECT lcat_id FROM %s
                    WHERE lart_id='%d'
                    ORDER BY lart_order_id ASC
                    LIMIT 1",
				$this->cms->tbname['papoo_lookup_art_cat'],
				$this->db->escape($this->checked->reporeid)
			);
			$menuid=$this->db->get_var($sql);

			//Verzeichnis Ebene rausholen
			$this->get_verzeichnis($menuid);

			//Umdrehen und durchloopen
			if (!is_array($this->m_url)) {
				return;
			}
			$this->m_url = array_reverse($this->m_url);
			$link="";
			foreach ($this->m_url as $dat) {
				$link.=$dat."/";
			}

			//Artikel ID
			return "http://".$this->cms->title_send.PAPOO_WEB_PFAD."/".$link.$url_header.".html";
		}

		//3. Fall - Free Urls
		if (!empty($this->cms->mod_free) && empty ($this->cms->mod_surls)) {
			//Artikel URL
			$sql = sprintf("SELECT url_header FROM %s
                    WHERE lan_repore_id='%d'
                    AND lang_id='%d'
                    LIMIT 1",
				$this->cms->tbname['papoo_language_article'],
				$this->db->escape($this->checked->reporeid),
				$this->cms->lang_back_content_id
			);
			$url_header=$this->db->get_var($sql);
			//Artikel ID
			return "http://".$this->cms->title_send.PAPOO_WEB_PFAD."".$url_header;
		}
	}

	/**
	 * blogger_class::get_verzeichnis()
	 *
	 * @param string $menid
	 * @return mixed|void
	 */
	protected function get_verzeichnis($menid="")
	{
		if (!empty($menid)) {
			$this->menu->data_front_complete = $this->menu->menu_data_read("FRONT");
			$mendata=$this->menu->data_front_complete;
			foreach ($mendata as $menuitems) {
				//aktuelle menid finden
				if ($menid==$menuitems['menuid']) {
					$this->m_url[]=($menuitems['url_menuname']);
					//Nicht oberste Ebene, neu aufrufen
					if ($menuitems['untermenuzu']!=0) {
						$this->get_verzeichnis($menuitems['untermenuzu']);
					}
					else {
						return $true;
					}
				}
			}
		}
	}

	protected function get_config()
	{
		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['plugin_blogger_trackback_config']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->content->template['blogger_plugin'] = $result;
	}

	/**
	 * blogger_class::save_config()
	 *
	 * @return void
	 */
	protected function save_config()
	{
		if (empty($this->checked->blogger_plugin_ping_service_aktivieren)) {
			$this->checked->blogger_plugin_ping_service_aktivieren=0;
		}
		if (empty($this->checked->blogger_plugin_trackback_aktivieren)) {
			$this->checked->blogger_plugin_trackback_aktivieren = 0;
		}
		if (empty($this->checked->blogger_plugin_mail_an_admin_wenn_neuer_tb_erstellt_wurde)) {
			$this->checked->blogger_plugin_mail_an_admin_wenn_neuer_tb_erstellt_wurde = 0;
		}

		//Daten speichern
		$xsql['dbname'] = "plugin_blogger_trackback_config";
		$xsql['praefix'] = "blogger_plugin";
		$xsql['where_name'] = "blogger_plugin_id";

		$this->checked->blogger_plugin_id = 1;
		$cat_dat = $this->db_abs->update($xsql);

		$this->get_config();
	}

	/**
	 * @return bool
	 */
	function output_filter()
	{
		//Trackback url im Template angeben (<link rel="pingback" href="http://www.webseite.xy/xmlrpc.php" />)
		//$my_url = $this->create_url();
		return false;

		$trackbacks = array();
		if (is_array($this->get_trackbacks())) {
			foreach($this->get_trackbacks() as $url) {
				$trackbacks[] = '<link rel="pingback" href="http://' . $url['blogger_plugin_tb_remote_url'] . '" />\n';
			}
		}

		global $output;
		$output = str_replace("</head>", implode('', $trackbacks) . "\n</head>", $output);
	}
}

$blogger_trackback = new blogger_trackback_class;
