<?php
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpIncludeInspection */
/** @noinspection SqlResolve */
/** @noinspection SqlNoDataSourceInspection */

function checkCache($cacheFilename, $cacheLifetime = '+1 month')
{
	// Check if file exists in the first place
	if (file_exists($cacheFilename)) {
		// Check the creation date of the file
		$fileStats = stat($cacheFilename);
		$endOfCacheLifetime = (new \DateTime('@' . $fileStats['mtime']))->modify($cacheLifetime);
		return $endOfCacheLifetime >= new \DateTime('now');
	}
	return false;
}

// region initialization
foreach (['site_conf.php', 'classes/curl_class.php'] as $libFile) {
	$file = '../../../lib/' . $libFile;
	if (is_file($file)) {
		include_once $file;
	}
}

$apiServerAddress = "https://nominatim.openstreetmap.org/search";
$format = "json";
$pluginPath = $pfadhier . "/plugins/osm_map_plugin/";
$cachePath = $pluginPath . "cache/";
$cacheLifetime = '+1 month';
$db = new MySQLi($db_host, $db_user, $db_pw, $db_name);
// endregion

// region get config
$query = sprintf('SELECT * FROM %1$s WHERE id = 1/* LIMIT 1*/;', $db_praefix . 'papoo_osm_map_plugin_config');
$result = $db->query($query);
$osmMapConfig = array();
if (!is_bool($result)) {
	$osmMapConfig = $result->fetch_assoc();
	$result->free();
}
else {
	var_dump($db, $db->last_error);
	die($query);
}
$db->close();

$cacheLifetime = '+' . $osmMapConfig['nominatim_cache_lifetime'] . ' '
	. $osmMapConfig['nominatim_cache_lifetime_unit']
	. ($osmMapConfig['nominatim_cache_lifetime'] > 1 ? 's' : '');
// endregion

// region get request
$address = strip_tags($_GET['address']);
$addressHash = sha1($address);
$nominatimCacheFilename = $cachePath . "nominatim_" . $addressHash . ".json";
// endregion

// region send headers
header('Cache-Control: public, max-age=' . (24 * 60 * 60));
header_remove('Expires');
header_remove('Pragma');
header('Content-Type: application/json');
// endregion

// region get map data
if (checkCache($nominatimCacheFilename, $cacheLifetime)) {
	// now get the data
	$nominatimContents = file_get_contents($nominatimCacheFilename);
	$dataSource = "cache";
}
else {
	$curlUrl = $apiServerAddress . "?q=" . urlencode($address) . "&limit=1&format=json";
	try {
		$curl = new curl($curlUrl);
		$curl->setopt(CURLOPT_RETURNTRANSFER, true);
		$curl->setopt(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$nominatimContents = $curl->exec();
	}
	catch (curl_exception $e) {
		die(json_encode(['success' => false, 'reason' => 'curl_failed', 'exception' => $e->getMessage(),]));
	}
	file_put_contents($nominatimCacheFilename, $nominatimContents, LOCK_EX);
	$dataSource = "api";
}
$nominatimContents = json_decode($nominatimContents);

echo json_encode([
	'success' => true,
	'lon' => $nominatimContents[0]->lon,
	'lat' => $nominatimContents[0]->lat,
	'dataSource' => $dataSource,
	'address' => $address,
]);
exit();
// endregion