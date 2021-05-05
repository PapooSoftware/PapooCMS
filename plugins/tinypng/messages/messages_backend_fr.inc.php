<?php 
$this->content->template['plugin_tinypng_head'] = "TinyPNG"; 
$this->content->template['plugin_tinypng_description'] = "<p>Ce plugin compresse toutes les images compressibles du répertoire images en utilisant l'API de <a href=\"https://tinypng.com\" target=\"_blank\">TinyPNG.com</a></p><p>Les images originales sont enregistrées dans le répertoire /images/original/ et peuvent être restaurées.</p>"; 
$this->content->template['plugin_tinypng_png_count_msg'] = " Les images sont compressibles."; 
$this->content->template['plugin_tinypng_pngs_processed'] = " Images comprimées."; 
$this->content->template['plugin_tinypng_compression'] = "Compression"; 
$this->content->template['plugin_tinypng_compression_start'] = "Compresser les images"; 
$this->content->template['plugin_tinypng_compression_success_msg'] = "Les images ont été compressées."; 
$this->content->template['plugin_tinypng_restore'] = "Récupération"; 
$this->content->template['plugin_tinypng_restore_start'] = "Restaurer les images"; 
$this->content->template['plugin_tinypng_restore_success_msg'] = "Les images ont été restaurées."; 
$this->content->template['plugin_tinypng_error_msg'] = "Une erreur s'est produite pendant l'opération."; 
$this->content->template['plugin_tinypng_apikey'] = "Clé API"; 
$this->content->template['plugin_tinypng_apikey_store'] = "Touche de sauvegarde"; 
$this->content->template['plugin_tinypng_apikey_store_success_msg'] = "La clé API a été enregistrée."; 
$this->content->template['plugin_tinypng_apikey_purge'] = "Retirer la clé"; 
$this->content->template['plugin_tinypng_apikey_purge_confirm_js'] = "Vous êtes sûr de vouloir retirer la clé ?"; 
$this->content->template['plugin_tinypng_apikey_purge_success_msg'] = "La clé API a été supprimée."; 
$this->content->template['plugin_tinypng_apikey_key_required_msg'] = "Une clé API est nécessaire."; 
$this->content->template['plugin_tinypng_no_write_perms_msg'] = "Veuillez définir les droits d'accès du dossier /images/ à 777."; 

 ?>