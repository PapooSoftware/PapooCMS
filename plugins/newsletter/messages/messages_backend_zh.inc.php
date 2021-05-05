<?php 
$this->content->template['message_20001'] = "发送通讯"; 
$this->content->template['message_20001a'] = "语言。 "; 
$this->content->template['message_20002'] = "主题"; 
$this->content->template['message_20003'] = "通讯内容。 "; 
$this->content->template['message_20004'] = "替代文本信息"; 
$this->content->template['message_20005'] = "通讯设置"; 
$this->content->template['message_20006'] = "版本说明内容。"; 
$this->content->template['message_20007'] = "内容"; 
$this->content->template['message_20008'] = "通讯订阅者管理"; 
$this->content->template['message_20009'] = "增加一个新的订阅者"; 
$this->content->template['message_20010'] = ""; 
$this->content->template['message_20011'] = "是 "; 
$this->content->template['message_20012'] = "没有"; 
$this->content->template['message_20013'] = "活跃"; 
$this->content->template['message_20014'] = "电子邮件地址"; 
$this->content->template['message_20015'] = "电子邮件"; 
$this->content->template['news_message_1'] = "<h2>编辑通讯</h2><p>你可以在这里编辑通讯，编辑订阅者和印记。</p><p>如果你想包括通讯，你可以。<br/><ol><li>创建一个菜单项。创建时，你可以在 \"包括链接或文件 \"下手动添加以下条目：<br /><strong>plugin:newsletter/templates/subscribe_newsletter.html</strong><br /></li><li>如果你不想有一个单独的菜单项，你可以通过编辑器的链接功能在任何文章中创建一个链接。该链接应该有以下内容：/plugin.php?menuid=1&amp;template=newsletter/templates/subscribe_newsletter.html 。<br /></li><li>你也可以使用模块管理器，在你喜欢的任何地方加入订阅表。<br /></li><li>此外，你也可以在你的页面上包括一个存档，在菜单项中有以下链接：<br /><strong>plugin:newsletter/templates/news_archiv.html</strong></li><li>通过占位符#Online_Link#，你可以链接到网站上的档案条目。正确的链接将自动输入那里。</li><li>对于通讯，你可以使用以下占位符：#title#（敬语）#name#（姓氏）#Newsletter_Kuendigen#（取消链接）。</li></ol>"; 
$this->content->template['news_message_2'] = "<h2 style=\"color:red;\">通讯已经发出去了。</h2>"; 
$this->content->template['news_message_3'] = "<h2>保存通讯</h2><p>点击保存通讯，所有相关的通讯数据将被保存在一个转储文件中。这个存储是独立于一般存储的。</p>"; 
$this->content->template['news_message_4'] = "保存通讯"; 
$this->content->template['message_20016'] = "发送的电子邮件地址。"; 
$this->content->template['message_20016a'] = "各种设置"; 
$this->content->template['message_20017'] = "*of:*部分的名称。"; 
$this->content->template['message_20018'] = "<p>你可以在这里订阅我们的通讯。要做到这一点，请填写以下表格。然后你会收到一封确认邮件，你必须回答。</p>
<p>只有这样，你才会被注册为新闻通讯员。</p>"; 
$this->content->template['message_20018_1'] = "通讯档案"; 
$this->content->template['message_20018_a'] = "nodecode<h2>:订阅通讯。</h2>"; 
$this->content->template['message_20019'] = "请输入你的数据。"; 
$this->content->template['message_20020'] = "订阅通讯"; 
$this->content->template['message_20021'] = "发送"; 
$this->content->template['message_20021d'] = "发送到以下分发列表"; 
$this->content->template['message_20021c'] = "预览"; 
$this->content->template['message_20021a'] = "正确的"; 
$this->content->template['newsmessage_20122'] = "添加文件附件"; 
$this->content->template['newsmessage_20122a'] = "附属文件"; 
$this->content->template['message_20023'] = "主体不见了。"; 
$this->content->template['message_20024'] = "创建新的通讯"; 
$this->content->template['message_20025'] = "这条信息丢失了。"; 
$this->content->template['message_20026'] = "未选择语言。"; 
$this->content->template['message_20027'] = "创建新的通讯分发列表"; 
$this->content->template['message_21027'] = "在前台显示分发列表？"; 
$this->content->template['message_21028'] = "分发名单有节制吗？"; 
$this->content->template['message_20028'] = "所有订户，包括系统分配列表"; 
$this->content->template['message_20029'] = "所有通讯分发名单"; 
$this->content->template['message_20030'] = "系统分配列表"; 
$this->content->template['message_20030a'] = "和Flex搜索结果"; 
$this->content->template['message_20031'] = "通讯分发清单"; 
$this->content->template['message_20032'] = "未指定分发名单"; 
$this->content->template['message_20033'] = "通讯分发名单是否应该 "; 
$this->content->template['message_20034'] = "真的被删除了吗？"; 
$this->content->template['message_20035'] = "通讯是否应该 "; 
$this->content->template['message_20036'] = "活跃的订户 "; 
$this->content->template['message_20037'] = "如果订阅者 "; 
$this->content->template['message_20038'] = "\"所有...... \"或个别分发列表只能选择"; 
$this->content->template['message_20039'] = "\"测试 \"分发列表必须是唯一选定的。"; 
$this->content->template['message_20040'] = "\"用户"; 
$this->content->template['message_20041'] = "你可以设置 \"测试 \"分发列表来发送通讯作为测试。只有那些你分配到这个分发列表的人将收到作为预览发送到分发列表 \"测试 \"的通讯。测试 \"分发列表不显示在前台，所以无法在前台订阅这个分发列表。已发送的测试新闻简报也没有显示在前台的新闻简报档案中。"; 
$this->content->template['message_20042'] = "激活通讯接收"; 
$this->content->template['message_20043'] = "停用通讯接收功能"; 
$this->content->template['message_20044'] = "登录日期前的字母 \"A \"表示由管理员输入的用户...<br />登录日期前面的字母 \"I \"表示是通过地址导入添加的用户。"; 
$this->content->template['erneut_versenden'] = "重新发送。"; 
$this->content->template['datum'] = "创建"; 
$this->content->template['senddate'] = "发送"; 
$this->content->template['kundensuchen'] = "搜索简讯订阅者"; 
$this->content->template['useranzahl'] = "# 订阅。"; 
$this->content->template['gruppe'] = "分发名单"; 
$this->content->template['newsletter_texthtml'] = "HTML-WYSIWYG"; 
$this->content->template['news_message1'] = "<h2>选择一种语言</h2><p>在此选择要创建通讯的语言。</p>"; 
$this->content->template['news_message2'] = "选择"; 
$this->content->template['news_loeschen'] = "删除"; 
$this->content->template['news_loeschene'] = "删除本通讯"; 
$this->content->template['news_grp_loeschene'] = "删除此通讯分发列表"; 
$this->content->template['news_edit'] = "编辑"; 
$this->content->template['news_edite'] = "编辑本通讯"; 
$this->content->template['news_grpname'] = "通讯分发名单"; 
$this->content->template['news_grpnamen'] = "通讯分发清单"; 
$this->content->template['news_grpdescript'] = "描述"; 
$this->content->template['news_grpfehlt'] = "没有选择分发名单"; 
$this->content->template['grp_edite'] = "编辑此通讯分发列表"; 
$this->content->template['abo_loeschene'] = "删除该用户"; 
$this->content->template['abo_edite'] = "编辑订阅者设置"; 
$this->content->template['message_news_is_del'] = "该条目已成功删除。"; 
$this->content->template['message_news_not_del'] = "这个分发列表不能被编辑或删除。"; 
$this->content->template['news_imptext1'] = "
-- 要取消订阅，请点击这里：http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html #imp#"; 
$this->content->template['news_imptext2'] = "<hr/>要取消通讯，请点击这里。<br /> <a href=\"http://#url#/plugin.php?menuid=1&amp;activate=#key#&amp;news_message=de_activate&amp;template=newsletter/templates/subscribe_newsletter.html\" rel=\"unsubscribe nofollow\">通讯取消</a><br />"; 
$this->content->template['news_mail1'] = "通过seitenurl订阅的通讯。"; 
$this->content->template['news_mail2'] = "您已经订阅了seitenurl的电子报，如果您没有订阅此电子报或不想要，请忽略此邮件，您将不会再收到。要激活通讯，请点击以下链接。"; 
$this->content->template['news_mail3'] = "一个新的订阅者已经注册了一个或更多的节制名单。"; 
$this->content->template['news_front1'] = "<h2>订阅的通讯</h2><p>你已经订阅了我们的通讯。你应该在几分钟内收到一封带有确认链接的电子邮件。</p><p>请点击邮件中的链接，最终订阅本通讯。</p>"; 
$this->content->template['news_front2'] = "<h2>通讯 </h2><p>您对我们的新闻简报的订阅已被激活。从今天起，您将开始收到我们的通讯。如果你想取消订阅，只需点击你从我们收到的任何电子邮件中的取消订阅链接。</p>"; 
$this->content->template['news_front3'] = "<h2>通讯被</h2>取消',<p>'通讯已被取消，你的数据已被删除</p>。"; 
$this->content->template['news_front4'] = "您的详细资料"; 
$this->content->template['news_front5'] = "先生。"; 
$this->content->template['news_front6'] = "女士。"; 
$this->content->template['news_front7'] = "名字"; 
$this->content->template['news_front8'] = "姓氏"; 
$this->content->template['news_front9'] = "街道和门牌号"; 
$this->content->template['news_front10'] = "邮政编号"; 
$this->content->template['news_front11'] = "居住地"; 
$this->content->template['news_front12'] = "语言"; 
$this->content->template['news_front13'] = "国家"; 
$this->content->template['news_front14'] = "规格缺失"; 
$this->content->template['news_front15'] = "无效的规范"; 
$this->content->template['news_front16'] = "已经存在。该用户已被分配到选定的分发列表。"; 
$this->content->template['news_front17'] = "IAKS成员"; 
$this->content->template['news_front18'] = "订户"; 
$this->content->template['news_front19'] = "公司"; 
$this->content->template['news_show_recipients'] = "显示发送通讯的邮件地址。"; 
$this->content->template['news_message3'] = "语言"; 
$this->content->template['message_aboeintragen'] = "输入/改变用户设置"; 
$this->content->template['plugin']['newsletter']['alle'] = "全部"; 
$this->content->template['plugin']['newsletter']['allow_delete'] = "如果设置了这个开关，订阅者将被不可逆转地删除（手动或通过取消订阅通讯），否则订阅者只是被标记为删除，不再可用于处理。后者起到了法律要求的证明作用。"; 
$this->content->template['plugin']['newsletter']['altnewsletter'] = "通讯管理"; 
$this->content->template['plugin']['newsletter']['inhalt_text'] = "作为文本的内容"; 
$this->content->template['plugin']['newsletter']['inhalt_html'] = "作为HTML的内容"; 
$this->content->template['plugin']['newsletter']['userdaten'] = "高级用户数据"; 
$this->content->template['plugin']['newsletter']['sprachwahl'] = "启用通讯注册的语言选择？"; 
$this->content->template['plugin']['newsletter']['text'] = "在登录处上方显示文字？"; 
$this->content->template['plugin']['newsletter']['html_mails'] = "HTML邮件？"; 
$this->content->template['plugin']['newsletter']['editor'] = "WYSIWYG编辑器tinymce?"; 
$this->content->template['plugin']['newsletter']['sprache'] = "语言"; 
$this->content->template['plugin']['newsletter']['daten'] = "日期。"; 
$this->content->template['plugin']['newsletter']['vorname'] = "名字"; 
$this->content->template['plugin']['newsletter']['nachname'] = "姓氏"; 
$this->content->template['plugin']['newsletter']['strasse'] = "街道和门牌号"; 
$this->content->template['plugin']['newsletter']['postleitzahl'] = "邮政编号"; 
$this->content->template['plugin']['newsletter']['wohnort'] = "居住地"; 
$this->content->template['plugin']['newsletter']['staat'] = "国家"; 
$this->content->template['plugin']['newsletter']['phone'] = "电话"; 
$this->content->template['plugin']['newsletter']['speichern'] = "进入"; 
$this->content->template['plugin']['newsletter']['email'] = "电子邮件"; 
$this->content->template['plugin']['newsletter']['eingabe_datei'] = "输入文件。"; 
$this->content->template['plugin']['newsletter']['dokument'] = "该文件。"; 
$this->content->template['plugin']['newsletter']['durchsuchen'] = "浏览..."; 
$this->content->template['plugin']['newsletter']['datei_upload'] = "上传文件。"; 
$this->content->template['plugin']['newsletter']['upload'] = "上传"; 
$this->content->template['plugin']['newsletter']['sicherung'] = "<h3>创建一个数据库的备份</h3><p>你可以在这里创建一个数据库的备份，你可以在新的安装后或在任何其他时间恢复。</p>"; 
$this->content->template['plugin']['newsletter']['sicherung_einspielen'] = "导入一个备份"; 
$this->content->template['plugin']['newsletter']['sicherung_ready'] = "备份文件已被导入。"; 
$this->content->template['plugin']['newsletter']['hinweis'] = "要导入一个备份，请选择备份文件。"; 
$this->content->template['plugin']['newsletter']['warnung'] = "注意 - 如果你导入一个备份，所有当前数据将被不可逆转地删除。因此，你必须事先创建一个备份!"; 
$this->content->template['plugin']['newsletter']['make_dump'] = "现在创建一个备份"; 
$this->content->template['plugin']['newsletter']['anzahlgef'] = "发现的订户数量。"; 
$this->content->template['plugin']['newsletter']['anzahlgefgrp'] = "发现的分发列表的数量。"; 
$this->content->template['plugin']['newsletter']['anzahlgefnl'] = "发现的新闻简报数量。"; 
$this->content->template['plugin']['newsletter']['asc'] = "升序"; 
$this->content->template['plugin']['newsletter']['desc'] = "下降"; 
$this->content->template['plugin']['newsletter']['sort'] = "分拣"; 
$this->content->template['plugin']['newsletter']['Ihr_Suchbegriff'] = "您的搜索词"; 
$this->content->template['plugin']['newsletter']['aktivjn'] = "已启用"; 
$this->content->template['plugin']['newsletter']['Newsletter_Kunden'] = "电子报订阅者"; 
$this->content->template['plugin']['newsletter']['Anrede'] = "问候语"; 
$this->content->template['plugin']['newsletter']['groups'] = "通讯分发列表管理"; 
$this->content->template['plugin']['newsletter']['errmsg']['attachment_already_exist'] = "该附件已经为本通讯上传。"; 
$this->content->template['plugin']['newsletter']['errmsg']['file_fehlt'] = "没有找到文件。"; 
$this->content->template['plugin']['newsletter']['errmsg']['kein_filename'] = "附件的文件名丢失。"; 
$this->content->template['plugin']['newsletter']['imgtext']['news_edit_attachment'] = "删除附件。"; 
$this->content->template['plugin']['newsletter']['label']['language'] = "选择你希望用于订阅新闻简报的语言。"; 
$this->content->template['plugin']['newsletter']['label']['timeout'] = "超时保护：以10秒为间隔一次性发送的邮件数量"; 
$this->content->template['plugin']['newsletter']['linktext']['news_edit_attachment'] = "在新窗口中显示附件。"; 
$this->content->template['plugin']['newsletter']['linktext']['sync'] = "这条记录是否应该标上Id "; 
$this->content->template['plugin']['newsletter']['linktext']['sync2'] = "真的被删除了吗？"; 
$this->content->template['plugin']['newsletter']['message']['attachment_loaded'] = "该文件是作为附件上传的。<br />请保存所有更改。"; 
$this->content->template['plugin']['newsletter']['message']['attachment_deleted'] = "该附件已被删除。<br />请保存所有更改。"; 
$this->content->template['plugin']['newsletter']['message']['nl_saved'] = "您的通讯数据已被保存。"; 
$this->content->template['plugin']['newsletter']['registration'] = "注册"; 
$this->content->template['plugin']['newsletter']['submit']['cancel'] = "取消"; 
$this->content->template['plugin']['newsletter']['submit']['save'] = "拯救"; 
$this->content->template['plugin']['newsletter']['submit']['send'] = "发送"; 
$this->content->template['plugin']['newsletter']['text2']['groups_nl_send'] = "注意：每种情况下显示的数字是数据库中现有的、但未被选中的用户条目的数量。无效的电子邮件地址和可能存在的重复地址不会被发送出去。因此，概览中显示的接收通讯的用户总数可能与这里的数值不同。"; 
$this->content->template['plugin']['newsletter']['text2']['mails_per_step'] = "每个调度步骤的电子邮件数量。"; 
$this->content->template['plugin']['newsletter']['text2']['news_new_attachment'] = "只有在输入主题和信息后，才可以上传文件附件。"; 
$this->content->template['plugin']['newsletter']['text2']['news_edit_attachment2'] = "你的一个或多个文件只在DB中输入，但在目录中已经找不到了。为了消除这个错误，你可以在这里或通过FTP上传这些文件，或在必要时立即删除它们。注意，上传时文件必须有相同的名称和相同的大小（后者不能通过FTP）。"; 
$this->content->template['plugin']['newsletter']['text2']['news_edit'] = "编辑通讯"; 
$this->content->template['plugin']['newsletter']['text2']['news_send_tip'] = "注意：附件和你创建的印记也将被发送。"; 
$this->content->template['plugin']['newsletter']['link']['grp_std'] = "NL 分发名单标准"; 
$this->content->template['plugin']['newsletter']['link']['grp_std_descr'] = "标准 NL 分发名单"; 
$this->content->template['plugin']['newsletter']['used_file'] = "文件名称"; 
$this->content->template['plugin']['newsletter']['size_text'] = "尺寸"; 
$this->content->template['plugin']['newsletter']['datum'] = "日期"; 
$this->content->template['plugin']['newsletter']['loeschen3'] = "删除"; 
$this->content->template['plugin']['newsletter']['export'] = "输出CSV"; 
$this->content->template['plugin']['newsletter']['header01'] = "上传的文件"; 
$this->content->template['plugin']['newsletter']['datei_loeschen'] = "删除选择"; 
$this->content->template['plugin']['newsletter']['das_dokument'] = "该文件。"; 
$this->content->template['plugin']['newsletter']['import_starten'] = "开始进口"; 
$this->content->template['plugin']['newsletter']['datei_hochladen'] = "上传文件"; 
$this->content->template['plugin']['newsletter']['text03'] = "如果你的文件已经存在，你可以在导入前现在删除它，以避免上传问题。"; 
$this->content->template['plugin']['newsletter']['text04'] = "导入文件的第1行必须包含这些字段名，顺序不限。名字，姓名，街道，邮编，城市，邮件。导入文件必须是一个CSV文件。字段必须用HT（Tab）（x09，t）分隔，行间必须用CR LF（x0D0A，rn）结束。"; 
$this->content->template['plugin']['newsletter']['datei_importieren'] = "1. 步骤：导入文件"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben'] = "2. 步骤：进口"; 
$this->content->template['plugin']['newsletter']['liste_waehlen'] = "请选择分发名单"; 
$this->content->template['plugin']['newsletter']['leeren_waehlen'] = "导入时的分发列表为空？"; 
$this->content->template['plugin']['newsletter']['datei_ist_oben_text'] = "该文件已成功上传。"; 
$this->content->template['plugin']['newsletter']['importprotokoll'] = "进口日志"; 
$this->content->template['plugin']['newsletter']['importprotokoll3'] = "进口错误日志概述"; 
$this->content->template['plugin']['newsletter']['daten_eingetragen'] = "记录已被输入。"; 
$this->content->template['plugin']['newsletter']['daten_del'] = "记录已被删除。"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen'] = "没有输入记录"; 
$this->content->template['plugin']['newsletter']['daten_nicht_eingetragen2'] = "未输入的数据记录"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report'] = "导入错误日志概述"; 
$this->content->template['plugin']['newsletter']['pageheader']['error_report2'] = "导入错误日志细节"; 
$this->content->template['plugin']['newsletter']['report_deleted'] = "删除的错误日志"; 
$this->content->template['plugin']['newsletter']['id'] = "同上"; 
$this->content->template['plugin']['newsletter']['import_time'] = "时间"; 
$this->content->template['plugin']['newsletter']['normaler_user'] = "用户"; 
$this->content->template['plugin']['newsletter']['records_to_import'] = "总数 #"; 
$this->content->template['plugin']['newsletter']['error_count'] = "错误 #"; 
$this->content->template['plugin']['newsletter']['success_count'] = "成功 #"; 
$this->content->template['plugin']['newsletter']['import_error_report_show_details'] = "显示细节"; 
$this->content->template['plugin']['newsletter']['alttext']['sync'] = "清除这个错误日志"; 
$this->content->template['plugin']['newsletter']['error_count2'] = "错误总数"; 
$this->content->template['plugin']['newsletter']['error_no'] = "Lfd."; 
$this->content->template['plugin']['newsletter']['import_file_record_no'] = "设置 #"; 
$this->content->template['plugin']['newsletter']['import_file_field_position'] = "领域 #"; 
$this->content->template['plugin']['newsletter']['import_file_excel_field_position'] = "Excel的位置。"; 
$this->content->template['plugin']['newsletter']['import_file_field_name'] = "领域名称"; 
$this->content->template['plugin']['newsletter']['import_error_msg'] = "错误信息"; 
$this->content->template['plugin']['newsletter']['completion_code'] = "编码"; 
$this->content->template['plugin']['newsletter']['email_error'] = "没有有效的电子邮件地址"; 
$this->content->template['plugin']['newsletter']['max255_4'] = "已经超过了255个字符的最大输入长度。"; 
$this->content->template['plugin']['newsletter']['email_schon_da'] = "这个电子邮件地址已经存在。"; 
$this->content->template['plugin']['newsletter']['feldanzahl'] = "缺少一个字段名：FIRST NAME, NAME, STREET, ZIP, CITY, MAIL。"; 
$this->content->template['plugin']['newsletter']['feldnamefalsch'] = "错误的字段名称：FIRST NAME, NAME, STREET, ZIP, CITY, MAIL..."; 
$this->content->template['plugin_glossar_dubletten_entfernen'] = "删除双打"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_text'] = "从数据库中删除重复的邮件地址。"; 
$this->content->template['plugin_newsletter_dubletten_entfernen_field'] = "删除双打"; 
$this->content->template['plugin_newsletter_import'] = "进口地址"; 
$this->content->template['plugin_newsletter_export'] = "出口地址"; 
$this->content->template['plugin_newsletter_import_text'] = "导入地址（CSV文件"; 
$this->content->template['plugin_newsletter_export_text'] = "输出地址（CSV文件）。"; 
$this->content->template['plugin_newsletter_inaktive_lschen'] = "删除不活动的"; 
$this->content->template['plugin_newsletter_blacklist_lschen'] = "通过导入黑名单删除订阅者"; 
$this->content->template['plugin_newsletter_inaktive_lschen_text'] = "删除所有不活跃的订户，无需确认!"; 
$this->content->template['plugin_newsletter_inaktive_eintrge_lschen'] = "删除不活跃的订阅者"; 
$this->content->template['plugin_newsletter_inaktive_geloescht'] = "不活跃的订户已被删除。"; 
$this->content->template['plugin_newsletter_dubletten_geloescht'] = "重复的邮件地址已被删除。"; 
$this->content->template['newsletter_verteilerliste'] = "分发名单"; 

 ?>