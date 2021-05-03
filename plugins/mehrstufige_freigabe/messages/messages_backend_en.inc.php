<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Test"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend of the test plugin</h1><p>This template is not accessible, but it makes no sense and is not X-HTML compliant. Nevertheless, it should be useful for explaining the programming of Papoo plugins.</p><p>The different menu items of this plugin are also meaningless. They always refer to the same template \"test_back.html\". The points are only to show how menu points can be created in the plugin XML file.</p><p>The integration of the frontend template works as follows: Create a new menu item. Enter the following under formlink (at the bottom): <strong>plugin:test/templates/test_front.html</strong>. Now the template is available in the frontend.</p><p>The modules contained in this template can be inserted with the module manager here in the administration. For all those who have not yet discovered the thing, it can be found under \"System -&gt; Module Manager\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "And here's a little form:"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Test value"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Pass a test value via POST"; 

 ?>