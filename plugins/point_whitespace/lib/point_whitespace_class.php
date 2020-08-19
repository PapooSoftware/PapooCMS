<?php
/***********************************************************
 * Point Whitespace - adds whitespace after points without.
 * @author Christoph Zimmer
 * @date 12.11.2014
 */
class PointWhitespace {
	/**
	 * PointWhitespace constructor.
	 */
	function __construct()
	{
		global $content, $db, $checked, $user, $cms;
		$this->content = &$content;
		$this->db = &$db;
		$this->checked = &$checked;
		$this->user = &$user;
		$this->cms = &$cms;

		if ( defined("admin") && $this->user->check_intern() ) {
			//Wenn Formular abgeschickt.
			if(isset($this->checked->plugin_point_whitespace_start)) {
				//fetch articles' language entries
				$sql=sprintf("SELECT * FROM %s;",
					$this->cms->tbname['papoo_language_article']
				);
				$result=$this->db->get_results($sql,ARRAY_A);

				//process each article
				foreach ($result as $value) {
					//add whitespace
					$text = preg_replace("/(?!>[^<]*)([.,;:!?]+)([^ ]|$|)(?=[^>]*<)/", "$1 $2", $value['lan_article']);
					//trim multiple whitespace
					$text = preg_replace("/[\s]{2,}/", " ", $text);
					//remove whitespace where it does not belong
					$search  = array(" .", " ,", " ;", " :", " !", " ?");
					$replace = array(".", ",", ";", ":", "!", "?");
					$text = str_replace($search, $replace, $text);

					//remove whitespace from abbreviations
					$search  = array("z. B.", "e. g.", "d. h.");
					$replace = array("z.B.", "e.g.", "d.h.");
					$text = str_replace($search, $replace, $text);

					//trim multiple <br> tags
					$text = preg_replace("/(?:<br[^>]*>[\s]*){2,}/", "<br>", $text);
					//remove <br> tags placed immediately after any opening and closing tag except for <br> tags
					$text = preg_replace("/(<(?!br)[^>]*>)[\s]*(?:<br[^>]*>[\s]*)+/", "$1", $text);

					//if something has changed
					if(strcmp($text, $value['lan_article']) != 0) {
						//write modified article back to the database
						$sql = sprintf("UPDATE %s SET lan_article='%s', lan_article_sans='%s' WHERE lan_repore_id=%d AND lang_id=%d",
							$this->cms->tbname['papoo_language_article'],
							$this->db->escape($text),
							$this->db->escape($text),
							$value['lan_repore_id'],
							$value['lang_id']
						);
						$this->db->query($sql);
					}
				}
				$this->content->template['plugin_point_whitespace_succeeded'] = true;
			}
		}
	}
}

$point_whitespace = new PointWhitespace();
