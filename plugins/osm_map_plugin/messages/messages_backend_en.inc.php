<?php
$this->content->template['plugin']['osm_map_plugin'] = array(
    // Texte
    'text' => array(
        // Plugin
        'plugin_headline' => 'OpenStreetMap map plugin',
        'plugin_description' =>
            'This plugin creates a map at each location an &lt;address&gt; tag can be found in the HTML 
            source code.',
        // Cache löschen
        'description_delete_cache' =>
            'This button deletes all files from the OSM plugin cache, recommended when any &lt;address&gt; 
            tag has been changed.',
        'button_delete_cache' => 'Delete cache',
        'question_delete_cache' => 'Are you sure you want to delete the OSM plugin cache?',
        'cache_not_deleted' => 'You did not decide to delete the cache.',
        'legend_delete_cache' => 'Delete cache',
        // Formular
        'legend_settings' => 'Settings',
        'label_settings_height' => 'Map height',
        'label_settings_width' => 'Map width',
        'label_settings_zoom' => 'Map zoom level (0-19)',
        'button_settings_save' => 'Save',
        'select_cache_units' => array(
            'day' => 'day/s',
            'week' => 'week/s',
            'month' => 'month/s',
            'year' => 'year/s',
        ),
        'description_map_settings' =>
            'You can adjust the height, width and zoom level setting for all maps. If you want to set one or more of 
            these settings on one map differently you have to append the data-osm-map-height for height, 
            data-osm-map-width for width and/or data-osm-map-zoom for zoom level setting to the &lt;address&gt; tag. 
            Example: &lt;address data-osm-map-width="90%"&gt; (this map will have width: 90% inline CSS)',
        'label_settings_cache_time' => 'Nominatim cache lifetime',
        'label_settings_cache_unit' => 'Lifetime unit',
    ),
    // Erfolgsmeldungen
    'success' => array(
        'cache_deleted' => 'The cache has been deleted successfully!',
        'settings_saved' => 'The settings have been saved!',
    ),
    // Fehlermeldungen
    'error' => array(
        'cache_not_writeable_bitmask' =>
            'The plugin cache is not writeable. Please ensure the cache folder has octal access rights #bitmask#.',
        'google_maps_installed' => 'Please uninstall the google maps plugin.',
		'curl_class_not_present' =>
			'The cURL class is not present, please add it to the path /lib/classes.',
        'cache_folder_empty' => 'The cache folder is empty, therefore no data has been deleted.',
        'invalid_height_value' => 'You specified an invalid value for map height, rolling back to default.',
        'invalid_width_value' => 'You specified an invalid value for map width, rolling back to default.',
        'unknown_error' => 'An unknown error has occurred.',
    ),
);