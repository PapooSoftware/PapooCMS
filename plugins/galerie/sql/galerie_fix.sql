-- Dieser Patch muss eingespielt werden, wenn nach einem Update des Bildergalerie-Plugins die vorhandenen Galerien fälschlicherweise in Kategorien umgewandelt worden sind
UPDATE `XXX_galerie_galerien` SET `parent_id`=999999999 WHERE `parent_id`=0; ##b_dump##
