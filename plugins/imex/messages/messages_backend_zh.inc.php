<?php 
$this->content->template['message']['plugin']['test']['name'] = "插件 \"测试"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>测试插件的后端</h1><p>这个模板无法访问，但它没有意义，也不符合X-HTML标准。尽管如此，它对于解释Papoo插件的编程应该是有用的。</p><p>这个插件的不同菜单项也是没有意义的。他们总是提到同一个模板 \"test_back.html\"。这些点只是为了显示如何在插件XML文件中创建菜单点。</p><p>前台模板的整合工作如下：创建一个新的菜单项。在 \"链接或文件的整合 \"下输入那里。在底部）输入以下内容：<strong>plugin:test/templates/test_front.html</strong>。现在，模板在前台可用。</p><p>该模板中包含的模块可以通过管理中的模块管理器插入。对于所有还没有发现这个东西的人来说，可以在 \"系统-&gt;模块管理器 \"下找到它。</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "而这里有一个小表格。"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "测试值"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "通过POST传递一个测试值"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "没有选择表"; 

 ?>