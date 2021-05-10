ALTER TABLE `XXX_papoo_faq_categories` CHANGE `id` `id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_faq_categories` DROP PRIMARY KEY; ##b_dump##
ALTER TABLE `XXX_papoo_faq_content` CHANGE `id` `id` INT NOT NULL; ##b_dump##
ALTER TABLE `XXX_papoo_faq_content` DROP PRIMARY KEY; ##b_dump##
