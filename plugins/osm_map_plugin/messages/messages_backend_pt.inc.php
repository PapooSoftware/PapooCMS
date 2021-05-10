<?php 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_headline'] = "OpenStreetMap plugin de mapa"; 
$this->content->template['plugin']['osm_map_plugin']['text']['plugin_description'] = "Este plugin cria mapas onde quer que os endereços sejam encontrados em &lt;endereço&gt; tags no código fonte HTML."; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_delete_cache'] = "Este botão limpa todos os ficheiros da cache de plugins OSM, recomendado para novas entradas em &lt;address&gt; tags."; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_delete_cache'] = "Limpar cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['question_delete_cache'] = "Você realmente quer apagar o cache de plugins OSM?"; 
$this->content->template['plugin']['osm_map_plugin']['text']['cache_not_deleted'] = "Você decidiu não limpar a cache."; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_delete_cache'] = "Limpar cache"; 
$this->content->template['plugin']['osm_map_plugin']['text']['legend_settings'] = "Configurações"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_height'] = "Altura das cartas"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_width'] = "Largura do cartão"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_zoom'] = "Nível de zoom dos mapas (0-19)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['button_settings_save'] = "Salvar"; 
$this->content->template['plugin']['osm_map_plugin']['text']['description_map_settings'] = "Aqui você pode definir a altura, largura e nível de zoom de todos os mapas. Se você quiser definir uma ou mais destas configurações em apenas um mapa, por favor especifique o atributo data-osm-map-height para a altura, data-osm-map-width para a largura e/ou data-osm-map-zoom para o nível de zoom no código fonte HTML do respectivo &lt;endereço&gt; tag. Aqui está um exemplo: &lt;abordagem dadososm-map-width=\"90%\"&gt; (o CSS em linha do mapa dirá então largura: 90%)"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_time'] = "Tempo de vida da cache Nominatim"; 
$this->content->template['plugin']['osm_map_plugin']['text']['label_settings_cache_unit'] = "Unidade para a vida útil"; 
$this->content->template['plugin']['osm_map_plugin']['success']['cache_deleted'] = "O cache foi limpo com sucesso!"; 
$this->content->template['plugin']['osm_map_plugin']['success']['settings_saved'] = "As configurações foram salvas!"; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_not_writeable_bitmask'] = "O cache do plugin não pode ser escrito. Por favor, certifique-se de que a pasta é gravável para o servidor web, definindo os direitos de acesso para #bitmask#."; 
$this->content->template['plugin']['osm_map_plugin']['error']['google_maps_installed'] = "Por favor, desinstale o plug-in do Google Maps."; 
$this->content->template['plugin']['osm_map_plugin']['error']['curl_class_not_present'] = "A classe cURL não existe, por favor mantenha-a no diretório /lib/classes."; 
$this->content->template['plugin']['osm_map_plugin']['error']['cache_folder_empty'] = "A pasta cache está vazia, nenhum arquivo foi apagado."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_height_value'] = "Você inseriu um valor inválido para a altura, defina o valor como padrão."; 
$this->content->template['plugin']['osm_map_plugin']['error']['invalid_width_value'] = "O usuário entrou um valor inválido para a largura, definir valor como padrão."; 
$this->content->template['plugin']['osm_map_plugin']['error']['unknown_error'] = "Ocorreu um erro desconhecido."; 

 ?>