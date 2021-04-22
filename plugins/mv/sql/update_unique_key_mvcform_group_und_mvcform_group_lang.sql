ALTER TABLE `XXX_papoo_mvcform_group` DROP INDEX `mvcform_group_id`; ##b_dump##
ALTER IGNORE TABLE `XXX_papoo_mvcform_group` ADD UNIQUE INDEX `unique_index` (`mvcform_group_form_id`,`mvcform_group_form_meta_id`,`mvcform_group_id`); ##b_dump##
ALTER TABLE `XXX_papoo_mvcform_group_lang` DROP INDEX `mvcform_group_lang_id`; ##b_dump##
ALTER IGNORE TABLE `XXX_papoo_mvcform_group_lang` ADD UNIQUE INDEX `unique_index` (`mvcform_group_lang_id`,`mvcform_group_lang_lang`,`mvcform_group_lang_meta`); ##b_dump##