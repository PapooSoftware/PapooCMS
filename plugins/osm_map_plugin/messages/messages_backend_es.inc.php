<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "Plugin de mapas de OpenStreetMap"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Este plugin crea mapas siempre que las direcciones se encuentren en las etiquetas &lt;address&gt; del código fuente HTML."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Este botón borra todos los archivos de la caché del plugin de OSM, recomendado para las nuevas entradas en las etiquetas &lt;address&gt;."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Borrar caché"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "¿Realmente quieres borrar la caché del plugin de OSM?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "Has decidido no borrar la caché."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Borrar caché"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Ajustes"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Altura de las tarjetas"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Ancho de la tarjeta"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Nivel de zoom de los mapas (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Guardar"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Aquí puede establecer la altura, la anchura y el nivel de zoom de todos los mapas. Si desea establecer uno o varios de estos ajustes en un solo mapa, especifique el atributo data-osm-map-height para la altura, data-osm-map-width para la anchura y/o data-osm-map-zoom para el nivel de zoom en el código fuente HTML de la etiqueta &lt;address&gt; correspondiente. He aquí un ejemplo: &lt;address data-osm-map-width=\"90%\"&gt; (el CSS en línea del mapa dirá entonces width: 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Duración de la caché de Nominatim"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Unidad para la vida útil"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "La caché se ha borrado con éxito"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "Los ajustes se han guardado!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "El caché del plugin no es escribible. Por favor, asegúrese de que la carpeta tiene permisos de escritura para el servidor web estableciendo los derechos de acceso a #máscara de bits#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "Por favor, desinstala el plugin de Google Maps."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "La clase cURL no existe, por favor manténgala en el directorio /lib/classes."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "La carpeta de caché está vacía, no se ha borrado ningún archivo."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "Ha introducido un valor no válido para la altura, establezca el valor por defecto."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "Ha introducido un valor no válido para la anchura, establezca el valor por defecto."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Se ha producido un error desconocido."; 

 ?>