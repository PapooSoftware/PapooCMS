<?php

/**
#####################################
# RSS-Feed Plugin f�r Papoo   	    #
#           							          #
# Authors: Thomas Schoessow         #
# http://www.tschoessow.org         #
# Internet                          #
#####################################
# PHP Version >4.2                  #
#####################################

Dieses Programm ist freie Software. Sie k�nnen es unter den
Bedingungen der GNU General Public License, wie von der Free
Software Foundation ver�ffentlicht, weitergeben und/oder
modifizieren, entweder gem�� Version 2 der Lizenz oder
(nach Ihrer Option) jeder sp�teren Version.

Die Ver�ffentlichung dieses Programms erfolgt in der Hoffnung,
da� es Ihnen von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE,
sogar ohne die implizite Garantie der MARKTREIFE oder der
VERWENDBARKEIT F�R EINEN BESTIMMTEN ZWECK. Details finden Sie
in der GNU General Public License.

Sie sollten eine Kopie der GNU General Public License
zusammen mit diesem Programm erhalten haben. Falls nicht, s
chreiben Sie an die Free Software Foundation, Inc., 59
Temple Place, Suite 330, Boston, MA 02111-1307, USA.
 */

//define('MAGPIE_OUTPUT_ENCODING', 'ISO-8859-1');
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');
define('MAGPIE_DIR', dirname(__FILE__)."/");
define('MAGPIE_DEBUG', 0);
define('MAGPIE_CACHE_DIR',dirname(dirname(__FILE__))."/rsscache");

// Setzt den Cache auf 1 Stunde.
define('MAGPIE_CACHE_AGE', 3600);

require_once("feedcreator.class.php");
require_once(MAGPIE_DIR.'rss_fetch.inc');

/**
 * Class rssfeed_class
 */
class rssfeed_class
{
	/**
	 * rssfeed_class constructor.
	 */
	function __construct()
	{
		global $content, $checked, $db, $cms, $module, $menu;
		$this->content = & $content;
		$this->checked = & $checked;
		$this->db = & $db;
		$this->cms = & $cms;
		$this->module = & $module;
		$this->menu= & $menu;
		//Plugin installiert, dann im Kopf einbinden
		$this->content->template['rssfeed'] = "ok";

		$this->make_rssfeed();
	}

	/**
	 * @param $item
	 * @return false|float|int
	 */
	function displayUnixTimestamp($item)
	{
		$rss_2_date = $item['pubdate'];
		$rss_1_date = $item['dc']['date'];
		$atom_date = $item['issued'];
		if ($atom_date != "") $date = parse_w3cdtf($atom_date);
		if ($rss_1_date != "") $date = parse_w3cdtf($rss_1_date);
		if ($rss_2_date != "") $date = strtotime($rss_2_date);
		if ($date == "") $date = time();
		return $date;
	}


	function make_rssfeed()
	{
		global $template;
		global $version;

		// Es gibt doch ein $version f�r Version 2 von Papoo. Also m�ssen wir hier auf
		//Vorhandensein einer 2 oder leer an erster Stelle pr�fen.
		$myversion="";

		if (substr($version,0,1)=="3") {
			$myversion="3";
		}

		$this->make_lang_var();

		if (strpos("XXX".$template, "rssfeed_start.html")) {
			$this->content->template['Papooversion'] = $myversion;
		}

		if(!isset($this->module->module_aktiv['mod_rssfeed_show'])) {
			$this->module->module_aktiv['mod_rssfeed_show'] = false;
		}
		if ($this->module->module_aktiv['mod_rssfeed_show']) {
			$this->show_feed('FEED1');
		}

		if(!isset($this->module->module_aktiv['mod_rssfeed_show1'])) {
			$this->module->module_aktiv['mod_rssfeed_show1'] = false;
		}
		if ($this->module->module_aktiv['mod_rssfeed_show1']) {
			$this->show_feed('FEED2');
		}

		if(!isset($this->module->module_aktiv['mod_rssfeed_show2'])) {
			$this->module->module_aktiv['mod_rssfeed_show2'] = false;
		}
		if ($this->module->module_aktiv['mod_rssfeed_show2']) {
			$this->show_feed('FEED3');
		}

		if (strpos("XXX".$template, "rssfeed_config.html")) {
			$this->validate_config();
		}

		if (strpos("XXX".$template, "rssfeed_create.html")) {
			$this->create_feed();
		}

		if (strpos("XXX".$template, "rssfeed_show.html")) {
			$this->show_feed('FEED1');
		}
		if (strpos("XXX".$template, "rssfeed_show1.html")) {
			$this->show_feed('FEED2');
		}
		if (strpos("XXX".$template, "rssfeed_show2.html")) {
			$this->show_feed('FEED3');
		}
		if (strpos("XXX".$template, "rssfeed_show3.html")) {
			$this->show_feed('FEED4');
		}
		if (strpos("XXX".$template, "rssfeed_show4.html")) {
			$this->show_feed('FEED5');
		}
		if (strpos("XXX".$template, "rssfeed_show5.html")) {
			$this->show_feed('FEED6');
		}
		if (strpos("XXX".$template, "rssfeed_show6.html")) {
			$this->show_feed('FEED7');
		}
		if (strpos("XXX".$template, "rssfeed_show7.html")) {
			$this->show_feed('FEED8');
		}
		if (strpos("XXX".$template, "rssfeed_show8.html")) {
			$this->show_feed('FEED9');
		}
		if (strpos("XXX".$template, "rssfeed_show9.html")) {
			$this->show_feed('FEED10');
		}
	}

	function write_into_db()
	{
		global $db_praefix;
		$sql=sprintf("DELETE FROM %s WHERE rss_feedlang='%s'",
			$db_praefix."rss_feed_config",
			$this->db->escape($this->checked->feedlang)
		);
		$this->db->query($sql);
		$query = sprintf("INSERT INTO `%s` SET rss_feedtitle='%s', rss_feeddesc='%s', rss_feedcount='%s', rss_feedlang='%s', rss_feedprefix='%s' , rss_feed_id=1",
			$db_praefix."rss_feed_config",
			$this->db->escape($this->checked->feedtitle),
			$this->db->escape($this->checked->feeddesc),
			$this->db->escape($this->checked->feedcount),
			$this->db->escape($this->checked->feedlang),
			$this->db->escape($this->checked->feedprefix)
		);
		// SQL-Anweisung ausf�hren
		$this->db->query($query);
	}

	/**
	 * @param $arg1
	 */
	function write_url_into_db($arg1)
	{
		global $db_praefix;

		$content='';

		if($arg1=='FEED1') {
			$content= $this->checked->feedurl1;
		}

		if($arg1=='FEED2') {
			$content= $this->checked->feedurl2;
		}

		if($arg1=='FEED3') {
			$content= $this->checked->feedurl3;
		}

		if($arg1=='FEED4') {
			$content= $this->checked->feedurl4;
		}

		if($arg1=='FEED5') {
			$content= $this->checked->feedurl5;
		}

		if($arg1=='FEED6') {
			$content= $this->checked->feedurl6;
		}

		if($arg1=='FEED7') {
			$content= $this->checked->feedurl7;
		}

		if($arg1=='FEED8') {
			$content= $this->checked->feedurl8;
		}

		if($arg1=='FEED9') {
			$content= $this->checked->feedurl9;
		}

		if($arg1=='FEED10') {
			$content= $this->checked->feedurl10;
		}

		$query = sprintf("SELECT * FROM `%s` WHERE rss_feed_short='%s'",
			$db_praefix."rss_feed_url",$this->db->escape($arg1));
		$result = $this->db->get_results($query);

		if($result) {
			$query = sprintf("UPDATE `%s` SET rss_feed_url='%s' WHERE rss_feed_short='%s'",
				$db_praefix."rss_feed_url",
				$this->db->escape($content),$this->db->escape($arg1));
		}
		else {
			$query = sprintf("INSERT INTO `%s` (rss_feed_url,rss_feed_short) VALUES('%s','%s')",
				$db_praefix."rss_feed_url",
				$this->db->escape($content),$this->db->escape($arg1));
		}
		$this->db->get_results($query);
	}

	function read_from_db()
	{
		global $db_praefix;
		$query = sprintf("SELECT * FROM `%s` WHERE rss_feed_id=1 AND rss_feedlang='%d'",
			$db_praefix."rss_feed_config",
			$this->db->escape($this->cms->lang_back_content_id)
		);
		$result = $this->db->get_results($query);

		$this->content->template['rss_feedtitle'] = $result[0]->rss_feedtitle;
		$this->content->template['rss_feeddesc'] = $result[0]->rss_feeddesc;
		$this->content->template['rss_feedcount'] = $result[0]->rss_feedcount;
		$this->content->template['rss_feedlang'] = $result[0]->rss_feedlang;
		$this->content->template['rss_feedprefix'] = $result[0]->rss_feedprefix;
	}

	/**
	 * @param $arg1
	 */
	function read_url_from_db($arg1)
	{
		global $db_praefix;

		$query = sprintf("SELECT * FROM `%s` WHERE rss_feed_short='%s'",
			$db_praefix."rss_feed_url",$this->db->escape($arg1));
		$result = $this->db->get_results($query);

		IfNotSetNull($result[0]->rss_feed_url);

		$this->content->template['rss_feed_url_'.$arg1] = $result[0]->rss_feed_url;
	}

	function validate_config()
	{
		if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="config") {
			$form_fehler=false;

			if(strlen($this->checked->feedtitle) < 1) {
				$rss_fehler_feedtitle= $this->content->template['plugin']['rssfeed']['error1'];
				$form_fehler=true;
				$this->content->template['rss_fehler_feedtitle']=$rss_fehler_feedtitle;
			}

			if(strlen($this->checked->feeddesc) < 1) {
				$rss_fehler_feeddesc= $this->content->template['plugin']['rssfeed']['error2'];
				$form_fehler=true;
				$this->content->template['rss_fehler_feeddesc']=$rss_fehler_feeddesc;
			}

			if(!is_numeric($this->checked->feedcount)) {
				$rss_fehler_feedcount= $this->content->template['plugin']['rssfeed']['error3'];
				$form_fehler=true;
				$this->content->template['rss_fehler_feedcount']=$rss_fehler_feedcount;
			}
			else {
				if (is_numeric($this->checked->feedcount)) {
					if (intval($this->checked->feedcount) < 1) {
						$rss_fehler_feedcount= $this->content->template['plugin']['rssfeed']['error4'];
						$form_fehler=true;
						$this->content->template['rss_fehler_feedcount']=$rss_fehler_feedcount;
					}
				}
				else {
					$rss_fehler_feedcount= $this->content->template['plugin']['rssfeed']['error5'];
					$form_fehler=true;
					$this->content->template['rss_fehler_feedcount']=$rss_fehler_feedcount;
				}
			}

			if(!is_numeric($this->checked->feedlang)) {
				$rss_fehler_feedlang= $this->content->template['plugin']['rssfeed']['error6'];
				$form_fehler=true;
				$this->content->template['rss_fehler_feedlang']=$rss_fehler_feedlang;
			}
			else {
				if (is_numeric($this->checked->feedlang)) {
					if (intval($this->checked->feedlang) < 1) {
						$rss_fehler_feedlang= $this->content->template['plugin']['rssfeed']['error7'];
						$form_fehler=true;
						$this->content->template['rss_fehler_feedlang']=$rss_fehler_feedlang;
					}
				}
				else {
					$rss_fehler_feedlang= $this->content->template['plugin']['rssfeed']['error8'];
					$form_fehler=true;
					$this->content->template['rss_fehler_feedlang']=$rss_fehler_feedlang;
				}
			}

			if($form_fehler==false) {
				$this->write_into_db();
				$this->read_from_db();
			}
			else {
				$this->content->template['rss_feedtitle'] = $this->checked->feedtitle;
				$this->content->template['rss_feeddesc'] = $this->checked->feeddesc;
				$this->content->template['rss_feedcount'] = $this->checked->feedcount;
				$this->content->template['rss_feedlang'] = $this->checked->feedlang;
				$this->content->template['rss_feedprefix'] = $this->checked->feedprefix;
			}
		}
		else {
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED1") {
				$this->write_url_into_db("FEED1");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED2") {
				$this->write_url_into_db("FEED2");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED3") {
				$this->write_url_into_db("FEED3");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED4") {
				$this->write_url_into_db("FEED4");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED5") {
				$this->write_url_into_db("FEED5");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED6") {
				$this->write_url_into_db("FEED6");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED7") {
				$this->write_url_into_db("FEED7");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED1") {
				$this->write_url_into_db("FEED1");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED8") {
				$this->write_url_into_db("FEED8");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED9") {
				$this->write_url_into_db("FEED9");
			}
			if (isset($this->checked->sendvalue) && $this->checked->sendvalue=="FEED10") {
				$this->write_url_into_db("FEED10");
			}

			$this->read_from_db();
			$this->read_url_from_db("FEED1");
			$this->read_url_from_db("FEED2");
			$this->read_url_from_db("FEED3");
			$this->read_url_from_db("FEED4");
			$this->read_url_from_db("FEED5");
			$this->read_url_from_db("FEED6");
			$this->read_url_from_db("FEED7");
			$this->read_url_from_db("FEED8");
			$this->read_url_from_db("FEED9");
			$this->read_url_from_db("FEED10");
		}
	}

	/**
	 * @param string $dofeed
	 */
	function create_feed($dofeed="")
	{
		global $db_praefix;

		IfNotSetNull($this->checked->dofeed);
		if ($this->checked->dofeed or $dofeed) {
			$query = sprintf("SELECT * FROM `%s` WHERE rss_feed_id=1 AND rss_feedlang='%d'",
				$db_praefix."rss_feed_config",
				$this->db->escape($this->cms->lang_back_content_id)
			);
			$result = $this->db->get_results($query);

			if (!empty($result)) {
				$rss = new UniversalFeedCreator();
				//$rss->useCached();
				$rss->title = $result[0]->rss_feedtitle;
				$rss->description = $result[0]->rss_feeddesc;
				$rss->descriptionTruncSize = 200;
				$rss->descriptionHtmlSyndicated = true;

				$rss_prefix=$result[0]->rss_feedprefix;
				$rss_feedlanguage=$result[0]->rss_feedlang;
				$rss_feedcount=$result[0]->rss_feedcount;

				$query = sprintf("SELECT * from ".$db_praefix."papoo_daten LIMIT 1");
				$result=$this->db->get_results($query);

				//$feedautor=$result[0]->admin_email.'('.$result[0]->autor_seite.')';

				$rss->link=$rss_prefix.$result[0]->seitenname.PAPOO_WEB_PFAD;
				$rss->syndicationURL = $rss_prefix.$result[0]->seitenname.$_SERVER["PHP_SELF"];

				//$heute=date("d"."."."m"."."."Y"." "."H".":"."i");
				$query = sprintf("	SELECT DISTINCT lan_repore_id, lcat_id, lan_teaser, header, cattextid, stamptime,dokuser, url_header, dok_teaserfix FROM ".$db_praefix."papoo_language_article artikel, ".$db_praefix."papoo_repore menu , ".$this->cms->tbname['papoo_lookup_art_cat']."
									WHERE lang_id=".$rss_feedlanguage." 
									AND artikel.lan_repore_id = menu.reporeID 
									AND menu.publish_yn=1 
									AND artikel.lan_rss_yn=1 
									AND menu.pub_dauerhaft='1' 
									AND lart_id = lan_repore_id
									GROUP BY lan_repore_id
									ORDER BY stamptime DESC
			
									LIMIT ".$rss_feedcount);
				/**
				 * OR
				lang_id=".$rss_feedlanguage."
				AND artikel.lan_repore_id = menu.reporeID
				AND menu.publish_yn=1
				AND artikel.lan_rss_yn=1
				AND menu.pub_start<='".$heute."'
				AND menu.pub_verfall>='".$heute."'
				 *
				 */

				$res = $this->db->get_results($query);

				if ($res) {
					foreach ( $res as $result ) {
						/** Bilder rausholen*/
						$sql = sprintf("SELECT teaser_bild_html FROM %s WHERE reporeID='%s'",
							$db_praefix."papoo_repore",
							$result->lan_repore_id
						);
						$bild=$this->db->get_var($sql);
						//$bildhtml="<span style=\"float:left;margin:5px;\">".$bild."</span>";
						$bildhtml=$bild;
						$item = new FeedItem();
						$item->title = $result->header;
						$this->menu->data_front_complete =$this->menu->menu_data_read("FRONT");
						$this->m_url = array();
						$this->get_verzeichnis($result->lcat_id);
						$this->m_url = array_reverse($this->m_url);
						$urldat_1="";
						if (!empty($this->m_url)) {
							foreach ($this->m_url as $urldat) {
								$urldat_1 .= $this->menu->urlencode($urldat) . "/";
							}
						}

						$header_url = $urldat_1 . (($this->menu->urlencode($result->url_header))) . ".html";

						if ($this->cms->mod_rewrite == 2) {
							$item->link = $rss->link.'/'.$this->content->template['sulrstrenner'].$header_url;
						}
						else {
							$item->link = $rss->link.'/index.php?menuid='.$result->cattextid.'&reporeid='.$result->lan_repore_id;
						}
						if ($this->cms->mod_free==1) {
							$item->link = $rss->link.$result->url_header;
						}

						#if ($rss->link=="..")$rss->link="";
						$bildhtml=str_replace("./", $rss->link."/", $bildhtml);

						if($bild=="") {
							//Wenn wir kein Teaserbild haben, dann brauchen wir auch kein HTML drum rum
							$item->description = $result->lan_teaser;
						}
						else {
							$item->description = $bildhtml.$result->lan_teaser;
						}
						$item->description=str_replace("../", $rss->link."/", $item->description);
						$item->comments = $result->dok_teaserfix;
						//Schreiber des Artikels suchen und verwenden

						$sqluser=sprintf("SELECT username,email FROM %s WHERE userid='%s'",$db_praefix."papoo_user",$result->dokuser);
						$resuser = $this->db->get_results($sqluser);

						$item->descriptionTruncSize = 200;
						$item->descriptionHtmlSyndicated = true;
						$item->source = $rss->link;
						$item->guid = $item->link;
						$item->author = $resuser[0]->email.' ('.$resuser[0]->username.') ';

						// Bis zum Patch der Timestamp Daten nehmen wir die Daten aus dem Feld erstellungsdatum
						$item->date = (int)$result->stamptime;
						$rss->addItem($item);
					}

					$pfad = dirname(dirname(__FILE__))."/feed".$this->cms->lang_back_content_id.".xml";
					//Mit dem Parameter false verhindern wir das l�stige �berschreiben der Seite im Plugin-Manager
					$rss->saveFeed("RSS2.0",$pfad,false);
				}
			}
		}
	}

	/**
	 * Verzeichnisse rauskriegen bzw. Men�namen f�r mod_rewrite mit sprechurls
	 *
	 * @param string $menid
	 * @param string $search
	 * @return mixed|void
	 */
	function get_verzeichnis($menid = "", $search = "")
	{
		if (!empty($_GET['var1']) or !empty($search) or $this->cms->mod_surls == 1) {
			$mendata = $this->menu->data_front_complete;
			foreach ($mendata as $menuitems) {
				// aktuelle men�id finden
				if ($menid == $menuitems['menuid']) {
					// echo $menid;
					$this->m_url[] = ($menuitems['url_menuname']);
					// Nicht oberste Ebene, neu aufrufen
					if ($menuitems['untermenuzu'] != 0) {
						$this->get_verzeichnis($menuitems['untermenuzu']);
					}
					else {
						return true;
					}
				}
			}
		}
	}

	/**
	 * @param $arg1
	 */
	function show_feed($arg1)
	{
		global $db_praefix;
		global $version;

		$rss_arr = array();

		// Es gibt doch ein $version f�r Version 2 von Papoo. Also m�ssen wir hier auf
		//Vorhandensein einer 2 oder leer an erster Stelle pr�fen.
		$myversion="";

		if (substr($version,0,1)=="3") {
			$myversion="3";
		}

		$query = sprintf("SELECT * FROM `%s` WHERE rss_feed_short='%s'",
			$db_praefix."rss_feed_url",$this->db->escape($arg1));

		$result = $this->db->get_results($query);

		$this->content->template['Papooversion'] = $myversion;

		//Damit auch die Templates die Version kennen, �bergeben wir sie hier

		if ($result) {
			$this->content->template['rss_channel_error']='';

			$rss = fetch_rss($result[0]->rss_feed_url);
			if (!$rss) {
				$this->content->template['rss_channel_error'] = magpie_error();
			}
			else {
				$channel=$rss->channel['title'];

				$this->content->template['rss_channel'.$arg1] = $channel;
				$this->content->template['rss_channel_link'.$arg1] = $result[0]->rss_feed_url;

				$query = sprintf("SELECT rss_feedcount FROM `%s`", $db_praefix."rss_feed_config");
				$feedcount = $this->db->get_var($query);
				foreach ($rss->items as $item) {
					if ($feedcount) {
						//$datum=date("d.m.Y",$this->displayUnixTimestamp($item));
						$datum=1;
						if (strtolower($rss->encoding)=="iso-8859-1") {
							$item['title']=utf8_encode($item['title']);
							$item['description']=utf8_encode($item['description']);
						}
						array_push($rss_arr, array (
								'link' => $item['link'],
								'title' => $item['title'],
								'description' => $item['description'],
								'date' => $datum)
						);
						$feedcount--;
					}
				}
				$this->content->template['rss_daten'.$arg1] = $rss_arr;
			}
		}
		unset($rss_arr);
	}

	/**
	 * @return bool
	 */
	function make_lang_var() {
		//Nicht mehr n�tig
		return true;
	}
}

$rssfeed = new rssfeed_class();
