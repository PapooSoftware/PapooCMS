<?php 
$this->content->template['message']['plugin']['test']['name'] = "Plugin \"Teste"; 
$this->content->template['message']['plugin']['test']['kopf'] = "<h1>Backend do plugin de teste</h1><p>Este template não é acessível, mas não faz sentido e não é compatível com X-HTML. No entanto, deve ser útil para explicar a programação dos plugins Papoo.</p><p>Os diferentes itens do menu deste plugin também não têm sentido. Eles referem-se sempre ao mesmo modelo \"test_back.html\". Os pontos são apenas para mostrar como os pontos de menu podem ser criados no ficheiro XML do plugin.</p><p>A integração do modelo front-end funciona da seguinte forma: Criar um novo item de menu. Entrar lá em \"Integração do link ou arquivo\" (na parte inferior) digite o seguinte: <strong>plugin:test/templates/test_front.html</strong>. Agora o modelo está disponível no frontend.</p><p>Os módulos contidos neste modelo podem ser inseridos com o administrador do módulo aqui na administração. Para todos aqueles que ainda não descobriram a coisa, ela pode ser encontrada em \"Sistema -&gt; Gerenciador de Módulos\".</p>"; 
$this->content->template['message']['plugin']['test']['form_kopf'] = "E aqui está uma pequena forma:"; 
$this->content->template['message']['plugin']['test']['form_legend'] = "Valor do teste"; 
$this->content->template['message']['plugin']['test']['form_testwert_label'] = "Passar um valor de teste via POST"; 
$this->content->template['message']['plugin']['test']['no_table_selected'] = "Nenhuma tabela selecionada"; 

 ?>