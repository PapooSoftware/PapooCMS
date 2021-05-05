<?php 
$this->content->template['plugin_tinypng_head'] = "TinyPNG"; 
$this->content->template['plugin_tinypng_description'] = "<p>Este plugin comprime todas las imágenes comprimibles del directorio de imágenes utilizando la API de <a href=\"https://tinypng.com\" target=\"_blank\">TinyPNG.com</a></p><p>Las imágenes originales se guardan en el directorio /images/original/ y pueden restaurarse.</p>"; 
$this->content->template['plugin_tinypng_png_count_msg'] = " Las imágenes son comprimibles."; 
$this->content->template['plugin_tinypng_pngs_processed'] = " Imágenes comprimidas."; 
$this->content->template['plugin_tinypng_compression'] = "Compresión"; 
$this->content->template['plugin_tinypng_compression_start'] = "Comprimir imágenes"; 
$this->content->template['plugin_tinypng_compression_success_msg'] = "Las imágenes han sido comprimidas."; 
$this->content->template['plugin_tinypng_restore'] = "Recuperación"; 
$this->content->template['plugin_tinypng_restore_start'] = "Restaurar imágenes"; 
$this->content->template['plugin_tinypng_restore_success_msg'] = "Las imágenes han sido restauradas."; 
$this->content->template['plugin_tinypng_error_msg'] = "Se ha producido un error durante la operación."; 
$this->content->template['plugin_tinypng_apikey'] = "Clave API"; 
$this->content->template['plugin_tinypng_apikey_store'] = "Tecla de guardar"; 
$this->content->template['plugin_tinypng_apikey_store_success_msg'] = "Se ha guardado la clave API."; 
$this->content->template['plugin_tinypng_apikey_purge'] = "Quitar la llave"; 
$this->content->template['plugin_tinypng_apikey_purge_confirm_js'] = "¿Estás seguro de que quieres quitar la llave?"; 
$this->content->template['plugin_tinypng_apikey_purge_success_msg'] = "La clave API ha sido eliminada."; 
$this->content->template['plugin_tinypng_apikey_key_required_msg'] = "Se requiere una clave API."; 
$this->content->template['plugin_tinypng_no_write_perms_msg'] = "Por favor, establezca los derechos de acceso de la carpeta /images/ a 777."; 

 ?>