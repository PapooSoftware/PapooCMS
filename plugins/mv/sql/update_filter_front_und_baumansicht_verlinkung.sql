ALTER TABLE `XXX_papoo_mvcform` ADD `mvcform_filter_front` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT 'Filter die Eintragungen die aus dem Frontend kommen'; ##b_dump##
ALTER TABLE `XXX_papoo_mvcform` ADD `mvcform_defaulttreeviewname` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT 'Das form-feld wessen Inhalt bei einer Baumansicht als Uebersicht angezeigt werden soll'; ##b_dump##

DROP PROCEDURE IF EXISTS add_columns_in_all_template_tables; ##b_dump##
CREATE PROCEDURE add_columns_in_all_template_tables()
BEGIN
	DECLARE done int(11);
	DECLARE tname varchar(64);
	DECLARE templatesiterator CURSOR FOR SELECT table_name FROM information_schema.tables where table_name LIKE '%ppx07_papoo_mv_template_%';
	DECLARE continue handler for not found set done=1;

	open templatesiterator;

	templatesloop: loop
		fetch templatesiterator into tname;
		if done = 1 then leave templatesloop; end if;

			SET @s = CONCAT('ALTER TABLE ', tname, ' ADD `template_content_flex_link_selection` text NULL'); 
			PREPARE stmt1 FROM @s; 
			EXECUTE stmt1; 
			DEALLOCATE PREPARE stmt1; 

			SET @s = CONCAT('ALTER TABLE ', tname, ' ADD `template_content_flex_link_tree` text NULL'); 
			PREPARE stmt1 FROM @s; 
			EXECUTE stmt1; 
			DEALLOCATE PREPARE stmt1; 

	end loop templatesloop;

	close templatesiterator;
END; ##b_dump##

CALL add_columns_in_all_template_tables(); ##b_dump##

