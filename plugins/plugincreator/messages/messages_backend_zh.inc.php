<?php 
$this->content->template['message']['plugin']['plugincreator']['infotext'] = "插件创建者插件可以创建、编辑、重新安装和卸载插件。如果它要创建一个插件，它会创建目录结构，创建并尽可能多地填充XML文件，创建（就可预见的而言）必要的模板、SQL文件、css文件、php文件，并将这些文件填充到类中，以及消息文件。在编辑时，例如XML文件中的模块条目被删除，对于数据库条目，SQL安装和卸载文件中的条目被删除。"; 
$this->content->template['message']['plugin']['plugincreator']['create_backend_expl'] = "在这里你可以创建或--如果适用--编辑一个插件。点击保存，包括文件在内的目录结构--只要你的规格允许--将被创建。"; 
$this->content->template['message']['plugin']['plugincreator']['create_backend_menu_annotation'] = "<p>注意：一个带有你的插件名称的菜单项将被自动创建。在这里，你只能添加或改变子菜单项目。</p>"; 
$this->content->template['message']['plugin']['plugincreator']['loeschenFrage'] = "删除插件包括目录结构真的吗？"; 
$this->content->template['message']['plugin']['plugincreator']['achtungLoeschen'] = "注意：这一步不能逆转！这一步不能逆转。"; 
$this->content->template['message']['plugin']['plugincreator']['deinstallLink'] = "如果你只是想卸载该插件，而不是点击这里"; 
$this->content->template['message']['plugin']['plugincreator']['checkboxCheck'] = "是的，我确信我想不可逆转地删除这个插件。"; 
$this->content->template['message']['plugin']['plugincreator']['rapid_dev_popup_info'] = "在这里，你可以用简单的控制元素点击组合一个表单。 为变量和标签选择一个名称，选择表单元素类型，对于某些元素类型，选择该元素应该有的相应条目，然后点击 \"输入数据库\"。 以这种方式输入的元素可能需要刷新两次页面才能看到。
输入到创建的表格中的数据将被输入到一个自动创建的表格中。 然后，这些数据将自动显示在 \"创建新字段 \"表格下面的表格中。 如果你不希望显示 \"创建新字段 \"和 \"演示数据 \"元素，请从模板中删除它们或者卸载 \"插件创建者 \"插件。"; 

 ?>