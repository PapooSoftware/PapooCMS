<?php
/**
 * @param $query
 * @param int $limit
 * @return mixed|void
 */
function osm_locate($query, $limit = 1) {
	$a = array('ä' , 'ö' , 'ü' , 'ß' , ','  , /*' bei ', ' am ',*/ '/'  , '.', '-'  , ' '  );
	$b = array('ae', 'oe', 'ue', 'ss', '%20', /*' '    , ' '   ,*/ '%20', '' , '%20', '%20');
	$query = str_ireplace($a, $b, $query);
	if (is_array($query)) {
		$url = 'http://nominatim.openstreetmap.org/search';
		if (isset($query['country']))
			$url .= '/' . $query['country'];
		else
			$url .= '/de';
		if (isset($query['postalcode']))
			$url .= '/' . $query['postalcode'];
		if (isset($query['city'])) {
			$city = explode('&20', $query['city']);
			$url .= '/' . $city[0];
		}
		if (isset($query['street']))
			$url .= '/' . $query['street'];
		$url .= '?format=json';
	}
	else {
		$url .= '&q=' . str_replace($a, $b, $query);
	}
	$headers = array(
		//"Content-type: application/json; charset=\"utf-8\"",
		"Cache-Control: no-cache, must-revalidate",
		"Pragma: no-cache",
	);
	$options = array(
		CURLOPT_URL => $url,
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
	foreach(json_decode($result, true) as $entry) {
		if ($entry['osm_type'] == 'way' || $entry['osm_type'] == 'node') {
			$entry['request'] = $url;
			return $entry;
		}
	}
}
