<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "OpenStreetMap kaart plugin"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Deze plugin maakt kaarten waar adressen worden gevonden in &lt;address&gt; tags in de HTML broncode."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Deze knop wist alle bestanden uit de OSM plugin cache, aanbevolen voor nieuwe entries in &lt;address&gt; tags."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Cache wissen"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "Wil je echt de OSM plugin cache verwijderen?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "U heeft besloten de cache niet te wissen."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Cache wissen"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Instellingen"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Hoogte van de kaarten"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Kaartbreedte"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Zoomniveau van de kaarten (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Save"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Hier kunt u de hoogte, breedte en zoomniveau van alle kaarten instellen. Als u een of meer van deze instellingen op slechts één kaart wilt instellen, specificeer dan het attribuut data-osm-map-height voor de hoogte, data-osm-map-width voor de breedte en/of data-osm-map-zoom voor het zoomniveau in de HTML-broncode van de respectieve &lt;address&gt;-tag. Hier is een voorbeeld: &lt;address data-osm-map-width=\"90%\"&gt; (de inline CSS van de kaart zal dan zeggen width: 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Levensduur van de Nominatim cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Eenheid voor de levensduur"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "De cache is met succes leeggemaakt!"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "De instellingen zijn opgeslagen!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "De cache van de plugin is niet beschrijfbaar. Zorg ervoor dat de map beschrijfbaar is voor de webserver door de toegangsrechten in te stellen op #bitmask#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "De-installeer de Google Maps plugin."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "De cURL klasse bestaat niet, bewaar deze in de /lib/classes directory."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "De cache map is leeg, er zijn geen bestanden verwijderd."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "U hebt een ongeldige waarde ingevoerd voor de hoogte, stel de waarde in op standaard."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "U hebt een ongeldige waarde ingevoerd voor de breedte, stel de waarde in op standaard."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Er is een onbekende fout opgetreden."; 

 ?>