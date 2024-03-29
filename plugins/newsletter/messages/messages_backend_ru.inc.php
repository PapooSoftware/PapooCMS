<?php 
$this->content->template['message_20001'] = "Отправить информационный бюллетень"; 
$this->content->template['message_20001a'] = "Язык "; 
$this->content->template['message_20002'] = "Тема"; 
$this->content->template['message_20003'] = " Содержание информационного бюллетеня "; 
$this->content->template['message_20004'] = "Альтернативное текстовое сообщение"; 
$this->content->template['message_20005'] = "Настройки рассылки новостей"; 
$this->content->template['message_20006'] = " Содержание оттиска:"; 
$this->content->template['message_20007'] = "Содержание"; 
$this->content->template['message_20008'] = "Управление подписчиками новостной рассылки"; 
$this->content->template['message_20009'] = "Добавить нового абонента"; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "Да "; 
$this->content->template['message_20012'] = "Нет"; 
$this->content->template['message_20013'] = "Активный"; 
$this->content->template['message_20014'] = "Адрес электронной почты"; 
$this->content->template['message_20015'] = "Электронная почта"; 
$this->content->template['news_message_1'] = "<h2>Редактирование информационного бюллетеня</h2><p>Вы можете редактировать рассылку, редактировать подписчиков и оттиск здесь.</p><p>Если вы хотите включить информационный бюллетень, вы можете это сделать<br/><ol><li>Создайте пункт меню. При его создании вы можете вручную добавить следующую запись в разделе \"Включить ссылку или файл.\": <br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>Если вы не хотите иметь отдельный пункт меню, вы можете создать ссылку в любой статье с помощью функции ссылки в редакторе. Ссылка должна иметь следующее содержание: /plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html .<br /></li><li>Вы также можете использовать менеджер модулей для включения формы подписки в любое удобное для вас место. <br /></li><li>Кроме того, вы также можете включить архив на свою страницу со следующей ссылкой в пункте меню:<br /><strong>plugin:newsletter/templates/news_archiv.html</strong></li><li>С помощью #Online_Link# вы можете ссылаться на архивную запись на сайте. Правильная ссылка будет введена туда автоматически.</li><li>Для информационного бюллетеня вы можете использовать следующие заполнители: #title# (приветствие) #name# (фамилия) #Newsletter_Kuendigen# (ссылка на отмену)</li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">Информационный бюллетень был разослан.</h2>"; 
$this->content->template['news_message_3'] = "<h2>Сохранить информационный бюллетень</h2><p>Нажмите на кнопку Сохранить рассылку, и все соответствующие данные рассылки будут сохранены в файле дампа. Это хранилище не зависит от общего хранилища.</p>"; 
$this->content->template['news_message_4'] = "Сохранить информационный бюллетень"; 
$this->content->template['message_20016'] = "Адрес электронной почты, с которого отправляется сообщение:"; 
$this->content->template['message_20016a'] = "Различные настройки"; 
$this->content->template['message_20017'] = "Имя для части *of:*:"; 
$this->content->template['message_20018'] = "<p>Вы можете подписаться на нашу рассылку здесь. Для этого, пожалуйста, заполните приведенную ниже форму. Затем вы получите подтверждение по электронной почте, на которое вы должны ответить.</p>
<p>Только после этого вы будете зарегистрированы для получения рассылки.</p>"; 
$this->content->template['message_20018_1'] = "Архив новостей"; 
$this->content->template['message_20018_a'] = "nodecode<h2>:Подписаться на рассылку новостей.</h2>"; 
$this->content->template['message_20019'] = "Пожалуйста, введите ваши данные."; 
$this->content->template['message_20020'] = "Подписаться на рассылку новостей"; 
$this->content->template['message_20021'] = "Отправить"; 
$this->content->template['message_20021d'] = "Отправить в следующий список рассылки"; 
$this->content->template['message_20021c'] = "Предварительный просмотр"; 
$this->content->template['message_20021a'] = "Правильно"; 
$this->content->template['newsmessage_20122'] = "Добавить вложения файлов"; 
$this->content->template['newsmessage_20122a'] = "Прикрепленные файлы"; 
$this->content->template['message_20023'] = "Объект отсутствует."; 
$this->content->template['message_20024'] = "Создать новый информационный бюллетень"; 
$this->content->template['message_20025'] = "Сообщение отсутствует."; 
$this->content->template['message_20026'] = "Язык не выбран."; 
$this->content->template['message_20027'] = "Создайте новый список рассылки новостей"; 
$this->content->template['message_21027'] = "Отображение списка рассылки во фронтенде?"; 
$this->content->template['message_21028'] = "Список рассылки модерируется?"; 
$this->content->template['message_20028'] = "Все абоненты, включая системные списки рассылки"; 
$this->content->template['message_20029'] = "Все списки рассылки информационных бюллетеней"; 
$this->content->template['message_20030'] = "Списки распределения системы"; 
$this->content->template['message_20030a'] = " и результат поиска Flex"; 
$this->content->template['message_20031'] = "Списки рассылки новостей"; 
$this->content->template['message_20032'] = "Список рассылки не указан"; 
$this->content->template['message_20033'] = "Должен ли список рассылки новостей "; 
$this->content->template['message_20034'] = " действительно будет удален?"; 
$this->content->template['message_20035'] = "Должен ли информационный бюллетень "; 
$this->content->template['message_20036'] = "Активные абоненты "; 
$this->content->template['message_20037'] = "Если абонент "; 
$this->content->template['message_20038'] = "\"Все...\" или отдельные списки рассылки могут быть выбраны только"; 
$this->content->template['message_20039'] = "Список рассылки \"Тест\" должен быть выбран только один."; 
$this->content->template['message_20040'] = "\"Подписчики"; 
$this->content->template['message_20041'] = "Вы можете настроить список рассылки \"Тест\" для отправки информационного бюллетеня в качестве теста. Только те, кого вы назначили в этот список рассылки, получат рассылку, отправленную в список рассылки \"Тест\" в качестве предварительного просмотра. Список рассылки \"Тест\" не отображается во фронтенде, поэтому подписаться на этот список рассылки во фронтенде невозможно. Отправленные тестовые рассылки также не отображаются в архиве рассылок во фронтенде."; 
$this->content->template['message_20042'] = "Активируйте получение новостной рассылки"; 
$this->content->template['message_20043'] = "Деактивировать получение новостей"; 
$this->content->template['message_20044'] = "Буква \"А\" перед датой входа указывает на абонента, введенного администратором... <br /> Буква \"I\" перед датой входа указывает на абонента, который был добавлен через импорт адресов."; 
$this->content->template['erneut_versenden'] = "Отправить."; 
$this->content->template['datum'] = "Создано"; 
$this->content->template['senddate'] = "Отправлено"; 
$this->content->template['kundensuchen'] = "Поиск подписчиков новостной рассылки"; 
$this->content->template['useranzahl'] = "# Subscribe."; 
$this->content->template['gruppe'] = "Список рассылки"; 
$this->content->template['newsletter_texthtml'] = "HTML-WYSIWYG"; 
$this->content->template['news_message1'] = "<h2>Выберите язык</h2><p>Здесь выберите язык, на котором будет создан информационный бюллетень.</p>"; 
$this->content->template['news_message2'] = "Выберите"; 
$this->content->template['news_loeschen'] = "Удалить"; 
$this->content->template['news_loeschene'] = "Удалить эту рассылку"; 
$this->content->template['news_grp_loeschene'] = "Удалить этот список рассылки новостей"; 
$this->content->template['news_edit'] = "Редактировать"; 
$this->content->template['news_edite'] = "Редактировать этот информационный бюллетень"; 
$this->content->template['news_grpname'] = "Список рассылки новостей"; 
$this->content->template['news_grpnamen'] = "Списки рассылки новостей"; 
$this->content->template['news_grpdescript'] = "Описание"; 
$this->content->template['news_grpfehlt'] = "Не выбран список рассылки"; 
$this->content->template['grp_edite'] = "Редактировать этот список рассылки новостей"; 
$this->content->template['abo_loeschene'] = "Удалить этого абонента"; 
$this->content->template['abo_edite'] = "Редактирование настроек абонента"; 
$this->content->template['message_news_is_del'] = "Запись была успешно удалена."; 
$this->content->template['message_news_not_del'] = "Этот список рассылки нельзя редактировать или удалять."; 
$this->content->template['news_imptext1'] = "
-- Чтобы отказаться от подписки, пожалуйста, нажмите здесь: http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "<hr/>Чтобы отменить рассылку, пожалуйста, нажмите здесь: <br /> <a href=\"http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html\" rel=\"unsubscribe nofollow\">Отмена рассылки новостей</a><br />"; 
$this->content->template['news_mail1'] = "Подписчик рассылки - seitenurl."; 
$this->content->template['news_mail2'] = "Вы подписались на рассылку новостей seitenurl. Если вы не подписывались на эту рассылку или не хотите ее получать, проигнорируйте это письмо, и вы больше не будете ее получать. Чтобы активировать рассылку, пожалуйста, нажмите на следующую ссылку"; 
$this->content->template['news_mail3'] = "Новый подписчик подписался на один или несколько модерируемых списков"; 
$this->content->template['news_front1'] = "<h2>Подписка на рассылку новостей</h2><p>Вы подписались на нашу рассылку. Через несколько минут вы получите электронное письмо со ссылкой для подтверждения.</p><p>Пожалуйста, нажмите на ссылку в письме, чтобы окончательно подписаться на эту рассылку.</p>"; 
$this->content->template['news_front2'] = "<h2>Информационный бюллетень </h2><p>Ваша подписка на нашу рассылку активирована. Вы начнете получать нашу рассылку с сегодняшнего дня. Если вы хотите отказаться от подписки, просто нажмите на ссылку \"Отказаться от подписки\" в любом полученном от нас электронном письме.</p>"; 
$this->content->template['news_front3'] = "<h2>Рассылка отменена</h2>',<p>'Рассылка отменена, и ваши данные удалены</p>."; 
$this->content->template['news_front4'] = "ваши данные"; 
$this->content->template['news_front5'] = "Господин"; 
$this->content->template['news_front6'] = "Мисс"; 
$this->content->template['news_front7'] = "Имя"; 
$this->content->template['news_front8'] = "Фамилия"; 
$this->content->template['news_front9'] = "Улица и номер дома"; 
$this->content->template['news_front10'] = "Почтовый индекс"; 
$this->content->template['news_front11'] = "Проживание"; 
$this->content->template['news_front12'] = "Язык"; 
$this->content->template['news_front13'] = "Государство"; 
$this->content->template['news_front14'] = " Спецификация отсутствует"; 
$this->content->template['news_front15'] = " Недопустимая спецификация"; 
$this->content->template['news_front16'] = " уже существует. Абонент был назначен в выбранные списки рассылки."; 
$this->content->template['news_front17'] = "Член IAKS"; 
$this->content->template['news_front18'] = "кто-л. абонент"; 
$this->content->template['news_front19'] = "Компания"; 
$this->content->template['news_show_recipients'] = "Показать почтовые адреса, на которые была отправлена рассылка."; 
$this->content->template['news_message3'] = "Язык"; 
$this->content->template['message_aboeintragen'] = "Ввод/изменение настроек абонента"; 
$this->content->template['plugin']['newsletter']['alle'] = "Все"; 
$this->content->template['plugin']['newsletter']['allow_delete'] = "Если этот переключатель установлен, подписчики удаляются безвозвратно (вручную или путем отписки от рассылки), в противном случае подписчик просто помечается как удаленный и больше не доступен для обработки. Последнее служит доказательством, требуемым по закону."; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "Управление информационными бюллетенями"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "Содержание как текст"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "Содержание как HTML"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "Расширенные пользовательские данные"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "Включить выбор языка для подписки на рассылку?"; 
$this->content->template['plugin']['newsletter']['text'] = "Показать текст над логином?"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "Письма в формате HTML?"; 
$this->content->template['plugin']['newsletter']['editor'] = "WYSIWYG-редактор tinymce?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "Язык"; 
$this->content->template['plugin']['newsletter']['daten'] = "Даты."; 
$this->content->template['plugin']['newsletter']['vorname'] = "Имя"; 
$this->content->template['plugin']['newsletter']['nachname'] = "Фамилия"; 
$this->content->template['plugin']['newsletter']['strasse'] = "Улица и номер дома"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "Почтовый индекс"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "Проживание"; 
$this->content->template['plugin']['newsletter']['staat'] = "Государство"; 
$this->content->template['plugin']['newsletter']['phone'] = "Телефон"; 
$this->content->template['plugin']['newsletter']['speichern'] = "Войти"; 
$this->content->template['plugin']['newsletter']['email'] = "Электронная почта"; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "Введите файл:"; 
$this->content->template['plugin']['newsletter']['dokument'] = "Документ:"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "Просмотреть..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "Загрузите файл:"; 
$this->content->template['plugin']['newsletter']['upload'] = "загрузить"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>Создание резервной копии базы данных</h3><p> Здесь можно создать резервную копию базы данных, которую можно восстановить после новой установки или в любое другое время.</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "Импорт резервной копии"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "Файл резервной копии был импортирован."; 
$this->content->template['plugin']['newsletter']['hinweis'] = "Чтобы импортировать резервную копию, выберите файл резервной копии:"; 
$this->content->template['plugin']['newsletter']['warnung'] = "ВНИМАНИЕ - При импорте резервной копии все текущие данные будут безвозвратно удалены. Поэтому очень важно заранее создать резервную копию!"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "Создайте резервную копию сейчас"; 
$this->content->template['plugin']['newsletter']['anzahlgef'] = "Количество найденных абонентов:"; 
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = "Количество найденных списков рассылки:"; 
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = "Количество найденных информационных бюллетеней:"; 
$this->content->template['plugin']['newsletter']['asc'] = "по возрастанию"; 
$this->content->template['plugin']['newsletter']['desc'] = "по убывающей"; 
$this->content->template['plugin']['newsletter']['sort'] = "Сортировка"; 
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = "Ваш поисковый запрос"; 
$this->content->template['plugin']['newsletter']['aktivjn'] = "Включено"; 
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = "Подписчики новостной рассылки"; 
$this->content->template['plugin']['newsletter']['Anrede'] = "Приветствие"; 
$this->content->template['plugin']['newsletter']['groups'] = "Управление списками рассылки новостей"; 
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = "Вложение уже загружено для этого бюллетеня."; 
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = "Файл не найден."; 
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = "Имя файла вложения отсутствует."; 
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = "Удалить вложение:"; 
$this->content->template['plugin']['newsletter']['label']['language'] = "Выберите языки, которые вы хотите сделать доступными для подписки на рассылку."; 
$this->content->template['plugin']['newsletter']['label']['timeout'] = "Защита по таймауту: количество писем, отправляемых одновременно с интервалом в 10 секунд"; 
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = "Показать вложение в новом окне."; 
$this->content->template['plugin']['newsletter']['linktext']['sync'] = "Должна ли эта запись быть помечена Id "; 
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = " действительно будет удален?"; 
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = "Файл был загружен как вложение. <br /> Пожалуйста, сохраните все изменения."; 
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = "Вложение было удалено. <br /> Пожалуйста, сохраните все изменения."; 
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = "Данные вашей рассылки сохранены."; 
$this->content->template['plugin']['newsletter']['registration'] = "Регистрация"; 
$this->content->template['plugin']['newsletter']['submit']['cancel'] = "Отмена"; 
$this->content->template['plugin']['newsletter']['submit']['save'] = "Сохранить"; 
$this->content->template['plugin']['newsletter']['submit']['send'] = "Отправить"; 
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = "Примечание: Число, отображаемое в каждом случае, - это число существующих, но не отмеченных записей абонентов в базе данных. Недействительные адреса электронной почты и дублирующие адреса, которые могут присутствовать, не рассылаются. Поэтому общее количество подписчиков, получающих рассылку, указанное в обзоре, может отличаться от приведенных здесь значений."; 
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = "Количество электронных писем на каждом этапе отправки:"; 
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = "Загрузка вложений файлов возможна только после ввода темы и сообщения."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = "Один или несколько ваших файлов только внесены в БД, но больше не могут быть найдены в каталоге. Чтобы устранить ошибку, вы можете загрузить эти файл(ы) здесь или через FTP или сразу удалить их, если это необходимо. Обратите внимание, что при загрузке файлы должны иметь одинаковое имя и одинаковый размер (последнее не через FTP)."; 
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = "Редактирование информационного бюллетеня"; 
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = "Примечание: Вложения и созданный вами отпечаток также будут отправлены."; 
$this->content->template['plugin']['newsletter']['link']['grp_std'] = "NL Стандарт списка распределения"; 
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = "Стандартный список рассылки NL"; 
$this->content->template['plugin']['newsletter']['used_file'] = "Имя файла"; 
$this->content->template['plugin']['newsletter']['size_text'] = "Размер"; 
$this->content->template['plugin']['newsletter']['datum'] = "Дата"; 
$this->content->template['plugin']['newsletter']['loeschen3'] = "Удалить"; 
$this->content->template['plugin']['newsletter']['export'] = "Экспорт CSV"; 
$this->content->template['plugin']['newsletter']['header01'] = "Загруженные файлы"; 
$this->content->template['plugin']['newsletter']['datei_loeschen'] = "Удалить выбор"; 
$this->content->template['plugin']['newsletter']['das_dokument'] = "Документ:"; 
$this->content->template['plugin']['newsletter']['import_starten'] = "Начать импорт"; 
$this->content->template['plugin']['newsletter']['datei_hochladen'] = "Загрузить файл"; 
$this->content->template['plugin']['newsletter']['text03'] = "Если ваш файл уже существует, вы можете удалить его сейчас перед импортом, чтобы избежать проблем с загрузкой."; 
$this->content->template['plugin']['newsletter']['text04'] = "Первая строка файла импорта должна содержать имена этих полей в любом порядке: ФАМИЛИЯ, ИМЯ, УЛИЦА, ПОЧТОВЫЙ ИНДЕКС, ГОРОД, ПОЧТА. Файл импорта должен быть CSV-файлом. Поля должны быть разделены с помощью HT (Tab) (x09, t), строки должны быть завершены CR LF (x0D0A, rn)."; 
$this->content->template['plugin']['newsletter']['datei_importieren'] = "1. Шаг: Импорт файла"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = "2. Шаг: Импорт"; 
$this->content->template['plugin']['newsletter']['liste_waehlen'] = "Пожалуйста, выберите список (списки) рассылки"; 
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = "Пустой список(и) рассылки при импорте?"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = "Файл был успешно загружен."; 
$this->content->template['plugin']['newsletter']['importprotokoll'] = "Журнал импорта"; 
$this->content->template['plugin']['newsletter']['importprotokoll3'] = "Обзор журналов регистрации ошибок импорта"; 
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = "Записи были введены."; 
$this->content->template['plugin']['newsletter']['daten_del'] = "Записи были удалены."; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = "Записи не введены"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = "Не введены записи данных"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = "Обзор журнала ошибок импорта"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = "Подробности журнала ошибок импорта"; 
$this->content->template['plugin']['newsletter']['report_deleted'] = "Журнал ошибок удален"; 
$this->content->template['plugin']['newsletter']['id'] = "Id"; 
$this->content->template['plugin']['newsletter']['import_time'] = "Время"; 
$this->content->template['plugin']['newsletter']['normaler_user'] = "Пользователь"; 
$this->content->template['plugin']['newsletter']['records_to_import'] = "Всего #"; 
$this->content->template['plugin']['newsletter']['error_count'] = "Ошибка #"; 
$this->content->template['plugin']['newsletter']['success_count'] = "Успех #"; 
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = "Показать подробности"; 
$this->content->template['plugin']['newsletter']['alttext']['sync'] = "Очистить этот журнал ошибок"; 
$this->content->template['plugin']['newsletter']['error_count2'] = "Общее количество ошибок"; 
$this->content->template['plugin']['newsletter']['error_no'] = "Lfd. #"; 
$this->content->template['plugin']['newsletter']['import_file_record_no'] = "Набор #"; 
$this->content->template['plugin']['newsletter']['import_file_field_position'] = "Поле #"; 
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = "Excel поз."; 
$this->content->template['plugin']['newsletter']['import_file_field_name'] = "Название поля"; 
$this->content->template['plugin']['newsletter']['import_error_msg'] = "Сообщение об ошибке"; 
$this->content->template['plugin']['newsletter']['completion_code'] = "Код"; 
$this->content->template['plugin']['newsletter']['email_error'] = "Нет действующего адреса электронной почты"; 
$this->content->template['plugin']['newsletter']['max255_4'] = "Превышена максимальная длина ввода 255 символов."; 
$this->content->template['plugin']['newsletter']['email_schon_da'] = "Этот адрес электронной почты уже существует."; 
$this->content->template['plugin']['newsletter']['feldanzahl'] = "Отсутствует имя поля: FIRST NAME, NAME, STREET, ZIP, CITY, MAIL."; 
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = "Неправильное имя поля: FIRST NAME, NAME, STREET, ZIP, CITY, MAIL..."; 
$this->content->template['plugin_glossar_dubletten_entfernen'] = "Удаление дублеров"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_text'] = "Удаление дубликатов почтовых адресов из базы данных."; 
$this->content->template['plugin_newsletter_dubletten_entfernen_field'] = "Удаление дублеров"; 
$this->content->template['plugin_newsletter_import'] = "Импорт адресов"; 
$this->content->template['plugin_newsletter_export'] = "Экспорт адресов"; 
$this->content->template['plugin_newsletter_import_text'] = "Импорт адресов (файл CSV)"; 
$this->content->template['plugin_newsletter_export_text'] = "Экспорт адресов (файл CSV)"; 
$this->content->template['plugin_newsletter_inaktive_lschen'] = "Удалить неактивные"; 
$this->content->template['plugin_newsletter_blacklist_lschen'] = "Удаление абонентов через импорт черного списка"; 
$this->content->template['plugin_newsletter_inaktive_lschen_text'] = "Удаляет всех неактивных абонентов без подтверждения!"; 
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen'] = "Удаление неактивных абонентов"; 
$this->content->template['plugin_newsletter_inaktive_geloescht'] = "Неактивные абоненты были удалены."; 
$this->content->template['plugin_newsletter_dubletten_geloescht'] = "Дублирующиеся почтовые адреса были удалены."; 
$this->content->template['newsletter_verteilerliste'] = "Список рассылки"; 

 ?>