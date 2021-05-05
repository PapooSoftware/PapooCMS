<?php 
$this->content->template['plugin']['devtools']['version_clean'] = "Удаление версий статей"; 
$this->content->template['plugin']['devtools']['text1'] = "Этот инструмент удаляет все записи из таблиц версий статей."; 
$this->content->template['plugin']['devtools']['text1_b'] = "Удаление только записей из &quot;текущего редактирования&quot;. Эти записи иногда остаются ненужными, особенно когда разные люди редактируют одну и ту же статью, не сохраняя ее."; 
$this->content->template['plugin']['devtools']['warnung'] = "Внимание:<br />Эта функция не является обратимой! Его следует использовать только в том случае, если вы уверены в своих действиях."; 
$this->content->template['plugin']['devtools']['version_delete'] = "Удаление версий"; 
$this->content->template['plugin']['devtools']['version_deleted'] = "версии были удалены."; 
$this->content->template['plugin']['devtools']['neu_ordnung'] = "Повторный заказ"; 
$this->content->template['plugin']['devtools']['menue_neu'] = "<h2>Переупорядочить менюЗдесь</h2>можно изменить сортировку пунктов меню. Затем сортировка выполняется в соответствии со временем создания пункта меню. Структура меню (подменю и т.д.) сохраняется "; 
$this->content->template['plugin']['devtools']['menue_ordnen'] = " Перестановка пунктов меню "; 
$this->content->template['plugin']['devtools']['artikel_neu'] = "<h2>Изменить порядок статейЗдесь</h2>можно изменить порядок сортировки статей. Сортировка производится в соответствии со временем создания статьи. Распределение статей по пунктам меню остается прежним."; 
$this->content->template['plugin']['devtools']['artikel_ordnen'] = "Переупорядочить статьи"; 
$this->content->template['plugin']['devtools']['bilder_neu'] = "<h2>Переназначение изображений и файловЗдесь</h2>вы можете переназначить статьи и файлы после обновления. После этого они не будут отнесены ни к каким категориям, что сделает их снова видимыми."; 
$this->content->template['plugin']['devtools']['bilder_ordnen'] = "Упорядочивание изображений и файлов"; 
$this->content->template['plugin']['devtools']['link_neu'] = "Изменение порядка категорий ссылок (плагин)"; 
$this->content->template['plugin']['devtools']['link_ordnen'] = "Ссылка Переупорядочить категории"; 
$this->content->template['plugin']['devtools']['datensicherung'] = "Резервное копирование данных"; 
$this->content->template['plugin']['devtools']['datei_loeschen'] = "Удалить файл."; 
$this->content->template['plugin']['devtools']['text2'] = "В целях безопасности после загрузки следует удалить файл резервной копии на сервере."; 
$this->content->template['plugin']['devtools']['text3'] = "Здесь вы можете создать файл резервной копии любой таблицы базы данных papoo."; 
$this->content->template['plugin']['devtools']['aktiv_deaktiv'] = "Активировать / деактивировать все таблицы."; 
$this->content->template['plugin']['devtools']['daten_ohne_speichern'] = "Храните данные без структуры."; 
$this->content->template['plugin']['devtools']['text4'] = "Если вы хотите сохранить только данные таблицы (таблиц) без их структуры, активируйте следующую опцию:"; 
$this->content->template['plugin']['devtools']['daten_speichern'] = "хранить данные"; 
$this->content->template['plugin']['devtools']['nur_daten_speichern'] = "Храните только данные (без структуры)."; 
$this->content->template['plugin']['devtools']['tabellen_speichern'] = "Сохранить выбранные таблицы"; 
$this->content->template['plugin']['devtools']['name'] = "Модули языковых записей"; 
$this->content->template['plugin']['devtools']['intro'] = "Этот небольшой плагин устанавливает &quot;имена и описания модулей&quot; для языков, где эти записи еще не существуют. Этот модуль фактически нужен только при определении нового языка."; 
$this->content->template['plugin']['devtools']['submit'] = "Создайте названия и описания модулей."; 
$this->content->template['plugin']['devtools']['submit2'] = "Создайте модуль и описания."; 
$this->content->template['plugin']['devtools']['erfolg'] = "Записи в таблице &quot;papoo_module_language&quot; были созданы."; 
$this->content->template['plugin']['devtools']['text5'] = "<h1>Инструменты разработчика</h1><p>Этот плагин содержит коллекцию полезных инструментов, которые весьма полезны при разработке плагинов для papoo. <br /><br /> Стефан желает вам много удовольствия от работы с ним.</p>"; 
$this->content->template['plugin']['devtools']['debug_optionen'] = "Параметры отладки"; 
$this->content->template['plugin']['devtools']['text6'] = "<p>Этот инструмент предоставляет некоторые опции отладки для разработки плагинов.</p>"; 
$this->content->template['plugin']['devtools']['text6a'] = "<h2>Автоматическое перенаправление при установке и удалении плагинов.</h2><p>Обычно автоматическое перенаправление происходит во время установки и удаления плагинов. Это перенаправление глупо при определенных обстоятельствах, поскольку любые сообщения об ошибках, которые могут возникнуть, не отображаются.</p>"; 
$this->content->template['plugin']['devtools']['weiterleitung_de'] = "Отключите переадресацию."; 
$this->content->template['plugin']['devtools']['einstellung_speichern'] = "Сохранить настройки"; 
$this->content->template['plugin']['devtools']['text7'] = "<h2>Удалите все записи плагина.</h2><p>Эта функция &quot;кувалды&quot; удаляет ВСЕ записи из таблиц papoo, которые были созданы плагинами, включая сами инструменты разработчика. Он предназначен для очистки во время или после интенсивной разработки. <br /> Таблицы, принадлежащие плагинам, остаются незатронутыми этой функцией.</p>"; 
$this->content->template['plugin']['devtools']['eintrag_entfernen'] = "Удалить все записи"; 
$this->content->template['plugin']['devtools']['text8'] = "<h2>Выключите или включите все автоматические переадресации.</h2><p>Здесь ВСЕ автоматические переадресации могут быть переключены на &quot;ручную переадресацию&quot;. На самом деле это интересно только для разработчиков на ядре Papoo.</p>"; 
$this->content->template['plugin']['devtools']['alle_weiterleitung_de'] = "Отключите все перенаправления."; 
$this->content->template['plugin']['devtools']['text9'] = "<h2>Установите права доступа к статьям для администраторов.</h2><p>При определенных обстоятельствах может случиться так, что администраторы не имеют прав на редактирование (и чтение) статей. Эта небольшая функция устанавливает необходимые права в статьях для группы &quot;Администратор&quot;.</p>"; 
$this->content->template['plugin']['devtools']['rechte_setzen'] = "Установить права"; 
$this->content->template['plugin']['devtools']['cache_cleaner'] = "Очиститель кэша"; 
$this->content->template['plugin']['devtools']['text10'] = "Этот инструмент удаляет различные файлы кэша papoo."; 
$this->content->template['plugin']['devtools']['cache_loeschen'] = "Удаление файлов кэша"; 
$this->content->template['plugin']['devtools']['cache_deleted'] = "Кэш был успешно очищен."; 
$this->content->template['plugin']['devtools']['automatik_de'] = "Автоматическая пересылка менеджера плагинов была деактивирована"; 
$this->content->template['plugin']['devtools']['automatik_re'] = "Автоматическая переадресация менеджера плагинов снова включена."; 
$this->content->template['plugin']['devtools']['alle_auto_de'] = "все автоматические переадресации были деактивированы"; 
$this->content->template['plugin']['devtools']['alle_auto_re'] = "вся автоматическая переадресация была снова включена."; 
$this->content->template['plugin']['devtools']['weiter'] = "Перейти к"; 
$this->content->template['plugin']['devtools']['rechte_admin'] = "Разрешения для группы &quot;Adminstrator&quot; были установлены."; 
$this->content->template['plugin']['devtools']['sicherung_deleted'] = "Файл резервной копии был удален."; 
$this->content->template['plugin']['devtools']['sicherung_download'] = "Здесь вы можете загрузить файл резервной копии."; 
$this->content->template['plugin']['devtools']['menue_ok'] = "Пункты меню были переставлены."; 
$this->content->template['plugin']['devtools']['artikel_ok'] = "Статьи были переставлены местами."; 
$this->content->template['plugin']['devtools']['bilder_ok'] = "Изображения и файлы были переставлены."; 
$this->content->template['plugin']['devtools']['kategorie_ok'] = "Перестановка категорий."; 
$this->content->template['plugin']['devtools']['need_admin'] = "Эти функции требуют членства в группе &quot;Администраторы&quot;"; 
$this->content->template['plugin']['devtools']['surls_neu'] = "Создайте говорящие урлы для существующих статей и пунктов меню."; 
$this->content->template['plugin']['devtools']['surls'] = "Создавайте урлы."; 
$this->content->template['plugin']['devtools']['surls_text'] = "Если вы произвели обновление, существующие URL-адреса должны быть преобразованы; вы делаете это, нажав на кнопку . <br /> Если URL-адреса полностью разрушены, вы также можете сохранить их здесь снова "; 
$this->content->template['plugin']['devtools']['surls_ok'] = "Урлы были успешно созданы."; 
$this->content->template['plugin']['devtools']['neu_modul_h3'] = "Создать новый модуль"; 
$this->content->template['plugin']['devtools']['neu_modul_text'] = "Здесь вы можете создать новый модуль, ввести необходимые данные. Обратите внимание, что это не может быть отменено!!!"; 
$this->content->template['plugin']['devtools']['neu_modul_legend'] = "Введите данные здесь"; 
$this->content->template['plugin']['devtools']['neu_modul_name'] = "Название модуля"; 
$this->content->template['plugin']['devtools']['neu_modul_rel'] = "Имя и рел. путь к файлу"; 
$this->content->template['plugin']['devtools']['neu_modul_des'] = "Краткое описание"; 
$this->content->template['plugin']['devtools']['Felder_erstellen'] = "Создание новых полей"; 
$this->content->template['plugin']['devtools']['text_db_charset_setzen'] = "<h2>Установите набор символов БД.</h2><p>При преобразовании базы данных в UTF-8 сортировка по алфавиту может выводиться некорректно. Установка таблиц базы данных в CHARACTER SET &quot;utf8&quot; COLLATION &quot;utf8_general_ci&quot; должна исправить это.</p>"; 
$this->content->template['plugin']['devtools']['db_charset_setzen'] = "Установите набор символов БД"; 
$this->content->template['plugin']['devtools']['fin_db_charset_setzen'] = "<p>.. Установлен набор символов DB.</p>"; 
$this->content->template['plugin']['devtools']['text_article_searchfield'] = "<h2>Инициализация поля поиска статей.</h2><p>Эта функция инициализирует поле поиска в таблице papoo_language_article. Это поле используется для полнотекстового поиска.</p>"; 
$this->content->template['plugin']['devtools']['set_article_searchfield'] = "Инициализация поля поиска статей"; 
$this->content->template['plugin']['devtools']['fin_article_searchfield'] = "<p>.. Поле поиска элементов было инициализировано.</p>"; 
$this->content->template['plugin']['devtools']['autocontent'] = "Генерируйте контент автоматически"; 
$this->content->template['plugin']['devtools']['autocontent_article_intro'] = "<h2>Создание пустых статей на основе структуры меню</h2><p>Для каждого пункта меню, для которого еще нет статьи, создается статья на активном в данный момент языке.</p>"; 
$this->content->template['plugin']['devtools']['autocontent_article_submit'] = "Создать статью"; 
$this->content->template['plugin']['devtools']['autocontent_article_fin'] = "<p>.. Статьи были созданы</p>"; 
$this->content->template['plugin']['devtools']['rechte_neu'] = "Сбросить права."; 
$this->content->template['plugin']['devtools']['rechte_text'] = "Если вы нажмете на кнопку, права на чтение и запись для всех статей и пунктов меню будут установлены на главного редактора."; 
$this->content->template['plugin']['devtools']['rechte'] = "Установите права сейчас."; 
$this->content->template['plugin']['devtools']['rights_ok'] = "Все права прекращены"; 
$this->content->template['plugin']['devtools']['text_article_seitenbaum'] = "<h2>Сделать статьи видимыми во внутреннем дереве страниц</h2><p></p>Если статьи не отображаются во внутреннем дереве страниц (в разделе \"Содержание\"), вы можете попробовать сделать их scihtbar с помощью этого. Это может произойти при обновлении с очень старых версий Papoo.</p>"; 
$this->content->template['plugin']['devtools']['set_article_seitenbaum'] = "Сделайте статьи видимыми."; 
$this->content->template['plugin']['devtools']['free_urls__sprach_neu'] = "Установите языковое сокращение для всех свободных ссылок."; 
$this->content->template['plugin']['devtools']['free_urls__sprach_neu2'] = "Это устанавливает свободные URL на /en/ /fr/ и т.д. для всех статей, кроме языка по умолчанию и только если система многоязычная. Он также заменяет ссылки во всех данных HTML (статьи, 3-я колонка и бесплатные модули)."; 
$this->content->template['plugin']['devtools']['free_urls_ok'] = "Все адреса были установлены"; 
$this->content->template['plugin']['devtools']['bilder_alts'] = "Alt и Title описания из отображения статей."; 
$this->content->template['plugin']['devtools']['bilder_alts2'] = "При этом подходящие картинки ищутся из всех статей на всех языках в каждом конкретном случае и указываются с точностью до языка."; 
$this->content->template['plugin']['devtools']['bilder_alts3'] = "Просмотр данных изображения."; 
$this->content->template['plugin']['devtools']['reorder_action_alt_images'] = "Все данные были отображены"; 
$this->content->template['plugin']['devtools']['reorder_plugin_text'] = "<h2>Изменение порядка следования пунктов меню плагина</h2><p>Здесь вы можете изменить порядок расположения пунктов меню плагинов. Это \"только\" влияет на порядок отображения в меню внутренних настроек.</p>"; 
$this->content->template['plugin']['devtools']['reorder_plugin_button'] = "Переупорядочить плагины"; 
$this->content->template['plugin']['devtools']['menu_artikel_rechte'] = "Установка прав для статей и пунктов меню"; 
$this->content->template['plugin']['devtools']['menu_artikel_rechte2'] = "Это устанавливает права редактирования для всех статей и пунктов меню на \"Главный редактор\"."; 
$this->content->template['plugin']['devtools']['art_men_chef_ok'] = "Права были установлены."; 
$this->content->template['plugin']['devtools']['dl_rechte_jeder'] = "Распределите права на загрузку среди группы \"все\"."; 
$this->content->template['plugin']['devtools']['dl_rechte_jeder2'] = "Это устанавливает права доступа для всех загрузок на всех."; 
$this->content->template['plugin']['devtools']['dl_rechte_jeder_ok'] = "Права были установлены."; 
$this->content->template['plugin']['devtools']['gen_article_language_entries_msg'] = "<h2>Генерация языковых записей для статей на всех языках</h2><p>Когда в процессе работы добавляются новые языки, статьи с других языков могут быть не видны на этих языках. Эта функция копирует языковые записи с других языков, чтобы статьи на новых языках были видны в разделе Содержание.</p>"; 
$this->content->template['plugin']['devtools']['gen_article_language_entries_btn'] = "Создание голосовых записей."; 
$this->content->template['plugin']['devtools']['dbcleanup']['h1'] = "Очистка базы данных"; 
$this->content->template['plugin']['devtools']['dbcleanup']['explain'] = "<p>Эта функция <strong>без предупреждения</strong> удаляет из базы данных все осиротевшие таблицы больше не установленных плагинов.</p><p>Поэтому, если вы не знаете, что именно вы здесь делаете, не нажимайте на кнопку вообще!</p>"; 
$this->content->template['plugin']['devtools']['dbcleanup']['success'] = "<p>Все осиротевшие таблицы были удалены.</p>"; 

 ?>