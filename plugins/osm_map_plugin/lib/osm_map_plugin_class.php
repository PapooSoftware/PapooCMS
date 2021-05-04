<?php
/** @noinspection ALL */
/** @noinspection JSUnusedLocalSymbols */

/**
 * Created by PhpStorm.
 * User: raphael
 * Date: 05.09.18
 * Time: 15:45
 */
class osm_map_plugin
{
	#region global variables
	/** @var string */
	private $template;
	/** @var content_class */
	private $content;
	/** @var checked_class */
	private $checked;
	/** @var ezSQL_mysqli */
	private $db;
	/** @var cms */
	private $cms;
	#endregion

	#region private variables
	/** @var array This plugin depends on these classes which need to be loaded in the constructor */
	private $dependencies = ['template', 'content', 'checked', 'db', 'cms'];
	/** @var string Nominatim API server address */
	private $apiServerAddress = "https://nominatim.openstreetmap.org/search";
	/** @var string Nominatim API response format */
	private $format = "json";
	/** @var string Plugin folder */
	private $pluginPath = PAPOO_ABS_PFAD . "/plugins/osm_map_plugin/";
	/** @var string Cache folder */
	private $cachePath = PAPOO_ABS_PFAD . "/plugins/osm_map_plugin/cache/";
	/** @var array */
	private $config;
	/** @var string */
	private $cacheLifetime = '+1 month';
	/** @var string */
	private $widthHeightRegex = <<< REGEXP
/^(?'cssvalue'\d*(p[xtc]|[cm]m|r?em|ex|ch|v(w|h|min|max)|\%)|auto|inherit|initial|unset)(\s+(border|content)-box)?$/i
REGEXP;
	/**
	 * This attribute controls the behaviour of the plugin during an AJAX call. If true, the CMS components
	 * will not be initialized (therefore not setting the spamcode again).
	 *
	 * SET THIS VALUE TO TRUE IN ORDER TO SOLVE PROBLEMS WITH SPAMCODE AND INSTALLED + ACTIVE OSM MAP PLUGIN
	 *
	 * @var bool
	 */
	private $useAjaxFile = false;
	#endregion

	public function __construct()
	{
		$this->loadDependencies();

		if (defined('admin')) {
			$this->prepareBackend($this->template);
		}
		else {
			// get coordinates for address
			if (strpos($this->template, 'osm_map_plugin/templates/getAddress.json') !== false) {
				header('Cache-Control: public, max-age=' . (24 * 60 * 60));
				header_remove('Expires');
				header_remove('Pragma');
				die($this->getMapData($this->checked->address));
			}
		}

		$this->config = $this->getConfig();
		/* build cache lifetime datetime modificator, e.g. "+1 week", "+2 months" */
		$this->cacheLifetime = '+' . $this->config['nominatim_cache_lifetime'] . ' '
			. $this->config['nominatim_cache_lifetime_unit']
			. ($this->config['nominatim_cache_lifetime'] > 1 ? 's' : '');
		$this->content->template['plugin']['osm_map_plugin']['config'] = $this->config;
	}

	/**
	 * This function loads all dependencies automatically
	 *
	 * @return void
	 */
	private function loadDependencies()
	{
		foreach ($this->dependencies as $dependency) {
			global ${$dependency};
			// pass as reference, failover if a non-object value is given
			$this->$dependency = &${$dependency};
		}
	}

	/**
	 * Prepares the backend view, handles backend ajax calls
	 *
	 * @param $template
	 */
	public function prepareBackend($template)
	{
		// set config
		if (isset($this->checked->osm_map_submit_config) && $this->checked->osm_map_submit_config) {
			$this->setConfig();
			if ($this->db->last_error === null) {
				$this->content->template['osm_map_plugin']['successes'][] =
					&$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'];
			}
			else {
				$this->content->template['osm_map_plugin']['errors'][] =
					&$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'];
			}
		}

		// check for errors
		if (!$this->checkCacheFolderAccess()) {
			// compare temp created file stats to cache folder stats
			$bitmask = $this->getCacheFolderWritePermissions();
			$this->content->template['osm_map_plugin']['errors'][] = str_replace(
				'#bitmask#',
				$bitmask,
				$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask']
			);
		}
		if ($this->checkGoogleMapsPlugin()) {
			$this->content->template['osm_map_plugin']['errors'][] =
				&$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'];
		}
		if (!file_exists(PAPOO_ABS_PFAD . "/lib/classes/curl_class.php")) {
			$this->content->template['osm_map_plugin']['errors'][] =
				&$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'];
		}

		// AJAX Calls

		// delete cache
		if (strpos($template, 'osm_map_plugin/templates/deleteCache.json')) {
			die($this->deleteCacheData());
		}
	}

	/**
	 * Loads the JS stuff into the frontend
	 */
	public function output_filter()
	{
		global $output;

		if (!defined('admin')) {
			// replace </head> with <script…></head>
			$slashJsIntegration = '<script type="text/javascript">
                    var papooWebRoot = "' . PAPOO_WEB_PFAD . '/",
                        osm_map_height = "' . $this->config['map_hoehe'] . '",
                        osm_map_width = "' . $this->config['map_breite'] . '",
                        osm_map_zoom_level = "' . $this->config['map_zoom'] . '";
                </script>';
			$scriptTagTemplate = "<script type='text/javascript' src='#jssource#'></script>";
			$linkTagTemplate = "<link rel='stylesheet' href='#csssource#'>";
			$paths = [
				'css' => PAPOO_WEB_PFAD . "/plugins/osm_map_plugin/vendors/leaflet/leaflet.css",
				'leafletjs' => PAPOO_WEB_PFAD . "/plugins/osm_map_plugin/vendors/leaflet/leaflet.js",
				'pluginjs' => PAPOO_WEB_PFAD . "/plugins/osm_map_plugin/js/"
					. ($this->useAjaxFile ? "osm_map_plugin_ajax_nopapoo.js" :  "osm_map_plugin.js"),
			];
			$linkTag = str_replace('#csssource#', $paths['css'], $linkTagTemplate);
			$leafletScriptTag = str_replace('#jssource#', $paths['leafletjs'], $scriptTagTemplate);
			$pluginScriptTag = str_replace('#jssource#', $paths['pluginjs'], $scriptTagTemplate);
			// include leaflets js after its css!
			$output = str_replace(
				'</head>',
				$linkTag . "\n"
				. $leafletScriptTag . "\n" . $slashJsIntegration . "\n" . $pluginScriptTag . "\n</head>",
				$output
			);
		}
	}

	/**
	 * Gets the coordinates for the given address.
	 * The function also has its own cache in the osm_map_plugin/cache directory
	 *
	 * @param string $address URL encoded address
	 *
	 * @throws curl_exception Will be thrown if the curl execution fails or returns null
	 *
	 * @return string JSON encoded latitude and longitude
	 */
	private function getMapData($address)
	{
		$address = strip_tags($address);
		$addressHash = sha1($address);
		$nominatimCacheFilename = $this->cachePath . "nominatim_" . $addressHash . ".json";

		// check nominatim cache and get the data
		if ($this->checkCache($nominatimCacheFilename)) {
			// now get the data
			$nominatimContents = file_get_contents($nominatimCacheFilename);
			$dataSource = "cache";
		}
		// there is no nominatim cache data, so we're going to get the data straight from the API
		else {
			require_once PAPOO_ABS_PFAD . "/lib/classes/curl_class.php";

			$curlUrl = $this->apiServerAddress . "?q=" . urlencode($address) . "&limit=1&format=json";
			$curl = new curl($curlUrl);
			$curl->setopt(CURLOPT_RETURNTRANSFER, true);
			// needs to be set, otherwise the Nominatim API will not deliver any data ...
			$curl->setopt(CURLOPT_USERAGENT, "Papoo Software - OpenStreetMap (+https://papoo-media.de/)");

			try {
				$nominatimContents = $curl->exec();
			}
			catch (curl_exception $e) {
				return json_encode([
					'success' => false,
					'reason' => 'curl_failed',
					'exception' => $e->getMessage()
				]);
			}

			// Cache the results -> https://operations.osmfoundation.org/policies/nominatim/
			file_put_contents($nominatimCacheFilename, $nominatimContents, LOCK_EX);
			$dataSource = "api";
		}
		$nominatimContents = json_decode($nominatimContents);

		return json_encode([
			'success' => true,
			'lon' => $nominatimContents[0]->lon,
			'lat' => $nominatimContents[0]->lat,
			'dataSource' => $dataSource,
			'address' => $address,
		]);
	}

	/**
	 * Deletes all the cache files in osm_map_plugin/cache.
	 *
	 * @return string
	 */
	private function deleteCacheData()
	{
		// get all file names
		$files = glob($this->cachePath . '*.json');
		if ($files !== false) {
			if ($files !== array()) {
				// iterate files
				foreach ($files as $file) {
					if (is_file($file)) {
						// delete file
						unlink($file);
					}
				}
				return json_encode(['success' => true]);
			}
			else {
				return json_encode([
					'success' => false,
					'reason' => 'empty_folder'
				]);
			}
		}
		return json_encode([
			'success' => false,
			'reason' => 'unknown_error'
		]);
	}

	/**
	 * Checks the internal plugin cache if the given file is present and not out of cache lifetime.
	 *
	 * @param string $cacheFilename
	 *
	 * @return bool
	 *
	 * @throws Exception DateTime error
	 */
	private function checkCache($cacheFilename)
	{
		// Check if file exists in the first place
		if (file_exists($cacheFilename)) {
			// Check the creation date of the file
			$fileStats = stat($cacheFilename);
			$endOfCacheLifetime = (new DateTime('@' . $fileStats['mtime']))->modify($this->cacheLifetime);
			return $endOfCacheLifetime >= new DateTime('now');
		}
		return false;
	}

	/**
	 * Determines the correct octal access rights for cache folder.
	 * These may be used in FileZilla or linux console chmod command.
	 *
	 * @return string
	 */
	private function getCacheFolderWritePermissions()
	{
		$testFile = $this->pluginPath . 'stat_test.txt';

		// TODO: what if the temp file cannot be created!?
		// create temporary test file, get stats, remove file
		touch($testFile);
		$testFileStats = stat($testFile);
		unlink($testFile);

		// get cache folder stats
		$cacheFolderStats = stat($this->cachePath);

		// compare uids and gids and set flags with mandatory executable bit
		if ($testFileStats['uid'] === $cacheFolderStats['uid']) {
			// user permissions should suffice
			$bitmask = '0755';
		}
		else if ($testFileStats['gid'] === $cacheFolderStats['gid']) {
			// here we should use group permissions
			$bitmask = '0575';
		}
		else {
			// no need to test, we have to set permissions for "other" ... possibly dangerous!?
			$bitmask = '0557';
		}

		return $bitmask;
	}

	/**
	 * Checks if the cache folder is writeable by the apache process user
	 *
	 * @return bool
	 */
	private function checkCacheFolderAccess()
	{
		$return = is_dir($this->cachePath) && is_writable($this->cachePath);
		return $return;
	}

	/**
	 * Checks if the google maps plugin is installed
	 *
	 * @return bool
	 */
	private function checkGoogleMapsPlugin()
	{
		$query = sprintf(
			'SELECT plugin_id FROM %1$s WHERE plugin_name LIKE "%%Google Maps Plugin%%";',
			$this->cms->papoo_plugins
		);
		return $this->db->get_var($query) !== null;
	}

	/**
	 * Sets the config submitted from the backend
	 *
	 * @return bool|int
	 */
	private function setConfig()
	{
		// normalize values -> never trust user input!
		// zoom level and lifetime will be set to min/max value if input exceeds the limits
		// lifetime unit will default to 'month' if no insertable value was given
		// width and height can be set to simple parseable css values but will rollback to default if invalid
		$width = $this->checked->osm_map_width;
		$height = $this->checked->osm_map_height;
		$zoom = intval($this->checked->osm_map_zoom_level);
		$cacheTime = intval($this->checked->osm_map_cache_time);
		$cacheUnit = $this->checked->osm_map_cache_unit;
		if ($zoom < 0) {
			$zoom = 0;
		}
		if ($zoom > 19) {
			$zoom = 19;
		}
		if ($cacheTime < 1) {
			$cacheTime = 1;
		}
		if (!in_array(
			$cacheUnit, array_keys($this->content->template['plugin']['osm_map_plugin']['text']['select_cache_units'])
		)) {
			$cacheUnit = 'month';
		}
		$this->normalizeWidthHeight($width, $height);

		$query = sprintf(
			'UPDATE %1$s
			SET map_hoehe = "%2$s", map_breite = "%3$s", map_zoom = "%4$d",
				nominatim_cache_lifetime = %5$d, nominatim_cache_lifetime_unit = "%6$s"
			WHERE id = 1;',
			DB_PRAEFIX . 'papoo_osm_map_plugin_config',
			$this->db->escape($height),
			$this->db->escape($width),
			$this->db->escape($zoom),
			$this->db->escape($cacheTime),
			$this->db->escape($cacheUnit)
		);

		return $this->db->query($query);
	}

	/**
	 * Normalizes the width and height attibutes given by the user. If these attibutes are not parseable
	 * they will be rolled back to default values.
	 *
	 * @param string $width
	 * @param string $height
	 */
	private function normalizeWidthHeight(&$width, &$height)
	{
		// normalize width and height values
		$results = array(
			'width' => array(),
			'height' => array(),
		);
		foreach (array_keys($results) as $var) {
			// $var can be "width" or "height"
			preg_match_all($this->widthHeightRegex, $$var, $results[$var]);
			if (!empty($results[$var]['cssvalue'][0])) {
				// given value is valid -> can be written to database
				$$var = $results[$var]['cssvalue'][0];
			}
			else {
				// rollback to default
				$$var = ($var === 'width' ? '100%' : '300px');
				// emit warning
				$this->content->template['osm_map_plugin']['errors'][] =
					&$this->content->template['plugin']['osm_map_plugin']['error']['invalid_' . $var . '_value'];
			}
		}
	}

	/**
	 * Returns the complete config
	 *
	 * @return array|null
	 */
	private function getConfig()
	{
		return $this->db->get_row(sprintf(
			'SELECT * FROM %1$s WHERE id = 1;', DB_PRAEFIX . 'papoo_osm_map_plugin_config'
		), ARRAY_A);
	}
}

$osm_map_plugin = new osm_map_plugin();
