<?php

/**
 * Class blogger_shortcut_class
 */
class blogger_shortcut_class {
	private $artikel;
	private $checked;
	private $cms;
	private $content;
	private $db;
	private $db_abs;
	private $menu;
	private $stopwords;

	private $data = array(
		'config' => array(),
		'menu' => array()
	);

	/**
	 * blogger_shortcut_class constructor.
	 */
	public function __construct()
	{
		global $artikel, $checked, $cms, $content, $db, $db_abs, $menu, $weiter;
		require_once('stopwords.php');
		$this->stopwords = $stopwords['de'];

		$this->artikel = $artikel;
		$this->checked = $checked;
		$this->cms = &$cms;
		$this->content = $content;
		$this->db = &$db;
		$this->db_abs = &$db_abs;
		$this->menu = $menu;
		$this->weiter = $weiter;

		$this->get_config();

		//Admin Dinge realisieren
		if (defined("admin")) {
			$this->check_artikel();

			IfNotSetNull($checked->submenu);

			if ($checked->submenu == 'shortcut') {
				$this->config();
			}
		}
		//Frontend
		else {
			$this->process();
		}
		$this->show();

		IfNotSetNull($this->content->template['blogger_message']);
	}

	/**
	 * @return bool|void
	 */
	public function check_artikel()
	{
		//Wenn Artikel speichern
		if (!empty($this->checked->inhalt_ar)) {
			$this->get_config();

			if (!is_array($this->menuids)) {
				return false;
			}
			foreach ($this->menuids as $k=>$v) {
				if (isset($this->checked->inhalt_ar['cattext_ar']) && is_array($this->checked->inhalt_ar['cattext_ar'])) {
					foreach ($this->checked->inhalt_ar['cattext_ar'] as $k1=>$v1) {
						if (in_array($v1,$v)) {
							$is_blog="ok";
						}
					}
				}
			}
			//Checken ob für Blog
			if (!isset($is_blog) || isset($is_blog) && $is_blog!="ok") {
				return false;
			}
			//Dann Inhalt hinzufügen...
			$this->create_wordlist($this->checked->inhalt_ar);
		}
	}

	/**
	 * Standard post_papoo - läuft nach dem Kern und den anderen Plugins
	 */
	public function post_papoo()
	{
		if(!isset($this->checked->widget)) {
			$this->checked->widget = NULL;
		}

		if ($this->checked->widget == 'wordcloud') {
			//Teaserdaten in das Standard table_data Array zuweisen... dann wird das ganz normal ausgegeben...
			$this->content->template['table_data']=$this->get_teaser();
		}

		if ($this->checked->widget == 'blogcal') {
			//Teaserdaten in das Standard table_data Array zuweisen... dann wird das ganz normal ausgegeben...
			$this->content->template['table_data']=$this->get_teaser_cal();
		}

		if ($this->checked->widget == 'blogmonth') {
			//Teaserdaten in das Standard table_data Array zuweisen... dann wird das ganz normal ausgegeben...
			$this->content->template['table_data']=$this->get_teaser_month();
		}
	}

	/**
	 * Standard output_filter  - läuft wenn alles fertig ist und manipuliert das HTML...
	 * Das ist hier aber nicht notwendig...
	 */
	public function output_filter()
	{
		/**
		if ($this->checked->widget == 'wordcloud') {
		global $output;
		preg_match("/\<\!-- \#\#\# start_of_content --\>(.*)\<\!-- \#\#\# end_of_content --\>/sm", $output, $match);
		$output = str_replace($match[1], $this->get_teaser(), $output);
		}
		 * */
	}

	/**
	 * @param $word
	 * @return mixed
	 * Bereinigt die Wörter um Umlaute...
	 */
	public function clean_word($word)
	{
		$word = preg_replace("/[^a-z0-9]/", "", $word);
		$word=(trim($word));
		$word=(urlencode($word));
		$word=(trim($word));
		$pattern = '/[^\w_-äöüÄÖÜß0-9]/';
		return preg_replace($pattern, '', $word);
	}

	/**
	 * Entfernt Wörter die Zahlen sind bzw. keine Buchstaben
	 *
	 * @param $word
	 * @param array $ausschulss
	 * @return bool
	 */
	public function filter_words($word,$ausschulss=array())
	{
		// Zahlen blocken
		if (is_numeric($word))
			return false;

		// zu kurze Wörter blocken
		if (strlen($word)<=5)
			return false;

		if (in_array($word,$ausschulss))
			return false;

		// Wörter aus der Blacklist blocken
		if (in_array($word,$this->stopwords))
			return false;

		return true;
	}

	private function set_ausschluss()
	{
		$this->ausschluss=array("also","alles","aller","ihre","jeder");
	}

	/**
	 * @param $a
	 * @param $b
	 * @return int
	 * Sortiert nach Alphabet..
	 */
	public static function sort_words($a, $b)
	{
		if ($a['count'] == $b['count']) {
			if ($a['word'] > $b['word'])
				return  ($a['word'] > $b['word']) ? 1 : -1;
			return 0;
		}
		return  ($a['count'] < $b['count']) ? 1000 : -1000;
	}

	/**
	 * Konfiguration steuern
	 */
	private function config()
	{
		//Alle Menueinträge rausholen
		$this->menu_entries();

		//Speichern
		if (!empty($this->checked->save_config) || !empty($this->checked->save_config_data))
			$this->save_config();

		//ODer Wortliste generieren
		elseif (!empty($this->checked->create_wordlist))
			$this->create_wordlist();
	}

	/**
	 * Holt die Konfigdaten aus der DB
	 */
	private function get_config()
	{
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['plugin_blogger_shortcuts']);
		$result = $this->db->get_results($sql, ARRAY_A);

		if (!$result) {
			return;
		}

		//Zuordnung der Menuids
		foreach ($result as $entry){
			$this->data['config']['shortcut'][$entry['blogger_shortcuts_menuid']] = $entry;
			$this->menuids[]=$entry;
		}

		//Zuordnung der Anzahl...
		$sql = sprintf("SELECT * FROM %s", $this->cms->tbname['plugin_blogger_shortcut_config']);
		$result = $this->db->get_results($sql, ARRAY_A);
		if (!$result) {
			return;
		}

		foreach ($result as $key=>$entry) {
			$this->data['config'][$entry['blogger_shortcut_config_name']] = $entry['blogger_shortcut_config_value'];
			$this->data['config']['blogger__feldtypen_fuer_schlagworte'] = unserialize($entry['blogger__feldtypen_fuer_schlagworte']);
		}

		$this->content->template['blogger_config']=$this->data['config'];
	}

	/**
	 * Speichert die Konfigdaten in der DB
	 */
	private function save_config()
	{
		IfNotSetNull($this->checked->save_config_data);

		if (isset($this->checked->save_config) && $this->checked->save_config) {
			// vorschlag vom christoph checken
			$sql = sprintf("TRUNCATE %s", $this->cms->tbname['plugin_blogger_shortcuts']);
			$result = $this->db->query($sql);

			if (is_array($this->checked->blogger_menu)) {
				foreach ($this->checked->blogger_menu as $key => $value) {
					if (empty($this->checked->blogger_calendar[$key]) && empty($this->checked->blogger_wordcloud[$key]) &&
						empty($this->checked->blogger_month[$key])) {
						continue;
					}

					$sql = sprintf("INSERT INTO `%s` VALUES(
                        '',
                        '%d',
                        '%d',
                        '%d',
                        '%d'
                    )",
						$this->cms->tbname['plugin_blogger_shortcuts'],
						$key,
						(int) !empty($this->checked->blogger_calendar[$key]),
						(int) !empty($this->checked->blogger_wordcloud[$key]),
						(int) !empty($this->checked->blogger_month[$key])
					);
					$result = $this->db->query($sql);
				}
			}
		}

		if ($this->checked->save_config_data) {
			$sql = sprintf("TRUNCATE %s", $this->cms->tbname['plugin_blogger_shortcut_config']);
			$this->db->query($sql);

			$this->checked->plugin_blogger__feldtypen_fuer_schlagworte=serialize($this->checked->plugin_blogger__feldtypen_fuer_schlagworte);

			if (is_array($this->checked->blogger_config)) {
				foreach ($this->checked->blogger_config as $key => $value) {
					$sql = sprintf("INSERT INTO `%s` VALUES(
                        '',
                        '%s',
                        '%s',
                        '%s'
                    )",
						$this->cms->tbname['plugin_blogger_shortcut_config'],
						$this->db->escape($key),
						$this->db->escape($value),
						$this->db->escape($this->checked->plugin_blogger__feldtypen_fuer_schlagworte)
					);
					$result = $this->db->query($sql);
				}
			}
		}


		$this->data['message'] = 'Die Konfiguration wurde gespeichern. Bitte generieren Sie eine neue Wörterliste!';
		$this->get_config();
	}

	/**
	 * Menüeinträge rausholen
	 */
	private function menu_entries()
	{
		$this->data['menu'] = $this->menu->menu_data_read('FRONT');
		$this->content->template['blogger_menu']=$this->data['menu'];
	}

	/**
	 * @param string $text
	 * @return mixed|string
	 * Text von überfküssigen Zeichen säubern...
	 */
	private function clean_input($text="")
	{
		$text=trim($text);
		$text=str_replace("\n"," ",$text);
		$text=str_replace("\r"," ",$text);
		$text=str_replace("\t"," ",$text);
		$text=str_ireplace('>',"> ",$text);
		$text=str_ireplace('.'," ",$text);
		$text=str_ireplace(','," ",$text);
		$text=str_ireplace(':'," ",$text);
		$text=str_ireplace(';'," ",$text);
		$text=str_ireplace('!'," ",$text);
		$text=str_ireplace('?'," ",$text);

		$text=strip_tags($text);
		$text=stripslashes($text);
		$text=htmlentities($text);
		$text=str_ireplace('&nbsp;'," ",$text);
		$text=html_entity_decode($text);
		if (function_exists("mb_strtolower")) {
			$text=mb_strtolower($text,"UTF-8");
		}

		$text=str_ireplace(utf8_encode("ö"),"oe",$text);
		$text=str_ireplace(utf8_encode("ä"),"ae",$text);
		$text=str_ireplace(utf8_encode("ü"),"ue",$text);
		$text=str_ireplace(utf8_encode("ß"),"ss",$text);

		$text=str_ireplace(("ö"),"oe",$text);
		$text=str_ireplace(("ä"),"ae",$text);
		$text=str_ireplace(("ü"),"ue",$text);
		$text=str_ireplace(("ß"),"ss",$text);

		return $text;
	}

	/**
	 * Liste der Wörter erstellen
	 *
	 * @param array $inhalts_array
	 * @return bool|void
	 */
	private function create_wordlist($inhalts_array=array())
	{
		//Ausschuss setzen
		$this->set_ausschluss();

		$list = array();

		$reporeid=$this->checked->reporeid;
		$this->checked->reporeid="";


		if (!is_array($this->data['config']['shortcut'])) {
			return false;
		}

		//Für jeden gewählten Menüeintrag durchgehen
		foreach ($this->data['config']['shortcut'] as $entry) {
			//Artikeldaten holen
			$posts = $this->artikel->get_artikel($entry['blogger_shortcuts_menuid']);

			if (!is_array($posts)) {
				continue;
			}
			//Alle posts durchlaufen
			foreach ($posts as $post) {
				$text="";

				if (isset($this->data['config']['blogger__feldtypen_fuer_schlagworte']['1']) && $this->data['config']['blogger__feldtypen_fuer_schlagworte']['1']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($post->header));
				}
				if (isset($this->data['config']['blogger__feldtypen_fuer_schlagworte']['2']) && $this->data['config']['blogger__feldtypen_fuer_schlagworte']['2']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($post->lan_article));
				}
				if (isset($this->data['config']['blogger__feldtypen_fuer_schlagworte']['3']) && $this->data['config']['blogger__feldtypen_fuer_schlagworte']['3']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($post->lan_teaser));
				}
				if (isset($this->data['config']['blogger__feldtypen_fuer_schlagworte']['4']) && $this->data['config']['blogger__feldtypen_fuer_schlagworte']['4']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($post->lan_metadescrip));
				}
				if (isset($this->data['config']['blogger__feldtypen_fuer_schlagworte']['5']) && $this->data['config']['blogger__feldtypen_fuer_schlagworte']['5']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($post->lan_metakey));
				}

				//Anhand Leerzeichen exploden
				$words = explode(" ", $text);

				if (!is_array($words)) {
					return false;
				}
				ini_set("display_errors", "Off");

				foreach (array_filter($words, "blogger_shortcut_class::filter_words") as $word) {
					$list[] = array($this->clean_word(trim($word)), $post->reporeID);
				}
				ini_set(display_errors, "On");
			}
			if (!empty($inhalts_array)) {
				$text="";

				IfNotSetNull($inhalts_array['uberschrift']);
				IfNotSetNull($inhalts_array['inhalt']);
				IfNotSetNull($inhalts_array['teaser']);
				IfNotSetNull($inhalts_array['metadescrip']);
				IfNotSetNull($inhalts_array['metakey']);

				//blogger__feldtypen_fuer_schlagworte
				if ($this->data['config']['blogger__feldtypen_fuer_schlagworte']['1']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($inhalts_array['uberschrift']));
				}
				if ($this->data['config']['blogger__feldtypen_fuer_schlagworte']['2']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($inhalts_array['inhalt']));
				}
				if ($this->data['config']['blogger__feldtypen_fuer_schlagworte']['3']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($inhalts_array['teaser']));
				}
				if ($this->data['config']['blogger__feldtypen_fuer_schlagworte']['4']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($inhalts_array['metadescrip']));
				}
				if ($this->data['config']['blogger__feldtypen_fuer_schlagworte']['5']==1) {
					//HTML usw entfernen
					$text.=" ".$this->clean_input(($inhalts_array['metakey']));
				}
				//Anhand Leerzeichen exploden
				$words = explode(" ", $text);

				ini_set("display_errors", "Off");
				foreach (array_filter($words, "blogger_shortcut_class::filter_words") as $word) {
					$list[] = array($this->clean_word(trim($word)), $post->reporeID);
				}
				ini_set("display_errors", "On");
			}
		}

		$result = array();
		foreach ($list as $word) {
			// bereingung auskommentiert: && !in_array($word[1], $result[$word[0]]['ids']) sonst zählt er immer nur 1x... das ist ja quatsch...
			if (array_key_exists($word[0], $result) ) {
				$result[$word[0]]['count']++;
				if (!in_array($word[1], $result[$word[0]]['ids'])) {
					$result[$word[0]]['ids'][] = $word[1];
				}
			}
			else {
				$result[$word[0]] = array(
					'word' => $word[0],
					'count' => 1,
					'ids' => array(
						$word[1]
					)
				);
			}
		}
		usort($result, "blogger_shortcut_class::sort_words");

		$result_slice=array_slice($result, 0,$this->data['config']['wordcloud_count']);
		$this->save_wordlist($result_slice);

		$this->checked->reporeid=$reporeid;
	}

	/**
	 * @return bool|void
	 */
	public function get_wordlist()
	{
		// limit setzen (config dafür anpassen)

		//Keine Menuids - dann raus
		if (!is_array($this->menuids)) {
			return false;
		}

		// Hier die Einträge der gewählten Menuids bestimmen
		$lcatid=" ";
		foreach ($this->menuids as $k=>$v) {
			$lcatid.=" OR lcat_id='".$v['blogger_shortcuts_menuid']."' ";
		}

		//Daten rausholen
		$sql = sprintf("SELECT * FROM `%s`
            LEFT JOIN `%s` ON
                blogger_word_article_lookup_word_id = blogger_wordlist_id
            INNER JOIN `%s` ON
                `lart_id` = `blogger_word_article_lookup_lan_repore_id`
                %s
            GROUP BY `blogger_wordlist_id`
            ORDER BY `blogger_wordlist_count` DESC, `blogger_wordlist_word` ASC
            LIMIT %d",
			$this->cms->tbname['plugin_blogger_wordlist'],
			$this->cms->tbname['plugin_blogger_word_article_lookup'],
			$this->cms->tbname['papoo_lookup_art_cat'],
			//$this->checked->menuid != 1 ? 'WHERE  `lcat_id` = \'' . $this->checked->menuid . '\'' : '',
			'WHERE lcat_id='."'xyz' ".$lcatid,

			$this->data['config']['wordcloud_count']
		);
		$data = $this->db->get_results($sql, ARRAY_A);

		if ($this->cms->mod_free=="1") {
			if (is_array($data)) {
				foreach ($data as $k=>$v) {
					$data[$k]['surl']=PAPOO_WEB_PFAD."/tag/".$v['blogger_wordlist_word']."/";
				}
			}
		}
		$this->data['wordlist']=$data;
	}

	/**
	 * @return bool|string
	 * Hier wird der Teaser generiert...
	 */
	public function get_teaser()
	{
		if (!$this->checked->word_id)
			return false;
		$word_id = $this->checked->word_id;
		if (!empty($word_id)) {
			$this->content->template['weiter_anzahl_pages']="";
			$this->content->template['weiter_array']=array();

			//Anzahl feststellen
			$sql = sprintf("SELECT COUNT(blogger_word_article_lookup_lan_repore_id) FROM `%s`
                LEFT JOIN `%s` ON
                    blogger_word_article_lookup_word_id = blogger_wordlist_id
                WHERE blogger_wordlist_id = '%d'",
				$this->cms->tbname['plugin_blogger_wordlist'],
				$this->cms->tbname['plugin_blogger_word_article_lookup'],
				$word_id

			);
			$result_count = $this->db->get_var($sql);
			$this->weiter->result_anzahl=$result_count;
			$this->weiter->make_limit($this->cms->config_paginierung);
			if ($this->cms->mod_free!="ok") {
				$this->weiter->weiter_link="./index.php?menuid=".$this->checked->menuid."&word_id=".$this->checked->word_id."&widget=wordcloud";
			}
			$this->weiter->do_weiter("teaser");
		}
		$sql = sprintf("SELECT * FROM `%s`
            LEFT JOIN `%s` ON
                blogger_word_article_lookup_word_id = blogger_wordlist_id
            WHERE blogger_wordlist_id = '%d'
            GROUP BY blogger_word_article_lookup_lan_repore_id
            %s",
			$this->cms->tbname['plugin_blogger_wordlist'],
			$this->cms->tbname['plugin_blogger_word_article_lookup'],
			$word_id,
			$this->weiter->sqllimit
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		//Alle Einträge durchloopen
		foreach($result as $entry) {
			$this->checked->reporeid = $entry['blogger_word_article_lookup_lan_repore_id'];
			#$article = $this->artikel->get_artikel();
			$teaser = $this->artikel->make_teaser();
			IfNotSetNull($teaser['0']);
			$return[]=$teaser['0'];
		}
		IfNotSetNull($return);
		return $return;
	}

	/**
	 * @param $list
	 * Wordliste speichern für die Wolke
	 */
	private function save_wordlist($list)
	{
		//Alte Einträge löschen
		$sql = sprintf("DELETE FROM `%s`", $this->cms->tbname['plugin_blogger_wordlist']);
		$this->db->query($sql);

		$sql = sprintf("DELETE FROM `%s`", $this->cms->tbname['plugin_blogger_word_article_lookup']);
		$this->db->query($sql);

		//Durchgehen
		foreach($list as $entry) {
			$sql = sprintf("INSERT INTO `%s` VALUES('', '%s', '%d')",
				$this->cms->tbname['plugin_blogger_wordlist'],
				$entry['word'],
				$entry['count']);
			$this->db->query($sql);
			$id = $this->db->insert_id;

			//Word in der Lookup Tabelle speichern für Aufruf des Artikels...
			foreach ($entry['ids'] as $repore_id) {
				$sql = sprintf("INSERT INTO `%s` VALUES ('', '%d', '%d')",
					$this->cms->tbname['plugin_blogger_word_article_lookup'],
					$id,
					$repore_id
				);
				$this->db->query($sql);
			}
		}
		$this->data['message'] = 'Eine neue Wörterliste wurde generiert und gespeichert!';
	}

	/**
	 * @param $list
	 * @return mixed
	 * Gewichtung der der einzelnen Wörter festelegen
	 */
	private function weight_wordlist($list)
	{
		if (!is_array($list)) {
			return false;
		}

		//Sortieren
		usort($list, "blogger_shortcut_class::sort_words_by_count");
		$weight = 5;
		$result = array();

		$max = $list[0]['blogger_wordlist_count'];
		$last=end($list);
		$min = $last['blogger_wordlist_count']; //['blogger_wordlist_count']

		$f = ($max - $min) / 5;
		foreach($list as $key => $entry) {
			if ($key == 0) {
				$list[$key]['weight'] = $weight;
				$last = $entry['blogger_wordlist_count'];
				continue;
			}
			$weight = ($entry['blogger_wordlist_count'] / $f) % 5;
			$last = $entry['blogger_wordlist_count'];
			$list[$key]['weight'] = $weight;
		}
		usort($list, "blogger_shortcut_class::sort_words_by_char");
		return $list;
	}

	/**
	 * @param $a
	 * @param $b
	 * @return int
	 * Nach anzahl sortieren
	 */
	public static function sort_words_by_count($a, $b)
	{
		return $a['blogger_wordlist_count'] > $b['blogger_wordlist_count'] ? -1  : 1;
	}

	/**
	 * @param $a
	 * @param $b
	 * @return int
	 * Nach ABS sortieren
	 */
	public static function sort_words_by_char($a, $b)
	{
		return $a['blogger_wordlist_word'] > $b['blogger_wordlist_word'] ? 1  : -1;
	}

	/**
	 * Weiche fürs Frontend
	 */
	private function process()
	{
		//Wörter rausholen
		$this->get_wordlist();

		if(!isset($this->cms->module->module_aktiv['mod_blogger_calendar'])) {
			$this->cms->module->module_aktiv['mod_blogger_calendar'] = false;
		}

		if ($this->cms->module->module_aktiv['mod_blogger_calendar'] === true) {
			$this->process_calendar();
		}

		//WOlke Modul - dann anzeigen...
		if ($this->cms->module->module_aktiv['mod_blogger_wordcloud'] ?? '' === true) {
			$this->process_wordcloud();
		}
	}

	private function process_wordcloud()
	{
		$this->data['wordlist'] = $this->weight_wordlist($this->data['wordlist']);
	}

	/**
	 * Daten ans Frontend übergeben
	 *
	 * @return bool|void
	 */
	private function show()
	{
		IfNotSetNull($this->showall);
		//surls?
		if ($this->cms->mod_free=="1") {
			$this->content->template['url_blog_modrewrite']="ok";
		}

		IfNotSetNull($this->content->template['url_blog_modrewrite']);

		global $checked;
		if (defined("admin") && $checked->submenu == 'shortcut') {
			$this->content->template['submenu'] = $this->checked->submenu;
		}
		//Module anzeigen
		else {
			IfNotSetNull($this->cms->module->module_aktiv['mod_blogger_calendar']);
			//$this->cms->module->module_aktiv['mod_blogger_calendar'] = NULL;

			//Wenn Modul aktiv Kalender - Kalender Modul anzeigen
			if ($this->cms->module->module_aktiv['mod_blogger_calendar'] === true) {
				if (!is_array($this->menuids)) {
					return false;
				}
				$show="";

				foreach ($this->menuids as $mk=>$mv) {
					if ($this->checked->menuid==$mv['blogger_shortcuts_menuid'] && $mv['blogger_shortcuts_calendar']==1 ) {
						$show="ok";
					}
				}
				//Nur beim jeweiligen Menüpunkt der Blog sein soll anzeigen
				if ($show=="ok" || $this->showall=="ok") {
					$this->show_calendar();
				}
			}
			IfNotSetNull($this->cms->module->module_aktiv['mod_blogger_monate']);

			if ($this->cms->module->module_aktiv['mod_blogger_monate'] === true) {
				if (!is_array($this->menuids)) {
					return false;
				}
				$show="";
				foreach ($this->menuids as $mk=>$mv) {
					if ($this->checked->menuid==$mv['blogger_shortcuts_menuid']  ) {
						$show="ok";
					}
				}

				//Nur beim jeweiligen Menüpunkt der Blog sein soll anzeigen
				if(!isset($this->showall)) {
					$this->showall = "";
				}
				if ($show == "ok" || $this->showall == "ok") {
					$this->show_monate();
				}
			}
			IfNotSetNull($this->cms->module->module_aktiv['mod_blogger_wordcloud']);

			//Wenn Modul aktiv Wolke - Wolke Modul anzeigen
			if ($this->cms->module->module_aktiv['mod_blogger_wordcloud'] === true) {
				if (!is_array($this->menuids)) {
					return false;
				}
				$show="";

				foreach ($this->menuids as $mk=>$mv) {
					if ($this->checked->menuid==$mv['blogger_shortcuts_menuid'] && $mv['blogger_shortcuts_wordcloud']==1 ) {
						$show="ok";
					}
				}

				//Nur beim jeweiligen Menüpunkt der Blog sein soll anzeigen
				if ($show=="ok" || $this->showall=="ok") {
					//Wolke anzeigen
					$this->show_wordcloud();
					//Worte durchgehen
					foreach ($this->data as $key => $value) {
						$this->content->template['blogger_' . $key] = $value;
					}
				}
			}
		}
		$this->content->template['menuid'] = $this->checked->menuid;
	}

	private function show_monate()
	{
		//Erstmal alle Artikel holen die passen inkl. Datum
		$data=$this->get_alle_artikel_mit_month();

		//Daraus die Monate als Liste erstellen
		$monats_liste=$this->create_monate_liste_from_articles($data);

		//Links einbauen... evtl. Template?
		$this->content->template['monatsliste_blog']=$monats_liste;
		$this->content->template['web_pfad']=PAPOO_WEB_PFAD;
	}

	/**
	 * Daraus die Monate als Liste erstellen
	 *
	 * @param array $data
	 * @return bool
	 */
	private function create_monate_liste_from_articles($data=array())
	{
		//Kein Array, abbrechen
		if (!is_array($data)) {
			return false;
		}
		$neu=array();
		foreach ($data as $k=>$v) {
			$neu[strtotime($v['timestamp'])]=$v;
		}

		krsort($neu);

		//Durchlaufen
		foreach ($neu as $k=>$v) {
			//Erstmal time
			$time=strtotime($v['timestamp']);

			//Dann daraus den Monat und das Jahr

			$jahr=date("Y",$time);
			$monat=$this->make_monat(date("F",$time));

			//make_monat
			$eintrag=$monat." ". $jahr;

			//$monate[$eintrag][]=$v['reporeID'];
			$ts=explode(" ",$v['timestamp']);
			$ts2=explode("-",$ts['0']);
			$monate[$eintrag]=$this->checked->menuid."-".trim($ts2['0'])."-".trim($ts2['1']);

			//Dann daraus ein Array bauen
		}
		return $monate;
	}

	/**
	 * Alle Artikel nach Monaten rausholen
	 */
	private function get_alle_artikel_mit_month()
	{
		// Join über reporep, lookupartcat und bloggershortcuts
		$sql = sprintf("SELECT reporeID, timestamp  FROM `%s`
                LEFT JOIN `%s` ON reporeID = lart_id
                LEFT JOIN `%s` ON lcat_id = blogger_shortcuts_menuid
                WHERE blogger_shortcuts_month='1'
                GROUP BY reporeID
                ",
			$this->cms->tbname['papoo_repore'],
			$this->cms->tbname['papoo_lookup_art_cat'],
			$this->cms->tbname['plugin_blogger_shortcuts']

		);
		$result=$this->db->get_results($sql,ARRAY_A);
		return $result;
	}

	private function show_calendar()
	{
		$this->content->template['blog_kalender_result']['1']['calender_dat']=$this->make_calender();
	}

	private function show_wordcloud()
	{
		if(!isset($this->checked->word_id)) {
			$this->checked->word_id = 0;
		}
		$this->data['word_id'] = $this->checked->word_id;
	}

	private function process_calendar()
	{
		#$vorlage=$this->create_cal_vorlage($all_cals);
		//Daten der Kalender
		#$this->content->template['kalender_result']=$vorlage;
	}

	/**
	 * Kalender erstellen
	 *
	 * @param string $cal
	 * @return mixed
	 */
	function make_calender( $cal = "" )
	{
		$monat = date( "m" );
		$jahr = date( "Y" );

		if (isset($this->checked->date_time) && is_numeric($this->checked->date_time) && $this->checked->date_time>date( "Y" )) {
			$jahr = date("Y", $this->checked->date_time)-1;
		}
		else {
			$jahr = date( "Y" )-1;
		}

		// Liste der Monate
		$n = date( "n", mktime( 0, 0, 0, $monat, 1, ($jahr) ) );

		//Die letzten 12 Monate
		$m = $n + 12;
		for ( $ij = $n; $ij <= $m; $ij++ ) {
			$monat_array[$ij]['name'] = $this->make_monat( date( "F", mktime( 0, 0, 0, $ij,
				1, $jahr ) ) );
			$monat_array[$ij]['jahr'] = ( date( "Y", mktime( 0, 0, 0, $ij, 1, $jahr ) ) );
			$monat_array[$ij]['mon_id'] = $ij;
		}
		$this->content->template['blog_monat_array_kal'] = $monat_array;
		IfNotSetNull($this->checked->monats_id);
		$this->content->template['blog_monats_id'] = $this->checked->monats_id;
		if ( !empty( $this->checked->monats_id ) ) {
			$monat = $this->checked->monats_id;
		}
		else {
			$this->content->template['blog_monats_id'] = date( "n", (time()) )+12;
			$monat =  $this->content->template['blog_monats_id'];
		}
		// Leere Eintr�ge bis zum ersten Tag in der Tabelle erzeugen
		$firstday = mktime( 0, 0, 0, $monat, 1, $jahr );
		$tagderwoche = date( "w", $firstday );
		if ($tagderwoche<=0) {
			$tagderwoche=7;
		}
		$heute = mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) );

		for ( $j = $tagderwoche-1; $j >= 1; $j-- ) {
			$tagderwoche_a[] = date( "d", $firstday-($j*24*3600) );
		}

		IfNotSetNull($tagderwoche_a);
		$this->content->template['blog_tagderwoche_a'] = $tagderwoche_a;
		$this->monat=$monat;
		$this->jahr=$jahr;
		// Tage des aktuellen Monats raussuchen
		$anzahl = date( "t", mktime( 0, 0, 0, $monat, 1, $jahr ) );

		# Leere Eintr�ge zum Auff�llen der Tabelle am Ende einf�gen
		$tagderwoche_last = date( "w", mktime( 0, 0, 0, $monat, $anzahl, $jahr ) ) ;
		if ($tagderwoche_last>0) {
			for ( $j = 7; $j > $tagderwoche_last; $j-- ) {
				$tagderwoche_last_array[] = $j;
			}
		}
		$this->content->template['blog_tagderwoche_last'] = $tagderwoche_last_array;

		// durchloopen und zuweisen
		for ( $tag = 1; $tag <= $anzahl; $tag++ ) {
			$kal_tage[$tag][] = $tag;
			// Checken ob Montag
			$montag = date( "w", mktime( 0, 0, 0, $monat, $tag, $jahr ) ) == 1;
			// Wenn Montag dann zuweisen und neuen tr erzwingen
			if ( $montag ) {
				$kal_tage[$tag][] = $montag;
			}

			// Anzahl der freien Pl�tze auf 0 setzen
			$this->frei = 0;
			$this->belegt = '';

			// checken ob an dem Tag buchen m�glich ist
			$dertag = mktime( 0, 0, 0, $monat, $tag, $jahr );

			//Immer für den ganzen Monat, hier gehts bis zum Datum, nicht ab Datum
			if ( $heute >= $dertag ) {
				$ok = $this->check_date( $dertag, $monat, $cal, $jahr);
				$ok2 = $this->check_date( $dertag, $monat, $cal, $jahr,"all" );
				$kal_tage[$tag]['belegt'] = "";
				if ($ok) {
					$kal_tage[$tag]['link'] = 'ok';
					$kal_tage[$tag]['url_date']=date("Y-m-d",$dertag);
					#$kal_tage[$tag]['pkal_date_id'] =$ok['pkal_date_id'];
					IfNotSetNull($ok['pkal_date_titel_des_termins']);
					$kal_tage[$tag]['termin_name'] = $ok['pkal_date_titel_des_termins'];
					// $kal_tage[$tag]['termin_time'] = $dertag;
					$kal_tage[$tag]['data'] = $ok2['0'];
					//$ok['pkal_date_titel_des_termins']=str_replace(" ","-",$ok['pkal_date_titel_des_termins']);
					//$kal_tage[$tag]['termin_name_url'] =  preg_replace("/[^a-z0-9-]/", "",strtolower($ok['pkal_date_titel_des_termins']));
				}
				else {
					$kal_tage[$tag]['link'] = "";
				}
			}
			$kal_tage[$tag]['datum'] = mktime( 0, 0, 0, $monat, $tag, $jahr );
		}
		// Daten ins Template
		return $kal_tage;
	}

	/**
	 * @param $date
	 * @param string $monat
	 * @param string $cal
	 * @param string $jahr
	 * @param bool $all
	 * @param int $plusstop
	 * @return array|bool|void
	 */
	function check_date($date, $monat="", $cal="", $jahr="",$all=false,$plusstop=0)
	{
		/**
		 * Eigentlich brauchen wir hier ja nur positiv oder negativ um einen Link zu erzeugen
		 * Die Auswahl welche Artikel erfolgt später...
		 */

		//Keine Menuids - dann raus
		if (!is_array($this->menuids)) {
			return false;
		}

		// Hier die Einträge der gewählten Menuids bestimmen
		$lcatid=" ";
		foreach ($this->menuids as $k=>$v) {
			$lcatid.=" OR lcat_id='".$v['blogger_shortcuts_menuid']."' ";
		}

		//Start und Stop definieren
		$start=$date;
		$stop =$date+43199+43199+$plusstop;
		global $diverse;

		if (!empty($this->checked->widget)) {
			$this->content->template['weiter_anzahl_pages']="";
			$this->content->template['weiter_array']=array();

			$sql=sprintf("SELECT reporeID, lcat_id FROM %s
                            LEFT JOIN %s  ON lart_id=reporeID
                            WHERE
                            stamptime < '%d'
                            AND stamptime >'%d'
                            AND allow_publish='1'
                            AND (lcat_id='%s'
                            %s)
                            ",
				DB_PRAEFIX."papoo_repore",
				DB_PRAEFIX."papoo_lookup_art_cat",
				$stop,
				$start,
				'nix',
				$lcatid
			);
			$resx2 = $this->db->get_results($sql);
			if (!empty($resx2)) {
				$result_count = count($resx2);
			}
			else {
				$result_count = 1;
			}

			$this->weiter->result_anzahl=$result_count;
			$this->weiter->make_limit($this->cms->config_paginierung);

			if ($this->cms->mod_free!="ok") {
				IfNotSetNull($this->checked->time);
				$this->weiter->weiter_link="./index.php?menuid=".$this->checked->menuid."&time=".$this->checked->time."&widget=blogcal";
			}
			$this->weiter->do_weiter("teaser");
		}
		$this->weiter->make_limit($this->cms->config_paginierung);

		$sql=sprintf("SELECT reporeID, lcat_id FROM %s
                        LEFT JOIN %s  ON lart_id=reporeID
                        WHERE
                        stamptime < '%d'
                        AND stamptime >'%d'
                        AND allow_publish='1'
                        AND (lcat_id='%s'
                        %s)
                        GROUP BY (reporeID)
                        %s",
			DB_PRAEFIX."papoo_repore",
			DB_PRAEFIX."papoo_lookup_art_cat",
			$stop,
			$start,
			'nix',
			$lcatid,
			$this->weiter->sqllimit
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (!empty($result)) {
			return $result;
		}
		return false;
	}

	/**
	 * Monatsnamen korrekt darstellen
	 *
	 * @param $mon
	 * @return string
	 */
	function make_monat( $mon )
	{
		if ( $this->cms->lang_id == 2 ) {
			return $mon;
		}
		else {
			switch ( $mon ) {
			case "January":
				$mon = "Januar";
				break;

			case "February":
				$mon = "Februar";
				break;

			case "March":
				$mon = "M&auml;rz";
				break;

			case "April":
				$mon = "April";
				break;

			case "May":
				$mon = "Mai";
				break;

			case "June":
				$mon = "Juni";
				break;

			case "July":
				$mon = "Juli";
				break;

			case "August":
				$mon = "August";
				break;

			case "September":
				$mon = "September";
				break;

			case "October":
				$mon = "Oktober";
				break;

			case "November":
				$mon = "November";
				break;

			case "December":
				$mon = "Dezember";
				break;

			default:

				break;
			}
			$this->monats_name=$mon;
			return $mon;
		}
	}

	/**
	 * @return array|bool
	 * Anzeige aller Einträge mit diesem Datum
	 */
	public function get_teaser_cal()
	{
		//Keine Zeit gewählt, raus
		if (!$this->checked->time) {
			return false;
		}
		$result=$this->check_date($this->checked->time);

		if (!is_array($result)) {
			return false;
		}
		//Alle Einträge durchloopen
		foreach($result as $entry) {
			$this->checked->reporeid = $entry['reporeID'];
			#$article = $this->artikel->get_artikel();
			$teaser = $this->artikel->make_teaser();
			$return[]=$teaser['0'];
		}
		return $return;
	}

	/**
	 * @return array|bool
	 *
	 */
	public function get_teaser_month()
	{

		//Keine Zeit gewählt, raus
		if (!$this->checked->monats_time)
			return false;
		$result=$this->check_date($this->checked->monats_time,"","","","","2592000");

		if (!is_array($result)) {
			return false;
		}
		//Alle Einträge durchloopen
		foreach($result as $entry) {
			$this->checked->reporeid = $entry['reporeID'];
			#$article = $this->artikel->get_artikel();
			$teaser = $this->artikel->make_teaser();
			$return[]=$teaser['0'];
		}
		return $return;
	}
}

$blogger_shortcut = new blogger_shortcut_class;
