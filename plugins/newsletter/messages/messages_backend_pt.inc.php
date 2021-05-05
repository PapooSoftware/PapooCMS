<?php 
$this->content->template['message_20001'] = "Enviar newsletter"; 
$this->content->template['message_20001a'] = "Linguagem "; 
$this->content->template['message_20002'] = "Assunto"; 
$this->content->template['message_20003'] = " Conteúdo da Newsletter "; 
$this->content->template['message_20004'] = "Mensagem de texto alternativa"; 
$this->content->template['message_20005'] = "Configurações da Newsletter"; 
$this->content->template['message_20006'] = " Conteúdo do Impresso:"; 
$this->content->template['message_20007'] = "Conteúdo"; 
$this->content->template['message_20008'] = "Gestão de assinantes da Newsletter"; 
$this->content->template['message_20009'] = "Adicionar um novo assinante"; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "Sim "; 
$this->content->template['message_20012'] = "Não"; 
$this->content->template['message_20013'] = "Ativo"; 
$this->content->template['message_20014'] = "Endereço de e-mail"; 
$this->content->template['message_20015'] = "Email"; 
$this->content->template['news_message_1'] = "<h2>Editar newsletter</h2><p>Você pode editar a newsletter, editar os assinantes e a impressão aqui.</p><p>Se você quiser incluir a newsletter, você pode<br/><ol><li>Crie um item de menu. Ao criá-lo, você pode adicionar manualmente a seguinte entrada em \"Incluir o link ou arquivo\": <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>Se você não quiser ter um item de menu separado, você pode criar um link em qualquer artigo através da função de link no editor. O link deve ter o seguinte conteúdo: /plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html .<br /></li><li>Você também pode usar o gerenciador de módulos para incluir o formulário de assinatura em qualquer lugar que você desejar. <br /></li><li>Além disso, você também pode incluir um arquivo em sua página com o seguinte link em um item de menu:<br /><strong>plugin:newsletter/templates/news_archiv.html</strong></li><li>Com o espaço reservado #Online_Link# você pode criar um link para a entrada do arquivo no site. A ligação correcta será introduzida automaticamente.</li><li>Para a newsletter você pode usar os seguintes marcadores de lugar : #title# (saudação) #name# (sobrenome) #Newsletter_Kuendigen# (link de cancelamento)</li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">A newsletter foi enviada.</h2>"; 
$this->content->template['news_message_3'] = "<h2>Guardar Newsletter</h2><p>Clique em Save Newsletter e todos os dados relevantes da newsletter serão salvos em um arquivo dump. Este armazenamento é independente do armazenamento geral.</p>"; 
$this->content->template['news_message_4'] = "Guardar newsletter"; 
$this->content->template['message_20016'] = "Endereço de e-mail com o qual é enviado:"; 
$this->content->template['message_20016a'] = "Várias configurações"; 
$this->content->template['message_20017'] = "O nome para a parte *de:*:"; 
$this->content->template['message_20018'] = "<p>Pode subscrever a nossa newsletter aqui. Para isso, por favor preencha o formulário abaixo. Você receberá então um e-mail de confirmação, que deverá responder.</p>
<p>Só então você está registrado para a newsletter.</p>"; 
$this->content->template['message_20018_1'] = "Arquivo de newsletters"; 
$this->content->template['message_20018_a'] = "nodecode<h2>:Assine a newsletter.</h2>"; 
$this->content->template['message_20019'] = "Por favor, introduza os seus dados."; 
$this->content->template['message_20020'] = "Subscrever a newsletter"; 
$this->content->template['message_20021'] = "Enviar"; 
$this->content->template['message_20021d'] = "Enviar para a seguinte lista de distribuição"; 
$this->content->template['message_20021c'] = "Pré-visualização"; 
$this->content->template['message_20021a'] = "Correcto"; 
$this->content->template['newsmessage_20122'] = "Adicionar anexos de arquivo"; 
$this->content->template['newsmessage_20122a'] = "Arquivos anexos"; 
$this->content->template['message_20023'] = "Falta o assunto."; 
$this->content->template['message_20024'] = "Criar nova newsletter"; 
$this->content->template['message_20025'] = "Falta a mensagem."; 
$this->content->template['message_20026'] = "Idioma não selecionado."; 
$this->content->template['message_20027'] = "Criar nova lista de distribuição de newsletter"; 
$this->content->template['message_21027'] = "Exibir lista de distribuição no frontend?"; 
$this->content->template['message_21028'] = "Lista de distribuição moderada?"; 
$this->content->template['message_20028'] = "Todos os assinantes incl. listas de distribuição do sistema"; 
$this->content->template['message_20029'] = "Todas as listas de distribuição da newsletter"; 
$this->content->template['message_20030'] = "Listas de distribuição do sistema"; 
$this->content->template['message_20030a'] = " e Flex resultado da pesquisa"; 
$this->content->template['message_20031'] = "Listas de distribuição de boletins"; 
$this->content->template['message_20032'] = "Nenhuma lista de distribuição especificada"; 
$this->content->template['message_20033'] = "Caso a lista de distribuição da newsletter "; 
$this->content->template['message_20034'] = " ser realmente apagado?"; 
$this->content->template['message_20035'] = "Caso a newsletter "; 
$this->content->template['message_20036'] = "Assinantes ativos "; 
$this->content->template['message_20037'] = "Caso o assinante "; 
$this->content->template['message_20038'] = "\"Todas...\" ou listas de distribuição individuais só podem ser seleccionadas"; 
$this->content->template['message_20039'] = "A lista de distribuição \"Teste\" deve ser a única selecionada."; 
$this->content->template['message_20040'] = "\"Assinantes"; 
$this->content->template['message_20041'] = "Você pode configurar a lista de distribuição \"Teste\" para enviar uma newsletter como um teste. Apenas aqueles que você atribuir a esta lista de distribuição receberão o boletim enviado para a lista de distribuição \"Teste\" como uma prévia. A lista de distribuição \"Teste\" não é exibida no frontend, por isso não é possível subscrever esta lista de distribuição no frontend. Os boletins de teste enviados também não são exibidos no arquivo de boletins no frontend."; 
$this->content->template['message_20042'] = "Activar a recepção da newsletter"; 
$this->content->template['message_20043'] = "Desactivar a recepção da newsletter"; 
$this->content->template['message_20044'] = "A letra \"A\" antes da data de login indica um assinante inserido pelo administrador... <br /> A letra \"I\" na frente da data de login indica um assinante que foi adicionado via importação de endereço."; 
$this->content->template['erneut_versenden'] = "Reenviar."; 
$this->content->template['datum'] = "Criado em"; 
$this->content->template['senddate'] = "Enviado para"; 
$this->content->template['kundensuchen'] = "Pesquisar subscritores da newsletter"; 
$this->content->template['useranzahl'] = "# Assine."; 
$this->content->template['gruppe'] = "Lista de distribuição"; 
$this->content->template['newsletter_texthtml'] = "HTML-WYSIWYG"; 
$this->content->template['news_message1'] = "<h2>Selecione um idioma</h2><p>Selecione aqui o idioma no qual uma newsletter será criada.</p>"; 
$this->content->template['news_message2'] = "Selecione"; 
$this->content->template['news_loeschen'] = "Eliminar"; 
$this->content->template['news_loeschene'] = "Apagar esta newsletter"; 
$this->content->template['news_grp_loeschene'] = "Eliminar esta lista de distribuição de newsletter"; 
$this->content->template['news_edit'] = "Editar"; 
$this->content->template['news_edite'] = "Editar esta newsletter"; 
$this->content->template['news_grpname'] = "Lista de distribuição de boletins"; 
$this->content->template['news_grpnamen'] = "Listas de distribuição de boletins"; 
$this->content->template['news_grpdescript'] = "Descrição"; 
$this->content->template['news_grpfehlt'] = "Nenhuma lista de distribuição foi selecionada"; 
$this->content->template['grp_edite'] = "Editar esta lista de distribuição de newsletter"; 
$this->content->template['abo_loeschene'] = "Eliminar este assinante"; 
$this->content->template['abo_edite'] = "Editar configurações do assinante"; 
$this->content->template['message_news_is_del'] = "A entrada foi eliminada com sucesso."; 
$this->content->template['message_news_not_del'] = "Esta lista de distribuição não pode ser editada ou eliminada."; 
$this->content->template['news_imptext1'] = "
-- Para cancelar a inscrição, por favor clique aqui: http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "<hr/>Para cancelar a newsletter, por favor clique aqui: <br /> <a href=\"http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html\" rel=\"unsubscribe nofollow\">Cancelamento da newsletter</a><br />"; 
$this->content->template['news_mail1'] = "Newsletter subscrita pela seitenurl."; 
$this->content->template['news_mail2'] = "Você assinou a newsletter da seitenurl. Se você não assinou esta newsletter ou não a quer, ignore este e-mail, você não receberá mais nenhuma. Para ativar a newsletter, clique no link a seguir"; 
$this->content->template['news_mail3'] = "Um novo assinante se inscreveu em uma ou mais listas moderadas"; 
$this->content->template['news_front1'] = "<h2>Newsletter subscrita</h2><p>Você subscreveu a nossa newsletter. Você deve receber um e-mail com um link de confirmação dentro de alguns minutos.</p><p>Por favor, clique no link no e-mail para finalmente subscrever esta newsletter.</p>"; 
$this->content->template['news_front2'] = "<h2>Boletim informativo </h2><p>A sua subscrição à nossa newsletter foi activada. Você começará a receber nossa newsletter a partir de hoje. Se você deseja cancelar a sua inscrição, basta clicar no link para cancelar a inscrição em qualquer e-mail que você receber de nós.</p>"; 
$this->content->template['news_front3'] = "<h2>Newsletter cancelada</h2>\",<p>'A newsletter foi cancelada e os seus dados foram apagados</p>."; 
$this->content->template['news_front4'] = "Seus detalhes"; 
$this->content->template['news_front5'] = "Sr"; 
$this->content->template['news_front6'] = "Sra"; 
$this->content->template['news_front7'] = "Primeiro nome"; 
$this->content->template['news_front8'] = "Sobrenome"; 
$this->content->template['news_front9'] = "Rua e número da casa"; 
$this->content->template['news_front10'] = "Código Postal"; 
$this->content->template['news_front11'] = "Residência"; 
$this->content->template['news_front12'] = "Idioma"; 
$this->content->template['news_front13'] = "Estado"; 
$this->content->template['news_front14'] = " Especificação em falta"; 
$this->content->template['news_front15'] = " Especificação inválida"; 
$this->content->template['news_front16'] = " já existe. O assinante foi designado para as listas de distribuição selecionadas."; 
$this->content->template['news_front17'] = "Membro do IAKS"; 
$this->content->template['news_front18'] = "alguém assinante"; 
$this->content->template['news_front19'] = "Empresa"; 
$this->content->template['news_show_recipients'] = "Mostrar os endereços de correio para os quais a newsletter foi enviada."; 
$this->content->template['news_message3'] = "Idioma"; 
$this->content->template['message_aboeintragen'] = "Entrar/alterar configurações do assinante"; 
$this->content->template['plugin']['newsletter']['alle'] = "Todos"; 
$this->content->template['plugin']['newsletter']['allow_delete'] = "Se esta mudança for definida, os assinantes são irremediavelmente excluídos (manualmente ou cancelando a assinatura da newsletter), caso contrário, o assinante é simplesmente marcado como excluído e não está mais disponível para processamento. Este último serve a prova exigida por lei."; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "Administração da Newsletter"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "Conteúdo como texto"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "Conteúdo como HTML"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "Dados avançados do utilizador"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "Habilitar seleção de idioma para assinatura de newsletter?"; 
$this->content->template['plugin']['newsletter']['text'] = "Mostrar texto acima do login?"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "Correios HTML?"; 
$this->content->template['plugin']['newsletter']['editor'] = "WYSIWYG Editor tinymce?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "Idioma"; 
$this->content->template['plugin']['newsletter']['daten'] = "Datas."; 
$this->content->template['plugin']['newsletter']['vorname'] = "Primeiro nome"; 
$this->content->template['plugin']['newsletter']['nachname'] = "Sobrenome"; 
$this->content->template['plugin']['newsletter']['strasse'] = "Rua e número da casa"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "Código Postal"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "Residência"; 
$this->content->template['plugin']['newsletter']['staat'] = "Estado"; 
$this->content->template['plugin']['newsletter']['phone'] = "Telefone"; 
$this->content->template['plugin']['newsletter']['speichern'] = "Entre"; 
$this->content->template['plugin']['newsletter']['email'] = "Email"; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "Introduza o ficheiro:"; 
$this->content->template['plugin']['newsletter']['dokument'] = "O documento:"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "Procura..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "Carregar ficheiro:"; 
$this->content->template['plugin']['newsletter']['upload'] = "carregamento"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>Criação de uma cópia de segurança da base de dados</h3><p> Você pode criar um backup da base de dados aqui, que você pode restaurar após uma nova instalação ou em qualquer outro momento.</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "Importar um backup"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "O arquivo de backup foi importado."; 
$this->content->template['plugin']['newsletter']['hinweis'] = "Para importar um backup, por favor seleccione o ficheiro de backup:"; 
$this->content->template['plugin']['newsletter']['warnung'] = "ATENÇÃO - Se você importar um backup, todos os dados atuais serão irremediavelmente excluídos. É, portanto, essencial que você crie um backup antes!"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "Crie um backup agora"; 
$this->content->template['plugin']['newsletter']['anzahlgef'] = "Número de assinantes encontrados:"; 
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = "Número de listas de distribuição encontradas:"; 
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = "Número de newsletters encontradas:"; 
$this->content->template['plugin']['newsletter']['asc'] = "ascendente"; 
$this->content->template['plugin']['newsletter']['desc'] = "decrescente"; 
$this->content->template['plugin']['newsletter']['sort'] = "Classificação"; 
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = "O seu termo de pesquisa"; 
$this->content->template['plugin']['newsletter']['aktivjn'] = "Habilitado"; 
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = "Assinantes da newsletter"; 
$this->content->template['plugin']['newsletter']['Anrede'] = "Saudação"; 
$this->content->template['plugin']['newsletter']['groups'] = "Gestão da lista de distribuição de boletins informativos"; 
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = "O anexo já foi carregado para esta newsletter."; 
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = "Arquivo não encontrado."; 
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = "O nome do arquivo do anexo está faltando."; 
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = "Apagar anexo:"; 
$this->content->template['plugin']['newsletter']['label']['language'] = "Selecione os idiomas que você quer que estejam disponíveis para a assinatura da newsletter."; 
$this->content->template['plugin']['newsletter']['label']['timeout'] = "Proteção de Timeout: número de e-mails enviados de uma só vez em intervalos de 10 segundos"; 
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = "Mostrar anexo em nova janela."; 
$this->content->template['plugin']['newsletter']['linktext']['sync'] = "Caso este registo seja marcado com o Id "; 
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = " ser realmente apagado?"; 
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = "O arquivo foi carregado como um anexo. <br /> Por favor, salve todas as alterações."; 
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = "O anexo foi apagado. <br /> Por favor, salve todas as alterações."; 
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = "Os dados da sua newsletter foram guardados."; 
$this->content->template['plugin']['newsletter']['registration'] = "Inscrição"; 
$this->content->template['plugin']['newsletter']['submit']['cancel'] = "Cancelar"; 
$this->content->template['plugin']['newsletter']['submit']['save'] = "Salvar"; 
$this->content->template['plugin']['newsletter']['submit']['send'] = "Enviar"; 
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = "Nota: O número exibido em cada caso é o número de entradas de assinantes existentes, mas não verificadas, na base de dados. Os endereços de e-mail inválidos e endereços duplicados que possam estar presentes não são enviados. Portanto, o número total de assinantes que recebem a newsletter mostrada na visão geral pode ser diferente dos valores aqui apresentados."; 
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = "Número de e-mails por etapa de envio:"; 
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = "O upload de anexos de arquivo só é possível depois de inserir o assunto e a mensagem."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = "Um ou mais dos seus arquivos são apenas inseridos no BD, mas não podem mais ser encontrados no diretório. Para eliminar o erro, você pode carregar esses arquivos aqui ou via FTP ou excluí-los imediatamente se necessário. Note que os arquivos devem ter o mesmo nome e o mesmo tamanho ao fazer o upload (este último não via FTP)."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = "Editar newsletter"; 
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = "Nota: Os anexos e a impressão que você criou também serão enviados."; 
$this->content->template['plugin']['newsletter']['link']['grp_std'] = "NL Lista de distribuição standard"; 
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = "Lista de distribuição NL standard"; 
$this->content->template['plugin']['newsletter']['used_file'] = "Nome do arquivo"; 
$this->content->template['plugin']['newsletter']['size_text'] = "Tamanho"; 
$this->content->template['plugin']['newsletter']['datum'] = "Data"; 
$this->content->template['plugin']['newsletter']['loeschen3'] = "Eliminar"; 
$this->content->template['plugin']['newsletter']['export'] = "CSV de Exportação"; 
$this->content->template['plugin']['newsletter']['header01'] = "Arquivos carregados"; 
$this->content->template['plugin']['newsletter']['datei_loeschen'] = "Eliminar selecção"; 
$this->content->template['plugin']['newsletter']['das_dokument'] = "O documento:"; 
$this->content->template['plugin']['newsletter']['import_starten'] = "Iniciar importação"; 
$this->content->template['plugin']['newsletter']['datei_hochladen'] = "Carregar arquivo"; 
$this->content->template['plugin']['newsletter']['text03'] = "Se o seu arquivo já existe, você pode apagá-lo agora antes de importar para evitar problemas de upload."; 
$this->content->template['plugin']['newsletter']['text04'] = "A 1ª linha do ficheiro de importação deve conter estes nomes de campo em qualquer ordem: PRIMEIRO NOME, NOME, RUA, ZIP, CIDADE, CORREIO. O arquivo de importação deve ser um arquivo CSV. Os campos devem ser separados com HT (Tab) (x09, t), as linhas devem ser terminadas com CR LF (x0D0A, rn)."; 
$this->content->template['plugin']['newsletter']['datei_importieren'] = "1. Etapa: Importar arquivo"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = "2. Etapa: Importação"; 
$this->content->template['plugin']['newsletter']['liste_waehlen'] = "Por favor, selecione a(s) lista(s) de distribuição"; 
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = "Lista(s) de distribuição vazia(s) na importação?"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = "O arquivo foi carregado com sucesso."; 
$this->content->template['plugin']['newsletter']['importprotokoll'] = "Diário de importação"; 
$this->content->template['plugin']['newsletter']['importprotokoll3'] = "Visão geral dos logs de erros de importação"; 
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = "Registos foram introduzidos."; 
$this->content->template['plugin']['newsletter']['daten_del'] = "Os registos foram apagados."; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = "Nenhum registo introduzido"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = "Registros de dados não inseridos"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = "Visão geral do log de erros de importação"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = "Detalhes do Log de Erros de Importação"; 
$this->content->template['plugin']['newsletter']['report_deleted'] = "Log de erros apagado"; 
$this->content->template['plugin']['newsletter']['id'] = "Id"; 
$this->content->template['plugin']['newsletter']['import_time'] = "Hora"; 
$this->content->template['plugin']['newsletter']['normaler_user'] = "Utilizador"; 
$this->content->template['plugin']['newsletter']['records_to_import'] = "Total #"; 
$this->content->template['plugin']['newsletter']['error_count'] = "Erro #"; 
$this->content->template['plugin']['newsletter']['success_count'] = "Sucesso #"; 
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = "Mostrar detalhes"; 
$this->content->template['plugin']['newsletter']['alttext']['sync'] = "Limpar este log de erros"; 
$this->content->template['plugin']['newsletter']['error_count2'] = "Número total de erros"; 
$this->content->template['plugin']['newsletter']['error_no'] = "Lfd. #"; 
$this->content->template['plugin']['newsletter']['import_file_record_no'] = "Conjunto #"; 
$this->content->template['plugin']['newsletter']['import_file_field_position'] = "Campo #"; 
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = "Excel pos."; 
$this->content->template['plugin']['newsletter']['import_file_field_name'] = "Nome do campo"; 
$this->content->template['plugin']['newsletter']['import_error_msg'] = "Mensagem de erro"; 
$this->content->template['plugin']['newsletter']['completion_code'] = "Código"; 
$this->content->template['plugin']['newsletter']['email_error'] = "Nenhum endereço de e-mail válido"; 
$this->content->template['plugin']['newsletter']['max255_4'] = "O comprimento máximo de entrada de 255 caracteres foi excedido."; 
$this->content->template['plugin']['newsletter']['email_schon_da'] = "Este endereço de e-mail já existe."; 
$this->content->template['plugin']['newsletter']['feldanzahl'] = "Falta um nome de campo: PRIMEIRO NOME, NOME, FORTE, ZIP, CIDADE, CORREIO."; 
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = "Nome de campo errado: PRIMEIRO NOME, NOME, FORTE, ZIP, CIDADE, CORREIO..."; 
$this->content->template['plugin_glossar_dubletten_entfernen'] = "Remover duplas"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_text'] = "Remova os endereços de correio duplicados da base de dados."; 
$this->content->template['plugin_newsletter_dubletten_entfernen_field'] = "Remover duplas"; 
$this->content->template['plugin_newsletter_import'] = "Endereços de importação"; 
$this->content->template['plugin_newsletter_export'] = "Endereços de exportação"; 
$this->content->template['plugin_newsletter_import_text'] = "Endereços de importação (ficheiro CSV)"; 
$this->content->template['plugin_newsletter_export_text'] = "Endereços de exportação (ficheiro CSV)"; 
$this->content->template['plugin_newsletter_inaktive_lschen'] = "Eliminar inactivo"; 
$this->content->template['plugin_newsletter_blacklist_lschen'] = "Eliminar subscritores através da importação da lista negra"; 
$this->content->template['plugin_newsletter_inaktive_lschen_text'] = "Elimina todos os assinantes inativos sem confirmação!"; 
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen'] = "Eliminar assinantes inativos"; 
$this->content->template['plugin_newsletter_inaktive_geloescht'] = "Os assinantes inativos foram excluídos."; 
$this->content->template['plugin_newsletter_dubletten_geloescht'] = "Os endereços de correio duplicados foram eliminados."; 
$this->content->template['newsletter_verteilerliste'] = "Lista de distribuição"; 

 ?>