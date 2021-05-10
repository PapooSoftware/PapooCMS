<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "OpenStreetMap地图插件"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "这个插件可以在HTML源代码中的&lt;address&gt;标签中找到地址的地方创建地图。"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "这个按钮可以清除OSM插件缓存中的所有文件，推荐用于&lt;地址&gt;标签中的新条目。"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "清除缓存"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "你真的要删除OSM插件的缓存吗？"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "你已经决定不清除缓存。"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "清除缓存"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "设置"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "卡片的高度"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "卡片宽度"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "地图的缩放级别（0-19）。"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "拯救"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "在这里，您可以设置所有地图的高度、宽度和缩放级别。 如果您只想在一张地图上设置一个或多个这样的设置，请在相应的&lt;address&gt;标签的HTML源代码中指定高度的属性data-osm-map-height、宽度的属性data-osm-map-width和/或缩放级别的data-osm-map-zoom。下面是一个例子：&lt;address data-osm-map-width=\"90%\"&gt;（然后地图的内联CSS将显示宽度：90%）。"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Nominatim缓存的寿命"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "服务寿命的单位"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "缓存已被成功清除!"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "设置已被保存!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "该插件的缓存是不可写的。请通过将访问权限设置为#bitmask#，确保该文件夹对网络服务器是可写的。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "请卸载谷歌地图插件。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "cURL类不存在，请在/lib/classes目录下维护它。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "缓存文件夹是空的，没有文件被删除。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "你输入了一个无效的高度值，请将值设为默认值。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "你输入了一个无效的宽度值，请将值设为默认值。"; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "发生了一个未知的错误。"; 

 ?>