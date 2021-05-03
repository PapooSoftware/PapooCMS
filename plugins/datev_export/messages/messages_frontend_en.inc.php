<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"test\""; 
$this->content->template['message']['plugin']['test']['text'] = "<h3>This is a little text.</h3>
You can use HTML-tags in the definition of text-data.

Be sure you have the following rules in mind:
<ul><li>Line-breaks are converted to &lt;br /&gt;s.
</li><li>The file(s) must be saved as \"UTF-8 (no BOM)\".
</li></ul>"; 
$this->content->template['message']['plugin']['test']['test_text'] = "Hello world!"; 

 ?>