<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "Plugin di mappe OpenStreetMap"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Questo plugin crea mappe ovunque gli indirizzi si trovino nei tag &lt;address&gt; nel codice sorgente HTML."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Questo pulsante cancella tutti i file dalla cache del plugin OSM, consigliato per le nuove voci nei tag &lt;address&gt;."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Cancella la cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "Vuoi davvero cancellare la cache del plugin OSM?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "Avete deciso di non cancellare la cache."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Cancella la cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Impostazioni"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Altezza delle carte"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Larghezza della carta"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Livello di zoom delle mappe (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Salva"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Qui puoi impostare l'altezza, la larghezza e il livello di zoom di tutte le mappe. Se vuoi impostare una o più di queste impostazioni su una sola mappa, specifica l'attributo data-osm-map-height per l'altezza, data-osm-map-width per la larghezza e/o data-osm-map-zoom per il livello di zoom nel codice sorgente HTML del rispettivo tag &lt;address&gt;. Ecco un esempio: &lt;address data-osm-map-width=\"90%\"&gt; (il CSS inline della mappa dirà allora width: 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Durata della cache Nominatim"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Unità per la vita di servizio"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "La cache è stata cancellata con successo!"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "Le impostazioni sono state salvate!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "La cache del plugin non è scrivibile. Assicurati che la cartella sia scrivibile per il server web impostando i diritti di accesso a #bitmask#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "Disinstalla il plugin di Google Maps."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "La classe cURL non esiste, mantenetela nella directory /lib/classes."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "La cartella della cache è vuota, nessun file è stato cancellato."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "Hai inserito un valore non valido per l'altezza, imposta il valore di default."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "Hai inserito un valore non valido per la larghezza, imposta il valore su quello predefinito."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Si è verificato un errore sconosciuto."; 

 ?>