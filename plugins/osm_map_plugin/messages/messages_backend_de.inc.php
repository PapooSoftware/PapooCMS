<?php

$this->content->template['plugin']['osm_map_plugin'] = [
	// Texte
	'text' => [
		// Plugin
		'plugin_headline' => 'OpenStreetMap Karten-Plugin',
		'plugin_description' =>
			'Dieses Plugin erzeugt Karten überall da, wo im HTML-Quelltext Adressen in &lt;address&gt;-Tags zu finden 
			sind.',
		// Cache löschen
		'description_delete_cache' => 'Dieser Knopf löscht alle Dateien aus dem OSM Plugin Cache, empfehlenswert bei 
		Neueingaben in &lt;address&gt;-Tags.',
		'button_delete_cache' => 'Cache löschen',
		'question_delete_cache' => 'Wollen Sie wirklich den OSM Plugin Cache löschen?',
		'cache_not_deleted' => 'Sie haben entschieden, den Cache nicht zu löschen.',
		'legend_delete_cache' => 'Cache löschen',
		// Formular
		'legend_settings' => 'Einstellungen',
		'label_settings_height' => 'Höhe der Karten',
		'label_settings_width' => 'Breite der Karten',
		'label_settings_zoom' => 'Zoomlevel der Karten (0-19)',
		'button_settings_save' => 'Speichern',
		'select_cache_units' => [
			'day' => 'Tag/e', 'week' => 'Woche/n', 'month' => 'Monat/e', 'year' => 'Jahr/e',
		],
		'description_map_settings' => 'Hier können Sie die Höhe, Breite und das Zoomlevel aller Karten einstellen. 
		Sollten Sie eine oder mehrere dieser Einstellungen auf nur einer Karte setzen wollen, so geben Sie bitte im 
		HTML-Quelltext des jeweiligen &lt;address&gt;-Tags das Attribut data-osm-map-height für die Höhe, 
		data-osm-map-width für die Breite und/oder data-osm-map-zoom für das Zoomlevel an. Hier ein Beispiel: 
		&lt;address data-osm-map-width="90%"&gt; (im Inline-CSS der Karte steht dann width: 90%)',
		'label_settings_cache_time' => 'Lebensdauer des Nominatim-Cache',
		'label_settings_cache_unit' => 'Einheit für die Lebensdauer',
	],
	// Erfolgsmeldungen
	'success' => [
		'cache_deleted' => 'Der Cache wurde erfolgreich gelöscht!',
		'settings_saved' => 'Die Einstellungen wurden gespeichert!',
	],
	// Fehlermeldungen
	'error' => [
		'cache_not_writeable_bitmask' => 'Der Cache des Plugins ist nicht beschreibbar. Bitte sorgen Sie dafür, dass 
		der Ordner für den Webserver beschreibbar ist, indem Sie die Zugriffsrechte auf #bitmask# setzen.',
		'google_maps_installed' => 'Bitte deinstallieren Sie das Google Maps Plugin.',
		'curl_class_not_present' =>
			'Die cURL-Klasse ist nicht vorhanden, bitte pflegen Sie diese in das Verzeichnis /lib/classes ein.',
		'cache_folder_empty' => 'Der Cache-Ordner ist leer, es wurden keine Dateien gelöscht.',
		'invalid_height_value' => 'Sie haben einen ungültigen Wert für die Höhe eingegeben, setze Wert auf Standard.',
		'invalid_width_value' => 'Sie haben einen ungültigen Wert für die Breite eingegeben, setze Wert auf Standard.',
		'unknown_error' => 'Ein unbekannter Fehler ist aufgetreten.',
	],
];