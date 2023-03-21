<?php

/**
 * Class del_artikel
 */
#[AllowDynamicProperties]
class del_artikel
{
	/**
	 * del_artikel constructor.
	 */
	function __construct()
	{
		global $content, $db, $user, $checked, $cms, $diverse, $sitemap, $menu, $intern_menu;
		// Einbindung des globalen Content-Objekts
		$this->content = $content;
		$this->db = $db;
		$this->user = $user;
		$this->checked = $checked;
		$this->cms = $cms;
		$this->diverse = $diverse;
		$this->sitemap = & $sitemap;
		$this->menu = &$menu;
		$this->intern_menu_class = &$intern_menu;

		if ( defined("admin") ) {
			$this->user->check_intern();
			global $template;

			if ( strpos( "XXX" . $template, "del_artikel.html" ) ) {
				//Erst checken
				$this->check_admin_rights();

				if ($this->has_admin_rights) {
					//Einstellungen �berabeiten
					$this->get_artikels();
				}
			}

			if (strpos("XXX" . $template, "del_menu.html")) {
				//Erst checken
				$this->check_admin_rights();

				if ($this->has_admin_rights) {
					//Einstellungen �berabeiten
					$this->get_menu();
				}
			}

			if ( strpos( "XXX" . $template, "del_images.html" ) ) {
				//Erst checken
				$this->check_admin_rights();

				if ($this->has_admin_rights) {
					//Einstellungen �berabeiten
					$this->get_images();
				}
			}

			if ( strpos( "XXX" . $template, "del_files.html" ) ) {
				//Erst checken
				$this->check_admin_rights();

				if ($this->has_admin_rights) {
					//Einstellungen �berabeiten
					$this->get_files();
				}
			}
		}
	}

	/**
	 * del_artikel::get_menu()
	 *
	 * @return void
	 */
	private function get_menu()
	{
		if ($this->has_admin_rights) {
			//Artikel l�schen
			$this->delete_menu();

			$menu_data = $this->menu->menu_data_read("FRONT");
			$this->content->template['menu_del_liste']=$menu_data;

			if (!empty($this->checked->message_del_artikel)) {
				$this->content->template['is_del']=true;
			}
		}
	}

	/**
	 * del_artikel::delete_menu()
	 *
	 * @return void
	 */
	private function delete_menu()
	{
		if (is_array($this->checked->delmenu)) {
			//Alle durchgehen
			if (is_array($this->checked->delmenu)) {
				foreach ($this->checked->delmenu as $key=>$value) {
					//Untermenuzu rausholen
					$untermenuzu=$this->get_untermenuzu($key);
					//L�schen
					$this->intern_menu_class->menu_delete($key,$untermenuzu,1);
				}
				$this->reload("delmen");
			}
		}
	}

	/**
	 * @param $menuid
	 * @return array|null
	 */
	private function get_untermenuzu($menuid)
	{
		$sql=sprintf("SELECT untermenuzu FROM %s 
						WHERE menuid='%d'",
			$this->cms->tbname['papoo_me_nu'],
			$menuid
		);
		$result=$this->db->get_var($sql);
		return $result;
	}
	/**
	 * del_artikel::check_admin_rights()
	 *
	 * @return void
	 */
	private function check_admin_rights()
	{
		$sql=sprintf("SELECT * FROM %s
						WHERE userid='%d'
						AND gruppenid='1'",
			$this->cms->tbname['papoo_lookup_ug'],
			$this->user->userid
		);
		$result=$this->db->get_results($sql,ARRAY_A);
		if (!empty($result)) {
			$this->has_admin_rights=true;
			$this->content->template['has_admin_rights']=true;
		}
	}

	/**
	 * del_artikel::get_artikels()
	 * Alle Artikeldaten rausholen
	 * geht so einfach weil admin Rechte bestehen
	 * @return void
	 */
	private function get_artikels()
	{
		/**
		 * Alle Artikeldaten rausholen
		 * geht so einfach weil admin Rechte bestehen
		 */
		if ($this->has_admin_rights) {
			//Artikel l�schen
			$this->delete_artikel();

			$sql=sprintf("SELECT * FROM %s WHERE lan_repore_id > 0",
				$this->cms->tbname['papoo_language_article']
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$this->content->template['artikel_del_liste']=$result;

			if (!empty($this->checked->message_del_artikel)) {
				$this->content->template['is_del']=true;
			}
		}
	}

	/**
	 * del_artikel::delete_artikel()
	 *
	 * @return void
	 */
	private function delete_artikel()
	{
		if (is_array($this->checked->delartikel)) {
			//Alle durchgehen
			if (is_array($this->checked->delartikel)) {
				foreach ($this->checked->delartikel as $key=>$value) {
					$temp_loesch_id=$key;

					// Daten zu Artikel l�schen
					$sql = sprintf("DELETE FROM %s WHERE reporeID='%d'", $this->cms->tbname['papoo_repore'], $temp_loesch_id);
					$this->db->query($sql);
					$sql = sprintf("DELETE FROM %s WHERE reporeID='%d'", $this->cms->tbname['papoo_version_repore'], $temp_loesch_id);
					$this->db->query($sql);

					// Sprach-Inhalte des Artikels l�schen
					$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d'", $this->cms->tbname['papoo_language_article'], $temp_loesch_id);
					$this->db->query($sql);
					$sql = sprintf("DELETE FROM %s WHERE lan_repore_id='%d'", $this->cms->tbname['papoo_version_language_article'], $temp_loesch_id);
					$this->db->query($sql);

					// Lese-Rechte des Artikel l�schen
					$sql = sprintf("DELETE FROM %s WHERE article_id='%d'", $this->cms->tbname['papoo_lookup_article'], $temp_loesch_id);
					$this->db->query($sql);
					$sql = sprintf("DELETE FROM %s WHERE article_id='%d'", $this->cms->tbname['papoo_version_lookup_article'], $temp_loesch_id);
					$this->db->query($sql);

					// Schreib-Rechte des Artikel l�schen
					$sql = sprintf("DELETE FROM %s WHERE article_wid_id='%d'", $this->cms->tbname['papoo_lookup_write_article'], $temp_loesch_id);
					$this->db->query($sql);
					$sql = sprintf("DELETE FROM %s WHERE article_wid_id='%d'", $this->cms->tbname['papoo_version_lookup_write_article'], $temp_loesch_id);
					$this->db->query($sql);

					// Men�-Zuweisung(en) des Artikel l�schen
					$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'", $this->cms->tbname['papoo_lookup_art_cat'], $temp_loesch_id);
					$this->db->query($sql);
					$sql = sprintf("DELETE FROM %s WHERE lart_id='%d'", $this->cms->tbname['papoo_version_lookup_art_cat'], $temp_loesch_id);
					$this->db->query($sql);

					// ??? 
					$sql = sprintf("DELETE FROM %s WHERE version_art_id='%d'", $this->cms->tbname['papoo_version_article'], $temp_loesch_id);
					$this->db->query($sql);
				}
				$this->reload("del");
			}
		}
	}

	private function get_images()
	{
		if(!empty($this->checked->del_images_now)) {
			$result = $this->del_images();
			if (is_int($result)) {
				$this->content->template['is_del'] = true;
			}
		}

		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_images']
		);
		$this->content->template['image_del_liste'] = $this->db->get_results($sql, ARRAY_A);
	}

	/**
	 * @return bool|int|mixed|mysqli_result|void
	 */
	private function del_images()
	{
		if (!is_array($this->checked->delimage))
			return;
		$sql = array();
		foreach ($this->checked->delimage as $key => $value) {
			if ($value != '1') {
				continue;
			}
			$sql[] = 'image_id = \'' . $key . '\'';
			$file = PAPOO_ABS_PFAD . 'images/' . $this->checked->del_image_name[$key];
			$thumb = PAPOO_ABS_PFAD . 'images/thumbs/' . $this->checked->del_image_name[$key];
			if (file_exists($file)) {
				unlink($file);
			}
			if (file_exists($thumb)) {
				unlink($thumb);
			}
		}
		if (empty($sql)) {
			return;
		}
		$sql = sprintf("DELETE FROM %s
                WHERE %s",
			$this->cms->tbname['papoo_images'],
			implode(' OR ', $sql)
		);
		return $this->db->query($sql);
	}

	private function get_files()
	{
		if(!empty($this->checked->del_files_now)) {
			$result = $this->del_files();
			if (is_int($result)) {
				$this->content->template['is_del'] = true;
			}
		}

		$sql = sprintf("SELECT * FROM %s",
			$this->cms->tbname['papoo_download']
		);
		$result = $this->db->get_results($sql, ARRAY_A);
		foreach ($result as $key => $row) {
			$result[$key]['file_icon'] = $this->get_file_icon($row['downloadlink']);
		}
		$this->content->template['file_del_liste'] = $result;
	}

	/**
	 * @return bool|int|mixed|mysqli_result|void
	 */
	private function del_files()
	{
		if (!is_array($this->checked->delfile)) {
			return;
		}
		$sql1 = array();
		$sql2 = array();
		$sql3 = array();
		foreach ($this->checked->delfile as $key => $value) {
			if ($value != '1') {
				continue;
			}
			$sql1[] = 'downloadid = \'' . $key . '\'';
			$sql2[] = 'dv_downloadid = \'' . $key . '\'';
			$sql3[] = 'download_id_id = \'' . $key . '\'';
			$file = PAPOO_ABS_PFAD .  $this->checked->del_file_name[$key];
			if (file_exists($file)) {
				unlink($file);
			}
		}
		if (empty($sql1))
			return;

		$sql = sprintf("DELETE FROM %s
                WHERE %s",
			$this->cms->tbname['papoo_download'],
			implode(' OR ', $sql1)
		);
		$this->db->query($sql);
		$sql = sprintf("DELETE FROM %s
                WHERE %s",
			$this->cms->tbname['papoo_download_versionen'],
			implode(' OR ', $sql2)
		);
		$this->db->query($sql);
		$sql = sprintf("DELETE FROM %s
                WHERE %s",
			$this->cms->tbname['papoo_lookup_download'],
			implode(' OR ', $sql3)
		);
		return $this->db->query($sql);
	}

	/**
	 * del_artikel_plugin_felder_class::reload()
	 *
	 * @param string $dat
	 * @return void
	 */
	private function reload( $dat = "" )
	{
		$url = "menuid=" . $this->checked->menuid;

		if ($dat=="delmen") {
			$url .= "&template=bulk_del/templates/del_menu.html";
		}
		else {
			$url .= "&template=bulk_del/templates/del_artikel.html";
		}

		if (!empty($dat)) {
			$url .= "&message_del_artikel=" . $dat;
		}

		$self = $_SERVER['PHP_SELF'];

		$location_url = $self . "?" . $url;
		if ( $_SESSION['debug_stopallredirect'] ) {
			echo '<a href="' . $location_url . '">' . $this->content->template['plugin']['mv']['weiter'] . '</a>';
		}
		else {
			header( "Location: $location_url" );
		}
		exit;
	}

	/**
	 * @param $file
	 * @return mixed|string
	 */
	function get_file_icon($file) {
		$types = array(
			"pdf"   =>  "pdf.png",
			"mp3"   =>  "mp3.png",
			"doc"   =>  "word.png",
			"docx"  =>  "word.png",
			"odt"   =>  "openofficeorg-20-writer.png",
			"ods"   =>  "openofficeorg-20-calc.png",
			"odp"   =>  "openofficeorg-20-impress.png",
			"zip"   =>  "zip.png"
		);

		$frag = explode(".", $file);
		$icon = $types[end($frag)];
		if (empty($icon)) {
			$icon="unknown_big.png";
		}
		return $icon;
	}
}

$del_artikel = new del_artikel();
