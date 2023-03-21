<?php
/***************************************************
 * Free URL Generator - obviously generates stuff!
 * @author Christoph Zimmer
 */
#[AllowDynamicProperties]
class PDFNewTab {
	/**
	 * PDFNewTab constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;

		if (defined("admin") && $this->user->check_intern()) {
			//Wenn Formular abgeschickt.
			if(isset($this->checked->plugin_pdf_new_tab_start)) {
				$this->go();

				$this->content->template['plugin_pdf_new_tab_succeeded'] = true;
			}
		}
	}

	private function go()
	{
		$sql=sprintf("SELECT lan_repore_id, lang_id, lan_article FROM %s",
			$this->cms->tbname['papoo_language_article']
		);
		$result = $this->db->get_results($sql,ARRAY_A);

		foreach ($result as $row) {

			$text = $row['lan_article'];
			preg_match_all("/<a[^>]*?href=\"[^\"]*?\.pdf\"[^>]*?>/", $text, $matches);
			if(is_array($matches)) {
				foreach($matches[0] as $match) {
					if(strpos($match, "target=\"") > 0) {
						$anchor = preg_replace("/target=\"[^\"]*?\"/", "target=\"_blank\"", $match);
					}
					else {
						$anchor = substr($match, 0, strlen($match) - 1) . " target=\"_blank\">";
					}
					$text = str_replace($match, $anchor, $text);
				}
			}

			if(strcmp($text, $row['lan_article'])) {
				$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($text), #$this->db->dbh->real_escape_string($text),
					$this->db->escape($text), #$this->db->dbh->real_escape_string($text),
					$row['lan_repore_id'],
					$row['lang_id']
				);
				$this->db->query($sql);
			}
		}
	}
}

$pdf_new_tab = new PDFNewTab();
