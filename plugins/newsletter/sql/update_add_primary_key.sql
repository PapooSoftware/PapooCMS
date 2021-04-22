ALTER TABLE `XXX_papoo_news_protocol_user` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `xxx_papoo_news_protocol_user` ADD PRIMARY KEY( `news_user_id`, `news_id`, `news_type`); ##b_dump##