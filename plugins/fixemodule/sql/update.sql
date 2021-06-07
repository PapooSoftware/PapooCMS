-- Emulate unique key to insert new type only once
SELECT @rowId := id FROM `XXX_plugin_fixemodule_feldtypen` WHERE name LIKE 'Checkbox' LIMIT 1; ##b_dump##
INSERT IGNORE INTO `XXX_plugin_fixemodule_feldtypen` SET id = @rowId, name = 'Checkbox'; ##b_dump##

ALTER TABLE `XXX_plugin_fixemodule_module`
ADD `html` text;