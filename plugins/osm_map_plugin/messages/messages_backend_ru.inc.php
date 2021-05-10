<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "Плагин для карт OpenStreetMap"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Этот плагин создает карты везде, где адреса встречаются в тегах &lt;address&gt; в исходном коде HTML."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Эта кнопка очищает все файлы из кэша плагина OSM, рекомендуется для новых записей в тегах &lt;address&gt;."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Очистить кэш"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "Вы действительно хотите удалить кэш плагина OSM?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "Вы решили не очищать кэш."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Очистить кэш"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Настройки"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Высота карт"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Ширина карты"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Уровень масштабирования карт (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Сохранить"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Здесь вы можете установить высоту, ширину и уровень масштабирования всех карт. Если вы хотите установить один или несколько из этих параметров только для одной карты, укажите атрибут data-osm-map-height для высоты, data-osm-map-width для ширины и/или data-osm-map-zoom для уровня масштабирования в исходном коде HTML соответствующего тега &lt;address&gt;. Вот пример: &lt;address data-osm-map-width=\"90%\"&gt; (встроенный CSS карты будет иметь ширину: 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Срок службы кэша Nominatim"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Единица измерения срока службы"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "Кэш успешно очищен!"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "Настройки были сохранены!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "Кэш плагина не доступен для записи. Пожалуйста, убедитесь, что папка доступна для записи веб-сервером, установив права доступа на #bitmask#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "Пожалуйста, удалите плагин Google Maps."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "Класс cURL не существует, пожалуйста, сохраните его в каталоге /lib/classes."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "Папка кэша пуста, никакие файлы не были удалены."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "Вы ввели недопустимое значение для высоты, установите значение по умолчанию."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "Вы ввели недопустимое значение для ширины, установите значение по умолчанию."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Произошла неизвестная ошибка."; 

 ?>