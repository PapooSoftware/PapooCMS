ALTER TABLE `XXX_papoo_kategorie_bilder` ADD `image_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_bilder` ADD `image_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 

ALTER TABLE `XXX_papoo_kategorie_dateien` ADD `dateien_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_dateien` ADD `dateien_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 

ALTER TABLE `XXX_papoo_kategorie_video` ADD `video_sub_cat_von` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 
ALTER TABLE `XXX_papoo_kategorie_video` ADD `video_sub_cat_level` int(11) NOT NULL  DEFAULT '0'; ##b_dump## 