<?php 
$this->content->template['plugin_tinypng_head'] = "TinyPNG"; 
$this->content->template['plugin_tinypng_description'] = "<p>This plugin compresses all compressible images from the images directory using the API of <a href=\"https://tinypng.com\" target=\"_blank\">TinyPNG.com</a></p><p>The original images are saved in the /images/original/ directory and can be restored.</p>"; 
$this->content->template['plugin_tinypng_png_count_msg'] = " Images are compressible."; 
$this->content->template['plugin_tinypng_pngs_processed'] = " Compressed images."; 
$this->content->template['plugin_tinypng_compression'] = "Compression"; 
$this->content->template['plugin_tinypng_compression_start'] = "Compress images"; 
$this->content->template['plugin_tinypng_compression_success_msg'] = "Images have been compressed."; 
$this->content->template['plugin_tinypng_restore'] = "Recovery"; 
$this->content->template['plugin_tinypng_restore_start'] = "Restore images"; 
$this->content->template['plugin_tinypng_restore_success_msg'] = "Images have been restored."; 
$this->content->template['plugin_tinypng_error_msg'] = "An error occurred during the operation."; 
$this->content->template['plugin_tinypng_apikey'] = "API Key"; 
$this->content->template['plugin_tinypng_apikey_store'] = "Save key"; 
$this->content->template['plugin_tinypng_apikey_store_success_msg'] = "API key has been saved."; 
$this->content->template['plugin_tinypng_apikey_purge'] = "Remove key"; 
$this->content->template['plugin_tinypng_apikey_purge_confirm_js'] = "Are you sure you want to remove the key?"; 
$this->content->template['plugin_tinypng_apikey_purge_success_msg'] = "API key has been removed."; 
$this->content->template['plugin_tinypng_apikey_key_required_msg'] = "An API key is required."; 
$this->content->template['plugin_tinypng_no_write_perms_msg'] = "Please set the access rights of the /images/ folder to 777."; 

 ?>