<?php

/**
 * Class adwords_kw_class
 */
class adwords_kw_class {
	/**
	 * adwords_kw_class constructor.
	 */
	function __construct()
	{
		// Einbindung der globalen Papoo-Objekte
		global $checked, $db, $db_praefix, $content, $menu, $intern_menu, $cms, $db_abs, $weiter;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->db_praefix = & $db_praefix;
		$this->content = & $content;
		$this->menu = &$menu;
		$this->intern_menu = & $intern_menu;
		$this->cms = $cms;
		$this->db_abs = & $db_abs;
		// weitere Seiten Klasse einbinden
		$this->weiter =& $weiter;

		$this->placeholder = $this->load_data();

		$this->make_adwords_kw();
	}

	public function make_adwords_kw()
	{
		// FRONTEND:
		if (!defined("admin")) {
			$this->switch_front();
		}

		// BACKEND:
		if (defined("admin")) {

			global $template;

			global $user;
			$user->check_intern();

			$this->content->template['adwords_kw_css_path'] = PAPOO_WEB_PFAD . '/plugins/adwords_kw_plugin/css';
			$this->content->template['log_data'] = $this->load_log_data();

			if (strpos("XXX".$template, "adwords_kw/templates/back_edit.html")) {
				$this->content->template['placeholder_list'] = $this->load_data();

				if (isset($this->checked->updateAdwords) && $this->checked->updateAdwords) {
					switch($this->checked->updateAdwords) {
					case "Speichern":
						$this->adwords_kw_save();
						break;
					}
				}

			}
			else if (strpos("XXX".$template, "adwords_kw/templates/back_number.html")) {
				if (isset($this->checked->updateNumber) && $this->checked->updateNumber) {
					$this->save_number();
				}
				$this->load_script();
			}
			else if (strpos("XXX".$template, "adwords_kw/templates/back_ab.html")) {
				if (isset($this->checked->updateAB) && $this->checked->updateAB) {
					$this->save_ab();
				}
				$this->load_ab_script();
			}
			else if (strpos("XXX".$template, "adwords_kw/templates/back_list.html")) {
				$this->make_weiter();
			}
			else if (strpos("XXX".$template, "adwords_kw/templates/back_edit_tel.html")) {
				$this->make_tel_data();
			}
		}
	}

	private function make_tel_data()
	{
		$this->content->template['placeholder_list'] = $this->load_data_tel();

		if (isset($this->checked->updateAdwordsTel) && $this->checked->updateAdwordsTel) {
			switch($this->checked->updateAdwordsTel) {
			case "Speichern":
				$this->adwords_tel_save();
				break;
			}
		}

	}

	function save_number()
	{
		$sql = sprintf("UPDATE %s SET
                    script='%s',
                    replace_number='%s'
                    WHERE id = '1'
                    ",
			$this->db_praefix . "plugin_adwords_kw_script",
			$this->db->escape( $this->checked->number_script ),
			$this->checked->number_number
		);
		$this->db->query($sql);
	}

	function save_ab()
	{
		$sql = sprintf("UPDATE %s SET
                    script='%s',
                    article_url='%s'
                    WHERE id = '1'
                    ",
			$this->db_praefix . "plugin_adwords_kw_ab",
			$this->db->escape( $this->checked->ab_script ),
			$this->checked->article_url
		);
		$this->db->query($sql);
	}

	function load_script()
	{
		$sql = sprintf("SELECT script, replace_number FROM %s",
			$this->db_praefix."plugin_adwords_kw_script"
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['number_script'] = $this->output_script = str_replace(array("\r", "\n"), '', $result[0]['script']);
		$this->content->template['number_number'] = $this->insert_number = $result[0]['replace_number'];
	}

	function load_ab_script()
	{
		$sql = sprintf("SELECT script, article_url FROM %s",
			$this->db_praefix."plugin_adwords_kw_ab"
		);
		$result = $this->db->get_results($sql, ARRAY_A);

		$this->content->template['ab_script'] = $this->ab_script = str_replace(array("\r", "\n"), '', $result[0]['script']);

		$this->content->template['article_url'] = $this->article_url = $result[0]['article_url'];
	}

	function switch_front()
	{
		$data = $this->load_data();

		foreach($data as $dat){
			$testerino = $dat['parameter'];
			$testerino = trim($testerino);

			if(isset($this->checked->$testerino) && $this->checked->$testerino != '') {
				$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$kw = $_GET[$testerino];
				$time = time();
				$this->save_log($url, $kw, $time);
			}
		}
		$this->load_script();
		$this->load_ab_script();
	}

	/**
	 * @param $output
	 * @return mixed|string|string[]|null
	 */
	private function load_tel_alternative($output)
	{
		$data = $this->load_data_tel();

		foreach($data as $dat){
			if (empty($this->checked->gclid)) {
				if (empty($_COOKIE[$dat['tel_placeholder']])) {
					$output=str_ireplace($dat['tel_placeholder'],$dat['tel_org'],$output);
				}
				//Cookie noch gesetzt = kam innerhalb der letzten 24h von AdWords
				else {
					$output=str_ireplace($dat['tel_placeholder'],$dat['tel_adwords'],$output);
				}
			}
			//glclid = kommt von adWords
			else {
				$output=str_ireplace($dat['tel_placeholder'],$dat['tel_adwords'],$output);
				setcookie($dat['tel_placeholder'],$dat['tel_adwords'],time()+86400);
			}
		}
		return $output;
	}

	/**
	 * @param string $kw
	 * @param string $url
	 */
	function delete_old_log($kw='',$url='')
	{
		$sql = sprintf("DELETE FROM %s WHERE keyword='%s' AND url ='%s'
        ",
			$this->db_praefix."plugin_adwords_kw_log",
			$kw,
			$url
		);
		$this->db->query($sql);
	}

	/**
	 * @param string $url
	 * @param string $kw
	 * @param string $time
	 */
	function save_log($url='', $kw='', $time='')
	{
		$sql = sprintf("SELECT counter FROM %s WHERE keyword = '%s' AND url = '%s' ",

			$this->db_praefix."plugin_adwords_kw_log",
			$kw,
			$url
		);

		$resultanzahl = $this->db->get_var($sql);

		if($resultanzahl > 0) {
			$resultanzahl++;
			$sql = sprintf("UPDATE %s SET date_time='%s', counter='%s' WHERE url ='%s' AND keyword = '%s'",
				$this->db_praefix . "plugin_adwords_kw_log",
				date('Y-m-d H:i:s', $time),
				$resultanzahl,
				$url,
				$kw
			);
			$this->db->query($sql);

		}
		else {
			$sql = sprintf("INSERT INTO %s SET date_time='%s', url='%s', keyword='%s', counter='%d'",
				$this->db_praefix . "plugin_adwords_kw_log",
				date('Y-m-d H:i:s', $time),
				$url,
				$kw,
				1
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param string $time
	 * @return string
	 */
	function convert_date_time($time ='')
	{
		$dt = new DateTime("@$time");
		return $dt->format('Y-m-d H:i:s');
	}

	function adwords_tel_save()
	{
		$this->delete_old_entrys_tel();
		$adwords_list = array();
		if($this->checked->tel_content_list) {
			preg_match_all('/(.*);(.*);(.*)/',$this->checked->tel_content_list,$matches);

			foreach($matches as $match) {
				IfNotSetNull($match[0]);
				IfNotSetNull($match[1]);
				IfNotSetNull($match[2]);

				if(!preg_match('/;/',$match[0]) && !preg_match('/;/',$match[1]) && !preg_match('/;/',$match[2])) {
					$i = 0;
					while($i < count($match)) {
						$adwords_list[$i][] = $match[$i];
						$i++;
					}
				}
			}
			foreach($adwords_list as $data) {
				$this->save_data_tel($data);
			}
			$this->content->template['placeholder_list'] = $this->load_data_tel();
		}
	}

	function adwords_kw_save()
	{

		$this->delete_old_entrys();
		$adwords_list = array();
		if($this->checked->rapid_content_list) {
			preg_match_all('/(.*);(.*);(.*)/',$this->checked->rapid_content_list,$matches);

			foreach($matches as $match) {
				IfNotSetNull($match[0]);
				IfNotSetNull($match[1]);
				IfNotSetNull($match[2]);

				if(!preg_match('/;/',$match[0]) && !preg_match('/;/',$match[1]) && !preg_match('/;/',$match[2])) {
					$i = 0;
					while($i < count($match)) {
						$adwords_list[$i][] = $match[$i];
						$i++;
					}
				}
			}
			foreach($adwords_list as $data) {
				$this->save_data($data);
			}
			$this->content->template['placeholder_list'] = $this->load_data();
		}
	}

	function delete_old_entrys()
	{
		$sql = sprintf("DELETE FROM %s",
			$this->cms->tbname['plugin_adwords_kw_list']
		);
		$this->db->query($sql);
	}

	function delete_old_entrys_tel()
	{
		$sql = sprintf("DELETE FROM %s",
			$this->cms->tbname['plugin_adwords_tel_list']
		);
		$this->db->query($sql);
	}

	/**
	 * @param string $data
	 */
	function save_data($data = '')
	{
		if(preg_match('/#(.*)#/',$data[0])) {
			$sql = sprintf("INSERT INTO %s SET
                    placeholder='%s',
                    keyword='%s',
                    parameter='%s'
                    ",
				$this->db_praefix . "plugin_adwords_kw_list",
				$data[0],
				$data[1],
				$data[2]
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @param string $data
	 */
	function save_data_tel($data = '')
	{
		if(preg_match('/#(.*)#/',$data[0])) {
			$sql = sprintf("INSERT INTO %s SET
                    tel_placeholder='%s',
                    tel_org='%s',
                    tel_adwords='%s'
                    ",
				$this->db_praefix . "plugin_adwords_tel_list",
				$data[0],
				$data[1],
				$data[2]
			);
			$this->db->query($sql);
		}
	}

	/**
	 * @return array|void
	 */
	function load_data()
	{
		$sql = sprintf("SELECT * FROM %s",
			$this->db_praefix."plugin_adwords_kw_list"
		);

		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * @return array|void
	 */
	function load_data_tel()
	{
		$sql = sprintf("SELECT * FROM %s",
			$this->db_praefix."plugin_adwords_tel_list"
		);

		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	function make_weiter()
	{
		if(isset($this->checked->search) && $this->checked->search) {
			$where = $this->checked->search;

			$sql = sprintf("SELECT count(*) FROM %s WHERE keyword LIKE '%s' OR url LIKE '%s' ",
				$this->db_praefix."plugin_adwords_kw_log",
				'%'.$where.'%',
				'%'.$where.'%'
			);
		} else {
			$sql = sprintf("SELECT count(*) FROM %s",
				$this->db_praefix."plugin_adwords_kw_log"
			);
		}
		IfNotSetNull($this->checked->search);

		$this->weiter->weiter_link = "plugin.php?menuid=" . $this->checked->menuid . "&template=adwords_kw/templates/back_list.html";
		$resultanzahl = $this->db->get_var($sql);
		$this->content->template['ergebnis_anzahl'] = $resultanzahl;
		$this->content->template['suchstring'] = $this->checked->search;
		$this->weiter->make_limit(10);
		$this->weiter->result_anzahl = $resultanzahl;
		$this->weiter->do_weiter('search');
	}

	/**
	 * @param string $url
	 * @return mixed|null
	 */
	function get_article_id($url = '')
	{
		$sql = sprintf("SELECT lan_repore_id FROM %s WHERE url_header = '%s'",
			$this->db_praefix."papoo_language_article",
			$url
		);
		$result = $this->db->get_var($sql);
		return $result;
	}

	/**
	 * @return array|void
	 */
	function load_log_data()
	{
		$this->content->template['menuid'] = isset($_GET['menuid']) ? $_GET['menuid'] : NULL;
		if (isset($this->checked->search) && $this->checked->search) {
			$where = $this->checked->search;

			$sql = sprintf("SELECT * FROM %s WHERE keyword LIKE '%s' OR url LIKE '%s' ",
				$this->db_praefix."plugin_adwords_kw_log",
				'%'.$where.'%',
				'%'.$where.'%'
			);
		}
		else {
			$sql = sprintf("SELECT * FROM %s ",
				$this->db_praefix."plugin_adwords_kw_log"
			);
		}
		$result = $this->db->get_results($sql, ARRAY_A);
		return $result;
	}

	/**
	 * @return bool|false|mixed|resource|string|string[]|void|null
	 */
	public function output_filter()
	{
		global $output;

		foreach($this->placeholder as $place_h) {
			$place_h['parameter'] = trim($place_h['parameter']);
			if (isset($this->checked->{$place_h['parameter']})) {
				$output = str_replace($place_h['placeholder'], $this->checked->{$place_h['parameter']}, $output);
			}
			else {
				$output=str_replace($place_h['placeholder'],$place_h['keyword'],$output);
			}
		}

		if($this->output_script != '' && $this->insert_number != '') {
			$output=str_replace("</body>",$this->output_script."</body>",$output);
			$output=str_replace($this->insert_number,'<span class="number">'.$this->insert_number.'</span>',$output);
			$output=str_replace("<body",'<body onload="_googWcmGet(\'number\',\''. $this->insert_number ."')\"", $output);
		}
		if($this->ab_script != '' && $this->article_url != '') {
			$id = $this->get_article_id($this->article_url);
			if($id == $this->content->template['reporeid_aktuell']) {
				$output=str_replace("</body>",$this->ab_script."</body>",$output);
			}
		}
		$output= $this->load_tel_alternative($output);
		return $output;
	}
}

$adwords_kw_class= new adwords_kw_class();