<?php
/**

Deutsche Text-Daten des Plugins "Skriptor" für das Backend

!! Diese Datei muss im Format "UTF-8 (NoBOM)" gespeichert werden !!!

*/

$this->content->template['message']['plugin']['skriptor']['name'] =
'Plugin "Skriptor"';

$this->content->template['message']['plugin']['skriptor']['kopf'] =
'<h1>Skriptor</h1>'.
'<p>This plugin combines used CSS-files and/or Javascript in order to minimize the number of DNS-lookups. Additionally, the Skripts can be compressed and comments can be removed.</p>';

$this->content->template['message']['plugin']['skriptor']['form_legend'] =
'Options';

$this->content->template['message']['plugin']['skriptor']['form_js_active_label'] =
'combine Javascript';

$this->content->template['message']['plugin']['skriptor']['form_js_compress_label'] =
'compress Javascript';

$this->content->template['message']['plugin']['skriptor']['form_js_remove_comments_label'] =
'remove Javascript comments';

$this->content->template['message']['plugin']['skriptor']['form_css_active_label'] =
'combine CSS';

$this->content->template['message']['plugin']['skriptor']['form_css_compress_label'] =
'compress CSS';

$this->content->template['message']['plugin']['skriptor']['form_css_remove_comments_label'] =
'remove CSS comments';

?>