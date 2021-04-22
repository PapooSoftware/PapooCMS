<?php
/*************************
 * Bilder-Ablaufcheck
 * @author Christoph Zimmer
 * @copyright 2014-08-18
 * Bilderklasse muss das Format "ablauf-tt-mm-jj" haben.
 */
class check_img_ablauf
{
	/**
	 * check_img_ablauf constructor.
	 */
	function __construct()
	{
		global $checked;
		$this->checked = &$checked;
		global $output;
		$this->output = &$output;
		global $cms;
		$this->cms = &$cms;
		global $db;
		$this->db = &$db;
	}

	function output_filter()
	{
		//fetch article
		$sql = sprintf("SELECT * FROM %s WHERE lang_id='%d' AND lan_repore_id='%d' LIMIT 1",
			$this->cms->papoo_language_article,
			$this->db->escape($this->cms->lang_id),
			$this->checked->real_reporeid
		);
		$select = $this->db->get_results($sql);

		//alle Eintr�ge durchgehen
		if (!empty ($select)) {
			foreach ($select as $row) {
				$this->article = $row;
			}
		}
		else {
			$this->article = null;
		}

		$this->remove_expired_images($this->output);
	}

	/**
	 * @param $haystack
	 */
	function remove_expired_images(&$haystack)
	{
		$imagesRemoved = false;
		//extract all and remove expired  images
		preg_match_all("/(<img.*?\/>)/", $haystack, $images);
		//loop all images
		foreach($images[0] as $image) {
			//preg_match("(class=\"ablauf-.*?\")", $image, $result);
			//extract date
			preg_match("/class=\"ablauf-(\d\d-\d\d-\d\d)\"/", $image, $result);
			$format = "d-m-y H:i:s";
			//fallback for different date format
			if(sizeof($result) == 0) {
				preg_match("/class=\"ablauf-(\d\d-\d\d-\d\d\d\d)\"/", $image, $result);
				$format = "d-m-Y H:i:s";
			}
			//continue;
			//if date exists
			if(sizeof($result) == 2) {
				//get last timestamp of that day
				$time = DateTime::createFromFormat($format, $result[1]." 23:59:59");
				$timestamp = $time->getTimestamp();
				//if timestamp lower than current remove image from output
				if($timestamp < time()) {
					//escape slash and period -> regex-sensitive characters
					$imageRX = str_replace(".","\.",str_replace("/","\/",$image));
					//remember string length to see if image has been removed
					$len = strlen($haystack);
					//remove image from output
					$haystack = preg_replace('/'.$imageRX.'/', "", $haystack);
					//if image is removed
					if(strlen($haystack) < $len)
					{
						$imagesRemoved = true;
					}
				}
			}
		}

		//if processing whole output and images have been removed;
		//see if you can remove them from the database
		if($haystack == $this->output && $imagesRemoved && $this->article != null) {
			$this->remove_from_database($this->article->lan_article);
			$this->remove_from_database($this->article->lan_article_sans);
			$this->remove_from_database($this->article->lan_teaser);
		}
	}

	/**
	 * @param $content
	 */
	function remove_from_database(&$content)
	{
		if($content == $this->article->lan_article) {
			$column = "lan_article";
		}
		else if($content == $this->article->lan_article_sans) {
			$column = "lan_article_sans";
		}
		else if($content == $this->article->lan_teaser) {
			$column = "lan_teaser";
		}
		else {
			$column = "";
		}
		//remember string length to see if image has been removed
		$len = strlen($content);
		//recursive call; get cleaned lan_article string
		$this->remove_expired_images($content);
		//if images have been removed
		if(strlen($content) < $len) {
			$sql = sprintf("UPDATE %s SET %s = '%s' WHERE lang_id='%d' AND lan_repore_id='%d';",
				$this->cms->papoo_language_article,
				$column,
				$content,
				$this->db->escape($this->cms->lang_id),
				$this->checked->real_reporeid
			);
			//remove images from database
			$this->db->query($sql);
		}
	}
}

$check_img_ablauf = new check_img_ablauf();
