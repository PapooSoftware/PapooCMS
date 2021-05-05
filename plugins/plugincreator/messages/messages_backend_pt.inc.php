<?php 
$this->content->template['message']['plugin']['plugincreator']['infotext'] = "O plugin do Plugin Creator pode criar, editar, reinstalar e desinstalar plugins. Se for para criar um plugin, ele cria a estrutura do diretório, cria e preenche o arquivo XML com o máximo de dados possíveis, cria os modelos (tanto quanto possível) necessários, os arquivos SQL, o arquivo css, o arquivo php e os preenche com a classe, e os arquivos de mensagens. Ao editar, as entradas do módulo no arquivo XML são excluídas, por exemplo, para as entradas da base de dados as entradas nos arquivos de instalação e desinstalação do SQL."; 
$this->content->template['message']['plugin']['plugincreator']['create_backend_expl'] = "Aqui você pode criar ou - se aplicável - editar um plugin. Clique em salvar, a estrutura do diretório incluindo arquivos - tanto quanto suas especificações permitirem - será criada."; 
$this->content->template['message']['plugin']['plugincreator']['create_backend_menu_annotation'] = "<p>Nota: Um item de menu com o nome do seu plugin será criado automaticamente. Aqui você só pode adicionar ou alterar itens de submenu.</p>"; 
$this->content->template['message']['plugin']['plugincreator']['loeschenFrage'] = "Apagar plugin, incluindo a estrutura do diretório, realmente?"; 
$this->content->template['message']['plugin']['plugincreator']['achtungLoeschen'] = "ATENÇÃO: Este passo não pode ser revertido!"; 
$this->content->template['message']['plugin']['plugincreator']['deinstallLink'] = "Se você quiser apenas desinstalar o plugin, clique aqui"; 
$this->content->template['message']['plugin']['plugincreator']['checkboxCheck'] = "Sim, tenho a certeza que quero apagar este plugin de forma irrevogável."; 
$this->content->template['message']['plugin']['plugincreator']['rapid_dev_popup_info'] = "Aqui você pode clicar em um formulário usando elementos de controle simples. Selecione um nome para a variável e a etiqueta, o tipo de elemento do formulário e para certos tipos de elemento as respectivas entradas que o elemento deve ter e clique em \"Entrar na base de dados\". Elementos inseridos desta forma podem requerer 2 páginas de atualização para se tornarem visíveis.
Os dados inseridos no formulário criado serão inseridos em uma tabela criada automaticamente. Estes dados serão então exibidos automaticamente em uma tabela abaixo do formulário \"criar novos campos\". Se você não quiser que os elementos \"criar novos campos\" e \"dados demo\" sejam exibidos, exclua-os do modelo ou desinstale o plugin \"plugin creator\"."; 

 ?>