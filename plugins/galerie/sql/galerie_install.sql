DROP TABLE IF EXISTS `XXX_galerie_bilder`; ##b_dump##
CREATE TABLE `XXX_galerie_bilder` (
  `bild_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bild_datei` varchar(255) NOT NULL,
  `bild_gal_id` bigint(20) NOT NULL,
  `bild_nummer` int(11) NOT NULL,
  `bild_format` varchar(50) NOT NULL,
  `bild_breite` int(11) NOT NULL,
  `bild_breite_thumb` int(11) NOT NULL,
  `bild_hoehe` int(11) NOT NULL,
  `bild_hoehe_thumb` int(11) NOT NULL,
  `bild_diashow_timeout` int(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`bild_id`),
  KEY `bild_gal_id` (`bild_gal_id`),
  KEY `bild_nummer` (`bild_nummer`)
) ENGINE=MyISAM AUTO_INCREMENT=416 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='1', bild_datei='../../../bilder/kein_bild.gif', bild_gal_id='0', bild_nummer='0', bild_format='GIF', bild_breite='100', bild_breite_thumb='100', bild_hoehe='30', bild_hoehe_thumb='30', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='24', bild_datei='asteroids.png', bild_gal_id='11', bild_nummer='2', bild_format='PNG', bild_breite='150', bild_breite_thumb='120', bild_hoehe='114', bild_hoehe_thumb='91', bild_diashow_timeout='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='25', bild_datei='invaders.png', bild_gal_id='11', bild_nummer='3', bild_format='PNG', bild_breite='150', bild_breite_thumb='120', bild_hoehe='133', bild_hoehe_thumb='106', bild_diashow_timeout='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='26', bild_datei='pacman.png', bild_gal_id='11', bild_nummer='4', bild_format='PNG', bild_breite='150', bild_breite_thumb='120', bild_hoehe='134', bild_hoehe_thumb='107', bild_diashow_timeout='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='27', bild_datei='tetris.png', bild_gal_id='11', bild_nummer='5', bild_format='PNG', bild_breite='150', bild_breite_thumb='120', bild_hoehe='149', bild_hoehe_thumb='119', bild_diashow_timeout='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='28', bild_datei='snake.png', bild_gal_id='11', bild_nummer='6', bild_format='PNG', bild_breite='150', bild_breite_thumb='120', bild_hoehe='119', bild_hoehe_thumb='95', bild_diashow_timeout='10'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='364', bild_datei='Tasse_AppleLogo2.jpg', bild_gal_id='11', bild_nummer='7', bild_format='JPG', bild_breite='500', bild_breite_thumb='120', bild_hoehe='333', bild_hoehe_thumb='80', bild_diashow_timeout='20'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='393', bild_datei='logo.gif', bild_gal_id='11', bild_nummer='1', bild_format='GIF', bild_breite='271', bild_breite_thumb='120', bild_hoehe='140', bild_hoehe_thumb='62', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='394', bild_datei='027.jpg', bild_gal_id='12', bild_nummer='1', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='395', bild_datei='035.jpg', bild_gal_id='12', bild_nummer='2', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='396', bild_datei='037.jpg', bild_gal_id='12', bild_nummer='3', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='397', bild_datei='043.jpg', bild_gal_id='12', bild_nummer='4', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='398', bild_datei='045.jpg', bild_gal_id='12', bild_nummer='5', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='399', bild_datei='049.jpg', bild_gal_id='12', bild_nummer='6', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='400', bild_datei='053.jpg', bild_gal_id='12', bild_nummer='7', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='401', bild_datei='067.jpg', bild_gal_id='12', bild_nummer='8', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='402', bild_datei='082.jpg', bild_gal_id='12', bild_nummer='9', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='403', bild_datei='134.jpg', bild_gal_id='12', bild_nummer='10', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='404', bild_datei='136.jpg', bild_gal_id='12', bild_nummer='11', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='405', bild_datei='194.jpg', bild_gal_id='12', bild_nummer='12', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='406', bild_datei='195.jpg', bild_gal_id='12', bild_nummer='13', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='407', bild_datei='212.jpg', bild_gal_id='12', bild_nummer='14', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='408', bild_datei='215.jpg', bild_gal_id='12', bild_nummer='15', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='409', bild_datei='228.jpg', bild_gal_id='12', bild_nummer='16', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='410', bild_datei='243.jpg', bild_gal_id='12', bild_nummer='17', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='411', bild_datei='248.jpg', bild_gal_id='12', bild_nummer='18', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='412', bild_datei='252.jpg', bild_gal_id='12', bild_nummer='19', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='413', bild_datei='254.jpg', bild_gal_id='12', bild_nummer='20', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='414', bild_datei='258.jpg', bild_gal_id='12', bild_nummer='21', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder` SET bild_id='415', bild_datei='268.jpg', bild_gal_id='12', bild_nummer='22', bild_format='JPG', bild_breite='2560', bild_breite_thumb='120', bild_hoehe='1920', bild_hoehe_thumb='90', bild_diashow_timeout='5'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_bilder_language`; ##b_dump##
CREATE TABLE `XXX_galerie_bilder_language` (
  `bildlang_bild_id` bigint(20) NOT NULL,
  `bildlang_lang_id` int(11) NOT NULL,
  `bildlang_name` varchar(255) NOT NULL,
  `bildlang_beschreibung` longtext NOT NULL,
  PRIMARY KEY (`bildlang_bild_id`, `bildlang_lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='1', bildlang_lang_id='1', bildlang_name='kein Bild', bildlang_beschreibung='Kein Bild'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='1', bildlang_lang_id='2', bildlang_name='no picture', bildlang_beschreibung='no picture'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='24', bildlang_lang_id='2', bildlang_name='Asteroids', bildlang_beschreibung='You have to navigate your little spaceship through a field of asteroids. Dont bump on one of them, better shot them before they damage your spaceship.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='25', bildlang_lang_id='1', bildlang_name='Space-Invaders', bildlang_beschreibung='Es gilt eine Invasion von außeridischen Raumschiffen aufzuhalten. Mit marzialischer Gewalt versteht sich, durch abschießen also.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='26', bildlang_lang_id='1', bildlang_name='PacMan', bildlang_beschreibung='Der Spieler manövriert durch ein Labyrinth, welches von gefräsigen Monstern nur so wimmelt. Es gilt dabei das Gesetz \"Fressen und gefressen werden\".'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='27', bildlang_lang_id='1', bildlang_name='Tetris', bildlang_beschreibung='Geometrische Objekte müssen während dem Herabfallen so positioniert werden, dass eine Reihe komplett gefüllt wird. Zu allem Übel muß das immer schneller geschehen, da sich die Spielgeschwindigkeit immer weiter steigert.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='28', bildlang_lang_id='2', bildlang_name='Snake', bildlang_beschreibung='A game for two persons. Your snake has to eat everything to grow. But do not bump into the other snake or you will die.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='364', bildlang_lang_id='1', bildlang_name='\"Täs\'sche Tee\"', bildlang_beschreibung='Dieses Bild hat nun mal überhaupt nichts mit Spielen zu tun.\r\n\r\nIst einfach ein Bild, welches etwas größer ist als die anderen Bilder.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='364', bildlang_lang_id='2', bildlang_name='Cup of Tea', bildlang_beschreibung='No game, just a cup of tea in a nice cup :-)'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='393', bildlang_lang_id='1', bildlang_name='Startbild der Galerie \"Spiele-Klassiker\" \'§$%\'', bildlang_beschreibung='Logo der Bilder-Reihe \"Spiele-Klassiker\" \'§$%\'\r\n\r\n.. siehe auch <a href=\"http://www.ximix.de\" target=\"_blank\">XiMiX.de</a>'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='26', bildlang_lang_id='2', bildlang_name='PacMan', bildlang_beschreibung='You have to navigate through a labyrinth. But be carefull, there are monster that want to kill you.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='24', bildlang_lang_id='1', bildlang_name='Asteroids', bildlang_beschreibung='Mit einem kleinen Raumschiff muß kollisionsfrei durch ein Asteroiden-Feld manövriert werden. Natürlich darf dabei auch auf den ein oder anderen Asteroiden geschoßen werden.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='25', bildlang_lang_id='2', bildlang_name='Space-Invaders', bildlang_beschreibung='Invasion from outer space. KILL ALL THE ALIENS!!'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='27', bildlang_lang_id='2', bildlang_name='Tetris', bildlang_beschreibung='You have to order geometric figures to fill a line. hurry up! the game will get faster and faster and faster and..'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='28', bildlang_lang_id='1', bildlang_name='Snake', bildlang_beschreibung='Ein Spiel für Zwei. Jeder der Spieler versucht dabei so viel zu Fressen wie nur Möglich, damit die eigene Schlange immer weiter wächst. Trifft man jedoch auf die gegnerische Schlange, so hat man sofort verloren.'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='393', bildlang_lang_id='2', bildlang_name='Logo', bildlang_beschreibung='Start-picture for the picture-galerie \"computer-games\"'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='394', bildlang_lang_id='1', bildlang_name='027.jpg', bildlang_beschreibung='027.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='394', bildlang_lang_id='2', bildlang_name='027.jpg', bildlang_beschreibung='027.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='395', bildlang_lang_id='1', bildlang_name='035.jpg', bildlang_beschreibung='035.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='395', bildlang_lang_id='2', bildlang_name='035.jpg', bildlang_beschreibung='035.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='396', bildlang_lang_id='1', bildlang_name='037.jpg', bildlang_beschreibung='037.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='396', bildlang_lang_id='2', bildlang_name='037.jpg', bildlang_beschreibung='037.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='397', bildlang_lang_id='1', bildlang_name='043.jpg', bildlang_beschreibung='043.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='397', bildlang_lang_id='2', bildlang_name='043.jpg', bildlang_beschreibung='043.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='398', bildlang_lang_id='1', bildlang_name='045.jpg', bildlang_beschreibung='045.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='398', bildlang_lang_id='2', bildlang_name='045.jpg', bildlang_beschreibung='045.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='399', bildlang_lang_id='1', bildlang_name='049.jpg', bildlang_beschreibung='049.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='399', bildlang_lang_id='2', bildlang_name='049.jpg', bildlang_beschreibung='049.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='400', bildlang_lang_id='1', bildlang_name='053.jpg', bildlang_beschreibung='053.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='400', bildlang_lang_id='2', bildlang_name='053.jpg', bildlang_beschreibung='053.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='401', bildlang_lang_id='1', bildlang_name='067.jpg', bildlang_beschreibung='067.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='401', bildlang_lang_id='2', bildlang_name='067.jpg', bildlang_beschreibung='067.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='402', bildlang_lang_id='1', bildlang_name='082.jpg', bildlang_beschreibung='082.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='402', bildlang_lang_id='2', bildlang_name='082.jpg', bildlang_beschreibung='082.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='403', bildlang_lang_id='1', bildlang_name='134.jpg', bildlang_beschreibung='134.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='403', bildlang_lang_id='2', bildlang_name='134.jpg', bildlang_beschreibung='134.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='404', bildlang_lang_id='1', bildlang_name='136.jpg', bildlang_beschreibung='136.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='404', bildlang_lang_id='2', bildlang_name='136.jpg', bildlang_beschreibung='136.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='405', bildlang_lang_id='1', bildlang_name='194.jpg', bildlang_beschreibung='194.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='405', bildlang_lang_id='2', bildlang_name='194.jpg', bildlang_beschreibung='194.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='406', bildlang_lang_id='1', bildlang_name='195.jpg', bildlang_beschreibung='195.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='406', bildlang_lang_id='2', bildlang_name='195.jpg', bildlang_beschreibung='195.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='407', bildlang_lang_id='1', bildlang_name='212.jpg', bildlang_beschreibung='212.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='407', bildlang_lang_id='2', bildlang_name='212.jpg', bildlang_beschreibung='212.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='408', bildlang_lang_id='1', bildlang_name='215.jpg', bildlang_beschreibung='215.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='408', bildlang_lang_id='2', bildlang_name='215.jpg', bildlang_beschreibung='215.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='409', bildlang_lang_id='1', bildlang_name='228.jpg', bildlang_beschreibung='228.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='409', bildlang_lang_id='2', bildlang_name='228.jpg', bildlang_beschreibung='228.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='410', bildlang_lang_id='1', bildlang_name='243.jpg', bildlang_beschreibung='243.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='410', bildlang_lang_id='2', bildlang_name='243.jpg', bildlang_beschreibung='243.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='411', bildlang_lang_id='1', bildlang_name='248.jpg', bildlang_beschreibung='248.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='411', bildlang_lang_id='2', bildlang_name='248.jpg', bildlang_beschreibung='248.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='412', bildlang_lang_id='1', bildlang_name='252.jpg', bildlang_beschreibung='252.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='412', bildlang_lang_id='2', bildlang_name='252.jpg', bildlang_beschreibung='252.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='413', bildlang_lang_id='1', bildlang_name='254.jpg', bildlang_beschreibung='254.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='413', bildlang_lang_id='2', bildlang_name='254.jpg', bildlang_beschreibung='254.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='414', bildlang_lang_id='1', bildlang_name='258.jpg', bildlang_beschreibung='258.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='414', bildlang_lang_id='2', bildlang_name='258.jpg', bildlang_beschreibung='258.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='415', bildlang_lang_id='1', bildlang_name='268.jpg', bildlang_beschreibung='268.jpg'  ; ##b_dump##
INSERT INTO `XXX_galerie_bilder_language` SET bildlang_bild_id='415', bildlang_lang_id='2', bildlang_name='268.jpg', bildlang_beschreibung='268.jpg'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_einstellungen`; ##b_dump##
CREATE TABLE `XXX_galerie_einstellungen` (
  `galset_thumb_breite` int(11) NOT NULL DEFAULT '150',
  `galset_thumb_hoehe` int(11) NOT NULL DEFAULT '150',
  `galset_diashow_id` int(11) NOT NULL DEFAULT '1',
  `galset_diashow_timeout` int(11) NOT NULL DEFAULT '5',
  `galset_lightbox` int(11) NOT NULL DEFAULT '1',
  `galset_diashow` int(11) NOT NULL DEFAULT '1',
  `galset_diashow_window` int(11) NOT NULL DEFAULT '1',
  `galset_gps_view` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_einstellungen` SET galset_thumb_breite='120', galset_thumb_hoehe='120', galset_diashow_id='1', galset_diashow_timeout='5', galset_lightbox='1', galset_diashow='1', galset_diashow_window='1', galset_gps_view='1'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_galerien`; ##b_dump##
CREATE TABLE `XXX_galerie_galerien` (
  `gal_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '999999999',
  `gal_bilderanzahl` int(11) NOT NULL DEFAULT '0',
  `gal_verzeichnis` varchar(255) NOT NULL,
  `gal_aktiv_janein` int(1) NOT NULL DEFAULT '0',
  `gal_bild_id` bigint(20) NOT NULL DEFAULT '1',
  `gal_order_id` int(11) NOT NULL,
  PRIMARY KEY (`gal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='11', parent_id='14', gal_bilderanzahl='7', gal_verzeichnis='SpieleKlassiker', gal_aktiv_janein='1', gal_bild_id='24', gal_order_id='2'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='12', parent_id='13', gal_bilderanzahl='22', gal_verzeichnis='Greece', gal_aktiv_janein='1', gal_bild_id='412', gal_order_id='1'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='13', parent_id='0', gal_bilderanzahl='0', gal_verzeichnis='', gal_aktiv_janein='0', gal_bild_id='1', gal_order_id='12'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien` SET gal_id='14', parent_id='0', gal_bilderanzahl='0', gal_verzeichnis='', gal_aktiv_janein='0', gal_bild_id='1', gal_order_id='22'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_galerie_galerien_language`; ##b_dump##
CREATE TABLE `XXX_galerie_galerien_language` (
  `gallang_gal_id` bigint(20) NOT NULL,
  `gallang_lang_id` bigint(20) NOT NULL,
  `gallang_name` varchar(255) NOT NULL,
  `gallang_beschreibung` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='11', gallang_lang_id='2', gallang_name='Computer-Games', gallang_beschreibung='Screenshots of some classic console-games.'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='11', gallang_lang_id='1', gallang_name='Spiele-Klassiker', gallang_beschreibung='Screenshots einiger klassischer Konsolen-Spiele. Sie gehören zu den ersten Computer-Spielen überhaupt und bereiteten den Weg für den enormen Erfolg von Computer-Spielen. Im Vergleich zu den grafisch aufwändigen Spielen heutiger Zeit, sehen diese Spiele eher bescheiden aus. Der Spaß-Faktor ist aber enorm, weshalb sich einige dieser Spiele bis in die heutige Zeit erhalten haben und nun z.B. auf Handys verfügbar sind.'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='12', gallang_lang_id='1', gallang_name='Griechenland', gallang_beschreibung='Impressionen aus Griechenland, alles was man erwartet, Blaue Dächer, weiße Häuser, alte Steine und traumhafte Strände.'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='12', gallang_lang_id='2', gallang_name='Greece', gallang_beschreibung='Greece'  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='13', gallang_lang_id='1', gallang_name='Urlaub', gallang_beschreibung=''  ; ##b_dump##
INSERT INTO `XXX_galerie_galerien_language` SET gallang_gal_id='14', gallang_lang_id='1', gallang_name='Freizeit', gallang_beschreibung=''  ; ##b_dump##
