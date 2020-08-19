<?php
/*********************************************
 * Break Annihilator - entfernt alle br-Tags!
 * @author Christoph Zimmer
 */
class BreakAnnihilator {
	/**
	 * BreakAnnihilator constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $cms;
		$this->content = & $content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;

		if (defined("admin")) {
			$this->user->check_intern();

			//Wenn Formular abgeschickt.
			if(isset($this->checked->plugin_break_annihilator_br_zerstoeren)) {
				$this->annihilate();
				$this->content->template['plugin_break_annihilator_succeeded'] = true;
			}
		}
	}

	private function annihilate()
	{
		$sql=sprintf("SELECT lan_repore_id, lang_id, lan_article FROM %s",
			$this->cms->tbname['papoo_language_article']
		);
		$articles = $this->db->get_results($sql,ARRAY_A);

		//annihilation pattern
		$pat = "/<br[^>]*?>/";
		//empty tag destruction pattern
		$pat2 = "/<([^>\s]*)[^>]*>[^a-zA-Z0-9]*?<\/\\1>/";
		foreach ($articles as $row) {

			$newString = preg_replace($pat, "", $row['lan_article']);

			do {
				$length = strlen($newString);
				$newString = preg_replace($pat2, "", $newString);
			} while(strlen($newString) < $length);

			if(strlen($newString) < strlen($row['lan_article']) || empty($row['url_header'])) {
				$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
					$this->cms->tbname['papoo_language_article'],
					$this->db->escape($newString),
					$this->db->escape($newString),
					$row['lan_repore_id'],
					$row['lang_id']
				);
				$this->db->query($sql);
			}
		}
	}
}

$break_annihilator = new BreakAnnihilator();
