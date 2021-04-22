<?php

/**
 * Class radius_search
 */
class radius_search
{
	/**
	 * radius_search constructor.
	 */
	function __construct()
	{
		global $cms, $db, $content, $checked, $template;
		$this->cms = & $cms;
		$this->db = & $db;
		$this->content = & $content;
		$this->checked = & $checked;

		if (isset($this->checked->plz)) {
			$this->do_radius_search();
		}
	}

	function do_radius_search()
	{
		$plz = $this->db->escape($this->checked->plz);
		if (!preg_match('/^([0-9]{3,5})$/', $plz)) {
			$plz = "";
		} // 3 bis 5 numerische Zeichen 0 - 9
		if ($plz) {
			$radius = 50;
			$sql = sprintf("SELECT * 
									FROM %s
									WHERE plz = '%s'
									LIMIT 1",
				$this->cms->tbname['papoo_radius_search_data_de'],
				$plz
			);
			$result = $this->db->get_results($sql, ARRAY_A);
			if ($result) {
				$lng = $result[0]['longitude'] / 180 * M_PI;
				$lat = $result[0]['latitude'] / 180 * M_PI;
				$sql = sprintf("SELECT DISTINCT plz,
										ort,
										ortsteil,
										ROUND((6367.41 * 
										SQRT(2 * 
										(1 - cos(RADIANS(latitude)) * 
										cos($lat) * 
										(sin(RADIANS(longitude)) * 
										sin($lng) + 
										cos(RADIANS(longitude)) * 
										cos($lng)) - 
										sin(RADIANS(latitude)) *
										sin($lat)))), 1) AS Distance
										
										FROM %s
										WHERE (6367.41 * 
										SQRT(2 * 
										(1 - cos(RADIANS(latitude)) * 
										cos($lat) * 
										(sin(RADIANS(longitude)) * 
										sin($lng) + 
										cos(RADIANS(longitude)) * 
										cos($lng)) - 
										sin(RADIANS(latitude)) * 
										sin($lat))) <= '$radius')
										GROUP BY plz
										ORDER BY plz",
					$this->cms->tbname['papoo_radius_search_data_de']
				);
				$this->content->template['result'] = $this->db->get_results($sql, ARRAY_A);
			}
		}
		$this->content->template['plz'] = $plz;
	}
}

$radius_search = new radius_search();
