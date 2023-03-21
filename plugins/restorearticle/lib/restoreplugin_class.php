<?php

/**
 * @autor: Andreas Gritzan <ag@papoo.de>
 */

/**
 * Die restoreplugin_class implementiert die hauptfunktionen des Artikel wiederherstellen-Plugins.
 *
 * */
#[AllowDynamicProperties]
class restoreplugin_class
{
	/**
	 * restoreplugin_class Konstruktor.
	 *
	 */
	function __construct()
	{
		global $db, $content, $checked, $db_praefix;
		$this->db = &$db;
		$this->content = $content;
		$this->checked = $checked;

		$this->article_version_language = $db_praefix . "papoo_version_language_article";
		$this->article_language = $db_praefix . "papoo_language_article";
		$this->article_version_lookup = $db_praefix . "papoo_version_lookup_article";
		$this->article_lookup = $db_praefix . "papoo_lookup_article";
		$this->article_version_repore = $db_praefix . "papoo_version_repore";
		$this->article_repore = $db_praefix . "papoo_repore";
		$this->article_version_write = $db_praefix . "papoo_version_lookup_write_article";
		$this->article_write = $db_praefix . "papoo_lookup_write_article";
		$this->version_lookup_art_cat = $db_praefix . "papoo_version_lookup_art_cat";
		$this->lookup_art_cat = $db_praefix . "papoo_lookup_art_cat";

		if(defined('admin')) {
			$this->content->template['css_path'] = $css_path = PAPOO_WEB_PFAD.'/plugins/restorearticle/css';

			$this->GetContent();
			$this->CheckPost();
		}
	}

	private function GetContent()
	{
		// SQL um die letzte Version aller Artikel zu holen die nicht mehr in den normalen Tabellen zu finden sind, die Überschrift des Artikels, wieviele Versionen
		// es gibt von diesem Artikel und den Teaser Text
		$suchterm = isset($_REQUEST['search']) ? $_REQUEST['search'] : NULL;

		$suchterm = $this->db->escape($suchterm);

		$sql = sprintf("SELECT tabelle_ohne_lastedit.*, repore_tabelle.timestamp as lastedit FROM (
                                        SELECT langtable.lan_repore_id, langtable.versionid as version, langtable.lan_metatitel, langtable.lan_teaser, COUNT(langtable.versionid) as versions
                                        FROM (SELECT * FROM %s a LEFT JOIN (SELECT lan_repore_id AS existing_article_id FROM %s) b ON a.lan_repore_id=b.existing_article_id ORDER BY a.versionid DESC) langtable
                                        WHERE langtable.existing_article_id IS NULL
                                        GROUP BY langtable.lan_repore_id
                                      ) tabelle_ohne_lastedit
                                      LEFT JOIN %s repore_tabelle ON tabelle_ohne_lastedit.version=repore_tabelle.versionid
                                      WHERE tabelle_ohne_lastedit.lan_teaser LIKE '%%%s%%' OR tabelle_ohne_lastedit.lan_metatitel LIKE '%%%s%%' OR tabelle_ohne_lastedit.lan_repore_id LIKE '%%%s%%';
                            ",
			$this->article_version_language,
			$this->article_language,
			$this->article_version_repore,
			$suchterm,
			$suchterm,
			$suchterm);

		#$sql = sprintf("SELECT versionid FROM %s WHERE lan_repore_id=4", $this->version_language);

		$result = $this->db->get_results($sql, ARRAY_A);

		foreach($result as &$entry) {
			$teaser = &$entry['lan_teaser'];

			$teaser = strip_tags($teaser);
		}

		$this->content->template['plugin']['restorearticle']['article_search'] = $result;
	}

	private function CheckPost()
	{

		if(isset($this->checked->restorearticle) and !empty($this->checked->restorearticle)) {
			foreach($this->checked->restorearticle as $lan_repore_id=>$versionid) {
				// Die Version $versionid der language_article wieder einfügen
				$sql = sprintf("INSERT INTO %s
                              (lan_repore_id, lang_id, header, lan_teaser, lan_teaser_link,
                               lan_article, lan_article_sans, lan_article_markdown, lan_metatitel, lan_metadescrip,
                                lan_metakey, lan_rss_yn, url_header, publish_yn_lang
                                )
                                SELECT lan_repore_id, lang_id, header, lan_teaser, lan_teaser_link,
                                             lan_article, lan_article_sans, lan_article_markdown,
                                              lan_metatitel, lan_metadescrip, lan_metakey, lan_rss_yn, url_header, publish_yn_lang
                                FROM %s
                                WHERE lan_repore_id=%d AND versionid=%d;

                ", $this->article_language, $this->article_version_language, $lan_repore_id, $versionid);

				$this->db->query($sql);

				// Die Version $versionid der lookup_article wieder einfügen
				$sql = sprintf("INSERT INTO %s
                              (article_id, gruppeid_id
                                )
                                SELECT article_id, gruppeid_id
                                FROM %s
                                WHERE article_id=%d AND version_lookup_article_id=%d

                ", $this->article_lookup, $this->article_version_lookup, $lan_repore_id, $versionid);

				$this->db->query($sql);

				// Die Version $versionid der repore wieder einfügen
				$sql = sprintf("INSERT INTO %s
                                (
                                    reporeID, text, text_bbcode, cattextid, dokuser, dokgruppe, dokschreibengrid,  dokschreiben_userid, doklesengrid,
                                    ueberschrift, uberschrift_hyn, timestamp, verfall, pub_verfall, pub_start, pub_verfall_page, pub_start_page,
                                    pub_wohin, pub_danach_aktiv, pub_dauerhaft, publish_yn, publish_yn_intra, intranet_yn, count, teaser, teaser_list,
                                    teaser_bbcode, teaser_text, teaser_bild, teaser_bild_html, teaser_link, teaser_bild_lr, teaser_atyn, count_download,
                                    allow_publish, comment_yn, allow_other, order_id, allow_comment, list_site, artikel_news_list, teaser_bild_groesse,
                                    teaser_menu, teaser_sub_menu, teaser_artikel, dokuser_last, dok_teaserfix, dok_show_teaser_link,
                                    dok_show_teaser_teaser, cat_category_id
                                )
                                SELECT
                                    reporeID, text, text_bbcode, cattextid, dokuser, dokgruppe, dokschreibengrid, dokschreiben_userid, doklesengrid,
                                    ueberschrift, uberschrift_hyn, timestamp, verfall, pub_verfall, pub_start, pub_verfall_page, pub_start_page, pub_wohin,
                                    pub_danach_aktiv, pub_dauerhaft, publish_yn, publish_yn_intra, intranet_yn, count, teaser, teaser_list, teaser_bbcode,
                                    teaser_text, teaser_bild, teaser_bild_html, teaser_link, teaser_bild_lr, teaser_atyn, count_download, allow_publish,
                                    comment_yn, allow_other, order_id, allow_comment, list_site, artikel_news_list, teaser_bild_groesse, teaser_menu,
                                    teaser_sub_menu, teaser_artikel, dokuser_last, dok_teaserfix, dok_show_teaser_link, dok_show_teaser_teaser,
                                    cat_category_id
                                FROM %s
                                WHERE reporeID=%d AND versionid=%d

                ", $this->article_repore, $this->article_version_repore, $lan_repore_id, $versionid);

				$this->db->query($sql);

				// Die Version $versionid der lookup_write_article wieder einfügen
				$sql = sprintf("INSERT INTO %s
                              (article_wid_id, gruppeid_wid_id
                                )
                                SELECT article_wid_id, gruppeid_wid_id
                                FROM %s
                                WHERE article_wid_id=%d AND version_lookup_article_id=%d

                ", $this->article_write, $this->article_version_write, $lan_repore_id, $versionid);

				$this->db->query($sql);

				// Die Version $versionid der lookup_art_cat wieder einfügen
				// Hier scheint in der original tabelle lart_id die Artikel ID zu sein,
				// aber in der versions Tabelle die versionsid
				$sql = sprintf("INSERT INTO %s
                              (lart_id, lcat_id, lart_order_id
                                )
                                SELECT %d as lart_id, derived.lcat_id, derived.lart_order_id
                                FROM (SELECT * FROM %s
                                WHERE lart_id=%d) derived

                ", $this->lookup_art_cat, $lan_repore_id, $this->version_lookup_art_cat, $versionid);

				$this->db->query($sql);
			}
			unset($this->checked->restorearticle);
			unset($_POST['restorearticle']);
		}
	}

	/**
	 * @ignore
	 */
	function output_filter()
	{
	}

	/**
	 * @ignore
	 */
	function post_papoo()
	{
	}
}

$restore_plugin = new restoreplugin_class();
