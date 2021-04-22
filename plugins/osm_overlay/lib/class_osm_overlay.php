<?php

/**
 * Class class_osm_overlay
 */
class class_osm_overlay {
	private $checked;
	private $cms;
	private $config;
	private $content;
	private $db;

	/**
	 * class_osm_overlay constructor.
	 */
	public function __construct()
	{
		global $checked, $cms, $content, $template, $db;
		if (!strpos($template, "osm_overlay")) {
			return;
		}

		$content->template['osm_overlay_slash'] = rtrim(PAPOO_WEB_PFAD, '/').'/';

		$this->checked = &$checked;
		$this->cms = &$cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->get_config();
		if (defined('admin')) {
			if ($checked->submenu == 'config') {
				$this->config();
			}
		}
		elseif (substr($template, -37) === 'osm_overlay/templates/markers.geojson') {
			// Warnungen ausschalten
			$error_reporting = error_reporting();
			error_reporting($error_reporting & ~E_WARNING);
			// GeoJSON ausgeben
			$this->get_geojson();
			exit();
		}
		$this->show();
	}

	/**
	 * @param $array
	 * @return array
	 */
	public function build_data($array)
	{
		$data = array();
		$query = array();
		$data['description'] = array();
		foreach ($array as $key => $value) {
			$value = trim($value);
			if ($key == $this->config['link_field']) {
				$data['description'][] = '<a href="' . $this->config['link'] . '&' . $this->config['link_field'] . '=' . $value . '">Link</a>';
				continue;
			}
			elseif (!in_array($key, $this->config['active_fields']) || empty($value)) {
				continue;
			}

			if (in_array($key, $this->config['loc_fields'])) {
				$query[$this->config['loc_field_type'][$key]] = $value;
			}

			if ($key == $this->config['label_field']) {
				$data['title'] = $value;
			}
			else {
				$data['description'][] =  $value;
			}
		}

		$data['query'] = $query;
		return $data;
	}

	public function check_json()
	{
		$json = $this->get_json();
		$this->config['path']['permission'] = @fileperms($this->config['path']['import']) & 0x0111 ? true : false;
		$this->config['exists'] = array();
		$this->config['exists']['data'] = file_exists($this->config['path']['data']);
		$this->config['exists']['fail_query'] = file_exists($this->config['path']['fail_query']);
		$this->config['exists']['fail_osm'] = file_exists($this->config['path']['fail_osm']);
		$this->config['exists']['json'] = !empty($json);
	}

	public function config()
	{
		if (isset($this->checked->save_config)){
			$this->save_config();
		}
		elseif (isset($this->checked->import)) {
			$this->import();
		}
		elseif (isset($this->checked->delete_json)) {
			$this->delete_json();
		}
	}

	/**
	 * @return bool
	 */
	public function config_table_exists()
	{
		$sql = sprintf("SHOW TABLES LIKE '%s%s'",
			DB_PRAEFIX,
			$this->config['table_name']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$exists = empty($result) ? false : true;
		$this->config['table_exists'] = $exists;
		if (!$exists) {
			return $exists;
		}
		$sql = sprintf("SELECT COUNT(*) AS `count` FROM `%s%s`",
			DB_PRAEFIX,
			$this->config['table_name']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->config['table_count'] = $result[0]['count'];
		return $exists;
	}

	public function delete_json()
	{
		@unlink($this->config['path']['data']);
		@unlink($this->config['path']['fail_query']);
		@unlink($this->config['path']['fail_osm']);

	}

	/**
	 * @return bool|void
	 */
	public function get_config()
	{
		$this->get_config_default();
		if (!$this->config_table_exists()) {
			return false;
		}
		$this->get_config_fields();
		$this->get_config_loc_fields();
	}

	public function get_config_default()
	{
		$sql = sprintf("SELECT * FROM `%splugin_osm_overlay`", DB_PRAEFIX);
		$result = $this->db->get_results($sql, ARRAY_A);
		$this->config = $result[0];

		$path = PAPOO_ABS_PFAD . '/' . $this->config['json_path'];
		$web = 'http://' . $_SERVER['SERVER_NAME'] . '/' . PAPOO_WEB_PFAD .  '/' . $this->config['json_path'];

		$this->config['path']['import'] = $path;
		$this->config['path']['data'] = $path . '/osm_overlay_data.json';
		$this->config['path']['fail_query'] = $path . '/osm_overlay_fail_query.json';
		$this->config['path']['fail_osm'] = $path . '/osm_overlay_fail_osm.json';
		if (empty($this->config['json_url'])) {
			$this->config['json_url'] = $web;
		}
		$this->config['web']['json'] = $this->config['json_url'] . '/osm_overlay_data.json';
		$this->config['web']['data'] = $web . '/osm_overlay_data.json';
		$this->config['web']['fail_query'] = $web . '/osm_overlay_fail_query.json';
		$this->config['web']['fail_osm'] = $web . '/osm_overlay_fail_osm.json';
	}

	public function get_config_fields()
	{
		$sql = sprintf("SELECT * FROM `%splugin_osm_overlay_fields`", DB_PRAEFIX);
		$result = $this->db->get_results($sql, ARRAY_A);
		$fields = array();
		if (!empty($result)) {
			foreach ($result as $row) {
				$fields[] = $row['field_name'];
			}
		}
		$this->config['active_fields'] = $fields;
	}

	public function get_config_loc_fields()
	{
		$sql = sprintf("SELECT * FROM `%splugin_osm_overlay_loc_fields`", DB_PRAEFIX);
		$result = $this->db->get_results($sql, ARRAY_A);
		$fields = array();
		$types = array();
		if (!empty($result)) {
			foreach ($result as $row) {
				$fields[$row['name']] = $row['name'];
				$types[$row['name']] = $row['type'];
			}
		}
		$this->config['loc_fields'] = $fields;
		$this->config['loc_field_type'] = $types;
	}

	/**
	 * @param null $filter
	 */
	public function get_table_fields($filter = null)
	{
		$sql = sprintf("SHOW columns FROM `%s%s`",
			DB_PRAEFIX,
			$this->config['table_name']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		$fields = array();
		foreach ($result as $row) {
			if ($filter !== null && preg_match('/' . $filter . '/', $row['Field'])) {
				continue;
			}
			$fields[] = $row['Field'];
		}
		if ($filter == null) {
			$this->config['fields'] = $fields;
		}
		else {
			$this->config['fields_filtered'] = $fields;
		}
	}

	/**
	 * @return mixed
	 */
	public function get_json()
	{
		// http(s) vorne? dann cURL call
		if (strpos(strtolower($this->config['web']['json']), 'http') === 0) {
			$headers = array(
				"Accept: application/json, text/json",
				"Accept-Charset: utf-8",
				"Pragma: no-cache"
			);
			$options = array(
				CURLOPT_URL => $this->config['web']['json'],
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_HEADER => false,
				CURLOPT_HTTPGET => true,
				CURLOPT_FRESH_CONNECT => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
			);
			$ch = curl_init();
			curl_setopt_array($ch, $options);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		// Ansonsten Dateiinhalt normal holen
		else {
			$result = file_get_contents(PAPOO_ABS_PFAD.$this->config['web']['json']);
		}
		return json_decode($result, true);
	}

	public function get_text()
	{
		$this->get_config();
		$data = $this->get_json();

		$result = array();
		$result[] = "point\ttitle\tdescription\ticon";

		foreach ($data as $entry) {
			$line = array();
			$description = array();
			$line[] = $entry['lat'] . ',' . $entry['lon'];
			$line[] = $entry['title'];
			$description = '';
			foreach($entry['description'] as $row) {
				$description .= '<span class="description">' . $row . '</span>';
			}
			$line[] = $description;
			$line[] = '/plugins/osm_overlay/css/marker.png';
			$result[] = implode("\t", $line);
		}
		header('Content-type: text/tab-separated-values; charset=utf-8');
		header('Cache-Control: no-cache, must-revalidate');
		echo implode(PHP_EOL, $result);
		echo PHP_EOL;
		exit;
	}

	public function get_geojson()
	{
		$this->get_config();
		$data = $this->get_json();

		$result = array();

		if (is_array($data)) {
			foreach ($data as $entry) {
				$feature = array(
					'type' => 'Feature',
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array((float)$entry['lon'], (float)$entry['lat'])
					),
					'properties' => array(
						'title' => $entry['title'],
						'description' => implode("\n", $entry['description']),
						'icon' => rtrim(PAPOO_WEB_PFAD, '/').'/plugins/osm_overlay/css/marker.png',
					)
				);
				$result[] = $feature;
			}

			$response = array(
				'type' => 'FeatureCollection',
				'features' => $result
			);
		}
		else {
			http_response_code(500);
			$response = array(
				'error' => 'Could not load JSON source',
				'type' => 'FeatureCollection',
				'features' => []
			);
		}

		header('Content-Type: application/geo+json');
		header('Cache-Control: public, max-age=30, must-revalidate');
		echo json_encode($response);
		exit;
	}

	public function import()
	{
		$sql = sprintf("SELECT * FROM `%s%s`",
			DB_PRAEFIX,
			$this->config['table_name']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		//$this->db = serialize($this->db);
		$data = array();
		$fail = array(
			'query' => array(),
			'osm' => array()
		);
		foreach ($result as $row) {
			$entry = $this->build_data($row);
			if (empty($entry['query'])) {
				$fail['query'][] = $entry;
				continue;
			}
			$data[] = $entry;
		}
		if (empty($data)) {
			return;
		}

		include PAPOO_ABS_PFAD . '/plugins/osm_overlay/lib/osm_locate.php';
		$result = array();
		foreach ($data as $key => $entry) {
			$loc = $this->locate($entry);
			if ($loc === false) {
				$fail['osm'][] = $entry;
			}
			else {
				$result[] = $loc;
			}
		}
		$this->save_json($result, $fail);
		// $this->db = unserialize($this->db);
	}

	/**
	 * @param $entry
	 * @return bool
	 */
	public function locate($entry)
	{
		$loc = osm_locate($entry['query']);
		if (empty($loc)) {
			return false;
		}
		$entry['lon'] = $loc['lon'];
		$entry['lat'] = $loc['lat'];
		$entry['request'] = $loc['request'];
		return $entry;
	}

	/**
	 * @param $data
	 * @param $fail
	 */
	public function save_json($data, $fail)
	{
		$this->delete_json();
		if (!empty($data)) {
			file_put_contents($this->config['path']['data'], json_encode($data));
			$this->content->template['osm_message'] = 'Es wurden ' . count($data) . ' Addressen importiert. <a href="' . $this->config['web']['data'] . '">Link</a>';
		}

		if (!empty($fail['query'])) {
			file_put_contents($this->config['path']['fail_query'], json_encode($fail['query']));
			$this->content->template['osm_message'] .= '<br>Es konnten ' . count($fail['query']) . ' Einträge nicht zugeordnet werden. <a href="' . $this->config['web']['fail_query'] . '">Link</a>';
		}
		if (!empty($fail['osm'])) {
			file_put_contents($this->config['path']['fail_osm'], json_encode($fail['osm']));
			$this->content->template['osm_message'] .= '<br>Es konnten ' . count($fail['osm']) . ' Addressen nicht gefunden werden. <a href="' . $this->config['web']['fail_osm'] . '">Link</a>';
		}
	}

	public function save_config()
	{
		$this->content->template['osm_message'] = '';
		$result = $this->save_config_default();
		$result = $this->save_config_fields();
		$result = $this->save_config_loc_fields();
		$this->content->template['osm_message'] .= 'Die Daten wurden gespeichert';
		$this->get_config();
	}

	/**
	 * @return bool|int|mixed|mysqli_result|void
	 */
	public function save_config_default()
	{
		$sql = sprintf("TRUNCATE `%splugin_osm_overlay`", DB_PRAEFIX);
		$this->db->query($sql);
		$sql = sprintf("INSERT INTO `%splugin_osm_overlay` VALUES(
				'1',
				'" . $this->checked->osm_lon . "',
				'" . $this->checked->osm_lat . "',
				'" . $this->checked->osm_zoom . "',
				'" . $this->checked->osm_search_zoom . "',
				'" . $this->checked->osm_table_name . "',
				'" . $this->checked->osm_json_path . "',
				'" . $this->checked->osm_json_url . "',
				'" . $this->checked->osm_link . "',
				'" . $this->checked->osm_link_field . "',
				'" . $this->checked->osm_label_field . "',
				'" . $this->checked->osm_header . "',
				'" . $this->checked->osm_footer . "'
			)",
			DB_PRAEFIX
		);

		return $this->db->query($sql);
	}

	/**
	 * @return bool|void
	 */
	public function save_config_fields()
	{
		$sql = sprintf("TRUNCATE `%splugin_osm_overlay_fields`", DB_PRAEFIX);
		$this->db->query($sql);
		if (!$this->checked->osm_active_fields) {
			return false;
		}

		$sql = sprintf('INSERT INTO `%splugin_osm_overlay_fields` VALUES', DB_PRAEFIX);
		$fields = array();
		foreach ($this->checked->osm_active_fields as $key => $field) {
			$fields[] = sprintf("('', '%s')",
				$field
			);
		}
		$sql .= implode(', ', $fields);
		$this->db->query($sql);
	}

	/**
	 * @return bool|void
	 */
	public function save_config_loc_fields()
	{
		$sql = sprintf("TRUNCATE `%splugin_osm_overlay_loc_fields`", DB_PRAEFIX);
		$this->db->query($sql);
		if (!$this->checked->osm_loc_fields) {
			return false;
		}

		$sql = sprintf('INSERT INTO `%splugin_osm_overlay_loc_fields` VALUES', DB_PRAEFIX);
		$fields = array();
		foreach ($this->checked->osm_loc_fields as $field) {
			$fields[] = sprintf("('', '%s', '%s')",
				$field,
				$this->checked->osm_loc_field_type[$field]
			);
		}
		$sql .= implode(', ', $fields);
		$this->db->query($sql);
	}

	public function show()
	{
		if (defined('admin')) {
			echo "ADMIN definiert";
			if ($this->config['table_exists']) {
				echo "TABELLE existiert";
				$this->get_table_fields();
				$this->get_table_fields('^mv_content_');
			}
			$this->content->template['submenu'] = $this->checked->submenu;
			$this->check_json();
		}
		foreach ($this->config as $key => $value) {
			$this->content->template['osm_' . $key] = $value;
		}
	}
}

$osm_overlay = new class_osm_overlay;
