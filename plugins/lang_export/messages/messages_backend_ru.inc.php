<?php 
$this->content->template['message']['plugin']['lang_export']['head'] = "Плагин \"Экспорт языков"; 
$this->content->template['message']['plugin']['lang_export']['start_text'] = "Этот плагин экспортирует языковые данные определенного языка. Создается SQL-файл, который затем может быть импортирован в другую установку Papoo. <br /> Кроме того, создаются 2 CSV-файла, один с пунктами меню, другой с содержанием статей для перевода."; 
$this->content->template['message']['plugin']['lang_export']['start_text2'] = "<h2>Языки копирования</h2><p>Здесь вы можете скопировать языковые данные одного языка в другой язык в качестве шаблона для перевода. <br /><strong>Нет перевода</strong><br /><div style=\"border:1px solid red; padding:5px; background:#ddd;\">Сначала сделайте резервную копию своих данных!!!!</div></p>"; 
$this->content->template['message']['plugin']['lang_export']['form1_legend'] = "Выбор языка"; 
$this->content->template['message']['plugin']['lang_export']['form1_label'] = "Здесь выберите язык для экспорта"; 
$this->content->template['message']['plugin']['lang_export']['formi2_label'] = "Выберите язык, который будет служить в качестве справочника"; 
$this->content->template['message']['plugin']['lang_export']['formi3_label'] = "Выберите здесь язык, который будет заполнен в качестве шаблона"; 
$this->content->template['message']['plugin']['lang_export']['form1_submit'] = "Выберите язык"; 
$this->content->template['message']['plugin']['lang_export']['form1_submit2'] = "Перекопировать языковое содержимое"; 
$this->content->template['message']['plugin']['lang_export']['download_text'] = "По следующей ссылке можно загрузить созданный файл резервной копии"; 
$this->content->template['message']['plugin']['lang_export']['download_link'] = "Резервный файл"; 
$this->content->template['message']['plugin']['lang_export']['download_link_men'] = "Пункты меню файла CSV"; 
$this->content->template['message']['plugin']['lang_export']['download_link_artikel'] = "Статья о файле CSV"; 
$this->content->template['message']['plugin']['lang_export']['form2_legend'] = "Удаление резервной копии"; 
$this->content->template['message']['plugin']['lang_export']['form2_label'] = "В целях безопасности следует удалить созданный SQL-файл после его загрузки"; 
$this->content->template['message']['plugin']['lang_export']['form2_submit'] = "Удаление резервной копии"; 
$this->content->template['message']['plugin']['lang_export']['kopok_text'] = "Данные были перекопированы."; 
$this->content->template['message']['plugin']['lang_export']['end_text'] = "Хорошо и готово :-)"; 

 ?>