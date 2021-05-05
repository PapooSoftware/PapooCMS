<?php
/**
 * #####################################
 * # CMS Papoo                         #
 * # (c) Dr. Carsten Euwens 2008       #
 * # Authors: Carsten Euwens           #
 * # http://www.papoo.de               #
 * # Internet                          #
 * #####################################
 * # PHP Version >4.3                  #
 * #####################################
 */

/**
 * Diese Klasse macht Bookmarks des Admin Bereiches.
 */
class intern_book
{
	/**
	 * intern_book constructor.
	 */
	function __construct()
	{
		/**
		 * Klassen globalisieren
		 */

		// einbinden des Objekts der Datenbak Abstraktionsklasse ez_sql
		global $db;
		global $cms;
		// Messages einbinden
		global $user;
		// weitere Seiten Klasse einbinden
		global $content;
		// Suchklasse einbinden
		global $checked;
		// Interne Menüklasse einbinden
		global $intern_menu;
		// Verschlüsselung etc. einbinden
		global $diverse;
		global $menu;

		/**
		 * und einbinden in die Klasse
		 */

		// Hier die Klassen als Referenzen
		$this->cms = &$cms;
		$this->db = &$db;
		$this->content = &$content;

		$this->user = &$user;

		$this->checked = &$checked;
		$this->intern_menu = &$intern_menu;

		$this->diverse = &$diverse;
		$this->menu=&$menu;
		//ausführen
		$this->make_book();
	}

	/**
	 *
	 */
	function make_book()
	{
		// Überprüfen ob Zugriff auf die Inhalte besteht
		# $this->user->check_access();
		$this->db->csrfok=true;
		$sql = sprintf("SELECT menulink FROM %s WHERE menuid='%s'",
			$this->cms->tbname['papoo_menuint'],
			$this->db->escape($this->checked->menuid)
		);

		if (is_array($this->menu->menu_back)) {
			foreach ($this->menu->menu_back as $key=>$value) {
				if ($this->checked->menuid==$value['menuid']) {
					$templa=$value['menulink']."?menuid=".$value['menuid']."&amp;template=".$value['template'];
				}
			}
		}
		IfNotSetNull($templa);
		$this->content->template['bookmarks_template']=$templa;
		$this->content->template['bookmarks_menuid']=$this->checked->menuid;

		if (!empty($this->checked->bookmarkit)) {
			$sql = sprintf("SELECT menuname FROM %s WHERE menuid_id='%s' AND lang_id='%s'",
				$this->cms->tbname['papoo_men_uint_language'],
				$this->db->escape($this->checked->menuid),
				$this->cms->lang_back_id
			);
			$name=$this->db->get_var($sql);

			//Checken ob schon existiert
			$sql = sprintf("SELECT * FROM %s WHERE book_menuid='%s' AND book_user='%s'",
				$this->cms->tbname['papoo_bookmarks'],
				$this->db->escape($this->checked->menuid),
				$this->user->userid
			);
			$result=$this->db->get_results($sql);

			if (empty($result)) {
				$sql=sprintf("INSERT INTO %s SET
							book_user='%s', 
							book_menuid='%s', 
							book_link='%s',
							book_name='%s'",
					$this->cms->tbname['papoo_bookmarks'],
					$this->user->userid,
					$this->db->escape($this->checked->menuid),
					"./".$this->db->escape($templa),
					$name
				);
				$this->db->query($sql);
			}
		}
		if (!empty($this->checked->bookmarkdel)) {
			$sql=sprintf("DELETE FROM %s WHERE book_menuid='%s' AND book_user='%s' LIMIT 1",
				$this->cms->tbname['papoo_bookmarks'],
				$this->db->escape($this->checked->menuid),
				$this->user->userid
			);
			$this->db->query($sql);
		}

		IfNotSetNull($_SESSION['dbp']['papoo_book']);
		//DIe Daetn des Users rausholen
		if (!is_array($_SESSION['dbp']['papoo_book'])) {
			$sql = sprintf("SELECT * FROM %s WHERE book_user='%d' ORDER BY book_name",
				$this->cms->tbname['papoo_bookmarks'],
				$this->user->userid
			);
			$result=$this->db->get_results($sql,ARRAY_A);
			$_SESSION['dbp']['papoo_book']=$result;
		}
		else {
			$result=$_SESSION['dbp']['papoo_book'];
		}

		if (!empty($result)) {
			foreach ($result as $dat) {
				if ($dat['book_menuid']==$this->checked->menuid) {
					$this->content->template['nod_link']="ok";
				}
			}
		}
		$this->content->template['bookmarks']=$result;
		$this->db->csrfok=false;
	}
}

$intern_book = new intern_book();
