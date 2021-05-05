<?php 
$this->content->template['plugin_tinypng_head'] = "TinyPNG"; 
$this->content->template['plugin_tinypng_description'] = "<p>Этот плагин сжимает все сжимаемые изображения из каталога images, используя API <a href=\"https://tinypng.com\" target=\"_blank\">TinyPNG.com</a></p><p>Оригинальные изображения сохраняются в каталоге /images/original/ и могут быть восстановлены.</p>"; 
$this->content->template['plugin_tinypng_png_count_msg'] = " Изображения можно сжимать."; 
$this->content->template['plugin_tinypng_pngs_processed'] = " Сжатые изображения."; 
$this->content->template['plugin_tinypng_compression'] = "Компрессия"; 
$this->content->template['plugin_tinypng_compression_start'] = "Сжатие изображений"; 
$this->content->template['plugin_tinypng_compression_success_msg'] = "Изображения были сжаты."; 
$this->content->template['plugin_tinypng_restore'] = "Восстановление"; 
$this->content->template['plugin_tinypng_restore_start'] = "Восстановление изображений"; 
$this->content->template['plugin_tinypng_restore_success_msg'] = "Изображения были восстановлены."; 
$this->content->template['plugin_tinypng_error_msg'] = "Во время выполнения операции произошла ошибка."; 
$this->content->template['plugin_tinypng_apikey'] = "API-ключ"; 
$this->content->template['plugin_tinypng_apikey_store'] = "Клавиша сохранения"; 
$this->content->template['plugin_tinypng_apikey_store_success_msg'] = "Ключ API был сохранен."; 
$this->content->template['plugin_tinypng_apikey_purge'] = "Извлеките ключ"; 
$this->content->template['plugin_tinypng_apikey_purge_confirm_js'] = "Вы уверены, что хотите извлечь ключ?"; 
$this->content->template['plugin_tinypng_apikey_purge_success_msg'] = "Ключ API был удален."; 
$this->content->template['plugin_tinypng_apikey_key_required_msg'] = "Требуется ключ API."; 
$this->content->template['plugin_tinypng_no_write_perms_msg'] = "Пожалуйста, установите права доступа к папке /images/ на 777."; 

 ?>