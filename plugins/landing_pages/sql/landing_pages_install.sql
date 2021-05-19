DROP TABLE IF EXISTS `XXX_plugin_landing_page`; ##b_dump##
CREATE TABLE `XXX_plugin_landing_page` (
  `landing_page_id` int(11) NOT NULL AUTO_INCREMENT,
  `landing_page_der_name_der_kategorie_` text,
  PRIMARY KEY (`landing_page_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page` SET landing_page_id='1', landing_page_der_name_der_kategorie_='testxxx'  ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page` SET landing_page_id='2', landing_page_der_name_der_kategorie_='tessdfsdf'  ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page` SET landing_page_id='3', landing_page_der_name_der_kategorie_='test3'  ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page` SET landing_page_id='4', landing_page_der_name_der_kategorie_='test4'  ; ##b_dump##
DROP TABLE IF EXISTS `XXX_plugin_landing_page_sites`; ##b_dump##
CREATE TABLE `XXX_plugin_landing_page_sites` (
  `kalender_id` int(11) NOT NULL AUTO_INCREMENT,
  `kalender_interne_bezeichnung` text,
  `kalender_domain` text,
  `kalender_titel_der_seite` text,
  `kalender_meta_description` text,
  `kalender_meta_keywords` text,
  `kalender_design` varchar(255) NOT NULL,
  `kalender_content` text,
  `kalender_kategori_der_sseite` varchar(255) NOT NULL,
  `kalender_gacode` text,
  PRIMARY KEY (`kalender_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page_sites` SET kalender_id='1', kalender_interne_bezeichnung='intern 1asdfsafd', kalender_domain='domain.de', kalender_titel_der_seite='titel', kalender_meta_description='meta descrip', kalender_meta_keywords='meta key', kalender_design='532', kalender_content='<p>content</p>', kalender_kategori_der_sseite='4', kalender_gacode=''  ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page_sites` SET kalender_id='2', kalender_interne_bezeichnung='asfasdf', kalender_domain='bfsystem.de', kalender_titel_der_seite='', kalender_meta_description='etert\r\newtrwert', kalender_meta_keywords='', kalender_design='559', kalender_content='<h1>Herzlich Willkommen auf Ihrer Internetseite.</h1><p><strong>Herzlichen  Glückwunsch.</strong> Die Installation Ihrer  Papoo Edition hat   funktioniert und Sie können das barrierefreie und  suchmaschienenoptimierte CMS  Papoo jetzt voll  benutzen.<img style=\"float: right; margin: 10px;\" title=\"Gratulation - die Installation hat funktioniert\" alt=\"Gratulation - die Installation hat funktioniert\" src=\"../images/10_gratulation.jpg\" height=\"205\" width=\"310\" /></p><p> </p><p>Mit  dem CMS Papoo steht Ihnen ein <strong>vollwertiges Content Management  System</strong> zur Verfügung mit dem Sie beliebig viele Inhalte  erstellen und managen können.</p><p> </p><p>Unser besonderer Focus  liegt auf der <strong>Suchmaschinenoptimierung </strong>und der  Barrierefreiheit, daher werden Sie mit dem CMS Papoo immer eine  erfolgreiche und auch für behinderte Mitbürger zugängliche Webseite  erstellen können.</p><p> </p><p>Diesen Artikel hier z.B. können Sie  unter Inhalte / Startseite-Inhalt in  der Administration verändern.</p><h2>Administration  des CMS</h2><p><a rel=\"lightbox\" title=\"Screenshot der Administration\" href=\"../images/10_admin_screen.jpg\"><img style=\"float: left; margin: 10px;\" title=\"Screenshot  der Administration\" alt=\"Screenshot der Administration\" src=\"../images/thumbs/10_admin_screen.jpg\" height=\"74\" width=\"120\" /></a>Die  Administration Ihrer neuen Webseite finden Sie unter <a href=\"http://www.test.papoo.de/373/pro/interna/\"> webseite.de/interna/</a>. Klicken Sie auf den Link um in die  Administration zu gelangen.</p><p>Sie können sich dort mit dem  Benutzernamen root und dem Passwort anmelden dass Sie bei der  Installation im letzten Schritt vergeben haben. Falls Sie sich nicht  mehr daran erinner, führen Sie einfach den letzten Schritt  /setup/start.php noch einmal durch.</p><p> </p><p>Für eine größere  Ansicht des Screenshots klicken Sie ihn einfach mal an. Diese Funktion  können Sie übrigens in der Administration einfach per Klick einbauen.</p><p> </p><h2><span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Kostenloser Support</span></h2><p>Wenn  Sie Fragen zu  Ihrem neuen  Papoo System haben können Sie jederzeit in  unserem <a href=\"http://www.papoo.de/forum.php\"> Community Forum</a> um Rat   fragen. Und keine Sorge, Neulinge werden dort genauso herzlich   willkommen geheißen wie die alten Hasen.<br /> <img style=\"margin: 10px; float: right;\" title=\"Papoo  Support\" alt=\"Papoo Support\" src=\"../images/10_support1.jpg\" height=\"135\" width=\"200\" /></p><h2><br />Business  Support</h2><p>Zusätzlich stehen wir unseren  Papoo Plus, Pro und <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Business</span> Kunden auch   immer gerne per E-Mail oder Telefon über unsere Hotline hilfreich zur   Seite.</p><p> </p><p>Wenn Sie weitere Fragen haben, wie z.B. ob  wir  von <a href=\"http://www.papoo.de/\" title=\"Papoo\"> Papoo</a> <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Software</span> ihr <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Design</span> erstellen oder  spezielle Anpassungen oder Funktionen  programmieren können, dann  kontaktieren Sie uns einfach oder rufen uns  an: 0228 / 280 56 68.</p><p> </p><h2>Dokumentation</h2><p>Eine  ausführliche PDF Dokumentation finden sie  auf unserer <a href=\"http://www.papoo.de/cms-dokumentation/cms-dokumentation-papoo.html\"> CMS Webseite</a>, dort finden Sie auch unser Online Hilfe Forum, unsere  Online Wiki Dokumentation und eine FAQ Sammlung.</p><p> </p><h2><span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Designvorlagen für Papoo<br /></span></h2><p><img style=\"float: left; margin: 10px;\" title=\"Design Varianten\" alt=\"Design Varianten\" src=\"../images/10_designs.jpg\" height=\"110\" width=\"166\" /></p><p>Die  Grundlage des CSS für das Standard Layout ist das 960 CSS Grid System,  für viele Designs benutzen wir auch das YAML Layout.</p><p> </p><p>In  unseren Papoo Plus, Pro und Business Versionen liefern wir mind. 30  fertige, sofort nutzbare Designvorlagen mit. Wie diese aussehen, davon  können Sie sich auf unserer Webseite ein Bild machen.</p><h2>Individuelle  Designs für Ihre Webseite</h2><p>Gerne setzen wir für Sie auch ein  invididuelle Design für Sie um, wir können das für Sie erstellen mit  unseren TOP Grafik Designern oder wir setzen ein erstelltest Photoshop  Design oder vorhandenes Design für Sie um. Wenn Sie Fragen dazu haben,  rufen Sie uns einfach an: 0228 280 56 68</p><p><br />Prinzipiell läßt sich  nahezu jedes beliebige Design mit Papoo umsetzen. Was alles möglich  ist, können Sie in unserer Referenzliste schauen mit weit mehr als 1000  ausgewählten Einträgen.</p><p> </p><h2>Viel Erfolg</h2><p>Viel Erfolg  mit Ihrem neuen <a href=\"http://www.papoo.de/\" title=\"Papoo\"> Papoo</a> CMS System</p><p> </p><p>Ihr  Team von Papoo Software.</p>', kalender_kategori_der_sseite='2', kalender_gacode='1234'  ; ##b_dump##
INSERT INTO `XXX_plugin_landing_page_sites` SET kalender_id='4', kalender_interne_bezeichnung='COPY - asfasdf', kalender_domain='bfsystem2.de', kalender_titel_der_seite='', kalender_meta_description='etert\r\newtrwert', kalender_meta_keywords='', kalender_design='559', kalender_content='<h1>Herzlich Willkommen auf Ihrer Internetseite.</h1><p><strong>Herzlichen  Glückwunsch.</strong> Die Installation Ihrer  Papoo Edition hat   funktioniert und Sie können das barrierefreie und  suchmaschienenoptimierte CMS  Papoo jetzt voll  benutzen.<img style=\"float: right; margin: 10px;\" title=\"Gratulation - die Installation hat funktioniert\" alt=\"Gratulation - die Installation hat funktioniert\" src=\"../images/10_gratulation.jpg\" height=\"205\" width=\"310\" /></p><p> </p><p>Mit  dem CMS Papoo steht Ihnen ein <strong>vollwertiges Content Management  System</strong> zur Verfügung mit dem Sie beliebig viele Inhalte  erstellen und managen können.</p><p> </p><p>Unser besonderer Focus  liegt auf der <strong>Suchmaschinenoptimierung </strong>und der  Barrierefreiheit, daher werden Sie mit dem CMS Papoo immer eine  erfolgreiche und auch für behinderte Mitbürger zugängliche Webseite  erstellen können.</p><p> </p><p>Diesen Artikel hier z.B. können Sie  unter Inhalte / Startseite-Inhalt in  der Administration verändern.</p><h2>Administration  des CMS</h2><p><a rel=\"lightbox\" title=\"Screenshot der Administration\" href=\"../images/10_admin_screen.jpg\"><img style=\"float: left; margin: 10px;\" title=\"Screenshot  der Administration\" alt=\"Screenshot der Administration\" src=\"../images/thumbs/10_admin_screen.jpg\" height=\"74\" width=\"120\" /></a>Die  Administration Ihrer neuen Webseite finden Sie unter <a href=\"http://www.test.papoo.de/373/pro/interna/\"> webseite.de/interna/</a>. Klicken Sie auf den Link um in die  Administration zu gelangen.</p><p>Sie können sich dort mit dem  Benutzernamen root und dem Passwort anmelden dass Sie bei der  Installation im letzten Schritt vergeben haben. Falls Sie sich nicht  mehr daran erinner, führen Sie einfach den letzten Schritt  /setup/start.php noch einmal durch.</p><p> </p><p>Für eine größere  Ansicht des Screenshots klicken Sie ihn einfach mal an. Diese Funktion  können Sie übrigens in der Administration einfach per Klick einbauen.</p><p> </p><h2><span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Kostenloser Support</span></h2><p>Wenn  Sie Fragen zu  Ihrem neuen  Papoo System haben können Sie jederzeit in  unserem <a href=\"http://www.papoo.de/forum.php\"> Community Forum</a> um Rat   fragen. Und keine Sorge, Neulinge werden dort genauso herzlich   willkommen geheißen wie die alten Hasen.<br /> <img style=\"margin: 10px; float: right;\" title=\"Papoo  Support\" alt=\"Papoo Support\" src=\"../images/10_support1.jpg\" height=\"135\" width=\"200\" /></p><h2><br />Business  Support</h2><p>Zusätzlich stehen wir unseren  Papoo Plus, Pro und <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Business</span> Kunden auch   immer gerne per E-Mail oder Telefon über unsere Hotline hilfreich zur   Seite.</p><p> </p><p>Wenn Sie weitere Fragen haben, wie z.B. ob  wir  von <a href=\"http://www.papoo.de/\" title=\"Papoo\"> Papoo</a> <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Software</span> ihr <span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Design</span> erstellen oder  spezielle Anpassungen oder Funktionen  programmieren können, dann  kontaktieren Sie uns einfach oder rufen uns  an: 0228 / 280 56 68.</p><p> </p><h2>Dokumentation</h2><p>Eine  ausführliche PDF Dokumentation finden sie  auf unserer <a href=\"http://www.papoo.de/cms-dokumentation/cms-dokumentation-papoo.html\"> CMS Webseite</a>, dort finden Sie auch unser Online Hilfe Forum, unsere  Online Wiki Dokumentation und eine FAQ Sammlung.</p><p> </p><h2><span class=\"lang_en\" xml:lang=\"en\" lang=\"en\"> Designvorlagen für Papoo<br /></span></h2><p><img style=\"float: left; margin: 10px;\" title=\"Design Varianten\" alt=\"Design Varianten\" src=\"../images/10_designs.jpg\" height=\"110\" width=\"166\" /></p><p>Die  Grundlage des CSS für das Standard Layout ist das 960 CSS Grid System,  für viele Designs benutzen wir auch das YAML Layout.</p><p> </p><p>In  unseren Papoo Plus, Pro und Business Versionen liefern wir mind. 30  fertige, sofort nutzbare Designvorlagen mit. Wie diese aussehen, davon  können Sie sich auf unserer Webseite ein Bild machen.</p><h2>Individuelle  Designs für Ihre Webseite</h2><p>Gerne setzen wir für Sie auch ein  invididuelle Design für Sie um, wir können das für Sie erstellen mit  unseren TOP Grafik Designern oder wir setzen ein erstelltest Photoshop  Design oder vorhandenes Design für Sie um. Wenn Sie Fragen dazu haben,  rufen Sie uns einfach an: 0228 280 56 68</p><p><br />Prinzipiell läßt sich  nahezu jedes beliebige Design mit Papoo umsetzen. Was alles möglich  ist, können Sie in unserer Referenzliste schauen mit weit mehr als 1000  ausgewählten Einträgen.</p><p> </p><h2>Viel Erfolg</h2><p>Viel Erfolg  mit Ihrem neuen <a href=\"http://www.papoo.de/\" title=\"Papoo\"> Papoo</a> CMS System</p><p> </p><p>Ihr  Team von Papoo Software.</p>', kalender_kategori_der_sseite='2', kalender_gacode='1234'  ; ##b_dump##
